<?php

/**
 * 修改:
 * 只在视频教程下启用
 * 删除评论tab
 * tab导航按钮flex space-between布局
 * 修改部分导航文字
 * 添加anspress问题分类连接
 * 添加在线学习按钮,点击后回到视频顶端
 */
$_widget_wap_position = _cao('show_shop_widget_wap_position', 'bottom');
$format = get_post_format() ? get_post_format() : 'image';
$shop_thumbnail_url = pm_get_post_thumbnail_url(null, 'full');
$post_shop_type = _get_post_shop_type();
$uinfo = pm_shop_post_info();
// $is_comments_tab = !(post_password_required() || !comments_open() || !is_site_comments());
$is_faq_tab = true;
$faq_tab_data = _cao('single_shop_template_help', array());
$post = get_post();
$question_category_meta = get_post_meta($post->ID, 'wppay_course_question', true);
$question_category = !empty($question_category_meta) ? (int) $question_category_meta : null;
$docs = get_post_meta($post->ID, 'wppay_course_document', true);
$course_data = get_post_meta($post->ID, 'wppay_chapter_info', true);
// 是否有介绍视频
$course_introduce = get_post_meta($uinfo['post_id'], 'wppay_course_intro', true) == '1';
$default_chapter_num = $course_introduce ? 0 : 1;
$current_chapter_num = !empty($_GET['chapter']) ? (int) $_GET['chapter'] : $default_chapter_num;
if ($current_chapter_num < 1) $current_chapter_num = $default_chapter_num;
$current_chapter = null;
$doc_content = null;
if ($current_chapter_num > 0 && $uinfo['course']) {
  $current_chapter = $course_data[$current_chapter_num - 1] ?? [];
  $doc = $current_chapter['doc'];
  if (!is_null($doc) && $uinfo['course']) {
    $doc_content = get_the_content(null, false, $doc);
  }
  if (!is_null($doc_content) && $uinfo['course']) {
    $doc_content = apply_filters('the_content', $doc_content);
    $doc_content = str_replace(']]>', ']]&gt;', $doc_content);
  }
}
?>

<?php if (_cao('is_single_shop_template_img', true) && !empty($shop_thumbnail_url) && $post_shop_type == 5) : ?>
  <div class="single-download-thumbnail">
    <div class="item-thumb lazy" style="background-image: url(<?php echo $shop_thumbnail_url; ?>)"></div>
  </div>
<?php endif; ?>


<div class='single-download-nav single-download-nav-flex'>
  <ul class="nav nav-pills" id="pills-tab" role="tablist">

    <li class="nav-item" role="presentation">
      <a class="nav-link<?php if (is_null($doc_content)) : ?> active<?php endif; ?>" id="pills-details-tab" data-toggle="pill" href="#pills-details" role="tab" aria-controls="pills-details" aria-selected="true">
        <i class="fab fa-canadian-maple-leaf mr-1"></i>序言
      </a>
    </li>
    <?php if (!is_null($doc_content)) : ?>
      <li class="nav-item" role="presentation">
        <a class="nav-link active" id="pills-doc-tab" data-toggle="pill" href="#pills-doc" role="tab" aria-controls="pills-doc" aria-selected="true">
          <i class="fas fa-book mr-1"></i>文档
        </a>
      </li>
    <?php endif; ?>
    <?php if ($is_faq_tab) : ?>
      <li class="nav-item" role="presentation">
        <a class="nav-link" id="pills-faq-tab" data-toggle="pill" href="#pills-faq" role="tab" aria-controls="pills-faq" aria-selected="false"><i class="fas fa-fire-alt mr-1"></i><?php _e('必读', 'rizhuti-v2'); ?></a>
      </li>
    <?php endif; ?>
  </ul>
  <div class="download-nav-right">
    <?php if (!is_null($question_category)) : ?>
      <a class='btn btn-outline-primary me-md-2 btn-sm' href="<?php echo get_category_link($question_category); ?>" target='_blank'"><i class=" far fa-question-circle mr-1"></i><?php _e('提问', 'rizhuti-v2'); ?></a>
    <?php endif; ?>
    <?php if (!empty($docs)) : ?>
      <!-- <a class='btn btn-outline-success me-md-2 btn-sm' href="<?php echo the_permalink($docs); ?>" target='_blank'"><i class=" fas fa-book mr-1"></i><?php _e('文档', 'rizhuti-v2'); ?></a> -->
    <?php endif; ?>
    <?php if (!empty($course_data) && count($course_data) > 0) : ?>
      <button id='goto-video' type='button' class='btn btn-outline-danger me-md-2 btn-sm'><i class='fas fa-angle-double-up mr-1'></i>学习</button>
    <?php endif; ?>
  </div>
</div>

<div class="tab-content" id="pills-tabContent">

  <div class="tab-pane fade<?php if (is_null($doc_content)) : ?> show active<?php endif; ?>" id="pills-details" role="tabpanel" aria-labelledby="pills-details-tab">
    <article id="post-<?php the_ID(); ?>" <?php post_class('article-content'); ?>>
      <div class="container">
        <?php if (_cao('is_single_breadcrumb', '1') && !$uinfo['course']) : ?>
          <div class="article-crumb"><?php rizhuti_v2_breadcrumb('breadcrumb'); ?></div>
        <?php endif; ?>

        <?php if ((!rizhuti_v2_show_hero() || $format != 'image') && !$uinfo['course']) {
          get_template_part('template-parts/content/entry-header');
        } ?>

        <?php if ($_widget_wap_position == 'top') {
          shop_widget_wap_position();
        } ?>

        <div class="entry-wrapper">
          <div class="entry-content u-text-format u-clearfix">
            <?php
            the_content();
            rizhuti_v2_pagination(5);
            if ($_widget_wap_position == 'bottom') {
              shop_widget_wap_position();
            }
            if ($copyright = _cao('single_copyright') && !$uinfo['course']) {
              echo '<div class="post-note alert alert-info mt-2" role="alert">' . $copyright . '</div>';
            }
            if (_cao('is_single_tags', '1') && !$uinfo['course']) {
              get_template_part('template-parts/content/entry-tags');
            }
            if (_cao('is_single_share', '1') && !$uinfo['course']) {
              get_template_part('template-parts/content/entry-share');
            }
            ?>
          </div>
        </div>
      </div>
    </article>
  </div>
  <?php if (!is_null($doc_content)) : ?>
    <div class="tab-pane fade show active" id="pills-doc" role="tabpanel" aria-labelledby="pills-doc-tab">
      <article id="doc-<?php echo $doc; ?>" <?php post_class('article-content'); ?>>
        <div class="container">
          <?php if ($_widget_wap_position == 'top') {
            shop_widget_wap_position();
          } ?>

          <div class="entry-wrapper">
            <div class="entry-content u-text-format u-clearfix">
              <?php
              if (strlen(trim(strip_tags($doc_content))) > 0) {
                echo $doc_content;
              } else {
                echo '<div class="alert alert-secondary" role="alert">本集教程文档还在准备中.敬请期待! </div>';
              }
              if ($_widget_wap_position == 'bottom') {
                shop_widget_wap_position();
              }
              ?>
            </div>
          </div>
        </div>
      </article>
    </div>
  <?php endif; ?>
  <?php if ($is_faq_tab) : ?>
    <div class="tab-pane fade" id="pills-faq" role="tabpanel" aria-labelledby="pills-faq-tab">

      <div class="accordion" id="accordionhelp">
        <?php foreach ($faq_tab_data as $key => $item) : ?>
          <div class="card">
            <div class="card-header" id="heading-<?php echo $key; ?>">
              <h2 class="mb-0">
                <button class="btn btn-sm btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse-<?php echo $key; ?>" aria-expanded="false" aria-controls="collapse-<?php echo $key; ?>">
                  <?php echo $item['title']; ?><span class="fa fa-plus"></span><span class="fa fa-minus"></span>
                </button>
              </h2>
            </div>
            <div id="collapse-<?php echo $key; ?>" class="collapse" aria-labelledby="heading-<?php echo $key; ?>" data-parent="#accordionhelp">
              <div class="card-body bg-primary text-white">
                <?php echo $item['desc']; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>


</div>


<?php
if (_cao('is_single_entry_page', true)) {
  get_template_part('template-parts/content/entry-navigation');
}
if (_cao('related_posts_item_style', 'list') != 'none') {
  get_template_part('template-parts/global/related-posts');
}
?>