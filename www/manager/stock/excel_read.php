<?
	error_reporting(E_ALL ^ E_NOTICE);

	require_once "../../_PHPExcel/Classes/PHPExcel.php";
	$objPHPExcel = new PHPExcel();
	require_once "../../_PHPExcel/Classes/PHPExcel/IOFactory.php";
	$filename = '../../upload_data/temp_stock/20150731.xlsx'; 
	
	try {
		
		$objReader = PHPExcel_IOFactory::createReaderForFile($filename);
		$objReader->setReadDataOnly(true);
		$objExcel = $objReader->load($filename);
		$objExcel->setActiveSheetIndex(0);
		$objWorksheet = $objExcel->getActiveSheet();

		$rowIterator = $objWorksheet->getRowIterator();

		foreach ($rowIterator as $row) {
			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(false); 
		}

		$maxRow = $objWorksheet->getHighestRow();


		for ($i = 0 ; $i <= $maxRow ; $i++) {

			$name = $objWorksheet->getCell('A' . $i)->getValue();
			$addr1 = $objWorksheet->getCell('B' . $i)->getValue();
			$addr2 = $objWorksheet->getCell('C' . $i)->getValue();
			$addr3 = $objWorksheet->getCell('D' . $i)->getValue();
			$addr4 = $objWorksheet->getCell('E' . $i)->getValue();
			$reg_date = $objWorksheet->getCell('F' . $i)->getValue();
			$reg_date = PHPExcel_Style_NumberFormat::toFormattedString($reg_date, 'YYYY-MM-DD');
			
			echo iconv("UTF-8","EUC-KR",$addr2)."<br>";
		}

	} catch (exception $e) {
		echo '엑셀파일을 읽는도중 오류가 발생하였습니다.';
	}

?>