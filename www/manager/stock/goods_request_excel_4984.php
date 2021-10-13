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

#===============================================================
# Get Search list count
#===============================================================
	$arr_rs = selectGoodsRequestByReqNo($conn, $req_no);

	$REQ_DATE				= $arr_rs[0]["REQ_DATE"];
	$SENDER_CP				= $arr_rs[0]["SENDER_CP"];
	$CEO_NM					= $arr_rs[0]["CEO_NM"];
	$SENDER_ADDR			= $arr_rs[0]["SENDER_ADDR"];
	$SENDER_PHONE			= $arr_rs[0]["SENDER_PHONE"];
	$SENDER_PHONE			= explode("/",$SENDER_PHONE);
	$SENDER_PHONE			= $SENDER_PHONE[0];
	$BUY_CP_NM				= $arr_rs[0]["BUY_CP_NM"];
	$BUY_MANAGER_NM			= $arr_rs[0]["BUY_MANAGER_NM"];
	$BUY_CP_PHONE			= $arr_rs[0]["BUY_CP_PHONE"];
	$DELIVERY_TYPE			= $arr_rs[0]["DELIVERY_TYPE"];
	$MEMO					= $arr_rs[0]["MEMO"];
	$TOTAL_REQ_QTY			= $arr_rs[0]["TOTAL_REQ_QTY"];
	$TOTAL_BUY_TOTAL_PRICE	= $arr_rs[0]["TOTAL_BUY_TOTAL_PRICE"];
	$RECEIVER_NM	= $arr_rs[0]["RECEIVER_NM"];
	
	$arr_rs_goods = listGoodsRequestGoods($conn, $req_no, 'N');

	require_once "../../_PHPExcel/Classes/PHPExcel.php";

	$objPHPExcel = new PHPExcel();

	$sheetIndex = $objPHPExcel->setActiveSheetIndex(0);

#===============================================================
# Style
#===============================================================
	$defaultStyle = array(
	  'font'  => array(
        'size'  => 9,
        'name'  => '맑은 고딕'
	  ),'borders' => array(
		'allborders' => array(
		  'style' => PHPExcel_Style_Border::BORDER_THIN
		)
	  )
	);

	$titleStyle = array(
	  'font'  => array(
        'size'  => 14,
        'name'  => '맑은 고딕'
	  )
	);

	$headerStyle = array(
		'font' => array(
			'size'  => 9,
			'name' => '맑은고딕',
			'bold' => 'true'
		),'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('rgb' => 'bfbfbf')
		),'borders' => array(
			'allborders' => array(
			  'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		)
	);

	$priceStyle = array(
		'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('rgb' => 'fcd5b4')
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

#===============================================================
# Print contents
#===============================================================
    //엑셀 내용 출력 시작
	$k = 1;
    
    //1행
    $sheetIndex->setCellValue("A".$k,iconv("EUC-KR", "UTF-8","배송 리스트"));
	$sheetIndex->getStyle("A$k")->getFont()->setSize(20)->setBold(true);
	$sheetIndex->mergeCells("A$k:N$k");
	$sheetIndex->getStyle("A$k")
		->getAlignment()
		->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
		->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    
    //2행
	$k += 1;
	$sheetIndex->setCellValue("A".$k,iconv("EUC-KR", "UTF-8","TEL : 82-2-1544-1278 / FAX : 82-2-544-8660  E-MAIL : order@winwik.com"));
	$sheetIndex->getStyle("A$k")->getFont()->setSize(11);
	$sheetIndex->mergeCells("A$k:N$k");
	$sheetIndex->getStyle("A$k")
	->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
	->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	//3행
	$k += 1;
	$sheetIndex->setCellValue("A".$k,iconv("EUC-KR", "UTF-8","날짜"));
	$sheetIndex->getStyle("A$k")->getFont()->setSize(12)->setBold(true);
	$sheetIndex->mergeCells("A$k:B$k");
	$sheetIndex->getStyle("A$k")
	->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
	->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$sheetIndex->setCellValue("C".$k,iconv("EUC-KR", "UTF-8",$REQ_DATE));
	$sheetIndex->getStyle("C$k")->getFont()->setSize(11)->setBold(true);
	$sheetIndex->mergeCells("C$k:E$k");
	$sheetIndex->getStyle("C$k")
	->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
	->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	//4행
	$k += 1;
	$sheetIndex->setCellValue("A".$k,iconv("EUC-KR", "UTF-8","위닉 담당자"));
	$sheetIndex->getStyle("A$k")->getFont()->setSize(12)->setBold(true);
	$sheetIndex->mergeCells("A$k:B$k");
	$sheetIndex->getStyle("A$k")
	->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
	->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$sheetIndex->setCellValue("C".$k,iconv("EUC-KR", "UTF-8",$BUY_MANAGER_NM));
	$sheetIndex->getStyle("C$k")->getFont()->setSize(11);
	$sheetIndex->mergeCells("C$k:E$k");
	$sheetIndex->getStyle("C$k")
	->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
	->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	//5행
	$k += 1;
	$sheetIndex->setCellValue("A".$k,iconv("EUC-KR", "UTF-8","업체명"));
	$sheetIndex->getStyle("A$k")->getFont()->setSize(12)->setBold(true);
	$sheetIndex->mergeCells("A$k:B$k");
	$sheetIndex->getStyle("A$k")
	->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
	->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$sheetIndex->setCellValue("C".$k,iconv("EUC-KR", "UTF-8",$SENDER_CP));
	$sheetIndex->getStyle("C$k")->getFont()->setSize(11);
	$sheetIndex->mergeCells("C$k:E$k");
	$sheetIndex->getStyle("C$k")
	->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
	->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	//6행
	$k += 1;
	$sheetIndex->setCellValue("A".$k,iconv("EUC-KR", "UTF-8","담당자"));
	$sheetIndex->getStyle("A$k")->getFont()->setSize(12)->setBold(true);
	$sheetIndex->mergeCells("A$k:B$k");
	$sheetIndex->getStyle("A$k")
	->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
	->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$sheetIndex->setCellValue("C".$k,iconv("EUC-KR", "UTF-8",TRIM($SENDER_CP)." / ".$SENDER_PHONE));
	$sheetIndex->getStyle("C$k")->getFont()->setSize(11);
	$sheetIndex->mergeCells("C$k:E$k");
	$sheetIndex->getStyle("C$k")
	->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
	->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	//7행
	$k += 1;
	$sheetIndex->setCellValue("A".$k,iconv("EUC-KR", "UTF-8","주문인"));
	$sheetIndex->setCellValue("B".$k,iconv("EUC-KR", "UTF-8","수령인"));
	$sheetIndex->setCellValue("C".$k,iconv("EUC-KR", "UTF-8","전화번호1"));
	$sheetIndex->setCellValue("D".$k,iconv("EUC-KR", "UTF-8","전화번호2"));
	$sheetIndex->setCellValue("E".$k,iconv("EUC-KR", "UTF-8","우편번호"));
	$sheetIndex->setCellValue("F".$k,iconv("EUC-KR", "UTF-8","주소"));
	$sheetIndex->setCellValue("G".$k,iconv("EUC-KR", "UTF-8","제품명"));
	$sheetIndex->setCellValue("H".$k,iconv("EUC-KR", "UTF-8","수량"));
	$sheetIndex->setCellValue("I".$k,iconv("EUC-KR", "UTF-8","비고"));
	$sheetIndex->setCellValue("J".$k,iconv("EUC-KR", "UTF-8","택배사"));
	$sheetIndex->setCellValue("K".$k,iconv("EUC-KR", "UTF-8","운송장번호"));
	$sheetIndex->setCellValue("L".$k,iconv("EUC-KR", "UTF-8","단가"));
	$sheetIndex->setCellValue("M".$k,iconv("EUC-KR", "UTF-8","택배비"));
	$sheetIndex->setCellValue("N".$k,iconv("EUC-KR", "UTF-8","합계"));

	//가운데 정렬
	$sheetIndex->getStyle("A$k:N$k")
	->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
	->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	//스타일 적용
	$sheetIndex->getStyle("A$k:N$k")->applyFromArray($headerStyle);

	//좌측 상단 업체정보 스타일
	$sheetIndex->getStyle("A3:E6")->applyFromArray($BStyle);
	$sheetIndex->getStyle("A3:E6")->applyFromArray($outline_style);

	//내용
	if (sizeof($arr_rs_goods) > 0) {
		for ($j = 0 ; $j < sizeof($arr_rs_goods); $j++) {
			$k += 1;
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
				//$BUY_PRICE = round($BUY_PRICE / 1.1); 191223 효곤대리님 요청으로 주석처리 
			}
			
			//부가세 별도라 다시 계산
			$BUY_TOTAL_PRICE = $BUY_PRICE * $REQ_QTY;
			$TOTAL_BUY_TOTAL_PRICE_NOTAX += $BUY_TOTAL_PRICE;

			if($TO_HERE == 'Y'){
				$ORDERER = $RECEIVER_NM;
			} else {
				$ORDERER = $MEMO2;
			}

			//주문인	수령인	전화번호1	전화번호2	우편번호	주소	제품명	수량	비고	택배사	운송장번호	단가	택배비	합계
			$sheetIndex->setCellValue("A".$k,iconv("EUC-KR", "UTF-8",$ORDERER));
			$sheetIndex->setCellValue("B".$k,iconv("EUC-KR", "UTF-8",$RECEIVER_NM));
			$sheetIndex->setCellValue("C".$k,iconv("EUC-KR", "UTF-8",$RECEIVER_PHONE));
			$sheetIndex->setCellValue("D".$k,iconv("EUC-KR", "UTF-8",$RECEIVER_HPHONE));
			$sheetIndex->setCellValue("E".$k,iconv("EUC-KR", "UTF-8",""));
			$sheetIndex->setCellValue("F".$k,iconv("EUC-KR", "UTF-8",$RECEIVER_ADDR));
			$sheetIndex->setCellValue("G".$k,iconv("EUC-KR", "UTF-8",$GOODS_NAME." ".$GOODS_SUB_NAME));
			$sheetIndex->setCellValue("H".$k,iconv("EUC-KR", "UTF-8",number_format($REQ_QTY)));
			$sheetIndex->setCellValue("I".$k,iconv("EUC-KR", "UTF-8",$MEMO1));
			$sheetIndex->setCellValue("J".$k,iconv("EUC-KR", "UTF-8",""));
			$sheetIndex->setCellValue("K".$k,iconv("EUC-KR", "UTF-8",""));
			$sheetIndex->setCellValue("L".$k,iconv("EUC-KR", "UTF-8",number_format($BUY_PRICE)));
			$sheetIndex->setCellValue("M".$k,iconv("EUC-KR", "UTF-8",""));
			$sheetIndex->setCellValue("N".$k,iconv("EUC-KR", "UTF-8",number_format($BUY_TOTAL_PRICE)));
			
			//스타일 적용
			$sheetIndex->getStyle("A$k:N$k")->applyFromArray($defaultStyle);
			$sheetIndex->getStyle("L$k:N$k")->applyFromArray($priceStyle);
			
			//행 높이
			$sheetIndex->getRowDimension($k)->setRowHeight(24);

			//자동 개행
			$sheetIndex->getStyle("A$k:N$k")
			->getAlignment()
			->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$sheetIndex->getStyle("A$k:N$k")
			->getAlignment()
			->setWrapText(true);
		}
	}

#===============================================================
# Cell size
#===============================================================
	//폭
	$margin = 2.2;
	$sheetIndex->getColumnDimension("A")->setWidth(12 + $margin);
	$sheetIndex->getColumnDimension("B")->setWidth(12 + $margin);
	$sheetIndex->getColumnDimension("C")->setWidth(10 + $margin);
	$sheetIndex->getColumnDimension("D")->setWidth(10 + $margin);
	$sheetIndex->getColumnDimension("E")->setWidth(8.33 + $margin);
	$sheetIndex->getColumnDimension("F")->setWidth(32 + $margin);
	$sheetIndex->getColumnDimension("G")->setWidth(20 + $margin);
	$sheetIndex->getColumnDimension("H")->setWidth(8.22 + $margin);
	$sheetIndex->getColumnDimension("I")->setWidth(20 + $margin);
	$sheetIndex->getColumnDimension("J")->setWidth(8.89 + $margin);
	$sheetIndex->getColumnDimension("K")->setWidth(18 + $margin);
	$sheetIndex->getColumnDimension("L")->setWidth(8.89 + $margin);
	$sheetIndex->getColumnDimension("M")->setWidth(8.89 + $margin);
	$sheetIndex->getColumnDimension("N")->setWidth(8.89 + $margin);
	
	//높이
	$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(32.25);
	$objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(13.5);
	$objPHPExcel->getActiveSheet()->getRowDimension(3)->setRowHeight(21);
	$objPHPExcel->getActiveSheet()->getRowDimension(4)->setRowHeight(21);
	$objPHPExcel->getActiveSheet()->getRowDimension(5)->setRowHeight(21);
	$objPHPExcel->getActiveSheet()->getRowDimension(6)->setRowHeight(21);
	$objPHPExcel->getActiveSheet()->getRowDimension(7)->setRowHeight(18);

#===============================================================
# Save
#===============================================================	
	// Rename sheet
	$objPHPExcel->setActiveSheetIndex(0)->setTitle(iconv("EUC-KR", "UTF-8","발주서"));

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