<?
ini_set('memory_limit',-1);
session_start();
?>
<?
# =============================================================================
# File Name    : in_write_goods_excel.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright    : Copyright @C&C Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "SG001"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

#====================================================================
# common_header Check Session
#====================================================================
	include "../../_common/common_header.php"; 

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/stock/stock.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/company/company.php";
#====================================================================
# Request Parameter
#====================================================================

	$nListCnt =totalCntStockGoods($conn, $con_in_cp_no, $con_out_cp_no, $search_field, $search_str);
	
	$nPageSize = $nListCnt;

	$nPage = 1;

	$arr_rs = listStockGoods($conn, $con_in_cp_no, $con_out_cp_no, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize, $nListCnt);

	require_once "../../_PHPExcel/Classes/PHPExcel.php";

	$objPHPExcel = new PHPExcel();


	// Add some data
	
	$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A1", "상품코드")
					->setCellValue("B1", "상품명")
					->setCellValue("C1", "입고구분")
					->setCellValue("D1", "매입처")
					->setCellValue("E1", "입고수량")
					->setCellValue("F1", "매입단가")
					->setCellValue("G1", "입고사유")
					->setCellValue("H1", "입고일")
					->setCellValue("I1", "결제일");
	
	if (sizeof($arr_rs) > 0) {

		for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

				$GOODS_NO						= trim($arr_rs[$j]["GOODS_NO"]);
				$GOODS_CODE					= trim($arr_rs[$j]["GOODS_CODE"]);
				$GOODS_NAME					= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
				$S_IN_QTY						= trim($arr_rs[$j]["S_IN_QTY"]);
				$S_IN_BQTY					= trim($arr_rs[$j]["S_IN_BQTY"]);
				$S_IN_FQTY					= trim($arr_rs[$j]["S_IN_FQTY"]);
				$S_OUT_QTY					= trim($arr_rs[$j]["S_OUT_QTY"]);
				$S_OUT_BQTY					= trim($arr_rs[$j]["S_OUT_BQTY"]);
				$S_OUT_FQTY					= trim($arr_rs[$j]["S_OUT_FQTY"]);
				$STOCK_CNT					= trim($arr_rs[$j]["STOCK_CNT"]);
				$BSTOCK_CNT					= trim($arr_rs[$j]["BSTOCK_CNT"]);
				$FSTOCK_CNT					= trim($arr_rs[$j]["FSTOCK_CNT"]);
				$MSTOCK_CNT					= trim($arr_rs[$j]["MSTOCK_CNT"]);

				$goods_rs = selectGoods($conn, $GOODS_NO);

				$RS_PRICE		= trim($goods_rs[0]["BUY_PRICE"]); 
				$RS_CP_NO		= trim($goods_rs[0]["CATE_03"]); 
				
				$company_rs = selectCompany($conn, $RS_CP_NO);
				$RS_CP_CODE = trim($company_rs[0]["CP_CODE"]); 
				
				$k = $j+2;

				$GOODS_CODE = iconv("EUC-KR", "UTF-8", $GOODS_CODE);
				$GOODS_NAME = iconv("EUC-KR", "UTF-8", $GOODS_NAME);
				
				$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue("A$k", $GOODS_CODE)
								->setCellValue("B$k", $GOODS_NAME)
								->setCellValue("C$k", "정상입고")
								->setCellValue("D$k", $RS_CP_CODE)
								->setCellValue("E$k", "")
								->setCellValue("F$k", $RS_PRICE)
								->setCellValue("G$k", "LG")
								->setCellValue("H$k", date("Y-m-d",strtotime("0 month")))
								->setCellValue("I$k", date("Y-m-d",strtotime("0 month")));
	
		}
	}

	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('Sheet1');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
	$filename = iconv("UTF-8", "EUC-KR", "입고 상품 리스트");

	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment;filename=".$filename.".xls");
	header('Cache-Control: max-age=0');
 
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

	mysql_close($conn);
	exit;

#====================================================================
# DB Close
#====================================================================
?>