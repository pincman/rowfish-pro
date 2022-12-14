<?php global $current_user; ?>
<?php
if (ap_is_addon_active('profile.php')) {
    $slug = get_option('ap_user_path');
    $qalink = home_url($slug) . '/' . $current_user->user_nicename . '/';
}
?>
<header class="site-header">
    <div class="container">
        <div class="navbar">
            <?php rizhuti_v2_logo(); ?>
            <div class="sep"></div>

            <nav class="main-menu d-none d-lg-block">
                <?php wp_nav_menu(array(
                    'container' => false,
                    'fallback_cb' => 'Pincman_Walker_Nav_Menu::fallback',
                    'menu_class' => 'nav-list u-plain-list',
                    'theme_location' => 'primary',
                    'walker' => new Pincman_Walker_Nav_Menu(true),
                )); ?>
            </nav>

            <div class="actions">


                <!-- user navbar dropdown -->
                <?php if (is_user_logged_in()) :
                    $action_opt = pincman_user_page_options();
                    $shop_nav = $action_opt['shop'];
                    $info_nav = $action_opt['info'];
                ?>

                    <li class="dropdown ml-2 d-inline-block">

                        <a class="rounded-circle d-flex align-items-center" href="<?php echo get_user_page_url(); ?>" role="button" id="dropdownUserProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" rel="nofollow noopener noreferrer">
                            <img class="menu-avatar-img mr-1" src="<?php echo get_avatar_url($current_user->ID); ?>" width="30" alt="avatar">
                            <small class="mx-display-name"><?php echo $current_user->display_name; ?></small>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownUserProfile">
                            <div class="dropdown-item">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-indicators">
                                        <img class="rounded-circle" src="<?php echo get_avatar_url($current_user->ID); ?>" alt="avatar">
                                    </div>
                                    <div class="ml-3 lh-1">
                                        <p class="d-flex align-items-center mb-1"><?php echo $current_user->display_name; ?><a href="<?php echo get_user_page_url('vip'); ?>"><?php echo pm_get_vip_badge($current_user->ID); ?></a></p>
                                        <small class="mb-0 text-muted"><?php echo $current_user->user_email; ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <ul class="list-unstyled mt-0 mb-2">
                                <?php if (site_mycoin('is')) : ?>
                                    <li>
                                        <div class="d-flex align-content-center justify-content-between py-2 px-3">
                                            <a class="badge badge-coin" href="<?php echo get_user_page_url('coin'); ?>"><i class="<?php echo site_mycoin('icon'); ?> mr-2"></i><?php echo get_user_mycoin($current_user->ID) . site_mycoin('name'); ?></a>
                                            <a class="badge badge-danger-lighten badge-coin-btn" href="<?php echo get_user_page_url('coin'); ?>"><?php echo esc_html__('??????', 'rizhuti-v2'); ?></a>
                                        </div>
                                    </li>
                                    <div class="dropdown-divider"></div>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="<?php echo get_user_page_url(); ?>"><i class="<?php echo $action_opt['info']['index']['icon']; ?> mr-2"></i><?php echo $action_opt['info']['index']['name']; ?></a></li>
                                <li><a class="dropdown-item" href="<?php echo get_user_page_url('vip'); ?>"><i class="<?php echo $action_opt['shop']['vip']['icon']; ?> mr-2"></i><?php echo $action_opt['shop']['vip']['name']; ?></a></li>
                                <li><a class="dropdown-item" href="<?php echo get_user_page_url('fav'); ?>"><i class="<?php echo $action_opt['shop']['fav']['icon']; ?> mr-2"></i><?php echo $action_opt['shop']['fav']['name']; ?></a></li>
                                <?php if (ap_is_addon_active('profile.php')) : ?>
                                    <li><a class="dropdown-item" href="<?php echo $qalink ?>"><i class="fa fa-wordpress nav-icon mr-2"></i>????????????</a></li>
                                <?php endif; ?>
                                <?php if (in_array('administrator', $current_user->roles)) : ?>
                                    <li><a target="_blank" class="dropdown-item" href="<?php echo esc_url(admin_url('/')); ?>"><i class="fa fa-wordpress nav-icon"></i><?php echo esc_html__('????????????', 'rizhuti-v2'); ?></a></li>
                                <?php endif; ?>
                                <div class="dropdown-divider"></div>
                                <li><a class="dropdown-item" href="<?php echo wp_logout_url(curPageURL()); ?>"><i class="fa fa-sign-out nav-icon"></i><?php echo esc_html__('????????????', 'rizhuti-v2'); ?></a></li>
                            </ul>
                        </div>
                    </li>
                <?php elseif (_cao('is_site_user_login', true) || _cao('is_site_user_register', true)) : ?>
                    <a class="btn btn-sm ml-2" rel="nofollow noopener noreferrer" href="<?php echo wp_login_url(curPageURL()); ?>"><i class="fa fa-user mr-1"></i><?php echo esc_html__('??????', 'rizhuti-v2'); ?></a>
                <?php endif; ?>
                <!-- user navbar dropdown -->

                <span class="btn btn-sm search-open navbar-button ml-2" rel="nofollow noopener noreferrer" data-action="omnisearch-open" data-target="#omnisearch" title="<?php echo esc_html('??????', 'rizhuti-v2'); ?>"><i class="fas fa-search"></i></span>
                <span class="btn btn-sm toggle-dark navbar-button ml-2" rel="nofollow noopener noreferrer" title="<?php echo esc_html('????????????', 'rizhuti-v2'); ?>"><i class="fa fa-adjust"></i></span>
                <div class="burger"></div>


            </div>

        </div>
    </div>
</header>
<?php
$termObj = get_queried_object();
$is_list = $termObj->taxonomy === 'category' || $termObj->taxonomy === 'post_tag';
?>
<div class="header-gap<?php echo $is_list ? ' post-item-collection' : ''; ?>"></div>