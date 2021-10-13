<?
ini_set('memory_limit',-1);
session_start();

#====================================================================
# DB Include, DB Connection
#====================================================================
require "../_classes/com/db/DBUtil.php";
$conn = db_connection("w");

#====================================================================
# Confirm right
#====================================================================
$menu_right = "OD003"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

#====================================================================
# common_header Check Session
#====================================================================
include "../_common/common_header.php";

#====================================================================
# common function, login_function
#====================================================================
require "../_common/config.php";
require "../_classes/com/util/Util.php";
require "../_classes/com/etc/etc.php";
require "../_classes/biz/confirm/confirm.php";
require "../_classes/biz/company/company.php";
require "../_classes/biz/order/order.php";
require "../_classes/biz/goods/goods.php";
require "../_classes/biz/member/member.php";
require "../_classes/biz/admin/admin.php";
require "../_classes/biz/payment/payment.php";

#====================================================================
# Request Parameter
#====================================================================
$mode	= trim($_POST["mode"]);

if ($this_date == "") 
	$this_date = date("Y-m-d",strtotime("0 month"));
#====================================================================
# DML Process
#====================================================================
function time_convert_EXCEL_to_PHP($time){
	$t = ($time- 25569) * 86400-60*60*9;
	$t = round($t*10)/10;
	return $t;
}

function checkCpOrderNo($db, $cp_order_no){
	$query =   "SELECT 
					CL.CL_NO
				FROM
					TBL_ORDER_GOODS OG
						JOIN
					TBL_COMPANY_LEDGER CL ON OG.RESERVE_NO = CL.RESERVE_NO
				WHERE
					OG.CP_ORDER_NO = '$cp_order_no'
					AND CL.INOUT_TYPE = '매출'
					AND CL.USE_TF = 'Y'
					AND CL.DEL_TF = 'N'
	";
    // echo $query;
    $result = mysql_query($query,$db);
    $record = array();
    if ($result <> "") {
        for($i=0;$i < mysql_num_rows($result);$i++) {
            $record[$i] = sql_result_array($result,$i);
        }
    }
    return $record[0]["CL_NO"];
}

if($mode == "PREVIEW_MRO_CONFIRM"){
	require_once "../_PHPExcel/Classes/PHPExcel.php";
	require_once "../_PHPExcel/Classes/PHPExcel/IOFactory.php";

	$s_adm_no = $_POST["s_adm_no"];

	error_reporting(E_ALL ^ E_NOTICE);
	$objPHPExcel 	= new PHPExcel();
	$savedir1 		= $g_physical_path."upload_data/temp_goods";
	$file_nm		= upload($_FILES['file'], $savedir1, 10000 , array('xls','xlsx'));
	$filename 		= '../upload_data/temp_goods/'.$file_nm;
	$objReader 		= PHPExcel_IOFactory::createReaderForFile($filename);
	$objReader 		-> setReadDataOnly(true);
	$objExcel 		= $objReader->load($filename);
	$objExcel 		-> setActiveSheetIndex(0);
	$objWorksheet 	= $objExcel->getActiveSheet();
	$rowIterator 	= $objWorksheet->getRowIterator();

	foreach ($rowIterator as $row) {
		$cellIterator = $row->getCellIterator();
		$cellIterator->setIterateOnlyExistingCells(false); 
	}

	$maxRow = $objWorksheet->getHighestRow()-1;
	$arr_cl_no = array();
	for ($i = 3 ; $i <= $maxRow ; $i++) {
		//업체 주문번호 생성에 필요한 항목(주문일,주문번호,주문순번)만 사용
		$order_date				= iconv("UTF-8", "EUC-KR", trim(date('Ymd',time_convert_EXCEL_to_PHP($objWorksheet->getCell('B' . $i)->getValue()))));
		$order_no				= iconv("UTF-8", "EUC-KR", trim($objWorksheet->getCell('C' . $i)->getValue()));
		$order_seq				= iconv("UTF-8", "EUC-KR", trim($objWorksheet->getCell('D' . $i)->getValue()));
		
		//업체 주문번호 생성
		$cp_order_no 			= $order_date."_".$order_no."_".$order_seq;

		// 미사용 항목
		// $row_no					= iconv("UTF-8", "EUC-KR", trim($objWorksheet->getCell('A' . $i)->getValue()));
		// $order_mem_nm			= iconv("UTF-8", "EUC-KR", trim($objWorksheet->getCell('E' . $i)->getValue()));
		// $reciever_nm			= iconv("UTF-8", "EUC-KR", trim($objWorksheet->getCell('F' . $i)->getValue()));
		// $goods_cd				= iconv("UTF-8", "EUC-KR", trim($objWorksheet->getCell('G' . $i)->getValue()));
		// $goods_nm				= iconv("UTF-8", "EUC-KR", trim($objWorksheet->getCell('H' . $i)->getValue()));
		// $option_nm				= iconv("UTF-8", "EUC-KR", trim($objWorksheet->getCell('I' . $i)->getValue()));
		// $specification			= iconv("UTF-8", "EUC-KR", trim($objWorksheet->getCell('J' . $i)->getValue()));
		// $origin					= iconv("UTF-8", "EUC-KR", trim($objWorksheet->getCell('K' . $i)->getValue()));
		// $vender_nm				= iconv("UTF-8", "EUC-KR", trim($objWorksheet->getCell('L' . $i)->getValue()));
		// $qty					= iconv("UTF-8", "EUC-KR", trim($objWorksheet->getCell('M' . $i)->getValue()));
		// $order_price			= iconv("UTF-8", "EUC-KR", trim($objWorksheet->getCell('N' . $i)->getValue()));
		// $confirm_price			= iconv("UTF-8", "EUC-KR", trim($objWorksheet->getCell('O' . $i)->getValue()));
		// $supply_price			= iconv("UTF-8", "EUC-KR", trim($objWorksheet->getCell('P' . $i)->getValue()));
		// $sur_tax_price			= iconv("UTF-8", "EUC-KR", trim($objWorksheet->getCell('Q' . $i)->getValue()));
		// $total_price			= iconv("UTF-8", "EUC-KR", trim($objWorksheet->getCell('R' . $i)->getValue()));
		// $confirm_susu_rate		= iconv("UTF-8", "EUC-KR", trim($objWorksheet->getCell('S' . $i)->getValue()));
		// $prepaid_interest		= iconv("UTF-8", "EUC-KR", trim($objWorksheet->getCell('T' . $i)->getValue()));
		// $sur_tax_option			= iconv("UTF-8", "EUC-KR", trim($objWorksheet->getCell('U' . $i)->getValue()));
		// $confirm_state			= iconv("UTF-8", "EUC-KR", trim($objWorksheet->getCell('V' . $i)->getValue()));
		// $confirm_classification	= iconv("UTF-8", "EUC-KR", trim($objWorksheet->getCell('W' . $i)->getValue()));
		// $delivery_end_date		= iconv("UTF-8", "EUC-KR", trim(date('Y-m-d',time_convert_EXCEL_to_PHP($objWorksheet->getCell('X' . $i)->getValue()))));

		array_push($arr_cl_no, array("cl_no" => checkCpOrderNo($conn, $cp_order_no)));
	}
	echo json_encode($arr_cl_no);
}

if($mode == "APPLY_MRO_CONFIRM"){
	require_once "../_PHPExcel/Classes/PHPExcel.php";
	require_once "../_PHPExcel/Classes/PHPExcel/IOFactory.php";

	$s_adm_no = $_POST["s_adm_no"];
	$cf_type = "CF006";
	$cf_code = $this_date;
	$tax_confirm_tf = "Y";

	//엑셀 파일 읽어서 업체 주문번호 생성 
	error_reporting(E_ALL ^ E_NOTICE);
	$objPHPExcel 	= new PHPExcel();
	$savedir1 		= $g_physical_path."upload_data/temp_goods";
	$file_nm		= upload($_FILES['file'], $savedir1, 10000 , array('xls','xlsx'));
	$filename 		= '../upload_data/temp_goods/'.$file_nm;
	$objReader 		= PHPExcel_IOFactory::createReaderForFile($filename);
	$objReader 		-> setReadDataOnly(true);
	$objExcel 		= $objReader->load($filename);
	$objExcel 		-> setActiveSheetIndex(0);
	$objWorksheet 	= $objExcel->getActiveSheet();
	$rowIterator 	= $objWorksheet->getRowIterator();

	foreach ($rowIterator as $row) {
		$cellIterator = $row->getCellIterator();
		$cellIterator->setIterateOnlyExistingCells(false); 
	}

	$maxRow = $objWorksheet->getHighestRow()-1;
	$success = 0;
	for ($i = 3 ; $i <= $maxRow ; $i++) {
		$cl_no = null;
		
		//업체 주문번호 생성에 필요한 항목(주문일,주문번호,주문순번)만 사용
		$order_date				= iconv("UTF-8", "EUC-KR", trim(date('Ymd',time_convert_EXCEL_to_PHP($objWorksheet->getCell('B' . $i)->getValue()))));
		$order_no				= iconv("UTF-8", "EUC-KR", trim($objWorksheet->getCell('C' . $i)->getValue()));
		$order_seq				= iconv("UTF-8", "EUC-KR", trim($objWorksheet->getCell('D' . $i)->getValue()));
		
		//업체 주문번호 생성
		$cp_order_no 			= $order_date."_".$order_no."_".$order_seq;

		//cl_no 조회
		$cl_no = checkCpOrderNo($conn, $cp_order_no);

		//발행
		if($cl_no != ""){
			$result = updateTaxInvoiceTF($conn, $cl_no, $cf_type, $cf_code, $tax_confirm_tf, $s_adm_no);

			if($result && $cf_code <> '' && $tax_confirm_tf == "Y") {
				updateTaxInvoceExtraInfo($conn, $cl_no, $cf_code);
			}
			//성공한 정산 개수 카운트
			if($result){
				$success++;
			}
		}
	}
	echo $success;
}

mysql_close($conn);
?>