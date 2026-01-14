<?php
namespace WPBaseApp\Controllers\Auth;

use WPBaseApp\Controllers\BaseController;

class RegistenController extends BaseController
{
  public function __construct(string $template)
  {
    parent::__construct($template, $this->getInitialData());
  }

  public function index(): void
  {
    $this->redirectIfAuthenticated();

    if ($this->request->isMethod('POST')) {
      $this->handleRegistration();
    }
  }

  private function getInitialData()
  {
    return [
      'site_name' => get_bloginfo('name'),
      'register_nonce' => wp_create_nonce('wp-base-app-register'),
      'error' => null,
      'success' => null,
      'login_url' => site_url('/login'),
      'username' => '',
      'email' => '',
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

    // Preserve form data
    $this->mergeData([
      'username' => $username,
      'email' => $email,
    ]);

    // Validate input
    $errors = $this->validate($username, $email, $password, $password_confirm);
    
    if (!empty($errors)) {
      $this->mergeData([
        'error' => $errors[0],
        'errors' => $errors,
      ]);
      return;
    }

    // Create user
    $user_id = wp_create_user($username, $password, $email);
    
    if (is_wp_error($user_id)) {
      $this->setData('error', $user_id->get_error_message());
      return;
    }

    $this->redirect(wp_login_url());
  }

  /**
   * Validate registration input
   *
   * @param string $username
   * @param string $email
   * @param string $password
   * @param string $password_confirm
   * @return array Array of error messages, empty if valid
   */
  private function validate($username, $email, $password, $password_confirm)
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

  /**
   * Get validation rules mapping
   *
   * @param string $username
   * @param string $email
   * @param string $password
   * @param string $password_confirm
   * @return array
   */
  private function getValidationRules($username, $email, $password, $password_confirm)
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