<?session_start();?>
<?
# =============================================================================
# File Name    : order_write.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @�Ƹ����� Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "SP009"; // �޴����� ���� �� �־�� �մϴ�

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
	require "../../_classes/biz/member/member.php";
	require "../../_classes/biz/payment/payment.php";
	require "../../_classes/biz/board/board.php";
	
	$reserve_no		= trim($reserve_no);
	$r_addr1			= trim($r_addr1);
	$r_zipcode		= trim($r_zipcode);
	$mode					= trim($mode);

	if ($mode == "U") {

		$result = updateOrderAddrOrderRead($conn, $r_zipcode, $r_addr1, $reserve_no);

		if ($result) {
?>
<script type="text/javascript">
	window.opener.js_reload();
	alert("���� �Ǿ����ϴ�.");
	self.close();
</script>
<?
			mysql_close($conn);
			exit;
		}

	}

#====================================================================
# Request Parameter
#====================================================================

	$del_tf = "N";
	
#===============================================================
# Get Search list count
#===============================================================

	$arr_order_rs = selectOrder($conn, $reserve_no);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />

<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/calendar.js"></script>
<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script>
<script language="javascript">

	function sample6_execDaumPostcode(a) {
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

				// ������ȣ�� �ּ� ������ �ش� �ʵ忡 �ִ´�.
				if (a == "O") {
					document.getElementById('o_zipcode').value = data.zonecode; //5�ڸ� ��������ȣ ���
					document.getElementById('o_addr1').value = fullAddr;
					// Ŀ���� ���ּ� �ʵ�� �̵��Ѵ�.
					document.getElementById('o_addr1').focus();
				}

				// ������ȣ�� �ּ� ������ �ش� �ʵ忡 �ִ´�.
				if (a == "R") {
					document.getElementById('r_zipcode').value = data.zonecode; //5�ڸ� ��������ȣ ���
					document.getElementById('r_addr1').value = fullAddr;
					// Ŀ���� ���ּ� �ʵ�� �̵��Ѵ�.
					document.getElementById('r_addr1').focus();
				}

			}
		}).open();
	}

	function js_save() {
	
		var frm = document.frm;
	
		frm.mode.value = "U";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}


</script>
</head>

<body id="popup_file">

<div id="popupwrap_file">
	<h1>����� �ּ� ����</h1>
	<div id="postsch_file">
		<form name="frm" method="post">
		<input type="hidden" name="mode"value="">
		<input type="hidden" name="reserve_no"value="<?=$reserve_no?>">
		<h2>* �ּ�����</h2>
					<table cellpadding="0" cellspacing="0" class="rowstable01" style="width:98%" border="0">
						<colgroup>
						<col width="15%" />
						<col width="65%" />
						<col width="20%" />
					</colgroup>
					<tr>
						<th>������ȣ</th>
						<th>�ּ�</th>
						<th class="end">���</th>
					</tr>
				<?
					$nCnt = 0;
					$sum_qty = 0;
					
					if (sizeof($arr_order_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_order_rs); $j++) {
							
							$rs_order_no					= trim($arr_order_rs[0]["ORDER_NO"]); 
							$rs_mem_no						= trim($arr_order_rs[0]["MEM_NO"]); 
							$rs_order_state				= trim($arr_order_rs[0]["ORDER_STATE"]); 
							$rs_total_sale_price	= trim($arr_order_rs[0]["TOTAL_SALE_PRICE"]); 
							$rs_total_extra_price	= trim($arr_order_rs[0]["TOTAL_EXTRA_PRICE"]); 
							$rs_order_date				= trim($arr_order_rs[0]["ORDER_DATE"]); 
							$rs_pay_date					= trim($arr_order_rs[0]["PAY_DATE"]); 
							$rs_pay_type					= trim($arr_order_rs[0]["PAY_TYPE"]); 
							$rs_cancel_date				= trim($arr_order_rs[0]["CANCEL_DATE"]); 
							$rs_delivery_type			= trim($arr_order_rs[0]["DELIVERY_TYPE"]); 
							$rs_delivery_date			= trim($arr_order_rs[0]["DELIVERY_DATE"]); 
							$rs_finish_date				= trim($arr_order_rs[0]["FINISH_DATE"]); 

							$rs_r_mem_nm					= trim($arr_order_rs[0]["R_MEM_NM"]);
							$rs_r_zipcode					= trim($arr_order_rs[0]["R_ZIPCODE"]);
							$rs_r_addr1						= trim($arr_order_rs[0]["R_ADDR1"]);
							$rs_r_addr2						= trim($arr_order_rs[0]["R_ADDR2"]);
							$rs_r_phone						= trim($arr_order_rs[0]["R_PHONE"]);
							$rs_r_hphone					= trim($arr_order_rs[0]["R_HPHONE"]);
							$rs_memo							= trim($arr_order_rs[0]["MEMO"]);

						?>

						<tr>
							<td><input type="text" name="r_zipcode" id="r_zipcode" value="<?=$rs_r_zipcode?>" class="txt" style="width:90%"></td>
							<td class="modeual_nm" height="35">
								<input type="text" name="r_addr1" id="r_addr1" value="<?=$rs_r_addr1?>" class="txt" style="width:90%">
							</td>
							<td class="filedown"><a href="javascript:void(0);" onclick="sample6_execDaumPostcode('R')"><img src="/manager/images/admin/btn_filesch.gif" alt="ã��" align="absmiddle" /></a></td>
						</tr>

						<?
						}
					}else{
						?>
						<tr>
							<td height="50" align="center" colspan="4">�����Ͱ� �����ϴ�. </td>
						</tr>
					<?
						}
					?>
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