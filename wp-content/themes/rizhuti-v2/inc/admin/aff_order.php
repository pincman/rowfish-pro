<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;
global $wpdb, $wppay_table_name;
// Authentication
if ( ! current_user_can( 'manage_options' ) ) {
	return;
}
$RiPlusTable = new RiPlus_List_Table();
$RiPlusTable->prepare_items();

$arr_itme = [
	['name' => '总佣金池','aff_status' =>'all'],
	['name' => '未提现佣金池','aff_status' => 0],
	['name' => '提现中佣金池','aff_status' => 1],
	['name' => '已提现佣金池','aff_status' =>2],
];


?>

<!-- 主页面 -->
<div class="wrap">
	<h2 style=" margin-bottom: 20px; ">AFF推广记录总览</h2>

	<hr class="wp-header-end">
	
	<div class="layui-div">
		<!-- 导航四 -->
		<div class="layui-row layui-col-space15">
			<?php foreach ($arr_itme as $key => $item) {
				$sql = "SELECT SUM(round(order_price*aff_ratio,1)) FROM $wppay_table_name WHERE status=1 AND aff_uid>0 AND aff_ratio>0";
				if ($item['aff_status']==='all') {
					$sql .= '';
				}else{
					$sql .= " AND IFNULL(aff_status,0)=".$item['aff_status'] ;
				}
				$_sum_ok = $wpdb->get_var($sql);
				$_sum_ok = ($_sum_ok) ? $_sum_ok : 0 ;
				?>
				<div class="layui-col-sm6 layui-col-md3 layui-bg-gray">
					<div class="layui-card">
			          <div class="layui-card-header">
			            <span class="layui-badge-dot layui-bg-orange"></span> <?php echo $item['name'];?></span>
			          </div>
			          <div class="layui-card-body layuiadmin-card-list">
			            <p class="layuiadmin-big-font">￥<?php echo $_sum_ok;?></p>
			          </div>
			        </div>
				</div>
			<?php } ?>
		</div>
	</div>
	
	<p>提现方法：</p>
	<p>1，用户联系管理员后，管理在此页面更具用户id信息搜索改用户的所有推广订单。</p>
	<p>2，筛选出来该用户的推广订单后，计算核对提现金额。然后手动给用户转账。</p>
	<p>3，转账后选中提现的订单，批量操作-更改状态为已提现即可。</p>
	<p>4，推广奖励金额保留一位小数点四舍五入。0.1之类的奖励金额不计算</p>

	<div id="post-body-content">
		<div class="meta-box-sortables ui-sortable">
			<form method="get">
				<?php $RiPlusTable->search_box('根据推荐人ID搜索', 'user_id'); ?>
				<input type="hidden" name="page" value="<?php echo $_GET['page']?>">
				<?php $RiPlusTable->display(); ?>
			</form>
		</div>
	</div>
	<br class="clear">
</div>

<?php
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Create a new table class that will extend the WP_List_Table
 */
class RiPlus_List_Table extends WP_List_Table{

	public function __construct(){
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

    public function prepare_items(){
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

    public function get_columns(){
       $columns = [
			'cb'      => '<input type="checkbox" />',
			'order_type'    => __( '购买商品', 'rizhuti-v2' ),
			'user_id'    => __( '购买者', 'rizhuti-v2' ),
            'order_price'    => __( '订单价格', 'rizhuti-v2' ),
            'create_time'    => __( '下单时间', 'rizhuti-v2' ),
            'pay_type'    => __( '支付方式', 'rizhuti-v2' ),
            'pay_time'    => __( '支付时间', 'rizhuti-v2' ),

            'aff_uid'    => __( '推荐人', 'rizhuti-v2' ),
            'aff_ratio'    => __( '佣金比例', 'rizhuti-v2' ),
            'aff_price'    => __( '佣金金额', 'rizhuti-v2' ),
            'aff_status'    => __( '提现状态', 'rizhuti-v2' ),
            'aff_time'    => __( '操作日期', 'rizhuti-v2' ),
		];
		return $columns;
    }

    public function column_default( $item, $column_name ){
        switch ( $column_name ) {
			case 'user_id':
				if ($author_obj = get_user_by('ID', $item['user_id'])) {
                    $u_name =$author_obj->user_login;
                }else{
                    $u_name = '游客';
                }
                return get_avatar($item['user_id'], 30).'<strong>'.$u_name.'<strong>';
            case 'aff_uid':
				if ($author_obj = get_user_by('ID', $item['aff_uid'])) {
                    $u_name =$author_obj->user_login;
                }else{
                    $u_name = '游客';
                }
                return get_avatar($item['aff_uid'], 30).'<strong>'.$u_name.'<strong>';
            
            case 'aff_ratio':
            	return round(100*$item['aff_ratio'],1).'%';
            case 'aff_status':
            	$arr = array('未提现','提现中','已提现');
            	if (empty($item[$column_name])) {
            		$item[$column_name] = 0;
            	}
            	return $arr[$item[$column_name]];
            case 'order_price':
            	return '<span class="badge badge-hollow">￥'.$item[$column_name].'</span>';
            case 'create_time':
            	return date('Y-m-d H:i:s',$item[$column_name]);
            case 'aff_time':
            	if (!empty($item[$column_name])) {
            		return date('Y-m-d H:i:s',$item[$column_name]);
            	}else{
            		return 'N/A';
            	}
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
            case 'pay_num':
            	if (!empty($item[$column_name])) {
            		return $item[$column_name];
            	}else{
            		return 'N/A';
            	}
            default:
              return $item[ $column_name ];
		}
    }

    public function get_hidden_columns(){
        return array();
    }

    public function get_sortable_columns(){
        $sortable_columns = array(
			'id' => array( 'id', true ),
			'create_time' => array( 'create_time', true ),
			'pay_time' => array( 'pay_time', true ),
			'order_price' => array( 'order_price', true ),
			'status' => array( 'status', true ),
		);

		return $sortable_columns;
    }

    public function display_tablenav( $which ){
	    
	    ?>
	    
	    <div class="tablenav mb-4 <?php echo esc_attr( $which ); ?>">

	        <div class="alignleft actions">
	            <?php $this->bulk_actions(); ?>
	        </div>
	        <?php
	        $this->extra_tablenav( $which );
	        if ($which=='bottom') {
    	        $this->pagination( $which );
    	    }
	        ?>
	        <br class="clear" />
	    </div>
	    <?php
	}

    public function extra_tablenav( $which ) {
	    global $wpdb, $testiURL, $tablename, $tablet;
	    if ( $which == "top" ){
	        ?>
	        <div class="alignleft actions bulkactions">
	        <?php
	        $filter = [
	        	['title'=>'未提现','id'=>'0'],
	        	['title'=>'提现中','id'=>'1'],
	        	['title'=>'已提现','id'=>'2'],

	        ];
	        if( $filter ){
	            ?>
	            <select name="status" class="ewc-filter-status">
	            	<option selected="selected" value="">提现状态</option>
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
	            <?php
	        }
	        ?>  
	        </div>
	        <?php
	    }
	    if ( $which == "bottom" ){
	    }
	}


	function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],
            $item['id']
        );
    }

	public function get_bulk_actions(){
		$actions = array(
            '0'    => '更新为未提现',
            '1'    => '更新为提现中',
            '2'    => '更新为已提现',
        );
        return $actions;
    }

	public function process_bulk_action() {
		global $wpdb,$wppay_table_name;
		$action = $this->current_action();
		if ( isset($action) ) {
			$update_ids = (!empty($_REQUEST['wp_list_event'])) ? esc_sql( $_REQUEST['wp_list_event'] ) : null ; 
			if ($update_ids) {
				foreach ($_REQUEST['wp_list_event'] as $id) {
					$wpdb->update($wppay_table_name,array('aff_status' => $this->current_action(),'aff_time' => time()),array('id' => $id),array('%d','%d'),array('%d'));
            	}
            	$_url = remove_query_arg(['action','wp_list_event','action2']);
            	echo "<script type='text/javascript'>window.location.href='$_url';</script>";
			}
            
        }

	}

    
    private function table_data($per_page = 5, $page_number = 1 ){
        global $wpdb, $wppay_table_name;
		$sql = "SELECT *,round(order_price*aff_ratio,1) as aff_price FROM $wppay_table_name WHERE 1=1 AND status=1 AND aff_uid>0 AND aff_ratio>0";
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
			$sql .= ' AND aff_uid=' . esc_sql($user_id);
		}
		//状态查询
		if ( isset( $_REQUEST['status'] ) && $_REQUEST['status']!='' ) {
			$status = absint($_REQUEST['status']);
			$sql .= ' AND IFNULL(aff_status,0)=' . $status;
		}
		
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

        $sql = "SELECT COUNT(*) FROM $wppay_table_name WHERE 1=1 AND status=1 AND aff_uid>0 AND aff_ratio>0";
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
			$sql .= ' AND aff_uid=' . esc_sql($user_id);
		}
		//状态查询
		if ( isset( $_REQUEST['status'] ) && $_REQUEST['status']!='' ) {
			$status = absint($_REQUEST['status']);
			$sql .= ' AND IFNULL(aff_status,0)=' . $status;
		}
		return $wpdb->get_var( $sql );
	}

	

}
?>


<!-- 主页面END -->

<script type="text/javascript">
jQuery(document).ready(function($){
	// javascript
});
</script>