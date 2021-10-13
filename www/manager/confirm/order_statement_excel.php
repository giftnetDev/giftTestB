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
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/company/company.php";


#====================================================================
# Request Parameter
#====================================================================

	$reserve_no				= trim($reserve_no);
	$op_cp_no				= trim($op_cp_no);



	// print_r($_POST);
	// reserve_no 	=> 20210706EN00007 
	// print_type 	=> 2
	// print_date	=> 2021-07-06
	// op_cp_no	=> 1

	// exit;

#===============================================================
# Get Search list count
#===============================================================


?>
<?
	function listManagerOrderGoodsException($db, $reserve_no, $mem_no, $use_tf, $del_tf) {

		$query = "SELECT C.ORDER_GOODS_NO, C.CLAIM_ORDER_GOODS_NO, C.RESERVE_NO, C.BUY_CP_NO, C.MEM_NO, C.ORDER_SEQ, 
						 C.GOODS_NO, C.GOODS_CODE, C.GOODS_NAME, C.GOODS_SUB_NAME, 
						 C.QTY, C.OPT_STICKER_NO, C.OPT_STICKER_MSG, C.OPT_OUTBOX_TF, C.DELIVERY_CNT_IN_BOX, C.OPT_WRAP_NO, C.OPT_PRINT_MSG, C.OPT_OUTSTOCK_DATE, C.OPT_MEMO, C.OPT_REQUEST_MEMO, C.OPT_SUPPORT_MEMO, 
						 C.DELIVERY_TYPE, C.CATE_01, C.CATE_02,
						 C.CATE_03, C.CATE_04, C.PRICE, C.BUY_PRICE, C.SALE_PRICE, C.EXTRA_PRICE, C.DELIVERY_PRICE, C.SA_DELIVERY_PRICE, C.DISCOUNT_PRICE, C.USE_TF, C.DEL_TF, 
						 C.REG_ADM, C.REG_DATE, C.DEL_ADM, C.DEL_DATE, C.ORDER_CONFIRM_DATE,
						 G.FILE_NM_100, C.ORDER_DATE, C.FINISH_DATE, C.PAY_DATE, C.ORDER_STATE,
						 ((C.SALE_PRICE * C.QTY) + C.SA_DELIVERY_PRICE - C.DISCOUNT_PRICE) AS SUM_PRICE, 
						 (((C.SALE_PRICE * C.QTY) + C.SA_DELIVERY_PRICE - C.DISCOUNT_PRICE) - ((C.PRICE + C.EXTRA_PRICE) * C.QTY)) AS PLUS_PRICE, 
						 ROUND(((((C.SALE_PRICE * C.QTY) + C.SA_DELIVERY_PRICE - C.DISCOUNT_PRICE) - ((C.PRICE + C.EXTRA_PRICE) * C.QTY)) / ((C.SALE_PRICE * C.QTY) + C.SA_DELIVERY_PRICE - C.DISCOUNT_PRICE)) * 100,2) AS LEE,
						 C.DELIVERY_CP, C.DELIVERY_NO, C.CP_ORDER_NO, C.WORK_FLAG, C.WORK_QTY, C.WORK_START_DATE, C.WORK_END_DATE, 
						 C.SALE_CONFIRM_TF, C.SALE_CONFIRM_YMD,
						 G.CATE_04 AS GOODS_STATE, G.TAX_TF,
						 R.HAS_GOODS_REQUEST

							  FROM TBL_ORDER_GOODS C 
						 LEFT JOIN TBL_GOODS G ON C.GOODS_NO = G.GOODS_NO
						 LEFT JOIN (SELECT ORDER_GOODS_NO, COUNT(*) > 0 AS HAS_GOODS_REQUEST 
						              FROM TBL_GOODS_REQUEST_GOODS 
				                     WHERE DEL_TF = 'N' AND CANCEL_TF = 'N'
								  GROUP BY ORDER_GOODS_NO) R ON C.ORDER_GOODS_NO = R.ORDER_GOODS_NO
							 WHERE G.DEL_TF = 'N' 
							 AND C.CATE_01 !='추가'
							     ";
		
		// 2017-03-14 상품에서 사용안함으로 변경(코드 잘못 물고 주문 들어간 경우등)되었을 시 주문 자체의 주문들이 사라지는 관계로 일단 보이게 수정
		//G.USE_TF= 'Y' AND
		
		if ($reserve_no <> "") {
			$query .= " AND C.RESERVE_NO = '".$reserve_no."' ";
		}

		if ($mem_no <> "") {
			$query .= " AND C.MEM_NO = '".$mem_no."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND C.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND C.DEL_TF = '".$del_tf."' ";
		}

		$query .= " ORDER BY C.ORDER_GOODS_NO DESC ";

		// echo $query."<br/>"."<br/>";
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
	//줄바꿈 문자
	$lfcr = chr(10);
	
	if($reserve_no == "") exit; 
		
	$arr_order_rs = selectOrder($conn, $reserve_no);

	$rs_cp_no						= trim($arr_order_rs[0]["CP_NO"]); 
	$rs_order_no					= trim($arr_order_rs[0]["ORDER_NO"]); 
	$rs_reserve_no				    = trim($arr_order_rs[0]["RESERVE_NO"]); 
	$rs_o_mem_nm					= trim(setStringToDB($arr_order_rs[0]["O_MEM_NM"]));
	$rs_o_phone						= trim($arr_order_rs[0]["O_PHONE"]);
	$rs_r_mem_nm					= trim($arr_order_rs[0]["R_MEM_NM"]); 
	$rs_r_zipcode					= trim($arr_order_rs[0]["R_ZIPCODE"]); 
	$rs_r_addr1						= trim($arr_order_rs[0]["R_ADDR1"]); 
	$rs_r_phone						= trim($arr_order_rs[0]["R_PHONE"]); 
	$rs_r_hphone					= trim($arr_order_rs[0]["R_HPHONE"]); 
	$rs_order_date					= trim($arr_order_rs[0]["ORDER_DATE"]); 
	$rs_opt_manager_no				= trim($arr_order_rs[0]["OPT_MANAGER_NO"]);
	$rs_reg_adm						= trim($arr_order_rs[0]["REG_ADM"]);
	
	// echo "
	// 	rs_cp_no : $rs_cp_no
	// 	,rs_order_no : $rs_order_no
	// 	,rs_reserve_no : $rs_reserve_no
	// 	,rs_o_mem_nm : $rs_o_mem_nm
	// 	,rs_o_phone : $rs_o_phone
	// 	,rs_r_mem_nm : $rs_r_mem_nm
	// 	,rs_r_zipcode : $rs_r_zipcode
	// 	,rs_r_addr1 : $rs_r_addr1
	// 	,rs_r_phone : $rs_r_phone
	// 	,rs_r_hphone : $rs_r_hphone
	// 	,rs_order_date : $rs_order_date
	// 	,rs_opt_manager_no : $rs_opt_manager_no
	// 	,rs_reg_adm : $rs_reg_adm
	// ";

	$rs_order_date		= date("Y년 n월 j일", strtotime($rs_order_date));	
	$rs_opt_manager_no  = getAdminName($conn, $rs_opt_manager_no);
	$rs_reg_adm			= getAdminName($conn, $rs_reg_adm);
	
	$arr_cp = selectCompany($conn, $rs_cp_no);
	$IS_MALL		 = $arr_cp[0]["IS_MALL"];
	$SENDER_NM      = $arr_cp[0]["CP_NM"]." ".$arr_cp[0]["CP_NM2"];

	if($IS_MALL == "Y") { 

		//수령자 아니고 주문자 정보로 변경 2017-07-03
		$SENDER_NM      = $rs_o_mem_nm;
		$SENDER_ADDR	= "";
		$SENDER_CP_PHONE= $rs_o_phone;

	} else { 
		$SENDER_NM      = $arr_cp[0]["CP_NM"]." ".$arr_cp[0]["CP_NM2"];
		$SENDER_ADDR	= $arr_cp[0]["CP_ADDR"];
		$SENDER_CP_PHONE= $arr_cp[0]["CP_PHONE"];
	}
	
	/*
	$arr_op_cp = getOperatingCompany($conn, $op_cp_no);

	$OP_CP_NM		= $arr_op_cp[0]["CP_NM"]." ".$arr_op_cp[0]["CP_NM2"];
	$CP_PHONE		= $arr_op_cp[0]["CP_PHONE"];
	$CP_FAX			= $arr_op_cp[0]["CP_FAX"];
	$BIZ_NO			= $arr_op_cp[0]["BIZ_NO"];
	$CP_ADDR		= $arr_op_cp[0]["CP_ADDR"];
	$UPTEA			= $arr_op_cp[0]["UPTEA"];
	$UPJONG			= $arr_op_cp[0]["UPJONG"];
	*/

	$arr_rs = listManagerOrderGoodsException($conn, $reserve_no, $mem_no, "Y", "N");

	$TOTAL_SALE_PRICE = 0;
	for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
	
		$ORDER_GOODS_NO				= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
		$ORDER_SEQ					= trim($arr_rs[$j]["ORDER_SEQ"]);
		$GOODS_NO					= trim($arr_rs[$j]["GOODS_NO"]);
		$GOODS_STATE				= trim($arr_rs[$j]["GOODS_STATE"]);
		$GOODS_CODE					= SetStringFromDB(trim($arr_rs[$j]["GOODS_CODE"]));
		$GOODS_NAME					= SetStringFromDB(trim($arr_rs[$j]["GOODS_NAME"]));
		$GOODS_SUB_NAME				= SetStringFromDB(trim($arr_rs[$j]["GOODS_SUB_NAME"]));
		//$QTY						= trim($arr_rs[$j]["QTY"]);
		$BUY_PRICE					= trim($arr_rs[$j]["BUY_PRICE"]);
		$SALE_PRICE					= trim($arr_rs[$j]["SALE_PRICE"]);
		$EXTRA_PRICE				= trim($arr_rs[$j]["EXTRA_PRICE"]);
		$DELIVERY_PRICE				= trim($arr_rs[$j]["DELIVERY_PRICE"]);
		$SA_DELIVERY_PRICE			= trim($arr_rs[$j]["SA_DELIVERY_PRICE"]);
		$DISCOUNT_PRICE				= trim($arr_rs[$j]["DISCOUNT_PRICE"]);
		$ORDER_STATE				= trim($arr_rs[$j]["ORDER_STATE"]);
		$CATE_04					= trim($arr_rs[$j]["CATE_04"]);


		$QTY = getRefundAbleQty_EstimateTransaction($conn, $reserve_no, $ORDER_GOODS_NO);

		//전체 취소 건 제외
		if($QTY == 0)
			continue;

		//교환건은 제외 2017-11-16
		if($CATE_04 <> '')
			continue;

		if($ORDER_STATE == "1" || $ORDER_STATE == "2" || $ORDER_STATE == "3") {

			$TOTAL_SALE_PRICE += $SALE_PRICE * $QTY - $DISCOUNT_PRICE;

		}
	
	}

	require_once "../../_PHPExcel/Classes/PHPExcel.php";

	// Cell caching to reduce memory usage.
	$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
	$cacheSettings = array( " memoryCacheSize " => "8MB");
	PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings); 

	$objPHPExcel = new PHPExcel();

	$sheetIndex = $objPHPExcel->setActiveSheetIndex(0);
	$w = 0.75;
	//$w = 3.75;
	/*
	$sheetIndex->getColumnDimension("A")->setWidth(1 + $w);
	$sheetIndex->getColumnDimension("B")->setWidth(14.89 + $w);
	$sheetIndex->getColumnDimension("C")->setWidth(20.33 + $w);
	$sheetIndex->getColumnDimension("D")->setWidth(9.78 + $w);
	$sheetIndex->getColumnDimension("E")->setWidth(14 + $w);
	$sheetIndex->getColumnDimension("F")->setWidth(13.22 + $w);
	$sheetIndex->getColumnDimension("G")->setWidth(16.11 + $w);
	$sheetIndex->getColumnDimension("H")->setWidth(1 + $w);

	$sheetIndex->getColumnDimension("A")->setWidth(1);
	$sheetIndex->getColumnDimension("B")->setWidth(12 + $w);
	$sheetIndex->getColumnDimension("C")->setWidth(24.33 + $w);
	$sheetIndex->getColumnDimension("D")->setWidth(7.78 + $w);
	$sheetIndex->getColumnDimension("E")->setWidth(12 + $w);
	$sheetIndex->getColumnDimension("F")->setWidth(12.22 + $w);
	$sheetIndex->getColumnDimension("G")->setWidth(20.11 + $w);
	$sheetIndex->getColumnDimension("H")->setWidth(1);
	*/

	$sheetIndex->getColumnDimension("A")->setWidth($w);
	$sheetIndex->getColumnDimension("B")->setWidth(13.43 + $w);
	$sheetIndex->getColumnDimension("C")->setWidth(19.57 + $w);
	$sheetIndex->getColumnDimension("D")->setWidth(7.86 + $w);
	$sheetIndex->getColumnDimension("E")->setWidth(12.86 + $w);
	$sheetIndex->getColumnDimension("F")->setWidth(15.14 + $w);
	$sheetIndex->getColumnDimension("G")->setWidth(14.86 + $w);
	$sheetIndex->getColumnDimension("H")->setWidth($w);
	

	$BStyle_font = array(
	  "font"  => array(
        "name"  => "Gulim")
	);

	$BStyle = array(
	  "borders" => array(
		"allborders" => array(
		  "style" => PHPExcel_Style_Border::BORDER_THIN
		)
	  )
	);

	$BStyle_outline = array(
	  'borders' => array(
		'outline' => array(
		  'style' => PHPExcel_Style_Border::BORDER_THICK
		)
	  )
	);

	$style = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );

	$style_left = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
        )
    );

	$style_right = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
        )
    );

	$background_color_style = array(
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'DFDFDF')
        )
	);

    $sheetIndex->getDefaultStyle()->applyFromArray($style);

	$o = 1;
	$sheetIndex->getRowDimension($o)->setRowHeight(27);
	$o = $o + 1;


	//2~3열
	$sheetIndex->setCellValue("B$o",iconv("EUC-KR", "UTF-8","주     문    서"));
	$sheetIndex->mergeCells("B$o:G".($o + 1));
	$sheetIndex->getStyle("B$o:G".($o + 1))->getFont()->setSize(20)->setBold(true);
	$sheetIndex->getRowDimension($o)->setRowHeight(13.50);
	$sheetIndex->getRowDimension(($o + 1))->setRowHeight(30.50);
	$o = $o + 2;



	//4열 
	$sheetIndex ->setCellValue("B$o", iconv("EUC-KR", "UTF-8","신청일자"))
				->setCellValue("C$o", iconv("EUC-KR", "UTF-8",$rs_order_date))
				->setCellValue("E$o", iconv("EUC-KR", "UTF-8", "담당"))
				->setCellValue("F$o", iconv("EUC-KR", "UTF-8", "영업) ".$rs_opt_manager_no." / 지원) ".$rs_reg_adm));
	$sheetIndex->mergeCells("C$o:D$o");
	$sheetIndex->mergeCells("F$o:G$o");
	$sheetIndex->getStyle("B$o")->getFont()->setSize(11)->setBold(true);
	$sheetIndex->getStyle("E$o")->getFont()->setSize(11)->setBold(true);
	$sheetIndex->getRowDimension($o)->setRowHeight(30);
	$o = $o + 1;

	//5열 //주문 업체	농협은행㈜서둔동지점(163)		주문자(성함)	박해원팀장님/031-201-8434	

	$sheetIndex ->setCellValue("B$o", iconv("EUC-KR", "UTF-8","주문 업체"))
				->setCellValue("C$o", iconv("EUC-KR", "UTF-8", $SENDER_NM))
				->setCellValue("E$o", iconv("EUC-KR", "UTF-8", "주문자(성함)"))
				->setCellValue("F$o", iconv("EUC-KR", "UTF-8", $rs_o_mem_nm."/".$rs_o_phone));
	$sheetIndex->mergeCells("C$o:D$o");
	$sheetIndex->mergeCells("F$o:G$o");
	$sheetIndex->getStyle("B$o")->getFont()->setSize(11)->setBold(true);
	$sheetIndex->getStyle("E$o")->getFont()->setSize(11)->setBold(true);
	$sheetIndex->getRowDimension($o)->setRowHeight(30);
	$o = $o + 1;

	//6열 //전 화 번 호	031-291-7477		 휴 대 폰 		

	$sheetIndex ->setCellValue("B$o", iconv("EUC-KR", "UTF-8","전 화 번 호"))
				->setCellValue("C$o", iconv("EUC-KR", "UTF-8",$SENDER_CP_PHONE))
				->setCellValue("E$o", iconv("EUC-KR", "UTF-8", "휴 대 폰"))
				->setCellValue("F$o", iconv("EUC-KR", "UTF-8", ""));
	$sheetIndex->mergeCells("C$o:D$o");
	$sheetIndex->mergeCells("F$o:G$o");
	$sheetIndex->getStyle("B$o")->getFont()->setSize(11)->setBold(true);
	$sheetIndex->getStyle("E$o")->getFont()->setSize(11)->setBold(true);
	$sheetIndex->getRowDimension($o)->setRowHeight(30);
	$o = $o + 1;

	//7열 //코드번호	품 목	수량	단가	합계	비고 		

	$sheetIndex ->setCellValue("B$o", iconv("EUC-KR", "UTF-8","코드번호"))
				->setCellValue("C$o", iconv("EUC-KR", "UTF-8","품 목"))
				->setCellValue("D$o", iconv("EUC-KR", "UTF-8","수량"))
				->setCellValue("E$o", iconv("EUC-KR", "UTF-8","단가"))
				->setCellValue("F$o", iconv("EUC-KR", "UTF-8","합계"))
				->setCellValue("G$o", iconv("EUC-KR", "UTF-8","비고"));
	$sheetIndex->getRowDimension($o)->setRowHeight(30);
	$o = $o + 1;

	$k  = $o;
	// ECHO "SIZEOF(arr_rs) : ".sizeof($arr_rs)."<br>";
	// exit;

	for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
	
		$ORDER_GOODS_NO				= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
		$ORDER_SEQ					= trim($arr_rs[$j]["ORDER_SEQ"]);
		$GOODS_NO					= trim($arr_rs[$j]["GOODS_NO"]);
		$GOODS_STATE				= trim($arr_rs[$j]["GOODS_STATE"]);
		$GOODS_CODE					= SetStringFromDB(trim($arr_rs[$j]["GOODS_CODE"]));
		$GOODS_NAME					= SetStringFromDB(trim($arr_rs[$j]["GOODS_NAME"]));
		$GOODS_SUB_NAME				= SetStringFromDB(trim($arr_rs[$j]["GOODS_SUB_NAME"]));
		//$QTY						= trim($arr_rs[$j]["QTY"]);
		$BUY_PRICE					= trim($arr_rs[$j]["BUY_PRICE"]);
		$SALE_PRICE					= trim($arr_rs[$j]["SALE_PRICE"]);
		$EXTRA_PRICE				= trim($arr_rs[$j]["EXTRA_PRICE"]);
		$DELIVERY_PRICE				= trim($arr_rs[$j]["DELIVERY_PRICE"]);
		$SA_DELIVERY_PRICE			= trim($arr_rs[$j]["SA_DELIVERY_PRICE"]);
		$DISCOUNT_PRICE				= trim($arr_rs[$j]["DISCOUNT_PRICE"]);
		$DELIVERY_TYPE				= trim($arr_rs[$j]["DELIVERY_TYPE"]);

		
		$CATE_01					= trim($arr_rs[$j]["CATE_01"]);
		$CATE_04					= trim($arr_rs[$j]["CATE_04"]);

		$ORDER_STATE				= trim($arr_rs[$j]["ORDER_STATE"]);
		$TAX_TF						= trim($arr_rs[$j]["TAX_TF"]);

		if($TAX_TF == "과세") 
			$STR_TAX_TF  = "";
		else
			$STR_TAX_TF  = "[비과세]".$lfcr;

		$OPT_STICKER_NO				= trim($arr_rs[$j]["OPT_STICKER_NO"]);
		$OPT_OUTBOX_TF				= trim($arr_rs[$j]["OPT_OUTBOX_TF"]);
		$OPT_WRAP_NO				= trim($arr_rs[$j]["OPT_WRAP_NO"]);
		$OPT_STICKER_MSG			= trim($arr_rs[$j]["OPT_STICKER_MSG"]);
		$OPT_PRINT_MSG				= trim($arr_rs[$j]["OPT_PRINT_MSG"]);
		$OPT_SUPPORT_MEMO			= trim($arr_rs[$j]["OPT_SUPPORT_MEMO"]);

		$option_str	= "";
		$option_str .= ($OPT_STICKER_NO <> "0" ? getGoodsName($conn, $OPT_STICKER_NO). "\n" : "");
		$option_str .= ($OPT_WRAP_NO <> "0" ? getGoodsName($conn, $OPT_WRAP_NO). "\n" : "");
		$option_str .= ($OPT_PRINT_MSG <> "" ? $OPT_PRINT_MSG. "\n" : "");
		$option_str .= ($OPT_SUPPORT_MEMO <> "" ? $OPT_SUPPORT_MEMO. " " : "");


		// echo $option_str."<br>";
		// exit;

		$QTY = getRefundAbleQty_EstimateTransaction($conn, $reserve_no, $ORDER_GOODS_NO);

		//전체 취소 건 제외
		if($QTY == 0) continue;

		//교환건은 제외 2017-11-16
		if($CATE_04 <> '')
			continue;

		
		if($ORDER_STATE == "1" || $ORDER_STATE == "2" || $ORDER_STATE == "3") {  
		
			// 자연을담다베이킹소다4종B세트 		 40 	 \5,000 		 \200,000 	부가세포함
			//10열 

			$sheetIndex ->setCellValue("B".$k, iconv("EUC-KR", "UTF-8", $GOODS_CODE))
						->setCellValue("C".$k, iconv("EUC-KR", "UTF-8", $STR_TAX_TF.($CATE_01 <> "" ? $CATE_01.") " : "").$GOODS_NAME." ".$GOODS_SUB_NAME))
						->setCellValue("D".$k, iconv("EUC-KR", "UTF-8", $QTY))
						->setCellValue("E".$k, iconv("EUC-KR", "UTF-8", $SALE_PRICE))
						->setCellValue("F".$k, iconv("EUC-KR", "UTF-8", $QTY * $SALE_PRICE))
						->setCellValue("G".$k, iconv("EUC-KR", "UTF-8", $option_str));
			$sheetIndex->getStyle("C$k")->getAlignment()->setWrapText(true);
			$sheetIndex->getStyle("G$k")->getAlignment()->setWrapText(true);
			$sheetIndex->getStyle("G$k")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$sheetIndex->getStyle("A$k:F$k")->getFont()->setSize(10);
			$sheetIndex->getStyle("C$k")->getFont()->setSize(8);
			$sheetIndex->getStyle("G$k")->getFont()->setSize(8);
			$sheetIndex->getStyle("D$k:F$k")->getNumberFormat()->setFormatCode("#,##0");
			$sheetIndex->getRowDimension($k)->setRowHeight(32.50);

			if($DELIVERY_TYPE == "98")
				$sheetIndex->getStyle("B$k:G$k")->applyFromArray($background_color_style);
				
			$k += 1;
		}

		
		if($DISCOUNT_PRICE != 0) { 

			$sheetIndex->setCellValue("C".$k, iconv("EUC-KR", "UTF-8", "매출할인"))
						->setCellValue("D".$k, iconv("EUC-KR", "UTF-8", "-1"))
						->setCellValue("E".$k, iconv("EUC-KR", "UTF-8", -1 * $DISCOUNT_PRICE))
						->setCellValue("F".$k, iconv("EUC-KR", "UTF-8", -1 * $DISCOUNT_PRICE))
						->setCellValue("G".$k, iconv("EUC-KR", "UTF-8", ""));
			$sheetIndex->getStyle("B$k")->getAlignment()->setWrapText(true);
			$sheetIndex->getStyle("A$k:G$k")->getFont()->setSize(10);
			$sheetIndex->getStyle("D$k:F$k")->getNumberFormat()->setFormatCode("#,##0");
			$sheetIndex->getRowDimension($k)->setRowHeight(32.50);

			$k += 1;
		}
		

		
	}

	while($k < (7 + 12)) { 

		$sheetIndex->mergeCells("D$k:E$k");
		$sheetIndex->getRowDimension($k)->setRowHeight(27);
	
		$k += 1;
	}

	//$sheetIndex->getStyle("C8:C".($k-1))->applyFromArray($style_left);
	$sheetIndex->getStyle("D8:F".($k-1))->applyFromArray($style_right);
	

	$sheetIndex->setCellValue("C$k", iconv("EUC-KR", "UTF-8","★ 제품별 계산서 발행해야 함. (주문서 뒤 견적서 참조하기)"));
	$sheetIndex->mergeCells("C$k:F$k");
	$sheetIndex->getStyle("A$k:F$k")->getFont()->setSize(10)->setBold(true);
	$sheetIndex->getRowDimension($k)->setRowHeight(27);
	$k += 1;

 
	$sheetIndex->setCellValue("B$k", iconv("EUC-KR", "UTF-8","합계액"))
			   ->setCellValue("G$k", number_format($TOTAL_SALE_PRICE));
	$sheetIndex->mergeCells("B$k:E$k");
	$sheetIndex->getStyle("B$k:F$k")->getFont()->setSize(11);
	$sheetIndex->getStyle("G$k")->getNumberFormat()->setFormatCode("#,##0");
	$sheetIndex->getRowDimension($k)->setRowHeight(27);
	$l = $k;
	$k += 1;
 
	$sheetIndex ->setCellValue("B$k", iconv("EUC-KR", "UTF-8","도착지 주소"))
				->setCellValue("C$k", iconv("EUC-KR", "UTF-8",$rs_r_addr1));
	$sheetIndex->mergeCells("C$k:G$k");
	$sheetIndex->getStyle("B$k")->getFont()->setSize(11)->setBold(true);
	$sheetIndex->getStyle("C$k:G$k")->getFont()->setSize(10);
	$sheetIndex->getRowDimension($k)->setRowHeight(24.75 + 24.75);
	$k += 1;
	$k += 1;

	//접수	기장	통화유무	출고/미출고	완결	
	$sheetIndex ->setCellValue("B$k", iconv("EUC-KR", "UTF-8","접수"))
				->setCellValue("C$k", iconv("EUC-KR", "UTF-8","기장"))
				->setCellValue("D$k", iconv("EUC-KR", "UTF-8","통화유무"))
				->setCellValue("E$k", iconv("EUC-KR", "UTF-8","출고/미출고"))
				->setCellValue("G$k", iconv("EUC-KR", "UTF-8","완결"));
	$sheetIndex->mergeCells("E$k:F$k");
	$sheetIndex->getRowDimension($k)->setRowHeight(13.50);
	$k = $k + 1;

	$sheetIndex ->setCellValue("B$k", iconv("EUC-KR", "UTF-8",""))
				->setCellValue("C$k", iconv("EUC-KR", "UTF-8",""))
				->setCellValue("D$k", iconv("EUC-KR", "UTF-8",""))
				->setCellValue("E$k", iconv("EUC-KR", "UTF-8",""))
				->setCellValue("G$k", iconv("EUC-KR", "UTF-8",""));
	$sheetIndex->mergeCells("E$k:F$k");
	$sheetIndex->getRowDimension($k)->setRowHeight(25.50);
	$k = $k + 1;
	

	$sheetIndex->getStyle("A2:H$k")->applyFromArray($BStyle_outline);
	$sheetIndex->getStyle("B4:G".($l+4))->applyFromArray($BStyle);
	$sheetIndex->getStyle("B4:G".($l+3))->applyFromArray($BStyle_font);
	$sheetIndex->getStyle("B4:G".$l)->applyFromArray($BStyle_outline);
	$sheetIndex->getStyle("B".($l+1).":G".($l+1))->applyFromArray($BStyle_outline);
	$sheetIndex->getStyle("B".($l+3).":G".($l+4))->applyFromArray($BStyle_outline);
	
	// Rename sheet
	$sheetIndex->setTitle(iconv("EUC-KR", "UTF-8", "주문서"));

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);




	// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
	$filename = "주문서-".$SENDER_NM."-".$reserve_no."-".date("Ymd");

	// echo $filename;
	$filename="test";
	// exit;
	// Redirect output to a client’s web browser (Excel5)
	header("Content-Type: application/vnd.ms-excel");

	header("Content-Disposition: attachment;filename=".$filename.".xls");
	header("Cache-Control: max-age=0");

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
	$objWriter->setUseDiskCaching(true);
	$objWriter->save("php://output");

	mysql_close($conn);
	exit;

?>
				
