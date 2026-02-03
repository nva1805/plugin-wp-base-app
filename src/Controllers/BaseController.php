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
   * Handle the request for each controller
   */
  abstract public function index(): void;

  public function render(): void
  {
    echo Twig::renderTemplate($this->template, $this->data);
  }

  protected function setData(string $key, mixed $value): self
  {
    $this->data[$key] = $value;
    return $this;
  }

  protected function getData(string $key, mixed $default = null): mixed
  {
    return $this->data[$key] ?? $default;
  }

  protected function mergeData(array $data): self
  {
    $this->data = array_merge($this->data, $data);
    return $this;
  }

  protected function redirect(string $url): void
  {
    wp_safe_redirect($url);
    exit;
  }

  protected function verifyNonce(string $key, string $action): bool
  {
    $nonce = $this->request->input($key, '');
    return !empty($nonce) && wp_verify_nonce($nonce, $action);
  }
}