<?php
defined('ABSPATH') || exit;
date_default_timezone_set(get_option('timezone_string')); //初始化本地时间
global $current_user;
$_nonce = wp_create_nonce('rizhuti-v2-click-' . $current_user->ID);
$page = 1;$limit = 20;$to_status = null;
$msgdata = RiMsg::get_tickets($current_user->ID,$to_status,$page,$limit);

?>

<div class="card mb-2 mb-lg-4">
    <div class="card-header">
    	<h5 class="card-title"><?php echo esc_html__('消息/工单中心','rizhuti-v2');?></h5>
        <span class="text-muted">为您显示最近20条工单消息记录</span>
    </div>
    <!-- Body -->

   <div class="card-body">
		<?php if (empty($msgdata)) : ?>
			<!-- Empty State -->
	        <div class="text-center space-1 p-4">
	            <img class="avatar avatar-xl mb-3" src="<?php echo get_template_directory_uri();?>/assets/img/empty-state-no-data.svg">
	                <p class="card-text">暂无记录</p>
	            </img>
	        </div>
	        <!-- End Empty State -->
		<?php else: ?>
			<ul class="list-group list-group-flush mt-0">
			<?php foreach ($msgdata as $key => $item) : ?>
			<li class="list-group-item px-0 py-4">
				<div class="media">
					<img src="<?php echo get_avatar_url($item->uid); ?>" class="rounded-circle avatar border-width-4 border-white">
					<div class="ml-2 mt-2 media-body">
						<div class="d-flex align-items-center justify-content-between">
							<div>
								<h6 class="mb-0"><?php echo get_userdata($item->uid)->display_name;?><span class="ml-2 badge badge-secondary-lighten"><i class="fa fa-bell-o mr-1"></i><?php echo RiMsg::$msg_type[$item->type];?></span></h6>
								<span class="text-muted small">提交时间 <?php echo date('Y年m月d日 H:i',$item->time);?></span>
							</div>
							<div>
								<?php 
								if ($item->type==1 && $item->to_status==2) {
									echo '<a href="#!" class="btn btn-outline-success btn-sm disabled"><i class="fa fa-check"></i> 已处理</a>';
								}elseif ($item->type==1){
									echo '<a href="#!" class="btn btn-outline-danger btn-sm disabled">处理中</a>';
								}
								?>
							</div>
						</div>
						<div class="mt-2">
							<div class="alert alert-info" role="alert"><?php echo $item->msg;?></div>
							<?php if (!empty($item->to_msg)) {
								echo '<div class="ml-4 alert alert-success" role="alert"><p class="small text-muted mb-2">客服回复 | '.date('Y-m-d H:i',$item->to_time).' </p>'.$item->to_msg.'</div>';
							}?>

						</div>
					</div>
				</div>
			</li>
			<?php endforeach;?>
			</ul>
		<?php endif;?>
		<form id="tickets-form" class="mt-4">
			<div class="form-group">
			    <textarea class="form-control" name="msg" rows="3" placeholder="输入工单内容提交新工单"></textarea>
			    <input type="hidden" name="nonce" value="<?php echo $_nonce; ?>">
			</div>
			<div class="row d-flex justify-content-end">
	        	<?php if (_cao('is_qq_007_captcha')):?>
	            <?php qq_captcha_btn(true,'col-lg-4 col-12'); ?>
	            <?php endif;?>
	            <div class="col-auto">
	            	<button type="submit" id="send-new-tickets" class="btn btn-primary">提交新工单</button>
	        	</div>
	        </div>
			
		</form>
	</div>
	
</div>


<!-- JS脚本 -->
<script type="text/javascript">
jQuery(function() {
    'use strict';
   
    
    $("#send-new-tickets").on("click", function(event) {
        event.preventDefault();
        var _this = $(this);
        var deft = _this.html();
	    var d = {};
	    var t = $('#tickets-form').serializeArray();
	    $.each(t, function() {
	      d[this.name] = this.value;
	    });
	    d['action'] = "send_new_tickets";
	    if (!d['msg']) {
	    	rizhuti_v2_toast_msg("info",'请输入工单内容'); return;
	    }
	    if (!is_qq_captcha_verify) {
            rizhuti_v2_toast_msg('info','请点击验证按钮进行验证'); return;
        }
        rizhuti_v2_ajax(d,function(before) {
          _this.html(iconspin+'提交中...');
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
