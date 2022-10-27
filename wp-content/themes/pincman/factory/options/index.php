<?php
require __DIR__ . '/admin.php';
require __DIR__ . '/taxonomy.php';
require __DIR__ . '/post.php';
require __DIR__ . '/doc.php';
function pm_create_options($type, $data)
{
    if ($type == 'admin') {
        CSF::createSection($data['hook'], array_merge($data['params'], ['fields' => $data['fields']]));
    } else if ($type == 'taxonomy') {
        CSF::createTaxonomyOptions($data['hook'], $data['params']);
        CSF::createSection($data['hook'], ['fields' => $data['fields']]);
    } else if ($type == 'docs') {
        CSF::createMetabox($data['hook'], $data['params']);
        CSF::createSection($data['hook'], array(
            'fields' => $data['fields']
        ));
    } else if ($type == 'post' && !apply_filters('is_site_shop', empty(_cao('is_site_shop', '1')))) {
        $question_cat_query = new WP_Term_Query([
            'taxonomy' => 'question_category',
            'orderby'                => 'name',
            'order'                  => 'ASC',
            'hide_empty'             => false,
        ]);
        $question_categories = [];
        foreach ($question_cat_query->get_terms() as $key => $item) {
            $question_categories[$item->term_id] = $item->name;
        }
        $cate_index = array_search('wppay_course_question', array_map(function ($arr) {
            return $arr['id'] ?? '';
        }, $data['fields']));
        if (count($question_categories)) {
            $data['fields'][$cate_index]['options'] = $question_categories;
        } else {
            unset($data['fields'][$cate_index]);
        }
        $document_query  = new WP_Query([
            'post_type'      => 'docs',
            'posts_per_page' => -1, // phpcs:ignore
            'post_parent'    => 0,
            'orderby'        => [
                'menu_order' => 'ASC',
                'date'       => 'DESC',
            ],
            'meta_query' => [[
                'key' =>  'course', 'compare' => '=', 'value' => 1, 'type' => 'NUMERIC',
            ]]
        ]);
        $post = get_current_post_for_box();
        $child_docs = [];
        if ($post) {
            $current_doc = get_post_meta($post->ID, 'wppay_course_document', true);
            if ($current_doc) {
                $children_query = new WP_Query(
                    array(
                        'post_type'      => 'docs',
                        'posts_per_page' => -1, // phpcs:ignore
                        'post_parent'    => $current_doc,
                        'orderby'        => array(
                            'menu_order' => 'ASC',
                            'date'       => 'DESC',
                        ),
                    )
                );
                if ($children_query->have_posts()) {
                    foreach ($children_query->get_posts() as $child_doc) {
                        $child_docs[$child_doc->ID] = $child_doc->post_title;
                    }
                }
            }
        }
        if (count($child_docs) > 0) {
            foreach ($data['fields'] as $index => $value) {
                if (isset($value['fields']) && in_array('video', array_column($value['fields'], 'id'))) {
                    $fields = $value['fields'];
                    array_push($fields, [
                        'id'      => 'doc',
                        'type'    => 'select',
                        'title'   => '关联的文档',
                        'placeholder' => '选择文档',
                        'desc'   => '需要先选择教程关联文档保存后方可选择',
                        'inline'  => true,
                        'options'     => $child_docs,
                        'default' => null,
                    ]);
                    $value['fields'] = $fields;
                    $data['fields'][$index] = $value;
                }
            }
        }
        $docs = [];

        if ($document_query->have_posts()) {
            while ($document_query->have_posts()) {
                $document_query->the_post();
                $docs[get_the_ID()] = get_the_title();
            }
        }
        $doc_index = array_search('wppay_course_document', array_map(function ($arr) {
            return $arr['id'] ?? '';
        }, $data['fields']));
        if (count($docs)) {
            $data['fields'][$doc_index]['options'] = $docs;
        } else {
            unset($data['fields'][$doc_index]);
        }
        CSF::createMetabox($data['hook'], $data['params']);
        CSF::createSection($data['hook'], array(
            'fields' => $data['fields']
        ));
    }
}
pm_create_options('docs', get_doc_options());
pm_create_options('admin', get_admin_options());
pm_create_options('post', get_post_options());
pm_create_options('taxonomy', get_taxonomy_options());
