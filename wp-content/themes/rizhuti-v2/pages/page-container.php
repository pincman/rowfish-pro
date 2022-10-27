<?php
/**
 * Template Name: 空白全宽页面
 */

get_header();
?>

<div class="container">
<?php while ( have_posts() ) : the_post();
    the_content();
endwhile; ?>
</div>

<?php get_footer();?>

