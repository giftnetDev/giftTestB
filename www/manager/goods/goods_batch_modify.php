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
	$menu_right = "GD004"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/company/company.php";
#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);

	$strParam = $strParam."?nPage=".$nPage."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str;
	$strParam = $strParam."&start_date=".$start_date."&end_date=".$end_date."&start_price=".$start_price."&end_price=".$end_price."&con_cate=".$con_cate;
	$strParam = $strParam."&con_cate_01=".$con_cate_01."&con_cate_02=".$con_cate_02."&con_cate_03=".$con_cate_03."&con_cate_04=".$con_cate_04;
	$strParam = $strParam."&chk_vendor=".$chk_vendor."&vendor_calc=".$vendor_calc."&view_type=".$view_type."&exclude_category=".$exclude_category;
	$strParam = $strParam."&gd_cate_01=".$old_gd_cate_01."&gd_cate_02=".$old_gd_cate_02."&gd_cate_03=".$old_gd_cate_03."&gd_cate_04=".$old_gd_cate_04."&con_cate=".$old_con_cate;

	if ($gd_cate_01 <> "") {
		$goods_cate = $gd_cate_01;
	}
	if ($gd_cate_02 <> "") {
		$goods_cate = $gd_cate_02;
	}
	if ($gd_cate_03 <> "") {
		$goods_cate = $gd_cate_03;
	}
	if ($gd_cate_04 <> "") {
		$goods_cate = $gd_cate_04;
	}

	$result	= false  ;

#====================================================================
# DML Process
#====================================================================

	if($mode == "" && count($chk_no) == 0) {
?>
<script language="javascript">
		alert('선택된 상품이 없습니다. 체크박스로 변경하실 상품을 리스트에서 먼저 선택해주세요.');
		self.close();
</script>
<?
		exit;
	}

	if ($mode == "U") {

		$row_cnt = count($hid_chk_no);
		for ($k = 0; $k < $row_cnt; $k++) {
			$str_goods_no = $hid_chk_no[$k];

			//echo $str_goods_no."<br/>";

			if($chk0 == 'on') { 
				
				if($cate_03 == "")
					continue;

				$result = updateGoodsBatch($conn, "CATE_03", $cate_03, $s_adm_no, $str_goods_no);
			}

			if($chk1 == 'on') {

				if($goods_cate == "")
					continue;

				if($cate_option == "Y") 
					$result = insertGoodsCategory($conn, $str_goods_no, $goods_cate, $page, $seq);
				else if($cate_option == "N")
					$result = deleteGoodsCategoryBatch($conn, $str_goods_no, $goods_cate);
				else
					$result = updateGoodsBatch($conn, "GOODS_CATE", $goods_cate, $s_adm_no, $str_goods_no);

			}

			if($chk6 == 'on') { 
				
				if($delivery_price == "")
					continue;
				
				$result = updateGoodsPrice($conn, "DELIVERY_PRICE", $delivery_price, $s_adm_no, $str_goods_no);
			}

			if($chk8 == 'on') { 
				$result = updateGoodsBatch($conn, "STOCK_TF", $stock_tf, $s_adm_no, $str_goods_no);
			}

			if($chk10 == 'on') { 
				$result = updateGoodsBatch($conn, "DELIVERY_CNT_IN_BOX", $delivery_cnt_in_box, $s_adm_no, $str_goods_no);
			}
			/*
			
			if($chk2 != 'on')
				$cate_04 = '';
			if($chk3 != 'on')
				$tax_tf = '';
			if($chk4 != 'on')
				$sticker_price = '';
			if($chk5 != 'on')
				$print_price = '';
			if($chk6 != 'on')
				$delivery_price = '';
			if($chk7 != 'on')
				$sale_susu = '';
			*/

 			
		}

	}


	if ($result) {
		$strParam = $strParam."&con_cp_type=".$con_cp_type;
		
		if ($mode == "U") {

			echo "goods_list.php".$strParam;
?>	
<script language="javascript">
		alert('정상 처리 되었습니다.');
		self.close();
		opener.location.href = "goods_list.php<?=$strParam?>";
</script>
<?
		}
		exit;
	}	
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->
<script type="text/javascript" src="../../_common/SE2.1.1.8141/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script language="javascript">

	// 저장 버튼 클릭 시 
	function js_save() {
		
		var frm = document.frm;

		// 스크립트 구현 부분

		if (document.frm.rs_stock_tf == null) {
			//alert(document.frm.rs_stock_tf);
		} else {
			if (frm.rs_stock_tf[0].checked == true) {
				frm.stock_tf.value = "Y";
			} else {
				frm.stock_tf.value = "N";
			}
		}

		frm.mode.value = "U";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}


</script>
</head>
<body id="popup_file">

<form name="frm" method="post">
<input type="hidden" name="mode" value=""/>
<input type="hidden" name="depth" value=""/>

<!-- 리스트로 되돌리는 파라메터 -->
<input type="hidden" name="nPage" value="<?=$nPage?>"/>
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>"/>
<input type="hidden" name="search_field" value="<?=$search_field?>"/>
<input type="hidden" name="search_str" value="<?=$search_str?>"/>
<input type="hidden" name="order_field" value="<?=$order_field?>"/>
<input type="hidden" name="order_str" value="<?=$order_str?>"/>

<input type="hidden" name="start_date" value="<?=$start_date?>"/>
<input type="hidden" name="end_date" value="<?=$end_date?>"/>
<input type="hidden" name="start_price" value="<?=$start_price?>"/>
<input type="hidden" name="end_price" value="<?=$end_price?>"/>

<input type="hidden" name="con_cate_01" value="<?=$con_cate_01?>"/>
<input type="hidden" name="con_cate_02" value="<?=$con_cate_02?>"/>
<input type="hidden" name="con_cate_03" value="<?=$con_cate_03?>"/>
<input type="hidden" name="con_cate_04" value="<?=$con_cate_04?>"/>

<input type="hidden" name="chk_vendor" value="<?=$chk_vendor?>"/>
<input type="hidden" name="vendor_calc" value="<?=$vendor_calc?>"/>
<input type="hidden" name="view_type" value="<?=$view_type?>"/>
<input type="hidden" name="exclude_category" value="<?=$exclude_category?>"/>

<input type="hidden" name="old_gd_cate_01" value="<?=$gd_cate_01?>"/>
<input type="hidden" name="old_gd_cate_02" value="<?=$gd_cate_02?>"/>
<input type="hidden" name="old_gd_cate_03" value="<?=$gd_cate_03?>"/>
<input type="hidden" name="old_gd_cate_04" value="<?=$gd_cate_04?>"/>
<input type="hidden" name="old_con_cate" value="<?=$con_cate?>"/>


<!-- 리스트로 되돌리는 파라메터 -->

<?
	if($chk_no != null)
	{
		$postvalue = "";
		foreach ($chk_no as $goods_no) {
		  $postvalue .= '<input type="hidden" name="hid_chk_no[]" value="' .$goods_no. '" />';
		}
		echo $postvalue;
	}
?>
<div id="popupwrap_file">
	<h1>상품 일괄 수정</h1>
	<div id="postsch">
		<h2>* 상품 정보를 일괄 수정 합니다.<br>
			- 수정 항목을 체크 후 확인을 클릭 하시면 해당 내용이 일괄 수정 됩니다.
		</h2>
		<div class="addr_inp">

			<table cellpadding="0" cellspacing="0" class="colstable02">

				<colgroup>
					<col width="3%" />
					<col width="8%" />
					<col width="*" />
				</colgroup>
				<tr>
					<th><input type="checkbox" name="chk1"></th>
					<th>카테고리</th>
					<td class="line input_field">
						<?= makeCategorySelectBoxOnChange($conn, $goods_cate, "");?>
						<br/><br/>
						<b>메인 카테고리 :</b> <label><input type="radio" name="cate_option" value="M" checked="checked"/> 이동</label><br/>
						<b>검색 카테고리 :</b>
						<input type="radio" name="cate_option" value="Y"/> 추가</label><input type="text" name="page" value="<?=$page?>" placeholder="페이지" style="width:40px;"/><label>&nbsp;&nbsp;|&nbsp;&nbsp;
						<label><input type="radio" name="cate_option" value="N"/> 제거</label>
						
					</td>
				</tr>
				
				<tr>
					<th><input type="checkbox" name="chk0"></th>
					<th>공급사</th>
					<td class="line input_field">
							<input type="text" class="autocomplete_off" style="width:90%" placeholder="업체(명/코드) 입력후 엔터를 눌러주세요" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'',$rs_cate_03)?>" />
							<!--<input type="hidden" name="con_cate_03" value="<?=$con_cate_03?>">-->
							<input type="hidden" name="cp_type" value="<?=$rs_cate_03?>" />
							<input type="hidden" name="cate_03" value="<?=$rs_cate_03?>" />

							<script>
								$(function(){

									$("input[name=txt_cp_type]").keydown(function(e){

										if(e.keyCode==13) { 

											var keyword = $(this).val();
											if(keyword == "") { 
												$("input[name=cate_03]").val('');
											} else { 
												$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('구매,판매공급') +"&term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
													if(data.length == 1) { 
														
														js_selecting_company("txt_cp_type", data[0].label, "cate_03", data[0].id);

													} else if(data.length > 1){ 
														NewWindow("../company/pop_company_searched_list.php?con_cp_type=구매,판매공급&search_str="+keyword + "&target_name=txt_cp_type&target_value=cate_03",'pop_company_searched_list','950','650','YES');

													} else 
														alert("검색결과가 없습니다.");
												});
											}
										}

									});

									$("input[name=txt_cp_type]").keyup(function(e){
										var keyword = $(this).val();

										if(keyword == "") { 
											$("input[name=cate_03]").val('');
										}
									});

								});

								function js_selecting_company(target_name, cp_nm, target_value, cp_no) {
									
									$(function(){

										$("input[name="+target_name+"]").val(cp_nm);
										$("input[name="+target_value+"]").val(cp_no);

									});

								}

							</script>
						<!--
						<input type="text" class="supplyer" style="width:250px" name="txt_cp_type" placeholder="업체명이나 업체코드를 입력해주세요" required value="<?=getCompanyAutocompleteTextBox($conn,'구매',"")?>" />
						<script>
						$(function() {
						 var cache = {};
							$( ".supplyer" ).autocomplete({
								source: function( request, response ) {
									var term = request.term;
									if ( term in cache ) {
										response( cache[term] );
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
									$("input[name=cate_03]").val(ui.item.id);

									var TABKEY = 9;
									this.value = ui.item.value;

									if (event.keyCode == TABKEY) { 
										event.preventDefault();
										$(".supplyer").val(ui.item.value);
										$("input[name=cate_03]").val(ui.item.id);
									}
								}
							}).bind( "blur", function( event ) {
								var cp_no = $("input[name=cate_03]").val();
								
								if(cp_no != '') {
									$.getJSON( "../company/json_company_list.php?cp_no=" + cp_no, function(data) {

										if(data[0].CP_NO == 'undefined') {
											$("input[name=cate_03]").val('');
										} else {
											if(data[0].COMPANY != $(".supplyer").val())
											{
												$(".supplyer").val('');
												$("input[name=cate_03]").val('');
											}
										}
									});
								} else {
									$(".supplyer").val('');
									$("input[name=cate_03]").val('');
								}
							});
						});
						</script>
						<input type="hidden" name="cp_type" value="<?=$rs_cate_03?>" />
						<input type="hidden" name="cate_03" value="<?=$rs_cate_03?>" />
						-->
					</td>
				</tr>	
				
				<!--
				
				<tr>
					<th><input type="checkbox" name="chk2"></th>
					<th>판매상태</th>
					<td class="line">
						<?= makeSelectBox($conn,"GOODS_STATE","cate_04","175","판매상태","",$rs_cate_04)?>
					</td>
				</tr>
				<tr>
					<th><input type="checkbox" name="chk3"></th>
					<th>과세여부</th>
					<td class="line">
						<?= makeSelectBox($conn,"TAX_TF","tax_tf","105","","",$rs_tax_tf)?>
					</td>
				</tr>

				<tr>
					<th><input type="checkbox" name="chk4"></th>
					<th>스티커 비용</th>
					<td class="line">
						<input type="text" class="txt" style="width:90px" name="sticker_price" value="" onkeyup="return isNumber(this)" /> 원
					</td>
				</tr>
				<tr>
					<th><input type="checkbox" name="chk5"></th>
					<th>포장인쇄 비용</th>
					<td class="line">
						<input type="text" class="txt" style="width:90px" name="print_price" value="" onkeyup="return isNumber(this)" /> 원
					</td>
				</tr>
				-->
				

				<tr>
					<th><input type="checkbox" name="chk6"></th>
					<th>택배비용</th>
					<td class="line input_field">
						<input type="text" class="txt" style="width:90px" name="delivery_price" value="" onkeyup="return isNumber(this)" /> 원
					</td>
				</tr>
				<tr>
					<th><input type="checkbox" name="chk8"></th>
					<th>재고관리 여부</th>
					<td class="line input_field" >
						<input type="radio" class="radio" name="rs_stock_tf" value="Y"> 사용함 <span style="width:20px;"></span>&nbsp;&nbsp;&nbsp;
						<input type="radio" class="radio" name="rs_stock_tf" value="N"> 사용안함
						<input type="hidden" name="stock_tf" value=""> 
					</td>
				</tr>
				<tr>
					<th><input type="checkbox" name="chk10"></th>
					<th>박스입수</th>
					<td class="line input_field">
						<input type="text" class="txt" style="width:90px" name="delivery_cnt_in_box" value="" onkeyup="return isNumber(this)" /> 개
					</td>
				</tr>
				<!--
				<tr>
					<th><input type="checkbox" name="chk7"></th>
					<th>판매 수수률</th>
					<td class="line">
						<input type="text" class="txt" style="width:90px" name="sale_susu" value="" onkeyup="return isFloat(this)" /> %
					</td>
				</tr>
				-->
			</table>
			<script>
				$(function(){
					$(".input_field").click(function(){
						$(this).closest("tr").find("th").eq(0).find("input").prop("checked", "checked");
					});
				});
			</script>
				
		</div>
		<div class="btn">
		<? if ($adm_no <> "" ) {?>
			<? if ($sPageRight_U == "Y") {?>
      <a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
			<? } ?>
		<? } else {?>
			<? if ($sPageRight_I == "Y") {?>
      <a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
			<? } ?>
		<? }?>
		</div>

	</div>
	<br />
	<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
</div>
<iframe src="" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>

</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>