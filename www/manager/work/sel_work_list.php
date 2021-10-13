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

	$con_order_type = "";


	$menu_right = "WO002"; // �޴����� ���� �� �־�� �մϴ�


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
	require "../../_classes/biz/work/work.php";
	require "../../_classes/biz/stock/stock.php";
	require "../../_classes/biz/company/company.php";

	//	������ �ֹ� �������
	if($mode == "I_DELIVERY_PAPER") { 

		$result = getOrderGoodsByOrderGoodsNos($conn, $order_goods_no);

		for($k = 0; $k < sizeof($result); $k ++) { 
			$temp_order_goods_no = SetStringFromDB($result[$k]["ORDER_GOODS_NO"]);
			$reserve_no			 = SetStringFromDB($result[$k]["RESERVE_NO"]);
			$individual_no		 = SetStringFromDB($result[$k]["INDIVIDUAL_NO"]);
			$DELIVERY_TYPE		 = SetStringFromDB($result[$k]["DELIVERY_TYPE"]);
			$DELIVERY_CP	     = SetStringFromDB($result[$k]["DELIVERY_CP"]);
			$SENDER_NM			 = SetStringFromDB($result[$k]["SENDER_NM"]);
			$SENDER_PHONE		 = SetStringFromDB($result[$k]["SENDER_PHONE"]);

			$DELIVERY_FEE_CODE = $DELIVERY_CP."-����";
			$DELIVERY_FEE = getDcodeName($conn, "DELIVERY_FEE", $DELIVERY_FEE_CODE); 

			//2016-11-24 �����°��� ���ü �̸��� ���� �� ���ü �ּҸ�, �ٸ��� �ּҿ��� ��ü�� ����
			//$CON_SEND_CP_ADDR = "��� �����ֽ� ������ ��縮 98���� �߱���Ʈ��";
			$arr_op_cp = getOperatingCompany($conn, $s_adm_com_code);

			$OP_CP_NM		= SetStringFromDB($arr_op_cp[0]["CP_NM"]);
			$OP_CP_ADDR		= SetStringFromDB($arr_op_cp[0]["CP_ADDR"]);

			if($SENDER_NM == $OP_CP_NM)
				$CON_SEND_CP_ADDR = $OP_CP_ADDR;
			else
				$CON_SEND_CP_ADDR = str_replace($OP_CP_NM, "", $OP_CP_ADDR);
			
			
			//�ù�ȸ�� ������ ������� �Ұ�
			if($DELIVERY_CP == "" || $DELIVERY_FEE == "") {  
				continue;
			}

			//�����ù��� �����ּҰ� �Էµ��� �ʾ����� ������� �Ұ�
			if($DELIVERY_TYPE == "3" && ($individual_no == null || $individual_no == "")) {  
				continue;
			}

			//��������, ������, �ܺξ�ü�߼�, ��Ÿ�� ���� ���� ����
			if($DELIVERY_TYPE == "1" || $DELIVERY_TYPE == "2" || $DELIVERY_TYPE == "98" || $DELIVERY_TYPE == "99") 
				continue;

			// �̹� ������ ������ ����
			if(countOrderDeliveryPaper($conn, $temp_order_goods_no, $individual_no) <= 0)
			{
				
				$arr_order = selectOrder($conn, $reserve_no);

				$GOODS_DELIVERY_NAME = "";
				$SUB_QTY = "";
				$MEMO_ALL = "������� ��ǰ�Դϴ�-�ιڽ��� �ѼյǴ� ������ �����ּ���~";

				$CP_NO = SetStringFromDB($arr_order[0]["CP_NO"]);

				if($individual_no == "") { 
					$R_MEM_NM = SetStringFromDB($arr_order[0]["R_MEM_NM"]);
					$R_PHONE  = SetStringFromDB($arr_order[0]["R_PHONE"]);
					$R_HPHONE = SetStringFromDB($arr_order[0]["R_HPHONE"]);
					$R_ADDR1  = SetStringFromDB($arr_order[0]["R_ADDR1"]);
					$MEMO	  = SetStringFromDB($arr_order[0]["MEMO"]);
				
				} else {
					$arr_individual = selectDeliveryIndividual($conn, $individual_no);
			
					$R_MEM_NM				  = SetStringFromDB($arr_individual[0]["R_MEM_NM"]);
					$R_PHONE				  = SetStringFromDB($arr_individual[0]["R_PHONE"]);
					$R_HPHONE				  = SetStringFromDB($arr_individual[0]["R_HPHONE"]);
					$R_ADDR1				  = SetStringFromDB($arr_individual[0]["R_ADDR1"]);
					$MEMO					  = SetStringFromDB($arr_individual[0]["MEMO"]);
					$INDIVIDUAL_DELIVERY_TYPE = SetStringFromDB($arr_individual[0]["DELIVERY_TYPE"]);
					$USE_TF					  = SetStringFromDB($arr_individual[0]["USE_TF"]);

					//�������� ��� �н�
					if($USE_TF != "Y") 
						continue;

					//�ù谡 �ƴϹǷ� �н�
					if($INDIVIDUAL_DELIVERY_TYPE != "0")
						continue;
					
					//���� �Է� ������� ������� �⺻ ������� �Է�
					if($R_ADDR1 == "")							
						$R_ADDR1  = $arr_order[0]["R_ADDR1"];

					$GOODS_DELIVERY_NAME	= SetStringFromDB($arr_individual[0]["GOODS_DELIVERY_NAME"]); 
					$SUB_QTY				= SetStringFromDB($arr_individual[0]["SUB_QTY"]);
				}
				//������ ���ݾ��ֱ� /*2016-02-25 �����*/
				$R_MEM_NM = str_replace(" ","", $R_MEM_NM); 
				
				if($MEMO == "")
					$MEMO = $MEMO_ALL;

				$arr_order_goods = selectOrderGoods($conn, $temp_order_goods_no);
				if(sizeof($arr_order_goods) > 0) { 
					for($i=0; $i < sizeof($arr_order_goods); $i++) {

						$CP_ORDER_NO		 = SetStringFromDB($arr_order_goods[$i]["CP_ORDER_NO"]);
						$GOODS_NAME			 = SetStringFromDB($arr_order_goods[$i]["GOODS_NAME"]);
						$WORK_SEQ			 = SetStringFromDB($arr_order_goods[$i]["WORK_SEQ"]);
						$CATE_01			 = SetStringFromDB($arr_order_goods[$i]["CATE_01"]);
						$DELIVERY_CNT_IN_BOX = SetStringFromDB($arr_order_goods[$i]["DELIVERY_CNT_IN_BOX"]);
						
						if ($individual_no != "")
							$QTY = $SUB_QTY;
						else 
							$QTY = getRefundAbleQty($conn, $reserve_no, $temp_order_goods_no); 

						if($QTY == 0) //��ü ����ϰ��
							continue;

						// �����ù迡 �����ǰ���� �ִٸ� �װɷ� ǥ��
						if($GOODS_DELIVERY_NAME != "")
							$GOODS_NAME = $GOODS_DELIVERY_NAME." / ".$QTY."��";
						else
							$GOODS_NAME = $GOODS_NAME." / ".$QTY."��";

						//����,���� �߰�
						if($CATE_01 != "") 
							$GOODS_NAME = $CATE_01.")".$GOODS_NAME;

						//â�� ���Ǹ� ���� �۾������߰�
						$GOODS_NAME = "[".$WORK_SEQ."��] ".$GOODS_NAME;
						
						$total_paper_qty = ceil($QTY / $DELIVERY_CNT_IN_BOX);

						for($y=0; $y < $total_paper_qty; $y++) { 
							
							$DELIVERY_CNT = $total_paper_qty;
							$SEQ_OF_DELIVERY = $y + 1;
							$CON_ORDER_QTY = "1";
							$CON_PAYMENT_TYPE = "�ſ�";
							$CON_DELIVERY_TYPE = "�ù�";

							if($total_paper_qty > 1)
								$RECEIVER_NAME = $R_MEM_NM.$DELIVERY_CNT."-".$SEQ_OF_DELIVERY;
							else
								$RECEIVER_NAME = $R_MEM_NM;

							//������ �ڵ�����ȣ�� ������� ������ ��ȭ��ȣ�� �Է�
							if($R_PHONE != "" && $R_HPHONE == "")
								$R_HPHONE = $R_PHONE;
							
							$RECEIVER_NAME		= SetStringToDB($RECEIVER_NAME);
							$R_ADDR1			= SetStringToDB($R_ADDR1);
							$SENDER_NM			= SetStringToDB($SENDER_NM);
							$CON_SEND_CP_ADDR	= SetStringToDB($CON_SEND_CP_ADDR);
							$MEMO				= SetStringToDB($MEMO);

							$order_goods_delivery_no = insertOrderDeliveryPaper($conn, $chk_work_date, $reserve_no, $temp_order_goods_no, $individual_no, $CP_NO, $DELIVERY_CNT, $SEQ_OF_DELIVERY, $RECEIVER_NAME, $R_PHONE, $R_HPHONE, $R_ADDR1, $CON_ORDER_QTY, $MEMO, $SENDER_NM, $SENDER_PHONE, $SENDER_NM, $SENDER_PHONE, $CON_PAYMENT_TYPE, $CON_SEND_CP_ADDR, $GOODS_NAME, $DELIVERY_CP, $CON_DELIVERY_TYPE, $DELIVERY_FEE, $DELIVERY_FEE_CODE, $s_adm_no);

						}

					}
				}
			}

		}
?>	
<script language="javascript">
		alert('������� �Ϸ� �Ǿ����ϴ�.');
		location.href ="sel_work_list.php";
</script>
<?
	exit;

	}
	
	//	�۾��� ���� �������
	if ($mode == "PI") {

		$result = getWorkedOrderGoodsNo($conn, $chk_work_date, $order_field);

		for($k = 0; $k < sizeof($result); $k ++) { 
			$temp_order_goods_no = SetStringFromDB($result[$k]["ORDER_GOODS_NO"]);
			$reserve_no			 = SetStringFromDB($result[$k]["RESERVE_NO"]);
			$individual_no		 = SetStringFromDB($result[$k]["INDIVIDUAL_NO"]);
			$DELIVERY_TYPE		 = SetStringFromDB($result[$k]["DELIVERY_TYPE"]);
			$DELIVERY_CP	     = SetStringFromDB($result[$k]["DELIVERY_CP"]);
			$SENDER_NM			 = SetStringFromDB($result[$k]["SENDER_NM"]);
			$SENDER_PHONE		 = SetStringFromDB($result[$k]["SENDER_PHONE"]);

			$DELIVERY_FEE_CODE = $DELIVERY_CP."-����";
			$DELIVERY_FEE = getDcodeName($conn, "DELIVERY_FEE", $DELIVERY_FEE_CODE); 

			//2016-11-24 �����°��� ���ü �̸��� ���� �� ���ü �ּҸ�, �ٸ��� �ּҿ��� ��ü�� ����
			//$CON_SEND_CP_ADDR = "��� �����ֽ� ������ ��縮 98���� �߱���Ʈ��";
			$arr_op_cp = getOperatingCompany($conn, $s_adm_com_code);

			$OP_CP_NM		= SetStringFromDB($arr_op_cp[0]["CP_NM"]);
			$OP_CP_ADDR		= SetStringFromDB($arr_op_cp[0]["CP_ADDR"]);

			if($SENDER_NM == $OP_CP_NM)
				$CON_SEND_CP_ADDR = $OP_CP_ADDR;
			else
				$CON_SEND_CP_ADDR = str_replace($OP_CP_NM, "", $OP_CP_ADDR);
			
			
			//�ù�ȸ�� ������ ������� �Ұ�
			if($DELIVERY_CP == "" || $DELIVERY_FEE == "") {  
				continue;
			}

			//�����ù��� �����ּҰ� �Էµ��� �ʾ����� ������� �Ұ�
			if($DELIVERY_TYPE == "3" && ($individual_no == null || $individual_no == "")) {  
				continue;
			}

			//��������, ������, �ܺξ�ü�߼�, ��Ÿ�� ���� ���� ����
			if($DELIVERY_TYPE == "1" || $DELIVERY_TYPE == "2" || $DELIVERY_TYPE == "98" || $DELIVERY_TYPE == "99") 
				continue;

			// �̹� ������ ������ ����
			if(countOrderDeliveryPaper($conn, $temp_order_goods_no, $individual_no) <= 0)
			{
				
				$arr_order = selectOrder($conn, $reserve_no);

				$GOODS_DELIVERY_NAME = "";
				$SUB_QTY = "";
				$MEMO_ALL = "������� ��ǰ�Դϴ�-�ιڽ��� �ѼյǴ� ������ �����ּ���~";

				$CP_NO = SetStringFromDB($arr_order[0]["CP_NO"]);

				if($individual_no == "") { 
					$R_MEM_NM = SetStringFromDB($arr_order[0]["R_MEM_NM"]);
					$R_PHONE  = SetStringFromDB($arr_order[0]["R_PHONE"]);
					$R_HPHONE = SetStringFromDB($arr_order[0]["R_HPHONE"]);
					$R_ADDR1  = SetStringFromDB($arr_order[0]["R_ADDR1"]);
					$MEMO	  = SetStringFromDB($arr_order[0]["MEMO"]);
				
				} else {
					$arr_individual = selectDeliveryIndividual($conn, $individual_no);
			
					$R_MEM_NM				  = SetStringFromDB($arr_individual[0]["R_MEM_NM"]);
					$R_PHONE				  = SetStringFromDB($arr_individual[0]["R_PHONE"]);
					$R_HPHONE				  = SetStringFromDB($arr_individual[0]["R_HPHONE"]);
					$R_ADDR1				  = SetStringFromDB($arr_individual[0]["R_ADDR1"]);
					$MEMO					  = SetStringFromDB($arr_individual[0]["MEMO"]);
					$INDIVIDUAL_DELIVERY_TYPE = SetStringFromDB($arr_individual[0]["DELIVERY_TYPE"]);
					$USE_TF					  = SetStringFromDB($arr_individual[0]["USE_TF"]);

					//�������� ��� �н�
					if($USE_TF != "Y") 
						continue;

					//�ù谡 �ƴϹǷ� �н�
					if($INDIVIDUAL_DELIVERY_TYPE != "0")
						continue;
					
					//���� �Է� ������� ������� �⺻ ������� �Է�
					if($R_ADDR1 == "")							
						$R_ADDR1  = $arr_order[0]["R_ADDR1"];

					$GOODS_DELIVERY_NAME	= SetStringFromDB($arr_individual[0]["GOODS_DELIVERY_NAME"]); 
					$SUB_QTY				= SetStringFromDB($arr_individual[0]["SUB_QTY"]);
				}
				//������ ���ݾ��ֱ� /*2016-02-25 �����*/
				$R_MEM_NM = str_replace(" ","", $R_MEM_NM); 
				
				if($MEMO == "")
					$MEMO = $MEMO_ALL;

				$arr_order_goods = selectOrderGoods($conn, $temp_order_goods_no);
				if(sizeof($arr_order_goods) > 0) { 
					for($i=0; $i < sizeof($arr_order_goods); $i++) {

						$CP_ORDER_NO		 = SetStringFromDB($arr_order_goods[$i]["CP_ORDER_NO"]);
						$GOODS_NAME			 = SetStringFromDB($arr_order_goods[$i]["GOODS_NAME"]);
						$WORK_SEQ			 = SetStringFromDB($arr_order_goods[$i]["WORK_SEQ"]);
						$CATE_01			 = SetStringFromDB($arr_order_goods[$i]["CATE_01"]);
						$DELIVERY_CNT_IN_BOX = SetStringFromDB($arr_order_goods[$i]["DELIVERY_CNT_IN_BOX"]);
						
						if ($individual_no != "")
							$QTY = $SUB_QTY;
						else 
							$QTY = getRefundAbleQty($conn, $reserve_no, $temp_order_goods_no); 

						if($QTY == 0) //��ü ����ϰ��
							continue;

						// �����ù迡 �����ǰ���� �ִٸ� �װɷ� ǥ��
						if($GOODS_DELIVERY_NAME != "")
							$GOODS_NAME = $GOODS_DELIVERY_NAME." / ".$QTY."��";
						else
							$GOODS_NAME = $GOODS_NAME." / ".$QTY."��";

						//����,���� �߰�
						if($CATE_01 != "") 
							$GOODS_NAME = $CATE_01.")".$GOODS_NAME;

						//â�� ���Ǹ� ���� �۾������߰�
						$GOODS_NAME = "[".$WORK_SEQ."��] ".$GOODS_NAME;
						
						$total_paper_qty = ceil($QTY / $DELIVERY_CNT_IN_BOX);

						for($y=0; $y < $total_paper_qty; $y++) { 
							
							$DELIVERY_CNT = $total_paper_qty;
							$SEQ_OF_DELIVERY = $y + 1;
							$CON_ORDER_QTY = "1";
							$CON_PAYMENT_TYPE = "�ſ�";
							$CON_DELIVERY_TYPE = "�ù�";

							if($total_paper_qty > 1)
								$RECEIVER_NAME = $R_MEM_NM.$DELIVERY_CNT."-".$SEQ_OF_DELIVERY;
							else
								$RECEIVER_NAME = $R_MEM_NM;

							//������ �ڵ�����ȣ�� ������� ������ ��ȭ��ȣ�� �Է�
							if($R_PHONE != "" && $R_HPHONE == "")
								$R_HPHONE = $R_PHONE;
							
							$RECEIVER_NAME		= SetStringToDB($RECEIVER_NAME);
							$R_ADDR1			= SetStringToDB($R_ADDR1);
							$SENDER_NM			= SetStringToDB($SENDER_NM);
							$CON_SEND_CP_ADDR	= SetStringToDB($CON_SEND_CP_ADDR);
							$MEMO				= SetStringToDB($MEMO);

							$order_goods_delivery_no = insertOrderDeliveryPaper($conn, $chk_work_date, $reserve_no, $temp_order_goods_no, $individual_no, $CP_NO, $DELIVERY_CNT, $SEQ_OF_DELIVERY, $RECEIVER_NAME, $R_PHONE, $R_HPHONE, $R_ADDR1, $CON_ORDER_QTY, $MEMO, $SENDER_NM, $SENDER_PHONE, $SENDER_NM, $SENDER_PHONE, $CON_PAYMENT_TYPE, $CON_SEND_CP_ADDR, $GOODS_NAME, $DELIVERY_CP, $CON_DELIVERY_TYPE, $DELIVERY_FEE, $DELIVERY_FEE_CODE, $s_adm_no);

						}

					}
				}
			}

		}
?>	
<script language="javascript">
		alert('������� �Ϸ� �Ǿ����ϴ�.');
		location.href ="sel_work_list.php";
</script>
<?
	exit;

	}

	// �����Է�, �۾�����
	if ($mode == "T") {

		$row_cnt = count($work_seq);

		$arr_work_seq = array();

		for($j=0; $j <= ($row_cnt - 1) ; $j++) {
			
			if ($order_goods_no[$j]) {

				$arr_work_seq[$j]["ORDER_GOODS_NO"] = $order_goods_no[$j];
				$arr_work_seq[$j]["WORK_SEQ"] = $work_seq[$j];
				$arr_work_seq[$j]["WORK_DATE"] = ($arr_work_date[$j] != "" ? $arr_work_date[$j] : $hid_work_date );
			}
		}

		foreach ($arr_work_seq as $key => $row) {
			$KEY_WORK_SEQ[$key]  = $row['WORK_SEQ'];
		}
		
		if(sizeof($KEY_WORK_SEQ) > 0)
			array_multisort($KEY_WORK_SEQ, SORT_ASC, $arr_work_seq);

		foreach ($arr_work_seq as $key => $row) {

			updateWorkSeq($conn, $row['ORDER_GOODS_NO'], $row['WORK_DATE'], $s_adm_no);
			// insertOrderWorkLog($conn, $row['ORDER_GOODS_NO'], $row['WORK_DATE'], $s_adm_no);
		}

	}

	// ��������
	if ($mode == "RELEASE_ORDER_GOODS_WORK_SEQ") {

		deleteWorks($conn, $no_selected_reserve_no, $s_adm_no);

	}

#====================================================================
# Request Parameter
#====================================================================

	if ($start_date == "") {
		$start_date = date("Y-m-d",strtotime("-2 month"));
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("15 day"));
	} else {
		$end_date = trim($end_date);
	}

	$work_date = date("Y-m-d",strtotime("1 day"));

	$day_1_plus = date("Y-m-d",strtotime("1 day"));
	$day_0 = date("Y-m-d",strtotime("0 month"));
	$day_1 = date("Y-m-d",strtotime("-1 day"));
	$day_7 = date("Y-m-d",strtotime("-7 day"));
	$day_31 = date("Y-m-d",strtotime("-1 month"));

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);

#===============================================================
# Get Search list count
#===============================================================

	$arr_rs = listWorkOrder($conn, $order_type, $start_date, $end_date, $order_state, $cp_no, $work_order_type, $search_field, $search_str, $order_field, $order_str);


?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<script type="text/javascript" src="../jquery/jquery.floatThead.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<style>
	select[name=delivery_type] {display:none;}
	select[name=delivery_cp] {display:none;}
</style>
<script>
    $(function() {
		$( ".datepicker" ).datepicker({
			buttonImage: "/manager/images/calendar/cal.gif",
			buttonImageOnly: true,
			buttonText: "Select date",
			showOn: "both",
			dateFormat: "yy-mm-dd",
			changeMonth: true,
			changeYear: true,
			beforeShow: function() {
			setTimeout(function(){
				$('.ui-datepicker').css('z-index', 99999999999999);
				}, 0);
			}
		});

		$( ".datepicker2" ).datepicker({
			buttonImage: "/manager/images/calendar/cal.gif",
			buttonImageOnly: true,
			buttonText: "Select date",
			showOn: "both",
			dateFormat: "yy-mm-dd",
			changeMonth: true,
			changeYear: true,
			numberOfMonths: 2,
			beforeShow: function() {
			setTimeout(function(){
				$('.ui-datepicker').css('z-index', 99999999999999);
				}, 0);
			}
		});
	});
</script>
<script>
	$(function(){
		$('table.fixed_header_table').floatThead({
			position: 'fixed'
		});
	});
</script>
<script language="javascript">
	
	function getMaxWorkSeq() {
		
		var frm = document.frm;
		var max_order_no = 0;

		if (frm['work_seq[]'].length != null) {

			for (i = 0 ; i < frm['work_seq[]'].length; i++) {
				if (frm['work_seq[]'][i].value > max_order_no) {
					max_order_no = eval(frm['work_seq[]'][i].value);
				}
			}
		} else {
				if (frm['work_seq[]'].value > max_order_no) {
					max_order_no = eval(frm['work_seq[]'].value);
				}
		}

		return max_order_no + 1;

	}


	//var work_seq_no = 3;

	function js_check_seq(idx) {
		
		var frm = document.frm;

		var work_seq_no = getMaxWorkSeq();

		frm.no_selected_reserve_no.value = "";

		if (frm['chk_no[]'].length != null) {
			
			if (frm['chk_no[]'][idx].checked == true) {
				frm['work_seq[]'][idx].value = work_seq_no;

				work_seq_no = eval(work_seq_no) + 1;
			} else {
				for (i = 0 ; i < frm['work_seq[]'].length; i++) {
					if (eval(frm['work_seq[]'][i].value) > eval(frm['work_seq[]'][idx].value)) {
					
						//alert(frm['work_seq[]'][i].value);
						frm['work_seq[]'][i].value = eval(frm['work_seq[]'][i].value) -1;
						//alert(frm['work_seq[]'][i].value);
					}
				}
				frm['work_seq[]'][idx].value = "";
				work_seq_no = eval(work_seq_no) - 1;
			}
		} else {

			if (frm['chk_no[]'].checked == true) {
				frm['work_seq[]'].value = work_seq_no;
				work_seq_no = eval(work_seq_no) + 1;
			} else {
				frm['work_seq[]'].value = "";
				work_seq_no = eval(work_seq_no) - 1;
			}

		}
	}

	function js_view(reserve_no) {

		var frm = document.frm;
		
		var url = "../order/order_read.php?reserve_no="+reserve_no;

		NewWindow(url, 'order_detail','860','600','YES');
	}

	// ��ȸ ��ư Ŭ�� �� 
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_work_save() {

		var frm = document.frm;
		var chk_cnt = 0;

		if (frm['chk_no[]'] == null) {
			alert("������ �ֹ��� �����ϴ�.");
			return;
		}
		

		if (frm['chk_no[]'].length != null) {
			
			for (i = 0 ; i < frm['chk_no[]'].length; i++) {
				if (frm['chk_no[]'][i].checked == true) {
					chk_cnt = 1;
					frm['order_goods_no[]'][i].value = frm['chk_no[]'][i].value;
				} else {
					if (frm.no_selected_reserve_no.value == "") {
						frm.no_selected_reserve_no.value = frm['chk_no[]'][i].value;
					} else {
						frm.no_selected_reserve_no.value = frm.no_selected_reserve_no.value +","+ frm['chk_no[]'][i].value;
					}
				}
			}
		} else {
			if (frm['chk_no[]'].checked == true) {
				chk_cnt = 1;
				frm['order_goods_no[]'].value = frm['chk_no[]'].value;
			} else {
				frm.no_selected_reserve_no.value = frm['chk_no[]'].value;
			}
		}
		
		if (chk_cnt == 0) {
			//alert("�۾� ����Ʈ�� ��� �� �ֹ��� ������ �ּ���");
			//return;
		}

		bDelOK = confirm('�۾� ����Ʈ�� ��� �Ͻðڽ��ϱ�?');
			
		if (bDelOK==true) {

			frm.mode.value = "T";
			frm.target = "";
			frm.hid_work_date.value = frm.work_date.value;
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_selected_delivery_paper() {

		var frm = document.frm;
		var chk_cnt = 0;

		if (frm['chk_no[]'] == null) {
			alert("������ �ֹ��� �����ϴ�.");
			return;
		}
		

		if (frm['chk_no[]'].length != null) {
			
			for (i = 0 ; i < frm['chk_no[]'].length; i++) {
				if (frm['chk_no[]'][i].checked == true) {
					chk_cnt = 1;
					frm['order_goods_no[]'][i].value = frm['chk_no[]'][i].value;
				} else {
					if (frm.no_selected_reserve_no.value == "") {
						frm.no_selected_reserve_no.value = frm['chk_no[]'][i].value;
					} else {
						frm.no_selected_reserve_no.value = frm.no_selected_reserve_no.value +","+ frm['chk_no[]'][i].value;
					}
				}
			}
		} else {
			if (frm['chk_no[]'].checked == true) {
				chk_cnt = 1;
				frm['order_goods_no[]'].value = frm['chk_no[]'].value;
			} else {
				frm.no_selected_reserve_no.value = frm['chk_no[]'].value;
			}
		}
		
		bDelOK = confirm('������ �ֹ��� ��������� �Ͻðڽ��ϱ�?');
			
		if (bDelOK==true) {

			frm.chk_work_date.value = frm.work_date.value;

			frm.mode.value = "I_DELIVERY_PAPER";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	

	function js_excel() {

		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.target = "";
		frm.action = "<?=str_replace("list","excel_list",$_SERVER[PHP_SELF])?>";
		frm.submit();

	}

	function js_reload() {
		location.reload();
	}

	function js_pop_individual(reserve_no, order_goods_no) { 

		var frm = document.frm;
		
		var url = "/manager/order/pop_individual_delivery_list.php?reserve_no="+reserve_no+"&order_goods_no="+order_goods_no;

		NewWindow(url, 'pop_individual_delivery_list','1000','600','YES');
	}

	var day_1_plus = "<?=$day_1_plus?>";
	var day_0 = "<?=$day_0?>";
	var day_1 = "<?=$day_1?>";
	var day_7 = "<?=$day_7?>";
	var day_31 = "<?=$day_31?>";

	function js_search_date(iday) {

		var frm = document.frm;

		if (iday == -1) {
			frm.start_date.value = day_1_plus;
			frm.end_date.value = day_1_plus;
		}

		if (iday == 0) {
			frm.start_date.value = day_0;
			frm.end_date.value = day_0;
		}

		if (iday == 1) {
			frm.start_date.value = day_1;
			frm.end_date.value = day_0;
		}

		if (iday == 7) {
			frm.start_date.value = day_7;
			frm.end_date.value = day_0;
		}

		if (iday == 31) {
			frm.start_date.value = day_31;
			frm.end_date.value = day_0;
		}

		frm.nPage.value = "1";
		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}
	
	/*
	function js_reset() {
		
		var frm = document.frm;
		frm.start_date.value = "<?=date("Y-m-d",strtotime("-1 month"))?>";
		frm.end_date.value = "<?=date("Y-m-d",strtotime("0 month"))?>";
		frm.sel_order_state.value = "";
		
		<? if ($s_adm_cp_type == "�") { ?>
			frm.cp_type.value = "";
			frm.cp_type2.value = "";
			frm.txt_cp_type.value = "";
			frm.txt_cp_type2.value = "";
			frm.sel_pay_type.value = "";
		<? } ?>
		
		frm.order_field.value = "ORDER_DATE";
		frm.order_str[0].checked = true;
		frm.nPageSize.value = "20";
		frm.search_field.value = "ALL";
		frm.search_str.value = "";
	}
	*/

	function js_delivery_save() {

		bDelOK = confirm('��������� ��� �Ͻðڽ��ϱ�?');
		
		if (bDelOK==true) {

			frm.mode.value = "DELIVERY";
			frm.target = "";
			frm.method = "post";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}

	}

	function js_cancel_delivery(order_goods_no) {

		bDelOK = confirm('�ش� �ֹ��� ����� ��� �Ͻðڽ��ϱ�?');
		
		if (bDelOK==true) {
			frm.cancel_order_goods_no.value = order_goods_no;
			frm.mode.value = "CANCEL";
			frm.target = "";
			frm.method = "post";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}


	function js_stock_view(order_goods_no) {

		var url = "popup_stock_goods.php?order_goods_no="+order_goods_no;
		NewWindow(url,'popup_stock_goods','820','700','YES');

	}

	function js_opt_memo_view(order_goods_no) {

		var url = "popup_opt_memo.php?order_goods_no="+order_goods_no;
		NewWindow(url,'popup_opt_memo','820','700','YES');

	}

	function js_create_delivery_paper(work_date) {
		
		var frm = document.frm;
		frm.target = "";
		frm.chk_work_date.value = work_date;
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.mode.value = "PI";
		frm.method = "post";
		frm.submit();
	}

	function js_delivery_paper_loading() { 

		var url = "/manager/order/pop_delivery_paper_loading.php";

		NewWindow(url, 'pop_delivery_paper_loading','1000','500','YES');

	}

	function js_list_delivery_paper(reserve_no, order_goods_no) {

		var url = "/manager/order/pop_delivery_paper_list.php?reserve_no=" + reserve_no + "&order_goods_no=" + order_goods_no;

		NewWindow(url, 'pop_delivery_paper_list','1000','500','YES');
		
	}

	function js_sub_goods_popup(start_work_date, end_work_date) {
		
		var url = "popup_work_goods.php?start_work_date=" + start_work_date + "&end_work_date=" + end_work_date;

		NewWindow(url,'popup_work_goods','820','700','YES');

	}


	function js_release_work_seq(order_goods_no) {
		
		if(confirm('�� �ֹ��� ������ ���� ���ܽ�Ű�ðڽ��ϱ�? \n ���� �Է��۾� ���̽ø� �Է� Ȯ�� ���Ŀ� �������ּ���. ���ΰ�ħ�˴ϴ�.')) { 
			var frm = document.frm;
			frm.target = "";
			frm.no_selected_reserve_no.value = order_goods_no;
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.mode.value = "RELEASE_ORDER_GOODS_WORK_SEQ";
			frm.method = "post";
			frm.submit();
		}

	}
</script>

</head>

<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="reserve_no" value="">
<input type="hidden" name="no_selected_reserve_no" value="">
<input type="hidden" name="cancel_order_goods_no" value="">
<input type="hidden" name="use_tf" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="chk_work_date" value="<?=$chk_work_date?>">
<input type="hidden" name="order_field" value="<?=$order_field?>">
<input type="hidden" name="con_order_type" value="<?=$con_order_type?>">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<input type="hidden" name="hid_work_date" value="">
<!--<input type="hidden" name="nPageSize" value="<?=$nPageSize?>">-->
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
				<h2 style="margin:0;">���� �۾� ����
				</h2>
				<div class="btnright" style="margin:0 0 5px;">
					<? if (($sPageRight_I == "Y") && ($sPageRight_U == "Y") && ($sPageRight_D == "Y")) { ?>
					<input type="button" name="bb" value="����ε� �� �����ȣ���" onclick="this.style.visibility='hidden'; js_delivery_paper_loading()"/>
					<? } ?>
				</div>
				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="10%" />
					<col width="*" />
					<col width="10%" />
					<col width="*" />
					<col width="6%" />
				</colgroup>
					<tr>
						<th>�����</th>
						<td colspan="3">
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="start_date" value="<?=$start_date?>" maxlength="10"/>
							 ~ 
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="end_date" value="<?=$end_date?>" maxlength="10"/>
							&nbsp;<input type="button" name="bb" onclick="javascript:js_search_date('-1');" value="����"/>
							<input type="button" name="bb" onclick="javascript:js_search_date('0');" value="����"/>
							<input type="button" name="bb" onclick="javascript:js_search_date('1');" value="����"/>
							<input type="button" name="bb" onclick="javascript:js_search_date('7');" value="7����"/>
							<input type="button" name="bb" onclick="javascript:js_search_date('31');" value="31����"/>
						</td>
						<td align="right">
							<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="���� ����Ʈ" /></a>
						</td>
					</tr>
					<tr>
						<th>����</th>
						<td>
							<select name="order_field" style="width:94px;">
								<option value="OPT_OUTSTOCK_DATE" <? if ($order_field == "OPT_OUTSTOCK_DATE") echo "selected"; ?> >�������</option>
								<option value="ORDER_DATE" <? if ($order_field == "ORDER_DATE") echo "selected"; ?> >�ֹ���</option>
								<option value="CP_NO" <? if ($order_field == "CP_NO") echo "selected"; ?> >�ֹ���ü</option>
								<option value="R_MEM_NM" <? if ($order_field == "R_MEM_NM") echo "selected"; ?> >������</option>
								<option value="GOODS_NAME" <? if ($order_field == "GOODS_NAME") echo "selected"; ?> >��ǰ��</option>
							</select>
						</td>
						<th>����</th>
						<td colspan="2">
							<select name="work_order_type">
								<option value="Y" <?if($work_order_type == "Y") echo "selected";?>>���� ����Ʈ</option>
								<option value="N" <?if($work_order_type == "N") echo "selected";?>>������ ����Ʈ</option>
								<option value="" <?if($work_order_type == "") echo "selected";?>>��ü</option>
							</select>
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						</td>
						
					</tr>
			</table>

			<div class="sp20"></div>
			<b>�� <span class="total_qty"></span> �� </b>
			<!--*  �۾� ���°� �Ϸᰡ �ƴ� �ֹ� ��ǰ ����Ʈ ��ü, �۾��ϷḦ �����ϵ���-->
			<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table" border="0" width="100%">

				<colgroup>
					<col width="3%" />
					<col width="3%" />
					<col width="8%" />
					<col width="13%" />
					<col width="*" />
					<col width="6%" />
					<col width="15%" />
					<col width="9%" />
					<col width="7%" />
					<col width="5%" />
					<col width="5%" />
					<col width="5%" />
				</colgroup>
				<thead>
					<tr>
						<th rowspan="2" colspan="2">����</th>
						<th rowspan="2">�ֹ���<br/>�����<br/>(�������)</th>
						<th>�ֹ���ü</th>
						<th rowspan="2" >��ǰ��</th>
						<th rowspan="2">�ֹ�/�۾�<br/>���ü���</th>
						<th>�۾�����/����/�޸�</th>
						<th rowspan="2">��۹��</th>
						<th rowspan="2">�ù��</th>
						<th rowspan="2">�����</th>
						<th rowspan="2">
							<input type="checkbox" name="show_stock" <?if($show_stock == "Y") echo "checked";?> value="Y"/>���
							<script type="text/javascript">
								$('[name=show_stock]').on('change', function() {
								  js_search();
								})
							</script>	
						</th>
						<th rowspan="2" class="end">�������</th>
					</tr>
					<tr>
						<th>������</th>
						<th>â��޸�</th>
					</tr>
				</thead>

				<tbody>
				<?
					$nCnt = 0;
					$cancelQty = 0;
					
					// �ʱⰪ 
					$temp_date = "";


					if (sizeof($arr_rs) > 0) {
						$g = 0;
						$o = 0;
						for ($h = 0 ; $h < sizeof($arr_rs); $h++) {
							
							$rn								  = trim($arr_rs[$h]["rn"]);
							$RESERVE_NO					      = trim($arr_rs[$h]["RESERVE_NO"]);
							$ORDER_GOODS_NO				      = trim($arr_rs[$h]["ORDER_GOODS_NO"]);
							$ORDER_DATE						  = trim($arr_rs[$h]["ORDER_DATE"]);
							$OPT_OUTSTOCK_DATE				  = trim($arr_rs[$h]["OPT_OUTSTOCK_DATE"]);
							$CP_NO					 		  = trim($arr_rs[$h]["CP_NO"]);
							$CP_NM					 		  = trim($arr_rs[$h]["CP_NM"]);
							$CP_NM2					 		  = trim($arr_rs[$h]["CP_NM2"]);
							$O_MEM_NM						  = trim($arr_rs[$h]["O_MEM_NM"]);
							$R_MEM_NM						  = trim($arr_rs[$h]["R_MEM_NM"]);
							$CATE_01						  = trim($arr_rs[$h]["CATE_01"]);
							$CATE_04						  = trim($arr_rs[$h]["CATE_04"]);
							$GOODS_CODE						  = trim($arr_rs[$h]["GOODS_CODE"]);
							$GOODS_NAME						  = trim($arr_rs[$h]["GOODS_NAME"]);
							$OPT_MANAGER_NAME				  = trim($arr_rs[$h]["OPT_MANAGER_NAME"]);
							$OPT_MEMO						  = trim($arr_rs[$h]["OPT_MEMO"]);
							$WORK_START_DATE				  = trim($arr_rs[$h]["WORK_START_DATE"]);
							$WORK_SEQ						  = trim($arr_rs[$h]["WORK_SEQ"]);
							$BULK_TF	  					  = trim($arr_rs[$h]["BULK_TF"]);
							$GOODS_NO						  = trim($arr_rs[$h]["GOODS_NO"]);
							$DELIVERY_TYPE					  = trim($arr_rs[$h]["DELIVERY_TYPE"]);
							$DELIVERY_TYPE_NAME				  = trim($arr_rs[$h]["DELIVERY_TYPE_NAME"]);

							
							$DELIVERY_CP					  = trim($arr_rs[$h]["DELIVERY_CP"]);
							$WORK_MSG						  = trim($arr_rs[$h]["WORK_MSG"]);
							$REG_DATE						  = trim($arr_rs[$h]["REG_DATE"]);

							$WORK_REQ_QTY					  = trim($arr_rs[$h]["WORK_REQ_QTY"]);
							$WORK_REG_ADM					  = trim($arr_rs[$h]["WORK_REG_ADM"]);
							$WORK_REG_ADM_NAME				  = trim($arr_rs[$h]["WORK_REG_ADM_NAME"]);
							
							$WORK_REG_DATE					  = trim($arr_rs[$h]["WORK_REG_DATE"]);
							$WORK_QTY						  = trim($arr_rs[$h]["WORK_QTY"]);

							$QTY							  = trim($arr_rs[$h]["QTY"]);

							//2018.11.08 ���ֿ���ǥ��
							$RGN_COUNT						  = trim($arr_rs[$h]["RGN_COUNT"]);

							


							//// �ӵ����� - �����߰� ������ ������ ���� /////// 
							$rs_goods_no			= trim($arr_rs[$h]["GOODS_NO"]);
							$rs_opt_wrap_no			= trim($arr_rs[$h]["OPT_WRAP_NO"]);
							$rs_opt_wrap_name		= trim($arr_rs[$h]["OPT_WRAP_NAME"]);
							$rs_opt_sticker_no		= trim($arr_rs[$h]["OPT_STICKER_NO"]);
							$rs_opt_sticker_name	= trim($arr_rs[$h]["OPT_STICKER_NAME"]);
							$rs_opt_sticker_ready	= trim($arr_rs[$h]["OPT_STICKER_READY"]);
							$rs_opt_outbox_tf		= trim($arr_rs[$h]["OPT_OUTBOX_TF"]);
							$rs_opt_sticker_msg		= trim($arr_rs[$h]["OPT_STICKER_MSG"]);
							$rs_opt_print_msg		= trim($arr_rs[$h]["OPT_PRINT_MSG"]);


							// ������ �����ؼ� �ε� �ð����� 2018-02-28
 							//$QTY = getRefundAbleQty($conn, $RESERVE_NO, $ORDER_GOODS_NO); 
							
							//��ü��Ұ��� ����
							//if($QTY == 0) { 
							//	$cancelQty ++; 
							//	continue;
							//}

							$str_reg_info = "";
							if($WORK_REG_ADM != null && $WORK_REG_ADM != "0")
								$str_reg_info .= "�����: ".$WORK_REG_ADM_NAME;
							if($WORK_REG_DATE != null && $WORK_REG_DATE != "0000-00-00 00:00:00")
								$str_reg_info .= ", ����Ͻ�: ".date("Y-m-d H:i", strtotime($WORK_REG_DATE));

							if($CATE_01 <> "")
								$GOODS_NAME = $CATE_01.")".$GOODS_NAME;

							if ($CATE_04 == "CHANGE") {
								$str_cate_04 = "<font color='red'>(��ȯ��)</font>";
							} else {
								$str_cate_04 = "";
							}

							if ($WORK_START_DATE == '0000-00-00 00:00:00' || $WORK_START_DATE == "") 
								$WORK_START_DATE = "";
							else
								$WORK_START_DATE = date("Y-m-d",strtotime($WORK_START_DATE));

							if($show_stock == "Y")
								$stock_flag = checkStock($conn, $GOODS_NO, $QTY - $WORK_QTY);

							// ����� = �ֹ��� ������ ǥ���� �ʿ� ����
							if(date("n�� j��",strtotime($REG_DATE)) != date("n�� j��",strtotime($ORDER_DATE)))  
								$REG_DATE = "<br/><span style='color:blue;'>".date("n�� j��",strtotime($REG_DATE))."</span>";
							else
								$REG_DATE = "";
							
							if ($temp_date <> $WORK_START_DATE) {
						?>
						<tr>
							<td colspan="12" height="35" style="text-align:left;padding-left:10px;background: #DEDEDE;vertical-align: middle; border-top:1px dotted black;" class="date_group_title" data-work_date="<?=$WORK_START_DATE?>" data-is_visible="true">
								<? if ($WORK_START_DATE) { ?>
								<b>�۾��� : <?=left($WORK_START_DATE,10)?></b> &nbsp;&nbsp;&nbsp;
								<input type="button" name="aa" value=" ���� ���� " class="btntxt" onclick="this.style.visibility='hidden'; disabledEventPropagation(event); js_create_delivery_paper('<?=$WORK_START_DATE?>');">&nbsp;&nbsp;
								<input type="button" name="bb" value=" ���� ��ȸ " class="btntxt" onclick="disabledEventPropagation(event);  js_sub_goods_popup('<?=$WORK_START_DATE?>', '<?=$WORK_START_DATE?>');">
								<? } else { ?>
								<b>������ �ֹ� ����Ʈ</b>
								<? } ?>
							</td>
						</tr>
						<?
								$temp_date = $WORK_START_DATE;
							}
						?>
						
						<tr height="35"  class="row_end date_group" data-work_date="<?=$WORK_START_DATE?>">
							
							<? if($WORK_SEQ == 0) { ?>
								<td colspan="2" class="second_floor text_align_center" >
									
									<input type="checkbox" name="chk_no[]" data-work_seq="<?=$WORK_SEQ?>" onClick="js_check_seq('<?=$g?>');" value="<?=$ORDER_GOODS_NO?>" <?= ($WORK_SEQ > 0 ? "checked='checked'" : "") ?> />
									<input type="hidden" name="order_goods_no[]" value="" />
									
									<? if ($sPageRight_D == "Y") { ?>
									<input type="hidden" name="arr_work_date[]" value="<?=$WORK_START_DATE?>" />
									<? 
										$g ++;
										} 
									?>
								</td>
							<? } else { ?>
								<td rowspan="2" colspan="2" class="text_align_center">
									<b class="seq" title="<?=$str_reg_info?>"><?=($WORK_SEQ > 0 ? $WORK_SEQ : "")?></b>
									<? if ($sPageRight_D == "Y") { ?>
									<input type="button" class="display_none btn_release" value="��������" onclick="js_release_work_seq('<?=$ORDER_GOODS_NO?>')"/>
									<? } ?>
								</td>
							<? } ?>
							<td class="second_floor" style="text-align:center"><?=date("n�� j��",strtotime($ORDER_DATE))."black"?><?if($REG_DATE != "") echo $REG_DATE."blue"; ?>
							</td>
							<td class="modeual_nm second_floor"><?= $CP_NM." ".$CP_NM2 ?></td>
							<td rowspan="2" class="modeual_nm" title="<?=$GOODS_CODE?>"><?=$str_cate_04?>
								<a href="javascript:js_view('<?=$RESERVE_NO?>');"><?= $RGN_COUNT > 0 ? "<b>*</b> " : "" ?><?=$GOODS_NAME?></a>
							</td>
							<td rowspan="2">
							<? if ($WORK_QTY > 0 || ($WORK_REQ_QTY != $QTY && $WORK_REQ_QTY > 0)) { ?>
								<?=number_format($QTY)?>
							<? } ?>
							<? if ($WORK_QTY > 0) { ?>
								 / <?=number_format($WORK_QTY)?>
							<? } ?>
							<br/>
							<?
								if($WORK_REQ_QTY != 0) { 
									if($WORK_REQ_QTY > ($QTY-$WORK_QTY))
										$selected_qty = $QTY-$WORK_QTY;
									else
										$selected_qty = $WORK_REQ_QTY;
								} else
									$selected_qty = $QTY-$WORK_QTY;
							?>
							<input type="text" name="req_qty[]" data-order_goods_no="<?=$ORDER_GOODS_NO?>"  data-ori_qty="<?=$selected_qty?>" value="<?=$selected_qty?>" style="width:90%;"/>
							</td>
							<td class="second_floor memo_trigger" style="<?= ($WORK_START_DATE ? "text-align:right;" : "text-align:left;") ?>padding-right:10px;padding-top:5px;padding-bottom:5px;" data-order_goods_no="<?=$ORDER_GOODS_NO?>">
								<?
										$option_str	= "";

										$sticker_ready = "<input type='checkbox' name='opt_sticker_ready' data-order_goods_no='".$ORDER_GOODS_NO."'  ".($rs_opt_sticker_ready == "Y" ? "checked" : "")." />";
										$option_str .= ($rs_opt_sticker_no <> "0" ? $sticker_ready."<b>��ƼĿ</b> : ".$rs_opt_sticker_name." <br/>" : "");
										$option_str .= ($rs_opt_outbox_tf == "Y" ? "<b>�ƿ��ڽ���ƼĿ</b> : ���� <br/>" : "" );
										$option_str .= ($rs_opt_wrap_no <> "0" ? "<b>������</b> : ".$rs_opt_wrap_name. " <br/>" : "");
										$option_str .= ($rs_opt_sticker_msg <> "" ? "<b>��ƼĿ�޼���</b> : ".$rs_opt_sticker_msg. " <br/>" : "");
										$option_str .= ($rs_opt_print_msg <> "" ? "<b>�μ�޼���</b> : ".$rs_opt_print_msg. " <br/>" : "");

										echo $option_str;

								?>

								<?= ($OPT_MEMO <> "" ? "<font color='red'><b>�۾��޸�</b> : ".$OPT_MEMO."</font>" : "") ?>
								
							</td>
							<td rowspan="2" class="delivery_type">
								<div><b><?= $DELIVERY_TYPE_NAME ?></b></div>
								

								<?
									//�ε� �ӵ� ������ �񵿱� ��ȯ
									if($DELIVERY_TYPE == "3") { 
								?>
								<div class="delivery_individual" data-order_goods_no="<?=$ORDER_GOODS_NO?>">
									<a href="javascript:js_pop_individual('<?=$RESERVE_NO?>','<?=$ORDER_GOODS_NO?>');"> (<span class="cnt_delivery_place"></span>��)</a><br/>
									<span class="total_sub_qty"></span>��
								</div>
								<?
									} 
								?>
							</td>
							<td rowspan="2">
							
								<div class="delivery_cp" data-order_goods_no="<?=$ORDER_GOODS_NO?>" <?=($DELIVERY_TYPE != "0" && $DELIVERY_TYPE != "3" ? "style='display:none;'" : "")?>><b><?= ($DELIVERY_CP != "" ? $DELIVERY_CP : "<font color='red'>�ù������</font>") ?></b></div>
								
							</td>
							<td rowspan="2">
								<!--�ε� �ӵ� ������ �񵿱� ��ȯ-->
								<div class="delivery_list" data-reserve_no="<?=$RESERVE_NO?>" data-order_goods_no="<?=$ORDER_GOODS_NO?>" data-delivery_cp="<?=$DELIVERY_CP?>">
								</div>
							</td>
							<td rowspan="2" style="text-align:center">
								<a href="javascript:js_stock_view(<?=$ORDER_GOODS_NO?>);">
									<?  				
										if($show_stock == "Y") { 
											if ($stock_flag) {
												echo "<font color='blue'><b>����</b></font>";
											} else {
												echo "<font color='red'><b>����</b></font>";
											}
										} else { 
											echo "";
										}
									?>
									
								</a>
							</td>
							<td rowspan="2" style="text-align:center"><?=$OPT_MANAGER_NAME?></td>
						</tr>
						<tr height="35" class="row_end date_group" data-work_date="<?=$WORK_START_DATE?>">

							<? if($WORK_SEQ == "0") { ?>
							<td colspan="2" style="text-align:center">
								<? if (($sPageRight_I == "Y") && ($sPageRight_U == "Y") && ($sPageRight_D == "Y") && ($WORK_SEQ == "0")) { ?>
								<input type="text" name="work_seq[]" style="width:22px" value="<?=($WORK_SEQ > 0 ? $o+1 : "")?>" />
								<? } ?>
							</td>
							<? } else { ?>
				

							<? } ?>
							
							<td style="text-align:center; color:red;"><?=($OPT_OUTSTOCK_DATE != "0000-00-00 00:00:00" ? date("n�� j��",strtotime($OPT_OUTSTOCK_DATE)) : "������" )."red"?></td>
							<td class="modeual_nm"><?= $O_MEM_NM ?></td>
							<td class="modeual_nm"><?= $WORK_MSG ?></td>
						</tr>
						<?

							$o = $o + 1;
						} 
					} else {
						?>
						<tr>
							<td height="50" align="center" colspan="14">�����Ͱ� �����ϴ�. </td>
						</tr>
					<?
						}
					?>
					<script>
						$(function(){
							$(".total_qty").html('<?=$h - $cancelQty?>');
						});
					</script>

				</tbody>
			</table>
				
		<!-- // E: mwidthwrap -->
			</div>
			<!-- // E: mwidthwrap -->

		</td>
	</tr>
	</table>
	<div class="sp40"></div>
	<div style="display:scroll;position:fixed;bottom:10px;right:10px;padding:10px;border:1px solid black;background-color:#fff;">
		<? if($work_order_type <> "Y") { ?>
			�۾��� : <input type="text" class="txt datepicker2" style="width: 80px; margin-right:3px;" name="work_date" value="<?=$work_date?>" maxlength="10"/>&nbsp;&nbsp;
			<? if (($sPageRight_I == "Y") && ($sPageRight_U == "Y") && ($sPageRight_D == "Y")) { ?>
				<input type="button" name="aa" value=" ������ �ֹ� �۾�����Ʈ�� ��� " class="btntxt" onclick="js_work_save();">&nbsp;&nbsp;
			<? } ?>
				<input type="button" name="aa" value=" ������� " class="btntxt" onclick="js_selected_delivery_paper();">&nbsp;&nbsp;
		<? } ?>
		<a href="#">�� ����</a>
	</div>
</div>
<script>

	$(function(){

		$(".date_group_title").click(function(){
		
			var work_date = $(this).data("work_date");
			var is_visible = $(this).data("is_visible");

			if(is_visible) {
				$("tr.date_group[data-work_date="+work_date+"]").hide();
				$(this).data("is_visible", false);
			} else { 
				$("tr.date_group[data-work_date="+work_date+"]").show();
				$(this).data("is_visible", true);
			}

		});

		$("span").click(function(){

			$(this).hide();
			$(this).parent().find("select").show();

		});

		$("input[name=opt_sticker_ready]").click(function(event){

			event.stopPropagation();

			var order_goods_no = $(this).attr("data-order_goods_no");
			var opt_sticker_ready = $(this).is(':checked') ? "Y" : "N"; 

			(function() {
			  $.getJSON( "/manager/order/json_update_order_goods.php", {
				mode: "UPDATE_ORDER_GOODS_OPT_STICKER_READY",
				order_goods_no: order_goods_no,
				opt_sticker_ready: opt_sticker_ready
			  })
				.done(function( data ) {
				  $.each( data, function( i, item ) {
					  if(item.RESULT == "0")
						  alert('������� : ����� �ٽ� �õ����ּ���');
				  });
				});
			})();


		});

		$("span.qty").click(function(){
			var order_goods_no = $(this).data("order_goods_no");

			$(this).hide();
			$("input[name=work_qty_"+order_goods_no+"]").show().focus().select();
			
		});

		// ���� Ŭ���� "��������" ��ư ������
		$("b.seq").click(function(){
			$(this).closest("td").find("input[type=button]").show();
		});

		
		$(".memo_trigger").on("click",function(){
			var order_goods_no = $(this).data("order_goods_no");
			js_opt_memo_view(order_goods_no);
		});

		$(".work_qty").on('keydown',function(){

			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13'){ //Enter

				var order_goods_no = $(this).attr("data-order_goods_no");
				var qty = $(this).attr("data-qty");
				var work_qty = $(this).val();

				qty = qty.replace(",", "");

				if(parseInt(work_qty) > parseInt(qty))
				{
					alert('�ֹ��������� �۾������� Ŭ�� �����ϴ�.');
					return;
				}

				(function() {
				  $.getJSON( "/manager/order/json_update_order_goods.php", {
					mode: "UPDATE_ORDER_GOODS_WORK_QTY",
					order_goods_no: order_goods_no,
					work_qty: work_qty
				  })
					.done(function( data ) {
					  $.each( data, function( i, item ) {
						  if(item.RESULT == "0")
							  alert('������� : ����� �ٽ� �õ����ּ���');
						  else (item.RESULT == "1") 
						  {
							$("span[name=work_qty_"+item.ORDER_GOODS_NO+"]").html(item.WORK_QTY).show();
						  }

					  });
					});
				})();
				
				$(this).hide();
			}
		});

		$(".work_seq").on('keydown',function(){

			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13'){ //Enter

				var order_goods_no = $(this).data("order_goods_no");
				var work_seq = $(this).val();

				(function() {
				  $.getJSON( "/manager/order/json_update_order_goods.php", {
					mode: "UPDATE_ORDER_GOODS_WORK_SEQ",
					order_goods_no: order_goods_no,
					work_seq: work_seq
				  })
					.done(function( data ) {
					  $.each( data, function( i, item ) {
						  if(item.RESULT == "0")
							  alert('������� : ����� �ٽ� �õ����ּ���');
						  else (item.RESULT == "1") 
						  {
							$("span[name=work_seq_"+item.ORDER_GOODS_NO+"]").html(item.WORK_SEQ).show();
						  }

					  });
					});
				})();
				
				$(this).hide();
			}
		});

		$("select[name=delivery_type]").change(function(){
			var order_goods_no = $(this).attr("data-order_goods_no");
			var delivery_type = $(this).val();

			if(delivery_type == "0" || delivery_type == "3") { 
				$("select[name=delivery_cp][data-order_goods_no="+order_goods_no+"]").show();
				$("span[class=delivery_cp][data-order_goods_no="+order_goods_no+"]").hide();
			}
			else {
				$("select[name=delivery_cp][data-order_goods_no="+order_goods_no+"]").hide();
				$("span[class=delivery_cp][data-order_goods_no="+order_goods_no+"]").hide();
			}

			(function() {
			  $.getJSON( "/manager/order/json_update_order_goods.php", {
				mode: "UPDATE_ORDER_GOODS_DELIVERY_TYPE",
				order_goods_no: order_goods_no,
				delivery_type: delivery_type
			  })
				.done(function( data ) {
				  $.each( data, function( i, item ) {
					  if(item.RESULT == "0")
						  alert('������� : ����� �ٽ� �õ����ּ���');
				  });
				});
			})();

		});

		$("select[name=delivery_cp]").change(function(){
			var order_goods_no = $(this).attr("data-order_goods_no");
			var delivery_cp = $(this).val();

			(function() {
			  $.getJSON( "/manager/order/json_update_order_goods.php", {
				mode: "UPDATE_ORDER_GOODS_DELIVERY_CP",
				order_goods_no: order_goods_no,
				delivery_cp: delivery_cp
			  })
				.done(function( data ) {
				  $.each( data, function( i, item ) {
					  if(item.RESULT == "0")
						  alert('������� : ����� �ٽ� �õ����ּ���');
				  });
				});
			})();

		});

/*
		$("select[name='req_qty[]']").change(function(){
			var order_goods_no = $(this).data("order_goods_no");
			var req_qty = $(this).val();

			if(confirm('�۾� ������ �����Ͻðڽ��ϱ�?')) { 
				(function() {
				  $.getJSON( "/manager/order/json_update_order_goods.php", {
					mode: "UPDATE_ORDER_GOODS_REQ_QTY",
					order_goods_no: order_goods_no,
					req_qty: req_qty
				  })
					.done(function( data ) {
					  $.each( data, function( i, item ) {
						  if(item.RESULT == "0")
							  alert('������� : ����� �ٽ� �õ����ּ���');
						  js_search();
					  });
					});
				})();

			} else { 
				$("select[name='req_qty[]']").prop('selectedIndex', 0);
			}
		});
*/

		$("[name='req_qty[]']").keydown(function(e){

			if(e.keyCode==13) { 
				var elem = $(this);
				var order_goods_no = $(this).data("order_goods_no");
				var ori_qty = $(this).data("ori_qty");
				var req_qty = $(this).val();

				if(ori_qty < req_qty) { 
					alert('�۾� ������������ �ʰ� �� �� �����ϴ�.');
					elem.val(ori_qty);
					return;

				}

				if(confirm('�۾� ������ �����Ͻðڽ��ϱ�?')) { 
					(function() {
					  $.getJSON( "/manager/order/json_update_order_goods.php", {
						mode: "UPDATE_ORDER_GOODS_REQ_QTY",
						order_goods_no: order_goods_no,
						req_qty: req_qty
					  })
						.done(function( data ) {
						  $.each( data, function( i, item ) {
							  if(item.RESULT == "0")
								  alert('������� : ����� �ٽ� �õ����ּ���');
							  js_search();
							 // elem.val(req_qty);
						  });
						});
					})();

				} else { 
					elem.val(ori_qty);
				}
			}
		});

		$(".delivery_individual").each(function(){

			var elem = $(this);
			var order_goods_no = $(this).data("order_goods_no");

			$.getJSON( "/manager/order/json_work.php", {
				mode: "GET_DELIVERY_INDIVIDUAL",
				order_goods_no: order_goods_no
			  })
			.done(function( data ) {
			  $.each( data, function( i, item ) {

				  elem.find(".cnt_delivery_place").html(item.CNT_DELIVERY_PLACE);
				  elem.find(".total_sub_qty").html(item.TOTAL_GOODS_DELIVERY_QTY);
				
			  });
			});


		});

		$(".delivery_list").each(function(){

			var elem = $(this);
			var reserve_no = $(this).data("reserve_no");
			var order_goods_no = $(this).data("order_goods_no");
			var delivery_cp = $(this).data("delivery_cp");

			$.getJSON( "/manager/order/json_work.php", {
				mode: "GET_ORDER_GOODS_DELIVERY",
				reserve_no: reserve_no,
				order_goods_no: order_goods_no
			  })
			.done(function( data ) {
			  $.each( data, function( i, item ) {

				  if(data.length == 1 && delivery_cp.startsWith(item.SHORT_DELIVERY_CP)) { 
					elem.append('<a href="javascript:js_list_delivery_paper(\''+ item.RESERVE_NO + '\', \'' + item.ORDER_GOODS_NO + '\');" style="font-weight:bold;">' + item.CNT_YES + '�� </a><br/>');

				  } else 
					elem.append('<a href="javascript:js_list_delivery_paper(\''+ item.RESERVE_NO + '\', \'' + item.ORDER_GOODS_NO + '\');" style="font-weight:bold;">('+item.SHORT_DELIVERY_CP+')' + item.CNT_YES + '�� </a><br/>');
				
			  });
			});

		});
	});
</script>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>