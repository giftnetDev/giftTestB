<?session_start();?>
<?
# =============================================================================
# File Name    : order_write.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @orion Corp. All Rights Reserved.
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
	

	if ($mode == "U") {
		
		$row_cnt = count($arr_delivery_seq);

		for ($k = 0; $k < $row_cnt; $k++) {

			$result = updateOrderGoodsDeliveryNumber($conn, $arr_delivery_seq[$k], $arr_delivery_cp[$k], $arr_delivery_no[$k], $s_adm_no);
		}

?>
<script type="text/javascript">
	window.opener.js_reload();
	alert("수정 되었습니다.");
	self.close();
</script>
<?
		exit;

	}

#====================================================================
# Request Parameter
#====================================================================

	$order_goods_no		= trim($order_goods_no);
	$delivery_seq		= trim($delivery_seq);

	$del_tf = "N";
	
#===============================================================
# Get Search list count
#===============================================================

	$arr_rs = selectOrderGoods($conn, $order_goods_no);

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


function js_save() {
	
	var frm = document.frm;
	
	frm.mode.value = "U";
	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();

}


</script>
</head>

<body id="popup_file">

<div id="popupwrap_file">
	<h1>송장 수정</h1>
	<div id="postsch_file">
		<form name="frm" method="post">
		<input type="hidden" name="mode"value="">
		<input type="hidden" name="order_goods_no"value="<?=$order_goods_no?>">
		<h2>* 주문 상품</h2>
					<table cellpadding="0" cellspacing="0" class="rowstable01" style="width:98%" border="0">
						<colgroup>
						<col width="10%" />
						<col width="30%" />
						<col width="60%" />
					</colgroup>
					<tr>
						<th>상품코드</th>
						<th>상품명</th>
						<th class="end">택배정보</th>
					</tr>
				<?
					$nCnt = 0;
					$total_sum_price = 0;
					$sum_qty = 0;
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$ORDER_GOODS_NO			= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
							$ON_UID							= trim($arr_rs[$j]["ON_UID"]);
							$MEM_NO							= trim($arr_rs[$j]["MEM_NO"]);
							$BUY_CP_NO					= trim($arr_rs[$j]["BUY_CP_NO"]);
							$RESERVE_NO					= trim($arr_rs[$j]["RESERVE_NO"]);
							$GOODS_NO						= trim($arr_rs[$j]["GOODS_NO"]);
							$GOODS_CODE					= trim($arr_rs[$j]["GOODS_CODE"]);
							$GOODS_SUB_NAME			= trim($arr_rs[$j]["GOODS_SUB_NAME"]);
							
							$GOODS_NAME					= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
							$BUY_PRICE					= trim($arr_rs[$j]["BUY_PRICE"]);
							$SALE_PRICE					= trim($arr_rs[$j]["SALE_PRICE"]);
							$EXTRA_PRICE				= trim($arr_rs[$j]["EXTRA_PRICE"]);
							$DELIVERY_PRICE			= trim($arr_rs[$j]["DELIVERY_PRICE"]);

							$GOODS_OPTION_01		= trim($arr_rs[$j]["GOODS_OPTION_01"]);
							$GOODS_OPTION_02		= trim($arr_rs[$j]["GOODS_OPTION_02"]);
							$GOODS_OPTION_03		= trim($arr_rs[$j]["GOODS_OPTION_03"]);
							$GOODS_OPTION_04		= trim($arr_rs[$j]["GOODS_OPTION_04"]);
							$GOODS_OPTION_NM_01	= trim($arr_rs[$j]["GOODS_OPTION_NM_01"]);
							$GOODS_OPTION_NM_02	= trim($arr_rs[$j]["GOODS_OPTION_NM_02"]);
							$GOODS_OPTION_NM_03	= trim($arr_rs[$j]["GOODS_OPTION_NM_03"]);
							$GOODS_OPTION_NM_04	= trim($arr_rs[$j]["GOODS_OPTION_NM_04"]);
							$SA_DELIVERY_PRICE 	= trim($arr_rs[$j]["SA_DELIVERY_PRICE"]);

							$CATE_01						= trim($arr_rs[$j]["CATE_01"]);
							$CATE_02						= trim($arr_rs[$j]["CATE_02"]);
							$CATE_03						= trim($arr_rs[$j]["CATE_03"]);
							$CATE_04						= trim($arr_rs[$j]["CATE_04"]);

							$SUM_PRICE					= trim($arr_rs[$j]["SUM_PRICE"]);
							$PLUS_PRICE					= trim($arr_rs[$j]["PLUS_PRICE"]);
							$GOODS_LEE					= trim($arr_rs[$j]["LEE"]);
							$QTY								= trim($arr_rs[$j]["QTY"]);
							$REQ_DATE						= trim($arr_rs[$j]["PAY_DATE"]);
							$END_DATE						= trim($arr_rs[$j]["FINISH_DATE"]);
							$ORDER_STATE				= trim($arr_rs[$j]["ORDER_STATE"]);
							$DELIVERY_CP				= trim($arr_rs[$j]["DELIVERY_CP"]);
							$DELIVERY_NO				= trim($arr_rs[$j]["DELIVERY_NO"]);

							$option_str = "";

							if ($GOODS_OPTION_NM_01 <> "") {
								$option_str .= $GOODS_OPTION_NM_01." : ".$GOODS_OPTION_01."&nbsp;";
							}

							if ($GOODS_OPTION_NM_02 <> "") {
								$option_str .= $GOODS_OPTION_NM_02." : ".$GOODS_OPTION_02."&nbsp;";
							}

							if ($GOODS_OPTION_NM_03 <> "") {
								$option_str .= $GOODS_OPTION_NM_03." : ".$GOODS_OPTION_03."&nbsp;";
							}

							if ($GOODS_OPTION_NM_04 <> "") {
								$option_str .= $GOODS_OPTION_NM_04." : ".$GOODS_OPTION_04."&nbsp;";
							}
							

							$refund_able_qty = getRefundAbleQty($conn, $RESERVE_NO, $ORDER_GOODS_NO);
						?>

						<tr>
							<td><?= $GOODS_NO?></td>
							<td class="modeual_nm" height="35"><?=$GOODS_NAME?><br><?=$GOODS_SUB_NAME?>
								<input type="hidden" name="on_uid" value="<?=$ON_UID?>">
								<input type="hidden" name="reserve_no" value="<?=$RESERVE_NO?>">
							</td>
							<td class="filedown">
								<? 
									$arr_delivery = listOrderDelivery($conn, $RESERVE_NO);
									for ($k = 0 ; $k < sizeof($arr_delivery); $k++) { 
										$rs_delivery_seq = $arr_delivery[$k]["DELIVERY_SEQ"];
										$rs_delivery_cp  = $arr_delivery[$k]["DELIVERY_CP"];
										$rs_delivery_no  = $arr_delivery[$k]["DELIVERY_NO"];
									
								?>
									<input type="hidden" name="arr_delivery_seq[]" value="<?=$rs_delivery_seq?>"> <?=$rs_delivery_seq?> 
									<?=makeSelectBox($conn,"DELIVERY_CP", "arr_delivery_cp[]","90", "택배사 선택", "", $rs_delivery_cp)?>
									<input type="text" name="arr_delivery_no[]" value="<?=$rs_delivery_no ?>" style="height: 16px; border: 1px solid #c0bfbf;" onkeyup="return isPhoneNumber(this)"> <br/>
								<?

									}
								
								?>
								
							</td>
						</tr>

						<?
						}
					}else{
						?>
						<tr>
							<td height="50" align="center" colspan="4">데이터가 없습니다. </td>
						</tr>
					<?
						}
					?>
					</table>

					<div class="sp10"></div>
					<div class="btn">
					<? if ($sPageRight_U == "Y") {?>
						<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="등록" /></a>
					<? } ?>
					</div>
					<div class="sp35"></div>

<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</div>
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