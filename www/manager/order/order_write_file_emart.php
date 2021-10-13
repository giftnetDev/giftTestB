<?
ini_set('memory_limit',-1);
session_start();
?>
<?

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "OD011"; // �޴����� ���� �� �־�� �մϴ�

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
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/member/member.php";
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
		
		error_reporting(E_ALL ^ E_NOTICE);

		/*
						 A.L AS GOODS_NAME, 
						 A.X AS GOODS_PRICE, 
						 A.V AS QTY, 
						 A.F AS GOODS_OPTION_NM2, 
						 A.P AS GOODS_OPTION_NM, 
						 A.AD MEMO, 
						 
						 A.D AS CP_ORDER_NO, 
						 A.O AS GOODS_MART_CODE 
		*/

		for($k = 1; $k <= $data->sheets[0]['numCols'] ; $k++) {
			if(trim($data->sheets[0]['cells'][1][$k]) == "��ǰ��") 	         $GOODS_NAME = $k;                       
			else if(trim($data->sheets[0]['cells'][1][$k]) == "�ǸŰ�")        $GOODS_PRICE = $k;				
			else if(trim($data->sheets[0]['cells'][1][$k]) == "���ü���")		 $QTY = $k;				
			else if(trim($data->sheets[0]['cells'][1][$k]) == "��۹�ȣ")       $GOODS_OPTION_NM2 = $k;		
			else if(trim($data->sheets[0]['cells'][1][$k]) == "�Ӽ�")         $GOODS_OPTION_NM = $k;	    
			else if(trim($data->sheets[0]['cells'][1][$k]) == "����۸޸�")    $MEMO = $k;			
			else if(trim($data->sheets[0]['cells'][1][$k]) == "�ֹ���ȣ")		 $CP_ORDER_NO = $k;	     
			else if(trim($data->sheets[0]['cells'][1][$k]) == "��ǰ��ȣ")       $GOODS_MART_CODE = $k;  
			else if(trim($data->sheets[0]['cells'][1][$k]) == "�������")       $OUT_TYPE = $k;  
			
		}

		//�ֹ� Sheet
		$not_normal_outstock_cnt = 0;
		for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
			
			$A	= '';
			$B	= SetStringToDB(trim($data->sheets[0]['cells'][$i][$OUT_TYPE]));
			$C	= '';
			$D	= SetStringToDB(trim($data->sheets[0]['cells'][$i][$CP_ORDER_NO]));
			$E	= '';
			$F	= SetStringToDB(trim($data->sheets[0]['cells'][$i][$GOODS_OPTION_NM2]));
			$G	= '';
			$H	= '';
			$I	= '';
			$J	= '';
			$K	= '';
			$L	= SetStringToDB(trim($data->sheets[0]['cells'][$i][$GOODS_NAME]));
			$M	= '';
			$N	= '';
			$O	= SetStringToDB(trim($data->sheets[0]['cells'][$i][$GOODS_MART_CODE]));
			$P	= SetStringToDB(trim($data->sheets[0]['cells'][$i][$GOODS_OPTION_NM]));
			$Q	= '';
			$R	= '';
			$S	= '';
			$T	= '';
			$U	= '';
			$V	= SetStringToDB(trim($data->sheets[0]['cells'][$i][$QTY]));
			$W	= '';
			$X	= SetStringToDB(trim($data->sheets[0]['cells'][$i][$GOODS_PRICE]));
			$Y	= '';
			$Z	= '';
			$AA	= '';
			$AB	= '';
			$AC	= '';
			$AD	= SetStringToDB(trim($data->sheets[0]['cells'][$i][$MEMO]));
			$AE	= '';
			$AF	= '';
			$AG	= '';

			if($D <> '')
			{
				if($B == "�Ϲ����")
					$temp_result = insertOrderEmart_Order($conn, $file_nm, $A, $B, $C, $D, $E, $F, $G, $H, $I, $J, $K, $L, $M, $N, $O, $P, $Q, $R, $S, $T, $U, $V, $W, $X, $Y, $Z, $AA, $AB, $AC, $AD, $AE, $AF, $AG,$s_adm_no);
				else
					$not_normal_outstock_cnt ++; 
			}
		}

		//��� Sheet

		for($j = 1; $j <= $data->sheets[1]['numCols'] ; $j++) {
			if(trim($data->sheets[1]['cells'][1][$j]) == "����") 	$RN = $j;                           // ����
			else if(trim($data->sheets[1]['cells'][1][$j]) == "��۹�ȣ") $Dnumber = $j;				// ��۹�ȣ
			else if(trim($data->sheets[1]['cells'][1][$j]) == "�ֹ���") $SenderName = $j;				// �ֹ���
			//else if(trim($data->sheets[1]['cells'][1][$j]) == "�������ȣ") $SenderOldPostcode = $j;    //�����»�������ȣ
			//else if(trim($data->sheets[1]['cells'][1][$j]) == "������ �����ּ�") $SenderOldAddress = $j; //�����»���ּ�
			else if(trim($data->sheets[1]['cells'][1][$j]) == "�ֹ�����ȭ��ȣ") $SenderPhone = $j;		// �ֹ�����ȭ��ȣ
			else if(trim($data->sheets[1]['cells'][1][$j]) == "�ֹ����޴�����ȣ") $SenderHPhone = $j;	    // �ֹ����޴�����ȣ
			else if(trim($data->sheets[1]['cells'][1][$j]) == "������") $ReceiverName = $j;			// ������
			else if(trim($data->sheets[1]['cells'][1][$j]) == "�����ȣ") $ReceiverPostcode = $j;	     //������ �����ȣ
			else if(trim($data->sheets[1]['cells'][1][$j]) == "������ ���θ��ּ�") $ReceiverAddress = $j;  //������ ���θ��ּ�
			else if(trim($data->sheets[1]['cells'][1][$j]) == "�������ȣ") $SenderOldPostcode = $ReceiverOldPostCode = $j;   // (��)�����ȣ
			else if(trim($data->sheets[1]['cells'][1][$j]) == "������ �����ּ�") $SenderOldAddress = $ReceiverOldAddress = $j;// �����������ּ�
			else if(trim($data->sheets[1]['cells'][1][$j]) == "��������ȭ��ȣ") $ReceiverPhone = $j;      // ��������ȭ��ȣ
			else if(trim($data->sheets[1]['cells'][1][$j]) == "�������޴�����ȣ") $ReceiverHPhone = $j;    // �������޴�����ȣ
		}

		for ($i = 2; $i <= $data->sheets[1]['numRows']; $i++) {
			$A	= SetStringToDB(trim($data->sheets[1]['cells'][$i][$RN]));					
			$B	= SetStringToDB(trim($data->sheets[1]['cells'][$i][$Dnumber]));				
			$C	= SetStringToDB(trim($data->sheets[1]['cells'][$i][$SenderName]));			
			$D	= SetStringToDB(trim($data->sheets[1]['cells'][$i][$SenderOldPostcode]));   
			$E	= SetStringToDB(trim($data->sheets[1]['cells'][$i][$SenderOldAddress]));    
			$F	= SetStringToDB(trim($data->sheets[1]['cells'][$i][$SenderPhone]));         
			$G	= SetStringToDB(trim($data->sheets[1]['cells'][$i][$SenderHPhone]));        
			$H	= SetStringToDB(trim($data->sheets[1]['cells'][$i][$ReceiverName]));        
			$I	= SetStringToDB(trim($data->sheets[1]['cells'][$i][$ReceiverPostcode]));  
			$J	= SetStringToDB(trim($data->sheets[1]['cells'][$i][$ReceiverAddress]));
			$K	= SetStringToDB(trim($data->sheets[1]['cells'][$i][$ReceiverOldPostCode])); 
			$L	= SetStringToDB(trim($data->sheets[1]['cells'][$i][$ReceiverOldAddress]));  
			$M	= SetStringToDB(trim($data->sheets[1]['cells'][$i][$ReceiverPhone]));       
			$N	= SetStringToDB(trim($data->sheets[1]['cells'][$i][$ReceiverHPhone]));     

			//echo "����".$A."//��۹�ȣ".$B."//�ֹ���".$C."//�����»�������ȣ".$D."//�����»���ּ�".$E."//�ֹ�����ȭ��ȣ".$F."//�ֹ����޴�����ȣ".$G."//������".$H."//������ �����ȣ".$I."//������ ���θ��ּ�".$J."//(��)�����ȣ".$K."//�����������ּ�".$L."//��������ȭ��ȣ".$M."//�������޴�����ȣ".$N."<br/>";
			
			if($A <> '')
				$temp_result = insertOrderEmart_Output($conn, $file_nm, $A, $B, $C, $D, $E, $F, $G, $H, $I, $J, $K, $L, $M, $N, $s_adm_no);
			
		}
		$con_cp_no = '3';
		insertTempOrderEmart2Temp($conn, $con_cp_no, $file_nm, $s_adm_no);
		
		/*
		$temp_file = $savedir1."/".$file_nm;						
		$exist = file_exists($temp_file);

		if($exist){
			$delrst=unlink($temp_file);
			if(!$delrst) {
				echo "��������";
			}
		}
		*/
?>	
<script language="javascript">
		location.href =  'order_write_file_emart.php?mode=L&temp_no=<?=$file_nm?>&this_date=<?=$this_date?>&exception_cnt=<?=$not_normal_outstock_cnt?>';
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
		$args_cp_no = '3';
		$arr_rs = listTempOrderForMart($conn, $temp_no, $args_cp_no);
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
<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script>
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
#temp_scroll { z-index: 1; background-color:#f7f7f7; overflow: auto; width: 95%; height:100%; border:1px solid #d1d1d1;}
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
		frm.action = "order_write_file_emart.php";
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

	function js_view(rn, file_nm, order_no) {
		
		var url = "order_modify.php?mode=S&temp_no="+file_nm+"&order_no="+order_no;
		NewWindow(url, '�ֹ��뷮�Է�', '860', '513', 'YES');
		
	}

	function js_reload() {
		location.href =  'order_write_file_emart.php?mode=L&temp_no=<?=$temp_no?>';
	}

	function js_chk_island() {

		if(document.frm.has_island.checked)
			location.href =  'order_write_file.php?mode=L&temp_no=<?=$temp_no?>&has_island=true';
		else
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
				<h2>�ֹ� ��� - �̸�Ʈ</h2>
				<table cellpadding="0" cellspacing="0" class="colstable">

					<colgroup>
						<col width="12%">
						<col width="38%">
						<col width="12%">
						<col width="38%">
					</colgroup>
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
					* ��ü <?=totalCntTempOrderEmart($conn, $temp_no)?> �ֹ��Ǽ� �� &nbsp;&nbsp;
					* �ֹ���ȣ�� <?=sizeof($arr_rs)?> �� &nbsp;&nbsp;
					<? if ($insert_result) { ?>
					* ��ϰ� <?=$row_cnt?> ��
					<? } ?>
					&nbsp;&nbsp;
					<? if ($exception_cnt != 0) { ?>
					* �Ϲ����� <?=$exception_cnt?> ��
					<? } ?>
				</div>
				<div id="temp_scroll">
				<table cellpadding="0" cellspacing="0" class="rowstable01" style="width:2630px">
					<colgroup>
						<col width="35">
						<col width="150">
						<col width="100">
						<col width="520">
						<col width="120">
						<col width="120">
						<col width="120">
						<col width="120">
						<col width="120">
						<col width="120">
						<col width="120">
						<col width="80">
						<col width="500">
						<col width="400">
						
					</colgroup>
					<thead>
						<tr>
							<th>&nbsp;</th>
							<th>���</th>
							<th>��ü�ֹ���ȣ</th>
							<th>
								�ֹ� ��ǰ<br>
								<table cellpadding="0" cellspacing="0" class="rowstable04" style="width:98%">
									<colgroup>
										<col width="12%">
										<col width="18%">
										<col width="45%">
										<col width="20%">
										<col width="10%">
									</colgroup>
									<thead>
										<tr>
											<th>��ǰ��ȣ</th>
											<th>��Ʈ��ǰ��ȣ</th>
											<th>��ǰ��</th>
											<th>�Ӽ�</th>
											<th class="end">����</th>
										</tr>
									</thead>
								</table>
							</th>
							<th>��ü��</th>
							<th>�ֹ���</th>
							<th>����ó</th>
							<th>�޴���ȭ��ȣ</th>
							<th>������</th>
							<th>����ó</th>
							<th>�޴���ȭ��ȣ</th>
							<th>�����ȣ</th>
							<th>�ּ�</th>
							<th class="end">�ֹ��ڸ޸�</th>
						</tr>
					</thead>
					<tbody>
					<?
						$nCnt = 0;
						
						if (sizeof($arr_rs) > 0) {
							
							for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
								
								$rn						= trim($arr_rs[$j]["rn"]);
								$CP_NO					= trim($arr_rs[$j]["CP_NO"]);
								$ORDER_NO				= trim($arr_rs[$j]["ORDER_NO"]);
								$O_NAME					= SetStringFromDB($arr_rs[$j]["O_NAME"]);
								$O_PHONE				= SetStringFromDB($arr_rs[$j]["O_PHONE"]);
								$O_HPHONE				= SetStringFromDB($arr_rs[$j]["O_HPHONE"]);
								$R_NAME					= SetStringFromDB($arr_rs[$j]["R_NAME"]);
								$R_PHONE				= SetStringFromDB($arr_rs[$j]["R_PHONE"]);
								$R_HPHONE				= SetStringFromDB($arr_rs[$j]["R_HPHONE"]);
								$R_ZIPCODE			    = SetStringFromDB($arr_rs[$j]["R_ZIPCODE"]);
								$R_ADDR1				= SetStringFromDB($arr_rs[$j]["R_ADDR1"]);
								$MEMO					= trim($arr_rs[$j]["MEMO"]);
								$DELIVERY				= trim($arr_rs[$j]["DELIVERY"]);
								$SA_DELIVERY		    = trim($arr_rs[$j]["SA_DELIVERY"]);
								$CP_ORDER_NO		    = trim($arr_rs[$j]["CP_ORDER_NO"]);
								$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
								$REG_DATE				= date("Y-m-d",strtotime($REG_DATE));

								// ������ ��ȿ�� �˻�
								$err_str = "����";
								$warning_str = "";

								if ($CP_NO == "") {
									$err_str .=  "�Ǹž�ü ����,";
								} else {
									if (getCompayChk($conn, "�Ǹ�", $s_adm_cp_type, $CP_NO) == "") {
										$err_str .=  "�Ǹž�ü ����,";
									}
								}


								$arr_rs_temp_goods = selectTempOrderGoods($conn, $temp_no, $ORDER_NO);
								if (sizeof($arr_rs_temp_goods) > 0) {
									for ($k = 0 ; $k < sizeof($arr_rs_temp_goods); $k++) {

										$GOODS_NO			= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_NO"]);
										$GOODS_MART_CODE	= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_MART_CODE"]);
										$GOODS_NAME			= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_NAME"]);
										$GOODS_PRICE		= trim($arr_rs_temp_goods[$k]["GOODS_PRICE"]);
										$QTY				= trim($arr_rs_temp_goods[$k]["QTY"]);
										$GOODS_OPTION_NM	= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_OPTION_NM"]);

										/*
										if(chkDuplicateOrder($db, $CP_ORDER_NO, $GOODS_NAME, $GOODS_OPTION_NM, $QTY, $O_NAME, $R_NAME, $R_ADDR1, $CP_NO, $this_date) > 0)
											$err_str .=  "���� ��ϵ� ���� �ֹ� ����,";
										*/

										// ��ǰ������ �˻��ؼ� $GOODS_NO ���ϱ�
										
										if ($GOODS_NO == "��Ͽ��") {

											$GOODS_NAME = SetStringToDB($GOODS_NAME);
											$GOODS_OPTION_NM = SetStringToDB($GOODS_OPTION_NM);

											$GOODS_NO = tryGoodNoFromMartData($conn, $GOODS_NAME, $GOODS_OPTION_NM, $GOODS_MART_CODE);
										}

										if ($GOODS_NO == "��Ͽ��" || $GOODS_NO == "������ǰ����") {
											$err_str .=  "��ǰ��ȣ ����,";
										}
										else
										{
											updateTempOrderGoodsNo($conn, $ORDER_NO, $ORDER_SEQ, $GOODS_NO, $temp_no);
											$arr_rs_temp_goods[$k]["GOODS_NO"] = $GOODS_NO;
										}
										

										
										if (chkCompanyOrderNo($conn, $CP_ORDER_NO, $GOODS_NO) > 0) {
											//$warning_str = "��Ʈ �ֹ���ȣ ���� ";
											$err_str .= "��Ʈ �ֹ���ȣ ����, ";
										}

										/*
										$arr_rs_goods = selectGoods($conn, $GOODS_NO);

										// ��ǰ ��ȣ�� �ش��ϴ� �ֹ� ��ǰ �ӽ� ��� ��� �ϱ�
										// ��Ʈ����, ��Ʈ �ֹ���ȣ, ��ǰ��ȣ, ����
										//$fake_result = insertFakeStock($conn, $this_date, $CP_NO, $CP_ORDER_NO, $GOODS_NO, $QTY);

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
												$new_price = getCompanyGoodsPrice($conn, $GOODS_NO, $CP_NO );

											if ($new_price <> 0)
												$rs_sale_price = $new_price;
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
										*/
										

										if ($QTY == "") {
											$err_str .=  "���� ����,";
										} else {
											if ($QTY  < "1") {
												//$err_str .=  "���� ����,";
											}
										}
										

										
										if ($R_NAME == "") {
											$err_str .=  "������ ����,";
										}

										if ($R_HPHONE == "") {
											$err_str .=  "�޴���ȭ��ȣ ����,";
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

										if ($R_ADDR1 == "") {
											$err_str .=  "�ּ� ����,";
										}
									}
								}
								
								/*								
								if ($R_ADDR1 == "") {
									$R_ADDR1 = "���� ���� ��� �ֹ�";
								}
								*/


								if ($err_str <> "����") {
									$err_str = str_replace("����","",$err_str);
									$err_str = substr($err_str, 0, (strlen($err_str) -1));
									$err_str = str_replace(",","<div class='sp5'></div>",$err_str);
									$err_str = "<font color='red'>".$err_str."</font>";
								}

								if($warning_str <> ""){ 
									$warning_str = "<font color='blue'>".$warning_str."</font>";
								}
					?>
						<tr>
							<td class="filedown">
								<input type="checkbox" name="chk[]" value="<?=$ORDER_NO?>">
							</td>
							<td class="modeual_nm" style="padding:8px 0 3px 0">
								<a href="javascript:js_view('<?= $rn ?>','<?= $temp_no ?>','<?= $ORDER_NO ?>');"><?=$err_str?></a>
								<? if ($err_str == "����") {?>
								<input type="hidden" name="ok[]" value="<?=$ORDER_NO?>">
								<? } ?>
								<br/>
								<?= $warning_str?>
							</td>
							<td><?= $CP_ORDER_NO?></td>
							<td>

								<table cellpadding="0" cellspacing="0" class="rowstable04"  style="width:98%">
									<colgroup>
										<col width="12%">
										<col width="18%">
										<col width="45%">
										<col width="20%">
										<col width="10%">
									</colgroup>
								<?
									if (sizeof($arr_rs_temp_goods) > 0) {
										for ($k = 0 ; $k < sizeof($arr_rs_temp_goods); $k++) {

											$GOODS_NO			= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_NO"]);
											$GOODS_MART_CODE	= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_MART_CODE"]);
											$GOODS_NAME			= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_NAME"]);
											$GOODS_PRICE		= trim($arr_rs_temp_goods[$k]["GOODS_PRICE"]);
											$QTY				= trim($arr_rs_temp_goods[$k]["QTY"]);
											$GOODS_OPTION_NM	= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_OPTION_NM"]);
											?>
											<tr>
												<td><a href="javascript:js_goods_view('<?= $GOODS_NO?>')"><?= $GOODS_NO?></a></td>
												<td><?= $GOODS_MART_CODE?></td>
												<td class="modeual_nm"><?= $GOODS_NAME?></td>
												<td class="modeual_nm"><?= $GOODS_OPTION_NM?></td>
												<td><?= number_format($QTY)?></td>
											</tr>
											<?
										}
									}
								?>
								</table>
							</td>
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
							
						</tr>
					<?			
										$warning_str = "";
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
					<input type="button" name="aa" value=" ��ǰ��Ͽ�� ����Ʈ " class="btntxt" onclick="js_unregistered_goods_excel();">&nbsp;&nbsp;&nbsp;&nbsp; 
					<input type="button" name="aa2" value=" �̵���ڷ� �����ޱ� " class="btntxt" onclick="js_excel();">&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="button" name="bb" value=" �����ڷ� ��� " class="btntxt" onclick="js_register();">&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="button" name="cc" value=" �����ڷ� ���� " class="btntxt" onclick="js_delete();">
				</div>

			</div>

			<?
				// ���� �ֹ� ��ǰ ���� ����Ʈ
				$order_goods_list = listTempOrderCnt($conn, $temp_no, $has_island);
			?>
			<div class="text_frame">* ���� �ֹ� ��ǰ ���� ����Ʈ &nbsp; <a href="javascript:js_temp_goods_excel();"><img src="../images/common/btn/btn_excel.gif" alt="�ֹ� ��ǰ ���� ����Ʈ"></a>
			<div class="float_right"><label><input type="checkbox" onclick="js_chk_island()" name="has_island" <?=($has_island ? "checked='checked'" : "")?> value="true"/> ���ֵ� �� �����갣 ����</label></div>
			</div>
			<table cellpadding="0" cellspacing="0" class="rowstable">

				<colgroup>
					<col width="120px" />
					<col width="120px" />
					<col width="120px" />
					<col width="*" />
					<col width="120px" />
					<col width="120px" />
					<col width="120px" />
				</colgroup>
				<thead>
					<tr>
						<th>��ǰ����</th>
						<th>��ǰ�ڵ�</th>
						<th>�������ڵ�</th>
						<th>��ǰ��</th>
						<th>���</th>
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
							$CATE_NAME		= trim($order_goods_list[$j]["CATE_NAME"]);
							
							//$CATE_02		= trim($order_goods_list[$j]["CATE_02"]);
							//$CATE_02 = getDcodeName($conn, "GOODS_SUB_CATE", $CATE_02);
							$GOODS_CODE		= trim($order_goods_list[$j]["GOODS_CODE"]);
							$KANCODE		= trim($order_goods_list[$j]["KANCODE"]);
							$GOODS_NAME		= SetStringFromDB($order_goods_list[$j]["GOODS_NAME"]);
							$DELIVERY_CNT_IN_BOX = trim($order_goods_list[$j]["DELIVERY_CNT_IN_BOX"]);
							$CNT		    = trim($order_goods_list[$j]["CNT"]);
							$STOCK_CNT			 = trim($order_goods_list[$j]["STOCK_CNT"]);
							$BSTOCK_CNT			 = trim($order_goods_list[$j]["BSTOCK_CNT"]);
				?>
					<tr>
						<td height="24px"><?=$CATE_NAME?></td>
						<td><?=$GOODS_CODE?></td>
						<td><?=$KANCODE?></td>
						<td class="pname" style="text-align:left;padding-left:5px;"><?=$GOODS_NAME?></td>
						<td style="text-align:right;padding-right:5px;"><?=number_format($STOCK_CNT)?></td>
						<td><?=$DELIVERY_CNT_IN_BOX?></td>
						<td style="text-align:right;padding-right:5px;"><?=number_format($CNT)?></td>
					</tr>
				<?
						}
					}else {
				?>
					<tr>
						<td colspan="7" height="30">�����Ͱ� �����ϴ�</td>
					</tr>
				<?
					}
				?>
				</tbody>
			</table>
			<br/>
			<span>(����, �︪�� �����갣������ �ջ꿡�� ���ܵ˴ϴ�.)</span>
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