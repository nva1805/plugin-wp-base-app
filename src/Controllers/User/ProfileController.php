<?php
namespace WPBaseApp\Controllers\User;

use WPBaseApp\Controllers\BaseController;

class ProfileController extends BaseController
{
  private int $user_id;

  public function __construct(string $template)
  {
    parent::__construct($template);
  }

  public function handle(): void
  {
    $this->user_id = (int) (get_query_var('user_id') ?: get_current_user_id());
    $user = get_userdata($this->user_id);
    $this->render($this->prepareData($user));
  }

  private function prepareData(\WP_User $user): array
  {
    return [
      // User info
      'user' => [
        'id' => $user->ID,
        'username' => $user->user_login,
        'email' => $user->user_email,
        'display_name' => $user->display_name,
        'avatar' => get_avatar_url($user->ID),
        'registered' => $user->user_registered,
      ],

      // User meta data
      'bio' => get_user_meta($this->user_id, 'description', true),
      'social_links' => $this->getSocialLinks(),

      // User posts
      'posts' => $this->getUserPosts(),
      'post_count' => count_user_posts($this->user_id),

      // User comments
      'comments' => $this->getUserComments(),

      // Permissions
      'can_edit' => current_user_can('edit_user', $this->user_id),
      'is_own_profile' => get_current_user_id() === $this->user_id,

      // Statistics
      'stats' => $this->getUserStats(),
    ];
  }

  /**
   * Lấy posts của user từ database
   */
  private function getUserPosts(): array
  {
    $posts = get_posts([
      'author' => $this->user_id,
      'posts_per_page' => 10,
      'orderby' => 'date',
      'order' => 'DESC',
    ]);

    return array_map(function ($post) {
      return [
        'id' => $post->ID,
        'title' => $post->post_title,
        'date' => $post->post_date,
        'permalink' => get_permalink($post->ID),
      ];
    }, $posts);
  }

  /**
   * Lấy comments của user
   */
  private function getUserComments(): array
  {
    return get_comments([
      'user_id' => $this->user_id,
      'number' => 5,
      'status' => 'approve',
    ]);
  }

  /**
   * Lấy social links từ user meta
   */
  private function getSocialLinks(): array
  {
    return [
      'facebook' => get_user_meta($this->user_id, 'facebook', true),
      'twitter' => get_user_meta($this->user_id, 'twitter', true),
      'linkedin' => get_user_meta($this->user_id, 'linkedin', true),
    ];
  }

  /**
   * Tính toán statistics động
   */
  private function getUserStats(): array
  {
    global $wpdb;

    return [
      'total_posts' => count_user_posts($this->user_id),
      'total_comments' => $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->comments} WHERE user_id = %d",
        $this->user_id
      )),
      'member_since_days' => $this->getMemberSinceDays(),
    ];
  }

  private function getMemberSinceDays(): int
  {
    $user = get_userdata($this->user_id);
    $registered = strtotime($user->user_registered);
    $now = current_time('timestamp');
    return (int) floor(($now - $registered) / DAY_IN_SECONDS);
  }
}
