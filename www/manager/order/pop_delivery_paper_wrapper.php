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

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";


#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";

#====================================================================
# Request Parameter
#====================================================================

	$delivery_cp				= trim($delivery_cp);
	$delivery_no				= trim($delivery_no);
	
#===============================================================
# Get Search list count
#===============================================================

	$trace = getDeliveryUrl($conn, $delivery_cp);
	$trace = $trace.$delivery_no;


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript">

	function js_search() {
		var frm = document.frm;
		
		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

</script>
</head>

<body id="popup_order_wide">

<div id="popupwrap_order_wide">
	<h1>택배 송장 조회</h1>
	<div id="postsch_code">

		<div class="addr_inp">

<form name="frm" method="post">

	<table cellpadding="0" cellspacing="0" width="100%" class="colstable02">

	<colgroup>
		<col width="10%" />
		<col width="35%" />
		<col width="10%" />
		<col width="35%" />
		<col width="*" />
	</colgroup>
	<tr>
		<th>택배사</th>
		<td class="line">
			<?=makeSelectBoxOnChange($conn,"DELIVERY_CP", "delivery_cp","120", "택배사 선택", "", $delivery_cp)?>
		</td>
		<th>송장번호</th>
		<td class="line">
			 <input type="text" name="delivery_no" value="<?=$delivery_no?>"/>
		</td>
		<td class="line">
			<input type="button" name="bb" onclick="js_search();" value="조회"/>
		</td>
	</tr>
	</table>
	<div class="sp20"></div>
	<!--<iframe width="875px" height="650px" id="delivery" src=""></iframe>-->
	*. HTTP -> HTTPS 로 보안상 이전함에 따라 택배 조회가 새 창에서 열립니다.
<script type="text/javascript">
	NewWindow('<?=$trace?>', "delivery_blank", 800, 600, 'yes');
	<?echo "console.log('<?=$trace?>');";?>
</script>
</form>
<div class="bot_close" style="width:auto;"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
</div>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>