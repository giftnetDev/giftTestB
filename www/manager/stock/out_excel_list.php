<?session_start();?>
<?
# =============================================================================
# File Name    : in_list.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2015-06-26
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
	$menu_right = "SG006"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/stock/stock.php";

	$file_name="출고 상세 리스트-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
	  header( "Content-Disposition: attachment; filename=$file_name" );
	  header( "Content-Description: orion70kr@gmail.com" );

#====================================================================
# Request Parameter
#====================================================================

	$mm_subtree	 = "3";

	if ($start_date == "") {
		$start_date = date("Y-m-d",strtotime("-1 month"));
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));
	} else {
		$end_date = trim($end_date);
	}

	if ($order_field == "")
		$order_field = "REG_DATE";

	$con_stock_type = "OUT";
	$con_in_cp_no = "";

	$con_stock_code = trim($con_stock_code);
	$cp_type2 = trim($cp_type2);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
	$del_tf = "N";
#============================================================
# Page process
#============================================================

	$nPage = 1;

	if ($nPageSize <> "") {
		$nPageSize = (int)($nPageSize);
	} else {
		$nPageSize = 20;
	}

	$nPageBlock	= 10;
	
#	echo $start_date;
#	echo $end_date;

#===============================================================
# Get Search list count
#===============================================================

	$nListCnt =totalCntStock($conn, $start_date, $end_date, $con_stock_type, $con_stock_code, $con_in_cp_no, $cp_type2, $sel_loc, $filter, $del_tf, $search_field, $search_str);
	
	$nPageSize = $nListCnt;

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listStock($conn, $start_date, $end_date, $con_stock_type, $con_stock_code, $con_in_cp_no, $cp_type2, $sel_loc, $filter, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize, $nListCnt);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>

</head>

<body>
<font size=3><b><?=$Admin_shop_name?> 출고 상세 리스트 </b></font> <br>
<br>
출력 일자 : [<?=date("Y년 m월 d일")?> ]
<br>
<br>
<TABLE border=1>
	<tr>
		<td align='center' bgcolor='#F4F1EF'>출고일</td>
		<td align='center' bgcolor='#F4F1EF'>업체명</td>
		<td align='center' bgcolor='#F4F1EF'>상품명</td>
		<td align='center' bgcolor='#F4F1EF'>재고구분</td>
		<td align='center' bgcolor='#F4F1EF'>매입가</td>
		<td align='center' bgcolor='#F4F1EF'>수량</td>
		<td align='center' bgcolor='#F4F1EF'>출고사유</td>
		<td align='center' bgcolor='#F4F1EF'>사유상세</td>
		<td align='center' bgcolor='#F4F1EF'>출고일</td>
		<td align='center' bgcolor='#F4F1EF'>등록일</td>
	</tr>
	<?
		$nCnt = 0;
		
		if (sizeof($arr_rs) > 0) {
			for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
				
				$rn									= trim($arr_rs[$j]["rn"]);
				$OUT_DATE						= trim($arr_rs[$j]["OUT_DATE"]);
				$STOCK_NO						= trim($arr_rs[$j]["STOCK_NO"]);
				$GOODS_NAME					= SetStringFromDB(trim($arr_rs[$j]["GOODS_NAME"]));
				$OUT_PRICE					= trim($arr_rs[$j]["OUT_PRICE"]);
				$OUT_QTY						= trim($arr_rs[$j]["OUT_QTY"]);
				$OUT_BQTY						= trim($arr_rs[$j]["OUT_BQTY"]);
				$OUT_TQTY						= trim($arr_rs[$j]["OUT_TQTY"]);
				$PAY_DATE						= trim($arr_rs[$j]["PAY_DATE"]);
				$OUT_CP_NO					= trim($arr_rs[$j]["OUT_CP_NO"]);
				$STOCK_CODE					= trim($arr_rs[$j]["STOCK_CODE"]);
				$IN_LOC							= trim($arr_rs[$j]["IN_LOC"]);
				$IN_LOC_EXT						= trim($arr_rs[$j]["IN_LOC_EXT"]);

				$REG_DATE						= trim($arr_rs[$j]["REG_DATE"]);
				
				$OUT_DATE			= date("Y-m-d",strtotime($OUT_DATE));
				$REG_DATE			= date("Y-m-d",strtotime($REG_DATE));
				$PAY_DATE			= date("Y-m-d",strtotime($PAY_DATE));

				if (left($STOCK_CODE,1) == "N") {
					$QTY = $OUT_QTY;
				} else if (left($STOCK_CODE,1) == "B") {
					$QTY = $OUT_BQTY;
				} else if (left($STOCK_CODE,1) == "T") {
					$QTY = $OUT_TQTY;
				}
				
	?>
	<tr>
		<td bgColor='#FFFFFF'><?=$OUT_DATE?></td>
		<td bgColor='#FFFFFF' align='left'><?= getCompanyName($conn, $OUT_CP_NO);?></td>
		<td bgColor='#FFFFFF' align='left'><?= $GOODS_NAME?></td>
		<td bgColor='#FFFFFF'><?=getDcodeName($conn, "OUT_ST", $STOCK_CODE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($OUT_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($QTY)?></td>
		<td bgColor='#FFFFFF'><?=getDcodeName($conn, "LOC", $IN_LOC)?></td>
		<td bgColor='#FFFFFF'><?=$IN_LOC_EXT?></td>
		<td bgColor='#FFFFFF'><?=$OUT_DATE?></td>
		<td bgColor='#FFFFFF'><?=$REG_DATE?></td>
	</tr>
	<?
			}
	?>
	<?
		}else{
	?>
		<tr>
			<td height="50" align="center" colspan="10">데이터가 없습니다. </td>
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