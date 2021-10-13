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
	$menu_right = "SG025"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/stock/stock.php";

#====================================================================
# Request Parameter
#====================================================================

	$mode = trim($mode);
	$goods_code = trim($goods_code);
	$result = false;

	if($mode == "I") { 
		
		$inout_date = date("Y-m-d",strtotime("0 day"));

		for ($k = 0; $k < sizeof($sub_goods_id); $k++) {

			$stock_code			= $sub_stock_code[$k];
			$goods_no			= $sub_goods_id[$k];
			$delivery_cnt_in_box= $sub_delivery_cnt_in_box[$k];
			$pallet_cnt			= $sub_pallet_cnt[$k];
			$box_cnt			= $sub_goods_cnt[$k];
			

			$total_qty		= $delivery_cnt_in_box * $pallet_cnt * $box_cnt;
			$total_box_qty	= $pallet_cnt * $box_cnt;

			$memo = $pallet_cnt. " 파렛 X ".$box_cnt." 박스 = 총 ".number_format($total_box_qty)."박스(".number_format($total_qty)."개)";

			$result = insertEachStock($conn, $warehouse_code, $inout_type, $stock_code, $goods_no, $delivery_cnt_in_box, $total_box_qty, $inout_date, $memo, $keys, $s_adm_no);

		}

	}

	if($result) {

?>
<script type="text/javascript">
	window.opener.js_search();
	alert("수정 되었습니다.");
	self.close();
</script>
<?
		mysql_close($conn);
		exit;
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title>기프트넷</title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
  
</head>
<script>

	function js_save() {
		var frm = document.frm;

		frm.target = "";
		frm.method = "post";
		frm.mode.value = "I";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

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

				html += "<table width='100%' border='0' class='rowstable'>" + 
					"<tr>" + 
						"<td style='padding:0px 5px 0px 5px' width='55px' rowspan='2'><img src='"+arr_keywordList[2]+"' width='50' height='50' border='0'></td>" + 
						"<td rowspan='2'><a href=\"javascript:js_select('"+ arr_keywordList[0]+"','"+arr_keywordList[1]+"')\">"+ arr_keywordList[1]+"</a></td>" + 
						"<td width='255px' class='modeual_nm'>판매상태 : "+arr_keywordList[8]+"</td>" + 
						"<td width='105px'>박스입수 : "+arr_keywordList[9]+"</td>" + 
					"</tr>" + 
					"</table>";
		
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
        // arr_keywordValues[2]; 공급가
		// arr_keywordValues[3]; 판매가
		/*
		//중복체크 안함
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
		*/

		$(".sub_goods_list").append(
			"<tr>" + 

				 "<th><select name='sub_stock_code[]' style='width:50px'><option value='정상'>정상</option><option value='불량'>불량</option></select></td>" +

				"<td class='line'>" + arr_keywordValues[0] + "["+ arr_keywordValues[1] + "]" + "<input type='hidden' name='sub_goods_id[]' value='" + arr_keywordValues[1] + "'></td>" + 

				"<th>박스<br/>입수</th>" + 
			    "<td class='line'><input type='text' name='sub_delivery_cnt_in_box[]' class='txt' style='width:50px' value='" + arr_keywordValues[11] + "'>개</td>" + 
			    
				"<th>파렛수</th>" + 
			    "<td class='line'><input type='text' name='sub_pallet_cnt[]' class='txt' style='width:50px' value=''></td>" +

				"<th>박스수</th>" + 
			    "<td class='line'><input type='text' name='sub_goods_cnt[]' class='txt' style='width:50px' value=''></td>" +
				
				"<td style='border-underline:1px solid #d2dfe5'><span class='remove_sub' style='color:#478fb2; cursor:pointer; font-weight:bold; text-decoration:underline;'>삭제</span></td>" + 
			"</tr>"); 

		loopSendKeyword = false;
		checkFirst = false;
		hide('suggest');

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
	});
});
</script>

<body id="popup_stock">

<div id="popupwrap_stock">
	<div id="postsch_stock">
		<div class="addr_inp">

<form name="frm" method="post">
<input type="hidden" name="reserve_no" value="<?=$reserve_no?>">
<input type="hidden" name="mode" value="<?=$mode?>">
<input type="hidden" name="warehouse_code" value="<?=$warehouse_code?>">
<input type="hidden" name="inout_type" value="<?=$inout_type?>">
<input type="hidden" name="keyword" value="">

			<div class="sp10"></div>
			<h1><?=getDcodeName($conn, "WAREHOUSE", $warehouse_code)?></h1>

			<table cellpadding="0" cellspacing="0" class="colstable02">
				<colgroup>
					<col width="10%" />
					<col width="35%" />
					<col width="15%" />
					<col width="35%" />
				</colgroup>
				<tbody>
					<tr class="set_goods">
						<th><?=($inout_type=="IN" ? "입고" : "출고")?> 자재 추가</th>
						<td colspan="3" style="position:relative" class="line">
							<div id="suggest" style="position:absolute; left:15px; top:25px; width:95%; height:81px; z-index:0; visibility: hidden; width:98%;">
								<div id="suggestList" style="position:relative; border:solid 1px #cec6ba; background:#FFFFFF; width:98%; height:250px; overflow-y: auto;"></div>
							</div>
							<input type="text" class="txt search_name" style="width:75%; ime-mode:Active;" name="search_name" value="" onKeyDown="startSuggest();" onFocus="this.value='';" />
							<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인"></a>

							<table cellpadding="0" cellspacing="0" class="colstable02" style="margin-top:5px;">
							<colgroup>
								<col width="6%" />
								<col width="*" />
								<col width="6%" />
								<col width="11%" />
								<col width="6%" />
								<col width="11%" />
								<col width="6%" />
								<col width="11%" />
								<col width="4%" />
							</colgroup>
							<thead>
								<tr>
									<th colspan="9" class="line">상품을 검색해서 선택하시면 아래에 자재가 추가됩니다</th>
								</tr>
							</thead>
							<tbody class="sub_goods_list">
							</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
	</div>
</div>
<iframe src="about:blank" name="ifr_hidden" id="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</div>
</body>
</html>

<?
	if($goods_code <> "") { 
?>
<script>
	document.frm.search_name.value = '<?=$goods_code?>';

	loopSendKeyword = true;
	sendKeyword();
	
</script>
<? } ?>

<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>