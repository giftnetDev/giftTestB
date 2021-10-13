<? session_start(); ?>
<?php
	function listGoodsRequestGoodsPungsung($db, $req_no, $cancel_tf) {

		$query = "SELECT GRG.RESERVE_NO, 
							GRG.REQ_GOODS_NO, GRG.ORDER_GOODS_NO, GRG.GOODS_NO, GRG.GOODS_CODE, GRG.GOODS_NAME, GRG.GOODS_SUB_NAME, GRG.BUY_PRICE,	GRG.REQ_QTY, GRG.BUY_TOTAL_PRICE, 
							GRG.RECEIVE_QTY, GRG.RECEIVE_DATE, GRG.REASON, OG.SENDER_NM, OG.SENDER_PHONE,
							GRG.TO_HERE, GRG.RECEIVER_NM, GRG.RECEIVER_ADDR, GRG.RECEIVER_PHONE, GRG.RECEIVER_HPHONE, GRG.MEMO1, GRG.MEMO2, GRG.MEMO3, 
							GRG.CHANGED_TF, GRG.UP_ADM, GRG.UP_DATE, GRG.CANCEL_TF, GRG.CANCEL_DATE, GRG.CANCEL_ADM, GRG.CONFIRM_TF, GRG.CONFIRM_DATE, GRG.REG_DATE
							, DATE_FORMAT(CONCAT(SUBSTRING(GRG.REG_DATE,1,10), ' 13:59:59'), '%Y-%m-%d %H:%i:%s') AS DEFAULT_DATE
							, CASE WHEN GRG.REG_DATE > DATE_FORMAT(CONCAT(SUBSTRING(GRG.REG_DATE,1,10), ' 13:59:59'), '%Y-%m-%d %H:%i:%s') THEN DATE_ADD(SUBSTRING(GRG.REG_DATE,1,10),INTERVAL 1 DAY)  ELSE SUBSTRING(GRG.REG_DATE,1,10) END AS DELIVERY_DATE
							, (SELECT B.DELIVERY_CNT_IN_BOX FROM TBL_GOODS B WHERE B.GOODS_NO = GRG.GOODS_NO) AS DELIVERY_CNT_IN_BOX
					FROM TBL_GOODS_REQUEST_GOODS GRG 
					JOIN TBL_GOODS G ON GRG.GOODS_NO = G.GOODS_NO
					JOIN TBL_ORDER_GOODS OG ON GRG.ORDER_GOODS_NO= OG.ORDER_GOODS_NO
					WHERE GRG.REQ_NO = '$req_no' AND GRG.DEL_TF ='N'";

		if ($cancel_tf <> "") {
			$query .= " AND GRG.CANCEL_TF = '".$cancel_tf."' ";
		}

		// echo $query;
		// exit;

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

?>
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

$arr_rs_goods = listGoodsRequestGoodsPungsung($conn, $req_no, 'N');

require_once "../../_PHPExcel/Classes/PHPExcel.php";

$objPHPExcel = new PHPExcel();

$sheetIndex = $objPHPExcel->setActiveSheetIndex(0);

{	//FONT_ZONE
	$titleStyle1 = array(
		'font'  => array(
			'size'  => 24,
			'name'  => '나눔 고딕',
			'bold' => true
		), 'borders' => array(
			'bottom' => array(
				'style' => PHPExcel_Style_Border::BORDER_MEDIUM
			)
		), 'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
		)
	);

	$titleStyle2 = array(
		'font'  => array(
			'size'  => 11,
			'name'  => '나눔 고딕',
			'bold' => true
		), 'borders' => array(
			'bottom' => array(
				'style' => PHPExcel_Style_Border::BORDER_MEDIUM
			)
		), 'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
			'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
		)
	);

	$titleStyle3 = array(
		'font'  => array(
			'size'  => 11,
			'name'  => '나눔 고딕',
			'bold' => true
		), 'borders' => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			),'bottom' => array(
				'style' => PHPExcel_Style_Border::BORDER_DOUBLE
			)
		), 'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
		)
	);

	$titleStyle4 = array(
		'font'  => array(
			'size'  => 11,
			'name'  => '나눔 고딕',
			'bold' => true
		), 'borders' => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			),'bottom' => array(
				'style' => PHPExcel_Style_Border::BORDER_MEDIUM
			)
		), 'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
		)
	);

	$contentsStyle = array(
		'font'  => array(
			'size'  => 10,
			'name'  => '나눔 고딕'
		), 'borders' => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		), 'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
		)
	);
}

//페이지 가로로 돌림
$sheetIndex->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
//인쇄용지 A4
$sheetIndex->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
//상하좌우 여백
$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0);
$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0);

//set title
//1행
$sheetIndex->setCellValue("A1", iconv("EUC-KR", "UTF-8", "(주)기프트넷"));
$sheetIndex->setCellValue("D1", iconv("EUC-KR", "UTF-8", date("Y")." 年"));
$sheetIndex->setCellValue("F1", iconv("EUC-KR", "UTF-8", date("m")));
$sheetIndex->setCellValue("G1", iconv("EUC-KR", "UTF-8", "월"));
$sheetIndex->setCellValue("H1", iconv("EUC-KR", "UTF-8", date("d")));
$sheetIndex->setCellValue("I1", iconv("EUC-KR", "UTF-8", "일"));
$sheetIndex->setCellValue("J1", iconv("EUC-KR", "UTF-8", "발 주 서"));
$sheetIndex->mergeCells("A1:C1");
$sheetIndex->mergeCells("D1:E1");
$sheetIndex->mergeCells("J1:P1");
$sheetIndex->mergeCells("F2:P2");

$sheetIndex->setCellValue("F2",iconv("EUC-KR","UTF-8","특이사항 : ".$MEMO));

//2행
$sheetIndex->setCellValue(
	"A2",
	iconv(
		"EUC-KR",
		"UTF-8",
		"# 3열에 기재된 내용은 주문시 참고사항입니다. 필수입력값은 꼭 입력해주셔야 정확한 발주처리 진행이 가능합니다.
# 5열부터 발주정보 입력해주시는대로 출고진행 됩니다. (전산처리 되오니 5열부터 꼭 넣어주셔야 합니다.)
# 셀 숨기기 / 시트추가 / 누적발주서 작성 - 절대금지    (금지사항 위반으로 인한 중복발주 및 누락건은 책임지지 않습니다."
	)
);
$sheetIndex->mergeCells("A2:E2");

//3행
$sheetIndex->setCellValue("A3", iconv("EUC-KR", "UTF-8", "(필수입력)"));
$sheetIndex->setCellValue(
	"B3",
	iconv(
		"EUC-KR",
		"UTF-8",
		"(선택입력)
1) 출고일지정-배송메모란 기재
2) 출력부착건-발주시 파일첨부
3) 인사장/명함동봉건 - 전화협의"
	)
);
$sheetIndex->setCellValue(
	"C3",
	iconv(
		"EUC-KR",
		"UTF-8",
		"(필수입력)
제안받은 상품명으로 기재"
	)
);
$sheetIndex->setCellValue(
	"D3",
	iconv(
		"EUC-KR",
		"UTF-8",
		"(필수입력)
주문수량"
	)
);
$sheetIndex->setCellValue(
	"E3",
	iconv(
		"EUC-KR",
		"UTF-8",
		"(필수입력)
견적받은
금액 입력"
	)
);
$sheetIndex->setCellValue("F3", iconv("EUC-KR", "UTF-8", "(자동계산)"));
$sheetIndex->setCellValue(
	"G3",
	iconv(
		"EUC-KR",
		"UTF-8",
		"(필수입력)
보내는분 미기재시
기본값(수취인명동일)로 설정됩니다."
	)
);
$sheetIndex->setCellValue(
	"J3",
	iconv(
		"EUC-KR",
		"UTF-8",
		"(필수입력)
주소 오기재 및 전화번호 오기재로 인한 배송사고의 경우 재출고나 반품사유가 되지 않습니다. 정확하게 입력바랍니다."
	)
);
$sheetIndex->setCellValue("O3", iconv("EUC-KR", "UTF-8", "(선택입력)"));
$sheetIndex->setCellValue("P3", iconv("EUC-KR", "UTF-8", "(선택입력)"));
$sheetIndex->mergeCells("G3:I3");
$sheetIndex->mergeCells("J3:N3");

//4행
$sheetIndex->setCellValue("A4", iconv("EUC-KR", "UTF-8", "주문일자"));
$sheetIndex->setCellValue("B4", iconv("EUC-KR", "UTF-8", "인사장/명함동봉/예약출고"));
$sheetIndex->setCellValue("C4", iconv("EUC-KR", "UTF-8", "상품명"));
$sheetIndex->setCellValue("D4", iconv("EUC-KR", "UTF-8", "수량"));
$sheetIndex->setCellValue("E4", iconv("EUC-KR", "UTF-8", "상품단가"));
$sheetIndex->setCellValue("F4", iconv("EUC-KR", "UTF-8", "정산합계"));
$sheetIndex->setCellValue("G4", iconv("EUC-KR", "UTF-8", "보내는분성함"));
$sheetIndex->setCellValue("H4", iconv("EUC-KR", "UTF-8", "보내는분전화번호"));
$sheetIndex->setCellValue("I4", iconv("EUC-KR", "UTF-8", "보내는분휴대폰번호"));
$sheetIndex->setCellValue("J4", iconv("EUC-KR", "UTF-8", "받는분성함"));
$sheetIndex->setCellValue("K4", iconv("EUC-KR", "UTF-8", "받는분전화번호"));
$sheetIndex->setCellValue("L4", iconv("EUC-KR", "UTF-8", "받는분휴대폰번호"));
$sheetIndex->setCellValue("M4", iconv("EUC-KR", "UTF-8", "우편번호"));
$sheetIndex->setCellValue("N4", iconv("EUC-KR", "UTF-8", "주소"));
$sheetIndex->setCellValue("O4", iconv("EUC-KR", "UTF-8", "배송메모"));
$sheetIndex->setCellValue("P4", iconv("EUC-KR", "UTF-8", "주문번호"));

$k = 5;
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
		$SENDER_NM			= trim($arr_rs_goods[$j]["SENDER_NM"]);
		$SENDER_PHONE		= trim($arr_rs_goods[$j]["SENDER_PHONE"]);
		//$MEMO1 					= str_replace("발주메모 : ", "", $MEMO1);
		$ORDERNO					= date("Ymd", strtotime($REQ_DATE))."-기프트넷-".$req_no;
		$REQ_DATE 				= date("Y-m-d", strtotime($REQ_DATE));

		$arr_rs_individual = listDeliveryIndividual($conn, $ORDER_GOODS_NO, "DESC");

		if($BUY_PRICE <> "") {
			$BUY_PRICE = number_format($BUY_PRICE);
		}

		if(sizeof($arr_rs_individual) > 0) {
			for($o = 0; $o < sizeof($arr_rs_individual); $o ++) {
				$R_ZIPCODE						= trim($arr_rs_individual[$o]["R_ZIPCODE"]); 
				$R_ADDR1 			 				= trim($arr_rs_individual[$o]["R_ADDR1"]);
				$R_MEM_NM			 				= trim($arr_rs_individual[$o]["R_MEM_NM"]);
				$R_PHONE			 				= trim($arr_rs_individual[$o]["R_PHONE"]); 
				$R_HPHONE			 				= trim($arr_rs_individual[$o]["R_HPHONE"]); 
				$SUB_QTY			 				= trim($arr_rs_individual[$o]["SUB_QTY"]);
				$SUB_MEMO			 				= trim($arr_rs_individual[$o]["MEMO"]);
				$GOODS_DELIVERY_NAME 	= trim($arr_rs_individual[$o]["GOODS_DELIVERY_NAME"]);
				$USE_TF				 				= trim($arr_rs_individual[$o]["USE_TF"]);

				if($USE_TF != "Y") continue;
				if($R_PHONE<>"" && $R_HPHONE==""){
					$R_HPHONE=$R_PHONE;
				}
				else if($R_PHONE=="" && $R_HPHONE<>""){
					$R_PHONE=$R_HPHONE;
				}
				if(strpos($MEMO1,"착불")>0){
					$SUB_MEMO.="-착불-";
				}
				
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A$k", iconv("EUC-KR", "UTF-8",$REQ_DATE))
					->setCellValue("B$k", iconv("EUC-KR", "UTF-8","선택없음"))
					->setCellValue("C$k", iconv("EUC-KR", "UTF-8",$GOODS_NAME))
					->setCellValue("D$k", iconv("EUC-KR", "UTF-8",$SUB_QTY))
					->setCellValue("E$k", iconv("EUC-KR", "UTF-8",$BUY_PRICE))
					->setCellValue("F$k", iconv("EUC-KR", "UTF-8",number_format((int)str_replace(",","",$SUB_QTY) * (int)str_replace(",","",$BUY_PRICE))))
					->setCellValue("G$k", iconv("EUC-KR", "UTF-8",$SENDER_NM)) 	//MEMO3		//2021_09_10 처리
					->setCellValue("H$k", iconv("EUC-KR", "UTF-8",""))			//R_PHONE	//2021_09_10 처리
					->setCellValue("I$k", iconv("EUC-KR", "UTF-8",$R_HPHONE))
					->setCellValue("J$k", iconv("EUC-KR", "UTF-8",$R_MEM_NM))
					->setCellValue("K$k", iconv("EUC-KR", "UTF-8",$R_PHONE))
					->setCellValue("L$k", iconv("EUC-KR", "UTF-8",$R_HPHONE))
					->setCellValue("M$k", iconv("EUC-KR", "UTF-8",$R_ZIPCODE))
					->setCellValue("N$k", iconv("EUC-KR", "UTF-8",$R_ADDR1))
					->setCellValue("O$k", iconv("EUC-KR", "UTF-8",$SUB_MEMO))
					->setCellValue("P$k", iconv("EUC-KR", "UTF-8",$ORDERNO));

				$sheetIndex->getRowDimension($k)->setRowHeight(-1);
				$sheetIndex->getStyle("A$k:P$k")->applyFromArray($contentsStyle);
				$sheetIndex->getStyle("A$k:P$k")->getAlignment()->setWrapText(true);

				$k++;
			}
		}//end of if(individual>0)
		else {
			if($REQ_QTY <> "") $REQ_QTY = number_format($REQ_QTY);

			if($RECEIVER_PHONE<>"" && $RECEIVER_HPHONE==""){
				$RECEIVER_HPHONE=$RECEIVER_PHONE;
			}
			else if($RECEIVER_PHONE=="" && $RECEIVER_HPHONE<>""){
				$RECEIVER_PHONE=$RECEIVER_HPHONE;
			}
			

			$ORDER_MEMO		  	= getOrderMemo($conn, $ORDER_GOODS_NO);
			$RECEIVER_ZIPCODE = getOrderReceiverZipcode($conn, $ORDER_GOODS_NO);

			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A$k", iconv("EUC-KR", "UTF-8",$REQ_DATE))
				->setCellValue("B$k", iconv("EUC-KR", "UTF-8","선택없음"))
				->setCellValue("C$k", iconv("EUC-KR", "UTF-8",$GOODS_NAME))
				->setCellValue("D$k", iconv("EUC-KR", "UTF-8",$REQ_QTY))
				->setCellValue("E$k", iconv("EUC-KR", "UTF-8",$BUY_PRICE))
				->setCellValue("F$k", iconv("EUC-KR", "UTF-8",number_format((int)str_replace(",","",$REQ_QTY) * (int)str_replace(",","",$BUY_PRICE))))
				->setCellValue("G$k", iconv("EUC-KR", "UTF-8",$SENDER_NM))//$MEMO3	//2021_09_10 처리
				->setCellValue("H$k", iconv("EUC-KR", "UTF-8",""))//RECEIVER_PHONE	//2021_09_10 처리
				->setCellValue("I$k", iconv("EUC-KR", "UTF-8",$RECEIVER_HPHONE))
				->setCellValue("J$k", iconv("EUC-KR", "UTF-8",$RECEIVER_NM))
				->setCellValue("K$k", iconv("EUC-KR", "UTF-8",$RECEIVER_HPHONE))
				->setCellValue("L$k", iconv("EUC-KR", "UTF-8",$RECEIVER_HPHONE))
				->setCellValueExplicit("M$k", $RECEIVER_ZIPCODE, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValue("N$k", iconv("EUC-KR", "UTF-8",$RECEIVER_ADDR))
				->setCellValue("O$k", iconv("EUC-KR", "UTF-8",$ORDER_MEMO))
				->setCellValue("P$k", iconv("EUC-KR", "UTF-8",$ORDERNO));
				
			$sheetIndex->getRowDimension($k)->setRowHeight(-1);
			$sheetIndex->getStyle("A$k:P$k")->applyFromArray($contentsStyle);
			$sheetIndex->getStyle("A$k:P$k")->getAlignment()->setWrapText(true);

			$k++;
		}
	}
}

// set style
// set font and cell style
$sheetIndex->getStyle("A1:P1")->applyFromArray($titleStyle1);
$sheetIndex->getStyle("A2:P2")->applyFromArray($titleStyle2);
$sheetIndex->getStyle("A3:P3")->applyFromArray($titleStyle3);
$sheetIndex->getStyle("A4:P4")->applyFromArray($titleStyle4);
$sheetIndex->getStyle("A2:P3")->getAlignment()->setWrapText(true);

// $sheetIndex->getStyle("B3:D4")->getAlignment()->setWrapText(true);

// set column width
$correctionWidth = 0.1958;
$sheetIndex->getColumnDimension("A")->setWidth(0.5 + 3.29 / $correctionWidth);
$sheetIndex->getColumnDimension("B")->setWidth(0.5 + 6.39 / $correctionWidth);
$sheetIndex->getColumnDimension("C")->setWidth(0.5 + 10.59 / $correctionWidth);
$sheetIndex->getColumnDimension("D")->setWidth(0.5 + 2.03 / $correctionWidth);
$sheetIndex->getColumnDimension("E")->setWidth(0.5 + 2.36 / $correctionWidth);
$sheetIndex->getColumnDimension("F")->setWidth(0.5 + 2.36 / $correctionWidth);
$sheetIndex->getColumnDimension("G")->setWidth(0.5 + 2.66 / $correctionWidth);
$sheetIndex->getColumnDimension("H")->setWidth(0.5 + 2.74 / $correctionWidth);
$sheetIndex->getColumnDimension("I")->setWidth(0.5 + 2.66 / $correctionWidth);
$sheetIndex->getColumnDimension("J")->setWidth(0.5 + 2.25 / $correctionWidth);
$sheetIndex->getColumnDimension("K")->setWidth(0.5 + 2.85 / $correctionWidth);
$sheetIndex->getColumnDimension("L")->setWidth(0.5 + 2.69 / $correctionWidth);
$sheetIndex->getColumnDimension("M")->setWidth(0.5 + 2.41 / $correctionWidth);
$sheetIndex->getColumnDimension("N")->setWidth(0.5 + 11.74 / $correctionWidth);
$sheetIndex->getColumnDimension("O")->setWidth(0.5 + 6.78 / $correctionWidth);
$sheetIndex->getColumnDimension("P")->setWidth(0.5 + 4.47 / $correctionWidth);

// set row height
$correctionHeight = 0.03528;
$sheetIndex->getRowDimension("1")->setRowHeight(1.38 / $correctionHeight);
$sheetIndex->getRowDimension("2")->setRowHeight(2.06 / $correctionHeight);
$sheetIndex->getRowDimension("3")->setRowHeight(1.88 / $correctionHeight);
$sheetIndex->getRowDimension("4")->setRowHeight(1.06 / $correctionHeight);

// Rename sheet
$objPHPExcel->setActiveSheetIndex(0)->setTitle(iconv("EUC-KR", "UTF-8", "발주서"));

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

//$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(100);

// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
$filename = "발주서-" . date("Ymd");

// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header("Content-Disposition: attachment;filename=" . $filename . ".xls");
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

mysql_close($conn);
exit;
?>