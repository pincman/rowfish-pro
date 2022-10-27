<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;
date_default_timezone_set('Asia/Shanghai');
global $wpdb, $wppay_table_name;
// Authentication
if ( ! current_user_can( 'manage_options' ) ) {
	return;
}

$RiPlusTable = new RiPlus_List_Table();
$RiPlusTable->prepare_items();
$message = '';
if ('delete' === $RiPlusTable->current_action()) {
    $message = '<div class="updated notice notice-success is-dismissible"><p>' . sprintf(__('成功删除: %d 条记录', 'rizhuti-v2'), count($_REQUEST['wp_list_event'])) . '</p></div>';
}


$admin_pay_coin = add_query_arg(array('page' => $_GET['page'],'page_action' => 'admin_pay_coin'), admin_url('admin.php'));
$admin_pay_vip = add_query_arg(array('page' => $_GET['page'],'page_action' => 'admin_pay_vip'), admin_url('admin.php'));
$index_pay_url = add_query_arg(array('page' => $_GET['page']), admin_url('admin.php'));
$page_action = (!empty($_GET['page_action'])) ? trim($_GET['page_action']) : false;
?>

<div class="wrap">
	<h1 class="wp-heading-inline">后台充值/开通</h1>
    <a href="<?php echo $index_pay_url ?>" class="page-title-action">充值记录</a>
    <a href="<?php echo $admin_pay_coin ?>" class="page-title-action">手动充值用户余额</a>
    <a href="<?php echo $admin_pay_vip ?>" class="page-title-action">手动开通用户会员</a>
    
    <p>1、请输入正确的ID，否则无法充值</p>
	<p>2、充值用户ID为数字ID，账号ID为用户前台个人中心的账号ID</p>
    <p>3、后台充值也可参与充值送活动，活动以后台设置套餐为准</p>
    <p>4、如果要避赠送，请不要输入活动套餐设置中的金额，例如套餐中充值100送10，避免充值送，可以分开充值50两次即可。</p>
    <p>5、所有操作均有操作日志，可到余额日志查询</p>
    <p>6、开通会员注意事项：</p>
    <p>7、如果用户已经是该等级会员再次开通视为续费</p>
    <?php echo $message; ?>
	<hr class="wp-header-end">
	<div id="post-body-content">
        <?php if ('admin_pay_coin' === $page_action): ?>
            <!-- 充值余额 -->
            <?php 
            if (!empty($_POST['pay_user']) && !empty($_POST['pay_price'])) {
                //用户搜索
                $s_user = (is_numeric($_POST['pay_user'])) ? absint($_POST['pay_user']) : trim($_POST['pay_user']);
                $user_id = 0;
                if (is_numeric($_POST['pay_user'])) {
                    $user_id = absint($_POST['pay_user']);
                } else {
                    $author_obj = get_user_by('login',trim($_POST['pay_user']));
                    if (!empty($author_obj)) {
                        $user_id = $author_obj->ID;
                    }
                }
                $pay_price = (float)$_POST['pay_price'];
                $pay_coin = convert_site_mycoin($pay_price,'coin');
                $old_coin = get_user_mycoin($user_id);

                if ($user_id>0) {
                    if ($_POST['pay_price']<0) {
                        if (update_user_mycoin($user_id,$pay_coin)) {
                            $_msg = '后台扣除'.$pay_coin.'，￥'.$pay_price.'，【'.$old_coin.'=>'.get_user_mycoin($user_id).'】';
                            RiMsg::add_user_coin_log( $user_id,$_msg );
                            echo '<div class="updated notice notice-success is-dismissible"><p>扣除成功，用户ID：'.$author_obj->ID.'，扣除前余额：'.$old_coin.site_mycoin('name').'，扣除后余额：'.get_user_mycoin($user_id).site_mycoin('name').'</p></div>';
                        }
                    }else{
                        // 添加订单入库
                        $order_data = [
                            'order_price'    => sprintf('%0.2f', $pay_price), // 订单价格
                            'order_num' => date("ymdhis") . mt_rand(100, 999) . mt_rand(100, 999) . mt_rand(100, 999), //本地订单号
                            'order_type'     => 99, //订单类型
                            'pay_type'       => 77, //支付方式
                            'order_name'     => get_bloginfo('name') . esc_html__('-后台充值','rizhuti-v2'),
                            'callback_url'   => home_url(),
                            'aff_uid'        => 0,
                            'aff_ratio'      => 0,
                            'ip'             => get_client_ip(),
                        ];
                        $RiClass = new RiClass(0,$user_id);
                        if (!$RiClass->add_order($order_data)) {
                            echo '<div class="updated notice notice-success is-dismissible"><p>添加订单失败，请刷新当前页面重试</p></div>';
                        }else{
                            $trade_no = '77-'.time(); // 时间戳和消费前余额
                            $RiClass = new RiClass;
                            if ( $RiClass->send_order_trade_notify($order_data['order_num'],$trade_no) ) {
                                echo '<div class="updated notice notice-success is-dismissible"><p>充值成功，用户ID：'.$user_id.'，充值前余额：'.$old_coin.site_mycoin('name').'，充值后余额：'.get_user_mycoin($user_id).site_mycoin('name').'</p></div>';
                            }
                        }
                    }
                   
                }else{
                    echo '<div class="updated notice notice-success is-dismissible"><p>用户ID错误，无法找到该用户信息</p></div>';
                }
            }?>
            <form action="" id="poststuff" method="post">
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="pay_user">充值用户ID/账号ID</label></th>
                            <td><input class="" id="pay_user" name="pay_user" type="text" size="20" placeholder="请输入用户ID" value=""></input></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="pay_price">充值金额/元</label></th>
                            <td><input class="" id="pay_price" name="pay_price" type="number" placeholder="请输入充值金额" value=""> 元，充值汇率为1元=<?php echo convert_site_mycoin(1,'coin').site_mycoin('name');?>，输入负数为扣除</input></td>
                        </tr>
                    </tbody>
                </table>
                <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="立即充值"></p>
            </form>
        <?php elseif ('admin_pay_vip' === $page_action): ?>
            <!-- 充值VIP -->
            <?php if (!empty($_POST['pay_user']) && isset($_POST['pay_vip_type'])) {
                //用户搜索
                $s_user = (is_numeric($_POST['pay_user'])) ? absint($_POST['pay_user']) : trim($_POST['pay_user']);
                $user_id = 0;
                if (is_numeric($_POST['pay_user'])) {
                    $user_id = absint($_POST['pay_user']);
                } else {
                    $author_obj = get_user_by('login',trim($_POST['pay_user']));
                    if (!empty($author_obj)) {
                        $user_id = $author_obj->ID;
                    }
                }
                $pay_vip_type = (int)$_POST['pay_vip_type'];
                if (empty($user_id)) {
                   echo '<div class="updated notice notice-success is-dismissible"><p>用户ID错误，无法找到该用户信息</p></div>';
                }elseif ($pay_vip_type<30) {
                    echo '<div class="updated notice notice-success is-dismissible"><p>请选择开通类型</p></div>';
                }else{
                    $vip_opt = _get_ri_vip_options();
                    switch ($pay_vip_type) {
                        case '31':
                            $order_type = 2;
                            break;
                        case '365':
                            $order_type = 3;//月费会员
                            break;
                        case '3600':
                            $order_type = 4;//年费会员
                            break;
                        default:
                            $order_type = 0;//终身会员
                            break;
                    }
                    // 添加订单入库
                    $order_data = [
                        'order_price'    => sprintf('%0.2f', $vip_opt[$pay_vip_type]['price']), // 订单价格
                        'order_num' => date("ymdhis") . mt_rand(100, 999) . mt_rand(100, 999) . mt_rand(100, 999), //本地订单号
                        'order_type'     => $order_type, //订单类型
                        'pay_type'       => 77, //支付方式
                        'order_name'     => get_bloginfo('name') . esc_html__('-后台开通','rizhuti-v2'),
                        'callback_url'   => home_url(),
                        'aff_uid'        => 0,
                        'aff_ratio'      => 0,
                        'ip'             => get_client_ip(),
                    ];
                    $RiClass = new RiClass(0,$user_id);
                    if (!$RiClass->add_order($order_data)) {
                        echo '<div class="updated notice notice-success is-dismissible"><p>添加订单失败，请刷新当前页面重试</p></div>';
                    }else{
                        $trade_no = '77-'.time(); // 时间戳和消费前余额
                        $RiClass = new RiClass;
                        if ( $RiClass->send_order_trade_notify($order_data['order_num'],$trade_no) ) {
                            echo '<div class="updated notice notice-success is-dismissible"><p>开通成功，用户ID：'.$user_id.'，后台开通：'.$vip_opt[$pay_vip_type]['name'].'，到期时间：'.date('Y-m-d',_get_user_vip_endtime($user_id)).'</p></div>';
                        }
                    }
                }
            }?>
            <form action="" id="poststuff" method="post">
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="pay_user">开通用户ID/账号ID</label></th>
                            <td><input class="" id="pay_user" name="pay_user" type="text" size="20" placeholder="请输入用户ID" value=""></input></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="pay_vip_type">开通类型</label></th>
                            <td><select name="pay_vip_type" class="ewc-filter-status">
                                    <option selected="selected" value="">选择开通类型</option>
                                    <option value="31"><?php echo get_order_type_text(2);?></option>
                                    <option value="365"><?php echo get_order_type_text(3);?></option>
                                    <option value="3600"><?php echo get_order_type_text(4);?></option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="立即开通"></p>
            </form>
        <?php else: ?>
            <!-- 列表 -->
        <div class="meta-box-sortables ui-sortable">
            <form method="get">
                <?php $RiPlusTable->search_box('搜索用户', 'user_id'); ?>
                <input type="hidden" name="page" value="<?php echo $_GET['page']?>">
                <?php $RiPlusTable->display(); ?>
            </form>
        </div>
        <?php endif; ?> 
		
	</div>
	<br class="clear">
</div>


<?php
// WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Create a new table class that will extend the WP_List_Table
 */
class RiPlus_List_Table extends WP_List_Table
{

	public function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular'  => 'wp_list_event',
            'plural'    => 'wp_list_events',
            'ajax'      => false
        ));
    }



    public function no_items() {
	  _e( '没有找到相关数据' );
	}

    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $per_page     = 20;
		$current_page = $this->get_pagenum();
		$total_items  = $this->get_pagenum();


        $this->set_pagination_args( array(
            'total_items' => $this->table_data_count(),
            'per_page'    => $per_page
        ) );

        $this->_column_headers = array($columns, $hidden, $sortable);
        
        $this->process_bulk_action();
        $this->items = $this->table_data($per_page,$current_page);
    }

    public function get_columns()
    {
       $columns = [
			'cb'      => '<input type="checkbox" />',
			'user_id'    => __( '充值对象', 'rizhuti-v2' ),
            'order_type'    => __( '充值类型', 'rizhuti-v2' ),
			'order_price'    => __( '金额', 'rizhuti-v2' ),
            'create_time'    => __( '充值时间', 'rizhuti-v2' ),
            'status'    => __( '状态', 'rizhuti-v2' ),
		];

		return $columns;
    }

    public function column_default( $item, $column_name )
    {
        switch ( $column_name ) {
			case 'user_id':
                if ($author_obj = get_user_by('ID', $item['user_id'])) {
                    $u_name =$author_obj->user_login;
                }else{
                    $u_name = '游客';
                }
                return get_avatar($item['user_id'], 30).'<strong>'.$u_name.'<strong>';
            case 'order_price':
                if (site_mycoin('is')) {
                    return '<span class="badge badge-hollow">￥'.$item[$column_name].' | '.convert_site_mycoin($item[$column_name],'coin').site_mycoin('name').'</span>';
                }else{
                    return '<span class="badge badge-hollow">￥'.$item[$column_name].'</span>';
                }
                
            case 'create_time':
                return date('Y-m-d H:i:s',$item[$column_name]);
            case 'pay_type':
                global $ri_pay_type_options;
                return $ri_pay_type_options[$item[$column_name]]['name'];
            case 'pay_time':
                if (!empty($item[$column_name])) {
                    return date('Y-m-d H:i:s',$item[$column_name]);
                }else{
                    return 'N/A';
                }
            case 'order_type':
                if ($item[$column_name]==1) {
                    return '<a target="_blank" href='.get_permalink($item['post_id']).'>'.get_the_title($item['post_id']).'</a>'; 
                }else{
                    return get_order_type_text($item[$column_name]);
                }
            case 'status':
                if ($item[$column_name]==1) {
                    return '成功'; 
                }else{
                    return '失败';
                }
            default:
              return $item[ $column_name ];
		}
    }

    public function get_hidden_columns()
    {
        return array();
    }

    public function get_sortable_columns()
    {
        $sortable_columns = array(
			'id' => array( 'id', true ),
            'create_time' => array( 'create_time', false ),
            'end_time' => array( 'end_time', false ),
            'apply_time' => array( 'apply_time', false ),
            'money' => array( 'money', false ),
			'status' => array( 'status', false ),
		);

		return $sortable_columns;
    }

    public function display_tablenav( $which ) 
	{
	    ?>
	    <div class="tablenav <?php echo esc_attr( $which ); ?>">

	        <div class="alignleft actions">
	            <?php $this->bulk_actions(); ?>
	        </div>
	        <?php
	        $this->extra_tablenav( $which );
	        $this->pagination( $which );
	        ?>
	        <br class="clear" />
	    </div>
	    <?php
	}

    public function extra_tablenav( $which ) {
        global $wpdb, $testiURL, $tablename,$ri_pay_type_options,$tablet;
        if ( $which == "top" ){ ?>
            <div class="alignleft actions bulkactions">
            <?php 
            global $ri_vip_options;
            $filter = [
                ['title'=>$ri_vip_options['31'],'id'=>'2'],
                ['title'=>$ri_vip_options['365'],'id'=>'3'],
                ['title'=>$ri_vip_options['3600'],'id'=>'4'],
                ['title'=>'余额充值','id'=>'99'],
            ];
                ?>
                <select name="status" class="ewc-filter-status">
                    <option selected="selected" value="">充值类型</option>
                    <?php foreach( $filter as $item ){
                        $selected = '';
                        $_REQUEST['status'] = (!empty($_REQUEST['status'])) ? $_REQUEST['status'] : null ;
                        if( $_REQUEST['status'] == $item['id'] ){
                            $selected = ' selected = "selected"';   
                        }
                        echo '<option value="'.$item['id'].'"'.$selected.'>'.$item['title'].'</option>';
                    }?>
                </select>

                <button type="submit" id="post-query-submit" class="button">筛选</button> 
            </div>
            
            <?php
        }

        if ( $which == "bottom" ){

        }
    }


	function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],
            $item['id']
        );
    }

	public function get_bulk_actions()
    {
        $actions = array(
            'delete'    => '删除选中',
        );
        return $actions;
    }

	public function process_bulk_action() {

		if ('delete' === $this->current_action()) {
			$delete_ids = (!empty($_REQUEST['wp_list_event'])) ? esc_sql( $_REQUEST['wp_list_event'] ) : null ; 

			if ($delete_ids) {
				foreach ($_REQUEST['wp_list_event'] as $event) {
                	$this->delete_table_data($event);
            	}
			}
            
        }

	}

    private function table_data($per_page = 5, $page_number = 1 )
    {
        global $wpdb, $wppay_table_name;

		$sql = "SELECT * FROM $wppay_table_name WHERE pay_type=77";
		//根据用户查询
        if ( ! empty( $_REQUEST['s'] ) ) {
            $user_id = 0;
            if (is_numeric($_REQUEST['s'])) {
                $user_id = absint($_REQUEST['s']);
            } else {
                $author_obj = get_user_by('login', $_REQUEST['s']);
                if (!empty($author_obj)) {
                    $user_id    = $author_obj->ID;
                }
            }
            $sql .= ' AND user_id=' . esc_sql($user_id);
        }
        //状态查询
        if ( isset( $_REQUEST['status'] ) && $_REQUEST['status']!='' ) {
            $status = absint($_REQUEST['status']);
            $sql .= ' AND order_type=' . esc_sql($status);
        }
		//排序
		$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'create_time' ;
		if ( ! empty( $orderby ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $orderby );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' DESC';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

		$result = $wpdb->get_results( $sql, 'ARRAY_A' );
		return $result;
    }

    private function table_data_count() {
		 global $wpdb, $wppay_table_name;

        $sql = "SELECT COUNT(*) FROM $wppay_table_name WHERE pay_type=77";
		//根据用户查询
        if ( ! empty( $_REQUEST['s'] ) ) {
            $user_id = 0;
            if (is_numeric($_REQUEST['s'])) {
                $user_id = absint($_REQUEST['s']);
            } else {
                $author_obj = get_user_by('login', $_REQUEST['s']);
                if (!empty($author_obj)) {
                    $user_id    = $author_obj->ID;
                }
            }
            $sql .= ' AND user_id=' . esc_sql($user_id);
        }
        //状态查询
        if ( isset( $_REQUEST['status'] ) && $_REQUEST['status']!='' ) {
            $status = absint($_REQUEST['status']);
            $sql .= ' AND order_type=' . esc_sql($status);
        }

		return $wpdb->get_var( $sql );
	}

	private function delete_table_data( $id ) {
		global $wpdb,$wppay_table_name;
		$wpdb->delete(
			"$wppay_table_name",
			[ 'id' => $id ],
			[ '%d' ]
		);
	}

}