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
	require "../../_classes/biz/member/member.php";
	require "../../_classes/biz/payment/payment.php";
	require "../../_classes/biz/board/board.php";
	require "../../_classes/biz/syscode/syscode.php";
	require "../../_classes/biz/company/company.php";
	require "../../_classes/biz/work/work.php";

#====================================================================
# Request Parameter
#====================================================================

	$reserve_no			= trim($reserve_no);
	$order_goods_no		= trim($order_goods_no);
	$individual_no		= trim($individual_no);
	$tab_index          = 0;

	if ($mode == "U") {

		if (isset($chk)) 
		{
			for($j = 0; $j < sizeof($order_goods_delivery_no); $j ++) { 

				if(in_array($order_goods_delivery_no[$j], $chk)) { 

					$base_order_goods_delivery_no	= $order_goods_delivery_no[$j]; 
					$temp_delivery_no				= $delivery_no[$j]; 
					$temp_delivery_fee_code			= $delivery_fee_code[$j]; 
					$temp_use_tf					= $use_tf[$j]; 

					$temp_delivery_fee = getDcodeName($conn, "DELIVERY_FEE", $temp_delivery_fee_code); 

					updateOrderDeliveryPaper($conn, $base_order_goods_delivery_no, $temp_delivery_no, $temp_delivery_fee_code, $temp_delivery_fee, $temp_use_tf, $s_adm_no);
				}
			}
		}

?>
<script language="javascript">
		alert('수정 되었습니다.');
</script>
<?
	}

	if ($mode == "APPEND") {
		//echo "mode : APPEND<br>";
		if (isset($chk)) 
		{
			$base_order_goods_delivery_no = $chk[0]; 

			appendOrderDeliveryPaper($conn, $base_order_goods_delivery_no, $individual_no, $chk_work_date, $s_adm_no);

			if($individualno_chkval == 'N')
			{
				$individual_no = '';
			}
?>
<script language="javascript">	
		alert('추가 되었습니다.');
</script>
<?
		}
	}

	if ($mode == "I") {
		//echo "mode : I<br>";
		// print_r($_REQUEST);
		// echo "<br>";
		// echo "DELIVERY_CP : $DELIVERY_CP<br>";
		// exit;

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
		if($DELIVERY_CP == "" || $DELIVERY_FEE == "") {  
			?>
			<script>
				alert('택배회사가 지정되어 있지 않습니다');
			</script>	
			<?
			goto Skip;
		}

		//개별택배의 개별주소가 입력되지 않았을때 송장생성 불가
		if($DELIVERY_TYPE == "3" && ($individual_no == null || $individual_no == "")) {  
			?>
			<script>
				alert('개별택배의 배송지가 입력되지 않았습니다');
			</script>	
			<?
			goto Skip;
		}

		//직접수령, 퀵서비스, 외부업체발송, 기타는 송장 생성 안함
		if($DELIVERY_TYPE == "1" || $DELIVERY_TYPE == "2" || $DELIVERY_TYPE == "98" || $DELIVERY_TYPE == "99") {
			?>
			<script>
				alert('직접수령, 퀵서비스, 외부업체발송, 기타는 송장을 생성하지 않습니다');
			</script>	
			<?
			goto Skip;
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
				$R_MEM_NM = SetStringFromDB($arr_order[0]["R_MEM_NM"]);
				$R_PHONE  = SetStringFromDB($arr_order[0]["R_PHONE"]);
				$R_HPHONE = SetStringFromDB($arr_order[0]["R_HPHONE"]);
				$R_ADDR1  = SetStringFromDB($arr_order[0]["R_ADDR1"]);
				$MEMO	  = SetStringFromDB($arr_order[0]["MEMO"]);
			
			} else {
				$arr_individual = selectDeliveryIndividual($conn, $individual_no);
		
				$R_MEM_NM					= SetStringFromDB($arr_individual[0]["R_MEM_NM"]);
				$R_PHONE					= SetStringFromDB($arr_individual[0]["R_PHONE"]);
				$R_HPHONE					= SetStringFromDB($arr_individual[0]["R_HPHONE"]);
				$R_ADDR1					= SetStringFromDB($arr_individual[0]["R_ADDR1"]);
				$MEMO						= SetStringFromDB($arr_individual[0]["MEMO"]);
				$INDIVIDUAL_DELIVERY_TYPE	= SetStringFromDB($arr_individual[0]["DELIVERY_TYPE"]);
				$USE_TF						= SetStringFromDB($arr_individual[0]["USE_TF"]);

				//사용안함일 경우 패스
				if($USE_TF != "Y") 
					goto Skip;

				//택배가 아니므로 패스
				if($INDIVIDUAL_DELIVERY_TYPE != "0")
					goto Skip;
				
				//개별 입력 배송지가 없을경우 기본 배송지로 입력
				if($R_ADDR1 == "")							
					$R_ADDR1  = $arr_order[0]["R_ADDR1"];

				$GOODS_DELIVERY_NAME	= SetStringFromDB($arr_individual[0]["GOODS_DELIVERY_NAME"]); 
				$SUB_QTY				= SetStringFromDB($arr_individual[0]["SUB_QTY"]);
			}

			//수령자 간격없애기 /*2016-02-25 과장님*/
			$R_MEM_NM = str_replace(" ","", $R_MEM_NM); 
			
			if($MEMO == "")
				$MEMO = $MEMO_ALL;

			$arr_order_goods = selectOrderGoods($conn, $order_goods_no);
			if(sizeof($arr_order_goods) > 0) { 
				for($i=0; $i < sizeof($arr_order_goods); $i++) {//무조건 하나 아니면 안나옴

					$CP_ORDER_NO = SetStringFromDB($arr_order_goods[$i]["CP_ORDER_NO"]);
					$GOODS_NAME  = SetStringFromDB($arr_order_goods[$i]["GOODS_NAME"]);
					$WORK_SEQ	 = SetStringFromDB($arr_order_goods[$i]["WORK_SEQ"]);
					$CATE_01	 = SetStringFromDB($arr_order_goods[$i]["CATE_01"]);
					
					if ($individual_no != "")
						$QTY = $SUB_QTY;
					else 
						$QTY = getRefundAbleQty($conn, $reserve_no, $order_goods_no); 

					if($QTY == 0) //전체 취소일경우
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

					$DELIVERY_CNT_IN_BOX = $arr_order_goods[$i]["DELIVERY_CNT_IN_BOX"];
					
					$total_paper_qty = ceil($QTY / $DELIVERY_CNT_IN_BOX);

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

						$order_goods_delivery_no = insertOrderDeliveryPaper(
											$conn, $chk_work_date, $reserve_no, $order_goods_no, $individual_no, 
											$CP_NO, $DELIVERY_CNT, $SEQ_OF_DELIVERY, $RECEIVER_NAME, $R_PHONE, 
											$R_HPHONE, $R_ADDR1, $CON_ORDER_QTY, $MEMO, $SENDER_NM, 
											$SENDER_PHONE, $SENDER_NM, $SENDER_PHONE, $CON_PAYMENT_TYPE, $CON_SEND_CP_ADDR, 
											$GOODS_NAME, $DELIVERY_CP, $CON_DELIVERY_TYPE, $DELIVERY_FEE, $DELIVERY_FEE_CODE, $s_adm_no);

					}
				}
			}
?>
<script language="javascript">
		alert('생성 되었습니다.');
</script>
<?
		}
	}

	if ($mode == "D") {
		//echo "mode : D<br>";
		if (isset($chk)) 
		{
			for($j = 0; $j < sizeof($chk); $j ++) { 
				$base_order_goods_delivery_no = $chk[$j]; 

				deleteOrderDeliveryPaper($conn, $base_order_goods_delivery_no, $s_adm_no);
			}
?>
<script language="javascript">
		alert('삭제 되었습니다.');
</script>
<?
		}
	}


	if ($mode == "UPDATE_OUTSIDE" || $mode == "UPDATE_OUTSIDE_ORDER_FINISH") {
		//echo "mode : UPDATE_OUTSIDE OR UPDATE_OUTSIDE_ORDER_FINISH<br>";

		$tab_index = 1;

		//삭제된 리스트
		if (isset($arr_deleted_no)) 
		{
			for($j = 0; $j < sizeof($arr_deleted_no); $j ++) { 
				
				$temp_deleted_no = $arr_deleted_no[$j];
				
				if($temp_deleted_no <> "") { 
					deleteOrderDeliveryPaperOutside($conn, $order_goods_no, $temp_deleted_no);
				}
			}
		}

		//개별 추가 리스트
		if (isset($arr_delivery_cp)) 
		{
			for($j = 0; $j < sizeof($arr_delivery_cp); $j ++) { 
				$t_delivery_cp = $arr_delivery_cp[$j]; 
				$t_delivery_no = $arr_delivery_no[$j]; 
				$t_memo		   = $arr_memo[$j]; 

				$t_delivery_no = str_replace("-","",$t_delivery_no);

				//2016-12-06 둘중 하나에서 택배회사 하나만 없어도 입력 안됨으로 수정
				if($t_delivery_cp == "" || $t_delivery_no == "") continue;

				insertOrderDeliveryPaperOutside($conn, $order_goods_no, $t_delivery_cp, $t_delivery_no, $t_memo);
			}
		}


		$arr_cp = listDcode($conn, "DELIVERY_CP", 'Y', 'N', "", "", 1, 10000);

		//붙여넣기 일괄 리스트
		if($delivery_paste <> "")
		{
			foreach(explode("\n",$delivery_paste) as $each_row) { 


				if(trim($each_row) == "") 
					continue;
				else { 
				
					$each_row = trim(preg_replace('/\s+/', ' ', $each_row));
					$each_row = trim(str_replace("  "," ",$each_row));
					$each_row = trim(str_replace('\n','',$each_row));

					$each_delivery_no = "";
					$each_memo = "";

					$arr_separated = explode(" ",$each_row);
					if(sizeof($arr_separated) > 0) {
						$each_delivery_no = $arr_separated[0];
						$each_memo = $arr_separated[1];

						$each_delivery_no = str_replace("-","",$each_delivery_no);
						
						//if($each_delivery_no <> "")
							insertOrderDeliveryPaperOutside($conn, $order_goods_no, $main_delivery_cp, $each_delivery_no, $each_memo);
					}
				}
			}
		}

		if($mode == "UPDATE_OUTSIDE_ORDER_FINISH") { 
			//echo "mode : UPDATE_OUTSIDE_ORDER_FINISH<br>";

			$temp_reserve_no					= $reserve_no;
			$temp_order_goods_no				= $order_goods_no;

			$arr_order_outside = listOrderDeliveryPaperOutside($conn, $temp_order_goods_no);
			for($o = 0; $o < sizeof($arr_order_outside); $o ++) { 
				$temp_delivery_cp = $arr_order_outside[$o]["DELIVERY_CP"];
				$temp_delivery_no = $arr_order_outside[$o]["DELIVERY_NO"];
			}
			
			$is_all_done = "Y";

			$refund_able_qty = getRefundAbleQty($conn, $temp_reserve_no, $temp_order_goods_no);

			$temp_delivery_no = str_replace("-","",$temp_delivery_no);

			$arr_order_goods = selectOrderGoodsForDeliveryList($conn, $temp_order_goods_no);
			if(sizeof($arr_order_goods) <= 0) continue;

			$temp_order_state		= SetStringFromDB($arr_order_goods[0]["ORDER_STATE"]);
			$temp_work_flag			= SetStringFromDB($arr_order_goods[0]["WORK_FLAG"]);

			//주문상태가 배송중이 맞는지 서버단에서 확인
			if($temp_order_state != "2") continue;
			
			$arr_rs_individual = listDeliveryIndividual($conn, $temp_order_goods_no, "DESC");
			$total_delivered_qty = 0;

			//개별 수량이 지정 되어 있으면 개별택배 룰로 완료, 그렇지 않다면 원래 외부업체 발송 룰로 완료 2017-04-11
			if(sizeof($arr_rs_individual) > 0) { 
				for($o = 0; $o < sizeof($arr_rs_individual); $o ++) { 

					$SUB_QTY		= SetStringFromDB($arr_rs_individual[$o]["SUB_QTY"]);
					$IS_DELIVERED	= SetStringFromDB($arr_rs_individual[$o]["IS_DELIVERED"]);
					$USE_TF			= SetStringFromDB($arr_rs_individual[$o]["USE_TF"]);
					
					if($USE_TF != "Y") 
						continue;

					if($IS_DELIVERED == "Y")
						$total_delivered_qty += $SUB_QTY;
				}
				if($total_delivered_qty >= $refund_able_qty) { 

					if($temp_work_flag == "N") { 
						updateWorksFlagYOrderGoods($conn, $temp_order_goods_no);
						$temp_work_flag = "Y";
					}
				} 

				//개별택배시 주문수량과 개별완료된 수량이 같지 않은경우에 -> 2017-01-05 취소, 교환처리되었을때 refund_able_qty 수량도 감소되므로 작을때로 수정 
				if($total_delivered_qty < $refund_able_qty)
					$is_all_done = "N";
			} else { 

					if($temp_work_flag == "N") { 
						updateWorksFlagYOrderGoods($conn, $temp_order_goods_no);
						$temp_work_flag = "Y";
					}

			}

			//echo $temp_delivery_no." ".$temp_work_flag." ".$is_all_done."<br/>";
			if ($temp_delivery_no <> "" && $temp_work_flag == "Y" && $is_all_done == "Y" && $refund_able_qty > 0) {

				//배송준비중 -> 배송완료처리
				$result = updateDeliveryState($conn, $temp_reserve_no, $temp_order_goods_no, $temp_delivery_cp, $temp_delivery_no, $s_adm_no);
			}	

		}
?>
<script language="javascript">
		alert('저장 되었습니다.');
</script>
<?
	}
	
	function updateMainDeliveryPaper($db, $order_goods_no, $delivery_cp, $delivery_no) {
		//s_adm_no
		$query =   "UPDATE
						TBL_ORDER_GOODS
					SET
						DELIVERY_CP = '$delivery_cp',
						DELIVERY_NO = '$delivery_no'
					WHERE
						ORDER_GOODS_NO = '$order_goods_no'
		";

		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
		} else {
			return true;
		}
	}

	if ($mode == "PICK_MAIN_DELIVERY_PAPER") {
		//echo "mode : PIXK_MAIN_DELIVERY_PAPER<br>";
		if(updateMainDeliveryPaper($conn, $order_goods_no, $main_delivery_cp2, $main_delivery_no))
			$return_msg ="변경 되었습니다.";
		else
			$return_msg ="실패했습니다.";
?>
<script language="javascript">
		alert('<?=$return_msg?>');
</script>
<?
	}

#===============================================================
# Get Search list count
#===============================================================

	Skip :

	$arr_order_goods = selectOrderGoods($conn, $order_goods_no);
	for($i=0; $i < sizeof($arr_order_goods); $i++) {

		$ORDER_STATE	 = SetStringFromDB($arr_order_goods[0]["ORDER_STATE"]);
		$DELIVERY_TYPE   = SetStringFromDB($arr_order_goods[0]["DELIVERY_TYPE"]);
		$DELIVERY_CP     = SetStringFromDB($arr_order_goods[0]["DELIVERY_CP"]);
		$SENDER_NM       = SetStringFromDB($arr_order_goods[0]["SENDER_NM"]);
		$SENDER_PHONE    = SetStringFromDB($arr_order_goods[0]["SENDER_PHONE"]);
		$WORK_START_DATE = SetStringFromDB($arr_order_goods[0]["WORK_START_DATE"]);
		$WORK_END_DATE	 = SetStringFromDB($arr_order_goods[0]["WORK_END_DATE"]);
	}
	// echo "WORK_START_DATE at 471 : ".$WORK_START_DATE."<br>";
	// echo "work_end_date at 472 : ".$WORK_END_DATE."<br>";

	$arr_order_rs = listOrderDeliveryPaper($conn, $order_goods_no, $individual_no);
	
	$arr_order_outside = listOrderDeliveryPaperOutside($conn, $order_goods_no);

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
			changeMonth: true,
      changeYear: true
    });

	});
</script>
<script>
	$(function() {
		$("#tabs").tabs({
		  active: <?=$tab_index?>
		});
	});
</script>
<script language="javascript">
	function js_open_manual(){
		NewWindow('pop_delivery_paper_list_manual.php','pop_delivery_paper_list_manual','800','500','Yes');
	}
	
	function js_create_delivery_paper()
	{
		var frm = document.frm;

		if($("select[name=DELIVERY_TYPE]").val() == "" || $("select[name=DELIVERY_CP]").val() == "")
		{
			alert('일괄 입력할 배송방식과 택배회사를 선택해주세요.');
			return;
		}

		frm.mode.value = "I";
		frm.target = "";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}


	function js_append_delivery_paper()
	{
		var frm = document.frm;

		var cntChecked = 0;

		if(frm.individual_no.value == '')
		{
			frm.individualno_chkval.value = 'N';
			
			//선택된 송장의 individual_no 갖고옴	20210806 주석
			/*for (i = 0; i < frm['chk[]'].length; i++) {
				if(frm['chk[]'][i].checked == true){
					cntChecked ++;
					frm.individual_no.value = frm['INDIVIDUALNO[]'][i].value;
				}
			}*/

			frm_chk   = document.getElementsByName("chk[]");
			ctl       = document.getElementsByName("INDIVIDUALNO[]");
			
			loop_cnt      = frm_chk.length;			

			if(loop_cnt > 0)
			{
				for(i=0; i<loop_cnt; i++)
				{
					if(frm_chk[i].checked == true)
					{
						frm.individual_no.value = ctl[i].value;
						cntChecked++;
					}
				}
			}
		}
		else
		{
			frm.individualno_chkval.value = 'Y';

			$("input[name='chk[]']:checked").each(function ()
			{
				cntChecked ++;
			});
		}		
		
		if(cntChecked != 1) {
			alert("기준이 되는 복사할 송장을 1개만 선택하여 주세요.");
			return;

		} else {

			frm.mode.value = "APPEND";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_update_delivery_paper()
	{
		var frm = document.frm;

		var cntChecked = 0;
		$("input[name='chk[]']:checked").each(function ()
		{
			cntChecked ++;
		});
		
		if(cntChecked < 1) {
			alert("수정할 송장이 선택되지 않았습니다.");
			return;

		} else {

			frm.mode.value = "U";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();

		}
	}

	function js_delete_delivery_paper()
	{
		var frm = document.frm;

		var cntChecked = 0;
		$("input[name='chk[]']:checked").each(function ()
		{
			cntChecked ++;
		});
		
		if(cntChecked < 1) {
			alert("삭제할 송장을 최소 1개이상 선택하여 주세요.");
			return;

		} else {

			bDelOK = confirm('송장을 삭제 하시겠습니까? 삭제하면 복구할 수 없습니다.');
	
			if (bDelOK==true) {
				frm.mode.value = "D";
				frm.target = "";
				frm.action = "<?=$_SERVER[PHP_SELF]?>";
				frm.submit();
			}
		}
	}

	function js_excel() {

		var frm = document.frm;
		var order_goods_no = frm.order_goods_no.value;
		var print_type = frm.print_type.value;

		NewDownloadWindow("delivery_paper_excel_list.php", {search_field : "ORDER_GOODS_NO", search_str: order_goods_no, print_type: print_type});

	}

	function js_out_excel() {

		var frm = document.frm;
		var order_goods_no = frm.order_goods_no.value;

		NewDownloadWindow("delivery_paper_outside_excel_list.php", {order_goods_no: order_goods_no});

	}

	function js_update_delivery_paper_from_outside() { 

		var frm = document.frm;
		
		frm.mode.value = "UPDATE_OUTSIDE";
		frm.target = "";
		frm.method = "post";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_finish() { 
		var frm = document.frm;
		
		frm.mode.value = "UPDATE_OUTSIDE_ORDER_FINISH";
		frm.target = "";
		frm.method = "post";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	function js_view_delivery_paper(order_goods_delivery_no) { 
		var url = "pop_delivery_paper_detail.php?order_goods_delivery_no=" + order_goods_delivery_no;

		NewWindow(url, 'pop_delivery_paper_detail','1000','500','YES');
	}

	function js_all_check() {
		var frm = document.frm;
		
		if (frm['chk[]'] != null) {
			
			if (frm['chk[]'].length != null) {

				if (frm.all_chk.checked == true) {
					for (i = 0; i < frm['chk[]'].length; i++) {
						frm['chk[]'][i].checked = true;
					}
				} else {
					for (i = 0; i < frm['chk[]'].length; i++) {
						frm['chk[]'][i].checked = false;
					}
				}
			} else {
			
				if (frm.all_chk.checked == true) {
					frm['chk[]'].checked = true;
				} else {
					frm['chk[]'].checked = false;
				}
			}
		}
	}


	function js_pop_delivery_paper_frame(delivery_cp, delivery_no) { 

		var url = "/manager/order/pop_delivery_paper_wrapper.php?delivery_cp=" + delivery_cp + "&delivery_no=" + delivery_no;
		NewWindow(url, 'pop_delivery_paper_wrapper', '920', '700', 'YES');

	}

function js_pick_main_delivery_paper(){
		var frm = document.frm;
		var cntChecked = 0;
		
		//선택된 송장의 번호와 회사명을 가져옴
		for (i = 0; i < frm['chk[]'].length; i++) {
			if(frm['chk[]'][i].checked == true){
				cntChecked ++;
				frm.main_delivery_no.value = frm['delivery_no[]'][i].value;
				frm.main_delivery_cp2.value = frm['delivery_cp_nm[]'][i].value;
			}
		}
		
		//대표 송장을 하나만 선택한 경우만 실행
		if(cntChecked > 1) {
			alert("대표 송장은 한 장만 선택할 수 있습니다.");
			return;
		} else if(cntChecked == 0){
			alert("대표 송장을 선택해주세요.");
			return;
		} else {
			frm.mode.value = "PICK_MAIN_DELIVERY_PAPER";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	$(function(){

		$(".change_fee").click(function(e){
			e.preventDefault();

			var standard_fee = $("select[name=sel_delivery_fee]  option:selected").text();

			$("input[name='chk[]']").each(function(index,elem){

				if($(this).is(":checked")) { 
					
					$("select[name='delivery_fee_code[]']").eq(index).val(standard_fee);
					//$(this).prop("checked",false);
				}
			});

			frm.mode.value = "U";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();

			//alert('변경되었습니다. 선택해서 수정하셔야 저장됩니다.');
		});

		$(".top_group input[type=text],.top_group select").focus(function(){

			$(this).closest("tr").find("input[type=checkbox]").prop("checked", true);

		});

	});

</script>
<style>
	.top_group td {border-top: 2px solid black;  }
	.bottom_group td {border-top: 1px dotted black; }
	table.rowstable td {background: none;}
	table.rowstable {border-bottom: 2px solid black;} 
</style>
</head>

<body id="popup_order_wide">

<div id="popupwrap_order_wide">
	<h1>송장 리스트 조회<input type="button" value="참고사항" onclick="js_open_manual()"> </h1>
	<div id="postsch_code">

		<div class="addr_inp">

<form name="frm" method="post">
<input type="hidden" name="mode" value="">
<input type="hidden" name="keyword" value="">
<input type="hidden" name="reserve_no" value="<?=$reserve_no?>">
<input type="hidden" name="order_goods_no" value="<?=$order_goods_no?>">
<input type="hidden" name="individual_no" value="<?=$individual_no?>">
<input type="hidden" name="main_delivery_no" value="">
<input type="hidden" name="main_delivery_cp2" value="">
<input type="hidden" name="individualno_chkval">


	<div id="tabs" style="width:95%; margin:10px 0;">
		<ul>
			<li><a href="#tabs-1">송장 생성</a></li>
			<li><a href="#tabs-2">거래처(외부)송장 입력</a></li>
		</ul>
		<div id="tabs-1">

			<? if ($s_adm_cp_type == "운영") { ?>
			<h2>* 일괄 선택</h2>
			
			<table cellpadding="0" cellspacing="0" style="width:100%;" class="colstable02">
			<colgroup>
				<col width="10%" />
				<col width="30%" />
				<col width="10%" />
				<col width="30%" />
				<col width="*" />
			</colgroup>
			<tbody>
				<tr>
					<th>송장기준일</th>
					<td class="line" colspan="3">
						<?
							// 작업 시작일이 있으면 시작일 기준으로
							if($WORK_START_DATE != "0000-00-00 00:00:00" && $WORK_START_DATE != ""){
								// echo "<script>alert('시작일 기준');</script>";
								// echo "work_end_date : $WORK_END_DATE<br>";
								// echo"시작일기준<br>";
								$chk_work_date = date("Y-m-d",strtotime($WORK_START_DATE)); 
							}

							//작업 완료일이 있으면 시작일을 덮고 완료일 기준으로
							if($WORK_END_DATE != "0000-00-00 00:00:00" && $WORK_END_DATE != ""){
								// echo"완료일기준<br>";
								$chk_work_date = date("Y-m-d",strtotime($WORK_END_DATE));
							}
								
							
							//개별택배이라면 내일자로
							if ($chk_work_date == "0000-00-00 00:00:00" || $chk_work_date == "" || $DELIVERY_TYPE == "3"){
								if($chk_work_date=="0000-00-00 00:00:00" || $chk_work_date==""){
									echo "chk_work_date is null<br>";
								}
								if($DELIVERY_TYPE=="3"){
									// echo "개별택배<br>";
								}
								$chk_work_date = date("Y-m-d",strtotime("1 day"));
							}
								
						?>

						<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="chk_work_date" value="<?=$chk_work_date?>" maxlength="10"/>
					</td>
					<td class="line" rowspan="3">
						<select name="print_type">
							<option value="out">외부용</option>
							<option value="">내부용</option>
						</select>
						<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>
					</td>
				</tr>
				<tr>
					<th>배송종류</th>
					<td class="line"><?=makeSelectBoxOnChange($conn,"DELIVERY_TYPE", "DELIVERY_TYPE","90", "배송종류 선택", "", $DELIVERY_TYPE)?></td>
					<th>택배회사</th>
					<td class="line"><?=makeSelectBoxOnChange($conn,"DELIVERY_CP", "DELIVERY_CP","90", "택배회사선택", "", $DELIVERY_CP)?>
				</tr>
				<tr>
					<th>보내는사람</th>
					<td class="line"><input type="text" name="SENDER_NM" value="<?=$SENDER_NM?>" /></td>
					<th>보내는 번호</th>
					<td class="line"><input type="text" name="SENDER_PHONE" value="<?=$SENDER_PHONE?>" />
				</tr>
			</tbody>
			</table> 
			
			<div class="sp10"></div>
			<div>
				<div style="float:left; display:inline-block;">
					<input type="button" name="" value="대표 송장 지정" onclick="js_pick_main_delivery_paper();" style="margin-bottom:3px;">
				</div>
				<div style="text-align:right;">
					<input type="button" name="b" value="생성" class="btntxt" onclick="js_create_delivery_paper();" style="margin-bottom:3px;">
					<?
						if(($s_adm_no==87 || $s_adm_no==85 )&& $DELIVERY_TYPE==3){
						?>
							<input type="button" name="b" value="추가(선택)" class="btntxt" disabled style="margin-bottom:3px;">
						<?
						}
						else{
						?>
							<input type="button" name="b" value="추가(선택)" class="btntxt" onclick="js_append_delivery_paper();" style="margin-bottom:3px;">
						<?
						}
					?>
					<input type="button" name="b" value="삭제(선택)" class="btntxt" onclick="js_delete_delivery_paper();">
				</div>
			</div>
			<? } ?>
			<div class="sp10"></div>

			<div>
				<div style="float:right; display:inline-block;">
					선택한 송장의
					<?=makeSelectBoxAsName($conn,"DELIVERY_FEE", "sel_delivery_fee","80px", "운임선택", "", "", "")?>
					<input type="button" name="bb" value="으로 변경" class="change_fee"/>
				</div>
				<span style="font-weight:bold;">
				<?	$arr_rs_delivery = cntOrderGoodsDelivery($conn, $reserve_no, $order_goods_no, $individual_no);
											
					for($k = 0; $k < sizeof($arr_rs_delivery); $k ++)
					{
						$DELIVERY_CP				= trim($arr_rs_delivery[$k]["DELIVERY_CP"]);
						$TOTAL						= trim($arr_rs_delivery[$k]["TOTAL"]);
						$CNT_YES					= trim($arr_rs_delivery[$k]["CNT_YES"]);
						$CNT_NO						= trim($arr_rs_delivery[$k]["CNT_NO"]);
						
						echo $DELIVERY_CP." : 전체 ".$TOTAL." 장, 사용 ".$CNT_YES."장, 미사용 ".$CNT_NO."장 <br/>";
					}
				?>
				</span>
			</div>
			<table cellpadding="0" cellspacing="0" style="width:100%;" class="rowstable">
			<colgroup>
				<col width="2%" />
				<col width="12%" />
				<col width="14%" />
				<col width="8%" />
				<col width="10%" />
				<col width="10%" />
				<col width="10%" />
				<col width="10%" />
				<col width="*" />
				<col width="8%" />
			</colgroup>
			<thead>
				<tr>
					<th rowspan="2"><input type="checkbox" name="all_chk"  onClick="js_all_check();" /></th>
					<th>출고번호</th>
					<th>송장번호</th>
					<th>택배사</th>
					<th colspan="2">상품명</th>
					
					<th>배송일</th>
					<th>운임</th>
					<th>메모</th>
					<th class="end" rowspan="2">사용여부</th>
				</tr>
				<tr>
					<th>주문자</th>
					<th>주문자전화</th>
					<th>담당자</th>
					<th>담당자전화</th>
					<th>수령자</th>
					<th>수령자전화</th>
					<th>수령자핸드폰</th>
					<th class="end">수령자주소</th>
				</tr>
			</thead>
			<tbody>
			
			<?
			if(sizeof($arr_order_rs) >= 1) {
				for($i = 0; $i < sizeof($arr_order_rs); $i ++) { 

				$rs_order_goods_delivery_no	= SetStringFromDB($arr_order_rs[$i]["ORDER_GOODS_DELIVERY_NO"]);
				$rs_delivery_seq	        = SetStringFromDB($arr_order_rs[$i]["DELIVERY_SEQ"]); 
				$rs_delivery_no 		    = SetStringFromDB($arr_order_rs[$i]["DELIVERY_NO"]);
				$rs_delivery_cp				= SetStringFromDB($arr_order_rs[$i]["DELIVERY_CP"]);
				$rs_order_nm		        = SetStringFromDB($arr_order_rs[$i]["ORDER_NM"]); 
				$rs_order_phone		        = SetStringFromDB($arr_order_rs[$i]["ORDER_PHONE"]);
				$rs_order_manager_nm	    = SetStringFromDB($arr_order_rs[$i]["ORDER_MANAGER_NM"]);
				$rs_order_manager_phone		= SetStringFromDB($arr_order_rs[$i]["ORDER_MANAGER_PHONE"]);
				$rs_receiver_nm		        = SetStringFromDB($arr_order_rs[$i]["RECEIVER_NM"]); 
				$rs_receiver_phone		    = SetStringFromDB($arr_order_rs[$i]["RECEIVER_PHONE"]);
				$rs_receiver_hphone		    = SetStringFromDB($arr_order_rs[$i]["RECEIVER_HPHONE"]);
				$rs_receiver_addr			= SetStringFromDB($arr_order_rs[$i]["RECEIVER_ADDR"]); 
				$rs_goods_delivery_name	    = SetStringFromDB($arr_order_rs[$i]["GOODS_DELIVERY_NAME"]); 
				$rs_memo				    = SetStringFromDB($arr_order_rs[$i]["MEMO"]); 
				$rs_delivery_fee			= SetStringFromDB($arr_order_rs[$i]["DELIVERY_FEE"]); 
				$rs_delivery_fee_code		= SetStringFromDB($arr_order_rs[$i]["DELIVERY_FEE_CODE"]); 
				$rs_delivery_claim_code		= SetStringFromDB($arr_order_rs[$i]["DELIVERY_CLAIM_CODE"]); 
				$rs_delivery_date           = SetStringFromDB($arr_order_rs[$i]["DELIVERY_DATE"]); 
				$rs_use_tf					= SetStringFromDB($arr_order_rs[$i]["USE_TF"]); 

				$INDIVIDUALNO				= SetStringFromDB($arr_order_rs[$i]["INDIVIDUAL_NO"]); 

				if($rs_delivery_date == "0000-00-00 00:00:00")
					$rs_delivery_date = "발송전";

				if($rs_use_tf == "N")
					$class_gray= "unused";
				else
					$class_gray= "";


			?>

			<tr height="35" class="<?=$class_gray?> top_group">
				<td rowspan="2">
					<input type="checkbox" name="chk[]" value="<?=$rs_order_goods_delivery_no?>">
					<input type="hidden" name="order_goods_delivery_no[]" value="<?=$rs_order_goods_delivery_no?>"/>
					<input type="hidden" name="INDIVIDUALNO[]" value="<?=$INDIVIDUALNO?>"/>

				</td>
				<td><?=$rs_delivery_seq?></td>
				<td>
					<input type="text" name="delivery_no[]" value="<?=$rs_delivery_no?>" class="txt" style="width:95%" <? if ($rs_delivery_no) {?>onClick="js_pop_delivery_paper_frame('<?=$rs_delivery_cp?>', '<?=$rs_delivery_no?>');" <?}?> title="<?=$rs_delivery_no?>" >
				</td>
				<td><input type="hidden" name="delivery_cp_nm[]" value="<?=getDcodeName($conn,"DELIVERY_CP", $rs_delivery_cp)?>"><?=getDcodeName($conn,"DELIVERY_CP", $rs_delivery_cp)?></td>
				<td colspan="2"><?=htmlspecialchars($rs_goods_delivery_name)?></td>
				
				<td><?=$rs_delivery_date?></td>
				<td>
					<?=makeSelectBox($conn,"DELIVERY_FEE", "delivery_fee_code[]","80px", "운임선택", "", $rs_delivery_fee_code, "")?>
				</td>
				<td><?=$rs_memo?></td>
				<td rowspan="2">
					<input type="button" name="b" value="조회" class="btntxt" onclick="js_view_delivery_paper('<?=$rs_order_goods_delivery_no?>');" style="margin-bottom:3px;">
					<?=makeSelectBox($conn, "USE_TF", "use_tf[]", "70", "", "", $rs_use_tf)?>
				</td>
			</tr>
			<tr height="35" class="<?=$class_gray?> bottom_group">
				<td><?=$rs_order_nm?></td>
				<td><?=$rs_order_phone?></td>
				<td><?=$rs_order_manager_nm?></td>
				<td><?=$rs_order_manager_phone?></td>
				<td><?=$rs_receiver_nm?></td>
				<td><?=$rs_receiver_phone?></td>
				<td><?=$rs_receiver_hphone?></td>
				<td><?=$rs_receiver_addr?></td>
				
			</tr>
			
			<?
				}
			} else {

			?>
			<tr>
				<td colspan="10" height="50" align="center">데이터가 없습니다</td>
			</tr>
			<?

			}
			
			?>
			
			</tbody>
			</table>
			

			<? if ($s_adm_cp_type == "운영") { ?>
			<div class="btn">
			  <a href="javascript:js_update_delivery_paper();"><img src="../images/admin/btn_modify.gif" alt="확인" /></a>
			  <a href="javascript:window.close();"><img src="../images/admin/btn_cancel.gif" alt="취소" /></a>
			</div>      
			<? } ?>

		</div>
		<div id="tabs-2">

			<table cellpadding="0" cellspacing="0" border="0" class="colstable01" style="width:98%">
				<colgroup>
					<col width="15%">
					<col width="35%">
					<col width="*">
					<col width="5%">
				</colgroup>

				<tr>
					<th>개별 추가</th>
					<td colspan="2" class="add_here">
						<? 
							for($o = 0; $o < sizeof($arr_order_outside); $o ++) { 
								$rs_delivery_cp = $arr_order_outside[$o]["DELIVERY_CP"];
								$rs_delivery_no = $arr_order_outside[$o]["DELIVERY_NO"];
								$rs_memo		= $arr_order_outside[$o]["MEMO"];
								
						?>
							<div class="delivery">
								<?=makeSelectBoxOnChange($conn,"DELIVERY_CP", "arr_delivery_cp[]","100", "선택하세요", "", $rs_delivery_cp)?>
								<input type="text" name="arr_delivery_no[]" <? if ($rs_delivery_no) {?>onClick="js_pop_delivery_paper_frame('<?=$rs_delivery_cp?>', '<?=$rs_delivery_no?>');" <?}?> value="<?=$rs_delivery_no?>" title="<?=$rs_delivery_no?>" /> &nbsp;
								<input type="text" name="arr_memo[]" value="<?=$rs_memo?>" placeholder="메모입력" />
								<input type="button" name="b" onclick="js_append_payment(this);" value="추가" />
								<input type="button" name="b" onclick="js_delete_payment(this);" value="삭제" />
							</div>
						<? } ?>
						<div class="delivery">
							<?=makeSelectBoxOnChange($conn,"DELIVERY_CP", "arr_delivery_cp[]","100", "선택하세요", "", "")?>
							<input type="text" name="arr_delivery_no[]" value="" /> &nbsp;
							<input type="text" name="arr_memo[]" value="" placeholder="메모입력" />
							<input type="button" name="b" onclick="js_append_payment(this);" value="추가" />
							<input type="button" name="b" onclick="js_delete_payment(this);" value="삭제" />
						</div>
					</td>
					<td align="right">
						<a href="javascript:js_out_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>
					</td>
				</tr>
				<script>
					function js_append_payment(elem) { 
						var copied = $(elem).closest(".delivery").clone();
						//copied.find("input[type=select]").val('');
						copied.find("input[type=text]").val('');
						copied.find("input[type=text]").prop("onclick", "");
						$(".add_here").append(copied);
					}

					function js_delete_payment(elem) {
						var delivery_no = $(elem).closest(".delivery").find("input[name='arr_delivery_no[]']").val();
						$(elem).closest(".delivery").remove();
						$(".add_here").append("<input type='hidden' name='arr_deleted_no[]' value='"+delivery_no+"' />");
					}
				</script>
				<tr>
					<th>일괄입력 붙여넣기 (택배회사 송장번호)</th>
					<td colspan="3">
						<?=makeSelectBoxExtraClass($conn,"DELIVERY_CP", "main_delivery_cp","100", "선택하세요", "", "", "vtop")?>
						<textarea name="delivery_paste" style="width:345px; height:60px" placeholder="<필수:택배번호> <선택:지점명 혹은 기타메모>"></textarea>
					</td>
				<tr>
				<? if ($DELIVERY_TYPE == "98" && $ORDER_STATE == "2" ) { ?>
				<tr>
					<th>외부업체 발송</th>
					<td colspan="3" style="text-align:right;"><input type="button" name="aa" onclick="js_finish();" value="송장입력 & 주문 배송완료"/></td>
				</tr>
				<? } ?>
			</table>
		
			<? if ($s_adm_cp_type == "운영") { ?>
			<div class="btn">
				<a href="javascript:js_update_delivery_paper_from_outside();"><img src="../images/admin/btn_modify.gif" alt="확인" /></a>
				
			</div>      
			<? } ?>


		</div>
	</div>
	
	


<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
<div class="bot_close" style="width:auto;"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
</div>
</body>
<script>
	$("SELECT[name='DELIVERY_TYPE']").prop("disabled",true);
	$(document).ready(function(){
		
	});
</script>

</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>