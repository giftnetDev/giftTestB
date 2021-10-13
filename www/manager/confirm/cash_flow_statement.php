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
	$menu_right = "CF011"; // �޴����� ���� �� �־�� �մϴ�

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
	require "../../_classes/biz/company/company.php";

	$result = false;

	if ($mode == "D") {
		$row_cnt = count($chk_no);

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_cf_no = $chk_no[$k];
			
			$result = deleteCashFlow($conn, $s_adm_no, $str_cf_no);
		}
		
	}

	if ($mode == "C") {
		$row_cnt = count($chk_no);

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_cf_no = $chk_no[$k];
			
			$result = updateCashFlowCheckTF($conn, $check_tf, $str_cf_no);
		}
		
	}

	if ($mode == "SYNC") {
		
		syncCashStatementWithCompanyLedger($conn);
	}

	if ($mode == "I") {

		

		$goods_no = null;
		$unit_price = str_replace(",", "", $unit_price);
		$surtax		= str_replace(",", "", $surtax);
		$qty					= 1;
		$reserve_no				= null;
		$order_goods_no			= null;
		$rgn_no					= null;

		
		switch($inout_method){ 
			case "����": 
						if($inout_type == "�Ա�") 
							$inout_type_code = "RX05";
						else
							$inout_type_code = "LX06";
							
						
						$name = getCompanyName($conn, $bank_no);
						$surtax = 0;
						$result	= insertCompanyLedger($conn, $cp_type, $inout_date, $inout_type_code, $goods_no, $name, $qty, $unit_price, $bank_no, $surtax, "", "", $memo, $reserve_no, $order_goods_no, "����", $rgn_no, $s_adm_no, null);
						break;
			case "��ü": 
						if($inout_type == "�Ա�") 
							$inout_type_code = "RX05";
						else
							$inout_type_code = "LX06";

						$name = getCompanyName($conn, $to_cp_no);
						$surtax = 0;
						$result	= insertCompanyLedger($conn, $cp_type, $inout_date, $inout_type_code, $goods_no, $name, $qty, $unit_price, $to_cp_no, $surtax, "", "", $memo, $reserve_no, $order_goods_no, "��ü", $rgn_no, $s_adm_no, null);
						break;
			case "ī��": 
						if($inout_type == "�Ա�") 
							$inout_type_code = "RX05";
						else
							$inout_type_code = "LX06";

						$name = getCompanyName($conn, $card_no);
						$result	= insertCompanyLedger($conn, $cp_type, $inout_date, $inout_type_code, $goods_no, $name, $qty, $unit_price + $surtax, $card_no, $surtax, "", "", $memo, $reserve_no, $order_goods_no, "ī��", $rgn_no, $s_adm_no, null);
						break;
			default:
						if($inout_type == "�Ա�") 
							$inout_type_code = "RW02";
						else
							$inout_type_code = "LW04";
						$name = "< ���� ".$inout_type." >";
						$to_cp_no = null;
						$surtax = 0;
						$result	= insertCompanyLedger($conn, $cp_type, $inout_date, $inout_type_code, $goods_no, $name, $qty, $unit_price, $to_cp_no, $surtax, "", "", $memo, $reserve_no, $order_goods_no, "����", $rgn_no, $s_adm_no, null);
						break;

		}

		if($result) { 

			$result = updateTaxInvoiceInCash($conn, $cf_no, $inout_type_code, $unit_price, $s_adm_no);
		}

		if($result) { 

			$strParam = $strParam."nPage=".$nPage."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str;
						$strParam = $strParam."&op_cp_no=".$op_cp_no."&account_cp_no=".$account_cp_no."&sale_cp_no=".$sale_cp_no."&sale_adm_no=".$sale_adm_no."&search_date_type=".$search_date_type."&start_date=".$start_date."&end_date=".$end_date."&cf_inout=".$cf_inout."&cf_type=".$cf_type."&has_in_cash=".$has_in_cash."&match_tf=".$match_tf;
?>
<script type="text/javascript">

	alert("�Ա�ó�� �Ǿ����ϴ�.");
	document.location = "<?=$_SERVER[PHP_SELF]?>?<?=$strParam?>";

</script>
<?
		} else { 
?>
<script type="text/javascript">
	
	alert('���� �߻� �Ǿ����ϴ�. �ý��� ����ڶ� �������ּ���.');

</script>
<?

		}
	}

#====================================================================
# Request Parameter
#====================================================================

	if ( count($_GET) == 0 && count($_POST) == 0 ) { 
		if($s_adm_md_tf == "Y")
			$sale_adm_no = $s_adm_no;
	}

	$day_0 = date("Y-m-d",strtotime("0 month"));
	$day_1 = date("Y-m-d",strtotime("-1 day"));
	$day_7 = date("Y-m-d",strtotime("-7 day"));
	$day_31 = date("Y-m-d",strtotime("-1 month"));

	if ($start_date == "") {
		$d = new DateTime('first day of this month');
		$start_date = $d->format("Y-m-d");
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));;
	} else {
		$end_date = trim($end_date);
	}

	if($search_date_type == "")
		$search_date_type = "out_date";

	$search_str = trim($search_str);

	if ($inout_date == "") {
		$inout_date = date("Y-m-d",strtotime("0 month"));;
	} else {
		$inout_date = trim($inout_date);
	}

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

	$filter = array('op_cp_no' => $op_cp_no, 'account_cp_no' => $account_cp_no, 'sale_cp_no' => $sale_cp_no, 'sale_adm_no' => $sale_adm_no, 'cf_inout' => $cf_inout, 'cf_type' => $cf_type, 'has_in_cash' => $has_in_cash, 'match_tf' => $match_tf);

	$nListCnt = totalCntCashFlow($conn, $search_date_type, $start_date, $end_date, $filter, $search_field, $search_str);

	$nTotalPage = (int)(($nListCnt - 1) / (int)$nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listCashFlow($conn, $search_date_type, $start_date, $end_date, $filter, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize, $nListCnt);

	$arr_rs_sum = sumCashFlow($conn, $search_date_type, $start_date, $end_date, $filter, $search_field, $search_str);
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js?v=2"></script>
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
	$(function() {
		$('.autocomplete_off').attr('autocomplete', 'off');
	});
</script>
<script>
	$(function(){
		$('table.fixed_header_table').floatThead({
			position: 'fixed'
		});
	});
</script>
<script language="javascript">


	function js_write() {

		/*
		var frm = document.frm;
		
		frm.target = "";
		frm.method = "get";
		frm.action = "cash_flow_statement_write.php";
		frm.submit();
		*/
		
		location.href= "cash_flow_statement_write.php";
	}

	function js_view(cf_no) { 

		/*
		// �Ա��� ���忡 �ִٸ� ���� ������ �ʿ� ����
		var frm = document.frm;

		frm.cf_no.value = cf_no;
		
		frm.target = "";
		frm.method = "get";
		frm.action = "cash_flow_statement_write.php";
		frm.submit();
		*/
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

	function js_sync_cash_sheet() { 
		var frm = document.frm;
		
		frm.mode.value = "SYNC";
		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	function js_delete() {
		var frm = document.frm;

		bDelOK = confirm('������ ������ ���� �Ͻðڽ��ϱ�?');
		
		if (bDelOK==true) {
			
			frm.mode.value = "D";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_check_tf(check_tf) { 
		var frm = document.frm;

		frm.mode.value = "C";
		frm.check_tf.value = check_tf;
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

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

		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_excel() {

		var frm = document.frm;

		/*
		if(frm.account_cp_no.value == "") { 
			alert('��ȸ�� ���°� ���õ��� �ʾҽ��ϴ�.');
			return;
		}
		*/
		
		frm.nPage.value = "1";
		frm.target = "";
		frm.action = "cash_flow_statement_excel.php";
		frm.submit();

	}

	function js_all_check() {
		var frm = document.frm;
		
		if (frm['chk_no[]'] != null) {
			
			if (frm['chk_no[]'].length != null) {

				if (frm.all_chk.checked == true) {
					for (i = 0; i < frm['chk_no[]'].length; i++) {
						frm['chk_no[]'][i].checked = true;
					}
				} else {
					for (i = 0; i < frm['chk_no[]'].length; i++) {
						frm['chk_no[]'][i].checked = false;
					}
				}
			} else {
			
				if (frm.all_chk.checked == true) {
					frm['chk_no[]'].checked = true;
				} else {
					frm['chk_no[]'].checked = false;
				}
			}
		}
	}

	function js_open_pop_cash_sheet() {
		
		var url = "pop_cash_flow_statement_excel.php";
		NewWindow(url, '���ݰ�꼭�Է�', '950', '513', 'YES');
		
	}

	function js_save() { 
		
		/*
		var frm = document.frm;
		frm.target = "_blank";
		frm.action = "company_ledger_write_method.php?cp_type=" + frm.cp_type.value + "&unit_price=" + frm.unit_price.value + "&inout_type=�Ա�";
		frm.submit();
		*/
		var frm = document.frm;
		
		if (isNull(frm.cp_type.value)) {
			alert('��ü�� �������ּ���.');
			frm.txt_cp_type.focus();
			return ;		
		}

		if (isNull(frm.inout_date.value)) {
			alert('�������� �������ּ���.');
			frm.inout_date.focus();
			return ;		
		}

		if (isNull(frm.inout_type.value)) {
			alert('�Ա�/���� �������ּ���.');
			frm.inout_type.focus();
			return ;		
		}		

		if (isNull(frm.inout_method.value)) {
			alert('�Է� ����� �������ּ���.');
			frm.inout_method.focus();
			return ;		
		}

		if (isNull(frm.unit_price.value)) {
			alert('������� �������ּ���.');
			frm.unit_price.focus();
			return ;		
		}

		frm.mode.value = "I";

		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_input_cash(cf_no, biz_no, total_price) { 

		$("input[name=cf_no]").val(cf_no);

		$("input[name=txt_cp_type]").val(biz_no);

		var e = jQuery.Event("keydown");
		e.which = 13; // # Some key code value
		e.keyCode = 13;
		$("input[name=txt_cp_type]").trigger(e);
		
		$("input[name=unit_price]").val(total_price);

	}
</script>
<style>
	.row_monthly {background-color:#DFDFDF; font-weight:bold;}
	.row_daily {background-color:#EFEFEF; font-weight:bold;}
	tr.row_matched > td {color:green;} 
	tr.row_checked > td {background-color:yellow;} 
	tr.closed > td {background-color:#fff; color: #A2A2A2;} 
</style> 
</head>

<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="cf_no" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<input type="hidden" name="check_tf" value="">
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

				<h2>�ڱ� �Ѱ�ǥ</h2>
				<div class="btnright" style="margin:0 0 5px 0;">
					<input type="button" name="cc" value=" ��꼭 ���� �Է� " class="btntxt" onclick="js_write();">
					<input type="button" name="cc" value=" ���ڼ��� ��꼭 �ϰ� �Է� (����)" class="btntxt" onclick="js_open_pop_cash_sheet();">
					<input type="button" name="cc" value=" �ŷ����� <-> ��꼭 ��ĪȮ�� " class="btntxt" onclick="js_sync_cash_sheet();">
				</div>
				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="100" />
					<col width="300" />
					<col width="100" />
					<col width="300" />
					<col width="50" />
				</colgroup>
				
				<tr>
					<th>
						<select name="search_date_type">
							<option value="out_date" <? if ($search_date_type == "out_date" || $search_date_type == "") echo "selected" ?>>��������</option>
							<option value="written_date" <? if ($search_date_type == "written_date") echo "selected" ?>>�ۼ�����</option>
							<option value="in_date" <? if ($search_date_type == "in_date") echo "selected" ?>>�Ա���</option>
							<option value="reg_date" <? if ($search_date_type == "reg_date") echo "selected" ?>>�����</option>
						</select>
					</th>
					<td colspan="3">
						<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" id="start_date" name="start_date" value="<?=$start_date?>" maxlength="10"/>
						 ~ 
						<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" id="end_date" name="end_date" value="<?=$end_date?>" maxlength="10"/>
						&nbsp;
						<input type="button" value="����" onclick="javascript:js_search_date_by_code('prev_month');"/>
						<input type="button" value="����" onclick="javascript:js_search_date_by_code('prev_week');"/>
						<input type="button" value="����" onclick="javascript:js_search_date_by_code('prev_day');"/>
						<input type="button" value="����" onclick="javascript:js_search_date_by_code('today');"/>
						<input type="button" value="����" onclick="javascript:js_search_date_by_code('this_week');"/>
						<input type="button" value="�ݿ�" onclick="javascript:js_search_date_by_code('this_month');"/>
						
					</td>
					<td align="right">
					</td>
				</tr>
				<!--
				<tr>
					<th>�����</th>
					<td colspan="4">
						<input type="text" class="autocomplete_off" style="width:80%" placeholder="��ü(��/�ڵ�) �Է��� ���͸� �����ּ���" name="txt_account_cp_no" value="<?=getCompanyAutocompleteTextBox($conn,'',$account_cp_no)?>" />
							<script>
								$(function(){

									$("input[name=txt_account_cp_no]").keydown(function(e){

										if(e.keyCode==13) { 

											var keyword = $(this).val();
											if(keyword == "") { 
												$("input[name=account_cp_no]").val('');
												js_search();
											} else { 
											
												$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('����') +"&term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
													if(data.length == 1) { 
														
														js_selecting_company("txt_account_cp_no", data[0].label, "account_cp_no", data[0].id);

													} else if(data.length > 1){ 
														NewWindow("../company/pop_company_searched_list.php?con_cp_type=����&search_str="+keyword + "&target_name=txt_account_cp_no&target_value=account_cp_no",'pop_company_searched_list','950','650','YES');

													} else 
														alert("�˻������ �����ϴ�.");
												});
											}
										}

									});

									$("input[name=txt_account_cp_no]").keyup(function(e){
										var keyword = $(this).val();

										if(keyword == "") { 
											$("[name=account_cp_no]").val('');
										}
									});

								});
							</script>
						
						
						<?
							
							$arr_account_cp = listCashFlowAccountCpDistinct($conn, $search_date_type, $start_date, $end_date, $filter);
							if(sizeof($arr_account_cp) > 0) { 
								echo makeGenericSelectBox($conn, $arr_account_cp, 'account_cp_no', '100', "����", "", $account_cp_no, "ACCOUNT_CP_NO", "CP_NM");
							} else { 
						?>
						<input type="hidden" name="account_cp_no" value="<?=$account_cp_no?>">
						<? } ?>
					</td>
					
				</tr>
				-->
				<tr>
					<!--
					<th>��ü��</th>
					<td>
						<input type="text" class="autocomplete_off" style="width:90%" placeholder="��ü(��/�ڵ�) �Է��� ���͸� �����ּ���" name="txt_sale_cp_no" value="<?=getCompanyAutocompleteTextBox($conn,'',$sale_cp_no)?>" />
						<input type="hidden" name="sale_cp_no" value="<?=$sale_cp_no?>">

						<script>
							$(function(){

								$("input[name=txt_sale_cp_no]").keydown(function(e){

									if(e.keyCode==13) { 

										var keyword = $(this).val();
										if(keyword == "") { 
											$("input[name=sale_cp_no]").val('');
										} else { 
										
											$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('�Ǹ�,����,�ǸŰ���') +"&term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
												if(data.length == 1) { 
													
													js_selecting_company("txt_sale_cp_no", data[0].label, "sale_cp_no", data[0].id);

												} else if(data.length > 1){ 
													NewWindow("../company/pop_company_searched_list.php?con_cp_type=�Ǹ�,����,�ǸŰ���&search_str="+keyword + "&target_name=txt_sale_cp_no&target_value=sale_cp_no",'pop_company_searched_list2','950','650','YES');

												} else 
													alert("�˻������ �����ϴ�.");
											});
										}
									}

								});

								$("input[name=txt_sale_cp_no]").keyup(function(e){
									var keyword = $(this).val();

									if(keyword == "") { 
										$("input[name=sale_cp_no]").val('');
									}
								});

							});

							function js_selecting_company(target_name, cp_nm, target_value, cp_no) {
								
								$(function(){
									
									//alert(target_name + " " + cp_nm  + " " + target_value + " " + cp_no);
									$("[name="+target_name+"]").val(cp_nm);
									$("[name="+target_value+"]").val(cp_no);
									js_search();
								});
							}
						</script>
					</td>
					-->
					
					<th>����</th>
					<td colspan="3">
						<label>���� : </label>
						<?
							$arr_op = getOperatingCompany($conn, '');
							echo makeGenericSelectBox($conn, $arr_op, 'op_cp_no', '100', "����", "", $op_cp_no, "CP_NO", "CP_NM");
						?>
						<label>���� : </label>
							<select name="cf_inout">
								<option value="" <?if($cf_inout == "") echo "selected";?>>��ü</option>
								<option value="����" <?if($cf_inout == "����") echo "selected";?>>����</option>
								<option value="����" <?if($cf_inout == "����") echo "selected";?>>����</option>
							</select>
						<label>�������� : </label>
						<?=makeSelectBox($conn, 'CASH_STATEMENT_TYPE', 'cf_type','100','��ü','',$cf_type)?>
						<label>�Աݿ��� : </label>
						<select name="has_in_cash">
							<option value="" <?if($has_in_cash == "") echo "selected";?>>��ü</option>
							<option value="N" <?if($has_in_cash == "N") echo "selected";?>>�Աݾ���</option>
							<option value="Y" <?if($has_in_cash == "Y") echo "selected";?>>�Աݵ�</option>
						</select>
						<label>�����ġ : </label>
						<select name="match_tf">
							<option value="" <?if($match_tf == "") echo "selected";?>>��ü</option>
							<option value="N" <?if($match_tf == "N") echo "selected";?>>��ġ����</option>
							<option value="Y" <?if($match_tf == "Y") echo "selected";?>>��ġ</option>
						</select>
						<label>��������� :</label>
						<?= makeAdminInfoByMDSelectBox($conn,"sale_adm_no"," style='width:70px;' ","��ü","", $sale_adm_no) ?>
					</td>
					<td align="right">
						<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="���� ����Ʈ" /></a>
					</td>
				</tr>
				<tr>
					<th>����</th>
					<td>
						<select name="order_field" style="width:94px;">
							<option value="OUT_DATE" <? if ($order_field == "OUT_DATE") echo "selected"; ?> >������</option>
							<option value="WRITTEN_DATE" <? if ($order_field == "WRITTEN_DATE") echo "selected"; ?> >�ۼ���</option>
							<option value="IN_DATE" <? if ($order_field == "IN_DATE") echo "selected"; ?> >�Ա���</option>
						</select>&nbsp;&nbsp;
						<input type='radio' class="" name='order_str' value='ASC' <? if (($order_str == "ASC") || ($order_str == "")) echo " checked"; ?> > �������� &nbsp;
						<input type='radio' name='order_str' value='DESC' <? if ($order_str == "DESC") echo " checked"; ?>> ��������
					</td>
					<th>�˻�����</th>
					<td colspan="2">
						
						<select name="nPageSize" style="width:74px;">
							<option value="20" <? if ($nPageSize == "20") echo "selected"; ?> >20����</option>
							<option value="50" <? if ($nPageSize == "50") echo "selected"; ?> >50����</option>
							<option value="100" <? if ($nPageSize == "100") echo "selected"; ?> >100����</option>
							<option value="200" <? if ($nPageSize == "200") echo "selected"; ?> >200����</option>
							<option value="300" <? if ($nPageSize == "300") echo "selected"; ?> >300����</option>
						</select>&nbsp;
						<select name="search_field" style="width:84px;">
							<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >���հ˻�</option>
							<option value="BIZ_NO" <? if ($search_field == "BIZ_NO") echo "selected"; ?> >����� ��ȣ</option>
							<option value="CP_NM" <? if ($search_field == "CP_NM") echo "selected"; ?> >��ȣ</option>
							<option value="CF_CODE" <? if ($search_field == "CF_CODE") echo "selected"; ?> >���ι�ȣ</option>
							
						</select>&nbsp;

						<input type="text" value="<?=$search_str?>" name="search_str" size="12" class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();"/>
						
						<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
					</td>
					
				</tr>
			</table>
			<div class="sp20"></div>
			�� <?=number_format($nListCnt)?> ��
			<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table" border="0">
				<colgroup>
					<col width="2%">
					<col width="10%">
					<col width="5%">
					<col width="8%">
					<col width="9%">
					<col width="8%">
					<col width="*">
					<col width="8%">
					<col width="8%">
					<col width="9%">
					<col width="7%">
					<col width="7%">
					<col width="7%">
				</colgroup>
				<thead>
					<tr>
						<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
						<th>����</th>
						<th>����</th>
						<th>��������</th>
						<th>���ι�ȣ</th>
						<th>����ڹ�ȣ</th>
						<th>��ȣ</th>
						<th>��������</th>
						<th>�ۼ�����</th>
						<th>�հ��</th>
						<th>�Աݾ�</th>
						<th>�ܾ�</th>
						<th class="end">�������</th>
						<!--
						<th><?= makeAdminInfoByMDSelectBox($conn,"sale_adm_no"," style='width:70px;' ","�������","", $sale_adm_no) ?></th>
						-->
					</tr>
				</thead>
				<tbody>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {

						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$CF_NO			= SetStringFromDB($arr_rs[$j]["CF_NO"]);
							$CF_TYPE		= SetStringFromDB($arr_rs[$j]["CF_TYPE"]);
							$CF_INOUT		= SetStringFromDB($arr_rs[$j]["CF_INOUT"]);
							$CF_CODE		= SetStringFromDB($arr_rs[$j]["CF_CODE"]);
							$OP_CP_NO		= SetStringFromDB($arr_rs[$j]["OP_CP_NO"]);
							$ACCOUNT_CP_NO	= SetStringFromDB($arr_rs[$j]["ACCOUNT_CP_NO"]);
							$SALE_CP_NO		= SetStringFromDB($arr_rs[$j]["SALE_CP_NO"]);
							$BIZ_NO			= SetStringFromDB($arr_rs[$j]["BIZ_NO"]);
							$CP_NM			= SetStringFromDB($arr_rs[$j]["CP_NM"]);
							$OUT_DATE		= SetStringFromDB($arr_rs[$j]["OUT_DATE"]);
							$WRITTEN_DATE	= SetStringFromDB($arr_rs[$j]["WRITTEN_DATE"]);
							$IN_DATE		= SetStringFromDB($arr_rs[$j]["IN_DATE"]);
							$CASH			= SetStringFromDB($arr_rs[$j]["CASH"]);
							
							$SUPPLY_PRICE	= SetStringFromDB($arr_rs[$j]["SUPPLY_PRICE"]);
							$SURTAX			= SetStringFromDB($arr_rs[$j]["SURTAX"]);
							$TOTAL_PRICE	= SetStringFromDB($arr_rs[$j]["TOTAL_PRICE"]);

							$SALE_ADM_NO	= SetStringFromDB($arr_rs[$j]["SALE_ADM_NO"]);
							$MATCH_TF		= SetStringFromDB($arr_rs[$j]["MATCH_TF"]);
							$CHECK_TF		= SetStringFromDB($arr_rs[$j]["CHECK_TF"]);

							
							
							$OP_CP_NM		= getCompanyNameWithNoCode($conn, $OP_CP_NO);
							$SALE_ADM_NM	= getAdminName($conn, $SALE_ADM_NO); 

							$CF_TYPE = getDcodeName($conn, 'CASH_STATEMENT_TYPE', $CF_TYPE);
							
							
							$arr_cp = selectCompany($conn, $SALE_CP_NO);
							if(sizeof($arr_cp) > 0) { 
								$SALE_CP_NM = "[".$arr_cp[0]["CP_CODE"]."] ".$arr_cp[0]["CP_NM"]." ".$arr_cp[0]["CP_NM2"];
							} else { 
								$SALE_CP_NM = "";
							}
							
							
							if($OUT_DATE <> "0000-00-00")
								$OUT_DATE		= date("Y-m-d",strtotime($OUT_DATE));
							else 
								$OUT_DATE = "";
							if($WRITTEN_DATE <> "0000-00-00")
								$WRITTEN_DATE	= date("Y-m-d",strtotime($WRITTEN_DATE));
							else 
								$WRITTEN_DATE = "";

							/*
							if($IN_DATE <> "0000-00-00")
								$IN_DATE		= date("Y-m-d",strtotime($IN_DATE));
							else 
								$IN_DATE = "";
							*/

							$str_row_class = "";

							if($MATCH_TF == "Y")
								$str_row_class .= " row_matched ";
						   
							if($CHECK_TF == "Y")
								$str_row_class .= " row_checked ";
								

				?>
					<tr height="40" class="<?=$str_row_class?>">
						<td><input type="checkbox" name="chk_no[]" value="<?=$CF_NO?>" data-biz_no="<?= $BIZ_NO ?>" data-total_price="<?=$TOTAL_PRICE?>" /></td>
						<td class="modeual_nm"><?= $OP_CP_NM ?></td>
						<td><?=$CF_INOUT?></td>
						<td><?=$CF_TYPE?></td>
						<td><?= $CF_CODE ?></td>
						<td><?= $BIZ_NO ?></td>
						<td class="modeual_nm"><?=$CP_NM?><?if($SALE_CP_NO > 0) echo "<br/>".$SALE_CP_NM;?></td>
						<td><?= $OUT_DATE ?></td>
						<td><?= $WRITTEN_DATE ?></td>
						

						<td class="price"><?= getSafeNumberFormatted($TOTAL_PRICE) ?></td>
						<td>
							<?
								if($CASH == 0) {
								
									if($CF_INOUT == "����" && $TOTAL_PRICE > 0) { 
							?>
								<input type="button" name="b" value=" ���� " onclick="js_input_cash('<?= $CF_NO ?>','<?= $BIZ_NO ?>','<?= $TOTAL_PRICE ?>');"/>
							<?
									}
								} else { 
							?>
								<?=number_format($CASH)?>
							<?
								}
							?>
						</td>
						<td>
							<?
								if($CASH == 0) {
									$REMAIN = "";
							?>
								<?=$REMAIN?>
							<?
								} else { 

									$REMAIN = $TOTAL_PRICE - $CASH;
							?>
								<?=number_format($REMAIN)?>
							<?
								}
							?>
						</td>
						<td><?= $SALE_ADM_NM ?></td>
					</tr>
				<?			
								}

								for ($j = 0 ; $j < sizeof($arr_rs_sum); $j++) {
									$SUM_SUPPLY_PRICE	= SetStringFromDB($arr_rs_sum[$j]["SUM_SUPPLY_PRICE"]);
									$SUM_SURTAX			= SetStringFromDB($arr_rs_sum[$j]["SUM_SURTAX"]);
									$SUM_TOTAL_PRICE	= SetStringFromDB($arr_rs_sum[$j]["SUM_TOTAL_PRICE"]);
				?>

					<tr height="40" class="row_period">
						<td></td>
						<td><b>�հ� :</b></td>
						<td colspan="7"></td>
						<td class="price"><?= getSafeNumberFormatted($SUM_TOTAL_PRICE) ?></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				<?
								}
							} else { 
						?> 
							<tr>
								<td align="center" height="50"  colspan="13">�����Ͱ� �����ϴ�. </td>
							</tr>
						<? 
							}
						?>
				</tbody>
			</table>
			<div style="width: 95%; text-align: right; margin: 10px 0 10px 0;">
				<input type="button" name="aa" value=" Ȯ�� " class="btntxt" onclick="js_check_tf('Y');">
				<input type="button" name="aa" value=" ��� " class="btntxt" onclick="js_check_tf('N');">
				<input type="button" name="aa" value="������ ���� ����" class="btntxt" onclick="js_delete();">
			</div>					
			
				<!-- --------------------- ������ ó�� ȭ�� START -------------------------->
				<?
					# ==========================================================================
					#  ����¡ ó��
					# ==========================================================================
					if (sizeof($arr_rs) > 0) {
						#$search_field		= trim($search_field);
						#$search_str			= trim($search_str);
						$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str;
						$strParam = $strParam."&op_cp_no=".$op_cp_no."&account_cp_no=".$account_cp_no."&sale_cp_no=".$sale_cp_no."&sale_adm_no=".$sale_adm_no."&search_date_type=".$search_date_type."&start_date=".$start_date."&end_date=".$end_date."&cf_inout=".$cf_inout."&cf_type=".$cf_type."&has_in_cash=".$has_in_cash."&match_tf=".$match_tf;

				?>
				<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
				<?
					}
				?>
			<div class="sp30"></div>
		<!-- // E: mwidthwrap -->
			</div>
			<!-- // E: mwidthwrap -->

		</td>
	</tr>
	</table>
	<div style="display:scroll;position:fixed;bottom:10px;right:10px;padding:10px;border:1px solid black;background-color:#fff;">
		
		<b>�Ա�ó��</b>&nbsp;
		��ü : 
		<input type="text" class="autocomplete_off" style="width:200px" placeholder="��ü(��/�ڵ�) �Է��� ���͸� �����ּ���" name="txt_cp_type" value="" />
		<input type="hidden" name="cp_type" value="">

		<script>
			$(function(){

				$("input[name=txt_cp_type]").keydown(function(e){

					if(e.keyCode==13) { 

						var keyword = $(this).val();
						if(keyword == "") { 
							$("input[name=cp_type]").val('');
						} else { 
						
							$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('�Ǹ�,�ǸŰ���') +"&term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE,BIZ_NO", function(data) {
								if(data.length == 1) { 
									
									js_selecting_company("txt_cp_type", data[0].label, "cp_type", data[0].id);

								} else if(data.length > 1){ 
									NewWindow("../company/pop_company_searched_list.php?con_cp_type=�Ǹ�,�ǸŰ���&search_str="+keyword + "&target_name=txt_cp_type&target_value=cp_type",'pop_company_searched_list2','950','650','YES');

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
					
					//alert(target_name + " " + cp_nm  + " " + target_value + " " + cp_no);
					$("[name="+target_name+"]").val(cp_nm);
					$("[name="+target_value+"]").val(cp_no);
				});
			}
		</script>
		�Ա��� : 
		<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="inout_date" value="<?=$inout_date?>" maxlength="10"/>
		<label><input type = 'radio' name= 'inout_method' value='����'>����</label>
		<label><input type = 'radio' name= 'inout_method' value='����'>����</label>
		<?=makeCompanySelectBoxAsCpNoWithName($conn, '����', 'bank_no', '')?>
		<label><input type = 'radio' name= 'inout_method' value='ī��'>ī��</label>
		<?=makeCompanySelectBoxAsCpNoWithName($conn, 'ī��', 'card_no', '')?>
		�Աݾ� : 
		<input type="text" name="unit_price" value=""/>
		<input type = 'hidden' name= 'inout_type' value='�Ա�'/>

		<input type="button" name="" onclick="js_save();" value=" �Ա�ó���� "/>
		<a href="#">�� ����</a>
	</div>
	<script type="text/javascript">
		$(function(){
			/*
			$("[name='chk_no[]']").click(function(){
				if($(this).is(":checked")) { 
					
					var biz_no = $(this).data("biz_no");
					var total_price = $(this).data("total_price");
						
					$("input[name=txt_cp_type]").val(biz_no);

					var e = jQuery.Event("keydown");
					e.which = 13; // # Some key code value
					e.keyCode = 13;
					$("input[name=txt_cp_type]").trigger(e);
					
					$("input[name=unit_price]").val(total_price);

				}
			});
			*/

			//���� ��� ���õǰ�	
			$("select[name=bank_no]").change(function(){
				$("input[name=inout_method][value=����]").prop("checked", true);
			});
		});
	
	</script>
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