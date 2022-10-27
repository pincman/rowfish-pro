<?php 
$currentterm = get_queried_object();
$currentterm_id  = $currentterm->term_id;
$top_term_id = (is_category()) ? get_category_root_id($currentterm_id ) : 0 ;
$current_array = array($currentterm_id);
$parent_id = $currentterm->parent;
while($parent_id){
    $current_array[] = $parent_id;
    $parent_term = get_term($parent_id,'category');
    $parent_id = $parent_term->parent;
}
?>

<div class="archive-filter">
  <div class="container">
    <div class="filters">

      <?php 
      // 获取一级分类
      $filter_cat_1=_cao('archive_filter_cat_1');
      if (_cao('is_archive_filter_cat','1') && !empty($filter_cat_1)) {
        echo '<ul class="filter">';
        echo '<li><span>'.esc_html__( '全部分类', 'rizhuti-v2').'<i class="fa fa-angle-right ml-1"></i></span></li>';
        foreach ($filter_cat_1 as $_cid) {
            $item = get_term($_cid,'category');
            $is_current = (in_array($item->term_id,$current_array)) ? ' class="current"' : '' ;
            echo '<li'.$is_current.'><a href="'.get_category_link($_cid).'" title="'.sprintf(__('%s个文章', 'rizhuti-v2'),$item->count).'">'.$item->name.'<span class="badge badge-pill badge-primary-lighten ml-1">'.$item->count.'</span></a></li>';
        }
        echo '</ul>';
      }

      // 获取二级分类
      $cat_orderby = _cao('archive_filter_cat_orderby','id');
      if ($top_term_id>0 && $child2 = get_category($top_term_id) ) {
        $is_child3 = 0 ;//三级指针
        $child_categories = get_terms('category', array('hide_empty' => 0,'parent' =>$child2->term_id,'orderby' =>$cat_orderby,'order' => 'DESC'));
        if (!empty($child_categories)) {
          echo '<ul class="filter child">';
          echo '<li><span>'.$child2->name.'<i class="fa fa-angle-right ml-1"></i></span></li>';
          foreach ($child_categories as $item) {
              $is_current = (in_array($item->term_id,$current_array)) ? ' class="current"' : '' ;
              if (!empty($is_current)) {
                $is_child3 = $item->term_id;
              }
              echo '<li'.$is_current.'><a href="'.get_category_link($item->term_id).'" title="'.sprintf(__('%s个文章', 'rizhuti-v2'),$item->count).'">'.$item->name.'<span class="badge badge-success-lighten ml-1">'.$item->count.'</span></a></li>';
          }
          echo '</ul>';
        }
        
        // 三级分类
        if ( $is_child3>0 && $child3 = get_category($is_child3) ) {
          $child_categories = get_terms('category', array('hide_empty' => 0,'parent' =>$child3->term_id,'orderby' =>$cat_orderby,'order' => 'DESC'));
          if (!empty($child_categories)) {
            echo '<ul class="filter child">';
            echo '<li><span>'.$child3->name.'<i class="fa fa-angle-right ml-1"></i></span></li></span></li>';
            foreach ($child_categories as $item) {
                $is_current = (in_array($item->term_id,$current_array)) ? ' class="current"' : '' ;
                echo '<li'.$is_current.'><a href="'.get_category_link($item->term_id).'" title="'.sprintf(__('%s个文章', 'rizhuti-v2'),$item->count).'">'.$item->name.'</a></li>';
            }
            echo '</ul>';
          }
        }
      }
      
      if (_cao('is_archive_filter_tag','1') && is_category()) {
        $tags = rizhuti_get_category_tags($currentterm_id );
        $tags = (empty($tags)) ? rizhuti_get_category_tags($top_term_id) : $tags ;
        if (!empty($tags)) {
          echo '<ul class="filter">';
          echo '<li><span>'.esc_html__( '相关标签', 'rizhuti-v2').'<i class="fa fa-angle-right ml-1"></i></span></li></span></li>';
          foreach ($tags as $tag) {
            echo '<li><a href="'.get_tag_link($tag->term_id).'">'.$tag->name.'</a></li>';
          }
          echo '</ul>';
        }
      }

      $is_archive_filter_price = _cao('is_archive_filter_price','1');
      $is_archive_filter_order = _cao('is_archive_filter_order','1');

      if ($is_archive_filter_price || $is_archive_filter_order) {
        echo '<div class="filter-tab"><div class="row">';
        echo '<div class="col-12 col-sm-8">';
        if ($is_archive_filter_price && !is_close_site_shop()) {
          $is_current = !empty($_GET['price_type']) ? $_GET['price_type'] : '';
          echo '<ul class="filter">';
          global $ri_vip_options;
          $riplus_type_arr = array('0' => esc_html__( '全部', 'rizhuti-v2'),'1' => esc_html__( '免费', 'rizhuti-v2'),'2' => esc_html__( '付费', 'rizhuti-v2') );

          $_vip_options = $ri_vip_options;
          unset($_vip_options[0]);
          foreach ($_vip_options as $key => $name) {
            $riplus_type_arr[$key] = $name.esc_html__( '免费', 'rizhuti-v2');
          }
          
          echo '<li><span>价格权限</span><i class="fa fa-angle-right ml-1"></i></span></li></li>';
          foreach ($riplus_type_arr as $key => $item) {
            $_current_css = ($is_current == $key) ? ' class="current"' : '' ;
            echo '<li'.$_current_css.'><a href="'.add_query_arg("price_type",$key).'">'.$item.'</a></li>';
          }
          echo '</ul>';
        }
        echo '</div>';

        echo '<div class="col-12 col-sm-4 recent">';
        if ($is_archive_filter_order) {
          $is_current = !empty($_GET['order']) ? $_GET['order'] : 'date';
          echo '<ul class="filter">';
          $order_arr = array('date' =>'<i class="far fa-clock"></i> '.esc_html__( '最新', 'rizhuti-v2'),'rand' =>'<i class="fas fa-random"></i> '. esc_html__( '随机', 'rizhuti-v2'),'views' => '<i class="far fa-eye"></i> '.esc_html__( '热度', 'rizhuti-v2'));
          foreach ($order_arr as $key => $item) {
            $_current_css = ($is_current == $key) ? ' class="current"' : '' ;
            echo '<li'.$_current_css.'><a href="'.add_query_arg("order",$key).'">'.$item.'</a></li>';
          }
          echo '</ul>';
        }
        echo '</div>';
        echo '</div></div>';
      }?>
    </div>
   
  </div>
</div>