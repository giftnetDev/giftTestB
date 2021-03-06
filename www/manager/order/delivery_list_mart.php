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
	$menu_right = "OD015"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/payment/payment.php";
	require "../../_classes/biz/stock/stock.php"; 
	require "../../_classes/biz/company/company.php"; 

	$seed_number = 1;

	//인터파크-SK케미칼은 제주도 제외 없음
	$has_island = true;

	if ($mode == "DECIDE_DELIVERY_PAPER") {
		if($cp_no == '')
			return;

		$CON_GIFTNET_CP_NO = 1;
		$SEND_CP_ADDR = getCompanyAddress($conn, $CON_GIFTNET_CP_NO);

		$arr_rs = listOrderDeliveryForMart_LEVEL1_From_To($conn, $start_date, $end_date, "2", $cp_no); //order_state = 주문확인
		if (sizeof($arr_rs) > 0) {
			for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
				
				$CP_NO		    = trim($arr_rs[$j]["CP_NO"]);
				$CP_NM		    = trim($arr_rs[$j]["CP_NM"]);
				$R_ADDR1      = SetStringToDB(trim($arr_rs[$j]["R_ADDR1"]));
				$R_MEM_NM    = trim($arr_rs[$j]["R_MEM_NM"]);
				$O_MEM_NM    = trim($arr_rs[$j]["O_MEM_NM"]);
				
				$arr_rs2 = listOrderDeliveryForMart_LEVEL2_From_To($conn, $start_date, $end_date, $CP_NO, $O_MEM_NM, $R_MEM_NM, $R_ADDR1, "'2'"); //order_state = 주문확인

				if (sizeof($arr_rs2) > 0) {
					$GOODS_NO			   = '';
					$GOODS_NAME		       = '';
					$QTY		           = '';
					$DELIVERY_CNT_IN_BOX   = '';
					$RESERVE_NO            = '';
					$ORDER_GOODS_NO        = '';
					$R_PHONE               = '';
					$R_HPHONE              = '';
					$R_ADDR1               = '';
					$MEMO                  = '';
					$O_MEM_NM              = '';

					$CATE_01               = ''; //송장말머리

					$DELIVERY_PROFIT_CODE  = '';
					$DELIVERY_PROFIT_PRICE = '';
					$DELIVERY_FEE_CODE     = '';
					$DELIVERY_FEE	       = '';

					$ORDER_MANAGER_NM      = '';
					$ORDER_MANAGER_PHONE   = '';
					$ORDER_PHONE           = '';
					$GOODS_DELIVERY_NAME   = '';

					$PAYMENT_TYPE = "신용";

					$DELIVERY_CP = "롯데택배";
					$DELIVERY_TYPE = "택배";

					$R_MEM_NM .= "님";
					
					// 조립이 한건이라도 있는지 없는지 체크
					$ASSEMBLE_TF = 'N';
					$EVENT_ASSEMBLE_TF = 'N';

					$GOODS_DELIVERY_NAME_TOTAL = '';
					$GOODS_DELIVERY_EVENT_NAME_TOTAL = '';
					$ORDER_GOODS_NO_TOTAL = '';

					$i = 0;

					for ($m = 0 ; $m < sizeof($arr_rs2); $m++) {

						//echo $arr_rs2[$m]["GOODS_NO"]."//".$arr_rs2[$m+1]["GOODS_NO"]."<br/>";
						//echo $arr_rs2[$m]["ORDER_GOODS_NO"]."<br/>";
						if(chkOrderDelivery($conn, $arr_rs2[$m]["ORDER_GOODS_NO"]) > 0)
						{	
							//echo "이미등록: ".$arr_rs2[$m]["ORDER_GOODS_NO"]."<br/>";
							continue;
						}
	
						$current_qty = getRefundAbleQty($conn, $arr_rs2[$m]["RESERVE_NO"], $arr_rs2[$m]["ORDER_GOODS_NO"]);
						if($arr_rs2[$m]["GOODS_NO"] == $arr_rs2[$m+1]["GOODS_NO"] && $arr_rs2[$m]["GOODS_OPTION_01"] == $arr_rs2[$m+1]["GOODS_OPTION_01"] && $m + 1 < sizeof($arr_rs2) && chkOrderDelivery($conn, $arr_rs2[$m+1]["ORDER_GOODS_NO"]) <= 0)
						{
							//echo "current".$current_qty."<br/>";
							$next_qty = getRefundAbleQty($conn, $arr_rs2[$m+1]["RESERVE_NO"], $arr_rs2[$m+1]["ORDER_GOODS_NO"]);
							//echo "next".$next_qty."<br/>";

							if(strpos($arr_rs2[$m]["ORDER_GOODS_NO"], ',') !== false)  
								$current_total_qty = intval($arr_rs2[$m]["GOODS_CNT"]) * intval($arr_rs2[$m]["QTY"]);
							else
								$current_total_qty = intval($arr_rs2[$m]["GOODS_CNT"]) * intval($current_qty);
							
							$next_total_qty = intval($arr_rs2[$m+1]["GOODS_CNT"]) * intval($next_qty);

							$arr_rs2[$m+1]["GOODS_CNT"]	= $next_total_qty  + $current_total_qty;

							//echo "current_total_qty".$current_total_qty."<br/>";
							//echo "next_total_qty".$next_total_qty."<br/>";
							//echo "total: ".(intval($next_total_qty) + intval($current_total_qty))."<br/>";
							
							$arr_rs2[$m+1]["QTY"]	= 1;
							$arr_rs2[$m+1]["ORDER_GOODS_NO"] .= ",".$arr_rs2[$m]["ORDER_GOODS_NO"];
							$arr_rs2[$m+1]["QTY_CHANGED"] = "Y"; 
						} else {

							//echo "입력:".$arr_rs2[$m]["GOODS_CNT"]."//".$arr_rs2[$m]["QTY"]."<br/>";

							$arr_rs3[$i]["CP_ORDER_NO"]         = trim($arr_rs2[$m]["CP_ORDER_NO"]);
							$arr_rs3[$i]["GOODS_NO"]            = trim($arr_rs2[$m]["GOODS_NO"]);
							$arr_rs3[$i]["GOODS_NAME"]		    = trim($arr_rs2[$m]["GOODS_NAME"]);
							$arr_rs3[$i]["GOODS_SUB_NAME"]		= trim($arr_rs2[$m]["GOODS_SUB_NAME"]);
							
							if($arr_rs2[$m]["QTY_CHANGED"] == "Y") 
								$arr_rs3[$i]["QTY"]		            = trim($arr_rs2[$m]["QTY"]);
							else 
								$arr_rs3[$i]["QTY"]		            = $current_qty;

							$arr_rs3[$i]["DELIVERY_CNT_IN_BOX"] = trim($arr_rs2[$m]["DELIVERY_CNT_IN_BOX"]);
							$arr_rs3[$i]["DELIVERY_PROFIT"]     = trim($arr_rs2[$m]["DELIVERY_PROFIT"]);
							$arr_rs3[$i]["RESERVE_NO"]          = trim($arr_rs2[$m]["RESERVE_NO"]);
							$arr_rs3[$i]["ORDER_GOODS_NO"]      = trim($arr_rs2[$m]["ORDER_GOODS_NO"]);
							$arr_rs3[$i]["GOODS_CNT"]           = trim($arr_rs2[$m]["GOODS_CNT"]);
							$arr_rs3[$i]["R_PHONE"]             = trim($arr_rs2[$m]["R_PHONE"]);
							$arr_rs3[$i]["R_HPHONE"]            = trim($arr_rs2[$m]["R_HPHONE"]);
							$arr_rs3[$i]["R_ADDR1"]             = SetStringToDB(trim($arr_rs2[$m]["R_ADDR1"]));
							$arr_rs3[$i]["MEMO"]                = SetStringToDB(trim($arr_rs2[$m]["MEMO"]));
							$arr_rs3[$i]["O_MEM_NM"]            = trim($arr_rs2[$m]["O_MEM_NM"]);
							$arr_rs3[$i]["DELIVERY_FEE"]        = trim($arr_rs2[$m]["DELIVERY_FEE"]);
							$arr_rs3[$i]["CATE_04"]             = trim($arr_rs2[$m]["CATE_04"]);
							$arr_rs3[$i]["CATE_01"]             = trim($arr_rs2[$m]["CATE_01"]);
							$arr_rs3[$i]["GOODS_CODE"]          = trim($arr_rs2[$m]["GOODS_CODE"]);
							$arr_rs3[$i]["GOODS_OPTION_01"]      = trim($arr_rs2[$m]["GOODS_OPTION_01"]);

							$i++;

						}
					}

					$arr_ogogd = array();

					$arr_cate_01 = array();

					$arr_ordergoods_scan = array();

					for ($k = 0 ; $k < sizeof($arr_rs3); $k++) {

						//echo trim($arr_rs3[$k]["GOODS_NO"])."//".$arr_rs3[$k]["GOODS_CNT"]."//".$arr_rs3[$k]["GOODS_OPTION_01"]."//".trim($arr_rs3[$k]["QTY"])."<br/>";

						$CP_ORDER_NO		   = trim($arr_rs3[$k]["CP_ORDER_NO"]);
						$GOODS_NO		       = trim($arr_rs3[$k]["GOODS_NO"]);
						$GOODS_NAME		       = trim($arr_rs3[$k]["GOODS_NAME"]);
						$GOODS_SUB_NAME		   = trim($arr_rs3[$k]["GOODS_SUB_NAME"]);
						$QTY		           = trim($arr_rs3[$k]["QTY"]);
						$DELIVERY_CNT_IN_BOX   = trim($arr_rs3[$k]["DELIVERY_CNT_IN_BOX"]);
						$DELIVERY_PROFIT_CODE  = trim($arr_rs3[$k]["DELIVERY_PROFIT"]);
						$RESERVE_NO            = trim($arr_rs3[$k]["RESERVE_NO"]);
						$ORDER_GOODS_NO_MULTI  = trim($arr_rs3[$k]["ORDER_GOODS_NO"]);
						$GOODS_CNT             = trim($arr_rs3[$k]["GOODS_CNT"]);
						$R_PHONE               = trim($arr_rs3[$k]["R_PHONE"]);
						$R_HPHONE              = trim($arr_rs3[$k]["R_HPHONE"]);
						$R_ADDR1               = trim($arr_rs3[$k]["R_ADDR1"]);
						$MEMO                  = trim($arr_rs3[$k]["MEMO"]);
						$O_MEM_NM              = trim($arr_rs3[$k]["O_MEM_NM"]);
						$DELIVERY_FEE_CODE_FULL= trim($arr_rs3[$k]["DELIVERY_FEE"]);
						$CATE_01			   = trim($arr_rs3[$k]["CATE_01"]);
						//$GOODS_CODE			   = trim($arr_rs3[$k]["GOODS_CODE"]);
						$GOODS_CODE = "";
						$GOODS_OPTION_01		   = trim($arr_rs3[$k]["GOODS_OPTION_01"]);
						
						if($GOODS_OPTION_01 <> "")
							$STICKER_NAME = getNameWithoutNumber(getGoodsName($conn, $GOODS_OPTION_01));
						else
							$STICKER_NAME = "없음";

					


						//if($GOODS_CODE <> "")
						//	$GOODS_CODE = "[".$GOODS_CODE."] ";

						if(in_array($CATE_01, $arr_cate_01)) {
							$CATE_01 = "";
						} else {
							$arr_cate_01[] = $CATE_01;
						}

						if($GOODS_CNT * $QTY <= 0)
							continue;

						//2015-09-14 품절, 일시품절, 단종시 송장생성 안함
						$GOODS_STATE           = trim($arr_rs3[$k]["CATE_04"]);
						if($GOODS_STATE != '판매중' && $GOODS_STATE != '재판매')
						{	
							//echo "단종";
							continue;
						}

						// 이미 등록했다면 패스
						//$order_goods_cnt = sizeof(explode(",", $ORDER_GOODS_NO_MULTI));
						//echo $order_goods_cnt."<br/>";
						if(chkOrderDelivery($conn, $ORDER_GOODS_NO_MULTI) > 0)
						{	
							//echo "이미등록";
							continue;
						}

						$DELIVERY_PROFIT = getDcodeName($conn, 'DELIVERY_PROFIT', $DELIVERY_PROFIT_CODE);
						$DELIVERY_PROFIT_PRICE = getDcodeExtByCode($conn, 'DELIVERY_PROFIT', $DELIVERY_PROFIT_CODE);

						$DELIVERY_FEE_FULL = getDcodeName($conn, 'DELIVERY_FEE', $DELIVERY_FEE_CODE_FULL);
						$DELIVERY_FEE_CODE = 'DF001';
						$DELIVERY_FEE =  getDcodeName($conn, 'DELIVERY_FEE', $DELIVERY_FEE_CODE);

						$ORDER_MANAGER_NM = getCompanyNameWithNoCode($conn, $CP_NO); 
						$ORDER_MANAGER_PHONE = getCompanyPhone($conn, $CP_NO);
						$ORDER_PHONE = $ORDER_MANAGER_PHONE;
						
						//(주문수 * 구성품수량) / 박스입수
						$total_qty = $QTY * $GOODS_CNT;
						$fullBoxCnt = floor($total_qty / $DELIVERY_CNT_IN_BOX);

						//echo "토탈수량".$total_qty."박스입수나눈수량".$fullBoxCnt."<br/>";

						//짧은상품명 2017-07-10
						$SHORT_GOODS_NAME = getNumberWithoutName($GOODS_NAME)."번 ".$GOODS_SUB_NAME;

						$GOODS_DELIVERY_NAME = $GOODS_CODE.$CATE_01.$SHORT_GOODS_NAME." * ".$total_qty." 개 ";

						//echo $GOODS_DELIVERY_NAME." ";
						//echo $DELIVERY_PROFIT."<br/>";

						if($DELIVERY_PROFIT == "조립") {

							$AssembleIndex = 1;

							if($fullBoxCnt > 0) {
								
								for($l = 1; $l <= $fullBoxCnt; $l ++)
								{
									//echo "완박스($l)<br/>";
									// 주문수량이 박스입수를 넘어갔을경우 박스 수량만큼 완박스 처리
									$DELIVERY_PROFIT_CHANGED = '완박스';
								    $DELIVERY_PROFIT_CODE_CHANGED = getDcodeCode($conn, 'DELIVERY_PROFIT', $DELIVERY_PROFIT_CHANGED);
									
									//완박스 되었을때 물건이 10KG 초과라면
									if($DELIVERY_FEE_CODE_FULL == 'DF004') { 
										$DELIVERY_FEE = $DELIVERY_FEE_FULL;
										$DELIVERY_FEE_CODE = $DELIVERY_FEE_CODE_FULL;
									}

									$GOODS_DELIVERY_NAME = $GOODS_CODE.$CATE_01.$SHORT_GOODS_NAME." * "."1"." 박스(".$DELIVERY_CNT_IN_BOX."개)";

									//echo "조립-완박".$GOODS_DELIVERY_NAME.(($total_qty / $DELIVERY_CNT_IN_BOX) - $fullBoxCnt > 0 ? $fullBoxCnt + 1 : $fullBoxCnt)."-".$AssembleIndex."<br/>";
									
									$order_goods_delivery_no = insertOrderDeliveryPerPaper($conn, $CP_ORDER_NO, '', '', $R_MEM_NM, $R_PHONE, $R_HPHONE, $R_ADDR1, '1', $MEMO, $O_MEM_NM, $ORDER_PHONE, $ORDER_MANAGER_NM, $ORDER_MANAGER_PHONE, $PAYMENT_TYPE, $SEND_CP_ADDR, $GOODS_DELIVERY_NAME, $DELIVERY_CP, $DELIVERY_TYPE, $DELIVERY_PROFIT_CHANGED, $DELIVERY_PROFIT_CODE_CHANGED, $DELIVERY_FEE, $DELIVERY_FEE_CODE, $s_adm_no);


									//송장당 바코드 스캔을 위한 상품 매치 - 일단 제외
									InsertOrderGoodsByDeliveryNo($conn, $order_goods_delivery_no, $GOODS_NO, $DELIVERY_CNT_IN_BOX, $GOODS_OPTION_01);
									
									$ARR_ORDER_GOODS_NO_TOTAL = explode(",", $ORDER_GOODS_NO_MULTI);
									for($o = 0; $o < sizeof($ARR_ORDER_GOODS_NO_TOTAL); $o ++)
									{
										$EACH_ORDER_GOODS_NO = $ARR_ORDER_GOODS_NO_TOTAL[$o];
										
										$arr_ogogd[] = array("ORDER_GOODS_NO" => $EACH_ORDER_GOODS_NO, "ORDER_GOODS_DELIVERY_NO" => $order_goods_delivery_no);
									}

									$AssembleIndex++;
								}

								//완박스 싸고 남은 조립 1건
								if(($total_qty / $DELIVERY_CNT_IN_BOX) - $fullBoxCnt > 0) {

									//echo "완박스 싸고 남은 조립 1건<br/>";

									$GOODS_DELIVERY_NAME = $GOODS_CODE.$CATE_01.$SHORT_GOODS_NAME." * ".($total_qty - ($fullBoxCnt * $DELIVERY_CNT_IN_BOX))." 개 ";
									$ASSEMBLE_TF = 'Y';
									$ORDER_GOODS_NO_TOTAL .= $ORDER_GOODS_NO_MULTI.",";
									$GOODS_DELIVERY_NAME_TOTAL .= $GOODS_DELIVERY_NAME."    //    ";

									$arr_ordergoods_scan[] = array("GOODS_TYPE" => $DELIVERY_PROFIT, "GOODS_NO" => $GOODS_NO, "GOODS_TOTAL" => ($total_qty - ($fullBoxCnt * $DELIVERY_CNT_IN_BOX)), "STICKER_NO" => $GOODS_OPTION_01);
								}

							} else {

								//echo "완박스는 애초에 없고 짜투리<br/>";

								$ASSEMBLE_TF = 'Y';
								$ORDER_GOODS_NO_TOTAL .= $ORDER_GOODS_NO_MULTI.",";
								$GOODS_DELIVERY_NAME_TOTAL .= $GOODS_DELIVERY_NAME."    //    ";
								$arr_ordergoods_scan[] = array("GOODS_TYPE" => $DELIVERY_PROFIT, "GOODS_NO" => $GOODS_NO, "GOODS_TOTAL" => $total_qty, "STICKER_NO" => $GOODS_OPTION_01);
							}
						
						} else if($DELIVERY_PROFIT == "이벤트") {

							$AssembleIndex = 1;

							if($fullBoxCnt > 0) {
								
								for($l = 1; $l <= $fullBoxCnt; $l ++)
								{
									// 주문수량이 박스입수를 넘어갔을경우 박스 수량만큼 완박스 처리
									//$DELIVERY_PROFIT_CHANGED = '완박스';
								    //$DELIVERY_PROFIT_CODE_CHANGED = getDcodeCode($conn, 'DELIVERY_PROFIT', $DELIVERY_PROFIT_CHANGED);
									
									//완박스 되었을때 물건이 10KG 초과라면
									if($DELIVERY_FEE_CODE_FULL == 'DF004') { 
										$DELIVERY_FEE = $DELIVERY_FEE_FULL;
										$DELIVERY_FEE_CODE = $DELIVERY_FEE_CODE_FULL;
									}

									$GOODS_DELIVERY_NAME = $GOODS_CODE.$CATE_01.$SHORT_GOODS_NAME." * "."1"." 박스(".$DELIVERY_CNT_IN_BOX."개)";

									//echo "이벤트-완박".$GOODS_DELIVERY_NAME.(($total_qty / $DELIVERY_CNT_IN_BOX) - $fullBoxCnt > 0 ? $fullBoxCnt + 1 : $fullBoxCnt)."-".$AssembleIndex."<br/>";
									//$DELIVERY_CNT_TOTAL_AND_INDEX = (($total_qty / $DELIVERY_CNT_IN_BOX) - $fullBoxCnt > 0 ? $fullBoxCnt + 1 : $fullBoxCnt)."-".$AssembleIndex;

									$order_goods_delivery_no = insertOrderDeliveryPerPaper($conn, $CP_ORDER_NO, '', '', $R_MEM_NM, $R_PHONE, $R_HPHONE, $R_ADDR1, '1', $MEMO, $O_MEM_NM, $ORDER_PHONE, $ORDER_MANAGER_NM, $ORDER_MANAGER_PHONE, $PAYMENT_TYPE, $SEND_CP_ADDR, $GOODS_DELIVERY_NAME, $DELIVERY_CP, $DELIVERY_TYPE, $DELIVERY_PROFIT, $DELIVERY_PROFIT_CODE, $DELIVERY_FEE, $DELIVERY_FEE_CODE, $s_adm_no);

									InsertOrderGoodsByDeliveryNo($conn, $order_goods_delivery_no, $GOODS_NO, $DELIVERY_CNT_IN_BOX, $GOODS_OPTION_01);

									$ARR_ORDER_GOODS_NO_TOTAL = explode(",", $ORDER_GOODS_NO_MULTI);
									for($o = 0; $o < sizeof($ARR_ORDER_GOODS_NO_TOTAL); $o ++)
									{
										$EACH_ORDER_GOODS_NO = $ARR_ORDER_GOODS_NO_TOTAL[$o];

										//updateOrderGoods_OrderGoodsDelivery($conn, $EACH_ORDER_GOODS_NO, $order_goods_delivery_no);

										$arr_ogogd[] = array("ORDER_GOODS_NO" => $EACH_ORDER_GOODS_NO, "ORDER_GOODS_DELIVERY_NO" => $order_goods_delivery_no);
									}
									$AssembleIndex++;
								}

								//완박스 싸고 남은 조립 1건
								if(($total_qty / $DELIVERY_CNT_IN_BOX) - $fullBoxCnt > 0) {

									$GOODS_DELIVERY_NAME = $GOODS_CODE.$CATE_01.$SHORT_GOODS_NAME." * ".($total_qty - ($fullBoxCnt * $DELIVERY_CNT_IN_BOX))." 개 ";
									$EVENT_ASSEMBLE_TF = 'Y';
									$ORDER_GOODS_NO_TOTAL .= $ORDER_GOODS_NO_MULTI.",";
									$GOODS_DELIVERY_EVENT_NAME_TOTAL .= $GOODS_DELIVERY_NAME."    //    ";
									$arr_ordergoods_scan[] = array("GOODS_TYPE" => $DELIVERY_PROFIT, "GOODS_NO" => $GOODS_NO, "GOODS_TOTAL" => ($total_qty - ($fullBoxCnt * $DELIVERY_CNT_IN_BOX)), "STICKER_NO" => $GOODS_OPTION_01);
								}

							} else {
								$EVENT_ASSEMBLE_TF = 'Y';
								$ORDER_GOODS_NO_TOTAL .= $ORDER_GOODS_NO_MULTI.",";
								$GOODS_DELIVERY_EVENT_NAME_TOTAL .= $GOODS_DELIVERY_NAME."    //    ";
								$arr_ordergoods_scan[] = array("GOODS_TYPE" => $DELIVERY_PROFIT, "GOODS_NO" => $GOODS_NO, "GOODS_TOTAL" => $total_qty, "STICKER_NO" => $GOODS_OPTION_01);
							}

						} else if($DELIVERY_PROFIT == "단독") {

							//단독상품만 상품명 나오도록 수정 2017-07-13

							$AssembleIndex = 1;

							if($fullBoxCnt > 0) {
								
								for($l = 1; $l <= $fullBoxCnt; $l ++)
								{
									// 주문수량이 박스입수를 넘어갔을경우 박스 수량만큼 완박스 처리
									//$DELIVERY_PROFIT_CHANGED = '완박스';
								    //$DELIVERY_PROFIT_CODE_CHANGED = getDcodeCode($conn, 'DELIVERY_PROFIT', $DELIVERY_PROFIT_CHANGED);
									
									//완박스 되었을때 물건이 10KG 초과라면
									if($DELIVERY_FEE_CODE_FULL == 'DF004') { 
										$DELIVERY_FEE = $DELIVERY_FEE_FULL;
										$DELIVERY_FEE_CODE = $DELIVERY_FEE_CODE_FULL;
									}

									$GOODS_DELIVERY_NAME = $GOODS_CODE.$CATE_01.$GOODS_NAME." ".$GOODS_SUB_NAME."[".$STICKER_NAME."]"." * "."1"." 박스(".$DELIVERY_CNT_IN_BOX."개)";

									//echo "이벤트-완박".$GOODS_DELIVERY_NAME.(($total_qty / $DELIVERY_CNT_IN_BOX) - $fullBoxCnt > 0 ? $fullBoxCnt + 1 : $fullBoxCnt)."-".$AssembleIndex."<br/>";
									//$DELIVERY_CNT_TOTAL_AND_INDEX = (($total_qty / $DELIVERY_CNT_IN_BOX) - $fullBoxCnt > 0 ? $fullBoxCnt + 1 : $fullBoxCnt)."-".$AssembleIndex;

									$order_goods_delivery_no = insertOrderDeliveryPerPaper($conn, $CP_ORDER_NO, '', '', $R_MEM_NM, $R_PHONE, $R_HPHONE, $R_ADDR1, '1', $MEMO, $O_MEM_NM, $ORDER_PHONE, $ORDER_MANAGER_NM, $ORDER_MANAGER_PHONE, $PAYMENT_TYPE, $SEND_CP_ADDR, $GOODS_DELIVERY_NAME, $DELIVERY_CP, $DELIVERY_TYPE, $DELIVERY_PROFIT, $DELIVERY_PROFIT_CODE, $DELIVERY_FEE, $DELIVERY_FEE_CODE, $s_adm_no);

									InsertOrderGoodsByDeliveryNo($conn, $order_goods_delivery_no, $GOODS_NO, $DELIVERY_CNT_IN_BOX, $GOODS_OPTION_01);

									$ARR_ORDER_GOODS_NO_TOTAL = explode(",", $ORDER_GOODS_NO_MULTI);
									for($o = 0; $o < sizeof($ARR_ORDER_GOODS_NO_TOTAL); $o ++)
									{
										$EACH_ORDER_GOODS_NO = $ARR_ORDER_GOODS_NO_TOTAL[$o];

										//updateOrderGoods_OrderGoodsDelivery($conn, $EACH_ORDER_GOODS_NO, $order_goods_delivery_no);

										$arr_ogogd[] = array("ORDER_GOODS_NO" => $EACH_ORDER_GOODS_NO, "ORDER_GOODS_DELIVERY_NO" => $order_goods_delivery_no);
									}
									$AssembleIndex++;
								}

								//완박스 싸고 남은 조립 1건
								if(($total_qty / $DELIVERY_CNT_IN_BOX) - $fullBoxCnt > 0) {

									$GOODS_DELIVERY_NAME = $GOODS_CODE.$CATE_01.$GOODS_NAME." ".$GOODS_SUB_NAME."[".$STICKER_NAME."]"." * ".($total_qty - ($fullBoxCnt * $DELIVERY_CNT_IN_BOX))." 개 ";

									//echo "단독-완박낱개".$GOODS_DELIVERY_NAME.(($total_qty / $DELIVERY_CNT_IN_BOX) - $fullBoxCnt > 0 ? $fullBoxCnt + 1 : $fullBoxCnt)."-".$AssembleIndex."<br/>";
									//$DELIVERY_CNT_TOTAL_AND_INDEX = (($total_qty / $DELIVERY_CNT_IN_BOX) - $fullBoxCnt > 0 ? $fullBoxCnt + 1 : $fullBoxCnt)."-".$AssembleIndex;

									$order_goods_delivery_no = insertOrderDeliveryPerPaper($conn, $CP_ORDER_NO, '', '', $R_MEM_NM, $R_PHONE, $R_HPHONE, $R_ADDR1, '1', $MEMO, $O_MEM_NM, $ORDER_PHONE, $ORDER_MANAGER_NM, $ORDER_MANAGER_PHONE, $PAYMENT_TYPE, $SEND_CP_ADDR, $GOODS_DELIVERY_NAME, $DELIVERY_CP, $DELIVERY_TYPE, $DELIVERY_PROFIT, $DELIVERY_PROFIT_CODE, $DELIVERY_FEE, $DELIVERY_FEE_CODE, $s_adm_no);

									InsertOrderGoodsByDeliveryNo($conn, $order_goods_delivery_no, $GOODS_NO, $DELIVERY_CNT_IN_BOX, $GOODS_OPTION_01);

									$ARR_ORDER_GOODS_NO_TOTAL = explode(",", $ORDER_GOODS_NO_MULTI);
									for($o = 0; $o < sizeof($ARR_ORDER_GOODS_NO_TOTAL); $o ++)
									{
										$EACH_ORDER_GOODS_NO = $ARR_ORDER_GOODS_NO_TOTAL[$o];

										//updateOrderGoods_OrderGoodsDelivery($conn, $EACH_ORDER_GOODS_NO, $order_goods_delivery_no);

										$arr_ogogd[] = array("ORDER_GOODS_NO" => $EACH_ORDER_GOODS_NO, "ORDER_GOODS_DELIVERY_NO" => $order_goods_delivery_no);
									}
								}

							} else {
								
								$GOODS_DELIVERY_NAME = $GOODS_CODE.$CATE_01.$GOODS_NAME." ".$GOODS_SUB_NAME."[".$STICKER_NAME."]"." * ".$total_qty." 개 ";

								//echo "단독-낱개".$GOODS_DELIVERY_NAME.(($total_qty / $DELIVERY_CNT_IN_BOX) - $fullBoxCnt > 0 ? $fullBoxCnt + 1 : $fullBoxCnt)."-".$AssembleIndex."<br/>";
								//$DELIVERY_CNT_TOTAL_AND_INDEX = (($total_qty / $DELIVERY_CNT_IN_BOX) - $fullBoxCnt > 0 ? $fullBoxCnt + 1 : $fullBoxCnt)."-".$AssembleIndex;

								$order_goods_delivery_no = insertOrderDeliveryPerPaper($conn, $CP_ORDER_NO, '', '', $R_MEM_NM, $R_PHONE, $R_HPHONE, $R_ADDR1, '1', $MEMO, $O_MEM_NM, $ORDER_PHONE, $ORDER_MANAGER_NM, $ORDER_MANAGER_PHONE, $PAYMENT_TYPE, $SEND_CP_ADDR, $GOODS_DELIVERY_NAME, $DELIVERY_CP, $DELIVERY_TYPE, $DELIVERY_PROFIT, $DELIVERY_PROFIT_CODE, $DELIVERY_FEE, $DELIVERY_FEE_CODE, $s_adm_no);

								InsertOrderGoodsByDeliveryNo($conn, $order_goods_delivery_no, $GOODS_NO, $DELIVERY_CNT_IN_BOX, $GOODS_OPTION_01);

								$ARR_ORDER_GOODS_NO_TOTAL = explode(",", $ORDER_GOODS_NO_MULTI);
								for($o = 0; $o < sizeof($ARR_ORDER_GOODS_NO_TOTAL); $o ++)
								{
									$EACH_ORDER_GOODS_NO = $ARR_ORDER_GOODS_NO_TOTAL[$o];

									//updateOrderGoods_OrderGoodsDelivery($conn, $EACH_ORDER_GOODS_NO, $order_goods_delivery_no);

									$arr_ogogd[] = array("ORDER_GOODS_NO" => $EACH_ORDER_GOODS_NO, "ORDER_GOODS_DELIVERY_NO" => $order_goods_delivery_no);
								}
							}
						} 
					}

					//조립이 한건이라도 있는 경우, 주문 수량이 한 박스를 넘지 않는 조립일 경우
					if($ASSEMBLE_TF == 'Y') {

						$GOODS_DELIVERY_NAME_TOTAL = RTRIM($GOODS_DELIVERY_NAME_TOTAL, "    //    ");
						
						//echo "여분조립 ".$GOODS_DELIVERY_NAME_TOTAL."1-1<br/>";
						
						$DELIVERY_PROFIT_CODE = 'DP001';
						$DELIVERY_FEE_CODE = 'DF001';

						$DELIVERY_PROFIT = getDcodeName($conn, 'DELIVERY_PROFIT', $DELIVERY_PROFIT_CODE);
						$DELIVERY_FEE = getDcodeName($conn, 'DELIVERY_FEE', $DELIVERY_FEE_CODE);
						
						$order_goods_delivery_no = insertOrderDeliveryPerPaper($conn, $CP_ORDER_NO, '', '', $R_MEM_NM, $R_PHONE, $R_HPHONE, $R_ADDR1, '1', $MEMO, $O_MEM_NM, $ORDER_PHONE, $ORDER_MANAGER_NM, $ORDER_MANAGER_PHONE, $PAYMENT_TYPE, $SEND_CP_ADDR, $GOODS_DELIVERY_NAME_TOTAL, $DELIVERY_CP, $DELIVERY_TYPE, $DELIVERY_PROFIT, $DELIVERY_PROFIT_CODE, $DELIVERY_FEE, $DELIVERY_FEE_CODE, $s_adm_no);

						$ORDER_GOODS_NO_TOTAL = RTRIM($ORDER_GOODS_NO_TOTAL, ",");

						$ARR_ORDER_GOODS_NO_TOTAL = explode(",", $ORDER_GOODS_NO_TOTAL);
						for($o = 0; $o < sizeof($ARR_ORDER_GOODS_NO_TOTAL); $o ++)
						{
							$EACH_ORDER_GOODS_NO = $ARR_ORDER_GOODS_NO_TOTAL[$o];

							$arr_ogogd[] = array("ORDER_GOODS_NO" => $EACH_ORDER_GOODS_NO, "ORDER_GOODS_DELIVERY_NO" => $order_goods_delivery_no);
						}


						for($g = 0; $g < sizeof($arr_ordergoods_scan); $g ++)
						{
							$each_goods = $arr_ordergoods_scan[$g];
							
							//바코드 연결 
							if($each_goods["GOODS_TYPE"] == "조립")
								InsertOrderGoodsByDeliveryNo($conn, $order_goods_delivery_no, $each_goods["GOODS_NO"], $each_goods["GOODS_TOTAL"], $each_goods["STICKER_NO"]);
						}
						 
					}

					//이벤트 낱건이 한건이라도 있는 경우, 주문 수량이 한 박스를 넘지 않는 이벤트일 경우
					if($EVENT_ASSEMBLE_TF == 'Y') {

						$GOODS_DELIVERY_EVENT_NAME_TOTAL = RTRIM($GOODS_DELIVERY_EVENT_NAME_TOTAL, "    //    ");
						
						//echo "여분이벤트 ".$GOODS_DELIVERY_EVENT_NAME_TOTAL."1-1<br/>";

						$DELIVERY_PROFIT_CODE = 'DP003';
						$DELIVERY_FEE_CODE = 'DF001';

						$DELIVERY_PROFIT = getDcodeName($conn, 'DELIVERY_PROFIT', $DELIVERY_PROFIT_CODE);
						$DELIVERY_FEE = getDcodeName($conn, 'DELIVERY_FEE', $DELIVERY_FEE_CODE);
						
						$order_goods_delivery_no = insertOrderDeliveryPerPaper($conn, $CP_ORDER_NO, '', '', $R_MEM_NM, $R_PHONE, $R_HPHONE, $R_ADDR1, '1', $MEMO, $O_MEM_NM, $ORDER_PHONE, $ORDER_MANAGER_NM, $ORDER_MANAGER_PHONE, $PAYMENT_TYPE, $SEND_CP_ADDR, $GOODS_DELIVERY_EVENT_NAME_TOTAL, $DELIVERY_CP, $DELIVERY_TYPE, $DELIVERY_PROFIT, $DELIVERY_PROFIT_CODE, $DELIVERY_FEE, $DELIVERY_FEE_CODE, $s_adm_no);

						$ORDER_GOODS_NO_TOTAL = RTRIM($ORDER_GOODS_NO_TOTAL, ",");

						$ARR_ORDER_GOODS_NO_TOTAL = explode(",", $ORDER_GOODS_NO_TOTAL);
						for($o = 0; $o < sizeof($ARR_ORDER_GOODS_NO_TOTAL); $o ++)
						{
							$EACH_ORDER_GOODS_NO = $ARR_ORDER_GOODS_NO_TOTAL[$o];

							//updateOrderGoods_OrderGoodsDelivery($conn, $EACH_ORDER_GOODS_NO, $order_goods_delivery_no);

							$arr_ogogd[] = array("ORDER_GOODS_NO" => $EACH_ORDER_GOODS_NO, "ORDER_GOODS_DELIVERY_NO" => $order_goods_delivery_no);
						}

						for($g = 0; $g < sizeof($arr_ordergoods_scan); $g ++)
						{
							$each_goods = $arr_ordergoods_scan[$g];

							if($each_goods["GOODS_TYPE"] == "이벤트")
								InsertOrderGoodsByDeliveryNo($conn, $order_goods_delivery_no, $each_goods["GOODS_NO"], $each_goods["GOODS_TOTAL"], $each_goods["STICKER_NO"]);
						}
					}

					$arr_rs3 = null;
					for($o = 0; $o < sizeof($arr_ogogd); $o ++)
					{
						$each = $arr_ogogd[$o];

						//echo $each["ORDER_GOODS_NO"]." : ".$each["ORDER_GOODS_DELIVERY_NO"]."<br/>";
						updateOrderGoods_OrderGoodsDelivery($conn, $each["ORDER_GOODS_NO"], $each["ORDER_GOODS_DELIVERY_NO"]);

					}

				}
			}
		}

?>	
<script language="javascript">
		alert('송장생성 완료 되었습니다.');
</script>
<?
	}

	if($mode == "DELETE_DELIVERY_PAPER") {
		deleteOrderDeliveryForMartAll($conn, $start_date, $end_date, $cp_no, $s_adm_no);
?>	
<script language="javascript">
		alert('삭제처리 완료 되었습니다.');
</script>
<?
	}

	if ($mode == "INSERT_DELIVERY_SEQ") {
				
			$today = date("Y-m-d",strtotime("0 month"));
			$arr = listOrderDeliveryForMart_LEVEL3($conn, $start_date, $end_date, "2", $cp_no); // order_state = 주문확인

			if (sizeof($arr) > 0) {
				for ($j = 0 ; $j < sizeof($arr); $j++) {

					$ORDER_GOODS_DELIVERY_NO = trim($arr[$j]["ORDER_GOODS_DELIVERY_NO"]);
					updateOrderGoodsDeliveryPaperSeq($conn, $ORDER_GOODS_DELIVERY_NO, $today, $seed_number);

				}
			}
?>	
<script language="javascript">
		alert('순번입력 완료 되었습니다.');
</script>
<?
	}


	if ($mode == "FU") {
		#====================================================================
			$savedir1 = $g_physical_path."upload_data/temp_delivery_seq";
		#====================================================================

			$file_nm	= upload($_FILES[file_nm], $savedir1, 10000 , array('xls'));

			require_once '../../_excel_reader/Excel/reader.php';
			$data = new Spreadsheet_Excel_Reader();
			$data->setOutputEncoding('euc-kr');
			//$data->read('test.xls');
			$data->read("../../upload_data/temp_delivery_seq/".$file_nm);
		
			error_reporting(E_ALL ^ E_NOTICE);

			for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {

				//현대택배 
				$TEMP_DELIVERY_NO	= SetStringToDB(trim($data->sheets[0]['cells'][$i][4]));
				$TEMP_DELIVERY_SEQ	= SetStringToDB(trim($data->sheets[0]['cells'][$i][5]));
				$delivery_extra_fee	= SetStringToDB(trim($data->sheets[0]['cells'][$i][35]));

				//CJ택배
				//$TEMP_DELIVERY_NO	= SetStringToDB(trim($data->sheets[0]['cells'][$i][8]));
				//$TEMP_DELIVERY_SEQ	= SetStringToDB(trim($data->sheets[0]['cells'][$i][19]));
				//$delivery_extra_fee	= SetStringToDB(trim($data->sheets[0]['cells'][$i][17]));

				$TEMP_DELIVERY_NO = str_replace("-", "", $TEMP_DELIVERY_NO);
				
				if($TEMP_DELIVERY_SEQ <> "" && $TEMP_DELIVERY_NO <> "")
					updateOrderGoodsDeliveryNumber($conn, $TEMP_DELIVERY_SEQ, '', $TEMP_DELIVERY_NO, $delivery_extra_fee);
			}
?>	
<script language="javascript">
		alert('송장 완료파일 입력완료 되었습니다.');
</script>
<?
	}

	
	if ($mode == "FU2") {
		#====================================================================
			$savedir2 = $g_physical_path."upload_data/temp_delivery_pickup";
		#====================================================================

			$file_nm2	= upload($_FILES[file_nm2], $savedir2, 10000 , array('xls'));

			require_once '../../_excel_reader/Excel/reader.php';
			$data = new Spreadsheet_Excel_Reader();
			$data->setOutputEncoding('euc-kr');
			//$data->read('test.xls');
			$data->read("../../upload_data/temp_delivery_pickup/".$file_nm2);
		
			error_reporting(E_ALL ^ E_NOTICE);

			 
			for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {

				//CJ기준
				//$TEMP_DELIVERY_NO	= SetStringToDB(trim($data->sheets[0]['cells'][$i][2]));
				//$TEMP_DELIVERY_SEQ	= SetStringToDB(trim($data->sheets[0]['cells'][$i][19]));
				//$TEMP_SENT_DATE	    = SetStringToDB(trim($data->sheets[0]['cells'][$i][12]));

				//현대기준
				$TEMP_DELIVERY_NO	= SetStringToDB(trim($data->sheets[0]['cells'][$i][6]));
				$TEMP_DELIVERY_SEQ	= SetStringToDB(trim($data->sheets[0]['cells'][$i][3]));
				$TEMP_SENT_DATE	    = SetStringToDB(trim($data->sheets[0]['cells'][$i][2]));

				$TEMP_DELIVERY_NO = str_replace("-", "", $TEMP_DELIVERY_NO);

				//echo $TEMP_SENT_DATE." / ".$TEMP_DELIVERY_SEQ." / ".$TEMP_DELIVERY_NO."<br/>";
				//continue;

				if($TEMP_DELIVERY_SEQ <> "" && $TEMP_DELIVERY_NO <> "" && $TEMP_SENT_DATE <> "") {

					//$sent_date =  substr($TEMP_SENT_DATE, 0, 4)."-".substr($TEMP_SENT_DATE, 4, 2)."-".substr($TEMP_SENT_DATE, 6, 2);
					updateOrderGoodsDeliveryNumberComplete($conn, $TEMP_DELIVERY_SEQ, $TEMP_DELIVERY_NO, $TEMP_SENT_DATE);
					
					//출고처리
					$arr_delivery_paper = listOrderGoodsDeliveryNumberComplete($conn, $TEMP_DELIVERY_SEQ, $TEMP_DELIVERY_NO);
					
					$order_goods_delivery_no = $arr_delivery_paper[0]["ORDER_GOODS_DELIVERY_NO"];
					$cp_order_no			 = $arr_delivery_paper[0]["CP_ORDER_NO"];
					$order_manager_nm		 = $arr_delivery_paper[0]["ORDER_MANAGER_NM"];
					$delivery_claim_code	 = $arr_delivery_paper[0]["DELIVERY_CLAIM_CODE"];

					$cp_no = getCompanyNoAsName($conn, $order_manager_nm);
					$reserve_no = selectReserveNoByCompanyOrderNo($conn, $cp_order_no);

					if($order_goods_delivery_no == "0") continue; //에러로 생성된 송장이 생길시 제외
					
					$arr_order_goods = selectOrderDeliveryGoods($conn, $order_goods_delivery_no);

					if (sizeof($arr_order_goods) > 0) {
						
						/*
						// 송장 집하시 재고 차감에서 작업완료시 재고 차감으로 변경  

						for ($j = 0 ; $j < sizeof($arr_order_goods); $j++) {
							$GOODS_NO			= trim($arr_order_goods[$j]["GOODS_NO"]);
							$GOODS_TOTAL		= trim($arr_order_goods[$j]["GOODS_TOTAL"]);

							//echo $reserve_no."/".$cp_order_no."/".$GOODS_NO."/".$GOODS_TOTAL."<br/>";

							$stock_type     = "OUT";         //입출고 구분 (출고) 
							$stock_code     = "NOUT01";      //출고 구분코드
							$in_cp_no		= "";	         // 입고 업체
							$out_cp_no	    = $cp_no;        // 출고업체

							$goods_no		= $GOODS_NO; //출고상품 ** 세트인 경우 해당 세트에 상품 수 만큼 각 각 처리해야 함
							$in_loc			= ($delivery_claim_code == "" ? "LOCE" : "LOCF");        // 창고 구분 디폴트 창고 A, 클레임 있을시 B
							$in_loc_ext	    = $TEMP_DELIVERY_NO;
							$in_qty			= 0;
							$in_bqty		= 0;
							$in_fbqty		= 0;
							$out_qty		= $GOODS_TOTAL; //구성품 수량
							$out_bqty		= 0;
							$out_fbqty	    = 0;
							$in_price		= 0;
							$out_price	    = 0;     
							$in_date		= "";
							$out_date		= $TEMP_SENT_DATE;
							$pay_date		= "";
							//$reserve_no	    = selectOrderGoodsNo($conn, $order_goods_delivery_no);
							$close_tf		= "N";
							$memo			= "";

							$out_result = insertStock($conn, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_fqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $close_tf, $s_adm_no, $memo);

						}

						*/
						updateOrderGoodsDeliveryPaperOutStockStatus($conn, $order_goods_delivery_no, 'Y');
						
					}
					
				}
				
			}
?>	
<script language="javascript">
		alert('집하 완료파일 입력완료 되었습니다.');
</script>
<?
	}

#====================================================================
# Request Parameter
#====================================================================


#============================================================
# Page process
#============================================================

	$this_date = date("Y-m-d",strtotime("0 month"));
	$prev_date = date("Y-m-d",strtotime("0 month"));
	

	if($end_date == "")
		$end_date = $this_date;

	if($start_date == "")
		$start_date = $prev_date;

#===============================================================
# Get Search list count
#===============================================================

	if($cp_no <> '')
		$arr_rs = listOrderDeliveryForMart_LEVEL1_From_To($conn, $start_date, $end_date, '', $cp_no); //order_state = all

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script type="text/javascript" src="../jquery/jquery.cookie.js"></script>

<script>
  $(function() {
    $( ".datepicker" ).datepicker({
      buttonImage: "/manager/images/calendar/cal.gif",
      buttonImageOnly: true,
      buttonText: "Select date",
	  showOn: "both",
	  dateFormat: "yy-mm-dd",
	  changeMonth: true,
      changeYear: true
    });

	$(".datepicker").keydown(function(){

		var value = $(this).val();

		if(value.length == 4 && value.lastIndexOf('-') == -1)
			$(this).val(value.substr(0, 4)+ "-" + value.substr(4)) ;

		if(value.length == 7 && value.lastIndexOf('-') == 4)
			$(this).val(value.substr(0, 8) + "-" + value.substr(8)) ;
	});

	/*
	$(".datepicker").blur(function(){
		if($(this).val().length > 8)
			checkDt($("input[name=specific_date]"));
	}); 
	*/
  });
</script>
<script language="javascript">

	function js_view(rn, reserve_no) {

		var frm = document.frm;
		
		var url = "../order/order_read.php?reserve_no="+reserve_no;

		NewWindow(url, 'order_detail','860','600','YES');
		
	}

	function js_trace(url) {

		window.open(url);
		//alert(url);
	}

	// 조회 버튼 클릭 시 
	function js_search() {
		var frm = document.frm;
		
		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_save_seq(btn) {

		btn.style.visibility = 'hidden';
		
		var frm = document.frm;

		if (isNull(frm.file_nm.value)) {
			alert('파일을 선택해 주세요.');
			btn.style.visibility = 'visible';
			frm.file_nm.focus();
			return ;		
		}
		
		if (!AllowAttach(frm.file_nm))
		{
			btn.style.visibility = 'visible';
			frm.file_nm.focus();
			return ;
		}

		frm.mode.value = "FU";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_save_pickup(btn) {

		btn.style.visibility = 'hidden';
		
		var frm = document.frm;

		if (isNull(frm.file_nm2.value)) {
			alert('파일을 선택해 주세요.');
			btn.style.visibility = 'visible';
			frm.file_nm2.focus();
			return ;		
		}
		
		if (!AllowAttach(frm.file_nm2))
		{
			btn.style.visibility = 'visible';
			frm.file_nm2.focus();
			return ;
		}

		frm.mode.value = "FU2";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_pop_delivery_list() {

		var frm = document.frm;
		
		var url = "<?=str_replace("list_mart","excel_list_mart",$_SERVER[PHP_SELF])?>?mode=pop&start_date=<?=$start_date?>&end_date=<?=$end_date?>";

		NewWindow(url, 'excel_list_mart','860','600','YES');
		
	}

	function js_delivery_excel() {

		var frm = document.frm;
		
		frm.target = "";
		frm.mode.value = "excel";
		frm.action = "<?=str_replace("list_mart","excel_list_mart",$_SERVER[PHP_SELF])?>";
		frm.submit();

	}
	
	function js_delivery_paper_confirm(btn) {

		btn.style.visibility = 'hidden';

		var frm = document.frm;
		
		frm.mode.value = "DECIDE_DELIVERY_PAPER";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	
	}

	function js_delivery_paper_delete() {
		if(confirm("출고번호와 송장번호가 없는 송장 전체가 삭제됩니다. 진행하시겠습니까?"))
		{
			var frm = document.frm;
			
			frm.mode.value = "DELETE_DELIVERY_PAPER";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_append_delivery_paper(order_goods_delivery_no, delivery_seq) {

		var row = "#row_"+order_goods_delivery_no;

		(function() {
		  $.getJSON( "json_delivery_paper.php", {
			mode: "APPEND_DELIVERY_PAPER",
			this_date: "<?=$this_date?>",
			order_goods_delivery_no: order_goods_delivery_no,
			delivery_seq_tf: (delivery_seq != "" ? 'Y': 'N'),
			s_adm_no: "<?=$s_adm_no?>"
		  })
			.done(function( data ) {
			  $.each( data, function( i, item ) {
				
				$(row).after("<tr id='row_" + item.ORDER_GOODS_DELIVERY_NO + "'><td><a href='javascript:js_update_delivery_paper("+item.ORDER_GOODS_DELIVERY_NO+");'>"+item.DELIVERY_SEQ+"</a></td><td>"+item.DELIVERY_NO+"</td><td><a href='javascript:js_update_delivery_paper("+item.ORDER_GOODS_DELIVERY_NO+");'>"+item.GOODS_DELIVERY_NAME+"</a></td><td>"+item.DELIVERY_PROFIT+"</td><td>"+item.DELIVERY_FEE+"</td><td><input type='button' name='a0' value=' 추가 ' class='btntxt' onclick='js_append_delivery_paper(" + item.ORDER_GOODS_DELIVERY_NO + ");'><input type='button' name='a1' value=' 삭제 ' class='btntxt' onclick='js_delete_delivery_paper(" + item.ORDER_GOODS_DELIVERY_NO + ");'></td></tr>");
			  });
			});
		})();
	
	}

	function js_delete_delivery_paper(order_goods_delivery_no) {
	
		if(confirm('정말로 송장을 삭제하시겠습니까?'))
		{
			var row = "#row_"+order_goods_delivery_no;

			(function() {
			  $.getJSON( "json_delivery_paper.php", {
				mode: "DELETE_DELIVERY_PAPER",
				order_goods_delivery_no: order_goods_delivery_no,
				s_adm_no: "<?=$s_adm_no?>"
			  })
				.done(function( data ) {
				  $.each( data, function( i, item ) {
					
					if(item.RESULT == "1")
						$(row).remove();

				  });
				});
			})();
		}
	}

	function js_update_delivery_paper(order_goods_delivery_no) {

		var url = "../order/pop_delivery_paper_detail.php?order_goods_delivery_no=" + order_goods_delivery_no;

		NewWindow(url, 'delivery_paper_detail','1000','500','YES');
		
	}


	function js_order_seq_confirm(btn) {

		btn.style.visibility = 'hidden';

		var frm = document.frm;
		
		frm.mode.value = "INSERT_DELIVERY_SEQ";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	
	}


	function js_excel_for_warehouse() {
		
		var frm = document.frm;

		$.cookie('start_name', $("input[name=start_name]").val()); 
		$.cookie('end_name', $("input[name=end_name]").val()); 
		
		frm.target = "";
		frm.action = "<?=str_replace("list_mart","excel_for_warehouse_mart",$_SERVER[PHP_SELF])?>";
		frm.submit();
	}

	function LimitAttach(obj) {
		var file = obj.value;
		extArray = new Array(".jsp", ".cgi", ".php", ".asp", ".aspx", ".exe", ".com", ".php3", ".inc", ".pl", ".asa", ".bak");
		allowSubmit = false;
		
		if (!file){
			//form1.submit();
		}

		while (file.indexOf("\\") != -1){
			file = file.slice(file.indexOf("\\") + 1);
			ext = file.slice(file.lastIndexOf(".")).toLowerCase();

			for (var i = 0; i < extArray.length; i++){
				if (extArray[i] == ext){ 
					allowSubmit = true; 
					break; 
				}
			}
		}

		if (!allowSubmit){
			return true;
		}else{
			alert("입력하신 파일은 업로드 될 수 없습니다!");
			return false;
		}
	}

	function AllowAttach(obj) {
		var file = obj.value;
		extArray = new Array(".xls");
		allowSubmit = false;
		
		if (!file){
			//form1.submit();
		}

		while (file.indexOf("\\") != -1){
			file = file.slice(file.indexOf("\\") + 1);
			ext = file.slice(file.lastIndexOf(".")).toLowerCase();

			for (var i = 0; i < extArray.length; i++){
				if (extArray[i] == ext){ 
					allowSubmit = true; 
					break; 
				}
			}
		}

		if (allowSubmit){
			return true;
		}else{
			alert("입력하신 파일은 업로드 될 수 없습니다!");
			return false;
		}
	}

	function js_reload() {
		//window.location.reload();
	}

</script>
<style type="text/css">
<!--
/*#pop_table {z-index: 1; left: 80; overflow: auto; width: 500; height: 220}*/
#ex_scroll { z-index: 1; background-color:#f7f7f7; overflow: auto; width: 100%; height:155px; border:1px solid #d1d1d1;}
#temp_scroll { z-index: 1; background-color:#f7f7f7; overflow: auto; width: 95%; height:100%; border:1px solid #d1d1d1;}
-->
</style>
</head>

<body id="admin">
<form name="frm" method="post" enctype="multipart/form-data" action="javascript:js_search();">
<input type="hidden" name="mode" value="">
<input type="hidden" name="total_cnt" value="<?=sizeof($arr_rs)?>">
<input type="hidden" name="order_goods_delivery_no" value="">

<div id="adminwrap">

<?
	#====================================================================
	# common top_area
	#====================================================================

	require "../../_common/top_area.php";
?>

	<table width="100%" cellpadding="0" cellspacing="0">
	<colgroup>
		<col width="180" />
		<col width="*" />
	</colgroup>
	<tr>
		<td class="leftarea">
<?
	#====================================================================
	# common left_area
	#====================================================================

	require "../../_common/left_area.php";
?>


		</td>
		<td class="contentarea">

			<!-- S: mwidthwrap -->
			<div id="mwidthwrap">

				<h2>마트 작업 & 출고 리스트</h2>

				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="15%" />
					<col width="30%" />
					<col width="15%" />
					<col width="30%" />
					<col width="10%" />
				</colgroup>
				<tbody>
					<tr>
						<th>주문회사</th>
						<td>
							<?
							    $arr_result = listCompanyBy($conn);
							?>
							<?=makeGenericSelectBox($conn, $arr_result, 'cp_no', '100', '선택', '', $cp_no, 'CP_NO', 'CP_NM')?>
						</td>
						<th>검색일</th>
						<td colspan="2">
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="start_date" value="<?=$start_date?>" maxlength="10"/> ~ 
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="end_date" value="<?=$end_date?>" maxlength="10"/>
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"></a>
						</td>
					</tr>
					<tr>
						<th>송장생성</th>
						<td>
							<a href="#" onclick="js_delivery_paper_confirm(this); return false;"><img src="../images/admin/btn_confirm.gif" alt="확인"></a> &nbsp;
							<a href="javascript:js_delivery_paper_delete();"><img src="../images/admin/btn_delete.gif" alt="삭제"></a>
						</td>
						<th>순번입력</th>
						<td>
							<a href="#" onclick="js_order_seq_confirm(this); return false;"><img src="../images/admin/btn_confirm.gif" alt="확인"></a>
						</td>
						<td>
						</td>
					</tr>
					<tr>
						<th>송장로딩엑셀다운</th>
						<td colspan="4">
							<a href="javascript:js_delivery_excel();"><img src="../images/common/btn/btn_excel.gif" alt="택배 송장출력용 엑셀" /></a>
						</td>
					</tr>
					<tr>
						<th>송장 완료파일 입력</th>
						<td colspan="4">
							<input type="file" name="file_nm" style="width:60%;" class="txt">
							<a href="#" onclick="js_save_seq(this); return false;"><img src="../images/admin/btn_confirm.gif" alt="확인"></a>
						</td>
					</tr>
					<tr>
						<th>집하 완료파일 입력</th>
						<td colspan="4">
							<input type="file" name="file_nm2" style="width:60%;" class="txt">
							<a href="#" onclick="js_save_pickup(this); return false;"><img src="../images/admin/btn_confirm.gif" alt="확인"></a>
						</td>
					</tr>
				</tbody>
			</table>
			
			<div style="width: 95%; text-align: right; margin: 10px 0 10px 0;">
				<!--
				<b>* 송장번호가 입력된 </b><label>출고번호</label>
				<input type="text" name="from_seq_of_day" value="<?=$seed_number?>" style="width:60px;"/>번 부터
				<input type="text" name="to_seq_of_day" value="" style="width:60px;"/>번 사이
				<select name="combined_type">
					<option value="">전체</option>
					<option value="0">박스단위</option>
					<option value="1">낱개</option>
					<option value="2">합포</option>
					<option value="3">낱개+합포</option>
				</select>
				<a href="javascript:js_excel_for_warehouse();">창고 작업 준비 품목 리스트 <img src="../images/common/btn/btn_excel.gif" alt="창고 작업 준비 품목 리스트" /> </a>
				<br/>
				<label><input type="checkbox" name="has_island" <?=($has_island ? "checked='checked'" : "")?>/> 제주도 및 도서산간 포함</label>
				-->
				주문자 <input type="text" name="start_name" value="" style="width:100px;"/>님 부터 <input type="text" name="end_name" value="" style="width:100px;"/>님 까지 합산리스트 출력<a href="javascript:js_excel_for_warehouse();"><img src="../images/common/btn/btn_excel.gif" alt="창고 작업 준비 품목 리스트" /> </a>

				<script>
							
					if($.cookie('start_name') != "") { 
						$("input[name=start_name]").val($.cookie('start_name'));
					} 
					if($.cookie('end_name') != "") { 
						$("input[name=end_name]").val($.cookie('end_name'));
					} 
				
				</script>
			</div>
 
 			<table cellpadding="0" cellspacing="0" class="rowstable03" border="0" style="width:95%" >
				<colgroup>
					<col width="6%" />
					<col width="10%" />
					<col width="*" />
					<col width="5%" />
					<col width="6%" />
					<col width="7%" />
					<col width="9%" />
					<col width="22%"/>
					<col width="4%" />
					<col width="4%" />
					<col width="5%" />
				</colgroup>
				<thead>
					<tr>
						<th>주문자명</th>
						<th>수령자명/주소</th>
						<th>상품명</th>
						<th>구성/주문/박스입수</th>
						<th>주문상태</th>
						<th>출고번호</th>
						<th>송장번호</th>
						<th>구성상품명</th>
						<th>형태</th>
						<th>비용</th>
						<th class="end">송장추가</th>
					</tr>
				</thead>
			</table>
			<div id="temp_scroll" style="height:600px;">

				<table cellpadding="0" cellspacing="0" class="rowstable03" border="0" style="width:100%" >
					<colgroup>
						<col width="6%" />
						<col width="10%" />
						<col width="*" />
						<col width="5%" />
						<col width="6%" />
						<col width="7%" />
						<col width="9%" />
						<col width="22%"/>
						<col width="4%" />
						<col width="4%" />
						<col width="5%" />
					</colgroup>
					<tbody>
					<?
					
						$delivery_paper_cnt = 0;

						if (sizeof($arr_rs) > 0) {
							for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
								
								$CP_NO		    = trim($arr_rs[$j]["CP_NO"]);
								$R_ADDR1      = trim($arr_rs[$j]["R_ADDR1"]);
								$R_MEM_NM    = trim($arr_rs[$j]["R_MEM_NM"]);
								$O_MEM_NM    = trim($arr_rs[$j]["O_MEM_NM"]);

								if(strpos($R_ADDR1,'제주시') !== false || strpos($R_ADDR1,'옹진군') !== false || strpos($R_ADDR1,'서귀포시') !== false || strpos($R_ADDR1,'울릉군') !== false)
								{
									$island_color = "style='background-color:yellow;'";
								}else
									$island_color = "";


					?>
						<tr <?=$island_color?>>
							<td>
								<?=($j+1)?>. <?=$O_MEM_NM?>
							</td>
							<td><?=$R_MEM_NM?><br/><br/><?=$R_ADDR1?></td>
							
							<td colspan="3">
								<table cellpadding="0" cellspacing="0" class="innertable" border="0" >
								<colgroup>
									<col width="*" />
									<col width="15%" />
									<col width="20%" />
								</colgroup>
								<tbody>
								<?
									$R_ADDR1      = SetStringToDB(trim($arr_rs[$j]["R_ADDR1"]));
				
									$arr_rs2 = listOrderDeliveryForMart_LEVEL2_From_To($conn, $start_date, $end_date, $CP_NO, $O_MEM_NM, $R_MEM_NM, $R_ADDR1, ''); 
								
									$ORDER_GOODS_NO_TOTAL = '';
									if (sizeof($arr_rs2) > 0) {
										for ($k = 0 ; $k < sizeof($arr_rs2); $k++) {

											//OG.GOODS_OPTION_01, OG.OPT_STICKER_MSG, OG.OPT_WRAP_NO, OG.OPT_PRINT_MSG, OG.OPT_OUTBOX_TF,

											$ORDER_GOODS_NO		   = trim($arr_rs2[$k]["ORDER_GOODS_NO"]);										
											$ORDER_GOODS_NO_TOTAL .= $ORDER_GOODS_NO.",";

											$GOODS_NO				= trim($arr_rs2[$k]["GOODS_NO"]);
											$GOODS_CODE				= trim($arr_rs2[$k]["GOODS_CODE"]);
											$GOODS_NAME				= trim($arr_rs2[$k]["GOODS_NAME"]);
											$QTY					= trim($arr_rs2[$k]["QTY"]);
											$GOODS_CNT		        = trim($arr_rs2[$k]["GOODS_CNT"]);
											$RESERVE_NO				= trim($arr_rs2[$k]["RESERVE_NO"]);
											$ORDER_STATE			= trim($arr_rs2[$k]["ORDER_STATE"]);											
											$DELIVERY_CNT_IN_BOX	= trim($arr_rs2[$k]["DELIVERY_CNT_IN_BOX"]);
											
											$GOODS_OPTION_01		    = trim($arr_rs2[$k]["GOODS_OPTION_01"]);
											$GOODS_OPTION_02		    = trim($arr_rs2[$k]["GOODS_OPTION_02"]);
											$GOODS_OPTION_03		    = trim($arr_rs2[$k]["GOODS_OPTION_03"]);

											$GOODS_STATE           = trim($arr_rs2[$k]["CATE_04"]);
											if($GOODS_STATE != '판매중' && $GOODS_STATE != '재판매')
												$style_goods_state = 'style="background-color:red;"';
											else
												$style_goods_state = '';
											
											$fullBoxCnt = floor($QTY /  $DELIVERY_CNT_IN_BOX);
											
											if($ORDER_STATE == "6" || $ORDER_STATE == "7" || $ORDER_STATE == "8" || $ORDER_STATE == "9")
												$other_state = 'style="background-color:lightpink;"';
											else
												$other_state = '';

								?>
								<tr <?=$other_state?>>
									<td <?=$style_goods_state?>><a href="javascript:js_view('<?=$rn?>','<?=$RESERVE_NO?>');"><?=$GOODS_NAME?> <br/> <b><?=$GOODS_OPTION_02?></b></a></td>
									<td><?=$GOODS_CNT."/<b>".$QTY."</b>/".$DELIVERY_CNT_IN_BOX?></td>
									<td><?=getDcodeName($conn, 'ORDER_STATE', $ORDER_STATE)?></td>
								</tr>
								
								<?		}
									}
								?>
								</tbody>
								</table>
							</td>
							<td colspan="6">
									<table cellpadding="0" cellspacing="0" class="innertable" border="0">
										<colgroup>
											<col width="14%" />
											<col width="16%" />
											<col width="*"/>
											<col width="8%" />
											<col width="8%" />
											<col width="10%" />
										</colgroup>
										<tbody>
										<?
											$ORDER_GOODS_NO_TOTAL  = RTRIM($ORDER_GOODS_NO_TOTAL, ',');
											$arr_rs3 = listOrderDeliveryForMart($conn, $ORDER_GOODS_NO_TOTAL);
											
											if (sizeof($arr_rs3) > 0) {
												for ($m = 0 ; $m < sizeof($arr_rs3); $m++) {

													//echo $m;
												
													$ORDER_GOODS_DELIVERY_NO   = trim($arr_rs3[$m]["ORDER_GOODS_DELIVERY_NO"]);
													$DELIVERY_SEQ		       = trim($arr_rs3[$m]["DELIVERY_SEQ"]);
													$DELIVERY_CP		       = trim($arr_rs3[$m]["DELIVERY_CP"]);
													$DELIVERY_NO		       = trim($arr_rs3[$m]["DELIVERY_NO"]);
													$RECEIVER_PHONE		       = trim($arr_rs3[$m]["RECEIVER_PHONE"]);
													$RECEIVER_HPHONE		   = trim($arr_rs3[$m]["RECEIVER_HPHONE"]);
													$GOODS_DELIVERY_NAME	   = trim($arr_rs3[$m]["GOODS_DELIVERY_NAME"]);
													$DELIVERY_PROFIT		   = trim($arr_rs3[$m]["DELIVERY_PROFIT"]);
													$DELIVERY_FEE		       = trim($arr_rs3[$m]["DELIVERY_FEE"]);
													$DELIVERY_DATE		       = trim($arr_rs3[$m]["DELIVERY_DATE"]);
													$MEMO		               = trim($arr_rs3[$m]["MEMO"]);
													$USE_TF		               = trim($arr_rs3[$m]["USE_TF"]);
													
													if($DELIVERY_DATE == "0000-00-00")
														$DELIVERY_DATE = "";

													$delivery_paper_cnt ++;
													
													$style_done = "";
													if($DELIVERY_DATE != "")
														$style_done = "style='background-color:#E9FFE9;'";
													
													if ($USE_TF == "N")
														$style_done = "style='background-color:#cccccc;'";

										?>
										<tr id="row_<?=$ORDER_GOODS_DELIVERY_NO?>" <?=$style_done?>>
											<td><a href="javascript:js_update_delivery_paper('<?=$ORDER_GOODS_DELIVERY_NO?>')"><?=$DELIVERY_SEQ?></a></td>
											<?
												if ($DELIVERY_NO) {
															
															//echo $DELIVERY_CP;
															$trace = getDeliveryUrl($conn, $DELIVERY_CP);
															$trace = $trace.$DELIVERY_NO;
														}
											
											?>
											<td><a href="javascript:js_trace('<?=$trace?>');" style="font-weight:bold;"><?=$DELIVERY_NO?></a>
											</td>
											<td>
												<a href="javascript:js_update_delivery_paper('<?=$ORDER_GOODS_DELIVERY_NO?>')"><?=$GOODS_DELIVERY_NAME?></a>
											</td>
											<td>
												<?=$DELIVERY_PROFIT?>
											</td>
											<td>
												<?=$DELIVERY_FEE?>
											</td>
											<td>
												<input type="button" name="a0" value=" 추가 " class="btntxt" onclick="js_append_delivery_paper('<?=$ORDER_GOODS_DELIVERY_NO?>', '<?=$DELIVERY_SEQ?>');">
											
											<? if(($DELIVERY_DATE == "0000-00-00" || $DELIVERY_DATE == "")) {
											?>

												<input type="button" name="a1" value=" 삭제 " class="btntxt" onclick="js_delete_delivery_paper('<?=$ORDER_GOODS_DELIVERY_NO?>');">

											<? }
											?>	
												
											</td>
										</tr>
										<?


												}
											}

										 ?>
										</tbody>
										</table>
							
							
							</td>
						</tr>

					<?	}
					}
					?>
						</tbody>
					</table>
				</div>
				<div class="sp10"></div>
				<div style="width: 95%; text-align: right; margin: 10px 0 10px 0;">
					총 송장 수 : <b><?=$delivery_paper_cnt?></b>
				</div>
			</div>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2" height="70"><div class="copyright"><img src="../images/admin/copyright.gif" alt="" /></div></td>
	</tr>
	</table>

</div>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>