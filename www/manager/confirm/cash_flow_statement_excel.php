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
	$menu_right = "CF008"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/confirm/confirm.php";
	require "../../_classes/biz/company/company.php";

#====================================================================
# Request Parameter
#====================================================================

	if ($start_date == "") {
		$d = new DateTime('first day of this month');
		$start_date = $d->format("Y-m-d");
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));;
	} else {
		$end_date = trim($end_date);
	}

	if($search_date_type == "")
		$search_date_type = "out_date";

	 #List Parameter
	$nPage		= 1;
	$nPageSize	= 10000;

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);

#===============================================================
# Get Search list count
#===============================================================


	$filter = array('op_cp_no' => $op_cp_no, 'account_cp_no' => $account_cp_no, 'sale_cp_no' => $sale_cp_no, 'sale_adm_no' => $sale_adm_no, 'cf_inout' => $cf_inout, 'cf_type' => $cf_type, 'has_in_cash' => $has_in_cash, 'match_tf' => $match_tf);

	$nListCnt = totalCntCashFlow($conn, $search_date_type, $start_date, $end_date, $filter, $search_field, $search_str);

	$nTotalPage = (int)(($nListCnt - 1) / (int)$nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listCashFlow($conn, $search_date_type, $start_date, $end_date, $filter, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize, $nListCnt);

	$arr_rs_sum = sumCashFlow($conn, $search_date_type, $start_date, $end_date, $filter, $search_field, $search_str);

	require_once "../../_PHPExcel/Classes/PHPExcel.php";

	$objPHPExcel = new PHPExcel();

	$sheetIndex = $objPHPExcel->setActiveSheetIndex(0);

	$w = 0.75;
	$sheetIndex->getColumnDimension("A")->setWidth(14.86 + $w);
	$sheetIndex->getColumnDimension("B")->setWidth(14.86 + $w);
	$sheetIndex->getColumnDimension("C")->setWidth(31.71 + $w);
	$sheetIndex->getColumnDimension("D")->setWidth(15.43 + $w);
	$sheetIndex->getColumnDimension("E")->setWidth(24.71 + $w);
	$sheetIndex->getColumnDimension("F")->setWidth(11.57 + $w);
	$sheetIndex->getColumnDimension("G")->setWidth(11.57 + $w);
	$sheetIndex->getColumnDimension("H")->setWidth(12.57 + $w);
	$sheetIndex->getColumnDimension("I")->setWidth(10.57 + $w);
	
	$style = array(
		"font"  => array("name"  => "Gulim"),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ),
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
   

	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A1", iconv("EUC-KR", "UTF-8", "구분"))
				->setCellValue("B1", iconv("EUC-KR", "UTF-8", "종류"))
				->setCellValue("C1", iconv("EUC-KR", "UTF-8", "승인종류"))
				->setCellValue("D1", iconv("EUC-KR", "UTF-8", "승인번호"))
				->setCellValue("E1", iconv("EUC-KR", "UTF-8", "사업자번호"))
				->setCellValue("F1", iconv("EUC-KR", "UTF-8", "상호"))
				->setCellValue("G1", iconv("EUC-KR", "UTF-8", "발행일자"))
				->setCellValue("H1", iconv("EUC-KR", "UTF-8", "작성일자"))
				->setCellValue("I1", iconv("EUC-KR", "UTF-8", "합계액"))
				->setCellValue("J1", iconv("EUC-KR", "UTF-8", "입금액"))
				->setCellValue("K1", iconv("EUC-KR", "UTF-8", "잔액"))
				->setCellValue("L1", iconv("EUC-KR", "UTF-8", "영업사원"))
				->setCellValue("M1", iconv("EUC-KR", "UTF-8", "매치여부"))
				->setCellValue("N1", iconv("EUC-KR", "UTF-8", "확인여부"));

	$sheetIndex->getStyle("A1:N1")->getFont()->setSize(11)->setBold(true);

	$k = 2;
	if (sizeof($arr_rs) > 0) {
		for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
			
			$CF_NO			= SetStringFromDB($arr_rs[$j]["CF_NO"]);
			$CF_TYPE		= SetStringFromDB($arr_rs[$j]["CF_TYPE"]);
			$CF_INOUT		= SetStringFromDB($arr_rs[$j]["CF_INOUT"]);
			$CF_CODE		= SetStringFromDB($arr_rs[$j]["CF_CODE"]);
			$OP_CP_NO		= SetStringFromDB($arr_rs[$j]["OP_CP_NO"]);
			$ACCOUNT_CP_NO	= SetStringFromDB($arr_rs[$j]["ACCOUNT_CP_NO"]);
			$SALE_CP_NO		= SetStringFromDB($arr_rs[$j]["SALE_CP_NO"]);
			$BIZ_NO			= SetStringFromDB($arr_rs[$j]["BIZ_NO"]);
			$CP_NM			= SetStringFromDB($arr_rs[$j]["CP_NM"]);
			$OUT_DATE		= SetStringFromDB($arr_rs[$j]["OUT_DATE"]);
			$WRITTEN_DATE	= SetStringFromDB($arr_rs[$j]["WRITTEN_DATE"]);
			$IN_DATE		= SetStringFromDB($arr_rs[$j]["IN_DATE"]);
			$CASH			= SetStringFromDB($arr_rs[$j]["CASH"]);
			
			$SUPPLY_PRICE	= SetStringFromDB($arr_rs[$j]["SUPPLY_PRICE"]);
			$SURTAX			= SetStringFromDB($arr_rs[$j]["SURTAX"]);
			$TOTAL_PRICE	= SetStringFromDB($arr_rs[$j]["TOTAL_PRICE"]);

			$SALE_ADM_NO	= SetStringFromDB($arr_rs[$j]["SALE_ADM_NO"]);
			$MATCH_TF		= SetStringFromDB($arr_rs[$j]["MATCH_TF"]);
			$CHECK_TF		= SetStringFromDB($arr_rs[$j]["CHECK_TF"]);
			
			$OP_CP_NM		= getCompanyNameWithNoCode($conn, $OP_CP_NO);
			$SALE_ADM_NM	= getAdminName($conn, $SALE_ADM_NO); 

			$CF_TYPE = getDcodeName($conn, 'CASH_STATEMENT_TYPE', $CF_TYPE);
			
			if($OUT_DATE <> "0000-00-00")
				$OUT_DATE		= date("Y-m-d",strtotime($OUT_DATE));
			else 
				$OUT_DATE = "";

			if($WRITTEN_DATE <> "0000-00-00")
				$WRITTEN_DATE	= date("Y-m-d",strtotime($WRITTEN_DATE));
			else 
				$WRITTEN_DATE = "";

			if($MATCH_TF == "Y")
				$str_row_match = "매치됨";
			else
				$str_row_match = "매치안됨";

			if($CHECK_TF == "Y")
				$str_row_check = "확인";
			else
				$str_row_check = "미확인";

			if($CASH == 0) {
				if($CF_INOUT == "매출" && $TOTAL_PRICE > 0) {
					$CASH = "입금전";
				} else {
					$CASH = "";
				}
			} else
				$CASH = getSafeNumberFormatted($CASH);

			if($CASH == 0) {
				$REMAIN = "";
			} else { 
				$REMAIN = $TOTAL_PRICE - $CASH;
				$REMAIN = getSafeNumberFormatted($REMAIN);
			}



			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A$k", iconv("EUC-KR", "UTF-8", $OP_CP_NM))
				->setCellValue("B$k", iconv("EUC-KR", "UTF-8", $CF_INOUT))
				->setCellValue("C$k", iconv("EUC-KR", "UTF-8", $CF_TYPE))
				->setCellValue("D$k", iconv("EUC-KR", "UTF-8", $CF_CODE))
				->setCellValue("E$k", iconv("EUC-KR", "UTF-8", $BIZ_NO))
				->setCellValue("F$k", iconv("EUC-KR", "UTF-8", $CP_NM))
				->setCellValue("G$k", iconv("EUC-KR", "UTF-8", $OUT_DATE))
				->setCellValue("H$k", iconv("EUC-KR", "UTF-8", $WRITTEN_DATE))
				->setCellValue("I$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($TOTAL_PRICE)))
				->setCellValue("J$k", iconv("EUC-KR", "UTF-8", $CASH))
				->setCellValue("K$k", iconv("EUC-KR", "UTF-8", $REMAIN))
				->setCellValue("L$k", iconv("EUC-KR", "UTF-8", $SALE_ADM_NM))
				->setCellValue("M$k", iconv("EUC-KR", "UTF-8", $str_row_match))
				->setCellValue("N$k", iconv("EUC-KR", "UTF-8", $str_row_check));

			$k = $k + 1;
		}
	} else { 
		$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A$k", iconv("EUC-KR", "UTF-8", '등록된 내역이 없습니다.'));
		$sheetIndex->mergeCells("A$k:H$k");
		$k += 1;
	}

	
	$k = $k + 1;

	for ($j = 0 ; $j < sizeof($arr_rs_sum); $j++) {
		$SUM_SUPPLY_PRICE	= SetStringFromDB($arr_rs_sum[$j]["SUM_SUPPLY_PRICE"]);
		$SUM_SURTAX			= SetStringFromDB($arr_rs_sum[$j]["SUM_SURTAX"]);
		$SUM_TOTAL_PRICE	= SetStringFromDB($arr_rs_sum[$j]["SUM_TOTAL_PRICE"]);

		$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("B$k", iconv("EUC-KR", "UTF-8", " 합계: "))
				->setCellValue("I$k", iconv("EUC-KR", "UTF-8", getSafeNumberFormatted($TOTAL_PRICE)));

		$k = $k + 1;
	}

	$sheetIndex->getStyle("A1:M".($k-1))->applyFromArray($style);
	$sheetIndex->getStyle("A1:M".($k-1))->applyFromArray($BStyle_outline);
	$sheetIndex->getStyle("D1:D".($k-1))->applyFromArray($style_left);
	$sheetIndex->getStyle("H1:J".($k-1))->applyFromArray($style_right);
	
	
	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle("sheet1");

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
	//$filename = iconv("UTF-8", "EUC-KR", "MRO -> 통합주문 변환 -".date("Ymd",strtotime("0 month")));

	
	$filename = "자금총괄표-".date("Ymd",strtotime("0 month"));

	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment;filename=".$filename.".xls");
	header('Cache-Control: max-age=0');
 
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

	mysql_close($conn);
	exit;
?>