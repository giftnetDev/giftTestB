<?session_start();?>
<?
# =============================================================================
# File Name    : pop_barcode_reader.php
# Modlue       : 
# Writer       : Sungwook Min
# Create Date  : 2015-11-05
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
	$menu_right = "OD015"; // 메뉴마다 셋팅 해 주어야 합니다

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
#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);

	if($mode == "I")
	{
		for($i = 0; $i < sizeof($codes); $i++)
		{
			echo $codes[$i]."<br/>";

		}
	}

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
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script>
	function js_save() {
		
		var frm = document.frm;

		frm.mode.value = "I";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	
</script>
</head>
<body id="popup_file" onload="document.getElementsByName('search_text')[0].focus();">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="" />
<div id="popupwrap_file">
	<h1>바코드입력</h1>
	<div id="postsch">
		<h2>* 바코드 입력합니다.</h2>
		<div class="addr_inp">
			<table cellpadding="0" cellspacing="0" class="colstable02">
				<colgroup>
					<col width="20%" />
					<col width="35%" />
					<col width="15%" />
					<col width="*%" />
				</colgroup>
				<tr>
					<th>송장번호</th>
					<td class="line">
						<input type="text" class="txt" style="width:80%;" name="search_text" value="" />

						<script>
							$(function(){
								var num = 0;
								$(".total_num").html(num);
								$("input[name=search_text]").keydown(function(event){
									var code = $("input[name=search_text]").val();
									if(event.keyCode == 13)
									{
										num++;

										$(".code_list").append("<tr class='code_"+code+"'><th>"+num+"</th><td colspan='2'><span>"+code+"</span><input type='hidden' name='codes[]' value='"+code+"'/></td><td><span class='cancel' style='color:#478fb2; cursor:pointer; font-weight:bold; text-decoration:underline;'>삭제</span></td></tr>");
										$("input[name=search_text]").val('');
										
										$(".total_num").html(num);
										event.preventDefault();
									}
								});

								$(function(){
									$('body').on('click', '.cancel', function() {
										$(this).closest("tr").remove();
										num--;
										$(".total_num").html(num);
									});
								});
								

							});
						</script>



					</td>
					<td colspan="2" class="line">
						
					</td>
				</tr>
			</table>
			<div class="btn">
			  <a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인"></a>
			  <a href="javascript:window.close();"><img src="../images/admin/btn_cancel.gif" alt="취소" /></a>
			</div>      
		</div>

		<h2>* 체크된 리스트. </h2>
		<div class="sp10"></div>
		총 스캔 수량 : <span class="total_num"></span>
		<div class="addr_inp">
			<table cellpadding="0" cellspacing="0" class="colstable02">
				<colgroup>
					<col width="20%" />
					<col width="*" />
					<col width="20%" />
				</colgroup>
				<tbody class="code_list">
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