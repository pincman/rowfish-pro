<?php

/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package rizhuti-v2
 */
?>
</main><!-- #main -->

<footer class="site-footer">
	<?php if (is_front_page() || is_home()) : ?>
		<?php get_template_part('template-parts/global/footer-widget'); ?>
	<?php endif; ?>
	<div class="footer-copyright d-flex text-center">
		<div class="container">
			<p class="m-0 small"><?php echo _cao('site_copyright_text') . '<span class="sep"> | </span>' . _cao('site_ipc_text') . '<span class="sep"> | </span>' . _cao('site_ipc2_text'); ?><?php do_action('get_copyright_before'); ?>
			</p>
		</div>
	</div>

</footer><!-- #footer -->

</div><!-- #page -->
<?php get_template_part('template-parts/global/footer-rollbar'); ?>

<div class="dimmer"></div>
<?php
get_template_part('template-parts/global/off-canvas');
get_template_part('template-parts/global/omnisearch');

?> <?php wp_footer(); ?>
<!-- 自定义js代码 统计代码 -->
<?php if (!empty(_cao('web_js'))) echo _cao('web_js'); ?>
<!-- 自定义js代码 统计代码 END -->

</body>

</html>