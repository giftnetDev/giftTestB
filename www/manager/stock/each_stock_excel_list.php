<?
ini_set('memory_limit',-1);
session_start();
?>
<?
# =============================================================================
# File Name    : each_stock_excel_list.php
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "SG025"; // 메뉴마다 셋팅 해 주어야 합니다

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

	if($tab_index == "")
		$tab_index = "0";

	if($warehouse_code == "")
		$warehouse_code = "WH001";


	if ($start_date == "") {
		$start_date = date("Y-m-d",strtotime("-1 month"));
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));
	} else {
		$end_date = trim($end_date);
	}

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

#============================================================
# Page process
#============================================================

	if ($nPage <> "") {
		$nPage = (int)($nPage);
	} else {
		$nPage = 1;
	}

	if ($nPageSize <> "") {
		$nPageSize = (int)($nPageSize);
	} else {
		$nPageSize = 20;
	}

	$nPageBlock	= 10;
	

#===============================================================
# Get Search list count
#===============================================================

	$arr_list = listEachStock($conn, $warehouse_code, $search_field, $search_str, $order_field, $order_str);

	require_once "../../_PHPExcel/Classes/PHPExcel.php";

	$objPHPExcel = new PHPExcel();


	$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A1", "상품코드")
					->setCellValue("B1", "상품명")
					->setCellValue("C1", "정상수량")
					->setCellValue("D1", "불량수량");

	if (sizeof($arr_list) > 0) {

		for ($j = 0 ; $j < sizeof($arr_list); $j++) {

			$GOODS_NO					= trim($arr_list[$j]["GOODS_NO"]);
			$GOODS_CODE					= trim($arr_list[$j]["GOODS_CODE"]);
			$GOODS_NAME					= SetStringFromDB($arr_list[$j]["GOODS_NAME"]);
			$N_TOTAL					= trim($arr_list[$j]["N_TOTAL"]);
			$B_TOTAL					= trim($arr_list[$j]["B_TOTAL"]);
			

			$k = $j+2;

			$GOODS_CODE = iconv("EUC-KR", "UTF-8", $GOODS_CODE);
			$GOODS_NAME = iconv("EUC-KR", "UTF-8", $GOODS_NAME);
				
			$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A$k", $GOODS_CODE)
							->setCellValue("B$k", $GOODS_NAME)
							->setCellValue("C$k", $N_TOTAL)
							->setCellValue("D$k", $B_TOTAL);
	
		}
	}

	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('Sheet1');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	//$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(100);

	// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
	$filename = "개별창고관리";

	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment;filename=".$filename.".xls");
	header('Cache-Control: max-age=0');
 
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

	mysql_close($conn);
	exit;

?>