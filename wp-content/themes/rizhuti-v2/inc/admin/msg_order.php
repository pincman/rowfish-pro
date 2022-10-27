<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;
global $wpdb, $msg_table_name;
// Authentication
if ( ! current_user_can( 'manage_options' ) ) {
	return;
}

wp_enqueue_style('thickbox');
wp_enqueue_script('thickbox');

$arr_itme = [
	['name' => '总工单消息','to_status' =>'all'],
	['name' => '未处理工单','to_status' => 0],
	['name' => '已读工单','to_status' => 1],
	['name' => '已处理工单','to_status' =>2],
];


?>
<!-- 编辑 -->
<?php if ( !empty($_GET['edit_id']) ) : ?>
<div class="wrap">
	<h2 style=" margin-bottom: 20px; ">回复消息工单</h2>
	<hr class="wp-header-end">
	<?php 
	if (isset($_POST['to_msg']) && isset($_POST['to_status'])) {
		if (RiMsg::update_tickets($_GET['edit_id'], $_POST['to_msg'],$_POST['to_status'])) {
			echo "工单处理成功！";
		}else{
			echo "处理失败！";
		}
	}
	$sql = "SELECT * FROM $msg_table_name WHERE 1=1 AND type=1 AND id=".$_GET['edit_id'];
	$result = $wpdb->get_row( $sql);

	?>
	<form method="post">
		<table class="form-table" role="presentation">
			<tbody>

			<tr class="user-user-login-wrap">
				<th><label>提交用户</label></th>
				<td><input type="text" value="<?php echo get_user_by('id',$result->uid)->user_login;?>" disabled="disabled"></td>
			</tr>
			
			<tr class="user-first-name-wrap">
				<th><label>提交时间</label></th>
				<td><input type="text" value="<?php echo date('Y-m-d H:i:s',$result->time);?>" disabled="disabled"></td>
			</tr>
			<tr class="user-first-name-wrap">
				<th><label>回复时间</label></th>
				<td><input type="text" value="<?php echo date('Y-m-d H:i:s',$result->to_time);?>" disabled="disabled"></td>
			</tr>
			
			<tr class="user-first-name-wrap">
				<th><label>工单内容</label></th>
				<td><textarea rows="5" cols="100" disabled="disabled"><?php echo $result->msg;?></textarea></td>
			</tr>

			<tr class="user-description-wrap">
				<th><label for="description">回复内容</label></th>
				<td><textarea name="to_msg" rows="5" cols="100"><?php echo $result->to_msg;?></textarea></td>
			</tr>
			<tr class="user-display-name-wrap">
			<th><label>状态</label></th>
			<td>
				<select name="to_status">
				<option value="0" >未处理</option>
				<option value="1" >已读</option>
				<option value="2" selected>已处理</option>
				</select>
			</td>
			</tr>
		</tbody></table>

		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="保存工单"></p>
	</form>



</div>



<?php else : ?>
<!-- 主页面 -->
<div class="wrap">
	<h2 style=" margin-bottom: 20px; ">消息工单记录总览</h2>

	<hr class="wp-header-end">
	
	<div class="layui-div">
		<!-- 导航四 -->
		<div class="layui-row layui-col-space15">
			<?php foreach ($arr_itme as $key => $item) {
				$sql = "SELECT count(id) FROM $msg_table_name WHERE type=1 AND to_uid=0";
				if ($item['to_status']==='all') {
					$sql .= '';
				}else{
					$sql .= " AND IFNULL(to_status,0)=".$item['to_status'] ;
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
			            <p class="layuiadmin-big-font"><?php echo $_sum_ok;?></p>
			          </div>
			        </div>
				</div>
			<?php } ?>
		</div>
	</div>
	
	<div id="post-body-content">
		<div class="meta-box-sortables ui-sortable">
			<?php $RiPlusTable = new RiPlus_List_Table();
			$RiPlusTable->prepare_items();?>
			<form method="get">
				<?php $RiPlusTable->search_box('根据用户ID搜索', 'user_id'); ?>
				<input type="hidden" name="page" value="<?php echo $_GET['page']?>">
				<?php $RiPlusTable->display(); ?>
			</form>
		</div>
	</div>
	<br class="clear">
</div>
<?php endif; ?>

<!-- 主页面END -->

<script type="text/javascript">
jQuery(document).ready(function($){
	$(".button.thickbox").click(function () {
    });
});
</script>



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
			'uid'    => __( '提交用户', 'rizhuti-v2' ),
			'msg'    => __( '工单内容', 'rizhuti-v2' ),
            'time'    => __( '提交时间', 'rizhuti-v2' ),
            'to_msg'    => __( '回复内容', 'rizhuti-v2' ),
            'to_time'    => __( '回复时间', 'rizhuti-v2' ),
            'to_status'    => __( '状态', 'rizhuti-v2' ),
            'edit'    => __( '操作/查看', 'rizhuti-v2' ),
		];
		return $columns;
    }

    public function column_default( $item, $column_name ){
        switch ( $column_name ) {
        	case 'edit':
        		// &TB_iframe=true&height=600&width=390 thickbox
                return '<a href="'.admin_url('admin.php?page=rizhuti_v2_msg_order_page&edit_id='.$item['id']).'" title="编辑查看" class="button ">查看/回复</a>';
                return '<span class="button edit-tickets" data-id="'.$item['id'].'">查看/回复<span>';
			case 'uid':
				if ($author_obj = get_user_by('ID', $item[$column_name])) {
                    $u_name =$author_obj->user_login;
                }else{
                    $u_name = '游客';
                }
                return get_avatar($item[$column_name], 30).'<strong>'.$u_name.'<strong>';
            case 'to_status':
            	$arr = array('未处理','已读','已处理');
            	if (empty($item[$column_name])) {
            		$item[$column_name] = 0;
            	}
            	return $arr[$item[$column_name]];
            case 'msg':
            	return wp_trim_words($item[ $column_name ],15,'...');
            case 'to_msg':
            	return wp_trim_words($item[ $column_name ],15,'...');
            case 'time':
            	return date('Y-m-d H:i:s',$item[$column_name]);
            case 'to_time':
            	if (!empty($item[$column_name])) {
            		return date('Y-m-d H:i:s',$item[$column_name]);
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
			'time' => array( 'time', true ),
			'to_time' => array( 'to_time', true ),
			'to_status' => array( 'to_status', true ),
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
	        	['title'=>'未处理','id'=>'0'],
	        	['title'=>'已读','id'=>'1'],
	        	['title'=>'已处理','id'=>'2'],

	        ];
	        if( $filter ){
	            ?>
	            <select name="status" class="ewc-filter-status">
	            	<option selected="selected" value="">处理状态</option>
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
            '0'    => '更新为未处理',
            '1'    => '更新为已读',
            '2'    => '更新为已处理',
        );
        return $actions;
    }

	public function process_bulk_action() {
		global $wpdb,$msg_table_name;
		$action = $this->current_action();
		if ( isset($action) ) {
			$update_ids = (!empty($_REQUEST['wp_list_event'])) ? esc_sql( $_REQUEST['wp_list_event'] ) : null ; 
			if ($update_ids) {
				foreach ($_REQUEST['wp_list_event'] as $id) {
					$wpdb->update($msg_table_name,array('to_status' => $this->current_action(),'to_time' => time()),array('id' => $id),array('%d','%d'),array('%d'));
            	}
            	$_url = remove_query_arg(['action','wp_list_event','action2']);
            	echo "<script type='text/javascript'>window.location.href='$_url';</script>";
			}
            
        }

	}

    
    private function table_data($per_page = 5, $page_number = 1 ){
        global $wpdb, $msg_table_name;
		$sql = "SELECT * FROM $msg_table_name WHERE 1=1 AND type=1 AND to_uid=0";
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
			$sql .= ' AND uid=' . esc_sql($user_id);
		}
		//状态查询
		if ( isset( $_REQUEST['status'] ) && $_REQUEST['status']!='' ) {
			$status = absint($_REQUEST['status']);
			$sql .= ' AND IFNULL(to_status,0)=' . $status;
		}
		
		$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'time' ;
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
		global $wpdb, $msg_table_name;

        $sql = "SELECT COUNT(*) FROM $msg_table_name WHERE 1=1 AND type=1 AND to_uid=0";
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
			$sql .= ' AND uid=' . esc_sql($user_id);
		}
		//状态查询
		if ( isset( $_REQUEST['status'] ) && $_REQUEST['status']!='' ) {
			$status = absint($_REQUEST['status']);
			$sql .= ' AND IFNULL(to_status,0)=' . $status;
		}
		return $wpdb->get_var( $sql );
	}

	

}
?>

