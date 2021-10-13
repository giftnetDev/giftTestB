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
	$menu_right = "OD021"; // �޴����� ���� �� �־�� �մϴ�

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

if ($s_adm_cp_type == "����" || $s_adm_cp_type == "�ǸŰ���") { 
	$cp_type = $s_adm_com_code;
}

if ($s_adm_cp_type == "�Ǹ�") { 
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
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/payment/payment.php";
	require "../../_classes/biz/stock/stock.php";
	require "../../_classes/biz/goods/goods.php";

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
			
		}
	}

	if ($mode == "U") {

		$row_cnt = count($order_goods_no);

		$chk_all_deliverys_are_done_in_order_goods = 'Y';

		for ($k = 0; $k < $row_cnt; $k++) {
		
			
			$temp_reserve_no				= $arr_reserve_no[$k];
			$temp_order_goods_no			= $order_goods_no[$k];
			$temp_delivery_cp				= $delivery_cp[$k];
			$temp_delivery_no				= $delivery_no[$k]; 
			$temp_delivery_seq				= $delivery_seq[$k];
			$temp_delivery_date				= $delivery_date[$k];
			$temp_cp_no			         	= $arr_cp_no[$k];
			$temp_is_change		         	= $arr_is_change[$k];

			if($temp_delivery_seq == "" || $temp_delivery_no == "" || $temp_delivery_cp == "" || $temp_delivery_date == "0000-00-00")
				$chk_all_deliverys_are_done_in_order_goods = 'N';

			if($order_goods_no[$k+1] == "" || $order_goods_no[$k+1] != $temp_order_goods_no)
			{
				if($chk_all_deliverys_are_done_in_order_goods == 'Y')
				{
					//���� ORDER_GOODS�� ����� ��� �Ϸ�� ���̹Ƿ� �Ϸ�ó��
					updateDeliveryState($conn, $temp_reserve_no, $temp_order_goods_no, $temp_delivery_cp, $temp_delivery_no, $s_adm_no);
				}

				$chk_all_deliverys_are_done_in_order_goods = 'Y';
			}
			
		}
		
	}

	if ($mode == "D") {
		$row_cnt = count($chk_reserve_no);

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_reserve_no = $chk_reserve_no[$k];
			
			$result = deleteOrderPackage($conn, $str_reserve_no, $s_adm_no);
		
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
	$row =totalCntManagerDeliveryPackage($conn, $start_date, $end_date, $sel_order_state, $cp_type, $cp_type2, $sel_pay_type, $con_use_tf, $del_tf, $search_field, $search_str);

	$nListCnt = $row[0];
	$nListCnt2 = $row[1];
	
	#echo $nListCnt;

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listManagerDeliveryPackage($conn, $start_date, $end_date, $sel_order_state, $cp_type, $cp_type2, $sel_pay_type, $con_use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize);

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
		frm.action = "delivery_write.php";
		frm.submit();
		
	}

	function js_view(rn, reserve_no) {

		var frm = document.frm;
		
		var url = "order_read.php?reserve_no="+reserve_no;

		NewWindow(url, '','860','600','YES'); //window name : ����â���� ���� �̸� ���� order_detail
		
	}

	
	function js_order_forced_complete(order_goods_no) {

		var frm = document.frm;
		
		var url = "order_goods_complete.php?order_goods_no="+order_goods_no;

		NewWindow(url, 'order_goods_complete','800','400','YES');
		
	}

	// ��ȸ ��ư Ŭ�� �� 
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_all_reserve_check() {
		var frm = document.frm;
		
		if (frm['chk_reserve_no[]'] != null) {
			
			if (frm['chk_reserve_no[]'].length != null) {

				if (frm.all_reserve_chk.checked == true) {
					for (i = 0; i < frm['chk_reserve_no[]'].length; i++) {
						frm['chk_reserve_no[]'][i].checked = true;
					}
				} else {
					for (i = 0; i < frm['chk_reserve_no[]'].length; i++) {
						frm['chk_reserve_no[]'][i].checked = false;
					}
				}
			} else {
			
				if (frm.all_reserve_chk.checked == true) {
					frm['chk_reserve_no[]'].checked = true;
				} else {
					frm['chk_reserve_no[]'].checked = false;
				}
			}
		}
	}

	function js_excel() {

		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.target = "blank";
		frm.mode.value = "normal";
		frm.action = "<?=str_replace("list","excel_list",$_SERVER[PHP_SELF])?>";
		frm.submit();

	}

	function js_excel_delivered() {

		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.target = "blank";
		frm.mode.value = "delivered";
		frm.action = "<?=str_replace("list","excel_list",$_SERVER[PHP_SELF])?>";
		frm.submit();

	}

	function js_excel_goods() {
		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.target = "blank";
		frm.mode.value = "delivering";
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

	function js_delete() {
		var frm = document.frm;

		bDelOK = confirm('������ �����Ͻðڽ��ϱ�?');
		
		if (bDelOK==true) {
			
			frm.mode.value = "D";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_all_order_check() {
		var frm = document.frm;
		
		if (frm['chk_order_no[]'] != null) {
			
			if (frm['chk_order_no[]'].length != null) {

				if (frm.all_order_chk.checked == true) {
					for (i = 0; i < frm['chk_order_no[]'].length; i++) {
						frm['chk_order_no[]'][i].checked = true;
					}
				} else {
					for (i = 0; i < frm['chk_order_no[]'].length; i++) {
						frm['chk_order_no[]'][i].checked = false;
					}
				}
			} else {
			
				if (frm.all_order_chk.checked == true) {
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

	function js_pop_delivery_paper_frame(delivery_cp, delivery_no) { 

		var url = "/manager/order/pop_delivery_paper_wrapper.php?delivery_cp=" + delivery_cp + "&delivery_no=" + delivery_no;
		NewWindow(url, 'pop_delivery_paper_wrapper', '920', '700', 'YES');

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
		
		<? if ($s_adm_cp_type == "�") { ?>
			frm.cp_type.value = "";
			frm.cp_type2.value = "";
		<? } ?>
		
		frm.order_field.value = "ORDER_DATE";
		frm.order_str[0].checked = true;
		frm.nPageSize.value = "20";
		frm.search_field.value = "ALL";
		frm.search_str.value = "";
	}

	function js_delivery_paper_detail(order_goods_delivery_no) {

		var frm = document.frm;
		var url = "pop_delivery_paper_detail.php?order_goods_delivery_no="+order_goods_delivery_no;
		NewWindow(url, 'pop_delivery_paper_detail','900','550','NO');

	}

	function js_search_reserveno(delivery_no) {

		if(delivery_no == undefined)
		{
			delivery_no = $("input[name=reserve_no]").val();
			$("input[name=reserve_no]").val('');
		}
		var url = "/manager/order/pop_search_order.php?mode=S&search_text="+delivery_no;
		NewWindow(url, 'pop_search_order','900','550','NO');

	}

	function js_popup_gift() {

		var url = "pop_send_gift.php";
		var frm = document.frm;
		NewWindow('about:blank', 'pop_send_gift', '860', '513', 'YES');
		frm.target = "pop_send_gift";
		frm.action = url;
		frm.submit();
	}

	function js_reload() {
		window.location.reload();
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

				<h2>��� ����Ʈ - ����</h2>
				<div class="btnright"><!--<a href="javascript:js_write();"><img src="../images/admin/btn_regist_02.gif" alt="���" /></a>--></div>
				<div class="category_choice"><!--<select style="width: 100px;"><option>��ǰ����</option></select>-->&nbsp;</div>

				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="10%" />
					<col width="35%" />
					<col width="10%" />
					<col width="35%" />
					<col width="10%" />
				</colgroup>
				<thead>
					<tr>
						<th>�ֹ���</th>
						<td>
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="start_date" value="<?=$start_date?>" maxlength="10"/>
							 ~ 
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="end_date" value="<?=$end_date?>" maxlength="10"/>
							&nbsp;<a href="javascript:js_search_date('0');"><img src="/manager/images/bu/btn_s_dday.gif" alt="" /></a>
							<a href="javascript:js_search_date('1');"><img src="/manager/images/bu/btn_s_bday.gif" alt="" /></a>
							<a href="javascript:js_search_date('7');"><img src="/manager/images/bu/btn_s_7day.gif" alt="" /></a>
							<a href="javascript:js_search_date('31');"><img src="/manager/images/bu/btn_s_1mon.gif" alt="" /></a>
							
						</td>
						<td><!--���޾�ü--></td>
						<td colspan="2"><input type="hidden" name="cp_type" value="">
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>�Ǹž�ü</th>
						<td>
							<input type="text" class="seller" style="width:90%" placeholder="��ü(��/�ڵ�) �Է��� ���͸� �����ּ���" name="txt_cp_type2" value="<?=getCompanyAutocompleteTextBox($conn,'',$cp_type2)?>" />
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
							<!--
							<input type="text" class="seller" style="width:90%" placeholder="��ü(��/�ڵ�) �Է����ּ���" name="txt_cp_type2" value="<?=getCompanyAutocompleteTextBox($conn,'�Ǹ�',$cp_type2)?>" />
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
						 
										$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('�Ǹ�'), request, function( data, status, xhr ) {
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
							-->
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
						
						<th>�ֹ�����</th>
						<td>
							<?=makeSelectBoxWithCondition($conn,"ORDER_STATE", "sel_order_state","200", "�����ϼ���.", "", $sel_order_state, " AND DCODE IN ('1', '2', '3', '7', '8') " );?>
						</td>
						<td align="right">
						</td>
					</tr>
					<tr>
						<th>����</th>
						<td>
							<select name="order_field" style="width:94px;">
								<option value="ORDER_DATE" <? if ($order_field == "ORDER_DATE") echo "selected"; ?> >�ֹ��Ͻ�</option>
								<option value="FINISH_DATE" <? if ($order_field == "FINISH_DATE") echo "selected"; ?> >��ۿϷ���</option>
								<option value="REG_DATE" <? if ($order_field == "REG_DATE") echo "selected"; ?> >�����</option>
								<option value="O_MEM_NM" <? if ($order_field == "O_MEM_NM") echo "selected"; ?> >�ֹ��ڸ�</option>
								<option value="R_MEM_NM" <? if ($order_field == "R_MEM_NM") echo "selected"; ?> >�����ڸ�</option>
					<? if ($s_adm_cp_type == "�") { ?>
								<option value="TOTAL_BUY_PRICE" <? if ($order_field == "TOTAL_BUY_PRICE") echo "selected"; ?> >�Ѱ��ް�</option>
								<option value="TOTAL_SALE_PRICE" <? if ($order_field == "TOTAL_SALE_PRICE") echo "selected"; ?> >���ǸŰ�</option>
								<option value="TOTAL_EXTRA_PRICE" <? if ($order_field == "TOTAL_EXTRA_PRICE") echo "selected"; ?> >�ѹ�ۺ�</option>
								<option value="TOTAL_QTY" <? if ($order_field == "TOTAL_QTY") echo "selected"; ?> >�Ѽ���</option>
								<option value="TOTAL_DELIVERY_PRICE" <? if ($order_field == "TOTAL_DELIVERY_PRICE") echo "selected"; ?> >�߰���ۺ�</option>
								<option value="TOTAL_PRICE" <? if ($order_field == "TOTAL_PRICE") echo "selected"; ?> >�հ�</option>
								<option value="TOTAL_PLUS_PRICE" <? if ($order_field == "TOTAL_PLUS_PRICE") echo "selected"; ?> >���Ǹ�����</option>
					<? } ?>
							</select>&nbsp;&nbsp;
							<input type='radio' class="" name='order_str' value='DESC' <? if (($order_str == "DESC") || ($order_str == "")) echo " checked"; ?> > �������� &nbsp;
							<input type='radio' name='order_str' value='ASC' <? if ($order_str == "ASC") echo " checked"; ?>> ��������
						</td>

						<th>�˻�����</th>
						<td>
							<select name="nPageSize" style="width:62px;">
								<option value="20" <? if ($nPageSize == "20") echo "selected"; ?> >20����</option>
								<option value="50" <? if ($nPageSize == "50") echo "selected"; ?> >50����</option>
								<option value="100" <? if ($nPageSize == "100") echo "selected"; ?> >100����</option>
								<option value="200" <? if ($nPageSize == "200") echo "selected"; ?> >200����</option>
								<option value="300" <? if ($nPageSize == "300") echo "selected"; ?> >300����</option>
								<option value="400" <? if ($nPageSize == "400") echo "selected"; ?> >400����</option>
								<option value="500" <? if ($nPageSize == "500") echo "selected"; ?> >500����</option>
							</select>&nbsp;
							<select name="search_field" style="width:100px;">
								<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >���հ˻�</option>
								<option value="O.RESERVE_NO" <? if ($search_field == "O.RESERVE_NO") echo "selected"; ?> >�ֹ���ȣ</option>
								<option value="G.CP_ORDER_NO" <? if ($search_field == "CP_ORDER_NO") echo "selected"; ?> >��ü�ֹ���ȣ</option>
								<option value="CP_ORDER_NO_MULTI" <? if ($search_field == "CP_ORDER_NO_MULTI") echo "selected"; ?> >��ü�ֹ���ȣ(����)</option>
								<option value="O_MEM_NM" <? if ($search_field == "O_MEM_NM") echo "selected"; ?> >�ֹ��ڸ�</option>
								<option value="O.O_HPHONE" <? if ($search_field == "O.O_HPHONE") echo "selected"; ?> >�ֹ�����ȭ��ȣ</option>
								<option value="R_MEM_NM" <? if ($search_field == "R_MEM_NM") echo "selected"; ?> >�����ڸ�</option>
								<option value="O.R_HPHONE" <? if ($search_field == "O.R_HPHONE") echo "selected"; ?> >��������ȭ��ȣ</option>
								<option value="O.R_ADDR1" <? if ($search_field == "O.R_ADDR1") echo "selected"; ?> >�������ּ�</option>
								<option value="GOODS_NAME" <? if ($search_field == "GOODS_NAME") echo "selected"; ?> >��Ʈ��ǰ��</option>
								<option value="MART_GOODS_CODE" <? if ($search_field == "MART_GOODS_CODE") echo "selected"; ?> >��Ʈ��ǰ��ȣ</option>
								<option value="SUB_GOODS_NAME" <? if ($search_field == "SUB_GOODS_NAME") echo "selected"; ?> >������ǰ��</option>
								<option value="SUB_GOODS_CODE" <? if ($search_field == "SUB_GOODS_CODE") echo "selected"; ?> >������ǰ�ڵ�</option>
								<option value="GOODS_OPTION_NM_02" <? if ($search_field == "GOODS_OPTION_NM_02") echo "selected"; ?> >�̸�Ʈ��۹�ȣ</option>
							</select>&nbsp;

							<input type="text" value="<?=$search_str?>" name="search_str" width="155px" class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();" />
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
							<!--a href="javascript:js_reset();"><img src="/manager/images/admin/btn_in.gif" alt="reset"/></a -->
						</td>
						<td align="right">
							<!--
							<select name="print_type">
								<option value="screen">ȭ��״��</option>
								<option value="selected">���õȻ�ǰ��</option>
							</select>
							<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="���� ����Ʈ" /></a><br/>
							-->
						</td>
					</tr>
					
				</tbody>
			</table>
			<div class="sp20"></div>
						
			<div style="width: 95%; text-align: right; margin: 0 0 0 0;">
			<? if ($sPageRight_U == "Y") {?>
				<input type="button" name="a0" value=" �ֹ�Ȯ�� (����غ���) " class="btntxt" onclick="js_order_confirm();">&nbsp;&nbsp;&nbsp;
				<input type="button" name="aa" value=" ����Ȯ�� (��ۿϷ�) " class="btntxt" onclick="js_delivery();">
			<? } ?>
			</div>

			<b>�� �ֹ� <?=$nListCnt?> ��, �� �ֹ���ǰ <?=$nListCnt2?> ��</b>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<!--<b><font color="orange">��ó���ֹ�</font></b>-->&nbsp;&nbsp;&nbsp;&nbsp;
			<!--<b><font color="blue">�Ա���</font> <font color="red"><?=$cnt_0?></font> <font color="blue">��</font></b>&nbsp;&nbsp;-->
			<!--<b><font color="blue">�ֹ�Ȯ����</font> <font color="red"><?=$cnt_1?></font> <font color="blue">��</font></b>&nbsp;&nbsp;
			<b><font color="blue">��ۿϷ���</font> <font color="red"><?=$cnt_2?></font> <font color="blue">��</font></b>&nbsp;&nbsp;-->
			<!--<b><font color="blue">��ۿϷ���</font> <font color="red"><?=$cnt_3?></font> <font color="blue">��</font></b>&nbsp;&nbsp;-->

			<table cellpadding="0" cellspacing="0" class="rowstable02" border="0">
				
				<? if ($s_adm_cp_type == "�") { ?>
				<colgroup>
					<col width="2%" />
					<col width="7%" />
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
						<th><input type="checkbox" name="all_reserve_chk" onClick="js_all_reserve_check();"></th>
						<th>�ֹ���ȣ</th>
						<th>�Ǹž�ü��</th>
						<th>�ֹ��ڸ�</th>
						<th>�ֹ��ڿ���ó</th>
						<th>�����ڸ�</th>
						<th>�����ȣ</th>
						<th colspan="5">�ּ�</th>
						<th>�����ڿ���ó</th>
						<th colspan="2" class="end">�ֹ��Ͻ�</th>

					</tr>
					<tr>
						<th style="background-color:#ffcdcf;"><input type="checkbox" name="all_order_chk" onClick="js_all_order_check();"></th>
						<th>�ֹ�Ȯ��</th>
						<th>���޾�ü��</th>
						<th colspan="3">
							�ֹ���ǰ��
						</th>
						<th colspan="3">
							�ɼ�
						</th>
						<th>����</th>
						<th colspan="3"><?=makeSelectBoxOnChange($conn,"DELIVERY_CP", "delivery_cp_all","90", "�ù�� ����", "", $DELIVERY_CP)?> &nbsp;&nbsp; ����</th>
						<th>�ֹ�����</th>
						<th class="end">��ۿϷ���</th>
					</tr>
				</thead>
				<tbody>
				<? } ?>

				<?
					$nCnt = 0;
					
					$SUB_GOODS_TOTAL = array();

					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$RESERVE_NO						= trim($arr_rs[$j]["RESERVE_NO"]);
							$MEM_TYPE							= trim($arr_rs[$j]["MEM_TYPE"]);
							$MEM_NO								= trim($arr_rs[$j]["MEM_NO"]);
							$CP_NO								= trim($arr_rs[$j]["CP_NO"]);
							
							$PAY_TYPE							= trim($arr_rs[$j]["PAY_TYPE"]);
							$ORDER_CONFIRM_DATE		= trim($arr_rs[$j]["ORDER_CONFIRM_DATE"]);
							
							//$ORDER_STATE					= trim($arr_rs[$j]["ORDER_STATE"]);
							$PAY_STATE						= trim($arr_rs[$j]["PAY_STATE"]);
							$O_MEM_NM							= trim($arr_rs[$j]["O_MEM_NM"]);
							$O_PHONE							= trim($arr_rs[$j]["O_PHONE"]);
							$O_HPHONE							= trim($arr_rs[$j]["O_HPHONE"]);
							$R_MEM_NM							= trim($arr_rs[$j]["R_MEM_NM"]);
							$R_ZIPCODE						= trim($arr_rs[$j]["R_ZIPCODE"]);
							$R_ADDR1							= trim($arr_rs[$j]["R_ADDR1"]);
							$R_PHONE							= trim($arr_rs[$j]["R_PHONE"]);
							$R_HPHONE							= trim($arr_rs[$j]["R_HPHONE"]);
							$TOTAL_BUY_PRICE			= trim($arr_rs[$j]["TOTAL_BUY_PRICE"]);
							$TOTAL_SALE_PRICE			= trim($arr_rs[$j]["TOTAL_SALE_PRICE"]);
							$TOTAL_EXTRA_PRICE		= trim($arr_rs[$j]["TOTAL_EXTRA_PRICE"]);
							$TOTAL_QTY						= trim($arr_rs[$j]["TOTAL_QTY"]);
							$TOTAL_DELIVERY_PRICE	= trim($arr_rs[$j]["TOTAL_DELIVERY_PRICE"]);
							
							$TOTAL_PRICE					= trim($arr_rs[$j]["TOTAL_PRICE"]);
							$TOTAL_PLUS_PRICE			= trim($arr_rs[$j]["TOTAL_PLUS_PRICE"]);
							$LEE									= trim($arr_rs[$j]["LEE"]);
							
							$ORDER_DATE						= trim($arr_rs[$j]["ORDER_DATE"]);
							$PAY_DATE							= trim($arr_rs[$j]["PAY_DATE"]);
							$CANCEL_DATE					= trim($arr_rs[$j]["CANCEL_DATE"]);

							$DELIVERY_TYPE			= trim($arr_rs[$j]["DELIVERY_TYPE"]);
							$REG_DATE						= trim($arr_rs[$j]["REG_DATE"]);
							
							$ORDER_DATE		= date("Y-m-d H:i:s",strtotime($ORDER_DATE));

							if ($TOTAL_QTY == 0)
								$str_cancel_style = "cancel_order";
							else
								$str_cancel_style = "";

						?>
						<tr class="order <?=$str_cancel_style?>" height="35">
							<td><input type="checkbox" name="chk_reserve_no[]" value="<?=$RESERVE_NO?>"></td>
							<td class="order"><a href="javascript:js_view('<?=$rn?>','<?=$RESERVE_NO?>');"><?=$RESERVE_NO?></a></td>
							<? if ($s_adm_cp_type == "�") { ?>
							<td class="modeual_nm"><?= getCompanyName($conn, $CP_NO);?></td>
							<? }?>
							<td><?=$O_MEM_NM?></td>
							<td><?=$O_HPHONE?></td>
							<td><?=$R_MEM_NM?></td>
							<td><?=$R_ZIPCODE?></td>
							<td colspan="5" class="modeual_nm"><?=$R_ADDR1?></td>
							<td><?=$R_HPHONE?></td>
							<td colspan="2" class="filedown"><?=$ORDER_DATE?></td>
						</tr>
						<?
							$arr_goods = listManagerOrderGoods($conn, $RESERVE_NO, $MEM_NO, "Y", "N");

							if (sizeof($arr_goods) > 0) {
								for ($h = 0 ; $h < sizeof($arr_goods); $h++) {
									
									$ORDER_GOODS_NO			= trim($arr_goods[$h]["ORDER_GOODS_NO"]);
									$RESERVE_NO					= trim($arr_goods[$h]["RESERVE_NO"]);
									$BUY_CP_NO					= trim($arr_goods[$h]["BUY_CP_NO"]);
									$GOODS_NO						= trim($arr_goods[$h]["GOODS_NO"]);
									$GOODS_NAME					= SetStringFromDB($arr_goods[$h]["GOODS_NAME"]);
									$BUY_PRICE					= trim($arr_goods[$h]["BUY_PRICE"]);
									$SALE_PRICE					= trim($arr_goods[$h]["SALE_PRICE"]);
									$EXTRA_PRICE				= trim($arr_goods[$h]["EXTRA_PRICE"]);

									$OPT_STICKER_NO				= trim($arr_goods[$h]["OPT_STICKER_NO"]);

									$DELIVERY_CP				= trim($arr_goods[$h]["DELIVERY_CP"]);
									$DELIVERY_NO				= trim($arr_goods[$h]["DELIVERY_NO"]);
									
									$DELIVERY_CNT				= trim($arr_goods[$h]["DELIVERY_CNT"]);
									$DELIVERY_PROFIT				= trim($arr_goods[$h]["DELIVERY_PROFIT"]);

									$SUM_PRICE					= trim($arr_goods[$h]["SUM_PRICE"]);
									$PLUS_PRICE					= trim($arr_goods[$h]["PLUS_PRICE"]);
									$GOODS_LEE					= trim($arr_goods[$h]["LEE"]);
									$QTY								= trim($arr_goods[$h]["QTY"]);
									$PAY_DATE						= trim($arr_goods[$h]["PAY_DATE"]);
									$DELIVERY_DATE			= trim($arr_goods[$h]["DELIVERY_DATE"]);
									$FINISH_DATE				= trim($arr_goods[$h]["FINISH_DATE"]);
									$ORDER_STATE				= trim($arr_goods[$h]["ORDER_STATE"]);
									$ORDER_CONFIRM_DATE = trim($arr_goods[$h]["ORDER_CONFIRM_DATE"]);

									$GOODS_STATE           = trim($arr_goods[$h]["GOODS_STATE"]);
									if($GOODS_STATE != '�Ǹ���' && $GOODS_STATE != '���Ǹ�')
										$style_goods_state = 'soldout_goods';
									else
										$style_goods_state = '';
									


									$CATE_04						= trim($arr_goods[$h]["CATE_04"]);

									if ($CATE_04 == "CHANGE") {
										$str_cate_04 = "<font color='red'>(��ȯ��)</font>";
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
											$str_tr = "goods_1_end";
										} else if ($ORDER_STATE == "3") {
											$str_tr = "goods_3_end";
										} else {
											$str_tr = "goods_end";
										}

									} else {

										if ($ORDER_STATE == "1") {
											$str_tr = "goods_1";
										} else if ($ORDER_STATE == "3") {
											$str_tr = "goods_3";
										} else {
											$str_tr = "goods";
										}
									}

									$option_str = "";

									if ($OPT_STICKER_NO <> "") {
										//$sticker_img		= getImage($conn, $OPT_STICKER_NO, "", "");
										$sticker_name		= getGoodsName($conn, $OPT_STICKER_NO);
										$option_str .= $sticker_name." ";
									}

									$str_price_class = "price";
									$str_state_class = "state";

									if (($ORDER_STATE == "1") || ($ORDER_STATE == "2")) {
										$refund_able_qty = getRefundAbleQty($conn, $RESERVE_NO, $ORDER_GOODS_NO);
									
									} else if (($ORDER_STATE == "3")) {
										$refund_able_qty = getRealDeliveryQty($conn, $RESERVE_NO, $ORDER_GOODS_NO);
									
									
									} else if ($ORDER_STATE == "7") {
										$refund_able_qty = -$QTY;

										$str_price_class = "price_refund";
										$str_state_class = "state_refund";

									} else {
										$refund_able_qty = $QTY;
									}

									//echo $refund_able_qty;
									if ($refund_able_qty == 0)
										$str_cancel_style = "cancel_goods";
									else
										$str_cancel_style = "";
									
									//echo $ORDER_STATE."<br>";

									if (($ORDER_STATE == "1") || ($ORDER_STATE == "2") || ($ORDER_STATE == "3") || ($ORDER_STATE == "7")) {

									//if ($refund_able_qty <> 0) {
						?>
						<tr class="<?=$str_tr?> <?=$str_cancel_style?> <?=$soldout_goods?> <?=$style_goods_state?>"  height="35">
							<td>
								<? if ($ORDER_STATE == "1") { ?>
									<input type="checkbox" name="chk_order_no[]" value="<?=$RESERVE_NO?>|<?=$ORDER_GOODS_NO?>">
								<? } ?>
							</td>
							<td class="modeual_nm">
								<? if ($ORDER_STATE <> "1") { ?>
									<?	
										if($ORDER_CONFIRM_DATE != "")
											$ORDER_CONFIRM_DATE		= date("Y-m-d H:i",strtotime($ORDER_CONFIRM_DATE));
										else 
											$ORDER_CONFIRM_DATE = "";
									?>
									<?=$ORDER_CONFIRM_DATE?>
								<? } else { ?>
									�ֹ�Ȯ��
								<? } ?>
								<?=$GOODS_STATE != '�Ǹ���' && $GOODS_STATE != '���Ǹ�' ? "(".$GOODS_STATE.")" : ""?>
							</td>
							<? if ($s_adm_cp_type == "�") { ?>
							<td class="modeual_nm"><?= getCompanyName($conn, $BUY_CP_NO);?></td>
							<? } ?>
							
							<td class="modeual_nm" colspan="3">
							<?
								

									if(($search_field == "GOODS_NAME" && strpos($GOODS_NAME, $search_str) !== FALSE))
										echo "<span style='color:blue;'>".$GOODS_NAME."</span>";
									else
										echo $GOODS_NAME;

							?>
							
							</td>
							<td class="modeual_nm" colspan="3">
							<?
									echo $option_str;
							?></td>
							<td class="<?=$str_price_class?>"><?=$str_cate_04?> 
							<?
								if($search_str != "") { 
									if(($search_field == "GOODS_NAME" && strpos($GOODS_NAME, $search_str) !== FALSE) || 
										($search_field == "SUB_GOODS_NAME" && strpos($SUB_GOODS_NAME, $search_str) !== FALSE) || 
											($search_field == "SUB_GOODS_CODE" && strpos($SUB_GOODS_CODE, $search_str) !== FALSE))
										echo "<span style='color:blue; font-weight:bold;'>".number_format($refund_able_qty)."</span>";
									else
										echo number_format($refund_able_qty);
							} else
									echo number_format($refund_able_qty);
							
							?>
								<input type="hidden" name="order_qty[]" value="<?=number_format($refund_able_qty)?>" class="txt" style="width:90px">
							</td>
							<td class="filedown" colspan="3">
								<?
									if($ORDER_STATE <> "7") {
								?>
									<? if ($ORDER_CONFIRM_DATE) { 

										$arr_delivery = listOrderDeliveryPackage($conn, $ORDER_GOODS_NO);
										if(sizeof($arr_delivery) > 0) {
											for($k = 0; $k < sizeof($arr_delivery); $k++) {

												$ORDER_GOODS_DELIVERY_NO = $arr_delivery[$k]["ORDER_GOODS_DELIVERY_NO"];
												$rs_delivery_seq = $arr_delivery[$k]["DELIVERY_SEQ"];
												$rs_delivery_cp  = $arr_delivery[$k]["DELIVERY_CP"];
												$rs_delivery_no  = $arr_delivery[$k]["DELIVERY_NO"];
												$rs_delivery_date	= $arr_delivery[$k]["DELIVERY_DATE"];
												$rs_outstock_tf		= $arr_delivery[$k]["OUTSTOCK_TF"];
												$rs_use_tf			= $arr_delivery[$k]["USE_TF"];

												if($rs_delivery_date == "")
													$rs_delivery_date = "0000-00-00";

												if($rs_use_tf == 'N') { 
													$style_delivery_state = "style='color:gray;'";
													$str_delivery_state = "<span style='color:gray;'>���</span>";
												} else {
													if($rs_delivery_date != "0000-00-00") {
														$style_delivery_state = "style='color:green;'";
														$str_delivery_state = "<span style='color:green;'>�߼�</span>";
													} else { 	
														$style_delivery_state = "style='color:red;'";
														$str_delivery_state = "<span style='color:red;'>�߼���</span>";
													}
												}

									?>
										
										<?

											if($rs_use_tf == 'Y') { 
										?>
											
											<input type="hidden" name="delivery_cp[]" value="<?=$rs_delivery_cp?>"  >
											<input type="hidden" name="order_goods_no[]" value="<?=$ORDER_GOODS_NO?>"  >
											<input type="hidden" name="arr_reserve_no[]" value="<?=$RESERVE_NO?>"  >
											<input type="hidden" name="delivery_seq[]" value="<?=$rs_delivery_seq?>"  >
											<input type="hidden" name="delivery_no[]" value="<?=$rs_delivery_no?>"  >
											<input type="hidden" name="delivery_date[]" value="<?=$rs_delivery_date?>"  >
											<input type="hidden" name="arr_cp_no[]" value="<?=$CP_NO?>"  >
											<input type="hidden" name="arr_is_change[]" value="<?=($CATE_04 != "" ? "Y" : "N")?>"  >
											
											<?
											}
											?>
											<span <?=$style_delivery_state?>>
											<a href="#" onclick="js_delivery_paper_detail('<?=$ORDER_GOODS_DELIVERY_NO?>'); return false;"><?=$rs_delivery_seq?></a>
										
											<a href="#" <? if ($rs_delivery_no) {?>onClick="js_pop_delivery_paper_frame('<?=$rs_delivery_cp?>', '<?=$rs_delivery_no?>');" title="<?=$rs_delivery_no?>" <?}?> style="font-weight:bold;" ><?=$rs_delivery_no?></a>
											<?=$str_delivery_state?>
											
											<br/>
									<?
											}

										}
									 } 
									?>
								<? 
									} 
								?>
							</td>
							<td class="<?=$str_state_class?>">
								<? if ($ORDER_STATE=="2") { ?>
								<a href="javascript:js_order_forced_complete('<?=$ORDER_GOODS_NO?>');">
									<?=getDcodeName($conn, "ORDER_STATE", $ORDER_STATE);?>
								</a>
								<? } else { ?>
									<?=getDcodeName($conn, "ORDER_STATE", $ORDER_STATE);?>
								<? } ?>
							</td>
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
							<td height="50" align="center" colspan="14">�����Ͱ� �����ϴ�. </td>
						</tr>
					<?
						}
					?>
				</tbody>
			</table>

			<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">
				<? if ($sPageRight_U == "Y") {?>
					<input type="button" name="a0" value=" �ֹ�Ȯ�� (����غ���) " class="btntxt" onclick="js_order_confirm();">&nbsp;&nbsp;&nbsp;
					<input type="button" name="aa" value=" ����Ȯ�� (��ۿϷ�) " class="btntxt" onclick="js_delivery();">  
				<? } ?>

				<div style="float:left;">
				<? if ($sPageRight_D == "Y") {?>
					<input type="button" name="aa" value=" ������ �ֹ� ���� " class="btntxt" onclick="js_delete();"> 
				<? } ?>
				</div>
			</div>
					<!-- --------------------- ������ ó�� ȭ�� START -------------------------->
					<?
						# ==========================================================================
						#  ����¡ ó��
						# ==========================================================================
						if (sizeof($arr_rs) > 0) {
							#$search_field		= trim($search_field);
							#$search_str			= trim($search_str);
							$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&start_date=".$start_date."&end_date=".$end_date;
							$strParam = $strParam."&sel_order_state=".$sel_order_state."&cp_type=".$cp_type."&cp_type2=".$cp_type2."&order_field=".$order_field."&order_str=".$order_str;
							

					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- ������ ó�� ȭ�� END -------------------------->
				<br />

				<div class="sp20"></div>
		<!-- // E: mwidthwrap -->
			</div>
			<!-- // E: mwidthwrap -->

		</td>
	</tr>
	<tr>
		<td colspan="2" height="70"><div class="copyright"></div></td>
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