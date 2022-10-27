<?php
defined('ABSPATH') || exit;
global $current_user;
$_nonce = wp_create_nonce('rizhuti-v2-click-' . $current_user->ID);
?>

<div class="card mb-2 mb-lg-4">
    <div class="card-header">
        <h5 class="card-title"><?php echo esc_html__('账号密码管理','rizhuti-v2');?></h5>
    </div>
    <!-- Body -->
    <div class="card-body">
        <form>
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

        	<div class="row form-group">
                <label class="col-sm-3 col-form-label input-label"><?php echo esc_html__('旧密码','rizhuti-v2');?></label>
                <div class="col-sm-9">
                <?php if (is_oauth_password()) {
                	echo '<input class="form-control" name="old_password" type="text" value="第三方一键登录账号，请设置您的登录密码" style="color:red;" disabled></input>';
               	}else{
               		echo '<input class="form-control" name="old_password" type="password" value=""></input>';
               	}?> 
                </div>
            </div>
            <div class="row form-group">
                <label class="col-sm-3 col-form-label input-label"><?php echo esc_html__('新密码','rizhuti-v2');?></label>
                <div class="col-sm-9">
                    <input class="form-control" name="new_password" type="password" value=""></input>
                </div>
            </div>

            
            <div class="row form-group">
                <label class="col-sm-3 col-form-label input-label"><?php echo esc_html__('确认新密码','rizhuti-v2');?></label>
                <div class="col-sm-9">
                    <div class="input-group">
                      <input type="password" class="form-control" name="new_password2">
                      <div class="input-group-append">
                        <button id="update-password" class="btn btn-sm btn-primary" data-nonce="<?php echo $_nonce; ?>" type="button"><?php echo esc_html__('确认修改密码','rizhuti-v2');?></button>
                      </div>
                    </div>
                </div>
            </div>
            
        </form>
    </div>
</div>


<!-- JS脚本 -->
<script type="text/javascript">
jQuery(function() {
    'use strict';
    //修改密码
    $("#update-password").on("click", function(event) {
        event.preventDefault();
        var _this = $(this);
        var deft = _this.html();
        var nonce = _this.data("nonce");
        var old_password = $("input[name='old_password']").val();
        var new_password = $("input[name='new_password']").val();
        var new_password2 = $("input[name='new_password2']").val();

        if (!is_qq_captcha_verify) {
            rizhuti_v2_toast_msg('info','请点击验证按钮进行验证');
            return;
        }
        rizhuti_v2_ajax({
            "action": "updete_password",
            "nonce": nonce,
            "old_password": old_password,
            "new_password": new_password,
            "new_password2": new_password2,
        },function(before) {
            _this.html(iconspin+'修改中...');
        },function(result) {
            if (result.status == 1) {
                rizhuti_v2_toast_msg("success",result.msg,function(){location.reload()})
            } else {
                rizhuti_v2_toast_msg("info",result.msg)
            }
        },function(complete) {
            _this.html(deft)
        });

    });

    
});
</script>
