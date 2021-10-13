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
	$menu_right = "OD016"; // �޴����� ���� �� �־�� �մϴ�

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


#====================================================================
# Request Parameter
#====================================================================

	$excelFlag=0;

	function listOrderGoodsDeliveryDeleted($db, $deliverySeq, $deliveryCp){
		$cnt=0;
		$record=array();
		$query=" SELECT RESERVE_NO, DELIVERY_SEQ, RECEIVER_NM, RECEIVER_HPHONE, ORDER_NM, DELIVERY_CP
					FROM TBL_ORDER_GOODS_DELIVERY
					WHERE DELIVERY_SEQ ='".$deliverySeq."' 
					AND DELIVERY_CP='".$deliveryCp."'
					AND (DEL_TF='Y' OR USE_TF='N') ";
		$result = mysql_query($query, $db);
		if($result<>""){
			$cnt=mysql_num_rows($result);
			if($cnt>0){
				for($i=0;$i<$cnt;$i++){
					$record[$i]=mysql_fetch_assoc($result);
				}
				return $record;
			}
			else{
				return null;
			}
		}
	}

	if ($mode == "FU") {
		#====================================================================
			$savedir1 = $g_physical_path."upload_data/temp_delivery_seq";
		#====================================================================

			$file_nm	= upload($_FILES[file_nm], $savedir1, 10000 , array('xls'));

			require_once '../../_excel_reader/Excel/reader.php';
			$data = new Spreadsheet_Excel_Reader();
			$data->setOutputEncoding('euc-kr');
			//$data->read('test.xls');
			
			$data->read("../../upload_data/temp_delivery_seq/".$file_nm);

			// echo "data : $data<br>";
		
			error_reporting(E_ALL ^ E_NOTICE);

			// echo "2<br>";

			for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {

				if($delivery_cp == "�Ե��ù�")
				{
					$TEMP_DELIVERY_NO	= SetStringToDB(trim($data->sheets[0]['cells'][$i][7])); //������ȣ
					$TEMP_DELIVERY_SEQ	= SetStringToDB(trim($data->sheets[0]['cells'][$i][10])); //�ֹ���ȣ
				} 
				else if ($delivery_cp == "CJ�������")
				{
					$TEMP_DELIVERY_NO	= SetStringToDB(trim($data->sheets[0]['cells'][$i][8])); //������ȣ
					$TEMP_DELIVERY_SEQ	= SetStringToDB(trim($data->sheets[0]['cells'][$i][30])); //��Ÿ1

					$TEMP_DELIVERY_NO = str_replace("-", "", $TEMP_DELIVERY_NO);

				}
				if($TEMP_DELIVERY_SEQ <> "" && $TEMP_DELIVERY_NO <> ""){
					$arr_rs=listOrderGoodsDeliveryDeleted($conn,$TEMP_DELIVERY_SEQ,$delivery_cp);
					if(sizeof($arr_rs)>0){
						$excelFlag=1;
					}
					updateOrderGoodsDeliveryNumber($conn, $TEMP_DELIVERY_SEQ, $delivery_cp,$TEMP_DELIVERY_NO);
				}
					
			}
?>	
<?
// function inspectDeliverySeq($db, $deliverySeq, $deliveryCp, $deliveryNo){

// }
// function updateOrderGoodsDeliveryNumber1($db, $delivery_seq, $delivery_cp, $delivery_no) {

// 	$query = "UPDATE TBL_ORDER_GOODS_DELIVERY 
// 			  SET 
// 				  DELIVERY_NO	= '$delivery_no' ";
	
// 	$query .= " WHERE DELIVERY_SEQ		= '$delivery_seq' AND DELIVERY_CP	= '$delivery_cp' AND MATCHING_TF ='' ";
	
// 	//echo $query."<br/>";
// 	//exit;

// 	if(!mysql_query($query,$db)) {
// 		return false;
// 		echo "<script>alert(\"[1]������ �߻��Ͽ����ϴ� - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
// 		exit;
// 	} else {
// 		return true;
// 	}
// }

?>
<script language="javascript">
		alert('���� �Ϸ����� �Է¿Ϸ� �Ǿ����ϴ�.');
</script>
<?
	}

/* //CJ��������� ���ϿϷᰡ ����

	if ($mode == "FU2") {
		#====================================================================
			$savedir2 = $g_physical_path."upload_data/temp_delivery_pickup";
		#====================================================================

			$file_nm2	= upload($_FILES[file_nm2], $savedir2, 10000 , array('xls'));

			require_once '../../_excel_reader/Excel/reader.php';
			$data = new Spreadsheet_Excel_Reader();
			$data->setOutputEncoding('euc-kr');
			//$data->read('test.xls');
			$data->read("../../upload_data/temp_delivery_pickup/".$file_nm2);
		
			error_reporting(E_ALL ^ E_NOTICE);

			 
			for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {

				$TEMP_DELIVERY_NO	= SetStringToDB(trim($data->sheets[0]['cells'][$i][6]));
				$TEMP_DELIVERY_SEQ	= SetStringToDB(trim($data->sheets[0]['cells'][$i][3]));
				$TEMP_SENT_DATE	    = SetStringToDB(trim($data->sheets[0]['cells'][$i][2]));
				
				if($TEMP_DELIVERY_SEQ <> "" && $TEMP_DELIVERY_NO <> "") {

					$sent_date =  substr($TEMP_SENT_DATE, 0, 4)."-".substr($TEMP_SENT_DATE, 4, 2)."-".substr($TEMP_SENT_DATE, 6, 2);
					updateOrderGoodsDeliveryNumberComplete($conn, $TEMP_DELIVERY_SEQ, $TEMP_DELIVERY_NO, $sent_date);
				}
			}
?>	
<script language="javascript">
		alert('���� �Ϸ����� �Է¿Ϸ� �Ǿ����ϴ�.');
</script>
<?
	}
*/



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
	$(document).ready(function(){
		
		var excelFlag=$('input[name=excelFlag]').val();
		if(excelFlag==1){
			$('#dvShowList').show();
			js_excel()
		}
		else{
			$('#dvShowList').hide();

		}
	});
	function js_excel(){
		var frmExcel=document.frmExcel;
		frmExcel.action="pop_delivery_paper_excel_deleted.php";
		frmExcel.method="POST";
		frmExcel.submit();

	}

	function js_delivery_paper_loading() { 		//������

		var frm = document.frm;
		
		if(frm.delivery_cp.value == "")
		{
			alert("������ ����� �ù�縦 �������ּ���");
			return;
		}

		frm.target = "";
		frm.mode.value = "excel";
		frm.action = "pop_delivery_paper_loading_excel.php";
		frm.submit();

	}

	function js_save_seq(btn) {		//������

		var frm = document.frm;

		if(frm.delivery_cp.value == "")
		{
			alert("������ ����� �ù�縦 �������ּ���");
			return;
		}

		btn.style.visibility = 'hidden';
		
		var frm = document.frm;

		if (isNull(frm.file_nm.value)) {
			alert('������ ������ �ּ���.');
			btn.style.visibility = 'visible';
			frm.file_nm.focus();
			return ;		
		}
		
		if (!AllowAttach(frm.file_nm))
		{
			btn.style.visibility = 'visible';
			frm.file_nm.focus();
			return ;
		}

		frm.mode.value = "FU";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

/*
	function js_save_pickup(btn) {

		btn.style.visibility = 'hidden';
		
		var frm = document.frm;

		if (isNull(frm.file_nm2.value)) {
			alert('������ ������ �ּ���.');
			btn.style.visibility = 'visible';
			frm.file_nm2.focus();
			return ;		
		}
		
		if (!AllowAttach(frm.file_nm2))
		{
			btn.style.visibility = 'visible';
			frm.file_nm2.focus();
			return ;
		}

		frm.mode.value = "FU2";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}
*/

	function AllowAttach(obj) { //������
		var file = obj.value;
		extArray = new Array(".xls");
		allowSubmit = false;
		
		if (!file){
			//form1.submit();
		}

		while (file.indexOf("\\") != -1){
			file = file.slice(file.indexOf("\\") + 1);
			ext = file.slice(file.lastIndexOf(".")).toLowerCase();

			for (var i = 0; i < extArray.length; i++){
				if (extArray[i] == ext){ 
					allowSubmit = true; 
					break; 
				}
			}
		}

		if (allowSubmit){
			return true;
		}else{
			alert("�Է��Ͻ� ������ ���ε� �� �� �����ϴ�!");
			return false;
		}
	}

</script>
<script>
    window.onunload = refreshParent;
    function refreshParent() {
        window.opener.js_search();
    }
</script>
</head>

<body id="popup_order_wide">

<div id="popupwrap_order_wide">
	<h1>���� ��ȣ ��� / ������Ʈ</h1>
	<div id="postsch_code">

		<div class="addr_inp">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="">

	<table cellpadding="0" cellspacing="0" wi<dth="100%" class="colstable02">

	<colgroup>
		<col width="15%" />
		<col width="35%" />
		<col width="15%" />
		<col width="35%" />
	</colgroup>
	<tr>
		<th>�ù� ȸ��/��� ����</th>
		<td class="line">
			<?= makeSelectBox($conn,"DELIVERY_CP","delivery_cp","105","��ü","", "")?>
			<?= makeSelectBoxAsName($conn,"DELIVERY_FEE", "delivery_fee","80px", "���Ӽ���", "", "", "")?>
		</td>
		<th>����ε����� �ٿ�</th>
		<td class="line">
			<a href="javascript:js_delivery_paper_loading();"><img src="../images/common/btn/btn_excel.gif" alt="���� ����Ʈ" /></a>
		</td>
	</tr>
	<tr>
		<th>���� �Ϸ����� �Է�</th>
		<td class="line" colspan="3">
			<input type="file" name="file_nm" style="width:60%;" class="txt">
			<a href="#" onclick="js_save_seq(this); return false;"><img src="../images/admin/btn_confirm.gif" alt="Ȯ��"></a>
		</td>
	</tr>
	<!--
	<tr>
		<th>���� �Ϸ����� �Է�</th>
		<td class="line" colspan="3">
			<input type="file" name="file_nm2" style="width:60%;" class="txt">
			<a href="#" onclick="js_save_pickup(this); return false;"><img src="../images/admin/btn_confirm.gif" alt="Ȯ��"></a>
		</td>
	</tr>
	-->
	</table>
	
	<!--div class="btn">
	  <a href="javascript:js_update();"><img src="../images/admin/btn_modify.gif" alt="Ȯ��" /></a>
	  <a href="javascript:window.close();"><img src="../images/admin/btn_cancel.gif" alt="���" /></a>
	</div -->      

	


<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>

<div class="bot_close" style="width:auto;"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="�ݱ�" /></a></div>
</div>
<div id="dvShowList">
	<table>
		<tr>
			<td>�ֹ���ȣ</td>
			<td>����</td>
			<td>������</td>
			<td>�ַ��� ����ó</td>
			<td>�ֹ���</td>
			<td>�ù�ȸ��</td>
		</tr>
	<?
		if($excelFlag>0){
			for($i=0;$i<sizeof($arr_rs);$i++){
				//RESERVE_NO, DELIVERY_SEQ, RECEIVER_NM, RECEIVER_HPHONE, ORDER_NM, DELIVERY_CP
				$RESERVE_NO=				$arr_rs[$i]['RESERVE_NO'];
				$DELIVERY_SEQ=				$arr_rs[$i]['DELIVERY_SEQ'];
				$RECEIVER_NM=			SetStringFromDB($arr_rs[$i]['RECEIVER_NM']); 
				$RECEIVER_HPHONE=			$arr_rs[$i]['RECEIVER_HPHONE'];
				$ORDER_NM=				SetStringFromDB($arr_rs[$i]['ORDER_NM']);
				$DELIVERY_CP=			SetStringFromDB($arr_rs[$i]['DELIVERY_CP']);

	?>
		<tr>
			<td><?=$RESERVE_NO?></td>
			<td><?=$DELIVERY_SEQ?></td>
			<td><?=$RECEIVER_NM?></td>
			<td><?=$RECEIVER_HPHONE?></td>
			<td><?=$ORDER_NM?></td>
			<td><?=$DELIVERY_CP?></td>
		</tr>


	<?
			}
		}
	?>
	</table>
</div>
<form name="frmExcel">
	<input type="hidden" name="excelFlag" value="<?=$excelFlag?>">
	<input type="hidden" name="TEMP_DELIVERY_SEQ" value="<?=$TEMP_DELIVERY_SEQ?>">
	<input type="hidden" name="delivery_cp" value="<?$delivery_cp?>">
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>