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
	require "../../_classes/biz/payment/payment.php";

	if($combined_type == "0")
		$type_name = "박스단위";
	else if($combined_type == "1")
		$type_name = "낱개";
	else if($combined_type == "2")
		$type_name = "합포장";
	else if($combined_type == "3")
		$type_name = "낱개+합포장";
	else 
		$type_name = "전체";

	$cp_nm = getCompanyNameWithNoCode($conn, $cp_no);
	$file_name="창고 작업 준비 품목별 리스트 - ".$cp_nm." - ".$type_name." - ".$specific_date.".xls";
	  
	header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
	header( "Content-Disposition: attachment; filename=$file_name" );

	
	$arr_rs = listOrderDeliveryWarehouseForMart($conn, $start_date, $end_date, $from_seq_of_day, $to_seq_of_day, $cp_nm, $combined_type, $has_island);
	$arr_rs_sticker = listOrderDeliveryWarehouseForMart_Sticker($conn, $start_date, $end_date, $from_seq_of_day, $to_seq_of_day, $cp_nm, $combined_type, $has_island);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<style>td { mso-number-format:\@; font-size: 16px; line-height: 24px; } </style> 
<title><?=$g_title?></title>
</head>

<body>
<font size=3><b><?=$file_name?></b></font> 
<br>
<br>
출력 일자 : [<?=date("Y년 m월 d일")?> ]
<br>
<br>
<font size=3><b>상품 준비 수량</b></font> 
<br>
<TABLE border=1>
	<? if($combined_type == "0") { ?>
	<thead>
		<tr>
			<td><font size=2><b>상품코드</b></td>
			<td><font size=2><b>바코드</b></td>
			<td><font size=2><b>상품명</b></td>
			<td><font size=2><b>박스입수</b></td>
			<td><font size=2><b>재고</b></td>
			<td bgcolor='#F4F1EF'><font size=2><b>준비할 박스수량</b></td>
		</tr>
	</thead>
	<tbody>

	<?
		if (sizeof($arr_rs) > 0) {
			for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
				
				$GOODS_CODE		     = trim($arr_rs[$j]["GOODS_CODE"]);
				$KANCODE		     = trim($arr_rs[$j]["KANCODE"]);
				$GOODS_NAME		     = trim($arr_rs[$j]["GOODS_NAME"]);
				$DELIVERY_CNT_IN_BOX = trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);
				$STOCK_CNT		     = trim($arr_rs[$j]["STOCK_CNT"]);
				$GOODS_CNT_SUM       = trim($arr_rs[$j]["GOODS_CNT_SUM"]);
				$per_box = floor($GOODS_CNT_SUM / $DELIVERY_CNT_IN_BOX);

			?>
			<tr>
				<td height="24" bgColor='#FFFFFF' align='left'><?=$GOODS_CODE?></td>
				<td bgColor='#FFFFFF' align='left'><?=$KANCODE?></td>
				<td bgColor='#FFFFFF' align='left'><?=$GOODS_NAME?></td>
				<td bgColor='#FFFFFF' align='right'><?=$DELIVERY_CNT_IN_BOX?></td>
				<td bgColor='#FFFFFF' align='right'><?=$STOCK_CNT?></td>
				<td bgColor='#FFFFFF' align='right'><b><?=$per_box?></b></td>
			</tr>
			
		<?
			}
		 }else {
		?>
			<tr class="order">
				<td height="50" align="center" colspan="4">데이터가 없습니다. </td>
			</tr>
		<?
		 }
		?>
	</tbody>
	<? } else { ?>

	<thead>
		<tr>
			<td><font size=2><b>상품코드</b></td>
			<td><font size=2><b>바코드</b></td>
			<td><font size=2><b>상품명</b></td>
			<td><font size=2><b>박스입수</b></td>
			<td><font size=2><b>재고</b></td>
			<td bgcolor='#F4F1EF'><font size=2><b>박스</b></td>
			<td bgcolor='#F4F1EF'><font size=2><b>낱개</b></td>
			<td><font size=2><b>총합</b></td>
		</tr>
	</thead>
	<tbody>

	<?
		if (sizeof($arr_rs) > 0) {
			for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

				$GOODS_CODE		     = trim($arr_rs[$j]["GOODS_CODE"]);
				$KANCODE		     = trim($arr_rs[$j]["KANCODE"]);
				$GOODS_NAME		     = trim($arr_rs[$j]["GOODS_NAME"]);
				$DELIVERY_CNT_IN_BOX = trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);
				$STOCK_CNT		     = trim($arr_rs[$j]["STOCK_CNT"]);
				$GOODS_CNT_SUM       = trim($arr_rs[$j]["GOODS_CNT_SUM"]);
				$per_box = floor($GOODS_CNT_SUM / $DELIVERY_CNT_IN_BOX);

			?>
			<tr>
				<td height="24" bgColor='#FFFFFF' align='left'><?=$GOODS_CODE?></td>
				<td bgColor='#FFFFFF' align='left'><?=$KANCODE?></td>
				<td bgColor='#FFFFFF' align='left'><?=$GOODS_NAME?></td>
				<td bgColor='#FFFFFF' align='right'><?=$DELIVERY_CNT_IN_BOX?></td>
				<td bgColor='#FFFFFF' align='right'><?=$STOCK_CNT?></td>
				<td bgColor='#FFFFFF' align='right'><b><?= ($per_box != 0 ? $per_box : "")?></b></td>
				<td bgColor='#FFFFFF' align='right'><b><?= $GOODS_CNT_SUM - ($DELIVERY_CNT_IN_BOX * $per_box)?></b></td>
				<td bgColor='#FFFFFF' align='right'><?=$GOODS_CNT_SUM?></td>
			</tr>
			
		<?
			}
		 }else {
		?>
			<tr class="order">
				<td height="50" align="center" colspan="6">데이터가 없습니다. </td>
			</tr>
		<?
		 }
		?>
	</tbody>
	<? } ?>
</table>
<br>
<br>
<font size=3><b>스티커 준비 수량</b></font> 
<br>
<TABLE border=1>
	<thead>
		<tr>
			<td><font size=2><b>상품코드</b></td>
			<td><font size=2><b>상품명</b></td>
			<td><font size=2><b>총합</b></td>
		</tr>
	</thead>
	<tbody>

	<?
		if (sizeof($arr_rs_sticker) > 0) {
			for ($j = 0 ; $j < sizeof($arr_rs_sticker); $j++) {

				$GOODS_CODE		     = trim($arr_rs_sticker[$j]["GOODS_CODE"]);
				$GOODS_NAME		     = trim($arr_rs_sticker[$j]["GOODS_NAME"]);
				$GOODS_CNT_SUM       = trim($arr_rs_sticker[$j]["GOODS_CNT_SUM"]);

			?>
			<tr>
				<td height="24" bgColor='#FFFFFF' align='left'><?=$GOODS_CODE?></td>
				<td bgColor='#FFFFFF' align='left'><?=$GOODS_NAME?></td>
				<td bgColor='#FFFFFF' align='right'><?=$GOODS_CNT_SUM?></td>
			</tr>
			
		<?
			}
		 }else {
		?>
			<tr class="order">
				<td height="50" align="center" colspan="3">데이터가 없습니다. </td>
			</tr>
		<?
		 }
		?>
	</tbody>
</table>


</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>