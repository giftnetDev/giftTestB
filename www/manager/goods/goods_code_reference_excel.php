<?session_start();
  set_time_limit(6000); 
  ini_set("memory_limit", -1);
?>
<?

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#====================================================================
# common_header Check Session
#====================================================================
	//require "../../_common/common_header.php"; 

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/goods/goods.php";

#===============================================================
# Get Search list count
#===============================================================

	$arr_goods = listGoodsCodeReference($conn, $keyword, $order_field, $order_str);


	require_once "../../_PHPExcel/Classes/PHPExcel.php";

	// Cell caching to reduce memory usage.
	$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
	$cacheSettings = array( " memoryCacheSize " => "8MB");
	PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings); 

	$objPHPExcel = new PHPExcel();

	$sheetIndex = $objPHPExcel->setActiveSheetIndex(0);

	$fColor = new PHPExcel_Style_Color();
	$fColor->setRGB("000000");

	$BStyle = array(
	  "borders" => array(
		"allborders" => array(
		  "style" => PHPExcel_Style_Border::BORDER_THIN
		)
	  ),
	  "font"  => array(
        "size"  => 9,
        "name"  => "Gulim")
	);

	$BStyle_title = array(
	  "borders" => array(
		"allborders" => array(
		  "style" => PHPExcel_Style_Border::BORDER_THIN
		)
	  ),
	  "font"  => array(
		"color" => array("rgb" => "FF0000"),
        "size"  => 10,
        "name"  => "Gulim")
	);
	
	////////////////////////////////////////////////////////////////////////////////
	//	판매중
	////////////////////////////////////////////////////////////////////////////////	

	//No.	위치	상품코드	상품명

	$sheetIndex->setCellValue("A1", iconv("EUC-KR", "UTF-8","No."));
	$sheetIndex->setCellValue("B1", iconv("EUC-KR", "UTF-8","위치"));
	$sheetIndex->setCellValue("C1", iconv("EUC-KR", "UTF-8","상품코드"));
	$sheetIndex->setCellValue("D1", iconv("EUC-KR", "UTF-8","상품명"));

	$cntRow = sizeof($arr_goods);
	$i_row = 2;
	if($cntRow >= 1) {
		for($i = 0; $i < $cntRow; $i ++) { 

			$REF_NO			= trim($arr_goods[$i]["REF_NO"]);
			$GOODS_CODE		= trim($arr_goods[$i]["GOODS_CODE"]);
			$GOODS_NAME		= trim($arr_goods[$i]["GOODS_NAME"]);
			$FROM_TABLE	    = trim($arr_goods[$i]["FROM_TABLE"]); 
			
			$sheetIndex->setCellValue("A".$i_row, iconv("EUC-KR", "UTF-8", $REF_NO));
			$sheetIndex->setCellValue("B".$i_row, iconv("EUC-KR", "UTF-8", $FROM_TABLE));
			$sheetIndex->setCellValue("C".$i_row, iconv("EUC-KR", "UTF-8", $GOODS_CODE));
			$sheetIndex->setCellValue("D".$i_row, iconv("EUC-KR", "UTF-8", $GOODS_NAME));
			
			$sheetIndex->getRowDimension($i_row)->setRowHeight(22.50);
			
			$i_row ++;
		}
	}

	$sheetIndex->getStyle("A1:D1")->applyFromArray($BStyle_title);
	$sheetIndex->getStyle("A2:D".($i_row-1))->applyFromArray($BStyle);

	$sheetIndex->getStyle("A1:D1")->getFont()->setBold(true);

	$sheetIndex->getStyle("A1:D".($i_row-1))->getAlignment()->setWrapText(true)
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$sheetIndex->getColumnDimension("A")->setWidth(8);
	$sheetIndex->getColumnDimension("B")->setWidth(15);
	$sheetIndex->getColumnDimension("C")->setWidth(9.70);
	$sheetIndex->getColumnDimension("D")->setWidth(34);

	// Rename sheet
	$sheetIndex->setTitle(iconv("EUC-KR", "UTF-8","임시코드"));


	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
	$filename = "상품코드관리-".date("Ymd");

	// Redirect output to a client’s web browser (Excel5)
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment;filename=".$filename.".xls");
	header("Cache-Control: max-age=0");

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
	$objWriter->setUseDiskCaching(true);
	$objWriter->save("php://output");

	mysql_close($conn);
	exit;


?>
				
