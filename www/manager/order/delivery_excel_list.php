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
	$menu_right = "OD005"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

	if ($s_adm_cp_type == "구매" || $s_adm_cp_type == "판매공급") { 
		$cp_type = $s_adm_com_code;
	}

	if ($s_adm_cp_type == "판매") { 
		$cp_type2 = $s_adm_com_code;
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

	$file_name="배송리스트-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
	  header( "Content-Disposition: attachment; filename=$file_name" );
	
	$con_use_tf		= "Y";
	$del_tf				= "N";
	
	$arr_rs = listManagerDeliverySelected($conn, $search_date_type, $start_date, $end_date, $bulk_tf, $sel_order_state, $cp_type, $cp_type2, $sel_cate_01, $con_work_flag, $sel_opt_manager_no, $sel_delivery_type, $sel_delivery_cp, $con_use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<style>td { mso-number-format:\@; } </style> 
<title><?=$g_title?></title>
</head>

<body>

<font size=3><b><?=$Admin_shop_name?> 배송 리스트 </b></font> <br>
<br>
출력 일자 : [<?=date("Y년 m월 d일")?> ]
<br>
<br>

<TABLE border=1>
	<tr>
		<td align='center' bgcolor='#F4F1EF'>주문번호</td>
		<td align='center' bgcolor='#F4F1EF'>판매업체명</td>
		<td align='center' bgcolor='#F4F1EF'>주문자명</td>
		<td align='center' bgcolor='#F4F1EF'>주문자연락처</td>
		<td align='center' bgcolor='#F4F1EF'>수령자명</td>
		<td align='center' bgcolor='#F4F1EF'>우편번호</td>
		<td align='center' bgcolor='#F4F1EF'>주소</td>
		<td align='center' bgcolor='#F4F1EF'>담당</td>
		<td align='center' bgcolor='#F4F1EF'>수령자연락처</td>
		<td align='center' bgcolor='#F4F1EF'>주문일시</td>
		<td align='center' bgcolor='#F4F1EF'>업체주문번호</td>
		<td align='center' bgcolor='#F4F1EF'>주문확인일시</td>
		<td align='center' bgcolor='#F4F1EF'>상품구분</td>
		<td align='center' bgcolor='#F4F1EF'>상품코드</td>
		<td align='center' bgcolor='#F4F1EF'>상품명</td>
		<td align='center' bgcolor='#F4F1EF'>출고예정일</td>
		<td align='center' bgcolor='#F4F1EF'>옵션-스티커종류</td>
		<td align='center' bgcolor='#F4F1EF'>옵션-스티커메세지</td>
		<td align='center' bgcolor='#F4F1EF'>옵션-포장지</td>
		<td align='center' bgcolor='#F4F1EF'>옵션-인쇄메세지</td>
		<td align='center' bgcolor='#F4F1EF'>옵션-아웃박스스티커여부</td>
		<td align='center' bgcolor='#F4F1EF'>옵션-작업메모</td>
		
		<td align='center' bgcolor='#F4F1EF'>수량</td>
		
		<td align='center' bgcolor='#F4F1EF'>배송종류</td>
		<td align='center' bgcolor='#F4F1EF'>개별배송지 수</td>
		<td align='center' bgcolor='#F4F1EF'>배송회사</td>
		<td align='center' bgcolor='#F4F1EF'>대표송장번호</td>

		<td align='center' bgcolor='#F4F1EF'>주문상태</td>
		<td align='center' bgcolor='#F4F1EF'>포장상태</td>
		<td align='center' bgcolor='#F4F1EF'>작업시작일</td>
		<td align='center' bgcolor='#F4F1EF'>작업완료일</td>
		<td align='center' bgcolor='#F4F1EF'>배송완료일</td>
	</tr>
				
<?
	$nCnt = 0;

	if (sizeof($arr_rs) > 0) {
		for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

/*
			O.RESERVE_NO, O.CP_NO, O.O_MEM_NM, O.O_PHONE, O.O_HPHONE, O.R_MEM_NM, O.R_ZIPCODE, O.R_ADDR1, O.OPT_MANAGER_NO, O.R_PHONE, O.ORDER_DATE,
				G.CP_ORDER_NO, G.ORDER_CONFIRM_DATE, G.GOODS_NO, G.CATE_01, G.GOODS_CODE, G.GOODS_NAME, G.OPT_OUTSTOCK_DATE, G.OPT_STICKER_NO, G.OPT_STICKER_MSG, G.OPT_OUTBOX_TF, G.OPT_WRAP_NO, G.OPT_PRINT_MSG, G.OPT_MEMO, G.QTY, G.DELIVERY_TYPE, G.DELIVERY_CP, G.DELIVERY_NO, G.ORDER_STATE, G.WORK_FLAG, G.WORK_START_DATE, G.WORK_END_DATE
*/

		$RESERVE_NO					= trim($arr_rs[$j]["RESERVE_NO"]);
		$CP_NO						= trim($arr_rs[$j]["CP_NO"]);
		$O_MEM_NM					= trim($arr_rs[$j]["O_MEM_NM"]);
		$O_PHONE					= trim($arr_rs[$j]["O_PHONE"]);
		$O_HPHONE					= trim($arr_rs[$j]["O_HPHONE"]);
		$R_MEM_NM					= trim($arr_rs[$j]["R_MEM_NM"]);
		$R_ZIPCODE					= trim($arr_rs[$j]["R_ZIPCODE"]);
		$R_ADDR1					= trim($arr_rs[$j]["R_ADDR1"]);
		$OPT_MANAGER_NO				= trim($arr_rs[$j]["OPT_MANAGER_NO"]);
		$R_PHONE					= trim($arr_rs[$j]["R_PHONE"]);
		$ORDER_DATE					= trim($arr_rs[$j]["ORDER_DATE"]);
		$ORDER_DATE					= date("Y-m-d H:i:s",strtotime($ORDER_DATE));
	
		///////////////////////////////////////////////////////////////////////////////////

		$ORDER_GOODS_NO				= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
		$ORDER_STATE				= trim($arr_rs[$j]["ORDER_STATE"]);

		$CP_ORDER_NO				= trim($arr_rs[$j]["CP_ORDER_NO"]);
		$ORDER_CONFIRM_DATE			= trim($arr_rs[$j]["ORDER_CONFIRM_DATE"]);
		$GOODS_NO					= trim($arr_rs[$j]["GOODS_NO"]);
		$CATE_01					= trim($arr_rs[$j]["CATE_01"]);
		$CATE_04					= trim($arr_rs[$j]["CATE_04"]);
		$GOODS_CODE					= SetStringFromDB($arr_rs[$j]["GOODS_CODE"]);
		$GOODS_NAME					= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
		
		$OPT_OUTSTOCK_DATE			= trim($arr_rs[$j]["OPT_OUTSTOCK_DATE"]);
		$OPT_STICKER_NO				= trim($arr_rs[$j]["OPT_STICKER_NO"]);
		$OPT_STICKER_MSG			= trim($arr_rs[$j]["OPT_STICKER_MSG"]);
		$OPT_OUTBOX_TF				= trim($arr_rs[$j]["OPT_OUTBOX_TF"]);
		$OPT_WRAP_NO				= trim($arr_rs[$j]["OPT_WRAP_NO"]);
		$OPT_PRINT_MSG				= trim($arr_rs[$j]["OPT_PRINT_MSG"]);
		$OPT_MEMO					= trim($arr_rs[$j]["OPT_MEMO"]);

		$DELIVERY_TYPE				= trim($arr_rs[$j]["DELIVERY_TYPE"]);
		$DELIVERY_CP				= trim($arr_rs[$j]["DELIVERY_CP"]);
		$DELIVERY_NO				= trim($arr_rs[$j]["DELIVERY_NO"]);

		$WORK_FLAG					= trim($arr_rs[$j]["WORK_FLAG"]);
		$WORK_START_DATE			= trim($arr_rs[$j]["WORK_START_DATE"]);
		$WORK_END_DATE				= trim($arr_rs[$j]["WORK_END_DATE"]);

		$FINISH_DATE				= trim($arr_rs[$j]["FINISH_DATE"]);

		$QTY						= trim($arr_rs[$j]["QTY"]);

		if ($DELIVERY_CP <> "") {
			if ($FINISH_DATE <> "")  {
				$FINISH_DATE = date("Y-m-d H:i",strtotime($FINISH_DATE));
			}
		} else {
			$FINISH_DATE = "";
		}
		
		if (($ORDER_STATE == "1") || ($ORDER_STATE == "2")) {
			$refund_able_qty = getRefundAbleQty($conn, $RESERVE_NO, $ORDER_GOODS_NO);
		
		
		} else if (($ORDER_STATE == "3")) {
			$refund_able_qty = getRealDeliveryQty($conn, $RESERVE_NO, $ORDER_GOODS_NO);
		
		
		} else if ($ORDER_STATE == "7") {
			$refund_able_qty = -$QTY;

		} else {
			$refund_able_qty = $QTY;
		}

		$cnt_delivery_place = 0;
		$total_sub_qty = 0;
		$total_delivered_qty = 0;

		$arr_rs_individual = cntDeliveryIndividual($conn, $ORDER_GOODS_NO);
		if(sizeof($arr_rs_individual) > 0) { 
			$cnt_delivery_place = $arr_rs_individual[0]["CNT_DELIVERY_PLACE"];
			$total_sub_qty = $arr_rs_individual[0]["TOTAL_GOODS_DELIVERY_QTY"];
			$total_delivered_qty= $arr_rs_individual[0]["TOTAL_DELIVERED_QTY"];
		}
?>
	<tr>
		<td bgColor='#FFFFFF' align='left'><?=$RESERVE_NO?></td>
		<td bgColor='#FFFFFF' align='left'><?=getCompanyName($conn, $CP_NO);?></td>
		<td bgColor='#FFFFFF' align='left'><?=$O_MEM_NM?></td>
		<td bgColor='#FFFFFF' align='left'><?=$O_PHONE?></td>
		<td bgColor='#FFFFFF' align='left'><?=$R_MEM_NM?></td>
		<td bgColor='#FFFFFF' align='left'><?=$R_ZIPCODE?></td>
		<td bgColor='#FFFFFF' align='left'><?=$R_ADDR1?></td>
		<td bgColor='#FFFFFF' align='left'><?=getAdminName($conn, $OPT_MANAGER_NO)?></td>
		<td bgColor='#FFFFFF' align='left'><?=$R_PHONE?></td>
		<td bgColor='#FFFFFF' align='left'><?=$ORDER_DATE?></td>
		<td bgColor='#FFFFFF' align='left'><?=$CP_ORDER_NO?></td>
		<td bgColor='#FFFFFF' align='left'><?=$ORDER_CONFIRM_DATE?></td>
		<td bgColor='#FFFFFF' align='left'><?=$CATE_01?></td>
		<td bgColor='#FFFFFF' align='left'><?=$GOODS_CODE?></td>
		<td bgColor='#FFFFFF' align='left'><?=$GOODS_NAME?></td>
		<td bgColor='#FFFFFF' align='left'><?=($OPT_OUTSTOCK_DATE != "" ? $OPT_OUTSTOCK_DATE : "출고미정")?></td>
		<td bgColor='#FFFFFF' align='left'><?=getGoodsName($conn, $OPT_STICKER_NO)?></td>
		<td bgColor='#FFFFFF' align='left'><?=$OPT_STICKER_MSG?></td>
		<td bgColor='#FFFFFF' align='left'><?=getGoodsName($conn, $OPT_WRAP_NO)?></td>
		<td bgColor='#FFFFFF' align='left'><?=$OPT_PRINT_MSG?></td>
		<td bgColor='#FFFFFF' align='left'><?=($OPT_OUTBOX_TF == "Y" ? "있음" : "없음" )?></td>
		<td bgColor='#FFFFFF' align='left'><?=$OPT_MEMO?></td>
		<? if($DELIVERY_TYPE == "3") { ?>
		<td bgColor='#FFFFFF' align='left'><?=$refund_able_qty."/".($refund_able_qty - $total_delivered_qty)?></td>
		<? } else { ?>
		<td bgColor='#FFFFFF' align='left'><?=$refund_able_qty?></td>
		<? } ?>
		<td bgColor='#FFFFFF' align='left'><?=getDcodeName($conn,"DELIVERY_TYPE",$DELIVERY_TYPE)?></td>
		<td bgColor='#FFFFFF' align='left'><?=($DELIVERY_TYPE == "3" || $DELIVERY_TYPE == "98" ? $cnt_delivery_place."곳" : "") ?></td>
		<td bgColor='#FFFFFF' align='left'><?=getDcodeName($conn,"DELIVERY_CP",$DELIVERY_CP)?></td>
		<td bgColor='#FFFFFF' align='left'><?=$DELIVERY_NO?></td>
		<td bgColor='#FFFFFF' align='left'><?=getDcodeName($conn,"ORDER_STATE",$ORDER_STATE)?></td>
		<td bgColor='#FFFFFF' align='left'><?=($WORK_FLAG == "Y" ? "포장완료" : "포장중")?></td>
		<td bgColor='#FFFFFF' align='left'><?=$WORK_START_DATE?></td>
		<td bgColor='#FFFFFF' align='left'><?=($WORK_FLAG == "Y" ? $WORK_END_DATE : "")?></td>
		<td bgColor='#FFFFFF' align='left'><?=$FINISH_DATE?></td>

	</tr>

<?
	}
} else {
?>
	<tr class="order">
		<td height="50" align="center" colspan="32">데이터가 없습니다. </td>
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