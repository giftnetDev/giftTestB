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
	$menu_right = "CP004"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/stock/stock.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/confirm/confirm.php";
	require "../../_classes/biz/company/company.php";


#====================================================================
# Request Parameter
#====================================================================

	if ($mode == "I") {

		$result = insertCompanyManager($conn, $cp_no, $manager_nm, $s_adm_no);

		if($result) { 
?>
<script language="javascript">
		alert("저장 되었습니다.");
		parent.opener.location.reload();
		window.location.replace("/manager/company/company_manager_pop.php?cp_no=<?=$cp_no?>");
</script>
<?
		}
		exit();
	}

	if ($mode == "D") {
		$row_cnt = count($chk_no);

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_managerno = $chk_no[$k];
			$result = deleteCompanyManager($conn, $cp_no, $str_managerno, $s_adm_no);
		}

		if($result) { 
?>
<script type="text/javascript">
		alert('정상 삭제 되었습니다.');
		parent.opener.location.reload();
		window.location.replace("/manager/company/company_manager_pop.php?cp_no=<?=$cp_no?>");
</script>
<?
		}
	}

	$arr_manager = listManager($conn, $cp_no);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />

<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script>

<script language="javascript">

	function js_all_check() {
		var frm = document.frm;
		
		if (frm['chk_no[]'] != null) {
			
			if (frm['chk_no[]'].length != null) {

				if (frm.all_chk.checked == true) {
					for (i = 0; i < frm['chk_no[]'].length; i++) {
						frm['chk_no[]'][i].checked = true;
					}
				} else {
					for (i = 0; i < frm['chk_no[]'].length; i++) {
						frm['chk_no[]'][i].checked = false;
					}
				}
			} else {
			
				if (frm.all_chk.checked == true) {
					frm['chk_no[]'].checked = true;
				} else {
					frm['chk_no[]'].checked = false;
				}
			}
		}
	}

	function js_search()
	{
		var frm = document.frm;

		frm.mode.value = "S";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	function js_save() 
	{ 

		var frm = document.frm;
		
		if (isNull(frm.manager_nm.value)) {
			alert('담당자명을 입력해주세요.');
			frm.manager_nm.focus();
			return ;		
		}

		if(!confirm('담당자를 추가 하시겠습니까?')) return;
		{
			frm.mode.value = "I";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}

	}

	function js_delete() 
	{ 

		var frm = document.frm;
		var selected_cnt = $("input[name='chk_no[]']:checked").length;

		//alert("======selected_cnt=="+selected_cnt);		
		if(selected_cnt == 0) 
		{
			alert('선택된 데이터가 없습니다');
			return;
		}

		if (confirm('삭제 하시겠습니까?'))
		{
			frm.mode.value = "D";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}

	}
</script>
</head>
<style>
body#popup_order_wide {width:100%;}
.delivered {background-color:#EFEFEF;}
tr.not_used > td {color: #A2A2A2;}
.color_yellow{background-color:yellow;}
</style>
<body id="popup_order_wide">

<div id="popupwrap_order_wide">
	<h1>업체 담당자 추가</h1>
	<div id="postsch_code">

		<div class="addr_inp">

<form name="frm" method="post" enctype="multipart/form-data" action="javascript:js_search();">
<input type="hidden" name="mode" value="">
<input type="hidden" name="cp_no" value="<?=$cp_no?>">
	
		<table cellpadding="0" cellspacing="0" width="100%" class="colstable02">
		<colgroup>
			<col width="24%" />
			<col width="76%" />
		</colgroup>
		<tbody>
			<tr>
				<th>담당자명</th>
				<td><input type="text" name="manager_nm" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_save();">				
				<input type="button" name="del_bt" value="추가 " class="btntxt" onclick="js_save();">
				</td>
			</tr>
		</tbody>
		</table>
	
	
		<div class="sp10"></div>
		<table cellpadding="0" cellspacing="0" width="100%" class="rowstable">
		<colgroup>
			<col width="15%" />
			<col width="15%" />			
			<col width="70%" />
		</colgroup>
		<thead>
			<tr>
				<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
				<th>No</th>
				<th>담당자</th>
			</tr>
		</thead>
		<tbody>
		
		<?
		//echo"====$arr_manager===="+sizeof($arr_manager);
		if(sizeof($arr_manager) >= 1) {
			for($i = 0; $i < sizeof($arr_manager); $i ++) { 

				$RN				= trim($arr_manager[$i]["RN"]);
				$MANAGER_NO		= trim($arr_manager[$i]["MANAGER_NO"]); 
				$MANAGER_NM		= trim($arr_manager[$i]["MANAGER_NM"]); 

		?>

		
		<tr height="30" data-ref_no='<?=$MANAGER_NO?>'>
			<td><input type="checkbox" name="chk_no[]" class="chk" value="<?=$MANAGER_NO?>"/></td>
			<td><?=$RN?></td>
			<td class="modeual_nm"><?=$MANAGER_NM?></td>
		</tr>
		
		<?
			}
		} else {

		?>
		<tr>
			<td colspan="3" height="40" align="center">데이터가 없습니다</td>
		</tr>
		<?

		}
		
		?>
		</tbody>
		</table>
		<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">
			<input type="button" name="del_bt" value="삭제 " class="btntxt" onclick="js_delete();">
		</div>

		</td>
	  </tr>
	  </table>
	  <div class="sp30"></div>
</div>

<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
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