<?
ini_set('memory_limit',-1);
session_start();
?>
<?

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "OD003"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/company/company.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/member/member.php";
	require "../../_classes/biz/admin/admin.php";
	require "../../_classes/biz/payment/payment.php";
#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);
	
	if ($this_date == "") 
		$this_date = date("Y-m-d H:i:s",strtotime("0 month"));
#====================================================================
# DML Process
#====================================================================
	if ($mode == "FR") {
	
	#====================================================================
		$savedir1 = $g_physical_path."upload_data/temp_order";
	#====================================================================

		require_once "../../_PHPExcel/Classes/PHPExcel.php";

		$file_nm	= upload($_FILES[file_nm], $savedir1, 10000 , array('xls'));

		//echo $file_nm;
		require_once '../../_excel_reader/Excel/reader.php';
		$data = new Spreadsheet_Excel_Reader();
		$data->setOutputEncoding('euc-kr');
		//$data->read('test.xls');
		$data->read("../../upload_data/temp_order/".$file_nm);

		$temp_no = $file_nm;
		
		error_reporting(E_ALL ^ E_NOTICE);
		
		for($i = 2 ; $i <= $data->sheets[0]['numRows']; $i++) {

			$order_date	        = SetStringToDB(trim($data->sheets[0]['cells'][$i][1]));
			$cp_order_no        = SetStringToDB(trim($data->sheets[0]['cells'][$i][2]));
			$cp_code            = SetStringToDB(trim($data->sheets[0]['cells'][$i][3]));
			$goods_code         = SetStringToDB(trim($data->sheets[0]['cells'][$i][4]));
			$goods_name         = SetStringToDB(trim($data->sheets[0]['cells'][$i][5]));
			$goods_price		= SetStringToDB(trim($data->sheets[0]['cells'][$i][6]));
			$qty				= SetStringToDB(trim($data->sheets[0]['cells'][$i][7]));
			$o_name				= SetStringToDB(trim($data->sheets[0]['cells'][$i][8]));
			$o_phone			= SetStringToDB(trim($data->sheets[0]['cells'][$i][9]));
			$o_hphone		    = SetStringToDB(trim($data->sheets[0]['cells'][$i][10]));
			$r_name				= SetStringToDB(trim($data->sheets[0]['cells'][$i][11]));
			$r_phone			= SetStringToDB(trim($data->sheets[0]['cells'][$i][12]));
			$r_hphone			= SetStringToDB(trim($data->sheets[0]['cells'][$i][13]));
			$r_zipcode			= SetStringToDB(trim($data->sheets[0]['cells'][$i][14]));
			$r_addr1			= SetStringToDB(trim($data->sheets[0]['cells'][$i][15]));
			$memo				= SetStringToDB(trim($data->sheets[0]['cells'][$i][16]));
			$opt_wrap_code		= SetStringToDB(trim($data->sheets[0]['cells'][$i][17]));
			$opt_sticker_code	= SetStringToDB(trim($data->sheets[0]['cells'][$i][18]));
			$opt_sticker_msg	= SetStringToDB(trim($data->sheets[0]['cells'][$i][19]));
			$opt_print_msg		= SetStringToDB(trim($data->sheets[0]['cells'][$i][20]));
			$opt_outbox_tf		= SetStringToDB(trim($data->sheets[0]['cells'][$i][21]));
			$opt_manager_name	= SetStringToDB(trim($data->sheets[0]['cells'][$i][22]));
			$opt_outstock_date	= SetStringToDB(trim($data->sheets[0]['cells'][$i][23]));
			$delivery_type		= SetStringToDB(trim($data->sheets[0]['cells'][$i][24]));
			$delivery_price		= SetStringToDB(trim($data->sheets[0]['cells'][$i][25]));
			$opt_memo			= SetStringToDB(trim($data->sheets[0]['cells'][$i][26]));
			$delivery_cp		= SetStringToDB(trim($data->sheets[0]['cells'][$i][27]));
			$sender_nm			= SetStringToDB(trim($data->sheets[0]['cells'][$i][28]));
			$sender_phone		= SetStringToDB(trim($data->sheets[0]['cells'][$i][29]));
			$delivery_cnt_in_box= SetStringToDB(trim($data->sheets[0]['cells'][$i][30]));

			$order_state        = '1';
			$use_tf				= 'Y';
			$reg_adm			= $s_adm_no;
			$order_seq = 1;

			$goods_price = str_replace(",", "", $goods_price);
			$qty = str_replace(",", "", $qty);

			$goods_no = tryGoodNoByGoodsCode($conn, $goods_code);
			$goods_name = getGoodsName($conn, $goods_no);
			$cp_no	  = tryCompanyNoByCompanyCode($conn, $cp_code);

			if($opt_sticker_code == "스티커 없음" || $opt_sticker_code == "선택 안 함"){
				$opt_sticker_code ="";
			}

			if($opt_sticker_code <> "")
				$opt_sticker_no	  = tryGoodNoByGoodsCode($conn, $opt_sticker_code);
			else 
				$opt_sticker_no	  = "";

			if($opt_wrap_code <> "")
				$opt_wrap_no	  = tryGoodNoByGoodsCode($conn, $opt_wrap_code);	
			else 
				$opt_wrap_no = "";

			if($opt_manager_name <> "")
				$opt_manager_no	= tryAdminNoByName($conn, $opt_manager_name);	
			else 
				$opt_manager_no = tryAdminNoByName($conn, "기타"); //기본으로 기타로 세팅

			$inserted_order_no = insertTempOrder($conn, $temp_no, $cp_no, $o_name, $o_phone, $o_hphone, $r_name, $r_phone, $r_hphone, $r_zipcode, $r_addr1, $memo, $order_state, $cp_order_no, $opt_manager_no, $opt_manager_name, $use_tf, $reg_adm);

			if($delivery_type <> "") { 
				$delivery_type_code = getDcodeCode($conn, "DELIVERY_TYPE", $delivery_type);
				if($delivery_type_code == "&nbsp;")
					$delivery_type_code = "";
			}
			else 
				$delivery_type_code = "";

			//$opt_outbox_tf = getDcodeCode($conn, "OUTBOX_STICKER_TF", $opt_outbox_tf);
			//if($opt_outbox_tf == "")
			//	$opt_outbox_tf = "N";

			if(is_numeric($order_date))
				$order_date = PHPExcel_Style_NumberFormat::toFormattedString($order_date, PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);


			if(is_numeric($opt_outstock_date))
				$opt_outstock_date = PHPExcel_Style_NumberFormat::toFormattedString($opt_outstock_date, PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);


			$opt_outstock_date = date("Y-m-d",strtotime($opt_outstock_date));

			$arr_op_cp = getOperatingCompany($conn, $s_adm_com_code);
			$OP_CP_NM		= $arr_op_cp[0]["CP_NM"];
			$OP_CP_PHONE	= $arr_op_cp[0]["CP_PHONE"];

			if($sender_nm == "")
				$sender_nm = $OP_CP_NM;

			if($sender_phone == "")
				$sender_phone = $OP_CP_PHONE;
		
			insertTempOrderGoods($conn, $temp_no, $order_date, $inserted_order_no, $order_seq, $goods_no, $goods_code, $goods_name, $goods_price, $qty,$opt_sticker_no, $opt_sticker_code, $opt_sticker_msg, $opt_outbox_tf, $opt_wrap_no, $opt_wrap_code, $opt_print_msg, $opt_outstock_date, $opt_memo, $delivery_type_code, $delivery_type, $delivery_price, $delivery_cp, $sender_nm, $sender_phone, $delivery_cnt_in_box);

		}

?>	
<script language="javascript">
		location.href =  '<?=$_SERVER[PHP_SELF]?>?mode=L&temp_no=<?=$file_nm?>&this_date=<?=$this_date?>';
</script>
<?
		exit;

	}	
	

	if ($mode == "I") {

		$row_cnt = count($ok);
		
		$str_order_no = "";

		for ($k = 0; $k < $row_cnt; $k++) {
			$str_order_no .= "'".$ok[$k]."',";
		}

		$str_order_no = substr($str_order_no, 0, (strlen($str_order_no) -1));
		//echo $str_cp_no;

		//은행권 주문이므로 합포 안함
		$is_package = "N";
		$insert_result = insertTempToRealOrderWithDate($conn, $temp_no, $str_order_no, $this_date, $is_package);

		if ($insert_result) {
			$delete_result = deleteTempToRealOrder($conn, $temp_no, $str_order_no);
		}

		$mode = "L";

	}

	if ($mode == "D") {

		$row_cnt = count($chk);
		
		for ($k = 0; $k < $row_cnt; $k++) {
		
			$tmp_order_no = $chk[$k];

			$temp_result = deleteTempOrder($conn, $temp_no, $tmp_order_no);
		}
		
		$mode = "L";
	}

	if ($mode == "L") {
		$arr_rs = listTempOrder($conn, $temp_no);
	}
	
	if ($result) {
?>	
<script language="javascript">
		alert('정상 처리 되었습니다.');
		location.href =  'order_list.php';
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
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script type="text/javascript" src="../jquery/jquery-ui-timepicker-addon.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui-timepicker-addon.css" type="text/css" />
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script>
<script>
  $(function() {
   /*
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

  */

	 $('.datepicker').datetimepicker({
	   	  dateFormat: "yy-mm-dd", 
		  timeFormat: "HH:mm:ss",
		  buttonImage: "/manager/images/calendar/cal.gif",
          buttonImageOnly: true,
          buttonText: "Select date",
     	  showOn: "both",
	      changeMonth: true,
	      changeYear: true
     });
  });
</script>
<style type="text/css">
#ex_scroll { z-index: 1; background-color:#f7f7f7; overflow: auto; width: 100%; height:155px; border:1px solid #d1d1d1;}
#temp_scroll { z-index: 1; background-color:#f7f7f7; overflow: auto; width: 95%; height:500px; border:1px solid #d1d1d1;}
</style>

<script language="javascript">
	
	// 조회 버튼 클릭 시 
	function js_list() {
		var frm = document.frm;
		
		frm.method = "get";
		frm.action = "order_list.php";
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
		
		//AllowAttach(frm.file_nm);

		if (isNull(file_rname)) {
			frm.mode.value = "FR";
		} else {
			frm.mode.value = "I";
		}

		frm.method = "post";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
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

	function js_view(rn, file_nm, order_no, type) {
		
		if(type == "order") { 
			var url = "order_modify.php?mode=S&temp_no="+file_nm+"&order_no="+order_no;
			NewWindow(url, '주문대량입력', '860', '513', 'YES');
		} else {
			var url = "order_modify_goods_add.php?mode=S&temp_no="+file_nm+"&order_no="+order_no;
			NewWindow(url, '주문대량상품입력', '860', '513', 'YES');
		}

		
	}

	function js_reload() {
		location.href =  '<?=$_SERVER[PHP_SELF]?>?mode=L&temp_no=<?=$temp_no?>';
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

	function js_unregistered_goods_excel() {
		
		var frm = document.frm;

		frm.target = "";
		
		frm.action = "order_write_file_excel_unregistered_goods.php";
		frm.submit();

		//alert("자료 출력");
	}

	function js_excel() {
		
		var frm = document.frm;

		frm.target = "";
		
		frm.action = "order_write_file_excel_mart.php";
		frm.submit();

		//alert("자료 출력");
	}

	function js_temp_goods_excel() {
		
		var frm = document.frm;

		frm.target = "";
		frm.action = "order_write_file_temp_goods_excel.php";
		frm.submit();

	}

	function js_open_pop_MRO() {
		
		var url = "pop_order_MRO_conversion.php";
		NewWindow(url, 'MRO변환', '950', '513', 'YES');
		
	}

	function js_open_pop_EZWELL() { 

		var url = "pop_order_ezwell_conversion.php";
		NewWindow(url, '이지웰변환', '950', '513', 'YES');
		
	}
	function js_change_option(orderNo){
		
	}
</script>
</head>
<body id="admin">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="temp_no" value="<?=$temp_no?>">
<input type="hidden" name="cp_no" value="<?=$args_cp_no?>">

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
<style>
	table.rowstable04 { border-top: none; }
	table.rowstable04 > th { padding: 9px 0 8px 0; font-weight: normal; color: #86a4b2; border-top: 1px solid #d2dfe5; background: #ebf3f6 url('../images/admin/bg_bar_01.gif') right center no-repeat; }
	table.rowstable04 > th.end { background: #ebf3f6; }
	table.rowstable04 td { color: #555555; text-align: center; vertical-align: middle; background: none; }

</style>
		</td>
		<td class="contentarea">

			<!-- S: mwidthwrap -->
			<div id="mwidthwrap">
				<h2>주문 등록 - 통합</h2>
				<div class="btnright">
					<input type="button" name="cc" value=" MRO 변환 " class="btntxt" onclick="js_open_pop_MRO();">&nbsp;&nbsp;
					<input type="button" name="cc" value=" 이지웰 변환 " class="btntxt" onclick="js_open_pop_EZWELL();">
				</div>
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
								<a href="/_common/download_file.php?file_name=insert_order.xls&filename_rnm=insert_example.xls&str_path=manager/order/">받기</a>
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
							<th>주문일</th>
							<td>
								<input type="text" class="txt datepicker" style="width: 150px; margin-right:3px;" name="this_date" value="<?=$this_date?>" maxlength="10"/>
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
					* 전체 <?=totalCntTempOrder($conn, $temp_no)?> 주문건수 중 &nbsp;&nbsp;
					* 주문번호별 <?=sizeof($arr_rs)?> 건 &nbsp;&nbsp;
					<? if ($insert_result) {?>
					* 등록건 <?=$row_cnt?> 건
					<? }?>
				</div>
				<div id="temp_scroll">
				<table cellpadding="0" cellspacing="0" class="rowstable01" style="width:1130px">
					<colgroup>
						<col width="15">
						<col width="80">
						<col width="60">
						<col width="80">
						<col width="80">
						<col width="80">
						<col width="80">
						<col width="80">
						<col width="80">
						<col width="80">
						<col width="80">
						<col width="80">
						<col width="80">
						<col width="80">
						<col width="80">
						<col width="80">
					</colgroup>
					<thead>
						<tr>
							<th rowspan="3">&nbsp;</th>
							<th rowspan="3">비고</th>
							<th>업체<br/>주문번호</th>
							<th>업체명</th>
							<th>상품번호</th>
							<th>상품코드</th>
							<th>상품명</th>
							<th>판매가</th>
							<th>수량</th>
							<th>배송방식코드</th>
							<th>배송방식</th>
							<th>추가배송금액</th>
							<th>택배회사</th>
							<th>보내는사람</th>
							<th>보내는사람<br/>연락처</th>
							<th class="end">영업담당자</th>
						</tr>
						<tr>
							<th>주문자</th>
							<th>연락처</th>
							<th>휴대전화번호</th>
							<th>수취인</th>
							<th>연락처</th>
							<th>휴대전화번호</th>
							<th>우편번호</th>
							<th colspan="3">주소</th>
							<th colspan="4" class="end">주문자메모</th>
							
						</tr>
						<tr>
							<th>스티커번호</th>
							<th>스티커코드</th>
							<th>스티커메세지</th>
							<th>아웃박스<br/>스티커유무</th>
							<th>포장지번호</th>
							<th>포장지코드</th>
							<th>인쇄메세지</th>
							<th>출고예정일</th>
							<th>박스입수</th>
							<th colspan='4'>작업메모</th>
							<th class="end">옵션변경</th>
						</tr>
					</thead>
					<tbody>
					<?
						$nCnt = 0;
						
						if (sizeof($arr_rs) > 0) {
							for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

								$rn							= trim($arr_rs[$j]["rn"]);
								$CP_NO					= trim($arr_rs[$j]["CP_NO"]);
								$ORDER_NO				= trim($arr_rs[$j]["ORDER_NO"]);

								$O_NAME					= SetStringFromDB($arr_rs[$j]["O_NAME"]);
								$O_PHONE				= SetStringFromDB($arr_rs[$j]["O_PHONE"]);
								$O_HPHONE				= SetStringFromDB($arr_rs[$j]["O_HPHONE"]);
								$R_NAME					= SetStringFromDB($arr_rs[$j]["R_NAME"]);
								$R_PHONE				= SetStringFromDB($arr_rs[$j]["R_PHONE"]);
								$R_HPHONE				= SetStringFromDB($arr_rs[$j]["R_HPHONE"]);
								$R_ZIPCODE				= SetStringFromDB($arr_rs[$j]["R_ZIPCODE"]);
								$R_ADDR1				= SetStringFromDB($arr_rs[$j]["R_ADDR1"]);
								$OPT_MANAGER_NO			= SetStringFromDB($arr_rs[$j]["OPT_MANAGER_NO"]);
								$OPT_MANAGER_NAME		= SetStringFromDB($arr_rs[$j]["OPT_MANAGER_NAME"]);
								$MEMO					= trim($arr_rs[$j]["MEMO"]);
								$DELIVERY				= trim($arr_rs[$j]["DELIVERY"]);
								$SA_DELIVERY			= trim($arr_rs[$j]["SA_DELIVERY"]);
								$CP_ORDER_NO			= trim($arr_rs[$j]["CP_ORDER_NO"]);
								$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
								$REG_DATE				= date("Y-m-d",strtotime($REG_DATE));
							
								// 데이터 유효성 검사
								$err_str_order = "";
								$err_str_order_goods = "";
								$warning_str_order_goods = "";

								if ($CP_NO == "") {
									$err_str_order .=  "판매업체 누락,";
								} else {
									if (getCompayChk($conn, "판매", $s_adm_cp_type, $CP_NO) == "") {
										$err_str_order .=  "판매업체 오류,";
									}
								}
								
								if ($R_NAME == "") {
									$err_str_order .=  "수령인 누락,";
								}

								/*
								if ($R_HPHONE == "") {
									$err_str_order .=  "휴대전화번호 누락,";
								}
								
								if ($R_PHONE == "") {
									$err_str .=  "연락처 누락,";
								}
								
								if ($R_ZIPCODE <> "") {
									if (!chkZip($conn, $R_ZIPCODE)) {
										$err_str .=  "우편번호 오류,";
									}
								}
								*/

								// 영업담당자로 검색해서 $OPT_MANAGER_NO 구하기
								if ($OPT_MANAGER_NO == "등록요망") {
									$OPT_MANAGER_NO = tryAdminNoByName($conn, $OPT_MANAGER_NAME);
								}

								if ($OPT_MANAGER_NO == "등록요망" || $OPT_MANAGER_NO == "복수담당자존재") {
									$err_str_order .=  "영업담당자 오류,";
								}
								else
								{
									updateTempOrderAdmNo($conn, $ORDER_NO, $ORDER_SEQ, $OPT_MANAGER_NO, $temp_no);
									$arr_rs_temp_goods[$k]["OPT_MANAGER_NO"] = $OPT_MANAGER_NO;
								}

								if ($R_ADDR1 == "") {
									$err_str_order .=  "주소 누락,";
								}

								$arr_rs_temp_goods = selectTempOrderGoods($conn, $temp_no, $ORDER_NO);
								if (sizeof($arr_rs_temp_goods) > 0) {
									for ($k = 0 ; $k < sizeof($arr_rs_temp_goods); $k++) {

										$ORDER_SEQ			= SetStringFromDB($arr_rs_temp_goods[$k]["ORDER_SEQ"]);

										$GOODS_NO			= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_NO"]);
										$GOODS_CODE			= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_CODE"]);
										$GOODS_NAME			= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_NAME"]);
										$GOODS_PRICE		= trim($arr_rs_temp_goods[$k]["GOODS_PRICE"]);
										$QTY				= trim($arr_rs_temp_goods[$k]["QTY"]);
										
										//OPT_STICKER_CODE, OPT_STICKER_MSG, OPT_OUTBOX_TF, OPT_WRAP_CODE, OPT_PRINT_MSG, OPT_OUTSTOCK_DATE, OPT_MANAGER_NAME, OPT_MEMO, DELIVERY_TYPE, DELIVERY_PRICE
										
										$OPT_STICKER_NO		= SetStringFromDB($arr_rs_temp_goods[$k]["OPT_STICKER_NO"]);
										$OPT_STICKER_CODE	= SetStringFromDB($arr_rs_temp_goods[$k]["OPT_STICKER_CODE"]);
										$OPT_STICKER_MSG	= SetStringFromDB($arr_rs_temp_goods[$k]["OPT_STICKER_MSG"]);
										$OPT_OUTBOX_TF		= SetStringFromDB($arr_rs_temp_goods[$k]["OPT_OUTBOX_TF"]);
										$OPT_WRAP_NO		= SetStringFromDB($arr_rs_temp_goods[$k]["OPT_WRAP_NO"]);
										$OPT_WRAP_CODE		= SetStringFromDB($arr_rs_temp_goods[$k]["OPT_WRAP_CODE"]);
										$OPT_PRINT_MSG		= SetStringFromDB($arr_rs_temp_goods[$k]["OPT_PRINT_MSG"]);
										$OPT_OUTSTOCK_DATE	= SetStringFromDB($arr_rs_temp_goods[$k]["OPT_OUTSTOCK_DATE"]);
										
										$OPT_MEMO			= SetStringFromDB($arr_rs_temp_goods[$k]["OPT_MEMO"]);
										$DELIVERY_TYPE		= SetStringFromDB($arr_rs_temp_goods[$k]["DELIVERY_TYPE"]);
										$DELIVERY_TYPE_CODE	= SetStringFromDB($arr_rs_temp_goods[$k]["DELIVERY_TYPE_CODE"]);
										$DELIVERY_PRICE		= SetStringFromDB($arr_rs_temp_goods[$k]["DELIVERY_PRICE"]);
										$DELIVERY_CP		= SetStringFromDB($arr_rs_temp_goods[$k]["DELIVERY_CP"]);
										$SENDER_NM			= SetStringFromDB($arr_rs_temp_goods[$k]["SENDER_NM"]);
										$SENDER_PHONE		= SetStringFromDB($arr_rs_temp_goods[$k]["SENDER_PHONE"]);
										$DELIVERY_CNT_IN_BOX= SetStringFromDB($arr_rs_temp_goods[$k]["DELIVERY_CNT_IN_BOX"]);

										if($CP_ORDER_NO <> "") { 
											if (chkCompanyOrderNo($conn, $CP_ORDER_NO, $GOODS_NO) > 0) {
												$err_str_order_goods .= "업체 주문번호 존재, ";
											}
										}


										// 상품명으로 검색해서 $GOODS_NO 구하기
										if ($GOODS_NO == "등록요망") {

											$GOODS_NAME = SetStringToDB($GOODS_NAME);

											$GOODS_NO = tryGoodNoByGoodsCode($conn, $GOODS_CODE);
											
										}

										if ($GOODS_NO == "등록요망" || $GOODS_NO == "복수상품존재" || $GOODS_NO == "단종") {
											$err_str_order_goods .=  "상품번호 오류,";
											// 2018-03-06 품절 단종 외에도 추가될 경우 체크 X, 검증 패스
											// 2018-09-05 품절때도 입력가능하게 하려고 했으나 구조적인 문제 및 사용자의 부주의 우려로 다시 금지
											// 2018-11-20 이지웰(연미씨)에서 품절처리된걸 넣어야 한다고 해서 회의후 경고처리로 전환(요청:효곤씨) || $GOODS_NO == "품절"
										} else {
											
											if($GOODS_NO == "품절") {
												$warning_str_order_goods .=  "상품상태(".$GOODS_NO."),";
												$GOODS_NO = tryGoodNoByGoodsCode($conn, $GOODS_CODE, 'Y');
												$GOODS_NAME = getGoodsName($conn, $GOODS_NO);
												$GOODS_NAME = SetStringToDB($GOODS_NAME);
											}
											
											updateTempOrderGoodsName($conn, $ORDER_NO, $ORDER_SEQ, $GOODS_NAME, $temp_no);
											updateTempOrderGoodsNo($conn, $ORDER_NO, $ORDER_SEQ, $GOODS_NO, $temp_no);
											$arr_rs_temp_goods[$k]["GOODS_NO"] = $GOODS_NO;
										}

										// 스티커코드으로 검색해서 $OPT_STICKER_NO 구하기
										if ($OPT_STICKER_NO == "등록요망") {
											$OPT_STICKER_NO = tryGoodNoByGoodsCode($conn, $OPT_STICKER_CODE);
										}

										if ($OPT_STICKER_NO == "등록요망" || $OPT_STICKER_NO == "복수스티커존재") {
											$err_str_order_goods .=  "스티커번호 오류,";
										}
										else
										{
											updateTempOrderStickerNo($conn, $ORDER_NO, $ORDER_SEQ, $OPT_STICKER_NO, $temp_no);
											$arr_rs_temp_goods[$k]["OPT_STICKER_NO"] = $OPT_STICKER_NO;
										}

										if(chkGoodsExtraWrap($conn, $GOODS_NO) == "Y" && $OPT_WRAP_NO == "") { 
											$warning_str_order_goods = "포장여부확인,";
										}


										// 포장지코드로 검색해서 $OPT_WRAP_NO 구하기
										if ($OPT_WRAP_NO == "등록요망") {
											$OPT_WRAP_NO = tryGoodNoByGoodsCode($conn, $OPT_WRAP_CODE);
										}

										if ($OPT_WRAP_NO == "등록요망" || $OPT_WRAP_NO == "복수포장지존재") {
											$err_str_order_goods .=  "포장지번호 오류,";
										}
										else
										{
											updateTempOrderWrapNo($conn, $ORDER_NO, $ORDER_SEQ, $OPT_WRAP_NO, $temp_no);
											$arr_rs_temp_goods[$k]["OPT_WRAP_NO"] = $OPT_WRAP_NO;
										}

										$arr_rs_goods_extra = selectGoodsExtra($conn, $goods_no, 'WRAP_CODE');

										if(sizeof($arr_rs_goods_extra) > 0) { 
											if($rs_pcode == "WRAP_CODE") { 
												$rs_wrap_code			= SetStringFromDB($arr_rs_goods_extra[0]["DCODE"]); 

												
											}
										} else { 
											$rs_wrap_code = "";
										}

										if($rs_wrap_code != "" && $OPT_WRAP_NO == "")
											$warning_str_order_goods .= "상품 포장지 존재";

										$chkOutboxTF = getDcodeCode($conn, "OUTBOX_STICKER_TF", $OPT_OUTBOX_TF);
										//echo "out: ".$chkOutboxTF."<br/>";
										if(!($chkOutboxTF == "Y" || $chkOutboxTF == "N" || $chkOutboxTF == ""))
											$err_str_order_goods .=  "아웃박스스티커유무값 오류,";
										else
										{
											updateTempOrderOutboxTF($conn, $ORDER_NO, $ORDER_SEQ, $chkOutboxTF, $temp_no);
											$arr_rs_temp_goods[$k]["OPT_OUTBOX_TF_CODE"] = $chkOutboxTF;
										}

										if($OPT_OUTSTOCK_DATE == "1970-01-01") { 
											$OPT_OUTSTOCK_DATE = "출고예정일 변환 오류(YYYY-MM-DD)";
											$err_str_order_goods .=  "출고예정일 오류,";
										}

										$chkDeliveryType = getDcodeCode($conn, "DELIVERY_TYPE", $DELIVERY_TYPE);
									
										if($DELIVERY_TYPE <> "") {
											if(($DELIVERY_TYPE == "" || $chkDeliveryType == "&nbsp;" || $chkDeliveryType == "")) {
												$err_str_order_goods .=  "택배방식 오류,";
											} else 
											{
												updateTempOrderDeliveryType($conn, $ORDER_NO, $ORDER_SEQ, $chkDeliveryType, $temp_no);
												$arr_rs_temp_goods[$k]["DELIVERY_TYPE_CODE"] = $chkDeliveryType;
											}
										} else {
											$err_str_order_goods .=  "택배방식 누락,";
											$arr_rs_temp_goods[$k]["DELIVERY_TYPE_CODE"] = "";
										}

										$chkDeliveryCP = getDcodeCode($conn, "DELIVERY_CP", $DELIVERY_CP);
										if($DELIVERY_CP <> "") {
											if($chkDeliveryCP == "") {
													
												$err_str_order_goods .=  "택배회사 오류,";
											}  
										}

										$arr_rs_goods = selectGoods($conn, $GOODS_NO);

										// 상품 번호에 해당하는 주문 상품 임시 재고 등록 하기
										// 마트구분, 마트 주문번호, 상품번호, 수량
										//$fake_result = insertFakeStock($conn, $CP_NO, $CP_ORDER_NO, $GOODS_NO, $QTY);

										if($arr_rs_goods[0] != null){
									
											$rs_price				= trim($arr_rs_goods[0]["PRICE"]);
											$rs_buy_price			= trim($arr_rs_goods[0]["BUY_PRICE"]);
											$rs_sale_price			= trim($arr_rs_goods[0]["SALE_PRICE"]);
											$rs_goods_state			= trim($arr_rs_goods[0]["CATE_04"]);

											if ($rs_goods_state <> "판매중") {
												$str_goods_state = "<font color='red'>".getDcodeName($conn, "GOODS_STATE", $rs_goods_state)."</font>";
											} else {
												$str_goods_state = getDcodeName($conn, "GOODS_STATE", $rs_goods_state);
											}

											if ($CP_NO <> "") {
												$rs_sale_price = getCompanyGoodsPriceOrDCRate($conn, $GOODS_NO, $rs_sale_price, $rs_price, $CP_NO);
											}
											
											if ($GOODS_PRICE <> $rs_sale_price ) {
												$str_price = "<font color='red'>".number_format($GOODS_PRICE)."</font>";
											} else {
												$str_price = number_format($GOODS_PRICE);
											}
											
											if ($GOODS_PRICE < $rs_buy_price) {
												$str_plus_price = "<font color='red'>".number_format($GOODS_PRICE - $rs_buy_price)."</font>";
											} else {
												$str_plus_price = number_format($GOODS_PRICE - $rs_buy_price);
											}
										}

										if ($QTY == "") {
											$err_str_order_goods .=  "수량 누락,";
										} else {
											if ($QTY  < "1") {
												//$err_str .=  "수량 오류,";
											}
										}
					
								/*								
								if ($R_ADDR1 == "") {
									$R_ADDR1 = "보안 송장 배송 주문";
								}
								*/
									
									
								if ($err_str_order <> "") {
									$err_str_order = substr($err_str_order, 0, (strlen($err_str_order) -1));
									$err_str_order = str_replace(",","<div class='sp5'></div>",$err_str_order);
									$err_str_order = "<font color='red'>".$err_str_order."</font>";
								} else
									$err_str_order = "주문정보 정상";

								if ($err_str_order_goods <> "") {
									$err_str_order_goods = substr($err_str_order_goods, 0, (strlen($err_str_order_goods) -1));
									$err_str_order_goods = str_replace(",","<div class='sp5'></div>",$err_str_order_goods);
									$err_str_order_goods = "<font color='red'>".$err_str_order_goods."</font>";
								} else
									$err_str_order_goods = "상품정보 정상";

								if ($warning_str_order_goods <> "") {
									$warning_str_order_goods = substr($warning_str_order_goods, 0, (strlen($warning_str_order_goods) -1));
									$warning_str_order_goods = str_replace(",","<div class='sp5'></div>",$warning_str_order_goods);
									$warning_str_order_goods = "<font color='blue'>".$warning_str_order_goods."</font>";
								} else
									$warning_str_order_goods = "";
								

					?>
						<tr height="30">
							<td rowspan="3" class="filedown">
								<input type="checkbox" name="chk[]" value="<?=$ORDER_NO?>">
							</td>
							<td rowspan="3" class="modeual_nm" style="padding:8px 0 3px 0">
								<!--
								<a href="javascript:js_view('<?= $rn ?>','<?= $temp_no ?>','<?= $ORDER_NO ?>', 'order');"><?=$err_str_order?></a><br/>
								<a href="javascript:js_view('<?= $rn ?>','<?= $temp_no ?>','<?= $ORDER_NO ?>', 'goods');"><?=$err_str_order_goods?></a> 
								-->
								<?=$err_str_order?><br/>
								<?=$err_str_order_goods?><br/>
								<?=$warning_str_order_goods?>
								<? if ($err_str_order == "주문정보 정상" && $err_str_order_goods == "상품정보 정상") {?>
								<input type="hidden" name="ok[]" value="<?=$ORDER_NO?>">
								<? } ?>
							</td>
							<td><?= $CP_ORDER_NO?></td>
							<td class="modeual_nm"><?=getCompanyName($conn, $CP_NO)?></a></td>
							<td><?= $GOODS_NO?></td>
							<td><?= $GOODS_CODE?></td>
							<td class="modeual_nm"><?= $GOODS_NAME?></td>
							<td><?= number_format($GOODS_PRICE)?></td>
							<td><?= number_format($QTY)?></td>
							<td><?=$DELIVERY_TYPE_CODE?></td>
							<td><?=$DELIVERY_TYPE?></td>
							<td><?=$DELIVERY_PRICE?></td>
							<td><?=$DELIVERY_CP?></td>
							<td><?=$SENDER_NM?></td>
							<td><?=$SENDER_PHONE?></td>
							<td class="modeual_nm"><?=$OPT_MANAGER_NAME?></td>
						</tr>
						<tr height="30">
							<td><?= $O_NAME?></td>
							<td><?= $O_PHONE?></td>
							<td><?= $O_HPHONE?></td>
							<td><?= $R_NAME?></td>
							<td><?= $R_PHONE?></td>
							<td><?= $R_HPHONE?></td>
							<td><?= $R_ZIPCODE?></td>
							<td colspan="3" class="modeual_nm"><?= $R_ADDR1?></td>
							<td colspan="4" class="modeual_nm"><?= $MEMO?></td>
						</tr>
						<tr height="30">
							<td><?=$OPT_STICKER_NO?></td>
							<td><?=$OPT_STICKER_CODE?></td>
							<td><?=$OPT_STICKER_MSG?></td>
							<td><?=$OPT_OUTBOX_TF?></td>
							<td><?=$OPT_WRAP_NO?></td>
							<td><?=$OPT_WRAP_CODE?></td>
							<td><?=$OPT_PRINT_MSG?></td>
							<td><?=$OPT_OUTSTOCK_DATE?></td>
							<td><?=$DELIVERY_CNT_IN_BOX?></td>
							<td colspan="4"><?=$OPT_MEMO?></td>
							<td><input type="button" value="<?= $CP_ORDER_NO?>" onclick="js_change_option();"></td>
						</tr>
						
					<?							
											}
										}
		
										$err_str_order = "";
										$err_str_order_goods = "";
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
					<!--
					<input type="button" name="aa" value=" 상품등록요망 리스트 " class="btntxt" onclick="js_unregistered_goods_excel();">&nbsp;&nbsp;&nbsp;&nbsp; 
					<input type="button" name="aa2" value=" 미등록자료 엑셀받기 " class="btntxt" onclick="js_excel();">&nbsp;&nbsp;&nbsp;&nbsp;
					-->
					<input type="button" name="bb" value=" 정상자료 등록 " class="btntxt" onclick="js_register();">&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="button" name="cc" value=" 선택자료 삭제 " class="btntxt" onclick="js_delete();">
				</div>

			</div>

			<?
				// 현재 주문 상품 수량 리스트
				//$order_goods_list = listTempOrderCnt($conn, $temp_no);
			?>
			* 현재 주문 상품 수량 리스트 &nbsp; <a href="javascript:js_temp_goods_excel();"><img src="../images/common/btn/btn_excel.gif" alt="주문 상품 수량 리스트"></a>
			<table cellpadding="0" cellspacing="0" class="rowstable" style="width:auto;">

				<colgroup>
					<col width="120px" />
					<col width="120px" />
					<col width="120px" />
					<col width="300px" />
					<col width="120px" />
					<col width="120px" />
				</colgroup>
				<thead>
					<tr>
						<th>상품종류</th>
						<th>상품코드</th>
						<th>낱개바코드</th>
						<th>상품명</th>
						<th>박스입수</th>
						<th class="end">주문수량*구성상품수량 (주문총합)</th>
					</tr>
				</thead>
				<tbody>
				<?
					if (sizeof($order_goods_list) > 0) {
						
						for ($j = 0 ; $j < sizeof($order_goods_list); $j++) {
							//GOODS_NO	GOODS_CODE	GOODS_NAME DELIVERY_CNT_IN_BOX CNT
							$GOODS_NO		= trim($order_goods_list[$j]["GOODS_NO"]);
							$CATE_02		= trim($order_goods_list[$j]["CATE_02"]);
							$CATE_02 = getDcodeName($conn, "GOODS_SUB_CATE", $CATE_02);
							$GOODS_CODE		= trim($order_goods_list[$j]["GOODS_CODE"]);
							$KANCODE		= trim($order_goods_list[$j]["KANCODE"]);
							$GOODS_NAME		= SetStringFromDB($order_goods_list[$j]["GOODS_NAME"]);
							$DELIVERY_CNT_IN_BOX = trim($order_goods_list[$j]["DELIVERY_CNT_IN_BOX"]);
							$CNT		    = trim($order_goods_list[$j]["CNT"]);
				?>
					<tr>
						<td height="24px"><?=$CATE_02?></td>
						<td><?=$GOODS_CODE?></td>
						<td><?=$KANCODE?></td>
						<td class="pname" style="text-align:left;padding-left:5px;"><?=$GOODS_NAME?></td>
						<td><?=$DELIVERY_CNT_IN_BOX?></td>
						<td style="text-align:right;padding-right:5px;"><?=number_format($CNT)?></td>
					</tr>
				<?
						}
					}else {
				?>
					<tr>
						<td colspan="6" height="30">데이터가 없습니다</td>
					</tr>
				<?
					}
				?>
				</tbody>
			</table>
			<br/>
			<div class="sp20"></div>
		</form>
    </td>
  </tr>
  </table>




</div>
<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>