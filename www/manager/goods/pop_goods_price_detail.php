<?session_start();?>
<?
# =============================================================================
# File Name    : ��ǰ���� ��
# =============================================================================

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

#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);
	
	$temp_no		= trim($temp_no);
	$goods_no		= trim($goods_no);

#====================================================================
# DML Process
#====================================================================
	$mode = "S";

	if ($mode == "S") {

		$arr_rs = selectGoodsPriceChange($conn, $seq_no);

		$rs_goods_no			= trim($arr_rs[0]["GOODS_NO"]); 
		$rs_cp_no				= trim($arr_rs[0]["CP_NO"]); 
		$rs_price				= trim($arr_rs[0]["PRICE"]); 
		$rs_buy_price			= trim($arr_rs[0]["BUY_PRICE"]); 
		$rs_sale_price			= trim($arr_rs[0]["SALE_PRICE"]);
		$rs_delivery_cnt_in_box	= trim($arr_rs[0]["DELIVERY_CNT_IN_BOX"]);
		$rs_sticker_price		= trim($arr_rs[0]["STICKER_PRICE"]);
		$rs_print_price			= trim($arr_rs[0]["PRINT_PRICE"]);
		$rs_delivery_price		= trim($arr_rs[0]["DELIVERY_PRICE"]);
		$rs_labor_price			= trim($arr_rs[0]["LABOR_PRICE"]);
		$rs_other_price			= trim($arr_rs[0]["OTHER_PRICE"]);
		$rs_sale_susu			= trim($arr_rs[0]["SALE_SUSU"]);

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
<script type="text/javascript" src="../js/calendar.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->

</head>
<body id="popup_file">


<div id="popupwrap_file">
	<h1>��ǰ ���� ����</h1>
	<div id="postsch_code">
		<h2>* ��ü�� ��ǰ ������ ����մϴ�. ��ǰ���� �Է��ϼ���.</h2>
		<div class="addr_inp">

		<table cellpadding="0" cellspacing="0" width="95%">
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0" width="100%" class="colstable02">

						<colgroup>
							<col width="15%" />
							<col width="35%" />
							<col width="15%" />
							<col width="35%" />
						</colgroup>
						<tr>
							<th title="(��Ʈ)���԰� = �ƿ��ڽ� ���� �������� ���԰� * ������ �� + (�ƿ��ڽ� ���԰� * ���� / �ڽ��Լ�)">���԰�</th>
							<td class="line">
								<input type="text" class="txt calc buy_price" style="width:90px" name="buy_price" value="<?=$rs_buy_price?>" required onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()" <?=(startsWith($rs_goods_cate, '14')  ? "readonly" : "") ?> /> �� <font class="buy_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_buy_price?>">(<?=$rs_buy_price?> ��)</font>
							</td>
							<th>�ǸŰ�</th>
							<td class="line">
								<input type="text" class="txt calc sale_price" style="width:90px" name="sale_price" value="<?=$rs_sale_price?>" required onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()" /> �� <font class="sale_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_sale_price?>">(<?=$rs_sale_price?> ��)</font>
							</td>
						</tr>
						
						<tr>
							<th>��ƼĿ ���</th>
							<td class="line">
								<input type="text" class="txt calc sticker_price" style="width:90px" name="sticker_price" value="<?=$rs_sticker_price?>" onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()"/> �� <font class="sticker_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_sticker_price?>">(<?=$rs_sticker_price?> ��)</font>
							</td>
							<th>������� 15%</th>
							<td class="line">
								<span id="vendor15"></span>��
							</td>
						</tr>
						<tr>
							<th>�����μ� ���</th>
							<td class="line">
								<input type="text" class="txt calc print_price" style="width:90px" name="print_price" value="<?=$rs_print_price?>" onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()"/> �� <font class="print_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_print_price?>">(<?=$rs_print_price?> ��)</font>
							</td>
							<th>������� 35%</th>
							<td class="line">
								<span id="vendor35"></span>��
							</td>
						</tr>
						<tr>
							<th>�ù���</th>
							<td class="line">
								<input type="text" class="txt calc delivery_price" style="width:90px" name="delivery_price" value="<?=$rs_delivery_price?>" onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()"/> �� <font class="delivery_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_delivery_price?>">(<?=$rs_delivery_price?> ��)</font>
							</td>
							<th>������� <input type="text" name="vendor_calc" value="55" class="txt calc" onkeyup="js_calculate_buy_and_sale_price()" style="width:20px;"/> %</th>
							<td class="line">
								<span id="vendor_calc"></span>��
							</td>
						</tr>
						<tr>
							<th>�ڽ��Լ�</th>
							<td class="line">
								<input type="text" class="txt calc delivery_cnt_in_box" style="width:90px" name="delivery_cnt_in_box" required value="<?=$rs_delivery_cnt_in_box?>" onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()"/> �� <font class="delivery_cnt_in_box" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_delivery_cnt_in_box?>">(<?=$rs_delivery_cnt_in_box?> ��)</font>
							</td>
							<th>�Ǹ� ������</th>
							<td class="line">
								<input type="text" class="txt calc sale_susu" style="width:90px" name="sale_susu" value="<?=($goods_no == "" ? "7.15" : $rs_sale_susu)?>" onkeyup="return isFloat(this)" onkeyup="js_calculate_buy_and_sale_price()"/> % <font class="sale_susu" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_sale_susu?>">(<?=$rs_sale_susu?> %)</font> &nbsp;&nbsp; <input type="checkbox" name="has_susu" onchange="js_calculate_buy_and_sale_price()" checked value="Y"/>&nbsp;(������ - �������� ����)
							</td>	
						</tr>
						<tr>
							<th title="������ = �ù��� / �ڽ��Լ�">
								������
							</th>
							<td class="line">
								<span id="delivery_per_price">0</span> ��
							</td>
							<th title="�Ǹ� ������ = ((�ǸŰ� / 100) * �Ǹ� ������)">�Ǹ� ������</th>
							<td class="line">
								<span id="susu_price">0</span> ��
							</td>	
		
						</tr>
						<tr>
							<th>�ΰǺ�</th>
							<td class="line">
								<input type="text" class="txt calc labor_price" style="width:90px" name="labor_price" value="<?=$rs_labor_price?>" onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()"/> �� <font class="labor_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_labor_price?>">(<?=$rs_labor_price?> ��)</font>
							</td>
							<th title="���� = �ǸŰ� - �Ǹż����� - �����հ�">����</th>
							<td class="line">
								<span id="majin">0</span> ��
								
							</td>	
						</tr>
						<tr>
							<th>��Ÿ ���</th>
							<td class="line">
								<input type="text" class="txt calc other_price" style="width:90px" name="other_price" value="<?=$rs_other_price?>" onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()"/> �� <font class="other_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_other_price?>">(<?=$rs_other_price?> ��)</font>
							</td>
							<th title="������ = ���� / �ǸŰ� * 100">������</th>
							<td class="line">
								<span id="majin_per">0</span> %
							</td>
						</tr>
						<tr>
							<th title="�����հ� = ���԰�(�ƿ��ڽ� ���� ������԰��� �� + (�ƿ��ڽ� ���԰� / �ڽ��Լ�)) + ��ƼĿ��� + �����μ��� + ������ + �ΰǺ� + ��Ÿ���">�����հ�</th>
							<td class="line">
								<input type="text" id="total_wonga" class="txt calc price" style="width:90px" name="price" value="<?=$rs_price?>" onkeyup="return isNumber(this)" readonly /> �� <font class="price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_price?>">(<?=$rs_price?> ��)</font>
								
							</td>
							<th title="������ ������� �ǸŰ� �� ����">�����ǸŰ� <input type="text" name="best_sale_calc" value="20" class="txt calc" onkeyup="js_calculate_buy_and_sale_price()" style="width:20px;"/> %</th>
							<td class="line">
								<span id="best_sale_price">N/A</span> ��
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
		
	<div class="btn">
		<a href="javascript:js_save();"><img src="../images/admin/btn_regist_02.gif" alt="���" /></a> 
	</div>

</div>
	<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="�ݱ�" /></a></div>
</div>
<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>
<?
#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);
?>