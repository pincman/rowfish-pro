<?php
/**
 * The sidebar containing the main widget area
 * 默认首页，非模块化首页，博客格式布局
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package rizhuti-v2
 */

get_header();
$sidebar = rizhuti_v2_sidebar();
$column_classes = rizhuti_v2_column_classes( $sidebar );

?>
<div class="container">

  <!-- 最新文章展示 -->
  <div class="row">
    <div class="<?php echo esc_attr( $column_classes[0] ); ?>">
      <div class="content-area">
          <?php if ( have_posts() ) : ?>
            <div class="row posts-wrapper scroll">
              <?php while ( have_posts() ) : the_post();
                get_template_part( 'template-parts/loop/item', rizhuti_v2_item_style());
              endwhile; ?>
            </div>
            <?php rizhuti_v2_pagination(5); ?>
          <?php else :
            get_template_part( 'template-parts/loop/item', 'none' );
          endif; ?>
      </div>
    </div>
    <?php if ( $sidebar != 'none' ) : ?>
			<div class="<?php echo esc_attr( $column_classes[1] ); ?>">
				<?php get_sidebar(); ?>
			</div>
		<?php endif; ?>
  </div>

</div>

<?php get_footer(); ?>