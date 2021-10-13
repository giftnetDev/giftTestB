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
//$req_no = trim($req_no);

//echo base64url_decode($req_no);

#===============================================================
# Get Search list count
#===============================================================

$arr_rs = selectGoodsRequestByReqNo($conn, $req_no);

$REQ_DATE				= $arr_rs[0]["REQ_DATE"];  //������
$SENDER_CP				= $arr_rs[0]["SENDER_CP"]; //����ȸ��
$CEO_NM					= $arr_rs[0]["CEO_NM"];		//����Ʈ�� ����
$SENDER_ADDR			= $arr_rs[0]["SENDER_ADDR"];//����Ʈ�� �ּ�
$SENDER_PHONE			= $arr_rs[0]["SENDER_PHONE"];//����Ʈ�� ��
$BUY_CP_NM				= $arr_rs[0]["BUY_CP_NM"];		//�ֹ���ü
$BUY_MANAGER_NM			= $arr_rs[0]["BUY_MANAGER_NM"];	//�ֹ���ü �޴���
$BUY_CP_PHONE			= $arr_rs[0]["BUY_CP_PHONE"];	//�ֹ���ü ��
$DELIVERY_TYPE			= $arr_rs[0]["DELIVERY_TYPE"];	//
$MEMO					= $arr_rs[0]["MEMO"];
$TOTAL_REQ_QTY			= $arr_rs[0]["TOTAL_REQ_QTY"];	//�� �ֹ���
$TOTAL_BUY_TOTAL_PRICE	= $arr_rs[0]["TOTAL_BUY_TOTAL_PRICE"];//�� �ֹ�����

$arr_rs_goods = listGoodsRequestGoods($conn, $req_no, 'N');

require_once "../../_PHPExcel/Classes/PHPExcel.php";

$objPHPExcel = new PHPExcel();

$sheetIndex = $objPHPExcel->setActiveSheetIndex(0);


//������ ���η� ����
$sheetIndex->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
//�μ���� A4
$sheetIndex->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
//�����¿� ����
$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0);
$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0);

//set title

$sheetIndex->getColumnDimension('A')->setWidth(5);
$sheetIndex->getColumnDimension('B')->setWidth(11);
$sheetIndex->getColumnDimension('C')->setWidth(11);
$sheetIndex->getColumnDimension('D')->setWidth(12);
$sheetIndex->getColumnDimension('E')->setWidth(38);
$sheetIndex->getColumnDimension('F')->setWidth(39);
$sheetIndex->getColumnDimension('G')->setWidth(9);
$sheetIndex->getColumnDimension('H')->setWidth(38);
$sheetIndex->getColumnDimension('I')->setWidth(24);

//1��
$regDate=substr($REQ_DATE,0,10);
$title ="���Ƽ�� ���ּ� ���(".$regDate.")";
$sheetIndex->mergeCells("A1:I3");

$sheetIndex->setCellValue("A1", iconv("EUC-KR", "UTF-8", $title));
$sheetIndex->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheetIndex->getStyle("A1")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheetIndex->getStyle("A1")->getBorders()->getOutline()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
//5��
$sheetIndex->setCellValue("A5", iconv("EUC-KR", "UTF-8", "No."));
$sheetIndex->setCellValue("B5", iconv("EUC-KR", "UTF-8", "�̸�"));
$sheetIndex->setCellValue("C5", iconv("EUC-KR", "UTF-8", "��ȭ"));
$sheetIndex->setCellValue("D5", iconv("EUC-KR", "UTF-8", "��ȭ"));
$sheetIndex->setCellValue("E5", iconv("EUC-KR", "UTF-8", "�ּ�"));
$sheetIndex->setCellValue("F5", iconv("EUC-KR", "UTF-8", "ǰ��(�𵨸�)"));
$sheetIndex->setCellValue("G5", iconv("EUC-KR", "UTF-8", "����"));
$sheetIndex->setCellValue("H5", iconv("EUC-KR", "UTF-8", "��۸޼���"));
$sheetIndex->setCellValue("I5", iconv("EUC-KR", "UTF-8", "���"));

$noticeRow=0;
$arr_rs_goods_row=sizeof($arr_rs_goods);
if($arr_rs_goods_row<9){
	$noticeRow=17;
}
else{
	$noticeRow=$arr_rs_goods_row+9;
}
$sheetIndex->setCellValue("A$noticeRow",iconv("EUC-KR","UTF-8","* ��ۿ�û���� �ù輱�� or ������  �� ������ּ���."));
$sheetIndex->getStyle("A$noticeRow")->getFont()->setBold(true);
$noticeRow++;
$sheetIndex->setCellValue("A$noticeRow",iconv("EUC-KR","UTF-8","  -  ���޻����� ������ ���ҷ� ���˴ϴ�."));
$sheetIndex->getStyle("A$noticeRow")->getFont()->setBold(true);

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
		//$MEMO1 					= str_replace("���ָ޸� : ", "", $MEMO1);
		$ORDERNO					= date("Ymd", strtotime($REQ_DATE))."-����Ʈ��-".$req_no;
		$REQ_DATE 				= date("Y-m-d", strtotime($REQ_DATE));

		//$arr_rs_individual = listDeliveryIndividual($conn, $ORDER_GOODS_NO, "DESC");

		$x=$j+6;
		// $sheetIndex->setCellValue("")

		$sheetIndex->setCellValue("A$x",iconv("EUC-KR","UTF-8",$j+1));
		$sheetIndex->setCellValue("B$x",iconv("EUC-KR","UTF-8",$RECEIVER_NM));
		$sheetIndex->setCellValue("C$x",iconv("EUC-KR","UTF-8",$RECEIVER_PHONE));
		$sheetIndex->setCellValue("D$x",iconv("EUC-KR","UTF-8",$RECEIVER_HPHONE));
		$sheetIndex->setCellValue("E$x",iconv("EUC-KR","UTF-8",$RECEIVER_ADDR));
		$sheetIndex->setCellValue("F$x",iconv("EUC-KR","UTF-8",$GOODS_NAME));
		$sheetIndex->setCellValue("G$x",iconv("EUC-KR","UTF-8",$REQ_QTY));

		// $sheetIndex->setCellValue("H$x",iconv("EUC-KR","UTF-8",$));
		// $sheetIndex->setCellValue("I$x",iconv("EUC-KR","UTF-8",$j+1));


		// if($TO_HERE != 'Y'){
		// 	$sheetIndex->setCellValue("Q$x",iconv("EUC-KR","UTF-8",$MEMO2));
		// }
		// else{
		// 	$sheetIndex->setCellValue("Q$x",iconv("EUC-KR","UTF-8",$SENDER_CP));
		// }
	}
}



// Rename sheet
$objPHPExcel->setActiveSheetIndex(0)->setTitle(iconv("EUC-KR", "UTF-8", "���ּ�"));

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

//$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(100);

// ������ ���������� utf-8�� ��� �ѱ����� �̸��� �����Ƿ� euc-kr�� ��ȯ���ش�.
$filename = "���ּ�-" . date("Ymd");

// Redirect output to a client��s web browser (Excel5)

header('Content-Type: application/vnd.ms-excel');
header("Content-Disposition: attachment;filename=" . $filename . ".xls");
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

mysql_close($conn);
exit;
?>