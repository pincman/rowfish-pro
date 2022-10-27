<?php

/**
 * The template for displaying question archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package rizhuti-v2
 */


get_header();
$sidebar = 'right';
$column_classes = rizhuti_v2_column_classes($sidebar);
$item_style = 'list';

$action = (isset($_GET['action'])) ? strtolower($_GET['action']) : '';
$termObj = get_queried_object();
$taxonomy = (!empty($termObj) && !empty($termObj->taxonomy)) ? $termObj->taxonomy : '';

$h_title = '最新问题';

// 查询
$_args = array(
	'post_type' => 'question',
);
if ($taxonomy == 'question_tag') {

	$h_title = '话题：#' . $termObj->name;

	$_args['tax_query'] = array(
		array(
			'taxonomy' => 'question_tag',
			'terms' => $termObj->term_id
		)
	);
}
if ($action == 'no') {
	$h_title = '等你回答';
	$_args['order'] = 'ASC';
	$_args['orderby'] = 'comment_count';
}

if ($action == 'new') {
	$h_title = '发布新问题';
}

// var_dump($_args);die;


$PostData = new WP_Query($_args);

?>
<div class="archive question-archive container">


	<div class="row">
		<div class="<?php echo esc_attr($column_classes[0]); ?>">


			<div class="content-area question-area">
				<?php echo '<h3 class="py-4 m-0 h5">' . $h_title . '</h3>'; ?>

				<?php if ($action == 'new') : ?>
					<div class="posts-wrapper">
						<form id="comments" class="new-question-form">
							<input name="title" type="text" class="form-control mb-3" placeholder="标题" autocomplete="off">
							<div class="question-form-area">
								<textarea id="comment" name="content" placeholder="输入回答内容..." class="form-control" rows="6"></textarea>
							</div>
							<button class="btn btn-primary my-4 go-inst-question-new"><i class="fa fa-send"></i> 发布问题</button>
						</form>
						<ul class="small text-muted m-0 pl-3 pb-4">
							<li>标题长度需大于6个字</li>
							<li>描述内容可不填写</li>
							<li>切勿重复提交或恶意提交</li>
							<li>恶意提交刷内容者封号处理</li>
						</ul>
					</div>
				<?php else : ?>
					<div class="row posts-wrapper scroll">
						<?php if ($PostData->have_posts()) : ?>
						<?php
							/* Start the Loop */
							while ($PostData->have_posts()) : $PostData->the_post();
								get_template_part('template-parts/loop/item-list-question');
							endwhile;
						else :
							get_template_part('template-parts/loop/item', 'none');

						endif;
						?>
					</div>
					<?php rizhuti_v2_pagination(5); ?>
				<?php endif; ?>

			</div>
		</div>
		<?php if ($sidebar != 'none') : ?>
			<div class="<?php echo esc_attr($column_classes[1]); ?>">
				<aside id="secondary" class="widget-area">
					<?php get_template_part('template-parts/global/widget-question'); ?>
				</aside>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php
wp_enqueue_script('question');
get_footer();
