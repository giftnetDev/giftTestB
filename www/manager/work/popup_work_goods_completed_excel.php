<?session_start();?>
<?
# =============================================================================
# File Name    : popup_work_goods_completed_excel.php
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "WO003"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/work/work.php";
	require "../../_classes/biz/order/order.php";

#====================================================================
# Request Parameter
#====================================================================

	//echo $work_date;
	$arr_goods_no = array();
	$arr_work_qty_no = array();
	$arr_cnt = 0; 

	$arr_rs = listWorkGoodsCompleted($conn, $work_date);
	
	if (sizeof($arr_rs) > 0) {
		for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

			$GOODS_NO					= SetStringFromDB($arr_rs[$j]["GOODS_NO"]);
			$DELIVERY_CNT_IN_BOX		= SetStringFromDB($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);
			$OG_GOODS_CATE				= SetStringFromDB($arr_rs[$j]["GOODS_CATE"]);
			$WORK_QTY					= SetStringFromDB($arr_rs[$j]["WORK_QTY"]);

			// 하위 상품이 있는지 확인 합니다.
			$arr_sub_goods = getSubGoodsInfo($conn, $GOODS_NO);
			
			if (sizeof($arr_sub_goods) > 0) {

				for ($k = 0 ; $k < sizeof($arr_sub_goods); $k++) {
					$is_flag = false; 
					$GOODS_SUB_NO	= SetStringFromDB($arr_sub_goods[$k]["GOODS_SUB_NO"]);
					$GOODS_CNT		= SetStringFromDB($arr_sub_goods[$k]["GOODS_CNT"]);
					$GS_GOODS_CATE	= SetStringFromDB($arr_sub_goods[$k]["GOODS_CATE"]);
					
					if (sizeof($arr_goods_no) > 0) {
						
						for ($g = 0 ; $g < sizeof($arr_goods_no) ; $g++) {
							if (trim($arr_goods_no[$g]) == $GOODS_SUB_NO) {
								//echo " 하위 상품 경우 수량만 넣을때 ".$arr_goods_no[$g]. " : ".$arr_qty_no[$g]."^".($g)."<br>";
								$is_flag = true;

								if(startsWith("010202", $GS_GOODS_CATE)) { 
									$arr_work_qty_no[$g] = $arr_work_qty_no[$g] + ceil(($WORK_QTY * $GOODS_CNT) / $DELIVERY_CNT_IN_BOX);
								} else { 
									$arr_work_qty_no[$g]  = $arr_work_qty_no[$g] + ($WORK_QTY * $GOODS_CNT);
								}
							}
						}

						if ($is_flag == false) {
							$arr_goods_no[$arr_cnt] = $GOODS_SUB_NO;

							if(startsWith("010202", $GS_GOODS_CATE)) { 
								$arr_work_qty_no[$arr_cnt] = ceil(($WORK_QTY * $GOODS_CNT) / $DELIVERY_CNT_IN_BOX);
							} else { 
								$arr_work_qty_no[$arr_cnt] = $WORK_QTY * $GOODS_CNT;
							}
							//echo " 하위 상품 배열에 넣을때 ".$arr_goods_no[$arr_cnt]. " : ".$arr_qty_no[$arr_cnt]."^".$arr_cnt."<br>";
							$arr_cnt = $arr_cnt + 1;
						}

					} else {
						$is_flag == false;
						$arr_goods_no[$arr_cnt] = $GOODS_SUB_NO;

						if(startsWith("010202", $GS_GOODS_CATE)) { 
							$arr_work_qty_no[$arr_cnt] = ceil(($WORK_QTY * $GOODS_CNT) / $DELIVERY_CNT_IN_BOX);
						} else { 
							$arr_work_qty_no[$arr_cnt] = $WORK_QTY * $GOODS_CNT;
						}
						//echo "하위 상품 처음 배열에 넣을때 ".$arr_goods_no[$arr_cnt]. " : ".$arr_qty_no[$arr_cnt]."^".$arr_cnt."<br>";
						$arr_cnt = $arr_cnt + 1;
						
					}
				}

			} else {

				// 하위 상품이 없을 경우
				if (sizeof($arr_goods_no) > 0) {
					for ($h = 0 ; $h < sizeof($arr_goods_no) ; $h++) {

						if (trim($arr_goods_no[$h]) == $GOODS_NO) {

							//echo " 하위 상품이 없을 경우 수량만 넣을때 ".$arr_goods_no[$g]. " : ".$arr_qty_no[$g]."^".($g)."<br>";
							$is_flag = true;
							$arr_work_qty_no[$h]  = $arr_work_qty_no[$h] + $WORK_QTY;
						}
					}

					if ($is_flag == false) {
						$arr_goods_no[$arr_cnt] = $GOODS_NO;

						$arr_work_qty_no[$arr_cnt] = $WORK_QTY;

						//echo " 하위 상품이 없을 경우 배열에 넣을때 ".$arr_goods_no[$arr_cnt]. " : ".$arr_qty_no[$arr_cnt]."^".$arr_cnt."<br>";
						$arr_cnt = $arr_cnt + 1;
					}

				} else {

					$is_flag == false;
					$arr_goods_no[$arr_cnt] = $GOODS_NO;
					
					$arr_work_qty_no[$arr_cnt] = $WORK_QTY;

					//echo "하위 상품이 없을 경우 처음 배열에 넣을때 ".$arr_goods_no[$arr_cnt]. " : ".$arr_qty_no[$arr_cnt]."^".$arr_cnt."<br>";
					$arr_cnt = $arr_cnt + 1;
				}

			}

			$is_flag = false;
		
		}
	}

	$arr = array();
	if(sizeof($arr_goods_no) > 0) {
		for($i = 0; $i < sizeof($arr_goods_no); $i++) {

			$arr[$i]["GOODS_NO"] = $arr_goods_no[$i];
			$arr[$i]["WORK_QTY_NO"] = $arr_work_qty_no[$i];

			$arr_goods = selectGoods($conn, $arr_goods_no[$i]);
			$arr[$i]["GOODS_CODE"]	= trim($arr_goods[0]["GOODS_CODE"]);
			$arr[$i]["GOODS_NAME"]	= trim($arr_goods[0]["GOODS_NAME"]);
			$arr[$i]["GOODS_CATE"]	= trim($arr_goods[0]["GOODS_CATE"]);
			$arr[$i]["STOCK_CNT"]	= trim($arr_goods[0]["STOCK_CNT"]);
			$arr[$i]["FSTOCK_CNT"]	= trim($arr_goods[0]["FSTOCK_CNT"]);
		}
	}

	foreach ($arr as $key => $row) {
		$GOODS_CATE[$key]  = $row['GOODS_CATE'];
		$GOODS_NAME[$key]  = $row['GOODS_NAME'];
	}

	if(sizeof($arr) > 0)
		array_multisort($GOODS_CATE, SORT_ASC, $GOODS_NAME, SORT_ASC, $arr);

	require_once "../../_PHPExcel/Classes/PHPExcel.php";

	$objPHPExcel = new PHPExcel();

	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A1", iconv("EUC-KR", "UTF-8", "상품코드"))
				->setCellValue("B1", iconv("EUC-KR", "UTF-8", "자재명"))
				->setCellValue("C1", iconv("EUC-KR", "UTF-8", "완료량"))
				->setCellValue("D1", iconv("EUC-KR", "UTF-8", "가재고"))
				->setCellValue("E1", iconv("EUC-KR", "UTF-8", "재고"));

	if(sizeof($arr) > 0) {
		for($j = 0; $j < sizeof($arr); $j++) {

		
			$goods_code		= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr[$j]["GOODS_CODE"]));
			$goods_name		= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr[$j]["GOODS_NAME"]));
			$work_qty_no	= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr[$j]["WORK_QTY_NO"]));
			$fstock_cnt		= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr[$j]["FSTOCK_CNT"]));
			$stock_cnt		= iconv("EUC-KR", "UTF-8", SetStringFromDB($arr[$j]["STOCK_CNT"]));


			$k = $j+2;

			$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A$k", $goods_code)
							->setCellValue("B$k", $goods_name)
							->setCellValue("C$k", $work_qty_no)
							->setCellValue("D$k", $fstock_cnt)
							->setCellValue("E$k", $stock_cnt);
			
		}
	}

	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('Sheet1');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(100);

	// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
	//$filename = iconv("UTF-8", "EUC-KR", "MRO -> 통합주문 변환 -".date("Ymd",strtotime("0 month")));
	$filename = "작업완료자재조회 - ".date("Ymd",strtotime("0 month"));

	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment;filename=".$filename.".xls");
	header('Cache-Control: max-age=0');
 
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

	mysql_close($conn);
	exit;
?>