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
	$menu_right = "GD005"; // 메뉴마다 셋팅 해 주어야 합니다

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

#====================================================================
# DML Process
#====================================================================

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->

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
				if(arr_keywordList[8] == "품절")
					html += "<td>" +"<a href=\"javascript:js_select_with_alert('"+ arr_keywordList[0]+"','"+arr_keywordList[1]+"')\">"+arr_keywordList[1]+"</a>" + "</td>";
				else if(arr_keywordList[8] == "판매중")
					html += "<td>" +"<a href=\"javascript:js_select('"+ arr_keywordList[0]+"','"+arr_keywordList[1]+"')\">"+arr_keywordList[1]+"</a>" + "</td>";
				else //단종등 기타 제외
					html += "<td style='color:gray;'>" + arr_keywordList[1] + "</td>";
					
				html += "<td width='55px'>"+arr_keywordList[8]+"</td>";
				html += "<td width='105px'>판매가 : "+arr_keywordList[3]+"</td>";
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

		frm.goods_name.value					= arr_keywordValues[0];
		frm.goods_no.value						= arr_keywordValues[1];
		
		loopSendKeyword = false;
		checkFirst = false;
		hide('suggest');

		document.getElementById('goods_detail').src = "order_goods_detail.php?goods_no="+frm.goods_no.value+"&cp_no=<?=$cp_no?>&mode=S&reserve_no=" + frm.reserve_no.value;

	}

	function js_select_with_alert(selectedKey,selectedKeyword) {
		
		if(confirm("품절상태입니다. 그래도 진행 하시겠습니까? \r\n진행하시겠다면 지원 담당자에게 '꼭' 알려주세요."))
			js_select(selectedKey,selectedKeyword);
		else
			return;

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

	function js_reload_list(keep) {
		
		var frm = document.frm;

		if (keep == "STOP") {
			opener.location.reload();
			self.close();
		} else {
			opener.location.reload();
			frm.search_name.value = "";
			frm.goods_no.value = "";
			frm.goods_name.value = "";

		}
	}

</script>
</head>
<body id="popup_file">

<form name="frm" method="post">
<input type="hidden" name="rn" value="" />
<input type="hidden" name="reserve_no" value="<?=$reserve_no?>" />
<input type="hidden" name="goods_no" value="<?=$goods_no?>" />

<input type="hidden" name="cp_no" value="<?=$cp_no?>">
<input type="hidden" name="send_data" value="">
<input type="hidden" name="keyword" value="">

<div id="popupwrap_file">
	<h1>주문 상품 추가</h1>
	<div id="postsch_code">
		<h2>* 주문할 상품을 검색 합니다.</h2>
		<div class="addr_inp">

		<table cellpadding="0" cellspacing="0" width="98%">
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0" width="100%" class="colstable02" border="0">

					<colgroup>
						<col width="14%" />
						<col width="20%" />
						<col width="13%" />
						<col width="20%" />
						<col width="13%" />
						<col width="20%" />
					</colgroup>
					<tbody>
						<tr>
							<th>상품검색</th>
							<td colspan="5" style="position:relative" class="line">
								<div id="suggest" style="position:absolute; left:15px; top:25px; width:95%; height:81px; z-index:0; visibility: hidden; width:95%; ">
									<div id="suggestList" style="height:500px; overflow-y:auto; position:relative; border:solid 1px #cec6ba; background:#FFFFFF; width:95%;"></div>
								</div>
								<input type="text" class="txt" style="width:95%; ime-mode:Active;" autocomplete="off" name="search_name" required value="" onKeyDown="startSuggest();" />
							</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="10"></td>
						</tr>
					</tfoot>
					</table>
				</td>
			</tr>
		</table>
		<input type="hidden" name="goods_name" value="" />

		<iframe name='goods_detail' id='goods_detail' onload="window.parent.parent.scrollTo(0,0)" width='100%' height='1250' noresize scrolling='yes' frameborder='0' marginheight='0' marginwidth='0' src="about:blank"></iframe>
	</div>
	<div class="sp20"></div>
</div>
	<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
</div>
<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>
<?
#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);
?>