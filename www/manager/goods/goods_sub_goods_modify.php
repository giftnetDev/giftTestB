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
	$menu_right = "GD004"; // �޴����� ���� �� �־�� �մϴ�

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
#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);
	
	$result	= false  ;

#====================================================================
# DML Process
#====================================================================

	if($mode == "" && count($chk_no) == 0) {
?>
<script language="javascript">
		alert('���õ� ��ǰ�� �����ϴ�. üũ�ڽ��� �����Ͻ� ��ǰ�� ����Ʈ���� ���� �������ּ���.');
		self.close();
</script>
<?
		exit;
	}


	if ($mode == "U") {
		$row_cnt = count($hid_chk_no);
		for ($k = 0; $k < $row_cnt; $k++) {
			$str_goods_no = $hid_chk_no[$k];

			$result = updateGoodsSubBatch($conn, $str_goods_no, $hid_pre_goods_no, $hid_next_goods_no);
		}
	}


	if ($result) {
		$strParam = $strParam."?nPage=".$nPage."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&con_cp_type=".$con_cp_type;
		
	if ($mode == "U") {
?>	
<script language="javascript">
		alert('���� ó�� �Ǿ����ϴ�.');
		self.close();
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
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../../_common/SE2.1.1.8141/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script language="javascript">

	// ���� ��ư Ŭ�� �� 
	function js_save() {
		
		var frm = document.frm;

		// ��ũ��Ʈ ���� �κ�
		frm.mode.value = "U";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}


</script>
</head>
<body id="popup_file">

<form name="frm" method="post">
<input type="hidden" name="mode" value=""/>
<?
	if($chk_no != null)
	{
		$postvalue = "";
		foreach ($chk_no as $goods_no) {
		  $postvalue .= '<input type="hidden" name="hid_chk_no[]" value="' .$goods_no. '" />';
		}
		echo $postvalue;
	}
?>

<div id="popupwrap_file">
	<h1>��ǰ ����ǰ ����</h1>
	<div id="postsch">
		<h2>* ��ǰ ����ǰ�� �ϰ� ���� �մϴ�.<br>
			- ������ ����ǰ�� ���� �� Ȯ���� Ŭ���ϸ� �ش� ����ǰ�� �ϰ� ���� �˴ϴ�.
		</h2>
		<div class="addr_inp">

			<table cellpadding="0" cellspacing="0" class="colstable02">

				<colgroup>
					<col width="20%" />
					<col width="35%" />
					<col width="10%" />
					<col width="35%" />
				</colgroup>
				<thead>
					<tr>
						<th>���� �� ����ǰ ����</th>
						<td colspan="3" class="line">
							<input type="text" class="txt" style="width:90%" name="txt_pre_goods_no" />
							<script>
							$(function() {
								$( "input[name=txt_pre_goods_no]" ).autocomplete({
									source: function( request, response ) {
										$.getJSON( "../goods/json_goods_list.php?category=01", request, function( data, status, xhr ) {
											response(data);
										});
									},
									minLength: 2,
									select: function( event, ui ) {
										$("input[name=txt_pre_goods_no]").val(ui.item.value);
										$("input[name=hid_pre_goods_no]").val(ui.item.id);
									}
								}).data('ui-autocomplete')._renderItem = function( ul, item ) {
										var pic_path = "<img src='" + item.label.split("|")[0] + "' width='50' height='50' border='0'/>";  
										return $( "<li></li>" )
										.data( "item.autocomplete", item )
										.append("<a>" + pic_path +  item.label.split("|")[1]  + "</a>")
										.appendTo( ul );
								};
							//		.bind( "focus", function( event ) {
							//			$(this).val('');
							//			$("input[name=cp_type]").val('');
							//	});
							});
							</script>
							<input type="hidden" name="hid_pre_goods_no"/>
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>���� �� ����ǰ ����</th>
						<td colspan="3" class="line">
							<input type="text" class="txt" style="width:90%" name="txt_next_goods_no" />
							<script>
							$(function() {
								$( "input[name=txt_next_goods_no]" ).autocomplete({
									source: function( request, response ) {
										$.getJSON( "../goods/json_goods_list.php?category=01", request, function( data, status, xhr ) {
											response(data);
										});
									},
									minLength: 2,
									select: function( event, ui ) {
										$("input[name=txt_next_goods_no]").val(ui.item.value);
										$("input[name=hid_next_goods_no]").val(ui.item.id);
									}
								}).data('ui-autocomplete')._renderItem = function( ul, item ) {
										var pic_path = "<img src='" + item.label.split("|")[0] + "' width='50' height='50' border='0'/>";  
										return $( "<li></li>" )
										.data( "item.autocomplete", item )
										.append("<a>" + pic_path +  item.label.split("|")[1]  + "</a>")
										.appendTo( ul );
								};
							//		.bind( "focus", function( event ) {
							//			$(this).val('');
							//			$("input[name=cp_type]").val('');
							//	});
							});
							</script>
							<input type="hidden" name="hid_next_goods_no"/>
						</td>
					</tr>
				</tbody>
			</table>
				
		</div>
		<div class="btn">
		<? if ($adm_no <> "" ) {?>
			<? if ($sPageRight_U == "Y") {?>
      <a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="Ȯ��" /></a>
			<? } ?>
		<? } else {?>
			<? if ($sPageRight_I == "Y") {?>
      <a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="Ȯ��" /></a>
			<? } ?>
		<? }?>
		</div>

	</div>
	<br />
	<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="�ݱ�" /></a></div>
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