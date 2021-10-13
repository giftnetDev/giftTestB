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
	$gp_no					= trim(base64url_decode($gp_no));
	
#===============================================================
# Get Search list count
#===============================================================

	//���� ȸ�� ����(����Ʈ�� or �˹�Ʈ�ν�)
	$arr_op_cp = getOperatingCompany($conn, $op_cp_no);
	
	$OP_CP_NM		= $arr_op_cp[0]["CP_NM"]." ".$arr_op_cp[0]["CP_NM2"];
	$CP_PHONE		= $arr_op_cp[0]["CP_PHONE"];
	$CP_FAX			= $arr_op_cp[0]["CP_FAX"];
	$BIZ_NO			= $arr_op_cp[0]["BIZ_NO"];
	$CP_ADDR		= $arr_op_cp[0]["CP_ADDR"];
	$UPTEA			= $arr_op_cp[0]["UPTEA"];
	$UPJONG			= $arr_op_cp[0]["UPJONG"];
	
	//������ ��ȸ �Լ� ����
	function getEstimate($db, $gp_no, $del_tf){
		$query =   "SELECT *
					FROM TBL_ESTIMATE
					WHERE GP_NO = '$gp_no'
		";
		if($del_tf <> ""){
			$query .= " AND DEL_TF = '$del_tf' ";
		}
		//echo $query;
		$result = mysql_query($query,$db);
		$record = array();
		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}
	
	//���� ��ǰ ��ȸ �Լ� ����
	function getEstimateGoods($db, $gp_no, $cancel_tf, $del_tf) {
		$query =    "SELECT 
						GOODS_NO
						,GOODS_NAME
						,QTY
						,SUPPLY_PRICE
						,ESTIMATE_PRICE
						,REG_DATE
						,UP_DATE
					FROM
						TBL_ESTIMATE_SUB
					WHERE 1=1
		";
		
		if ($gp_no <> "") {
			$query .= " AND GP_NO = '".$gp_no."' ";
		}

		if ($cancel_tf <> "") {
			$query .= " AND CANCEL_TF = '".$cancel_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}

		$query .= " ORDER BY GPG_NO ";
		//echo $query;
		$result = mysql_query($query,$db);
		$record = array();
		
		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	//��ǰ ���� ���� ��ȸ
	function getTAX_TF($db, $goods_no){
		$query =   "SELECT TAX_TF
					FROM TBL_GOODS 
					WHERE GOODS_NO ='$goods_no'
		";

		$result = mysql_query($query,$db);
		$record = array();
		
		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record[0]["TAX_TF"];
	}

	//���� ���� ��ȸ
	$temp_arr_rs2 = getEstimate($conn, $gp_no, 'N');
	
	//���� ��ǰ ���� ��ȸ
	$temp_arr_rs = getEstimateGoods($conn, $gp_no, 'N', 'N');
	
	//�� ��������
	$TEMP_TOTAL_SALE_PRICE = intval(SetStringFromDB(trim($temp_arr_rs2[0]["GRAND_TOTAL_SALE_PRICE"])));

	//�� ���ξ�
	$TEMP_TOTAL_DISCOUNT_PRICE = SetStringFromDB(trim($temp_arr_rs2[0]["TOTAL_DISCOUNT_PRICE"]));

	//���� ��û�� ȸ���
	$arr_cp = getOperatingCompany($conn, $temp_arr_rs2[0]["CP_NO"]);
	$R_CP_NM = SetStringFromDB(trim($arr_cp[0]["CP_NM"]));


	//���� �۾�
	require_once "../../_PHPExcel/Classes/PHPExcel.php";

	// Cell caching to reduce memory usage.
	$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
	$cacheSettings = array( " memoryCacheSize " => "8MB");
	PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings); 

	$objPHPExcel = new PHPExcel();

	$sheetIndex = $objPHPExcel->setActiveSheetIndex(0);
	//�� ������ 0.71~0.72 ���̳� �÷� ���� ����
	$sheetIndex->getColumnDimension("A")->setWidth(23.85);
	$sheetIndex->getColumnDimension("B")->setWidth(9);
	$sheetIndex->getColumnDimension("C")->setWidth(8.85);
	$sheetIndex->getColumnDimension("D")->setWidth(10.42);
	$sheetIndex->getColumnDimension("E")->setWidth(3.71);
	$sheetIndex->getColumnDimension("F")->setWidth(16.42);
	$sheetIndex->getColumnDimension("G")->setWidth(15.43);
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

	//2��
	$sheetIndex->setCellValue("A2",iconv("EUC-KR", "UTF-8","��     ��    ��"));
	$sheetIndex->mergeCells("A2:G2");
	$sheetIndex->getStyle("A2:G2")->getFont()->setSize(28)->setBold(true);
	$sheetIndex->getRowDimension(2)->setRowHeight(60.0);


	//3�� 
	$sheetIndex->setCellValue("E3", iconv("EUC-KR", "UTF-8","�� �� ��"))
	->setCellValue("F3", iconv("EUC-KR", "UTF-8","�� �� �� ȣ"))
	->setCellValue("G3", iconv("EUC-KR", "UTF-8", $BIZ_NO));
	$sheetIndex->mergeCells("E3:E7");
	$sheetIndex->getStyle("E3:E7")->getAlignment()->setWrapText(true);
	$sheetIndex->getRowDimension(3)->setRowHeight(29.25);

	//4�� 
	$sheetIndex
	->setCellValue("F4", iconv("EUC-KR", "UTF-8","��ȣ(���θ�) "))
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

	//5�� 
	$sheetIndex
	->setCellValue("F5", iconv("EUC-KR", "UTF-8","������ּ�"))
	->setCellValue("G5", iconv("EUC-KR", "UTF-8", $CP_ADDR));
	$sheetIndex->getStyle("G5")->getFont()->setSize(9);
	$sheetIndex->getStyle("G5")->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	//$sheetIndex->getRowDimension(5)->setRowHeight(29.25);

	//6�� 
	$sheetIndex->setCellValue("A6", iconv("EUC-KR", "UTF-8", $R_CP_NM))
	->setCellValue("C6", iconv("EUC-KR", "UTF-8","����"))
	->setCellValue("F6", iconv("EUC-KR", "UTF-8","�� �� : ".$UPTEA))
	->setCellValue("G6", iconv("EUC-KR", "UTF-8","��  �� : ".$UPJONG));
	$sheetIndex->mergeCells("A6:B6");
	$sheetIndex->getStyle("A6")->getFont()->setSize(10)->setBold(true);
	$sheetIndex->getStyle("C6")->getFont()->setSize(10)->setBold(true);
	$sheetIndex->getStyle("A6:C6")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$sheetIndex->getRowDimension(6)->setRowHeight(29.25);

	//7�� 
	$sheetIndex
	->setCellValue("F7", iconv("EUC-KR", "UTF-8","�� ȭ �� ȣ"))
	->setCellValue("G7", iconv("EUC-KR", "UTF-8", $CP_PHONE));

	$sheetIndex->getStyle("E3:G7")->getFont()->setSize(10);
	$sheetIndex->getStyle("E3:G7")->applyFromArray($BStyle);
	$sheetIndex->getRowDimension(7)->setRowHeight(29.25);

	//8�� 
	$sheetIndex->setCellValue("A8", iconv("EUC-KR", "UTF-8","(���ް���+����)"))
	->setCellValue("B8", iconv("EUC-KR", "UTF-8", NUMBERSTRING($TEMP_TOTAL_SALE_PRICE)))
	->setCellValue("F8", iconv("EUC-KR", "UTF-8", number_format($TEMP_TOTAL_SALE_PRICE)));
	//->setCellValue("B8", iconv("EUC-KR", "UTF-8", NUMBERSTRING($TOTAL_SALE_PRICE)))
	//->setCellValue("F8", iconv("EUC-KR", "UTF-8", number_format($TOTAL_SALE_PRICE)));
	$sheetIndex->mergeCells("B8:E8");
	$sheetIndex->getStyle("A8")->getFont()->setSize(10);
	$sheetIndex->getStyle("B8:F8")->getFont()->setSize(10)->setBold(true);
	$sheetIndex->getStyle("F8")->getNumberFormat()->setFormatCode("#,##0");
	$sheetIndex->getRowDimension(8)->setRowHeight(29.25);

	//ǰ           ��	��  ��	��  ��	��    ��		��  ��  ��  ��	��       ��
	//9�� 
	$sheetIndex->setCellValue("A9", iconv("EUC-KR", "UTF-8","ǰ ��"))
	->setCellValue("B9", iconv("EUC-KR", "UTF-8","�� ��"))
	->setCellValue("C9", iconv("EUC-KR", "UTF-8","�� ��"))
	->setCellValue("D9", iconv("EUC-KR", "UTF-8","�� ��"))
	->setCellValue("F9", iconv("EUC-KR", "UTF-8","�� �� �� ��"))
	->setCellValue("G9", iconv("EUC-KR", "UTF-8","��  ��"));
	$sheetIndex->mergeCells("D9:E9");
	$sheetIndex->getStyle("A9:G9")->getFont()->setSize(10);
	$sheetIndex->getRowDimension(9)->setRowHeight(29.25);


	$DEFAULT_DATE = date("Y�� n�� j��",strtotime("0000-00-00 00:00:00"));
	$ESTIMATE_DATE = $DEFAULT_DATE;
	$k  = 10;
	for ($j = 0 ; $j < sizeof($temp_arr_rs); $j++) {
	
		$TEMP_GOODS_NAME		= SetStringFromDB(trim($temp_arr_rs[$j]["GOODS_NAME"]));
		$TEMP_GOODS_SUB_NAME	= "";
		$TEMP_QTY				= SetStringFromDB(trim($temp_arr_rs[$j]["QTY"]));
		$TEMP_ESTIMATE_PRICE	= SetStringFromDB(trim($temp_arr_rs[$j]["ESTIMATE_PRICE"]));
		$TEMP_SUPPLY_PRICE		= SetStringFromDB(trim($temp_arr_rs[$j]["SUPPLY_PRICE"]));
		$TEMP_GOODS_NO			= SetStringFromDB(trim($temp_arr_rs[$j]["GOODS_NO"]));
		$TEMP_TAX_TF			= SetStringFromDB(trim(getTAX_TF($conn,$TEMP_GOODS_NO)));
		$TEMP_STR_TAX_TF		= "";

		if($TEMP_TAX_TF == "����") 
			$TEMP_STR_TAX_TF  = "�ΰ�������";
		else
			$TEMP_STR_TAX_TF  = "�鼼";	

		//10�� 
		$sheetIndex
		->setCellValue("A".$k, iconv("EUC-KR", "UTF-8", $TEMP_GOODS_NAME))
		->setCellValue("B".$k, iconv("EUC-KR", "UTF-8", $TEMP_GOODS_SUB_NAME))
		->setCellValue("C".$k, iconv("EUC-KR", "UTF-8", $TEMP_QTY))
		->setCellValue("D".$k, iconv("EUC-KR", "UTF-8", $TEMP_ESTIMATE_PRICE))
		->setCellValue("F".$k, iconv("EUC-KR", "UTF-8", $TEMP_SUPPLY_PRICE))
		->setCellValue("G".$k, iconv("EUC-KR", "UTF-8", $TEMP_STR_TAX_TF));
		$sheetIndex->mergeCells("D$k:E$k");
		$sheetIndex->getStyle("A$k")->getAlignment()->setWrapText(true);
		$sheetIndex->getStyle("A$k:G$k")->getFont()->setSize(9)->setBold(true);
		$sheetIndex->getStyle("D$k")->getNumberFormat()->setFormatCode("#,##0");
		$sheetIndex->getStyle("F$k")->getNumberFormat()->setFormatCode("#,##0");
		$sheetIndex->getRowDimension($k)->setRowHeight(27);

		$k += 1;

		//������ ����� ǥ��, ��ǰ ���� ���� ������ ��ǰ ���� �����Ϸ� ǥ��
		$TEMP_REG_DATE = date("Y�� n�� j��",strtotime($temp_arr_rs[$j]["REG_DATE"]));
		$TEMP_UP_DATE = date("Y�� n�� j��",strtotime($temp_arr_rs[$j]["UP_DATE"]));
		
		if($TEMP_UP_DATE != $DEFAULT_DATE && $TEMP_UP_DATE > $ESTIMATE_DATE)
			$ESTIMATE_DATE = $TEMP_UP_DATE;
		else if($TEMP_UP_DATE == $DEFAULT_DATE && $TEMP_REG_DATE > $ESTIMATE_DATE)
			$ESTIMATE_DATE = $TEMP_REG_DATE;
		$sheetIndex->setCellValue("A4", iconv("EUC-KR", "UTF-8", $ESTIMATE_DATE));
	}

	if($TEMP_TOTAL_DISCOUNT_PRICE != 0) { 

		$sheetIndex->setCellValue("A".$k, iconv("EUC-KR", "UTF-8", "��������"))
		->setCellValue("F".$k, iconv("EUC-KR", "UTF-8", -1 * $TEMP_TOTAL_DISCOUNT_PRICE));
		$sheetIndex->mergeCells("D$k:E$k");
		$sheetIndex->getStyle("A$k")->getAlignment()->setWrapText(true);
		$sheetIndex->getStyle("A$k:G$k")->getFont()->setSize(9)->setBold(true);
		$sheetIndex->getStyle("D$k")->getNumberFormat()->setFormatCode("#,##0");
		$sheetIndex->getStyle("F$k")->getNumberFormat()->setFormatCode("#,##0");
		$sheetIndex->getRowDimension($k)->setRowHeight(27);

		$k += 1;
	}

	while($k < 23) { 

		$sheetIndex->mergeCells("D$k:E$k");
		$sheetIndex->getRowDimension($k)->setRowHeight(27);
	
		$k += 1;
	}

	//26�� 
	$sheetIndex->setCellValue("A$k", iconv("EUC-KR", "UTF-8","�հ��"))
	// ->setCellValue("G$k", number_format($TOTAL_SALE_PRICE));
	->setCellValue("G$k", number_format($TEMP_TOTAL_SALE_PRICE));
	$sheetIndex->mergeCells("A$k:E$k");
	$sheetIndex->getStyle("A$k:F$k")->getFont()->setSize(14);
	$sheetIndex->getStyle("G$k")->getFont()->setSize(9)->setBold(true);
	$sheetIndex->getStyle("G$k")->getNumberFormat()->setFormatCode("#,##0");
	$sheetIndex->getRowDimension($k)->setRowHeight(27);

	$sheetIndex->getStyle("A9:G".($k-1))->applyFromArray($BStyle);
	$sheetIndex->getStyle("A$k:G$k")->applyFromArray($BStyle_outline);
	$sheetIndex->getStyle("A1:G$k")->applyFromArray($BStyle_font);

	$sheetIndex->getPageSetup()->setFitToPage(TRUE);  //2021_10_13 ����Ʈ�� 1�������θ� �������� �����ϴ� �ڵ�
	
	
	// Rename sheet
	$sheetIndex->setTitle(iconv("EUC-KR", "UTF-8","������"));

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	// ������ ���������� utf-8�� ��� �ѱ����� �̸��� �����Ƿ� euc-kr�� ��ȯ���ش�.
	$filename = "������-".$SENDER_NM."-".$reserve_no."-".date("Ymd");
	
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