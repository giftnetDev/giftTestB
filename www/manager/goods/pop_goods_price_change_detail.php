<?session_start();?>
<?
# =============================================================================
# File Name    : 상품가격 상세
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "GD004"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/goods/goods.php";

#====================================================================
# Request Parameter
#====================================================================
	$kind		= trim($kind);
	$seq_no		= trim($seq_no);
#====================================================================
# DML Process
#====================================================================
	function updateDisplay($db, $seq_no, $display){
		if($display != "" && $seq_no != ""){
			$query 	  ="UPDATE TBL_GOODS_PRICE
						SET DISPLAY = '$display'
						WHERE SEQ_NO = '$seq_no'
			";
			//echo $query;
			if(!mysql_query($query,$db)) {
				return false;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}

	if($mode == "U"){
		if(updateDisplay($conn, $seq_no, $chk_display)){
			?>
			<script language="javascript">
			alert("수정 되었습니다.");
			document.location.href = "pop_goods_price_change_detail.php?seq_no="+<?=$seq_no?>;
			</script>
			<?
		} else {
			?>
			<script language="javascript">
			alert("실패 하였습니다.");
			document.location.href = "pop_goods_price_change_detail.php?seq_no="+<?=$seq_no?>;
			</script>
			<?
		}
	}

	$arr_rs = selectGoodsPriceChange($conn, $kind, $seq_no);

	$rs_goods_no			= trim($arr_rs[0]["GOODS_NO"]); 
	$rs_cp_no				= trim($arr_rs[0]["CP_NO"]); 
	$rs_price				= trim($arr_rs[0]["PRICE"]); 
	$rs_buy_price			= trim($arr_rs[0]["BUY_PRICE"]); 
	$rs_sale_price			= trim($arr_rs[0]["SALE_PRICE"]);
	$rs_delivery_cnt_in_box	= trim($arr_rs[0]["DELIVERY_CNT_IN_BOX"]);
	$rs_sticker_price		= trim($arr_rs[0]["STICKER_PRICE"]);
	$rs_print_price			= trim($arr_rs[0]["PRINT_PRICE"]);
	$rs_delivery_price		= trim($arr_rs[0]["DELIVERY_PRICE"]);
	$rs_labor_price			= trim($arr_rs[0]["LABOR_PRICE"]);
	$rs_other_price			= trim($arr_rs[0]["OTHER_PRICE"]);
	$rs_sale_susu			= trim($arr_rs[0]["SALE_SUSU"]);
	$rs_cp_sale_susu		= trim($arr_rs[0]["CP_SALE_SUSU"]);
	$rs_cp_sale_price		= trim($arr_rs[0]["CP_SALE_PRICE"]);
	$rs_display				= trim($arr_rs[0]["DISPLAY"]);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script>

	function js_calculate_buy_and_sale_price( )	{

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
		var i_labor_price = 0;
		var i_other_price = 0;
		var i_majin	= 0;
		var f_majin_per	= 0;

		if ($("input[name=sale_price]").val() != "") i_sale_price = parseInt($("input[name=sale_price]").val());
		if ($("input[name=buy_price]").val() != "") i_buy_price = parseInt($("input[name=buy_price]").val());
		if ($("input[name=sticker_price]").val() != "") i_sticker_price = parseInt($("input[name=sticker_price]").val());
		if ($("input[name=print_price]").val() != "") i_print_price = parseInt($("input[name=print_price]").val());
		if ($("input[name=delivery_cnt_in_box]").val() != "") i_delivery_cnt_in_box = parseInt($("input[name=delivery_cnt_in_box]").val());
		if ($("input[name=delivery_price]").val() != "") i_delivery_price = parseInt($("input[name=delivery_price]").val());
		if ($("input[name=sale_susu]").val() != "") f_sale_susu = parseFloat($("input[name=sale_susu]").val());
		if ($("input[name=labor_price]").val() != "") i_labor_price = parseInt($("input[name=labor_price]").val());
		if ($("input[name=other_price]").val() != "") i_other_price = parseInt($("input[name=other_price]").val());

		var has_susu = $("input[name=has_susu]").is(":checked");
		
		if(i_delivery_price == 0)
			i_delivery_per_price = 0;
		else
			i_delivery_per_price = Math.round(i_delivery_price / i_delivery_cnt_in_box);
		$("#delivery_per_price").html(numberFormat(i_delivery_per_price));

		i_susu_price = Math.round((i_sale_price / 100) * f_sale_susu);
		$("#susu_price").html(numberFormat(i_susu_price));

		i_total_wonga = i_buy_price + i_sticker_price + i_print_price + i_delivery_per_price + i_labor_price + i_other_price;
		$("#total_wonga").val(i_total_wonga);
		
		if(!has_susu) {
			f_sale_susu = 0;
			i_susu_price = 0;
		}

		i_majin = i_sale_price - i_susu_price - i_total_wonga;
		if(i_majin > 0)
			$("#majin").html(numberFormat(i_majin));
		else
			$("#majin").html(i_majin);
		
		if (i_sale_price != 0) {
			f_majin_per = Math.round10((i_majin / i_sale_price) * 100.0, -2);
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

		var i_vender_calc = 0;

		if(i_sale_price > 0 && i_majin > 0) { 

			if ($("input[name=vendor_calc]").val() != "") i_vender_calc = parseInt($("input[name=vendor_calc]").val());
			var vendor15 = Math.ceil10(((i_sale_price - i_total_wonga) * 15 / 100.0 + i_total_wonga) , 1);
			var vendor35 = Math.ceil10(((i_sale_price - i_total_wonga) * 35 / 100.0 + i_total_wonga) , 1);
			var vendor_calc = Math.ceil10(((i_sale_price - i_total_wonga) * i_vender_calc / 100.0 + i_total_wonga) , 1);

			$("#vendor15").html(numberFormat(vendor15));
			$("#vendor35").html(numberFormat(vendor35));
			$("#vendor_calc").html(numberFormat(vendor_calc));
		} else { 
			$("#vendor15").html("0");
			$("#vendor35").html("0");
			$("#vendor_calc").html("0");
		}

		var i_best_sale_calc = 0;
		if ($("input[name=best_sale_calc]").val() != "") i_best_sale_calc = parseInt($("input[name=best_sale_calc]").val());

		var best_sale_price = Math.ceil10(i_total_wonga / ((100 - f_sale_susu - i_best_sale_calc) / 100), 1);
		$("#best_sale_price").html(numberFormat(best_sale_price));


		$(".calc").each(function(index, value){
	
			var name = $(this).attr("name");
			if(name.indexOf("[]") <= -1) { 
				if(name != "sale_susu") { 
					if($(this).val() != parseInt($("font[class="+name+"]").attr("data-value")))
						$("font[class="+name+"]").show();
					else
						$("font[class="+name+"]").hide();
				} else {
					if($(this).val() != parseFloat($("font[class="+name+"]").attr("data-value")))
						$("font[class="+name+"]").show();
					else
						$("font[class="+name+"]").hide();
				}
			}

		});

	}

	$(function(){
		js_calculate_buy_and_sale_price();

		$(".calc").blur(function(){

			var withcomma = $(this).val();
			$(this).val(withcomma.replaceall(',',''));
		
			js_calculate_buy_and_sale_price();

		});
	});

	function js_update_display(){
		var frm = document.frm;
		frm.mode.value = "U";
		frm.seq_no.value = <?=$seq_no?>;
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}
</script>
</head>
<body id="popup_file">
<form name="frm">
<input type="hidden" name="mode" value="">
<input type="hidden" name="seq_no" value="">
<div id="popupwrap_file">
	<h1>상품 변경 가격 확인</h1>
	<div id="postsch_code">
		<h2>* 단가를 확인하세요. 변경은 안됩니다.</h2>
		<div class="addr_inp">
		<table cellpadding="0" cellspacing="0" width="95%">
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0" width="100%" class="colstable02">
						<colgroup>
							<col width="20%" />
							<col width="30%" />
							<col width="20%" />
							<col width="30%" />
						</colgroup>
						<tr>
							<th title="(세트)매입가 = 아웃박스 제외 구성자재 매입가 * 수량의 합 + (아웃박스 매입가 * 수량 / 박스입수)">매입가</th>
							<td class="line">
								<input type="text" class="txt calc buy_price" style="width:90px" name="buy_price" value="<?=$rs_buy_price?>" required onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()" /> 원 <font class="buy_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_buy_price?>">(<?=$rs_buy_price?> 원)</font>
							</td>
							<th>판매가</th>
							<td class="line">
								<input type="text" class="txt calc sale_price" style="width:90px" name="sale_price" value="<?=$rs_sale_price?>" required onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()" /> 원 <font class="sale_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_sale_price?>">(<?=$rs_sale_price?> 원)</font>
							</td>
						</tr>
						<tr>
							<th>스티커 비용</th>
							<td class="line">
								<input type="text" class="txt calc sticker_price" style="width:90px" name="sticker_price" value="<?=$rs_sticker_price?>" onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()"/> 원 <font class="sticker_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_sticker_price?>">(<?=$rs_sticker_price?> 원)</font>
							</td>
							<th>밴더할인 15%</th>
							<td class="line">
								<span id="vendor15"></span>원
							</td>
						</tr>
						<tr>
							<th>포장인쇄 비용</th>
							<td class="line">
								<input type="text" class="txt calc print_price" style="width:90px" name="print_price" value="<?=$rs_print_price?>" onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()"/> 원 <font class="print_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_print_price?>">(<?=$rs_print_price?> 원)</font>
							</td>
							<th>밴더할인 35%</th>
							<td class="line">
								<span id="vendor35"></span>원
							</td>
						</tr>
						<tr>
							<th>택배비용</th>
							<td class="line">
								<input type="text" class="txt calc delivery_price" style="width:90px" name="delivery_price" value="<?=$rs_delivery_price?>" onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()"/> 원 <font class="delivery_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_delivery_price?>">(<?=$rs_delivery_price?> 원)</font>
							</td>
							<th>밴더할인 <input type="text" name="vendor_calc" value="55" class="txt calc" onkeyup="js_calculate_buy_and_sale_price()" style="width:20px;"/> %</th>
							<td class="line">
								<span id="vendor_calc"></span>원
							</td>
						</tr>
						<tr>
							<th>박스입수</th>
							<td class="line">
								<input type="text" class="txt calc delivery_cnt_in_box" style="width:90px" name="delivery_cnt_in_box" required value="<?=$rs_delivery_cnt_in_box?>" onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()"/> 개 <font class="delivery_cnt_in_box" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_delivery_cnt_in_box?>">(<?=$rs_delivery_cnt_in_box?> 개)</font>
							</td>
							<th>판매 수수률</th>
							<td class="line">
								<input type="text" class="txt calc sale_susu" style="width:90px" name="sale_susu" value="<?=$rs_sale_susu?>" onkeyup="return isFloat(this)" onkeyup="js_calculate_buy_and_sale_price()"/> % <font class="sale_susu" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_sale_susu?>">(<?=$rs_sale_susu?> %)</font> &nbsp;&nbsp; <input type="checkbox" name="has_susu" onchange="js_calculate_buy_and_sale_price()" checked value="Y"/>
							</td>	
						</tr>
						<tr>
							<th title="물류비 = 택배비용 / 박스입수">
								물류비
							</th>
							<td class="line">
								<span id="delivery_per_price">0</span> 원
							</td>
							<th title="판매 수수료 = ((판매가 / 100) * 판매 수수률)">판매 수수료</th>
							<td class="line">
								<span id="susu_price">0</span> 원
							</td>
						</tr>
						<tr>
							<th>인건비</th>
							<td class="line">
								<input type="text" class="txt calc labor_price" style="width:90px" name="labor_price" value="<?=$rs_labor_price?>" onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()"/> 원 <font class="labor_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_labor_price?>">(<?=$rs_labor_price?> 원)</font>
							</td>
							<th title="마진 = 판매가 - 판매수수료 - 매입합계">마진</th>
							<td class="line">
								<span id="majin">0</span> 원
							</td>	
						</tr>
						<tr>
							<th>기타 비용</th>
							<td class="line">
								<input type="text" class="txt calc other_price" style="width:90px" name="other_price" value="<?=$rs_other_price?>" onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()"/> 원 <font class="other_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_other_price?>">(<?=$rs_other_price?> 원)</font>
							</td>
							<th title="마진률 = 마진 / 판매가 * 100">마진률</th>
							<td class="line">
								<span id="majin_per">0</span> %
							</td>
						</tr>
						<tr>
							<th title="매입합계 = 매입가(아웃박스 제외 자재매입가의 합 + (아웃박스 매입가 / 박스입수)) + 스티커비용 + 포장인쇄비용 + 물류비 + 인건비 + 기타비용">매입합계</th>
							<td class="line">
								<input type="text" id="total_wonga" class="txt calc price" style="width:90px" name="price" value="<?=$rs_price?>" onkeyup="return isNumber(this)" readonly /> 원 <font class="price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_price?>">(<?=$rs_price?> 원)</font>
							</td>
							<th title="마진률 기반으로 판매가 역 산출">최적판매가 <input type="text" name="best_sale_calc" value="20" class="txt calc" onkeyup="js_calculate_buy_and_sale_price()" style="width:20px;"/> %</th>
							<td class="line">
								<span id="best_sale_price">N/A</span> 원
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		
		<? if($rs_cp_no <> '') { ?>
		<div class="sp15"></div>
		<table cellpadding="0" cellspacing="0" width="95%">
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0" width="100%" class="colstable02">
						<colgroup>
							<col width="20%" />
							<col width="30%" />
							<col width="20%" />
							<col width="30%" />
						</colgroup>
						<tr>
							<th>판매업체 수수료</th>
							<td class="line">
								<?=$rs_cp_sale_susu?> %
							</td>
							<th>판매업체 판매가</th>
							<td class="line">
								<?=$rs_cp_sale_price?> 원
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<? } ?>
	</div>
	<div class="sp15"></div>
	<table cellpadding="0" cellspacing="0" width="95%">
		<tr>
			<td>※. 전시여부를 제외한 다른 정보는 변경되지 않습니다.
				<table cellpadding="0" cellspacing="0" width="100%" class="colstable02">
					<colgroup>
						<col width="20%" />
						<col width="30%" />
						<col width="20%" />
						<col width="30%" />
					</colgroup>
					<tr>
						<th>전시여부</th>
						<td class="line" colspan="3">
							<label><input type="radio" name="chk_display" value="Y" <? if($rs_display == "Y") echo "checked='checked'"?>/> 전시</label>&nbsp;&nbsp;&nbsp;&nbsp;
							<label><input type="radio" name="chk_display" value="N" <? if($rs_display == "N") echo "checked='checked'"?>/> 비전시</label>&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="button" name="btn_update_display" value="전시 여부 수정" onclick="javascript:js_update_display();" />
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<div class="sp15"></div>
</div>
	<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
</div>
<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>
<?
#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);
?>