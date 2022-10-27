<article id="post-<?php the_ID(); ?>" <?php post_class('article-content'); ?>>

  <div class="container">

    <?php if ( !rizhuti_v2_show_hero() ) : ?>
      <header class="entry-header">
      <?php 
        rizhuti_v2_entry_title(array( 'link' => false,'tag' => 'h1' ));
        rizhuti_v2_entry_meta(array('category' => true, 'author' => true, 'views' => true,'favnum' => true,'date' => true, 'edit' => true));
      ?>
    </header>
    <?php endif; ?>

    <div class="entry-wrapper">
      <div class="entry-content u-text-format u-clearfix">
        <?php the_content(); ?>
      </div>
      <?php rizhuti_v2_pagination(5); ?>
    </div>
  </div>
</article>

<?php
// get_template_part( 'template-parts/global/entry-navigation' );
// get_template_part( 'template-parts/global/related-posts');
?>

<?php
  // get_template_part( 'template-parts/related-posts' );
  if ( comments_open() || get_comments_number() ) :
    comments_template();
  endif;
?>
