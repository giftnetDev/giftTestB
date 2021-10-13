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
	$rs_opt_manager_no				= trim($arr_order_rs[0]["OPT_MANAGER_NO"]);
	$rs_reg_adm						= trim($arr_order_rs[0]["REG_ADM"]);
	$rs_memo						= trim($arr_order_rs[0]["MEMO"]);
	
	$rs_order_date		= date("Y�� n�� j��", strtotime($rs_order_date));	
	$rs_opt_manager_no  = getAdminName($conn, $rs_opt_manager_no);
	$rs_reg_adm			= getAdminName($conn, $rs_reg_adm);
	
	$arr_cp = selectCompany($conn, $rs_cp_no);
	$IS_MALL		 = $arr_cp[0]["IS_MALL"];
	$SENDER_NM      = $arr_cp[0]["CP_NM"]." ".$arr_cp[0]["CP_NM2"];

	if($IS_MALL == "Y") { 

		//������ �ƴϰ� �ֹ��� ������ ���� 2017-07-03
		$SENDER_NM      = $rs_o_mem_nm;
		$SENDER_ADDR	= "";
		$SENDER_CP_PHONE= $rs_o_phone;

	} else { 
		$SENDER_NM      = $arr_cp[0]["CP_NM"]." ".$arr_cp[0]["CP_NM2"];
		$SENDER_ADDR	= $arr_cp[0]["CP_ADDR"];
		$SENDER_CP_PHONE= $arr_cp[0]["CP_PHONE"];
	}
	
	$arr_rs = listManagerOrderGoods($conn, $reserve_no, $mem_no, "Y", "N");

	require_once "../../_PHPExcel/Classes/PHPExcel.php";

	// Cell caching to reduce memory usage.
	$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
	$cacheSettings = array( " memoryCacheSize " => "8MB");
	PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings); 

	$objPHPExcel = new PHPExcel();

	$sheetIndex = $objPHPExcel->setActiveSheetIndex(0);

	$k1 = 1;

	$w = 0.75;


	$sheetIndex->getColumnDimension("A")->setWidth(10.43 + $w);
	$sheetIndex->getColumnDimension("B")->setWidth(11.71 + $w);
	$sheetIndex->getColumnDimension("C")->setWidth(14.29 + $w);
	$sheetIndex->getColumnDimension("D")->setWidth(50.14 + $w);
	$sheetIndex->getColumnDimension("E")->setWidth(13.57 + $w);
	$sheetIndex->getColumnDimension("F")->setWidth(16.86 + $w);
	$sheetIndex->getColumnDimension("G")->setWidth(12.86 + $w);
	$sheetIndex->getColumnDimension("H")->setWidth(51.86 + $w);
	$sheetIndex->getColumnDimension("I")->setWidth(646 + $w);

	
	$sheetIndex->setCellValue("A".$k1, iconv("EUC-KR", "UTF-8", "�����"))
				->setCellValue("B".$k1, iconv("EUC-KR", "UTF-8", "�ù��"))
				->setCellValue("C".$k1, iconv("EUC-KR", "UTF-8", "�����ȣ"))
				->setCellValue("D".$k1, iconv("EUC-KR", "UTF-8", "��ǰ��"))
				->setCellValue("E".$k1, iconv("EUC-KR", "UTF-8", "����"))
				->setCellValue("F".$k1, iconv("EUC-KR", "UTF-8", "������"))
				->setCellValue("G".$k1, iconv("EUC-KR", "UTF-8", "�����ڹ�ȣ"))
				->setCellValue("H".$k1, iconv("EUC-KR", "UTF-8", "�������ּ�"))
				->setCellValue("I".$k1, iconv("EUC-KR", "UTF-8", "��۸޸�"));
	$k1 += 1;

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
		  'style' => PHPExcel_Style_Border::BORDER_THICK
		)
	  )
	);

	$style = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );

	$style_left = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
        )
    );

	$style_right = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
        )
    );

	$background_color_style = array(
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'DFDFDF')
        )
	);

    $sheetIndex->getDefaultStyle()->applyFromArray($style_left);

	for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
	
		$ORDER_GOODS_NO				= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
		$ORDER_SEQ					= trim($arr_rs[$j]["ORDER_SEQ"]);
		$GOODS_NO					= trim($arr_rs[$j]["GOODS_NO"]);
		$GOODS_STATE				= trim($arr_rs[$j]["GOODS_STATE"]);
		$GOODS_CODE					= SetStringFromDB(trim($arr_rs[$j]["GOODS_CODE"]));
		$GOODS_NAME					= SetStringFromDB(trim($arr_rs[$j]["GOODS_NAME"]));
		$GOODS_SUB_NAME				= SetStringFromDB(trim($arr_rs[$j]["GOODS_SUB_NAME"]));
		$BUY_PRICE					= trim($arr_rs[$j]["BUY_PRICE"]);
		$SALE_PRICE					= trim($arr_rs[$j]["SALE_PRICE"]);
		$EXTRA_PRICE				= trim($arr_rs[$j]["EXTRA_PRICE"]);
		$DELIVERY_PRICE				= trim($arr_rs[$j]["DELIVERY_PRICE"]);
		$SA_DELIVERY_PRICE			= trim($arr_rs[$j]["SA_DELIVERY_PRICE"]);
		$DISCOUNT_PRICE				= trim($arr_rs[$j]["DISCOUNT_PRICE"]);
		$DELIVERY_TYPE				= trim($arr_rs[$j]["DELIVERY_TYPE"]);
		$DELIVERY_CP				= trim($arr_rs[$j]["DELIVERY_CP"]);
		$DELIVERY_NO				= trim($arr_rs[$j]["DELIVERY_NO"]);
		$OPT_OUTSTOCK_DATE			= trim($arr_rs[$j]["OPT_OUTSTOCK_DATE"]);
		
		$CATE_01					= trim($arr_rs[$j]["CATE_01"]);
		$CATE_04					= trim($arr_rs[$j]["CATE_04"]);

		$ORDER_STATE				= trim($arr_rs[$j]["ORDER_STATE"]);
		$TAX_TF						= trim($arr_rs[$j]["TAX_TF"]);

		if($TAX_TF == "����") 
			$STR_TAX_TF  = "�ΰ�������";
		else
			$STR_TAX_TF  = "�鼼";

		$WORK_END_DATE			= trim($arr_rs[$j]["WORK_END_DATE"]);

		$QTY = getRefundAbleQty_EstimateTransaction($conn, $reserve_no, $ORDER_GOODS_NO);

		//��ü ��� �� ����
		if($QTY == 0) continue;

		//��ȯ���� ���� 2017-11-16
		if($CATE_04 <> '')
			continue;

		
		if($ORDER_STATE == "1" || $ORDER_STATE == "2" || $ORDER_STATE == "3") {  

			$has_row = false;

			if($DELIVERY_TYPE == "3") {

				$arr_ind_rs = listDeliveryIndividual($conn, $ORDER_GOODS_NO, "DESC");
				if(sizeof($arr_ind_rs) > 0) { 

					for($i = 0; $i < sizeof($arr_ind_rs); $i ++) { 

						$rs4_individual_no			= trim($arr_ind_rs[$i]["INDIVIDUAL_NO"]);
						$rs4_r_zipcode			    = trim($arr_ind_rs[$i]["R_ZIPCODE"]); 
						$rs4_r_addr1 				= trim($arr_ind_rs[$i]["R_ADDR1"]);
						$rs4_r_mem_nm				= trim($arr_ind_rs[$i]["R_MEM_NM"]);
						$rs4_r_phone				= trim($arr_ind_rs[$i]["R_PHONE"]); 
						$rs4_r_hphone				= trim($arr_ind_rs[$i]["R_HPHONE"]); 
						$rs4_goods_delivery_name	= trim($arr_ind_rs[$i]["GOODS_DELIVERY_NAME"]); 
						$rs4_sub_qty				= trim($arr_ind_rs[$i]["SUB_QTY"]);
						$rs4_memo					= trim($arr_ind_rs[$i]["MEMO"]);
						$rs4_delivery_type			= trim($arr_ind_rs[$i]["DELIVERY_TYPE"]);
						$rs4_is_delivered			= trim($arr_ind_rs[$i]["IS_DELIVERED"]);
						$rs4_use_tf					= trim($arr_ind_rs[$i]["USE_TF"]);

						if($rs4_use_tf == "N")
							continue;
					
					
						$arr_order_rs = listOrderDeliveryPaper($conn, $ORDER_GOODS_NO, $rs4_individual_no);

						for ($r = 0 ; $r < sizeof($arr_order_rs); $r++) {
							$rs3_delivery_seq	        = SetStringFromDB($arr_order_rs[$r]["DELIVERY_SEQ"]); 
							$rs3_delivery_no 		    = SetStringFromDB($arr_order_rs[$r]["DELIVERY_NO"]);
							$rs3_delivery_cp			= SetStringFromDB($arr_order_rs[$r]["DELIVERY_CP"]);
							$rs3_order_nm		        = SetStringFromDB($arr_order_rs[$r]["ORDER_NM"]); 
							$rs3_order_phone		    = SetStringFromDB($arr_order_rs[$r]["ORDER_PHONE"]);
							$rs3_order_manager_nm	    = SetStringFromDB($arr_order_rs[$r]["ORDER_MANAGER_NM"]);
							$rs3_order_manager_phone	= SetStringFromDB($arr_order_rs[$r]["ORDER_MANAGER_PHONE"]);
							$rs3_receiver_nm		    = SetStringFromDB($arr_order_rs[$r]["RECEIVER_NM"]); 
							$rs3_receiver_phone		    = SetStringFromDB($arr_order_rs[$r]["RECEIVER_PHONE"]);
							$rs3_receiver_hphone	    = SetStringFromDB($arr_order_rs[$r]["RECEIVER_HPHONE"]);
							$rs3_receiver_addr			= SetStringFromDB($arr_order_rs[$r]["RECEIVER_ADDR"]); 
							$rs3_goods_delivery_name    = SetStringFromDB($arr_order_rs[$r]["GOODS_DELIVERY_NAME"]); 
							$rs3_memo				    = SetStringFromDB($arr_order_rs[$r]["MEMO"]); 
							$rs3_delivery_fee			= SetStringFromDB($arr_order_rs[$r]["DELIVERY_FEE"]); 
							$rs3_delivery_fee_code		= SetStringFromDB($arr_order_rs[$r]["DELIVERY_FEE_CODE"]); 
							$rs3_delivery_claim_code	= SetStringFromDB($arr_order_rs[$r]["DELIVERY_CLAIM_CODE"]); 
							$rs3_delivery_date          = SetStringFromDB($arr_order_rs[$r]["DELIVERY_DATE"]); 
							$rs3_use_tf					= SetStringFromDB($arr_order_rs[$r]["USE_TF"]); 
							$rs3_reg_date				= SetStringFromDB($arr_order_rs[$r]["REG_DATE"]); 

							if($rs3_use_tf == "N" || $rs3_delivery_no == "")
								continue;

							$rs3_reg_date = date("Y-m-d", strtotime($rs3_reg_date));

							$sheetIndex ->setCellValue("A".$k1, iconv("EUC-KR", "UTF-8", $OPT_OUTSTOCK_DATE))
										->setCellValue("B".$k1, iconv("EUC-KR", "UTF-8", $rs3_delivery_cp))
										->setCellValue("C".$k1, iconv("EUC-KR", "UTF-8", $rs3_delivery_no))
										->setCellValue("D".$k1, iconv("EUC-KR", "UTF-8", ($CATE_01 <> "" ? $CATE_01.") " : "").$GOODS_NAME." ".$GOODS_SUB_NAME))
										->setCellValue("E".$k1, iconv("EUC-KR", "UTF-8", $rs4_sub_qty))
										->setCellValue("F".$k1, iconv("EUC-KR", "UTF-8", $rs3_receiver_nm))
										->setCellValue("G".$k1, iconv("EUC-KR", "UTF-8", $rs3_receiver_phone))
										->setCellValue("H".$k1, iconv("EUC-KR", "UTF-8", $rs3_receiver_addr))
										->setCellValue("I".$k1, iconv("EUC-KR", "UTF-8", $rs3_memo));

							$sheetIndex->getStyle("C$k1")->getNumberFormat()->setFormatCode("############");
							$sheetIndex->getStyle("A$k1:I$k1")->applyFromArray($style_left);
							$sheetIndex->getStyle("E$k1")->applyFromArray($style_right);


							$has_row = true;
							$k1 += 1;
						}
					}
				}

			} else { 
				$arr_order_rs = listOrderDeliveryPaper($conn, $ORDER_GOODS_NO, "");
			
				//�����ּҼ���
				for ($r = 0 ; $r < sizeof($arr_order_rs); $r++) {
					$rs3_delivery_seq	        = SetStringFromDB($arr_order_rs[$r]["DELIVERY_SEQ"]); 
					$rs3_delivery_no 		    = SetStringFromDB($arr_order_rs[$r]["DELIVERY_NO"]);
					$rs3_delivery_cp			= SetStringFromDB($arr_order_rs[$r]["DELIVERY_CP"]);
					$rs3_order_nm		        = SetStringFromDB($arr_order_rs[$r]["ORDER_NM"]); 
					$rs3_order_phone		    = SetStringFromDB($arr_order_rs[$r]["ORDER_PHONE"]);
					$rs3_order_manager_nm	    = SetStringFromDB($arr_order_rs[$r]["ORDER_MANAGER_NM"]);
					$rs3_order_manager_phone	= SetStringFromDB($arr_order_rs[$r]["ORDER_MANAGER_PHONE"]);
					$rs3_receiver_nm		    = SetStringFromDB($arr_order_rs[$r]["RECEIVER_NM"]); 
					$rs3_receiver_phone		    = SetStringFromDB($arr_order_rs[$r]["RECEIVER_PHONE"]);
					$rs3_receiver_hphone	    = SetStringFromDB($arr_order_rs[$r]["RECEIVER_HPHONE"]);
					$rs3_receiver_addr			= SetStringFromDB($arr_order_rs[$r]["RECEIVER_ADDR"]); 
					$rs3_goods_delivery_name    = SetStringFromDB($arr_order_rs[$r]["GOODS_DELIVERY_NAME"]); 
					$rs3_memo				    = SetStringFromDB($arr_order_rs[$r]["MEMO"]); 
					$rs3_delivery_fee			= SetStringFromDB($arr_order_rs[$r]["DELIVERY_FEE"]); 
					$rs3_delivery_fee_code		= SetStringFromDB($arr_order_rs[$r]["DELIVERY_FEE_CODE"]); 
					$rs3_delivery_claim_code	= SetStringFromDB($arr_order_rs[$r]["DELIVERY_CLAIM_CODE"]); 
					$rs3_delivery_date          = SetStringFromDB($arr_order_rs[$r]["DELIVERY_DATE"]); 
					$rs3_use_tf					= SetStringFromDB($arr_order_rs[$r]["USE_TF"]); 
					$rs3_reg_date				= SetStringFromDB($arr_order_rs[$r]["REG_DATE"]); 

					if($rs3_use_tf == "N" || $rs3_delivery_no == "")
						continue;

					$rs3_reg_date = date("Y-m-d", strtotime($rs3_reg_date));

					$sheetIndex ->setCellValue("A".$k1, iconv("EUC-KR", "UTF-8", $OPT_OUTSTOCK_DATE))
								->setCellValue("B".$k1, iconv("EUC-KR", "UTF-8", $rs3_delivery_cp))
								->setCellValue("C".$k1, iconv("EUC-KR", "UTF-8", $rs3_delivery_no))
								->setCellValue("D".$k1, iconv("EUC-KR", "UTF-8", ($CATE_01 <> "" ? $CATE_01.") " : "").$GOODS_NAME." ".$GOODS_SUB_NAME))
								->setCellValue("E".$k1, iconv("EUC-KR", "UTF-8", $QTY))
								->setCellValue("F".$k1, iconv("EUC-KR", "UTF-8", $rs3_receiver_nm))
								->setCellValue("G".$k1, iconv("EUC-KR", "UTF-8", $rs3_receiver_phone))
								->setCellValue("H".$k1, iconv("EUC-KR", "UTF-8", $rs3_receiver_addr))
								->setCellValue("I".$k1, iconv("EUC-KR", "UTF-8", $rs3_memo));

					$sheetIndex->getStyle("C$k1")->getNumberFormat()->setFormatCode("############");
					$sheetIndex->getStyle("A$k1:I$k1")->applyFromArray($style_left);
					$sheetIndex->getStyle("E$k1")->applyFromArray($style_right);


					$has_row = true;
					$k1 += 1;
				}
			}


			//�ܺξ�ü�߼��� ��츸
			if($DELIVERY_TYPE == "98")
				$arr_order_outside = listOrderDeliveryPaperOutside($conn, $ORDER_GOODS_NO);	


			//�ܺμ���
			for ($r = 0 ; $r < sizeof($arr_order_outside); $r++) {

				$rs2_delivery_cp = trim($arr_order_outside[$r]["DELIVERY_CP"]);
				$rs2_delivery_no = trim($arr_order_outside[$r]["DELIVERY_NO"]);
				$rs2_memo		 = trim($arr_order_outside[$r]["MEMO"]);
				$rs2_reg_date	 = trim($arr_order_outside[$r]["REG_DATE"]);

				if($rs2_delivery_no == "")
					continue;

				$rs2_reg_date = date("Y-m-d", strtotime($rs2_reg_date));

				$sheetIndex ->setCellValue("A".$k1, iconv("EUC-KR", "UTF-8", $rs2_reg_date))
							->setCellValue("B".$k1, iconv("EUC-KR", "UTF-8", $rs2_delivery_cp))
							->setCellValue("C".$k1, iconv("EUC-KR", "UTF-8", $rs2_delivery_no))
							->setCellValue("D".$k1, iconv("EUC-KR", "UTF-8", ($CATE_01 <> "" ? $CATE_01.") " : "").$GOODS_NAME." ".$GOODS_SUB_NAME))
							->setCellValue("E".$k1, iconv("EUC-KR", "UTF-8", $QTY))
							->setCellValue("I".$k1, iconv("EUC-KR", "UTF-8", $rs2_memo));
		
				$sheetIndex->getStyle("C$k1")->getNumberFormat()->setFormatCode("############");
				$sheetIndex->getStyle("A$k1:I$k1")->applyFromArray($style_left);
				$sheetIndex->getStyle("E$k1")->applyFromArray($style_right);

				$has_row = true;
				$k1 += 1;
			}

			//��ǥ������ �������
			if($DELIVERY_NO <> "1" && !$has_row && $DELIVERY_TYPE <> '99' ) { 

				if($WORK_END_DATE <> "0000-00-00 00:00:00" && $WORK_END_DATE <> "")
					$WORK_END_DATE = date("Y-m-d", strtotime($WORK_END_DATE));
				else
					$WORK_END_DATE = "�����";

				$sheetIndex ->setCellValue("A".$k1, iconv("EUC-KR", "UTF-8", $WORK_END_DATE))
							->setCellValue("B".$k1, iconv("EUC-KR", "UTF-8", $DELIVERY_CP))
							->setCellValue("C".$k1, iconv("EUC-KR", "UTF-8", $DELIVERY_NO))
							->setCellValue("D".$k1, iconv("EUC-KR", "UTF-8", ($CATE_01 <> "" ? $CATE_01.") " : "").$GOODS_NAME." ".$GOODS_SUB_NAME))
							->setCellValue("E".$k1, iconv("EUC-KR", "UTF-8", $QTY))
							->setCellValue("F".$k1, iconv("EUC-KR", "UTF-8", $rs_r_mem_nm))
							->setCellValue("G".$k1, iconv("EUC-KR", "UTF-8", $rs_r_phone))
							->setCellValue("H".$k1, iconv("EUC-KR", "UTF-8", $rs_r_addr1))
							->setCellValue("I".$k1, iconv("EUC-KR", "UTF-8", $rs_memo));

				$sheetIndex->getStyle("C$k1")->getNumberFormat()->setFormatCode("############");
				$sheetIndex->getStyle("A$k1:I$k1")->applyFromArray($style_left);
				$sheetIndex->getStyle("E$k1")->applyFromArray($style_right);

				$has_row = true;
				$k1 += 1;
			}
				
			if($has_row)
				$k1 += 1;
		}
	}
	
	// Rename sheet
	$sheetIndex->setTitle(iconv("EUC-KR", "UTF-8", "���帮��Ʈ"));

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	// ������ ���������� utf-8�� ��� �ѱ����� �̸��� �����Ƿ� euc-kr�� ��ȯ���ش�.
	$filename = "�ֹ���-".$SENDER_NM."-".$reserve_no."-".date("Ymd");
	$filename = str_replace(",","\.",$filename);
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