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
	require "../../_classes/com/util/ImgUtil.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/company/company.php";


#====================================================================
# Request Parameter
#====================================================================

	$reserve_no				= trim(base64url_decode($reserve_no));
	$op_cp_no				= trim(base64url_decode($op_cp_no));

#===============================================================
# Get Search list count
#===============================================================

	if($reserve_no == "") exit; 
		
	$arr_order_rs = selectOrder($conn, $reserve_no);

	$rs_cp_no						= trim($arr_order_rs[0]["CP_NO"]); 
	$rs_order_no					= trim($arr_order_rs[0]["ORDER_NO"]); 
	$rs_reserve_no				    = trim($arr_order_rs[0]["RESERVE_NO"]); 
	$rs_o_mem_nm					= trim($arr_order_rs[0]["O_MEM_NM"]);
	$rs_o_phone						= trim($arr_order_rs[0]["O_PHONE"]);
	$rs_r_mem_nm					= trim($arr_order_rs[0]["R_MEM_NM"]); 
	$rs_r_zipcode					= trim($arr_order_rs[0]["R_ZIPCODE"]); 
	$rs_r_addr1						= trim($arr_order_rs[0]["R_ADDR1"]); 
	$rs_r_phone						= trim($arr_order_rs[0]["R_PHONE"]); 
	$rs_r_hphone					= trim($arr_order_rs[0]["R_HPHONE"]); 
	$rs_order_date					= trim($arr_order_rs[0]["ORDER_DATE"]); 
	
	$rs_order_date = date("Y년 n월 j일", strtotime($rs_order_date));
	
	$arr_cp = selectCompany($conn, $rs_cp_no);
	$IS_MALL		 = $arr_cp[0]["IS_MALL"];
	$SENDER_NM      = $arr_cp[0]["CP_NM"]." ".$arr_cp[0]["CP_NM2"];

	if($IS_MALL == "Y") { 

		//수령자 아니고 주문자 정보로 변경 2017-07-03
		$SENDER_NM      = $rs_o_mem_nm;
		$SENDER_ADDR	= "";
		$SENDER_CP_PHONE= $rs_o_phone;

	} else { 
		$SENDER_NM      = $arr_cp[0]["CP_NM"]." ".$arr_cp[0]["CP_NM2"];
		$SENDER_ADDR	= $arr_cp[0]["CP_ADDR"];
		$SENDER_CP_PHONE= $arr_cp[0]["CP_PHONE"];
	}

	$arr_op_cp = getOperatingCompany($conn, $op_cp_no);

	$OP_CP_NM		= $arr_op_cp[0]["CP_NM"]." ".$arr_op_cp[0]["CP_NM2"];
	$CP_PHONE		= $arr_op_cp[0]["CP_PHONE"];
	$CP_FAX			= $arr_op_cp[0]["CP_FAX"];
	$BIZ_NO			= $arr_op_cp[0]["BIZ_NO"];
	$CP_ADDR		= $arr_op_cp[0]["CP_ADDR"];
	$UPTEA			= $arr_op_cp[0]["UPTEA"];
	$UPJONG			= $arr_op_cp[0]["UPJONG"];


	$arr_rs = listManagerOrderGoods($conn, $reserve_no, $mem_no, "Y", "N");

	$TOTAL_SALE_PRICE = 0;
	for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
	
		$ORDER_GOODS_NO				= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
		$ORDER_SEQ					= trim($arr_rs[$j]["ORDER_SEQ"]);
		$GOODS_NO					= trim($arr_rs[$j]["GOODS_NO"]);
		$GOODS_STATE				= trim($arr_rs[$j]["GOODS_STATE"]);
		$GOODS_CODE					= SetStringFromDB(trim($arr_rs[$j]["GOODS_CODE"]));
		$GOODS_NAME					= SetStringFromDB(trim($arr_rs[$j]["GOODS_NAME"]));
		$GOODS_SUB_NAME				= SetStringFromDB(trim($arr_rs[$j]["GOODS_SUB_NAME"]));
		//$QTY						= trim($arr_rs[$j]["QTY"]);
		$BUY_PRICE					= trim($arr_rs[$j]["BUY_PRICE"]);
		$SALE_PRICE					= trim($arr_rs[$j]["SALE_PRICE"]);
		$EXTRA_PRICE				= trim($arr_rs[$j]["EXTRA_PRICE"]);
		$DELIVERY_PRICE				= trim($arr_rs[$j]["DELIVERY_PRICE"]);
		$SA_DELIVERY_PRICE			= trim($arr_rs[$j]["SA_DELIVERY_PRICE"]);
		$DISCOUNT_PRICE				= trim($arr_rs[$j]["DISCOUNT_PRICE"]);
		$ORDER_STATE				= trim($arr_rs[$j]["ORDER_STATE"]);
		$CATE_04					= trim($arr_rs[$j]["CATE_04"]);
		$CATE_01					= trim($arr_rs[$j]["CATE_01"]);


		$QTY = getRefundAbleQty_EstimateTransaction($conn, $reserve_no, $ORDER_GOODS_NO);

		//전체 취소 건 제외
		if($QTY == 0)
			continue;

		//교환건은 제외 2017-11-16
		if($CATE_04 <> '')
			continue;

		//판매가 0 제외 2018-11-14
		if($SALE_PRICE == 0)
			continue;

		//증정,샘플,추가 제외 2018-11-15
		if($CATE_01 <> '')
			continue;

		if($ORDER_STATE == "1" || $ORDER_STATE == "2" || $ORDER_STATE == "3") {

			$TOTAL_SALE_PRICE += $SALE_PRICE * $QTY - $DISCOUNT_PRICE;

		}
	
	}

	require_once "../../_PHPExcel/Classes/PHPExcel.php";

	// Cell caching to reduce memory usage.
	$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
	$cacheSettings = array( " memoryCacheSize " => "8MB");
	PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings); 

	$objPHPExcel = new PHPExcel();

	$sheetIndex = $objPHPExcel->setActiveSheetIndex(0);
	//실 엑셀과 0.71~0.72 차이남
	$sheetIndex->getColumnDimension("A")->setWidth(23.85);
	$sheetIndex->getColumnDimension("B")->setWidth(14.2);
	$sheetIndex->getColumnDimension("C")->setWidth(8.85);
	$sheetIndex->getColumnDimension("D")->setWidth(10.42);
	$sheetIndex->getColumnDimension("E")->setWidth(3.71);
	$sheetIndex->getColumnDimension("F")->setWidth(16.42);
	$sheetIndex->getColumnDimension("G")->setWidth(17.20);

	$BStyle_font = array(
	  "font"  => array(
        "name"  => "Gulim")
	);

	$BStyle = array(
	  "borders" => array(
		"allborders" => array(
		  "style" => PHPExcel_Style_Border::BORDER_THIN
		)
	  )
	);

	$BStyle_outline = array(
	  'borders' => array(
		'outline' => array(
		  'style' => PHPExcel_Style_Border::BORDER_THIN
		)
	  )
	);

	$style = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );

    $sheetIndex->getDefaultStyle()->applyFromArray($style);

	$sheetIndex->getRowDimension(1)->setRowHeight(14.25);

	//2열
	$sheetIndex->setCellValue("A2",iconv("EUC-KR", "UTF-8","견     적    서"));
	$sheetIndex->mergeCells("A2:G2");
	$sheetIndex->getStyle("A2:G2")->getFont()->setSize(28)->setBold(true);
	$sheetIndex->getRowDimension(2)->setRowHeight(60.00);


	//3열 
	$sheetIndex->setCellValue("E3", iconv("EUC-KR", "UTF-8","공 급 자"))
	->setCellValue("F3", iconv("EUC-KR", "UTF-8","등 록 번 호"))
	->setCellValue("G3", iconv("EUC-KR", "UTF-8", $BIZ_NO));
	$sheetIndex->mergeCells("E3:E7");
	$sheetIndex->getStyle("E3:E7")->getAlignment()->setWrapText(true);
	$sheetIndex->getRowDimension(3)->setRowHeight(29.25);

	//4열 
	$sheetIndex->setCellValue("A4", iconv("EUC-KR", "UTF-8", $rs_order_date))
	->setCellValue("F4", iconv("EUC-KR", "UTF-8","상호(법인명) "))
	->setCellValue("G4", iconv("EUC-KR", "UTF-8", $OP_CP_NM));
	$sheetIndex->mergeCells("A4:C4");
	$sheetIndex->getRowDimension(4)->setRowHeight(29.25);

	///www/upload_data/operating_image/giftnet_stamp.jpg
	$objDrawing = new PHPExcel_Worksheet_Drawing();

	$img_path = $_SERVER["DOCUMENT_ROOT"]."/upload_data/operating_image/giftnet_stamp.png";
	$objDrawing->setPath($img_path);
	$objDrawing->setCoordinates("G4");
	$objDrawing->setResizeProportional(true);
	$objDrawing->setWidth(50);
	$objDrawing->setWorksheet($sheetIndex);
	$objDrawing->setOffsetX(60);
	$objDrawing->setOffsetY(-8);

	//5열 
	$sheetIndex
	->setCellValue("F5", iconv("EUC-KR", "UTF-8","사업장주소"))
	->setCellValue("G5", iconv("EUC-KR", "UTF-8", $CP_ADDR));
	$sheetIndex->getStyle("G5")->getFont()->setSize(9);
	$sheetIndex->getStyle("G5")->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	//$sheetIndex->getRowDimension(5)->setRowHeight(29.25);

	//6열 
	$sheetIndex->setCellValue("A6", iconv("EUC-KR", "UTF-8", $SENDER_NM))
	->setCellValue("C6", iconv("EUC-KR", "UTF-8","귀하"))
	->setCellValue("F6", iconv("EUC-KR", "UTF-8","업 태 : ".$UPTEA))
	->setCellValue("G6", iconv("EUC-KR", "UTF-8","종  목 : ".$UPJONG));
	$sheetIndex->mergeCells("A6:B6");
	$sheetIndex->getStyle("A6")->getFont()->setSize(10)->setBold(true);
	$sheetIndex->getStyle("C6")->getFont()->setSize(10)->setBold(true);
	$sheetIndex->getStyle("A6:C6")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$sheetIndex->getRowDimension(6)->setRowHeight(29.25);

	//7열 
	$sheetIndex
	->setCellValue("F7", iconv("EUC-KR", "UTF-8","전 화 번 호"))
	->setCellValue("G7", iconv("EUC-KR", "UTF-8", $CP_PHONE));

	$sheetIndex->getStyle("E3:G7")->getFont()->setSize(10);
	$sheetIndex->getStyle("E3:G7")->applyFromArray($BStyle);
	$sheetIndex->getRowDimension(7)->setRowHeight(29.25);

	//8열 
	$sheetIndex->setCellValue("A8", iconv("EUC-KR", "UTF-8","(공급가액+세액)"))
	->setCellValue("B8", iconv("EUC-KR", "UTF-8", NUMBERSTRING($TOTAL_SALE_PRICE)))
	->setCellValue("F8", iconv("EUC-KR", "UTF-8", number_format($TOTAL_SALE_PRICE)));
	$sheetIndex->mergeCells("B8:E8");
	$sheetIndex->getStyle("A8")->getFont()->setSize(10);
	$sheetIndex->getStyle("B8:F8")->getFont()->setSize(10)->setBold(true);
	$sheetIndex->getStyle("F8")->getNumberFormat()->setFormatCode("#,##0");
	$sheetIndex->getRowDimension(8)->setRowHeight(29.25);

	//품           명	규  격	수  량	단    가		공  급  가  액	세       액
	//9열 
	$sheetIndex->setCellValue("A9", iconv("EUC-KR", "UTF-8","품 명"))
	->setCellValue("B9", iconv("EUC-KR", "UTF-8","규 격"))
	->setCellValue("C9", iconv("EUC-KR", "UTF-8","수 량"))
	->setCellValue("D9", iconv("EUC-KR", "UTF-8","단 가"))
	->setCellValue("F9", iconv("EUC-KR", "UTF-8","공 급 가 액"))
	->setCellValue("G10", iconv("EUC-KR", "UTF-8","세  액"));
	$sheetIndex->mergeCells("D9:E9");
	$sheetIndex->getStyle("A9:G9")->getFont()->setSize(10);
	$sheetIndex->getRowDimension(9)->setRowHeight(29.25);

	$k  = 10;

	for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
	
		$ORDER_GOODS_NO				= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
		$ORDER_SEQ					= trim($arr_rs[$j]["ORDER_SEQ"]);
		$GOODS_NO					= trim($arr_rs[$j]["GOODS_NO"]);
		$GOODS_STATE				= trim($arr_rs[$j]["GOODS_STATE"]);
		$GOODS_CODE					= SetStringFromDB(trim($arr_rs[$j]["GOODS_CODE"]));
		$GOODS_NAME					= SetStringFromDB(trim($arr_rs[$j]["GOODS_NAME"]));
		$GOODS_SUB_NAME				= SetStringFromDB(trim($arr_rs[$j]["GOODS_SUB_NAME"]));
		//$QTY						= trim($arr_rs[$j]["QTY"]);
		$BUY_PRICE					= trim($arr_rs[$j]["BUY_PRICE"]);
		$SALE_PRICE					= trim($arr_rs[$j]["SALE_PRICE"]);
		$EXTRA_PRICE				= trim($arr_rs[$j]["EXTRA_PRICE"]);
		$DELIVERY_PRICE				= trim($arr_rs[$j]["DELIVERY_PRICE"]);
		$SA_DELIVERY_PRICE			= trim($arr_rs[$j]["SA_DELIVERY_PRICE"]);
		$DISCOUNT_PRICE				= trim($arr_rs[$j]["DISCOUNT_PRICE"]);
		
		$CATE_01					= trim($arr_rs[$j]["CATE_01"]);
		$CATE_04					= trim($arr_rs[$j]["CATE_04"]);

		$ORDER_STATE				= trim($arr_rs[$j]["ORDER_STATE"]);
		$TAX_TF						= trim($arr_rs[$j]["TAX_TF"]);

		if($TAX_TF == "과세") 
			$STR_TAX_TF  = "부가세포함";
		else
			$STR_TAX_TF  = "면세";

		$QTY = getRefundAbleQty_EstimateTransaction($conn, $reserve_no, $ORDER_GOODS_NO);

		//전체 취소 건 제외
		if($QTY == 0) continue;

		//교환건은 제외 2017-11-16
		if($CATE_04 <> '')
			continue;

		//판매가 0 제외 2018-11-14
		if($SALE_PRICE == 0)
			continue;

		//증정,샘플,추가 제외 2018-11-15
		if($CATE_01 <> '')
			continue;

		if($ORDER_STATE == "1" || $ORDER_STATE == "2" || $ORDER_STATE == "3") {  
		
			// 자연을담다베이킹소다4종B세트 		 40 	 \5,000 		 \200,000 	부가세포함
			//10열 

			$sheetIndex->setCellValue("A".$k, iconv("EUC-KR", "UTF-8", ($CATE_01 <> "" ? $CATE_01.") " : "").$GOODS_NAME))
			->setCellValue("B".$k, iconv("EUC-KR", "UTF-8", $GOODS_SUB_NAME))
			->setCellValue("C".$k, iconv("EUC-KR", "UTF-8", $QTY))
			->setCellValue("D".$k, iconv("EUC-KR", "UTF-8", $SALE_PRICE))
			->setCellValue("F".$k, iconv("EUC-KR", "UTF-8", $QTY * $SALE_PRICE))
			->setCellValue("G".$k, iconv("EUC-KR", "UTF-8", $STR_TAX_TF));
			$sheetIndex->mergeCells("D$k:E$k");
			$sheetIndex->getStyle("A$k")->getAlignment()->setWrapText(true);
			$sheetIndex->getStyle("A$k:G$k")->getFont()->setSize(9)->setBold(true);
			$sheetIndex->getStyle("D$k")->getNumberFormat()->setFormatCode("#,##0");
			$sheetIndex->getStyle("F$k")->getNumberFormat()->setFormatCode("#,##0");
			$sheetIndex->getRowDimension($k)->setRowHeight(27);

			$k += 1;
		}

		if($DISCOUNT_PRICE != 0) { 

			$sheetIndex->setCellValue("A".$k, iconv("EUC-KR", "UTF-8", "매출할인"))
			->setCellValue("B".$k, "")
			->setCellValue("C".$k, iconv("EUC-KR", "UTF-8", "-1"))
			->setCellValue("D".$k, iconv("EUC-KR", "UTF-8", -1 * $DISCOUNT_PRICE))
			->setCellValue("F".$k, iconv("EUC-KR", "UTF-8", -1 * $DISCOUNT_PRICE))
			->setCellValue("G".$k, iconv("EUC-KR", "UTF-8", $STR_TAX_TF));
			$sheetIndex->mergeCells("D$k:E$k");
			$sheetIndex->getStyle("A$k")->getAlignment()->setWrapText(true);
			$sheetIndex->getStyle("A$k:G$k")->getFont()->setSize(9)->setBold(true);
			$sheetIndex->getStyle("D$k")->getNumberFormat()->setFormatCode("#,##0");
			$sheetIndex->getStyle("F$k")->getNumberFormat()->setFormatCode("#,##0");
			$sheetIndex->getRowDimension($k)->setRowHeight(27);

			$k += 1;
		}

		
	}

	while($k < 23) { 

		$sheetIndex->mergeCells("D$k:E$k");
		$sheetIndex->getRowDimension($k)->setRowHeight(27);
	
		$k += 1;
	}

	//26열 
	$sheetIndex->setCellValue("A$k", iconv("EUC-KR", "UTF-8","합계액"))
	->setCellValue("G$k", number_format($TOTAL_SALE_PRICE));
	$sheetIndex->mergeCells("A$k:E$k");
	$sheetIndex->getStyle("A$k:F$k")->getFont()->setSize(14);
	$sheetIndex->getStyle("G$k")->getFont()->setSize(9)->setBold(true);
	$sheetIndex->getStyle("G$k")->getNumberFormat()->setFormatCode("#,##0");
	$sheetIndex->getRowDimension($k)->setRowHeight(27);

	$sheetIndex->getStyle("A9:G".($k-1))->applyFromArray($BStyle);
	$sheetIndex->getStyle("A$k:G$k")->applyFromArray($BStyle_outline);
	$sheetIndex->getStyle("A1:G$k")->applyFromArray($BStyle_font);
	
	
	// Rename sheet
	$sheetIndex->setTitle(iconv("EUC-KR", "UTF-8","견적서"));

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
	$filename = "견적서1-".$SENDER_NM."-".$reserve_no."-".date("Ymd");

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
				
