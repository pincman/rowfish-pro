<?php

$post_id = get_the_ID();
$author_id = (int)get_post_field( 'post_author', $post_id );
$comments_number = get_comments_number( $post_id );

?>


<div class="col-lg-12">

	<article id="post-<?php the_ID(); ?>" <?php post_class( 'post post-list' ); ?>>
		
	    <div class="entry-wrapper">
	    	
	    	<header class="entry-header">
	    		<?php rizhuti_v2_entry_title(array( 'link' => true ));?>
	    	</header>
	      	
	      	<?php 
	      	$excerpt = rizhuti_v2_excerpt();
	      	if ( !empty($excerpt) && $excerpt!='...' ) {
	      		echo '<div class="entry-excerpt">'.$excerpt.'</div>';
	      	}?>

	      	<div class="entry-footer">
	      		<div class="entry-meta">
	      		
				  <span class="meta-author">
	                <div class="d-flex align-items-center"><?php
	                    echo get_avatar($author_id);
	                    echo get_the_author_meta( 'display_name', $author_id );
	                  ?>
	                </div>
	              </span>
	              <span class="meta-date">
	                <time datetime="<?php echo esc_attr( get_the_date( 'c', $post_id ) ); ?>">
	                    <i class="fa fa-clock-o"></i>
	                    <?php
	                      if ( _cao('is_post_list_date_diff',true) ) {
	                        echo sprintf( __( '%s前','rizhuti-v2' ), human_time_diff( get_the_time( 'U', $post_id ), current_time( 'timestamp' ) ) );
	                      } else {
	                        echo esc_html( get_the_date( null, $post_id ) );
	                      }
	                      echo esc_html__(' 提问','rizhuti-v2');
	                    ?>
	                 </time>
	              </span>
	              <span class="meta-views"><i class="fa fa-eye"></i> <?php echo _get_post_views($post_id); ?></span>

	              <span class="meta-comment">
	                <a href="<?php echo esc_url( get_the_permalink( $post_id ) . '#comments' ); ?>">
	                   <i class="fa fa-comments-o"></i>
	                  <?php printf( _n( '%s', $comments_number, 'rizhuti-v2' ) ); ?>
	                </a>
	              </span>
	              <?php if (!$comments_number) {
	      			echo '<span class="meta-btn"><a class="" href="'. get_the_permalink( $post_id ) . '#comments">
	                   <i class="far fa-edit"></i> '.esc_html__( '写回答','rizhuti-v2' ).'</a></span>';
	      		  }?>

				</div>
			</div>
	    </div>
	</article>

</div>
