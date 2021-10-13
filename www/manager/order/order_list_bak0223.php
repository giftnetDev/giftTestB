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

	$con_order_type = "";


	$menu_right = "OD002"; // 메뉴마다 셋팅 해 주어야 합니다


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
	require "../../_classes/biz/payment/payment.php";
	
	if ($s_adm_cp_type == "구매" || $s_adm_cp_type == "판매공급" ) { 
		$cp_type2 = $s_adm_com_code;
	}

	if ($s_adm_cp_type == "판매") { 
		$cp_type = $s_adm_com_code;
	}

	if ($mode == "T") {
		
		#echo $chg_order_state;
		
		$reserve_no_cnt = count($chk_reserve_no);

		for($i=0; $i <= ($reserve_no_cnt - 1) ; $i++) {
			$result = updateOrderState($conn, $chk_reserve_no[$i], $chg_order_state);
		}

		//updateBoardUseTF($conn, $use_tf, $s_adm_no, $bb_code, $bb_no);
	}


	if ($mode == "D") {
		$row_cnt = count($chk_no);

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_reserve_no = $chk_no[$k];
			
			$result = deleteOrder($conn, $str_reserve_no, $s_adm_no);
		
		}
		
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
	
#	echo $start_date;
#	echo $end_date;

#===============================================================
# Get Search list count
#===============================================================

	$nListCnt =totalCntManagerOrder($conn, $con_order_type, $start_date, $end_date, $sel_order_state, $cp_type, $cp_type2, $sel_pay_type, $con_use_tf, $del_tf, $search_field, $search_str);
	
	#echo $nListCnt;

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listManagerOrder($conn, $con_order_type, $start_date, $end_date, $sel_order_state, $cp_type, $cp_type2, $sel_pay_type, $con_use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize);


	$arr_rs_all = listAllOrder($conn, $con_order_type, $start_date, $end_date, $sel_order_state, $cp_type, $cp_type2, $sel_pay_type, $con_use_tf, $del_tf, $search_field, $search_str);


	if ($s_adm_cp_type == "구매"  || $s_adm_cp_type == "판매공급" ) { 
		$cnt_0 = cntOrderGoodsState($conn, '0', '', $s_adm_com_code); //입금전
		$cnt_1 = cntOrderGoodsState($conn, '1', '', $s_adm_com_code); // 주문확인
		$cnt_2 = cntOrderGoodsState($conn, '2', '', $s_adm_com_code); // 배송대기
		$cnt_3 = cntOrderGoodsState($conn, '3', '', $s_adm_com_code); // 배송완료
	} else if ($s_adm_cp_type == "판매") { 
		$cnt_0 = cntOrderGoodsState($conn, '0', $s_adm_com_code, ''); //입금전
		$cnt_1 = cntOrderGoodsState($conn, '1', $s_adm_com_code, ''); // 주문확인
		$cnt_2 = cntOrderGoodsState($conn, '2', $s_adm_com_code, ''); // 배송대기
		$cnt_3 = cntOrderGoodsState($conn, '3', $s_adm_com_code, ''); // 배송완료	
	} else {
		$cnt_0 = cntOrderGoodsState($conn, '0', '', ''); //입금전
		$cnt_1 = cntOrderGoodsState($conn, '1', '', ''); // 주문확인
		$cnt_2 = cntOrderGoodsState($conn, '2', '', ''); // 배송대기
		$cnt_3 = cntOrderGoodsState($conn, '3', '', ''); // 배송완료	
	}

	
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
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
      changeYear: true
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
<script language="javascript">

	function js_write() {

		var frm = document.frm;
		
		frm.target = "";
		frm.method = "get";
		frm.action = "order_write.php";
		frm.submit();
		
	}

	function js_view(rn, reserve_no) {

		var frm = document.frm;
		
		var url = "order_read.php?reserve_no="+reserve_no;

		NewWindow(url, 'order_detail','860','600','YES');
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

	function js_toggle() {

		var frm = document.frm;
		var chk_cnt = 0;

		if (frm('chk_reserve_no[]') == null) {
			alert("선택할 주문이 없습니다.");
			return;
		}

		if (frm('chk_reserve_no[]').length != null) {
			
			for (i = 0 ; i < frm('chk_reserve_no[]').length; i++) {
				if (frm('chk_reserve_no[]')[i].checked == true) {
					chk_cnt = 1;
				}
			}
		
		} else {
			if (frm('chk_reserve_no[]').checked == true) chk_cnt = 1;
		}
		
		if (chk_cnt == 0) {
			alert("상태 변경할 주문을 선택해 주세요");
			return;
		}

		bDelOK = confirm('주문 상태를 변경 하시겠습니까?');
			
		if (bDelOK==true) {

			frm.mode.value = "T";
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

	function js_excel() {

		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.target = "";
		frm.action = "<?=str_replace("list","excel_list",$_SERVER[PHP_SELF])?>";
		frm.submit();

	}

	function js_reload() {
		location.reload();
	}

	var day_0 = "<?=$day_0?>";
	var day_1 = "<?=$day_1?>";
	var day_7 = "<?=$day_7?>";
	var day_31 = "<?=$day_31?>";

	function js_search_date(iday) {

		var frm = document.frm;
		
		if (iday == 0) {
			frm.start_date.value = day_0;
			frm.end_date.value = day_0;
		}

		if (iday == 1) {
			frm.start_date.value = day_1;
			frm.end_date.value = day_0;
		}

		if (iday == 7) {
			frm.start_date.value = day_7;
			frm.end_date.value = day_0;
		}

		if (iday == 31) {
			frm.start_date.value = day_31;
			frm.end_date.value = day_0;
		}

		frm.nPage.value = "1";
		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}
	
	function js_reset() {
		
		var frm = document.frm;
		frm.start_date.value = "<?=date("Y-m-d",strtotime("-1 month"))?>";
		frm.end_date.value = "<?=date("Y-m-d",strtotime("0 month"))?>";
		frm.sel_order_state.value = "";
		
		<? if ($s_adm_cp_type == "운영") { ?>
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
</head>

<body id="admin"> 

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="rn" value="">
<input type="hidden" name="reserve_no" value="">
<input type="hidden" name="use_tf" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="con_order_type" value="<?=$con_order_type?>">
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
				<h2>주문 관리
			<? if ($s_adm_cp_type == "구매" || $s_adm_cp_type == "판매공급" ) { ?>
			<? } ?>
				</h2>
				<div class="btnright">
					<a href="javascript:js_write();"><img src="../images/admin/btn_regist_02.gif" alt="등록" /></a>
				</div>
				<div class="category_choice"><!--<select style="width: 100px;"><option>제품선택</option></select>-->&nbsp;</div>

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
						<th>주문일</th>
						<td>
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="start_date" value="<?=$start_date?>" maxlength="10"/>
							 ~ 
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="end_date" value="<?=$end_date?>" maxlength="10"/>
							&nbsp;<a href="javascript:js_search_date('0');"><img src="/manager/images/bu/btn_s_dday.gif" alt="" /></a>
							<a href="javascript:js_search_date('1');"><img src="/manager/images/bu/btn_s_bday.gif" alt="" /></a>
							<a href="javascript:js_search_date('7');"><img src="/manager/images/bu/btn_s_7day.gif" alt="" /></a>
							<a href="javascript:js_search_date('31');"><img src="/manager/images/bu/btn_s_1mon.gif" alt="" /></a>

						</td>
						<th>주문상태</th>
						<td colspan="2">
							<?= makeSelectBox($conn,"ORDER_STATE","sel_order_state","125","선택","",$sel_order_state)?>
						</td>
					</tr>
				</thead>
				<tbody>
					<? if ($s_adm_cp_type == "운영") { ?>
					<tr>
						<th>판매업체</th>
						<td>
							<input type="text" class="seller" style="width:210px" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'판매',$cp_type)?>" />
							<script>
							$(function() {
						     var cache = {};
								$( ".seller" ).autocomplete({
									source: function( request, response ) {
										var term = request.term;
										if ( term in cache ) {
											response(cache[term]);
											return;
										}
						 
										$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('판매'), request, function( data, status, xhr ) {
											cache[term] = data;
											response(data);
										});
									},
									minLength: 2,
									select: function( event, ui ) {
										$(".seller").val(ui.item.value);
										$("input[name=cp_type]").val(ui.item.id);
									}
								}).bind( "blur", function( event ) {
									var cp_no = $("input[name=cp_type]").val();
									if(cp_no != '') {
										$.getJSON( "../company/json_company_list.php?cp_no=" + cp_no, function(data) {
											if(data[0].CP_NO == 'undefined') {
												$("input[name=cp_type]").val('');
											} else {
												if(data[0].COMPANY != $(".seller").val())
												{

													$(".seller").val();
													$("input[name=cp_type]").val('');
												}
											}
										});
									} 
								});
							});
							</script>
							<input type="hidden" name="cp_type" value="<?=$cp_type?>">
						</td>
						<th>공급업체</th>
						<td colspan="2">
							<input type="text" class="supplyer" style="width:210px" name="txt_cp_type2" value="<?=getCompanyAutocompleteTextBox($conn,'구매',$cp_type2)?>" />
							<script>
							$(function() {
						     var cache2 = {};
								$( ".supplyer" ).autocomplete({
									source: function( request, response ) {
										var term = request.term;
										if ( term in cache2 ) {
											response(cache2[term]);
											return;
										}
						 
										$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('구매'), request, function( data, status, xhr ) {
											cache2[term] = data;
											response(data);
										});
									},
									minLength: 2,
									select: function( event, ui ) {
										$(".supplyer").val(ui.item.value);
										$("input[name=cp_type2]").val(ui.item.id);
									}
								})
								.bind( "blur", function( event ) {
									var cp_no = $("input[name=cp_type2]").val();
									if(cp_no != '') {
										$.getJSON( "../company/json_company_list.php?cp_no=" + cp_no, function(data) {
											if(data[0].CP_NO == 'undefined') {
												$("input[name=cp_type2]").val('');
											} else {
												if(data[0].COMPANY != $(".supplyer").val())
												{

													$(".supplyer").val();
													$("input[name=cp_type2]").val('');
												}
											}
										});
									} 
								});
							});
							</script>
							<input type="hidden" name="cp_type2" value="<?=$cp_type2?>">
						</td>					
					</tr>
					<tr>
						<th>결재방식</th>
						<td colspan="4">
							<?= makeSelectBox($conn,"PAY_TYPE","sel_pay_type","125","선택","",$sel_pay_type)?>
						</td>					
					</tr>
					<? } else { ?>
					<input type="hidden" name="cp_type" value = "">
					<input type="hidden" name="sel_pay_type" value = "">
					<? } ?>
					<tr>
						<th>정렬</th>
						<td>
							<select name="order_field" style="width:84px;">
								<option value="ORDER_DATE" <? if ($order_field == "ORDER_DATE") echo "selected"; ?> >주문일시</option>
								<option value="REG_DATE" <? if ($order_field == "REG_DATE") echo "selected"; ?> >등록일</option>
								<option value="O_MEM_NM" <? if ($order_field == "O_MEM_NM") echo "selected"; ?> >주문자명</option>
								<option value="R_MEM_NM" <? if ($order_field == "R_MEM_NM") echo "selected"; ?> >수령자명</option>
								<? if ($s_adm_cp_type == "운영") { ?>
								<option value="TOTAL_BUY_PRICE" <? if ($order_field == "TOTAL_BUY_PRICE") echo "selected"; ?> >총매입원가</option>
								<option value="TOTAL_SALE_PRICE" <? if ($order_field == "TOTAL_SALE_PRICE") echo "selected"; ?> >총판매가</option>
								<option value="TOTAL_EXTRA_PRICE" <? if ($order_field == "TOTAL_EXTRA_PRICE") echo "selected"; ?> >총배송비</option>
								<option value="TOTAL_QTY" <? if ($order_field == "TOTAL_QTY") echo "selected"; ?> >총수량</option>
								<option value="TOTAL_DELIVERY_PRICE" <? if ($order_field == "TOTAL_DELIVERY_PRICE") echo "selected"; ?> >추가배송비</option>
								<option value="TOTAL_PRICE" <? if ($order_field == "TOTAL_PRICE") echo "selected"; ?> >합계</option>
								<option value="TOTAL_PLUS_PRICE" <? if ($order_field == "TOTAL_PLUS_PRICE") echo "selected"; ?> >총판매이익</option>
								<option value="LEE" <? if ($order_field == "LEE") echo "selected"; ?> >총판매이익율</option>
								<? } ?>
							</select>&nbsp;&nbsp;
							<input type='radio' class="" name='order_str' value='DESC' <? if (($order_str == "DESC") || ($order_str == "")) echo " checked"; ?> > 오름차순 &nbsp;
							<input type='radio' name='order_str' value='ASC' <? if ($order_str == "ASC") echo " checked"; ?>> 내림차순
						</td>

						<th>검색조건</th>
						<td>
							<select name="nPageSize" style="width:74px;">
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
								<option value="RESERVE_NO" <? if ($search_field == "RESERVE_NO") echo "selected"; ?> >주문번호</option>
								<option value="O_MEM_NM" <? if ($search_field == "O_MEM_NM") echo "selected"; ?> >주문자명</option>
								<option value="R_MEM_NM" <? if ($search_field == "R_MEM_NM") echo "selected"; ?> >수령자명</option>
								<option value="GOODS_NAME" <? if ($search_field == "GOODS_NAME") echo "selected"; ?> >상품명</option>
							</select>&nbsp;

							<input type="text" value="<?=$search_str?>" name="search_str" size="12"class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();"/>
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
							<a href="javascript:js_reset();"><img src="/manager/images/admin/btn_in.gif" alt="reset"/></a>
						</td>
						<td align="right">
							<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="sp20"></div>
			<b>총 <?=$nListCnt?> 건</b>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<!--<b><font color="orange">미처리주문</font></b>-->&nbsp;&nbsp;&nbsp;&nbsp;
			<b><font color="blue">입금전</font> <font color="red"><?=$cnt_0?></font> <font color="blue">건</font></b>&nbsp;&nbsp;
			<b><font color="blue">주문확인전</font> <font color="red"><?=$cnt_1?></font> <font color="blue">건</font></b>&nbsp;&nbsp;
			<b><font color="blue">배송완료전</font> <font color="red"><?=$cnt_2?></font> <font color="blue">건</font></b>&nbsp;&nbsp;
			<table cellpadding="0" cellspacing="0" class="rowstable02" border="0">

				<? if ($s_adm_cp_type == "운영") { ?>
				<colgroup>
					<col width="2%" />
					<col width="8%" />
					<col width="9%" />
					<col width="9%" />
					<col width="12%"/>
					<col width="5%" />
					<col width="5%" />
					<col width="6%" />
					<col width="6%" />
					<col width="5%" />
					<col width="4%" />
					<col width="6%" />
					<col width="7%" />
					<col width="10%" />
					<col width="6%" />
				</colgroup>
				<thead>
					<tr>
						<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
						<th>주문번호</th>
						<th colspan="2" >주문일시</th>
						<th>업체명</th>
						<th>주문자명</th>
						<th>수령자명</th>
						
						<th>총판매가</th>
						<th>총수량</th>
						<th>총할인</th>
						<th>총추가배송비</th>
						<th>합계</th>
						<th>총매입합계</th>
						<th>총판매이익</th>
						<th class="end">결제방법</th>

					</tr>
					<tr>
						<th colspan="2"></th>
						<th>요청일</th>
						<th>처리일</th>
						<th colspan="3">공급사 : 상품명</th>
						<th>판매가</th>
						<th>수량</th>
						<th>할인</th>
						<th>추가배송비</th>
						<th>소계</th>
						<th>매입원가</th>
						<th>판매이익</th>
						<th class="end">주문상태</th>
					</tr>
				</thead>
				<? } ?>
				<!--
				<? if ($s_adm_cp_type == "판매") { ?>
				<colgroup>
					<col width="8%" />
					<col width="23%"/>
					<col width="5%" />
					<col width="5%" />
					<col width="6%" />
					<col width="6%" />
					<col width="6%" />
					<col width="4%" />
					<col width="6%" />
					<col width="7%" />
					<col width="8%" />
					<col width="8%" />
					<col width="8%" />
				</colgroup>
				<thead>
					<tr>
						<th>주문번호</th>
						<th>상품명</th>
						<th>주문자명</th>
						<th>수령자명</th>
						<th>판매가</th>
						<th>배송비</th>
						<th>추가배송비</th>
						<th>수량</th>
						<th>합계</th>
						<th>주문상태</th>
						<th>주문일시</th>
						<th>요청일</th>
						<th class="end">처리일</th>
					</tr>
				</thead>
				<? } ?>

				<? if ($s_adm_cp_type == "구매"  || $s_adm_cp_type == "판매공급") { ?>
				<colgroup>
					<col width="8%" />
					<col width="23%"/>
					<col width="5%" />
					<col width="5%" />
					<col width="6%" />
					<col width="6%" />
					<col width="6%" />
					<col width="4%" />
					<col width="6%" />
					<col width="7%" />
					<col width="8%" />
					<col width="8%" />
					<col width="8%" />
				</colgroup>
				<thead>
					<tr>
						<th>주문번호</th>
						<th>상품명</th>
						<th>주문자명</th>
						<th>수령자명</th>
						<th>매입원가</th>
						<th>배송비</th>
						<th>추가배송비</th>
						<th>수량</th>
						<th>합계</th>
						<th>주문상태</th>
						<th>주문일시</th>
						<th>요청일</th>
						<th class="end">처리일</th>
					</tr>
				</thead>
				<? } ?>
				-->
				<tbody>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$rn										= trim($arr_rs[$j]["rn"]);
							$RESERVE_NO							= trim($arr_rs[$j]["RESERVE_NO"]);
							$MEM_TYPE							= trim($arr_rs[$j]["MEM_TYPE"]);
							$MEM_NO								= trim($arr_rs[$j]["MEM_NO"]);
							$CP_NO								= trim($arr_rs[$j]["CP_NO"]);
							
							$PAY_TYPE							= trim($arr_rs[$j]["PAY_TYPE"]);
							//$ORDER_STATE						= trim($arr_rs[$j]["ORDER_STATE"]);
							$PAY_STATE					  		= trim($arr_rs[$j]["PAY_STATE"]);
							$O_MEM_NM							= trim($arr_rs[$j]["O_MEM_NM"]);
							$R_MEM_NM							= trim($arr_rs[$j]["R_MEM_NM"]);
							$TOTAL_BUY_PRICE			= trim($arr_rs[$j]["TOTAL_BUY_PRICE"]);
							$TOTAL_SALE_PRICE			= trim($arr_rs[$j]["TOTAL_SALE_PRICE"]);
							$TOTAL_EXTRA_PRICE			= trim($arr_rs[$j]["TOTAL_EXTRA_PRICE"]);
							$TOTAL_QTY					= trim($arr_rs[$j]["TOTAL_QTY"]);
							$TOTAL_DELIVERY_PRICE		= trim($arr_rs[$j]["TOTAL_DELIVERY_PRICE"]);
							$TOTAL_SA_DELIVERY_PRICE	= trim($arr_rs[$j]["TOTAL_SA_DELIVERY_PRICE"]);
							$TOTAL_DISCOUNT_PRICE		= trim($arr_rs[$j]["TOTAL_DISCOUNT_PRICE"]);
							$TOTAL_SUM_PRICE			= trim($arr_rs[$j]["TOTAL_SUM_PRICE"]);
							
							$TOTAL_PRICE					= trim($arr_rs[$j]["TOTAL_PRICE"]);
							$TOTAL_PLUS_PRICE			= trim($arr_rs[$j]["TOTAL_PLUS_PRICE"]);
							$LEE									= trim($arr_rs[$j]["LEE"]);
							
							$ORDER_DATE						= trim($arr_rs[$j]["ORDER_DATE"]);
							$PAY_DATE							= trim($arr_rs[$j]["PAY_DATE"]);
							$CANCEL_DATE					= trim($arr_rs[$j]["CANCEL_DATE"]);

							$DELIVERY_TYPE			= trim($arr_rs[$j]["DELIVERY_TYPE"]);
							$REG_DATE						= trim($arr_rs[$j]["REG_DATE"]);
							
							$ORDER_DATE		= date("Y-m-d H:i:s",strtotime($ORDER_DATE));
							
							if ($s_adm_cp_type == "운영") {
						?>
						<tr class="order" height="37">
							<td class="order"><input type="checkbox" name="chk_no[]" value="<?=$RESERVE_NO?>"></td>
							<td class="order"><a href="javascript:js_view('<?=$rn?>','<?=$RESERVE_NO?>');"><?=$RESERVE_NO?></a></td>
							<td colspan="2" class="filedown"><?=$ORDER_DATE?></td>
							<td class="modeual_nm"><?= getCompanyName($conn, $CP_NO);?></td>
							<td><?=$O_MEM_NM?></td>
							<td><?=$R_MEM_NM?></td>
							<td class="price"><?=number_format($TOTAL_SALE_PRICE)?></td>
							<td class="price"><?=number_format($TOTAL_QTY)?></td>
							<td class="price"><?=number_format($TOTAL_DISCOUNT_PRICE)?></td>
							<td class="price"><?=number_format($TOTAL_SA_DELIVERY_PRICE)?></td>
							<td class="price"><?=number_format($TOTAL_SUM_PRICE)?></td>
							<td class="price"><?=number_format($TOTAL_PRICE)?></td>
							<td class="price"><?=number_format($TOTAL_PLUS_PRICE)?> (<?=$LEE?>%)</td>
							<td><?=getDcodeName($conn, "PAY_TYPE", $PAY_TYPE);?></td>
						</tr>
						<?
							}


							$arr_goods = listManagerOrderGoods($conn, $RESERVE_NO, $MEM_NO, "Y", "N");

							if (sizeof($arr_goods) > 0) {
								for ($h = 0 ; $h < sizeof($arr_goods); $h++) {
									
									$BUY_CP_NO					= trim($arr_goods[$h]["BUY_CP_NO"]);
									$CATE_01					= SetStringFromDB(trim($arr_goods[$h]["CATE_01"]));
									$GOODS_NAME					= SetStringFromDB(trim($arr_goods[$h]["GOODS_NAME"]));
									$PRICE						= trim($arr_goods[$h]["PRICE"]);
									$BUY_PRICE					= trim($arr_goods[$h]["BUY_PRICE"]);
									$SALE_PRICE					= trim($arr_goods[$h]["SALE_PRICE"]);
									$EXTRA_PRICE				= trim($arr_goods[$h]["EXTRA_PRICE"]);
									$DELIVERY_PRICE				= trim($arr_goods[$h]["DELIVERY_PRICE"]);
									$SA_DELIVERY_PRICE			= trim($arr_goods[$h]["SA_DELIVERY_PRICE"]);
									$DISCOUNT_PRICE				= trim($arr_goods[$h]["DISCOUNT_PRICE"]);

									$SUM_PRICE					= trim($arr_goods[$h]["SUM_PRICE"]);
									$PLUS_PRICE					= trim($arr_goods[$h]["PLUS_PRICE"]);
									$GOODS_LEE					= trim($arr_goods[$h]["LEE"]);
									$QTY						= trim($arr_goods[$h]["QTY"]);
									$ORDER_DATE					= trim($arr_goods[$h]["ORDER_DATE"]);
									$REQ_DATE					= trim($arr_goods[$h]["PAY_DATE"]);
									$END_DATE					= trim($arr_goods[$h]["FINISH_DATE"]);
									$ORDER_STATE				= trim($arr_goods[$h]["ORDER_STATE"]);

									if ($ORDER_DATE <> "")  {
										$ORDER_DATE		= date("Y-m-d H:i",strtotime($ORDER_DATE));
									}

									if ($REQ_DATE <> "")  {
										$REQ_DATE		= date("Y-m-d H:i",strtotime($REQ_DATE));
									}
									
									if ($END_DATE <> "")  {
										$END_DATE		= date("Y-m-d H:i",strtotime($END_DATE));
									}
									

									if ($h == (sizeof($arr_goods)-1)) {

										if ($ORDER_STATE == "1") {
											$str_tr = "class='goods_1_end'";
										} else if ($ORDER_STATE == "3") {
											$str_tr = "class='goods_3_end'";
										} else {
											$str_tr = "class='goods_end'";
										}

									} else {

										if ($ORDER_STATE == "1") {
											$str_tr = "class='goods_1'";
										} else if ($ORDER_STATE == "3") {
											$str_tr = "class='goods_3'";
										} else {
											$str_tr = "class='goods'";
										}
									}
									
									if($CATE_01 <> "")
										$str_cate_01 = $CATE_01.") ";
									else 
										$str_cate_01 = "";

									$str_price_class = "price";
									$str_state_class = "state";
									
									$STR_QTY = number_format($QTY);

									if (($ORDER_STATE == "4") || ($ORDER_STATE == "6") || ($ORDER_STATE == "7") || ($ORDER_STATE == "8")) {
										
										if ($ORDER_STATE == "4") {
											
											$BUY_PRICE = 0;
											$SALE_PRICE = 0;
											$EXTRA_PRICE = 0;
											$STR_QTY = "[".number_format($QTY)."]";
											$SUM_PRICE = 0;
											$PLUS_PRICE = 0;
											$GOODS_LEE = 0;
											$PRICE = 0;
											$SA_DELIVERY_PRICE = 0;
											$DISCOUNT_PRICE = 0;

											$REQ_DATE = $ORDER_DATE;
											$END_DATE = $ORDER_DATE;
											$str_price_class = "price_cancel";
											$str_state_class = "state_cancel";

										} else {
											$BUY_PRICE = -$BUY_PRICE;
											$SALE_PRICE = -$SALE_PRICE;
											$EXTRA_PRICE = -$EXTRA_PRICE;
											$STR_QTY = number_format(-$QTY);
											$SUM_PRICE = -$SUM_PRICE;
											$PLUS_PRICE = -$PLUS_PRICE;
											$GOODS_LEE = - $GOODS_LEE;
											$PRICE = -$PRICE;
											$SA_DELIVERY_PRICE = -$SA_DELIVERY_PRICE;
											$DISCOUNT_PRICE = -$DISCOUNT_PRICE;

											$REQ_DATE = $ORDER_DATE;
											$END_DATE = $ORDER_DATE;

											$str_price_class = "price_refund";
											$str_state_class = "state_refund";
										}
									} 
									
									if ($s_adm_cp_type == "운영") {

						?>
						<tr <?=$str_tr?>  height="35">
							<td class="filedown" colspan="2">&nbsp;</td>
							<td><?=$REQ_DATE?></td>
							<td><?=$END_DATE?></td>
							<td class="modeual_nm" colspan="3" ><?=getCompanyName($conn, $BUY_CP_NO)?> : <?=$str_cate_01?><?=$GOODS_NAME?></td>
							<td class="<?=$str_price_class?>"><?=number_format($SALE_PRICE)?></td>
							<td class="<?=$str_price_class?>"><?=$STR_QTY?></td>
							<td class="<?=$str_price_class?>"><?=number_format($DISCOUNT_PRICE)?></td>
							<td class="<?=$str_price_class?>"><?=number_format($SA_DELIVERY_PRICE)?></td>
							<td class="<?=$str_price_class?>"><?=number_format($SUM_PRICE)?></td>
							<td class="<?=$str_price_class?>"><?=number_format($PRICE)?></td>
							
							<td class="<?=$str_price_class?>"><?=number_format($PLUS_PRICE)?> (<?=$GOODS_LEE?>%)</td>
							<td class="<?=$str_state_class?>"><?=getDcodeName($conn, "ORDER_STATE", $ORDER_STATE);?></td>
						</tr>
						<?
									}
						?>
						<!--
						<?
						if ($s_adm_cp_type == "판매") {

						?>
						<tr <?=$str_tr?>  height="35">
							<td class="filedown"><?=$RESERVE_NO?></td>
							<td class="modeual_nm"><?=$GOODS_NAME?></td>
							<td><?=$O_MEM_NM?></td>
							<td><?=$R_MEM_NM?></td>
							<td class="<?=$str_price_class?>"><?=number_format($SALE_PRICE)?></td>
							<td class="<?=$str_price_class?>"><?=number_format($EXTRA_PRICE)?></td>
							<td class="<?=$str_price_class?>"><?=number_format($DELIVERY_PRICE)?></td>
							<td class="<?=$str_price_class?>"><?=$STR_QTY?></td>
							<td class="<?=$str_price_class?>"><?=number_format($SUM_PRICE)?></td>
							<td class="<?=$str_state_class?>"><?=getDcodeName($conn, "ORDER_STATE", $ORDER_STATE);?></td>
							<td><?=$ORDER_DATE?></td>
							<td><?=$REQ_DATE?></td>
							<td><?=$END_DATE?></td>
						</tr>
						<?
									}

									if ($s_adm_cp_type == "구매" || $s_adm_cp_type == "판매공급") {

						?>
						<tr <?=$str_tr?>  height="35">
							<td class="filedown"><a href="javascript:js_view('<?=$rn?>','<?=$RESERVE_NO?>');"><?=$RESERVE_NO?></a></td>
							<td class="modeual_nm"><?=$GOODS_NAME?></td>
							<td><?=$O_MEM_NM?></td>
							<td><?=$R_MEM_NM?></td>
							<td class="<?=$str_price_class?>"><?=number_format($BUY_PRICE)?></td>
							<td class="<?=$str_price_class?>"><?=number_format($EXTRA_PRICE)?></td>
							<td class="<?=$str_price_class?>"><?=number_format($DELIVERY_PRICE)?></td>
							<td class="<?=$str_price_class?>"><?=$STR_QTY?></td>
							<td class="<?=$str_price_class?>"><?=number_format(($BUY_PRICE * $QTY) + ($EXTRA_PRICE * $QTY) + $DELIVERY_PRICE)?></td>
							<td class="<?=$str_state_class?>"><?=getDcodeName($conn, "ORDER_STATE", $ORDER_STATE);?></td>
							<td><?=$ORDER_DATE?></td>
							<td><?=$REQ_DATE?></td>
							<td><?=$END_DATE?></td>
						</tr>
						<?
									}
						?>
						-->
						<?

								}
							}
						?>
						<?
						} 
						
						
						if (sizeof($arr_rs_all) > 0) {
							for ($j = 0 ; $j < sizeof($arr_rs_all); $j++) {
								$ALL_BUY_PRICE			= trim($arr_rs_all[$j]["ALL_BUY_PRICE"]);
								$ALL_SALE_PRICE			= trim($arr_rs_all[$j]["ALL_SALE_PRICE"]);
								$ALL_EXTRA_PRICE		= trim($arr_rs_all[$j]["ALL_EXTRA_PRICE"]);
								$ALL_STR_QTY				= trim($arr_rs_all[$j]["ALL_QTY"]);
								$ALL_DELIVERY_PRICE	= trim($arr_rs_all[$j]["ALL_DELIVERY_PRICE"]);
								$ALL_SUM_PRICE			= trim($arr_rs_all[$j]["ALL_SUM_PRICE"]);
								$ALL_PLUS_PRICE			= trim($arr_rs_all[$j]["ALL_PLUS_PRICE"]);
								$ALL_GOODS_LEE			= trim($arr_rs_all[$j]["ALL_LEE"]);
							}
						}

						if ($s_adm_cp_type == "운영") {
					?>
					<!--
						<tr class="goods_end">
							<td colspan="16">
								&nbsp;
							</td>
						</tr>
						<tr class="goods_end" height="35">
							<td class="filedown" colspan="2">합 계</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td class="modeual_nm" colspan="3" ></td>
							<td class="<?=$str_price_class?>"><?=number_format($ALL_BUY_PRICE)?></td>
							<td class="<?=$str_price_class?>"><?=number_format($ALL_SALE_PRICE)?></td>
							<td class="<?=$str_price_class?>"><?=number_format($ALL_EXTRA_PRICE)?></td>
							<td class="<?=$str_price_class?>"><?=$ALL_STR_QTY?></td>
							<td class="<?=$str_price_class?>"><?=number_format($ALL_DELIVERY_PRICE)?></td>
							<td class="<?=$str_price_class?>"><?=number_format($ALL_SUM_PRICE)?></td>
							<td class="<?=$str_price_class?>"><?=number_format($ALL_PLUS_PRICE)?> (<?=$ALL_GOODS_LEE?>%)</td>
							<td class="<?=$str_state_class?>">&nbsp;</td>
						</tr>
					-->
					<?
						}

					}else{
						?>
						<tr class="order">
							<td height="50" align="center" colspan="16">데이터가 없습니다. </td>
						</tr>
					<?
						}
					?>
				</tbody>
			</table>

				<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">
				<? if (($sPageRight_D == "Y") && ($s_adm_cp_type == "운영")) {?>
					<input type="button" name="aa" value=" 선택한 주문 삭제 " class="btntxt" onclick="js_delete();"> 
				<? } ?>
				</div>

					<!-- --------------------- 페이지 처리 화면 START -------------------------->
					<?
						# ==========================================================================
						#  페이징 처리
						# ==========================================================================
						if (sizeof($arr_rs) > 0) {
							#$search_field		= trim($search_field);
							#$search_str			= trim($search_str);
							//$sel_order_state, $cp_type, $cp_type2, $sel_pay_type, $con_use_tf,
							$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&start_date=".$start_date."&end_date=".$end_date;
							$strParam = $strParam."&sel_order_state=".$sel_order_state."&cp_type=".$cp_type."&cp_type2=".$cp_type2."&sel_pay_type=".$sel_pay_type."&con_order_type=".$con_order_type;
							$strParam = $strParam."&order_field=".$order_field."&order_str=".$order_str;

					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- 페이지 처리 화면 END -------------------------->
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
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>