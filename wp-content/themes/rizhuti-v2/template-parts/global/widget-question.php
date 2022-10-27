<?php

global $wpdb;
$q_count = $wpdb->get_var("
    SELECT COUNT(ID) FROM $wpdb->posts WHERE post_type = 'question' AND post_status = 'publish' AND comment_count<=0
");

$c_count = $wpdb->get_var($wpdb->prepare("SELECT SUM(meta_value) FROM $wpdb->commentmeta WHERE meta_key=%s", 'liek_num'));

?>


<div class="widget question-btns">
	<div class="p-4 text-center">
	<a href="<?php echo home_url('/question');?>" class="btn btn-info btn-rounded btn-block"> <i class="fas fa-list mr-2"></i>最新问题 </a>
	<a href="<?php echo home_url('/question?action=no');?>" class="btn btn-primary btn-rounded btn-block"> <i class="fa fa-comments-o mr-2"></i>等你回答 </a>
	<a href="<?php echo home_url('/question?action=new');?>" class="btn btn-success btn-rounded btn-block"> <i class="far fa-edit mr-2"></i>开始提问 </a>
	<p class="m-0 mt-3 text-muted">共有 <?php echo (int)$q_count;?> 个问题等你回答</p>
	<p class="m-0 text-muted small">累计收到 <?php echo (int)$c_count;?> 次点赞</p>
	</div>
</div>



<div class="widget question-users">
	<h5 class="widget-title">回答排行</h5>
	<ul>
	<?php
	$result = $wpdb->get_results("select user_id,count(comment_ID) as comment_num from $wpdb->comments where comment_type='question' AND comment_parent=0 group by user_id order by count(comment_ID) desc limit 5");
	foreach ($result as $key => $item) {
		echo '<li class="d-flex align-items-center">';
		echo get_avatar($item->user_id);
		echo '<div class="ml-3">';
		echo '<b>'.get_the_author_meta( 'display_name', $item->user_id ).'</b>';
		echo '<p class="small text-muted m-0">排名：'.($key+1).' | 回答：'.$item->comment_num.'</p>';
        echo '</div>';
		echo '</li>';
	}?>
	</ul>
	
</div>




<div class="widget question-tags">
	<h5 class="widget-title mb-3">热门话题</h5>
	<div class="tagcloud">
	<?php
	
	$tags = get_tags(array(
	  'taxonomy' => 'question_tag',
	  'orderby' => 'count',
	  'number' => 16, //显示
	  'hide_empty' => true // for development
	));
	foreach ($tags as $tag) {
		echo '<a href="'.get_tag_link( $tag->term_id ).'" class="tag-cloud-link" rel="tag" title="'.$tag->name.'">'.$tag->name.' ('.$tag->count.')</a>';
	}?>
	</div>
	
</div>
