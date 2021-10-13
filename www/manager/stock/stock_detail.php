<?session_start();?>
<?
# =============================================================================
# File Name    : stock_detail.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2015-07-02
# Modify Date  : 
#	Copyright : Copyright @Giftnet Corp. All Rights Reserved.
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

	$mm_subtree	 = "3";

	$del_tf = "N";
	
#===============================================================
# Get Search list count
#===============================================================
	$arr_rs = getDetailGoodsStock($conn, $goods_no);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
</head>

<body id="popup_order_wide" onload="init();">

<div id="popupwrap_order_wide">
	<h1>재고 상세 조회</h1>
	<div id="postsch_code">

		<div class="addr_inp">
		<h2>* 재고 조회</h2>
		
		<table cellpadding="0" cellspacing="0" width="500" class="rowstable" border="0">
			<colgroup>
					<col width="15%" />
					<col width="20%" />
					<col width="8%"/>
					<col width="8%" />
					<col width="8%" />
					<col width="8%" />
					<col width="8%" />
					<col width="8%" />
					<col width="8%" />
					<col width="9%" />
			</colgroup>
			<tr>
				<th>상품코드</th>
				<th>상품명</th>
				<th>정상재고</th>
				<th>가재고</th>
				<th>불량재고</th>
				<th>선출고</th>
				<th class="end">사유</th>
			</tr>
			<?
				$nCnt = 0;
				
				if (sizeof($arr_rs) > 0) {
					for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
						
						$rn									= trim($arr_rs[$j]["rn"]);
						$GOODS_NO						= trim($arr_rs[$j]["GOODS_NO"]);
						$GOODS_CODE					= trim($arr_rs[$j]["GOODS_CODE"]);
						$GOODS_NAME					= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
						$S_IN_QTY						= trim($arr_rs[$j]["S_IN_QTY"]);
						$S_IN_BQTY					= trim($arr_rs[$j]["S_IN_BQTY"]);
						$S_IN_FQTY					= trim($arr_rs[$j]["S_IN_FQTY"]);
						$S_OUT_QTY					= trim($arr_rs[$j]["S_OUT_QTY"]);
						$S_OUT_BQTY					= trim($arr_rs[$j]["S_OUT_BQTY"]);
						$S_OUT_TQTY					= trim($arr_rs[$j]["S_OUT_TQTY"]);
						$STOCK_CNT					= trim($arr_rs[$j]["STOCK_CNT"]);
						$BSTOCK_CNT					= trim($arr_rs[$j]["BSTOCK_CNT"]);
						$FSTOCK_CNT					= trim($arr_rs[$j]["FSTOCK_CNT"]);
						$IN_LOC							= trim($arr_rs[$j]["IN_LOC"]);
						
						$CAL_QTY = $S_IN_QTY - $S_OUT_QTY;
						$CAL_BQTY = $S_IN_BQTY - $S_OUT_BQTY;
						$CAL_FQTY = $S_IN_FQTY;
						$CAL_TQTY = - $S_OUT_TQTY;

			?>
			<tr height="37">
				<td class="modeual_nm"><?=$GOODS_CODE?></td>
				<td class="modeual_nm"><a href="javascript:js_view('<?=$GOODS_NO?>');"><?= $GOODS_NAME?></a></td>
				<td class="price"><?=number_format($CAL_QTY)?></td>
				<td class="price"><?=number_format($CAL_FQTY)?></td>
				<td class="price"><?=number_format($CAL_BQTY)?></td>
				<td class="price"><?=number_format($CAL_TQTY)?></td>
				<td>
					<?= getDcodeName($conn, 'LOC', $IN_LOC);?>
				</td>
			</tr>
			<?
										}
									}
			?>
		</table>


	</div>
</div>
<div class="sp50"></div>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
</div>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>