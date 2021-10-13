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

	$con_order_type = "";

	$menu_right = "ST006"; // �޴����� ���� �� �־�� �մϴ�


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
	require "../../_classes/biz/stats/stats.php";

	if ( count($_GET) == 0 && count($_POST) == 0 ) { 
		if($s_adm_md_tf == "Y")
			$opt_manager_no = $s_adm_no;
	}

#====================================================================
# Request Parameter
#====================================================================

	$mm_subtree	 = "3";

	if ($start_date == "") {
		$start_date = date("Y-m-d",strtotime("-1 month"));
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));
	} else {
		$end_date = trim($end_date);
	}

	$day_0 = date("Y-m-d",strtotime("0 month"));
	$day_1 = date("Y-m-d",strtotime("-1 day"));
	$day_7 = date("Y-m-d",strtotime("-7 day"));
	$day_31 = date("Y-m-d",strtotime("-1 month"));

	if ($order_field == "")
		$order_field = "REG_DATE";

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

#===============================================================
# Get Search list count
#===============================================================

	$filter = array("cate_01" => $cate_01, "PICK_ORDER_STATE" => $PICK_ORDER_STATE);

	$nListCnt = cntSalesStatement($conn, $start_date, $end_date, $cp_type2, $cp_type, $opt_manager_no, $filter, $search_field, $search_str);

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listSalesStatement($conn, $start_date, $end_date, $cp_type2, $cp_type, $opt_manager_no, $filter, $search_field, $search_str, $order_field, $order_asc, $nPage, $nPageSize, $nListCnt);

	$arr_rs_sum = sumSalesStatement($conn, $start_date, $end_date, $cp_type2, $cp_type, $opt_manager_no, $filter, $search_field, $search_str);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<script type="text/javascript" src="../jquery/jquery.floatThead.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script>
  $(function() {
    $( ".datepicker" ).datepicker({
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

	$(".datepicker").keydown(function(){

		var value = $(this).val();

		if(value.length == 4 && value.lastIndexOf('-') == -1)
			$(this).val(value.substr(0, 4)+ "-" + value.substr(4)) ;

		if(value.length == 7 && value.lastIndexOf('-') == 4)
			$(this).val(value.substr(0, 8) + "-" + value.substr(8)) ;
	});

	$(".datepicker").blur(function(){
		if($(this).val().length > 8)
			checkStaEndDt($("input[name=start_date]"), $("input[name=end_date]"));
	});

  });

</script>
<script>
	$(function() {
		$('.autocomplete_off').attr('autocomplete', 'off');
	});
</script>
<script language="javascript">
	function js_view(rn, reserve_no) {

		var frm = document.frm;
		
		var url = "/manager/order/order_read.php?reserve_no="+reserve_no;

		NewWindow(url, 'order_detail','860','600','YES');
	}

	// ��ȸ ��ư Ŭ�� �� 
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_excel() {

		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.target = "";
		frm.method = "post";
		frm.action = "sales_statement_excel.php";
		frm.submit();

	}

	function js_reload() {
		location.reload();
	}

		function js_search_date_by_code(code) {

		var frm = document.frm;

		if (code == "prev_month") {
			SetPrevMonthDays("start_date", "end_date");
		}

		if (code == "prev_week") {
			SetPrevWeek("start_date", "end_date");
		}

		if (code == "prev_day") {
			SetYesterday("start_date", "end_date");
		}

		if (code == "today") {
			SetToday("start_date", "end_date");
		}

		if (code == "this_week") {
			SetWeek("start_date", "end_date");
		}

		if (code == "this_month") {
			SetCurrentMonthDays("start_date", "end_date");
		}

		frm.nPage.value = 1;
		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}
	
	function js_reset() {
		
		var frm = document.frm;
		frm.start_date.value = "<?=date("Y-m-d",strtotime("-1 month"))?>";
		frm.end_date.value = "<?=date("Y-m-d",strtotime("0 month"))?>";
		frm.sel_order_state.value = "";
		
		<? if ($s_adm_cp_type == "�") { ?>
			frm.cp_type.value = "";
			frm.cp_type2.value = "";
			frm.txt_cp_type.value = "";
			frm.txt_cp_type2.value = "";
			frm.sel_pay_type.value = "";
		<? } ?>
		
		frm.order_field.value = "ORDER_DATE";
		frm.order_str[0].checked = true;
		frm.nPageSize.value = "20";
		frm.search_field.value = "ALL";
		frm.search_str.value = "";
	}



</script>
<style type="text/css">
#ex_scroll { z-index: 1; background-color:#f7f7f7; overflow: auto; width: 100%; height:155px; border:1px solid #d1d1d1;}
#temp_scroll { z-index: 1; background-color:#f7f7f7; overflow: auto; width: 95%; height:500px; border:1px solid #d1d1d1;}
</style>
</head>

<body id="admin"> 

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="rn" value="">
<input type="hidden" name="reserve_no" value="">
<input type="hidden" name="use_tf" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="con_order_type" value="<?=$con_order_type?>">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<input type="hidden" name="pick_order_state" value="<?=$PICK_ORDER_STATE?>">
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
				<h2>���� ����ǥ
				</h2>
				<div class="category_choice"><!--<select style="width: 100px;"><option>��ǰ����</option></select>-->&nbsp;</div>

				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="10%" />
					<col width="37%" />
					<col width="10%" />
					<col width="37%" />
					<col width="6%" />
				</colgroup>
				<thead>
					<tr>
						<th>
							<select name="search_date_type">
								<option value="order_date" <? if ($search_date_type == "order_date" || $search_date_type == "") echo "selected" ?>>�ֹ���</option>
								<!--<option value="reg_date" <? if ($search_date_type == "reg_date") echo "selected" ?>>�ֹ������</option>-->
							</select>
						</th>
						<td colspan="3">
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" id="start_date"  name="start_date" value="<?=$start_date?>" maxlength="10"/>
							 ~ 
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" id="end_date" name="end_date" value="<?=$end_date?>" maxlength="10"/>
							&nbsp;<input type="button" value="����" onclick="javascript:js_search_date_by_code('prev_month');"/>
							<input type="button" value="����" onclick="javascript:js_search_date_by_code('prev_week');"/>
							<input type="button" value="����" onclick="javascript:js_search_date_by_code('prev_day');"/>
							<input type="button" value="����" onclick="javascript:js_search_date_by_code('today');"/>
							<input type="button" value="����" onclick="javascript:js_search_date_by_code('this_week');"/>
							<input type="button" value="�ݿ�" onclick="javascript:js_search_date_by_code('this_month');"/>

						</td>
						<td align="right">
							<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="���� ����Ʈ" /></a>
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>����</th>
						<td colspan="4">
							<b>��������� :</b>
							<?if($s_adm_group_no <> "6") { //2016-09-02 �����δ� �ڱ� �ֹ��� ���� ����?>
								<?= makeAdminInfoByMDSelectBox($conn,"opt_manager_no"," style='width:70px;' ","��ü","", $opt_manager_no) ?>
							<? } else { ?>
								<input type="hidden" name="opt_manager_no" value="<?=$opt_manager_no?>"/>
								<?=getAdminName($conn,$opt_manager_no)?>
								<script>console.log('opt_manager_no : <?=$opt_manager_no?>');</script>
							<? } ?>
							&nbsp;&nbsp;&nbsp;&nbsp;<b>�ֹ���ǰ���� :</b>
							<label><input type="checkbox" name="cate_01[]" value="����" <?if(in_array('����', $cate_01)) echo "checked";?>/>����</label>
							<label><input type="checkbox" name="cate_01[]" value="����" <?if(in_array('����', $cate_01)) echo "checked";?>/>����</label>
							<label><input type="checkbox" name="cate_01[]" value="�߰�" <?if(in_array('�߰�', $cate_01)) echo "checked";?>/>�߰�</label>
							&nbsp;&nbsp;&nbsp;&nbsp;<b>��ۻ���(���� �� ����) :</b>
							<label><input type="checkbox" name="PICK_ORDER_STATE[]" value="0" <?if(in_array('0', $PICK_ORDER_STATE)) echo "checked";?>/>�Ա���</label>
							<label><input type="checkbox" name="PICK_ORDER_STATE[]" value="1" <?if(in_array('1', $PICK_ORDER_STATE)) echo "checked";?>/>�ֹ�����</label>
							<label><input type="checkbox" name="PICK_ORDER_STATE[]" value="2"	<?if(in_array('2', $PICK_ORDER_STATE)) echo "checked";?>/>����غ���</label>
							<label><input type="checkbox" name="PICK_ORDER_STATE[]" value="3"	<?if(in_array('3', $PICK_ORDER_STATE)) echo "checked";?>/>��ۿϷ�</label>
							<label><input type="checkbox" name="PICK_ORDER_STATE[]" value="4"	<?if(in_array('4', $PICK_ORDER_STATE)) echo "checked";?>/>�Ա������</label>
							<label><input type="checkbox" name="PICK_ORDER_STATE[]" value="6"	<?if(in_array('6', $PICK_ORDER_STATE)) echo "checked";?>/>���</label>
							<label><input type="checkbox" name="PICK_ORDER_STATE[]" value="7"	<?if(in_array('7', $PICK_ORDER_STATE)) echo "checked";?>/>��ǰ</label>
							<label><input type="checkbox" name="PICK_ORDER_STATE[]" value="8"	<?if(in_array('8', $PICK_ORDER_STATE)) echo "checked";?>/>��ȯ</label>
							<label><input type="checkbox" name="PICK_ORDER_STATE[]" value="9"	<?if(in_array('9', $PICK_ORDER_STATE)) echo "checked";?>/>�±�ȯ</label>
							<label><input type="checkbox" name="PICK_ORDER_STATE[]" value="99"<?if(in_array('99',$PICK_ORDER_STATE)) echo "checked";?>/>��Ÿ</label>
						</td>
					</tr>
					<tr>
						<th>�Ǹž�ü</th>
						<td>
							<input type="text" class="autocomplete_off" style="width:90%" placeholder="��ü(��/�ڵ�) �Է��� ���͸� �����ּ���" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'',$cp_type)?>" />
							<input type="hidden" name="cp_type" value="<?=$cp_type?>">

							<script>
								$(function(){

									$("input[name=txt_cp_type]").keydown(function(e){

										if(e.keyCode==13) { 

											var keyword = $(this).val();
											if(keyword == "") { 
												$("input[name=cp_type]").val('');
												js_search();
											} else { 
												$.getJSON( "../company/json_company_list.php?term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE,MEMO", function(data) {
													if(data.length == 1) { 
														
														js_selecting_company("txt_cp_type", data[0].label, "cp_type", data[0].id);

													} else if(data.length > 1){ 
														NewWindow("../company/pop_company_searched_list.php?search_str="+keyword + "&target_name=txt_cp_type&target_value=cp_type",'pop_company_searched_list','950','650','YES');

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

							</script>
						</td>
						<th>���޾�ü</th>
						<td colspan="2">
							<input type="text" class="autocomplete_off" style="width:90%" placeholder="��ü(��/�ڵ�) �Է��� ���͸� �����ּ���" name="txt_cp_type2" value="<?=getCompanyAutocompleteTextBox($conn,'',$cp_type2)?>" />
							<input type="hidden" name="cp_type2" value="<?=$cp_type2?>">

							<script>
								$(function(){

									$("input[name=txt_cp_type2]").keydown(function(e){

										if(e.keyCode==13) { 

											var keyword = $(this).val();
											
											if(keyword == "") { 
												$("input[name=cp_type2]").val('');
												js_search();
											} else { 
												$.getJSON( "../company/json_company_list.php?term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE,MEMO", function(data) {
													if(data.length == 1) { 
														
														js_selecting_company("txt_cp_type2", data[0].label, "cp_type2", data[0].id);

													} else if(data.length > 1){ 
														NewWindow("../company/pop_company_searched_list.php?search_str="+keyword + "&target_name=txt_cp_type2&target_value=cp_type2",'pop_company_searched_list','950','650','YES');

													} else 
														alert("�˻������ �����ϴ�.");
												});
											}
										}

									});
									
									$("input[name=txt_cp_type2]").keyup(function(e){
										var keyword = $(this).val();

										if(keyword == "") { 
											$("input[name=cp_type2]").val('');
										}
									});

								});

							</script>
							<script>
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
							<!--
							<select name="order_field" style="width:84px;">
								<option value="ORDER_DATE" <? if ($order_field == "ORDER_DATE") echo "selected"; ?> >�ֹ��Ͻ�</option>
								<option value="REG_DATE" <? if ($order_field == "REG_DATE") echo "selected"; ?> >�����</option>
								<option value="O_MEM_NM" <? if ($order_field == "O_MEM_NM") echo "selected"; ?> >�ֹ��ڸ�</option>
								<option value="R_MEM_NM" <? if ($order_field == "R_MEM_NM") echo "selected"; ?> >�����ڸ�</option>
								<? if ($s_adm_cp_type == "�") { ?>
								<option value="TOTAL_BUY_PRICE" <? if ($order_field == "TOTAL_BUY_PRICE") echo "selected"; ?> >�Ѹ��Կ���</option>
								<option value="TOTAL_SALE_PRICE" <? if ($order_field == "TOTAL_SALE_PRICE") echo "selected"; ?> >���ǸŰ�</option>
								<option value="TOTAL_EXTRA_PRICE" <? if ($order_field == "TOTAL_EXTRA_PRICE") echo "selected"; ?> >�ѹ�ۺ�</option>
								<option value="TOTAL_QTY" <? if ($order_field == "TOTAL_QTY") echo "selected"; ?> >�Ѽ���</option>
								<option value="TOTAL_DELIVERY_PRICE" <? if ($order_field == "TOTAL_DELIVERY_PRICE") echo "selected"; ?> >�߰���ۺ�</option>
								<option value="TOTAL_PRICE" <? if ($order_field == "TOTAL_PRICE") echo "selected"; ?> >�հ�</option>
								<option value="TOTAL_PLUS_PRICE" <? if ($order_field == "TOTAL_PLUS_PRICE") echo "selected"; ?> >���Ǹ�����</option>
								<option value="LEE" <? if ($order_field == "LEE") echo "selected"; ?> >���Ǹ�������</option>
								<? } ?>
							</select>&nbsp;&nbsp;
							-->
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
								<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >���հ˻�</option>
								<option value="O.RESERVE_NO" <? if ($search_field == "O.RESERVE_NO") echo "selected"; ?> >�ֹ���ȣ</option>
								<option value="O.O_MEM_NM" <? if ($search_field == "O.O_MEM_NM") echo "selected"; ?> >�ֹ��ڸ�</option>
								<option value="O.R_MEM_NM" <? if ($search_field == "O.R_MEM_NM") echo "selected"; ?> >�����ڸ�</option>
								<option value="OG.GOODS_NAME" <? if ($search_field == "OG.GOODS_NAME") echo "selected"; ?> >��ǰ��</option>
								<option value="OG.GOODS_CODE" <? if ($search_field == "OG.GOODS_CODE") echo "selected"; ?> >��ǰ�ڵ�</option>
							</select>&nbsp;

							<input type="text" value="<?=$search_str?>" name="search_str" size="12"class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();"/>
							
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
							<!--<a href="javascript:js_reset();"><img src="/manager/images/admin/btn_in.gif" alt="reset"/></a>-->
						</td>
						<td align="right">
						</td>
					</tr>
				</tbody>
			</table>
			<div class="sp20"></div>
			<b>�� <?=$nListCnt?> ��</b>
			<div id="temp_scroll">
			<table cellpadding="0" cellspacing="0" class="rowstable02" border="0">
				<colgroup>
					<col width="140px" /> 
					<col width="100px" />
					<col width="150px" />
					<col width="100px" /> 
					<col width="140px" /> 
					<col width="70px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="200px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
					<col width="100px" /> 
				</colgroup>
				<thead>
					<tr>
						<th>�����ֹ�����ȣ</th>
						<th>�����ֹ���ǰ��ȣ</th>
						<th>�ܺ��ֹ���ȣ</th>
						<th>�Ǹ�_��ü�ڵ�</th>
						<th>�Ǹ�_��ü��</th>
						<th>�Ǹ�_������</th>
						<th>�ֹ��ڸ�</th>
						<th>�����ڸ�</th>
						
						<th>�ֹ���ǰ����</th>
						<th>��������</th>
						<th>��ȯ����</th>

						<th>��ǰ�ڵ�</th>
						<th>��ǰ��</th>
						<th>�Ӽ�</th>
						<th>����</th>
						<th>�ǸŰ�</th>
						<th>��������</th>
						<th>�߰���ۺ�</th>
						<th>������</th>
						<th><b>�Ǹű��հ�</b></th>

						<th>����_��ü�ڵ�</th>
						<th>����_��ü��</th>
						<th>����_������</th>
						
						<th>���԰�</th>
						<th>��ƼĿ���</th>
						<th>����/�μ���</th>
						<th>�ù��</th>
						<th>�ڽ��Լ�</th>
						<th>������</th>
						<th>�Ǹż�����</th>
						<th>�۾���</th>
						<th>��Ÿ���</th>
						<th>���Կ���</th>
						<th><b>���Կ����հ�</b></th>

						<th>���� ����</th>
						<th>������</th>
						<th><b>�����հ�</b></th>
						
						<th>��ƼĿ��Ī</th>
						<th>��ƼĿ�޼���</th>
						<th>�ƿ��ڽ���ƼĿ����</th>
						<th>��������Ī</th>
						<th>�μ�޼���</th>
						<th>���������</th>
						<th>�۾��޸�</th>
						<th>��۹��</th>

						<th>�ֹ�����</th>
						<th>�ֹ�Ȯ����</th>
						<th>�ù�����</th>
						<th>����Ȯ����</th>
						<th>���������</th>
						<th class="end">�ֹ���</th>
					</tr>
				</thead>
				
				<tbody>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							
							$RESERVE_NO				= SetStringFromDB($arr_rs[$j]["RESERVE_NO"]);
							$ORDER_GOODS_NO			= SetStringFromDB($arr_rs[$j]["ORDER_GOODS_NO"]); 
							$CP_ORDER_NO			= SetStringFromDB($arr_rs[$j]["CP_ORDER_NO"]); 
							$CP_CODE				= SetStringFromDB($arr_rs[$j]["CP_CODE"]); 
							$CP_NM					= SetStringFromDB($arr_rs[$j]["CP_NM"]); 
							$CP_NM2					= SetStringFromDB($arr_rs[$j]["CP_NM2"]); 
							$O_MEM_NM				= SetStringFromDB($arr_rs[$j]["O_MEM_NM"]); 
							$R_MEM_NM				= SetStringFromDB($arr_rs[$j]["R_MEM_NM"]); 
							
							$CATE_01				= SetStringFromDB($arr_rs[$j]["CATE_01"]); 
							$TAX_TF					= SetStringFromDB($arr_rs[$j]["TAX_TF"]);
							$CATE_04				= SetStringFromDB($arr_rs[$j]["CATE_04"]); 
							
							$GOODS_NO				= SetStringFromDB($arr_rs[$j]["GOODS_NO"]); 
							$GOODS_CODE				= SetStringFromDB($arr_rs[$j]["GOODS_CODE"]); 
							$GOODS_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]); 
							$GOODS_SUB_NAME			= SetStringFromDB($arr_rs[$j]["GOODS_SUB_NAME"]); 
							$QTY					= SetStringFromDB($arr_rs[$j]["QTY"]);  
							$SALE_PRICE				= SetStringFromDB($arr_rs[$j]["SALE_PRICE"]); 
							$DISCOUNT_PRICE			= SetStringFromDB($arr_rs[$j]["DISCOUNT_PRICE"]); 
							$SA_DELIVERY_PRICE		= SetStringFromDB($arr_rs[$j]["SA_DELIVERY_PRICE"]); 
							$EXTRA_PRICE			= SetStringFromDB($arr_rs[$j]["EXTRA_PRICE"]); 

							$BUY_CP_CODE			= SetStringFromDB($arr_rs[$j]["BUY_CP_CODE"]); 
							$BUY_CP_NM				= SetStringFromDB($arr_rs[$j]["BUY_CP_NM"]); 
							$BUY_CP_NM2				= SetStringFromDB($arr_rs[$j]["BUY_CP_NM2"]); 
							
							$BUY_PRICE				= SetStringFromDB($arr_rs[$j]["BUY_PRICE"]); 
							$STICKER_PRICE			= SetStringFromDB($arr_rs[$j]["STICKER_PRICE"]); 
							$PRINT_PRICE			= SetStringFromDB($arr_rs[$j]["PRINT_PRICE"]); 
							$DELIVERY_PRICE			= SetStringFromDB($arr_rs[$j]["DELIVERY_PRICE"]); 
							$DELIVERY_CNT_IN_BOX	= SetStringFromDB($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]); 
							$SALE_SUSU				= SetStringFromDB($arr_rs[$j]["SALE_SUSU"]); 
							$LABOR_PRICE			= SetStringFromDB($arr_rs[$j]["LABOR_PRICE"]); 
							$OTHER_PRICE			= SetStringFromDB($arr_rs[$j]["OTHER_PRICE"]); 
							$PRICE					= SetStringFromDB($arr_rs[$j]["PRICE"]); 

							$OPT_STICKER_NO			= SetStringFromDB($arr_rs[$j]["OPT_STICKER_NO"]); 
							$OPT_STICKER_READY		= SetStringFromDB($arr_rs[$j]["OPT_STICKER_READY"]);	
							$OPT_STICKER_MSG		= SetStringFromDB($arr_rs[$j]["OPT_STICKER_MSG"]); 
							$OPT_OUTBOX_TF			= SetStringFromDB($arr_rs[$j]["OPT_OUTBOX_TF"]); 
							$OPT_WRAP_NO			= SetStringFromDB($arr_rs[$j]["OPT_WRAP_NO"]); 
							$OPT_PRINT_MSG			= SetStringFromDB($arr_rs[$j]["OPT_PRINT_MSG"]); 
							$OPT_OUTSTOCK_DATE		= SetStringFromDB($arr_rs[$j]["OPT_OUTSTOCK_DATE"]); 
							$OPT_MEMO				= SetStringFromDB($arr_rs[$j]["OPT_MEMO"]);
							
							$ORDER_STATE			= SetStringFromDB($arr_rs[$j]["ORDER_STATE"]); 
							$ORDER_CONFIRM_DATE		= SetStringFromDB($arr_rs[$j]["ORDER_CONFIRM_DATE"]); 
							$DELIVERY_DATE			= SetStringFromDB($arr_rs[$j]["DELIVERY_DATE"]); 
							$SALE_CONFIRM_YMD		= SetStringFromDB($arr_rs[$j]["SALE_CONFIRM_YMD"]);
							$OPT_MANAGER_NO			= SetStringFromDB($arr_rs[$j]["OPT_MANAGER_NO"]); 
							$ORDER_DATE				= SetStringFromDB($arr_rs[$j]["ORDER_DATE"]); 

							$DELIVERY_TYPE			= SetStringFromDB($arr_rs[$j]["DELIVERY_TYPE"]); 
								
							if($ORDER_STATE > 3)
								$QTY = $QTY * -1;

							if($CATE_04 == "CHANGE") {
								$CATE_04 = "��ȯ��";
							}

							//if($DELIVERY_TYPE != 99) { 

								if($OPT_STICKER_NO == "0") { 
									$OPT_STICKER_NO = "����";
									$STICKER_PRICE = 0;
								} else { 
									$OPT_STICKER_NO = getGoodsCodeName($conn, $OPT_STICKER_NO);
								}

								if($OPT_WRAP_NO == "0") { 
									$OPT_WRAP_NO = "����";
									$PRINT_PRICE = 0;
								} else { 
									$OPT_WRAP_NO = getGoodsCodeName($conn, $OPT_WRAP_NO);
								}

								if($OPT_OUTBOX_TF == "N" || $OPT_OUTBOX_TF == "")
									$OPT_OUTBOX_TF = "����";
								else
									$OPT_OUTBOX_TF = "����";


								//////////////////////////////////////////////////////////////////
								//$BUY_PRICE = getBuyPrice($conn, $BUY_PRICE, $GOODS_NO, $DELIVERY_CNT_IN_BOX);

								if($DELIVERY_PRICE == 0 || $DELIVERY_CNT_IN_BOX == 0)
									$DELIVERY_PER_PRICE = 0;
								else 
									$DELIVERY_PER_PRICE = round($DELIVERY_PRICE / $DELIVERY_CNT_IN_BOX, 0);
								
								
								//���� ������� MRO�� �����
								//if($CP_CODE == "3559") 
								//	$SUSU_PRICE = round($SALE_PRICE / 100.0 * $SALE_SUSU, 4);
								//else {
								//	$SUSU_PRICE = 0;
								//	$SALE_SUSU = 0;
								//}

								//$EXTRA_PRICE�� ����
								//$SUSU_PRICE = round($SALE_PRICE / 100.0 * $SALE_SUSU, 0);

								//�ǸŽ� �����Ḧ ����ϰ� ���� �������� ���� 2018-08-08
								$TOTAL_WONGA = round($BUY_PRICE + $STICKER_PRICE + $PRINT_PRICE + $DELIVERY_PER_PRICE + $LABOR_PRICE + $OTHER_PRICE, 0);

							
								//$MAJIN = $SALE_PRICE - $SUSU_PRICE - $TOTAL_WONGA;
								$TOTAL_SALE_PRICE = ($SALE_PRICE * $QTY) - $DISCOUNT_PRICE - ($EXTRA_PRICE * $QTY);
								
								if($QTY > 0)
									$MAJIN = $SALE_PRICE - $TOTAL_WONGA;
								else
									$MAJIN = ($SALE_PRICE - $TOTAL_WONGA) * -1;

								if($SALE_PRICE != 0)
									$MAJIN_PER = round(($MAJIN / ($TOTAL_SALE_PRICE / $QTY)) * 100, 2);
								else 
									$MAJIN_PER = 0;

								$TOTAL_MAJIN = $TOTAL_SALE_PRICE - ($TOTAL_WONGA * $QTY);

							
							/*
							} else { 
								
								$OPT_STICKER_NO = "";
								$STICKER_PRICE = "0";
								$OPT_WRAP_NO = "";
								$PRINT_PRICE = "0";
								$OPT_OUTBOX_TF = "";
								$BUY_PRICE = "0";
								$DELIVERY_PER_PRICE = 0;
								$DELIVERY_CNT_IN_BOX = 0;
								$SUSU_PRICE = 0;
								$SALE_SUSU = 0;
								$SUSU_PRICE = 0;
								$TOTAL_WONGA = 0;
								$MAJIN = 0;
								$MAJIN_PER = 0;
								$TOTAL_MAJIN = 0;

								$TOTAL_SALE_PRICE = ($SALE_PRICE * $QTY) - $DISCOUNT_PRICE - ($EXTRA_PRICE * $QTY);

							}
							*/

							///////////////////////////////////////////////////////////////////

							if ($OPT_OUTSTOCK_DATE <> "")  {
								$OPT_OUTSTOCK_DATE		= date("Y-m-d",strtotime($OPT_OUTSTOCK_DATE));
							}


							if ($ORDER_CONFIRM_DATE <> "")  {
								$ORDER_CONFIRM_DATE		= date("Y-m-d",strtotime($ORDER_CONFIRM_DATE));
							}

							if ($DELIVERY_DATE <> "")  {
								$DELIVERY_DATE		= date("Y-m-d",strtotime($DELIVERY_DATE));
							}

							
							if ($ORDER_DATE <> "")  {
								$ORDER_DATE		= date("Y-m-d H:i",strtotime($ORDER_DATE));
							}

							$OPT_MANAGER_NO = getAdminName($conn, $OPT_MANAGER_NO);
							$ORDER_STATE = getDcodeName($conn, 'ORDER_STATE', $ORDER_STATE);

								
						?>
						<tr style="text-align:center;">
							<td><?=$RESERVE_NO ?></td>		
							<td><?=$ORDER_GOODS_NO ?></td>	
							<td><?=$CP_ORDER_NO ?></td>	
							<td><?=$CP_CODE?></td>
							<td><?=$CP_NM?></td>
							<td><?=$CP_NM2?></td>
							<td><?=$O_MEM_NM?></td>
							<td><?=$R_MEM_NM?></td> 
							
							<td><?=$CATE_01?></td>
							<td><?=$TAX_TF?></td>
							<td><?=$CATE_04?></td>
							
							<td><?=$GOODS_CODE?></td>
							<td><?=$GOODS_NAME?></td>
							<td><?=$GOODS_SUB_NAME?></td>
							<td><?=number_format($QTY)?></td>
							<td><?=number_format($SALE_PRICE)?></td>
							<td><?=number_format($DISCOUNT_PRICE)?></td>
							<td><?=number_format($SA_DELIVERY_PRICE)?></td>
							<td><?=number_format($EXTRA_PRICE * $QTY)?></td>
							<td><?=number_format($TOTAL_SALE_PRICE)?></td>

							<td><?=$BUY_CP_CODE?></td> 
							<td><?=$BUY_CP_NM?></td>
							<td><?=$BUY_CP_NM2?></td>
							
							<td><?=number_format($BUY_PRICE)?></td> 
							<td><?=number_format($STICKER_PRICE)?></td> 
							<td><?=number_format($PRINT_PRICE)?></td> 
							<td><?=number_format($DELIVERY_PRICE)?></td>
							<td><?=number_format($DELIVERY_CNT_IN_BOX) ?></td>
							<td><?=number_format($DELIVERY_PER_PRICE) ?></td>
							<td><?=$SALE_SUSU?></td>
							<td><?=number_format($LABOR_PRICE)?></td>
							<td><?=number_format($OTHER_PRICE)?></td> 
							<td><?=number_format($TOTAL_WONGA)?></td>
							<td><?=number_format($TOTAL_WONGA * $QTY)?></td>

							<td><?=number_format($MAJIN)?></td>
							<td><?=$MAJIN_PER?>%</td>
							<td><?=number_format($TOTAL_MAJIN)?></td>

							<td><?=$OPT_STICKER_NO?></td>
							<td><?=$OPT_STICKER_MSG?></td> 
							<td><?=$OPT_OUTBOX_TF?></td> 
							<td><?=$OPT_WRAP_NO?></td>
							<td><?=$OPT_PRINT_MSG?></td> 
							<td><?=$OPT_OUTSTOCK_DATE?></td>
							<td><?=$OPT_MEMO?></td>
							<td><?=getDcodeName($conn,"DELIVERY_TYPE",$DELIVERY_TYPE)?></td>
							
							<td><?=$ORDER_STATE?></td>
							<td><?=$ORDER_CONFIRM_DATE?></td> 
							<td><?=$DELIVERY_DATE?></td>
							<td><?=$SALE_CONFIRM_YMD?></td>
							<td><?=$OPT_MANAGER_NO?></td>
							<td><?=$ORDER_DATE?></td>
						</tr>
						
						<?

							}
						} 
						
					?>
				</tbody>
			</table>
			</div>
					
				
				<div class="sp10"></div>

				<table cellpadding="0" cellspacing="0" class="rowstable02" border="0">
				<colgroup>
					<col width="*" /> 
					<col width="8%" />
					<col width="8%" />
					<col width="8%" /> 
					<col width="8%" /> 
					<col width="8%" /> 
					<col width="12%" /> 
					<col width="12%" /> 
					<col width="8%" /> 
					<col width="8%" /> 
					<col width="12%" /> 
				</colgroup>
				<tr>
					<th rowspan="2"><b>�հ�</b></th>
					<th colspan="6">�Ǹ�</th>
					<th colspan="4">����</th>
				</tr>
				<tr>
					<th>����</th>
					<th>�Ǹž�</th>
					<th>���ξ�</th>
					<th>�߰���ۺ�</th>
					<th>������</th>
					<th>�� �Ǹž�</th>
					<th>�����Ѿ�</th>
					<th>��� ����</th>
					<th>��� ������</th>
					<th>�� ����</th>
				</tr>
				<?
						if(sizeof($arr_rs_sum) > 0) { 

							$SUM_QTY				= SetStringFromDB($arr_rs_sum[0]["SUM_QTY"]);
							$SUM_SALE_PRICE			= SetStringFromDB($arr_rs_sum[0]["SUM_SALE_PRICE"]);
							$SUM_DISCOUNT_PRICE		= SetStringFromDB($arr_rs_sum[0]["SUM_DISCOUNT_PRICE"]);
							$SUM_SA_DELIVERY_PRICE	= SetStringFromDB($arr_rs_sum[0]["SUM_SA_DELIVERY_PRICE"]);
							$SUM_EXTRA_PRICE		= SetStringFromDB($arr_rs_sum[0]["SUM_EXTRA_PRICE"]);
							$SUM_TOTAL_SALE_PRICE	= SetStringFromDB($arr_rs_sum[0]["SUM_TOTAL_SALE_PRICE"]);
							
							$SUM_TOTAL_WONGA		= SetStringFromDB($arr_rs_sum[0]["SUM_TOTAL_WONGA"]);
							//$AVG_MAJIN				= SetStringFromDB($arr_rs_sum[0]["AVG_MAJIN"]);
							//$AVG_MAJIN_PER			= SetStringFromDB($arr_rs_sum[0]["AVG_MAJIN_PER"]);
							$SUM_TOTAL_MAJIN		= SetStringFromDB($arr_rs_sum[0]["SUM_TOTAL_MAJIN"]);

					?>
						<tr style="text-align:center; height:30px;">
							<td></td>
							<td><?=number_format($SUM_QTY)?></td>
							<td><?=number_format($SUM_SALE_PRICE)?></td>
							<td><?=number_format($SUM_DISCOUNT_PRICE)?></td>
							<td><?=number_format($SUM_SA_DELIVERY_PRICE)?></td>
							<td><?=number_format($SUM_EXTRA_PRICE)?></td>
							<td><?=number_format($SUM_TOTAL_SALE_PRICE)?></td>

							<td><?=number_format($SUM_TOTAL_WONGA)?></td>
							<td><?=number_format($AVG_MAJIN)?></td>
							<td><?=$AVG_MAJIN_PER?>%</td>
							<td><?=number_format($SUM_TOTAL_MAJIN)?></td>
							
						</tr>

					<?  }  ?>
				</table>
				<div class="sp10"></div>

				<!-- --------------------- ������ ó�� ȭ�� START -------------------------->
				<?
					# ==========================================================================
					#  ����¡ ó��
					# ==========================================================================
					if (sizeof($arr_rs) > 0) {
						#$search_field		= trim($search_field);
						#$search_str			= trim($search_str);

						$strParam = "";
						$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str;
						$strParam = $strParam."&start_date=".$start_date."&end_date=".$end_date."&cp_type2=".$cp_type2."&cp_type=".$cp_type."&opt_manager_no=".$opt_manager_no;
						$strParam = $strParam."&".http_build_query(array('cate_01' => $cate_01));

				?>
				<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
				<?
					}
				?>
				<!-- --------------------- ������ ó�� ȭ�� END -------------------------->

		<!-- // E: mwidthwrap -->
			</div>
			<!-- // E: mwidthwrap -->
			<div class="sp50"></div>

		</td>
	</tr>
	</table>
	<a style="display:scroll;position:fixed;bottom:10px;right:10px;" href="#">�� ����</a>
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