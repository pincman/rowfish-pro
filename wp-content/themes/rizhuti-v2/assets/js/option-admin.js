jQuery(document).ready(function($) {
    //刷新菜单
    $(document).on('click', ".rest_mpweixin_menu", function(event) {
        event.preventDefault()
        $.post(rizhuti_option_js.admin_url, {
            "action": "rest_mpweixin_menu",
        }, function(data) {
            // console.log(data)
            if (data.errcode == 0) {
                alert('公众号菜单更新成功');
            } else {
                alert(data.errmsg);
            }
        });
    });
    //END
});
