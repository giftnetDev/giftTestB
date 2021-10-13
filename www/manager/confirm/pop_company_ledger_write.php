<?session_start();?>
<?

#=========================================================================
# 잔액실사 정정 / 주문없는 추가기장용
#=========================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "CF006"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/confirm/confirm.php";

#====================================================================
# Request Parameter
#====================================================================
	$mode			= trim($mode);

	$strParam = "?start_date=".$start_date."&end_date=".$end_date."&cp_type=".$cp_type;
	
	$inout_date				= SetStringToDB($inout_date);
	$balance				= SetStringToDB($balance);
	
	$result	= false  ;

#====================================================================
# DML Process
#====================================================================
	if ($mode == "I") {

		$balance = str_replace(",", "", $balance);

		$result	= insertCompanyLedgerBalance($conn, $cp_no, $inout_date, $balance, $memo, $s_adm_no);

if($result) { 
?>
<script type="text/javascript">
	
	self.close();
	alert('처리되었습니다.');
	opener.parent.js_search();

</script>
<?
} else { 
?>
<script type="text/javascript">
	alert('잔액과 입력액이 같거나 입력오류입니다 다시 확인해주세요.');
	self.close();
</script>
<?
}
		mysql_close($conn);
		exit;
	}



	$RS_INOUT_DATE			= date("Y-m-d",strtotime("0 month"));
	$RS_CP_NO = $cp_type;
	

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
      changeYear: true,
	  beforeShow: function() {
        setTimeout(function(){
            $('.ui-datepicker').css('z-index', 99999999999999);
        }, 0);
	  }
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

	// 저장 버튼 클릭 시 
	function js_save() {
		
		var frm = document.frm;
		frm.mode.value = "I";

		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

</script>
<!--
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
				if(arr_keywordList[8] != "판매중")
					html += "<td style='color:gray;'>" + arr_keywordList[1] + "</td>";
				else
					html += "<td>" +"<a href=\"javascript:js_select('"+ arr_keywordList[0]+"','"+arr_keywordList[1]+"')\">"+arr_keywordList[1]+"</a>" + "</td>";
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

		frm.search_name.value					= arr_keywordValues[0];
		frm.goods_no.value						= arr_keywordValues[1];
		
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
-->
</head>
<body id="popup_file">

<form name="frm" method="post" enctype="multipart/form-data">

	<input type="hidden" name="mode" value="<? if($mode == "APPEND") { echo "APPEND"; } ?>">
	<input type="hidden" name="cl_no" value="<?=$cl_no?>">
	<input type="hidden" name="start_date" value="<?=$start_date?>">
	<input type="hidden" name="end_date" value="<?=$end_date?>">
	<input type="hidden" name="cp_type" value="<?=$cp_type?>">

<div id="popupwrap_file">
	<h1>잔액 실사 입력</h1>  
	<div id="postsch">
		<div class="addr_inp">
			<br/>
			<table cellpadding="0" cellspacing="0" class="colstable02">
			<colgroup>
				<col width="16%">
				<col width="34%">
				<col width="16%">
				<col width="34%">
			</colgroup>
				<tr>
					<th>업체</th>
					<td class="line">
						<?=getCompanyName($conn, $RS_CP_NO)?>
						<input type="hidden" name="cp_no" value="<?=$RS_CP_NO?>">
					</td>
					<th>잔액</th>
					<td class="line">
						<span class="get_balance" data-cp_no="<?=$RS_CP_NO?>">클릭해주세요</span>
						<script>
							$(function(){
								$(".get_balance").click(function(){
									var cp_no = $(this).data("cp_no");
									var clicked_obj = $(this);

									$.getJSON( "../confirm/json_company_ledger.php?cp_no=" + encodeURIComponent(cp_no), function(data) {
										if(data != undefined) { 
											if(data.length == 1) 
												clicked_obj.html(numberFormat(data[0].SUM_BALANCE) + " 원");
											else {
												clicked_obj.html("검색결과가 없습니다.");
											}
										}
									});


								});

								$(".get_balance").click();

							});
							</script>
					</td>
				</tr>
			</table>
			<div class="sp10"></div>
	
			<table cellpadding="0" cellspacing="0" class="colstable02">
				<colgroup>
					<col width="16%">
					<col width="34%">
					<col width="16%">
					<col width="34%">
				</colgroup>
				<tr>
					<th>실사기장일</th>
					<td class="line">
						<input type="Text" name="inout_date" value="<?= $RS_INOUT_DATE?>" style="width:80px; margin-right:3px;" class="txt datepicker">
					</td>
					<th>실사금액</th>
					<td class="line">
						<input type="Text" name="balance" value="" style="width:100px;" class="txt">
					</td>
				</tr>
				<!--
				<tr>
					<th>기장구분</th>
					<td class="line" colspan="3">
						
						<input type = 'radio' name= 'inout_type' value='LR01'><label> 매출 </label>
						<input type = 'radio' name= 'inout_type' value='RR03'><label> 매입 </label>
						
						<input type = 'radio' name= 'inout_type' value='BALANCE'> <label> 잔액실사 </label>
						<input type="hidden" name="to_cp_no" value=""/>
					</td>
					
				</tr>
				
				<tr>
					<th>기장명</th> 
					<td colspan="3" class="line">
						<div id="suggest" style="position:absolute; left:15px; top:25px; width:95%; height:81px; z-index:0; visibility: hidden; width:95%; ">
							<div id="suggestList" style="height:500px; overflow-y:auto; position:relative; border:solid 1px #cec6ba; background:#FFFFFF; width:95%;"></div>
						</div>
						<input type="text" class="txt" style="width:95%; ime-mode:Active;" name="search_name" required value=""  placeholder="상품검색 또는 기장명을 넣어주세요" onKeyDown="startSuggest();" />
						<input type="hidden" name="goods_no" value="0"/>
					</td>
					
				</tr>
				
				<tr>
					<th>단가</th>
					<td class="line">
						<input type="Text" name="unit_price" value=""  required onkeyup="return isNumber(this)" style="width:95%;" class="txt calc">
					</td>
					<th>수량</th>
					<td class="line">
						<input type="Text" name="qty" value=""  required onkeyup="return isNumber(this)" style="width:95%;" class="txt calc">
					</td>
				</tr>
				-->
				<tr>
					
					<th>비고</th>
					<td class="line" colspan="3">
						<input type="Text" name="memo" value="" style="width:95%;" class="txt">
					</td>
				</tr>
			</table>
			
		</div>   
		<div class="btn">
			<? if ($sPageRight_I == "Y") {?>
				<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
			<? } ?>
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