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
	$menu_right = "OD005"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

if ($s_adm_cp_type == "구매" || $s_adm_cp_type == "판매공급") { 
	$cp_type = $s_adm_com_code;
}

if ($s_adm_cp_type == "판매") { 
	$cp_type2 = $s_adm_com_code;
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
	require "../../_classes/com/util/ImgUtil.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/payment/payment.php";

	if ($mode == "CU") { //주문확인

		$row_cnt = count($chk_order_no);
		//echo $row_cnt;

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_order_goods_no			= $chk_order_no[$k];

			//echo 	$str_order_goods_no;
			$arr_order_goods_no			= explode("|", $str_order_goods_no);
			
			$temp_order_no				= trim($arr_order_goods_no[0]);
			$temp_order_goods_no	= trim($arr_order_goods_no[1]);

			//echo $temp_order_no."<br>";
			//echo $temp_order_goods_no."<br>";
			//echo $tmp_pay_reason."<br>";

			#echo $tmp_pay_no;

			$result = updateOrderConfirmState($conn, $temp_order_no, $temp_order_goods_no, $s_adm_no);
		}
	}

	if ($mode == "U") { //배송완료

		$row_cnt = count($order_goods_no);
		//echo $row_cnt;

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$temp_reserve_no					= $arr_reserve_no[$k];
			$temp_order_goods_no				= $order_goods_no[$k];
			$temp_delivery_cp					= $delivery_cp[$k];
			$temp_delivery_no					= $delivery_no[$k];
			$temp_work_flag						= $arr_work_flag[$k];

			$temp_delivery_no = str_replace("-","",$temp_delivery_no);
			
			#echo $temp_delivery_cp."<br>";
			#echo $temp_delivery_no."<br>";
			#echo $temp_reserve_no."<br>";

			#echo $tmp_pay_no;
			
			if ($temp_delivery_cp <> "" && $temp_delivery_no <> "" && $temp_work_flag == "Y")
				$result = updateDeliveryState($conn, $temp_reserve_no, $temp_order_goods_no, $temp_delivery_cp, $temp_delivery_no, $s_adm_no);
		
		}
	}
	/*
	if ($mode == "CU") {

		$row_cnt = count($chk_order_no);

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_order_goods_no			= $chk_order_no[$k];
			$temp_delivery_cnt			= $delivery_cnt[$k];
			$temp_order_qty			    = $order_qty[$k];
			

			$arr_order_goods_no			= explode("|", $str_order_goods_no);
			
			$temp_reserve_no				= trim($arr_order_goods_no[0]);
			$temp_order_goods_no	= trim($arr_order_goods_no[1]);
			
			$result = updateOrderConfirmState($conn, $temp_reserve_no, $temp_order_goods_no, $s_adm_no);
			updateOrderDeliveryCnt($conn, $temp_reserve_no, $temp_order_goods_no, $temp_delivery_cnt, $temp_order_qty, $s_adm_no);
			
		}
	}

	if ($mode == "U") {

		$row_cnt = count($order_goods_no);
		//echo $row_cnt;

		$inserted_reserve_no = "";
		for ($k = 0; $k < $row_cnt; $k++) {
		
			$temp_reserve_no					= $arr_reserve_no[$k];
			$temp_order_goods_no			= $order_goods_no[$k];
			$temp_delivery_cp					= $delivery_cp[$k];
			$temp_delivery_no					= $delivery_no[$k];
			$temp_delivery_seq				= $delivery_seq[$k];

			$temp_delivery_no = str_replace("-","",$temp_delivery_no);
			
			if ($inserted_reserve_no <> $temp_reserve_no && $temp_delivery_cp <> "" && $temp_delivery_no <> "")
				$result = updateDeliveryState($conn, $temp_reserve_no, $temp_order_goods_no, $temp_delivery_cp, $temp_delivery_no, $s_adm_no);
		
			updateDeliveryStateMulti($conn, $temp_delivery_seq, $temp_delivery_cp, $temp_delivery_no);

			$inserted_reserve_no = $temp_reserve_no;
		}
	}
	*/

	if ($mode == "DELIVERY_APPEND") {

		$delivery_cnt = '0';
		$order_qty = '0';
	    insertOrderDelivery($conn, $reserve_no, $hid_order_goods_no, $delivery_cnt, $order_qty);
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
		$order_field = "G_REG_DATE";
//	if ($sel_order_state == "") 
//		$sel_order_state = "1";

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

	$nListCnt =totalCntManagerDelivery($conn, $start_date, $end_date, $sel_order_state, $cp_type, $cp_type2, $con_work_flag, $sel_opt_manager_no, $con_use_tf, $del_tf, $search_field, $search_str);
	
	#echo $nListCnt;

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listManagerDelivery($conn, $start_date, $end_date, $sel_order_state, $cp_type, $cp_type2, $con_work_flag, $sel_opt_manager_no, $con_use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize);


	if ($s_adm_cp_type == "구매" || $s_adm_cp_type == "판매공급" ) { 
		$cnt_0 = cntOrderGoodsState($conn, '0', '', $s_adm_com_code); //입금전
		$cnt_1 = cntOrderGoodsState($conn, '1', '', $s_adm_com_code); // 주문확인
		$cnt_2 = cntOrderGoodsState($conn, '2', '', $s_adm_com_code); // 배송대기
		$cnt_3 = cntOrderGoodsState($conn, '3', '', $s_adm_com_code); // 배송완료
	} else if ($s_adm_cp_type == "판매" ) { 
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

	function js_write() {

		var frm = document.frm;
		
		frm.target = "";
		frm.method = "get";
		frm.action = "delivery_write.php";
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
		frm.method = "post";
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

	/*
	function js_all_check() {
		var frm = document.frm;
		
		if (frm['chk_reserve_no[]'] != null) {
			
			if (frm['chk_reserve_no[]'].length != null) {

				if (frm.all_chk.checked == true) {
					for (i = 0; i < frm['chk_reserve_no[]'].length; i++) {
						frm['chk_reserve_no[]'][i].checked = true;
					}
				} else {
					for (i = 0; i < frm['chk_reserve_no[]'].length; i++) {
						frm['chk_reserve_no[]'][i].checked = false;
					}
				}
			} else {
			
				if (frm.all_chk.checked == true) {
					frm['chk_reserve_no[]'].checked = true;
				} else {
					frm['chk_reserve_no[]'].checked = false;
				}
			}
		}
	}
	*/

	function js_excel() {

		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.target = "";
		frm.action = "<?=str_replace("list","excel_list",$_SERVER[PHP_SELF])?>";
		frm.submit();

	}


	function js_delivery() {

		var frm = document.frm;
		
		frm.mode.value = "U";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	function js_order_delivery_append(reserve_no, order_goods_no) {
		
		frm.reserve_no.value = reserve_no;
		frm.hid_order_goods_no.value = order_goods_no;
		frm.mode.value = "DELIVERY_APPEND";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	function js_all_check() {
		var frm = document.frm;
		
		if (frm['chk_order_no[]'] != null) {
			
			if (frm['chk_order_no[]'].length != null) {

				if (frm.all_chk.checked == true) {
					for (i = 0; i < frm['chk_order_no[]'].length; i++) {
						frm['chk_order_no[]'][i].checked = true;
					}
				} else {
					for (i = 0; i < frm['chk_order_no[]'].length; i++) {
						frm['chk_order_no[]'][i].checked = false;
					}
				}
			} else {
			
				if (frm.all_chk.checked == true) {
					frm['chk_order_no[]'].checked = true;
				} else {
					frm['chk_order_no[]'].checked = false;
				}
			}
		}
	}

	function js_order_confirm() {

		var frm = document.frm;
		
		frm.mode.value = "CU";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	
	}

	function js_trace(url) {

		window.open(url);
		//alert(url);
	}

	function js_delivery_cp_all() {
		var frm = document.frm;
		
		for (i = 0; i < frm['delivery_cp[]'].length ; i++) {
			if (frm['delivery_no[]'][i].value == "") {
				frm['delivery_cp[]'][i].value = frm.delivery_cp_all.value;
			}
		}
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
<input type="hidden" name="hid_order_goods_no" value="">
<input type="hidden" name="use_tf" value="">
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

				<h2>배송 리스트</h2>
				<div class="btnright"><!--<a href="javascript:js_write();"><img src="../images/admin/btn_regist_02.gif" alt="등록" /></a>--></div>
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
						<td colspan="4">
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="start_date" value="<?=$start_date?>" maxlength="10"/>
							 ~ 
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="end_date" value="<?=$end_date?>" maxlength="10"/>
							&nbsp;<a href="javascript:js_search_date('0');"><img src="/manager/images/bu/btn_s_dday.gif" alt="" /></a>
							<a href="javascript:js_search_date('1');"><img src="/manager/images/bu/btn_s_bday.gif" alt="" /></a>
							<a href="javascript:js_search_date('7');"><img src="/manager/images/bu/btn_s_7day.gif" alt="" /></a>
							<a href="javascript:js_search_date('31');"><img src="/manager/images/bu/btn_s_1mon.gif" alt="" /></a>
							
						</td>
					<tr>
					</tr>
						<th>주문상태</th>
						<td>
							<?=makeSelectBoxWithCondition($conn,"ORDER_STATE", "sel_order_state","200", "선택하세요.", "", $sel_order_state, " AND DCODE IN ('1', '2', '3', '7', '8') " );?>
						</td>
						<th>포장(작업) 상태</th>
						<td colspan="2">
							<select name="con_work_flag">
								<option value="">선택하세요.</option>
								<option value="N" <? if ($con_work_flag == "N") echo "selected" ?>>포장(작업)중</option>
								<option value="Y" <? if ($con_work_flag == "Y") echo "selected" ?>>포장 완료</option>
							</select>
						</td>
					</tr>
				</thead>
				<tbody>
					<? if ($s_adm_cp_type == "운영") { ?>
					<tr>
						<!--
						<th>공급업체</th>
						<td>
							<input type="text" class="supplyer" style="width:210px" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'구매',$cp_type)?>" />
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
						 
										$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('구매'), request, function( data, status, xhr ) {
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
							<input type="hidden" name="cp_type" value="<?=$cp_type?>">
						</td>
						-->
						
						<th>판매업체</th>
						<td>
							<input type="text" class="seller" style="width:210px" name="txt_cp_type2" value="<?=getCompanyAutocompleteTextBox($conn,'판매',$cp_type2)?>" />
							<script>
							$(function() {
						     var cache2 = {};
								$( ".seller" ).autocomplete({
									source: function( request, response ) {
										var term = request.term;
										if ( term in cache2 ) {
											response(cache2[term]);
											return;
										}
						 
										$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('판매'), request, function( data, status, xhr ) {
											cache2[term] = data;
											response(data);
										});
									},
									minLength: 2,
									select: function( event, ui ) {
										$(".seller").val(ui.item.value);
										$("input[name=cp_type2]").val(ui.item.id);
									}
								}).bind( "blur", function( event ) {
									var cp_no = $("input[name=cp_type2]").val();
									if(cp_no != '') {
										$.getJSON( "../company/json_company_list.php?cp_no=" + cp_no, function(data) {
											if(data[0].CP_NO == 'undefined') {
												$("input[name=cp_type2]").val('');
											} else {
												if(data[0].COMPANY != $(".seller").val())
												{

													$(".seller").val();
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
						<th>영업담당자</th>
						<td colspan="2"><?= makeAdminInfoByMDSelectBox($conn,"sel_opt_manager_no"," style='width:70px;' ","전체","", $sel_opt_manager_no) ?></td>
					</tr>
					<? } else { ?>
					<input type="hidden" name="cp_type" value="">
					<input type="hidden" name="cp_type2" value="">
					<? }?>
					<tr>
						<th>정렬</th>
						<td>
							<select name="order_field" style="width:94px;">
								<option value="ORDER_DATE" <? if ($order_field == "ORDER_DATE") echo "selected"; ?> >주문일시</option>
								<option value="FINISH_DATE" <? if ($order_field == "FINISH_DATE") echo "selected"; ?> >배송완료일</option>
								<option value="G_REG_DATE" <? if ($order_field == "G_REG_DATE") echo "selected"; ?> >등록일</option>
								<option value="O_MEM_NM" <? if ($order_field == "O_MEM_NM") echo "selected"; ?> >주문자명</option>
								<option value="R_MEM_NM" <? if ($order_field == "R_MEM_NM") echo "selected"; ?> >수령자명</option>
					<? if ($s_adm_cp_type == "운영") { ?>
								<option value="TOTAL_BUY_PRICE" <? if ($order_field == "TOTAL_BUY_PRICE") echo "selected"; ?> >총공급가</option>
								<option value="TOTAL_SALE_PRICE" <? if ($order_field == "TOTAL_SALE_PRICE") echo "selected"; ?> >총판매가</option>
								<option value="TOTAL_EXTRA_PRICE" <? if ($order_field == "TOTAL_EXTRA_PRICE") echo "selected"; ?> >총배송비</option>
								<option value="TOTAL_QTY" <? if ($order_field == "TOTAL_QTY") echo "selected"; ?> >총수량</option>
								<option value="TOTAL_DELIVERY_PRICE" <? if ($order_field == "TOTAL_DELIVERY_PRICE") echo "selected"; ?> >추가배송비</option>
								<option value="TOTAL_PRICE" <? if ($order_field == "TOTAL_PRICE") echo "selected"; ?> >합계</option>
								<option value="TOTAL_PLUS_PRICE" <? if ($order_field == "TOTAL_PLUS_PRICE") echo "selected"; ?> >총판매이익</option>
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
								<option value="O.RESERVE_NO" <? if ($search_field == "O.RESERVE_NO") echo "selected"; ?> >주문번호</option>
								<option value="O_MEM_NM" <? if ($search_field == "O_MEM_NM") echo "selected"; ?> >주문자명</option>
								<option value="R_MEM_NM" <? if ($search_field == "R_MEM_NM") echo "selected"; ?> >수령자명</option>
								<option value="GOODS_NAME" <? if ($search_field == "GOODS_NAME") echo "selected"; ?> >상품명</option>
							</select>&nbsp;

							<input type="text" value="<?=$search_str?>" name="search_str" size="12" class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();"/>
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
					<col width="9%" />
					<col width="10%"/>
					<col width="5%" />
					<col width="7%" />
					<col width="6%" />
					<col width="5%" />
					<col width="5%" />
					<col width="4%" />
					<col width="6%" />
					<col width="10%" />
					<col width="8%" />
					<col width="7%" />
					<col width="9%" />
					<col width="9%" />
				</colgroup>
				<thead>
					<tr>
						<th>주문번호</th>
						<th>판매업체명</th>
						<th>주문자명</th>
						<th>주문자연락처</th>
						<th>수령자명</th>
						<th>우편번호</th>
						<th colspan="4">주소</th>
						<th>담당</th>
						<th>수령자연락처</th>
						<th colspan="2" class="end">주문일시</th>

					</tr>
					<tr>
						<th><input type="checkbox" name="all_chk" onClick="js_all_check();">&nbsp;&nbsp;주문확인</th>
						<th>이미지</th>
						<th colspan="4">상품명</th>
						<th colspan="2">옵션/출고예정일</th>
						<th>수량</th>
						<th colspan="3"><?=makeSelectBoxOnChange($conn,"DELIVERY_CP", "delivery_cp_all","90", "택배사 선택", "", $DELIVERY_CP)?> &nbsp;&nbsp; 송장</th>
						<th>주문상태</th>
						<th class="end">배송완료일</th>
					</tr>
				</thead>
				<tbody>
				<? } ?>

				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$RESERVE_NO							= trim($arr_rs[$j]["RESERVE_NO"]);
							$MEM_TYPE							= trim($arr_rs[$j]["MEM_TYPE"]);
							$MEM_NO								= trim($arr_rs[$j]["MEM_NO"]);
							$CP_NO								= trim($arr_rs[$j]["CP_NO"]);
							
							$PAY_TYPE							= trim($arr_rs[$j]["PAY_TYPE"]);
							$ORDER_CONFIRM_DATE					= trim($arr_rs[$j]["ORDER_CONFIRM_DATE"]);
							
							//$ORDER_STATE						= trim($arr_rs[$j]["ORDER_STATE"]);
							$PAY_STATE							= trim($arr_rs[$j]["PAY_STATE"]);
							$O_MEM_NM							= trim($arr_rs[$j]["O_MEM_NM"]);
							$O_PHONE							= trim($arr_rs[$j]["O_PHONE"]);
							$O_HPHONE							= trim($arr_rs[$j]["O_HPHONE"]);
							$R_MEM_NM							= trim($arr_rs[$j]["R_MEM_NM"]);
							$R_ZIPCODE							= trim($arr_rs[$j]["R_ZIPCODE"]);
							$R_ADDR1							= trim($arr_rs[$j]["R_ADDR1"]);
							$R_PHONE							= trim($arr_rs[$j]["R_PHONE"]);
							$R_HPHONE							= trim($arr_rs[$j]["R_HPHONE"]);
							$TOTAL_BUY_PRICE					= trim($arr_rs[$j]["TOTAL_BUY_PRICE"]);
							$TOTAL_SALE_PRICE					= trim($arr_rs[$j]["TOTAL_SALE_PRICE"]);
							$TOTAL_EXTRA_PRICE					= trim($arr_rs[$j]["TOTAL_EXTRA_PRICE"]);
							$TOTAL_QTY							= trim($arr_rs[$j]["TOTAL_QTY"]);
							$TOTAL_DELIVERY_PRICE				= trim($arr_rs[$j]["TOTAL_DELIVERY_PRICE"]);

							$TOTAL_PRICE						= trim($arr_rs[$j]["TOTAL_PRICE"]);
							$TOTAL_PLUS_PRICE					= trim($arr_rs[$j]["TOTAL_PLUS_PRICE"]);
							$LEE								= trim($arr_rs[$j]["LEE"]);
							
							$OPT_MANAGER_NO						= trim($arr_rs[$j]["OPT_MANAGER_NO"]);
							$admName = getAdminName($conn, $OPT_MANAGER_NO);

							$ORDER_DATE							= trim($arr_rs[$j]["ORDER_DATE"]);
							$PAY_DATE							= trim($arr_rs[$j]["PAY_DATE"]);
							$CANCEL_DATE						= trim($arr_rs[$j]["CANCEL_DATE"]);

							$DELIVERY_TYPE						= trim($arr_rs[$j]["DELIVERY_TYPE"]);
							$REG_DATE							= trim($arr_rs[$j]["REG_DATE"]);
							
							$ORDER_DATE		= date("Y-m-d H:i:s",strtotime($ORDER_DATE));

						?>
						<tr class="order" height="35">
							<td class="order"><a href="javascript:js_view('<?=$rn?>','<?=$RESERVE_NO?>');"><?=$RESERVE_NO?></a></td>
							<? if ($s_adm_cp_type == "운영") { ?>
							<td class="modeual_nm"><?= getCompanyName($conn, $CP_NO);?></td>
							<? }?>
							<td><?=$O_MEM_NM?></td>
							<td><?=$O_HPHONE?></td>
							<td><?=$R_MEM_NM?></td>
							<td><?=$R_ZIPCODE?></td>
							<td colspan="4" class="modeual_nm"><?=$R_ADDR1?></td>
							<td><?=$admName?></td>
							<td><?=$R_HPHONE?></td>
							<td colspan="2" class="filedown"><?=$ORDER_DATE?></td>
						</tr>
						<?
							$arr_goods = listManagerOrderGoods($conn, $RESERVE_NO, $MEM_NO, "Y", "N");

							if (sizeof($arr_goods) > 0) {
								for ($h = 0 ; $h < sizeof($arr_goods); $h++) {
									
									$ORDER_GOODS_NO				= trim($arr_goods[$h]["ORDER_GOODS_NO"]);
									$RESERVE_NO					= trim($arr_goods[$h]["RESERVE_NO"]);
									$BUY_CP_NO					= trim($arr_goods[$h]["BUY_CP_NO"]);
									$CP_ORDER_NO				= trim($arr_goods[$h]["CP_ORDER_NO"]);
									$GOODS_NO					= trim($arr_goods[$h]["GOODS_NO"]);
									$GOODS_NAME					= SetStringFromDB($arr_goods[$h]["GOODS_NAME"]);
									$BUY_PRICE					= trim($arr_goods[$h]["BUY_PRICE"]);
									$SALE_PRICE					= trim($arr_goods[$h]["SALE_PRICE"]);
									$EXTRA_PRICE				= trim($arr_goods[$h]["EXTRA_PRICE"]);

									//C.OPT_STICKER_NO, C.OPT_OUTBOX_TF, C.OPT_OUTBOX_CNT, C.OPT_WRAP_NO, C.OPT_PRINT_MSG, C.OPT_OUTSTOCK_DATE, C.OPT_MEMO

									$DELIVERY_CP				= trim($arr_goods[$h]["DELIVERY_CP"]);
									$DELIVERY_NO				= trim($arr_goods[$h]["DELIVERY_NO"]);

									$DELIVERY_CNT				= trim($arr_goods[$h]["DELIVERY_CNT"]);

									$DELIVERY_TYPE				= trim($arr_goods[$h]["DELIVERY_TYPE"]);

									$SUM_PRICE					= trim($arr_goods[$h]["SUM_PRICE"]);
									$PLUS_PRICE					= trim($arr_goods[$h]["PLUS_PRICE"]);
									$GOODS_LEE					= trim($arr_goods[$h]["LEE"]);
									$QTY						= trim($arr_goods[$h]["QTY"]);
									$PAY_DATE					= trim($arr_goods[$h]["PAY_DATE"]);
									$DELIVERY_DATE				= trim($arr_goods[$h]["DELIVERY_DATE"]);
									$FINISH_DATE				= trim($arr_goods[$h]["FINISH_DATE"]);
									$ORDER_STATE				= trim($arr_goods[$h]["ORDER_STATE"]);
									$ORDER_CONFIRM_DATE			= trim($arr_goods[$h]["ORDER_CONFIRM_DATE"]);

									$OPT_STICKER_NO				= trim($arr_goods[$h]["OPT_STICKER_NO"]);
									$OPT_OUTBOX_TF				= trim($arr_goods[$h]["OPT_OUTBOX_TF"]);
									$OPT_WRAP_NO				= trim($arr_goods[$h]["OPT_WRAP_NO"]);
									$OPT_STICKER_MSG			= trim($arr_goods[$h]["OPT_STICKER_MSG"]);
									$OPT_PRINT_MSG				= trim($arr_goods[$h]["OPT_PRINT_MSG"]);
									$OPT_OUTSTOCK_DATE			= trim($arr_goods[$h]["OPT_OUTSTOCK_DATE"]);
									if($OPT_OUTSTOCK_DATE != "0000-00-00 00:00:00")
										$OPT_OUTSTOCK_DATE			= date("Y-m-d", strtotime($OPT_OUTSTOCK_DATE));
									$OPT_MEMO					= trim($arr_goods[$h]["OPT_MEMO"]);

									$CATE_01					= trim($arr_goods[$h]["CATE_01"]);
									$CATE_04					= trim($arr_goods[$h]["CATE_04"]);
									$WORK_FLAG					= trim($arr_goods[$h]["WORK_FLAG"]);

									
									$IMG_URL = getImage($conn, $GOODS_NO, "50", "50");

									if($CATE_01 <> "")
										$str_cate_01 = $CATE_01.") ";
									else 
										$str_cate_01 = "";

									if ($CATE_04 == "CHANGE") {
										$str_cate_04 = "<font color='red'>(교환건)</font>";
									} else {
										$str_cate_04 = "";
									}

									if ($REQ_DATE <> "")  {
										$REQ_DATE		= date("Y-m-d H:i",strtotime($REQ_DATE));
									}
									
									if ($DELIVERY_CP <> "") {
										if ($FINISH_DATE <> "")  {
											$FINISH_DATE		= date("Y-m-d H:i",strtotime($FINISH_DATE));
										}
									} else {
										$FINISH_DATE = "";
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
									

									
									$option_str	= "";

									$option_str_sub	= "";

									$option_str_sub .= ($OPT_STICKER_NO <> "0" ? "스티커 : ".getGoodsName($conn, $OPT_STICKER_NO). " \r\n" : "");
									$option_str_sub .= ($OPT_OUTBOX_TF == "Y" ? "아웃박스 : 있음 \r\n" : "" );
									$option_str_sub .= ($OPT_WRAP_NO <> "0" ? "포장지 : ".getGoodsName($conn, $OPT_WRAP_NO). " \r\n" : "");
									$option_str_sub .= ($OPT_STICKER_MSG <> "" ? "스티커메세지 : ".$OPT_STICKER_MSG. " \r\n" : "");
									$option_str_sub .= ($OPT_PRINT_MSG <> "" ? "인쇄메세지 : ".$OPT_PRINT_MSG. " \r\n" : "");
									$option_str_sub .= ($OPT_MEMO <> "" ? "작업메모 : ".$OPT_MEMO : "");

									$option_str .= "[".($OPT_OUTSTOCK_DATE != "0000-00-00 00:00:00" ? $OPT_OUTSTOCK_DATE : "출고미정"). "]<br/> ";
									
									if($OPT_STICKER_NO <> "0" || $OPT_OUTBOX_TF == "Y" || $OPT_WRAP_NO <> "0" || $OPT_PRINT_MSG <> "" || $OPT_MEMO) {
										$option_str .= "<font color='red' title='".$option_str_sub."'>포장옵션</font>";
									}
									

									$str_price_class = "price";
									$str_state_class = "state";

									if (($ORDER_STATE == "1") || ($ORDER_STATE == "2")) {
										$refund_able_qty = getRefundAbleQty($conn, $RESERVE_NO, $GOODS_NO);
									
									
									} else if (($ORDER_STATE == "3")) {
										$refund_able_qty = getRealDeliveryQty($conn, $RESERVE_NO, $GOODS_NO);
									
									
									} else if ($ORDER_STATE == "7") {
										$refund_able_qty = -$QTY;

										$str_price_class = "price_refund";
										$str_state_class = "state_refund";

									} else {
										$refund_able_qty = $QTY;
									}
									
									//echo $ORDER_STATE."<br>";

									if (($ORDER_STATE == "1") || ($ORDER_STATE == "2") || ($ORDER_STATE == "3") || ($ORDER_STATE == "7")) {
										//if ($refund_able_qty <> 0) {
						?>
						<tr <?=$str_tr?> height="35">
							<td class="modeual_nm">
								<?=($CP_ORDER_NO != "" ? "<font color='red'>".$CP_ORDER_NO."</font><br/>" : "" )?>
								<? if ($ORDER_STATE <> "1") { ?>
									<?$ORDER_CONFIRM_DATE		= date("Y-m-d H:i",strtotime($ORDER_CONFIRM_DATE));?>
									<?=$ORDER_CONFIRM_DATE?>
								<? } else { ?>
									<input type="checkbox" name="chk_order_no[]" value="<?=$RESERVE_NO?>|<?=$ORDER_GOODS_NO?>">&nbsp;&nbsp;주문확인
								<? } ?>
							</td>
							<? if ($s_adm_cp_type == "운영") { ?>
							<td class="modeual_nm"><img src="<?=$IMG_URL?>" width="50" height="50"/><!--<?= getCompanyName($conn, $BUY_CP_NO);?>--></td>
							<? } ?>
							<td class="modeual_nm" colspan="4"><?=$str_cate_01?><?=$GOODS_NAME?></td>
							<td class="modeual_nm" colspan="2"><?=$option_str?></td>
							<td class="<?=$str_price_class?>"><?=$str_cate_04?> <?=number_format($refund_able_qty)?></td>
							
							<? if ($DELIVERY_TYPE == 0) {?>
							
							<td class="filedown">
								<? if ($ORDER_CONFIRM_DATE) { ?>
								<?=makeSelectBox($conn,"DELIVERY_CP", "delivery_cp[]","90", "택배사 선택", "", $DELIVERY_CP)?>
								<input type="hidden" name="order_goods_no[]" value="<?=$ORDER_GOODS_NO?>">
								<input type="hidden" name="arr_reserve_no[]" value="<?=$RESERVE_NO?>">
								<input type="hidden" name="arr_work_flag[]" value="<?=$WORK_FLAG?>">
								<? } else {?>
									&nbsp;
								<? }?>
							</td>
							<td>
								<? if ($ORDER_CONFIRM_DATE) { ?>
								<?
										if ($DELIVERY_NO) {
											$trace = getDeliveryUrl($conn, $DELIVERY_CP);
											$trace = $trace.$DELIVERY_NO;
										}
								?>
								<input type="text" name="delivery_no[]" value="<?=$DELIVERY_NO?>" class="txt" style="width:90px" <? if ($DELIVERY_NO) {?>onClick="js_trace('<?=$trace?>');" <?}?> >
								<? } else {?>
									&nbsp;
								<? } ?>
								
							</td>
							<?
								} else {
							?>
							<td class="filedown">
								<b><?=getDcodeName($conn,"DELIVERY_TYPE",$DELIVERY_TYPE);?></b>
							</td>
							<td>
								
								<? if ($ORDER_STATE <> "1") { ?>
									배송 완료 : <input type="checkbox" name="delivery_no[]" value="1" <? if ($DELIVERY_NO == 1) echo "checked"; ?> >
									<input type="hidden" name="delivery_cp[]" value="<?=getDcodeName($conn,"DELIVERY_TYPE",$DELIVERY_TYPE);?>" >
									<input type="hidden" name="order_goods_no[]" value="<?=$ORDER_GOODS_NO?>" >
									<input type="hidden" name="arr_reserve_no[]" value="<?=$RESERVE_NO?>">
									<input type="hidden" name="arr_work_flag[]" value="<?=$WORK_FLAG?>">
								<? } else {?>
									&nbsp;
								<? }?>
							</td>
							<?
								}
							?>
							<td class="<?=$str_state_class?>">
								<?=getDcodeName($conn, "ORDER_STATE", $ORDER_STATE);?>
								<? if ($WORK_FLAG == "N") { ?>
								<br><font color="red">(포장중)</font>
								<? } else { ?>
								<br><font color="blue">(포장완료)</font>
								<? } ?>
							</td>
							<td><?=$PAY_DATE?></td>
							<td><?=$FINISH_DATE?></td>
						</tr>
						<?
										//}
									} 
								}
							}
						?>


						<?
						}
					}else{
						?>
						<tr class="order">
							<td height="50" align="center" colspan="14">데이터가 없습니다. </td>
						</tr>
					<?
						}
					?>
				</tbody>
			</table>

			<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">
<? if ($sPageRight_U == "Y") {?>
	<input type="button" name="a0" value=" 주문확인 (배송준비중) " class="btntxt" onclick="js_order_confirm();">&nbsp;&nbsp;&nbsp;
	<input type="button" name="aa" value=" 송장입력 (배송완료) " class="btntxt" onclick="js_delivery();"> 
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
							$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&start_date=".$start_date."&end_date=".$end_date;
							$strParam = $strParam."&sel_order_state=".$sel_order_state."&con_work_flag=".$con_work_flag."&sel_opt_manager_no=".$sel_opt_manager_no."&cp_type=".$cp_type."&cp_type2=".$cp_type2."&order_field=".$order_field."&order_str=".$order_str;
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