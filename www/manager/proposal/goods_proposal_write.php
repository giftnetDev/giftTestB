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
	$menu_right = "GD007"; // �޴����� ���� �� �־�� �մϴ�

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
		alert('���� ó�� �Ǿ����ϴ�.');
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
		alert('���� ó�� �Ǿ����ϴ�.');
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

			//�Ǹ���, ǰ���� �����ϵ��� �� 2017-06-21
			if($cate_04 != "�Ǹ���") continue;

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
		//alert('���� ó�� �Ǿ����ϴ�.');
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
		alert('���� ó�� �Ǿ����ϴ�.');
	//	location.href =  "goods_proposal_write.php?gp_no=<?=$gp_no?>";
</script>
<?
	}


	if($mode == "PROPOSAL_CONFIRM") { 
		updateGoodsProposalSentEmail($conn, $gp_no, $request_type, $sent_email = "", $email_subject = "", $email_body = "");
?>
<script language="javascript">
		alert('���� ó�� �Ǿ����ϴ�.');
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
		$filename = "����Ʈ��_���ȼ�(GP_".$gp_no.").xls";
		$file = $path . "/" . $filename;
		
		downloadFile($download_url, $file);

		//���ü�� �ƴҰ�� �ش� ��ü�� ��������� �� - ������ ���ü�� ����Ѵٰ� ����
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
			
			$EMAIL_BODY = str_replace("[ȸ���]", $OP_CP_NM, $EMAIL_BODY);
			$EMAIL_BODY = str_replace("[�߽���]", $s_adm_nm, $EMAIL_BODY);
			$EMAIL_BODY = str_replace("[����]", "\r\n", $EMAIL_BODY);

			//��ü������ ���� �ּ� �Ѱ��ۿ� ����� �ȵǾ� �ӽ� �ϵ��ڵ� ó��
			//$OP_EMAIL = "gift@giftnet.co.kr";

			$sent_email = str_replace(";", ",", $sent_email);

			mailer($OP_CP_NM, $FROM_EMAIL, $sent_email, $sent_email, $email_subject, $EMAIL_BODY, $path, $filename);

			//���Ϲ߼ۻ�Ȳ ������Ʈ
			updateGoodsProposalSentEmail($conn, $gp_no, $request_type, $sent_email, $email_subject, $EMAIL_BODY);
	?>
	<script language="javascript">
			alert('���� ó�� �Ǿ����ϴ�.');
	</script>
	<?
		} else {
	?>
	<script language="javascript">
			alert('�����Դϴ� ������ ��ǥ�� �´���, �̸��� �ּҰ� �ִ��� Ȯ�κ�Ź�帳�ϴ�.');
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

		//���ü�� �ƴҰ�� �ش� ��ü�� ��������� �� - ������ ���ü�� ����Ѵٰ� ����
		$arr_op_cp = getOperatingCompany($conn, $s_adm_com_code);
		$OP_CP_NM = $arr_op_cp[0]["CP_NM"]." ".$arr_op_cp[0]["CP_NM2"];
		$OP_CEO_NM = $arr_op_cp[0]["CEO_NM"];
		$OP_CP_ADDR = $arr_op_cp[0]["CP_ADDR"];
		$OP_CP_PHONE = $arr_op_cp[0]["CP_PHONE"];
		$OP_EMAIL = $arr_op_cp[0]["EMAIL"];


		if($EMAIL_SUBJECT == "")
			$EMAIL_SUBJECT = $OP_CP_NM." ����_".trim($CP_NM)."_".date("Ymd", strtotime("0 month"));

		
		if($EMAIL_BODY == "") { 

			$EMAIL_BODY = "�ȳ��ϼ���. ".$OP_CP_NM." ".$s_adm_nm."�Դϴ�. \r\n\r\n".
							 
							 "���ȼ� Ȯ�� ��Ź�帳�ϴ�  \r\n\r\n".

							 "(ȸ�� �����ּ� : gift@giftnet.co.kr) \r\n\r\n";
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
		$MEMO = "* �ΰ��� ���� \n";
		$MEMO .="* �ڽ����� �ϰ���۽� �ù�� ����\n";
		$MEMO .="* �������, �ڽ����� ä������ ���� �� ���� ������ 3,000�� ���� \n";
		$MEMO .="* ����, ��ƼĿ �۾��� ����\n";
		$MEMO .="* �����갣, ���ֵ� ��۽� �߰� ��� ���� \n";
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
		
		//�ε� ���ڸ��� ����ϸ�ũ �ɼ� �ƴϸ� ��ư ����
		if($(this).val() == "RT005") {
			$("#btnCreateLink").show();
			$("#btnCreateImage").show();
		} else {
			$("#btnCreateLink").hide();
			$("#btnCreateImage").hide();
		}

		//�߼۹�� ���� ����Ʈ ���� �̺�Ʈ ���ε�
		$("select[name=request_type]").on("change", function(){
			//�̸��� ���� �߼� �ɼ��� ���
			if($(this).val() == "RT001") {
				$(".display_none").show();
				$("input[type=button][name=a0]").hide();
			} else {
				$(".display_none").hide();
				$("input[type=button][name=a0]").show();
			}

			//����� ��ũ �ɼ��� ���
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
				//�̸����� ����
				$("iframe[name='preview']").attr('src', "/manager/proposal/pop_goods_proposal.php?gp_no=<?=$gp_no?>"+"&print_type=DETAIL_MIDDLE_SIZE&discount_percent="+frm.discount_percent.value);
			} else {
				$("input[name='discount_percent']").attr("style","visibility:hidden");
				//�̸����� ����
				$("iframe[name='preview']").attr('src', "/manager/proposal/pop_goods_proposal.php?gp_no=<?=$gp_no?>");
			}
		});
		$("input[name='discount_percent']").on("change", function(){
			$("iframe[name='preview']").attr('src', "/manager/proposal/pop_goods_proposal.php?gp_no=<?=$gp_no?>"+"&print_type=DETAIL_MIDDLE_SIZE&discount_percent="+frm.discount_percent.value);
		});

		
		//�̸��� ����߼� �ɼ��� ��쿡�� �̸��� ������ ��ư�� ���̰�, ���� ó�� �Ϸ� ��ư�� �����.
		if($("select[name=request_type]").val() == "RT001") {
			$(".display_none").show();
			$("input[type=button][name=a0]").hide();
		} else {
			$(".display_none").hide();
			$("input[type=button][name=a0]").show();
		}

		//��ũ ���� ��ư Ŭ�� �̺�Ʈ ���ε�
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
						alert("�����Ͽ����ϴ�.");
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
				alert("��ũ�� ����Ǿ����ϴ�.\n\n"+link);
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
						alert("�����Ͽ����ϴ�.");
					}
				}, error: function(jqXHR, textStatus, errorThrown) {
					console.log(jqXHR.responseText); 
				}
			});
		});
	});//ready
  </script>
  <script type="text/javascript" >

	// ��ȸ ��ư Ŭ�� �� 
	function js_save() {
		var frm = document.frm;
		
		frm.target = "";
		frm.method = "post";

		if(frm.cp_type.value == '' || frm.cp_type.value == '0' ) { 
			alert("���Ⱦ�ü�� �Է����ֽʽÿ�.");
			return;
		}

		frm.mode.value = "INSERT_GOODS_PROPOSAL";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_update() {
		var frm = document.frm;
		
		if(frm.cp_type.value == '' || frm.cp_type.value == '0' ) { 
			alert("���Ⱦ�ü�� �Է����ֽʽÿ�.");
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
			bOK = confirm('�̹� �߼��ϼ̴µ� ��߼� �Ͻðڽ��ϱ�?');
		
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
			alert("���Ⱦ�ü�� �Է����ֽʽÿ�.");
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

	// tag ���� ���̾ �� �ε� �Ǳ������� �Ǻ��ϱ� ���� �ʿ�
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
				if(arr_keywordList[8] != "�Ǹ���" && arr_keywordList[8] != "ǰ��")
					html += "<td style='color:gray;'>" + arr_keywordList[1] + "</td>";
				else
					html += "<td>" +"<a href=\"javascript:js_select('"+ arr_keywordList[0]+"','"+arr_keywordList[1]+"')\">"+arr_keywordList[1]+"</a>" + "</td>";
				html += "<td width='155px'>����Ʈ�ݴܰ� : "+arr_keywordList[10]+"</td>";
				html += "<td width='155px'>���Ȱ� : "+arr_keywordList[3]+"</td>";
				html += "<td width='105px'>�ڽ��Լ� : "+arr_keywordList[9]+"</td>";
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
				alert('�̹� �߰��� ��ǰ�Դϴ�. Ȯ���ϰ� �������ּ���.');
		}
		
		if(arr_keywordValues[14] == "�Ǹ���")
			$(".sub_goods_list").append("<tr><th>��ǰ��</th><td class='line'>" + arr_keywordValues[0] + "["+ arr_keywordValues[4] + "]" + "<input type='hidden' name='sub_gpg_no[]' value=''><input type='hidden' name='sub_goods_name[]' value='" + arr_keywordValues[0] + "'><input type='hidden' name='sub_goods_id[]' value='" + arr_keywordValues[1] + "'></td><th>�ڽ��Լ�</th><td class='line' ><input type='hidden' name='sub_delivery_cnt_in_box[]' value='"+arr_keywordValues[11]+"'/>"+arr_keywordValues[11]+"</td><th>����Ʈ�ݴܰ�</th><td class='line'><input type='text' name='sub_retail_price[]' class='txt' style='width:70%' value='"+arr_keywordValues[13]+"'/>��</td><th>�������Ȱ�</th><td class='line'><input type='text' name='sub_proposal_price[]' class='txt' style='width:70%' value='"+arr_keywordValues[3]+"'>��</td><td class='line'><span class='remove_sub' style='color:#478fb2; cursor:pointer; font-weight:bold; text-decoration:underline;'>����</span></td><td class='line'></td></tr>");
		else
			$(".sub_goods_list").append("<tr><th>��ǰ��</th><td class='line'>" + arr_keywordValues[0] + " / <span style='color:red;'>" + arr_keywordValues[14] + "</span>["+ arr_keywordValues[4] + "]" + "<input type='hidden' name='sub_gpg_no[]' value=''><input type='hidden' name='sub_goods_name[]' value='" + arr_keywordValues[0] + "'><input type='hidden' name='sub_goods_id[]' value='" + arr_keywordValues[1] + "'></td><th>�ڽ��Լ�</th><td class='line' ><input type='hidden' name='sub_delivery_cnt_in_box[]' value='"+arr_keywordValues[11]+"'/>"+arr_keywordValues[11]+"</td><th>����Ʈ�ݴܰ�</th><td class='line'><input type='text' name='sub_retail_price[]' class='txt' style='width:70%' value='"+arr_keywordValues[13]+"'/>��</td><th>�������Ȱ�</th><td class='line'><input type='text' name='sub_proposal_price[]' class='txt' style='width:70%' value='"+arr_keywordValues[3]+"'>��</td><td class='line'><span class='remove_sub' style='color:#478fb2; cursor:pointer; font-weight:bold; text-decoration:underline;'>����</span></td><td class='line'></td></tr>");

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

				<h2>���� ����</h2>
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
						<th>�߼۹�� ����</th>
						<td class="line">
							<?= makeSelectBox($conn,"REQUEST_TYPE","request_type","125","����","",$request_type)?>
						</td>
						<td class="line" colspan="2">
							<? if($IS_SENT != "Y") { ?>
							<input type="button" name="a0" value=" ���� ó�� �Ϸ� " class="btntxt" onclick="javascript:js_proposal_confirm();">
							<? } ?>
							<input type="button" value=" ��ũ ���� " class="btntxt" id="btnCreateLink">
							<input type="button" value=" �̹����� ���� " class="btntxt" id="btnCreateImage">
						</td>
						<td align="right">
							<input type="text" name="discount_percent" placeholder="�ܰ�������"/>
							<select name="print_type">
								<option value="ALL">����Ʈ+��ǰ������</option>
								<option value="LIST_ONLY">����Ʈ��(�̹�������)</option>
								<option value="LIST_WITH_IMAGE">����Ʈ��(�̹�������)</option>
								<option value="DETAIL_MIDDLE_SIZE">��ǰ������_�߰�������</option>
							</select>
							<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="���� ����Ʈ" /></a>
						</td>
					</tr>
					<tr class="display_none">
						<th>�̸���</th>
						<td class="line">
							<input type="text" class="txt" name="sent_email" value="<?=$SENT_EMAIL?>" style="width: 80%;" placeholder="������ ������ ������ ������ ',' Ȥ�� ';'�� ��ĭ���� �ٿ��� �Է����ּ���."/> 
						</td>
						<th>�߼ۿ���</th>
						<td colspan="2"><?=$IS_SENT == "Y" ? "<font color='green'>".$SENT_DATE."</font>" : "<font color='red'>�߼���</font>"?> &nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="b0" value=" ���ȼ� �߼� " class="btntxt" onclick="javascript:js_send_email('<?=$IS_SENT?>');">
						</td>
						
					</tr>
					<tr class="display_none">
						<th>���� ����</th>
						<td class="line" colspan="4">
							<input type="text" class="txt" name="email_subject" value="<?=$EMAIL_SUBJECT?>" style="width: 85%;"/> 
						</td>
					</tr>
					<tr class="display_none">
						<th>���� ����</th>
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
						<th>��ǥ��ȣ</th>
						<td class="line">
							<input type="text" class="txt" name="group_no" value="<?=$GROUP_NO?>" style="width: 100px;"/>
						</td>
						<th>���Ⱦ�ü</th>
						<td class="line">
							<input type="text" class="txt" class="autocomplete_off" style="width:90%" placeholder="��ü(��/�ڵ�) �Է��� ���͸� �����ּ���" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'',$CP_NO)?>" />
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
															alert("�˻������ �����ϴ�.");
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
							(������ : <input type="text" name="dc_rate" value="<?=$DC_RATE?>" class="txt" style="width:20px;"/> % <input type="button" name="bb" value="�ϰ�����" onclick="javascript:js_update_dc_rate();" />)
							
						</td>
					</tr>
				</tbody>
			</table>
			<div class="sp20"></div>
			<h3>���Ȼ�ǰ �߰�</h3>
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
						<th>ī�װ��� ��ǰ</th>
						<td class="line">
							<?= makeCategorySelectBoxOnChange($conn, $con_cate, $exclude_category);?>
							<input type="button" name="b" value=" �߰� " onclick="js_insert_category();"/>
						</td>
					</tr>
					<? } ?>
					<tr class="set_goods">
						<th>���� ��ǰ</th>
						<td style="position:relative" class="line">
							<div id="suggest" style="position:absolute; left:15px; top:25px; width:95%; height:81px; z-index:0; visibility: hidden; width:95%; ">
								<div id="suggestList" style="position:relative; border:solid 1px #cec6ba; background:#FFFFFF; width:95%;"></div>
							</div>
							<input type="text" class="txt search_name" style="width:75%; ime-mode:Active;" name="search_name" value="" onKeyDown="startSuggest();" onFocus="this.value='';" />

								<?
									if($gp_no != "") { 
								?>
								<a href="javascript:js_update();"><img src="../images/admin/btn_confirm.gif" alt="Ȯ��"></a>
								<?  }  else {?>
								<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="Ȯ��"></a>
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
									<th colspan="10" class="line">��ǰ�� �˻��ؼ� �����Ͻø� �Ʒ��� ���簡 �߰��˴ϴ�</th>
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
				"<th>��ǰ��</th><td class='line'><?=$arr_rs_goods[$i]["GOODS_NAME"]?>[<?=$arr_rs_goods[$i]["GOODS_CODE"]?>]"+
				"<input type='hidden' name='sub_gpg_no[]' value='<?=$arr_rs_goods[$i]["GPG_NO"]?>'>"+
				"<input type='hidden' name='sub_goods_name[]' value='<?=$arr_rs_goods[$i]["GOODS_NAME"]?>'>"+
				"<input type='hidden' name='sub_goods_id[]' value='<?=$arr_rs_goods[$i]["GOODS_NO"]?>'></td>"+
				"<th>�ڽ��Լ�</th><td class='line'><input type='hidden' name='sub_delivery_cnt_in_box[]' value='<?=$arr_rs_goods[$i]["DELIVERY_CNT_IN_BOX"]?>'><?=$arr_rs_goods[$i]["DELIVERY_CNT_IN_BOX"]?></td>"+
				"<th>����Ʈ�ݴܰ�</th><td class='line'><input type='text' name='sub_retail_price[]' class='txt' style='width:70%' value='<?=$arr_rs_goods[$i]["RETAIL_PRICE"]?>'>��</td>"+
				"<th>�������Ȱ�</th><td class='line'>"+
				"<input type='text' name='sub_proposal_price[]' class='txt' style='width:70%' value='<?=$arr_rs_goods[$i]["PROPOSAL_PRICE"]?>'>��</td>"+
				"<td class='line'>" + 
				"<span class='remove_sub' style='color:#478fb2; cursor:pointer; font-weight:bold; text-decoration:underline;'>����</span>" +
				"<td class='line'><input type='button' onclick='javascript:js_pop_proposal_goods(<?=$arr_rs_goods[$i]["GPG_NO"]?>);' value='����/����'></td>"+
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
						<th>�޸�</th>
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
			<div style="font-size:16px; font-weight:bold; height:50px; line-height:50px; text-align:center;">�����Ͱ� 50�� �̻��� ��� �뷮 ������ ���ܵξ����ϴ�.</div> 
		<? 
			} else { 
		?>
			<div style="font-size:16px; font-weight:bold; height:50px; line-height:50px; text-align:center;">�����Ͱ� �����ϴ�.</div> 
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