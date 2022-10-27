<?php
$tags = get_the_tags();
if ( !empty($tags) ){
    echo '<div class="entry-tags">';
    foreach ( $tags as $tag ){
       echo '<a href="'.esc_url( get_tag_link( $tag->term_id ) ).'" class="btn btn-sm btn-light mr-2" rel="tag">'.esc_html( $tag->name ).'</a>'; 
    }
    echo '</div>';
}
