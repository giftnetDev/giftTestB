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
			'color' => array('rgb' => 'd9d9d9')
		)
	);

	$listTitleStyle_right = array(
		'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('rgb' => 'fde9d9')
		)
	);

	$k = 1;

	$sheetIndex ->setCellValue("A$k", iconv("EUC-KR", "UTF-8","상품명(옵션)"))
				->setCellValue("B$k", iconv("EUC-KR", "UTF-8","주문수량"))
				->setCellValue("C$k", iconv("EUC-KR", "UTF-8","수령인명"))   
				->setCellValue("D$k", iconv("EUC-KR", "UTF-8","수령인연락처1"))   
				->setCellValue("E$k", iconv("EUC-KR", "UTF-8","수령인연락처2"))   
				->setCellValue("F$k", iconv("EUC-KR", "UTF-8","우편번호"))
				->setCellValue("G$k", iconv("EUC-KR", "UTF-8","주소"))
				->setCellValue("H$k", iconv("EUC-KR", "UTF-8","배송메세지"))
				->setCellValue("I$k", iconv("EUC-KR", "UTF-8","배송준비 처리일"))
				->setCellValue("J$k", iconv("EUC-KR", "UTF-8","관리자메모"))
				->setCellValue("K$k", iconv("EUC-KR", "UTF-8","주문자명"))
				->setCellValue("L$k", iconv("EUC-KR", "UTF-8","주문자연락처"))
				->setCellValue("M$k", iconv("EUC-KR", "UTF-8","택배사"))
				->setCellValue("N$k", iconv("EUC-KR", "UTF-8","배송번호"));


	//$sheetIndex->getRowDimension($k)->setRowHeight(30);

	$sheetIndex->getStyle("A$k:N$k")->getFont()->setSize(10)->setBold(true);
	$sheetIndex->getStyle("A$k:N$k")->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheetIndex->getStyle("A$k:N$k")->applyFromArray($listTitleStyle_left);
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

			$DEFAULT_DATE				= trim($arr_rs_goods[$j]["DEFAULT_DATE"]);
			$DELIVERY_DATE				= trim($arr_rs_goods[$j]["DELIVERY_DATE"]);

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
						->setCellValue("A$k", iconv("EUC-KR", "UTF-8",$GOODS_NAME." ".$GOODS_SUB_NAME))
						->setCellValue("B$k", iconv("EUC-KR", "UTF-8",number_format($SUB_QTY)))
						->setCellValue("C$k", iconv("EUC-KR", "UTF-8",$R_MEM_NM))
						->setCellValue("D$k", iconv("EUC-KR", "UTF-8",$R_PHONE))
						->setCellValue("E$k", iconv("EUC-KR", "UTF-8",$R_HPHONE))
						->setCellValue("G$k", iconv("EUC-KR", "UTF-8",$R_ADDR1))
						->setCellValue("H$k", iconv("EUC-KR", "UTF-8",$MEMO))				//배송메세지
						->setCellValue("I$k", iconv("EUC-KR", "UTF-8",$DELIVERY_DATE))		//배송준비 처리일
						->setCellValue("J$k", iconv("EUC-KR", "UTF-8",$MEMO1))				//관리자메모
						->setCellValue("K$k", iconv("EUC-KR", "UTF-8",$MEMO2))				//주문자명
						->setCellValue("L$k", iconv("EUC-KR", "UTF-8","031-527-6812"));		//주문자연락처

					$sheetIndex->getStyle("A$k:N$k")->getFont()->setSize(9);
					$sheetIndex->getStyle("A$k:N$k")->getAlignment()->setWrapText(true);
					$sheetIndex->getStyle("A$k:N$k")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					//$sheetIndex->getRowDimension($k)->setRowHeight(30);
					
					$l += 1;
					$k += 1;
				}// end of for(size of arr_rs_individual)
			}//end of if(arr_rs_individual>0)
			else {

				$objPHPExcel->setActiveSheetIndex(0)->getRowDimension($k)->setRowHeight(-1); 
					
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A$k", iconv("EUC-KR", "UTF-8",$GOODS_NAME." ".$GOODS_SUB_NAME))
					->setCellValue("B$k", iconv("EUC-KR", "UTF-8",number_format($REQ_QTY)))
					->setCellValue("C$k", iconv("EUC-KR", "UTF-8",$RECEIVER_NM))
					->setCellValue("D$k", iconv("EUC-KR", "UTF-8",$RECEIVER_PHONE))
					->setCellValue("E$k", iconv("EUC-KR", "UTF-8",$RECEIVER_HPHONE))
					->setCellValue("G$k", iconv("EUC-KR", "UTF-8",$RECEIVER_ADDR))
					->setCellValue("H$k", iconv("EUC-KR", "UTF-8",$MEMO))					//배송메세지
					->setCellValue("I$k", iconv("EUC-KR", "UTF-8",$DELIVERY_DATE))			//배송준비 처리일
					->setCellValue("J$k", iconv("EUC-KR", "UTF-8",$MEMO1))					//관리자메모
					->setCellValue("K$k", iconv("EUC-KR", "UTF-8",$MEMO2))					//주문자명
					->setCellValue("L$k", iconv("EUC-KR", "UTF-8","031-527-6812"));			//주문자연락처
	
				$sheetIndex->getStyle("A$k:N$k")->getFont()->setSize(9);
				$sheetIndex->getStyle("A$k:N$k")->getAlignment()->setWrapText(true);
				$sheetIndex->getStyle("A$k:N$k")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				//$sheetIndex->getRowDimension($k)->setRowHeight(30);
				
				$l += 1;
				$k += 1;
				
			}//end of for(size of arr_rs_goods);
		}//end of if(arr_rs_goods>0)
	}
	
	/*while($k <= 22) { 
		$sheetIndex->setCellValue("A$k", iconv("EUC-KR", "UTF-8",$l));
		$sheetIndex->getRowDimension($k)->setRowHeight(30);

		$l += 1;
		$k += 1;

	}*/


	$k += -1;
	$sheetIndex->getRowDimension($k)->setRowHeight(30);

	$objPHPExcel->getActiveSheet()->getStyle("A1:N".$k)->applyFromArray($BStyle);

	$margin = 0.45;
	$sheetIndex->getColumnDimension("A")->setWidth(35 + $margin);
	$sheetIndex->getColumnDimension("B")->setWidth(10 + $margin);
	$sheetIndex->getColumnDimension("C")->setWidth(15 + $margin);
	$sheetIndex->getColumnDimension("D")->setWidth(15 + $margin);
	$sheetIndex->getColumnDimension("E")->setWidth(15 + $margin);
	$sheetIndex->getColumnDimension("F")->setWidth(10 + $margin);
	$sheetIndex->getColumnDimension("G")->setWidth(35 + $margin);
	$sheetIndex->getColumnDimension("H")->setWidth(35 + $margin);
	$sheetIndex->getColumnDimension("I")->setWidth(15 + $margin);
	$sheetIndex->getColumnDimension("J")->setWidth(30 + $margin);
	$sheetIndex->getColumnDimension("K")->setWidth(35 + $margin);
	$sheetIndex->getColumnDimension("L")->setWidth(30 + $margin);
	$sheetIndex->getColumnDimension("M")->setWidth(13 + $margin);
	$sheetIndex->getColumnDimension("N")->setWidth(13 + $margin);
	
	// Rename sheet
	$objPHPExcel->setActiveSheetIndex(0)->setTitle(iconv("EUC-KR", "UTF-8","리앤쿡 발주서"));

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	//$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(100);

	// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
	$filename = "리앤쿡 발주서-".date("Ymd");

	// Redirect output to a client’s web browser (Excel5)
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment;filename=".$filename.".xls");
	header("Cache-Control: max-age=0");
 
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
	$objWriter->save("php://output");

	mysql_close($conn);
	exit;


?>
				
