<?php

namespace WPBaseApp\Services;

use WPBaseApp\Admin\AdminConfig;

class AppPageManager
{
  public static function getVirtualPages(): array
  {
    return require WP_BASE_APP_PATH . 'config/virtual-pages.php';
  }

  public static function getEnabledPages(): array
  {
    $option = get_option('wpba_enabled_pages', null);
    if ($option === null) {
      return ['login', 'register']; // default enabled pages
    }
    return (array) $option;
  }

  public static function registerRewriteRules(): void
  {
    $pages = self::getVirtualPages();

    foreach (self::getEnabledPages() as $key) {
      if (isset($pages[$key])) {
        add_rewrite_rule(
          '^' . $pages[$key]['slug'] . '/?$',
          'index.php?wp_base_app_page=' . $pages[$key]['slug'],
          'top'
        );
      }
    }
  }

  public static function createEnabledPages(): void
  {
    self::registerRewriteRules();
    flush_rewrite_rules();
  }
}
