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

	$REQ_DATE								= $arr_rs[0]["REQ_DATE"];
	$SENDER_CP							= $arr_rs[0]["SENDER_CP"];
	$CEO_NM									= $arr_rs[0]["CEO_NM"];
	$SENDER_ADDR						= $arr_rs[0]["SENDER_ADDR"];
	$SENDER_PHONE						= $arr_rs[0]["SENDER_PHONE"];
	$BUY_CP_NM							= $arr_rs[0]["BUY_CP_NM"];
	$BUY_MANAGER_NM					= $arr_rs[0]["BUY_MANAGER_NM"];
	$BUY_CP_PHONE						= $arr_rs[0]["BUY_CP_PHONE"];
	$DELIVERY_TYPE					= $arr_rs[0]["DELIVERY_TYPE"];
	$MEMO										= $arr_rs[0]["MEMO"];
	$TOTAL_REQ_QTY					= $arr_rs[0]["TOTAL_REQ_QTY"];
	$TOTAL_BUY_TOTAL_PRICE	= $arr_rs[0]["TOTAL_BUY_TOTAL_PRICE"];

	$arr_rs_goods = listGoodsRequestGoods($conn, $req_no, 'N');

	//결제 구분
	$arr_rs_company = selectCompany($conn, "4736");
	if(sizeof($arr_rs_company) > 0) { 
		/*
		$rs_cp_type							= SetStringFromDB($arr_rs_company[0]["CP_TYPE"]); 
		$rs_cp_zip							= SetStringFromDB($arr_rs_company[0]["CP_ZIP"]); 
		$rs_cp_addr							= SetStringFromDB($arr_rs_company[0]["CP_ADDR"]); 
		$rs_biz_no							= SetStringFromDB($arr_rs_company[0]["BIZ_NO"]); 
		$rs_ceo_nm							= SetStringFromDB($arr_rs_company[0]["CEO_NM"]); 
		$rs_cp_phone						= SetStringFromDB($arr_rs_company[0]["CP_PHONE"]); 
		*/
		$rs_ad_type							= SetStringFromDB($arr_rs_company[0]["AD_TYPE"]); 
		if($rs_ad_type == "")
			$rs_ad_type = "미정";
	}

	require_once "../../_PHPExcel/Classes/PHPExcel.php";

	$objPHPExcel = new PHPExcel();

	$sheetIndex = $objPHPExcel->setActiveSheetIndex(0);

	//스타일
	$defaultStyle = array(
	  'font'  => array(
        'size'  => 9,
        'name'  => '맑은 고딕'
	  ),'borders' => array(
			'allborders' => array(
		  	'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		),'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    )
	);

	$headerStyle = array(
	  'font'  => array(
        'size'  => 9,
        'name'  => '맑은 고딕'
	  ),'borders' => array(
			'allborders' => array(
		  	'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		),'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    )
	);
	
	$fillYellow =  array(
		'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('rgb' => 'FFFF00')
		)
	);
	
	$fillRed =  array(
		'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('rgb' => 'FF0000')
		)
	);

	//엑셀 내용 출력 시작
	$k = 1;
    
	//1행(행 제목)
	$sheetIndex->setCellValue("A".$k,iconv("EUC-KR", "UTF-8","사용안함"));
	$sheetIndex->setCellValue("B".$k,iconv("EUC-KR", "UTF-8","사용안함"));
	$sheetIndex->setCellValue("C".$k,iconv("EUC-KR", "UTF-8","보내는분성명"));
	$sheetIndex->setCellValue("D".$k,iconv("EUC-KR", "UTF-8","보내는분전화번호"));
	$sheetIndex->setCellValue("E".$k,iconv("EUC-KR", "UTF-8","보내는분기타연락처"));
	$sheetIndex->setCellValue("F".$k,iconv("EUC-KR", "UTF-8","보내는분우편번호"));
	$sheetIndex->setCellValue("G".$k,iconv("EUC-KR", "UTF-8","보내는분주소(전체, 분할)"));
	$sheetIndex->setCellValue("H".$k,iconv("EUC-KR", "UTF-8","받는분성명"));
	$sheetIndex->setCellValue("I".$k,iconv("EUC-KR", "UTF-8","받는분전화번호"));
	$sheetIndex->setCellValue("J".$k,iconv("EUC-KR", "UTF-8","받는분기타연락처"));
	$sheetIndex->setCellValue("K".$k,iconv("EUC-KR", "UTF-8","받는분우편번호"));
	$sheetIndex->setCellValue("L".$k,iconv("EUC-KR", "UTF-8","받는분주소(전체, 분할)"));
	$sheetIndex->setCellValue("M".$k,iconv("EUC-KR", "UTF-8","운송장번호"));
	$sheetIndex->setCellValue("N".$k,iconv("EUC-KR", "UTF-8","고객주문번호"));
	$sheetIndex->setCellValue("O".$k,iconv("EUC-KR", "UTF-8","품목명"));
	$sheetIndex->setCellValue("P".$k,iconv("EUC-KR", "UTF-8","단가"));
	$sheetIndex->setCellValue("Q".$k,iconv("EUC-KR", "UTF-8","박스수량"));
	$sheetIndex->setCellValue("R".$k,iconv("EUC-KR", "UTF-8","박스타입"));
	$sheetIndex->setCellValue("S".$k,iconv("EUC-KR", "UTF-8","기본운임"));
	$sheetIndex->setCellValue("T".$k,iconv("EUC-KR", "UTF-8","배송메세지1"));
	$sheetIndex->setCellValue("U".$k,iconv("EUC-KR", "UTF-8","배송메세지2"));
	$sheetIndex->setCellValue("V".$k,iconv("EUC-KR", "UTF-8","운임구분"));
	
	//스타일 적용
	$sheetIndex->getStyle("A$k:V$k")->applyFromArray($headerStyle);
	$sheetIndex->getStyle("A$k:V$k")->getFont()->setSize(10)->setBold(true);

	//노란배경
	$sheetIndex->getStyle("C$k")->applyFromArray($fillYellow);
	$sheetIndex->getStyle("D$k")->applyFromArray($fillYellow);
	$sheetIndex->getStyle("H$k")->applyFromArray($fillYellow);
	$sheetIndex->getStyle("I$k")->applyFromArray($fillYellow);
	$sheetIndex->getStyle("J$k")->applyFromArray($fillYellow);
	$sheetIndex->getStyle("L$k")->applyFromArray($fillYellow);
	$sheetIndex->getStyle("O$k")->applyFromArray($fillYellow);
	$sheetIndex->getStyle("V$k")->applyFromArray($fillYellow);

	//빨간배경
	$sheetIndex->getStyle("G$k")->applyFromArray($fillRed);

	//내용
	if (sizeof($arr_rs_goods) > 0) {
		for ($j = 0 ; $j < sizeof($arr_rs_goods); $j++) {
			$k += 1;
			$GOODS_NO					= trim($arr_rs_goods[$j]["GOODS_NO"]);
			$GOODS_NAME				= trim($arr_rs_goods[$j]["GOODS_NAME"]);
			$GOODS_SUB_NAME		= trim($arr_rs_goods[$j]["GOODS_SUB_NAME"]);
			$REQ_QTY					= trim($arr_rs_goods[$j]["REQ_QTY"]);
			$BUY_PRICE				= trim($arr_rs_goods[$j]["BUY_PRICE"]);
			$BUY_TOTAL_PRICE	= trim($arr_rs_goods[$j]["BUY_TOTAL_PRICE"]);
			$RECEIVER_NM			= trim($arr_rs_goods[$j]["RECEIVER_NM"]);
			$RECEIVER_ADDR		= trim($arr_rs_goods[$j]["RECEIVER_ADDR"]);
			$RECEIVER_PHONE		= trim($arr_rs_goods[$j]["RECEIVER_PHONE"]);
			$RECEIVER_HPHONE	= trim($arr_rs_goods[$j]["RECEIVER_HPHONE"]);
			$MEMO1						= trim($arr_rs_goods[$j]["MEMO1"]);
			$MEMO2						= trim($arr_rs_goods[$j]["MEMO2"]);
			$MEMO3						= trim($arr_rs_goods[$j]["MEMO3"]);
			$TO_HERE					= trim($arr_rs_goods[$j]["TO_HERE"]);
			$ORDER_GOODS_NO		= trim($arr_rs_goods[$j]["ORDER_GOODS_NO"]);

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

			$sheetIndex->setCellValue("C".$k,iconv("EUC-KR", "UTF-8",$RECEIVER_NM));
			$sheetIndex->setCellValue("D".$k,iconv("EUC-KR", "UTF-8",$RECEIVER_PHONE));
			$sheetIndex->setCellValue("G".$k,iconv("EUC-KR", "UTF-8",$RECEIVER_ADDR));
			$sheetIndex->setCellValue("H".$k,iconv("EUC-KR", "UTF-8",$RECEIVER_NM));
			$sheetIndex->setCellValue("I".$k,iconv("EUC-KR", "UTF-8",$RECEIVER_PHONE));
			$sheetIndex->setCellValue("J".$k,iconv("EUC-KR", "UTF-8",$RECEIVER_PHONE));
			$sheetIndex->setCellValue("L".$k,iconv("EUC-KR", "UTF-8",$RECEIVER_ADDR));
			$sheetIndex->setCellValue("O".$k,iconv("EUC-KR", "UTF-8",$GOODS_NAME ." / ".$REQ_QTY."개"));
			$sheetIndex->setCellValue("P".$k,iconv("EUC-KR", "UTF-8",$BUY_PRICE));
			$sheetIndex->setCellValue("Q".$k,iconv("EUC-KR", "UTF-8",''));	//20210707 효곤대리 요청 박스수량 보이지 않도록 처리
			$sheetIndex->setCellValue("T".$k,iconv("EUC-KR", "UTF-8",$MEMO1));
			// $sheetIndex->setCellValue("U".$k,iconv("EUC-KR", "UTF-8",$MEMO2));
			$sheetIndex->setCellValue("V".$k,iconv("EUC-KR", "UTF-8",$rs_ad_type));

			//스타일 적용
			$sheetIndex->getStyle("A$k:V$k")->applyFromArray($defaultStyle);
			
			//자동 개행
			$sheetIndex->getStyle("A$k:V$k")
			->getAlignment()
			->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$sheetIndex->getStyle("A$k:V$k")
			->getAlignment()
			->setWrapText(true);
			
			$objPHPExcel->getActiveSheet()->getRowDimension($k)->setRowHeight(24);
		}
	}

	//내용과 유의사항 사이의 행 높이 조절
	$k += 1;
	$sheetIndex->getRowDimension($k)->setRowHeight(12);
	
	//유의사항
	$k += 1;
	$sheetIndex->setCellValue("B".$k,iconv("EUC-KR", "UTF-8","★제일 윗칸 노란색부분만 아래에 적어주시면 되고 빨간색부분은 반품회수지 주소입니다. 저주소로 다 적어주시면 됩니다.★"))->getStyle("A$k")->getFont()->setSize(10)->setBold(true);
	$k += 1;
	$sheetIndex->setCellValue("B".$k,iconv("EUC-KR", "UTF-8","★품목명에는 제품명 / 색상 / 수량 을 슬러쉬로 구분하여 다 적어주셔야 합니다.★"))->getStyle("A$k")->getFont()->setSize(10)->setBold(true);
	$k += 1;
	$sheetIndex->setCellValue("B".$k,iconv("EUC-KR", "UTF-8","★운임구분에는 선불or착불 적어주시면 됩니다★"))->getStyle("A$k")->getFont()->setSize(10)->setBold(true);
	$k += 1;
	$sheetIndex->setCellValue("B".$k,iconv("EUC-KR", "UTF-8","★보내는분성명에 꼭 커스커즈에 등록된 사업자상호를 기재해주세요★"))->getStyle("A$k")->getFont()->setSize(10)->setBold(true);

	$k +=1;
	$sheetIndex->setCellvalue("A".$k,iconv("EUC-KR", "UTF-8","특이사항 : ".$MEMO));
	//폭
	$small_margin1 = 13;//2.54
	$small_margin2 = 15;//2.85
	$big_margin1 = 34.42;//6.54
	$big_margin2 = 47.94;//9.11

	$sheetIndex->getColumnDimension("A")->setWidth($small_margin1);
	$sheetIndex->getColumnDimension("B")->setWidth($small_margin1);
	$sheetIndex->getColumnDimension("C")->setWidth($small_margin1);
	$sheetIndex->getColumnDimension("D")->setWidth($small_margin2);
	$sheetIndex->getColumnDimension("E")->setWidth($small_margin2);
	$sheetIndex->getColumnDimension("F")->setWidth($small_margin1);
	$sheetIndex->getColumnDimension("G")->setWidth($big_margin1);
	$sheetIndex->getColumnDimension("H")->setWidth($small_margin1);
	$sheetIndex->getColumnDimension("I")->setWidth($small_margin2);
	$sheetIndex->getColumnDimension("J")->setWidth($small_margin2);
	$sheetIndex->getColumnDimension("K")->setWidth($small_margin1);
	$sheetIndex->getColumnDimension("L")->setWidth($big_margin1);
	$sheetIndex->getColumnDimension("M")->setWidth($small_margin2);
	$sheetIndex->getColumnDimension("N")->setWidth($small_margin1);
	$sheetIndex->getColumnDimension("O")->setWidth($big_margin2);
	$sheetIndex->getColumnDimension("P")->setWidth($small_margin1);
	$sheetIndex->getColumnDimension("Q")->setWidth($small_margin1);
	$sheetIndex->getColumnDimension("R")->setWidth($small_margin1);
	$sheetIndex->getColumnDimension("S")->setWidth($small_margin1);
	$sheetIndex->getColumnDimension("T")->setWidth($small_margin1);
	$sheetIndex->getColumnDimension("U")->setWidth($small_margin1);
	$sheetIndex->getColumnDimension("V")->setWidth($small_margin1);
	
	//높이
	// $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(24);
	// $objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(36.75);

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