<?
ini_set('memory_limit',-1);
session_start();
?>
<?

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "OD003"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/member/member.php";
	require "../../_classes/biz/payment/payment.php";
#====================================================================
# Request Parameter
#====================================================================

	$file_name="현재주문상품리스트-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
	  header( "Content-Disposition: attachment; filename=$file_name" );
	  header( "Content-Description: orion70kr@gmail.com" );

	$order_goods_list = listTempOrderCnt($conn, $temp_no, $has_island);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<style>td { mso-number-format:\@; } </style> 
</head>
<body>
<TABLE border=1>
	<tr>
		<td align='center' bgcolor='#F4F1EF'>공급사</td>
		<td align='center' bgcolor='#F4F1EF'>상품코드</td>
		<td align='center' bgcolor='#F4F1EF'>바코드</td>
		<td align='center' bgcolor='#F4F1EF'>상품명</td>
		<td align='center' bgcolor='#F4F1EF'>박스입수</td>
		<td align='center' bgcolor='#F4F1EF'>재고</td>
		<td align='center' bgcolor='#F4F1EF'>박스</td>
		<td align='center' bgcolor='#F4F1EF'>낱개</td>
		<td align='center' bgcolor='#F4F1EF'>총합</td>
		<td align='center' bgcolor='#F4F1EF'>예측잔여</td>

	</tr>
				<?
					if (sizeof($order_goods_list) > 0) {
						
						for ($j = 0 ; $j < sizeof($order_goods_list); $j++) {
							//GOODS_NO	GOODS_CODE	GOODS_NAME	CNT
							$CP_NM				 = trim($order_goods_list[$j]["CP_NM"]);
							$GOODS_NO			 = trim($order_goods_list[$j]["GOODS_NO"]);
							$GOODS_CODE			 = trim($order_goods_list[$j]["GOODS_CODE"]);
							$KANCODE			 = trim($order_goods_list[$j]["KANCODE"]);
							$CATE_NAME		     = trim($order_goods_list[$j]["CATE_NAME"]);
							//$CATE_02			 = trim($order_goods_list[$j]["CATE_02"]);
							//$CATE_02			 = getDcodeName($conn, "GOODS_SUB_CATE", $CATE_02);
							$GOODS_NAME			 = SetStringFromDB($order_goods_list[$j]["GOODS_NAME"]);
							$DELIVERY_CNT_IN_BOX = trim($order_goods_list[$j]["DELIVERY_CNT_IN_BOX"]);
							$STOCK_CNT			 = trim($order_goods_list[$j]["STOCK_CNT"]);
							$BSTOCK_CNT			 = trim($order_goods_list[$j]["BSTOCK_CNT"]);
							$CNT				 = trim($order_goods_list[$j]["CNT"]);
							$per_box = floor($CNT / $DELIVERY_CNT_IN_BOX);

				?>
	<tr>
		<td bgColor='#FFFFFF' align='left'><?=$CP_NM?></td>
		<td bgColor='#FFFFFF' align='left'><?=$GOODS_CODE?></td>
		<td bgColor='#FFFFFF' align='left'><?=$KANCODE?></td>
		<td bgColor='#FFFFFF' align='left'><?=$GOODS_NAME?></td>
		<td bgColor='#FFFFFF' align='right'><?=$DELIVERY_CNT_IN_BOX?></td>
		<td bgColor='#FFFFFF' align='right'><?=$STOCK_CNT?></td>
		<td bgColor='#FFFFFF' align='right'><?=$per_box?></td>
		<td bgColor='#FFFFFF' align='right'><?=$CNT - ($DELIVERY_CNT_IN_BOX * $per_box)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($CNT)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($STOCK_CNT - $CNT)?></td>
	</tr>

				<?			
						}
					} else {
				?> 
	<tr>
		<td colspan="9">데이터가 없습니다</td>
	</tr>

				<?
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