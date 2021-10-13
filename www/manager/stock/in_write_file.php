<?
ini_set('memory_limit',-1);
session_start();
?>
<?
# =============================================================================
# File Name    : in_write_file.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2015-07-04
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
	$menu_right = "SG001"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/stock/stock.php";
	require "../../_classes/biz/goods/goods.php";


?>
<?
	// function get
	function getCpInfoByCpCode($db, $cpCode){
		$query="SELECT CP_NO, CP_NM
				FROM TBL_COMPANY
				WHERE CP_CODE='".$cpCode."' ; ";

		$result=mysql_query($query, $db);
		if($result){
			$rows=mysql_fetch_array($result);

			return $rows;
		}
		return "";
	}
	function getGoodsInfoByGoodsCode($db, $goodsCode){
		$query = " SELECT GOODS_NO, GOODS_NAME
				FROM TBL_GOODS
				WHERE GOODS_CODE='".$goodsCode."' ; ";
		$result=mysql_query($query, $db);
		if($result){
			$rows=mysql_fetch_array($result);
			return $rows;
		}
		return "";
	}
	function insertTempStock2($db, $file_nm, $stock_type, $goods_no, $goods_code, $goods_name,  $str_stock_code, $cp_no, $cp_code, $cp_name, $qty, $price, $in_loc, $in_loc_ext, $in_date, $pay_date, $memo) {

		$query="INSERT INTO TBL_TEMP_STOCK  (TEMP_NO, STOCK_TYPE, STOCK_CODE, GOODS_NO, GOODS_CODE,
																	 GOODS_NAME, CP_NO, CP_CODE, CP_NAME, IN_LOC, IN_LOC_EXT,
																	 QTY, PRICE, IN_DATE, PAY_DATE, MEMO) 
												values ('$file_nm', '$stock_type', '$str_stock_code','$goods_no', '$goods_code', 
																'$goods_name', '$cp_no', '$cp_code', '$cp_name', '$in_loc', '$in_loc_ext', 
																'$qty', '$price', '$in_date', '$pay_date', '$memo' ); ";
		
		// echo $query;

		// exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}
	function listTempStock2($db, $tempNo){
		$query=" SELECT STOCK_NO, STOCK_CODE, GOODS_CODE,GOODS_NAME, CP_NAME, IN_LOC, IN_LOC_EXT, QTY, PRICE, IN_DATE, PAY_DATE, MEMO 
				FROM TBL_TEMP_STOCK
				WHERE TEMP_NO ='".$tempNo."' ";
		$result=mysql_query($query, $db);
		$cnt=mysql_num_rows($result);
		$record=array();
		if($cnt>0){
			for($i=0;$i<$cnt;$i++){
				$record[$i]=mysql_fetch_assoc($result);
			}
			return $record;
		}
		return "";
	}

	function insertTempToRealStock($db, $temp_no, $str_stock_no, $adm_no){
		//$str_stock_no - str_no가 엮여있는 형태 "101, 105, 206" 이런 식으로
		$query="SELECT STOCK_NO, STOCK_TYPE, STOCK_CODE, GOODS_NO, GOODS_CODE, 
					   GOODS_NAME, CP_NO, CP_CODE, CP_NAME, IN_LOC,
					   IN_LOC_EXT, QTY, PRICE, IN_DATE, PAY_DATE, MEMO
				FROM TBL_TEMP_STOCK
				WHERE TEMP_NO = '".$temp_no."' AND STOCK_NO IN (".$str_stock_no.") ; ";

		// echo "in_write_file.php_122 : <br>".$query;
		// exit;

		$result	=	mysql_query($query,$db);
		$record	=	array();



		if($result<>""){
			$cnt = mysql_num_rows($result);

			// echo "cnt : ".$cnt."<br>";
			for($i = 0; $i < $cnt; $i++){
				$record[$i] = mysql_fetch_assoc($result);
			}
			// print_r($record);
			// exit;
			for($j=0; $j<$cnt; $j++){
				echo "j : ".$j."<br>";
				echo "cnt : ".$cnt."<br>";
				$stock_type		=trim($record[$j]["STOCK_TYPE"]); //"IN"
				$stock_code		=trim($record[$j]["STOCK_CODE"]); //"정상입고"
				$goods_no		=trim($record[$j]["GOODS_NO"]);
				$goods_code		=trim($record[$j]["GOODS_CODE"]);
				$goods_name		=trim($record[$j]["GOODS_NAME"]);
				$cp_no			=trim($record[$j]["CP_NO"]);
				$cp_code		=trim($record[$j]["CP_CODE"]);
				$cp_name		=trim($record[$j]["CP_NAME"]);
				$in_loc			=trim($record[$j]["IN_LOC"]);	//"B동"
				$in_loc_ext		=trim($record[$j]["IN_LOC_EXT"]);
				$qty			=trim($record[$j]["QTY"]);
				$price			=trim($record[$j]["PRICE"]);
				$in_date		=trim($record[$j]["IN_DATE"]);
				$pay_date		=trim($record[$j]["PAY_DATE"]);
				$memo			=trim($record[$j]["MEMO"]);

				if($stock_type="IN"){
					$stock_code=getDcodeCode($db, "IN_ST", $stock_code);//$stock_code=의 내용이 정상입고->"NST01"로 변환된다.

					$leftStockCd=left($stock_code,1);

					if($leftStockCd == "N"){
						$in_qty	= $qty;
						$in_bqty=0;
						$in_fqty=0;
					}
					else if($leftStockCd == "B"){
						$in_qty=0;
						$in_bqty=$qty;
						$in_fqty=0;
					}
					else if($leftStockCd == "F"){
						$in_qty=0;
						$in_bqty=0;
						$in_fqty=$qty;
					}
				}


				// $arr_rs_goods=selectGoods($db, $goods_no);

				// $rs_goods_name=trim($arr_rs_goods[0]["GOODS_NAME"]);
				// $rs_price=trim($arr_rs_goods[0]["BUY_PRICE"]);
				// $rs_cp_no=trim($arr_rs_goods[0]["CATE_03"]);// CATE_03->공급사

				$in_loc = getDcodeCode($db, "LOC", $in_loc); //$in_loc의 내용이 "B동"에서 LOCA로 바뀐다.

				$result = insertStock($db, $stock_type, $stock_code, $cp_no, "", $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, "", "", "", $price, "", $in_date, "", $pay_date, "", "", "", "N" ,$adm_no, $memo);


				
			}//end of for($j=0; $j<$cnt);
			if(!$result){
				return false;
				echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
				exit;
			}
			else{
				// echo "success<br>";
				return true;
			}
		}

	}
	
?>
<?


#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);
	
	if ($this_date == "") 
		$this_date = date("Y-m-d",strtotime("0 month")); //지금 DATE

	if ($this_h == "") 
		$this_h = date("H",strtotime("0 month"));	//현재 Hour

	if ($this_i == "") 
		$this_i = date("i",strtotime("0 month")); //현재 Minute

#====================================================================
# DML Process
#====================================================================
	if ($mode == "FR") {
	
	#====================================================================
		$savedir1 = $g_physical_path."upload_data/temp_stock";
	#====================================================================

		$file_nm	= upload($_FILES[file_nm], $savedir1, 10000 , array('xls'));

		//echo $file_nm;
		require_once '../../_excel_reader/Excel/reader.php';
		$data = new Spreadsheet_Excel_Reader();
		$data->setOutputEncoding('euc-kr');
		//$data->read('test.xls');
		$data->read("../../upload_data/temp_stock/".$file_nm);
		
		error_reporting(E_ALL ^ E_NOTICE);

		// echo "dataRows : ".$data->sheets[0]['numRows'];
		// exit;

		for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
			
			$stock_type				= "IN";
			$goods_code				= trim($data->sheets[0]['cells'][$i][1]);
			//2
			$str_stock_code			= trim($data->sheets[0]['cells'][$i][3]);
			$in_cp_code				= trim($data->sheets[0]['cells'][$i][4]);
			$in_qty					= trim($data->sheets[0]['cells'][$i][5]);
			$in_price				= trim($data->sheets[0]['cells'][$i][6]);
			$in_loc					= trim($data->sheets[0]['cells'][$i][7]);
			$in_loc_ext				= trim($data->sheets[0]['cells'][$i][8]);
			$in_date				= trim($data->sheets[0]['cells'][$i][9]);
			$pay_date				= trim($data->sheets[0]['cells'][$i][10]);
			// $reserve_no				= trim($data->sheets[0]['cells'][$i][11]);
			$memo					= trim($data->sheets[0]['cells'][$i][11]);

			// echo "input in_date : $in_date<br>input pay_date : $pay_date<br>";
			// $in_date=strtotime($in_date);
			// $pay_date=strtotime($pay_date);
			// echo "after func in_date : $in_date<br>after func pay_date : $pay_date<br>";
			// exit;

			$goodsInfo=getGoodsInfoByGoodsCode($conn, $goods_code);
			$goods_name=trim($goodsInfo[1]);
			$goods_no=trim($goodsInfo[0]);

			$cpInfo=getCpInfoByCpCode($conn, $in_cp_code);
			$cp_no=$cpInfo[0];
			$cp_name=$cpInfo[1];

			//$use_tf					= "Y";

			$goods_name		= str_replace("\"","",$goods_name);

			$in_price	= str_replace(",","",$in_price);
			$in_price	= str_replace("\"","",$in_price);
			$in_price	= trim($in_price);
			$in_qty		= str_replace(",","",$in_qty);
			$in_qty		= str_replace("\"","",$in_qty);
			$in_qty		= trim($in_qty);

			$goods_code			= SetStringToDB($goods_code);
			$goods_name			= SetStringToDB($goods_name);
			$str_stock_code	= SetStringToDB($str_stock_code);
			$in_cp_code			= SetStringToDB($in_cp_code);
			$cp_name			=	SetStringToDB($cp_name);
			$in_qty					= SetStringToDB($in_qty);
			$in_price				= SetStringToDB($in_price);
			$in_loc					= SetStringToDB($in_loc);
			$in_loc_ext			= SetStringToDB($in_loc_ext);
			$in_date				= SetStringToDB($in_date);
			$pay_date				= SetStringToDB($pay_date);
			$memo					= SetSTringToDB($memo);


			if ($s_adm_cp_type <> "운영") {
				$cp_nm = $s_adm_com_code;
			}
			
			$temp_result = insertTempStock2($conn, $file_nm, $stock_type, $goods_no, $goods_code, $goods_name, $str_stock_code,$cp_no, $in_cp_code, $cp_name, $in_qty, $in_price, $in_loc, $in_loc_ext, $in_date, $pay_date, $memo);
			
		}
		// exit;
		
?>	
<script language="javascript">
		location.href =  'in_write_file.php?mode=L&temp_no=<?=$file_nm?>&this_date=<?=$this_date?>&this_h=<?=$this_h?>&this_i=<?=$this_i?>';
</script>
<?
		exit;

	}	
	

	if ($mode == "I") {

		$row_cnt = count($ok);
		
		$str_stock_no = "";

		for ($k = 0; $k < $row_cnt; $k++) {
			$str_stock_no .= "'".$ok[$k]."',";
		}
		// echo $str_stock_no."<br>";
		// exit;

		$str_stock_no = substr($str_stock_no, 0, (strlen($str_stock_no) -1));

		//$temp_date = $this_date." ".$this_h.":".$this_i.":00";

		$insert_result = insertTempToRealStock($conn, $temp_no, $str_stock_no, $s_adm_no);

		if ($insert_result) {
			$delete_result = deleteTempToRealStock($conn, $temp_no, $str_stock_no);
		}

		$mode = "L";

	}

	if ($mode == "D") {

		$row_cnt = count($chk);
		
		for ($k = 0; $k < $row_cnt; $k++) {
		
			$tmp_stock_no = $chk[$k];

			$temp_result = deleteTempStock($conn, $temp_no, $tmp_stock_no);
		}
		
		$mode = "L";
	}

	if ($mode == "L") {

		// echo "temp_no : ".$temp_no."<br>";
		$arr_rs = listTempStock2($conn, $temp_no);
		// echo "size of tmpStock : ".sizeof($arr_rs);
		// exit;
	}
	
	if ($result) {
?>	
<script language="javascript">
		alert('정상 처리 되었습니다.');
		//location.href =  'in_list.php';
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

/*#pop_table {z-index: 1; left: 80; overflow: auto; width: 500; height: 220}*/
#ex_scroll { z-index: 1; background-color:#f7f7f7; overflow: auto; width: 100%; height:155px; border:1px solid #d1d1d1;}
#temp_scroll { z-index: 1; background-color:#f7f7f7; overflow: auto; width: 95%; height:100%; border:1px solid #d1d1d1;}

</style>

<script language="javascript">
	
	// 조회 버튼 클릭 시 
	function js_list() {
		var frm = document.frm;
		
		frm.method = "get";
		frm.action = "in_list.php";
		frm.submit();
	}

	// 저장 버튼 클릭 시 
	function js_save() {
		
		var file_rname = "<?= $file_rname ?>";
		var frm = document.frm;

		//frm.full_date.value = frm.this_date.value+" "+frm.this_h.value+":"+frm.this_m.value+":00";

		//alert(frm.full_date.value);
		
		if (isNull(frm.file_nm.value)) {
			alert('파일을 선택해 주세요.');
			frm.file_nm.focus();
			return ;		
		}
		
		AllowAttach(frm.file_nm);

		if (isNull(file_rname)) {
			alert("FR mode in js_save()");
			frm.mode.value = "FR";
		} else {
			alert("I mode in js_save()")
			frm.mode.value = "I";
		}

		frm.method = "post";
		frm.action = "in_write_file.php";
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
			alert("입력하신 파일은 업로드 될 수 없습니다!");
			return;
		}
	}

	function js_view(file_nm, stock_no) {
		
		var url = "in_modify.php?mode=S&temp_no="+file_nm+"&stock_no="+stock_no;
		NewWindow(url, ' 입고파일입력', '820', '413', 'YES');
		
	}

	function js_reload() {
		location.href =  'in_write_file.php?mode=L&temp_no=<?=$temp_no?>';
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

	function js_excel() {
		
		var frm = document.frm;

		frm.target = "";
		frm.action = "in_write_file_excel.php";
		frm.submit();

		//alert("자료 출력");
	}

	function js_reg_excel() {

		var frm = document.frm;

		frm.target = "";
		frm.action = "in_write_goods_excel.php";
		frm.submit();

	}
</script>
</head>
<body id="admin">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="temp_no" value="<?=$temp_no?>">
<input type="hidden" name="cp_no" value="">

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
				<h2>입고 등록</h2>
				<table cellpadding="0" cellspacing="0" class="colstable">

					<colgroup>
						<col width="12%">
						<col width="38%">
						<col width="35%">
						<col width="15%">
					</colgroup>
					<thead>
						<tr>
							<th>
								입력 예
								<br><br>
								<a href="/_common/download_file.php?file_name=insert_in_stock.xls&filename_rnm=insert_in_stock.xls&str_path=manager/stock/">받기</a>
							</th>
							<td colspan="3">
								<div id="ex_scroll">
								<img src="sample_in.png">
								</div>
							</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th>파일</th>
							<td colspan="2">
								<input type="file" name="file_nm" style="width:60%;" class="txt">
							</td>
							<td align="right">
								<input type="button" name="bb" value=" 입고등록 용 상품리스트 출력 " class="btntxt" onclick="js_reg_excel();">
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
					<? if ($insert_result) {?>
					* 등록건 <?=$row_cnt?> 건
					<? }?>
				</div>
				<div id="temp_scroll">
				<table cellpadding="0" cellspacing="0" class="rowstable01" style="width:1525px">
					<colgroup>
						<col width="35">
						<col width="200">
						<col width="180">
						<col width="300">
						<col width="90">
						<col width="160">
						<col width="90">
						<col width="90">
						<col width="80">
						<col width="100">
						<col width="100">
						<col width="100">
						<col width="200">
					</colgroup>
					<thead>
						<tr>
							<th>&nbsp;</th>
							<th>비고</th>
							<th>상품코드</th>
							<th>상품명</th>
							<th>입고구분</th>
							<th>입고업체</th>
							<th>입고수량</th>
							<th>매입단가</th>
							<th>입고사유</th>
							<th>사유상세</th>
							<th>입고일</th>
							<th>결제일</th>
							<th class="end">메모</th>
						</tr>
					</thead>
					<tbody>
					<?
						$nCnt = 0;
						
						if (sizeof($arr_rs) > 0) {
							
							for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
								
								// CP_NO, GOODS_NO, GOODS_NAME, QTY , GOODS_OPTION_NM_01, GOODS_OPTION_01, GOODS_OPTION_NM_02, GOODS_OPTION_02,
								// GOODS_OPTION_NM_03, GOODS_OPTION_03, O_NAME, O_PHONE, O_HPHONE, R_NAME, R_PHONE, R_HPHONE, R_ZIPCODE, R_ADDR1,
								// MEMO, ORDER_STATE, SA_DELIVERY, USE_TF, REG_ADM, REG_DATE
								
								//echo $j;

								$rn							= trim($arr_rs[$j]["rn"]);
								$STOCK_NO				= SetStringFromDB($arr_rs[$j]["STOCK_NO"]);
								$STOCK_TYPE			= SetStringFromDB($arr_rs[$j]["STOCK_TYPE"]);
								$STOCK_CODE			= SetStringFromDB($arr_rs[$j]["STOCK_CODE"]);
								$GOODS_CODE			= SetStringFromDB($arr_rs[$j]["GOODS_CODE"]);
								$GOODS_NAME			= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
								$CP_CODE				= SetStringFromDB($arr_rs[$j]["CP_CODE"]);
								$IN_LOC					= SetStringFromDB($arr_rs[$j]["IN_LOC"]);
								$IN_LOC_EXT			= SetStringFromDB($arr_rs[$j]["IN_LOC_EXT"]);
								$QTY						= SetStringFromDB($arr_rs[$j]["QTY"]);
								$PRICE					= SetStringFromDB($arr_rs[$j]["PRICE"]);
								$IN_DATE				= trim($arr_rs[$j]["IN_DATE"]);
								// $PAY_DATE				= trim($arr_rs[$j]["PAY_DATE"]);

								$IN_DATE	= left($IN_DATE,10);
								// $PAY_DATE = left($PAY_DATE,10);

								// 데이터 유효성 검사
								$err_str = "정상";

								// 상품명으로 검색해서 $GOODS_CODE 구하기
								if ($GOODS_CODE == "") {
									$GOODS_NO = getStockGoodsNoAsName($conn, $GOODS_NAME, $STOCK_NO);
								} else {
									$GOODS_NO = getStockGoodsNoAsCode($conn, $GOODS_CODE, $GOODS_NAME, $STOCK_NO);
								}
								
								if ($GOODS_NO == "") {
									$err_str .=  "상품코드 누락,";
								} else {
									if (getGoodsNoChk($conn, $GOODS_NO) == false) {
										$err_str .=  "상품코드 오류,";
									} else {
										$goods_rs = selectGoods($conn, $GOODS_NO);

										$GOODS_NAME = trim($goods_rs[0]["GOODS_NAME"]); 
										$RS_PRICE		= trim($goods_rs[0]["BUY_PRICE"]); 
										$RS_CP_NO		= trim($goods_rs[0]["CATE_03"]); 

									}
								}

								//echo $RS_PRICE."<br>";
								//echo $RS_CP_NO."<br>";
								
								if ($STOCK_CODE == "") {
									$err_str .=  "입고구분 누락,";
								} else {
									if (getStcokCodeChk($conn, "IN", $STOCK_CODE) == false) {
										$err_str .=  "입고구분 오류,";
									}
								}

								if ($CP_CODE == "") {
									if ($RS_CP_NO == "") {
										$err_str .=  "업체 누락,";
									} else {
										$CP_NO = $RS_CP_NO;
									}
								} else {
									$CP_NO = getStockCompayChkAsCode($conn, $s_adm_cp_type, $CP_CODE, $STOCK_NO);
									if ($CP_NO == "") {
										$err_str .=  "업체 오류,";
									}
								}
								

								if ($IN_LOC == "") {
									$err_str .=  "사유 누락,";
								} else {
									if (getLocChkAsName($conn, $IN_LOC) == false) {
										$err_str .=  "사유 오류,";
									}
								}

								if ($QTY == "") {
									$err_str .=  "수량 누락,";
								} else {
									if ($QTY  < "1") {
										//$err_str .=  "수량 오류,";
									}
								}
								
								//echo $PRICE;

								if ($PRICE == "") {
									if ($RS_PRICE == "") {
										$err_str .=  "매입단가 누락,";
									} else {
										//echo $RS_PRICE."<br>";
										$PRICE = $RS_PRICE;
									}
								}
								// echo"입고일 : ".$IN_DATE."<br>";

								if ($IN_DATE == "") {
									$err_str .=  "입고일 누락,";
								} else {
									if (chkDate($IN_DATE, "YYYY-MM-DD") == false) {
										$err_str .=  "입고일 오류,";
									}
								}

								$PAY_DATE=date("Y-m-d");
								if ($PAY_DATE == "") {
									$err_str .=  "결제일 누락,";
									
									
									echo"결제일 : ".$PAY_DATE."<br>";
								} else {
									if (chkDate($PAY_DATE, "YYYY-MM-DD") == false) {
										$err_str .=  "결제일 오류,";
									}
								}

								if ($err_str <> "정상") {
									$err_str = str_replace("정상","",$err_str);
									$err_str = substr($err_str, 0, (strlen($err_str) -1));
									$err_str = str_replace(",","<div class='sp5'></div>",$err_str);
									$err_str = "<font color='red'>".$err_str."</font>";
								}
								
					?>
						<tr>
							<td class="filedown">
								<input type="checkbox" name="chk[]" value="<?=$STOCK_NO?>">
							</td>
							<td class="modeual_nm" style="padding:8px 0 3px 0"><!--비고-->
								<a href="javascript:js_view('<?= $temp_no ?>','<?= $STOCK_NO ?>');"><?=$err_str?></a>
								<? if ($err_str == "정상") {?>
								<input type="hidden" name="ok[]" value="<?=$STOCK_NO?>">
								<? } ?>
							</td>
							<td><?= $GOODS_CODE?></td><!--상품코드-->
							<td class="modeual_nm"><?= $GOODS_NAME?></td><!--상품명-->
							<td><?= $STOCK_CODE?></td><!--입고구분-->
							<td class="modeual_nm"><?=getCompanyName($conn, $CP_NO)?></td><!--입고업체-->
							<td>
								<? if ($QTY) echo number_format($QTY)?>
							</td><!--입고수량-->
							<td>
								<? if ($PRICE) echo number_format($PRICE)?>
							</td><!--매입단가-->
							<td><?= $IN_LOC?></td><!--입고사유-->
							<td><?= $IN_LOC_EXT?></td><!--사유상세-->
							<td><?= $IN_DATE?></td><!--입고일-->
							<td><?= $PAY_DATE?></td><!--결제일-->
							<td><?= $MEMO?></td><!--메모-->
						</tr>
					<?			
										$err_str = "";
									}//end of for($j ; arr_rs);
								} else { 
							?> 
								<tr>
									<td align="center" height="50"  colspan="12">데이터가 없습니다. </td>
								</tr>
							<? 
								}
							?>
							</tbody>
						</table>
						</div>


				<div class="btnright">
					<input type="button" name="aa" value=" 미등록자료 엑셀받기 " class="btntxt" onclick="js_excel();">&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="button" name="bb" value=" 정상자료 등록 " class="btntxt" onclick="js_register();">&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="button" name="cc" value=" 선택자료 삭제 " class="btntxt" onclick="js_delete();">
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