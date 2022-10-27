<?php
defined('ABSPATH') || exit;
global $current_user, $paged;
$user_id     = $current_user->ID;
$get_pay_ids = get_user_meta($user_id, 'fav_post', true);
if (empty($get_pay_ids)) {
  $get_pay_ids = array(0);
}
$args = array(
  'post_type' => 'post',
  'post_status' => 'publish',
  'posts_per_page' => 10,
  'paged' => $paged,
  //'showposts' => count($current_post_ids),
  'post__in' => $get_pay_ids,
  'has_password' => false,
  'ignore_sticky_posts' => 1,
  'orderby' => 'date', // modified - 如果按最新编辑时间排序
  'order' => 'DESC'
);
?>

<div class="card mb-3 mb-lg-5">
  <div class="card-header">
    <h5 class="card-title"><?php echo esc_html__('我的收藏', 'rizhuti-v2'); ?></h5>
  </div>
  <!-- Body -->
  <div class="card-body">

    <div class="user-favpage p-0 row">
      <?php query_posts($args);
      if (have_posts()) {
        while (have_posts()) : the_post();
          get_template_part('template-parts/loop/item-list');
        endwhile;
      } else {
        get_template_part('template-parts/loop/item', 'none');
      }
      ?>
    </div>
    <?php rizhuti_v2_pagination(5);
    wp_reset_query(); ?>
  </div>
  <!-- End Body -->

</div>