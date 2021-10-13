<?session_start();
#====================================================================
# DB Include, DB Connection
#====================================================================
require "../../_classes/com/db/DBUtil.php";
$conn = db_connection("w");

#====================================================================
# common_header Check Session
#====================================================================
require "../../_common/common_header.php"; 

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

if (sizeof($arr_rs_goods) > 0) {
	$MEMO2								= trim($arr_rs_goods[0]["MEMO2"]);
	
	if($MEMO2 == "")
		$MEMO2 							= $SENDER_CP;
	
	$ORDER_GOODS_NO				= trim($arr_rs_goods[0]["ORDER_GOODS_NO"]);
	$arr_order 						= selectOrderByOrderGoodsNo($conn, $ORDER_GOODS_NO);
	if (sizeof($arr_order) > 0) {
		$R_MEM_NM						= trim($arr_order[0]["R_MEM_NM"]);
		$R_PHONE						= trim($arr_order[0]["R_PHONE"]);
	} else {
		$R_MEM_NM						= trim($arr_rs_goods[0]["RECEIVER_NM"]);
		$R_PHONE						= trim($arr_rs_goods[0]["RECEIVER_PHONE"]);;
	}
}

require_once "../../_PHPExcel/Classes/PHPExcel.php";
$objPHPExcel = new PHPExcel();
$sheetIndex = $objPHPExcel->setActiveSheetIndex(0);

//style
$allborder = array(
	'borders' => array(
		'allborders' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN
		)
	)
);

$borderbottom = array(
	'borders' => array(
		'bottom' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN
		)
	)
);

$deliveryinfo = array(
	'font'  => array(
		'size'  => 9,
		'name'  => '나눔 고딕',
		'color' => array('rgb' => '0070C0'),
		'bold' => true
	)
);

$noticetext = array(
	'font'  => array(
		'size'  => 9,
		'name'  => '나눔 고딕',
		'color' => array('rgb' => 'FF0000'),
		'bold' => true
	)
);

$fillbackground = array(
	'fill' => array(
		'type'  => PHPExcel_Style_Fill::FILL_SOLID,
		'color' => array('rgb' => 'EEECE1')
	)
);

$correctionWidth = 0.1958;
$correctionHeight = 0.03528;


//1~2행
$sheetIndex->setCellValue("A1",iconv("EUC-KR", "UTF-8","LG생활건강 화장품 특판 발주서"));
//셀병합
$sheetIndex->mergeCells("A1:AD2");
//폰트 : 볼드, 사이즈 14
$sheetIndex->getStyle("A1")->getFont()->setSize(14)->setBold(true);
//가로 세로 가운데 정렬
$sheetIndex->getStyle("A1")->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
	->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

//3행
$sheetIndex->setCellValue("A3",iconv("EUC-KR", "UTF-8","E-mail : jmp0116@lghnh.com\n팩스: 0505-106-7395"));
//셀병합
$sheetIndex ->mergeCells("A3:AD3");
//폰트 : 사이즈10
$sheetIndex->getStyle("A1")->getFont()->setSize(14);
//세로 가운데정렬, 가로 오른쪽 정렬
$sheetIndex->getStyle("A3")->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
	->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
//아래쪽 얇은 보더 추가해야함

//4행 공백

//5행
$sheetIndex->setCellValue("A5",iconv("EUC-KR", "UTF-8","아래와 같이 발주 합니다."));

//6행 공백

//7행
$sheetIndex->setCellValue("A7",iconv("EUC-KR", "UTF-8","발주처"));
$sheetIndex->setCellValue("F7",iconv("EUC-KR", "UTF-8",$MEMO2));
$sheetIndex->setCellValue("O7",iconv("EUC-KR", "UTF-8","중간납품처"));
$sheetIndex->setCellValue("T7",iconv("EUC-KR", "UTF-8","No"));
$sheetIndex ->mergeCells("A7:E7");
$sheetIndex ->mergeCells("F7:N7");
$sheetIndex ->mergeCells("O7:S7");
$sheetIndex ->mergeCells("T7:AD7");

//8행
$sheetIndex->setCellValue("A8",iconv("EUC-KR", "UTF-8","배송처"));
$sheetIndex->setCellValue("F8",iconv("EUC-KR", "UTF-8",$SENDER_CP));
$sheetIndex->setCellValue("O8",iconv("EUC-KR", "UTF-8","최종납품처"));
$sheetIndex->setCellValue("T8",iconv("EUC-KR", "UTF-8",$MEMO2));
$sheetIndex ->mergeCells("A8:E8");
$sheetIndex ->mergeCells("F8:N8");
$sheetIndex ->mergeCells("O8:S8");
$sheetIndex ->mergeCells("T8:AD8");

//9행
$sheetIndex->setCellValue("A9",iconv("EUC-KR", "UTF-8","배송요청일"));
$sheetIndex->setCellValue("F9",iconv("EUC-KR", "UTF-8",""));//기존 양식에서도 배송 요청일은 공백
$sheetIndex->setCellValue("O9",iconv("EUC-KR", "UTF-8","납품용도"));
$sheetIndex->setCellValue("T9",iconv("EUC-KR", "UTF-8","은행고객사은품"));
$sheetIndex ->mergeCells("A9:E9");
$sheetIndex ->mergeCells("F9:N9");
$sheetIndex ->mergeCells("O9:S9");
$sheetIndex ->mergeCells("T9:AD9");

//7~9행 가로 세로 가운데정렬
$sheetIndex->getStyle("A7:AD9")->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
	->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

//10행
$sheetIndex->setCellValue("A10",iconv("EUC-KR", "UTF-8","* 중간 납품처가 있을경우, 납품증빙자료를 수령할 수 있는 곳만 사전 확인 후 발주요청해 주십시오."));

//11행
$sheetIndex->setCellValue("A11",iconv("EUC-KR", "UTF-8","* 납품 후 최종납품처에 대한 납품증빙자료를 제출해야 하며, 미 제출시 차기 주문이 불가할 수 있습니다."));

//12행 공백

//13행
$sheetIndex->setCellValue("A13",iconv("EUC-KR", "UTF-8","제품명"));
$sheetIndex->setCellValue("K13",iconv("EUC-KR", "UTF-8","제품 코드"));
$sheetIndex->setCellValue("Q13",iconv("EUC-KR", "UTF-8","단가(원)"));
$sheetIndex->setCellValue("U13",iconv("EUC-KR", "UTF-8","수량(개)"));
$sheetIndex->setCellValue("Y13",iconv("EUC-KR", "UTF-8","합계 금액(세별도)"));
$sheetIndex ->mergeCells("A13:J13");
$sheetIndex ->mergeCells("K13:P13");
$sheetIndex ->mergeCells("Q13:T13");
$sheetIndex ->mergeCells("U13:X13");
$sheetIndex ->mergeCells("Y13:AD13");
$sheetIndex->getStyle("A13:AD13")->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
	->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

//14행
$k = 14;
$TOTAL_BUY_TOTAL_PRICE_NOTAX = 0;
if (sizeof($arr_rs_goods) > 0) {
	for ($j = 0 ; $j < sizeof($arr_rs_goods); $j++) {
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

		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue("A$k", iconv("EUC-KR", "UTF-8",$GOODS_NAME." ".$GOODS_SUB_NAME))->mergeCells("A$k:J$k")
			->setCellValue("K$k", iconv("EUC-KR", "UTF-8",$rs_extra_dcode))->mergeCells("K$k:P$k")
			->setCellValue("Q$k", iconv("EUC-KR", "UTF-8",number_format($BUY_PRICE)))->mergeCells("Q$k:T$k")
			->setCellValue("U$k", iconv("EUC-KR", "UTF-8",number_format($REQ_QTY)))->mergeCells("U$k:X$k")
			->setCellValue("Y$k", iconv("EUC-KR", "UTF-8",number_format($BUY_TOTAL_PRICE)))->mergeCells("Y$k:AD$k");

		// $objPHPExcel->setActiveSheetIndex(0)
		// 	->setCellValue("AE$k", iconv("EUC-KR", "UTF-8",$MEMO2));

		//가로 세로 가운데 정렬
		$sheetIndex->getStyle("A$k:J$k")->getAlignment()
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
			->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$sheetIndex->getStyle("K$k:P$k")->getAlignment()
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
			->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$sheetIndex->getStyle("Q$k:AD$k")->getAlignment()
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
			->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		//폰트
		$sheetIndex->getStyle("A$k:AD$k")->getFont()->setSize(10);

		//행 높이
		$sheetIndex->getRowDimension($k)->setRowHeight(0.71 / $correctionHeight);

		//테두리
		$sheetIndex->getStyle("A$k:AD$k")->applyFromArray($allborder);
		$k += 1;
	}
}

//상품이 14개 이상이면 합계행을 상품 다음줄에 출력하고, 
//상품이 14개 미만이면 27행까지 공백을 출력하고 28행부터 합계행을 표시함.
if($k>27){
	//상품이 14개 이상
	//발주 총액 출력
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$k", iconv("EUC-KR", "UTF-8","발주 총액"));
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("Y$k", iconv("EUC-KR", "UTF-8","5,392,250 원"));
	
	//셀병함
		$objPHPExcel->setActiveSheetIndex(0)
		->mergeCells("A$k:J$k")
		->mergeCells("K$k:P$k")
		->mergeCells("Q$k:T$k")
		->mergeCells("U$k:X$k")
		->mergeCells("Y$k:AD$k");
	
	//가로 세로 가운데 정렬
	$sheetIndex->getStyle("A$k:AD$k")->getAlignment()
		->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
		->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	//테두리
	$sheetIndex->getStyle("A$k:AD$k")->applyFromArray($allborder);
	$k++;
} else {
	//상품이 14개 미만
	//공백 채우기
	for(;$k<28;$k++){
		//셀병함
		$objPHPExcel->setActiveSheetIndex(0)
			->mergeCells("A$k:J$k")
			->mergeCells("K$k:P$k")
			->mergeCells("Q$k:T$k")
			->mergeCells("U$k:X$k")
			->mergeCells("Y$k:AD$k");
		//가로 세로 가운데 정렬
		$sheetIndex->getStyle("A$k:AD$k")->getAlignment()
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
			->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		//폰트 : 사이즈 9
		$sheetIndex->getStyle("A$k:AD$k")->getFont()->setSize(10);
		//테두리
		$sheetIndex->getStyle("A$k:AD$k")->applyFromArray($allborder);
	}

	//발주 총액 출력
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$k", iconv("EUC-KR", "UTF-8","발주 총액"));
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("Y$k", iconv("EUC-KR", "UTF-8",number_format($TOTAL_BUY_TOTAL_PRICE_NOTAX)." 원"));

	//셀병함
	$objPHPExcel->setActiveSheetIndex(0)
		->mergeCells("A$k:J$k")
		->mergeCells("K$k:P$k")
		->mergeCells("Q$k:T$k")
		->mergeCells("U$k:X$k")
		->mergeCells("Y$k:AD$k");
	
	//가로 세로 가운데 정렬
	$sheetIndex->getStyle("A$k:AD$k")->getAlignment()
		->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
		->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
	//테두리
	$sheetIndex->getStyle("A$k:AD$k")->applyFromArray($allborder);
	$k++;
}

//안내문
$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$k", iconv("EUC-KR", "UTF-8","* 상기 수량에 미달하여 최종납품처에 납품될 경우, 해당 미달 물량에 대하여는 사전에 당사와 협의해 주시기 바람"));
$sheetIndex->getStyle("A$k")->applyFromArray($noticetext);

//부가세 포함가
$objPHPExcel->setActiveSheetIndex(0)->setCellValue("Y$k",iconv("EUC-KR","UTF-8",number_format($TOTAL_BUY_TOTAL_PRICE)." 원 (부가세포함)"));
$objPHPExcel->setActiveSheetIndex(0)->mergeCells("Y$k:AD$k");
$objPHPExcel->setActiveSheetIndex(0)->getStyle("Y$k")->getFont()->setSize(10);
$k++;

//공백
$k++;

//발주일자, 발주업체명

//구 발주일자 설정 사용하지 않음(기본으로 요청일의 익일로..익일이 주말일 경우 주말 아닐때까지 +1 day)
// $REQ_DATE = date('Y-m-d H:i:s', strtotime($REQ_DATE . ' +1 day'));
// while(isWeekend($REQ_DATE)) {
// 	$REQ_DATE = date('Y-m-d H:i:s', strtotime($REQ_DATE . ' +1 day'));
// }

$REQ_DATE = date('Y-m-d H:i:s', strtotime($REQ_DATE));

$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$k", iconv("EUC-KR", "UTF-8","발 주 일 자 :"));
$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D$k", iconv("EUC-KR", "UTF-8",date("Y-m-d",strtotime($REQ_DATE))));
$objPHPExcel->setActiveSheetIndex(0)->setCellValue("K$k", iconv("EUC-KR", "UTF-8","발주 업체명 :"));
$objPHPExcel->setActiveSheetIndex(0)->setCellValue("O$k", iconv("EUC-KR", "UTF-8","㈜기프트넷"));
$k++;

//대표자, 날인
$objPHPExcel->setActiveSheetIndex(0)->setCellValue("K$k", iconv("EUC-KR", "UTF-8","대표자 :"));
$objPHPExcel->setActiveSheetIndex(0)->setCellValue("O$k", iconv("EUC-KR", "UTF-8","양진현"));
$objPHPExcel->setActiveSheetIndex(0)->setCellValue("AA$k", iconv("EUC-KR", "UTF-8","(  날    인  )"));
$k++;

//안내문
$objPHPExcel->setActiveSheetIndex(0)->setCellValue("AD$k", iconv("EUC-KR", "UTF-8","※ '날인'은 인감(사용인감 또는 법인인감) 사용."));
$sheetIndex->getStyle("AD$k")->applyFromArray($noticetext);
$sheetIndex->getStyle("AD$k")->getAlignment()
		->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$k++;

//스타일적용
$sheetIndex->getStyle("A7:AD9")->applyFromArray($allborder);
$sheetIndex->getStyle("F7:N9")->applyFromArray($deliveryinfo);
$sheetIndex->getStyle("T7:AD9")->applyFromArray($deliveryinfo);
$sheetIndex->getStyle("A3:AD3")->applyFromArray($borderbottom);
$sheetIndex->getStyle("A13:AD13")->applyFromArray($allborder);
$sheetIndex->getStyle("A13:AD13")->applyFromArray($fillbackground);
$sheetIndex->getStyle("A10:A11")->applyFromArray($noticetext);

//격자 표시 해제
$sheetIndex->setShowGridlines(false);

//행, 열 크기
$sheetIndex->getColumnDimension("A")->setWidth(0.65 / $correctionWidth);
$sheetIndex->getColumnDimension("B")->setWidth(1.85 / $correctionWidth);
$sheetIndex->getColumnDimension("C")->setWidth(0.65 / $correctionWidth);
$sheetIndex->getColumnDimension("D")->setWidth(0.65 / $correctionWidth);
$sheetIndex->getColumnDimension("E")->setWidth(0.65 / $correctionWidth);
$sheetIndex->getColumnDimension("F")->setWidth(0.65 / $correctionWidth);
$sheetIndex->getColumnDimension("G")->setWidth(0.65 / $correctionWidth);
$sheetIndex->getColumnDimension("H")->setWidth(0.65 / $correctionWidth);
$sheetIndex->getColumnDimension("I")->setWidth(0.65 / $correctionWidth);
$sheetIndex->getColumnDimension("J")->setWidth(0.65 / $correctionWidth);
$sheetIndex->getColumnDimension("K")->setWidth(0.65 / $correctionWidth);
$sheetIndex->getColumnDimension("L")->setWidth(0.65 / $correctionWidth);
$sheetIndex->getColumnDimension("M")->setWidth(0.65 / $correctionWidth);
$sheetIndex->getColumnDimension("N")->setWidth(0.65 / $correctionWidth);
$sheetIndex->getColumnDimension("O")->setWidth(0.65 / $correctionWidth);
$sheetIndex->getColumnDimension("P")->setWidth(0.65 / $correctionWidth);
$sheetIndex->getColumnDimension("Q")->setWidth(0.65 / $correctionWidth);
$sheetIndex->getColumnDimension("R")->setWidth(0.65 / $correctionWidth);
$sheetIndex->getColumnDimension("S")->setWidth(0.65 / $correctionWidth);
$sheetIndex->getColumnDimension("T")->setWidth(0.65 / $correctionWidth);
$sheetIndex->getColumnDimension("U")->setWidth(0.65 / $correctionWidth);
$sheetIndex->getColumnDimension("V")->setWidth(0.65 / $correctionWidth);
$sheetIndex->getColumnDimension("W")->setWidth(0.65 / $correctionWidth);
$sheetIndex->getColumnDimension("X")->setWidth(0.65 / $correctionWidth);
$sheetIndex->getColumnDimension("Y")->setWidth(0.65 / $correctionWidth);
$sheetIndex->getColumnDimension("Z")->setWidth(0.65 / $correctionWidth);
$sheetIndex->getColumnDimension("AA")->setWidth(0.65 / $correctionWidth);
$sheetIndex->getColumnDimension("AB")->setWidth(0.65 / $correctionWidth);
$sheetIndex->getColumnDimension("AC")->setWidth(0.65 / $correctionWidth);
$sheetIndex->getColumnDimension("AD")->setWidth(0.65 / $correctionWidth);

$sheetIndex->getRowDimension("1")->setRowHeight(0.58 / $correctionHeight);
$sheetIndex->getRowDimension("2")->setRowHeight(0.58 / $correctionHeight);
$sheetIndex->getRowDimension("3")->setRowHeight(1.01 / $correctionHeight);
$sheetIndex->getRowDimension("4")->setRowHeight(0.61 / $correctionHeight);
$sheetIndex->getRowDimension("5")->setRowHeight(0.49 / $correctionHeight);
$sheetIndex->getRowDimension("6")->setRowHeight(0.49 / $correctionHeight);
$sheetIndex->getRowDimension("7")->setRowHeight(0.61 / $correctionHeight);
$sheetIndex->getRowDimension("8")->setRowHeight(0.61 / $correctionHeight);
$sheetIndex->getRowDimension("9")->setRowHeight(0.61 / $correctionHeight);
$sheetIndex->getRowDimension("10")->setRowHeight(0.61 / $correctionHeight);
$sheetIndex->getRowDimension("11")->setRowHeight(0.61 / $correctionHeight);
$sheetIndex->getRowDimension("12")->setRowHeight(0.82 / $correctionHeight);


//인감 이미지 추가
//create new image object
$objDrawing_p = new PHPExcel_Worksheet_Drawing();
//get image path
$stamp_img_path = $_SERVER["DOCUMENT_ROOT"]."/upload_data/operating_image/giftnet_stamp.png";
$objDrawing_p->setPath($stamp_img_path);
//set image position
$objDrawing_p->setCoordinates("AA".($k-3));
//set image position(offset)
$objDrawing_p->setOffsetX(0);
$objDrawing_p->setOffsetY(0);
//set image size
$objDrawing_p->setWidthAndHeight(70,70);
$objDrawing_p->setResizeProportional(true);
//print image
$objDrawing_p->setWorksheet($objPHPExcel->setActiveSheetIndex(0));


// Rename sheet
$objPHPExcel->setActiveSheetIndex(0)->setTitle(iconv("EUC-KR", "UTF-8","LG발주서"));

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