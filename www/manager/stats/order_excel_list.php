<?session_start();?>
<?
# =============================================================================
# File Name    : 주문별 판매 현황 엑셀 
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================

	$con_order_type = "";

	$menu_right = "ST005"; // 메뉴마다 셋팅 해 주어야 합니다


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
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/payment/payment.php";
	
#====================================================================
# Request Parameter
#====================================================================

	$file_name= "주문별 판매 현황"."-".date("Ymd").".xls";
	header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
	header( "Content-Disposition: attachment; filename=$file_name" );


	$mm_subtree	 = "3";

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

	$day_0 = date("Y-m-d",strtotime("0 month"));
	$day_1 = date("Y-m-d",strtotime("-1 day"));
	$day_7 = date("Y-m-d",strtotime("-7 day"));
	$day_31 = date("Y-m-d",strtotime("-1 month"));

	if ($order_field == "")
		$order_field = "REG_DATE";

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$con_cate_01 = trim($con_cate_01);
	$con_cate_02 = trim($con_cate_02);
	$con_cate_03 = trim($con_cate_03);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
	$del_tf = "N";
#============================================================
# Page process
#============================================================

	$nPage = 1;

	$nPageSize = 1000;
	
#	echo $start_date;
#	echo $end_date;

#===============================================================
# Get Search list count
#===============================================================

	$arr_rs = listManagerOrder($conn, $con_order_type, $search_date_type, $start_date, $end_date, $sel_order_state, $cp_type, $cp_type2, $sel_pay_type, $sel_opt_manager_no, $con_use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize);



?>
<font size=3><b>주문별 판매 현황</b></font> <br>
<br>
출력 일자 : [<?=date("Y년 m월 d일")?> ]
<br>
<br>
<TABLE border=1>

	<tr>
		<th align='center' bgcolor='#F4F1EF'>주문번호</th>
		<th align='center' bgcolor='#F4F1EF'>주문일시</th>
		<th align='center' bgcolor='#F4F1EF'>업체명</th>
		<th align='center' bgcolor='#F4F1EF'>주문자명</th>
		<th align='center' bgcolor='#F4F1EF'>수령자명</th>
		<th align='center' bgcolor='#F4F1EF'>주문총판매가</th>
		<th align='center' bgcolor='#F4F1EF'>주문총수량</th>
		<th align='center' bgcolor='#F4F1EF'>주문총할인</th>
		<th align='center' bgcolor='#F4F1EF'>주문총추가배송비</th>
		<th align='center' bgcolor='#F4F1EF'>주문합계</th>
		<th align='center' bgcolor='#F4F1EF'>주문총매입가</th>
		<th align='center' bgcolor='#F4F1EF'>주문총수수료</th>
		<th align='center' bgcolor='#F4F1EF'>주문총매입합계</th>
		<th align='center' bgcolor='#F4F1EF'>주문총판매이익</th>
		<th align='center' bgcolor='#F4F1EF'>주문총마진률</th>
		<th align='center' bgcolor='#F4F1EF'>영업담당</th>

		<th align='center' bgcolor='#F4F1EF'>외부주문번호</th>
		<th align='center' bgcolor='#F4F1EF'>요청일</th>
		<th align='center' bgcolor='#F4F1EF'>처리일</th>
		<th align='center' bgcolor='#F4F1EF'>공급사</th>
		<th align='center' bgcolor='#F4F1EF'>상품코드</th>
		<th align='center' bgcolor='#F4F1EF'>상품명</th>
		<th align='center' bgcolor='#F4F1EF'>판매가</th>
		<th align='center' bgcolor='#F4F1EF'>수량</th>
		<th align='center' bgcolor='#F4F1EF'>할인</th>
		<th align='center' bgcolor='#F4F1EF'>추가배송비</th>
		<th align='center' bgcolor='#F4F1EF'>소계</th>
		<th align='center' bgcolor='#F4F1EF'>매입가</th>
		<th align='center' bgcolor='#F4F1EF'>수수료</th>
		<th align='center' bgcolor='#F4F1EF'>매입원가</th>
		<th align='center' bgcolor='#F4F1EF'>판매이익</th>
		<th align='center' bgcolor='#F4F1EF'>마진율</th>
		<th align='center' bgcolor='#F4F1EF'>주문상태</th>
	</tr>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$rn										= trim($arr_rs[$j]["rn"]);
							$RESERVE_NO							= trim($arr_rs[$j]["RESERVE_NO"]);
							$MEM_TYPE							= trim($arr_rs[$j]["MEM_TYPE"]);
							$MEM_NO								= trim($arr_rs[$j]["MEM_NO"]);
							$CP_NO								= trim($arr_rs[$j]["CP_NO"]);
							
							$PAY_TYPE							= trim($arr_rs[$j]["PAY_TYPE"]);
							$PAY_STATE					  		= trim($arr_rs[$j]["PAY_STATE"]);
							$O_MEM_NM							= trim($arr_rs[$j]["O_MEM_NM"]);
							$R_MEM_NM							= trim($arr_rs[$j]["R_MEM_NM"]);
							$OPT_MANAGER_NO						= trim($arr_rs[$j]["OPT_MANAGER_NO"]);
							$TOTAL_PRICE				= trim($arr_rs[$j]["TOTAL_PRICE"]);
							$TOTAL_BUY_PRICE			= trim($arr_rs[$j]["TOTAL_BUY_PRICE"]);
							$TOTAL_SALE_PRICE			= trim($arr_rs[$j]["TOTAL_SALE_PRICE"]);
							$TOTAL_EXTRA_PRICE			= trim($arr_rs[$j]["TOTAL_EXTRA_PRICE"]);
							$TOTAL_QTY					= trim($arr_rs[$j]["TOTAL_QTY"]);
							$TOTAL_DELIVERY_PRICE		= trim($arr_rs[$j]["TOTAL_DELIVERY_PRICE"]);
							$TOTAL_SA_DELIVERY_PRICE	= trim($arr_rs[$j]["TOTAL_SA_DELIVERY_PRICE"]);
							$TOTAL_DISCOUNT_PRICE		= trim($arr_rs[$j]["TOTAL_DISCOUNT_PRICE"]);
							$TOTAL_SUM_SALE_PRICE		= trim($arr_rs[$j]["TOTAL_SUM_SALE_PRICE"]);
							$TOTAL_SUM_EXTRA_PRICE		= trim($arr_rs[$j]["TOTAL_SUM_EXTRA_PRICE"]);
							
							$TOTAL_PLUS_PRICE			= trim($arr_rs[$j]["TOTAL_PLUS_PRICE"]);
							$TOTAL_SUM_PRICE			= trim($arr_rs[$j]["TOTAL_SUM_PRICE"]);
							$LEE									= trim($arr_rs[$j]["LEE"]);
							
							$ORDER_DATE						= trim($arr_rs[$j]["ORDER_DATE"]);
							$PAY_DATE							= trim($arr_rs[$j]["PAY_DATE"]);
							$CANCEL_DATE					= trim($arr_rs[$j]["CANCEL_DATE"]);

							$DELIVERY_TYPE			= trim($arr_rs[$j]["DELIVERY_TYPE"]);
							$REG_DATE						= trim($arr_rs[$j]["REG_DATE"]);
							
							$ORDER_DATE		= date("Y-m-d H:i",strtotime($ORDER_DATE));

							$admName = getAdminName($conn, $OPT_MANAGER_NO);
							
							///////////////////////////////////////////////////////////////////////////////
							// 주문상품리스트
							///////////////////////////////////////////////////////////////////////////////

							$arr_goods = listManagerOrderGoods($conn, $RESERVE_NO, $MEM_NO, "Y", "N");

							if (sizeof($arr_goods) > 0) {
								for ($h = 0 ; $h < sizeof($arr_goods); $h++) {
									
									$CP_ORDER_NO				= trim($arr_goods[$h]["CP_ORDER_NO"]);
									$BUY_CP_NO					= trim($arr_goods[$h]["BUY_CP_NO"]);
									$CATE_01					= SetStringFromDB(trim($arr_goods[$h]["CATE_01"]));
									$GOODS_CODE					= SetStringFromDB(trim($arr_goods[$h]["GOODS_CODE"]));
									$GOODS_NAME					= SetStringFromDB(trim($arr_goods[$h]["GOODS_NAME"]));
									$PRICE						= trim($arr_goods[$h]["PRICE"]);
									$BUY_PRICE					= trim($arr_goods[$h]["BUY_PRICE"]);
									$SALE_PRICE					= trim($arr_goods[$h]["SALE_PRICE"]);
									$EXTRA_PRICE				= trim($arr_goods[$h]["EXTRA_PRICE"]);
									$DELIVERY_PRICE				= trim($arr_goods[$h]["DELIVERY_PRICE"]);
									$SA_DELIVERY_PRICE			= trim($arr_goods[$h]["SA_DELIVERY_PRICE"]);
									$DISCOUNT_PRICE				= trim($arr_goods[$h]["DISCOUNT_PRICE"]);

									$SUM_PRICE					= trim($arr_goods[$h]["SUM_PRICE"]);
									$PLUS_PRICE					= trim($arr_goods[$h]["PLUS_PRICE"]);
									$GOODS_LEE					= trim($arr_goods[$h]["LEE"]);
									$QTY						= trim($arr_goods[$h]["QTY"]);
									$ORDER_DATE					= trim($arr_goods[$h]["ORDER_DATE"]);
									$REQ_DATE					= trim($arr_goods[$h]["PAY_DATE"]);
									$END_DATE					= trim($arr_goods[$h]["FINISH_DATE"]);
									$ORDER_STATE				= trim($arr_goods[$h]["ORDER_STATE"]);


									if ($ORDER_DATE <> "")  {
										$ORDER_DATE		= date("Y-m-d H:i",strtotime($ORDER_DATE));
									}

									if ($REQ_DATE <> "")  {
										$REQ_DATE		= date("Y-m-d H:i",strtotime($REQ_DATE));
									}
									
									if ($END_DATE <> "")  {
										$END_DATE		= date("Y-m-d H:i",strtotime($END_DATE));
									}
									

									if ($h == (sizeof($arr_goods)-1)) {

										if ($ORDER_STATE == "1") {
											$str_tr = "class='goods_1_end'";
										} else if ($ORDER_STATE == "3") {
											$str_tr = "class='goods_3_end'";
										} else {
											$str_tr = "class='goods_end'";
										}

									} else {

										if ($ORDER_STATE == "1") {
											$str_tr = "class='goods_1'";
										} else if ($ORDER_STATE == "3") {
											$str_tr = "class='goods_3'";
										} else {
											$str_tr = "class='goods'";
										}
									}
									
									if($CATE_01 <> "")
										$str_cate_01 = $CATE_01.") ";
									else 
										$str_cate_01 = "";

									$str_price_class = "price";
									$str_state_class = "state";
									
									$STR_QTY = number_format($QTY);

									if (($ORDER_STATE == "4") || ($ORDER_STATE == "6") || ($ORDER_STATE == "7") || ($ORDER_STATE == "8")) {
										
										if ($ORDER_STATE == "4") {
											
											$BUY_PRICE = 0;
											$SALE_PRICE = 0;
											$EXTRA_PRICE = 0;
											$STR_QTY = "[".number_format($QTY)."]";
											$SUM_PRICE = 0;
											$PLUS_PRICE = 0;
											$GOODS_LEE = 0;
											$PRICE = 0;
											$SA_DELIVERY_PRICE = 0;
											$DISCOUNT_PRICE = 0;

											$REQ_DATE = $ORDER_DATE;
											$END_DATE = $ORDER_DATE;
											$str_price_class = "price_cancel";
											$str_state_class = "state_cancel";

										} else {
											$BUY_PRICE = -$BUY_PRICE;
											$SALE_PRICE = -$SALE_PRICE;
											$EXTRA_PRICE = -$EXTRA_PRICE;
											$STR_QTY = number_format(-$QTY);
											$SUM_PRICE = -$SUM_PRICE;
											$PLUS_PRICE = -$PLUS_PRICE;
											$GOODS_LEE = - $GOODS_LEE;
											$PRICE = -$PRICE;
											$SA_DELIVERY_PRICE = -$SA_DELIVERY_PRICE;
											$DISCOUNT_PRICE = -$DISCOUNT_PRICE;

											$REQ_DATE = $ORDER_DATE;
											$END_DATE = $ORDER_DATE;

											$str_price_class = "price_refund";
											$str_state_class = "state_refund";
										}
									} 
									
						?>

						<tr height="37">
							<td><?=$RESERVE_NO?></td>
							<td><?=$ORDER_DATE?></td>
							<td><?= getCompanyName($conn, $CP_NO);?></td>
							<td><?=$O_MEM_NM?></td>
							<td><?=$R_MEM_NM?></td>
							<td><?=number_format($TOTAL_SALE_PRICE)?></td>
							<td><?=number_format($TOTAL_QTY)?></td>
							<td><?=number_format($TOTAL_DISCOUNT_PRICE)?></td>
							<td><?=number_format($TOTAL_SA_DELIVERY_PRICE)?></td>
							<td><?=number_format($TOTAL_SUM_SALE_PRICE)?></td>
							<td><?=number_format($TOTAL_PRICE)?></td>
							<td><?=number_format($TOTAL_EXTRA_PRICE)?></td>
							<td><?=number_format($TOTAL_PRICE + $TOTAL_EXTRA_PRICE)?></td>
							<td><?=number_format($TOTAL_PLUS_PRICE)?></td>
							<td><?=$LEE?>%</td>
							<td><?=$admName?></td>
							
							<td><?=$CP_ORDER_NO?></td>
							<td><?=$REQ_DATE?></td>
							<td><?=$END_DATE?></td>
							<td><?=getCompanyName($conn, $BUY_CP_NO)?></td> 
							<td><?=$GOODS_CODE?></td>
							<td><?=$str_cate_01?><?=$GOODS_NAME?></td>
							<td><?=number_format($SALE_PRICE)?></td>
							<td><?=$STR_QTY?></td>
							<td><?=number_format($DISCOUNT_PRICE)?></td>
							<td><?=number_format($SA_DELIVERY_PRICE)?></td>
							<td><?=number_format($SUM_PRICE)?></td>
							<td><?=number_format($PRICE * $QTY)?></td>
							<td><?=number_format($EXTRA_PRICE * $QTY)?></td>
							<td><?=number_format($PRICE * $QTY + $EXTRA_PRICE * $QTY)?></td>
							<td><?=number_format($PLUS_PRICE)?></td>
							<td><?=$GOODS_LEE?>%</td>
							<td><?=getDcodeName($conn, "ORDER_STATE", $ORDER_STATE);?></td>
						</tr>
						
						<?

								}
							}
						} 
					}
				?>
</table>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>