<?
ini_set('memory_limit',-1);
session_start();
?>
<?
# =============================================================================
# File Name    : admin_write.php
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
	$menu_right = "SG001"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/company/company.php";
#====================================================================
# Request Parameter
#====================================================================




	$file_name="입고 상품 리스트-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
	  header( "Content-Disposition: attachment; filename=$file_name" );
	  header( "Content-Description: orion70kr@gmail.com" );

	$nListCnt =totalCntStockGoods($conn, $con_in_cp_no, $con_out_cp_no, $search_field, $search_str);
	
	$nPageSize = $nListCnt;

	$nPage = 1;

	$arr_rs = listStockGoods($conn, $con_in_cp_no, $con_out_cp_no, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize, $nListCnt);

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
		<td align='center' bgcolor='#F4F1EF'>입고구분</td>
		<td align='center' bgcolor='#F4F1EF'>매입처</td>
		<td align='center' bgcolor='#F4F1EF'>입고수량</td>
		<td align='center' bgcolor='#F4F1EF'>매입단가</td>
		<td align='center' bgcolor='#F4F1EF'>창고위치</td>
		<td align='center' bgcolor='#F4F1EF'>창고상세</td>
		<td align='center' bgcolor='#F4F1EF'>입고일</td>
		<td align='center' bgcolor='#F4F1EF'>결제일</td>
	</tr>
	<?
		$nCnt = 0;
		
		if (sizeof($arr_rs) > 0) {
			
			for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

				$GOODS_NO						= trim($arr_rs[$j]["GOODS_NO"]);
				$GOODS_CODE					= trim($arr_rs[$j]["GOODS_CODE"]);
				$GOODS_NAME					= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
				$S_IN_QTY						= trim($arr_rs[$j]["S_IN_QTY"]);
				$S_IN_BQTY					= trim($arr_rs[$j]["S_IN_BQTY"]);
				$S_IN_FQTY					= trim($arr_rs[$j]["S_IN_FQTY"]);
				$S_OUT_QTY					= trim($arr_rs[$j]["S_OUT_QTY"]);
				$S_OUT_BQTY					= trim($arr_rs[$j]["S_OUT_BQTY"]);
				$S_OUT_FQTY					= trim($arr_rs[$j]["S_OUT_FQTY"]);
				$STOCK_CNT					= trim($arr_rs[$j]["STOCK_CNT"]);
				$BSTOCK_CNT					= trim($arr_rs[$j]["BSTOCK_CNT"]);
				$FSTOCK_CNT					= trim($arr_rs[$j]["FSTOCK_CNT"]);
				$MSTOCK_CNT					= trim($arr_rs[$j]["MSTOCK_CNT"]);

				$goods_rs = selectGoods($conn, $GOODS_NO);

				$RS_PRICE		= trim($goods_rs[0]["BUY_PRICE"]); 
				$RS_CP_NO		= trim($goods_rs[0]["CATE_03"]); 
				
				$company_rs = selectCompany($conn, $RS_CP_NO);
				$RS_CP_CODE = trim($company_rs[0]["CP_CODE"]); 


	?>
	<tr>
		<td bgColor='#FFFFFF' align='center'><?=$GOODS_CODE?></td>
		<td bgColor='#FFFFFF' align='left'><?=$GOODS_NAME?></td>
		<td bgColor='#FFFFFF' align='center'>정상입고</td>
		<td bgColor='#FFFFFF' align='center'><?=$RS_CP_CODE?></td>
		<td bgColor='#FFFFFF' align='right'></td>
		<td bgColor='#FFFFFF' align='right'><?=$RS_PRICE?></td>
		<td bgColor='#FFFFFF' align='left'>창고A</td>
		<td bgColor='#FFFFFF' align='left'></td>
		<td bgColor='#FFFFFF' align='center'><?=date("Y-m-d",strtotime("0 month"))?></td>
		<td bgColor='#FFFFFF' align='center'><?=date("Y-m-d",strtotime("0 month"))?></td>
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