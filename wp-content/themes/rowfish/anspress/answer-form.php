<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 06:12:22 +0800
 * @Path           : /wp-content/themes/rowfish/anspress/answer-form.php
 * @Description    : 回答表单
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */



$ajax_query = wp_json_encode(
    array(
        'ap_ajax_action' => 'load_tinymce',
        'question_id'    => get_question_id(),
    )
);
global $current_user;
?>

<?php if (ap_user_can_answer(get_question_id())) : ?>
    <div id="answer-form-c" class="ap-minimal-editor">
        <div class="ap-avatar ap-pull-left">
            <a href="<?php echo esc_url(get_author_posts_url($current_user->ID, get_the_author_meta('display_name', $current_user->ID))); ?>">
                <?php echo get_avatar(get_current_user_id(), ap_opt('avatar_size_qquestion')); ?>
            </a>
        </div>
        <div id="ap-drop-area" class="ap-cell ap-form-c clearfix">
            <div class="ap-cell-inner">
                <div id="ap-form-main">
                    <?php echo rf_ap_answer_form((int) get_question_id()); ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php ap_get_template_part('login-signup'); ?>