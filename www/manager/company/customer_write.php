<?session_start();?>
<?
# =============================================================================
# File Name    : customer_write.php
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "CP004"; // �޴����� ���� �� �־�� �մϴ�

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
	require "../../_classes/biz/company/customer_210409.php";

#====================================================================
# Request Parameter
#====================================================================

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);

#============================================================
# Page process
#============================================================

if ($nPage <> "") {
	$nPage = (int)($nPage);
} else {
	$nPage = 1;
}

if ($nPageSize <> "") {
	$nPageSize = (int)($nPageSize);
} else {
	$nPageSize = 20;
}

$nPageBlock	= 10;

#===============================================================
# Get Search list count
#===============================================================
	$nListCnt = totalCntCustomer($conn, $groupNo, $search_field, $search_str);

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}
	
	$arr_rs = selectCustomer($conn, $groupNo, $search_field, $search_str, $nPage, $nPageSize);

?>


<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script>

<script type="text/javascript">

function win_onload()
{
	var frm = document.frm;		

	var target = document.getElementById("gr_no");
	
	document.getElementById("gr_no").disabled = true;
	document.getElementById("custNo").disabled = true;

	frm.gr_no.value = frm.groupNo.value;

	frm.gr_no2.value = frm.gr_no.value;

	//alert(target.options[target.selectedIndex].text);
	frm.groupNm.value = target.options[target.selectedIndex].text;

	document.getElementById("span1").innerHTML = "["+frm.groupNm.value+"] �׷� �� ����";

	frm.group_Name.value = "";
}

function js_groupMove()
{
	document.getElementById("gr_no").disabled = false;
}

function js_gr_no()
{
	var frm = document.frm;
	var target = document.getElementById("gr_no");
	//alert(frm.gr_no.value);
	//alert(target.options[target.selectedIndex].text);
	frm.gr_no2.value = frm.gr_no.value;
	frm.gr_nm.value = target.options[target.selectedIndex].text;
}

function js_group_Name()
{
	var frm = document.frm;
	var target = document.getElementById("group_Name");
	frm.groupMovenm.value = target.options[target.selectedIndex].text;
}

function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

function js_valueSetting(custNo, custNm, depart, hp, companyNm, position, memo, grno) 
{
	var frm = document.frm;		
	var text = memo;
	
	result = text.replace(/(<br>|<br\/>|<br \/>)/g, '\r\n');

	frm.custNm.value 		= custNm;
	frm.custNo.value 		= custNo;
	frm.custNo2.value 		= custNo;
	frm.department.value 	= depart;
	frm.hphone.value 		= hp;
	frm.companyNm.value 	= companyNm;
	frm.position.value 		= position;
	frm.memo.value 			= result;
	frm.gr_no.value 		= grno;
	frm.gr_no2.value 		= grno;	

	document.getElementById("gr_no").disabled = true;
}

function js_clean() 
{	
	var frm = document.frm;
	
	frm.custNm.value 		= "";
	frm.custNo.value 		= "";
	frm.custNo2.value 		= "";
	frm.department.value 	= "";
	frm.hphone.value 		= "";
	frm.companyNm.value 	= "";
	frm.position.value 		= "";
	frm.memo.value 			= "";
}

function js_save() {

	var frm = document.frm;
	var groupNumber;
	var groupName;
	var groupType;

	if (frm.gr_no2.value == "") 
	{
		alert('�׷���� ���� �� �ּ���.');
		frm.gr_no.focus();
		return ;		
	}
	
	if (frm.custNm.value == "") 
	{
		alert('�� �̸��� �Է��� �ּ���.');
		frm.custNm.focus();
		return ;		
	}

	if (frm.companyNm.value == "") 
	{
		alert('ȸ����� �Է��� �ּ���.');
		frm.companyNm.focus();
		return ;		
	}
	
	if (frm.hphone.value == "") 
	{
		alert('�޴���ȭ��ȣ�� �Է��� �ּ���.');
		frm.hphone.focus();
		return ;		
	}

	if(frm.groupNo.value != frm.gr_no2.value)
	{
		if(frm.custNo2.value == "")
		{
			if (!confirm("�׷���� ���� �Ǿ����ϴ�.\n\n�ű� �����͸� ���� �Ͻðڽ��ϱ�?")) return;	
		}
		else
		{
			if (!confirm("�׷���� ���� �Ǿ����ϴ�.\n\n�� ��ȣ [ "+frm.custNo2.value+" ] ���� ���� �Ͻðڽ��ϱ�?")) return;	
		}
		
		groupNumber = frm.gr_no2.value;
		groupName 	= frm.gr_nm.value;
		groupType 	= 1;
	}
	else
	{
		if(frm.custNo2.value == "")
		{
			if (!confirm("�ű� �����͸� ���� �Ͻðڽ��ϱ�?")) return;	
		}
		else
		{
			if (!confirm("�� ��ȣ [ "+frm.custNo2.value+" ] ���� ���� �Ͻðڽ��ϱ�?")) return;	
		}

		groupNumber = frm.groupNo.value;
		groupName 	= frm.groupNm.value;
		groupType 	= 0;
	}	

	$.ajax({
			url: "json_customer_action.php",
			dataType: 'json',
			type: 'post',
			async: true,
			data: {
					  mode: "CUSTOMER_MERGE"					
					, reg_adm: <?=$s_adm_no?>
					, customer_no: frm.custNo2.value
					, customer_nm : frm.custNm.value
					, company_nm : frm.companyNm.value
					, hPhone : frm.hphone.value
					, department : frm.department.value
					, position : frm.position.value
					, memo : frm.memo.value
					, groupNo : groupNumber
					, groupType : groupType
				},
				success: function(data) 
				{
					$.each(data, function(i, item)
					{
						if(item.RESULT == "N")
						{
							alert("���� ���õ� [ "+groupName+" ] �׷��� �� ["+ item.GROUP_CNT +"] �� ���� ��� �����մϴ�.");
						}
						else
						{
							alert("���� �Ǿ����ϴ�.");
							js_search();
						}
					});
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					alert('��� ����');
					return;
				}
		});

}

function js_delete() {

	var frm = document.frm;

	if(frm.custNo2.value =="")
	{
		alert("������ ���� ���� �� �ּ���.");
		return;
	}
	
	if (!confirm("�� ��ȣ [ "+frm.custNo2.value+" ] ���� ���� �Ͻðڽ��ϱ�?")) return;	

	$.ajax({
			url: "json_customer_action.php",
			dataType: 'json',
			type: 'post',
			async: true,
			data: {
					  mode: "CUSTOMER_DEL"					
					, reg_adm: <?=$s_adm_no?>
					, customer_no: frm.custNo2.value
					, groupNo: frm.groupNo.value
				},
				success: function(data) 
				{
					alert("���� �Ǿ����ϴ�.");
					js_search();
				}	,
				error: function(jqXHR, textStatus, errorThrown)
				{
					alert('��� ����');
					return;
				}
		});

}

function js_all_check() 
{
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

	function js_selDel() 
	{ 
		var frm = document.frm;
		var selected_cnt = $("input[name='chk_no[]']:checked").length;

		if(selected_cnt == 0) 
		{
			alert('���õ� �����Ͱ� �����ϴ�');
			return;
		}

		if (!confirm("������ �����͸� ���� �Ͻðڽ��ϱ�?")) return;	

		var customer_no= new Array();

		$("input[name='chk_no[]']:checked").each(function(){
			customer_no.push($(this).val());
		});

		$.ajax({
			url: "json_customer_action.php",
			dataType: 'json',
			type: 'post',
			async: true,
			data: {
					  mode: "CUSTOMER_SELDEL"					
					, reg_adm: <?=$s_adm_no?>
					, customer_no: customer_no
					, groupNo: frm.groupNo.value
				},
				success: function(data) 
				{
					alert("���� �� �����Ͱ� ���� �Ǿ����ϴ�.");
					js_search();
				}	,
				error: function(jqXHR, textStatus, errorThrown)
				{
					alert('��� ����');
					return;
				}
		});
	}

	function js_Gr_Move() 
	{ 
		var frm = document.frm;
		var selected_cnt = $("input[name='chk_no[]']:checked").length;

		if(selected_cnt == 0) 
		{
			alert('���õ� �����Ͱ� �����ϴ�');
			return;
		}

		if (frm.group_Name.value == "") 
		{
			alert('�̵��� �׷���� ���� �� �ּ���.');
			frm.group_Name.focus();
			return ;		
		}

		if (!confirm("������ �������� �׷��� �̵� �Ͻðڽ��ϱ�?")) return;	

		var customer_no= new Array();

		$("input[name='chk_no[]']:checked").each(function(){
			customer_no.push($(this).val());
		});

		$.ajax({
			url: "json_customer_action.php",
			dataType: 'json',
			type: 'post',
			async: true,
			data: {
					  mode: "CUSTOMER_GROUP_MOVE"					
					, reg_adm: <?=$s_adm_no?>
					, customer_no: customer_no
					, groupNo: frm.group_Name.value
				},
				success: function(data) 
				{
					$.each(data, function(i, item)
					{
						if(item.RESULT == "N")
						{
							alert("���� ���õ� [ "+frm.groupMovenm.value+" ] �׷��� �� ["+ item.GROUP_CNT +"] �� ���� ��� �����մϴ�.");
						}
						else
						{
							alert("���� �� �����Ͱ� �̵� �Ǿ����ϴ�.");
							js_search();
						}
					});
				}	,
				error: function(jqXHR, textStatus, errorThrown)
				{
					alert('��� ����');
					return;
				}
		});
	}



</script>

</head>
<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="nPage" value="<?=$nPage?>" />
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>" />
<input type="hidden" name="groupNo" value="<?=$groupNo?>" />
<input type="hidden" name="groupNm" value="<?=$groupNm?>" />
<input type="hidden" id="gr_nm" name="gr_nm"/>
<input type="hidden" id="groupMovenm" name="groupMovenm"/>

<input type="hidden" name="search_field" value="<?=$search_field?>">
<input type="hidden" name="search_str" value="<?=$search_str?>">

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
	  <h2><span id="span1">�� ����</span></h2>
		<div class="btnright">					
			<a href="javascript:js_clean();"><img src="../images/admin/btn_insert.gif" alt="�ű�" /></a>
			<a href="javascript:js_save();"><img src="../images/admin/btn_save.gif" alt="����" /></a>			
			<a href="javascript:js_delete();"><img src="../images/admin/btn_delete.gif" alt="����" /></a>
        </div>
        <table cellpadding="0" cellspacing="0" class="colstable">
			<colgroup>
				<col width="15%" />
				<col width="35%" />
				<col width="15%" />
				<col width="35%" />
			</colgroup>
			<tr>
				<th><font color='red'>*</font> �׷��</th>
				<td>
					<?= CustGroupSelectBox($conn,"gr_no","125","����","",$gr_no)?>
					<input type="hidden" id="gr_no2" name="gr_no2"/>					
					&nbsp;<input type="button" id="alter_bt" name="alter_bt" onclick="js_groupMove()" value="����" />
				</td>
				<th>�� ��ȣ</th>
				<td>
					<input type="text" class="box01" style="width:10%; text-align:center;" id="custNo" name="custNo" readonly />
					<input type="hidden" id="custNo2" name="custNo2"/>
				</td>				
			</tr>
			<tr>
				<th><font color='red'>*</font> �� �̸�</th>
				<td>
					<input type="text" class="box01" style="width:35%" id="custNm" name="custNm" />
				</td>				
			</tr>
			<tr>
				<th><font color='red'>*</font> ȸ���</th>
				<td colspan="3">
					<input type="text" class="box01" style="width:35%" name="companyNm"/>
				</td>
			</tr>
			<tr>
				<th><font color='red'>*</font> �޴���ȭ��ȣ</th>
				<td><input type="text" class="box01" style="width:35%" name="hphone" onkeyup="return isPhoneNumber(this)" /></td>
			</tr>
			<tr>
				<th>�μ�</th>
				<td colspan="3">
					<input type="text" class="box01" style="width:35%" name="department"/>
				</td>
			</tr>			
			<tr>
				<th>��å</th>
				<td colspan="3">
					<input type="text" class="box01" style="width:35%" name="position"/>
				</td>
			</tr>		
			<tr>
				<th>�޸�</th>
				<td colspan="3">
					<textarea class="box01" cols="100" style="width:40%" rows="5" id="memo" name="memo"></textarea>
				</td>
			</tr>
			
        </table>

		<h3>�� ����Ʈ</h3>  
		<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="15%" />
					<col width="79%" />
					<col width="6%" />
				</colgroup>
					<tr>
						
						<th>�˻�����</th>
						<td>
							<select name="search_field" style="width:84px;">
								<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >���հ˻�</option>
								<option value="CUSTOMER_NM" <? if ($search_field == "CUSTOMER_NM") echo "selected"; ?> >���̸�</option>
								<option value="COMPANY_NM" <? if ($search_field == "COMPANY_NM") echo "selected"; ?> >ȸ��� </option>
							</select>&nbsp;

							<input type="text" value="<?=$search_str?>" name="search_str" size="15" class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();" />
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						</td>
						<td align="right">
						</td>
					</tr>
				</tbody>
			</table>
			
			<div class="sp10"></div>
	
				�� <?=number_format($nListCnt)?> ��				
				<div style="display:inline-block; width: 91.8%; text-align: right; margin: 0 0 10px 0;">
				<?= CustGroupSelectBox($conn,"group_Name","100","����","",$group_Name)?>&nbsp;
					<input type="button" value="�׷��̵�" onclick="js_Gr_Move()">&nbsp;&nbsp;
					<input type="button" value="���û���" onclick="js_selDel()">
				</div>				

				<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table">
					<colgroup>
						<col width="5%" />
						<col width="5%" />
						<col width="10%" />
						<col width="5%"/>
						<col width="25%" />
						<col width="10%" />
						<col width="30%" />
						<col width="10%" />
					</colgroup>
					<thead>
						<tr>
							<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
							<th>No.</th>
							<th>���̸�</th>
							<th>����ȣ</th>
							<th>ȸ���</th>
							<th>�޴�����ȣ</th>
							<th>�μ�</th>
							<th>��å</th>
						</tr>
					</thead>
					<tbody>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {

						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							$RN							= trim($arr_rs[$j]["RN"]);
							$CUSTOMER_NM				= SetStringFromDB($arr_rs[$j]["CUSTOMER_NM"]);
							$CUSTOMER_NO				= trim($arr_rs[$j]["CUSTOMER_NO"]); 
							$DEPARTMENT					= SetStringFromDB($arr_rs[$j]["DEPARTMENT"]);
							$GROUP_NO					= trim($arr_rs[$j]["GROUP_NO"]);
							$HPHONE						= trim($arr_rs[$j]["HPHONE"]);
							$COMPANY_NM					= SetStringFromDB($arr_rs[$j]["COMPANY_NM"]);
							$POSITION					= SetStringFromDB($arr_rs[$j]["POSITION"]);							
							$MEMO						= SetStringFromDB($arr_rs[$j]["MEMO"]);
				
				?>
						<tr height="30">
							<td><input type="checkbox" name="chk_no[]" class="chk" value="<?=$CUSTOMER_NO?>"></td>
							<td><?= $RN ?></td>
							<td><a href="javascript:js_valueSetting('<?=$CUSTOMER_NO?>','<?=$CUSTOMER_NM?>','<?=$DEPARTMENT?>','<?=$HPHONE?>','<?=$COMPANY_NM?>','<?=$POSITION?>','<?=nl2br($MEMO)?>','<?=$GROUP_NO?>')"><?= $CUSTOMER_NM ?></a></td>
							<td><a href="javascript:js_valueSetting('<?=$CUSTOMER_NO?>','<?=$CUSTOMER_NM?>','<?=$DEPARTMENT?>','<?=$HPHONE?>','<?=$COMPANY_NM?>','<?=$POSITION?>','<?=nl2br($MEMO)?>','<?=$GROUP_NO?>')"><?= $CUSTOMER_NO ?></a></td>
							<td><?= $COMPANY_NM ?></td>
							<td><?= $HPHONE ?></td>							
							<td><?= $DEPARTMENT ?></td>
							<td><?= $POSITION ?></td>							
						</tr>
						
				<?			
						}
					} else { 
				?> 
						<tr>
							<td align="center" height="50" colspan="10">�����Ͱ� �����ϴ�. </td>
						</tr>
				<? 
					}
				?>
					</tbody>
				</table>
								
					<!-- --------------------- ������ ó�� ȭ�� START -------------------------->
					<?
						# ==========================================================================
						#  ����¡ ó��
						# ==========================================================================
						if (sizeof($arr_rs) > 0) {
							#$search_field		= trim($search_field);
							#$search_str			= trim($search_str);
							$strParam = $strParam."&groupNo=".$groupNo."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str;

					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- ������ ó�� ȭ�� END -------------------------->
      <!-- // E: mwidthwrap -->

    </td>
  </tr>
  </table>
</div>
<iframe src="" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>
<?
#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);
?>
<body onload=win_onload()>
</body>