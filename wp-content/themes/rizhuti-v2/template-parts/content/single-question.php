<?php 
  $post_id = get_the_ID();
  $author_id = (int)get_post_field( 'post_author', $post_id );
  $question_comment_num = get_question_comment_num($post_id);

?>
<article id="post-<?php the_ID(); ?>" <?php post_class('article-content'); ?>>

  <div class="container">
    <?php if (_cao('is_single_breadcrumb','1')) : ?>
    <div class="article-crumb"><?php rizhuti_v2_breadcrumb('breadcrumb'); ?></div>
    <?php endif;?>
  
    <div class="entry-wrapper">
      <?php rizhuti_v2_entry_title(array('link' => false, 'tag' => 'h1'));?>
      <div class="entry-content u-text-format u-clearfix">
        <?php 
          the_content();
          rizhuti_v2_pagination(5);
        ?>

      </div>

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
                      echo sprintf( __( '%så','rizhuti-v2' ), human_time_diff( get_the_time( 'U', $post_id ), current_time( 'timestamp' ) ) );
                    } else {
                      echo esc_html( get_the_date( null, $post_id ) );
                    }
                    echo esc_html__(' æé®','rizhuti-v2');
                  ?>
               </time>
            </span>
            <span class="meta-views"><i class="fa fa-eye"></i> <?php echo _get_post_views($post_id); ?></span>

            <span class="meta-comment">
              <a href="<?php echo esc_url( get_the_permalink( $post_id ) . '#comments' ); ?>">
                 <i class="fa fa-comments-o"></i>
                <?php printf( _n( '%s', esc_html( get_comments_number( $post_id ) ), 'rizhuti-v2' ) ); ?>
              </a>
            </span>
          </div>
      </div>

      <p class="text-muted m-0 small mt-3"><?php printf( __('å±ä»¥äž <span>%s</span> äžªåç­ïŒ', 'rizhuti-v2'), $question_comment_num );?></p>


    </div>
    

  </div>
</article>



<div class="question-list">
<?php

// åŸªç¯èŸå¥è¯è®ºæ°æ®
if ( $question_comment_num > 0 ) {

  $args = array(
    'post_id' => $post_id,
    'parent' => 0,
    'meta_key' => 'liek_num',
    'orderby' => array('meta_value_num' => 'DESC','comment_date' => 'ASC'),
    'type' => 'question',
  );
  // æ°å»ºæ¥è¯¢
  
  $question_query = new WP_Comment_Query;
  $items = $question_query->query( $args );

  foreach ( $items as $item ) { ?>
    
    <div class="question-item">
      <header>
        <span class="meta-author">
          <div class="d-flex align-items-center"><?php
              echo get_avatar($item->user_id);
              echo get_the_author_meta( 'display_name', $item->user_id );
            ?>
          </div>
        </span>
        <span class="meta-author">
          <?php echo sprintf( __( '%så','rizhuti-v2' ), human_time_diff( strtotime($item->comment_date), current_time( 'timestamp' ) ) );echo esc_html__(' åç­','rizhuti-v2');?>
        </span>
      </header>
      <div class="py-3"><?php echo $item->comment_content;?></div>
      <footer class="question-info">
        <span class="btn btn-sm badge-primary-lighten go-question-liek" data-cid="<?php echo $item->comment_ID;?>"><i class="fas fa-caret-up"></i> <span><?php echo get_question_liek_num($item->comment_ID);?></span> <?php echo esc_html__( 'èµå','rizhuti-v2' );?></span>
        <span class="btn btn-sm go-question-comment" data-cid="<?php echo $item->comment_ID;?>" data-pid="<?php echo $post_id;?>"><i class="fa fa-comments-o"></i> <span><?php echo get_question_comment_num( 0, $item->comment_ID );?></span> <?php echo esc_html__( 'è¯è®º','rizhuti-v2' );?></span>
      </footer>
    </div>
  <?php }

}?>

<form id="comments" class="question-form">
    <h3><i class="far fa-edit mr-2"></i><?php echo esc_html__( 'ååç­ïŒ','rizhuti-v2' );?></h3>
    <h4><?php echo esc_html__( 'é®é¢ïŒ# ','rizhuti-v2' );?><?php the_title()?></h4>
    <div class="question-form-area">
        <input type="hidden" name="pid" value="<?php echo $post_id;?>">
        <textarea name="content" placeholder="èŸå¥åç­åå®¹..." class="form-control" rows="6"></textarea>
    </div>
    <button class="btn btn-primary mt-2 go-inst-question-comment"><i class="fa fa-send"></i> <?php echo esc_html__( 'æäº€åç­','rizhuti-v2' );?></button>
</form>

</div>
