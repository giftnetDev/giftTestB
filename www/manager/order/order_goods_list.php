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
	$menu_right = "OD002"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/com/util/ImgUtil.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/cart/cart.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/payment/payment.php";

#====================================================================
# Request Parameter
#====================================================================
	
	$use_tf = "Y";
	$del_tf = "N";
#============================================================
# Page process
#============================================================

#===============================================================
# Get Search list count
#===============================================================
	
	$s_ord_no = get_session('s_ord_no');

	if ($mode == "D") {
		$result = deleteCart($conn, $cart_no);
	}

	
	if ($mode == "M") {

		$row_cnt = count($qty);
		
		for ($k = 0; $k < $row_cnt; $k++) {
		
			$tmp_cart_no = $m_cart_no[$k];

			$tmp_qty = str_replace(",", "", $qty[$k]);
			$tmp_sale_price = str_replace(",", "", $sale_price[$k]);
			$tmp_discount_price = str_replace(",", "", $discount_price[$k]);


			$result = updateCart($conn, $tmp_sale_price, $tmp_qty, $tmp_discount_price, $tmp_cart_no);
		}
	}
	

	$arr_rs = listCart($conn, $s_ord_no, $cp_no, $use_tf, $del_tf, "ASC");

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript">

	function init() { 
		var doc = document.getElementById("infodoc"); 
		doc.style.top=0; 
		doc.style.left=0; 
		if(doc.offsetHeight == 0){ 
		} else { 
			pageheight = doc.offsetHeight; 
			pagewidth = "670"; 

			parent.document.getElementById('goods_list').height = pageheight;
			//parent.frames["ifr_detail"].resizeTo(pagewidth,pageheight); 
			parent.document.frm.total_qty.value = frm.total_qty.value;
			parent.document.frm.total_sale_price.value = frm.total_sum_price.value;
			parent.document.frm.disply_total_sale_price.value = numberFormat(frm.total_sum_price.value);

			parent.js_cal();

		}
	}

	function js_view(rn, reserve_no) {

		var frm = document.frm;
		
		frm.reserve_no.value = reserve_no;
		frm.mode.value = "S";
		frm.target = "";
		frm.method = "get";
		frm.action = "order_read.php";
		frm.submit();
		
	}

	function js_add_goods() {

		if (parent.frm.cp_type.value == "") {
			alert("판매 업체를 선택해 주세요.");
			return;
		}
		
		var url = "order_goods_add.php?cp_no="+parent.frm.cp_type.value;

		NewWindow(url,'popup_add_goods','820','600','YES');

	}

	function js_delete_goods(cart_no) {

		var frm = document.frm;
		
		frm.cart_no.value = cart_no;
		frm.mode.value = "D";
		frm.target = "";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

		
	}

	
	function js_modify_goods() {
		var frm = document.frm;
		var chk_cnt = 0;

		check = document.getElementsByName("qty[]");
		
		for (i=0;i<check.length;i++) {
			if(check.item(i).value <= 0) {
				alert("수량은 0보다 커야 합니다.");
				check.item(i).focus();
				return;
			}
		}

		check2 = document.getElementsByName("sale_price[]");
		
		for (i=0;i<check2.length;i++) {
			if(check2.item(i).value < 0) {
				alert("판매가는 0이거나 커야 합니다.");
				check2.item(i).focus();
				return;
			}
		}

		frm.mode.value = "M";
		frm.target = "";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}


	function js_calculate_buy_and_sale_price( )	{

		temp_qty			= document.getElementsByName("qty[]");
		temp_sale_price		= document.getElementsByName("sale_price[]");
		temp_discount_price = document.getElementsByName("discount_price[]");
		temp_sum_price		= document.getElementsByName("sum_price[]");
		temp_sum_price2		= document.getElementsByName("sum_price2[]");
		
		for (i=0;i<temp_qty.length;i++) {

			var qty = temp_qty.item(i).value.replaceall(",", "");
			var sale_price = temp_sale_price.item(i).value.replaceall(",", "");
			var discount_price = temp_discount_price.item(i).value.replaceall(",", "");
			var sum_price = temp_sum_price.item(i).innerHTML.replaceall(",", "");
			var sum_price2 = temp_sum_price2.item(i).getAttribute("data-value");

			temp_sum_price.item(i).innerHTML = numberFormat(qty * sale_price - discount_price) + " 원";

			if(sum_price != sum_price2)
				$(temp_sum_price2.item(i)).show();
			else
				$(temp_sum_price2.item(i)).hide();
		}
	}

</script>
</head>

<body onLoad="init();">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="rn" value="">
<input type="hidden" name="cart_no" value="">
<input type="hidden" name="goods_no" value="">
<input type="hidden" name="use_tf" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>">

<div id="infodoc" style="position:absolute;left:0;top:0;width:100%"> 
<table cellpadding="0" cellspacing="0" class="rowstable">
	<colgroup>
		<!--<col width="5%" />-->
		<col width="8%" />
		<col width="*" />
		<col width="7%"/>
		<col width="8%" />
		<col width="8%" />
		<col width="8%" />
		<col width="8%" />
		<col width="14%" />
	</colgroup>
	<thead>
		<tr>
			<!--<th>순번</th>-->
			<th>이미지</th>
			<th>상품명</th>
			<th>수량</th>
			<th>판매가</th>
			<th>추가배송비</th>
			<th>할인</th>
			<th>합계</th>
			<th class="end" rowspan="2">삭제</th>
		</tr>
		<tr>
			<th>출고예정일</th>
			<th>작업/발주메모</th>
			<th>스티커</th>
			<th>스티커메세지</th>
			<th>포장지</th>
			<th>아웃박스<br/>스티커</th>
			<th>인쇄메세지</th>
		</tr>
	</thead>
	<tbody>
	<?
		$nCnt = 0;
		$TOTAL_SUM_PRICE = 0;
		$TOTAL_QTY = 0;
		
		if (sizeof($arr_rs) > 0) {
			for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
				
				$rn								= trim($arr_rs[$j]["rn"]);
				$CART_NO						= trim($arr_rs[$j]["CART_NO"]);
				$ON_UID							= trim($arr_rs[$j]["ON_UID"]);
				$GOODS_CODE						= trim($arr_rs[$j]["GOODS_CODE"]);
				$GOODS_NAME						= trim($arr_rs[$j]["GOODS_NAME"]);
				$QTY							= trim($arr_rs[$j]["QTY"]);
				$BUY_PRICE						= trim($arr_rs[$j]["BUY_PRICE"]);
				$PRICE							= trim($arr_rs[$j]["PRICE"]);
				$SALE_PRICE						= trim($arr_rs[$j]["SALE_PRICE"]);
				$EXTRA_PRICE					= trim($arr_rs[$j]["EXTRA_PRICE"]);
				$DELIVERY_PRICE					= trim($arr_rs[$j]["DELIVERY_PRICE"]);
				$DISCOUNT_PRICE					= trim($arr_rs[$j]["DISCOUNT_PRICE"]);
				$SA_DELIVERY_PRICE				= trim($arr_rs[$j]["SA_DELIVERY_PRICE"]);

				$IMG_URL						= trim($arr_rs[$j]["IMG_URL"]);
				$FILE_NM						= trim($arr_rs[$j]["FILE_NM_100"]);
				$FILE_RNM						= trim($arr_rs[$j]["FILE_RNM_100"]);
				$FILE_PATH						= trim($arr_rs[$j]["FILE_PATH_100"]);
				$FILE_SIZE						= trim($arr_rs[$j]["FILE_SIZE_100"]);
				$FILE_EXT						= trim($arr_rs[$j]["FILE_EXT_100"]);
				$FILE_NM_150					= trim($arr_rs[$j]["FILE_NM_150"]);
				$FILE_RNM_150					= trim($arr_rs[$j]["FILE_RNM_150"]);
				$FILE_PATH_150					= trim($arr_rs[$j]["FILE_PATH_150"]);
				$FILE_SIZE_150					= trim($arr_rs[$j]["FILE_SIZE_150"]);
				$FILE_EXT_150					= trim($arr_rs[$j]["FILE_EXT_150"]);

				$CATE_01						= trim($arr_rs[$j]["C_CATE_01"]);

				$OPT_STICKER_NO					= trim($arr_rs[$j]["OPT_STICKER_NO"]);
				$OPT_OUTBOX_TF					= trim($arr_rs[$j]["OPT_OUTBOX_TF"]);
				$OPT_WRAP_NO					= trim($arr_rs[$j]["OPT_WRAP_NO"]);
				$OPT_STICKER_MSG				= trim($arr_rs[$j]["OPT_STICKER_MSG"]);
				$OPT_PRINT_MSG					= trim($arr_rs[$j]["OPT_PRINT_MSG"]);
				$OPT_OUTSTOCK_DATE				= trim($arr_rs[$j]["OPT_OUTSTOCK_DATE"]);
				$OPT_MEMO						= trim($arr_rs[$j]["OPT_MEMO"]);
				$OPT_REQUEST_MEMO				= trim($arr_rs[$j]["OPT_REQUEST_MEMO"]);

				if($OPT_OUTSTOCK_DATE != "0000-00-00 00:00:00")
					$OPT_OUTSTOCK_DATE			= date("Y-m-d", strtotime($OPT_OUTSTOCK_DATE));

				$OPT_OUTBOX_TF = ($OPT_OUTBOX_TF == "Y" ? "있음" : "" );

				$OPT_OUTSTOCK_DATE = ($OPT_OUTSTOCK_DATE != "0000-00-00 00:00:00" ? $OPT_OUTSTOCK_DATE : "출고미정");


				if($CATE_01 <> "")
					$str_cate_01 = $CATE_01.") ";
				else 
					$str_cate_01 = "";

				$SUM_PRICE = ($QTY * $SALE_PRICE) + $SA_DELIVERY_PRICE - $DISCOUNT_PRICE;

				$TOTAL_QTY = $TOTAL_QTY + $QTY;

				//if($CATE_01 == "") //2016-12-21 샘플, 증정 주문서 금액에 다시 추가
				$TOTAL_SUM_PRICE = $TOTAL_SUM_PRICE + $SUM_PRICE;

				$img_url	= getGoodsImage($FILE_NM, $IMG_URL, $FILE_PATH_150, $FILE_RNM_150, "50", "50");
				
				$REG_DATE						= trim($arr_rs[$j]["REG_DATE"]);
	?>
			<tr>
				<!--<td><input type="checkbox" name="chk_on_uid_no[]" value="<?=$ON_UID?>"></td>-->
				<td style="padding: 1px 1px 1px 1px"><img src="<?=$img_url?>" width="50" height="50"></td>
				<td class="modeual_nm"><?=$str_cate_01?> [<?=$GOODS_CODE?>] <?=$GOODS_NAME?></td>
				<td class="price">
					<input type="hidden" name="m_cart_no[]" value="<?=$CART_NO?>">
					<input type="text" class="txt" style="width:50px" name="qty[]" value="<?=$QTY?>" required onchange="javascript:js_calculate_buy_and_sale_price();" onkeyup="return isNumber(this)"/> 개
				</td>
				<td class="price">
					<? if ($s_adm_cp_type == "운영") {?>
					<input type="text" class="txt" style="width:50px" name="sale_price[]" value="<?=number_format($SALE_PRICE)?>" required onchange="javascript:js_calculate_buy_and_sale_price();" onkeyup="return isNumber(this)"/> 원
					<? } else { ?>
						<?=number_format($SALE_PRICE)?> 원
						<input type="hidden" name="sale_price[]" value="<?=number_format($SALE_PRICE)?>"/>
					<? } ?>
				</td>
				<td class="price">
					<?=number_format($SA_DELIVERY_PRICE)?>
				</td>
				<td class="price">
					<? if ($s_adm_cp_type == "운영") {?>
						<input type="text" class="txt" style="width:50px" name="discount_price[]" value="<?=number_format($DISCOUNT_PRICE)?>" onchange="javascript:js_calculate_buy_and_sale_price();" onkeyup="return isNumber(this)" /> 원
					<? } else { ?>
						0 원
						<input type="hidden" name="discount_price[]" value="0"/>
					<? } ?>
				</td>
				<td class="price">
					<span class="calc sum_price" name="sum_price[]"><?=number_format($SUM_PRICE)?> 원</span><br/>
					<font name="sum_price2[]" class="sum_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$SUM_PRICE?>">(<?=number_format($SUM_PRICE)?> 원)</font>
				</td>
				<td class="filedown" rowspan="2">
					<input type="button" name="b" value=" 수정 " class="btntxt" onclick="js_modify_goods();">
					<input type="button" name="b" value=" 삭제 " class="btntxt" onclick="js_delete_goods('<?=$CART_NO?>');" >
				</td>
			</tr>
			<tr height="30">
				<td class="modeual_nm"><?=$OPT_OUTSTOCK_DATE?></td>
				<td class="modeual_nm"><?=$OPT_MEMO?><b> / </b><?=$OPT_REQUEST_MEMO?></td>
				<td class="modeual_nm"><?=getGoodsName($conn, $OPT_STICKER_NO)?></td>
				<td class="modeual_nm"><?=$OPT_STICKER_MSG?></td>
				<td class="modeual_nm"><?=getGoodsName($conn, $OPT_WRAP_NO)?></td>
				<td class="modeual_nm"><?=$OPT_OUTBOX_TF?></td>
				<td class="modeual_nm"><?=$OPT_PRINT_MSG?></td>
			</tr>
	<?
			}
		}else{
			?>
			<tr>
				<td height="50" align="center" colspan="8" rowspan="2">데이터가 없습니다. </td>
			</tr>
		<?
			}
		?>
	<input type="hidden" name="total_sum_price" value="<?=number_format($TOTAL_SUM_PRICE)?>">
	<input type="hidden" name="total_qty" value="<?=$TOTAL_QTY?>">
	</tbody>
</table>
<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">
<b>총 수량 : <?=number_format($TOTAL_QTY)?>&nbsp;&nbsp;&nbsp;총합 : <?=number_format($TOTAL_SUM_PRICE)?> 원 </b>&nbsp;&nbsp;&nbsp;

<? if ($sPageRight_I == "Y") {?>
	<!--input type="button" name="aa" value="새로고침" class="btntxt" onclick="document.location.reload();" --> 
	<input type="button" name="aa" value="상품추가" class="btntxt" onclick="js_add_goods();">
<? } ?>

</div>
</div>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>