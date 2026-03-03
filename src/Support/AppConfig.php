<?php

namespace WPBaseApp\Support;

/**
 * Centralized option access — tránh rải get_option('wpba_...') khắp nơi.
 */
class AppConfig
{
  private const PREFIX = 'wpba_';

  public static function get(string $key, mixed $default = null): mixed
  {
    $value = get_option(self::PREFIX . $key);
    return $value !== false ? $value : $default;
  }

  public static function set(string $key, mixed $value): bool
  {
    return update_option(self::PREFIX . $key, $value);
  }
}
