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
	$menu_right = "GD007"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/proposal/proposal.php";
	require "../../_classes/biz/company/company.php";
	require "../../_classes/biz/goods/goods.php";


#====================================================================
# Request Parameter
#====================================================================
	$today = date("Y-m-d", strtotime("0 month"));

	$gp_no = trim($gp_no);

#====================================================================
# DML Process
#====================================================================

	if($mode == "INSERT_GOODS_PROPOSAL") { 

		$cp_no = $cp_type;

		$row_cnt = count($sub_goods_id);

		$gp_no = insertGoodsProposal($conn, $group_no, $cp_no, $dc_rate, nl2br($memo), $s_adm_no);
 
		for ($k = 0; $k < $row_cnt; $k++) {

			$goods_no			= $sub_goods_id[$k];
			$goods_nm			= $sub_goods_name[$k];
			$delivery_cnt_in_box= $sub_delivery_cnt_in_box[$k];
			$retail_price       = $sub_retail_price[$k];
			$proposal_price     = $sub_proposal_price[$k];

			$result = insertGoodsProposalGoods($conn, $gp_no, $group_no, $goods_no, $goods_nm, $delivery_cnt_in_box, $retail_price, $proposal_price, $s_adm_no);
		}

?>
<script language="javascript">
		alert('정상 처리 되었습니다.');
		location.href =  "goods_proposal_write.php?gp_no=<?=$gp_no?>";
</script>
<?
	}

	if($mode == "UPDATE_GOODS_PROPOSAL") { 
		updateProposal($conn, $dc_rate, nl2br($memo), $gp_no, $s_adm_no);

		$arr_rs_goods = listGoodsProposalGoods($conn, $gp_no, 'N');
		$row_cnt = count($sub_goods_id);

		for ($k = 0; $k < $row_cnt; $k++) {


			$gpg_no				= $sub_gpg_no[$k];
			$goods_no			= $sub_goods_id[$k];
			$goods_nm			= $sub_goods_name[$k];
			$delivery_cnt_in_box= $sub_delivery_cnt_in_box[$k];
			$retail_price       = $sub_retail_price[$k];
			$proposal_price		= $sub_proposal_price[$k];


			if($gpg_no <> "") {
				$result = updateGoodsProposalGoods($conn, $goods_no, $goods_nm, $delivery_cnt_in_box, $retail_price, $proposal_price, $gpg_no, $s_adm_no);
			} else {
				$result = insertGoodsProposalGoods($conn, $gp_no, $group_no, $goods_no, $goods_nm, $delivery_cnt_in_box, $retail_price, $proposal_price, $s_adm_no);
				
			}

			for($i = 0; $i < sizeof($arr_rs_goods); $i++) {
				if($arr_rs_goods[$i]["GPG_NO"] == $gpg_no)
					$arr_rs_goods[$i]["EXISTS"] = 'Y';
			}

		}
		
		for($i = 0; $i < sizeof($arr_rs_goods); $i++) {

			if($arr_rs_goods[$i]["EXISTS"] == "")
				deleteGoodsProposalGoods($conn, $arr_rs_goods[$i]["GPG_NO"], $s_adm_no);
		}

?>
<script language="javascript">
		alert('정상 처리 되었습니다.');
		location.href =  "goods_proposal_write.php?gp_no=<?=$gp_no?>";
</script>
<?
	}

	if($mode == "INSERT_CATEGORY") { 

		$goods_cate = "";

		if ($gd_cate_01 <> "") {
			$goods_cate = $gd_cate_01;
		}
		if ($gd_cate_02 <> "") {
			$goods_cate = $gd_cate_02;
		}
		if ($gd_cate_03 <> "") {
			$goods_cate = $gd_cate_03;
		}
		if ($gd_cate_04 <> "") {
			$goods_cate = $gd_cate_04;
		}

		$arr_rs = listGoodsBySearchCategory($conn, $goods_cate);

		$cp_no = $cp_type;

		$gp_no = insertGoodsProposal($conn, $group_no, $cp_no, $dc_rate, nl2br($memo), $s_adm_no, $goods_cate);

		for($i = 0; $i < sizeof($arr_rs); $i++) { 
			$goods_nm			 = $arr_rs[$i]["GOODS_NAME"];
			$goods_no			 = $arr_rs[$i]["GOODS_NO"];
			$delivery_cnt_in_box = $arr_rs[$i]["DELIVERY_CNT_IN_BOX"];
			$retail_price		 = $arr_rs[$i]["SALE_PRICE"];
			$goods_cate			 = $arr_rs[$i]["GOODS_CATE"];
			$page				 = $arr_rs[$i]["PAGE"];
			$seq				 = $arr_rs[$i]["SEQ"];
			$cate_04			 = $arr_rs[$i]["CATE_04"];

			//판매중, 품절만 제안하도록 함 2017-06-21
			if($cate_04 != "판매중") continue;

			$arr_goods = selectGoodsPriceOnly($conn, $goods_no);
			if(sizeof($arr_goods) > 0) { 
				$price = $arr_goods[0]["PRICE"];
				$sale_price = $arr_goods[0]["SALE_PRICE"];
				$proposal_price = getCompanyGoodsPriceOrDCRate($conn, $goods_no, $sale_price, $price, $cp_no, $dc_rate);

			}

			$result = insertGoodsProposalGoods($conn, $gp_no, $group_no, $goods_no, $goods_nm, $delivery_cnt_in_box, $retail_price, $proposal_price, $s_adm_no, $goods_cate, $page, $seq);
		}

?>
<script language="javascript">
		//alert('정상 처리 되었습니다.');
		//location.href =  "goods_proposal_write.php?gp_no=<?=$gp_no?>";
</script>
<?
	}

	if($mode == "UPDATE_DC_RATE") { 
		//echo "cp_type : ".$cp_type."<br/>";

		updateProposal($conn, $dc_rate, nl2br($memo), $gp_no, $s_adm_no);

		$arr_rs_goods = listGoodsProposalGoods($conn, $gp_no, 'N');
		$row_cnt = count($sub_goods_id);

		for ($k = 0; $k < $row_cnt; $k++) {

			$gpg_no				= $sub_gpg_no[$k];
			$goods_no			= $sub_goods_id[$k];
			$goods_nm			= $sub_goods_name[$k];
			$delivery_cnt_in_box= $sub_delivery_cnt_in_box[$k];
			$retail_price       = $sub_retail_price[$k];
			//$proposal_price		= $sub_proposal_price[$k];

			$arr_goods = selectGoodsPriceOnly($conn, $goods_no);
			if(sizeof($arr_goods) > 0) { 
				$price = $arr_goods[0]["PRICE"];
				$sale_price = $arr_goods[0]["SALE_PRICE"];

				//echo $goods_no." / ".$sale_price." / ".$price." / ".$cp_type." / ".$dc_rate."<br/>";

				$proposal_price = getCompanyGoodsPriceOrDCRate($conn, $goods_no, $sale_price, $price, $cp_type, $dc_rate);
			}
			echo $goods_nm." r : ".$retail_price." p : ".$proposal_price;

			//echo $retail_price." / ";
			//echo $proposal_price."<br/>";

			if($gpg_no <> "") {
				echo " - if<br>";
				$result = updateGoodsProposalGoods($conn, $goods_no, $goods_nm, $delivery_cnt_in_box, $retail_price, $proposal_price, $gpg_no, $s_adm_no);
			} else {
				echo " - else<br>";
				$result = insertGoodsProposalGoods($conn, $gp_no, $group_no, $goods_no, $goods_nm, $delivery_cnt_in_box, $retail_price, $proposal_price, $s_adm_no);
				
			}

			for($i = 0; $i < sizeof($arr_rs_goods); $i++) {
				if($arr_rs_goods[$i]["GPG_NO"] == $gpg_no)
					$arr_rs_goods[$i]["EXISTS"] = 'Y';
			}

		}
		
		for($i = 0; $i < sizeof($arr_rs_goods); $i++) {

			if($arr_rs_goods[$i]["EXISTS"] == "")
				deleteGoodsProposalGoods($conn, $arr_rs_goods[$i]["GPG_NO"], $s_adm_no);
		}

?>
<script language="javascript">
		alert('정상 처리 되었습니다.');
	//	location.href =  "goods_proposal_write.php?gp_no=<?=$gp_no?>";
</script>
<?
	}


	if($mode == "PROPOSAL_CONFIRM") { 
		updateGoodsProposalSentEmail($conn, $gp_no, $request_type, $sent_email = "", $email_subject = "", $email_body = "");
?>
<script language="javascript">
		alert('정상 처리 되었습니다.');
		location.href =  "goods_proposal_write.php?gp_no=<?=$gp_no?>";
</script>
<?
	}

	if($mode == "SEND_EMAIL") { 
		if($print_type == "DETAIL_MIDDLE_SIZE"){
			$download_url = "http://".$_SERVER['HTTP_HOST']."/manager/proposal/goods_proposal_excel.php?gp_no=".base64url_encode($gp_no)."&print_type=$print_type&discount_percent=$discount_percent";
		}else{
			$download_url = "http://".$_SERVER['HTTP_HOST']."/manager/proposal/goods_proposal_excel.php?gp_no=".base64url_encode($gp_no)."&print_type=$print_type";
		}
		// $download_url = "http://".$_SERVER['HTTP_HOST']."/manager/proposal/goods_proposal_excel.php?gp_no=".base64url_encode($gp_no)."&print_type=".$print_type;
		$path = $_SERVER["DOCUMENT_ROOT"]."/upload_data/temp_mail";
		$filename = "기프트넷_제안서(GP_".$gp_no.").xls";
		$file = $path . "/" . $filename;
		
		downloadFile($download_url, $file);

		//운영업체가 아닐경우 해당 업체로 변경해줘야 함 - 지금은 운영업체만 사용한다고 가정
		$arr_op_cp = getOperatingCompany($conn, $s_adm_com_code);
		$OP_CP_NM = $arr_op_cp[0]["CP_NM"]." ".$arr_op_cp[0]["CP_NM2"];
		$OP_CEO_NM = $arr_op_cp[0]["CEO_NM"];
		$OP_CP_ADDR = $arr_op_cp[0]["CP_ADDR"];
		$OP_CP_PHONE = $arr_op_cp[0]["CP_PHONE"];
		$OP_EMAIL = $arr_op_cp[0]["EMAIL"];

		if($gp_no <> "" && $sent_email <> "") {
		
			include('../../_PHPMailer/class.phpmailer.php');

			$FROM_EMAIL = getDcodeExtByCode($conn, 'GOODS_PROPOSAL_EMAIL', 'FROM_EMAIL');
			$EMAIL_BODY = getDcodeExtByCode($conn, 'GOODS_PROPOSAL_EMAIL', 'EMAIL_BODY');
			
			$EMAIL_BODY = str_replace("[회사명]", $OP_CP_NM, $EMAIL_BODY);
			$EMAIL_BODY = str_replace("[발신자]", $s_adm_nm, $EMAIL_BODY);
			$EMAIL_BODY = str_replace("[엔터]", "\r\n", $EMAIL_BODY);

			//업체정보에 메일 주소 한개밖에 등록이 안되어 임시 하드코딩 처리
			//$OP_EMAIL = "gift@giftnet.co.kr";

			$sent_email = str_replace(";", ",", $sent_email);

			mailer($OP_CP_NM, $FROM_EMAIL, $sent_email, $sent_email, $email_subject, $EMAIL_BODY, $path, $filename);

			//메일발송상황 업데이트
			updateGoodsProposalSentEmail($conn, $gp_no, $request_type, $sent_email, $email_subject, $EMAIL_BODY);
	?>
	<script language="javascript">
			alert('정상 처리 되었습니다.');
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

	if($gp_no <> "") { 
		$arr_rs = selectGoodsProposalByGpNo($conn, $gp_no);

		$GROUP_NO		= $arr_rs[0]["GROUP_NO"];
		$CP_NO			= $arr_rs[0]["CP_NO"];
		$DC_RATE		= $arr_rs[0]["DC_RATE"];
		$GOODS_CATE		= $arr_rs[0]["GOODS_CATE"];
		$CP_NM			= getCompanyNameWithNoCode($conn, $CP_NO);
		$MEMO			= trim($arr_rs[0]["MEMO"]);
		$request_type   = $arr_rs[0]["REQUEST_TYPE"];
		$SENT_EMAIL     = $arr_rs[0]["SENT_EMAIL"];
		$EMAIL_SUBJECT  = $arr_rs[0]["EMAIL_SUBJECT"];
		$EMAIL_BODY     = $arr_rs[0]["EMAIL_BODY"];
		$IS_SENT		= $arr_rs[0]["IS_SENT"];
		$SENT_DATE		= $arr_rs[0]["SENT_DATE"];

		if($SENT_DATE == "0000-00-00 00:00:00")
			$SENT_DATE = "";
		else
			$SENT_DATE = date("Y-m-d H:i", strtotime($SENT_DATE));

		$arr_rs_goods = listGoodsProposalGoods($conn, $gp_no, 'N');

		//운영업체가 아닐경우 해당 업체로 변경해줘야 함 - 지금은 운영업체만 사용한다고 가정
		$arr_op_cp = getOperatingCompany($conn, $s_adm_com_code);
		$OP_CP_NM = $arr_op_cp[0]["CP_NM"]." ".$arr_op_cp[0]["CP_NM2"];
		$OP_CEO_NM = $arr_op_cp[0]["CEO_NM"];
		$OP_CP_ADDR = $arr_op_cp[0]["CP_ADDR"];
		$OP_CP_PHONE = $arr_op_cp[0]["CP_PHONE"];
		$OP_EMAIL = $arr_op_cp[0]["EMAIL"];


		if($EMAIL_SUBJECT == "")
			$EMAIL_SUBJECT = $OP_CP_NM." 제안_".trim($CP_NM)."_".date("Ymd", strtotime("0 month"));

		
		if($EMAIL_BODY == "") { 

			$EMAIL_BODY = "안녕하세요. ".$OP_CP_NM." ".$s_adm_nm."입니다. \r\n\r\n".
							 
							 "제안서 확인 부탁드립니다  \r\n\r\n".

							 "(회신 메일주소 : gift@giftnet.co.kr) \r\n\r\n";
		} else {
			$EMAIL_BODY = str_replace("<br/>", "\r\n", $EMAIL_BODY);
		}

	} else {
		$GROUP_NO = cntMaxGroupNoProposal($conn);

		if(sizeof($chk_no) > 0) { 

			$arr_rs_goods = listGoodsByGoodsNoArray($conn, $chk_no);

		}
		
	}

	if($MEMO == "") { 
		$MEMO = "* 부가세 포함 \n";
		$MEMO .="* 박스단위 일괄배송시 택배비 포함\n";
		$MEMO .="* 개별배송, 박스단위 채워지지 않을 시 개당 물류비 3,000원 별도 \n";
		$MEMO .="* 포장, 스티커 작업비 별도\n";
		$MEMO .="* 도서산간, 제주도 배송시 추가 요금 별도 \n";
	} else {
		$MEMO = br2nl($MEMO);
	}


	

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
<style>
	input[name='discount_percent']{
		visibility:hidden;
		width:70px;
	}
</style>
<script>
	$(document).ready(function(){
		
		//로딩 되자마자 모바일링크 옵션 아니면 버튼 숨김
		if($(this).val() == "RT005") {
			$("#btnCreateLink").show();
			$("#btnCreateImage").show();
		} else {
			$("#btnCreateLink").hide();
			$("#btnCreateImage").hide();
		}

		//발송방식 선택 셀렉트 변경 이벤트 바인딩
		$("select[name=request_type]").on("change", function(){
			//이메일 전산 발송 옵션일 경우
			if($(this).val() == "RT001") {
				$(".display_none").show();
				$("input[type=button][name=a0]").hide();
			} else {
				$(".display_none").hide();
				$("input[type=button][name=a0]").show();
			}

			//모바일 링크 옵션일 경우
			if($(this).val() == "RT005") {
				$("#btnCreateLink").show();
				$("#btnCreateImage").show();
			} else {
				$("#btnCreateLink").hide();
				$("#btnCreateImage").hide();
			}
		});

		$("select[name='print_type']").on("change", function(){
			if($("select[name=print_type]").val() == "DETAIL_MIDDLE_SIZE") {
				$("input[name='discount_percent']").attr("style","visibility:visible");
				//미리보기 변경
				$("iframe[name='preview']").attr('src', "/manager/proposal/pop_goods_proposal.php?gp_no=<?=$gp_no?>"+"&print_type=DETAIL_MIDDLE_SIZE&discount_percent="+frm.discount_percent.value);
			} else {
				$("input[name='discount_percent']").attr("style","visibility:hidden");
				//미리보기 변경
				$("iframe[name='preview']").attr('src', "/manager/proposal/pop_goods_proposal.php?gp_no=<?=$gp_no?>");
			}
		});
		$("input[name='discount_percent']").on("change", function(){
			$("iframe[name='preview']").attr('src', "/manager/proposal/pop_goods_proposal.php?gp_no=<?=$gp_no?>"+"&print_type=DETAIL_MIDDLE_SIZE&discount_percent="+frm.discount_percent.value);
		});

		
		//이메일 전산발송 옵션일 경우에는 이메일 보내기 버튼을 보이고, 제안 처리 완료 버튼을 숨긴다.
		if($("select[name=request_type]").val() == "RT001") {
			$(".display_none").show();
			$("input[type=button][name=a0]").hide();
		} else {
			$(".display_none").hide();
			$("input[type=button][name=a0]").show();
		}

		//링크 생성 버튼 클릭 이벤트 바인딩
		$("#btnCreateLink").on("mousedown", function() {
			var reg_adm = "<?=$s_adm_no?>";
			var gp_no = "<?=$gp_no?>";
			$.ajax({
				url: '/manager/ajax_processing.php',
				dataType: 'json',
				type: 'post',
				data : {
				'mode': "INSERT_LINK",
				'gp_no': gp_no,
				"reg_adm": reg_adm
				},
				success: function(response) {
					if(response != false){
						link = "https://www.giftnet.co.kr/manager/goods/pop_simple_goods_info.php?key=" + response;
						var $temp = $("<input id='clipboard' />");
						$("body").append($temp);
						$('#clipboard').val(link);
					} else{
						alert("실패하였습니다.");
					}
				}, error: function(jqXHR, textStatus, errorThrown) {
					console.log(jqXHR.responseText); 
				}
			});
		});
		$("#btnCreateLink").on("mouseup", function() {
			setTimeout(function() {
				var $input = $("#clipboard");
				if ($input.length && $input.val().length > 0) {
					$input.select();
					document.execCommand("copy");
					$input.remove();
				}
				alert("링크가 복사되었습니다.\n\n"+link);
				link="";
			}, 100);
		});
		$("#btnCreateImage").on("click", function() {
			var reg_adm = "<?=$s_adm_no?>";
			var gp_no = "<?=$gp_no?>";
			$.ajax({
				url: '/manager/ajax_processing.php',
				dataType: 'json',
				type: 'post',
				data : {
				'mode': "INSERT_LINK",
				'gp_no': gp_no,
				"reg_adm": reg_adm
				},
				success: function(response) {
					if(response != false){
						link = "https://www.giftnet.co.kr/manager/goods/pop_simple_goods_info_image.php?key=" + response;
						window.open(link, '_blank', 'width=450, height=768');
					} else{
						alert("실패하였습니다.");
					}
				}, error: function(jqXHR, textStatus, errorThrown) {
					console.log(jqXHR.responseText); 
				}
			});
		});
	});//ready
  </script>
  <script type="text/javascript" >

	// 조회 버튼 클릭 시 
	function js_save() {
		var frm = document.frm;
		
		frm.target = "";
		frm.method = "post";

		if(frm.cp_type.value == '' || frm.cp_type.value == '0' ) { 
			alert("제안업체를 입력해주십시오.");
			return;
		}

		frm.mode.value = "INSERT_GOODS_PROPOSAL";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_update() {
		var frm = document.frm;
		
		if(frm.cp_type.value == '' || frm.cp_type.value == '0' ) { 
			alert("제안업체를 입력해주십시오.");
			return;
		}

		frm.target = "";
		frm.method = "post";
		frm.mode.value = "UPDATE_GOODS_PROPOSAL";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_send_email(is_sent) {
		var frm = document.frm;

		if(is_sent == "Y")
			bOK = confirm('이미 발송하셨는데 재발송 하시겠습니까?');
		
		if (is_sent == "N" || (is_sent == "Y" && bOK==true)) {
			frm.target = "";
			frm.method = "post";
			frm.mode.value = "SEND_EMAIL";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_proposal_confirm() {
		var frm = document.frm;
		frm.target = "";
		frm.method = "post";
		frm.mode.value = "PROPOSAL_CONFIRM";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_update_dc_rate() {
		var frm = document.frm;
		frm.target = "";
		frm.method = "post";
		frm.mode.value = "UPDATE_DC_RATE";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_insert_category() {
		var frm = document.frm;
		frm.target = "";
		frm.method = "post";

		if(frm.cp_type.value == '' || frm.cp_type.value == '0' ) { 
			alert("제안업체를 입력해주십시오.");
			return;
		}

		frm.mode.value = "INSERT_CATEGORY";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_excel() {

		var frm = document.frm;
		if(frm.print_type.value == "DETAIL_MIDDLE_SIZE"){
			var url = "goods_proposal_excel.php?gp_no=<?=base64url_encode($gp_no)?>&print_type=" + frm.print_type.value + "&discount_percent=" + frm.discount_percent.value;
		}else{
			var url = "goods_proposal_excel.php?gp_no=<?=base64url_encode($gp_no)?>&print_type=" + frm.print_type.value;
		}
		window.location.assign(url);

	}

	function js_pop_proposal_goods(gpg_no){
		var url = "/manager/proposal/pop_goods_proposal_goods.php?gpg_no=" + gpg_no;
		NewWindow(url,'pop_proposal_goods','1024','600','Yes');
	}

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
				frm.cp_no.value = frm.cp_type.value;
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
				
				html += "<table width='100%' border='0'>";
				html += "<tr>";
				html += "<td style='padding:0px 5px 0px 5px' width='55px'><img src='"+arr_keywordList[2]+"' width='50' height='50' border='0'></td>";
				if(arr_keywordList[8] != "판매중" && arr_keywordList[8] != "품절")
					html += "<td style='color:gray;'>" + arr_keywordList[1] + "</td>";
				else
					html += "<td>" +"<a href=\"javascript:js_select('"+ arr_keywordList[0]+"','"+arr_keywordList[1]+"')\">"+arr_keywordList[1]+"</a>" + "</td>";
				html += "<td width='155px'>기프트넷단가 : "+arr_keywordList[10]+"</td>";
				html += "<td width='155px'>제안가 : "+arr_keywordList[3]+"</td>";
				html += "<td width='105px'>박스입수 : "+arr_keywordList[9]+"</td>";
				html += "<td width='55px'>"+arr_keywordList[8]+"</td>";
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

		
		var sub_goods_ids = frm.elements['sub_goods_id[]'];
		if(sub_goods_ids != undefined)
		{	
			var is_duplicated = false;
			if(sub_goods_ids.value == arr_keywordValues[1]) 
			{
				is_duplicated = true;
			}
			for (var i = 0; i < sub_goods_ids.length; i++) {
				if(sub_goods_ids[i].value == arr_keywordValues[1]){
					is_duplicated = true;
					continue;
				}
			}

			if(is_duplicated)
				alert('이미 추가한 상품입니다. 확인하고 진행해주세요.');
		}
		
		if(arr_keywordValues[14] == "판매중")
			$(".sub_goods_list").append("<tr><th>상품명</th><td class='line'>" + arr_keywordValues[0] + "["+ arr_keywordValues[4] + "]" + "<input type='hidden' name='sub_gpg_no[]' value=''><input type='hidden' name='sub_goods_name[]' value='" + arr_keywordValues[0] + "'><input type='hidden' name='sub_goods_id[]' value='" + arr_keywordValues[1] + "'></td><th>박스입수</th><td class='line' ><input type='hidden' name='sub_delivery_cnt_in_box[]' value='"+arr_keywordValues[11]+"'/>"+arr_keywordValues[11]+"</td><th>기프트넷단가</th><td class='line'><input type='text' name='sub_retail_price[]' class='txt' style='width:70%' value='"+arr_keywordValues[13]+"'/>원</td><th>벤더제안가</th><td class='line'><input type='text' name='sub_proposal_price[]' class='txt' style='width:70%' value='"+arr_keywordValues[3]+"'>원</td><td class='line'><span class='remove_sub' style='color:#478fb2; cursor:pointer; font-weight:bold; text-decoration:underline;'>삭제</span></td><td class='line'></td></tr>");
		else
			$(".sub_goods_list").append("<tr><th>상품명</th><td class='line'>" + arr_keywordValues[0] + " / <span style='color:red;'>" + arr_keywordValues[14] + "</span>["+ arr_keywordValues[4] + "]" + "<input type='hidden' name='sub_gpg_no[]' value=''><input type='hidden' name='sub_goods_name[]' value='" + arr_keywordValues[0] + "'><input type='hidden' name='sub_goods_id[]' value='" + arr_keywordValues[1] + "'></td><th>박스입수</th><td class='line' ><input type='hidden' name='sub_delivery_cnt_in_box[]' value='"+arr_keywordValues[11]+"'/>"+arr_keywordValues[11]+"</td><th>기프트넷단가</th><td class='line'><input type='text' name='sub_retail_price[]' class='txt' style='width:70%' value='"+arr_keywordValues[13]+"'/>원</td><th>벤더제안가</th><td class='line'><input type='text' name='sub_proposal_price[]' class='txt' style='width:70%' value='"+arr_keywordValues[3]+"'>원</td><td class='line'><span class='remove_sub' style='color:#478fb2; cursor:pointer; font-weight:bold; text-decoration:underline;'>삭제</span></td><td class='line'></td></tr>");

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
		<input type="hidden" name="depth" value="" />
		<input type="hidden" name="keyword" value="" />
		<input type="hidden" name="gp_no" value="<?=$gp_no?>" />
		<input type="hidden" name="cp_no" value="" />

			<!-- S: mwidthwrap -->
			<div id="mwidthwrap">

				<h2>제안 관리</h2>
				<?
					if($gp_no != "") { 
				?>
				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="10%" />
					<col width="*" />
					<col width="10%" />
					<col width="*" />
					<col width="20%" />
				</colgroup>
				<tbody>
					<tr>
						<th>발송방식 선택</th>
						<td class="line">
							<?= makeSelectBox($conn,"REQUEST_TYPE","request_type","125","선택","",$request_type)?>
						</td>
						<td class="line" colspan="2">
							<? if($IS_SENT != "Y") { ?>
							<input type="button" name="a0" value=" 제안 처리 완료 " class="btntxt" onclick="javascript:js_proposal_confirm();">
							<? } ?>
							<input type="button" value=" 링크 생성 " class="btntxt" id="btnCreateLink">
							<input type="button" value=" 이미지로 저장 " class="btntxt" id="btnCreateImage">
						</td>
						<td align="right">
							<input type="text" name="discount_percent" placeholder="단가할인율"/>
							<select name="print_type">
								<option value="ALL">리스트+상품디테일</option>
								<option value="LIST_ONLY">리스트만(이미지제외)</option>
								<option value="LIST_WITH_IMAGE">리스트만(이미지포함)</option>
								<option value="DETAIL_MIDDLE_SIZE">상품디테일_중간사이즈</option>
							</select>
							<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>
						</td>
					</tr>
					<tr class="display_none">
						<th>이메일</th>
						<td class="line">
							<input type="text" class="txt" name="sent_email" value="<?=$SENT_EMAIL?>" style="width: 80%;" placeholder="복수의 메일을 보내실 때에는 ',' 혹은 ';'를 빈칸없이 붙여서 입력해주세요."/> 
						</td>
						<th>발송여부</th>
						<td colspan="2"><?=$IS_SENT == "Y" ? "<font color='green'>".$SENT_DATE."</font>" : "<font color='red'>발송전</font>"?> &nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="b0" value=" 제안서 발송 " class="btntxt" onclick="javascript:js_send_email('<?=$IS_SENT?>');">
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
					<col width="30%" />
					<col width="10%" />
					<col width="30%" />
					<col width="*" />
				</colgroup>
				<tbody>
					
					<tr>
						<th>전표번호</th>
						<td class="line">
							<input type="text" class="txt" name="group_no" value="<?=$GROUP_NO?>" style="width: 100px;"/>
						</td>
						<th>제안업체</th>
						<td class="line">
							<input type="text" class="txt" class="autocomplete_off" style="width:90%" placeholder="업체(명/코드) 입력후 엔터를 눌러주세요" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'',$CP_NO)?>" />
								<input type="hidden" name="cp_type" value="<?=$CP_NO?>">


								<script>
									$(function(){

										$("input[name=txt_cp_type]").keydown(function(e){

											if(e.keyCode==13) { 

												var keyword = $(this).val();
												if(keyword == "") { 
													$("input[name=cp_type]").val('');
												} else { 
													$.getJSON( "../company/json_company_list.php?term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
														if(data.length == 1) { 
															
															js_selecting_company("txt_cp_type", data[0].label, "cp_type", data[0].id, {'DC_RATE': data[0].dc_rate});

														} else if(data.length > 1){ 
															NewWindow("../company/pop_company_searched_list.php?search_str="+keyword + "&target_name=txt_cp_type&target_value=cp_type",'pop_company_searched_list','950','650','YES');

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

									function js_selecting_company(target_name, cp_nm, target_value, cp_no, cp_options = null) {
										
										$(function(){

											$("input[name="+target_name+"]").val(cp_nm);
											$("input[name="+target_value+"]").val(cp_no);
	
											if(cp_options != null) {
												$("input[name=dc_rate]").val(cp_options.DC_RATE);
											}
										});

									}

								</script>
							
						</td>
						<td>
							(할인율 : <input type="text" name="dc_rate" value="<?=$DC_RATE?>" class="txt" style="width:20px;"/> % <input type="button" name="bb" value="일괄적용" onclick="javascript:js_update_dc_rate();" />)
							
						</td>
					</tr>
				</tbody>
			</table>
			<div class="sp20"></div>
			<h3>제안상품 추가</h3>
			<table cellpadding="0" cellspacing="0" class="colstable02">
				<colgroup>
					<col width="15%" />
					<col width="*" />
				</colgroup>
				<tbody>
					<? 
						if($gp_no == "") { 

							$con_cate = "";
							$exclude_category = "";
					?>
					<tr>
						<th>카테고리 상품</th>
						<td class="line">
							<?= makeCategorySelectBoxOnChange($conn, $con_cate, $exclude_category);?>
							<input type="button" name="b" value=" 추가 " onclick="js_insert_category();"/>
						</td>
					</tr>
					<? } ?>
					<tr class="set_goods">
						<th>개별 상품</th>
						<td style="position:relative" class="line">
							<div id="suggest" style="position:absolute; left:15px; top:25px; width:95%; height:81px; z-index:0; visibility: hidden; width:95%; ">
								<div id="suggestList" style="position:relative; border:solid 1px #cec6ba; background:#FFFFFF; width:95%;"></div>
							</div>
							<input type="text" class="txt search_name" style="width:75%; ime-mode:Active;" name="search_name" value="" onKeyDown="startSuggest();" onFocus="this.value='';" />

								<?
									if($gp_no != "") { 
								?>
								<a href="javascript:js_update();"><img src="../images/admin/btn_confirm.gif" alt="확인"></a>
								<?  }  else {?>
								<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인"></a>
								<?  } ?>

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
								<col width="8%" />
								<col width="8%" />
							</colgroup>
							<thead>
								<tr>
									<th colspan="10" class="line">상품을 검색해서 선택하시면 아래에 자재가 추가됩니다</th>
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
			?>

			$(".sub_goods_list").append("<tr>"+
				"<th>상품명</th><td class='line'><?=$arr_rs_goods[$i]["GOODS_NAME"]?>[<?=$arr_rs_goods[$i]["GOODS_CODE"]?>]"+
				"<input type='hidden' name='sub_gpg_no[]' value='<?=$arr_rs_goods[$i]["GPG_NO"]?>'>"+
				"<input type='hidden' name='sub_goods_name[]' value='<?=$arr_rs_goods[$i]["GOODS_NAME"]?>'>"+
				"<input type='hidden' name='sub_goods_id[]' value='<?=$arr_rs_goods[$i]["GOODS_NO"]?>'></td>"+
				"<th>박스입수</th><td class='line'><input type='hidden' name='sub_delivery_cnt_in_box[]' value='<?=$arr_rs_goods[$i]["DELIVERY_CNT_IN_BOX"]?>'><?=$arr_rs_goods[$i]["DELIVERY_CNT_IN_BOX"]?></td>"+
				"<th>기프트넷단가</th><td class='line'><input type='text' name='sub_retail_price[]' class='txt' style='width:70%' value='<?=$arr_rs_goods[$i]["RETAIL_PRICE"]?>'>원</td>"+
				"<th>벤더제안가</th><td class='line'>"+
				"<input type='text' name='sub_proposal_price[]' class='txt' style='width:70%' value='<?=$arr_rs_goods[$i]["PROPOSAL_PRICE"]?>'>원</td>"+
				"<td class='line'>" + 
				"<span class='remove_sub' style='color:#478fb2; cursor:pointer; font-weight:bold; text-decoration:underline;'>삭제</span>" +
				"<td class='line'><input type='button' onclick='javascript:js_pop_proposal_goods(<?=$arr_rs_goods[$i]["GPG_NO"]?>);' value='보기/수정'></td>"+
				"</td>"+
				"</tr>");	
			<?
				} 
			?>
			</script>
			<div class="sp10"></div>
			<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="10%" />
					<col width="37%" />
					<col width="10%" />
					<col width="37%" />
					<col width="6%" />
				</colgroup>
				<tbody>
				<tr>
						<th>메모</th>
						<td class="line" colspan="4">
							<textarea name="memo" rows="7" style="width:90%;"><?=$MEMO?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
			<iframe src="about:blank" name="ifr_hidden" id="ifr_hidden" frameborder="no" width="100%" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
		</form>
		<?
			if($gp_no != "" && sizeof($arr_rs_goods) <= 50) { 
		?>
		<iframe name="preview" src="/manager/proposal/pop_goods_proposal.php?gp_no=<?=$gp_no?>" frameborder="no" width="100%" height="800px" marginwidth="0" marginheight="0" border="1"></iframe>
		<?
			} else if(sizeof($arr_rs_goods) > 50){ 
		?>
			<div style="font-size:16px; font-weight:bold; height:50px; line-height:50px; text-align:center;">데이터가 50개 이상일 경우 용량 문제로 숨겨두었습니다.</div> 
		<? 
			} else { 
		?>
			<div style="font-size:16px; font-weight:bold; height:50px; line-height:50px; text-align:center;">데이터가 없습니다.</div> 
		<?  } ?>
		</td>

	</tr>
	</table>
	<div class="sp20"></div>
	        
</div>

</body>
<iframe src="about:blank" name="ifr_hidden" id="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>