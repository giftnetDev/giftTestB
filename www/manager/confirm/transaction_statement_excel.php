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

	$print_date_month = date("n", strtotime("0 day"));
	$print_date_day = date("j", strtotime("0 day"));


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
	
	$arr_cp = selectCompany($conn, $rs_cp_no);

	$IS_MALL		 = $arr_cp[0]["IS_MALL"];

	//$cp_name=GetCompanyNameWithReserveNo($conn,$reserve_no);
	


	if($IS_MALL == "Y") { 

		//?????? ?????? ?????? ?????? ???? 2017-07-03
		$SENDER_NM      = $rs_o_mem_nm." ".$arr_cp[0]["CP_NM"];
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

		//???? ???? ?? ????
		if($QTY == 0)
			continue;
		
		//???????? ???? 2017-11-16
		if($CATE_04 <> '')
			continue;

		//?????? 0 ???? 2018-11-14
		if($SALE_PRICE == 0)
			continue;

		//????,????,???? ???? 2018-11-15
		if($CATE_01 <> '')
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

	$fColor = new PHPExcel_Style_Color();
	$fColor->setRGB("000000");

	$BStyle = array(
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

	$BStyle_middle = array(
	  "borders" => array(
		"allborders" => array(
		  "style" => PHPExcel_Style_Border::BORDER_MEDIUM,
          "color" => array("rgb" => "953735")
		)
	  )
	);

	$BStyle_outline = array(
	  "borders" => array(
		"outline" => array(
		  "style" => PHPExcel_Style_Border::BORDER_MEDIUM,
          "color" => array("rgb" => "953735")
		)
	  )
	);

	$sheetIndex->getRowDimension(1)->setRowHeight(14.25);
	$sheetIndex->mergeCells("A1:AF1");
	//2-3??
	$sheetIndex->setCellValue("A2",iconv("EUC-KR", "UTF-8","?? ?? ?? ?? ?? (?????????? ??????)"));
	$sheetIndex->mergeCells("A2:AF3");
	$sheetIndex->getRowDimension(2)->setRowHeight(14.25);	
	
	//4-5?? 
	$sheetIndex->setCellValue("A4", iconv("EUC-KR", "UTF-8","??????????"));
	$sheetIndex->mergeCells("A4:A11");
	$sheetIndex->setCellValue("B4", iconv("EUC-KR", "UTF-8","?? ?? (??????)"));
	$sheetIndex->mergeCells("B4:D5");
	$sheetIndex->setCellValue("E4", iconv("EUC-KR", "UTF-8", $SENDER_NM/*." ".$cp_name*/));
	$sheetIndex->mergeCells("E4:N5");
	$sheetIndex->setCellValue("O4", iconv("EUC-KR", "UTF-8","??????"));
	$sheetIndex->mergeCells("O4:O11");
	$sheetIndex->setCellValue("P4", iconv("EUC-KR", "UTF-8","????????"));
	$sheetIndex->mergeCells("P4:S5");
	$sheetIndex->setCellValue("T4", iconv("EUC-KR", "UTF-8", $BIZ_NO));
	$sheetIndex->mergeCells("T4:AF5");

	//6-7?? 
	$sheetIndex->setCellValue("B6", iconv("EUC-KR", "UTF-8","??????        ?? ??"));
	$sheetIndex->mergeCells("B6:D7");
	$sheetIndex->setCellValue("E6", iconv("EUC-KR", "UTF-8", $SENDER_ADDR));
	$sheetIndex->mergeCells("E6:N7");
	$sheetIndex->setCellValue("P6", iconv("EUC-KR", "UTF-8","?? ??    (??????)"));
	$sheetIndex->mergeCells("P6:S7");
	$sheetIndex->setCellValue("T6", iconv("EUC-KR", "UTF-8", $OP_CP_NM));
	$sheetIndex->mergeCells("T6:Y7");
	$sheetIndex->setCellValue("Z6", iconv("EUC-KR", "UTF-8","?? ??"));
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
	
	//8-9?? 
	$sheetIndex->setCellValue("B8", iconv("EUC-KR", "UTF-8","????????"));
	$sheetIndex->mergeCells("B8:D9");
	$sheetIndex->setCellValue("E8", iconv("EUC-KR", "UTF-8", $SENDER_CP_PHONE));
	$sheetIndex->mergeCells("E8:N9");
	$sheetIndex->setCellValue("P8", iconv("EUC-KR", "UTF-8","??????   ?? ??"));
	$sheetIndex->mergeCells("P8:S9");
	$sheetIndex->setCellValue("T8", iconv("EUC-KR", "UTF-8", $CP_ADDR));
	$sheetIndex->mergeCells("T8:AF9");
	
	//10-11?? 
	$sheetIndex->setCellValue("B10", iconv("EUC-KR", "UTF-8","???????? (VAT????)"));
	$sheetIndex->mergeCells("B10:D11");
	$sheetIndex->setCellValue("E10", iconv("EUC-KR", "UTF-8", number_format($TOTAL_SALE_PRICE)));
	$sheetIndex->mergeCells("E10:N11");
	$sheetIndex->setCellValue("P10", iconv("EUC-KR", "UTF-8","?? ??"));
	$sheetIndex->mergeCells("P10:S11");
	$sheetIndex->setCellValue("T10", iconv("EUC-KR", "UTF-8", $CP_PHONE));
	$sheetIndex->mergeCells("T10:Y11");
	$sheetIndex->setCellValue("Z10", iconv("EUC-KR", "UTF-8","?? ??"));
	$sheetIndex->mergeCells("Z10:AA11");
	$sheetIndex->setCellValue("AB10", iconv("EUC-KR", "UTF-8", $CP_FAX));
	$sheetIndex->mergeCells("AB10:AF11");

	$sheetIndex->getStyle("E10:N11")->getFont()->setBold(true);
	
	//12??
	//??	??	??               ??								????					????				????				????????					????			
	$sheetIndex->setCellValue("A12", iconv("EUC-KR", "UTF-8","??"));
	$sheetIndex->setCellValue("B12", iconv("EUC-KR", "UTF-8","??"));
	$sheetIndex->setCellValue("C12", iconv("EUC-KR", "UTF-8","??    ??"));
	$sheetIndex->mergeCells("C12:J12");
	$sheetIndex->setCellValue("K12", iconv("EUC-KR", "UTF-8","????"));
	$sheetIndex->mergeCells("K12:O12");
	$sheetIndex->setCellValue("P12", iconv("EUC-KR", "UTF-8","????"));
	$sheetIndex->mergeCells("P12:S12");
	$sheetIndex->setCellValue("T12", iconv("EUC-KR", "UTF-8","????"));
	$sheetIndex->mergeCells("T12:W12");
	$sheetIndex->setCellValue("X12", iconv("EUC-KR", "UTF-8","????????"));
	$sheetIndex->mergeCells("X12:AB12");
	$sheetIndex->setCellValue("AC12", iconv("EUC-KR", "UTF-8","????"));
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

		//???? ???? ?? ????
		if($QTY == 0)
			continue;
		
		//???????? ???? 2017-11-16
		if($CATE_04 <> '')
			continue;

		//?????? 0 ???? 2018-11-14
		if($SALE_PRICE == 0)
			continue;

		//????,????,???? ???? 2018-11-15
		if($CATE_01 <> '')
			continue;

		if($ORDER_STATE == "1" || $ORDER_STATE == "2" || $ORDER_STATE == "3") {  

			if($TAX_TF == "????") { 
				$arr_tax = get_tax($SALE_PRICE * $QTY, "G");
				$supply = $arr_tax[0];
				$tax = $arr_tax[1];
			} else {
				$supply = $SALE_PRICE * $QTY;
				$tax = 0;
			}

			//13??
			//8	10	????????????????????4??B????													40 				5,000 				181,818 					18,182 			
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

				if($TAX_TF == "????") { 
					$arr_tax = get_tax($DISCOUNT_PRICE, "G");
					$supply = $arr_tax[0];
					$tax = $arr_tax[1];
				} else {
					$supply = $DISCOUNT_PRICE;
					$tax = 0;
				}

				$sheetIndex->setCellValue("A".$k, iconv("EUC-KR", "UTF-8", $print_date_month));
				$sheetIndex->setCellValue("B".$k, iconv("EUC-KR", "UTF-8", $print_date_day));
				$sheetIndex->setCellValue("C".$k, iconv("EUC-KR", "UTF-8", "????????"));
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

		//???? ???????? ???????? ???? ???? ????
	//???? ?????? ???? ???? ?????? ?????? ???????? ???? ???? ?????? ??
	$sheetIndex->mergeCells("C$k:J$k");
	$sheetIndex->mergeCells("K$k:O$k");
	$sheetIndex->mergeCells("P$k:S$k");
	$sheetIndex->mergeCells("T$k:W$k");
	$sheetIndex->mergeCells("X$k:AB$k");
	$sheetIndex->mergeCells("AC$k:AF$k");
	$sheetIndex->getRowDimension($k)->setRowHeight(22.50);
	$k += 1;

	$sheetIndex->mergeCells("C$k:J$k");
	$sheetIndex->mergeCells("K$k:W$k");
	$sheetIndex->mergeCells("X$k:AB$k");
	$sheetIndex->mergeCells("AC$k:AF$k");
	$sheetIndex->getRowDimension($k)->setRowHeight(22.50);
	$sheetIndex->setCellValue("K".$k, iconv("EUC-KR", "UTF-8","???? / 105-01-279041 / (??)????????"));
	$sheetIndex->getStyle("K$k:W$k")->getFont()->setBold(true);
	$k += 1;
	//???? ???????? ???????? ???? ???? ????
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
	//29-30??
	$sheetIndex->setCellValue("A$k", iconv("EUC-KR", "UTF-8","?? ?? ??"));
	$sheetIndex->mergeCells("A$k:E$l");
	$sheetIndex->mergeCells("F$k:H$l");
	$sheetIndex->setCellValue("I$k", iconv("EUC-KR", "UTF-8","??"));
	$sheetIndex->mergeCells("I$k:I$l");
	$sheetIndex->setCellValue("J$k", iconv("EUC-KR", "UTF-8","?? ?? ??"));
	$sheetIndex->mergeCells("J$k:N$l");
	$sheetIndex->mergeCells("O$k:S$l");
	$sheetIndex->setCellValue("T$k", iconv("EUC-KR", "UTF-8","??"));
	$sheetIndex->mergeCells("T$k:T$l");
	$sheetIndex->setCellValue("U$k", iconv("EUC-KR", "UTF-8","?? ?? ??"));
	$sheetIndex->mergeCells("U$k:Y$l");
	$sheetIndex->mergeCells("Z$k:AF$l");

	$sheetIndex->getStyle("A2:AF$l")->applyFromArray($BStyle);
	$sheetIndex->getStyle("A2:AF3")->applyFromArray($BStyle_middle);
	$sheetIndex->getStyle("E4:N9")->applyFromArray($BStyle_middle);
	$sheetIndex->getStyle("A12:AF".($k-1))->applyFromArray($BStyle_middle);
	$sheetIndex->getStyle("A2:AF$l")->applyFromArray($BStyle_outline);
	
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
	$sheetIndex->setTitle(iconv("EUC-KR", "UTF-8","??????????"));

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	// ?????? ?????????? utf-8?? ???? ???????? ?????? ???????? euc-kr?? ??????????.
	$filename = "??????????-".$SENDER_NM."-".$reserve_no."-".date("Ymd");

	// Redirect output to a client??s web browser (Excel5)
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment;filename=".$filename.".xls");
	header("Cache-Control: max-age=0");

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
	$objWriter->setUseDiskCaching(true);
	$objWriter->save("php://output");

	mysql_close($conn);
	exit;


?>
				
