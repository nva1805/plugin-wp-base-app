<?php
namespace WPBaseApp;

use WPBaseApp\Routing\Router;
use WPBaseApp\Assets\AssetLoader;

class Plugin
{
  private Router $router;
  private AssetLoader $assetLoader;

  public function __construct()
  {
    $this->initDependencies();
    $this->registerHooks();
  }

  /**
   * Initialize dependencies
   */
  private function initDependencies(): void
  {
    $routes = require WP_BASE_APP_PATH . 'config/router.php';

    $this->router = new Router($routes);
    $this->assetLoader = new AssetLoader(WP_BASE_APP_PATH, WP_BASE_APP_URL);
  }

  /**
   * Register WordPress hooks
   */
  private function registerHooks(): void
  {
    add_action('init', [$this->router, 'registerRewriteRules']);
    add_filter('query_vars', [$this->router, 'registerQueryVars']);
    add_filter('template_include', [$this->router, 'dispatch']);
    add_action('wp_enqueue_scripts', [$this->assetLoader, 'register']);
  }
}