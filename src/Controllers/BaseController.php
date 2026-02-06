<?php
namespace WPBaseApp\Controllers;

use WPBaseApp\View\Twig;
use WPBaseApp\Http\Request;

abstract class BaseController
{
  protected string $template;
  protected array $data = [];
  protected Request $request;

  public function __construct(string $template)
  {
    $this->template = $template;
    $this->request = new Request();
  }

  abstract public function handle(): void;
  protected function render(array $data = []): void
  {
    $this->data = array_merge($this->data, $data);
    echo Twig::renderTemplate($this->template, $this->data);
    exit;
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