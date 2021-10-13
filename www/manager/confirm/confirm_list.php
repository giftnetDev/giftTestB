<?session_start();?>
<?
# =============================================================================
# File Name    : order_list.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
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
	$menu_right = "CF002"; // �޴����� ���� �� �־�� �մϴ�

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

if ($s_adm_cp_type == "����" || $s_adm_cp_type == "�ǸŰ���") { 
	$cp_type = $s_adm_com_code;
}


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
	require "../../_classes/biz/payment/payment.php";

	if ($mode == "U") {

		$row_cnt = count($chk_order_goods_no);
		//echo $row_cnt;

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$temp_order_goods_no					= $chk_order_goods_no[$k];
			
			//echo $temp_order_goods_no;

			//if ($temp_delivery_cp <> "")
			//echo $temp_order_goods_no."<br>";
			//echo $confirm_ymd."<br>";
			//echo $confirm_tf."<br>";

			$result = updateConfirmState($conn, $temp_order_goods_no, $confirm_ymd, $confirm_tf, $s_adm_no);
		
		}
	}

	if ($mode == "TU") {

		$row_cnt = count($chk_order_goods_no);
		//echo $row_cnt;

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$temp_order_goods_no					= $chk_order_goods_no[$k];
			
			//echo $temp_order_goods_no;

			//if ($temp_delivery_cp <> "")
			//echo $temp_order_goods_no."<br>";
			//echo $confirm_ymd."<br>";
			//echo $confirm_tf."<br>";

			$result = updateTaxState($conn, $temp_order_goods_no, $tax_tf);
		
		}
	}


#====================================================================
# Request Parameter
#====================================================================


	if ($start_date == "") {
		$start_date = date("Y-m-d",strtotime("-1 month"));;
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));;
	} else {
		$end_date = trim($end_date);
	}

	if ($confirm_ymd == "") {
		$confirm_ymd = date("Y-m-d",strtotime("0 month"));;
	} else {
		$confirm_ymd = trim($confirm_ymd);
	}


	if ($sel_order_state == "") 
		$sel_order_state = "1";

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$con_cate_01 = trim($con_cate_01);
	$con_cate_02 = trim($con_cate_02);
	$con_cate_03 = trim($con_cate_03);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
	$del_tf = "N";
#============================================================
# Page process
#============================================================

	if ($nPage <> "") {
		$nPage = (int)($nPage);
	} else {
		$nPage = 1;
	}

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

	$nListCnt =totalCntConfirmOrderGoods($conn, $start_date, $end_date, $cp_type, $cp_type2, $con_confirm_tf, $con_tax_tf, $etc_condition, $con_use_tf, $del_tf, $search_field, $search_str);
	
	#echo $nListCnt;

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listConfirmOrderGoods($conn, $start_date, $end_date, $cp_type, $cp_type2, $con_confirm_tf, $con_tax_tf, $etc_condition, $con_use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script>
  $(function() {
    $( ".datepicker" ).datepicker({
      showOn: "button",
      buttonImage: "/manager/images/calendar/cal.gif",
      buttonImageOnly: true,
      buttonText: "Select date",
	  showOn: "both",
	  dateFormat: "yy-mm-dd",
	  changeMonth: true,
      changeYear: true
    });
  });
</script>
<script language="javascript">

	function js_view(rn, reserve_no) {

		var frm = document.frm;
		
		var url = "/manager/order/order_read.php?reserve_no="+reserve_no;

		NewWindow(url, 'order_detail','860','600','YES');
		
	}

	function js_price_edit(order_goods_no) {

		var frm = document.frm;
		
		var url = "/manager/order/order_price_edit.php?order_goods_no="+order_goods_no;

		NewWindow(url, 'order_price_edit','860','600','YES');

	}

	// ��ȸ ��ư Ŭ�� �� 
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

function js_toggle() {

	var frm = document.frm;
	var chk_cnt = 0;

	if (frm('chk_order_goods_no[]') == null) {
		alert("������ �ֹ��� �����ϴ�.");
		return;
	}

	if (frm('chk_order_goods_no[]').length != null) {
		
		for (i = 0 ; i < frm('chk_order_goods_no[]').length; i++) {
			if (frm('chk_order_goods_no[]')[i].checked == true) {
				chk_cnt = 1;
			}
		}
	
	} else {
		if (frm('chk_reserve_no[]').checked == true) chk_cnt = 1;
	}
	
	if (chk_cnt == 0) {
		alert("���� ������ �ֹ��� ������ �ּ���");
		return;
	}

	bDelOK = confirm('�ֹ� ���¸� ���� �Ͻðڽ��ϱ�?');
		
	if (bDelOK==true) {

		frm.mode.value = "T";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}
}

	function js_all_check() {
		var frm = document.frm;
		
		if (frm['chk_order_goods_no[]'] != null) {
			
			if (frm['chk_order_goods_no[]'].length != null) {

				if (frm.all_chk.checked == true) {
					for (i = 0; i < frm['chk_order_goods_no[]'].length; i++) {
						frm['chk_order_goods_no[]'][i].checked = true;
					}
				} else {
					for (i = 0; i < frm['chk_order_goods_no[]'].length; i++) {
						frm['chk_order_goods_no[]'][i].checked = false;
					}
				}
			} else {
			
				if (frm.all_chk.checked == true) {
					frm['chk_order_goods_no[]'].checked = true;
				} else {
					frm['chk_order_goods_no[]'].checked = false;
				}
			}
		}
	}

	function js_excel() {

		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.target = "";
		frm.action = "<?=str_replace("list","excel_list",$_SERVER[PHP_SELF])?>";
		frm.submit();

	}

	function js_confirm() {

		var frm = document.frm;
		
		frm.mode.value = "U";
		frm.confirm_tf.value = "Y";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	function js_cancel_confirm() {

		var frm = document.frm;
		
		frm.mode.value = "U";
		frm.confirm_tf.value = "N";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	function js_tax_confirm() {

		var frm = document.frm;
		
		frm.mode.value = "TU";
		frm.tax_tf.value = "����";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	function js_tax_cancel_confirm() {

		var frm = document.frm;
		
		frm.mode.value = "TU";
		frm.tax_tf.value = "�����";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}


	function js_cp_type() {
		
		var frm = document.frm;
		
		frm.target = "ifr_hidden";
		frm.action = "confirm_set_date.php";
		frm.submit();
	}

	function js_setDate (ad_type, sDate, eDate) {

		var frm = document.frm;

		document.getElementById('ad_type_display').innerHTML = ad_type;
		
		if (ad_type != "") {
//			frm.start_date.value = sDate;
//			frm.end_date.value = eDate;
		}
	}

	function js_reset() {
		
		var frm = document.frm;
		frm.con_tax_tf.value = "";
		frm.start_date.value = "<?=date("Y-m-d",strtotime("-1 month"))?>";
		frm.end_date.value = "<?=date("Y-m-d",strtotime("0 month"))?>";
		
		<? if ($s_adm_cp_type == "�") { ?>
			frm.cp_type.value = "";
		<? } ?>
		frm.con_confirm_tf.value = "";
		frm.order_field.value = "C.FINISH_DATE";
		frm.order_str[0].checked = true;
		frm.nPageSize.value = "20";
		frm.search_field.value = "C.RESERVE_NO";
		frm.search_str.value = "";
	}
</script>
</head>

<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="rn" value="">
<input type="hidden" name="order_goods_no" value="">
<input type="hidden" name="reserve_no" value="">
<input type="hidden" name="tax_tf" value="">
<input type="hidden" name="confirm_tf" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<!--<input type="hidden" name="nPageSize" value="<?=$nPageSize?>">-->
<div id="adminwrap">

<?
	#====================================================================
	# common top_area
	#====================================================================

	require "../../_common/top_area.php";
?>

	<table width="100%" cellpadding="0" cellspacing="0">
	<colgroup>
		<col width="180" />
		<col width="*" />
	</colgroup>
	<tr>
		<td class="leftarea">
<?
	#====================================================================
	# common left_area
	#====================================================================

	require "../../_common/left_area.php";
?>


		</td>
		<td class="contentarea">

			<!-- S: mwidthwrap -->
			<div id="mwidthwrap">

				<h2>���� ��ü ���� �� ����Ʈ</h2>
				<div class="btnright"><!--<a href="javascript:js_write();"><img src="../images/admin/btn_regist_02.gif" alt="���" /></a>-->&nbsp;</div>
				<div class="category_choice"><!--<select style="width: 100px;"><option>��ǰ����</option></select>-->&nbsp;</div>

				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="120" />
					<col width="*" />
					<col width="120" />
					<col width="*" />
					<col width="120" />
				</colgroup>
				<thead>
					<tr>
						<th>���޾�ü</th>
						<td>
							<? if ($s_adm_cp_type == "�") { ?>
							<input type="text" class="supplyer" style="width:90%" placeholder="��ü(��/�ڵ�) �Է����ּ���"  name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'����',$cp_type)?>" />
							<script>
							$(function() {
						     var cache = {};
								$( ".supplyer" ).autocomplete({
									source: function( request, response ) {
										var term = request.term;
										if ( term in cache ) {
											response(cache[term]);
											return;
										}
						 
										$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('����'), request, function( data, status, xhr ) {
											cache[term] = data;
											response(data);
										});
									},
									minLength: 2,
									select: function( event, ui ) {
										$(".supplyer").val(ui.item.value);
										$("input[name=cp_type]").val(ui.item.id);
									}
								}).bind( "blur", function( event ) {
									var cp_no = $("input[name=cp_type]").val();
									if(cp_no != '') {
										$.getJSON( "../company/json_company_list.php?cp_no=" + cp_no, function(data) {
											if(data[0].CP_NO == 'undefined') {
												$("input[name=cp_type]").val('');
											} else {
												if(data[0].COMPANY != $(".supplyer").val())
												{

													$(".supplyer").val();
													$("input[name=cp_type]").val('');
												}
											}
										});
									} 
								});
							});
							</script>
							<input type="hidden" name="cp_type" value="<?=$cp_type?>">&nbsp;&nbsp;&nbsp;<span id="ad_type_display"></span>
							<? } else { ?>
							<?=getCompanyName($conn,$cp_type)?>
							<input type="hidden" name="cp_type" value="">
							<? } ?>
						</td>
						<th>��������</th>
						<td colspan="2">
							<?= makeSelectBox($conn,"TAX_TF","con_tax_tf","125","����","",$con_tax_tf)?>
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>���(���)�Ϸ�</th>
						<td>
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="start_date" value="<?=$start_date?>" maxlength="10"/>
							 ~ 
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="end_date" value="<?=$end_date?>" maxlength="10"/>

						<!--
							<input type="text" class="txt" style="width: 75px;" name="start_date" value="<?=$start_date?>" maxlength="10" readonly="1" />
							<a href="javascript:show_calendar('document.frm.start_date', document.frm.start_date.value);" onFocus="blur();">
							<img src="/manager/images/bu/ic_calendar.gif" alt="" /></a>  ~ 

							<input type="text" class="txt" style="width: 75px;" name="end_date" value="<?=$end_date?>" maxlength="10" readonly="1" />
							<a href="javascript:show_calendar('document.frm.end_date', document.frm.end_date.value);" onFocus="blur();">
							<img src="/manager/images/bu/ic_calendar.gif" alt="" /></a>
						-->
						</td>
						<th>���걸��</th>
						<td colspan="2">
							<?= makeSelectBox($conn,"CONFIRM_TF","con_confirm_tf","125","����","",$con_confirm_tf)?>
						</td>
					</tr>
					<tr>
					<!--
						<th>������ :</th>
						<td>
							<input type="text" class="txt" style="width: 75px;" name="confirm_ymd" value="<?=$confirm_ymd?>" maxlength="10" readonly="1" />
							<a href="javascript:show_calendar('document.frm.confirm_ymd', document.frm.confirm_ymd.value);" onFocus="blur();"><img src="/manager/images/bu/ic_calendar.gif" alt="" /></a>
						</td>
						-->
						<th>����</th>
						<td>
							<select name="order_field" style="width:124px;">
								<option value="C.FINISH_DATE" <? if ($order_field == "C.FINISH_DATE") echo "selected"; ?> >�Ϸ��Ͻ�</option>
								<option value="C.CONFIRM_DATE" <? if ($order_field == "C.CONFIRM_DATE") echo "selected"; ?> >�����Ͻ�</option>
								<option value="C.ORDER_DATE" <? if ($order_field == "C.ORDER_DATE") echo "selected"; ?> >�ֹ���</option>
								<option value="O.O_MEM_NM" <? if ($order_field == "O.O_MEM_NM") echo "selected"; ?> >�ֹ��ڸ�</option>
								<option value="O.R_MEM_NM" <? if ($order_field == "O.R_MEM_NM") echo "selected"; ?> >�����ڸ�</option>
								<option value="C.GOODS_NAME" <? if ($order_field == "C.GOODS_NAME") echo "selected"; ?> >��ǰ��</option>
								<option value="C.BUY_PRICE" <? if ($order_field == "C.BUY_PRICE") echo "selected"; ?> >���ް�</option>
								<? if ($s_adm_cp_type == "�") { ?>
								<option value="C.SALE_PRICE" <? if ($order_field == "C.SALE_PRICE") echo "selected"; ?> >�ǸŰ�</option>
								<option value="C.EXTRA_PRICE" <? if ($order_field == "C.EXTRA_PRICE") echo "selected"; ?> >��ۺ�</option>
								<option value="C.DELIVERY_PRICE" <? if ($order_field == "C.DELIVERY_PRICE") echo "selected"; ?> >�߰���ۺ�</option>
								<option value="QQTY" <? if ($order_field == "QQTY") echo "selected"; ?> >����</option>
								<option value="C.SA_DELIVERY_PRICE" <? if ($order_field == "C.SA_DELIVERY_PRICE") echo "selected"; ?> >3�� ������</option>
								<option value="SUM_PRICE" <? if ($order_field == "SUM_PRICE") echo "selected"; ?> >���ǸŰ�</option>
								<option value="PLUS_PRICE" <? if ($order_field == "PLUS_PRICE") echo "selected"; ?> >���Ǹ�����</option>
								<option value="LEE" <? if ($order_field == "LEE") echo "selected"; ?> >���Ǹ�������</option>
								<? } ?>
							</select>&nbsp;&nbsp;
							<input type='radio' class="" name='order_str' value='DESC' <? if (($order_str == "DESC") || ($order_str == "")) echo " checked"; ?> > �������� &nbsp;
							<input type='radio' name='order_str' value='ASC' <? if ($order_str == "ASC") echo " checked"; ?>> ��������
						</td>

						<th>�˻�����</th>
						<td>
							<select name="nPageSize" style="width:74px;">
								<option value="20" <? if ($nPageSize == "20") echo "selected"; ?> >20����</option>
								<option value="50" <? if ($nPageSize == "50") echo "selected"; ?> >50����</option>
								<option value="100" <? if ($nPageSize == "100") echo "selected"; ?> >100����</option>
								<option value="200" <? if ($nPageSize == "200") echo "selected"; ?> >200����</option>
								<option value="300" <? if ($nPageSize == "300") echo "selected"; ?> >300����</option>
								<option value="400" <? if ($nPageSize == "400") echo "selected"; ?> >400����</option>
								<option value="500" <? if ($nPageSize == "500") echo "selected"; ?> >500����</option>
							</select>&nbsp;
							<select name="search_field" style="width:84px;">
								<option value="C.RESERVE_NO" <? if ($search_field == "C.RESERVE_NO") echo "selected"; ?> >�ֹ���ȣ</option>
								<option value="O.O_MEM_NM" <? if ($search_field == "O.O_MEM_NM") echo "selected"; ?> >�ֹ��ڸ�</option>
								<option value="O.R_MEM_NM" <? if ($search_field == "O.R_MEM_NM") echo "selected"; ?> >�����ڸ�</option>
								<option value="C.GOODS_NAME" <? if ($search_field == "C.GOODS_NAME") echo "selected"; ?> >��ǰ��</option>
							</select>&nbsp;

							<input type="text" value="<?=$search_str?>" name="search_str" size="12" class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();"/>
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
							<a href="javascript:js_reset();"><img src="/manager/images/admin/btn_in.gif" alt="reset"/></a>
						</td>
						<td align="right">
							<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="���� ����Ʈ" /></a>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="sp20"></div>
			�� <?=$nListCnt?> ��
			<table cellpadding="0" cellspacing="0" class="rowstable" border="0">
				
				<? if ($s_adm_cp_type == "�") { ?>
				<colgroup>
					<col width="3%" />
					<col width="8%" />
					<col width="12%" />
					<col width="6%" />
					<col width="15%"/>
					<col width="6%" />
					<col width="6%" />
					<col width="4%" />
					<col width="6%" />
					<col width="6%" />
					<col width="6%" />
					<col width="5%" />
					<col width="9%" />
					<col width="8%" />
				</colgroup>
				<thead>
					<tr>
						<th rowspan="2"><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
						<th rowspan="2">�ֹ���ȣ</th>
						<th>�Ǹž�ü</th>
						<th>�ֹ��ڸ�</th>
						<th rowspan="2">��ǰ��</th>
						<th rowspan="2">���ް�</th>
						<th rowspan="2">�ǸŰ�</th>
						<th rowspan="2">����</th>
						<th rowspan="2">��ۺ�</th>
						<th rowspan="2">�߰���ۺ�</th>
						<th rowspan="2">���ް��հ�</th>
						<th rowspan="2">3�ڹ���</th>
						<th>�ֹ�����</th>
						<th class="end">���걸��</th>
					</tr>
					<tr>
						<th>���޾�ü</th>
						<th>�����ڸ�</th>
						<th>�Ϸ��Ͻ�</th>
						<th class="end">�����Ͻ�</th>
					</tr>
					<? } ?>

				<? if ($s_adm_cp_type == "����" || $s_adm_cp_type == "�ǸŰ���") { ?>
				<colgroup>
					<col width="3%" />
					<col width="10%" />
					<col width="7%" />
					<col width="20%"/>
					<col width="7%" />
					<col width="5%" />
					<col width="7%" />
					<col width="7%" />
					<col width="7%" />
					<col width="7%" />
					<col width="10%" />
					<col width="10%" />
				</colgroup>
				<thead>
					<tr>
						<th rowspan="2"><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
						<th rowspan="2">�ֹ���ȣ</th>
						<th>�ֹ��ڸ�</th>
						<th rowspan="2">��ǰ��</th>
						<th rowspan="2">���ް�</th>
						<th rowspan="2">����</th>
						<th rowspan="2">��ۺ�</th>
						<th rowspan="2">�߰���ۺ�</th>
						<th rowspan="2">���ް��հ�</th>
						<th rowspan="2">3�ڹ���</th>
						<th>�ֹ�����</th>
						<th class="end">���걸��</th>
					</tr>
					<tr>
						<th>�����ڸ�</th>
						<th>�Ϸ��Ͻ�</th>
						<th class="end">�����Ͻ�</th>
					</tr>
					<? } ?>

				</thead>
				<tbody>
				<?
					$nCnt = 0;

					$tot_qty = 0;
					$tot_delivery = 0;
					$tot_sum = 0;
					$tot_sa_delivery = 0;
					$tot_add_delivery = 0;
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							/*
							as rn, C.ORDER_GOODS_NO, C.RESERVE_NO, C.MEM_NO, C.ORDER_SEQ, C.GOODS_NO, C.GOODS_CODE, 
							C.GOODS_NAME, C.GOODS_SUB_NAME, 
							C.QTY, C.GOODS_OPTION_01, C.GOODS_OPTION_02, C.GOODS_OPTION_03,
							C.GOODS_OPTION_04, C.GOODS_OPTION_NM_01, C.GOODS_OPTION_NM_02,
							C.GOODS_OPTION_NM_03, C.GOODS_OPTION_NM_04, C.CATE_01, C.CATE_02,
							C.CATE_03, C.CATE_04, C.BUY_PRICE, C.SALE_PRICE, C.EXTRA_PRICE, C.SA_DELIVERY_PRICE, 
							C.USE_TF, C.DEL_TF, C.REG_ADM, C.REG_DATE, C.DEL_ADM, C.DEL_DATE,
							C.ORDER_STATE, C.BUY_CP_NO, C.FINISH_DATE, O.O_MEM_NM, O.R_MEM_NM, C.CONFIRM_TF, C.CONFIRM_DATE, O.CP_NO,
							((C.SALE_PRICE * C.QTY) + (C.EXTRA_PRICE * C.QTY)) AS SUM_PRICE, 
							((C.SALE_PRICE * C.QTY) - (C.BUY_PRICE * C.QTY)) AS PLUS_PRICE, 
							ROUND((((C.SALE_PRICE * C.QTY) - (C.BUY_PRICE * C.QTY)) / (C.SALE_PRICE * C.QTY) * 100),2) AS LEE
							*/

							$rn										= trim($arr_rs[$j]["rn"]);
							$ORDER_GOODS_NO				= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
							$RESERVE_NO						= trim($arr_rs[$j]["RESERVE_NO"]);
							$MEM_NO								= trim($arr_rs[$j]["MEM_NO"]);
							$ORDER_SEQ						= trim($arr_rs[$j]["ORDER_SEQ"]);
							$GOODS_NO							= trim($arr_rs[$j]["GOODS_NO"]);
							$GOODS_CODE						= trim($arr_rs[$j]["GOODS_CODE"]);
							$GOODS_NAME						= trim($arr_rs[$j]["GOODS_NAME"]);
							$GOODS_SUB_NAME				= trim($arr_rs[$j]["GOODS_SUB_NAME"]);
							
							$QTY									= trim($arr_rs[$j]["QTY"]);
							$GOODS_OPTION_01			= trim($arr_rs[$j]["GOODS_OPTION_01"]);
							$GOODS_OPTION_02			= trim($arr_rs[$j]["GOODS_OPTION_02"]);
							$GOODS_OPTION_03			= trim($arr_rs[$j]["GOODS_OPTION_03"]);
							$GOODS_OPTION_04			= trim($arr_rs[$j]["GOODS_OPTION_04"]);

							$GOODS_OPTION_NM_01		= trim($arr_rs[$j]["GOODS_OPTION_NM_01"]);
							$GOODS_OPTION_NM_02		= trim($arr_rs[$j]["GOODS_OPTION_NM_02"]);
							$GOODS_OPTION_NM_03		= trim($arr_rs[$j]["GOODS_OPTION_NM_03"]);
							$GOODS_OPTION_NM_04		= trim($arr_rs[$j]["GOODS_OPTION_NM_04"]);

							$CATE_01							= trim($arr_rs[$j]["CATE_01"]);
							$CATE_02							= trim($arr_rs[$j]["CATE_02"]);
							$CATE_03							= trim($arr_rs[$j]["CATE_03"]);
							$CATE_04							= trim($arr_rs[$j]["CATE_04"]);
							$BUY_PRICE						= trim($arr_rs[$j]["BUY_PRICE"]);
							$SALE_PRICE						= trim($arr_rs[$j]["SALE_PRICE"]);
							$EXTRA_PRICE					= trim($arr_rs[$j]["EXTRA_PRICE"]);
							$DELIVERY_PRICE				= trim($arr_rs[$j]["DELIVERY_PRICE"]);
							$SA_DELIVERY_PRICE		= trim($arr_rs[$j]["SA_DELIVERY_PRICE"]);
							$ORDER_STATE					= trim($arr_rs[$j]["ORDER_STATE"]);
							
							$BUY_CP_NO						= trim($arr_rs[$j]["BUY_CP_NO"]);
							$FINISH_DATE					= trim($arr_rs[$j]["FINISH_DATE"]);
							$O_MEM_NM							= trim($arr_rs[$j]["O_MEM_NM"]);
							$R_MEM_NM							= trim($arr_rs[$j]["R_MEM_NM"]);
							$CONFIRM_TF						= trim($arr_rs[$j]["CONFIRM_TF"]);
							$CONFIRM_DATE					= trim($arr_rs[$j]["CONFIRM_DATE"]);
							$CP_NO								= trim($arr_rs[$j]["CP_NO"]);
							$SUM_PRICE						= trim($arr_rs[$j]["SUM_PRICE"]);
							$PLUS_PRICE						= trim($arr_rs[$j]["PLUS_PRICE"]);
							$LEE									= trim($arr_rs[$j]["LEE"]);
							$TAX_TF								= trim($arr_rs[$j]["TAX_TF"]);
							
							if (($CONFIRM_TF == "N") || ($CONFIRM_TF == "") ) {
								$CONFIRM_DATE		= "";
								$str_confirm = "<font color = 'gray'>������</font>";
							} else {
								$CONFIRM_DATE		= date("Y-m-d H:i",strtotime($CONFIRM_DATE));
								$str_confirm = "<font color = 'navy'>����</font>";
							}


							if ($TAX_TF == "�����") {
								$STR_TAX_TF = "<font color='orange'>(��)</font>";
							} else {
								$STR_TAX_TF = "<font color='navy'>(��)</font>";
							}

							$FINISH_DATE		= date("Y-m-d H:i",strtotime($FINISH_DATE));

							$str_price_class = "price";
							$str_state_class = "state";

							if (($ORDER_STATE == "1") || ($ORDER_STATE == "2")) {
								$refund_able_qty = getRefundAbleQty($conn, $RESERVE_NO, $GOODS_NO, $GOODS_OPTION_01, $GOODS_OPTION_02, $GOODS_OPTION_03, $GOODS_OPTION_04, $GOODS_OPTION_NM_01, $GOODS_OPTION_NM_02, $GOODS_OPTION_NM_03, $GOODS_OPTION_NM_04);
							
							
							//} else if (($ORDER_STATE == "2")) {
							//	$refund_able_qty = getRealDeliveryQty($conn, $RESERVE_NO, $GOODS_NO, $GOODS_OPTION_01, $GOODS_OPTION_02, $GOODS_OPTION_03, $GOODS_OPTION_04, $GOODS_OPTION_NM_01, $GOODS_OPTION_NM_02, $GOODS_OPTION_NM_03, $GOODS_OPTION_NM_04);
							} else if ($ORDER_STATE == "7") {
								$refund_able_qty = -$QTY;

								$str_price_class = "price_refund";
								$str_state_class = "state_refund";
								$tot_sa_delivery = $tot_sa_delivery - $SA_DELIVERY_PRICE;
								$tot_sum = $tot_sum - ($SUM_PRICE);
								$tot_add_delivery = $tot_add_delivery - $DELIVERY_PRICE;

							} else {
								$refund_able_qty = $QTY;
								$tot_sa_delivery = $tot_sa_delivery + $SA_DELIVERY_PRICE;
								$tot_sum = $tot_sum + ($SUM_PRICE);
								$tot_add_delivery = $tot_add_delivery + $DELIVERY_PRICE;
							}

							$tot_qty = $tot_qty + $refund_able_qty;

							//$tot_delivery = $tot_delivery + ($EXTRA_PRICE * $refund_able_qty);
							$tot_delivery = $tot_delivery + ($EXTRA_PRICE);
							
							//echo $tot_sum;
							//echo $SUM_PRICE;
							//echo $refund_able_qty;
							
							if ($s_adm_cp_type == "�") {
						?>
						<tr>
							<td rowspan="2"><input type="checkbox" name="chk_order_goods_no[]" value="<?=$ORDER_GOODS_NO?>"></td>
							<td rowspan="2"><a href="javascript:js_view('<?=$rn?>','<?=$RESERVE_NO?>');"><?=$RESERVE_NO?></a></td>
							<td class="modeual_nm"><?= getCompanyName($conn, $CP_NO);?></td>
							<td class="filedown"><?=$O_MEM_NM?></td>
							<td rowspan="2" class="modeual_nm"><?=$STR_TAX_TF?><?=$GOODS_NAME?></td>
							<td rowspan="2" class="<?=$str_price_class?>"><?=number_format($BUY_PRICE)?></td>
							<? if ($sPageRight_U == "Y") {?>
							<td rowspan="2" class="<?=$str_price_class?>"><a href="javascript:js_price_edit('<?=$ORDER_GOODS_NO?>');"><?=number_format($SALE_PRICE)?></a></td>
							<? } else {?>
							<td rowspan="2" class="<?=$str_price_class?>"><?=number_format($SALE_PRICE)?></td>
							<? } ?>
							<td rowspan="2" class="<?=$str_price_class?>"><?=number_format($refund_able_qty)?></td>
							<td rowspan="2" class="<?=$str_price_class?>"><?=number_format($EXTRA_PRICE)?></td>
							<td rowspan="2" class="<?=$str_price_class?>"><?=number_format($DELIVERY_PRICE)?></td>
							<td rowspan="2" class="<?=$str_price_class?>"><?=number_format($SUM_PRICE)?></td>
							<td rowspan="2" class="<?=$str_price_class?>"><?=number_format($SA_DELIVERY_PRICE)?></td>
							<td class="<?=$str_state_class?>"><?=getDcodeName($conn, "ORDER_STATE", $ORDER_STATE);?></td>
							<td><?=$str_confirm?></td>
						</tr>
						<tr>
							<td class="modeual_nm"><?= getCompanyName($conn, $BUY_CP_NO);?></td>
							<td class="filedown"><?=$R_MEM_NM?></td>
							<td><?=$FINISH_DATE?></td>
							<td><?=$CONFIRM_DATE?></td>
						</tr>
						<?
							}

							if ($s_adm_cp_type == "����" || $s_adm_cp_type == "�ǸŰ���") { 
						?>
						<tr>
							<td rowspan="2"><input type="checkbox" name="chk_order_goods_no[]" value="<?=$ORDER_GOODS_NO?>"></td>
							<td rowspan="2"><a href="javascript:js_view('<?=$rn?>','<?=$RESERVE_NO?>');"><?=$RESERVE_NO?></a></td>
							<td class="filedown"><?=$O_MEM_NM?></td>
							<td rowspan="2" class="modeual_nm"><?=$GOODS_NAME?></td>
							<td rowspan="2" class="<?=$str_price_class?>"><?=number_format($BUY_PRICE)?></td>
							<td rowspan="2" class="<?=$str_price_class?>"><?=number_format($refund_able_qty)?></td>
							<td rowspan="2" class="<?=$str_price_class?>"><?=number_format($EXTRA_PRICE)?></td>
							<td rowspan="2" class="<?=$str_price_class?>"><?=number_format($DELIVERY_PRICE)?></td>
							<td rowspan="2" class="<?=$str_price_class?>"><?=number_format($SUM_PRICE)?></td>
							<td rowspan="2" class="<?=$str_price_class?>"><?=number_format($SA_DELIVERY_PRICE)?></td>
							<td class="<?=$str_state_class?>"><?=getDcodeName($conn, "ORDER_STATE", $ORDER_STATE);?></td>
							<td><?=$str_confirm?></td>
						</tr>
						<tr>
							<td class="filedown"><?=$R_MEM_NM?></td>
							<td><?=$FINISH_DATE?></td>
							<td><?=$CONFIRM_DATE?></td>
						</tr>
						<?
							}
						}
						if ($s_adm_cp_type == "�") {
						?>
						<tr>
							<td>�հ�</td>
							<td>&nbsp;</td>
							<td class="modeual_nm">&nbsp;</td>
							<td class="filedown">&nbsp;</td>
							<td class="modeual_nm">&nbsp;</td>
							<td class="<?=$str_price_class?>">&nbsp;</td>
							<td class="<?=$str_price_class?>">&nbsp;</td>
							<td class="<?=$str_price_class?>"><?=number_format($tot_qty)?></td>
							<td class="<?=$str_price_class?>"><?=number_format($tot_delivery)?></td>
							<td class="<?=$str_price_class?>"><?=number_format($tot_add_delivery)?></td>
							<td class="<?=$str_price_class?>"><?=number_format($tot_sum)?></td>
							<td class="<?=$str_price_class?>"><?=number_format($tot_sa_delivery)?></td>
							<td class="<?=$str_state_class?>">&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<?
						}

						if ($s_adm_cp_type == "����" || $s_adm_cp_type == "�ǸŰ���") { 
						?>
						<tr>
							<td>�հ�</td>
							<td>&nbsp;</td>
							<td class="modeual_nm">&nbsp;</td>
							<td class="filedown">&nbsp;</td>
							<td class="<?=$str_price_class?>">&nbsp;</td>
							<td class="<?=$str_price_class?>"><?=number_format($tot_qty)?></td>
							<td class="<?=$str_price_class?>"><?=number_format($tot_delivery)?></td>
							<td class="<?=$str_price_class?>"><?=number_format($tot_add_delivery)?></td>
							<td class="<?=$str_price_class?>"><?=number_format($tot_sum)?></td>
							<td class="<?=$str_price_class?>"><?=number_format($tot_sa_delivery)?></td>
							<td class="<?=$str_state_class?>">&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<?
						}

					}else{
						?>
						<tr>
							<td height="50" align="center" colspan="16">�����Ͱ� �����ϴ�. </td>
						</tr>
					<?
						}
					?>
				</tbody>
			</table>

			<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">
			
			<? if ($sPageRight_U == "Y") {?>
				������ :
				<!--
				<input type="text" class="txt" style="width: 75px;" name="confirm_ymd" value="<?=$confirm_ymd?>" maxlength="10" readonly="1" />
				<a href="javascript:show_calendar('document.frm.confirm_ymd', document.frm.confirm_ymd.value);" onFocus="blur();">
				<img src="/manager/images/bu/ic_calendar.gif" alt="" /></a>-->
				<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="confirm_ymd" value="<?=$confirm_ymd?>" readonly="1" maxlength="10"/>
				&nbsp;&nbsp;
				<input type="button" name="aa" value=" ����ó�� " class="btntxt" onclick="js_confirm();"> 
				<input type="button" name="aa" value=" ������� " class="btntxt" onclick="js_cancel_confirm();">
				&nbsp;
				&nbsp;
				<input type="button" name="aa" value=" ������ ���� " class="btntxt" onclick="js_tax_confirm();"> 
				<input type="button" name="aa" value=" ������� ���� " class="btntxt" onclick="js_tax_cancel_confirm();"> 

				<? } ?>
			</div>
					<!-- --------------------- ������ ó�� ȭ�� START -------------------------->
					<?
						# ==========================================================================
						#  ����¡ ó��
						# ==========================================================================
						if (sizeof($arr_rs) > 0) {
							#$search_field		= trim($search_field);
							#$search_str			= trim($search_str);
							$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str;
							$strParam = $strParam."&start_date=".$start_date."&end_date=".$end_date."&confirm_date=".$confirm_date;
							$strParam = $strParam."&sel_order_state=".$sel_order_state."&cp_type=".$cp_type."&cp_type2=".$cp_type2;
							$strParam = $strParam."&con_confirm_tf=".$con_confirm_tf."&con_tax_tf=".$con_tax_tf;
							$strParam = $strParam."&order_field=".$order_field."&order_str=".$order_str;

					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- ������ ó�� ȭ�� END -------------------------->
				<br />

				<div class="sp10"></div>
		<!-- // E: mwidthwrap -->
			</div>
			<!-- // E: mwidthwrap -->

		</td>
	</tr>
	</table>
</div>
</form>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>