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
	$menu_right = "CF011"; // 메뉴마다 셋팅 해 주어야 합니다

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
			case "통장": 
						if($inout_type == "입금") 
							$inout_type_code = "RX05";
						else
							$inout_type_code = "LX06";
							
						
						$name = getCompanyName($conn, $bank_no);
						$surtax = 0;
						$result	= insertCompanyLedger($conn, $cp_type, $inout_date, $inout_type_code, $goods_no, $name, $qty, $unit_price, $bank_no, $surtax, "", "", $memo, $reserve_no, $order_goods_no, "통장", $rgn_no, $s_adm_no, null);
						break;
			case "대체": 
						if($inout_type == "입금") 
							$inout_type_code = "RX05";
						else
							$inout_type_code = "LX06";

						$name = getCompanyName($conn, $to_cp_no);
						$surtax = 0;
						$result	= insertCompanyLedger($conn, $cp_type, $inout_date, $inout_type_code, $goods_no, $name, $qty, $unit_price, $to_cp_no, $surtax, "", "", $memo, $reserve_no, $order_goods_no, "대체", $rgn_no, $s_adm_no, null);
						break;
			case "카드": 
						if($inout_type == "입금") 
							$inout_type_code = "RX05";
						else
							$inout_type_code = "LX06";

						$name = getCompanyName($conn, $card_no);
						$result	= insertCompanyLedger($conn, $cp_type, $inout_date, $inout_type_code, $goods_no, $name, $qty, $unit_price + $surtax, $card_no, $surtax, "", "", $memo, $reserve_no, $order_goods_no, "카드", $rgn_no, $s_adm_no, null);
						break;
			default:
						if($inout_type == "입금") 
							$inout_type_code = "RW02";
						else
							$inout_type_code = "LW04";
						$name = "< 현금 ".$inout_type." >";
						$to_cp_no = null;
						$surtax = 0;
						$result	= insertCompanyLedger($conn, $cp_type, $inout_date, $inout_type_code, $goods_no, $name, $qty, $unit_price, $to_cp_no, $surtax, "", "", $memo, $reserve_no, $order_goods_no, "현금", $rgn_no, $s_adm_no, null);
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

	alert("입금처리 되었습니다.");
	document.location = "<?=$_SERVER[PHP_SELF]?>?<?=$strParam?>";

</script>
<?
		} else { 
?>
<script type="text/javascript">
	
	alert('에러 발생 되었습니다. 시스템 담당자랑 상의해주세요.');

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
		// 입금이 원장에 있다면 굳이 수정할 필요 없음
		var frm = document.frm;

		frm.cf_no.value = cf_no;
		
		frm.target = "";
		frm.method = "get";
		frm.action = "cash_flow_statement_write.php";
		frm.submit();
		*/
	}


	// 조회 버튼 클릭 시 
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

		bDelOK = confirm('선택한 내역을 삭제 하시겠습니까?');
		
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
			alert('조회할 계좌가 선택되지 않았습니다.');
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
		NewWindow(url, '세금계산서입력', '950', '513', 'YES');
		
	}

	function js_save() { 
		
		/*
		var frm = document.frm;
		frm.target = "_blank";
		frm.action = "company_ledger_write_method.php?cp_type=" + frm.cp_type.value + "&unit_price=" + frm.unit_price.value + "&inout_type=입금";
		frm.submit();
		*/
		var frm = document.frm;
		
		if (isNull(frm.cp_type.value)) {
			alert('업체를 선택해주세요.');
			frm.txt_cp_type.focus();
			return ;		
		}

		if (isNull(frm.inout_date.value)) {
			alert('기장일을 선택해주세요.');
			frm.inout_date.focus();
			return ;		
		}

		if (isNull(frm.inout_type.value)) {
			alert('입금/지급 선택해주세요.');
			frm.inout_type.focus();
			return ;		
		}		

		if (isNull(frm.inout_method.value)) {
			alert('입력 방식을 선택해주세요.');
			frm.inout_method.focus();
			return ;		
		}

		if (isNull(frm.unit_price.value)) {
			alert('기장액을 선택해주세요.');
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

				<h2>자금 총괄표</h2>
				<div class="btnright" style="margin:0 0 5px 0;">
					<input type="button" name="cc" value=" 계산서 개별 입력 " class="btntxt" onclick="js_write();">
					<input type="button" name="cc" value=" 전자세금 계산서 일괄 입력 (엑셀)" class="btntxt" onclick="js_open_pop_cash_sheet();">
					<input type="button" name="cc" value=" 거래원장 <-> 계산서 매칭확인 " class="btntxt" onclick="js_sync_cash_sheet();">
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
							<option value="out_date" <? if ($search_date_type == "out_date" || $search_date_type == "") echo "selected" ?>>발행일자</option>
							<option value="written_date" <? if ($search_date_type == "written_date") echo "selected" ?>>작성일자</option>
							<option value="in_date" <? if ($search_date_type == "in_date") echo "selected" ?>>입금일</option>
							<option value="reg_date" <? if ($search_date_type == "reg_date") echo "selected" ?>>등록일</option>
						</select>
					</th>
					<td colspan="3">
						<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" id="start_date" name="start_date" value="<?=$start_date?>" maxlength="10"/>
						 ~ 
						<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" id="end_date" name="end_date" value="<?=$end_date?>" maxlength="10"/>
						&nbsp;
						<input type="button" value="전월" onclick="javascript:js_search_date_by_code('prev_month');"/>
						<input type="button" value="전주" onclick="javascript:js_search_date_by_code('prev_week');"/>
						<input type="button" value="전일" onclick="javascript:js_search_date_by_code('prev_day');"/>
						<input type="button" value="오늘" onclick="javascript:js_search_date_by_code('today');"/>
						<input type="button" value="금주" onclick="javascript:js_search_date_by_code('this_week');"/>
						<input type="button" value="금월" onclick="javascript:js_search_date_by_code('this_month');"/>
						
					</td>
					<td align="right">
					</td>
				</tr>
				<!--
				<tr>
					<th>통장명</th>
					<td colspan="4">
						<input type="text" class="autocomplete_off" style="width:80%" placeholder="업체(명/코드) 입력후 엔터를 눌러주세요" name="txt_account_cp_no" value="<?=getCompanyAutocompleteTextBox($conn,'',$account_cp_no)?>" />
							<script>
								$(function(){

									$("input[name=txt_account_cp_no]").keydown(function(e){

										if(e.keyCode==13) { 

											var keyword = $(this).val();
											if(keyword == "") { 
												$("input[name=account_cp_no]").val('');
												js_search();
											} else { 
											
												$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('통장') +"&term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
													if(data.length == 1) { 
														
														js_selecting_company("txt_account_cp_no", data[0].label, "account_cp_no", data[0].id);

													} else if(data.length > 1){ 
														NewWindow("../company/pop_company_searched_list.php?con_cp_type=통장&search_str="+keyword + "&target_name=txt_account_cp_no&target_value=account_cp_no",'pop_company_searched_list','950','650','YES');

													} else 
														alert("검색결과가 없습니다.");
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
								echo makeGenericSelectBox($conn, $arr_account_cp, 'account_cp_no', '100', "선택", "", $account_cp_no, "ACCOUNT_CP_NO", "CP_NM");
							} else { 
						?>
						<input type="hidden" name="account_cp_no" value="<?=$account_cp_no?>">
						<? } ?>
					</td>
					
				</tr>
				-->
				<tr>
					<!--
					<th>업체명</th>
					<td>
						<input type="text" class="autocomplete_off" style="width:90%" placeholder="업체(명/코드) 입력후 엔터를 눌러주세요" name="txt_sale_cp_no" value="<?=getCompanyAutocompleteTextBox($conn,'',$sale_cp_no)?>" />
						<input type="hidden" name="sale_cp_no" value="<?=$sale_cp_no?>">

						<script>
							$(function(){

								$("input[name=txt_sale_cp_no]").keydown(function(e){

									if(e.keyCode==13) { 

										var keyword = $(this).val();
										if(keyword == "") { 
											$("input[name=sale_cp_no]").val('');
										} else { 
										
											$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('판매,구매,판매공급') +"&term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
												if(data.length == 1) { 
													
													js_selecting_company("txt_sale_cp_no", data[0].label, "sale_cp_no", data[0].id);

												} else if(data.length > 1){ 
													NewWindow("../company/pop_company_searched_list.php?con_cp_type=판매,구매,판매공급&search_str="+keyword + "&target_name=txt_sale_cp_no&target_value=sale_cp_no",'pop_company_searched_list2','950','650','YES');

												} else 
													alert("검색결과가 없습니다.");
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
					
					<th>필터</th>
					<td colspan="3">
						<label>구분 : </label>
						<?
							$arr_op = getOperatingCompany($conn, '');
							echo makeGenericSelectBox($conn, $arr_op, 'op_cp_no', '100', "선택", "", $op_cp_no, "CP_NO", "CP_NM");
						?>
						<label>종류 : </label>
							<select name="cf_inout">
								<option value="" <?if($cf_inout == "") echo "selected";?>>전체</option>
								<option value="매출" <?if($cf_inout == "매출") echo "selected";?>>매출</option>
								<option value="매입" <?if($cf_inout == "매입") echo "selected";?>>매입</option>
							</select>
						<label>승인종류 : </label>
						<?=makeSelectBox($conn, 'CASH_STATEMENT_TYPE', 'cf_type','100','전체','',$cf_type)?>
						<label>입금여부 : </label>
						<select name="has_in_cash">
							<option value="" <?if($has_in_cash == "") echo "selected";?>>전체</option>
							<option value="N" <?if($has_in_cash == "N") echo "selected";?>>입금없음</option>
							<option value="Y" <?if($has_in_cash == "Y") echo "selected";?>>입금됨</option>
						</select>
						<label>원장매치 : </label>
						<select name="match_tf">
							<option value="" <?if($match_tf == "") echo "selected";?>>전체</option>
							<option value="N" <?if($match_tf == "N") echo "selected";?>>매치없음</option>
							<option value="Y" <?if($match_tf == "Y") echo "selected";?>>매치</option>
						</select>
						<label>영업담당자 :</label>
						<?= makeAdminInfoByMDSelectBox($conn,"sale_adm_no"," style='width:70px;' ","전체","", $sale_adm_no) ?>
					</td>
					<td align="right">
						<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>
					</td>
				</tr>
				<tr>
					<th>정렬</th>
					<td>
						<select name="order_field" style="width:94px;">
							<option value="OUT_DATE" <? if ($order_field == "OUT_DATE") echo "selected"; ?> >발행일</option>
							<option value="WRITTEN_DATE" <? if ($order_field == "WRITTEN_DATE") echo "selected"; ?> >작성일</option>
							<option value="IN_DATE" <? if ($order_field == "IN_DATE") echo "selected"; ?> >입금일</option>
						</select>&nbsp;&nbsp;
						<input type='radio' class="" name='order_str' value='ASC' <? if (($order_str == "ASC") || ($order_str == "")) echo " checked"; ?> > 오름차순 &nbsp;
						<input type='radio' name='order_str' value='DESC' <? if ($order_str == "DESC") echo " checked"; ?>> 내림차순
					</td>
					<th>검색조건</th>
					<td colspan="2">
						
						<select name="nPageSize" style="width:74px;">
							<option value="20" <? if ($nPageSize == "20") echo "selected"; ?> >20개씩</option>
							<option value="50" <? if ($nPageSize == "50") echo "selected"; ?> >50개씩</option>
							<option value="100" <? if ($nPageSize == "100") echo "selected"; ?> >100개씩</option>
							<option value="200" <? if ($nPageSize == "200") echo "selected"; ?> >200개씩</option>
							<option value="300" <? if ($nPageSize == "300") echo "selected"; ?> >300개씩</option>
						</select>&nbsp;
						<select name="search_field" style="width:84px;">
							<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >통합검색</option>
							<option value="BIZ_NO" <? if ($search_field == "BIZ_NO") echo "selected"; ?> >사업자 번호</option>
							<option value="CP_NM" <? if ($search_field == "CP_NM") echo "selected"; ?> >상호</option>
							<option value="CF_CODE" <? if ($search_field == "CF_CODE") echo "selected"; ?> >승인번호</option>
							
						</select>&nbsp;

						<input type="text" value="<?=$search_str?>" name="search_str" size="12" class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();"/>
						
						<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
					</td>
					
				</tr>
			</table>
			<div class="sp20"></div>
			총 <?=number_format($nListCnt)?> 건
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
						<th>구분</th>
						<th>종류</th>
						<th>승인종류</th>
						<th>승인번호</th>
						<th>사업자번호</th>
						<th>상호</th>
						<th>발행일자</th>
						<th>작성일자</th>
						<th>합계액</th>
						<th>입금액</th>
						<th>잔액</th>
						<th class="end">영업사원</th>
						<!--
						<th><?= makeAdminInfoByMDSelectBox($conn,"sale_adm_no"," style='width:70px;' ","영업사원","", $sale_adm_no) ?></th>
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
								
									if($CF_INOUT == "매출" && $TOTAL_PRICE > 0) { 
							?>
								<input type="button" name="b" value=" 선택 " onclick="js_input_cash('<?= $CF_NO ?>','<?= $BIZ_NO ?>','<?= $TOTAL_PRICE ?>');"/>
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
						<td><b>합계 :</b></td>
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
								<td align="center" height="50"  colspan="13">데이터가 없습니다. </td>
							</tr>
						<? 
							}
						?>
				</tbody>
			</table>
			<div style="width: 95%; text-align: right; margin: 10px 0 10px 0;">
				<input type="button" name="aa" value=" 확인 " class="btntxt" onclick="js_check_tf('Y');">
				<input type="button" name="aa" value=" 취소 " class="btntxt" onclick="js_check_tf('N');">
				<input type="button" name="aa" value="선택한 내역 삭제" class="btntxt" onclick="js_delete();">
			</div>					
			
				<!-- --------------------- 페이지 처리 화면 START -------------------------->
				<?
					# ==========================================================================
					#  페이징 처리
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
		
		<b>입금처리</b>&nbsp;
		업체 : 
		<input type="text" class="autocomplete_off" style="width:200px" placeholder="업체(명/코드) 입력후 엔터를 눌러주세요" name="txt_cp_type" value="" />
		<input type="hidden" name="cp_type" value="">

		<script>
			$(function(){

				$("input[name=txt_cp_type]").keydown(function(e){

					if(e.keyCode==13) { 

						var keyword = $(this).val();
						if(keyword == "") { 
							$("input[name=cp_type]").val('');
						} else { 
						
							$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('판매,판매공급') +"&term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE,BIZ_NO", function(data) {
								if(data.length == 1) { 
									
									js_selecting_company("txt_cp_type", data[0].label, "cp_type", data[0].id);

								} else if(data.length > 1){ 
									NewWindow("../company/pop_company_searched_list.php?con_cp_type=판매,판매공급&search_str="+keyword + "&target_name=txt_cp_type&target_value=cp_type",'pop_company_searched_list2','950','650','YES');

								} else 
									alert("검색결과가 없습니다.");
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
		입금일 : 
		<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="inout_date" value="<?=$inout_date?>" maxlength="10"/>
		<label><input type = 'radio' name= 'inout_method' value='현금'>현금</label>
		<label><input type = 'radio' name= 'inout_method' value='통장'>통장</label>
		<?=makeCompanySelectBoxAsCpNoWithName($conn, '통장', 'bank_no', '')?>
		<label><input type = 'radio' name= 'inout_method' value='카드'>카드</label>
		<?=makeCompanySelectBoxAsCpNoWithName($conn, '카드', 'card_no', '')?>
		입금액 : 
		<input type="text" name="unit_price" value=""/>
		<input type = 'hidden' name= 'inout_type' value='입금'/>

		<input type="button" name="" onclick="js_save();" value=" 입금처리로 "/>
		<a href="#">▲ 위로</a>
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

			//통장 방식 선택되게	
			$("select[name=bank_no]").change(function(){
				$("input[name=inout_method][value=통장]").prop("checked", true);
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