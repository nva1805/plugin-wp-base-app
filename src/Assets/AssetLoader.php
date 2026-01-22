<?php

namespace WPBaseApp\Assets;

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
   * Format: 'filename.js' => ['dependency1', 'dependency2']
   */
  private array $scriptDependencies = [
    'register.js' => ['jquery'],
    'login.js' => ['jquery'],
  ];

  /**
   * Style dependencies configuration
   * Format: 'filename.css' => ['dependency1', 'dependency2']
   */
  private array $styleDependencies = [];

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
    $this->enqueueAssets(self::TYPE_STYLE);
    $this->enqueueAssets(self::TYPE_SCRIPT);
  }

  /**
   * Enqueue assets by type
   */
  private function enqueueAssets(string $type): void
  {
    $files = $this->getFiles($type);

    foreach ($files as $filename) {
      $url = $this->buildAssetUrl($type, $filename);
      $dependencies = $this->getDependencies($type, $filename);
      if ($type === self::TYPE_STYLE) {
        wp_enqueue_style($$this->prefix . '-' . $filename, $url, $dependencies, WP_BASE_APP_VERSION);
      } else {
        wp_enqueue_script($$this->prefix . '-' . $filename, $url, $dependencies, WP_BASE_APP_VERSION, true);
      }
    }
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
}
