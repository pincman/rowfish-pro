<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 05:25:42 +0800
 * @Path           : /wp-content/themes/rowfish/factory/helpers/plugin.php
 * @Description    : 插件相关函数
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */

if (!function_exists('isEditorMD')) {
    /**
     * 判断是否安装wp-editorMD插件
     * 此插件用于编写markdown
     * @return bool
     */
    function isEditorMD()
    {
        return is_plugin_active('wp-editormd/wp-editormd.php');
    }
}

if (!function_exists('isDocsPress')) {
    /**
     * 判断是否已安装docpress插件
     * 此插件用于构建文档系统
     * @return bool
     */
    function isDocsPress()
    {
        return is_plugin_active('docspress/docspress.php');
    }
}

if (!function_exists('isAnsPress')) {
    /**
     * 判断是否已安装anspress-question-answer插件
     * 此插件用于构建问答系统
     * @return bool
     */
    function isAnsPress()
    {
        return is_plugin_active('anspress-question-answer/anspress-question-answer.php');
    }
}

if (!function_exists('isPostTypesOrder')) {
    /**
     * 判断是否已安装post-types-order插件
     * 此插件用于文章手动排序
     * @return bool
     */
    function isPostTypesOrder()
    {
        return is_plugin_active('post-types-order/post-types-order.php');
    }
}
