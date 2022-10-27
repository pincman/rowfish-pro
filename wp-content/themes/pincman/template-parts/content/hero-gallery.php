<?php
global $post, $current_user;
$user_id = $current_user->ID; //用户ID
$post_id = $post->ID; //文章ID
$gallery_opt = get_post_meta($post_id, 'hero_gallery_data', true);
$gallery_ids = explode(',', $gallery_opt);
$free_num = (int)get_post_meta($post_id, 'hero_gallery_data_free_num', true);
$dir = get_template_directory_uri() . '/assets';
// 付费资源信息
$shop_info = get_post_shop_info();

if ($shop_info['wppay_type'] != '6') {
	return;
}
//是否购买
$RiClass = new RiClass($post_id, $user_id);
$IS_PAID = $RiClass->is_pay_post();
$is_vip_post = !empty($shop_info['wppay_vip_auth']) && empty($shop_info['wppay_price']);
$is_nologin_free = $IS_PAID == 4 && empty($user_id) && !_cao('is_rizhuti_v2_nologin_pay');
$is_nologin_pay = empty($user_id) && !_cao('is_rizhuti_v2_nologin_pay');

if (is_close_site_shop() && $IS_PAID != 4) {
	return;
}

?>
<div class="container-lg">
	<!-- 付费信息 -->
	<?php if ($shop_info['wppay_type'] == '6') : ?>
		<div class="mb-0 text-center">
			<div class="h6 text-white mb-2">
				<i class="fas fa-info-circle"></i> 以下图片共 <?php echo count($gallery_ids); ?> 个，可免费查看 <b><?php echo $free_num; ?></b> 个，剩余 <?php echo count($gallery_ids) - $free_num; ?> 个需要权限查看
			</div>
			<?php if ($IS_PAID > 0) :
				if ($is_nologin_free) {
					echo '<div class="mb-5"><a href="' . wp_login_url(curPageURL()) . '" class="btn btn-primary btn-rounded btn-sm my-3"><i class="fa fa-user"></i> ' . esc_html__('登录后免费查看', 'rizhuti-v2') . '</a></div>';
				} else {
					echo '<div class="mb-4"><b class="badge badge-light mr-2">您已获得查看权限</b></div>';
				} ?>

			<?php else : ?>
				<?php if (!empty($shop_info['wppay_vip_auth'])) {
					echo '<div class="mb-4 text-white">' . get_post_vip_auth_badge($shop_info['wppay_vip_auth']) . '可免费查看本相册所有图片</div>';
				} ?>
				<div class="mb-5">
					<?php if (!$is_vip_post) {
						if ($is_nologin_pay) {
							echo '<a href="' . wp_login_url(curPageURL()) . '" class="btn btn-primary btn-rounded mt-3" rel="nofollow noopener noreferrer"><i class="fa fa-user"></i> ' . esc_html__('登录后购买', 'rizhuti-v2') . '</a>';
						} else {
							if (site_mycoin('is')) {
								echo '<button type="button" class="click-pay-post btn btn-primary btn-sm btn-rounded ml-2 mr-2" data-postid="' . $post_id . '" data-nonce="' . wp_create_nonce('rizhuti_click_' . $post_id) . '" data-price="' . $shop_info['wppay_price'] . '">支付 ' . convert_site_mycoin($shop_info['wppay_price'], 'coin') . ' <i class="' . site_mycoin('icon') . '"></i> ' . site_mycoin('name') . '查看</button>';
							} else {
								echo '<button type="button" class="click-pay-post btn btn-primary btn-sm btn-rounded ml-2 mr-2" data-postid="' . $post_id . '" data-nonce="' . wp_create_nonce('rizhuti_click_' . $post_id) . '" data-price="' . $shop_info['wppay_price'] . '">支付 ￥' . $shop_info['wppay_price'] . ' 查看</button>';
							}
						}
					}
					if (!empty($shop_info['wppay_vip_auth'])) {
						echo '<a href="' . get_user_page_url('vip') . '" class="btn btn-warning btn-sm btn-rounded ml-2 mr-2"><i class="fa fa-diamond"></i> 升级VIP免费查看</a>';
					} ?>
				</div>
			<?php endif; ?>

		</div>
	<?php endif; ?>
	<!-- 付费信息 -->

	<div class="entry-gallery justified-gallery">
		<?php foreach ($gallery_ids as $key => $image_id) {
			$thum_1 = wp_get_attachment_image_src($image_id, 'thumbnail');
			$full = wp_get_attachment_image_src($image_id, 'full');
			$thum = (!empty($thum_1)) ? $thum_1 : $full;
			$alt = get_post_meta($image_id, '_wp_attachment_image_alt', 1);

			if ($key >= $free_num && !$IS_PAID && !$is_nologin_free) {

				$thum_2 = (!empty($thum_1)) ? $thum_1 : array(pm_get_post_thumbnail_url(), 300, 200);
				$thum = $thum_2;
				$full = array('#', 0, 0);
				$pay_note = esc_html__('暂无权限', 'rizhuti-v2');
			}
			echo '<div class="gallery-item">';
			if (isset($pay_note)) {
				echo '<span class="pay_filter"></span>';
				echo '<span class="pay_note">' . $pay_note . '</span>';
			}
			echo '<a href="' . $full[0] . '">';
			if (isset($pay_note)) {
				echo '<img class="blur-5 scale-12" src="' . $thum[0] . '" alt="' . $alt . '">';
			} else {
				echo '<img src="' . $thum[0] . '" alt="' . $alt . '">';
			}
			echo '</a>';
			if ($desc = get_the_excerpt($image_id)) {
				echo '<div class="caption">' . $desc . '</div>';
			}
			echo '</div>';
		} ?>
	</div>
</div>


<!-- JS脚本 -->
<script type="text/javascript">
	jQuery(function() {
		'use strict';
		jQuery(".entry-gallery.justified-gallery").justifiedGallery({
			border: 0,
			margins: 6,
			rowHeight: 140,
			captions: !1
		}), jQuery(".entry-gallery.justified-gallery").on("click", ".gallery-item > a", function(e) {
			e.preventDefault();
			var r, a, t = [];
			r = (a = $(this)).parent().index(), jQuery.each(a.parent().siblings().addBack(), function(e, r) {
				var a = jQuery(r).find("a").attr("href"),
					i = jQuery(r).find("img").attr("src"),
					l = jQuery(r).find("a").attr("alt") || jQuery(r).find(".caption").text();
				"#" !== a && t.push({
					src: a,
					thumb: i,
					subHtml: l
				})
			}), a.lightGallery({
				dynamic: !0,
				dynamicEl: t,
				download: !1,
				share: !1
			}).data("lightGallery").index = r
		});
	});
</script>