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
	$menu_right = "SG017"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/stock/stock.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/confirm/confirm.php";


	function get_ADTYPE_list($db){
		$query="SELECT 	DCODE, DCODE_NM
				FROM	TBL_CODE_DETAIL
				WHERE	PCODE='AD_TYPE'
				AND		DEL_TF='N'
				AND		USE_TF='Y'
				";
		
		// echo "$query<br>";
		// exit;

		$result=mysql_query($query, $db);
		
		$cnt=0;
		$record=array();
		if($result<>""){
			$cnt=mysql_num_rows($result);

		}
		for($i = 0; $i < $cnt; $i++){
			$record[$i]=mysql_fetch_assoc($result);
		}
		return $record;
	}



#====================================================================
# Request Parameter
#====================================================================


	//echo "con_payment : ".$con_payment."<br>";




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

	if ($base_confirm_date == "") {
		$base_confirm_date = date("Y-m-d",strtotime("0 month"));
	} else {
		$base_confirm_date = trim($base_confirm_date);
	}

	$day_0 = date("Y-m-d",strtotime("0 month"));
	$day_1 = date("Y-m-d",strtotime("-1 day"));
	$day_7 = date("Y-m-d",strtotime("-7 day"));
	$day_31 = date("Y-m-d",strtotime("-1 month"));

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);

	$confirm_mode = "Y";

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


	//매입확정
	if ($mode == "CY") {
		
		$arrlength = count($chk_rg_no);

		for($x = 0; $x < $arrlength; $x++) {
			$temp_req_no = $chk_rg_no[$x];

			//echo $temp_req_no."<br/>";

			//* 순서 변경 금지
			//매입기장
			$result = insertCompanyLedgerDepositFromGoodsRequest($conn, $temp_req_no, $base_confirm_date, $s_adm_no);

			//여분이 있을시 삭제
			//if($result)
			//	deleteFStock($conn, $temp_req_no, $s_adm_no);
				
		}
	}

	//매입취소
	if ($mode == "CN") {
		
		$arrlength = count($chk_rg_no);

		for($x = 0; $x < $arrlength; $x++) {
			$temp_req_no = $chk_rg_no[$x];

			//echo $temp_req_no."<br/>";

			//* 순서 변경 금지
			//매입기장
			$result = deleteCompanyLedgerDepositFromGoodsRequest($conn, $temp_req_no, $s_adm_no);
				
		}
	}

	//추가기장 입력
	if ($mode == "UPDATE_EXTRA") { 

		$arrlength = count($arr_option_rgn_no);

		for($x = 0; $x < $arrlength; $x++) {
			
			$option_rgn_no	= $arr_option_rgn_no[$x];
			$option_name	= $arr_option_name[$x];
			$option_qty		= $arr_option_qty[$x];
			$option_price	= $arr_option_price[$x];
			$option_memo	= $arr_option_memo[$x];

			//echo "option_rgn_no: ".$option_rgn_no."option_name: ".$option_name."option_qty: ".$option_qty."option_price: ".$option_price."<br/>";

			if($option_rgn_no <> "" && $option_name <> "" && $option_qty <> "" && $option_price <> "") { 
				InsertRequestGoodsSubLedger($conn, $option_rgn_no, $option_name, $option_qty, $option_price, $option_memo, $s_adm_no);
			}
		}
	}

	//추가기장 삭제
	if ($mode == "DELETE_EXTRA") { 

		if($grgl_no <> "") { 

			deleteRequestGoodsSubLedger($conn, $grgl_no, $s_adm_no);
			
			//기장삭제
			$options = array('GRGL_NO' => $grgl_no);
			deleteCompanyLedgerByCode($conn, $options, $s_adm_no);
		}

	}

	//전표 삭제
	if ($mode == "D") {

		$arrlength = count($chk_no);

		for($x = 0; $x < $arrlength; $x++) {
			$temp_req_no = $chk_no[$x];

			DeleteGoodsRequest($conn, $temp_req_no, $s_adm_no);
		}
	}

	//전표 상품 취소/복구
	if ($mode == "CANCEL") {
			
		$arrlength = count($chk_rg_no);

		for($x = 0; $x < $arrlength; $x++) {
			$temp_req_no = $chk_rg_no[$x];

			for($j = 0; $j < sizeof($arr_received_tf); $j ++) { 

				$arr_temp = explode("|",$arr_received_tf[$j]);
				
				if($temp_req_no == $arr_temp[0]) { 

					$temp_received_tf = $arr_temp[1];

					//echo "req_no : ".$temp_req_no.", received_tf : ".$temp_received_tf."<br/>";
					if(!$temp_received_tf) { 

						//발주취소, 기장취소, 가입고삭제, 기장삭제
						UpdateGoodsRequestGoodsStatus($conn, $temp_req_no, $s_adm_no);
						
					}
				}
			}
		}
		
	}

	$filter = array('con_delivery_tf' => $con_delivery_tf, 'con_to_here' => $con_to_here, 'con_cancel_tf' => $con_cancel_tf, 'con_confirm_tf' => $con_confirm_tf, 'con_changed_tf' => $con_changed_tf, 'con_wrap_tf' => $con_wrap_tf, 'con_sticker_tf' => $con_sticker_tf, 'con_receive_tf' => $con_receive_tf, 'chk_after_confirm' => $chk_after_confirm, 'con_payment' =>$con_payment);

	$nListCnt =totalCntGoodsRequest($conn, $start_date, $end_date, $con_cp_type, $filter, $search_field, $search_str, $exclude_category, $order_field, $order_str);

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	//$arr_rs = listGoodsRequest($conn, $start_date, $end_date, $con_cp_type, $filter, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize, $nListCnt);

	if($order_field != "DELIVERY_DATE") 
	{
		$arr_rs = listGoodsRequest($conn, $start_date, $end_date, $con_cp_type, $filter, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize, $nListCnt);
	}
	else
	{	// 2021.01.27 입고처리일 정렬chk_after_confirm=
		$arr_rs = listGoodsRequestOrderDelivery($conn, $start_date, $end_date, $con_cp_type, $filter, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize, $nListCnt);
	}

	// 2018.11.8 사용안함으로 제거
	//$arr_stat = cntRequestGoodsState($conn, $start_date, $end_date);

	$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str;
	$strParam = $strParam."&start_date=".$start_date."&end_date=".$end_date."&con_cp_type=".$con_cp_type;
	$strParam = $strParam."&con_delivery_tf=".$con_delivery_tf."&con_to_here=".$con_to_here."&con_cancel_tf=".$con_cancel_tf."&con_confirm_tf=".$con_confirm_tf."&con_changed_tf=".$con_changed_tf."&con_wrap_tf=".$con_wrap_tf."&con_sticker_tf=".$con_sticker_tf."&con_payment=".$con_payment;

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
  <script>
	$(function(){
		$('table.fixed_header_table').floatThead({
			position: 'fixed'
		});
	});
  </script>
  <script type="text/javascript" >

	function js_write() {

		var frm = document.frm;
		
		frm.target = "";
		frm.method = "post";
		frm.action = "goods_request_write.php";
		frm.submit();

	}

	function js_view(req_no) {

		var frm = document.frm;

		frm.req_no.value = req_no;
		frm.target = "";
		frm.method = "post"; //리퀘스트 URI 너무 길어서 get으로 바꾸지 말것..
		frm.action = "goods_request_write.php";
		frm.submit();

		//var url = "pop_goods_request.php";

		//NewWindow(url, 'pop_goods_request','860','600','YES');
		
		
	}
	
	// 조회 버튼 클릭 시 
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";

		frm.target = "";
		frm.method = "post";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_excel() {
		
		//alert("준비중 입니다..");
		//return;

		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.target = "";
		frm.action = "<?=str_replace("list","excel_list",$_SERVER[PHP_SELF])?>";
		frm.submit();

	}
	function calcSelectedLedger2(){
		var total_buying=0;

		$("input[name='chk_rg_no[]']").each(function(){
			
			var rgn_no = $(this).val();
			
			if($(this).prop('checked') == true)
			{				
				var buying=0;
				
				cancleTf=$(this).closest("tr").find("input[name='cancleTf']").data("value");
				
				if(cancleTf != "Y")
				{						
					buying=$(this).closest("tr").find("td.row_buying").data("value");

					$(".group_"+rgn_no).each(function(){
						buying =parseFloat(buying)+parseFloat($(this).find("td.row_buying").data("value"));
					});
				}

				total_buying+=parseFloat(buying);
			}
		});

		if(total_buying != 0){
			$(".selected").show();
			$("#total_buying").html(numberFormat(total_buying));
		}
		else{
			$('.selected').hide();
			$("#total_buying").html("");
		}
	}//end of function

	function js_all_check() {

		var frm = document.frm;

		
		if (frm['chk_rg_no[]'] != null) {
			
			if (frm['chk_rg_no[]'].length != null) {

				if (frm.chkAll.checked == true) {
					for (i = 0; i < frm['chk_rg_no[]'].length; i++) {
						frm['chk_rg_no[]'][i].checked = true;


					}


				} else {
					for (i = 0; i < frm['chk_rg_no[]'].length; i++) {
						frm['chk_rg_no[]'][i].checked = false;
					}
				}
			} else {
			
				if (frm.chkAll.checked == true) {
					frm['chk_rg_no[]'].checked = true;
				} else {
					frm['chk_rg_no[]'].checked = false;
				}
			}
		}
		calcSelectedLedger2();
	}

	function js_delete() {
		var frm = document.frm;

		bDelOK = confirm('선택한 발주를 삭제하시겠습니까?');
		
		if (bDelOK==true) {
			
			frm.mode.value = "D";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_cancel() {
		var frm = document.frm;
	
		bDelOK = confirm('선택한 발주를 취소/복구하시겠습니까?');
		
		if (bDelOK==true) {
			
			frm.mode.value = "CANCEL";

			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	//매입확정
	function js_confirm(tf) {
		var frm = document.frm;

		var selected_cnt = $("input[name='chk_rg_no[]']:checked").length;

		if(selected_cnt > 0) 
		{
			if(tf == "Y") { 
				if(selected_cnt >= 1) 
				{
					
					if (confirm('선택한 발주를 매입확정하시겠습니까?'))
					{
						frm.mode.value = "CY";
						frm.target = "";
						frm.action = "<?=$_SERVER[PHP_SELF]?>";
						frm.submit();
					} 
				} else
					alert('선택된 발주가 없습니다');
			}
			else { 
				if(selected_cnt >= 1) 
				{
					if (confirm('선택한 발주를 매입취소하시겠습니까?'))
					{
						frm.mode.value = "CN";
						frm.target = "";
						frm.action = "<?=$_SERVER[PHP_SELF]?>";
						frm.submit();
					}
				} else
					alert('선택된 발주가 없습니다');
			}

		}
		else
			alert('선택된 발주가 없습니다');
	}


	var day_0 = "<?=$day_0?>";
	var day_1 = "<?=$day_1?>";
	var day_7 = "<?=$day_7?>";
	var day_31 = "<?=$day_31?>";

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

		frm.nPage.value = "1";
		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_pop_delivery_paper_frame(delivery_cp, delivery_no) { 

		var url = "/manager/order/pop_delivery_paper_wrapper.php?delivery_cp=" + delivery_cp + "&delivery_no=" + delivery_no;
		NewWindow(url, 'pop_delivery_paper_wrapper', '920', '700', 'YES');

	}

	function js_link_to_in_stock(request_goods_no, req_qty, start_date) { 

		if(req_qty > 0)
			window.open("/manager/stock/in_list.php?nPage=1&order_field=REG_DATE&order_str=DESC&nPageSize=20&search_field=RGN_NO&search_str=" + request_goods_no + "&start_date=" + start_date,'_blank');
		else
			window.open("/manager/stock/out_list.php?nPage=1&order_field=REG_DATE&order_str=DESC&nPageSize=20&search_field=RGN_NO&search_str=" + request_goods_no + "&start_date=" + start_date,'_blank');

	}

	function js_pop_company_extra(cp_no) { 

		var frm = document.frm;

		var tab_index = 1; //송장탭으로 이동
		
		var url = "/manager/company/pop_company_extra.php?cp_no="+cp_no + "&tab_index=" + tab_index;

		NewWindow(url, 'pop_company_extra','1000','600','YES');
	}

	function js_pop_buy_cp_check() { 

		var url = "/manager/stock/pop_goods_request_buy_company_list.php?<?=$strParam?>";
		NewWindow(url, 'pop_goods_request_buy_company_list', '920', '700', 'YES');

	}

	function js_chk_confirm(req_no, group_no, req_date) 
	{
		var frm = document.frm;
		var value_yn;
		var chk_yn = $("input[name=confirmReqNo_"+req_no).val();

		//alert("req_no=="+req_no+", chk_yn=="+chk_yn);		

		if(chk_yn == "Y")
		{
			//if (!confirm("발주번호 : "+req_no+"\n\n발주 확인을 취소 하시겠습니까?")) return;
			if (!confirm("전표 : "+group_no+"\n\n발주일자 : "+req_date+"\n\n발주 확인을 취소 하시겠습니까?")) return;
			//if (!confirm("발주 확인을 취소 하시겠습니까?")) return;		
			value_yn = "N";
		}
		else
		{
			//if (!confirm("발주번호 : "+req_no+"\n\n발주 확인 하시겠습니까?")) return;
			if (!confirm("전표 : "+group_no+"\n\n발주일자 : "+req_date+"\n\n발주 확인을 하시겠습니까?")) return;
			//if (!confirm("발주 확인 하시겠습니까?")) return;
			value_yn = "Y";
		}		

		//alert(value_yn);

		$.ajax({
			url: "json_goods_request_check.php",
			dataType: 'json',
			type: 'post',
			async: true,
			data: {
					  mode: "GODDS_REQUEST_CONFIRM"					
					, reg_adm: <?=$s_adm_no?>
					, req_no: req_no
					, check_yn: value_yn
				},
				success: function(data) 
				{
					$("input[name=confirmReqNo_"+req_no).val(value_yn);
					
					if(value_yn == "Y")
					{
						alert("확인 되었습니다.");
						document.getElementById("confirmCk_"+req_no).style.color="blue";
						//$("#confirmCk_"+req_no).css("color","blue");
					}
					else
					{
						alert("취소 되었습니다.");
						document.getElementById("confirmCk_"+req_no).style.color="green";
					}						
				}	,
				error: function(jqXHR, textStatus, errorThrown)
				{
					alert('통신 실패');
					return;
				}
		});		
	}

</script>
<style>
	
	table.rowstable td {background: none;}
	table.rowstable tr {border-bottom: 1px dotted #eee;}
	.top_group {border-top: 1px solid #86a4b3; font-weight:bold;}
	.confirm_order {background: #EFEFEF;}
	.extra_ledger {border-top: 1px dotted black; border-bottom: 1px dotted black;} 
	.row_goods_sum {background-color:#EFEFEF; font-weight:bold;}
	span.box_cnt {color:red;} 
	.selected {border-top: 2px solid #86a4b3;}
</style>
</head>

<body id="admin">

<form name="frm" method="post" onSubmit="js_search(); return true;">
<input type="hidden" name="depth" value="" />
<input type="hidden" name="req_no" value="">
<input type="hidden" name="rg_no" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<input type="hidden" name="keyword" value="">
<input type="hidden" name="group_no" value="">
<input type="hidden" name="grgl_no" value="">
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
				
				<h2>발주 관리</h2>
				<div class="btnright">
					<a href="javascript:js_write();"><img src="../images/admin/btn_regist_02.gif" alt="등록"></a>
				</div>

				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="10%" />
					<col width="37%" />
					<col width="10%" />
					<col width="37%" />
					<col width="6%" />
				</colgroup>
				<tbody>
					<tr>
						<th>발주일자</th>
						<td colspan="2">
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
						<td colspan="2" align="right">
							<select name="print_type">
								<option value="" <?if($print_type == "") echo "selected";?>>화면그대로</option>
								<option value="BUY_CP" <?if($print_type == "BUY_CP") echo "selected";?>>매입업체만</option>
							</select>&nbsp;<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>
						</td>
					</tr>
					<tr>
						<th>매입업체</th>
						<td>
							<input type="text" class="autocomplete_off" style="width:90%" placeholder="업체(명/코드) 입력후 엔터를 눌러주세요" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'',$con_cp_type)?>" />
							<input type="hidden" name="con_cp_type" value="<?=$con_cp_type?>">

							<script>
								$(function(){

									$("input[name=txt_cp_type]").keydown(function(e){

										if(e.keyCode==13) { 

											var keyword = $(this).val();
											if(keyword == "") { 
												$("input[name=con_cp_type]").val('');
												js_search();
											} else { 
												$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('구매,판매공급') +"&term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
													if(data.length == 1) { 
														
														js_selecting_company("txt_cp_type", data[0].label, "con_cp_type", data[0].id);

													} else if(data.length > 1){ 
														NewWindow("../company/pop_company_searched_list.php?con_cp_type=구매,판매공급&search_str="+keyword + "&target_name=txt_cp_type&target_value=con_cp_type",'pop_company_searched_list','950','650','YES');

													} else 
														alert("검색결과가 없습니다.");
												});
											}
										}

									});

									$("input[name=txt_cp_type]").keyup(function(e){
										var keyword = $(this).val();

										if(keyword == "") { 
											$("input[name=con_cp_type]").val('');
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
						<td colspan="3">
							
						</td>
					</tr>
					<tr>
						<th rowspan="3">필터</th>
						<td colspan="4">
							<span>
								<b>배송여부:</b>
								<select name="con_delivery_tf">
									<option value="" <? if ($con_delivery_tf == "") echo "selected"; ?> >전체</option>
									<option value="N" <? if ($con_delivery_tf == "N") echo "selected"; ?> >발송전</option>
									<option value="Y" <? if ($con_delivery_tf == "Y") echo "selected"; ?> >발송함</option>
								</select>
							</span>&nbsp;&nbsp;&nbsp;&nbsp;
							<span>
								<b>취소여부:</b>
								<select name="con_cancel_tf">
									<option value="" <? if ($con_cancel_tf == "") echo "selected"; ?> >전체</option>
									<option value="N" <? if ($con_cancel_tf == "N") echo "selected"; ?> >유효만</option>
									<option value="Y" <? if ($con_cancel_tf == "Y") echo "selected"; ?> >취소만</option>
								</select>
							</span>&nbsp;&nbsp;&nbsp;&nbsp;
							<span>
								<b>매입확정여부:</b>
								<select name="con_confirm_tf">
									<option value="" <? if ($con_confirm_tf == "") echo "selected"; ?> >전체</option>
									<option value="N" <? if ($con_confirm_tf == "N") echo "selected"; ?> >미확정</option>
									<option value="Y" <? if ($con_confirm_tf == "Y") echo "selected"; ?> >확정</option>
								</select>
							</span>&nbsp;&nbsp;&nbsp;&nbsp;
							<span>
								<b>내역변경여부:</b>
								<select name="con_changed_tf">
									<option value="" <? if ($con_changed_tf == "") echo "selected"; ?> >전체</option>
									<option value="N" <? if ($con_changed_tf == "N") echo "selected"; ?> >변경없음</option>
									<option value="Y" <? if ($con_changed_tf == "Y") echo "selected"; ?> >변경됨</option>
								</select>
							</span>&nbsp;&nbsp;&nbsp;&nbsp;
						</td>
					</tr>
					<tr>
						<td colspan="4">
							<span>
								<b>수령처:</b>
								<select name="con_to_here">
									<option value="" <? if ($con_to_here == "") echo "selected"; ?> >전체</option>
									<option value="Y" <? if ($con_to_here == "Y") echo "selected"; ?> >자체수령</option>
									<option value="N" <? if ($con_to_here == "N") echo "selected"; ?> >직송</option>
								</select>
							</span>&nbsp;&nbsp;&nbsp;&nbsp;
							<span>
								<b>포장여부:</b>
								<select name="con_wrap_tf">
									<option value="" <? if ($con_wrap_tf == "") echo "selected"; ?> >전체</option>
									<option value="N" <? if ($con_wrap_tf == "N") echo "selected"; ?> >작업없음</option>
									<option value="Y" <? if ($con_wrap_tf == "Y") echo "selected"; ?> >작업있음</option>
								</select>
							</span>&nbsp;&nbsp;&nbsp;&nbsp;
							<span>
								<b>스티커여부:</b>
								<select name="con_sticker_tf">
									<option value="" <? if ($con_sticker_tf == "") echo "selected"; ?> >전체</option>
									<option value="N" <? if ($con_sticker_tf == "N") echo "selected"; ?> >작업없음</option>
									<option value="Y" <? if ($con_sticker_tf == "Y") echo "selected"; ?> >작업있음</option>
								</select>
							</span>&nbsp;&nbsp;&nbsp;&nbsp;
							<span>
								<b>입고여부:</b>
								<select name="con_receive_tf">
									<option value="" <? if ($con_receive_tf == "") echo "selected"; ?> >전체</option>
									<option value="N" <? if ($con_receive_tf == "N") echo "selected"; ?> >입고안됨</option>
									<option value="Y" <? if ($con_receive_tf == "Y") echo "selected"; ?> >입고됨</option>
								</select>
							</span>&nbsp;&nbsp;&nbsp;&nbsp;
							<span>
							<?
								$adtypes=get_ADTYPE_list($conn);
								$cntad=sizeof($adtypes);
								// echo "cntad : ".$cntad."<br>";
							?>
								<b>결제구분</b>
								<select name="con_payment">
									<option value="" <?if($con_payment == "") echo "selected"; ?>>전체</option>
									<?
										for($i=0; $i<$cntad; $i++){
										?>
											<option value="<?=$adtypes[$i]["DCODE"]?>" <?if($con_payment == $adtypes[$i]["DCODE"]) echo "selected"; ?>><?=$adtypes[$i]["DCODE_NM"]?></option>
										<?
										}
									?>
									<!-- <option value="" -->
								</select>
							</span>
						</td>
					</tr>

					<tr>
						<td colspan="2">
							<span>
								매입확정후 변경: 
								<input type="checkbox" name="chk_after_confirm" value="Y" <?if($chk_after_confirm == "Y") echo "checked";?>/>
							</span>&nbsp;&nbsp;&nbsp;&nbsp;
							
						</td>
						<td colspan="2" style="text-align:right;">
							<a href="javascript:js_pop_buy_cp_check()" style="text-decoration:underline;">매입업체별 계산서확인</a>
						</td>
					</tr>
					<tr>
						<th>정렬</th>
						<td>
							<select name="order_field" style="width:84px;">
								<option value="REG_DATE" <? if ($order_field == "REG_DATE") echo "selected"; ?> >등록일</option>
								<option value="REQ_DATE" <? if ($order_field == "REQ_DATE") echo "selected"; ?> >발주일</option>
								<option value="BUY_CP_NM" <? if ($order_field == "BUY_CP_NM") echo "selected"; ?> >매입업체</option>
								<option value="BUY_MANAGER_NM" <? if ($order_field == "BUY_MANAGER_NM") echo "selected"; ?> >매입담당자</option>								
								<option value="DELIVERY_DATE" <? if ($order_field == "DELIVERY_DATE") echo "selected"; ?> >입고처리일</option>
							</select>&nbsp;&nbsp;
							<input type='radio' class="" name='order_str' value='ASC' <? if (($order_str == "ASC")) echo " checked"; ?> > 오름차순 &nbsp;
							<input type='radio' name='order_str' value='DESC' <? if (($order_str == "DESC") || ($order_str == "")) echo " checked"; ?>> 내림차순
						</td>
						<th>검색조건</th>
						<td colspan="2">
							<select name="nPageSize" style="width:84px;">
								<option value="20" <? if ($nPageSize == "20") echo "selected"; ?> >20개씩</option>
								<option value="50" <? if ($nPageSize == "50") echo "selected"; ?> >50개씩</option>
								<option value="100" <? if ($nPageSize == "100") echo "selected"; ?> >100개씩</option>
							</select>&nbsp;
							<select name="search_field" style="width:84px;">
								<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >통합검색</option>
								<option value="BUY_CP_NM" <? if ($search_field == "BUY_CP_NM") echo "selected"; ?> >매입업체</option>
								<option value="BUY_MANAGER_NM" <? if ($search_field == "BUY_MANAGER_NM") echo "selected"; ?> >매입담당자</option>
								<option value="BUY_CP_PHONE" <? if ($search_field == "BUY_CP_PHONE") echo "selected"; ?> >매입연락처</option>
								<option value="REQ_GOODS_NO" <? if ($search_field == "REQ_GOODS_NO") echo "selected"; ?> >발주상품번호</option>
								<option value="GOODS_NAME" <? if ($search_field == "GOODS_NAME") echo "selected"; ?> >제품명/코드</option>
								<option value="MEMO2" <? if ($search_field == "MEMO2") echo "selected"; ?> >비고2</option>
								<option value="ORDER_GOODS_NO" <? if ($search_field == "ORDER_GOODS_NO") echo "selected"; ?> >*주문상품번호</option>
							</select>&nbsp;

							<input type="text" value="<?=$search_str?>" name="search_str" size="20" class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();" />
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						</td>
						
					</tr>
				</tbody>
			</table>
			<div class="sp20"></div>
				<!--
				<b><font color='blue'>총 발주서</font> <font color='red'><?=$nListCnt?></font> <font color='blue'>장 /</font></b>
				<?
					for($o = 0; $o < sizeof($arr_stat); $o++) { 
						$cnt		= $arr_stat[$o]["CNT"];
						$grg_type   = $arr_stat[$o]["GRG_TYPE"];

						echo "<b><font color='blue'>".$grg_type."</font> <font color='red'>".$cnt."</font> <font color='blue'>건</font></b>&nbsp;";
					}
				?>
				-->
				<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table">
					<colgroup>
						<col width="3%" />
						<col width="7%" />
						<col width="*" />
						<col width="10%" />
						<col width="5%" />
						<col width="10%" />
						<col width="13%" />
						<col width="8%" />
						<col width="7%" />
						<col width="10%" />
						<col width="5%" />
					</colgroup>
					<thead>
						<tr>
							<th>전표</th>
							<th>발주일자</th>
							<th>매입업체</th>
							<th>담당자</th>
							<th>총수량</th>
							<th>총합계금액</th>
							<th>연락처</th>
							<th>결제구분</th>
							<th>등록일</th>
							<th class="end" colspan="2">발송일</th>
						</tr>
						<tr>
							<th><input type="checkbox" name="chkAll"></th>
							<th>발주번호</th>
							<th>제품명</th>
							<th>단가</th>
							<th>수량</th>
							<th>매입합계</th>
							<th>납품처</th> 
							<th>비고2</th>
							<th>입고수량</th>
							<th class="end" colspan="2">입고처리일 / 취소여부</th>
						</tr>
					</thead>
					<tbody>

				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$rn							= trim($arr_rs[$j]["rn"]);
							$REQ_NO						= trim($arr_rs[$j]["REQ_NO"]);
							$GROUP_NO					= trim($arr_rs[$j]["GROUP_NO"]);
							$REQ_DATE					= trim($arr_rs[$j]["REQ_DATE"]);
							$BUY_CP_NO					= trim($arr_rs[$j]["BUY_CP_NO"]);
							$BUY_CP_NM					= trim($arr_rs[$j]["BUY_CP_NM"]);
							$BUY_MANAGER_NM				= trim($arr_rs[$j]["BUY_MANAGER_NM"]);
							$BUY_CP_PHONE				= trim($arr_rs[$j]["BUY_CP_PHONE"]);
							$TOTAL_REQ_QTY				= trim($arr_rs[$j]["TOTAL_REQ_QTY"]);
							$TOTAL_BUY_TOTAL_PRICE		= trim($arr_rs[$j]["TOTAL_BUY_TOTAL_PRICE"]);
							$REG_DATE					= trim($arr_rs[$j]["REG_DATE"]);
							$DELIVERY_TYPE				= trim($arr_rs[$j]["DELIVERY_TYPE"]);
							$IS_SENT					= trim($arr_rs[$j]["IS_SENT"]);
							$SENT_DATE					= trim($arr_rs[$j]["SENT_DATE"]);

							$CHECK_YN					= trim($arr_rs[$j]["CHECK_YN"]);
							
							if($SENT_DATE == "0000-00-00 00:00:00")
								$SENT_DATE = "";
							else
								$SENT_DATE = date("Y-m-d H:i", strtotime($SENT_DATE));

							$REQ_DATE = date("Y-m-d",strtotime($REQ_DATE));
							$REG_DATE = date("Y-m-d H:i",strtotime($REG_DATE));
				?>
						<tr height="30" class="top_group">
							<? if($IS_SENT != "Y") { ?>
							<td style="background-color:#ffcdcf;">
								<input type="checkbox" name="chk_no[]" value="<?=$REQ_NO?>">
							</td>
							<? } else { ?>
							<td>
								<a href="javascript:js_view('<?=$REQ_NO?>')"><?=$GROUP_NO?></a>
							</td>
							<? } ?>
							<td><?=$REQ_DATE?></td>
							<td class="modeual_nm"><a href="javascript:js_view('<?=$REQ_NO?>')"><?=$BUY_CP_NM?></a></td>
							<td class="modeual_nm"><a href="javascript:js_view('<?=$REQ_NO?>')"><?=$BUY_MANAGER_NM?></a></td>
							<td><?= number_format($TOTAL_REQ_QTY)?> </td>
							<td><?= number_format($TOTAL_BUY_TOTAL_PRICE)?> </td>
							<td><?=$BUY_CP_PHONE?></td>
							<?

								$arr_rs_cp=grlGetCompanyADTYPE($conn,$BUY_CP_NO);

								$rs_ad_type=SetStringFromDB($arr_rs_cp[0]["AD_TYPE"]);
								
								if($rs_ad_type==""){
									$rs_ad_type="미정";
								}


								// $arr_rs_company = selectCompany($conn, $BUY_CP_NO);
		
								// if(sizeof($arr_rs_company) > 0) { 

								// 	/*
								// 	$rs_cp_type							= SetStringFromDB($arr_rs_company[0]["CP_TYPE"]); 
								// 	$rs_cp_zip							= SetStringFromDB($arr_rs_company[0]["CP_ZIP"]); 
								// 	$rs_cp_addr							= SetStringFromDB($arr_rs_company[0]["CP_ADDR"]); 
								// 	$rs_biz_no							= SetStringFromDB($arr_rs_company[0]["BIZ_NO"]); 
								// 	$rs_ceo_nm							= SetStringFromDB($arr_rs_company[0]["CEO_NM"]); 
								// 	$rs_cp_phone						= SetStringFromDB($arr_rs_company[0]["CP_PHONE"]); 
								// 	*/

								// 	$rs_ad_type							= SetStringFromDB($arr_rs_company[0]["AD_TYPE"]); 

								// 	if($rs_ad_type == "")
								// 		$rs_ad_type = "미정";
								// }
							?>
							<td><b><span style='color:red; font-weight:bold;'><?=$rs_ad_type?></b></span></td>
							<td><?= $REG_DATE?> </td>
							<td colspan="2">
								<!--<?=$IS_SENT == "Y" ? "<font color='green'>".$SENT_DATE."</font>" : "<font color='red'>발송전</font>"?> ---20210520주석--->
								<? if($IS_SENT == "Y") 
									{
										if($CHECK_YN == "Y")
										{	
								?>			
											<input type="hidden" name="confirmReqNo_<?=$REQ_NO?>" value = "<?=$CHECK_YN?>"/>
											<a href="javascript:js_chk_confirm('<?=$REQ_NO?>', '<?=$GROUP_NO?>', '<?=$REQ_DATE?>')"><font color='blue'><span id="confirmCk_<?=$REQ_NO?>"><?=$SENT_DATE?></span></font></a>
								<?		}
										else
										{
								?>			<input type="hidden" name="confirmReqNo_<?=$REQ_NO?>" value = "<?=$CHECK_YN?>"/>
											<a href="javascript:js_chk_confirm('<?=$REQ_NO?>', '<?=$GROUP_NO?>', '<?=$REQ_DATE?>')"><font color='green'><span id="confirmCk_<?=$REQ_NO?>"><?=$SENT_DATE?></span></font></a>	
								<?		}
									 } else { ?>	
									<font color='red'>발송전</font>
								<? } ?>		
							</td>
						</tr>

				<?
							$goods_sum_sub_total = 0;
							$goods_sum_group_total = 0;
							$arr_rs_goods = listGoodsRequestGoods($conn, $REQ_NO, $con_cancel_tf);
							if (sizeof($arr_rs_goods) > 0) {
								
								for ($k = 0 ; $k < sizeof($arr_rs_goods); $k++) {

									$REQ_GOODS_NO				= trim($arr_rs_goods[$k]["REQ_GOODS_NO"]);
									$ORDER_GOODS_NO				= trim($arr_rs_goods[$k]["ORDER_GOODS_NO"]);
									$GOODS_NO					= trim($arr_rs_goods[$k]["GOODS_NO"]);
									$GOODS_NAME					= SetStringFromDB($arr_rs_goods[$k]["GOODS_NAME"]);
									$GOODS_SUB_NAME				= SetStringFromDB($arr_rs_goods[$k]["GOODS_SUB_NAME"]);
									$BUY_PRICE					= trim($arr_rs_goods[$k]["BUY_PRICE"]);
									$REQ_QTY					= trim($arr_rs_goods[$k]["REQ_QTY"]);
									$BUY_TOTAL_PRICE			= trim($arr_rs_goods[$k]["BUY_TOTAL_PRICE"]);
									$RECEIVE_QTY				= trim($arr_rs_goods[$k]["RECEIVE_QTY"]);
									$RECEIVE_DATE				= trim($arr_rs_goods[$k]["RECEIVE_DATE"]);
									$RECEIVER_NM				= trim($arr_rs_goods[$k]["RECEIVER_NM"]);
									$TO_HERE					= trim($arr_rs_goods[$k]["TO_HERE"]);
									$MEMO1						= trim($arr_rs_goods[$k]["MEMO1"]);
									$MEMO2						= trim($arr_rs_goods[$k]["MEMO2"]);

									$UP_DATE					= trim($arr_rs_goods[$k]["UP_DATE"]);
									$UP_ADM						= trim($arr_rs_goods[$k]["UP_ADM"]);
									
									$CANCEL_TF					= trim($arr_rs_goods[$k]["CANCEL_TF"]);
									$CANCEL_DATE				= trim($arr_rs_goods[$k]["CANCEL_DATE"]);
									$CANCEL_ADM					= trim($arr_rs_goods[$k]["CANCEL_ADM"]);

									$CONFIRM_TF					= trim($arr_rs_goods[$k]["CONFIRM_TF"]);
									$CONFIRM_DATE				= trim($arr_rs_goods[$k]["CONFIRM_DATE"]);

									$CHANGED_TF					= trim($arr_rs_goods[$k]["CHANGED_TF"]);

									$SUB_REG_DATE				= trim($arr_rs_goods[$k]["REG_DATE"]);

									if($RECEIVE_DATE != "0000-00-00 00:00:00")
										$RECEIVE_DATE = date("Y-m-d H:i",strtotime($RECEIVE_DATE));
									else
										$RECEIVE_DATE = "<font color='red'>입고전</font>";

									if($UP_DATE != "0000-00-00 00:00:00")
										$UP_DATE = date("Y-m-d",strtotime($UP_DATE));
									else
										$UP_DATE = "";

									if($SUB_REG_DATE != "0000-00-00 00:00:00")
										$SUB_REG_DATE = date("Y-m-d",strtotime($SUB_REG_DATE));
									else
										$SUB_REG_DATE = "";
								
									if($CANCEL_DATE != "0000-00-00 00:00:00")
										$CANCEL_DATE = date("Y-m-d",strtotime($CANCEL_DATE));
									else
										$CANCEL_DATE = "";

									if($CONFIRM_DATE != "0000-00-00 00:00:00" && $CONFIRM_TF == 'Y')
										$CONFIRM_DATE = "매출확정일시:".date("Y-m-d H:i",strtotime($CONFIRM_DATE));
									else
										$CONFIRM_DATE = "";

									if($CANCEL_TF == "Y")
										$str_cancel_style = "cancel_order";
									else { 
										$str_cancel_style = "";
										$goods_sum_sub_total += $BUY_TOTAL_PRICE;
									}

									if($CONFIRM_TF == "Y")  
										$str_confirm_style = "confirm_order";
									else  
										$str_confirm_style = "";
									
									if($TO_HERE != "Y") { 
										
										$arr_order_delivery_paper = getOrderGoodsDeliveryPaper($conn, $ORDER_GOODS_NO);
										
										if(sizeof($arr_order_delivery_paper) > 0) {
											$DELIVERY_NO = $arr_order_delivery_paper[0]["DELIVERY_NO"];
											$DELIVERY_CP = $arr_order_delivery_paper[0]["DELIVERY_CP"];
											$DELIVERY_DATE = $arr_order_delivery_paper[0]["DELIVERY_DATE"];

											//echo $DELIVERY_CP."//".$DELIVERY_NO."<br/>";
											
											
											if($DELIVERY_DATE != "0000-00-00 00:00:00" && $DELIVERY_DATE != "") { 
											
												$DELIVERY_DATE = date("Y-m-d H:i",strtotime($DELIVERY_DATE));
											}
											else
											{ 
												if($TO_HERE != "Y") { 
													$DELIVERY_DATE = "<font color='red'>직송배송완료전</font>";
												} else { 
													$DELIVERY_DATE = "<font color='red'>입고전</font>";
												}
											}
										} 
									} 
									
									if($TO_HERE == "Y" && $RECEIVE_QTY > 0 && $CANCEL_TF == "N") { 
										$RECEIVED_TF = true;
									} else
										$RECEIVED_TF = false;
									
				?>
					
						<tr height="30" class="<?=$str_cancel_style?> <?=$str_confirm_style?>" title="매입확정일:<?=$CONFIRM_DATE?>, 상품취소일:<?=$CANCEL_DATE."/".getAdminName($conn, $CANCEL_ADM)?>">
						
							<td>
								<? if($IS_SENT == "Y") { ?>
								<input type="checkbox" name="chk_rg_no[]" class="chkBoxReq" value="<?=$REQ_GOODS_NO?>" <?($CANCEL_TF == "Y" ? 'checked' : '')?>/>
								<input type="hidden" name="arr_received_tf[]" value="<?=$REQ_GOODS_NO?>|<?=$RECEIVED_TF?>"/>
								<? } ?>
								
							</td>
							<td><? if ($TO_HERE == "Y") {?>
								<a href="javascript:js_link_to_in_stock('<?=$REQ_GOODS_NO?>', '<?=$REQ_QTY?>', '<?=$SUB_REG_DATE?>');" style="font-weight:bold;">발주번호:<?=$REQ_GOODS_NO?></a>
								<? } else { ?>
									발주번호:<?=$REQ_GOODS_NO?>
								<? } ?>
							</td>
							<td class="modeual_nm">
								<span class="get_goods_info" data-goods_no="<?=$GOODS_NO?>"><?= $GOODS_NAME." ".$GOODS_SUB_NAME ?></span>
								<? if(strpos($MEMO1,"착불") > 0) { ?>
									<span style="color:red; font-size:11px;">/ 착불</span>
								<? } ?>
								<? if($CHANGED_TF == 'Y') { ?>
									<span style="color:red; font-size:11px;">(변경)</span>
								<? } ?>
							</td>
							<td><?= number_format($BUY_PRICE)?></td>
							<td><?= number_format($REQ_QTY)?></td>
							<td  class="row_buying" data-value="<?=$BUY_TOTAL_PRICE?>"><?= number_format($BUY_TOTAL_PRICE)?>
								<input type="hidden" name="cancleTf" class="cancleTf" data-value="<?=$CANCEL_TF?>" />
							</td>
							<? if ($TO_HERE == "Y") {?>
								<td class="modeual_nm"><?=$RECEIVER_NM?></td>
								<td class="modeual_nm"><?=$MEMO2?></td>
								<td>
									<a href="javascript:js_link_to_in_stock('<?=$REQ_GOODS_NO?>', '<?=$REQ_QTY?>', '<?=$SUB_REG_DATE?>');" style="font-weight:bold; ">
										<? if($REQ_QTY != $RECEIVE_QTY) { ?>
											<font color='red'><?=$RECEIVE_QTY ?></font>
										<? } else { ?>
											<font color='blue'><?=$RECEIVE_QTY ?></font>
										<? } ?>
									</a>
								</td>
								<td>
									<a href="javascript:js_link_to_in_stock('<?=$REQ_GOODS_NO?>', '<?=$REQ_QTY?>', '<?=$SUB_REG_DATE?>');" style="font-weight:bold;">
										<? if($REQ_QTY != $RECEIVE_QTY) { ?>
											<font color='red'><?= $RECEIVE_DATE ?></font>
										<? } else { ?>
											<font color='blue'><?= $RECEIVE_DATE ?></font>
										<? } ?>
									</a>
									<? if($CANCEL_TF == "Y") {?>
										/ <font color='red' title="">취소됨</font>
									<? } ?>
								</td>
							<? } else { ?>
								<td class="modeual_nm"><?="직송(".$RECEIVER_NM.")"?></td>
								<td class="modeual_nm"><?=$MEMO2?></td>
								<td>
									<? if ($DELIVERY_NO) {?>
										<a href="javascript:js_pop_delivery_paper_frame('<?=$DELIVERY_CP?>', '<?=$DELIVERY_NO?>');" style="font-weight:bold; color:blue;"><?=$DELIVERY_CP?>(<?=$DELIVERY_NO?>)</a>
									<? } else { 
										    if($CANCEL_TF != "Y") {  
									?>
										<input type="button" onclick="js_pop_company_extra('<?=$BUY_CP_NO?>');" value=" 송장 "/>
									<?		}  
										} 
									?>
								</td>
								<td>
									<font color='blue'><?= $DELIVERY_DATE ?></font>
									<? if($CANCEL_TF == "Y") {?>
										/ <font color='red'>취소됨</font>
									<? } ?>
								</td>
								
							<? } ?>
							<td>
								<!-- 2017-09-13 수령처에서 받았거나 직송건만 보이게 했었는데 젠체 보이게로 수정 //$RECEIVED_TF || $TO_HERE != "Y"
								-->
								<input type="button" name="bb" data-rgn_no="<?=$REQ_GOODS_NO?>" value="추가기장" onclick="js_extra_ledger(this);"/>
								
							</td>
							
						</tr>
						<? 

							
							$arr_sub = selectRequestGoodsSubLedger($conn, $REQ_GOODS_NO);
									
							if(sizeof($arr_sub) > 0) { 

							for($m = 0; $m < sizeof($arr_sub); $m ++) { 
								//GRGL_NO, REQ_GOODS_NO, NAME, QTY, UNIT_PRICE, CONFIRM_TF
								$GRGL_NO				= trim($arr_sub[$m]["GRGL_NO"]);
								$EXTRA_NAME				= trim($arr_sub[$m]["NAME"]);
								$EXTRA_QTY				= trim($arr_sub[$m]["QTY"]);
								$EXTRA_UNIT_PRICE		= trim($arr_sub[$m]["UNIT_PRICE"]);
								$EXTRA_MEMO				= trim($arr_sub[$m]["MEMO"]);
								$EXTRA_CONFIRM_TF		= trim($arr_sub[$m]["CONFIRM_TF"]);
								
								if($EXTRA_CONFIRM_TF == "Y")  
									$str_sub_confirm_style = "confirm_order";
								else  
									$str_sub_confirm_style = "";

								$goods_sum_group_total += ($EXTRA_UNIT_PRICE * $EXTRA_QTY);

						?>
						<tr class="extra_ledger group_<?=$REQ_GOODS_NO?> <?=$str_sub_confirm_style?>" style="height:30px;">
							<td colspan="2"><input type="hidden" name="arr_option_rgnl_no[]" value="<?=$GRGL_NO?>"/>추가번호:<?=$GRGL_NO?></td>
							<td class="modeual_nm"><?=$EXTRA_NAME?></td>
							<td><?=number_format($EXTRA_UNIT_PRICE)?></td>
							<td><?=number_format($EXTRA_QTY)?></td>
							<td class="row_buying" data-value="<?=($EXTRA_QTY  * $EXTRA_UNIT_PRICE)?>"><?=number_format($EXTRA_QTY  * $EXTRA_UNIT_PRICE)?></td>
							<td><?=$EXTRA_MEMO?></td>
							<td colspan="3"></td>
							<td><input type="button" name="b" onclick="js_delete_option('<?=$GRGL_NO?>');" value="삭제"/></td>
						</tr>
						<?	
							} 
						} 
						?>
						<!--$RECEIVED_TF || $TO_HERE != "Y" -->
						<tr data-rgn_no="<?=$REQ_GOODS_NO?>" class="extra_ledger" style="display:none; height:30px;" >
							<td colspan="11" class="line add_here">
								<div class="options">
									<input type="hidden" name="arr_option_rgn_no[]" value="<?=$REQ_GOODS_NO?>"/>
									<input type="text" name="arr_option_name[]" value="" placeholder="기장명" />
									<input type="text" name="arr_option_qty[]" value="" placeholder="수량" style="width:40px;" />
									<input type="text" name="arr_option_price[]" value="" placeholder="단가" style="width:80px;" />
									<input type="text" name="arr_option_memo[]" value="<?=$MEMO2?>" placeholder="비고" style="width:200px;" />
									<a onclick="js_append_option(this); return false;" style="color:blue; text-decoration:underline;">추가</a>
									<a onclick="js_cancel_option(this); return false;" style="color:blue; text-decoration:underline;">취소</a>
								</div>
							</td>
						</tr>
						
				<?					
									$DELIVERY_NO = "";
									$DELIVERY_CP = "";
									$DELIVERY_DATE = "";
								}
							}

							if($goods_sum_group_total <> 0) { 
				?>
					<tr class="extra_ledger row_goods_sum rgn_<?=$REQ_GOODS_NO?>" style="height:30px;">
						<td colspan="2"></td>
						<td class="modeual_nm">소계 : </td>
						<td></td>
						<td></td>
						<td><?=number_format($goods_sum_sub_total + $goods_sum_group_total)?></td>
						<td></td>
						<td colspan="3"></td>
						<td></td>
					</tr>					

				<?
							}
						}
					} else { 
				?> 
						<tr>
							<td align="center" height="50" colspan="11">데이터가 없습니다. </td>
						</tr>
				<? 
					}
				?>
					<tr class="selected display_none" style="height:30px;">
						<td colspan="2"></td>
						<td class="modeual_nm">선택 총액 : </td>
						<td></td>
						<td></td>
						<td><span id="total_buying"></span></td>
						<td></td>
						<td colspan="3"></td>
						<td></td>
					</tr>
					</tbody>
				</table>
				<script>
					$(function(){
						$(".get_goods_info").click(function(){
							var goods_no = $(this).data("goods_no");
							var clicked_obj = $(this);

							$.getJSON( "../goods/json_goods_info.php?goods_no=" + encodeURIComponent(goods_no) + "&mode=" + encodeURIComponent("DELIVERY_CNT_IN_BOX"), function(data) {
								if(data != undefined) { 
									if(data.length == 1) 
										clicked_obj.append("<span class='box_cnt'>(박스입수:" + data[0].RESULT + ")</span>");
									else {
										clicked_obj.closest("td").find(".box_cnt").remove();
									}
								}
							});

							//상품정보로 새창열기
							//window.open("/manager/goods/goods_write.php?mode=S&goods_no=" + goods_no);

						});

					});
				</script>

				<script>
				
					function js_extra_ledger(elem) { 

						var frm = document.frm;
						
						if($(elem).val() == "추가기장") { 

							var rgn_no = $(elem).data("rgn_no");
							$("tr[data-rgn_no="+rgn_no+"]").show();
							$(elem).val("저장");

						} else if($(elem).val() == "저장") {  

							frm.mode.value = "UPDATE_EXTRA";
							frm.target = "";
							frm.method = "post";
							frm.action = "<?=$_SERVER[PHP_SELF]?>";
							frm.submit();
						}
					}

					function js_delete_option(grgl_no) { 

						var frm = document.frm;

						frm.mode.value = "DELETE_EXTRA";
						frm.target = "";
						frm.grgl_no.value = grgl_no;
						frm.method = "post";
						frm.action = "<?=$_SERVER[PHP_SELF]?>";
						frm.submit();
					}

					function js_append_option(elem) { 
						var copied = $(elem).closest(".options").clone();
						copied.find("input[type=select]").val('');
						copied.find("input[type=text][name!='arr_option_memo[]']").val('');
						$(elem).closest(".add_here").append(copied);
					}

					function js_cancel_option(elem) {
						$(elem).closest(".options").remove();
					}


					$(function(){
						

						function calcSelectedLedger() { 
							var total_buying = 0;
							
							$("input[name='chk_rg_no[]']").each(function(){
								
								var rgn_no = $(this).val();
								if($(this).prop('checked')==true) { 
									
									var buying = 0;
				
									cancleTf=$(this).closest("tr").find("input[name='cancleTf']").data("value");
				
									if(cancleTf != "Y")
									{
										buying = $(this).closest("tr").find("td.row_buying").data("value");

										$(".group_" + rgn_no).each(function(){
											buying = parseFloat(buying) + parseFloat($(this).find("td.row_buying").data("value"));
										});
									}
											
									total_buying += parseFloat(buying);
								}
								
							});

							//alert("total_buying : " + total_buying); 

							if(total_buying != 0) {
								$(".selected").show();
								$("#total_buying").html(numberFormat(total_buying));
							} else { 
								$(".selected").hide();
								$("#total_buying").html("");
							}

						}
						
						$("input[name='chk_rg_no[]']").click(function(){
							calcSelectedLedger();
						});
						$("input[name='chkAll']").click(function(){
							js_all_check();

						});

					});

				</script>

					<!-- --------------------- 페이지 처리 화면 START -------------------------->
					<?
						# ==========================================================================
						#  페이징 처리
						# ==========================================================================
						if (sizeof($arr_rs) > 0) {
							#$search_field		= trim($search_field);
							#$search_str			= trim($search_str);

							$strParam = "";
							$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str;
							$strParam = $strParam."&start_date=".$start_date."&end_date=".$end_date."&con_cp_type=".$con_cp_type;
							$strParam = $strParam."&con_delivery_tf=".$con_delivery_tf."&con_to_here=".$con_to_here."&con_cancel_tf=".$con_cancel_tf."&con_confirm_tf=".$con_confirm_tf."&con_changed_tf=".$con_changed_tf."&con_wrap_tf=".$con_wrap_tf."&con_sticker_tf=".$con_sticker_tf."&con_receive_tf=".$con_receive_tf."&chk_after_confirm=".$chk_after_confirm;
							$strParam = $strParam."&con_payment=".$con_payment;

					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- 페이지 처리 화면 END -------------------------->
			</div>
			<!-- // E: mwidthwrap -->
			<div class="sp50"></div>


		</td>
	</tr>
	</table>
	<div style="display:scroll;position:fixed;bottom:10px;right:10px;padding:10px;border:1px solid black;background-color:#fff;">
		
		<? if ($sPageRight_D == "Y" && $s_adm_cp_type == "운영") {?>
			매입확정일 : <input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="base_confirm_date" value="<?=$base_confirm_date?>" maxlength="10"/>
			<input type="button" name="aa" value=" 매입확정 " class="btntxt" onclick="js_confirm('Y');">
			<input type="button" name="aa" value=" 매입취소 " class="btntxt" onclick="js_confirm('N');"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="button" name="aa" value=" 선택한 (발송전)발주서 전체삭제 " class="btntxt" onclick="js_delete();">&nbsp;&nbsp;&nbsp;
			<input type="button" name="aa" value=" 선택한 발주 상품 취소/복구 " class="btntxt" onclick="js_cancel();">
		<? } ?>

		<a href="#">▲ 위로</a>
	</div>
</div>
<iframe src="about:blank" name="ifr_hidden" id="ifr_hidden" frameborder="no" width="100%" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>
<?
	function grlGetCompanyADTYPE($db, $cpNo){
		$query = "SELECT AD_TYPE
		FROM TBL_COMPANY WHERE CP_NO = '$cpNo' ";
		//echo $query;
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}
?>