<?session_start();?>
<?
# =============================================================================
# File Name    : confirm_order_detail_list.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2011.01.13
# Modify Date  : 
#	Copyright : Copyright @orion. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "SG005"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

#====================================================================
# common_header Check Session
#====================================================================
	include "../../_common/common_header.php"; 

	
#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/stock/stock.php";

#====================================================================
# DML Process
#====================================================================

	$file_name="입고 정산 상세 리스트-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
	  header( "Content-Disposition: attachment; filename=$file_name" );
	  header( "Content-Description: orion70kr@gmail.com" );

#====================================================================
# Request Parameter
#====================================================================

	$use_tf = "Y";
	$del_tf = "N";
#============================================================
# Page process
#============================================================

#===============================================================
# Get Search list count
#===============================================================
	//echo $p_confirm_ymd;
	//echo $p_buy_cp_no;

	$arr_rs = listConfirmCpStOrderList($conn, $p_confirm_ymd, $p_buy_cp_no, $use_tf, $del_tf, $search_field, $search_str);

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
	<tr>
		<td align='center' bgcolor='#F4F1EF'>상품명</td>
		<td align='center' bgcolor='#F4F1EF'>매입가</td>
		<td align='center' bgcolor='#F4F1EF'>수량</td>
		<td align='center' bgcolor='#F4F1EF'>합계</td>
		<td align='center' bgcolor='#F4F1EF'>결제일</td>
	</tr>
				<?
					$nCnt = 0;

					$SUM_PRICE = 0;
					$TOT_SUM_PRICE = 0;
					$TOT_QTY	= 0;
					
					if (sizeof($arr_rs) > 0) {
						
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$GOODS_NAME					= trim($arr_rs[$j]["GOODS_NAME"]);
							$GOODS_OPTION_NAME	= trim($arr_rs[$j]["GOODS_OPTION_NAME"]);
							
							$BUY_PRICE					= trim($arr_rs[$j]["BUY_PRICE"]);
							$QTY								= trim($arr_rs[$j]["QTY"]);
							$PAY_DATE						= trim($arr_rs[$j]["PAY_DATE"]);


							$SUM_PRICE = ($BUY_PRICE * $QTY);
							$TOT_SUM_PRICE = $TOT_SUM_PRICE + $SUM_PRICE;
							$TOT_QTY = $TOT_QTY + $QTY;
				
				?>
	<tr>
		<td bgColor='#FFFFFF' align='left'><?=$GOODS_NAME?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($BUY_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($QTY)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($SUM_PRICE)?></td>
		<td bgColor='#FFFFFF' align='center'><?=$PAY_DATE?></td>
	</tr>
				<?
						}
					}
				?>
</table>
</body>
</html>
<script type="text/javascript" src="../js/wrest.js"></script>
<?
#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);
?>