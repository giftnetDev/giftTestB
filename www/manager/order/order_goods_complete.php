<?session_start();?>
<?
# =============================================================================
# File Name    : order_goods_complete.php
# Modlue       : 
# Writer       : Sungwook Min 
# Create Date  : 2015.10.12
# Modify Date  : 
#	Copyright : Copyright @C&C Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "GD005"; // �޴����� ���� �� �־�� �մϴ�

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
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/stock/stock.php";

	$today = date("Y-m-d",strtotime("0 month"));

#====================================================================
# DML Process
#====================================================================
	if ($mode == "U") {

		$arr_order_goods = selectOrderGoodsForOutStockPerReserve($conn, $order_goods_no);
				
		if($use_tf == "Y")
		{
			for ($j = 0; $j < sizeof($arr_order_goods); $j++) {

				$rs_cp_no = $arr_order_goods[$j]['CP_NO'];
				$rs_reserve_no = $arr_order_goods[$j]['RESERVE_NO'];
				$rs_qty = $arr_order_goods[$j]['QTY'];
				$rs_buy_price = $arr_order_goods[$j]['BUY_PRICE'];
				$rs_goods_cnt = $arr_order_goods[$j]['GOODS_CNT'];
				$rs_goods_sub_no = $arr_order_goods[$j]['GOODS_SUB_NO'];

				$stock_type     = "OUT";         //����� ���� (���) 
				$stock_code     = "NOUT01";      //��� �����ڵ�
				$in_cp_no		= "";	         // �԰� ��ü
				$out_cp_no	    = $rs_cp_no;        // ����ü
				$goods_no		= $rs_goods_sub_no; //����ǰ ** ��Ʈ�� ��� �ش� ��Ʈ�� ��ǰ �� ��ŭ �� �� ó���ؾ� ��
				$in_loc			= "LOCA";        // ������ ����Ʈ LG
				$in_loc_ext	    = "";
				$in_qty			= 0;
				$in_bqty		= 0;
				$in_fbqty		= 0;
				$out_qty		= $rs_qty * $rs_goods_cnt; //����ǰ ���� * �ֹ���
				$out_bqty		= 0;
				$out_fbqty	    = 0;
				$in_price		= 0;
				$out_price	    = $rs_qty * $rs_buy_price;     //���ް�
				$in_date		= "";
				$out_date		= $outstock_date;
				$pay_date		= "";
				$reserve_no	    = $rs_reserve_no;
				$close_tf		= "N";
				

				$out_result = insertStock($conn, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_fqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $close_tf, $s_adm_no, $memo);
				
			}
		}

		//���� ORDER_GOODS�� ����� ��� �Ϸ�� ���̹Ƿ� �Ϸ�ó��
		updateDeliveryState($conn, $rs_reserve_no, $order_goods_no, $delivery_cp, $delivery_no, $s_adm_no);

	}
	
	if ($out_result) {
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<script type="text/javascript">

	self.close();

</script>
<?
		mysql_close($conn);
		exit;
	}

	/*
	if ($mode == "S") {

		$arr_rs = selectGoods($conn, $goods_no);

		#GOODS_NO, GOODS_TYPE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, CATE_01, CATE_02, CATE_03, CATE_04,
		#PRICE, SALE_PRICE, EXTRA_PRICE, FILE_NM, FILE_RNM, FILE_PATH, FILE_SIZE, FILE_EXT, CONTENTS,
		#READ_CNT, DISP_SEQ, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, ADM_NO, UP_DATE, DEL_ADM, DEL_DATE

		$rs_goods_no				= trim($arr_rs[0]["GOODS_NO"]); 
		$rs_goods_cate			= trim($arr_rs[0]["GOODS_CATE"]); 
		$rs_goods_code			= trim($arr_rs[0]["GOODS_CODE"]); 
		$rs_goods_name			= SetStringFromDB($arr_rs[0]["GOODS_NAME"]); 
		$rs_goods_sub_name	= SetStringFromDB($arr_rs[0]["GOODS_SUB_NAME"]); 
		$rs_cate_01					= trim($arr_rs[0]["CATE_01"]); 
		$rs_cate_02					= trim($arr_rs[0]["CATE_02"]); 
		$rs_cate_03					= trim($arr_rs[0]["CATE_03"]); 
		$rs_cate_04					= trim($arr_rs[0]["CATE_04"]); 
		$rs_price						= trim($arr_rs[0]["PRICE"]); 
		$rs_buy_price				= trim($arr_rs[0]["BUY_PRICE"]); 
		$rs_sale_price			= trim($arr_rs[0]["SALE_PRICE"]); 
		$rs_extra_price			= trim($arr_rs[0]["EXTRA_PRICE"]); 
		$rs_stock_cnt				= trim($arr_rs[0]["STOCK_CNT"]); 
		$rs_img_url					= trim($arr_rs[0]["IMG_URL"]); 
		$rs_file_nm_100			= trim($arr_rs[0]["FILE_NM_100"]); 
		$rs_file_rnm_100		= trim($arr_rs[0]["FILE_RNM_100"]); 
		$rs_file_path_100		= trim($arr_rs[0]["FILE_PATH_100"]); 
		$rs_file_size_100		= trim($arr_rs[0]["FILE_SIZE_100"]); 
		$rs_file_ext_100		= trim($arr_rs[0]["FILE_EXT_100"]); 
		$rs_file_nm_150			= trim($arr_rs[0]["FILE_NM_150"]); 
		$rs_file_rnm_150		= trim($arr_rs[0]["FILE_RNM_150"]); 
		$rs_file_path_150		= trim($arr_rs[0]["FILE_PATH_150"]); 
		$rs_file_size_150		= trim($arr_rs[0]["FILE_SIZE_150"]); 
		$rs_file_ext_150		= trim($arr_rs[0]["FILE_EXT_150"]); 
		$rs_contents				= trim($arr_rs[0]["CONTENTS"]); 
		$rs_read_cnt				= trim($arr_rs[0]["READ_CNT"]); 
		$rs_disp_seq				= trim($arr_rs[0]["DISP_SEQ"]); 
		$rs_use_tf					= trim($arr_rs[0]["USE_TF"]); 
		$rs_del_tf					= trim($arr_rs[0]["DEL_TF"]); 
		$content						= trim($arr_rs[0]["CONTENTS"]); 


	}
	*/
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
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script>
  $(function() {
    $( ".datepicker" ).datepicker({
      showOn: "button",
      buttonImage: "/manager/images/calendar/cal.gif",
      buttonImageOnly: true,
      buttonText: "Select date",
	  showOn: "both",
	  dateFormat: "yy-mm-dd",
	  changeMonth: true,
      changeYear: true
    });
  });
</script>
<script type="text/javascript">

	function js_complete () {
		
		if (document.frm.rd_use_tf == null) {
			//alert(document.frm.rd_use_tf);
		} else {
			if (frm.rd_use_tf[0].checked == true) {
				frm.use_tf.value = "Y";
			} else {
				frm.use_tf.value = "N";
			}
		}

		document.frm.mode.value = "U";
		document.frm.target = "";
		document.frm.action = "<?=$_SERVER[PHP_SELF]?>";
		document.frm.submit();

	}
</script>
</head>
<body id="popup_order">


<form name="frm" method="post">
<div id="popupwrap_order">
	<h1>����� ��Ÿ ���� ���</h1>

	<table cellpadding="0" cellspacing="0" width="100%">

	<input type="hidden" name="order_goods_no" value="<?=$order_goods_no?>">
	<input type="hidden" name="mode" value="">
		<tr>
			<td>
				<table cellpadding="0" cellspacing="0" width="100%" class="colstable02" border="0">

				<colgroup>
					<col width="15%" />
					<col width="30%" />
					<col width="15%" />
					<col width="30%" />
					<col width="10%" />
				</colgroup>

					<tr>
						<th>�����</th>
						<td class="line">
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="outstock_date" value="<?=$today?>" maxlength="10"/>
						</td>
						<th>��� ����</th>
						<td class="line" colspan="2">
							<input type="radio" class="radio" name="rd_use_tf" value="Y" checked> ����� <span style="width:20px;"></span>
							<input type="radio" class="radio" name="rd_use_tf" value="N"> ������
							<input type="hidden" name="use_tf" value="<?= $rs_use_tf ?>"> 
						</td>
					</tr>
					<tr>
						<th>�����</th>
						<td class="line">
							<?=makeSelectBoxOnChange($conn,"DELIVERY_CP", "delivery_cp","90", "�ù�� ����", "", "")?>
						</td>
						<th>��۹�ȣ</th>
						<td class="line" colspan="2">
							<input type="text" class="txt" name="delivery_no" value=""/>
						</td>
					</tr>

						

				</table>
				<div class="sp10"></div>
				<div class="btn">
					<? if ($sPageRight_U == "Y") {?>
						<a href="javascript:js_complete();"><img src="../images/admin/btn_regist_02.gif" alt="���" /></a>
					<? } ?>
				</div>
			</td>
		</tr>
	</table>
</div>
</form>
</body>
</html>
<?
#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);
?>