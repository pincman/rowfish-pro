<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;
date_default_timezone_set('Asia/Shanghai');
global $wpdb, $wppay_cdk_name;
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


$add_cdk_url = add_query_arg(array('page' => $_GET['page'],'page_action' => 'addcdk'), admin_url('admin.php'));
$index_cdk_url = add_query_arg(array('page' => $_GET['page']), admin_url('admin.php'));
$page_action = (!empty($_GET['page_action'])) ? trim($_GET['page_action']) : false;
?>

<div class="wrap">
	<h1 class="wp-heading-inline">卡密管理</h1>
    <a href="<?php echo $index_cdk_url ?>" class="page-title-action">卡密列表</a>
    <a href="<?php echo $add_cdk_url ?>" class="page-title-action">添加卡密-CDK</a>
	<p>管理卡密，生成卡密，导出卡密，删除卡密</p>
    <?php echo $message; ?>
	<hr class="wp-header-end">
	<div id="post-body-content">
        <?php if ($page_action=='addcdk'): ?>
            <!-- 添加卡密 -->
            <?php 
            if (isset($_POST['add_money']) && $_POST['add_day'] && $_POST['add_num']) {
                RiCdk::add_cdk($_POST['add_money'], $_POST['add_day'], $_POST['add_num']);
                echo '<div class="updated notice notice-success is-dismissible"><p>' . sprintf( __('成功添加: %d 条卡密', 'rizhuti-v2'), $_POST['add_num'] ) . '</p></div>';
            }?>
            <form action="" id="poststuff" method="post" name="post_add_cdk">
                <input name="action" type="hidden" value="add"></input>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="add_num">生成卡密数量</label></th>
                            <td><input class="small-text" id="add_num" min="1" name="add_num" step="1" type="number" value="1"> 个</input></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="add_day">卡密有效期/天</label></th>
                            <td><input class="small-text" id="add_day" min="1" name="add_day" step="1" type="number" value="99"> 天</input></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="add_money">卡密金额</label></th>
                            <td><input id="add_money" min="1" name="add_money" step="1" type="number" value="1"> <?php echo _cao('site_money_ua')?></input></td>
                        </tr>
                    </tbody>
                </table>
                <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="立即添加"></p>
            </form>
        <?php elseif ('outcdk' === $RiPlusTable->current_action()): ?>
            <!-- 导出卡密 -->
            <?php 
            $outcdk_ids = (!empty($_REQUEST['wp_list_event'])) ? esc_sql( $_REQUEST['wp_list_event'] ) : array() ; 
            $outcdk_text = '';
            foreach ($outcdk_ids as $key => $cdkid) {
                $cdk = $wpdb->get_var($wpdb->prepare("SELECT code FROM $wppay_cdk_name WHERE id = %d ", $cdkid));
                $outcdk_text .= $cdk.PHP_EOL;  //\r\n 为换行
            }
            ?>
            <div style="margin-top: 30px;">
                <textarea name="moderation_keys" rows="30" cols="50" id="moderation_keys" class="large-text code" readonly><?php echo $outcdk_text;?></textarea>
            </div>
   
        <?php else: ?>
            <!-- 卡密列表 -->
        <div class="meta-box-sortables ui-sortable">
            <form method="get">
                <?php $RiPlusTable->search_box('搜索卡密', 'user_id'); ?>
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
			'code'    => __( '卡密代码', 'rizhuti-v2' ),
            'code_type'    => __( '卡密类型', 'rizhuti-v2' ),
			'money'    => __( '卡密面值', 'rizhuti-v2' ),
			'create_time'    => __( '创建时间', 'rizhuti-v2' ),
			'end_time'    => __( '到期时间', 'rizhuti-v2' ),
            'apply_time'    => __( '使用时间', 'rizhuti-v2' ),
            'status'    => __( '状态', 'rizhuti-v2' ),
		];

		return $columns;
    }

    public function column_default( $item, $column_name )
    {
        switch ( $column_name ) {
			case 'code':
                return '<strong>'.$item[$column_name].'<strong>';
            case 'code_type':
            	return RiCdk::$cdk_type[$item[$column_name]];
            case 'money':
            	if (site_mycoin('is')) {
                    return '<span class="badge badge-hollow">￥'.$item[$column_name].' | '.convert_site_mycoin($item[$column_name],'coin').site_mycoin('name').'</span>';
                }else{
                    return '<span class="badge badge-hollow">￥'.$item[$column_name].'</span>';
                }

            case 'create_time':
            	if (!empty($item[$column_name])) {
                    return date('Y-m-d H:i:s',$item[$column_name]);
                }else{
                    return 'N/A';
                }
            case 'apply_time':
                if (!empty($item[$column_name])) {
                    return date('Y-m-d H:i:s',$item[$column_name]);
                }else{
                    return 'N/A';
                }
            case 'end_time':
                if (!empty($item[$column_name])) {
                    return date('Y-m-d H:i:s',$item[$column_name]);
                }else{
                    return 'N/A';
                }
            case 'status':
                if ($item[$column_name]==0 && !RiCdk::get_cdk($item['code'],true)) {
                    return '<span class="badge badge-danger">已失效</span>';
                }elseif ($item[$column_name]==1) {
                    return '<span class="badge badge-danger">'.RiCdk::$cdk_status[$item[$column_name]].'</span>';
                }else{
                    return '<span class="badge badge-hollow">'.RiCdk::$cdk_status[$item[$column_name]].'</span>';
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
            <?php $filter = [
                ['title'=>'未使用','id'=>'0'],
                ['title'=>'已使用','id'=>'1'],
            ];
                ?>
                <select name="status" class="ewc-filter-status">
                    <option selected="selected" value="">使用状态</option>
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
            'outcdk'    => '导出选中',
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
        if ('outcdk' === $this->current_action()) {
            $outcdk_ids = (!empty($_REQUEST['wp_list_event'])) ? esc_sql( $_REQUEST['wp_list_event'] ) : null ; 
        }

	}

    private function table_data($per_page = 5, $page_number = 1 )
    {
        global $wpdb, $wppay_cdk_name;

		$sql = "SELECT * FROM $wppay_cdk_name WHERE 1=1";
		//根据用户查询
		if ( ! empty( $_REQUEST['s'] ) ) {
			$sql .= ' AND code="' . trim($_REQUEST['s']) . '"';
		}
        //状态查询
        if ( isset( $_REQUEST['status'] ) && $_REQUEST['status']!='' ) {
            $status = absint($_REQUEST['status']);
            $sql .= ' AND status=' . esc_sql($status);
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
		 global $wpdb, $wppay_cdk_name;

        $sql = "SELECT COUNT(*) FROM $wppay_cdk_name WHERE 1=1";
		//根据用户查询
		if ( ! empty( $_REQUEST['s'] ) ) {
			$sql .= ' AND code="' . trim($_REQUEST['s']) . '"';
		}
        //状态查询
        if ( isset( $_REQUEST['status'] ) && $_REQUEST['status']!='' ) {
            $status = absint($_REQUEST['status']);
            $sql .= ' AND status=' . esc_sql($status);
        }

		return $wpdb->get_var( $sql );
	}

	private function delete_table_data( $id ) {
		global $wpdb,$wppay_cdk_name;
		$wpdb->delete(
			"$wppay_cdk_name",
			[ 'id' => $id ],
			[ '%d' ]
		);
	}

}