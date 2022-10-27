<?php
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
  'posts_per_page' => (int)_cao('single_related_posts_num', 4),
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
