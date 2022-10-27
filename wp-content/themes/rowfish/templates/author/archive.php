<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 05:10:53 +0800
 * @Path           : /wp-content/themes/rowfish/templates/author/archive.php
 * @Description    : 用户个人空间内容列表
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
$author_tab = $args['author_tab'];
$author_id = $args['author_id'];
$has_data = false;
?>
<?php if (is_null($author_tab) || $author_tab == 'courses') :
    if ($author_tab == 'courses') rf_build_course_page_query(8, true);
?>
    <div class="row">
        <div class="col-lg-12">
            <div class="content-area archive-list">
                <div class="row posts-wrapper scroll">
                    <?php if (have_posts()) :
                        $has_data = true;
                        /* Start the Loop */
                        while (have_posts()) : the_post();
                            $author_tab == 'courses' ?  get_template_part('course/templates/item') : get_template_part('templates/loop/item');
                        endwhile;
                    else :
                        get_template_part('templats/loop/item', 'none');

                    endif;
                    ?>
                </div>
            </div>
        </div>
    </div>
<? endif; ?>
<?php if (in_array($author_tab, ['questions', 'answers'])) : ?>
    <div class="anspress-content">
        <div class="anspress user-center-anspress" id="anspress">
            <?php if ($author_tab == 'questions') : ?>
                <?php
                $_args['ap_current_user_ignore'] = true;
                $_args['author']                 = $author_id;
                $_args = apply_filters('ap_authors_questions_args', $_args);
                anspress()->questions = new \Question_Query($_args); ?>
                <?php if (ap_have_questions()) : $has_data = true; ?>
                    <div class="ap-questions">
                        <?php
                        /* Start the Loop */
                        while (ap_have_questions()) :
                            ap_the_question();
                            get_template_part('pages/user/question-list-item');
                        endwhile;
                        ?>
                    </div>

                    <?php
                    ap_pagination(false, anspress()->questions->max_num_pages, '?paged=%#%', ap_user_link($_args['author'], 'questions'));
                    ?>
                <?php endif; ?>
            <?php elseif ($author_tab == 'answers') : ?>
                <?php
                global $answers;
                $_args['ap_current_user_ignore'] = true;
                $_args['ignore_selected_answer'] = true;
                $_args['showposts']              = 10;
                $_args['author']                 =  $author_id;
                $_args               = apply_filters('ap_user_answers_args', $author_id);
                anspress()->answers = $answers = new \Answers_Query($_args);
                ?>
                <?php if (ap_have_answers()) : $has_data = true; ?>
                    <div id="ap-bp-answers">
                        <?php
                        /* Start the Loop */
                        while (ap_have_answers()) :
                            ap_the_answer();
                            get_template_part('pages/user/answer-item');
                        endwhile;
                        ?>
                    </div>
                    <?php
                    if ($answers->max_num_pages > 1) {
                        $_args = wp_json_encode(
                            [
                                'ap_ajax_action' => 'user_more_answers',
                                '__nonce'        => wp_create_nonce('loadmore-answers'),
                                'type'           => 'answers',
                                'current'        => 1,
                                'user_id'        => get_queried_object_id(),
                            ]
                        );

                        echo '<button class="ap-bp-loadmore ap-btn" ap-loadmore="' . esc_js($_args) . '">' . esc_attr__('Load more answers', 'anspress-question-answer') . '</button>';
                    }
                    ?>
                <?php endif; ?>
                <?php if (!$has_data) get_template_part('templates/loop/item', 'none'); ?>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>