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
	$menu_right = "ST005"; // �޴����� ���� �� �־�� �մϴ�

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

#====================================================================
# Request Parameter
#====================================================================

	$order_goods_no		= trim($order_goods_no);
	$cp_type			= trim($cp_type);
	$buy_cp_no			= trim($buy_cp_no);

	if ($mode == "U") {

		$result = updateOrderBuyCompany($conn, $order_goods_no, $cp_type);

		if($result) { 
?>
<script language="javascript">
		window.opener.js_reload();
		alert('���� �Ǿ����ϴ�.');
		self.close();
</script>
<?
		} else { 
?>
<script language="javascript">
		alert('�˼� ���� ������ �����Ͽ����ϴ�. �����ڿ� �������ּ���.');
		self.close();
</script>
<?

		}
	}

#===============================================================
# Get Search list count
#===============================================================

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />

<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script language="javascript">

	function js_save() { 

		var frm = document.frm;

		if(frm.cp_type.value == "") { 
			alert('������ ��ü�� �������ּ���.');
			frm.txt_cp_type.focus();
		}

		if(confirm('���� ��ü�� �����Ͻðڽ��ϱ�? ���� �� ����� ��ġ�ؾ� �մϴ�.')) {  
		
			frm.mode.value = "U";
			frm.target = "";
			frm.method = "post";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}
	
</script>
</head>

<body id="popup_file">

<div id="popupwrap_file">
	<h1>���� ��ü ����</h1>
	<div id="postsch_file">

		<form name="frm" method="post">
		<input type="hidden" name="mode" value="">
		<input type="hidden" name="order_goods_no" value="<?=$order_goods_no?>">

		<h2>* ���� ��ü �Է�</h2>
		
		<table cellpadding="0" cellspacing="0" style="width:100%;" class="colstable02">
		<colgroup>
			<col width="10%" />
			<col width="30%" />
			<col width="10%" />
			<col width="30%" />
			<col width="*" />
		</colgroup>
		<tbody>
			<tr>
				<th>���� ��ü��</th>
				<td class="line" colspan="3">
					<?=getCompanyName($conn, $buy_cp_no)?>
				</td>
			</tr>
			<tr>
				<th>���� ��ü��</th>
				<td class="line" colspan="3">
					<input type="text" style="width:90%" placeholder="��ü(��/�ڵ�) �Է��� ���͸� �����ּ���" name="txt_cp_type" value="" />
					<input type="hidden" name="cp_type" value="">

					<script>
					
						$(function(){

							$("input[name=txt_cp_type]").keydown(function(e){

								if(e.keyCode==13) { 
									
									//�ڵ� postback ����
									e.preventDefault();

									var keyword = $(this).val();
									if(keyword == "") { 
										$("input[name=cp_type]").val('');
									} else { 
										$.getJSON( "../company/json_company_list.php?term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE,MEMO", function(data) {
											if(data.length == 1) { 
												
												js_selecting_company("txt_cp_type", data[0].label, "cp_type", data[0].id);

											} else if(data.length > 1){ 
												NewWindow("../company/pop_company_searched_list.php?search_str="+keyword + "&target_name=txt_cp_type&target_value=cp_type",'pop_company_searched_list','950','650','YES');

											} else 
												alert("�˻������ �����ϴ�.");
										});
									}
								} 

							});

							$("input[name=txt_cp_type]").keyup(function(e){
								var keyword = $(this).val();

								if(keyword == "") { 
									$("input[name=cp_type]").val('');
								}
							});

						});

						function js_selecting_company(target_name, cp_nm, target_value, cp_no) {
							
							$(function(){

								$("input[name="+target_name+"]").val(cp_nm);
								$("input[name="+target_value+"]").val(cp_no);
								
							});

						}
					</script>
				</td>
			</tr>
		</tbody>
		</table> 
	<div class="sp10"></div>
	<div class="btn">
	<? if ($sPageRight_U == "Y") {?>
		<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="���" /></a>
	<? } ?>
	</div>
	<div class="sp35"></div>


<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</div>
<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="�ݱ�" /></a></div>
</div>

</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>