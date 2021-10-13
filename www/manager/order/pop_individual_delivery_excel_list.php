<?
ini_set('memory_limit',-1);
session_start();
?>
<?
#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "OD016"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

#====================================================================
# common_header Check Session
#====================================================================
	require "../../_common/common_header.php"; 

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/stock/stock.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/confirm/confirm.php";


	$arr_rs = listDeliveryIndividual($conn, $order_goods_no, "DESC");


	require_once "../../_PHPExcel/Classes/PHPExcel.php";

	$objPHPExcel = new PHPExcel();

 	$BStyle = array(
	  'borders' => array(
		'allborders' => array(
		  'style' => PHPExcel_Style_Border::BORDER_THIN
		)
	  )
	);

	// Rename sheet
	$objPHPExcel->getActiveSheet(0)->setTitle(iconv("EUC-KR", "UTF-8","개별주소지"));

	//개별주소지 출력
	$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A1", iconv("EUC-KR", "UTF-8","수령자"))
					->setCellValue("B1", iconv("EUC-KR", "UTF-8","연락처"))
					->setCellValue("C1", iconv("EUC-KR", "UTF-8","휴대폰번호"))
					->setCellValue("D1", iconv("EUC-KR", "UTF-8","주소"))
					->setCellValue("E1", iconv("EUC-KR", "UTF-8","송장상품명"))
					->setCellValue("F1", iconv("EUC-KR", "UTF-8","상품수량"))
					->setCellValue("G1", iconv("EUC-KR", "UTF-8","배송메모"))
					->setCellValue("H1", iconv("EUC-KR", "UTF-8","배송방식"))
					->setCellValue("I1", iconv("EUC-KR", "UTF-8","송장수량"))
					->setCellValue("J1", iconv("EUC-KR", "UTF-8","등록자"))
					->setCellValue("K1", iconv("EUC-KR", "UTF-8","등록일시"))
					->setCellValue("L1", iconv("EUC-KR", "UTF-8","완료처리"))
					->setCellValue("M1", iconv("EUC-KR", "UTF-8","사용여부"));

	//송장내역 출력
	
	$objPHPExcel->createSheet();
		$objPHPExcel->setActiveSheetIndex(1)
			->setTitle(iconv("EUC-KR", "UTF-8", "송장내역"))
			->setCellValue("A1", iconv("EUC-KR", "UTF-8","송장번호"))
			->setCellValue("B1", iconv("EUC-KR", "UTF-8","택배사"))
			->setCellValue("C1", iconv("EUC-KR", "UTF-8","송장명"))
			->setCellValue("D1", iconv("EUC-KR", "UTF-8","수령자"))
			->setCellValue("E1", iconv("EUC-KR", "UTF-8","수령자전화"))
			->setCellValue("F1", iconv("EUC-KR", "UTF-8","수령자핸드폰"))
			->setCellValue("G1", iconv("EUC-KR", "UTF-8","수령자주소"));

	$o = 1;
	if (sizeof($arr_rs) > 0) {

		for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

			$INDIVIDUAL_NO			= trim($arr_rs[$j]["INDIVIDUAL_NO"]);
			$R_ZIPCODE			    = trim($arr_rs[$j]["R_ZIPCODE"]); 
			$R_ADDR1 				= trim($arr_rs[$j]["R_ADDR1"]);
			$R_MEM_NM				= trim($arr_rs[$j]["R_MEM_NM"]);
			$R_PHONE				= trim($arr_rs[$j]["R_PHONE"]); 
			$R_HPHONE				= trim($arr_rs[$j]["R_HPHONE"]); 
			$GOODS_DELIVERY_NAME	= trim($arr_rs[$j]["GOODS_DELIVERY_NAME"]); 
			$SUB_QTY				= trim($arr_rs[$j]["SUB_QTY"]);
			$MEMO					= trim($arr_rs[$j]["MEMO"]);
			$DELIVERY_TYPE			= trim($arr_rs[$j]["DELIVERY_TYPE"]);
			$IS_DELIVERED			= trim($arr_rs[$j]["IS_DELIVERED"]);
			$USE_TF					= trim($arr_rs[$j]["USE_TF"]);

			$REG_DATE				= date("n월j일H시i분", strtotime(trim($arr_rs[$j]["REG_DATE"])));

			$REG_ADM				= trim($arr_rs[$j]["REG_ADM"]);
			$REG_ADM = getAdminName($conn, $REG_ADM);

			$DELIVERY_PAPER_QTY = countOrderDeliveryPaper($conn, $order_goods_no, $INDIVIDUAL_NO);

			if($IS_DELIVERED == "Y") { 
				$DELIVERY_DATE = date("n월j일H시i분", strtotime(trim($arr_rs[$j]["DELIVERY_DATE"])));

			} else { 
				$DELIVERY_DATE = "배송전";
			}

			$k = $j+2;

			$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A$k", iconv("EUC-KR", "UTF-8", $R_MEM_NM))
							->setCellValue("B$k", iconv("EUC-KR", "UTF-8", $R_PHONE))
							->setCellValue("C$k", iconv("EUC-KR", "UTF-8", $R_HPHONE))
							->setCellValue("D$k", iconv("EUC-KR", "UTF-8", $R_ADDR1))
							->setCellValue("E$k", iconv("EUC-KR", "UTF-8", $GOODS_DELIVERY_NAME))
							->setCellValue("F$k", iconv("EUC-KR", "UTF-8", $SUB_QTY))
							->setCellValue("G$k", iconv("EUC-KR", "UTF-8", $MEMO))
							->setCellValue("H$k", iconv("EUC-KR", "UTF-8", getDcodeName($conn,"DELIVERY_TYPE",$DELIVERY_TYPE)))
							->setCellValue("I$k", iconv("EUC-KR", "UTF-8", $DELIVERY_PAPER_QTY))
							->setCellValue("J$k", iconv("EUC-KR", "UTF-8", $REG_ADM))
							->setCellValue("K$k", iconv("EUC-KR", "UTF-8", $REG_DATE))
							->setCellValue("L$k", iconv("EUC-KR", "UTF-8", $DELIVERY_DATE))
							->setCellValue("M$k", iconv("EUC-KR", "UTF-8", ($USE_TF == 'Y' ? "사용함" : "사용안함")));
	

			
			$arr_order_rs = listOrderDeliveryPaper($conn, $order_goods_no, $INDIVIDUAL_NO);
			if(sizeof($arr_order_rs) > 0) { 

				for($i = 0; $i < sizeof($arr_order_rs); $i ++) { 

					$rs_order_goods_delivery_no	= trim($arr_order_rs[$i]["ORDER_GOODS_DELIVERY_NO"]);
					$rs_delivery_seq	        = trim($arr_order_rs[$i]["DELIVERY_SEQ"]); 
					$rs_delivery_no 		    = trim($arr_order_rs[$i]["DELIVERY_NO"]);
					$rs_delivery_cp				= trim($arr_order_rs[$i]["DELIVERY_CP"]);
					$rs_order_nm		        = trim($arr_order_rs[$i]["ORDER_NM"]); 
					$rs_order_phone		        = trim($arr_order_rs[$i]["ORDER_PHONE"]);
					$rs_order_manager_nm	    = trim($arr_order_rs[$i]["ORDER_MANAGER_NM"]);
					$rs_order_manager_phone		= trim($arr_order_rs[$i]["ORDER_MANAGER_PHONE"]);
					$rs_receiver_nm		        = trim($arr_order_rs[$i]["RECEIVER_NM"]); 
					$rs_receiver_phone		    = trim($arr_order_rs[$i]["RECEIVER_PHONE"]);
					$rs_receiver_hphone		    = trim($arr_order_rs[$i]["RECEIVER_HPHONE"]);
					$rs_receiver_addr			= trim($arr_order_rs[$i]["RECEIVER_ADDR"]); 
					$rs_goods_delivery_name	    = trim($arr_order_rs[$i]["GOODS_DELIVERY_NAME"]); 
					$rs_memo				    = trim($arr_order_rs[$i]["MEMO"]); 
					$rs_delivery_fee			= trim($arr_order_rs[$i]["DELIVERY_FEE"]); 
					$rs_delivery_fee_code		= trim($arr_order_rs[$i]["DELIVERY_FEE_CODE"]); 
					$rs_delivery_claim_code		= trim($arr_order_rs[$i]["DELIVERY_CLAIM_CODE"]); 
					$rs_delivery_date           = trim($arr_order_rs[$i]["DELIVERY_DATE"]); 
					$rs_use_tf					= trim($arr_order_rs[$i]["USE_TF"]); 

					if($rs_use_tf == "N")
						continue;

					$o = $o + 1;

					$objPHPExcel->setActiveSheetIndex(1)
							->setCellValue("A$o", iconv("EUC-KR", "UTF-8", $rs_delivery_no))
							->setCellValue("B$o", iconv("EUC-KR", "UTF-8", $rs_delivery_cp))
							->setCellValue("C$o", iconv("EUC-KR", "UTF-8", $rs_goods_delivery_name))
							->setCellValue("D$o", iconv("EUC-KR", "UTF-8", $rs_receiver_nm))
							->setCellValue("E$o", iconv("EUC-KR", "UTF-8", $rs_receiver_phone))
							->setCellValue("F$o", iconv("EUC-KR", "UTF-8", $rs_receiver_hphone))
							->setCellValue("G$o", iconv("EUC-KR", "UTF-8", $rs_receiver_addr));
				}
			}
			//$o = $o + 1;

		}
	}

	$objPHPExcel->setActiveSheetIndex(0)->getStyle("A1:M$k")->applyFromArray($BStyle);
	$objPHPExcel->setActiveSheetIndex(0)->getStyle("A1:M1")->getFont()->setSize(10)->setBold(true);
	$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(12);
	$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(12);
	$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(12);
	$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(20);
	$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(30);

	$objPHPExcel->setActiveSheetIndex(1)->getStyle("A1:G$o")->applyFromArray($BStyle);
	$objPHPExcel->setActiveSheetIndex(1)->getStyle("A1:G1")->getFont()->setSize(10)->setBold(true);
	$objPHPExcel->setActiveSheetIndex(1)->getColumnDimension('A')->setWidth(12);
	$objPHPExcel->setActiveSheetIndex(1)->getColumnDimension('B')->setWidth(10);
	$objPHPExcel->setActiveSheetIndex(1)->getColumnDimension('C')->setWidth(40);
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	//$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(100);

	// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
	$filename = "개별택배등록지-".date("Ymd");

	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment;filename=".$filename.".xls");
	header('Cache-Control: max-age=0');
 
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

	mysql_close($conn);
	exit;

?>