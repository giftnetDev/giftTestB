<?
ini_set('memory_limit',-1);
ini_set('max_execution_time', 600);
session_start();
?>
<?

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "CF006"; // �޴����� ���� �� �־�� �մϴ�

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

if ($s_adm_cp_type == "�Ǹ�" || $s_adm_cp_type == "�ǸŰ���") { 
	$cp_type = $s_adm_com_code;
}

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/confirm/confirm.php";
	require "../../_classes/biz/company/company.php";

#====================================================================
# Request Parameter
#====================================================================

	if($cp_no <> "") { 
		$cp_type			= trim(base64url_decode($cp_no));
		$start_date			= trim(base64url_decode($start_date));
		$end_date			= trim(base64url_decode($end_date));
	}

	$day_0 = date("Y-m-d",strtotime("0 month"));
	$day_1 = date("Y-m-d",strtotime("-1 day"));
	$day_7 = date("Y-m-d",strtotime("-7 day"));
	$day_31 = date("Y-m-d",strtotime("-1 month"));

	if ($start_date == "") {
		$start_date = date("Y-m-d",strtotime("-1 month"));;
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));;
	} else {
		$end_date = trim($end_date);
	}

#===============================================================
# Get Search list count
#===============================================================

	$arr_rs_prev = getCompanyLedgerPreviousMonth($conn, $start_date, $cp_type);

	if($cp_type <> "")
		$arr_rs = listCompanyLedger($conn, $start_date, $end_date, $cp_type);

	
	$arr_rs_company = selectCompany($conn, $cp_type);
	
	if(sizeof($arr_rs_company)) { 
		$rs_cp_type							= SetStringFromDB($arr_rs_company[0]["CP_TYPE"]); 
		$rs_cp_zip							= SetStringFromDB($arr_rs_company[0]["CP_ZIP"]); 
		$rs_cp_addr							= SetStringFromDB($arr_rs_company[0]["CP_ADDR"]); 
		$rs_biz_no							= SetStringFromDB($arr_rs_company[0]["BIZ_NO"]); 
		$rs_ceo_nm							= SetStringFromDB($arr_rs_company[0]["CEO_NM"]); 
		$rs_cp_phone						= SetStringFromDB($arr_rs_company[0]["CP_PHONE"]); 
		$rs_cp_fax							= SetStringFromDB($arr_rs_company[0]["CP_FAX"]); 
	}

	require_once "../../_PHPExcel/Classes/PHPExcel.php";

	// Cell caching to reduce memory usage.
	$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
	$cacheSettings = array( " memoryCacheSize " => "8MB");
	PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings); 

	$objPHPExcel = new PHPExcel();

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

	$style = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );

	$underline_style = array(
	  'font' => array(
		'underline' => PHPExcel_Style_Font::UNDERLINE_SINGLE
	  )
	);

	$sheetIndex = $objPHPExcel->setActiveSheetIndex(0);

	$sheetIndex->getColumnDimension("A")->setWidth(11.7);
	$sheetIndex->getColumnDimension("B")->setWidth(7.7);
	$sheetIndex->getColumnDimension("C")->setWidth(28.85);
	$sheetIndex->getColumnDimension("D")->setWidth(7);
	$sheetIndex->getColumnDimension("E")->setWidth(11);
	$sheetIndex->getColumnDimension("F")->setWidth(11);
	$sheetIndex->getColumnDimension("G")->setWidth(11);
	$sheetIndex->getColumnDimension("H")->setWidth(11);
	$sheetIndex->getColumnDimension("I")->setWidth(11);
	$sheetIndex->getColumnDimension("J")->setWidth(17);
	$sheetIndex->getColumnDimension("K")->setWidth(17);

	$sheetIndex->getDefaultStyle()->applyFromArray($style);
	$sheetIndex->getDefaultStyle()->applyFromArray($BStyle_font);

	$k = 1;
	$sheetIndex->getRowDimension($k)->setRowHeight(14.25);
	$k = $k + 1;
	
	//2��
	$sheetIndex->setCellValue("A$k",iconv("EUC-KR", "UTF-8","�� �� �� ��"));
	$sheetIndex->mergeCells("A$k:J$k");
	$sheetIndex->getStyle("A$k:J$k")->getFont()->setSize(28)->setBold(true);
	$sheetIndex->getRowDimension($k)->setRowHeight(30.50);
	$k = $k + 1;

	//3�� 
	$sheetIndex->setCellValue("A$k", iconv("EUC-KR", "UTF-8","�Ⱓ"))
				->setCellValue("B$k", iconv("EUC-KR", "UTF-8", date("Y�� n��j��",strtotime($start_date))." ~ ".date("Y�� n��j��",strtotime($end_date))))
				->setCellValue("F$k", iconv("EUC-KR", "UTF-8","����ڹ�ȣ"))
				->setCellValue("G$k", iconv("EUC-KR", "UTF-8", $rs_biz_no))
				->setCellValue("J$k", iconv("EUC-KR", "UTF-8", "from..(��)����Ʈ��"));
	$sheetIndex->mergeCells("B$k:E$k");
	$sheetIndex->mergeCells("G$k:I$k");
	$sheetIndex->getStyle("A$k")->getFont()->setBold(true);
	$sheetIndex->getStyle("F$k")->getFont()->setBold(true);
	
	$sheetIndex->getRowDimension($k)->setRowHeight(29.25);
	$k = $k + 1;

	//4��
	$arr_balance = SumCompanyLedger($conn, $cp_type);
	$sheetIndex->setCellValue("A$k", iconv("EUC-KR", "UTF-8","��ü��"))
			   ->setCellValue("B$k", iconv("EUC-KR", "UTF-8", getCompanyNameWithNoCode($conn, $cp_type)))
			   ->setCellValue("F$k", iconv("EUC-KR", "UTF-8","��ǥ��"))
			   ->setCellValue("G$k", iconv("EUC-KR", "UTF-8", $rs_ceo_nm));
			   
	$sheetIndex->mergeCells("B$k:E$k");
	$sheetIndex->mergeCells("G$k:J$k");
	$sheetIndex->getStyle("A$k")->getFont()->setBold(true);
	$sheetIndex->getStyle("F$k")->getFont()->setBold(true);
	$sheetIndex->getRowDimension($k)->setRowHeight(29.25);
	$k = $k + 1;

	//5�� 
	$sheetIndex->setCellValue("A$k", iconv("EUC-KR", "UTF-8","�ּ�"))
			   ->setCellValue("B$k", iconv("EUC-KR", "UTF-8", $rs_cp_zip." ".$rs_cp_addr))
			   ->setCellValue("F$k", iconv("EUC-KR", "UTF-8","��ȭ"))
			   ->setCellValue("G$k", iconv("EUC-KR", "UTF-8", $rs_cp_phone))
			   ->setCellValue("I$k", iconv("EUC-KR", "UTF-8","�ѽ�"))
			   ->setCellValue("J$k", iconv("EUC-KR", "UTF-8", $rs_cp_fax));
			   
	$sheetIndex->mergeCells("B$k:E$k");
	$sheetIndex->mergeCells("G$k:H$k");
	$sheetIndex->getStyle("A$k")->getFont()->setBold(true);
	$sheetIndex->getStyle("F$k")->getFont()->setBold(true);
	$sheetIndex->getStyle("I$k")->getFont()->setBold(true);
	$sheetIndex->getRowDimension($k)->setRowHeight(29.25);
	$k = $k + 1;
	
	//6�� 
	$sheetIndex->setCellValue("F$k", iconv("EUC-KR", "UTF-8","�ܾ�"))
			   ->setCellValue("G$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted(getBalance($conn, $cp_type))."��"));
	$sheetIndex->mergeCells("G$k:J$k");
	$sheetIndex->getStyle("F$k")->getFont()->setBold(true);
	$sheetIndex->getRowDimension($k)->setRowHeight(29.25);
	$k = $k + 1;

	if($rs_cp_type == "����") { 
		$sheetIndex
					->setCellValue("A$k", iconv("EUC-KR", "UTF-8", "��¥"))
					->setCellValue("B$k", iconv("EUC-KR", "UTF-8", "����"))
					->setCellValue("C$k", iconv("EUC-KR", "UTF-8", "��ǰ��"))
					->setCellValue("D$k", iconv("EUC-KR", "UTF-8", "����"))
					->setCellValue("E$k", iconv("EUC-KR", "UTF-8", "�ܰ�"))
					->setCellValue("F$k", iconv("EUC-KR", "UTF-8", "����/���޾�"))
					->setCellValue("G$k", iconv("EUC-KR", "UTF-8", "����/�Աݾ�"))
					->setCellValue("H$k", iconv("EUC-KR", "UTF-8", "�ΰ���"))
					->setCellValue("I$k", iconv("EUC-KR", "UTF-8", "�ܾ�"))
					->setCellValue("J$k", iconv("EUC-KR", "UTF-8", "���"))
					->setCellValue("K$k", iconv("EUC-KR", "UTF-8", "��꼭���࿩��"))
					->setCellValue("L$k", iconv("EUC-KR", "UTF-8", "����ڵ�Ϲ�ȣ"));
	} else {
		$sheetIndex
					->setCellValue("A$k", iconv("EUC-KR", "UTF-8", "��¥"))
					->setCellValue("B$k", iconv("EUC-KR", "UTF-8", "����"))
					->setCellValue("C$k", iconv("EUC-KR", "UTF-8", "��ǰ��"))
					->setCellValue("D$k", iconv("EUC-KR", "UTF-8", "����"))
					->setCellValue("E$k", iconv("EUC-KR", "UTF-8", "�ܰ�"))
					->setCellValue("F$k", iconv("EUC-KR", "UTF-8", "����/���޾�"))
					->setCellValue("G$k", iconv("EUC-KR", "UTF-8", "����/�Աݾ�"))
					->setCellValue("H$k", iconv("EUC-KR", "UTF-8", "�ΰ���"))
					->setCellValue("I$k", iconv("EUC-KR", "UTF-8", "�ܾ�"))
					->setCellValue("J$k", iconv("EUC-KR", "UTF-8", "���"))
					->setCellValue("K$k", iconv("EUC-KR", "UTF-8", "��꼭���࿩��"));
	}
	$sheetIndex->getStyle("A$k:J$k")->getFont()->setBold(true);
	$sheetIndex->getRowDimension($k)->setRowHeight(29);
	$k = $k + 1;

	if (sizeof($arr_rs_prev) > 0) {
		for ($o = 0 ; $o < sizeof($arr_rs_prev); $o++) {
			$BALANCE					= trim($arr_rs_prev[$o]["BALANCE"]);

			$sheetIndex
					->setCellValue("A$k", iconv("EUC-KR", "UTF-8", date("Y-m-d",strtotime("-1 day", strtotime($start_date)))))
					->setCellValue("C$k", iconv("EUC-KR", "UTF-8", "<�����̿�>"))
					->setCellValue("I$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($BALANCE, 0)));
			$sheetIndex->getRowDimension($k)->setRowHeight(29);
			$k = $k + 1;
		}
	}

	//�Ⱓ��
	$period_qty_total = "";
	$period_withdraw_total = ""; 
	$period_deposit_total = "";
	$period_surtax_total = "";
	$period_balance_total = "";

	$month_group = "";
	$month_qty_total = "";
	$month_withdraw_total = ""; 
	$month_deposit_total = "";
	$month_surtax_total = "";
	$month_balance_total = "";
	

	$day_group = "";
	$day_qty_total = "";
	$day_withdraw_total = ""; 
	$day_deposit_total = "";
	$day_surtax_total = "";
	$day_balance_total = "";

	//���ݰ�(����/�����)
	$tax_Y_withdraw_total = 0;
	$tax_Y_deposit_total = 0;
	$tax_N_withdraw_total = 0;
	$tax_N_deposit_total = 0;

	//���ݰ�꼭 ���࿩�� ��
	$invoiced_tax_Y_withdraw_total = 0;
	$invoiced_tax_Y_deposit_total = 0;
	$invoiced_tax_N_withdraw_total = 0;
	$invoiced_tax_N_deposit_total = 0;

	if(sizeof($arr_rs) > 0) {
		
		for($j = 0; $j < sizeof($arr_rs); $j++) {

			$CL_NO						= trim($arr_rs[$j]["CL_NO"]);
			$INOUT_DATE					= trim($arr_rs[$j]["INOUT_DATE"]);
			$INOUT_TYPE					= trim($arr_rs[$j]["INOUT_TYPE"]);
			$NAME						= trim($arr_rs[$j]["NAME"]);
			$QTY						= trim($arr_rs[$j]["QTY"]);
			$UNIT_PRICE					= trim($arr_rs[$j]["UNIT_PRICE"]);
			$WITHDRAW					= trim($arr_rs[$j]["WITHDRAW"]);
			$DEPOSIT					= trim($arr_rs[$j]["DEPOSIT"]);
			$SURTAX						= trim($arr_rs[$j]["SURTAX"]);
			$MEMO						= trim($arr_rs[$j]["MEMO"]);
			$RESERVE_NO					= trim($arr_rs[$j]["RESERVE_NO"]);
			$ORDER_GOODS_NO				= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
			$RGN_NO						= trim($arr_rs[$j]["RGN_NO"]);
			$TO_CP_NO					= trim($arr_rs[$j]["TO_CP_NO"]);
			$USE_TF						= trim($arr_rs[$j]["USE_TF"]);
			$TAX_CONFIRM_TF				= trim($arr_rs[$j]["TAX_CONFIRM_TF"]);
			$TAX_CONFIRM_DATE			= trim($arr_rs[$j]["TAX_CONFIRM_DATE"]);
			$CF_CODE					= trim($arr_rs[$j]["CF_CODE"]);

			if($TAX_CONFIRM_TF == "Y")
				$TAX_CONFIRM_DATE = date("Y-m-d",strtotime($TAX_CONFIRM_DATE));
			else
				$TAX_CONFIRM_DATE = "";

			if($USE_TF == "Y")
				$BALANCE = $BALANCE + $DEPOSIT - $WITHDRAW;
			else { 
				$QTY = 0;
				$WITHDRAW = 0;
				$DEPOSIT = 0;
				$SURTAX = 0;
			}

			$period_qty_total += $QTY;
			$period_withdraw_total += $WITHDRAW;
			$period_deposit_total += $DEPOSIT;
			$period_surtax_total += $SURTAX;
			$period_balance_total = $BALANCE;

			
			$rs_biz_no	= "";
			if($rs_cp_type == "����") { 
				if($TO_CP_NO  > 0) { 
					$arr_rs_company = selectCompany($conn, $TO_CP_NO);
			
					if(sizeof($arr_rs_company)) { 
						$rs_biz_no	= SetStringFromDB($arr_rs_company[0]["BIZ_NO"]); 
					}
				}
			}

			if($INOUT_TYPE == "����") { 
				$TAX_TF = getOrderGoodsTaxTF($conn, $ORDER_GOODS_NO);

				if ($TAX_TF == "�����") {
					$STR_TAX_TF = "�����) ";
				} else {
					$STR_TAX_TF = "����) ";
				}
			} else 
				$STR_TAX_TF = "";
			
			if($INOUT_TYPE == "����" || $INOUT_TYPE == "����") {
				if ($TAX_TF == "�����") {
					$tax_N_withdraw_total += $WITHDRAW;
					$tax_N_deposit_total += $DEPOSIT;

				
					if($TAX_CONFIRM_TF == "Y") { 
						$invoiced_tax_N_withdraw_total += $WITHDRAW;
						$invoiced_tax_N_deposit_total += $DEPOSIT;
					}

				} else { 
					$tax_Y_withdraw_total += $WITHDRAW;
					$tax_Y_deposit_total += $DEPOSIT;

					if($TAX_CONFIRM_TF == "Y") { 
						$invoiced_tax_Y_withdraw_total += $WITHDRAW;
						$invoiced_tax_Y_deposit_total += $DEPOSIT;
					}

				}
			}


			//���� ����� ������ �ϰ� ǥ��
			if($view_daily == "Y") { 
				if($day_group != date("Y-m-d", strtotime($INOUT_DATE)) && $day_group != "" ) { 

					$sheetIndex
						->setCellValue("A$k", iconv("EUC-KR", "UTF-8", "�ϰ� : ".$day_group))
						->setCellValue("D$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($day_qty_total)))
						->setCellValue("F$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($day_deposit_total)))
						->setCellValue("G$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($day_withdraw_total)))
						->setCellValue("H$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($day_surtax_total)))
						->setCellValue("I$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($day_balance_total, 0)));
					$sheetIndex->mergeCells("A$k:C$k");
					$sheetIndex->getStyle("A$k")->getFont()->setBold(true);
					$sheetIndex->getStyle("A$k")->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$sheetIndex->getRowDimension($k)->setRowHeight(27);
					$k = $k + 1;
					
					$day_group = date("Y-m-d",strtotime($INOUT_DATE));
					$day_qty_total = $QTY;
					$day_withdraw_total = $WITHDRAW;
					$day_deposit_total = $DEPOSIT;
					$day_surtax_total = $SURTAX;
					$day_balance_total = $BALANCE;
				} else { 
					
					$day_qty_total += $QTY;
					$day_withdraw_total += $WITHDRAW;
					$day_deposit_total += $DEPOSIT;
					$day_surtax_total += $SURTAX;
					$day_balance_total = $BALANCE;
				}
			}

			//���� ����� ������ ���� ǥ��
			if($month_group != date("Y-m", strtotime($INOUT_DATE)) && $month_group != "" ) { 

				$sheetIndex
					->setCellValue("A$k", iconv("EUC-KR", "UTF-8", "���� : ".$month_group))
					->setCellValue("D$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($month_qty_total)))
					->setCellValue("F$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($month_deposit_total)))
					->setCellValue("G$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($month_withdraw_total)))
					->setCellValue("H$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($month_surtax_total)))
					->setCellValue("I$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($month_balance_total, 0)));
				$sheetIndex->mergeCells("A$k:C$k");
				$sheetIndex->getStyle("A$k")->getFont()->setBold(true);
				$sheetIndex->getStyle("A$k")->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sheetIndex->getRowDimension($k)->setRowHeight(27);
				$k = $k + 1;
				
				$month_group = date("Y-m",strtotime($INOUT_DATE));
				$month_qty_total = $QTY;
				$month_withdraw_total = $WITHDRAW;
				$month_deposit_total = $DEPOSIT;
				$month_surtax_total = $SURTAX;
				$month_balance_total = $BALANCE;
			} else { 
				
				$month_qty_total += $QTY;
				$month_withdraw_total += $WITHDRAW;
				$month_deposit_total += $DEPOSIT;
				$month_surtax_total += $SURTAX;
				$month_balance_total = $BALANCE;
			}

			if($TAX_CONFIRM_TF == "Y") {
								
				if(chkCashStatementByCFCode($conn, $CF_CODE) <= 0)
					$str_tax_class = "����ó��/��������";
				else
					$str_tax_class = "����ó��/��������";
			} else
				$str_tax_class = "�̹���";

			if($rs_cp_type == "����") { 
				$sheetIndex
								->setCellValue("A$k", iconv("EUC-KR", "UTF-8", date("Y-m-d",strtotime($INOUT_DATE))))
								->setCellValue("B$k", iconv("EUC-KR", "UTF-8", $INOUT_TYPE))
								->setCellValue("C$k", iconv("EUC-KR", "UTF-8", $NAME))
								->setCellValue("D$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($QTY)))
								->setCellValue("E$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($UNIT_PRICE)))
								->setCellValue("F$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($DEPOSIT)))
								->setCellValue("G$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($WITHDRAW)))
								->setCellValue("H$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($SURTAX)))
								->setCellValue("I$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($BALANCE, 0)))
								->setCellValue("J$k", iconv("EUC-KR", "UTF-8", $MEMO))
								->setCellValue("K$k", iconv("EUC-KR", "UTF-8", $str_tax_class))
								->setCellValue("L$k", iconv("EUC-KR", "UTF-8", $rs_biz_no))
				;
				$sheetIndex->getStyle("C$k")->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sheetIndex->getStyle("J$k")->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sheetIndex->getStyle("L$k")->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sheetIndex->getRowDimension($k)->setRowHeight(27);
				$k = $k + 1;

			} else { 
				$sheetIndex
								->setCellValue("A$k", iconv("EUC-KR", "UTF-8", date("Y-m-d",strtotime($INOUT_DATE))))
								->setCellValue("B$k", iconv("EUC-KR", "UTF-8", $INOUT_TYPE))
								->setCellValue("C$k", iconv("EUC-KR", "UTF-8", $STR_TAX_TF.$NAME))
								->setCellValue("D$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($QTY)))
								->setCellValue("E$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($UNIT_PRICE)))
								->setCellValue("F$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($DEPOSIT)))
								->setCellValue("G$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($WITHDRAW)))
								->setCellValue("H$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($SURTAX)))
								->setCellValue("I$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($BALANCE, 0)))
								->setCellValue("J$k", iconv("EUC-KR", "UTF-8", $MEMO))
								->setCellValue("K$k", iconv("EUC-KR", "UTF-8", $str_tax_class))
				;
				$sheetIndex->getStyle("C$k")->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sheetIndex->getStyle("J$k")->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sheetIndex->getRowDimension($k)->setRowHeight(27);
				$k = $k + 1;
			}

			if($day_group == "")
				$day_group = date("Y-m-d",strtotime($INOUT_DATE));

			if($month_group == "")
				$month_group = date("Y-m",strtotime($INOUT_DATE));
		}

		if($view_daily == "Y") {
			$sheetIndex
				->setCellValue("A$k", iconv("EUC-KR", "UTF-8", "�ϰ� : ".$day_group))
				->setCellValue("D$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($day_qty_total)))
				->setCellValue("F$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($day_deposit_total)))
				->setCellValue("G$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($day_withdraw_total)))
				->setCellValue("H$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($day_surtax_total)))
				->setCellValue("I$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($day_balance_total, 0)));
			$sheetIndex->mergeCells("A$k:C$k");
			$sheetIndex->getStyle("A$k")->getFont()->setBold(true);
			$sheetIndex->getStyle("A$k")->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sheetIndex->getRowDimension($k)->setRowHeight(27);
			$k = $k + 1;
		}

		$sheetIndex
			->setCellValue("A$k", iconv("EUC-KR", "UTF-8", "���� : ".$month_group))
			->setCellValue("D$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($month_qty_total)))
			->setCellValue("F$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($month_deposit_total)))
			->setCellValue("G$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($month_withdraw_total)))
			->setCellValue("H$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($month_surtax_total)))
			->setCellValue("I$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($month_balance_total, 0)));
		$sheetIndex->mergeCells("A$k:C$k");
		$sheetIndex->getStyle("A$k")->getFont()->setBold(true);
		$sheetIndex->getStyle("A$k")->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheetIndex->getRowDimension($k)->setRowHeight(27);
		$k = $k + 1;

		$sheetIndex
			->setCellValue("A$k", iconv("EUC-KR", "UTF-8", "�Ⱓ�� : "))
			->setCellValue("D$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($period_qty_total)))
			->setCellValue("F$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($period_deposit_total)))
			->setCellValue("G$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($period_withdraw_total)))
			->setCellValue("H$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($period_surtax_total)))
			->setCellValue("I$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($period_balance_total, 0)));
		$sheetIndex->mergeCells("A$k:C$k");
		$sheetIndex->getStyle("A$k")->getFont()->setBold(true);
		$sheetIndex->getStyle("A$k")->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheetIndex->getRowDimension($k)->setRowHeight(27);
		$k = $k + 1;
			
	}

	$o = $k + 1;
	
	$sheetIndex
			->setCellValue("E$o", iconv("EUC-KR", "UTF-8", "���� : "))
			->setCellValue("F$o", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($tax_Y_deposit_total)))
			->setCellValue("G$o", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($tax_Y_withdraw_total)));
	$sheetIndex->getStyle("E$o")->getFont()->setBold(true);
	$sheetIndex->getStyle("E$o")->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheetIndex->getRowDimension($o)->setRowHeight(27);
	$o = $o + 1;
	
	$sheetIndex
			->setCellValue("E$o", iconv("EUC-KR", "UTF-8", "�鼼 : "))
			->setCellValue("F$o", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($tax_N_deposit_total)))
			->setCellValue("G$o", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($tax_N_withdraw_total)));
	$sheetIndex->getStyle("E$o")->getFont()->setBold(true);
	$sheetIndex->getStyle("E$o")->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheetIndex->getRowDimension($o)->setRowHeight(27);
	$o = $o + 1;

	$sheetIndex
			->setCellValue("E$o", iconv("EUC-KR", "UTF-8", "�հ� : "))
			->setCellValue("F$o", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($tax_Y_deposit_total + $tax_N_deposit_total)))
			->setCellValue("G$o", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($tax_Y_withdraw_total + $tax_N_withdraw_total)));
	$sheetIndex->getStyle("E$o")->getFont()->setBold(true);
	$sheetIndex->getStyle("E$o")->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheetIndex->getRowDimension($o)->setRowHeight(27);
	$o = $o + 1;

	$sheetIndex->getStyle("A2:J5")->applyFromArray($BStyle);
	$sheetIndex->getStyle("F6:J6")->applyFromArray($BStyle);
	$sheetIndex->getStyle("A7:J".($k-1))->applyFromArray($BStyle);
	$sheetIndex->getStyle("E".($k).":G".($o-1))->applyFromArray($BStyle);


	$sheetIndex->getStyle("J3")->applyFromArray($underline_style);
	$sheetIndex->getStyle("G6")->applyFromArray($underline_style);


	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('Sheet1');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$sheetIndex;

	// ������ ���������� utf-8�� ��� �ѱ����� �̸��� �����Ƿ� euc-kr�� ��ȯ���ش�.
	//$filename = iconv("UTF-8", "EUC-KR", "MRO -> �����ֹ� ��ȯ -".date("Ymd",strtotime("0 month")));
	$filename = "�ŷ�ó���� - ".date("Ymd",strtotime("0 month"));


	// Redirect output to a client��s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment;filename=".$filename.".xls");
	header('Cache-Control: max-age=0');
 
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

	mysql_close($conn);
	exit;
?>