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

	$arr_rs_goods = listGoodsRequestGoods($conn, $req_no, "N");

	require_once "../../_PHPExcel/Classes/PHPExcel.php";

	$objPHPExcel = new PHPExcel();

	$sheetIndex = $objPHPExcel->setActiveSheetIndex(0);

	$BStyle = array(
	  'borders' => array(
		'allborders' => array(
		  'style' => PHPExcel_Style_Border::BORDER_THIN
		)
	  )
	);

	$outline_style = array(
	  'borders' => array(
		'outline' => array(
		  'style' => PHPExcel_Style_Border::BORDER_MEDIUM
		)
	  )
	);

	$listTitleStyle_left = array(
		'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('rgb' => 'dce6f1')
		)
	);

	$listTitleStyle_right = array(
		'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('rgb' => 'fde9d9')
		)
	);

	$k = 1;

	$sheetIndex->setCellValue("A$k",iconv("EUC-KR", "UTF-8","발 주 서"));
	$sheetIndex->mergeCells("A$k:O$k");
	$sheetIndex->getStyle("A$k")->getFont()->setSize(26)->setBold(true);
	$sheetIndex->getStyle("A$k")->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheetIndex->getRowDimension($k)->setRowHeight(36);
	$k += 1;

	$sheetIndex->setCellValue("M$k",iconv("EUC-KR", "UTF-8","발주일 : ".date("Y년 n월 j일",strtotime($REQ_DATE))));
	$sheetIndex->mergeCells("M$k:O$k");
	$sheetIndex->getStyle("M$k")->getFont()->setSize(11);
	$sheetIndex->getStyle("M$k")->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheetIndex->getRowDimension($k)->setRowHeight(28.50);
	$k += 1;

	$sheetIndex ->setCellValue("A$k", iconv("EUC-KR", "UTF-8","발신처"))->mergeCells("A$k:B$k")
				->setCellValue("C$k", iconv("EUC-KR", "UTF-8",$SENDER_CP))->mergeCells("C$k:I$k")
				->setCellValue("J$k", iconv("EUC-KR", "UTF-8","수신처"))
				->setCellValue("K$k", iconv("EUC-KR", "UTF-8",$BUY_CP_NM))->mergeCells("K$k:O$k");
	
	$sheetIndex->getStyle("A$k")->getFont()->setBold(true);
	$sheetIndex->getStyle("A$k")->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheetIndex->getStyle("J$k")->getFont()->setBold(true);
	$sheetIndex->getStyle("J$k")->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$sheetIndex->getStyle("C$k")->getAlignment()->setWrapText(true)
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheetIndex->getStyle("K$k")->getAlignment()->setWrapText(true)
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheetIndex->getRowDimension($k)->setRowHeight(25.50);
	$k += 1;

	$sheetIndex ->setCellValue("A$k", iconv("EUC-KR", "UTF-8","대표자"))->mergeCells("A$k:B$k")
				->setCellValue("C$k", iconv("EUC-KR", "UTF-8",$CEO_NM))->mergeCells("C$k:I$k")
				->setCellValue("J$k", iconv("EUC-KR", "UTF-8","담당자"))
				->setCellValue("K$k", iconv("EUC-KR", "UTF-8",$BUY_MANAGER_NM))->mergeCells("K$k:O$k");
	
	$sheetIndex->getStyle("A$k")->getFont()->setBold(true);
	$sheetIndex->getStyle("A$k")->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheetIndex->getStyle("J$k")->getFont()->setBold(true);
	$sheetIndex->getStyle("J$k")->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$sheetIndex->getStyle("C$k")->getAlignment()->setWrapText(true)
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheetIndex->getStyle("K$k")->getAlignment()->setWrapText(true)
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheetIndex->getRowDimension($k)->setRowHeight(25.50);
	$k += 1;


	$sheetIndex ->setCellValue("A$k", iconv("EUC-KR", "UTF-8","주소"))->mergeCells("A$k:B$k")
				->setCellValue("C$k", iconv("EUC-KR", "UTF-8",$SENDER_ADDR))->mergeCells("C$k:I$k")
				->setCellValue("J$k", iconv("EUC-KR", "UTF-8","연락처"))
				->setCellValue("K$k", iconv("EUC-KR", "UTF-8",$BUY_CP_PHONE))->mergeCells("K$k:O$k");
	
	$sheetIndex->getStyle("A$k")->getFont()->setBold(true);
	$sheetIndex->getStyle("A$k")->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheetIndex->getStyle("J$k")->getFont()->setBold(true);
	$sheetIndex->getStyle("J$k")->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$sheetIndex->getStyle("C$k")->getAlignment()->setWrapText(true)
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheetIndex->getStyle("K$k")->getAlignment()->setWrapText(true)
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheetIndex->getRowDimension($k)->setRowHeight(25.50);
	$k += 1;

	$sheetIndex ->setCellValue("A$k", iconv("EUC-KR", "UTF-8","연락처"))->mergeCells("A$k:B$k")
				->setCellValue("C$k", iconv("EUC-KR", "UTF-8",$SENDER_PHONE))->mergeCells("C$k:I$k")
				->setCellValue("J$k", iconv("EUC-KR", "UTF-8","특이사항"))->mergeCells("J$k:J".($k+1))
				->setCellValue("K$k", iconv("EUC-KR", "UTF-8",$MEMO))->mergeCells("K$k:O".($k+1));
	
	$sheetIndex->getStyle("A$k")->getFont()->setBold(true);
	$sheetIndex->getStyle("A$k")->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheetIndex->getStyle("J$k")->getFont()->setBold(true);
	$sheetIndex->getStyle("J$k")->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$sheetIndex->getStyle("C$k")->getAlignment()->setWrapText(true)
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheetIndex->getStyle("K$k")->getAlignment()->setWrapText(true)
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheetIndex->getRowDimension($k)->setRowHeight(25.50);
	$k += 1;
		
	$sheetIndex ->setCellValue("A$k", iconv("EUC-KR", "UTF-8","배송방식"))->mergeCells("A$k:B$k")
				->setCellValue("C$k", iconv("EUC-KR", "UTF-8"," □ 직배송        ■  택배          □ 기타(       )"))->mergeCells("C$k:I$k");
	
	$sheetIndex->getStyle("A$k")->getFont()->setBold(true);
	$sheetIndex->getStyle("A$k")->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$sheetIndex->getStyle("C$k")->getAlignment()->setWrapText(true)
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheetIndex->getRowDimension($k)->setRowHeight(25.50);
	$k += 1;

	$objPHPExcel->getActiveSheet()->getStyle("A3:O".($k-1))->applyFromArray($BStyle);
	$objPHPExcel->getActiveSheet()->getStyle("A3:O".($k-1))->applyFromArray($outline_style);
	
	$sheetIndex->getRowDimension($k)->setRowHeight(19.50);
	$k += 1;
	$sheetIndex->getRowDimension($k)->setRowHeight(19.50);
	$k += 1;

	$sheetIndex->setCellValue("K6",iconv("EUC-KR","UTF-8",$MEMO));

	//NO	코드	품명	규격	수량	" 판매단가(+VAT) "	" 합계(+VAT) "	"고객주문번호"	주문자명	수취인명	우편번호	수취인주소	수취인연락처	수취인휴대폰	비고

	$sheetIndex ->setCellValue("A$k", iconv("EUC-KR", "UTF-8","NO"))
	            ->setCellValue("B$k", iconv("EUC-KR", "UTF-8","코드"))   
			    ->setCellValue("C$k", iconv("EUC-KR", "UTF-8","품명"))
	            ->setCellValue("D$k", iconv("EUC-KR", "UTF-8","규격"))
			    ->setCellValue("E$k", iconv("EUC-KR", "UTF-8","수량"))
				->setCellValue("F$k", iconv("EUC-KR", "UTF-8","판매단가\n(+VAT)"))
				->setCellValue("G$k", iconv("EUC-KR", "UTF-8","합계\n(+VAT)"))
				->setCellValue("H$k", iconv("EUC-KR", "UTF-8","고객\n주문번호"))
				->setCellValue("I$k", iconv("EUC-KR", "UTF-8","주문자명"))
				->setCellValue("J$k", iconv("EUC-KR", "UTF-8","수취인명"))
				->setCellValue("K$k", iconv("EUC-KR", "UTF-8","우편번호"))
				->setCellValue("L$k", iconv("EUC-KR", "UTF-8","수취인주소"))
				->setCellValue("M$k", iconv("EUC-KR", "UTF-8","수취인연락처"))
				->setCellValue("N$k", iconv("EUC-KR", "UTF-8","수취인휴대폰"))
				->setCellValue("O$k", iconv("EUC-KR", "UTF-8","비고"));

	$sheetIndex->getStyle("F$k")->getAlignment()->setWrapText(true);
	$sheetIndex->getStyle("G$k")->getAlignment()->setWrapText(true);
	$sheetIndex->getStyle("H$k")->getAlignment()->setWrapText(true);

	//$sheetIndex->getRowDimension($k)->setRowHeight(30);

	$sheetIndex->getStyle("A$k:O$k")->getFont()->setSize(10)->setBold(true);
	$sheetIndex->getStyle("A$k:O$k")->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheetIndex->getStyle("A$k:G$k")->applyFromArray($listTitleStyle_left);
	$sheetIndex->getStyle("H$k:O$k")->applyFromArray($listTitleStyle_right);
	$k += 1;

	if (sizeof($arr_rs_goods) > 0) {
		$l = 0;
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
			for($p = 0; $p < sizeof($arr_rs_goods_extra); $p ++) { 
				$rs_extra_dcode	= SetStringFromDB($arr_rs_goods_extra[$p]["DCODE"]); 
			}
			
			//$MEMO1 = str_replace("발주메모 : ", "", $MEMO1);

			$REQ_DATE = date("Y-m-d", strtotime($REQ_DATE));

			$arr_rs_individual = listDeliveryIndividual($conn, $ORDER_GOODS_NO, "DESC");

			if(sizeof($arr_rs_individual) > 0) { 
				for($o = 0; $o < sizeof($arr_rs_individual); $o ++) { 

					$R_ADDR1 			 = trim($arr_rs_individual[$o]["R_ADDR1"]);
					$R_MEM_NM			 = trim($arr_rs_individual[$o]["R_MEM_NM"]);
					$R_PHONE			 = trim($arr_rs_individual[$o]["R_PHONE"]); 
					$R_HPHONE			 = trim($arr_rs_individual[$o]["R_HPHONE"]); 
					$SUB_QTY			 = trim($arr_rs_individual[$o]["SUB_QTY"]);
					$MEMO				 = trim($arr_rs_individual[$o]["MEMO"]);
					$GOODS_DELIVERY_NAME = trim($arr_rs_individual[$o]["GOODS_DELIVERY_NAME"]);
					$USE_TF				 = trim($arr_rs_individual[$o]["USE_TF"]);

					if($USE_TF != "Y") continue;

					$SUB_QTY_TOTAL = $SUB_QTY * $BUY_PRICE;

					$objPHPExcel->setActiveSheetIndex(0)->getRowDimension($k)->setRowHeight(-1); 
						
					$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue("A$k", iconv("EUC-KR", "UTF-8",($j + $o +1)))
						->setCellValue("B$k", iconv("EUC-KR", "UTF-8",$rs_extra_dcode))
						->setCellValue("C$k", iconv("EUC-KR", "UTF-8",$GOODS_NAME))
						->setCellValue("D$k", iconv("EUC-KR", "UTF-8",$GOODS_SUB_NAME))
						->setCellValue("E$k", iconv("EUC-KR", "UTF-8",number_format($SUB_QTY)))
						->setCellValue("F$k", iconv("EUC-KR", "UTF-8",number_format($BUY_PRICE)))
						->setCellValue("G$k", iconv("EUC-KR", "UTF-8",number_format($SUB_QTY_TOTAL)));

					$objPHPExcel->getActiveSheet()->getStyle("C".($k))->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->getStyle("C".($k))->getFont()->setSize(9);;

					$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue("H$k", iconv("EUC-KR", "UTF-8",""))
						->setCellValue("I$k", iconv("EUC-KR", "UTF-8",$MEMO2))
						->setCellValue("J$k", iconv("EUC-KR", "UTF-8",$R_MEM_NM))
						->setCellValue("K$k", iconv("EUC-KR", "UTF-8",""))
						->setCellValue("L$k", iconv("EUC-KR", "UTF-8",$R_ADDR1))
						->setCellValue("M$k", iconv("EUC-KR", "UTF-8",$R_PHONE))
						->setCellValue("N$k", iconv("EUC-KR", "UTF-8",$R_HPHONE))
						->setCellValue("O$k", iconv("EUC-KR", "UTF-8",$MEMO));
		

					$sheetIndex->getStyle("A$k:O$k")->getFont()->setSize(9);
					$sheetIndex->getStyle("A$k:O$k")->getAlignment()->setWrapText(true);
					$sheetIndex->getStyle("A$k:O$k")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					//$sheetIndex->getRowDimension($k)->setRowHeight(30);
					
					$l += 1;
					$k += 1;
				}// end of for(size of arr_rs_individual)
			}//end of if(arr_rs_individual>0)
			else {

				$objPHPExcel->setActiveSheetIndex(0)->getRowDimension($k)->setRowHeight(-1); 
					
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A$k", iconv("EUC-KR", "UTF-8",($j+1)))
					->setCellValue("B$k", iconv("EUC-KR", "UTF-8",$rs_extra_dcode))
					->setCellValue("C$k", iconv("EUC-KR", "UTF-8",$GOODS_NAME))
					->setCellValue("D$k", iconv("EUC-KR", "UTF-8",$GOODS_SUB_NAME))
					->setCellValue("E$k", iconv("EUC-KR", "UTF-8",number_format($REQ_QTY)))
					->setCellValue("F$k", iconv("EUC-KR", "UTF-8",number_format($BUY_PRICE)))
					->setCellValue("G$k", iconv("EUC-KR", "UTF-8",number_format($BUY_TOTAL_PRICE)));

				$objPHPExcel->getActiveSheet()->getStyle("C".($k))->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getStyle("C".($k))->getFont()->setSize(9);;

				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("H$k", iconv("EUC-KR", "UTF-8",""))
					->setCellValue("I$k", iconv("EUC-KR", "UTF-8",$MEMO2))
					->setCellValue("J$k", iconv("EUC-KR", "UTF-8",$RECEIVER_NM))
					->setCellValue("K$k", iconv("EUC-KR", "UTF-8",""))
					->setCellValue("L$k", iconv("EUC-KR", "UTF-8",$RECEIVER_ADDR))
					->setCellValue("M$k", iconv("EUC-KR", "UTF-8",$RECEIVER_PHONE))
					->setCellValue("N$k", iconv("EUC-KR", "UTF-8",$RECEIVER_HPHONE))
					->setCellValue("O$k", iconv("EUC-KR", "UTF-8",$MEMO1));
	
				$sheetIndex->getStyle("A$k:O$k")->getFont()->setSize(9);
				$sheetIndex->getStyle("A$k:O$k")->getAlignment()->setWrapText(true);
				$sheetIndex->getStyle("A$k:O$k")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				//$sheetIndex->getRowDimension($k)->setRowHeight(30);
				
				$l += 1;
				$k += 1;

			}//end of for(size of arr_rs_goods);
		}//end of if(arr_rs_goods>0)
	}
	while($k <= 23) { 
		$sheetIndex->setCellValue("A$k", iconv("EUC-KR", "UTF-8",$l));
		$sheetIndex->getRowDimension($k)->setRowHeight(30);

		$l += 1;
		$k += 1;

	}

	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A".$k, iconv("EUC-KR", "UTF-8","합계"))->mergeCells("A".$k.":D".$k);

	$sheetIndex->getStyle("A".$k)->getFont()->setBold(true);
	$sheetIndex->getStyle("A".$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->getActiveSheet()->getStyle("A10:O".$k)->applyFromArray($BStyle);

	$margin = 0.45;
	$sheetIndex->getColumnDimension("A")->setWidth(4 + $margin);
	$sheetIndex->getColumnDimension("B")->setWidth(10 + $margin);
	$sheetIndex->getColumnDimension("C")->setWidth(38 + $margin);
	$sheetIndex->getColumnDimension("D")->setWidth(12.43 + $margin);
	$sheetIndex->getColumnDimension("E")->setWidth(7 + $margin);
	$sheetIndex->getColumnDimension("F")->setWidth(10 + $margin);
	$sheetIndex->getColumnDimension("G")->setWidth(10 + $margin);
	$sheetIndex->getColumnDimension("H")->setWidth(10 + $margin);
	$sheetIndex->getColumnDimension("I")->setWidth(11 + $margin);
	$sheetIndex->getColumnDimension("J")->setWidth(11.14 + $margin);
	$sheetIndex->getColumnDimension("K")->setWidth(9.43 + $margin);
	$sheetIndex->getColumnDimension("L")->setWidth(28.14 + $margin);
	$sheetIndex->getColumnDimension("M")->setWidth(15.71 + $margin);
	$sheetIndex->getColumnDimension("N")->setWidth(14.43 + $margin);
	$sheetIndex->getColumnDimension("O")->setWidth(15 + $margin);
	
	// Rename sheet
	$objPHPExcel->setActiveSheetIndex(0)->setTitle(iconv("EUC-KR", "UTF-8","엑셀업로드"));

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	//$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(100);

	// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
	$filename = "발주서-".date("Ymd");

	// Redirect output to a client’s web browser (Excel5)
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment;filename=".$filename.".xls");
	header("Cache-Control: max-age=0");
 
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
	$objWriter->save("php://output");

	mysql_close($conn);
	exit;


?>
				
