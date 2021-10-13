<?session_start();?>
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
	require "../../_classes/biz/stock/stock.php";
	require "../../_classes/biz/company/company.php";
	require "../../_classes/biz/work/work.php";
	require "../../_classes/biz/admin/admin.php";
	require "../../_classes/biz/order/order.php";


#====================================================================
# Request Parameter
#====================================================================

	$req_no = trim(base64url_decode($req_no));
	//$req_no = trim($req_no);

    //echo base64url_decode($req_no);

#===============================================================
# Get Search list count
#===============================================================

	$arr_rs = selectGoodsRequestByReqNo($conn, $req_no);

	$REQ_DATE				= $arr_rs[0]["REQ_DATE"];
	$SENDER_CP				= $arr_rs[0]["SENDER_CP"];
	$CEO_NM					= $arr_rs[0]["CEO_NM"];
	$SENDER_ADDR			= $arr_rs[0]["SENDER_ADDR"];
	$SENDER_PHONE			= $arr_rs[0]["SENDER_PHONE"];
	$BUY_CP_NM				= $arr_rs[0]["BUY_CP_NM"];
	$BUY_MANAGER_NM			= $arr_rs[0]["BUY_MANAGER_NM"];
	$BUY_CP_PHONE			= $arr_rs[0]["BUY_CP_PHONE"];
	$DELIVERY_TYPE			= $arr_rs[0]["DELIVERY_TYPE"];
	$MEMO					= $arr_rs[0]["MEMO"];
	$TOTAL_REQ_QTY			= $arr_rs[0]["TOTAL_REQ_QTY"];
	$TOTAL_BUY_TOTAL_PRICE	= $arr_rs[0]["TOTAL_BUY_TOTAL_PRICE"];

	$arr_rs_goods = listGoodsRequestGoods($conn, $req_no, 'N');

	require_once "../../_PHPExcel/Classes/PHPExcel.php";

	$objPHPExcel = new PHPExcel();

	$sheetIndex = $objPHPExcel->setActiveSheetIndex(0);

	$defaultStyle = array(
	  'font'  => array(
        'size'  => 10,
        'name'  => '맑은 고딕'
	  )
	);

	$titleStyle = array(
	  'font'  => array(
        'size'  => 14,
        'name'  => '맑은 고딕'
	  )
	);

	$BStyle = array(
	  'borders' => array(
		'allborders' => array(
		  'style' => PHPExcel_Style_Border::BORDER_THIN
		)
	  )
	);

	$alertStyle = array(
	 'font'  => array(
        'color' => array('rgb' => 'FF0000'),
        'size'  => 9,
        'name'  => '맑은 고딕'
    ));

	$listTitleStyle = array(
		'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('rgb' => 'eeece1')
		)
	);

	$outline_style = array(
	  'borders' => array(
		'outline' => array(
		  'style' => PHPExcel_Style_Border::BORDER_MEDIUM
		)
	  )
	);

	

	//1~2열
	$k = 1;
	$sheetIndex->setCellValue("A".$k,iconv("EUC-KR", "UTF-8","LG생활건강 특판팀 발주서"));
	$sheetIndex->mergeCells("A$k:AD".($k+1));
	$sheetIndex->getStyle("A$k")->getFont()->setSize(14)->setBold(true);
	$sheetIndex->getStyle("A$k")->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheetIndex->getRowDimension($k)->setRowHeight(16.5);
	$sheetIndex->getRowDimension($k+1)->setRowHeight(16.5);
	$k += 2;

	$k += 1;
	$k += 1;

	$sheetIndex ->mergeCells("A$k:AD$k");
	$k += 1;
	
	$sheetIndex->setCellValue("A$k",iconv("EUC-KR", "UTF-8","아래와 같이 발주합니다."));
	$sheetIndex->setCellValue("V$k", iconv("EUC-KR", "UTF-8","발주일 : ".date("Y-m-d",strtotime($REQ_DATE))));
	$k += 1;

	$sheetIndex ->mergeCells("A$k:AD$k");
	$k += 1;

	if (sizeof($arr_rs_goods) > 0) {
		$MEMO2						= trim($arr_rs_goods[0]["MEMO2"]);
		if($MEMO2 == "")
			$MEMO2 = $SENDER_CP;
		
		$ORDER_GOODS_NO				= trim($arr_rs_goods[0]["ORDER_GOODS_NO"]);
		$arr_order = selectOrderByOrderGoodsNo($conn, $ORDER_GOODS_NO);
		if (sizeof($arr_order) > 0) {
			$R_MEM_NM				= trim($arr_order[0]["R_MEM_NM"]);
			$R_PHONE				= trim($arr_order[0]["R_PHONE"]);
		} else {
			$R_MEM_NM				= trim($arr_rs_goods[0]["RECEIVER_NM"]);
			$R_PHONE				= trim($arr_rs_goods[0]["RECEIVER_PHONE"]);;
		}
	}

	$sheetIndex ->setCellValue("A$k", iconv("EUC-KR", "UTF-8","발주처"))->mergeCells("A$k:E$k")
				->setCellValue("F$k", iconv("EUC-KR", "UTF-8",$MEMO2))->mergeCells("F$k:N$k")
				->setCellValue("O$k", iconv("EUC-KR", "UTF-8","배송처"))->mergeCells("O$k:S$k")
				->setCellValue("T$k", iconv("EUC-KR", "UTF-8",$SENDER_CP))->mergeCells("T$k:AD$k");
	
	$sheetIndex->getStyle("A$k:AD$k")->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$k += 1;

	//기본으로 요청일의 익일로..익일이 주말일 경우 주말 아닐때까지 +1 day
	$REQ_DATE = date('Y-m-d H:i:s', strtotime($REQ_DATE . ' +1 day'));
	while(isWeekend($REQ_DATE)) { 
		$REQ_DATE = date('Y-m-d H:i:s', strtotime($REQ_DATE . ' +1 day'));
	}

	$sheetIndex ->setCellValue("A$k", iconv("EUC-KR", "UTF-8","최종납품처"))->mergeCells("A$k:E$k")
				->setCellValue("F$k", iconv("EUC-KR", "UTF-8",$MEMO2))->mergeCells("F$k:N$k")
				->setCellValue("O$k", iconv("EUC-KR", "UTF-8","배송요청일"))->mergeCells("O$k:S$k")
				->setCellValue("T$k", iconv("EUC-KR", "UTF-8", ""))->mergeCells("T$k:AD$k");
	$sheetIndex->getStyle("A$k:AD$k")->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$k += 1;

	$sheetIndex ->setCellValue("A$k", iconv("EUC-KR", "UTF-8","납품처 담당자"))->mergeCells("A$k:E$k")
				->setCellValue("F$k", iconv("EUC-KR", "UTF-8",$R_MEM_NM))->mergeCells("F$k:N$k")
				->setCellValue("O$k", iconv("EUC-KR", "UTF-8","연락처(사무실)"))->mergeCells("O$k:S$k")
				->setCellValue("T$k", iconv("EUC-KR", "UTF-8",$R_PHONE))->mergeCells("T$k:AD$k");
	$sheetIndex->getStyle("A$k:AD$k")->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);	$sheetIndex->getStyle("A3:AD$k")->applyFromArray($defaultStyle);
	$k += 1;

	//기프트넷 주소 표기
	$sheetIndex ->setCellValue("A$k", iconv("EUC-KR", "UTF-8","주소"))->mergeCells("A$k:E$k")
				->setCellValue("F$k", iconv("EUC-KR", "UTF-8",$SENDER_ADDR))->mergeCells("F$k:AD$k");
	$sheetIndex->getStyle("A$k:AD$k")->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheetIndex->getStyle("A3:AD$k")->applyFromArray($defaultStyle);
	$k += 1;

	$sheetIndex ->setCellValue("A$k", iconv("EUC-KR", "UTF-8","* 담당자 연락처는 반드시 핸드폰이 아닌 사무실 전화 번호이여야 합니다. "));
	$sheetIndex->getStyle("A$k:AD$k")->applyFromArray($alertStyle);
	$k += 1;


	$sheetIndex ->setCellValue("Z$k", iconv("EUC-KR", "UTF-8","(가격; 세 별도)"));
	$sheetIndex->getStyle("Z$k")->getFont()->setBold(true);
	$k += 1;
	

	// 제품명	 제품 코드 단가 수량(개) 합계 금액					
	$sheetIndex->setCellValue("A$k", iconv("EUC-KR", "UTF-8","제품명"))->mergeCells("A$k:J$k")
	           ->setCellValue("K$k", iconv("EUC-KR", "UTF-8","제품 코드"))->mergeCells("K$k:P$k")
			   ->setCellValue("Q$k", iconv("EUC-KR", "UTF-8","단가"))->mergeCells("Q$k:T$k")
			   ->setCellValue("U$k", iconv("EUC-KR", "UTF-8","수량(개)"))->mergeCells("U$k:X$k")
			   ->setCellValue("Y$k", iconv("EUC-KR", "UTF-8","합계 금액"))->mergeCells("Y$k:AD$k");

	$sheetIndex->getStyle("A$k:AD$k")->getFont()->setBold(true);
	$sheetIndex->getStyle("A$k:AD$k")->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheetIndex->getStyle("A$k:AD$k")->applyFromArray($listTitleStyle);
	$k += 1;

	$TOTAL_BUY_TOTAL_PRICE_NOTAX = 0;

	if (sizeof($arr_rs_goods) > 0) {
		for ($j = 0 ; $j < sizeof($arr_rs_goods); $j++) {

			$GOODS_NO					= trim($arr_rs_goods[$j]["GOODS_NO"]);
			$GOODS_NAME					= trim($arr_rs_goods[$j]["GOODS_NAME"]);
			$GOODS_SUB_NAME				= trim($arr_rs_goods[$j]["GOODS_SUB_NAME"]);
			$REQ_QTY					= trim($arr_rs_goods[$j]["REQ_QTY"]);
			$BUY_PRICE					= trim($arr_rs_goods[$j]["BUY_PRICE"]);
			$BUY_TOTAL_PRICE			= trim($arr_rs_goods[$j]["BUY_TOTAL_PRICE"]);

			$RECEIVER_NM				= trim($arr_rs_goods[$j]["RECEIVER_NM"]);
			$RECEIVER_ADDR				= trim($arr_rs_goods[$j]["RECEIVER_ADDR"]);
			$RECEIVER_PHONE				= trim($arr_rs_goods[$j]["RECEIVER_PHONE"]);
			$RECEIVER_HPHONE			= trim($arr_rs_goods[$j]["RECEIVER_HPHONE"]);
			$MEMO1						= trim($arr_rs_goods[$j]["MEMO1"]);
			$MEMO2						= trim($arr_rs_goods[$j]["MEMO2"]);
			$MEMO3						= trim($arr_rs_goods[$j]["MEMO3"]);

			$TO_HERE					= trim($arr_rs_goods[$j]["TO_HERE"]);

			$ORDER_GOODS_NO				= trim($arr_rs_goods[$j]["ORDER_GOODS_NO"]);

			$arr_rs_goods_extra = selectGoodsExtra($conn, $GOODS_NO, 'GOODS_CODE_LG');
			if(sizeof($arr_rs_goods_extra) > 0) { 
				for($p = 0; $p < sizeof($arr_rs_goods_extra); $p ++) { 
					$rs_extra_dcode	= SetStringFromDB($arr_rs_goods_extra[$p]["DCODE"]); 
				}
			} else { 
				$rs_extra_dcode = "";
			}

			
			if($BUY_PRICE <> "") {
				//부가세 별도라 다시 계산
				$BUY_PRICE = round($BUY_PRICE / 1.1);
			}
			
			
			//부가세 별도라 다시 계산
			$BUY_TOTAL_PRICE = $BUY_PRICE * $REQ_QTY;
			$TOTAL_BUY_TOTAL_PRICE_NOTAX += $BUY_TOTAL_PRICE;


			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A$k", iconv("EUC-KR", "UTF-8",$GOODS_NAME." ".$GOODS_SUB_NAME))->mergeCells("A$k:J$k")
				->setCellValue("K$k", iconv("EUC-KR", "UTF-8",$rs_extra_dcode))->mergeCells("K$k:P$k")
				->setCellValue("Q$k", iconv("EUC-KR", "UTF-8",number_format($BUY_PRICE)))->mergeCells("Q$k:T$k")
				->setCellValue("U$k", iconv("EUC-KR", "UTF-8",number_format($REQ_QTY)))->mergeCells("U$k:X$k")
				->setCellValue("Y$k", iconv("EUC-KR", "UTF-8",number_format($BUY_TOTAL_PRICE)))->mergeCells("Y$k:AD$k");

			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("AE$k", iconv("EUC-KR", "UTF-8",$MEMO2));

			$sheetIndex->getStyle("K$k:P$k")->getAlignment()
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheetIndex->getStyle("Q$k:AD$k")->getAlignment()
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

			$k += 1;
		}
	}


	$total_row = $k+((sizeof($arr_rs_goods) <= 13 ? 13 - sizeof($arr_rs_goods) : 1));

	
	while($total_row >= $k) { 
		$objPHPExcel->setActiveSheetIndex(0)
					->mergeCells("A$k:J$k")
					->mergeCells("K$k:P$k")
					->mergeCells("Q$k:T$k")
					->mergeCells("U$k:X$k")
					->mergeCells("Y$k:AD$k");
		$k += 1;
	}

	$sheetIndex
				->setCellValue("A".$total_row, iconv("EUC-KR", "UTF-8","당일 발주 총액"))->mergeCells("A".$total_row.":J".$total_row)
				->mergeCells("K".$total_row.":P".$total_row)
				->mergeCells("Q".$total_row.":T".$total_row)
				->mergeCells("U".$total_row.":X".$total_row)
				->setCellValue("Y".$total_row, iconv("EUC-KR", "UTF-8",number_format($TOTAL_BUY_TOTAL_PRICE_NOTAX)." 원"))->mergeCells("Y".$total_row.":AD".$total_row);
	$sheetIndex->getStyle("A".$total_row.":J".$total_row)->getFont()->setBold(true);
	$sheetIndex->getStyle("A".$total_row.":J".$total_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheetIndex->getStyle("Y".$total_row.":AD".$total_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$k = $total_row + 1;

	$sheetIndex	->setCellValue("A$k", iconv("EUC-KR", "UTF-8",number_format($TOTAL_BUY_TOTAL_PRICE)." 원 (부가세포함)"))->mergeCells("A$k:AD$k"); 
	$sheetIndex->getStyle("A$k")->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$k += 1;

	$sheetIndex
				->setCellValue("A".$k, iconv("EUC-KR", "UTF-8","기타 문의 사항"))->mergeCells("A".$k.":AD".$k);
	$sheetIndex->getStyle("A$k")->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$k += 1;

	
	$sheetIndex->setCellValue("A".$k, iconv("EUC-KR", "UTF-8",$MEMO));
	$sheetIndex->mergeCells("A".$k.":AD".($k+5));
	$sheetIndex->getStyle("A$k")->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$k += 5;

	

	$objPHPExcel->getActiveSheet()->getStyle("A8:AD11")->applyFromArray($BStyle);
	$objPHPExcel->getActiveSheet()->getStyle("A14:AD".($total_row))->applyFromArray($BStyle);
	$objPHPExcel->getActiveSheet()->getStyle("A".($total_row+2).":AD$k")->applyFromArray($BStyle);

	$objPHPExcel->getActiveSheet()->getStyle("A14:AD".($total_row))->applyFromArray($outline_style);
	$objPHPExcel->getActiveSheet()->getStyle("A".($total_row+2).":AD$k")->applyFromArray($outline_style);

	$margin = 2.2;
	$sheetIndex->getColumnDimension("A")->setWidth(1 + $margin);
	$sheetIndex->getColumnDimension("B")->setWidth(1 + $margin);
	$sheetIndex->getColumnDimension("C")->setWidth(1 + $margin);
	$sheetIndex->getColumnDimension("D")->setWidth(1 + $margin);
	$sheetIndex->getColumnDimension("E")->setWidth(1 + $margin);
	$sheetIndex->getColumnDimension("F")->setWidth(1 + $margin);
	$sheetIndex->getColumnDimension("G")->setWidth(1 + $margin);
	$sheetIndex->getColumnDimension("H")->setWidth(1 + $margin);
	$sheetIndex->getColumnDimension("I")->setWidth(1 + $margin);
	$sheetIndex->getColumnDimension("J")->setWidth(1 + $margin);
	$sheetIndex->getColumnDimension("K")->setWidth(1 + $margin);
	$sheetIndex->getColumnDimension("L")->setWidth(1 + $margin);
	$sheetIndex->getColumnDimension("M")->setWidth(1 + $margin);
	$sheetIndex->getColumnDimension("N")->setWidth(1 + $margin);
	$sheetIndex->getColumnDimension("O")->setWidth(1 + $margin);
	$sheetIndex->getColumnDimension("P")->setWidth(1 + $margin);
	$sheetIndex->getColumnDimension("Q")->setWidth(1 + $margin);
	$sheetIndex->getColumnDimension("R")->setWidth(1 + $margin);
	$sheetIndex->getColumnDimension("S")->setWidth(1 + $margin);
	$sheetIndex->getColumnDimension("T")->setWidth(1 + $margin);
	$sheetIndex->getColumnDimension("U")->setWidth(1 + $margin);
	$sheetIndex->getColumnDimension("V")->setWidth(1 + $margin);
	$sheetIndex->getColumnDimension("W")->setWidth(1 + $margin);
	$sheetIndex->getColumnDimension("X")->setWidth(1 + $margin);
	$sheetIndex->getColumnDimension("Y")->setWidth(1 + $margin);
	$sheetIndex->getColumnDimension("Z")->setWidth(1 + $margin);
	$sheetIndex->getColumnDimension("AA")->setWidth(1 + $margin);
	$sheetIndex->getColumnDimension("AB")->setWidth(1 + $margin);
	$sheetIndex->getColumnDimension("AC")->setWidth(1 + $margin);
	$sheetIndex->getColumnDimension("AD")->setWidth(1 + $margin);
	
	// Rename sheet
	$objPHPExcel->setActiveSheetIndex(0)->setTitle(iconv("EUC-KR", "UTF-8","LG발주서"));

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	//$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(100);

	// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
	$filename = "발주서-".date("Ymd");

	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment;filename=".$filename.".xls");
	header('Cache-Control: max-age=0');
 
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

	mysql_close($conn);
	exit;


?>
				
