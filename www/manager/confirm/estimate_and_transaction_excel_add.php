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

	function getCpCateFromCpNo($db, $cpNo){
		$query="SELECT CP_CATE
				FROM TBL_COMPANY
				WHERE CP_NO='".$cpNo."'
				";
		// echo $query."<br>";

		$result= mysql_query($query, $db);
		if($$result<>""){
			$rows=mysql_fetch_row($result);
			if($rows[0]==""){
				return -1;
			}
			else{
				return $rows[0];
			}
		}
	}

	$reserve_no				= trim(base64url_decode($reserve_no));
	$op_cp_no				= trim(base64url_decode($op_cp_no));
	
	$print_date_month = date("n", strtotime("0 day"));
	$print_date_day = date("j", strtotime("0 day"));
	
	// echo "op_cp_no : ".$op_cp_no."<br>";
	// exit;

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

	$cpCate=getCpCateFromCpNo($conn,$rs_cp_no);


	// echo "rs_cp_no : ".$rs_cp_no."<br>";
	// exit;
	
	$rs_order_date = date("Y년 n월 j일", strtotime($rs_order_date));
	
	$arr_cp = selectCompany($conn, $rs_cp_no);
	$IS_MALL		= $arr_cp[0]["IS_MALL"];
	$SENDER_NM      = $arr_cp[0]["CP_NM"]." ".$arr_cp[0]["CP_NM2"];
	$CP_CATE		= $arr_cp[0]["CP_CATE"];

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
	$CEO_NM			= $arr_op_cp[0]["CEO_NM"];

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
		//증정,샘플 제외 20210518
		if($CATE_01 <> '' && $CATE_01 <> '추가')
			continue;

		$arr_rs[$j]["QTY"] = $QTY;

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

	$sheetIndex->getPageSetup()->setFitToPage(1);

	//////////////////// 견적서 //////////////////////////////
	$sheetIndex = $objPHPExcel->setActiveSheetIndex(0);
	//실 엑셀과 0.71~0.72 차이남
	$sheetIndex->getColumnDimension("A")->setWidth(23.85);
	$sheetIndex->getColumnDimension("B")->setWidth(9);
	$sheetIndex->getColumnDimension("C")->setWidth(8.85);
	$sheetIndex->getColumnDimension("D")->setWidth(10.42);
	$sheetIndex->getColumnDimension("E")->setWidth(3.71);
	$sheetIndex->getColumnDimension("F")->setWidth(16.42);
	$sheetIndex->getColumnDimension("G")->setWidth(16.14);

	$BStyle_font = array(
	  "font"  => array(
        "name"  => "Gulim")
	);

	$BStyle_esti = array(
	  "borders" => array(
		"allborders" => array(
		  "style" => PHPExcel_Style_Border::BORDER_THIN
		)
	  )
	);

	$BStyle_outline_esti = array(
	  'borders' => array(
		'outline' => array(
		  'style' => PHPExcel_Style_Border::BORDER_THIN
		)
	  )
	);

	$style_esti = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );

    $sheetIndex->getDefaultStyle()->applyFromArray($style_esti);
	$sheetIndex->getRowDimension(1)->setRowHeight(14.25);

	//2열
	$sheetIndex->setCellValue("A2",iconv("EUC-KR", "UTF-8","견     적    서"));
	$sheetIndex->mergeCells("A2:G2");
	$sheetIndex->getStyle("A2:G2")->getFont()->setSize(28)->setBold(true);
	$sheetIndex->getRowDimension(2)->setRowHeight(97.50);


	//3열 
	$sheetIndex->setCellValue("E3", iconv("EUC-KR", "UTF-8","공 급 자"))
	->setCellValue("F3", iconv("EUC-KR", "UTF-8","등 록 번 호"))
	->setCellValue("G3", iconv("EUC-KR", "UTF-8", $BIZ_NO));
	$sheetIndex->mergeCells("E3:E7");
	$sheetIndex->getStyle("E3:E7")->getAlignment()->setWrapText(true);
	$sheetIndex->getRowDimension(3)->setRowHeight(29.25);

	//4열 
	$sheetIndex->setCellValue("A4", iconv("EUC-KR", "UTF-8", $rs_order_date))
	->setCellValue("F4", iconv("EUC-KR", "UTF-8","상 호    (법인명)"))
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
	$sheetIndex->getStyle("E3:G7")->applyFromArray($BStyle_esti);
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
	->setCellValue("G9", iconv("EUC-KR", "UTF-8","세  액"));
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
		//증정,샘플 제외 20210518
		if($CATE_01 <> '' && $CATE_01 <> '추가')
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

	$sheetIndex->getStyle("A9:G".($k-1))->applyFromArray($BStyle_esti);
	$sheetIndex->getStyle("A$k:G$k")->applyFromArray($BStyle_outline_esti);
	$sheetIndex->getStyle("A1:G$k")->applyFromArray($BStyle_font);
	

	// Rename sheet
	$sheetIndex->setTitle(iconv("EUC-KR", "UTF-8","견적서"));

	//////////////////// 거래명세서 //////////////////////////////

	$objPHPExcel->createSheet();
	$sheetIndex = $objPHPExcel->setActiveSheetIndex(1);

	$fColor = new PHPExcel_Style_Color();
	$fColor->setRGB("000000");

	$BStyle_tran = array(
	  "borders" => array(
		"allborders" => array(
		  "style" => PHPExcel_Style_Border::BORDER_THIN,
          "color" => array("rgb" => "953735")
		)
	  ),
	  "font"  => array(
        "color" => array("rgb" => "953735"),
        "size"  => 9,
        "name"  => "Gulim")
	);

	$BStyle_middle_tran = array(
	  "borders" => array(
		"allborders" => array(
		  "style" => PHPExcel_Style_Border::BORDER_MEDIUM,
          "color" => array("rgb" => "953735")
		)
	  )
	);

	$BStyle_outline_tran = array(
	  "borders" => array(
		"outline" => array(
		  "style" => PHPExcel_Style_Border::BORDER_MEDIUM,
          "color" => array("rgb" => "953735")
		)
	  )
	);

	$sheetIndex->getRowDimension(1)->setRowHeight(14.25);
	$sheetIndex->mergeCells("A1:AF1");

	//2-3열
	$sheetIndex->setCellValue("A2",iconv("EUC-KR", "UTF-8","거 래 명 세 표 (공급받는자 보관용)"));
	$sheetIndex->mergeCells("A2:AF3");
	$sheetIndex->getRowDimension(2)->setRowHeight(14.25);	
	
	//4-5열 
	$sheetIndex->setCellValue("A4", iconv("EUC-KR", "UTF-8","공급받는자"));
	$sheetIndex->mergeCells("A4:A11");
	$sheetIndex->setCellValue("B4", iconv("EUC-KR", "UTF-8","상 호 (법인명)"));
	$sheetIndex->mergeCells("B4:D5");
	$sheetIndex->setCellValue("E4", iconv("EUC-KR", "UTF-8", $SENDER_NM));
	$sheetIndex->mergeCells("E4:N5");
	$sheetIndex->setCellValue("O4", iconv("EUC-KR", "UTF-8","공급자"));
	$sheetIndex->mergeCells("O4:O11");
	$sheetIndex->setCellValue("P4", iconv("EUC-KR", "UTF-8","등록번호"));
	$sheetIndex->mergeCells("P4:S5");
	$sheetIndex->setCellValue("T4", iconv("EUC-KR", "UTF-8", $BIZ_NO));
	$sheetIndex->mergeCells("T4:AF5");

	//6-7열 
	$sheetIndex->setCellValue("B6", iconv("EUC-KR", "UTF-8","사업장        주 소"));
	$sheetIndex->mergeCells("B6:D7");
	$sheetIndex->setCellValue("E6", iconv("EUC-KR", "UTF-8", $SENDER_ADDR));
	$sheetIndex->mergeCells("E6:N7");
	$sheetIndex->setCellValue("P6", iconv("EUC-KR", "UTF-8","상 호 (법인명)"));
	$sheetIndex->mergeCells("P6:S7");
	$sheetIndex->setCellValue("T6", iconv("EUC-KR", "UTF-8", $OP_CP_NM));
	$sheetIndex->mergeCells("T6:Y7");
	$sheetIndex->setCellValue("Z6", iconv("EUC-KR", "UTF-8","성 명"));
	$sheetIndex->mergeCells("Z6:AA7");
	$sheetIndex->setCellValue("AB6", iconv("EUC-KR", "UTF-8", $CEO_NM));
	$sheetIndex->mergeCells("AB6:AF7");

	///www/upload_data/operating_image/giftnet_stamp.jpg
	$objDrawing = new PHPExcel_Worksheet_Drawing();

	$img_path = $_SERVER["DOCUMENT_ROOT"]."/upload_data/operating_image/giftnet_stamp.png";
	$objDrawing->setPath($img_path);
	$objDrawing->setCoordinates("AD6");
	$objDrawing->setResizeProportional(true);
	$objDrawing->setWidth(60);
	$objDrawing->setWorksheet($sheetIndex);
	$objDrawing->setOffsetX(-5);
	$objDrawing->setOffsetY(-10);
	
	//8-9열 
	$sheetIndex->setCellValue("B8", iconv("EUC-KR", "UTF-8","전화번호"));
	$sheetIndex->mergeCells("B8:D9");
	$sheetIndex->setCellValue("E8", iconv("EUC-KR", "UTF-8", $SENDER_CP_PHONE));
	$sheetIndex->mergeCells("E8:N9");
	$sheetIndex->setCellValue("P8", iconv("EUC-KR", "UTF-8","사업장   주 소"));
	$sheetIndex->mergeCells("P8:S9");
	$sheetIndex->setCellValue("T8", iconv("EUC-KR", "UTF-8", $CP_ADDR));
	$sheetIndex->mergeCells("T8:AF9");
	
	//10-11열 
	$sheetIndex->setCellValue("B10", iconv("EUC-KR", "UTF-8","합계금액 (VAT포함)"));
	$sheetIndex->mergeCells("B10:D11");
	$sheetIndex->setCellValue("E10", iconv("EUC-KR", "UTF-8", number_format($TOTAL_SALE_PRICE)));
	$sheetIndex->mergeCells("E10:N11");
	$sheetIndex->setCellValue("P10", iconv("EUC-KR", "UTF-8","전 화"));
	$sheetIndex->mergeCells("P10:S11");
	$sheetIndex->setCellValue("T10", iconv("EUC-KR", "UTF-8", $CP_PHONE));
	$sheetIndex->mergeCells("T10:Y11");
	$sheetIndex->setCellValue("Z10", iconv("EUC-KR", "UTF-8","팩 스"));
	$sheetIndex->mergeCells("Z10:AA11");
	$sheetIndex->setCellValue("AB10", iconv("EUC-KR", "UTF-8", $CP_FAX));
	$sheetIndex->mergeCells("AB10:AF11");

	$sheetIndex->getStyle("E10:N11")->getFont()->setBold(true);
	
	//12열
	//월	일	품 목		규격		수량	  단가	공급가액		세액			
	$sheetIndex->setCellValue("A12", iconv("EUC-KR", "UTF-8","월"));
	$sheetIndex->setCellValue("B12", iconv("EUC-KR", "UTF-8","일"));
	$sheetIndex->setCellValue("C12", iconv("EUC-KR", "UTF-8","품    목"));
	$sheetIndex->mergeCells("C12:J12");
	$sheetIndex->setCellValue("K12", iconv("EUC-KR", "UTF-8","규격"));
	$sheetIndex->mergeCells("K12:O12");
	$sheetIndex->setCellValue("P12", iconv("EUC-KR", "UTF-8","수량"));
	$sheetIndex->mergeCells("P12:S12");
	$sheetIndex->setCellValue("T12", iconv("EUC-KR", "UTF-8","단가"));
	$sheetIndex->mergeCells("T12:W12");
	$sheetIndex->setCellValue("X12", iconv("EUC-KR", "UTF-8","공급가액"));
	$sheetIndex->mergeCells("X12:AB12");
	$sheetIndex->setCellValue("AC12", iconv("EUC-KR", "UTF-8","세액"));
	$sheetIndex->mergeCells("AC12:AF12");


	$k  = 13;

	for ($j = 0 ; $j < sizeof($arr_rs) ; $j++) {
	
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
		$TAX_TF						= trim($arr_rs[$j]["TAX_TF"]);

		$CATE_01					= trim($arr_rs[$j]["CATE_01"]);
		$CATE_04					= trim($arr_rs[$j]["CATE_04"]);

		$ORDER_STATE				= trim($arr_rs[$j]["ORDER_STATE"]);

		$QTY = getRefundAbleQty_EstimateTransaction($conn, $reserve_no, $ORDER_GOODS_NO);

		if($QTY == "0") continue;

		//교환건은 제외 2017-11-16
		if($CATE_04 <> '')
			continue;

		//판매가 0 제외 2018-11-14
		if($SALE_PRICE == 0)
			continue;

		//증정,샘플,추가 제외 2018-11-15
		//증정,샘플 제외 20210518
		if($CATE_01 <> '' && $CATE_01 <> '추가')
			continue;
	
		if($ORDER_STATE == "1" || $ORDER_STATE == "2" || $ORDER_STATE == "3") {  

			if($TAX_TF == "과세") { 
				$arr_tax = get_tax($SALE_PRICE * $QTY, "G");
				$supply = $arr_tax[0];
				$tax = $arr_tax[1];
			} else {
				$supply = $SALE_PRICE * $QTY;
				$tax = 0;
			}

			//13열
			//8	10	자연을담다베이킹소다4종B세트    	40 		5,000 	181,818  18,182 			
			$sheetIndex->setCellValue("A".$k, iconv("EUC-KR", "UTF-8", $print_date_month));
			$sheetIndex->setCellValue("B".$k, iconv("EUC-KR", "UTF-8", $print_date_day));
			$sheetIndex->setCellValue("C".$k, iconv("EUC-KR", "UTF-8", ($CATE_01 <> "" ? $CATE_01.") " : "").$GOODS_NAME));
			$sheetIndex->mergeCells("C$k:J$k");
			$sheetIndex->setCellValue("K".$k, iconv("EUC-KR", "UTF-8", $GOODS_SUB_NAME));
			$sheetIndex->mergeCells("K$k:O$k");
			$sheetIndex->setCellValue("P".$k, iconv("EUC-KR", "UTF-8", $QTY));
			$sheetIndex->mergeCells("P$k:S$k");
			$sheetIndex->setCellValue("T".$k, iconv("EUC-KR", "UTF-8", number_format($SALE_PRICE)));
			$sheetIndex->mergeCells("T$k:W$k");
			$sheetIndex->setCellValue("X".$k, iconv("EUC-KR", "UTF-8", number_format($supply)));
			$sheetIndex->mergeCells("X$k:AB$k");
			$sheetIndex->setCellValue("AC".$k, iconv("EUC-KR", "UTF-8", number_format($tax)));
			$sheetIndex->mergeCells("AC$k:AF$k");
			$sheetIndex->getRowDimension($k)->setRowHeight(22.50);

			$k += 1;

			if($DISCOUNT_PRICE != 0) { 

				if($TAX_TF == "과세") { 
					$arr_tax = get_tax($DISCOUNT_PRICE, "G");
					$supply = $arr_tax[0];
					$tax = $arr_tax[1];
				} else {
					$supply = $DISCOUNT_PRICE;
					$tax = 0;
				}

				$sheetIndex->setCellValue("A".$k, iconv("EUC-KR", "UTF-8", $print_date_month));
				$sheetIndex->setCellValue("B".$k, iconv("EUC-KR", "UTF-8", $print_date_day));
				$sheetIndex->setCellValue("C".$k, iconv("EUC-KR", "UTF-8", "매출할인"));
				$sheetIndex->mergeCells("C$k:J$k");
				$sheetIndex->setCellValue("K".$k, "");
				$sheetIndex->mergeCells("K$k:O$k");
				$sheetIndex->setCellValue("P".$k, iconv("EUC-KR", "UTF-8", -1));
				$sheetIndex->mergeCells("P$k:S$k");
				$sheetIndex->setCellValue("T".$k, iconv("EUC-KR", "UTF-8", number_format($DISCOUNT_PRICE)));
				$sheetIndex->mergeCells("T$k:W$k");
				$sheetIndex->setCellValue("X".$k, iconv("EUC-KR", "UTF-8", number_format($supply)));
				$sheetIndex->mergeCells("X$k:AB$k");
				$sheetIndex->setCellValue("AC".$k, iconv("EUC-KR", "UTF-8", number_format($tax)));
				$sheetIndex->mergeCells("AC$k:AF$k");
				$sheetIndex->getRowDimension($k)->setRowHeight(22.50);

				$k += 1;

			}
		}
	}

	while($k < 29) { 

		$sheetIndex->mergeCells("C$k:J$k");
		$sheetIndex->mergeCells("K$k:O$k");
		$sheetIndex->mergeCells("P$k:S$k");
		$sheetIndex->mergeCells("T$k:W$k");
		$sheetIndex->mergeCells("X$k:AB$k");
		$sheetIndex->mergeCells("AC$k:AF$k");
		$sheetIndex->getRowDimension($k)->setRowHeight(22.50);
	
		$k += 1;
	}

	$l = $k + 1;
	//29-30열
	$sheetIndex->setCellValue("A$k", iconv("EUC-KR", "UTF-8","인 수 자"));
	$sheetIndex->mergeCells("A$k:E$l");
	$sheetIndex->mergeCells("F$k:H$l");
	$sheetIndex->setCellValue("I$k", iconv("EUC-KR", "UTF-8","인"));
	$sheetIndex->mergeCells("I$k:I$l");
	$sheetIndex->setCellValue("J$k", iconv("EUC-KR", "UTF-8","납 품 자"));
	$sheetIndex->mergeCells("J$k:N$l");
	$sheetIndex->mergeCells("O$k:S$l");
	$sheetIndex->setCellValue("T$k", iconv("EUC-KR", "UTF-8","인"));
	$sheetIndex->mergeCells("T$k:T$l");
	$sheetIndex->setCellValue("U$k", iconv("EUC-KR", "UTF-8","인 수 자"));
	$sheetIndex->mergeCells("U$k:Y$l");
	$sheetIndex->mergeCells("Z$k:AF$l");

	$sheetIndex->getStyle("A2:AF$l")->applyFromArray($BStyle_tran);
	$sheetIndex->getStyle("A2:AF3")->applyFromArray($BStyle_middle_tran);
	$sheetIndex->getStyle("E4:N9")->applyFromArray($BStyle_middle_tran);
	$sheetIndex->getStyle("A12:AF".($k-1))->applyFromArray($BStyle_middle_tran);
	$sheetIndex->getStyle("A2:AF$l")->applyFromArray($BStyle_outline_tran);
	
	$sheetIndex->getStyle("A2:AF$l")->getAlignment()->setWrapText(true)
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	
	$sheetIndex->getStyle("A2:AF3")->getFont()->setName("Gulim")->setSize(20)->setBold(true);



	$sheetIndex->getStyle("A4:AF$l")->getFont()->setName("Gulim")->setSize(9);
	$sheetIndex->getStyle("A13:AF28")->getFont()->setColor($fColor);
	$sheetIndex->getStyle("E4:N9")->getFont()->setName("Gulim")->setSize(10)->setColor($fColor);
	$sheetIndex->getStyle("E10:N11")->getFont()->setName("Gulim")->setSize(12)->setColor($fColor);
	$sheetIndex->getStyle("T4:AF5")->getFont()->setName("Gulim")->setSize(10)->setColor($fColor);
	$sheetIndex->getStyle("T6:Y7")->getFont()->setName("Gulim")->setSize(10)->setColor($fColor);
	$sheetIndex->getStyle("AB6:AF7")->getFont()->setName("Gulim")->setSize(10)->setColor($fColor);
	$sheetIndex->getStyle("T8:AF9")->getFont()->setName("Gulim")->setSize(10)->setColor($fColor);
	$sheetIndex->getStyle("T10:Y11")->getFont()->setName("Gulim")->setSize(10)->setColor($fColor);
	$sheetIndex->getStyle("AB10:AF11")->getFont()->setName("Gulim")->setSize(10)->setColor($fColor);

	$sheetIndex->getColumnDimension("A")->setWidth(2.71);
	$sheetIndex->getColumnDimension("B")->setWidth(2.71);
	$sheetIndex->getColumnDimension("C")->setWidth(2.71);
	$sheetIndex->getColumnDimension("D")->setWidth(2.71);
	$sheetIndex->getColumnDimension("E")->setWidth(2.71);
	$sheetIndex->getColumnDimension("F")->setWidth(2.71);
	$sheetIndex->getColumnDimension("G")->setWidth(2.71);
	$sheetIndex->getColumnDimension("H")->setWidth(2.71);
	$sheetIndex->getColumnDimension("I")->setWidth(2.71);
	$sheetIndex->getColumnDimension("J")->setWidth(2.71);
	$sheetIndex->getColumnDimension("K")->setWidth(2.71);
	$sheetIndex->getColumnDimension("L")->setWidth(2.71);
	$sheetIndex->getColumnDimension("M")->setWidth(2.71);
	$sheetIndex->getColumnDimension("N")->setWidth(2.71);
	$sheetIndex->getColumnDimension("O")->setWidth(2.71);
	$sheetIndex->getColumnDimension("P")->setWidth(2.71);
	$sheetIndex->getColumnDimension("Q")->setWidth(2.71);
	$sheetIndex->getColumnDimension("R")->setWidth(2.71);
	$sheetIndex->getColumnDimension("S")->setWidth(2.71);
	$sheetIndex->getColumnDimension("T")->setWidth(2.71);
	$sheetIndex->getColumnDimension("U")->setWidth(2.71);
	$sheetIndex->getColumnDimension("V")->setWidth(2.71);
	$sheetIndex->getColumnDimension("W")->setWidth(2.71);
	$sheetIndex->getColumnDimension("X")->setWidth(2.71);
	$sheetIndex->getColumnDimension("Y")->setWidth(2.71);
	$sheetIndex->getColumnDimension("Z")->setWidth(2.71);
	$sheetIndex->getColumnDimension("AA")->setWidth(2.71);
	$sheetIndex->getColumnDimension("AB")->setWidth(2.71);
	$sheetIndex->getColumnDimension("AC")->setWidth(2.71);
	$sheetIndex->getColumnDimension("AD")->setWidth(2.71);
	$sheetIndex->getColumnDimension("AE")->setWidth(2.71);
	$sheetIndex->getColumnDimension("AF")->setWidth(2.71);
	
	
	// Rename sheet
	$sheetIndex->setTitle(iconv("EUC-KR", "UTF-8","거래명세서"));

	//////////////////////////////////////////////////////////////////////////////////////////////
	///사업자 등록증
	//////////////////////////////////////////////////////////////////////////////////////////////
	
	$objPHPExcel->createSheet();
	$sheetIndex = $objPHPExcel->setActiveSheetIndex(2);
	
	//www/upload_data/operating_image/giftnet_business_registration.jpg
	$objDrawing = new PHPExcel_Worksheet_Drawing();

	$img_path = $_SERVER["DOCUMENT_ROOT"]."/upload_data/operating_image/giftnet_business_registration.jpg";
	$objDrawing->setPath($img_path);
	$objDrawing->setCoordinates("A1");
	$objDrawing->setResizeProportional(true);
	$objDrawing->setWidth(560);
	$objDrawing->setWorksheet($sheetIndex);

	// Rename sheet
	$sheetIndex->setTitle(iconv("EUC-KR", "UTF-8","사업자등록증"));


	//////////////////////////////////////////////////////////////////////////////////////////////
	///통장사본
	//////////////////////////////////////////////////////////////////////////////////////////////
	
	$objPHPExcel->createSheet();
	$sheetIndex = $objPHPExcel->setActiveSheetIndex(3);
	
	//www/upload_data/operating_image/giftnet_bank_account.png
	$objDrawing = new PHPExcel_Worksheet_Drawing();

	$bank_account_img_file = getDcodeExtByCode($conn, "BANK_ACCOUNT", "DEFAULT");

	//각 은행 벤더사도 은행을 이용해서 각 은행에 일치하지 않으면 그냥 기본(농협) 표기되도록 수정
	if($CP_CATE <> "") { 
		$temp_account_file = getDcodeExtByCode($conn, "BANK_ACCOUNT", $CP_CATE);
		if($temp_account_file <> "")
			$bank_account_img_file = $temp_account_file;
	}
	if($cpCate=="300601"){
		$img_path = $_SERVER["DOCUMENT_ROOT"]."/upload_data/operating_image/통장사본(기업).jpg";//$bank_account_img_file;
	}
	else if($cpCate=="300801"){
		$img_path = $_SERVER["DOCUMENT_ROOT"]."/upload_data/operating_image/통장사본(신한).jpg";//$bank_account_img_file;
	}
	else if($cpCate=="301001"){
		$img_path = $_SERVER["DOCUMENT_ROOT"]."/upload_data/operating_image/통장사본(우리).jpg";//$bank_account_img_file;
	}
	else if($cpCate=="300701"){
		$img_path = $_SERVER["DOCUMENT_ROOT"]."/upload_data/operating_image/통장사본(국민).jpg";//$bank_account_img_file;
	}
	else if($cpCate=="300901"){
		$img_path = $_SERVER["DOCUMENT_ROOT"]."/upload_data/operating_image/통장사본(하나).jpg";//$bank_account_img_file;
	}
	else{
		$img_path = $_SERVER["DOCUMENT_ROOT"]."/upload_data/operating_image/통장사본(우리).jpg";//$bank_account_img_file;
	}
		

	$objDrawing->setPath($img_path);
	$objDrawing->setCoordinates("A1");
	$objDrawing->setResizeProportional(true);
	$objDrawing->setWidth(560);
	$objDrawing->setWorksheet($sheetIndex);

	// Rename sheet
	$sheetIndex->setTitle(iconv("EUC-KR", "UTF-8","통장사본"));

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
	$filename = "견적서_거래명세서_추가-".$SENDER_NM."-".$reserve_no."-".date("Ymd");

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
				
