<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 06:08:48 +0800
 * @Path           : /wp-content/themes/rowfish/anspress/search-form.php
 * @Description    : 问题搜索组件
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */

?>

<form id="ap-search-form" class="ap-search-form" action="<?php echo esc_url(home_url('/')); ?>">
	<button class="ap-btn ap-search-btn btn btn-primary" type="submit"><?php esc_attr_e('Search', 'anspress-question-answer'); ?></button>
	<div class="ap-search-inner no-overflow">
		<input name="s" type="text" class="ap-search-input ap-form-input" placeholder="<?php esc_attr_e('Search questions...', 'anspress-question-answer'); ?>" value="<?php the_search_query(); ?>" />
		<input type="hidden" name="post_type" value="question" />
	</div>
</form>