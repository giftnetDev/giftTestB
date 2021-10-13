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
	$menu_right = "CF008"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/confirm/confirm.php";


#====================================================================
# Request Parameter
#====================================================================

	

	
#===============================================================
# Get Search list count
#===============================================================

	$arr_rs = listUndeliveredOrderGoods($conn);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />

<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery.floatThead.min.js"></script>
<script>
	$(function(){
		$('table.fixed_header_table').floatThead({
			position: 'fixed'
		});
	});
</script>
<script language="javascript">
	
	function js_search()
	{
		var frm = document.frm;

		frm.mode.value = "S";
		frm.target = "";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

</script>
<style>
	.top_group td {border-top: 2px solid black;  }
	.bottom_group td {border-top: 1px dotted black; }
	table.rowstable td {background: none;}
	table.rowstable {border-bottom: 2px solid black;} 
	.btnright {text-align:right; padding-right:50px; margin: 4px 0;}
</style>
</head>

<body id="popup_order_wide">

<div id="popupwrap_order_wide">
	<h1>미배송 주문 조회</h1>
	<div id="postsch_code">

		<div class="addr_inp">

<form name="frm" method="post">
<input type="hidden" name="mode" value="">
<input type="hidden" name="keyword" value="">
<input type="hidden" name="target_name" value="<?=$target_name?>">
<input type="hidden" name="target_value" value="<?=$target_value?>">

	<div class="btnright">
		<input type="text" value="<?=$search_str?>" name="search_str" size="20" class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();" />
		<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
	</div>
	<table cellpadding="0" cellspacing="0" class="rowstable01 fixed_header_table">
		<colgroup>
			<col width="5%">
			<col width="*">
			<col width="10%">
			<col width="12%">
			<col width="10%">
			<col width="7%">
			<col width="6%">
			<col width="7%">
			<col width="7%">
		</colgroup>
		<thead>
			<tr>
				<th>주문번호</th>
				<th>업체명 - 지점명</th>
				<th>상품코드</th>
				<th>상품명</th>
				<th>판매가</th>
				<th>원주문수량</th>
				<th>배송예정수량</th>
				<th>개별잔여수량</th>
				<th>영업담당자</th>
				<th class="end">총액</th>
			</tr>
		</thead>
		<tbody>
		<?
			$nCnt = 0;
			
			if (sizeof($arr_rs) > 0) {
				
				for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
					
					//O.RESERVE_NO, C.CP_CODE, CONCAT( C.CP_NM,  ' ', CP_NM2 ) AS CP_NAME, OG.GOODS_CODE, OG.GOODS_NAME, OG.SALE_PRICE, OG.QTY, IFNULL(K.REFUNDABLE_QTY, 0) AS REFUNDABLE_QTY, IFNULL(OGI.SUB_SUM, 0) AS SUM_SUB_QTY, A.ADM_NAME\

					$RESERVE_NO				= SetStringFromDB($arr_rs[$j]["RESERVE_NO"]);
					$CP_CODE				= SetStringFromDB($arr_rs[$j]["CP_CODE"]);
					$CP_NAME				= SetStringFromDB($arr_rs[$j]["CP_NAME"]);
					$GOODS_CODE				= SetStringFromDB($arr_rs[$j]["GOODS_CODE"]);
					$GOODS_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
					$SALE_PRICE				= SetStringFromDB($arr_rs[$j]["SALE_PRICE"]);
					$QTY					= SetStringFromDB($arr_rs[$j]["QTY"]);
					$REFUNDABLE_QTY			= SetStringFromDB($arr_rs[$j]["REFUNDABLE_QTY"]);
					$SUM_SUB_QTY			= SetStringFromDB($arr_rs[$j]["SUM_SUB_QTY"]);
					$ADM_NAME				= SetStringFromDB($arr_rs[$j]["ADM_NAME"]);

					$ADM_NAME = getAdminName($conn, $ADM_NAME); 

					$BALANCE = $SALE_PRICE * (($REFUNDABLE_QTY == 0 ? $QTY : $REFUNDABLE_QTY) - $SUM_SUB_QTY);
					

		?>
			<tr height="40">
				<td><?= $RESERVE_NO ?></td>
				<td><?= $CP_CODE ?></td>
				<td class="modeual_nm"><?= $CP_NAME ?></td>
				<td><?= $GOODS_CODE ?></td>
				<td class="modeual_nm"><?= $GOODS_NAME ?></td>
				<td><?= $SALE_PRICE ?></td>
				<td><?= $QTY ?></td>
				<td><?= $REFUNDABLE_QTY ?></td>
				<td><?= $SUM_SUB_QTY ?></td>
				<td><?= $ADM_NAME ?></td>
				<td class="price"><?= number_format($BALANCE) ?>원</td>
			</tr>
		<?			
						}
					} else { 
				?> 
					<tr>
						<td align="center" height="50"  colspan="11">데이터가 없습니다. </td>
					</tr>
				<? 
					}
				?>
		</tbody>
	</table>

<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
<div class="bot_close" style="width:auto;"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
</div>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>