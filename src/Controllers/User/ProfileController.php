<?php
namespace WPBaseApp\Controllers\User;

use WPBaseApp\Controllers\BaseController;

class ProfileController extends BaseController
{
  private $user_id;
  
  public function __construct($template)
  {
    // Lấy user_id từ URL hoặc current user
    $this->user_id = get_query_var('user_id') ?: get_current_user_id();
    
    // Chuẩn bị data động
    $data = $this->prepareData();
    
    parent::__construct($template, $data);
  }

  public function index(): void
  {
    // Có thể thêm logic xử lý ở đây nếu cần
  }
  
  /**
   * ✅ XỬ LÝ DATA ĐỘNG - Query Database, API, etc
   */
  private function prepareData()
  {
    // Lấy user data
    $user = get_userdata($this->user_id);
    
    if (!$user) {
      wp_die('User not found');
    }

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
  private function getUserPosts()
  {
    return get_posts([
      'author' => $this->user_id,
      'posts_per_page' => 10,
      'orderby' => 'date',
      'order' => 'DESC',
    ]);
  }

  /**
   * Lấy comments của user
   */
  private function getUserComments()
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
  private function getSocialLinks()
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
  private function getUserStats()
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

  private function getMemberSinceDays()
  {
    $user = get_userdata($this->user_id);
    $registered = strtotime($user->user_registered);
    $now = current_time('timestamp');
    return floor(($now - $registered) / DAY_IN_SECONDS);
  }
}
