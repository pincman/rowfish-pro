<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 05:09:58 +0800
 * @Path           : /wp-content/themes/rowfish/templates/content/page.php
 * @Description    : 公共内容页模板
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('article-content'); ?>>

  <div class="container">
    <div class="entry-wrapper">
      <div class="entry-content u-text-format u-clearfix">
        <?php the_content(); ?>
      </div>
      <?php rizhuti_v2_pagination(5); ?>
    </div>
  </div>
</article>

<?php
// get_template_part( 'template-parts/global/entry-navigation' );
// get_template_part( 'template-parts/global/related-posts');
?>

<?php
// get_template_part( 'template-parts/related-posts' );
if (comments_open() || get_comments_number()) :
  comments_template();
endif;
?>