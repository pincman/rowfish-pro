<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 21:31:56 +0800
 * @Path           : /wp-content/themes/rowfish/templates/author/header.php
 * @Description    : 用户个人空间头部组件
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
defined('ABSPATH') || exit;
$author_id = $args['author_id'];
$author = get_user_by('id', $author_id);
$top_image = trailingslashit(get_stylesheet_directory_uri()) . 'assets/images/deer.png';
$top_images = [];
$global_top_images = _cao('author_top_images', []);
if (count($global_top_images) > 0) {
  $global_top_images = array_filter($global_top_images, function ($img) {
    return $img && is_array($img) && $img['image'] && !empty($img['image']);
  });
}
if (count($global_top_images)) {
  $top_image_data = $global_top_images[rand(0, count($global_top_images) - 1)];
  if ($top_image_data && isset($top_image_data['image'])) $top_image = $top_image_data['image'];
  $top_images = array_map(function ($item) {
    return $item['image'];
  }, $global_top_images);
}
$author_top_image = get_user_meta($author_id, 'top_image', 1);
if (!empty($author_top_image)) {
  if (!in_array($author_top_image, $top_images)) {
    update_user_meta($author_id, 'top_image', null);
  } else {
    $top_image = $author_top_image;
  }
}
?>


<div class="user-top-header author-top-header">
  <div class="align-items-center">
    <!-- Bg -->
    <div class="pt-16 rounded-top" style="background-image:url(<?php echo $top_image; ?>);"></div>
    <div class="d-flex align-items-end justify-content-between bg-white px-4 pt-2 pb-4 rounded-none rounded-bottom shadow-sm">
      <div class="d-flex align-items-center">
        <div class="mr-2 position-relative d-flex justify-content-end align-items-end mt-n5">
          <img src="<?php echo get_avatar_url($author->ID); ?>" class="avatar-xl rounded-circle border-width-4 border-white" alt="<?php echo $author->display_name; ?>">
        </div>
        <div class="lh-1">
          <h5 class="mb-1">
            <?php echo $author->display_name; ?>
            <a href="<?php echo get_user_page_url('vip'); ?>" title="<?php echo date('Y-m-d', _get_user_vip_endtime()); ?>到期"><?php echo rf_get_vip_badge($author->ID, null, '', 'mx-2'); ?></a>
          </h5>
          <p class="mb-1 d-block text-muted"><?php echo $author->user_email; ?></p>
        </div>
      </div>

    </div>
  </div>
</div>