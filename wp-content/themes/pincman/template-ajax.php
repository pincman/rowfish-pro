<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;




/**
 * 夜间模式切换
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:25:38+0800
 * @return   [type]                   [description]
 */
function toggle_dark()
{
    header('Content-type:application/json; Charset=utf-8');
    $is_open_dark = !empty($_POST['dark']) ? $_POST['dark'] : 0;

    if ($is_open_dark == 1 && empty($_SESSION['site_dark_open'])) {
        $_SESSION['site_dark_open'] = 1;
    } else {
        $_SESSION['site_dark_open'] = 0;
    }

    echo json_encode(array('status' => '1', 'msg' => ''));
    exit;
}
add_action('wp_ajax_toggle_dark', 'toggle_dark');
add_action('wp_ajax_nopriv_toggle_dark', 'toggle_dark');


/**
 * 添加文章阅读量
 * @Author   Dadong2g
 * @DateTime 2021-01-25T20:38:13+0800
 */
function add_post_views_num()
{
    header('Content-type:application/json; Charset=utf-8');
    $post_id = !empty($_POST['id']) ? (int) $_POST['id'] : 0;
    if ($post_id && add_post_views($post_id)) {
        echo json_encode(array('status' => '1', 'msg' => 'PostID：' . $post_id . ' views +1'));
        exit;
    } else {
        echo json_encode(array('status' => '0', 'msg' => 'post views error'));
        exit;
    }
}
add_action('wp_ajax_add_post_views_num', 'add_post_views_num');
add_action('wp_ajax_nopriv_add_post_views_num', 'add_post_views_num');



/**
 * 收藏文章
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:25:53+0800
 * @return   [type]                   [description]
 */
function go_fav_post()
{
    header('Content-type:application/json; Charset=utf-8');
    $user_id = get_current_user_id();
    $post_id = !empty($_POST['post_id']) ? (int) $_POST['post_id'] : 0;
    if (!$user_id) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('请登录收藏', 'rizhuti-v2')));
        exit;
    }
    if (is_fav_post($post_id)) {
        // 取消收藏
        del_fav_post($user_id, $post_id);
        echo json_encode(array('status' => '1', 'msg' => esc_html__('已取消收藏', 'rizhuti-v2')));
        exit;
    } else {
        //新收藏
        add_fav_post($user_id, $post_id);
        echo json_encode(array('status' => '1', 'msg' => esc_html__('收藏成功', 'rizhuti-v2')));
        exit;
    }

    exit;
}
add_action('wp_ajax_go_fav_post', 'go_fav_post');
add_action('wp_ajax_nopriv_go_fav_post', 'go_fav_post');





/**
 * 获取海报
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:25:57+0800
 * @return   [type]                   [description]
 */
function get_poster_html()
{
    header('Content-type:application/html; Charset=utf-8');
    global $current_user;
    $post_id   = !empty($_POST['id']) ? esc_sql($_POST['id']) : 0;
    $post    = get_post($post_id);
    if ($current_user->ID > 0) {
        // 生出带参数的推广文章链接
        $afflink = add_query_arg(array('aff' => $current_user->ID), get_the_permalink($post_id));
    } else {
        $afflink = get_the_permalink($post_id);
    }
    if (!$post) {
        exit('参数错误');
    }
    $img_u = pm_get_post_thumbnail_url($post_id);
    $imageInfo = getimagesize($img_u);
    $b64 = base64_encode(file_get_contents($img_u));
    $img_base64 = 'data:' . $imageInfo['mime'] . ';base64,' . $b64;
    echo '<div id="poster-html" class="poster-html">';
    echo '<div class="poster-header">';
    echo '<img src="' . $img_base64 . '">';
    echo '<h2 class="poster-title">' . get_the_title($post_id) . '</h2>';
    echo '</div>';
    echo '<div class="poster-body">';
    echo '<div class="poster-meta">';
    echo '<div class="poster-author">' . get_avatar($post->post_author) . get_the_author_meta('display_name', $post->post_author) . '</div>';
    echo '<div class="poster-data">' . $post->post_date . '</div>';
    echo '</div>';
    echo '<div class="poster-text">' . wp_trim_words(strip_shortcodes($post->post_content), 120, '...') . '</div>';
    echo '</div>';
    echo '<div class="poster-footer">';
    echo '<div class="poster-logo">';
    echo '<img src="' . _cao('single_share_poser_logo') . '">';
    echo '<p>' . _cao('single_share_poser_desc') . '</p>';
    echo '</div>';
    echo '<div class="poster-qrcode">';
    echo '<img src="' . getQrcodeApi($afflink) . '">';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    exit;
}
add_action('wp_ajax_get_poster_html', 'get_poster_html');
add_action('wp_ajax_nopriv_get_poster_html', 'get_poster_html');


/**
 * 用户登录
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:26:06+0800
 * @return   [type]                   [description]
 */
function user_login()
{
    header('Content-type:application/json; Charset=utf-8');
    $username   = !empty($_POST['username']) ? esc_sql($_POST['username']) : null;
    $password   = !empty($_POST['password']) ? esc_sql($_POST['password']) : null;
    $rememberme = !empty($_POST['rememberme']) ? esc_sql($_POST['rememberme']) : null;

    if (!_cao('is_site_user_login', '1')) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('本站已经关闭登录功能', 'rizhuti-v2')));
        exit;
    }

    $login_data = array();

    $login_data['user_login'] = $username;
    $login_data['user_password'] = $password;
    $login_data['remember']      = false;
    if (isset($rememberme) && $rememberme == '1') {
        $login_data['remember'] = true;
    }
    if (!$username || !$password) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('请输入登录账号或密码', 'rizhuti-v2')));
        exit;
    }



    //腾讯安全验证
    if (!qq_captcha_verify()) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('安全验证失败，请刷新页面重试', 'rizhuti-v2')));
        exit;
    } else {
        $_SESSION['is_qq_captcha_verify'] = 0;
    }

    $user_verify = wp_signon($login_data, false);
    if (is_wp_error($user_verify)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('用户名或密码错误', 'rizhuti-v2')));
        exit;
    } else {
        if (!empty(get_user_meta($user_verify->ID, 'is_fuck', true))) {
            wp_logout();
            $mesg = esc_html__('您的账号检测异常，', 'rizhuti-v2') . get_user_meta($user_verify->ID, 'is_fuck_desc', true);
            echo json_encode(array('status' => '0', 'msg' => $mesg));
            exit;
        } else {
            wp_set_current_user($user_verify->ID, $user_verify->user_login);
            wp_set_auth_cookie($user_verify->ID, $login_data['remember']);
            echo json_encode(array('status' => '1', 'msg' => esc_html__('登录成功', 'rizhuti-v2')));
            exit;
        }
    }
    exit();
}
add_action('wp_ajax_nopriv_user_login', 'user_login');

/**
 * 注册新用户
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:26:11+0800
 * @return   [type]                   [description]
 */
function user_register()
{
    if (is_close_site_shop()) {
        exit;
    }
    header('Content-type:application/json; Charset=utf-8');
    $user_email        = isset($_POST['user_email']) ? esc_sql(apply_filters('user_registration_email', $_POST['user_email'])) : null;
    $user_pass         = isset($_POST['user_pass']) ? esc_sql($_POST['user_pass']) : null;
    $user_pass2        = isset($_POST['user_pass2']) ? esc_sql($_POST['user_pass2']) : null;
    $email_verify_code = isset($_POST['email_verify_code']) ? esc_sql($_POST['email_verify_code']) : null;
    if (!_cao('is_site_user_register')) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('本站已经关闭新用户注册', 'rizhuti-v2')));
        exit;
    }

    if (!is_email($user_email)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('邮箱地址错误', 'rizhuti-v2')));
        exit;
    }
    if (email_exists($user_email)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('邮箱已经被注册', 'rizhuti-v2')));
        exit;
    }
    if (strlen($user_pass) < 6) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('密码长度不得小于6位', 'rizhuti-v2')));
        exit;
    }
    if ($user_pass != $user_pass2) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('两次输入的密码不一致', 'rizhuti-v2')));
        exit;
    }



    //腾讯安全验证
    if (!qq_captcha_verify()) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('安全验证失败，请刷新页面重试', 'rizhuti-v2')));
        exit;
    } else {
        $_SESSION['is_qq_captcha_verify'] = 0;
    }

    // 是否需要邮箱验证
    if (!email_captcha_verify($user_email, $email_verify_code)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('邮箱验证码错误', 'rizhuti-v2')));
        exit;
    }

    // 验证通过
    $nweUserData = array(
        'user_login'   => "mail_" . mt_rand(1000, 9999) . mt_rand(1000, 9999),
        'user_email'   => $user_email,
        'display_name' => esc_html__('匿名用户', 'rizhuti-v2'),
        'nickname'     => esc_html__('匿名用户', 'rizhuti-v2'),
        'user_pass'    => $user_pass2,
        'role'         => get_option('default_role'),
    );
    $user_id = wp_insert_user($nweUserData);
    if (is_wp_error($user_id)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('注册信息异常，请刷新页面重试', 'rizhuti-v2')));
        exit;
    } else {
        //登陆老用户
        $user = get_user_by('id', $user_id);
        wp_set_current_user($user->ID, $user->user_login);
        wp_set_auth_cookie($user->ID, true);
        do_action('wp_login', $user->user_login, $user);
        echo json_encode(array('status' => '1', 'msg' => esc_html__('注册成功', 'rizhuti-v2')));
        exit;
    }
    exit();
}
add_action('wp_ajax_nopriv_user_register', 'user_register');

/**
 * 找回密码
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:26:16+0800
 * @return   [type]                   [description]
 */
function user_lostpassword()
{
    if (is_close_site_shop()) {
        exit;
    }
    header('Content-type:application/json; Charset=utf-8');
    $user_info = isset($_POST['user_email']) ? esc_sql($_POST['user_email']) : null;
    $user_info = esc_html__($user_info);
    if (empty($user_info)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('请输入用户名或邮箱', 'rizhuti-v2')));
        exit;
    }

    //腾讯安全验证
    if (!qq_captcha_verify()) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('安全验证失败，请刷新页面重试', 'rizhuti-v2')));
        exit;
    } else {
        $_SESSION['is_qq_captcha_verify'] = 0;
    }
    //处理业务逻辑
    if (strpos($user_info, '@')) {
        $user_data = get_user_by('email', $user_info);
        if (empty($user_data)) {
            echo json_encode(array('status' => '0', 'msg' => esc_html__('该邮箱账号不存在', 'rizhuti-v2')));
            exit;
        }
    } else {
        $user_data = get_user_by('login', $user_info);
        if (empty($user_data)) {
            echo json_encode(array('status' => '0', 'msg' => esc_html__('该用户名不存在', 'rizhuti-v2')));
            exit;
            exit;
        }
    }
    do_action('lostpassword_post');
    // Redefining user_login ensures we return the right case in the email.
    $user_id = $user_data->ID;
    $user_login = $user_data->user_login;
    $user_email = $user_data->user_email;
    $key = get_password_reset_key($user_data);
    if (is_wp_error($key)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('用户账号异常，请刷新页面', 'rizhuti-v2')));
        exit;
    }
    $reset_url = esc_url_raw(
        add_query_arg(
            array(
                'riresetpass' => 'true',
                'rifrp_action' => 'rp',
                'key' => $key,
                'uid' => $user_id
            ),
            wp_lostpassword_url()
        )
    );
    $reset_link = '<a href="' . $reset_url . '">' . $reset_url . '</a>';
    $message = '<br/>';
    $message .= esc_html__('站点名称: ', 'rizhuti-v2') . get_bloginfo('name');
    $message .= '<br/>';
    $message .= esc_html__('账号ID: ', 'rizhuti-v2') . $user_login;
    $message .= '<br/>';
    $message .= esc_html__('要重置您的密码，请打开下面的链接', 'rizhuti-v2');
    $message .= '<br/>';
    $message .= $reset_link;
    $message .= '<br/>';
    $message .= esc_html__('如果不是您本人发送，请忽略本邮件，不会造成任何错误', 'rizhuti-v2') . '<br/>';
    //发送邮箱
    if (_sendMail($user_email, esc_html__('重置密码邮件提醒', 'rizhuti-v2'), $message)) {
        $_SESSION['action_riresetpass_emali'] = 1;
        echo json_encode(array('status' => '1', 'msg' => esc_html__('密码重置链接发送成功，请前往邮箱查看继续', 'rizhuti-v2')));
        exit;
    }
    echo json_encode(array('status' => '0', 'msg' => esc_html__('电子邮件发送失败，请稍后重试', 'rizhuti-v2')));
    exit;
}
add_action('wp_ajax_nopriv_user_lostpassword', 'user_lostpassword');

/**
 * 找回密码
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:26:22+0800
 * @return   [type]                   [description]
 */
function user_set_lostpassword()
{
    if (is_close_site_shop()) {
        exit;
    }
    header('Content-type:application/json; Charset=utf-8');
    $key = sanitize_text_field($_POST['key']);
    $user_id = sanitize_text_field($_POST['uid']);
    $user_pass = isset($_POST['user_pass']) ? trim($_POST['user_pass']) : null;
    $user_pass2 = isset($_POST['user_pass2']) ? trim($_POST['user_pass2']) : null;

    if (empty($key) || empty($user_id) || empty($user_pass) || empty($user_pass2)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('参数错误，请返回重试', 'rizhuti-v2')));
        exit;
    }
    if ($user_pass != $user_pass2) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('两次输入的密码不一致', 'rizhuti-v2')));
        exit;
    }

    //腾讯安全验证
    if (!_cao('is_site_email_captcha_verify') && !qq_captcha_verify()) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('安全验证失败，请刷新页面重试', 'rizhuti-v2')));
        exit;
    } else {
        $_SESSION['is_qq_captcha_verify'] = 0;
    }
    $userdata = get_userdata(absint($user_id));
    $login = $userdata ? $userdata->user_login : '';
    $user = check_password_reset_key($key, $login);
    if (is_wp_error($user)) {
        if ($user->get_error_code() === 'expired_key') {
            echo json_encode(array('status' => '0', 'msg' => esc_html__('重置密码链接已过期，请重新找回密码', 'rizhuti-v2')));
            exit;
        } else {
            echo json_encode(array('status' => '0', 'msg' => esc_html__('重置密码链接无效，请重新找回密码', 'rizhuti-v2')));
            exit;
        }
    }
    // 验证通过 处理业务逻辑
    reset_password($user, $user_pass);
    echo json_encode(array('status' => '1', 'msg' => esc_html__('密码重置成功，请使用新密码登录', 'rizhuti-v2')));
    exit;
}
add_action('wp_ajax_nopriv_user_set_lostpassword', 'user_set_lostpassword');



/**
 * 绑定邮箱
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:26:30+0800
 * @return   [type]                   [description]
 */
function user_bind_email()
{
    if (is_close_site_shop()) {
        exit;
    }
    header('Content-type:application/json; Charset=utf-8');
    global $wpdb, $current_user;
    $user_email        = !empty($_POST['user_email']) ? esc_sql($_POST['user_email']) : null;
    $user_email        = apply_filters('user_registration_email', $user_email);
    $user_email        = $wpdb->_escape(trim($user_email));
    $email_verify_code = !empty($_POST['email_verify_code']) ? esc_sql($_POST['email_verify_code']) : null;

    if (!is_email($user_email)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('邮箱地址错误', 'rizhuti-v2')));
        exit;
    }


    //腾讯安全验证
    if (!_cao('is_site_email_captcha_verify') && !qq_captcha_verify()) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('安全验证失败，请刷新页面重试', 'rizhuti-v2')));
        exit;
    } else {
        $_SESSION['is_qq_captcha_verify'] = 0;
    }

    // 是否需要邮箱验证
    if (!email_captcha_verify($user_email, $email_verify_code)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('邮箱验证码错误', 'rizhuti-v2')));
        exit;
    }

    if (email_exists($user_email)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('邮箱已存在绑定，请更换其他邮箱', 'rizhuti-v2')));
        exit;
    }
    $userdata['ID']         = $current_user->ID;
    $userdata['user_email'] = $user_email;
    if (!wp_update_user($userdata)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('绑定失败，请刷新重试', 'rizhuti-v2')));
        exit;
    }
    echo json_encode(array('status' => '1', 'msg' => esc_html__('绑定成功', 'rizhuti-v2')));
    exit;
}
add_action('wp_ajax_user_bind_email', 'user_bind_email');

/**
 * 第三方登录绑定账号
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:26:38+0800
 * @return   [type]                   [description]
 */
function user_bind_olduser()
{
    if (is_close_site_shop()) {
        exit;
    }
    header('Content-type:application/json; Charset=utf-8');
    global $current_user;
    $username   = !empty($_POST['username']) ? esc_sql($_POST['username']) : null;
    $password   = !empty($_POST['password']) ? esc_sql($_POST['password']) : null;
    if (empty($current_user->ID)) exit;
    if (!$username || !$password) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('请输入登录账号或密码', 'rizhuti-v2')));
        exit;
    }

    //腾讯安全验证
    if (!qq_captcha_verify()) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('安全验证失败，请刷新页面重试', 'rizhuti-v2')));
        exit;
    } else {
        $_SESSION['is_qq_captcha_verify'] = 0;
    }
    // 获取老用户信息
    if (strpos($username, '@')) {
        $old_user_data = get_user_by('email', $username);
    } else {
        $old_user_data = get_user_by('login', $username);
    }

    if (empty($old_user_data)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('登录名或邮箱错误', 'rizhuti-v2')));
        exit;
    }
    if (!wp_check_password($password, $old_user_data->data->user_pass, $old_user_data->data->ID)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('用户密码错误，请重新输入', 'rizhuti-v2')));
        exit;
    }
    //通过验证 处理业务逻辑
    // 获取用户注册方式
    $regType = explode('_', $current_user->user_login);
    $type = (isset($regType[0])) ? $regType[0] : '';
    $old_openid = get_user_meta($old_user_data->data->ID, 'open_' . $type . '_openid', 1);
    $new_openid = get_user_meta($current_user->ID, 'open_' . $type . '_openid', 1);
    if (empty($type) || empty($new_openid) || !empty($old_openid)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('该账号已绑定过，请更换账号', 'rizhuti-v2')));
        exit;
    }
    //账号没有绑定过 更新用户openid字段
    $snsInfo = [
        'openid' => $new_openid,
        'unionid' => get_user_meta($current_user->ID, 'open_' . $type . '_unionid', 1),
        'nick' => get_user_meta($current_user->ID, 'open_' . $type . '_name', 1),
        'avatar' => get_user_meta($current_user->ID, 'open_' . $type . '_avatar', 1),
    ];
    $RiPlusSNS = new RiPlusSNS;
    $RiPlusSNS->updete_oauth_info($old_user_data->data->ID, $type, $snsInfo);
    //删除当前用户
    if ($current_user->ID != 1) {
        require_once(ABSPATH . 'wp-admin/includes/user.php');
        wp_delete_user($current_user->ID);
    }

    //登陆老用户
    $user = get_user_by('id', $old_user_data->data->ID);
    wp_set_current_user($user->ID, $user->user_login);
    wp_set_auth_cookie($user->ID, true);
    do_action('wp_login', $user->user_login, $user);

    echo json_encode(array('status' => '1', 'msg' => esc_html__('绑定成功，已自动登录绑定账号', 'rizhuti-v2')));
    exit;
}
add_action('wp_ajax_user_bind_olduser', 'user_bind_olduser');


/**
 * 发送邮箱验证码
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:26:57+0800
 * @return   [type]                   [description]
 */
function send_email_verify_code()
{
    if (is_close_site_shop()) {
        exit;
    }
    header('Content-type:application/json; Charset=utf-8');
    global $wpdb;
    $user_email = !empty($_POST['user_email']) ? esc_sql($_POST['user_email']) : null;
    $user_email = apply_filters('user_registration_email', $user_email);
    $user_email = $wpdb->_escape(trim($user_email));

    //腾讯安全验证
    if (!qq_captcha_verify()) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('安全验证失败，请刷新页面重试', 'rizhuti-v2')));
        exit;
    } else {
        $_SESSION['is_qq_captcha_verify'] = 0;
    }

    if (email_exists($user_email)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('邮箱已存在', 'rizhuti-v2')));
        exit;
    } else {
        $send_email = set_verify_email_code($user_email);
        if ($send_email) {
            echo json_encode(array('status' => '1', 'msg' => esc_html__('发送成功', 'rizhuti-v2')));
            exit;
        } else {
            echo json_encode(array('status' => '0', 'msg' => esc_html__('发送失败', 'rizhuti-v2')));
            exit;
        }
    }
}
add_action('wp_ajax_send_email_verify_code', 'send_email_verify_code');
add_action('wp_ajax_nopriv_send_email_verify_code', 'send_email_verify_code');



/**
 * 保存个人信息
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:27:06+0800
 * @return   [type]                   [description]
 */
function seav_userinfo()
{
    if (is_close_site_shop()) {
        exit;
    }
    header('Content-type:application/json; Charset=utf-8');
    global $current_user;
    $user_id = $current_user->ID;
    $nonce = !empty($_POST['nonce']) ? $_POST['nonce'] : null;
    $qq = !empty($_POST['qq']) ? esc_sql($_POST['qq']) : null;
    $description = !empty($_POST['description']) ? sanitize_text_field($_POST['description']) : null;
    if ($nonce && !wp_verify_nonce($nonce, 'rizhuti-v2-click-' . $user_id)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('非法请求，请刷新重试', 'rizhuti-v2')));
        exit;
    }
    $userdata = [];
    $userdata['ID'] = $user_id;
    $userdata['nickname'] = !empty($_POST['nickname']) ? sanitize_text_field($_POST['nickname']) : esc_html__('匿名用户', 'rizhuti-v2');
    $userdata['display_name'] = $userdata['nickname'];
    if (wp_update_user($userdata)) {
        if ($qq && is_numeric($qq)) {
            update_user_meta($user_id, 'qq', $qq);
        }
        if ($description) {
            update_user_meta($user_id, 'description', $description);
        }
        echo json_encode(array('status' => '1', 'msg' => esc_html__('保存成功', 'rizhuti-v2')));
        exit;
    }
    echo json_encode(array('status' => '0', 'msg' => esc_html__('保存失败，请刷新重试', 'rizhuti-v2')));
    exit;
}
add_action('wp_ajax_seav_userinfo', 'seav_userinfo');


/**
 * 解绑开放登录
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:27:11+0800
 * @return   [type]                   [description]
 */
function unset_open_login()
{
    if (is_close_site_shop()) {
        exit;
    }
    header('Content-type:application/json; Charset=utf-8');
    global $current_user;
    $user_id   = $current_user->ID;
    $type = !empty($_POST['type']) ? $_POST['type'] : null;
    if (!$type || !in_array($type, array('qq', 'weixin', 'mpweixin', 'weibo'))) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('解绑类型错误，请刷新重试', 'rizhuti-v2')));
        exit;
    }
    if ($user_id && $type) {
        update_user_meta($user_id, 'open_' . $type . '_openid', '');
        update_user_meta($user_id, 'open_' . $type . '_unionid', '');
        update_user_meta($user_id, 'open_' . $type . '_bind', '');
        update_user_meta($user_id, 'open_' . $type . '_name', '');
        update_user_meta($user_id, 'open_' . $type . '_avatar', '');
        echo json_encode(array('status' => '1', 'msg' => esc_html__('解绑成功', 'rizhuti-v2')));
        exit;
    }
}
add_action('wp_ajax_unset_open_login', 'unset_open_login');



/**
 * 提交工单
 * @Author   Dadong2g
 * @DateTime 2021-01-24T16:25:58+0800
 * @return   [type]                   [description]
 */
function send_new_tickets()
{
    if (!_cao('is_site_tickets', true)) {
        exit;
    }
    header('Content-type:application/json; Charset=utf-8');
    global $current_user;
    $user_id   = $current_user->ID;
    $nonce = !empty($_POST['nonce']) ? $_POST['nonce'] : null;

    if ($nonce && !wp_verify_nonce($nonce, 'rizhuti-v2-click-' . $user_id)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('非法请求，请刷新重试', 'rizhuti-v2')));
        exit;
    }
    if (empty($_POST['msg'])) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('请输入工单内容', 'rizhuti-v2')));
        exit;
    }

    //腾讯安全验证
    if (!qq_captcha_verify()) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('安全验证失败，请刷新页面重试', 'rizhuti-v2')));
        exit;
    } else {
        $_SESSION['is_qq_captcha_verify'] = 0;
    }



    if (RiMsg::add_tickets($user_id, sanitize_text_field(wp_unslash($_POST['msg'])))) {
        echo json_encode(array('status' => '1', 'msg' => esc_html__('工单提交成功', 'rizhuti-v2')));
        exit;
    } else {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('提交失败，刷新重试', 'rizhuti-v2')));
        exit;
    }
}
add_action('wp_ajax_send_new_tickets', 'send_new_tickets');

/**
 * 用户投稿
 * @Author   Dadong2g
 * @DateTime 2021-01-17T18:32:59+0800
 * @return   [type]                   [description]
 */
function user_tougao()
{
    if (!_cao('is_site_tougao')) {
        exit;
    }
    header('Content-type:application/json; Charset=utf-8');
    global $current_user;
    $user_id   = $current_user->ID;
    $nonce = !empty($_POST['nonce']) ? $_POST['nonce'] : null;

    if ($nonce && !wp_verify_nonce($nonce, 'rizhuti-v2-click-' . $user_id)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('非法请求，请刷新重试', 'rizhuti-v2')));
        exit;
    }

    if (empty($_POST['post_title']) || empty($_POST['post_cat']) || empty($_POST['post_content'])) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('请输入完整的文章标题/分类/内容', 'rizhuti-v2')));
        exit;
    }

    if (!_cao('is_site_tougao')) {
        echo json_encode(array('status' => '0', 'msg' => '您没有权限发布或修改文章'));
        exit;
    }


    //腾讯安全验证
    if (!qq_captcha_verify()) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('安全验证失败，请刷新页面重试', 'rizhuti-v2')));
        exit;
    } else {
        $_SESSION['is_qq_captcha_verify'] = 0;
    }

    // 插入文章
    $new_post = wp_insert_post(array(
        'post_title'    => wp_strip_all_tags($_POST['post_title']),
        'post_content'  => $_POST['post_content'],
        'post_status'   => 'pending',
        'post_author'   => $user_id,
        'post_category' => array((int)$_POST['post_cat']),
        'meta_input' => $_POST['post_meta'],
    ));

    if ($new_post instanceof WP_Error) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('投稿失败，请刷新重试！', 'rizhuti-v2')));
        exit;
    } else {
        set_post_thumbnail($new_post, (int)$_POST['_thumbnail_id']);
        echo json_encode(array('status' => '1', 'msg' => esc_html__('投稿成功，感谢您宝贵的投稿！', 'rizhuti-v2')));
        exit;
    }
}
add_action('wp_ajax_user_tougao', 'user_tougao');


/**
 * 修改密码
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:27:15+0800
 * @return   [type]                   [description]
 */
function updete_password()
{
    if (is_close_site_shop()) {
        exit;
    }
    header('Content-type:application/json; Charset=utf-8');
    global $current_user;
    $nonce = !empty($_POST['nonce']) ? $_POST['nonce'] : null;
    $old_password = !empty($_POST['old_password']) ? $_POST['old_password'] : null;
    $new_password = !empty($_POST['new_password']) ? $_POST['new_password'] : null;
    $new_password2 = !empty($_POST['new_password2']) ? $_POST['new_password2'] : null;
    if ($nonce && !wp_verify_nonce($nonce, 'rizhuti-v2-click-' . $current_user->ID)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('非法请求，请刷新重试', 'rizhuti-v2')));
        exit;
    }
    if (empty($old_password) || empty($new_password) || empty($new_password2)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('请输入完整密码修改信息', 'rizhuti-v2')));
        exit;
    }
    if ($old_password == $new_password) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('新密码不能与旧密码相同', 'rizhuti-v2')));
        exit;
    }
    if ($new_password != $new_password2) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('两次输入的密码不一致', 'rizhuti-v2')));
        exit;
    }

    //腾讯安全验证
    if (!qq_captcha_verify()) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('安全验证失败，请刷新页面重试', 'rizhuti-v2')));
        exit;
    } else {
        $_SESSION['is_qq_captcha_verify'] = 0;
    }

    //判断是否一键登录密码

    if ($current_user && wp_check_password($old_password, $current_user->data->user_pass, $current_user->ID)) {
        wp_set_password($new_password2, $current_user->ID);
        wp_logout();
        echo json_encode(array('status' => '1', 'msg' => esc_html__('密码修改成功，请使用新密码重新登录', 'rizhuti-v2')));
        exit;
    } elseif (is_oauth_password()) {
        wp_set_password($new_password2, $current_user->ID);
        wp_logout();
        echo json_encode(array('status' => '1', 'msg' => esc_html__('密码设置成功，请使用新密码登录账号', 'rizhuti-v2')));
        exit;
    } else {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('旧密码错误，请输入正确的密码', 'rizhuti-v2')));
        exit;
    }
}
add_action('wp_ajax_updete_password', 'updete_password');





/**
 * 购买文章资源
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:27:20+0800
 * @return   [type]                   [description]
 */
function go_post_pay()
{
    if (is_close_site_shop()) {
        exit;
    }
    header('Content-type:application/json; Charset=utf-8');
    $ip      = get_client_ip(); //客户端IP
    $user_id = get_current_user_id();
    $nonce   = !empty($_POST['nonce']) ? $_POST['nonce'] : null;
    $post_id = !empty($_POST['post_id']) ? $_POST['post_id'] : 0;
    // 1支付宝官方；2微信官方 ； 11讯虎支付宝 ；12讯虎微信
    $pay_type = !empty($_POST['pay_type']) ? (int) $_POST['pay_type'] : 0;

    if ($nonce && !wp_verify_nonce($nonce, 'rizhuti_click_' . $post_id)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('非法请求，请刷新当前页面重试', 'rizhuti-v2')));
        exit;
    }
    if (!is_user_logged_in() && !_cao('is_rizhuti_v2_nologin_pay')) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('请登录后再进行购买', 'rizhuti-v2')));
        exit;
    }
    if ($pay_type == 0) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('请选择支付方式', 'rizhuti-v2')));
        exit;
    }
    if ($post_id <= 0) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('购买ID参数错误', 'rizhuti-v2')));
        exit;
    }

    /////////商品属性START/////// RiClass
    $shop_info = get_post_shop_info($post_id);
    $post_price = $shop_info['wppay_price']; //文章价格
    if (empty($post_price) || $post_price == 0 || $shop_info['wppay_type'] == 4) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('免费资源无需购买', 'rizhuti-v2')));
        exit;
    }
    //是否VIP资源 普通用户不能购买
    if (!empty($shop_info['wppay_vip_auth']) && empty($shop_info['wppay_price'])) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('VIP资源，普通用户不允许购买', 'rizhuti-v2')));
        exit;
    }


    //推荐奖励AFF
    $aff_uid = 0;
    $aff_ratio = 0;
    if (_cao('is_site_aff')) {
        $aff_uid = (!empty($_SESSION['current_aff_uid'])) ? $_SESSION['current_aff_uid'] : 0;
        $aff_from_id = get_user_meta($user_id, 'aff_from_id', true);
        $aff_uid = (!empty($aff_from_id)) ? $aff_from_id : $aff_uid;
        if ($aff_uid != $user_id) {
            $vip_opt = _get_ri_vip_options();
            $aff_vip_type = _get_user_vip_type($aff_uid);
            $aff_ratio = $vip_opt[$aff_vip_type]['aff_ratio'];
        }
    }


    $order_data = [
        'order_price'    => sprintf('%0.2f', $post_price), // 订单价格
        'order_num' => date("ymdhis") . mt_rand(100, 999) . mt_rand(100, 999) . mt_rand(100, 999), //本地订单号
        'order_type'     => 1, //订单类型
        'pay_type'       => $pay_type, //支付方式
        'order_name'     => get_bloginfo('name') . esc_html__('-自助购买', 'rizhuti-v2'),
        'callback_url'   => get_permalink($post_id),
        'aff_uid'        => $aff_uid,
        'aff_ratio'      => $aff_ratio,
        'ip'             => $ip,
    ];

    /////////商品属性END///////

    // 添加订单入库
    $RiClass = new RiClass($post_id, $user_id);
    if (!$RiClass->add_order($order_data)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('添加订单失败，请刷新当前页面重试', 'rizhuti-v2')));
        exit;
    }
    get_pay_snyc_data($pay_type, $order_data);
    exit;
}
add_action('wp_ajax_go_post_pay', 'go_post_pay');
add_action('wp_ajax_nopriv_go_post_pay', 'go_post_pay');


/**
 * 购买VIP
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:27:28+0800
 * @return   [type]                   [description]
 */
function go_vip_pay()
{
    if (is_close_site_shop()) {
        exit;
    }
    header('Content-type:application/json; Charset=utf-8');
    $ip       = get_client_ip(); //客户端IP
    $user_id  = get_current_user_id();
    $nonce    = !empty($_POST['nonce']) ? $_POST['nonce'] : null;
    $vip_type = !empty($_POST['vip_type']) ? trim($_POST['vip_type']) : null;
    // 1支付宝官方；2微信官方 ； 11虎皮椒支付宝 ；12虎皮椒微信
    $pay_type = !empty($_POST['pay_type']) ? (int) $_POST['pay_type'] : 0;
    if ($nonce && !wp_verify_nonce($nonce, 'rizhuti-v2_click_' . $user_id)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('非法请求，请刷新当前页面重试', 'rizhuti-v2')));
        exit;
    }
    if (empty($pay_type)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('请选择支付方式', 'rizhuti-v2')));
        exit;
    }
    if (empty($vip_type)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('请选择开通会员类型', 'rizhuti-v2')));
        exit;
    }

    $current_vip_type = _get_user_vip_type($user_id);
    if ($current_vip_type == '3600' && ($vip_type == '31' || $vip_type == '365')) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('您已经获得该特权', 'rizhuti-v2')));
        exit;
    }
    if ($current_vip_type == '365' && $vip_type == '31') {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('您已经获得该特权', 'rizhuti-v2')));
        exit;
    }

    /////////商品属性START///////
    $vip_opt = _get_ri_vip_options();
    switch ($vip_type) {
        case '31':
            $order_type = 2;
            break;
        case '365':
            $order_type = 3; //月费会员
            break;
        case '3600':
            $order_type = 4; //年费会员
            break;
        default:
            $order_type = 0; //终身会员
            break;
    }

    //推荐奖励AFF
    $aff_uid = 0;
    $aff_ratio = 0;
    if (_cao('is_site_aff')) {
        $aff_uid = (!empty($_SESSION['current_aff_uid'])) ? $_SESSION['current_aff_uid'] : 0;
        $aff_from_id = get_user_meta($user_id, 'aff_from_id', true);
        $aff_uid = (!empty($aff_from_id)) ? $aff_from_id : $aff_uid;
        if ($aff_uid != $user_id) {
            $vip_opt = _get_ri_vip_options();
            $aff_vip_type = _get_user_vip_type($aff_uid);
            $aff_ratio = $vip_opt[$aff_vip_type]['aff_ratio'];
        }
    }
    $order_data = [
        'order_price'    => sprintf('%0.2f', $vip_opt[$vip_type]['price']), // 订单价格
        'order_num' => date("ymdhis") . mt_rand(100, 999) . mt_rand(100, 999) . mt_rand(100, 999), //本地订单号
        'order_type'     => $order_type, //订单类型
        'pay_type'       => $pay_type, //支付方式
        'order_name'     => $vip_opt[$vip_type]['name'] . esc_html__('-自助购买', 'rizhuti-v2'),
        'callback_url'   => get_user_page_url(),
        'aff_uid'         => $aff_uid,
        'aff_ratio'      => $aff_ratio,
        'ip'             => $ip,
    ];

    /////////商品属性END///////

    // 添加订单入库
    $RiClass = new RiClass(0, $user_id);
    if (!$RiClass->add_order($order_data)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('添加订单失败，请刷新当前页面重试', 'rizhuti-v2')));
        exit;
    }
    get_pay_snyc_data($pay_type, $order_data);
    exit;
}
add_action('wp_ajax_go_vip_pay', 'go_vip_pay');

/**
 * 在线充值接口
 * @Author   Dadong2g
 * @DateTime 2021-03-12T09:44:33+0800
 * @return   [type]                   [description]
 */
function go_coin_pay()
{
    if (is_close_site_shop()) {
        exit;
    }
    header('Content-type:application/json; Charset=utf-8');
    $ip       = get_client_ip(); //客户端IP
    $user_id  = get_current_user_id();
    $nonce    = !empty($_POST['nonce']) ? $_POST['nonce'] : null;
    $coin_key = !empty($_POST['coin_key']) ? (int)$_POST['coin_key'] : 0;
    $pay_type = !empty($_POST['pay_type']) ? (int) $_POST['pay_type'] : 0;
    $order_type = 99;
    // 1支付宝官方；2微信官方 ； 11虎皮椒支付宝 ；12虎皮椒微信

    if ($nonce && !wp_verify_nonce($nonce, 'rizhuti-v2_click_' . $user_id)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('非法请求，请刷新当前页面重试', 'rizhuti-v2')));
        exit;
    }
    if (empty($pay_type)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('请选择支付方式', 'rizhuti-v2')));
        exit;
    }
    //充值套餐配置
    $site_mycoin_pay_arr = site_mycoin('pay_arr');
    if (empty($site_mycoin_pay_arr[$coin_key])) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('请选择充值套餐', 'rizhuti-v2')));
        exit;
    }

    $coin_num = $site_mycoin_pay_arr[$coin_key]['num'];
    $order_price = convert_site_mycoin($coin_num, 'rmb');

    //推荐奖励AFF
    $aff_uid = 0;
    $aff_ratio = 0;
    if (_cao('is_site_aff')) {
        $aff_uid = (!empty($_SESSION['current_aff_uid'])) ? $_SESSION['current_aff_uid'] : 0;
        $aff_from_id = get_user_meta($user_id, 'aff_from_id', true);
        $aff_uid = (!empty($aff_from_id)) ? $aff_from_id : $aff_uid;
        if ($aff_uid != $user_id) {
            $vip_opt = _get_ri_vip_options();
            $aff_vip_type = _get_user_vip_type($aff_uid);
            $aff_ratio = $vip_opt[$aff_vip_type]['aff_ratio'];
        }
    }

    $order_data = [
        'order_price'    => sprintf('%0.2f', $order_price), // 订单价格
        'order_num' => date("ymdhis") . mt_rand(100, 999) . mt_rand(100, 999) . mt_rand(100, 999), //本地订单号
        'order_type'     => $order_type, //订单类型
        'pay_type'       => $pay_type, //支付方式
        'order_name'     => get_bloginfo('name') . esc_html__('-自助充值', 'rizhuti-v2'),
        'callback_url'   => get_user_page_url(),
        'aff_uid'         => $aff_uid,
        'aff_ratio'      => $aff_ratio,
        'ip'             => $ip,
    ];

    /////////商品属性END///////

    // 添加订单入库
    $RiClass = new RiClass(0, $user_id);
    if (!$RiClass->add_order($order_data)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('添加订单失败，请刷新当前页面重试', 'rizhuti-v2')));
        exit;
    }
    get_pay_snyc_data($pay_type, $order_data);
    exit;
}
add_action('wp_ajax_go_coin_pay', 'go_coin_pay');

/**
 * 卡密充值接口
 * @Author   Dadong2g
 * @DateTime 2021-03-12T18:37:03+0800
 * @return   [type]                   [description]
 */
function go_cdkpay_coin()
{
    if (is_close_site_shop()) {
        exit;
    }
    header('Content-type:application/json; Charset=utf-8');
    $ip       = get_client_ip(); //客户端IP
    $user_id  = get_current_user_id();
    $nonce    = !empty($_POST['nonce']) ? $_POST['nonce'] : null;
    $cdk_code = !empty($_POST['cdk_code']) ? trim($_POST['cdk_code']) : 0;
    $order_type = 99;
    $pay_type = 88;

    if ($nonce && !wp_verify_nonce($nonce, 'rizhuti-v2_click_' . $user_id)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('非法请求，请刷新当前页面重试', 'rizhuti-v2')));
        exit;
    }
    if (!_cao('is_cdk_pay', true)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('卡密通道暂未开启', 'rizhuti-v2')));
        exit;
    }
    //检查卡密
    $cdk_money = RiCdk::get_cdk($cdk_code, true);

    if (empty($cdk_money) || $cdk_money <= 0) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('请输入有效卡密', 'rizhuti-v2')));
        exit;
    }

    $order_data = [
        'order_price'    => sprintf('%0.2f', $cdk_money), // 订单价格
        'order_num' => date("ymdhis") . mt_rand(100, 999) . mt_rand(100, 999) . mt_rand(100, 999), //本地订单号
        'order_type'     => $order_type, //订单类型
        'pay_type'       => $pay_type, //支付方式
        'order_name'     => get_bloginfo('name') . esc_html__('-卡密充值', 'rizhuti-v2'),
        'callback_url'   => get_user_page_url(),
        'aff_uid'         => 0,
        'aff_ratio'      => 0,
        'ip'             => $ip,
        'cdk_code'       => $cdk_code,
    ];

    /////////商品属性END///////

    // 添加订单入库
    $RiClass = new RiClass(0, $user_id);
    if (!$RiClass->add_order($order_data)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('添加订单失败，请刷新当前页面重试', 'rizhuti-v2')));
        exit;
    }
    get_pay_snyc_data($pay_type, $order_data);
    exit;
}
add_action('wp_ajax_go_cdkpay_coin', 'go_cdkpay_coin');



/**
 * 检测支付状态
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:28:17+0800
 * @return   [type]                   [description]
 */
function check_pay()
{
    if (is_close_site_shop()) {
        exit;
    }
    header('Content-type:application/json; Charset=utf-8');
    $user_id    = get_current_user_id();
    $post_id    = !empty($_POST['post_id']) ? $_POST['post_id'] : 0;
    $orderNum   = !empty($_POST['num']) ? $_POST['num'] : null;
    $RiClass = new RiClass($post_id, $user_id);
    $status     = $RiClass->check_order($orderNum);
    if ($status) {
        $intstatus = 1;
        $msg       = esc_html__('恭喜你，支付成功', 'rizhuti-v2');
    } else {
        $intstatus = 0;
        $msg       = esc_html__('支付中', 'rizhuti-v2');
    }
    $result = array(
        'status' => $intstatus,
        'msg'    => $msg,
    );
    echo json_encode($result);
    exit;
}
add_action('wp_ajax_check_pay', 'check_pay');
add_action('wp_ajax_nopriv_check_pay', 'check_pay');


/**
 * 获取微信登陆二维码
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:28:21+0800
 * @return   [type]                   [description]
 */
function get_mpweixin_qr()
{
    if (is_close_site_shop()) {
        exit;
    }
    header('Content-type:application/json; Charset=utf-8');
    $wxConfig = _cao('sns_weixin');
    if ($wxConfig['sns_weixin_mod'] != 'mp') {
        echo json_encode(array('status' => 0, 'ticket_img' => '', 'scene_id' => ''));
        exit;
    }
    $RiPlusSNS = new RiPlusSNS();
    echo json_encode($RiPlusSNS->getLoginQr());
    exit;
}
add_action('wp_ajax_get_mpweixin_qr', 'get_mpweixin_qr');
add_action('wp_ajax_nopriv_get_mpweixin_qr', 'get_mpweixin_qr');


/**
 * 检测微信公众号状态+绑定已登录用户
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:28:33+0800
 * @return   [type]                   [description]
 */
function check_mpweixin_qr()
{
    if (is_close_site_shop()) {
        exit;
    }
    header('Content-type:application/json; Charset=utf-8');
    $scene_id   = !empty($_POST['scene_id']) ? esc_sql($_POST['scene_id']) : null;
    global $wpdb, $mpwx_log_table_name, $current_user;
    $current_user_id = $current_user->ID;
    // 查询数据库
    $res = $wpdb->get_row($wpdb->prepare("SELECT * FROM $mpwx_log_table_name WHERE scene_id = %s AND openid != '' ", esc_sql($scene_id)));
    if (empty($res) || $res->scene_id != $scene_id) {
        echo json_encode(array('status' => 0, 'msg' => ''));
        exit;
    }

    if (($res->create_time + 180) < time()) {
        echo json_encode(array('status' => 0, 'msg' => esc_html__('登录超时，请刷新页面重试', 'rizhuti-v2')));
        exit;
    }

    // 查询openid
    $_prefix = 'mpweixin';
    $_openid_meta_key = 'open_' . $_prefix . '_openid';
    $search_user = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key=%s AND meta_value=%s", $_openid_meta_key, $res->openid));

    // 如果当前用户已登录，而$search_user存在，即该开放平台账号连接被其他用户占用了，不能再重复绑定了
    if ($current_user_id > 0 && $search_user > 0 && $current_user_id != $search_user) {
        echo json_encode(array('status' => 0, 'msg' => '已绑定过其他账号---当前$current_user_id：' . $current_user_id . '---搜索到的$search_user：' . $search_user . '，请先登录其他账户解绑'));
        exit;
    }
    // 当前已登录了本地账号, 并且微信没有被绑定 提示用手机打开绑定

    if (empty($current_user_id)) {
        $user = get_user_by('id', $search_user);
        wp_set_current_user($user->ID, $user->user_login);
        wp_set_auth_cookie($user->ID, true);
        do_action('wp_login', $user->user_login, $user);

        echo json_encode(array('status' => 1, 'msg' => esc_html__('登录成功，即将刷新页面', 'rizhuti-v2')));
        exit;
    }
}
add_action('wp_ajax_check_mpweixin_qr', 'check_mpweixin_qr');
add_action('wp_ajax_nopriv_check_mpweixin_qr', 'check_mpweixin_qr');



/**
 * 刷新公众号菜单
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:28:40+0800
 * @return   [type]                   [description]
 */
function rest_mpweixin_menu()
{

    header('Content-type:application/json; Charset=utf-8');
    $sns_weixin = _cao('sns_weixin');
    $menu = array();
    $i = 0;
    if ($sns_weixin['sns_weixin_mod'] == 'mp' && !empty($sns_weixin['custom_wxmenu_opt'])) {
        $RiPlusSNS = new RiPlusSNS;
        foreach ($sns_weixin['custom_wxmenu_opt'] as $item) {
            $menu['button'][$i]['name'] = $item['name'];
            if (!empty($item['sub_button'])) {
                $j = 0;
                foreach ($item['sub_button'] as $sub) {
                    $menu['button'][$i]['sub_button'][$j]['type'] = 'view';
                    $menu['button'][$i]['sub_button'][$j]['name'] = $sub['name'];
                    $menu['button'][$i]['sub_button'][$j]['url'] = $sub['url'];
                    $j++;
                }
            } else {
                $menu['button'][$i]['type'] = 'view';
                $menu['button'][$i]['url'] = $item['url'];
            }
            $i++;
        }
    }
    $data = json_encode($menu, JSON_UNESCAPED_UNICODE);
    $data = str_replace('\/', '/', $data);
    $result = $RiPlusSNS->CreateMenu($data);
    echo $result;
    exit;
}
add_action('wp_ajax_rest_mpweixin_menu', 'rest_mpweixin_menu');





/**
 * 评论表单提交
 * @Author   Dadong2g
 * @DateTime 2021-04-04T21:35:23+0800
 * @param    [type]                   $a [description]
 * @return   [type]                      [description]
 */
function rizhuti_v2_ajax_comment_err($a)
{
    header('HTTP/1.0 500 Internal Server Error');
    header('Content-Type: text/plain;charset=UTF-8');
    echo $a;
    exit;
}

/**
 * 评论表单提交回调
 * @Author   Dadong2g
 * @DateTime 2021-04-04T21:35:40+0800
 * @return   [type]                   [description]
 */
function rizhuti_v2_ajax_comment_callback()
{

    if (!is_site_comments()) {
        rizhuti_v2_ajax_comment_err(esc_html__('评论功能未开启', 'rizhuti-v2'));
    }

    $comment = wp_handle_comment_submission(wp_unslash($_POST));
    if (is_wp_error($comment)) {
        $data = $comment->get_error_data();
        if (!empty($data)) {
            rizhuti_v2_ajax_comment_err($comment->get_error_message());
        } else {
            exit;
        }
    }
    $user = wp_get_current_user();
    do_action('set_comment_cookies', $comment, $user);
    $GLOBALS['comment'] = $comment;
    $author = get_comment_author();
    $reply = '';
    if ($comment->user_id) {
        $author = '<a>' . $author . '</a>';
    } else if ($comment->comment_author_url) {
        $author = '<a href="' . esc_url($comment->comment_author_url) . '" target="_blank" rel="nofollow">' . $author . '</a>';
    }
?>

    <li <?php comment_class(); ?>>
        <div id="div-comment-<?php comment_ID() ?>" class="comment-inner">
            <div class="comment-author vcard">
                <?php echo get_avatar($comment, 50); ?>
            </div>
            <div class="comment-body">
                <div class="nickname"><?php echo $author . $reply; ?>
                    <span class="comment-time"><?php echo get_comment_date() . ' ' . get_comment_time(); ?></span>
                </div>
                <?php if ($comment->comment_approved == '0') : ?>
                    <div class="comment-awaiting-moderation"><?php _e('您的评论正在等待审核。', 'riplus'); ?></div>
                <?php endif; ?>
                <div class="comment-text"><?php comment_text(); ?></div>
            </div>

            <div class="reply">
                <?php comment_reply_link(); ?>
            </div>
        </div>
    </li>

<?php die();
}
add_action('wp_ajax_nopriv_ajax_comment', 'rizhuti_v2_ajax_comment_callback');
add_action('wp_ajax_ajax_comment', 'rizhuti_v2_ajax_comment_callback');
