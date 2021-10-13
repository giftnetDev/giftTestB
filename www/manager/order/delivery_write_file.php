<?
ini_set('memory_limit',-1);
session_start();
?>
<?
# =============================================================================
# File Name    : delivery_write_file.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
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
	$menu_right = "OD007"; // 메뉴마다 셋팅 해 주어야 합니다

	if ($start_date == "") {
		$start_date = date("Y-m-d",strtotime("-1 month"));
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));
	} else {
		$end_date = trim($end_date);
	}

	if ($sel_order_state == "") 
		$sel_order_state = "2";

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
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/member/member.php";
	require "../../_classes/biz/payment/payment.php";
#====================================================================
# Request Parameter
#====================================================================
	
	$mode	= trim($mode);
	
#====================================================================
# DML Process
#====================================================================
	if ($mode == "FR") {
	
	#====================================================================
		$savedir1 = $g_physical_path."upload_data/temp_delivery";
	#====================================================================

		$file_nm	= upload($_FILES[file_nm], $savedir1, 10000 , array('xls'));

		//echo $file_nm;
		require_once '../../_excel_reader/Excel/reader.php';
		$data = new Spreadsheet_Excel_Reader();
		$data->setOutputEncoding('euc-kr');
		//$data->read('test.xls');
		$data->read("../../upload_data/temp_delivery/".$file_nm);
		
		error_reporting(E_ALL ^ E_NOTICE);

		for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
			
			if ($file_kind == "S") {

				$order_goods_no		= trim($data->sheets[0]['cells'][$i][1]);
				$reserve_no				= trim($data->sheets[0]['cells'][$i][2]);
				$goods_name				= trim($data->sheets[0]['cells'][$i][3]);
				$qty							= trim($data->sheets[0]['cells'][$i][4]);
				$option						= trim($data->sheets[0]['cells'][$i][5]);
				$r_mem_nm					= trim($data->sheets[0]['cells'][$i][6]);
				$r_phone					= trim($data->sheets[0]['cells'][$i][7]);
				$r_zipcode				= trim($data->sheets[0]['cells'][$i][8]);
				$r_addr						= trim($data->sheets[0]['cells'][$i][9]);
				$delivery_cp			= trim($data->sheets[0]['cells'][$i][10]);
				$delivery_no			= trim($data->sheets[0]['cells'][$i][11]);
				$cp_no						= trim($data->sheets[0]['cells'][$i][12]);
				$buy_cp_no				= trim($data->sheets[0]['cells'][$i][13]);
			
			} else {

				$order_goods_no		= trim($data->sheets[0]['cells'][$i][1]);
				$reserve_no				= trim($data->sheets[0]['cells'][$i][2]);
				$goods_name				= trim($data->sheets[0]['cells'][$i][3]);
				$qty							= trim($data->sheets[0]['cells'][$i][4]);
				$option						= trim($data->sheets[0]['cells'][$i][5]);
				$r_mem_nm					= trim($data->sheets[0]['cells'][$i][9]);
				$r_phone					= trim($data->sheets[0]['cells'][$i][14]);
				$r_zipcode				= trim($data->sheets[0]['cells'][$i][10]);
				$r_addr						= trim($data->sheets[0]['cells'][$i][11]);
				$delivery_cp			= trim($data->sheets[0]['cells'][$i][17]);
				$delivery_no			= trim($data->sheets[0]['cells'][$i][18]);
				$cp_no						= trim($data->sheets[0]['cells'][$i][20]);
				$buy_cp_no				= trim($data->sheets[0]['cells'][$i][21]);

			}



			$goods_name		= str_replace("\"","",$goods_name);

			$delivery_cp	= str_replace("\"","",$delivery_cp);
			$delivery_cp	= trim($delivery_cp);
			
			$delivery_cp = getDcodeCode($conn, 'DELIVERY_CP', $delivery_cp);

			$delivery_no	= str_replace(",","",$delivery_no);
			$delivery_no	= str_replace("\"","",$delivery_no);
			$delivery_no	= str_replace("-","",$delivery_no);
			$delivery_no	= trim($delivery_no);
			
			/*
			echo $option_name_01;
			echo $option_01;
			echo $option_name_02;
			echo $option_02;

			exit;
			*/
			$reserve_no			= SetStringToDB($reserve_no);
			$goods_name			= SetStringToDB($goods_name);
			$qty						= SetStringToDB($qty);
			$option					= SetStringToDB($option);
			$r_mem_nm				= SetStringToDB($r_mem_nm);
			$r_phone				= SetStringToDB($r_phone);
			$r_zipcode			= SetStringToDB($r_zipcode);
			$r_addr					= SetStringToDB($r_addr);
			$delivery_cp		= SetStringToDB($delivery_cp);
			$delivery_no		= SetStringToDB($delivery_no);
			$cp_no					= SetStringToDB($cp_no);
			$buy_cp_no			= SetStringToDB($buy_cp_no);

			$temp_result = insertTempDelivery($conn, $file_nm, $reserve_no, $order_goods_no, $qty, $option, $r_mem_nm, $r_phone, $r_zipcode, $r_addr, $goods_name, $delivery_cp, $delivery_no, $cp_no, $buy_cp_no, $s_adm_no);
			
		}
		
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
		location.href =  'delivery_write_file.php?mode=L&temp_no=<?=$file_nm?>';
</script>
<?
		exit;

	}	
	

	if ($mode == "I") {

		$row_cnt = count($ok);
		
		$str_seq_no = "";

		for ($k = 0; $k < $row_cnt; $k++) {
			$str_seq_no .= "'".$ok[$k]."',";
		}

		$str_seq_no = substr($str_seq_no, 0, (strlen($str_seq_no) -1));
		//echo $str_seq_no;

		$insert_result = insertTempToRealDelivery($conn, $temp_no, $str_seq_no);

		if ($insert_result) {
			$delete_result = deleteTempToRealDelivery($conn, $temp_no, $str_seq_no);
		}

		$mode = "L";

	}

	if ($mode == "D") {

		$row_cnt = count($chk);
		
		for ($k = 0; $k < $row_cnt; $k++) {
		
			$tmp_seq_no = $chk[$k];

			$temp_result = deleteTempDelivery($conn, $temp_no, $tmp_seq_no);
		}
		
		$mode = "L";
	}

	if ($mode == "L") {
		$arr_rs = listTempDelivery($conn, $temp_no);
	}
	
	if ($result) {
?>	
<script language="javascript">
		alert('정상 처리 되었습니다.');
		location.href =  'delivery_list.php';
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
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
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
		frm.action = "delivery_list.php";
		frm.submit();
	}

	// 저장 버튼 클릭 시 
	function js_save(f) {
		
		var file_rname = "<?= $file_rname ?>";
		var frm = document.frm;

		frm.file_kind.value = f;

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

		frm.method = "post";
		frm.action = "delivery_write_file.php";
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

	function js_view(rn, file_nm, seq_no) {
		
		var url = "delivery_modify.php?mode=S&temp_no="+file_nm+"&seq_no="+seq_no;
		NewWindow(url, '송장대량입력', '860', '363', 'YES');
		
	}

	function js_reload() {
		location.href =  'delivery_write_file.php?mode=L&temp_no=<?=$temp_no?>';
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
		frm.action = "delivery_write_file_excel.php";
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
<input type="hidden" name="cp_no" value="">
<input type="hidden" name="file_kind" value="">

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
				<h2>송장 등록</h2>

				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="120" />
					<col width="*" />
					<col width="120" />
					<col width="*" />
					<col width="140" />
					<col width="80" />
				</colgroup>
				<thead>
					<tr>
						<th>주문일 :</th>
						<td>
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="start_date" value="<?=$start_date?>" maxlength="10"/>
							 ~ 
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="end_date" value="<?=$end_date?>" maxlength="10"/>
						</td>
						<th>주문상태 :</th>
						<td colspan="3">
							<?=makeSelectBoxWithCondition($conn,"ORDER_STATE", "sel_order_state","200", "선택하세요.", "", $sel_order_state, " AND DCODE IN ('1', '2', '3', '7', '8') " );?>
						</td>
					</tr>
				</thead>
				<tbody>
					<? if ($s_adm_cp_type == "운영") { ?>
					<tr>
						<th>공급업체 :</th>
						<td>
							<input type="text" class="supplyer" style="width:210px" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'구매',$cp_type)?>" />
							<script>
							$(function() {
						     var cache = {};
								$( ".supplyer" ).autocomplete({
									source: function( request, response ) {
										var term = request.term;
										if ( term in cache ) {
											response(cache[term]);
											return;
										}
						 
										$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('구매'), request, function( data, status, xhr ) {
											cache[term] = data;
											response(data);
										});
									},
									minLength: 2,
									select: function( event, ui ) {
										$(".supplyer").val(ui.item.value);
										$("input[name=cp_type]").val(ui.item.id);
									}
								}).bind( "blur", function( event ) {
									var cp_no = $("input[name=cp_type]").val();
									if(cp_no != '') {
										$.getJSON( "../company/json_company_list.php?cp_no=" + cp_no, function(data) {
											if(data[0].CP_NO == 'undefined') {
												$("input[name=cp_type]").val('');
											} else {
												if(data[0].COMPANY != $(".supplyer").val())
												{

													$(".supplyer").val();
													$("input[name=cp_type]").val('');
												}
											}
										});
									} 
								});
							});
							</script>
							<input type="hidden" name="cp_type" value="<?=$cp_type?>">
						</td>
						<th>판매업체 :</th>
						<td colspan="3">
							<input type="text" class="seller" style="width:210px" name="txt_cp_type2" value="<?=getCompanyAutocompleteTextBox($conn,'판매',$cp_type2)?>" />
							<script>
							$(function() {
						     var cache2 = {};
								$( ".seller" ).autocomplete({
									source: function( request, response ) {
										var term = request.term;
										if ( term in cache2 ) {
											response(cache2[term]);
											return;
										}
						 
										$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('판매'), request, function( data, status, xhr ) {
											cache2[term] = data;
											response(data);
										});
									},
									minLength: 2,
									select: function( event, ui ) {
										$(".seller").val(ui.item.value);
										$("input[name=cp_type2]").val(ui.item.id);
									}
								}).bind( "blur", function( event ) {
									var cp_no = $("input[name=cp_type2]").val();
									if(cp_no != '') {
										$.getJSON( "../company/json_company_list.php?cp_no=" + cp_no, function(data) {
											if(data[0].CP_NO == 'undefined') {
												$("input[name=cp_type2]").val('');
											} else {
												if(data[0].COMPANY != $(".seller").val())
												{

													$(".seller").val();
													$("input[name=cp_type2]").val('');
												}
											}
										});
									} 
								});
							});
							</script>
							<input type="hidden" name="cp_type2" value="<?=$cp_type2?>">
						</td>					
					</tr>
					<? } else { ?>
					<input type="hidden" name="cp_type" value="">
					<input type="hidden" name="cp_type2" value="">
					<? }?>
					<tr>
						<th>정렬 :</th>
						<td>
							<select name="order_field" style="width:84px;">
								<option value="ORDER_DATE" <? if ($order_field == "REG_DATE") echo "selected"; ?> >주문일시</option>
								<option value="O_MEM_NM" <? if ($order_field == "O_MEM_NM") echo "selected"; ?> >주문자명</option>
								<option value="R_MEM_NM" <? if ($order_field == "R_MEM_NM") echo "selected"; ?> >수령자명</option>
					<? if ($s_adm_cp_type == "운영") { ?>
								<option value="TOTAL_BUY_PRICE" <? if ($order_field == "TOTAL_BUY_PRICE") echo "selected"; ?> >총공급가</option>
								<option value="TOTAL_SALE_PRICE" <? if ($order_field == "TOTAL_SALE_PRICE") echo "selected"; ?> >총판매가</option>
								<option value="TOTAL_EXTRA_PRICE" <? if ($order_field == "TOTAL_EXTRA_PRICE") echo "selected"; ?> >총배송비</option>
								<option value="TOTAL_QTY" <? if ($order_field == "TOTAL_QTY") echo "selected"; ?> >총수량</option>
								<option value="TOTAL_DELIVERY_PRICE" <? if ($order_field == "TOTAL_DELIVERY_PRICE") echo "selected"; ?> >추가배송비</option>
								<option value="TOTAL_PRICE" <? if ($order_field == "TOTAL_PRICE") echo "selected"; ?> >합계</option>
								<option value="TOTAL_PLUS_PRICE" <? if ($order_field == "TOTAL_PLUS_PRICE") echo "selected"; ?> >총판매이익</option>
								<option value="LEE" <? if ($order_field == "LEE") echo "selected"; ?> >총판매이익율</option>
					<? } ?>
							</select>&nbsp;&nbsp;
							<input type='radio' class="" name='order_str' value='DESC' <? if (($order_str == "DESC") || ($order_str == "")) echo " checked"; ?> > 오름차순 &nbsp;
							<input type='radio' name='order_str' value='ASC' <? if ($order_str == "ASC") echo " checked"; ?>> 내림차순
						</td>

						<th>검색조건 :</th>
						<td>
							<select name="search_field" style="width:84px;">
								<option value="O.RESERVE_NO" <? if ($search_field == "O.RESERVE_NO") echo "selected"; ?> >주문번호</option>
								<option value="O_MEM_NM" <? if ($search_field == "O_MEM_NM") echo "selected"; ?> >주문자명</option>
								<option value="R_MEM_NM" <? if ($search_field == "R_MEM_NM") echo "selected"; ?> >수령자명</option>
								<option value="GOODS_NAME" <? if ($search_field == "GOODS_NAME") echo "selected"; ?> >상품명</option>
							</select>&nbsp;

							<input type="text" value="<?=$search_str?>" name="search_str" class="txt" />
						</td>
						<th>송장입력용엑셀받기 :</th>
						<td>
							<a href="javascript:js_excel();"> <img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="sp20"></div>

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
								<a href="/_common/download_file.php?file_name=insert_delivery.xls&filename_rnm=insert_delivery.xls&str_path=manager/order/">받기</a>
							</th>
							<td colspan="3">
								<div id="ex_scroll">
								<img src="delivery_example.jpg">
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
					<input type="button" name="cc" value=" 송장 자료로 등록 " class="btntxt" onclick="js_save('S');">&nbsp;
					<input type="button" name="cc" value=" 배송 자료로 등록 " class="btntxt" onclick="js_save('D');">&nbsp;
					<? } ?>
				<? } else {?>
					<? if ($sPageRight_I == "Y") {?>
					<input type="button" name="cc" value=" 송장 자료로 등록 " class="btntxt" onclick="js_save('S');">&nbsp;
					<input type="button" name="cc" value=" 배송 자료로 등록 " class="btntxt" onclick="js_save('D');">&nbsp;
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
				<table cellpadding="0" cellspacing="0" class="rowstable01" style="width:1775px">
					<colgroup>
						<col width="35">
						<col width="180"><!--비고-->
						<col width="80"><!--주문번호-->
						<col width="200"><!--상품명-->
						<col width="50"><!--수량-->
						<col width="150"><!--옵션-->
						<col width="80"><!--수령자명-->
						<col width="120"><!--수령자연락처-->
						<col width="80"><!--우편번호-->
						<col width="250"><!--주소-->
						<col width="150"><!--택배사-->
						<col width="100"><!--송장번호-->
						<col width="150"><!--판매업체명-->
						<col width="150"><!--공급업체명-->
					</colgroup>
					<thead>
						<tr>
							<th>&nbsp;</th>
							<th>비고</th>
							<th>주문번호</th>
							<th>상품명</th>
							<th>수량</th>
							<th>옵션</th>
							<th>수령자명</th>
							<th>수령자연락처</th>
							<th>우편번호</th>
							<th>주소</th>
							<th>택배사</th>
							<th>송장번호</th>
							<th>판매업체명</th>
							<th class='end'>공급업체명</th>
						</tr>
					</thead>
					<tbody>
					<?
						$nCnt = 0;
						
						if (sizeof($arr_rs) > 0) {
							
							for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
								
								//SEQ_NO, RESERVE_NO, ORDER_GOODS_NO, QTY, OPTION, R_MEM_NM, R_PHONE, R_ZIPCODE,
								//R_ADDR, GOODS_NAME, DELIVERY_CP, DELIVERY_NO, CP_NO, BUY_CP_NO, REG_ADM, REG_DATE
								
								//echo $j;

								$SEQ_NO					= trim($arr_rs[$j]["SEQ_NO"]);
								$RESERVE_NO			= trim($arr_rs[$j]["RESERVE_NO"]);
								$ORDER_GOODS_NO	= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
								$QTY						= trim($arr_rs[$j]["QTY"]);
								$OPTION					= SetStringFromDB($arr_rs[$j]["OPTIONS"]);
								$R_MEM_NM				= SetStringFromDB($arr_rs[$j]["R_MEM_NM"]);
								$R_PHONE				= SetStringFromDB($arr_rs[$j]["R_PHONE"]);
								$R_ZIPCODE			= SetStringFromDB($arr_rs[$j]["R_ZIPCODE"]);
								$R_ADDR					= SetStringFromDB($arr_rs[$j]["R_ADDR"]);
								$GOODS_NAME			= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
								$DELIVERY_CP		= SetStringFromDB($arr_rs[$j]["DELIVERY_CP"]);
								$DELIVERY_NO		= SetStringFromDB($arr_rs[$j]["DELIVERY_NO"]);
								$CP_NO					= SetStringFromDB($arr_rs[$j]["CP_NO"]);
								$BUY_CP_NO			= SetStringFromDB($arr_rs[$j]["BUY_CP_NO"]);
								$REG_DATE				= date("Y-m-d",strtotime($REG_DATE));

								// 데이터 유효성 검사
								$err_str = "정상";

								if ($ORDER_GOODS_NO == "") {
									$err_str .=  "주문 일련 번호 누락,";
								} else {
									$arr_rs_order_goods = selectOrderGoods($conn, $ORDER_GOODS_NO);
									if (sizeof($arr_rs_order_goods) <= 0) {
										$err_str .=  "주문 일련 번호 오류,";
									}
								}
								
								// 상품명으로 검색해서 $GOODS_NO 구하기
								/*
								if ($GOODS_NO == "") {
									$GOODS_NO = getGoodsNoAsName($conn, $GOODS_NAME, $ORDER_NO);
								}

								if ($GOODS_NO == "") {
									$err_str .=  "상품번호 누락,";
								} else {
									if (getGoodsNoChk($conn, $GOODS_NO) == "") {
										$err_str .=  "상품번호 오류,";
									}
								}
								*/

								if ($DELIVERY_CP == "") {
									$err_str .=  "택배사 누락,";
								}

								if ($DELIVERY_NO == "") {
									$err_str .=  "송장번호 누락,";
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
								<input type="checkbox" name="chk[]" value="<?=$SEQ_NO?>">
							</td>
							<td class="modeual_nm" style="padding:8px 0 3px 0">
								<a href="javascript:js_view('<?= $rn ?>','<?= $temp_no ?>','<?= $SEQ_NO ?>');"><?=$err_str?></a>
								<? if ($err_str == "정상") {?>
								<input type="hidden" name="ok[]" value="<?=$SEQ_NO?>">
								<? } ?>
							</td>
							<td><?= $RESERVE_NO?></td>
							<td class="modeual_nm"><?=$GOODS_NAME?></td>
							<td><?= $QTY?></td>
							<td class="modeual_nm"><?= $OPTION?></td>
							<td class="modeual_nm"><?=$R_MEM_NM?></td>
							<td class="modeual_nm"><?=$R_PHONE?></td>
							<td><?=$R_ZIPCODE?></td>
							<td class="modeual_nm"><?=$R_ADDR?></td>
							<td><?=$DELIVERY_CP?></td>
							<td><?= $DELIVERY_NO?></td>
							<td class="modeual_nm"><?=$CP_NO?></td>
							<td class="modeual_nm"><?=$BUY_CP_NO?></td>
						</tr>
					<?			
										$err_str = "";
									}
								} else { 
							?> 
								<tr>
									<td align="center" height="50"  colspan="14">데이터가 없습니다. </td>
								</tr>
							<? 
								}
							?>
							</tbody>
						</table>
						</div>


				<div class="btnright">
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