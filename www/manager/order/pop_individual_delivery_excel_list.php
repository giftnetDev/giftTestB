<?
ini_set('memory_limit',-1);
session_start();
?>
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


	$arr_rs = listDeliveryIndividual($conn, $order_goods_no, "DESC");


	require_once "../../_PHPExcel/Classes/PHPExcel.php";

	$objPHPExcel = new PHPExcel();

 	$BStyle = array(
	  'borders' => array(
		'allborders' => array(
		  'style' => PHPExcel_Style_Border::BORDER_THIN
		)
	  )
	);

	// Rename sheet
	$objPHPExcel->getActiveSheet(0)->setTitle(iconv("EUC-KR", "UTF-8","�����ּ���"));

	//�����ּ��� ���
	$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A1", iconv("EUC-KR", "UTF-8","������"))
					->setCellValue("B1", iconv("EUC-KR", "UTF-8","����ó"))
					->setCellValue("C1", iconv("EUC-KR", "UTF-8","�޴�����ȣ"))
					->setCellValue("D1", iconv("EUC-KR", "UTF-8","�ּ�"))
					->setCellValue("E1", iconv("EUC-KR", "UTF-8","�����ǰ��"))
					->setCellValue("F1", iconv("EUC-KR", "UTF-8","��ǰ����"))
					->setCellValue("G1", iconv("EUC-KR", "UTF-8","��۸޸�"))
					->setCellValue("H1", iconv("EUC-KR", "UTF-8","��۹��"))
					->setCellValue("I1", iconv("EUC-KR", "UTF-8","�������"))
					->setCellValue("J1", iconv("EUC-KR", "UTF-8","�����"))
					->setCellValue("K1", iconv("EUC-KR", "UTF-8","����Ͻ�"))
					->setCellValue("L1", iconv("EUC-KR", "UTF-8","�Ϸ�ó��"))
					->setCellValue("M1", iconv("EUC-KR", "UTF-8","��뿩��"));

	//���峻�� ���
	
	$objPHPExcel->createSheet();
		$objPHPExcel->setActiveSheetIndex(1)
			->setTitle(iconv("EUC-KR", "UTF-8", "���峻��"))
			->setCellValue("A1", iconv("EUC-KR", "UTF-8","�����ȣ"))
			->setCellValue("B1", iconv("EUC-KR", "UTF-8","�ù��"))
			->setCellValue("C1", iconv("EUC-KR", "UTF-8","�����"))
			->setCellValue("D1", iconv("EUC-KR", "UTF-8","������"))
			->setCellValue("E1", iconv("EUC-KR", "UTF-8","��������ȭ"))
			->setCellValue("F1", iconv("EUC-KR", "UTF-8","�������ڵ���"))
			->setCellValue("G1", iconv("EUC-KR", "UTF-8","�������ּ�"));

	$o = 1;
	if (sizeof($arr_rs) > 0) {

		for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

			$INDIVIDUAL_NO			= trim($arr_rs[$j]["INDIVIDUAL_NO"]);
			$R_ZIPCODE			    = trim($arr_rs[$j]["R_ZIPCODE"]); 
			$R_ADDR1 				= trim($arr_rs[$j]["R_ADDR1"]);
			$R_MEM_NM				= trim($arr_rs[$j]["R_MEM_NM"]);
			$R_PHONE				= trim($arr_rs[$j]["R_PHONE"]); 
			$R_HPHONE				= trim($arr_rs[$j]["R_HPHONE"]); 
			$GOODS_DELIVERY_NAME	= trim($arr_rs[$j]["GOODS_DELIVERY_NAME"]); 
			$SUB_QTY				= trim($arr_rs[$j]["SUB_QTY"]);
			$MEMO					= trim($arr_rs[$j]["MEMO"]);
			$DELIVERY_TYPE			= trim($arr_rs[$j]["DELIVERY_TYPE"]);
			$IS_DELIVERED			= trim($arr_rs[$j]["IS_DELIVERED"]);
			$USE_TF					= trim($arr_rs[$j]["USE_TF"]);

			$REG_DATE				= date("n��j��H��i��", strtotime(trim($arr_rs[$j]["REG_DATE"])));

			$REG_ADM				= trim($arr_rs[$j]["REG_ADM"]);
			$REG_ADM = getAdminName($conn, $REG_ADM);

			$DELIVERY_PAPER_QTY = countOrderDeliveryPaper($conn, $order_goods_no, $INDIVIDUAL_NO);

			if($IS_DELIVERED == "Y") { 
				$DELIVERY_DATE = date("n��j��H��i��", strtotime(trim($arr_rs[$j]["DELIVERY_DATE"])));

			} else { 
				$DELIVERY_DATE = "�����";
			}

			$k = $j+2;

			$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A$k", iconv("EUC-KR", "UTF-8", $R_MEM_NM))
							->setCellValue("B$k", iconv("EUC-KR", "UTF-8", $R_PHONE))
							->setCellValue("C$k", iconv("EUC-KR", "UTF-8", $R_HPHONE))
							->setCellValue("D$k", iconv("EUC-KR", "UTF-8", $R_ADDR1))
							->setCellValue("E$k", iconv("EUC-KR", "UTF-8", $GOODS_DELIVERY_NAME))
							->setCellValue("F$k", iconv("EUC-KR", "UTF-8", $SUB_QTY))
							->setCellValue("G$k", iconv("EUC-KR", "UTF-8", $MEMO))
							->setCellValue("H$k", iconv("EUC-KR", "UTF-8", getDcodeName($conn,"DELIVERY_TYPE",$DELIVERY_TYPE)))
							->setCellValue("I$k", iconv("EUC-KR", "UTF-8", $DELIVERY_PAPER_QTY))
							->setCellValue("J$k", iconv("EUC-KR", "UTF-8", $REG_ADM))
							->setCellValue("K$k", iconv("EUC-KR", "UTF-8", $REG_DATE))
							->setCellValue("L$k", iconv("EUC-KR", "UTF-8", $DELIVERY_DATE))
							->setCellValue("M$k", iconv("EUC-KR", "UTF-8", ($USE_TF == 'Y' ? "�����" : "������")));
	

			
			$arr_order_rs = listOrderDeliveryPaper($conn, $order_goods_no, $INDIVIDUAL_NO);
			if(sizeof($arr_order_rs) > 0) { 

				for($i = 0; $i < sizeof($arr_order_rs); $i ++) { 

					$rs_order_goods_delivery_no	= trim($arr_order_rs[$i]["ORDER_GOODS_DELIVERY_NO"]);
					$rs_delivery_seq	        = trim($arr_order_rs[$i]["DELIVERY_SEQ"]); 
					$rs_delivery_no 		    = trim($arr_order_rs[$i]["DELIVERY_NO"]);
					$rs_delivery_cp				= trim($arr_order_rs[$i]["DELIVERY_CP"]);
					$rs_order_nm		        = trim($arr_order_rs[$i]["ORDER_NM"]); 
					$rs_order_phone		        = trim($arr_order_rs[$i]["ORDER_PHONE"]);
					$rs_order_manager_nm	    = trim($arr_order_rs[$i]["ORDER_MANAGER_NM"]);
					$rs_order_manager_phone		= trim($arr_order_rs[$i]["ORDER_MANAGER_PHONE"]);
					$rs_receiver_nm		        = trim($arr_order_rs[$i]["RECEIVER_NM"]); 
					$rs_receiver_phone		    = trim($arr_order_rs[$i]["RECEIVER_PHONE"]);
					$rs_receiver_hphone		    = trim($arr_order_rs[$i]["RECEIVER_HPHONE"]);
					$rs_receiver_addr			= trim($arr_order_rs[$i]["RECEIVER_ADDR"]); 
					$rs_goods_delivery_name	    = trim($arr_order_rs[$i]["GOODS_DELIVERY_NAME"]); 
					$rs_memo				    = trim($arr_order_rs[$i]["MEMO"]); 
					$rs_delivery_fee			= trim($arr_order_rs[$i]["DELIVERY_FEE"]); 
					$rs_delivery_fee_code		= trim($arr_order_rs[$i]["DELIVERY_FEE_CODE"]); 
					$rs_delivery_claim_code		= trim($arr_order_rs[$i]["DELIVERY_CLAIM_CODE"]); 
					$rs_delivery_date           = trim($arr_order_rs[$i]["DELIVERY_DATE"]); 
					$rs_use_tf					= trim($arr_order_rs[$i]["USE_TF"]); 

					if($rs_use_tf == "N")
						continue;

					$o = $o + 1;

					$objPHPExcel->setActiveSheetIndex(1)
							->setCellValue("A$o", iconv("EUC-KR", "UTF-8", $rs_delivery_no))
							->setCellValue("B$o", iconv("EUC-KR", "UTF-8", $rs_delivery_cp))
							->setCellValue("C$o", iconv("EUC-KR", "UTF-8", $rs_goods_delivery_name))
							->setCellValue("D$o", iconv("EUC-KR", "UTF-8", $rs_receiver_nm))
							->setCellValue("E$o", iconv("EUC-KR", "UTF-8", $rs_receiver_phone))
							->setCellValue("F$o", iconv("EUC-KR", "UTF-8", $rs_receiver_hphone))
							->setCellValue("G$o", iconv("EUC-KR", "UTF-8", $rs_receiver_addr));
				}
			}
			//$o = $o + 1;

		}
	}

	$objPHPExcel->setActiveSheetIndex(0)->getStyle("A1:M$k")->applyFromArray($BStyle);
	$objPHPExcel->setActiveSheetIndex(0)->getStyle("A1:M1")->getFont()->setSize(10)->setBold(true);
	$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(12);
	$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(12);
	$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(12);
	$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(20);
	$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(30);

	$objPHPExcel->setActiveSheetIndex(1)->getStyle("A1:G$o")->applyFromArray($BStyle);
	$objPHPExcel->setActiveSheetIndex(1)->getStyle("A1:G1")->getFont()->setSize(10)->setBold(true);
	$objPHPExcel->setActiveSheetIndex(1)->getColumnDimension('A')->setWidth(12);
	$objPHPExcel->setActiveSheetIndex(1)->getColumnDimension('B')->setWidth(10);
	$objPHPExcel->setActiveSheetIndex(1)->getColumnDimension('C')->setWidth(40);
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	//$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(100);

	// ������ ���������� utf-8�� ��� �ѱ����� �̸��� �����Ƿ� euc-kr�� ��ȯ���ش�.
	$filename = "�����ù�����-".date("Ymd");

	// Redirect output to a client��s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment;filename=".$filename.".xls");
	header('Cache-Control: max-age=0');
 
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

	mysql_close($conn);
	exit;

?>