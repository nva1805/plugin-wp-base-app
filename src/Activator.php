<?php

namespace WPBaseApp;

/**
 * Activator: Fired during plugin activation
 */
class Activator
{
  /**
   * Activate the plugin
   */
  public static function activate(): void
  {
    self::registerRewriteRules();
    flush_rewrite_rules();
  }

  /**
   * Register rewrite rules for virtual pages
   */
  private static function registerRewriteRules(): void
  {
    $virtualPages = require WP_BASE_APP_PATH . 'config/virtual-pages.php';

    foreach ($virtualPages as $page) {
      add_rewrite_rule(
        '^' . $page['slug'] . '/?$',
        'index.php?wp_base_app_page=' . $page['slug'],
        'top'
      );
    }
  }
}
