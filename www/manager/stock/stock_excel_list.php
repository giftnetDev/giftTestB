<?session_start();?>
<?
# =============================================================================
# File Name    : stock_list.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2015-07-01
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
	$menu_right = "SG007"; // 메뉴마다 셋팅 해 주어야 합니다

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
	
#====================================================================
# Request Parameter
#====================================================================

	$file_name="재고 리스트-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
	  header( "Content-Disposition: attachment; filename=$file_name" );

	if ($order_field == "")
		$order_field = "B.GOODS_NAME";

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize		= trim($nPageSize);

	$cp_type2		= trim($cp_type2);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
	$del_tf = "N";

	
	$exclude_category = "";


#===============================================================
# Get Search list count
#===============================================================
	
	$where_cause = "";
	if(count($qty_type) > 0 && ($min_qty <> "" || $max_qty <> "")) { 

		$where_cause .= " AND (";

		for($i=0; $i < count($qty_type); $i++) { 
			
			if($min_qty != "" && $max_qty != "")
				$where_cause .= " (".$qty_type[$i]." BETWEEN ".$min_qty." AND ".$max_qty." ) ".$qty_type_conjunction;
			else if($min_qty != "" && $max_qty == "")
				$where_cause .= " (".$qty_type[$i]." >= ".$min_qty." ) ".$qty_type_conjunction;
			else if($min_qty == "" && $max_qty != "")
				$where_cause .= " (".$qty_type[$i]." <= ".$max_qty." ) ".$qty_type_conjunction;
			else 
				$where_cause .= "";
		}

		$where_cause = rtrim($where_cause, $qty_type_conjunction);
		$where_cause .= " ) ";
	}

#===============================================================
# Get Search list count
#===============================================================

	$filter = array('is_same' => $is_same, 'is_under_mstock' => $is_under_mstock, 'is_not_zero' => $is_not_zero );

	/*
	$nListCnt =totalCntStockTotalGoods($conn, $start_date, $end_date, $con_in_cp_no, $con_out_cp_no, $con_cate, $where_cause, $filter, $search_field, $search_str);
	
	#echo $nListCnt;

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}
	*/

	$nPage = 1;
	$nPageSize = 10000;
	$nListCnt = 100000;

	$arr_rs = listStockTotalGoods($conn, $start_date, $end_date,$con_in_cp_no, $con_out_cp_no, $con_cate, $where_cause, $filter, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize, $nListCnt);


?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>

<body>
<font size=3><b><?=$Admin_shop_name?> 재고 리스트 </b></font> <br>
<br>
출력 일자 : [<?=date("Y년 m월 d일")?> ]
<br>
<br>
<TABLE border=1>
	<tr>
		<th align='center' bgcolor='#F4F1EF'>상품코드</th>
		<th align='center' bgcolor='#F4F1EF'>낱개바코드</th>
		<th align='center' bgcolor='#F4F1EF'>상품명</th>
		<th align='center' bgcolor='#F4F1EF'>정상재고</th>
		<th align='center' bgcolor='#F4F1EF'>가재고</th>
		<th align='center' bgcolor='#F4F1EF'>불량재고</th>
		<th align='center' bgcolor='#F4F1EF'>선출고</th>
		<th align='center' bgcolor='#F4F1EF'>최소재고</th>
		<th align='center' bgcolor='#F4F1EF'>박스입수</th>
		<th align='center' bgcolor='#F4F1EF'>비고</th>
	</tr>
	<?
		$nCnt = 0;
		
		if (sizeof($arr_rs) > 0) {
			for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
				
				$rn									= trim($arr_rs[$j]["rn"]);
				$GOODS_NO						= trim($arr_rs[$j]["GOODS_NO"]);
				$GOODS_CODE					= trim($arr_rs[$j]["GOODS_CODE"]);
				$KANCODE					= trim($arr_rs[$j]["KANCODE"]);
				$GOODS_NAME					= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
				$S_IN_QTY					= trim($arr_rs[$j]["S_IN_QTY"]);
				$S_IN_BQTY					= trim($arr_rs[$j]["S_IN_BQTY"]);
				$S_IN_FQTY					= trim($arr_rs[$j]["S_IN_FQTY"]);
				$S_OUT_QTY					= trim($arr_rs[$j]["S_OUT_QTY"]);
				$S_OUT_BQTY					= trim($arr_rs[$j]["S_OUT_BQTY"]);
				$S_OUT_TQTY					= trim($arr_rs[$j]["S_OUT_TQTY"]);
				$STOCK_CNT					= trim($arr_rs[$j]["STOCK_CNT"]);
				$BSTOCK_CNT					= trim($arr_rs[$j]["BSTOCK_CNT"]);
				$FSTOCK_CNT					= trim($arr_rs[$j]["FSTOCK_CNT"]);
				$TSTOCK_CNT					= trim($arr_rs[$j]["TSTOCK_CNT"]);
				$MSTOCK_CNT					= trim($arr_rs[$j]["MSTOCK_CNT"]);
				$DELIVERY_CNT_IN_BOX		= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);
				$MEMO						= trim($arr_rs[$j]["MEMO"]);
				
				$CAL_QTY = $S_IN_QTY - $S_OUT_QTY;
				$CAL_BQTY = $S_IN_BQTY - $S_OUT_BQTY;
				$CAL_FQTY = $S_IN_FQTY; 
				$CAL_TQTY = - $S_OUT_TQTY;
	?>
	<tr>
		<td bgColor='#FFFFFF' align='center'><?=$GOODS_CODE?></td>
		<td bgColor='#FFFFFF' align='center'><?=$KANCODE?></td>
		<td bgColor='#FFFFFF' align='left'><?= $GOODS_NAME?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($CAL_QTY)?>
			<? if ($CAL_QTY != $STOCK_CNT) { ?>
			(<?=number_format($STOCK_CNT)?>)
			<? } ?>
		</td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($CAL_FQTY)?>
		<? if ($CAL_FQTY != $FSTOCK_CNT) { ?>
			(<?=number_format($FSTOCK_CNT)?>)
			<? } ?>
		</td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($CAL_BQTY)?>
			<? if ($CAL_BQTY != $BSTOCK_CNT) { ?>
			(<?=number_format($BSTOCK_CNT)?>)
			<? } ?>
		</td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($CAL_TQTY)?>
			<? if ($CAL_TQTY != $TSTOCK_CNT) { ?>
			(<?=number_format($TSTOCK_CNT)?>)
			<? } ?>
		</td>
		<td bgColor='#FFFFFF' align='right'><?= $MSTOCK_CNT?></td>
		<td bgColor='#FFFFFF' align='right'><?= $DELIVERY_CNT_IN_BOX?></td>
		<td bgColor='#FFFFFF' align='left'><?= $MEMO?></td>
	</tr>
	<?
		}
	}else{
		?>
	<tr>
		<td align="center" colspan="9">데이터가 없습니다. </td>
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