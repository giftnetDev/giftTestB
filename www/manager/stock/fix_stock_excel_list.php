<?
ini_set('memory_limit',-1);
session_start();
?>
<?
# =============================================================================
# File Name    : base_stock_excel_list.php
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "SG011"; // 메뉴마다 셋팅 해 주어야 합니다

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

	if ($order_field == "")
		$order_field = "B.GOODS_NAME";

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	
	$del_tf = "N";
#============================================================
# Page process
#============================================================

	$nPage = 1;

	$nPageBlock	= 10;

#===============================================================
# Get Search list count
#===============================================================


	$nListCnt =totalCntStockGoods($conn, $con_cate, $con_in_cp_no, $con_out_cp_no, $search_field, $search_str);
	
	#echo $nListCnt;

	$nPageSize = $nListCnt;

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listStockGoods($conn, $con_cate, $con_in_cp_no, $con_out_cp_no, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize, $nListCnt);


	require_once "../../_PHPExcel/Classes/PHPExcel.php";

	$objPHPExcel = new PHPExcel();


	$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A1", "상품 NO")
					->setCellValue("B1", "상품코드")
					->setCellValue("C1", "상품명")
					->setCellValue("D1", "박스입수")
					->setCellValue("E1", "정상재고")
					->setCellValue("F1", "불량재고")
					->setCellValue("G1", "비고")
					->setCellValue("H1", "[전산]정상재고")
					->setCellValue("I1", "[전산]불량재고");

	if (sizeof($arr_rs) > 0) {

		for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

			$GOODS_NO						= trim($arr_rs[$j]["GOODS_NO"]);
			$GOODS_CODE					= trim($arr_rs[$j]["GOODS_CODE"]);
			$DELIVERY_CNT_IN_BOX		= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);
			$GOODS_NAME					= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
			$STOCK_CNT					= trim($arr_rs[$j]["STOCK_CNT"]);
			$BSTOCK_CNT					= trim($arr_rs[$j]["BSTOCK_CNT"]);
			/*
			$S_IN_QTY					= trim($arr_rs[$j]["S_IN_QTY"]);
			$S_IN_BQTY					= trim($arr_rs[$j]["S_IN_BQTY"]);
			$S_IN_FQTY					= trim($arr_rs[$j]["S_IN_FQTY"]);
			$S_OUT_QTY					= trim($arr_rs[$j]["S_OUT_QTY"]);
			$S_OUT_BQTY					= trim($arr_rs[$j]["S_OUT_BQTY"]);
			$S_OUT_TQTY					= trim($arr_rs[$j]["S_OUT_TQTY"]);
			
			$FSTOCK_CNT					= trim($arr_rs[$j]["FSTOCK_CNT"]);
			$MSTOCK_CNT					= trim($arr_rs[$j]["MSTOCK_CNT"]);
			*/

			$k = $j+2;

			$GOODS_CODE = iconv("EUC-KR", "UTF-8", $GOODS_CODE);
			$GOODS_NAME = iconv("EUC-KR", "UTF-8", $GOODS_NAME);
				
			$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A$k", $GOODS_NO)
							->setCellValue("B$k", $GOODS_CODE)
							->setCellValue("C$k", $GOODS_NAME)
							->setCellValue("D$k", $DELIVERY_CNT_IN_BOX)
							->setCellValue("E$k", "")
							->setCellValue("F$k", "")
							->setCellValue("G$k", "")
							->setCellValue("H$k", $STOCK_CNT)
							->setCellValue("I$k", $BSTOCK_CNT);
	
		}
	}

	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('Sheet1');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(100);

	// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
	$filename = iconv("UTF-8", "EUC-KR", "재고실사등록 상품 리스트");

	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment;filename=".$filename.".xls");
	header('Cache-Control: max-age=0');
 
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

	mysql_close($conn);
	exit;

?>