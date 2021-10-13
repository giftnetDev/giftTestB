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
	$menu_right = "GD005"; // �޴����� ���� �� �־�� �մϴ�

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
	require "../../_classes/biz/goods/goods.php";


	if ($mode == "SYNC_PRICE") {

		$row_cnt = count($chk);

		for ($k = 0; $k < $row_cnt; $k++) {
			$str_seq_no			= $chk[$k];

			if(count($chk_goods_option) <= 0) { 
?>
	<script type="text/javascript">
		alert('����ȭ �� ������ �������ּ���.');
	</script>
<?
				break;
			} 

			syncGoodsPriceAsSeqNo($conn, $chk_goods_option, $str_seq_no, $s_adm_no);
		}
	}


	if ($mode == "D") {

		$row_cnt = count($chk);

		for ($k = 0; $k < $row_cnt; $k++) {
			$str_seq_no			= $chk[$k];
			deleteGoodsPriceAsSeqNo($conn, $str_seq_no);
		}
	}

#====================================================================
# Request Parameter
#====================================================================


	if ($start_date == "") {
		//$start_date = date("Y-m-d",strtotime("-12 month"));
		$start_date = "2010-07-24";
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));;
	} else {
		$end_date = trim($end_date);
	}

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

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
		$nPageSize = 10;
	}

	$nPageBlock	= 10;

#===============================================================
# Get Search list count
#===============================================================

	$filter = array("cp_no" => $cp_no, "diff_buy_price" => $diff_buy_price, "diff_delivery_cnt_in_box" => $diff_delivery_cnt_in_box, "chk_display" => $chk_display);

	$nListCnt =totalCntGoodsPrice($conn, $con_cate, $start_date, $end_date, $start_price, $end_price, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $con_use_tf, $del_tf, $filter, $search_field, $search_str, $order_field, $order_str);

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listGoodsPrice($conn, $con_cate, $start_date, $end_date, $start_price, $end_price, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $con_use_tf, $del_tf, $filter, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize);


?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<script type="text/javascript" src="../jquery/jquery.floatThead.min.js"></script>

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
      changeYear: true,
	  beforeShow: function() {
        setTimeout(function(){
            $('.ui-datepicker').css('z-index', 99999999999999);
        }, 0);
	  }
    });
  });
</script>
<script>
	$(function(){
		$('table.fixed_header_table').floatThead({
			position: 'fixed'
		});
	});
</script>
<script type="text/javascript" >

	function js_write() {

		var url = "goods_price_write.php";
		NewWindow(url, '���ݵ��', '800', '563', 'NO');

	}

	function js_view(goods_no, price) {

		/*
		var url = "goods_price_write.php?mode=S&goods_no="+goods_no+"&price="+price + "&nPage=" + document.frm.nPage.value + "&nPageSize=" + document.frm.nPageSize.value + "&search_field=" + document.search_field.value + "&search_str=" + document.frm.search_str.value + "&order_field=" + document.frm.order_field.value + "&order_str=" + document.frm.order_str.value + "&start_date=" + document.frm.start_date.value + "&end_date=" + document.frm.nPage.value + "&start_price=" + document.frm.nPage.value + "&end_price=" + document.frm.nPage.value + "&con_cate=" + document.frm.nPage.value + "&con_cate_01=" + document.frm.nPage.value + "&con_cate_02=" + document.frm.nPage.value + "&con_cate_03=" + document.frm.nPage.value + "&con_cate_04=" + document.frm.nPage.value;
		NewWindow(url, '���ݵ��', '800', '563', 'NO');
		*/

		var url = "goods_price_write.php";
		var frm = document.frm;

		frm.mode.value = "S";
		frm.goods_no.value = goods_no;
		frm.price.value = price;
		NewWindow('about:blank', 'goods_price_write', '800', '563', 'NO');
		frm.target = "goods_price_write";
		frm.action = url;
		frm.submit();

	}

	function js_sync_goods_price() { 

		var frm = document.frm;
		
		frm.mode.value = "SYNC_PRICE";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}
	
	// ��ȸ ��ư Ŭ�� �� 
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";

		frm.con_cate.value = "";

		if (frm.gd_cate_01 != null) {
			if (frm.gd_cate_01.value != "") {
				frm.con_cate.value = frm.gd_cate_01.value;
			}
		}

		if (frm.gd_cate_02 != null) {
			if (frm.gd_cate_02.value != "") {
				frm.con_cate.value = frm.gd_cate_02.value;
			}
		}

		if (frm.gd_cate_03 != null) {
			if (frm.gd_cate_03.value != "") {
				frm.con_cate.value = frm.gd_cate_03.value;
			}
		}

		if (frm.gd_cate_04 != null) {
			if (frm.gd_cate_04.value != "") {
				frm.con_cate.value = frm.gd_cate_04.value;
			}
		}


		//frm.con_cate_03.value = frm.cp_type.value;


		frm.target = "";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_toggle(seq_no, use_tf) {
		var frm = document.frm;

		bDelOK = confirm('��� ���θ� ���� �Ͻðڽ��ϱ�?');
		
		if (bDelOK==true) {

			if (use_tf == "Y") {
				use_tf = "N";
			} else {
				use_tf = "Y";
			}

			frm.seq_no.value = seq_no;
			frm.use_tf.value = use_tf;
			frm.mode.value = "T";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_excel() {
		
		//alert("�غ��� �Դϴ�..");
		//return;

		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.target = "";
		frm.action = "<?=str_replace("list","excel_list",$_SERVER[PHP_SELF])?>";
		frm.submit();

	}

	function js_all_check() {
		var frm = document.frm;
		
		if (frm['chk[]'] != null) {
			
			if (frm['chk[]'].length != null) {

				if (frm.all_chk.checked == true) {
					for (i = 0; i < frm['chk[]'].length; i++) {
						frm['chk[]'][i].checked = true;
					}
				} else {
					for (i = 0; i < frm['chk[]'].length; i++) {
						frm['chk[]'][i].checked = false;
					}
				}
			} else {
			
				if (frm.all_chk.checked == true) {
					frm['chk[]'].checked = true;
				} else {
					frm['chk[]'].checked = false;
				}
			}
		}
	}

	function js_delete() {
		var frm = document.frm;

		bDelOK = confirm('�ش� �����͸� ���� �Ͻðڽ��ϱ�?');
		
		if (bDelOK==true) {

			frm.mode.value = "D";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_goods_price_change(seq_no) { 
		var url = "pop_goods_price_change_detail.php?seq_no="+seq_no;
		NewWindow(url,'pop_goods_price_change_detail','830','600','Yes');
	}

	function js_productStatusByCompany(){
		var url = "pop_product_state_summary.php";
		NewWindow(url, 'pop_product_state_summary','920','550','YES');
	}

</script>
</head>

<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="rn" value="">
<input type="hidden" name="depth" value="" />
<input type="hidden" name="seq_no" value="">
<input type="hidden" name="use_tf" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>">
<input type="hidden" name="goods_no" value="">
<input type="hidden" name="price" value="">

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

				<h2>��ǰ ���� ����</h2>
				<div class="category_choice"><!--<select style="width: 100px;"><option>��ǰ����</option></select>-->&nbsp;</div>


				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="90" />
					<col width="*" />
					<col width="90" />
					<col width="*" />
					<col width="125" />
				</colgroup>
				<thead>
					<tr>
						<th>ī�װ���</th>
						<td colspan="3">
							<?= makeCategorySelectBoxOnChange($conn, $con_cate, $exclude_category);?>
							<input type="hidden" name="con_cate" value="<?=$con_cate?>">
						</td>
						<td><input type="button" value="��ü�� ��ǰ��Ȳ" onclick="javascript:js_productStatusByCompany();" /></td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>�����</th>
						<td>
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="start_date" value="<?=$start_date?>" maxlength="10" readonly="1" />  ~ 
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="end_date" value="<?=$end_date?>" maxlength="10" readonly="1" />
						</td>
						<th>�ǸŰ�</th>
						<td colspan="2">
							<input type="text" value="<?=$start_price?>" name="start_price" style="width: 75px;" class="txt" onkeyup="return isNumber(this)" /> �� ~
							<input type="text" value="<?=$end_price?>" name="end_price" style="width: 75px;" class="txt" onkeyup="return isNumber(this)" /> ��
						</td>
					</tr>
					<tr>
						<th>�ǸŻ���</th>
						<td>
							<?= makeSelectBox($conn,"GOODS_STATE","con_cate_04","125","����","",$con_cate_04)?>
						</td>
						<th>����</th>
						<td>
							<b>������:</b>
							<label><input type="checkbox" name="diff_buy_price" value="Y" <? if($diff_buy_price == "Y") echo "checked='checked'"?>/> ���ް� ����</label>
							<label><input type="checkbox" name="diff_delivery_cnt_in_box" value="Y" <? if($diff_delivery_cnt_in_box == "Y") echo "checked='checked'"?>/> �ڽ��Լ� ����</label>
							<br><b>���ÿ���:</b>
							<label><input type="radio" name="chk_display" value="Y" <? if($chk_display == "Y") echo "checked='checked'"?>/> ����</label>
							<label><input type="radio" name="chk_display" value="N" <? if($chk_display == "N") echo "checked='checked'"?>/> ������</label>
						</td>
					</tr>
					<tr>
						<th>���޾�ü</th>
						<td>
							<input type="text" class="autocomplete_off" style="width:90%" placeholder="��ü(��/�ڵ�) �Է��� ���͸� �����ּ���" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'',$con_cate_03)?>" />
							<input type="hidden" name="con_cate_03" value="<?=$con_cate_03?>">

							<script>
								$(function(){

									$("input[name=txt_cp_type]").keydown(function(e){

										if(e.keyCode==13) { 

											var keyword = $(this).val();
											if(keyword == "") { 
												$("input[name=con_cate_03]").val('');
												js_search();
											} else { 
												$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('����,�ǸŰ���') +"&term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
													if(data.length == 1) { 
														
														js_selecting_company("txt_cp_type", data[0].label, "con_cate_03", data[0].id);

													} else if(data.length > 1){ 
														NewWindow("../company/pop_company_searched_list.php?con_cp_type=����,�ǸŰ���&search_str="+keyword + "&target_name=txt_cp_type&target_value=con_cate_03",'pop_company_searched_list','950','650','YES');

													} else 
														alert("�˻������ �����ϴ�.");
												});
											}
										}

									});

									$("input[name=txt_cp_type]").keyup(function(e){
										var keyword = $(this).val();

										if(keyword == "") { 
											$("input[name=con_cate_03]").val('');
										}
									});

								});

							</script>
							
						</td>
						<th>�Ǹž�ü</th>
						<td colspan="2">
							<input type="text" class="autocomplete_off" style="width:90%" placeholder="��ü(��/�ڵ�) �Է��� ���͸� �����ּ���" name="txt_cp_no" value="<?=getCompanyAutocompleteTextBox($conn,'',$cp_no)?>" />
							<input type="hidden" name="cp_no" value="<?=$cp_no?>">

							<script>
								$(function(){

									$("input[name=txt_cp_no]").keydown(function(e){

										if(e.keyCode==13) { 

											var keyword = $(this).val();
											if(keyword == "") { 
												$("input[name=cp_no]").val('');
												js_search();
											} else { 
												$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('�Ǹ�,�ǸŰ���') +"&term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
													if(data.length == 1) { 
														
														js_selecting_company("txt_cp_no", data[0].label, "cp_no", data[0].id);

													} else if(data.length > 1){ 
														NewWindow("../company/pop_company_searched_list.php?con_cp_type=�Ǹ�,�ǸŰ���&search_str="+keyword + "&target_name=txt_cp_no&target_value=cp_no",'pop_company_searched_list','950','650','YES');

													} else 
														alert("�˻������ �����ϴ�.");
												});
											}
										}

									});

									$("input[name=txt_cp_no]").keyup(function(e){
										var keyword = $(this).val();

										if(keyword == "") { 
											$("input[name=cp_no]").val('');
										}
									});

								});

								function js_selecting_company(target_name, cp_nm, target_value, cp_no) {
									
									$(function(){

										$("input[name="+target_name+"]").val(cp_nm);
										$("input[name="+target_value+"]").val(cp_no);

									});

									js_search();
								}

							</script>
							
						</td>
					</tr>
					<tr>
						<th>����</th>
						<td>
							<select name="order_field" style="width:84px;">
								<option value="P.REG_DATE" <? if ($order_field == "P.REG_DATE") echo "selected"; ?> >�����</option>
								<option value="GOODS_NAME" <? if ($order_field == "GOODS_NAME") echo "selected"; ?> >��ǰ��</option>
								<option value="GOODS_NO" <? if ($order_field == "GOODS_NO") echo "selected"; ?> >��ǰ��ȣ</option>
								<option value="GOODS_CODE" <? if ($order_field == "GOODS_CODE") echo "selected"; ?> >��ǰ�ڵ�</option>
								<option value="SALE_PRICE" <? if ($order_field == "SALE_PRICE") echo "selected"; ?> >�ǸŰ�</option>
								<option value="BUY_PRICE" <? if ($order_field == "BUY_PRICE") echo "selected"; ?> >���԰�</option>
								<option value="CP_NAME" <? if ($order_field == "CP_NAME") echo "selected"; ?> >�Ǹž�ü</option>
								<option value="STOCK_CNT" <? if ($order_field == "STOCK_CNT") echo "selected"; ?> >���</option>
								<option value="UP_DATE" <? if ($order_field == "UP_DATE") echo "selected"; ?> >������</option>
								<option value="MAJIN_PER" <? if ($order_field == "MAJIN_PER") echo "selected"; ?> >������</option>
							</select>&nbsp;&nbsp;
							<input type='radio' name='order_str' value='ASC' <? if ($order_str == "ASC") echo " checked"; ?>> ��������&nbsp;
							<input type='radio' class="" name='order_str' value='DESC' <? if (($order_str == "DESC") || ($order_str == "")) echo " checked"; ?> > ��������
						</td>
						<th>�˻�����</th>
						<td>
							<select name="nPageSize" style="width:60px;">
								<option value="20" <? if ($nPageSize == "20") echo "selected"; ?> >20����</option>
								<option value="50" <? if ($nPageSize == "50") echo "selected"; ?> >50����</option>
								<option value="100" <? if ($nPageSize == "100") echo "selected"; ?> >100����</option>
								<option value="300" <? if ($nPageSize == "300") echo "selected"; ?> >300����</option>
								<option value="500" <? if ($nPageSize == "500") echo "selected"; ?> >500����</option>
							</select>&nbsp;
							<select name="search_field" style="width:75px;">
								<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >���հ˻�</option>
								<option value="GOODS_NAME" <? if ($search_field == "GOODS_NAME") echo "selected"; ?> >��ǰ��</option>
								<option value="GOODS_NO" <? if ($search_field == "GOODS_NO") echo "selected"; ?> >��ǰ��ȣ</option>
								<option value="GOODS_CODE" <? if ($search_field == "GOODS_CODE") echo "selected"; ?> >��ǰ�ڵ�</option>
								<option value="GOODS_SUB_NO" <? if ($search_field == "GOODS_SUB_NO") echo "selected"; ?> >*���Ի�ǰ��ȣ</option>
								<option value="GOODS_SUB_CODE" <? if ($search_field == "GOODS_SUB_CODE") echo "selected"; ?> >*���Ի�ǰ�ڵ�</option>
								<option value="GOODS_SUB_CODE_AND" <? if ($search_field == "GOODS_SUB_CODE_AND") echo "selected"; ?> >*���Ի�ǰ�ڵ�(AND)</option>
								<option value="GOODS_SUB_NAME_AND" <? if ($search_field == "GOODS_SUB_NAME_AND") echo "selected"; ?> >*���Ի�ǰ��(AND)</option>
								<option value="SUB_CP_CODE" <? if ($search_field == "SUB_CP_CODE") echo "selected"; ?> >*���԰��޻��ڵ�</option>
							</select>&nbsp;

							<input type="text" value="<?=$search_str?>" name="search_str" class="txt" onkeydown = "if(event.keyCode==13) js_search();" />
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						</td>
						<td align="right">
							<select name="print_type" style="width:84px;">
								<option value="" <? if ($print_type == "") echo "selected"; ?> >ȭ��״��</option>
								<option value="FOR_REG" <? if ($print_type == "FOR_REG") echo "selected"; ?> >�ܰ�ǥ����</option>
							</select>&nbsp;
							<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="���� ����Ʈ" /></a>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="sp20"></div>
			�� <?=number_format($nListCnt)?> ��
				<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table">
					<colgroup>
						<col width="3%" />
						<col width="5%" />
						<col width="7%" />
						<col width="8%" />
						<col width="*"/>
						<col width="6%" />
						<col width="7%" />
						<col width="7%" />
						<col width="7%" />
						<col width="7%" />
						<col width="7%" />
						<col width="7%" />
						<col width="9%" />
						<col width="5%" />
						<col width="5%" />
					</colgroup>
					<thead>
						<tr>
							<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
							<th>��ǰ��ȣ</th>
							<th>�̹���</th>
							<th>��ǰ�ڵ�</th>
							<th>��ǰ��</th>
							<th>���޻�</th>
							<th>�ڽ��Լ�</th>
							<th>���԰�</th>
							<th>�����հ�</th>
							<th>�ǸŰ�</th>
							<th>����</th>
							<th>������</th>
							<th>�Ǹž�ü</th>
							<th>��뿩��</th>
							<th class="end">���ÿ���</th>
						</tr>
					</thead>
					<tbody>

				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$rn						= trim($arr_rs[$j]["rn"]);
							$SEQ_NO					= trim($arr_rs[$j]["SEQ_NO"]);
							$GOODS_NO				= trim($arr_rs[$j]["GOODS_NO"]);
							$GOODS_CATE				= trim($arr_rs[$j]["GOODS_CATE"]);
							$GOODS_CODE				= trim($arr_rs[$j]["GOODS_CODE"]);
							$GOODS_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
							$GOODS_SUB_NAME			= SetStringFromDB($arr_rs[$j]["GOODS_SUB_NAME"]);
							$CATE_01				= trim($arr_rs[$j]["CATE_01"]);
							$CATE_02				= trim($arr_rs[$j]["CATE_02"]);
							$CATE_03				= trim($arr_rs[$j]["CATE_03"]);
							$CATE_04				= trim($arr_rs[$j]["CATE_04"]);
							$PRICE					= trim($arr_rs[$j]["PRICE"]);
							$SALE_PRICE				= trim($arr_rs[$j]["SALE_PRICE"]);
							$BUY_PRICE				= trim($arr_rs[$j]["BUY_PRICE"]);
							$EXTRA_PRICE			= trim($arr_rs[$j]["EXTRA_PRICE"]);
							$STOCK_CNT				= trim($arr_rs[$j]["STOCK_CNT"]);
							$IMG_URL				= trim($arr_rs[$j]["IMG_URL"]);
							$FILE_NM				= trim($arr_rs[$j]["FILE_NM_100"]);
							$FILE_RNM				= trim($arr_rs[$j]["FILE_RNM_100"]);
							$FILE_PATH				= trim($arr_rs[$j]["FILE_PATH_100"]);
							$FILE_SIZE				= trim($arr_rs[$j]["FILE_SIZE_100"]);
							$FILE_EXT				= trim($arr_rs[$j]["FILE_EXT_100"]);
							$FILE_NM_150			= trim($arr_rs[$j]["FILE_NM_150"]);
							$FILE_RNM_150			= trim($arr_rs[$j]["FILE_RNM_150"]);
							$FILE_PATH_150			= trim($arr_rs[$j]["FILE_PATH_150"]);
							$FILE_SIZE_150			= trim($arr_rs[$j]["FILE_SIZE_150"]);
							$FILE_EXT_150			= trim($arr_rs[$j]["FILE_EXT_150"]);
							$CP_NAME				= trim($arr_rs[$j]["CP_NAME"]);
							$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
							$DEL_TF					= trim($arr_rs[$j]["DEL_TF"]);
							$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);

							$DELIVERY_CNT_IN_BOX	= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);
							$STICKER_PRICE			= trim($arr_rs[$j]["STICKER_PRICE"]); 
							$PRINT_PRICE			= trim($arr_rs[$j]["PRINT_PRICE"]); 
							$DELIVERY_PRICE			= trim($arr_rs[$j]["DELIVERY_PRICE"]); 
							$LABOR_PRICE			= trim($arr_rs[$j]["LABOR_PRICE"]); 
							$OTHER_PRICE			= trim($arr_rs[$j]["OTHER_PRICE"]);
							$SALE_SUSU				= trim($arr_rs[$j]["SALE_SUSU"]);

							$ORI_BUY_PRICE				= trim($arr_rs[$j]["ORI_BUY_PRICE"]);
							$ORI_DELIVERY_CNT_IN_BOX	= trim($arr_rs[$j]["ORI_DELIVERY_CNT_IN_BOX"]);

							//���ÿ���
							if(trim($arr_rs[$j]["DISPLAY"]) == "Y"){
								$DISPLAY_TF = "����";
							} else {
								$DISPLAY_TF = "������";
							}

							$str_goods_no = $GOODS_TYPE.substr("000000".$GOODS_NO,-5);
							
							// �̹����� ���� �Ǿ� ���� ���
							$img_url	= getGoodsImage($FILE_NM, $IMG_URL, $FILE_PATH_150, $FILE_RNM_150, "50", "50");

							if ($TAX_TF == "�����") {
								$STR_TAX_TF = "<font color='orange'>(�����)</font>";
							} else {
								$STR_TAX_TF = "<font color='navy'>(����)</font>";
							}

							if($DELIVERY_PRICE == 0 || $DELIVERY_CNT_IN_BOX == 0)
								$DELIVERY_PER_PRICE = 0;
							else 
								$DELIVERY_PER_PRICE = round($DELIVERY_PRICE / $DELIVERY_CNT_IN_BOX, 0);
							
							$SUSU_PRICE = round($SALE_PRICE / 100 * $SALE_SUSU, 0);

							$MAJIN = $SALE_PRICE - $SUSU_PRICE - $PRICE;

							if($SALE_PRICE != 0)
								$MAJIN_PER = round(($MAJIN / $SALE_PRICE) * 100, 2)."%";
							else 
								$MAJIN_PER = "���Ұ�";

							if($USE_TF == "N")
								$str_use_style = "unused";
							else { 
								if($CATE_04 != "�Ǹ���")
									$str_use_style = "expired";
								else
									$str_use_style = "";
							}

							$REG_DATE = date("Y-m-d",strtotime($REG_DATE));
				
				?>
						<tr class="<?=$str_use_style ?>" >
							<td>
								<input type="checkbox" name="chk[]" class="chk" value="<?=$SEQ_NO?>">
							</td>
							<td><a href="javascript:js_goods_price_change('<?= $SEQ_NO ?>');"><?=$GOODS_NO?></a></td>
							<td style="padding: 1px 1px 1px 1px"><a href="javascript:js_goods_price_change('<?= $SEQ_NO ?>');"><img src="<?=$img_url?>" width="50" height="50"></a></td>
							<td><a href="javascript:js_goods_price_change('<?= $SEQ_NO ?>');"><?=$GOODS_CODE?></td>
							<td class="modeual_nm"><a href="javascript:js_goods_price_change('<?= $SEQ_NO ?>');"><?=$STR_TAX_TF?> <?= $GOODS_NAME ?> <?= $GOODS_SUB_NAME ?></a></td>
							<td><?= getCompanyName($conn, $CATE_03);?></td>
							<td class="price"><?= $DELIVERY_CNT_IN_BOX ?> ��
							<?
								if($ORI_DELIVERY_CNT_IN_BOX <> $DELIVERY_CNT_IN_BOX)
									echo "<span style='color:red;'>(".$ORI_DELIVERY_CNT_IN_BOX."��)</span>";
							?>
							</td>
							<td class="price"><?= number_format($BUY_PRICE) ?> ��
							<?
								if($ORI_BUY_PRICE <> $BUY_PRICE)
									echo "<span style='color:red;'>(".number_format($ORI_BUY_PRICE)."��)</span>";
							?>
							</td>
							<td class="price"><?= number_format($PRICE) ?> ��</td>
							<td class="price"><b><?= number_format($SALE_PRICE) ?></b> ��</td>
							<td class="price"><?= number_format($MAJIN) ?> ��</td>
							<td class="price"><?=$MAJIN_PER?></td>
							<td><?=$CP_NAME ?></td>
							<td class="filedown"><?=getDcodeName($conn, "GOODS_STATE", $CATE_04); ?></a></td>
							<td class="filedown"><?=$DISPLAY_TF?></a></td>
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
					<!-- --------------------- ������ ó�� ȭ�� START -------------------------->
					<?
						# ==========================================================================
						#  ����¡ ó��
						# ==========================================================================
						if (sizeof($arr_rs) > 0) {
							$strParam = "";
							$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str;
							$strParam = $strParam."&start_date=".$start_date."&end_date=".$end_date."&start_price=".$start_price."&end_price=".$end_price."&con_cate=".$con_cate;
							$strParam = $strParam."&con_cate_01=".$con_cate_01."&con_cate_02=".$con_cate_02."&con_cate_03=".$con_cate_03."&con_cate_04=".$con_cate_04."&view_type=".$view_type."&cp_no=".$cp_no."&diff_buy_price=".$diff_buy_price."&diff_delivery_cnt_in_box=".$diff_delivery_cnt_in_box;

					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- ������ ó�� ȭ�� END -------------------------->
			</div>

				<div style="padding:5px;">
				<!--
				<? if ($sPageRight_I == "Y") {?>
					<a href="javascript:js_write();"><img src="../images/admin/btn_regist_02.gif" alt="���" /></a>
				<? } ?>
				-->
				<? if ($sPageRight_D == "Y") {?>
					<a href="javascript:js_delete();"><img src="../images/admin/btn_delete.gif" alt="����" /></a>
				<? } ?>
				</div>

			<div style="display:scroll;position:fixed;bottom:10px;right:10px;padding:10px;border:1px solid black;background-color:#fff;">
				
				<? if ($sPageRight_D == "Y" || $sPageRight_U == "Y" || $sPageRight_I == "Y") {?>
				<b>��ǰ���� ��Ī : </b>
				<label><input type="checkbox" name="chk_goods_option[]" value="buy_price"/>���ް�</label>
				<label><input type="checkbox" name="chk_goods_option[]" value="delivery_cnt_in_box"/>�ڽ��Լ�</label>
				<input type="button" name="aa" value=" ����ȭ " class="btntxt" onclick="js_sync_goods_price();"> 
				<? } ?>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="#">�� ����</a>
			</div>

			<!-- // E: mwidthwrap -->

		</td>
	</tr>
	</table>
		<script>

		$(function(){

			var last_click_idx = -1;
			$(".chk").click(function(event){
				
				var clicked_elem = $(this);
				var clicked_elem_chked = $(this).prop("checked");

				var start_idx = -1;
				var end_idx = -1;
				var click_idx = -1;

				$(".chk").each(function( index, elem ) {

					//Ŭ����ġ ����
					if(clicked_elem.val() == $(elem).val())
						click_idx = index;

				});

				if(event.shiftKey) {

					if($(".chk:checked").size() >= 2) {
						$(".chk").each(function( index, elem ) {

							//üũ�� ���� ���� üũ
							if(start_idx == -1 && $(elem).prop("checked"))
								start_idx = index;

							//üũ�� ������ �ε��� üũ
							if($(elem).prop("checked"))
								end_idx = index;

						});

						if($(".chk:checked").size() > 2 && last_click_idx > click_idx)
							start_idx = click_idx;

						if($(".chk:checked").size() > 2 && last_click_idx < click_idx)
							end_idx = click_idx;


						//alert("start_idx: " + start_idx + ", end_idx: " + end_idx + ", click_idx: " + click_idx+ ", last_click_idx: " + last_click_idx);

						
						$(".chk").each(function(index, elem) {

							if(start_idx <= index && index <= end_idx) {
								$(elem).prop("checked", true);
							}
							else
								$(elem).prop("checked", false);
							
						});
						
					}

					last_click_idx = click_idx;
				}

			});

		});
	
	</script>
</div>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>