<?session_start();?>
<?
# =============================================================================
# File Name    : order_write.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @CNC Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "SP009"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/member/member.php";
	require "../../_classes/biz/payment/payment.php";
	require "../../_classes/biz/board/board.php";

#====================================================================
# Request Parameter
#====================================================================

	$order_goods_no		= trim($order_goods_no);

	if ($mode == "U") {
		
		$result = updateOrderGoodsSalePrice($conn, $old_sale_price, $sale_price, $qty, $order_state, $order_goods_no, $reserve_no);
		
		if ($result) {
?>
<script type="text/javascript">
	window.opener.location.reload();
	alert("수정 되었습니다.");
	self.close();
</script>
<?
			mysql_close($conn);
			exit;
		}
	}

	$del_tf = "N";
	
#===============================================================
# Get Search list count
#===============================================================
	$arr_order_rs = selectOrderGoods($conn, $order_goods_no);
	
	if (sizeof($arr_order_rs) > 0) {
		
		$j = 0;
		$ORDER_GOODS_NO				= trim($arr_order_rs[$j]["ORDER_GOODS_NO"]);
		$RESERVE_NO						= trim($arr_order_rs[$j]["RESERVE_NO"]);
		$GOODS_NAME						= SetStringFromDB($arr_order_rs[$j]["GOODS_NAME"]);
		$GOODS_SUB_NAME				= SetStringFromDB($arr_order_rs[$j]["GOODS_SUB_NAME"]);
		$GOODS_NO							= trim($arr_order_rs[$j]["GOODS_NO"]);
		$BUY_PRICE						= trim($arr_order_rs[$j]["BUY_PRICE"]);
		$SALE_PRICE						= trim($arr_order_rs[$j]["SALE_PRICE"]);
		$ORDER_STATE					= trim($arr_order_rs[$j]["ORDER_STATE"]);
		$QTY									= trim($arr_order_rs[$j]["QTY"]);

	}
	/*
	C.ON_UID, C.ORDER_GOODS_NO, C.RESERVE_NO, C.BUY_CP_NO, C.MEM_NO, C.ORDER_SEQ, C.GOODS_NO, C.GOODS_CODE, C.GOODS_NAME, C.GOODS_SUB_NAME, 
	C.QTY, C.GOODS_OPTION_01, C.GOODS_OPTION_02, C.GOODS_OPTION_03,
	C.GOODS_OPTION_04, C.GOODS_OPTION_NM_01, C.GOODS_OPTION_NM_02,
	C.GOODS_OPTION_NM_03, C.GOODS_OPTION_NM_04, C.CATE_01, C.CATE_02,
	C.CATE_03, C.CATE_04, C.BUY_PRICE, C.SALE_PRICE, C.EXTRA_PRICE, C.DELIVERY_PRICE, C.SA_DELIVERY_PRICE, C.TAX_TF, C.USE_TF, C.DEL_TF, C.REG_ADM, C.REG_DATE, C.DEL_ADM, C.DEL_DATE,
	G.FILE_NM_100, C.ORDER_DATE, C.FINISH_DATE, C.PAY_DATE, C.ORDER_STATE,
	*/
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />

<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/calendar.js"></script>
<script language="javascript">


function js_reload() {
	window.location.reload();
	window.opener.location.reload();
}


function js_save() {
	var frm = document.frm;
	
	frm.mode.value = "U";
	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();

}


function init() {
	window.resizeTo(870,350);
}

</script>
</head>

<body id="popup_order" onload="init();">

<div id="popupwrap_order">
	<h1>판매 가격 수정</h1>
	<div id="postsch_code">

		<div class="addr_inp">
		<h2>* 판매 가격 조회</h2>

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="reserve_no" value="<?=$RESERVE_NO?>">
<input type="hidden" name="order_goods_no" value="<?=$order_goods_no?>">
<input type="hidden" name="mode" value="">

		<table cellpadding="0" cellspacing="0" class="rowstable" border="0">
			<colgroup>
				<col width="7%" />
				<col width="47%" />
				<col width="12%" />
				<col width="12%" />
				<col width="10%" />
				<col width="12%" />
			</colgroup>
			<tr>
				<th>상품코드</th>
				<th>상품명</th>
				<th>공급가</th>
				<th>판매가</th>
				<th>수량</th>
				<th class="end">합계</th>
			</tr>
			<tr>
				<td>
					<?=$GOODS_NO?>
				</td>
				<td class="modeual_nm"><?=$GOODS_NAME?><br><?=$GOODS_SUB_NAME?></td>
				<td class="price">
					<?=number_format($BUY_PRICE) ?>
				</td>
				<td class="price">
					<input type="text" name="sale_price" value="<?=$SALE_PRICE ?>" class="txt" style="width:70%" onkeyup="return isPhoneNumber(this)"> &nbsp;
					<input type="hidden" name="old_sale_price" value="<?=$SALE_PRICE ?>">
				</td>
				<td class="price">
					<?=number_format($QTY) ?>
					<input type="hidden" name="qty" value="<?=$QTY ?>">
					<input type="hidden" name="order_state" value="<?=$ORDER_STATE ?>">
				</td>
				<td class="price">
					<?=number_format($QTY * $SALE_PRICE) ?>
				</td>
			</tr>
		</table>
		<div class="sp10"></div>
		<div class="btn">
			<? if ($sPageRight_U == "Y") {?>
			<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="등록" /></a>
			<? } ?>
		</div>
		<div class="sp35"></div>
	</div>
</div>
<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
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