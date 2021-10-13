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
	
	$menu_right = "OD023"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/company/company.php";

	if ($mode == "I") {

		//updateBoardUseTF($conn, $use_tf, $s_adm_no, $bb_code, $bb_no);
		// 신규 주문 번호 생성
		$new_reserve_no = getReservNo($conn,"EN");

		$s_ord_no = get_session('s_ord_no');
		//echo "주문번호".$s_ord_no;

		// 2017-07-27 맴버 등록 기능 삭제
		$new_mem_no = 0;
		
		//echo $s_ord_no;

		$arr_rs_cart = listCart($conn, $s_ord_no, $cp_type, $use_tf, $del_tf, "DESC");

		$nCnt = 0;
		$TOTAL_PRICE = 0;
		$TOTAL_BUY_PRICE = 0;
		$TOTAL_SALE_PRICE = 0;
		$TOTAL_EXTRA_PRICE = 0;
		$TOTAL_DELIVERY_PRICE = 0;
		$TOTAL_QTY = 0;
		
		if (sizeof($arr_rs_cart) > 0) {

			for ($j = 0 ; $j < sizeof($arr_rs_cart); $j++) {
				
				$CART_NO					= SetStringToDB($arr_rs_cart[$j]["CART_NO"]);
				$ON_UID						= SetStringToDB($arr_rs_cart[$j]["ON_UID"]);
				$CP_ORDER_NO				= SetStringToDB($arr_rs_cart[$j]["CP_ORDER_NO"]);
				$BUY_CP_NO					= SetStringToDB($arr_rs_cart[$j]["BUY_CP_NO"]);
				$GOODS_NO					= SetStringToDB($arr_rs_cart[$j]["GOODS_NO"]);
				$GOODS_CODE					= SetStringToDB($arr_rs_cart[$j]["GOODS_CODE"]);
				$GOODS_NAME					= SetStringToDB($arr_rs_cart[$j]["GOODS_NAME"]);
				$QTY						= SetStringToDB($arr_rs_cart[$j]["QTY"]);
				$BUY_PRICE					= SetStringToDB($arr_rs_cart[$j]["BUY_PRICE"]);
				$PRICE						= SetStringToDB($arr_rs_cart[$j]["PRICE"]);
				$SALE_PRICE					= SetStringToDB($arr_rs_cart[$j]["SALE_PRICE"]);
				$EXTRA_PRICE				= SetStringToDB($arr_rs_cart[$j]["EXTRA_PRICE"]);
				$DELIVERY_PRICE				= SetStringToDB($arr_rs_cart[$j]["DELIVERY_PRICE"]);
				$SA_DELIVERY_PRICE			= SetStringToDB($arr_rs_cart[$j]["SA_DELIVERY_PRICE"]);
				$DISCOUNT_PRICE				= SetStringToDB($arr_rs_cart[$j]["DISCOUNT_PRICE"]);

				//카트에 대한 여분필드
				$C_CATE_01					= SetStringToDB($arr_rs_cart[$j]["C_CATE_01"]); //샘플, 증정, 일반("")
				$C_CATE_02					= SetStringToDB($arr_rs_cart[$j]["C_CATE_02"]); //계산서번호
				$C_CATE_03					= SetStringToDB($arr_rs_cart[$j]["C_CATE_03"]); //계산서종류
				$C_CATE_04					= SetStringToDB($arr_rs_cart[$j]["C_CATE_04"]);

				//if($C_CATE_01 <> "") //증정이나 샘플이라면 판매가를 -로
				//	$SALE_PRICE = -$SALE_PRICE;

				//상품에 대한 여분필드 - 저장안함
				$cate_01					= SetStringToDB($arr_rs_cart[$j]["CATE_01"]); 
				$cate_02					= SetStringToDB($arr_rs_cart[$j]["CATE_02"]);
				$cate_03					= SetStringToDB($arr_rs_cart[$j]["CATE_03"]);
				$cate_04					= SetStringToDB($arr_rs_cart[$j]["CATE_04"]); 

				$OPT_STICKER_NO				= SetStringToDB($arr_rs_cart[$j]["OPT_STICKER_NO"]);
				$OPT_OUTBOX_TF				= SetStringToDB($arr_rs_cart[$j]["OPT_OUTBOX_TF"]);
				$DELIVERY_CNT_IN_BOX		= SetStringToDB($arr_rs_cart[$j]["DELIVERY_CNT_IN_BOX"]);
				$OPT_WRAP_NO				= SetStringToDB($arr_rs_cart[$j]["OPT_WRAP_NO"]);
				$OPT_PRINT_MSG				= SetStringToDB($arr_rs_cart[$j]["OPT_PRINT_MSG"]);
				$OPT_OUTSTOCK_DATE			= SetStringToDB($arr_rs_cart[$j]["OPT_OUTSTOCK_DATE"]);
				$OPT_MEMO					= SetStringToDB($arr_rs_cart[$j]["OPT_MEMO"]);
				$OPT_REQUEST_MEMO			= SetStringToDB($arr_rs_cart[$j]["OPT_REQUEST_MEMO"]);
				$OPT_SUPPORT_MEMO			= SetStringToDB($arr_rs_cart[$j]["OPT_SUPPORT_MEMO"]);
				$DELIVERY_TYPE				= SetStringToDB($arr_rs_cart[$j]["DELIVERY_TYPE"]);
				$DELIVERY_CP				= SetStringToDB($arr_rs_cart[$j]["DELIVERY_CP"]);
				$SENDER_NM					= SetStringToDB($arr_rs_cart[$j]["SENDER_NM"]);
				$SENDER_PHONE				= SetStringToDB($arr_rs_cart[$j]["SENDER_PHONE"]);

				$STICKER_PRICE				= SetStringToDB($arr_rs_cart[$j]["STICKER_PRICE"]); 
				$PRINT_PRICE				= SetStringToDB($arr_rs_cart[$j]["PRINT_PRICE"]); 
				$SALE_SUSU					= SetStringToDB($arr_rs_cart[$j]["SALE_SUSU"]);
				$OPT_STICKER_MSG			= SetStringToDB($arr_rs_cart[$j]["OPT_STICKER_MSG"]); 
				$LABOR_PRICE				= SetStringToDB($arr_rs_cart[$j]["LABOR_PRICE"]); 
				$OTHER_PRICE				= SetStringToDB($arr_rs_cart[$j]["OTHER_PRICE"]); 

				// 사입건에 대한 부분 확인 하자
				$SUM_PRICE = $QTY * $PRICE;
				$SUM_BUY_PRICE = $QTY * $BUY_PRICE;
				/*
				if($C_CATE_01 <> "") //증정이나 샘플이라면 판매가를 -로, 2016-12-21 샘플, 증정 주문서 금액에 다시 추가
					$SUM_SALE_PRICE = 0;
				else
				*/
				$SUM_SALE_PRICE = $QTY * $SALE_PRICE;

				$SUM_EXTRA_PRICE = $QTY * $EXTRA_PRICE;

				$TOTAL_QTY = $TOTAL_QTY + $QTY;
				$TOTAL_PRICE = $TOTAL_PRICE + $SUM_PRICE;
				$TOTAL_BUY_PRICE = $TOTAL_BUY_PRICE + $SUM_BUY_PRICE;
				$TOTAL_SALE_PRICE = $TOTAL_SALE_PRICE + $SUM_SALE_PRICE;
				$TOTAL_EXTRA_PRICE = $TOTAL_EXTRA_PRICE + $SUM_EXTRA_PRICE;
				//$TOTAL_DELIVERY_PRICE = $TOTAL_DELIVERY_PRICE + $DELIVERY_PRICE;
				$TOTAL_SA_DELIVERY_PRICE = $TOTAL_SA_DELIVERY_PRICE + $SA_DELIVERY_PRICE;
				$TOTAL_DISCOUNT_PRICE = $TOTAL_DISCOUNT_PRICE + $DISCOUNT_PRICE;

				$order_state		= "1"; //입금완료처리 - giftnet
				$use_tf					= "Y";

				$arr_rs_goods = selectGoods($conn, $GOODS_NO);
				$TAX_TF				= trim($arr_rs_goods[0]["TAX_TF"]); 
	
				$memos = array('opt_request_memo' => $OPT_REQUEST_MEMO, 'opt_support_memo' => $OPT_SUPPORT_MEMO);

				$result = insertOrderGoods($conn, $ON_UID, $new_reserve_no, $CP_ORDER_NO, $BUY_CP_NO, $new_mem_no, $j, $GOODS_NO, $GOODS_CODE, $GOODS_NAME, $goods_sub_name, $QTY, $OPT_STICKER_NO, $OPT_STICKER_MSG, $OPT_OUTBOX_TF, $DELIVERY_CNT_IN_BOX, $OPT_WRAP_NO, $OPT_PRINT_MSG, $OPT_OUTSTOCK_DATE, $OPT_MEMO, $memos, $DELIVERY_TYPE, $DELIVERY_CP, $SENDER_NM, $SENDER_PHONE, $C_CATE_01, $C_CATE_02, $C_CATE_03, $C_CATE_04, $PRICE, $BUY_PRICE, $SALE_PRICE, $EXTRA_PRICE, $DELIVERY_PRICE, $SA_DELIVERY_PRICE, $DISCOUNT_PRICE, $STICKER_PRICE, $PRINT_PRICE, $SALE_SUSU, $LABOR_PRICE, $OTHER_PRICE, $TAX_TF, $order_state, $use_tf, $s_adm_no);

			}
		}
		

		$result = insertOrder($conn, $ON_UID, $new_reserve_no, $new_mem_no, $cp_type, $o_mem_name, $o_zipcode, $o_addr1, $o_addr2, $o_phone, $o_hphone, $o_email, $r_mem_name, $r_zipcode, $r_addr1, $r_addr2, $r_phone, $r_hphone, $r_email, $memo, $bulk_tf, $opt_manager_no, $order_state, $TOTAL_PRICE, $TOTAL_BUY_PRICE, $TOTAL_SALE_PRICE, $TOTAL_EXTRA_PRICE, $TOTAL_DELIVERY_PRICE, $TOTAL_SA_DELIVERY_PRICE, $TOTAL_DISCOUNT_PRICE, $TOTAL_QTY, $pay_type, $delivery_type, $use_tf, $s_adm_no);

		$query_date = "UPDATE TBL_ORDER SET ORDER_DATE = '$order_date' WHERE RESERVE_NO = '$new_reserve_no' ";
		mysql_query($query_date,$conn);

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
	$nPageSize		= trim($nPageSize);

	$reserve_no			= trim($reserve_no);
	$sel_order_state	= trim($sel_order_state);
	$sel_pay_type		= trim($sel_pay_type);
	$start_date			= trim($start_date);
	$end_date			= trim($end_date);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
	$del_tf = "N";
	
#===============================================================
# Get Search list count
#===============================================================

	if ($s_adm_cp_type <> "운영") { 
		$cp_type = $s_adm_com_code;
		$arr_company = selectCompany($conn, $cp_type);
		$rs_cp_nm		= $arr_company[0]["CP_NM"];
		$rs_cp_nm2		= $arr_company[0]["CP_NM2"];
		$rs_o_mem_name	= $arr_company[0]["MANAGER_NM"];
		$rs_o_email		= $arr_company[0]["EMAIL"];
		$rs_o_phone		= $arr_company[0]["PHONE"];
		$rs_o_hphone	= $arr_company[0]["HPHONE"];
		$rs_o_zipcode	= $arr_company[0]["CP_ZIP"];
		$rs_o_addr1		= $arr_company[0]["CP_ADDR"];
		$SALE_ADM_NO	= $arr_company[0]["SALE_ADM_NO"];
		

	}

	$order_date = date("Y-m-d H:i:s",strtotime("0 month"));
	

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
<script type="text/javascript" src="../jquery/jquery-ui-timepicker-addon.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui-timepicker-addon.css" type="text/css" />
<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script>
<script>
  $(function() {

	 $('.datepicker').datetimepicker({
	  dateFormat: "yy-mm-dd", 
	  timeFormat: "HH:mm:ss",
	  buttonImage: "/manager/images/calendar/cal.gif",
	  buttonImageOnly: true,
	  buttonText: "Select date",
	  showOn: "both",
	  changeMonth: true,
	  changeYear: true
     });
  });
</script>
<script>
	$(function() {
		$('.autocomplete_off').attr('autocomplete', 'off');
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

		arr_keywordValues = selectedKey.split('&');

		frm.o_mem_name.value	= arr_keywordValues[0];
		frm.o_email.value		= arr_keywordValues[1];
		frm.o_phone.value		= arr_keywordValues[2];
		frm.o_hphone.value		= arr_keywordValues[3];
		frm.o_zipcode.value		= arr_keywordValues[4];
		frm.o_addr1.value		= arr_keywordValues[5];
		
		loopSendKeyword = false;
		checkFirst = false;
		hide('suggest');

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

		if (frm.opt_manager_no.value == "") {
			alert("영업담당자를 입력하세요.");
			return;
		}

	/*
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

		frm.mode.value = "I";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

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

	$(function(){

		$("input[name=o_mem_name]").focusout(function(){

			$("#suggestList").css("visibility","hidden");

		});

		$("[name=chk_phone_switcher]").change(function(){
								
			var view_phone = $("[name=o_phone]").val();
			var hidden_phone = $("[name=phone]").val();

			$("[name=o_phone]").val(hidden_phone);
			$("[name=phone]").val(view_phone);

			if($(".phone_label").html() == "담당자번호로") { 
				$(".phone_label").html('대표번호로');
			} else { 
				$(".phone_label").html('담당자번호로');
			}

		});

	});

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
							<? if ($s_adm_cp_type <> "운영") { ?>
								<?=$rs_cp_nm." ".$rs_cp_nm2 ?>
							<? } else { ?>
								<input type="text" class="autocomplete_off" style="width:90%" placeholder="업체(명/코드) 입력후 엔터를 눌러주세요" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'',$cp_type)?>" />
								<input type="hidden" name="cp_type" value="<?=$cp_type?>">
								<input type="hidden" name="old_cp_type" value="<?=$cp_type?>">

								<script>
									$(function(){

										$("input[name=txt_cp_type]").keydown(function(e){

											if(e.keyCode==13) { 

												var keyword = $(this).val();
												if(keyword == "") { 
													$("input[name=cp_type]").val('');
												} else { 
													$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('판매,판매공급') +"&term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
														if(data.length == 1) { 
															
															js_selecting_company("txt_cp_type", data[0].label, "cp_type", data[0].id);

															

														} else if(data.length > 1){ 
															NewWindow("../company/pop_company_searched_list.php?con_cp_type=판매,판매공급&search_str="+keyword + "&target_name=txt_cp_type&target_value=cp_type",'pop_company_searched_list','950','650','YES');

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

									function js_selecting_company(target_name, cp_nm, target_value, cp_no) {
										
										$(function(){

											$("input[name="+target_name+"]").val(cp_nm);
											$("input[name="+target_value+"]").val(cp_no);

											$.getJSON( "../company/json_company_list.php?cp_no=" + cp_no, function( data, status, xhr ) {
												
												$.each(data, function(i, field){
														$("input[name=o_mem_name]").val(field.MANAGER_NM);
														$("input[name=o_email]").val(field.EMAIL);
														$("input[name=o_phone]").val(field.PHONE);
														$("input[name=phone]").val(field.PHONE2);
														$("input[name=o_hphone]").val(field.HPHONE);
														$("input[name=o_zipcode]").val(field.RE_ZIP);
														$("input[name=o_addr1]").val(field.RE_ADDR);
														$("select[name=opt_manager_no]").val(field.SALE_ADM_NO);
														
												});

											});

										});
									}
								</script>
								<!--<input type="text" class="seller" style="width:210px" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'판매',$cp_type)?>" />
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
														$(".seller").val('');
														$("input[name=cp_type]").val('');
													}
												}
											});
										} else {
											$(".seller").val('');
											$("input[name=cp_type]").val('');
										}
									});
								});
								</script>
								
							<? } ?>
							<input type="hidden" name="cp_type" value="<?=$cp_type?>">
							<input type="hidden" name="old_cp_type" value="<?=$cp_type?>">
							-->
						</td>
						<th>주문일</th>
						<td>
							<input type="text" class="txt datepicker" style="width: 150px; margin-right:3px;" name="order_date" value="<?=$order_date?>" maxlength="10"/>
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>주문자명</th>
						<td style="position:relative">
							<!-- 이전 주문자 등록정보가 적을시에 주소 및 연락처가 수정 안되는 문제로 기능 삭제 - 2016-11-11 -->
							<!--
							<div id="suggest" style="position:absolute; left:15px; top:25px; width:95%; height:81px; z-index:0; visibility: hidden; width:95%; ">
							<div id="suggestList" style="position:relative; border:solid 1px #cec6ba; background:#FFFFFF; width:95%;"></div>
							</div>
							<input type="text" class="txt" style="width:35%" name="o_mem_name" required value="<?=$rs_o_mem_name?>" onKeyDown="startSuggest();" />
							-->
							<input type="text" class="txt" style="width:35%" name="o_mem_name" required value="<?=$rs_o_mem_name?>"  />
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
							<input type="hidden" name="phone" value=""/>
							<label><input type="checkbox" name="chk_phone_switcher"/></label>
							<span class="phone_label">담당자번호로</span>
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
							<label><input type="checkbox" class="chk" id="same" name="same" onclick="javascript:js_same();"> 주문자 정보와 동일한 경우 체크 하세요.</label>
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
					<th>담당영업사원</th>
					<td class="line" colspan="3">
						<?= makeAdminInfoByMDSelectBox($conn,"opt_manager_no","  ","선택해주세요","", "") ?>
					</td>
				</tr>
				<tr>
					<th>주문자메모<br/>(배송메모)</th>
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
	  E: mwidthwrap
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