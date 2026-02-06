<?php
namespace WPBaseApp\Controllers\Auth;

use WPBaseApp\Controllers\BaseController;
use WPBaseApp\Services\AuthService;

class RegisterController extends BaseController
{
  public function __construct(string $template)
  {
    parent::__construct($template);
  }

  public function handle(): void
  {
    $authService = new AuthService();
    $authService->redirectIfAuthenticated();

    if ($this->request->isMethod('POST')) {
      $this->handleRegistration();
    }
    $this->render($this->getInitialData());
  }

  private function getInitialData(): array
  {
    return [
      'site_name' => get_bloginfo('name'),
      'register_nonce' => wp_create_nonce('wp-base-app-register'),
      'error' => $this->getData('error'),
      'errors' => $this->getData('errors', []),
      'success' => $this->getData('success'),
      'login_url' => site_url('/login'),
      'username' => $this->getData('username', ''),
      'email' => $this->getData('email', ''),
    ];
  }

  private function handleRegistration(): void
  {
    if (!$this->verifyNonce('register_nonce', 'wp-base-app-register')) {
      $this->setData('error', 'Invalid security token');
      return;
    }

    $username = sanitize_text_field($this->request->input('username', ''));
    $email = sanitize_email($this->request->input('email', ''));
    $password = $this->request->input('password', '');
    $password_confirm = $this->request->input('password_confirm', '');

    $this->mergeData([
      'username' => $username,
      'email' => $email,
    ]);
    $errors = $this->validate($username, $email, $password, $password_confirm);

    if (!empty($errors)) {
      $this->mergeData([
        'error' => $errors[0],
        'errors' => $errors,
      ]);
      return;
    }

    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
      $this->setData('error', $user_id->get_error_message());
      return;
    }

    $this->redirect(site_url('/login'));
  }

  private function validate(string $username, string $email, string $password, string $password_confirm): array
  {
    $errors = [];

    $rules = $this->getValidationRules($username, $email, $password, $password_confirm);

    foreach ($rules as $rule) {
      if ($rule['condition']) {
        $errors[] = $rule['message'];
      }
    }

    return $errors;
  }

  private function getValidationRules(string $username, string $email, string $password, string $password_confirm): array
  {
    return [
      // Username rules
      [
        'condition' => empty($username),
        'message' => 'Username is required',
      ],
      [
        'condition' => !empty($username) && strlen($username) < 3,
        'message' => 'Username must be at least 3 characters',
      ],
      [
        'condition' => !empty($username) && !preg_match('/^[a-zA-Z0-9_]+$/', $username),
        'message' => 'Username can only contain letters, numbers and underscores',
      ],
      [
        'condition' => !empty($username) && username_exists($username),
        'message' => 'Username already exists',
      ],

      // Email rules
      [
        'condition' => empty($email),
        'message' => 'Email is required',
      ],
      [
        'condition' => !empty($email) && !is_email($email),
        'message' => 'Invalid email address',
      ],
      [
        'condition' => !empty($email) && is_email($email) && email_exists($email),
        'message' => 'Email already exists',
      ],

      // Password rules
      [
        'condition' => empty($password),
        'message' => 'Password is required',
      ],
      [
        'condition' => !empty($password) && strlen($password) < 8,
        'message' => 'Password must be at least 8 characters',
      ],
      [
        'condition' => !empty($password) && (!preg_match('/[a-zA-Z]/', $password) || !preg_match('/\d/', $password)),
        'message' => 'Password must contain letters and numbers',
      ],

      // Password confirmation rules
      [
        'condition' => empty($password_confirm),
        'message' => 'Please confirm your password',
      ],
      [
        'condition' => !empty($password_confirm) && $password !== $password_confirm,
        'message' => 'Passwords do not match',
      ],
    ];
  }
}