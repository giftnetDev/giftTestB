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

	$BStyle = array(
	  'borders' => array(
		'allborders' => array(
		  'style' => PHPExcel_Style_Border::BORDER_THIN
		)
	  ),
	  'font'  => array(
        'size'  => 9,
        'name'  => '굴림'
	  )
	);


	//1열
	$sheetIndex->setCellValue('A1',iconv("EUC-KR", "UTF-8","발 주 서"));
	$sheetIndex->mergeCells('A1:K1');
	$sheetIndex->getStyle('A1')->getFont()->setSize(20)->setBold(true);
	$sheetIndex->getStyle('A1')->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheetIndex->getRowDimension(1)->setRowHeight(40);

	//2열
	$sheetIndex->setCellValue("I2", iconv("EUC-KR", "UTF-8","발주일 : ".date("Y년 n월 j일",strtotime($REQ_DATE))))->mergeCells('I2:K2');
	$sheetIndex->getStyle('I2:K2')->getFont()->setSize(9);


	$sheetIndex->getRowDimension(3)->setRowHeight(28);
	$sheetIndex->getRowDimension(4)->setRowHeight(28);
	$sheetIndex->getRowDimension(5)->setRowHeight(28);
	$sheetIndex->getRowDimension(6)->setRowHeight(28);
	$sheetIndex->getRowDimension(7)->setRowHeight(28);

	//3열
	$sheetIndex ->setCellValue("A3", iconv("EUC-KR", "UTF-8","발신처"))
				->setCellValue("B3", iconv("EUC-KR", "UTF-8",$SENDER_CP))->mergeCells('B3:E3')
				->setCellValue("F3", iconv("EUC-KR", "UTF-8","수신처"))->mergeCells('F3:G3')
				->setCellValue("H3", iconv("EUC-KR", "UTF-8",$BUY_CP_NM))->mergeCells('H3:K3');
	
	$sheetIndex->getStyle('A3')->getFont()->setBold(true);
	$sheetIndex->getStyle('A3')->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheetIndex->getStyle('F3')->getFont()->setBold(true);
	$sheetIndex->getStyle('F3')->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$sheetIndex->getStyle('B3')->getAlignment()->setWrapText(true)
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheetIndex->getStyle('H3')->getAlignment()->setWrapText(true)
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	//4열
	$sheetIndex ->setCellValue("A4", iconv("EUC-KR", "UTF-8","대표자"))
				->setCellValue("B4", iconv("EUC-KR", "UTF-8",$CEO_NM))->mergeCells('B4:E4')
				->setCellValue("F4", iconv("EUC-KR", "UTF-8","담당자"))->mergeCells('F4:G4')
				->setCellValue("H4", iconv("EUC-KR", "UTF-8",$BUY_MANAGER_NM))->mergeCells('H4:K4');

	$sheetIndex->getStyle('A4')->getFont()->setBold(true);
	$sheetIndex->getStyle('A4')->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheetIndex->getStyle('F4')->getFont()->setBold(true);
	$sheetIndex->getStyle('F4')->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$sheetIndex->getStyle('B4')->getAlignment()->setWrapText(true)
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheetIndex->getStyle('H4')->getAlignment()->setWrapText(true)
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	//5열
	$sheetIndex ->setCellValue("A5", iconv("EUC-KR", "UTF-8","주소"))
				->setCellValue("B5", iconv("EUC-KR", "UTF-8",$SENDER_ADDR))->mergeCells('B5:E5')
				->setCellValue("F5", iconv("EUC-KR", "UTF-8","연락처"))->mergeCells('F5:G5')
				->setCellValue("H5", iconv("EUC-KR", "UTF-8",$BUY_CP_PHONE))->mergeCells('H5:K5');

	$sheetIndex->getStyle('A5')->getFont()->setBold(true);
	$sheetIndex->getStyle('A5')->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheetIndex->getStyle('F5')->getFont()->setBold(true);
	$sheetIndex->getStyle('F5')->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$sheetIndex->getStyle('B5')->getAlignment()->setWrapText(true)
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheetIndex->getStyle('H5')->getAlignment()->setWrapText(true)
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	//6열
	$sheetIndex ->setCellValue("A6", iconv("EUC-KR", "UTF-8","연락처"))->mergeCells('A6:A7')
				->setCellValue("B6", iconv("EUC-KR", "UTF-8",$SENDER_PHONE))->mergeCells('B6:E7')
				->setCellValue("F6", iconv("EUC-KR", "UTF-8","특이사항"))->mergeCells('F6:G7')
				->setCellValue("H6", iconv("EUC-KR", "UTF-8",$MEMO))->mergeCells('H6:K7');

	$sheetIndex->getStyle('A6')->getFont()->setBold(true);
	$sheetIndex->getStyle('A6')->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheetIndex->getStyle('F6')->getFont()->setBold(true);
	$sheetIndex->getStyle('F6')->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$sheetIndex->getStyle('B6')->getAlignment()->setWrapText(true)
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheetIndex->getStyle('H6')->getAlignment()->setWrapText(true)
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	//7열 - 2017-09-15 배송방식 -> 연락처에 팩스추가 
	/*
	$sheetIndex->setCellValue("A7", iconv("EUC-KR", "UTF-8","배송방식"))
			   ->setCellValue("B7", iconv("EUC-KR", "UTF-8",$DELIVERY_TYPE))->mergeCells('B7:E7');

	$sheetIndex->getStyle('A7')->getFont()->setBold(true);
	$sheetIndex->getStyle('A7')->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$sheetIndex->getStyle('C7')->getAlignment()->setWrapText(true)
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheetIndex->getStyle('H7')->getAlignment()->setWrapText(true)
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	*/
	$objPHPExcel->getActiveSheet()->getStyle('A3:K7')->applyFromArray($BStyle);
	

	//8열 - 마진
	//9열 (품명	수량	판매단가(+VAT)	합계(+VAT)	수취인명	수취인연락처	수취인휴대폰	수취인주소	비고 1	비고 2)

	$sheetIndex->setCellValue("A9", iconv("EUC-KR", "UTF-8","품명"))->setCellValue("B9", iconv("EUC-KR", "UTF-8","수량"))
	->setCellValue("C9", iconv("EUC-KR", "UTF-8","단가(VAT)"))->setCellValue("D9", iconv("EUC-KR", "UTF-8","합계(VAT)"))
	->setCellValue("E9", iconv("EUC-KR", "UTF-8","수취인명"))->setCellValue("F9", iconv("EUC-KR", "UTF-8","수취인연락처"))
	->setCellValue("G9", iconv("EUC-KR", "UTF-8","수취인휴대폰"))->setCellValue("H9", iconv("EUC-KR", "UTF-8","수취인주소"))
	->setCellValue("I9", iconv("EUC-KR", "UTF-8","비고-작업"))->setCellValue("J9", iconv("EUC-KR", "UTF-8","비고-주문지"))
	->setCellValue("K9", iconv("EUC-KR", "UTF-8","비고-발송자"));

	$sheetIndex->getRowDimension(9)->setRowHeight(25);

	$sheetIndex->getStyle('A9:K9')->getFont()->setBold(true);
	$sheetIndex->getStyle('A9')->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	if (sizeof($arr_rs_goods) > 0) {
		$k = 0;
		$p = 1;
		for ($j = 0 ; $j < sizeof($arr_rs_goods); $j++) {

			$GOODS_NAME					= trim(setStringFromDB($arr_rs_goods[$j]["GOODS_NAME"]));
			$GOODS_SUB_NAME				= trim(setStringFromDB($arr_rs_goods[$j]["GOODS_SUB_NAME"]));
			$REQ_QTY					= trim($arr_rs_goods[$j]["REQ_QTY"]);
			$BUY_PRICE					= trim($arr_rs_goods[$j]["BUY_PRICE"]);
			$BUY_TOTAL_PRICE			= trim($arr_rs_goods[$j]["BUY_TOTAL_PRICE"]);

			$RECEIVER_NM				= trim(setStringFromDB($arr_rs_goods[$j]["RECEIVER_NM"]));
			$RECEIVER_ADDR				= trim(setStringFromDB($arr_rs_goods[$j]["RECEIVER_ADDR"]));
			$RECEIVER_PHONE				= trim($arr_rs_goods[$j]["RECEIVER_PHONE"]);
			$RECEIVER_HPHONE			= trim($arr_rs_goods[$j]["RECEIVER_HPHONE"]);
			$MEMO1						= trim(setStringFromDB($arr_rs_goods[$j]["MEMO1"]));
			$MEMO2						= trim(iconv("UTF-8","EUC-KR",setStringFromDB($arr_rs_goods[$j]["MEMO2"])));
			$MEMO3						= trim(setStringFromDB($arr_rs_goods[$j]["MEMO3"]));

			$TO_HERE					= trim($arr_rs_goods[$j]["TO_HERE"]);

			$ORDER_GOODS_NO				= trim($arr_rs_goods[$j]["ORDER_GOODS_NO"]);
			$arr_rs_individual = listDeliveryIndividual($conn, $ORDER_GOODS_NO, "DESC");

			if(sizeof($arr_rs_individual) > 0 && $TO_HERE != 'Y') { 

				//수령자	연락처	휴대폰번호	주소	배송메모	송장상품명	상품수량
				$objPHPExcel->createSheet();

				//$SHORT_GOODS_NAME = cleanUTF(iconv("EUC-KR", "UTF-8",$GOODS_NAME." ".$GOODS_SUB_NAME));
				//if(strlen($SHORT_GOODS_NAME) > 20)
				//	$SHORT_GOODS_NAME = substr($SHORT_GOODS_NAME, 0, 20)."...";


				$objPHPExcel->setActiveSheetIndex($p)
					->setTitle(iconv("EUC-KR", "UTF-8",($j+1)."번 개별배송 리스트"))
					->setCellValue("A1", iconv("EUC-KR", "UTF-8","수령자"))->setCellValue("B1", iconv("EUC-KR", "UTF-8","연락처"))
					->setCellValue("C1", iconv("EUC-KR", "UTF-8","휴대폰번호"))->setCellValue("D1", iconv("EUC-KR", "UTF-8","주소"))
					->setCellValue("E1", iconv("EUC-KR", "UTF-8","배송메모"))->setCellValue("F1", iconv("EUC-KR", "UTF-8","송장상품명"))
					->setCellValue("G1", iconv("EUC-KR", "UTF-8","상품수량"));

				$objPHPExcel->setActiveSheetIndex($p)->getStyle('A1')->getFont()->setBold(true);
				$objPHPExcel->setActiveSheetIndex($p)->getStyle('A1')->getAlignment()
					->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$objPHPExcel->setActiveSheetIndex($p)->getStyle('B1')->getFont()->setBold(true);
				$objPHPExcel->setActiveSheetIndex($p)->getStyle('B1')->getAlignment()
					->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$objPHPExcel->setActiveSheetIndex($p)->getStyle('C1')->getFont()->setBold(true);
				$objPHPExcel->setActiveSheetIndex($p)->getStyle('C1')->getAlignment()
					->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$objPHPExcel->setActiveSheetIndex($p)->getStyle('D1')->getFont()->setBold(true);
				$objPHPExcel->setActiveSheetIndex($p)->getStyle('D1')->getAlignment()
					->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$objPHPExcel->setActiveSheetIndex($p)->getStyle('E1')->getFont()->setBold(true);
				$objPHPExcel->setActiveSheetIndex($p)->getStyle('E1')->getAlignment()
					->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$objPHPExcel->setActiveSheetIndex($p)->getStyle('F1')->getFont()->setBold(true);
				$objPHPExcel->setActiveSheetIndex($p)->getStyle('F1')->getAlignment()
					->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$objPHPExcel->setActiveSheetIndex($p)->getStyle('G1')->getFont()->setBold(true);
				$objPHPExcel->setActiveSheetIndex($p)->getStyle('G1')->getAlignment()
					->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$objPHPExcel->setActiveSheetIndex($p)->getColumnDimension('A')->setWidth(25);
				$objPHPExcel->setActiveSheetIndex($p)->getColumnDimension('B')->setWidth(15);
				$objPHPExcel->setActiveSheetIndex($p)->getColumnDimension('C')->setWidth(15);
				$objPHPExcel->setActiveSheetIndex($p)->getColumnDimension('D')->setWidth(40);
				$objPHPExcel->setActiveSheetIndex($p)->getColumnDimension('E')->setWidth(25);
				$objPHPExcel->setActiveSheetIndex($p)->getColumnDimension('F')->setWidth(25);
				$objPHPExcel->setActiveSheetIndex($p)->getColumnDimension('G')->setWidth(10);

				$row_index = 0;
				for($o = 0; $o < sizeof($arr_rs_individual); $o ++) { 

					//$R_ZIPCODE	     = trim($arr_rs_individual[$o]["R_ZIPCODE"]); 
					$R_ADDR1 			 = trim($arr_rs_individual[$o]["R_ADDR1"]);
					$R_MEM_NM			 = trim($arr_rs_individual[$o]["R_MEM_NM"]);
					$R_PHONE			 = trim($arr_rs_individual[$o]["R_PHONE"]); 
					$R_HPHONE			 = trim($arr_rs_individual[$o]["R_HPHONE"]); 
					$SUB_QTY			 = trim($arr_rs_individual[$o]["SUB_QTY"]);
					$MEMO				 = trim($arr_rs_individual[$o]["MEMO"]);
					$GOODS_DELIVERY_NAME = trim($arr_rs_individual[$o]["GOODS_DELIVERY_NAME"]);
					$USE_TF				 = trim($arr_rs_individual[$o]["USE_TF"]);

					if($USE_TF != "Y") continue;

					$q = $row_index + 2;
					$objPHPExcel->setActiveSheetIndex($p)
						->setCellValue("A$q", iconv("EUC-KR", "UTF-8",$R_MEM_NM))
						->setCellValue("B$q", iconv("EUC-KR", "UTF-8",$R_PHONE))
						->setCellValue("C$q", iconv("EUC-KR", "UTF-8",$R_HPHONE))
						->setCellValue("D$q", iconv("EUC-KR", "UTF-8",$R_ADDR1))
						->setCellValue("E$q", iconv("EUC-KR", "UTF-8",$MEMO))
						->setCellValue("F$q", iconv("EUC-KR", "UTF-8",$GOODS_DELIVERY_NAME))
						->setCellValue("G$q", iconv("EUC-KR", "UTF-8",$SUB_QTY));

					$row_index += 1;

				}

				$objPHPExcel->setActiveSheetIndex($p)->getStyle('A1:G'.$q)->applyFromArray($BStyle);

				$p = $p + 1;
			}

			if($REQ_QTY <> "") 
				$REQ_QTY = number_format($REQ_QTY);
			
			if($BUY_PRICE <> "") 
				$BUY_PRICE = number_format($BUY_PRICE);
			
			if($BUY_TOTAL_PRICE <> "") 
				$BUY_TOTAL_PRICE = number_format($BUY_TOTAL_PRICE);

			$k = $j + 10;



			// 2016-12-15 외부업체 발송인데 기타 시트를 참조안하고 수령처로 보내는 일때문에 수령처 정보 제거
			if(sizeof($arr_rs_individual) > 0 && $TO_HERE != 'Y') { 
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A$k", iconv("EUC-KR", "UTF-8",($j+1).". ".$GOODS_NAME." ".$GOODS_SUB_NAME))
				->setCellValue("B$k", iconv("EUC-KR", "UTF-8",$REQ_QTY))
				->setCellValue("C$k", iconv("EUC-KR", "UTF-8",$BUY_PRICE))
				->setCellValue("D$k", iconv("EUC-KR", "UTF-8",$BUY_TOTAL_PRICE))
				->setCellValue("E$k", iconv("EUC-KR", "UTF-8",""))
				->setCellValue("F$k", iconv("EUC-KR", "UTF-8",""))
				->setCellValue("G$k", iconv("EUC-KR", "UTF-8",""))
				->setCellValue("H$k", iconv("EUC-KR", "UTF-8","해당 상품 시트 참조"))
				->setCellValue("I$k", iconv("EUC-KR", "UTF-8",$MEMO1))
				->setCellValue("J$k", iconv("EUC-KR", "UTF-8",$MEMO2))
				->setCellValue("K$k", iconv("EUC-KR", "UTF-8",$MEMO3));
			} else {
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A$k", iconv("EUC-KR", "UTF-8",($j+1).". ".$GOODS_NAME." ".$GOODS_SUB_NAME))
					->setCellValue("B$k", iconv("EUC-KR", "UTF-8",$REQ_QTY))
					->setCellValue("C$k", iconv("EUC-KR", "UTF-8",$BUY_PRICE))
					->setCellValue("D$k", iconv("EUC-KR", "UTF-8",$BUY_TOTAL_PRICE))
					->setCellValue("E$k", iconv("EUC-KR", "UTF-8",$RECEIVER_NM))
					->setCellValue("F$k", iconv("EUC-KR", "UTF-8",$RECEIVER_PHONE))
					->setCellValue("G$k", iconv("EUC-KR", "UTF-8",$RECEIVER_HPHONE))
					->setCellValue("H$k", iconv("EUC-KR", "UTF-8",$RECEIVER_ADDR))
					->setCellValue("I$k", iconv("EUC-KR", "UTF-8",$MEMO1))
					->setCellValue("J$k", iconv("EUC-KR", "UTF-8",$MEMO2))
					->setCellValue("K$k", iconv("EUC-KR", "UTF-8",$MEMO3));
			}

			$objPHPExcel->setActiveSheetIndex(0)->getStyle("A$k:K$k")->getAlignment()->setWrapText(true);

		}
	}

	$total_row = $k+((sizeof($arr_rs_goods) <= 13 ? 13 - sizeof($arr_rs_goods) : 1));
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A".$total_row, iconv("EUC-KR", "UTF-8","합계"))->mergeCells("A".$total_row.":D".$total_row)
				->setCellValue("J".$total_row, iconv("EUC-KR", "UTF-8",number_format($TOTAL_REQ_QTY)." 개"))
				->setCellValue("K".$total_row, iconv("EUC-KR", "UTF-8",number_format($TOTAL_BUY_TOTAL_PRICE)." 원"));

	$sheetIndex->getStyle("A".$total_row.":C".$total_row)->getFont()->setBold(true);
	$sheetIndex->getStyle("A".$total_row.":C".$total_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->getActiveSheet()->getStyle("A9:K".$total_row)->applyFromArray($BStyle);

	$sheetIndex->getColumnDimension('A')->setWidth(17.64);
	$sheetIndex->getColumnDimension('B')->setWidth(4.09);
	$sheetIndex->getColumnDimension('C')->setWidth(8.55);
	$sheetIndex->getColumnDimension('D')->setWidth(8.55);
	$sheetIndex->getColumnDimension('E')->setWidth(7.55);
	$sheetIndex->getColumnDimension('F')->setWidth(12.55);
	$sheetIndex->getColumnDimension('G')->setWidth(11.09);
	$sheetIndex->getColumnDimension('H')->setWidth(18.82);
	$sheetIndex->getColumnDimension('I')->setWidth(8.06);
	$sheetIndex->getColumnDimension('J')->setWidth(9.91);
	$sheetIndex->getColumnDimension('K')->setWidth(9.91);
	
	// Rename sheet
	$objPHPExcel->setActiveSheetIndex(0)->setTitle(iconv("EUC-KR", "UTF-8",'발주서'));

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	//$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(100);

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
				
