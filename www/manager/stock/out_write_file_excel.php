<?
ini_set('memory_limit',-1);
session_start();
?>
<?
# =============================================================================
# File Name    : out_write_file_excel.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright    : Copyright @C&C Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "SG008"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/stock/stock.php";
	require "../../_classes/biz/goods/goods.php";

#====================================================================
# Request Parameter
#====================================================================

	$file_name="미등록출고리스트-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
	  header( "Content-Disposition: attachment; filename=$file_name" );
	  header( "Content-Description: orion70kr@gmail.com" );

	$arr_rs = listTempStock($conn, $temp_no);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
</head>
<body>
<TABLE border=1>
	<tr>
		<td align='center' bgcolor='#F4F1EF'>상품코드</td>
		<td align='center' bgcolor='#F4F1EF'>상품명</td>
		<td align='center' bgcolor='#F4F1EF'>출고구분</td>
		<td align='center' bgcolor='#F4F1EF'>매출처</td>
		<td align='center' bgcolor='#F4F1EF'>출고수량</td>
		<td align='center' bgcolor='#F4F1EF'>출고단가</td>
		<td align='center' bgcolor='#F4F1EF'>출고사유</td>
		<td align='center' bgcolor='#F4F1EF'>사유상세</td>
		<td align='center' bgcolor='#F4F1EF'>출고일</td>
	</tr>
	<?
		$nCnt = 0;
		
		if (sizeof($arr_rs) > 0) {
			
			for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
				
				$GOODS_CODE			= trim($arr_rs[$j]["GOODS_CODE"]);
				$GOODS_NAME			= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
				$GOODS_NAME			= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
				$STOCK_CODE			= trim($arr_rs[$j]["STOCK_CODE"]);
				$CP_CODE				= trim($arr_rs[$j]["CP_CODE"]);
				$QTY						= trim($arr_rs[$j]["QTY"]);
				$PRICE					= trim($arr_rs[$j]["PRICE"]);
				$IN_LOC					= SetStringFromDB($arr_rs[$j]["IN_LOC"]);
				$IN_LOC_EXT			= SetStringFromDB($arr_rs[$j]["IN_LOC_EXT"]);
				$IN_DATE				= SetStringFromDB($arr_rs[$j]["IN_DATE"]);
				$PAY_DATE				= SetStringFromDB($arr_rs[$j]["PAY_DATE"]);

				$IN_DATE	= left($IN_DATE,10);
				$PAY_DATE = left($PAY_DATE,10);

	?>
	<tr>
		<td bgColor='#FFFFFF' align='center'><?=$GOODS_CODE?></td>
		<td bgColor='#FFFFFF' align='left'><?=$GOODS_NAME?></td>
		<td bgColor='#FFFFFF' align='center'><?=$STOCK_CODE?></td>
		<td bgColor='#FFFFFF' align='center'><?=$CP_CODE?></td>
		<td bgColor='#FFFFFF' align='right'><?=$QTY?></td>
		<td bgColor='#FFFFFF' align='right'><?=$PRICE?></td>
		<td bgColor='#FFFFFF' align='left'><?=$IN_LOC?></td>
		<td bgColor='#FFFFFF' align='left'><?=$IN_LOC_EXT?></td>
		<td bgColor='#FFFFFF' align='center'><?=$IN_DATE?></td>
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