<?
ini_set('memory_limit',-1);
session_start();
?>
<?
# =============================================================================
# File Name    : order_write_file.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright    : Copyright @Orion Corp. All Rights Reserved.
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

#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);
	
	if ($this_date == "") 
		$this_date = date("Y-m-d",strtotime("0 month"));

	if ($this_h == "") 
		$this_h = date("H",strtotime("0 month"));

	if ($this_i == "") 
		$this_i = date("i",strtotime("0 month"));

#====================================================================
# DML Process
#====================================================================
	if ($mode == "FR") {
	
	#====================================================================
		$savedir1 = $g_physical_path."upload_data/temp_st_order";
	#====================================================================

		$file_nm	= upload($_FILES[file_nm], $savedir1, 10000 , array('xls'));

		//echo $file_nm;
		require_once '../../_excel_reader/Excel/reader.php';
		$data = new Spreadsheet_Excel_Reader();
		$data->setOutputEncoding('euc-kr');
		//$data->read('test.xls');
		$data->read("../../upload_data/temp_st_order/".$file_nm);
		
		error_reporting(E_ALL ^ E_NOTICE);

		for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
			
			$buy_cp_no			= trim($data->sheets[0]['cells'][$i][1]);
			$goods_no				= trim($data->sheets[0]['cells'][$i][2]);
			$goods_name			= trim($data->sheets[0]['cells'][$i][3]);
			$buy_price			= trim($data->sheets[0]['cells'][$i][4]);
			$qty						= trim($data->sheets[0]['cells'][$i][5]);
			$option_name_01	= trim($data->sheets[0]['cells'][$i][6]);
			$option_01			= trim($data->sheets[0]['cells'][$i][7]);
			$option_name_02	= trim($data->sheets[0]['cells'][$i][8]);
			$option_02			= trim($data->sheets[0]['cells'][$i][9]);
			$option_name_03	= trim($data->sheets[0]['cells'][$i][10]);
			$option_03			= trim($data->sheets[0]['cells'][$i][11]);
			$pay_date				= trim($data->sheets[0]['cells'][$i][12]);
			$use_tf					= "Y";

			$goods_name		= str_replace("\"","",$goods_name);

			$buy_price		= str_replace(",","",$buy_price);
			$buy_price		= str_replace("\"","",$buy_price);
			$buy_price		= trim($buy_price);
			$qty					= str_replace(",","",$qty);
			$qty					= str_replace("\"","",$qty);
			$qty					= trim($qty);
			
			$goods_no				= SetStringToDB($goods_no);
			$goods_name			= SetStringToDB($goods_name);
			$option_name_01	= SetStringToDB($option_name_01);
			$option_01			= SetStringToDB($option_01);
			$option_name_02	= SetStringToDB($option_name_02);
			$option_02			= SetStringToDB($option_02);
			$option_name_03	= SetStringToDB($option_name_03);
			$option_03			= SetStringToDB($option_03);
			
			$order_state = "1";

			//echo $full_date;
			$full_date = $this_date." ".$this_h.":".$this_i.":00";
			
			//echo "DATE".$pay_date;
			
			$temp_result = insertTempStOrder($conn, $file_nm, $buy_cp_no, $goods_no, $goods_code, $qty, $goods_name, $goods_sub_name, $option_01, $option_02, $option_03, $option_04, $option_name_01, $option_name_02, $option_name_03, $option_name_04, $buy_price, $order_state, $full_date, $pay_date, $use_tf, $s_adm_no);
			
		}
		
?>	
<script language="javascript">
		location.href =  'order_write_file.php?mode=L&temp_no=<?=$file_nm?>&this_date=<?=$this_date?>&this_h=<?=$this_h?>&this_i=<?=$this_i?>';
</script>
<?
		exit;

	}	
	

	if ($mode == "I") {

		$row_cnt = count($ok);
		
		$str_order_goods_no = "";

		for ($k = 0; $k < $row_cnt; $k++) {
			$str_order_goods_no .= "'".$ok[$k]."',";
		}

		$str_order_goods_no = substr($str_order_goods_no, 0, (strlen($str_order_goods_no) -1));
		//echo $str_cp_no;

		$temp_date = $this_date." ".$this_h.":".$this_i.":00";
		$insert_result = insertTempToRealStOrder($conn, $temp_no, $str_order_goods_no);

		if ($insert_result) {
			$delete_result = deleteTempToRealStOrder($conn, $temp_no, $str_order_goods_no);
		}

		$mode = "L";

	}

	if ($mode == "D") {

		$row_cnt = count($chk);
		
		for ($k = 0; $k < $row_cnt; $k++) {
		
			$tmp_order_goods_no = $chk[$k];

			$temp_result = deleteTempStOrder($conn, $temp_no, $tmp_order_goods_no);
		}
		
		$mode = "L";
	}

	if ($mode == "L") {
		$arr_rs = listTempStOrder($conn, $temp_no);
	}
	
	if ($result) {
?>	
<script language="javascript">
		alert('정상 처리 되었습니다.');
		location.href =  'st_order_list.php';
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
		frm.action = "st_order_list.php";
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
			frm.mode.value = "FR";
		} else {
			frm.mode.value = "I";
		}
		
		//alert(frm.full_date.value);
		frm.method = "post";
		frm.action = "order_write_file.php";
		frm.submit();
	}

	//우편번호 찾기
	function js_post(zip, addr) {
		var url = "/_common/common_post.php?zip="+zip+"&addr="+addr;
		NewWindow(url, '우편번호찾기', '390', '370', 'NO');
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

	function js_view(rn, file_nm, order_no) {
		
		var url = "st_order_modify.php?mode=S&temp_no="+file_nm+"&order_no="+order_no;
		NewWindow(url, '입고등록입력', '820', '420', 'YES');
		
	}

	function js_reload() {
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
		frm.action = "order_write_file_excel.php";
		frm.submit();

		//alert("자료 출력");
	}

</script>
</head>
<body id="admin">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="temp_no" value="<?=$temp_no?>">

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
						<col width="12%">
						<col width="38%">
					</colgroup>
					<thead>
						<tr>
							<th>
								입력 예
								<br><br>
								<a href="/_common/download_file.php?file_name=insert_order.xls&filename_rnm=insert_example.xls&str_path=manager/stock/">받기</a>
							</th>
							<td colspan="3">
								<div id="ex_scroll">
								<img src="order_example.jpg">
								</div>
							</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th>파일</th>
							<td><input type="file" name="file_nm" style="width:60%;" class="txt"></td>
							<th>입고일</th>
							<td>
								<input type="text" class="txt" style="width: 75px;" name="this_date" value="<?=$this_date?>" maxlength="10" readonly="1" />
								<a href="javascript:show_calendar('document.frm.this_date', document.frm.this_date.value);" onFocus="blur();"><!--
								--><img src="/manager/images/bu/ic_calendar.gif" alt="" /></a>&nbsp;
								<select name="this_h">
								<?
									for ($h = 1 ; $h < 25 ; $h++) { 

										$str_h = right("0".$h,2);
								?>
									<option value="<?=$str_h?>" <? if (trim($str_h) == trim($this_h)) echo "selected"; ?>><?=$str_h?></option>
								<?
									}
								?>
								</select> 시 &nbsp;
								<select name="this_i">
								<?
									for ($h = 1 ; $h < 61 ; $h++) { 

										$str_h = right("0".$h,2);
								?>
									<option value="<?=$str_h?>" <? if (trim($str_h) == trim($this_i)) echo "selected"; ?>><?=$str_h?></option> 
								<?
									}
								?>
								</select> 분
								<input type="hidden" name="full_date" value="<?=$full_date?>">
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
				<table cellpadding="0" cellspacing="0" class="rowstable01" style="width:1725px">
					<colgroup>
						<col width="35">
						<col width="270">
						<col width="90">
						<col width="80">
						<col width="300">
						<col width="100">
						<col width="90">
						<col width="90">
						<col width="90">
						<col width="90">
						<col width="90">
						<col width="80">
						<col width="100">
						<col width="100">
						<col width="100">
						<col width="100">
					</colgroup>
					<thead>
						<tr>
							<th>&nbsp;</th>
							<th>비고</th>
							<th>공급업체</th>
							<th>상품코드</th>
							<th>상품명</th>
							<th>상태</th>
							<th>입고가</th>
							<th>공급가(DB)</th>
							<th>수량</th>
							<th>옵션명1</th>
							<th>옵션1</th>
							<th>옵션명2</th>
							<th>옵션2</th>
							<th>옵션명3</th>
							<th>옵션3</th>
							<th class="end">결제일</th>
						</tr>
					</thead>
					<tbody>
					<?
						$nCnt = 0;
						
						if (sizeof($arr_rs) > 0) {
							
							for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
								
								$rn							= trim($arr_rs[$j]["rn"]);
								$ORDER_GOODS_NO	= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
								$BUY_CP_NO			= trim($arr_rs[$j]["BUY_CP_NO"]);
								$GOODS_NO				= SetStringFromDB($arr_rs[$j]["GOODS_NO"]);
								$GOODS_NAME			= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
								$BUY_PRICE			= trim($arr_rs[$j]["BUY_PRICE"]);
								$QTY						= trim($arr_rs[$j]["QTY"]);
								$OPTION_NAME_01	= SetStringFromDB($arr_rs[$j]["GOODS_OPTION_NM_01"]);
								$OPTION_01			= SetStringFromDB($arr_rs[$j]["GOODS_OPTION_01"]);
								$OPTION_NAME_02	= SetStringFromDB($arr_rs[$j]["GOODS_OPTION_NM_02"]);
								$OPTION_02			= SetStringFromDB($arr_rs[$j]["GOODS_OPTION_02"]);
								$OPTION_NAME_03	= SetStringFromDB($arr_rs[$j]["GOODS_OPTION_NM_03"]);
								$OPTION_03			= SetStringFromDB($arr_rs[$j]["GOODS_OPTION_03"]);
								$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
								$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);
								$PAY_DATE				= trim($arr_rs[$j]["PAY_DATE"]);
								$REG_DATE				= date("Y-m-d",strtotime($REG_DATE));
								//$PAY_DATE				= date("Y-m-d",strtotime($PAY_DATE));

								// 데이터 유효성 검사
								$err_str = "정상";

								if ($BUY_CP_NO == "") {
									$err_str .=  "공급업체 누락,";
								} else {
									if (getCompanyChk($conn, $BUY_CP_NO) == "") {
										$err_str .=  "공급업체 오류,";
									}
								}

								// 상품명으로 검색해서 $GOODS_NO 구하기
								if ($GOODS_NO == "") {
									$GOODS_NO = getStockGoodsNoAsName($conn, $GOODS_NAME, $ORDER_GOODS_NO);
								}

								if ($GOODS_NO == "") {
									$err_str .=  "상품번호 누락,";
								} else {
									if (getGoodsNoChk($conn, $GOODS_NO) == "") {
										$err_str .=  "상품번호 오류,";
									}
								}
								
								$arr_rs_goods = selectGoods($conn, $GOODS_NO);
								$rs_buy_price			= trim($arr_rs_goods[0]["BUY_PRICE"]);
								//$BUY_CP_NO				= trim($arr_rs_goods[0]["CATE_03"]);
								$rs_goods_state			= trim($arr_rs_goods[0]["CATE_04"]);

								if ($rs_goods_state <> "판매중") {
									$str_goods_state = "<font color='red'>".getDcodeName($conn, "GOODS_STATE", $rs_goods_state)."</font>";
								} else {
									$str_goods_state = getDcodeName($conn, "GOODS_STATE", $rs_goods_state);
								}

								if ($BUY_PRICE <> $rs_buy_price ) {
									$str_price = "<font color='red'>".number_format($BUY_PRICE)."</font>";
								} else {
									$str_price = number_format($BUY_PRICE);
								}
										
								if ($QTY == "") {
									$err_str .=  "수량 누락,";
								} else {
									//if ($QTY  < "1") {
									//	$err_str .=  "수량 오류,";
									//}
								}

								if ($PAY_DATE == "") {
									$err_str .=  "결제일 누락,";
								} else {
									if (chkDate($PAY_DATE, "YYYY-MM-DD")) {
									} else {
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
								<input type="checkbox" name="chk[]" value="<?=$ORDER_GOODS_NO?>">
							</td>
							<td class="modeual_nm" style="padding:8px 0 3px 0">
								<a href="javascript:js_view('<?= $rn ?>','<?= $temp_no ?>','<?= $ORDER_GOODS_NO ?>');"><?=$err_str?></a>
								<? if ($err_str == "정상") {?>
								<input type="hidden" name="ok[]" value="<?=$ORDER_GOODS_NO?>">
								<? } ?>
							</td>
							<td><?= getCompanyName($conn,$BUY_CP_NO)?></td>
							<td><?= $GOODS_NO?></td>
							<td class="modeual_nm"><?= $GOODS_NAME?></td>
							<td><?=$str_goods_state?></td>
							<td><?= $str_price?></td>
							<td><?= number_format($rs_buy_price)?></td>
							<td><?= number_format($QTY)?></td>
							<td><?= $OPTION_NAME_01?></td>
							<td><?= $OPTION_01?></td>
							<td><?= $OPTION_NAME_02?></td>
							<td><?= $OPTION_02?></td>
							<td><?= $OPTION_NAME_03?></td>
							<td><?= $OPTION_03?></td>
							<td><?= $PAY_DATE?></td>
						</tr>
					<?			
										$err_str = "";
									}
								} else { 
							?> 
								<tr>
									<td align="center" height="50"  colspan="16">데이터가 없습니다. </td>
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