<?
ini_set('memory_limit',-1);
session_start();
?>
<?
# =============================================================================
# File Name    : goods_info_update.php
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "GD009"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/syscode/syscode.php";

#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);
	

#====================================================================
# DML Process
#====================================================================
	if ($mode == "FR") {
	
		
	#====================================================================
		$savedir1 = $g_physical_path."upload_data/temp_goods";
	#====================================================================

		$file_nm	= upload($_FILES[file_nm], $savedir1, 10000 , array('xls','xlsx'));
		
		//echo $file_nm;
		require_once "../../_PHPExcel/Classes/PHPExcel.php";
		$objPHPExcel = new PHPExcel();
		require_once "../../_PHPExcel/Classes/PHPExcel/IOFactory.php";
		$filename = '../../upload_data/temp_goods/'.$file_nm; 

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

		error_reporting(E_ALL ^ E_NOTICE);

		$prev_goods_no = '';
		for ($i = 2 ; $i <= $maxRow ; $i++) {

			$goods_cate			= iconv("UTF-8","EUC-KR", trim($objWorksheet->getCell('A' . $i)->getValue()));
			$goods_name			= iconv("UTF-8","EUC-KR", trim($objWorksheet->getCell('B' . $i)->getValue()));
			$goods_sub_name		= iconv("UTF-8","EUC-KR", trim($objWorksheet->getCell('C' . $i)->getValue()));
            $goods_code			= iconv("UTF-8","EUC-KR", trim($objWorksheet->getCell('D' . $i)->getValue()));
			$cate_01			= iconv("UTF-8","EUC-KR", trim($objWorksheet->getCell('E' . $i)->getValue())); // 구성품수량
			$delivery_cnt_in_box= iconv("UTF-8","EUC-KR", trim($objWorksheet->getCell('F' . $i)->getValue()));
			$mstock_cnt			= iconv("UTF-8","EUC-KR", trim($objWorksheet->getCell('G' . $i)->getValue()));
			$cate_02			= iconv("UTF-8","EUC-KR", trim($objWorksheet->getCell('H' . $i)->getValue())); // 제조사
			$cate_03			= iconv("UTF-8","EUC-KR", trim($objWorksheet->getCell('I' . $i)->getValue())); // 공급사
			$cate_04			= iconv("UTF-8","EUC-KR", trim($objWorksheet->getCell('J' . $i)->getValue())); // 판매상태
			$buy_price			= iconv("UTF-8","EUC-KR", trim($objWorksheet->getCell('K' . $i)->getValue()));
			$sticker_price      = iconv("UTF-8","EUC-KR", trim($objWorksheet->getCell('L' . $i)->getValue()));
			$print_price        = iconv("UTF-8","EUC-KR", trim($objWorksheet->getCell('M' . $i)->getValue()));
			$delivery_price     = iconv("UTF-8","EUC-KR", trim($objWorksheet->getCell('N' . $i)->getValue()));
			$labor_price	    = iconv("UTF-8","EUC-KR", trim($objWorksheet->getCell('O' . $i)->getValue()));
			$other_price	    = iconv("UTF-8","EUC-KR", trim($objWorksheet->getCell('P' . $i)->getValue()));
			$sale_price			= iconv("UTF-8","EUC-KR", trim($objWorksheet->getCell('Q' . $i)->getValue()));
			$sale_susu          = iconv("UTF-8","EUC-KR", trim($objWorksheet->getCell('R' . $i)->getValue()));
			$tax_tf				= iconv("UTF-8","EUC-KR", trim($objWorksheet->getCell('S' . $i)->getValue()));
			$img_url			= iconv("UTF-8","EUC-KR", trim($objWorksheet->getCell('T' . $i)->getValue()));
			$file_path_150		= iconv("UTF-8","EUC-KR", trim($objWorksheet->getCell('U' . $i)->getValue()));
			$file_rnm_150		= iconv("UTF-8","EUC-KR", trim($objWorksheet->getCell('V' . $i)->getValue()));
			$contents			= iconv("UTF-8","EUC-KR", trim($objWorksheet->getCell('W' . $i)->getValue()));

			$stock_cnt			= 0; //= trim($data->sheets[0]['cells'][$i][8]);

			$use_tf				= "Y";
			
			if ($s_adm_cp_type <> "운영") {
				$cate_03 = $s_adm_com_code;
			} else {
				$cate_03 = getCompanyNoAsCode($conn, $cate_03);
			}

			$goods_name = str_replace("\"","",$goods_name);

			$stock_cnt		= trim($stock_cnt);
			$price			= trim($price);
			$sale_price		= trim($sale_price);

			$stock_cnt		= str_replace(",","",$stock_cnt);
			$price			= str_replace(",","",$price);
			$buy_price		= str_replace(",","",$buy_price);
			$sale_price		= str_replace(",","",$sale_price);
			$extra_price	= str_replace(",","",$extra_price);

			$sticker_price		= str_replace(",","",$sticker_price);
			$print_price		= str_replace(",","",$print_price);
			$delivery_price		= str_replace(",","",$delivery_price);
			$labor_price		= str_replace(",","",$labor_price);
			$other_price		= str_replace(",","",$other_price);

			$goods_name			= SetStringToDB($goods_name);
			$goods_sub_name  	= SetStringToDB($goods_sub_name);
			$goods_code			= SetStringToDB($goods_code);
			$cate_01			= SetStringToDB($cate_01);
			$cate_02			= SetStringToDB($cate_02);
			$cate_03			= SetStringToDB($cate_03);
			$cate_04			= SetStringToDB($cate_04);

			if($delivery_cnt_in_box == "" || $delivery_cnt_in_box == "0")
				$delivery_cnt_in_box = 1;

			$price			= $buy_price + $sticker_price + $print_price + round($delivery_price / $delivery_cnt_in_box) + $labor_price + $other_price; 
			$extra_price	= $price - $buy_price;

			if($goods_code <> "")
				$prev_goods_no = insertTempGoods($conn, $file_nm, $goods_cate, $goods_code, $goods_name, $goods_sub_name, $cate_01, $cate_02, $cate_03, $cate_04, $price, $buy_price, $sale_price, $extra_price, $stock_cnt, $mstock_cnt, $tax_tf, $img_url, $file_nm_100, $file_rnm_100, $file_path_100, $file_size_100, $file_ext_100, $file_nm_150, $file_rnm_150, $file_path_150, $file_size_150, $file_ext_150, $contents, $memo, $delivery_cnt_in_box, $sticker_price, $print_price, $delivery_price, $sale_susu, $labor_price, $other_price, $use_tf, $s_adm_no, $prev_goods_no);

		}

?>	
<script language="javascript">
		location.href =  'goods_write_file.php?mode=L&temp_no=<?=$file_nm?>';
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
<script type="text/javascript" src="../js/calendar.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->

<style type="text/css">
<!--
/*#pop_table {z-index: 1; left: 80; overflow: auto; width: 500; height: 220}*/
#ex_scroll { z-index: 1; background-color:#f7f7f7; overflow: auto; width: 100%; height:155px; border:1px solid #d1d1d1;}
#temp_scroll { z-index: 1; background-color:#f7f7f7; overflow: auto; width: 95%; height:100%; border:1px solid #d1d1d1;}
-->
</style>

<script language="javascript">
	
	// 조회 버튼 클릭 시 
	function js_list() {
		var frm = document.frm;
		
		frm.method = "get";
		frm.action = "goods_list.php";
		frm.submit();
	}

	// 저장 버튼 클릭 시 
	function js_save() {
		
		var file_rname = "<?= $file_rname ?>";
		var frm = document.frm;
		
		if (isNull(frm.file_nm.value)) {
			alert('파일을 선택해 주세요.');
			frm.file_nm.focus();
			return ;		
		}
		
		if(AllowAttach(frm.file_nm)) { 

			if (isNull(file_rname)) {
				frm.mode.value = "FR";
			} else {
				frm.mode.value = "I";
			}

			frm.method = "post";
			frm.action = "goods_write_file.php";
			frm.submit();
		}
	}


	/**
	* 파일 첨부에 대한 선택에 따른 파일첨부 입력란 visibility 설정
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
			alert("입력하신 파일은 업로드 될 수 없습니다!");
			return;
		}
	}

	function AllowAttach(obj) {
		var file = obj.value;
		extArray = new Array(".xls", ".xlsx");
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
			return true;
		}else{
			alert("입력하신 파일은 업로드 될 수 없습니다!");
			return false;
		}
		return true;
	}


	function js_reload() {
		location.href =  'goods_write_file.php?mode=L&temp_no=<?=$temp_no?>';
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
			alert("선택 하신 자료가 없습니다.");
		} else {

			bDelOK = confirm('선택하신 자료를 삭제 하시겠습니까?');
			
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
		bDelOK = confirm('정상 데이타는 모두 등록 하시겠습니까?');

		if (bDelOK==true) {
			frm.mode.value = "I";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
		
	}

</script>
<script>
function LeftToRight() {
	var objSel, objSel
	var frm = document.frm;
	
	objLeft = frm.sel_left;
	objRight = frm.sel_right;
		
	for(var i = 0; i < objLeft.length; i++)
		if(objLeft.options[i].selected)
			objRight.options[objRight.length] = new Option(objLeft.options[i].text,  objLeft.options[i].value);

	for(var i = 0; i < objRight.length; i++) {
		for(var j = 0; j < objLeft.length; j++) {
			if(objLeft.options[j].selected) {
				objLeft.options[j] = null;
				break;
			}
		}
	}
}

function RightToLeft() {
	var objSel, objSel
	var frm = document.frm;

	objLeft = frm.sel_left;
	objRight = frm.sel_right;
		
	for(var i = 0; i < objRight.length; i++)
		if(objRight.options[i].selected)
			objLeft.options[objLeft.length] = new Option(objRight.options[i].text,  objRight.options[i].value);

	for(var i = 0; i < objLeft.length; i++) {
		for(var j = 0; j < objRight.length; j++) {
			if(objRight.options[j].selected) {
				objRight.options[j] = null;
				break;
			}	
		}
	} 
}

function js_select_message() {
	
	var frm = document.frm;
	var type = "";

	objLeft = frm.sel_left;
	objRight = frm.sel_right;

	clear_select(objLeft);
	
	for (k = 0; k < arr_message_no.length; k++) {
		
		objLeft.options[objLeft.length] = new Option(arr_message_name[k],  arr_message_no[k]);
	}
	


}

function clear_select(obj){
	sel_len = obj.length;
	for(i = sel_len ; i >= 0; i--) {
		obj.options[i] = null;
	}
	return ;
}


</script>
</head>
<body id="admin">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="temp_no" value="<?=$temp_no?>">
<input type="hidden" name="goods_no" value="">

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

		</td>
		<td class="contentarea">

			<!-- S: mwidthwrap -->
			<div id="mwidthwrap">
				<h2>상품 수정</h2>
				<table cellpadding="0" cellspacing="0" class="colstable">

					<colgroup>
						<col width="12%">
						<col width="38%">
						<col width="12%">
						<col width="38%">
					</colgroup>
					<tbody>
						<tr>
							<th>파일 <br/><br/><a href="/_common/download_file.php?file_name=insert_goods.xls&filename_rnm=insert_example.xls&str_path=manager/goods/">입력파일받기</a></th>
							<td colspan="3"><input type="file" name="file_nm" style="width:60%;" class="txt"></td>
						</tr>

						<tr>
							<th>항목</th>
							<td colspan="3">
								<table border="0">
									<tr>
										<td width="230" style="padding: 10px 10px 10px 0px">
											* 항목 리스트 <br>
											<select name="sel_left" size="10" style="width:230px" multiple>
												<?
													//$arr_message_no = listMessageBoard($conn, $rs_bb_no, $rs_bb_code, "NO");
													$arr_dcode = listDcode($conn, "GOODS_INFO_UPDATE", "Y", "N", "", "", 1, 1000); 
													
													if (sizeof($arr_dcode) > 0) {
						
														for ($j = 0 ; $j < sizeof($arr_dcode); $j++) {
															
															$DCODE					= trim($arr_dcode[$j]["DCODE"]);
															$DCODE_NM				= trim($arr_dcode[$j]["DCODE_NM"]);
												?>
												<option value="<?=$DCODE?>"><?=$DCODE_NM?></option>
												<?
														}
													}
												?>
											</select>
										</td>
										<td style="padding: 10px 10px 10px 10px" align="center">
											&nbsp;
											<br><br>
											<input type=button name=LR value=' &gt; ' onClick="javascript:LeftToRight()" class="txt" style="width:30px">
											<br><br>
											<input type=button name=RL value=' &lt; '  onClick="javascript:RightToLeft()" class="txt" style="width:30px">
										</td>
										<td width="230" style="padding: 10px 10px 10px 10px">
											* 적용 사용자 리스트 <br>
											<select name="sel_right" size="10" style="width:230px" multiple>
												<?
													//$arr_message_yes = listMessageBoard($conn, $rs_bb_code, $rs_bb_no, "YES");
													
													if (sizeof($arr_message_yes) > 0) {
						
														for ($j = 0 ; $j < sizeof($arr_message_yes); $j++) {
															$ADM_NO					= trim($arr_message_yes[$j]["ADM_NO"]);
															$ADM_NAME				= trim($arr_message_yes[$j]["ADM_NAME"]);
												?>
												<option value="<?=$ADM_NO?>"><?=$ADM_NAME?></option>
												<?
														}
													}
												?>
											</select>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</tbody>
				</table>

				<div class="btnright">
				<? if ($file_nm <> "" ) {?>
					<? if ($sPageRight_U == "Y") {?>
					<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
					<? } ?>
				<? } else {?>
					<? if ($sPageRight_I == "Y") {?>
					<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
					<? } ?>
				<? }?>
				</div>

				<div class="sp20"></div>
				<div>
					* 총 <?=sizeof($arr_rs)?> 건 &nbsp;&nbsp;
				</div>
				<div id="temp_scroll">
				<table cellpadding="0" cellspacing="0" class="rowstable01" style="width:1915px">
					<colgroup>
						<col width="35">
						<col width="200">
						<col width="150"><!--카테고리-->
						<col width="60"><!--구성품수-->
						<col width="200"><!--상품명-->
						<col width="90"><!--모델명-->
						<col width="80"><!--상품코드-->
						<col width="100"><!--제조사-->
						<col width="100"><!--공급사-->
						<col width="100"><!--판매상태-->
						<col width="80"><!--매입가-->
						<col width="80"><!--매입합계-->
						<col width="80"><!--판매가-->
						<col width="80"><!--배송비-->
						<col width="80"><!--과세-->
						<col width="110"><!--이미지-->
						<col width="100"><!-- 상세 보기 -->

					</colgroup>
					<thead>
						<tr>
							<th>&nbsp;</th>
							<th>비고</th>
							<th>카테고리</th>
							<th>구성품수</th>
							<th>상품명</th>
							<th>박스입수</th>
							<th>모델명</th>
							<th>상품코드</th>
							<th>제조사</th>
							<th>공급사</th>
							<th>판매상태</th>
							<th>매입가</th>
							<th>매입합계</th>
							<th>판매가</th>
							<th>배송비</th>
							<th>과세여부</th>
							<th>이미지</th>
							<th class="end">상품상세</th>
						</tr>
					</thead>
					<tbody>
					<?
						$nCnt = 0;
						
						if (sizeof($arr_rs) > 0) {
							
							for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
																
								$rn							            = trim($arr_rs[$j]["rn"]);
								$GOODS_NO			              = trim($arr_rs[$j]["GOODS_NO"]);
								$GOODS_CATE			 = SetStringFromDB($arr_rs[$j]["GOODS_CATE"]);
								$GOODS_CODE			 = SetStringFromDB($arr_rs[$j]["GOODS_CODE"]);
								$GOODS_NAME			 = SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
								$GOODS_SUB_NAME	 = SetStringFromDB($arr_rs[$j]["GOODS_SUB_NAME"]);
								$DELIVERY_CNT_IN_BOX = trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);
								$CATE_01				= SetStringFromDB($arr_rs[$j]["CATE_01"]);
								$CATE_02				= SetStringFromDB($arr_rs[$j]["CATE_02"]);
								$CATE_03				= SetStringFromDB($arr_rs[$j]["CATE_03"]);
								$CATE_04				= SetStringFromDB($arr_rs[$j]["CATE_04"]);
								$PRICE						  		 = trim($arr_rs[$j]["PRICE"]);
								$BUY_PRICE						 = trim($arr_rs[$j]["BUY_PRICE"]);
								$SALE_PRICE		   	            = trim($arr_rs[$j]["SALE_PRICE"]);
								$EXTRA_PRICE		           = trim($arr_rs[$j]["EXTRA_PRICE"]);
								$LABOR_PRICE		           = trim($arr_rs[$j]["LABOR_PRICE"]);
								$OTHER_PRICE		           = trim($arr_rs[$j]["OTHER_PRICE"]);
								$STOCK_CNT			             = trim($arr_rs[$j]["STOCK_CNT"]);
								$TAX_TF					            = trim($arr_rs[$j]["TAX_TF"]);
								$IMG_URL						   = trim($arr_rs[$j]["IMG_URL"]);
								$FILE_NM_150		= trim($arr_rs[$j]["FILE_NM_150"]);
								$FILE_RNM_150		= trim($arr_rs[$j]["FILE_RNM_150"]);
								$FILE_PATH_150	= trim($arr_rs[$j]["FILE_PATH_150"]);
								$FILE_SIZE_150	= trim($arr_rs[$j]["FILE_SIZE_150"]);
								$FILE_EXT_150		= trim($arr_rs[$j]["FILE_EXT_150"]);
								$OPTION01_NAME	= SetStringFromDB($arr_rs[$j]["OPTION01_NAME"]);
								$OPTION01_VALUE	= SetStringFromDB($arr_rs[$j]["OPTION01_VALUE"]);
								$OPTION02_NAME	= SetStringFromDB($arr_rs[$j]["OPTION02_NAME"]);
								$OPTION02_VALUE	= SetStringFromDB($arr_rs[$j]["OPTION02_VALUE"]);
								$CONTENTS				= trim($arr_rs[$j]["CONTENTS"]);

								$DUPLICATED_TF      = trim($arr_rs[$j]["DUPLICATED_TF"]);

								$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
								$DEL_TF					= trim($arr_rs[$j]["DEL_TF"]);
								$REG_DATE				= date("Y-m-d",strtotime($REG_DATE));
								
								// 데이터 유효성 검사
								$err_str = "정상";
								$err_warning_str = "";
								
								if ($DUPLICATED_TF == "Y") {
									$err_str .=  "이미 등록되어 있음,";
								}

								if ($GOODS_CATE == "") {
									$err_str =  "카테고리 누락,";
								} else {
									if (!chkCateNm($conn, $GOODS_CATE)) {
										$err_str .=  "카테고리 오류,";
									}
								}

								$cntGoodsCode = chkDuplicateGoodsCode($conn, $GOODS_CODE);
								
								if($serial_part == "")
									$serial_part = substr($GOODS_CODE, 5);

								if(strlen($serial_part) >= 5)
									$cntPartlyGoodsCode = chkDuplicatePartlyGoodsCode($conn, $serial_part);
								else
									$cntPartlyGoodsCode = 0;

								if($cntGoodsCode > 0)
									$err_str =  "상품코드 중복,";
							
								if($cntPartlyGoodsCode > 0)
									$err_warning_str =  "상품코드-일련번호 중복,";

								
								if (startsWith($GOODS_CATE,"14"))
								{
									$arr_rs_goods_sub = selectTempGoodsSub($conn, $temp_no, $GOODS_NO);
									$cntGoodsSub = sizeof($arr_rs_goods_sub);
									if($cntGoodsSub == 0)
									{
										$err_str .=  "구성품 누락,";
									} 
									else 
									{
										for ($k= 0 ; $k < $cntGoodsSub; $k++) 
										{
											$GOODS_SUB_NO = trim($arr_rs_goods_sub[$k]["GOODS_SUB_NO"]);
											
											if($GOODS_SUB_NO == "0")
											{
												$err_str .=  "구성품 상품번호 찾을수 없음,";
											}
										}
									}
									
								} else $cntGoodsSub = "없음";
								
								
								if ($GOODS_NAME == "") {
									$err_str .=  "상품명 누락,";
								}

								/*
								if ($CATE_01 == "") {
									$err_str .=  "원산지 누락,";
								}
								*/
								/*
								if ($CATE_02 == "") {
									$err_str .=  "제조사 누락,";
								}
								*/

								if ($CATE_03 == "") {
									$err_str .=  "공급업체 누락,";
								} else {
									if (getCompayChk($conn, "구매", $s_adm_cp_type, $CATE_03) == "") {
										$err_str .=  "공급업체 오류,";
									}
								}
								
								if ($CATE_04 == "") {
									$err_str .=  "판매상태 누락,";
								} else {
									if (getDcodeName($conn, "GOODS_STATE", $CATE_04) == "") {
										$err_str .=  "판매상태 오류,";
									}
								}

								if ($BUY_PRICE == "") {
									$err_str .=  "매입가 누락,";
								} else {
									if (!is_numeric($BUY_PRICE)) {
										$err_str .=  "매입가(숫자만 가능) 오류,";
									}
								}

								if ($SALE_PRICE == "") {
									$err_str .=  "판매가 누락,";
								} else {
									if (!is_numeric($SALE_PRICE)) {
										$err_str .=  "판매가(숫자만 가능) 오류,";
									}
								}

								if ($STOCK_CNT == "") {
									$err_str .=  "재고 누락,";
								} else {
									if (!is_numeric($STOCK_CNT)) {
										$err_str .=  "재고(숫자만 가능) 오류,";
									}
								}

								if ($TAX_TF == "") {
									$err_str .=  "과세구분 누락,";
								} else {
									if (getDcodeName($conn, "TAX_TF", $TAX_TF) == "") {
										$err_str .=  "과세구분 오류,";
									}
								}

								if ($FILE_PATH_150 <> "") {
									if ($FILE_RNM_150 <> "") {
										$file_path = $_SERVER[DOCUMENT_ROOT].$FILE_PATH_150.$FILE_RNM_150;
										if(!file_exists($file_path)){
											$err_str .=  "이미지 경로 오류,";
										}
									}
								}

								if ($err_str <> "정상") {
									$err_str = str_replace("정상","",$err_str);
									$err_str = substr($err_str, 0, (strlen($err_str) -1));
									$err_str = str_replace(",","<div class='sp5'></div>",$err_str);
									$err_str = "<font color='red'>".$err_str."</font>";
								}

								if ($err_warning_str <> "") {
									$err_warning_str = substr($err_warning_str, 0, (strlen($err_warning_str) -1));
									$err_warning_str = str_replace(",","<div class='sp5'></div>",$err_warning_str);
									$err_warning_str = "<font color='blue'>".$err_warning_str."</font>";
								}

								//echo $CATE_03;
								//echo "ffff".$s_adm_cp_type;

								if (!is_numeric($CATE_03)) {
									$CATE_03 = getCompanyCode($conn, $CATE_03);
								}

					?>
						<tr>
							<td class="filedown">
								<input type="checkbox" name="chk[]" value="<?=$GOODS_NO?>">
							</td>
							<td class="modeual_nm" style="padding:8px 0 3px 0">
								<a href="javascript:js_view('<?= $rn ?>','<?= $temp_no ?>','<?= $GOODS_NO ?>');"><?=$err_str?><?=($err_warning_str != "" ? ", ".$err_warning_str : "")?></a>
								
								<? if ($err_str == "정상") {?>
								<input type="hidden" name="ok[]" value="<?=$GOODS_NO?>">
								<? } ?>								
							</td>

							<td class="modeual_nm"><?= getCategoryName($conn, $GOODS_CATE) ?></td>
							<td><?=$cntGoodsSub?></td>
							<td class="modeual_nm"><?=$GOODS_NAME?></td>
							<td class="modeual_nm"><?=$DELIVERY_CNT_IN_BOX?></td>
							<td class="modeual_nm"><?=$GOODS_SUB_NAME?></td>
							<td class="modeual_nm"><?=$GOODS_CODE?></td>
							<td class="modeual_nm"><?=$CATE_02?></td>
							<td class="modeual_nm"><?=getCompanyName($conn, $CATE_03)?></td>
							<td><?= getDcodeName($conn, "GOODS_STATE", $CATE_04);?></td>
							<td class="price"><?= number_format($BUY_PRICE) ?></td>
							<td class="price"><?= number_format($PRICE) ?></td>
							<td class="price"><?= number_format($SALE_PRICE) ?></td>
							<td class="price"><?= number_format($EXTRA_PRICE) ?></td>
							<td><?= getDcodeName($conn, "TAX_TF", $TAX_TF);?></td>
							<td>
								<?
									if ($IMG_URL <> "") {
								?>
								<img src="<?=$IMG_URL?>" width="50" height="50">
								<?
									} else {
										if ($FILE_PATH_150 <> "") {
											if($FILE_RNM_150 <> "") {
								?>
								<img src="<?=$FILE_PATH_150?><?=$FILE_RNM_150?>" width="50" height="50">
								<?
											}
										}
									}
								?>
								&nbsp;
							</td>
							<td class="filedown"><a href="javascript:js_view_html('<?= $temp_no ?>','<?= $GOODS_NO ?>');">상세 보기</a></td>
						</tr>
					<?			
										$err_str = "";
									}
								} else { 
							?> 
								<tr>
									<td align="center" height="50"  colspan="26">데이터가 없습니다. </td>
								</tr>
							<? 
								}
							?>
							</tbody>
						</table>
						</div>


				<div class="btnright">
					<a href="javascript:js_register();"><img src="../images/admin/btn_regist_02.gif" alt="등록" /></a>
					<a href="javascript:js_delete();"><img src="../images/admin/btn_delete.gif" alt="삭제" /></a>
				</div>

			</div>
			<!-- // E: mwidthwrap -->

    </td>
  </tr>
  </table>

</div>
<script type="text/javascript" src="../js/wrest.js"></script>
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