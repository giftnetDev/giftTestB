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

	//스타일
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
			'name' => '돋움'
		),'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('rgb' => 'd9d9d9')
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

	
    //엑셀 내용 출력 시작
	$k = 1;
    
    //1행
    $sheetIndex->setCellValue("A".$k,iconv("EUC-KR", "UTF-8","INS엔터프라이즈"));
	$sheetIndex->getStyle("A$k")->getFont()->setSize(14)->setBold(true);
	$sheetIndex->getStyle("A$k")
		->getAlignment()
		->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    
    //2행
	$k += 1;
    $sheetIndex->setCellValue("A".$k,iconv("EUC-KR", "UTF-8","요청일자"))->getStyle("A$k")->getFont()->setSize(10)->setBold(true);
    $sheetIndex->setCellValue("B".$k,iconv("EUC-KR", "UTF-8","수취인"))->getStyle("B$k")->getFont()->setSize(10)->setBold(true);
    $sheetIndex->setCellValue("C".$k,iconv("EUC-KR", "UTF-8","주소"))->getStyle("C$k")->getFont()->setSize(10)->setBold(true);
    $sheetIndex->setCellValue("D".$k,iconv("EUC-KR", "UTF-8","연락처1"))->getStyle("D$k")->getFont()->setSize(10)->setBold(true);
    $sheetIndex->setCellValue("E".$k,iconv("EUC-KR", "UTF-8","연락처2"))->getStyle("E$k")->getFont()->setSize(10)->setBold(true);
    $sheetIndex->setCellValue("F".$k,iconv("EUC-KR", "UTF-8","수량"))->getStyle("F$k")->getFont()->setSize(10)->setBold(true);
    $sheetIndex->setCellValue("G".$k,iconv("EUC-KR", "UTF-8","배송메모"))->getStyle("G$k")->getFont()->setSize(10)->setBold(true);
    $sheetIndex->setCellValue("H".$k,iconv("EUC-KR", "UTF-8","상품명+모델명"))->getStyle("H$k")->getFont()->setSize(10)->setBold(true);
	
	$sheetIndex->getStyle("A$k:H$k")->applyFromArray($headerStyle);
	$sheetIndex->getStyle("A$k:H$k")
		->getAlignment()
		->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$k+=1;
	//내용	
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
			$arr_rs_individual=listDeliveryIndividual($conn, $ORDER_GOODS_NO, "DESC");


			$individualCnt=sizeof($arr_rs_individual);
			if($individualCnt>0 && $TO_HERE != "Y"){
				for($v=0;$v<$individualCnt;$v++){
					$R_ADDR1			=trim(SetStringFromDB($arr_rs_individual[$v]["R_ADDR1"]));
					$R_MEM_NM			=trim(SetStringFromDB($arr_rs_individual[$v]["R_MEM_NM"]));
					$R_PHONE			=trim(SetStringFromDB($arr_rs_individual[$v]["R_PHONE"]));
					$R_HPHONE			=trim(SetStringFromDB($arr_rs_individual[$v]["R_HPHONE"]));
					$SUB_QTY			=trim(SetStringFromDB($arr_rs_individual[$v]["SUB_QTY"]));
					$MEMO				=trim(SetStringFromDB($arr_rs_individual[$v]["MEMO"]));
					$GOODS_DELIVERY_NAME=trim(SetStringFromDB($arr_rs_individual[$v]["GOODS_DELIVERY_NAME"]));
					$USE_TF				=trim(SetStringFromDB($arr_rs_individual[$v]["USE_TF"]));
				
					if($USE_TF !="Y") continue;

					$sheetIndex->setCellValue("A".$k,iconv("EUC-KR","UTF-8",$REQ_DATE));
					$sheetIndex->setCellValue("B".$k,iconv("EUC-KR","UTF-8",$R_MEM_NM));
					$sheetIndex->setCellValue("C".$k,iconv("EUC-KR","UTF-8",$R_ADDR1));
					$sheetIndex->setCellValue("D".$k,iconv("EUC-KR","UTF-8",$R_PHONE));
					$sheetIndex->setCellValue("E".$k,iconv("EUC-KR","UTF-8",$R_HPHONE));
					$sheetIndex->setCellValue("F".$k,iconv("EUC-KR","UTF-8",$SUB_QTY));
					$sheetIndex->setCellValue("G".$k,iconv("EUC-KR","UTF-8",$MEMO));
					$sheetIndex->setCellValue("H".$k,iconv("EUC-KR","UTF-8",$GOODS_NAME." ".$GOODS_SUB_NAME));

					$REQ_QTY-=$SUB_QTY;
					
					//스타일 적용
					$sheetIndex->getStyle("A$k:H$k")->applyFromArray($defaultStyle);
					
					//자동 개행
					$sheetIndex->getStyle("A$k:H$k")
					->getAlignment()
					->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sheetIndex->getStyle("A$k:H$k")
					->getAlignment()
					->setWrapText(true);

					$k++;
				
				}
			}
			if($REQ_QTY>0){
//부가세 별도라 다시 계산
				$BUY_TOTAL_PRICE = $BUY_PRICE * $REQ_QTY;
				$TOTAL_BUY_TOTAL_PRICE_NOTAX += $BUY_TOTAL_PRICE;

				$sheetIndex->setCellValue("A".$k,iconv("EUC-KR", "UTF-8",$REQ_DATE));
				$sheetIndex->setCellValue("B".$k,iconv("EUC-KR", "UTF-8",$RECEIVER_NM));
				$sheetIndex->setCellValue("C".$k,iconv("EUC-KR", "UTF-8",$RECEIVER_ADDR));
				$sheetIndex->setCellValue("D".$k,iconv("EUC-KR", "UTF-8",$RECEIVER_PHONE));
				$sheetIndex->setCellValue("E".$k,iconv("EUC-KR", "UTF-8",$RECEIVER_HPHONE));
				$sheetIndex->setCellValue("F".$k,iconv("EUC-KR", "UTF-8",$REQ_QTY));
				$sheetIndex->setCellValue("G".$k,iconv("EUC-KR", "UTF-8",$MEMO1));
				$sheetIndex->setCellValue("H".$k,iconv("EUC-KR", "UTF-8",$GOODS_NAME ." ".$GOODS_SUB_NAME));

			}
				//스타일 적용
				$sheetIndex->getStyle("A$k:H$k")->applyFromArray($defaultStyle);
			
				//자동 개행
				$sheetIndex->getStyle("A$k:H$k")
				->getAlignment()
				->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$sheetIndex->getStyle("A$k:H$k")
				->getAlignment()
				->setWrapText(true);
				$k++;
			
		
		}
	}

	//내용과 유의사항 사이의 행 높이 조절
	$k += 1;
	$sheetIndex->getRowDimension($k)->setRowHeight(12);
	
	//유의사항
	$k += 1;
	$sheetIndex->setCellValue("B".$k,iconv("EUC-KR", "UTF-8","* 발주주실때 택배비 착불 or 선불인지 기재해주세요~ 기재 안해주시면 선불로 출고됩니다."))->getStyle("A$k")->getFont()->setSize(10)->setBold(true);

	//폭
	$margin = 2.2;
	$sheetIndex->getColumnDimension("A")->setWidth(8.78 + $margin);
	$sheetIndex->getColumnDimension("B")->setWidth(15.56 + $margin);
	$sheetIndex->getColumnDimension("C")->setWidth(32.56 + $margin);
	$sheetIndex->getColumnDimension("D")->setWidth(11.44 + $margin);
	$sheetIndex->getColumnDimension("E")->setWidth(12 + $margin);
	$sheetIndex->getColumnDimension("F")->setWidth(3.67 + $margin);
	$sheetIndex->getColumnDimension("G")->setWidth(18.78 + $margin);
	$sheetIndex->getColumnDimension("H")->setWidth(30.78 + $margin);
	
	//높이
	$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(24);
	$objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(36.75);

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