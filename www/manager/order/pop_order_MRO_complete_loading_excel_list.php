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

	$arr_rs = listTempOrderMROComplete($conn, $temp_no);


	require_once "../../_PHPExcel/Classes/PHPExcel.php";

	$objPHPExcel = new PHPExcel();

	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A1", iconv("EUC-KR", "UTF-8", "주문일자"))
				->setCellValue("B1", iconv("EUC-KR", "UTF-8", "주문번호"))
				->setCellValue("C1", iconv("EUC-KR", "UTF-8", "순번"))
				->setCellValue("D1", iconv("EUC-KR", "UTF-8", "상품코드"))
				->setCellValue("E1", iconv("EUC-KR", "UTF-8", "포장순번"))
				->setCellValue("F1", iconv("EUC-KR", "UTF-8", "포장수량"))
				->setCellValue("G1", iconv("EUC-KR", "UTF-8", "보내는 사람"))
				->setCellValue("H1", iconv("EUC-KR", "UTF-8", "받는 사람"))
				->setCellValue("I1", iconv("EUC-KR", "UTF-8", "택배사코드"))
				->setCellValue("J1", iconv("EUC-KR", "UTF-8", "송장번호"));

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);

	if (sizeof($arr_rs) > 0) {

		for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

			$ORDER_DATE				= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["ORDER_DATE"]));
			$ORDER_NO				= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["ORDER_NO"]));
			$SEQ					= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["SEQ"]));
			$SELLER_GOODS_CODE		= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["SELLER_GOODS_CODE"]));
			$BOX_SEQ				= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["BOX_SEQ"]));
			$BOX_QTY				= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["BOX_QTY"]));
			$SENDER					= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["SENDER"]));
			$RECEIVER				= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["RECEIVER"]));
			$DELIVERY_CODE			= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["DELIVERY_CODE"]));
			$DELIVERY_NO			= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["DELIVERY_NO"]));
			//$ETC					= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr_rs[$j]["ETC"]));

			$k = $j+2;
			 

			$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A$k", $ORDER_DATE)
							->setCellValueExplicit("B$k", $ORDER_NO, PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit("C$k", $SEQ, PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit("D$k", $SELLER_GOODS_CODE, PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit("E$k", $BOX_SEQ, PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit("F$k", $BOX_QTY, PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValue("G$k", $SENDER)
							->setCellValue("H$k", $RECEIVER)
							->setCellValueExplicit("I$k", $DELIVERY_CODE, PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit("J$k", $DELIVERY_NO, PHPExcel_Cell_DataType::TYPE_STRING);

		}
	}


	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('Sheet1');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
	//$filename = iconv("UTF-8", "EUC-KR", "MRO -> 통합주문 변환 -".date("Ymd",strtotime("0 month")));
	$filename = "MRO 완료 로딩 - ".date("Ymd",strtotime("0 month"));

	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment;filename=".$filename.".xls");
	header('Cache-Control: max-age=0');
 
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

	mysql_close($conn);
	exit;
?>