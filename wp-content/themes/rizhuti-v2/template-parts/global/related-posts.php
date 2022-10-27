<?php

$repost = (int)_cao('single_related_posts_num', 4);

if ( empty($repost) ) {
  return;
}

$related_posts_item_style = _cao('related_posts_item_style');

$type  = 'tag';
$terms = get_the_tags();

if (!$terms) {
  $terms = get_the_category();
  $type  = 'category';
}

$args = array(
  'orderby'        => 'rand',
  'post__not_in'   => array(get_the_ID()),
  'posts_per_page' => $repost,
);

$term_ids = array();

foreach ($terms as $term) {
  $term_ids[] = $term->term_id;
}

switch ($type) {
  case 'tag':
    $args['tag__in'] = $term_ids;
    break;
  case 'category':
    $args['category__in'] = $term_ids;
    break;
}

$related_posts = new WP_Query($args);
if (!$related_posts->post_count) {
  $args = array(
    'orderby'        => 'rand',
    'post__not_in'   => array(get_the_ID()),
    'posts_per_page' => (int)_cao('single_related_posts_num', 4),
  );
  $related_posts = new WP_Query($args);
}

$col_classes   = ($related_posts_item_style == 'list') ? 'col-lg-6 col-12' : 'col-lg-3 col-md-4 col-6 ';
if ($related_posts->have_posts()): ?>
    <div class="related-posts">
        <h3 class="u-border-title"><?php echo apply_filters('rizhuti_v2_related_posts_title', esc_html__('相关文章', 'rizhuti-v2')); ?></h3>
        <div class="row">
          <?php while ($related_posts->have_posts()): $related_posts->the_post();?>
            <div class="<?php echo esc_attr($col_classes); ?>">
              <article id="post-<?php the_ID();?>" <?php post_class('post post-' . $related_posts_item_style);?>>
                  <?php echo _get_post_media(null, 'thumbnail'); ?>
                  <div class="entry-wrapper">
                    <header class="entry-header"><?php rizhuti_v2_entry_title(array('link' => true));?></header>
                    <?php if ($related_posts_item_style == 'list'): ?>
                    <div class="entry-footer"><?php rizhuti_v2_entry_meta(array('category' => true, 'views' => true,'date' => true));?></div>
                    <?php endif;?>
                </div>
            </article>
          </div>
          <?php endwhile;wp_reset_postdata();?>
        </div>
    </div>
<?php endif;