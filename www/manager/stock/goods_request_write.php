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
	$menu_right = "SG017"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/stock/stock.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/company/company.php";
	require "../../_classes/biz/work/work.php";
	require "../../_classes/biz/confirm/confirm.php";

#====================================================================
# Request Parameter
#====================================================================
	$today = date("Y-m-d", strtotime("0 month"));

	$req_no = trim($req_no);

#====================================================================
# DML Process
#====================================================================
	//echo "con_cp_type : ".$cp_type."<br>";
	if($mode == "UPDATE_CP_NO") { 

		if($req_no <> "" && $cp_type <> "")		
			updateGoodsRequestCPNo($conn, $req_no, $cp_type, getCompanyNameWithNoCode($conn, $cp_type), $s_adm_no);


?>
<script language="javascript">
		alert('정상 처리 되었습니다.');
		location.href =  "goods_request_write.php?req_no=<?=$req_no?>";
</script>
<?
	}

	if($mode == "UPDATE_GOODS_INFO") { 

		$arr_rs_goods = listGoodsRequestGoods($conn, $req_no, 'N');
		$row_cnt = count($sub_goods_id);

		for ($k = 0; $k < $row_cnt; $k++) {

			$order_goods_no		= $sub_order_goods_no[$k];
			$goods_no			= $sub_goods_id[$k];
			$goods_code			= SetStringFromDB($sub_goods_code[$k]);
			$goods_nm			= SetStringFromDB($sub_goods_name[$k]);
			$goods_sub_nm		= SetStringFromDB($sub_goods_sub_name[$k]);
			$buy_price          = $sub_buy_price[$k];
			$req_qty			= $sub_goods_cnt[$k];

			$origin_chk_to_here			= $origin_sub_to_here[$k];
			
			$buy_total_price = $buy_price * $req_qty;

			if(sizeof($sub_to_here) > 0)
				$chk_to_here = in_array ($goods_no, $sub_to_here);
			else 
				$chk_to_here = false;

			//선택한 상품의 내역만 변경
			if($req_goods_no == $sub_req_goods_no[$k]) {

				//변경 히스토리 추가
				$result = insertRequestGoodsHistory($conn, $req_no, $req_goods_no, $goods_no, $buy_price, $req_qty, $s_adm_no);

				if($result) { 

					//가재고의 공급가, 수량 변경
					updateStockFromRequestGoods($conn, $goods_no, $req_qty, $buy_price, $req_goods_no, $s_adm_no, " (발주의 공급가, 수량변경 : ".date("Y-m-d H:i", strtotime("0 month")).")\n");

					//변경시 원장내역도 변경
					updateCompanyLedgerByRGNNo($conn, $req_qty, $buy_price, $req_goods_no);

					$result = updateGoodsRequestGoods($conn, $s_adm_com_code, $order_goods_no, $goods_no, $goods_code, $goods_nm, $goods_sub_nm, $buy_price, $req_qty, $buy_total_price, $origin_chk_to_here, $chk_to_here, $req_goods_no, $s_adm_no);
				}

			} else 
				continue;
			
		}
		
		resetGoodsRequestTotal($conn, $req_no);
	}

	if($mode == "INSERT_GOODS_REQUEST") { 

		$buy_cp_no = $cp_type;

		$row_cnt = count($sub_goods_id);

		$req_no = insertGoodsRequest($conn, $s_adm_com_code, $group_no, $req_date, $buy_cp_no, $delivery_type, $memo, $s_adm_no);

		for ($k = 0; $k < $row_cnt; $k++) {

			$goods_no			= 		$sub_goods_id[$k];
			$goods_code			= 	  $sub_goods_code[$k];
			$goods_nm			= 	  $sub_goods_name[$k];
			$goods_sub_nm		= $sub_goods_sub_name[$k];
			$buy_price          = 	   $sub_buy_price[$k];
			$req_qty			= 	   $sub_goods_cnt[$k];
			
			$buy_total_price = $buy_price * $req_qty;

			if(sizeof($sub_to_here) > 0)
				$chk_to_here = in_array ($goods_no, $sub_to_here);
			else
				$chk_to_here = false;

			$result = insertGoodsRequestGoods($conn, $s_adm_com_code, $req_no, '', '', $group_no, $goods_no, $goods_code, $goods_nm, $goods_sub_nm, $buy_price, $req_qty, $buy_total_price, $chk_to_here, $memo1, $memo2, $s_adm_no);
		}

		resetGoodsRequestTotal($conn, $req_no);

?>
<script language="javascript">
		alert('정상 처리 되었습니다.');
		location.href =  "goods_request_write.php?req_no=<?=$req_no?>";
</script>
<?
	}

	if($mode == "UPDATE_GOODS_REQUEST") { 

		updateGoodsRequest($conn, $req_date, $delivery_type, $memo, $req_no, $s_adm_no);

		$arr_rs_goods = listGoodsRequestGoods($conn, $req_no, 'N');
		$row_cnt = count($sub_goods_id);

		for ($k = 0; $k < $row_cnt; $k++) {


			$order_goods_no		= $sub_order_goods_no[$k];
			$req_goods_no		= $sub_req_goods_no[$k];
			$goods_no			= $sub_goods_id[$k];
			$goods_code			= $sub_goods_code[$k];
			$goods_nm			= $sub_goods_name[$k];
			$goods_sub_nm		= $sub_goods_sub_name[$k];
			$buy_price          = $sub_buy_price[$k];
			$req_qty			= $sub_goods_cnt[$k];

			$origin_chk_to_here			= $origin_sub_to_here[$k];
			
			$buy_total_price = $buy_price * $req_qty;

			if(sizeof($sub_to_here) > 0)
				$chk_to_here = in_array ($goods_no, $sub_to_here);
			else 
				$chk_to_here = false;

			if($req_goods_no <> "") {
				$result = updateGoodsRequestGoods($conn, $s_adm_com_code, $order_goods_no, $goods_no, $goods_code, $goods_nm, $goods_sub_nm, $buy_price, $req_qty, $buy_total_price, $origin_chk_to_here, $chk_to_here, $req_goods_no, $s_adm_no);
			} else {
				$result = insertGoodsRequestGoods($conn, $s_adm_com_code, $req_no, $reserve_no, $order_goods_no, $group_no, $goods_no, $goods_code, $goods_nm, $goods_sub_nm, $buy_price, $req_qty, $buy_total_price, $chk_to_here, $memo1, $memo2, $s_adm_no);
			}

			for($i = 0; $i < sizeof($arr_rs_goods); $i++) {
				if($arr_rs_goods[$i]["REQ_GOODS_NO"] == $req_goods_no)
					$arr_rs_goods[$i]["EXISTS"] = 'Y';
			}

		}
		
		for($i = 0; $i < sizeof($arr_rs_goods); $i++) {

			if($arr_rs_goods[$i]["EXISTS"] == "")
				deleteGoodsRequestGoods($conn, $arr_rs_goods[$i]["REQ_GOODS_NO"], $s_adm_no);
		}
		
		resetGoodsRequestTotal($conn, $req_no);

?>
<script language="javascript">
		alert('정상 처리 되었습니다.');
		location.href =  "goods_request_write.php?req_no=<?=$req_no?>";
</script>
<?
	}

	if($mode == "REQUEST_CONFIRM") { 
		
		//발주입력 - 가입고
		if($request_type != "") { 

			insertFStock($conn, $req_no, $s_adm_no);

			updateGoodsRequestSent($conn, $request_type, $req_no);

?>
<script language="javascript">
		alert('정상 처리 되었습니다.');
</script>
<?

		}
	}

	if($mode == "SEND_EMAIL") { 

		$buy_cp_no = $cp_type;
	
		if($only_for == "Y")
			$download_url = "https://".$_SERVER['HTTP_HOST']."/manager/stock/goods_request_excel_".$buy_cp_no.".php?req_no=".base64url_encode($req_no)."";
		else
			$download_url = "https://".$_SERVER['HTTP_HOST']."/manager/stock/goods_request_excel.php?req_no=".base64url_encode($req_no)."";
		echo "<script>alert('$download_url');<br></script>";
		$path = $_SERVER["DOCUMENT_ROOT"]."/upload_data/temp_mail";
		$filename = "기프트넷_발주서(REQ_".$req_no.").xls";
		$file = $path . "/" . $filename;
		
		downloadFile($download_url, $file);

		//운영업체가 아닐경우 해당 업체로 변경해줘야 함 - 지금은 운영업체만 사용한다고 가정
		$arr_op_cp = getOperatingCompany($conn, $s_adm_com_code);
		$OP_CP_NM = $arr_op_cp[0]["CP_NM"]." ".$arr_op_cp[0]["CP_NM2"];
		$OP_CEO_NM = $arr_op_cp[0]["CEO_NM"];
		$OP_CP_ADDR = $arr_op_cp[0]["CP_ADDR"];
		$OP_CP_PHONE = $arr_op_cp[0]["CP_PHONE"];
		$OP_EMAIL = $arr_op_cp[0]["EMAIL"];

		if($req_no <> "" && $sent_email <> "") {
		
			//sendMail($OP_EMAIL, $OP_CP_NM, $email_subject, $email_body, $sent_email);
			//sendMail2($OP_EMAIL, $OP_CP_NM, $email_subject, $email_body, $sent_email, $path, $filename);

			include('../../_PHPMailer/class.phpmailer.php');

			$sent_email = str_replace(";", ",", $sent_email);

			mailer($OP_CP_NM, $OP_EMAIL, $sent_email, $sent_email, $email_subject, $email_body, $path, $filename);

			//발주입력 - 가입고
			insertFStock($conn, $req_no, $s_adm_no);

			//발주확인하고 발주상태입력
			updateGoodsRequestSent($conn, $request_type, $req_no);

			//메일발송상황 업데이트
			updateGoodsRequestSentEmail($conn, $req_no, $sent_email, $email_subject, $email_body);
	?>
	<script language="javascript">
			alert('정상 처리 되었습니다.');
			location.href =  "goods_request_list.php";
	</script>
	<?
		} else {
	?>
	<script language="javascript">
			alert('에러입니다 생성된 전표가 맞는지, 이메일 주소가 있는지 확인부탁드립니다.');
	</script>
	<?
		}

	}
#===============================================================
# Get Search list count
#===============================================================

	if($req_no <> "") { 
		$arr_rs = selectGoodsRequestByReqNo($conn, $req_no);

		$REQ_DATE = $arr_rs[0]["REQ_DATE"];
		$REQ_DATE = date("Y-m-d", strtotime($REQ_DATE));
		$GROUP_NO = $arr_rs[0]["GROUP_NO"];
		$SENDER_CP = $arr_rs[0]["SENDER_CP"];
		$CEO_NM = $arr_rs[0]["CEO_NM"];
		$SENDER_ADDR = $arr_rs[0]["SENDER_ADDR"];
		$SENDER_PHONE = $arr_rs[0]["SENDER_PHONE"];
		$BUY_CP_NO = $arr_rs[0]["BUY_CP_NO"];
		$BUY_CP_NM = $arr_rs[0]["BUY_CP_NM"];
		$BUY_MANAGER_NM = $arr_rs[0]["BUY_MANAGER_NM"];
		$BUY_CP_PHONE = $arr_rs[0]["BUY_CP_PHONE"];
		$DELIVERY_TYPE = $arr_rs[0]["DELIVERY_TYPE"];
		$MEMO = $arr_rs[0]["MEMO"];
		$TOTAL_REQ_QTY = $arr_rs[0]["TOTAL_REQ_QTY"];
		$TOTAL_BUY_TOTAL_PRICE = $arr_rs[0]["TOTAL_BUY_TOTAL_PRICE"];
		$request_type = $arr_rs[0]["REQUEST_TYPE"];
		$SENT_EMAIL = $arr_rs[0]["SENT_EMAIL"];
		$EMAIL_SUBJECT = $arr_rs[0]["EMAIL_SUBJECT"];
		$EMAIL_BODY = $arr_rs[0]["EMAIL_BODY"];
		$IS_SENT = $arr_rs[0]["IS_SENT"];
		$SENT_DATE = $arr_rs[0]["SENT_DATE"];

		if($SENT_DATE == "0000-00-00 00:00:00")
			$SENT_DATE = "";
		else
			$SENT_DATE = date("Y-m-d H:i", strtotime($SENT_DATE));

		$arr_rs_goods = listGoodsRequestGoods($conn, $req_no, 'N');

		$combine_memo = "";
		$arr_temp_memo2 = array(); //같은이름이 두번들어가는걸 방지하는 체커
		for($o = 0; $o < sizeof($arr_rs_goods); $o++) {
			$temp_memo2 = $arr_rs_goods[$o]["MEMO2"];
			if(!in_array($temp_memo2, $arr_temp_memo2)) { 
				array_push($arr_temp_memo2,$temp_memo2);
				$combine_memo .= $temp_memo2.",";
			}
		}
		$combine_memo = rtrim($combine_memo,",");

		if($EMAIL_SUBJECT == "")
			$EMAIL_SUBJECT = $SENDER_CP." 발주_".trim($BUY_CP_NM)."_".date("Ymd", strtotime("0 month"))."_".$combine_memo;

		
		if($EMAIL_BODY == "") { 

			//운영업체가 아닐경우 해당 업체로 변경해줘야 함 - 지금은 운영업체만 사용한다고 가정
			$arr_op_cp = getOperatingCompany($conn, $s_adm_com_code);
			$OP_CP_NM = $arr_op_cp[0]["CP_NM"]." ".$arr_op_cp[0]["CP_NM2"];
			$OP_CEO_NM = $arr_op_cp[0]["CEO_NM"];
			$OP_CP_ADDR = $arr_op_cp[0]["CP_ADDR"];
			$OP_CP_PHONE = $arr_op_cp[0]["CP_PHONE"];
			$OP_EMAIL = $arr_op_cp[0]["EMAIL"];

			$template_msg = getDcodeExtByCode($conn, "MESSAGE_TEMPLATE", "GOODS_REQUEST");

			$template_msg = str_replace("[회사명]", $OP_CP_NM, $template_msg);
			$template_msg = str_replace("[발신자]", $s_adm_nm, $template_msg);
			$template_msg = str_replace("[메일주소]", $OP_EMAIL, $template_msg);
			$template_msg = str_replace("[엔터]", "\r\n", $template_msg);

			$EMAIL_BODY = $template_msg;
		} else {
			$EMAIL_BODY = str_replace("<br/>", "\r\n", $EMAIL_BODY);
		}

		$arr_exclusive_form = selectexclusiveformcpno($conn, $BUY_CP_NO);

		$EXCLUSIVE_CPNO = $arr_exclusive_form[0]["CP_NO"];

	} else {
		$REQ_DATE = $today;
		$GROUP_NO = cntMaxGroupNoRequest($conn);
		
	}

	$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str;
	$strParam = $strParam."&start_date=".$start_date."&end_date=".$end_date."&con_cp_type=".$con_cp_type;
	$strParam = $strParam."&con_delivery_tf=".$con_delivery_tf."&con_to_here=".$con_to_here."&con_cancel_tf=".$con_cancel_tf."&con_confirm_tf=".$con_confirm_tf."&con_changed_tf=".$con_changed_tf."&con_wrap_tf=".$con_wrap_tf."&con_sticker_tf=".$con_sticker_tf;
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

	$(".datepicker").blur(function(){
		if($(this).val().length > 8)
			checkStaEndDt($("input[name=start_date]"), $("input[name=end_date]"));
	});
  });

  </script>
<script>
	$(function() {
		$('.autocomplete_off').attr('autocomplete', 'off');
	});
</script>
  <script type="text/javascript" >

	function js_list() {
		document.location.href = "goods_request_list.php?<?=$strParam?>";
	}

	function js_save() {
		var frm = document.frm;

		if(frm.cp_type.value == ""){ 
			alert('매입업체를 입력해주세요.');
			return;
		}
		
		frm.target = "";
		frm.method = "post";
		frm.mode.value = "INSERT_GOODS_REQUEST";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_update() {
		var frm = document.frm;

		if(frm.cp_type.value == "0" || frm.cp_type.value == ""){ 
			alert('매입업체를 입력해주세요.');
			return;
		}

		frm.target = "";
		frm.method = "post";
		frm.mode.value = "UPDATE_GOODS_REQUEST";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_send_email(is_sent) {
		var frm = document.frm;

		if(is_sent == "Y")
			bOK = confirm('이미 발송하셨는데 재발송 하시겠습니까?');

		if(frm.sent_email.value != "") {

			var res = frm.sent_email.value.match(/[^0-9a-zA-Z-_.@,;]/gi);
			if (res != null) {
				alert('이메일에서 쓰이지 않는 부호가 있습니다. \n정상 이메일이 아닐경우 전산 메일 발송이 안되며 다중으로 보내실때는 주소사이에 , 또는 ; 기호를 사용하세요.');
				return;
			}

		} else { 
			alert('발송할 대상 이메일을 입력해주세요.');
			return;
		}
		
		if (is_sent == "N" || (is_sent == "Y" && bOK==true)) {
			frm.target = "";
			frm.method = "post";
			frm.mode.value = "SEND_EMAIL";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_request_confirm() {
		var frm = document.frm;
		frm.target = "";
		frm.method = "post";
		frm.mode.value = "REQUEST_CONFIRM";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_excel() {
		
		var frm = document.frm;
		var url = "";

		if($("[name=only_for]").is(':checked')){
			url = "https://<?=$_SERVER['HTTP_HOST']?>/manager/stock/goods_request_excel_"+frm.cp_type.value+".php?req_no=<?=base64url_encode($req_no)?>";
			//alert(url);
		}
		else
			url = "https://<?=$_SERVER['HTTP_HOST']?>/manager/stock/goods_request_excel.php?req_no=<?=base64url_encode($req_no)?>";
		window.location.assign(url);

	}

	function js_change_goods_info(req_goods_no) { 

		var frm = document.frm;

		bDelOK = confirm('입고정보가 변경됩니다. 가입고 정보를 확인해주세요.');
		
		if (bDelOK==true) {
			
			frm.mode.value = "UPDATE_GOODS_INFO";
			frm.req_goods_no.value = req_goods_no;
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_change_cp() { 
		var frm = document.frm;

		bOK = confirm('업체가 변경되면 기장내역도 변경됩니다. 꼭 확인해주세요.');
		
		if (bOK==true) {
			
			frm.mode.value = "UPDATE_CP_NO";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

</script>
<script type="text/javascript">

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

		var keyword = document.frm.search_name.value;
		
		if (keyword == '') {
			
			lastKeyword = '';
		
			hide('suggest');

		} else if (keyword != lastKeyword) {

			lastKeyword = keyword;
				
			if (keyword != '') {
				
				frm.keyword.value = keyword;
				frm.con_cate_03.value = frm.cp_type.value;
				frm.buy_sub_company.value = "Y";
				frm.action = "/manager/goods/search_goods.php";
				frm.target = "ifr_hidden";
				frm.submit();
				
			} else {
				hide('suggest');
			}
		}
		setTimeout("sendKeyword();", 100);
	}

	function displayResult(str) {
		//alert('1');		
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

				var hasMemo = false;
				if(arr_keywordList[11] != "")
					hasMemo = true;

				
				html += "<table width='100%' border='0'>";
				html += "<tr title='"+arr_keywordList[11]+"'>";
				html += "<td style='padding:0px 5px 0px 5px' width='55px'><img src='"+arr_keywordList[2]+"' width='50' height='50' border='0'></td>";
				html += "<td>" +"<a href=\"javascript:js_select('"+ arr_keywordList[0]+"','"+arr_keywordList[1]+"')\">"+arr_keywordList[1]+"</a>" +(hasMemo ? " <span style='color:red;font-size:11px;'>m</span>" : "") +  "</td>";
				html += "<td width='55px'>"+arr_keywordList[8]+"</td>";
				html += "<td width='105px'>공급가 : "+arr_keywordList[7]+"</td>";
				html += "</tr>";
				html += "</table>";
		
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

		frm.search_name.value = selectedKeyword;

		arr_keywordValues = selectedKey.split('');

		//frm.goods_name.value					= arr_keywordValues[0];
		//frm.goods_no.value						= arr_keywordValues[1];
        // arr_keywordValues[2]; 공급가
		// arr_keywordValues[3]; 판매가
		
		/* 
		// 20160511 같은 상품 추가할 필요있음
		var sub_goods_ids = frm.elements['sub_goods_id[]'];
		if(sub_goods_ids != undefined)
		{
			if(sub_goods_ids.value == arr_keywordValues[1]) 
			{
				alert('이미 추가한 상품입니다');
				return;
			}
			for (var i = 0; i < sub_goods_ids.length; i++) {
				if(sub_goods_ids[i].value == arr_keywordValues[1]){
					alert('이미 추가한 상품입니다');
					return;
				}
			}
		}
		*/

		$(".sub_goods_list").append("<tr><th>상품명</th><td class='line'>" + arr_keywordValues[0] + "["+ arr_keywordValues[1] + "]" + "<input type='hidden' name='sub_order_goods_no[]' value=''><input type='hidden' name='sub_req_goods_no[]' value=''><input type='hidden' name='sub_goods_code[]' value='" + arr_keywordValues[4] + "'><input type='hidden' name='sub_goods_name[]' value='" + arr_keywordValues[0] + "'><input type='hidden' name='sub_goods_sub_name[]' value='" + arr_keywordValues[12] + "'><input type='hidden' name='sub_goods_id[]' value='" + arr_keywordValues[1] + "'></td><th>공급가</th><td class='line'><input type='text' name='sub_buy_price[]' class='txt' style='width:70%' value='"+arr_keywordValues[2]+"'>원</td><th>수량</th><td class='line'><input type='text' name='sub_goods_cnt[]' class='txt' style='width:70%' value='1'>개</td><th>자체수령</th><td class='line'><input type='hidden' name='origin_sub_to_here[]' value='Y'/><input type='checkbox' name='sub_to_here[]' class='txt' checked='checked' value='"+ arr_keywordValues[1] +"'></td><td class='line'><span class='remove_sub' style='color:#478fb2; cursor:pointer; font-weight:bold; text-decoration:underline;'>삭제</span></td></tr>");

		loopSendKeyword = false;
		checkFirst = false;
		hide('suggest');

	}

	function show(elementId) {
		var element = document.getElementById(elementId);
		
		if (element) {
			element.style.visibility  ="visible"; 
		}
	}

	function hide(elementId) {
		var element = document.getElementById(elementId);
		if (element) {
			element.style.visibility  ="hidden"; 
		}
	}

$(function(){
	$('body').on('click', '.remove_sub', function() {
		$(this).closest("tr").remove();
	});
});

</script>
</head>

<body id="admin">




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

		<form name="frm" method="post">
		<input type="hidden" name="mode" value="" />
		<input type="hidden" name="keyword" value="" />
		<input type="hidden" name="con_cate_03" value="" />
		<input type="hidden" name="req_no" value="<?=$req_no?>" />
		<input type="hidden" name="req_goods_no" value="" />
		<input type="hidden" name="buy_sub_company" value="" />

		<input type="hidden" name="nPageSize" value="<?=$nPageSize?>" />
		<input type="hidden" name="search_field" value="<?=$search_field?>" />
		<input type="hidden" name="search_str" value="<?=$search_str?>" />
		<input type="hidden" name="order_field" value="<?=$order_field?>" />
		<input type="hidden" name="order_str" value="<?=$order_str?>" />
		<input type="hidden" name="start_date" value="<?=$start_date?>" />
		<input type="hidden" name="end_date" value="<?=$end_date?>" />
		<input type="hidden" name="con_cp_type" value="<?=$con_cp_type?>" />
		<input type="hidden" name="con_delivery_tf" value="<?=$con_delivery_tf?>" />
		<input type="hidden" name="con_to_here" value="<?=$con_to_here?>" />
		<input type="hidden" name="con_cancel_tf" value="<?=$con_cancel_tf?>" />
		<input type="hidden" name="con_confirm_tf" value="<?=$con_confirm_tf?>" />
		<input type="hidden" name="con_changed_tf" value="<?=$con_changed_tf?>" />
		<input type="hidden" name="con_wrap_tf" value="<?=$con_wrap_tf?>" />
		<input type="hidden" name="con_sticker_tf" value="<?=$con_sticker_tf?>" />


			<!-- S: mwidthwrap -->
			<div id="mwidthwrap">

				<h2>발주 관리</h2>
				<div class="btn_right">
					<a href="javascript:js_list();"><img src="../images/admin/btn_list.gif" alt="목록"></a>
				</div>
				<?
					if($req_no != "") { 
				?>
				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="10%" />
					<col width="35%" />
					<col width="10%" />
					<col width="35%" />
					<col width="*" />
				</colgroup>
				<tbody>
					<tr>
						<th>발송방식 선택</th>
						<td class="line">
							<?= makeSelectBox($conn,"REQUEST_TYPE","request_type","125","선택","",$request_type)?>
							<script>
								$(function(){
									$("select[name=request_type]").on("change", function(){
										if($(this).val() == "RT001") { 
											$(".display_none").show();
											$("input[type=button][name=a0]").hide();
										} else { 
											$(".display_none").hide();
											$("input[type=button][name=a0]").show();
										}
									});

									if($("select[name=request_type]").val() == "RT001") { 
										$(".display_none").show();
										$("input[type=button][name=a0]").hide();
									} else { 
										$(".display_none").hide();
										$("input[type=button][name=a0]").show();
									}
								});
							</script>
						</td>
						<td class="line" colspan="2">
							<? if($IS_SENT != "Y") { ?>
							<input type="button" name="a0" value=" 발주 처리 완료 / 가입고 입력 " class="btntxt" onclick="this.style.visibility='hidden';  js_request_confirm();">
							<? } ?>
						</td>
						<td align="right">	
							<? 
								//전용 서식 사용업체 예외 처리
								//5332 LG생활건강-화장품, 6874 (주)풍성에프에이, 4489 (주)네오플램, 5331 LG생활건강, 4848 아이엔에스엔터프라이즈 , 4984 위닉, 5227 프로넷, 4736 커스커즈, 5651 강고집, 4522 대상엘티디, 9701 제스파, 5611 리앤쿡
								//if($BUY_CP_NO == "5332" || $BUY_CP_NO == "6874" || $BUY_CP_NO == "4489" || $BUY_CP_NO == "5331" || $BUY_CP_NO == "4848" || $BUY_CP_NO == "4984" || $BUY_CP_NO == "5227" || $BUY_CP_NO == "4736" || $BUY_CP_NO == "5651" || $BUY_CP_NO == "4522" || $BUY_CP_NO =="4670" || $BUY_CP_NO=="9794" || $BUY_CP_NO=="9701" || $BUY_CP_NO=="5611") {

								//20210525 전용서식 사용업체 T_EXCLUSIVE_FORM 테이블 생성									
								if($EXCLUSIVE_CPNO <> ''){
							?>
							<label><input type="checkbox" id="only_for" name="only_for" value="Y" checked="checked" />전용서식</label>
							<? } else { ?>
								<input type="hidden" name="only_for" value=""/>
							<? } ?>
							<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>
						</td>
					</tr>
					<tr class="display_none">
						<th>이메일</th>
						<td class="line">
							<input type="text" class="txt" name="sent_email" value="<?=$IS_SENT == "Y" ? $SENT_EMAIL : getCompanyEmail($conn, $BUY_CP_NO)?>" style="width: 80%;" placeholder="복수의 메일을 보내실 때에는 ',' 혹은 ';'를 빈칸없이 붙여서 입력해주세요."/> 
						</td>
						<th>발송여부</th>
						<td colspan="2"><?=$IS_SENT == "Y" ? "<font color='green'>".$SENT_DATE."</font>" : "<font color='red'>발송전</font>"?> &nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="b0" value=" 발주서 발송 " class="btntxt" onclick="this.style.visibility='hidden';  js_send_email('<?=$IS_SENT?>'); ">
						</td>
						
					</tr>
					<tr class="display_none">
						<th>메일 제목</th>
						<td class="line" colspan="4">
							<input type="text" class="txt" name="email_subject" value="<?=$EMAIL_SUBJECT?>" style="width: 85%;"/> 
						</td>
					</tr>
					<tr class="display_none">
						<th>메일 내용</th>
						<td colspan="4" class="memo">
							<textarea style="width:85%; height:160px;" name="email_body"><?= $EMAIL_BODY ?></textarea>
						</td>
					</tr>
				</tbody>
				</table>
				<div class="sp20"></div>
				<?
					}
				?>
				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="10%" />
					<col width="37%" />
					<col width="10%" />
					<col width="37%" />
					<col width="6%" />
				</colgroup>
				<tbody>
					<?if($req_no == "") { ?>
					<tr>
						<th>전표번호</th>
						<td class="line">
							<input type="text" class="txt" name="group_no" value="<?=$GROUP_NO?>" style="width: 100px;"/>
						</td>
						<th>매입업체</th>
						<td colspan="2" class="line">
							<input type="text" class="autocomplete_off" style="width:90%" placeholder="업체(명/코드) 입력후 엔터를 눌러주세요" name="txt_cp_type" value="<?=$BUY_CP_NM?>" />
							<input type="hidden" name="cp_type" value="<?=$BUY_CP_NO?>">

							<script>
								$(function(){

									$("input[name=txt_cp_type]").keydown(function(e){

										if(e.keyCode==13) { 

											var keyword = $(this).val();
											if(keyword == "") { 
												$("input[name=cp_type]").val('');
												alert('검색 문자열을 입력해 주세요');
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

								}
							</script>
						</td>
					</tr>
					<tr>
						<th>발주일</th>
						<td class="line" colspan="4">
							<input type="text" class="txt datepicker" style="width: 100px; margin-right:3px;" name="req_date" value="<?=$REQ_DATE?>" maxlength="10"/>
						</td>
					</tr>
					<tr>
						<th>특이사항</th>
						<td colspan="4" class="line">
							<textarea name="memo" cols="50" rows="2"><?=$MEMO?></textarea>
							
						</td>
					</tr>
					<? } else { ?>
					<tr>
						<th>전표번호</th>
						<td class="line">
							<?=$GROUP_NO?>
							<input type="hidden" name="group_no" value="<?=$GROUP_NO?>"/>
						</td>
						<th>매입업체</th>
						<td colspan="2" class="line">
							<input type="text" class="autocomplete_off" style="width:200px" placeholder="업체(명/코드) 입력후 엔터를 눌러주세요" name="txt_cp_type" value="<?=$BUY_CP_NM?>" />
							<input type="hidden" name="cp_type" value="<?=$BUY_CP_NO?>">

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

								}
							</script>
							<input type="button" name="b" onclick="js_change_cp();" value=" 업체변경 ">

						</td>
					</tr>
					<tr>
						<th>발주일</th>
						<td class="line" colspan="2">
							<?=$REQ_DATE?>
							<input type="hidden" name="req_date" value="<?=$REQ_DATE?>"/>
						</td>
						<td colspan="2">
							<?
								$arr_rs_company = selectCompany($conn, $BUY_CP_NO);
		
								if(sizeof($arr_rs_company) > 0) { 

									/*
									$rs_cp_type							= SetStringFromDB($arr_rs_company[0]["CP_TYPE"]); 
									$rs_cp_zip							= SetStringFromDB($arr_rs_company[0]["CP_ZIP"]); 
									$rs_cp_addr							= SetStringFromDB($arr_rs_company[0]["CP_ADDR"]); 
									$rs_biz_no							= SetStringFromDB($arr_rs_company[0]["BIZ_NO"]); 
									$rs_ceo_nm							= SetStringFromDB($arr_rs_company[0]["CEO_NM"]); 
									$rs_cp_phone						= SetStringFromDB($arr_rs_company[0]["CP_PHONE"]); 
									*/

									$rs_ad_type							= SetStringFromDB($arr_rs_company[0]["AD_TYPE"]); 

									if($rs_ad_type == "")
										$rs_ad_type = "미정";
								}
							?>
							<b>결제 구분</b> : <span style="color:red; font-weight:bold;"><?=$rs_ad_type?></span>
						</td>
					</tr>
					<tr>
						<th>특이사항</th>
						<td colspan="4" class="line">
							<?=$MEMO?>
							<input type="hidden" name="memo" value="<?=$MEMO?>"/>
						</td>
					</tr>
					<? } ?>
				</tbody>
			</table>
			<div class="sp20"></div>
			<table cellpadding="0" cellspacing="0" class="colstable02">
				<colgroup>
					<col width="15%" />
					<col width="*" />
				</colgroup>
				<tbody>
					
					<tr class="set_goods">
						<th>발주 자재 추가</th>
						<td style="position:relative" class="line">
							<div id="suggest" style="position:absolute; left:15px; top:25px; width:95%; height:81px; z-index:0; visibility: hidden; width:95%; ">
								<div id="suggestList" style="height:300px; overflow-y:scroll; position:relative; border:solid 1px #cec6ba; background:#FFFFFF; width:95%;"></div>
							</div>
							<input type="text" class="txt search_name" style="width:75%; ime-mode:Active;" autocomplete="off" name="search_name" value="" onKeyDown="startSuggest();" onFocus="this.value='';" />

							<? if($IS_SENT != "Y") {  ?>
								<?
									if($req_no != "") { 
								?>
								<a href="javascript:js_update();"><img src="../images/admin/btn_confirm.gif" alt="확인"></a>
								<?  }  else {?>
								<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인"></a>
								<?  } ?>
							<? } ?>

							<table cellpadding="0" cellspacing="0" class="colstable02" style="margin-top:5px;">
							<colgroup>
								<col width="7%" />
								<col width="*" />
								<col width="7%" />
								<col width="10%" />
								<col width="7%" />
								<col width="10%" />
								<col width="7%" />
								<col width="10%" />
								<col width="7%" />
							</colgroup>
							<thead>
								<tr>
									<th colspan="9" class="line">상품을 검색해서 선택하시면 아래에 자재가 추가됩니다</th>
								</tr>
							</thead>
							<tbody class="sub_goods_list">
							</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			<script>
			<? 
				for($i = 0; $i < sizeof($arr_rs_goods); $i++) {

					if($arr_rs_goods[$i]["TO_HERE"] == 'Y')
						$str_to_here = "checked='checked'";
					else 
						$str_to_here = "";

					$arr_history = listRequestGoodsHistory($conn, "", $arr_rs_goods[$i]["REQ_GOODS_NO"]);

					$str_sub = "";
					for($m = 0; $m < sizeof($arr_history); $m++) { 

						$str_sub .= "<tr style='text-align:right;'><td class='line' colspan='2'>이전기록</td><td class='line' colspan='2'>".(float)$arr_history[$m]["BUY_PRICE"]." 원</td><td class='line' colspan='2'>".$arr_history[$m]["REQ_QTY"]." 개</td><td  class='line' colspan='3'>".getAdminName($conn, $arr_history[$m]["REG_ADM"])." (".$arr_history[$m]["REG_DATE"].")</td></tr>";
					}
			?>
				

			$(".sub_goods_list").append("<tr>"+
				"<th>상품명</th><td class='line'><?=$arr_rs_goods[$i]["GOODS_NAME"]?>[<?=$arr_rs_goods[$i]["GOODS_CODE"]?>]"+
				"<input type='hidden' name='sub_order_goods_no[]' value='<?=$arr_rs_goods[$i]["ORDER_GOODS_NO"]?>'>"+
				"<input type='hidden' name='sub_req_goods_no[]' value='<?=$arr_rs_goods[$i]["REQ_GOODS_NO"]?>'>"+
				"<input type='hidden' name='sub_goods_code[]' value='<?=$arr_rs_goods[$i]["GOODS_CODE"]?>'>"+
				"<input type='hidden' name='sub_goods_name[]' value='<?=$arr_rs_goods[$i]["GOODS_NAME"]?>'>"+
				"<input type='hidden' name='sub_goods_sub_name[]' value='<?=$arr_rs_goods[$i]["GOODS_SUB_NAME"]?>'>"+
				"<input type='hidden' name='sub_goods_id[]' value='<?=$arr_rs_goods[$i]["GOODS_NO"]?>'></td>"+
				"<th>공급가</th><td class='line'>"+
				"<input type='text' name='sub_buy_price[]' class='txt' style='width:70%' value='<?=(float)$arr_rs_goods[$i]["BUY_PRICE"]?>'>원</td>"+
				"<th>수량</th><td class='line'><input type='text' name='sub_goods_cnt[]' class='txt calc' style='width:70%' value='<?=$arr_rs_goods[$i]["REQ_QTY"]?>'>개</td>"+
				"<th>자체수령</th><td class='line'><input type='hidden' name='origin_sub_to_here[]' value='<?=$arr_rs_goods[$i]["TO_HERE"]?>'/><input type='checkbox' name='sub_to_here[]' class='txt' <?=$str_to_here?> value='<?=$arr_rs_goods[$i]["GOODS_NO"]?>'></td><td class='line'>" +
				<? if($IS_SENT != "Y") { ?>
				"<span class='remove_sub' style='color:#478fb2; cursor:pointer; font-weight:bold; text-decoration:underline;'>삭제</span>" +
				<? } else { ?>
				"<input type='button' name='bb' value='내역변경' onclick='js_change_goods_info(<?=$arr_rs_goods[$i]["REQ_GOODS_NO"]?>);' /> " + 
				<? } ?>
				"</td>"+
				"<?=$str_sub?></tr>");	
			<?
				} 
			?>
			</script>


			
			<iframe src="about:blank" name="ifr_hidden" id="ifr_hidden" frameborder="no" width="100%" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
		</form>
		<?
			if($req_no != "") { 
		?>
		<iframe src="/manager/stock/pop_goods_request.php?req_no=<?=$req_no?>&mode=edit" frameborder="no" width="100%" height="800px" marginwidth="0" marginheight="0" border="1"></iframe>
		<?
			}
		?>
		</td>

	</tr>
	</table>

	        
</div>

</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>