<?php 
global $post;
$post_id = $post->ID; //文章ID
$author_id = (int)get_the_author_meta( 'ID' ); ?>

<div class="entry-share">
	<div class="row">
		<div class="col d-none d-lg-block">
			<a class="share-author" href="<?php echo esc_url( get_author_posts_url($author_id,get_the_author_meta( 'display_name', $author_id ) ));?>">
                <?php
                echo get_avatar( $author_id, 50 );
                echo get_the_author_meta( 'display_name', $author_id ) . get_vip_badge($author_id);
                ?>
            </a>
		</div>
		<div class="col-auto mb-3 mb-lg-0">
			<?php if (!is_close_site_shop()) {

                if (is_fav_post($post_id)) {
                    $arr = array(1=>esc_html__('取消收藏','rizhuti-v2'),2=>' ok');
                }else{
                    $arr = array(1=>esc_html__('收藏','rizhuti-v2'),'rizhuti-v2',2=>'');
                }
                echo '<button class="go-star-btn btn btn-sm btn-outline-info mr-2'.$arr[2].'" data-id="'.$post_id.'"><i class="far fa-star"></i> '.$arr[1].'</button>';
            }?>
			
			<button class="share-poster btn btn-sm btn-outline-info" data-id="<?php echo $post_id;?>"><i class="fa fa-share-alt"></i> <?php echo esc_html__('海报分享','rizhuti-v2');?></button>
		</div>
	</div>
</div>
