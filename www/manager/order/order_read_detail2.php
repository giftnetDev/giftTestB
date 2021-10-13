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
	$menu_right = "SP009"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/stock/stock.php";
	require "../../_classes/biz/goods/goods.php";
	
	if ($this_date == "") 
		$this_date = date("Y-m-d",strtotime("0 month"));

	if ($this_h == "") 
		$this_h = date("H",strtotime("0 month"));

	if ($this_i == "") 
		$this_i = date("i",strtotime("0 month"));

	if ($this_s == "") 
		$this_s = date("s",strtotime("0 month"));


	$temp_date = $this_date." ".$this_h.":".$this_i.":".$this_s;

	if ($mode == "I") {

		$use_tf = "Y";
		$cart_seq = getOrderGoodsMaxSeq($conn, $reserve_no);
		$cart_seq++;

			$arr_order_rs = selectOrder($conn, $reserve_no);
			$rs_cp_no		= trim($arr_order_rs[0]["CP_NO"]);
			$rs_o_mem_nm	= trim($arr_order_rs[0]["O_MEM_NM"]); 
			$rs_r_mem_nm	= trim($arr_order_rs[0]["R_MEM_NM"]); 

		// 취소 상품 등록

		//if ($claim_state == "3") {
		//	$result = "";
		//} else {
		if ($refund_able_qty == $cancel_qty) {
			$delivery_price = -$delivery_price;
		} else {
			$delivery_price = 0;
		}
		
		if ($claim_state <> "99") {
			$result = insertOrderGoods($conn, $on_uid, $reserve_no, $buy_cp_no, $mem_no, $cart_seq, $goods_no, $goods_code, $goods_name, $goods_sub_name, $cancel_qty, $goods_option_01, $goods_option_02, $goods_option_03,$goods_option_04, $goods_option_nm_01, $goods_option_nm_02,$goods_option_nm_03, $goods_option_nm_04, $cate_01, $cate_02, $cate_03, $null_cate_04, $buy_price, $sale_price, $extra_price, $delivery_price, $sa_delivery_price, $tax_tf, $claim_state, $use_tf, $s_adm_no);
		}
		
		if (($claim_state == "4") || ($claim_state == "6") || ($claim_state == "7") || ($claim_state == "8")) {

			$claim_order_goods_no =  getLastInsertedID($conn);
			updateOrderGoodsGroupNo($conn, $order_goods_no, $claim_order_goods_no);
		}

		if ($claim_state == "8") {
			$cart_seq++;

			$cancel_qty = $cancel_qty;
			$delivery_price = "0";
			$order_state	= "1"; 
			// 교환인 경우 
			$cate_04 = "CHANGE";
			$result = insertOrderGoods($conn, $on_uid, $reserve_no, $buy_cp_no, $mem_no, $cart_seq, $goods_no, $goods_code, $goods_name, $goods_sub_name, $cancel_qty, $goods_option_01, $goods_option_02, $goods_option_03,$goods_option_04, $goods_option_nm_01, $goods_option_nm_02,$goods_option_nm_03, $goods_option_nm_04, $cate_01, $cate_02, $cate_03, $cate_04, $buy_price, $sale_price, $extra_price, $delivery_price, $sa_delivery_price, $tax_tf, $order_state, $use_tf, $s_adm_no);

		}

		//}

		// 환불인경우 환불 정보 입력
		if(sizeof($arr_refund_method) > 0) { 
			$refund_state = "0";
			$refund_type = "환불함";
			
			for($o = 0; $o < sizeof($arr_refund_method); $o ++) { 
				$cms_depositor = $arr_refund_method[$o];
				$bank_amount = $arr_bank_amount[$o];

				if($bank_amount == "") continue;

				$result = insertRefund($conn, $refund_type, $refund_state, $cart_seq, $on_uid, $reserve_no, $rs_o_mem_nm, $buy_cp_no, $cms_depositor, $bank_amount, $bank_name, $bank_pay_account, $use_tf, $s_adm_no);
			}

		}


		/*
		if ($refund_type == "환불함") {

			$refund_state = "0";
			
			$cms_depositor = $refund_method;
			$result = insertRefund($conn, $refund_type, $refund_state, $cart_seq, $on_uid, $reserve_no, $rs_o_mem_nm, $buy_cp_no, $cms_depositor, $bank_amount, $bank_name, $bank_pay_account, $use_tf, $s_adm_no);
		}
		*/
		

		if ($claim_state <> "99") {
			$result = resetOrderInfor($conn, $reserve_no);
		}

		// 입금전 취소일 경우 입금액을 수정 한다..
		if ($claim_state == "4") {
			$result = resetPaymentInfor($conn, $reserve_no);
		}

		// 클레임 등록
		$bb_code		= "CLAIM";
		$writer_nm	= $s_adm_nm;
		$writer_pw	= $s_adm_no;
		$cate_01		= $reserve_no;
		$cate_02		= $claim_type;
		$cate_03		= $cart_seq;
		$cate_04		= $claim_state;
		$title			= $goods_name." ".$goods_option_nm_01;
		$contents		= $claim_memo;
		$email			= $rs_o_mem_nm;
		$homepage		= $rs_r_mem_nm;
		$keyword		= $buy_cp_no;
		$file_size	= $cancel_qty;

		$ref_ip = $receiver_name; //상담원이름

		$contents = $contents." ".$s_adm_nm." (".$temp_date.")";

		$new_bb_no = insertBoard($conn, $bb_code, $cate_01, $cate_02, $cate_03, $cate_04, $writer_nm, $writer_pw, $email, $homepage, $title, $ref_ip, $recomm, $contents, $file_nm, $file_rnm, $file_path, $file_size, $file_ext, $keyword, $comment_tf, $use_tf, $s_adm_no);
		
		//updateClaimExtra($conn, $claim_detail, $s_adm_no, $bb_code, $new_bb_no);

		//기존 주문에 잘못 처리된 상품 수 기록
		//updateClaimOutStockGoods($conn, $new_bb_no, $sub_goods_id, $sub_goods_cnt, $s_adm_no);

		// 재출고, 오배송건으로 인한 출고
		for ($j = 0; $j < sizeof($sub_goods_id); $j++) {

			$goods_sub_no = $sub_goods_id[$j];
			$goods_cnt    = $sub_goods_cnt[$j];
			$in_out		  = $sub_in_out[$j];

			if($chk_wrong == "Y") {
				if ($in_out == "OUT") {

					//오배송시 잘못나간 물건 가입고 처리까지
					//잘못나간 물건 받아와야할  (F+)
					$stock_type     = "IN";          //입출고 구분 (입고) 
					$stock_code     = "FST02";       //가입고
					$in_cp_no	    = $rs_cp_no;     // 출고업체
					$out_cp_no	    = 0;
					$goods_no		= $goods_sub_no; //출고상품 ** 세트인 경우 해당 세트에 상품 수 만큼 각 각 처리해야 함
					$in_loc			= "LOCB";        // 출고사유 - 클레임
					$in_loc_ext	    = getDcodeName($conn, "CLAIM_TYPE", $claim_type);
					$in_qty			= 0;
					$in_bqty		= 0;
					$in_fqty		= $goods_cnt; //구성품 수량 * 주문수
					$out_qty		= 0;
					$out_bqty		= 0;
					$out_fqty	    = 0;
					$in_price		= 0;
					$out_price	    = 0;     
					$in_date		= $this_date;
					$out_date		= "";
					$pay_date		= "";
					$close_tf		= "N";
					
					insertStock($conn, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_fqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $close_tf, $s_adm_no, $memo);

					//잘못나간 물건 (-)
					$stock_type     = "OUT";         //입출고 구분 (출고) 
					$stock_code     = "NOUT02";      //기타코드
					$in_cp_no	    = 0;
					$out_cp_no	    = $rs_cp_no;        // 출고업체
					$goods_no		= $goods_sub_no; //출고상품 ** 세트인 경우 해당 세트에 상품 수 만큼 각 각 처리해야 함
					$in_loc			= "LOCB";        // 출고사유 - 클레임
					$in_loc_ext	    = getDcodeName($conn, "CLAIM_TYPE", $claim_type);
					$in_qty			= 0;
					$in_bqty		= 0;
					$in_fqty		= 0;
					$out_qty		= $goods_cnt; //구성품 수량 * 주문수
					$out_bqty		= 0;
					$out_fqty	    = 0;
					$in_price		= 0;
					$out_price	    = 0;     
					$in_date		= "";
					$out_date		= $this_date;
					$pay_date		= "";
					$close_tf		= "N";

				} else {

					$stock_type     = "IN";         //입출고 구분 (입고) 
					$stock_code     = "NST99";      //기타코드
					$in_cp_no	    = $rs_cp_no;        // 출고업체
					$goods_no		= $goods_sub_no; //출고상품 ** 세트인 경우 해당 세트에 상품 수 만큼 각 각 처리해야 함
					$in_loc			= "LOCB";        // 입고사유 - 클레임
					$in_loc_ext	    = getDcodeName($conn, "CLAIM_TYPE", $claim_type);
					$in_qty			= $goods_cnt; //구성품 수량 * 주문수
					$in_bqty		= 0;
					$in_fqty		= 0;
					$out_qty		= 0;
					$out_bqty		= 0;
					$out_fqty	    = 0;
					$in_price		= 0;
					$out_price	    = 0;     
					$in_date		= $this_date;
					$out_date		= "";
					$pay_date		= "";
					$close_tf		= "N";
				}

			
				$new_stock_no = insertStock($conn, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_fqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $close_tf, $s_adm_no, $memo);
			} 

			if($chk_missing == "Y") {

				$stock_type     = "IN";         //입출고 구분 (입고) 
				$stock_code     = "NST99";      //기타코드
				$in_cp_no	    = $rs_cp_no;        // 출고업체
				$goods_no		= $goods_sub_no; //출고상품 ** 세트인 경우 해당 세트에 상품 수 만큼 각 각 처리해야 함
				$in_loc			= "LOCB";        // 입고사유 - 클레임
				$in_loc_ext	    = getDcodeName($conn, "CLAIM_TYPE", $claim_type);
				$in_qty			= $goods_cnt; //구성품 수량 * 주문수
				$in_bqty		= 0;
				$in_fqty		= 0;
				$out_qty		= 0;
				$out_bqty		= 0;
				$out_fqty	    = 0;
				$in_price		= 0;
				$out_price	    = 0;     
				$in_date		= $this_date;
				$out_date		= "";
				$pay_date		= "";
				$close_tf		= "N";

				$new_stock_no = insertStock($conn, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_fqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $close_tf, $s_adm_no, $memo);

			}


			if($chk_return == "Y") {

				$stock_type     = "IN";          //입출고 구분 (입고) 
				$stock_code     = "FST02";       //가입고
				$in_cp_no	    = $rs_cp_no;     // 출고업체
				$out_cp_no	    = 0;
				$goods_no		= $goods_sub_no; //출고상품 ** 세트인 경우 해당 세트에 상품 수 만큼 각 각 처리해야 함
				$in_loc			= "LOCB";        // 출고사유 - 클레임
				$in_loc_ext	    = getDcodeName($conn, "CLAIM_TYPE", $claim_type);
				$in_qty			= 0;
				$in_bqty		= 0;
				$in_fqty		= $goods_cnt; //구성품 수량 * 주문수
				$out_qty		= 0;
				$out_bqty		= 0;
				$out_fqty	    = 0;
				$in_price		= 0;
				$out_price	    = 0;     
				$in_date		= $this_date;
				$out_date		= "";
				$pay_date		= "";
				$close_tf		= "N";

				$new_stock_no = insertStock($conn, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_fqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $close_tf, $s_adm_no, $memo);

			}

			updateStockClaimNo($conn, $new_stock_no, $new_bb_no);
		
		}

		if ($new_bb_no) {
?>
<script type="text/javascript">
	window.opener.js_reload();
	alert("접수 되었습니다.");
	self.close();
</script>
<?
			mysql_close($conn);
			exit;
		}

	}

#====================================================================
# Request Parameter
#====================================================================

	$order_goods_no		= trim($order_goods_no);

	$del_tf = "N";
	
#===============================================================
# Get Search list count
#===============================================================

	$arr_rs = selectOrderGoods($conn, $order_goods_no);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script language="javascript">

var click_flag = "N";

function js_send() {
	var frm = document.frm;
	
	if (frm.rphone.value == "") {
		alert("고객번호를 입력하세요");
		frm.rphone.focus();
		return;
	}

	if (frm.msg.value == "") {
		alert("발송내용을 입력하세요");
		frm.msg.focus();
		return;
	}



	frm.target = "_blank";
	frm.action = "/manager/cafe24_SMS_sender.php";
	frm.submit();

}

function js_save() {
	
	var frm = document.frm;
	
	if (frm.claim_state.value == "") {
		alert("클레임을 선택하세요");
		frm.claim_state.focus();
		return;
	}

	/*
	if (frm.receiver_name.value == "") {
		alert("상담원 이름을 입력하세요");
		frm.receiver_name.focus();
		return;
	}


	if (frm.refund_type.value == "") {
		alert("반송 택배비 여부를 선택하세요");
		frm.refund_type.focus();
		return;
	}
	
	if (frm.refund_type.value == "환불함") {
	
		if (frm.bank_name.value.trim() == "") {
			alert("환불 은행를 입력하세요");
			frm.bank_name.focus();
			return;
		}

		if (frm.bank_pay_account.value.trim() == "") {
			alert("환불 은행 계좌번호를 입력하세요");
			frm.bank_pay_account.focus();
			return;
		}

		if (frm.cms_depositor.value.trim() == "") {
			alert("예금주를 입력하세요");
			frm.cms_depositor.focus();
			return;
		}
	}
	*/

	if (frm.claim_type.value == "") {
		alert("클레임 사유를 선택하세요");
		frm.claim_type.focus();
		return;
	}
	
	if (click_flag == "N") {
		frm.mode.value = "I";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		click_flag = "Y";
		frm.submit();
	} else {
		alert("처리 중 입니다. 화면이 1분 이상 멈추신 경우 창을 닫으시고 다시 처리해 주시기 바랍니다.");
		return;
	}
}


function js_refund_type() {

	var total_refund = 0;
	var frm = document.frm;

	if (frm.refund_type.value == "환불함") {
		total_refund = 6000; //(frm.sale_price.value * frm.cancel_qty.value) + (frm.extra_price.value * frm.cancel_qty.value);
	}
	
	frm.bank_amount.value = total_refund;
}


function js_goods_view(goods_no) {

	var frm = document.frm;
	
	frm.goods_no.value = goods_no;
	frm.mode.value = "S";
	frm.target = "blank";
	frm.method = "post";
	frm.action = "/manager/goods/goods_write.php";
	frm.submit();
	
}

</script>
<script type="text/javascript">

	// tag 관련 레이어가 다 로드 되기전인지 판별하기 위해 필요
	var tag_flag = "0";

	var checkFirst1 = false;
	var checkFirst2 = false;
	var lastKeyword1 = '';
	var lastKeyword2 = '';
	var loopSendKeyword1 = false;
	var loopSendKeyword2 = false;
	$(function(){
		$('input[name=search_name1]').on('change keydown',function (){

			$(".outstock").css("position","relative");
			$(".instock").css("position","static");

			if ((event.keyCode == 8) || (event.keyCode == 46)) {
				checkFirst1 = false;
				loopSendKeyword1 = false;
			}

			if (checkFirst1 == false) {
				setTimeout("sendKeyword();", 100);
				loopSendKeyword1 = true;
			}
			checkFirst1 = true;
		});

		$('input[name=search_name2]').on('change keydown',function (){

			$(".outstock").css("position","static");
			$(".instock").css("position","relative");

			if ((event.keyCode == 8) || (event.keyCode == 46)) {
				checkFirst2 = false;
				loopSendKeyword2 = false;
			}

			if (checkFirst2 == false) {
				setTimeout("sendKeyword2();", 100);
				loopSendKeyword2 = true;
			}
			checkFirst2 = true;
		});
	});

	function sendKeyword() {
		
		var frm = document.frm;

		if (loopSendKeyword1 == false) return;

		var keyword = document.frm.search_name1.value;
		
		if (keyword == '') {
			
			lastKeyword1 = '';
		
			hide('suggest');

		} else if (keyword != lastKeyword1) {

			lastKeyword1 = keyword;
				
			if (keyword != '') {
				console.log(keyword);
				frm.keyword.value = keyword;
				frm.goods_type.value = "unit";
				frm.mode.value = "1";
				frm.action = "/manager/goods/search_goods.php";
				frm.target = "ifr_hidden";
				frm.submit();
				
			} else {
				hide('suggest');
			}
		}
		setTimeout("sendKeyword();", 100);
	}

	function sendKeyword2() {
		
		var frm = document.frm;

		if (loopSendKeyword2 == false) return;

		var keyword = document.frm.search_name2.value;
		
		if (keyword == '') {
			
			lastKeyword2 = '';
		
			hide('suggest');

		} else if (keyword != lastKeyword2) {

			lastKeyword2 = keyword;
				
			if (keyword != '') {
				console.log(keyword);
				frm.keyword.value = keyword;
				frm.goods_type.value = "unit";
				frm.mode.value = "2";
				frm.action = "/manager/goods/search_goods.php";
				frm.target = "ifr_hidden";
				frm.submit();
				
			} else {
				hide('suggest');
			}
		}
		setTimeout("sendKeyword2();", 100);
	}


	function displayResult(str, mode) {
				
		var resultText = str;
		
		var result = resultText.split('|');

		var count = parseInt(result[0]);

		var keywordList = null;
		var arr_keywordList = null;

		if (count > 0) {
					
			keywordList = result[1].split('^');
			
			var html = '';
					
			for (var i = 0 ; i < keywordList.length ; i++) {
						
				arr_keywordList = keywordList[i].split('');
				
				html += "<table width='100%' border='0'><tr><td style='padding:0px 5px 0px 5px' width='55px'><img src='"+arr_keywordList[2]+"' width='50' height='50' border='0'></td><td><a href=\"javascript:js_select('"+
				arr_keywordList[0]+"','"+arr_keywordList[1]+"', '" + mode + "')\">"+
				arr_keywordList[1]+"</a></td><td width='105px'>판매가 : "+arr_keywordList[3]+"</td></tr></table>";
		
			}

			var listView = document.getElementById('suggestList' + mode);
			listView.innerHTML = html;

			var elmt_Suggest = document.getElementById('suggest' + mode);
					
			elmt_Suggest.style.visibility  ="visible"; 
		} else {
			elmt_Suggest.style.visibility  ="hidden"; 
		}
	}

	function js_select(selectedKey,selectedKeyword, mode) {

		var frm = document.frm;

		if(mode == "1")
			frm.search_name1.value = selectedKeyword;
		else 
			frm.search_name2.value = selectedKeyword;

		arr_keywordValues = selectedKey.split('');

		//frm.goods_name.value					= arr_keywordValues[0];
		//frm.goods_no.value						= arr_keywordValues[1];
        // arr_keywordValues[2]; 공급가
		// arr_keywordValues[3]; 판매가
		var sub_goods_ids = frm.elements['sub_goods_id[]'];
		var sub_mode = frm.elements['sub_mode[]'];
		if(sub_goods_ids != undefined)
		{
			if(sub_goods_ids.value == arr_keywordValues[1] && sub_mode.value == mode) 
			{
				alert('이미 추가한 상품입니다');
				return;
			}
			for (var i = 0; i < sub_goods_ids.length; i++) {
				if(sub_goods_ids[i].value == arr_keywordValues[1] && sub_mode[i].value == mode){
					alert('이미 추가한 상품입니다');
					return;
				}
			}
		}


		$(".sub_goods_list" + mode).append("<tr><th>상품명</th><td class='line'><input type='hidden' name='sub_mode[]' value='" + mode + "'>" + arr_keywordValues[0] + "[<a href=\"javascript:js_goods_view('"+ arr_keywordValues[1] + "')\">"+ arr_keywordValues[1] + "</a>]" + "<input type='hidden' name='sub_goods_id[]' value='" + arr_keywordValues[1] + "'><input type='hidden' name='sub_in_out[]' value='" + (mode == "1" ? "OUT" : "IN") + "'></td><th>수량</th><td class='line'><input type='text' name='sub_goods_cnt[]' class='txt' style='width:80px' value='1'>개</td><td style='border-underline:1px solid #d2dfe5'><span class='remove_sub' style='color:#478fb2; cursor:pointer; font-weight:bold; text-decoration:underline;'>삭제</span></td></tr>");

		if(mode == "1")
		{
			loopSendKeyword1 = false;
			checkFirst1 = false;
		}else { 
			loopSendKeyword2 = false;
			checkFirst2 = false;
		}
		hide('suggest' + mode);

	}

	function show(elementId) {
		var element = document.getElementById(elementId);
		
		if (element) {
			element.style.visibility  ="visible"; 
			//element.style.display = '';
		}
	}

	function hide(elementId) {
		var element = document.getElementById(elementId);
		if (element) {
			element.style.visibility  ="hidden"; 
			//element.style.display = 'none';
		}
	}

$(function(){
	$('body').on('click', '.remove_sub', function() {
		$(this).closest("tr").remove();
	});
});
</script>
</head>
<style>
	table.rowstable01 th.cancel {background-color: #ff8080; color:white;} 
	table.rowstable01 td.cancel {background-color: #ffb3b3;}
</style>



<body id="popup_order">

<div id="popupwrap_order">
	<h1>클레임 등록</h1>
	<div id="postsch_file">
		<form name="frm" method="post">
		<input type="hidden" name="keyword" value="">
		<input type="hidden" name="goods_type" value="">
		<input type="hidden" name="mode" value="">
		<input type="hidden" name="order_goods_no" value="<?=$order_goods_no?>">

		<h2>* 주문 상품</h2>
					<table cellpadding="0" cellspacing="0" class="rowstable01" style="width:98%" border="0">
						<colgroup>
						<col width="7%" />
						<col width="33%" />
						<col width="6%" />
						<col width="10%" />
						<col width="10%" />
						<col width="22%" />
						<col width="10%" />
					</colgroup>
					<tr>
						<th rowspan="2">상품코드</th>
						<th>상품명</th>
						<th rowspan="2">수량</th>
						<th>금액</th>
						<th rowspan="2">합계</th>
						<th>주문상태</th>
						<th rowspan="2" class="end cancel">취소수량</th>
					</tr>
					<tr>
						<th>옵션</th>
						<th>배송비</th>
						<th>배송정보</th>
					</tr>
				<?
					$nCnt = 0;
					$total_sum_price = 0;
					$sum_qty = 0;
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$ORDER_GOODS_NO			= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
							$ON_UID							= trim($arr_rs[$j]["ON_UID"]);
							$MEM_NO							= trim($arr_rs[$j]["MEM_NO"]);
							$BUY_CP_NO					= trim($arr_rs[$j]["BUY_CP_NO"]);
							$RESERVE_NO					= trim($arr_rs[$j]["RESERVE_NO"]);
							$GOODS_NO						= trim($arr_rs[$j]["GOODS_NO"]);
							$GOODS_CODE					= trim($arr_rs[$j]["GOODS_CODE"]);
							$GOODS_SUB_NAME			= trim($arr_rs[$j]["GOODS_SUB_NAME"]);
							
							$GOODS_NAME					= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
							$BUY_PRICE					= trim($arr_rs[$j]["BUY_PRICE"]);
							$SALE_PRICE					= trim($arr_rs[$j]["SALE_PRICE"]);
							$EXTRA_PRICE				= trim($arr_rs[$j]["EXTRA_PRICE"]);
							$DELIVERY_PRICE			= trim($arr_rs[$j]["DELIVERY_PRICE"]);

							$GOODS_OPTION_01		= trim($arr_rs[$j]["GOODS_OPTION_01"]);
							$GOODS_OPTION_02		= trim($arr_rs[$j]["GOODS_OPTION_02"]);
							$GOODS_OPTION_03		= trim($arr_rs[$j]["GOODS_OPTION_03"]);
							$GOODS_OPTION_04		= trim($arr_rs[$j]["GOODS_OPTION_04"]);
							$GOODS_OPTION_NM_01	= trim($arr_rs[$j]["GOODS_OPTION_NM_01"]);
							$GOODS_OPTION_NM_02	= trim($arr_rs[$j]["GOODS_OPTION_NM_02"]);
							$GOODS_OPTION_NM_03	= trim($arr_rs[$j]["GOODS_OPTION_NM_03"]);
							$GOODS_OPTION_NM_04	= trim($arr_rs[$j]["GOODS_OPTION_NM_04"]);
							$SA_DELIVERY_PRICE 	= trim($arr_rs[$j]["SA_DELIVERY_PRICE"]);
							$TAX_TF							= trim($arr_rs[$j]["TAX_TF"]);

							$CATE_01						= trim($arr_rs[$j]["CATE_01"]);
							$CATE_02						= trim($arr_rs[$j]["CATE_02"]);
							$CATE_03						= trim($arr_rs[$j]["CATE_03"]);
							$CATE_04						= trim($arr_rs[$j]["CATE_04"]);

							$SUM_PRICE					= trim($arr_rs[$j]["SUM_PRICE"]);
							$PLUS_PRICE					= trim($arr_rs[$j]["PLUS_PRICE"]);
							$GOODS_LEE					= trim($arr_rs[$j]["LEE"]);
							$QTY								= trim($arr_rs[$j]["QTY"]);
							$REQ_DATE						= trim($arr_rs[$j]["PAY_DATE"]);
							$END_DATE						= trim($arr_rs[$j]["FINISH_DATE"]);
							$ORDER_STATE				= trim($arr_rs[$j]["ORDER_STATE"]);
							$DELIVERY_CP				= trim($arr_rs[$j]["DELIVERY_CP"]);
							$DELIVERY_NO				= trim($arr_rs[$j]["DELIVERY_NO"]);

							$option_str = "";

							if ($GOODS_OPTION_NM_01 <> "") {
								$option_str .= $GOODS_OPTION_NM_01." : ".$GOODS_OPTION_01."&nbsp;";
							}

							if ($GOODS_OPTION_NM_02 <> "") {
								$option_str .= $GOODS_OPTION_NM_02." : ".$GOODS_OPTION_02."&nbsp;";
							}

							if ($GOODS_OPTION_NM_03 <> "") {
								$option_str .= $GOODS_OPTION_NM_03." : ".$GOODS_OPTION_03."&nbsp;";
							}

							if ($GOODS_OPTION_NM_04 <> "") {
								$option_str .= $GOODS_OPTION_NM_04." : ".$GOODS_OPTION_04."&nbsp;";
							}
							

							$refund_able_qty = getRefundAbleQty($conn, $RESERVE_NO, $ORDER_GOODS_NO);
						?>

						<tr>
							<td rowspan="2"><a href="javascript:js_goods_view('<?= $GOODS_NO?>')"><?= $GOODS_NO?></a></td>
							<td class="modeual_nm" height="35"><?=$GOODS_NAME?><br><?=$GOODS_SUB_NAME?></td>
							<td rowspan="2" class="price"><?=$QTY?></td>
							<td class="price">
								<?=number_format($SALE_PRICE)?>
								<input type="hidden" name="on_uid" value="<?=$ON_UID?>">
								<input type="hidden" name="reserve_no" value="<?=$RESERVE_NO?>">
								<input type="hidden" name="mem_no" value="<?=$MEM_NO?>">
								<input type="hidden" name="buy_cp_no" value="<?=$BUY_CP_NO?>">
								<input type="hidden" name="mem_no" value="<?=$MEM_NO?>">
								<input type="hidden" name="goods_no" value="<?=$GOODS_NO?>">
								<input type="hidden" name="goods_name" value="<?=$GOODS_NAME?>">
								<input type="hidden" name="goods_code" value="<?=$GOODS_CODE?>">
								<input type="hidden" name="goods_sub_name" value="<?=$GOODS_SUB_NAME?>">
								<input type="hidden" name="buy_price" value="<?=$BUY_PRICE?>">
								<input type="hidden" name="sale_price" value="<?=$SALE_PRICE?>">
								<input type="hidden" name="extra_price" value="<?=$EXTRA_PRICE?>">
								<input type="hidden" name="goods_option_nm_01" value="<?=$GOODS_OPTION_NM_01?>">
								<input type="hidden" name="goods_option_nm_02" value="<?=$GOODS_OPTION_NM_02?>">
								<input type="hidden" name="goods_option_nm_03" value="<?=$GOODS_OPTION_NM_03?>">
								<input type="hidden" name="goods_option_nm_04" value="<?=$GOODS_OPTION_NM_04?>">
								<input type="hidden" name="goods_option_01" value="<?=$GOODS_OPTION_01?>">
								<input type="hidden" name="goods_option_02" value="<?=$GOODS_OPTION_02?>">
								<input type="hidden" name="goods_option_03" value="<?=$GOODS_OPTION_03?>">
								<input type="hidden" name="goods_option_04" value="<?=$GOODS_OPTION_04?>">
								<input type="hidden" name="cate_01" value="<?=$CATE_01?>">
								<input type="hidden" name="cate_02" value="<?=$CATE_02?>">
								<input type="hidden" name="cate_03" value="<?=$CATE_03?>">
								<input type="hidden" name="cate_04" value="<?=$CATE_04?>">
								<input type="hidden" name="delivery_price" value="<?=$DELIVERY_PRICE?>">
								<input type="hidden" name="sa_delivery_price" value="<?=$SA_DELIVERY_PRICE?>">
								<input type="hidden" name="tax_tf" value="<?=$TAX_TF?>">
								<input type="hidden" name="refund_able_qty" value="<?=$refund_able_qty?>">
								
							</td>
							<td rowspan="2" class="price"><?=number_format($SUM_PRICE)?></td>
							<td class="filedown"><?=getDcodeName($conn, "ORDER_STATE", $ORDER_STATE);?></td>
							<td rowspan="2" class="cancel">
								<? if ($refund_able_qty <=0 ) { ?>
								<input type="hidden" name="cancel_qty" value = "0">
								0
								<? } else {?>
								<select name="cancel_qty" onChange="js_refund_type();">
									<? for ($c = 1 ; $c <= $refund_able_qty ; $c++) { ?>
									<option value="<?=$c?>" <?=($c == $refund_able_qty ? "selected" : "") ?> ><?=$c?></option>
									<? } ?>
								</select>
								<? }?>
							</td>
						</tr>
						<tr>
							<td class="filedown"><?=$option_str ?>&nbsp;</td>
							<td class="price" height="35"><?=number_format($EXTRA_PRICE)?></td>
							<td>
								<? 
									$arr_delivery = listOrderDelivery($conn, $RESERVE_NO);
									for ($k = 0 ; $k < sizeof($arr_delivery); $k++) { 
										$rs_delivery_seq = $arr_delivery[$k]["DELIVERY_SEQ"];
										$rs_delivery_cp  = $arr_delivery[$k]["DELIVERY_CP"];
										$rs_delivery_no  = $arr_delivery[$k]["DELIVERY_NO"];
									
								?>
									<?=getDeliveryLink($conn, $rs_delivery_cp, $rs_delivery_no)."<br/>"?>
								<?

									}
								
								?>
							</td>
						</tr>

						<?
						}
					}else{
						?>
						<tr>
							<td height="50" align="center" colspan="10">데이터가 없습니다. </td>
						</tr>
					<?
						}
					?>
					</table>

			<h2>* 주문 자재 상세</h2>
			<table cellpadding="0" cellspacing="0" border="0" class="rowstable01" style="width:98%">
				<colgroup>
					<col width="25%">
					<col width="25%">
					<col width="25%">
					<col width="25%">
				</colgroup>
					<tr>
						<th>자재코드</th>
						<th>상품명</th>
						<th>구성상품수량</th>
						<th class="end">구성상품수 * 주문수</th>
					</tr>
					<?
						$arr_sub =	selectGoodsSub($conn, $GOODS_NO);
							
						for($k = 0; $k < sizeof($arr_sub); $k ++) {
							$SUB_GOODS_SUB_NO	= trim($arr_sub[$k]["GOODS_SUB_NO"]);
							$SUB_GOODS_NAME	= trim($arr_sub[$k]["GOODS_NAME"]);
							$SUB_GOODS_CODE	= trim($arr_sub[$k]["GOODS_CODE"]);
							$SUB_GOODS_CNT	= trim($arr_sub[$k]["GOODS_CNT"]);
							//$SUB_DELIVERY_CNT_IN_BOX	= trim($arr_sub[$k]["DELIVERY_CNT_IN_BOX"]);
	
					?>
					<tr height="30">
						<td><?=$SUB_GOODS_CODE?></td>
						<td><?=$SUB_GOODS_NAME?></td>
						<td><?=$SUB_GOODS_CNT?></td>
						<td><?=$SUB_GOODS_CNT * $refund_able_qty?> </td>
					</tr>
					<? } ?>
			</table>

			<h2>* 클레임 등록</h2>

					<table cellpadding="0" cellspacing="0" border="0" class="colstable01" style="width:98%">
						<colgroup>
							<col width="20%">
							<col width="30%">
							<col width="20%">
							<col width="30%">
						</colgroup>
							<tr>
								<th>클레임 선택</th>
								<td>
									<?
										if ($ORDER_STATE == "0") {
											$condition = "AND DCODE IN ('4','99')";
										} else if ($ORDER_STATE == "1") {
											$condition = "AND DCODE IN ('6','99')";
										} else if ($ORDER_STATE == "2") {
											$condition = "AND DCODE IN ('6', '8','99')";
										} else if ($ORDER_STATE == "3") {
											$condition = "AND DCODE IN ('7', '8','99')";
										}
									?>
									<?=makeSelectBoxWithCondition($conn,"ORDER_STATE", "claim_state","200", "클레임을 선택하세요.", "", $rs_claim_state, $condition );?>

								</td>
								<th>클레임 사유</th>
								<td colspan="2">
									<?=makeSelectBox($conn,"CLAIM_TYPE", "claim_type", "200", "클레임 사유를 선택하세요.", "", $rs_claim_type)?>

									
									<script>
									$(function(){

										claim_options_all = $("select[name=claim_type] option").clone();

										$("select[name=claim_state]").change(function(){

											var claim_type = $("select[name=claim_type]").find('option').remove().end();
											if($(this).val() == "6") //취소
											{
												claim_options_all.each(function(index, item){

													if(item.value.indexOf("CC") == 0)
														$("select[name=claim_type]").append(item);

												});
											}
											else if($(this).val() == "7") //반품
											{
												claim_options_all.each(function(index, item){

													if(item.value.indexOf("CR") == 0)
														$("select[name=claim_type]").append(item);

												});
											}
											else if($(this).val() == "8") //교환
											{
												claim_options_all.each(function(index, item){

													if(item.value.indexOf("CE") == 0)
														$("select[name=claim_type]").append(item);

												});
											}
											else if($(this).val() == "99") //기타
											{
												claim_options_all.each(function(index, item){

													if(item.value.indexOf("CX") == 0)
														$("select[name=claim_type]").append(item);

												});
											}
										});

									});
									</script>
								</td>
							</tr>
							<tr>
								<th>상담원 이름</th>
								<td colspan="3">
									<input type="text" name="receiver_name" value="" />
								</td>
							<tr>
							<tr>
								<th>추가 결제사항</th>
								<td colspan="3" class="add_here">
									<!--<?=makeSelectBoxOnChange($conn,"REFUND_TYPE", "refund_type","100", "요청여부", "", "")?>-->
									<div class="refund">
										<?=makeSelectBoxOnChange($conn,"REFUND_METHOD", "arr_refund_method[]","100", "선택하세요", "", "")?>
										<input type="text" name="arr_bank_amount[]" value="" /> 원
										<input type="button" name="b" onclick="js_append_payment(this);" value="추가" />
										<input type="button" name="b" onclick="js_delete_payment(this);" value="삭제" />
									</div>
								</td>
							</tr>
							<script>
								function js_append_payment(elem) { 
									var copied = $(elem).closest(".refund").clone();
									$(".add_here").append(copied);
								}

								function js_delete_payment(elem) {
									$(elem).closest(".refund").remove();
								}
							</script>
							<!--
							<tr>
								<th>사유상세</th>
								<td colspan="3">
									<textarea name="claim_detail" style="width:95%; height:30px"></textarea>
								</td>
							<tr>
							-->
							<tr>
								<th>클레임 메모</th>
								<td colspan="3">
									<textarea name="claim_memo" style="width:95%; height:60px"></textarea>
								</td>
							<tr>
							<tr>
								<th>재고 조정</th>
								<td colspan="3">
									<label><input type="checkbox" name="chk_wrong" value="Y"/>오배송</label>
									<label><input type="checkbox" name="chk_missing" value="Y"/>누락</label>
									<label><input type="checkbox" name="chk_return" value="Y"/>반품</label>
									<br/><br/>
									
									<table cellpadding="0" cellspacing="0" border="0" class="colstable01 restock_table" style="width:98%; display:none;">
										<colgroup>
											<col width="20%">
											<col width="30%">
											<col width="20%">
											<col width="30%">
										</colgroup>
										<tr class="in">
											<th><span class="plus_msg"></span></th>
											<td colspan="3" style="position:relative" class="line instock">
												<div id="suggest2" style="position:absolute; left:15px; top:25px; width:95%; height:81px; z-index:0; visibility: hidden; width:95%; ">
													<div id="suggestList2" style="position:relative; border:solid 1px #cec6ba; background:#FFFFFF; width:95%;"></div>
												</div>
												<input type="text" class="txt search_name" style="width:95%; ime-mode:Active;" name="search_name2" value=""  onFocus="this.value='';" />

												<table cellpadding="0" cellspacing="0" class="colstable02" style="margin-top:5px;">
												<colgroup>
													<col width="12.5%" />
													<col width="30%" />
													<col width="12.5%" />
													<col width="30%" />
													<col width="*%" />
												</colgroup>
												<thead>
													<tr>
														<th colspan="5" class="line">상품을 검색해서 선택해주세요</th>
													</tr>
												</thead>
												<tbody class="sub_goods_list2">
												</tbody>
												</table>
											</td>
										</tr>
										<tr class="out">
											<th><span class="minus_msg"></span></th>
											<td colspan="3" style="position:relative" class="line outstock">
												<div id="suggest1" style="position:absolute; left:15px; top:25px; width:95%; height:81px; z-index:0; visibility: hidden; width:95%; ">
													<div id="suggestList1" style="position:relative; border:solid 1px #cec6ba; background:#FFFFFF; width:95%;"></div>
												</div>
												<input type="text" class="txt search_name" style="width:95%; ime-mode:Active;" name="search_name1" value=""  onFocus="this.value='';" />

												<table cellpadding="0" cellspacing="0" class="colstable02" style="margin-top:5px;">
												<colgroup>
													<col width="12.5%" />
													<col width="30%" />
													<col width="12.5%" />
													<col width="30%" />
													<col width="*%" />
												</colgroup>
												<thead>
													<tr>
														<th colspan="5" class="line">상품을 검색해서 선택해주세요</th>
													</tr>
												</thead>
												<tbody class="sub_goods_list1">
												</tbody>
												</table>
											</td>
										</tr>									
									</table>

								</td>
							<tr>
							<script>
								$(function(){

									$("input[name=chk_wrong]").change(function(){

										$("input[name=chk_missing]").prop("checked", false);
										$("input[name=chk_return]").prop("checked", false);

										$(".plus_msg").html("원주문받은 자재(+)");
										$(".minus_msg").html("잘못나간 자재(-)");

										if($(this).is(":checked")) { 

											$(".restock_table").show();
											$(".in, .out").show();

											$(".sub_goods_list1 > tr").remove();
											$(".sub_goods_list2 > tr").remove();
											$(".search_name").val('');

										}else { 

											$(".restock_table").hide();
											$(".in, .out").hide();

											$(".sub_goods_list1 > tr").remove();
											$(".sub_goods_list2 > tr").remove();
											$(".search_name").val('');

										}

									});

									$("input[name=chk_missing]").change(function(){

										$("input[name=chk_wrong]").prop("checked", false);
										$("input[name=chk_return]").prop("checked", false);

										$(".plus_msg").html("안나간 자재(+)");
										$(".minus_msg").html("");

										if($(this).is(":checked")) { 

											$(".restock_table").show();
											$(".in").show();
											$(".out").hide();

											$(".sub_goods_list1 > tr").remove();
											$(".sub_goods_list2 > tr").remove();
											$(".search_name").val('');

										}else { 

											$(".restock_table").hide();
											$(".in").hide();
											$(".out").hide();

											$(".sub_goods_list1 > tr").remove();
											$(".sub_goods_list2 > tr").remove();
											$(".search_name").val('');

										}

									});

									$("input[name=chk_return]").change(function(){

										$("input[name=chk_wrong]").prop("checked", false);
										$("input[name=chk_missing]").prop("checked", false);

										$(".plus_msg").html("돌아올 자재(+)");
										$(".minus_msg").html("");

										if($(this).is(":checked")) { 

											$(".restock_table").show();
											$(".in").show();
											$(".out").hide();

											$(".sub_goods_list1 > tr").remove();
											$(".sub_goods_list2 > tr").remove();
											$(".search_name").val('');

										}else { 

											$(".restock_table").hide();
											$(".in").hide();
											$(".out").hide();

											$(".sub_goods_list1 > tr").remove();
											$(".sub_goods_list2 > tr").remove();
											$(".search_name").val('');

										}

									});
								});

							
							</script>
							<!--
							<tr>
								<th>입고 등록</th>
								<td colspan="3" style="position:relative" class="line instock">
									<div id="suggest2" style="position:absolute; left:15px; top:25px; width:95%; height:81px; z-index:0; visibility: hidden; width:95%; ">
										<div id="suggestList2" style="position:relative; border:solid 1px #cec6ba; background:#FFFFFF; width:95%;"></div>
									</div>
									<input type="text" class="txt search_name" style="width:95%; ime-mode:Active;" name="search_name2" value=""  onFocus="this.value='';" />

									<table cellpadding="0" cellspacing="0" class="colstable02" style="margin-top:5px;">
									<colgroup>
										<col width="12.5%" />
										<col width="30%" />
										<col width="12.5%" />
										<col width="30%" />
										<col width="*%" />
									</colgroup>
									<thead>
										<tr>
											<th colspan="5" class="line">상품을 검색해서 선택해주세요</th>
										</tr>
									</thead>
									<tbody class="sub_goods_list2">
									</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<th>출고 등록</th>
								<td colspan="3" style="position:relative" class="line outstock">
									<div id="suggest1" style="position:absolute; left:15px; top:25px; width:95%; height:81px; z-index:0; visibility: hidden; width:95%; ">
										<div id="suggestList1" style="position:relative; border:solid 1px #cec6ba; background:#FFFFFF; width:95%;"></div>
									</div>
									<input type="text" class="txt search_name" style="width:95%; ime-mode:Active;" name="search_name1" value=""  onFocus="this.value='';" />

									<table cellpadding="0" cellspacing="0" class="colstable02" style="margin-top:5px;">
									<colgroup>
										<col width="12.5%" />
										<col width="30%" />
										<col width="12.5%" />
										<col width="30%" />
										<col width="*%" />
									</colgroup>
									<thead>
										<tr>
											<th colspan="5" class="line">상품을 검색해서 선택해주세요</th>
										</tr>
									</thead>
									<tbody class="sub_goods_list1">
									</tbody>
									</table>
								</td>
							</tr>
							-->
					</table>
					<div class="sp10"></div>
					<div class="btn">
					<? if ($sPageRight_U == "Y") {?>
						<? if ($refund_able_qty <=0 ) { ?>
						<a href="javascript:js_save();"><img src="../images/admin/btn_regist_02.gif" alt="등록" /></a>
						<? } else {?>
						<a href="javascript:js_save();"><img src="../images/admin/btn_regist_02.gif" alt="등록" /></a>
						<? } ?>
					<? } ?>
					</div>


					<h2>* 문자 발송</h2>
					<?
						$arr_rs_order = selectOrder($conn, $RESERVE_NO);
						if (sizeof($arr_rs_order) > 0) {
							for ($k = 0 ; $k < sizeof($arr_rs_order); $k++) {
								
								//ORDER_NO, CP_ORDER_NO, ON_UID, RESERVE_NO, MEM_NO, CP_NO, O_MEM_NM, O_ZIPCODE, O_ADDR1, O_ADDR2, O_PHONE, O_HPHONE, O_EMAIL,
								//		 R_MEM_NM, R_ZIPCODE, R_ADDR1, R_ADDR2, R_PHONE, R_HPHONE, R_EMAIL, MEMO, OPT_STCK_L1, OPT_STCK_L2, OPT_STCK_L3, OPT_STCK_SIZE, OPT_WRAPPING_PAPER, OPT_PRINT_TEXT, OPT_STCK_OUTBOX_TF, OPT_READY_DATE, OPT_MANAGER_NO, 
								//		 TOTAL_BUY_PRICE, TOTAL_SALE_PRICE, TOTAL_EXTRA_PRICE, TOTAL_DELIVERY_PRICE, TOTAL_QTY,
								//		 ORDER_DATE, PAY_DATE, PAY_TYPE, DELIVERY_TYPE, DELIVERY_DATE, FINISH_DATE, 
								//		 CANCEL_DATE, USE_TF, DEL_TF, REG_ADM, REG_DATE, DEL_ADM, DEL_DATE

								$RESERVE_NO					= trim($arr_rs_order[$k]["RESERVE_NO"]);

								$O_HPHONE					= trim($arr_rs_order[$k]["O_HPHONE"]);
								$R_HPHONE					= trim($arr_rs_order[$k]["R_HPHONE"]);
								$CP_NO					= trim($arr_rs_order[$k]["CP_NO"]);
								$SELLER_CP_NAME = getCompanyNameWithNoCode($conn, $CP_NO);
								$SELLER_CP_PHONE = getCompanyPhone($conn, $CP_NO);
								
								$arr_phone = explode("-",$SELLER_CP_PHONE);
								if(sizeof($arr_phone) > 2)
								{
									$sphone1 = $arr_phone[0];
									$sphone2 = $arr_phone[1];
								    $sphone3 = $arr_phone[2];
								}
								else
								{
									$sphone1 = '00';
									$sphone2 = $arr_phone[0];
								    $sphone3 = $arr_phone[1];
								}
								

							}
						}
					
					?>
					<input type="hidden" name="cp_no" value="<?=$CP_NO?>">
					<table cellpadding="0" cellspacing="0" border="0" class="colstable01" style="width:98%">
						<colgroup>
							<col width="20%">
							<col width="30%">
							<col width="20%">
							<col width="30%">
						</colgroup>
							<tr>
								<th>메세지 선택</th>
								<td>
									<?=makeSelectBoxWithExt($conn,"SMS_MESSAGE", "sms_message","200", "메세지 템플릿을 선택하세요.", "", "")?>
									<script>
										$(function(){
											$("select[name=sms_message]").change(function(){
												if($(this).val() != '')
												{

													template_message = $("option[value=" + $(this).val() + "]").attr("data-ext");

													template_message = replaceAll(template_message, "[판매사]", $("input[name=seller_cp_name]").val());
													template_message = replaceAll(template_message, "[판매사번호]", $("input[name=seller_cp_phone]").val());
													template_message = replaceAll(template_message, "[상품명]", $("input[name=goods_name]").val());
													$("textarea[name=msg]").val(template_message);


												}
											});
										});
									</script>
								</td>
								<th>고객 번호</th>
								<td>
									
									<select name="sel_phone">
										<option value="<?=$O_HPHONE?>">주문자</option>
										<option value="<?=$R_HPHONE?>">수령자</option>
										<option value="">직접입력</option>
									</select>
									<input type="text" name="rphone" value="<?=$O_HPHONE?>" class="txt" style="width:50%;" onkeyup="return isPhoneNumber(this)" >
									<script>
										$("select[name=sel_phone]").change(function(){
											$("input[name=rphone]").val($(this).val());
										});
									
									</script>
								</td>
							</tr>
							<tr>
								<th>제목</th>
								<td colspan="3">
									<input type="text" name="subject" value = "<?=$SELLER_CP_NAME?> 엘지생활건강입니다" class="txt" style="width:95%;">
								</td>
							<tr>
							<tr>
								<th>발송 내용</th>
								<td colspan="3">
									<textarea name="msg" style="width:95%; height:60px"></textarea>
								</td>
							<tr>
					</table>
					<div class="sp10"></div>
					<div class="btn">
					<? if ($sPageRight_U == "Y") {?>
						<? if ($refund_able_qty <=0 ) { ?>
						<a href="javascript:js_send();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
						<? } else {?>
						<a href="javascript:js_send();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
						<? } ?>
					<? } ?>
					</div>

					<input type="hidden" name="seller_cp_name" value="<?=$SELLER_CP_NAME?>"/>
					<input type="hidden" name="seller_cp_phone" value="<?=$SELLER_CP_PHONE?>"/>

					<!--SMS용-->
					<input type="hidden" name="smsType" value="L"/>
					<input type="hidden" name="destination" value="">
					<input type="hidden" name="nointeractive" value="0"> 
					<input type="hidden" name="testflag" value="N">
					<input type="hidden" name="sphone1" value="<?=$sphone1?>">
					<input type="hidden" name="sphone2" value="<?=$sphone2?>">
					<input type="hidden" name="sphone3" value="<?=$sphone3?>">
					<input type="hidden" name="repeatFlag" value="N" />
					<input type="hidden" name="rdate" value=""> 
					<input type="hidden" name="rtime" value="">

					<div class="sp35"></div>
</div>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
</div>
<script type="text/javascript" src="../js/wrest.js"></script>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>