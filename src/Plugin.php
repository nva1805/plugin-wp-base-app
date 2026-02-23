<?php
namespace WPBaseApp;

use WPBaseApp\Assets\AssetLoader;
use WPBaseApp\Services\AppPageManager;

class Plugin
{
  private array $virtualPages = [];
  private AssetLoader $assetLoader;

  public function __construct()
  {
    $allPages = AppPageManager::getVirtualPages();
    $enabledKeys = AppPageManager::getEnabledPages();

    // Only keep enabled virtual pages
    $this->virtualPages = array_filter(
      $allPages,
      fn($key) => in_array($key, $enabledKeys),
      ARRAY_FILTER_USE_KEY
    );

    $this->assetLoader = new AssetLoader(WP_BASE_APP_PATH, WP_BASE_APP_URL);

    add_action('init', [AppPageManager::class, 'registerRewriteRules']);
    add_action('init', [$this, 'loadTextdomain']);
    add_filter('query_vars', fn($vars) => array_merge($vars, ['wp_base_app_page']));
    add_filter('template_include', [$this, 'dispatch']);
    add_action('wp_enqueue_scripts', [$this->assetLoader, 'register']);
    add_filter('show_admin_bar', fn($show) => current_user_can('administrator') ? $show : false);
  }

  public function loadTextdomain(): void
  {
    load_plugin_textdomain('wp-base-app', false, 'plugin-wp-base-app/languages');
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
    $controller->handle();
  }
}