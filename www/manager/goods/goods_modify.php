<?session_start();?>
<?
# =============================================================================
# File Name    : admin_write.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
#	Copyright    : Copyright @C&C Corp. All Rights Reserved.
# =============================================================================

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
	
	$temp_no		= trim($temp_no);
	$goods_no		= trim($goods_no);

	
	//echo $pb_nm; 
	//echo $$mode;
	
	$goods_name			= SetStringToDB($goods_name);
	$goods_sub_name	= SetStringToDB($goods_sub_name);


	$result	= false  ;

#====================================================================
# DML Process
#====================================================================

	if ($mode == "S") {

		$arr_rs = selectTempGoods($conn, $temp_no, $goods_no);

		$rs_goods_no			= trim($arr_rs[0]["GOODS_NO"]); 
		$rs_goods_cate			= trim($arr_rs[0]["GOODS_CATE"]); 
		$rs_goods_code			= trim($arr_rs[0]["GOODS_CODE"]); 
		$rs_goods_name			= SetStringFromDB($arr_rs[0]["GOODS_NAME"]); 
		$rs_goods_sub_name   	= SetStringFromDB($arr_rs[0]["GOODS_SUB_NAME"]); 
		$rs_cate_01				= trim($arr_rs[0]["CATE_01"]); 
		$rs_cate_02				= trim($arr_rs[0]["CATE_02"]); 
		$rs_cate_03				= trim($arr_rs[0]["CATE_03"]); 
		$rs_cate_04				= trim($arr_rs[0]["CATE_04"]); 
		$rs_price				= trim($arr_rs[0]["PRICE"]); 
		$rs_buy_price			= trim($arr_rs[0]["BUY_PRICE"]); 
		$rs_sale_price			= trim($arr_rs[0]["SALE_PRICE"]); 
		$rs_extra_price			= trim($arr_rs[0]["EXTRA_PRICE"]); 
		$rs_stock_cnt			= trim($arr_rs[0]["STOCK_CNT"]); 
		$rs_mstock_cnt          = trim($arr_rs[0]["MSTOCK_CNT"]);
		$rs_tax_tf				= trim($arr_rs[0]["TAX_TF"]); 
		$rs_img_url				= trim($arr_rs[0]["IMG_URL"]); 
		$rs_file_nm_100			= trim($arr_rs[0]["FILE_NM_100"]); 
		$rs_file_rnm_100		= trim($arr_rs[0]["FILE_RNM_100"]); 
		$rs_file_path_100		= trim($arr_rs[0]["FILE_PATH_100"]); 
		$rs_file_size_100		= trim($arr_rs[0]["FILE_SIZE_100"]); 
		$rs_file_ext_100		= trim($arr_rs[0]["FILE_EXT_100"]); 
		$rs_file_nm_150			= trim($arr_rs[0]["FILE_NM_150"]); 
		$rs_file_rnm_150		= trim($arr_rs[0]["FILE_RNM_150"]); 
		$rs_file_path_150		= trim($arr_rs[0]["FILE_PATH_150"]); 
		$rs_file_size_150		= trim($arr_rs[0]["FILE_SIZE_150"]); 
		$rs_file_ext_150		= trim($arr_rs[0]["FILE_EXT_150"]); 
		$rs_contents			= trim($arr_rs[0]["CONTENTS"]); 
		$rs_memo				= trim($arr_rs[0]["MEMO"]); 
		$rs_delivery_cnt_in_box	= trim($arr_rs[0]["DELIVERY_CNT_IN_BOX"]); 
		$rs_sticker_price	    = trim($arr_rs[0]["STICKER_PRICE"]); 
		$rs_print_price		    = trim($arr_rs[0]["PRINT_PRICE"]); 
		$rs_delivery_price      = trim($arr_rs[0]["DELIVERY_PRICE"]); 
		$rs_labor_price			= trim($arr_rs[0]["LABOR_PRICE"]); 
		$rs_other_price			= trim($arr_rs[0]["OTHER_PRICE"]); 
		$rs_sale_susu			= trim($arr_rs[0]["SALE_SUSU"]);  
		$rs_read_cnt			= trim($arr_rs[0]["READ_CNT"]); 
		$rs_disp_seq			= trim($arr_rs[0]["DISP_SEQ"]); 
		$rs_use_tf				= trim($arr_rs[0]["USE_TF"]); 
		$rs_del_tf				= trim($arr_rs[0]["DEL_TF"]); 
		$contents			    = trim($arr_rs[0]["CONTENTS"]); 
		$rs_sticker_price	= trim($arr_rs[0]["STICKER_PRICE"]); 
		$rs_print_price		= trim($arr_rs[0]["PRINT_PRICE"]); 
		$rs_delivery_price= trim($arr_rs[0]["DELIVERY_PRICE"]); 
		$rs_sale_susu			= trim($arr_rs[0]["SALE_SUSU"]); 
		
		$arr_rs_goods_sub = selectTempGoodsSub($conn, $temp_no, $goods_no);
	}

	if ($mode == "U") {


		$goods_cate = "";
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

		$extra_price = $price - $buy_price;

		$result = updateTempGoods($conn, $goods_cate, $goods_code, $goods_name, $goods_sub_name, $cate_01, $cate_02, $cate_03, $cate_04, $price, $buy_price, $sale_price, $extra_price, $stock_cnt, $mstock_cnt, $tax_tf, $img_url, $file_nm_100, $file_rnm_100, $file_path_100, $file_size_100, $file_ext_100, $file_nm_150, $file_rnm_150, $file_path_150, $file_size_150, $file_ext_150, $contents, $memo, $delivery_cnt_in_box, $sticker_price, $print_price, $delivery_price, $sale_susu, $use_tf, $s_adm_no, $temp_no, $goods_no);

		if(startsWith($goods_cate, "14"))
			updateTempGoodsSub($conn, $temp_no, $goods_no, $sub_goods_id, $sub_goods_cnt);
		else
			updateTempGoodsSub($conn, $temp_no, $goods_no, NULL, NULL);
			

	}


	if ($result) {
		$strParam = $strParam."?nPage=".$nPage."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&con_cp_type=".$con_cp_type;
		
		if ($mode == "U") {
?>	
<script language="javascript">
	opener.js_reload();
	self.close();
	//location.href =  "company_modify.php<?=$strParam?>&mode=S&temp_no=<?=$temp_no?>&cp_no=<?=$cp_no?>";
</script>
<?
		} else {
?>	
<script language="javascript">
		alert('정상 처리 되었습니다.');
		location.href =  "goods_list.php<?=$strParam?>";
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
<script type="text/javascript" src="../js/calendar.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->
<script type="text/javascript" src="../../_common/SE2.1.1.8141/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script language="javascript">

	// 저장 버튼 클릭 시 
	function js_save() {
		
		var frm = document.frm;
		var goods_no = "<?= $goods_no ?>";
		
		frm.goods_name.value = frm.goods_name.value.trim();
		frm.buy_price.value = frm.buy_price.value.trim();
		frm.sale_price.value = frm.sale_price.value.trim();

		// 제조사
		frm.cate_02.value = frm.cate_02.value.trim();

		oEditors[0].exec("UPDATE_CONTENTS_FIELD", []);   // 에디터의 내용이 textarea에 적용된다.
		frm.contents.value = frm.contents.value.trim();

		if (frm.gd_cate_01.value == "") {
			alert('카테고리구분을 선택해 주세요.');
			frm.gd_cate_01.focus();
			return ;		
		}
		
		if (isNull(frm.goods_name.value)) {
			alert('상품명을 입력해주세요.');
			frm.goods_name.focus();
			return ;		
		}

		if (frm.cp_type.value == "") {
			alert('공급사를 선택해 주세요.');
			frm.cp_type.focus();
			return ;		
		}

		frm.cate_03.value = frm.cp_type.value;

		if (frm.cate_04.value == "") {
			alert('판매상태를 선택해 주세요.');
			frm.cate_04.focus();
			return ;		
		}

		if (frm.tax_tf.value == "") {
			alert('과세여부를 선택해 주세요.');
			frm.tax_tf.focus();
			return ;		
		}

		if (isNull(frm.buy_price.value)) {
			alert('매입가를 입력해주세요.');
			frm.buy_price.focus();
			return ;		
		}

		if (isNull(frm.sale_price.value)) {
			alert('판매가를 입력해주세요.');
			frm.sale_price.focus();
			return ;		
		}

		if (isNull(frm.contents.value)) {
			alert('상품 상세를 입력해주세요.');
			return ;		
		}

		if (isNull(goods_no)) {
			frm.mode.value = "I";
		} else {
			frm.mode.value = "U";
			frm.goods_no.value = frm.goods_no.value;
		}

		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}


</script>

<script type="text/javascript">

	// tag 관련 레이어가 다 로드 되기전인지 판별하기 위해 필요
	var tag_flag = "0";

	var checkFirst = false;
	var lastKeyword = '';
	var loopSendKeyword = false;
	
	function startSuggest() {

		if ((event.keyCode == 8) || (event.keyCode == 46)) {
			checkFirst = false;
			loopSendKeyword = false;
		}

		if (checkFirst == false) {
			setTimeout("sendKeyword();", 100);
			loopSendKeyword = true;
		}
		checkFirst = true;
	}

	function sendKeyword() {
		
		var frm = document.frm;

		if (loopSendKeyword == false) return;

		var keyword = document.frm.search_name.value;
		
		if (keyword == '') {
			
			lastKeyword = '';
		
			hide('suggest');

		} else if (keyword != lastKeyword) {

			lastKeyword = keyword;
				
			if (keyword != '') {
				frm.keyword.value = keyword;
				frm.goods_type.value = "unit";
				frm.action = "/manager/goods/search_goods.php";
				frm.target = "ifr_hidden";
				frm.submit();
				
			} else {
				hide('suggest');
			}
		}
		setTimeout("sendKeyword();", 100);
	}

	function displayResult(str) {
				
		var resultText = str;
		
		var result = resultText.split('|');

		var count = parseInt(result[0]);

		var keywordList = null;
		var arr_keywordList = null;

		if (count > 0) {
					
			keywordList = result[1].split('^');
			
			var html = '';
					
			for (var i = 0 ; i < keywordList.length ; i++) {
						
				arr_keywordList = keywordList[i].split('');
				
				html += "<table width='100%' border='0'><tr><td style='padding:0px 5px 0px 5px' width='55px'><img src='"+arr_keywordList[2]+"' width='50' height='50' border='0'></td><td><a href=\"javascript:js_select('"+
				arr_keywordList[0]+"','"+arr_keywordList[1]+"')\">"+
				arr_keywordList[1]+"</a></td><td width='105px'>판매가 : "+arr_keywordList[3]+"</td></tr></table>";
		
			}

			var listView = document.getElementById('suggestList');
			listView.innerHTML = html;
					
			suggest.style.visibility  ="visible"; 
		} else {
			suggest.style.visibility  ="hidden"; 
		}
	}


	function js_select(selectedKey,selectedKeyword) {

		var frm = document.frm;

		frm.search_name.value = selectedKeyword;

		arr_keywordValues = selectedKey.split('');

		//frm.goods_name.value					= arr_keywordValues[0];
		//frm.goods_no.value						= arr_keywordValues[1];
        // arr_keywordValues[2]; 매입가
		// arr_keywordValues[3]; 판매가
		var sub_goods_ids = frm.elements['sub_goods_id[]'];
		if(sub_goods_ids != undefined)
		{
			if(sub_goods_ids.value == arr_keywordValues[1]) 
			{
				alert('이미 추가한 상품입니다');
				return;
			}
			for (var i = 0; i < sub_goods_ids.length; i++) {
				if(sub_goods_ids[i].value == arr_keywordValues[1]){
					alert('이미 추가한 상품입니다');
					return;
				}
			}
		}


		$(".sub_goods_list").append("<tr><th>상품명</th><td class='line'>" + arr_keywordValues[0] + "["+ arr_keywordValues[1] + "]" + "<input type='hidden' name='sub_goods_id[]' value='" + arr_keywordValues[1] + "'><input type='hidden' name='sub_buy_price[]' value='" + arr_keywordValues[2] + "'><input type='hidden' name='sub_goods_cate[]' value='" + arr_keywordValues[10] + "'></td><th>매입가</th><td class='line'>" + arr_keywordValues[2] + "</td><th>수량</th><td class='line'><input type='text' name='sub_goods_cnt[]' class='txt' style='width:80px' onblur='javascript:js_calculate_buy_and_sale_price();' value='1'>개</td><td style='border-underline:1px solid #d2dfe5'><span class='remove_sub' style='color:#478fb2; cursor:pointer; font-weight:bold; text-decoration:underline;'>삭제</span></td></tr>");

		js_calculate_buy_and_sale_price();

		loopSendKeyword = false;
		checkFirst = false;
		hide('suggest');

	}

	// 원가 계산
	function js_calculate_buy_and_sale_price( )	{

		var cate1 = $( "select[name='gd_cate_01']" ).val();
		if(cate1.startsWith('14')) {
			var total_buy_price = 0;

			$("input[type='hidden'][name=sub_goods_id\\[\\]]").each(function(index, value){
			    var temp_buy_price = $("input[type='hidden'][name=sub_buy_price\\[\\]]").eq(index).val();
				var temp_goods_cnt = $("input[name=sub_goods_cnt\\[\\]]").eq(index).val();
				var temp_goods_cate = $("input[name=sub_goods_cate\\[\\]]").eq(index).val();
				//alert(temp_goods_cate);
				
				if(temp_goods_cate.startsWith('010202')) { 
					var i_delivery_cnt_in_box = 1;
					
					if ($("input[name=delivery_cnt_in_box]").val() != "") i_delivery_cnt_in_box = parseInt($("input[name=delivery_cnt_in_box]").val());
					
					total_buy_price += Math.round(parseInt(temp_buy_price) * parseInt(temp_goods_cnt) / i_delivery_cnt_in_box);
				}
				else
					total_buy_price += parseInt(temp_buy_price) * parseInt(temp_goods_cnt);
			});
			frm.buy_price.value = total_buy_price;
		}

		var i_sale_price		= 0;
		var i_buy_price			= 0;
		var i_sticker_price = 0;
		var i_print_price		= 0;
		var i_delivery_cnt_in_box = 1;
		var i_delivery_price = 0;
		var f_sale_susu = 0;
		var i_delivery_per_price = 0;
		var i_total_wonga = 0;
		var i_susu_price = 0;
		var i_labor_price = 0;
		var i_other_price = 0;
		var i_majin	= 0;
		var f_majin_per	= 0;

		if ($("input[name=sale_price]").val() != "") i_sale_price = parseInt($("input[name=sale_price]").val());
		if ($("input[name=buy_price]").val() != "") i_buy_price = parseInt($("input[name=buy_price]").val());
		if ($("input[name=sticker_price]").val() != "") i_sticker_price = parseInt($("input[name=sticker_price]").val());
		if ($("input[name=print_price]").val() != "") i_print_price = parseInt($("input[name=print_price]").val());
		if ($("input[name=delivery_cnt_in_box]").val() != "") i_delivery_cnt_in_box = parseInt($("input[name=delivery_cnt_in_box]").val());
		if ($("input[name=delivery_price]").val() != "") i_delivery_price = parseInt($("input[name=delivery_price]").val());
		if ($("input[name=sale_susu]").val() != "") f_sale_susu = parseFloat($("input[name=sale_susu]").val());
		if ($("input[name=labor_price]").val() != "") i_labor_price = parseInt($("input[name=labor_price]").val());
		if ($("input[name=other_price]").val() != "") i_other_price = parseInt($("input[name=other_price]").val());

		var has_susu = $("input[name=has_susu]").is(":checked");
		
		if(i_delivery_price == 0)
			i_delivery_per_price = 0;
		else
			i_delivery_per_price = Math.round(i_delivery_price / i_delivery_cnt_in_box);
		$("#delivery_per_price").html(numberFormat(i_delivery_per_price));

		i_susu_price = Math.round((i_sale_price / 100) * f_sale_susu);
		$("#susu_price").html(numberFormat(i_susu_price));

		i_total_wonga = i_buy_price + i_sticker_price + i_print_price + i_delivery_per_price + i_labor_price + i_other_price;
		$("#total_wonga").val(i_total_wonga);
		
		if(!has_susu) i_susu_price = 0;

		i_majin = i_sale_price - i_susu_price - i_total_wonga;
		if(i_majin > 0)
			$("#majin").html(numberFormat(i_majin));
		else
			$("#majin").html(i_majin);
		
		if (i_sale_price != 0) {
			f_majin_per = Math.round10((i_majin / i_sale_price) * 100, -2);
			$("#majin_per").html(f_majin_per);
		} else {
			if (i_majin == 0) {
				f_majin_per = 0
				$("#majin_per").html(f_majin_per);
			} else {
				f_majin_per = -100
				$("#majin_per").html(f_majin_per);
			}
		}

		$(".calc").each(function(index, value){
	
			var name = $(this).attr("name");
			if(name.indexOf("[]") <= -1) { 
				if(name != "sale_susu") { 
					if($(this).val() != parseInt($("font[class="+name+"]").attr("data-value")))
						$("font[class="+name+"]").show();
					else
						$("font[class="+name+"]").hide();
				} else {
					if($(this).val() != parseFloat($("font[class="+name+"]").attr("data-value")))
						$("font[class="+name+"]").show();
					else
						$("font[class="+name+"]").hide();
				}
			}

		});

	}


	function show(elementId) {
		var element = document.getElementById(elementId);
		
		if (element) {
			element.style.visibility  ="visible"; 
			//element.style.display = '';
		}
	}

	function hide(elementId) {
		var element = document.getElementById(elementId);
		if (element) {
			element.style.visibility  ="hidden"; 
			//element.style.display = 'none';
		}
	}

$(function(){
	$('body').on('click', '.remove_sub', function() {
		$(this).closest("tr").remove();
		js_calculate_buy_and_sale_price();
	});
});
	

</script>
</head>
<body id="popup_file">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="" />
<input type="hidden" name="depth" value="" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="temp_no" value="<?= $temp_no?>">
<input type="hidden" name="goods_no" value="<?=$goods_no?>" />
<input type="hidden" name="nPage" value="<?=$nPage?>" />
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>" />

<input type="hidden" name="search_field" value="<?=$search_field?>">
<input type="hidden" name="search_str" value="<?=$search_str?>">
<input type="hidden" name="keyword" value="">
<input type="hidden" name="goods_type" value="">

<div id="popupwrap_file">
	<h1>상품 등록 수정</h1>
	<div id="postsch">
		<h2>* 상품 정보를 수정 합니다.</h2>
		<div class="addr_inp">

			<table cellpadding="0" cellspacing="0" class="colstable02">

				<colgroup>
					<col width="15%" />
					<col width="35%" />
					<col width="15%" />
					<col width="35%" />
				</colgroup>
				<thead>
					<tr>
						<th>카테고리 구분</th>
						<td colspan="3" class="line">
							<?= makeCategorySelectBoxOnChange($conn, $rs_goods_cate, $exclude_category);?>
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>상품명</th>
						<td colspan="3" class="line">
							<input type="text" class="txt" style="width:95%" name="goods_name" required value="<?=$rs_goods_name?>" />
						</td>
					</tr>
					<tr class="set_goods" style="display:none;">
						<th>구성상품 등록</th>
							<td colspan="3" style="position:relative" class="line">
								<div id="suggest" style="position:absolute; left:15px; top:25px; width:95%; height:81px; z-index:0; visibility: hidden; width:95%; ">
									<div id="suggestList" style="position:relative; border:solid 1px #cec6ba; background:#FFFFFF; width:95%;"></div>
								</div>
								<input type="text" class="txt search_name" style="width:95%; ime-mode:Active;" name="search_name" value="" onKeyDown="startSuggest();" onFocus="this.value='';" />

								<table cellpadding="0" cellspacing="0" class="colstable02" style="margin-top:5px;">
								<colgroup>
									<col width="10%" />
									<col width="25%" />
									<col width="10%" />
									<col width="10%" />
									<col width="10%" />
									<col width="20%" />
									<col width="5%" />
								</colgroup>
								<thead>
									<tr>
										<th colspan="7" class="line">상품을 검색해서 선택하시면 아래에 구성 상품이 추가됩니다</th>
									</tr>
								</thead>
								<tbody class="sub_goods_list">
								</tbody>
								</table>
							</td>
					</tr>
					<script>
						$( "select[name='gd_cate_01']" ).change(function(){
							var cate1 = $(this).val();
							if(cate1.startsWith('14')) //카테고리 1 - 세트선택시
								$(".set_goods").show();
							else
								$(".set_goods").hide();
						 });
					<?
						if(startsWith($rs_goods_cate, '14')) { 
					?>
				  		 $(".set_goods").show();
					<? 
						//$rs_buy_price = 0;
						for($i = 0; $i < sizeof($arr_rs_goods_sub); $i++) {

					?>
						
						$(".sub_goods_list").append("<tr><th>상품명</th><td class='line'> <?=$arr_rs_goods_sub[$i]["GOODS_NAME"]?>[<?=$arr_rs_goods_sub[$i]["GOODS_SUB_NO"]?>]<input type='hidden' name='sub_goods_id[]' value='<?=$arr_rs_goods_sub[$i]["GOODS_SUB_NO"]?>'><input type='hidden' name='sub_goods_cate[]' value='<?=$arr_rs_goods_sub[$i]["GOODS_CATE"]?>'></td><th>매입가</th><td class='line'><input type='hidden' name='sub_buy_price[]' value='<?=$arr_rs_goods_sub[$i]["BUY_PRICE"]?>'><?=$arr_rs_goods_sub[$i]["BUY_PRICE"]?></td><th>수량</th><td class='line'><input type='text' name='sub_goods_cnt[]' class='txt' onblur='javascript:js_calculate_buy_and_sale_price();'  style='width:80px' value='<?=$arr_rs_goods_sub[$i]["GOODS_CNT"]?>'>개</td><td style='border-underline:1px solid #d2dfe5'><span class='remove_sub' style='color:#478fb2; cursor:pointer; font-weight:bold; text-decoration:underline;'>삭제</span></td></tr>");	
					<?
						//$rs_buy_price = $rs_buy_price + $arr_rs_goods_sub[$i]["PRICE"];

						}
					} 
					?>
					</script>
					<tr>
						<th>모델명</th>
						<td class="line">
							<input type="text" class="txt" style="width:210px" name="goods_sub_name" value="<?=$rs_goods_sub_name?>" />
						</td>
						<th>상품코드</th>
						<td class="line">
							<input type="text" class="txt" style="width:110px" name="goods_code" value="<?=$rs_goods_code?>" />
							<a href="javascript:js_search();"><img src="../images/admin/btn_search.gif" alt="확인" /></a>
							&nbsp;&nbsp;&nbsp;<span class="msg"></span>
							<script>

								function js_search() { 
											var url = "pop_goods_reference.php?keyword=" + $("input[name=goods_code]").val();
											NewWindow(url,'pop_goods_reference','900','600','Yes');
								}

								$("input[name=goods_code]").keyup(function(){
									var new_code = $(this).val().trim();
									if(new_code.length >= 10)
										checkDuplicate($(this).val().trim(), '');
								});

								function checkDuplicate(new_code, serial_part) {

									if (!isNull(new_code)) {

										$.ajax({
										  url: "json_goods_list.php",
										  dataType: 'json',
										  async: false,
										  data: {goods_code: new_code, serial_part:serial_part},
										  success: function(data) {
											$.each( data, function( i, item ) {
												
												if(item.RESULT != "0")
												{
													$(".msg").css("color","red");
													$(".msg").html("에러 : 상품코드 중복");

												} else if(item.PARTLY != "0")
												{
													$(".msg").css("color","blue");
													$(".msg").html("체크요망 : 일련번호 중복");
												} else if(item.RESULT != "0" && item.PARTLY != "0")
												{
													$(".msg").css("color","red");
													$(".msg").html("에러 : 상품코드 중복");
												} else {
													$(".msg").css("color","black");
													$(".msg").html("");
												}

											  });
										  }
										});
									}

								}
							</script>
						</td>
					</tr>

					<tr>
						<th>제조사</th>
						<td class="line">
							<input type="text" class="txt" style="width:210px" name="cate_02" value="<?=$rs_cate_02?>" />
						</td>
						<th>최소재고</th>
						<td class="line">
							<input type="text" class="txt" style="width:210px" name="mstock_cnt" required value="<?=$rs_mstock_cnt?>" onkeyup="return isNumber(this)"/> 개
						</td>
					</tr>

					<tr>
						<th>공급사</th>
						<td class="line">
							<? if ($s_adm_cp_type == "운영") { ?>
							<input type="text" class="supplyer" style="width:210px" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'구매',$rs_cate_03)?>" />
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
							<input type="hidden" name="cp_type" value="<?=$rs_cate_03?>" />
							<input type="hidden" name="cate_03" value="<?=$rs_cate_03?>" />
							<? } else {?>
							<?=getCompanyName($conn, $s_adm_com_code)?>
							<input type="hidden" name="cp_type" value="<?=$s_adm_com_code?>" />
							<input type="hidden" name="cate_03" value="<?=$s_adm_com_code?>" />
							<? } ?>
							
						</td>
						<th>재고 수량</th>
						<td class="line">
							<?=$rs_stock_cnt?> 개
							<input type="hidden" name="stock_cnt" value="<?=$rs_stock_cnt?>" />
						</td>
					</tr>


					<tr>
						<th>판매상태</th>
						<td  class="line">
							<?= makeSelectBox($conn,"GOODS_STATE","cate_04","175","판매상태","",$rs_cate_04)?>
						</td>

						<th>과세여부</th>
						<td class="line">
							<?= makeSelectBox($conn,"TAX_TF","tax_tf","105","","",$rs_tax_tf)?>
						</td>
					</tr>
					</table>
					<div class="sp20"></div>
					가격 정보 (괄호안 숫자는 현재 저장되어 있는 가격입니다) 
					<table cellpadding="0" cellspacing="0" class="colstable02">
					<colgroup>
						<col width="15%" />
						<col width="35%" />
						<col width="15%" />
						<col width="35%" />
					</colgroup>
					<? if ($s_adm_cp_type == "운영") { ?>
					<tr>
						<th title="(세트)매입가 = 아웃박스 제외 구성자재 매입가 * 수량의 합 + (아웃박스 매입가 * 수량 / 박스입수)">매입가</th>
						<td class="line">
							<input type="text" class="txt calc buy_price" style="width:90px" name="buy_price" value="<?=$rs_buy_price?>" required onkeyup="return isNumber(this)" onChange="js_calculate_buy_and_sale_price()" <?=(startsWith($rs_goods_cate, '14')  ? "readonly" : "") ?> /> 원 <font class="buy_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_buy_price?>">(<?=$rs_buy_price?> 원)</font>
						</td>
						<th>판매가</th>
						<td class="line">
							<input type="text" class="txt calc sale_price" style="width:90px" name="sale_price" value="<?=$rs_sale_price?>" required onkeyup="return isNumber(this)" onChange="js_calculate_buy_and_sale_price()" /> 원 <font class="sale_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_sale_price?>">(<?=$rs_sale_price?> 원)</font>
						</td>
					</tr>
					
					<tr>
						<th>스티커 비용</th>
						<td class="line" colspan="3">
							<input type="text" class="txt calc sticker_price" style="width:90px" name="sticker_price" value="<?=$rs_sticker_price?>" onkeyup="return isNumber(this)" onChange="js_calculate_buy_and_sale_price()"/> 원 <font class="sticker_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_sticker_price?>">(<?=$rs_sticker_price?> 원)</font>
						</td>
						
					</tr>
					<tr>
						<th>포장인쇄 비용</th>
						<td class="line" colspan="3">
							<input type="text" class="txt calc print_price" style="width:90px" name="print_price" value="<?=$rs_print_price?>" onkeyup="return isNumber(this)" onChange="js_calculate_buy_and_sale_price()"/> 원 <font class="print_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_print_price?>">(<?=$rs_print_price?> 원)</font>
						</td>

					</tr>
					<tr>
						<th>택배비용</th>
						<td class="line" colspan="3">
							<input type="text" class="txt calc delivery_price" style="width:90px" name="delivery_price" value="<?=$rs_delivery_price?>" onkeyup="return isNumber(this)" onChange="js_calculate_buy_and_sale_price()"/> 원 <font class="delivery_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_delivery_price?>">(<?=$rs_delivery_price?> 원)</font>
						</td>
					</tr>
					<tr>
						<th>박스입수</th>
						<td class="line">
							<input type="text" class="txt calc delivery_cnt_in_box" style="width:90px" name="delivery_cnt_in_box" value="<?=$rs_delivery_cnt_in_box?>" onkeyup="return isNumber(this)" onChange="js_calculate_buy_and_sale_price()"/> 개 <font class="delivery_cnt_in_box" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_delivery_cnt_in_box?>">(<?=$rs_delivery_cnt_in_box?> 개)</font>
						</td>
						<th>판매수수료 적용여부</th>
						<td class="line"><input type="checkbox" name="has_susu" onChange="js_calculate_buy_and_sale_price()" value="Y"/>&nbsp;(참조용 - 저장하지 않음)</td>
					</tr>
					<tr>
						<th title="물류비 = 택배비용 / 박스입수">
							물류비
						</th>
						<td class="line">
							<span id="delivery_per_price">0</span> 원
						</td>
						<th>판매 수수률</th>
						<td class="line">
							<input type="text" class="txt calc sale_susu" style="width:90px" name="sale_susu" value="<?=$rs_sale_susu?>" onkeyup="return isFloat(this)" onChange="js_calculate_buy_and_sale_price()"/> % <font class="sale_susu" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_sale_susu?>">(<?=$rs_sale_susu?> %)</font> 
						</td>					
	
					</tr>
					<tr>
						<th>인건비</th>
						<td class="line">
							<input type="text" class="txt calc labor_price" style="width:90px" name="labor_price" value="<?=$rs_labor_price?>" onkeyup="return isNumber(this)" onChange="js_calculate_buy_and_sale_price()"/> 원 <font class="labor_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_labor_price?>">(<?=$rs_labor_price?> 원)</font>
						</td>
						<th title="판매 수수료 = ((판매가 * 100) * 판매 수수률)">판매 수수료</th>
						<td class="line">
							<span id="susu_price">0</span> 원
						</td>
											
					</tr>
					<tr>
						<th>기타 비용</th>
						<td class="line">
							<input type="text" class="txt calc other_price" style="width:90px" name="other_price" value="<?=$rs_other_price?>" onkeyup="return isNumber(this)" onChange="js_calculate_buy_and_sale_price()"/> 원 <font class="other_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_other_price?>">(<?=$rs_other_price?> 원)</font>
						</td>
						<th title="마진 = 판매가 - 판매수수료 - 매입합계">마진</th>
						<td class="line">
							<span id="majin">0</span> 원
							
						</td>	
					</tr>
					<tr>
						<th title="매입합계 = 매입가(아웃박스 제외 자재매입가의 합 + (아웃박스 매입가 / 박스입수)) + 스티커비용 + 포장인쇄비용 + 물류비 + 인건비 + 기타비용">매입합계</th>
						<td class="line">
							<input type="text" id="total_wonga" class="txt calc price" style="width:90px" name="price" value="<?=$rs_price?>" onkeyup="return isNumber(this)" readonly /> 원 <font class="price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_price?>">(<?=$rs_price?> 원)</font>
							
						</td>
						<th title="마진률 = 마진 / 판매가 * 100">마진률</th>
						<td class="line">
							<span id="majin_per">0</span> %
						</td>
					</tr>
					<? }  ?>
					</table>
					<div class="sp20"></div>
					상세 정보
					<table cellpadding="0" cellspacing="0" class="colstable02">
					<colgroup>
						<col width="15%" />
						<col width="35%" />
						<col width="15%" />
						<col width="35%" />
					</colgroup>
					<tr>
						<th>상품 이미지 URL</th>
						<td colspan="3" class="line">
							<?
								if ($rs_img_url <> "") {
							?>
							<img src="<?=$rs_img_url?>" alt="<?=$rs_img_url?>" width="100" alt="이미지"><br>
							<?
								}
							?>
							<input type="text" class="txt" style="width:75%" name="img_url" value="<?=$rs_img_url?>" />
						</td>
					</tr>
					<tr>
						<th>이미지 경로</th>
						<td colspan="3" class="line">
							<input type="text" class="txt" style="width:75%" name="file_path_150" value="<?=$rs_file_path_150?>" />
						</td>
					</tr>
					<tr>
						<th>이미지 파일명</th>
						<td colspan="3" class="line">
							<input type="text" class="txt" style="width:75%" name="file_rnm_150" value="<?=$rs_file_rnm_150?>" />
						</td>
					</tr>
					<tr>
						<th>비고란</th>
						<td colspan="3" class="line">
							<textarea name="memo" style="padding-left:0px;width:100%;height:100px;"><?=$rs_memo?></textarea>
						</td>
					</tr>
					<tr>
						<th>상품 상세</th>
						<td colspan="3" class="subject line">
							<span class="fl" style="padding-left:0px;width:900px;height:500px;"><textarea name="contents" id="contents"  style="padding-left:0px;width:890px;height:400px;"><?=$rs_contents?></textarea></span>
						</td>
					</tr>

				</tbody>
			</table>
				
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
<script type="text/javascript" src="../js/wrest.js"></script>
</form>
<SCRIPT LANGUAGE="JavaScript">

var oEditors = [];
	nhn.husky.EZCreator.createInIFrame({
	oAppRef: oEditors,
	elPlaceHolder: "contents",
	sSkinURI: "../../_common/SE2.1.1.8141/SmartEditor2Skin.html",
	htParams : {
		bUseToolbar : true, 
		fOnBeforeUnload : function(){ 
			// alert('야') 
		},
		fOnAppLoad : function(){ 
		// 이 부분에서 FOCUS를 실행해주면 됩니다. 
		this.oApp.exec("EVENT_EDITING_AREA_KEYDOWN", []); 
		this.oApp.setIR(""); 
		//oEditors.getById["ir1"].exec("SET_IR", [""]); 
		}
	}, 
	fCreator: "createSEditor2"
});

js_calculate_buy_and_sale_price();

//-->
</SCRIPT>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>