<?
ini_set('memory_limit',-1);
session_start();
#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "CF006"; // �޴����� ���� �� �־�� �մϴ�

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
	require "../../_classes/biz/confirm/confirm.php";
	require "../../_classes/biz/company/company.php";
	require "../../_classes/biz/syscode/syscode.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/email/email.php";


#====================================================================
# Request Parameter
#====================================================================

	// echo "IP : ".$_SERVER["HTTP_X_FORWARDED_FOR"]."<br>";

	$pw="";
	if($cp_type=="5732" || $cp_type=="9966" || $cp_type=="5733"){
		echo "<script type='text/javascript' src='../js/common.js?v=2'></script>";
		$query="SELECT SYS_VALUE 
				FROM TBL_SYSTEM_NUM_VALUE 
				WHERE SYS_KEY='SECURITY_PASSWORD_ENTER_COUNT' ;
				";
		$result=mysql_query($query, $conn);
		$rows=mysql_fetch_array($result);

		if($rows[0]<1){
			echo $rows[0]."<br>";
			echo "<script>var g_pw=prompt('Ư������ ��ȸ�� ���ؼ��� ��й�ȣ�� �ʿ��մϴ�.');</script>";
			?>
			<script>
				if(g_pw!="9620"){
					history.go(-1);
					NewWindow('pop_password_error.php', 'pop_password_error','500','500','YES');
					// $('input[name=cp_type]').val("");

				}
			</script>
			<?
		}
		else{
			echo"<script>alert('��й�ȣ�� �߸� �Է��ϼż� �����Ͻ� �� �����ϴ�. ��ڿ��� �����ϼ���!');</script>";
			// echo"<script>$('input[name=cp_type]').val('');</script>";
			echo"<script>history.back();</script>";
		}
		
		

		// echo "test<br>";
	}

	if($mode == "SEND_EMAIL") { 
	
		$cp_no = $cp_type;
		$file_title = "�ŷ�����";
		$reserve_no = "custom";
	
		$download_url = "http://".$_SERVER['HTTP_HOST']."/manager/confirm/company_ledger_excel_list.php?cp_no=".base64url_encode($cp_type)."&start_date=".base64url_encode($start_date)."&end_date=".base64url_encode($end_date);
		$path = $_SERVER["DOCUMENT_ROOT"]."/upload_data/temp_mail";
		$filename = "����Ʈ��_".$file_title."_".$reserve_no.".xls";
		$file = $path . "/" . $filename;
		
		downloadFile($download_url, $file);

		//���ü�� �ƴҰ�� �ش� ��ü�� ��������� �� - ������ ���ü�� ����Ѵٰ� ����
		$arr_op_cp = getOperatingCompany($conn, $s_adm_com_code);
		$OP_CP_NM = $arr_op_cp[0]["CP_NM"]." ".$arr_op_cp[0]["CP_NM2"];
		$OP_CEO_NM = $arr_op_cp[0]["CEO_NM"];
		$OP_CP_ADDR = $arr_op_cp[0]["CP_ADDR"];
		$OP_CP_PHONE = $arr_op_cp[0]["CP_PHONE"];
		$OP_EMAIL = $arr_op_cp[0]["EMAIL"];

		if($sent_email <> "") {
		
			//sendMail($OP_EMAIL, $OP_CP_NM, $email_subject, $email_body, $sent_email);
			//sendMail2($OP_EMAIL, $OP_CP_NM, $email_subject, $email_body, $sent_email, $path, $filename);

			include('../../_PHPMailer/class.phpmailer.php');

			$sent_email = str_replace(";", ",", $sent_email);

			mailer($OP_CP_NM, $OP_EMAIL, $sent_email, $sent_email, $email_subject, $email_body, $path, $filename);

			insertEmail($conn, $file_title, $cp_no, $OP_CP_NM, $OP_EMAIL, $sending_email, $sending_email, $email_subject, $email_body, $download_url, $s_adm_no, $option);

	?>
	<script language="javascript">
			alert('���� ó�� �Ǿ����ϴ�.');
			location.href =  "company_ledger_list.php?cp_type=<?=$cp_type?>&start_date=<?=$start_date?>&end_date=<?=$end_date?>";
	</script>
	<?
		} else {
	?>
	<script language="javascript">
			alert('�����Դϴ�. �̸��� �ּҰ� ��ȿ���� Ȯ�κ�Ź�帳�ϴ�.');
	</script>
	<?
		}

	}


	if ($mode == "D") {
		$row_cnt = count($chk_no);

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_cl_no = $chk_no[$k];

			$arr_cl = selectCompanyLedger($conn, $str_cl_no);

			if(sizeof($arr_cl) > 0) { 
				$rs_inout_type	= $arr_cl[0]["INOUT_TYPE"]; 

				// ����ȭ ���Ŀ� ����, ���� ���� �Ұ���� ����
				//if($rs_inout_type != "����" && $rs_inout_type != "����" && $rs_inout_type != "�뺯�ǻ�" && $rs_inout_type != "�����ǻ�")
				
				$result = deleteCompanyLedger($conn, $str_cl_no, $s_adm_no);
				
				//��꼭 �Ա� ���� ����
				if($result)
					deleteTaxInvoiceInCash($conn, $str_cl_no);
			}
		}

?>	
<script language="javascript">
		alert('ó�� �Ǿ����ϴ�.');
</script>
<?

	}

	if($mode == "TAX_INVOICE") { 

		$row_cnt = count($chk_no);

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_cl_no = $chk_no[$k];

			$result = updateTaxInvoiceTF($conn, $str_cl_no, $cf_type, $cf_code, $tax_confirm_tf, $s_adm_no);
			
			//echo $result." // ".$cf_code." // ".$tax_confirm_tf."<br/>";
			if($result && $cf_code <> '' && $tax_confirm_tf == "Y") { 

				updateTaxInvoceExtraInfo($conn, $str_cl_no, $cf_code);

			}
		}
		?>	
		<script language="javascript">
			alert('ó�� �Ǿ����ϴ�.');
			location.href="<?=$_SERVER[PHP_SELF]?>?cp_type=<?=$cp_type?>&view_daily=<?=$view_daily?>&view_tax=<?=$view_tax?>&start_date=<?=$start_date?>&end_date=<?=$end_date?>&show_type=<?=$show_type?>&cf_type=<?=$cf_type?>";
		</script>
<?
		$cf_code = "";
		exit;
	}

	if($mode == "CANCEL_TAX_INVOICE") { 

		$row_cnt = count($chk_no);

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_cl_no = $chk_no[$k];

			$result = cancelTaxInvoiceCode($conn, $str_cl_no, $cf_code);
		}

?>	
<script language="javascript">
		alert('ó�� �Ǿ����ϴ�.');
</script>
<?
		$cf_code = "";
	}

	

	if($mode == "UPDATE_INOUT_DATE") { 
		
		$row_cnt = count($chk_no);

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_cl_no = $chk_no[$k];

			$result = updateCompanyLedgerInoutDate($conn, $str_cl_no, $tax_confirm_date);
		}

?>	
<script language="javascript">
		alert('ó�� �Ǿ����ϴ�.');
</script>
<?

	}

	$day_0 = date("Y-m-d",strtotime("0 month"));
	$day_1 = date("Y-m-d",strtotime("-1 day"));
	$day_7 = date("Y-m-d",strtotime("-7 day"));
	$day_31 = date("Y-m-d",strtotime("-1 month"));

	if ($start_date == "") {
		$d = new DateTime('first day of this month');
		$start_date = $d->format("Y-m-d");
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));;
	} else {
		$end_date = trim($end_date);
	}

#===============================================================
# Get Search list count
#===============================================================

	// 2017-08-25
	// �Ϻ� �ŷ�ó�� ���� ������ ���� ���� �ο�, �ý��� �ڵ忡�� ����
	// �ý��۰����� ���� ��ü ���� ��������
	$arr_chk = listDcode($conn, 'LIMIT_COMPANY_LEDGER', 'Y', 'N', 'DCODE', $cp_type, 1, 1000);

	if(sizeof($arr_chk) <= 0 || (sizeof($arr_chk) > 0 && $s_adm_group_no == 1)) { 
		
		//echo $start_date."<br/>";
		$arr_rs_prev = getCompanyLedgerPreviousMonth($conn, $start_date, $cp_type);

		if($cp_type <> "")
			$arr_rs = listCompanyLedger($conn, $start_date, $end_date, $cp_type);
	}
	
	$arr_rs_company = selectCompany($conn, $cp_type);
		
	if(sizeof($arr_rs_company) > 0) { 

		$rs_cp_type							= SetStringFromDB($arr_rs_company[0]["CP_TYPE"]); 
		$rs_cp_zip							= SetStringFromDB($arr_rs_company[0]["CP_ZIP"]); 
		$rs_cp_addr							= SetStringFromDB($arr_rs_company[0]["CP_ADDR"]); 
		$rs_biz_no							= SetStringFromDB($arr_rs_company[0]["BIZ_NO"]); 
		$rs_ceo_nm							= SetStringFromDB($arr_rs_company[0]["CEO_NM"]); 
		$rs_cp_phone						= SetStringFromDB($arr_rs_company[0]["CP_PHONE"]); 
		$rs_ad_type							= SetStringFromDB($arr_rs_company[0]["AD_TYPE"]); 
		$rs_email							= SetStringFromDB($arr_rs_company[0]["EMAIL"]);
		$rs_email_tf						= SetStringFromDB($arr_rs_company[0]["EMAIL_TF"]);

		if($rs_email_tf=="N") $rs_email="";

		echo"<script>console.log('".$rs_email."');</script>";

		if($rs_ad_type == "")
			$rs_ad_type = "����";
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js?v=2"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<script type="text/javascript" src="../jquery/jquery.floatThead.min.js"></script>
<script type="text/javascript" src="../jquery/jquery.cookie.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script>
	$(document).ready(function(){
		pickOption();

		//���������� ���� ǥ�õǴ� ��� ���� �Լ�
		function pickOption(){
			if($("select[name='cf_type']").val()=="CF006"){
				//���� ��� �� ����
					$("#confirm_group").hide();				
				
				//���� ��� ǥ��
					$("#confirm_group2").show();				
				//alert("1");
			} else {
				//������ �� ����
				$("#confirm_group2").hide();				
				
				//���� ��� ǥ��
					$("#confirm_group").show();
			}
		}

		//����Ʈ��Ŀ
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

		//�ϰ� Ŭ���� ���� �Ϸ� ����� �̿� ���� ����
		$(document).on("click",".row_daily",function(){
			var billIssueWithSameDate = $(".row_tax_confirm").find("[data-value ='"+$(this).data('value')+"']");

			var cnt = billIssueWithSameDate.length;

			for(var i=0;i<cnt;i++){
				billIssueWithSameDate.eq(i).parent().remove();
			}

			for(var i=0;i<cnt;i++){
				$(this).before(billIssueWithSameDate.eq(i).parent());
			}
		});

		//���������� ���� ǥ�õǴ� ��� ���� �̺�Ʈ ���ε�
		$(document).on("click","select[name='cf_type']",function(){
			pickOption();
		});

		//�������� �ʰ� �ʱ�ȭ
		$(document).on("click", "#reset", function(){
			location.reload();
		});

		//MRO ���� ����
		$(document).on("click", "#apply", function(){
			var form_data = new FormData();
			var mro_order_excel_file = $("#mro_file_nm").prop('files')[0];
			form_data.append('mode', "APPLY_MRO_CONFIRM");
			form_data.append('s_adm_no', "<?=$s_adm_no?>");
			form_data.append('file', mro_order_excel_file);
			$.ajax({
				url: '/manager/ajax_file_read.php',
				dataType: 'json',
				type: 'post',
				processData: false,
				contentType:false,
				data: form_data,
				success: function(response) {
					alert(response + "���� ������ �Ϸ��Ͽ����ϴ�.");
					location.reload();
				}, error: function(jqXHR, textStatus, errorThrown) {
					console.log(jqXHR.responseText); 
				}
			});//ajax
		});
		
		//MRO ���� �̸�����
		$(document).on("click", "#preview", function(){
			var mro_order_excel_file = $("#mro_file_nm").prop('files')[0];
			var form_data = new FormData();
			form_data.append('file', mro_order_excel_file);
			form_data.append('mode', "PREVIEW_MRO_CONFIRM");
			form_data.append('s_adm_no', "<?=$s_adm_no?>");
			$.ajax({
				url: '/manager/ajax_file_read.php',
				dataType: 'json',
				type: 'post',
				processData: false,
				contentType:false,
				data: form_data,
				success: function(response) {
					if(response != false){
						$.each(response,function(i,value){
							$("input[value='"+response[i]["cl_no"]+"']").parent().parent().addClass("preview_confirm");
							$("input[value='"+response[i]["cl_no"]+"']").parent().parent().find("td:last").html("<div class='btn_cf_code' data-cf_code='MRO ���� <?=$day_0?>' data-total_price=''>MRO ���� <?=$day_0?></div>");
						});
					} else{
						alert("�����Ͽ����ϴ�.");
					}
				}, error: function(jqXHR, textStatus, errorThrown) {
					console.log(jqXHR.responseText); 
				}
			});//ajax
		});

		//�ڵ��ϼ�
		$('.autocomplete_off').attr('autocomplete', 'off');
	});
</script>
<script>
	$(function(){
		$('table.fixed_header_table').floatThead({
			position: 'fixed'
		});
	});
</script>
<script language="javascript">


	// ��ȸ ��ư Ŭ�� �� 
	function js_search() {
		var frm = document.frm;
		
		if(frm.cp_type.value == "") { 
			var e = jQuery.Event( 'keydown', { keyCode: $.ui.keyCode.ENTER } );
			$("input[name=txt_cp_type]").trigger(e);
		}
		
		//frm.nPage.value = "1";
		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_search_date_by_code(code) {

		var frm = document.frm;

		if (code == "prev_month") {
			SetPrevMonthDays("start_date", "end_date");
		}

		if (code == "prev_week") {
			SetPrevWeek("start_date", "end_date");
		}

		if (code == "prev_day") {
			SetYesterday("start_date", "end_date");
		}

		if (code == "today") {
			SetToday("start_date", "end_date");
		}

		if (code == "this_week") {
			SetWeek("start_date", "end_date");
		}

		if (code == "this_month") {
			SetCurrentMonthDays("start_date", "end_date");
		}

		
		if(frm.cp_type.value == "") { 
			var e = jQuery.Event( 'keydown', { keyCode: $.ui.keyCode.ENTER } );
			$("input[name=txt_cp_type]").trigger(e);
		}

		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_delete() {
		var frm = document.frm;

		bDelOK = confirm('������ �����Ͻðڽ��ϱ�?');
		
		if (bDelOK==true) {
			
			frm.mode.value = "D";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_confirm_tax_invoice(tax_confirm_tf) { 

		var frm = document.frm;

		/*
		//��꼭 ���� ī�� ������ ��쵵 �����Ƿ� �ϴ� �н�
		if(tax_confirm_tf == 'Y' && frm.cf_code.value == '') { 
			alert('���� ��꼭 ���ι�ȣ�� �Է����ּ���.');
			frm.cf_code.focus();
			return;
		}
		*/

		var expire = new Date('2999-12-31T23:59:59Z');
		$.cookie('cookie_cf_type', frm.cf_type.value, {expires: expire}); 



		if(frm.cf_type.value == '') { 
			alert('���� ������ �������ּ���.');
			frm.cf_type.focus();
			return;
		}

		bDelOK = confirm((tax_confirm_tf == 'Y' ? '���� ó���Ͻðڽ��ϱ�?' : '���� ����Ͻðڽ��ϱ�?'));
		
		if (bDelOK==true) {
			
			frm.mode.value = "TAX_INVOICE";
			frm.tax_confirm_tf.value = tax_confirm_tf;
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_minus_tax_invoice() { 
		var frm = document.frm;

		if(frm.cf_code.value == '') { 
			alert('������ ���ι�ȣ�� �Է����ּ���.');
			frm.cf_code.focus();
			return;
		}

		bDelOK = confirm('�߸� �Էµ� ���ι�ȣ�� ����Ҷ� ����մϴ�.');
		
		if (bDelOK==true) {
			
			frm.mode.value = "CANCEL_TAX_INVOICE";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}

	}

	function js_update_inout_date() { 

		var frm = document.frm;

		bOK = confirm("���õ� ������ " + frm.tax_confirm_date.value + " �� �����Ͻðڽ��ϱ�?");
		
		if (bOK) {
			
			frm.mode.value = "UPDATE_INOUT_DATE";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}

	}


	/*
	function js_append(cl_no) {

		var url = "/manager/confirm/pop_company_ledger_append.php";
		var frm = document.frm;

		frm.cl_no.value = cl_no;
		frm.mode.value = "APPEND";

		NewWindow('about:blank', 'company_ledger_write', '860', '513', 'YES');
		frm.target = "company_ledger_write";
		frm.action = url;
		frm.submit();

	}
	*/

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

	function js_view_order(rn, reserve_no) {

		var frm = document.frm;
		
		var url = "/manager/order/order_read.php?reserve_no="+reserve_no;

		NewWindow(url, 'order_detail','860','600','YES');
		
	}

	function js_write() { 
	
		var frm = document.frm;
		
		var url = "/manager/confirm/pop_company_ledger_write.php?cp_type=" + frm.cp_type.value + "&start_date=" + frm.start_date.value + "&end_date=" + frm.end_date.value;

		NewWindow(url, 'pop_company_ledger_write','860','600','YES');

	}

	function js_recalc() { 
		var frm = document.frm;

		var url = "/manager/confirm/pop_company_ledger_surtax_fix.php";
		var frm = document.frm;
		NewWindow('about:blank', 'pop_company_ledger_surtax_fix', '860', '513', 'YES');
		frm.mode.value = "";
		frm.target = "pop_company_ledger_surtax_fix";
		frm.action = url;
		frm.submit();

	}

	function js_view_goods_request(req_no) {

		var url = "../stock/pop_goods_request.php?req_no=" + req_no;

		NewWindow(url, 'pop_goods_request','1024','600','YES');
		
	}

	function js_excel() {

		var frm = document.frm;
		
		frm.target = "";
		frm.action = "<?=str_replace("list","excel_list",$_SERVER[PHP_SELF])?>";
		frm.submit();

	}

	function js_memo_view(cl_no) {

		var url = "popup_company_ledger_memo.php?cl_no=" + cl_no;
		NewWindow(url,'popup_memo','820','700','YES');

	}

	function js_print_view() {
		
		var frm = document.frm;
		
		var url = "pdf_maker.php?cp_type=" + frm.cp_type.value + "&start_date=" + frm.start_date.value + "&end_date=" + frm.end_date.value;
		NewWindow(url,'pdf_maker','820','700','YES');

	}

	function js_print_view2() {
		
		var url = "/manager/confirm/popup_company_ledger_for_print.php?cp_type=" + frm.cp_type.value + "&start_date=" + frm.start_date.value + "&end_date=" + frm.end_date.value;

		NewWindow(url, 'popup_company_ledger_for_print','980','600','YES');
	}

	function js_send_email() {
		var frm = document.frm;

		if(frm.sent_email.value != "") {

			var res = frm.sent_email.value.match(/[^0-9a-zA-Z-_.@,;]/gi);
			if (res != null) {
				alert('�̸��Ͽ��� ������ �ʴ� ��ȣ�� �ֽ��ϴ�. \n���� �̸����� �ƴҰ�� ���� ���� �߼��� �ȵǸ� �������� �����Ƕ��� �ּһ��̿� , �Ǵ� ; ��ȣ�� ����ϼ���.');
				return;
			}

		} else { 
			alert('�߼��� ��� �̸����� �Է����ּ���.');
			return;
		}
		
		frm.target = "";
		frm.method = "post";
		frm.mode.value = "SEND_EMAIL";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	/*
	function js_change_cf_code(prev_cf_code) {
		
		var url = "pop_change_tax_invoice.php?prev_cf_code=" + prev_cf_code;
		NewWindow(url, '���ι�ȣ�ϰ�����', '850', '213', 'YES');
		
	}
	

	function js_select_cf_code(selected_cf_code) {
		
		var frm = document.frm;
		frm.cf_code.value = selected_cf_code;
		frm.cf_code.focus();
		
	}
	*/
</script>
<style>
	#confirm_group, #confirm_group2{
		display:inline;
	}

	#apply, #preview, #reset{
		width:80px;
	}
	
	.preview_confirm > td, .preview_confirm > td > a{
		color:red !important;
	}

	.row_period {background-color:#CCCCCC; font-weight:bold;}
	.row_monthly {background-color:#DFDFDF; font-weight:bold;}
	.row_daily {background-color:#EFEFEF; font-weight:bold;}
	tr.row_tax_confirm > td, tr.row_tax_confirm > td > a {/*background-color:#99c1ef;*/ color:blue;} 
	tr.row_tax_confirm_safe > td, tr.row_tax_confirm_safe > td > a {/*background-color:#99c1ef;*/ color:green;} 
	tr.closed > td {background-color:#fff; color: #A2A2A2;} 

	.row_tax { background-color:#EEEEEEE; font-weight:bold;}
	.row_invoiced { background-color:#DFDFFE; font-weight:bold;}
</style> 
</head>

<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="cl_no" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="tax_confirm_tf" value="">
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

				<h2>�ŷ� ����</h2>
				
				<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">
					<div style="float:left;">
						<b>���� :</b> 
						<label><input type="checkbox" name="view_daily" value="Y"/>�ϰ�</label>
						<label><input type="checkbox" name="view_tax" value="Y"/>����/�����</label>
						<label><input type="checkbox" name="chkInput" value="Y"/>�Ա�</label>
						<label><input type="checkbox" name="chkOutput" value="Y"/>���</label>
					</div>
						
						<script>
							var expire = new Date('2999-12-31T23:59:59Z');

							if($.cookie('view_daily') == undefined) { 
								$.cookie('view_daily', "Y", {expires: expire}); 
								$("input[name=view_daily]").prop("checked", true);
							} else if($.cookie('view_daily') == "Y") { 
								$("input[name=view_daily]").prop("checked", true);
							} else
								$("input[name=view_daily]").prop("checked", false);
									
							$("input[name=view_daily]").click(function(){
								
								if($("input[name=view_daily]").is(":checked"))
									$.cookie('view_daily', "Y", {expires: expire}); 
								else
									$.cookie('view_daily', "", {expires: expire}); 

								js_search();
							});

							if($.cookie('view_tax') == undefined) { 
								$.cookie('view_tax', "Y", {expires: expire}); 
								$("input[name=view_tax]").prop("checked", true);
							} else if($.cookie('view_tax') == "Y") { 
								$("input[name=view_tax]").prop("checked", true);
							} else
								$("input[name=view_tax]").prop("checked", false);
									
							$("input[name=view_tax]").click(function(){
								
								if($("input[name=view_tax]").is(":checked"))
									$.cookie('view_tax', "Y", {expires: expire}); 
								else
									$.cookie('view_tax', "", {expires: expire}); 

								js_search();
							});

								
						</script>
						<div style="float:right; margin-right:10px;">
						 <a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="���� ����Ʈ" /></a>
						</div>
				</div>
				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="120" />
					<col width="250" />
					<col width="120" />
					<col width="120" />
					<col width="*" />
				</colgroup>
				<tr>
					<th>
						�Ⱓ
					</th>
					<td colspan="3">
						<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" id="start_date" name="start_date" value="<?=$start_date?>" maxlength="10"/>
						 ~ 
						<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" id="end_date" name="end_date" value="<?=$end_date?>" maxlength="10"/>
						&nbsp;
						<input type="button" value="����" onclick="javascript:js_search_date_by_code('prev_month');"/>
						<input type="button" value="����" onclick="javascript:js_search_date_by_code('prev_week');"/>
						<input type="button" value="����" onclick="javascript:js_search_date_by_code('prev_day');"/>
						<input type="button" value="����" onclick="javascript:js_search_date_by_code('today');"/>
						<input type="button" value="����" onclick="javascript:js_search_date_by_code('this_week');"/>
						<input type="button" value="�ݿ�" onclick="javascript:js_search_date_by_code('this_month');"/>
						
					</td>
					<td align="right">
						<b>���� ����</b> : <span style="color:red; font-weight:bold;"><?=$rs_ad_type?></span>, <b>����� ��ȣ</b> : <?=$rs_biz_no?><br/><br/> <b>��ǥ�� ��</b> : <?=$rs_ceo_nm?>, <b>��ǥ��ȭ</b> : <?=$rs_cp_phone?>
						<br/><br/><b>��ǥ �̸��� : </b><?=$rs_email?>
					</td>
				</tr>
				<tr>
					<th>��ü��</th>
					<td>
						<input type="text" class="autocomplete_off" style="width:90%" placeholder="��ü(��/�ڵ�) �Է��� ���͸� �����ּ���" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'',$cp_type)?>" />
						<input type="hidden" name="cp_type" value="<?=$cp_type?>">

						<script>
							$(function(){

								$("input[name=txt_cp_type]").keydown(function(e){

									if(e.keyCode==13) { 

										var keyword = $(this).val();
										if(keyword == "") { 
											// alert("null");
											$("input[name=cp_type]").val('');
											js_search();
										} else { 
										
											$.getJSON( "../company/json_company_list.php?term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE,CEO_NM", function(data) {
												if(data.length == 1) { 
													
													js_selecting_company("txt_cp_type", data[0].label, "cp_type", data[0].id);

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

							function js_selecting_company(target_name, cp_nm, target_value, cp_no) {
								
								$(function(){

									$("input[name="+target_name+"]").val(cp_nm);
									$("input[name="+target_value+"]").val(cp_no);
									js_search();

								});
							}
						</script>
					</td>
					<td colspan="2">

						<b>�ܾ� : </b><span class="get_balance" data-cp_no="<?=$cp_type?>">...</span>
						<script>
							$(function(){
								$(".get_balance").click(function(){
									var cp_no = $(this).data("cp_no");
									var clicked_obj = $(this);

									$.getJSON( "../confirm/json_company_ledger.php?cp_no=" + encodeURIComponent(cp_no), function(data) {
										if(data != undefined) { 
											if(data.length == 1) 
												clicked_obj.html(numberFormat(data[0].SUM_BALANCE) + " ��");
											else {
												alert(cp_no);
												clicked_obj.html("�˻������ �����ϴ�.");
											}
										}
										else
											alert(cp_no);
									});
								});

								$(".get_balance").click();

							});
						</script>

						<a href="javascript:js_search();" class="btn_search"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
					</td>
					<td align="right">
						<b>��ǥ �ּ�</b> : <?=$rs_cp_zip?> <?=$rs_cp_addr?>
					</td>
				</tr>
			</table>
			<div class="sp20"></div>

			<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table" border="0">

				<colgroup>
					<col width="3%" />
					<col width="8%" />
					<col width="3%" />
					<col width="*"/>
					<col width="3%" />
					<col width="8%" />
					<col width="8%" />
					<col width="8%" />
					<col width="8%" />
					<col width="10%" />
					<col width="8%" />
					<col width="7%" />
					<col width="5%" />
				</colgroup>
				<thead>
				<tr>
					<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
					<th>��¥</th>
					<th>����</th>
					<th>��ǰ��</th>
					<th>����</th>
					<th>�ܰ�</th>
					<th>����/���޾�</th>
					<th>����/�Աݾ�</th>
					<th>�ΰ���</th>
					<th>�ܾ�</th>
					<th>���</th>
					<th class="end" colspan="2">
						<select name="show_type" onchange="js_search();">
							<option value="1" <?if($show_type == "1" || $show_type == "") echo "selected";?>>�ֹ�/����</option>
							<option value="2" <?if($show_type == "2") echo "selected";?>>���ݰ�꼭</option>
						</select>
					</th>
				</tr>
				</thead>

				
				<?
					if (sizeof($arr_rs_prev) > 0) {
						for ($k = 0 ; $k < sizeof($arr_rs_prev); $k++) {

							$BALANCE					= trim($arr_rs_prev[$k]["BALANCE"]);
				?>
				<tr height="30">
					<td></td>
					<td><?=date("Y-m-d",strtotime("-1 day", strtotime($start_date)))?></td>
					<td></td>
					<td class="modeual_nm"><�����̿�></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="price"><?=number_format($BALANCE, 0)?></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>

				<? }  }  ?>

				<?
					//�Ⱓ��
					$period_qty_total = "";
					$period_withdraw_total = ""; 
					$period_deposit_total = "";
					$period_surtax_total = "";
					$period_balance_total = "";
					
					//����
					$month_group = "";
					$month_qty_total = "";
					$month_withdraw_total = ""; 
					$month_deposit_total = "";
					$month_surtax_total = "";
					$month_balance_total = "";
					
					//�ϰ�
					$day_group = "";
					$day_qty_total = "";
					$day_withdraw_total = ""; 
					$day_deposit_total = "";
					$day_surtax_total = "";
					$day_balance_total = "";

					//���ݰ�(����/�����)
					$tax_Y_withdraw_total = 0;
					$tax_Y_deposit_total = 0;
					$tax_N_withdraw_total = 0;
					$tax_N_deposit_total = 0;

					//���ݰ�꼭 ���࿩�� ��
					$invoiced_tax_Y_withdraw_total = 0;
					$invoiced_tax_Y_deposit_total = 0;
					$invoiced_tax_N_withdraw_total = 0;
					$invoiced_tax_N_deposit_total = 0;

					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							//CL_NO, CP_NO, INOUT_DATE, INOUT_TYPE, NAME, QTY, UNIT_PRICE, WITHDRAW, DEPOSIT, RESERVE_NO

							$CL_NO						= trim($arr_rs[$j]["CL_NO"]);
							$INOUT_DATE					= trim($arr_rs[$j]["INOUT_DATE"]);
							$INOUT_TYPE					= trim($arr_rs[$j]["INOUT_TYPE"]);
							$GOODS_NO					= trim($arr_rs[$j]["GOODS_NO"]);
							$NAME						= trim($arr_rs[$j]["NAME"]);
							$QTY						= trim($arr_rs[$j]["QTY"]);
							$UNIT_PRICE					= trim($arr_rs[$j]["UNIT_PRICE"]);
							$WITHDRAW					= trim($arr_rs[$j]["WITHDRAW"]);
							$DEPOSIT					= trim($arr_rs[$j]["DEPOSIT"]);
							$SURTAX						= trim($arr_rs[$j]["SURTAX"]);
							$CATE_01					= trim($arr_rs[$j]["CATE_01"]);
							$TAX_TF						= trim($arr_rs[$j]["TAX_TF"]);
							$MEMO						= trim($arr_rs[$j]["MEMO"]);
							$RESERVE_NO					= trim($arr_rs[$j]["RESERVE_NO"]);
							$ORDER_GOODS_NO				= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
							$RGN_NO						= trim($arr_rs[$j]["RGN_NO"]);
							$TO_CP_NO					= trim($arr_rs[$j]["TO_CP_NO"]);
							$INPUT_TYPE					= trim($arr_rs[$j]["INPUT_TYPE"]);

							$USE_TF						= trim($arr_rs[$j]["USE_TF"]);

							$TAX_CONFIRM_TF				= trim($arr_rs[$j]["TAX_CONFIRM_TF"]);
							$TAX_CONFIRM_DATE			= trim($arr_rs[$j]["TAX_CONFIRM_DATE"]);

							//$CF_CODE					= trim($arr_rs[$j]["CF_CODE"]);

							$WITHDRAW = floor($WITHDRAW);
							$DEPOSIT = floor($DEPOSIT);


							$INOUT_DATE = date("Y-m-d",strtotime($INOUT_DATE));

							if($USE_TF == "Y")
								$BALANCE = $BALANCE + $DEPOSIT - $WITHDRAW;
							else { 
								$QTY = 0;
								$WITHDRAW = 0;
								$DEPOSIT = 0;
								$SURTAX = 0;
							}

							if($INPUT_TYPE == "��������")
								$QTY = 0;


							if($TAX_CONFIRM_TF == "Y") {

								$arr_cf_code = listTaxInvoiceConfirmCode($conn, $CL_NO);
								
								//for($p = 0; $p < sizeof($arr_cf_code); $p ++) { 
								//	if(chkCashStatementByCFCode($conn, $CF_CODE) <= 0)
										$str_tax_class = "row_tax_confirm";
								//	else
								//		$str_tax_class = "row_tax_confirm_safe";
								//}
							} else { 
								$arr_cf_code = null;
								$str_tax_class = "";
							}

							$period_qty_total += $QTY;
							$period_withdraw_total += $WITHDRAW;
							$period_deposit_total += $DEPOSIT;
							$period_surtax_total += $SURTAX;
							$period_balance_total = $BALANCE;

							if($INOUT_TYPE == "����") { 
								//2017-07-19 ���� ����� ���� ��ü�� �Է����� �ֹ����� �������� ����
								//$TAX_TF = getOrderGoodsTaxTF($conn, $ORDER_GOODS_NO);
								$TAX_TF =  getGoodsTaxTF($conn, $GOODS_NO);
							} 

							if ($TAX_TF == "�����") {
								$STR_TAX_TF = "<font color='orange'>(�����)</font>";
							} else if ($TAX_TF == "����") {
								$STR_TAX_TF = "<font color='navy'>(����)</font>";
							} else
								$STR_TAX_TF = "";



							
							if($INOUT_TYPE == "����" || $INOUT_TYPE == "����") {
								if ($TAX_TF == "�����") {
									$tax_N_withdraw_total += $WITHDRAW;
									$tax_N_deposit_total += $DEPOSIT;

								
									if($TAX_CONFIRM_TF == "Y") { 
										$invoiced_tax_N_withdraw_total += $WITHDRAW;
										$invoiced_tax_N_deposit_total += $DEPOSIT;
									}

								} else { 
									$tax_Y_withdraw_total += $WITHDRAW;
									$tax_Y_deposit_total += $DEPOSIT;

									if($TAX_CONFIRM_TF == "Y") { 
										$invoiced_tax_Y_withdraw_total += $WITHDRAW;
										$invoiced_tax_Y_deposit_total += $DEPOSIT;
									}

								}
							}

							//���� ����� ������ �ϰ� ǥ��
							if($day_group != date("Y-m-d", strtotime($INOUT_DATE)) && $day_group != "" ) { 

									if($view_daily == "Y") {
				?>
				<tr height="30" class="row_daily" data-value="<?=$day_group?>">
					<td></td>
					<td colspan="2">�ϰ� :<?=$day_group?></td>
					<td class="modeual_nm"></td>
					<td class="price"><?=number_format($day_qty_total)?></td>
					<td></td>
					<td class="price"><?=number_format($day_deposit_total)?></td>
					<td class="price"><?=number_format($day_withdraw_total)?></td>
					<td class="price"><?=number_format($day_surtax_total)?></td>
					<td class="price"><?=number_format($day_balance_total, 0)?></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>

				<? 
									}

								$day_group = date("Y-m-d",strtotime($INOUT_DATE));
								$day_qty_total = $QTY;
								$day_withdraw_total = $WITHDRAW;
								$day_deposit_total = $DEPOSIT;
								$day_surtax_total = $SURTAX;
								$day_balance_total = $BALANCE;
							} else { 
								
								$day_qty_total += $QTY;
								$day_withdraw_total += $WITHDRAW;
								$day_deposit_total += $DEPOSIT;
								$day_surtax_total += $SURTAX;
								$day_balance_total = $BALANCE;
							}




							//���� ����� ������ ���� ǥ��
							if($month_group != date("Y-m", strtotime($INOUT_DATE)) && $month_group != "" ) { 
				?>
				<tr height="30" class="row_monthly">
					<td></td>
					<td colspan="2">���� :<?=$month_group?></td>
					<td class="modeual_nm"></td>
					<td class="price"><?=number_format($month_qty_total)?></td>
					<td></td>
					<td class="price"><?=number_format($month_deposit_total)?></td>
					<td class="price"><?=number_format($month_withdraw_total)?></td>
					<td class="price"><?=number_format($month_surtax_total)?></td>
					<td class="price"><?=number_format($month_balance_total, 0)?></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>

				<? 

								$month_group = date("Y-m",strtotime($INOUT_DATE));
								$month_qty_total = $QTY;
								$month_withdraw_total = $WITHDRAW;
								$month_deposit_total = $DEPOSIT;
								$month_surtax_total = $SURTAX;
								$month_balance_total = $BALANCE;
							} else { 
								
								$month_qty_total += $QTY;
								$month_withdraw_total += $WITHDRAW;
								$month_deposit_total += $DEPOSIT;
								$month_surtax_total += $SURTAX;
								$month_balance_total = $BALANCE;
							}

							
				?>
				<tr height="30" class="<?=$str_tax_class?> <?if($USE_TF != "Y") echo "closed";?>">
					<td><input type="checkbox" name="chk_no[]" value="<?=$CL_NO?>"/></td>
					<td data-value="<?=$INOUT_DATE?>"><?=$INOUT_DATE?></td><!--��¥-->
					<td><?=$INOUT_TYPE?></td><!--����-->
					<td class="modeual_nm">
						<?=$STR_TAX_TF?>
						<?=$NAME?>
						<!--
						<? if($INOUT_TYPE == "����" || $INOUT_TYPE == "����") { ?>
							<a href="javascript:js_view('<?=$CL_NO?>');"><?=$NAME?></a>
						<? } else { ?>
							<?=$NAME?>
						<? } ?>
						-->
					</td><!--��ǰ��-->
					<td class="price">
						<? if($QTY != 0) {?>
						<?=number_format($QTY)?>
						<? } ?>
					</td><!--����-->
					<td class="price"><?=number_format($UNIT_PRICE)?></td><!--�ܰ�-->
					<td class="price row_deposit" data-value="<?=$DEPOSIT?>"><?=number_format($DEPOSIT)?></td><!--����/���޾�-->
					<td class="price row_withdraw" data-value="<?=$WITHDRAW?>"><?=number_format($WITHDRAW)?></td><!--����/�Աݾ�-->
					<td class="price row_surtax" data-value="<?=$SURTAX?>"><?=number_format($SURTAX)?></td><!--�ΰ���-->
					<td class="price"><?=number_format($BALANCE, 0)?></td><!--�ܾ�-->
					<td class="memo_trigger" data-cl_no="<?=$CL_NO?>">
						<?if($CATE_01 <> "") echo "[".$CATE_01."] "?>
						<?=$MEMO?>
					</td><!--���-->
					<td colspan="2">

						<? if($show_type == "1" || $show_type == "") { ?>
							<? if($INOUT_TYPE == "����" || $INOUT_TYPE == "����") { ?>
								<?=getLinkScriptForOrderView($conn, $RESERVE_NO, $ORDER_GOODS_NO, $RGN_NO)?>
							<? } ?>
						<? } ?>
						<? if($show_type == "2") { ?>

							<? 
								
								for($p = 0; $p < sizeof($arr_cf_code); $p ++) { 

									$t_cf_type = $arr_cf_code[$p]["CF_TYPE"];
									$t_cf_code = $arr_cf_code[$p]["CF_CODE"];
									?>
										<div class="btn_cf_code" data-cf_code="<?=$t_cf_code?>" data-total_price="" ><?=$t_cf_type?> <?=$t_cf_code?></div>
									<?
								}
							
							?>
							
						<? } ?>

						<?
							$rs_biz_no	= "";
							if($rs_cp_type == "����") { 
								if($TO_CP_NO  > 0) { 
									$arr_rs_company = selectCompany($conn, $TO_CP_NO);
							
									if(sizeof($arr_rs_company)) { 
										$rs_biz_no	= SetStringFromDB($arr_rs_company[0]["BIZ_NO"]); 
									}
								}
							}
						?>
						<?=$rs_biz_no?>
					</td>
				</tr>

				<? 
							if($day_group == "")
									$day_group = date("Y-m-d",strtotime($INOUT_DATE));

							if($month_group == "")
									$month_group = date("Y-m",strtotime($INOUT_DATE));

						}

							if($view_daily == "Y") {
						?>
						<tr height="30" class="row_daily" data-value="<?=$day_group?>">
							<td></td>
							<td colspan="2">�ϰ� :<?=$day_group?></td>
							<td class="modeual_nm"></td>
							<td class="price"><?=number_format($day_qty_total)?></td>
							<td></td>
							<td class="price"><?=number_format($day_deposit_total)?></td>
							<td class="price"><?=number_format($day_withdraw_total)?></td>
							<td class="price"><?=number_format($day_surtax_total)?></td>
							<td class="price"><b><?=number_format($day_balance_total, 0)?></b></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<?  }  ?>
						<tr height="30" class="row_monthly">
							<td></td>
							<td colspan="2">���� :<?=$month_group?></td>
							<td class="modeual_nm"></td>
							<td class="price"><?=number_format($month_qty_total)?></td>
							<td></td>
							<td class="price"><?=number_format($month_deposit_total)?></td>
							<td class="price"><?=number_format($month_withdraw_total)?></td>
							<td class="price"><?=number_format($month_surtax_total)?></td>
							<td class="price"><b><?=number_format($month_balance_total, 0)?></b></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr height="30" class="row_period">
							<td></td>
							<td colspan="2">�Ⱓ�� :</td>
							<td class="modeual_nm"></td>
							<td class="price"><?=number_format($period_qty_total)?></td>
							<td></td>
							<td class="price"><?=number_format($period_deposit_total)?></td>
							<td class="price"><?=number_format($period_withdraw_total)?></td>
							<td class="price"><?=number_format($period_surtax_total)?></td>
							<td class="price"><b><?=number_format($period_balance_total, 0)?></b></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr height="30" class="selected display_none">
							<td></td>
							<td colspan="2">���� �Ѿ� :</td>
							<td class="modeual_nm"></td>
							<td class="price"></td>
							<td></td>
							<td class="price"><span id="total_deposit"></span></td>
							<td class="price"><span id="total_withdraw"></span></td>
							<td class="price"><span id="total_surtax"></span></td>
							<td class="price"></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>

						<? if($view_tax == "Y") { ?>
						<tr height="30">
							<td colspan="13"></td>
						</tr>

						<tr height="30" class="row_tax">
							<td></td>
							<td colspan="2">����:</td>
							<td class="modeual_nm"></td>
							<td class="price"></td>
							<td></td>
							<td class="price"><?=number_format($tax_Y_deposit_total)?></td>
							<td class="price"><?=number_format($tax_Y_withdraw_total)?></td>
							<td class="price"></td>
							<td class="price"></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr height="30" class="row_invoiced">
							<td></td>
							<td colspan="2">����(��꼭):</td>
							<td class="modeual_nm"></td>
							<td class="price"></td>
							<td></td>
							<td class="price"><?=number_format($invoiced_tax_Y_deposit_total)?></td>
							<td class="price"><?=number_format($invoiced_tax_Y_withdraw_total)?></td>
							<td class="price"></td>
							<td class="price"></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr height="30" class="row_tax">
							<td></td>
							<td colspan="2">�����:</td>
							<td class="modeual_nm"></td>
							<td class="price"></td>
							<td></td>
							<td class="price"><?=number_format($tax_N_deposit_total)?></td>
							<td class="price"><?=number_format($tax_N_withdraw_total)?></td>
							<td class="price"></td>
							<td class="price"></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr height="30" class="row_invoiced">
							<td></td>
							<td colspan="2">�����(��꼭):</td>
							<td class="modeual_nm"></td>
							<td class="price"></td>
							<td></td>
							<td class="price"><?=number_format($invoiced_tax_N_deposit_total)?></td>
							<td class="price"><?=number_format($invoiced_tax_N_withdraw_total)?></td>
							<td class="price"></td>
							<td class="price"></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<? } ?>
						<? 

					} else { 
				?>

				<tr height="35">
					<td colspan="13">�����Ͱ� �����ϴ�.</td>
				</tr>

				<? } ?>
			</table>
			<script>
				$(function(){
					$(".memo_trigger").on("click",function(){
						var cl_no = $(this).data("cl_no");
						js_memo_view(cl_no);
					});
				});
			</script>
				
				<div style="width: 95%; margin: 10px 0 20px 0; overflow:hidden;">
					<div style="width:30%; float:left;">
						<? if ($sPageRight_D == "Y" && $cp_type <> "") {?>
						<input type="button" name="b" class="btntxt" value=" ���� �ǻ� ���� " onclick="js_write();"/>
						<? } ?>
						<? if ($sPageRight_D == "Y" && $cp_type <> "") {?>
						<input type="button" name="b" class="btntxt" value=" ������ ���� " onclick="js_recalc();"/>
						<? } ?>
					</div>
					<div style="width:70%; float:right; text-align: right;">
						<? if ($sPageRight_U == "Y" || $sPageRight_I == "Y") {?>
						<b>������ : </b><input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="tax_confirm_date" value="<?=$day_0?>" maxlength="10"/>
						<input type="button" name="aa" value=" ���� ���ں��� " class="btntxt" onclick="js_update_inout_date();"> 
						<? } ?>
						<? if ($sPageRight_D == "Y") {?>
						<input type="button" name="aa" value=" ���� ���� " class="btntxt" onclick="js_delete();"> 
						<? } ?>
					</div>
				</div>
				
					
				<br />
				<h3>���� �߼�</h3>
				<?
					$email_subject = "��)����Ʈ�ݿ��� �߼��ϴ� �ŷ������Դϴ�_".getCompanyNameWithNoCode($conn,$cp_type);
		
					$arr_op_cp = getOperatingCompany($conn, $s_adm_com_code);
					$OP_CP_NM = $arr_op_cp[0]["CP_NM"]." ".$arr_op_cp[0]["CP_NM2"];
					$OP_CEO_NM = $arr_op_cp[0]["CEO_NM"];
					$OP_CP_ADDR = $arr_op_cp[0]["CP_ADDR"];
					$OP_CP_PHONE = $arr_op_cp[0]["CP_PHONE"];
					$OP_EMAIL = $arr_op_cp[0]["EMAIL"];

					$ADM_PHONE = getAdminPhone($conn, $s_adm_no);

					if($ADM_PHONE == "")
						$ADM_PHONE = $OP_CP_PHONE;

					$email_body = getDcodeExtByCode($conn, "MESSAGE_TEMPLATE", "LEDGER");
					$email_body = str_replace("[ȸ���]", $OP_CP_NM, $email_body);
					$email_body = str_replace("[�߽���]", $s_adm_nm, $email_body);
					$email_body = str_replace("[��ǥ��ȣ]", $OP_CP_PHONE, $email_body);
					$email_body = str_replace("[������]", $print_title, $email_body);
					$email_body = str_replace("[�����ڹ�ȣ]", $ADM_PHONE, $email_body);
					$email_body = str_replace("[����]", "\r\n", $email_body);
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
						<th>�̸���</th>
						<td class="line" colspan="3">
							<input type="text" class="txt" name="sent_email" value="<?=getCompanyEmail($conn, $cp_type)?>" style="width: 90%;" placeholder="������ ������ ������ ������ ',' Ȥ�� ';'�� ��ĭ���� �ٿ��� �Է����ּ���."/> 
						</td>
						<td><input type="button" name="b0" value=" �߼� " class="btntxt" onclick="this.style.visibility='hidden';  js_send_email(); ">
						</td>
						
					</tr>
					<tr>
						<th>���� ����</th>
						<td class="line" colspan="4">
							<input type="text" class="txt" name="email_subject" value="<?=$email_subject?>" style="width: 85%;"/> 
						</td>
					</tr>
					<tr>
						<th>���� ����</th>
						<td colspan="4" class="memo">
							<textarea style="width:85%; height:160px;" name="email_body"><?= $email_body ?></textarea>
						</td>
					</tr>
				</tbody>
				</table>
				<div class="sp20"></div>

				<div class="sp30"></div>
		<!-- // E: mwidthwrap -->
			</div>
			<!-- // E: mwidthwrap -->

		</td>
	</tr>
	</table>
	
	<div style="display:scroll;position:fixed;bottom:10px;right:10px;padding:10px;border:1px solid black;background-color:#fff;">
		
		<? if ($sPageRight_D == "Y" || $sPageRight_U == "Y" || $sPageRight_I == "Y") {?>
		<b> �������� : </b><?=makeSelectBox($conn, 'CASH_STATEMENT_TYPE', 'cf_type','100','�ʼ�����','',$cf_type)?>
		<div id="confirm_group">
			<b> ���ι�ȣ : </b>
			<input type="text" name="cf_code" value="" style="width:200px;" placeholder="���ι�ȣ �Է�"/>
			<input type="button" name="aa" value=" ����ó�� " class="btntxt" onclick="js_confirm_tax_invoice('Y');">
			<input type="button" name="aa" value=" ������� " class="btntxt" onclick="js_confirm_tax_invoice('N');"> 
			<input type="button" name="aa" value=" ��ȣ������ " class="btntxt" onclick="js_minus_tax_invoice();"> 
		</div>
		<div id="confirm_group2">
			<input type="file" id="mro_file_nm" class="inputfile" />
			<input type="button" value="�̸�����" id="preview" class="btntxt" />
			<input type="button" value="�ʱ�ȭ" id="reset" class="btntxt" />
			<input type="button" value="����" id="apply" class="btntxt" />
		</div>
		<? } ?>

		<!--<input type="button" name="aa" value=" ���ι�ȣ �ϰ����� " class="btntxt" onclick="js_change_cf_code(frm.cf_code.value)">-->

		<a href="#">�� ����</a>
	</div>

	<script type="text/javascript">
		$(function(){
			$("[name=cf_code]").keyup(function(){
				var cf_type = $("[name=cf_type] option:selected").val();
				
				if(cf_type != "CF004" && cf_type != "CF005") { 
					key_text = $(this).val();
					if(key_text.length == 8) { 
						$(this).val(key_text + "-10000000-");
					}
				}
			});

		});
	
	</script>
</div>
<script>

	$(function(){

		//���ݰ�꼭 ���� - �����Ǿ��ִ� ������ ���� Ȥ�� �⺻ ���ݰ�꼭�� ����
		if($.cookie('cookie_cf_type') != undefined) { 
			if($.cookie('cookie_cf_type') != "Y") { 
				$("select[name=cf_type]").val($.cookie('cookie_cf_type'));
			}
		} else 
			$("select[name=cf_type]").val("CF002");

		function calcSelectedLedger() { 
			var total_withdraw = 0;
			var total_deposit = 0;
			var total_surtax = 0;
			
			$("input[name='chk_no[]']").each(function(){
				
				if($(this).prop('checked')==true) { 

					var withdraw = $(this).closest("tr").find("td.row_withdraw").data("value");
					var deposit = $(this).closest("tr").find("td.row_deposit").data("value");
					var surtax = $(this).closest("tr").find("td.row_surtax").data("value");
					
					total_withdraw += parseFloat(withdraw);
					total_deposit += parseFloat(deposit);
					total_surtax += parseFloat(surtax);

				}
				
			});

			//alert("total_withdraw:" + total_withdraw + ",total_deposit : " + total_deposit); 

			if(total_withdraw != 0 || total_deposit != 0) {
				$(".selected").show();
				$("#total_withdraw").html(numberFormat(total_withdraw));
				$("#total_deposit").html(numberFormat(total_deposit));
				$("#total_surtax").html(numberFormat(total_surtax));
			} else { 
				$(".selected").hide();
				$("#total_withdraw").html("");
				$("#total_deposit").html("");
				$("#total_surtax").html("");
			}

		}
		
		$(document).on("click","input[name='chk_no[]'], input[name=all_chk]",function(){
			calcSelectedLedger();
		});

		/*
		// ��꼭 ���� ��Ī ��� �׽�Ʈ�ϱ� ������ ����
		$(".btn_cf_code").click(function(){
			
			var elem = $(this);
			var cf_code = elem.data("cf_code");
			var total_price = elem.attr("data-total_price");

			if(total_price == "") { 

				(function() {
				  $.getJSON( "json_company_ledger.php", {
					mode: "GET_TOTAL_PRICE",
					cf_code: cf_code
				  })
					.done(function( data ) {
					  if(data.length > 0 && data[0].TOTAL_PRICE > 0) { 
						elem.attr("data-total_price", numberFormat(data[0].TOTAL_PRICE));
						elem.html(numberFormat(data[0].TOTAL_PRICE));
					  } else { 
						elem.attr("data-total_price", "N");
						elem.html("��꼭 ��������");
					  }
					});
				})();

			} else { 
				var total_price = elem.attr("data-total_price");

				alert(total_price);

				if(total_price != "N")
					elem.html(total_price);
				else
					elem.html("��꼭 ��������");
			}
			
		});
		*/

	});

</script>
</form>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>