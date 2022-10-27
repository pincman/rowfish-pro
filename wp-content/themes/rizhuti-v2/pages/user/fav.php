<?php
defined('ABSPATH') || exit;
global $current_user;
$user_id     = $current_user->ID;
$get_pay_ids = get_user_meta($user_id,'fav_post',true) ;
if (empty($get_pay_ids)) {
    $get_pay_ids = array(0);
}

//** custom_pagination start **//
$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1; //第几页
$limit = 10; //每页显示数量
$offset = ( $pagenum - 1 ) * $limit; //偏移量
$total = count($get_pay_ids); //总数
$max_num_pages = ceil( $total / $limit ); //多少页
//** custom_pagination end **//


$args = array(
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => $limit,
    'paged' => $pagenum,
    'post__in' => $get_pay_ids,
    'has_password' => false,
    'ignore_sticky_posts' => 1,
    'orderby' => 'date', // modified - 如果按最新编辑时间排序
    'order' => 'DESC'
);
?>

<div class="card mb-3 mb-lg-5">
    <div class="card-header">
        <h5 class="card-title"><?php echo esc_html__('我的收藏','rizhuti-v2');?></h5>
    </div>
    <!-- Body -->
    <div class="card-body">
    
      <div class="user-favpage p-0 row">
       <?php query_posts($args);
        if ( have_posts() ){
          while ( have_posts() ) : the_post();
            get_template_part( 'template-parts/loop/item-list');
          endwhile;
        }else{
          get_template_part( 'template-parts/loop/item', 'none');
        }
        ?>
      </div>
      <?php 
        global $wp_query;
        rizhuti_v2_custom_pagination($pagenum,$wp_query->max_num_pages);
        wp_reset_query();
      ?>
    </div>
    <!-- End Body -->
   
</div>

