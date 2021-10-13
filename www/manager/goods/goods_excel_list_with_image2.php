<?session_start();?>
<?
ini_set('memory_limit',-1);
// ini_set('display_errors', 1);
// error_reporting(E_ALL ^ E_NOTICE);
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

	function getCpNm($db, $cp_no) {

		$query =    "SELECT CP_NM
							FROM TBL_COMPANY
							WHERE CP_NO = '$cp_no' ";
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}

		return $record[0]['CP_NM'];
	}

	function singleGoods($db){
		//��ǰ
		$query1 = "set @rownum = 1772;";
		$query2 = "SELECT @rownum:= @rownum - 1 as rn, GOODS_NO, GOODS_CATE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, CATE_01, CATE_02, CATE_03, CATE_04, PRICE, BUY_PRICE, SALE_PRICE, EXTRA_PRICE, STOCK_CNT, TAX_TF, IMG_URL, FILE_NM_100, FILE_RNM_100, FILE_PATH_100, FILE_SIZE_100, FILE_EXT_100, FILE_NM_150, FILE_RNM_150, FILE_PATH_150, FILE_SIZE_150, FILE_EXT_150, CONTENTS, READ_CNT, DISP_SEQ, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE, (SELECT CP_NM FROM TBL_COMPANY WHERE TBL_COMPANY.CP_NO = TBL_GOODS.CATE_03 ) AS CP_NAME, DELIVERY_CNT_IN_BOX, STOCK_TF, MSTOCK_CNT, TSTOCK_CNT, FSTOCK_CNT, BSTOCK_CNT, STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, SALE_SUSU, LABOR_PRICE, OTHER_PRICE, NEXT_SALE_PRICE, WRAP_WIDTH, WRAP_LENGTH, WRAP_MEMO, RESTOCK_DATE, MEMO FROM TBL_GOODS WHERE 1 = 1 AND (GOODS_CATE like '17%' OR GOODS_NO IN (SELECT GOODS_NO FROM TBL_GOODS_CATEGORY WHERE GOODS_CATE LIKE '17%' )) AND REG_DATE >= '2010-07-24' AND REG_DATE <= '2019-10-18 23:59:59' AND SALE_PRICE >= '10000' AND SALE_PRICE <= '30000' AND CATE_04 = '�Ǹ���' AND DEL_TF = 'N' ORDER BY REG_DATE DESC, GOODS_NO ASC limit 0, 9999";
		
		mysql_query($query1,$db);
		$result = mysql_query($query2,$db);
		$record = array();
		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}
	
	function setGoods($db){
		//��Ʈǰ
		$query1 = "set @rownum = 158;";
		$query2 = "SELECT @rownum:= @rownum - 1 as rn, GOODS_NO, GOODS_CATE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, CATE_01, CATE_02, CATE_03, CATE_04, PRICE, BUY_PRICE, SALE_PRICE, EXTRA_PRICE, STOCK_CNT, TAX_TF, IMG_URL, FILE_NM_100, FILE_RNM_100, FILE_PATH_100, FILE_SIZE_100, FILE_EXT_100, FILE_NM_150, FILE_RNM_150, FILE_PATH_150, FILE_SIZE_150, FILE_EXT_150, CONTENTS, READ_CNT, DISP_SEQ, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE, (SELECT CP_NM FROM TBL_COMPANY WHERE TBL_COMPANY.CP_NO = TBL_GOODS.CATE_03 ) AS CP_NAME, DELIVERY_CNT_IN_BOX, STOCK_TF, MSTOCK_CNT, TSTOCK_CNT, FSTOCK_CNT, BSTOCK_CNT, STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, SALE_SUSU, LABOR_PRICE, OTHER_PRICE, NEXT_SALE_PRICE, WRAP_WIDTH, WRAP_LENGTH, WRAP_MEMO, RESTOCK_DATE, MEMO FROM TBL_GOODS WHERE 1 = 1 AND (GOODS_CATE like '14%' OR GOODS_NO IN (SELECT GOODS_NO FROM TBL_GOODS_CATEGORY WHERE GOODS_CATE LIKE '14%' )) AND REG_DATE >= '2010-07-24' AND REG_DATE <= '2019-10-18 23:59:59' AND SALE_PRICE >= '10000' AND SALE_PRICE <= '30000' AND CATE_04 = '�Ǹ���' AND DEL_TF = 'N' ORDER BY REG_DATE DESC, GOODS_NO ASC limit 0, 300";
		
		mysql_query($query1,$db);
		$result = mysql_query($query2,$db);
		$record = array();
		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	// $arr_rs = singleGoods($conn);
	$arr_rs = setGoods($conn);

	require_once "../../_PHPExcel/Classes/PHPExcel.php";

	$objPHPExcel = new PHPExcel();

	$sheetIndex = $objPHPExcel->setActiveSheetIndex(0);

	//���� ���� ��� ����
	$k = 1;
    
	//1��(�� ����)
	$sheetIndex->setCellValue("A".$k,iconv("EUC-KR", "UTF-8","��ǰ��ȣ"));
	$sheetIndex->setCellValue("B".$k,iconv("EUC-KR", "UTF-8","��ǰ�ڵ�"));
	$sheetIndex->setCellValue("C".$k,iconv("EUC-KR", "UTF-8","��ǰī�װ�"));
	$sheetIndex->setCellValue("D".$k,iconv("EUC-KR", "UTF-8","�̹���"));
	$sheetIndex->setCellValue("E".$k,iconv("EUC-KR", "UTF-8","��ǰ��"));
	$sheetIndex->setCellValue("F".$k,iconv("EUC-KR", "UTF-8","�𵨸�"));
	$sheetIndex->setCellValue("G".$k,iconv("EUC-KR", "UTF-8","�ڽ��Լ�"));
	// $sheetIndex->setCellValue("G".$k,iconv("EUC-KR", "UTF-8","����ǰ����"));
	$sheetIndex->setCellValue("H".$k,iconv("EUC-KR", "UTF-8","�ǸŻ���"));
	// $sheetIndex->setCellValue("H".$k,iconv("EUC-KR", "UTF-8","�ּ����"));
	$sheetIndex->setCellValue("I".$k,iconv("EUC-KR", "UTF-8","���԰�"));
	$sheetIndex->setCellValue("J".$k,iconv("EUC-KR", "UTF-8","��ƼĿ���"));
	$sheetIndex->setCellValue("K".$k,iconv("EUC-KR", "UTF-8","�����μ���"));
	$sheetIndex->setCellValue("L".$k,iconv("EUC-KR", "UTF-8","�ù� ��ۺ�"));
	$sheetIndex->setCellValue("M".$k,iconv("EUC-KR", "UTF-8","�ΰǺ�"));
	$sheetIndex->setCellValue("N".$k,iconv("EUC-KR", "UTF-8","��Ÿ���"));
	$sheetIndex->setCellValue("O".$k,iconv("EUC-KR", "UTF-8","�ǸŰ�"));
	$sheetIndex->setCellValue("P".$k,iconv("EUC-KR", "UTF-8","�Ǹż�����"));
	$sheetIndex->setCellValue("Q".$k,iconv("EUC-KR", "UTF-8","��������"));
	$sheetIndex->setCellValue("R".$k,iconv("EUC-KR", "UTF-8","������"));
	$sheetIndex->setCellValue("S".$k,iconv("EUC-KR", "UTF-8","���޻�"));

	//����
	if (sizeof($arr_rs) > 0) {
		for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
			$k += 1;
			
			//get data

			//��ǰ��ȣ
			$GOODS_NO				= trim($arr_rs[$j]["GOODS_NO"]);
			
			//��ǰ�ڵ�
			$GOODS_CODE				= trim($arr_rs[$j]["GOODS_CODE"]);

			//��ǰī�װ�
			$GOODS_CATE				= trim($arr_rs[$j]["GOODS_CATE"]);

			//��ǰ��
			$GOODS_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
			
			//�𵨸�
			$GOODS_SUB_NAME			= SetStringFromDB($arr_rs[$j]["GOODS_SUB_NAME"]);
			
			//����ǰ����
			// $CATE_01				= trim($arr_rs[$j]["CATE_01"]);

			//�ڽ��Լ�
			$DELIVERY_CNT_IN_BOX	= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);

			//�ּ����
			// $MSTOCK_CNT 			= trim($arr_rs[$j]["MSTOCK_CNT"]);

			//������
			$CATE_02				= trim($arr_rs[$j]["CATE_02"]);

			//���޻�
			$CATE_03				= trim($arr_rs[$j]["CATE_03"]);
			$CATE_03				= getCpNm($conn, $CATE_03);
			//�ǸŻ���
			$CATE_04				= trim($arr_rs[$j]["CATE_04"]);

			//���԰�
			$BUY_PRICE				= trim($arr_rs[$j]["BUY_PRICE"]);

			//��ƼĿ���
			$STICKER_PRICE       	= trim($arr_rs[$j]["STICKER_PRICE"]);

			//�����μ���
			$PRINT_PRICE			= trim($arr_rs[$j]["PRINT_PRICE"]);

			//�ù� ��ۺ�
			$DELIVERY_PRICE			= trim($arr_rs[$j]["DELIVERY_PRICE"]); 

			//�ΰǺ�
			$LABOR_PRICE			= trim($arr_rs[$j]["LABOR_PRICE"]);

			//��Ÿ���
			$OTHER_PRICE	        = trim($arr_rs[$j]["OTHER_PRICE"]);

			//�ǸŰ�
			$SALE_PRICE				= trim($arr_rs[$j]["SALE_PRICE"]);

			//�Ǹż�����
			$SALE_SUSU			    = trim($arr_rs[$j]["SALE_SUSU"]);

			//��������
			$TAX_TF					= trim($arr_rs[$j]["TAX_TF"]);


			$sheetIndex->setCellValue("A".$k,iconv("EUC-KR", "UTF-8",$GOODS_NO));
			$sheetIndex->setCellValue("B".$k,iconv("EUC-KR", "UTF-8",$GOODS_CODE));
			$sheetIndex->setCellValue("C".$k,iconv("EUC-KR", "UTF-8",$GOODS_CATE));
			//image
			$sheetIndex->setCellValue("E".$k,iconv("EUC-KR", "UTF-8",$GOODS_NAME));
			$sheetIndex->setCellValue("F".$k,iconv("EUC-KR", "UTF-8",$GOODS_SUB_NAME));
			$sheetIndex->setCellValue("G".$k,iconv("EUC-KR", "UTF-8",$DELIVERY_CNT_IN_BOX));
			// $sheetIndex->setCellValue("".$k,iconv("EUC-KR", "UTF-8",$CATE_01));
			// $sheetIndex->setCellValue("".$k,iconv("EUC-KR", "UTF-8",$MSTOCK_CNT));
			$sheetIndex->setCellValue("H".$k,iconv("EUC-KR", "UTF-8",$CATE_04));
			$sheetIndex->setCellValue("I".$k,iconv("EUC-KR", "UTF-8",$BUY_PRICE));
			$sheetIndex->setCellValue("J".$k,iconv("EUC-KR", "UTF-8",$STICKER_PRICE));
			$sheetIndex->setCellValue("K".$k,iconv("EUC-KR", "UTF-8",$PRINT_PRICE));
			$sheetIndex->setCellValue("L".$k,iconv("EUC-KR", "UTF-8",$DELIVERY_PRICE));
			$sheetIndex->setCellValue("M".$k,iconv("EUC-KR", "UTF-8",$LABOR_PRICE));
			$sheetIndex->setCellValue("N".$k,iconv("EUC-KR", "UTF-8",$OTHER_PRICE));
			$sheetIndex->setCellValue("O".$k,iconv("EUC-KR", "UTF-8",$SALE_PRICE));
			$sheetIndex->setCellValue("P".$k,iconv("EUC-KR", "UTF-8",$SALE_SUSU));
			$sheetIndex->setCellValue("Q".$k,iconv("EUC-KR", "UTF-8",$TAX_TF));
			$sheetIndex->setCellValue("R".$k,iconv("EUC-KR", "UTF-8",$CATE_02));
			$sheetIndex->setCellValue("S".$k,iconv("EUC-KR", "UTF-8",$CATE_03));

			//�����
			$sheetIndex->getRowDimension($k)->setRowHeight(115);

			//�̹����� ����
			$sheetIndex->getColumnDimension("D")->setWidth(21.5);

			$sheetIndex->getColumnDimension("B")->setWidth(18);
			$sheetIndex->getColumnDimension("C")->setWidth(18);
			$sheetIndex->getColumnDimension("E")->setWidth(50);
			$sheetIndex->getColumnDimension("F")->setWidth(40);
			$sheetIndex->getColumnDimension("R")->setWidth(35);
			$sheetIndex->getColumnDimension("S")->setWidth(35);

			//���� ��� ����
			$sheetIndex->getStyle("A$k:S$k")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

			// //set image
			// //create new image object
			$objDrawing_p = new PHPExcel_Worksheet_Drawing();
			
			// //get image path
			$img_url_big	= $_SERVER["DOCUMENT_ROOT"].getImage($conn, $GOODS_NO, "400", "400");	
			// echo "$img_url_big<br>";
			if($img_url_big == "/kustaf/www/upload_data/goods_image/500/209-213969.jpg"
			 ||$img_url_big == "/kustaf/www/upload_data/goods_image/500/209-213968.jpg"
			 ||$img_url_big == "/kustaf/www/upload_data/goods_image/500/401-213665.jpg"
			 ||$img_url_big == "/kustaf/www/upload_data/goods_image/500/708-213062.jpg"
			 ||$img_url_big == "/kustaf/www/upload_data/goods_image/500/809-212920.jpg"
			 ||$img_url_big == "/kustaf/www/upload_data/goods_image/500/110-214667.jpg"
			 ||$img_url_big == "/kustaf/www/upload_data/goods_image/500/150-113612.jpg"
			 ||$img_url_big == "/kustaf/www/upload_data/goods_image/500/607-214631.jpg"
			 ||$img_url_big == "/kustaf/www/upload_data/goods_image/500/607-214632.jpg"
			 ||$img_url_big == "/kustaf/www/upload_data/goods_image/500/708-212937.jpg"
			 ||$img_url_big == "/kustaf/www/upload_data/goods_image/500/708-212938.jpg"){
				$img_url_big = "/kustaf/www/manager/images/no_img.gif";
			}

			$objDrawing_p->setPath($img_url_big);
				
			// //set image position
			$objDrawing_p->setCoordinates("D".$k);

			// //set image position(offset)
			$objDrawing_p->setOffsetX(0);
			$objDrawing_p->setOffsetY(0);

			// //set image size
			$objDrawing_p->setWidthAndHeight(150,150);
			$objDrawing_p->setResizeProportional(true);

			// //print image
			$objDrawing_p->setWorksheet($objPHPExcel->setActiveSheetIndex(0));

			// ��Ÿ�� ����
			// $sheetIndex->getStyle("A$k:V$k")->applyFromArray($defaultStyle);
			
			// �ڵ� ����
			// $sheetIndex->getStyle("A$k:V$k")
			// ->getAlignment()
			// ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			// $sheetIndex->getStyle("A$k:V$k")
			// ->getAlignment()
			// ->setWrapText(true);
		}
	}

	// Rename sheet
	$objPHPExcel->setActiveSheetIndex(0)->setTitle(iconv("EUC-KR", "UTF-8","���̽���� ��� ��ǰ ����Ʈ"));

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	// ������ ���������� utf-8�� ��� �ѱ����� �̸��� �����Ƿ� euc-kr�� ��ȯ���ش�.
	$filename = "���̽����";

	// Redirect output to a client��s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment;filename=".$filename.".xls");
	header('Cache-Control: max-age=0');
 
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

	mysql_close($conn);
	exit;
?>