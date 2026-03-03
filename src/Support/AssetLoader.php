<?php

namespace WPBaseApp\Support;

/**
 * Asset Loader - Handles registration and enqueueing of plugin assets
 */
class AssetLoader
{
  private const TYPE_STYLE = 'css';
  private const TYPE_SCRIPT = 'js';

  private string $basePath;
  private string $baseUrl;
  private string $prefix;

  /**
   * Script dependencies configuration
   * Loading order: form-validator.js → login.js/register.js → alpinejs
   * Base validator loads first, then specific forms, then Alpine initializes
   */
  private array $scriptDependencies = [
    'form-validator.js' => [],
    'login.js' => ['wp-base-app-form-validator.js'],
    'register.js' => ['wp-base-app-form-validator.js'],
  ];

  /**
   * Style dependencies configuration
   * Format: 'filename.css' => ['dependency1', 'dependency2']
   */
  private array $styleDependencies = [
    'components.css' => ['wp-base-app-base.css'],
    'login.css' => ['wp-base-app-base.css', 'wp-base-app-components.css'],
    'register.css' => ['wp-base-app-base.css', 'wp-base-app-components.css'],
    'profile.css' => ['wp-base-app-base.css', 'wp-base-app-components.css'],
  ];

  /**
   * Page-specific assets: only load when on matching page
   * Assets NOT listed here are treated as global and always loaded
   */
  private array $pageAssets = [
    'login.css' => 'login',
    'login.js' => 'login',
    'register.css' => 'register',
    'register.js' => 'register',
    'profile.css' => 'profile',
  ];

  public function __construct(string $basePath, string $baseUrl, string $prefix = 'wp-base-app')
  {
    $this->basePath = rtrim($basePath, '/');
    $this->baseUrl = rtrim($baseUrl, '/');
    $this->prefix = $prefix;
  }

  /**
   * Register all assets
   */
  public function register(): void
  {
    $this->registerExternalLibraries();
    $this->enqueueAssets(self::TYPE_STYLE);
    $this->enqueueAssets(self::TYPE_SCRIPT);
    $this->localizeScripts();
  }

  /**
   * Enqueue assets by type
   */
  private function enqueueAssets(string $type): void
  {
    $files = $this->getFiles($type);

    foreach ($files as $filename) {
      if (!$this->shouldEnqueue($filename)) {
        continue;
      }

      $url = $this->buildAssetUrl($type, $filename);
      $dependencies = $this->getDependencies($type, $filename);
      if ($type === self::TYPE_STYLE) {
        wp_enqueue_style($this->prefix . '-' . $filename, $url, $dependencies, WP_BASE_APP_VERSION);
      } else {
        wp_enqueue_script($this->prefix . '-' . $filename, $url, $dependencies, WP_BASE_APP_VERSION, true);
      }
    }
  }

  /**
   * Determine if an asset should be enqueued on the current page
   */
  private function shouldEnqueue(string $filename): bool
  {
    $slug = get_query_var('wp_base_app_page');

    // Global assets always load
    if (!isset($this->pageAssets[$filename])) {
      return true;
    }

    return $slug === $this->pageAssets[$filename];
  }

  /**
   * Get all files from asset directory
   */
  private function getFiles(string $type): array
  {
    $pattern = $this->buildAssetPath($type, "*.{$type}");
    $files = glob($pattern) ?: [];
    return array_map('basename', $files);
  }

  /**
   * Build asset directory path
   */
  private function buildAssetPath(string $type, string $filename): string
  {
    return "{$this->basePath}/assets/{$type}/{$filename}";
  }

  /**
   * Build asset URL
   */
  private function buildAssetUrl(string $type, string $filename): string
  {
    return "{$this->baseUrl}/assets/{$type}/{$filename}";
  }

  /**
   * Get dependencies for an asset
   */
  private function getDependencies(string $type, string $filename): array
  {
    $config = $type === self::TYPE_STYLE
      ? $this->styleDependencies
      : $this->scriptDependencies;

    return $config[$filename] ?? [];
  }

  /**
   * Register and enqueue external libraries from CDN
   * Alpine loads LAST after all component scripts
   */
  private function registerExternalLibraries(): void
  {
    wp_enqueue_script(
      'alpinejs',
      'https://cdn.jsdelivr.net/npm/alpinejs@3.15.8/dist/cdn.min.js',
      [],
      '3.15.8',
      ['strategy' => 'defer']
    );
  }

  /**
   * Pass PHP config to JS
   */
  private function localizeScripts(): void
  {
    wp_localize_script($this->prefix . '-script', 'wpBaseApp', [
      'siteUrl' => site_url(),
      'ajaxUrl' => admin_url('admin-ajax.php'),
      'nonce' => wp_create_nonce('wp_rest'),
      'isLoggedIn' => is_user_logged_in(),
    ]);
  }
}
