<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 05:10:05 +0800
 * @Path           : /wp-content/themes/rowfish/templates/content/single.php
 * @Description    : 文章内容页
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
$_widget_wap_position = _cao('show_shop_widget_wap_position', 'bottom');
$format = get_post_format() ? get_post_format() : 'image';
$info = rf_get_post_info();
?>
<?php
if ($info['top_thumbnail']) {
  echo rf_get_post_media(null, ['width' => 600, 'height' => 250]);
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('article-content'); ?>>
  <div class="container">


    <?php if (!rizhuti_v2_show_hero() || $format != 'image') {
      get_template_part('template-parts/content/entry-header');
    } ?>

    <?php if ($_widget_wap_position == 'top') {
      shop_widget_wap_position();
    } ?>

    <div class="entry-wrapper">

      <div class="entry-content u-text-format u-clearfix">
        <?php the_content();

        rizhuti_v2_pagination(5);

        if ($_widget_wap_position == 'bottom') {
          shop_widget_wap_position();
        }

        if ($copyright = _cao('single_copyright')) {
          echo '<div class="post-note alert alert-info mt-2" role="alert">' . $copyright . '</div>';
        }
        if (_cao('is_single_tags', '1')) {
          get_template_part('template-parts/content/entry-tags');
        }

        if (_cao('is_single_share', '1')) {
          get_template_part('template-parts/content/entry-share');
        } ?>

      </div>
    </div>


  </div>
</article>

<?php
if (_cao('is_single_entry_page', true)) {
  get_template_part('template-parts/content/entry-navigation');
}
if (_cao('related_posts_item_style', 'list') != 'none') {
  get_template_part('template-parts/global/related-posts');
}
?>

<?php
// get_template_part( 'template-parts/related-posts' );
if (comments_open() || get_comments_number()) :
  comments_template();
endif;
?>