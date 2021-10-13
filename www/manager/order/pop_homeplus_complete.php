<?session_start();?>
<?
# =============================================================================
# File Name    : pop_homeplus_complete.php
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
		$savedir1 = $g_physical_path."upload_data/temp_order_homeplus_complete";
	#====================================================================

		$file_nm	= upload($_FILES[file_nm], $savedir1, 10000 , array('xls','xlsx'));
		
		//echo $file_nm;
		require_once "../../_PHPExcel/Classes/PHPExcel.php";
		$objPHPExcel = new PHPExcel();
		require_once "../../_PHPExcel/Classes/PHPExcel/IOFactory.php";
		$filename = '../../upload_data/temp_order_homeplus_complete/'.$file_nm; 

		
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

		for ($i = 3 ; $i <= $maxRow ; $i++) {

			$homeplus_delivery_code = trim($objWorksheet->getCell('F' . $i)->getValue()); //배송번호

			$homeplus_delivery_code		= iconv("UTF-8","EUC-KR",$homeplus_delivery_code);
			
			//echo $homeplus_delivery_code."<br/>";
			InsertOrderHomeplusComplete($conn, $file_nm, $homeplus_delivery_code, $s_adm_no);

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

	function js_homeplus_upload_excel() {

		var frm = document.frm;
		
		frm.mode.value = "homeplus";
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
	<h1>홈플러스 출고 등록</h1>
	<div id="postsch">
		<h2>* 홈플러스 배송완료를 위한 출고리스트를 다운받아 등록 합니다.</h2>
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
						<a href="javascript:js_homeplus_upload_excel();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a> 출고관리 / 업체출고관리 / 운송장번호 일괄등록
					</td>
				</tr>
				<? } ?>
			</table>
			<br/>
			* 혹 메모리 오류가 발생하면 F열(배송번호)을 남기고 좌우 컬럼들을 "내용지우기"로 지워주시거나 파일을 나누어 (열기준, 예를들어 1000줄 단위로) 넣어주세요.<br/>
			(오류예제 : "Fatal error: Allowed memory size of 134217728 bytes exhausted (tried to allocate 96 bytes) in ****/PHPExcel/Worksheet.php on line ***")
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