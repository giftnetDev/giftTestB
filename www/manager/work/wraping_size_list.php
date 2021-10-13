<?session_start();?>
<?
header("Pragma;no-cache");
header("Cache-Control;no-cache,must-revalidate");


#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "WO011"; // 메뉴마다 셋팅 해 주어야 합니다

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


#====================================================================
# Request Parameter
#====================================================================


	if ($start_date == "") {
		//$start_date = date("Y-m-d",strtotime("-12 month"));
		$start_date = "2010-07-24";
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

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

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

	if($chk_vendor != "Y" && $vendor_calc == "100")
		$vendor_calc = 100;

	$arr_options = array("exclude_category" => $exclude_category, "vendor_calc" => $vendor_calc);

	$nListCnt =totalCntGoods($conn, $con_cate, $start_date, $end_date, $start_price, $end_price, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $con_tax_tf, $con_use_tf, $del_tf, $search_field, $search_str, $arr_options, $order_field, $order_str);

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	#$del_tf = "Y";

	$arr_rs = listGoods($conn, $con_cate, $start_date, $end_date, $start_price, $end_price, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $con_tax_tf, $con_use_tf, $del_tf, $search_field, $search_str, $arr_options, $order_field, $order_str, $nPage, $nPageSize);

	$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str;
	$strParam = $strParam."&start_date=".$start_date."&end_date=".$end_date."&start_price=".$start_price."&end_price=".$end_price."&con_cate=".$con_cate;
	$strParam = $strParam."&con_cate_01=".$con_cate_01."&con_cate_02=".$con_cate_02."&con_cate_03=".$con_cate_03."&con_cate_04=".$con_cate_04;

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
<style>
	.wrong {background-color:#ffcdcf;} 
</style>
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
	$(function(){
		$('table.fixed_header_table').floatThead({
			position: 'fixed'
		});
	});
  </script>
  <script type="text/javascript" >

	function js_write() {
		document.location.href = "goods_write.php";
	}

	function js_view(rn, goods_no) {

		var frm = document.frm;
		
		frm.goods_no.value = goods_no;
		frm.mode.value = "S";
		frm.target = "";
		frm.method = "get";
		frm.action = "goods_write.php";
		frm.submit();
		
	}
	
	// 조회 버튼 클릭 시 
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";

		frm.con_cate.value = "";

		if (frm.gd_cate_01 != null) {
			if (frm.gd_cate_01.value != "") {
				frm.con_cate.value = frm.gd_cate_01.value;
			}
		}

		if (frm.gd_cate_02 != null) {
			if (frm.gd_cate_02.value != "") {
				frm.con_cate.value = frm.gd_cate_02.value;
			}
		}

		if (frm.gd_cate_03 != null) {
			if (frm.gd_cate_03.value != "") {
				frm.con_cate.value = frm.gd_cate_03.value;
			}
		}

		if (frm.gd_cate_04 != null) {
			if (frm.gd_cate_04.value != "") {
				frm.con_cate.value = frm.gd_cate_04.value;
			}
		}

//		if (frm.cp_type.value != "") 
//			frm.cp_type.value = frm.con_cate_03.value;
//		else
//			frm.con_cate_03.value = "";
		

		frm.target = "";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_toggle(goods_no, use_tf) {
		var frm = document.frm;

		bDelOK = confirm('사용 여부를 변경 하시겠습니까?');
		
		if (bDelOK==true) {

			if (use_tf == "Y") {
				use_tf = "N";
			} else {
				use_tf = "Y";
			}

			frm.goods_no.value = goods_no;
			frm.use_tf.value = use_tf;
			frm.mode.value = "T";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
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

		bDelOK = confirm('선택한 상품을 삭제 하시겠습니까?\n체크박스에 선택을 하셨어도 상품 판매 내역이 있을 경우 삭제 되지 않을 수 있습니다.');
		
		if (bDelOK==true) {
			
			frm.mode.value = "D";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_state_mod() {
		var frm = document.frm;

		bDelOK = confirm('선택한 상품 판매 상태를 변경 하시겠습니까?\n');
		
		if (bDelOK==true) {
			
			frm.mode.value = "SU";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_copy() {
		var frm = document.frm;

		bDelOK = confirm('선택한 상품을 복사 하시겠습니까?');
		
		if (bDelOK==true) {
			
			frm.mode.value = "C";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
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
		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_layer_show(goods_no, img_url) {
		
		var origin_img = img_url.replace("simg/s_50_50_","");
		$("#l_"+goods_no).html("<img src='"+origin_img+"'>").show();

	}

	function js_layer_hide(goods_no) {
		$("#l_"+goods_no).hide();
	}


	function js_batch_modify() {
		var url = "/manager/goods/goods_batch_modify.php";
		var frm = document.frm;
		NewWindow('about:blank', 'modify_batch_popup', '860', '513', 'YES');
		frm.target = "modify_batch_popup";
		frm.action = url;
		frm.submit();
	}

	/*
	function js_sub_goods_modify() {

		var url = "goods_sub_goods_modify.php";
		NewWindow('about:blank', 'sub_goods_modify_popup', '860', '513', 'YES');
		var frm = document.frm;
		frm.target = "sub_goods_modify_popup";
		frm.action = url;
		frm.submit();
	}
	*/

	function js_reload() {
		location.reload();
	}

	(function($){
		$.fn.extend({
			center: function () {
				return this.each(function() {
					var top = ($(window).height() - $(this).find("img").outerHeight()) / 2 + $(window).scrollTop();
					var left = ($(window).width() - $(this).find("img").outerWidth()) / 2;

					if($(this).find("img").outerHeight() == 0 || $(this).find("img").outerWidth() == 0)
						$(this).css({position:'absolute', margin:0, top: (100 + $(window).scrollTop()) +'px', left: 400 +'px'});
					else
						$(this).css({position:'absolute', margin:0, top: (top > 0 ? top : 0)+'px', left: (left > 0 ? left : 0)+'px'});
				});
			}
		}); 
	})(jQuery);

	$(function(){
	
		var img_frame = $("<div style='background-color: #EFEFEF; border: 1px solid #DEDEDE; padding:5px 5px 5px 5px; z-index:9999;'></div>");
		$(".goods_thumb").hover(function(){

			var origin_img = $(this).prop("src").replace("simg/s_50_50_","");
			
			img_frame.show().append($("<img src='"+origin_img+"' style='max-height:800px; max-width:600px;'/>"));

			$(this).after(img_frame);

			img_frame.center();

		}, function(){

			img_frame.empty().hide();

		});

		var win;
		$(".goods_thumb").click(function(e) {
			e.preventDefault();
			
			var origin_img = $(this).prop("src").replace("simg/s_50_50_","");
			
			win = window.open(origin_img, 'win');
			window.setTimeout('check()',1000);

		});

		function check() {
			if(win.document.readyState =='complete'){
				win.document.execCommand("SaveAs");
				win.close();
			} else { 
				window.setTimeout('check();',1000);
			}
		}

		$(window).scroll(function() {
		   img_frame.empty().hide();
		});

	});

</script>
</head>

<body id="admin">

<form name="frm" method="post" onSubmit="js_search(); return true;">
<input type="hidden" name="rn" value="">
<input type="hidden" name="depth" value="" />
<input type="hidden" name="goods_no" value="">
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

				<h2>포장지 재단</h2>

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
						<th>카테고리</th>
						<td colspan="4">
							<?= makeCategorySelectBoxOnChange($conn, $con_cate, $exclude_category);?>
							<input type="hidden" name="con_cate" value="<?=$con_cate?>">
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>등록일</th>
						<td colspan="4">
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="start_date" value="<?=$start_date?>" maxlength="10"/>
							 ~ 
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="end_date" value="<?=$end_date?>" maxlength="10"/>
							&nbsp;<a href="javascript:js_search_date('0');"><img src="/manager/images/bu/btn_s_dday.gif" alt="" /></a>
							<a href="javascript:js_search_date('1');"><img src="/manager/images/bu/btn_s_bday.gif" alt="" /></a>
							<a href="javascript:js_search_date('7');"><img src="/manager/images/bu/btn_s_7day.gif" alt="" /></a>
							<a href="javascript:js_search_date('31');"><img src="/manager/images/bu/btn_s_1mon.gif" alt="" /></a>
						</td>
					</tr>
					<tr>
						<th>판매상태</th>
						<td>
							<?= makeSelectBox($conn,"GOODS_STATE","con_cate_04","125","선택","",$con_cate_04)?>
						</td>
						<th>과세구분</th>
						<td colspan="2">
							<?= makeSelectBox($conn,"TAX_TF","con_tax_tf","125","선택","",$con_tax_tf)?>
						</td>
					</tr>

					<? if ($s_adm_cp_type == "운영") { ?>
					<tr>
						<th>판매가</th>
						<td>
							<input type="text" value="<?=$start_price?>" name="start_price" style="width: 75px;" class="txt" onkeyup="return isNumber(this)" /> 원 ~
							<input type="text" value="<?=$end_price?>" name="end_price" style="width: 75px;" class="txt" onkeyup="return isNumber(this)" /> 원
						</td>
						<th>공급업체</th>
						<td colspan="2">
							<input type="text" class="seller" style="width:90%" placeholder="업체(명/코드) 입력후 엔터를 눌러주세요" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'',$con_cate_03)?>" />
							<input type="hidden" name="con_cate_03" value="<?=$con_cate_03?>">

							<script>
								$(function(){

									$("input[name=txt_cp_type]").keydown(function(e){

										if(e.keyCode==13) { 

											var keyword = $(this).val();
											
											if(keyword == "") { 
												$("input[name=con_cate_03]").val('');
												js_search();
											} else { 
												$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('구매,판매공급') +"&term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
													if(data.length == 1) { 
														
														js_selecting_company("txt_cp_type", data[0].label, "con_cate_03", data[0].id);

													} else if(data.length > 1){ 
														NewWindow("../company/pop_company_searched_list.php?con_cp_type=구매,판매공급&search_str="+keyword + "&target_name=txt_cp_type&target_value=con_cate_03",'pop_company_searched_list','950','650','YES');

													} else 
														alert("검색결과가 없습니다.");
												});
											}
										}

									});
									
									$("input[name=txt_cp_type]").keyup(function(e){
										var keyword = $(this).val();

										if(keyword == "") { 
											$("input[name=con_cate_03]").val('');
										}
									});

								});

							</script>
							
							<!--
							<input type="text" class="supplyer" style="width:90%" placeholder="업체(명/코드) 입력" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'구매',$con_cate_03)?>" />
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
										$("input[name=con_cate_03]").val(ui.item.id);
									}
								}).bind( "blur", function( event ) {
									var cp_no = $("input[name=con_cate_03]").val();
									if(cp_no != '') {
										$.getJSON( "../company/json_company_list.php?cp_no=" + cp_no, function(data) {
											if(data[0].CP_NO == 'undefined') {
												$("input[name=con_cate_03]").val('');
											} else {
												if(data[0].COMPANY != $(".supplyer").val())
												{

													$(".supplyer").val();
													$("input[name=con_cate_03]").val('');
												}
											}
										});
									} 
								});
							});
							</script>
							<input type="hidden" name="con_cate_03" value="<?=$con_cate_03?>">
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
					<? } else { ?>
						<input type="hidden" name="con_cate_03" value="<?=$con_cate_03?>">
						<input type="hidden" name="cp_type" value="">
					<? } ?>

					<tr>
						<th>정렬</th>
						<td>
							<select name="order_field" style="width:84px;">
								<option value="REG_DATE" <? if ($order_field == "REG_DATE") echo "selected"; ?> >등록일</option>
								<option value="GOODS_NAME" <? if ($order_field == "GOODS_NAME") echo "selected"; ?> >상품명</option>
								<option value="GOODS_NO" <? if ($order_field == "GOODS_NO") echo "selected"; ?> >상품번호</option>
								<option value="GOODS_CODE" <? if ($order_field == "GOODS_CODE") echo "selected"; ?> >상품코드</option>
								<option value="BUY_PRICE" <? if ($order_field == "BUY_PRICE") echo "selected"; ?> >매입가</option>
								<option value="SALE_PRICE" <? if ($order_field == "SALE_PRICE") echo "selected"; ?> >판매가</option>
								<? if ($s_adm_cp_type == "운영") { ?>
								<option value="EXTRA_PRICE" <? if ($order_field == "EXTRA_PRICE") echo "selected"; ?> >매입합계</option>
								<option value="CP_NAME" <? if ($order_field == "CP_NAME") echo "selected"; ?> >공급업체</option>
								<? } ?>
								<option value="STOCK_CNT" <? if ($order_field == "STOCK_CNT") echo "selected"; ?> >재고</option>
								<option value="UP_DATE" <? if ($order_field == "UP_DATE") echo "selected"; ?> >수정일</option>
							</select>&nbsp;&nbsp;
							<input type='radio' name='order_str' value='ASC' <? if ($order_str == "ASC") echo " checked"; ?>> 오름차순 &nbsp;
							<input type='radio' class="" name='order_str' value='DESC' <? if (($order_str == "DESC") || ($order_str == "")) echo " checked"; ?> > 내림차순 
						</td>
						<th>검색조건</th>
						<td>
							<select name="nPageSize" style="width:84px;">
								<option value="20" <? if ($nPageSize == "20") echo "selected"; ?> >20개씩</option>
								<option value="50" <? if ($nPageSize == "50") echo "selected"; ?> >50개씩</option>
								<option value="100" <? if ($nPageSize == "100") echo "selected"; ?> >100개씩</option>
								<option value="300" <? if ($nPageSize == "300") echo "selected"; ?> >300개씩</option>
								<option value="500" <? if ($nPageSize == "500") echo "selected"; ?> >500개씩</option>
							</select>&nbsp;
							<select name="search_field" style="width:84px;">
								<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >통합검색</option>
								<option value="GOODS_NAME" <? if ($search_field == "GOODS_NAME") echo "selected"; ?> >상품명</option>
								<option value="GOODS_NO" <? if ($search_field == "GOODS_NO") echo "selected"; ?> >상품번호</option>
								<option value="GOODS_CODE" <? if ($search_field == "GOODS_CODE") echo "selected"; ?> >상품코드</option>
								<option value="GOODS_NAME_AND" <? if ($search_field == "GOODS_NAME_AND") echo "selected"; ?> >*상품명(AND)</option>
								<option value="GOODS_SUB_NO" <? if ($search_field == "GOODS_SUB_NO") echo "selected"; ?> >*포함상품번호</option>
								<option value="GOODS_SUB_CODE" <? if ($search_field == "GOODS_SUB_CODE") echo "selected"; ?> >*포함상품코드</option>
								<option value="GOODS_SUB_CODE_AND" <? if ($search_field == "GOODS_SUB_CODE_AND") echo "selected"; ?> >*포함상품코드(AND)</option>
								<option value="GOODS_SUB_NAME_AND" <? if ($search_field == "GOODS_SUB_NAME_AND") echo "selected"; ?> >*포함상품명(AND)</option>
								<option value="CP_CODE" <? if ($search_field == "CP_CODE") echo "selected"; ?> >*공급사코드</option>
							</select>&nbsp;

							<input type="text" value="<?=$search_str?>" name="search_str" size="15" class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();" />
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						</td>
						<td align="right">
							<!--<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>-->
						</td>
					</tr>
				</tbody>
			</table>
			<div class="sp20"></div>

				총 <?=number_format($nListCnt)?> 건

				<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table">
					<colgroup>
						<col width="2%" />
						<col width="5%" />
						<col width="5%" />
						<col width="10%" />
						<col width="*"/>
						<col width="10%" />
						<col width="10%" />
						<col width="20%" />
						<col width="7%" />
					</colgroup>
					<thead>
						<tr>
							<th><!-- <input type="checkbox" name="all_chk" onClick="js_all_check();"> --></th>
							<th>상품번호</th>
							<th>이미지</th>
							<th>상품코드</th>
							<th>상품명</th>
							<th>가로</th>
							<th>세로</th>
							<th>포장메모</th>
							<th class="end"></th>
						</tr>
					</thead>
					
					<tbody>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							$rn								= trim($arr_rs[$j]["rn"]);
							$GOODS_NO					= trim($arr_rs[$j]["GOODS_NO"]);
							$GOODS_CATE				= trim($arr_rs[$j]["GOODS_CATE"]);
							$GOODS_CODE				= trim($arr_rs[$j]["GOODS_CODE"]);
							$GOODS_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
							$GOODS_SUB_NAME			= SetStringFromDB($arr_rs[$j]["GOODS_SUB_NAME"]);
							$CATE_01				= trim($arr_rs[$j]["CATE_01"]);
							$CATE_02				= trim($arr_rs[$j]["CATE_02"]);
							$CATE_03				= trim($arr_rs[$j]["CATE_03"]);
							$CATE_04				= trim($arr_rs[$j]["CATE_04"]);
							$PRICE					= trim($arr_rs[$j]["PRICE"]);
							$SALE_PRICE				= trim($arr_rs[$j]["SALE_PRICE"]);
							$BUY_PRICE				= trim($arr_rs[$j]["BUY_PRICE"]);
							$EXTRA_PRICE			= trim($arr_rs[$j]["EXTRA_PRICE"]);
							$SALE_SUSU				= trim($arr_rs[$j]["SALE_SUSU"]);
							$STOCK_CNT				= trim($arr_rs[$j]["STOCK_CNT"]);
							$TAX_TF					= trim($arr_rs[$j]["TAX_TF"]);
							$IMG_URL				= trim($arr_rs[$j]["IMG_URL"]);
							$FILE_NM				= trim($arr_rs[$j]["FILE_NM_100"]);
							$FILE_RNM				= trim($arr_rs[$j]["FILE_RNM_100"]);
							$FILE_PATH				= trim($arr_rs[$j]["FILE_PATH_100"]);
							$FILE_SIZE				= trim($arr_rs[$j]["FILE_SIZE_100"]);
							$FILE_EXT				= trim($arr_rs[$j]["FILE_EXT_100"]);
							$FILE_NM_150			= trim($arr_rs[$j]["FILE_NM_150"]);
							$FILE_RNM_150			= trim($arr_rs[$j]["FILE_RNM_150"]);
							$FILE_PATH_150			= trim($arr_rs[$j]["FILE_PATH_150"]);
							$FILE_SIZE_150			= trim($arr_rs[$j]["FILE_SIZE_150"]);
							$FILE_EXT_150			= trim($arr_rs[$j]["FILE_EXT_150"]);
							$DELIVERY_CNT_IN_BOX	= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);
							$CONTENTS				= trim($arr_rs[$j]["CONTENTS"]);
							$READ_CNT				= trim($arr_rs[$j]["READ_CNT"]);
							$DISP_SEQ				= trim($arr_rs[$j]["DISP_SEQ"]);
							$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
							$DEL_TF					= trim($arr_rs[$j]["DEL_TF"]);
							$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);

							$WRAP_WIDTH				= trim($arr_rs[$j]["WRAP_WIDTH"]);
							$WRAP_LENGTH			= trim($arr_rs[$j]["WRAP_LENGTH"]);
							$WRAP_MEMO				= trim($arr_rs[$j]["WRAP_MEMO"]);

							$str_goods_no = $GOODS_TYPE.substr("000000".$GOODS_NO,-5);

							//echo $IMG_URL;

							// 이미지가 저장 되어 있을 경우
							$img_url	= getGoodsImage($FILE_NM, $IMG_URL, $FILE_PATH_150, $FILE_RNM_150, "50", "50");


							if($USE_TF == "N")
								$str_use_style = "unused";
							else
								$str_use_style = "";
							
				
				?>
						<tr class="<?=$str_use_style?>" data-goods_no="<?=$GOODS_NO?>" >
							<td>
								<!-- <input type="checkbox" name="chk_no[]" value="<?=$GOODS_NO?>"> -->
							</td>
							<td>
								<?=$GOODS_NO?>
							</td>
							<td style="padding: 1px 1px 1px 1px">
								<img src="<?=$img_url?>" title="클릭하시면 새 창에 원본 이미지가 열립니다." data-thumbnail="<?=$img_url?>" class="goods_thumb" width="50px" height="50px">
							</td>
							<td><?=$GOODS_CODE?></td>
							<td class="modeual_nm"><?= $GOODS_NAME ?> <?= $GOODS_SUB_NAME ?></td>
							<td><input type="text" name="wrap_width" class="txt" style="width:80%;" value="<?= $WRAP_WIDTH?>"/></td>
							<td><input type="text" name="wrap_length" class="txt" style="width:80%;" value="<?= $WRAP_LENGTH?>"/></td>
							<td><input type="text" name="wrap_memo" class="txt" style="width:80%;" value="<?= $WRAP_MEMO?>"/></td>
							<td><input type="button" name="aa" value=" 수정 " class="btntxt btn_update_wrap"></td>
						</tr>
					
				<?			
						}
					} else { 
				?> 
						<tr>
							<td align="center" height="50" colspan="9">데이터가 없습니다. </td>
						</tr>
				<? 
					}
				?>
					</tbody>
				</table>
				<script>
					$(function() {
					
						$(".btn_update_wrap").click(function(){

							var goods_no = $(this).closest("tr").data("goods_no");
							var wrap_width = $(this).closest("tr").find("input[name=wrap_width]").val();
							var wrap_length = $(this).closest("tr").find("input[name=wrap_length]").val();
							var wrap_memo = $(this).closest("tr").find("input[name=wrap_memo]").val();

							//alert(goods_no + " " + wrap_width + " " + wrap_length + " " + wrap_memo);

							
							$.getJSON( "json_goods_wrap.php", {
								mode: "UPDATE_GOODS_WRAP",
								goods_no: goods_no,
								wrap_width: wrap_width,
								wrap_length: wrap_length,
								wrap_memo: wrap_memo
							  })
								.done(function( data ) {
								  alert("수정되었습니다.");
								});
							

						});
					  
					})();
				
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
							$strParam = $strParam."&start_date=".$start_date."&end_date=".$end_date."&start_price=".$start_price."&end_price=".$end_price."&con_cate=".$con_cate;
							$strParam = $strParam."&con_cate_01=".$con_cate_01."&con_cate_02=".$con_cate_02."&con_cate_03=".$con_cate_03."&con_cate_04=".$con_cate_04."&con_tax_tf=".$con_tax_tf;

					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- 페이지 처리 화면 END -------------------------->
			</div>
			<div class="sp50"></div>
			<!-- // E: mwidthwrap -->

		</td>
	</tr>
	</table>
</div>
<iframe src="about:blank" name="ifr_hidden" id="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>