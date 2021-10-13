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

	$REQ_DATE				= $arr_rs[0]["REQ_DATE"]; //��������
	$SENDER_CP				= $arr_rs[0]["SENDER_CP"]; //�߽�ó
	$CEO_NM					= $arr_rs[0]["CEO_NM"]; //�߽�ó ��ǥ�ڸ�
	$SENDER_ADDR			= $arr_rs[0]["SENDER_ADDR"]; //�߽�ó �ּ�
	$SENDER_PHONE			= $arr_rs[0]["SENDER_PHONE"]; //�߽�ó ����ó
	$BUY_CP_NM				= $arr_rs[0]["BUY_CP_NM"]; //����ó
	$BUY_MANAGER_NM			= $arr_rs[0]["BUY_MANAGER_NM"]; //����ó ����ڸ�
	$BUY_CP_PHONE			= $arr_rs[0]["BUY_CP_PHONE"]; //����ó ����ó
	$DELIVERY_TYPE			= $arr_rs[0]["DELIVERY_TYPE"];
	$MEMO					= $arr_rs[0]["MEMO"]; //�޸�
	$TOTAL_REQ_QTY			= $arr_rs[0]["TOTAL_REQ_QTY"]; //���� ����
	$TOTAL_BUY_TOTAL_PRICE	= $arr_rs[0]["TOTAL_BUY_TOTAL_PRICE"]; //�� ���� ����

	$arr_rs_goods = listGoodsRequestGoods($conn, $req_no, 'N');

	require_once "../../_PHPExcel/Classes/PHPExcel.php";

	$objPHPExcel = new PHPExcel();

	$sheetIndex = $objPHPExcel->setActiveSheetIndex(0);

	$defaultStyle = array(
	  'font'  => array(
        'size'  => 10,
		'name'  => '���� ���'
	  )
	);

	$headerStyle = array(
		'font'  => array(
			'size'  => 9,
			'name'  => '����',
			'bold' => 'true'
		),'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('rgb' => 'ccff99')
		),'borders' => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		)
	);
	
	$contentsStyle = array(
		'font'  => array(
			'size'  => 9,
			'name'  => '����'
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
        'name'  => '���� ���'
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
	
	//1��
	$k = 1;
	$sheetIndex->setCellValue("A".$k,iconv("EUC-KR", "UTF-8","��������"));
	$sheetIndex->setCellValue("B".$k,iconv("EUC-KR", "UTF-8","����ó"));
	$sheetIndex->setCellValue("C".$k,iconv("EUC-KR", "UTF-8","�����ڸ�"));
	$sheetIndex->setCellValue("D".$k,iconv("EUC-KR", "UTF-8","������ȭ��ȣ"));
	$sheetIndex->setCellValue("E".$k,iconv("EUC-KR", "UTF-8","������ȭ��ȣ"));
	$sheetIndex->setCellValue("F".$k,iconv("EUC-KR", "UTF-8","�����ȣ"));
	$sheetIndex->setCellValue("G".$k,iconv("EUC-KR", "UTF-8","�ּ�"));
	$sheetIndex->setCellValue("H".$k,iconv("EUC-KR", "UTF-8","���ǻ��� (�ɼ� - ����� ������)"));
	$sheetIndex->setCellValue("I".$k,iconv("EUC-KR", "UTF-8","�ֹ���ȣ"));
	$sheetIndex->setCellValue("J".$k,iconv("EUC-KR", "UTF-8","��ǰ��"));
	$sheetIndex->setCellValue("K".$k,iconv("EUC-KR", "UTF-8","����"));
	$sheetIndex->setCellValue("L".$k,iconv("EUC-KR", "UTF-8","��ۻ�"));
	$sheetIndex->setCellValue("M".$k,iconv("EUC-KR", "UTF-8","����ڹ�ȣ"));
	$sheetIndex->setCellValue("N".$k,iconv("EUC-KR", "UTF-8","�ù��"));

	//��� ����
	$sheetIndex->getStyle("A$k:N$k")
	->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
	->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	//��Ÿ�� ����
	$sheetIndex->getStyle("A$k:N$k")->applyFromArray($headerStyle);

	if (sizeof($arr_rs_goods) > 0) {
		for ($j = 0 ; $j < sizeof($arr_rs_goods); $j++) {
			$GOODS_NO					= trim($arr_rs_goods[$j]["GOODS_NO"]); //��ǰ��ȣ
			$GOODS_NAME					= trim($arr_rs_goods[$j]["GOODS_NAME"]); //��ǰ��
			$GOODS_SUB_NAME				= trim($arr_rs_goods[$j]["GOODS_SUB_NAME"]); //��ǰ ����ǰ��
			$REQ_QTY					= trim($arr_rs_goods[$j]["REQ_QTY"]); //���� ����
			$BUY_PRICE					= trim($arr_rs_goods[$j]["BUY_PRICE"]); //����
			$BUY_TOTAL_PRICE			= trim($arr_rs_goods[$j]["BUY_TOTAL_PRICE"]); //�Ѱ���
			$RECEIVER_NM				= trim($arr_rs_goods[$j]["RECEIVER_NM"]); //�����ڸ�
			$RECEIVER_ADDR				= trim($arr_rs_goods[$j]["RECEIVER_ADDR"]); //������ �ּ�
			$RECEIVER_PHONE				= trim($arr_rs_goods[$j]["RECEIVER_PHONE"]); //������ ����ó
			$RECEIVER_HPHONE			= trim($arr_rs_goods[$j]["RECEIVER_HPHONE"]); //������ �ڵ���
			$MEMO1						= trim($arr_rs_goods[$j]["MEMO1"]); //�۾�
			$MEMO2						= trim($arr_rs_goods[$j]["MEMO2"]); //�ֹ���
			$MEMO3						= trim($arr_rs_goods[$j]["MEMO3"]); //�߼���
			$TO_HERE					= trim($arr_rs_goods[$j]["TO_HERE"]); //���ۿ���
			$ORDER_GOODS_NO				= trim($arr_rs_goods[$j]["ORDER_GOODS_NO"]);
			$arr_rs_goods_extra 		= selectGoodsExtra($conn, $GOODS_NO, 'GOODS_CODE_LG');

			//���� ���� 15
			$k += 1;
			$sheetIndex->setCellValue("A".$k,iconv("EUC-KR", "UTF-8","$REQ_DATE"));
			$sheetIndex->setCellValue("B".$k,iconv("EUC-KR", "UTF-8","$SENDER_CP"));
			$sheetIndex->setCellValue("C".$k,iconv("EUC-KR", "UTF-8","$RECEIVER_NM"));
			$sheetIndex->setCellValue("D".$k,iconv("EUC-KR", "UTF-8","$RECEIVER_PHONE"));
			$sheetIndex->setCellValue("E".$k,iconv("EUC-KR", "UTF-8","$RECEIVER_HPHONE"));
			$sheetIndex->setCellValue("F".$k,iconv("EUC-KR", "UTF-8",""));//�����ȣ
			$sheetIndex->setCellValue("G".$k,iconv("EUC-KR", "UTF-8","$RECEIVER_ADDR"));
			$sheetIndex->setCellValue("H".$k,iconv("EUC-KR", "UTF-8","������ ��� : $SENDER_CP".(($MEMO1!="")?"\n$MEMO1":"")));//���ǻ���
			$sheetIndex->setCellValue("I".$k,iconv("EUC-KR", "UTF-8",""));//�ֹ���ȣ
			$sheetIndex->setCellValue("J".$k,iconv("EUC-KR", "UTF-8","$GOODS_NAME"));
			$sheetIndex->setCellValue("K".$k,iconv("EUC-KR", "UTF-8","$REQ_QTY"));
			$sheetIndex->setCellValue("L".$k,iconv("EUC-KR", "UTF-8","$BUY_CP_NM"));
			$sheetIndex->setCellValue("M".$k,iconv("EUC-KR", "UTF-8",""));
			$sheetIndex->setCellValue("N".$k,iconv("EUC-KR", "UTF-8",""));

			//��� ����
			$sheetIndex->getStyle("A$k:N$k")
			->getAlignment()
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
			->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			//�ּҿ� ��ǰ��, ���ǻ����� ��������
			$sheetIndex->getStyle("G$k")//�ּ�
			->getAlignment()
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

			$sheetIndex->getStyle("J$k")//��ǰ��
			->getAlignment()
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			
			$sheetIndex->getStyle("H$k")//���ǻ���
			->getAlignment()
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

			//��Ÿ�� ����
			$sheetIndex->getStyle("A$k:N$k")->applyFromArray($contentsStyle);
			
			//�� ���� : �޸� ������ ����Ǵϱ� ���� ������� �� ���̸� ���̰�, �ƴϸ� �⺻ �� ���� ����
			if($MEMO1 == "")
				$objPHPExcel->getActiveSheet()->getRowDimension($k)->setRowHeight(15);
			else{
				$objPHPExcel->getActiveSheet()->getRowDimension($k)->setRowHeight(-1);
				$sheetIndex->getStyle("H$k")->getAlignment()->setWrapText(true);
			}
		}
	}

	//��
	$margin = 2;
	$sheetIndex->getColumnDimension("A")->setWidth(18.43 + $margin);
	$sheetIndex->getColumnDimension("B")->setWidth(11.14 + $margin);
	$sheetIndex->getColumnDimension("C")->setWidth(11.14 + $margin);
	$sheetIndex->getColumnDimension("D")->setWidth(12.43 + $margin);
	$sheetIndex->getColumnDimension("E")->setWidth(12.43 + $margin);
	$sheetIndex->getColumnDimension("F")->setWidth(6.33 + $margin);
	$sheetIndex->getColumnDimension("G")->setWidth(45 + $margin);
	$sheetIndex->getColumnDimension("H")->setWidth(23.44 + $margin);
	$sheetIndex->getColumnDimension("I")->setWidth(6.33 + $margin);
	$sheetIndex->getColumnDimension("J")->setWidth(49.86 + $margin);
	$sheetIndex->getColumnDimension("K")->setWidth(5 + $margin);
	$sheetIndex->getColumnDimension("L")->setWidth(9.43 + $margin);
	$sheetIndex->getColumnDimension("M")->setWidth(7.78 + $margin);
	$sheetIndex->getColumnDimension("N")->setWidth(4.89 + $margin);

	//����
	$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(14.25);

	// Rename sheet
	$objPHPExcel->setActiveSheetIndex(0)->setTitle(iconv("EUC-KR", "UTF-8","���ֳ���"));

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	// ������ ���������� utf-8�� ��� �ѱ����� �̸��� �����Ƿ� euc-kr�� ��ȯ���ش�.
	$filename = "���ּ�-".date("Ymd");

	// Redirect output to a client��s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment;filename=".$filename.".xls");
	header('Cache-Control: max-age=0');
 
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

	mysql_close($conn);
	exit;
?>
				
