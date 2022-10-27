<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package rizhuti-v2
 */


if ( post_password_required() || !comments_open() || !is_site_comments() ) {
	return;
}

?>

<div id="comments" class="entry-comments">
    <?php
    $login_url = wp_login_url(curPageURL());
    $fields =  array(
        'author' => '<div class="comment-form-author"><input id="author" name="author" type="text" placeholder="'.__('*昵称: ', 'rizhuti-v2').'" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"'.( $req ? ' class="required"' : '' ).'></div>',
        'email'  => '<div class="comment-form-email"><input id="email" name="email" type="text" placeholder="'.__('*邮箱: ', 'rizhuti-v2').'" value="' . esc_attr(  $commenter['comment_author_email'] ) . '"'.( $req ? ' class="required"' : '' ).'></div>',
        'url'  => '<div class="comment-form-url"><input id="url" name="url" type="text" placeholder="'.__('网址: ', 'rizhuti-v2').'" value="' . esc_attr(  $commenter['comment_author_url'] ) . '" size="30"></div>',
        'cookies' => '<div class="comment-form-cookies-consent"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes" checked="checked" > ' . __( '浏览器会保存昵称、邮箱和网站cookies信息，下次评论时使用。', 'rizhuti-v2' ) . '</div>'
    );
    $formsubmittext = '';
    if(is_user_logged_in()) {
        $user = wp_get_current_user();
        $user_identity = $user->exists() ? $user->display_name : '';
        $user_type=_get_user_vip_type($user->ID);
        $formsubmittext = '<div class="float-left form-submit-text">'.get_avatar( $user->ID, 50, '', $user_identity ).'<span>'.$user_identity.'</span></div>';
    }

    comment_form( array(
        'title_reply_before' => '<h3 id="reply-title" class="comment-reply-title">',
        'title_reply_after'  => '</h3>',
        'fields' => apply_filters( 'comment_form_default_fields', $fields ),
        'comment_field' =>  '<div class="comment-form-comment"><textarea id="comment" name="comment" class="required" rows="4" placeholder="请输入评论内容..."></textarea></div>',
        'must_log_in' => '<div class="comment-form"><div class="comment-must-login">' . __( '您需要登录后才可以发表评论...', 'rizhuti-v2' ) . '</div><div class="form-submit"><div class="form-submit-text float-left"><a href="'.$login_url.'">' . __( '登录...', 'rizhuti-v2' ) . '</a> ' . __( '后才能评论', 'rizhuti-v2' ) . '</div> <input name="submit" type="submit" id="must-submit" class="submit disabled" value="' . __( '发表', 'rizhuti-v2' ) . '" disabled></div></div>',
        'logged_in_as' => '',
        'submit_field' => '<div class="form-submit">'.$formsubmittext.'%1$s %2$s</div>',
        'label_submit' => __( '提交', 'rizhuti-v2' ),
        'format' => 'html5'
    ) );
    ?>
	<?php if ( have_comments() ) : ?>
		<h3 class="comments-title">
			<?php
			$comments_number = get_comments_number();
			printf(__('评论(%s)', 'rizhuti-v2'), number_format_i18n( $comments_number ));
			?>
		</h3>

		<ul class="comments-list">
			<?php
            wp_list_comments( array(
                'walker' => new Rizhuti_V2_Walker_Comment,
                'style'       => 'ul',
                'short_ping'  => true,
                'type'        => 'comment',
                'avatar_size' => '60',
                'format'    => 'html5'
            ) );
			?>
		</ul>
        <div class="pagination m-0">
            <?php paginate_comments_links(array('prev_text'=>__('<i class="mdi mdi-chevron-left"></i> 上一页', 'rizhuti-v2'),'next_text'=>__('下一页 <i class="mdi mdi-chevron-right"></i>', 'rizhuti-v2')));?>
        </div>
	<?php endif;?>
</div>
