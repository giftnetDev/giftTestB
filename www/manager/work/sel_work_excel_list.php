<?session_start();
  set_time_limit(6000); 
  ini_set("memory_limit", -1);
?>
<?
# =============================================================================
# File Name    : sel_work_excel_list.php
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================

	$con_order_type = "";


	$menu_right = "WO002"; // 메뉴마다 셋팅 해 주어야 합니다


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
	require "../../_classes/biz/work/work.php";
	require "../../_classes/biz/goods/goods.php";
	
	if ($s_adm_cp_type == "구매" || $s_adm_cp_type == "판매공급" ) { 
		$cp_type2 = $s_adm_com_code;
	}

	if ($s_adm_cp_type == "판매") { 
		$cp_type = $s_adm_com_code;
	}

#====================================================================
# Request Parameter
#====================================================================

	if ($start_date == "") {
		$start_date = date("Y-m-d",strtotime("-1 month"));
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("5 day"));
	} else {
		$end_date = trim($end_date);
	}

	$work_date = date("Y-m-d",strtotime("1 day"));

	$day_1_plus = date("Y-m-d",strtotime("1 day"));
	$day_0 = date("Y-m-d",strtotime("0 month"));
	$day_1 = date("Y-m-d",strtotime("-1 day"));
	$day_7 = date("Y-m-d",strtotime("-7 day"));
	$day_31 = date("Y-m-d",strtotime("-1 month"));

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
	$del_tf = "N";
#============================================================
# Page process
#============================================================

	if ($nPage <> "") {
		$nPage = (int)($nPage);
	} else {
		$nPage = 1;
	}

	if ($nPageSize <> "") {
		$nPageSize = (int)($nPageSize);
	} else {
		$nPageSize = 2000;
	}

	$nPageBlock	= 10;
	
#	echo $start_date;
#	echo $end_date;

#===============================================================
# Get Search list count
#===============================================================

	$arr_rs = listWorkOrder($conn, $order_type, $start_date, $end_date, $order_state, $cp_no, $work_order_type, $search_field, $search_str, $order_field, $order_str);

	require_once "../../_PHPExcel/Classes/PHPExcel.php";

	$objPHPExcel = new PHPExcel();

	$sheetIndex = $objPHPExcel->setActiveSheetIndex(0);

	$BStyle = array(
	    "borders" => array(
		 "allborders" => array(
		  "style" => PHPExcel_Style_Border::BORDER_THIN
		 )
	    ),
	    'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ),
	    "font"  => array(
        "name"  => "Gulim",
		"size" => 9)
	);

	$sheetIndex
				->setCellValue("A1", iconv("EUC-KR", "UTF-8", "작업지시일"))
				->setCellValue("B1", iconv("EUC-KR", "UTF-8", "순번"))
				->setCellValue("C1", iconv("EUC-KR", "UTF-8", "주문일"))
				->setCellValue("D1", iconv("EUC-KR", "UTF-8", "출고예정일"))
				->setCellValue("E1", iconv("EUC-KR", "UTF-8", "주문업체"))
				->setCellValue("F1", iconv("EUC-KR", "UTF-8", "수령자"))
				->setCellValue("G1", iconv("EUC-KR", "UTF-8", "상품명/구성품")) //2016-03-29 김옥경팀장님요청
				->setCellValue("H1", iconv("EUC-KR", "UTF-8", "주문/작업수량")) 
				->setCellValue("I1", iconv("EUC-KR", "UTF-8", "작업내역/상태/작업메모"))//2016-03-29 김옥경팀장님요청
				->setCellValue("J1", iconv("EUC-KR", "UTF-8", "배송방식"))
				->setCellValue("K1", iconv("EUC-KR", "UTF-8", "택배사"))
				->setCellValue("L1", iconv("EUC-KR", "UTF-8", "송장수"))
				->setCellValue("M1", iconv("EUC-KR", "UTF-8", "재고"))
				->setCellValue("N1", iconv("EUC-KR", "UTF-8", "영업담당"));

	$temp_date = "";
	$k = 1;

	if (sizeof($arr_rs) > 0) {
		for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
			$RESERVE_NO					      = iconv("EUC-KR", "UTF-8", SetStringFromDB(trim($arr_rs[$j]["RESERVE_NO"])));
			$ORDER_GOODS_NO				      = iconv("EUC-KR", "UTF-8", SetStringFromDB(trim($arr_rs[$j]["ORDER_GOODS_NO"])));
			$ORDER_DATE						  = iconv("EUC-KR", "UTF-8", SetStringFromDB(trim($arr_rs[$j]["ORDER_DATE"])));
			$OPT_OUTSTOCK_DATE				  = iconv("EUC-KR", "UTF-8", SetStringFromDB(trim($arr_rs[$j]["OPT_OUTSTOCK_DATE"])));
			$CP_NO					 		  = iconv("EUC-KR", "UTF-8", SetStringFromDB(trim($arr_rs[$j]["CP_NO"])));
			$O_MEM_NM						  = iconv("EUC-KR", "UTF-8", SetStringFromDB(trim($arr_rs[$j]["O_MEM_NM"])));
			$R_MEM_NM						  = iconv("EUC-KR", "UTF-8", SetStringFromDB(trim($arr_rs[$j]["R_MEM_NM"])));
			$CATE_01						  = iconv("EUC-KR", "UTF-8", SetStringFromDB(trim($arr_rs[$j]["CATE_01"])));
			$CATE_04						  = iconv("EUC-KR", "UTF-8", SetStringFromDB(trim($arr_rs[$j]["CATE_04"])));
			$GOODS_CODE						  = iconv("EUC-KR", "UTF-8", SetStringFromDB(trim($arr_rs[$j]["GOODS_CODE"])));
			$GOODS_NAME						  = iconv("EUC-KR", "UTF-8", SetStringFromDB(trim($arr_rs[$j]["GOODS_NAME"])));
			$OPT_MANAGER_NO					  = iconv("EUC-KR", "UTF-8", SetStringFromDB(trim($arr_rs[$j]["OPT_MANAGER_NO"])));
			$OPT_MEMO						  = SetStringFromDB(trim($arr_rs[$j]["OPT_MEMO"]));
			$WORK_ORDER						  = iconv("EUC-KR", "UTF-8", SetStringFromDB(trim($arr_rs[$j]["WORK_ORDER"])));
			$WORK_SEQ						  = iconv("EUC-KR", "UTF-8", SetStringFromDB(trim($arr_rs[$j]["WORK_SEQ"])));
			$WORK_START_DATE				  = iconv("EUC-KR", "UTF-8", SetStringFromDB(trim($arr_rs[$j]["WORK_START_DATE"])));
			$BULK_TF	  					  = iconv("EUC-KR", "UTF-8", SetStringFromDB(trim($arr_rs[$j]["BULK_TF"])));
			$GOODS_NO						  = iconv("EUC-KR", "UTF-8", SetStringFromDB(trim($arr_rs[$j]["GOODS_NO"])));
			$DELIVERY_TYPE					  = iconv("EUC-KR", "UTF-8", SetStringFromDB(trim($arr_rs[$j]["DELIVERY_TYPE"])));
			$DELIVERY_CP					  = iconv("EUC-KR", "UTF-8", SetStringFromDB(trim($arr_rs[$j]["DELIVERY_CP"])));

			if($CATE_01 <> "")
				$GOODS_NAME = $CATE_01.") "."[".$GOODS_CODE."] ".$GOODS_NAME;
			else
				$GOODS_NAME = "[".$GOODS_CODE."] ".$GOODS_NAME;

			if ($CATE_04 == "CHANGE") {
				$str_cate_04 = iconv("EUC-KR", "UTF-8","(교환건) ");
			} else {
				$str_cate_04 = "";
			}

			if ($WORK_ORDER == 100000) $WORK_ORDER = "";
			if ($WORK_START_DATE == iconv("EUC-KR", "UTF-8",'헤')) $WORK_START_DATE = "";

			$QTY							  = getRefundAbleQty($conn, $RESERVE_NO, $ORDER_GOODS_NO); //trim($arr_rs[$j]["QTY"]);
			$WORK_QTY						  = iconv("EUC-KR", "UTF-8", SetStringFromDB(trim($arr_rs[$j]["WORK_QTY"])));
			
			if($QTY == "0") continue;
			// 재고 파악
			//echo $ORDER_GOODS_NO;
			//echo $GOODS_NO;

			$arr_rs_sub = selectGoodsSub($conn, $GOODS_NO);
			$goods_sub = "";
			for($n = 0; $n < sizeof($arr_rs_sub); $n++)
			{
				$sub_goods_name = iconv("EUC-KR", "UTF-8", SetStringFromDB(trim($arr_rs_sub[$n]["GOODS_NAME"])));
				$sub_goods_cnt =  iconv("EUC-KR", "UTF-8", SetStringFromDB(trim($arr_rs_sub[$n]["GOODS_CNT"])));
				$sub_goods_cate = iconv("EUC-KR", "UTF-8", SetStringFromDB(trim($arr_rs_sub[$n]["GOODS_CATE"])));

				if(startsWith($sub_goods_cate, "0102"))
					continue;

				$goods_sub .= $sub_goods_name." x ".$sub_goods_cnt.iconv("EUC-KR", "UTF-8","개")." ,";
				
			}
			$goods_sub = rtrim($goods_sub, " , ");
			
			$stock_flag = checkStock($conn, $GOODS_NO, $QTY);
			
			/*
			if($j == 0)
				$k = $j + 2;
			else 
				$k = $k + 1;
			*/

			$k = $k + 1;

			/*
			if ($temp_date <> $WORK_START_DATE) {

				//style="text-align:left;padding-left:10px;background: #DEDEDE;vertical-align: middle;"

				if ($WORK_START_DATE) {
					//echo "작업일 : ".left($WORK_START_DATE,10)."<br/>";
					$sheetIndex
							->setCellValue("A$k", iconv("EUC-KR", "UTF-8", "작업일 : ".left($WORK_START_DATE,10)))		 
							->setCellValue("B$k", "")
							->setCellValue("C$k", "")
							->setCellValue("D$k", "")
							->setCellValue("E$k", "")
							->setCellValue("F$k", "")
							->setCellValue("G$k", "")
							->setCellValue("H$k", "")
							->setCellValue("I$k", "")
							->setCellValue("J$k", "")
							->setCellValue("K$k", "")
							->setCellValue("L$k", "")
							->setCellValue("M$k", "");  
				} else { 
					// "미지정 주문 리스트<br/>";
					$sheetIndex
							->setCellValue("A$k", iconv("EUC-KR", "UTF-8","미지정 주문 리스트"))
							->setCellValue("B$k", "")
							->setCellValue("C$k", "")
							->setCellValue("D$k", "")
							->setCellValue("E$k", "")
							->setCellValue("F$k", "")
							->setCellValue("G$k", "")
							->setCellValue("H$k", "")
							->setCellValue("I$k", "")
							->setCellValue("J$k", "")
							->setCellValue("K$k", "")
							->setCellValue("L$k", "")
							->setCellValue("M$k", "");  
				}

				//$k = $k + 1;
				$temp_date = $WORK_START_DATE;
			}
			*/

			$option_str	= "";

			$rs_order_goods = selectOrderGoods($conn, $ORDER_GOODS_NO);
			$rs_goods_no			= trim($rs_order_goods[0]["GOODS_NO"]);
			$rs_opt_wrap_no			= trim($rs_order_goods[0]["OPT_WRAP_NO"]);
			$rs_opt_sticker_no		= trim($rs_order_goods[0]["OPT_STICKER_NO"]);
			$rs_opt_sticker_ready	= trim($rs_order_goods[0]["OPT_STICKER_READY"]);
			$rs_opt_outbox_tf		= trim($rs_order_goods[0]["OPT_OUTBOX_TF"]);
			$rs_opt_sticker_msg		= trim($rs_order_goods[0]["OPT_STICKER_MSG"]);
			$rs_opt_print_msg		= trim($rs_order_goods[0]["OPT_PRINT_MSG"]);

			$sticker_ready = ($rs_opt_sticker_ready == "Y" ? "* " : "");
			$option_str .= ($rs_opt_sticker_no <> "0" ? $sticker_ready." ".getGoodsName($conn, $rs_opt_sticker_no).", " : "");
			$option_str .= ($rs_opt_outbox_tf == "Y" ? "아웃박스스티커," : "" );
			$option_str .= ($rs_opt_wrap_no <> "0" ? " ".getGoodsName($conn, $rs_opt_wrap_no). ", " : "");
			$option_str .= ($rs_opt_sticker_msg <> "" ? $rs_opt_sticker_msg. ", " : "");
			$option_str .= ($rs_opt_print_msg <> "" ? $rs_opt_print_msg. ", " : "");
			$option_str = ltrim($option_str, ",");
			$option_str .= $OPT_MEMO;

			$option_str = rtrim($option_str, ", ");

			$total_cnt_yes = 0;
			$arr_rs_delivery = cntOrderGoodsDelivery($conn, $RESERVE_NO, $ORDER_GOODS_NO, $individual_no);
			
			for($o = 0; $o < sizeof($arr_rs_delivery); $o ++)
			{
				$DELIVERY_CP				= trim($arr_rs_delivery[$o]["DELIVERY_CP"]);
				$TOTAL						= trim($arr_rs_delivery[$o]["TOTAL"]);
				$CNT_YES					= trim($arr_rs_delivery[$o]["CNT_YES"]);
				$CNT_NO						= trim($arr_rs_delivery[$o]["CNT_NO"]);

				$total_cnt_yes += $CNT_YES;
			}
			$total_cnt_yes .= " 장";

			if($WORK_START_DATE != "0000-00-00 00:00:00")
				$WORK_START_DATE = date("Y-m-d",strtotime($WORK_START_DATE));
			else
				$WORK_START_DATE = "미지정";

			if($WORK_SEQ == "0")
				$WORK_SEQ = "";

			$sheetIndex
							->setCellValue("A$k", iconv("EUC-KR", "UTF-8", $WORK_START_DATE))                                   // 작업지시일
							->setCellValue("B$k", iconv("EUC-KR", "UTF-8", $WORK_SEQ))											// 순번
							->setCellValue("C$k", iconv("EUC-KR", "UTF-8", left($ORDER_DATE,10)))								// 주문일
							->setCellValue("D$k", iconv("EUC-KR", "UTF-8", left($OPT_OUTSTOCK_DATE,10)))						// 출고예정일
							->setCellValue("E$k", iconv("EUC-KR", "UTF-8", getCompanyName($conn, $CP_NO)))						// 주문업체
							->setCellValue("F$k", $O_MEM_NM)																	// 수령자
							->setCellValue("G$k", $str_cate_04.$GOODS_NAME.($goods_sub != "" ? " (".$goods_sub.")" : ""))		// 상품명/구성품
							->setCellValue("H$k", iconv("EUC-KR", "UTF-8", ($WORK_START_DATE && $WORK_QTY <> 0 ? $QTY."/".$WORK_QTY : $QTY)))																															// 주문/작업 수량
							->setCellValue("I$k", iconv("EUC-KR", "UTF-8", $option_str))										// 작업내역 / 상태 / 작업메모
							->setCellValue("J$k", iconv("EUC-KR", "UTF-8", getDcodeName($conn, "DELIVERY_TYPE", $DELIVERY_TYPE))) //배송방식
							->setCellValue("K$k", iconv("EUC-KR", "UTF-8", getDcodeName($conn, "DELIVERY_CP", $DELIVERY_CP)))	// 택배사
							->setCellValue("L$k", iconv("EUC-KR", "UTF-8", $total_cnt_yes))										// 송장수
							->setCellValue("M$k", iconv("EUC-KR", "UTF-8", ($stock_flag ? "있음" : "없음")))						// 재고
							->setCellValue("N$k", iconv("EUC-KR", "UTF-8", getAdminName($conn,$OPT_MANAGER_NO)));				// 영업담당  
		}
	}
	$sheetIndex->getStyle("A1:N1")->getFont()->setBold(true);
	$sheetIndex->getStyle("A1:N$k")->applyFromArray($BStyle);
	$sheetIndex->getColumnDimension("A")->setWidth(12.1);
	$sheetIndex->getColumnDimension("B")->setWidth(4.3);
	$sheetIndex->getColumnDimension("C")->setWidth(12.1);
	$sheetIndex->getColumnDimension("D")->setWidth(12.1);
	$sheetIndex->getColumnDimension("E")->setWidth(25.7);
	$sheetIndex->getColumnDimension("F")->setWidth(25.7);
	$sheetIndex->getColumnDimension("G")->setWidth(30);
	$sheetIndex->getColumnDimension("H")->setWidth(12.7);
	$sheetIndex->getColumnDimension("I")->setWidth(25.7);
	$sheetIndex->getColumnDimension("J")->setWidth(8.71);
	$sheetIndex->getColumnDimension("K")->setWidth(11.4);
	$sheetIndex->getColumnDimension("L")->setWidth(8.3);
	$sheetIndex->getColumnDimension("M")->setWidth(4.3);
	$sheetIndex->getColumnDimension("N")->setWidth(7.7);

	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('Sheet1');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	//$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(100);

	// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
	//$filename = iconv("UTF-8", "EUC-KR", "MRO -> 통합주문 변환 -".date("Ymd",strtotime("0 month")));
	$filename = "작업지시서 - ".date("Ymd",strtotime("0 month"));

	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment;filename=".$filename.".xls");
	header('Cache-Control: max-age=0');
 
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

	mysql_close($conn);
	exit;
?>