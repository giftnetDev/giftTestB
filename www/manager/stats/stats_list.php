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

	
	if ($code == "day") {
		$menu_right = "ST002"; // 메뉴마다 셋팅 해 주어야 합니다
		$str_menu_title = "일별 판매 현황";
		$str_list_title = "년월일";
	}

	if ($code == "month") {
		$menu_right = "ST003"; // 메뉴마다 셋팅 해 주어야 합니다
		$str_menu_title = "월별 판매 현황";
		$str_list_title = "년월";
	}

	if ($code == "goods") {
		$menu_right = "ST004"; // 메뉴마다 셋팅 해 주어야 합니다
		$str_menu_title = "상품별 판매 현황";
		$str_list_title = "상품명";
	}

	if ($order_field == "")
		$order_field = "TITLE";

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
	require "../../_classes/biz/stats/stats.php";


#====================================================================
# Request Parameter
#====================================================================

	if($s_adm_md_tf == "Y")
		$sel_opt_manager_no = $s_adm_no;

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
	
	$del_tf = "N";
#============================================================
# Page process
#============================================================
	
#	echo $start_date;
#	echo $end_date;

#===============================================================
# Get Search list count
#===============================================================

	$arr_rs = listStatsOrder($conn, $code, $sel_date_type, $start_date, $end_date, $cp_type, $cp_type2, $sel_opt_manager_no, $search_field, $search_str, $order_field, $order_str);
	$arr_rs_all = listStatsAllOrder($conn, $code, $sel_date_type, $start_date, $end_date, $cp_type, $cp_type2, $sel_opt_manager_no, $search_field, $search_str);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
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

	// 조회 버튼 클릭 시 
	function js_search() {
		var frm = document.frm;
		
		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_excel() {

		var frm = document.frm;
		
		frm.target = "";
		frm.action = "<?=str_replace("list","excel_list",$_SERVER[PHP_SELF])?>";
		frm.submit();

	}

	function js_reload() {
		location.reload();
	}

</script>
</head>

<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="code" value="<?=$code?>">
<input type="hidden" name="old_sel_opt_manager_no" value="<?=$sel_opt_manager_no?>">
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

				<h2><?=$str_menu_title?></h2>
				<div class="btnright">&nbsp;</div>
				<div class="category_choice"><!--<select style="width: 100px;"><option>제품선택</option></select>-->&nbsp;</div>

				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="120" />
					<col width="*" />
					<col width="120" />
					<col width="*" />
					<col width="120" />
				</colgroup>
				<thead>
					<tr>
						<th>조회기간</th>
						<td>
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="start_date" value="<?=$start_date?>" maxlength="10"/>
							 ~ 
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="end_date" value="<?=$end_date?>" maxlength="10"/>

							<!--
							<select name="sel_date_type" class="txt">
								<option value="PAY_DATE" <?if ($sel_date_type == "PAY_DATE") echo "selected" ?>>입금일</option>
								<option value="DELIVERY_DATE" <?if ($sel_date_type == "DELIVERY_DATE") echo "selected" ?>>배송완료일</option>
								<option value="FINISH_DATE" <?if ($sel_date_type == "FINISH_DATE") echo "selected" ?>>주문(취소)완료일</option>
							</select>
							
							<input type="text" class="txt" style="width: 75px;" name="start_date" value="<?=$start_date?>" maxlength="10" readonly="1" />
							<a href="javascript:show_calendar('document.frm.start_date', document.frm.start_date.value);" onFocus="blur();">
							<img src="/manager/images/bu/ic_calendar.gif" alt="" /></a>  ~ 

							<input type="text" class="txt" style="width: 75px;" name="end_date" value="<?=$end_date?>" maxlength="10" readonly="1" />
							<a href="javascript:show_calendar('document.frm.end_date', document.frm.end_date.value);" onFocus="blur();">
							<img src="/manager/images/bu/ic_calendar.gif" alt="" /></a>
							-->
						</td>
						<th>영업담당자</th>
						<td colspan="2">
							<? if ($s_adm_md_tf != "Y") { ?>
								<?= makeAdminInfoByMDSelectBox($conn,"sel_opt_manager_no"," style='width:70px;' ","전체","", $sel_opt_manager_no) ?>
							<? } else { ?>
								<input type="hidden" name="sel_opt_manager_no" value="<?=$sel_opt_manager_no?>"/>
								<?=getAdminName($conn,$sel_opt_manager_no)?>
							<? } ?>	
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
												$.getJSON( "../company/json_company_list.php?term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
													if(data.length == 1) { 
														
														js_selecting_company("txt_cp_type", data[0].label, "cp_type", data[0].id);

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

								});

							</script>
							<!--
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
							<input type="hidden" name="cp_type" value="<?=$cp_type?>">
							-->
						</td>
						<th>판매업체</th>
						<td colspan="2">
							<input type="text" class="autocomplete_off" style="width:90%" placeholder="업체(명/코드) 입력후 엔터를 눌러주세요" name="txt_cp_type2" value="<?=getCompanyAutocompleteTextBox($conn,'',$cp_type2)?>" />
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
												$.getJSON( "../company/json_company_list.php?term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
													if(data.length == 1) { 
														
														js_selecting_company("txt_cp_type2", data[0].label, "cp_type2", data[0].id);

													} else if(data.length > 1){ 
														NewWindow("../company/pop_company_searched_list.php?search_str="+keyword + "&target_name=txt_cp_type2&target_value=cp_type2",'pop_company_searched_list','950','650','YES');

													} else 
														alert("검색결과가 없습니다.");
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
							<input type="text" class="seller" style="width:90%" placeholder="업체(명/코드) 입력해주세요" name="txt_cp_type2" value="<?=getCompanyAutocompleteTextBox($conn,'판매',$cp_type2)?>" />
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
					</tr>
					<tr>
						<th>정렬</th>
						<td>
							<select name="order_field" style="width:144px;">
								<option value="TITLE" <? if ($order_field == "TITLE") echo "selected"; ?> >항목</option>
								<option value="TOT_ORDER_SALE_QTY" <? if ($order_field == "TOT_ORDER_SALE_QTY") echo "selected"; ?> >주문완료수량</option>
								<option value="TOT_ORDER_SALE_PRICE" <? if ($order_field == "TOT_ORDER_SALE_PRICE") echo "selected"; ?> >주문완료합계</option>
								<option value="TOT_DELIVERY_SALE_QTY" <? if ($order_field == "TOT_DELIVERY_SALE_QTY") echo "selected"; ?> >배송완료수량</option>
								<option value="TOT_DELIVERY_SALE_PRICE" <? if ($order_field == "TOT_DELIVERY_SALE_PRICE") echo "selected"; ?> >배송완료합계</option>
								<option value="TOT_CANCEL_SALE_QTY" <? if ($order_field == "TOT_CANCEL_SALE_QTY") echo "selected"; ?> >주문취소수량</option>
								<option value="TOT_CANCEL_SALE_PRICE" <? if ($order_field == "TOT_CANCEL_SALE_PRICE") echo "selected"; ?> >주문취소합계</option>
								<option value="TOT_SUN_SALE_QTY" <? if ($order_field == "TOT_SUN_SALE_QTY") echo "selected"; ?> >순판매수량</option>
								<option value="TOT_SUN_SALE_PRICE" <? if ($order_field == "TOT_SUN_SALE_PRICE") echo "selected"; ?> >순판매합계</option>
								<option value="PLUS_PRICE" <? if ($order_field == "PLUS_PRICE") echo "selected"; ?> >판매이익</option>
								<option value="LEE" <? if ($order_field == "LEE") echo "selected"; ?> >판매이익율</option>
							</select>&nbsp;&nbsp;
							<input type='radio' class="" name='order_str' value='DESC' <? if (($order_str == "DESC") || ($order_str == "")) echo " checked"; ?> > 오름차순 &nbsp;
							<input type='radio' name='order_str' value='ASC' <? if ($order_str == "ASC") echo " checked"; ?>> 내림차순
						</td>
						<th>검색조건</th>
						<td>
							<select name="search_field" style="width:84px;">
								<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >통합검색</option>
								<option value="A.GOODS_NAME" <? if ($search_field == "A.GOODS_NAME") echo "selected"; ?> >상품명</option>
							</select>&nbsp;
							<input type="text" value="<?=$search_str?>" name="search_str" size="15"class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();"/>
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						</td>
						<td align="right">
							<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="sp20"></div>
			<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table" border="0">
				<colgroup>

					<? if ($code == "goods") { ?>
					<col width="28%" />
					<col width="5%" />
					<col width="7%"/>
					<col width="5%" />
					<col width="7%"/>
					<col width="5%" />
					<col width="7%"/>
					<col width="5%" />
					<col width="7%"/>
					<col width="7%" />
					<col width="7%"/>
					<? } else { ?>
					<col width="10%" />
					<col width="9%" />
					<col width="9%"/>
					<col width="9%" />
					<col width="9%"/>
					<col width="9%" />
					<col width="9%"/>
					<col width="9%" />
					<col width="9%"/>
					<col width="9%" />
					<col width="9%"/>
					<? } ?>


				</colgroup>
				<thead>
					<tr>
						<th rowspan="2">항목</th>
						<th colspan="2">주문완료</th>
						<th colspan="2">배송완료</th>
						<th colspan="2">주문취소</th>
						<th colspan="2">순판매</th>
						<th rowspan="2">판매이익</th>
						<th rowspan="2" class="end">이익율</th>
					</tr>
					<tr>
						<th>수량</th>
						<th>합계</th>
						<th>수량</th>
						<th>합계</th>
						<th>수량</th>
						<th>합계</th>
						<th>수량</th>
						<th>합계</th>
					</tr>
				</thead>
				<tbody>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							/*
							G_DATE,
							sum(TOT_ORDER_SALE_PRICE) AS TOT_ORDER_SALE_PRICE, 
							sum(TOT_ORDER_SALE_QTY) AS TOT_ORDER_SALE_QTY,
							sum(TOT_DELIVERY_SALE_PRICE) AS TOT_DELIVERY_SALE_PRICE, 
							sum(TOT_DELIVERY_SALE_QTY) AS TOT_DELIVERY_SALE_QTY,
							sum(TOT_CANCEL_SALE_PRICE) AS TOT_CANCEL_SALE_PRICE, 
							sum(TOT_CANCEL_SALE_QTY) AS TOT_CANCEL_SALE_QTY,
							sum(TOT_ORDER_SALE_PRICE) - sum(TOT_CANCEL_SALE_PRICE) AS TOT_SUN_SALE_PRICE, 
							sum(TOT_ORDER_SALE_QTY) - sum(TOT_CANCEL_SALE_QTY) AS TOT_SUN_SALE_QTY,
							(sum(TOT_ORDER_SALE_PRICE) - sum(TOT_CANCEL_SALE_PRICE)) -
							(sum(TOT_ORDER_BUY_PRICE) - sum(TOT_CANCEL_BUY_PRICE)) AS PLUS_PRICE,
							ROUND(((sum(TOT_ORDER_SALE_PRICE) - sum(TOT_ORDER_BUY_PRICE)) / 
							sum(TOT_ORDER_SALE_PRICE) * 100),2) AS LEE
							*/

							$G_DATE										= trim($arr_rs[$j]["G_DATE"]);
							$TOT_ORDER_SALE_PRICE			= trim($arr_rs[$j]["TOT_ORDER_SALE_PRICE"]);
							$TOT_ORDER_SALE_QTY				= trim($arr_rs[$j]["TOT_ORDER_SALE_QTY"]);
							$TOT_DELIVERY_SALE_PRICE	= trim($arr_rs[$j]["TOT_DELIVERY_SALE_PRICE"]);
							$TOT_DELIVERY_SALE_QTY		= trim($arr_rs[$j]["TOT_DELIVERY_SALE_QTY"]);
							$TOT_CANCEL_SALE_PRICE		= trim($arr_rs[$j]["TOT_CANCEL_SALE_PRICE"]);
							$TOT_CANCEL_SALE_QTY			= trim($arr_rs[$j]["TOT_CANCEL_SALE_QTY"]);
							$TOT_SUN_SALE_PRICE				= trim($arr_rs[$j]["TOT_SUN_SALE_PRICE"]);
							$TOT_SUN_SALE_QTY					= trim($arr_rs[$j]["TOT_SUN_SALE_QTY"]);
							$PLUS_PRICE								= trim($arr_rs[$j]["PLUS_PRICE"]);
							$LEE											= trim($arr_rs[$j]["LEE"]);
							

						?>
						<tr height="37">
							<td class="modeual_nm"><?=$G_DATE?></td>
							<td class="price"><?=number_format($TOT_ORDER_SALE_QTY)?></td>
							<td class="price"><?=number_format($TOT_ORDER_SALE_PRICE)?></td>
							<td class="price"><?=number_format($TOT_DELIVERY_SALE_QTY)?></td>
							<td class="price"><?=number_format($TOT_DELIVERY_SALE_PRICE)?></td>
							<td class="price"><?=number_format($TOT_CANCEL_SALE_QTY)?></td>
							<td class="price"><?=number_format($TOT_CANCEL_SALE_PRICE)?></td>
							<td class="price"><?=number_format($TOT_SUN_SALE_QTY)?></td>
							<td class="price"><?=number_format($TOT_SUN_SALE_PRICE)?></td>
							<td class="price"><?=number_format($PLUS_PRICE)?></td>
							<td class="price"><?=$LEE?> %</td>
						</tr>
						<?
								}
							}

							if (sizeof($arr_rs_all) > 0) {
								for ($j = 0 ; $j < sizeof($arr_rs_all); $j++) {
									$TOT_ORDER_SALE_QTY				= trim($arr_rs_all[$j]["TOT_ORDER_SALE_QTY"]);
									$TOT_ORDER_SALE_PRICE			= trim($arr_rs_all[$j]["TOT_ORDER_SALE_PRICE"]);
									$TOT_DELIVERY_SALE_QTY		= trim($arr_rs_all[$j]["TOT_DELIVERY_SALE_QTY"]);
									$TOT_DELIVERY_SALE_PRICE	= trim($arr_rs_all[$j]["TOT_DELIVERY_SALE_PRICE"]);
									$TOT_CANCEL_SALE_QTY			= trim($arr_rs_all[$j]["TOT_CANCEL_SALE_QTY"]);
									$TOT_CANCEL_SALE_PRICE		= trim($arr_rs_all[$j]["TOT_CANCEL_SALE_PRICE"]);
									$TOT_SUN_SALE_QTY					= trim($arr_rs_all[$j]["TOT_SUN_SALE_QTY"]);
									$TOT_SUN_SALE_PRICE				= trim($arr_rs_all[$j]["TOT_SUN_SALE_PRICE"]);
									$PLUS_PRICE								= trim($arr_rs_all[$j]["PLUS_PRICE"]);
									$LEE											= trim($arr_rs_all[$j]["LEE"]);
								}
							}
					?>
						<tr class="goods_end">
							<td colspan="11">
								&nbsp;
							</td>
						</tr>
						<tr height="37">
							<td>합계</td>
							<td class="price"><?=number_format($TOT_ORDER_SALE_QTY)?></td>
							<td class="price"><?=number_format($TOT_ORDER_SALE_PRICE)?></td>
							<td class="price"><?=number_format($TOT_DELIVERY_SALE_QTY)?></td>
							<td class="price"><?=number_format($TOT_DELIVERY_SALE_PRICE)?></td>
							<td class="price"><?=number_format($TOT_CANCEL_SALE_QTY)?></td>
							<td class="price"><?=number_format($TOT_CANCEL_SALE_PRICE)?></td>
							<td class="price"><?=number_format($TOT_SUN_SALE_QTY)?></td>
							<td class="price"><?=number_format($TOT_SUN_SALE_PRICE)?></td>
							<td class="price"><?=number_format($PLUS_PRICE)?></td>
							<td class="price"><?=$LEE?> %</td>
						</tr>
				</tbody>
			</table>

				<div class="sp50"></div>
		<!-- // E: mwidthwrap -->
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