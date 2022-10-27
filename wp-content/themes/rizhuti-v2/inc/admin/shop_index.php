<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;
global $wpdb, $wppay_table_name,$ri_pay_type_options;
// Authentication
if ( ! current_user_can( 'manage_options' ) ) {
	return;
}


function query_day($star,$end){
	global $wpdb,$wppay_table_name;
	$sql = $wpdb->get_var("SELECT SUM(order_price) FROM $wppay_table_name WHERE create_time > {$star} AND create_time < {$end}");
	$sql = ($sql) ? sprintf("%.2f",$sql) : 0 ;
	$sql_ok = $wpdb->get_var("SELECT SUM(order_price) FROM $wppay_table_name WHERE create_time > {$star} AND create_time < {$end} AND status=1");
	$sql_ok = ($sql_ok) ? sprintf("%.2f",$sql_ok) : 0 ;
	return array('sum' => $sql,'sum_ok' => $sql_ok,'sum_no' => round(($sql-$sql_ok),2));
}
// 时间安排
// 时间安排
$arr_itme = [
	['name' => '今日','time' => RiPLus_Time::today(),],
	['name' => '本月','time' => RiPLus_Time::month(),],
	['name' => '今年','time' => RiPLus_Time::year(),],
];


?>

<!-- 主页面 -->
<div class="wrap">
	<h2 style=" margin-bottom: 20px; ">商城统计/总览V2</h2>
	<hr class="wp-header-end">

	<div class="layui-row layui-col-space15">

		<div class="layui-col-md9">
			<div class="layui-card">
		        <div class="layui-card-header">全部订单统计</div>
		        <div class="layui-card-body">
		        	<div class="layui-row layui-col-space15 layui-bg-gray">
					<?php foreach ($arr_itme as $key => $item) { 
						// 获取今日总订单
						$_time = $item['time'];
						$_count = $wpdb->get_var("SELECT COUNT(id) FROM $wppay_table_name WHERE create_time > {$_time[0]} AND create_time < {$_time[1]}");
						$_count = ($_count) ? $_count : 0 ;
						$_count_ok = $wpdb->get_var("SELECT COUNT(id) FROM $wppay_table_name WHERE create_time > {$_time[0]} AND create_time < {$_time[1]} AND status=1");
						$_count_ok = ($_count_ok) ? $_count_ok : 0 ;

						$_sum = $wpdb->get_var("SELECT SUM(order_price) FROM $wppay_table_name WHERE create_time > {$_time[0]} AND create_time < {$_time[1]}");
						$_sum = ($_sum) ? $_sum : 0 ;
						$_sum_ok = $wpdb->get_var("SELECT SUM(order_price) FROM $wppay_table_name WHERE create_time > {$_time[0]} AND create_time < {$_time[1]} AND status=1");
						$_sum_ok = ($_sum_ok) ? $_sum_ok : 0 ;
						?>
						<div class="layui-col-sm6 layui-col-md4">
							<div class="layui-card">
					          <div class="layui-card-header">
					            <span class="layui-badge-dot layui-bg-orange"></span> <?php echo $item['name'];?>已付款</span>
					            <span class="layui-badge layui-bg-danger "><?php echo $_count_ok;?> 条</span> 
					            <span class="layui-badge-rim layuiadmin-badge">付款率 <?php echo $retVal = ($_count_ok==0 || $_count==0) ? 0 : sprintf("%.2f",$_count_ok/$_count*100) ;?>%</span>
					          </div>
					          <div class="layui-card-body layuiadmin-card-list">
					            <p class="layuiadmin-big-font">￥<?php echo $_sum_ok;?></p>
					            <p>订单总数 <span class="layui-badge-rim"><?php echo $_count;?>条</span>
					            	
					             	<span class="layuiadmin-span-color"><span class="layui-badge-dot"></span> 订单总额 <span class="layui-badge-rim">￥<?php echo $_sum;?></span></span> </p>
					          </div>
					        </div>
						</div>
					<?php } ?>
					</div>
				</div>
			</div>

			<div class="layui-card">
				<div class="layui-card-header">本月销售统计图(近30天)</div>
				<div class="layui-card-body">
					<div class="layui-row">
		              <div class="layui-col-sm8">
		                  <div id="conversionsChart" style="width: auto;height:450px;"></div>
		              </div>
		              <div class="layui-col-sm4">
		                  <div id="conversionsChart2" style="width: auto;height:450px;"></div>
		              </div>
		            </div>
				</div>
			</div>
		</div>

		<div class="layui-col-md3">

			<div class="layui-card">
				<div class="layui-card-header">其他信息统计/总览</div>
		        <div class="layui-card-body">
		         <ul class="layui-row layui-col-space10 layui-this">
		         	<?php
		         	
		         	global $down_table_name, $msg_table_name;
		         	$value_a = wp_count_posts()->publish;
		         	$value_b = $wpdb->get_var($wpdb->prepare("SELECT COUNT(post_id) FROM $wpdb->postmeta WHERE meta_key=%s AND meta_value>%s", 'wppay_type',0));
		         	$value_c = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users");
		         	$value_d = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users WHERE DATE_FORMAT( user_registered,'%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')");
		         	$value_e = $wpdb->get_var($wpdb->prepare("SELECT SUM(meta_value) FROM $wpdb->usermeta WHERE meta_key=%s", 'mycoin'));

		         	$value_f = $wpdb->get_var("SELECT SUM(round(order_price*aff_ratio,1)) FROM $wppay_table_name WHERE status=1 AND aff_uid>0 AND aff_ratio>0");
		         	$value_r = $wpdb->get_var("SELECT COUNT(id) FROM $down_table_name WHERE DATE_FORMAT( FROM_UNIXTIME(create_time),'%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')");
		         	$value_y = $wpdb->get_var("SELECT COUNT(id) FROM $msg_table_name WHERE type=1");

		         	$info_arr = [
		         		array('title' => '文章总数','value' => $value_a ),
		         		array('title' => '资源文章数量','value' => $value_b ),
		         		array('title' => '总用户','value' => $value_c ),
		         		array('title' => '今日注册','value' => $value_d ),
		         		array('title' => '总余额池','value' => $value_e ),
		         		array('title' => '总佣金池','value' => $value_f ),
		         		array('title' => '今日下载点击量','value' => $value_r ),
		         		array('title' => '工单消息','value' => $value_y ),
		         	];

		         	foreach ($info_arr as $key => $item) { ?>
	         		<li class="layui-col-xs6">
						<a class="layadmin-backlog-body">
						  <h3><?php echo $item['title'];?></h3>
						  <p><cite><?php echo $item['value'];?></cite></p>
						</a>
					</li>
		         	<?php } ?>
                    </ul>
		        </div>
		    </div>

			<div class="layui-card">
		        <div class="layui-card-header">订单类型收入统计/总览</div>
		        <div class="layui-card-body">
					<table class="layui-table" lay-skin="line">
						<colgroup><col width="120"><col></colgroup>
						<thead>
						    <tr>
						      <th>订单类型</th>
						      <th>收入详情</th>
						    </tr> 
						</thead>
						<tbody>

						<?php
						$sql = $wpdb->get_results("SELECT order_type,sum(order_price) as sum FROM $wppay_table_name where status>0 GROUP BY order_type ");
						foreach ($sql as $k => $value) {
							$coin_text = (!site_mycoin('is')) ? '' : ' | <span class="badge badge-primary">'.convert_site_mycoin($value->sum,'coin').site_mycoin('name').'</span>';
							echo '<tr><td>'.get_order_type_text($value->order_type).'</td><td><span class="badge badge-blue">￥'.$value->sum.'</span>'.$coin_text.'</td></tr>';
						}?>
						</tbody>
					</table>   
		        </div>
		    </div>

		</div>
		
		<div class="layui-col-md12">
			<!-- ss -->
			<div class="layui-row layui-col-space15">

				<div class="layui-col-md3">
					<div class="layui-card">
						<div class="layui-card-header">最新余额日志动态</div>
						<div class="layui-card-body">
							<dl class="layuiadmin-card-status">
								<?php $result = $wpdb->get_results("SELECT * FROM $msg_table_name WHERE type=3 AND uid=0 ORDER BY time LIMIT 5 ");
								foreach ($result as $key => $value) {
									echo '<dd>';
									$_user_id = $value->to_uid;
									if ($author_obj = get_user_by('ID', $_user_id)) {
					                    $u_name =$author_obj->user_login;
					                }else{
					                    $u_name = '游客';
					                }
									echo '<div class="layui-status-img"><a href="javascript:;"><img src="'.get_avatar_url($_user_id).'"></a></div>';
									echo '<div>';
									echo '<p><strong>'.$u_name.'</strong>：'.date('Y-m-d H:i:s',$value->time).'</p>';
									echo '<p style="font-size: 10px;color: #c1bbca;">'.$value->msg.'</p>';
									echo '</div>';
									echo '</dd>';
								}?>
				            </dl>
						</div>
					</div>
				</div>

				<div class="layui-col-md3">
					<div class="layui-card">
						<div class="layui-card-header">资源销量排行(前五)</div>
						<div class="layui-card-body">
							<dl class="layuiadmin-card-status">
								<?php $result = $wpdb->get_results("select post_id,count(post_id) as sum_pay_num,sum(order_price) as sum_order_amount from $wppay_table_name where order_type=1 group by post_id,status having status=1 and sum(order_price) >0 order by count(post_id) desc limit 5");
								foreach ($result as $key => $value) {
									echo '<dd>';
									echo '<span class="layui-badge layui-bg-orange" style="margin-right: 5px;margin-top: 2px;">'.($key+1).'</span>';
									echo '<div>';
									echo '<p><a target="_blank" href='.get_permalink($value->post_id).'>'.get_the_title($value->post_id).'</a></p>';
									echo '<span>单价：￥'.get_post_meta($value->post_id,'wppay_price', 1).' | 总销售额：￥'.$value->sum_order_amount.'</span>';
									echo '</div>';
									echo '</dd>';
								}?>
				            </dl>
						</div>
					</div>
				</div>
				
				<div class="layui-col-md3">
					<div class="layui-card">
						<div class="layui-card-header">用户余额排行(前五)</div>
						<div class="layui-card-body">
							<dl class="layuiadmin-card-status">
								<?php $result = $wpdb->get_results("select user_id,meta_value from $wpdb->usermeta where meta_key='mycoin' group by user_id having meta_value>0 order by meta_value desc limit 5");
								foreach ($result as $key => $value) {
									echo '<dd>';
									echo '<div class="layui-status-img"><a href="javascript:;"><img src="'.get_avatar_url($value->user_id).'"></a></div>';
									echo '<div>';
									if ($author_obj = get_user_by('ID', $value->user_id)) {
					                    $u_name =$author_obj->user_login;
					                }else{
					                    $u_name = '游客';
					                }
									echo '<p><strong>'.$u_name.'</strong></p>';
									echo '<span>排名：<span class="layui-badge layui-bg-orange" style="margin-right: 5px;margin-top: 2px;">'.($key+1).'</span> | 余额：'.$value->meta_value.site_mycoin('name').'</span>';

									echo '</div>';
									echo '</dd>';
								}?>
				            </dl>
						</div>
					</div>
				</div>

				<div class="layui-col-md3">
					<div class="layui-card">
						<div class="layui-card-header">用户消费排行(前五)</div>
						<div class="layui-card-body">
							<dl class="layuiadmin-card-status">
								<?php $result = $wpdb->get_results("select user_id,sum(order_price) as sum_order_amount from $wppay_table_name where user_id>0 group by user_id,status having status=1 and sum(order_price) >0 order by sum(order_price) desc limit 5");
								
								foreach ($result as $key => $value) {
									echo '<dd>';
									echo '<div class="layui-status-img"><a href="javascript:;"><img src="'.get_avatar_url($value->user_id).'"></a></div>';
									echo '<div>';
									if ($author_obj = get_user_by('ID', $value->user_id)) {
					                    $u_name =$author_obj->user_login;
					                }else{
					                    $u_name = '游客';
					                }
									echo '<p><strong>'.$u_name.'</strong></p>';
									echo '<span>排名：<span class="layui-badge layui-bg-orange" style="margin-right: 5px;margin-top: 2px;">'.($key+1).'</span> | 累计消费：￥'.$value->sum_order_amount.'</span>';

									echo '</div>';
									echo '</dd>';
								}?>
				            </dl>
						</div>
					</div>
				</div>
				
			</div>

		</div>
		

	</div>

	
	<br class="clear">
</div>

<!-- 主页面END -->
<script src="https://cdn.jsdelivr.net/npm/echarts@5.0.0/dist/echarts.min.js"></script>
<script type="text/javascript">
<?php 
$day=[];
$__day=[];
// 获取30天时间
for ($i=0; $i < 30; $i++) { 
	$_day = 30-$i;
	$time=mktime(0, 0, 0, date('m'), date('d') - $_day, date('Y'));
	$day[$i] = date('Y-m-d',$time);
	$__day[$i] = $time;
}

$__sum_data = [];
$__sum_ok_data = [];
$__sum_no_data = [];
foreach ($__day as $k => $time) {
	$end=$time+24*60*60;
	$query = query_day($time,$end);
	$__sum_data[$k] = $query['sum'];
	$__sum_ok_data[$k] = $query['sum_ok'];
	$__sum_no_data[$k] = $query['sum_no'];
}

echo "var time_arr =". json_encode($day).";";
echo "var __sum_data =". json_encode($__sum_data).";";
echo "var __sum_ok_data =". json_encode($__sum_ok_data).";";
echo "var __sum_no_data =". json_encode($__sum_no_data).";";
?>

var myChart = echarts.init(document.getElementById('conversionsChart'));
var option={tooltip:{trigger:"axis",axisPointer:{type:"shadow"}},legend:{data:["总订单","已付款","未付款"]},grid:{left:"3%",right:"4%",bottom:"3%",containLabel:!0},xAxis:[{type:"category",data:time_arr}],yAxis:[{type:"value"}],series:[{name:"总订单",type:"bar",data:__sum_data},{name:"已付款",type:"bar",data:__sum_ok_data},{name:"未付款",type:"bar",data:__sum_no_data}]};

// 使用刚指定的配置项和数据显示图表。
myChart.setOption(option);

//图表2 
<?php 
$pay_type_data = [];
$sql = $wpdb->get_results("SELECT pay_type,sum(order_price) as sum FROM $wppay_table_name where status>0 GROUP BY pay_type ");

foreach ($sql as $k => $value) {
	$pay_type_data[$k] = [ 'value'=>$value->sum,'name'=>$ri_pay_type_options[$value->pay_type]['name'] ];
}
echo "var pay_type_data =". json_encode($pay_type_data).";";
?>

var myChart2 = echarts.init(document.getElementById('conversionsChart2'));
var option2={title: {
        text: '支付方式占比/金额',
        subtext: '只统计成功支付全部订单',
        left: 'center'
    },tooltip:{trigger:"item",formatter:"{a} {b}<br/> {c}元 ({d}%)"},legend:{top:"bottom"},series:[{name:"",type:"pie",radius:"70%",label:{position:"inner",fontSize:10},data:pay_type_data,itemStyle:{borderRadius:8,borderColor:"#fff",borderWidth:2},emphasis:{itemStyle:{shadowBlur:10,shadowOffsetX:0,shadowColor:"rgba(0, 0, 0, 0.5)"}}}]};
myChart2.setOption(option2);

</script>