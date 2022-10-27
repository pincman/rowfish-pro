<?php if (!defined('ABSPATH')) {die;} // Cannot access directly.


$prefix = '_prefix_menu_options';

CSF::createNavMenuOptions($prefix, array(
    'data_type' => 'unserialize',
));

CSF::createSection($prefix, array(
    'fields' => array(
        array(
            'id'    => 'nav_icon',
            'type'  => 'icon',
            'title' => esc_html__('菜单图标', 'rizhuti-v2'),
        ),
        array(
            'id'    => 'is_mega_nav',
            'type'  => 'switcher',
            'title' => esc_html__('启用高级菜单文章', 'rizhuti-v2'),
            'label' => esc_html__('只在分类或者标签菜单下有效，只支持一级菜单开启', 'rizhuti-v2'),
        ),

    ),
));
