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

	if ($mode == "S") {
		$arr_cp_order_no = selectOrderNoByDeliveryNo($conn, $search_text); 
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
	function js_search() {
		
		var frm = document.frm;

		frm.mode.value = "S";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	function setParentWindowValue(reserve_no) { 

		opener.document.getElementsByName("reserve_no")[0].value = reserve_no;
		self.close();
	}

	
</script>
</head>
<body id="popup_file">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="" />
<div id="popupwrap_file">
	<h1>주문번호 검색</h1>
	<div id="postsch">
		<h2>* 송장번호를 통해 주문번호를 검색합니다.</h2>
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
					<td colspan="3" class="line">
						<input type="text" class="txt" style="width:80%;" name="search_text" value="<?=$search_text?>" />
					</td>
				</tr>
			</table>
			<div class="btn">
			  <a href="javascript:js_search();"><img src="../images/admin/btn_confirm.gif" alt="확인"></a>
			  <a href="javascript:window.close();"><img src="../images/admin/btn_cancel.gif" alt="취소" /></a>
			</div>      
		</div>

		<h2>* 검색된 주문번호.</h2>
		<div class="sp20"></div>
		<div class="addr_inp">
			<table cellpadding="0" cellspacing="0" class="colstable02">
				<colgroup>
					<col width="15%" />
					<col width="35%" />
					<col width="15%" />
					<col width="35%" />
				</colgroup>
				<? if(sizeof($arr_cp_order_no) > 0) { ?>
				<tr>
					<th>주문번호</th>
					<td colspan="3" class="line">
					<?
						
						for($i = 0; $i < sizeof($arr_cp_order_no); $i++) {
						$cp_order_no = $arr_cp_order_no[$i]["CP_ORDER_NO"];
					?>
						<a href="javascript:setParentWindowValue('<?=$cp_order_no?>')"><?=$cp_order_no?></a><br/>
					<?
						}
					
					?>
					</td>
				</tr>
				<? } else { ?>
				<tr>
					<th>주문번호</th>
					<td colspan="3" class="line">
						검색결과가 없습니다
					</td>
				</tr>
				<? } ?>
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