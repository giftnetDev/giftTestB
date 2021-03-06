<?
ini_set('memory_limit',-1);
session_start();
?>
<?

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "CF012"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/confirm/confirm.php";
	require "../../_classes/biz/company/company.php";

#====================================================================
# Request Parameter
#====================================================================

	if($mode == "UPDATE_PREV") { 

		$row_cnt = count($chk_no);

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_cp_no = $chk_no[$k];
			$result = updateAccountReceivableReport_MovePrev($conn, $str_cp_no, $s_adm_no);
		}

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<script>
	alert('수정 되었습니다');
</script>
</head>
</html>
<?
	}


	if ( count($_GET) == 0 && count($_POST) == 0 ) { 
		if($s_adm_md_tf == "Y")
			$con_sale_adm_no = $s_adm_no;
	}

	$day_0 = date("Y-m-d",strtotime("0 month"));
	$day_1 = date("Y-m-d",strtotime("-1 day"));
	$day_7 = date("Y-m-d",strtotime("-7 day"));
	$day_31 = date("Y-m-d",strtotime("-1 month"));

	$d = new DateTime('first day of previous month');
	$prev_first_date = $d->format("Y-m-d");

	if ($start_date == "") {
		$d = new DateTime('first day of this month');
		$start_date = $d->format("Y-m-d");
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));
	} else {
		$end_date = trim($end_date);
	}

	if ( count($_GET) == 0 && count($_POST) == 0 ) { 
		if(date("d",strtotime("0 month")) >= 15)
			$chk_prev_month = "Y";
	}

	 #List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);

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

	$con_cp_type = "'판매','판매공급'";
	$filter = array('con_sale_adm_no' => $con_sale_adm_no, 'con_cp_type' => $con_cp_type, 'con_ad_type' => $con_ad_type, 'chk_prev_month' => $chk_prev_month);
	
	$arr_rs = listAccountReceivableSaleReport($conn, $start_date, $end_date, $filter, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize);
	$nListCnt = sizeof($arr_rs);
	$arr_rs_sum = SumAccountReceivableSaleReport($conn, $start_date, $end_date, $filter);

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

  $(function(){
	js_make_select();
  });
</script>
<script>
	var contents = null;
	$(function() {
		$('.autocomplete_off').attr('autocomplete', 'off');
	});
	
	$(function(){
		contents = $("#mwidthwrap").html();
		fixedHeader();
	});
	function fixedHeader(){
		$('table.fixed_header_table').floatThead({
			position: 'fixed'
		});
	}

$(function(){
	$("input[name='chk_no[]'], input[name=all_chk]").click(function(){
		calcSelectedLedger();
	});

	function calcSelectedLedger() {
		var total_withdraw = 0;
		var total_deposit = 0;
		var total_balance = 0;
		var total_prev_balance = 0;
		var total_prev_0 = 0;
		var total_prev_1 = 0;
		var total_prev_2 = 0;
		var total_prev_3 = 0;

		$("input[name='chk_no[]']").each(function(){
			if($(this).prop('checked') == true) {
				var withdraw 		= $(this).closest("tr").find("td.row_withdraw").data("value");
				var deposit 		= $(this).closest("tr").find("span.row_deposit").data("value");
				var balance 		= $(this).closest("tr").find("td.row_balance").data("value");
				var prev_balance 	= $(this).closest("tr").find("td.row_prev_balance").data("value");
				var prev_0 			= $(this).closest("tr").find("input[name='row_prev_0']").val();
				var prev_1 			= $(this).closest("tr").find("input[name='row_prev_1']").val();
				var prev_2 			= $(this).closest("tr").find("input[name='row_prev_2']").val();
				var prev_3 			= $(this).closest("tr").find("input[name='row_prev_3']").val();
				
				if(prev_0 == null) prev_0 = 0;
				if(prev_1 == null) prev_1 = 0;
				if(prev_2 == null) prev_2 = 0;
				if(prev_3 == null) prev_3 = 0;
				
				total_withdraw 		+= parseFloat(withdraw);
				total_deposit 		+= parseFloat(deposit);
				total_balance 		+= parseFloat(balance);
				total_prev_balance 	+= parseFloat(prev_balance);
				total_prev_0 		+= parseFloat(prev_0);
				total_prev_1 		+= parseFloat(prev_1);
				total_prev_2 		+= parseFloat(prev_2);
				total_prev_3 		+= parseFloat(prev_3);
			}
		});


		if(total_withdraw != 0 || total_deposit != 0 || total_balance != 0 || total_prev_balance != 0 || total_prev_0 != 0 || total_prev_1 != 0 || total_prev_2 != 0 || total_prev_3 != 0) {
			$(".selected").show();
			$("#total_withdraw").html(numberFormat(total_withdraw));
			$("#total_deposit").html(numberFormat(total_deposit));
			$("#total_balance").html(numberFormat(total_balance));
			$("#total_prev_balance").html(numberFormat(total_prev_balance));
			$("#total_prev_0").html(numberFormat(total_prev_0));
			$("#total_prev_1").html(numberFormat(total_prev_1));
			$("#total_prev_2").html(numberFormat(total_prev_2));
			$("#total_prev_3").html(numberFormat(total_prev_3));
			$("#grand_total_prev").html(numberFormat(total_prev_0+total_prev_1+total_prev_2+total_prev_3));
		} else {
			$(".selected").hide();
			$("#total_withdraw").html("");
			$("#total_deposit").html("");
			$("#total_surtax").html("");
			$("#total_withdraw").html("");
			$("#total_deposit").html("");
			$("#total_balance").html("");
			$("#total_prev_balance").html("");
			$("#total_prev_0").html("");
			$("#total_prev_1").html("");
			$("#total_prev_2").html("");
			$("#total_prev_3").html("");
			$("#grand_total_prev").html("");
		}
	}
});

function js_select_screen(){
	var report_time_no = $("#selected_screen").val();
	$.ajax({
		url: '/manager/ajax_processing.php',
		dataType: 'text',
		type: 'post',
		data : {
		'mode': "SELECT_SCREEN",
		'report_time_no': report_time_no
		},
		success: function(response) {
			if(response != ""){
				alert("로드되었습니다.");
				$("#mwidthwrap").html(response);
				$("#isLoding").html("(불러온 화면)");
				fixedHeader();
				js_make_select();
			} else{
				alert("실패하였습니다.");
			}
		}
	});
}

function js_delete_screen(){
	var report_time_no = $("#selected_screen").val();
	$.ajax({
		url: '/manager/ajax_processing.php',
		dataType: 'text',
		type: 'post',
		data : {
		'mode': "DELETE_SCREEN",
		'report_time_no': report_time_no
		},
		success: function(response) {
			if(response == "true"){
				alert("삭제되었습니다.");
				js_make_select();
			}
			else
				alert("실패하였습니다.");
		}
	});
}

function js_insert_screen(){
	var sales_adm_no	= "<?=$con_sale_adm_no?>";
	var s_adm_no		= "<?=$s_adm_no?>";
	var memo			= $("#screen_memo").val();
	
	$.ajax({
		url: '/manager/ajax_processing.php',
		dataType: 'text',
		type: 'post',
		data : {
		'mode': "INSERT_SCREEN",
		'contents': contents,
		'sales_adm_no': sales_adm_no,
		's_adm_no': s_adm_no,
		'memo': memo
		},
		success: function(response) {
			if(response == "true"){
				alert("저장되었습니다.");
				js_make_select();
			}
			else
				alert("실패하였습니다.");
		}
	});
}

function js_make_select(){
	var sales_adm_no = $("select[name='con_sale_adm_no']").val();
	$.ajax({
		url: '/manager/ajax_processing.php',
		dataType: 'text',
		type: 'post',
		data : {
		'mode': "MAKE_SELECT",
		'sales_adm_no': sales_adm_no
		},
		success: function(response) {
			// alert(response);
			$("#selected_screen").html(response);
		}
	});
}

</script>
<script language="javascript">

	// 조회 버튼 클릭 시 
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.method = "get";
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
		
		frm.target = "";
		frm.action = "<?=str_replace("report","excel_report",$_SERVER[PHP_SELF])?>";
		frm.submit();

	}


	function js_link_to_company_ledger(cp_no) {

		var frm = document.frm;
		
		//window.open("/manager/confirm/company_ledger_list.php?cp_type=" + cp_no + "&start_date=" + frm.start_date.value + "&end_date=" + frm.end_date.value ,'_blank');
		window.open("/manager/confirm/company_ledger_list.php?cp_type=" + cp_no + "&start_date=<?=$prev_first_date?>&end_date=<?=$day_0?>" ,'_blank');
		
	}

	function js_link_to_company_info(cp_no) {

		var frm = document.frm;
		
		window.open("/manager/company/company_write.php?rn=&cp_no=" + cp_no + "&mode=S" ,'_blank');
		
	}
	
	function js_prev_balance_list(cp_no, cp_nm) {

		var frm = document.frm;

		var chk_prev_month = (frm.chk_prev_month.checked ? "Y" : "N");

		var url = "pop_account_receivable_report.php?cp_no=" + cp_no + "&start_date=" + frm.start_date.value + "&cp_nm=" + cp_nm + "&chk_prev_month=" + chk_prev_month;
		NewWindow(url,'pop_account_receivable_report','820','400','YES');

	}
	
	function js_memo_view(cp_no) {
		var frm = document.frm;

		var chk_prev_month = (frm.chk_prev_month.checked ? "Y" : "N");
	
		var url = "popup_memo.php?cp_no=" + cp_no + "&chk_prev_month=" + chk_prev_month;
		NewWindow(url,'popup_memo','820','700','YES');

	}

	function js_update_prev() { 
		var frm = document.frm;

		bDelOK = confirm('선택한 업체의 이전 미수들을 이월하시겠습니까?');
		
		if (bDelOK==true) {
			
			frm.mode.value = "UPDATE_PREV";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}

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
</script>
<style>
	.row_color_lev1 {background-color:#EFEFEF; font-weight:bold;}
	.row_color_lev2 {background-color:#DFDFDF; font-weight:bold;}
	.only_tax {display:none; color:blue;} 
</style> 
</head>

<body id="admin">
<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="cl_no" value="">
<input type="hidden" name="report_time_no" value="">
<input type="hidden" name="memo" value="">
<input type="hidden" name="sales_adm_no" value="<?=$con_sale_adm_no?>">
<input type="hidden" name="reg_adm" value="<?=$s_adm_no?>">
<input type="hidden" name="mode" value="">
<input type="hidden" name="nPage" value="<?=$nPage?>">
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

				<h2>매출 미수 보고<span id="isLoding"></span></h2>
				<div class="btn_right">
					<label><input type="checkbox" name="chk_prev_month" value="Y" <?if($chk_prev_month == "Y") echo "checked";?>/>현재 기준 미수 (15일이후 자동체크)</label>
					<script type="text/javascript">
						$("[name=chk_prev_month]").click(function(){
							js_search();
						});
					</script>
				</div>
				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="10%" />
					<col width="37%" />
					<col width="10%" />
					<col width="37%" />
					<col width="6%" />
				</colgroup>
				<tr>
					<th>
						기간
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
						<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>
					</td>
				</tr>
				<tr>
					<th>영업사원</th>
					<td <? if($con_sale_adm_no == "") echo "colspan='3'";?>>
						<? if ($s_adm_md_tf != "Y") { ?>
							<?= makeAdminInfoByMDSelectBox($conn,"con_sale_adm_no"," style='width:70px;' ","전체","", $con_sale_adm_no) ?>
						<? } else { ?>
							<input type="hidden" name="con_sale_adm_no" value="<?=$con_sale_adm_no?>"/>
							<?=getAdminName($conn, $con_sale_adm_no)?>
						<? } ?>
					</td>
					<? if($con_sale_adm_no != "") { ?>
					<th>현재화면<br>저장/불러오기</th>
					<td>
						<select id="selected_screen">
							<option>선택</option>
						</select>
						<br>
						메모 : <input type="text" id="screen_memo" value=""> 
						&nbsp;
						<input type="button" value="불러오기" onclick="js_select_screen()" style="width:5em;"/>
						&nbsp;
						<input type="button" value="저장" onclick="js_insert_screen()" style="width:5em;"/>
						&nbsp;
						<input type="button" value="삭제" onclick="js_delete_screen()" style="width:5em;"/>
					</td>
					<? } ?>
					<td align="right">
					</td>
				</tr>
				<tr>
					
					<th>정렬</th>
					<td>
						<select name="order_field" style="width:84px;">
							<option value="EXCEPT_SALE" <? if ($order_field == "EXCEPT_SALE") echo "selected"; ?> >당월매출제외 미수</option>
							<option value="O.CP_NM" <? if ($order_field == "O.CP_NM") echo "selected"; ?> >업체명</option>
						</select>&nbsp;&nbsp;
						<input type='radio' class="" name='order_str' value='ASC' <? if ($order_str == "ASC") echo " checked"; ?>> 오름차순 &nbsp;
						<input type='radio' name='order_str' value='DESC' <? if (($order_str == "DESC")  || ($order_str == "")) echo " checked"; ?>> 내림차순  
					</td>
					<th>검색조건</th>
					<td>
						<select name="search_field" style="width:84px;">
							<option value="ALL" <? if ($search_field == "ALL" || $search_field == "") echo "selected"; ?> >통합검색</option>
							<option value="CP_CODE" <? if ($search_field == "CP_CODE") echo "selected"; ?> >업체코드</option>
							<option value="CP_NAME" <? if ($search_field == "CP_NAME") echo "selected"; ?> >업체명</option>
						</select>&nbsp;

						<input type="text" value="<?=$search_str?>" name="search_str" size="15" class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();" />
						<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
					</td>
					<td align="right">
						
					</td>
				</tr>
			</table>
			<div class="sp20"></div>
			<div>
				<span>총 <?=number_format($nListCnt)?> 건</span>
				<div style="float:right; margin-right:60px;"><label><input type="checkbox" id="show_only_tax" value="Y"/>계산서발행액</label></div>
			</div>
			<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table" border="0">

				<colgroup>
					<col width="2%" />
					<col width="5%" />
					<col width="*" />
					<col width="8%" />
					<col width="8%" />
					<col width="8%" />
					<col width="9%" />
					<? if($con_sale_adm_no == "") { ?>
					<col width="7%" />
					<? } else { ?>
					<col width="16%" />
					<? } ?>
					<col width="9%" />
				</colgroup>
				<thead>
				<tr>
					<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
					<th>업체코드</th>
					<th>업체명</th>
					<th>이월잔액</th>
					<th>매출액</th>
					<th>입금액</th>
					<?if($chk_prev_month == "Y") { ?>
						<th>잔액</th>
					<? } else { ?>
						<th>전월미수</th>
					<? } ?>
					<th>
						<? if($con_sale_adm_no <> "") { ?>
						비고
						<? } else { ?>
						영업담당
						<? } ?>
					</th>
					<th class="end">
						<select name="show_type">
							<option value="ledger">원장 조회</option>
							<option value="up_date">최종 수정일</option>
						</select>
					</th>
				
				</tr>
				</thead>
				<?
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							$CP_NO						= trim($arr_rs[$j]["CP_NO"]);
							$CP_TYPE					= trim($arr_rs[$j]["CP_TYPE"]);
							$CP_CODE					= trim($arr_rs[$j]["CP_CODE"]);
							$CP_NM						= trim($arr_rs[$j]["CP_NM"]);
							$CP_NM2						= trim($arr_rs[$j]["CP_NM2"]);
							$PREV_BALANCE				= trim($arr_rs[$j]["PREV_BALANCE"]);
							$SUM_SALES					= trim($arr_rs[$j]["SUM_SALES"]);
							$SUM_SALES_TAX				= trim($arr_rs[$j]["SUM_SALES_TAX"]);
							$SUM_COLLECT				= trim($arr_rs[$j]["SUM_COLLECT"]);
							$SUM_BALANCE				= trim($arr_rs[$j]["SUM_BALANCE"]);
							$EXCEPT_SALE				= trim($arr_rs[$j]["EXCEPT_SALE"]);
							$SALE_ADM_NO				= trim($arr_rs[$j]["SALE_ADM_NO"]);
							$MEMO						= trim($arr_rs[$j]["MEMO"]);
							$PREV_0						= trim($arr_rs[$j]["PREV_0"]);
							$PREV_1						= trim($arr_rs[$j]["PREV_1"]);
							$PREV_2						= trim($arr_rs[$j]["PREV_2"]);
							$PREV_3						= trim($arr_rs[$j]["PREV_3"]);
							$EXCEPT_TF					= trim($arr_rs[$j]["EXCEPT_TF"]);
							$UP_DATE					= trim($arr_rs[$j]["UP_DATE"]);

							$LATEST_SUM_COLLECT			= trim($arr_rs[$j]["LATEST_SUM_COLLECT"]);

							$str_row_class = "";
							if($UP_DATE <> "")
								if(date("Y-m",strtotime($UP_DATE)) != date("Y-m",strtotime("0 month")))  
									$str_row_class .= "background-color:#f44242;";
							
							if($EXCEPT_TF == "Y")
								$str_row_class .= "background-color:#dfdfdf;";
							
							$upMonth = "";
							$thisMonth = "";

							if($UP_DATE!="")
								$upMonth = date("m", strtotime($UP_DATE));
							else
								$upMonth = "";
							
							$thisMonth = date("m",strtotime("0 month"));
				?>
				<tr height="30" style="<?=$str_row_class?>">
					<td><input type="checkbox" name="chk_no[]" value="<?=$CP_NO?>"/></td>
					<td><a href="javascript:js_link_to_company_ledger('<?=$CP_NO?>')"><?=$CP_CODE?></a></td><!--업체코드-->
					<td class="modeual_nm"><a href="javascript:js_link_to_company_info('<?=$CP_NO?>');"><?=$CP_NM." ".$CP_NM2?></a></td><!--업체명-->
					<td class="price row_prev_balance" data-value="<?=$PREV_BALANCE?>"><?=getSafeNumberFormatted($PREV_BALANCE)?></td><!--이월잔액-->
					<td class="price">
						<span class="include_tax row_deposit" data-value="<?=$SUM_SALES?>"><?=getSafeNumberFormatted($SUM_SALES)?></span>
						<span class="only_tax"><?=getSafeNumberFormatted($SUM_SALES_TAX)?></span>
					</td><!--매출액-->
					<td class="price row_withdraw" data-value="<?=$SUM_COLLECT?>"><?=getSafeNumberFormatted($SUM_COLLECT)?></td><!--입금액-->
					<?if($chk_prev_month == "Y") { ?>
						<td class="price row_balance" data-value="<?=$SUM_BALANCE?>">
								<a href="javascript:js_prev_balance_list('<?=$CP_NO?>', '<?=$CP_NM." ".$CP_NM2?>')" style="font-weight:bold;"><?=getSafeNumberFormatted($SUM_BALANCE)?></a>
						</td><!--잔액-->

					<? } else { ?>
						<td class="price row_balance" data-value="<?=$SUM_BALANCE?>">
								<a href="javascript:js_prev_balance_list('<?=$CP_NO?>', '<?=$CP_NM." ".$CP_NM2?>')" style="font-weight:bold;"><?=getSafeNumberFormatted($EXCEPT_SALE)?></a>
						</td><!--잔액-->
					<? } ?>
					<td onclick="javascript:js_memo_view('<?=$CP_NO?>');">
						<? if($con_sale_adm_no <> "") { ?>
						<div style="text-align:right;">
						<?						
						    if($chk_prev_month == "Y" && $SUM_SALES > 0) {
								if($upMonth == $thisMonth && $PREV_0 == 0){
									//echo date("n월",strtotime("0 month"))." : ".getSafeNumberFormatted($PREV_0)."<br/>";
									echo "<input type='hidden' name='row_prev_0' value='".$PREV_0."'>";
									echo "<input type='hidden' name='test_prev_0' value='".$UP_DATE."'>";
								} else {
									if($PREV_0 == 0){
										echo date("n월",strtotime("0 month"))." : ".getSafeNumberFormatted($SUM_SALES)."<br/>";
										echo "<input type='hidden' name='row_prev_0' value='".$SUM_SALES."'>";
										echo "<input type='hidden' name='test_prev_0' value='".$UP_DATE."'>";
										echo "<input type='hidden' name='monthz' value='".$month."'>"; 
									}
									else{
										echo date("n월",strtotime("0 month"))." : ".getSafeNumberFormatted($PREV_0)."<br/>";
										echo "<input type='hidden' name='row_prev_0' value='".$PREV_0."'>";
										echo "<input type='hidden' name='test_prev_0' value='".$UP_DATE."'>";
										echo "<input type='hidden' name='monthz' value='".$month."'>"; 
									}
								}
							} else {
								if($PREV_0 > 0){
									echo date("n월",strtotime("0 month"))." : ".getSafeNumberFormatted($PREV_0)."<br/>";
									echo "<input type='hidden' name='row_prev_0' value='".$PREV_0."'>";
									echo "<input type='hidden' name='test_prev_0' value='".$UP_DATE."'>";
									echo "<input type='hidden' name='monthz' value='".$month."'>"; 
								}
							}
							if($PREV_1 != 0){
								echo date("n월",strtotime("first day of -1 month"))." : ".getSafeNumberFormatted($PREV_1)."<br/>";
								echo "<input type='hidden' name='row_prev_1' value='".$PREV_1."'>";
							}
							if($PREV_2 != 0){
								echo date("n월",strtotime("first day of -2 month"))." : ".getSafeNumberFormatted($PREV_2)."<br/>";
								echo "<input type='hidden' name='row_prev_2' value='".$PREV_2."'>";
							}
							if($PREV_3 != 0){
								echo date("n월",strtotime("first day of -3 month"))." : ".getSafeNumberFormatted($PREV_3)."<br/>";
								echo "<input type='hidden' name='row_prev_3' value='".$PREV_3."'>";
							}
						?>
						
						<?=$MEMO?>
						</div>
						<? } else { ?>
						<?=getAdminName($conn, $SALE_ADM_NO)?>
						<? } ?>
					</td><!--영업담당-->
					<td>
						<div class="column_show_type"><input type="button" name="bb" value="조회" onclick="js_link_to_company_ledger('<?=$CP_NO?>');"/></div>
						<div class="column_show_type" style="display:none;">
							<? 
								if($UP_DATE != "" )
									echo date("Y-m-d",strtotime($UP_DATE));
						        else
									echo "없음";
							?>
						</div>
					</td><!--원장조회-->
				</tr>

				<? 
						}
					} else { 
				?>

				<tr height="35">
					<td colspan="10">데이터가 없습니다.</td>
				</tr>

				<? } ?>
				<? if($con_sale_adm_no <> "") { ?>
				<tr height="30" class="selected display_none">
					<td></td>
					<td colspan="2">선택 총액 :</td>
					<td class="price"><span id="total_prev_balance"></span></td><!--이월잔액-->
					<td class="price"><span id="total_deposit"></span></td><!--매출액-->
					<td class="price"><span id="total_withdraw"></span></td><!--입금액-->
					<td class="price"><span id="total_balance"></span></td><!--잔액-->
					<td class="price" style="text-align:right;">
						<span id="total_prev_0"></span><br/>
						<span id="total_prev_1"></span><br/>
						<span id="total_prev_2"></span><br/>
						<span id="total_prev_3"></span><br/><br/>
						<span id="grand_total_prev"></span>
					</td>
					<td style="text-align:left; padding-left:10px;">
						<?=date("n월",strtotime("0 month"))?><br/>
						<?=date("n월",strtotime("first day of -1 month"))?><br/>
						<?=date("n월",strtotime("first day of -2 month"))?><br/>
						<?=date("n월",strtotime("first day of -3 month"))?><br/><br/>
						합계
					</td>
				</tr>
				<? } ?>
			</table>
				
				<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">
				
				</div>

					
					<!-- --------------------- 페이지 처리 화면 START -------------------------->
					<?
						# ==========================================================================
						#  페이징 처리
						# ==========================================================================
						if (sizeof($arr_rs) > 0) {
							#$search_field		= trim($search_field);
							#$search_str			= trim($search_str);
							$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&start_date=".$start_date."&end_date=".$end_date;
							$strParam = $strParam."&con_sale_adm_no=".$con_sale_adm_no."&con_cp_type=".$con_cp_type."&con_ad_type=".$con_ad_type."&order_field=".$order_field."&order_str=".$order_str;
					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- 페이지 처리 화면 END -------------------------->
					
				<br />

				<?if($search_str == "") { ?>
				<div class="sp30"></div>

				<table cellpadding="0" cellspacing="0" class="rowstable" border="0">

				<colgroup>
					<col width="2%" />
					<col width="5%" />
					<col width="*" />
					<col width="8%" />
					<col width="8%" />
					<col width="8%" />
					<col width="9%" />
					<? if($con_sale_adm_no == "") { ?>
					<col width="7%" />
					<? } else { ?>
					<col width="16%" />
					<? } ?>
					<col width="9%" />
				</colgroup>
				<thead>
				</thead>
				<?
					if (sizeof($arr_rs_sum) > 0) {

						$ALL_TOTAL_PREV_BALANCE		= 0;
						$ALL_TOTAL_SUM_SALES		= 0;
						$ALL_TOTAL_SUM_SALES_TAX	= 0;
						$ALL_TOTAL_SUM_COLLECT		= 0;
						$ALL_TOTAL_SUM_BALANCE		= 0;
						$ALL_TOTAL_EXCEPT_SALE		= 0;
						
						$ALL_TOTAL_SUM_PREV_0		= 0;
						$ALL_TOTAL_SUM_PREV_1		= 0;
						$ALL_TOTAL_SUM_PREV_2		= 0;
						$ALL_TOTAL_SUM_PREV_3		= 0;


						for ($k = 0 ; $k < sizeof($arr_rs_sum); $k++) {

							$TOTAL_PREV_BALANCE				= trim($arr_rs_sum[$k]["TOTAL_PREV_BALANCE"]);
							$TOTAL_SUM_SALES				= trim($arr_rs_sum[$k]["TOTAL_SUM_SALES"]);
							$TOTAL_SUM_SALES_TAX			= trim($arr_rs_sum[$k]["TOTAL_SUM_SALES_TAX"]);
							$TOTAL_SUM_COLLECT				= trim($arr_rs_sum[$k]["TOTAL_SUM_COLLECT"]);
							$TOTAL_EXCEPT_SALE				= trim($arr_rs_sum[$k]["TOTAL_EXCEPT_SALE"]);
							$TOTAL_SUM_BALANCE				= trim($arr_rs_sum[$k]["TOTAL_SUM_BALANCE"]);
							$GROUP_SALE_ADM_NO				= trim($arr_rs_sum[$k]["SALE_ADM_NO"]);

							$TOTAL_SUM_PREV_0				= trim($arr_rs_sum[$k]["TOTAL_SUM_PREV_0"]);

							// if($con_sale_adm_no == 7 && date("n", strtotime("0 month")) == 12)
							//  	$TOTAL_SUM_PREV_0 -= 524700;
							$TOTAL_SUM_PREV_1				= trim($arr_rs_sum[$k]["TOTAL_SUM_PREV_1"]);
							$TOTAL_SUM_PREV_2				= trim($arr_rs_sum[$k]["TOTAL_SUM_PREV_2"]);
							$TOTAL_SUM_PREV_3				= trim($arr_rs_sum[$k]["TOTAL_SUM_PREV_3"]);

							$ALL_TOTAL_PREV_BALANCE		+= $TOTAL_PREV_BALANCE;
							$ALL_TOTAL_SUM_SALES		+= $TOTAL_SUM_SALES;
							$ALL_TOTAL_SUM_SALES_TAX	+= $TOTAL_SUM_SALES_TAX;
							$ALL_TOTAL_SUM_COLLECT		+= $TOTAL_SUM_COLLECT;
							$ALL_TOTAL_EXCEPT_SALE      += $TOTAL_EXCEPT_SALE;
							$ALL_TOTAL_SUM_BALANCE		+= $TOTAL_SUM_BALANCE;
							
							$ALL_TOTAL_SUM_PREV_0		+= $TOTAL_SUM_PREV_0;
							$ALL_TOTAL_SUM_PREV_1		+= $TOTAL_SUM_PREV_1;
							$ALL_TOTAL_SUM_PREV_2		+= $TOTAL_SUM_PREV_2;
							$ALL_TOTAL_SUM_PREV_3		+= $TOTAL_SUM_PREV_3;
			
							$GROUP_SALE_ADM_NM = getAdminName($conn, $GROUP_SALE_ADM_NO);

							if(sizeof($arr_rs_sum) > 2) {

				?>
				<tr height="65" class="row_color_lev1">
					<td class="modeual_nm" colspan="3"><?=$GROUP_SALE_ADM_NM?> 합계 :</td>
					<td class="price"><?=getSafeNumberFormatted($TOTAL_PREV_BALANCE)?></td>
					<td class="price">
						<span class="include_tax"><?=getSafeNumberFormatted($TOTAL_SUM_SALES)?></span>
						<span class="only_tax"><?=getSafeNumberFormatted($TOTAL_SUM_SALES_TAX)?></span>
					</td>
					<td class="price"><?=getSafeNumberFormatted($TOTAL_SUM_COLLECT)?></td>

					<?if($chk_prev_month == "Y") { ?>
						<td class="price">
							<?=getSafeNumberFormatted($TOTAL_SUM_BALANCE)?>
						</td>
					<? } else { ?>
						<td class="price">
							<?=getSafeNumberFormatted($TOTAL_EXCEPT_SALE)?>
						</td>
					<? } ?>
					<td class="price">
						<? if($chk_prev_month == "Y" && $TOTAL_SUM_SALES != 0) { ?>
							<?=getSafeNumberFormatted($TOTAL_SUM_PREV_0)?><br/>
						<? } ?>
						<?=getSafeNumberFormatted($TOTAL_SUM_PREV_1)?><br/>
						<?=getSafeNumberFormatted($TOTAL_SUM_PREV_2)?><br/>
						<?=getSafeNumberFormatted($TOTAL_SUM_PREV_3)?><br/><br/>
						<?=getSafeNumberFormatted($TOTAL_SUM_PREV_1 + $TOTAL_SUM_PREV_2 + $TOTAL_SUM_PREV_3)?>
					</td>
					<td style="text-align:left; padding-left:10px;">
						<? if($chk_prev_month == "Y" && $TOTAL_SUM_SALES != 0) { ?>
							<?=date("n월",strtotime("0 month"))?><br/>
						<? } ?>
						<?=date("n월",strtotime("first day of -1 month"))?><br/>
						<?=date("n월",strtotime("first day of -2 month"))?><br/>
						<?=date("n월",strtotime("first day of -3 month"))?><br/><br/>
						합계
					</td>
					
				</tr>

				<? 
							}
						}
				?>
				<tr height="65" class="row_color_lev2">
					<td class="modeual_nm" colspan="3">전체 합계:</td>
					<td class="price"><?=getSafeNumberFormatted($ALL_TOTAL_PREV_BALANCE)?></td><!--이월잔액-->
					<td class="price"><?=getSafeNumberFormatted($ALL_TOTAL_SUM_SALES)?></td><!--매출액-->
					<td class="price"><?=getSafeNumberFormatted($ALL_TOTAL_SUM_COLLECT)?></td><!--입금액-->

					<?if($chk_prev_month == "Y") { ?>
						<td class="price">
							<?=getSafeNumberFormatted($ALL_TOTAL_SUM_BALANCE)?>
						</td><!--잔액-->
					<? } else { ?>
						<td class="price">
							<?=getSafeNumberFormatted($ALL_TOTAL_EXCEPT_SALE)?>
						</td><!--전월미수-->
					<? } ?>
					<td class="price">
						<? if($chk_prev_month == "Y" && $ALL_TOTAL_SUM_SALES != 0) { ?>
							<?=getSafeNumberFormatted($ALL_TOTAL_SUM_PREV_0)?><br/>
						<? } ?>
						<?=getSafeNumberFormatted($ALL_TOTAL_SUM_PREV_1)?><br/>
						<?=getSafeNumberFormatted($ALL_TOTAL_SUM_PREV_2)?><br/>
						<?=getSafeNumberFormatted($ALL_TOTAL_SUM_PREV_3)?><br/><br/>
						<? if($chk_prev_month == "Y" && $ALL_TOTAL_SUM_SALES != 0) { ?>
						<?=getSafeNumberFormatted($ALL_TOTAL_SUM_PREV_0 + $ALL_TOTAL_SUM_PREV_1 + $ALL_TOTAL_SUM_PREV_2 + $ALL_TOTAL_SUM_PREV_3)?>
						<? } else { ?>
						<?=getSafeNumberFormatted($ALL_TOTAL_SUM_PREV_1 + $ALL_TOTAL_SUM_PREV_2 + $ALL_TOTAL_SUM_PREV_3)?>
						<? } ?>
					</td><!--월별합계-->
					<td style="text-align:left; padding-left:10px;">
						<? if($chk_prev_month == "Y" && $ALL_TOTAL_SUM_SALES != 0) { ?>
							<?=date("n월",strtotime("0 month"))?><br/>
						<? } ?>
						<?=date("n월",strtotime("first day of -1 month"))?><br/>
						<?=date("n월",strtotime("first day of -2 month"))?><br/>
						<?=date("n월",strtotime("first day of -3 month"))?><br/><br/>
						합계 
					</td><!--월-->
					
				</tr>
				<?
					} else { 
				?>

				<tr height="35">
					<td colspan="10">데이터가 없습니다.</td>
				</tr>

				<? } ?>
			</table>
			<? } ?>
			
			<div class="sp50"></div>
		<!-- // E: mwidthwrap -->
			</div>
			<!-- // E: mwidthwrap -->

		</td>
	</tr>
	</table>
	<script type="text/javascript">
		$(function(){
			$("#show_only_tax").click(function(){
				$(".include_tax").toggle();
				$(".only_tax").toggle();
			});

			$("select[name=show_type]").change(function(){
				$(".column_show_type").toggle();
			});
		});
	
	</script>
	<div style="display:scroll;position:fixed;bottom:10px;right:10px;padding:10px;border:1px solid black;background-color:#fff;">
		<input type="button" name="aa" value=" 선택한 업체 이전월 미수 이월 " class="btntxt" onclick="js_update_prev();">
		<a href="#">▲ 위로</a>
	</div>
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