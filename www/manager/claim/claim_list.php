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
	$menu_right = "BO003"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";


if ($s_adm_cp_type == "구매" || $s_adm_cp_type == "판매공급") { 
	$cp_type = $s_adm_com_code;
}

//echo $cp_type;
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
	require "../../_classes/biz/board/board.php";
	require "../../_classes/biz/order/order.php";

	$bb_code = "CLAIM";

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

	if ($mode == "T") {
		updateBoardConfirmTF($conn, $confirm_tf, $s_adm_no, $bb_code, $bb_no);
	}

	if ($mode == "SU") {
		$row_cnt = count($chk_no);
		for ($k = 0; $k < $row_cnt; $k++) {
			$str_bb_no = $chk_no[$k];
			$bb_code = 'CLAIM';
			updateBoardConfirmTF($conn, $mode_reply_state, $s_adm_no, $bb_code, $str_bb_no);
		}
	}

#====================================================================
# Request Parameter
#====================================================================

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$cp_type = trim($cp_type);
	$cp_type2 = trim($cp_type2);
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


	$nListCnt =totalCntBoardClaim($conn, $start_date, $end_date, $bb_code, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $cp_type, $reply_state, $adm_no, $con_use_tf, $del_tf, $search_field, $search_str);

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listBoardClaim($conn, $start_date, $end_date, $bb_code, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $cp_type, $reply_state, $adm_no, $con_use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize);

	$arr_rs_order_state = totalCntClaimOrderState($conn, $start_date, $end_date, $bb_code, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $cp_type, $reply_state, $adm_no, $con_use_tf, $del_tf, $search_field, $search_str);

	$arr_rs_claim_type = totalCntClaimType($conn, $start_date, $end_date, $bb_code, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $cp_type, $reply_state, $adm_no, $con_use_tf, $del_tf, $search_field, $search_str);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script language="javascript" type="text/javascript" src="../js/common.js"></script>
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
	$(function(){
		$('table.fixed_header_table').floatThead({
			position: 'fixed'
		});
	});
</script>
<script language="javascript">

	function js_write() {
		document.location.href = "claim_write.php";
	}

	function js_view_claim(rn, bb_code, bb_no) {

		var frm = document.frm;
		
		frm.bb_code.value = bb_code;
		frm.bb_no.value = bb_no;
		frm.mode.value = "S";
		frm.target = "";
		frm.method = "post";
		frm.action = "claim_write.php";
		frm.submit();
		
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

function js_toggle(bb_code, bb_no, confirm_tf) {
	var frm = document.frm;

	//alert("상세 화면을 통해 처리 상태를 변경헤 주세요.");
	//return;

	
	bDelOK = confirm('처리 여부를 변경 하시겠습니까?');
		
	if (bDelOK==true) {

		if (confirm_tf == "Y") {
			confirm_tf = "N";
		} else {
			confirm_tf = "Y";
		}

		frm.bb_code.value = bb_code;
		frm.bb_no.value = bb_no;
		frm.confirm_tf.value = confirm_tf;
		frm.mode.value = "T";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}
	
}

/*
function js_con_cate_01 () {
	frm.nPage.value = "1";
	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();
}

function js_con_cate_02 () {
	frm.nPage.value = "1";
	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();
}
*/
function js_con_cate_03 () {
	frm.nPage.value = "1";
	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();
}

function js_reload() {
	location.reload();
}

function js_view(rn, reserve_no) {

	var frm = document.frm;
	var url = "/manager/order/order_read.php?reserve_no="+reserve_no;
	NewWindow(url, '','860','600','YES');
}

function js_excel() {

	var frm = document.frm;
	
	frm.nPage.value = "1";
	frm.target = "";
	frm.action = "<?=str_replace("list","excel_list",$_SERVER[PHP_SELF])?>";
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

function js_state_mod() {
	var frm = document.frm;

	bDelOK = confirm('선택한 클레임 처리 상태를 변경 하시겠습니까?\n취소,반품,교환에 대한 완료는 상세페이지에서만 해주세요.');
	
	if (bDelOK==true) {
		
		frm.mode.value = "SU";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}
}
</script>
</head>

<body id="admin" onresize="BodyMinSize();">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="rn" value="">
<input type="hidden" name="bb_no" value="">
<input type="hidden" name="bb_code" value="<?=$bb_code?>">
<input type="hidden" name="use_tf" value="">
<input type="hidden" name="confirm_tf" value="">
<input type="hidden" name="seq_no" value="" />
<input type="hidden" name="mode" value="">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<input type="hidden" name="reserve_no" value="">

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

        <h2>클레임 관리</h2>
        <div class="btnright">&nbsp;<!--<a href="javascript:js_write();"><img src="../images/admin/btn_regist_02.gif" alt="등록" /></a>--></div>
        <div class="category_choice">
					&nbsp;
				</div>
				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="120" />
					<col width="*" />
					<col width="120" />
					<col width="*" />
				</colgroup>
				<thead>
					<tr>
						<th>등록일</th>
						<td>
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="start_date" value="<?=$start_date?>" maxlength="10"/>
							 ~ 
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="end_date" value="<?=$end_date?>" maxlength="10"/>
						</td>
						<th>처리상태</th>
						<td>
							<?=makeSelectBox($conn,"CONFIRM_STATE", "reply_state","90", "전체", "", $reply_state)?>
						</td>
					</tr>
					<tr>
						<th>클레임 종류</th>
						<td>
							<?
								$condition = "AND DCODE IN ('6', '7','8', '99')";
							?>
							<?=makeSelectBoxWithCondition($conn,"ORDER_STATE", "con_cate_04","170", "클레임을 선택하세요.", "", $con_cate_04, $condition );?>
						</td>
						<th>클레임 사유</th>
						<td>
							<?=makeSelectBox($conn,"CLAIM_TYPE", "con_cate_02","170", "클레임 사유를 선택하세요.", "", $con_cate_02)?>

							<script>
							$(function(){

								claim_options_all = $("select[name=con_cate_02] option").clone();

								$("select[name=con_cate_04]").change(function(){

									var claim_type = $("select[name=con_cate_02]").find('option').remove().end();
									$("select[name=con_cate_02]").append("<option value=''>클레임 사유를 선택하세요.</option>");
									if($(this).val() == "6") //취소
									{
										claim_options_all.each(function(index, item){

											if(item.value.indexOf("CC") == 0)
												$("select[name=con_cate_02]").append(item);

										});
									}
									else if($(this).val() == "7") //반품
									{
										claim_options_all.each(function(index, item){

											if(item.value.indexOf("CR") == 0)
												$("select[name=con_cate_02]").append(item);

										});
									}
									else if($(this).val() == "8") //교환
									{
										claim_options_all.each(function(index, item){

											if(item.value.indexOf("CE") == 0)
												$("select[name=con_cate_02]").append(item);

										});
									}
									else if($(this).val() == "99") //기타
									{
										claim_options_all.each(function(index, item){

											if(item.value.indexOf("CX") == 0)
												$("select[name=con_cate_02]").append(item);

										});
									}
								});

								$("select[name=con_cate_04]").trigger("change");

							});
							</script>

						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>공급업체</th>
						<td>
							<input type="text" class="autocomplete_off" style="width:90%" placeholder="업체(명/코드) 입력후 엔터를 눌러주세요" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'',$cp_type)?>" />
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
												$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('구매,판매공급') +"&term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
													if(data.length == 1) { 
														
														js_selecting_company("txt_cp_type", data[0].label, "cp_type", data[0].id);

													} else if(data.length > 1){ 
														NewWindow("../company/pop_company_searched_list.php?con_cp_type=구매,판매공급&search_str="+keyword + "&target_name=txt_cp_type&target_value=cp_type",'pop_company_searched_list','950','650','YES');

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
						<th>판매업체</th>
						<td>
							<input type="text" class="autocomplete_off" style="width:90%" placeholder="업체(명/코드) 입력후 엔터를 눌러주세요" name="txt_con_cate_01" value="<?=getCompanyAutocompleteTextBox($conn,'',$con_cate_01)?>" />
							<input type="hidden" name="con_cate_01" value="<?=$con_cate_01?>">

							<script>
								$(function(){

									$("input[name=txt_con_cate_01]").keydown(function(e){

										if(e.keyCode==13) { 

											var keyword = $(this).val();
											
											if(keyword == "") { 
												$("input[name=con_cate_01]").val('');
												js_search();
											} else { 
												$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('판매,판매공급') +"&term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
													if(data.length == 1) { 
														
														js_selecting_company("txt_con_cate_01", data[0].label, "con_cate_01", data[0].id);

													} else if(data.length > 1){ 
														NewWindow("../company/pop_company_searched_list.php?con_cp_type=판매,판매공급&search_str="+keyword + "&target_name=txt_con_cate_01&target_value=con_cate_01",'pop_company_searched_list','950','650','YES');

													} else 
														alert("검색결과가 없습니다.");
												});
											}
										}

									});
									
									$("input[name=txt_con_cate_01]").keyup(function(e){
										var keyword = $(this).val();

										if(keyword == "") { 
											$("input[name=con_cate_01]").val('');
										}
									});

								});

							</script>
						</td>
					</tr>
					<tr>
						<th>요약</th>
						<td colspan="2">
							<label><input type="checkbox" name="chkSum" <?=($chkSum == 'Y' ? "checked='checked'" : "")?> value="Y">&nbsp;합계보기</label>&nbsp;&nbsp;
						</td>
						<td align="right">
							<?= makeAdminInfoSelectBox($conn, $adm_no)?> &nbsp;
							<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>
						</td>
					</tr>
					<tr>
						<th>정렬</th>
						<td>
							<select name="order_field" style="width:84px;">
								<option value="B.REG_DATE" <? if ($order_field == "B.REG_DATE") echo "selected"; ?> >등록일</option>
								<option value="B.REPLY_DATE" <? if ($order_field == "B.REPLY_DATE") echo "selected"; ?> >처리일</option>
								<option value="B.EMAIL" <? if ($order_field == "B.EMAIL") echo "selected"; ?> >주문자명</option>
								<option value="B.HOMEPAGE" <? if ($order_field == "B.HOMEPAGE") echo "selected"; ?> >수령자명</option>
							</select>&nbsp;&nbsp;
							<input type='radio' class="" name='order_str' value='DESC' <? if (($order_str == "DESC") || ($order_str == "")) echo " checked"; ?> > 오름차순 &nbsp;
							<input type='radio' name='order_str' value='ASC' <? if ($order_str == "ASC") echo " checked"; ?>> 내림차순
						</td>

						<th>검색조건</th>
						<td>
							<select name="nPageSize" style="width:84px;">
								<option value="20" <? if ($nPageSize == "20") echo "selected"; ?> >20개씩</option>
								<option value="50" <? if ($nPageSize == "50") echo "selected"; ?> >50개씩</option>
								<option value="100" <? if ($nPageSize == "100") echo "selected"; ?> >100개씩</option>
								<option value="200" <? if ($nPageSize == "200") echo "selected"; ?> >200개씩</option>
								<option value="300" <? if ($nPageSize == "300") echo "selected"; ?> >300개씩</option>
								<option value="400" <? if ($nPageSize == "400") echo "selected"; ?> >400개씩</option>
								<option value="500" <? if ($nPageSize == "500") echo "selected"; ?> >500개씩</option>
							</select>&nbsp;
							<select name="search_field" style="width:84px;">
								<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >통합검색</option>
								<option value="B.CATE_01" <? if ($search_field == "B.CATE_01") echo "selected"; ?> >주문번호</option>
								<option value="B.EMAIL" <? if ($search_field == "B.EMAIL") echo "selected"; ?> >주문자명</option>
								<option value="B.HOMEPAGE" <? if ($search_field == "B.HOMEPAGE") echo "selected"; ?> >수령자명</option>
								<option value="B.TITLE" <? if ($search_field == "B.TITLE") echo "selected"; ?> >상품명</option>
								<option value="B.CONTENTS" <? if ($search_field == "B.CONTENTS") echo "selected"; ?> >클레임</option>
							</select>&nbsp;
							
							<input type="text" value="<?=$search_str?>" name="search_str" size="15" class="txt"  onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();" />
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						</td>
					</tr>
					
				</tbody>
			</table>

			<div class="sp20"></div>
			<div>
				<? if($chkSum == "Y") { ?>
				<table cellpadding="0" cellspacing="0" class="rowstable" border="0" style="width:400px; float:left; margin-right:20px;">
					<colgroup>
						<col width="50%" />
						<col width="20%" />
						<col width="20%" />
					</colgroup>
					<thead>
						<tr>
							<th>클레임종류</th>
							<th>접수</th>
							<th class="end">처리완료</th>
						</tr>
					</thead>
					<tbody>
					<?
						if (sizeof($arr_rs_order_state) > 0) {
							for ($k = 0 ; $k < sizeof($arr_rs_order_state); $k++) {

								$DCODE	= trim($arr_rs_order_state[$k]["DCODE"]);
								$ORDER_STATE_NAME	= trim($arr_rs_order_state[$k]["ORDER_STATE_NAME"]);
								$ORDER_STATE_CNT_YES= trim($arr_rs_order_state[$k]["ORDER_STATE_CNT_YES"]);
								$ORDER_STATE_CNT_NO	= trim($arr_rs_order_state[$k]["ORDER_STATE_CNT_NO"]);

								if($DCODE != $con_cate_04 && $con_cate_04 <> "")
									continue;
								
					?>
					<tr height="30">

						<td><?=$ORDER_STATE_NAME?></td>
						<td><?=$ORDER_STATE_CNT_NO?></td>
						<td><?=$ORDER_STATE_CNT_YES?></td>
					</tr>

					<?
							}
						}
					?>
					</tbody>
				</table>
				<? } ?>
				<table cellpadding="0" cellspacing="0" class="rowstable" border="0" style="width:400px;">
					<colgroup>
						<col width="30%" />
						<col width="*" />
					</colgroup>
					<? if($con_cate_04 <> "") { ?>
					<thead>
						<tr>
							<th>클레임사유</th>
							<th class="end">총합</th>
						</tr>
					</thead>
					<? }?>
					<tbody>
					<?
						
						if (sizeof($arr_rs_claim_type) > 0) {
							for ($k = 0 ; $k < sizeof($arr_rs_claim_type); $k++) {

								$CLAIM_PCODE	= trim($arr_rs_claim_type[$k]["PCODE"]);
								$CLAIM_DCODE	= trim($arr_rs_claim_type[$k]["DCODE_NM"]);
								$CLAIM_CNT	= trim($arr_rs_claim_type[$k]["CNT"]);

								if($CLAIM_PCODE != $con_cate_04 )
									continue;

					?>
					<tr height="25">
						<td><?=$CLAIM_DCODE?></td>
						<td><?=number_format($CLAIM_CNT) ?></td>
					</tr>

					<?


							}
						}
					?>

					</tbody>
				</table>
			</div>
			<div class="sp20" style="both:clear;"></div>
			

			총 <?=$nListCnt?> 건

        <table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table">
        <colgroup>
		  <col width="2%" />
          <col width="4%" />
          <col width="8%" />
          <col width="5%" />
          <col width="6%" />
          <col width="6%" />
          <col width="6%" />
          <col width="10%" />
          <col width="10%" />
          <col width="12%" />
          <col width="10%" />
          <col width="5%" />
          <col width="8%" />
          <col width="8%" />
        </colgroup>
		<thead>
        <tr>
		  <th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
          <th>No.</th>
          <th>주문번호</th>
          <th>클레임구분</th>
          <th>사유</th>
          <th>주문자</th>
          <th>수령자</th>
          <th>판매업체</th>
          <th>공급업체</th>
          <th>상품명</th>
          <th>클레임</th>
          <th>처리상태</th>
          <th>등록일</th>
          <th class="end">처리일</th>
        </tr>
		</thead>
				
							<?
								$nCnt = 0;
								
								if (sizeof($arr_rs) > 0) {
									
									for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
										
										$rn							= trim($arr_rs[$j]["rn"]);
										$BB_NO					= trim($arr_rs[$j]["BB_NO"]);
										$BB_CODE				= trim($arr_rs[$j]["BB_CODE"]);
										$CATE_01				= trim($arr_rs[$j]["CATE_01"]);
										$CATE_02				= trim($arr_rs[$j]["CATE_02"]);
										$CATE_03				= trim($arr_rs[$j]["CATE_03"]);
										$CATE_04				= trim($arr_rs[$j]["CATE_04"]);
										$WRITER_NM				= trim($arr_rs[$j]["WRITER_NM"]);
										$TITLE					= trim($arr_rs[$j]["TITLE"]);
										$HIT_CNT				= trim($arr_rs[$j]["HIT_CNT"]);
										$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
										$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);
										$CONTENTS				= trim($arr_rs[$j]["CONTENTS"]);
										$CONFIRM_TF				= trim($arr_rs[$j]["REPLY_STATE"]);
										$REPLY_DATE				= trim($arr_rs[$j]["REPLY_DATE"]);
										$REF_IP					= trim($arr_rs[$j]["KEYWORD"]);
										 
										$O_NAME					= trim($arr_rs[$j]["EMAIL"]);
										$R_NAME					= trim($arr_rs[$j]["HOMEPAGE"]);
										//$CP_ORDER_NO			= trim($arr_rs[$j]["CP_ORDER_NO"]);
										
										$REG_ADM				= trim($arr_rs[$j]["REG_ADM"]);

										if (isHeadAdmin($conn, $REG_ADM)) {
											//echo "ADMIN";
										}

										$REG_DATE = date("Y-m-d",strtotime($REG_DATE));

										if ($CONFIRM_TF == "Y")
											$REPLY_DATE = date("Y-m-d",strtotime($REPLY_DATE));


										if ($CONFIRM_TF == "Y") {
											$STR_CONFIRM_TF = "<font color='navy'>처리완료</font>";
										} else {
											$STR_CONFIRM_TF = "<font color='red'>접수</font>";
										}
							
										if ($USE_TF == "Y") {
											$STR_USE_TF = "<font color='navy'>공개</font>";
										} else {
											$STR_USE_TF = "<font color='red'>비공개</font>";
										}

							?>
        <tr height="35"> 
			<td>
				<input type="checkbox" name="chk_no[]" value="<?=$BB_NO?>">
			  </td>
			<td><?= $rn ?></td>
			<td><a href="javascript:js_view('<?=$rn?>','<?=$CATE_01?>');"><?=$CATE_01?></a></td>
			<td class="filedown"><a href="javascript:js_view_claim('<?=$rn?>','<?=$BB_CODE?>','<?=$BB_NO?>');"><?=getDcodeName($conn,"ORDER_STATE",$CATE_04)?></a></td>
			<td class="filedown"><?=getDcodeName($conn,"CLAIM_TYPE",$CATE_02)?></td>
			<td><?=$O_NAME?></td>
			<td><?=$R_NAME?></td>
			<td class="modeual_nm"><?=getSaleCompanyName($conn, $CATE_01)?></td>
			<td class="modeual_nm"><?=getCompanyName($conn,$REF_IP)?></td>
			<td class="modeual_nm">
				<? if (isHeadAdmin($conn, $REG_ADM)) { ?>
					<a href="javascript:js_view_claim('<?=$rn?>','<?=$BB_CODE?>','<?=$BB_NO?>');"><?=$TITLE?></a>
				<? } else { ?>
					<a href="javascript:js_view_claim('<?=$rn?>','<?=$BB_CODE?>','<?=$BB_NO?>');"><font color="orange" ><?=$TITLE?></a></a>
				<? } ?>
			</td>
			<td class="modeual_nm" title="<?=$CONTENTS?>">
				<?=substr(nl2br($CONTENTS), 0, 100)?>
			</td>
			<td>
				<? if($CATE_04 == "99") { ?>
					<a href="javascript:js_toggle('<?=$BB_CODE?>','<?=$BB_NO?>','<?=$CONFIRM_TF?>');"><?=$STR_CONFIRM_TF?></a>
				<? } else { ?>
					<?=$STR_CONFIRM_TF?>
				<? } ?>
			</td>
			<td><?= $REG_DATE ?></td>
			<td><?= $REPLY_DATE ?></td>
        </tr>
				
							<?			
									}
								} else { 
							?> 
								<tr>
									<td height="50" align="center" colspan="15">데이터가 없습니다. </td>
								</tr>
							<? 
								}
							?>
						</table>
						<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">

							<input type="button" name="aa" value="선택한 클레임" class="btntxt" onclick="js_state_mod();">
							<?=makeSelectBox($conn,"CONFIRM_STATE", "mode_reply_state","90", "상태선택", "", "")?>
							<input type="button" name="aa" value="으로 변경" class="btntxt" onclick="js_state_mod();">
						</div>

					<!-- --------------------- 페이지 처리 화면 START -------------------------->
					<?
						# ==========================================================================
						#  페이징 처리
						# ==========================================================================
						if (sizeof($arr_rs) > 0) {
							#$search_field		= trim($search_field);
							#$search_str			= trim($search_str);

							//$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&con_cate_01=".$con_cate_01."&con_cate_02=".$con_cate_02."&con_cate_03=".$con_cate_03."&con_cate_04=".$con_cate_04."&cp_type=".$cp_type."&reply_state=".$reply_state;
							$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str;
							$strParam .= $strParam."&con_cate_04=".$con_cate_04."&reply_state=".$reply_state."&con_cate_01=".$con_cate_01."&con_cate_02=".$con_cate_02;
							$strParam .= $strParam."&adm_no=".$adm_no."&cp_type=".$cp_type."&start_date=".$start_date."&end_date=".$end_date."&order_field=".$order_field."&order_str=".$order_str."&chkSum=".$chkSum;

					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- 페이지 처리 화면 END -------------------------->
				<br />


			</div>
			<!-- // E: mwidthwrap -->

		</td>
	</tr>

	</table>
	<a style="display:scroll;position:fixed;bottom:10px;right:10px;" href="#">▲ 위로</a>
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
