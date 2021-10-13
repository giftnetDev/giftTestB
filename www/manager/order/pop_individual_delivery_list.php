<?session_start();?>
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
	require "../../_classes/biz/company/company.php";


#====================================================================
# Request Parameter
#====================================================================

	//	송장생성
	if ($mode == "I") {

		$result = listDeliveryIndividual($conn, $order_goods_no, "ASC");
		

		$arr_error = array();

		for($k = 0; $k < sizeof($result); $k ++) { 
			$error_msg_each = "";
			
			$individual_no  = $result[$k]["INDIVIDUAL_NO"];
			$work_seq		= $result[$k]["WORK_SEQ"];

			$DELIVERY_FEE_CODE = $DELIVERY_CP."-보통";
			$DELIVERY_FEE = getDcodeName($conn, "DELIVERY_FEE", $DELIVERY_FEE_CODE); 

			//2016-11-24 보내는곳과 운영업체 이름이 같을 시 운영업체 주소를, 다를시 주소에서 업체명 제거
			//$CON_SEND_CP_ADDR = "경기 남양주시 진건읍 배양리 98번지 ㈜기프트넷";
			$arr_op_cp = getOperatingCompany($conn, $s_adm_com_code);

			$OP_CP_NM		= $arr_op_cp[0]["CP_NM"];
			$OP_CP_ADDR		= $arr_op_cp[0]["CP_ADDR"];

			if($SENDER_NM == $OP_CP_NM)
				$CON_SEND_CP_ADDR = $OP_CP_ADDR;
			else
				$CON_SEND_CP_ADDR = str_replace($OP_CP_NM, "", $OP_CP_ADDR);
			
			//택배회사 없을땐 송장생성 불가
			if($DELIVERY_CP == "" || $DELIVERY_FEE == "")   
				$error_msg_each = "택배회사/비용 없음,";

			//개별택배의 개별주소가 입력되지 않았을때 송장생성 불가
			if($DELIVERY_TYPE == "3" && ($individual_no == null || $individual_no == ""))   
				$error_msg_each = "개별택배 배송처 없음,";

			//직접수령, 퀵서비스, 외부업체발송, 기타는 송장 생성 안함
			if($DELIVERY_TYPE == "1" || $DELIVERY_TYPE == "2" || $DELIVERY_TYPE == "98" || $DELIVERY_TYPE == "99") 
				$error_msg_each = "개별택배,택배외 상품,";

			if($error_msg_each != "") { 
				array_push($arr_error, array('INDIVIDUAL_NO' => $individual_no, 'ERROR_MSG_EACH' => rtrim($error_msg_each, ",")));
				continue;

			}

			// 이미 생성된 송장이 있음
			if(countOrderDeliveryPaper($conn, $order_goods_no, $individual_no) <= 0)
			{

				$arr_order = selectOrder($conn, $reserve_no);

				$GOODS_DELIVERY_NAME = "";
				$SUB_QTY = "";
				$MEMO_ALL = "취급주의 제품입니다-인박스가 훼손되니 던지지 말아주세요~";

				$CP_NO = SetStringFromDB($arr_order[0]["CP_NO"]);

				if($individual_no == "") { 
					$R_MEM_NM = $arr_order[0]["R_MEM_NM"];
					$R_PHONE  = $arr_order[0]["R_PHONE"];
					$R_HPHONE = $arr_order[0]["R_HPHONE"];
					$R_ADDR1  = $arr_order[0]["R_ADDR1"];
					$MEMO	  = $arr_order[0]["MEMO"];
				
				} else {
					$arr_individual = selectDeliveryIndividual($conn, $individual_no);
			
					$R_MEM_NM				= $arr_individual[0]["R_MEM_NM"];
					$R_PHONE				= $arr_individual[0]["R_PHONE"];
					$R_HPHONE				= $arr_individual[0]["R_HPHONE"];
					$R_ADDR1				= $arr_individual[0]["R_ADDR1"];
					$MEMO					= $arr_individual[0]["MEMO"];
					$INDIVIDUAL_DELIVERY_TYPE = $arr_individual[0]["DELIVERY_TYPE"];
					$USE_TF					= $arr_individual[0]["USE_TF"];

					//사용안하면 패스
					if($USE_TF != "Y")
						continue;

					//택배가 아니므로 패스
					if($INDIVIDUAL_DELIVERY_TYPE != "0")
						continue;

					//개별 입력 배송지가 없을경우 기본 배송지로 입력
					if($R_ADDR1 == "")							
						$R_ADDR1  = $arr_order[0]["R_ADDR1"];

					$GOODS_DELIVERY_NAME	= $arr_individual[0]["GOODS_DELIVERY_NAME"]; 
					$SUB_QTY				= $arr_individual[0]["SUB_QTY"];
				}
				//수령자 간격없애기 /*2016-02-25 과장님*/
				$R_MEM_NM = str_replace(" ","", $R_MEM_NM); 
				
				if($MEMO == "")
					$MEMO = $MEMO_ALL;

				$arr_order_goods = selectOrderGoods($conn, $order_goods_no);
				if(sizeof($arr_order_goods) > 0) { 
					for($i=0; $i < sizeof($arr_order_goods); $i++) {

						$CP_ORDER_NO = $arr_order_goods[$i]["CP_ORDER_NO"];
						$GOODS_NAME  = $arr_order_goods[$i]["GOODS_NAME"];
						$WORK_SEQ	 = $arr_order_goods[$i]["WORK_SEQ"];
						$CATE_01	 = $arr_order_goods[$i]["CATE_01"];
						$DELIVERY_CNT_IN_BOX = $arr_order_goods[$i]["DELIVERY_CNT_IN_BOX"];
						
						if ($individual_no != "")
							$QTY = $SUB_QTY;
						else 
							$QTY = getRefundAbleQty($conn, $reserve_no, $order_goods_no); 

						//전체 취소일경우
						if($QTY == 0) 
							continue;

						// 개별택배에 송장상품명이 있다면 그걸로 표시
						if($GOODS_DELIVERY_NAME != "")
							$GOODS_NAME = $GOODS_DELIVERY_NAME." / ".$QTY."개";
						else
							$GOODS_NAME = $GOODS_NAME." / ".$QTY."개";

						//샘플,증정 추가
						if($CATE_01 != "") 
							$GOODS_NAME = $CATE_01.")".$GOODS_NAME;

						//창고 편의를 위한 작업순번추가
						$GOODS_NAME = "[".$WORK_SEQ."번] ".$GOODS_NAME;
						
						$total_paper_qty = ceil($QTY / $DELIVERY_CNT_IN_BOX);

						//echo $QTY." ".$total_paper_qty;

						for($j=0; $j < $total_paper_qty; $j++) { 
							
							$DELIVERY_CNT = $total_paper_qty;
							$SEQ_OF_DELIVERY = $j + 1;
							$CON_ORDER_QTY = "1";
							$CON_PAYMENT_TYPE = "신용";
							$CON_DELIVERY_TYPE = "택배";

							if($total_paper_qty > 1)
								$RECEIVER_NAME = $R_MEM_NM.$DELIVERY_CNT."-".$SEQ_OF_DELIVERY;
							else
								$RECEIVER_NAME = $R_MEM_NM;

							//수령인 핸드폰번호가 없을경우 수령인 전화번호를 입력
							if($R_PHONE != "" && $R_HPHONE == "")
								$R_HPHONE = $R_PHONE;

							$RECEIVER_NAME		= SetStringToDB($RECEIVER_NAME);
							$R_ADDR1			= SetStringToDB($R_ADDR1);
							$SENDER_NM			= SetStringToDB($SENDER_NM);
							$CON_SEND_CP_ADDR	= SetStringToDB($CON_SEND_CP_ADDR);
							$MEMO				= SetStringToDB($MEMO);
							
							$order_goods_delivery_no = insertOrderDeliveryPaper($conn, $chk_work_date, $reserve_no, $order_goods_no, $individual_no, $CP_NO, $DELIVERY_CNT, $SEQ_OF_DELIVERY, $RECEIVER_NAME, $R_PHONE, $R_HPHONE, $R_ADDR1, $CON_ORDER_QTY, $MEMO, $SENDER_NM, $SENDER_PHONE, $SENDER_NM, $SENDER_PHONE, $CON_PAYMENT_TYPE, $CON_SEND_CP_ADDR, $GOODS_NAME, $DELIVERY_CP, $CON_DELIVERY_TYPE, $DELIVERY_FEE, $DELIVERY_FEE_CODE, $s_adm_no);

						}

					}
				}
			}

		}
		/*
		$total_error_msg = "";
		if(count($arr_error) > 0) { 
			foreach($arr_error as $row) {
				$total_error_msg .= $row["INDIVIDUAL_NO"];
				$total_error_msg .= $row["ERROR_MSG_EACH"];
				$total_error_msg .= "; ";
			}

		}
		*/
?>
<script>
		alert("Creating Delivery Paper is complete");
		window.location.replace("/manager/order/pop_individual_delivery_list.php?reserve_no=<?=$reserve_no?>&order_goods_no=<?=$order_goods_no?>");
</script>
<?
		exit();
	}

	if ($mode == "DELIVERY_COMPLETE") {

		$err_msg = "";

		$row_cnt = count($chk_no);
		
		$arr_order = selectOrder($conn, $reserve_no);

		$CP_NO			 = $arr_order[0]["CP_NO"];

		$arr_order_goods = selectOrderGoods($conn, $order_goods_no);

		$REFUNDABLE_QTY = getRefundAbleQty($conn, "", $order_goods_no); 

		$GOODS_NO		 = $arr_order_goods[0]["GOODS_NO"];
		$GOODS_CODE		 = $arr_order_goods[0]["GOODS_CODE"];
		$GOODS_NAME		 = $arr_order_goods[0]["GOODS_NAME"];
		$BUY_PRICE       = $arr_order_goods[0]["BUY_PRICE"];
		$SALE_PRICE      = $arr_order_goods[0]["SALE_PRICE"];
		$OG_DELIVERY_TYPE= $arr_order_goods[0]["DELIVERY_TYPE"];
		$SALE_CONFIRM_TF = $arr_order_goods[0]["SALE_CONFIRM_TF"];

		$total_qty = 0;
		for ($k = 0; $k < $row_cnt; $k++) {
		
			$temp_individual_no = $chk_no[$k];
			
			$arr_individual = selectDeliveryIndividual($conn, $temp_individual_no);

			if(sizeof($arr_individual) > 0) { 

				$SUB_QTY		= $arr_individual[0]["SUB_QTY"];
				$R_MEM_NM		= $arr_individual[0]["R_MEM_NM"];
				$IS_DELIVERED	= $arr_individual[0]["IS_DELIVERED"];
				$DELIVERY_TYPE	= $arr_individual[0]["DELIVERY_TYPE"];
				$USE_TF			= $arr_individual[0]["USE_TF"];

				if($USE_TF != "Y") { 
					$err_msg .= "".$R_MEM_NM.", ";
					continue;
				}

				$DELIVERY_PAPER_QTY = countOrderDeliveryPaper($conn, $order_goods_no, $temp_individual_no);

				//택배일때 송장이 없으면 패스 - //2016-12-28 외부업체 발송일 경우 송장이 있던 없던 진행
				if($OG_DELIVERY_TYPE != "98")
					if($DELIVERY_TYPE == "0" && $DELIVERY_PAPER_QTY == "0") { 
						$err_msg .= "".$R_MEM_NM.", ";
						continue;
					}

				//새로고침에 대비한 중복 완료 방지
				if($IS_DELIVERED == "Y") continue;

				//개별 배송완료 표기
				$result = completeDeliveryIndividual($conn, $temp_individual_no);

				//개별에 대한 매출 기장을 위한 합산
				$total_qty += $SUB_QTY;

				/*
				// 창고 작업리스트에서만 재고 차감 2017-05-24

				//개별 택배만 출고
				if($OG_DELIVERY_TYPE == "3") { 
					//세트라면 조립입고된 물건 그대로 출고
					$stock_type     = "OUT";         //입출고 구분 (출고) 
					$stock_code     = "NOUT01";      //출고 구분코드 (정상출고)
					$in_cp_no		= "";	         // 입고 업체
					$out_cp_no	    = $CP_NO;		 // 출고업체

					$goods_no		= $GOODS_NO;	 //출고상품 ** 세트인 경우 해당 세트에 상품 수 만큼 각 각 처리해야 함
					$in_loc			= "LOCA";        // 창고 구분 디폴트 창고 A, 클레임 있을시 B
					$in_loc_ext	    = "개별완료 출고";
					$in_qty			= 0;
					$in_bqty		= 0;
					$in_fbqty		= 0;
					$out_qty		= $SUB_QTY;
					$out_bqty		= 0;
					$out_tqty	    = 0; 
					$in_price		= 0;
					$out_price	    = $BUY_PRICE;     
					$in_date		= "";
					$out_date		= date("Y-m-d H:i:s",strtotime("0 month"));
					$pay_date		= "";
					$close_tf		= "N";
					$memo           = "개별배송:".$R_MEM_NM."(".$temp_individual_no.")";

					$out_result = insertStock($conn, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $order_goods_no, $rgn_no, $close_tf, $s_adm_no, $memo);
				}
				*/
				
			}
		}

		//개별에 대한 매출 기장
		/*
		// 6월 1일부로 선기장으로 전체 변경했으므로 부분기장 없음 
		if($total_qty > 0 && $SALE_CONFIRM_TF != "Y") { 
			$inout_date = date("Y-m-d",strtotime("0 month"));
			$inout_type = "LR01"; //매출
			$total_price = $total_qty * $SALE_PRICE;

			$TEMP_MEMO = "";
			$arr_memo = getMemoFromOrderGoods($conn, $order_goods_no);
			if(sizeof($arr_memo) > 0) { 
				$A = $arr_memo[0]["A"];
				$B = $arr_memo[0]["B"];
				$C = $arr_memo[0]["C"];
				$D = $arr_memo[0]["D"];

				$TEMP_MEMO .= $A;
				$TEMP_MEMO .= ($B != "" ? ($TEMP_MEMO != "" ? "/".$B : $B) : "");
				$TEMP_MEMO .= ($C != "" ? ($TEMP_MEMO != "" ? "/".$C : $C) : "");
				$TEMP_MEMO .= ($D != "" ? ($TEMP_MEMO != "" ? "/".$D : $D) : "");

			}

			insertCompanyLedger($conn, $CP_NO, $inout_date, $inout_type, $GOODS_NO, $GOODS_NAME."[".$GOODS_CODE."]", $total_qty, $SALE_PRICE, null, 0, $TEMP_MEMO, $reserve_no, $order_goods_no, "개별기장", null, $s_adm_no, null);
		}
		*/

	?>	
	<script language="javascript">
		
		alert('선택한 배송지가 배송완료 되었습니다.');

		<?if($err_msg <> "") { ?>
			alert('작업중 일부 오류가 있습니다. \n\n<?=rtrim($err_msg, ", ");?>');
		<? } ?>
		window.location.replace("/manager/order/pop_individual_delivery_list.php?reserve_no=<?=$reserve_no?>&order_goods_no=<?=$order_goods_no?>");
	</script>
	<?
		exit;
	}

	if ($mode == "DELIVERY_CANCEL") {

		$err_msg = "";

		$row_cnt = count($chk_no);

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$temp_individual_no = $chk_no[$k];
						
			$result = cancelDeliveryIndividual($conn, $temp_individual_no);
		
		}
		
	?>	
	<script language="javascript">
		
		alert('선택한 배송지가 배송취소 되었습니다.');

		window.location.replace("/manager/order/pop_individual_delivery_list.php?reserve_no=<?=$reserve_no?>&order_goods_no=<?=$order_goods_no?>");
	</script>
	<?
		exit;
	}

	if ($mode == "USE") {

		$row_cnt = count($chk_no);
		//echo $row_cnt;

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$temp_individual_no = $chk_no[$k];
						
			$result = updateDeliveryIndividualUseTF($conn, $temp_individual_no, $use_tf);
		
		}

	?>	
	<script language="javascript">
		alert('선택항목이 수정 되었습니다.');
		window.location.replace("/manager/order/pop_individual_delivery_list.php?reserve_no=<?=$reserve_no?>&order_goods_no=<?=$order_goods_no?>");
	</script>
	<?
		exit;
	}

	if ($mode == "D") {

		$row_cnt = count($chk_no);
		//echo $row_cnt;

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$temp_individual_no = $chk_no[$k];
						
			$result = deleteDeliveryIndividual($conn, $temp_individual_no);
		
		}

	?>	
	<script language="javascript">
		alert('선택항목이 삭제 되었습니다. 사용중인 송장이 있으면 지워지지 않습니다.');
		window.location.replace("/manager/order/pop_individual_delivery_list.php?reserve_no=<?=$reserve_no?>&order_goods_no=<?=$order_goods_no?>");
	</script>
	<?
		exit;
	}

	if ($mode == "FU") {
		#====================================================================
			$savedir1 = $g_physical_path."upload_data/temp_delivery";
		#====================================================================

			$file_nm	= upload($_FILES[file_nm], $savedir1, 10000 , array('xls'));

			require_once '../../_excel_reader/Excel/reader.php';
			$data = new Spreadsheet_Excel_Reader();
			$data->setOutputEncoding('euc-kr');
			//$data->read('test.xls');
			$data->read("../../upload_data/temp_delivery/".$file_nm);
		
			error_reporting(E_ALL ^ E_NOTICE);

			$err_msg = "";

			for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {

				$TEMP_R_MEM_NM				= SetStringToDB(trim($data->sheets[0]['cells'][$i][1]));  	//A
				$TEMP_R_PHONE				= SetStringToDB(trim($data->sheets[0]['cells'][$i][2]));  	//B
				$TEMP_R_HPHONE				= SetStringToDB(trim($data->sheets[0]['cells'][$i][3]));  	//C
				$TEMP_R_ZIPCODE				= ""; //사용안한다 하심							
				$TEMP_R_ADDR1				= SetStringToDB(trim($data->sheets[0]['cells'][$i][4]));  	//D
				$TEMP_GOODS_DELIVERY_NAME	= SetStringToDB(trim($data->sheets[0]['cells'][$i][5]));	//E
				$TEMP_SUB_QTY				= SetStringToDB(trim($data->sheets[0]['cells'][$i][6]));	//F
				$TEMP_OPT_MEMO				= SetStringToDB(trim($data->sheets[0]['cells'][$i][7]));	//G
				$TEMP_DELIVERY_TYPE			= SetStringToDB(trim($data->sheets[0]['cells'][$i][8]));	//H
				
				//echo $TEMP_R_MEM_NM." ".$TEMP_R_PHONE." ".$TEMP_R_HPHONE." ".$TEMP_R_ADDR1." ".$TEMP_GOODS_DELIVERY_NAME." ".$TEMP_SUB_QTY." ".$TEMP_OPT_MEMO."<br/>";

				$TEMP_R_ADDR1 = str_replace("\"", "'", $TEMP_R_ADDR1);
				$TEMP_OPT_MEMO = str_replace("\"", "'", $TEMP_OPT_MEMO);


				//0 이상 숫자가 아니면 패스
				if (filter_var($TEMP_SUB_QTY, FILTER_VALIDATE_INT, array('options' => array('min_range' => 0))) === FALSE) {
					$err_msg .= $i.", ";
					continue;
				}

				$TEMP_DELIVERY_TYPE = getDcodeCode($conn, 'DELIVERY_TYPE', $TEMP_DELIVERY_TYPE);

				// 지정 없을경우 택배
				// 주문 자체가 외부업체 발송일 경우 세부 내역도 외부업체 발송으로, 택배 송장 생성 떄문에 강제로 외부 지정 2017-04-11
				if($hid_delivery_type == "98")
					$TEMP_DELIVERY_TYPE = "98";
				else
					if($TEMP_DELIVERY_TYPE == "")  
						$TEMP_DELIVERY_TYPE = "0";

				if($TEMP_R_MEM_NM <> "" && $TEMP_SUB_QTY <> "")
					insertDeliveryIndividual($conn, $file_nm, $order_goods_no, $TEMP_R_MEM_NM, $TEMP_R_PHONE, $TEMP_R_HPHONE, $TEMP_R_ZIPCODE, $TEMP_R_ADDR1, $TEMP_GOODS_DELIVERY_NAME, $TEMP_SUB_QTY, $TEMP_OPT_MEMO, $TEMP_DELIVERY_TYPE, $s_adm_no);
				else
					$err_msg .= $i.", ";
			}

			if($err_msg <> "") { 
				$err_msg = rtrim($err_msg, ", ");
				$err_msg = "입력 파일 내에서 해당 엑셀의 (".$err_msg.") 번째 줄의 입력에 수령자 혹은 수량이 잘못되었습니다."; 
?>	
<script language="javascript">
		alert('<?=$err_msg?>');
		window.location.replace("/manager/order/pop_individual_delivery_list.php?reserve_no=<?=$reserve_no?>&order_goods_no=<?=$order_goods_no?>");
</script>
<?
			} else {
?>	
<script language="javascript">
		alert('개별택배 입력완료 되었습니다.');
		window.location.replace("/manager/order/pop_individual_delivery_list.php?reserve_no=<?=$reserve_no?>&order_goods_no=<?=$order_goods_no?>");
</script>
<?
			}
		exit;
	}

	$arr_order_goods = selectOrderGoods($conn, $order_goods_no);

	$DELIVERY_TYPE   = $arr_order_goods[0]["DELIVERY_TYPE"];
	$DELIVERY_CP     = $arr_order_goods[0]["DELIVERY_CP"];
	$SENDER_NM       = $arr_order_goods[0]["SENDER_NM"];
	$SENDER_PHONE    = $arr_order_goods[0]["SENDER_PHONE"];
	$WORK_START_DATE = $arr_order_goods[0]["WORK_START_DATE"];
	$WORK_END_DATE	 = $arr_order_goods[0]["WORK_END_DATE"];
	$ORDER_STATE	 = $arr_order_goods[0]["ORDER_STATE"];
	$QTY			 = $arr_order_goods[0]["QTY"];

	$arr_rs = listDeliveryIndividual($conn, $order_goods_no, "DESC");

	$SUM_QTY = 0;

	$UNDELIVERED_PLACE_CNT = 0;
	$UNDELIVERED_DELIVERY_CNT = 0;
	$DELIVERED_PLACE_CNT = 0;
	$DELIVERED_DELIVERY_CNT = 0;

	$place_cnt = 0;
	if(sizeof($arr_rs) >= 1) {
		for($i = 0; $i < sizeof($arr_rs); $i ++) { 

			$SUB_QTY				= trim($arr_rs[$i]["SUB_QTY"]);
			$IS_DELIVERED			= trim($arr_rs[$i]["IS_DELIVERED"]);
			$USE_TF					= trim($arr_rs[$i]["USE_TF"]);

			if($USE_TF != "Y") continue;

			$place_cnt += 1;

			if($IS_DELIVERED != "Y") { 
				$UNDELIVERED_PLACE_CNT ++;
				$UNDELIVERED_DELIVERY_CNT += $SUB_QTY;
			} else { 
				$DELIVERED_PLACE_CNT ++;
				$DELIVERED_DELIVERY_CNT += $SUB_QTY;
			}
			
			$SUM_QTY += $SUB_QTY;
		}
	}

	$REFUNDABLE_QTY = getRefundAbleQty($conn, "", $order_goods_no); 
	$REALDELIVERY_QTY = getRealDeliveryQty($conn, "", $order_goods_no); 


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />

<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script>
	$(function() {
		$( ".datepicker" ).datepicker({
		    buttonImage: "/manager/images/calendar/cal.gif",
		    buttonImageOnly: true,
		    buttonText: "Select date",
			showOn: "both",
			dateFormat: "yy-mm-dd",
			numberOfMonths: 2,
			changeMonth: true,
			changeYear: true
		});
	});
</script>
<script>
	$(function() {
		$("#tabs").tabs();
	});
</script>
<script language="javascript">

	function js_create_delivery_paper()
	{
		var frm = document.frm;

		if($("select[name=DELIVERY_TYPE]").val() == "" || $("select[name=DELIVERY_CP]").val() == "")
		{
			alert('Please select the delivery method and courier company to enther in bulk.');
			return;
		}

		frm.mode.value = "I";
		frm.target = "";
		frm.method = "post";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	function js_individual_delivery(btn) {

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

	function js_list_delivery_paper(reserve_no, order_goods_no, individual_no) {

		var url = "pop_delivery_paper_list.php?reserve_no=" + reserve_no + "&order_goods_no=" + order_goods_no + "&individual_no=" + individual_no;

		NewWindow(url, 'pop_delivery_paper_list','1000','500','YES');
		
	}

	function js_all_check() {
		var frm = document.frm;
		
		if (frm['chk_no[]'] != null) {
			
			if (frm['chk_no[]'].length != null) {

				if (frm.all_chk.checked == true) {
					for (i = 0; i < frm['chk_no[]'].length; i++) {
						frm['chk_no[]'][i].checked = true;
					}
				} else {
					for (i = 0; i < frm['chk_no[]'].length; i++) {
						frm['chk_no[]'][i].checked = false;
					}
				}
			} else {
			
				if (frm.all_chk.checked == true) {
					frm['chk_no[]'].checked = true;
				} else {
					frm['chk_no[]'].checked = false;
				}
			}
		}
	}

	function js_delete() {

		var frm = document.frm;
		
		if (confirm("배송완료 된 배송지를 삭제해야 하실 경우에는 연결된 송장을 먼저 삭제해주세요. (필수체크 : '정산관리 > 거래처 원장' 수정) \n\n 계속 진행하시겠습니까?")) {
			frm.mode.value = "D";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}

	}

	function js_done(use_tf) {

		var frm = document.frm;
		
		frm.use_tf.value = use_tf;

		if(use_tf == "Y") { 
			frm.mode.value = "DELIVERY_COMPLETE";
		} else
			frm.mode.value = "DELIVERY_CANCEL";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	function js_use(use_tf) { 

		var frm = document.frm;
		
		frm.use_tf.value = use_tf;
		frm.mode.value = "USE";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_pop_delivery_confirmation(reserve_no, individual_no, print_type) {

		var frm = document.frm;
		
		print_date = frm.print_date.value;
	
		NewDownloadWindow("pop_delivery_confirmation2.php", {reserve_no : reserve_no, individual_no: individual_no, print_type :  print_type, print_date : print_date});

	}

	function js_excel() {

		var frm = document.frm;
		
		frm.target = "";
		frm.action = "<?=str_replace("list","excel_list",$_SERVER[PHP_SELF])?>";
		frm.submit();

	}
</script>
<style>
	input[type=text].list {display:none; width:90%;} 
</style>
<script>
	$(function(){
		
		$(".data_list td, th").on("click", function(){

			//if($(this).closest("tr").prop("class") == "delivered") { 
			//	alert("배송처리 된 내용은 수정할 수 없습니다.");
			//	return;
			//}

			//var source = $(this).prop("title");
			var text_box = $(this).find("input[type=text].list");

			$(this).find("span").hide();
			if($(this).prop("title") != $(this).find("span").html()) { 
				
				var target_text = $(this).find("span").html();
				target_text = target_text.replaceall("&amp;","&");
				target_text = target_text.replaceall("&quot;",'"');
				target_text = target_text.replaceall("&lt;","<");
				target_text = target_text.replaceall("&gt;",">");

				text_box.val(target_text).show().focus();
			} else
				text_box.val($(this).prop("title")).show().focus();

		});

		$("input[type=text].list").on("keydown", function(){
			$(this).parent().find("span").html($(this).val());

			var target_text = $(this).val();
			target_text = target_text.replaceall("&amp;","&");
			target_text = target_text.replaceall("&quot;",'"');
			target_text = target_text.replaceall("&lt;","<");
			target_text = target_text.replaceall("&gt;",">");

			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13'){

				var individual_no = $(this).closest("tr").data("individual_no");
				var column = $(this).data("column");
				var value = target_text;

				(function() {
				  $.getJSON( "/manager/order/json_update_order_goods.php", {
					mode: "UPDATE_INDIVIDUAL_DELIVERY",
					individual_no: individual_no,
					column: column,
					value : value
				  })
					.done(function( data ) {
					  $.each( data, function( i, item ) {
						  if(item.RESULT == "0")
							  alert('연결오류 : 잠시후 다시 시도해주세요');
					  });
					});
				})();


				$("span").show();
				$("input[type=text].list").hide();
			}
		});
		

		$("select[name=delivery_type]").change(function(){

			var individual_no = $(this).closest("tr").data("individual_no");
			var value = $(this).val();

			(function() {
			  $.getJSON( "/manager/order/json_update_order_goods.php", {
				mode: "UPDATE_INDIVIDUAL_DELIVERY",
				individual_no: individual_no,
				column: 'DELIVERY_TYPE',
				value : value
			  })
				.done(function( data ) {
				  $.each( data, function( i, item ) {
					  if(item.RESULT == "0")
						  alert('연결오류 : 잠시후 다시 시도해주세요');
					  else
						  location.href = "<?=$_SERVER[PHP_SELF]?>?reserve_no=<?=$reserve_no?>&order_goods_no=<?=$order_goods_no?>" ;
				  });
				});
			})();
		});

		$("input[type=text].list").on("blur", function(){
			$("span").show();
			$("input[type=text].list").hide();
		});
	});
</script>
</head>
<style>
body#popup_order_wide {width:100%;}
.delivered {background-color:#EFEFEF;}
tr.not_used > td {color: #A2A2A2;}
.color_yellow{background-color:#fcfc3f !important;}
</style>
<body id="popup_order_wide">

<div id="popupwrap_order_wide">
	<h1>개별 배송지 입력</h1>
	<div id="postsch_code">

		<div class="addr_inp">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="">
<input type="hidden" name="reserve_no" value="<?=$reserve_no?>">
<input type="hidden" name="order_goods_no" value="<?=$order_goods_no?>">
<input type="hidden" name="hid_delivery_type" value="<?=$DELIVERY_TYPE?>"/>
<input type="hidden" name="use_tf" value=""/>
	
	<? if($ORDER_STATE != "3") { ?>
	<div id="tabs" style="width:95%; margin:10px 0;">
		<ul>
			<li><a href="#tabs-1">개별배송지 입력</a></li>
			<li><a href="#tabs-2">송장 생성</a></li>
		</ul>
		<div id="tabs-1">
			<table cellpadding="0" cellspacing="0" width="100%" class="colstable02">
			<colgroup>
				<col width="10%" />
				<col width="45%" />
				<col width="*" />
			</colgroup>
			<tr>
				<? if($REALDELIVERY_QTY > $SUM_QTY) { ?>
				<th>개별배송지 파일 입력<br/><br/><a href="/manager/order/input_example_individual.xls">입력파일 받기</a></th>
				<td class="line">
					<input type="file" name="file_nm" style="width:60%;" class="txt">
					<a href="#" onclick="js_individual_delivery(this); return false;"><img src="../images/admin/btn_confirm.gif" alt="확인"></a>
				</td>
				<? } else { ?>
				<td colspan="2" class="line"></td>
				<? } ?>
				<td class="line">
				<b>선택한 배송지를</b><br/><br/>
				<input type="button" name="bb" value=" 배송완료 " style="padding:3px;" onclick="js_done('Y')"/>  
				<input type="button" name="bb" value=" 배송취소 " style="padding:3px;" onclick="js_done('N')"/> &nbsp;&nbsp;
				
				<input type="button" name="bb" value=" 사용함 " style="padding:3px;" onclick="js_use('Y');"/> 
				<input type="button" name="bb" value=" 사용안함 " style="padding:3px;" onclick="js_use('N');"/> &nbsp;&nbsp;

				<input type="button" name="bb" value=" 삭제 " style="padding:3px;" onclick="js_delete()"/></td>
			</tr>
			</table>
		</div>
		<div id="tabs-2">
			<table cellpadding="0" cellspacing="0" width="100%" class="colstable02">
				<colgroup>
					<col width="12%" />
					<col width="33%" />
					<col width="12%" />
					<col width="33%" />
					<col width="*" />
				</colgroup>
				<tbody>
					<tr>
						<th>송장기준일</th>
						<td class="line" colspan="3">
							<?
								$chk_work_date = date("Y-m-d",strtotime("1 day"));
							?>
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="chk_work_date" value="<?=$chk_work_date?>" maxlength="10"/>
						</td>
					</tr>
					<tr>
						<th>배송종류</th>
						<td class="line"><?=makeSelectBoxOnChange($conn,"DELIVERY_TYPE", "DELIVERY_TYPE","90", "배송종류 선택", "", $DELIVERY_TYPE)?></td>
						<th>택배회사</th>
						<td class="line"><?=makeSelectBoxOnChange($conn,"DELIVERY_CP", "DELIVERY_CP","90", "택배회사선택", "", $DELIVERY_CP)?>
						
						<td class="line" rowspan="3">
							<? if(($DELIVERY_TYPE == "0" || $DELIVERY_TYPE == "3") && $WORK_START_DATE != "0000-00-00 00:00:00") { ?>
								<input type="button" name="b" value="송장 생성" class="btntxt" onclick="js_create_delivery_paper();">
							<? } else { ?>
								<span style="color:red; font-weight:bold;">작업순번<br/> 지정요망</span>
							<? } ?>
						</td>
					</tr>
					<tr>
						<th>보내는사람</th>
						<td class="line"><input type="text" name="SENDER_NM" value="<?=$SENDER_NM?>" /></td>
						<th>보내는 번호</th>
						<td class="line"><input type="text" name="SENDER_PHONE" value="<?=$SENDER_PHONE?>" />
					</tr>
				</tbody>
				</table> 
		</div>
	</div>
	<? } ?>
	<div class="sp10"></div>
	<table cellpadding="0" cellspacing="0" width="95%" style="font-weight:bold;">
		<colgroup>
			<col width="50%" />
			<col width="50%" />
		</colgroup>
		<tr height="30">
			<td>
				<span style="color:green;">완료된 배송지 총 <?=number_format($DELIVERED_PLACE_CNT)?> 곳, <?=number_format($DELIVERED_DELIVERY_CNT)?> 개</span>
			</td>
			<td style="text-align:right;">
				<span>주문 수량 <?=number_format($REFUNDABLE_QTY)?>개, 총 <?=number_format($SUM_QTY)?>개 상품, <?=number_format($place_cnt)?>곳의 배송지 지정됨</span>
				<?
					if($REALDELIVERY_QTY <> $QTY)
						echo "<br/><span style='color:#A2A2A2;'>(원 주문수량 : ".number_format($QTY)."개)</span>"
				?>
				<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>
			</td>
		</tr>
		<tr>
			<td>
				<span style="color:red;">완료전 배송지 총 <?=number_format($UNDELIVERED_PLACE_CNT)?> 곳, <?=number_format($UNDELIVERED_DELIVERY_CNT)?> 개</span>
			</td>
			<td style="text-align:right;">
				<?
					if($ORDER_STATE != "3") { 
				?>
					<? if($REFUNDABLE_QTY < $SUM_QTY) { ?>
						 <span style="color:red;"><?=number_format($SUM_QTY-$REFUNDABLE_QTY)?>개 초과되었습니다. 수량확인해주세요.</span>
					<? } else if($REFUNDABLE_QTY == $SUM_QTY) {?>
						 <span style="color:green;">주문 수량만큼 정확히 입력되었습니다.</span>
					<? } else {?>
						 <span style="color:blue;"><?=number_format($REFUNDABLE_QTY - $SUM_QTY) ?>개에 대한 배송지가 지정되지 않았습니다.</span>
					<? } ?>
				<? } else { ?>
					<span style="color:gray;">배송완료 되었습니다.</span>
				<? } ?>
			</td>
		</tr>
	</table>
	<div class="sp10"></div>
	* 범위 선택 하고 싶은 경우 "SHIFT" 키를 누른 상태로 시작과 끝을 선택해주세요.
	<?
		if($search_str <> "") {
			echo "<b>(검색어 : ".$search_str.")</b>";
		}
	?>
	<table cellpadding="0" cellspacing="0" width="90%" class="rowstable data_list">
	<colgroup>
		<col width="5%" />
		<col width="8%" />
		<col width="9%" />
		<col width="9%" />
		<col width="15%" />
		<col width="10%" />
		<col width="*" />
		<col width="6%" />
		<col width="6%" />
		<col width="10%" />
		<col width="11%" />
	</colgroup>
	<thead>
		<tr>
			<th><input type="checkbox" name="all_chk" onClick="js_all_check();" class="display_none"></th>
			<th>수령자</th>
			<th>연락처</th>
			<th>휴대폰번호</th>
			<th>주소</th>
			<th>배송메모</th>
			<th>송장상품명</th>
			<th>상품수량</th>
			<th>송장수량</th>
			<th>등록자<br/>(등록일시)</th>
			<th class="end">납품일자가
			<select name="print_date">
				<option value="<?=date("Y-m-d",strtotime("0 day"))?>">오늘</option>
				<option value="<?=date("Y-m-d",strtotime("1 day"))?>">내일</option>
			</select>
			인
			</th>
		</tr>
	</thead>
	<tbody>
	
	<?
	if(sizeof($arr_rs) >= 1) {
		for($i = 0; $i < sizeof($arr_rs); $i ++) { 

		$INDIVIDUAL_NO			= trim($arr_rs[$i]["INDIVIDUAL_NO"]);
		$R_ZIPCODE			    = trim($arr_rs[$i]["R_ZIPCODE"]); 
		$R_ADDR1 				= trim($arr_rs[$i]["R_ADDR1"]);
		$R_MEM_NM				= trim($arr_rs[$i]["R_MEM_NM"]);
		$R_PHONE				= trim($arr_rs[$i]["R_PHONE"]); 
		$R_HPHONE				= trim($arr_rs[$i]["R_HPHONE"]); 
		$GOODS_DELIVERY_NAME	= trim($arr_rs[$i]["GOODS_DELIVERY_NAME"]); 
		$SUB_QTY				= trim($arr_rs[$i]["SUB_QTY"]);
		$MEMO					= trim($arr_rs[$i]["MEMO"]);
		$DELIVERY_TYPE			= trim($arr_rs[$i]["DELIVERY_TYPE"]);
		$IS_DELIVERED			= trim($arr_rs[$i]["IS_DELIVERED"]);
		$USE_TF					= trim($arr_rs[$i]["USE_TF"]);

		$REG_DATE				= date("n월j일H시i분", strtotime(trim($arr_rs[$i]["REG_DATE"])));

		$REG_ADM				= trim($arr_rs[$i]["REG_ADM"]);
		$REG_ADM = getAdminName($conn, $REG_ADM);

		$DELIVERY_PAPER_QTY = countOrderDeliveryPaper($conn, $order_goods_no, $INDIVIDUAL_NO);

		if($IS_DELIVERED == "Y") { 
			$str_tr_class = "delivered";
			$DELIVERY_DATE = date("n월j일H시i분", strtotime(trim($arr_rs[$i]["DELIVERY_DATE"])));

		} else { 
			$str_tr_class = "";
			$DELIVERY_DATE = "배송전";
		}

		if($USE_TF == "N")
			$str_tr_use_class = "not_used";
		else
			$str_tr_use_class = "";


		$TXT_R_ADDR1 = $R_ADDR1;
		$TXT_R_MEM_NM = $R_MEM_NM;
		$TXT_GOODS_DELIVERY_NAME = htmlspecialchars($GOODS_DELIVERY_NAME);

		//주문, 배송리스트의 키워드 검색어를 가지고 올라온 경우
		$td_class1 = "";
		$td_class2 = "";
		$td_class3 = "";
		if($search_str <> "") {

			if(strpos($TXT_R_ADDR1, $search_str) !== false) { $td_class1 = "color_yellow"; } 
			if(strpos($TXT_R_MEM_NM, $search_str) !== false) {  $td_class2 = "color_yellow"; } 
			if(strpos($TXT_GOODS_DELIVERY_NAME, $search_str) !== false) {  $td_class3 = "color_yellow"; } 
			//echo "<script>console.log('search_str is ".$search_str."');</script>";
		
		}
	?>

		<tr height="35" class="<?=$str_tr_class?> <?=$str_tr_use_class?>" title="배송완료시간: <?=$DELIVERY_DATE?>" data-individual_no="<?=$INDIVIDUAL_NO?>" >
			<td><input type="checkbox" name="chk_no[]" class="chk" value="<?=$INDIVIDUAL_NO?>"><br/><?=$INDIVIDUAL_NO?></td>
			<td class="<?=$td_class2?>" title="<?=$R_MEM_NM?>"><span><?=$TXT_R_MEM_NM?></span><input type="text" class="list" data-column="r_mem_nm" value=""/></td>
			<td title="<?=$R_PHONE?>"><span><?=$R_PHONE?></span><input type="text" class="list"  data-column="r_phone" value=""/></td>
			<td title="<?=$R_HPHONE?>"><span><?=$R_HPHONE?></span><input type="text" class="list"  data-column="r_hphone" value=""/></td>
			<td class="<?=$td_class1?>" title="<?=$R_ADDR1?>"><span><?=$TXT_R_ADDR1?></span><input type="text" class="list"  data-column="r_addr1" value=""/></td>
			<td title="<?=$MEMO?>"><span><?=$MEMO?></span><input type="text" class="list"  data-column="memo" value=""/></td>
			<td class="<?=$td_class3?>" title="<?=$GOODS_DELIVERY_NAME?>"><span><?=$TXT_GOODS_DELIVERY_NAME?></span><input type="text" class="list"  data-column="goods_delivery_name" value=""/></td>
			<td title="<?=$SUB_QTY?>"><span><?=$SUB_QTY?></span><input type="text" class="list"  data-column="sub_qty" value=""/></td>
			<td style="font-weight:bold; <?if($DELIVERY_TYPE == "0" && $DELIVERY_PAPER_QTY == "0") echo "color:red;";?>">
				<?=$DELIVERY_PAPER_QTY?>
			</td>
			<td>
				<?=$REG_ADM."<br/>(".$REG_DATE.")"?>
			</td>
			<td>
				<? if($ORDER_STATE != "3") { ?>
					<?=makeSelectBox($conn,"DELIVERY_TYPE", "delivery_type","95", "배송방식", "", $DELIVERY_TYPE, "")?>
					
					<? if($DELIVERY_TYPE == "0") { ?>
					<input type="button" name="b" value="송장생성/조회" class="btntxt" onclick="js_list_delivery_paper('<?=$reserve_no?>', '<?=$order_goods_no?>','<?=$INDIVIDUAL_NO?>');" style="margin-bottom:3px;"><br/>
					<? } else if($DELIVERY_TYPE == "1") { // 직접수령?>
					<input type="button" name="b" value="인수증" class="btntxt" onclick="js_pop_delivery_confirmation('<?=$reserve_no?>','<?=$INDIVIDUAL_NO?>', '<?=$DELIVERY_TYPE?>');">
					<? } else if($DELIVERY_TYPE == "2") { // 퀵서비스?>
					<input type="button" name="b" value="납품확인서" class="btntxt" onclick="js_pop_delivery_confirmation('<?=$reserve_no?>','<?=$INDIVIDUAL_NO?>', '<?=$DELIVERY_TYPE?>');">
					<? } ?>
				<? } ?>
				
				</td>
		</tr>
	
	<?
		}
	} else {

	?>
		<tr>
			<td colspan="11" height="50" align="center">데이터가 없습니다</td>
		</tr>
	<?

	}
	
	?>
	
	</tbody>
	</table>
	

	<script>

		$(function(){

			//전체 로딩전 클릭 방지
			$("input[name=all_chk]").show();

		});

		var last_click_idx = -1;
		$(".chk").click(function(event){
			
			var clicked_elem = $(this);
			var clicked_elem_chked = $(this).prop("checked");

			var start_idx = -1;
			var end_idx = -1;
			var click_idx = -1;

			$(".chk").each(function( index, elem ) {

				//클릭위치 저장
				if(clicked_elem.val() == $(elem).val())
					click_idx = index;

			});

			if(event.shiftKey) {

				if($(".chk:checked").size() >= 2) {
					$(".chk").each(function( index, elem ) {

						//체크된 곳의 시작 체크
						if(start_idx == -1 && $(elem).prop("checked"))
							start_idx = index;

						//체크의 마지막 인덱스 체크
						if($(elem).prop("checked"))
							end_idx = index;

					});

					if($(".chk:checked").size() > 2 && last_click_idx > click_idx)
						start_idx = click_idx;

					if($(".chk:checked").size() > 2 && last_click_idx < click_idx)
						end_idx = click_idx;


					//alert("start_idx: " + start_idx + ", end_idx: " + end_idx + ", click_idx: " + click_idx+ ", last_click_idx: " + last_click_idx);

					
					$(".chk").each(function(index, elem) {

						if(start_idx <= index && index <= end_idx) {
							$(elem).prop("checked", true);
						}
						else
							$(elem).prop("checked", false);
						
					});
					
				}

				last_click_idx = click_idx;
			}

		});

		
	
	</script>


<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
<div class="bot_close" style="width:auto;"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
</div>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>