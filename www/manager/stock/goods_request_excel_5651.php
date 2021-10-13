<? session_start(); ?>
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
$req_no = trim($req_no);

// echo base64url_decode($req_no);
// secho $req_no;

#===============================================================
# Get Search list count
#===============================================================
$x=2;
$page=1;
$arr_rs = selectGoodsRequestByReqNo($conn, $req_no);

$REQ_DATE				= $arr_rs[0]["REQ_DATE"];  //발주일
$SENDER_CP				= $arr_rs[0]["SENDER_CP"]; //발주회사
$CEO_NM					= $arr_rs[0]["CEO_NM"];		//기프트넷 오너
$SENDER_ADDR			= $arr_rs[0]["SENDER_ADDR"];//기프트넷 주소
$SENDER_PHONE			= $arr_rs[0]["SENDER_PHONE"];//기프트넷 폰
$BUY_CP_NM				= $arr_rs[0]["BUY_CP_NM"];		//주문업체
$BUY_MANAGER_NM			= $arr_rs[0]["BUY_MANAGER_NM"];	//주문업체 메니져
$BUY_CP_PHONE			= $arr_rs[0]["BUY_CP_PHONE"];	//주문업체 폰
$DELIVERY_TYPE			= $arr_rs[0]["DELIVERY_TYPE"];	//
$MEMO					= $arr_rs[0]["MEMO"];
$TOTAL_REQ_QTY			= $arr_rs[0]["TOTAL_REQ_QTY"];	//총 주문량
$TOTAL_BUY_TOTAL_PRICE	= $arr_rs[0]["TOTAL_BUY_TOTAL_PRICE"];//총 주문가격

$str=explode("/",$SENDER_PHONE);
$senderPhone = $str[0];

// echo "REQ_DATE is $REQ_DATE<br/>";
// echo "SENDER_CP is $SENDER_CP<br/>";
// echo "CEO_NM is $CEO_NM<br/>";
// echo "SENDER_ADDR is $SENDER_ADDR<br/>";
// echo "SENDER_PHONE is $SENDER_PHONE<br/>";
// echo "BUY_CP_NM is $BUY_CP_NM<br/>";
// echo "BUY_MANAGER_NM is $BUY_MANAGER_NM<br/>";
// echo "BUY_CP_PHONE is $BUY_CP_PHONE<br/>";
// echo "DELIVERY_TYPE is $DELIVERY_TYPE<br/>";
// echo "MEMO is $MEMO<br/>";
// echo "TOTAL_REQ_QTY is $TOTAL_REQ_QTY<br/>";
// echo "TOTAL_BUY_TOTAL_PRICE is $TOTAL_BUY_TOTAL_PRICE<br/>";

$arr_rs_goods = listGoodsRequestGoods($conn, $req_no, 'N');

require_once "../../_PHPExcel/Classes/PHPExcel.php";

$objPHPExcel = new PHPExcel();

$sheetIndex = $objPHPExcel->setActiveSheetIndex(0);

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

//페이지 가로로 돌림
$sheetIndex->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
//인쇄용지 A4
$sheetIndex->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
//상하좌우 여백
$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0);
$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0);

$sheetIndex->getColumnDimension("A")->setWidth(10);
$sheetIndex->getColumnDimension("B")->setWidth(10);
$sheetIndex->getColumnDimension("C")->setWidth(18);
$sheetIndex->getColumnDimension("D")->setWidth(18);
$sheetIndex->getColumnDimension("E")->setWidth(15);
$sheetIndex->getColumnDimension("F")->setWidth(10);
$sheetIndex->getColumnDimension("G")->setWidth(48);
$sheetIndex->getColumnDimension("H")->setWidth(17);
$sheetIndex->getColumnDimension("I")->setWidth(15);
$sheetIndex->getColumnDimension("J")->setWidth(28);

$sheetIndex->getColumnDimension("L")->setWidth(10);
$sheetIndex->getColumnDimension("M")->setWidth(10);
$sheetIndex->getColumnDimension("N")->setWidth(24);
$sheetIndex->getColumnDimension("O")->setWidth(10);
$sheetIndex->getColumnDimension("P")->setWidth(10);
$sheetIndex->getColumnDimension("Q")->setWidth(13);
$sheetIndex->getColumnDimension("R")->setWidth(47);
$sheetIndex->getColumnDimension("S")->setWidth(17);

for($i=1;$i<=108;$i++) $sheetIndex->getRowDimension($i)->setRowHeight(18.75);

$sheetIndex->getStyle("A1:S108")->applyFromArray(
	array(
		'borders' => array(
			'allborders' =>array(
				'style' =>PHPExcel_Style_Border::BORDER_THIN,
				'color' =>array('rgb' => '000000')
			)
		)
	)
);

$sheetIndex->getStyle("A1:S1")->getFont()->setBold(true);
$sheetIndex->getStyle("A1:S108")->getFont()->setSize(10);
$sheetIndex->getStyle("A1:S1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F2F2F2');
$sheetIndex->getStyle("C1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
$sheetIndex->getStyle("D1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
$sheetIndex->getStyle("G1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
$sheetIndex->getStyle("J1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
$sheetIndex->getStyle("Q1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
$sheetIndex->getStyle("R1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
$sheetIndex->getStyle("S1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
$sheetIndex->getStyle("A1:S1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

//set title
//1행
$sheetIndex->setCellValue("A1", iconv("EUC-KR", "UTF-8", "예약구분"));
$sheetIndex->setCellValue("B1", iconv("EUC-KR", "UTF-8", "집하예정일"));
$sheetIndex->setCellValue("C1", iconv("EUC-KR", "UTF-8", "받는분 성명"));
$sheetIndex->setCellValue("D1", iconv("EUC-KR", "UTF-8", "받는분 전화번호"));
$sheetIndex->setCellValue("E1", iconv("EUC-KR", "UTF-8", "받는분 기타연락처"));
$sheetIndex->setCellValue("F1", iconv("EUC-KR", "UTF-8", "받는분 우편번호"));
$sheetIndex->setCellValue("G1", iconv("EUC-KR", "UTF-8", "받는분 주소"));
$sheetIndex->setCellValue("H1", iconv("EUC-KR", "UTF-8", "운송장 번호"));
$sheetIndex->setCellValue("I1", iconv("EUC-KR", "UTF-8", "고객주문번호"));
$sheetIndex->setCellValue("J1", iconv("EUC-KR", "UTF-8", "품목명"));
$sheetIndex->setCellValue("K1", iconv("EUC-KR", "UTF-8", "박스수량"));
$sheetIndex->setCellValue("L1", iconv("EUC-KR", "UTF-8", "박스타입"));
$sheetIndex->setCellValue("M1", iconv("EUC-KR", "UTF-8", "기본운임"));
$sheetIndex->setCellValue("N1", iconv("EUC-KR", "UTF-8", "배송메세지1"));
$sheetIndex->setCellValue("O1", iconv("EUC-KR", "UTF-8", "배송메세지2"));
$sheetIndex->setCellValue("P1", iconv("EUC-KR", "UTF-8", "품목명"));
$sheetIndex->setCellValue("Q1", iconv("EUC-KR", "UTF-8", "보내는분"));
$sheetIndex->setCellValue("R1", iconv("EUC-KR", "UTF-8", "주소"));
$sheetIndex->setCellValue("S1", iconv("EUC-KR", "UTF-8", "전화번호"));

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
		$MEMO1 					= str_replace("발주메모 : ", "", $MEMO1);
		$ORDERNO					= date("Ymd", strtotime($REQ_DATE))."-기프트넷-".$req_no;
		$REQ_DATE 				= date("Y-m-d", strtotime($REQ_DATE));




		$arr_rs_individual = listDeliveryIndividual($conn, $ORDER_GOODS_NO, "DESC");
		$individualCnt=sizeof($arr_rs_individual);
		if($individualCnt>0){
			for($v=0;$v<$individualCnt;$v++){
				$R_ADDR1 			 = trim($arr_rs_individual[$v]["R_ADDR1"]);
				$R_MEM_NM			 = trim($arr_rs_individual[$v]["R_MEM_NM"]);
				$R_PHONE			 = trim($arr_rs_individual[$v]["R_PHONE"]); 
				$R_HPHONE			 = trim($arr_rs_individual[$v]["R_HPHONE"]); 
				$SUB_QTY			 = trim($arr_rs_individual[$v]["SUB_QTY"]);
				$MEMO				 = trim($arr_rs_individual[$v]["MEMO"]);
				$GOODS_DELIVERY_NAME = trim($arr_rs_individual[$v]["GOODS_DELIVERY_NAME"]);
				$USE_TF				 = trim($arr_rs_individual[$v]["USE_TF"]);
				if($USE_TF != "Y") continue;

				$REQ_QTY-=$SUB_QTY;
				$sheetIndex->setCellValue("C$x",iconv("EUC-KR","UTF-8",$R_MEM_NM));
				$sheetIndex->setCellValue("D$x",iconv("EUC-KR","UTF-8",$R_PHONE));
				$sheetIndex->setCellValue("G$x",iconv("EUC-KR","UTF-8",$R_ADDR1));
				$sheetIndex->setCellValue("J$x",iconv("EUC-KR","UTF-8",$GOODS_NAME." X ".$SUB_QTY));

				$sheetIndex->setCellValue("R$x",iconv("EUC-KR","UTF-8",$SENDER_ADDR));
				$sheetIndex->setCellValue("S$x",iconv("EUC-KR","UTF-8",$senderPhone));
				
				if($TO_HERE != 'Y'){//수령처
					$sheetIndex->setCellValue("Q$x",iconv("EUC-KR","UTF-8",$MEMO2));
				}
				else{//운영처
					
					$sheetIndex->setCellValue("R$x",iconv("EUC-KR","UTF-8",$SENDER_ADDR));
				}
				$x++;

			}
		}


		// $sheetIndex->setCellValue("")
		if($REQ_QTY<=0) continue;
		$sheetIndex->setCellValue("C$x",iconv("EUC-KR","UTF-8",$RECEIVER_NM));
		$sheetIndex->setCellValue("D$x",iconv("EUC-KR","UTF-8",$RECEIVER_PHONE));
		$sheetIndex->setCellValue("G$x",iconv("EUC-KR","UTF-8",$RECEIVER_ADDR));
		$sheetIndex->setCellValue("J$x",iconv("EUC-KR","UTF-8",$GOODS_NAME." X ".$REQ_QTY));
		
		$sheetIndex->setCellValue("R$x",iconv("EUC-KR","UTF-8",$SENDER_ADDR));
		$sheetIndex->setCellValue("S$x",iconv("EUC-KR","UTF-8",$senderPhone));

		if($TO_HERE != 'Y'){//수령처
			$sheetIndex->setCellValue("Q$x",iconv("EUC-KR","UTF-8",$MEMO2));
		}
		else{//운영처
			$sheetIndex->setCellValue("Q$x",iconv("EUC-KR","UTF-8",$SENDER_CP));
		}
		$x++;
	}
}



// Rename sheet
$objPHPExcel->setActiveSheetIndex(0)->setTitle(iconv("EUC-KR", "UTF-8", "발주서"));

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(100);

// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
$filename = "발주서-" . date("Ymd");

// // Redirect output to a client’s web browser (Excel5)

header('Content-Type: application/vnd.ms-excel');
header("Content-Disposition: attachment;filename=" . $filename . ".xls");
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

mysql_close($conn);
exit;
?>