<?session_start();?>
<?
# =============================================================================
# File Name    : pop_lotte_complete.php 
# Modlue       : 
# Writer       : Sungwook Min 
# Create Date  : 2015-10-18
# Modify Date  : 
#					미완성 - 보류
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
	$menu_right = "OD015"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/company/company.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/stock/stock.php";
#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);

	if ($mode == "I") {
		
	#====================================================================
		$savedir1 = $g_physical_path."upload_data/temp_order_lotte_complete";
	#====================================================================

		$file_nm	= upload($_FILES[file_nm], $savedir1, 10000 , array('xls','xlsx'));
		
		//echo $file_nm;
		require_once "../../_PHPExcel/Classes/PHPExcel.php";
		$objPHPExcel = new PHPExcel();
		require_once "../../_PHPExcel/Classes/PHPExcel/IOFactory.php";
		$filename = '../../upload_data/temp_order_lotte_complete/'.$file_nm; 

		
		error_reporting(E_ALL ^ E_NOTICE);

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

			$A = trim($objWorksheet->getCell('A' . $i)->getValue()); //주문일자
			$B = trim($objWorksheet->getCell('B' . $i)->getValue()); //상품명
			$C = trim($objWorksheet->getCell('C' . $i)->getValue()); //옵션
			$E = trim($objWorksheet->getCell('D' . $i)->getValue()); //상품가격
			$F = trim($objWorksheet->getCell('E' . $i)->getValue()); //주문수량
			$G = trim($objWorksheet->getCell('F' . $i)->getValue()); //배송비
			$H = trim($objWorksheet->getCell('G' . $i)->getValue()); //주문번호
			$I = trim($objWorksheet->getCell('H' . $i)->getValue()); //배송지순번

			$A		= iconv("UTF-8", "EUC-KR", $A);
			$B		= iconv("UTF-8", "EUC-KR", $B);
			$C		= iconv("UTF-8", "EUC-KR", $C);
			$D		= iconv("UTF-8", "EUC-KR", $D);
			$E		= iconv("UTF-8", "EUC-KR", $E);
			$F		= iconv("UTF-8", "EUC-KR", $F);
			$G		= iconv("UTF-8", "EUC-KR", $G);
			$H		= iconv("UTF-8", "EUC-KR", $H);
			$I		= iconv("UTF-8", "EUC-KR", $I);
			
			InsertOrderLotteComplete($conn, $file_nm, $A, $B, $C, $D, $E, $F, $G, $H, $I, $s_adm_no);

		}
	}
	
	//echo $file_nm."<br/>";

#====================================================================
# DML Process
#====================================================================


?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script>
	function js_save() {
		
		var frm = document.frm;

		frm.mode.value = "I";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	function js_lotte_upload_excel() {

		var frm = document.frm;
		
		frm.mode.value = "lotte";
		frm.target = "";
		frm.action = "delivery_excel_complete_list_mart.php";
		frm.submit();
	}
	
</script>
</head>
<body id="popup_file">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="temp_no" value="<?= $file_nm?>">
<div id="popupwrap_file">
	<h1>롯데 출고 등록</h1>
	<div id="postsch">
		<h2>* 롯데 배송완료를 위한 출고리스트를 다운받아 등록 합니다.</h2>
		<div class="addr_inp">
			<table cellpadding="0" cellspacing="0" class="colstable02">
				<colgroup>
					<col width="15%" />
					<col width="35%" />
					<col width="15%" />
					<col width="35%" />
				</colgroup>
				<tr>
					<th>파일선택</th>
					<td colspan="3" style="position:relative" class="line">
						<input type="file" class="txt" style="width:80%;" name="file_nm" value="" />
						<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
					</td>
				</tr>
				<? if( $file_nm != "") { ?>
				<tr>
					<th>완료 엑셀 다운로드</th>
					<td colspan="3" style="position:relative" class="line">
						<a href="javascript:js_lotte_upload_excel();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
					</td>
				</tr>
				<? } ?>
			</table>
			<br/>
		</div>
	</div>
	<br />
</div>
<iframe src="" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>