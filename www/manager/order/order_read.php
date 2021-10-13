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
	$menu_right = "OD005"; // �޴����� ���� �� �־�� �մϴ�

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
	require "../../_classes/biz/company/company.php";
	require "../../_classes/biz/confirm/confirm.php";
	require "../../_classes/biz/work/work.php";
	require "../../_classes/biz/email/email.php";

	//���Ϲ߼�

	if($mode == "CANCEL_ORDER_CONFIRM"){
		$query="UPDATE TBL_ORDER_GOODS 
				SET ORDER_CONFIRM_ADM='',
					ORDER_CONFIRM_DATE=NULL,
					SALE_CONFIRM_TF='N',
					SALE_CONFIRM_DATE='0000-00-00 00:00:00',
					SALE_CONFIRM_YMD='',
					ORDER_STATE='1'
				WHERE ORDER_GOODS_NO ='".$cancel_order_goods_no."' ;
				";
		// echo $query."<br>";
		// exit;
		if(!mysql_query($query,$conn)) {

			echo "<script>alert(\"tbl_order_goods process error- ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			return false;
			exit;
		} 

		$query1="UPDATE TBL_COMPANY_LEDGER
					SET USE_TF='N',
						DEL_TF='Y',
						DEL_DATE=now(),
						DEL_ADM='".$s_adm_no."'
					WHERE ORDER_GOODS_NO='".$cancel_order_goods_no."'
					  AND INOUT_TYPE='����' ;
						";
		// echo $query1."<br>";
		// exit;
		if(!mysql_query($query1,$conn)) {

			echo "<script>alert(\"tbl_company_ledger process errror- ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			return false;
			exit;
		} 

	}
	if($mode == "SENDING_EMAIL") { 

		//echo $sending_email." // ".$print_type." // ".$op_cp_no." // ".$print_date." // ".$reserve_no."<br/>";	

		switch($print_type) { 
			case "1" : 
			case "2" :
				break;
			case "3" :
				$download_url = "https://".$_SERVER['HTTP_HOST']."/manager/confirm/estimate_sheet_excel.php";
				$file_title = "������";
				break;
			case "4" :
				$download_url = "https://".$_SERVER['HTTP_HOST']."/manager/confirm/transaction_statement_excel.php";
				$file_title = "�ŷ�����";
				break;
			case "5" :
				$download_url = "https://".$_SERVER['HTTP_HOST']."/manager/confirm/estimate_and_transaction_excel.php";
				$file_title = "����_�ŷ�����";
				break;
			case "6" :
				$download_url = "https://".$_SERVER['HTTP_HOST']."/manager/confirm/transaction_statement_with_balance_excel.php";
				$file_title = "�ŷ�����_�ܾ�����";
				break;
			case "7" :
				break;
			case "8" :
				$download_url = "https://".$_SERVER['HTTP_HOST']."/manager/confirm/order_statement_delivery_no_excel.php";
				$file_title = "�ֹ���ǰ_�����ȣ";
				break;
			case "9" :
				$download_url = "https://".$_SERVER['HTTP_HOST']."/manager/confirm/company_ledger_excel_list.php";
				$file_title = "�ŷ�����";
				break;

		}

		$download_url .= "?reserve_no=".base64url_encode($reserve_no)."&print_type=".base64url_encode($print_type)."&print_date=".base64url_encode($print_date)."&op_cp_no=".base64url_encode($op_cp_no)."&cp_no=".base64url_encode($cp_no);
		$path = $_SERVER["DOCUMENT_ROOT"]."/upload_data/temp_mail";
		$filename = "����Ʈ��_".$file_title."_".$reserve_no.".xls";
		$file = $path . "/" . $filename;
		
		downloadFile($download_url, $file);

		//���ü�� �ƴҰ�� �ش� ��ü�� ��������� �� - ������ ���ü�� ����Ѵٰ� ����
		$arr_op_cp = getOperatingCompany($conn, $s_adm_com_code);
		$OP_CP_NM = $arr_op_cp[0]["CP_NM"]." ".$arr_op_cp[0]["CP_NM2"];
		$OP_CEO_NM = $arr_op_cp[0]["CEO_NM"];
		$OP_CP_ADDR = $arr_op_cp[0]["CP_ADDR"];
		$OP_CP_PHONE = $arr_op_cp[0]["CP_PHONE"];
		$OP_EMAIL = $arr_op_cp[0]["EMAIL"];

		if($sending_email <> "") {
		
			include('../../_PHPMailer/class.phpmailer.php');

			$sent_email = str_replace(";", ",", $sent_email);

			$error_msg = mailer($OP_CP_NM, $OP_EMAIL, $sending_email, $sending_email, $email_subject, $email_body, $path, $filename);
			echo"=======".$error_msg;
			if($error_msg <> "") { 
?>
	<script language="javascript">
			alert('�����Դϴ� ����� ������ ������ Ȯ���Ͻð�, �̸��� �ּҰ� �ִ��� Ȯ�� ��Ź�帳�ϴ�.....');
	</script>
<?
			} else { 

				$option = array("RESERVE_NO" => $reserve_no);
				insertEmail($conn, $file_title, $cp_no, $OP_CP_NM, $OP_EMAIL, $sending_email, $sending_email, $email_subject, $email_body, $download_url, $s_adm_no, $option);

				//echo $OP_CP_NM." // ".$OP_EMAIL." // ".$sending_email." // ".$sending_email." // ".$email_subject." // ".$email_body." // ".$path." // ".$filename."<br/>";
	?>
	<script language="javascript">
			alert('���� ó�� �Ǿ����ϴ�.');
			location.href =  "<?=$_SERVER[PHP_SELF]?>?reserve_no=<?=$reserve_no?>&print_date=<?=$print_date?>&op_cp_no=<?=$op_cp_no?>&print_type=<?=$print_type?>&sending_email=<?=$sending_email?>";
	</script>
	<?
			}
		} else {
	?>
	<script language="javascript">
			alert('�����Դϴ� ����� ������ ������ Ȯ���Ͻð�, �̸��� �ּҰ� �ִ��� Ȯ�� ��Ź�帳�ϴ�..');
	</script>
	<?
		}

		
		
	}

	//��ۿϷ� -> ����غ��� �ǵ�����
	if($mode == "UNDO_ORDER_STATE") { 

		if($order_goods_no <> '') { 
			$result = undoOrderStateFromComplete($conn, $order_goods_no);
			$result = resetOrderInfor($conn, $reserve_no);
?>
<script type="text/javascript">
	window.opener.js_search();
	alert("���� �Ǿ����ϴ�.");
</script>
<?
		}
	}

	//�ֹ����� ��ǰ ����
	if ($mode == "D") {
		$result = deleteOrderGoods($conn, $order_goods_no, $s_adm_no);
		$result = resetOrderInfor($conn, $reserve_no);
?>
<script type="text/javascript">
	window.opener.js_search();
	alert("���� �Ǿ����ϴ�.");
</script>
<?
	}

	if ($mode == "C") {
		//echo "<script>alert('Cancel Claim');</script>";
		$result = cancelClaim($conn, $reserve_no, $order_goods_no, $order_seq, $s_adm_no);

		$query = "";
		if($order_state == "8") { //��ȯ

			$query = "SELECT GOODS_NO, GROUP_NO, QTY, ORDER_GOODS_NO, DELIVERY_TYPE, CLAIM_ORDER_GOODS_NO
				   	    FROM TBL_ORDER_GOODS
				       WHERE RESERVE_NO = '".$reserve_no."' AND ORDER_SEQ = '".($order_seq + 1)."' ";
		} else 
			$query = "SELECT GOODS_NO, GROUP_NO, QTY, ORDER_GOODS_NO, DELIVERY_TYPE, CLAIM_ORDER_GOODS_NO
					    FROM TBL_ORDER_GOODS
				       WHERE ORDER_GOODS_NO = '".$order_goods_no."' ";

		$result = mysql_query($query, $conn);
		$rows   = mysql_fetch_array($result);
		$goods_no			  = $rows[0];
		$group_no			  = $rows[1];
		$cancel_qty           = $rows[2];
		$next_order_goods_no  = $rows[3];
		$claim_order_goods_no = $rows[4];

		if($delivery_type != "98") { 

			$prev_refund_able_qty = getRefundAbleQty($conn, $reserve_no, $group_no);
			$arr_prev_rs = selectOrderGoods($conn, $group_no);

			for ($j = 0 ; $j < sizeof($arr_prev_rs); $j++) {
							
				$rs_prev_ORDER_STATE		= trim($arr_prev_rs[$j]["ORDER_STATE"]);
				$rs_prev_WORK_QTY			= trim($arr_prev_rs[$j]["WORK_QTY"]);
				$rs_prev_WORK_FLAG			= trim($arr_prev_rs[$j]["WORK_FLAG"]);
				
				if($rs_prev_ORDER_STATE == "2" && $rs_prev_WORK_FLAG == "Y" && ($prev_refund_able_qty > $rs_prev_WORK_QTY)) {
					updateWorksFlagNOrderGoods($conn, $group_no);
				}
			}
		}

		//�׷� ��ȣ�� ���� ��� (�� �ֹ���ȣ) �ش� �ֹ����� ��� ������ŭ�� �������´�
		//2018-12-03 �����ù�, �ܺι߼��� ��� claim_order_goods_no ����
		if($group_no > 0)
			cancelOrderGoodsRefundableQty($conn, $group_no, $cancel_qty);
		else if($claim_order_goods_no > 0)
			cancelOrderGoodsRefundableQty($conn, $claim_order_goods_no, $cancel_qty);

		//��ȯ�� ���� Ŭ���� ���
		if($order_state == "8") { 
			$options = array('ORDER_GOODS_NO' => $next_order_goods_no);
			deleteCompanyLedgerByCode($conn, $options, $s_adm_no);
		}

		//Ŭ���� ��ü ��ȣ�� ���� ���� ���
		$options = array('CLAIM_ORDER_GOODS_NO' => $order_goods_no);
		deleteCompanyLedgerByCode($conn, $options, $s_adm_no);

		//��ȯ�� ��ȯ�� �� �ֹ���ȣ, ��ǰ�� ���� �ֹ���ȣ
		//$cancel_order_goods_no = "";
		//if($order_state == "8") //��ȯ
		//	$cancel_order_goods_no =  $next_order_goods_no;
		//else
		//	$cancel_order_goods_no =  $group_no;

		//$cancel_order_goods_no =  $next_order_goods_no;


		//��ȯ, ��ǰ�� �Էµ� ��ǰ ���԰� ���� 
		if($order_state == "8" || $order_state == "7")
			updateStatusFStockCancel($conn, $next_order_goods_no, $cancel_qty, $goods_no, $s_adm_no);
		
		/*
		//�ش� ��� ������ŭ ����� ����
		if($order_state == "8")
			updateStatusTStockCancel($conn, $order_goods_no, "N", $cancel_qty);
		else if($order_state == "6")
			updateStatusTStockCancel($conn, $cancel_order_goods_no, "N", $cancel_qty);
		*/

		$result = resetOrderInfor($conn, $reserve_no);

?>
<script type="text/javascript">
	//window.opener.js_search();
	//alert("���� �Ǿ����ϴ�.");
</script>
<?
	}

	if ($mode == "ORDER_UPDATE") {
		$result = updateOrderOrderInfo($conn, $o_mem_nm, $o_phone, $o_hphone, $o_zipcode, $o_addr1, $o_email, $opt_manager_no, $reserve_no);

?>
<script type="text/javascript">
	window.opener.js_search();
	alert("���� �Ǿ����ϴ�.");
</script>
<?
	}

	if ($mode == "RECEIVER_UPDATE") {
		$result = updateOrderReceiverInfo($conn, $r_mem_nm, $r_email, $r_phone, $r_hphone, $r_zipcode, $r_addr1, $memo, $reserve_no);

?>
<script type="text/javascript">
	window.opener.js_search();
	alert("���� �Ǿ����ϴ�.");
</script>
<?
	}

	if ($mode == "ORDER_DELETE") { 
		$result = deleteOrder($conn, $reserve_no, $s_adm_no);

?>
<script type="text/javascript">
	window.opener.js_search();
	alert("���� �Ǿ����ϴ�.");
</script>
<?
	}

	if ($mode == "DELIVERY_UPDATE") { 
	
		$temp_delivery_no = str_replace("-","",$temp_delivery_no);
		$result = updateDeliveryInfo($conn, $temp_delivery_cp, $temp_delivery_no, $order_goods_no);

?>
<script type="text/javascript">
	window.opener.js_search();
	alert("���� �Ǿ����ϴ�.");
</script>
<?
	}

/*
	if ($mode == "TAX_CONFIRM") {

		//echo "tax_confirm_tf :".$tax_confirm_tf."<br/>";
		$err_msg = "";

		$row_cnt = count($chk_no);
		
		for ($k = 0; $k < $row_cnt; $k++) {
		
			$temp_order_goods_no = $chk_no[$k];

			//echo "temp_order_goods_no :".$temp_order_goods_no."<br/>";

			$result = updateTaxConfirmByOrderGoodsNo($conn, $temp_order_goods_no, $tax_confirm_tf, $s_adm_no);
		}
?>
<script type="text/javascript">
	window.opener.js_search();
	alert("���� �Ǿ����ϴ�.");
</script>
<?
	}
*/

#====================================================================
# Request Parameter
#====================================================================

	$mm_subtree	 = "3";

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$reserve_no				= trim($reserve_no);
	$sel_order_state	= trim($sel_order_state);
	$sel_pay_type			= trim($sel_pay_type);
	$start_date				= trim($start_date);
	$end_date					= trim($end_date);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
	$del_tf = "N";
	
#===============================================================
# Get Search list count
#===============================================================
	$arr_order_rs = selectOrder($conn, $reserve_no);

	$rs_cp_no						= trim($arr_order_rs[0]["CP_NO"]); 
	$rs_order_no					= trim($arr_order_rs[0]["ORDER_NO"]); 
	$rs_reserve_no				    = trim($arr_order_rs[0]["RESERVE_NO"]); 
	$rs_o_mem_nm					= trim($arr_order_rs[0]["O_MEM_NM"]); 
	$rs_o_zipcode					= trim($arr_order_rs[0]["O_ZIPCODE"]); 
	$rs_o_addr1						= SetStringFromDB($arr_order_rs[0]["O_ADDR1"]); 
	$rs_o_addr2						= SetStringFromDB($arr_order_rs[0]["O_ADDR2"]); 
	$rs_o_phone						= trim($arr_order_rs[0]["O_PHONE"]); 
	$rs_o_hphone					= trim($arr_order_rs[0]["O_HPHONE"]); 
	$rs_o_email						= trim($arr_order_rs[0]["O_EMAIL"]); 
	$rs_r_mem_nm					= trim($arr_order_rs[0]["R_MEM_NM"]); 
	$rs_r_zipcode					= trim($arr_order_rs[0]["R_ZIPCODE"]); 
	$rs_r_addr1						= SetStringFromDB($arr_order_rs[0]["R_ADDR1"]); 
	$rs_r_addr2						= SetStringFromDB($arr_order_rs[0]["R_ADDR2"]); 
	$rs_r_phone						= trim($arr_order_rs[0]["R_PHONE"]); 
	$rs_r_hphone					= trim($arr_order_rs[0]["R_HPHONE"]); 
	$rs_r_email						= trim($arr_order_rs[0]["R_EMAIL"]); 
	$rs_order_date					= trim($arr_order_rs[0]["ORDER_DATE"]); 
	$rs_memo						= trim($arr_order_rs[0]["MEMO"]); 
	$rs_total_sale_price			= trim($arr_order_rs[0]["TOTAL_SALE_PRICE"]); 
	$rs_total_extra_price			= trim($arr_order_rs[0]["TOTAL_EXTRA_PRICE"]); 
	$rs_total_qty					= trim($arr_order_rs[0]["TOTAL_QTY"]); 
	$rs_total_delivery_price		= trim($arr_order_rs[0]["TOTAL_DELIVERY_PRICE"]); 
	$rs_total_sa_delivery_price		= trim($arr_order_rs[0]["TOTAL_SA_DELIVERY_PRICE"]);
	$rs_total_discount_price		= trim($arr_order_rs[0]["TOTAL_DISCOUNT_PRICE"]);
  
	$rs_opt_manager_no				= trim($arr_order_rs[0]["OPT_MANAGER_NO"]); 

	$arr_rs = listManagerOrderGoods($conn, $reserve_no, $mem_no, "Y", "N");
	$arr_rs_payment = listManagerPayment($conn, $reserve_no, "Y", "N");

	$bb_code = "CLAIM";

	$arr_rs_claim = listBoard($conn, $bb_code, $reserve_no, $con_cate_02, $con_cate_03, $con_cate_04, $cp_type, $reply_state, "Y", "N", $search_field, $search_str, "1", "1000");


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script>
<style>
	.inline {
		display : inline-block;
	}
</style>
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
								
				if (document.getElementById("addr_type").value == "s") {
				  // �����ȣ�� �ּ� ������ �ش� �ʵ忡 �ִ´�.
					document.getElementById("o_zipcode").value = data.zonecode;
					//document.getElementById("cp_zip").value = data.postcode2;
					document.getElementById("o_addr1").value = fullAddr;
					// Ŀ���� ���ּ� �ʵ�� �̵��Ѵ�.
					document.getElementById("o_addr1").focus();
				} else {
				  // �����ȣ�� �ּ� ������ �ش� �ʵ忡 �ִ´�.
					document.getElementById("r_zipcode").value = data.zonecode;
					//document.getElementById("re_zip").value = data.postcode2;
					document.getElementById("r_addr1").value = fullAddr;
					// Ŀ���� ���ּ� �ʵ�� �̵��Ѵ�.
					document.getElementById("r_addr1").focus();
				}


            }
        }).open();
    }

	function js_addr_open(s) {
		document.getElementById("addr_type").value = s;
		sample6_execDaumPostcode();
	}

</script>  
<script language="javascript">

	function js_list() {
		var frm = document.frm;
			
		frm.method = "get";
		frm.action = "order_list.php";
		frm.submit();
	}

	function js_view(rn, order_no) {

		var frm = document.frm;
		
		frm.order_no.value = order_no;
		frm.mode.value = "S";
		frm.target = "";
		frm.method = "get";
		frm.action = "order_write.php";
		frm.submit();
		
	}

	function js_sending_email() { 

		var frm = document.frm;

		if(frm.sending_email.value != "") {

			var res = frm.sending_email.value.match(/[^0-9a-zA-Z-_.@,;]/gi);
			if (res != null) {
				alert('�̸��Ͽ��� ������ �ʴ� ��ȣ�� �ֽ��ϴ�. \n���� �̸����� �ƴҰ�� ���� ���� �߼��� �ȵǸ� �������� �����Ƕ��� �ּһ��̿� , �Ǵ� ; ��ȣ�� ����ϼ���.');
				return;
			}

		} else { 
			alert('�߼��� ��� �̸����� �Է����ּ���.');
			return;
		}
			

		frm.mode.value = "SENDING_EMAIL";
		frm.target = "";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	// ��ȸ ��ư Ŭ�� �� 
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_toggle(order_no, use_tf) {
		var frm = document.frm;

		bDelOK = confirm('���� ���θ� ���� �Ͻðڽ��ϱ�?');
			
		if (bDelOK==true) {

			if (use_tf == "Y") {
				use_tf = "N";
			} else {
				use_tf = "Y";
			}

			frm.order_no.value = order_no;
			frm.use_tf.value = use_tf;
			frm.mode.value = "T";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_cancel(order_goods_no) {

			var frm = document.frm;
			var url = "order_read_detail.php?order_goods_no="+order_goods_no;
			NewWindow(url, 'goods_detail','860','600','YES');

	}

	function js_option_edit(order_goods_no) {

			var frm = document.frm;
			//var url = "order_read_option_edit.php?order_goods_no="+order_goods_no;
			var url = "order_read_option_work_edit.php?order_goods_no="+order_goods_no;
			NewWindow(url, 'goods_option_edit','800','650','YES');

	}

	function js_delivery_edit(order_goods_no) {

			var frm = document.frm;
			var url = "order_read_delivery_edit.php?order_goods_no="+order_goods_no;
			NewWindow(url, 'goods_delivery_edit','800','270','NO');

	}

	function js_sale_price_edit(order_goods_no) {

			var frm = document.frm;
			var url = "order_read_sale_price_edit.php?order_goods_no="+order_goods_no;
			NewWindow(url, 'goods_sale_price_edit','800','270','NO');

	}

	function js_advanced_price_edit(order_goods_no) {

		var frm = document.frm;
		var url = "order_read_advanced_price_edit.php?order_goods_no="+order_goods_no;
		NewWindow(url, 'goods_advanced_price_edit','800','500','NO');

	}

	/*
	function js_buy_price_edit(order_goods_no) { 

		var frm = document.frm;
		var url = "order_read_buy_price_edit.php?order_goods_no="+order_goods_no;
		NewWindow(url, 'order_read_buy_price_edit','800','500','NO');

	}
	*/

	function js_delivery_no_edit(order_goods_no) {

			var frm = document.frm;
			var url = "order_read_delivery_no_edit.php?order_goods_no="+order_goods_no;
			NewWindow(url, 'goods_delivery_no_edit','800','400','NO');

	}

	function js_claim_cancel(order_goods_no, order_seq, order_state) {

		bOK = confirm('�� Ŭ������ ������ ����Ͻðڽ��ϱ�? ���, ����� ���� ������ �Ĳ��� Ȯ�� �� �ּ���.');
			
		if (bOK) {
			frm.order_goods_no.value = order_goods_no;
			frm.order_seq.value = order_seq;
			frm.order_state.value = order_state;
			frm.mode.value = "C";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}

	}

	function js_reload() {
		document.frm.mode.value = "";
		window.location.reload();
		window.opener.js_search();
	}

	function js_view_claim(rn, bb_code, bb_no) {

		var frm = document.frm;

		frm.bb_code.value = bb_code;
		frm.bb_no.value = bb_no;
		frm.mode.value = "S";
		frm.target = "_new";
		frm.method = "get";
		frm.action = "/manager/claim/claim_write.php";
		frm.submit();
		
	}

	function init() {
		window.resizeTo(980,700);
	}

	function js_edit_order_info() {

		if($("input[name=order_switch]").val() == "0") {
			$(".order_info").hide();
			$(".order").show();
			
			$("input[name=order_switch]").val("1");
		} else {
			var frm = document.frm;
			frm.mode.value = "ORDER_UPDATE";
			frm.method = "post";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_edit_receiver_info() {

		if($("input[name=receiver_switch]").val() == "0") {
			$(".receiver_info").hide();
			$(".receiver").show();
			
			$("input[name=receiver_switch]").val("1");
		} else {
			var frm = document.frm;
			frm.mode.value = "RECEIVER_UPDATE";
			frm.method = "post";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_edit_delivery(order_goods_no) { 

		var delivery_cp = $("select[name=delivery_cp_"+order_goods_no+"]");
		var delivery_no = $("input[name=delivery_no_"+order_goods_no+"]");

		if(delivery_cp.is(":visible")) { 

			var frm = document.frm;
			frm.mode.value = "DELIVERY_UPDATE";
			frm.order_goods_no.value = order_goods_no;
			frm.temp_delivery_cp.value = delivery_cp.val();
			frm.temp_delivery_no.value = delivery_no.val();

			frm.method = "post";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();

		} else {

			delivery_cp.show();
			delivery_no.show();
		}
		
	}

	function js_list_delivery_paper(reserve_no, order_goods_no) {

		var url = "pop_delivery_paper_list.php?reserve_no=" + reserve_no + "&order_goods_no=" + order_goods_no;

		NewWindow(url, 'pop_delivery_paper_list','1000','500','YES');
		
	}

	function js_order_sale_company(reserve_no, cp_no) {
	
		var url = "pop_order_sale_company.php?reserve_no=" + reserve_no + "&cp_no=" + cp_no;

		NewWindow(url, 'pop_order_sale_company','800','600','YES');
		
	}

	function js_order_buy_company(order_goods_no, buy_cp_no) {
	
		var url = "pop_order_buy_company.php?order_goods_no=" + order_goods_no + "&buy_cp_no=" + buy_cp_no;

		NewWindow(url, 'pop_order_buy_company','800','600','YES');
		
	}

	function js_pop_individual(reserve_no, order_goods_no) { 

		var frm = document.frm;
		
		var url = "pop_individual_delivery_list.php?reserve_no="+reserve_no+"&order_goods_no="+order_goods_no;

		NewWindow(url, 'pop_individual_delivery_list','1000','600','YES');
	}

	function js_pop_delivery_paper_frame(delivery_cp, delivery_no) { 

		var url = "/manager/order/pop_delivery_paper_wrapper.php?delivery_cp=" + delivery_cp + "&delivery_no=" + delivery_no;
		NewWindow(url, 'pop_delivery_paper_wrapper', '920', '700', 'YES');

	}

	function js_direct_order_statement(reserve_no) {
		var frm = document.frm;
		print_type = frm.print_type.value;
		print_date = frm.print_date.value;
		op_cp_no = frm.op_cp_no.value;
		cp_no = frm.cp_no.value;
		NewDownloadWindow("../confirm/order_statement_excel.php", {reserve_no : reserve_no, print_type :  print_type, print_date : print_date, op_cp_no : op_cp_no});
	}

	function js_pop_delivery_confirmation(reserve_no) {
		var frm = document.frm;
		
		print_type = frm.print_type.value;
		print_date = frm.print_date.value;
		op_cp_no = frm.op_cp_no.value;
		cp_no = frm.cp_no.value;

		if(print_type == "1" || print_type == "2") 
			NewDownloadWindow("pop_delivery_confirmation.php", {reserve_no : reserve_no, print_type :  print_type, print_date : print_date, op_cp_no : op_cp_no});
		else if(print_type == "3") 
			NewDownloadWindow("../confirm/estimate_sheet_excel.php", {reserve_no : Base64.encode(reserve_no), print_type :  Base64.encode(print_type), print_date : Base64.encode(print_date), op_cp_no : Base64.encode(op_cp_no)});
		else if (print_type == "4") 
			NewDownloadWindow("../confirm/transaction_statement_excel.php", {reserve_no : Base64.encode(reserve_no), print_type :  Base64.encode(print_type), print_date : Base64.encode(print_date), op_cp_no : Base64.encode(op_cp_no)});
		else if (print_type == "5") 
			NewDownloadWindow("../confirm/estimate_and_transaction_excel.php", {reserve_no : Base64.encode(reserve_no), print_type :  Base64.encode(print_type), print_date : Base64.encode(print_date), op_cp_no : Base64.encode(op_cp_no)});
		else if (print_type == "6")
			NewDownloadWindow("../confirm/transaction_statement_with_balance_excel.php", {reserve_no : Base64.encode(reserve_no), print_type :  Base64.encode(print_type), print_date : Base64.encode(print_date), op_cp_no : Base64.encode(op_cp_no)});
		else if (print_type == "7")
			NewDownloadWindow("../confirm/order_statement_excel.php", {reserve_no : reserve_no, print_type :  print_type, print_date : print_date, op_cp_no : op_cp_no});
		else if (print_type == "8")
			NewDownloadWindow("../confirm/order_statement_delivery_no_excel.php", {reserve_no : Base64.encode(reserve_no), print_type :  Base64.encode(print_type), print_date : Base64.encode(print_date), op_cp_no : Base64.encode(op_cp_no)});
		else if (print_type == "9")
			NewDownloadWindow("../confirm/company_ledger_excel_list.php", {reserve_no : reserve_no, print_type :  print_type, print_date : print_date, op_cp_no : op_cp_no, cp_no : Base64.encode(cp_no)});
		else if (print_type == "10") 
			NewDownloadWindow("../confirm/estimate_and_transaction_excel_add.php", {reserve_no : Base64.encode(reserve_no), print_type :  Base64.encode(print_type), print_date : Base64.encode(print_date), op_cp_no : Base64.encode(op_cp_no)});			
	}

	function js_goods_view(goods_no) {

		var frm = document.frm;
		
		frm.goods_no.value = goods_no;
		frm.mode.value = "S";
		frm.target = "blank";
		frm.action = "/manager/goods/goods_write.php";
		frm.submit();
		
	}

	function js_undo_order_state(order_goods_no) {
		
		if(confirm('����غ����� ���·� �ǵ����ϴ�. �����ϰ� �������ּ���.')) { 
			var frm = document.frm;
		
			frm.order_goods_no.value = order_goods_no;
			frm.mode.value = "UNDO_ORDER_STATE";
			frm.method = "post";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}

	}

	function js_order_delete(reserve_no) { 

		bDelOK = confirm('�� �ֹ��� ������ ��� ������ ������ �����˴ϴ�. �ֹ� ����� ���ܾ� �� ���, Ŭ���ӿ��� ��ü������ŭ ��Ҹ� ���ּ���. �׷��� �����Ͻðڽ��ϱ�?');
			
		if (bDelOK==true) {

			var frm = document.frm;
			
			frm.reserve_no.value = reserve_no;
			frm.mode.value = "ORDER_DELETE";
			frm.method = "post";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();

			window.opener.js_search();

		}
	}

	function js_delete_order_goods(order_goods_no) {
		var frm = document.frm;

		bDelOK = confirm('������ �����Ͻðڽ��ϱ�?');
		
		if (bDelOK==true) {
			
			frm.mode.value = "D";
			frm.order_goods_no.value = order_goods_no;
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_goods_request(reserve_no, order_goods_no, goods_no, order_qty) { 

		/*
		var frm = document.frm;
		
		frm.order_goods_no.value = order_goods_no;
		frm.goods_no.value = goods_no;
		frm.order_qty.value = order_qty;
		frm.mode.value = "FROM_ORDER";
		frm.target = "_blank";
		frm.method = "post";
		frm.action = "/manager/stock/goods_request_bridge.php";
		frm.submit();
		*/

		var frm = document.frm;
		
		var url = "order_read_goods_request.php?reserve_no="+reserve_no+"&order_goods_no="+order_goods_no;

		NewWindow(url, 'order_read_goods_request','830','700','YES');
	}

	function js_check_goods_request(order_goods_no, order_date) { 
		window.open("/manager/stock/goods_request_list.php?search_field=ORDER_GOODS_NO&start_date="+order_date+"&search_str=" + order_goods_no,'_blank');

	}

	function js_link_to_item_ledger(order_goods_no, order_date) { 

		window.open("/manager/stock/item_ledger_list.php?nPage=1&nPageSize=20&search_field=RESERVE_NO&start_date="+order_date+"&search_str=" + order_goods_no,'_blank');

	}

	function js_link_to_stat_order(reserve_no) { 

		window.open("/manager/stats/order_list.php?nPage=1&nPageSize=20&search_field=RESERVE_NO&search_str=" + reserve_no,'_blank');

	}

	function js_link_to_company_ledger(cp_no) {

		window.open("/manager/confirm/company_ledger_list.php?cp_type=" + cp_no,'_blank');
		
	}

	function js_add_goods(reserve_no) {

		var url = "order_goods_add.php?cp_no="+ frm.cp_no.value + "&reserve_no=" + reserve_no;

		NewWindow(url,'popup_add_goods','820','700','YES');

	}
	function js_cancel_confirm_order(order_goods_no){
		var frm = document.frm;
		frm.cancel_order_goods_no.value=order_goods_no;
		frm.mode.value="CANCEL_ORDER_CONFIRM";
		frm.target="";
		frm.action="<?=$_SERVER['PHP_SELF']?>";
		frm.submit();
	}

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

	/*
	function js_tax_confirm(tax_confirm_tf) {

		var frm = document.frm;
		
		frm.tax_confirm_tf.value = tax_confirm_tf;
		frm.mode.value = "TAX_CONFIRM";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
		
	}
	*/

</script>
<style>
	.email_section {display:none;}
</style>
</head>

<body id="popup_order_wide" onload="init();">

<div id="popupwrap_order_wide">
	<h1>�ֹ� �� ��ȸ</h1>
	<div id="postsch_code">

		<div class="addr_inp">
		

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="reserve_no" value="<?=$reserve_no?>">
<input type="hidden" name="order_goods_no" value="">
<input type="hidden" name="order_seq" value="">
<input type="hidden" name="order_state" value="">
<input type="hidden" name="mode" value="">

<input type="hidden" name="addr_type" id="addr_type" value="">

<input type="hidden" name="goods_no" value="">
<input type="hidden" name="order_qty" value="">

<input type="hidden" name="bb_code" value="">
<input type="hidden" name="bb_no" value="">

<input type="hidden" name="temp_delivery_cp" value="">
<input type="hidden" name="temp_delivery_no" value="">
<input type="hidden" name="cancel_order_goods_no" value="">

<input type="hidden" name="tax_confirm_tf" value="">
				<?
					if ($s_adm_cp_type == "�") { 
				?>
					<div class="inline"><a href="javascript:js_direct_order_statement('<?=$reserve_no?>');"><img src="../images/common/btn/btn_excel.gif" alt="���� ����Ʈ">�ֹ���</a></div>
					<div class="btn_right90 inline">
						��ǰ���ڰ�
						<select name="print_date" >
							<option value="<?=date("Y-m-d",strtotime("0 day"))?>" <?if($print_date == date("Y-m-d",strtotime("0 day"))) echo "selected";?>>����</option>
							<option value="<?=date("Y-m-d",strtotime("1 day"))?>" <?if($print_date == date("Y-m-d",strtotime("1 day"))) echo "selected";?>>����</option>
						</select>
						��
						<?
							$arr_op = getOperatingCompany($conn, '');
							if($op_cp_no == "") 
								$op_cp_no = $s_adm_com_code;
							echo makeGenericSelectBox($conn, $arr_op, 'op_cp_no', '100', "", "", $op_cp_no, "CP_NO", "CP_NM");
						?>

						<select name="print_type">
							<option value="2" <?if($print_type == 2) echo "selected";?>>��ǰȮ�μ�(������)</option>
							<option value="1" <?if($print_type == 1) echo "selected";?>>�μ���(��������)</option>
							<option value="7" <?if($print_type == 7) echo "selected";?>>�ֹ���(����)</option>
							<option value="3" <?if($print_type == 3) echo "selected";?>>������(����)</option>
							<option value="4" <?if($print_type == 4) echo "selected";?>>�ŷ�����(����)</option>
							<option value="6" <?if($print_type == 6) echo "selected";?>>�ŷ�����+�ܾ�(����)</option>
							<option value="5" <?if($print_type == 5) echo "selected";?>>����+�ŷ�����(����)</option>
							<option value="10" <?if($print_type == 10) echo "selected";?>>����+�ŷ�����(����+�߰�)</option>
							<option value="8" <?if($print_type == 8) echo "selected";?>>�����ȣ(����)</option>
							<option value="9" <?if($print_type == 9) echo "selected";?>>�ŷ�����(�ֱ��Ѵ�-����)</option>
						</select>
						<input type="button" name="b" value="���" class="btntxt" onclick="js_pop_delivery_confirmation('<?=$reserve_no?>');">

						<?
							switch($print_type) { 
								case "3": 
									$print_title = "������";
									$template_num = "ORDER_1";
									break;
								case "4": 
									$print_title = "�ŷ�����";
									$template_num = "ORDER_2";
									break;
								case "6": 
									$print_title = "�ŷ�����(�ܾ�����)";
									$template_num = "ORDER_2";
									break;
								case "5": 
									$print_title = "������ �� �ŷ�����";
									$template_num = "ORDER_3";
									break;
								case "8": 
									$print_title = "�ֹ���ǰ �����ȣ";
									$template_num = "DELIVERY_NO";
									break;
								case "9": 
									$print_title = "�ŷ�����";
									$template_num = "LEDGER";
									break;
								case "10": 
									$print_title = "������ �� �ŷ�����";
									$template_num = "ORDER_3";
									break;	
								default : 
									$class_email_section = "email_section";
									break;

							}
							
							$email_subject = "��)����Ʈ�� [������]�Դϴ�.";
							$email_subject = str_replace("[������]", $print_title, $email_subject);

							$arr_op_cp = getOperatingCompany($conn, $s_adm_com_code);
							$OP_CP_NM = $arr_op_cp[0]["CP_NM"]." ".$arr_op_cp[0]["CP_NM2"];
							$OP_CEO_NM = $arr_op_cp[0]["CEO_NM"];
							$OP_CP_ADDR = $arr_op_cp[0]["CP_ADDR"];
							$OP_CP_PHONE = $arr_op_cp[0]["CP_PHONE"];
							$OP_EMAIL = $arr_op_cp[0]["EMAIL"];

							$ADM_PHONE = getAdminPhone($conn, $s_adm_no);

							if($ADM_PHONE == "")
								$ADM_PHONE = $OP_CP_PHONE;

							$email_body = getDcodeExtByCode($conn, "MESSAGE_TEMPLATE", $template_num);
							$email_body = str_replace("[ȸ���]", $OP_CP_NM, $email_body);
							$email_body = str_replace("[�߽���]", $s_adm_nm, $email_body);
							$email_body = str_replace("[��ǥ��ȣ]", $OP_CP_PHONE, $email_body);
							$email_body = str_replace("[������]", $print_title, $email_body);
							$email_body = str_replace("[�����ڹ�ȣ]", $ADM_PHONE, $email_body);
							$email_body = str_replace("[����]", "\r\n", $email_body);

							//�ֹ��� �̸����� �������� ��ü���� �������� 18-11-15
							if($rs_o_email == "")
								$cp_email = getCompanyEmail($conn, $rs_cp_no);
							else
								$cp_email = $rs_o_email;


						?>
						<div class="<?=$class_email_section?> email_part_toggle">
							
							<table cellpadding="0" cellspacing="0" class="colstable" style="width:100%;">
								<colgroup>
									<col width="15%" />
									<col width="32%" />
									<col width="15%" />
									<col width="32%" />
									<col width="6%" />
								</colgroup>
								
								<tr>
									<th style="text-align:center;">�����ּ�</th>
									<td colspan="4"><input type="text" name="sending_email" value="<?=$cp_email?>" style="width:400px;" placeholder="������ �̸��� �ּ� �Է�"/>&nbsp;&nbsp;&nbsp;<input type="button" name="b" value="����߼�" class="btntxt" onclick="js_sending_email();"></td>
								</tr>
								<tr>
									<th style="text-align:center;">��������</th>
									<td colspan="4">
										<input type="text" class="txt" name="email_subject" value="<?=$email_subject?>[<?=$rs_r_mem_nm?>]_<?=getCompanyNameWithNoCode($conn,$rs_cp_no)?>" style="width: 85%;"/> 
									</td>
								</tr>
								<tr>
									<th style="text-align:center;">���ϳ���</th>
									<td colspan="4"><textarea style="width:85%; height:160px;" name="email_body"><?=$email_body?></textarea></td>
								</tr>
							</table>
						</div>
						

						<script>
							$(function(){
								$("[name=print_type]").change(function(){
									var selected = $("[name=print_type] :selected").val();
									switch(selected) { 
										case "1":
										case "2":
										case "7":
											$(".email_part_toggle").hide();
											break;
										default : 
											location.href =  "<?=$_SERVER[PHP_SELF]?>?reserve_no=<?=$reserve_no?>&print_date=<?=$print_date?>&op_cp_no=<?=$op_cp_no?>&sending_email=<?=$sending_email?>&print_type=" + selected;
											break;
									}
								});
							});
						</script>
					</div>
				<?
					}
				?>
					<h2>* �ֹ��� ����.</h2>
					<table cellpadding="0" cellspacing="0" width="100%" class="colstable02">
					<colgroup>
						<col width="15%" />
						<col width="32%" />
						<col width="15%" />
						<col width="32%" />
						<col width="6%" />
					</colgroup>
						<tr>
							<th>�ֹ���ȣ</th>
							<td class="line">
								<?=$rs_reserve_no?>
							</td>
							<th>�ֹ���</th>
							<td class="line">
								<?=$rs_order_date?>
							</td>
							<td rowspan="5" class="line">
								<input type="hidden" name="order_switch" value="0" />
								<?
									if ($s_adm_cp_type == "�") { 
								?>
								<input type="button" name="b" value="����" class="btntxt" onclick="js_edit_order_info();">
								<? 
									} 
								?>
							</td>
						</tr>
						<tr>
							<th>�Ǹž�ü</th>
							<td class="line">
								<input type="hidden" name="cp_no" value="<?=$rs_cp_no?>"/>
								<input type="hidden" name="cp_nm" value="<?=getCompanyNameWithNoCode($conn,$rs_cp_no)?>"/>
								<?=getCompanyName($conn,$rs_cp_no)?>
								<? if($s_adm_cp_type == "�") { ?>
								<input type="button" name="bb" value="�ŷ�����" class="btntxt" onclick="js_link_to_company_ledger('<?=$rs_cp_no?>');"/>
								<input type="button" name="bb" value="����" class="btntxt" onclick="js_order_sale_company('<?=$rs_reserve_no?>', '<?=$rs_cp_no?>');"/>
								<? } ?>
							</td>
							<th>�ֹ��ڸ�</th>
							<td class="line">
								<span class="order_info"><?=$rs_o_mem_nm?></span>
								<input type="text" name="o_mem_nm" class="txt display_none order" value="<?=$rs_o_mem_nm?>" />
							</td>
						</tr>
						<tr>
							<th>����ó</th>
							<td class="line">
								<span class="order_info"><?=$rs_o_phone?></span>
								<input type="text" name="o_phone" class="txt display_none order" value="<?=$rs_o_phone?>" />
							</td>
							<th>�޴���ȭ��ȣ</th>
							<td class="line">
								<span class="order_info"><?=$rs_o_hphone?></span>
								<input type="text" name="o_hphone" class="txt display_none order" value="<?=$rs_o_hphone?>" />
							</td>
						</tr>
						<tr>
							<th>�ּ�</th>
							<td class="line" colspan="3">
								<span class="order_info"><?=$rs_o_zipcode?> &nbsp;<?=$rs_o_addr1?></span>
								<input type="text" id="o_zipcode" name="o_zipcode" class="txt display_none order" style="width:60px;" maxlength="7" value="<?=$rs_o_zipcode?>" />
								<input type="text" id="o_addr1" name="o_addr1" class="txt display_none order" value="<?=$rs_o_addr1?>"  style="width:65%;" />
								<a href="#none" onClick="js_addr_open('s');" class="display_none order"><img src="/manager/images/admin/btn_filesch.gif" alt="ã��" align="absmiddle" /></a>
							</td>
						</tr>
						<tr>
							<th>�̸���</th>
							<td class="line">
								<span class="order_info"><?=$rs_o_email?></span>
								<input type="text" name="o_email" class="txt display_none order" value="<?=$rs_o_email?>" />
							</td>
							<th>���������</th>
							<td class="line">
								<span class="order_info"><?=getAdminName($conn, $rs_opt_manager_no)?></span>
								<?= makeAdminInfoByMDSelectBox($conn,"opt_manager_no"," style='70px;' class='txt display_none order' ","��ü","", $rs_opt_manager_no) ?>
							</td>
						</tr>
					</table>

					<h2>* ������ ����.</h2>
					<table cellpadding="0" cellspacing="0" width="100%" class="colstable02">
					<colgroup>
						<col width="15%" />
						<col width="32%" />
						<col width="15%" />
						<col width="32%" />
						<col width="6%" />
					</colgroup>
						<tr>
							<th>�����ڸ�</th>
							<td class="line">
								<span class="receiver_info"><?=$rs_r_mem_nm?></span>
								<input type="text" name="r_mem_nm" class="txt display_none receiver" value="<?=$rs_r_mem_nm?>" />
							</td>
							<th>�̸���</th>
							<td class="line">
								<span class="receiver_info"><?=$rs_r_email?></span>
								<input type="text" name="r_email" class="txt display_none receiver" value="<?=$rs_r_email?>" />
							</td>
							<td rowspan="5" class="line">
								<input type="hidden" name="receiver_switch" value="0" />
								<?
									if ($s_adm_cp_type == "�") { 
								?>
								<input type="button" name="b" value="����" class="btntxt" onclick="js_edit_receiver_info();">
								<?
									}	
								?>
							</td>
						</tr>
						<tr>
							<th>����ó</th>
							<td class="line">
								<span class="receiver_info"><?=$rs_r_phone?></span>
								<input type="text" name="r_phone" class="txt display_none receiver" value="<?=$rs_r_phone?>" />
							</td>
							<th>�޴���ȭ��ȣ</th>
							<td class="line">
								<span class="receiver_info"><?=$rs_r_hphone?></span>
								<input type="text" name="r_hphone" class="txt display_none receiver" value="<?=$rs_r_hphone?>" />
							</td>
						</tr>
						<tr>
							<th>�ּ�</th>
							<td class="line" colspan="3">
								<span class="receiver_info"><?=$rs_r_zipcode?> &nbsp;<?=$rs_r_addr1?></span>
								<input type="text" id="r_zipcode" name="r_zipcode" class="txt display_none receiver" style="width:60px;" maxlength="7" value="<?=$rs_r_zipcode?>" />
								<input type="text" id="r_addr1" name="r_addr1" class="txt display_none receiver" value="<?=$rs_r_addr1?>"  style="width:65%;" />
								
								<a href="#none" onClick="js_addr_open('r');" class="display_none receiver"><img src="/manager/images/admin/btn_filesch.gif" alt="ã��" align="absmiddle" /></a>
								
							</td>
						</tr>
						<tr>
							<th>�ֹ��ڸ޸�(��۸޸�)</th>
							<td class="line" colspan="3">
								<span class="receiver_info"><?=nl2br($rs_memo)?></span>
								<textarea style="width:75%; height:60px;" class="txt display_none receiver" name="memo"><?=$rs_memo?></textarea>
							</td>
						</tr>
					</table>

					<div class="sp10"></div>
					<h2 style="float:left">* �ֹ� ��ǰ</h2>
					<?
						if ($s_adm_cp_type == "�") { 
					?>
						<div class="btn_right">
							
							<!--
							������ ���ݰ�꼭 : 
							<input type="text" name="cf_code" value="" style="width:200px;" placeholder="���ι�ȣ �Է�"/>
							<input type="button" name="b" value="����ó��" class="btntxt" onclick="js_tax_confirm('Y');">
							<input type="button" name="b" value="�������" class="btntxt" onclick="js_tax_confirm('N');">
							&nbsp;&nbsp; | &nbsp;&nbsp; 
							-->
							
							<input type="button" name="b" value="�ֹ���ǰ�߰�" class="btntxt" onclick="js_add_goods('<?=$reserve_no?>');">
						</div>
						<script type="text/javascript">
							$(function(){
								$("[name=cf_code]").keyup(function(){
									key_text = $(this).val();
									if(key_text.length == 8) { 
										$(this).val(key_text + "-10000000-");
									}
								});

							});

						</script>
					<?
						}
					?>
					<table cellpadding="0" cellspacing="0" class="rowstable" border="0">
					<colgroup>
						<col width="7%" />
						<col width="28%" />
						<col width="9%" />
						<col width="13%" />
						<col width="11%" />
						<col width="22%" />
						<col width="10%" />
					</colgroup>
					<tr>
						<th rowspan="2"><input type="checkbox" name="all_chk" onClick="js_all_check();"><br/>��ǰ��ȣ</th>
						<th>��ǰ��</th>
						<th>�ݾ�</th>
						<th rowspan="2">�߰���ۺ�</th>
						<th>����</th>
						<th>�ֹ�����</th>
						<th rowspan="2" class="end">Ŭ����/�������</th>
					</tr>
					<tr>
						<th>�ɼ�</th>
						<th>����</th>
						<th>�հ�</th>
						<th>�������</th>
					</tr>
					<?
						$nCnt = 0;
						$total_sum_price = 0;
						$sum_qty = 0;
					
						$is_all_confirmed = false;

						if (sizeof($arr_rs) > 0) {
							for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
								$ORDER_GOODS_NO				= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
								$ORDER_SEQ					= trim($arr_rs[$j]["ORDER_SEQ"]);
								$BUY_CP_NO					= trim($arr_rs[$j]["BUY_CP_NO"]);
								$GOODS_NO					= trim($arr_rs[$j]["GOODS_NO"]);
								$GOODS_STATE				= trim($arr_rs[$j]["GOODS_STATE"]);
								$GOODS_CODE					= SetStringFromDB(trim($arr_rs[$j]["GOODS_CODE"]));
								$GOODS_NAME					= SetStringFromDB(trim($arr_rs[$j]["GOODS_NAME"]));
								$GOODS_SUB_NAME				= SetStringFromDB(trim($arr_rs[$j]["GOODS_SUB_NAME"]));
								$BUY_PRICE					= trim($arr_rs[$j]["BUY_PRICE"]);
								$SALE_PRICE					= trim($arr_rs[$j]["SALE_PRICE"]);
								$EXTRA_PRICE				= trim($arr_rs[$j]["EXTRA_PRICE"]);
								$DELIVERY_PRICE				= trim($arr_rs[$j]["DELIVERY_PRICE"]);
								$SA_DELIVERY_PRICE			= trim($arr_rs[$j]["SA_DELIVERY_PRICE"]);
								$DISCOUNT_PRICE				= trim($arr_rs[$j]["DISCOUNT_PRICE"]);

								$SUM_PRICE					= trim($arr_rs[$j]["SUM_PRICE"]);
								$PLUS_PRICE					= trim($arr_rs[$j]["PLUS_PRICE"]);
								$GOODS_LEE					= trim($arr_rs[$j]["LEE"]);
								$QTY						= trim($arr_rs[$j]["QTY"]);
								$REQ_DATE					= trim($arr_rs[$j]["PAY_DATE"]);
								$END_DATE					= trim($arr_rs[$j]["FINISH_DATE"]);
								$ORDER_STATE				= trim($arr_rs[$j]["ORDER_STATE"]);
								$DELIVERY_CP				= trim($arr_rs[$j]["DELIVERY_CP"]);
								$DELIVERY_NO				= trim($arr_rs[$j]["DELIVERY_NO"]);
								$DELIVERY_TYPE				= trim($arr_rs[$j]["DELIVERY_TYPE"]);

								$OPT_STICKER_NO				= trim($arr_rs[$j]["OPT_STICKER_NO"]);
								$OPT_STICKER_MSG			= trim($arr_rs[$j]["OPT_STICKER_MSG"]);
								$OPT_OUTBOX_TF				= trim($arr_rs[$j]["OPT_OUTBOX_TF"]);
								$OPT_WRAP_NO				= trim($arr_rs[$j]["OPT_WRAP_NO"]);
								$OPT_PRINT_MSG				= trim($arr_rs[$j]["OPT_PRINT_MSG"]);
								$OPT_OUTSTOCK_DATE			= trim($arr_rs[$j]["OPT_OUTSTOCK_DATE"]);
								$ORDER_CONFIRM_DATE			= trim($arr_rs[$j]["ORDER_CONFIRM_DATE"]);
								$ORDER_DATE					= trim($arr_rs[$j]["ORDER_DATE"]);
								$TAX_TF						= trim($arr_rs[$j]["TAX_TF"]);

								if ($TAX_TF == "�����") {
									$STR_TAX_TF = "<font color='orange'>(�����)</font>";
								} else {
									$STR_TAX_TF = "<font color='navy'>(����)</font>";
								}
								

								if($OPT_OUTSTOCK_DATE == "1970-01-01 00:00:00" || $OPT_OUTSTOCK_DATE == "0000-00-00 00:00:00")
									$OPT_OUTSTOCK_DATE = "";

								if($OPT_OUTSTOCK_DATE != "")
									$OPT_OUTSTOCK_DATE			= date("Y-m-d", strtotime($OPT_OUTSTOCK_DATE));

								if($ORDER_DATE != "0000-00-00 00:00:00")
									$ORDER_DATE			= date("Y-m-d", strtotime($ORDER_DATE));

								//�ֹ�Ȯ������ ��ǰ���̶� �ִٸ� �ֹ����� �ȵ�
								$refund_able_qty	= getRefundAbleQty($conn, $reserve_no, $ORDER_GOODS_NO);
								$is_all_confirmed = $is_all_confirmed | ($ORDER_STATE != "1" && $refund_able_qty != 0);

								$OPT_MEMO					= trim($arr_rs[$j]["OPT_MEMO"]);
								$OPT_REQUEST_MEMO			= trim($arr_rs[$j]["OPT_REQUEST_MEMO"]);
								$OPT_SUPPORT_MEMO			= trim($arr_rs[$j]["OPT_SUPPORT_MEMO"]);

								$CATE_01					= trim($arr_rs[$j]["CATE_01"]);

								if($CATE_01 <> "")
									$str_cate_01 = $CATE_01.") ";
								else 
									$str_cate_01 = "";
								
								if ($refund_able_qty == 0 && ($ORDER_STATE == "1" || $ORDER_STATE == "2" || $ORDER_STATE == "3"))
										$str_cancel_style = "cancel_goods";
									else
										$str_cancel_style = "";

								$option_str	= "";
								$option_str .= ($OPT_STICKER_NO <> "0" ? "��ƼĿ ".getGoodsName($conn, $OPT_STICKER_NO). " <br/> " : "");
								$option_str .= ($OPT_OUTBOX_TF == "Y" ? "�ڽ���ƼĿ �� <br/> " : "" );
								$option_str .= ($OPT_WRAP_NO <> "0" ? "������ ".getGoodsName($conn, $OPT_WRAP_NO). " <br/> " : "");
								$option_str .= ($OPT_STICKER_MSG <> "" ? "��ƼĿ�޼��� : ".$OPT_STICKER_MSG. " <br/>" : "");
								$option_str .= ($OPT_PRINT_MSG <> "" ? "�μ�޼��� ".$OPT_PRINT_MSG. " <br/> " : "");
								$option_str .= ($OPT_MEMO <> "" ? "�۾��޸� ".$OPT_MEMO. " <br/> " : "");
								$option_str .= ($OPT_REQUEST_MEMO <> "" ? "���ָ޸� : ".$OPT_REQUEST_MEMO. " <br/> " : "");
								$option_str .= ($OPT_SUPPORT_MEMO <> "" ? "<span style='color:red;'>��޸� : ".$OPT_SUPPORT_MEMO. "</span> <br/> " : "");
								$option_str .= ($OPT_OUTSTOCK_DATE != "" ? "������� ".$OPT_OUTSTOCK_DATE : "������");
								
								
								$str_price_class = "price";
								$str_state_class = "state";
							
								$STR_QTY = number_format($QTY);

								if (($ORDER_STATE == "4") || ($ORDER_STATE == "6") || ($ORDER_STATE == "7") || ($ORDER_STATE == "8")) {
								
									if ($ORDER_STATE == "4") {
									
										$BUY_PRICE = 0;
										$SALE_PRICE = 0;
										//$EXTRA_PRICE = 0;
										$SA_DELIVERY_PRICE = 0;
										$DISCOUNT_PRICE = 0;
										$STR_QTY = "[".number_format($QTY)."]";
										$SUM_PRICE = 0;
										$PLUS_PRICE = 0;
										$GOODS_LEE = 0;

										$REQ_DATE = $ORDER_DATE;
										$END_DATE = $ORDER_DATE;
										$str_price_class = "price_cancel";
										$str_state_class = "state_cancel";

									} else {
										$BUY_PRICE = -$BUY_PRICE;
										$SALE_PRICE = -$SALE_PRICE;
										//$EXTRA_PRICE = -$EXTRA_PRICE;
										$SA_DELIVERY_PRICE = -$SA_DELIVERY_PRICE;
										$DISCOUNT_PRICE = -$DISCOUNT_PRICE;
										$STR_QTY = number_format(-$QTY);
										$SUM_PRICE = -$SUM_PRICE;
										$PLUS_PRICE = -$PLUS_PRICE;
										$GOODS_LEE = - $GOODS_LEE;

										$REQ_DATE = $ORDER_DATE;
										$END_DATE = $ORDER_DATE;

										$str_price_class = "price_refund";
										$str_state_class = "state_refund";
									}
									$option_fix_show = false;
								} else { 

									$option_fix_show = true;
								}

								$cntExistOrderGoodsNo = chkExistOrderGoodsNo($conn, $ORDER_GOODS_NO);

					?>
					<?
								if ($s_adm_cp_type == "�") { 
					?>
					<tr height="35" class="<?=$str_cancel_style?>">
						<td rowspan="2"><input type="checkbox" name="chk_no[]" class="chk" value="<?=$ORDER_GOODS_NO?>"><br/><?= $ORDER_GOODS_NO?>
						<? if ($sPageRight_D == "Y" && $ORDER_STATE == "1") { ?>
						<br>
						<input type="button" name="b" value="����" class="btntxt" onclick="js_delete_order_goods('<?=$ORDER_GOODS_NO?>');">
						<? } ?>
						<?
							//��۹�� ��Ÿ�϶� ���ֹ�ư�� �� ��������? .. �����䱸1ȸ(2016-10-21)
							if($ORDER_STATE == "2" && $DELIVERY_TYPE != "99" && $refund_able_qty > 0) {
						?>
						<br/><input type="button" name="b" value="<?=($cntExistOrderGoodsNo > 0 ? "�߰�����" : "����")?>" class="btntxt" onclick="js_goods_request('<?=$reserve_no?>','<?=$ORDER_GOODS_NO?>', '<?=$GOODS_NO?>', '<?=$STR_QTY?>');">
						<?
							}
						?>
						<?
							//������(- ���ָ� ���ؼ�)
							if($ORDER_STATE == "6" || $ORDER_STATE == "7" || $ORDER_STATE == "8") {
						?>
						<br/><input type="button" name="b" value="<?=($cntExistOrderGoodsNo > 0 ? "�߰�����" : "����")?>" class="btntxt" onclick="js_goods_request('<?=$reserve_no?>','<?=$ORDER_GOODS_NO?>', '<?=$GOODS_NO?>', '<?=$STR_QTY?>');">
						<?
							}
						?>
						<? if($cntExistOrderGoodsNo > 0) { ?>
							<br/><br/><input type="button" name="b" value="���ֳ���" class="btntxt" onclick="js_check_goods_request('<?=$ORDER_GOODS_NO?>', '<?=$ORDER_DATE?>');"/>
						<? } ?>
						<? if($ORDER_STATE =="2" && $s_adm_no==1){?>
							<br/><input type="button" name="btnCancel" value="�ֹ�Ȯ�� ���" onclick="js_cancel_confirm_order('<?=$ORDER_GOODS_NO?>')"/>
						<?}?>
						</td>
						<td class="modeual_nm">
							<a href="javascript:js_goods_view('<?= $GOODS_NO?>')"><?=$STR_TAX_TF?>  <?=$str_cate_01?><?=$GOODS_NAME?><br><?=$GOODS_SUB_NAME?> [<?=$GOODS_CODE?>] (<?=$GOODS_STATE?>)</a>
							
							<input type="button" name="bb" value="���Ծ�ü����" class="btntxt" onclick="js_order_buy_company('<?=$ORDER_GOODS_NO?>', '<?=$BUY_CP_NO?>');"/><br/>
							<?
								// ����ǰ ���� ������ ���� 
								$arr_goods_sub = selectGoodsSub($conn, $GOODS_NO);
							?>
								<?
									if (sizeof($arr_goods_sub) > 0) {
										echo "<font style='color:#A2A2A2;'><br/><br/>- ����ǰ -<br/>";
										for ($jk = 0 ; $jk < sizeof($arr_goods_sub); $jk++) {
											$sub_goods_name			= trim($arr_goods_sub[$jk]["GOODS_NAME"]);
											$sub_goods_cnt			= trim($arr_goods_sub[$jk]["GOODS_CNT"]);
											echo $sub_goods_name."&nbsp;&nbsp;(".$sub_goods_cnt.")<br>";
										}
										echo "</font>";
									}
								?>
							
						</td>

						<td class="<?=$str_price_class?>">
							<?=number_format(abs($SALE_PRICE))?><br>
						</td>
						<td rowspan="2" class="<?=$str_price_class?>">
							<?=number_format($SA_DELIVERY_PRICE)?><div class="sp15"></div>
							<?
								// && $refund_able_qty <> $QTY �� �κ��� ��??
								if(($ORDER_STATE == "1" || $ORDER_STATE == "2" || $ORDER_STATE == "3")) {
							?>
							<input type="button" name="b" value="�ݾ�������" class="btntxt" onclick="js_advanced_price_edit('<?=$ORDER_GOODS_NO?>');">
							<?
								}
							?>
							<!--<input type="button" name="b" value="��������" class="btntxt" onclick="js_buy_price_edit('<?=$ORDER_GOODS_NO?>');">-->
						</td>
						<td class="<?=$str_price_class?>"><?=$STR_QTY?>
							<?
									if(($ORDER_STATE == "1" || $ORDER_STATE == "2" || $ORDER_STATE == "3") && $refund_able_qty <> $QTY) {
							?>
							<br/><span style="color:#A2A2A2;">�ܿ� : <?=number_format($refund_able_qty)?></span>
							<? } ?>
						</td>

						<td class="<?=$str_state_class?>"><?=getDcodeName($conn, "ORDER_STATE", $ORDER_STATE);?>
							<?
								if($ORDER_STATE == "3") { 
							?>
								<br/><input type="button" onclick="javascript:js_undo_order_state('<?= $ORDER_GOODS_NO?>');" value="�ǵ�����"/>
							<?
								}
							?>
						</td>
						<td rowspan="2">
							<?
									if ($sPageRight_U == "Y") {
										if (!$option_fix_show) { 
							?>
								<input type="button" name="b" value="��&nbsp;&nbsp;&nbsp;��" class="btntxt" onclick="js_claim_cancel('<?=$ORDER_GOODS_NO?>','<?=$ORDER_SEQ?>','<?=$ORDER_STATE?>');">
							<?		} else  {
							?>
								<?
									if($refund_able_qty > 0 && $ORDER_STATE>1) {
								?>
								<input type="button" name="b" value="Ŭ����" class="btntxt" onclick="js_cancel('<?=$ORDER_GOODS_NO?>');">
								<br/><br/>
								<?  } ?>
								<input type="button" name="b" value="�������" class="btntxt" onclick="js_link_to_item_ledger('<?=$ORDER_GOODS_NO?>', '<?=$ORDER_DATE?>');">
								<? if ($s_adm_cp_type == "�") { ?>
								<br/><br/>
								<input type="button" name="b" value="����󼼳���" class="btntxt" onclick="js_link_to_stat_order('<?=$reserve_no?>');">
								<? } ?>
							<?					
										}
									} 
							?>
							
						</td>
					</tr>
					<tr height="35" class="<?=$str_cancel_style?>">
						<td class="filedown">
							<?=$option_str ?>&nbsp; 
							<? if($option_fix_show) { ?>
							<input type="button" name="b" value="�ɼǼ���" class="btntxt" onclick="js_option_edit('<?=$ORDER_GOODS_NO?>');"> 
							<? } ?>
						</td>
						<td class="<?=$str_price_class?>"><?=number_format($DISCOUNT_PRICE)?></td>
						<td class="<?=$str_price_class?>"><?=number_format($SUM_PRICE)?></td>
						<td class="filedown">
								<?
									$cnt_delivery_place = 0;
									$total_sub_qty = 0;
									$total_delivered_qty = 0;

									if($DELIVERY_TYPE == "3" || $DELIVERY_TYPE == "98") { 
									
										$arr_rs_individual = cntDeliveryIndividual($conn, $ORDER_GOODS_NO);
										if(sizeof($arr_rs_individual) > 0) { 
											$cnt_delivery_place = $arr_rs_individual[0]["CNT_DELIVERY_PLACE"];
											$total_sub_qty = $arr_rs_individual[0]["TOTAL_GOODS_DELIVERY_QTY"];
											$total_delivered_qty= $arr_rs_individual[0]["TOTAL_DELIVERED_QTY"];
											
										}
										
									}
									$delivered_qty = ($refund_able_qty - $total_delivered_qty < 0 ? 0 : $refund_able_qty - $total_delivered_qty);
								?>
								<? if($option_fix_show) { ?>
								<a onclick="js_list_delivery_paper('<?=$reserve_no?>', '<?=$ORDER_GOODS_NO?>');"><b><?=getDcodeName($conn,"DELIVERY_TYPE",$DELIVERY_TYPE);?></b></a>
								<?	if($DELIVERY_TYPE == "3" || $DELIVERY_TYPE == "98") { ?>
								<a href="javascript:js_pop_individual('<?=$reserve_no?>','<?=$ORDER_GOODS_NO?>');" title="<?=$str_places?>"> <?="(".$cnt_delivery_place."��)"?></a>
								<?	} ?>
								<br/>
								<?	
									if ($DELIVERY_TYPE == "0" || $DELIVERY_TYPE == "3") {

										$arr_rs_delivery = cntOrderGoodsDelivery($conn, $reserve_no, $ORDER_GOODS_NO, $individual_no);
									
										for($k = 0; $k < sizeof($arr_rs_delivery); $k ++)
										{
											$DELIVERY_CP				= trim($arr_rs_delivery[$k]["DELIVERY_CP"]);
											$TOTAL						= trim($arr_rs_delivery[$k]["TOTAL"]);
											$CNT_YES					= trim($arr_rs_delivery[$k]["CNT_YES"]);
											$CNT_NO						= trim($arr_rs_delivery[$k]["CNT_NO"]);
								?>		
										<?=$DELIVERY_CP." : ".$CNT_YES." ��<br/>"?>
								<?
										}
								?>

								<br/>
								<input type="button" name="b" value="���� ����/��ȸ" class="btntxt" onclick="js_list_delivery_paper('<?=$reserve_no?>', '<?=$ORDER_GOODS_NO?>');">

								<?
									}

									if($DELIVERY_TYPE == "3" || $DELIVERY_TYPE == "98") { 
								?>
									<input type="button" name="b" value="������� �Է�" class="btntxt" onclick="js_pop_individual('<?=$reserve_no?>', '<?=$ORDER_GOODS_NO?>');">
								<? 
									}

									// ��ۿϷ��̰� �ܺξ�ü�߼��϶� 
									if($ORDER_STATE == "3" && $DELIVERY_TYPE == "98") { 
								?>
									<br/><a href="javascript:js_pop_delivery_paper_frame('<?=$DELIVERY_CP?>', '<?=$DELIVERY_NO?>');" title="<?=$DELIVERY_NO?>"><?=$DELIVERY_CP." ".$DELIVERY_NO?></a><br/>
									
									<input type="button" name="b" value="������� ����" class="btntxt" onclick="js_edit_delivery('<?=$ORDER_GOODS_NO?>');"><br/>
									<?=makeSelectBoxWithAttributes($conn,"DELIVERY_CP", "delivery_cp_".$ORDER_GOODS_NO, "85", "�ù�ȸ��", "", $DELIVERY_CP, "class='display_none box01' ")?>
									<input type="text" name="delivery_no_<?=$ORDER_GOODS_NO?>" class="txt display_none" value="<?=$DELIVERY_NO?>" style="width:80px;"/>
									
								<?
									}
								?>
								<? } ?>
						</td>
					</tr>
					<?		
								////////////////////// ��ü ���� �ֹ� ///////////////////////////////
								} else {
					?>

					<tr height="35">
						<td rowspan="2"><?= $ORDER_GOODS_NO?></td>
						<td class="modeual_nm">
							<?=$str_cate_01?><?=$GOODS_NAME?><br><?=$GOODS_SUB_NAME?> [<?=$GOODS_CODE?>] (<?=$GOODS_STATE?>)
						</td>

						<td class="<?=$str_price_class?>">
							<?=number_format($SALE_PRICE)?><br>
						</td>
						<td rowspan="2" class="<?=$str_price_class?>">
							<?=number_format($SA_DELIVERY_PRICE)?><div class="sp15"></div>
						</td>
						<td class="<?=$str_price_class?>"><?=$STR_QTY?></td>

						<td class="<?=$str_state_class?>">
							<?=getDcodeName($conn, "ORDER_STATE", $ORDER_STATE);?>
							
						</td>
						<td rowspan="2">
						</td>
					</tr>
					<tr height="35">
						<td class="filedown">
							<?=$option_str ?>
						</td>
						<td class="<?=$str_price_class?>"><?=number_format($DISCOUNT_PRICE)?></td>
						<td class="<?=$str_price_class?>"><?=number_format($SUM_PRICE)?></td>
						<td class="filedown">
								<? if($option_fix_show) { ?>
								<b><?=getDcodeName($conn,"DELIVERY_TYPE",$DELIVERY_TYPE);?></b>
								<?	
									if ($DELIVERY_TYPE == "0" || $DELIVERY_TYPE == "3") {

										$arr_rs_delivery = cntOrderGoodsDelivery($conn, $reserve_no, $ORDER_GOODS_NO, $individual_no);
									
										for($k = 0; $k < sizeof($arr_rs_delivery); $k ++)
										{
											$DELIVERY_CP				= trim($arr_rs_delivery[$k]["DELIVERY_CP"]);
											$TOTAL						= trim($arr_rs_delivery[$k]["TOTAL"]);
											$CNT_YES					= trim($arr_rs_delivery[$k]["CNT_YES"]);
											$CNT_NO						= trim($arr_rs_delivery[$k]["CNT_NO"]);
								?>		
										<?=$DELIVERY_CP." : ".$CNT_YES." ��<br/>"?>
								<?
										}
									}

									// ��ۿϷ��̰� �ܺξ�ü�߼��϶� 
									if($ORDER_STATE == "3" && $DELIVERY_TYPE == "98") { 
								?>
									<a href="javascript:js_pop_delivery_paper_frame('<?=$DELIVERY_CP?>', '<?=$DELIVERY_NO?>');" title="<?=$DELIVERY_NO?>"><?=$DELIVERY_CP." ".$DELIVERY_NO?></a>
								<?
									}
								?>
								<? } ?>
						</td>
					</tr>


					<?			}

							}

					?>
						
					<?
						}else{
					?>
					<tr>
						<td height="50" align="center" colspan="10">�����Ͱ� �����ϴ�. </td>
					</tr>
					<?
						}
					?>
					</table>
					<? if (sizeof($arr_rs) > 0) { ?>
					<table cellpadding="0" cellspacing="0" class="rowstable" border="0">
					<colgroup>
						<col width="*" />
						<col width="16.6%" />
						<col width="16.6%" />
						<col width="16.6%" />
						<col width="16.6%" />
						<col width="16.6%" />
					</colgroup>
						<tr height="35">
							<td><b>�ֹ��հ� : </b></td>
							<td class="price"><b>�� �ǸŰ�: <?=number_format($rs_total_sale_price)?></b></td>
							<td class="price"><b>�� ����: <?=number_format($rs_total_qty)?></b></td>
							<td class="price"><b>�� �߰���ۺ�: <?=number_format($rs_total_sa_delivery_price)?></b></td>
							<td class="price"><b>�� ����: <?=number_format($rs_total_discount_price)?></b></td>
							<td class="price"><b>�� ���� �հ�: <?=number_format($rs_total_sale_price + $rs_total_sa_delivery_price - $rs_total_discount_price  )?></b></td>
						</tr>
					</table>
					<? } ?>

					<? if ($s_adm_cp_type == "�") { ?>

<!--
				<h2>* ���� ����.</h2>
				<?
					
					if (sizeof($arr_rs_payment) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs_payment); $j++) {
							
							$PAY_TYPE				= trim($arr_rs_payment[$j]["PAY_TYPE"]);
							$BANK_AMOUNT 			= trim($arr_rs_payment[$j]["BANK_AMOUNT"]);
							$BANK_PAY_ACCOUNT		= trim($arr_rs_payment[$j]["BANK_PAY_ACCOUNT"]);
							$PAID_DATE				= trim($arr_rs_payment[$j]["PAID_DATE"]);
							$CMS_DEPOSITOR			= trim($arr_rs_payment[$j]["CMS_DEPOSITOR"]);

						?>

					<table cellpadding="0" cellspacing="0" width="100%" class="colstable02">

					<colgroup>
						<col width="15%" />
						<col width="35%" />
						<col width="15%" />
						<col width="35%" />
					</colgroup>
						<tr>
							<th>������</th>
							<td class="line">
								<?=getDcodeName($conn,"PAY_TYPE",$PAY_TYPE)?>
							</td>
							<th>����ݾ�</th>
							<td class="line">
								<?=number_format($BANK_AMOUNT)?>
							</td>
						</tr>
						<tr>
							<th>�Ա�����</th>
							<td colspan="3" class="line">
								<?=getDcodeName($conn,"ACCOUNT_BANK",$BANK_PAY_ACCOUNT)?>
							</td>
						</tr>
						<tr>
							<th>�Ա���</th>
							<td class="line">
								<?=$PAID_DATE?>
							</td>
							<th>�Ա���</th>
							<td class="line">
								<?=$CMS_DEPOSITOR?>
							</td>
						</tr>
					</table>
					<div class="sp20"></div>
						<?
						}
					}else{
						?>
						<tr>
							<td height="50" align="center" colspan="10">�����Ͱ� �����ϴ�. </td>
						</tr>
					</table>
					<div class="sp20"></div>
					<?
							}
					?>
-->

					<?
						}
					?>
					<h2>* Ŭ���� ����</h2>

					<table cellpadding="0" cellspacing="0" class="rowstable">
					<colgroup>
					  <col width="4%" />
					  <col width="11%" />
					  <col width="7%" />
					  <col width="14%" />
					  <col width="23%" />
					  <col width="23%" />
					  <col width="10%" />
					  <col width="8%" />
					</colgroup>
					<tr>
					  <th>No.</th>
					  <th>Ŭ���ӱ���</th>
					  <th>����</th>
					  <th>���޾�ü</th>
					  <th>��ǰ��</th>
					  <th>Ŭ����</th>
					  <th>ó������</th>
					  <th class="end">�����</th>
					</tr>
							<?
								$nCnt = 0;
								
								if (sizeof($arr_rs_claim) > 0) {
									
									for ($j = 0 ; $j < sizeof($arr_rs_claim); $j++) {
										
										$rn						= trim($arr_rs_claim[$j]["rn"]);
										$BB_NO					= trim($arr_rs_claim[$j]["BB_NO"]);
										$BB_CODE				= trim($arr_rs_claim[$j]["BB_CODE"]);
										$CATE_01				= trim($arr_rs_claim[$j]["CATE_01"]);
										$CATE_02				= trim($arr_rs_claim[$j]["CATE_02"]);
										$CATE_03				= trim($arr_rs_claim[$j]["CATE_03"]);
										$CATE_04				= trim($arr_rs_claim[$j]["CATE_04"]);
										$WRITER_NM				= trim($arr_rs_claim[$j]["WRITER_NM"]);
										$TITLE					= trim($arr_rs_claim[$j]["TITLE"]);
										$HIT_CNT				= trim($arr_rs_claim[$j]["HIT_CNT"]);
										$USE_TF					= trim($arr_rs_claim[$j]["USE_TF"]);
										$REG_DATE				= trim($arr_rs_claim[$j]["REG_DATE"]);
										$CONTENTS				= trim($arr_rs_claim[$j]["CONTENTS"]);
										$CONFIRM_TF				= trim($arr_rs_claim[$j]["REPLY_STATE"]);
										$REF_IP					= trim($arr_rs_claim[$j]["KEYWORD"]);
										 
										
										$REG_DATE = date("Y-m-d",strtotime($REG_DATE));

										if ($CONFIRM_TF == "Y") {
											$STR_CONFIRM_TF = "<font color='navy'>ó���Ϸ�</font>";
										} else {
											$STR_CONFIRM_TF = "<font color='red'>����</font>";
										}
							
										if ($USE_TF == "Y") {
											$STR_USE_TF = "<font color='navy'>����</font>";
										} else {
											$STR_USE_TF = "<font color='red'>�����</font>";
										}
							?>
								<tr> 
									<td><?= $rn ?></td>
									<td class="filedown"><a href="javascript:js_view_claim('<?=$rn?>','<?=$BB_CODE?>','<?=$BB_NO?>');"><?=getDcodeName($conn,"ORDER_STATE",$CATE_04)?></a></td>
									<td class="filedown"><?=getDcodeName($conn,"CLAIM_TYPE",$CATE_02)?></td>
									<td class="modeual_nm"><?=getCompanyName($conn,$REF_IP)?></td>
									 <td class="modeual_nm"><a href="javascript:js_view_claim('<?=$rn?>','<?=$BB_CODE?>','<?=$BB_NO?>');"><?=$TITLE?></a></td>
									<td class="modeual_nm">
										<?=nl2br($CONTENTS)?>
									</td>
									<td><?=$STR_CONFIRM_TF?></td>
									<td><?= $REG_DATE ?></td>
								</tr>
							<?			
									}
								} else { 
							?> 
								<tr>
									<td height="50" align="center" colspan="8">�����Ͱ� �����ϴ�. </td>
								</tr>
							<? 
								}
							?>
						</table>
						
						<div class="sp25"></div>

						<h2>* �ֹ� ������� ��Ȳ</h2>

						<table cellpadding="0" cellspacing="0" class="rowstable">
						<colgroup>
							<col width="4%" />
							<col width="11%" />
							<col width="5%" />
							<col width="*" />
							<col width="7%" />
							<col width="10%" />
							<col width="10%" />
							<col width="10%" />
							<col width="10%" />
							<col width="11%" />
						</colgroup>
						<tr>
							<th>�ֹ���ȣ</th>
							<th>������</th>
							<th>����</th>
							<th>�����</th>
							<th>����</th>
							<th>�ܰ�</th>
							<th>����/���޾�</th>
							<th>����/�Աݾ�</th>
							<th>�ΰ���/������</th>
							<th class="end">���</th>
						</tr>
						<?
							$arr_cl = listCompanyLedgerByReserveNo($conn, $reserve_no);
							if(sizeof($arr_cl) > 0) { 
								for ($k = 0 ; $k < sizeof($arr_cl); $k++) {
									$rscl_order_goods_no= trim($arr_cl[$k]["ORDER_GOODS_NO"]);
									$rscl_cl_no			= trim($arr_cl[$k]["CL_NO"]);
									$rscl_inout_date	= trim($arr_cl[$k]["INOUT_DATE"]);
									$rscl_inout_type	= trim($arr_cl[$k]["INOUT_TYPE"]);
									$rscl_name			= trim($arr_cl[$k]["NAME"]);
									$rscl_qty			= trim($arr_cl[$k]["QTY"]);
									$rscl_unit_price	= trim($arr_cl[$k]["UNIT_PRICE"]);
									$rscl_withdraw		= trim($arr_cl[$k]["WITHDRAW"]);
									$rscl_deposit		= trim($arr_cl[$k]["DEPOSIT"]);
									$rscl_surtax		= trim($arr_cl[$k]["SURTAX"]);
									$rscl_memo			= trim($arr_cl[$k]["MEMO"]);
						?>
						<tr height="30" title="<?=$rscl_cl_no?>">
							<td><?=$rscl_order_goods_no?></td>
							<td><?=$rscl_inout_date?></td>
							<td><?=$rscl_inout_type?></td>
							<td><?=$rscl_name?></td>
							<td><?=number_format($rscl_qty)?></td>
							<td><?=number_format($rscl_unit_price)?></td>
							<td><?=number_format($rscl_deposit)?></td>
							<td><?=number_format($rscl_withdraw)?></td>
							<td><?=number_format($rscl_surtax)?></td>
							<td><?=$rscl_memo?></td>
						</tr>
						<?
								}
							} else { 
						?> 
							<tr>
								<td height="50" align="center" colspan="10">�����Ͱ� �����ϴ�. </td>
							</tr>
						<? 
							}
						?>
						</table>
						<div class="sp25"></div>

						<h2>* �ֹ� �۾��Ϸ� ��Ȳ</h2>

						<table cellpadding="0" cellspacing="0" class="rowstable">
						<colgroup>
						  <col width="11%" />
						  <col width="11%" />
						  <col width="*" />
						  <col width="7%" />
						  <col width="12%" />
						  <col width="12%" />
						   <col width="12%" />
						</colgroup>
						<tr>
						  <th>�ֹ���ǰ��ȣ</th>
						  <th>�۾���ȣ</th>
						  <th>��ǰ��</th>
						  <th>����</th>
						  <th>�����</th>
						  <th>�۾��Ϸ���</th>
						  <th class="end">�۾��Ϸ���</th>
						</tr>
						<?
							$arr_sww = selectOrderWorkHistoryByReserveNo($conn, $reserve_no);
							if(sizeof($arr_sww) > 0) { 
								for ($k = 0 ; $k < sizeof($arr_sww); $k++) {
									$rsww_order_goods_no= trim($arr_sww[$k]["ORDER_GOODS_NO"]);
									$rsww_work_done_no	= trim($arr_sww[$k]["WORK_DONE_NO"]);
									$rsww_goods_code	= trim($arr_sww[$k]["GOODS_CODE"]);
									$rsww_goods_name	= trim($arr_sww[$k]["GOODS_NAME"]);
									$rsww_goods_sub_name= trim($arr_sww[$k]["GOODS_SUB_NAME"]);
									$rsww_work_type		= trim($arr_sww[$k]["WORK_TYPE"]);
									$rsww_sub_qty		= trim($arr_sww[$k]["QTY"]);
									$rsww_reg_date		= trim($arr_sww[$k]["REG_DATE"]);
									$rsww_reg_adm		= trim($arr_sww[$k]["REG_ADM"]);

									$rsww_reg_date = date("Y-m-d H:i",strtotime($rsww_reg_date));
									$rsww_reg_adm = getAdminName($conn, $rsww_reg_adm);

									switch($rsww_work_type) { 
										case "WORK_DONE" : $rsww_work_type = "�����"; break;
										case "WORK_SENT" : $rsww_work_type = "������"; break;
									}
						?>
						<tr height="30">
							<td><?=$rsww_order_goods_no?></td>
							<td><?=$rsww_work_done_no?></td>
							<td>[<?=$rsww_goods_code?>] <?=$rsww_goods_name?> <?=$rsww_goods_sub_name?></td>
							<td><?=$rsww_sub_qty?></td>
							<td><?=$rsww_work_type?></td>
							<td><?=$rsww_reg_date?></td>
							<td><?=$rsww_reg_adm?></td>
						</tr>
						<?
								}
							} else { 
						?> 
							<tr>
								<td height="50" align="center" colspan="7">�����Ͱ� �����ϴ�. </td>
							</tr>
						<? 
							}
						?>
						</table>
						<div class="sp25"></div>

						<h2>* �ֹ� ����� ��Ȳ</h2>

						<table cellpadding="0" cellspacing="0" class="rowstable">
						<colgroup>
						  <col width="4%" />
						  <col width="7%" />
						  <col width="11%" />
						  <col width="7%" />
						  <col width="*" />
						  <col width="8%" />
						  <col width="10%" />
						  <col width="12%" />
						  <col width="10%" />
						</colgroup>
						<tr>
						  <th>�ֹ���ȣ</th>
						  <th>�۾�/���ֹ�ȣ</th>
						  <th>�������</th>
						  <th>�������</th>
						  <th>��ǰ��</th>
						  <th>����</th>
						  <th>����</th>
						  <th>������</th>
						  <th class="end">�������</th>
						</tr>
						<?
							$arr_goods = listClaimGoods($conn, '', $reserve_no);

							if(sizeof($arr_goods) > 0) { 
								for($i = 0; $i < sizeof($arr_goods); $i++) {
									$STOCK_TYPE = $arr_goods[$i]["STOCK_TYPE"];
									$STOCK_CODE = $arr_goods[$i]["STOCK_CODE"];
									$GOODS_NAME = $arr_goods[$i]["GOODS_NAME"];
									$GOODS_NO	= $arr_goods[$i]["GOODS_NO"];
									$IN_QTY		= $arr_goods[$i]["IN_QTY"];
									$IN_FQTY	= $arr_goods[$i]["IN_FQTY"];
									$IN_BQTY	= $arr_goods[$i]["IN_BQTY"];
									$OUT_QTY	= $arr_goods[$i]["OUT_QTY"];
									$OUT_FQTY	= $arr_goods[$i]["OUT_FQTY"];
									$OUT_BQTY	= $arr_goods[$i]["OUT_BQTY"];
									$OUT_QTY	= $arr_goods[$i]["OUT_QTY"];
									$IN_DATE	= $arr_goods[$i]["IN_DATE"];
									$OUT_DATE	= $arr_goods[$i]["OUT_DATE"];
									$IN_LOC		= $arr_goods[$i]["IN_LOC"];
									$IN_LOC_EXT	= $arr_goods[$i]["IN_LOC_EXT"];
									$ARR_ORDER_GOODS_NO = $arr_goods[$i]["ORDER_GOODS_NO"];
									$ARR_WORK_DONE_NO   = $arr_goods[$i]["WORK_DONE_NO"];
									$ARR_RGN_NO		    = $arr_goods[$i]["RGN_NO"];
			

									if($IN_DATE <> '0000-00-00 00:00:00') 
										$IN_DATE = date("Y-m-d",strtotime($IN_DATE));
									
									if($OUT_DATE <> '0000-00-00 00:00:00')
										$OUT_DATE = date("Y-m-d",strtotime($OUT_DATE));


									$str_row_background = "";
									if($STOCK_TYPE == "IN") { 
										if($STOCK_CODE == "FST02") 
											$str_row_background = "style='background-color:yellow;'";
										if(($STOCK_CODE == "NST01" || $STOCK_CODE == "BST03") && $IN_LOC == "LOCD") //Ŭ���� + �����԰� or �ҷ��԰�
											$str_row_background = "style='background-color:#42f450;'";
										if(($STOCK_CODE == "NST01" || $STOCK_CODE == "BST03") && $IN_LOC == "LOCD" && strpos($IN_LOC_EXT, "��ǰ����") > 0) //Ŭ���� + �����԰� or �ҷ��԰� + ��ǰ���� 
											$str_row_background = "style='background-color:#E6A1A1;'";
						?>
						<tr height="30" <?=$str_row_background?>>
							<td><?=$ARR_ORDER_GOODS_NO?></td>
							<td><?=$ARR_RGN_NO?></td>
							<td>�԰�</td>
							<td><?=getDcodeName($conn, "IN_ST", $STOCK_CODE)?></td>
							<td class="modeual_nm"> <?=$GOODS_NAME?>[<a href="javascript:js_goods_view('<?=$GOODS_NO?>')"><?=$GOODS_NO?></a>]</td>
							<td>
								<? 
									if(startsWith($STOCK_CODE, "N")) { 
										echo $IN_QTY;
									} else if(startsWith($STOCK_CODE, "B")) { 
										echo $IN_BQTY;
									} else if(startsWith($STOCK_CODE, "F")) { 
										echo $IN_FQTY;
									}
								?> ��
							</td>
							<td class="modeual_nm"><?=getDcodeName($conn, "LOC", $IN_LOC)?></td>
							<td class="modeual_nm"><?=$IN_LOC_EXT?></td>
							<td><?=$IN_DATE?></td>
						</tr>
						<?
								} else { 
						?>
						<tr height="30">
							<td><?=$ARR_ORDER_GOODS_NO?></td>
							<td><?=$ARR_WORK_DONE_NO?></td>
							<td>���</td>
							<td><?=getDcodeName($conn, "OUT_ST", $STOCK_CODE)?></td>
							<td class="modeual_nm"> <?=$GOODS_NAME?>[<a href="javascript:js_goods_view('<?=$GOODS_NO?>')"><?=$GOODS_NO?></a>]</td>
							<td>
								<? 
									if(startsWith($STOCK_CODE, "N")) { 
										echo $OUT_QTY;
									} else if(startsWith($STOCK_CODE, "B")) { 
										echo $OUT_BQTY;
									} else if(startsWith($STOCK_CODE, "F")) { 
										echo $OUT_FQTY;
									}
								?> ��
							</td>
							<td class="modeual_nm"><?=getDcodeName($conn, "LOC", $IN_LOC)?></td>
							<td class="modeual_nm"><?=$IN_LOC_EXT?></td>
							<td><?=$OUT_DATE?></td>
						</tr>
						<?
										}

									}
						?>
							<?			
								} else { 
							?> 
								<tr>
									<td height="50" align="center" colspan="9">�����Ͱ� �����ϴ�. </td>
								</tr>
							<? 
								}
							?>
						</table>
						<div class="sp25"></div>

						<h2>* �̸��� �߼۳���</h2>

						<table cellpadding="0" cellspacing="0" class="rowstable">
						<colgroup>
						  <col width="11%" />
						  <col width="11%" />
						  <col width="18%" />
						  <col width="18%" />
						  <col width="11%" />
						  <col width="*" />
						  <col width="4%" />
						  <col width="8%" />
						</colgroup>
						<tr>
						  <th>�߼���</th>
						  <th>����</th>
						  <th>�߼���</th>
						  <th>������</th>
						  <th>����</th>
						  <th>����</th>
						  <th>÷��</th>
						  <th class="end">�߼���</th>
						</tr>
						<?
							$arr_email = listEmailByReserveNo($conn, $reserve_no);

							if(sizeof($arr_email) > 0) { 
								for($i = 0; $i < sizeof($arr_email); $i++) {

									//PAGE_FROM, NAME_FROM, EMAIL_FROM, NAME_TO, EMAIL_TO, TITLE, BODY, ATTACH_LINK, REG_DATE, REG_ADM
									$PAGE_FROM		= $arr_email[$i]["PAGE_FROM"];
									$NAME_FROM		= $arr_email[$i]["NAME_FROM"];
									$EMAIL_FROM		= $arr_email[$i]["EMAIL_FROM"];
									$NAME_TO		= $arr_email[$i]["NAME_TO"];
									$EMAIL_TO		= $arr_email[$i]["EMAIL_TO"];
									$TITLE			= $arr_email[$i]["TITLE"];
									$BODY			= $arr_email[$i]["BODY"];
									$ATTACH_LINK	= $arr_email[$i]["ATTACH_LINK"];
									$EMAIL_REG_ADM	= $arr_email[$i]["REG_ADM"];
									$EMAIL_REG_DATE	= $arr_email[$i]["REG_DATE"];
			

									if($EMAIL_REG_DATE <> '0000-00-00 00:00:00') {
										$EMAIL_REG_DATE_DATE = date("Y-m-d",strtotime($EMAIL_REG_DATE));
										$EMAIL_REG_DATE_TIME = date("H:i",strtotime($EMAIL_REG_DATE));
									}

									$EMAIL_REG_ADM = getAdminName($conn, $EMAIL_REG_ADM);

						?>
						<tr height="30">
							<td><?=$EMAIL_REG_DATE_DATE?><br/><?=$EMAIL_REG_DATE_TIME?></td>
							<td class="modeual_nm"><?=$PAGE_FROM?></td>
							<td class="modeual_nm"><?=$NAME_FROM?><br/>(<?=$EMAIL_FROM?>)</td>
							<td class="modeual_nm"><?=$NAME_TO?><br/>(<?=$EMAIL_TO?>)</td>
							<td class="modeual_nm" title="<?=$TITLE?>"><?=substr(nl2br($TITLE), 0, 20)?></td>
							<td class="modeual_nm" title="<?=$BODY?>"><?=substr(nl2br($BODY), 0, 100)?></td>
							<td class="modeual_nm"><a href="<?=$ATTACH_LINK?>" target="_blank"><img src="../images/common/btn/btn_excel.gif" alt="÷��ȭ�� Ȯ��"></a></td>
							<td class="modeual_nm"><?=$EMAIL_REG_ADM?></td>
						</tr>
						<?

									}
								} else { 
							?> 
								<tr>
									<td height="50" align="center" colspan="8">�����Ͱ� �����ϴ�. </td>
								</tr>
							<? 
								}
							?>
						</table>
						<div class="sp25"></div>

						<? if(!$is_all_confirmed && $s_adm_cp_type == "�") { ?>
						<div class="btn_right">
							<input type="button" name="b" value="�ֹ���������" class="btntxt" onclick="js_order_delete('<?=$reserve_no?>');">
						</div>
						<? } ?>

	</div>
</div>
<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
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