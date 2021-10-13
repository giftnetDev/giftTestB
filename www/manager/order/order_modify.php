<?session_start();?>
<?
# =============================================================================
# File Name    : admin_write.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
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
	$menu_right = "OD003"; // �޴����� ���� �� �־�� �մϴ�

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
	require "../../_classes/com/util/ImgUtil.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/goods/goods.php";

#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);
	
	$temp_no	= trim($temp_no);
	$order_no		= trim($order_no);
	
	$result	= false  ;

#====================================================================
# DML Process
#====================================================================

	if ($mode == "S") {

		$arr_rs = selectTempOrder($conn, $temp_no, $order_no);

		$rs_cp_no							= trim($arr_rs[0]["CP_NO"]); 
		$rs_order_no						= SetStringFromDB($arr_rs[0]["ORDER_NO"]); 
		$rs_o_name							= SetStringFromDB($arr_rs[0]["O_NAME"]); 
		$rs_o_phone							= SetStringFromDB($arr_rs[0]["O_PHONE"]); 
		$rs_o_hphone						= SetStringFromDB($arr_rs[0]["O_HPHONE"]); 
		$rs_r_name							= SetStringFromDB($arr_rs[0]["R_NAME"]); 
		$rs_r_phone							= SetStringFromDB($arr_rs[0]["R_PHONE"]); 
		$rs_r_hphone						= SetStringFromDB($arr_rs[0]["R_HPHONE"]); 		
		$rs_r_zipcode						= SetStringFromDB($arr_rs[0]["R_ZIPCODE"]); 		
		$rs_r_addr1							= SetStringFromDB($arr_rs[0]["R_ADDR1"]); 		
		$rs_memo							= SetStringFromDB($arr_rs[0]["MEMO"]); 		
		$rs_order_state						= trim($arr_rs[0]["ORDER_STATE"]); 
		$rs_use_tf							= trim($arr_rs[0]["USE_TF"]); 
		$rs_cp_order_no						= SetStringFromDB($arr_rs[0]["CP_ORDER_NO"]); 
		$rs_opt_manager_no					= SetStringFromDB($arr_rs[0]["OPT_MANAGER_NO"]); 

	}

	if ($mode == "U") {
		
		$result = updateTempOrder($conn, $cp_no, $o_name, $o_phone, $o_hphone, $r_name, $r_phone, $r_hphone, $r_zipcode, $r_addr1, $memo, $cp_order_no, $opt_manager_no, $s_adm_no, $temp_no, $order_no);

		/*
		$arr_rs_temp_goods = selectTempOrderGoods($conn, $temp_no, $order_no);
		if (sizeof($arr_rs_temp_goods) > 0) {
			for ($k = 0 ; $k < sizeof($arr_rs_temp_goods); $k++) {

				$ORDER_SEQ			= SetStringFromDB($arr_rs_temp_goods[$k]["ORDER_SEQ"]);
				$goods_name			= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_NAME"]);
				$goods_price		= trim($arr_rs_temp_goods[$k]["GOODS_PRICE"]);
				$qty				= trim($arr_rs_temp_goods[$k]["QTY"]);
				$goods_option_nm	= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_OPTION_NM"]);
				$goods_mart_code	= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_MART_CODE"]);

				$goods_no = $_POST['search_goods_no_'.$ORDER_SEQ];

				updateTempOrderGoodsNo($conn, $order_no, $ORDER_SEQ, $goods_no, $temp_no);			
		
			}
		}
		*/
	}
	
	if ($result) {
		$strParam = $strParam."?nPage=".$nPage."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&con_cp_type=".$con_cp_type;
		
		if ($mode == "U") {
?>	
<script language="javascript">
	opener.js_reload();
	self.close();
	//location.href =  "company_modify.php<?=$strParam?>&mode=S&temp_no=<?=$temp_no?>&cp_no=<?=$cp_no?>";
</script>
<?
		} else {
?>	
<script language="javascript">
		alert('���� ó�� �Ǿ����ϴ�.');
		location.href =  "order_list.php<?=$strParam?>";
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
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script>

<script>

    function sample6_execDaumPostcode() {
        new daum.Postcode({
            oncomplete: function(data) {

                // �˾����� �˻���� �׸��� Ŭ�������� ������ �ڵ带 �ۼ��ϴ� �κ�.
                // �� �ּ��� ���� ��Ģ�� ���� �ּҸ� �����Ѵ�.
                // �������� ������ ���� ���� ��쿣 ����('')���� �����Ƿ�, �̸� �����Ͽ� �б� �Ѵ�.
                var fullAddr = ''; // ���� �ּ� ����
                var extraAddr = ''; // ������ �ּ� ����

                // ����ڰ� ������ �ּ� Ÿ�Կ� ���� �ش� �ּ� ���� �����´�.
                if (data.userSelectedType === 'R') { // ����ڰ� ���θ� �ּҸ� �������� ���
                    fullAddr = data.roadAddress;

                } else { // ����ڰ� ���� �ּҸ� �������� ���(J)
                    fullAddr = data.jibunAddress;
                }

                // ����ڰ� ������ �ּҰ� ���θ� Ÿ���϶� �����Ѵ�.
                if(data.userSelectedType === 'R'){
                    //���������� ���� ��� �߰��Ѵ�.
                    if(data.bname !== ''){
                        extraAddr += data.bname;
                    }
                    // �ǹ����� ���� ��� �߰��Ѵ�.
                    if(data.buildingName !== ''){
                        extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                    }
                    // �������ּ��� ������ ���� ���ʿ� ��ȣ�� �߰��Ͽ� ���� �ּҸ� �����.
                    fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
                }

				 // �����ȣ�� �ּ� ������ �ش� �ʵ忡 �ִ´�.
				document.getElementById("r_zipcode").value = data.zonecode;
				//document.getElementById("re_zip").value = data.postcode2;
				document.getElementById("r_addr1").value = fullAddr;
				// Ŀ���� ���ּ� �ʵ�� �̵��Ѵ�.
				document.getElementById("r_addr1").focus();
				


            }
        }).open();
    }

		function js_addr_open() {
			sample6_execDaumPostcode();
		}

</script>  

<script language="javascript">
	
	// ���� ��ư Ŭ�� �� 
	function js_save() {
		
		var order_no = "<?= $rs_order_no ?>";
		var frm = document.frm;

		//frm.cp_no.value = frm.cp_type.value;

		if (frm.cp_no.value == "") {
			alert('�Ǹž�ü�� �������ּ���.');
			frm.cp_no.focus();
			return ;		
		}

		if (isNull(frm.opt_manager_no.value)) {
			alert('��翵������� �������ּ���.');
			frm.opt_manager_no.focus();
			return ;		
		}
		/*
		if (isNull(frm.r_hphone.value)) {
			alert('�޴� ��ȭ��ȣ�� �Է����ּ���.');
			frm.r_hphone.focus();
			return ;		
		}

		if (isNull(frm.r_zipcode.value)) {
			alert('�����ȣ�� �Է����ּ���.');
			//frm.r_zipcode.focus();
			return ;		
		}

		if (isNull(frm.r_addr1.value)) {
			alert('�ּҸ� �Է����ּ���.');
			frm.r_addr1.focus();
			return ;		
		}
		*/

		frm.mode.value = "U";

		frm.target = "";
		frm.method = "post";
		frm.action = "order_modify.php";
		frm.submit();
	}

</script>
<script>
    window.onunload = refreshParent;
    function refreshParent() {
        window.opener.location.reload();
    }
</script>
</head>
<body id="popup_file">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="temp_no" value="<?= $temp_no?>">
<input type="hidden" name="order_no" value="<?= $order_no?>">
<input type="hidden" name="search_field" value="<?= $search_field ?>">
<input type="hidden" name="search_str" value="<?= $search_str ?>">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>">
<input type="hidden" name="keyword" value="">
<input type="hidden" name="goods_name" value="<?=$rs_goods_name?>">

<div id="popupwrap_file">
	<h1>�ֹ� ��� ����</h1>
	<div id="postsch">
		
		<div class="addr_inp">
			<h2>* �ֹ��� ����</h2>
			<table cellpadding="0" cellspacing="0" class="colstable">

				<colgroup>
					<col width="16%">
					<col width="34%">
					<col width="16%">
					<col width="34%">
				</colgroup>
				<thead>
					<tr>
						<th>��ü��</th>
						<td>
							<input type="text" class="seller" style="width:90%" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'�Ǹ�',$rs_cp_no)?>" />
							<script>
							$(function() {
						     var cache = {};
								$( ".seller" ).autocomplete({
									source: function( request, response ) {
										var term = request.term;
										if ( term in cache ) {
											response( cache[term] );
											return;
										}
						 
										$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('�Ǹ�'), request, function( data, status, xhr ) {
											cache[term] = data;
											response(data);
										});
									},
									minLength: 2,
									select: function( event, ui ) {
										$(".seller").val(ui.item.value);
										$("input[name=cp_no]").val(ui.item.id);

										$.getJSON( "../company/json_company_list.php?cp_no=" + ui.item.id, function( data, status, xhr ) {
											
											$.each(data, function(i, field){
													$("input[name=o_mem_name]").val(field.MANAGER_NM);
													$("input[name=o_email]").val(field.EMAIL);
													$("input[name=o_phone]").val(field.PHONE);
													$("input[name=o_hphone]").val(field.HPHONE);
													$("input[name=o_zipcode]").val(field.RE_ZIP);
													$("input[name=o_addr1]").val(field.RE_ADDR);
											});

										});
									}
								}).bind( "blur", function( event ) {
									var cp_no = $("input[name=cp_no]").val();
									if(cp_no != '') {
										$.getJSON( "../company/json_company_list.php?cp_no=" + cp_no, function(data) {
											if(data[0].CP_NO == 'undefined') {
												$("input[name=cp_no]").val('');
											} else {
												if(data[0].COMPANY != $(".seller").val())
												{
													$(".seller").val();
													$("input[name=cp_no]").val('');
												}
											}
										});
									} 
								});
							});
							</script>
							<input type="hidden" name="cp_no" value="<?=$rs_cp_no?>">
						</td>
						<th>��ü�ֹ���ȣ</th>
						<td><input type="Text" name="cp_order_no" value="<?= $rs_cp_order_no?>" style="width:70%;" itemname="��ü�ֹ���ȣ" required class="txt"></td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>�ֹ���</th>
						<td colspan="3">
							<input type="Text" name="o_name" value="<?= $rs_o_name?>" style="width:70%;" itemname="�ֹ���" required class="txt">
						</td>
					</tr>
					<tr>
						<th>����ó</th>
						<td>
							<input type="Text" name="o_phone" value="<?= $rs_o_phone?>" style="width:160px;" itemname="����ó" required class="txt">
						</td>
						<th>�޴���ȭ��ȣ</th>
						<td>
							<input type="Text" name="o_hphone" value="<?= $rs_o_hphone?>" style="width:120px;" itemname="�޴���ȭ��ȣ" required class="txt">
						</td>
					</tr>
				</tbody>
			</table>
			<div class="sp10"></div>
			<h2>* ������ ����</h2>
			<table cellpadding="0" cellspacing="0" class="colstable">

				<colgroup>
					<col width="16%">
					<col width="34%">
					<col width="16%">
					<col width="34%">
				</colgroup>
				<tbody>
					<tr>
						<th>������</th>
						<td colspan="3">
							<input type="Text" name="r_name" value="<?= $rs_r_name?>" style="width:70%;" itemname="������" required class="txt">
						</td>
					</tr>
					<tr>
						<th>����ó</th>
						<td>
							<input type="Text" name="r_phone" value="<?= $rs_r_phone?>" style="width:120px;" itemname="����ó" required class="txt">
						</td>
						<th>�޴���ȭ��ȣ</th>
						<td>
							<input type="Text" name="r_hphone" value="<?= $rs_r_hphone?>" style="width:120px;" itemname="�޴���ȭ��ȣ" required class="txt">
						</td>
					</tr>					
					<tr>
						<th>�����ּ�</th>
						<td colspan="3">
							<input type="Text" id="r_zipcode" name="r_zipcode" value="<?= $rs_r_zipcode?>" style="width:60px;" maxlength="7" class="txt">
							<input type="Text" id="r_addr1" name="r_addr1" value="<?= $rs_r_addr1?>" style="width:65%;" class="txt">
							<a href="javascript:js_addr_open();"><img src="/manager/images/admin/btn_filesch.gif" alt="ã��" align="absmiddle" /></a>
						</td>
					<tr>
				</tbody>
			</table>
			<div class="sp10"></div>
			<h2>* ��Ÿ ����</h2>
			<table cellpadding="0" cellspacing="0" class="colstable">

				<colgroup>
					<col width="16%">
					<col width="34%">
					<col width="16%">
					<col width="34%">
				</colgroup>
				<tbody>
					<tr>
						<th>�޸�</th>
						<td colspan="3" class="memo">
							<textarea style="width:75%" name="memo"><?= $rs_memo ?></textarea>
						</td>
					</tr>
					<tr>
						<th>��翵�����</th>
						<td colspan="3">
							<?= makeAdminInfoByMDSelectBox($conn,"opt_manager_no"," style='width:70px;' ","��ü","", $rs_opt_manager_no) ?>
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