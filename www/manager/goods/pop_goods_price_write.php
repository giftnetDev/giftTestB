<?session_start();?>
<?
# =============================================================================
# File Name    : ��ǰ���� ��
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "GD004"; // �޴����� ���� �� �־�� �մϴ�

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

	if ($mode == "I") {
		if ($cp_type <> '') {
			//��ü�� �ǸŰ� ����
			$result = insertGoodsPrice($conn, $goods_no, $cp_type, $price, $buy_price, $sale_price, $sticker_price, $print_price, $delivery_price, $delivery_cnt_in_box, $labor_price, $other_price, $sale_susu, $cp_sale_susu, $cp_sale_price, $s_adm_no, $chk_display);

			//��ǰ������ �Ʒ� ��ǰ���� ������ - ���� ����� �����Ƿ� �׶��׶��� ���� �����丮�� �����
			insertGoodsPriceUpdate($conn, "TBL_GOODS_PRICE", $goods_no, $cp_type, $price, $buy_price, $sale_price, $sticker_price, $print_price, $delivery_price, $delivery_cnt_in_box, $labor_price, $other_price, $sale_susu, $cp_sale_susu, $cp_sale_price, $s_adm_no, $chk_display);

			if($result) {
			?>
			<script type="text/javascript">
				alert('����Ǿ����ϴ�.');
				self.close();
			</script>
			<?
			}
		}
	}

	if($mode == "DELETE_PRICE_CHANGE") {
		$result = deleteGoodsPriceChangeAsSeqNo($conn, $seq_no, $s_adm_no);

		if($result) { 
		?>
		<script type="text/javascript">
			alert('����Ǿ����ϴ�.');
		</script>
		<?
		}
	}

#====================================================================
# Request Parameter
#====================================================================
	$goods_no		= trim($goods_no);
#====================================================================
# DML Process
#====================================================================
	$arr_rs = selectGoods($conn, $goods_no);

	$rs_price				= trim($arr_rs[0]["PRICE"]); 
	$rs_buy_price			= trim($arr_rs[0]["BUY_PRICE"]); 
	$rs_sale_price			= trim($arr_rs[0]["SALE_PRICE"]); 
	$rs_extra_price			= trim($arr_rs[0]["EXTRA_PRICE"]); 
	$rs_delivery_cnt_in_box	= trim($arr_rs[0]["DELIVERY_CNT_IN_BOX"]); 
	$rs_sticker_price		= trim($arr_rs[0]["STICKER_PRICE"]); 
	$rs_print_price			= trim($arr_rs[0]["PRINT_PRICE"]); 
	$rs_delivery_price		= trim($arr_rs[0]["DELIVERY_PRICE"]); 
	$rs_sale_susu			= trim($arr_rs[0]["SALE_SUSU"]); 
	$rs_labor_price			= trim($arr_rs[0]["LABOR_PRICE"]); 
	$rs_other_price			= trim($arr_rs[0]["OTHER_PRICE"]); 

	if($cp_type <> "" && $goods_no <> "") {
		$arr_rs_price = listGoodsPriceUpdate($conn, $goods_no, $cp_type);
	}
	//�ʱⰪ���� ���ÿ� üũ���ֱ� ����
	else {
		$chk_display ="Y";
	}

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
<script type="text/javascript">

	function js_save() {

		$(function(){
			$(".txt.calc").prop("readonly","");
		});

		var frm = document.frm;
		var goods_no = "<?= $goods_no ?>";

		if (isNull(frm.cp_type.value)) {
			alert("���� ���� ��ü�� ���õ��� �ʾҽ��ϴ�.");
			return;
		}
		
		if (isNull(frm.sale_price.value)) {
			alert('�ǸŰ��� �Է����ּ���.');
			frm.sale_price.focus();
			return ;		
		}

		frm.mode.value = "I";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	function js_delete_price_history(seq_no) { 
		
		var frm = document.frm;

		frm.seq_no.value = seq_no;
		frm.mode.value = "DELETE_PRICE_CHANGE";

		if(!confirm('��ǰ �ݾ׺��� ������ �����˴ϴ�. ���� �ʿ���� ������ �³���?')) { 
			return;	
		}
		
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

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
</script>
<style>
input[readonly]
{
  /* Your CSS Styles */
  background-color:#F0F0F0 !important; 
  color:#303030 !important;
}
	.row_deleted {background-color:#dfdfdf; }
	.row_deleted > td{color:#fff !important;}
	.row_deleted > td > a{color:#fff !important;}
</style>
</head>
<body id="popup_file">
<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="">
<input type="hidden" name="send_data" value="">
<input type="hidden" name="goods_no" value="<?=$goods_no?>">
<input type="hidden" name="seq_no" value="">

<div id="popupwrap_file">
	<h1>��ü�� �ܰ� ���</h1>
	<div id="postsch_code">
		<h2>* ��ü�� �ܰ��� ������ּ���. ����Ʈ�� ��ǰ���ݰ������� Ȯ�� �����մϴ�.</h2>
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
							<th>�Ǹž�ü</th>
							<td class="line" colspan="2">
								
								<input type="text" autocomplete="off" style="width:90%" placeholder="��ü(��/�ڵ�) �Է��� ���͸� �����ּ���" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'',$cp_type)?>" />
								<input type="hidden" name="cp_type" value="<?=$cp_type?>">
								<script>
									$(function(){

										$("input[name=txt_cp_type]").keydown(function(e){

											if(e.keyCode==13) { 

												var keyword = $(this).val();
												if(keyword == "") { 
													$("input[name=cp_type]").val('');
												} else { 
													$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('�Ǹ�,�ǸŰ���') +"&term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
														if(data.length == 1) { 
															
															js_selecting_company("txt_cp_type", data[0].label, "cp_type", data[0].id);

														} else if(data.length > 1){ 
															NewWindow("../company/pop_company_searched_list.php?con_cp_type=�Ǹ�,�ǸŰ���&search_str="+keyword + "&target_name=txt_cp_type&target_value=cp_type",'pop_company_searched_list','950','650','YES');

														} else 
															alert("�˻������ �����ϴ�.");
													});
												}
											}

										});

										$("input[name=txt_cp_type]").keyup(function(e){
											var keyword = $(this).val();

											if(keyword == "") { 
												$("input[name=cp_type]").val('');
											}
										});

									});

									function js_selecting_company(target_name, cp_nm, target_value, cp_no) {
										
										$(function(){

											$("input[name="+target_name+"]").val(cp_nm);
											$("input[name="+target_value+"]").val(cp_no);

										});

									}

								</script>
							</td>
							<td class="line" style="text-align:right;">
								<input type="button" class="reopen" value=" ��ǰ�ܰ����� "/>
								<script type="text/javascript">
									$(function(){
										$(".reopen").on("click",function(){
											$(".txt.calc").prop("readonly","");
										});
									});
								</script>
							
							</td>
						</tr>
						<tr>
							<th title="(��Ʈ)���԰� = �ƿ��ڽ� ���� �������� ���԰� * ������ �� + (�ƿ��ڽ� ���԰� * ���� / �ڽ��Լ�)">���԰�</th>
							<td class="line">
								<input type="text" class="txt calc buy_price" style="width:90px" name="buy_price" value="<?=$rs_buy_price?>" required onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()" readonly /> �� <font class="buy_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_buy_price?>">(<?=$rs_buy_price?> ��)</font>
							</td>
							<th>�Ǹž�ü ��ǰ��(�ǸŰ�)</th>
							<td class="line">
								<input type="text" class="txt calc sale_price" style="width:90px" name="sale_price" value="<?=$rs_sale_price?>" required onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()" /> �� <font class="sale_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_sale_price?>">(<?=$rs_sale_price?> ��)</font>
							</td>
						</tr>
						
						<tr>
							<th>��ƼĿ ���</th>
							<td class="line">
								<input type="text" class="txt calc sticker_price" style="width:90px" name="sticker_price" value="<?=$rs_sticker_price?>" onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()" readonly/> �� <font class="sticker_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_sticker_price?>">(<?=$rs_sticker_price?> ��)</font>
							</td>
							<th>������� 15%</th>
							<td class="line">
								<span id="vendor15"></span>��
							</td>
						</tr>
						<tr>
							<th>�����μ� ���</th>
							<td class="line">
								<input type="text" class="txt calc print_price" style="width:90px" name="print_price" value="<?=$rs_print_price?>" onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()" readonly/> �� <font class="print_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_print_price?>">(<?=$rs_print_price?> ��)</font>
							</td>
							<th>������� 35%</th>
							<td class="line">
								<span id="vendor35"></span>��
							</td>
						</tr>
						<tr>
							<th>�ù���</th>
							<td class="line">
								<input type="text" class="txt calc delivery_price" style="width:90px" name="delivery_price" value="<?=$rs_delivery_price?>" onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()" readonly/> �� <font class="delivery_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_delivery_price?>">(<?=$rs_delivery_price?> ��)</font>
							</td>
							<th>������� <input type="text" name="vendor_calc" value="55" class="txt calc" onkeyup="js_calculate_buy_and_sale_price()" style="width:20px;"/> %</th>
							<td class="line">
								<span id="vendor_calc"></span>��
							</td>
						</tr>
						<tr>
							<th>�ڽ��Լ�</th>
							<td class="line">
								<input type="text" class="txt calc delivery_cnt_in_box" style="width:90px" name="delivery_cnt_in_box" required value="<?=$rs_delivery_cnt_in_box?>" onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()" readonly/> �� <font class="delivery_cnt_in_box" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_delivery_cnt_in_box?>">(<?=$rs_delivery_cnt_in_box?> ��)</font>
							</td>
							<th>�Ǹ� ������</th>
							<td class="line">
								<input type="text" class="txt calc sale_susu" style="width:90px" name="sale_susu" value="<?=$rs_sale_susu?>" onkeyup="return isFloat(this)" onkeyup="js_calculate_buy_and_sale_price()" readonly/> % <font class="sale_susu" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_sale_susu?>">(<?=$rs_sale_susu?> %)</font> &nbsp;&nbsp; <input type="checkbox" name="has_susu" onchange="js_calculate_buy_and_sale_price()" checked value="Y"/>
							</td>	
						</tr>
						<tr>
							<th title="������ = �ù��� / �ڽ��Լ�">
								������
							</th>
							<td class="line">
								<span id="delivery_per_price">0</span> ��
							</td>
							<th title="�Ǹ� ������ = ((�ǸŰ� / 100) * �Ǹ� ������)">�Ǹ� ������</th>
							<td class="line">
								<span id="susu_price">0</span> ��
							</td>	
		
						</tr>
						<tr>
							<th>�ΰǺ�</th>
							<td class="line">
								<input type="text" class="txt calc labor_price" style="width:90px" name="labor_price" value="<?=$rs_labor_price?>" onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()" readonly/> �� <font class="labor_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_labor_price?>">(<?=$rs_labor_price?> ��)</font>
							</td>
							<th title="���� = �ǸŰ� - �Ǹż����� - �����հ�">����</th>
							<td class="line">
								<span id="majin">0</span> ��
								
							</td>	
						</tr>
						<tr>
							<th>��Ÿ ���</th>
							<td class="line">
								<input type="text" class="txt calc other_price" style="width:90px" name="other_price" value="<?=$rs_other_price?>" onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()" readonly/> �� <font class="other_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_other_price?>">(<?=$rs_other_price?> ��)</font>
							</td>
							<th title="������ = ���� / �ǸŰ� * 100">������</th>
							<td class="line">
								<span id="majin_per">0</span> %
							</td>
						</tr>
						<tr>
							<th title="�����հ� = ���԰�(�ƿ��ڽ� ���� ������԰��� �� + (�ƿ��ڽ� ���԰� / �ڽ��Լ�)) + ��ƼĿ��� + �����μ��� + ������ + �ΰǺ� + ��Ÿ���">�����հ�</th>
							<td class="line">
								<input type="text" id="total_wonga" class="txt calc price" style="width:90px" name="price" value="<?=$rs_price?>" onkeyup="return isNumber(this)" readonly /> �� <font class="price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_price?>">(<?=$rs_price?> ��)</font>
								
							</td>
							<th title="������ ������� �ǸŰ� �� ����">�����ǸŰ� <input type="text" name="best_sale_calc" value="20" class="txt calc" onkeyup="js_calculate_buy_and_sale_price()" style="width:20px;"/> %</th>
							<td class="line">
								<span id="best_sale_price">N/A</span> ��
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
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
							<th>�Ǹž�ü ������</th>
							<td class="line">
								<input type="text" name="cp_sale_susu" class="txt" value="" style="width:90px"/>%
							</td>
							<th>�Ǹž�ü �ǸŰ�</th>
							<td class="line">
								<input type="text" name="cp_sale_price" class="txt" value="" style="width:90px"/>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
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
						<th>���ÿ���</th>
						<td class="line" colspan="3">
							<label><input type="radio" name="chk_display" value="Y" <? if($chk_display == "Y") echo "checked='checked'"?>/> ����</label>&nbsp;&nbsp;&nbsp;&nbsp;
							<label><input type="radio" name="chk_display" value="N" <? if($chk_display == "N") echo "checked='checked'"?>/> ������</label>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<div class="sp15"></div>
	<div class="btn">

		<? if ($adm_no <> "" ) {?>
			<? if ($sPageRight_U == "Y") {?>
			<a href="javascript:js_save();"><img src="../images/admin/btn_regist_02.gif" alt="���" /></a> 
			<? } ?>
		<? } else {?>
			<? if ($sPageRight_I == "Y") {?>
			<a href="javascript:js_save();"><img src="../images/admin/btn_regist_02.gif" alt="���" /></a> 
			<? } ?>
		<? }?>
	
	</div>
	<div class="sp15"></div>
	<? if($cp_type <> "") { ?>
	<div style="width:95%;">
		<div style="float:left;">
			* ���� ���� ���� ����
		</div>
	</div>
	<table cellpadding="0" cellspacing="0" class="rowstable">
		<colgroup>
			<col width="16%" />
			<col width="7%" />
			<col width="7%" />
			<col width="5%" />
			<col width="5%" />
			<col width="7%" />
			<col width="3%" />
			<col width="7%" />
			<col width="7%" />
			<col width="9%" />
			<col width="7%" />
			<col width="7%" />
			<col width="7%" />
			<col width="*" />
			
		</colgroup>
		<thead>
			<tr>
				<th>������</th>
				<th>��ǰ��</th>
				<th>���԰�</th>
				<th>��ƼĿ<br/>���</th>
				<th>�����μ�<br/>���</th>
				<th>�ù���</th>
				<th>�ڽ��Լ�</th>
				<th>�ΰǺ��</th>
				<th>��Ÿ���</th>
				<th>�Ǹż�����</th>
				<th>�����հ�</th>
				<th>�Ǹž�ü������</th>
				<th>�Ǹž�ü�ǸŰ�</th>
				<th class="end">�׼�</th>
			</tr>
		</thead>
		<tbody>

	<?
		$nCnt = 0;
		 
		if (sizeof($arr_rs_price) > 0) {
			
			$temp_start_date = "";
			$temp_display_date = "";
			for ($j = 0 ; $j < sizeof($arr_rs_price); $j++) {
				
				$PRE_SEQ_NO 		= trim($arr_rs_price[$j]["SEQ_NO"]);
				$PRE_BUY_PRICE 		= trim($arr_rs_price[$j]["BUY_PRICE"]);
				$PRE_SALE_PRICE 	= trim($arr_rs_price[$j]["SALE_PRICE"]);
				$PRE_PRICE			= trim($arr_rs_price[$j]["PRICE"]);
				
				$PRE_STICKER_PRICE	= trim($arr_rs_price[$j]["STICKER_PRICE"]);
				$PRE_PRINT_PRICE	= trim($arr_rs_price[$j]["PRINT_PRICE"]);
				$PRE_DELIVERY_PRICE	= trim($arr_rs_price[$j]["DELIVERY_PRICE"]);
				$PRE_DELIVERY_CNT_IN_BOX	= trim($arr_rs_price[$j]["DELIVERY_CNT_IN_BOX"]);
				$PRE_LABOR_PRICE	= trim($arr_rs_price[$j]["LABOR_PRICE"]);
				$PRE_OTHER_PRICE	= trim($arr_rs_price[$j]["OTHER_PRICE"]);
				$PRE_SALE_SUSU		= trim($arr_rs_price[$j]["SALE_SUSU"]);
				$PRE_CP_SALE_SUSU	= trim($arr_rs_price[$j]["CP_SALE_SUSU"]);
				$PRE_CP_SALE_PRICE	= trim($arr_rs_price[$j]["CP_SALE_PRICE"]);

				$REG_DATE			= trim($arr_rs_price[$j]["REG_DATE"]);

				$PRE_DEL_TF				= trim($arr_rs_price[$j]["DEL_TF"]);
				$PRE_DEL_ADM			= trim($arr_rs_price[$j]["DEL_ADM"]);
				$PRE_DEL_DATE			= trim($arr_rs_price[$j]["DEL_DATE"]);

				$short_reg_date = date("ymd",strtotime($REG_DATE));

				if($j != sizeof($arr_rs_price) - 1) { 
					$temp_display_date = date("ymd",strtotime($arr_rs_price[$j+1]["REG_DATE"]))." ~ ".date("ymd",strtotime($arr_rs_price[$j]["REG_DATE"]));
					$PRE_ADM_NAME		= getAdminName($conn, $arr_rs_price[$j+1]["REG_ADM"]);
				
				} else { 
					$temp_display_date = "���ʵ�� ~ ".date("ymd",strtotime($arr_rs_price[$j]["REG_DATE"]));
					$PRE_ADM_NAME		= getAdminName($conn, $rs_reg_adm);
				}

				if($PRE_DEL_TF == "Y")
					$str_price_class = "row_deleted";
				else
					$str_price_class = "";
	?>
			<tr class="<?=$str_price_class?>">
				<td style="text-align:left;"><?=$temp_display_date?> (<?=$PRE_ADM_NAME?>)</td>
				<td class="price"><?= number_format($PRE_SALE_PRICE) ?> ��</td>
				<td class="price"><?= number_format($PRE_BUY_PRICE) ?> ��</td>
				<td class="price"><?= number_format($PRE_STICKER_PRICE) ?> ��</td>

				<td class="price"><?= number_format($PRE_PRINT_PRICE) ?> ��</td>
				<td class="price"><?= number_format($PRE_DELIVERY_PRICE) ?> ��</td>
				<td class="price"><?= number_format($PRE_DELIVERY_CNT_IN_BOX) ?></td>
				<td class="price"><?= number_format($PRE_LABOR_PRICE) ?> ��</td>
				<td class="price"><?= number_format($PRE_OTHER_PRICE) ?> ��</td>
				<td class="price"><?= $PRE_SALE_SUSU ?> %</td>
				<td class="price"><?= number_format($PRE_PRICE) ?> ��</td>
				<td class="price"><?= $PRE_CP_SALE_SUSU ?> %</td>
				<td class="price"><?= number_format($PRE_CP_SALE_PRICE) ?> ��</td>
				<td class="filedown">
					<? if($PRE_DEL_TF != 'Y') { ?>
					<a href="javascript:js_delete_price_history('<?=$arr_rs_price[$j]["SEQ_NO"]?>')">����</a>
					<? } ?>
				</td>
			</tr>
	<?			
				
			}
		} else { 
	?> 
			<tr>
				<td align="center" height="50" colspan="14">�����Ͱ� �����ϴ�. </td>
			</tr>
	<? 
		}
	?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="10"></td>
			</tr>
		</tfoot>
	</table>
	<div class="sp20"></div>
	<? } ?>
</div>
	<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="�ݱ�" /></a></div>
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