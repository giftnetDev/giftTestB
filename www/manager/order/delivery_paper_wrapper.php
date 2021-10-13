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
	$menu_right = "OD022"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/com/etc/etc.php";

#====================================================================
# Request Parameter
#====================================================================

	$delivery_cp				= trim($delivery_cp);
	$delivery_no				= trim($delivery_no);

	$delivery_no = str_replace('-', '', $delivery_no);
	
#===============================================================
# Get Search list count
#===============================================================

	$trace = getDeliveryUrl($conn, $delivery_cp);
	$trace = $trace.$delivery_no;


?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
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

<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<div id="adminwrap">

<?
	#====================================================================
	# common top_area
	#====================================================================

	require "../../_common/top_area.php";
?>

	<table width="100%" cellpadding="0" cellspacing="0">
	<colgroup>
		<col width="180" />
		<col width="*" />
	</colgroup>
	<tr>
		<td class="leftarea">
<?
	#====================================================================
	# common left_area
	#====================================================================

	require "../../_common/left_area.php";
?>


		</td>
		<td class="contentarea">

			<!-- S: mwidthwrap -->
			<div id="mwidthwrap">

				<h2>택배송장 조회</h2>
				<table cellpadding="0" cellspacing="0" class="colstable">
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
				</script>
				<div class="sp50"></div>
		<!-- // E: mwidthwrap -->
			</div>
			<!-- // E: mwidthwrap -->

		</td>
	</tr>
	</table>
</div>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>