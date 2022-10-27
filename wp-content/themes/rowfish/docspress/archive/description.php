<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 05:29:50 +0800
 * @Path           : /wp-content/themes/rowfish/docspress/archive/description.php
 * @Description    : 覆盖文档内容模块
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */


if (!defined('ABSPATH')) {
    exit;
}

if (docspress()->get_docs_page_content()) : ?>
    <div class="docspress-archive-description">
        <?php
        // phpcs:ignore
        echo docspress()->get_docs_page_content();
        ?>
    </div>
<?php
endif;
