<div class="entry-navigation">
	<div class="row">
        <?php if( $pre = get_previous_post() ): ?>
    	<div class="col-lg-6 col-12">
            <a class="entry-page-prev lazyload" href="<?php echo get_the_permalink($pre->ID);?>" title="<?php echo esc_attr(get_the_title($pre->ID));?>" data-bg="<?php echo esc_url(_get_post_thumbnail_url($pre->ID));?>">
                <div class="entry-page-icon"><i class="fas fa-arrow-left"></i></div>
                <div class="entry-page-info">
                    <span class="d-block rnav"><?php echo esc_html( '上一篇','rizhuti-v2' );?></span>
                    <span class="d-block title"><?php echo get_the_title($pre);?></span>
                </div>
            </a> 
        </div>
        <?php endif; ?>
        <?php if( $next = get_next_post() ): ?>
    	<div class="col-lg-6 col-12">
            <a class="entry-page-next lazyload" href="<?php echo get_the_permalink($next->ID);?>" title="<?php echo esc_attr(get_the_title($next->ID));?>" data-bg="<?php echo esc_url(_get_post_thumbnail_url($next->ID));?>">
                <div class="entry-page-info">
                    <span class="d-block rnav"><?php echo esc_html( '下一篇', 'rizhuti-v2' );?></span>
                    <span class="d-block title"><?php echo get_the_title($next);?></span>
                </div>
                <div class="entry-page-icon"><i class="fas fa-arrow-right"></i></div>
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>