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
	$menu_right = "CF003"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

if ($s_adm_cp_type == "구매" || $s_adm_cp_type == "판매공급") { 
	$cp_type = $s_adm_com_code;
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


#====================================================================
# Request Parameter
#====================================================================


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

	if ($confirm_ymd == "") {
		$confirm_ymd = date("Y-m-d",strtotime("0 month"));
	} else {
		$confirm_ymd = trim($confirm_ymd);
	}

	$con_cate_01 = trim($con_cate_01);
	$con_cate_02 = trim($con_cate_02);
	$con_cate_03 = trim($con_cate_03);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
	$del_tf = "N";
#============================================================
# Page process
#============================================================
	
#===============================================================
# Get Search list count
#===============================================================

	$arr_rs = listBuyConfirmList($conn, $start_date, $end_date, $cp_type, $ad_type, $con_tax_tf, $order_field, $order_str);

	$arr_rs_all = listBuyConfirmAll($conn, $start_date, $end_date, $cp_type, $ad_type, $con_tax_tf);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
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

	function js_view(rn, reserve_no) {

		var frm = document.frm;
		
		var url = "/manager/order/order_read.php?reserve_no="+reserve_no;

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

	if (frm('chk_order_goods_no[]') == null) {
		alert("선택할 주문이 없습니다.");
		return;
	}

	if (frm('chk_order_goods_no[]').length != null) {
		
		for (i = 0 ; i < frm('chk_order_goods_no[]').length; i++) {
			if (frm('chk_order_goods_no[]')[i].checked == true) {
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
		
		if (frm['chk_order_goods_no[]'] != null) {
			
			if (frm['chk_order_goods_no[]'].length != null) {

				if (frm.all_chk.checked == true) {
					for (i = 0; i < frm['chk_order_goods_no[]'].length; i++) {
						frm['chk_order_goods_no[]'][i].checked = true;
					}
				} else {
					for (i = 0; i < frm['chk_order_goods_no[]'].length; i++) {
						frm['chk_order_goods_no[]'][i].checked = false;
					}
				}
			} else {
			
				if (frm.all_chk.checked == true) {
					frm['chk_order_goods_no[]'].checked = true;
				} else {
					frm['chk_order_goods_no[]'].checked = false;
				}
			}
		}
	}

	function js_excel() {

		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.target = "";
		frm.action = "<?=str_replace("list","excel_list",$_SERVER[PHP_SELF])?>";
		frm.submit();

	}

	function js_confirm() {

		var frm = document.frm;
		
		frm.mode.value = "U";
		frm.confirm_tf.value = "Y";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	function js_cancel_confirm() {

		var frm = document.frm;
		
		frm.mode.value = "U";
		frm.confirm_tf.value = "N";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	function js_cp_type() {
		
		var frm = document.frm;
		
		frm.target = "ifr_hidden";
		frm.action = "confirm_set_date.php";
		frm.submit();
	}

	function js_setDate (ad_type, sDate, eDate) {

		var frm = document.frm;

		document.getElementById('ad_type_display').innerHTML = ad_type;
		
		if (ad_type != "") {
			frm.start_date.value = sDate;
			frm.end_date.value = eDate;
		}
	}

	function js_detail(confirm_ymd,buy_cp_no,cp_nm,account_bank,account,ad_type,cp_phone) {
		var frm = document.frm;

		frm.p_confirm_ymd.value		= confirm_ymd;
		frm.p_buy_cp_no.value			= buy_cp_no;
		frm.p_cp_nm.value					= cp_nm;
		frm.p_account_bank.value	= account_bank;
		frm.p_account.value				= account;
		frm.p_ad_type.value				= ad_type;
		frm.p_cp_phone.value			= cp_phone;
		
		NewWindow('about:blank', 'order_detail_list','1018','600','YES');
		
		frm.target = "order_detail_list";
		frm.action = "confirm_order_detail_list.php";
		frm.submit();

	}

</script>
</head>

<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="rn" value="">
<input type="hidden" name="order_goods_no" value="">
<input type="hidden" name="reserve_no" value="">
<input type="hidden" name="confirm_tf" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>">

<input type="hidden" name="p_confirm_ymd" value="">
<input type="hidden" name="p_buy_cp_no" value="">
<input type="hidden" name="p_cp_nm" value="">
<input type="hidden" name="p_account_bank" value="">
<input type="hidden" name="p_account" value="">
<input type="hidden" name="p_ad_type" value="">
<input type="hidden" name="p_cp_phone" value="">

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

				<h2>공급 업체 정산 리스트</h2>
				<div class="btnright"><!--<a href="javascript:js_write();"><img src="../images/admin/btn_regist_02.gif" alt="등록" /></a>-->&nbsp;</div>
				<div class="category_choice"><!--<select style="width: 100px;"><option>제품선택</option></select>-->&nbsp;</div>

				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="120" />
					<col width="*" />
					<col width="120" />
					<col width="*" />
					<col width="120" />
				</colgroup>

				<? if ($s_adm_cp_type == "운영") { ?>
				<thead>
					<tr>
						<th>결재구분</th>
						<td>
							<?= makeSelectBox($conn,"AD_TYPE","ad_type","125","결재구분선택","",$ad_type)?> &nbsp;
							<?= makeSelectBox($conn,"TAX_TF","con_tax_tf","125","과세구분선택","",$con_tax_tf)?>
						</td>
						<th>공급업체</th>
						<td colspan="2">
							<input type="text" class="supplyer" style="width:90%" placeholder="업체(명/코드) 입력해주세요"  name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'구매',$cp_type)?>" />
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
							<input type="hidden" name="cp_type" value="<?=$cp_type?>">&nbsp;&nbsp;&nbsp;<span id="ad_type_display"></span>
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>정산일</th>
						<td>
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="start_date" value="<?=$start_date?>" maxlength="10"/>
							 ~ 
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="end_date" value="<?=$end_date?>" maxlength="10"/>
						<!--
							<input type="text" class="txt" style="width: 75px;" name="start_date" value="<?=$start_date?>" maxlength="10" readonly="1" />
							<a href="javascript:show_calendar('document.frm.start_date', document.frm.start_date.value);" onFocus="blur();">
							<img src="/manager/images/bu/ic_calendar.gif" alt="" /></a>  ~ 

							<input type="text" class="txt" style="width: 75px;" name="end_date" value="<?=$end_date?>" maxlength="10" readonly="1" />
							<a href="javascript:show_calendar('document.frm.end_date', document.frm.end_date.value);" onFocus="blur();">
							<img src="/manager/images/bu/ic_calendar.gif" alt="" /></a>
						-->
						</td>
						<th>정렬</th>
						<td>
							<select name="order_field" style="width:84px;">
								<option value="AA.CONFIRM_YMD" <? if ($order_field == "AA.CONFIRM_YMD") echo "selected"; ?> >정산일</option>
								<option value="BB.CP_NM" <? if ($order_field == "BB.CP_NM") echo "selected"; ?> >업체명</option>
								<option value="ALL_BUY_PRICE" <? if ($order_field == "ALL_BUY_PRICE") echo "selected"; ?> >총공급가</option>
								<option value="ALL_SALE_PRICE" <? if ($order_field == "ALL_SALE_PRICE") echo "selected"; ?> >총판매가</option>
								<option value="ALL_EXTRA_PRICE" <? if ($order_field == "ALL_EXTRA_PRICE") echo "selected"; ?> >배송비</option>
								<option value="ALL_DELIVERY_PRICE" <? if ($order_field == "ALL_DELIVERY_PRICE") echo "selected"; ?> >추가배송비</option>
								<option value="ALL_SA_DELIVERY_PRICE" <? if ($order_field == "ALL_SA_DELIVERY_PRICE") echo "selected"; ?> >3자 물류비</option>
								<option value="PLUS_PRICE" <? if ($order_field == "PLUS_PRICE") echo "selected"; ?> >총판매이익</option>
								<option value="LEE" <? if ($order_field == "LEE") echo "selected"; ?> >총판매이익율</option>
							</select>&nbsp;&nbsp;
							<input type='radio' class="" name='order_str' value='DESC' <? if (($order_str == "DESC") || ($order_str == "")) echo " checked"; ?> > 오름차순 &nbsp;
							<input type='radio' name='order_str' value='ASC' <? if ($order_str == "ASC") echo " checked"; ?>> 내림차순
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						</td>
						<td align="right">
							<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>
						</td>
					</tr>
				</tbody>
				<? } ?>

				<? if ($s_adm_cp_type == "구매" || $s_adm_cp_type == "판매공급") { ?>
				<input type="hidden" name="ad_type" value="">
				<input type="hidden" name="cp_type" value="">
				<thead>
					<tr>
						<th>정산일 :</th>
						<td>
							<input type="text" class="txt" style="width: 75px;" name="start_date" value="<?=$start_date?>" maxlength="10" readonly="1" />
							<a href="javascript:show_calendar('document.frm.start_date', document.frm.start_date.value);" onFocus="blur();"><!--
						--><img src="/manager/images/bu/ic_calendar.gif" alt="" /></a>  ~ 

							<input type="text" class="txt" style="width: 75px;" name="end_date" value="<?=$end_date?>" maxlength="10" readonly="1" />
							<a href="javascript:show_calendar('document.frm.end_date', document.frm.end_date.value);" onFocus="blur();"><!--
						--><img src="/manager/images/bu/ic_calendar.gif" alt="" /></a>
							&nbsp;<?= makeSelectBox($conn,"TAX_TF","con_tax_tf","125","과세구분선택","",$con_tax_tf)?>
						</td>
						<th>정렬 :</th>
						<td>
							<select name="order_field" style="width:84px;">
								<option value="AA.CONFIRM_YMD" <? if ($order_field == "AA.CONFIRM_YMD") echo "selected"; ?> >정산일</option>
								<option value="ALL_BUY_PRICE" <? if ($order_field == "ALL_BUY_PRICE") echo "selected"; ?> >총공급가</option>
								<option value="ALL_EXTRA_PRICE" <? if ($order_field == "ALL_EXTRA_PRICE") echo "selected"; ?> >배송비</option>
								<option value="ALL_DELIVERY_PRICE" <? if ($order_field == "ALL_DELIVERY_PRICE") echo "selected"; ?> >추가배송비</option>
								<option value="ALL_SA_DELIVERY_PRICE" <? if ($order_field == "ALL_SA_DELIVERY_PRICE") echo "selected"; ?> >3자 물류비</option>
							</select>&nbsp;&nbsp;
							<input type='radio' class="" name='order_str' value='DESC' <? if (($order_str == "DESC") || ($order_str == "")) echo " checked"; ?> > 오름차순 &nbsp;
							<input type='radio' name='order_str' value='ASC' <? if ($order_str == "ASC") echo " checked"; ?>> 내림차순
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						</td>
						<td align="right">
							<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>
						</td>

				</thead>
				<? } ?>

			</table>
			<div class="sp20"></div>
			총 <?=sizeof($arr_rs)?> 건
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<font color="orange"><b>정산일을 클릭하시면 상세 내역을 조회 하실 수 있습니다.</b></font>
			<table cellpadding="0" cellspacing="0" class="rowstable" border="0">

				<? if ($s_adm_cp_type == "운영") { ?>
				<colgroup>
					<col width="7%" />
					<col width="8%" />
					<col width="10%" />
					<col width="11%"/>
					<col width="10%" />
					<col width="9%" />
					<col width="7%" />
					<col width="7%" />
					<col width="7%" />
					<col width="7%" />
					<col width="7%" />
					<col width="10%" />
				</colgroup>
				<thead>
					<tr>
						<th>정산일</th>
						<th>결재구분</th>
						<th>공급업체</th>
						<th>결재은행</th>
						<th>계좌번호</th>
						<th>연락처</th>
						<th>정산금액</th>
						<th>총공급가</th>
						<th>총판매가</th>
						<th>총배송비</th>
						<th>총추가배송비</th>
						<!--<th>3자물류</th>-->
						<th class="end">총판매이익</th>
					</tr>
					<? } ?>

				<? if ($s_adm_cp_type == "구매" || $s_adm_cp_type == "판매공급") { ?>
				<colgroup>
					<col width="8%" />
					<col width="12%" />
					<col width="10%"/>
					<col width="12%" />
					<col width="12%" />
					<col width="12%" />
					<col width="11%" />
					<col width="11%" />
					<col width="12%" />
				</colgroup>
				<thead>
					<tr>
						<th>정산일</th>
						<th>결재구분</th>
						<th>결재은행</th>
						<th>계좌번호</th>
						<th>연락처</th>
						<th>정산금액</th>
						<th>총공급가</th>
						<th>총배송비</th>
						<th class="end">총추가배송비</th>
						<!--<th class="end">3자물류</th>-->
					</tr>
					<? } ?>
				</thead>
				<tbody>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							/*
							AA.CONFIRM_YMD, AA.BUY_CP_NO, BB.CP_NM, BB.ACCOUNT_BANK, BB.ACCOUNT, BB.AD_TYPE, BB.CP_PHONE,
							SUM(AA.BUY_PRICE * AA.QTY) ALL_BUY_PRICE, 
							SUM(AA.SALE_PRICE * AA.QTY) ALL_SALE_PRICE,
							SUM(AA.EXTRA_PRICE * AA.QTY) ALL_EXTRA_PRICE,
							SUM(AA.SA_DELIVERY_PRICE * AA.QTY) ALL_SA_DELIVERY_PRICE,
							(SUM(AA.SALE_PRICE * AA.QTY) - SUM(AA.BUY_PRICE * AA.QTY)) AS PLUS_PRICE,
							ROUND(((SUM(AA.SALE_PRICE * AA.QTY) - SUM(AA.BUY_PRICE * AA.QTY)) / SUM(AA.SALE_PRICE * AA.QTY) * 100),2) AS LEE
							*/

							$CONFIRM_YMD						= trim($arr_rs[$j]["CONFIRM_YMD"]);
							$BUY_CP_NO							= trim($arr_rs[$j]["BUY_CP_NO"]);
							$CP_NM									= trim($arr_rs[$j]["CP_NM"]);
							$ACCOUNT_BANK						= trim($arr_rs[$j]["ACCOUNT_BANK"]);
							$ACCOUNT								= trim($arr_rs[$j]["ACCOUNT"]);
							
							$AD_TYPE								= trim($arr_rs[$j]["AD_TYPE"]);
							$CP_PHONE								= trim($arr_rs[$j]["CP_PHONE"]);
							$ALL_BUY_PRICE					= trim($arr_rs[$j]["ALL_BUY_PRICE"]);
							$ALL_SALE_PRICE					= trim($arr_rs[$j]["ALL_SALE_PRICE"]);
							$ALL_EXTRA_PRICE				= trim($arr_rs[$j]["ALL_EXTRA_PRICE"]);
							$ALL_DELIVERY_PRICE			= trim($arr_rs[$j]["ALL_DELIVERY_PRICE"]);
							$ALL_SA_DELIVERY_PRICE	= trim($arr_rs[$j]["ALL_SA_DELIVERY_PRICE"]);
							$ALL_PAY_PRICE					= trim($arr_rs[$j]["ALL_PAY_PRICE"]);
							
							$PLUS_PRICE						= trim($arr_rs[$j]["PLUS_PRICE"]);
							$LEE									= trim($arr_rs[$j]["LEE"]);
							

							$str_price_class = "price";
							$str_state_class = "state";
							
							if ($s_adm_cp_type == "운영") {

						?>
						<!--
						<th>정산일</th>
						<th>결재구분</th>
						<th>공급업체</th>
						<th>결재은행</th>
						<th>계좌번호</th>
						<th>연락처</th>
						<th>정산금액</th>
						<th>총공급가</th>
						<th>총판매가</th>
						<th>총배송비</th>
						<th>총추가배송비</th>
						-->
						<tr>
							<td><a href="javascript:js_detail('<?=$CONFIRM_YMD?>','<?=$BUY_CP_NO?>','<?=$CP_NM?>','<?=$ACCOUNT_BANK?>','<?=$ACCOUNT?>','<?=$AD_TYPE?>','<?=$CP_PHONE?>');"><?=$CONFIRM_YMD?></a></td>
							<td class="modeual_nm"><?=$AD_TYPE?></td> <!--결재구분-->
							<td class="modeual_nm"><a href="javascript:js_detail('<?=$CONFIRM_YMD?>','<?=$BUY_CP_NO?>','<?=$CP_NM?>','<?=$ACCOUNT_BANK?>','<?=$ACCOUNT?>','<?=$AD_TYPE?>','<?=$CP_PHONE?>');"><?=$CP_NM?></a></td>
							<td class="filedown"><?=$ACCOUNT_BANK?></td>	<!--결재은행-->
							<td class="modeual_nm"><?=$ACCOUNT?></td>			<!--계좌번호-->
							<td class="modeual_nm"><?=$CP_PHONE?></td>		<!--연락처-->
							<td class="<?=$str_price_class?>"><?=number_format($ALL_PAY_PRICE)?></td>
							<td class="<?=$str_price_class?>"><?=number_format($ALL_BUY_PRICE)?></td>
							<td class="<?=$str_price_class?>"><?=number_format($ALL_SALE_PRICE)?></td>
							<td class="<?=$str_price_class?>"><?=number_format($ALL_EXTRA_PRICE)?></td>
							<td class="<?=$str_price_class?>"><?=number_format($ALL_DELIVERY_PRICE)?></td>
							<!--<td class="<?=$str_price_class?>"><?=number_format($ALL_SA_DELIVERY_PRICE)?></td>-->
							<td class="<?=$str_price_class?>"><?=number_format($PLUS_PRICE)?> (<?=$LEE?>%)</td>
						</tr>
						<?
							}

							if ($s_adm_cp_type == "구매" || $s_adm_cp_type == "판매공급") {
						?>
						<tr>
							<td><a href="javascript:js_detail('<?=$CONFIRM_YMD?>','<?=$BUY_CP_NO?>','<?=$CP_NM?>','<?=$ACCOUNT_BANK?>','<?=$ACCOUNT?>','<?=$AD_TYPE?>','<?=$CP_PHONE?>');"><?=$CONFIRM_YMD?></a></td>
							<td class="modeual_nm"><?=$AD_TYPE?></td>
							<td class="filedown"><?=$ACCOUNT_BANK?></td>
							<td><?=$ACCOUNT?></td>
							<td><?=$CP_PHONE?></td>
							<td class="<?=$str_price_class?>"><?=number_format($ALL_PAY_PRICE)?></td>
							<td class="<?=$str_price_class?>"><?=number_format($ALL_BUY_PRICE)?></td>
							<td class="<?=$str_price_class?>"><?=number_format($ALL_EXTRA_PRICE)?></td>
							<td class="<?=$str_price_class?>"><?=number_format($ALL_DELIVERY_PRICE)?></td>
							<!--<td class="<?=$str_price_class?>"><?=number_format($ALL_SA_DELIVERY_PRICE)?></td>-->
						</tr>
						<?
							}
						}
					}else{
						?>
						<tr>
							<td height="50" align="center" colspan="16">데이터가 없습니다. </td>
						</tr>
					<?
						}
					?>
				</tbody>
			</table>
			<? if ($s_adm_cp_type == "운영") { ?>
			<div class="sp20"></div>
			총합계
			<table cellpadding="0" cellspacing="0" class="rowstable" border="0">
				<colgroup>
					<col width="16%" />
					<col width="16%" />
					<col width="16%" />
					<col width="16%" />
					<col width="16%" />
					<col width="20%" />
				</colgroup>
				<thead>
					<tr>
						<th>정산금액</th>
						<th>총공급가</th>
						<th>총판매가</th>
						<th>총배송비</th>
						<th>총추가배송비</th>
						<!--<th>3자물류</th>-->
						<th class="end">총판매이익</th>
					</tr>
				</thead>
				<tbody>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs_all) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs_all); $j++) {

							$ALL_BUY_PRICE					= trim($arr_rs_all[$j]["ALL_BUY_PRICE"]);
							$ALL_SALE_PRICE					= trim($arr_rs_all[$j]["ALL_SALE_PRICE"]);
							$ALL_EXTRA_PRICE				= trim($arr_rs_all[$j]["ALL_EXTRA_PRICE"]);
							$ALL_DELIVERY_PRICE			= trim($arr_rs_all[$j]["ALL_DELIVERY_PRICE"]);
							$ALL_SA_DELIVERY_PRICE	= trim($arr_rs_all[$j]["ALL_SA_DELIVERY_PRICE"]);
							
							$ALL_PAY_PRICE					= trim($arr_rs_all[$j]["ALL_PAY_PRICE"]);
							
							$PLUS_PRICE							= trim($arr_rs_all[$j]["PLUS_PRICE"]);
							$LEE										= trim($arr_rs_all[$j]["LEE"]);
							

							$str_price_class = "price";
							$str_state_class = "state";

						?>
						<tr>
							<td class="<?=$str_price_class?>"><?=number_format($ALL_PAY_PRICE)?></td>
							<td class="<?=$str_price_class?>"><?=number_format($ALL_BUY_PRICE)?></td>
							<td class="<?=$str_price_class?>"><?=number_format($ALL_SALE_PRICE)?></td>
							<td class="<?=$str_price_class?>"><?=number_format($ALL_EXTRA_PRICE)?></td>
							<td class="<?=$str_price_class?>"><?=number_format($ALL_DELIVERY_PRICE)?></td>
							<!--<td class="<?=$str_price_class?>"><?=number_format($ALL_SA_DELIVERY_PRICE)?></td>-->
							<td class="<?=$str_price_class?>"><?=number_format($PLUS_PRICE)?> (<?=$LEE?>%)</td>
						</tr>
						<?
						}
					}else{
						?>
						<tr>
							<td height="50" align="center" colspan="16">데이터가 없습니다. </td>
						</tr>
					<?
						}
					?>
				</tbody>
			</table>
					<?
						}
					?>

			</div>
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
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>