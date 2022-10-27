<?php
	$col_classes = (rizhuti_v2_sidebar() != 'none') ? 'col-lg-12' : 'col-lg-6 col-12' ;
?>

<div class="<?php echo esc_attr( $col_classes );?>">

	<article id="post-<?php the_ID(); ?>" <?php post_class( 'post post-list' ); ?>>
		<?php if (_cao('is_post_list_price',true)) {
			echo get_post_meta_vip_price();
		}?>
	    <?php echo _get_post_media(null,'thumbnail');?>
	    <div class="entry-wrapper">
	    	<?php if (_cao('is_post_list_category',1)) {
	    		rizhuti_v2_category_dot(2);
	    	}?>
	    	<header class="entry-header">
	    		<?php rizhuti_v2_entry_title(array( 'link' => true ));?>
	    	</header>
	      
			<div class="entry-excerpt"><?php echo rizhuti_v2_excerpt(); ?></div>

	      	<div class="entry-footer">
			  <?php rizhuti_v2_entry_meta(
			   array( 
			   	'author' => _cao('is_post_list_author',1), 
			   	'category' => false,
			   	'comment' => _cao('is_post_list_comment',1),
			   	'date' => _cao('is_post_list_date',1),
			   	'favnum' => _cao('is_post_list_favnum',1),
			   	'views' => _cao('is_post_list_views',1),
			   	'shop' => _cao('is_post_list_shop',1),
			   )
			);?>
			</div>
	    </div>
	</article>

</div>
