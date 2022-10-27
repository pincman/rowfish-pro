<?php
/**
 * Docs archive title template
 *
 * This template can be overridden by copying it to yourtheme/docspress/archive/title.php.
 *
 * @author  nK
 * @package docspress/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>

<header class="page-header">
    <h1 class="page-title">
        <?php
        // phpcs:ignore
        echo docspress()->get_docs_page_title();
        ?>
    </h1>
</header><!-- .page-header -->
