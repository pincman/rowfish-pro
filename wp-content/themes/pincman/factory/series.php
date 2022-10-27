<?php
/*
 * @Author: Pincman
 * @created_at: 2021-05-21 05:42:34
 * @updated_at: 2021-05-21 06:11:18
 * @description: 专题文章函数
 * @homepage: https://pincman.cn
 */

/**
 *  获取当前文章所属的专题
 * 
 * @param int $num 
 * @return void 
 */
function pm_serie_dot($num = 2)
{
    global $post;
    $post_ID = $post->ID;
    $series = get_the_terms($post_ID, 'series');
    $i = 0;
    if ($series && count($series) > 0) {
        echo '<span class="meta-serie-dot">';

        foreach ($series as $v) {
            $i++;
            if ($i > $num) break;
            echo '<a href="' . esc_url(get_term_link($v->term_id)) . '" rel="category">' . esc_html($v->name) . '</a>';
        }

        echo '</span>';
    }
}
