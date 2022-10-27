<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 05:19:13 +0800
 * @Path           : /wp-content/themes/rowfish/pages/user/fav.php
 * @Description    : 用户中心-我的收藏页面
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
defined('ABSPATH') || exit;
global $current_user;
$user_id     = $current_user->ID;
$get_pay_ids = get_user_meta($user_id, 'fav_post', true);
if (empty($get_pay_ids)) {
  $get_pay_ids = array(0);
}

//** custom_pagination start **//
$pagenum = isset($_GET['pagenum']) ? absint($_GET['pagenum']) : 1; //第几页
$limit = 8; //每页显示数量
$offset = ($pagenum - 1) * $limit; //偏移量
$total = count($get_pay_ids); //总数
$max_num_pages = ceil($total / $limit); //多少页
//** custom_pagination end **//
$types = ['course' => ['text' => '课程收藏', 'icon' => 'fas fa-coffee'], 'post' => ['text' => '文章收藏', 'icon' => 'fas fa-bookmark']];
$type = isset($_GET['type']) && $_GET['type'] == 'post' ? 'post' : 'course';
$args = array(
  'post_type' => 'course',
  'post_status' => 'publish',
  'posts_per_page' => $limit,
  'paged' => $pagenum,
  'post__in' => $get_pay_ids,
  'has_password' => false,
  'ignore_sticky_posts' => 1,
  'orderby' => ['date' => 'DESC'], // modified - 如果按最新编辑时间排序
);
if ($type == 'post') {
  $args['post_type'] = 'post';
}
if (isPostTypesOrder()) {
  $args['orderby'] = ['menu_order' => 'ASC'];
}
?>

<div class="card mb-3 mb-lg-5">
  <div class="card-header">
    <h5 class="card-title"><?php echo esc_html__('我的收藏', 'rizhuti-v2'); ?></h5>
  </div>
  <!-- Body -->
  <div class="card-body">
    <ul class="nav nav-segment nav-fill mt-0 mb-4" id="editUserTab" role="tablist">
      <?php foreach ($types as $k => $v) : ?>
        <li class="nav-item">
          <?php
          $href = 'javascript:void(0);';
          if ($k != $type) {
            $href =  get_user_page_url('fav');
            if ($k != 'course') $href .= '?type=' . $k;
          }
          ?>
          <a class="nav-link<?php if ($k == $type) echo ' active'; ?>" id="fav-<?php echo $k ?>-tab" href="<?php echo $href; ?>" role="tab" aria-selected="true" target="_self">
            <?php if (isset($v['icon'])) {
              echo '<i class="' . esc_attr($v['icon']) . '"></i>';
            }
            echo esc_attr($v['text']); ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
    <div class="tab-content user-favpage p-0 row">
      <?php query_posts($args);
      if (have_posts()) {
        while (have_posts()) : the_post();
          get_template_part('templates/loop/item', null, ['no-big' => true, 'no-author' => true]);
        endwhile;
      } else {
        get_template_part('templates/loop/item', 'none');
      }
      ?>
    </div>
    <?php
    global $wp_query;
    rizhuti_v2_custom_pagination($pagenum, $wp_query->max_num_pages);
    wp_reset_query();
    ?>
  </div>
  <!-- End Body -->

</div>