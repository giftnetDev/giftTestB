<?session_start();?>
<?
# =============================================================================
# File Name    : delivery_excel_list.php
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
	$menu_right = "OD015"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/payment/payment.php";

//	$file_name="배송리스트-".$specific_date."-".getDcodeName($conn, "DELIVERY_PROFIT", $delivery_profit).".xls";
//	  header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
//	  header( "Content-Disposition: attachment; filename=$file_name" );
//	  header( "Content-Description: orion70kr@gmail.com" );
	
	if($delivery_profit <> "") {
		$args_order_state = "2"; //주문확인
		$arr_rs = listOrderDeliveryExcelForMart($conn, $specific_date, $delivery_profit, $args_order_state);
	}
	else
		exit;

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<style>td { mso-number-format:\@; } </style> 
<title><?=$g_title?></title>
</head>

<body>

<TABLE border=1>
				<?
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							$ORDER_STATE		    = trim($arr_rs[$j]["ORDER_STATE"]);
							$ORDER_GOODS_DELIVERY_NO= trim($arr_rs[$j]["ORDER_GOODS_DELIVERY_NO"]);
							$RESERVE_NO			    = trim($arr_rs[$j]["RESERVE_NO"]);
							$CP_ORDER_NO			= trim($arr_rs[$j]["CP_ORDER_NO"]);
							$DELIVERY_NO			= trim($arr_rs[$j]["DELIVERY_NO"]);
							$DELIVERY_SEQ			= trim($arr_rs[$j]["DELIVERY_SEQ"]);
							$RECEIVER_NM			= trim($arr_rs[$j]["RECEIVER_NM"]);
							$RECEIVER_PHONE			= trim($arr_rs[$j]["RECEIVER_PHONE"]);
							$RECEIVER_HPHONE		= trim($arr_rs[$j]["RECEIVER_HPHONE"]);
							$RECEIVER_ADDR			= trim($arr_rs[$j]["RECEIVER_ADDR"]);
							$GOODS_DELIVERY_NAME	= trim($arr_rs[$j]["GOODS_DELIVERY_NAME"]);								
							$ORDER_QTY		        = trim($arr_rs[$j]["ORDER_QTY"]);
							$MEMO			        = trim($arr_rs[$j]["MEMO"]);
							$ORDER_MANAGER_NM	    = trim($arr_rs[$j]["ORDER_MANAGER_NM"]);								
							$ORDER_MANAGER_PHONE    = trim($arr_rs[$j]["ORDER_MANAGER_PHONE"]);								
							$ORDER_NM			    = trim($arr_rs[$j]["ORDER_NM"]);
							$ORDER_PHONE		    = trim($arr_rs[$j]["ORDER_PHONE"]);
							$DELIVERY_FEE		    = trim($arr_rs[$j]["DELIVERY_FEE"]);
							$DELIVERY_PROFIT		= trim($arr_rs[$j]["DELIVERY_PROFIT"]);
							$PAYMENT_TYPE   		= trim($arr_rs[$j]["PAYMENT_TYPE"]);
							$SEND_CP_ADDR      	    = trim($arr_rs[$j]["SEND_CP_ADDR"]);

						?>
						<tr>
							<td bgColor='#FFFFFF' align='left'><?=$DELIVERY_SEQ?></td>
							<td bgColor='#FFFFFF' align='left'><?=$RECEIVER_NM?></td>
							<td bgColor='#FFFFFF' align='left'><?=$RECEIVER_PHONE?></td>
							<td bgColor='#FFFFFF' align='left'><?=$RECEIVER_HPHONE?></td>
							<td bgColor='#FFFFFF' align='left'><?=$RECEIVER_ADDR?></td>
							<td bgColor='#FFFFFF' align='left'><?=$GOODS_DELIVERY_NAME?></td>
							<td bgColor='#FFFFFF' align='left'><?=$ORDER_QTY?></td>
							<td bgColor='#FFFFFF' align='left'><?=$MEMO?></td>
							<td bgColor='#FFFFFF' align='left'><?=$ORDER_MANAGER_NM?></td>
							<td bgColor='#FFFFFF' align='left'><?=$ORDER_MANAGER_PHONE?></td>
							<td bgColor='#FFFFFF' align='left'><?=$ORDER_NM?></td>
							<td bgColor='#FFFFFF' align='left'><?=$ORDER_PHONE?></td>
							<td bgColor='#FFFFFF' align='left'><?=$DELIVERY_FEE?></td>
							<td bgColor='#FFFFFF' align='left'><?=$PAYMENT_TYPE?></td>
							<td bgColor='#FFFFFF' align='left'><?=$SEND_CP_ADDR?></td>
						</tr>
						
					<?
						}
					 }else {
					?>
						<tr class="order">
							<td height="50" align="center" colspan="15">데이터가 없습니다. </td>
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