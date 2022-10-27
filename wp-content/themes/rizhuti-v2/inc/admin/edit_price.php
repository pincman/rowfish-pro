<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;
date_default_timezone_set('Asia/Shanghai');
global $wpdb;
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

?>

<div class="wrap">
	<h1>文章价格批量修改</h1>
    <br>
    <p>注意事项：修改不可逆转，请您勾选好要修改的文章价格，设置价格和会员权限类型</p>
    <p>1、价格单位为￥人民币，可以设置为0或者其他数字</p>
    <p>2、会员权限必须选择，否则修改无效，权限关系是包含关系，终身可查看年月</p>
    <p>3、价格为0时，如果启用VIP会员权限，则普通用户不能购买。只允许会员下载，反之普通用户可以购买</p>
	<p>4、为防止站长误操作造成不可逆数价格修改失误，暂未开放一键修改全站所有文章价格通道</p>
    <?php echo $message; ?>
	<hr class="wp-header-end">
	<div id="post-body-content">
		<div class="meta-box-sortables ui-sortable">
			<form method="get">
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
			'id'    => __( '文章ID', 'rizhuti-v2' ),
            'post_title'    => __( '文章标题', 'rizhuti-v2' ),
            'post_date'    => __( '发布时间', 'rizhuti-v2' ),
            'wppay_type' => __( '资源类型', 'rizhuti-v2' ),
            'wppay_price'    => __( '售价', 'rizhuti-v2' ),
            'wppay_vip_auth' => __( '会员权限', 'rizhuti-v2' ),
		];

		return $columns;
    }

    public function column_default( $item, $column_name )
    {
        switch ( $column_name ) {
            case 'id':
                return $item['ID'];
            case 'post_title':
            	if (get_permalink($item['ID'])) {
            		return '<a target="_blank" href='.get_permalink($item['ID']).'>'.get_the_title($item['ID']).'</a>'; 
            	}else{
            		return '<span style="color:red;">【文章ID-'.$item['ID'].'】-已删除</span>'; 
            	}
            case 'wppay_price':
                $meta = get_post_meta($item['ID'], 'wppay_price', true);
                $meta = ($meta=='') ? '' : '￥'.(float)$meta ;
                return $meta;
            case 'wppay_vip_auth':
                $auth = array(
                    '0' => esc_html__('不启用', 'rizhuti-v2'),
                    '1' => esc_html__('包月VIP免费', 'rizhuti-v2'),
                    '2' => esc_html__('包年VIP免费', 'rizhuti-v2'),
                    '3' => esc_html__('终身VIP免费', 'rizhuti-v2'),
                );
                $meta = get_post_meta($item['ID'], 'wppay_vip_auth', true);
                $meta = ($meta=='') ? '' : $auth[intval($meta)] ;
                return $meta;
            case 'wppay_type':
                $auth = array(
                    '0' => esc_html__('不启用', 'rizhuti-v2'),
                    '3' => esc_html__('付费下载资源', 'rizhuti-v2'),
                    '4' => esc_html__('免费下载资源', 'rizhuti-v2'),
                    '2' => esc_html__('付费隐藏内容', 'rizhuti-v2'),
                    '1' => esc_html__('付费查看全文', 'rizhuti-v2'),
                    '5' => esc_html__('付费观看视频', 'rizhuti-v2'),
                    '6' => esc_html__('付费图片相册', 'rizhuti-v2'),
                );
                $meta = get_post_meta($item['ID'], 'wppay_type', true);
                $meta = ($meta=='') ? '无' : $auth[intval($meta)] ;
                return $meta;
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
			'id' => array( 'ID', true ),
			'post_date' => array( 'post_date', false ),
		);

		return $sortable_columns;
    }

    public function display_tablenav( $which ) 
	{
	    ?>
	    <div class="tablenav <?php echo esc_attr( $which ); ?>">
            <?php if ('top' === $which) { ?>
                <div class="alignleft actions bulkactions">
                    <select name="edit_action" id="edit_action">
                        <option value="-1">批量操作</option>
                        <option value="edit_cb">修改选中文章</option>
                        <option value="edit_all">修改全部文章</option>
                    </select>
                    <input type="text" id="wppay_price" size="20" placeholder="请输入价格，单位为元/￥" name="wppay_price" value="">
                    <select name="wppay_vip_auth" id="wppay_vip_auth">
                        <option value="-1">资源会员权限</option>
                        <option value="0">不启用</option>
                        <option value="1">包月VIP免费</option>
                        <option value="2">包年VIP免费</option>
                        <option value="3">终身VIP免费</option>
                    </select>
                    <input type="submit" id="go_edit_action" class="button action" value="应用批量修改">
                </div>
            <?php } ?>
	        
	        <?php
	        $this->extra_tablenav( $which );
	        $this->pagination( $which );
	        ?>
	        <br class="clear" />
	    </div>
	    <?php
	}


	function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],
            $item['ID']
        );
    }

	public function process_bulk_action() {
        $edit_action = (!empty($_REQUEST['edit_action'])) ? esc_sql( $_REQUEST['edit_action'] ) : null ; 
        $wppay_price = (isset($_REQUEST['wppay_price']) && $_REQUEST['wppay_price'] != '') ? (float)$_REQUEST['wppay_price'] : null ; 
        $wppay_vip_auth = (isset($_REQUEST['wppay_vip_auth']) && $_REQUEST['wppay_vip_auth'] >= 0) ? (int)$_REQUEST['wppay_vip_auth'] : null ;
        if (($edit_action == 'edit_cb' || $edit_action == 'edit_all') && ($wppay_vip_auth===null || $wppay_price===null)) {
            echo '<div class="updated notice notice-success is-dismissible"><p>请设置价格和权限</p></div>';
        }
        //修改选中
		if ('edit_cb' == $edit_action && $wppay_price !== null && $wppay_vip_auth !== null) {
			$edit_ids = (!empty($_REQUEST['wp_list_event'])) ? esc_sql( $_REQUEST['wp_list_event'] ) : null ; 
            
            if (empty($edit_ids)) {
                echo '<div class="updated notice notice-success is-dismissible"><p>请勾选要修改的文章</p></div>';
            }else{
                foreach ($edit_ids as $key => $post_id) {
                    update_post_meta($post_id, 'wppay_price',$wppay_price);
                    update_post_meta($post_id, 'wppay_vip_auth', $wppay_vip_auth);
                }
                echo '<div class="updated notice notice-success is-dismissible"><p>' . sprintf( __('成功修改: %d 个文章价格权限', 'rizhuti-v2'), count($edit_ids) ) . '</p></div>';
            }
        }
        //修改全部
        if ('edit_all' == $edit_action && $wppay_price !== null && $wppay_vip_auth !== null) {
            echo '<div class="updated notice notice-success is-dismissible"><p>为防止误操作，暂未开放一次性修改全部文章价格通道</p></div>';
        }

	}

    private function table_data($per_page = 5, $page_number = 1 )
    {
        global $wpdb;

		$sql = "SELECT * FROM $wpdb->posts WHERE post_type='post' AND post_status='publish'";
		
        // $sql = "SELECT a.ID,a.post_date,b.meta_value as wppay_price FROM $wpdb->posts a LEFT JOIN $wpdb->postmeta b ON a.ID = b.post_id WHERE a.post_type='post' AND a.post_status='publish'";
        
		//排序
		$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'ID' ;
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
		global $wpdb;

        $sql = "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_type='post' AND post_status='publish'";
		
		return $wpdb->get_var( $sql );
	}

	private function delete_table_data( $id ) {
		global $wpdb;
		$wpdb->delete(
			"$wpdb->posts",
			[ 'id' => $id ],
			[ '%d' ]
		);
	}

}