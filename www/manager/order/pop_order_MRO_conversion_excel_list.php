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
	$menu_right = "SP009"; // 메뉴마다 셋팅 해 주어야 합니다

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

#====================================================================
# Request Parameter
#====================================================================

	$arr_rs = listTempOrderMROConversion($conn, $temp_no);


	require_once "../../_PHPExcel/Classes/PHPExcel.php";

	$objPHPExcel = new PHPExcel();



	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A1", iconv("EUC-KR", "UTF-8", "업체주문일"))
				->setCellValue("B1", iconv("EUC-KR", "UTF-8", "판매업체주문번호"))
				->setCellValue("C1", iconv("EUC-KR", "UTF-8", "매출업체"))
				->setCellValue("D1", iconv("EUC-KR", "UTF-8", "상품코드"))
				->setCellValue("E1", iconv("EUC-KR", "UTF-8", "상품명"))
				->setCellValue("F1", iconv("EUC-KR", "UTF-8", "판매가"))
				->setCellValue("G1", iconv("EUC-KR", "UTF-8", "주문수량"))
				->setCellValue("H1", iconv("EUC-KR", "UTF-8", "주문자"))
				->setCellValue("I1", iconv("EUC-KR", "UTF-8", "주문자연락처"))
				->setCellValue("J1", iconv("EUC-KR", "UTF-8", "주문자휴대전화번호"))
				->setCellValue("K1", iconv("EUC-KR", "UTF-8", "수취인"))
				->setCellValue("L1", iconv("EUC-KR", "UTF-8", "수취인연락처"))
				->setCellValue("M1", iconv("EUC-KR", "UTF-8", "수취인휴대전화번호"))
				->setCellValue("N1", iconv("EUC-KR", "UTF-8", "우편번호"))
				->setCellValue("O1", iconv("EUC-KR", "UTF-8", "주소"))
				->setCellValue("P1", iconv("EUC-KR", "UTF-8", "주문자메모"))
				->setCellValue("Q1", iconv("EUC-KR", "UTF-8", "포장지코드"))
				->setCellValue("R1", iconv("EUC-KR", "UTF-8", "스티커코드"))
				->setCellValue("S1", iconv("EUC-KR", "UTF-8", "스티커메세지"))
				->setCellValue("T1", iconv("EUC-KR", "UTF-8", "인쇄메세지"))
				->setCellValue("U1", iconv("EUC-KR", "UTF-8", "아웃박스스티커유무"))
				->setCellValue("V1", iconv("EUC-KR", "UTF-8", "영업담당자"))
				->setCellValue("W1", iconv("EUC-KR", "UTF-8", "출고예정일"))
				->setCellValue("X1", iconv("EUC-KR", "UTF-8", "배송방식"))
				->setCellValue("Y1", iconv("EUC-KR", "UTF-8", "배송비"))
				->setCellValue("Z1", iconv("EUC-KR", "UTF-8", "작업메모"))
				->setCellValue("AA1", iconv("EUC-KR", "UTF-8", "택배사"))
				->setCellValue("AB1", iconv("EUC-KR", "UTF-8", "보내는분"))
				->setCellValue("AC1", iconv("EUC-KR", "UTF-8", "보내는분연락처"))
				->setCellValue("AD1", iconv("EUC-KR", "UTF-8", "박스입수"));

	if (sizeof($arr_rs) > 0) {

		for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

			$ORDER_DATE				= trim($arr_rs[$j]["ORDER_DATE"]);
			$CP_ORDER_NO			= trim($arr_rs[$j]["CP_ORDER_NO"]);
			$CP_NO					= trim($arr_rs[$j]["CP_NO"]);
			$GOODS_CODE				= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["GOODS_CODE"]));
			$GOODS_NAME				= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["GOODS_NAME"]));
			$SALE_PRICE				= SetStringFromDB($arr_rs[$j]["SALE_PRICE"]);
			$QTY					= SetStringFromDB($arr_rs[$j]["QTY"]);
			$O_MEM_NM				= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["O_MEM_NM"]));
			$O_PHONE				= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["O_PHONE"]));
			$O_HPHONE				= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["O_HPHONE"]));
			$R_MEM_NM				= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["R_MEM_NM"]));
			$R_PHONE				= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["R_PHONE"]));
			$R_HPHONE				= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["R_HPHONE"]));

			$ZIPCODE				= SetStringFromDB($arr_rs[$j]["ZIPCODE"]);
			$R_ADDR1				= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["R_ADDR1"]));
			$MEMO					= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["MEMO"]));
			$OPT_WRAP_CODE			= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["OPT_WRAP_CODE"]));
			$OPT_STICKER_CODE		= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["OPT_STICKER_CODE"]));
			$OPT_STICKER_MSG		= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["OPT_STICKER_MSG"]));
			$OPT_PRINT_MSG			= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["OPT_PRINT_MSG"]));
			$OPT_OUTBOX_TF			= SetStringFromDB($arr_rs[$j]["OPT_OUTBOX_TF"]);
			$OPT_MANAGER_NM			= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["OPT_MANAGER_NM"]));
			$OPT_OUTSTOCK_DATE		= SetStringFromDB($arr_rs[$j]["OPT_OUTSTOCK_DATE"]);
			$DELIVERY_TYPE			= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["DELIVERY_TYPE"]));
			$DELIVERY_PRICE			= SetStringFromDB($arr_rs[$j]["DELIVERY_PRICE"]);
			$WORK_MEMO				= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["WORK_MEMO"]));
			$DELIVERY_CP			= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["DELIVERY_CP"]));
			$SENDER_NM				= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["SENDER_NM"]));
			$SENDER_PHONE			= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["SENDER_PHONE"]));
			$DELIVERY_CNT_IN_BOX	= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]));

			$k = $j+2;

			$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A$k", $ORDER_DATE)
							->setCellValue("B$k", $CP_ORDER_NO)
							->setCellValue("C$k", $CP_NO)
							->setCellValue("D$k", $GOODS_CODE)
							->setCellValue("E$k", $GOODS_NAME)
							->setCellValue("F$k", $SALE_PRICE)
							->setCellValue("G$k", $QTY)
							->setCellValue("H$k", $O_MEM_NM)
							->setCellValue("I$k", $O_PHONE)
							->setCellValue("J$k", $O_HPHONE)
							->setCellValue("K$k", $R_MEM_NM)
							->setCellValue("L$k", $R_PHONE)
							->setCellValue("M$k", $R_HPHONE)
							->setCellValueExplicit("N$k", $ZIPCODE, PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValue("O$k", $R_ADDR1)
							->setCellValue("P$k", $MEMO)
							->setCellValue("Q$k", $OPT_WRAP_CODE)
							->setCellValue("R$k", $OPT_STICKER_CODE)
							->setCellValue("S$k", $OPT_STICKER_MSG)
							->setCellValue("T$k", $OPT_PRINT_MSG)
							->setCellValue("U$k", $OPT_OUTBOX_TF)
							->setCellValue("V$k", $OPT_MANAGER_NM)
							->setCellValue("W$k", $OPT_OUTSTOCK_DATE)
							->setCellValue("X$k", $DELIVERY_TYPE)
							->setCellValue("Y$k", $DELIVERY_PRICE)
							->setCellValue("Z$k", $WORK_MEMO)
							->setCellValue("AA$k", $DELIVERY_CP)
							->setCellValue("AB$k", $SENDER_NM)
							->setCellValue("AC$k", $SENDER_PHONE)
							->setCellValue("AD$k", $DELIVERY_CNT_IN_BOX);

		}
	}

	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('Sheet1');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	//$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(100);

	// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
	//$filename = iconv("UTF-8", "EUC-KR", "MRO -> 통합주문 변환 -".date("Ymd",strtotime("0 month")));
	$filename = "인터넷몰 통합주문 변환 - ".date("Ymd",strtotime("0 month"));

	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment;filename=".$filename.".xls");
	header('Cache-Control: max-age=0');
 
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

	mysql_close($conn);
	exit;
?>