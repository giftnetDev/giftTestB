<?session_start();?>
<?
# =============================================================================
# 판매현황 > 엑셀변환
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================

	
	if ($code == "day") {
		$menu_right = "ST002"; // 메뉴마다 셋팅 해 주어야 합니다
		$str_menu_title = "일별 판매 현황";
		$str_list_title = "년월일";
	}

	if ($code == "month") {
		$menu_right = "ST003"; // 메뉴마다 셋팅 해 주어야 합니다
		$str_menu_title = "월별 판매 현황";
		$str_list_title = "년월";
	}

	if ($code == "goods") {
		$menu_right = "ST004"; // 메뉴마다 셋팅 해 주어야 합니다
		$str_menu_title = "상품별 판매 현황";
		$str_list_title = "상품명";
	}

	if ($order_field == "")
		$order_field = "TITLE";

	$file_name= $str_menu_title."-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
	  header( "Content-Disposition: attachment; filename=$file_name" );

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

#====================================================================
# common_header Check Session
#====================================================================
	require "../../_common/common_header.php"; 

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/stats/stats.php";

#====================================================================
# Request Parameter
#====================================================================

	if ($start_date == "") {
		$start_date = date("Y-m-d",strtotime("-1 month"));
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));
	} else {
		$end_date = trim($end_date);
	}
	
	$del_tf = "N";
#============================================================
# Page process
#============================================================
	
#	echo $start_date;
#	echo $end_date;

#===============================================================
# Get Search list count
#===============================================================

	$arr_rs = listStatsOrder($conn, $code, $sel_date_type, $start_date, $end_date, $cp_type, $cp_type2, $sel_opt_manager_no, $search_field, $search_str, $order_field, $order_str);
	$arr_rs_all = listStatsAllOrder($conn, $code, $sel_date_type, $start_date, $end_date, $cp_type, $cp_type2, $sel_opt_manager_no, $search_field, $search_str);


?>
<font size=3><b><?=$Admin_shop_name?> <?=$str_menu_title?></b></font> <br>
<br>
출력 일자 : [<?=date("Y년 m월 d일")?> ]
<br>
<br>
<TABLE border=1>
	<tr>
		<th rowspan="2" align='center' bgcolor='#F4F1EF'>항목</th>
		<th colspan="2" align='center' bgcolor='#F4F1EF'>주문완료</th>
		<th colspan="2" align='center' bgcolor='#F4F1EF'>배송완료</th>
		<th colspan="2" align='center' bgcolor='#F4F1EF'>주문취소</th>
		<th colspan="2" align='center' bgcolor='#F4F1EF'>순판매</th>
		<th rowspan="2" align='center' bgcolor='#F4F1EF'>판매이익</th>
		<th rowspan="2" align='center' bgcolor='#F4F1EF'>이익율</th>
	</tr>
	<tr>
		<th align='center' bgcolor='#F4F1EF'>수량</th>
		<th align='center' bgcolor='#F4F1EF'>합계</th>
		<th align='center' bgcolor='#F4F1EF'>수량</th>
		<th align='center' bgcolor='#F4F1EF'>합계</th>
		<th align='center' bgcolor='#F4F1EF'>수량</th>
		<th align='center' bgcolor='#F4F1EF'>합계</th>
		<th align='center' bgcolor='#F4F1EF'>수량</th>
		<th align='center' bgcolor='#F4F1EF'>합계</th>
	</tr>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							/*
							G_DATE,
							sum(TOT_ORDER_SALE_PRICE) AS TOT_ORDER_SALE_PRICE, 
							sum(TOT_ORDER_SALE_QTY) AS TOT_ORDER_SALE_QTY,
							sum(TOT_DELIVERY_SALE_PRICE) AS TOT_DELIVERY_SALE_PRICE, 
							sum(TOT_DELIVERY_SALE_QTY) AS TOT_DELIVERY_SALE_QTY,
							sum(TOT_CANCEL_SALE_PRICE) AS TOT_CANCEL_SALE_PRICE, 
							sum(TOT_CANCEL_SALE_QTY) AS TOT_CANCEL_SALE_QTY,
							sum(TOT_ORDER_SALE_PRICE) - sum(TOT_CANCEL_SALE_PRICE) AS TOT_SUN_SALE_PRICE, 
							sum(TOT_ORDER_SALE_QTY) - sum(TOT_CANCEL_SALE_QTY) AS TOT_SUN_SALE_QTY,
							(sum(TOT_ORDER_SALE_PRICE) - sum(TOT_CANCEL_SALE_PRICE)) -
							(sum(TOT_ORDER_BUY_PRICE) - sum(TOT_CANCEL_BUY_PRICE)) AS PLUS_PRICE,
							ROUND(((sum(TOT_ORDER_SALE_PRICE) - sum(TOT_ORDER_BUY_PRICE)) / 
							sum(TOT_ORDER_SALE_PRICE) * 100),2) AS LEE
							*/

							$G_DATE										= trim($arr_rs[$j]["G_DATE"]);
							$TOT_ORDER_SALE_PRICE			= trim($arr_rs[$j]["TOT_ORDER_SALE_PRICE"]);
							$TOT_ORDER_SALE_QTY				= trim($arr_rs[$j]["TOT_ORDER_SALE_QTY"]);
							$TOT_DELIVERY_SALE_PRICE	= trim($arr_rs[$j]["TOT_DELIVERY_SALE_PRICE"]);
							$TOT_DELIVERY_SALE_QTY		= trim($arr_rs[$j]["TOT_DELIVERY_SALE_QTY"]);
							$TOT_CANCEL_SALE_PRICE		= trim($arr_rs[$j]["TOT_CANCEL_SALE_PRICE"]);
							$TOT_CANCEL_SALE_QTY			= trim($arr_rs[$j]["TOT_CANCEL_SALE_QTY"]);
							$TOT_SUN_SALE_PRICE				= trim($arr_rs[$j]["TOT_SUN_SALE_PRICE"]);
							$TOT_SUN_SALE_QTY					= trim($arr_rs[$j]["TOT_SUN_SALE_QTY"]);
							$PLUS_PRICE								= trim($arr_rs[$j]["PLUS_PRICE"]);
							$LEE											= trim($arr_rs[$j]["LEE"]);
							

						?>

	<tr>
		<td bgColor='#FFFFFF' align='left'><?=$G_DATE?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($TOT_ORDER_SALE_QTY)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($TOT_ORDER_SALE_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($TOT_DELIVERY_SALE_QTY)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($TOT_DELIVERY_SALE_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($TOT_CANCEL_SALE_QTY)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($TOT_CANCEL_SALE_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($TOT_SUN_SALE_QTY)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($TOT_SUN_SALE_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($PLUS_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=$LEE?> %</td>
	</tr>
						<?
								}
							}

							if (sizeof($arr_rs_all) > 0) {
								for ($j = 0 ; $j < sizeof($arr_rs_all); $j++) {
									$TOT_ORDER_SALE_QTY				= trim($arr_rs_all[$j]["TOT_ORDER_SALE_QTY"]);
									$TOT_ORDER_SALE_PRICE			= trim($arr_rs_all[$j]["TOT_ORDER_SALE_PRICE"]);
									$TOT_DELIVERY_SALE_QTY		= trim($arr_rs_all[$j]["TOT_DELIVERY_SALE_QTY"]);
									$TOT_DELIVERY_SALE_PRICE	= trim($arr_rs_all[$j]["TOT_DELIVERY_SALE_PRICE"]);
									$TOT_CANCEL_SALE_QTY			= trim($arr_rs_all[$j]["TOT_CANCEL_SALE_QTY"]);
									$TOT_CANCEL_SALE_PRICE		= trim($arr_rs_all[$j]["TOT_CANCEL_SALE_PRICE"]);
									$TOT_SUN_SALE_QTY					= trim($arr_rs_all[$j]["TOT_SUN_SALE_QTY"]);
									$TOT_SUN_SALE_PRICE				= trim($arr_rs_all[$j]["TOT_SUN_SALE_PRICE"]);
									$PLUS_PRICE								= trim($arr_rs_all[$j]["PLUS_PRICE"]);
									$LEE											= trim($arr_rs_all[$j]["LEE"]);
								}
							}
					?>
	<tr>
		<td bgColor='#FFFFFF' align='center'>합계</td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($TOT_ORDER_SALE_QTY)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($TOT_ORDER_SALE_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($TOT_DELIVERY_SALE_QTY)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($TOT_DELIVERY_SALE_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($TOT_CANCEL_SALE_QTY)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($TOT_CANCEL_SALE_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($TOT_SUN_SALE_QTY)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($TOT_SUN_SALE_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($PLUS_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=$LEE?> %</td>
	</tr>
</table>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>