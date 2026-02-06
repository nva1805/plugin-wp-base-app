<?php
namespace WPBaseApp\Admin;

class AdminConfig
{
  public function __construct()
  {
    add_action('admin_menu', [$this, 'addAdminMenu']);
  }

  public function addAdminMenu()
  {
    add_menu_page(
      'WP Base App',
      'WP Base App',
      'manage_options',
      'wp-base-app',
      [$this, 'renderAdminPage'],
      'dashicons-admin-generic',
      6
    );
  }

  public function renderAdminPage()
  {
    echo '<h1>WP Base App</h1>';
  }
}