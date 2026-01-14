<?php

namespace WPBaseApp\View;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Twig
{
  private static $twig = null;

  public static function getInstance()
  {
    if (self::$twig === null) {
      $paths = [];
      if (is_dir(THEME_TEMPLATES)) {
        $paths[] = THEME_TEMPLATES;
      }
      $paths[] = WP_BASE_APP_TEMPLATES;
      $loader = new FilesystemLoader($paths);
      self::$twig = new Environment($loader, [
        'cache' => false,
        'debug' => defined('WP_DEBUG') ? WP_DEBUG : true,
      ]);
      self::registerWordPressHelpers(self::$twig);
    }
    return self::$twig;
  }

  private static function registerWordPressHelpers(Environment $twig)
  {
    // Danh sách các hàm WordPress muốn expose vào Twig
    $functions = [
      'home_url',
      'site_url',
      //'wp_nonce_field',
      'wp_nonce_url',
      'wp_login_url',
      'wp_logout_url',
      'wp_registration_url',
      'wp_lostpassword_url',
      'is_user_logged_in',
      'get_current_user_id',
      'current_user_can',
      //'get_permalink',                                                                                       
      'get_the_title',
      'get_avatar',
      'admin_url',
      'plugins_url',
      'content_url',
      'wp_get_current_user',
      'wp_head',
      'wp_footer',
      'wp_body_open',
      'get_bloginfo',
    ];

    foreach ($functions as $name) {
      $twig->addFunction(new \Twig\TwigFunction($name, $name));
    }

    $filters = [
      'esc_html',
      'esc_attr',
      'esc_url',
      'esc_js',
      'esc_textarea',
      'sanitize_text_field',
      'number_format',
      'date' => 'date_i18n',
    ];

    foreach ($filters as $twigName => $wpFunction) {
      if (is_numeric($twigName)) {
        $twigName = $wpFunction;
      }
      $twig->addFilter(new \Twig\TwigFilter($twigName, $wpFunction));
    }
  }

  public static function renderTemplate($template, $data = [])
  {
    return self::getInstance()->render($template, $data);
  }
}