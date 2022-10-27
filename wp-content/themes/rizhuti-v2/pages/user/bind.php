<?php
defined('ABSPATH') || exit;
global $current_user;
$_nonce = wp_create_nonce('rizhuti-v2-click-' . $current_user->ID);
?>


<div class="card mb-2 mb-lg-4">
    <div class="card-header">
        <h5 class="card-title"><?php echo esc_html__('账号邮箱绑定','rizhuti-v2');?></h5>
    </div>
    <!-- Body -->
    <div class="card-body">
        <form>
            <div class="row form-group">
                <label class="col-sm-3 col-form-label input-label"><?php echo esc_html__('当前绑定邮箱','rizhuti-v2');?></label>
                <div class="col-sm-9">
                    <input class="form-control" name="email" type="text" value="<?php echo $current_user->user_email;?>" disabled></input>
                </div>
            </div>
            <?php if (_cao('is_qq_007_captcha')):?>
            <div class="row form-group">
                <label class="col-sm-3 col-form-label input-label"><?php echo esc_html__('安全验证','rizhuti-v2');?></label>
                <div class="col-sm-9">
                    <div class="input-group no-gutters">
                      <?php qq_captcha_btn(); ?>
                    </div>
                </div>
            </div>
            <?php endif;?>
            <?php if (_cao('is_site_email_captcha_verify')):?>
                <div class="row form-group">
                    <label class="col-sm-3 col-form-label input-label"><?php echo esc_html__('绑定新邮箱','rizhuti-v2');?></label>
                    <div class="col-sm-9">
                        <div class="input-group">
                          <input type="email" class="form-control pl-3" placeholder="输入要新邮箱账号" name="user_email">
                          <div class="input-group-append">
                            <button class="btn btn-sm btn-outline-secondary go-send-email-code" type="button" id="send-email-code"><?php echo esc_html__('发送验证码','rizhuti-v2');?></button>
                          </div>
                        </div>
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-sm-3 col-form-label input-label"><?php echo esc_html__('邮箱验证码','rizhuti-v2');?></label>
                    <div class="col-sm-9">
                        <div class="input-group">
                          <input type="text" class="form-control" placeholder="请输入邮箱验证码" name="email_verify_code" aria-label="邮箱验证码" aria-describedby="bind-email-code" disabled="disabled">
                          <div class="input-group-append">
                            <button class="btn btn-sm btn-primary go-bind-email" type="button" id="bind-email-code"><?php echo esc_html__('绑定新邮箱','rizhuti-v2');?></button>
                          </div>
                        </div>
                    </div>
                </div>
            <?php else:?>
                <div class="row form-group">
                    <label class="col-sm-3 col-form-label input-label"><?php echo esc_html__('绑定新邮箱','rizhuti-v2');?></label>
                    <div class="col-sm-9">
                        <div class="input-group">
                          <input type="email" class="form-control pl-3" placeholder="输入要新邮箱账号" name="user_email">
                          <div class="input-group-append">
                            <button class="btn btn-sm btn-primary go-bind-email" type="button" id="bind-email-code"><?php echo esc_html__('绑定新邮箱','rizhuti-v2');?></button>
                          </div>
                        </div>
                    </div>
                </div>
            <?php endif;?>



        </form>
    </div>
</div>


<div class="card mb-3 mb-lg-5">
    <div class="card-header">
        <h5 class="card-title"><?php echo esc_html__('第三方登录绑定','rizhuti-v2');?></h5>
    </div>
    <!-- Body -->
    <div class="card-body">
        <p class="card-text"><?php echo esc_html__('绑定第三方登录您可以快速登录账号','rizhuti-v2');?></p>
        <form>
            <div class="list-group list-group-lg list-group-flush list-group-no-gutters">
                <?php $sns_opt = [
                    'qq' => ['name'=>'QQ','icon'=>'fa fa-qq','css'=>'primary'],
                    'weixin' => ['name'=>'微信','icon'=>'fa fa-weixin','css'=>'success'],
                    'weibo' => ['name'=>'微博','icon'=>'fa fa-weibo','css'=>'danger']
                ];
                $current_url = urlencode(curPageURL());
                
               foreach ($sns_opt as $type => $opt) : if(_cao('is_sns_'.$type)): 
                    if ($type=='weixin' && _cao('sns_weixin')['sns_weixin_mod']=='mp') {
                        $type='mpweixin';
                    }
                    $openid = get_user_meta($current_user->ID, 'open_' . $type . '_openid', 1);
                    
                ?>
                <!-- List Item -->
                <div class="list-group-item">
                  <div class="media">
                    <i class="<?php echo $opt['icon'];?> text-<?php echo $opt['css'];?> list-group-icon mt-1"></i>
                    <div class="media-body">
                      <div class="row align-items-center">
                        <div class="col-sm mb-1 mb-sm-0">
                          <h6 class="mb-0"><?php echo $opt['name'];?></h6>
                          <small>
                            <?php if (!empty($openid)) {
                                echo '<strong class="mr-1">'.$opt['name'].'昵称</strong> | <strong class="ml-1">'.get_user_meta($current_user->ID,'open_' . $type . '_name',1 ).'</strong>';
                            }else{
                                echo '<strong class="mr-1">未绑定</strong>';
                            }?>
                          </small>
                        </div>
                        <div class="col-sm-auto">
                            <?php if (!empty($openid)) {
                                echo '<a href="javascript:;" class="go-unset-open btn-sm btn btn-outline-secondary" data-type="'.$type.'"><i class="'.$opt['icon'].'"></i> 解绑'.$opt['name'].'</a>';
                            }else{
                                $to_url = get_open_oauth_url($type, $current_url);
                                $to_title = '绑定'.$opt['name'];
                                if ($type=='mpweixin' && !is_weixin_visit()) {
                                    $to_url = 'javascript:;';
                                    $to_title = '请在微信内访问绑定';
                                }elseif ($type=='mpweixin') {
                                    $to_url = esc_url(home_url('/oauth/weixin?rurl='.$current_url));
                                }
                                echo '<a href="'.$to_url.'" class="btn btn-sm btn-'.$opt['css'].'"><i class="'.$opt['icon'].'"></i> 绑定'.$to_title.'</a>';
                            }?>
                        </div>

                      </div>
                    </div>
                  </div>
                </div>
                <!-- End List Item -->
                <?php endif; endforeach;?>

            </div>

            
            
        </form>
    </div>
</div>


<!-- JS脚本 -->
<script type="text/javascript">
jQuery(function() {
    'use strict';
    //解绑开放登录
    $(".go-unset-open").on("click", function(event) {
        event.preventDefault();
        var _this = $(this);
        var deft = _this.html();
        var type = _this.data("type");
        rizhuti_v2_ajax({
            "action": "unset_open_login",
            "type": type,
        }, function(before) {
            _this.html(iconspin + '解绑中...')
        }, function(result) {
            if (result.status == 1) {
                rizhuti_v2_toast_msg("success", result.msg, function() {
                    location.reload()
                })
            } else {
                rizhuti_v2_toast_msg("info", result.msg, function() {
                    location.reload()
                })
            }
        }, function(complete) {
            _this.html(deft)
        });
        return;
    });
});
</script>
