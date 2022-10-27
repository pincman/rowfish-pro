<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;
date_default_timezone_set('Asia/Shanghai');
global $wpdb, $msg_table_name;
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

$page_action = (!empty($_GET['page_action'])) ? trim($_GET['page_action']) : false;
?>

<div class="wrap">
	<h1 class="wp-heading-inline">用户余额日志查询</h1>
	<p>查询用户站内余额日志，显示关联订单信息，消费前后余额</p>
    <?php echo $message; ?>
	<hr class="wp-header-end">
	<div id="post-body-content">
        <div class="meta-box-sortables ui-sortable">
            <form method="get">
                <?php $RiPlusTable->search_box('搜索用户日志', 'user_id'); ?>
                <input type="hidden" name="page" value="<?php echo $_GET['page']?>">
                <?php $RiPlusTable->display(); ?>
            </form>
        </div>
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
            'to_uid'    => __( '用户', 'rizhuti-v2' ),
            'vip_type'    => __( '用户等级', 'rizhuti-v2' ),
			'mycoin'    => __( '当前余额', 'rizhuti-v2' ),
            'type'    => __( '类型', 'rizhuti-v2' ),
			'time'    => __( '时间', 'rizhuti-v2' ),
            'msg'    => __( '日志详情', 'rizhuti-v2' ),
            // 'to_status'    => __( '状态', 'rizhuti-v2' ),
		];

		return $columns;
    }

    public function column_default( $item, $column_name )
    {
        switch ( $column_name ) {
			case 'to_uid':
                if ($author_obj = get_user_by('ID', $item[$column_name])) {
                    $u_name =$author_obj->user_login;
                }else{
                    $u_name = '游客';
                }
                return get_avatar($item[$column_name], 30).'<strong>'.$u_name.'<strong>';
            case 'vip_type':
                global $ri_vip_options;
                $vip_type = _get_user_vip_type($item['to_uid']);
                return $ri_vip_options[$vip_type];
            case 'mycoin':
                return get_user_mycoin($item['to_uid']).site_mycoin('name');
            case 'type':
            	return RiMsg::$msg_type[$item[$column_name]];
            case 'time':
            	if (!empty($item[$column_name])) {
                    return date('Y-m-d H:i:s',$item[$column_name]);
                }else{
                    return 'N/A';
                }
            case 'to_status':
                return RiMsg::$msg_status[$item[$column_name]];
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
            'uid' => array( 'uid', false ),
            'time' => array( 'time', false ),
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
        if ('outcdk' === $this->current_action()) {
            $outcdk_ids = (!empty($_REQUEST['wp_list_event'])) ? esc_sql( $_REQUEST['wp_list_event'] ) : null ; 
        }

	}

    private function table_data($per_page = 5, $page_number = 1 )
    {
        global $wpdb, $msg_table_name;

		$sql = "SELECT * FROM $msg_table_name WHERE uid=0 AND type=3";
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
            $sql .= ' AND to_uid=' . esc_sql($user_id);
        }
        //状态查询
        if ( isset( $_REQUEST['status'] ) && $_REQUEST['status']!='' ) {
            $status = absint($_REQUEST['status']);
            $sql .= ' AND to_status=' . esc_sql($status);
        }
		//排序
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

        $sql = "SELECT COUNT(*) FROM $msg_table_name WHERE uid=0 AND type=3";
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
            $sql .= ' AND to_uid=' . esc_sql($user_id);
        }
        //状态查询
        if ( isset( $_REQUEST['status'] ) && $_REQUEST['status']!='' ) {
            $status = absint($_REQUEST['status']);
            $sql .= ' AND to_status=' . esc_sql($status);
        }

		return $wpdb->get_var( $sql );
	}

	private function delete_table_data( $id ) {
		global $wpdb,$msg_table_name;
		$wpdb->delete(
			"$msg_table_name",
			[ 'id' => $id ],
			[ '%d' ]
		);
	}

}