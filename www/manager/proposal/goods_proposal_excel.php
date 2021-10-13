<?session_start();
  set_time_limit(6000); 
  ini_set("memory_limit", -1);
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
	require "../../_classes/com/util/ImgUtilResize.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/proposal/proposal.php";
	require "../../_classes/biz/admin/admin.php";



#====================================================================
# Request Parameter
#====================================================================

	$gp_no = trim(base64url_decode($gp_no));
	$discount_percent = trim($discount_percent);

#===============================================================
# Get Search list count
#===============================================================

	$arr_rs = selectGoodsProposalByGpNo($conn, $gp_no);

	$GROUP_NO		= $arr_rs[0]["GROUP_NO"];
	$CP_NO			= $arr_rs[0]["CP_NO"];
	$GOODS_CATE		= $arr_rs[0]["GOODS_CATE"];
	$CP_NM			= getCompanyNameWithNoCode($conn, $CP_NO);
	$MEMO			= $arr_rs[0]["MEMO"];

	$arr_rs_goods = listGoodsProposalGoods($conn, $gp_no,'N');

	$IS_NOT_SAME_PRICE = false;
	if (sizeof($arr_rs_goods) > 0) {
		for ($j = 0 ; $j < sizeof($arr_rs_goods); $j++) {
			$RETAIL_PRICE				= trim($arr_rs_goods[$j]["RETAIL_PRICE"]);
			$PROPOSAL_PRICE				= trim($arr_rs_goods[$j]["PROPOSAL_PRICE"]);

			if($RETAIL_PRICE != $PROPOSAL_PRICE) { 
				$IS_NOT_SAME_PRICE = true;
				break;
			}

		}
	}

	require_once "../../_PHPExcel/Classes/PHPExcel.php";

	// Cell caching to reduce memory usage.
	$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
	$cacheSettings = array( ' memoryCacheSize ' => '8MB');
	PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings); 

	$objPHPExcel = new PHPExcel();

	$sheetIndex = $objPHPExcel->setActiveSheetIndex(0);

	$BStyle = array(
	  'borders' => array(
		'allborders' => array(
		  'style' => PHPExcel_Style_Border::BORDER_THIN
		)
	  )
	);

	$NoneStyle = array(
	  'borders' => array(
		'allborders' => array(
		  'style' => PHPExcel_Style_Border::BORDER_NONE
		)
	  )
	);

	$defaultStyle = array(
		'font'  => array(
			'size'  => 12,
			'name'  => '���� ���'
		),'borders' => array(
			'allborders' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		),'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
		)
	);

	$rowTitleStyle = array(
		'font'  => array(
			'size'  => 11,
			'name'  => '���� ���',
			'bold' => true
		)
	);

	$descriptionStyle = array(
		'font'  => array(
			'size'  => 8
		)
	);

	if($IS_NOT_SAME_PRICE) {
		//���� �߰��� A4 ���忡 3���� ��ǰ ���� ���
		if($print_type == "DETAIL_MIDDLE_SIZE"){
			$p = -1;
			
			for($goodsIndex = 0;$goodsIndex < sizeof($arr_rs_goods);$goodsIndex++){
				//create a new sheet
				if($goodsIndex % 3 == 0){
					//increase sheet index
					$p++;
					
					//Because the first sheet is automatically created, it creates a sheet except the first sheet.
					if($p != 0){
						$objPHPExcel->createSheet();
					}

					//set sheet title
					$objPHPExcel->setActiveSheetIndex($p)->setTitle(iconv("EUC-KR", "UTF-8", (($goodsIndex / 3)+1)."��"));

					//set printing properties
					//������ ���η� ����
					$objPHPExcel->setActiveSheetIndex($p)->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
					//�μ���� A4
					$objPHPExcel->setActiveSheetIndex($p)->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
					//�����¿� ����
					$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.4);
					$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.4);
					$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.24);
					$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.24);
	
					//set row title
					$objPHPExcel->setActiveSheetIndex($p)->setCellValue("A1",iconv("EUC-KR", "UTF-8","����"));
					$objPHPExcel->setActiveSheetIndex($p)->setCellValue("A2",iconv("EUC-KR", "UTF-8","�̹���"));
					$objPHPExcel->setActiveSheetIndex($p)->setCellValue("A3",iconv("EUC-KR", "UTF-8","ǰ��"));
					$objPHPExcel->setActiveSheetIndex($p)->setCellValue("A4",iconv("EUC-KR", "UTF-8","����"));
					$objPHPExcel->setActiveSheetIndex($p)->setCellValue("A5",iconv("EUC-KR", "UTF-8","����Ʈ��\n�ܰ�"));
					$objPHPExcel->setActiveSheetIndex($p)->setCellValue("A6",iconv("EUC-KR", "UTF-8","���Ȱ�"));
					$objPHPExcel->setActiveSheetIndex($p)->setCellValue("A7",iconv("EUC-KR", "UTF-8","�ڽ��Լ�"));

					//set style
					//set font and cell style
					$objPHPExcel->setActiveSheetIndex($p)->getStyle("A1:D7")->applyFromArray($defaultStyle);
					$objPHPExcel->setActiveSheetIndex($p)->getStyle("A1:A7")->applyFromArray($rowTitleStyle);
					$objPHPExcel->setActiveSheetIndex($p)->getStyle("B3:D4")->applyFromArray($descriptionStyle);
					$objPHPExcel->setActiveSheetIndex($p)->getStyle("B3:D4")->getAlignment()->setWrapText(true);
					$objPHPExcel->setActiveSheetIndex($p)->getStyle("A5")->getAlignment()->setWrapText(true);

					//set column width
					$rowTitleWidth = 10;
					$contentWidth = 43.7;
					$objPHPExcel->setActiveSheetIndex($p)->getColumnDimension("A")->setWidth($rowTitleWidth);
					$objPHPExcel->setActiveSheetIndex($p)->getColumnDimension("B")->setWidth($contentWidth);
					$objPHPExcel->setActiveSheetIndex($p)->getColumnDimension("C")->setWidth($contentWidth);
					$objPHPExcel->setActiveSheetIndex($p)->getColumnDimension("D")->setWidth($contentWidth);
					
					//set row height
					$rowHeightImage = 230;
					$rowHeightdefault = 30;
					$rowHeightHigh = 50;
					$objPHPExcel->setActiveSheetIndex($p)->getRowDimension("2")->setRowHeight($rowHeightImage);
					$objPHPExcel->setActiveSheetIndex($p)->getRowDimension("1")->setRowHeight($rowHeightdefault);
					$objPHPExcel->setActiveSheetIndex($p)->getRowDimension("3")->setRowHeight($rowHeightHigh);
					$objPHPExcel->setActiveSheetIndex($p)->getRowDimension("4")->setRowHeight($rowHeightHigh);
					$objPHPExcel->setActiveSheetIndex($p)->getRowDimension("5")->setRowHeight($rowHeightdefault);
					$objPHPExcel->setActiveSheetIndex($p)->getRowDimension("6")->setRowHeight($rowHeightdefault);
					$objPHPExcel->setActiveSheetIndex($p)->getRowDimension("7")->setRowHeight($rowHeightdefault);
				}

				//column change
				if($goodsIndex % 3 == 0){
					//set column number
					$column = "B";
				} elseif($goodsIndex % 3 == 1){
					//set column number
					$column = "C";
				} else {
					//set column number
					$column = "D";
				}

				//set contents
				$GPG_NO = trim($arr_rs_goods[$j]["GPG_NO"]);
				$GOODS_NO = trim($arr_rs_goods[$goodsIndex]["GOODS_NO"]);
				$GOODS_CODE = trim($arr_rs_goods[$goodsIndex]["GOODS_CODE"]);
				$GOODS_NAME = trim($arr_rs_goods[$goodsIndex]["GOODS_NAME"]);
				$RETAIL_PRICE = trim($arr_rs_goods[$goodsIndex]["RETAIL_PRICE"]);
				// $PROPOSAL_PRICE = trim($arr_rs_goods[$goodsIndex]["PROPOSAL_PRICE"]);

				//�ܰ������� ����
				$PROPOSAL_PRICE = $RETAIL_PRICE * (100 - $discount_percent) / 100.0;

				//���ڸ� �ϳ� ������
				$units = $PROPOSAL_PRICE - ((int)($PROPOSAL_PRICE / 10) * 10);
				//7�̻��̸� +10 7�̸��̸� 1���ڸ� ����
				if($units >= 7)
					$PROPOSAL_PRICE = ($PROPOSAL_PRICE - $units) + 10;
				else
					$PROPOSAL_PRICE -= $units;

				$DELIVERY_CNT_IN_BOX = trim($arr_rs_goods[$goodsIndex]["DELIVERY_CNT_IN_BOX"]);
				$COMPONENT = trim($arr_rs_goods[$goodsIndex]["COMPONENT"]);
				$SIZE = trim($arr_rs_goods[$goodsIndex]["SIZE"]);
				$DESCRIPTION = trim($arr_rs_goods[$goodsIndex]["DESCRIPTION"]);
				$MANUFACTURER = trim($arr_rs_goods[$goodsIndex]["MANUFACTURER"]);
				$ORIGIN = trim($arr_rs_goods[$goodsIndex]["ORIGIN"]);
				$GOODS_CATE = trim($arr_rs_goods[$goodsIndex]["GOODS_CATE"]);
				$PAGE = trim($arr_rs_goods[$goodsIndex]["PAGE"]);

				$objPHPExcel->setActiveSheetIndex($p)->setCellValue($column."1",iconv("EUC-KR", "UTF-8",$GOODS_CODE."(".($goodsIndex+1).")"));
				$objPHPExcel->setActiveSheetIndex($p)->setCellValue($column."2",iconv("EUC-KR", "UTF-8","�̹���"));
				$objPHPExcel->setActiveSheetIndex($p)->setCellValue($column."3",iconv("EUC-KR", "UTF-8",$GOODS_NAME));
				$objPHPExcel->setActiveSheetIndex($p)->setCellValue($column."4",iconv("EUC-KR", "UTF-8",$COMPONENT));
				$objPHPExcel->setActiveSheetIndex($p)->setCellValue($column."5",iconv("EUC-KR", "UTF-8",$RETAIL_PRICE));
				$objPHPExcel->setActiveSheetIndex($p)->setCellValue($column."6",iconv("EUC-KR", "UTF-8",$PROPOSAL_PRICE));
				$objPHPExcel->setActiveSheetIndex($p)->setCellValue($column."7",iconv("EUC-KR", "UTF-8",$DELIVERY_CNT_IN_BOX));
				
				//set thousands separator
				$objPHPExcel->setActiveSheetIndex($p)->getStyle($column."5")->getNumberFormat()->setFormatCode('#,###');
				$objPHPExcel->setActiveSheetIndex($p)->getStyle($column."6")->getNumberFormat()->setFormatCode('#,###');
				$objPHPExcel->setActiveSheetIndex($p)->getStyle($column."7")->getNumberFormat()->setFormatCode('#,###');

				//set image
				//create new image object
				$objDrawing_p = new PHPExcel_Worksheet_Drawing();
				
				//get image path
				$img_url_big	= $_SERVER["DOCUMENT_ROOT"].getImage($conn, $GOODS_NO, "400", "400");	
				$objDrawing_p->setPath($img_url_big);

				//set image position
				$objDrawing_p->setCoordinates($column."2");

				//set image position(offset)
				$objDrawing_p->setOffsetX(3);
				$objDrawing_p->setOffsetY(3);

				//set image size
				$objDrawing_p->setWidthAndHeight(300,300);
				$objDrawing_p->setResizeProportional(true);

				//print image
				$objDrawing_p->setWorksheet($objPHPExcel->setActiveSheetIndex($p));
			}
		} else {
			//���� ���

			//ī�װ� �߰��� ���� ���
			if($GOODS_CATE <> "") { 
				$CATEGORY_NAME = getCategoryNameOnly($conn, $GOODS_CATE);

				//1��
				$sheetIndex->setCellValue('A1',iconv("EUC-KR", "UTF-8","�� �� �� ( ".$CATEGORY_NAME." )"));
				if($print_type == "LIST_WITH_IMAGE")
					$sheetIndex->mergeCells('A1:H1');
				else
					$sheetIndex->mergeCells('A1:G1');
				$sheetIndex->getStyle('A1')->getFont()->setName('Gulim')->setSize(15)->setBold(true);
				$sheetIndex->getStyle('A1')->getAlignment()
				->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$sheetIndex->getRowDimension(1)->setRowHeight(40);


				//3�� (��ȣ	��ǰ��	����	�ڽ�����	����Ʈ�ݴܰ� 	���Ȱ�	�̹���)

				
				$sheetIndex	->setCellValue("A3", iconv("EUC-KR", "UTF-8","��ȣ"))
							->setCellValue("B3", iconv("EUC-KR", "UTF-8","ī�װ�"))
							->setCellValue("C3", iconv("EUC-KR", "UTF-8","������"))
							->setCellValue("D3", iconv("EUC-KR", "UTF-8","��ǰ��"))
							->setCellValue("E3", iconv("EUC-KR", "UTF-8","�ڽ�����"))
							->setCellValue("F3", iconv("EUC-KR", "UTF-8","����Ʈ��\n�ܰ�"))
							->setCellValue("G3", iconv("EUC-KR", "UTF-8","���Ȱ�"));

				if($print_type == "LIST_WITH_IMAGE") 
					$sheetIndex	->setCellValue("H3", iconv("EUC-KR", "UTF-8","�̹���"));

				//$sheetIndex->getRowDimension(3)->setRowHeight(25);

				$sheetIndex->getStyle('A3')->getFont()->setName('Gulim')->setBold(true);
				$sheetIndex->getStyle('A3')->getAlignment()->setWrapText(true)
				->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$sheetIndex->getStyle('B3')->getFont()->setName('Gulim')->setBold(true);
				$sheetIndex->getStyle('B3')->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$sheetIndex->getStyle('C3')->getFont()->setName('Gulim')->setBold(true);
				$sheetIndex->getStyle('C3')->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$sheetIndex->getStyle('D3')->getFont()->setName('Gulim')->setBold(true);
				$sheetIndex->getStyle('D3')->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$sheetIndex->getStyle('E3')->getFont()->setName('Gulim')->setBold(true);
				$sheetIndex->getStyle('E3')->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$sheetIndex->getStyle('F3')->getFont()->setName('Gulim')->setBold(true);
				$sheetIndex->getStyle('F3')->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$sheetIndex->getStyle('G3')->getFont()->setName('Gulim')->setBold(true);
				$sheetIndex->getStyle('G3')->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

				if($print_type == "LIST_WITH_IMAGE") { 
					$sheetIndex->getStyle('H3')->getFont()->setName('Gulim')->setBold(true);
					$sheetIndex->getStyle('H3')->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				}


				if (sizeof($arr_rs_goods) > 0) {
					$k = 0;
					$p = 1;
					for ($j = 0 ; $j < sizeof($arr_rs_goods); $j++) {

						$GPG_NO						= trim($arr_rs_goods[$j]["GPG_NO"]);
						$GOODS_NO					= trim($arr_rs_goods[$j]["GOODS_NO"]);
						$GOODS_CODE					= trim($arr_rs_goods[$j]["GOODS_CODE"]);
						$GOODS_NAME					= trim($arr_rs_goods[$j]["GOODS_NAME"]);
						$RETAIL_PRICE				= trim($arr_rs_goods[$j]["RETAIL_PRICE"]);
						$PROPOSAL_PRICE				= trim($arr_rs_goods[$j]["PROPOSAL_PRICE"]);
						$DELIVERY_CNT_IN_BOX		= trim($arr_rs_goods[$j]["DELIVERY_CNT_IN_BOX"]);
						$COMPONENT					= trim($arr_rs_goods[$j]["COMPONENT"]);
						$SIZE						= trim($arr_rs_goods[$j]["SIZE"]);
						$DESCRIPTION				= trim($arr_rs_goods[$j]["DESCRIPTION"]);
						$MANUFACTURER				= trim($arr_rs_goods[$j]["MANUFACTURER"]);
						$ORIGIN						= trim($arr_rs_goods[$j]["ORIGIN"]);

						$GOODS_CATE					= trim($arr_rs_goods[$j]["GOODS_CATE"]);
						$PAGE						= trim($arr_rs_goods[$j]["PAGE"]);

						$CATEGORY_NAME = getCategoryNameOnly($conn, $GOODS_CATE);

						if($RETAIL_PRICE <> "") 
							$RETAIL_PRICE = number_format($RETAIL_PRICE)." ��";
						
						if($PROPOSAL_PRICE <> "") 
							$PROPOSAL_PRICE = number_format($PROPOSAL_PRICE)." ��";

						$img_url	= getImage($conn, $GOODS_NO, "80", "80");
						$file_name = strtolower(substr($img_url, strrpos($img_url, '/') + 1));		
						$dst_file_path = $_SERVER["DOCUMENT_ROOT"]."/upload_data/goods_image/thumb_80/".$file_name;
						create_thumbnail($_SERVER["DOCUMENT_ROOT"].$img_url, $dst_file_path, 80, 80);

						//$objDrawing->getWidth(); - 80���� ���� 
						//$objDrawing->getHeight(); - 80���� ����


						$objDrawing = new PHPExcel_Worksheet_Drawing();

						$objDrawing->setPath($dst_file_path);

						$k = $j + 4;

						$sheetIndex
							->setCellValue("A$k", iconv("EUC-KR", "UTF-8",$j + 1))
							->setCellValue("B$k", iconv("EUC-KR", "UTF-8",$CATEGORY_NAME))
							->setCellValue("C$k", iconv("EUC-KR", "UTF-8",$PAGE))
							->setCellValue("D$k", iconv("EUC-KR", "UTF-8",$GOODS_NAME." [".$GOODS_CODE."]"))
							->setCellValue("E$k", iconv("EUC-KR", "UTF-8",$DELIVERY_CNT_IN_BOX))
							->setCellValue("F$k", iconv("EUC-KR", "UTF-8",$RETAIL_PRICE))
							->setCellValue("G$k", iconv("EUC-KR", "UTF-8",$PROPOSAL_PRICE));

						if($print_type == "LIST_WITH_IMAGE") { 
							$objDrawing->setCoordinates("H".$k);
							$objDrawing->setWidthAndHeight(80,80);
							$objDrawing->setResizeProportional(true);
							$objDrawing->setWorksheet($sheetIndex);
							$objDrawing->setOffsetX((105 - 80) / 2);
							$objDrawing->setOffsetY((100 - 80) / 2);
						}

						$sheetIndex->getStyle("A$k")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$sheetIndex->getStyle("B$k")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$sheetIndex->getStyle("C$k")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$sheetIndex->getStyle("D$k")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$sheetIndex->getStyle("E$k")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$sheetIndex->getStyle("F$k")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$sheetIndex->getStyle("G$k")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

						if($print_type == "LIST_WITH_IMAGE") { 
							$sheetIndex->getStyle("A$k:H".$k)->getFont()->setName('Gulim')->setSize(9);
							$sheetIndex->getStyle("A$k:H".$k)->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
							$sheetIndex->getStyle("C$k")->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
						} else { 
							$sheetIndex->getStyle("A$k:G".$k)->getFont()->setName('Gulim')->setSize(9);
							$sheetIndex->getStyle("A$k:G".$k)->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
							$sheetIndex->getStyle("C$k")->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
						}
						$sheetIndex->getRowDimension($k)->setRowHeight(80);
					}
				}

				if($print_type == "LIST_WITH_IMAGE") {
					$sheetIndex->getStyle("A3:H".$k)->applyFromArray($BStyle);
				} else 
					$sheetIndex->getStyle("A3:G".$k)->applyFromArray($BStyle);

				if($print_type == "LIST_WITH_IMAGE") {
					$sheetIndex->getColumnDimension('A')->setWidth(6);
					$sheetIndex->getColumnDimension('B')->setWidth(15);
					$sheetIndex->getColumnDimension('C')->setWidth(10);
					$sheetIndex->getColumnDimension('D')->setWidth(20);
					$sheetIndex->getColumnDimension('E')->setWidth(10);
					$sheetIndex->getColumnDimension('F')->setWidth(10);
					$sheetIndex->getColumnDimension('G')->setWidth(9);
					$sheetIndex->getColumnDimension('H')->setWidth(15);
				} else { 
					$sheetIndex->getColumnDimension('A')->setWidth(6);
					$sheetIndex->getColumnDimension('B')->setWidth(20);
					$sheetIndex->getColumnDimension('C')->setWidth(12);
					$sheetIndex->getColumnDimension('D')->setWidth(25);
					$sheetIndex->getColumnDimension('E')->setWidth(12);
					$sheetIndex->getColumnDimension('F')->setWidth(10);
					$sheetIndex->getColumnDimension('G')->setWidth(9);

				}
				
				$arr_memo = explode("\n", br2nl($MEMO));
				for ($m = 0; $m < sizeof($arr_memo); $m++) { 
					$sheetIndex->setCellValue("B".($k+$m+2), iconv("EUC-KR", "UTF-8", $arr_memo[$m]));

					if($print_type == "LIST_WITH_IMAGE") 
						$sheetIndex->mergeCells("B".($k+$m+2).":H".($k+$m+2));
					else
						$sheetIndex->mergeCells("B".($k+$m+2).":G".($k+$m+2));
					$sheetIndex->getStyle("B".($k+$m+2))->getFont()->setName('Gulim')->setSize(9);
				}
			
			} else { 


				//1��
				$sheetIndex->setCellValue('A1',iconv("EUC-KR", "UTF-8","�� �� ��"));
				if($print_type == "LIST_WITH_IMAGE" || $print_type == "ALL")
					$sheetIndex->mergeCells('A1:G1');
				else
					$sheetIndex->mergeCells('A1:F1');
				$sheetIndex->getStyle('A1')->getFont()->setName('Gulim')->setSize(15)->setBold(true);
				$sheetIndex->getStyle('A1')->getAlignment()
				->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$sheetIndex->getRowDimension(1)->setRowHeight(40);


				//3�� (��ȣ	��ǰ��	����	�ڽ�����	����Ʈ�ݴܰ� 	���Ȱ�	�̹���)

				$sheetIndex ->setCellValue("A3", iconv("EUC-KR", "UTF-8","��ȣ"))
							->setCellValue("B3", iconv("EUC-KR", "UTF-8","��ǰ��"))
							->setCellValue("C3", iconv("EUC-KR", "UTF-8","����"))
							->setCellValue("D3", iconv("EUC-KR", "UTF-8","�ڽ�����"))
							->setCellValue("E3", iconv("EUC-KR", "UTF-8","����Ʈ��\n�ܰ�"))
							->setCellValue("F3", iconv("EUC-KR", "UTF-8","���Ȱ�"));

				if($print_type == "LIST_WITH_IMAGE" || $print_type == "ALL") 
					$sheetIndex	->setCellValue("G3", iconv("EUC-KR", "UTF-8","�̹���"));


				//$sheetIndex->getRowDimension(3)->setRowHeight(25);

				$sheetIndex->getStyle('A3')->getFont()->setName('Gulim')->setBold(true);
				$sheetIndex->getStyle('A3')->getAlignment()->setWrapText(true)
				->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$sheetIndex->getStyle('B3')->getFont()->setName('Gulim')->setBold(true);
				$sheetIndex->getStyle('B3')->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$sheetIndex->getStyle('C3')->getFont()->setName('Gulim')->setBold(true);
				$sheetIndex->getStyle('C3')->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$sheetIndex->getStyle('D3')->getFont()->setName('Gulim')->setBold(true);
				$sheetIndex->getStyle('D3')->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$sheetIndex->getStyle('E3')->getFont()->setName('Gulim')->setBold(true);
				$sheetIndex->getStyle('E3')->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$sheetIndex->getStyle('F3')->getFont()->setName('Gulim')->setBold(true);
				$sheetIndex->getStyle('F3')->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

				if($print_type == "LIST_WITH_IMAGE" || $print_type == "ALL") { 
					$sheetIndex->getStyle('G3')->getFont()->setName('Gulim')->setBold(true);
					$sheetIndex->getStyle('G3')->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				}

				if (sizeof($arr_rs_goods) > 0) {
					$k = 0;
					$p = 1;
					for ($j = 0 ; $j < sizeof($arr_rs_goods); $j++) {

						$GPG_NO						= trim($arr_rs_goods[$j]["GPG_NO"]);
						$GOODS_NO					= trim($arr_rs_goods[$j]["GOODS_NO"]);
						$GOODS_CODE					= trim($arr_rs_goods[$j]["GOODS_CODE"]);
						$GOODS_NAME					= trim($arr_rs_goods[$j]["GOODS_NAME"]);
						$RETAIL_PRICE				= trim($arr_rs_goods[$j]["RETAIL_PRICE"]);
						$PROPOSAL_PRICE				= trim($arr_rs_goods[$j]["PROPOSAL_PRICE"]);
						$DELIVERY_CNT_IN_BOX		= trim($arr_rs_goods[$j]["DELIVERY_CNT_IN_BOX"]);
						$COMPONENT					= trim($arr_rs_goods[$j]["COMPONENT"]);
						$SIZE						= trim($arr_rs_goods[$j]["SIZE"]);
						$DESCRIPTION				= trim($arr_rs_goods[$j]["DESCRIPTION"]);
						$MANUFACTURER				= trim($arr_rs_goods[$j]["MANUFACTURER"]);
						$ORIGIN						= trim($arr_rs_goods[$j]["ORIGIN"]);

						if($RETAIL_PRICE <> "") 
							$RETAIL_PRICE = number_format($RETAIL_PRICE)." ��";
						
						if($PROPOSAL_PRICE <> "") 
							$PROPOSAL_PRICE = number_format($PROPOSAL_PRICE)." ��";

						$img_url	= getImage($conn, $GOODS_NO, "80", "80");
						$file_name = strtolower(substr($img_url, strrpos($img_url, '/') + 1));		
						$dst_file_path = $_SERVER["DOCUMENT_ROOT"]."/upload_data/goods_image/thumb_80/".$file_name;
						create_thumbnail($_SERVER["DOCUMENT_ROOT"].$img_url, $dst_file_path, 80, 80);

						//$objDrawing->getWidth(); - 80���� ���� 
						//$objDrawing->getHeight(); - 80���� ����


						$objDrawing = new PHPExcel_Worksheet_Drawing();

						$objDrawing->setPath($dst_file_path);

						$k = $j + 4;

						$sheetIndex
							->setCellValue("A$k", iconv("EUC-KR", "UTF-8",$j + 1))
							->setCellValue("B$k", iconv("EUC-KR", "UTF-8",$GOODS_NAME." [".$GOODS_CODE."]"))
							->setCellValue("C$k", iconv("EUC-KR", "UTF-8",br2nl($COMPONENT)))
							->setCellValue("D$k", iconv("EUC-KR", "UTF-8",$DELIVERY_CNT_IN_BOX))
							->setCellValue("E$k", iconv("EUC-KR", "UTF-8",$RETAIL_PRICE))
							->setCellValue("F$k", iconv("EUC-KR", "UTF-8",$PROPOSAL_PRICE));

						if($print_type == "LIST_WITH_IMAGE" || $print_type == "ALL") { 
							$objDrawing->setCoordinates("G".$k);
							$objDrawing->setWidthAndHeight(80,80);
							$objDrawing->setResizeProportional(true);
							$objDrawing->setWorksheet($sheetIndex);
							$objDrawing->setOffsetX((105 - 80) / 2);
							$objDrawing->setOffsetY((100 - 80) / 2);
						}

						

						$sheetIndex->getStyle("A$k")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$sheetIndex->getStyle("D$k")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$sheetIndex->getStyle("E$k")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$sheetIndex->getStyle("F$k")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

						if($print_type == "LIST_WITH_IMAGE" || $print_type == "ALL") { 
							$sheetIndex->getStyle("A$k:G".$k)->getFont()->setName('Gulim')->setSize(9);
							$sheetIndex->getStyle("A$k:G".$k)->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
							$sheetIndex->getStyle("C$k")->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
						} else { 
							$sheetIndex->getStyle("A$k:F".$k)->getFont()->setName('Gulim')->setSize(9);
							$sheetIndex->getStyle("A$k:F".$k)->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
							$sheetIndex->getStyle("C$k")->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
						}

						$sheetIndex->getRowDimension($k)->setRowHeight(80);

						if($print_type == "ALL") { 

							// ��ǰ �Ұ��� ��Ʈ (2 ~ )
							$objPHPExcel->createSheet();

							//1��
							$objPHPExcel->setActiveSheetIndex($p)->setCellValue('A1',iconv("EUC-KR", "UTF-8","�� ǰ �� �� ��"));
							$objPHPExcel->setActiveSheetIndex($p)->mergeCells('A1:D1');
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('A1')->getFont()->setName('Gulim')->setSize(15)->setBold(true);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('A1')->getAlignment()
							->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
							$objPHPExcel->setActiveSheetIndex($p)->getRowDimension(1)->setRowHeight(40);
							
							//3�� 
							$objPHPExcel->setActiveSheetIndex($p) 
								->setCellValue("A3", iconv("EUC-KR", "UTF-8","��ǰ��(�귣������)"))
								->setCellValue("B3", iconv("EUC-KR", "UTF-8",$GOODS_NAME." [".$GOODS_CODE."]"))->mergeCells('B3:D3');

							$objPHPExcel->setActiveSheetIndex($p)->getStyle('A3')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('A3')->getAlignment()
							->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('B3')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('B3')->getAlignment()
							->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
							
							//4-6��
							//$objPHPExcel->setActiveSheetIndex($p)->mergeCells('A4:D4');
							$img_url_big	= getImage($conn, $GOODS_NO, "400", "400");
							$file_name_big = strtolower(substr($img_url, strrpos($img_url, '/') + 1));		
							$dst_file_path_big = $_SERVER["DOCUMENT_ROOT"]."/upload_data/goods_image/thumb_400/".$file_name_big;
							create_thumbnail($_SERVER["DOCUMENT_ROOT"].$img_url_big, $dst_file_path_big, 400, 400);

							$objDrawing_p = new PHPExcel_Worksheet_Drawing();

							$objDrawing_p->setPath($dst_file_path_big);

							$objDrawing_p->setCoordinates("A5");
							
							$objDrawing_p->setWidthAndHeight(400,400);
							$objDrawing_p->setResizeProportional(true);

							$objDrawing_p->setOffsetX((609-$objDrawing_p->getWidth())/2);
							$objDrawing_p->setOffsetY(0);

							$objDrawing_p->setWorksheet($objPHPExcel->setActiveSheetIndex($p));
							//$objPHPExcel->setActiveSheetIndex($p)->mergeCells('A5:D25');
							//$objPHPExcel->setActiveSheetIndex($p)->mergeCells('A26:D26');
							$objPHPExcel->setActiveSheetIndex($p)->mergeCells('A4:D26');

							//27��
							$objPHPExcel->setActiveSheetIndex($p) 
								->setCellValue("A27", iconv("EUC-KR", "UTF-8","��ǰ�԰�( cm )"))
								->setCellValue("B27", br2nl4Excel(iconv("EUC-KR", "UTF-8",$SIZE)))->mergeCells('B27:D27');

							$objPHPExcel->setActiveSheetIndex($p)->getStyle('A27')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('A27')->getAlignment()
							->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('B27')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('B27')->getAlignment()
							->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

							//28��
							$objPHPExcel->setActiveSheetIndex($p) 
								->setCellValue("A28", iconv("EUC-KR", "UTF-8","����(���γ���)"))
								->setCellValue("B28", br2nl4Excel(iconv("EUC-KR", "UTF-8",$COMPONENT)))->mergeCells('B28:D28');

							$objPHPExcel->setActiveSheetIndex($p)->getRowDimension(28)->setRowHeight(getRowcount(br2nl4Excel($COMPONENT), 105) * 25 + 2.25);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('A28')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('A28')->getAlignment()->setWrapText(true)
							->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('B28')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('B28')->getAlignment()->setWrapText(true)
							->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

							//29��
							$objPHPExcel->setActiveSheetIndex($p) 
								->setCellValue("A29", iconv("EUC-KR", "UTF-8","�뵵 �� Ư¡"))
								->setCellValue("B29", br2nl4Excel(iconv("EUC-KR", "UTF-8", $DESCRIPTION)))->mergeCells('B29:D29');

							$objPHPExcel->setActiveSheetIndex($p)->getRowDimension(29)->setRowHeight(getRowcount(br2nl4Excel($DESCRIPTION), 105) * 25 + 2.25);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('A29')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('A29')->getAlignment()->setWrapText(true)
							->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('B29')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('B29')->getAlignment()->setWrapText(true)
							->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
							

							//30��
							$objPHPExcel->setActiveSheetIndex($p) 
								->setCellValue("A30", iconv("EUC-KR", "UTF-8","���Ȱ�(VAT����)"))
								->setCellValue("B30", iconv("EUC-KR", "UTF-8",$PROPOSAL_PRICE))->mergeCells('B30:D30');

							$objPHPExcel->setActiveSheetIndex($p)->getStyle('A30')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('A30')->getAlignment()
							->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('B30')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('B30')->getAlignment()
							->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

							//31��
							$objPHPExcel->setActiveSheetIndex($p) 
								->setCellValue("A31", iconv("EUC-KR", "UTF-8","����Ʈ�ݴܰ�"))
								->setCellValue("B31", iconv("EUC-KR", "UTF-8",$RETAIL_PRICE))
								->setCellValue("C31", iconv("EUC-KR", "UTF-8","�ڽ��Լ�"))
								->setCellValue("D31", iconv("EUC-KR", "UTF-8",$DELIVERY_CNT_IN_BOX));

							$objPHPExcel->setActiveSheetIndex($p)->getStyle('A31')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('A31')->getAlignment()
							->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('C31')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('C31')->getAlignment()
							->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('B31')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('B31')->getAlignment()
							->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('D31')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('D31')->getAlignment()
							->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

							//32��
							$objPHPExcel->setActiveSheetIndex($p) 
								->setCellValue("A32", iconv("EUC-KR", "UTF-8","������"))
								->setCellValue("B32", iconv("EUC-KR", "UTF-8",$MANUFACTURER))
								->setCellValue("C32", iconv("EUC-KR", "UTF-8","������"))
								->setCellValue("D32", iconv("EUC-KR", "UTF-8",$ORIGIN));

							$objPHPExcel->setActiveSheetIndex($p)->getStyle('A32')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('A32')->getAlignment()
							->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('C32')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('C32')->getAlignment()
							->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('B32')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('B32')->getAlignment()
							->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('D32')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
							$objPHPExcel->setActiveSheetIndex($p)->getStyle('D32')->getAlignment()
							->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

							$objPHPExcel->setActiveSheetIndex($p)->getColumnDimension('A')->setWidth(20);
							$objPHPExcel->setActiveSheetIndex($p)->getColumnDimension('B')->setWidth(21);
							$objPHPExcel->setActiveSheetIndex($p)->getColumnDimension('C')->setWidth(21);
							$objPHPExcel->setActiveSheetIndex($p)->getColumnDimension('D')->setWidth(25);
							
							$objPHPExcel->setActiveSheetIndex($p)->getStyle("A3:D32")->applyFromArray($BStyle);
							//$objPHPExcel->setActiveSheetIndex($p)->getStyle("A4:D26")->applyFromArray($NoneStyle);

							$objPHPExcel->setActiveSheetIndex($p)->setTitle(iconv("EUC-KR", "UTF-8", ($j + 1)."��"));

							$p = $p + 1;
						}
					}
				}

				if($print_type == "LIST_WITH_IMAGE" || $print_type == "ALL") {
					$sheetIndex->getStyle("A3:G".$k)->applyFromArray($BStyle);
				} else 
					$sheetIndex->getStyle("A3:F".$k)->applyFromArray($BStyle);

				if($print_type == "LIST_WITH_IMAGE" || $print_type == "ALL") {
					$sheetIndex->getColumnDimension('A')->setWidth(6);
					$sheetIndex->getColumnDimension('B')->setWidth(23);
					$sheetIndex->getColumnDimension('C')->setWidth(16);
					$sheetIndex->getColumnDimension('D')->setWidth(10);
					$sheetIndex->getColumnDimension('E')->setWidth(12);
					$sheetIndex->getColumnDimension('F')->setWidth(9);
					$sheetIndex->getColumnDimension('G')->setWidth(15);
				} else { 
					$sheetIndex->getColumnDimension('A')->setWidth(6);
					$sheetIndex->getColumnDimension('B')->setWidth(23);
					$sheetIndex->getColumnDimension('C')->setWidth(16);
					$sheetIndex->getColumnDimension('D')->setWidth(10);
					$sheetIndex->getColumnDimension('E')->setWidth(12);
					$sheetIndex->getColumnDimension('F')->setWidth(9);

				}

				$arr_memo = explode("\n", br2nl($MEMO));
				for ($m = 0; $m < sizeof($arr_memo); $m++) { 
					$sheetIndex->setCellValue("B".($k+$m+2), iconv("EUC-KR", "UTF-8", $arr_memo[$m]));

					if($print_type == "LIST_WITH_IMAGE" || $print_type == "ALL") 
						$sheetIndex->mergeCells("B".($k+$m+2).":G".($k+$m+2));
					else
						$sheetIndex->mergeCells("B".($k+$m+2).":F".($k+$m+2));
					$sheetIndex->getStyle("B".($k+$m+2))->getFont()->setName('Gulim')->setSize(9);
				}

			}
		}
	} else {
		if($print_type == "DETAIL_MIDDLE_SIZE"){
		$p = -1;
		
		for($goodsIndex = 0;$goodsIndex < sizeof($arr_rs_goods);$goodsIndex++){
			//create a new sheet
			if($goodsIndex % 3 == 0){
				//increase sheet index
				$p++;
				
				//Because the first sheet is automatically created, it creates a sheet except the first sheet.
				if($p != 0){
					$objPHPExcel->createSheet();
				}

				//set sheet title
				$objPHPExcel->setActiveSheetIndex($p)->setTitle(iconv("EUC-KR", "UTF-8", (($goodsIndex / 3)+1)."��"));

				//set printing properties
				//������ ���η� ����
				$objPHPExcel->setActiveSheetIndex($p)->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
				//�μ���� A4
				$objPHPExcel->setActiveSheetIndex($p)->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
				//�����¿� ����
				$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.4);
				$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.4);
				$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.24);
				$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.24);

				//set row title
				$objPHPExcel->setActiveSheetIndex($p)->setCellValue("A1",iconv("EUC-KR", "UTF-8","����"));
				$objPHPExcel->setActiveSheetIndex($p)->setCellValue("A2",iconv("EUC-KR", "UTF-8","�̹���"));
				$objPHPExcel->setActiveSheetIndex($p)->setCellValue("A3",iconv("EUC-KR", "UTF-8","ǰ��"));
				$objPHPExcel->setActiveSheetIndex($p)->setCellValue("A4",iconv("EUC-KR", "UTF-8","����"));
				$objPHPExcel->setActiveSheetIndex($p)->setCellValue("A5",iconv("EUC-KR", "UTF-8","����Ʈ��\n�ܰ�"));
				$objPHPExcel->setActiveSheetIndex($p)->setCellValue("A6",iconv("EUC-KR", "UTF-8","���Ȱ�"));
				$objPHPExcel->setActiveSheetIndex($p)->setCellValue("A7",iconv("EUC-KR", "UTF-8","�ڽ��Լ�"));

				//set style
				//set font and cell style
				$objPHPExcel->setActiveSheetIndex($p)->getStyle("A1:D7")->applyFromArray($defaultStyle);
				$objPHPExcel->setActiveSheetIndex($p)->getStyle("A1:A7")->applyFromArray($rowTitleStyle);
				$objPHPExcel->setActiveSheetIndex($p)->getStyle("B3:D4")->applyFromArray($descriptionStyle);
				$objPHPExcel->setActiveSheetIndex($p)->getStyle("B3:D4")->getAlignment()->setWrapText(true);
				$objPHPExcel->setActiveSheetIndex($p)->getStyle("A5")->getAlignment()->setWrapText(true);

				//set column width
				$rowTitleWidth = 10;
				$contentWidth = 43.7;
				$objPHPExcel->setActiveSheetIndex($p)->getColumnDimension("A")->setWidth($rowTitleWidth);
				$objPHPExcel->setActiveSheetIndex($p)->getColumnDimension("B")->setWidth($contentWidth);
				$objPHPExcel->setActiveSheetIndex($p)->getColumnDimension("C")->setWidth($contentWidth);
				$objPHPExcel->setActiveSheetIndex($p)->getColumnDimension("D")->setWidth($contentWidth);
				
				//set row height
				$rowHeightImage = 230;
				$rowHeightdefault = 30;
				$rowHeightHigh = 50;
				$objPHPExcel->setActiveSheetIndex($p)->getRowDimension("2")->setRowHeight($rowHeightImage);
				$objPHPExcel->setActiveSheetIndex($p)->getRowDimension("1")->setRowHeight($rowHeightdefault);
				$objPHPExcel->setActiveSheetIndex($p)->getRowDimension("3")->setRowHeight($rowHeightHigh);
				$objPHPExcel->setActiveSheetIndex($p)->getRowDimension("4")->setRowHeight($rowHeightHigh);
				$objPHPExcel->setActiveSheetIndex($p)->getRowDimension("5")->setRowHeight($rowHeightdefault);
				$objPHPExcel->setActiveSheetIndex($p)->getRowDimension("6")->setRowHeight($rowHeightdefault);
				$objPHPExcel->setActiveSheetIndex($p)->getRowDimension("7")->setRowHeight($rowHeightdefault);
			}

			//column change
			if($goodsIndex % 3 == 0){
				//set column number
				$column = "B";
			} elseif($goodsIndex % 3 == 1){
				//set column number
				$column = "C";
			} else {
				//set column number
				$column = "D";
			}

			//set contents
			$GPG_NO = trim($arr_rs_goods[$j]["GPG_NO"]);
			$GOODS_NO = trim($arr_rs_goods[$goodsIndex]["GOODS_NO"]);
			$GOODS_CODE = trim($arr_rs_goods[$goodsIndex]["GOODS_CODE"]);
			$GOODS_NAME = trim($arr_rs_goods[$goodsIndex]["GOODS_NAME"]);
			$RETAIL_PRICE = trim($arr_rs_goods[$goodsIndex]["RETAIL_PRICE"]);
			// $PROPOSAL_PRICE = trim($arr_rs_goods[$goodsIndex]["PROPOSAL_PRICE"]);
			//�ܰ������� ����
			$PROPOSAL_PRICE = $RETAIL_PRICE * (100 - $discount_percent) / 100.0;
			$DELIVERY_CNT_IN_BOX = trim($arr_rs_goods[$goodsIndex]["DELIVERY_CNT_IN_BOX"]);
			$COMPONENT = trim($arr_rs_goods[$goodsIndex]["COMPONENT"]);
			$SIZE = trim($arr_rs_goods[$goodsIndex]["SIZE"]);
			$DESCRIPTION = trim($arr_rs_goods[$goodsIndex]["DESCRIPTION"]);
			$MANUFACTURER = trim($arr_rs_goods[$goodsIndex]["MANUFACTURER"]);
			$ORIGIN = trim($arr_rs_goods[$goodsIndex]["ORIGIN"]);
			$GOODS_CATE = trim($arr_rs_goods[$goodsIndex]["GOODS_CATE"]);
			$PAGE = trim($arr_rs_goods[$goodsIndex]["PAGE"]);

			$objPHPExcel->setActiveSheetIndex($p)->setCellValue($column."1",iconv("EUC-KR", "UTF-8",$GOODS_CODE."(".($goodsIndex+1).")"));
			$objPHPExcel->setActiveSheetIndex($p)->setCellValue($column."2",iconv("EUC-KR", "UTF-8","�̹���"));
			$objPHPExcel->setActiveSheetIndex($p)->setCellValue($column."3",iconv("EUC-KR", "UTF-8",$GOODS_NAME));
			$objPHPExcel->setActiveSheetIndex($p)->setCellValue($column."4",iconv("EUC-KR", "UTF-8",$COMPONENT));
			$objPHPExcel->setActiveSheetIndex($p)->setCellValue($column."5",iconv("EUC-KR", "UTF-8",$RETAIL_PRICE));
			$objPHPExcel->setActiveSheetIndex($p)->setCellValue($column."6",iconv("EUC-KR", "UTF-8",$PROPOSAL_PRICE));
			$objPHPExcel->setActiveSheetIndex($p)->setCellValue($column."7",iconv("EUC-KR", "UTF-8",$DELIVERY_CNT_IN_BOX));

			//set thousands separator
			$objPHPExcel->setActiveSheetIndex($p)->getStyle($column."5")->getNumberFormat()->setFormatCode('#,###');
			$objPHPExcel->setActiveSheetIndex($p)->getStyle($column."6")->getNumberFormat()->setFormatCode('#,###');
			$objPHPExcel->setActiveSheetIndex($p)->getStyle($column."7")->getNumberFormat()->setFormatCode('#,###');

			//set image
			//create new image object
			$objDrawing_p = new PHPExcel_Worksheet_Drawing();
			
			//get image path
			$img_url_big	= $_SERVER["DOCUMENT_ROOT"].getImage($conn, $GOODS_NO, "400", "400");	
			$objDrawing_p->setPath($img_url_big);

			//set image position
			$objDrawing_p->setCoordinates($column."2");

			//set image position(offset)
			$objDrawing_p->setOffsetX(3);
			$objDrawing_p->setOffsetY(3);

			//set image size
			$objDrawing_p->setWidthAndHeight(300,300);
			$objDrawing_p->setResizeProportional(true);

			//print image
			$objDrawing_p->setWorksheet($objPHPExcel->setActiveSheetIndex($p));
		}
	} else {
			//���� ���

			//1��
			$sheetIndex->setCellValue('A1',iconv("EUC-KR", "UTF-8","�� �� ��"));
			$sheetIndex->mergeCells('A1:F1');
			$sheetIndex->getStyle('A1')->getFont()->setName('Gulim')->setSize(15)->setBold(true);
			$sheetIndex->getStyle('A1')->getAlignment()
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$sheetIndex->getRowDimension(1)->setRowHeight(40);


			//3�� (��ȣ	��ǰ��	����	�ڽ�����	����Ʈ�ݴܰ� 	���Ȱ�	�̹���)

			$sheetIndex ->setCellValue("A3", iconv("EUC-KR", "UTF-8","��ȣ"))
						->setCellValue("B3", iconv("EUC-KR", "UTF-8","��ǰ��"))
						->setCellValue("C3", iconv("EUC-KR", "UTF-8","����"))
						->setCellValue("D3", iconv("EUC-KR", "UTF-8","�ڽ�����"))
						->setCellValue("E3", iconv("EUC-KR", "UTF-8","���Ȱ�"))
						->setCellValue("F3", iconv("EUC-KR", "UTF-8","�̹���"));

			//$sheetIndex->getRowDimension(3)->setRowHeight(25);

			$sheetIndex->getStyle('A3')->getFont()->setName('Gulim')->setBold(true);
			$sheetIndex->getStyle('A3')->getAlignment()->setWrapText(true)
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$sheetIndex->getStyle('B3')->getFont()->setName('Gulim')->setBold(true);
			$sheetIndex->getStyle('B3')->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$sheetIndex->getStyle('C3')->getFont()->setName('Gulim')->setBold(true);
			$sheetIndex->getStyle('C3')->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$sheetIndex->getStyle('D3')->getFont()->setName('Gulim')->setBold(true);
			$sheetIndex->getStyle('D3')->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$sheetIndex->getStyle('E3')->getFont()->setName('Gulim')->setBold(true);
			$sheetIndex->getStyle('E3')->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$sheetIndex->getStyle('F3')->getFont()->setName('Gulim')->setBold(true);
			$sheetIndex->getStyle('F3')->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);


			if (sizeof($arr_rs_goods) > 0) {
				$k = 0;
				$p = 1;
				for ($j = 0 ; $j < sizeof($arr_rs_goods); $j++) {

					$GPG_NO						= trim($arr_rs_goods[$j]["GPG_NO"]);
					$GOODS_NO					= trim($arr_rs_goods[$j]["GOODS_NO"]);
					$GOODS_CODE					= trim($arr_rs_goods[$j]["GOODS_CODE"]);
					$GOODS_NAME					= trim($arr_rs_goods[$j]["GOODS_NAME"]);
					$PROPOSAL_PRICE				= trim($arr_rs_goods[$j]["PROPOSAL_PRICE"]);
					$DELIVERY_CNT_IN_BOX		= trim($arr_rs_goods[$j]["DELIVERY_CNT_IN_BOX"]);
					$COMPONENT					= trim($arr_rs_goods[$j]["COMPONENT"]);
					$SIZE						= trim($arr_rs_goods[$j]["SIZE"]);
					$DESCRIPTION				= trim($arr_rs_goods[$j]["DESCRIPTION"]);
					$MANUFACTURER				= trim($arr_rs_goods[$j]["MANUFACTURER"]);
					$ORIGIN						= trim($arr_rs_goods[$j]["ORIGIN"]);

					if($PROPOSAL_PRICE <> "") 
						$PROPOSAL_PRICE = number_format($PROPOSAL_PRICE)." ��";

					$img_url	= getImage($conn, $GOODS_NO, "80", "80");
					$file_name = strtolower(substr($img_url, strrpos($img_url, '/') + 1));		
					$dst_file_path = $_SERVER["DOCUMENT_ROOT"]."/upload_data/goods_image/thumb_80/".$file_name;
					create_thumbnail($_SERVER["DOCUMENT_ROOT"].$img_url, $dst_file_path, 80, 80);

					//$objDrawing->getWidth(); - 80���� ���� 
					//$objDrawing->getHeight(); - 80���� ����


					$objDrawing = new PHPExcel_Worksheet_Drawing();

					$objDrawing->setPath($dst_file_path);

					$k = $j + 4;

					$sheetIndex
						->setCellValue("A$k", iconv("EUC-KR", "UTF-8",$j + 1))
						->setCellValue("B$k", iconv("EUC-KR", "UTF-8",$GOODS_NAME." [".$GOODS_CODE."]"))
						->setCellValue("C$k", iconv("EUC-KR", "UTF-8",br2nl($COMPONENT)))
						->setCellValue("D$k", iconv("EUC-KR", "UTF-8",$DELIVERY_CNT_IN_BOX))
						->setCellValue("E$k", iconv("EUC-KR", "UTF-8",$PROPOSAL_PRICE));

					$objDrawing->setCoordinates("F".$k);
					$objDrawing->setWidthAndHeight(80,80);
					$objDrawing->setResizeProportional(true);
					$objDrawing->setWorksheet($sheetIndex);
					$objDrawing->setOffsetX((105 - 80) / 2);
					$objDrawing->setOffsetY((100 - 80) / 2);

					$sheetIndex->getStyle("A$k")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sheetIndex->getStyle("D$k")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sheetIndex->getStyle("E$k")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

					$sheetIndex->getStyle("A$k:F".$k)->getFont()->setName('Gulim')->setSize(9);
					$sheetIndex->getStyle("A$k:F".$k)->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

					$sheetIndex->getRowDimension($k)->setRowHeight(80);

					if($print_type == "ALL") { 

						// ��ǰ �Ұ��� ��Ʈ (2 ~ )
						$objPHPExcel->createSheet();

						//1��
						$objPHPExcel->setActiveSheetIndex($p)->setCellValue('A1',iconv("EUC-KR", "UTF-8","�� ǰ �� �� ��"));
						$objPHPExcel->setActiveSheetIndex($p)->mergeCells('A1:D1');
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('A1')->getFont()->setName('Gulim')->setSize(15)->setBold(true);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('A1')->getAlignment()
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$objPHPExcel->setActiveSheetIndex($p)->getRowDimension(1)->setRowHeight(40);
						
						//3�� 
						$objPHPExcel->setActiveSheetIndex($p) 
							->setCellValue("A3", iconv("EUC-KR", "UTF-8","��ǰ��(�귣������)"))
							->setCellValue("B3", iconv("EUC-KR", "UTF-8",$GOODS_NAME." [".$GOODS_CODE."]"))->mergeCells('B3:D3');

						$objPHPExcel->setActiveSheetIndex($p)->getStyle('A3')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('A3')->getAlignment()
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('B3')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('B3')->getAlignment()
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						
						//4-6��
						//$objPHPExcel->setActiveSheetIndex($p)->mergeCells('A4:D4');
						$img_url_big	= getImage($conn, $GOODS_NO, "400", "400");
						$file_name_big = strtolower(substr($img_url, strrpos($img_url, '/') + 1));		
						$dst_file_path_big = $_SERVER["DOCUMENT_ROOT"]."/upload_data/goods_image/thumb_400/".$file_name_big;
						create_thumbnail($_SERVER["DOCUMENT_ROOT"].$img_url_big, $dst_file_path_big, 400, 400);

						$objDrawing_p = new PHPExcel_Worksheet_Drawing();

						$objDrawing_p->setPath($dst_file_path_big);

						$objDrawing_p->setCoordinates("A5");
						
						$objDrawing_p->setWidthAndHeight(400,400);
						$objDrawing_p->setResizeProportional(true);

						$objDrawing_p->setOffsetX((609-$objDrawing_p->getWidth())/2);
						$objDrawing_p->setOffsetY(0);

						$objDrawing_p->setWorksheet($objPHPExcel->setActiveSheetIndex($p));
						//$objPHPExcel->setActiveSheetIndex($p)->mergeCells('A5:D25');
						//$objPHPExcel->setActiveSheetIndex($p)->mergeCells('A26:D26');
						$objPHPExcel->setActiveSheetIndex($p)->mergeCells('A4:D26');

						//27��
						$objPHPExcel->setActiveSheetIndex($p) 
							->setCellValue("A27", iconv("EUC-KR", "UTF-8","��ǰ�԰�( cm )"))
							->setCellValue("B27", br2nl4Excel(iconv("EUC-KR", "UTF-8",$SIZE)))->mergeCells('B27:D27');

						$objPHPExcel->setActiveSheetIndex($p)->getStyle('A27')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('A27')->getAlignment()
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('B27')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('B27')->getAlignment()
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

						//28��
						$objPHPExcel->setActiveSheetIndex($p) 
							->setCellValue("A28", iconv("EUC-KR", "UTF-8","����(���γ���)"))
							->setCellValue("B28", br2nl4Excel(iconv("EUC-KR", "UTF-8",$COMPONENT)))->mergeCells('B28:D28');

						$objPHPExcel->setActiveSheetIndex($p)->getRowDimension(28)->setRowHeight(getRowcount(br2nl4Excel($COMPONENT), 105) * 25 + 2.25);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('A28')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('A28')->getAlignment()->setWrapText(true)
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('B28')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('B28')->getAlignment()->setWrapText(true)
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

						//29��
						$objPHPExcel->setActiveSheetIndex($p) 
							->setCellValue("A29", iconv("EUC-KR", "UTF-8","�뵵 �� Ư¡"))
							->setCellValue("B29", br2nl4Excel(iconv("EUC-KR", "UTF-8", $DESCRIPTION)))->mergeCells('B29:D29');

						$objPHPExcel->setActiveSheetIndex($p)->getRowDimension(29)->setRowHeight(getRowcount(br2nl4Excel($DESCRIPTION), 105) * 25 + 2.25);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('A29')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('A29')->getAlignment()->setWrapText(true)
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('B29')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('B29')->getAlignment()->setWrapText(true)
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						

						//30��
						$objPHPExcel->setActiveSheetIndex($p) 
							->setCellValue("A30", iconv("EUC-KR", "UTF-8","���Ȱ�(VAT����)"))
							->setCellValue("B30", iconv("EUC-KR", "UTF-8",$PROPOSAL_PRICE))
							->setCellValue("C30", iconv("EUC-KR", "UTF-8","�ڽ��Լ�"))
							->setCellValue("D30", iconv("EUC-KR", "UTF-8",$DELIVERY_CNT_IN_BOX));

						$objPHPExcel->setActiveSheetIndex($p)->getStyle('A30')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('A30')->getAlignment()
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('C30')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('C30')->getAlignment()
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('B30')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('B30')->getAlignment()
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('D30')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('D30')->getAlignment()
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

						//32��
						$objPHPExcel->setActiveSheetIndex($p) 
							->setCellValue("A31", iconv("EUC-KR", "UTF-8","������"))
							->setCellValue("B31", iconv("EUC-KR", "UTF-8",$MANUFACTURER))
							->setCellValue("C31", iconv("EUC-KR", "UTF-8","������"))
							->setCellValue("D31", iconv("EUC-KR", "UTF-8",$ORIGIN));

						$objPHPExcel->setActiveSheetIndex($p)->getStyle('A31')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('A31')->getAlignment()
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('C31')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('C31')->getAlignment()
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('B31')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('B31')->getAlignment()
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('D31')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('D31')->getAlignment()
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

						$objPHPExcel->setActiveSheetIndex($p)->getColumnDimension('A')->setWidth(20);
						$objPHPExcel->setActiveSheetIndex($p)->getColumnDimension('B')->setWidth(21);
						$objPHPExcel->setActiveSheetIndex($p)->getColumnDimension('C')->setWidth(21);
						$objPHPExcel->setActiveSheetIndex($p)->getColumnDimension('D')->setWidth(25);
						
						$objPHPExcel->setActiveSheetIndex($p)->getStyle("A3:D31")->applyFromArray($BStyle);
						//$objPHPExcel->setActiveSheetIndex($p)->getStyle("A4:D26")->applyFromArray($NoneStyle);

						$objPHPExcel->setActiveSheetIndex($p)->setTitle(iconv("EUC-KR", "UTF-8", ($j + 1)."��"));

						$p = $p + 1;
					}
				}
			}

			$sheetIndex->getStyle("A3:F".$k)->applyFromArray($BStyle);

			$sheetIndex->getColumnDimension('A')->setWidth(6);
			$sheetIndex->getColumnDimension('B')->setWidth(30);
			$sheetIndex->getColumnDimension('C')->setWidth(16);
			$sheetIndex->getColumnDimension('D')->setWidth(10);
			$sheetIndex->getColumnDimension('E')->setWidth(12);
			$sheetIndex->getColumnDimension('F')->setWidth(15);
			
			$arr_memo = explode("\n", br2nl($MEMO));
			for ($m = 0; $m < sizeof($arr_memo); $m++) { 
				$sheetIndex->setCellValue("B".($k+$m+2), iconv("EUC-KR", "UTF-8", $arr_memo[$m]));
				$sheetIndex->mergeCells("B".($k+$m+2).":F".($k+$m+2));
				$sheetIndex->getStyle("B".($k+$m+2))->getFont()->setName('Gulim')->setSize(9);
			}

		}
	}

	if($print_type != "DETAIL_MIDDLE_SIZE"){
		// Rename sheet
		$sheetIndex->setTitle(iconv("EUC-KR", "UTF-8",'���ȼ�'));
	}

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	//$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(100);

	//echo"<script>console.log('".date("Ymd")."');</script>";
	//echo"<script>alert('".date("Ymd")."');</script>";
	
	// ������ ���������� utf-8�� ��� �ѱ����� �̸��� �����Ƿ� euc-kr�� ��ȯ���ش�.
	$filename = "���ȼ�-".date("Ymd");

	// Redirect output to a client��s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment;filename=".$filename.".xls");
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->setUseDiskCaching(true);
	$objWriter->save('php://output');


	mysql_close($conn);
	exit;


?>
				
