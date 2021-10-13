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
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/member/member.php";
	require "../../_classes/biz/payment/payment.php";
	require "../../_classes/biz/board/board.php";
	require "../../_classes/biz/company/company.php";

#====================================================================
# Request Parameter
#====================================================================

	$reserve_no				= trim($reserve_no);
	$individual_no			= trim($individual_no);
	$print_type				= trim($print_type);
	$print_date				= trim($print_date);
	$op_cp_no				= trim($op_cp_no);

	$print_date = date("n월 j일", strtotime($print_date));
	
#===============================================================
# Get Search list count
#===============================================================

	if($reserve_no <> "" && $individual_no == "") { 
		$arr_order_rs = selectOrder($conn, $reserve_no);

		$rs_cp_no						= trim($arr_order_rs[0]["CP_NO"]); 
		$rs_order_no					= trim($arr_order_rs[0]["ORDER_NO"]); 
		$rs_reserve_no				    = trim($arr_order_rs[0]["RESERVE_NO"]); 
		$rs_o_mem_nm					= trim($arr_order_rs[0]["O_MEM_NM"]);
		$rs_r_mem_nm					= trim($arr_order_rs[0]["R_MEM_NM"]); 
		$rs_r_zipcode					= trim($arr_order_rs[0]["R_ZIPCODE"]); 
		$rs_r_addr1						= trim($arr_order_rs[0]["R_ADDR1"]); 
		$rs_r_phone						= trim($arr_order_rs[0]["R_PHONE"]); 
		$rs_r_hphone					= trim($arr_order_rs[0]["R_HPHONE"]); 
		$rs_memo						= trim($arr_order_rs[0]["MEMO"]); 
		
		$arr_cp = selectCompany($conn, $rs_cp_no);
		$IS_MALL		 = $arr_cp[0]["IS_MALL"];
		$SENDER_NM      = $arr_cp[0]["CP_NM"]." ".$arr_cp[0]["CP_NM2"];

		if($IS_MALL != "Y") { 
			$arr_op_cp = getOperatingCompany($conn, $op_cp_no);

			$OP_CP_NM		= $arr_op_cp[0]["CP_NM"]." ".$arr_op_cp[0]["CP_NM2"];
			$CP_PHONE		= $arr_op_cp[0]["CP_PHONE"];
			$CP_FAX			= $arr_op_cp[0]["CP_FAX"];

		} else {
			
			$CP_PHONE		= $arr_cp[0]["CP_PHONE"];
			$CP_FAX			= $arr_cp[0]["CP_FAX"];

		}

		$arr_rs = listOrderGoodsDeliveryConfirmation($conn, $reserve_no, $print_type);

	} else if($individual_no <> "") { 

		$arr_order_rs = selectOrder($conn, $reserve_no);

		$rs_cp_no						= trim($arr_order_rs[0]["CP_NO"]); 
		$rs_o_mem_nm					= trim($arr_order_rs[0]["O_MEM_NM"]);
		
		$arr_cp = selectCompany($conn, $rs_cp_no);
		$IS_MALL		 = $arr_cp[0]["IS_MALL"];
		$SENDER_NM      = $arr_cp[0]["CP_NM"]." ".$arr_cp[0]["CP_NM2"];

		if($IS_MALL != "Y") { 
			$arr_op_cp = getOperatingCompany($conn, $op_cp_no);

			$OP_CP_NM		= $arr_op_cp[0]["CP_NM"]." ".$arr_op_cp[0]["CP_NM2"];
			$CP_PHONE		= $arr_op_cp[0]["CP_PHONE"];
			$CP_FAX			= $arr_op_cp[0]["CP_FAX"];

		} else {
			
			$CP_PHONE		= $arr_cp[0]["CP_PHONE"];
			$CP_FAX			= $arr_cp[0]["CP_FAX"];

		}

		$arr_rs = selectOrderGoodsDeliveryConfirmation($conn, $individual_no, $print_type);

		$rs_r_mem_nm					= trim($arr_rs[0]["R_MEM_NM"]); 
		$rs_r_zipcode					= trim($arr_rs[0]["R_ZIPCODE"]); 
		$rs_r_addr1						= trim($arr_rs[0]["R_ADDR1"]); 
		$rs_r_phone						= trim($arr_rs[0]["R_PHONE"]); 
		$rs_r_hphone					= trim($arr_rs[0]["R_HPHONE"]); 
		$rs_memo						= trim($arr_rs[0]["MEMO"]);

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
          "color" => array("rgb" => "000000")
		)
	  ),
	  "font"  => array(
        "color" => array("rgb" => "000000"),
        "size"  => 9,
        "name"  => "Gulim")
	);

	//납품확인서

	$sheetIndex->getRowDimension(1)->setRowHeight(30);
	$sheetIndex->mergeCells("A1:F1");

	//2-3열
	if($print_type == "1")
		$sheetIndex->setCellValue("A2",iconv("EUC-KR", "UTF-8","인 수 증"));
	else if($print_type == "2")
		$sheetIndex->setCellValue("A2",iconv("EUC-KR", "UTF-8","납 품 확 인 서"));

	$sheetIndex->mergeCells("A2:F3");
	$sheetIndex->getRowDimension(2)->setRowHeight(30);	

	//4열
	$sheetIndex->getRowDimension(4)->setRowHeight(30);
	$sheetIndex->mergeCells("A4:F4");
	
	//5열 
	$sheetIndex->setCellValue("A5", iconv("EUC-KR", "UTF-8","납품처"));
	$sheetIndex->setCellValue("B5", iconv("EUC-KR", "UTF-8",($IS_MALL == "Y" ? $rs_o_mem_nm : $SENDER_NM)));
	$sheetIndex->setCellValue("C5", iconv("EUC-KR", "UTF-8","받는분"));
	$sheetIndex->setCellValue("D5", iconv("EUC-KR", "UTF-8",$rs_r_mem_nm));
	$sheetIndex->mergeCells("D5:F5");
	$sheetIndex->getRowDimension(5)->setRowHeight(30);

	//6열 
	$sheetIndex->setCellValue("A6", iconv("EUC-KR", "UTF-8","주 소"));
	$sheetIndex->setCellValue("B6", iconv("EUC-KR", "UTF-8", $rs_r_zipcode." ".$rs_r_addr1 ));
	$sheetIndex->mergeCells("B6:F6");
	$sheetIndex->getRowDimension(6)->setRowHeight(30);

	//7열 
	$sheetIndex->setCellValue("A7", iconv("EUC-KR", "UTF-8","전화/받을분"));
	$sheetIndex->setCellValue("B7", iconv("EUC-KR", "UTF-8",$rs_r_phone.($rs_r_hphone != "" ? " (M : ".$rs_r_hphone.")" : "")));
	$sheetIndex->mergeCells("B7:F7");
	$sheetIndex->getRowDimension(7)->setRowHeight(30);

	//8열 
	$sheetIndex->setCellValue("A8", iconv("EUC-KR", "UTF-8","납품일자"));
	$sheetIndex->setCellValue("B8", iconv("EUC-KR", "UTF-8","품명"));
	$sheetIndex->setCellValue("C8", iconv("EUC-KR", "UTF-8","규격"));
	$sheetIndex->setCellValue("D8", iconv("EUC-KR", "UTF-8","수량"));
	$sheetIndex->setCellValue("E8", iconv("EUC-KR", "UTF-8","비고"));
	$sheetIndex->mergeCells("E8:F8");
	$sheetIndex->getRowDimension(8)->setRowHeight(30);

	$k = 9;
	$l = 0;

	if (sizeof($arr_rs) > 0) {
		for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
		
			if($reserve_no <> "" && $individual_no == "") { 
				$ORDER_GOODS_NO				= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
				$GOODS_NAME					= trim($arr_rs[$j]["GOODS_NAME"]);
				$GOODS_SUB_NAME				= trim($arr_rs[$j]["GOODS_SUB_NAME"]);

				if(sizeof($arr_rs) > $j) {
					$refund_able_qty = getRefundAbleQty($conn, $reserve_no, $ORDER_GOODS_NO);
					$sent_date = $print_date;
					$str_box = "BOX";
				} else { 
					$sent_date = "";
					$str_box = "";
				}
			} else if($individual_no <> "") { 

				if(sizeof($arr_rs) > $j) {
					$GOODS_NAME					= trim($arr_rs[0]["GOODS_DELIVERY_NAME"]); 
					$refund_able_qty			= trim($arr_rs[0]["SUB_QTY"]); 
					$sent_date = $print_date;
					$str_box = "BOX";
				} else { 
					$GOODS_NAME = "";
					$sent_date = "";
					$str_box = "";
				}

			}

			if($refund_able_qty == 0) continue;

			$k = 9 + $l;

			$sheetIndex->setCellValue("A".$k, iconv("EUC-KR", "UTF-8", $sent_date));
			$sheetIndex->setCellValue("B".$k, iconv("EUC-KR", "UTF-8", $GOODS_NAME." ".$GOODS_SUB_NAME));
			$sheetIndex->setCellValue("C".$k, iconv("EUC-KR", "UTF-8", ""));
			$sheetIndex->setCellValue("D".$k, iconv("EUC-KR", "UTF-8", $refund_able_qty));
			$sheetIndex->setCellValue("E".$k, iconv("EUC-KR", "UTF-8", ""));
			$sheetIndex->setCellValue("F".$k, iconv("EUC-KR", "UTF-8", $str_box));
			$sheetIndex->getRowDimension($k)->setRowHeight(30);

			$refund_able_qty = "";

			$l ++;
	
		}
	}


	$k ++; 
	// 상품 + 1열
	$sheetIndex->setCellValue("A".$k, iconv("EUC-KR", "UTF-8", ""));
	$sheetIndex->setCellValue("B".$k, iconv("EUC-KR", "UTF-8", ""));
	$sheetIndex->setCellValue("C".$k, iconv("EUC-KR", "UTF-8", ""));
	$sheetIndex->setCellValue("D".$k, iconv("EUC-KR", "UTF-8", "총박스"));
	$sheetIndex->setCellValue("E".$k, iconv("EUC-KR", "UTF-8", ""));
	$sheetIndex->setCellValue("F".$k, iconv("EUC-KR", "UTF-8", "BOX"));
	$sheetIndex->getRowDimension($k)->setRowHeight(30);

	$k ++; 
	// 상품 + 2열
	$sheetIndex->setCellValue("A".$k, iconv("EUC-KR", "UTF-8", "★ ".$rs_memo." ★"));
	$sheetIndex->mergeCells("A".$k.":F".$k);
	$sheetIndex->getRowDimension($k)->setRowHeight(30);	

	$k ++; 
	//끝-4열 
	$sheetIndex->setCellValue("A$k", iconv("EUC-KR", "UTF-8","* 상기 물품을 납품하였음을 확인합니다 *"));
	$sheetIndex->mergeCells("A$k:F$k");
	$sheetIndex->getRowDimension($k)->setRowHeight(30);

	$k ++; 
	//끝-3열  
	$sheetIndex->setCellValue("A$k", iconv("EUC-KR", "UTF-8", ($IS_MALL != "Y" ? $OP_CP_NM : $SENDER_NM)));
	$sheetIndex->mergeCells("A$k:B$k");
	$sheetIndex->setCellValue("C$k", iconv("EUC-KR", "UTF-8",""));
	$sheetIndex->setCellValue("D$k", iconv("EUC-KR", "UTF-8",""));
	$sheetIndex->setCellValue("E$k", iconv("EUC-KR", "UTF-8",""));
	$sheetIndex->mergeCells("C$k:C".($k+2));
	$sheetIndex->mergeCells("D$k:D".($k+2));
	$sheetIndex->mergeCells("E$k:F".($k+2));
	$sheetIndex->getRowDimension($k)->setRowHeight(30);

	$k ++; 
	//끝-2열 
	$sheetIndex->setCellValue("A$k", iconv("EUC-KR", "UTF-8", "T.".$CP_PHONE));
	$sheetIndex->mergeCells("A$k:B$k");
	$sheetIndex->getRowDimension($k)->setRowHeight(30);

	$k ++; 
	//끝-1열 
	$sheetIndex->setCellValue("A$k", iconv("EUC-KR", "UTF-8", "F.".$CP_FAX));
	$sheetIndex->mergeCells("A$k:B$k");
	$sheetIndex->getRowDimension($k)->setRowHeight(30);

	$k ++; 
	//끝열 
	$sheetIndex->setCellValue("A$k", iconv("EUC-KR", "UTF-8", ""));
	$sheetIndex->mergeCells("A$k:B$k");
	$sheetIndex->setCellValue("C$k", iconv("EUC-KR", "UTF-8","납품확인란"));
	$sheetIndex->setCellValue("D$k", iconv("EUC-KR", "UTF-8","기사확인란"));
	$sheetIndex->setCellValue("E$k", iconv("EUC-KR", "UTF-8","수취인확인란"));
	$sheetIndex->mergeCells("E$k:F$k");
	$sheetIndex->getRowDimension($k)->setRowHeight(30);


	$sheetIndex->getStyle("A2:F3")->applyFromArray($BStyle);

	$sheetIndex->getStyle("A5:F$k")->applyFromArray($BStyle);
	
	$sheetIndex->getStyle("A2:F$k")->getAlignment()->setWrapText(true)
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$sheetIndex->getStyle("A".($k-3).":B$k")->getAlignment()->setWrapText(true)
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	
	$sheetIndex->getStyle("A2:F3")->getFont()->setName("Gulim")->setSize(20)->setBold(true);

	$sheetIndex->getColumnDimension("A")->setWidth(17);
	$sheetIndex->getColumnDimension("B")->setWidth(36);
	$sheetIndex->getColumnDimension("C")->setWidth(10);
	$sheetIndex->getColumnDimension("D")->setWidth(10);
	$sheetIndex->getColumnDimension("E")->setWidth(10);
	$sheetIndex->getColumnDimension("F")->setWidth(5);
	
	// Rename sheet
	if($print_type == "1")
		$sheetIndex->setTitle(iconv("EUC-KR", "UTF-8","인수증"));
	else if($print_type == "2")
		$sheetIndex->setTitle(iconv("EUC-KR", "UTF-8","납품확인서"));

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
	if($print_type == "1")
		$filename = "인수증-".date("Ymd");
	else if($print_type == "2")
		$filename = "납품확인서-".date("Ymd");

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