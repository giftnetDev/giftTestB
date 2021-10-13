<?
ini_set('memory_limit',-1);
session_start();
?>
<?
# =============================================================================
# File Name    : order_write_file.php
# Modlue       : 
# Writer       : Min sung wook
# Create Date  : 2015.07.21
# Modify Date  : 
#	Copyright    : Copyright @C&C Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "OD003"; // �޴����� ���� �� �־�� �մϴ�

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

#====================================================================
# common_header Check Session
#====================================================================
	include "../../_common/common_header.php"; 

	
#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/company/company.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/member/member.php";
	require "../../_classes/biz/admin/admin.php";
	require "../../_classes/biz/payment/payment.php";
#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);
	
	if ($this_date == "") 
		$this_date = date("Y-m-d H:i:s",strtotime("0 month"));
#====================================================================
# DML Process
#====================================================================
	if ($mode == "FR") {
	
	#====================================================================
		$savedir1 = $g_physical_path."upload_data/temp_order";
	#====================================================================

		$file_nm	= upload($_FILES[file_nm], $savedir1, 10000 , array('xls'));

		//echo $file_nm;
		require_once '../../_excel_reader/Excel/reader.php';
		$data = new Spreadsheet_Excel_Reader();
		$data->setOutputEncoding('euc-kr');
		//$data->read('test.xls');
		$data->read("../../upload_data/temp_order/".$file_nm);

		$temp_no = $file_nm;
		
		error_reporting(E_ALL ^ E_NOTICE);
		
		for($i = 2 ; $i <= $data->sheets[0]['numRows']; $i++) {

			$cp_order_no        = SetStringToDB(trim($data->sheets[0]['cells'][$i][1]));
			$cp_code            = SetStringToDB(trim($data->sheets[0]['cells'][$i][2]));
			$goods_code         = SetStringToDB(trim($data->sheets[0]['cells'][$i][3]));
			$goods_name         = SetStringToDB(trim($data->sheets[0]['cells'][$i][4]));
			$goods_price		= SetStringToDB(trim($data->sheets[0]['cells'][$i][5]));
			$qty				= SetStringToDB(trim($data->sheets[0]['cells'][$i][6]));
			$o_name				= SetStringToDB(trim($data->sheets[0]['cells'][$i][7]));
			$o_phone			= SetStringToDB(trim($data->sheets[0]['cells'][$i][8]));
			$o_hphone		    = SetStringToDB(trim($data->sheets[0]['cells'][$i][9]));
			$r_name				= SetStringToDB(trim($data->sheets[0]['cells'][$i][10]));
			$r_phone			= SetStringToDB(trim($data->sheets[0]['cells'][$i][11]));
			$r_hphone			= SetStringToDB(trim($data->sheets[0]['cells'][$i][12]));
			$r_zipcode			= SetStringToDB(trim($data->sheets[0]['cells'][$i][13]));
			$r_addr1			= SetStringToDB(trim($data->sheets[0]['cells'][$i][14]));
			$memo				= SetStringToDB(trim($data->sheets[0]['cells'][$i][15]));
			$opt_wrap_code		= SetStringToDB(trim($data->sheets[0]['cells'][$i][16]));
			$opt_sticker_code	= SetStringToDB(trim($data->sheets[0]['cells'][$i][17]));
			$opt_sticker_msg	= SetStringToDB(trim($data->sheets[0]['cells'][$i][18]));
			$opt_print_msg		= SetStringToDB(trim($data->sheets[0]['cells'][$i][19]));
			$opt_outbox_tf		= SetStringToDB(trim($data->sheets[0]['cells'][$i][20]));
			$opt_manager_name	= SetStringToDB(trim($data->sheets[0]['cells'][$i][21]));
			$opt_outstock_date	= SetStringToDB(trim($data->sheets[0]['cells'][$i][22]));
			$delivery_type		= SetStringToDB(trim($data->sheets[0]['cells'][$i][23]));
			$delivery_price		= SetStringToDB(trim($data->sheets[0]['cells'][$i][24]));
			$opt_memo			= SetStringToDB(trim($data->sheets[0]['cells'][$i][25]));

			$order_state        = '1';
			$use_tf				= 'Y';
			$reg_adm			= $s_adm_no;
			$order_seq = 1;

			$goods_no = tryGoodNoByGoodsCode($conn, $goods_code);
			$cp_no	  = tryCompanyNoByCompanyCode($conn, $cp_code);

			if($opt_sticker_code <> "")
				$opt_sticker_no	  = tryGoodNoByGoodsCode($conn, $opt_sticker_code);
			else 
				$opt_sticker_no	  = "";

			if($opt_wrap_code <> "")
				$opt_wrap_no	  = tryGoodNoByGoodsCode($conn, $opt_wrap_code);	
			else 
				$opt_wrap_no = "";

			if($opt_manager_name <> "")
				$opt_manager_no	  = tryAdminNoByName($conn, $opt_manager_name);	
			else 
				$opt_manager_no = "";

			$inserted_order_no = insertTempOrder($conn, $temp_no, $cp_no, $o_name, $o_phone, $o_hphone, $r_name, $r_phone, $r_hphone, $r_zipcode, $r_addr1, $memo, $order_state, $cp_order_no, $opt_manager_name, $use_tf, $reg_adm);
		
			insertTempOrderGoods($conn, $temp_no, $inserted_order_no, $order_seq, $goods_no, $goods_code, $goods_name, $goods_price, $qty,$opt_sticker_no, $opt_sticker_code, $opt_sticker_msg, $opt_outbox_tf, $opt_wrap_no, $opt_wrap_code, $opt_print_msg, $opt_outstock_date, $opt_memo, $delivery_type, $delivery_price);

		}
		

?>	
<script language="javascript">
		location.href =  'order_write_file.php?mode=L&temp_no=<?=$file_nm?>&this_date=<?=$this_date?>';
</script>
<?
		exit;

	}	
	

	if ($mode == "I") {

		$row_cnt = count($ok);
		
		$str_order_no = "";

		for ($k = 0; $k < $row_cnt; $k++) {
			$str_order_no .= "'".$ok[$k]."',";
		}

		$str_order_no = substr($str_order_no, 0, (strlen($str_order_no) -1));
		//echo $str_cp_no;

		$insert_result = insertTempToRealOrderWithDate($conn, $temp_no, $str_order_no, $this_date);

		if ($insert_result) {
			$delete_result = deleteTempToRealOrder($conn, $temp_no, $str_order_no);
		}

		$mode = "L";

	}

	if ($mode == "D") {

		$row_cnt = count($chk);
		
		for ($k = 0; $k < $row_cnt; $k++) {
		
			$tmp_order_no = $chk[$k];

			$temp_result = deleteTempOrder($conn, $temp_no, $tmp_order_no);
		}
		
		$mode = "L";
	}

	if ($mode == "L") {
		$arr_rs = listTempOrder($conn, $temp_no);
	}
	
	if ($result) {
?>	
<script language="javascript">
		alert('���� ó�� �Ǿ����ϴ�.');
		location.href =  'order_list.php';
</script>
<?
		exit;
	}	
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script type="text/javascript" src="../jquery/jquery-ui-timepicker-addon.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui-timepicker-addon.css" type="text/css" />
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<script>
  $(function() {
   /*
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

  */

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
<style type="text/css">
<!--
/*#pop_table {z-index: 1; left: 80; overflow: auto; width: 500; height: 220}*/
#ex_scroll { z-index: 1; background-color:#f7f7f7; overflow: auto; width: 100%; height:155px; border:1px solid #d1d1d1;}
#temp_scroll { z-index: 1; background-color:#f7f7f7; overflow: auto; width: 95%; height:500px; border:1px solid #d1d1d1;}
-->
</style>

<script language="javascript">
	
	// ��ȸ ��ư Ŭ�� �� 
	function js_list() {
		var frm = document.frm;
		
		frm.method = "get";
		frm.action = "order_list.php";
		frm.submit();
	}

	// ���� ��ư Ŭ�� �� 
	function js_save() {
		
		var file_rname = "<?= $file_rname ?>";
		var frm = document.frm;

		//frm.full_date.value = frm.this_date.value+" "+frm.this_h.value+":"+frm.this_m.value+":00";

		//alert(frm.full_date.value);
		
		if (isNull(frm.file_nm.value)) {
			alert('������ ������ �ּ���.');
			frm.file_nm.focus();
			return ;		
		}
		
		AllowAttach(frm.file_nm);

		if (isNull(file_rname)) {
			frm.mode.value = "FR";
		} else {
			frm.mode.value = "I";
		}

		frm.method = "post";
		frm.action = "order_write_file.php";
		frm.submit();
	}

	//�����ȣ ã��
	function js_post(zip, addr) {
		var url = "/_common/common_post.php?zip="+zip+"&addr="+addr;
		NewWindow(url, '�����ȣã��', '390', '370', 'NO');
	}

	/**
	* ���� ÷�ο� ���� ���ÿ� ���� ����÷�� �Է¶� visibility ����
	*/
	function js_fileView(obj,idx) {
		
		var frm = document.frm;
		
		if (idx == 01) {
			if (obj.selectedIndex == 2) {
				frm.contracts_nm.style.visibility = "visible";
			} else {
				frm.contracts_nm.style.visibility = "hidden";
			}
		}

	}

	function LimitAttach(obj) {
		var file = obj.value;
		extArray = new Array(".jsp", ".cgi", ".php", ".asp", ".aspx", ".exe", ".com", ".php3", ".inc", ".pl", ".asa", ".bak");
		allowSubmit = false;
		
		if (!file){
			//form1.submit();
		}

		while (file.indexOf("\\") != -1){
			file = file.slice(file.indexOf("\\") + 1);
			ext = file.slice(file.indexOf(".")).toLowerCase();

			for (var i = 0; i < extArray.length; i++){
				if (extArray[i] == ext){ 
					allowSubmit = true; 
					break; 
				}
			}
		}

		if (!allowSubmit){
			//
		}else{
			alert("�Է��Ͻ� ������ ���ε� �� �� �����ϴ�!");
			return;
		}
	}

	function AllowAttach(obj) {
		var file = obj.value;
		extArray = new Array(".xls");
		allowSubmit = false;
		
		if (!file){
			//form1.submit();
		}

		while (file.indexOf("\\") != -1){
			file = file.slice(file.indexOf("\\") + 1);
			ext = file.slice(file.indexOf(".")).toLowerCase();

			for (var i = 0; i < extArray.length; i++){
				if (extArray[i] == ext){ 
					allowSubmit = true; 
					break; 
				}
			}
		}

		if (allowSubmit){
			//
		}else{
			alert("�Է��Ͻ� ������ ���ε� �� �� �����ϴ�!");
			return;
		}
	}

	function js_view(rn, file_nm, order_no, type) {
		
		if(type == "order") { 
			var url = "order_modify.php?mode=S&temp_no="+file_nm+"&order_no="+order_no;
			NewWindow(url, '�ֹ��뷮�Է�', '860', '513', 'YES');
		} else {
			var url = "order_modify_goods_add.php?mode=S&temp_no="+file_nm+"&order_no="+order_no;
			NewWindow(url, '�ֹ��뷮��ǰ�Է�', '860', '513', 'YES');
		}

		
	}

	function js_reload() {
		location.href =  'order_write_file.php?mode=L&temp_no=<?=$temp_no?>';
	}

	function js_delete() {

		var frm = document.frm;
		var chk_cnt = 0;

		check=document.getElementsByName("chk[]");
		
		for (i=0;i<check.length;i++) {
			if(check.item(i).checked==true) {
				chk_cnt++;
			}
		}
		
		if (chk_cnt == 0) {
			alert("���� �Ͻ� �ڷᰡ �����ϴ�.");
		} else {

			bDelOK = confirm('�����Ͻ� �ڷḦ ���� �Ͻðڽ��ϱ�?');
			
			if (bDelOK==true) {
				frm.mode.value = "D";
				frm.target = "";
				frm.action = "<?=$_SERVER[PHP_SELF]?>";
				frm.submit();
			}
		}
	}

	function js_register() {
		var frm = document.frm;
		bDelOK = confirm('���� ����Ÿ�� ��� ��� �Ͻðڽ��ϱ�?');

		if (bDelOK==true) {
			frm.mode.value = "I";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
		
	}

	function js_unregistered_goods_excel() {
		
		var frm = document.frm;

		frm.target = "";
		
		frm.action = "order_write_file_excel_unregistered_goods.php";
		frm.submit();

		//alert("�ڷ� ���");
	}

	function js_excel() {
		
		var frm = document.frm;

		frm.target = "";
		
		frm.action = "order_write_file_excel_mart.php";
		frm.submit();

		//alert("�ڷ� ���");
	}

	function js_temp_goods_excel() {
		
		var frm = document.frm;

		frm.target = "";
		frm.action = "order_write_file_temp_goods_excel.php";
		frm.submit();

	}

	function js_open_pop_MRO() {
		
		var url = "pop_order_MRO_conversion.php";
		NewWindow(url, 'MRO��ȯ', '950', '513', 'YES');
		
	}
</script>
</head>
<body id="admin">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="temp_no" value="<?=$temp_no?>">
<input type="hidden" name="cp_no" value="<?=$args_cp_no?>">

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
<style>
	table.rowstable04 { border-top: none; }
	table.rowstable04 > th { padding: 9px 0 8px 0; font-weight: normal; color: #86a4b2; border-top: 1px solid #d2dfe5; background: #ebf3f6 url('../images/admin/bg_bar_01.gif') right center no-repeat; }
	table.rowstable04 > th.end { background: #ebf3f6; }
	table.rowstable04 td { color: #555555; text-align: center; vertical-align: middle; background: none; }

</style>
		</td>
		<td class="contentarea">

			<!-- S: mwidthwrap -->
			<div id="mwidthwrap">
				<h2>�ֹ� ��� - ����</h2>
				<div class="btnright">
					<input type="button" name="cc" value=" MRO ��ȯ " class="btntxt" onclick="js_open_pop_MRO();">
				</div>
				<table cellpadding="0" cellspacing="0" class="colstable">

					<colgroup>
						<col width="12%">
						<col width="38%">
						<col width="12%">
						<col width="38%">
					</colgroup>
					<thead>
						<tr>
							<th>
								�Է� ��
								<br><br>
								<a href="/_common/download_file.php?file_name=insert_order.xls&filename_rnm=insert_example.xls&str_path=manager/order/">�ޱ�</a>
							</th>
							<td colspan="3">
								<div id="ex_scroll">
								<img src="order_example.jpg">
								</div>
							</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th>����</th>
							<td><input type="file" name="file_nm" style="width:60%;" class="txt"></td>
							<th>�ֹ���</th>
							<td>
								<input type="text" class="txt datepicker" style="width: 150px; margin-right:3px;" name="this_date" value="<?=$this_date?>" maxlength="10"/>
							</td>
						</tr>
					</tbody>
				</table>

				<div class="btnright">
				<? if ($file_nm <> "" ) {?>
					<? if ($sPageRight_U == "Y") {?>
					<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="Ȯ��" /></a>
					<? } ?>
				<? } else {?>
					<? if ($sPageRight_I == "Y") {?>
					<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="Ȯ��" /></a>
					<? } ?>
				<? }?>
				</div>

				<div class="sp20"></div>
				<div>
					* ��ü <?=totalCntTempOrder($conn, $temp_no)?> �ֹ��Ǽ� �� &nbsp;&nbsp;
					* �ֹ���ȣ�� <?=sizeof($arr_rs)?> �� &nbsp;&nbsp;
					<? if ($insert_result) {?>
					* ��ϰ� <?=$row_cnt?> ��
					<? }?>
				</div>
				<div id="temp_scroll">
				<table cellpadding="0" cellspacing="0" class="rowstable01" style="width:3830px">
					<colgroup>
						<col width="35">
						<col width="150">
						<col width="100">
						<col width="120">
						<col width="120">
						<col width="120">
						<col width="120">
						<col width="120">
						<col width="120">
						<col width="120">
						<col width="80">
						<col width="350">
						<col width="250">
						<col width="80">
						<col width="2020">
					</colgroup>
					<thead>
						<tr>
							<th>&nbsp;</th>
							<th>���</th>
							<th>��ü�ֹ���ȣ</th>
							<th>��ü��</th>
							<th>�ֹ���</th>
							<th>����ó</th>
							<th>�޴���ȭ��ȣ</th>
							<th>������</th>
							<th>����ó</th>
							<th>�޴���ȭ��ȣ</th>
							<th>�����ȣ</th>
							<th>�ּ�</th>
							<th>�ֹ��ڸ޸�</th>
							<th>���������</th>
							<th class="end">
								�ֹ� ��ǰ<br>
								<table cellpadding="0" cellspacing="0" class="rowstable04" style="width:98%">
									<colgroup>
										<col width="100">
										<col width="100">
										<col width="300">
										<col width="80">

										<col width="80">
										<col width="80">
										<col width="200">
										<col width="90">
										<col width="80">
										<col width="80">
										<col width="200">
										<col width="100">
										
										<col width="200">
										<col width="80">
										<col width="80">
									</colgroup>
									<thead>
										<tr>
											<th>��ǰ��ȣ</th>
											<th>��ǰ�ڵ�</th>
											<th>��ǰ��</th>
											<th>����</th>
											
											<th>��ƼĿ��ȣ</th>
											<th>��ƼĿ�ڵ�</th>
											<th>��ƼĿ�޼���</th>
											<th>�ƿ��ڽ���ƼĿ</th>
											<th>��������ȣ</th>
											<th>�������ڵ�</th>
											<th>�μ�޼���</th>
											<th>�������</th>
											
											<th>�۾��޸�</th>
											<th>��۹��</th>
											<th class="end">��۱ݾ�</th>
										</tr>
									</thead>
								</table>
							</th>
						</tr>
					</thead>
					<tbody>
					<?
						$nCnt = 0;
						
						if (sizeof($arr_rs) > 0) {
							
							for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
								
								$rn							= trim($arr_rs[$j]["rn"]);
								$CP_NO					= trim($arr_rs[$j]["CP_NO"]);
								$ORDER_NO				= trim($arr_rs[$j]["ORDER_NO"]);

								$O_NAME					= SetStringFromDB($arr_rs[$j]["O_NAME"]);
								$O_PHONE				= SetStringFromDB($arr_rs[$j]["O_PHONE"]);
								$O_HPHONE				= SetStringFromDB($arr_rs[$j]["O_HPHONE"]);
								$R_NAME					= SetStringFromDB($arr_rs[$j]["R_NAME"]);
								$R_PHONE				= SetStringFromDB($arr_rs[$j]["R_PHONE"]);
								$R_HPHONE				= SetStringFromDB($arr_rs[$j]["R_HPHONE"]);
								$R_ZIPCODE				= SetStringFromDB($arr_rs[$j]["R_ZIPCODE"]);
								$R_ADDR1				= SetStringFromDB($arr_rs[$j]["R_ADDR1"]);
								$OPT_MANAGER_NO			= SetStringFromDB($arr_rs[$j]["OPT_MANAGER_NO"]);
								$OPT_MANAGER_NAME		= SetStringFromDB($arr_rs[$j]["OPT_MANAGER_NAME"]);
								$MEMO					= trim($arr_rs[$j]["MEMO"]);
								$DELIVERY				= trim($arr_rs[$j]["DELIVERY"]);
								$SA_DELIVERY			= trim($arr_rs[$j]["SA_DELIVERY"]);
								$CP_ORDER_NO			= trim($arr_rs[$j]["CP_ORDER_NO"]);
								$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
								$REG_DATE				= date("Y-m-d",strtotime($REG_DATE));

								// ������ ��ȿ�� �˻�
								$err_str_order = "";
								$err_str_order_goods = "";

								if ($CP_NO == "") {
									$err_str_order .=  "�Ǹž�ü ����,";
								} else {
									if (getCompayChk($conn, "�Ǹ�", $s_adm_cp_type, $CP_NO) == "") {
										$err_str_order .=  "�Ǹž�ü ����,";
									}
								}
								
								if ($R_NAME == "") {
									$err_str_order .=  "������ ����,";
								}

								if ($R_HPHONE == "") {
									$err_str_order .=  "�޴���ȭ��ȣ ����,";
								}
								
								/*
								if ($R_PHONE == "") {
									$err_str .=  "����ó ����,";
								}
								
								if ($R_ZIPCODE <> "") {
									if (!chkZip($conn, $R_ZIPCODE)) {
										$err_str .=  "�����ȣ ����,";
									}
								}
								*/

								// ��������ڷ� �˻��ؼ� $OPT_MANAGER_NO ���ϱ�
								if ($OPT_MANAGER_NO == "��Ͽ��") {
									$OPT_MANAGER_NO = tryAdminNoByName($conn, $OPT_MANAGER_NAME);
								}

								if ($OPT_MANAGER_NO == "��Ͽ��" || $OPT_MANAGER_NO == "�������������") {
									$err_str_order .=  "��������� ����,";
								}
								else
								{
									updateTempOrderAdmNo($conn, $ORDER_NO, $ORDER_SEQ, $OPT_MANAGER_NO, $temp_no);
									$arr_rs_temp_goods[$k]["OPT_MANAGER_NO"] = $OPT_MANAGER_NO;
								}

								if ($R_ADDR1 == "") {
									$err_str_order .=  "�ּ� ����,";
								}

								$arr_rs_temp_goods = selectTempOrderGoods($conn, $temp_no, $ORDER_NO);
								if (sizeof($arr_rs_temp_goods) > 0) {
									for ($k = 0 ; $k < sizeof($arr_rs_temp_goods); $k++) {

										$GOODS_NO			= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_NO"]);
										$GOODS_CODE			= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_CODE"]);
										$GOODS_NAME			= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_NAME"]);
										$GOODS_PRICE		= trim($arr_rs_temp_goods[$k]["GOODS_PRICE"]);
										$QTY				= trim($arr_rs_temp_goods[$k]["QTY"]);
										
										//OPT_STICKER_CODE, OPT_STICKER_MSG, OPT_OUTBOX_TF, OPT_WRAP_CODE, OPT_PRINT_MSG, OPT_OUTSTOCK_DATE, OPT_MANAGER_NAME, OPT_MEMO, DELIVERY_TYPE, DELIVERY_PRICE
										
										$OPT_STICKER_NO		= SetStringFromDB($arr_rs_temp_goods[$k]["OPT_STICKER_NO"]);
										$OPT_STICKER_CODE	= SetStringFromDB($arr_rs_temp_goods[$k]["OPT_STICKER_CODE"]);
										$OPT_STICKER_MSG	= SetStringFromDB($arr_rs_temp_goods[$k]["OPT_STICKER_MSG"]);
										$OPT_OUTBOX_TF		= SetStringFromDB($arr_rs_temp_goods[$k]["OPT_OUTBOX_TF"]);
										$OPT_WRAP_NO		= SetStringFromDB($arr_rs_temp_goods[$k]["OPT_WRAP_NO"]);
										$OPT_WRAP_CODE		= SetStringFromDB($arr_rs_temp_goods[$k]["OPT_WRAP_CODE"]);
										$OPT_PRINT_MSG		= SetStringFromDB($arr_rs_temp_goods[$k]["OPT_PRINT_MSG"]);
										$OPT_OUTSTOCK_DATE	= SetStringFromDB($arr_rs_temp_goods[$k]["OPT_OUTSTOCK_DATE"]);
										
										$OPT_MEMO			= SetStringFromDB($arr_rs_temp_goods[$k]["OPT_MEMO"]);
										$DELIVERY_TYPE		= SetStringFromDB($arr_rs_temp_goods[$k]["DELIVERY_TYPE"]);
										$DELIVERY_TYPE_CODE	= SetStringFromDB($arr_rs_temp_goods[$k]["DELIVERY_TYPE_CODE"]);
										$DELIVERY_PRICE		= SetStringFromDB($arr_rs_temp_goods[$k]["DELIVERY_PRICE"]);

										// ��ǰ������ �˻��ؼ� $GOODS_NO ���ϱ�
										if ($GOODS_NO == "��Ͽ��") {

											$GOODS_NAME = SetStringToDB($GOODS_NAME);

											$GOODS_NO = tryGoodNoByGoodsCode($conn, $GOODS_CODE);
										}

										if ($GOODS_NO == "��Ͽ��" || $GOODS_NO == "������ǰ����") {
											$err_str_order_goods .=  "��ǰ��ȣ ����,";
										}
										else
										{
											updateTempOrderGoodsNo($conn, $ORDER_NO, $ORDER_SEQ, $GOODS_NO, $temp_no);
											$arr_rs_temp_goods[$k]["GOODS_NO"] = $GOODS_NO;
										}


										// ��ƼĿ�ڵ����� �˻��ؼ� $OPT_STICKER_NO ���ϱ�
										if ($OPT_STICKER_NO == "��Ͽ��") {
											$OPT_STICKER_NO = tryGoodNoByGoodsCode($conn, $OPT_STICKER_CODE);
										}

										if ($OPT_STICKER_NO == "��Ͽ��" || $OPT_STICKER_NO == "������ƼĿ����") {
											$err_str_order_goods .=  "��ƼĿ��ȣ ����,";
										}
										else
										{
											updateTempOrderStickerNo($conn, $ORDER_NO, $ORDER_SEQ, $OPT_STICKER_NO, $temp_no);
											$arr_rs_temp_goods[$k]["OPT_STICKER_NO"] = $GOODS_NO;
										}

										// �������ڵ�� �˻��ؼ� $OPT_WRAP_NO ���ϱ�
										if ($OPT_WRAP_NO == "��Ͽ��") {
											$OPT_WRAP_NO = tryGoodNoByGoodsCode($conn, $OPT_WRAP_CODE);
										}

										if ($OPT_WRAP_NO == "��Ͽ��" || $OPT_WRAP_NO == "��������������") {
											$err_str_order_goods .=  "��������ȣ ����,";
										}
										else
										{
											updateTempOrderWrapNo($conn, $ORDER_NO, $ORDER_SEQ, $OPT_WRAP_NO, $temp_no);
											$arr_rs_temp_goods[$k]["OPT_WRAP_NO"] = $OPT_WRAP_NO;
										}


										$chkOutboxTF = getDcodeCode($conn, "OUTBOX_STICKER_TF", $OPT_OUTBOX_TF);
										if(!($chkOutboxTF == "Y" || $chkOutboxTF == "N" || $chkOutboxTF == ""))
											$err_str_order_goods .=  "�ƿ��ڽ���ƼĿ������ ����,";
										else
										{
											updateTempOrderOutboxTF($conn, $ORDER_NO, $ORDER_SEQ, $chkOutboxTF, $temp_no);
											$arr_rs_temp_goods[$k]["OPT_OUTBOX_TF_CODE"] = $chkOutboxTF;
										}
											

										$chkDeliveryType = getDcodeCode($conn, "DELIVERY_TYPE", $DELIVERY_TYPE);
										if($DELIVERY_TYPE_CODE == "") {
											if(!($DELIVERY_TYPE != "" || $chkDeliveryType == "&nbsp;")) {
												//echo $DELIVERY_TYPE."/".$chkDeliveryType;
													
												$err_str_order_goods .=  "�ù��� ����,";
											} else 
											{
												updateTempOrderDeliveryType($conn, $ORDER_NO, $ORDER_SEQ, $chkDeliveryType, $temp_no);
												$arr_rs_temp_goods[$k]["DELIVERY_TYPE_CODE"] = $chkDeliveryType;
											}
										}

										$arr_rs_goods = selectGoods($conn, $GOODS_NO);

										// ��ǰ ��ȣ�� �ش��ϴ� �ֹ� ��ǰ �ӽ� ��� ��� �ϱ�
										// ��Ʈ����, ��Ʈ �ֹ���ȣ, ��ǰ��ȣ, ����
										//$fake_result = insertFakeStock($conn, $CP_NO, $CP_ORDER_NO, $GOODS_NO, $QTY);

										if($arr_rs_goods[0] != null){
									
											$rs_buy_price			= trim($arr_rs_goods[0]["BUY_PRICE"]);
											$rs_sale_price			= trim($arr_rs_goods[0]["SALE_PRICE"]);
											$rs_goods_state			= trim($arr_rs_goods[0]["CATE_04"]);

											if ($rs_goods_state <> "�Ǹ���") {
												$str_goods_state = "<font color='red'>".getDcodeName($conn, "GOODS_STATE", $rs_goods_state)."</font>";
											} else {
												$str_goods_state = getDcodeName($conn, "GOODS_STATE", $rs_goods_state);
											}

											if ($CP_NO <> "") {
												$rs_sale_price = getCompanyGoodsPriceOrDCRate($conn, $GOODS_NO, $rs_sale_price, $CP_NO);
											}
											
											if ($GOODS_PRICE <> $rs_sale_price ) {
												$str_price = "<font color='red'>".number_format($GOODS_PRICE)."</font>";
											} else {
												$str_price = number_format($GOODS_PRICE);
											}
											
											if ($GOODS_PRICE < $rs_buy_price) {
												$str_plus_price = "<font color='red'>".number_format($GOODS_PRICE - $rs_buy_price)."</font>";
											} else {
												$str_plus_price = number_format($GOODS_PRICE - $rs_buy_price);
											}
										}

										if ($QTY == "") {
											$err_str_order_goods .=  "���� ����,";
										} else {
											if ($QTY  < "1") {
												//$err_str .=  "���� ����,";
											}
										}
										
									}
								}
								
								/*								
								if ($R_ADDR1 == "") {
									$R_ADDR1 = "���� ���� ��� �ֹ�";
								}
								*/


								if ($err_str_order <> "") {
									$err_str_order = substr($err_str_order, 0, (strlen($err_str_order) -1));
									$err_str_order = str_replace(",","<div class='sp5'></div>",$err_str_order);
									$err_str_order = "<font color='red'>".$err_str_order."</font>";
								} else
									$err_str_order = "�ֹ����� ����";

								if ($err_str_order_goods <> "") {
									$err_str_order_goods = substr($err_str_order_goods, 0, (strlen($err_str_order_goods) -1));
									$err_str_order_goods = str_replace(",","<div class='sp5'></div>",$err_str_order_goods);
									$err_str_order_goods = "<font color='red'>".$err_str_order_goods."</font>";
								} else
									$err_str_order_goods = "��ǰ���� ����";
								

					?>
						<tr>
							<td class="filedown">
								<input type="checkbox" name="chk[]" value="<?=$ORDER_NO?>">
							</td>
							<td class="modeual_nm" style="padding:8px 0 3px 0">
								<a href="javascript:js_view('<?= $rn ?>','<?= $temp_no ?>','<?= $ORDER_NO ?>', 'order');"><?=$err_str_order?></a><br/>
								<a href="javascript:js_view('<?= $rn ?>','<?= $temp_no ?>','<?= $ORDER_NO ?>', 'goods');"><?=$err_str_order_goods?></a>
								<? if ($err_str_order == "�ֹ����� ����" && $err_str_order_goods == "��ǰ���� ����") {?>
								<input type="hidden" name="ok[]" value="<?=$ORDER_NO?>">
								<? } ?>
							</td>
							<td><?= $CP_ORDER_NO?></td>
							
							<td class="modeual_nm"><?=getCompanyName($conn, $CP_NO)?></a></td>
							<td><?= $O_NAME?></td>
							<td><?= $O_PHONE?></td>
							<td><?= $O_HPHONE?></td>
							<td><?= $R_NAME?></td>
							<td><?= $R_PHONE?></td>
							<td><?= $R_HPHONE?></td>
							<td><?= $R_ZIPCODE?></td>
							<td class="modeual_nm"><?= $R_ADDR1?></td>
							<td class="modeual_nm"><?= $MEMO?></td>
							<td class="modeual_nm"><?=$OPT_MANAGER_NAME?></td>
							<td>
								<table cellpadding="0" cellspacing="0" class="rowstable04"  style="width:98%">
									<colgroup>
										<col width="100">
										<col width="100">
										<col width="300">
										<col width="80">

										<col width="80">
										<col width="80">
										<col width="200">
										<col width="80">
										<col width="80">
										<col width="80">
										<col width="200">
										<col width="100">
									
										<col width="200">
										<col width="80">
										<col width="80">
									</colgroup>
								<?
									if (sizeof($arr_rs_temp_goods) > 0) {
										for ($k = 0 ; $k < sizeof($arr_rs_temp_goods); $k++) {

											$GOODS_NO			= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_NO"]);
											$GOODS_CODE			= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_CODE"]);
											$GOODS_NAME			= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_NAME"]);
											$GOODS_PRICE		= trim($arr_rs_temp_goods[$k]["GOODS_PRICE"]);
											$QTY				= trim($arr_rs_temp_goods[$k]["QTY"]);

											//OPT_STICKER_CODE, OPT_STICKER_MSG, OPT_OUTBOX_TF, OPT_WRAP_CODE, OPT_PRINT_MSG, OPT_OUTSTOCK_DATE, OPT_MANAGER_NAME, OPT_MEMO, DELIVERY_TYPE, DELIVERY_PRICE

											?>
											<tr>
												<td><?= $GOODS_NO?></td>
												<td><?= $GOODS_CODE?></td>
												<td class="modeual_nm"><?= $GOODS_NAME?></td>
												<td><?= number_format($QTY)?></td>

												<td><?=$OPT_STICKER_NO?></td>
												<td><?=$OPT_STICKER_CODE?></td>
												<td><?=$OPT_STICKER_MSG?></td>
												<td><?=$OPT_OUTBOX_TF?></td>
												<td><?=$OPT_WRAP_NO?></td>
												<td><?=$OPT_WRAP_CODE?></td>
												<td><?=$OPT_PRINT_MSG?></td>
												<td><?=$OPT_OUTSTOCK_DATE?></td>
												
												<td><?=$OPT_MEMO?></td>
												<td><?=$DELIVERY_TYPE?></td>
												<td><?=$DELIVERY_PRICE?></td>
											</tr>
											<?
										}
									}
								?>
								</table>
							</td>
							
						</tr>
					<?			
										$err_str = "";
									}
								} else { 
					?> 
								<tr>
									<td align="center" height="50"  colspan="25">�����Ͱ� �����ϴ�. </td>
								</tr>
					<? 
								}
					?>
							</tbody>
						</table>
					</div>


				<div class="btnright">
					<!--
					<input type="button" name="aa" value=" ��ǰ��Ͽ�� ����Ʈ " class="btntxt" onclick="js_unregistered_goods_excel();">&nbsp;&nbsp;&nbsp;&nbsp; 
					<input type="button" name="aa2" value=" �̵���ڷ� �����ޱ� " class="btntxt" onclick="js_excel();">&nbsp;&nbsp;&nbsp;&nbsp;
					-->
					<input type="button" name="bb" value=" �����ڷ� ��� " class="btntxt" onclick="js_register();">&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="button" name="cc" value=" �����ڷ� ���� " class="btntxt" onclick="js_delete();">
				</div>

			</div>

			<?
				// ���� �ֹ� ��ǰ ���� ����Ʈ
				//$order_goods_list = listTempOrderCnt($conn, $temp_no);
			?>
			* ���� �ֹ� ��ǰ ���� ����Ʈ &nbsp; <a href="javascript:js_temp_goods_excel();"><img src="../images/common/btn/btn_excel.gif" alt="�ֹ� ��ǰ ���� ����Ʈ"></a>
			<table cellpadding="0" cellspacing="0" class="rowstable" style="width:auto;">

				<colgroup>
					<col width="120px" />
					<col width="120px" />
					<col width="120px" />
					<col width="300px" />
					<col width="120px" />
					<col width="120px" />
				</colgroup>
				<thead>
					<tr>
						<th>��ǰ����</th>
						<th>��ǰ�ڵ�</th>
						<th>�������ڵ�</th>
						<th>��ǰ��</th>
						<th>�ڽ��Լ�</th>
						<th class="end">�ֹ�����*������ǰ���� (�ֹ�����)</th>
					</tr>
				</thead>
				<tbody>
				<?
					if (sizeof($order_goods_list) > 0) {
						
						for ($j = 0 ; $j < sizeof($order_goods_list); $j++) {
							//GOODS_NO	GOODS_CODE	GOODS_NAME DELIVERY_CNT_IN_BOX CNT
							$GOODS_NO		= trim($order_goods_list[$j]["GOODS_NO"]);
							$CATE_02		= trim($order_goods_list[$j]["CATE_02"]);
							$CATE_02 = getDcodeName($conn, "GOODS_SUB_CATE", $CATE_02);
							$GOODS_CODE		= trim($order_goods_list[$j]["GOODS_CODE"]);
							$KANCODE		= trim($order_goods_list[$j]["KANCODE"]);
							$GOODS_NAME		= SetStringFromDB($order_goods_list[$j]["GOODS_NAME"]);
							$DELIVERY_CNT_IN_BOX = trim($order_goods_list[$j]["DELIVERY_CNT_IN_BOX"]);
							$CNT		    = trim($order_goods_list[$j]["CNT"]);
				?>
					<tr>
						<td height="24px"><?=$CATE_02?></td>
						<td><?=$GOODS_CODE?></td>
						<td><?=$KANCODE?></td>
						<td class="pname" style="text-align:left;padding-left:5px;"><?=$GOODS_NAME?></td>
						<td><?=$DELIVERY_CNT_IN_BOX?></td>
						<td style="text-align:right;padding-right:5px;"><?=number_format($CNT)?></td>
					</tr>
				<?
						}
					}else {
				?>
					<tr>
						<td colspan="6" height="30">�����Ͱ� �����ϴ�</td>
					</tr>
				<?
					}
				?>
				</tbody>
			</table>
			<br/>
			<div class="sp20"></div>
		</form>
    </td>
  </tr>
  </table>




</div>
<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>