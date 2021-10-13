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

	 #List Parameter
	$nPage		= 1;
	$nPageSize	= 10000;

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);

#===============================================================
# Get Search list count
#===============================================================

	$filter = array('con_sale_adm_no' => $con_sale_adm_no, 'con_cp_type' => $con_cp_type, 'con_ad_type' => $con_ad_type);

	$nListCnt = totalCntAccountReceivable($conn, $start_date, $end_date, $filter, $search_field, $search_str);
	
	#echo $nListCnt;

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listAccountReceivable($conn, $start_date, $end_date, $filter, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize);

	$arr_rs_sum = SumAccountReceivable($conn, $start_date, $end_date, $filter);

	require_once "../../_PHPExcel/Classes/PHPExcel.php";

	$objPHPExcel = new PHPExcel();

	//업체구분	업체코드	업체명	 이월잔액	매출액	 입금액	매입액	 지급액	잔 액	 영업사원
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A1", iconv("EUC-KR", "UTF-8", "업체구분"))
				->setCellValue("B1", iconv("EUC-KR", "UTF-8", "업체코드"))
				->setCellValue("C1", iconv("EUC-KR", "UTF-8", "업체명"))
				->setCellValue("D1", iconv("EUC-KR", "UTF-8", "이월잔액"))
				->setCellValue("E1", iconv("EUC-KR", "UTF-8", "매출액"))
				->setCellValue("F1", iconv("EUC-KR", "UTF-8", "매출액(계산서발행분)"))
				->setCellValue("G1", iconv("EUC-KR", "UTF-8", "입금액"))
				->setCellValue("H1", iconv("EUC-KR", "UTF-8", "매입액"))
				->setCellValue("I1", iconv("EUC-KR", "UTF-8", "매입액(계산서발행분)"))
				->setCellValue("J1", iconv("EUC-KR", "UTF-8", "매입액(계산서발행분_비과세)"))
				->setCellValue("K1", iconv("EUC-KR", "UTF-8", "지급액"))
				->setCellValue("L1", iconv("EUC-KR", "UTF-8", "잔 액"))
				->setCellValue("M1", iconv("EUC-KR", "UTF-8", "영업사원"));

	//2017-08-28
	//매입액(계산서발행분_과세)분은 작업비, 택배비등 상품번호가 없는건 상품 기준으로 넣을 수 없기에 원 요청대로 비과세만 집계함 

	if (sizeof($arr_rs) > 0) {
		$k = 2;
		for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
			
			//CP_NO, CP_CODE, CP_NM, CP_NM2, PREV_BALANCE, SUM_SALES, SUM_COLLECT, SUM_BUYING, SUM_PAID, SUM_BALANCE, SALE_ADM_NO

			$CP_NO						= trim($arr_rs[$j]["CP_NO"]);
			$CP_TYPE					= trim($arr_rs[$j]["CP_TYPE"]);
			$CP_CODE					= trim($arr_rs[$j]["CP_CODE"]);
			$CP_NM						= trim($arr_rs[$j]["CP_NM"]);
			$CP_NM2						= trim($arr_rs[$j]["CP_NM2"]);
			$PREV_BALANCE				= trim($arr_rs[$j]["PREV_BALANCE"]);
			$SUM_SALES					= trim($arr_rs[$j]["SUM_SALES"]);
			$SUM_SALES_TAX				= trim($arr_rs[$j]["SUM_SALES_TAX"]);
			$SUM_COLLECT				= trim($arr_rs[$j]["SUM_COLLECT"]);
			$SUM_BUYING					= trim($arr_rs[$j]["SUM_BUYING"]);
			$SUM_BUYING_TAX				= trim($arr_rs[$j]["SUM_BUYING_TAX"]);
			$SUM_BUYING_TAX_Y			= trim($arr_rs[$j]["SUM_BUYING_TAX_Y"]);
			$SUM_BUYING_TAX_N			= trim($arr_rs[$j]["SUM_BUYING_TAX_N"]);
			$SUM_PAID					= trim($arr_rs[$j]["SUM_PAID"]);
			$SUM_BALANCE				= trim($arr_rs[$j]["SUM_BALANCE"]);
			$SALE_ADM_NO				= trim($arr_rs[$j]["SALE_ADM_NO"]);

			$SALE_ADM_NM = getAdminName($conn, $SALE_ADM_NO);


			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A$k", iconv("EUC-KR", "UTF-8", $CP_TYPE))
				->setCellValue("B$k", iconv("EUC-KR", "UTF-8", $CP_CODE))
				->setCellValue("C$k", iconv("EUC-KR", "UTF-8", $CP_NM." ".$CP_NM2))
				->setCellValue("D$k", iconv("EUC-KR", "UTF-8", $PREV_BALANCE))
				->setCellValue("E$k", iconv("EUC-KR", "UTF-8", $SUM_SALES))
				->setCellValue("F$k", iconv("EUC-KR", "UTF-8", $SUM_SALES_TAX))
				->setCellValue("G$k", iconv("EUC-KR", "UTF-8", $SUM_COLLECT))
				->setCellValue("H$k", iconv("EUC-KR", "UTF-8", $SUM_BUYING))
				->setCellValue("I$k", iconv("EUC-KR", "UTF-8", $SUM_BUYING_TAX))
				->setCellValue("J$k", iconv("EUC-KR", "UTF-8", $SUM_BUYING_TAX_N))
				->setCellValue("K$k", iconv("EUC-KR", "UTF-8", $SUM_PAID))
				->setCellValue("L$k", iconv("EUC-KR", "UTF-8", $SUM_BALANCE))
				->setCellValue("M$k", iconv("EUC-KR", "UTF-8", $SALE_ADM_NM));

			$k = $k + 1;
		}
	}

	$k = $k + 3;

	if (sizeof($arr_rs_sum) > 0 && $search_str == "") {

		$ALL_TOTAL_PREV_BALANCE		= 0;
		$ALL_TOTAL_SUM_SALES		= 0;
		$ALL_TOTAL_SUM_SALES_TAX	= 0;
		$ALL_TOTAL_SUM_COLLECT		= 0;
		$ALL_TOTAL_SUM_BUYING		= 0;
		$ALL_TOTAL_SUM_BUYING_TAX	= 0;
		$ALL_TOTAL_SUM_BUYING_TAX_Y	= 0;
		$ALL_TOTAL_SUM_BUYING_TAX_N	= 0;
		$ALL_TOTAL_SUM_PAID			= 0;
		$ALL_TOTAL_SUM_BALANCE		= 0;


		for ($o = 0 ; $o < sizeof($arr_rs_sum); $o++) {
			
			//CP_NO, CP_CODE, CP_NM, CP_NM2, PREV_BALANCE, SUM_SALES, SUM_COLLECT, SUM_BUYING, SUM_PAID, SUM_BALANCE, SALE_ADM_NO

			$TOTAL_PREV_BALANCE				= trim($arr_rs_sum[$o]["TOTAL_PREV_BALANCE"]);
			$TOTAL_SUM_SALES				= trim($arr_rs_sum[$o]["TOTAL_SUM_SALES"]);
			$TOTAL_SUM_SALES_TAX			= trim($arr_rs_sum[$o]["TOTAL_SUM_SALES_TAX"]);
			$TOTAL_SUM_COLLECT				= trim($arr_rs_sum[$o]["TOTAL_SUM_COLLECT"]);
			$TOTAL_SUM_BUYING				= trim($arr_rs_sum[$o]["TOTAL_SUM_BUYING"]);
			$TOTAL_SUM_BUYING_TAX			= trim($arr_rs_sum[$o]["TOTAL_SUM_BUYING_TAX"]);
			$TOTAL_SUM_BUYING_TAX_Y			= trim($arr_rs_sum[$o]["TOTAL_SUM_BUYING_TAX_Y"]);
			$TOTAL_SUM_BUYING_TAX_N			= trim($arr_rs_sum[$o]["TOTAL_SUM_BUYING_TAX_N"]);
			$TOTAL_SUM_PAID					= trim($arr_rs_sum[$o]["TOTAL_SUM_PAID"]);
			$TOTAL_SUM_BALANCE				= trim($arr_rs_sum[$o]["TOTAL_SUM_BALANCE"]);
			$GROUP_SALE_ADM_NO				= trim($arr_rs_sum[$o]["SALE_ADM_NO"]);


			$ALL_TOTAL_PREV_BALANCE		+= $TOTAL_PREV_BALANCE;
			$ALL_TOTAL_SUM_SALES		+= $TOTAL_SUM_SALES;
			$ALL_TOTAL_SUM_SALES_TAX	+= $TOTAL_SUM_SALES_TAX;
			$ALL_TOTAL_SUM_COLLECT		+= $TOTAL_SUM_COLLECT;
			$ALL_TOTAL_SUM_BUYING		+= $TOTAL_SUM_BUYING;
			$ALL_TOTAL_SUM_BUYING_TAX	+= $TOTAL_SUM_BUYING_TAX;
			$ALL_TOTAL_SUM_BUYING_TAX_Y	+= $TOTAL_SUM_BUYING_TAX_Y;
			$ALL_TOTAL_SUM_BUYING_TAX_N	+= $TOTAL_SUM_BUYING_TAX_N;
			$ALL_TOTAL_SUM_PAID			+= $TOTAL_SUM_PAID;
			$ALL_TOTAL_SUM_BALANCE		+= $TOTAL_SUM_BALANCE;

			$GROUP_SALE_ADM_NM = getAdminName($conn, $GROUP_SALE_ADM_NO);

			if(sizeof($arr_rs_sum) > 2) {
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("C$k", iconv("EUC-KR", "UTF-8", $GROUP_SALE_ADM_NM." 합계 :"))
					->setCellValue("D$k", iconv("EUC-KR", "UTF-8", $TOTAL_PREV_BALANCE))
					->setCellValue("E$k", iconv("EUC-KR", "UTF-8", $TOTAL_SUM_SALES))
					->setCellValue("F$k", iconv("EUC-KR", "UTF-8", $TOTAL_SUM_SALES_TAX))
					->setCellValue("G$k", iconv("EUC-KR", "UTF-8", $TOTAL_SUM_COLLECT))
					->setCellValue("H$k", iconv("EUC-KR", "UTF-8", $TOTAL_SUM_BUYING))
					->setCellValue("I$k", iconv("EUC-KR", "UTF-8", $TOTAL_SUM_BUYING_TAX))
					->setCellValue("J$k", iconv("EUC-KR", "UTF-8", $TOTAL_SUM_BUYING_TAX_N))
					->setCellValue("K$k", iconv("EUC-KR", "UTF-8", $TOTAL_SUM_PAID))
					->setCellValue("L$k", iconv("EUC-KR", "UTF-8", $TOTAL_SUM_BALANCE));

				$k = $k + 1;
			}

		}

		$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("C$k", iconv("EUC-KR", "UTF-8", " 전체 합계: "))
				->setCellValue("D$k", iconv("EUC-KR", "UTF-8", $ALL_TOTAL_PREV_BALANCE))
				->setCellValue("E$k", iconv("EUC-KR", "UTF-8", $ALL_TOTAL_SUM_SALES))
				->setCellValue("F$k", iconv("EUC-KR", "UTF-8", $ALL_TOTAL_SUM_SALES_TAX))
				->setCellValue("G$k", iconv("EUC-KR", "UTF-8", $ALL_TOTAL_SUM_COLLECT))
				->setCellValue("H$k", iconv("EUC-KR", "UTF-8", $ALL_TOTAL_SUM_BUYING))
				->setCellValue("I$k", iconv("EUC-KR", "UTF-8", $ALL_TOTAL_SUM_BUYING_TAX))
				->setCellValue("J$k", iconv("EUC-KR", "UTF-8", $ALL_TOTAL_SUM_BUYING_TAX_N))
				->setCellValue("K$k", iconv("EUC-KR", "UTF-8", $ALL_TOTAL_SUM_PAID))
				->setCellValue("L$k", iconv("EUC-KR", "UTF-8", $ALL_TOTAL_SUM_BALANCE));

		$k = $k + 1;
	}

	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('Sheet1');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
	//$filename = iconv("UTF-8", "EUC-KR", "MRO -> 통합주문 변환 -".date("Ymd",strtotime("0 month")));
	$filename = "미수금원장 - ".date("Ymd",strtotime("0 month"));

	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment;filename=".$filename.".xls");
	header('Cache-Control: max-age=0');
 
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

	mysql_close($conn);
	exit;
?>