<?session_start();?>
<?
# =============================================================================
# File Name    : out_modify.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2015-07-14
# Modify Date  : 
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
	$menu_right = "SG008"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/stock/stock.php";
#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);
	
	$temp_no		= trim($temp_no);
	$stock_no		= trim($stock_no);

	//echo $pb_nm; 
	//echo $$mode;

	$result	= false  ;

#====================================================================
# DML Process
#====================================================================

	if ($mode == "S") {

		$arr_rs = selectTempStock($conn, $temp_no, $stock_no);

		$rs_temp_no				= SetStringFromDB($arr_rs[0]["TEMP_NO"]); 
		$rs_stock_no			= SetStringFromDB($arr_rs[0]["STOCK_NO"]); 
		$rs_stock_type		= SetStringFromDB($arr_rs[0]["STOCK_TYPE"]); 
		$rs_stock_code		= SetStringFromDB($arr_rs[0]["STOCK_CODE"]); 
		$rs_goods_no			= SetStringFromDB($arr_rs[0]["GOODS_NO"]); 
		$rs_goods_code		= SetStringFromDB($arr_rs[0]["GOODS_CODE"]); 
		$rs_goods_name		= SetStringFromDB($arr_rs[0]["GOODS_NAME"]); 
		$rs_cp_no					= SetStringFromDB($arr_rs[0]["CP_NO"]); 
		$rs_cp_code				= SetStringFromDB($arr_rs[0]["CP_CODE"]); 
		$rs_cp_name				= SetStringFromDB($arr_rs[0]["CP_NAME"]); 
		$rs_in_loc				= SetStringFromDB($arr_rs[0]["IN_LOC"]); 
		$rs_in_loc_ext		= SetStringFromDB($arr_rs[0]["IN_LOC_EXT"]); 
		$rs_qty						= SetStringFromDB($arr_rs[0]["QTY"]); 
		$rs_price					= SetStringFromDB($arr_rs[0]["PRICE"]); 
		$rs_in_date				= SetStringFromDB($arr_rs[0]["IN_DATE"]); 
		$rs_pay_date			= SetStringFromDB($arr_rs[0]["PAY_DATE"]); 

		//echo $rs_cp_no;

		if ($rs_cp_code == "") {

			if ($rs_goods_no != "") {

				$goods_rs = selectGoods($conn, $rs_goods_no);
				$rs_cp_no		= trim($goods_rs[0]["CATE_03"]); 
				
				if ($rs_price == "") {
					$rs_price		= trim($goods_rs[0]["BUY_PRICE"]);
				}

				$arr_company = selectCompany($conn, $rs_cp_no);
				$rs_cp_code = trim($arr_company[0]["CP_CODE"]); 
				$rs_cp_name = trim($arr_company[0]["CP_NM"]); 
			}
		}

		//echo $rs_goods_name;
	}

	if ($mode == "U") {

		$stock_code_name = getDcodeName($conn,"OUT_ST",$stock_code);
		$arr_company = selectCompany($conn, $cp_type);
		$cp_code = trim($arr_company[0]["CP_CODE"]); 
		
		$in_loc_name = getDcodeName($conn,"LOC",$in_loc);

		$pay_date = "";

		$result = updateTempStock($conn, $stock_type, $stock_code_name, $goods_no, $goods_code, $goods_name, $cp_type, $cp_code, $txt_cp_type, $in_loc_name, $in_loc_ext, $qty, $price, $in_date, $pay_date, $temp_no, $stock_no);
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
		//location.href =  "in_list.php<?=$strParam?>";
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
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
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
<script>
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
				frm.action = "/manager/goods/search_goods.php";
				frm.target = "ifr_hidden";
				frm.submit();
			} else {
				hide('suggest');
			}
		}
		setTimeout("sendKeyword();", 100);
	}

	function searchKeyword() {
		var frm = document.frm;
		var keyword = document.frm.search_name.value;
		frm.keyword.value = keyword;
		frm.action = "/manager/goods/search_goods.php";
		frm.target = "ifr_hidden";
		frm.submit();
	}

	function displayResult(str) {

//		if (httpRequest.readyState == 4) {
//			if (httpRequest.status == 200) {
				
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

		//alert(arr_keywordValues[0]); // 상품명
		//alert(arr_keywordValues[1]); // 상품번호
		//alert(arr_keywordValues[2]); // 공급가
		//alert(arr_keywordValues[3]); // 판매가
		//alert(arr_keywordValues[4]); // 상품코드 (경박)
		//alert(arr_keywordValues[5]); // 업체번호
		//alert(arr_keywordValues[6]); // 업체이름 [업체코드 (경박)]

		frm.goods_name.value					= arr_keywordValues[0];
		frm.goods_no.value						= arr_keywordValues[1];
		frm.price.value								= arr_keywordValues[2];
		frm.goods_code.value					= arr_keywordValues[4];
		//frm.cp_type.value							= arr_keywordValues[5];
		//frm.txt_cp_type.value					= arr_keywordValues[6];

		//frm.goods_sale_price.value		= arr_keywordValues[3];

		loopSendKeyword = false;
		checkFirst = false;
		hide('suggest');

		//document.getElementById('goods_detail').src = "order_goods_detail.php?goods_no="+frm.goods_no.value+"&cp_no=<?=$cp_no?>&mode=S";

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

	// 저장 버튼 클릭 시 
	function js_save() {
		
		var stock_no = "<?= $stock_no ?>";
		var frm = document.frm;

		if (isNull(frm.goods_no.value)) {
			alert('상품을 선택해주세요.');
			frm.goods_no.focus();
			return ;		
		}

		if (isNull(frm.stock_code.value)) {
			alert('출고구분을 선택해주세요.');
			frm.stock_code.focus();
			return ;		
		}

		if (isNull(frm.cp_type.value)) {
			alert('출고업체를 선택해주세요.');
			frm.cp_type.focus();
			return ;		
		}

		if (isNull(frm.qty.value)) {
			alert('수량을 입력해주세요.');
			frm.qty.focus();
			return ;		
		}

		if (isNull(frm.price.value)) {
			alert('매출가를 입력해주세요.');
			frm.price.focus();
			return ;		
		}

		if (isNull(frm.in_loc.value)) {
			alert('사유를 선택해주세요.');
			frm.in_loc.focus();
			return ;		
		}
		

		if (isNull(frm.in_date.value)) {
			alert('출고일을 입력해주세요.');
			frm.in_date.focus();
			return;
		}

		
		frm.mode.value = "U";

		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

</script>
</head>
<body id="popup_file">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="temp_no" value="<?= $temp_no?>">
<input type="hidden" name="stock_no" value="<?=$stock_no?>" />
<input type="hidden" name="stock_type" value="<?=$rs_stock_type?>" />
<input type="hidden" name="goods_name" value="<?=$rs_goods_name?>">
<input type="hidden" name="goods_code" value="<?=$rs_goods_code?>">
<input type="hidden" name="goods_no" value="<?=$rs_goods_no?>">
<input type="hidden" name="keyword" value="">
<input type="hidden" name="goods_type" value="unit">
<div id="popupwrap_file">
	<h1>출고 등록 수정</h1>
	<div id="postsch">
		<h2>* 출고 정보를 수정 합니다.</h2>
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
						<th>상품검색</th>
						<td colspan="5" style="position:relative" class="line">
							<div id="suggest" style="position:absolute; left:15px; top:25px; width:95%; height:81px; z-index:100; visibility: hidden; width:95%; ">
								<div id="suggestList" style="position:relative; border:solid 1px #cec6ba; background:#FFFFFF; width:95%;"></div>
							</div>
							<input type="text" class="txt" style="width:95%; ime-mode:Active;" name="search_name" required value="<?=$rs_goods_name?>" onKeyDown="startSuggest();" />
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>출고구분</th>
						<td style="position:relative" class="line">
							<?= makeSelectBoxAsName($conn, 'OUT_ST','stock_code',"125", "선택", "", $rs_stock_code);?>
						</td>
						<th>출고업체</th>
						<td style="position:relative" class="line">
							<?=makeCompanySelectBoxWithName($conn, 'cp_type', '', $rs_cp_no)?>
						</td>
					</tr>
					<tr>
						<th>출고수량</th>
						<td style="position:relative" class="line">
							<input type="Text" name="qty" value="<?= $rs_qty ?>" style="width:70px;" required onkeyup="return isNumber(this)" class="txt">
						</td>
						<th>출고단가</th>
						<td style="position:relative" class="line">
							<input type="Text" name="price" value="<?= $rs_price?>" style="width:120px;" itemname="가격" required onkeyup="return isNumber(this)" class="txt">
						</td>
					</tr>
					<tr>
						<th>출고 사유</th>
						<td style="position:relative" class="line">
							<?= makeSelectBoxAsName($conn, 'LOC','in_loc',"125", "선택", "", $rs_in_loc);?>
						</td>
						<th>사유 상세</th>
						<td style="position:relative" class="line">
							<input type="Text" name="in_loc_ext" value="<?= $rs_in_loc_ext?>" style="width:120px;" itemname="사유 상세" class="txt">
						</td>
					</tr>
					<tr>
						<th>촐고일</th>
						<td colspan="5" style="position:relative" class="line">
							<input type="Text" name="in_date" value="<?= left($rs_in_date,10)?>" style="width:100px; margin-right:3px;" itemname="입고일" required class="txt datepicker">
						</td>

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
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>