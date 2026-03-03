<?php
namespace WPBaseApp\Controllers\Auth;

use WPBaseApp\Controllers\BaseController;

class LoginController extends BaseController
{
  public function __construct(string $template)
  {
    parent::__construct($template);
  }

  public function handle(): void
  {
    if ($this->request->isMethod('POST')) {
      $this->handleLogin();
    }

    $this->render($this->getInitialData());
  }

  private function getInitialData(): array
  {
    return [
      'site_name' => get_bloginfo('name'),
      'destination' => $_GET['destination'] ?? home_url(),
      'login_nonce' => wp_create_nonce('wp-base-app-login'),
      'error' => $this->getData('error'),
      'register_url' => site_url('/register'),
      'lost_password_url' => wp_lostpassword_url(),
    ];
  }

  private function handleLogin(): void
  {
    if (!$this->request->has('login_nonce') || !wp_verify_nonce($this->request->input('login_nonce'), 'wp-base-app-login')) {
      $this->setData('error', 'Invalid security token');
      return;
    }

    $username = sanitize_text_field($this->request->input('username', ''));
    $password = $this->request->input('password', '');
    $remember = $this->request->has('remember');

    // Validate input
    if (empty($username) || empty($password)) {
      $this->setData('error', 'Please enter both username and password');
      return;
    }

    $credentials = [
      'user_login' => $username,
      'user_password' => $password,
      'remember' => $remember,
    ];

    $user = wp_signon($credentials, false);

    if (is_wp_error($user)) {
      $this->setData('error', $user->get_error_message());
      return;
    }

    // Login thành công -> redirect
    $destination = $this->request->input('destination', home_url());
    $this->redirect($destination);
  }
}