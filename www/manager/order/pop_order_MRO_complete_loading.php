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
	$menu_right = "SP009"; // �޴����� ���� �� �־�� �մϴ�

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
	require "../../_classes/biz/member/member.php";
	require "../../_classes/biz/payment/payment.php";
	require "../../_classes/biz/board/board.php";

	$mode	= trim($mode);
	
	if ($this_date == "") 
		$this_date = date("Y-m-d",strtotime("0 month"));

	if ($mode == "FR") {
		
		#====================================================================
			$savedir1 = $g_physical_path."upload_data/temp_order";
		#====================================================================

		$file_nm	= upload($_FILES[file_nm], $savedir1, 10000 , array('xls','xlsx'));
		//echo "<script>console.log('file_nm is :".$file_nm."');</script>";
		error_reporting(E_ALL ^ E_NOTICE);

		require_once "../../_PHPExcel/Classes/PHPExcel.php";
		$objPHPExcel = new PHPExcel();
		require_once "../../_PHPExcel/Classes/PHPExcel/IOFactory.php";
		$filename = '../../upload_data/temp_order/'.$file_nm; 

		$temp_no = $file_nm;
		echo "<script>console.log('tmp_no is :".$temp_no."');</script>";
		
		try {
			
			$objReader = PHPExcel_IOFactory::createReaderForFile($filename);
			$objReader->setReadDataOnly(true);
			$objExcel = $objReader->load($filename);
			$objExcel->setActiveSheetIndex(0);
			$objWorksheet = $objExcel->getActiveSheet();

			$rowIterator = $objWorksheet->getRowIterator();

			foreach ($rowIterator as $row) {
				$cellIterator = $row->getCellIterator();
				$cellIterator->setIterateOnlyExistingCells(false); 
			}

			$maxRow = $objWorksheet->getHighestRow();


			for ($i = 2 ; $i <= $maxRow ; $i++) {

				//2017-12-16 MRO �ý��� ����, 2018�� 1���� ����
				$order_date			= $objWorksheet->getCell('A'.$i)->getValue();	//�ֹ�����
				$order_no			= $objWorksheet->getCell('B'.$i)->getValue();	//�ֹ���ȣ
				$seq				= $objWorksheet->getCell('C'.$i)->getValue();	//����
				$seller_goods_code  = $objWorksheet->getCell('D'.$i)->getValue();	//��ǰ�ڵ�
				$box_seq			= $objWorksheet->getCell('E'.$i)->getValue();	//�������
				$box_qty			= $objWorksheet->getCell('F'.$i)->getValue();	//�������
				$sender				= $objWorksheet->getCell('G'.$i)->getValue();	//������ ���
				$receiver			= $objWorksheet->getCell('H'.$i)->getValue();	//�޴� ���

				$order_date			= iconv("UTF-8","EUC-KR",$order_date);
				$order_no			= iconv("UTF-8","EUC-KR",$order_no);
				$seq				= iconv("UTF-8","EUC-KR",$seq);
				$seller_goods_code	= iconv("UTF-8","EUC-KR",$seller_goods_code);
				$box_seq			= iconv("UTF-8","EUC-KR",$box_seq);
				$box_qty			= iconv("UTF-8","EUC-KR",$box_qty);
				$sender				= iconv("UTF-8","EUC-KR",$sender);
				$receiver			= iconv("UTF-8","EUC-KR",$receiver);


				insertTempOrderMROComplete($conn, $temp_no, $order_date, $order_no, $seq, $seller_goods_code, $box_seq, $box_qty, $sender, $receiver, $s_adm_no);

			}

		} catch (exception $e) {
			echo '���������� �дµ��� ������ �߻��Ͽ����ϴ�.';
		}
		
	}	

	$arr_rs = listTempOrderMROComplete($conn, $temp_no);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script>
// ���� ��ư Ŭ�� �� 
	function js_save() {
		
		var frm = document.frm;

		if (isNull(frm.file_nm.value)) {
			alert('������ ������ �ּ���.');
			frm.file_nm.focus();
			return ;		
		}
		
		AllowAttach(frm.file_nm);

		frm.mode.value = "FR";

		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
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

	function js_temp_goods_excel() {
		
		var frm = document.frm;

		frm.target = "";
		frm.action = "pop_order_MRO_complete_loading_excel_list.php";
		frm.submit();

	}

</script>
</head>

<body id="popup_order_wide">

<div id="popupwrap_order_wide">
	<h1>MRO �Ϸ� �ε� - ������� �Է�</h1>
	<div id="postsch_code">

		<div class="addr_inp">
		

<form name="frm" method="post" enctype="multipart/form-data">

			<input type="hidden" name="mode" value="">
			<input type="hidden" name="temp_no" value="<?=$temp_no?>">

			<div class="sp10"></div>
			<table cellpadding="0" cellspacing="0" width="100%" class="colstable02">
				<colgroup>
					<col width="10%">
					<col width="35%">
					<col width="10%">
					<col width="35%">
					<col width="10%">
				</colgroup>
				<tr>
					<th>����</th>
					<td class="line"><input type="file" name="file_nm" style="width:60%;" class="txt"></td>
					<td class="line">
					<? if ($file_nm <> "" ) {?>
						<? if ($sPageRight_U == "Y") {?>
						<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="Ȯ��" /></a>
						<? } ?>
					<? } else {?>
						<? if ($sPageRight_I == "Y") {?>
						<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="Ȯ��" /></a>
						<? } ?>
					<? }?>
					</td>
				</tr>
			</table>
			<div class="sp15"></div>
			<?
				if(sizeof($arr_rs) > 0) {
			
			?>
			
			<table cellpadding="0" cellspacing="0" width="100%" class="colstable02">
				<colgroup>
					<col width="10%">
					<col width="*">
				</colgroup>
				<tr>
					<th>��ȯ�� ���� �ٿ�ε�</th>
					<td class="line"><a href="javascript:js_temp_goods_excel();"><img src="../images/common/btn/btn_excel.gif" alt="���� ����Ʈ" /></a></td>
				</tr>
			</table>
			<? } ?>
			<div class="sp15"></div>

			<table cellpadding="0" cellspacing="0" class="rowstable01">
				<colgroup>
					<col width="100">
					<col width="100">
					<col width="100">
					<col width="100">
					<col width="100">
					<col width="100">
					<col width="100">
					<col width="100">
					<col width="100">
					<col width="100">
					<!--<col width="100">-->
				</colgroup>
				<thead>
					<tr>
						<th>�ֹ�����</th>
						<th>�ֹ���ȣ</th>
						<th>����</th>
						<th>��ǰ�ڵ�</th>
						<th>�������</th>
						<th>�������</th>
						<th>������ ���</th>
						<th>�޴� ���</th>
						<th>�ù���ڵ�</th>
						<th>�����ȣ</th>
						<!--
						<th>��Ÿ</th>
						-->
					</tr>
				</thead>
				<tbody>
				<?
					if (sizeof($arr_rs) > 0) {
						
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
						
							// ORDER_DATE, ORDER_NO, SEQ, BOXCODE, SENDER, RECEIVER, DELIVERY_CODE, DELIVERY_NO, ETC
							$ORDER_DATE				= SetStringFromDB($arr_rs[$j]["ORDER_DATE"]);
							$ORDER_NO				= SetStringFromDB($arr_rs[$j]["ORDER_NO"]);
							$SEQ					= SetStringFromDB($arr_rs[$j]["SEQ"]);
							$SELLER_GOODS_CODE		= SetStringFromDB($arr_rs[$j]["SELLER_GOODS_CODE"]);
							$BOX_SEQ				= SetStringFromDB($arr_rs[$j]["BOX_SEQ"]);
							$BOX_QTY				= SetStringFromDB($arr_rs[$j]["BOX_QTY"]);
							$SENDER					= SetStringFromDB($arr_rs[$j]["SENDER"]);
							$RECEIVER				= SetStringFromDB($arr_rs[$j]["RECEIVER"]);
							$DELIVERY_CODE			= SetStringFromDB($arr_rs[$j]["DELIVERY_CODE"]);
							$DELIVERY_NO			= SetStringFromDB($arr_rs[$j]["DELIVERY_NO"]);
							$ETC					= SetStringFromDB($arr_rs[$j]["ETC"]);
					?>

					<tr height="25">
						<td><?=$ORDER_DATE?></td>
						<td><?=$ORDER_NO?></td>
						<td><?=$SEQ?></td>
						<td><?=$SELLER_GOODS_CODE?></td>
						<td><?=$BOX_SEQ?></td>
						<td><?=$BOX_QTY?></td>
						<td><?=$SENDER?></td>
						<td><?=$RECEIVER?></td>
						<td><?=$DELIVERY_CODE?></td>
						<td><?=$DELIVERY_NO?></td>
						<!--
						<td><?=$ETC?></td>
						-->
					</tr>
				<?
						}
					}
				?>
				</tbody>
			</table>
</form>

		</div>
	</div>
	<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="�ݱ�" /></a></div>
</div>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>