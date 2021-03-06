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
	$menu_right = "OD024"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/estimate/estimate.php";
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

	if($mode == "INSERT_GOODS_ESTIMATE") {
		//천단위 구분기호 제거
		$TOTAL_QTY = str_replace(",","",$TOTAL_QTY);
		$TOTAL_SALE_PRICE = str_replace(",","",$TOTAL_SALE_PRICE);
		$TOTAL_DISCOUNT_PRICE = str_replace(",","",$TOTAL_DISCOUNT_PRICE);
		$TOTAL_SA_DELIVERY_PRICE = str_replace(",","",$TOTAL_SA_DELIVERY_PRICE);
		$GRAND_TOTAL_SALE_PRICE = str_replace(",","",$GRAND_TOTAL_SALE_PRICE);

		$cp_no = $cp_type;
		$row_cnt = count($sub_goods_id);
		
		$gp_no = insertGoodsEstimate($conn, $group_no, $cp_no, $dc_rate, nl2br($memo), $s_adm_no,$TOTAL_QTY,$TOTAL_SALE_PRICE,$TOTAL_DISCOUNT_PRICE,$TOTAL_SA_DELIVERY_PRICE,$GRAND_TOTAL_SALE_PRICE);
 
		for ($k = 0; $k < $row_cnt; $k++) {

			// $goods_no			= $sub_goods_id[$k];
			// $goods_nm			= $sub_goods_name[$k];
			// $delivery_cnt_in_box= $sub_delivery_cnt_in_box[$k];
			// $retail_price       = $sub_retail_price[$k];
			// $estimate_price     = $sub_estimate_price[$k];
			// $qty				= $sub_qty[$k];
			// $supply_price		= $sub_supply_price[$k];
			
			//천단위 구분기호 제거
			$goods_no			= str_replace(",","",$sub_goods_id[$k]);
			$goods_nm			= str_replace(",","",$sub_goods_name[$k]);
			$delivery_cnt_in_box= str_replace(",","",$sub_delivery_cnt_in_box[$k]);
			$retail_price       = str_replace(",","",$sub_retail_price[$k]);
			$estimate_price		= str_replace(",","",$sub_estimate_price[$k]);
			$qty				= str_replace(",","",$sub_qty[$k]);
			$supply_price		= str_replace(",","",$sub_supply_price[$k]);

			
			//echo "qty : $qty, supply_price : $supply_price";
			$result = insertGoodsEstimateGoods($conn, $gp_no, $group_no, $goods_no, $goods_nm, $delivery_cnt_in_box, $retail_price, $estimate_price, $s_adm_no, $qty, $supply_price);
		}
		

?>
<script language="javascript">
		alert('정상 처리 되었습니다.');
		location.href =  "goods_estimate_write.php?gp_no=<?=$gp_no?>";
</script>
<?
	}

	if($mode == "UPDATE_GOODS_ESTIMATE") {
		
		//천단위 구분기호 제거
		$TOTAL_QTY = str_replace(",","",$TOTAL_QTY);
		$TOTAL_SALE_PRICE = str_replace(",","",$TOTAL_SALE_PRICE);
		$TOTAL_DISCOUNT_PRICE = str_replace(",","",$TOTAL_DISCOUNT_PRICE);
		$TOTAL_SA_DELIVERY_PRICE = str_replace(",","",$TOTAL_SA_DELIVERY_PRICE);
		$GRAND_TOTAL_SALE_PRICE = str_replace(",","",$GRAND_TOTAL_SALE_PRICE);
		
		updateEstimate($conn, $dc_rate, nl2br($memo), $gp_no, $s_adm_no,$TOTAL_QTY,$TOTAL_SALE_PRICE,$TOTAL_DISCOUNT_PRICE,$TOTAL_SA_DELIVERY_PRICE,$GRAND_TOTAL_SALE_PRICE);
		$arr_rs_goods = listGoodsEstimateGoods($conn, $gp_no, 'N');
		$row_cnt = count($sub_goods_id);

		for ($k = 0; $k < $row_cnt; $k++) {
			//천단위 구분기호 제거
			$gpg_no				= str_replace(",","",$sub_gpg_no[$k]);
			$goods_no			= str_replace(",","",$sub_goods_id[$k]);
			$goods_nm			= str_replace(",","",$sub_goods_name[$k]);
			$delivery_cnt_in_box= str_replace(",","",$sub_delivery_cnt_in_box[$k]);
			$retail_price       = str_replace(",","",$sub_retail_price[$k]);
			$estimate_price		= str_replace(",","",$sub_estimate_price[$k]);
			$qty				= str_replace(",","",$sub_qty[$k]);
			$supply_price		= str_replace(",","",$sub_supply_price[$k]);

			if($gpg_no <> "") {
				$result = updateGoodsEstimateGoods($conn, $goods_no, $goods_nm, $delivery_cnt_in_box, $retail_price, $estimate_price, $gpg_no, $s_adm_no, $qty, $supply_price);
			} else {
				$result = insertGoodsEstimateGoods($conn, $gp_no, $group_no, $goods_no, $goods_nm, $delivery_cnt_in_box, $retail_price, $estimate_price, $s_adm_no,  $qty, $supply_price);
			}

			for($i = 0; $i < sizeof($arr_rs_goods); $i++) {
				if($arr_rs_goods[$i]["GPG_NO"] == $gpg_no)
					$arr_rs_goods[$i]["EXISTS"] = 'Y';
			}
		}

		for($i = 0; $i < sizeof($arr_rs_goods); $i++) {
			if($arr_rs_goods[$i]["EXISTS"] == "")
				deleteGoodsEstimateGoods($conn, $arr_rs_goods[$i]["GPG_NO"], $s_adm_no);
		}

?>
<script language="javascript">
		alert('정상 처리 되었습니다.');
		location.href =  "goods_estimate_write.php?gp_no=<?=$gp_no?>";
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

		$gp_no = insertGoodsEstimate($conn, $group_no, $cp_no, $dc_rate, nl2br($memo), $s_adm_no, $goods_cate);

		for($i = 0; $i < sizeof($arr_rs); $i++) { 
			$goods_nm			 = $arr_rs[$i]["GOODS_NAME"];
			$goods_no			 = $arr_rs[$i]["GOODS_NO"];
			$delivery_cnt_in_box = $arr_rs[$i]["DELIVERY_CNT_IN_BOX"];
			$retail_price		 = $arr_rs[$i]["SALE_PRICE"];
			$goods_cate			 = $arr_rs[$i]["GOODS_CATE"];
			$page				 = $arr_rs[$i]["PAGE"];
			$seq				 = $arr_rs[$i]["SEQ"];
			$cate_04			 = $arr_rs[$i]["CATE_04"];

			//판매중, 품절만 견적하도록 함 2017-06-21
			if($cate_04 != "판매중") continue;

			$arr_goods = selectGoodsPriceOnly($conn, $goods_no);
			if(sizeof($arr_goods) > 0) { 
				$price = $arr_goods[0]["PRICE"];
				$sale_price = $arr_goods[0]["SALE_PRICE"];
				$estimate_price = getCompanyGoodsPriceOrDCRate($conn, $goods_no, $sale_price, $price, $cp_no, $dc_rate);

			}

			$result = insertGoodsEstimateGoods($conn, $gp_no, $group_no, $goods_no, $goods_nm, $delivery_cnt_in_box, $retail_price, $estimate_price, $s_adm_no, $goods_cate, $page, $seq);
		}

?>
<script language="javascript">
		//alert('정상 처리 되었습니다.');
		//location.href =  "goods_estimate_write.php?gp_no=<?=$gp_no?>";
</script>
<?
	}

	if($mode == "UPDATE_DC_RATE") {
		//천단위 구분기호 제거
		$TOTAL_QTY = str_replace(",","",$TOTAL_QTY);
		$TOTAL_SALE_PRICE = str_replace(",","",$TOTAL_SALE_PRICE);
		$TOTAL_DISCOUNT_PRICE = str_replace(",","",$TOTAL_DISCOUNT_PRICE);
		$TOTAL_SA_DELIVERY_PRICE = str_replace(",","",$TOTAL_SA_DELIVERY_PRICE);
		$GRAND_TOTAL_SALE_PRICE = str_replace(",","",$GRAND_TOTAL_SALE_PRICE);

		$arr_rs_goods = listGoodsEstimateGoods($conn, $gp_no, 'N');
		$row_cnt = count($sub_goods_id);
		$TOTAL_SALE_PRICE		= 0;
		for ($k = 0; $k < $row_cnt; $k++) {

			// $gpg_no				= $sub_gpg_no[$k];
			// $goods_no			= $sub_goods_id[$k];
			// $goods_nm			= $sub_goods_name[$k];
			// $delivery_cnt_in_box= $sub_delivery_cnt_in_box[$k];
			// $retail_price       = $sub_retail_price[$k];
			// $qty				= $sub_qty[$k];

			//천단위 구분기호 제거
			$gpg_no				= str_replace(",","",$sub_gpg_no[$k]);
			$goods_no			= str_replace(",","",$sub_goods_id[$k]);
			$goods_nm			= str_replace(",","",$sub_goods_name[$k]);
			$delivery_cnt_in_box= str_replace(",","",$sub_delivery_cnt_in_box[$k]);
			$retail_price       = str_replace(",","",$sub_retail_price[$k]);
			$qty				= str_replace(",","",$sub_qty[$k]);

			//$estimate_price		= $sub_estimate_price[$k];

			$arr_goods = selectGoodsPriceOnly($conn, $goods_no);
			if(sizeof($arr_goods) > 0) { 
				$price = $arr_goods[0]["PRICE"];
				$sale_price = $arr_goods[0]["SALE_PRICE"];

				//echo $goods_no." / ".$sale_price." / ".$price." / ".$cp_type." / ".$dc_rate."<br/>";

				$estimate_price = getCompanyGoodsPriceOrDCRate($conn, $goods_no, $sale_price, $price, $cp_type, $dc_rate);
			}
			$supply_price		=  $qty * $estimate_price;
			$TOTAL_SALE_PRICE	+= $supply_price;
			//echo $retail_price." / ";
			//echo $estimate_price."<br/>";

			if($gpg_no <> "") {
				$result = updateGoodsEstimateGoods($conn, $goods_no, $goods_nm, $delivery_cnt_in_box, $retail_price, $estimate_price, $gpg_no, $s_adm_no, $qty, $supply_price);
			} else {
				$result = insertGoodsEstimateGoods($conn, $gp_no, $group_no, $goods_no, $goods_nm, $delivery_cnt_in_box, $retail_price, $estimate_price, $s_adm_no, $qty, $supply_price);
				
			}

			for($i = 0; $i < sizeof($arr_rs_goods); $i++) {
				if($arr_rs_goods[$i]["GPG_NO"] == $gpg_no)
					$arr_rs_goods[$i]["EXISTS"] = 'Y';
			}

		}
		
		for($i = 0; $i < sizeof($arr_rs_goods); $i++) {

			if($arr_rs_goods[$i]["EXISTS"] == "")
				deleteGoodsEstimateGoods($conn, $arr_rs_goods[$i]["GPG_NO"], $s_adm_no);
		}

		$GRAND_TOTAL_SALE_PRICE	= $TOTAL_SALE_PRICE - $TOTAL_DISCOUNT_PRICE;
		
		updateEstimate($conn, $dc_rate, nl2br($memo), $gp_no, $s_adm_no,$TOTAL_QTY,$TOTAL_SALE_PRICE,$TOTAL_DISCOUNT_PRICE,$TOTAL_SA_DELIVERY_PRICE,$GRAND_TOTAL_SALE_PRICE);


?>
<script language="javascript">
		alert('정상 처리 되었습니다.');
		location.href =  "goods_estimate_write.php?gp_no=<?=$gp_no?>";
</script>
<?
	}


	if($mode == "ESTIMATE_CONFIRM") { 
		updateGoodsEstimateSentEmail($conn, $gp_no, $request_type, $sent_email = "", $email_subject = "", $email_body = "");
?>
<script language="javascript">
		alert('정상 처리 되었습니다.');
		location.href =  "goods_estimate_write.php?gp_no=<?=$gp_no?>";
</script>
<?
	}

	if($mode == "SEND_EMAIL") { 

		$download_url = "http://".$_SERVER['HTTP_HOST']."/manager/estimate/estimate_sheet_excel.php?gp_no=".base64url_encode($gp_no)."&print_type=".$print_type;
		$path = $_SERVER["DOCUMENT_ROOT"]."/upload_data/temp_mail";
		$filename = "기프트넷_견적서(GP_".$gp_no.").xls";
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

			//업체정보에 메일 주소 한개밖에 등록이 안되어 임시 하드코딩 처리
			$OP_EMAIL = "gift@giftnet.co.kr";

			$sent_email = str_replace(";", ",", $sent_email);

			mailer($OP_CP_NM, $OP_EMAIL, $sent_email, $sent_email, $email_subject, $email_body, $path, $filename);

			//메일발송상황 업데이트
			updateGoodsEstimateSentEmail($conn, $gp_no, $request_type, $sent_email, $email_subject, $email_body);
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
		$arr_rs = selectGoodsEstimateByGpNo($conn, $gp_no);

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

		//토탈류 컬럼 추가
		$TOTAL_QTY					= $arr_rs[0]["TOTAL_QTY"];
		$TOTAL_SALE_PRICE			= $arr_rs[0]["TOTAL_SALE_PRICE"];
		$TOTAL_DISCOUNT_PRICE		= $arr_rs[0]["TOTAL_DISCOUNT_PRICE"];
		$TOTAL_SA_DELIVERY_PRICE	= $arr_rs[0]["TOTAL_SA_DELIVERY_PRICE"];
		$GRAND_TOTAL_SALE_PRICE		= $arr_rs[0]["GRAND_TOTAL_SALE_PRICE"];
		
		if($SENT_DATE == "0000-00-00 00:00:00")
			$SENT_DATE = "";
		else
			$SENT_DATE = date("Y-m-d H:i", strtotime($SENT_DATE));

		$arr_rs_goods = listGoodsEstimateGoods($conn, $gp_no, 'N');

		//운영업체가 아닐경우 해당 업체로 변경해줘야 함 - 지금은 운영업체만 사용한다고 가정
		$arr_op_cp = getOperatingCompany($conn, $s_adm_com_code);
		$OP_CP_NM = $arr_op_cp[0]["CP_NM"]." ".$arr_op_cp[0]["CP_NM2"];
		$OP_CEO_NM = $arr_op_cp[0]["CEO_NM"];
		$OP_CP_ADDR = $arr_op_cp[0]["CP_ADDR"];
		$OP_CP_PHONE = $arr_op_cp[0]["CP_PHONE"];
		$OP_EMAIL = $arr_op_cp[0]["EMAIL"];


		if($EMAIL_SUBJECT == "")
			$EMAIL_SUBJECT = $OP_CP_NM." 견적_".trim($CP_NM)."_".date("Ymd", strtotime("0 month"));

		
		if($EMAIL_BODY == "") { 

			$EMAIL_BODY = "안녕하세요. ".$OP_CP_NM." ".$s_adm_nm."입니다. \r\n\r\n".
							 
							 "견적서 확인 부탁드립니다  \r\n\r\n".

							 "(회신 메일주소 : gift@giftnet.co.kr) \r\n\r\n";
		} else {
			$EMAIL_BODY = str_replace("<br/>", "\r\n", $EMAIL_BODY);
		}

	} else {
		$GROUP_NO = cntMaxGroupNoEstimate($conn);

		if(sizeof($chk_no) > 0) { 

			$arr_rs_goods = listGoodsByGoodsNoArray($conn, $chk_no);

		}

		//토탈류 컬럼 추가
		$TOTAL_QTY					= 0;
		$TOTAL_SALE_PRICE			= 0;
		$TOTAL_DISCOUNT_PRICE		= 0;
		$TOTAL_SA_DELIVERY_PRICE	= 0;
		$GRAND_TOTAL_SALE_PRICE		= 0;
		//토탈류 컬럼 추가
		
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
	tr.row_sum {background-color:#DEDEDE;}
	tr.row_sum > td {border-bottom:2px solid #86a4b3;}
	.normal_table{
		width:100%;
	}
	.txt_tot_dc_price{
		width:100%;
	}
</style>
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
  <script type="text/javascript" >

	// 조회 버튼 클릭 시 
	function js_save() {
		//alert("신규");
		var frm = document.frm;
		
		frm.target = "";
		frm.method = "post";

		if(frm.cp_type.value == '' || frm.cp_type.value == '0' ) { 
			alert("견적업체를 입력해주십시오.");
			return;
		}

		frm.mode.value = "INSERT_GOODS_ESTIMATE";
		frm.TOTAL_QTY.value 				= parseInt(document.getElementsByName("tot_qty")[0].innerText.replaceall(",", ""));
		frm.TOTAL_SALE_PRICE.value 			= parseInt(document.getElementsByName("tot_supply_price")[0].innerText.replaceall(",", ""));
		frm.TOTAL_DISCOUNT_PRICE.value 		= parseInt(document.getElementsByName("tot_dc_price")[0].value.replaceall(",", ""));
		frm.GRAND_TOTAL_SALE_PRICE.value 	= parseInt(document.getElementsByName("grd_tot_sale_price")[0].innerText.replaceall(",", ""));

		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_update() {
		//alert("수정");
		var frm = document.frm;
		
		if(frm.cp_type.value == '' || frm.cp_type.value == '0' ) { 
			alert("견적업체를 입력해주십시오.");
			return;
		}

		frm.target = "";
		frm.method = "post";
		frm.mode.value = "UPDATE_GOODS_ESTIMATE";

		frm.TOTAL_QTY.value 				= parseInt(document.getElementsByName("tot_qty")[0].innerText.replaceall(",", ""));
		frm.TOTAL_SALE_PRICE.value 			= parseInt(document.getElementsByName("tot_supply_price")[0].innerText.replaceall(",", ""));
		frm.TOTAL_DISCOUNT_PRICE.value 		= parseInt(document.getElementsByName("tot_dc_price")[0].value.replaceall(",", ""));
		frm.GRAND_TOTAL_SALE_PRICE.value 	= parseInt(document.getElementsByName("grd_tot_sale_price")[0].innerText.replaceall(",", ""));
		
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

	function js_estimate_confirm() {
		var frm = document.frm;
		frm.target = "";
		frm.method = "post";
		frm.mode.value = "ESTIMATE_CONFIRM";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_update_dc_rate() {
		var frm = document.frm;
		frm.target = "";
		frm.method = "post";
		frm.mode.value = "UPDATE_DC_RATE";

		frm.TOTAL_QTY.value 				= parseInt(document.getElementsByName("tot_qty")[0].innerText.replaceall(",", ""));
		frm.TOTAL_SALE_PRICE.value 			= parseInt(document.getElementsByName("tot_supply_price")[0].innerText.replaceall(",", ""));
		frm.TOTAL_DISCOUNT_PRICE.value 		= parseInt(document.getElementsByName("tot_dc_price")[0].value.replaceall(",", ""));
		frm.GRAND_TOTAL_SALE_PRICE.value 	= parseInt(document.getElementsByName("grd_tot_sale_price")[0].innerText.replaceall(",", ""));

		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_insert_category() {
		var frm = document.frm;
		frm.target = "";
		frm.method = "post";

		if(frm.cp_type.value == '' || frm.cp_type.value == '0' ) { 
			alert("견적업체를 입력해주십시오.");
			return;
		}

		frm.mode.value = "INSERT_CATEGORY";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_excel() {

		var frm = document.frm;
		var url = "goods_estimate_excel.php?gp_no=<?=base64url_encode($gp_no)?>&print_type=" + frm.print_type.value;
		window.location.assign(url);

	}

	function js_pop_estimate_goods(gpg_no){
		var url = "/manager/estimate/pop_goods_estimate_goods.php?gpg_no=" + gpg_no;
		NewWindow(url,'pop_estimate_goods','1024','600','Yes');
	}
	
	function js_calculate_specifications(){
		//객체 가져오기
		sub_retail_price	= document.getElementsByName("sub_retail_price[]");
		sub_estimate_price	= document.getElementsByName("sub_estimate_price[]");
		sub_qty				= document.getElementsByName("sub_qty[]");
		sub_supply_price	= document.getElementsByName("sub_supply_price[]");
		tot_qty				= document.getElementsByName("tot_qty")[0];
		tot_supply_price	= document.getElementsByName("tot_supply_price")[0];
		tot_dc_price		= document.getElementsByName("tot_dc_price")[0];
		grd_tot_sale_price	= document.getElementsByName("grd_tot_sale_price")[0];

		//천단위 구분기호 제거 및 숫자로 변경
		temp_tot_qty			= parseInt(tot_qty.innerText.replaceall(",", ""));
		temp_tot_dc_price		= parseInt(tot_dc_price.value.replaceall(",", ""));
		temp_tot_supply_price	= parseInt(tot_supply_price.innerText.replaceall(",", ""));
		temp_grd_tot_sale_price	= parseInt(grd_tot_sale_price.innerText.replaceall(",", ""));

		var temp_tot_qty = 0;
		var temp_tot_supply_price = 0;

		for (i=0;i<sub_qty.length;i++) {
			//천단위 구분기호 제거 및 숫자로 변경
			temp_sub_retail_price 	= parseInt(sub_retail_price.item(i).value.replaceall(",", ""));
			temp_sub_estimate_price = parseInt(sub_estimate_price.item(i).value.replaceall(",", ""));
			temp_sub_qty 			= parseInt(sub_qty.item(i).value.replaceall(",", ""));

			//계산
			sub_supply_price.item(i).value = temp_sub_qty * temp_sub_estimate_price;
			temp_tot_qty += temp_sub_qty;
			temp_tot_supply_price += temp_sub_qty * temp_sub_estimate_price;
			
			//넘버포맷
			sub_supply_price.item(i).value 		= numberFormat(sub_supply_price.item(i).value);
			sub_qty.item(i).value 				= numberFormat(sub_qty.item(i).value);
			sub_retail_price.item(i).value		= numberFormat(sub_retail_price.item(i).value);
			sub_estimate_price.item(i).value 	= numberFormat(sub_estimate_price.item(i).value);
		}
		
		//계산
		tot_qty.innerText				= temp_tot_qty;
		tot_supply_price.innerText		= temp_tot_supply_price;
		grd_tot_sale_price.innerText	= temp_tot_supply_price - temp_tot_dc_price;

		//넘버포맷
		tot_qty.innerText				= numberFormat(tot_qty.innerText);
		tot_supply_price.innerText		= numberFormat(tot_supply_price.innerText);
		tot_dc_price.value				= numberFormat(tot_dc_price.value);
		grd_tot_sale_price.innerText	= numberFormat(grd_tot_sale_price.innerText);
	}

	function js_estimate_test(gp_no, op_cp_no, print_type, print_date){
		NewDownloadWindow("estimate_sheet_excel.php",{
			gp_no : Base64.encode(gp_no)
		   ,print_type :  Base64.encode(print_type)
		   ,print_date : Base64.encode(print_date)
		   ,op_cp_no : Base64.encode(op_cp_no)
		});
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
				html += "<td width='155px'>견적가 : "+arr_keywordList[3]+"</td>";
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
			$(".sub_goods_list").append("<tr height='35'><td>" + arr_keywordValues[0] + "["+ arr_keywordValues[4] + "]" + "<input type='hidden' name='sub_gpg_no[]' value=''><input type='hidden' name='sub_goods_name[]' value='" + arr_keywordValues[0] + "'><input type='hidden' name='sub_goods_id[]' value='" + arr_keywordValues[1] + "'></td><td ><input type='hidden' name='sub_delivery_cnt_in_box[]' value='"+arr_keywordValues[11]+"'/>"+arr_keywordValues[11]+"</td><td><input type='text' name='sub_retail_price[]' onchange='javascript:js_calculate_specifications();' class='txt' style='width:70%' value='"+arr_keywordValues[13]+"'/>원</td><td><input type='text' name='sub_estimate_price[]' class='txt' style='width:70%' value='"+arr_keywordValues[3]+"' onchange='javascript:js_calculate_specifications();'>원</td><td><input type='text' name='sub_qty[]' class='txt' style='width:70%' value='1' onChange='javascript:js_calculate_specifications();' onkeyup='return isNumber(this)'></td><td><input type='text' name='sub_supply_price[]' class='txt' style='width:70%' value='0' onchange='javascript:js_calculate_specifications();' onkeyup='return isNumber(this)'>원</td><td><span class='remove_sub' style='color:#478fb2; cursor:pointer; font-weight:bold; text-decoration:underline;'>삭제</span></td></tr>");
		else
			$(".sub_goods_list").append("<tr height='35'><td>" + arr_keywordValues[0] + " / <span style='color:red;'>" + arr_keywordValues[14] + "</span>["+ arr_keywordValues[4] + "]" + "<input type='hidden' name='sub_gpg_no[]' value=''><input type='hidden' name='sub_goods_name[]' value='" + arr_keywordValues[0] + "'><input type='hidden' name='sub_goods_id[]' value='" + arr_keywordValues[1] + "'></td><td ><input type='hidden' name='sub_delivery_cnt_in_box[]' value='"+arr_keywordValues[11]+"'/>"+arr_keywordValues[11]+"</td><td><input type='text' name='sub_retail_price[]' onchange='javascript:js_calculate_specifications();' class='txt' style='width:70%' value='"+arr_keywordValues[13]+"'/>원</td><td><input type='text' name='sub_estimate_price[]' class='txt' style='width:70%' value='"+arr_keywordValues[3]+"' onchange='javascript:js_calculate_specifications();'>원</td><td><input type='text' name='sub_qty[]' class='txt' style='width:70%' value='1' onChange='javascript:js_calculate_specifications();' onkeyup='return isNumber(this)'></td><td><input type='text' name='sub_supply_price[]' class='txt' style='width:70%' value='0' onchange='javascript:js_calculate_specifications();' onkeyup='return isNumber(this)'>원</td><td><span class='remove_sub' style='color:#478fb2; cursor:pointer; font-weight:bold; text-decoration:underline;'>삭제</span></td></tr>");

		loopSendKeyword = false;
		checkFirst = false;
		hide('suggest');
		js_calculate_specifications();
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
		js_calculate_specifications();
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
		<input type="hidden" name="TOTAL_QTY" value="" />
		<input type="hidden" name="TOTAL_SALE_PRICE" value="" />
		<input type="hidden" name="TOTAL_DISCOUNT_PRICE" value="" />
		<input type="hidden" name="TOTAL_SA_DELIVERY_PRICE" value="" />
		<input type="hidden" name="GRAND_TOTAL_SALE_PRICE" value="" />

			<!-- S: mwidthwrap -->
			<div id="mwidthwrap">

				<h2>견적 관리</h2>
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
							<input type="button" name="a0" value=" 견적 처리 완료 " class="btntxt" onclick="javascript:js_estimate_confirm();">
							<? } ?>
						</td>
						<td align="right">
							<select name="print_type">
								<option value="ESTIMATE_ONLY">견적서만</option>								
							</select>
							<a href="javascript:js_estimate_test('<?=$gp_no?>','1','3','',frm.cp_type.value);"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>
						</td>
					</tr>
					<tr class="display_none">
						<th>이메일</th>
						<td class="line">
							<input type="text" class="txt" name="sent_email" value="<?=$SENT_EMAIL?>" style="width: 80%;" placeholder="복수의 메일을 보내실 때에는 ',' 혹은 ';'를 빈칸없이 붙여서 입력해주세요."/> 
						</td>
						<th>발송여부</th>
						<td colspan="2"><?=$IS_SENT == "Y" ? "<font color='green'>".$SENT_DATE."</font>" : "<font color='red'>발송전</font>"?> &nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="b0" value=" 견적서 발송 " class="btntxt" onclick="javascript:js_send_email('<?=$IS_SENT?>');">
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
						<th>견적업체</th>
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
			<h3>견적상품 추가</h3>
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

							<table cellpadding="0" cellspacing="0" class="rowstable" style="margin-top:5px;">
							<colgroup>
								<col width="*%" />
								<col width="10%" />
								<col width="10%" />
								<col width="10%" />
								<col width="10%" />
								<col width="10%" />
								<col width="5%" />
							</colgroup>
							<thead>
								<tr>
									<th colspan="7" class="line">상품을 검색해서 선택하시면 아래에 자재가 추가됩니다</th>
								</tr>
								<tr>
									<th class="line">상품명</th>
									<th class="line">박스입수</th>
									<th class="line">기프트넷단가</th>
									<th class="line">단가</th>
									<th class="line">수량</th>
									<th class="line">합계</th>
									<th class="line">삭제</th>
								</tr>
							</thead>
							<tbody class="sub_goods_list">
							</tbody>
							</table>
							<table class="rowstable">
							<tbody class='total_group'></tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			<script>
			<?
			for($i = 0; $i < sizeof($arr_rs_goods); $i++) {
			?>

				$(".sub_goods_list").append(
					"<tr height='35'>"+
						"<td><?=$arr_rs_goods[$i]["GOODS_NAME"]?>[<?=$arr_rs_goods[$i]["GOODS_CODE"]?>]"+
						"<input type='hidden' name='sub_gpg_no[]' value='<?=$arr_rs_goods[$i]["GPG_NO"]?>'>"+
						"<input type='hidden' name='sub_goods_name[]' value='<?=$arr_rs_goods[$i]["GOODS_NAME"]?>'>"+
						"<input type='hidden' name='sub_goods_id[]' value='<?=$arr_rs_goods[$i]["GOODS_NO"]?>'></td>"+
						"<td><input type='hidden' name='sub_delivery_cnt_in_box[]' value='<?=$arr_rs_goods[$i]["DELIVERY_CNT_IN_BOX"]?>'><?=$arr_rs_goods[$i]["DELIVERY_CNT_IN_BOX"]?></td>"+
						"<td><input type='text' name='sub_retail_price[]' onChange='javascript:js_calculate_specifications();' class='txt' style='width:70%' value='<?=number_format($arr_rs_goods[$i]["RETAIL_PRICE"])?>'>원</td>"+
						"<td>"+
						"<input type='text' name='sub_estimate_price[]' onchange='javascript:js_calculate_specifications();' class='txt' style='width:70%' value='<?=number_format($arr_rs_goods[$i]["ESTIMATE_PRICE"])?>' onChange='javascript:js_calculate_specifications();'>원</td>"+
						"<td><input type='text' name='sub_qty[]' class='txt' style='width:70%' value='<?=$arr_rs_goods[$i]["QTY"]?>' onChange='javascript:js_calculate_specifications();' onkeyup='return isNumber(this)'></td>"+
						"<td><input type='text' name='sub_supply_price[]'  onchange='javascript:js_calculate_specifications();' class='txt' style='width:70%' value='<?=number_format($arr_rs_goods[$i]["SUPPLY_PRICE"])?>' onkeyup='return isNumber(this)'>원</td>"+
						"<td>" + 
						"<span class='remove_sub' style='color:#478fb2; cursor:pointer; font-weight:bold; text-decoration:underline;'>삭제</span>" +
						"</td>"+
					"</tr>"
				);
				
			<?
			}
				
			?>
				$(".total_group").append(
					"<tr height='35' class='row_sum'>"
						+"<td colspan ='7'>"
							+"<table class='normal_table'>"
								+"<tr>"
									+"<td class='normal_td'><b>주문합계 :</b></td>"
									+"<td><b>총 수량: </b></td><td class='normal_td' name='tot_qty'><?=number_format($TOTAL_QTY)?></td>"
									+"<td><b>총 판매가: </b></td><td class='normal_td' name='tot_supply_price'><?=number_format($TOTAL_SALE_PRICE)?></td>"
									+"<td><b>총 할인: </b></td><td class='normal_td'><input class='txt_tot_dc_price' name='tot_dc_price' type='text' value='<?=number_format($TOTAL_DISCOUNT_PRICE)?>' onChange='javascript:js_calculate_specifications();'></td>"
									+"<td><b>총 매출 합계: </b></td><td class='normal_td' name='grd_tot_sale_price'><?=number_format($GRAND_TOTAL_SALE_PRICE)?></td>"
								+"</tr>"
							+"</table>"
						+"</td>"
					+"</tr>"
				);
			<?
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
		<!--<iframe src="/manager/estimate/pop_goods_estimate.php?gp_no=<?=$gp_no?>" display ='none' frameborder="no" width="100%" height="800px" marginwidth="0" marginheight="0" border="1"></iframe>
		--><?
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