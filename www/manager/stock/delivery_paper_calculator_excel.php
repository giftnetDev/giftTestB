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
	$menu_right = "SG018"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/stock/stock.php";


#====================================================================
# Request Parameter
#====================================================================


	$arr_sum = listDeliveryCalculator($conn);

	require_once "../../_PHPExcel/Classes/PHPExcel.php";

	$objPHPExcel = new PHPExcel();

	$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A1", iconv("EUC-KR", "UTF-8", "상품코드"))
					->setCellValue("B1", iconv("EUC-KR", "UTF-8", "상품명"))
					->setCellValue("C1", iconv("EUC-KR", "UTF-8", "총 수량"));

	if (sizeof($arr_sum) > 0) {

		for ($j = 0 ; $j < sizeof($arr_sum); $j++) {
					
			$GOODS_CODE				= trim($arr_sum[$j]["GOODS_CODE"]);
			$GOODS_NAME				= SetStringFromDB($arr_sum[$j]["GOODS_NAME"]);
			$SUM_GOODS_TOTAL		= trim($arr_sum[$j]["SUM_GOODS_TOTAL"]);

			$k = $j+2;

			$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A$k", $GOODS_CODE)
							->setCellValue("B$k", iconv("EUC-KR", "UTF-8",$GOODS_NAME))
							->setCellValue("C$k", $SUM_GOODS_TOTAL);
		}
	}

	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('Sheet1');


	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	//$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(60);


	// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
	$filename = "송장계산기"; //iconv("UTF-8", "EUC-KR", "발주 리스트");

	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment;filename=".$filename.".xls");
	header('Cache-Control: max-age=0');
 
 
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

	mysql_close($conn);
	exit;

?>