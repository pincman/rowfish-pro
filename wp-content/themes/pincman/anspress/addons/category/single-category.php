<?php

/**
 * Display single question category page.
 *
 * Display category page.
 *
 * @link        http://anspress.net
 * @since       4.0
 * @package     AnsPress
 * @subpackage  Templates
 * @since       4.1.1 Renamed file from category.php to single-category.php.
 */
function pm_ap_sub_category_list($parent)
{
    $classes = ['primary', 'secondary', 'success', 'danger', 'warning'];
    $categories = get_terms(
        array('taxonomy' => 'question_category'),
        array(
            'parent'     => $parent,
            'hide_empty' => false,
        )
    );

    if ($categories) {
        echo '<ul class="ap-category-subitems ap-ul-inline clearfix">';
        foreach ($categories as $cat) {
            if (!isset($tmp) || count($tmp) < 1) $tmp = $classes;
            $current_class =  $classes[rand(0, count($classes) - 1)];
            unset($tmp[array_search($current_class, $tmp)]);
            echo '<li><a class="' . $current_class . '" href="' . get_category_link($cat) . '"><i class="dot"></i>' . $cat->name . '(' . $cat->count . ')</a></li>';
        }
        echo '</ul>';
    }
}
$icon = ap_get_category_icon($question_category->term_id);
?>

<?php dynamic_sidebar('ap-top'); ?>

<div class="ap-row">
    <div id="ap-category" class="<?php echo is_active_sidebar('ap-category') && is_anspress() ? 'ap-col-9' : 'ap-col-12'; ?>">

        <?php if (ap_category_have_image($question_category->term_id)) : ?>
            <div class="ap-category-feat" style="height: 300px;">
                <?php ap_category_image($question_category->term_id, 300); ?>
            </div>
        <?php endif; ?>

        <div class="ap-taxo-detail">
            <?php if (!empty($icon)) : ?>
                <div class="ap-pull-left">
                    <?php ap_category_icon($question_category->term_id); ?>
                </div>
            <?php endif; ?>

            <div class="no-overflow">
                <div>
                    <a class="entry-title" href="<?php echo get_category_link($question_category); ?>">
                        <?php echo esc_html($question_category->name); ?>
                    </a>
                    <span class="ap-tax-count">
                        <?php
                        printf(
                            _n('%d Question', '%d Questions', (int) $question_category->count, 'anspress-question-answer'),
                            (int) $question_category->count
                        );
                        ?>
                    </span>
                </div>


                <?php if ('' !== $question_category->description) : ?>
                    <p class="ap-taxo-description">
                        <?php echo wp_kses_post($question_category->description); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div><!-- close .ap-taxo-detail -->
        <?php
        $sub_cat_count = count(get_term_children($question_category->term_id, 'question_category'));

        if ($sub_cat_count > 0) {
            echo '<div class="ap-term-sub">';
            // echo '<div class="sub-taxo-label">' . $sub_cat_count . ' ' . __('Sub Categories', 'anspress-question-answer') . '</div>';
            pm_ap_sub_category_list($question_category->term_id);
            echo '</div>';
        }
        ?>
        <?php ap_get_template_part('question-list'); ?>


    </div><!-- close #ap-lists -->

    <?php if (is_active_sidebar('ap-category') && is_anspress()) { ?>
        <div class="ap-question-right ap-col-3">
            <?php dynamic_sidebar('ap-category'); ?>
        </div>
    <?php } ?>
</div><!-- close .row -->