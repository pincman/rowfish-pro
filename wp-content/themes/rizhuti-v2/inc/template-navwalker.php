<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * 自定义高级菜单
 */
class rizhuti_v2_Walker_Nav_Menu extends Walker_Nav_Menu {

    public $tree_type = array('post_type', 'taxonomy', 'custom');
    public $db_fields = array('parent' => 'menu_item_parent', 'id' => 'db_id');
    protected $mega;

    public function __construct($mega = false) {
        $this->mega = $mega;
    }

    public function start_lvl(&$output, $depth = 0, $args = array()) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"sub-menu\">\n";
    }

    public function end_lvl(&$output, $depth = 0, $args = array()) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }

    public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {


        $indent = ($depth) ? str_repeat("\t", $depth) : '';

        $classes   = empty($item->classes) ? array() : (array) $item->classes;
        // $classes[] = 'menu-item-' . $item->ID;

        $is_mega_nav = get_post_meta($item->ID, 'is_mega_nav', true);
        
        if ($depth == 0 && $this->mega && !empty($is_mega_nav) ){
            if ($item->object == 'category' || $item->object == 'post_tag') {
                $classes[] = 'menu-item-has-children';
                $classes[] = 'menu-item-mega';
            }
        }

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        // $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth);
        // $id = $id ? ' id="' . esc_attr($id) . '"' : '';
        $id = '';
        $output .= $indent . '<li' . $id . $class_names . '>';

        $atts           = array();
        $atts['title']  = !empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = !empty($item->target) ? $item->target : '';
        $atts['rel']    = !empty($item->xfn) ? $item->xfn : '';
        $atts['href']   = !empty($item->url) ? $item->url : '';

        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }
        $nav_icon = get_post_meta($item->ID, 'nav_icon', true);
        if( ! empty( $nav_icon ) ) {
            $item->title = '<i class="'. $nav_icon .'"></i>' . $item->title;
        }
        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;
        // 高级菜单
        if ($depth == 0 && $this->mega && ($item->object == 'category' || $item->object == 'post_tag') && !empty($is_mega_nav)) {
            $term_id   = $item->object_id;
            $term_args = array('posts_per_page' => 8);
            switch ($item->object) {
            case 'category':
                $term_args['cat'] = $term_id;
                break;
            case 'post_tag':
                $term_args['tag_id'] = $term_id;
                break;
            }
            $term_posts = new WP_Query($term_args);

            $item_output .= '<div class="mega-menu">';

            if ($term_posts->have_posts()) {
                $item_output .= '<div class="menu-posts owl">';
                while ($term_posts->have_posts()): $term_posts->the_post();
                    $item_output .= '<div class="menu-post">';
                    ob_start();
                    echo _get_post_media(null,'thumbnail');
                    rizhuti_v2_entry_title(array( 'link' => true ));
                    $item_output .= ob_get_clean();
                    $item_output .= '</div>';
                endwhile;
                $item_output .= '</div>';
            }

            wp_reset_postdata();

            $item_output .= '</div>';
        }
        // 高级菜单END

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    public function end_el(&$output, $item, $depth = 0, $args = array()) {
        $output .= "</li>\n";
    }

    public function display_element($element, &$children_elements, $max_depth, $depth = 0, $args, &$output) {
        $id_field = $this->db_fields['id'];
        if (is_object($args[0])) {
            $args[0]->has_children = !empty($children_elements[$element->$id_field]);
        }
        return parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
    }

    public static function fallback($args) {
        extract($args);

        $fb_output = null;

        if ($container) {
            $fb_output = '<' . $container;

            if ($container_id) {
                $fb_output .= ' id="' . $container_id . '"';
            }

            if ($container_class) {
                $fb_output .= ' class="' . $container_class . '"';
            }

            $fb_output .= '>';
        }

        $fb_output .= '<ul';

        if ($menu_id) {
            $fb_output .= ' id="' . $menu_id . '"';
        }

        if ($menu_class) {
            $fb_output .= ' class="' . $menu_class . '"';
        }

        $fb_output .= '>';
        $fb_output .= '<li class="menu-item"><a href="' . esc_url(admin_url('nav-menus.php')) . '">' . esc_html__('添加菜单', 'rizhuti-v2') . '</a></li>';
        $fb_output .= '</ul>';

        if ($container) {
            $fb_output .= '</' . $container . '>';
        }

        echo wp_kses($fb_output, array(
            'ul'   => array('id' => array(), 'class' => array()),
            'li'   => array('class' => array()),
            'a'    => array('href' => array()),
            'span' => array(),
        ));
    }

}


class Rizhuti_V2_Walker_Comment extends Walker_Comment {
    public function start_lvl( &$output, $depth = 0, $args = array() ) {
        $GLOBALS['comment_depth'] = $depth + 1;
        if($depth>0) return $output;

        switch ( $args['style'] ) {
            case 'div':
                break;
            case 'ol':
                $output .= '<ol class="comment-children">' . "\n";
                break;
            case 'ul':
            default:
                $output .= '<ul class="comment-children">' . "\n";
                break;
        }
    }
    public function end_lvl( &$output, $depth = 0, $args = array() ) {
        $GLOBALS['comment_depth'] = $depth + 1;
        if($depth>0) return $output;

        switch ( $args['style'] ) {
            case 'div':
                break;
            case 'ol':
                $output .= "</ol>\n";
                break;
            case 'ul':
            default:
                $output .= "</ul>\n";
                break;
        }
    }

    public function html5_comment( $comment, $depth, $args ) {
        $GLOBALS['comment'] = $comment;

        if ( 'div' == $args['style'] ) {
            $tag = 'div';
            $add_below = 'comment';
        } else {
            $tag = 'li';
            $add_below = 'div-comment';
        }
        $author = get_comment_author();
        $reply = '';
        if($depth>0 && $comment->comment_parent){
            $reply = get_comment_author($comment->comment_parent);
            $reply = $reply ? esc_html__(' @ ','rizhuti-v2') . '<a href="#comment-' . $comment->comment_parent.'">'.$reply.'</a>' : '';
        }
        if( $comment->user_id){
            $author = '<a>'.$author.'</a>';
        }else if( $comment->comment_author_url ){
            $author = '<a href="'.esc_url($comment->comment_author_url).'" target="_blank" rel="nofollow">'.$author.'</a>';
        }
        
        ?>
        <<?php echo $tag ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ) ?> id="comment-<?php comment_ID() ?>">
        
        <div id="div-comment-<?php comment_ID() ?>" class="comment-inner">
            <div class="comment-author vcard">
                <?php if ( $args['avatar_size'] != 0 ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
            </div>
            <div class="comment-body">
                <div class="nickname"><?php echo $author.$reply;?>
                    <span class="comment-time"><?php echo get_comment_date().' '.get_comment_time(); ?></span>
                </div>
                <?php if ( $comment->comment_approved == '0' ) : ?>
                    <div class="comment-awaiting-moderation"><?php _e( '您的评论正在等待审核。', 'riplus' ); ?></div>
                <?php endif; ?>
                <div class="comment-text"><?php comment_text(); ?></div>
            </div>

            <div class="reply">
                <?php comment_reply_link( array_merge( $args, array( 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
            </div>
        </div>
        <?php
    }
}