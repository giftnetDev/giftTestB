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
	require "../../_classes/biz/goods/goods.php";

#====================================================================
# Request Parameter
#====================================================================
	if($cp_no <> "")
		$con_cate_03 = trim(base64url_decode($cp_no));
	else
		$con_cate_03 = trim($con_cate_03);

	if($con_cate_03 == "") exit;

	$con_use_tf = 'Y';
	$del_tf = 'N';
	$nPage = 1; 
	$nPageSize = 10000;
	$order_field = "GOODS_NAME";
	$order_str = "ASC";



#===============================================================
# Get Search list count
#===============================================================

	$arr_rs = listGoods($conn, $con_cate, $start_date, $end_date, $start_price, $end_price, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $con_tax_tf, $con_use_tf, $del_tf, $search_field, $search_str, $arr_options, $order_field, $order_str, $nPage, $nPageSize);


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

	$a_idx = 2; //�Ǹ���
	$b_idx = 2; //ǰ��
	$c_idx = 2; //����

	$total_sheet_idx = 0;




	if($type_a == "Y") { 
		////////////////////////////////////////////////////////////////////////////////
		//	�Ǹ���
		////////////////////////////////////////////////////////////////////////////////	
		
		if($total_sheet_idx > 0)
			$objPHPExcel->createSheet();
		
		$sheetIndex = $objPHPExcel->setActiveSheetIndex($total_sheet_idx);
		$total_sheet_idx++;
		
		$sheetIndex->setCellValue("A1", iconv("EUC-KR", "UTF-8","����Ʈ�� ��ǰ�ڵ�"));
		$sheetIndex->setCellValue("B1", iconv("EUC-KR", "UTF-8","��ǰ��"));
		$sheetIndex->setCellValue("C1", iconv("EUC-KR", "UTF-8","��������"));
		$sheetIndex->setCellValue("D1", iconv("EUC-KR", "UTF-8","���԰�(VAT����)"));
		$sheetIndex->setCellValue("E1", iconv("EUC-KR", "UTF-8","�ڽ��Լ�"));
		$sheetIndex->setCellValue("F1", iconv("EUC-KR", "UTF-8","�ù����������"));
		$sheetIndex->setCellValue("G1", iconv("EUC-KR", "UTF-8","�ǸŻ���"));
		$sheetIndex->setCellValue("H1", iconv("EUC-KR", "UTF-8","���"));

		for ($j = 0 ; $j < sizeof($arr_rs) ; $j++) {
			
			$GOODS_CODE				= trim($arr_rs[$j]["GOODS_CODE"]);
			$GOODS_NAME				= trim($arr_rs[$j]["GOODS_NAME"]);
			$GOODS_SUB_NAME			= trim($arr_rs[$j]["GOODS_SUB_NAME"]);
			$TAX_TF					= trim($arr_rs[$j]["TAX_TF"]);
			$BUY_PRICE				= trim($arr_rs[$j]["BUY_PRICE"]);
			$DELIVERY_CNT_IN_BOX	= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);
			$CATE_04				= trim($arr_rs[$j]["CATE_04"]);
			
			if($CATE_04 == "�Ǹ���") { 
				
				$sheetIndex->setCellValue("A".$a_idx, iconv("EUC-KR", "UTF-8", $GOODS_CODE));
				$sheetIndex->setCellValue("B".$a_idx, iconv("EUC-KR", "UTF-8", $GOODS_NAME." ".$GOODS_SUB_NAME));
				$sheetIndex->setCellValue("C".$a_idx, iconv("EUC-KR", "UTF-8", $TAX_TF));
				$sheetIndex->setCellValue("D".$a_idx, iconv("EUC-KR", "UTF-8", number_format($BUY_PRICE)));
				$sheetIndex->setCellValue("E".$a_idx, iconv("EUC-KR", "UTF-8", $DELIVERY_CNT_IN_BOX));
				$sheetIndex->setCellValue("F".$a_idx, "");
				$sheetIndex->setCellValue("G".$a_idx, iconv("EUC-KR", "UTF-8", "�Ǹ���"));
				$sheetIndex->setCellValue("H".$a_idx, "");
				$sheetIndex->getRowDimension($a_idx)->setRowHeight(22.50);
				
				$a_idx ++;
			}
		}

		$sheetIndex->getStyle("A1:H1")->applyFromArray($BStyle_title);
		$sheetIndex->getStyle("A2:H".($a_idx-1))->applyFromArray($BStyle);

		$sheetIndex->getStyle("A1:H1")->getFont()->setBold(true);

		$sheetIndex->getStyle("A1:H".($a_idx-1))->getAlignment()->setWrapText(true)
		->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

		$sheetIndex->getColumnDimension("A")->setWidth(13.80);
		$sheetIndex->getColumnDimension("B")->setWidth(35);
		$sheetIndex->getColumnDimension("C")->setWidth(9.70);
		$sheetIndex->getColumnDimension("D")->setWidth(16);
		$sheetIndex->getColumnDimension("E")->setWidth(9.2);
		$sheetIndex->getColumnDimension("F")->setWidth(14.6);
		$sheetIndex->getColumnDimension("G")->setWidth(9.2);
		$sheetIndex->getColumnDimension("H")->setWidth(30);

		// Rename sheet
		$sheetIndex->setTitle(iconv("EUC-KR", "UTF-8","�Ǹ���"));
	}

	if($type_b == "Y") { 
		////////////////////////////////////////////////////////////////////////////////
		//	ǰ��
		////////////////////////////////////////////////////////////////////////////////
		
		if($total_sheet_idx > 0)
			$objPHPExcel->createSheet();
		
		$sheetIndex = $objPHPExcel->setActiveSheetIndex($total_sheet_idx);
		$total_sheet_idx++;

		$sheetIndex->setCellValue("A1", iconv("EUC-KR", "UTF-8","����Ʈ�� ��ǰ�ڵ�"));
		$sheetIndex->setCellValue("B1", iconv("EUC-KR", "UTF-8","��ǰ��"));
		$sheetIndex->setCellValue("C1", iconv("EUC-KR", "UTF-8","��������"));
		$sheetIndex->setCellValue("D1", iconv("EUC-KR", "UTF-8","���԰�(VAT����)"));
		$sheetIndex->setCellValue("E1", iconv("EUC-KR", "UTF-8","�ڽ��Լ�"));
		$sheetIndex->setCellValue("F1", iconv("EUC-KR", "UTF-8","�ù����������"));
		$sheetIndex->setCellValue("G1", iconv("EUC-KR", "UTF-8","�ǸŻ���"));
		$sheetIndex->setCellValue("H1", iconv("EUC-KR", "UTF-8","���"));

		for ($j = 0 ; $j < sizeof($arr_rs) ; $j++) {
		
			$GOODS_CODE				= trim($arr_rs[$j]["GOODS_CODE"]);
			$GOODS_NAME				= trim($arr_rs[$j]["GOODS_NAME"]);
			$GOODS_SUB_NAME			= trim($arr_rs[$j]["GOODS_SUB_NAME"]);
			$TAX_TF					= trim($arr_rs[$j]["TAX_TF"]);
			$BUY_PRICE				= trim($arr_rs[$j]["BUY_PRICE"]);
			$DELIVERY_CNT_IN_BOX	= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);
			$CATE_04				= trim($arr_rs[$j]["CATE_04"]);

			if($CATE_04 == "ǰ��") { 
				$sheetIndex->setCellValue("A".$b_idx, iconv("EUC-KR", "UTF-8", $GOODS_CODE));
				$sheetIndex->setCellValue("B".$b_idx, iconv("EUC-KR", "UTF-8", $GOODS_NAME." ".$GOODS_SUB_NAME));
				$sheetIndex->setCellValue("C".$b_idx, iconv("EUC-KR", "UTF-8", $TAX_TF));
				$sheetIndex->setCellValue("D".$b_idx, iconv("EUC-KR", "UTF-8", number_format($BUY_PRICE)));
				$sheetIndex->setCellValue("E".$b_idx, iconv("EUC-KR", "UTF-8", $DELIVERY_CNT_IN_BOX));
				$sheetIndex->setCellValue("F".$b_idx, "");
				$sheetIndex->setCellValue("G".$b_idx, iconv("EUC-KR", "UTF-8", "ǰ��"));
				$sheetIndex->setCellValue("H".$b_idx, "");
				$sheetIndex->getRowDimension($b_idx)->setRowHeight(22.50);

				$b_idx ++;
			}
		}

		$sheetIndex->getStyle("A1:H1")->applyFromArray($BStyle_title);
		$sheetIndex->getStyle("A2:H".($b_idx-1))->applyFromArray($BStyle);

		$sheetIndex->getStyle("A1:H1")->getFont()->setBold(true);

		$sheetIndex->getStyle("A1:H".($b_idx-1))->getAlignment()->setWrapText(true)
		->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

		$sheetIndex->getColumnDimension("A")->setWidth(13.80);
		$sheetIndex->getColumnDimension("B")->setWidth(35);
		$sheetIndex->getColumnDimension("C")->setWidth(9.70);
		$sheetIndex->getColumnDimension("D")->setWidth(16);
		$sheetIndex->getColumnDimension("E")->setWidth(9.2);
		$sheetIndex->getColumnDimension("F")->setWidth(14.6);
		$sheetIndex->getColumnDimension("G")->setWidth(9.2);
		$sheetIndex->getColumnDimension("H")->setWidth(30);

		// Rename sheet
		$sheetIndex->setTitle(iconv("EUC-KR", "UTF-8","ǰ��"));
	}

	if($type_c == "Y") { 
		////////////////////////////////////////////////////////////////////////////////
		//	����
		////////////////////////////////////////////////////////////////////////////////

		if($total_sheet_idx > 0)
			$objPHPExcel->createSheet();
		
		$sheetIndex = $objPHPExcel->setActiveSheetIndex($total_sheet_idx);
		$total_sheet_idx++;

		$sheetIndex->setCellValue("A1", iconv("EUC-KR", "UTF-8","����Ʈ�� ��ǰ�ڵ�"));
		$sheetIndex->setCellValue("B1", iconv("EUC-KR", "UTF-8","��ǰ��"));
		$sheetIndex->setCellValue("C1", iconv("EUC-KR", "UTF-8","��������"));
		$sheetIndex->setCellValue("D1", iconv("EUC-KR", "UTF-8","���԰�(VAT����)"));
		$sheetIndex->setCellValue("E1", iconv("EUC-KR", "UTF-8","�ڽ��Լ�"));
		$sheetIndex->setCellValue("F1", iconv("EUC-KR", "UTF-8","�ù����������"));
		$sheetIndex->setCellValue("G1", iconv("EUC-KR", "UTF-8","�ǸŻ���"));
		$sheetIndex->setCellValue("H1", iconv("EUC-KR", "UTF-8","���"));

		for ($j = 0 ; $j < sizeof($arr_rs) ; $j++) {
		
			$GOODS_CODE				= trim($arr_rs[$j]["GOODS_CODE"]);
			$GOODS_NAME				= trim($arr_rs[$j]["GOODS_NAME"]);
			$GOODS_SUB_NAME			= trim($arr_rs[$j]["GOODS_SUB_NAME"]);
			$TAX_TF					= trim($arr_rs[$j]["TAX_TF"]);
			$BUY_PRICE				= trim($arr_rs[$j]["BUY_PRICE"]);
			$DELIVERY_CNT_IN_BOX	= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);
			$CATE_04				= trim($arr_rs[$j]["CATE_04"]);

			if($CATE_04 == "����") { 
				$sheetIndex->setCellValue("A".$c_idx, iconv("EUC-KR", "UTF-8", $GOODS_CODE));
				$sheetIndex->setCellValue("B".$c_idx, iconv("EUC-KR", "UTF-8", $GOODS_NAME." ".$GOODS_SUB_NAME));
				$sheetIndex->setCellValue("C".$c_idx, iconv("EUC-KR", "UTF-8", $TAX_TF));
				$sheetIndex->setCellValue("D".$c_idx, iconv("EUC-KR", "UTF-8", number_format($BUY_PRICE)));
				$sheetIndex->setCellValue("E".$c_idx, iconv("EUC-KR", "UTF-8", $DELIVERY_CNT_IN_BOX));
				$sheetIndex->setCellValue("F".$c_idx, "");
				$sheetIndex->setCellValue("G".$c_idx, iconv("EUC-KR", "UTF-8", "����"));
				$sheetIndex->setCellValue("H".$c_idx, "");
				$sheetIndex->getRowDimension($c_idx)->setRowHeight(22.50);

				$c_idx ++;
			}
		}

		$sheetIndex->getStyle("A1:H1")->applyFromArray($BStyle_title);
		$sheetIndex->getStyle("A2:H".($c_idx-1))->applyFromArray($BStyle);

		$sheetIndex->getStyle("A1:H1")->getFont()->setBold(true);

		$sheetIndex->getStyle("A1:H".($c_idx-1))->getAlignment()->setWrapText(true)
		->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

		$sheetIndex->getColumnDimension("A")->setWidth(13.80);
		$sheetIndex->getColumnDimension("B")->setWidth(35);
		$sheetIndex->getColumnDimension("C")->setWidth(9.70);
		$sheetIndex->getColumnDimension("D")->setWidth(16);
		$sheetIndex->getColumnDimension("E")->setWidth(9.2);
		$sheetIndex->getColumnDimension("F")->setWidth(14.6);
		$sheetIndex->getColumnDimension("G")->setWidth(9.2);
		$sheetIndex->getColumnDimension("H")->setWidth(30);

		// Rename sheet
		$sheetIndex->setTitle(iconv("EUC-KR", "UTF-8","����"));
	}

	
	////////////////////////////////////////////////////////////////////////////////
	//	�ű�����
	////////////////////////////////////////////////////////////////////////////////
	
	if($total_sheet_idx > 0)
		$objPHPExcel->createSheet();
	
	$sheetIndex = $objPHPExcel->setActiveSheetIndex($total_sheet_idx);
	$total_sheet_idx++;

	// Rename sheet
	$sheetIndex->setTitle(iconv("EUC-KR", "UTF-8","�ű� ����"));


	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	// ������ ���������� utf-8�� ��� �ѱ����� �̸��� �����Ƿ� euc-kr�� ��ȯ���ش�.
	$filename = "��ǰȮ�ο�û��-".date("Ymd");

	// Redirect output to a client��s web browser (Excel5)
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment;filename=".$filename.".xls");
	header("Cache-Control: max-age=0");

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
	$objWriter->setUseDiskCaching(true);
	$objWriter->save("php://output");

	mysql_close($conn);
	exit;


?>
				
