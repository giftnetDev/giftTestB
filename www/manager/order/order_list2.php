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
	$menu_right = "OD002"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/com/util/ImgUtil.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/payment/payment.php";
	require "../../_classes/biz/stock/stock.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/confirm/confirm.php";

	if ($mode == "CU") { //주문확인

		$row_cnt = count($chk_order_no);
		//echo $row_cnt;

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_order_goods_no			= $chk_order_no[$k];

			$arr_order_goods_no			= explode("|", $str_order_goods_no);
			
			$temp_order_no			= trim($arr_order_goods_no[0]);
			$temp_order_goods_no	= trim($arr_order_goods_no[1]);
			$temp_cate_01			= trim($arr_order_goods_no[2]);
			$temp_reg_adm			= trim($arr_order_goods_no[3]);

			//2017-06-12 추가는 시스템 권한만 주문확인가능
			if($temp_cate_01 == "추가" && $s_adm_group_no != "1")
				continue;
			
			//2016-06-21 주문확인 되어있다면 패스
			if(chkOrderConfirmState($conn, $temp_order_goods_no) > 0)
				continue;

			$result = updateOrderConfirmState($conn, $temp_order_no, $temp_order_goods_no, $s_adm_no);

			////////////////////////////////////////////////////////
			// 2017-05-17 선기장으로 변경
			////////////////////////////////////////////////////////

			$sale_cp_no = selectCompanyNoByOrderGoodsNo($conn, $temp_order_goods_no);

			$arr_order_goods = selectOrderGoodsForDeliveryList($conn, $temp_order_goods_no);
			if(sizeof($arr_order_goods) <= 0) continue;

			$temp_reserve_no		= SetStringFromDB($arr_order_goods[0]["RESERVE_NO"]);
			$temp_group_no			= SetStringFromDB($arr_order_goods[0]["GROUP_NO"]);
			$temp_goods_no			= SetStringFromDB($arr_order_goods[0]["GOODS_NO"]);
			$temp_goods_name		= ($cate_01 <> "" ? $cate_01.")" : "").$arr_order_goods[0]["GOODS_NAME"]."[".$arr_order_goods[0]["GOODS_CODE"]."]";
			$temp_order_state		= SetStringFromDB($arr_order_goods[0]["ORDER_STATE"]);
			$temp_buy_price			= SetStringFromDB($arr_order_goods[0]["BUY_PRICE"]);
			$temp_sale_price		= SetStringFromDB($arr_order_goods[0]["SALE_PRICE"]);
			$temp_discount_price	= SetStringFromDB($arr_order_goods[0]["DISCOUNT_PRICE"]);
			$temp_sa_delivery_price	= SetStringFromDB($arr_order_goods[0]["SA_DELIVERY_PRICE"]);
			$temp_work_end_date		= SetStringFromDB($arr_order_goods[0]["WORK_END_DATE"]);
			$temp_sale_confirm_tf	= SetStringFromDB($arr_order_goods[0]["SALE_CONFIRM_TF"]);
			$temp_tax_tf			= SetStringFromDB($arr_order_goods[0]["TAX_TF"]);
			$temp_cate_02			= SetStringFromDB($arr_order_goods[0]["CATE_02"]);
			$temp_cate_03			= SetStringFromDB($arr_order_goods[0]["CATE_03"]);
			

			$inout_type = "LR01";
			$inout_date = date("Y-m-d", strtotime("0 month"));
			//if($temp_delivery_type != "98")
			//	$inout_date = date("Y-m-d", strtotime($temp_work_end_date));
			//else
			//	$inout_date = date("Y-m-d", strtotime("0 month"));

			$TEMP_MEMO = "";
			$arr_memo = getMemoFromOrderGoods($conn, $temp_order_goods_no);
			if(sizeof($arr_memo) > 0) { 
				$A = $arr_memo[0]["A"];
				//$B = $arr_memo[0]["B"];
				$C = $arr_memo[0]["C"];
				$D = $arr_memo[0]["D"];

				$TEMP_MEMO .= $A;
				//2017-07-19 추가,증정,샘플 별도로 분리
				//$TEMP_MEMO .= ($B != "" ? ($TEMP_MEMO != "" ? "/".$B : $B) : "");
				$TEMP_MEMO .= ($C != "" ? ($TEMP_MEMO != "" ? "/".$C : $C) : "");
				$TEMP_MEMO .= ($D != "" ? ($TEMP_MEMO != "" ? "/".$D : $D) : "");

			}

			$refund_able_qty = getRefundAbleQty($conn, $temp_reserve_no, $temp_order_goods_no);

			//개별택배나 외부 업체 발송중 개별 배송이 완료되지 않은 경우  매출 기장
			//$arr_cnt_delivery = cntDeliveryIndividual($conn, $temp_order_goods_no);

			if($temp_sale_confirm_tf == "N") { 
				//if($arr_cnt_delivery[0]["CNT_DELIVERY_PLACE"] <= 0) { 

				
				//$chk_reg_date = getOrderGoodsRegDate($conn, $temp_group_no);
				//$base_date = getDcodeExtByCode($conn, "LEDGER_SETUP", "BASE_DATE");

				//if($base_date < $chk_reg_date) {
				
					if($refund_able_qty != 0 && $temp_sale_price != 0) { 
						
						insertCompanyLedger($conn, $sale_cp_no, $inout_date, $inout_type, $temp_goods_no, $temp_goods_name, $refund_able_qty, $temp_sale_price, null, 0, $temp_cate_01, $temp_tax_tf, $TEMP_MEMO, $temp_reserve_no, $temp_order_goods_no, "매출상품", null, $s_adm_no, null);
						updateSaleConfirmState($conn, $temp_order_goods_no, $inout_date, 'Y', $s_adm_no);
					
						//매출할인이 있을경우 - 개별완료나 외부업체 발송중 배송지 지정된 경우엔 따로 안하기 떄문에 2017-04-19
						if($temp_discount_price != 0) { 
							insertCompanyLedger($conn, $sale_cp_no, $inout_date, $inout_type, null, "매출할인", "1", $temp_discount_price * -1,  null, 0, $temp_cate_01, $temp_tax_tf, $TEMP_MEMO, $temp_reserve_no, $temp_order_goods_no, "매출할인", null, $s_adm_no, null);
						}

						//세금 계산 승인 코드가 있을 경우
						if($temp_cate_02 <> "")
						{
							$arr_cl_no = getCompanyLedgerCLNoByOrderGoodsNo($conn, $temp_order_goods_no);

							foreach(explode("\n",$temp_cate_02) as $each_cf_code) { 

								$each_cf_code = trim(str_replace('\n','',$each_cf_code));

								if(trim($each_cf_code) != "") 
								{
								
									for($k = 0; $k < sizeof($arr_cl_no); $k ++) { 
										$cl_no = $arr_cl_no[$k]["CL_NO"];
										updateTaxInvoiceTF($conn, $cl_no, $temp_cate_03, $each_cf_code, 'Y', $s_adm_no);
									}
								}
							}
						}
						
						/*
						//추가배송비가 있을경우 - 개별완료나 외부업체 발송중 배송지 지정된 경우엔 따로 안하기 떄문에 2017-04-19
						//추가배송비 기장 안함, 월초 회의에서 결정 - 2018년 1월 30일 
						if($temp_sa_delivery_price != 0) { 
							insertCompanyLedger($conn, $sale_cp_no, $inout_date, $inout_type, null, "추가배송비", "1", $temp_sa_delivery_price,  null, 0, $temp_cate_01, $temp_tax_tf, $TEMP_MEMO, $temp_reserve_no, $temp_order_goods_no, "추가배송비", null, $s_adm_no, null);
						}
						*/

					}
				//}
			}

		}
	}

	if($mode == "SALE_CONFIRM") { 

		$sale_cp_no = selectCompanyNoByOrderGoodsNo($conn, $hid_order_goods_no);

		$arr_order_goods = selectOrderGoodsForDeliveryList($conn, $hid_order_goods_no);
		if(sizeof($arr_order_goods) <= 0) continue;

		$temp_reserve_no		= SetStringFromDB($arr_order_goods[0]["RESERVE_NO"]);
		$temp_goods_no			= SetStringFromDB($arr_order_goods[0]["GOODS_NO"]);
		$temp_goods_name		= ($cate_01 <> "" ? $cate_01.")" : "").$arr_order_goods[0]["GOODS_NAME"]."[".$arr_order_goods[0]["GOODS_CODE"]."]";
		$temp_order_state		= SetStringFromDB($arr_order_goods[0]["ORDER_STATE"]);
		$temp_buy_price			= SetStringFromDB($arr_order_goods[0]["BUY_PRICE"]);
		$temp_sale_price		= SetStringFromDB($arr_order_goods[0]["SALE_PRICE"]);
		$temp_discount_price	= SetStringFromDB($arr_order_goods[0]["DISCOUNT_PRICE"]);
		$temp_sa_delivery_price	= SetStringFromDB($arr_order_goods[0]["SA_DELIVERY_PRICE"]);
		$temp_work_end_date		= SetStringFromDB($arr_order_goods[0]["WORK_END_DATE"]);
		$temp_sale_confirm_tf	= SetStringFromDB($arr_order_goods[0]["SALE_CONFIRM_TF"]);
		$temp_cate_01			= SetStringFromDB($arr_order_goods[0]["CATE_01"]);
		$temp_tax_tf			= SetStringFromDB($arr_order_goods[0]["TAX_TF"]);
		$temp_cate_02			= SetStringFromDB($arr_order_goods[0]["CATE_02"]);

		$inout_type = "LR01";
		$inout_date = date("Y-m-d", strtotime("0 month"));

		$TEMP_MEMO = "";
		$arr_memo = getMemoFromOrderGoods($conn, $hid_order_goods_no);
		if(sizeof($arr_memo) > 0) { 
			$A = $arr_memo[0]["A"];
			//$B = $arr_memo[0]["B"];
			$C = $arr_memo[0]["C"];
			$D = $arr_memo[0]["D"];

			$TEMP_MEMO .= $A;
			//$TEMP_MEMO .= ($B != "" ? ($TEMP_MEMO != "" ? "/".$B : $B) : "");
			$TEMP_MEMO .= ($C != "" ? ($TEMP_MEMO != "" ? "/".$C : $C) : "");
			$TEMP_MEMO .= ($D != "" ? ($TEMP_MEMO != "" ? "/".$D : $D) : "");

		}

		$refund_able_qty = getRefundAbleQty($conn, $temp_reserve_no, $hid_order_goods_no);

		//개별택배나 외부 업체 발송중 개별 배송이 완료되지 않은 경우  매출 기장
		//$arr_cnt_delivery = cntDeliveryIndividual($conn, $hid_order_goods_no);

		if($temp_sale_confirm_tf == "N") { 
			//if($arr_cnt_delivery[0]["CNT_DELIVERY_PLACE"] <= 0) { 
				
			insertCompanyLedger($conn, $sale_cp_no, $inout_date, $inout_type, $temp_goods_no, $temp_goods_name, $refund_able_qty, $temp_sale_price, null, 0, $temp_cate_01, $temp_tax_tf, $TEMP_MEMO, $temp_reserve_no, $hid_order_goods_no, "매출상품", null, $s_adm_no, null);

			updateSaleConfirmState($conn, $hid_order_goods_no, $inout_date, 'Y', $s_adm_no);
			//}

			//매출할인이 있을경우 - 개별완료나 외부업체 발송중 배송지 지정된 경우엔 따로 안하기 떄문에 2017-04-19
			if($temp_discount_price != 0) { 
				insertCompanyLedger($conn, $sale_cp_no, $inout_date, $inout_type, null, "매출할인", "1", $temp_discount_price * -1,  null, 0, $temp_cate_01, $temp_tax_tf, $TEMP_MEMO, $temp_reserve_no, $hid_order_goods_no, "매출할인", null, $s_adm_no, null);
			}

			/*
			//세금 계산 승인 코드가 있을 경우
			if($temp_cate_02 <> "")
			{
				$arr_cl_no = getCompanyLedgerCLNoByOrderGoodsNo($conn, $temp_order_goods_no);

				foreach(explode("\n",$temp_cate_02) as $each_cf_code) { 
					$each_cf_code = trim(str_replace('\n','',$each_cf_code));

					if(trim($each_cf_code) != "") 
					{
						for($k = 0; $k < sizeof($arr_cl_no); $k ++) { 
							$cl_no = $arr_cl_no[$k]["CL_NO"];
							updateTaxInvoiceTF($conn, $cl_no, $temp_cate_03, $each_cf_code, 'Y', $s_adm_no);
						}
					}
				}
			}
			*/

			/*
			//추가배송비가 있을경우 - 개별완료나 외부업체 발송중 배송지 지정된 경우엔 따로 안하기 떄문에 2017-04-19
			//추가배송비 기장 안함, 월초 회의에서 결정 - 2018년 1월 30일 
			if($temp_sa_delivery_price != 0) { 
				insertCompanyLedger($conn, $sale_cp_no, $inout_date, $inout_type, null, "추가배송비", "1", $temp_sa_delivery_price,  null, 0, $temp_cate_01, $temp_tax_tf, $TEMP_MEMO, $temp_reserve_no, $temp_order_goods_no, "추가배송비", null, $s_adm_no, null);
			}
			*/
		}
	}

#====================================================================
# Request Parameter
#====================================================================

	if ( count($_GET) == 0 && count($_POST) == 0 ) { 
		if($s_adm_md_tf == "Y")
			$sel_opt_manager_no = $s_adm_no;
	}

	$mm_subtree	 = "3";

	$day_0 = date("Y-m-d",strtotime("0 month"));
	$day_1 = date("Y-m-d",strtotime("-1 day"));
	$day_7 = date("Y-m-d",strtotime("-7 day"));
	$day_31 = date("Y-m-d",strtotime("-1 month"));
	$day_180 = date("Y-m-d",strtotime("-6 month"));
	$day_365 = date("Y-m-d",strtotime("-12 month"));
	$day_1095 = date("Y-m-d",strtotime("-36 month"));

	if ($search_date_type == "") {
		$search_date_type = "order_date";
	} else {
		$search_date_type = trim($search_date_type);
	}

	if ($start_date == "") {
		$start_date = $day_31;
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = $day_0;
	} else {
		$end_date = trim($end_date);
	}

	if ($order_field == "")
		$order_field = "G.PAY_DATE";

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$con_cate_01 = trim($con_cate_01);
	$con_cate_02 = trim($con_cate_02);
	$con_cate_03 = trim($con_cate_03);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
	$con_use_tf = "Y";
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
		$nPageSize = 20;
	}

	$nPageBlock	= 10;
	
#	echo $start_date;
#	echo $end_date;

#===============================================================
# Get Search list count
#===============================================================

	//검색 조건 없을땐 한달, 있을 경우엔 6개월으로 세팅 - 속도개선용
	// if($sel_order_state.$cp_type.$cp_type2.$sel_cate_01.$sel_sale_confirm_tf.$con_work_flag.$sel_opt_manager_no.$sel_delivery_type. $sel_delivery_cp.$search_str <> "" ) { 
	// 	if($start_date == $day_31 && $end_date == $day_0 ) {
	// 		$start_date = $day_180;
	// 		$end_date = $day_0;
	// 	} 
	// }

	$nListCnt = totalCntManagerDelivery($conn, $search_date_type, $start_date, $end_date, $bulk_tf, $sel_order_state, $cp_type, $cp_type2, $sel_cate_01, $sel_sale_confirm_tf, $con_work_flag, $sel_opt_manager_no, $sel_delivery_type, $sel_delivery_cp, $con_use_tf, $del_tf, $search_field, $search_str);
	
	//echo $nListCnt;

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listManagerDelivery2($conn, $search_date_type, $start_date, $end_date, $bulk_tf, $sel_order_state, $cp_type, $cp_type2, $sel_cate_01, $sel_sale_confirm_tf, $con_work_flag, $sel_opt_manager_no, $sel_delivery_type, $sel_delivery_cp, $con_use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize);


		
	$arr_reserve_no = "";
	if (sizeof($arr_rs) > 0) {
		for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
			$arr_reserve_no	.= "'".trim($arr_rs[$j]["RESERVE_NO"])."',";
		}
	}
	$arr_reserve_no = rtrim($arr_reserve_no, ",");

	$arr_goods = listManagerOrderGoodsV2($conn, $arr_reserve_no);
	echo "$arr_reserve_no";
	$arr_rs_sum = sumManagerDelivery($conn, $search_date_type, $start_date, $end_date, $bulk_tf, $sel_order_state, $cp_type, $cp_type2, $sel_cate_01, $sel_sale_confirm_tf, $con_work_flag, $sel_opt_manager_no, $sel_delivery_type, $sel_delivery_cp, $con_use_tf, $del_tf, $search_field, $search_str);



	//$cnt_0 = cntOrderGoodsState($conn, '0', '', ''); //입금전
	//$cnt_1 = cntOrderGoodsState($conn, '1', '', ''); // 주문확인
	//$cnt_2 = cntOrderGoodsState($conn, '2', '', ''); // 배송대기
	//$cnt_3 = cntOrderGoodsState($conn, '3', '', ''); // 배송완료	

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<script type="text/javascript" src="../jquery/jquery.floatThead.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script>
  $(function() {
    $( ".datepicker" ).datepicker({
      showOn: "button",
      buttonImage: "/manager/images/calendar/cal.gif",
      buttonImageOnly: true,
      buttonText: "Select date",
	  showOn: "both",
	  dateFormat: "yy-mm-dd",
	  changeMonth: true,
      changeYear: true,
	  beforeShow: function() {
        setTimeout(function(){
            $('.ui-datepicker').css('z-index', 99999999999999);
        }, 0);
	  }
    });
  });
</script>
<script>
	$(function() {
		$('.autocomplete_off').attr('autocomplete', 'off');
	});
</script>
<script>
	$(function(){
		$('table.fixed_header_table').floatThead({
			getSizingRow: function($table){ // this is only called when using IE
				return $table.find('tbody tr:not(.grouping):visible:first>*');
			}, position: 'fixed'
		});
	});
</script>
<script language="javascript">

	function js_write() {

		/* 
		//리스트의 공급업체와 등록의 주문업체 변수 수정 필요 
		var frm = document.frm;
		
		frm.target = "";
		frm.method = "get";
		frm.action = "order_write.php";
		frm.submit();
		*/

		location.href = "order_write.php";
		
	}

	function js_view(rn, reserve_no) {

		var frm = document.frm;
		
		var url = "order_read.php?reserve_no="+reserve_no;

		NewWindow(url, 'order_detail','860','600','YES');
		
	}

	// 조회 버튼 클릭 시 
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}
	/*
	function js_toggle() {

		var frm = document.frm;
		var chk_cnt = 0;

		if (frm('chk_reserve_no[]') == null) {
			alert("선택할 주문이 없습니다.");
			return;
		}

		if (frm('chk_reserve_no[]').length != null) {
			
			for (i = 0 ; i < frm('chk_reserve_no[]').length; i++) {
				if (frm('chk_reserve_no[]')[i].checked == true) {
					chk_cnt = 1;
				}
			}
		
		} else {
			if (frm('chk_reserve_no[]').checked == true) chk_cnt = 1;
		}
		
		if (chk_cnt == 0) {
			alert("상태 변경할 주문을 선택해 주세요");
			return;
		}

		bDelOK = confirm('주문 상태를 변경 하시겠습니까?');
			
		if (bDelOK==true) {

			frm.mode.value = "T";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}
	*/

	function js_excel() {

		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.target = "";
		frm.action = "<?=str_replace("list","excel_list",$_SERVER[PHP_SELF])?>";
		frm.submit();

	}

	function js_all_check() {
		var frm = document.frm;
		
		if (frm['chk_order_no[]'] != null) {
			
			if (frm['chk_order_no[]'].length != null) {

				if (frm.all_chk.checked == true) {
					for (i = 0; i < frm['chk_order_no[]'].length; i++) {
						frm['chk_order_no[]'][i].checked = true;
					}
				} else {
					for (i = 0; i < frm['chk_order_no[]'].length; i++) {
						frm['chk_order_no[]'][i].checked = false;
					}
				}
			} else {
			
				if (frm.all_chk.checked == true) {
					frm['chk_order_no[]'].checked = true;
				} else {
					frm['chk_order_no[]'].checked = false;
				}
			}
		}
	}

	function js_order_confirm() {

		var frm = document.frm;
		
		frm.mode.value = "CU";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	
	}

	
	function js_sale_confirm_tf(order_goods_no) { 

		var frm = document.frm;
		
		if(confirm('기장 하시겠습니까?')) { 

			frm.mode.value = "SALE_CONFIRM";
			frm.hid_order_goods_no.value = order_goods_no;
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();

		}
	}

	function js_delivery_cp_all() {
		var frm = document.frm;
		
		for (i = 0; i < frm['delivery_cp[]'].length ; i++) {
			if (frm['delivery_no[]'][i].value == "") {
				frm['delivery_cp[]'][i].value = frm.delivery_cp_all.value;
			}
		}
	}

	var day_0 = "<?=$day_0?>";
	var day_1 = "<?=$day_1?>";
	var day_7 = "<?=$day_7?>";
	var day_31 = "<?=$day_31?>";
	var day_180 = "<?=$day_180?>";
	var day_365 = "<?=$day_365?>";
	var day_1095 = "<?=$day_1095?>";

	function js_search_date(iday) {

		var frm = document.frm;
		
		if (iday == 0) {
			frm.start_date.value = day_0;
			frm.end_date.value = day_0;
		}

		if (iday == 1) {
			frm.start_date.value = day_1;
			frm.end_date.value = day_0;
		}

		if (iday == 7) {
			frm.start_date.value = day_7;
			frm.end_date.value = day_0;
		}

		if (iday == 31) {
			frm.start_date.value = day_31;
			frm.end_date.value = day_0;
		}

		if (iday == 180) {
			frm.start_date.value = day_180;
			frm.end_date.value = day_0;
		}

		if (iday == 365) {
			frm.start_date.value = day_365;
			frm.end_date.value = day_0;
		}

		if (iday == 1095) {
			frm.start_date.value = day_1095;
			frm.end_date.value = day_0;
		}

		frm.nPage.value = "1";
		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_reset() {
		
		var frm = document.frm;
		frm.start_date.value = "<?=date("Y-m-d",strtotime("-1 month"))?>";
		frm.end_date.value = "<?=date("Y-m-d",strtotime("0 month"))?>";
		frm.sel_order_state.value = "";
		
		<? if ($s_adm_cp_type == "운영") { ?>
			frm.cp_type.value = "";
			frm.cp_type2.value = "";
		<? } ?>
		
		frm.order_field.value = "ORDER_DATE";
		frm.order_str[0].checked = true;
		frm.nPageSize.value = "20";
		frm.search_field.value = "ALL";
		frm.search_str.value = "";
	}

	function js_move() { 
		var frm = document.frm;
		
		frm.method = "post";
		frm.target = "_blank";
		frm.action = "delivery_list.php";
		frm.submit();
	}

	function js_pop_individual(reserve_no, order_goods_no) { 

		var frm = document.frm;
		
		var url = "pop_individual_delivery_list.php?reserve_no=" + reserve_no + "&order_goods_no=" + order_goods_no+"&search_str="+frm.search_str.value;

		NewWindow(url, 'pop_individual_delivery_list','1000','600','YES');
	}


	(function($){
		$.fn.extend({
			center: function () {
				return this.each(function() {
					var top = ($(window).height() - $(this).find("img").outerHeight()) / 2 + $(window).scrollTop();
					var left = ($(window).width() - $(this).find("img").outerWidth()) / 2;

					if($(this).find("img").outerHeight() == 0 || $(this).find("img").outerWidth() == 0)
						$(this).css({position:'absolute', margin:0, top: (100 + $(window).scrollTop()) +'px', left: 400 +'px'});
					else
						$(this).css({position:'absolute', margin:0, top: (top > 0 ? top : 0)+'px', left: (left > 0 ? left : 0)+'px'});
				});
			}
		}); 
	})(jQuery);

	$(function(){
	
		var img_frame = $("<div style='background-color: #EFEFEF; border: 1px solid #DEDEDE; padding:5px 5px 5px 5px; z-index:9999;'></div>");
		$(".goods_thumb").hover(function(){

			var origin_img = $(this).prop("src").replace("simg/s_50_50_","");
			
			img_frame.show().append($("<img src='"+origin_img+"' style='max-height:800px; max-width:600px;'/>"));

			$(this).after(img_frame);

			img_frame.center();

		}, function(){

			img_frame.empty().hide();

		});

		$(window).scroll(function() {
		   img_frame.empty().hide();
		});

	});
</script>
<style>
	table.rowstable02 tr.goods_end td {border-bottom:1px dotted #ebf3f6/*#86a4b3*/;} 
	tr.row_sum {background-color:#DEDEDE;}
	tr.row_sum > td {border-bottom:2px solid #86a4b3;}
</style>
</head>

<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="rn" value="">
<input type="hidden" name="reserve_no" value="">
<input type="hidden" name="hid_order_goods_no" value="">
<input type="hidden" name="use_tf" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<!--<input type="hidden" name="nPageSize" value="<?=$nPageSize?>">-->
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

				<h2 style="margin:0;">주문 리스트</h2>
				
				<div class="btnright" style="margin:0 0 5px 0;">
					<? if($sPageRight_I == "Y") { ?>
					<a href="javascript:js_write();"><img src="../images/admin/btn_regist_02.gif" alt="등록"></a>
					<? } ?>
				</div>
				
				<!--<div class="category_choice"></div>-->

				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="9%" />
					<col width="*" />
					<col width="9%" />
					<col width="*" />
					<col width="9%" />
					<col width="*" />
					<col width="8%" />
				</colgroup>
				<thead>
					<tr>
						<th>
							<select name="search_date_type">
								<option value="order_date" <? if ($search_date_type == "order_date" || $search_date_type == "") echo "selected" ?>>주문일</option>
								<option value="reg_date" <? if ($search_date_type == "reg_date") echo "selected" ?>>주문등록일</option>
								<option value="order_confirm_date" <? if ($search_date_type == "order_confirm_date") echo "selected" ?>>주문확인일</option>
								<option value="opt_outstock_date" <? if ($search_date_type == "opt_outstock_date") echo "selected" ?>>출고예정일</option>
								<option value="work_date" <? if ($search_date_type == "work_date") echo "selected" ?>>작업완료일</option>
								<option value="delivery_date" <? if ($search_date_type == "delivery_date") echo "selected" ?>>배송완료일</option>
							</select>
						</th>
						<td colspan="3">
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="start_date" value="<?=$start_date?>" maxlength="10"/>
							 ~ 
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="end_date" value="<?=$end_date?>" maxlength="10"/>
							&nbsp;<a href="javascript:js_search_date('0');"><input type="button" class="btntxt" value="당일"></a>
							<a href="javascript:js_search_date('1');"><input type="button" class="btntxt" value="전일"></a>
							<a href="javascript:js_search_date('7');"><input type="button" class="btntxt" value="7일"></a>
							<a href="javascript:js_search_date('31');"><input type="button" class="btntxt" value="1개월"></a> <a href="javascript:js_search_date('180');"><input type="button" class="btntxt" value="6개월"></a> <a href="javascript:js_search_date('365');"><input type="button" class="btntxt" value="1년"></a> <a href="javascript:js_search_date('1095');"><input type="button" class="btntxt" value="3년"></a>
							<!--<label><input type="checkbox" name="bulk_tf" value="Y" <? if($bulk_tf == 'Y') echo 'checked';?>/>출고미정/대량건</label>-->
						</td>
						<th>영업담당자</th>
						<td colspan="2">
							<? if ($s_adm_md_tf != "Y") { ?>
							<?= makeAdminInfoByMDSelectBox($conn,"sel_opt_manager_no"," style='width:70px;' ","전체","", $sel_opt_manager_no) ?>
							<? } else { ?>
								<input type="hidden" name="sel_opt_manager_no" value="<?=$sel_opt_manager_no?>"/>
								<?=getAdminName($conn, $sel_opt_manager_no)?>
							<? } ?>
							
							<? if($cp_type2 == "1480") { ?>
							<input type="button" onclick="js_open_pop_MRO();" name="cc" value=" MRO 완료 ">
							<? } ?>
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>주문상태</th>
						<td>
							<?=makeSelectBoxWithCondition($conn,"ORDER_STATE", "sel_order_state","90%", "선택하세요.", "", $sel_order_state, " AND DCODE IN ('1', '2', '3', '7', '8') " );?>
						</td>
						<th>배송방식</th>
						<td>
							<?=makeSelectBox($conn,"DELIVERY_TYPE", "sel_delivery_type","", "배송방식", "", $sel_delivery_type, "")?>
							<?=makeSelectBox($conn,"DELIVERY_CP", "sel_delivery_cp","90%", "택배회사", "", $sel_delivery_cp, "")?>
						</td>
						<th>주문상품종류</th>
						<td colspan="2"><?=makeSelectBox($conn, "ORDER_GOODS_TYPE", "sel_cate_01", "100", "전체", "", $sel_cate_01)?></td>
					</tr>
					<tr>
						<th>판매업체</th>
						<td>
							<input type="text" class="autocomplete_off" style="width:90%" placeholder="업체(명/코드) 입력후 엔터를 눌러주세요" name="txt_cp_type2" value="<?=getCompanyAutocompleteTextBox($conn,'',$cp_type2)?>" />
							<input type="hidden" name="cp_type2" value="<?=$cp_type2?>">

							<script>
								$(function(){

									$("input[name=txt_cp_type2]").keydown(function(e){

										if(e.keyCode==13) { 

											var keyword = $(this).val();
											
											if(keyword == "") { 
												$("input[name=cp_type2]").val('');
												js_search();
											} else { 
												$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('판매,판매공급') +"&term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
													if(data.length == 1) { 
														
														js_selecting_company("txt_cp_type2", data[0].label, "cp_type2", data[0].id);

													} else if(data.length > 1){ 
														NewWindow("../company/pop_company_searched_list.php?con_cp_type=판매,판매공급&search_str="+keyword + "&target_name=txt_cp_type2&target_value=cp_type2",'pop_company_searched_list','950','650','YES');

													} else 
														alert("검색결과가 없습니다.");
												});
											}
										}

									});
									
									$("input[name=txt_cp_type2]").keyup(function(e){
										var keyword = $(this).val();

										if(keyword == "") { 
											$("input[name=cp_type2]").val('');
										}
									});

								});

							</script>
						</td>
						<th>공급업체</th>
						<td>
							<input type="text" class="autocomplete_off" style="width:90%" placeholder="업체(명/코드) 입력후 엔터를 눌러주세요" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'',$cp_type)?>" />
							<input type="hidden" name="cp_type" value="<?=$cp_type?>">

							<script>
								$(function(){

									$("input[name=txt_cp_type]").keydown(function(e){

										if(e.keyCode==13) { 

											var keyword = $(this).val();
											if(keyword == "") { 
												$("input[name=cp_type]").val('');
												js_search();
											} else { 
												$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('구매,판매공급') +"&term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
													if(data.length == 1) { 
														
														js_selecting_company("txt_cp_type", data[0].label, "cp_type", data[0].id);

													} else if(data.length > 1){ 
														NewWindow("../company/pop_company_searched_list.php?con_cp_type=구매,판매공급&search_str="+keyword + "&target_name=txt_cp_type&target_value=cp_type",'pop_company_searched_list','950','650','YES');

													} else 
														alert("검색결과가 없습니다.");
												});
											}
										}

									});

									$("input[name=txt_cp_type]").keyup(function(e){
										var keyword = $(this).val();

										if(keyword == "") { 
											$("input[name=cp_type]").val('');
										}
									});

								});

							</script>
							<script>
								function js_selecting_company(target_name, cp_nm, target_value, cp_no) {
									
									$(function(){

										$("input[name="+target_name+"]").val(cp_nm);
										$("input[name="+target_value+"]").val(cp_no);

									});

									js_search();
								}
							</script>
						</td>
						<th>포장(작업) 상태</th>
						<td>
							<select name="con_work_flag">
								<option value="">선택하세요.</option>
								<option value="N" <? if ($con_work_flag == "N") echo "selected" ?>>포장(작업)중</option>
								<option value="Y" <? if ($con_work_flag == "Y") echo "selected" ?>>포장 완료</option>
							</select>
						</td>
						<td>
							
						</td>
					</tr>
					
					<tr>
						<th>정렬</th>
						<td>
							<select name="order_field" style="width:74px;">
								<option value="ORDER_DATE" <? if ($order_field == "ORDER_DATE") echo "selected"; ?> >주문일시</option>
								<option value="FINISH_DATE" <? if ($order_field == "FINISH_DATE") echo "selected"; ?> >배송완료일</option>
								<option value="G.PAY_DATE" <? if ($order_field == "G.PAY_DATE") echo "selected"; ?> >등록일</option>
								<option value="O_MEM_NM" <? if ($order_field == "O_MEM_NM") echo "selected"; ?> >주문자명</option>
								<option value="R_MEM_NM" <? if ($order_field == "R_MEM_NM") echo "selected"; ?> >수령자명</option>
								<option value="TOTAL_BUY_PRICE" <? if ($order_field == "TOTAL_BUY_PRICE") echo "selected"; ?> >총공급가</option>
								<option value="TOTAL_SALE_PRICE" <? if ($order_field == "TOTAL_SALE_PRICE") echo "selected"; ?> >총판매가</option>
								<option value="TOTAL_EXTRA_PRICE" <? if ($order_field == "TOTAL_EXTRA_PRICE") echo "selected"; ?> >총배송비</option>
								<option value="TOTAL_QTY" <? if ($order_field == "TOTAL_QTY") echo "selected"; ?> >총수량</option>
								<option value="TOTAL_DELIVERY_PRICE" <? if ($order_field == "TOTAL_DELIVERY_PRICE") echo "selected"; ?> >추가배송비</option>
								<option value="TOTAL_PRICE" <? if ($order_field == "TOTAL_PRICE") echo "selected"; ?> >합계</option>
								<!--<option value="TOTAL_PLUS_PRICE" <? if ($order_field == "TOTAL_PLUS_PRICE") echo "selected"; ?> >총판매이익</option>-->
							</select>&nbsp;
							<input type='radio' class="" name='order_str' value='DESC' <? if (($order_str == "DESC") || ($order_str == "")) echo " checked"; ?> > 내림
							<input type='radio' name='order_str' value='ASC' <? if ($order_str == "ASC") echo " checked"; ?>> 오름
						</td>

						<th>검색조건</th>
						<td colspan="3">
							<select name="nPageSize" style="width:74px;">
								<option value="20" <? if ($nPageSize == "20") echo "selected"; ?> >20개씩</option>
								<option value="50" <? if ($nPageSize == "50") echo "selected"; ?> >50개씩</option>
								<option value="100" <? if ($nPageSize == "100") echo "selected"; ?> >100개씩</option>
								<option value="200" <? if ($nPageSize == "200") echo "selected"; ?> >200개씩</option>
								<option value="300" <? if ($nPageSize == "300") echo "selected"; ?> >300개씩</option>
								<option value="400" <? if ($nPageSize == "400") echo "selected"; ?> >400개씩</option>
								<option value="500" <? if ($nPageSize == "500") echo "selected"; ?> >500개씩</option>
							</select>&nbsp;
							<select name="search_field" style="width:84px;">
								<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >통합검색</option>
								<option value="RESERVE_NO" <? if ($search_field == "RESERVE_NO") echo "selected"; ?> >주문번호</option>
								<option value="O_MEM_NM" <? if ($search_field == "O_MEM_NM") echo "selected"; ?> >주문자명</option>
								<option value="R_MEM_NM" <? if ($search_field == "R_MEM_NM") echo "selected"; ?> >수령자명</option>
								<option value="R_ADDR1" <? if ($search_field == "R_ADDR1") echo "selected"; ?> >수령자주소</option>

								<option value="ORDER_GOODS_NO" <? if ($search_field == "ORDER_GOODS_NO") echo "selected"; ?> >주문상품번호</option>
								<option value="GOODS_CODE" <? if ($search_field == "GOODS_CODE") echo "selected"; ?> >상품코드</option>
								<option value="GOODS_NAME" <? if ($search_field == "GOODS_NAME") echo "selected"; ?> >상품명</option>

								<option value="CP_CODE" <? if ($search_field == "CP_CODE") echo "selected"; ?> >회사코드</option>
								<option value="CP_NM" <? if ($search_field == "CP_NM") echo "selected"; ?> >회사명+지점명</option>

								<option value="CP_ORDER_NO" <? if ($search_field == "CP_ORDER_NO") echo "selected"; ?> >*외부주문번호</option>
								<option value="R_MEM_NM_ALL" <? if ($search_field == "R_MEM_NM_ALL") echo "selected"; ?> >*수령자(개별택배포함)</option>
								<option value="GOODS_NAME_ALL" <? if ($search_field == "GOODS_NAME_ALL") echo "selected"; ?> >*상품명+개별택배송장명</option>
								<option value="GOODS_CODE_ALL" <? if ($search_field == "GOODS_CODE_ALL") echo "selected"; ?> >*상품코드(세트상품포함)</option>
								<option value="R_ADDR_ALL" <? if ($search_field == "R_ADDR_ALL") echo "selected"; ?> >*수령자주소(개별택배포함)</option>
								<option value="REG_ADM" <? if ($search_field == "REG_ADM") echo "selected"; ?> >*등록자(처리자)</option>
								<option value="INDIVIDUAL_DELIVERY" <? if ($search_field == "INDIVIDUAL_DELIVERY") echo "selected"; ?> >*개별택배 통합검색</option>
								<option value="DELIVERY_NO" <? if ($search_field == "DELIVERY_NO") echo "selected"; ?> >*송장번호(내외포함)</option>
							</select>&nbsp;

							<input type="text" value="<?=$search_str?>" name="search_str" size="18" class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();"/>
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
							<a href="javascript:js_reset();"><img src="/manager/images/admin/btn_in.gif" alt="reset"/></a>
							
						</td>
						<td align="right">
							<a href="javascript:js_move();" style="text-decoration:underline;">배송관리로</a>
						</td>
					</tr>
					
				</tbody>
			</table>
			<div class="sp10"></div>
			<div style="width: 95%; text-align: right; margin: 0;">
<? if ($sPageRight_U == "Y") {?>
	<input type="button" name="a0" value=" 주문확인 (배송준비중) " class="btntxt" onclick="this.style.visibility='hidden'; js_order_confirm();">
<? } ?>
			</div>
			<b>총 <?=number_format($nListCnt)?> 건</b>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<!--<b><font color="orange">미처리주문</font></b>&nbsp;&nbsp;&nbsp;&nbsp;
			<b><font color="blue">입금전</font> <font color="red"><?=$cnt_0?></font> <font color="blue">건</font></b>&nbsp;&nbsp;
			<b><font color="blue">주문확인전</font> <font color="red"><?=$cnt_1?></font> <font color="blue">건</font></b>&nbsp;&nbsp;
			<b><font color="blue">배송완료전</font> <font color="red"><?=$cnt_2?></font> <font color="blue">건</font></b>&nbsp;&nbsp;-->
			

			<table cellpadding="0" cellspacing="0" class="rowstable02 fixed_header_table" border="0" width="100%">
				<colgroup>
					<col width="9%" />
					<col width="5%" />
					<col width="5%" />
					<col width="5%" />
					<col width="7%" />
					<col width="7%" />
					<col width="7%" />
					<col width="7%" />
					<col width="7%" />
					<col width="5%" />
					<col width="7%" />
					<col width="9%" />
					<col width="7%" />
					<col width="5%" />
					<col width="8%" />
				</colgroup>
				<thead>
					<tr>
						<th>주문번호</th>
						<th colspan="2">판매업체명</th>
						<th>주문자명</th>
						<th>주문자연락처</th>
						<th>수령자명</th>
						<th>수령자연락처</th>
						<th colspan="5">주소</th>
						<th>영업담당</th>
						<th>처리자</th>
						<th class="end">주문일시</th>
					</tr>
					<tr>
						<th><input type="checkbox" name="all_chk" onClick="js_all_check();">&nbsp;&nbsp;주문확인</th>
						<th>이미지</th>
						<th colspan="3">상품명</th>
						<th>판매가</th>
						<th>수량</th>
						<th>포장지</th>
						<th>스티커</th>
						<th>아웃박스<br/>스티커</th>
						<th>인쇄메세지</th>
						<th>작업/발주메모</th>
						<th>출고예정일</th>
						<th>배송방식</th>
						<th class="end">기장여부</th>
					</tr>
				</thead>
				<tbody>

				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$RESERVE_NO							= trim($arr_rs[$j]["RESERVE_NO"]);
							$MEM_TYPE							= trim($arr_rs[$j]["MEM_TYPE"]);
							$MEM_NO								= trim($arr_rs[$j]["MEM_NO"]);
							$CP_NO								= trim($arr_rs[$j]["CP_NO"]);
							
							$PAY_TYPE							= trim($arr_rs[$j]["PAY_TYPE"]);
							$ORDER_CONFIRM_DATE					= trim($arr_rs[$j]["ORDER_CONFIRM_DATE"]);
							
							//$ORDER_STATE						= trim($arr_rs[$j]["ORDER_STATE"]);
							$PAY_STATE							= trim($arr_rs[$j]["PAY_STATE"]);
							$O_MEM_NM							= trim($arr_rs[$j]["O_MEM_NM"]);
							$O_PHONE							= trim($arr_rs[$j]["O_PHONE"]);
							$O_HPHONE							= trim($arr_rs[$j]["O_HPHONE"]);
							$R_MEM_NM							= trim($arr_rs[$j]["R_MEM_NM"]);
							$R_ZIPCODE							= trim($arr_rs[$j]["R_ZIPCODE"]);
							$R_ADDR1							= trim($arr_rs[$j]["R_ADDR1"]);
							$R_PHONE							= trim($arr_rs[$j]["R_PHONE"]);
							$R_HPHONE							= trim($arr_rs[$j]["R_HPHONE"]);
							$TOTAL_BUY_PRICE					= trim($arr_rs[$j]["TOTAL_BUY_PRICE"]);
							$TOTAL_SALE_PRICE					= trim($arr_rs[$j]["TOTAL_SALE_PRICE"]);
							$TOTAL_EXTRA_PRICE					= trim($arr_rs[$j]["TOTAL_EXTRA_PRICE"]);
							$TOTAL_QTY							= trim($arr_rs[$j]["TOTAL_QTY"]);
							$TOTAL_DELIVERY_PRICE				= trim($arr_rs[$j]["TOTAL_DELIVERY_PRICE"]);
							$TOTAL_SA_DELIVERY_PRICE			= trim($arr_rs[$j]["TOTAL_SA_DELIVERY_PRICE"]);
							$TOTAL_DISCOUNT_PRICE				= trim($arr_rs[$j]["TOTAL_DISCOUNT_PRICE"]);

							$TOTAL_PRICE						= trim($arr_rs[$j]["TOTAL_PRICE"]);
							$TOTAL_PLUS_PRICE					= trim($arr_rs[$j]["TOTAL_PLUS_PRICE"]);
							$LEE								= trim($arr_rs[$j]["LEE"]);
							
							$OPT_MANAGER_NO						= trim($arr_rs[$j]["OPT_MANAGER_NO"]);
							$admName = getAdminName($conn, $OPT_MANAGER_NO);

							$REG_ADM						= trim($arr_rs[$j]["REG_ADM"]);
							$regADMName = getAdminName($conn, $REG_ADM);

							$ORDER_DATE							= trim($arr_rs[$j]["ORDER_DATE"]);
							//$PAY_DATE							= trim($arr_rs[$j]["PAY_DATE"]);
							$CANCEL_DATE						= trim($arr_rs[$j]["CANCEL_DATE"]);

							$DELIVERY_TYPE						= trim($arr_rs[$j]["DELIVERY_TYPE"]);
							$REG_DATE							= trim($arr_rs[$j]["REG_DATE"]);
							
							$ORDER_DATE		= date("Y-m-d H:i:s",strtotime($ORDER_DATE));

							if ($TOTAL_QTY == 0)
								$str_cancel_style = "cancel_order";
							else
								$str_cancel_style = "";

						?>
						
						<tr class="order <?=$str_cancel_style?>" height="35">
							<td class="order"><a href="javascript:js_view('<?=$rn?>','<?=$RESERVE_NO?>');"><?=$RESERVE_NO?></a></td>
							<td class="modeual_nm" colspan="2"><?= getCompanyName($conn, $CP_NO);?></td>
							<td><?=$O_MEM_NM?></td>
							<td><?=$O_PHONE?></td>
							<td><?=$R_MEM_NM?></td>
							<td><?=$R_PHONE?></td>
							<td colspan="5" class="modeual_nm"><?=$R_ADDR1?></td>
							<td><?=$admName?></td>
							<td><?=$regADMName?></td>
							<td colspan="2" class="filedown"><?=$ORDER_DATE?></td>
						</tr>
						
						<?
							//$arr_goods = null; //listManagerOrderGoods($conn, $RESERVE_NO, $MEM_NO, "Y", "N");

							if (sizeof($arr_goods) > 0) {
								for ($h = 0 ; $h < sizeof($arr_goods); $h++) {

									$OG_RESERVE_NO				= trim($arr_goods[$h]["RESERVE_NO"]);
									if($RESERVE_NO != $OG_RESERVE_NO)
										continue;
									
									$ORDER_GOODS_NO				= trim($arr_goods[$h]["ORDER_GOODS_NO"]);
									$GROUP_NO					= trim($arr_goods[$h]["GROUP_NO"]);
									$BUY_CP_NO					= trim($arr_goods[$h]["BUY_CP_NO"]);
									$CP_ORDER_NO				= trim($arr_goods[$h]["CP_ORDER_NO"]);
									$GOODS_NO					= trim($arr_goods[$h]["GOODS_NO"]);
									$GOODS_CODE					= trim($arr_goods[$h]["GOODS_CODE"]);
									$GOODS_NAME					= SetStringFromDB($arr_goods[$h]["GOODS_NAME"]);
									$BUY_PRICE					= trim($arr_goods[$h]["BUY_PRICE"]);
									$SALE_PRICE					= trim($arr_goods[$h]["SALE_PRICE"]);
									//$EXTRA_PRICE				= trim($arr_goods[$h]["EXTRA_PRICE"]);

									//C.OPT_STICKER_NO, C.OPT_OUTBOX_TF, C.OPT_OUTBOX_CNT, C.OPT_WRAP_NO, C.OPT_PRINT_MSG, C.OPT_OUTSTOCK_DATE, C.OPT_MEMO

									$DELIVERY_CP				= trim($arr_goods[$h]["DELIVERY_CP"]);
									$DELIVERY_NO				= trim($arr_goods[$h]["DELIVERY_NO"]);

									$DELIVERY_CNT				= trim($arr_goods[$h]["DELIVERY_CNT"]);

									$DELIVERY_TYPE				= trim($arr_goods[$h]["DELIVERY_TYPE"]);

									//$SUM_PRICE					= trim($arr_goods[$h]["SUM_PRICE"]);
									//$PLUS_PRICE					= trim($arr_goods[$h]["PLUS_PRICE"]);
									//$GOODS_LEE					= trim($arr_goods[$h]["LEE"]);
									$QTY						= trim($arr_goods[$h]["QTY"]);
									//$PAY_DATE					= trim($arr_goods[$h]["PAY_DATE"]);
									$DELIVERY_DATE				= trim($arr_goods[$h]["DELIVERY_DATE"]);
									$FINISH_DATE				= trim($arr_goods[$h]["FINISH_DATE"]);
									$ORDER_STATE				= trim($arr_goods[$h]["ORDER_STATE"]);
									$ORDER_CONFIRM_DATE			= trim($arr_goods[$h]["ORDER_CONFIRM_DATE"]);

									$OPT_STICKER_NO				= trim($arr_goods[$h]["OPT_STICKER_NO"]);
									$OPT_OUTBOX_TF				= trim($arr_goods[$h]["OPT_OUTBOX_TF"]);
									$OPT_WRAP_NO				= trim($arr_goods[$h]["OPT_WRAP_NO"]);
									$OPT_STICKER_MSG			= trim($arr_goods[$h]["OPT_STICKER_MSG"]);
									$OPT_PRINT_MSG				= trim($arr_goods[$h]["OPT_PRINT_MSG"]);
									$OPT_OUTSTOCK_DATE			= trim($arr_goods[$h]["OPT_OUTSTOCK_DATE"]);

									$SALE_CONFIRM_TF			= trim($arr_goods[$h]["SALE_CONFIRM_TF"]);
									$SALE_CONFIRM_YMD			= trim($arr_goods[$h]["SALE_CONFIRM_YMD"]);

									$HAS_GOODS_REQUEST			= trim($arr_goods[$h]["HAS_GOODS_REQUEST"]);
									
									if($OPT_OUTSTOCK_DATE != "" && $OPT_OUTSTOCK_DATE != "0000-00-00 00:00:00" && $OPT_OUTSTOCK_DATE != "1970-01-01 00:00:00")
										$OPT_OUTSTOCK_DATE			= date("n월 j일", strtotime($OPT_OUTSTOCK_DATE));
									else 
										$OPT_OUTSTOCK_DATE = "출고미정";

									$OPT_MEMO					= trim($arr_goods[$h]["OPT_MEMO"]);
									$OPT_REQUEST_MEMO			= trim($arr_goods[$h]["OPT_REQUEST_MEMO"]);
									$OPT_SUPPORT_MEMO			= trim($arr_goods[$h]["OPT_SUPPORT_MEMO"]);

									$CATE_01					= trim($arr_goods[$h]["CATE_01"]);
									$CATE_04					= trim($arr_goods[$h]["CATE_04"]);
									$WORK_FLAG					= trim($arr_goods[$h]["WORK_FLAG"]);
									$TAX_TF						= trim($arr_goods[$h]["TAX_TF"]);


									if ($TAX_TF == "비과세") {
										$STR_TAX_TF = "<font color='orange'>(비과세)</font>";
									} else {
										$STR_TAX_TF = "<font color='navy'>(과세)</font>";
									}

									
									$SALE_PRICE = abs($SALE_PRICE);

									$IMG_URL = getImage($conn, $GOODS_NO, "50", "50");

									if($CATE_01 <> "")
										$str_cate_01 = $CATE_01.") ";
									else 
										$str_cate_01 = "";

									if ($CATE_04 == "CHANGE") {
										$str_cate_04 = "<font color='red'>(교환건)</font>";
									} else {
										$str_cate_04 = "";
									}

									if ($REQ_DATE <> "")  {
										$REQ_DATE		= date("Y-m-d H:i",strtotime($REQ_DATE));
									}
									
									if ($DELIVERY_CP <> "") {
										if ($FINISH_DATE <> "")  {
											$FINISH_DATE		= date("Y-m-d H:i",strtotime($FINISH_DATE));
										}
									} else {
										$FINISH_DATE = "";
									}
									
									if ($h == (sizeof($arr_goods)-1)) {

										if ($ORDER_STATE == "1") {
											$str_tr = "goods_1_end";
										} else if ($ORDER_STATE == "3") {
											$str_tr = "goods_3_end";
										} else {
											$str_tr = "goods_end";
										}

									} else {

										if ($ORDER_STATE == "1") {
											$str_tr = "goods_1";
										} else if ($ORDER_STATE == "3") {
											$str_tr = "goods_3";
										} else {
											$str_tr = "goods";
										}
									}
									
									$OPT_OUTBOX_TF = ($OPT_OUTBOX_TF == "Y" ? "있음" : "" );

									$str_price_class = "price";
									$str_state_class = "state";

									$refund_able_qty = $QTY;
									if (($ORDER_STATE == "1") || ($ORDER_STATE == "2")) {

										if($DELIVERY_TYPE == 3 || $DELIVERY_TYPE == 98 || $GROUP_NO != 0 ) {
											$refund_able_qty = getRefundAbleQty($conn, $RESERVE_NO, $ORDER_GOODS_NO);
										}  
									
									} else if (($ORDER_STATE == "3")) {
										if($DELIVERY_TYPE == 3 || $DELIVERY_TYPE == 98 || $GROUP_NO != 0 ) {
											$refund_able_qty = getRealDeliveryQty($conn, $RESERVE_NO, $ORDER_GOODS_NO);
										}
									
									} else if ($ORDER_STATE == "7") {
										$refund_able_qty = -$QTY;

										$str_price_class = "price_refund";
										$str_state_class = "state_refund";

									} 

									if ($refund_able_qty == 0)
										$str_cancel_style = "cancel_goods";
									else
										$str_cancel_style = "";
									
									if (($ORDER_STATE == "1") || ($ORDER_STATE == "2") || ($ORDER_STATE == "3") || ($ORDER_STATE == "7")) {
										//if ($refund_able_qty <> 0) {
						?>
						
						<tr class="<?=$str_tr?> <?=$str_cancel_style?>" height="35">
							<td class="modeual_nm">
								
								<? if ($ORDER_STATE <> "1") { ?>
									<?	if($ORDER_CONFIRM_DATE != "")
										$ORDER_CONFIRM_DATE		= date("Y-m-d H:i",strtotime($ORDER_CONFIRM_DATE));
									?>
									<?=$ORDER_CONFIRM_DATE?>
								<? } else { ?>
									<input type="checkbox" name="chk_order_no[]" value="<?=$RESERVE_NO?>|<?=$ORDER_GOODS_NO?>|<?=$CATE_01?>|<?=$REG_ADM?>">&nbsp;&nbsp;주문확인
								<? } ?>
							</td>
							<td title="<?=$GOODS_CODE?>">
								<img src="<?=$IMG_URL?>" data-thumbnail="<?=$IMG_URL?>" class="goods_thumb" width="50px" height="50px">
							</td>
							<td class="modeual_nm" colspan="3"><?= $HAS_GOODS_REQUEST > 0 ? "<b>*</b> " : "" ?>  <?=$STR_TAX_TF?><?=$str_cate_01?><?=$GOODS_NAME?>[<?=$GOODS_CODE?>]
							</td>
							<td class="price"><?=number_format($SALE_PRICE)?></td>
							<td class="<?=$str_price_class?>"><?=$str_cate_04?> <?=number_format($refund_able_qty)?>
								<?
									if($QTY <> $refund_able_qty && $ORDER_STATE <> "7") {
								?>
									<br/><span style="color:#A2A2A2;">최초:<?=$QTY?></span>
								<?
									}
								?>
							</td>
							<td class="modeual_nm"><?=getGoodsName($conn, $OPT_WRAP_NO)?></td>
							<td class="modeual_nm" title="<?=$OPT_STICKER_MSG?>"><?=getGoodsName($conn, $OPT_STICKER_NO)?></td>
							<td class="modeual_nm"><?=$OPT_OUTBOX_TF?></td>
							<td class="modeual_nm"><?=$OPT_PRINT_MSG?></td>
							<td class="modeual_nm"><?=$OPT_MEMO?>
							<?if($OPT_REQUEST_MEMO != "") echo "<br/><b>/</b>발주:".$OPT_REQUEST_MEMO; ?>
							<?if($OPT_SUPPORT_MEMO != "") echo "<br/><b>/</b><br/><span style='color:red;'>".$OPT_SUPPORT_MEMO."</span>"; ?>
							</td>
							<td class="modeual_nm"><?=$OPT_OUTSTOCK_DATE?></td>
							<td class="filedown">
								
								
								<? if($DELIVERY_TYPE == "3" || $DELIVERY_TYPE == "98") { ?>
									<a href="javascript:js_pop_individual('<?=$RESERVE_NO?>','<?=$ORDER_GOODS_NO?>');"><b><?=getDcodeName($conn,"DELIVERY_TYPE",$DELIVERY_TYPE);?></b>
								
								<?
									/*
									$arr_individual = cntDeliveryIndividual($conn, $ORDER_GOODS_NO);
									if (sizeof($arr_individual) > 0) {
										$CNT_DELIVERY_PLACE = $arr_individual[0]["CNT_DELIVERY_PLACE"];
										$TOTAL_GOODS_DELIVERY_QTY = $arr_individual[0]["TOTAL_GOODS_DELIVERY_QTY"];

										echo "<br/>배송지 ".$CNT_DELIVERY_PLACE."곳"; 
									} else { 
										echo "<br/>배송지 미입력"; 
									}
									*/

								?>
									</a>

								<? } else { ?>
									<b><?=getDcodeName($conn,"DELIVERY_TYPE",$DELIVERY_TYPE);?></b>
								<? } ?>
								
									
								<!--<?=getDcodeName($conn,"DELIVERY_TYPE",$DELIVERY_TYPE);?>-->

								
							</td>
							<? if($ORDER_STATE <> "7" && $refund_able_qty <> 0) { 
									if (($SALE_CONFIRM_TF == "N") || ($SALE_CONFIRM_TF == "") ) {
							?>
									<td colspan="2">
										<a href="javascript:js_sale_confirm_tf('<?=$ORDER_GOODS_NO?>')" style='color:gray; text-decoration:underline;'>미기장</a>
									</td>
							<?
									} else {
							?>
									<td colspan="2">
										<font color='navy'>기장<br/>
										(<?=$SALE_CONFIRM_YMD?>)
										</font>
									</td>
							<?
									}
								} else {
							?>
							<td colspan="2">&nbsp;</td>
							<?  }  ?>
						</tr>
						
						<?
										
									} 
								}
							} 

						?>
								
						<tr height="35" class="row_sum">
							<td class="order"><b>주문합계 :</b></td>
							<td class="price" colspan="3"><b>총 판매가: <?=number_format($TOTAL_SALE_PRICE)?></b></td>
							<td class="price" colspan="2"><b>총 수량: <?=number_format($TOTAL_QTY)?></b></td>
							<td class="price" colspan="3"><b>총 추가배송비: <?=number_format($TOTAL_SA_DELIVERY_PRICE)?></b></td>
							<td class="price" colspan="3"><b>총 할인: <?=number_format($TOTAL_DISCOUNT_PRICE)?></b></td>
							<td class="price" colspan="4"><b>총 매출 합계: <?=number_format($TOTAL_SALE_PRICE + $TOTAL_SA_DELIVERY_PRICE - $TOTAL_DISCOUNT_PRICE  )?></b></td>
						</tr>

						<?
						}
					}else{
						?>
						<tr class="order">
							<td height="50" align="center" colspan="14">데이터가 없습니다. </td>
						</tr>
					<?
						}
					?>
				</tbody>
			</table>

			<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">
<? if ($sPageRight_U == "Y") {?>
	<input type="button" name="a0" value=" 주문확인 (배송준비중) " class="btntxt" onclick="this.style.visibility='hidden'; js_order_confirm();">&nbsp;&nbsp;&nbsp;
	<!--input type="button" name="aa" value=" 송장입력 (배송완료) " class="btntxt" onclick="js_delivery();"--> 
<? } ?>
			</div>
					<!-- --------------------- 페이지 처리 화면 START -------------------------->
					<?
						# ==========================================================================
						#  페이징 처리
						# ==========================================================================
						if (sizeof($arr_rs) > 0) {
							#$search_field		= trim($search_field);
							#$search_str			= trim($search_str);
							$strParam = $strParam."&nPageSize=".$nPageSize."&search_date_type=".$search_date_type."&search_field=".$search_field."&search_str=".$search_str."&start_date=".$start_date."&end_date=".$end_date."&bulk_tf=".$bulk_tf;
							$strParam = $strParam."&sel_order_state=".$sel_order_state."&sel_delivery_type=".$sel_delivery_type."&sel_delivery_cp=".$sel_delivery_cp."&con_work_flag=".$con_work_flag."&sel_opt_manager_no=".$sel_opt_manager_no."&cp_type=".$cp_type."&cp_type2=".$cp_type2."&sel_cate_01=".$sel_cate_01."&sel_sale_confirm_tf=".$sel_sale_confirm_tf."&order_field=".$order_field."&order_str=".$order_str;
					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- 페이지 처리 화면 END -------------------------->
				<br />

				<? 
					if(sizeof($arr_rs_sum) > 0) { 
						$SUM_TOTAL_SALE_PRICE			= $arr_rs_sum[0]["SUM_TOTAL_SALE_PRICE"];
						$SUM_TOTAL_QTY					= $arr_rs_sum[0]["SUM_TOTAL_QTY"];
						$SUM_TOTAL_SA_DELIVERY_PRICE	= $arr_rs_sum[0]["SUM_TOTAL_SA_DELIVERY_PRICE"];
						$SUM_TOTAL_DISCOUNT_PRICE		= $arr_rs_sum[0]["SUM_TOTAL_DISCOUNT_PRICE"];
						$SUM_TOTAL_SALE_PRICE			= $arr_rs_sum[0]["SUM_TOTAL_SALE_PRICE"];
						$SUM_TOTAL_SA_DELIVERY_PRICE	= $arr_rs_sum[0]["SUM_TOTAL_SA_DELIVERY_PRICE"];
						$SUM_TOTAL_DISCOUNT_PRICE		= $arr_rs_sum[0]["SUM_TOTAL_DISCOUNT_PRICE"];
				
				?>
				<table cellpadding="0" cellspacing="0" class="rowstable02" border="0" width="100%">
				<colgroup>
					<col width="9%" />
					<col width="5%"/>
					<col width="5%"/>
					<col width="5%" />
					<col width="7%" />
					<col width="7%" />
					<col width="7%" />
					<col width="7%" />
					<col width="7%" />
					<col width="7%" />
					<col width="7%" />
					<col width="7%" />
					<col width="7%" />
					<col width="5%" />
					<col width="8%" />
				</colgroup>
					<tr height="50" style="background-color:#000; color:#fff;">
						<td class="order"><b>검색된 <br/>주문합계 :</b></td>
						<td class="price" colspan="3"><b>판매가 합계: <br/><?=number_format($SUM_TOTAL_SALE_PRICE)?></b></td>
						<td class="price" colspan="2"><b>수량 합계: <br/><?=number_format($SUM_TOTAL_QTY)?></b></td>
						<td class="price" colspan="3"><b>추가배송비 합계: <br/><?=number_format($SUM_TOTAL_SA_DELIVERY_PRICE)?></b></td>
						<td class="price" colspan="3"><b>할인 합계: <br/><?=number_format($SUM_TOTAL_DISCOUNT_PRICE)?></b></td>
						<td class="price" colspan="4"><b>매출 합계: <br/><?=number_format($SUM_TOTAL_SALE_PRICE + $SUM_TOTAL_SA_DELIVERY_PRICE - $SUM_TOTAL_DISCOUNT_PRICE)?></b></td>
					</tr>
				</table>
				<? } ?>

				<div class="sp50"></div>
		<!-- // E: mwidthwrap -->
			</div>
			<!-- // E: mwidthwrap -->

		</td>
	</tr>
	</table>
	<a style="display:scroll;position:fixed;bottom:10px;right:10px;" href="#">▲ 위로</a>
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