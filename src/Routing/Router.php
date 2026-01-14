<?php
namespace WPBaseApp\Routing;

class Router
{
  private array $routes = [];

  public function __construct(array $routes = [])
  {
    $this->routes = $routes;
  }

  /**
   * Register rewrite rules for all routes
   */
  public function registerRewriteRules(): void
  {
    foreach ($this->routes as $route) {
      add_rewrite_rule(
        '^' . $route['slug'] . '/?$',
        'index.php?wp_base_app_page=' . $route['slug'],
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
   * Resolve the current route
   */
  public function resolve(): ?array
  {
    $slug = get_query_var('wp_base_app_page');

    if (empty($slug) || !isset($this->routes[$slug])) {
      return null;
    }

    return $this->routes[$slug];
  }

  /**
   * Dispatch the route to controller
   */
  public function dispatch(): mixed
  {
    $route = $this->resolve();

    if (!$route) {
      return null;
    }

    $controller = $this->createController($route);
    $controller->index();

    return $controller->render();
  }

  /**
   * Create controller instance
   */
  private function createController(array $route): object
  {
    $controllerClass = $route['controller'];

    if (!class_exists($controllerClass)) {
      wp_die("Controller class {$controllerClass} not found.");
    }

    return new $controllerClass($route['template']);
  }

  /**
   * Get all routes
   */
  public function getRoutes(): array
  {
    return $this->routes;
  }

  /**
   * Get route by slug
   */
  public function getRoute(string $slug): ?array
  {
    return $this->routes[$slug] ?? null;
  }
}
