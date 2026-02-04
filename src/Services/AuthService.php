<?php
namespace WPBaseApp\Services;

class AuthService
{
  public function isAuthenticated(): bool
  {
    return (bool) get_current_user_id();
  }

  public function getUserID(): int
  {
    return get_current_user_id();
  }

  public function getUser(): ?\WP_User
  {
    $user = wp_get_current_user();
    return $user->ID ? $user : null;
  }

  public function redirectIfAuthenticated(string $url = ''): void
  {
    if ($this->isAuthenticated()) {
      wp_safe_redirect($url ?: home_url());
      exit;
    }
  }

  public function redirectIfGuest(string $url = ''): void
  {
    if ($this->isAuthenticated()) {
      wp_safe_redirect($url ?: home_url('/login'));
      exit;
    }
  }
}
