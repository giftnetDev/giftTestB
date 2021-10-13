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
	require "../../_classes/biz/payment/payment.php";

#====================================================================
# Request Parameter
#====================================================================


	$arr = listKyungbakLoading($conn, $start_date, $end_date);
	//exit;

	require_once "../../_PHPExcel/Classes/PHPExcel.php";

	$objPHPExcel = new PHPExcel();

	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A1", iconv("EUC-KR", "UTF-8", "��¥"))
				->setCellValue("B1", iconv("EUC-KR", "UTF-8", "��ǥ��ȣ"))
				->setCellValue("C1", iconv("EUC-KR", "UTF-8", "�����ڵ�"))
				->setCellValue("D1", iconv("EUC-KR", "UTF-8", "����"))
				->setCellValue("E1", iconv("EUC-KR", "UTF-8", "�ŷ�ó�ڵ�"))
				->setCellValue("F1", iconv("EUC-KR", "UTF-8", "�ŷ�ó"))
				->setCellValue("G1", iconv("EUC-KR", "UTF-8", "��ü�ŷ�ó�ڵ�"))
				->setCellValue("H1", iconv("EUC-KR", "UTF-8", "��ü�ŷ�ó"))
				->setCellValue("I1", iconv("EUC-KR", "UTF-8", "ǰ���ڵ�"))
				->setCellValue("J1", iconv("EUC-KR", "UTF-8", "ǰ��"))
				->setCellValue("K1", iconv("EUC-KR", "UTF-8", "�԰�"))
				->setCellValue("L1", iconv("EUC-KR", "UTF-8", "����"))
				->setCellValue("M1", iconv("EUC-KR", "UTF-8", "�ܰ�"))
				->setCellValue("N1", iconv("EUC-KR", "UTF-8", "�ݾ�"))
				->setCellValue("O1", iconv("EUC-KR", "UTF-8", "�ΰ���"))
				->setCellValue("P1", iconv("EUC-KR", "UTF-8", "��ǥ����"))
				->setCellValue("Q1", iconv("EUC-KR", "UTF-8", "����ڵ�"))
				->setCellValue("R1", iconv("EUC-KR", "UTF-8", "�����"))
				->setCellValue("S1", iconv("EUC-KR", "UTF-8", "����-�۾��޸�"));

	if(sizeof($arr) > 0) {
		
		$k = 2;
		for($j = 0; $j < sizeof($arr); $j++) {

			$DELIVERY_DATE		= SetStringFromDB($arr[$j]["DELIVERY_DATE"]);
			$CP_CODE			= SetStringFromDB($arr[$j]["CP_CODE"]);
			$CP_NAME			= SetStringFromDB($arr[$j]["CP_NAME"]);
			$GOODS_CODE			= SetStringFromDB($arr[$j]["GOODS_CODE"]);
			$GOODS_NAME			= SetStringFromDB($arr[$j]["GOODS_NAME"]);
			$QTY				= SetStringFromDB($arr[$j]["QTY"]);
			$SALE_PRICE			= SetStringFromDB($arr[$j]["SALE_PRICE"]);
			//$TOTAL_PRICE		= SetStringFromDB($arr[$j]["TOTAL_PRICE"]);
			$CATE_01			= SetStringFromDB($arr[$j]["CATE_01"]); //����,���õ�
			$O_MEM_NM			= SetStringFromDB($arr[$j]["O_MEM_NM"]); 
			$IS_MALL			= SetStringFromDB($arr[$j]["IS_MALL"]); 
			$SA_DELIVERY_PRICE	= SetStringFromDB($arr[$j]["SA_DELIVERY_PRICE"]); 
			$DISCOUNT_PRICE		= SetStringFromDB($arr[$j]["DISCOUNT_PRICE"]);
			$ORDER_STATE        = $arr[$j]["ORDER_STATE"];
			//$DELIVERY_TYPE      = $arr[$j]["DELIVERY_TYPE"];
			$ORDER_GOODS_NO     = $arr[$j]["ORDER_GOODS_NO"];
			$CATE_04			= SetStringFromDB($arr[$j]["CATE_04"]); //��ȯ 

			if($CATE_04 <> "")
				$CATE_04 = "��ȯ";

			$STR_ORDER_STATE = "";
			if($ORDER_STATE == 7)
				$STR_ORDER_STATE = "��ǰ";
			else
				$STR_ORDER_STATE = "";

			$OPT_MEMO			= SetStringFromDB($arr[$j]["OPT_MEMO"]); //�۾��޸�

			if($ORDER_GOODS_NO != 0) { 
				$QTY = getRefundAbleQty($conn, "", $ORDER_GOODS_NO);
			}

			$TOTAL_PRICE = $QTY * $SALE_PRICE;


			$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A$k", iconv("EUC-KR", "UTF-8", date("Y.m.d",strtotime($DELIVERY_DATE))))
							->setCellValue("B$k", "")
							->setCellValue("C$k", "3")
							->setCellValue("D$k", "")
							->setCellValue("E$k", iconv("EUC-KR", "UTF-8", $CP_CODE))
							->setCellValue("F$k", iconv("EUC-KR", "UTF-8", $CP_NAME))
							->setCellValue("G$k", "")
							->setCellValue("H$k", "")
							->setCellValue("I$k", iconv("EUC-KR", "UTF-8", $GOODS_CODE))
							->setCellValue("J$k", iconv("EUC-KR", "UTF-8", $GOODS_NAME))
							->setCellValue("K$k", "")
							->setCellValue("L$k", iconv("EUC-KR", "UTF-8", $QTY))
							->setCellValue("M$k", iconv("EUC-KR", "UTF-8", $SALE_PRICE))
							->setCellValue("N$k", iconv("EUC-KR", "UTF-8", $TOTAL_PRICE))
							->setCellValue("O$k", "")
							->setCellValue("P$k", iconv("EUC-KR", "UTF-8", ($IS_MALL == "Y" ? $O_MEM_NM : $CP_NAME).($CATE_01 != "" ? "/".$CATE_01 : "").($CATE_04 != "" ? "/".$CATE_04 : "").($STR_ORDER_STATE != "" ? "/".$STR_ORDER_STATE : "")))
							->setCellValue("Q$k", "")
							->setCellValue("R$k", "")
							->setCellValue("S$k", iconv("EUC-KR", "UTF-8", $OPT_MEMO))
			;

			/*
			if($ORDER_STATE == "2" && $DELIVERY_TYPE == "3") { 
				$arr_rs_individual = listDeliveryIndividual($conn, $ORDER_GOODS_NO, "DESC");
				for($o = 0; $o < sizeof($arr_rs_individual); $o ++) { 

					$INDIVIDUAL_NO	= trim($arr_rs_individual[$o]["INDIVIDUAL_NO"]);
					$R_ZIPCODE	    = trim($arr_rs_individual[$o]["R_ZIPCODE"]); 
					$R_ADDR1 		= trim($arr_rs_individual[$o]["R_ADDR1"]);
					$R_MEM_NM		= trim($arr_rs_individual[$o]["R_MEM_NM"]);
					$R_PHONE		= trim($arr_rs_individual[$o]["R_PHONE"]); 
					$R_HPHONE		= trim($arr_rs_individual[$o]["R_HPHONE"]); 
					$SUB_QTY		= trim($arr_rs_individual[$o]["SUB_QTY"]);
					$IS_DELIVERED	= trim($arr_rs_individual[$o]["IS_DELIVERED"]);

					if($IS_DELIVERED == "Y") { 
						$objPHPExcel->setActiveSheetIndex(0)
										->setCellValue("A$k", date("Y.m.d",strtotime($WORK_END_DATE)))
										->setCellValue("B$k", "")
										->setCellValue("C$k", "3")
										->setCellValue("D$k", "")
										->setCellValue("E$k", $CP_CODE)
										->setCellValue("F$k", $CP_NAME)
										->setCellValue("G$k", "")
										->setCellValue("H$k", "")
										->setCellValue("I$k", $GOODS_CODE)
										->setCellValue("J$k", $GOODS_NAME)
										->setCellValue("K$k", "")
										->setCellValue("L$k", $SUB_QTY)
										->setCellValue("M$k", $SALE_PRICE)
										->setCellValue("N$k", $TOTAL_PRICE)
										->setCellValue("O$k", "")
										->setCellValue("P$k", ($IS_MALL == "Y" ? $O_MEM_NM : $CP_NAME).($CATE_01 != "" ? "/".$CATE_01 : ""))
										->setCellValue("Q$k", "")
										->setCellValue("R$k", "")
						;
					}
				}

			}
			*/

			if($SA_DELIVERY_PRICE != "0" && $ORDER_STATE == "3") {
				
				$k = $k + 1;
				$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A$k", iconv("EUC-KR", "UTF-8", date("Y.m.d",strtotime($DELIVERY_DATE))))
							->setCellValue("B$k", "")
							->setCellValue("C$k", "3")
							->setCellValue("D$k", "")
							->setCellValue("E$k", iconv("EUC-KR", "UTF-8", $CP_CODE))
							->setCellValue("F$k", iconv("EUC-KR", "UTF-8", $CP_NAME))
							->setCellValue("G$k", "")
							->setCellValue("H$k", "")
							->setCellValue("I$k", "")
							->setCellValue("J$k", iconv("EUC-KR", "UTF-8", "�߰���ۺ�"))
							->setCellValue("K$k", "")
							->setCellValue("L$k", "")
							->setCellValue("M$k", iconv("EUC-KR", "UTF-8", $SA_DELIVERY_PRICE))
							->setCellValue("N$k", "")
							->setCellValue("O$k", "")
							->setCellValue("P$k", "")
							->setCellValue("Q$k", "")
							->setCellValue("R$k", "")
							->setCellValue("S$k", "")
				;

			}

			if($DISCOUNT_PRICE != "0" && $ORDER_STATE == "3") {
				
				$k = $k + 1;
				$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A$k", iconv("EUC-KR", "UTF-8", date("Y.m.d",strtotime($DELIVERY_DATE))))
							->setCellValue("B$k", "")
							->setCellValue("C$k", "3")
							->setCellValue("D$k", "")
							->setCellValue("E$k", iconv("EUC-KR", "UTF-8", $CP_CODE))
							->setCellValue("F$k", iconv("EUC-KR", "UTF-8", $CP_NAME))
							->setCellValue("G$k", "")
							->setCellValue("H$k", "")
							->setCellValue("I$k", "")
							->setCellValue("J$k", iconv("EUC-KR", "UTF-8", "��������"))
							->setCellValue("K$k", "")
							->setCellValue("L$k", "")
							->setCellValue("M$k", iconv("EUC-KR", "UTF-8", $DISCOUNT_PRICE))
							->setCellValue("N$k", "")
							->setCellValue("O$k", "")
							->setCellValue("P$k", "")
							->setCellValue("Q$k", "")
							->setCellValue("R$k", "")
							->setCellValue("S$k", "")
				;

			}
			
			$k = $k + 1;
		}
	}

	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('Sheet1');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	// ������ ���������� utf-8�� ��� �ѱ����� �̸��� �����Ƿ� euc-kr�� ��ȯ���ش�.
	//$filename = iconv("UTF-8", "EUC-KR", "MRO -> �����ֹ� ��ȯ -".date("Ymd",strtotime("0 month")));
	$filename = "��ڱ���ε� - ".date("Ymd",strtotime("0 month"));

	// Redirect output to a client��s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment;filename=".$filename.".xls");
	header('Cache-Control: max-age=0');
 
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

	mysql_close($conn);
	exit;
?>