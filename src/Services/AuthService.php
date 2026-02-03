<?php
namespace WPBaseApp\Services;

class AuthService
{
  public static function isAuthenticated(): bool
  {
    return (bool) get_current_user_id();
  }

  public static function getUserID(): int
  {
    return get_current_user_id();
  }

  public static function getUser(): ?\WP_User
  {
    $user = wp_get_current_user();
    return $user->ID ? $user : null;
  }

  public static function redirectIfAuthenticated(string $url = ''): void
  {
    if (self::isAuthenticated()) {
      wp_safe_redirect($url ?: home_url());
      exit;
    }
  }

  public static function redirectIfGuest(string $url = ''): void
  {
    if (self::isAuthenticated()) {
      wp_safe_redirect($url ?: home_url('/login'));
      exit;
    }
  }
}
