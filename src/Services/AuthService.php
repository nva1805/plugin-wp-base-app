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

  public function redirectIfGuest(string $url = '', string $destination = ''): void
  {
    if (!$this->isAuthenticated()) {
      $redirectUrl = $url ?: home_url('/login');
      if ($destination) {
        $redirectUrl = add_query_arg('destination', urlencode($destination), $redirectUrl);
      }
      wp_safe_redirect($redirectUrl);
      exit;
    }
  }
}
