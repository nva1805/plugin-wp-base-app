<?php

if (!defined('ABSPATH')) {
  exit;
}

/**
 * @var array  $virtualPages All available virtual pages from config
 * @var array  $enabledPages Currently enabled page keys
 * @var string $optionName   Option name for the checkbox input
 */
?>

<p class="description">
  <?php _e('Enable or disable the virtual pages for your site. Enabled pages will have URL routes created automatically.', 'wp-base-app'); ?>
</p>

<table class="form-table">
  <tbody>
    <?php foreach ($virtualPages as $key => $page): ?>
        <tr>
          <th scope="row"><?php echo esc_html($page['title']); ?></th>
          <td>
            <label class="wpba-toggle">
              <input type="checkbox"
                     name="<?php echo esc_attr($optionName); ?>[]"
                     value="<?php echo esc_attr($key); ?>"
                     <?php checked(in_array($key, $enabledPages)); ?>>
              <span class="wpba-toggle-slider"></span>
            </label>
            <code class="wpba-field-slug">/<?php echo esc_html($page['slug']); ?></code>
          </td>
        </tr>
    <?php endforeach; ?>
  </tbody>
</table>
