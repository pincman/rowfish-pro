<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 04:02:41 +0800
 * @Path           : /wp-content/themes/rowfish/templates/loop/item-none.php
 * @Description    : 文章列表中没有文章的模板
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
?>
<div class="col-12">
    <div class="_404">
        <div class="_404-inner">
            <div class="text-center">
                <img class="_404-icon mb-3" src="<?php echo get_template_directory_uri(); ?>/assets/img/empty-state-no-data.svg">
                <p class="card-text text-muted"><?php echo apply_filters('post_no_result_message', esc_html__('抱歉，此处没有任何资料。', 'rizhuti-v2')); ?></p>
                </img>
            </div>
        </div>
    </div>
</div>