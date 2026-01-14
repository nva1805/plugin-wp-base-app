<?php
namespace WPBaseApp\Assets;

class AssetLoader
{
  private string $basePath;
  private string $baseUrl;
  private string $prefix;

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
    $this->enqueueStyles();
    $this->enqueueScripts();
  }

  /**
   * Enqueue all CSS files
   */
  private function enqueueStyles(): void
  {
    $files = $this->getFiles('css', '*.css');

    foreach ($files as $file) {
      $handle = $this->generateHandle($file, 'css');
      
      wp_enqueue_style(
        $handle,
        $this->getFileUrl('css', $file),
        $this->getStyleDependencies($file),
        $this->getFileVersion($file)
      );
    }
  }

  /**
   * Enqueue all JS files
   */
  private function enqueueScripts(): void
  {
    $files = $this->getFiles('js', '*.js');

    foreach ($files as $file) {
      $handle = $this->generateHandle($file, 'js');
      
      wp_enqueue_script(
        $handle,
        $this->getFileUrl('js', $file),
        $this->getScriptDependencies($file),
        $this->getFileVersion($file),
        true
      );
    }
  }

  /**
   * Get files from directory
   */
  private function getFiles(string $directory, string $pattern): array
  {
    $path = "{$this->basePath}/assets/{$directory}/{$pattern}";
    $files = glob($path) ?: [];

    return array_map('basename', $files);
  }

  /**
   * Generate handle for asset
   */
  private function generateHandle(string $filename, string $type): string
  {
    $name = pathinfo($filename, PATHINFO_FILENAME);
    return "{$this->prefix}-{$name}";
  }

  /**
   * Get file URL
   */
  private function getFileUrl(string $directory, string $filename): string
  {
    return "{$this->baseUrl}/assets/{$directory}/{$filename}";
  }

  /**
   * Get file version based on modification time
   */
  private function getFileVersion(string $filename): string
  {
    $directory = pathinfo($filename, PATHINFO_EXTENSION) === 'css' ? 'css' : 'js';
    $filePath = "{$this->basePath}/assets/{$directory}/{$filename}";

    return file_exists($filePath) ? (string) filemtime($filePath) : '1.0.0';
  }

  /**
   * Get style dependencies
   */
  private function getStyleDependencies(string $filename): array
  {
    // Add dependencies based on filename if needed
    return [];
  }

  /**
   * Get script dependencies
   */
  private function getScriptDependencies(string $filename): array
  {
    $dependencies = [];

    // Add jQuery dependency for specific files
    $jqueryFiles = ['register.js', 'login.js'];
    
    if (in_array($filename, $jqueryFiles)) {
      $dependencies[] = 'jquery';
    }

    return $dependencies;
  }
}
