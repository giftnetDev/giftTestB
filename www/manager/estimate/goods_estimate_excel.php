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
	require "../../_classes/biz/estimate/estimate.php";
	require "../../_classes/biz/admin/admin.php";



#====================================================================
# Request Parameter
#====================================================================

	$gp_no = trim(base64url_decode($gp_no));

#===============================================================
# Get Search list count
#===============================================================

	$arr_rs = selectGoodsEstimateByGpNo($conn, $gp_no);

	$GROUP_NO		= $arr_rs[0]["GROUP_NO"];
	$CP_NO			= $arr_rs[0]["CP_NO"];
	$GOODS_CATE		= $arr_rs[0]["GOODS_CATE"];
	$CP_NM			= getCompanyNameWithNoCode($conn, $CP_NO);
	$MEMO			= $arr_rs[0]["MEMO"];

	$arr_rs_goods = listGoodsEstimateGoods($conn, $gp_no,'N');

	$IS_NOT_SAME_PRICE = false;
	if (sizeof($arr_rs_goods) > 0) {
		for ($j = 0 ; $j < sizeof($arr_rs_goods); $j++) {
			$RETAIL_PRICE				= trim($arr_rs_goods[$j]["RETAIL_PRICE"]);
			$ESTIMATE_PRICE				= trim($arr_rs_goods[$j]["ESTIMATE_PRICE"]);

			if($RETAIL_PRICE != $ESTIMATE_PRICE) { 
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

	if($IS_NOT_SAME_PRICE) { 


		//카테고리 추가로 인한 출력
		if($GOODS_CATE <> "") { 
			$CATEGORY_NAME = getCategoryNameOnly($conn, $GOODS_CATE);

			//1열
			$sheetIndex->setCellValue('A1',iconv("EUC-KR", "UTF-8","제 안 서 ( ".$CATEGORY_NAME." )"));
			if($print_type == "LIST_WITH_IMAGE")
				$sheetIndex->mergeCells('A1:H1');
			else
				$sheetIndex->mergeCells('A1:G1');
			$sheetIndex->getStyle('A1')->getFont()->setName('Gulim')->setSize(15)->setBold(true);
			$sheetIndex->getStyle('A1')->getAlignment()
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$sheetIndex->getRowDimension(1)->setRowHeight(40);
			$sheetIndex->getRowDimension(2)->setRowHeight(60);


			//3열 (번호	상품명	구성	박스단위	기프트넷단가 	제안가	이미지)

			
			$sheetIndex	->setCellValue("A3", iconv("EUC-KR", "UTF-8","번호"))
						->setCellValue("B3", iconv("EUC-KR", "UTF-8","카테고리"))
						->setCellValue("C3", iconv("EUC-KR", "UTF-8","페이지"))
						->setCellValue("D3", iconv("EUC-KR", "UTF-8","상품명"))
						->setCellValue("E3", iconv("EUC-KR", "UTF-8","박스단위"))
						->setCellValue("F3", iconv("EUC-KR", "UTF-8","기프트넷\n단가"))
						->setCellValue("G3", iconv("EUC-KR", "UTF-8","제안가"));

			if($print_type == "LIST_WITH_IMAGE") 
				$sheetIndex	->setCellValue("H3", iconv("EUC-KR", "UTF-8","이미지"));

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
					$ESTIMATE_PRICE				= trim($arr_rs_goods[$j]["ESTIMATE_PRICE"]);
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
						$RETAIL_PRICE = number_format($RETAIL_PRICE)." 원";
					
					if($ESTIMATE_PRICE <> "") 
						$ESTIMATE_PRICE = number_format($ESTIMATE_PRICE)." 원";

					$img_url	= getImage($conn, $GOODS_NO, "80", "80");
					$file_name = strtolower(substr($img_url, strrpos($img_url, '/') + 1));		
					$dst_file_path = $_SERVER["DOCUMENT_ROOT"]."/upload_data/goods_image/thumb_80/".$file_name;
					create_thumbnail($_SERVER["DOCUMENT_ROOT"].$img_url, $dst_file_path, 80, 80);

					//$objDrawing->getWidth(); - 80으로 수정 
					//$objDrawing->getHeight(); - 80으로 수정


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
						->setCellValue("G$k", iconv("EUC-KR", "UTF-8",$ESTIMATE_PRICE));

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


			//1열
			$sheetIndex->setCellValue('A1',iconv("EUC-KR", "UTF-8","제 안 서"));
			if($print_type == "LIST_WITH_IMAGE" || $print_type == "ALL")
				$sheetIndex->mergeCells('A1:G1');
			else
				$sheetIndex->mergeCells('A1:F1');
			$sheetIndex->getStyle('A1')->getFont()->setName('Gulim')->setSize(15)->setBold(true);
			$sheetIndex->getStyle('A1')->getAlignment()
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$sheetIndex->getRowDimension(1)->setRowHeight(40);


			//3열 (번호	상품명	구성	박스단위	기프트넷단가 	제안가	이미지)

			$sheetIndex ->setCellValue("A3", iconv("EUC-KR", "UTF-8","번호"))
						->setCellValue("B3", iconv("EUC-KR", "UTF-8","상품명"))
						->setCellValue("C3", iconv("EUC-KR", "UTF-8","구성"))
						->setCellValue("D3", iconv("EUC-KR", "UTF-8","박스단위"))
						->setCellValue("E3", iconv("EUC-KR", "UTF-8","기프트넷\n단가"))
						->setCellValue("F3", iconv("EUC-KR", "UTF-8","제안가"));

			if($print_type == "LIST_WITH_IMAGE" || $print_type == "ALL") 
				$sheetIndex	->setCellValue("G3", iconv("EUC-KR", "UTF-8","이미지"));


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
					$ESTIMATE_PRICE				= trim($arr_rs_goods[$j]["ESTIMATE_PRICE"]);
					$DELIVERY_CNT_IN_BOX		= trim($arr_rs_goods[$j]["DELIVERY_CNT_IN_BOX"]);
					$COMPONENT					= trim($arr_rs_goods[$j]["COMPONENT"]);
					$SIZE						= trim($arr_rs_goods[$j]["SIZE"]);
					$DESCRIPTION				= trim($arr_rs_goods[$j]["DESCRIPTION"]);
					$MANUFACTURER				= trim($arr_rs_goods[$j]["MANUFACTURER"]);
					$ORIGIN						= trim($arr_rs_goods[$j]["ORIGIN"]);

					if($RETAIL_PRICE <> "") 
						$RETAIL_PRICE = number_format($RETAIL_PRICE)." 원";
					
					if($ESTIMATE_PRICE <> "") 
						$ESTIMATE_PRICE = number_format($ESTIMATE_PRICE)." 원";

					$img_url	= getImage($conn, $GOODS_NO, "80", "80");
					$file_name = strtolower(substr($img_url, strrpos($img_url, '/') + 1));		
					$dst_file_path = $_SERVER["DOCUMENT_ROOT"]."/upload_data/goods_image/thumb_80/".$file_name;
					create_thumbnail($_SERVER["DOCUMENT_ROOT"].$img_url, $dst_file_path, 80, 80);

					//$objDrawing->getWidth(); - 80으로 수정 
					//$objDrawing->getHeight(); - 80으로 수정


					$objDrawing = new PHPExcel_Worksheet_Drawing();

					$objDrawing->setPath($dst_file_path);

					$k = $j + 4;

					$sheetIndex
						->setCellValue("A$k", iconv("EUC-KR", "UTF-8",$j + 1))
						->setCellValue("B$k", iconv("EUC-KR", "UTF-8",$GOODS_NAME." [".$GOODS_CODE."]"))
						->setCellValue("C$k", iconv("EUC-KR", "UTF-8",br2nl($COMPONENT)))
						->setCellValue("D$k", iconv("EUC-KR", "UTF-8",$DELIVERY_CNT_IN_BOX))
						->setCellValue("E$k", iconv("EUC-KR", "UTF-8",$RETAIL_PRICE))
						->setCellValue("F$k", iconv("EUC-KR", "UTF-8",$ESTIMATE_PRICE));

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

						// 상품 소개서 시트 (2 ~ )
						$objPHPExcel->createSheet();

						//1열
						$objPHPExcel->setActiveSheetIndex($p)->setCellValue('A1',iconv("EUC-KR", "UTF-8","상 품 소 개 서"));
						$objPHPExcel->setActiveSheetIndex($p)->mergeCells('A1:D1');
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('A1')->getFont()->setName('Gulim')->setSize(15)->setBold(true);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('A1')->getAlignment()
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$objPHPExcel->setActiveSheetIndex($p)->getRowDimension(1)->setRowHeight(40);
						
						//3열 
						$objPHPExcel->setActiveSheetIndex($p) 
							->setCellValue("A3", iconv("EUC-KR", "UTF-8","상품명(브랜드포함)"))
							->setCellValue("B3", iconv("EUC-KR", "UTF-8",$GOODS_NAME." [".$GOODS_CODE."]"))->mergeCells('B3:D3');

						$objPHPExcel->setActiveSheetIndex($p)->getStyle('A3')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('A3')->getAlignment()
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('B3')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('B3')->getAlignment()
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						
						//4-6열
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

						//27열
						$objPHPExcel->setActiveSheetIndex($p) 
							->setCellValue("A27", iconv("EUC-KR", "UTF-8","상품규격( cm )"))
							->setCellValue("B27", br2nl4Excel(iconv("EUC-KR", "UTF-8",$SIZE)))->mergeCells('B27:D27');

						$objPHPExcel->setActiveSheetIndex($p)->getStyle('A27')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('A27')->getAlignment()
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('B27')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('B27')->getAlignment()
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

						//28열
						$objPHPExcel->setActiveSheetIndex($p) 
							->setCellValue("A28", iconv("EUC-KR", "UTF-8","구성(세부내역)"))
							->setCellValue("B28", br2nl4Excel(iconv("EUC-KR", "UTF-8",$COMPONENT)))->mergeCells('B28:D28');

						$objPHPExcel->setActiveSheetIndex($p)->getRowDimension(28)->setRowHeight(getRowcount(br2nl4Excel($COMPONENT), 105) * 25 + 2.25);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('A28')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('A28')->getAlignment()->setWrapText(true)
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('B28')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('B28')->getAlignment()->setWrapText(true)
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

						//29열
						$objPHPExcel->setActiveSheetIndex($p) 
							->setCellValue("A29", iconv("EUC-KR", "UTF-8","용도 및 특징"))
							->setCellValue("B29", br2nl4Excel(iconv("EUC-KR", "UTF-8", $DESCRIPTION)))->mergeCells('B29:D29');

						$objPHPExcel->setActiveSheetIndex($p)->getRowDimension(29)->setRowHeight(getRowcount(br2nl4Excel($DESCRIPTION), 105) * 25 + 2.25);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('A29')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('A29')->getAlignment()->setWrapText(true)
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('B29')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('B29')->getAlignment()->setWrapText(true)
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
						

						//30열
						$objPHPExcel->setActiveSheetIndex($p) 
							->setCellValue("A30", iconv("EUC-KR", "UTF-8","제안가(VAT포함)"))
							->setCellValue("B30", iconv("EUC-KR", "UTF-8",$ESTIMATE_PRICE))->mergeCells('B30:D30');

						$objPHPExcel->setActiveSheetIndex($p)->getStyle('A30')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('A30')->getAlignment()
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('B30')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
						$objPHPExcel->setActiveSheetIndex($p)->getStyle('B30')->getAlignment()
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

						//31열
						$objPHPExcel->setActiveSheetIndex($p) 
							->setCellValue("A31", iconv("EUC-KR", "UTF-8","기프트넷단가"))
							->setCellValue("B31", iconv("EUC-KR", "UTF-8",$RETAIL_PRICE))
							->setCellValue("C31", iconv("EUC-KR", "UTF-8","박스입수"))
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

						//32열
						$objPHPExcel->setActiveSheetIndex($p) 
							->setCellValue("A32", iconv("EUC-KR", "UTF-8","제조원"))
							->setCellValue("B32", iconv("EUC-KR", "UTF-8",$MANUFACTURER))
							->setCellValue("C32", iconv("EUC-KR", "UTF-8","원산지"))
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

						$objPHPExcel->setActiveSheetIndex($p)->setTitle(iconv("EUC-KR", "UTF-8", ($j + 1)."번"));

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
	} else { 
		//1열
		$sheetIndex->setCellValue('A1',iconv("EUC-KR", "UTF-8","제 안 서"));
		$sheetIndex->mergeCells('A1:F1');
		$sheetIndex->getStyle('A1')->getFont()->setName('Gulim')->setSize(15)->setBold(true);
		$sheetIndex->getStyle('A1')->getAlignment()
		->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$sheetIndex->getRowDimension(1)->setRowHeight(40);


		//3열 (번호	상품명	구성	박스단위	기프트넷단가 	제안가	이미지)

		$sheetIndex ->setCellValue("A3", iconv("EUC-KR", "UTF-8","번호"))
					->setCellValue("B3", iconv("EUC-KR", "UTF-8","상품명"))
					->setCellValue("C3", iconv("EUC-KR", "UTF-8","구성"))
					->setCellValue("D3", iconv("EUC-KR", "UTF-8","박스단위"))
					->setCellValue("E3", iconv("EUC-KR", "UTF-8","제안가"))
					->setCellValue("F3", iconv("EUC-KR", "UTF-8","이미지"));

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
				$ESTIMATE_PRICE				= trim($arr_rs_goods[$j]["ESTIMATE_PRICE"]);
				$DELIVERY_CNT_IN_BOX		= trim($arr_rs_goods[$j]["DELIVERY_CNT_IN_BOX"]);
				$COMPONENT					= trim($arr_rs_goods[$j]["COMPONENT"]);
				$SIZE						= trim($arr_rs_goods[$j]["SIZE"]);
				$DESCRIPTION				= trim($arr_rs_goods[$j]["DESCRIPTION"]);
				$MANUFACTURER				= trim($arr_rs_goods[$j]["MANUFACTURER"]);
				$ORIGIN						= trim($arr_rs_goods[$j]["ORIGIN"]);

				if($ESTIMATE_PRICE <> "") 
					$ESTIMATE_PRICE = number_format($ESTIMATE_PRICE)." 원";

				$img_url	= getImage($conn, $GOODS_NO, "80", "80");
				$file_name = strtolower(substr($img_url, strrpos($img_url, '/') + 1));		
				$dst_file_path = $_SERVER["DOCUMENT_ROOT"]."/upload_data/goods_image/thumb_80/".$file_name;
				create_thumbnail($_SERVER["DOCUMENT_ROOT"].$img_url, $dst_file_path, 80, 80);

				//$objDrawing->getWidth(); - 80으로 수정 
				//$objDrawing->getHeight(); - 80으로 수정


				$objDrawing = new PHPExcel_Worksheet_Drawing();

				$objDrawing->setPath($dst_file_path);

				$k = $j + 4;

				$sheetIndex
					->setCellValue("A$k", iconv("EUC-KR", "UTF-8",$j + 1))
					->setCellValue("B$k", iconv("EUC-KR", "UTF-8",$GOODS_NAME." [".$GOODS_CODE."]"))
					->setCellValue("C$k", iconv("EUC-KR", "UTF-8",br2nl($COMPONENT)))
					->setCellValue("D$k", iconv("EUC-KR", "UTF-8",$DELIVERY_CNT_IN_BOX))
					->setCellValue("E$k", iconv("EUC-KR", "UTF-8",$ESTIMATE_PRICE));

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

					// 상품 소개서 시트 (2 ~ )
					$objPHPExcel->createSheet();

					//1열
					$objPHPExcel->setActiveSheetIndex($p)->setCellValue('A1',iconv("EUC-KR", "UTF-8","상 품 소 개 서"));
					$objPHPExcel->setActiveSheetIndex($p)->mergeCells('A1:D1');
					$objPHPExcel->setActiveSheetIndex($p)->getStyle('A1')->getFont()->setName('Gulim')->setSize(15)->setBold(true);
					$objPHPExcel->setActiveSheetIndex($p)->getStyle('A1')->getAlignment()
					->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$objPHPExcel->setActiveSheetIndex($p)->getRowDimension(1)->setRowHeight(40);
					
					//3열 
					$objPHPExcel->setActiveSheetIndex($p) 
						->setCellValue("A3", iconv("EUC-KR", "UTF-8","상품명(브랜드포함)"))
						->setCellValue("B3", iconv("EUC-KR", "UTF-8",$GOODS_NAME." [".$GOODS_CODE."]"))->mergeCells('B3:D3');

					$objPHPExcel->setActiveSheetIndex($p)->getStyle('A3')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
					$objPHPExcel->setActiveSheetIndex($p)->getStyle('A3')->getAlignment()
					->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$objPHPExcel->setActiveSheetIndex($p)->getStyle('B3')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
					$objPHPExcel->setActiveSheetIndex($p)->getStyle('B3')->getAlignment()
					->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					
					//4-6열
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

					//27열
					$objPHPExcel->setActiveSheetIndex($p) 
						->setCellValue("A27", iconv("EUC-KR", "UTF-8","상품규격( cm )"))
						->setCellValue("B27", br2nl4Excel(iconv("EUC-KR", "UTF-8",$SIZE)))->mergeCells('B27:D27');

					$objPHPExcel->setActiveSheetIndex($p)->getStyle('A27')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
					$objPHPExcel->setActiveSheetIndex($p)->getStyle('A27')->getAlignment()
					->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$objPHPExcel->setActiveSheetIndex($p)->getStyle('B27')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
					$objPHPExcel->setActiveSheetIndex($p)->getStyle('B27')->getAlignment()
					->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

					//28열
					$objPHPExcel->setActiveSheetIndex($p) 
						->setCellValue("A28", iconv("EUC-KR", "UTF-8","구성(세부내역)"))
						->setCellValue("B28", br2nl4Excel(iconv("EUC-KR", "UTF-8",$COMPONENT)))->mergeCells('B28:D28');

					$objPHPExcel->setActiveSheetIndex($p)->getRowDimension(28)->setRowHeight(getRowcount(br2nl4Excel($COMPONENT), 105) * 25 + 2.25);
					$objPHPExcel->setActiveSheetIndex($p)->getStyle('A28')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
					$objPHPExcel->setActiveSheetIndex($p)->getStyle('A28')->getAlignment()->setWrapText(true)
					->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$objPHPExcel->setActiveSheetIndex($p)->getStyle('B28')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
					$objPHPExcel->setActiveSheetIndex($p)->getStyle('B28')->getAlignment()->setWrapText(true)
					->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

					//29열
					$objPHPExcel->setActiveSheetIndex($p) 
						->setCellValue("A29", iconv("EUC-KR", "UTF-8","용도 및 특징"))
						->setCellValue("B29", br2nl4Excel(iconv("EUC-KR", "UTF-8", $DESCRIPTION)))->mergeCells('B29:D29');

					$objPHPExcel->setActiveSheetIndex($p)->getRowDimension(29)->setRowHeight(getRowcount(br2nl4Excel($DESCRIPTION), 105) * 25 + 2.25);
					$objPHPExcel->setActiveSheetIndex($p)->getStyle('A29')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
					$objPHPExcel->setActiveSheetIndex($p)->getStyle('A29')->getAlignment()->setWrapText(true)
					->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$objPHPExcel->setActiveSheetIndex($p)->getStyle('B29')->getFont()->setName('Gulim')->setSize(9)->setBold(true);
					$objPHPExcel->setActiveSheetIndex($p)->getStyle('B29')->getAlignment()->setWrapText(true)
					->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					

					//30열
					$objPHPExcel->setActiveSheetIndex($p) 
						->setCellValue("A30", iconv("EUC-KR", "UTF-8","제안가(VAT포함)"))
						->setCellValue("B30", iconv("EUC-KR", "UTF-8",$ESTIMATE_PRICE))
						->setCellValue("C30", iconv("EUC-KR", "UTF-8","박스입수"))
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

					//32열
					$objPHPExcel->setActiveSheetIndex($p) 
						->setCellValue("A31", iconv("EUC-KR", "UTF-8","제조원"))
						->setCellValue("B31", iconv("EUC-KR", "UTF-8",$MANUFACTURER))
						->setCellValue("C31", iconv("EUC-KR", "UTF-8","원산지"))
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

					$objPHPExcel->setActiveSheetIndex($p)->setTitle(iconv("EUC-KR", "UTF-8", ($j + 1)."번"));

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
	// Rename sheet
	$sheetIndex->setTitle(iconv("EUC-KR", "UTF-8",'제안서'));

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	//$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(100);

	// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
	$filename = "제안서-".date("Ymd");

	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment;filename=".$filename.".xls");
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->setUseDiskCaching(true);
	$objWriter->save('php://output');

	mysql_close($conn);
	exit;


?>
				
