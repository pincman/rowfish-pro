<?php if (!defined('ABSPATH')) {
    die;
}
/**
 * 侧边栏分类文章展示
 */
CSF::createWidget('pm_post_item', array(
    'title'       => esc_html__('PM : 文章侧边展示', 'rizhuti-v2'),
    'classname'   => 'rizhuti_v2-widget-post',
    'description' => esc_html__('Displays a 文章展示', 'rizhuti-v2'),
    'fields'      => array(
        array(
            'id'    => 'title',
            'type'  => 'text',
            'title' => esc_html__('标题', 'rizhuti-v2'),
        ),
        array(
            'id'      => 'is_media',
            'type'    => 'switcher',
            'title'   => esc_html__('显示缩略图', 'rizhuti-v2'),
            'default' => true,
        ),
        array(
            'id'      => 'is_one_maxbg',
            'type'    => 'switcher',
            'title'   => esc_html__('第一篇文章大图', 'rizhuti-v2'),
            'default' => false,
        ),
        array(
            'id'          => 'category',
            'type'        => 'select',
            'title'       => esc_html__('只显示分类下', 'rizhuti-v2'),
            'placeholder' => esc_html__('最新文章', 'rizhuti-v2'),
            'options'     => 'categories',
            'desc'     => esc_html__('不选择分类则显示为最新文章，建议没有特殊需求选择其他分类文章展示，有利于seo爬虫爬取', 'rizhuti-v2'),
        ),
        array(
            'id'      => 'orderby',
            'type'    => 'select',
            'title'   => esc_html__('排序方式', 'rizhuti-v2'),
            'options' => array(
                'date'          => esc_html__('日期', 'rizhuti-v2'),
                'rand'          => esc_html__('随机', 'rizhuti-v2'),
                'comment_count' => esc_html__('评论数', 'rizhuti-v2'),
                'views'         => esc_html__('阅读量', 'rizhuti-v2'),
                'modified'      => esc_html__('最近编辑时间', 'rizhuti-v2'),
                'title'         => esc_html__('标题', 'rizhuti-v2'),
                'ID'            => esc_html__('文章ID', 'rizhuti-v2'),
            ),
        ),

        array(
            'id'      => 'count',
            'type'    => 'text',
            'title'   => esc_html__('文章数量', 'rizhuti-v2'),
            'default' => 4,
        ),
        array(
            'id'      => 'offset',
            'type'    => 'text',
            'title'   => esc_html__('第几页', 'rizhuti-v2'),
            'default' => 0,
        ),

    ),
));
if (!function_exists('pm_post_item')) {
    function pm_post_item($args, $instance)
    {
        if (is_page_template_modular()) {
            return false;
        } //非模块页面不显示

        $instance = array_merge(array(
            'is_media' => true,
            'is_one_maxbg' => false,
            'category' => 0,
            'orderby' => 'date',
            'count' => 4,
            'offset' => 0,
        ), $instance);


        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . $instance['title'] . $args['after_title'];
        }
        // 查询
        $_args = array(
            'cat'                 => (int)$instance['category'],
            'ignore_sticky_posts' => true,
            'post_status'         => 'publish',
            'posts_per_page'      => (int)$instance['count'],
            'paged'              => (int)$instance['offset'],
            'orderby'             => $instance['orderby'],
        );
        if ($instance['orderby'] == 'views') {
            $_args['meta_key'] = '_views';
            $_args['orderby'] = 'meta_value_num';
            $_args['order'] = 'DESC';
        }

        $PostData = new WP_Query($_args);
        $i = 0;

        ob_start(); ?>
        <div class="posts-wrapper list">
            <?php while ($PostData->have_posts()) : $PostData->the_post();
                $i++;
                $maxbg = ($i == 1 && !empty($instance['is_one_maxbg'])) ? ' maxbg' : '';
            ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('post post-list' . $maxbg); ?>>
                    <?php if (!empty($instance['is_media'])) {
                        echo pm_get_post_media(null, 'thumbnail');
                    } ?>
                    <div class="entry-wrapper">
                        <header class="">
                            <?php rizhuti_v2_entry_title(array('link' => true)); ?>
                        </header>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
<?php wp_reset_postdata();
        echo ob_get_clean();
        echo $args['after_widget'];
    }
}
