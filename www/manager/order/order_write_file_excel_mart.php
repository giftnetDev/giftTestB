<?
ini_set('memory_limit',-1);
session_start();
?>
<?
# =============================================================================
# File Name    : order_write_file_excel_mart.php
# =============================================================================

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

	$file_name="미등록주문리스트-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
	  header( "Content-Disposition: attachment; filename=$file_name" );

	$arr_rs = listTempOrderForMart4Later($conn, $temp_no);
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
		<td align='center' bgcolor='#F4F1EF'>상품고유번호</td>
		<td align='center' bgcolor='#F4F1EF'>마트주문번호</td>
		<td align='center' bgcolor='#F4F1EF'>판매사번호</td>
		<td align='center' bgcolor='#F4F1EF'>상품명</td>
		<td align='center' bgcolor='#F4F1EF'>상품가격</td>
		<td align='center' bgcolor='#F4F1EF'>주문수량</td>
		<td align='center' bgcolor='#F4F1EF'>옵션명</td>
		<td align='center' bgcolor='#F4F1EF'>마트상품번호</td>
		<td align='center' bgcolor='#F4F1EF'>주문자</td>
		<td align='center' bgcolor='#F4F1EF'>주문자연락처</td>
		<td align='center' bgcolor='#F4F1EF'>주문자휴대전화번호</td>
		<td align='center' bgcolor='#F4F1EF'>수취인</td>
		<td align='center' bgcolor='#F4F1EF'>수취인연락처</td>
		<td align='center' bgcolor='#F4F1EF'>수취인휴대전화번호</td>
		<td align='center' bgcolor='#F4F1EF'>우편번호</td>
		<td align='center' bgcolor='#F4F1EF'>주소</td>
		<td align='center' bgcolor='#F4F1EF'>주문자메모</td>
		<td align='center' bgcolor='#F4F1EF'>배송비</td>
		<td align='center' bgcolor='#F4F1EF'>3자 물류비</td>
		<td align='center' bgcolor='#F4F1EF'>스티커코드(옵션)</td>
		<td align='center' bgcolor='#F4F1EF'>스티커메세지(옵션)</td>
	</tr>
					<?
						
						if (sizeof($arr_rs) > 0) {
							
							for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
								
								// CP_ORDER_NO, CP_NO, GOODS_NAME, GOODS_PRICE, QTY, GOODS_OPTION_NM, GOODS_OPTION_NM2, GOODS_MART_CODE, O_NAME, O_PHONE, O_HPHONE, R_NAME, R_PHONE, R_HPHONE, R_ZIPCODE, R_ADDR1, MEMO, DELIVERY, SA_DELIVERY
								
								//echo $j;
								
								$CP_ORDER_GOODS			= trim($arr_rs[$j]["CP_ORDER_GOODS"]);
								$CP_ORDER_NO			= trim($arr_rs[$j]["CP_ORDER_NO"]);
								$CP_NO					= trim($arr_rs[$j]["CP_NO"]);
								$GOODS_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
								$GOODS_SUB_NAME		    = SetStringFromDB($arr_rs[$j]["GOODS_SUB_NAME"]);
								$GOODS_PRICE		    = trim($arr_rs[$j]["GOODS_PRICE"]);
								$QTY					= trim($arr_rs[$j]["QTY"]);
								$GOODS_MART_CODE	    = SetStringFromDB($arr_rs[$j]["GOODS_MART_CODE"]);
								$O_NAME					= SetStringFromDB($arr_rs[$j]["O_NAME"]);
								$O_PHONE				= SetStringFromDB($arr_rs[$j]["O_PHONE"]);
								$O_HPHONE				= SetStringFromDB($arr_rs[$j]["O_HPHONE"]);
								$R_NAME					= SetStringFromDB($arr_rs[$j]["R_NAME"]);
								$R_PHONE				= SetStringFromDB($arr_rs[$j]["R_PHONE"]);
								$R_HPHONE				= SetStringFromDB($arr_rs[$j]["R_HPHONE"]);
								$R_ZIPCODE			    = SetStringFromDB($arr_rs[$j]["R_ZIPCODE"]);
								$R_ADDR1				= SetStringFromDB($arr_rs[$j]["R_ADDR1"]);
								$MEMO					= SetStringFromDB(trim($arr_rs[$j]["MEMO"]));
								$DELIVERY				= trim($arr_rs[$j]["DELIVERY"]);
								$SA_DELIVERY		    = trim($arr_rs[$j]["SA_DELIVERY"]);
								$GOODS_OPTION_02	    = SetStringFromDB($arr_rs[$j]["GOODS_OPTION_02"]);
								$GOODS_OPTION_03	    = SetStringFromDB($arr_rs[$j]["GOODS_OPTION_03"]);
								$GOODS_OPTION_NM_04	    = SetStringFromDB($arr_rs[$j]["GOODS_OPTION_NM_04"]);
								
								//3pl 위함
								$CP_ORDER_NO = $GOODS_OPTION_NM_04;

					?>
					<tr>
						<td bgColor='#FFFFFF' align='left'><?=$CP_ORDER_GOODS?></td>
						<td bgColor='#FFFFFF' align='left'><?=$CP_ORDER_NO?></td>
						<td bgColor='#FFFFFF' align='left'><?=$CP_NO?></td>
						<td bgColor='#FFFFFF' align='left'><?=$GOODS_NAME?></td>
						<td bgColor='#FFFFFF' align='left'><?=$GOODS_PRICE?></td>
						<td bgColor='#FFFFFF' align='left'><?=$QTY?></td>
						<td bgColor='#FFFFFF' align='left'><?=$GOODS_SUB_NAME?></td>
						<td bgColor='#FFFFFF' align='left'><?=$GOODS_MART_CODE?></td>
						<td bgColor='#FFFFFF' align='left'><?=$O_NAME?></td>
						<td bgColor='#FFFFFF' align='left'><?=$O_PHONE?></td>
						<td bgColor='#FFFFFF' align='left'><?=$O_HPHONE?></td>
						<td bgColor='#FFFFFF' align='left'><?=$R_NAME?></td>
						<td bgColor='#FFFFFF' align='left'><?=$R_PHONE?></td>
						<td bgColor='#FFFFFF' align='left'><?=$R_HPHONE?></td>
						<td bgColor='#FFFFFF' align='left'><?=$R_ZIPCODE?></td>
						<td bgColor='#FFFFFF' align='left'><?=$R_ADDR1?></td>
						<td bgColor='#FFFFFF' align='left'><?=$MEMO?></td>
						<td bgColor='#FFFFFF' align='left'><?=$DELIVERY?></td>
						<td bgColor='#FFFFFF' align='left'><?=$SA_DELIVERY?></td>
						<td bgColor='#FFFFFF' align='left'><?=$GOODS_OPTION_02?></td>
						<td bgColor='#FFFFFF' align='left'><?=$GOODS_OPTION_03?></td>
					</tr>
					
					<?

										$err_str = "";
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