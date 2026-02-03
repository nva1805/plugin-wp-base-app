<?php
namespace WPBaseApp;

use WPBaseApp\Assets\AssetLoader;

class Plugin
{
  private array $virtualPages = [];
  private AssetLoader $assetLoader;

  public function __construct()
  {
    $this->virtualPages = require WP_BASE_APP_PATH . 'config/virtual-pages.php';
    $this->assetLoader = new AssetLoader(WP_BASE_APP_PATH, WP_BASE_APP_URL);

    add_filter('query_vars', fn($vars) => array_merge($vars, ['wp_base_app_page']));
    add_filter('template_include', [$this, 'dispatch']);
    add_action('wp_enqueue_scripts', [$this->assetLoader, 'register']);
  }

  /**
   * Dispatch the route to controller
   */
  public function dispatch($template)
  {
    $slug = get_query_var('wp_base_app_page');

    if (empty($slug) || !isset($this->virtualPages[$slug])) {
      return $template;
    }

    $page = $this->virtualPages[$slug];
    $controllerClass = $page['controller'];

    if (!class_exists($controllerClass)) {
      wp_die("Controller class {$controllerClass} not found.");
    }

    $controller = new $controllerClass($page['template']);
    $controller->index();

    echo $controller->render();
    exit;
  }
}