<?php
namespace WPBaseApp;

use WPBaseApp\Assets\AssetLoader;

class Plugin
{
  private array $virtualPages = [];
  private AssetLoader $assetLoader;

  public function __construct()
  {
    // Load configurations
    $this->virtualPages = require WP_BASE_APP_PATH . 'config/virtual-pages.php';
    $this->assetLoader = new AssetLoader(WP_BASE_APP_PATH, WP_BASE_APP_URL);

    // Register hooks
    add_action('init', [$this, 'registerRewriteRules']);
    add_filter('query_vars', [$this, 'registerQueryVars']);
    add_filter('template_include', [$this, 'dispatch']);
    add_action('wp_enqueue_scripts', [$this->assetLoader, 'register']);
  }

  /**
   * Register rewrite rules for all virtual pages
   */
  public function registerRewriteRules(): void
  {
    foreach ($this->virtualPages as $page) {
      add_rewrite_rule(
        '^' . $page['slug'] . '/?$',
        'index.php?wp_base_app_page=' . $page['slug'],
        'top'
      );
    }
  }

  /**
   * Register query vars
   */
  public function registerQueryVars(array $vars): array
  {
    $vars[] = 'wp_base_app_page';
    return $vars;
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