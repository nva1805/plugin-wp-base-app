<?php
namespace WPBaseApp\Controllers;

use WPBaseApp\View\Twig;
use WPBaseApp\Http\Request;

abstract class BaseController
{
  protected string $template;
  protected array $data = [];
  protected Request $request;

  public function __construct(string $template, array $data = [])
  {
    $this->template = $template;
    $this->data = $data;
    $this->request = new Request();
  }

  /**
   * Handle the request
   */
  abstract public function index(): void;

  /**
   * Render the template
   */
  public function render(): void
  {
    echo Twig::renderTemplate($this->template, $this->data);
  }

  /**
   * Set data for template
   */
  protected function setData(string $key, mixed $value): self
  {
    $this->data[$key] = $value;
    return $this;
  }

  /**
   * Get data value
   */
  protected function getData(string $key, mixed $default = null): mixed
  {
    return $this->data[$key] ?? $default;
  }

  /**
   * Merge data into template data
   */
  protected function mergeData(array $data): self
  {
    $this->data = array_merge($this->data, $data);
    return $this;
  }

  /**
   * Redirect to URL
   */
  protected function redirect(string $url): void
  {
    wp_safe_redirect($url);
    exit;
  }

  /**
   * Check if user is authenticated
   */
  protected function isAuthenticated(): bool
  {
    return (bool) get_current_user_id();
  }

  /**
   * Redirect if authenticated
   */
  protected function redirectIfAuthenticated(string $url = ''): void
  {
    if ($this->isAuthenticated()) {
      $this->redirect($url ?: home_url());
    }
  }

  /**
   * Redirect if not authenticated
   */
  protected function redirectIfGuest(string $url = ''): void
  {
    if (!$this->isAuthenticated()) {
      $this->redirect($url ?: wp_login_url());
    }
  }

  /**
   * Verify nonce from request
   */
  protected function verifyNonce(string $key, string $action): bool
  {
    $nonce = $this->request->input($key, '');
    return !empty($nonce) && wp_verify_nonce($nonce, $action);
  }
}