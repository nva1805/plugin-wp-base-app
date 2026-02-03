<?php

namespace WPBaseApp;

/**
 * Deactivator: Fired during plugin deactivation
 */
class Deactivator
{
  /**
   * Deactivate the plugin
   */
  public static function deactivate(): void
  {
    flush_rewrite_rules();
  }
}
