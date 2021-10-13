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
	require "../../_classes/biz/stock/stock.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/confirm/confirm.php";
	require "../../_classes/biz/work/work.php";
	
	if ($this_date == "") 
		$this_date = date("Y-m-d",strtotime("0 month"));

	if ($this_h == "") 
		$this_h = date("H",strtotime("0 month"));

	if ($this_i == "") 
		$this_i = date("i",strtotime("0 month"));

	if ($this_s == "") 
		$this_s = date("s",strtotime("0 month"));


	$temp_date = $this_date." ".$this_h.":".$this_i.":".$this_s;

	if ($mode == "I") { //to 424 Line

		$use_tf = "Y";
		$cart_seq = getOrderGoodsMaxSeq($conn, $reserve_no); //�ش� reserve_no�� �� ���� ū orderSeq�� ���� TBL_ORDER_GOODS�� ORDER_SEQ�� ������´�.
		$cart_seq++;

		$arr_order_rs = selectOrder($conn, $reserve_no); //������ TBL_ORDER���� RESERVE_NO�� 1���� �����ϹǷ� Record���ٸ� �������� �ȴ�.
		$rs_o_mem_nm	= trim($arr_order_rs[0]["O_MEM_NM"]); 
		$rs_r_mem_nm	= trim($arr_order_rs[0]["R_MEM_NM"]); 
		$rs_cp_no		= trim($arr_order_rs[0]["CP_NO"]); 
		$rs_reg_date	= trim($arr_order_rs[0]["REG_DATE"]); 

		// ��� ��ǰ ���

		//if ($claim_state == "3") {
		//	$result = "";
		//} else {
		//if ($refund_able_qty == $cancel_qty) {
		//	$delivery_price = -$delivery_price;
		//} else {
		//	$delivery_price = 0;
		//}
		// echo "CANCEL_QTY : ".$cancel_qty."<br>";
		// exit;

		$discount_price = 0;
		$claim_order_goods_no = "";

		if ($claim_state <> "99") {

			$memos = array('opt_request_memo' => $opt_request_memo, 'opt_support_memo' => $opt_support_memo);//request_memo : ���ָ޸�, support_memo : �����޸�

			$claim_order_goods_no = insertOrderGoods($conn, $on_uid, $reserve_no, $cp_order_no, $buy_cp_no, $mem_no, $cart_seq, $goods_no, $goods_code, $goods_name, 
													$goods_sub_name, $cancel_qty, $opt_sticker_no, $opt_sticker_msg, $opt_outbox_tf, $delivery_cnt_in_box, $opt_wrap_no, 
													$opt_print_msg, $opt_outstock_date, $opt_memo, $memos, $delivery_type, $delivery_cp, $sender_nm, $sender_phone, $cate_01, 
													$cate_02, $cate_03, $null_cate_04, $price, $buy_price, $sale_price, $extra_price, $delivery_price, $sa_delivery_price, 
													$discount_price, $sticker_price, $print_price, $sale_susu, $labor_price, $other_price, $tax_tf, $claim_state, $use_tf, $s_adm_no);
			
		}



		//echo $claim_state." ".$claim_order_goods_no."<br/>";

		if (($claim_state == "4") || ($claim_state == "6") || ($claim_state == "7") || ($claim_state == "8")) {
			//�Ա��� ���				���						��ǰ					��ȯ

			//$claim_order_goods_no =  getLastInsertedID($conn);
			//�� �ֹ� ��ǰ�� ����

			if($delivery_type != "98" && $delivery_type != "3") //98 : �ܺξ�ü�߼�		3:������
				updateOrderGoodsGroupNo($conn, $order_goods_no, $claim_order_goods_no, $cancel_qty);
			else
				if($claim_state != "8")
					updateOrderGoodsGroupNo($conn, $order_goods_no, $claim_order_goods_no, $cancel_qty);

			//2017-11-30 Ŭ���� ���� ���ֹ����� �Ǹűݾ� ����� ����ȭ�� ���� ��� ���ǿ��� Ŭ���ӿ� ���� ���ֹ��� ����
			//����� �׳� �׷����� ó��, 2018�� ���� ������ ������ ������ ��ġ �ʿ�
			updateOrderGoodsClaimNo($conn, $order_goods_no, $claim_order_goods_no);


			$inout_date = date("Y-m-d",strtotime("0 month"));
			//����
			$inout_type = "LR01";

			$claim_state_name = getDcodeName($conn, "ORDER_STATE", $claim_state);
			$TEMP_MEMO = $claim_state_name."(Ŭ����:".$claim_order_goods_no.")";


			$options = array('CLAIM_ORDER_GOODS_NO' => $claim_order_goods_no);


			$base_date = getDcodeExtByCode($conn, "LEDGER_SETUP", "BASE_DATE");

			if($base_date < $rs_reg_date && $order_state != 1) {
				insertCompanyLedger($conn, $rs_cp_no, $inout_date, $inout_type, $goods_no, $goods_name."[".$goods_code."]", -1 * $cancel_qty, $sale_price, null, 0, $cate_01, $tax_tf, $TEMP_MEMO, $reserve_no, $order_goods_no, "Ŭ���� ".$claim_state_name, null, $s_adm_no, $options);
			}

			// ����� ���� 2017-05-10
			//if($delivery_type != "98") { 
			//	updateStatusTStockCancel($conn, $order_goods_no, "Y", $cancel_qty);
			//}
		}



		//��ۼ��� - �۾��Ϸ� ���� ���ؼ� �۾��Ϸ� ó��
		if ($delivery_type != "98" && $order_state == "2" && (($claim_state == "6") || ($claim_state == "8"))) {

			$refund_able_qty = getRefundAbleQty($conn, $reserve_no, $order_goods_no);

			if($work_qty >= $refund_able_qty)
				updateWorksFlagYOrderGoods($conn, $order_goods_no);

		}

		if ($claim_state == "8") {

			$cart_seq++;

			//2018-08-08 ������ ������ ������ �� �� ���� ����
			//$delivery_price = "0";
			$new_order_state	= "1";
			// ��ȯ�� ��� 
			$cate_04 = "CHANGE";

			$memos = array('opt_request_memo' => $opt_request_memo, 'opt_support_memo' => $opt_support_memo);

			$new_order_goods_no = insertOrderGoods($conn, $on_uid, $reserve_no, $cp_order_no, $buy_cp_no, $mem_no, $cart_seq, $goods_no, $goods_code, $goods_name, $goods_sub_name, $cancel_qty, $opt_sticker_no, $opt_sticker_msg, $opt_outbox_tf, $delivery_cnt_in_box, $opt_wrap_no, $opt_print_msg, $opt_outstock_date, $opt_memo, $memos, $delivery_type, $delivery_cp, $sender_nm, $sender_phone, $cate_01, $cate_02, $cate_03, $cate_04, $price, $buy_price, $sale_price, $extra_price, $delivery_price, $sa_delivery_price, $discount_price, $sticker_price, $print_price, $sale_susu, $labor_price, $other_price, $tax_tf, $new_order_state, $use_tf, $s_adm_no);

		}

		//��ۿϷ� �����϶� ��ȯ�̳� ��ǰ�� ������ ���԰� �Է�
		/*
		if($order_state == "3" && ($claim_state == "7" || $claim_state == "8")) { 

			if($delivery_type != "98") { 

				$datetime_now = date("Y-m-d H:i:s",strtotime("0 month"));

				$stock_type = "IN";
				$stock_code = "FST02";
				$in_qty		= 0;
				$in_bqty	= 0;
				$in_fqty	= $cancel_qty;
				$in_cp_no   = $buy_cp_no; 
				$out_cp_no  = 0;
				$in_loc     = "LOCA"; 
				$in_loc_ext = getDcodeName($conn, "ORDER_STATE", $claim_state)." (".$datetime_now.")";
				$out_qty    = 0;
				$out_bqty   = 0;
				$out_tqty   = 0;
				$in_price   = $buy_price;
				$out_price  = 0;
				$in_date    = $datetime_now;
				$out_date   = "";
				$pay_date   = "";
				$close_tf   = "N"; 
				$memo       = $rs_r_mem_nm;
				$next_order_goods_no = ($claim_state == "7" ? $claim_order_goods_no : $new_order_goods_no);

				$result = insertStock($conn, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no,  $next_order_goods_no, NULL, $close_tf, $s_adm_no, $memo);

			}
		}
		*/


		//}

		/* ��� ���,��ȯ,��ǰ�� ��Ģ������ ȯ�� ó���� ���
		// ȯ���ΰ�� ȯ�� ���� �Է�

		if ($refund_type == "ȯ����") {

			$refund_state = "0";
						
			$result = insertRefund($conn, $refund_type, $refund_state, $cart_seq, $on_uid, $reserve_no, $rs_o_mem_nm, $buy_cp_no, $cms_depositor, $bank_amount,$bank_name, $bank_pay_account, $use_tf, $s_adm_no);
		}
		*/

		if ($claim_state <> "99") {

			$result = resetOrderInfor($conn, $reserve_no);
		}

		// �Ա��� ����� ��� �Աݾ��� ���� �Ѵ�..
		//if ($claim_state == "4") {
		//	$result = resetPaymentInfor($conn, $reserve_no);
		//}

		// Ŭ���� ���
		$bb_code		= "CLAIM";
		$writer_nm	= $s_adm_nm;
		$writer_pw	= $s_adm_no;
		$cate_01		= $reserve_no;
		$cate_02		= $claim_type;
		$cate_03		= $cart_seq;
		$cate_04		= $claim_state;
		$title			= $goods_name;
		$contents		= $claim_memo;
		$email			= $rs_o_mem_nm;
		$homepage		= $rs_r_mem_nm;
		$keyword		= $buy_cp_no;
		$file_size	= $cancel_qty;

		$contents = $contents.$s_adm_nm." (".$temp_date.")";
		$recomm = $order_goods_no;

		$new_bb_no =  insertBoard($conn, $bb_code, $cate_01, $cate_02, $cate_03, $cate_04, $writer_nm, $writer_pw, $email, $homepage, $title, $ref_ip, $recomm, $contents, $file_nm, $file_rnm, $file_path, $file_size, $file_ext, $keyword, $comment_tf, $use_tf, $s_adm_no);


		// �����, ����۰����� ���� ���
		for ($j = 0; $j < sizeof($sub_goods_id); $j++) {

			$goods_sub_no = $sub_goods_id[$j];
			$goods_cnt    = $sub_goods_cnt[$j];
			$in_out		  = $sub_in_out[$j];

			//�˻��� ���� Ŭ���ӽ� ������ �̸� ����
			$memo = $rs_r_mem_nm;

			if($chk_wrong == "Y") {
				if ($in_out == "OUT") {

					//����۽� �߸����� ���� ���԰� ó������
					//�߸����� ���� �޾ƿ;���  (F+)
					$stock_type     = "IN";          //����� ���� (�԰�) 
					$stock_code     = "FST02";       //���԰�
					$in_cp_no	    = $rs_cp_no;     // ����ü
					$out_cp_no	    = 0;
					$goods_no		= $goods_sub_no; //����ǰ ** ��Ʈ�� ��� �ش� ��Ʈ�� ��ǰ �� ��ŭ �� �� ó���ؾ� ��
					$in_loc			= "LOCD";        // ������ - Ŭ����
					$in_loc_ext	    = getDcodeName($conn, "CLAIM_TYPE", $claim_type);
					$in_qty			= 0;
					$in_bqty		= 0;
					$in_fqty		= $goods_cnt; //����ǰ ���� * �ֹ���
					$out_qty		= 0;
					$out_bqty		= 0;
					$out_fqty	    = 0;
					$in_price		= 0;
					$out_price	    = 0;     
					$in_date		= $this_date;
					$out_date		= "";
					$pay_date		= "";
					$close_tf		= "N";
					
					$new_stock_no = insertStock($conn, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_fqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no,  $order_goods_no, NULL, $close_tf, $s_adm_no, $memo);
					updateStockClaimNo($conn, $new_stock_no, $new_bb_no);

					//�߸����� ���� (-)
					$stock_type     = "OUT";         //����� ���� (���) 
					$stock_code     = "NOUT02";      //��Ÿ�ڵ�
					$in_cp_no	    = 0;
					$out_cp_no	    = $rs_cp_no;        // ����ü
					$goods_no		= $goods_sub_no; //����ǰ ** ��Ʈ�� ��� �ش� ��Ʈ�� ��ǰ �� ��ŭ �� �� ó���ؾ� ��
					$in_loc			= "LOCD";        // ������ - Ŭ����
					$in_loc_ext	    = getDcodeName($conn, "CLAIM_TYPE", $claim_type);
					$in_qty			= 0;
					$in_bqty		= 0;
					$in_fqty		= 0;
					$out_qty		= $goods_cnt; //����ǰ ���� * �ֹ���
					$out_bqty		= 0;
					$out_fqty	    = 0;
					$in_price		= 0;
					$out_price	    = 0;     
					$in_date		= "";
					$out_date		= $this_date;
					$pay_date		= "";
					$close_tf		= "N";

				} else {

					$stock_type     = "IN";         //����� ���� (�԰�) 
					$stock_code     = "NST99";      //��Ÿ�ڵ�
					$in_cp_no	    = $rs_cp_no;        // ����ü
					$goods_no		= $goods_sub_no; //����ǰ ** ��Ʈ�� ��� �ش� ��Ʈ�� ��ǰ �� ��ŭ �� �� ó���ؾ� ��
					$in_loc			= "LOCD";        // �԰���� - Ŭ����
					$in_loc_ext	    = getDcodeName($conn, "CLAIM_TYPE", $claim_type);
					$in_qty			= $goods_cnt; //����ǰ ���� * �ֹ���
					$in_bqty		= 0;
					$in_fqty		= 0;
					$out_qty		= 0;
					$out_bqty		= 0;
					$out_fqty	    = 0;
					$in_price		= 0;
					$out_price	    = 0;     
					$in_date		= $this_date;
					$out_date		= "";
					$pay_date		= "";
					$close_tf		= "N";
				}

			
				$new_stock_no = insertStock($conn, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_fqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $order_goods_no, NULL, $close_tf, $s_adm_no, $memo);
				updateStockClaimNo($conn, $new_stock_no, $new_bb_no);
			} 

			if($chk_missing == "Y") {

				if ($in_out == "OUT") {
					
					//�����ÿ��� ���������� ���� ������� �޾ƿ;� �� �� �������� ���԰� ó��
					$stock_type     = "IN";          //����� ���� (�԰�) 
					$stock_code     = "FST02";       //���԰�
					$in_cp_no	    = $rs_cp_no;     // ����ü
					$out_cp_no	    = 0;
					$goods_no		= $goods_sub_no; //����ǰ ** ��Ʈ�� ��� �ش� ��Ʈ�� ��ǰ �� ��ŭ �� �� ó���ؾ� ��
					$in_loc			= "LOCD";        // ������ - Ŭ����
					$in_loc_ext	    = getDcodeName($conn, "CLAIM_TYPE", $claim_type);
					$in_qty			= 0;
					$in_bqty		= 0;
					$in_fqty		= $goods_cnt; //����ǰ ���� * �ֹ���
					$out_qty		= 0;
					$out_bqty		= 0;
					$out_fqty	    = 0;
					$in_price		= 0;
					$out_price	    = 0;     
					$in_date		= $this_date;
					$out_date		= "";
					$pay_date		= "";
					$close_tf		= "N";

					$new_stock_no = insertStock($conn, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_fqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no,  $order_goods_no, NULL, $close_tf, $s_adm_no, $memo);
					updateStockClaimNo($conn, $new_stock_no, $new_bb_no);
				} else { 
					$stock_type     = "IN";         //����� ���� (�԰�) 
					$stock_code     = "NST99";      //��Ÿ�ڵ�
					$in_cp_no	    = $rs_cp_no;        // ����ü
					$goods_no		= $goods_sub_no; //����ǰ ** ��Ʈ�� ��� �ش� ��Ʈ�� ��ǰ �� ��ŭ �� �� ó���ؾ� ��
					$in_loc			= "LOCD";        // �԰���� - Ŭ����
					$in_loc_ext	    = getDcodeName($conn, "CLAIM_TYPE", $claim_type);
					$in_qty			= $goods_cnt; //����ǰ ���� * �ֹ���
					$in_bqty		= 0;
					$in_fqty		= 0;
					$out_qty		= 0;
					$out_bqty		= 0;
					$out_fqty	    = 0;
					$in_price		= 0;
					$out_price	    = 0;     
					$in_date		= $this_date;
					$out_date		= "";
					$pay_date		= "";
					$close_tf		= "N";

					$new_stock_no = insertStock($conn, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_fqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no,  $order_goods_no, NULL, $close_tf, $s_adm_no, $memo);
					updateStockClaimNo($conn, $new_stock_no, $new_bb_no);
				}

			}


			if($chk_return == "Y") {

				$stock_type     = "IN";          //����� ���� (�԰�) 
				$stock_code     = "FST02";       //���԰�
				$in_cp_no	    = $rs_cp_no;     // ����ü
				$out_cp_no	    = 0;
				$goods_no		= $goods_sub_no; //����ǰ ** ��Ʈ�� ��� �ش� ��Ʈ�� ��ǰ �� ��ŭ �� �� ó���ؾ� ��
				$in_loc			= "LOCD";        // ������ - Ŭ����
				$in_loc_ext	    = getDcodeName($conn, "CLAIM_TYPE", $claim_type);
				$in_qty			= 0;
				$in_bqty		= 0;
				$in_fqty		= $goods_cnt; //����ǰ ���� * �ֹ���
				$out_qty		= 0;
				$out_bqty		= 0;
				$out_fqty	    = 0;
				$in_price		= 0;
				$out_price	    = 0;     
				$in_date		= $this_date;
				$out_date		= "";
				$pay_date		= "";
				$close_tf		= "N";

				$new_stock_no = insertStock($conn, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_fqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no,  $order_goods_no, NULL, $close_tf, $s_adm_no, $memo);
				updateStockClaimNo($conn, $new_stock_no, $new_bb_no);

			}
		
		}
	
		if ($new_bb_no > 0) {
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

	$order_goods_no		= trim($order_goods_no);

	$del_tf = "N";
	
#===============================================================
# Get Search list count
#===============================================================

	$arr_rs = selectOrderGoods($conn, $order_goods_no);

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

var click_flag = "N";

function js_save() {
	
	var frm = document.frm;
	
	if (frm.claim_state.value == "") {
		alert("Ŭ������ �����ϼ���");
		frm.claim_state.focus();
		return;
	}

	/*
	if (frm.refund_type.value == "") {
		alert("ȯ�� ���θ� �����ϼ���");
		frm.refund_type.focus();
		return;
	}
	*/
	/*
	if (frm.refund_type.value == "ȯ����") {
	
		if (frm.bank_name.value.trim() == "") {
			alert("ȯ�� ���ฦ �Է��ϼ���");
			frm.bank_name.focus();
			return;
		}

		if (frm.bank_pay_account.value.trim() == "") {
			alert("ȯ�� ���� ���¹�ȣ�� �Է��ϼ���");
			frm.bank_pay_account.focus();
			return;
		}

		if (frm.cms_depositor.value.trim() == "") {
			alert("�����ָ� �Է��ϼ���");
			frm.cms_depositor.focus();
			return;
		}
	
	}
	*/

	if (frm.claim_type.value == "") {
		alert("Ŭ���� ������ �����ϼ���");
		frm.claim_type.focus();
		return;
	}
	
	if (click_flag == "N") {
		frm.mode.value = "I";
		frm.target = "";
		frm.action = "<?=$_SERVER['PHP_SELF']?>";
		click_flag = "Y";
		frm.submit();
	} else {
		alert("ó�� �� �Դϴ�. ȭ���� 1�� �̻� ���߽� ��� â�� �����ð� �ٽ� ó���� �ֽñ� �ٶ��ϴ�.");
		return;
	}
}

function js_pop_individual(reserve_no, order_goods_no) { 

	var frm = document.frm;
	
	var url = "../order/pop_individual_delivery_list.php?reserve_no="+reserve_no+"&order_goods_no="+order_goods_no+"&search_str=";

	NewWindow(url, 'pop_individual_delivery_list','1000','600','YES');
}

/*
function js_refund_type() {

	var total_refund = 0;
	var frm = document.frm;

	if (frm.refund_type.value == "ȯ����") {
		total_refund = (frm.sale_price.value * frm.cancel_qty.value) + (frm.extra_price.value * frm.cancel_qty.value);
	}
	
	frm.bank_amount.value = total_refund;
}
*/

</script>

<script type="text/javascript">

	// tag ���� ���̾ �� �ε� �Ǳ������� �Ǻ��ϱ� ���� �ʿ�
	var tag_flag = "0";

	var checkFirst1 = false;
	var checkFirst2 = false;
	var lastKeyword1 = '';
	var lastKeyword2 = '';
	var loopSendKeyword1 = false;
	var loopSendKeyword2 = false;
	$(function(){
		$('input[name=search_name1]').on('change keydown',function (){

			$(".outstock").css("position","relative");
			$(".instock").css("position","static");

			if ((event.keyCode == 8) || (event.keyCode == 46)) {
				checkFirst1 = false;
				loopSendKeyword1 = false;
			}

			if (checkFirst1 == false) {
				setTimeout("sendKeyword();", 100);
				loopSendKeyword1 = true;
			}
			checkFirst1 = true;
		});

		$('input[name=search_name2]').on('change keydown',function (){

			$(".outstock").css("position","static");
			$(".instock").css("position","relative");

			if ((event.keyCode == 8) || (event.keyCode == 46)) {
				checkFirst2 = false;
				loopSendKeyword2 = false;
			}

			if (checkFirst2 == false) {
				setTimeout("sendKeyword2();", 100);
				loopSendKeyword2 = true;
			}
			checkFirst2 = true;
		});
	});

	function sendKeyword() {
		
		var frm = document.frm;

		if (loopSendKeyword1 == false) return;

		var keyword = document.frm.search_name1.value;
		
		if (keyword == '') {
			
			lastKeyword1 = '';
		
			hide('suggest');

		} else if (keyword != lastKeyword1) {

			lastKeyword1 = keyword;
				
			if (keyword != '') {
				console.log(keyword);
				frm.search_name.value = keyword;
				frm.mode.value = "1";
				frm.action = "/manager/goods/search_goods.php";
				frm.target = "ifr_hidden";
				frm.submit();
				
			} else {
				hide('suggest');
			}
		}
		setTimeout("sendKeyword();", 100);
	}

	function sendKeyword2() {
		
		var frm = document.frm;

		if (loopSendKeyword2 == false) return;

		var keyword = document.frm.search_name2.value;
		
		if (keyword == '') {
			
			lastKeyword2 = '';
		
			hide('suggest');

		} else if (keyword != lastKeyword2) {

			lastKeyword2 = keyword;
				
			if (keyword != '') {
				console.log(keyword);
				frm.search_name.value = keyword;
				frm.mode.value = "2";
				frm.action = "/manager/goods/search_goods.php";
				frm.target = "ifr_hidden";
				frm.submit();
				
			} else {
				hide('suggest');
			}
		}
		setTimeout("sendKeyword2();", 100);
	}


	function displayResult(str, mode) {
				
		var resultText = str;
		
		var result = resultText.split('|');

		var count = parseInt(result[0]);

		var keywordList = null;
		var arr_keywordList = null;

		if (count > 0) {
					
			keywordList = result[1].split('^');
			
			var html = '';
					
			for (var i = 0 ; i < keywordList.length ; i++) {
						
				arr_keywordList = keywordList[i].split('');
				
				html += "<table width='100%' border='0'>";
				html += "<tr>";
				html += "<td style='padding:0px 5px 0px 5px' width='55px'><img src='"+arr_keywordList[2]+"' width='50' height='50' border='0'></td>";
				html += "<td>" +"<a href=\"javascript:js_select('"+ arr_keywordList[0]+"','"+arr_keywordList[1]+"', '" + mode + "')\">"+arr_keywordList[1]+"</a>" + "</td>";
				html += "<td width='55px'>"+arr_keywordList[8]+"</td>";
				html += "<td width='105px'>�ǸŰ� : "+arr_keywordList[3]+"</td>";
				html += "</tr>";
				html += "</table>";
		
			}

			var listView = document.getElementById('suggestList' + mode);
			listView.innerHTML = html;

			var elmt_Suggest = document.getElementById('suggest' + mode);
					
			elmt_Suggest.style.visibility  ="visible"; 
		} else {
			elmt_Suggest.style.visibility  ="hidden"; 
		}
	}

	function js_select(selectedKey,selectedKeyword, mode) {

		var frm = document.frm;

		if(mode == "1")
			frm.search_name1.value = selectedKeyword;
		else 
			frm.search_name2.value = selectedKeyword;

		arr_keywordValues = selectedKey.split('');

		//frm.goods_name.value					= arr_keywordValues[0];
		//frm.goods_no.value						= arr_keywordValues[1];
        // arr_keywordValues[2]; ���ް�
		// arr_keywordValues[3]; �ǸŰ�
		var sub_goods_ids = frm.elements['sub_goods_id[]'];
		var sub_mode = frm.elements['sub_mode[]'];
		if(sub_goods_ids != undefined)
		{
			if(sub_goods_ids.value == arr_keywordValues[1] && sub_mode.value == mode) 
			{
				alert('�̹� �߰��� ��ǰ�Դϴ�');
				return;
			}
			for (var i = 0; i < sub_goods_ids.length; i++) {
				if(sub_goods_ids[i].value == arr_keywordValues[1] && sub_mode[i].value == mode){
					alert('�̹� �߰��� ��ǰ�Դϴ�');
					return;
				}
			}
		}


		$(".sub_goods_list" + mode).append("<tr><th>��ǰ��</th><td class='line'><input type='hidden' name='sub_mode[]' value='" + mode + "'>" + arr_keywordValues[0] + "[<a href=\"javascript:js_goods_view('"+ arr_keywordValues[1] + "')\">"+ arr_keywordValues[1] + "</a>]" + "<input type='hidden' name='sub_goods_id[]' value='" + arr_keywordValues[1] + "'><input type='hidden' name='sub_in_out[]' value='" + (mode == "1" ? "OUT" : "IN") + "'></td><th>����</th><td class='line'><input type='text' name='sub_goods_cnt[]' class='txt' style='width:80px' value='1'>��</td><td style='border-underline:1px solid #d2dfe5'><span class='remove_sub' style='color:#478fb2; cursor:pointer; font-weight:bold; text-decoration:underline;'>����</span></td></tr>");

		if(mode == "1")
		{
			loopSendKeyword1 = false;
			checkFirst1 = false;
		}else { 
			loopSendKeyword2 = false;
			checkFirst2 = false;
		}
		hide('suggest' + mode);

	}

	function show(elementId) {
		var element = document.getElementById(elementId);
		
		if (element) {
			element.style.visibility  ="visible"; 
			//element.style.display = '';
		}
	}

	function hide(elementId) {
		var element = document.getElementById(elementId);
		if (element) {
			element.style.visibility  ="hidden"; 
			//element.style.display = 'none';
		}
	}

$(function(){
	$('body').on('click', '.remove_sub', function() {
		$(this).closest("tr").remove();
	});
});
</script>
<style>
	table.rowstable01 th.cancel {background-color: #ff8080; color:white;} 
	table.rowstable01 td.cancel {background-color: #ffb3b3;}
	.warning_delivery_type {color:red; font-weight:bold; line-height:1.5em; text-align:center; margin-top:5px;} 
</style>
</head>

<body id="popup_order">

<div id="popupwrap_order">
	<h1>Ŭ���� ���</h1>
	<div id="postsch_file">
		<form name="frm" method="post">
		<input type="hidden" name="search_name" value="">
		<input type="hidden" name="mode" value="">
		<input type="hidden" name="order_goods_no" value="<?=$order_goods_no?>">
		<h2>* �ֹ� ��ǰ</h2>
		<table cellpadding="0" cellspacing="0" class="rowstable01" style="width:98%" border="0">
			<colgroup>
			<col width="7%" />
			<col width="33%" />
			<col width="6%" />
			<col width="10%" />
			<col width="10%" />
			<col width="22%" />
			<col width="10%" />
		</colgroup>
		<tr>
			<th rowspan="2">��ǰ�ڵ�</th>
			<th>��ǰ��</th>
			<th rowspan="2">����</th>
			<th>�ݾ�</th>
			<th rowspan="2">�հ�</th>
			<th>�ֹ�����</th>
			<th rowspan="2" class="end cancel" >��Ҽ���</th>
		</tr>
		<tr>
			<th>�ɼ�</th>
			<th>�߰���ۺ�</th>
			<th>�������</th>
		</tr>
	<?
		$nCnt = 0;
		$total_sum_price = 0;
		$sum_qty = 0;
		
		if (sizeof($arr_rs) > 0) {
			for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
				
				$ORDER_GOODS_NO				= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
				$ON_UID						= trim($arr_rs[$j]["ON_UID"]);
				$MEM_NO						= trim($arr_rs[$j]["MEM_NO"]);
				$CP_ORDER_NO				= trim($arr_rs[$j]["CP_ORDER_NO"]);
				$BUY_CP_NO					= trim($arr_rs[$j]["BUY_CP_NO"]);
				$RESERVE_NO					= trim($arr_rs[$j]["RESERVE_NO"]);
				$GOODS_NO					= trim($arr_rs[$j]["GOODS_NO"]);
				$GOODS_CODE					= trim($arr_rs[$j]["GOODS_CODE"]);
				$GOODS_SUB_NAME				= trim($arr_rs[$j]["GOODS_SUB_NAME"]);
				
				$GOODS_NAME					= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
				$PRICE						= trim($arr_rs[$j]["PRICE"]);
				$BUY_PRICE					= trim($arr_rs[$j]["BUY_PRICE"]);
				$SALE_PRICE					= trim($arr_rs[$j]["SALE_PRICE"]);
				$EXTRA_PRICE				= trim($arr_rs[$j]["EXTRA_PRICE"]);
				$DELIVERY_PRICE				= trim($arr_rs[$j]["DELIVERY_PRICE"]);

				$SA_DELIVERY_PRICE 			= trim($arr_rs[$j]["SA_DELIVERY_PRICE"]);
				$DISCOUNT_PRICE 			= trim($arr_rs[$j]["DISCOUNT_PRICE"]);
				$STICKER_PRICE 				= trim($arr_rs[$j]["STICKER_PRICE"]);
				$PRINT_PRICE 				= trim($arr_rs[$j]["PRINT_PRICE"]);
				$SALE_SUSU 					= trim($arr_rs[$j]["SALE_SUSU"]);
				$TAX_TF						= trim($arr_rs[$j]["TAX_TF"]);
				$LABOR_PRICE 				= trim($arr_rs[$j]["LABOR_PRICE"]);
				$OTHER_PRICE 				= trim($arr_rs[$j]["OTHER_PRICE"]);

				$CATE_01					= trim($arr_rs[$j]["CATE_01"]);
				$CATE_02					= trim($arr_rs[$j]["CATE_02"]);
				$CATE_03					= trim($arr_rs[$j]["CATE_03"]);
				$CATE_04					= trim($arr_rs[$j]["CATE_04"]);

				$SUM_PRICE					= trim($arr_rs[$j]["SUM_PRICE"]);
				$PLUS_PRICE					= trim($arr_rs[$j]["PLUS_PRICE"]);
				$GOODS_LEE					= trim($arr_rs[$j]["LEE"]);
				$QTY						= trim($arr_rs[$j]["QTY"]);
				$REQ_DATE					= trim($arr_rs[$j]["PAY_DATE"]);
				$END_DATE					= trim($arr_rs[$j]["FINISH_DATE"]);
				$ORDER_STATE				= trim($arr_rs[$j]["ORDER_STATE"]);
				$DELIVERY_CP				= trim($arr_rs[$j]["DELIVERY_CP"]);
				$DELIVERY_NO				= trim($arr_rs[$j]["DELIVERY_NO"]);
				$SENDER_NM					= trim($arr_rs[$j]["SENDER_NM"]);
				$SENDER_PHONE				= trim($arr_rs[$j]["SENDER_PHONE"]);
				
				$OPT_STICKER_NO  = trim($arr_rs[$j]["OPT_STICKER_NO"]);
				$OPT_STICKER_MSG = trim($arr_rs[$j]["OPT_STICKER_MSG"]);
				$OPT_OUTBOX_TF		= trim($arr_rs[$j]["OPT_OUTBOX_TF"]);
				$DELIVERY_CNT_IN_BOX= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);
				$OPT_WRAP_NO		= trim($arr_rs[$j]["OPT_WRAP_NO"]);
				$OPT_PRINT_MSG		= trim($arr_rs[$j]["OPT_PRINT_MSG"]);
				$OPT_OUTSTOCK_DATE	= trim($arr_rs[$j]["OPT_OUTSTOCK_DATE"]);
				$OPT_OUTSTOCK_DATE	= date('Y-m-d', strtotime($OPT_OUTSTOCK_DATE));
				$OPT_MEMO			= trim($arr_rs[$j]["OPT_MEMO"]);
				$OPT_REQUEST_MEMO	= trim($arr_rs[$j]["OPT_REQUEST_MEMO"]);
				$OPT_SUPPORT_MEMO	= trim($arr_rs[$j]["OPT_SUPPORT_MEMO"]);
				$DELIVERY_TYPE		= trim($arr_rs[$j]["DELIVERY_TYPE"]);
				$WORK_QTY			= trim($arr_rs[$j]["WORK_QTY"]);

				$refund_able_qty = getRefundAbleQty($conn, $RESERVE_NO, $ORDER_GOODS_NO);
			?>

			<tr>
				<td rowspan="2"><?= $GOODS_NO?></td>
				<td class="modeual_nm" height="35"><?=$GOODS_NAME?> <?=$GOODS_SUB_NAME?> [<?=$GOODS_CODE?>]</td>
				<td rowspan="2" class="price"><?=$QTY?></td>
				<td class="price">
					<?=number_format($SALE_PRICE)?>
					<input type="hidden" name="on_uid" value="<?=$ON_UID?>">
					<input type="hidden" name="reserve_no" value="<?=$RESERVE_NO?>">
					<input type="hidden" name="cp_order_no" value="<?=$CP_ORDER_NO?>">
					<input type="hidden" name="mem_no" value="<?=$MEM_NO?>">
					<input type="hidden" name="buy_cp_no" value="<?=$BUY_CP_NO?>">
					<input type="hidden" name="mem_no" value="<?=$MEM_NO?>">
					<input type="hidden" name="goods_no" value="<?=$GOODS_NO?>">
					<input type="hidden" name="goods_name" value="<?=$GOODS_NAME?>">
					<input type="hidden" name="goods_code" value="<?=$GOODS_CODE?>">
					<input type="hidden" name="goods_sub_name" value="<?=$GOODS_SUB_NAME?>">
					<input type="hidden" name="price" value="<?=$PRICE?>">
					<input type="hidden" name="buy_price" value="<?=$BUY_PRICE?>">
					<input type="hidden" name="sale_price" value="<?=$SALE_PRICE?>">
					<input type="hidden" name="extra_price" value="<?=$EXTRA_PRICE?>">
					<input type="hidden" name="delivery_price" value="<?=$DELIVERY_PRICE?>">
					<input type="hidden" name="sa_delivery_price" value="<?=$SA_DELIVERY_PRICE?>">
					<input type="hidden" name="discount_price" value="<?=$DISCOUNT_PRICE?>">
					<input type="hidden" name="sticker_price" value="<?=$STICKER_PRICE?>">
					<input type="hidden" name="print_price" value="<?=$PRINT_PRICE?>">
					<input type="hidden" name="sale_susu" value="<?=$SALE_SUSU?>">
					<input type="hidden" name="labor_price" value="<?=$LABOR_PRICE?>">
					<input type="hidden" name="other_price" value="<?=$OTHER_PRICE?>">

					<input type="hidden" name="order_state" value="<?=$ORDER_STATE?>">

					<input type="hidden" name="cate_01" value="<?=$CATE_01?>">
					<input type="hidden" name="cate_02" value="<?=$CATE_02?>">
					<input type="hidden" name="cate_03" value="<?=$CATE_03?>">
					<input type="hidden" name="cate_04" value="<?=$CATE_04?>">

					<input type="hidden" name="sender_nm" value="<?=$SENDER_NM?>">
					<input type="hidden" name="sender_phone" value="<?=$SENDER_PHONE?>">

					<input type="hidden" name="opt_sticker_no" value="<?=$OPT_STICKER_NO?>">
					<input type="hidden" name="opt_sticker_msg" value="<?=$OPT_STICKER_MSG?>">
					<input type="hidden" name="opt_outbox_tf" value="<?=$OPT_OUTBOX_TF?>">
					<input type="hidden" name="delivery_cnt_in_box" value="<?=$DELIVERY_CNT_IN_BOX?>">
					<input type="hidden" name="opt_wrap_no" value="<?=$OPT_WRAP_NO?>">
					<input type="hidden" name="opt_print_msg" value="<?=$OPT_PRINT_MSG?>">
					<input type="hidden" name="opt_outstock_date" value="<?=$OPT_OUTSTOCK_DATE?>">
					<input type="hidden" name="opt_memo" value="<?=$OPT_MEMO?>">
					<input type="hidden" name="opt_request_memo" value="<?=$OPT_REQUEST_MEMO?>">
					<input type="hidden" name="opt_support_memo" value="<?=$OPT_SUPPORT_MEMO?>">
					<input type="hidden" name="delivery_type" value="<?=$DELIVERY_TYPE?>">
					<input type="hidden" name="delivery_cp" value="<?=$DELIVERY_CP?>">
					
					<input type="hidden" name="tax_tf" value="<?=$TAX_TF?>">
					<input type="hidden" name="refund_able_qty" value="<?=$refund_able_qty?>">
					<input type="hidden" name="work_qty" value="<?=$WORK_QTY?>">
					
				</td>
				<td rowspan="2" class="price"><?=number_format($SALE_PRICE * $QTY - $DISCOUNT_PRICE + $SA_DELIVERY_PRICE)?></td>
				<td class="filedown"><?=getDcodeName($conn, "ORDER_STATE", $ORDER_STATE);?></td>
				<td rowspan="2" class="cancel">
					<? if ($refund_able_qty <=0 ) { ?>
					<input type="hidden" name="cancel_qty" value = "0">
					0
					<? } else {?>
					<select name="cancel_qty" <!--onChange="js_refund_type();" --> >
						<? for ($c = 1 ; $c <= $refund_able_qty ; $c++) { ?>
						<option value="<?=$c?>"><?=$c?></option>
						<? } ?>
					</select>
					<? }?>
				</td>
			</tr>
			<tr>
				<td class="filedown"><?=$option_str ?>&nbsp;</td>
				<td class="price" height="35"><?=number_format($SA_DELIVERY_PRICE)?></td>
				<td>
					<? 
						$arr_delivery = listOrderDelivery($conn, $RESERVE_NO, $order_goods_no);
						for ($k = 0 ; $k < sizeof($arr_delivery); $k++) { 
							$rs_delivery_seq = $arr_delivery[$k]["DELIVERY_SEQ"];
							$rs_delivery_cp  = $arr_delivery[$k]["DELIVERY_CP"];
							$rs_delivery_no  = $arr_delivery[$k]["DELIVERY_NO"];
						
					?>
						<?=getDeliveryLink($conn, $rs_delivery_cp, $rs_delivery_no)."<br/>"?>
					<?

						}
					
					?>
				</td>
			</tr>

			<?
			}
		}else{
			?>
			<tr>
				<td height="50" align="center" colspan="10">�����Ͱ� �����ϴ�. </td>
			</tr>
		<?
			}
		?>
		</table>

		<? if($DELIVERY_TYPE == "98") { ?>
			<div class="warning_delivery_type">"�ܺξ�ü �߼�" : ���� ������ �ڵ� ������� �ʽ��ϴ�. ���ּ� Ȯ���� �ּ���.</div>
		<? } ?>
		<? if($DELIVERY_TYPE == "3") { ?>
			<div class="warning_delivery_type">"���� �ù�" : ���� ������ ���� ���� ����� �������ּ���. �ڵ� �������� �ʽ��ϴ�. <input type="button" name="b" onclick="js_pop_individual('<?=$RESERVE_NO?>', '<?=$order_goods_no?>');" value="����� Ȯ��"/></div>
		<? } ?>

		<h2>* Ŭ���� ���</h2>

		<table cellpadding="0" cellspacing="0" border="0" class="colstable01" style="width:98%">
			<colgroup>
				<col width="20%">
				<col width="30%">
				<col width="20%">
				<col width="30%">
			</colgroup>
				<tr>
					<th>Ŭ���� ����</th>
					<td colspan="3" >
						<?
							if ($ORDER_STATE == "0") {
								$condition = "AND DCODE IN ('4','99')";
							} else if ($ORDER_STATE == "1") {
								$condition = "AND DCODE IN ('6','99')";
							} else if ($ORDER_STATE == "2") {
								$condition = "AND DCODE IN ('6', '8','99')";
							} else if ($ORDER_STATE == "3") {
								$condition = "AND DCODE IN ('7', '8','99')";
							}
						?>
						<?=makeSelectBoxWithCondition($conn,"ORDER_STATE", "claim_state","200", "Ŭ������ �����ϼ���.", "", $rs_claim_state, $condition );?>
					</td>
				</tr>
				<!-- ��� ��ǰ�� �⺻ ������ ���, ��ȯ, ��ǰ�� ȯ�� ó�������� ���� 2017-05-04
				<tr>
					<th>ȯ�� ����</th>
					<td>
						<?=makeSelectBoxOnChange($conn,"REFUND_TYPE", "refund_type","200", "ȯ�� ���θ� �����ϼ���.", "", $rs_refund_type)?>
					</td>
					<th>ȯ�� �ݾ�</th>
					<td>
						<input type="text" name="bank_amount" value = "<?=$rs_bank_amount?>" class="txt" style="width:35%;" onkeyup="return isPhoneNumber(this)" >
					</td>
				</tr>
				-->
				<!--
				<tr>
					<th>ȯ�� ����</th>
					<td>
						<input type="text" name="bank_name" value = "<?=$rs_bank_name?>" class="txt" style="width:75%;" >
					</td>
					<th>���� ��ȣ</th>
					<td>
						<input type="text" name="bank_pay_account" value = "<?=$rs_bank_pay_account?>" class="txt" style="width:75%;" >
					</td>
				</tr>
				<tr>
					<th>ȯ�� ������</th>
					<td colspan="3">
						<input type="text" name="cms_depositor" value = "<?=$rs_cms_depositor?>" class="txt" style="width:80px;" >
					</td>
				</tr>
				-->
				<tr>
					<th>Ŭ���� ����</th>
					<td colspan="3">
						<?=makeSelectBox($conn,"CLAIM_TYPE", "claim_type","200", "Ŭ���� ������ �����ϼ���.", "", $rs_claim_type)?>
						
						<script>
						$(function(){

							claim_options_all = $("select[name=claim_type] option").clone();

							$("select[name=claim_state]").change(function(){

								var claim_type = $("select[name=claim_type]").find('option').remove().end();
								if($(this).val() == "6") //���
								{
									claim_options_all.each(function(index, item){

										if(item.value.indexOf("CC") == 0)
											$("select[name=claim_type]").append(item);

									});
								}
								else if($(this).val() == "7") //��ǰ
								{
									claim_options_all.each(function(index, item){

										if(item.value.indexOf("CR") == 0)
											$("select[name=claim_type]").append(item);

									});
								}
								else if($(this).val() == "8") //��ȯ
								{
									claim_options_all.each(function(index, item){

										if(item.value.indexOf("CE") == 0)
											$("select[name=claim_type]").append(item);

									});
								}
								else if($(this).val() == "99") //��Ÿ
								{
									claim_options_all.each(function(index, item){

										if(item.value.indexOf("CX") == 0)
											$("select[name=claim_type]").append(item);

									});
								}
							});

						});
						</script>
					</td>
				</tr>
				<tr>
					<th>Ŭ���� �޸�</th>
					<td colspan="3">
						<textarea name="claim_memo" style="width:95%; height:60px"></textarea>
					</td>
				<tr>
				<tr>
					<th>��� ����</th>
					<td colspan="3">
						<label><input type="checkbox" name="chk_wrong" value="Y"/>�����</label>
						<label><input type="checkbox" name="chk_missing" value="Y"/>����</label>
						<label><input type="checkbox" name="chk_return" value="Y"/>�ļ�/ȸ��</label>
						<br/><br/>
						
						<table cellpadding="0" cellspacing="0" border="0" class="colstable01 restock_table" style="width:98%; display:none;">
							<colgroup>
								<col width="15%">
								<col width="35%">
								<col width="15%">
								<col width="35%">
							</colgroup>
							<tr class="in">
								<th><span class="plus_msg"></span></th>
								<td colspan="3" style="position:relative" class="line instock">
									<div id="suggest2" style="position:absolute; left:15px; top:25px; width:95%; height:81px; z-index:0; visibility: hidden; width:95%; ">
										<div id="suggestList2" style="position:relative; border:solid 1px #cec6ba; background:#FFFFFF; width:95%;"></div>
									</div>
									<input type="text" class="txt search_name" style="width:95%; ime-mode:Active;" name="search_name2" value=""  onFocus="this.value='';" />

									<table cellpadding="0" cellspacing="0" class="colstable02" style="margin-top:5px;">
									<colgroup>
										<col width="12.5%" />
										<col width="30%" />
										<col width="12.5%" />
										<col width="30%" />
										<col width="*%" />
									</colgroup>
									<thead>
										<tr>
											<th colspan="5" class="line">��ǰ�� �˻��ؼ� �������ּ���</th>
										</tr>
									</thead>
									<tbody class="sub_goods_list2">
									</tbody>
									</table>
								</td>
							</tr>
							<tr class="out">
								<th><span class="minus_msg"></span></th>
								<td colspan="3" style="position:relative" class="line outstock">
									<div id="suggest1" style="position:absolute; left:15px; top:25px; width:95%; height:81px; z-index:0; visibility: hidden; width:95%; ">
										<div id="suggestList1" style="position:relative; border:solid 1px #cec6ba; background:#FFFFFF; width:95%;"></div>
									</div>
									<input type="text" class="txt search_name" style="width:95%; ime-mode:Active;" name="search_name1" value=""  onFocus="this.value='';" />

									<table cellpadding="0" cellspacing="0" class="colstable02" style="margin-top:5px;">
									<colgroup>
										<col width="12.5%" />
										<col width="30%" />
										<col width="12.5%" />
										<col width="30%" />
										<col width="*%" />
									</colgroup>
									<thead>
										<tr>
											<th colspan="5" class="line">��ǰ�� �˻��ؼ� �������ּ���</th>
										</tr>
									</thead>
									<tbody class="sub_goods_list1">
									</tbody>
									</table>
								</td>
							</tr>									
						</table>

					</td>
				<tr>
				<script>
					$(function(){

						$("input[name=chk_wrong]").change(function(){

							$("input[name=chk_missing]").prop("checked", false);
							$("input[name=chk_return]").prop("checked", false);

							$(".plus_msg").html("���ֹ����� ����(+)");
							$(".minus_msg").html("�߸����� ����(-)");

							if($(this).is(":checked")) { 

								$(".restock_table").show();
								$(".in, .out").show();

								$(".sub_goods_list1 > tr").remove();
								$(".sub_goods_list2 > tr").remove();
								$(".search_name").val('');

							}else { 

								$(".restock_table").hide();
								$(".in, .out").hide();

								$(".sub_goods_list1 > tr").remove();
								$(".sub_goods_list2 > tr").remove();
								$(".search_name").val('');

							}

						});

						$("input[name=chk_missing]").change(function(){

							$("input[name=chk_wrong]").prop("checked", false);
							$("input[name=chk_return]").prop("checked", false);

							$(".plus_msg").html("�ȳ��� ����(+)");
							$(".minus_msg").html("���ƿ� ����(+F)");

							if($(this).is(":checked")) { 

								$(".restock_table").show();
								$(".in").show();
								$(".out").show();

								$(".sub_goods_list1 > tr").remove();
								$(".sub_goods_list2 > tr").remove();
								$(".search_name").val('');

							}else { 

								$(".restock_table").hide();
								$(".in").hide();
								$(".out").hide();

								$(".sub_goods_list1 > tr").remove();
								$(".sub_goods_list2 > tr").remove();
								$(".search_name").val('');

							}

						});

						$("input[name=chk_return]").change(function(){

							$("input[name=chk_wrong]").prop("checked", false);
							$("input[name=chk_missing]").prop("checked", false);

							$(".plus_msg").html("���ƿ� ����(+F)");
							$(".minus_msg").html("");

							if($(this).is(":checked")) { 

								$(".restock_table").show();
								$(".in").show();
								$(".out").hide();

								$(".sub_goods_list1 > tr").remove();
								$(".sub_goods_list2 > tr").remove();
								$(".search_name").val('');

							}else { 

								$(".restock_table").hide();
								$(".in").hide();
								$(".out").hide();

								$(".sub_goods_list1 > tr").remove();
								$(".sub_goods_list2 > tr").remove();
								$(".search_name").val('');

							}

						});
					});

				
				</script>
				
		</table>
		<div class="sp10"></div>
		<div class="btn">
		<? if ($sPageRight_U == "Y") {?>
			<? if ($refund_able_qty <=0 ) { ?>
			<a href="javascript:js_save();"><img src="../images/admin/btn_regist_02.gif" alt="���" /></a>
			<? } else {?>
			<a href="javascript:js_save();"><img src="../images/admin/btn_regist_02.gif" alt="���" /></a>
			<? } ?>
		<? } ?>
		</div>
		<div class="sp35"></div>


</div>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
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