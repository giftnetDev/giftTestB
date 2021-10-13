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
	$menu_right = "OD016"; // �޴����� ���� �� �־�� �մϴ�

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
	require "../../_classes/biz/stock/stock.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/confirm/confirm.php";
	require "../../_classes/biz/company/company.php";


#====================================================================
# Request Parameter
#====================================================================

	//	�������
	if ($mode == "I") {

		$result = listDeliveryIndividual($conn, $order_goods_no, "ASC");
		

		$arr_error = array();

		for($k = 0; $k < sizeof($result); $k ++) { 
			$error_msg_each = "";
			
			$individual_no  = $result[$k]["INDIVIDUAL_NO"];
			$work_seq		= $result[$k]["WORK_SEQ"];

			$DELIVERY_FEE_CODE = $DELIVERY_CP."-����";
			$DELIVERY_FEE = getDcodeName($conn, "DELIVERY_FEE", $DELIVERY_FEE_CODE); 

			//2016-11-24 �����°��� ���ü �̸��� ���� �� ���ü �ּҸ�, �ٸ��� �ּҿ��� ��ü�� ����
			//$CON_SEND_CP_ADDR = "��� �����ֽ� ������ ��縮 98���� �߱���Ʈ��";
			$arr_op_cp = getOperatingCompany($conn, $s_adm_com_code);

			$OP_CP_NM		= $arr_op_cp[0]["CP_NM"];
			$OP_CP_ADDR		= $arr_op_cp[0]["CP_ADDR"];

			if($SENDER_NM == $OP_CP_NM)
				$CON_SEND_CP_ADDR = $OP_CP_ADDR;
			else
				$CON_SEND_CP_ADDR = str_replace($OP_CP_NM, "", $OP_CP_ADDR);
			
			//�ù�ȸ�� ������ ������� �Ұ�
			if($DELIVERY_CP == "" || $DELIVERY_FEE == "")   
				$error_msg_each = "�ù�ȸ��/��� ����,";

			//�����ù��� �����ּҰ� �Էµ��� �ʾ����� ������� �Ұ�
			if($DELIVERY_TYPE == "3" && ($individual_no == null || $individual_no == ""))   
				$error_msg_each = "�����ù� ���ó ����,";

			//��������, ������, �ܺξ�ü�߼�, ��Ÿ�� ���� ���� ����
			if($DELIVERY_TYPE == "1" || $DELIVERY_TYPE == "2" || $DELIVERY_TYPE == "98" || $DELIVERY_TYPE == "99") 
				$error_msg_each = "�����ù�,�ù�� ��ǰ,";

			if($error_msg_each != "") { 
				array_push($arr_error, array('INDIVIDUAL_NO' => $individual_no, 'ERROR_MSG_EACH' => rtrim($error_msg_each, ",")));
				continue;

			}

			// �̹� ������ ������ ����
			if(countOrderDeliveryPaper($conn, $order_goods_no, $individual_no) <= 0)
			{

				$arr_order = selectOrder($conn, $reserve_no);

				$GOODS_DELIVERY_NAME = "";
				$SUB_QTY = "";
				$MEMO_ALL = "������� ��ǰ�Դϴ�-�ιڽ��� �ѼյǴ� ������ �����ּ���~";

				$CP_NO = SetStringFromDB($arr_order[0]["CP_NO"]);

				if($individual_no == "") { 
					$R_MEM_NM = $arr_order[0]["R_MEM_NM"];
					$R_PHONE  = $arr_order[0]["R_PHONE"];
					$R_HPHONE = $arr_order[0]["R_HPHONE"];
					$R_ADDR1  = $arr_order[0]["R_ADDR1"];
					$MEMO	  = $arr_order[0]["MEMO"];
				
				} else {
					$arr_individual = selectDeliveryIndividual($conn, $individual_no);
			
					$R_MEM_NM				= $arr_individual[0]["R_MEM_NM"];
					$R_PHONE				= $arr_individual[0]["R_PHONE"];
					$R_HPHONE				= $arr_individual[0]["R_HPHONE"];
					$R_ADDR1				= $arr_individual[0]["R_ADDR1"];
					$MEMO					= $arr_individual[0]["MEMO"];
					$INDIVIDUAL_DELIVERY_TYPE = $arr_individual[0]["DELIVERY_TYPE"];
					$USE_TF					= $arr_individual[0]["USE_TF"];

					//�����ϸ� �н�
					if($USE_TF != "Y")
						continue;

					//�ù谡 �ƴϹǷ� �н�
					if($INDIVIDUAL_DELIVERY_TYPE != "0")
						continue;

					//���� �Է� ������� ������� �⺻ ������� �Է�
					if($R_ADDR1 == "")							
						$R_ADDR1  = $arr_order[0]["R_ADDR1"];

					$GOODS_DELIVERY_NAME	= $arr_individual[0]["GOODS_DELIVERY_NAME"]; 
					$SUB_QTY				= $arr_individual[0]["SUB_QTY"];
				}
				//������ ���ݾ��ֱ� /*2016-02-25 �����*/
				$R_MEM_NM = str_replace(" ","", $R_MEM_NM); 
				
				if($MEMO == "")
					$MEMO = $MEMO_ALL;

				$arr_order_goods = selectOrderGoods($conn, $order_goods_no);
				if(sizeof($arr_order_goods) > 0) { 
					for($i=0; $i < sizeof($arr_order_goods); $i++) {

						$CP_ORDER_NO = $arr_order_goods[$i]["CP_ORDER_NO"];
						$GOODS_NAME  = $arr_order_goods[$i]["GOODS_NAME"];
						$WORK_SEQ	 = $arr_order_goods[$i]["WORK_SEQ"];
						$CATE_01	 = $arr_order_goods[$i]["CATE_01"];
						$DELIVERY_CNT_IN_BOX = $arr_order_goods[$i]["DELIVERY_CNT_IN_BOX"];
						
						if ($individual_no != "")
							$QTY = $SUB_QTY;
						else 
							$QTY = getRefundAbleQty($conn, $reserve_no, $order_goods_no); 

						//��ü ����ϰ��
						if($QTY == 0) 
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

						//echo $QTY." ".$total_paper_qty;

						for($j=0; $j < $total_paper_qty; $j++) { 
							
							$DELIVERY_CNT = $total_paper_qty;
							$SEQ_OF_DELIVERY = $j + 1;
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
							
							$order_goods_delivery_no = insertOrderDeliveryPaper($conn, $chk_work_date, $reserve_no, $order_goods_no, $individual_no, $CP_NO, $DELIVERY_CNT, $SEQ_OF_DELIVERY, $RECEIVER_NAME, $R_PHONE, $R_HPHONE, $R_ADDR1, $CON_ORDER_QTY, $MEMO, $SENDER_NM, $SENDER_PHONE, $SENDER_NM, $SENDER_PHONE, $CON_PAYMENT_TYPE, $CON_SEND_CP_ADDR, $GOODS_NAME, $DELIVERY_CP, $CON_DELIVERY_TYPE, $DELIVERY_FEE, $DELIVERY_FEE_CODE, $s_adm_no);

						}

					}
				}
			}

		}
		/*
		$total_error_msg = "";
		if(count($arr_error) > 0) { 
			foreach($arr_error as $row) {
				$total_error_msg .= $row["INDIVIDUAL_NO"];
				$total_error_msg .= $row["ERROR_MSG_EACH"];
				$total_error_msg .= "; ";
			}

		}
		*/
?>
<script>
		alert("������� �Ϸ� �Ǿ����ϴ�.");
		window.location.replace("/manager/order/pop_individual_delivery_list.php?reserve_no=<?=$reserve_no?>&order_goods_no=<?=$order_goods_no?>");
</script>
<?
		exit();
	}

	if ($mode == "DELIVERY_COMPLETE") {

		$err_msg = "";

		$row_cnt = count($chk_no);
		
		$arr_order = selectOrder($conn, $reserve_no);

		$CP_NO			 = $arr_order[0]["CP_NO"];

		$arr_order_goods = selectOrderGoods($conn, $order_goods_no);

		$REFUNDABLE_QTY = getRefundAbleQty($conn, "", $order_goods_no); 

		$GOODS_NO		 = $arr_order_goods[0]["GOODS_NO"];
		$GOODS_CODE		 = $arr_order_goods[0]["GOODS_CODE"];
		$GOODS_NAME		 = $arr_order_goods[0]["GOODS_NAME"];
		$BUY_PRICE       = $arr_order_goods[0]["BUY_PRICE"];
		$SALE_PRICE      = $arr_order_goods[0]["SALE_PRICE"];
		$OG_DELIVERY_TYPE= $arr_order_goods[0]["DELIVERY_TYPE"];
		$SALE_CONFIRM_TF = $arr_order_goods[0]["SALE_CONFIRM_TF"];

		$total_qty = 0;
		for ($k = 0; $k < $row_cnt; $k++) {
		
			$temp_individual_no = $chk_no[$k];
			
			$arr_individual = selectDeliveryIndividual($conn, $temp_individual_no);

			if(sizeof($arr_individual) > 0) { 

				$SUB_QTY		= $arr_individual[0]["SUB_QTY"];
				$R_MEM_NM		= $arr_individual[0]["R_MEM_NM"];
				$IS_DELIVERED	= $arr_individual[0]["IS_DELIVERED"];
				$DELIVERY_TYPE	= $arr_individual[0]["DELIVERY_TYPE"];
				$USE_TF			= $arr_individual[0]["USE_TF"];

				if($USE_TF != "Y") { 
					$err_msg .= "".$R_MEM_NM.", ";
					continue;
				}

				$DELIVERY_PAPER_QTY = countOrderDeliveryPaper($conn, $order_goods_no, $temp_individual_no);

				//�ù��϶� ������ ������ �н� - //2016-12-28 �ܺξ�ü �߼��� ��� ������ �ִ� ���� ����
				if($OG_DELIVERY_TYPE != "98")
					if($DELIVERY_TYPE == "0" && $DELIVERY_PAPER_QTY == "0") { 
						$err_msg .= "".$R_MEM_NM.", ";
						continue;
					}

				//���ΰ�ħ�� ����� �ߺ� �Ϸ� ����
				if($IS_DELIVERED == "Y") continue;

				//���� ��ۿϷ� ǥ��
				$result = completeDeliveryIndividual($conn, $temp_individual_no);

				//������ ���� ���� ������ ���� �ջ�
				$total_qty += $SUB_QTY;

				/*
				// â�� �۾�����Ʈ������ ��� ���� 2017-05-24

				//���� �ù踸 ���
				if($OG_DELIVERY_TYPE == "3") { 
					//��Ʈ��� �����԰��� ���� �״�� ���
					$stock_type     = "OUT";         //����� ���� (���) 
					$stock_code     = "NOUT01";      //��� �����ڵ� (�������)
					$in_cp_no		= "";	         // �԰� ��ü
					$out_cp_no	    = $CP_NO;		 // �����ü

					$goods_no		= $GOODS_NO;	 //�����ǰ ** ��Ʈ�� ��� �ش� ��Ʈ�� ��ǰ �� ��ŭ �� �� ó���ؾ� ��
					$in_loc			= "LOCA";        // â�� ���� ����Ʈ â�� A, Ŭ���� ������ B
					$in_loc_ext	    = "�����Ϸ� ���";
					$in_qty			= 0;
					$in_bqty		= 0;
					$in_fbqty		= 0;
					$out_qty		= $SUB_QTY;
					$out_bqty		= 0;
					$out_tqty	    = 0; 
					$in_price		= 0;
					$out_price	    = $BUY_PRICE;     
					$in_date		= "";
					$out_date		= date("Y-m-d H:i:s",strtotime("0 month"));
					$pay_date		= "";
					$close_tf		= "N";
					$memo           = "�������:".$R_MEM_NM."(".$temp_individual_no.")";

					$out_result = insertStock($conn, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $order_goods_no, $rgn_no, $close_tf, $s_adm_no, $memo);
				}
				*/
				
			}
		}

		//������ ���� ���� ����
		/*
		// 6�� 1�Ϻη� ���������� ��ü ���������Ƿ� �κб��� ���� 
		if($total_qty > 0 && $SALE_CONFIRM_TF != "Y") { 
			$inout_date = date("Y-m-d",strtotime("0 month"));
			$inout_type = "LR01"; //����
			$total_price = $total_qty * $SALE_PRICE;

			$TEMP_MEMO = "";
			$arr_memo = getMemoFromOrderGoods($conn, $order_goods_no);
			if(sizeof($arr_memo) > 0) { 
				$A = $arr_memo[0]["A"];
				$B = $arr_memo[0]["B"];
				$C = $arr_memo[0]["C"];
				$D = $arr_memo[0]["D"];

				$TEMP_MEMO .= $A;
				$TEMP_MEMO .= ($B != "" ? ($TEMP_MEMO != "" ? "/".$B : $B) : "");
				$TEMP_MEMO .= ($C != "" ? ($TEMP_MEMO != "" ? "/".$C : $C) : "");
				$TEMP_MEMO .= ($D != "" ? ($TEMP_MEMO != "" ? "/".$D : $D) : "");

			}

			insertCompanyLedger($conn, $CP_NO, $inout_date, $inout_type, $GOODS_NO, $GOODS_NAME."[".$GOODS_CODE."]", $total_qty, $SALE_PRICE, null, 0, $TEMP_MEMO, $reserve_no, $order_goods_no, "��������", null, $s_adm_no, null);
		}
		*/

	?>	
	<script language="javascript">
		
		alert('������ ������� ��ۿϷ� �Ǿ����ϴ�.');

		<?if($err_msg <> "") { ?>
			alert('�۾��� �Ϻ� ������ �ֽ��ϴ�. \n\n<?=rtrim($err_msg, ", ");?>');
		<? } ?>
		window.location.replace("/manager/order/pop_individual_delivery_list.php?reserve_no=<?=$reserve_no?>&order_goods_no=<?=$order_goods_no?>");
	</script>
	<?
		exit;
	}

	if ($mode == "DELIVERY_CANCEL") {

		$err_msg = "";

		$row_cnt = count($chk_no);

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$temp_individual_no = $chk_no[$k];
						
			$result = cancelDeliveryIndividual($conn, $temp_individual_no);
		
		}
		
	?>	
	<script language="javascript">
		
		alert('������ ������� ������ �Ǿ����ϴ�.');

		window.location.replace("/manager/order/pop_individual_delivery_list.php?reserve_no=<?=$reserve_no?>&order_goods_no=<?=$order_goods_no?>");
	</script>
	<?
		exit;
	}

	if ($mode == "USE") {

		$row_cnt = count($chk_no);
		//echo $row_cnt;

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$temp_individual_no = $chk_no[$k];
						
			$result = updateDeliveryIndividualUseTF($conn, $temp_individual_no, $use_tf);
		
		}

	?>	
	<script language="javascript">
		alert('�����׸��� ���� �Ǿ����ϴ�.');
		window.location.replace("/manager/order/pop_individual_delivery_list.php?reserve_no=<?=$reserve_no?>&order_goods_no=<?=$order_goods_no?>");
	</script>
	<?
		exit;
	}

	if ($mode == "D") {

		$row_cnt = count($chk_no);
		//echo $row_cnt;

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$temp_individual_no = $chk_no[$k];
						
			$result = deleteDeliveryIndividual($conn, $temp_individual_no);
		
		}

	?>	
	<script language="javascript">
		alert('�����׸��� ���� �Ǿ����ϴ�. ������� ������ ������ �������� �ʽ��ϴ�.');
		window.location.replace("/manager/order/pop_individual_delivery_list.php?reserve_no=<?=$reserve_no?>&order_goods_no=<?=$order_goods_no?>");
	</script>
	<?
		exit;
	}

	if ($mode == "FU") {
		#====================================================================
		$savedir1 = $g_physical_path."upload_data/temp_order";
		#====================================================================

		$file_nm	= upload($_FILES[file_nm], $savedir1, 10000 , array('xls','xlsx'));
		error_reporting(E_ALL ^ E_NOTICE);

		require_once "../../_PHPExcel/Classes/PHPExcel/IOFactory.php";
		$filename = '../../upload_data/temp_order/'.$file_nm; 

		try {
			$objReader = PHPExcel_IOFactory::createReaderForFile($filename);
			$objReader->setReadDataOnly(true);
			$objExcel = $objReader->load($filename);
			$objExcel->setActiveSheetIndex(0);
			$objWorksheet = $objExcel->getActiveSheet();
			$rowIterator = $objWorksheet->getRowIterator();

			foreach ($rowIterator as $row) {
				$cellIterator = $row->getCellIterator();
				$cellIterator->setIterateOnlyExistingCells(false); 
			}

			$maxRow = $objWorksheet->getHighestRow();
			print_r($objWorksheet);
			// echo iconv("UTF-8","EUC-KR",$objWorksheet->getCell("D2")->getValue())."<br>";
			// echo iconv("UTF-8","EUC-KR",$objWorksheet->getCell("D3")->getValue())."<br>";
			// echo iconv("UTF-8","EUC-KR",$objWorksheet->getCell("D4")->getValue())."<br>";
			// echo iconv("UTF-8","EUC-KR",$objWorksheet->getCell("D5")->getValue())."<br>";
			// echo iconv("UTF-8","EUC-KR",$objWorksheet->getCell("D6")->getValue())."<br>";

			//��� ��
			// $i = 1;
			
			// $k = 1;
			// while(iconv("UTF-8","EUC-KR",$objWorksheet->getCell(getNameFromNumber($k).$i)->getValue()) != "") {
			// 		switch(trim(iconv("UTF-8","EUC-KR",$objWorksheet->getCell(getNameFromNumber($k).$i)->getValue()))) {
			// 				case "�ۼ�����" : 	 $COL_WRITTEN_DATE = getNameFromNumber($k); break;
			// 				case "���ι�ȣ" : 	 $COL_CF_CODE = getNameFromNumber($k);      break;
			// 				case "�߱�����" : 	 $COL_OUT_DATE = getNameFromNumber($k);     break;
			// 				case "�����ڻ���ڵ�Ϲ�ȣ" :  	 $COL_BIZ_NO1 = getNameFromNumber($k);       break;
			// 				case "���޹޴��ڻ���ڵ�Ϲ�ȣ" :  	 $COL_BIZ_NO2 = getNameFromNumber($k);       break;
			// 				case "��ȣ" : 	 if($COL_CP_NM1 == "")
			// 														$COL_CP_NM1 = getNameFromNumber($k);    
			// 														else 
			// 														$COL_CP_NM2 = getNameFromNumber($k);    
			// 						break;
			// 				case "�հ�ݾ�" : 	 $COL_TOTAL_PRICE = getNameFromNumber($k);  break;
			// 				case "���ް���" : 	 $COL_SUPPLY_PRICE = getNameFromNumber($k); break;
			// 				case "����" : 	 $COL_SURTAX = getNameFromNumber($k);       break;
			// 				case "ǰ���" : 	 $COL_GOODS_NM = getNameFromNumber($k);     break;
			// 		}
			// 		$k += 1;
			// }

			// for ($i = 7 ; $i <= $maxRow ; $i++) {
			// 		$written_date     = iconv("UTF-8","EUC-KR",$objWorksheet->getCell($COL_WRITTEN_DATE.$i)->getValue());
			// 		$cf_code	      = iconv("UTF-8","EUC-KR",$objWorksheet->getCell($COL_CF_CODE.$i)->getValue());
			// 		$out_date         = iconv("UTF-8","EUC-KR",$objWorksheet->getCell($COL_OUT_DATE.$i)->getValue());
			// 		$biz_no2		  = iconv("UTF-8","EUC-KR",$objWorksheet->getCell($COL_BIZ_NO2.$i)->getValue());
			// 		$cp_nm1			  = iconv("UTF-8","EUC-KR",$objWorksheet->getCell($COL_CP_NM1.$i)->getValue());
			// 		$cp_nm2			  = iconv("UTF-8","EUC-KR",$objWorksheet->getCell($COL_CP_NM2.$i)->getValue());
			// 		$total_price	  = iconv("UTF-8","EUC-KR",$objWorksheet->getCell($COL_TOTAL_PRICE.$i)->getValue());
			// 		$supply_price	  = iconv("UTF-8","EUC-KR",$objWorksheet->getCell($COL_SUPPLY_PRICE.$i)->getValue());
			// 		if($COL_SURTAX != "")
			// 				$surtax			  = iconv("UTF-8","EUC-KR",$objWorksheet->getCell($COL_SURTAX.$i)->getValue());
			// 		else
			// 				$surtax			  = 0;
			// 		$goods_nm		  = iconv("UTF-8","EUC-KR",$objWorksheet->getCell($COL_GOODS_NM.$i)->getValue());
			// 		$biz_no1		  = iconv("UTF-8","EUC-KR",$objWorksheet->getCell($COL_BIZ_NO1.$i)->getValue());

			// 		$total_price = str_replace(",","", $total_price);
			// 		$supply_price = str_replace(",","", $supply_price);
			// 		$surtax = str_replace(",","", $surtax);

			// 		insertTempCashStatement($conn, $temp_no, $written_date, $cf_code, $out_date, $biz_no1, $cp_nm1, $biz_no2, $cp_nm2, $total_price, $supply_price, $surtax, $goods_nm, $s_adm_no);
			// }
		} catch (exception $e) {
			echo '���������� �дµ��� ������ �߻��Ͽ����ϴ�.'.$e;
		}







































		#====================================================================
			$savedir1 = $g_physical_path."upload_data/temp_delivery";
		#====================================================================
			$file_nm	= upload($_FILES[file_nm], $savedir1, 10000 , array('xls'));

			require_once '../../_excel_reader/Excel/reader.php';
			$data = new Spreadsheet_Excel_Reader();
			$data->setOutputEncoding('euc-kr');
			//$data->read('test.xls');
			$data->read("../../upload_data/temp_delivery/".$file_nm);
		
			error_reporting(E_ALL ^ E_NOTICE);

			$err_msg = "";

			for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
				$TEMP_R_MEM_NM				= SetStringToDB(trim($data->sheets[0]['cells'][$i][1]));
				$TEMP_R_PHONE				= SetStringToDB(trim($data->sheets[0]['cells'][$i][2]));
				$TEMP_R_HPHONE				= SetStringToDB(trim($data->sheets[0]['cells'][$i][3]));
				$TEMP_R_ZIPCODE				= ""; //�����Ѵ� �Ͻ�
				$TEMP_R_ADDR1				= SetStringToDB(trim($data->sheets[0]['cells'][$i][4]));
				$TEMP_GOODS_DELIVERY_NAME	= SetStringToDB(trim($data->sheets[0]['cells'][$i][5]));
				$TEMP_SUB_QTY				= SetStringToDB(trim($data->sheets[0]['cells'][$i][6]));
				$TEMP_OPT_MEMO				= SetStringToDB(trim($data->sheets[0]['cells'][$i][7]));
				$TEMP_DELIVERY_TYPE			= SetStringToDB(trim($data->sheets[0]['cells'][$i][8]));
				
				// echo $TEMP_R_MEM_NM." ".$TEMP_R_PHONE." ".$TEMP_R_HPHONE." ".$TEMP_R_ADDR1." ".$TEMP_GOODS_DELIVERY_NAME." ".$TEMP_SUB_QTY." ".$TEMP_OPT_MEMO."<br/>";

				$TEMP_R_ADDR1 = str_replace("\"", "'", $TEMP_R_ADDR1);
				$TEMP_OPT_MEMO = str_replace("\"", "'", $TEMP_OPT_MEMO);


				//0 �̻� ���ڰ� �ƴϸ� �н�
				if (filter_var($TEMP_SUB_QTY, FILTER_VALIDATE_INT, array('options' => array('min_range' => 0))) === FALSE) {
					$err_msg .= $i.", ";
					continue;
				}

				$TEMP_DELIVERY_TYPE = getDcodeCode($conn, 'DELIVERY_TYPE', $TEMP_DELIVERY_TYPE);

				// ���� ������� �ù�
				// �ֹ� ��ü�� �ܺξ�ü �߼��� ��� ���� ������ �ܺξ�ü �߼�����, �ù� ���� ���� ������ ������ �ܺ� ���� 2017-04-11
				if($hid_delivery_type == "98")
					$TEMP_DELIVERY_TYPE = "98";
				else
					if($TEMP_DELIVERY_TYPE == "")  
						$TEMP_DELIVERY_TYPE = "0";

				if($TEMP_R_MEM_NM <> "" && $TEMP_SUB_QTY <> "")
					insertDeliveryIndividual($conn, $file_nm, $order_goods_no, $TEMP_R_MEM_NM, $TEMP_R_PHONE, $TEMP_R_HPHONE, $TEMP_R_ZIPCODE, $TEMP_R_ADDR1, $TEMP_GOODS_DELIVERY_NAME, $TEMP_SUB_QTY, $TEMP_OPT_MEMO, $TEMP_DELIVERY_TYPE, $s_adm_no);
				else
					$err_msg .= $i.", ";
			}

			if($err_msg <> "") { 
				$err_msg = rtrim($err_msg, ", ");
				$err_msg = "�Է� ���� ������ �ش� ������ (".$err_msg.") ��° ���� �Է¿� ������ Ȥ�� ������ �߸��Ǿ����ϴ�."; 
?>	
<script language="javascript">
		alert('<?=$err_msg?>');
		// window.location.replace("/manager/order/pop_individual_delivery_list.php?reserve_no=<?=$reserve_no?>&order_goods_no=<?=$order_goods_no?>");
</script>
<?
			} else {
?>	
<script language="javascript">
		alert('�����ù� �Է¿Ϸ� �Ǿ����ϴ�.');
		// window.location.replace("/manager/order/pop_individual_delivery_list.php?reserve_no=<?=$reserve_no?>&order_goods_no=<?=$order_goods_no?>");
</script>
<?
			}
		exit;
	}

	$arr_order_goods = selectOrderGoods($conn, $order_goods_no);

	$DELIVERY_TYPE   = $arr_order_goods[0]["DELIVERY_TYPE"];
	$DELIVERY_CP     = $arr_order_goods[0]["DELIVERY_CP"];
	$SENDER_NM       = $arr_order_goods[0]["SENDER_NM"];
	$SENDER_PHONE    = $arr_order_goods[0]["SENDER_PHONE"];
	$WORK_START_DATE = $arr_order_goods[0]["WORK_START_DATE"];
	$WORK_END_DATE	 = $arr_order_goods[0]["WORK_END_DATE"];
	$ORDER_STATE	 = $arr_order_goods[0]["ORDER_STATE"];
	$QTY			 = $arr_order_goods[0]["QTY"];

	$arr_rs = listDeliveryIndividual($conn, $order_goods_no, "DESC");

	$SUM_QTY = 0;

	$UNDELIVERED_PLACE_CNT = 0;
	$UNDELIVERED_DELIVERY_CNT = 0;
	$DELIVERED_PLACE_CNT = 0;
	$DELIVERED_DELIVERY_CNT = 0;

	$place_cnt = 0;
	if(sizeof($arr_rs) >= 1) {
		for($i = 0; $i < sizeof($arr_rs); $i ++) { 

			$SUB_QTY				= trim($arr_rs[$i]["SUB_QTY"]);
			$IS_DELIVERED			= trim($arr_rs[$i]["IS_DELIVERED"]);
			$USE_TF					= trim($arr_rs[$i]["USE_TF"]);

			if($USE_TF != "Y") continue;

			$place_cnt += 1;

			if($IS_DELIVERED != "Y") { 
				$UNDELIVERED_PLACE_CNT ++;
				$UNDELIVERED_DELIVERY_CNT += $SUB_QTY;
			} else { 
				$DELIVERED_PLACE_CNT ++;
				$DELIVERED_DELIVERY_CNT += $SUB_QTY;
			}
			
			$SUM_QTY += $SUB_QTY;
		}
	}

	$REFUNDABLE_QTY = getRefundAbleQty($conn, "", $order_goods_no); 
	$REALDELIVERY_QTY = getRealDeliveryQty($conn, "", $order_goods_no); 


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
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
		    buttonImage: "/manager/images/calendar/cal.gif",
		    buttonImageOnly: true,
		    buttonText: "Select date",
			showOn: "both",
			dateFormat: "yy-mm-dd",
			numberOfMonths: 2,
			changeMonth: true,
			changeYear: true
		});
	});
</script>
<script>
	$(function() {
		$("#tabs").tabs();
	});
</script>
<script language="javascript">

	function js_create_delivery_paper()
	{
		var frm = document.frm;

		if($("select[name=DELIVERY_TYPE]").val() == "" || $("select[name=DELIVERY_CP]").val() == "")
		{
			alert('�ϰ� �Է��� ��۹�İ� �ù�ȸ�縦 �������ּ���.');
			return;
		}

		frm.mode.value = "I";
		frm.target = "";
		frm.method = "post";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	function js_individual_delivery(btn) {

		btn.style.visibility = 'hidden';
		
		var frm = document.frm;

		if (isNull(frm.file_nm.value)) {
			alert('������ ������ �ּ���.');
			btn.style.visibility = 'visible';
			frm.file_nm.focus();
			return ;		
		}
		
		if (!AllowAttach(frm.file_nm))
		{
			btn.style.visibility = 'visible';
			frm.file_nm.focus();
			return ;
		}

		frm.mode.value = "FU";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function AllowAttach(obj) {
		var file = obj.value;
		extArray = new Array(".xls");
		allowSubmit = false;
		
		if (!file){
			//form1.submit();
		}

		while (file.indexOf("\\") != -1){
			file = file.slice(file.indexOf("\\") + 1);
			ext = file.slice(file.lastIndexOf(".")).toLowerCase();

			for (var i = 0; i < extArray.length; i++){
				if (extArray[i] == ext){ 
					allowSubmit = true; 
					break; 
				}
			}
		}

		if (allowSubmit){
			return true;
		}else{
			alert("�Է��Ͻ� ������ ���ε� �� �� �����ϴ�!");
			return false;
		}
	}

	function js_list_delivery_paper(reserve_no, order_goods_no, individual_no) {

		var url = "pop_delivery_paper_list.php?reserve_no=" + reserve_no + "&order_goods_no=" + order_goods_no + "&individual_no=" + individual_no;

		NewWindow(url, 'pop_delivery_paper_list','1000','500','YES');
		
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

	function js_delete() {

		var frm = document.frm;
		
		if (confirm("��ۿϷ� �� ������� �����ؾ� �Ͻ� ��쿡�� ����� ������ ���� �������ּ���. (�ʼ�üũ : '������� > �ŷ�ó ����' ����) \n\n ��� �����Ͻðڽ��ϱ�?")) {
			frm.mode.value = "D";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}

	}

	function js_done(use_tf) {

		var frm = document.frm;
		
		frm.use_tf.value = use_tf;

		if(use_tf == "Y") { 
			frm.mode.value = "DELIVERY_COMPLETE";
		} else
			frm.mode.value = "DELIVERY_CANCEL";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	function js_use(use_tf) { 

		var frm = document.frm;
		
		frm.use_tf.value = use_tf;
		frm.mode.value = "USE";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_pop_delivery_confirmation(reserve_no, individual_no, print_type) {

		var frm = document.frm;
		
		print_date = frm.print_date.value;
	
		NewDownloadWindow("pop_delivery_confirmation2.php", {reserve_no : reserve_no, individual_no: individual_no, print_type :  print_type, print_date : print_date});

	}

	function js_excel() {

		var frm = document.frm;
		
		frm.target = "";
		frm.action = "<?=str_replace("list","excel_list",$_SERVER[PHP_SELF])?>";
		frm.submit();

	}
</script>
<style>
	input[type=text].list {display:none; width:90%;} 
</style>
<script>
	$(function(){
		
		$(".data_list td, th").on("click", function(){

			//if($(this).closest("tr").prop("class") == "delivered") { 
			//	alert("���ó�� �� ������ ������ �� �����ϴ�.");
			//	return;
			//}

			//var source = $(this).prop("title");
			var text_box = $(this).find("input[type=text].list");

			$(this).find("span").hide();
			if($(this).prop("title") != $(this).find("span").html()) { 
				
				var target_text = $(this).find("span").html();
				target_text = target_text.replaceall("&amp;","&");
				target_text = target_text.replaceall("&quot;",'"');
				target_text = target_text.replaceall("&lt;","<");
				target_text = target_text.replaceall("&gt;",">");

				text_box.val(target_text).show().focus();
			} else
				text_box.val($(this).prop("title")).show().focus();

		});

		$("input[type=text].list").on("keydown", function(){
			$(this).parent().find("span").html($(this).val());

			var target_text = $(this).val();
			target_text = target_text.replaceall("&amp;","&");
			target_text = target_text.replaceall("&quot;",'"');
			target_text = target_text.replaceall("&lt;","<");
			target_text = target_text.replaceall("&gt;",">");

			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13'){

				var individual_no = $(this).closest("tr").data("individual_no");
				var column = $(this).data("column");
				var value = target_text;

				(function() {
				  $.getJSON( "/manager/order/json_update_order_goods.php", {
					mode: "UPDATE_INDIVIDUAL_DELIVERY",
					individual_no: individual_no,
					column: column,
					value : value
				  })
					.done(function( data ) {
					  $.each( data, function( i, item ) {
						  if(item.RESULT == "0")
							  alert('������� : ����� �ٽ� �õ����ּ���');
					  });
					});
				})();


				$("span").show();
				$("input[type=text].list").hide();
			}
		});
		

		$("select[name=delivery_type]").change(function(){

			var individual_no = $(this).closest("tr").data("individual_no");
			var value = $(this).val();

			(function() {
			  $.getJSON( "/manager/order/json_update_order_goods.php", {
				mode: "UPDATE_INDIVIDUAL_DELIVERY",
				individual_no: individual_no,
				column: 'DELIVERY_TYPE',
				value : value
			  })
				.done(function( data ) {
				  $.each( data, function( i, item ) {
					  if(item.RESULT == "0")
						  alert('������� : ����� �ٽ� �õ����ּ���');
					  else
						  location.href = "<?=$_SERVER[PHP_SELF]?>?reserve_no=<?=$reserve_no?>&order_goods_no=<?=$order_goods_no?>" ;
				  });
				});
			})();
		});

		$("input[type=text].list").on("blur", function(){
			$("span").show();
			$("input[type=text].list").hide();
		});
	});
</script>
</head>
<style>
body#popup_order_wide {width:100%;}
.delivered {background-color:#EFEFEF;}
tr.not_used > td {color: #A2A2A2;}
.color_yellow{background-color:#fcfc3f !important;}
</style>
<body id="popup_order_wide">

<div id="popupwrap_order_wide">
	<h1>���� ����� �Է�</h1>
	<div id="postsch_code">

		<div class="addr_inp">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="">
<input type="hidden" name="reserve_no" value="<?=$reserve_no?>">
<input type="hidden" name="order_goods_no" value="<?=$order_goods_no?>">
<input type="hidden" name="hid_delivery_type" value="<?=$DELIVERY_TYPE?>"/>
<input type="hidden" name="use_tf" value=""/>
	
	<? if($ORDER_STATE != "3") { ?>
	<div id="tabs" style="width:95%; margin:10px 0;">
		<ul>
			<li><a href="#tabs-1">��������� �Է�</a></li>
			<li><a href="#tabs-2">���� ����</a></li>
		</ul>
		<div id="tabs-1">
			<table cellpadding="0" cellspacing="0" width="100%" class="colstable02">
			<colgroup>
				<col width="10%" />
				<col width="45%" />
				<col width="*" />
			</colgroup>
			<tr>
				<? if($REALDELIVERY_QTY > $SUM_QTY) { ?>
				<th>��������� ���� �Է�<br/><br/><a href="/manager/order/input_example_individual.xls">�Է����� �ޱ�</a></th>
				<td class="line">
					<input type="file" name="file_nm" style="width:60%;" class="txt">
					<a href="#" onclick="js_individual_delivery(this); return false;"><img src="../images/admin/btn_confirm.gif" alt="Ȯ��"></a>
				</td>
				<? } else { ?>
				<td colspan="2" class="line"></td>
				<? } ?>
				<td class="line">
				<b>������ �������</b><br/><br/>
				<input type="button" name="bb" value=" ��ۿϷ� " style="padding:3px;" onclick="js_done('Y')"/>  
				<input type="button" name="bb" value=" ������ " style="padding:3px;" onclick="js_done('N')"/> &nbsp;&nbsp;
				
				<input type="button" name="bb" value=" ����� " style="padding:3px;" onclick="js_use('Y');"/> 
				<input type="button" name="bb" value=" ������ " style="padding:3px;" onclick="js_use('N');"/> &nbsp;&nbsp;

				<input type="button" name="bb" value=" ���� " style="padding:3px;" onclick="js_delete()"/></td>
			</tr>
			</table>
		</div>
		<div id="tabs-2">
			<table cellpadding="0" cellspacing="0" width="100%" class="colstable02">
				<colgroup>
					<col width="12%" />
					<col width="33%" />
					<col width="12%" />
					<col width="33%" />
					<col width="*" />
				</colgroup>
				<tbody>
					<tr>
						<th>���������</th>
						<td class="line" colspan="3">
							<?
								$chk_work_date = date("Y-m-d",strtotime("1 day"));
							?>
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="chk_work_date" value="<?=$chk_work_date?>" maxlength="10"/>
						</td>
					</tr>
					<tr>
						<th>�������</th>
						<td class="line"><?=makeSelectBoxOnChange($conn,"DELIVERY_TYPE", "DELIVERY_TYPE","90", "������� ����", "", $DELIVERY_TYPE)?></td>
						<th>�ù�ȸ��</th>
						<td class="line"><?=makeSelectBoxOnChange($conn,"DELIVERY_CP", "DELIVERY_CP","90", "�ù�ȸ�缱��", "", $DELIVERY_CP)?>
						
						<td class="line" rowspan="3">
							<? if(($DELIVERY_TYPE == "0" || $DELIVERY_TYPE == "3") && $WORK_START_DATE != "0000-00-00 00:00:00") { ?>
								<input type="button" name="b" value="���� ����" class="btntxt" onclick="js_create_delivery_paper();">
							<? } else { ?>
								<span style="color:red; font-weight:bold;">�۾�����<br/> �������</span>
							<? } ?>
						</td>
					</tr>
					<tr>
						<th>�����»��</th>
						<td class="line"><input type="text" name="SENDER_NM" value="<?=$SENDER_NM?>" /></td>
						<th>������ ��ȣ</th>
						<td class="line"><input type="text" name="SENDER_PHONE" value="<?=$SENDER_PHONE?>" />
					</tr>
				</tbody>
				</table> 
		</div>
	</div>
	<? } ?>
	<div class="sp10"></div>
	<table cellpadding="0" cellspacing="0" width="95%" style="font-weight:bold;">
		<colgroup>
			<col width="50%" />
			<col width="50%" />
		</colgroup>
		<tr height="30">
			<td>
				<span style="color:green;">�Ϸ�� ����� �� <?=number_format($DELIVERED_PLACE_CNT)?> ��, <?=number_format($DELIVERED_DELIVERY_CNT)?> ��</span>
			</td>
			<td style="text-align:right;">
				<span>�ֹ� ���� <?=number_format($REFUNDABLE_QTY)?>��, �� <?=number_format($SUM_QTY)?>�� ��ǰ, <?=number_format($place_cnt)?>���� ����� ������</span>
				<?
					if($REALDELIVERY_QTY <> $QTY)
						echo "<br/><span style='color:#A2A2A2;'>(�� �ֹ����� : ".number_format($QTY)."��)</span>"
				?>
				<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="���� ����Ʈ" /></a>
			</td>
		</tr>
		<tr>
			<td>
				<span style="color:red;">�Ϸ��� ����� �� <?=number_format($UNDELIVERED_PLACE_CNT)?> ��, <?=number_format($UNDELIVERED_DELIVERY_CNT)?> ��</span>
			</td>
			<td style="text-align:right;">
				<?
					if($ORDER_STATE != "3") { 
				?>
					<? if($REFUNDABLE_QTY < $SUM_QTY) { ?>
						 <span style="color:red;"><?=number_format($SUM_QTY-$REFUNDABLE_QTY)?>�� �ʰ��Ǿ����ϴ�. ����Ȯ�����ּ���.</span>
					<? } else if($REFUNDABLE_QTY == $SUM_QTY) {?>
						 <span style="color:green;">�ֹ� ������ŭ ��Ȯ�� �ԷµǾ����ϴ�.</span>
					<? } else {?>
						 <span style="color:blue;"><?=number_format($REFUNDABLE_QTY - $SUM_QTY) ?>���� ���� ������� �������� �ʾҽ��ϴ�.</span>
					<? } ?>
				<? } else { ?>
					<span style="color:gray;">��ۿϷ� �Ǿ����ϴ�.</span>
				<? } ?>
			</td>
		</tr>
	</table>
	<div class="sp10"></div>
	* ���� ���� �ϰ� ���� ��� "SHIFT" Ű�� ���� ���·� ���۰� ���� �������ּ���.
	<?
		if($search_str <> "") {
			echo "<b>(�˻��� : ".$search_str.")</b>";
		}
	?>
	<table cellpadding="0" cellspacing="0" width="90%" class="rowstable data_list">
	<colgroup>
		<col width="5%" />
		<col width="8%" />
		<col width="9%" />
		<col width="9%" />
		<col width="15%" />
		<col width="10%" />
		<col width="*" />
		<col width="6%" />
		<col width="6%" />
		<col width="10%" />
		<col width="11%" />
	</colgroup>
	<thead>
		<tr>
			<th><input type="checkbox" name="all_chk" onClick="js_all_check();" class="display_none"></th>
			<th>������</th>
			<th>����ó</th>
			<th>�޴�����ȣ</th>
			<th>�ּ�</th>
			<th>��۸޸�</th>
			<th>�����ǰ��</th>
			<th>��ǰ����</th>
			<th>�������</th>
			<th>�����<br/>(����Ͻ�)</th>
			<th class="end">��ǰ���ڰ�
			<select name="print_date">
				<option value="<?=date("Y-m-d",strtotime("0 day"))?>">����</option>
				<option value="<?=date("Y-m-d",strtotime("1 day"))?>">����</option>
			</select>
			��
			</th>
		</tr>
	</thead>
	<tbody>
	
	<?
	if(sizeof($arr_rs) >= 1) {
		for($i = 0; $i < sizeof($arr_rs); $i ++) { 

		$INDIVIDUAL_NO			= trim($arr_rs[$i]["INDIVIDUAL_NO"]);
		$R_ZIPCODE			    = trim($arr_rs[$i]["R_ZIPCODE"]); 
		$R_ADDR1 				= trim($arr_rs[$i]["R_ADDR1"]);
		$R_MEM_NM				= trim($arr_rs[$i]["R_MEM_NM"]);
		$R_PHONE				= trim($arr_rs[$i]["R_PHONE"]); 
		$R_HPHONE				= trim($arr_rs[$i]["R_HPHONE"]); 
		$GOODS_DELIVERY_NAME	= trim($arr_rs[$i]["GOODS_DELIVERY_NAME"]); 
		$SUB_QTY				= trim($arr_rs[$i]["SUB_QTY"]);
		$MEMO					= trim($arr_rs[$i]["MEMO"]);
		$DELIVERY_TYPE			= trim($arr_rs[$i]["DELIVERY_TYPE"]);
		$IS_DELIVERED			= trim($arr_rs[$i]["IS_DELIVERED"]);
		$USE_TF					= trim($arr_rs[$i]["USE_TF"]);

		$REG_DATE				= date("n��j��H��i��", strtotime(trim($arr_rs[$i]["REG_DATE"])));

		$REG_ADM				= trim($arr_rs[$i]["REG_ADM"]);
		$REG_ADM = getAdminName($conn, $REG_ADM);

		$DELIVERY_PAPER_QTY = countOrderDeliveryPaper($conn, $order_goods_no, $INDIVIDUAL_NO);

		if($IS_DELIVERED == "Y") { 
			$str_tr_class = "delivered";
			$DELIVERY_DATE = date("n��j��H��i��", strtotime(trim($arr_rs[$i]["DELIVERY_DATE"])));

		} else { 
			$str_tr_class = "";
			$DELIVERY_DATE = "�����";
		}

		if($USE_TF == "N")
			$str_tr_use_class = "not_used";
		else
			$str_tr_use_class = "";


		$TXT_R_ADDR1 = $R_ADDR1;
		$TXT_R_MEM_NM = $R_MEM_NM;
		$TXT_GOODS_DELIVERY_NAME = htmlspecialchars($GOODS_DELIVERY_NAME);

		//�ֹ�, ��۸���Ʈ�� Ű���� �˻�� ������ �ö�� ���
		$td_class1 = "";
		$td_class2 = "";
		$td_class3 = "";
		if($search_str <> "") {

			if(strpos($TXT_R_ADDR1, $search_str) !== false) { $td_class1 = "color_yellow"; } 
			if(strpos($TXT_R_MEM_NM, $search_str) !== false) {  $td_class2 = "color_yellow"; } 
			if(strpos($TXT_GOODS_DELIVERY_NAME, $search_str) !== false) {  $td_class3 = "color_yellow"; } 
		
		}
	?>

		<tr height="35" class="<?=$str_tr_class?> <?=$str_tr_use_class?>" title="��ۿϷ�ð�: <?=$DELIVERY_DATE?>" data-individual_no="<?=$INDIVIDUAL_NO?>" >
			<td><input type="checkbox" name="chk_no[]" class="chk" value="<?=$INDIVIDUAL_NO?>"><br/><?=$INDIVIDUAL_NO?></td>
			<td class="<?=$td_class2?>" title="<?=$R_MEM_NM?>"><span><?=$TXT_R_MEM_NM?></span><input type="text" class="list" data-column="r_mem_nm" value=""/></td>
			<td title="<?=$R_PHONE?>"><span><?=$R_PHONE?></span><input type="text" class="list"  data-column="r_phone" value=""/></td>
			<td title="<?=$R_HPHONE?>"><span><?=$R_HPHONE?></span><input type="text" class="list"  data-column="r_hphone" value=""/></td>
			<td class="<?=$td_class1?>" title="<?=$R_ADDR1?>"><span><?=$TXT_R_ADDR1?></span><input type="text" class="list"  data-column="r_addr1" value=""/></td>
			<td title="<?=$MEMO?>"><span><?=$MEMO?></span><input type="text" class="list"  data-column="memo" value=""/></td>
			<td class="<?=$td_class3?>" title="<?=$GOODS_DELIVERY_NAME?>"><span><?=$TXT_GOODS_DELIVERY_NAME?></span><input type="text" class="list"  data-column="goods_delivery_name" value=""/></td>
			<td title="<?=$SUB_QTY?>"><span><?=$SUB_QTY?></span><input type="text" class="list"  data-column="sub_qty" value=""/></td>
			<td style="font-weight:bold; <?if($DELIVERY_TYPE == "0" && $DELIVERY_PAPER_QTY == "0") echo "color:red;";?>">
				<?=$DELIVERY_PAPER_QTY?>
			</td>
			<td>
				<?=$REG_ADM."<br/>(".$REG_DATE.")"?>
			</td>
			<td>
				<? if($ORDER_STATE != "3") { ?>
					<?=makeSelectBox($conn,"DELIVERY_TYPE", "delivery_type","95", "��۹��", "", $DELIVERY_TYPE, "")?>
					
					<? if($DELIVERY_TYPE == "0") { ?>
					<input type="button" name="b" value="�������/��ȸ" class="btntxt" onclick="js_list_delivery_paper('<?=$reserve_no?>', '<?=$order_goods_no?>','<?=$INDIVIDUAL_NO?>');" style="margin-bottom:3px;"><br/>
					<? } else if($DELIVERY_TYPE == "1") { // ��������?>
					<input type="button" name="b" value="�μ���" class="btntxt" onclick="js_pop_delivery_confirmation('<?=$reserve_no?>','<?=$INDIVIDUAL_NO?>', '<?=$DELIVERY_TYPE?>');">
					<? } else if($DELIVERY_TYPE == "2") { // ������?>
					<input type="button" name="b" value="��ǰȮ�μ�" class="btntxt" onclick="js_pop_delivery_confirmation('<?=$reserve_no?>','<?=$INDIVIDUAL_NO?>', '<?=$DELIVERY_TYPE?>');">
					<? } ?>
				<? } ?>
				
				</td>
		</tr>
	
	<?
		}
	} else {

	?>
		<tr>
			<td colspan="11" height="50" align="center">�����Ͱ� �����ϴ�</td>
		</tr>
	<?

	}
	
	?>
	
	</tbody>
	</table>
	

	<script>

		$(function(){

			//��ü �ε��� Ŭ�� ����
			$("input[name=all_chk]").show();

		});

		var last_click_idx = -1;
		$(".chk").click(function(event){
			
			var clicked_elem = $(this);
			var clicked_elem_chked = $(this).prop("checked");

			var start_idx = -1;
			var end_idx = -1;
			var click_idx = -1;

			$(".chk").each(function( index, elem ) {

				//Ŭ����ġ ����
				if(clicked_elem.val() == $(elem).val())
					click_idx = index;

			});

			if(event.shiftKey) {

				if($(".chk:checked").size() >= 2) {
					$(".chk").each(function( index, elem ) {

						//üũ�� ���� ���� üũ
						if(start_idx == -1 && $(elem).prop("checked"))
							start_idx = index;

						//üũ�� ������ �ε��� üũ
						if($(elem).prop("checked"))
							end_idx = index;

					});

					if($(".chk:checked").size() > 2 && last_click_idx > click_idx)
						start_idx = click_idx;

					if($(".chk:checked").size() > 2 && last_click_idx < click_idx)
						end_idx = click_idx;


					//alert("start_idx: " + start_idx + ", end_idx: " + end_idx + ", click_idx: " + click_idx+ ", last_click_idx: " + last_click_idx);

					
					$(".chk").each(function(index, elem) {

						if(start_idx <= index && index <= end_idx) {
							$(elem).prop("checked", true);
						}
						else
							$(elem).prop("checked", false);
						
					});
					
				}

				last_click_idx = click_idx;
			}

		});

		
	
	</script>


<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
<div class="bot_close" style="width:auto;"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="�ݱ�" /></a></div>
</div>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>