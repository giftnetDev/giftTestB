<?
ini_set('memory_limit',-1);
session_start();
?>
<?
# =============================================================================
# File Name    : fix_stock_input.php
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "SG011"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/stock/stock.php";


	function insertTempFixStock($db, $file_nm, $goods_no, $qty, $bqty, $goods_stock_cnt, $goods_bstock_cnt, $memo) {
		
		$query = "INSERT INTO TBL_TEMP_FIX_GOODS_STOCK (TEMP_FILE, GOODS_NO, STOCK_CNT, BSTOCK_CNT, GOODS_STOCK_CNT, GOODS_BSTOCK_CNT, MEMO) 
									VALUES ('$file_nm','$goods_no', '$qty', '$bqty', '$goods_stock_cnt', '$goods_bstock_cnt', '$memo');";

		//echo $query."<br/>";
		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);

	if ($mode == "I") {
		
	#====================================================================
		$savedir1 = $g_physical_path."upload_data/temp_stock";
	#====================================================================

		$file_nm	= upload($_FILES[file_nm], $savedir1, 10000 , array('xls','xlsx'));
		
		//echo $file_nm;
		require_once "../../_PHPExcel/Classes/PHPExcel.php";
		$objPHPExcel = new PHPExcel();
		require_once "../../_PHPExcel/Classes/PHPExcel/IOFactory.php";
		$filename = '../../upload_data/temp_stock/'.$file_nm; 

		
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

			$goods_no		     = trim($objWorksheet->getCell('A' . $i)->getValue());
			$goods_code	         = trim($objWorksheet->getCell('B' . $i)->getValue());
			$goods_name	         = trim($objWorksheet->getCell('C' . $i)->getValue());
			$delivery_cnt_in_box = trim($objWorksheet->getCell('D' . $i)->getValue());
			$qty				 = trim($objWorksheet->getCell('E' . $i)->getValue());
			$bqty				 = trim($objWorksheet->getCell('F' . $i)->getValue());
			$memo				 = trim($objWorksheet->getCell('G' . $i)->getValue());

			$goods_no			= iconv("UTF-8","EUC-KR",$goods_no);
			$goods_code			= iconv("UTF-8","EUC-KR",$goods_code);
			$goods_name			= iconv("UTF-8","EUC-KR",$goods_name);
			$qty				= iconv("UTF-8","EUC-KR",$qty);
			$bqty				= iconv("UTF-8","EUC-KR",$bqty);
			$memo				= iconv("UTF-8","EUC-KR",$memo);

			$arr_goods = selectGoods($conn, $goods_no);

			$str_goods_code	= trim($arr_goods[0]["GOODS_CODE"]);
			$STOCK_CNT		= trim($arr_goods[0]["STOCK_CNT"]);
			$BSTOCK_CNT		= trim($arr_goods[0]["BSTOCK_CNT"]);


			//잘못된 파일 넣었을때를 막기위한 방어코드
			if($str_goods_code != $goods_code) continue;

			if (($qty <> "") || ($bqty <> "")) {

				//echo $file_nm."-".$goods_no."-".$qty."-".$bqty."-".$STOCK_CNT."-".$BSTOCK_CNT."<br>";

				$result = insertTempFixStock($conn, $file_nm, $goods_no, $qty, $bqty, $STOCK_CNT, $BSTOCK_CNT, $memo);
			}
			//echo "상품 명 ".$str_goods_nm."<br>";

		}
	}

	
	//echo $pb_nm; 
	//echo $$mode;

	//$result	= false;

#====================================================================
# DML Process
#====================================================================



	if ($result) {
		//$strParam = $strParam."?nPage=".$nPage."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&con_cp_type=".$con_cp_type;
		
		if ($mode == "I" ) {
?>	
<script language="javascript">
	alert('정상 처리 되었습니다.');
	
	opener.js_reload('<?=$file_nm?>');
	
	//alert('1');

	self.close();
	//location.href =  "company_modify.php<?=$strParam?>&mode=S&temp_no=<?=$temp_no?>&cp_no=<?=$cp_no?>";
</script>
<?
		} 
		
		mysql_close($conn);
		exit;
	}	
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

		frm.mode.value = "I";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

</script>
</head>
<body id="popup_file">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="temp_no" value="<?= $temp_no?>">
<div id="popupwrap_file">
	<h1>재고 실사 등록</h1>
	<div id="postsch">
		<h2>* 재고 실사 파일을 등록 합니다.</h2>
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
					<td colspan="5" style="position:relative" class="line">
						<input type="file" class="txt" style="width:80%;" name="file_nm" value="" />
						<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
					</td>
				</tr>
			</table>
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