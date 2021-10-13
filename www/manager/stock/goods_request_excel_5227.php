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

	$REQ_DATE				= $arr_rs[0]["REQ_DATE"]; //발주일자
	$SENDER_CP				= $arr_rs[0]["SENDER_CP"]; //발신처
	$CEO_NM					= $arr_rs[0]["CEO_NM"]; //발신처 대표자명
	$SENDER_ADDR			= $arr_rs[0]["SENDER_ADDR"]; //발신처 주소
	$SENDER_PHONE			= $arr_rs[0]["SENDER_PHONE"]; //발신처 연락처
	$BUY_CP_NM				= $arr_rs[0]["BUY_CP_NM"]; //수신처
	$BUY_MANAGER_NM			= $arr_rs[0]["BUY_MANAGER_NM"]; //수신처 담당자명
	$BUY_CP_PHONE			= $arr_rs[0]["BUY_CP_PHONE"]; //수신처 연락처
	$DELIVERY_TYPE			= $arr_rs[0]["DELIVERY_TYPE"];
	$MEMO					= $arr_rs[0]["MEMO"]; //메모
	$TOTAL_REQ_QTY			= $arr_rs[0]["TOTAL_REQ_QTY"]; //발주 수량
	$TOTAL_BUY_TOTAL_PRICE	= $arr_rs[0]["TOTAL_BUY_TOTAL_PRICE"]; //총 발주 가격

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

	$headerStyle = array(
		'font'  => array(
			'size'  => 9,
			'name'  => '굴림',
			'bold' => 'true'
		),'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('rgb' => 'ccff99')
		),'borders' => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		)
	);
	
	$contentsStyle = array(
		'font'  => array(
			'size'  => 9,
			'name'  => '굴림'
		),'borders' => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			)
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
	
	//1행
	$k = 1;
	$sheetIndex->setCellValue("A".$k,iconv("EUC-KR", "UTF-8","발주일자"));
	$sheetIndex->setCellValue("B".$k,iconv("EUC-KR", "UTF-8","매출처"));
	$sheetIndex->setCellValue("C".$k,iconv("EUC-KR", "UTF-8","수령자명"));
	$sheetIndex->setCellValue("D".$k,iconv("EUC-KR", "UTF-8","유선전화번호"));
	$sheetIndex->setCellValue("E".$k,iconv("EUC-KR", "UTF-8","무선전화번호"));
	$sheetIndex->setCellValue("F".$k,iconv("EUC-KR", "UTF-8","우편번호"));
	$sheetIndex->setCellValue("G".$k,iconv("EUC-KR", "UTF-8","주소"));
	$sheetIndex->setCellValue("H".$k,iconv("EUC-KR", "UTF-8","유의사항 (옵션 - 색상및 사이즈)"));
	$sheetIndex->setCellValue("I".$k,iconv("EUC-KR", "UTF-8","주문번호"));
	$sheetIndex->setCellValue("J".$k,iconv("EUC-KR", "UTF-8","상품명"));
	$sheetIndex->setCellValue("K".$k,iconv("EUC-KR", "UTF-8","수량"));
	$sheetIndex->setCellValue("L".$k,iconv("EUC-KR", "UTF-8","배송사"));
	$sheetIndex->setCellValue("M".$k,iconv("EUC-KR", "UTF-8","운송자번호"));
	$sheetIndex->setCellValue("N".$k,iconv("EUC-KR", "UTF-8","택배사"));

	//가운데 정렬
	$sheetIndex->getStyle("A$k:N$k")
	->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
	->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	//스타일 적용
	$sheetIndex->getStyle("A$k:N$k")->applyFromArray($headerStyle);

	if (sizeof($arr_rs_goods) > 0) {
		for ($j = 0 ; $j < sizeof($arr_rs_goods); $j++) {
			$GOODS_NO					= trim($arr_rs_goods[$j]["GOODS_NO"]); //상품번호
			$GOODS_NAME					= trim($arr_rs_goods[$j]["GOODS_NAME"]); //상품명
			$GOODS_SUB_NAME				= trim($arr_rs_goods[$j]["GOODS_SUB_NAME"]); //상품 구성품명
			$REQ_QTY					= trim($arr_rs_goods[$j]["REQ_QTY"]); //발주 수량
			$BUY_PRICE					= trim($arr_rs_goods[$j]["BUY_PRICE"]); //가격
			$BUY_TOTAL_PRICE			= trim($arr_rs_goods[$j]["BUY_TOTAL_PRICE"]); //총가격
			$RECEIVER_NM				= trim($arr_rs_goods[$j]["RECEIVER_NM"]); //수신자명
			$RECEIVER_ADDR				= trim($arr_rs_goods[$j]["RECEIVER_ADDR"]); //수신자 주소
			$RECEIVER_PHONE				= trim($arr_rs_goods[$j]["RECEIVER_PHONE"]); //수신자 연락처
			$RECEIVER_HPHONE			= trim($arr_rs_goods[$j]["RECEIVER_HPHONE"]); //수신자 핸드폰
			$MEMO1						= trim($arr_rs_goods[$j]["MEMO1"]); //작업
			$MEMO2						= trim($arr_rs_goods[$j]["MEMO2"]); //주문지
			$MEMO3						= trim($arr_rs_goods[$j]["MEMO3"]); //발송자
			$TO_HERE					= trim($arr_rs_goods[$j]["TO_HERE"]); //직송여부
			$ORDER_GOODS_NO				= trim($arr_rs_goods[$j]["ORDER_GOODS_NO"]);
			$arr_rs_goods_extra 		= selectGoodsExtra($conn, $GOODS_NO, 'GOODS_CODE_LG');

			//내용 높이 15
			$k += 1;
			$sheetIndex->setCellValue("A".$k,iconv("EUC-KR", "UTF-8","$REQ_DATE"));
			$sheetIndex->setCellValue("B".$k,iconv("EUC-KR", "UTF-8","$SENDER_CP"));
			$sheetIndex->setCellValue("C".$k,iconv("EUC-KR", "UTF-8","$RECEIVER_NM"));
			$sheetIndex->setCellValue("D".$k,iconv("EUC-KR", "UTF-8","$RECEIVER_PHONE"));
			$sheetIndex->setCellValue("E".$k,iconv("EUC-KR", "UTF-8","$RECEIVER_HPHONE"));
			$sheetIndex->setCellValue("F".$k,iconv("EUC-KR", "UTF-8",""));//우편번호
			$sheetIndex->setCellValue("G".$k,iconv("EUC-KR", "UTF-8","$RECEIVER_ADDR"));
			$sheetIndex->setCellValue("H".$k,iconv("EUC-KR", "UTF-8","보내는 사람 : $SENDER_CP".(($MEMO1!="")?"\n$MEMO1":"")));//유의사항
			$sheetIndex->setCellValue("I".$k,iconv("EUC-KR", "UTF-8",""));//주문번호
			$sheetIndex->setCellValue("J".$k,iconv("EUC-KR", "UTF-8","$GOODS_NAME"));
			$sheetIndex->setCellValue("K".$k,iconv("EUC-KR", "UTF-8","$REQ_QTY"));
			$sheetIndex->setCellValue("L".$k,iconv("EUC-KR", "UTF-8","$BUY_CP_NM"));
			$sheetIndex->setCellValue("M".$k,iconv("EUC-KR", "UTF-8",""));
			$sheetIndex->setCellValue("N".$k,iconv("EUC-KR", "UTF-8",""));

			//가운데 정렬
			$sheetIndex->getStyle("A$k:N$k")
			->getAlignment()
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
			->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			//주소와 상품명, 유의사항은 좌측정렬
			$sheetIndex->getStyle("G$k")//주소
			->getAlignment()
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

			$sheetIndex->getStyle("J$k")//상품명
			->getAlignment()
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			
			$sheetIndex->getStyle("H$k")//유의사항
			->getAlignment()
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

			//스타일 적용
			$sheetIndex->getStyle("A$k:N$k")->applyFromArray($contentsStyle);
			
			//행 높이 : 메모 있으면 개행되니까 오토 사이즈로 행 높이를 늘이고, 아니면 기본 행 높이 적용
			if($MEMO1 == "")
				$objPHPExcel->getActiveSheet()->getRowDimension($k)->setRowHeight(15);
			else{
				$objPHPExcel->getActiveSheet()->getRowDimension($k)->setRowHeight(-1);
				$sheetIndex->getStyle("H$k")->getAlignment()->setWrapText(true);
			}
		}
	}

	//폭
	$margin = 2;
	$sheetIndex->getColumnDimension("A")->setWidth(18.43 + $margin);
	$sheetIndex->getColumnDimension("B")->setWidth(11.14 + $margin);
	$sheetIndex->getColumnDimension("C")->setWidth(11.14 + $margin);
	$sheetIndex->getColumnDimension("D")->setWidth(12.43 + $margin);
	$sheetIndex->getColumnDimension("E")->setWidth(12.43 + $margin);
	$sheetIndex->getColumnDimension("F")->setWidth(6.33 + $margin);
	$sheetIndex->getColumnDimension("G")->setWidth(45 + $margin);
	$sheetIndex->getColumnDimension("H")->setWidth(23.44 + $margin);
	$sheetIndex->getColumnDimension("I")->setWidth(6.33 + $margin);
	$sheetIndex->getColumnDimension("J")->setWidth(49.86 + $margin);
	$sheetIndex->getColumnDimension("K")->setWidth(5 + $margin);
	$sheetIndex->getColumnDimension("L")->setWidth(9.43 + $margin);
	$sheetIndex->getColumnDimension("M")->setWidth(7.78 + $margin);
	$sheetIndex->getColumnDimension("N")->setWidth(4.89 + $margin);

	//높이
	$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(14.25);

	// Rename sheet
	$objPHPExcel->setActiveSheetIndex(0)->setTitle(iconv("EUC-KR", "UTF-8","발주내역"));

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

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
				
