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
	require "../../_classes/biz/confirm/confirm.php";
	

	if ($mode == "U") {

		$result = updateOrderGoodsSalePriceOrderReadAdvanced($conn, $old_sale_price, $old_qty, $old_discount_price, $sale_price, $qty, $discount_price, $order_state, $order_goods_no, $reserve_no);

		updateCompanyLedgerByOrderSub($conn, "매출상품", $order_goods_no, $sale_price, $s_adm_no);
		updateCompanyLedgerByOrderSub($conn, "매출할인", $order_goods_no, -1 * $discount_price, $s_adm_no);

		//취소나 교환 클레임 기장 금액도 변경
		updateCompanyLedgerByOrderSubClaim($conn, $order_goods_no, $sale_price, $s_adm_no);

		$result = resetOrderInfor($conn, $reserve_no);

		if ($result) {
?>
<script type="text/javascript">
	window.opener.js_reload();
	alert("수정 되었습니다.");
	self.close();
</script>
<?
			mysql_close($conn);
			exit;
		}

	}

#====================================================================
# Request Parameter
#====================================================================

	$order_goods_no		= trim($order_goods_no);

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
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script language="javascript">


function js_save() {
	
	var frm = document.frm;
	
	frm.mode.value = "U";
	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();

}

	// 원가 계산
	function js_calculate_all_price() {
		
		var i_sale_price		= 0;
		var i_buy_price			= 0;
		var i_sticker_price = 0;
		var i_print_price		= 0;
		var i_delivery_cnt_in_box = 1;
		var i_delivery_price = 0;
		var f_sale_susu = 0;
		var i_delivery_per_price = 0;
		var i_total_wonga = 0;
		var i_susu_price = 0;
		var i_majin	= 0;
		var f_majin_per	= 0;
		var i_discount_price	= 0;
		var i_sale_total	= 0;
		var i_qty = 1;

		if ($("input[name=sale_price]").val() != "") i_sale_price = parseInt($("input[name=sale_price]").val());
		if ($("input[name=buy_price]").val() != "") i_buy_price = parseInt($("input[name=buy_price]").val());
		if ($("input[name=sticker_price]").val() != "") i_sticker_price = parseInt($("input[name=sticker_price]").val());
		if ($("input[name=print_price]").val() != "") i_print_price = parseInt($("input[name=print_price]").val());
		if ($("input[name=delivery_cnt_in_box]").val() != "") i_delivery_cnt_in_box = parseInt($("input[name=delivery_cnt_in_box]").val());
		if ($("input[name=delivery_price]").val() != "") i_delivery_price = parseInt($("input[name=delivery_price]").val());
		if ($("input[name=sale_susu]").val() != "") f_sale_susu = parseFloat($("input[name=sale_susu]").val());
		if ($("input[name=qty]").val() != "") i_qty = parseInt($("input[name=qty]").val());
		if ($("input[name=discount_price]").val() != "") i_discount_price = parseInt($("input[name=discount_price]").val());
		if ($("input[name=sale_total]").val() != "") i_sale_total = parseInt($("input[name=sale_total]").val());
		
		i_delivery_per_price = Math.round(i_delivery_price / i_delivery_cnt_in_box);
		$("#delivery_per_price").html(numberFormat(i_delivery_per_price));

		i_susu_price = Math.round((i_sale_price / 100) * f_sale_susu);
		$("#susu_price").html(numberFormat(i_susu_price));

		i_total_wonga = i_buy_price + i_sticker_price + i_print_price + i_delivery_per_price;
		$("#total_wonga").val(i_total_wonga);
		
		i_majin = i_sale_price - i_susu_price - i_total_wonga;
		$("#majin").html(numberFormat(i_majin));
		
		if (i_sale_price != 0) {
			f_majin_per = Math.round10((i_majin / i_sale_price) * 100, -2);
			$("#majin_per").html(f_majin_per);
		} else {
			if (i_majin == 0) {
				f_majin_per = 0
				$("#majin_per").html(f_majin_per);
			} else {
				f_majin_per = -100
				$("#majin_per").html(f_majin_per);
			}
		}

		i_sale_total = i_sale_price * i_qty - i_discount_price;
		$("#sale_total").val(i_sale_total);

		$(".sub_goods_cnt").each(function( index, value ){
			var each_value = parseInt($(value).attr("data-goods_cnt"))
			$(value).html(i_qty * each_value);

		});
	}


</script>
</head>

<body id="popup_file">

<div id="popupwrap_file">
	<h1>주문원가 금액 수정</h1>
	<div id="postsch_file">
		<form name="frm" method="post">
		<input type="hidden" name="mode"value="">
		<input type="hidden" name="order_goods_no"value="<?=$order_goods_no?>">
		<h2>* 주문 상품</h2>
			<table cellpadding="0" cellspacing="0" class="colstable02" style="width:98%" border="0">
			<colgroup>
				<col width="15%" />
				<col width="35%" />
				<col width="15%" />
				<col width="35%" />
			</colgroup>

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
					$PRICE						= trim($arr_rs[$j]["PRICE"]); 
					$BUY_PRICE					= trim($arr_rs[$j]["BUY_PRICE"]);
					$SALE_PRICE					= trim($arr_rs[$j]["SALE_PRICE"]);
					$EXTRA_PRICE				= trim($arr_rs[$j]["EXTRA_PRICE"]);
					$DELIVERY_PRICE			= trim($arr_rs[$j]["DELIVERY_PRICE"]);
					$DISCOUNT_PRICE			= trim($arr_rs[$j]["DISCOUNT_PRICE"]);

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
					$DELIVERY_TYPE				= trim($arr_rs[$j]["DELIVERY_TYPE"]);

					$DELIVERY_CNT_IN_BOX		= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]); 
					$STICKER_PRICE				= trim($arr_rs[$j]["STICKER_PRICE"]); 
					$PRINT_PRICE				= trim($arr_rs[$j]["PRINT_PRICE"]); 
					$SALE_SUSU					= trim($arr_rs[$j]["SALE_SUSU"]); 
					$OTHER_PRICE				= trim($arr_rs[$j]["OTHER_PRICE"]); 
					$LABOR_PRICE				= trim($arr_rs[$j]["LABOR_PRICE"]);

					$refund_able_qty = getRefundAbleQty($conn, $RESERVE_NO, $ORDER_GOODS_NO);
				?>
			<tr>
				<th>주문서번호</th>
				<td class="line"><?=$RESERVE_NO?></td>
				<th>주문상품번호</th>
				<td class="line"><?=$ORDER_GOODS_NO?></td>
			</tr>
			<tr>
				<th>상품명</th>
				<td class="modeual_nm line" colspan="3">[<?= $GOODS_CODE?>] <?=$GOODS_NAME?> <?=$GOODS_SUB_NAME?></td>
			</tr>
			<tr>
				<th>매입가</th>
				<td class="line">
					<input type="hidden" name="buy_price" value="<?=$BUY_PRICE?>"/>
					<?=number_format($BUY_PRICE)?> 원
				</td>
				<th>판매가</th>
				<td class="line">
					<?=number_format($SALE_PRICE)?> 원
				</td>
				
			</tr>
			<tr>
				<th>스티커비용</th>
				<td class="line" colspan="3">
					<input type="hidden" name="sticker_price" value="<?=$STICKER_PRICE?>"/>
					<?=number_format($STICKER_PRICE)?> 원

				</td>
				
			</tr>
			<tr>
				
				<th>포장인쇄비용</th>
				<td class="line" colspan="3">
					<input type="hidden" name="print_price" value="<?=$PRINT_PRICE?>"/>
					<?=number_format($PRINT_PRICE)?> 원

				</td>
			</tr>
			<tr>
				<th title="왕복택배비 / 박스입수">물류비</th>
				<td class="line">
					<input type="hidden" name="delivery_price" value="<?=$DELIVERY_PRICE?>"/>
					<span id="delivery_per_price">0</span> 원 (택배비용 : <?=number_format($DELIVERY_PRICE)?> 원)

				</td>
				<th>판매 수수률</th>
				<td class="line">
					<input type="hidden" name="sale_susu" value="<?=$SALE_SUSU?>"/>
					<?= $SALE_SUSU ?> % 

				</td>
				
			</tr>
			<tr>
				<th>인건비</th>
				<td class="line">
					<?=number_format($LABOR_PRICE)?> 원
				</td>
				<th title="(판매가 / 100) * 판매 수수률">판매 수수료</th>
				<td class="line">
					  <span id="susu_price">0</span> 원
				</td>
				
			</tr>
			<tr>
				<th>기타비용</th>
				<td class="line">
					<?=number_format($PRINT_PRICE)?> 원
				</td>
				<th title="판매가 - 판매수수료 - 매입합계">마진</th>
				<td class="line">
					<span id="majin">0</span> 원
				</td>
				
				
			</tr> 
			<tr>
				<th title="매입가 + 스티커비용 + 포장인쇄비용 + 물류비">매입합계</th>
				<td class="line">
					<input type="hidden" name="price" value="<?=$PRICE?>"/>
					<?=number_format($PRICE)?> 원
				</td>
				<th title="마진 / 판매가 * 100">마진률</th>
				<td class="line">
					<span id="majin_per">0</span> %
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
			<? if (!(($ORDER_STATE == "4") || ($ORDER_STATE == "6") || ($ORDER_STATE == "7") || ($ORDER_STATE == "8"))) { ?>
			<? if ($sPageRight_U == "Y") {?>
				<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="등록" /></a>
			<? } ?>
			</div>
			<? } ?>
			<div class="sp35"></div>

<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</div>
<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
</div>

</body>
</html>
<script>
	js_calculate_all_price();
</script>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>