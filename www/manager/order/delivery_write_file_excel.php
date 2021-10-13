<?session_start();?>
<?

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "OD007"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

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

	if ($s_adm_cp_type == "구매" || $s_adm_cp_type == "판매공급") { 
		$cp_type = $s_adm_com_code;
	}

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

	$file_name="송장등록용리스트-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
	  header( "Content-Disposition: attachment; filename=$file_name" );
	  header( "Content-Description: orion70kr@gmail.com" );
	
	//$sel_order_state = "2";
	$con_cate_01 = trim($con_cate_01);
	$con_cate_02 = trim($con_cate_02);
	$con_cate_03 = trim($con_cate_03);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);

	$con_use_tf		= "Y";
	$del_tf				= "N";
	$nPage				= "1";
	$nPageSize		= "100000";
	
	$arr_rs = listManagerDelivery($conn, $start_date, $end_date, $sel_order_state, $cp_type, $cp_type2,  $con_work_flag, $sel_opt_manager_no, $sel_delivery_type, $sel_delivery_cp, $con_use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<style>td { mso-number-format:\@; } </style> 
</head>

<body>

<font size=3><b><?=$Admin_shop_name?> 배송 리스트 </b></font> <br>
<br>
출력 일자 : [<?=date("Y년 m월 d일")?> ]
<br>
주문 일자 : [<?=$start_date?> ~ <?=$end_date?>]
<br>
<br>
<TABLE border=1>
	<tr>
		<th>일련번호(상품별)</th>
		<th>주문번호</th>
		<th>상품명</th>
		<th>수량</th>
		<th>옵션</th>
		<th>수령자명</th>
		<th>수령자연락처</th>
		<th>우편번호</th>
		<th>주소</th>
		<th>택배사</th>
		<th>송장번호</th>
		<th>업체주문번호</th>
		<th>판매업체명</th>
		<th>공급업체명</th>
	</tr>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							$RESERVE_NO						= trim($arr_rs[$j]["RESERVE_NO"]);
							$MEM_TYPE							= trim($arr_rs[$j]["MEM_TYPE"]);
							$MEM_NO								= trim($arr_rs[$j]["MEM_NO"]);
							$CP_NO								= trim($arr_rs[$j]["CP_NO"]);
							
							//$ORDER_STATE					= trim($arr_rs[$j]["ORDER_STATE"]);
							$PAY_STATE						= trim($arr_rs[$j]["PAY_STATE"]);
							$O_MEM_NM							= trim($arr_rs[$j]["O_MEM_NM"]);
							$O_PHONE							= trim($arr_rs[$j]["O_PHONE"]);
							$O_HPHONE							= trim($arr_rs[$j]["O_HPHONE"]);
							$R_MEM_NM							= trim($arr_rs[$j]["R_MEM_NM"]);
							$R_ZIPCODE						= trim($arr_rs[$j]["R_ZIPCODE"]);
							$R_ADDR1							= trim($arr_rs[$j]["R_ADDR1"]);
							$R_PHONE							= trim($arr_rs[$j]["R_PHONE"]);
							$R_HPHONE							= trim($arr_rs[$j]["R_HPHONE"]);
							
							$ORDER_DATE						= trim($arr_rs[$j]["ORDER_DATE"]);
							$PAY_DATE							= trim($arr_rs[$j]["PAY_DATE"]);
							$CANCEL_DATE					= trim($arr_rs[$j]["CANCEL_DATE"]);

							$DELIVERY_TYPE			= trim($arr_rs[$j]["DELIVERY_TYPE"]);
							$REG_DATE						= trim($arr_rs[$j]["REG_DATE"]);
							
							$ORDER_DATE		= date("Y-m-d H:i:s",strtotime($ORDER_DATE));

							$arr_goods = listManagerOrderGoods($conn, $RESERVE_NO, $MEM_NO, "Y", "N");

							if (sizeof($arr_goods) > 0) {

								for ($h = 0 ; $h < sizeof($arr_goods); $h++) {
									
									$ORDER_GOODS_NO			= trim($arr_goods[$h]["ORDER_GOODS_NO"]);
									$RESERVE_NO					= trim($arr_goods[$h]["RESERVE_NO"]);
									$BUY_CP_NO					= trim($arr_goods[$h]["BUY_CP_NO"]);
									$GOODS_NO						= trim($arr_goods[$h]["GOODS_NO"]);
									$GOODS_NAME					= SetStringFromDB($arr_goods[$h]["GOODS_NAME"]);
									
									$DELIVERY_CP				= trim($arr_goods[$h]["DELIVERY_CP"]);
									$DELIVERY_NO				= trim($arr_goods[$h]["DELIVERY_NO"]);
									$CP_ORDER_NO				= trim($arr_goods[$h]["CP_ORDER_NO"]);

									$ORDER_STATE				= trim($arr_goods[$h]["ORDER_STATE"]);
									$QTY								= trim($arr_goods[$h]["QTY"]);
									
									$GOODS_OPTION_01		= trim($arr_goods[$h]["GOODS_OPTION_01"]);
									$GOODS_OPTION_02		= trim($arr_goods[$h]["GOODS_OPTION_02"]);
									$GOODS_OPTION_03		= trim($arr_goods[$h]["GOODS_OPTION_03"]);
									$GOODS_OPTION_04		= trim($arr_goods[$h]["GOODS_OPTION_04"]);
									$GOODS_OPTION_NM_01	= trim($arr_goods[$h]["GOODS_OPTION_NM_01"]);
									$GOODS_OPTION_NM_02	= trim($arr_goods[$h]["GOODS_OPTION_NM_02"]);
									$GOODS_OPTION_NM_03	= trim($arr_goods[$h]["GOODS_OPTION_NM_03"]);
									$GOODS_OPTION_NM_04	= trim($arr_goods[$h]["GOODS_OPTION_NM_04"]);

									$option_str = "";

									if ($GOODS_OPTION_NM_01 <> "") {
										$option_str .= $GOODS_OPTION_NM_01." : ".$GOODS_OPTION_01."<br />";
									}

									if ($GOODS_OPTION_NM_02 <> "") {
										$option_str .= $GOODS_OPTION_NM_02." : ".$GOODS_OPTION_02."<br />";
									}

									if ($GOODS_OPTION_NM_03 <> "") {
										$option_str .= $GOODS_OPTION_NM_03." : ".$GOODS_OPTION_03."<br />";
									}

									if ($GOODS_OPTION_NM_04 <> "") {
										$option_str .= $GOODS_OPTION_NM_04." : ".$GOODS_OPTION_04."<br />";
									}
									//if ($ORDER_STATE == "2") {
						?>
	<tr>
		<td><?=$ORDER_GOODS_NO?></td>
		<td><?=$RESERVE_NO?></td>
		<td><?=$GOODS_NAME?></td>
		<td><?=$QTY?></td>
		<td><?=$option_str?></td>
		<td><?=$R_MEM_NM?></td>
		<td><?=$R_HPHONE?></td>
		<td><?=$R_ZIPCODE?></td>
		<td><?=$R_ADDR1?></td>
		<td><?=$DELIVERY_CP?></td>
		<td><?=$DELIVERY_NO?></td>
		<? if ($s_adm_cp_type == "운영") { ?>
		<td><?=$CP_ORDER_NO?></td>
		<td><?=getCompanyName($conn, $CP_NO)?></td>
		<td><?=getCompanyName($conn, $BUY_CP_NO)?></td>
		<? } else { ?>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<? } ?>
	</tr>

						<?
									//}
								}
							}
						?>


						<?
						}
					}else{
						?>
						<tr class="order">
							<td height="50" align="center" colspan="14">데이터가 없습니다. </td>
						</tr>
					<?
						}
					?>
</table>

</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>