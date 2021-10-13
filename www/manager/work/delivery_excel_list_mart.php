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
	$menu_right = "WO010"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/work/work.php";

	if($mode == "excel") {
	  $file_name="배송리스트-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
	  header( "Content-Disposition: attachment; filename=$file_name" );
	  header( "Content-Description: orion70kr@gmail.com" );
	}
	
	$arr_rs = listOrderDeliveryExcelForMart($conn, $specific_date, $cp_no);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<style>td { mso-number-format:\@; } </style> 
<title><?=$g_title?></title>
</head>

<body>

<?
	if($mode == "pop") {
?>
			<link rel="stylesheet" href="../css/admin.css" type="text/css" />
			<table cellpadding="0" cellspacing="0" class="rowstable03" border="0" style="width:100%">
				<colgroup>
					<col width="5%">
					<col width="5%">
					<col width="7%">
					<col width="7%">
					<col width="11%">
					<col width="10%">
					<col width="5%">
					<col width="10%">
					<col width="5%">
					<col width="5%">
					<col width="5%">
					<col width="5%">
					<col width="5%">
					<col width="5%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th>출고번호</th>
						<th>수령인</th>
						<th>수령인전화번호</th>
						<th>수령인핸드폰</th>
						<th>수령인주소</th>
						<th>송장내용</th>
						<th>주문수</th>
						<th>메모</th>
						<th>주문관리자</th>
						<th>주문관리자번호</th>
						<th>주문자</th>
						<th>주문자번호</th>
						<th>배송운임</th>
						<th>결제조건</th>
						<th class="end">발송지주소</th>
					</tr>
				</thead>

			
<?
}else{
?>
	<TABLE border=1>
<?
} 
?>
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
							$DELIVERY_FEE_CODE	    = trim($arr_rs[$j]["DELIVERY_FEE_CODE"]);
							$DELIVERY_PROFIT		= trim($arr_rs[$j]["DELIVERY_PROFIT"]);
							$PAYMENT_TYPE   		= trim($arr_rs[$j]["PAYMENT_TYPE"]);
							$SEND_CP_ADDR      	    = trim($arr_rs[$j]["SEND_CP_ADDR"]);

							$ORDER_MANAGER_PHONE = "02-".$ORDER_MANAGER_PHONE;
							$ORDER_PHONE = "02-".$ORDER_PHONE;

							$DELIVERY_FEE = getDcodeExtByCode($conn, 'DELIVERY_FEE', $DELIVERY_FEE_CODE);

							$RECEIVER_ADDR = str_replace(array("\r\n", "\n", "\r", "<br>", "<br/>", "<BR/>", "<BR>"), '', $RECEIVER_ADDR);

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