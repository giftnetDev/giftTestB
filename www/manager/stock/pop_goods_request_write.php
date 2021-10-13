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
	$menu_right = "SG017"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/stock/stock.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/admin/admin.php";
#====================================================================
# Request Parameter
#====================================================================

	$today = date("Y-m-d", strtotime("0 month"));

#====================================================================
# DML Process
#====================================================================

	if($mode == "INSERT_GOODS_REQUEST") { 

		$buy_cp_no = $cp_type;

		$row_cnt = count($sub_goods_id);

		for ($k = 0; $k < $row_cnt; $k++) {

			$goods_no			= $sub_goods_id[$k];
			$buy_price          = $sub_buy_price[$k];
			$req_qty			= $sub_goods_cnt[$k];
			$buy_total_price = $buy_price * $req_qty;

			$result = insertGoodsRequest($conn, $s_adm_com_code, $group_no, $req_date, $buy_cp_no, $goods_no, $buy_price, $req_qty, $buy_total_price, $s_adm_no);
		}

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
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
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
				frm.con_cate_03.value = frm.cp_type.value;
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
				
				html += "<table width='100%' border='0'>";
				html += "<tr>";
				html += "<td style='padding:0px 5px 0px 5px' width='55px'><img src='"+arr_keywordList[2]+"' width='50' height='50' border='0'></td>";
				html += "<td>" +"<a href=\"javascript:js_select('"+ arr_keywordList[0]+"','"+arr_keywordList[1]+"')\">"+arr_keywordList[1]+"</a>" + "</td>";
				html += "<td width='55px'>"+arr_keywordList[8]+"</td>";
				html += "<td width='105px'>공급가 : "+arr_keywordList[7]+"</td>";
				html += "</tr>";
				html += "</table>";
		
				//alert(html);
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

		$(".sub_goods_list").append("<tr><th>상품명</th><td class='line'>" + arr_keywordValues[0] + "["+ arr_keywordValues[1] + "]" + "<input type='hidden' name='sub_goods_id[]' value='" + arr_keywordValues[1] + "'></td><th>공급가</th><td class='line'><input type='text' name='sub_buy_price[]' class='txt' style='width:70%' value='"+arr_keywordValues[2]+"'>원</td><th>수량</th><td class='line'><input type='text' name='sub_goods_cnt[]' class='txt' style='width:70%' value='1'>개</td><td style='border-underline:1px solid #d2dfe5'><span class='remove_sub' style='color:#478fb2; cursor:pointer; font-weight:bold; text-decoration:underline;'>삭제</span></td></tr>");

		loopSendKeyword = false;
		checkFirst = false;
		hide('suggest');

	}

	function show(elementId) {
		var element = document.getElementById(elementId);
		
		if (element) {
			element.style.visibility  ="visible"; 
		}
	}

	function hide(elementId) {
		var element = document.getElementById(elementId);
		if (element) {
			element.style.visibility  ="hidden"; 
		}
	}

$(function(){
	$('body').on('click', '.remove_sub', function() {
		$(this).closest("tr").remove();
		js_calculate_buy_and_sale_price();
	});
});

</script>
<script>
	
	// 조회 버튼 클릭 시 
	function js_save() {
		var frm = document.frm;
		
		frm.target = "";
		frm.method = "post";
		frm.mode.value = "INSERT_GOODS_REQUEST";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

</script>
<style> 
	body#popup_file {width:100%;}
	#popupwrap_file {width:100%;}
	input[type=button] {width:95%; height:50px; margin:10px;}
</style>
</head>
<body id="popup_file">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="keyword" value="" />
<input type="hidden" name="con_cate_03" value="" />
<div id="popupwrap_file">
	<h1>발주 - 전표입력</h1>
	<div id="postsch">
		<h2>* 전표입력</h2>
		<div class="addr_inp">

			<table cellpadding="0" cellspacing="0" class="colstable02">
				<colgroup>
					<col width="20%" />
					<col width="40%" />
					<col width="40%" />
				</colgroup>
				<tr>
					<th>전표번호</th>
					<td colspan="2" class="line">
						<?
							$group_no = cntMaxGroupNoRequest($conn);
						?>
						<span><?=$group_no?></span>
						<input type="hidden" name="group_no" value="<?=$group_no?>" style="width: 100px;"/>
					</td>
				</tr>
				<tr>
					<th>발주일</th>
					<td colspan="2" class="line">
						<input type="text" class="txt datepicker" style="width: 100px; margin-right:3px;" name="req_date" value="<?=$today?>" maxlength="10"/>
					</td>
				</tr>
				<tr>
					<th>주문업체(매입)</th>
					<td colspan="2" class="line">
						<input type="text" class="supplier" style="width:160px" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'구매',$cp_type)?>" placeholder="업체명/코드입력후 선택해주세요" />
						<script>
						$(function() {
						 var cache = {};
							$( ".supplier" ).autocomplete({
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
									$(".supplier").val(ui.item.value);
									$("input[name=cp_type]").val(ui.item.id);
								}
							}).bind( "blur", function( event ) {
								var cp_no = $("input[name=cp_type]").val();
								if(cp_no != '') {
									$.getJSON( "../company/json_company_list.php?cp_no=" + cp_no, function(data) {
										if(data[0].CP_NO == 'undefined') {
											$("input[name=cp_type]").val('');
										} else {
											if(data[0].COMPANY != $(".supplier").val())
											{

												$(".supplier").val();
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
				</tr>
			</table>
			<div class="sp10"></div>
			<table cellpadding="0" cellspacing="0" class="colstable02">
				<colgroup>
					<col width="20%" />
					<col width="80%" />
				</colgroup>
				<tbody>
					
					<tr class="set_goods">
						<th>발주 자재 추가</th>
						<td style="position:relative" class="line">
							<div id="suggest" style="position:absolute; left:15px; top:25px; width:95%; height:81px; z-index:0; visibility: hidden; width:95%; ">
								<div id="suggestList" style="position:relative; border:solid 1px #cec6ba; background:#FFFFFF; width:95%;"></div>
							</div>
							<input type="text" class="txt search_name" style="width:75%; ime-mode:Active;" name="search_name" value="" onKeyDown="startSuggest();" onFocus="this.value='';" />
							<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인"></a>

							<table cellpadding="0" cellspacing="0" class="colstable02" style="margin-top:5px;">
							<colgroup>
								<col width="10%" />
								<col width="*" />
								<col width="10%" />
								<col width="15%" />
								<col width="10%" />
								<col width="15%" />
								<col width="5%" />
							</colgroup>
							<thead>
								<tr>
									<th colspan="7" class="line">상품을 검색해서 선택하시면 아래에 자재가 추가됩니다</th>
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
	<br />
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