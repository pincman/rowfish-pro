<?php if (_cao('is_site_footer_widget')): ?>
<div class="footer-widget d-none d-lg-block">
    <div class="container">
	    <div class="row">
	        <div class="col-lg-3 col-md">
	            <div class="footer-info">
	                <div class="logo mb-2">
	                    <img class="logo" src="<?php echo esc_url( _cao( 'site_footer_logo') ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
	                </div>
	                <p class="desc mb-0"><?php echo _cao('site_footer_desc');?></p>
	            </div>
	        </div>
	        <div class="col-lg-9 col-auto widget-warp">
	        	<div class="d-flex justify-content-xl-between">
	            	<?php dynamic_sidebar('footer');?>
	        	</div>
	        </div>
	    </div>
   </div>
</div>
<?php endif;?>
<?php if (!empty(_cao('site_footer_links',0))): 
	$bookmarks = get_bookmarks( array('orderby' => 'link_rating','category' => _cao('site_footer_links') ) );
?>
<div class="footer-links d-none d-lg-block">
	<div class="container">
		<h6><?php echo esc_html__('友情链接：','rizhuti-v2') ;?></h6>
		<ul class="friendlinks-ul">
		<?php $resul = $wpdb->get_results("SELECT * FROM $wpdb->links where link_visible ='y' ORDER BY link_rating DESC LIMIT 0 , 40");
		foreach ($bookmarks as $item){
			$nofollow = (!empty($item->link_rel)) ? ' rel="nofollow noopener noreferrer"' : ' rel="noopener noreferrer"' ;
			echo '<li><a target="'.$item->link_target.'" href="'.$item->link_url.'" title="'.$item->link_name.'"'.$nofollow.'>'.$item->link_name.'</a></li>';
		} ?>
		</ul>
	</div>
</div>
<?php endif;?>
