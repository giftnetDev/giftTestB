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
	$menu_right = "CF011"; // �޴����� ���� �� �־�� �մϴ�

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
	require "../../_classes/biz/confirm/confirm.php";
	require "../../_classes/biz/company/company.php";

	$mode	= trim($mode);
	$result = false;
	
	if ($this_date == "") 
		$this_date = date("Y-m-d",strtotime("0 month"));

	if ($mode == "FR") {
		
		#====================================================================
			$savedir1 = $g_physical_path."upload_data/temp_order";
		#====================================================================

		$file_nm	= upload($_FILES[file_nm], $savedir1, 10000 , array('xls','xlsx'));
		error_reporting(E_ALL ^ E_NOTICE);

		require_once "../../_PHPExcel/Classes/PHPExcel.php";
		$objPHPExcel = new PHPExcel();
		require_once "../../_PHPExcel/Classes/PHPExcel/IOFactory.php";
		$filename = '../../upload_data/temp_order/'.$file_nm; 

		$temp_no = $file_nm;
		
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

			//��� ��
			$i = 6;
			
			$k = 1;
			while(iconv("UTF-8","EUC-KR",$objWorksheet->getCell(getNameFromNumber($k).$i)->getValue()) != "") { 

				switch(trim(iconv("UTF-8","EUC-KR",$objWorksheet->getCell(getNameFromNumber($k).$i)->getValue()))) { 
					case "�ۼ�����" : 	 $COL_WRITTEN_DATE = getNameFromNumber($k); break;
					case "���ι�ȣ" : 	 $COL_CF_CODE = getNameFromNumber($k);      break;
					case "�߱�����" : 	 $COL_OUT_DATE = getNameFromNumber($k);     break;
					case "�����ڻ���ڵ�Ϲ�ȣ" :  	 $COL_BIZ_NO1 = getNameFromNumber($k);       break;
					case "���޹޴��ڻ���ڵ�Ϲ�ȣ" :  	 $COL_BIZ_NO2 = getNameFromNumber($k);       break;
					case "��ȣ" : 	 if($COL_CP_NM1 == "")
										$COL_CP_NM1 = getNameFromNumber($k);    
									 else 
										$COL_CP_NM2 = getNameFromNumber($k);    
						break;
					case "�հ�ݾ�" : 	 $COL_TOTAL_PRICE = getNameFromNumber($k);  break;
					case "���ް���" : 	 $COL_SUPPLY_PRICE = getNameFromNumber($k); break;
					case "����" : 	 $COL_SURTAX = getNameFromNumber($k);       break;
					case "ǰ���" : 	 $COL_GOODS_NM = getNameFromNumber($k);     break;
				}

				$k += 1;
			}

			for ($i = 7 ; $i <= $maxRow ; $i++) {

				$written_date     = iconv("UTF-8","EUC-KR",$objWorksheet->getCell($COL_WRITTEN_DATE.$i)->getValue());
				$cf_code	      = iconv("UTF-8","EUC-KR",$objWorksheet->getCell($COL_CF_CODE.$i)->getValue());
				$out_date         = iconv("UTF-8","EUC-KR",$objWorksheet->getCell($COL_OUT_DATE.$i)->getValue());
				$biz_no2		  = iconv("UTF-8","EUC-KR",$objWorksheet->getCell($COL_BIZ_NO2.$i)->getValue());
				$cp_nm1			  = iconv("UTF-8","EUC-KR",$objWorksheet->getCell($COL_CP_NM1.$i)->getValue());
				$cp_nm2			  = iconv("UTF-8","EUC-KR",$objWorksheet->getCell($COL_CP_NM2.$i)->getValue());
				$total_price	  = iconv("UTF-8","EUC-KR",$objWorksheet->getCell($COL_TOTAL_PRICE.$i)->getValue());
				$supply_price	  = iconv("UTF-8","EUC-KR",$objWorksheet->getCell($COL_SUPPLY_PRICE.$i)->getValue());
				if($COL_SURTAX != "")
					$surtax			  = iconv("UTF-8","EUC-KR",$objWorksheet->getCell($COL_SURTAX.$i)->getValue());
				else
					$surtax			  = 0;
				$goods_nm		  = iconv("UTF-8","EUC-KR",$objWorksheet->getCell($COL_GOODS_NM.$i)->getValue());
				$biz_no1		  = iconv("UTF-8","EUC-KR",$objWorksheet->getCell($COL_BIZ_NO1.$i)->getValue());

				$total_price = str_replace(",","", $total_price);
				$supply_price = str_replace(",","", $supply_price);
				$surtax = str_replace(",","", $surtax);

				insertTempCashStatement($conn, $temp_no, $written_date, $cf_code, $out_date, $biz_no1, $cp_nm1, $biz_no2, $cp_nm2, $total_price, $supply_price, $surtax, $goods_nm, $s_adm_no);

			}

?>	
<script language="javascript">
	//	alert('���� ó�� �Ǿ����ϴ�.');
		location.href =  '<?=$_SERVER[PHP_SELF]?>?mode=L&temp_no=<?=$temp_no?>' ;
</script>
<?

		} catch (exception $e) {
			echo '���������� �дµ��� ������ �߻��Ͽ����ϴ�.'.$e;
		}
		
		exit;
	}	

	if ($mode == "I") {

		$row_cnt = count($ok);
		
		$str_cf_code = "";

		for ($k = 0; $k < $row_cnt; $k++) {
			$str_cf_code .= "'".$ok[$k]."',";
		}

		$str_cf_code = substr($str_cf_code, 0, (strlen($str_cf_code) -1));
		//echo $str_cp_no;

		$result = insertTempToRealCashStatement($conn, $temp_no, $str_cf_code);

		syncCashStatementWithCompanyLedger($conn);

?>	
<script language="javascript">
		alert('���� ó�� �Ǿ����ϴ�.');
		//self.close();
		location.href =  '<?=$_SERVER[PHP_SELF]?>?mode=L&temp_no=<?=$temp_no?>' ;
</script>
<?
		exit;

	}

	if ($mode == "D") {

		$row_cnt = count($chk);
		
		for ($k = 0; $k < $row_cnt; $k++) {
		
			$tmp_cf_code = $chk[$k];

			$result = deleteTempCashStatement($conn, $temp_no, $tmp_cf_code);
		}
		
?>	
<script language="javascript">
		alert('���� ó�� �Ǿ����ϴ�.');
		//self.close();
		location.href =  '<?=$_SERVER[PHP_SELF]?>?mode=L&temp_no=<?=$temp_no?>' ;
</script>
<?
		
	}

	if ($mode == "L") {
		$arr_rs = listTempCashStatement($conn, $temp_no);
	}

		

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
<script type="text/javascript" src="../jquery/jquery-ui-timepicker-addon.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui-timepicker-addon.css" type="text/css" />
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<script>
  $(function() {
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
  });
</script>
<script>
// ���� ��ư Ŭ�� �� 
	function js_save() {
		
		var frm = document.frm;

		if (isNull(frm.file_nm.value)) {
			alert('������ ������ �ּ���.');
			frm.file_nm.focus();
			return ;		
		}
		
		//AllowAttach(frm.file_nm);

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
		frm.action = "pop_order_MRO_conversion_excel_list.php";
		frm.submit();

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

	function js_all_check() {
		var frm = document.frm;
		
		if (frm['chk[]'] != null) {
			
			if (frm['chk[]'].length != null) {

				if (frm.all_chk.checked == true) {
					for (i = 0; i < frm['chk[]'].length; i++) {
						frm['chk[]'][i].checked = true;
					}
				} else {
					for (i = 0; i < frm['chk[]'].length; i++) {
						frm['chk[]'][i].checked = false;
					}
				}
			} else {
			
				if (frm.all_chk.checked == true) {
					frm['chk[]'].checked = true;
				} else {
					frm['chk[]'].checked = false;
				}
			}
		}
	}

</script>
<style>
	table.rowstable01 tr.first_tr td { background-color: #ffffff; }
	table.rowstable01 tr.second_tr td { background-color: #f5f5f5;}
	table.rowstable01 tr.end td { border-bottom:2px solid black;}
</style>
</head>

<body id="popup_order_wide">

<div id="popupwrap_order_wide">
	<h1>���� ��꼭 �ϰ� �Է�</h1>
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
					<col width="*">
				</colgroup>
				<tr>
					<th>����</th>
					<td class="line"><input type="file" name="file_nm" style="width:60%;" class="txt"></td>
					<td class="line">
						<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="Ȯ��" /></a>
					</td>
				</tr>
			</table>
			<div class="sp15"></div>
			�� <?=sizeof($arr_rs)?> ��
			<table cellpadding="0" cellspacing="0" class="rowstable01">
				<colgroup>
					<col width="20">
					<col width="100">
					<col width="100">
					<col width="150">
					<col width="100">
					<col width="100">
					<col width="100">
					<col width="200">
					<col width="150">
					<col width="100">
					<col width="100">
					<col width="100">
					<col width="100">
				</colgroup>
				<thead>
					<tr>
						<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
						<th>���</th>
						<th>�ۼ�����</th>
						<th>���ι�ȣ</th>
						<th>�߱�����</th>
						<th>�����ڻ���ڵ�Ϲ�ȣ</th>
						<th>��ȣ</th>
						<th>���޹޴��ڻ���ڵ�Ϲ�ȣ</th>
						<th>��ȣ</th>
						<th>�հ�ݾ�</th>
						<th>���ް���</th>
						<th>����</th>
						<th>ǰ���</th>
					</tr>
				</thead>
				<tbody>
				<?
					if (sizeof($arr_rs) > 0) {
						
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							$WRITTEN_DATE			= SetStringFromDB($arr_rs[$j]["WRITTEN_DATE"]);
							$CF_CODE				= SetStringFromDB($arr_rs[$j]["CF_CODE"]);
							$OUT_DATE				= SetStringFromDB($arr_rs[$j]["OUT_DATE"]);
							$BIZ_NO1				= SetStringFromDB($arr_rs[$j]["BIZ_NO1"]);
							$CP_NM1					= SetStringFromDB($arr_rs[$j]["CP_NM1"]);
							$BIZ_NO2				= SetStringFromDB($arr_rs[$j]["BIZ_NO2"]);
							$CP_NM2					= SetStringFromDB($arr_rs[$j]["CP_NM2"]);
							$TOTAL_PRICE			= SetStringFromDB($arr_rs[$j]["TOTAL_PRICE"]);
							$SUPPLY_PRICE			= SetStringFromDB($arr_rs[$j]["SUPPLY_PRICE"]);
							$SURTAX					= SetStringFromDB($arr_rs[$j]["SURTAX"]);
							$GOODS_NM				= SetStringFromDB($arr_rs[$j]["GOODS_NM"]);

							// ������ ��ȿ�� �˻�
							$err_str = "";

							if (chkCashStatementByCFCode($conn, $CF_CODE) > 0) {
								$err_str .=  "���ι�ȣ �ߺ�,";
							} 

							if (getOPCompanyNoByBizNo($conn, $BIZ_NO1) <= 0 && getOPCompanyNoByBizNo($conn, $BIZ_NO2) <= 0) {
								$err_str .=  "� ����ڹ�ȣ ����,";
							} 

							if ($err_str <> "") {
								$err_str = substr($err_str, 0, (strlen($err_str) -1));
								$err_str = str_replace(",","<div class='sp5'></div>",$err_str);
								$err_str = "<font color='red'>".$err_str."</font>";
							} else
								$err_str = "����";

					?>
					<tr class="first_tr">
						<td><input type="checkbox" name="chk[]" value="<?=$CF_CODE?>"/></td>
						<td>
							<?=$err_str?><br/>
							<? if ($err_str == "����") {?>
							<input type="hidden" name="ok[]" value="<?=$CF_CODE?>">
							<? } ?>
						</td>
						<td><?=$WRITTEN_DATE?></td>
						<td><?=$CF_CODE?></td>
						<td><?=$OUT_DATE?></td>
						<td><?=$BIZ_NO1?></td>
						<td><?=$CP_NM1?></td>
						<td><?=$BIZ_NO2?></td>
						<td><?=$CP_NM2?></td>
						<td><?=getSafeNumberFormatted($TOTAL_PRICE)?></td>
						<td><?=getSafeNumberFormatted($SUPPLY_PRICE)?></td>
						<td><?=getSafeNumberFormatted($SURTAX)?></td>
						<td><?=$GOODS_NM?></td>
					</tr>
				<?
							$err_str = "";
						}
					} else { 
				?> 
							<tr>
								<td align="center" height="30"  colspan="13">�����Ͱ� �����ϴ�. </td>
							</tr>
				<? 
							}
				?>

					
				</tbody>
			</table>
			<div class="btn_right">
				<input type="button" name="bb" value=" �����ڷ� ��� " class="btntxt" onclick="js_register();">&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="button" name="cc" value=" �����ڷ� ���� " class="btntxt" onclick="js_delete();">
			</div>
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