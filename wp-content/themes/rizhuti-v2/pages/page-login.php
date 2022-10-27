<?php
/**
 * Template Name: 登录注册页面
 */

if (is_close_site_shop()) {
    wp_safe_redirect(home_url());exit;
}

$page_mod = !empty($_GET['mod']) ? $_GET['mod'] : 'login';
if (!in_array($page_mod, array('login', 'register', 'bindemail','lostpassword'))) {
  $page_mod = 'login';
}

//已登录跳转到用户中心
if (is_user_logged_in()) {
    
  global $current_user;
  //是否强制绑定邮箱
  $is_bind_email = $page_mod=='bindemail' && _cao('is_site_register_bind_email') && empty($current_user->user_email);
  if(!$is_bind_email){
    wp_safe_redirect(get_user_page_url());exit;
  }
  
}

get_header();

?>
<div class="bg-img-cover lazyload" data-bg="<?php echo _cao('site_login_bg_img');?>"></div>

<div class="container">
  <div class="row justify-content-center align-items-center login-warp">
    <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5">
      
      <!-- Form -->
      <form class="js-validate card p-lg-5 p-4">
        <div class="d-flex flex-center mb-4">
        <?php rizhuti_v2_logo(); ?>
        </div>
        <?php if ( $page_mod=='login' && _cao('is_site_user_login',true) ) : ?>
          <!-- Title -->
          <div class="mb-4 login-page-title">
            <p><?php echo esc_html__('登录您的账户','rizhuti-v2' ); ?></p>
          </div>
          <!-- End Title -->
          <div class="row">
            <div class="col-lg-12">
                <div class="form-group position-relative">
                    <label class="text-muted"><?php echo esc_html__('账号 *','rizhuti-v2' ); ?></label>
                    <input type="email" class="form-control pl-3" placeholder="请输入电子邮箱/用户名" name="username" required="">
                </div>
            </div>
            <div class="col-lg-12">
                <div class="form-group position-relative">
                    <label class="text-muted"><?php echo esc_html__('密码 *','rizhuti-v2' ); ?><a class="ml-2 btn-link" href="<?php echo wp_lostpassword_url(); ?>"><?php echo esc_html__('忘记密码？','rizhuti-v2' ); ?></a></label>
                    <input type="password" class="form-control pl-3" placeholder="请输入密码" name="password" required="">
                </div>
            </div>
            <?php qq_captcha_btn(); ?>
            <div class="col-lg-12 mb-0"><button class="btn btn-primary w-100 go-login"><?php echo esc_html__('立即登录','rizhuti-v2' ); ?></button></div>
            <?php get_template_part('template-parts/login-sns');?>
            <?php if ( _cao('is_site_user_register',true) ) : ?>
            <div class="col-12 text-center">
                <p class="mb-0 mt-3"><small class="text-dark mr-2"><?php echo esc_html__('还没有账号，现在注册?','rizhuti-v2' ); ?></small> <a href="<?php echo wp_registration_url(); ?>" class="btn-link"><?php echo esc_html__('注册新用户','rizhuti-v2' ); ?></a></p>
            </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>
        
        <?php if ( $page_mod=='register' && _cao('is_site_user_register',true) ) : ?>
          <div class="mb-4 login-page-title">
            <p><?php echo esc_html__('注册新账户','rizhuti-v2' ); ?></p>
          </div>
          <div class="row">
            <div class="col-lg-12">
                <div class="form-group position-relative">
                    <label class="text-muted"><?php echo esc_html__('邮箱 *','rizhuti-v2' ); ?></label>
                    <div class="input-group mb-3">
                      <input type="email" class="form-control pl-3" placeholder="请输入注册邮箱" name="user_email" required="">
                    </div>
                </div>
            </div>
            <?php if (_cao('is_site_email_captcha_verify')) : ?>
            <div class="col-lg-12">
                <div class="form-group position-relative">
                    <div class="input-group mb-3">
                      <input type="text" class="form-control" placeholder="<?php echo esc_html__('邮箱验证码 *','rizhuti-v2' ); ?>" name="email_verify_code" aria-label="<?php echo esc_html__('请输入邮箱验证码','rizhuti-v2' ); ?>" aria-describedby="send-email-code" disabled="disabled">
                      <div class="input-group-append">
                        <button class="btn btn-outline-secondary go-send-email-code" type="button" id="send-email-code"><?php echo esc_html__('发送验证码','rizhuti-v2' ); ?></button>
                      </div>
                    </div>
                </div>
            </div>
            <?php endif;?>
            <div class="col-lg-12">
                <div class="form-group position-relative">
                    <label class="text-muted"><?php echo esc_html__('密码 *','rizhuti-v2' ); ?></label>
                    <input type="password" class="form-control pl-3" placeholder="<?php echo esc_html__('密码长度最低6位','rizhuti-v2' ); ?>" name="user_pass" required="">
                </div>
            </div>
            <div class="col-lg-12">
                <div class="form-group position-relative">
                    <label class="text-muted"><?php echo esc_html__('确认密码 *','rizhuti-v2' ); ?></label>
                    <input type="password" class="form-control pl-3" placeholder="再次输入密码" name="user_pass2" required="">
                </div>
            </div>
            <div class="col-12 mb-2">
                <small class="text-muted">
                  <?php printf(__('注册登录即表示同意 ','rizhuti-v2' ).'<a class="btn-link" href="%s">'.__('用户协议','rizhuti-v2' ).'</a>'.'、<a class="btn-link" href="%s">'.__('隐私政策','rizhuti-v2' ).'</a>',_cao('site_login_reg_href1','#'),_cao('site_login_reg_href2','#') ); ?>
                </small>
            </div>
            <?php qq_captcha_btn(); ?>
            <div class="col-lg-12 mb-0">
                <button class="btn btn-primary w-100 go-register"><?php echo esc_html__('立即注册','rizhuti-v2' ); ?></button>
            </div>
            
            <?php get_template_part('template-parts/login-sns');?>
            <?php if ( _cao('is_site_user_login',true) ) : ?>
            <div class="col-12 text-center">
                <p class="mb-0 mt-3"><small class="mr-2"><?php echo esc_html__('已有账号 ?','rizhuti-v2' ); ?></small> <a href="<?php echo wp_login_url();?>" class="btn-link"><?php echo esc_html__('立即登录','rizhuti-v2' ); ?></a></p>
            </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>
        <!-- //第三方登录绑定用户未设置 -->
        <?php if ($page_mod=='bindemail') : ?>
          <div class="mb-4 login-page-title">
            <p><?php echo esc_html__('您需要完善邮箱信息','rizhuti-v2' ); ?></p>
            <ul class="nav nav-segment nav-fill mb-2" id="editUserTab" role="tablist">
              <?php 
                $is_bind_olduser = isset($_GET['to']) && $_GET['to']=='old_user';
                $is_active1 = ($is_bind_olduser) ? '' : ' active' ;
                $is_active2 = ($is_bind_olduser) ? ' active' : '' ;
              ?>
              <li class="nav-item"><a class="nav-link<?php echo $is_active1;?>" href="<?php echo add_query_arg(array('mod'=>'bindemail'),wp_login_url());?>"><?php echo esc_html__('新用户绑定','rizhuti-v2' ); ?></a></li>
              <li class="nav-item"><a class="nav-link<?php echo $is_active2;?>" href="<?php echo add_query_arg(array('mod'=>'bindemail','to'=>'old_user'),wp_login_url());?>"><?php echo esc_html__('绑定已有账号','rizhuti-v2' ); ?></a></li>
            </ul>
          </div>
          <?php if($is_bind_olduser): ?>
            <!-- Empty State -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group position-relative">
                        <label class="text-muted"><?php echo esc_html__('账号 *','rizhuti-v2' ); ?></label>
                        <input type="email" class="form-control pl-3" placeholder="请输入电子邮箱/用户名" name="bind_username" required="">
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group position-relative">
                        <label class="text-muted"><?php echo esc_html__('密码 *','rizhuti-v2' ); ?><a class="ml-2 btn-link" href="<?php echo wp_lostpassword_url(); ?>"><?php echo esc_html__('忘记密码？','rizhuti-v2' ); ?></a></label>
                        <input type="password" class="form-control pl-3" placeholder="请输入密码" name="bind_password" required="">
                    </div>
                </div>
                <?php qq_captcha_btn(); ?>
                <div class="col-lg-12 mb-0"><button class="btn btn-primary w-100 go-bind-olduser"><?php echo esc_html__('立即绑定','rizhuti-v2' ); ?></button></div>
                <div class="col-12 text-center">
                    <p class="mb-0 mt-3"><small class="btn-link mr-2"><?php echo esc_html__('不想绑定 ?','rizhuti-v2' ); ?></small> <a href="<?php echo wp_logout_url(home_url()); ?>" class=""><?php echo esc_html__('放弃绑定','rizhuti-v2' ); ?></a></p>
                </div>
            </div>
            <!-- End Empty State -->
          <?php else: ?>
            <!-- Empty State -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group position-relative">
                        <label class="text-muted"><?php echo esc_html__('邮箱 *','rizhuti-v2' ); ?></label>
                        <div class="input-group mb-3">
                          <input type="email" class="form-control pl-3" placeholder="绑定邮箱" name="user_email" required="">
                          <div class="input-group-append">
                            <button class="btn btn-outline-secondary go-send-email-code" type="button" id="send-email-code"><?php echo esc_html__('发送验证码','rizhuti-v2' ); ?></button>
                          </div>
                        </div>
                        
                    </div>
                </div>
                <?php if (_cao('is_site_email_captcha_verify') || true) : ?>
                <div class="col-lg-12">
                    <div class="form-group position-relative">
                        <label class="text-muted"><?php echo esc_html__('邮箱验证码 *','rizhuti-v2' ); ?></label>
                        <div class="input-group mb-3">
                          <input type="text" class="form-control" placeholder="<?php echo esc_html__('邮箱验证码 *','rizhuti-v2' ); ?>" name="email_verify_code" aria-label="<?php echo esc_html__('邮箱验证码','rizhuti-v2' ); ?>" aria-describedby="send-email-code" disabled="disabled">
                        </div>
                    </div>
                </div>
                <?php endif;?>
                <?php qq_captcha_btn(); ?>
                <div class="col-lg-12 mb-0"><button class="btn btn-primary w-100 go-bind-email"><?php echo esc_html__('立即绑定','rizhuti-v2' ); ?></button></div>
                <div class="col-12 text-center">
                    <p class="mb-0 mt-3"><small class="btn-link mr-2"><?php echo esc_html__('不想绑定 ?','rizhuti-v2' ); ?></small> <a href="<?php echo wp_logout_url(home_url()); ?>" class=""><?php echo esc_html__('放弃绑定','rizhuti-v2' ); ?></a></p>
                </div>
            </div>
            <!-- End Empty State -->
          <?php endif; ?>
        <?php endif; ?>
        
        <?php if ($page_mod=='lostpassword') : ?>
        
          <?php if (isset($_GET['uid']) && isset($_GET['key']) && isset($_GET['riresetpass']) && $_GET['riresetpass']=='true' && isset($_GET['rifrp_action'])) { ?>
            <div class="mb-4 login-page-title">
              <p><?php echo esc_html__('您正在重新设置账号密码','rizhuti-v2' ); ?></p>
            </div>
            <div class="row">
              <!-- 设置密码框 -->
              <div class="col-lg-12">
                  <div class="form-group position-relative">
                      <label class="text-muted"><?php echo esc_html__('新密码 *','rizhuti-v2' ); ?></label>
                      <input type="password" class="form-control pl-3" placeholder="<?php echo esc_html__('密码长度最低6位','rizhuti-v2' ); ?>" name="user_pass" required="">
                  </div>
              </div>
              <div class="col-lg-12">
                  <div class="form-group position-relative">
                      <label class="text-muted"><?php echo esc_html__('确认新密码 *','rizhuti-v2' ); ?></label>
                      <input type="password" class="form-control pl-3" placeholder="再次输入密码" name="user_pass2" required="">
                  </div>
              </div>

              <?php qq_captcha_btn(); ?>
              <div class="col-lg-12 mb-0">
                <button class="btn btn-primary w-100 go-set-rest-password" data-key="<?php echo $_GET['key'];?>" data-uid="<?php echo $_GET['uid'];?>"><?php echo esc_html__('立即设置新密码','rizhuti-v2' ); ?></button>
              </div>


            </div>
          <?php }else{ ?>
            <!-- 找回密码框 -->
            <div class="mb-4 login-page-title">
              <p><?php echo esc_html__('使用邮箱找回密码，重置密码链接会发送到您邮箱','rizhuti-v2' ); ?></p>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group position-relative">
                        <label class="text-muted"><?php echo esc_html__('邮箱账号 *','rizhuti-v2' ); ?></label>
                        <input type="email" class="form-control pl-3" placeholder="<?php echo esc_html__('请输入绑定的邮箱','rizhuti-v2' ); ?>" name="user_email" required="">
                    </div>
                </div>
                <?php qq_captcha_btn(); ?>
                <div class="col-lg-12 mb-0">
                    <button class="btn btn-primary w-100 go-rest-password"><?php echo esc_html__('立即找回密码','rizhuti-v2' ); ?></button>
                </div>
                <div class="col-12 text-center">
                  <p class="mb-0 mt-3"><small class="mr-2"><?php echo esc_html__('不想找回？','rizhuti-v2' ); ?></small> <a href="<?php echo wp_login_url();?>" class="btn-link"><?php echo esc_html__('立即登录','rizhuti-v2' ); ?></a></p>
                </div>
            </div>
          <?php }?>
        <?php endif; ?>
      
        <?php if ($page_mod=='login' || $page_mod=='register') {
          get_template_part('template-parts/global/login-sns');
        }?>

        <!-- End Button -->
      </form>
      <!-- End Form -->
    </div>
    <div class="position-abs d-lg-block d-none">
      <div class="footer-copyright text-center">
        <p class="m-0 small"><?php echo _cao('site_copyright_text').'<span class="sep"> | </span>'._cao('site_ipc_text').'<span class="sep"> | </span>'._cao('site_ipc2_text'); ?><?php do_action('get_copyright_before');?>
        </p>
      </div>
    </div>

  </div>
</div>


<!-- JS脚本 -->
<script type="text/javascript">
jQuery(function(){"use strict";var t=navigator.userAgent.toLowerCase();$(".oauth-btn.weixin").hasClass("mp")&&"micromessenger"==t.match(/MicroMessenger/i)&&Swal.fire({title:"快速登录提示",text:"检测到您正在微信中浏览本站，是否使用微信快速登录本站？",icon:"info",showCancelButton:!0,confirmButtonColor:"#3085d6",cancelButtonColor:"#d33",confirmButtonText:"确定",cancelButtonText:"取消"}).then(t=>{t.isConfirmed&&(window.location.href=$(".oauth-btn.mp").attr("href"))}),$(document).on("click",".go-login",function(t){t.preventDefault();var i=$(this),n=i.text(),s=$("input[name='username']").val(),o=$("input[name='password']").val(),e=(n=i.text(),document.location.href.split("redirect_to=")[1]||rizhutiv2.home_url+"/user");i.html(iconspin+n),s&&o?is_qq_captcha_verify?rizhuti_v2_ajax({action:"user_login",username:s,password:o,rememberme:1},function(t){},function(t){1==t.status?rizhuti_v2_toast_msg("success",t.msg,function(){window.location.href=decodeURIComponent(e)}):rizhuti_v2_toast_msg("info",t.msg,function(){i.html(n)})},function(t){i.html(n)}):rizhuti_v2_toast_msg("info","请点击验证按钮进行验证",function(){i.html(n)}):rizhuti_v2_toast_msg("info","请输入用户名或密码",function(){i.html(n)})}),$(document).on("click",".go-register",function(t){t.preventDefault();var i=$(this),n=i.text(),s=$("input[name='user_name']").val(),o=$("input[name='user_email']").val(),e=$("input[name='user_pass']").val(),a=$("input[name='user_pass2']").val(),u=$("input[name='email_verify_code']").val();i.html(iconspin+n),is_check_name(s)?is_check_mail(o)?is_qq_captcha_verify?rizhuti_v2_ajax({action:"user_register",user_name:s,user_email:o,user_pass:e,user_pass2:a,email_verify_code:u},null,function(t){1==t.status?rizhuti_v2_toast_msg("success",t.msg,function(){window.location.href=rizhutiv2.home_url+"/user"}):rizhuti_v2_toast_msg("info",t.msg,function(){i.html(n)})}):rizhuti_v2_toast_msg("info","请点击验证按钮进行验证",function(){i.html(n)}):rizhuti_v2_toast_msg("info","邮箱格式错误",function(){i.html(n)}):rizhuti_v2_toast_msg("info","用户名格式错误",function(){i.html(n)})}),$(document).on("click",".go-bind-olduser",function(t){t.preventDefault();var i=$(this),n=i.text(),s=$("input[name='bind_username']").val(),o=$("input[name='bind_password']").val();n=i.text();i.html(iconspin+n),s&&o?is_qq_captcha_verify?rizhuti_v2_ajax({action:"user_bind_olduser",username:s,password:o},null,function(t){1==t.status?rizhuti_v2_toast_msg("success",t.msg,function(){location.reload()}):rizhuti_v2_toast_msg("info",t.msg,function(){location.reload()})}):rizhuti_v2_toast_msg("info","请点击验证按钮进行验证",function(){i.html(n)}):rizhuti_v2_toast_msg("info","请输入用户名或密码",function(){i.html(n)})}),$(document).on("click",".go-rest-password",function(t){t.preventDefault();var i=$(this),n=i.text(),s=$("input[name='user_email']").val();s&&(i.html(iconspin+n),is_qq_captcha_verify?rizhuti_v2_ajax({action:"user_lostpassword",user_email:s},null,function(t){1==t.status?rizhuti_v2_toast_msg("success",t.msg,function(){window.location.href=rizhutiv2.home_url}):rizhuti_v2_toast_msg("info",t.msg)},function(t){i.html(n)}):rizhuti_v2_toast_msg("info","请点击验证按钮进行验证",function(){i.html(n)}))}),$(document).on("click",".go-set-rest-password",function(t){t.preventDefault();var i=$(this),n=i.text(),s=i.data("key"),o=i.data("uid"),e=$("input[name='user_pass']").val(),a=$("input[name='user_pass2']").val();is_qq_captcha_verify?e&&a&&rizhuti_v2_ajax({action:"user_set_lostpassword",key:s,uid:o,user_pass:e,user_pass2:a},function(t){i.html(iconspin+n)},function(t){1==t.status?rizhuti_v2_toast_msg("success",t.msg,function(){window.location.href=rizhutiv2.home_url+"/login"}):rizhuti_v2_toast_msg("info",t.msg,function(){location.reload()})},function(t){i.html(n)}):rizhuti_v2_toast_msg("info","请点击验证按钮进行验证",function(){i.html(n)})})});
</script>

<?php get_footer(); ?>



