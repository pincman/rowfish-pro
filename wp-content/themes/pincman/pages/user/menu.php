<?php
defined('ABSPATH') || exit;
global $current_user, $ri_vip_options;
$action_var = get_query_var('action');
$action = (!empty($action_var)) ? strtolower($action_var) : 'index';
$vip_type = _get_user_vip_type($current_user->ID);

$action_opt = pincman_user_page_options();
$shop_nav = $action_opt['shop'];
$info_nav = $action_opt['info'];
?>

<!-- Navbar -->
<div class="navbar-expand-lg navbar-expand-lg-collapse-block navbar-light">
  <div id="sidebarNav" class="collapse navbar-collapse navbar-vertical">
    <!-- Card -->
    <div class="card mb-5">
      <div class="card-body">

        <h6 class="text-cap small text-muted"><?php echo esc_html__('会员中心', 'rizhuti-v2'); ?></h6>
        <ul class="nav nav-sub nav-sm nav-tabs mt-0 mb-4">
          <?php foreach ($info_nav as $key => $nav) {
            $is_active = ($action == $nav['action']) ? ' active' : '';
            $href = get_user_page_url($nav['action']);
            echo '<li class="nav-item"><a class="nav-link' . $is_active . '" href="' . $href . '"><i class="' . $nav['icon'] . '"></i> ' . $nav['name'] . '</a></li>';
          } ?>
          <?php foreach ($shop_nav as $key => $nav) {
            $is_active = ($action == $nav['action']) ? ' active' : '';
            $href = get_user_page_url($nav['action']);
            echo '<li class="nav-item"><a class="nav-link' . $is_active . '" href="' . $href . '"><i class="' . $nav['icon'] . '"></i> ' . $nav['name'] . '</a></li>';
          } ?>
        </ul>

        <div class="d-lg-none">
          <div class="dropdown-divider"></div>
          <ul class="nav nav-sub nav-sm nav-tabs my-2">
            <li class="nav-item">
              <a class="nav-link text-primary" href="<?php echo wp_logout_url(home_url()); ?>"><i class="fa fa-sign-out nav-icon"></i><?php echo esc_html__('退出登录', 'rizhuti-v2'); ?></a>
            </li>
          </ul>
        </div>


      </div>
    </div>
    <!-- End Card -->
  </div>
</div>
<!-- End Navbar -->