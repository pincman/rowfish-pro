<?php if (!defined('ABSPATH')) {
    die;
} // Cannot access directly.
/**
 *  添加:
 * 
 * 一个用于开源项目文章的小工具
 */
if (apply_filters('is_site_shop', empty(_cao('is_site_shop', '1')))) {
    return;
}

//付费下载小工具
CSF::createWidget('pm_oss', array(
    'title'       => esc_html__('PM: 开源推荐组件', 'pincman'),
    'classname'   => 'rizhuti_v2-widget-shop-down',
    'description' => esc_html__('用于开源项目推荐页面', 'pincman'),
    'fields'      => array(),
));


/**
 * 付费下载小工具
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:15:14+0800
 * @param    [type]                   $args     [description]
 * @param    [type]                   $instance [description]
 * @return   [type]                             [description]
 */
if (!function_exists('pm_oss')) {
    function pm_oss($args, $instance)
    {
        $oss_info = pm_oss_post_info();
        if (!is_single() || $oss_info['wppay_type'] !== '7') {
            return;
        }

        echo $args['before_widget'];
        if ($oss_info['wppay_type'] === '7') {
            $title_text = __('<small>项目信息</small>', 'rizhuti-v2');
        }

        //显示价格信息
        echo '<div class="price"><h3>' . $title_text . '</h3></div>';
        echo '<div class="down-info">';
        echo '<ul class="infos">';
        echo '<li><p class="data-label">项目名称</p><p class="info">' . $oss_info['wppay_oss_name'] . '</p></li>';
        if (!empty($oss_info['wppay_oss_website'])) {
            echo '<li><p class="data-label">' . esc_html__('主页文档', 'rizhuti-v2') . '</p><p class="info"><a target="_blank" rel="nofollow noopener noreferrer" href="' . $oss_info['wppay_oss_website'] . '" class="badge badge-secondary-lighten"><i class="fas fa-link"></i> ' . esc_html__('点击查看', 'rizhuti-v2') . '</a></p></li>';
        }
        if (!empty($oss_info['wppay_oss_demourl'])) {
            echo '<li><p class="data-label">' . esc_html__('演示地址', 'rizhuti-v2') . '</p><p class="info"><a target="_blank" rel="nofollow noopener noreferrer" href="' . $oss_info['wppay_oss_demourl'] . '" class="badge badge-secondary-lighten"><i class="fas fa-link"></i> ' . esc_html__('点击查看', 'rizhuti-v2') . '</a></p></li>';
        }
        if (!empty($oss_info['wppay_oss_agreement'])) {
            echo '<li><p class="data-label">开源协议</p><p class="info">' . $oss_info['wppay_oss_agreement'] . '</p></li>';
        }
        echo '</ul>';
        echo '</div>';
        $buttons = [
            // [
            //     'text' => '主页/文档', 'value' => $oss_info['wppay_oss_website']
            // ],
            ['text' => '访问仓库', 'value' => $oss_info['wppay_oss_git']],
            // ['text' => '访问演示', 'value' => $oss_info['wppay_oss_demourl']]
        ];
        $btns = '';
        foreach ($buttons as $key => $item) {
            if (!empty($item['value'])) {
                $btns .= '<div class="mt-1 btn-group btn-block" role="group" >';
            }
            $btns .=
                '<div class="mt-1 btn-group btn-block" role="group">
            <a target="_blank" href="' . $item['value'] . '" class="btn btn-light">
            <i class="fa fa-github" style="padding-right: 10px;"></i> ' . $item['text'] .
                '</a></div></div>';
        }
        if (!empty($btns)) echo $btns;
        ////其他信息
        if (!empty($shop_info['wppay_info'])) {
            echo '<div class="down-info">';
            echo '<h5>' . esc_html__('其他信息', 'rizhuti-v2') . '</h5>';
            echo '<ul class="infos">';
            // if (!empty($shop_info['wppay_demourl'])) {
            //     echo '<li><p class="data-label">' . esc_html__('链接', 'rizhuti-v2') . '</p><p class="info"><a target="_blank" rel="nofollow noopener noreferrer" href="' . $shop_info['wppay_demourl'] . '" class="badge badge-secondary-lighten"><i class="fas fa-link"></i> ' . esc_html__('点击查看', 'rizhuti-v2') . '</a></p></li>';
            // }
            foreach ($shop_info['wppay_info'] as $key => $value) {
                echo '<li><p class="data-label">' . $value['title'] . '</p><p class="info">' . $value['desc'] . '</p></li>';
            }
            echo '</ul>';
            echo '</div>';
        }

        if (!empty($instance['desc'])) {
            echo '<div class="mt-2 down-help small text-muted">' . $instance['desc'] . '</div>';
        }

        echo $args['after_widget'];
    }
}


// Shop Widget Options