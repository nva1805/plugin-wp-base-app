<?php

namespace WPBaseApp\Admin\Tabs;

use WPBaseApp\Support\AppPageManager;

class PagesTab implements TabInterface
{
  const OPTION_NAME = 'wpba_enabled_pages';
  const GROUP_NAME = 'wpba_virtual_pages';

  public function __construct()
  {
    add_action('update_option_' . self::OPTION_NAME, [$this, 'onOptionChanged']);
    add_action('add_option_' . self::OPTION_NAME, [$this, 'onOptionChanged']);
  }

  public function getId(): string
  {
    return 'pages-settings';
  }
  public function getTitle(): string
  {
    return __('Custom Pages Settings', 'wp-base-app');
  }
  public function getIcon(): string
  {
    return 'dashicons-admin-page';
  }
  public function getGroupName(): string
  {
    return self::GROUP_NAME;
  }

  public function registerSettings(): void
  {
    register_setting(self::GROUP_NAME, self::OPTION_NAME, [
      'sanitize_callback' => [$this, 'sanitize'],
    ]);
  }

  public function render(): void
  {
    $virtualPages = AppPageManager::getVirtualPages();
    $enabledPages = AppPageManager::getEnabledPages();
    $optionName = self::OPTION_NAME;

    include plugin_dir_path(dirname(__FILE__)) . 'views/tabs/pages-settings.php';
  }

  public function sanitize($value): array
  {
    $validKeys = array_keys(AppPageManager::getVirtualPages());
    $value = is_array($value) ? array_intersect($value, $validKeys) : [];
    return array_values($value);
  }

  public function onOptionChanged(): void
  {
    AppPageManager::createEnabledPages();
  }
}
