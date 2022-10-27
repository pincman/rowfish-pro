<?php
defined('ABSPATH') || exit;
global $current_user;
$_nonce = wp_create_nonce('rizhuti-v2-click-' . $current_user->ID);
?>


<div class="card mb-3 mb-lg-5">
    <div class="card-header">
        <h5 class="card-title"><?php echo esc_html__('个人基本信息', 'rizhuti-v2'); ?></h5>
    </div>
    <!-- Body -->
    <div class="card-body">
        <form id="post-form">

            <div class="row form-group">
                <div class="col-12">
                    <span class="btn avatarinfo">
                        <label for="addPic">
                            <img src="<?php echo get_avatar_url($current_user->ID); ?>" height="50" class="mr-2">
                            <a class="upload" data-toggle="tooltip" data-placement="right" title="请上传80x80尺寸的JPG/PNG头像，最大可上传80KB"><i class="fa fa-camera"></i><input type="file" name="addPic" id="addPic" accept=".jpg, .gif, .png" resetonclick="true" data-nonce="<?php echo $_nonce; ?>">
                            </a>
                        </label>
                    </span>

                </div>
                <label class="col-sm-3 col-form-label input-label"><?php echo esc_html__('账号ID', 'rizhuti-v2'); ?></label>
                <div class="col-sm-9">
                    <input class="form-control" name="loginID" type="text" value="<?php echo $current_user->user_login; ?>" disabled></input>
                </div>
            </div>
            <div class="row form-group">
                <label class="col-sm-3 col-form-label input-label"><?php echo esc_html__('昵称', 'rizhuti-v2'); ?></label>
                <div class="col-sm-9">
                    <input class="form-control" name="nickname" type="text" value="<?php echo $current_user->display_name; ?>"></input>
                </div>
            </div>

            <div class="row form-group">
                <label class="col-sm-3 col-form-label input-label"><?php echo esc_html__('QQ', 'rizhuti-v2'); ?></label>
                <div class="col-sm-9">
                    <input class="form-control" name="qq" type="text" value="<?php echo get_user_meta($current_user->ID, 'qq', 1); ?>"></input>
                </div>
            </div>
            <div class="row form-group">
                <label class="col-sm-3 col-form-label input-label"><?php echo esc_html__('介绍', 'rizhuti-v2'); ?></label>
                <div class="col-sm-9">
                    <textarea name="description" rows="4" class="form-control pl-3" placeholder="请输入个人介绍 :"><?php echo get_user_meta($current_user->ID, 'description', true) ?></textarea>
                </div>
            </div>



        </form>
    </div>
    <!-- End Body -->
    <!-- Footer -->
    <div class="card-footer d-flex justify-content-end">
        <button id="save-userinfo" type="button" class="btn btn-primary" data-nonce="<?php echo $_nonce; ?>">保存个人信息</button>
    </div>
    <!-- End Footer -->
</div>


<!-- JS脚本 -->
<script type="text/javascript">
    jQuery(function() {
        'use strict';

        //保存个人信息
        $("#save-userinfo").on("click", function(event) {
            event.preventDefault();
            var _this = $(this);
            var deft = _this.html();
            var toast_type = "success";
            var d = {};
            var t = $('#post-form').serializeArray();
            $.each(t, function() {
                d[this.name] = this.value;
            });
            d['action'] = "seav_userinfo";
            d['nonce'] = _this.data("nonce");
            rizhuti_v2_ajax(d, function(before) {
                _this.html(iconspin + '保存中...')
            }, function(result) {
                if (result.status == 0) {
                    toast_type = "info";
                }
                rizhuti_v2_toast_msg(toast_type, result.msg, function() {
                    location.reload()
                })
            }, function(complete) {
                _this.html(deft)
            });

        });

    });
</script>