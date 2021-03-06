<?
session_start();
?>
<?
# =============================================================================
# File Name    : goods_write_file.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#				 2015.06.23 원산지, 옵션 리스팅 제거, 샘플 이미지 변경, 샘플 엑셀파일 변경  - 민성욱
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
	$menu_right = "GD006"; // 메뉴마다 셋팅 해 주어야 합니다

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

		$file_nm	= upload($_FILES[file_nm], $savedir1, 10000 , array('txt','TXT'));
		
		$file_dir = "../../upload_data/temp_goods/".$file_nm;

		$fo = fopen($file_dir, "r");

		$number_id = 0;

		while($str = fgets($fo, 3000)){

			$number_id++;

			$a_str = explode("	",$str);

			$goods_cate			= trim($a_str[0]);
			$goods_name			= trim($a_str[1]);
			$goods_sub_name	= trim($a_str[2]);
			$goods_code			= trim($a_str[3]);
			$cate_01			= ''; //	= trim($a_str[4]);
			$cate_02				= trim($a_str[5]);
			$cate_03				= trim($a_str[6]);
			$stock_cnt			= 0; //= trim($a_str[7]);
			$cate_04				= trim($a_str[7]);
			$price					= trim($a_str[8]);
			$buy_price			= trim($a_str[9]);
			$sale_price			= trim($a_str[10]);
			$extra_price		= trim($a_str[11]);
			$tax_tf					= trim($a_str[12]);
			$img_url				= trim($a_str[13]);
			$file_path_150	= trim($a_str[14]);
			$file_rnm_150		= trim($a_str[15]);
			//$option01_name	= trim($a_str[17]);
			//$option01_value	= trim($a_str[18]);
			//$option02_name	= trim($a_str[19]);
			//$option03_value	= trim($a_str[20]);
			$contents				= trim($a_str[16]);
			$use_tf					= "Y";

			if ($s_adm_cp_type <> "운영") {
				$cate_03 = $s_adm_com_code;
			}

			$goods_name		= str_replace("\"","",$goods_name);

			$contents	= str_replace("\"<","<",$contents);
			$contents	= str_replace(">\"",">",$contents);
			$contents	= str_replace("\"\"","'",$contents);
			$contents	= str_replace("\"","",$contents);

			$stock_cnt		= trim($stock_cnt);
			$price				= trim($price);
			$buy_price		= trim($buy_price);
			$sale_price		= trim($sale_price);
			$extra_price	= trim($extra_price);

			//$stock_cnt		= str_replace(",","",$stock_cnt);
			$price				= str_replace(",","",$price);
			$buy_price		= str_replace(",","",$buy_price);
			$sale_price		= str_replace(",","",$sale_price);
			$extra_price	= str_replace(",","",$extra_price);
			
			$tax_tf				= trim($tax_tf);

			$goods_name			= SetStringToDB($goods_name);
			$goods_sub_name	= SetStringToDB($goods_sub_name);
			$goods_code			= SetStringToDB($goods_code);
			//$cate_01				= SetStringToDB($cate_01);
			$cate_02				= SetStringToDB($cate_02);
			$cate_03				= SetStringToDB($cate_03);
			$cate_04				= SetStringToDB($cate_04);
			//$option01_name	= SetStringToDB($option01_name);
			//$option01_value	= SetStringToDB($option01_value);
			//$option02_name	= SetStringToDB($option02_name);
			//$option02_value	= SetStringToDB($option02_value);
			
			//echo $tex_tf."<br>";

			if ($number_id <> "1") {
				
				if ($goods_name <> "") {
					$temp_result = insertTempGoods($conn, $file_nm, $goods_cate, $goods_code, $goods_name, $goods_sub_name, $cate_01, $cate_02, $cate_03, $cate_04, $price, $buy_price, $sale_price, $extra_price, $stock_cnt, $tax_tf, $img_url, $file_nm_100, $file_rnm_100, $file_path_100, $file_size_100, $file_ext_100, $file_nm_150, $file_rnm_150, $file_path_150, $file_size_150, $file_ext_150, $option01_name, $option01_value, $option02_name, $option02_value, $contents, $use_tf, $s_adm_no);
				}
			}	
		}

		fclose($fo);
		
		/*
		$temp_file = $savedir1."/".$file_nm;						
		$exist = file_exists($temp_file);

		if($exist){
			$delrst=unlink($temp_file);
			if(!$delrst) {
				echo "삭제실패";
			}
		}
		*/
?>	
<script language="javascript">
	location.href =  'goods_write_file_txt.php?mode=L&temp_no=<?=$file_nm?>';
</script>
<?
		exit;

	}	
	

	if ($mode == "I") {

		$row_cnt = count($ok);
		
		$str_goods_no = "";

		for ($k = 0; $k < $row_cnt; $k++) {
			$str_goods_no .= "'".$ok[$k]."',";
		}

		$str_goods_no = substr($str_goods_no, 0, (strlen($str_goods_no) -1));
		//echo $str_cp_no;
		$insert_result = insertTempToRealGoods($conn, $temp_no, $str_goods_no);

		if ($insert_result) {
			$delete_result = deleteTempToRealGoods($conn, $temp_no, $str_goods_no);
		}

		$mode = "L";

	}

	if ($mode == "D") {

		$row_cnt = count($chk);
		
		for ($k = 0; $k < $row_cnt; $k++) {
		
			$tmp_goods_no = $chk[$k];

			$temp_result = deleteTempGoods($conn, $temp_no, $tmp_goods_no);
		}
		
		$mode = "L";
	}

	if ($mode == "L") {
		$arr_rs = listTempGoods($conn, $temp_no);
	}
	
	if ($result) {
?>	
<script language="javascript">
		alert('정상 처리 되었습니다.');
		location.href =  'goods_list.php';
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
		
		AllowAttach(frm.file_nm);

		if (isNull(file_rname)) {
			frm.mode.value = "FR";
		} else {
			frm.mode.value = "I";
		}

		frm.method = "post";
		frm.action = "goods_write_file_txt.php";
		frm.submit();
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
		extArray = new Array(".txt");
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
			alert("입력하신 파일은 업로드 될 수 없습니다!");
			return;
		}
	}

	function js_view(rn, file_nm, goods_no) {
		
		var url = "goods_modify.php?mode=S&temp_no="+file_nm+"&goods_no="+goods_no;
		NewWindow(url, '상품대량입력', '860', '513', 'YES');
		
	}

	function js_reload() {
		location.href =  'goods_write_file_txt.php?mode=L&temp_no=<?=$temp_no?>';
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
	
	function js_view_html(temp_no, goods_no) {

		var url = "pop_goods_detail_view.php?temp_no="+temp_no+"&goods_no="+goods_no;
		NewWindow(url,'pop_detail','830','600','Yes');
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
				<h2>상품 등록</h2>
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
								입력 예
								<br><br>
								<a href="/_common/download_file.php?file_name=insert_example.xls&filename_rnm=insert_example_without_options.xls&str_path=manager/goods/">받기</a>
							</th>
							<td colspan="3">
								<div id="ex_scroll">
								<img src="goods_example_without_options.png">
								</div>
							</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th>파일</th>
							<td colspan="3"><input type="file" name="file_nm" style="width:60%;" class="txt"></td>
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
					<? if ($insert_result) {?>
					* 등록건 <?=$row_cnt?> 건
					<? }?>
				</div>
				<div id="temp_scroll">
				<table cellpadding="0" cellspacing="0" class="rowstable01" style="width:2635px">
					<colgroup>
						<col width="35">
						<col width="200">
						<col width="150"><!--카테고리-->
						<col width="200"><!--상품명-->
						<col width="90"><!--모델명-->
						<col width="80"><!--상품코드-->
						<col width="100"><!--제조사-->
						<col width="100"><!--공급사-->
						<col width="100"><!--판매상태-->
						<col width="80"><!--매입합계-->
						<col width="80"><!--매입가-->
						<col width="80"><!--판매가-->
						<col width="80"><!--배송비-->
						<col width="80"><!--배송비-->
						<col width="110"><!--이미지-->
						<col width="100"><!-- 상세 보기 -->
					</colgroup>
					<thead>
						<tr>
							<th>&nbsp;</th>
							<th>비고</th>
							<th>카테고리</th>
							<th>상품명</th>
							<th>모델명</th>
							<th>상품코드</th>
							<th>제조사</th>
							<th>공급사</th>
							<th>판매상태</th>
							<th>매입합계</th>
							<th>매입가</th>
							<th>판매가</th>
							<th>배송비</th>
							<th>과세구분</th>
							<th>이미지</th>
							<th class="end">상품상세</th>
						</tr>
					</thead>
					<tbody>
					<?
						$nCnt = 0;
						
						if (sizeof($arr_rs) > 0) {
							
							for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
																
								$rn							= trim($arr_rs[$j]["rn"]);
								$GOODS_NO				= trim($arr_rs[$j]["GOODS_NO"]);
								$GOODS_CATE			= SetStringFromDB($arr_rs[$j]["GOODS_CATE"]);
								$GOODS_CODE			= SetStringFromDB($arr_rs[$j]["GOODS_CODE"]);
								$GOODS_NAME			= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
								$GOODS_SUB_NAME	= SetStringFromDB($arr_rs[$j]["GOODS_SUB_NAME"]);
								$CATE_01				= SetStringFromDB($arr_rs[$j]["CATE_01"]);
								$CATE_02				= SetStringFromDB($arr_rs[$j]["CATE_02"]);
								$CATE_03				= SetStringFromDB($arr_rs[$j]["CATE_03"]);
								$CATE_04				= SetStringFromDB($arr_rs[$j]["CATE_04"]);
								$PRICE					= trim($arr_rs[$j]["PRICE"]);
								$BUY_PRICE			= trim($arr_rs[$j]["BUY_PRICE"]);
								$SALE_PRICE			= trim($arr_rs[$j]["SALE_PRICE"]);
								$EXTRA_PRICE		= trim($arr_rs[$j]["EXTRA_PRICE"]);
								$TAX_TF					= trim($arr_rs[$j]["TAX_TF"]);
								$STOCK_CNT			= trim($arr_rs[$j]["STOCK_CNT"]);
								$IMG_URL				= trim($arr_rs[$j]["IMG_URL"]);
								$FILE_NM_150		= trim($arr_rs[$j]["FILE_NM_150"]);
								$FILE_RNM_150		= trim($arr_rs[$j]["FILE_RNM_150"]);
								$FILE_PATH_150	= trim($arr_rs[$j]["FILE_PATH_150"]);
								$FILE_SIZE_150	= trim($arr_rs[$j]["FILE_SIZE_150"]);
								$FILE_EXT_150		= trim($arr_rs[$j]["FILE_EXT_150"]);
								//$OPTION01_NAME	= SetStringFromDB($arr_rs[$j]["OPTION01_NAME"]);
								//$OPTION01_VALUE	= SetStringFromDB($arr_rs[$j]["OPTION01_VALUE"]);
								//$OPTION02_NAME	= SetStringFromDB($arr_rs[$j]["OPTION02_NAME"]);
								//$OPTION02_VALUE	= SetStringFromDB($arr_rs[$j]["OPTION02_VALUE"]);
								$CONTENTS				= trim($arr_rs[$j]["CONTENTS"]);

								$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
								$DEL_TF					= trim($arr_rs[$j]["DEL_TF"]);
								$REG_DATE				= date("Y-m-d",strtotime($REG_DATE));
								
								//echo $GOODS_CATE;
								// 데이터 유효성 검사
								$err_str = "정상";
								
								if ($GOODS_CATE == "") {
									$err_str =  "카테고리 누락,";
								} else {
									if (!chkCateNm($conn, $GOODS_CATE)) {
										$err_str .=  "카테고리 오류,";
									}
								}
								
								if ($GOODS_NAME == "") {
									$err_str .=  "상품명 누락,";
								}

								/*
								if ($CATE_01 == "") {
									$err_str .=  "원산지 누락,";
								}
								*/

								if ($CATE_02 == "") {
									$err_str .=  "제조사 누락,";
								}

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

								if ($PRICE == "") {
									//
								} else {
									if (!is_numeric($PRICE)) {
										$err_str .=  "매입합계(숫자만 가능) 오류,";
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

								if ($EXTRA_PRICE == "") {
									//
								} else {
									if (!is_numeric($EXTRA_PRICE)) {
										$err_str .=  "배송비(숫자만 가능) 오류,";
									}
								}
								
								/*
								if ($STOCK_CNT == "") {
									$err_str .=  "재고 누락,";
								} else {
									if (!is_numeric($STOCK_CNT)) {
										$err_str .=  "재고(숫자만 가능) 오류,";
									}
								}
								*/

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
								<a href="javascript:js_view('<?= $rn ?>','<?= $temp_no ?>','<?= $GOODS_NO ?>');"><?=$err_str?></a>
								<? if ($err_str == "정상") {?>
								<input type="hidden" name="ok[]" value="<?=$GOODS_NO?>">
								<? } ?>
							</td>

							<td class="modeual_nm"><?= getCategoryName($conn, $GOODS_CATE) ?></td>
							<td class="modeual_nm"><?=$GOODS_NAME?></td>
							<td class="modeual_nm"><?=$GOODS_SUB_NAME?></td>
							<td class="modeual_nm"><?=$GOODS_CODE?></td>
							<!--<td class="modeual_nm"><?=$CATE_01?></td>-->
							<td class="modeual_nm"><?=$CATE_02?></td>
							<td class="modeual_nm"><?=getCompanyName($conn, $CATE_03)?></td>
							<!--<td class="price"><?= number_format($STOCK_CNT) ?></td>-->
							<td><?= getDcodeName($conn, "GOODS_STATE", $CATE_04);?></td>
							<td class="price"><?= number_format($PRICE) ?></td>
							<td class="price"><?= number_format($BUY_PRICE) ?></td>
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
							<!--
							<td><?= $OPTION01_NAME ?></td>
							<td class="modeual_nm"><?= $OPTION01_VALUE ?></td>
							<td><?= $OPTION02_NAME ?></td>
							<td class="modeual_nm"><?= $OPTION02_VALUE ?></td>
							-->
							<td class="filedown"><a href="javascript:js_view_html('<?= $temp_no ?>','<?= $GOODS_NO ?>');">상세 보기</a></td>
						</tr>
					<?			
										$err_str = "";
									}
								} else { 
							?> 
								<tr>
									<td align="center" height="50"  colspan="25">데이터가 없습니다. </td>
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