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
	$menu_right = "CF007"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/confirm/confirm.php";
	require "../../_classes/biz/company/company.php";

#====================================================================
# Request Parameter
#====================================================================
	$mode			= trim($mode);
	$cp_type		= trim($cp_type);

	//$strParam = "?start_date=".$start_date."&end_date=".$end_date."&cp_type=".$cp_type;
	/*
	$inout_date				= SetStringToDB($inout_date);
	$inout_type				= SetStringToDB($inout_type);
	$name					= SetStringToDB($name);
	$qty					= SetStringToDB($qty);
	$unit_price				= SetStringToDB($unit_price);
	$withdraw				= SetStringToDB($withdraw);
	$deposit				= SetStringToDB($deposit);
	$reserve_no				= SetStringToDB($reserve_no);
	$order_goods_no			= SetStringToDB($order_goods_no);
	$rgn_no					= SetStringToDB($rgn_no);
	*/
	
	$result	= false  ;

#====================================================================
# DML Process
#====================================================================

	if ($mode == "D") {
		$row_cnt = count($chk_no);

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_cl_no = $chk_no[$k];
			
			$result = deleteCompanyLedger($conn, $str_cl_no, $s_adm_no);
		
		}
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
?>
<script type="text/javascript">
	
	if(confirm('저장되었습니다. 같은 업체에 계속 기장하실꺼면 취소를 눌러주세요.'))
		document.location = "company_ledger_write_method.php?inout_date=<?=$inout_date?>";
	else
		document.location = "company_ledger_write_method.php?cp_type=<?=$cp_type?>&inout_date=<?=$inout_date?>&inout_type=<?=$inout_type?>";
		

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

	if($inout_date == "") 
		$inout_date			= date("Y-m-d",strtotime("0 month"));
	

	$arr_rs_company = selectCompany($conn, $cp_type);
	
	if(sizeof($arr_rs_company) > 0) { 
		$rs_cp_zip							= SetStringFromDB($arr_rs_company[0]["CP_ZIP"]); 
		$rs_cp_addr							= SetStringFromDB($arr_rs_company[0]["CP_ADDR"]); 
		$rs_biz_no							= SetStringFromDB($arr_rs_company[0]["BIZ_NO"]); 
		$rs_ceo_nm							= SetStringFromDB($arr_rs_company[0]["CEO_NM"]); 
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js?v=5"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<script type="text/javascript" src="../jquery/jquery.floatThead.min.js"></script>
<script>
	var chkHide=1;
	$(document).ready(function(){
		if(chkHide==0){
			$('input[name=chkHiddenContent]').show();
		}
		else{
			$('input[name=chkHiddenContent]').hide();
		}

		fixedHeader();
		calcSelectedLedger();
		$("input[name='chk_no[]'], input[name=all_chk]").click(function(){
			calcSelectedLedger();
		});
		$('input[name=chkHiddenContent]').change(function(){
			//alert("changed");
			if($('input[name=chkHiddenContent]').is(":checked")){
				$('#tblSecret').hide();
			}
			else{
				$("#tblSecret").show();
			}
		});
		
		$(document).on('keypress',function(e) {

                if(e.which == 32) {
					if(chkHide==0){
						chkHide=1;
					}
					else{
						chkHide=0;
					}
					if(chkHide==0){
						$('input[name=chkHiddenContent]').show();
					}
					else{
						$('input[name=chkHiddenContent]').hide();
					}
                }
            });



		function calcSelectedLedger() {
			var tot_qty = 0;
			var tot_deposit = 0;
			var tot_withdraw = 0;
			var tot_surtax = 0;

			$("input[name='chk_no[]']").each(function(){
				if($(this).prop('checked') == true) {
					var qty			= $(this).closest("tr").find("td.qty").data("value");
					var deposit		= $(this).closest("tr").find("td.deposit").data("value");
					var withdraw	= $(this).closest("tr").find("td.withdraw").data("value");
					var surtax		= $(this).closest("tr").find("td.surtax").data("value");

					qty				= parseInt(qty);
					deposit			= parseInt(deposit);
					withdraw		= parseInt(withdraw);
					surtax			= parseInt(surtax);

					tot_qty				+= qty;
					tot_deposit			+= deposit;
					tot_withdraw		+= withdraw;
					tot_surtax			+= surtax;
				}
			});
			
			if(tot_qty != 0 || tot_deposit != 0 || tot_withdraw != 0 || tot_surtax != 0) {
				$(".selected").show();
				$("#tot_qty").html(numberFormat(tot_qty));
				$("#tot_deposit").html(numberFormat(tot_deposit));
				$("#tot_withdraw").html(numberFormat(tot_withdraw));
				$("#tot_surtax").html(numberFormat(tot_surtax));
			} else {
				$(".selected").hide();
				$("#tot_qty").html("");
				$("#tot_deposit").html("");
				$("#tot_withdraw").html("");
				$("#tot_surtax").html("");
			}
		}

		$("#scroll_wrapper").scroll(function() {
			var scrollHeight = $("#scroll_wrapper").prop("scrollHeight");
			var clientHeight = $("#scroll_wrapper").prop("clientHeight");
			var scrollTop = $("#scroll_wrapper").prop("scrollTop");
			// $("#status").html("scrollHeight : " + scrollHeight + " / clientHeight : " + clientHeight + " / scrollTop : " + scrollTop);
			if(scrollHeight - scrollTop === clientHeight){
				//최근 기장 내역 AJAX로 10개 불러옴
				//현재 chk_no[] 개수
				var offset = $("input[name='chk_no[]']").length;
				$.ajax({
					url: '/manager/ajax_processing.php',
					dataType: 'json',
					type: 'post',
					data : {
						'mode': "SELECT_RECENT_LEDGER_HISTORY",
						'offset': offset,
						's_adm_no': '<?=$s_adm_no?>',
						'start_date' : '<?=$start_date?>',
						'end_date' : '<?=$end_date?>',
						'cp_type' : '<?=$cp_type?>',
						'order_field' : 'REG_DATE' ,
						'order_str' : 'DESC' ,
						'search_field' : 'LATEST_5_BY_REG_ADM' ,
						'nRowCount' : 10
					},
					success: function(response) {
						if(response != false){
							for(var i=0;i<response.length;i++){
								CL_NO 				= "";
								CP_NO 				= "";
								TO_CP_NO 			= "";
								INOUT_DATE 			= "";
								INOUT_TYPE 			= "";
								GOODS_NO 			= "";
								NAME 				= "";
								QTY 				= "";
								UNIT_PRICE 			= "";
								WITHDRAW 			= "";
								DEPOSIT 			= "";
								SURTAX 				= "";
								MEMO 				= "";
								RESERVE_NO 			= "";
								ORDER_GOODS_NO 		= "";
								RGN_NO 				= "";
								TAX_CONFIRM_TF 		= "";
								TAX_CONFIRM_DATE 	= "";
								USE_TF 				= "";
								CATE_01 			= "";
								TAX_TF 				= "";
								CF_CODE 			= "";
								INPUT_TYPE 			= "";

								CL_NO 				= response[i]["CL_NO"];
								CP_NO 				= response[i]["CP_NO"];
								TO_CP_NO 			= response[i]["TO_CP_NO"];
								INOUT_DATE 			= response[i]["INOUT_DATE"];
								INOUT_TYPE 			= response[i]["INOUT_TYPE"];
								GOODS_NO 			= response[i]["GOODS_NO"];
								NAME 				= response[i]["NAME"];
								QTY 				= response[i]["QTY"];
								UNIT_PRICE 			= response[i]["UNIT_PRICE"];
								WITHDRAW 			= response[i]["WITHDRAW"];
								DEPOSIT 			= response[i]["DEPOSIT"];
								SURTAX 				= response[i]["SURTAX"];
								MEMO 				= response[i]["MEMO"];
								RESERVE_NO 			= response[i]["RESERVE_NO"];
								ORDER_GOODS_NO 		= response[i]["ORDER_GOODS_NO"];
								RGN_NO 				= response[i]["RGN_NO"];
								TAX_CONFIRM_TF 		= response[i]["TAX_CONFIRM_TF"];
								TAX_CONFIRM_DATE 	= response[i]["TAX_CONFIRM_DATE"];
								USE_TF 				= response[i]["USE_TF"];
								CATE_01 			= response[i]["CATE_01"];
								TAX_TF 				= response[i]["TAX_TF"];
								CF_CODE 			= response[i]["CF_CODE"];
								INPUT_TYPE 			= response[i]["INPUT_TYPE"];

								var temp = "<tr height='30'>\
												<td><input type='checkbox' name='chk_no[]' value='"+CL_NO+"'/></td>\
												<td>"+INOUT_DATE+"</td>\
												<td>"+INOUT_TYPE+"</td>\
												<td class='modeual_nm'>"+NAME+"</td>\
												<td class='price qty' data-value='"+QTY+"'>"+QTY+"</td>\
												<td class='price' data-value='"+UNIT_PRICE+"'>"+numberFormat(UNIT_PRICE)+"</td>\
												<td class='price deposit' data-value='"+DEPOSIT+"'>"+numberFormat(DEPOSIT)+"</td>\
												<td class='price withdraw' data-value='"+WITHDRAW+"'>"+numberFormat(WITHDRAW)+"</td>\
												<td class='price surtax' data-value='"+SURTAX+"'>"+numberFormat(SURTAX)+"</td>\
												<td>"+MEMO+"</td>\
												<td><a href='javascript:js_link_to_company_ledger("+CP_NO+");'>"+CP_NO+"</a></td>\
												<td><a href='javascript:js_link_to_company_ledger("+TO_CP_NO+");'>"+TO_CP_NO+"</td>\
											</tr>";
								$("#recentLedgerHistory:last").append(temp);
							}
						} else{
							if(response.length == 0){
								alert("불러올 내역이 없습니다.");
							}
							else {
								alert("실패하였습니다.");
							}
						}
					}, error: function(jqXHR, textStatus, errorThrown) {
						console.log(jqXHR.responseText); 
					}
				}).done(function(){
					$(function(){
						$("input[name='chk_no[]'], input[name=all_chk]").click(function(){
							calcSelectedLedger();
						});
					});
				});
			}
		});
	});

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
  
	$(function() {
		$('.autocomplete_off').attr('autocomplete', 'off');
	});

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

	function fixedHeader(){
		$('table.fixed_header_table').floatThead({
			position: 'fixed'
		});
	}
</script>
<script language="javascript">

	function js_delete() {
		var frm = document.frm;

		bDelOK = confirm('정말로 삭제하시겠습니까?');
		
		if (bDelOK==true) {
			
			frm.mode.value = "D";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}


	// 저장 버튼 클릭 시 
	function js_save() {
		
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

	function js_list() {

		location.href = "company_ledger_list.php<?=$strParam?>";
	}

	function js_calculate_surtax() {

		var i_susu_rate			= 0;
		var i_unit_price		= 0;
		var f_surtax			= 0;

		if ($("input[name=susu_rate]").val() != "") i_susu_rate = parseFloat($("input[name=susu_rate]").val().replaceall(",", ""));

		if ($("input[name=unit_price]").val() != "") i_unit_price = parseInt($("input[name=unit_price]").val().replaceall(",", ""));

		if(i_unit_price == "0") return;
		if($("input[name=inout_method]:checked").val() != "카드") return;

		f_surtax =  Math.round10(i_unit_price * i_susu_rate / 100.0, 0); 

		var unit_price = i_unit_price - f_surtax;

		$("input[name=surtax]").val(numberFormat(f_surtax));
		$("input[name=unit_price]").val(numberFormat(unit_price));

	}

	function js_calculate_show_price() { 

		var i_prev_balance = 0;
		var i_next_balance = 0;
		
		var i_current_price = $("input[name=unit_price]").val().replaceall(",", "");

		if(i_current_price == "NaN")
			$("input[name=unit_price]").val('');

		if(!$.isNumeric(i_current_price)) { 
			//$("input[name=unit_price]").val('');
			$(".remain_balance").html("...");
			return;
		}


		if ($(".get_balance").html() != "...") i_prev_balance = parseInt($(".get_balance").html().replaceall(",", "").replaceall("원", ""));
		
		if($("input[name=inout_type]:checked").val() == "지급" || $("input[name=inout_type]:checked").val() == "입금") { 
			if($("input[name=inout_type]:checked").val() == "지급") 
				i_next_balance = parseInt(i_prev_balance) + parseInt(i_current_price);
			else 
				i_next_balance = parseInt(i_prev_balance) - parseInt(i_current_price);

			$(".remain_balance").html(numberFormat(i_next_balance) + " 원");
		}
		else
			$(".remain_balance").html("...");
		
	}

	function js_link_to_company_ledger(cp_no) {

		window.open("/manager/confirm/company_ledger_list.php?cp_type=" + cp_no,'_blank');
		
	}

</script>
<style>
.input-disabled{
	background-color:#EBEBE4;
	border:1px solid #ABADB3;
	padding:2px 1px;
	color:rgb(84, 84, 84);
}
#scroll_wrapper {
	z-index: 1;
	/* overflow: auto; */
	width: 95%;
	height:330px;
	border:1px solid #d1d1d1;
	overflow: scroll;
	overflow-y: visible;
	overflow-x: hidden;
}
table.rowstable {
    width: 100%;
}
.btnright{
	margin: 0px 0px 10px 0px !important;
}
table td.contentarea h2 {
	margin: 0 0 0px 0;
}

</style>

</head>
<body id="admin">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="">
<input type="hidden" name="start_date" value="<?=$start_date?>">
<input type="hidden" name="end_date" value="<?=$end_date?>">

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
				<h2>입금/지급/대체 기장</h2>  
				<div class="sp10"></div>

				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="16%">
					<col width="34%">
					<col width="16%">
					<col width="34%">
				</colgroup>
					<tr>
						<th>업체</th>
						<td class="line">
							<input type="text" class="autocomplete_off" style="width:90%" placeholder="업체(명/코드) 입력후 엔터를 눌러주세요" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'',$cp_type)?>" />
						<input type="hidden" name="cp_type" value="<?=$cp_type?>">

						<script>
							$(function(){

								$("input[name=txt_cp_type]").keydown(function(e){

									if(e.keyCode==13) { 

										var keyword = $(this).val();
										if(keyword == "") { 
											$("input[name=cp_type]").val('');
										} else { 
										
											$.getJSON( "../company/json_company_list.php?term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE,CEO_NM", function(data) {
												if(data.length == 1) { 
													
													js_selecting_company("txt_cp_type", data[0].label, "cp_type", data[0].id, {'DC_RATE': data[0].dc_rate, 'CP_TYPE': data[0].cp_type});

												} else if(data.length > 1){ 
													NewWindow("../company/pop_company_searched_list.php?search_str="+keyword + "&target_name=txt_cp_type&target_value=cp_type",'pop_company_searched_list','950','650','YES');

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

								js_selecting_company('txt_cp_type', '<?=getCompanyAutocompleteTextBox($conn,'',$cp_type)?>', 'cp_type', '<?=$cp_type?>', null);

							});

							function js_selecting_company(target_name, cp_nm, target_value, cp_no, cp_options = null) {

								$(function(){

									$("input[name="+target_name+"]").val(cp_nm);
									$("input[name="+target_value+"]").val(cp_no);

									if(cp_options != null) {
										
										if(target_value == "cp_type") { 
											if(cp_options.CP_TYPE == "구매")
												$("input[name=inout_type][value=지급]").prop("checked", true);

											if(cp_options.CP_TYPE == "판매")
												$("input[name=inout_type][value=입금]").prop("checked", true);

											$(".get_balance").click();
										} 
									}

									if(cp_no != '') {
										$.getJSON( "../company/json_company_list.php?cp_no=" + cp_no, function(data) {
											if(data[0].CP_NO != 'undefined') {
												$("#cptype").html(data[0].CP_TYPE);
												$("#biz_no").html(data[0].BIZ_NO);
												$("#ceo_nm").html(data[0].CEO_NM);
												$("#cp_zip").html(data[0].RE_ZIP);
												$("#cp_addr").html(data[0].RE_ADDR);
												$("#account_bank").html(data[0].ACCOUNT_BANK);
												$("#account").html(data[0].ACCOUNT);
												$("#account_owner_nm").html(data[0].ACCOUNT_OWNER_NM);
												$(".link").html('<input type="button" value="거래원장" class="btntxt" onclick="js_link_to_company_ledger('+cp_no+');"/>');
											}
										});
									} 
								});
							}
						</script>
						</td>
						<th> 업체 정보<div class="link"></div></th>
						<td class="line">
							<b>사업자 번호</b> : <span id="biz_no"></span>, <b>대표자 명</b> : <span id="ceo_nm"></span><br/><br/>
							<b>대표 주소</b> : <span id="cp_zip"></span> <span id="cp_addr"></span><br/><br/>
							<b>계좌정보</b> : <span id="account_bank"></span> <span id="account"></span> <span id="account_owner_nm"></span>
						</td>
					</tr>
					<tr>
						<th> 전 잔액</th>
						<td class="line">
							<span class="get_balance">...</span>
						</td>
						<th>예상 잔액</th>
						<td class="line">
							<span class="remain_balance">...</span>
						</td>
					</tr>
				</table>
				<div class="sp10"></div>
		
				<table cellpadding="0" cellspacing="0" class="colstable">
					<colgroup>
						<col width="16%">
						<col width="34%">
						<col width="16%">
						<col width="34%">
					</colgroup>
					<tr height="40">
						<th>날짜</th>
						<td class="line">
							<input type="Text" name="inout_date" value="<?=$inout_date?>" style="width:80px; margin-right:3px;" class="txt datepicker">
						</td>
						<th>입금/지급</th>
						<td>
							<input type = 'radio' name= 'inout_type' value='입금' <? if($inout_type=="입금") echo "checked"; ?> /><label> 입금 </label>
							<input type = 'radio' name= 'inout_type' value='지급' <? if($inout_type=="지급") echo "checked"; ?> /><label> 지급 </label>
						</td>
					</tr>

					<tr height="40">
						<th rowspan="5">
							방식
						</th>							
							<td>
								<input type = 'radio' name= 'inout_method' value='현금'><label> 현금 </label>
							</td>
						<th>업체구분</th>
							<td>
								<span id="cptype"></span>
							</td>
					</tr>
					<tr height="40">
						<td colspan="3">
							<label><input type = 'radio' name= 'inout_method' value='통장'> 통장 </label>
							&nbsp;&nbsp;&nbsp;
							<?=makeCompanySelectBoxAsCpNoWithName($conn, '통장', 'bank_no', '')?>
													
						</td>
					</tr>
					<tr height="40">
						<td colspan="3">
							<label><input type = 'radio' name= 'inout_method' value='대체'> 대체 </label>
							&nbsp;&nbsp;&nbsp;
							<input type="Text" name="to_cp_nm" value="" autocomplete="off" class="txt" placeholder="업체 검색어">
							<input type="button" name="show_to_cp_no" value="대체선택"/>
							<input type="hidden" name="to_cp_no" value=""/>
							
						</td>
					</tr>
					<tr height="40">
						<td rowspan="2" >
							<label><input type = 'radio' name= 'inout_method' value='카드'> 카드 </label>
							&nbsp;&nbsp;&nbsp;
							<?=makeCompanySelectBoxAsCpNoWithName($conn, '카드', 'card_no', '')?>
							
						</td>
						<th>수수료율</th>
						<td><input type="Text" name="susu_rate" value="0" class="txt" onChange="js_calculate_surtax()" >%</td>
					</tr>
					<tr height="40">
						<th>수수료</th>
						<td><input type="Text" name="surtax" value="0" class="txt">원</td>
					</tr>
				</table>


				<div class="sp10"></div>
		
				<table cellpadding="0" cellspacing="0" class="colstable">
					<colgroup>
						<col width="16%">
						<col width="34%">
						<col width="16%">
						<col width="34%">
					</colgroup>
					<tr height="40">
						<th>
							금액
						</th>
						<td>
							<input type="Text" name="unit_price" value="<?= $unit_price ?>"  required onChange="js_calculate_surtax()" class="txt">
						</td>
						<th>비고</th>
						<td>
							<input type="Text" name="memo" value="<?= $RS_MEMO ?>" class="txt">
							<?
								if($s_adm_no==58 || $s_adm_no==1){
									echo"<input type='checkbox' name='chkHiddenContent'/>";
								}
								
							?>
						</td>
					</tr>
				</table>
				<script>
					//전 잔액 계산
					$(function(){
						$(".get_balance").click(function(){
							var cp_no = $("input[name=cp_type]").val();
							var clicked_obj = $(this);

							$.getJSON( "../confirm/json_company_ledger.php?cp_no=" + encodeURIComponent(cp_no), function(data) {
								if(data != undefined) { 
									if(data.length == 1) 
										clicked_obj.html(numberFormat(data[0].SUM_BALANCE) + " 원");
									else {
										clicked_obj.html("검색결과가 없습니다.");
									}
								}
							});
						});

						$(".get_balance").click();
					});

					//통장 방식 선택되게	
					$("select[name=bank_no]").change(function(){
						$("input[name=inout_method][value=통장]").prop("checked", true);
					});
					 
					//대체에서 엔터치면 자동 팝업 검색 
					$("input[name=to_cp_nm]").keydown(function(e){
						if(e.keyCode == 13){
							$("input[name=show_to_cp_no]").click();
						}
					});

					//대체 팝업 검색
					$("input[name=show_to_cp_no]").click(function(e){

						$("input[name=inout_method][value=대체]").prop("checked", true);
						
						var keyword = $("input[name=to_cp_nm]").val();

						if(keyword != "")
							NewWindow("../company/pop_company_searched_list.php?search_str="+keyword+"&target_name=to_cp_nm&target_value=to_cp_no&con_cp_type=",'pop_company_searched_list','950','650','YES');
						else
							alert("기장명에 검색어를 넣어주세요.");

					});

					 //카드 자동 선택 + 수수료율에 따른 수수료 계산
					 $("select[name=card_no]").change(function(){
						$("input[name=inout_method][value=카드]").prop("checked", true);

						var cp_no = $("select[name=card_no]").val();
						if(cp_no != '') {
							$.getJSON( "../company/json_company_list.php?cp_no=" + cp_no, function(data) {
								if(data[0].CP_NO == 'undefined') {
									$("input[name=susu_rate]").val('검색안됨');
								} else {
									$("input[name=susu_rate]").val(data[0].DC_RATE);
									js_calculate_surtax();
								}
							});
						} 
					 });
						
					 //금액에서 비고로 포커스 이동, 잔액 계산
					 $("input[name=unit_price]").keydown(function(e){
						
						if(e.keyCode == 13){
							$("input[name=memo]").focus();
						}
					 });

					 //금액에서 비고로 포커스 이동, 잔액 계산
					 $("input[name=unit_price]").keyup(function(e){
						
						var unit_price = $(this).val().replaceall(",", "");
						
						//-만 눌렀을때는 처리 안함
						if(unit_price != "-" && unit_price != "") { 
							$(this).val(numberFormat(unit_price));	
						}

						js_calculate_show_price();
						
						//
						//	$(this).val(0);
						
						
					 });

					//비고에서 엔터치면 저장
					$("input[name=memo]").keydown(function(e){
						if(e.keyCode == 13){
							js_save();
						}
						
						js_calculate_show_price();
					});

					//화면 기본 포커스로 업체 선택
					$("input[name=txt_cp_type]").focus();

				</script>
			<div class="sp5"></div>
			<div class="btnright">
				<? if ($sPageRight_I == "Y") {?>
					<a href="javascript:js_save();" class="btn_insert"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
				<? } ?>
			</div>  
			
			<div style="width:95%;text-align:center;">
				<h2 style="float: left;"> 최근 기장 내역 </h2>
				<!-- <div id="status"></div> -->
				<!-- <div style="width: 95%; text-align: left; margin: 0;">
					<label><input type="checkbox" name="show_recently" checked value="Y"/>보기</label>
					<script type="text/javascript">
						$(function(){
							$("[name='show_recently']").change(function(){
								$(".recently_list").toggle();
							});
						});
					</script>
				</div> -->
				<div class="btnright recently_list" style="width:30%;float: right;">
					<? if ($sPageRight_D == "Y") {?>
						<input type="button" name="aa" value=" 선택한 기장 삭제 " class="btntxt" onclick="js_delete();"> 
					<? } ?>

					<!-- <label>내용 숨기기</label> -->
				</div>
			</div>
			<div id ="scroll_wrapper">
				<table cellpadding="0" cellspacing="0" id="tblSecret" class="rowstable fixed_header_table recently_list" border="0">

					<colgroup>
						<col width="3%" />
						<col width="8%" />
						<col width="3%" />
						<col width="*"/>
						<col width="3%" />
						<col width="8%" />
						<col width="8%" />
						<col width="8%" />
						<col width="10%" />
						<col width="8%" />
						<col width="11%" />
						<col width="11%" />
					</colgroup>
					<thead>
					<tr>
						<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
						<th>날짜</th>
						<th>구분</th>
						<th>상품명</th>
						<th>수량</th>
						<th>단가</th>
						<th>매출/지급액</th>
						<th>매입/입금액</th>
						<th>부가세</th>
						<th>비고</th>
						<th>업체</th>
						<th class="end">대입처</th>
					</tr>
					</thead>
					<tbody id="recentLedgerHistory">
					<tr class="selected" height="30">
						<td colspan="4">선택 총액</td>
						<td id="tot_qty" class="price"></td>
						<td></td>
						<td id="tot_deposit" class="price"></td>
						<td id="tot_withdraw" class="price"></td>
						<td id="tot_surtax" class="price"></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<?
						$arr_rs = listCompanyLedger($conn, $start_date, $end_date, $cp_type, $order_field = "REG_DATE", $order_str = "DESC", $search_field = "LATEST_5_BY_REG_ADM", $search_str = $s_adm_no, $nRowCount = 10);

						if (sizeof($arr_rs) > 0) {
							for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
								$CL_NO						= trim($arr_rs[$j]["CL_NO"]);
								$CP_NO						= trim($arr_rs[$j]["CP_NO"]);
								$TO_CP_NO					= trim($arr_rs[$j]["TO_CP_NO"]);
								$INOUT_DATE					= trim($arr_rs[$j]["INOUT_DATE"]);
								$INOUT_TYPE					= trim($arr_rs[$j]["INOUT_TYPE"]);
								$NAME						= trim($arr_rs[$j]["NAME"]);
								$QTY						= trim($arr_rs[$j]["QTY"]);
								$UNIT_PRICE					= trim($arr_rs[$j]["UNIT_PRICE"]);
								$WITHDRAW					= trim($arr_rs[$j]["WITHDRAW"]);
								$DEPOSIT					= trim($arr_rs[$j]["DEPOSIT"]);
								$SURTAX						= trim($arr_rs[$j]["SURTAX"]);
								$MEMO						= trim($arr_rs[$j]["MEMO"]);
								$RESERVE_NO					= trim($arr_rs[$j]["RESERVE_NO"]);
								$ORDER_GOODS_NO				= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
								$RGN_NO						= trim($arr_rs[$j]["RGN_NO"]);

								$INOUT_DATE = date("Y-m-d",strtotime($INOUT_DATE));
								//$BALANCE = $BALANCE + $DEPOSIT - $WITHDRAW;
					?>
					
					<tr height="30">
						<td><input type="checkbox" name="chk_no[]" value="<?=$CL_NO?>"/></td>
						<td><?=$INOUT_DATE?></td>
						<td><?=$INOUT_TYPE?></td>
						<td class="modeual_nm">
							<? if($INOUT_TYPE == "매입" || $INOUT_TYPE == "매출") { ?>
								<a href="javascript:js_view('<?=$CL_NO?>');"><?=$NAME?></a>
							<? } else { ?>
								<?=$NAME?>
							<? } ?>
						</td>
						<td class="price qty" data-value="<?=$QTY?>"><?=number_format($QTY)?></td>
						<td class="price" data-value="<?=$$UNIT_PRICE?>"><?=number_format($UNIT_PRICE)?></td>
						<td class="price deposit" data-value="<?=$DEPOSIT?>"><?=number_format($DEPOSIT)?></td>
						<td class="price withdraw" data-value="<?=$WITHDRAW?>"><?=number_format($WITHDRAW)?></td>
						<td class="price surtax" data-value="<?=$SURTAX?>"><?=number_format($SURTAX)?></td>
						<td><?=$MEMO?></td>
						<td><a href="javascript:js_link_to_company_ledger('<?=$CP_NO?>');"><?=getCompanyNameWithNoCode($conn, $CP_NO)?></a>
						</td>
						<td><a href="javascript:js_link_to_company_ledger('<?=$TO_CP_NO?>');"><?=getCompanyNameWithNoCode($conn, $TO_CP_NO)?></a>
						</td>
					</tr>
					<? 
							}
						} else { 
					?>

					<tr height="35">
						<td colspan="12">데이터가 없습니다.</td