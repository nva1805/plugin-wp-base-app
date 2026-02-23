<?php

if (!defined('ABSPATH')) {
  exit;
}

/**
 * @var array<string, \WPBaseApp\Admin\Tabs\TabInterface> $tabs
 * @var string $activeTab Current active tab ID
 */
?>

<div class="wrap">
  <div class="wpba-admin-header">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <span class="wpba-version">v<?php echo WP_BASE_APP_VERSION; ?></span>
  </div>

  <hr class="wp-header-end">
  <?php settings_errors(); ?>

  <!-- Tab Navigation -->
  <div class="wpba-tabs">
    <?php foreach ($tabs as $tabId => $tab): ?>
          <a href="?page=wp-base-app-config-admin&tab=<?php echo esc_attr($tabId); ?>"
             class="wpba-tab-link <?php echo $activeTab === $tabId ? 'active' : ''; ?>">
            <span class="dashicons <?php echo esc_attr($tab->getIcon()); ?>"></span>
            <?php echo esc_html($tab->getTitle()); ?>
          </a>
    <?php endforeach; ?>
  </div>

  <!-- Settings Form -->
  <form method="POST" action="options.php">
    <div class="wpba-tab-content">
      <?php
      if (isset($tabs[$activeTab])) {
        settings_fields($tabs[$activeTab]->getGroupName());
        $tabs[$activeTab]->render();
      }
      ?>
    </div>

    <div class="wpba-submit-area">
      <?php submit_button(__('Save Changes', 'wp-base-app'), 'primary', 'submit', false); ?>
    </div>
  </form>
</div>
