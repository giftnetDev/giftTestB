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
	$menu_right = "CF012"; // 메뉴마다 셋팅 해 주어야 합니다

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

	if ( count($_GET) == 0 && count($_POST) == 0 ) { 
		if($s_adm_md_tf == "Y")
			$con_sale_adm_no = $s_adm_no;
	}

	$day_0 = date("Y-m-d",strtotime("0 month"));
	$day_1 = date("Y-m-d",strtotime("-1 day"));
	$day_7 = date("Y-m-d",strtotime("-7 day"));
	$day_31 = date("Y-m-d",strtotime("-1 month"));

	if ($start_date == "") {
		$d = new DateTime('first day of this month');
		$start_date = $d->format("Y-m-d");
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));
	} else {
		$end_date = trim($end_date);
	}

	if ( count($_GET) == 0 && count($_POST) == 0 ) { 
		if(date("d",strtotime("0 month")) >= 15)
			$chk_prev_month = "Y";
	}

	 #List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);


#===============================================================
# Get Search list count
#===============================================================

	$con_cp_type = "'판매','판매공급'";
	$filter = array('con_sale_adm_no' => $con_sale_adm_no, 'con_cp_type' => $con_cp_type, 'con_ad_type' => $con_ad_type, 'chk_prev_month' => $chk_prev_month);

	$arr_rs = listAccountReceivableSaleReport($conn, $start_date, $end_date, $filter, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize);

	$nListCnt = sizeof($arr_rs);

	$arr_rs_sum = SumAccountReceivableSaleReport($conn, $start_date, $end_date, $filter);

	require_once "../../_PHPExcel/Classes/PHPExcel.php";

	$objPHPExcel = new PHPExcel();

	$BStyle = array(
	  "font"  => array(
        "size"  => 10,
        "name"  => "Gulim")
	);

	$BStyle_center = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
 
	$BStyle_left = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
        )
    );

	$BStyle_right = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
        )
    );

	$BStyle_background_gray = array(
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'DFDFDF')
        )
    );

	 

	$BStyle_outline = array(
	  "borders" => array(
		"allborders" => array(
		  "style" => PHPExcel_Style_Border::BORDER_THIN
		)
	  )
	);

	$sheetIndex = $objPHPExcel->setActiveSheetIndex(0);

	
	$width = 0.41;
	$sheetIndex->getColumnDimension("A")->setWidth(11 + $width);
	$sheetIndex->getColumnDimension("B")->setWidth(37.8 + $width);
	$sheetIndex->getColumnDimension("C")->setWidth(11 + $width);
	$sheetIndex->getColumnDimension("D")->setWidth(11 + $width);
	$sheetIndex->getColumnDimension("E")->setWidth(11 + $width);
	$sheetIndex->getColumnDimension("F")->setWidth(11 + $width);
	$sheetIndex->getColumnDimension("G")->setWidth(11 + $width);
	$sheetIndex->getColumnDimension("H")->setWidth(25 + $width);
	if($chk_prev_month == "Y") { 
		$sheetIndex->getColumnDimension("I")->setWidth(11 + $width);
		$sheetIndex->getColumnDimension("J")->setWidth(11 + $width);
		$sheetIndex->getColumnDimension("K")->setWidth(11 + $width);
		$sheetIndex->getColumnDimension("L")->setWidth(11 + $width);
	} else { 
		$sheetIndex->getColumnDimension("I")->setWidth(11 + $width);
		$sheetIndex->getColumnDimension("J")->setWidth(11 + $width);
		$sheetIndex->getColumnDimension("K")->setWidth(11 + $width);
	}

	$sheetIndex->getDefaultStyle()->applyFromArray($BStyle);
	$sheetIndex->getDefaultStyle()->applyFromArray($BStyle_center);

	$k = 1;

	$sheetIndex
				->setCellValue("A1", iconv("EUC-KR", "UTF-8", "업체코드"))
				->setCellValue("B1", iconv("EUC-KR", "UTF-8", "업체명"))
				->setCellValue("C1", iconv("EUC-KR", "UTF-8", "이월잔액"))
				->setCellValue("D1", iconv("EUC-KR", "UTF-8", "매출액"))
				->setCellValue("E1", iconv("EUC-KR", "UTF-8", "입금액"));
	if($chk_prev_month == "Y") { 
		$sheetIndex
				->setCellValue("F1", iconv("EUC-KR", "UTF-8", "잔 액"));
	} else { 
		$sheetIndex
				->setCellValue("F1", iconv("EUC-KR", "UTF-8", "전월미수"));
	}
	$sheetIndex
				->setCellValue("G1", iconv("EUC-KR", "UTF-8", "영업담당"))
				->setCellValue("H1", iconv("EUC-KR", "UTF-8", "비고"));
	
	if($chk_prev_month == "Y") { 
		$sheetIndex
				->setCellValue("I1", iconv("EUC-KR", "UTF-8", date("n월",strtotime("0 month"))))
				->setCellValue("J1", iconv("EUC-KR", "UTF-8", date("n월",strtotime("-1 month"))))
				->setCellValue("K1", iconv("EUC-KR", "UTF-8", date("n월",strtotime("-2 month"))))
				->setCellValue("L1", iconv("EUC-KR", "UTF-8", date("n월",strtotime("-3 month"))));
	} else { 
		$sheetIndex
				->setCellValue("I1", iconv("EUC-KR", "UTF-8", date("n월",strtotime("-1 month"))))
				->setCellValue("J1", iconv("EUC-KR", "UTF-8", date("n월",strtotime("-2 month"))))
				->setCellValue("K1", iconv("EUC-KR", "UTF-8", date("n월",strtotime("-3 month"))));

	}
	
	$sheetIndex->getStyle("A$k:H$k")->getFont()->setBold(true);
	$sheetIndex->getRowDimension($k)->setRowHeight(18);
	$k += 1;

	//2017-08-28
	//매입액(계산서발행분_과세)분은 작업비, 택배비등 상품번호가 없는건 상품 기준으로 넣을 수 없기에 원 요청대로 비과세만 집계함 

	if (sizeof($arr_rs) > 0) {
		for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
			
			//CP_NO, CP_CODE, CP_NM, CP_NM2, PREV_BALANCE, SUM_SALES, SUM_COLLECT, SUM_BUYING, SUM_PAID, SUM_BALANCE, SALE_ADM_NO

			$CP_NO						= trim($arr_rs[$j]["CP_NO"]);
			$CP_TYPE					= trim($arr_rs[$j]["CP_TYPE"]);
			$CP_CODE					= trim($arr_rs[$j]["CP_CODE"]);
			$CP_NM						= trim($arr_rs[$j]["CP_NM"]);
			$CP_NM2						= trim($arr_rs[$j]["CP_NM2"]);
			$PREV_BALANCE				= trim($arr_rs[$j]["PREV_BALANCE"]);
			$SUM_SALES					= trim($arr_rs[$j]["SUM_SALES"]);
			$SUM_COLLECT				= trim($arr_rs[$j]["SUM_COLLECT"]);
			$EXCEPT_SALE				= trim($arr_rs[$j]["EXCEPT_SALE"]);
			$SUM_BALANCE				= trim($arr_rs[$j]["SUM_BALANCE"]);
			$SALE_ADM_NO				= trim($arr_rs[$j]["SALE_ADM_NO"]);
			$MEMO						= trim($arr_rs[$j]["MEMO"]);
			$PREV_0						= trim($arr_rs[$j]["PREV_0"]);
			$PREV_1						= trim($arr_rs[$j]["PREV_1"]);
			$PREV_2						= trim($arr_rs[$j]["PREV_2"]);
			$PREV_3						= trim($arr_rs[$j]["PREV_3"]);
			$EXCEPT_TF					= trim($arr_rs[$j]["EXCEPT_TF"]);
							
			

			$SALE_ADM_NM = getAdminName($conn, $SALE_ADM_NO);


			$sheetIndex
				->setCellValue("A$k", iconv("EUC-KR", "UTF-8", $CP_CODE))
				->setCellValue("B$k", iconv("EUC-KR", "UTF-8", $CP_NM." ".$CP_NM2))
				->setCellValue("C$k", iconv("EUC-KR", "UTF-8", $PREV_BALANCE))
				->setCellValue("D$k", iconv("EUC-KR", "UTF-8", $SUM_SALES))
				->setCellValue("E$k", iconv("EUC-KR", "UTF-8", $SUM_COLLECT));

			if($chk_prev_month == "Y") { 
				$sheetIndex
						->setCellValue("F$k", iconv("EUC-KR", "UTF-8", $SUM_BALANCE));
			} else { 
				$sheetIndex
						->setCellValue("F$k", iconv("EUC-KR", "UTF-8", $EXCEPT_SALE));
			}
			$sheetIndex
				->setCellValue("G$k", iconv("EUC-KR", "UTF-8", $SALE_ADM_NM))
				->setCellValue("H$k", iconv("EUC-KR", "UTF-8", $MEMO));

			if($chk_prev_month == "Y") { 

				if($PREV_0 == 0)
					$sheetIndex
						->setCellValue("I$k", iconv("EUC-KR", "UTF-8", $SUM_SALES));
				else
					$sheetIndex
						->setCellValue("I$k", iconv("EUC-KR", "UTF-8", $PREV_0));
				$sheetIndex
					->setCellValue("J$k", iconv("EUC-KR", "UTF-8", $PREV_1))
					->setCellValue("K$k", iconv("EUC-KR", "UTF-8", $PREV_2))
					->setCellValue("L$k", iconv("EUC-KR", "UTF-8", $PREV_3));
				
			} else { 
				$sheetIndex
					->setCellValue("I$k", iconv("EUC-KR", "UTF-8", $PREV_1))
					->setCellValue("J$k", iconv("EUC-KR", "UTF-8", $PREV_2))
					->setCellValue("K$k", iconv("EUC-KR", "UTF-8", $PREV_3));
			}

			$sheetIndex->getStyle("B$k")->applyFromArray($BStyle_left);
			$sheetIndex->getStyle("H$k")->applyFromArray($BStyle_left);

			if($EXCEPT_TF == "Y")
				$sheetIndex->getStyle("A$k:L$k")->applyFromArray($BStyle_background_gray);

			$k = $k + 1;
		}
	}
	$o = $k - 1;

	
	$sheetIndex->getStyle("A1:H$o")->applyFromArray($BStyle_outline);
	$sheetIndex->getStyle("C2:G$o")->getNumberFormat()->setFormatCode('#,##0');
	$sheetIndex->getStyle("C2:G$o")->applyFromArray($BStyle_right);
	$sheetIndex->getStyle("I2:L$o")->getNumberFormat()->setFormatCode('#,##0');
	$sheetIndex->getStyle("I2:L$o")->applyFromArray($BStyle_right);

	$k = $k + 3;

	if (sizeof($arr_rs_sum) > 0 && $search_str == "") {

		$ALL_TOTAL_PREV_BALANCE		= 0;
		$ALL_TOTAL_SUM_SALES		= 0;
		$ALL_TOTAL_SUM_SALES_TAX	= 0;
		$ALL_TOTAL_SUM_COLLECT		= 0;
		$ALL_TOTAL_SUM_BALANCE		= 0;
		$ALL_TOTAL_EXCEPT_SALE		= 0;

		$ALL_TOTAL_SUM_PREV_0		= 0;
		$ALL_TOTAL_SUM_PREV_1		= 0;
		$ALL_TOTAL_SUM_PREV_2		= 0;
		$ALL_TOTAL_SUM_PREV_3		= 0;


		for ($o = 0 ; $o < sizeof($arr_rs_sum); $o++) {
			
			//CP_NO, CP_CODE, CP_NM, CP_NM2, PREV_BALANCE, SUM_SALES, SUM_COLLECT, SUM_BUYING, SUM_PAID, SUM_BALANCE, SALE_ADM_NO

			$TOTAL_PREV_BALANCE				= trim($arr_rs_sum[$o]["TOTAL_PREV_BALANCE"]);
			$TOTAL_SUM_SALES				= trim($arr_rs_sum[$o]["TOTAL_SUM_SALES"]);
			$TOTAL_SUM_SALES_TAX			= trim($arr_rs_sum[$o]["TOTAL_SUM_SALES_TAX"]);
			$TOTAL_SUM_COLLECT				= trim($arr_rs_sum[$o]["TOTAL_SUM_COLLECT"]);
			$TOTAL_EXCEPT_SALE				= trim($arr_rs_sum[$o]["TOTAL_EXCEPT_SALE"]);
			$TOTAL_SUM_BALANCE				= trim($arr_rs_sum[$o]["TOTAL_SUM_BALANCE"]);

			$TOTAL_SUM_PREV_0				= trim($arr_rs_sum[$o]["TOTAL_SUM_PREV_0"]);
			$TOTAL_SUM_PREV_1				= trim($arr_rs_sum[$o]["TOTAL_SUM_PREV_1"]);
			$TOTAL_SUM_PREV_2				= trim($arr_rs_sum[$o]["TOTAL_SUM_PREV_2"]);
			$TOTAL_SUM_PREV_3				= trim($arr_rs_sum[$o]["TOTAL_SUM_PREV_3"]);

			$GROUP_SALE_ADM_NO				= trim($arr_rs_sum[$o]["SALE_ADM_NO"]);


			$ALL_TOTAL_PREV_BALANCE		+= $TOTAL_PREV_BALANCE;
			$ALL_TOTAL_SUM_SALES		+= $TOTAL_SUM_SALES;
			$ALL_TOTAL_SUM_SALES_TAX	+= $TOTAL_SUM_SALES_TAX;
			$ALL_TOTAL_SUM_COLLECT		+= $TOTAL_SUM_COLLECT;
			$ALL_TOTAL_EXCEPT_SALE      += $TOTAL_EXCEPT_SALE;
			$ALL_TOTAL_SUM_BALANCE		+= $TOTAL_SUM_BALANCE;

			$ALL_TOTAL_SUM_PREV_0		+= $TOTAL_SUM_PREV_0;
			$ALL_TOTAL_SUM_PREV_1		+= $TOTAL_SUM_PREV_1;
			$ALL_TOTAL_SUM_PREV_2		+= $TOTAL_SUM_PREV_2;
			$ALL_TOTAL_SUM_PREV_3		+= $TOTAL_SUM_PREV_3;

			$GROUP_SALE_ADM_NM = getAdminName($conn, $GROUP_SALE_ADM_NO);

			if(sizeof($arr_rs_sum) > 2) {
				$sheetIndex
					->setCellValue("B$k", iconv("EUC-KR", "UTF-8", $GROUP_SALE_ADM_NM." 합계 :"))
					->setCellValue("C$k", iconv("EUC-KR", "UTF-8", $TOTAL_PREV_BALANCE))
					->setCellValue("D$k", iconv("EUC-KR", "UTF-8", $TOTAL_SUM_SALES))
					->setCellValue("E$k", iconv("EUC-KR", "UTF-8", $TOTAL_SUM_COLLECT));
				if($chk_prev_month == "Y") { 
					$sheetIndex
							->setCellValue("F$k", iconv("EUC-KR", "UTF-8", $TOTAL_SUM_BALANCE));
				} else { 
					$sheetIndex
							->setCellValue("F$k", iconv("EUC-KR", "UTF-8", $TOTAL_EXCEPT_SALE));
				}

				if($chk_prev_month == "Y") { 
					$sheetIndex
						->setCellValue("I$k", iconv("EUC-KR", "UTF-8", $TOTAL_SUM_PREV_0))
						->setCellValue("J$k", iconv("EUC-KR", "UTF-8", $TOTAL_SUM_PREV_1))
						->setCellValue("K$k", iconv("EUC-KR", "UTF-8", $TOTAL_SUM_PREV_2))
						->setCellValue("L$k", iconv("EUC-KR", "UTF-8", $TOTAL_SUM_PREV_3));
				} else {
					$sheetIndex
						->setCellValue("I$k", iconv("EUC-KR", "UTF-8", $TOTAL_SUM_PREV_1))
						->setCellValue("J$k", iconv("EUC-KR", "UTF-8", $TOTAL_SUM_PREV_2))
						->setCellValue("K$k", iconv("EUC-KR", "UTF-8", $TOTAL_SUM_PREV_3));
				}
				
				$sheetIndex->getStyle("B$k")->getFont()->setBold(true);
				$sheetIndex->getStyle("C$k:G$k")->getNumberFormat()->setFormatCode('#,##0');
				$sheetIndex->getStyle("C$k:G$k")->applyFromArray($BStyle_right);
				$sheetIndex->getStyle("I$k:L$k")->getNumberFormat()->setFormatCode('#,##0');
				$sheetIndex->getStyle("I$k:L$k")->applyFromArray($BStyle_right);
				$k = $k + 1;
			}

		}

		$sheetIndex
				->setCellValue("B$k", iconv("EUC-KR", "UTF-8", " 전체 합계: "))
				->setCellValue("C$k", iconv("EUC-KR", "UTF-8", $ALL_TOTAL_PREV_BALANCE))
				->setCellValue("D$k", iconv("EUC-KR", "UTF-8", $ALL_TOTAL_SUM_SALES))
				->setCellValue("E$k", iconv("EUC-KR", "UTF-8", $ALL_TOTAL_SUM_COLLECT));
		if($chk_prev_month == "Y") { 
			$sheetIndex
					->setCellValue("F$k", iconv("EUC-KR", "UTF-8", $ALL_TOTAL_SUM_BALANCE));
		} else { 
			$sheetIndex
					->setCellValue("F$k", iconv("EUC-KR", "UTF-8", $ALL_TOTAL_EXCEPT_SALE));
		}
		
		if($chk_prev_month == "Y") { 
			$sheetIndex
				->setCellValue("I$k", iconv("EUC-KR", "UTF-8", $ALL_TOTAL_SUM_PREV_0))
				->setCellValue("J$k", iconv("EUC-KR", "UTF-8", $ALL_TOTAL_SUM_PREV_1))
				->setCellValue("K$k", iconv("EUC-KR", "UTF-8", $ALL_TOTAL_SUM_PREV_2))
				->setCellValue("L$k", iconv("EUC-KR", "UTF-8", $ALL_TOTAL_SUM_PREV_3));
		} else {
			$sheetIndex
				->setCellValue("I$k", iconv("EUC-KR", "UTF-8", $ALL_TOTAL_SUM_PREV_1))
				->setCellValue("J$k", iconv("EUC-KR", "UTF-8", $ALL_TOTAL_SUM_PREV_2))
				->setCellValue("K$k", iconv("EUC-KR", "UTF-8", $ALL_TOTAL_SUM_PREV_3));
		}
			
		$sheetIndex->getStyle("B$k")->getFont()->setBold(true);
		$sheetIndex->getStyle("C$k:G$k")->getNumberFormat()->setFormatCode('#,##0');
		$sheetIndex->getStyle("C$k:G$k")->applyFromArray($BStyle_right);
		$sheetIndex->getStyle("I$k:L$k")->getNumberFormat()->setFormatCode('#,##0');
		$sheetIndex->getStyle("I$k:L$k")->applyFromArray($BStyle_right);

		$k = $k + 1;
	}

	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('Sheet1');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
	//$filename = iconv("UTF-8", "EUC-KR", "MRO -> 통합주문 변환 -".date("Ymd",strtotime("0 month")));
	$filename = "매출미수보고 - ".date("Ymd",strtotime("0 month"));

	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment;filename=".$filename.".xls");
	header('Cache-Control: max-age=0');
 
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

	mysql_close($conn);
	exit;
?>