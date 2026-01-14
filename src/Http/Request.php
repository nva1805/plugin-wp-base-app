<?php
namespace WPBaseApp\Http;

/**
 * Request: handler HTTP requests
 */
class Request
{
  private $method;
  private $data;

  public function __construct()
  {
    $this->method = $_SERVER['REQUEST_METHOD'];
    $this->data = $_REQUEST;
  }

  public function isMethod($method)
  {
    return $this->method === strtoupper($method);
  }

  public function input($key, $default = null)
  {
    return $this->data[$key] ?? $default;
  }

  public function all()
  {
    return $this->data;
  }

  public function has($key)
  {
    return isset($this->data[$key]);
  }
}
