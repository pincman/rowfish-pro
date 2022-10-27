<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 06:11:21 +0800
 * @Path           : /wp-content/themes/rowfish/anspress/function.php
 * @Description    : 修改Anspress问答插件相关函数
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
// anspress()->add_action('ap_answer_form_fields', 'new_answer_forms', 10, 2);
function rf_ap_answer_form($question_id, $editing = false)
{
    $editing    = false;
    $editing_id = ap_sanitize_unslash('id', 'r');
    // If post_id is empty then its not editing.
    if (!empty($editing_id)) {
        $editing = true;
    }

    if ($editing && !ap_user_can_edit_answer($editing_id)) {
        echo '<p>' . esc_attr__('You cannot edit this answer.', 'anspress-question-answer') . '</p>';
        return;
    }

    if (!$editing && !ap_user_can_answer($question_id)) {
        echo '<p>' . esc_attr__('You do not have permission to answer this question.', 'anspress-question-answer') . '</p>';
        return;
    }
    $args = array(
        'hidden_fields' => array(
            array(
                'name'  => 'action',
                'value' => 'ap_form_answer',
            ),
            array(
                'name'  => 'question_id',
                'value' => (int) $question_id,
            ),
        ),
    );

    $values         = [];
    $session_values = anspress()->session->get('form_answer_' . $question_id);

    // Add value when editing post.
    if ($editing) {
        $answer = ap_get_post($editing_id);

        $form['editing']      = true;
        $form['editing_id']   = $editing_id;
        $form['submit_label'] = __('Update Answer', 'anspress-question-answer');

        $values['post_title']   = $answer->post_title;
        $values['post_content'] = $answer->post_content_filtered;
        $values['is_private']   = 'private_post' === $answer->post_status ? true : false;

        if (isset($values['anonymous_name'])) {
            $fields = ap_get_post_field('fields', $answer);

            $values['anonymous_name'] = !empty($fields['anonymous_name']) ? $fields['anonymous_name'] : '';
        }

        $args['hidden_fields'][] = array(
            'name'  => 'post_id',
            'value' => (int) $editing_id,
        );
    } elseif (!empty($session_values)) {
        // Set last session values if not editing.
        $values = $session_values;
    }
    $af = anspress()->get_form('answer');
    unset($af->args['fields']['is_private']);
    $af->args['fields']['post_content']['type'] = 'textarea';
    // $af->args['fields']['post_content']['name'] = 'post_content';
    // unset($af->args['fields']['post_content']);
    // <div id="wp-content-editor-container"></div>
    ob_start();
    $af->set_values($values)->generate($args);
    $html = ob_get_contents();
    ob_end_clean();
    $doc = new DOMDocument();
    $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    $form = $doc->getElementById('form_answer');
    $before = $doc->createElement('div');
    $attr = $doc->createAttribute('id');
    $attr->value = 'wp-content-editor-container';
    $before->appendChild($attr);
    $form->insertBefore($before, $form->firstChild);
    echo $doc->saveHTML();
}

function rf_ap_ask_form($deprecated = null)
{
    if (!is_null($deprecated)) {
        _deprecated_argument(__FUNCTION__, '4.1.0', 'Use $_GET[id] for currently editing question ID.');
    }

    $editing    = false;
    $editing_id = ap_sanitize_unslash('id', 'r');

    // If post_id is empty then its not editing.
    if (!empty($editing_id)) {
        $editing = true;
    }

    if ($editing && !ap_user_can_edit_question($editing_id)) {
        echo '<p>' . esc_attr__('You cannot edit this question.', 'anspress-question-answer') . '</p>';
        return;
    }

    if (!$editing && !ap_user_can_ask()) {
        echo '<p>' . esc_attr__('You do not have permission to ask a question.', 'anspress-question-answer') . '</p>';
        return;
    }

    $args = array(
        'hidden_fields' => array(
            array(
                'name'  => 'action',
                'value' => 'ap_form_question',
            ),
        ),
    );

    $values         = [];
    $session_values = anspress()->session->get('form_question');

    // Add value when editing post.
    if ($editing) {
        $question = ap_get_post($editing_id);

        $form['editing']      = true;
        $form['editing_id']   = $editing_id;
        $form['submit_label'] = __('Update Question', 'anspress-question-answer');

        $values['post_title']   = $question->post_title;
        $values['post_content'] = $question->post_content_filtered;
        $values['is_private']   = 'private_post' === $question->post_status ? true : false;

        if (isset($values['anonymous_name'])) {
            $fields = ap_get_post_field('fields', $question);

            $values['anonymous_name'] = !empty($fields['anonymous_name']) ? $fields['anonymous_name'] : '';
        }
    } elseif (!empty($session_values)) {
        // Set last session values if not editing.
        $values = $session_values;
    }

    // Generate form.
    $af = anspress()->get_form('question');
    unset($af->args['fields']['is_private']);
    $af->args['fields']['post_content']['type'] = 'textarea';
    ob_start();
    $af->set_values($values)->generate($args);
    $html = ob_get_contents();
    ob_end_clean();
    $doc = new DOMDocument();
    $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    $form = $doc->getElementById('form_question');
    $before = $doc->createElement('div');
    $attr = $doc->createAttribute('id');
    $attr->value = 'wp-content-editor-container';
    $before->appendChild($attr);
    $form->insertBefore($before, $form->childNodes[1]);
    echo $doc->saveHTML();
}

function answer_edit_page()
{
    $post_id = (int) ap_sanitize_unslash('id', 'r');

    if (!ap_verify_nonce('edit-post-' . $post_id) || empty($post_id) || !ap_user_can_edit_answer($post_id)) {
        echo '<p>' . esc_attr__('Sorry, you cannot edit this answer.', 'anspress-question-answer') . '</p>';
        return;
    }

    global $editing_post;
    $editing_post = ap_get_post($post_id);
    rf_ap_answer_form($editing_post->post_parent, true);
}
function init_qa()
{
    ap_register_page('edit', __('Edit Answer', 'anspress-question-answer'),  'answer_edit_page', false);
    add_rewrite_rule(
        'questions/([0-9]+)?.html$',
        'index.php?post_type=dwqa-question&p=$matches[1]',
        'top'
    );
}

add_action('init', 'init_qa');
// function anspress_user_rewrite_rules($rules, $slug, $base_page_id)
// {
//     $base_slug = get_page_uri(ap_opt('user_page'));
//     update_option('ap_user_path', $base_slug . '/aaaa', true);

//     $new_rules = [];
//     $new_rules = array(
//         $base_slug . '/([^/]+)/([^/]+)/page/?([0-9]{1,})/?' => 'index.php?author_name=$matches[#]&ap_page=user&user_page=$matches[#]&ap_paged=$matches[#]',
//         $base_slug . '/([^/]+)/([^/]+)/?' => 'index.php?author_name=$matches[#]&ap_page=user&user_page=$matches[#]',
//         $base_slug . '/([^/]+)/?'         => 'index.php?author_name=$matches[#]&ap_page=user',
//         $base_slug . '/?'                 => 'index.php?ap_page=user',
//     );

//     return $new_rules + $rules;
// }
// anspress()->add_action('ap_rewrites', $this, 'rewrite_rules', 10, 3);
