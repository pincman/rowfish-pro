<?php
defined('ABSPATH') || exit;
date_default_timezone_set(get_option('timezone_string')); //初始化本地时间
global $current_user,$ri_vip_options;
?>


<div class="card">
    <div class="card-header">
        <h5 class="card-title"><?php echo esc_html__('订单记录（最近10条）','rizhuti-v2');?></h5>
    </div>

    <!-- Body -->
    <div class="card-body">
    	<ul class="nav nav-segment nav-fill mt-0 mb-4" id="editUserTab" role="tablist">
	        <li class="nav-item">
	          <a class="nav-link active" id="all-orders-tab" data-toggle="tab" href="#all-orders" role="tab" aria-selected="true">文章资源</a>
	        </li>
	        <li class="nav-item">
	          <a class="nav-link" id="open-orders-tab" data-toggle="tab" href="#open-orders" role="tab" aria-selected="false">会员开通</a>
	        </li>
	    </ul>
	    <div class="tab-content" id="editUserTabContent">
		    <div aria-labelledby="all-orders-tab" class="tab-pane fade active show" id="all-orders" role="tabpanel">
		        <?php
		        global $wpdb, $wppay_table_name,$ri_pay_type_options;
		        $page_number = isset($_GET['paged']) ? (int)$_GET['paged'] : 1 ;
		        $per_page = 10;
		        $offset = ( $page_number - 1 ) * $per_page;
				$sql = "SELECT * FROM $wppay_table_name WHERE user_id=$current_user->ID AND order_type=1 AND status=1 ORDER BY create_time DESC LIMIT $per_page OFFSET $offset";
				$result = $wpdb->get_results( $sql, 'ARRAY_A' );
		        ?>
		        <?php if (empty($result)) : ?>
		        <!-- Empty State -->
		        <div class="text-center space-1">
		            <img class="avatar avatar-xl mb-3" src="<?php echo get_template_directory_uri();?>/assets/img/empty-state-no-data.svg">
		                <p class="card-text">暂无记录</p>
		            </img>
		        </div>
		        <!-- End Empty State -->
		        <?php else: ?>
		        <!-- End Select Group -->
		        <div class="table-responsive border-0 overflow-y-hidden">
					<table class="table mb-0 text-nowrap">
				       <thead>
							<tr class="text-uppercase text-body">
								<th scope="col">名称</th>
								<th scope="col">价格</th>
								<th scope="col">支付方式</th>
								<th scope="col">时间</th>
							</tr>
						</thead>

						<tbody>
						<?php foreach ($result as $key => $card) : ?>
							<tr class="text-uppercase text-body">
								<td class="align-middle border-top-0"><a href="<?php echo get_the_permalink($card['post_id']);?>" class="text-inherit"><?php echo get_the_title($card['post_id']);?></a></td>
								<td class="align-middle border-top-0"><?php echo $card['order_price'];?></td>
								<td class="align-middle border-top-0"><?php echo $ri_pay_type_options[$card['pay_type']]['name'];?></td>
								<td class="align-middle border-top-0"><?php echo date('Y-m-d H:i:s',$card['create_time']);?></td>
							</tr>
						<?php endforeach;?>
						</tbody>
				    </table>
				</div>
		    	<?php endif; ?>
		    </div>

		    <div aria-labelledby="open-orders-tab" class="tab-pane fade" id="open-orders" role="tabpanel">
		        <?php
		        global $wpdb, $wppay_table_name,$ri_pay_type_options;
		        $page_number = isset($_GET['paged']) ? (int)$_GET['paged'] : 1 ;
		        $per_page = 10;
		        $offset = ( $page_number - 1 ) * $per_page;
				$sql = "SELECT * FROM $wppay_table_name WHERE user_id=$current_user->ID AND order_type>1 AND status=1 ORDER BY create_time DESC LIMIT $per_page OFFSET $offset";
				$result = $wpdb->get_results( $sql, 'ARRAY_A' );
		        ?>
		        <?php if (empty($result)) : ?>
		        <!-- Empty State -->
		        <div class="text-center space-1">
		            <img class="avatar avatar-xl mb-3" src="<?php echo get_template_directory_uri();?>/assets/img/empty-state-no-data.svg">
		                <p class="card-text">暂无记录</p>
		            </img>
		        </div>
		        <!-- End Empty State -->
		        <?php else: ?>
		        <!-- End Select Group -->
		        <div class="table-responsive border-0 overflow-y-hidden">
					<table class="table mb-0 text-nowrap">
				       <thead>
							<tr class="text-uppercase text-body">
								<th scope="col">名称</th>
								<th scope="col">价格</th>
								<th scope="col">支付方式</th>
								<th scope="col">时间</th>
							</tr>
						</thead>

						<tbody>
						<?php foreach ($result as $key => $card) : ?>
							<tr class="text-uppercase text-body">
								<td class="align-middle border-top-0">
									<?php switch ($card['order_type']) {
		                            	case '2':
		                            		echo $ri_vip_options['31'];
		                            		break;
		                            	case '3':
		                            		echo $ri_vip_options['365'];
		                            		break;
		                            	case '4':
		                            		echo $ri_vip_options['3600'];
		                            		break;
		                            }?>
								</td>
								<td class="align-middle border-top-0"><?php echo $card['order_price'];?></td>
								<td class="align-middle border-top-0"><?php echo $ri_pay_type_options[$card['pay_type']]['name'];?></td>
								<td class="align-middle border-top-0"><?php echo date('Y-m-d H:i:s',$card['create_time']);?></td>

							</tr>
						<?php endforeach;?>
						</tbody>
				    </table>
				</div>
		    	<?php endif; ?>
		    </div>
		</div>

    </div>
    <!-- End Body -->
   
</div>

