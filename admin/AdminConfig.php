<?php

namespace WPBaseApp\Admin;

use WPBaseApp\Admin\Tabs\PagesTab;
use WPBaseApp\Admin\Tabs\TabInterface;
use WPBaseApp\Support\AppPageManager;

class AdminConfig
{
  /**
   * @var TabInterface[]
   */
  private array $tabs = [];

  public function __construct()
  {
    $this->registerTabs();

    // Admin UI hooks
    add_action('admin_menu', [$this, 'addAdminMenu']);
    add_action('admin_init', [$this, 'registerSettings']);
    add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
  }

  /**
   * Register available settings tabs.
   * New tabs can be instantiated and added here.
   */
  private function registerTabs(): void
  {
    $customPagesTab = new PagesTab();
    $this->tabs[$customPagesTab->getId()] = $customPagesTab;

    // Future tabs:
    // $generalTab = new GeneralTab();
    // $this->tabs[$generalTab->getId()] = $generalTab;
  }

  /**
   * Loop through tabs and ask them to register their Settings API fields
   */
  public function registerSettings(): void
  {
    foreach ($this->tabs as $tab) {
      $tab->registerSettings();
    }
  }

  public function addAdminMenu()
  {
    $iconPath = plugin_dir_path(__FILE__) . 'imgs/icon.svg';
    $iconSvg = file_get_contents($iconPath);
    $iconBase64 = 'data:image/svg+xml;base64,' . base64_encode($iconSvg);

    add_menu_page(
      'Base App config',
      'Base App config',
      'manage_options',
      'wp-base-app-config-admin',
      [$this, 'renderAdminPage'],
      $iconBase64,
    );
  }

  public function enqueueAdminAssets($hook)
  {
    if ($hook !== 'toplevel_page_wp-base-app-config-admin') {
      return;
    }

    wp_enqueue_style(
      'wpba-admin-style',
      plugin_dir_url(__FILE__) . 'css/admin-style.css',
      [],
      WP_BASE_APP_VERSION
    );
  }

  public function renderAdminPage()
  {
    // Define active tab or fallback to the first tab (or 'virtual-pages')
    $defaultTab = !empty($this->tabs) ? array_key_first($this->tabs) : '';
    $activeTab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : $defaultTab;

    // Pass the registered tabs into the view to be rendered dynamically
    $tabs = $this->tabs;

    include __DIR__ . '/views/admin-page.php';
  }
}