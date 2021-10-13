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
	$menu_right = "GD002"; // �޴����� ���� �� �־�� �մϴ�

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
# Request Parameter
#====================================================================
	$mode	= trim($mode);
	$goods_no		= trim($goods_no);

#====================================================================
# DML Process
#====================================================================

	if($mode == "I") { 

		if($goods_no <> 0 && $cate_03 <> 0)
			$result = insertGoodsBuyCompany($conn, $goods_no, $cate_03, $buy_price, $memo, $s_adm_no);
		else 
			$result = false;
	
		if($result) { 
?>
	<script>
		alert('���� �Ǿ����ϴ�.');
		window.location.replace("<?=$_SERVER[PHP_SELF]?>?goods_no=<?=$goods_no?>");
	</script>
<?
		}

	}

	if($mode == "D") { 

		$result = deleteGoodsBuyCompany($conn, $goods_no, $cp_no, $s_adm_no);
	
		if($result) { 
?>
	<script>
		alert('���� �Ǿ����ϴ�.');
		window.location.replace("<?=$_SERVER[PHP_SELF]?>?goods_no=<?=$goods_no?>");
	</script>
<?
		}

	}

	$arr = listGoodsBuyCompany($conn, $goods_no);

	
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
<script language="javascript">

	// ���� ��ư Ŭ�� �� 
	function js_save() {
		
		var frm = document.frm;

		if(frm.cate_03.value == '') { 
			alert('���޻� �Է��� ����Ű�� Ȯ�����ּ���.');
			return;
		}

		frm.mode.value = "I";

		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_delete(cp_no) {
		
		var frm = document.frm;
		frm.mode.value = "D";
		frm.cp_no.value = cp_no;

		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

</script>
</head>
<body id="popup_file">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="goods_no" value="<?= $goods_no ?>" />
<input type="hidden" name="cp_no" value="" />

<div id="popupwrap_file">
	<h1>��Ÿ ���޻� �߰�</h1>
	<div id="postsch">
		<h2>* �ļ��� ���޻縦 ���/���� �մϴ�.</h2>
		<div class="addr_inp">

			<table cellpadding="0" cellspacing="0" class="colstable02">

				<colgroup>
					<col width="7%" />
					<col width="30%" />
					<col width="12%" />
					<col width="*" />
					<col width="4%" />
				</colgroup>
				<tbody>
				<tr>
						<th>���޻�</th>
						<td class="line">
							<input type="text" class="autocomplete_off" style="width:200px" placeholder="��ü(��/�ڵ�) �Է��� ����" name="txt_cp_type" value="" />
							<input type="hidden" name="cate_03" value="">
							

							<script>
								$(function(){

									$("input[name=txt_cp_type]").keydown(function(e){

										if(e.keyCode==13) { 

											var keyword = $(this).val();
											if(keyword == "") { 
												$("input[name=cate_03]").val('');
											} else { 
												$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('����,�ǸŰ���') +"&term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
													if(data.length == 1) { 
														
														js_selecting_company("txt_cp_type", data[0].label, "cate_03", data[0].id);

													} else if(data.length > 1){ 
														NewWindow("../company/pop_company_searched_list.php?con_cp_type=����,�ǸŰ���&search_str="+keyword + "&target_name=txt_cp_type&target_value=cate_03",'pop_company_searched_list','950','650','YES');

													} else 
														alert("�˻������ �����ϴ�.");
												});
											}
										}

									});

									$("input[name=txt_cp_type]").keyup(function(e){
										var keyword = $(this).val();

										if(keyword == "") { 
											$("input[name=cate_03]").val('');
										}
									});

								});

								function js_selecting_company(target_name, cp_nm, target_value, cp_no) {
									
									$(function(){

										$("input[name="+target_name+"]").val(cp_nm);
										$("input[name="+target_value+"]").val(cp_no);

										//���õǸ� �ڵ� ��Ŀ�� �̵�
										$("input[name=buy_price]").focus();

									});

								}

							</script>
							
						</td>
						
						<td class="line">
							<input type="text" class="txt" style="width:50px" name="buy_price"  placeholder="���԰�" required value="" />��
						</td>
						<td class="line">
							<input type="text" class="txt" style="width:95%" name="memo" placeholder="����Է�" required value="" />
						</td>
						<td class="line">
							<input type="button" onclick="js_save()" value="�߰�"/>
						</td>
					</tr>
				</tbody>
			</table>

			<div class="sp20"></div>

			<table cellpadding="0" cellspacing="0" class="rowstable02">
				<col width="30%" />
				<col width="10%" />
				<col width="*" />
				<col width="10%" />
				<thead>
					<tr>
						<th>��ü��</th>
						<th>���԰�</th>
						<th>�޸�</th>
						<th class="end">�׼�</th>
					</tr>
				</thead>
				<tbody>
					<? 
					if(sizeof($arr) > 0) { 
						for($i = 0; $i < sizeof($arr); $i++) {
							
							$BUY_CP_NO		= $arr[$i]["BUY_CP_NO"];
							$BUY_PRICE		= $arr[$i]["BUY_PRICE"];
							$MEMO			= $arr[$i]["MEMO"];
							$BUY_CP_NAME	= $arr[$i]["BUY_CP_NAME"];
							?>
					<tr>
						<td><?=$BUY_CP_NAME?></td>
						<td class="price"><?=number_format($BUY_PRICE)?> ��</td>
						<td class="modeual_nm"><?=$MEMO?></td>
						<td style="text-align:center;"><input type="button" name="b" value=" ���� " onclick="js_delete('<?=$BUY_CP_NO?>')"/></td>
					</tr>
							<?
						}
				
						?>

					<? } else { ?>
					<tr>
						<td colspan="4" height="30" style="text-align:center;">�Էµ� ������ �����ϴ�.</td>
					</tr>
						
					<? } ?>
					
				</tbody>
			</table>
				
		</div>

	</div>
	<br />
	<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="�ݱ�" /></a></div>
</div>
<script type="text/javascript" src="../js/wrest.js"></script>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>