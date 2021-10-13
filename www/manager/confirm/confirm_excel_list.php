<?session_start();?>
<?
# =============================================================================
# File Name    : order_list.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @C&C Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "CF002"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

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


#====================================================================
# Request Parameter
#====================================================================

	$file_name="공급 업체 정산 상세 리스트-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
	  header( "Content-Disposition: attachment; filename=$file_name" );
	  header( "Content-Description: orion70kr@gmail.com" );
	
	$con_use_tf		= "Y";
	$del_tf				= "N";
	$nPage				= "1";
	$nPageSize		= "100000";


	$arr_rs = listConfirmOrderGoods($conn, $start_date, $end_date, $cp_type, $cp_type2, $con_confirm_tf, $con_tax_tf, $etc_condition, $con_use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
</head>

<body>

<font size=3><b><?=$Admin_shop_name?> 공급 업체 정산 상세 리스트 </b></font> <br>
<br>
출력 일자 : [<?=date("Y년 m월 d일")?> ]
<br>
<br>
<TABLE border=1>

	<? if ($s_adm_cp_type == "운영") { ?>
	<tr>
		<td align='center' bgcolor='#F4F1EF'>주문번호</td>
		<td align='center' bgcolor='#F4F1EF'>판매업체</td>
		<td align='center' bgcolor='#F4F1EF'>공급업체</td>
		<td align='center' bgcolor='#F4F1EF'>주문자명</td>
		<td align='center' bgcolor='#F4F1EF'>수령자명</td>
		<td align='center' bgcolor='#F4F1EF'>수령자주소</td>
		<td align='center' bgcolor='#F4F1EF'>수령자연락처</td>
		<td align='center' bgcolor='#F4F1EF'>상품명</td>
		<td align='center' bgcolor='#F4F1EF'>공급가</td>
		<td align='center' bgcolor='#F4F1EF'>판매가</td>
		<td align='center' bgcolor='#F4F1EF'>수량</td>
		<td align='center' bgcolor='#F4F1EF'>배송비</td>
		<td align='center' bgcolor='#F4F1EF'>추가배송비</td>
		<td align='center' bgcolor='#F4F1EF'>공급가합계</td>
		<td align='center' bgcolor='#F4F1EF'>3자물류</td>
		<td align='center' bgcolor='#F4F1EF'>주문상태</td>
		<td align='center' bgcolor='#F4F1EF'>완료일시</td>
		<td align='center' bgcolor='#F4F1EF'>정산구분</td>
		<td align='center' bgcolor='#F4F1EF'>정산일시</td>
	</tr>
	<? } ?>

	<? if ($s_adm_cp_type == "구매" || $s_adm_cp_type == "판매공급") {  ?>
	<tr>
		<td align='center' bgcolor='#F4F1EF'>주문번호</td>
		<td align='center' bgcolor='#F4F1EF'>주문자명</td>
		<td align='center' bgcolor='#F4F1EF'>수령자명</td>
		<td align='center' bgcolor='#F4F1EF'>수령자주소</td>
		<td align='center' bgcolor='#F4F1EF'>수령자연락처</td>
		<td align='center' bgcolor='#F4F1EF'>상품명</td>
		<td align='center' bgcolor='#F4F1EF'>공급가</td>
		<td align='center' bgcolor='#F4F1EF'>수량</td>
		<td align='center' bgcolor='#F4F1EF'>배송비</td>
		<td align='center' bgcolor='#F4F1EF'>추가배송비</td>
		<td align='center' bgcolor='#F4F1EF'>공급가합계</td>
		<td align='center' bgcolor='#F4F1EF'>3자물류</td>
		<td align='center' bgcolor='#F4F1EF'>주문상태</td>
		<td align='center' bgcolor='#F4F1EF'>완료일시</td>
		<td align='center' bgcolor='#F4F1EF'>정산구분</td>
		<td align='center' bgcolor='#F4F1EF'>정산일시</td>
	</tr>
	<? } ?>

				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							/*
							as rn, C.ORDER_GOODS_NO, C.RESERVE_NO, C.MEM_NO, C.ORDER_SEQ, C.GOODS_NO, C.GOODS_CODE, 
							C.GOODS_NAME, C.GOODS_SUB_NAME, 
							C.QTY, C.GOODS_OPTION_01, C.GOODS_OPTION_02, C.GOODS_OPTION_03,
							C.GOODS_OPTION_04, C.GOODS_OPTION_NM_01, C.GOODS_OPTION_NM_02,
							C.GOODS_OPTION_NM_03, C.GOODS_OPTION_NM_04, C.CATE_01, C.CATE_02,
							C.CATE_03, C.CATE_04, C.BUY_PRICE, C.SALE_PRICE, C.EXTRA_PRICE, C.SA_DELIVERY_PRICE, 
							C.USE_TF, C.DEL_TF, C.REG_ADM, C.REG_DATE, C.DEL_ADM, C.DEL_DATE,
							C.ORDER_STATE, C.BUY_CP_NO, C.FINISH_DATE, O.O_MEM_NM, O.R_MEM_NM, C.CONFIRM_TF, C.CONFIRM_DATE, O.CP_NO,
							((C.SALE_PRICE * C.QTY) + (C.EXTRA_PRICE * C.QTY)) AS SUM_PRICE, 
							((C.SALE_PRICE * C.QTY) - (C.BUY_PRICE * C.QTY)) AS PLUS_PRICE, 
							ROUND((((C.SALE_PRICE * C.QTY) - (C.BUY_PRICE * C.QTY)) / (C.SALE_PRICE * C.QTY) * 100),2) AS LEE,
							O.R_ZIPCODE, O.R_ADDR1, O.R_ADDR2, O.R_PHONE, O.R_HPHONE
							*/

							$rn										= trim($arr_rs[$j]["rn"]);
							$ORDER_GOODS_NO				= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
							$RESERVE_NO						= trim($arr_rs[$j]["RESERVE_NO"]);
							$MEM_NO								= trim($arr_rs[$j]["MEM_NO"]);
							$ORDER_SEQ						= trim($arr_rs[$j]["ORDER_SEQ"]);
							$GOODS_NO							= trim($arr_rs[$j]["GOODS_NO"]);
							$GOODS_CODE						= trim($arr_rs[$j]["GOODS_CODE"]);
							$GOODS_NAME						= trim($arr_rs[$j]["GOODS_NAME"]);
							$GOODS_SUB_NAME				= trim($arr_rs[$j]["GOODS_SUB_NAME"]);
							
							$QTY									= trim($arr_rs[$j]["QTY"]);
							$GOODS_OPTION_01			= trim($arr_rs[$j]["GOODS_OPTION_01"]);
							$GOODS_OPTION_02			= trim($arr_rs[$j]["GOODS_OPTION_02"]);
							$GOODS_OPTION_03			= trim($arr_rs[$j]["GOODS_OPTION_03"]);
							$GOODS_OPTION_04			= trim($arr_rs[$j]["GOODS_OPTION_04"]);

							$GOODS_OPTION_NM_01		= trim($arr_rs[$j]["GOODS_OPTION_NM_01"]);
							$GOODS_OPTION_NM_02		= trim($arr_rs[$j]["GOODS_OPTION_NM_02"]);
							$GOODS_OPTION_NM_03		= trim($arr_rs[$j]["GOODS_OPTION_NM_03"]);
							$GOODS_OPTION_NM_04		= trim($arr_rs[$j]["GOODS_OPTION_NM_04"]);

							$CATE_01							= trim($arr_rs[$j]["CATE_01"]);
							$CATE_02							= trim($arr_rs[$j]["CATE_02"]);
							$CATE_03							= trim($arr_rs[$j]["CATE_03"]);
							$CATE_04							= trim($arr_rs[$j]["CATE_04"]);
							$BUY_PRICE						= trim($arr_rs[$j]["BUY_PRICE"]);
							$SALE_PRICE						= trim($arr_rs[$j]["SALE_PRICE"]);
							$EXTRA_PRICE					= trim($arr_rs[$j]["EXTRA_PRICE"]);
							$DELIVERY_PRICE				= trim($arr_rs[$j]["DELIVERY_PRICE"]);
							$SA_DELIVERY_PRICE		= trim($arr_rs[$j]["SA_DELIVERY_PRICE"]);
							$ORDER_STATE					= trim($arr_rs[$j]["ORDER_STATE"]);
							
							$BUY_CP_NO						= trim($arr_rs[$j]["BUY_CP_NO"]);
							$FINISH_DATE					= trim($arr_rs[$j]["FINISH_DATE"]);
							$O_MEM_NM							= trim($arr_rs[$j]["O_MEM_NM"]);
							$R_MEM_NM							= trim($arr_rs[$j]["R_MEM_NM"]);
							$CONFIRM_TF						= trim($arr_rs[$j]["CONFIRM_TF"]);
							$CONFIRM_DATE					= trim($arr_rs[$j]["CONFIRM_DATE"]);
							$CP_NO								= trim($arr_rs[$j]["CP_NO"]);
							$SUM_PRICE						= trim($arr_rs[$j]["SUM_PRICE"]);
							$PLUS_PRICE						= trim($arr_rs[$j]["PLUS_PRICE"]);
							$LEE									= trim($arr_rs[$j]["LEE"]);

							$R_ZIPCODE						= trim($arr_rs[$j]["R_ZIPCODE"]);
							$R_ADDR1							= trim($arr_rs[$j]["R_ADDR1"]);
							$R_ADDR2							= trim($arr_rs[$j]["R_ADDR2"]);
							$R_PHONE							= trim($arr_rs[$j]["R_PHONE"]);
							$R_HPHONE							= trim($arr_rs[$j]["R_HPHONE"]);

							if ($R_HPHONE == "") $R_HPHONE = $R_PHONE;

							if (($CONFIRM_TF == "N") || ($CONFIRM_TF == "") ) {
								$CONFIRM_DATE		= "";
								$str_confirm = "<font color = 'gray'>미정산</font>";
							} else {
								$CONFIRM_DATE		= date("Y-m-d H:i",strtotime($CONFIRM_DATE));
								$str_confirm = "<font color = 'navy'>정산</font>";
							}

							$FINISH_DATE		= date("Y-m-d H:i",strtotime($FINISH_DATE));

							$str_price_class = "price";
							$str_state_class = "state";

							if (($ORDER_STATE == "1") || ($ORDER_STATE == "2")) {
								$refund_able_qty = getRefundAbleQty($conn, $RESERVE_NO, $GOODS_NO, $GOODS_OPTION_01, $GOODS_OPTION_02, $GOODS_OPTION_03, $GOODS_OPTION_04, $GOODS_OPTION_NM_01, $GOODS_OPTION_NM_02, $GOODS_OPTION_NM_03, $GOODS_OPTION_NM_04);
							
							
							//} else if (($ORDER_STATE == "2")) {
							//	$refund_able_qty = getRealDeliveryQty($conn, $RESERVE_NO, $GOODS_NO, $GOODS_OPTION_01, $GOODS_OPTION_02, $GOODS_OPTION_03, $GOODS_OPTION_04, $GOODS_OPTION_NM_01, $GOODS_OPTION_NM_02, $GOODS_OPTION_NM_03, $GOODS_OPTION_NM_04);
							
							
							} else if ($ORDER_STATE == "7") {
								$refund_able_qty = -$QTY;

								$str_price_class = "price_refund";
								$str_state_class = "state_refund";
								$SUM_PRICE = -$SUM_PRICE;
								$EXTRA_PRICE	=  -$EXTRA_PRICE;

							} else {
								$refund_able_qty = $QTY;
							}

							if ($s_adm_cp_type == "운영") {
						?>
	<tr>
		<td bgColor='#FFFFFF' align='center'><?=$RESERVE_NO?></td>
		<td bgColor='#FFFFFF' align='left'><?= getCompanyName($conn, $CP_NO);?></td>
		<td bgColor='#FFFFFF' align='left'><?= getCompanyName($conn, $BUY_CP_NO);?></td>
		<td bgColor='#FFFFFF' align='center'><?=$O_MEM_NM?></td>
		<td bgColor='#FFFFFF' align='center'><?=$R_MEM_NM?></td>
		<td bgColor='#FFFFFF' align='left'>[<?=$R_ZIPCODE?>] <?=$R_ADDR1?> <?=$R_ADDR2?></td>
		<td bgColor='#FFFFFF' align='left'><?=$R_HPHONE?></td>
		<td bgColor='#FFFFFF' align='left'><?=$GOODS_NAME?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($BUY_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($SALE_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($refund_able_qty)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($EXTRA_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($DELIVERY_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($SUM_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($SA_DELIVERY_PRICE)?></td>
		<td bgColor='#FFFFFF' align='center'><?=getDcodeName($conn, "ORDER_STATE", $ORDER_STATE);?></td>
		<td bgColor='#FFFFFF' align='center'><?=$FINISH_DATE?></td>
		<td bgColor='#FFFFFF' align='center'><?=$str_confirm?></td>
		<td bgColor='#FFFFFF' align='center'><?=$CONFIRM_DATE?></td>
	</tr>

						<?
							}

							if ($s_adm_cp_type == "구매" || $s_adm_cp_type == "판매공급") { 
						?>
	<tr>
		<td bgColor='#FFFFFF' align='center'><?=$RESERVE_NO?></td>
		<td bgColor='#FFFFFF' align='center'><?=$O_MEM_NM?></td>
		<td bgColor='#FFFFFF' align='center'><?=$R_MEM_NM?></td>
		<td bgColor='#FFFFFF' align='left'>[<?=$R_ZIPCODE?>] <?=$R_ADDR1?> <?=$R_ADDR2?></td>
		<td bgColor='#FFFFFF' align='left'><?=$R_HPHONE?></td>
		<td bgColor='#FFFFFF' align='left'><?=$GOODS_NAME?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($BUY_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($refund_able_qty)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($EXTRA_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($DELIVERY_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format(($BUY_PRICE * $QTY) + ($EXTRA_PRICE * $QTY) + $DELIVERY_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($SA_DELIVERY_PRICE)?></td>
		<td bgColor='#FFFFFF' align='center'><?=getDcodeName($conn, "ORDER_STATE", $ORDER_STATE);?></td>
		<td bgColor='#FFFFFF' align='center'><?=$FINISH_DATE?></td>
		<td bgColor='#FFFFFF' align='center'><?=$str_confirm?></td>
		<td bgColor='#FFFFFF' align='center'><?=$CONFIRM_DATE?></td>
	</tr>

						<?
							}

						}
					}else{
						?>
						<tr>
							<td height="50" align="center" colspan="19">데이터가 없습니다. </td>
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