<?session_start();?>
<?
# =============================================================================
# File Name    : order_write.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @C&C Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	//echo $con_order_type;
	
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
	require "../../_common/common_header.php"; 

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/cart/cart.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/member/member.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/payment/payment.php";

	if ($mode == "I") {

		//updateBoardUseTF($conn, $use_tf, $s_adm_no, $bb_code, $bb_no);
		// 신규 주문 번호 생성
		$new_reserve_no = getReservNo($conn,"EN");

		$s_ord_no = get_session('s_ord_no');
		//echo "주문번호".$s_ord_no;

		// 맴버 등록
		$b_is_member = memberChk($conn, $o_mem_name, $o_phone, $o_hphone);
		
		$mem_type = $cp_type; 

		if (!$b_is_member) {

			$mem_id		= str_replace("-","",$o_hphone);
			$mem_pw		= right($mem_id,4);
			$email_tf = "Y";
			$join_how = "물품구매";
			$use_tf		= "Y";

			$new_mem_no = insertMember($conn, $mem_type, $mem_id, $mem_pw, $o_mem_name, $jumin1, $jumin2, $biz_num1, $biz_num2, $biz_num3, $birth_date, $calendar, $o_email, $email_tf, $o_zipcode, $o_addr1, $addr2, $o_phone, $o_hphone, $job, $position, $cphone, $cfax, $czipcode, $caddr1, $caddr2, $join_how, $join_how_person, $join_how_etc, $etc, $foreigner_num, $use_tf, $s_adm_no);
			
		} else {
			$new_mem_no = getMemberNo($conn, $o_mem_name, $o_phone, $o_hphone);
		}
		
		//echo $s_ord_no;

		$arr_rs_cart = listCart($conn, $s_ord_no, $cp_type, $use_tf, $del_tf);

		$nCnt = 0;
		$TOTAL_BUY_PRICE = 0;
		$TOTAL_SALE_PRICE = 0;
		$TOTAL_EXTRA_PRICE = 0;
		$TOTAL_DELIVERY_PRICE = 0;
		$TOTAL_QTY = 0;
		
		if (sizeof($arr_rs_cart) > 0) {

			for ($j = 0 ; $j < sizeof($arr_rs_cart); $j++) {
				
				$CART_NO						= trim($arr_rs_cart[$j]["CART_NO"]);
				$ON_UID							= trim($arr_rs_cart[$j]["ON_UID"]);
				$BUY_CP_NO					= trim($arr_rs_cart[$j]["BUY_CP_NO"]);
				$GOODS_NO						= trim($arr_rs_cart[$j]["GOODS_NO"]);
				$GOODS_NAME					= trim($arr_rs_cart[$j]["GOODS_NAME"]);
				$QTY								= trim($arr_rs_cart[$j]["QTY"]);
				$BUY_PRICE					= trim($arr_rs_cart[$j]["BUY_PRICE"]);
				$SALE_PRICE					= trim($arr_rs_cart[$j]["SALE_PRICE"]);
				$EXTRA_PRICE				= trim($arr_rs_cart[$j]["EXTRA_PRICE"]);
				$DELIVERY_PRICE			= trim($arr_rs_cart[$j]["DELIVERY_PRICE"]);
				$SA_DELIVERY_PRICE	= trim($arr_rs_cart[$j]["SA_DELIVERY_PRICE"]);
				$GOODS_OPTION_01		= trim($arr_rs_cart[$j]["GOODS_OPTION_01"]);
				$GOODS_OPTION_02		= trim($arr_rs_cart[$j]["GOODS_OPTION_02"]);
				$GOODS_OPTION_03		= trim($arr_rs_cart[$j]["GOODS_OPTION_03"]);
				$GOODS_OPTION_04		= trim($arr_rs_cart[$j]["GOODS_OPTION_04"]);
				$GOODS_OPTION_NM_01	= trim($arr_rs_cart[$j]["GOODS_OPTION_NM_01"]);
				$GOODS_OPTION_NM_02	= trim($arr_rs_cart[$j]["GOODS_OPTION_NM_02"]);
				$GOODS_OPTION_NM_03	= trim($arr_rs_cart[$j]["GOODS_OPTION_NM_03"]);
				$GOODS_OPTION_NM_04	= trim($arr_rs_cart[$j]["GOODS_OPTION_NM_04"]);
				
				// 사입건에 대한 부분 확인 하자
				$SUM_BUY_PRICE = $QTY * $BUY_PRICE;
				$SUM_SALE_PRICE = $QTY * $SALE_PRICE;
				$SUM_EXTRA_PRICE = $QTY * $EXTRA_PRICE;
				$TOTAL_QTY = $TOTAL_QTY + $QTY;
				$TOTAL_BUY_PRICE = $TOTAL_BUY_PRICE + $SUM_BUY_PRICE;
				$TOTAL_SALE_PRICE = $TOTAL_SALE_PRICE + $SUM_SALE_PRICE;
				$TOTAL_EXTRA_PRICE = $TOTAL_EXTRA_PRICE + $SUM_EXTRA_PRICE;
				$TOTAL_DELIVERY_PRICE = $TOTAL_DELIVERY_PRICE + $DELIVERY_PRICE;

				$sa_delivery_price = 0;
				$order_state		= "1"; //입금완료처리 - giftnet
				$use_tf					= "Y";

				$arr_rs_goods = selectGoods($conn, $GOODS_NO);
				$TAX_TF				= trim($arr_rs_goods[0]["TAX_TF"]); 

				$result = insertOrderGoods($conn, $ON_UID, $new_reserve_no, $BUY_CP_NO, $new_mem_no, $j, $GOODS_NO, $goods_code, $GOODS_NAME, $goods_sub_name, $QTY, $GOODS_OPTION_01, $GOODS_OPTION_02, $GOODS_OPTION_03, $GOODS_OPTION_04, $GOODS_OPTION_NM_01, $GOODS_OPTION_NM_02, $GOODS_OPTION_NM_03, $GOODS_OPTION_NM_04, $cate_01, $cate_02, $cate_03, $cate_04, $BUY_PRICE, $SALE_PRICE, $EXTRA_PRICE, $DELIVERY_PRICE, $SA_DELIVERY_PRICE, $TAX_TF, $order_state, $use_tf, $s_adm_no);
			}
		}
		
		// 주문 정보 넣는다 배송지 정보등
		$opt_stck_l1 = $gd_cate_01;
		$opt_stck_l2 = $gd_cate_02;
		$opt_stck_l3 = $gd_cate_03;

		$result = insertOrder($conn, $order_type,  $ON_UID, $new_reserve_no, $new_mem_no, $cp_type, $o_mem_name, $o_zipcode, $o_addr1, $o_addr2, $o_phone, $o_hphone, $o_email, $r_mem_name, $r_zipcode, $r_addr1, $r_addr2, $r_phone, $r_hphone, $r_email, $memo, $opt_stck_l1, $opt_stck_l2, $opt_stck_l3, $opt_stck_size, $opt_wrapping_paper, $opt_print_text, $opt_stck_outbox_tf, $opt_ready_date, $opt_manager_no, $order_state, $TOTAL_BUY_PRICE, $TOTAL_SALE_PRICE, $TOTAL_EXTRA_PRICE, $TOTAL_DELIVERY_PRICE, $TOTAL_QTY, $pay_type, $delivery_type, $use_tf, $s_adm_no);

		$query_date = "UPDATE TBL_ORDER_GOODS SET ORDER_DATE = '$order_date' WHERE RESERVE_NO = '$new_reserve_no' ";
		mysql_query($query_date,$conn);

		$query_date = "UPDATE TBL_ORDER SET ORDER_DATE = '$order_date' WHERE RESERVE_NO = '$new_reserve_no' ";
		mysql_query($query_date,$conn);

		// 결제 방식을 입력한다. 
		// 결제는 이미 받은것으로 처리 - GIFTNET

		$pay_state	= "1";
		$pay_reason = "물품구매";
		$pay_type == "BANK";
		$cms_amount	= 0;
		$cms_casu		= 0;

		if ($pay_type == "BANK") {
			$bank_amount = $TOTAL_SALE_PRICE + $TOTAL_EXTRA_PRICE + $TOTAL_DELIVERY_PRICE;
		}

		// 일단 통장으로만

		$result = insertPayment($conn, $pay_type, $pay_state, $ON_UID, $new_reserve_no, $new_mem_no, $o_mem_nm, $mem_type, $pay_reason, $cms_amount, $cms_casu, $cms_pay_bank, $cms_pay_account, $cms_depositor, $bank_amount, $bank_pay_account, $bank_pay_date, $cash_receipt, $cash_receipt_phone, $cash_receipt_state, $card_amount, $card_name, $pgbank_amount, $pgbank_name, $card_code, $card_isscode, $card_appr_no, $card_appr_dm, $card_msg, $card_vantr, $card_num, $req_date, $use_tf, $s_adm_no);
		
		set_session('s_ord_no', "");
?>
<script type="text/javascript">
	
	var bDelOK = "";

	bDelOK = confirm('계속 주문 하시겠습니까?');

	if (bDelOK==true) {
		document.location = "order_write.php";
	} else {
		document.location = "order_list.php";
	}

</script>
<?
		mysql_close($conn);
		exit;
	}

#====================================================================
# Request Parameter
#====================================================================
	set_session('s_ord_no', "");

	if (!get_session('s_ord_no')) {
		set_session('s_ord_no', getUniqueId($conn));
	}

	$s_ord_no = get_session('s_ord_no');

	//echo $s_ord_no;

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$reserve_no				= trim($reserve_no);
	$sel_order_state	= trim($sel_order_state);
	$sel_pay_type			= trim($sel_pay_type);
	$start_date				= trim($start_date);
	$end_date					= trim($end_date);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
	$del_tf = "N";
	
#===============================================================
# Get Search list count
#===============================================================
	$arr_order_rs = selectOrder($conn, $reserve_no);

	$rs_order_no					= trim($arr_order_rs[0]["ORDER_NO"]); 
	$rs_mem_no						= trim($arr_order_rs[0]["MEM_NO"]); 
	$rs_order_state				= trim($arr_order_rs[0]["ORDER_STATE"]); 
	$rs_total_sale_price	= trim($arr_order_rs[0]["TOTAL_SALE_PRICE"]); 
	$rs_total_extra_price	= trim($arr_order_rs[0]["TOTAL_EXTRA_PRICE"]); 
	$rs_order_date				= trim($arr_order_rs[0]["ORDER_DATE"]); 
	$rs_pay_date					= trim($arr_order_rs[0]["PAY_DATE"]); 
	$rs_pay_type					= trim($arr_order_rs[0]["PAY_TYPE"]); 
	$rs_cancel_date				= trim($arr_order_rs[0]["CANCEL_DATE"]); 
	$rs_delivery_type			= trim($arr_order_rs[0]["DELIVERY_TYPE"]); 
	$rs_delivery_date			= trim($arr_order_rs[0]["DELIVERY_DATE"]); 
	$rs_finish_date				= trim($arr_order_rs[0]["FINISH_DATE"]); 

	$rs_r_mem_nm					= trim($arr_order_rs[0]["R_MEM_NM"]);
	$rs_r_zipcode					= trim($arr_order_rs[0]["R_ZIPCODE"]);
	$rs_r_addr1						= trim($arr_order_rs[0]["R_ADDR1"]);
	$rs_r_addr2						= trim($arr_order_rs[0]["R_ADDR2"]);
	$rs_r_phone						= trim($arr_order_rs[0]["R_PHONE"]);
	$rs_r_hphone					= trim($arr_order_rs[0]["R_HPHONE"]);
	$rs_memo							= trim($arr_order_rs[0]["MEMO"]);

    $rs_opt_stck_l1				= trim($arr_order_rs[0]["OPT_STCK_L1"]);
	$rs_opt_stck_l2				= trim($arr_order_rs[0]["OPT_STCK_L2"]);
	$rs_opt_stck_l3				= trim($arr_order_rs[0]["OPT_STCK_L3"]);
	$rs_opt_stck_size			= trim($arr_order_rs[0]["OPT_STCK_SIZE"]);
	$rs_opt_wrapping_paper= trim($arr_order_rs[0]["OPT_WRAPPING_PAPER"]);
	$rs_opt_print_text    = trim($arr_order_rs[0]["OPT_PRINT_TEXT"]);
	$rs_opt_stck_outbox_tf= trim($arr_order_rs[0]["OPT_STCK_OUTBOX_TF"]);
	$rs_opt_ready_date		= trim($arr_order_rs[0]["OPT_READY_DATE"]);
	$rs_opt_manager_no		= trim($arr_order_rs[0]["OPT_MANAGER_NO"]);

	$arr_mem_rs = selectMember($conn, $rs_mem_no);
	$rs_mem_id						= trim($arr_mem_rs[0]["MEM_ID"]); 
	$rs_mem_nm						= trim($arr_mem_rs[0]["MEM_NM"]); 
	$rs_email							= trim($arr_mem_rs[0]["EMAIL"]); 
	$rs_zipcode						= trim($arr_mem_rs[0]["ZIPCODE"]); 
	$rs_addr1							= trim($arr_mem_rs[0]["ADDR1"]); 
	$rs_addr2							= trim($arr_mem_rs[0]["ADDR2"]); 
	$rs_phone							= trim($arr_mem_rs[0]["PHONE"]); 
	$rs_hphone						= trim($arr_mem_rs[0]["HPHONE"]); 
	
	$real_mem_type = getMemberType($conn, $rs_mem_no);

	$order_date = date("Y-m-d",strtotime("0 month"));
	
//	$arr_rs = listManagerOrderGoods($conn, $reserve_no, $mem_no, $use_tf, $del_tf);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
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
      changeYear: true
    });
  });
</script>
<script>

    function sample6_execDaumPostcode() {
        new daum.Postcode({
            oncomplete: function(data) {

                // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.
                // 각 주소의 노출 규칙에 따라 주소를 조합한다.
                // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
                var fullAddr = ''; // 최종 주소 변수
                var extraAddr = ''; // 조합형 주소 변수

                // 사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
                if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
                    fullAddr = data.roadAddress;

                } else { // 사용자가 지번 주소를 선택했을 경우(J)
                    fullAddr = data.jibunAddress;
                }

                // 사용자가 선택한 주소가 도로명 타입일때 조합한다.
                if(data.userSelectedType === 'R'){
                    //법정동명이 있을 경우 추가한다.
                    if(data.bname !== ''){
                        extraAddr += data.bname;
                    }
                    // 건물명이 있을 경우 추가한다.
                    if(data.buildingName !== ''){
                        extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                    }
                    // 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
                    fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
                }
								
								if (document.getElementById("addr_type").value == "s") {
								  // 우편번호와 주소 정보를 해당 필드에 넣는다.
									document.getElementById("o_zipcode").value = data.zonecode;
									//document.getElementById("cp_zip").value = data.postcode2;
									document.getElementById("o_addr1").value = fullAddr;
									// 커서를 상세주소 필드로 이동한다.
									document.getElementById("o_addr1").focus();
								} else {
								  // 우편번호와 주소 정보를 해당 필드에 넣는다.
									document.getElementById("r_zipcode").value = data.zonecode;
									//document.getElementById("re_zip").value = data.postcode2;
									document.getElementById("r_addr1").value = fullAddr;
									// 커서를 상세주소 필드로 이동한다.
									document.getElementById("r_addr1").focus();
								}


            }
        }).open();
    }

		function js_addr_open(s) {
			document.getElementById("addr_type").value = s;
			sample6_execDaumPostcode();
		}

</script> 

<script language="javascript">

	// tag 관련 레이어가 다 로드 되기전인지 판별하기 위해 필요
	var tag_flag = "0";

	var checkFirst = false;
	var lastKeyword = '';
	var loopSendKeyword = false;
	
	function startSuggest() {
		
		if ((event.keyCode == 8) || (event.keyCode == 46)) {
			checkFirst = false;
			loopSendKeyword = false;
		}

		if (checkFirst == false) {
			setTimeout("sendKeyword();", 100);
			loopSendKeyword = true;
		}
		checkFirst = true;
	}

	function sendKeyword() {
		
		var frm = document.frm;

		if (loopSendKeyword == false) return;

		var keyword = document.frm.o_mem_name.value;
		
		if (keyword == '') {
			
			lastKeyword = '';
		
			hide('suggest');

		} else if (keyword != lastKeyword) {

			lastKeyword = keyword;
				
			if (keyword != '') {
				
				frm.keyword.value = keyword;
				frm.action = "/_common/search_member.php";
				frm.target = "ifr_hidden";
				frm.submit();
				
				//var params = "keyword="+encodeURIComponent(keyword);
				//var params = "keyword="+keyword;
				//alert(params);
				//sendRequest("search_dept.asp", params, displayResult, 'POST');

			} else {
				hide('suggest');
			}
		}
		setTimeout("sendKeyword();", 100);
	}

	function displayResult(str) {
		
//		if (httpRequest.readyState == 4) {
//			if (httpRequest.status == 200) {
				
		var resultText = str;
		
		var result = resultText.split('|');

		var count = parseInt(result[0]);

		var keywordList = null;
		var arr_keywordList = null;

		if (count > 0) {
					
			keywordList = result[1].split('^');
			
			var html = '';
					
			for (var i = 0 ; i < keywordList.length ; i++) {
						
				arr_keywordList = keywordList[i].split('%');
				
				html += "<table width='50%' border='0'><tr><a href=\"javascript:js_select('"+arr_keywordList[0]+"','"+arr_keywordList[1]+"')\">"+arr_keywordList[1]+" "+arr_keywordList[2]+"</a></td></tr></table>";
		
				//alert(html);
			}

			var listView = document.getElementById('suggestList');
			listView.innerHTML = html;
					
			suggest.style.visibility  ="visible"; 
		} else {
			suggest.style.visibility  ="hidden"; 
		}
	}

	function js_select(selectedKey,selectedKeyword) {
		
		var frm = document.frm;

		//frm.o_mem_name.value = selectedKeyword;
		
		arr_keywordValues = selectedKey.split('&');

		frm.o_mem_name.value	= arr_keywordValues[0];
		frm.o_email.value			= arr_keywordValues[1];
		frm.o_phone.value			= arr_keywordValues[2];
		frm.o_hphone.value		= arr_keywordValues[3];
		frm.o_zipcode.value		= arr_keywordValues[4];
		frm.o_addr1.value			= arr_keywordValues[5];
		
		loopSendKeyword = false;
		checkFirst = false;
		hide('suggest');

		//document.getElementById('goods_detail').src = "order_goods_detail.php?goods_no="+frm.goods_no.value+"&cp_no=<?=$cp_no?>&mode=S";

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


	function js_list() {
		var frm = document.frm;
		
		frm.method = "get";
		frm.action = "order_list.php";
		frm.submit();
	}

	function js_view(rn, order_no) {

		var frm = document.frm;
		
		frm.order_no.value = order_no;
		frm.mode.value = "S";
		frm.target = "";
		frm.method = "get";
		frm.action = "order_write.php";
		frm.submit();
		
	}

	// 조회 버튼 클릭 시 
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

function js_toggle(order_no, use_tf) {
	var frm = document.frm;

	bDelOK = confirm('공개 여부를 변경 하시겠습니까?');
		
	if (bDelOK==true) {

		if (use_tf == "Y") {
			use_tf = "N";
		} else {
			use_tf = "Y";
		}

		frm.order_no.value = order_no;
		frm.use_tf.value = use_tf;
		frm.mode.value = "T";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}
}

function js_cp_type() {

	var frm = document.frm;
	var cp_no = frm.old_cp_type.value;

	if (cp_no != "") {
		
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	} else {
		frm.old_cp_type.value = frm.cp_type.value;
	}
}

function js_save() {
	var frm = document.frm;

	if (frm.cp_type.value =="") {
		alert("업체를 선택하세요.");
		frm.cp_type.focus();
		return;
	}

	if (frm.o_mem_name.value.trim() == "") {
		alert("주문자명을 입력하세요.");
		frm.o_mem_name.focus();
		return;
	}

	if (frm.o_phone.value.trim() == "") {
		alert("연락처를 입력하세요.");
		frm.o_phone.focus();
		return;
	}
	
//	if (frm.o_hphone.value.trim() == "") {
//		alert("휴대전화번호를 입력하세요.");
//		frm.o_hphone.focus();
//		return;
//	}


	if (frm.o_addr1.value.trim() == "") {
		alert("주소를 입력하세요.");
		frm.o_addr1.focus();
		return;
	}

	if (frm.r_mem_name.value.trim() == "") {
		alert("수령자명을 입력하세요.");
		frm.r_mem_name.focus();
		return;
	}

	if (frm.r_phone.value.trim() == "") {
		alert("연락처를 입력하세요.");
		frm.r_phone.focus();
		return;
	}

//	if (frm.r_hphone.value.trim() == "") {
//		alert("휴대전화번호를 입력하세요.");
//		frm.r_hphone.focus();
//		return;
//	}

	if (frm.r_addr1.value.trim() == "") {
		alert("주소를 입력하세요.");
		frm.r_addr1.focus();
		return;
	}

/*
	if (frm.total_qty.value <= 0) {
		alert("주문하실 상품을 추가해 주세요.");
		return;
	}


	if (frm.delivery_type.value == "") {
		alert("배송방식를 선택하세요.");
		frm.delivery_type.focus();
		return;
	}

	if (frm.bank_pay_account.value == "") {
		alert("입금은행를 선택하세요.");
		frm.bank_pay_account.focus();
		return;
	}

	if (frm.cms_depositor.value.trim() == "") {
		alert("입금자를 입력하세요.");
		frm.cms_depositor.focus();
		return;
	}
*/

	//alert("2");
	frm.mode.value = "I";
	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();

	// 조건더 넣는다..
}



function js_same() {
	
	var f = document.frm;
	
	if (f.same.checked == true) {
		f.r_mem_name.value = f.o_mem_name.value;
		f.r_email.value = f.o_email.value;
		f.r_phone.value = f.o_phone.value;
		f.r_hphone.value = f.o_hphone.value;
		f.r_zipcode.value = f.o_zipcode.value;
		f.r_addr1.value = f.o_addr1.value;
	} else {
		f.r_mem_name.value = "";
		f.r_email.value = "";
		f.r_phone.value = "";
		f.r_hphone.value = "";
		f.r_zipcode.value = "";
		f.r_addr1.value = "";
	}
}

//우편번호 찾기
function js_post(zip, addr) {
	var url = "/_common/common_post.php?zip="+zip+"&addr="+addr;
	NewWindow(url, '우편번호찾기', '390', '370', 'NO');
}


function js_cal() {

	var frm = document.frm;
	var temp_price = eval(frm.total_sale_price.value);
	frm.disply_total_sale_price.value = numberFormat(temp_price);

}


function s_test() {
	frm.mode.value = "I";
	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();
}

</script>
</head>

<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="rn" value="">
<input type="hidden" name="depth" value="" />
<input type="hidden" name="order_no" value="">
<input type="hidden" name="use_tf" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>">

<input type="hidden" name="search_field" value="<?=$search_field?>">
<input type="hidden" name="search_str" value="<?=$search_str?>">

<input type="hidden" name="keyword" value="">
<input type="hidden" name="send_data" value="">
<input type="hidden" name="addr_type" id="addr_type" value="">

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
	include_once('../../_common/editor/func_editor.php');

?>

		</td>
		<td class="contentarea">

			<!-- S: mwidthwrap -->
			<div id="mwidthwrap">
				<h2>주문 관리</h2>  

				* 주문자 정보
				<div class="sp5"></div>
				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="120" />
					<col width="*" />
					<col width="120" />
					<col width="*" />
				</colgroup>
				<thead>
					<tr>
						<th>주문업체</th>
						<td>
							<input type="text" class="seller" style="width:210px" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'판매',$cp_type)?>" />
							<script>
							$(function() {
						     var cache = {};
								$( ".seller" ).autocomplete({
									source: function( request, response ) {
										var term = request.term;
										if ( term in cache ) {
											response( cache[term] );
											return;
										}
						 
										$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('판매'), request, function( data, status, xhr ) {
											cache[term] = data;
											response(data);
										});
									},
									minLength: 2,
									select: function( event, ui ) {
										$(".seller").val(ui.item.value);
										$("input[name=cp_type]").val(ui.item.id);

										$.getJSON( "../company/json_company_list.php?cp_no=" + ui.item.id, function( data, status, xhr ) {
											
											$.each(data, function(i, field){
													$("input[name=o_mem_name]").val(field.MANAGER_NM);
													$("input[name=o_email]").val(field.EMAIL);
													$("input[name=o_phone]").val(field.PHONE);
													$("input[name=o_hphone]").val(field.HPHONE);
													$("input[name=o_zipcode]").val(field.RE_ZIP);
													$("input[name=o_addr1]").val(field.RE_ADDR);
											});

										});
									}
								}).bind( "blur", function( event ) {
									var cp_no = $("input[name=cp_type]").val();
									if(cp_no != '') {
										$.getJSON( "../company/json_company_list.php?cp_no=" + cp_no, function(data) {
											if(data[0].CP_NO == 'undefined') {
												$("input[name=cp_type]").val('');
											} else {
												if(data[0].COMPANY != $(".seller").val())
												{

													$(".seller").val();
													$("input[name=cp_type]").val('');
												}
											}
										});
									} 
								});
							});
							</script>
							<input type="hidden" name="cp_type" value="<?=$cp_type?>">
							<input type="hidden" name="old_cp_type" value="<?=$cp_type?>">
						</td>
						<th>주문일</th>
						<td>
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="order_date" value="<?=$order_date?>" maxlength="10"/>
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>주문자명</th>
						<td style="position:relative">
							<div id="suggest" style="position:absolute; left:15px; top:25px; width:95%; height:81px; z-index:0; visibility: hidden; width:95%; ">
							<div id="suggestList" style="position:relative; border:solid 1px #cec6ba; background:#FFFFFF; width:95%;"></div>
							</div>
							<input type="text" class="txt" style="width:35%" name="o_mem_name" required value="<?=$rs_o_mem_name?>" onKeyDown="startSuggest();" />
						</td>
						<th>이메일</th>
						<td>
							<input type="text" class="txt" style="width:35%" name="o_email" value="<?=$rs_o_email?>" />
						</td>
					</tr>
					<tr>
						<th>연락처</th>
						<td>
							<input type="text" class="txt" style="width:120px" name="o_phone" value="<?=$rs_o_phone?>" required onkeyup="return isPhoneNumber(this)" />
						</td>
						<th>휴대전화번호</th>
						<td>
							<input type="text" class="txt" style="width:120px" name="o_hphone" value="<?=$rs_o_hphone?>" onkeyup="return isPhoneNumber(this)" />
						</td>
					</tr>
					<tr>
						<th>주소</th>
						<td colspan = "3">
								<input type="Text" name="o_zipcode" id="o_zipcode" value="<?= $rs_o_zipcode?>" style="width:60px;" maxlength="7" class="txt" onkeyup="return isPhoneNumber(this)">
								<input type="Text" name="o_addr1" id="o_addr1" value="<?= $rs_o_addr1?>" style="width:65%;" class="txt">
								<a href="#none" onClick="js_addr_open('s');"><img src="/manager/images/admin/btn_filesch.gif" alt="찾기" align="absmiddle" /></a>
						</td>
					</tr>
				</tbody>
				</table>
				<div class="sp15"></div>
					* 수령자 정보
				<div class="sp5"></div>
				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="120" />
					<col width="*" />
					<col width="120" />
					<col width="*" />
				</colgroup>
				<thead>
					<tr>
						<th>&nbsp;</th>
						<td colspan = "3">
							<input type="checkbox" class="chk" id="same" name="same" onclick="javascript:js_same();"> 주문자 정보와 동일한 경우 체크 하세요.
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>수령자명</th>
						<td>
							<input type="text" class="txt" style="width:35%" name="r_mem_name" required value="<?=$rs_r_mem_name?>" />
						</td>
						<th>이메일</th>
						<td>
							<input type="text" class="txt" style="width:35%" name="r_email" value="<?=$rs_r_email?>" />
						</td>
					</tr>
					<tr>
						<th>연락처</th>
						<td>
							<input type="text" class="txt" style="width:120px" name="r_phone" value="<?=$rs_r_phone?>" required onkeyup="return isPhoneNumber(this)" />
						</td>
						<th>휴대전화번호</th>
						<td>
							<input type="text" class="txt" style="width:120px" name="r_hphone" value="<?=$rs_r_hphone?>" onkeyup="return isPhoneNumber(this)" />
						</td>
					</tr>
					<tr>
						<th>주소</th>
						<td colspan = "3">
								<input type="Text" name="r_zipcode" id="r_zipcode" value="<?= $rs_r_zipcode?>" style="width:60px;" maxlength="7" class="txt" onkeyup="return isPhoneNumber(this)">
								<input type="Text" name="r_addr1" id="r_addr1" value="<?= $rs_r_addr1?>" style="width:65%;" class="txt">
								<a href="#none" onClick="js_addr_open('r');"><img src="/manager/images/admin/btn_filesch.gif" alt="찾기" align="absmiddle" /></a>
						</td>
					</tr>
				</tbody>
				</table>
				
				<div class="sp15"></div>
					* 추가정보
				<div class="sp5"></div>
				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="120" />
					<col width="*" />
					<col width="120" />
					<col width="*" />
				</colgroup>
				<tbody>
					<tr>
					<th>스티커</th>
					<td class="line" colspan="3"> 
							<?= makeCategorySelectBoxOnChange($conn, '05');?>
							<script>
							$(function(){
								$("select[name='gd_cate_01']").hide();
							});
							</script>
							<?= makeSelectBox($conn,"STICKER_SIZE","opt_stck_size","70","사이즈","",$rs_opt_stck_size) ?>
					   
					</td>
					
				</tr>
				<tr>
				  <th>아웃박스 스티커</th>
					<td> <?= makeSelectBox($conn,"OUTBOX_STICKER_TF","opt_stck_outbox_tf","150","선택","",$rs_opt_stck_outbox_tf) ?></td>
					<th>아웃박스 수량</th>
					<td class="line">
							<input type="text" class="txt" style="width:120px;" name="opt_outbox_cnt" value="<?=$rs_opt_outbox_cnt?>"/>
					</td>
				</tr>
				<tr>
					<th>포장지</th>
					<td colspan="3" class="line">
							<input type="text" class="wrapping_paper" style="width:210px" name="txt_opt_wrapping_paper" value="<?=getGoodsAutocompleteTextBox($conn, '0304' ,$rs_opt_wrapping_paper)?>" />
							<script>
							$(function() {
								$( ".wrapping_paper" ).autocomplete({
									source: function( request, response ) {
										$.getJSON( "../goods/json_goods_list.php?category=0304", request, function( data, status, xhr ) {
											response(data);
										});
									},
									minLength: 2,
									select: function( event, ui ) {
										$(".wrapping_paper").val(ui.item.value);
										$("input[name=opt_wrapping_paper]").val(ui.item.id);
									}
								}).data('ui-autocomplete')._renderItem = function( ul, item ) {
										var pic_path = "<img src='" + item.label.split("|")[0] + "' width='50' height='50' border='0'/>";  
										return $( "<li></li>" )
										.data( "item.autocomplete", item )
										.append("<a>" + pic_path +  item.label.split("|")[1]  + "</a>")
										.appendTo( ul );
								};
							//		.bind( "focus", function( event ) {
							//			$(this).val('');
							//			$("input[name=cp_type]").val('');
							//	});
							});
							</script>
							<input type="hidden" name="opt_wrapping_paper" value="<?=$rs_opt_wrapping_paper?>"/>
					</td>
				</tr>
				<tr>
					<th>인쇄</th>
					<td class="line" colspan="3">
							<input type="text" class="txt" style="width:90%" name="opt_print_text" value="<?=$rs_opt_print_text?>"/>
					</td>
				</tr>
				<tr>
					<th>출고일</th>
					<td class="line">
						<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="opt_ready_date" value="<?=$rs_opt_ready_date?>" maxlength="10"/>
					</td>
					<th>담당영업사원</th>
					<td class="line">
						<?= makeAdminInfoByMDSelectBox($conn,"opt_manager_no","70","선택","",$rs_opt_manager_no) ?>
				  </td>
				</tr>
				<tr>
						<th>배송방식</th>
						<td colspan="3">
							<?=makeSelectBox($conn,"DELIVERY_TYPE", "delivery_type","200", "배송방법을 선택하세요", "", $rs_delivery_type)?>
						</td>
				</tr>
				<tr>
						<th>주문메모</th>
						<td colspan="3">
							<textarea name="memo" style="width:98%; height:50px" class="txt"></textarea>
						</td>
					</tr>
				</tbody>
				</table>
				
				<div class="sp15"></div>
				* 주문 상품 정보
				<div class="sp5"></div>
				<iframe name='goods_list' id='goods_list' width='100%' height='150' noresize scrolling='no' frameborder='0' marginheight='0' marginwidth='0' src="order_goods_list.php"></iframe>
				<div class="sp15"></div>
				
				<!--결제 정보-->
				<input type='hidden' name='disply_total_sale_price' value='<?=$rs_total_sale_price?>'>
				<input type="hidden" name="total_sale_price" value="<?=$rs_total_sale_price?>"/>
				<input type="hidden" name="total_qty" value="0"/>
				<input type='hidden' name='pay_type' value=''>
				<input type="hidden" name="bank_pay_account" value='<?=$rs_bank_pay_account?>'/>
				<input type='hidden' name='cms_depositor' value='<?=$rs_cms_depositor?>'>
				
				<!--
				* 결제 정보
				<div class="sp5"></div>
				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="120" />
					<col width="*" />
					<col width="120" />
					<col width="*" />
					<col width="120" />
					<col width="*" />
				</colgroup>
				<thead>
					<tr>
						<th>총결재금액</th>
						<td>
							<input type="text" class="txt" style="width:35%; text-align:right" name="disply_total_sale_price" value="<?=$rs_total_sale_price?>" readonly="1"/>
							<input type="hidden" name="total_sale_price" value="<?=$rs_total_sale_price?>"/>
							<input type="hidden" name="total_qty" value="0"/>
						</td>
						<th>배송방식</th>
						<td colspan="3">
							<?=makeSelectBox($conn,"DELIVERY_TYPE", "delivery_type","200", "배송방법을 선택하세요", "", $rs_delivery_type)?>
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>결제방식</th>
						<td>
							<? $rs_pay_type = "BANK";?>
							<?=makeRadioBox($conn,"PAY_TYPE", "pay_type" ,$rs_pay_type)?>
						</td>
						<th>입금은행</th>
						<td>
							<?=makeSelectBox($conn,"ACCOUNT_BANK","bank_pay_account","250","입금할 은행을 선택하세요.","",$rs_bank_pay_account)?>
						</td>
						<th>입금자명</th>
						<td>
							<input type="text" class="txt" style="width:35%" name="cms_depositor" value="<?=$rs_cms_depositor?>" />
						</td>
					</tr>
					<tr>
						<th>주문메모</th>
						<td colspan="5">
							<textarea name="memo" style="width:98%; height:50px" class="txt"></textarea>
						</td>
					</tr>
				</tbody>
				</table>
				-->

				<div class="btnright">
				<? if ($adm_no <> "" ) {?>
					<? if ($sPageRight_U == "Y") {?>
          <a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
					<? } ?>
				<? } else {?>
					<? if ($sPageRight_I == "Y") {?>
          <a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
					<? } ?>
				<? }?>

          <a href="javascript:js_list();"><img src="../images/admin/btn_list.gif" alt="목록" /></a>
				<? if ($adm_no <> "") {?>
					<? if ($sPageRight_D == "Y") {?>
          <a href="javascript:js_delete();"><img src="../images/admin/btn_delete.gif" alt="삭제" /></a>
					<? } ?>
				<? } ?>
        </div>      
      </div>
      <!-- // E: mwidthwrap -->

    </td>
  </tr>
  </table>
</div>
<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
<form name='frm_print' method='post'>
<input type='hidden' name='frm_body' value=''>
<input type='hidden' name='frm_header' value=''>
<input type='hidden' name='frm_footer' value=''>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>