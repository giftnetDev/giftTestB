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
	$menu_right = "OD012"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/syscode/syscode.php";

#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);

	//인터파크-SK케미칼은 제주도 제외 없음
	$has_island = true;

	if ($this_date == "") 
		$this_date = date("Y-m-d H:i:s",strtotime("0 month"));
#====================================================================
# DML Process
#====================================================================
	if ($mode == "FR") {
	
	#====================================================================
		$savedir1 = $g_physical_path."upload_data/temp_order";
	#====================================================================

		$arr_dcode = listDcode($conn, "REMOVABLE_OPT_NAME", 'Y', 'N', "", "", 1, 1000);

		$file_nm	= upload($_FILES[file_nm], $savedir1, 10000 , array('xls'));

		require_once '../../_excel_reader/Excel/reader.php';
		$data = new Spreadsheet_Excel_Reader();
		$data->setOutputEncoding('euc-kr');
		$data->read("../../upload_data/temp_order/".$file_nm);
		$temp_no = $file_nm;

		error_reporting(E_ALL ^ E_NOTICE);

		//$prev_cp_order_no = "";
		$prev_o_name = "";
		$prev_r_name = "";
		$prev_r_addr1 = "";
		$prev_order_no = "";
		$order_seq = 1;

		$arr_options = getGoodsNames($conn, '0802', "", "");
		$arr_mart_goods = getGoodsNames($conn, '09', "", "");

		//echo "LINES : ".$data->sheets[0]['numRows']."<br/>";
		//exit;
		for($i = 2 ; $i <= $data->sheets[0]['numRows']; $i++) {

			$cp_order_goods		= SetStringToDB(trim($data->sheets[0]['cells'][$i][1]));
			$cp_order_status	= SetStringToDB(trim($data->sheets[0]['cells'][$i][15]));
			//배송준비중인거만 처리
			if($cp_order_status <> "배송준비중" && $cp_order_status <> "배송중") { 
				continue;
			}

			$cp_order_no        = SetStringToDB(trim($data->sheets[0]['cells'][$i][3]));
			$cp_no              = "8398";
			$goods_name         = SetStringToDB(trim($data->sheets[0]['cells'][$i][17]));
			$goods_price	    = SetStringToDB(trim($data->sheets[0]['cells'][$i][20]));
			$qty				= SetStringToDB(trim($data->sheets[0]['cells'][$i][19]));
			$merged_str		    = SetStringToDB(trim($data->sheets[0]['cells'][$i][18]));

			//옵션명에 필요없는 글씨들 제거 
			for($g = 0; $g <= sizeof($arr_dcode); $g++) { 
				
				$dcode_nm = $arr_dcode[$g]["DCODE_NM"];
				$merged_str     = str_replace($dcode_nm, "", $merged_str);

			}

			$re_merged_str = "";
			$arr_goods_sub = explode("\n", $merged_str);
			for($g = 0; $g <= sizeof($arr_goods_sub); $g++) { 

				$splited = $arr_goods_sub[$g];

				$is_matched = false;

				for($k = 0 ; $k < sizeof($arr_options); $k++) {

					$t_sticker_name = $arr_options[$k]['GOODS_NAME'];

					//스티커 옵션 찾는 위치
					if (strpos($splited, $t_sticker_name) !== FALSE)
					{
						$is_matched = true;
					}

				}
				
				if($is_matched)
					$opt_sticker_code = getNameWithoutNumber($splited);
				else
					$re_merged_str .= $splited;

			}

			$goods_sub_name = $re_merged_str;





			/*
			for($k = 0 ; $k < sizeof($arr_options); $k++) {

				$t_sticker_name = $arr_options[$k]['GOODS_NAME'];

				//스티커 옵션 찾는 위치
				if (strpos($splited, $t_sticker_name) !== FALSE)
				{
					$splited = getNameWithoutNumber($splited);
				}

			}

			$concat_str = "";
			if(strpos($goods_sub_name,"\n") > 0) { 
				$arr_goods_sub = explode("\n", $goods_sub_name);
				for($g = 0; $g <= sizeof($arr_goods_sub); $g++) { 

					$splited = $arr_goods_sub[$g];

					for($k = 0 ; $k < sizeof($arr_options); $k++) {

						$t_sticker_name = $arr_options[$k]['GOODS_NAME'];

						//스티커 옵션 찾는 위치
						if (strpos($splited, $t_sticker_name) !== FALSE)
						{
							$splited = getNameWithoutNumber($splited);
						}

					}

					$concat_str .= $splited;

				}
			} else 
				$concat_str = $goods_sub_name;

			if(strpos($goods_sub_name,"\n") > 0) { 
				

			} else { 

				// 스티커 번호 제거
				$goods_sub_name = getNameWithoutNumber($goods_sub_name);

				for($k = 0 ; $k < sizeof($arr_options); $k++) {

					$t_sticker_name = $arr_options[$k]['GOODS_NAME'];

					//스티커 옵션 찾는 위치
					if (strpos($opt_sticker_code, $t_sticker_name) !== FALSE)
					{
						$opt_sticker_code = $t_sticker_name;

						//echo "매치:".$t_sticker_name."<br/>";
					}

				}

				for($k = 0 ; $k < sizeof($arr_options); $k++) {

					$t_sticker_name = $arr_options[$k]['GOODS_NAME'];

					//상품 옵션에는 스티커 이름이 안들어가게 모두 제거
					$goods_sub_name = str_replace($t_sticker_name, "", $goods_sub_name);

				}

			}
			*/
			
			$goods_mart_code	= SetStringToDB(trim($data->sheets[0]['cells'][$i][4]));
			$o_name				= SetStringToDB(trim($data->sheets[0]['cells'][$i][7]));
			$o_phone			= SetStringToDB(trim($data->sheets[0]['cells'][$i][8]));
			$o_hphone			= SetStringToDB(trim($data->sheets[0]['cells'][$i][9]));
			$r_name				= SetStringToDB(trim($data->sheets[0]['cells'][$i][10]));
			$r_phone			= SetStringToDB(trim($data->sheets[0]['cells'][$i][11]));
			$r_hphone			= SetStringToDB(trim($data->sheets[0]['cells'][$i][12]));
			$r_zipcode			= "";
			$r_addr1			= SetStringToDB(trim($data->sheets[0]['cells'][$i][25]));
			$memo				= SetStringToDB(trim($data->sheets[0]['cells'][$i][26]));
			$delivery			= 0;
			$sa_delivery		= 0;
			//$opt_sticker_code	= $goods_sub_name;
			$opt_sticker_msg	= "";
			$order_state        = '1';
			$use_tf				= 'Y';
			//$reg_adm			= $s_adm_no;

			$no_need_to_regist = false;
			for($o = 0 ; $o < sizeof($arr_mart_goods); $o++) {
				$t_goods_name = $arr_mart_goods[$o]['GOODS_NAME'];
				$t_cate_04	  = $arr_mart_goods[$o]['CATE_04']; //상품판매상태

				//echo $t_cate_04." ".$t_goods_name." ".$goods_name."<br/>";
				//echo $o." ";

				// 마트 상품으로 업체자체배송으로 되어있을 경우 패스
				if($t_goods_name == $goods_name) { 
					$no_need_to_regist = true;

					//echo "매치 : ".$t_cate_04." ".$t_goods_name." ".$goods_name."<br/>";
				}

			}

			// 전체 상품 배열안에서 검색해야할 필요가 있을때 한번에 줄을 무시하려고 진행
			if($no_need_to_regist)
				continue;


			//if(strpos($goods_sub_name,"\n") <= 0)
			//	$goods_sub_name = "";
			
			//echo $cp_order_no." / ".$cp_no." / ".$goods_name." / ".$goods_price." / ".$qty." / ".$goods_option_nm." / ".$goods_option_nm2." / ".$goods_mart_code." / ".$o_name." / ".$o_phone." / ".$o_hphone." / ".$r_name." / ".$r_phone." / ".$r_hphone." / ".$r_zipcode." / ".$r_addr1." / ".$memo." / ".$delivery." / ".$sa_delivery."<br/>";


			$goods_no = tryGoodNoFromMartData($conn, $goods_name, $goods_sub_name, $goods_mart_code);

			if($opt_sticker_code <> "")
				$opt_sticker_no	  = tryGoodNoByGoodsName($conn, $opt_sticker_code);
			else 
				$opt_sticker_no	  = "";

			//echo $goods_no."<br/>";

			if($r_name != $prev_r_name || $o_name != $prev_o_name || $r_addr1 != $prev_r_addr1){
				$order_seq = 1;

				$inserted_order_no = insertTempOrder($conn, $temp_no, $cp_no, '', '', '', '', '', '', '', '', '', '', $o_name, $o_phone, $o_hphone, $r_name, $r_phone, $r_hphone, $r_zipcode, $r_addr1, $memo, $order_state, $delivery, $sa_delivery, $cp_order_no, $use_tf, $s_adm_no);

				insertTempOrderGoods($conn, $temp_no, $cp_order_goods, $inserted_order_no, $order_seq, $goods_no, $goods_name, $goods_sub_name, $goods_price, $qty, $opt_sticker_no, $opt_sticker_code, $opt_sticker_msg, '', '', '', '', $cp_order_no, $goods_mart_code);

				$prev_order_no = $inserted_order_no;
				
				//echo 	$cp_order_no." - ".$inserted_order_no.":new<br>";

			} else {

				//echo 	$cp_order_no." - ".$inserted_order_no.":append<br>";
				$order_seq = $order_seq + 1;
				insertTempOrderGoods($conn, $temp_no, $cp_order_goods, $prev_order_no, $order_seq,  $goods_no, $goods_name, $goods_sub_name, $goods_price, $qty, $opt_sticker_no, $opt_sticker_code, $opt_sticker_msg, '', '', '', '', $cp_order_no, $goods_mart_code);
			}
			
			//$prev_cp_order_no = $cp_order_no;
			$prev_o_name = $o_name;
			$prev_r_name = $r_name;
			$prev_r_addr1 = $r_addr1;

		}
		unset($data);
		//echo 'order_write_file.php?mode=L&temp_no='.$file_nm.'&this_date='.$this_date;

?>	
<script language="javascript">
		location.href =  '<?=$_SERVER[PHP_SELF]?>?mode=L&temp_no=<?=$file_nm?>&this_date=<?=$this_date?>';
</script>
<?
		exit;

	}	

	/*
	if ($mode == "I") {

		$row_cnt = count($ok);
		
		$str_order_no = "";

		for ($k = 0; $k < $row_cnt; $k++) {
			$str_order_no .= "'".$ok[$k]."',";
		}

		$str_order_no = substr($str_order_no, 0, (strlen($str_order_no) -1));
		//echo $str_cp_no;

		$insert_result = insertTempToRealOrderWithDate($conn, $temp_no, $str_order_no, $this_date);

		if ($insert_result) {
			$delete_result = deleteTempToRealOrder($conn, $temp_no, $str_order_no);
		}

		$mode = "L";

	}
	*/

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

	function js_view(rn, file_nm, order_no) {
		
		var url = "order_modify.php?mode=S&temp_no="+file_nm+"&order_no="+order_no;
		NewWindow(url, '주문대량입력', '860', '513', 'YES');
		
	}

	function js_reload() {
		location.href =  '<?=$_SERVER[PHP_SELF]?>?mode=L&temp_no=<?=$temp_no?>';
	}

	function js_chk_island() {

		if(document.frm.has_island.checked)
			location.href =  '<?=$_SERVER[PHP_SELF]?>?mode=L&temp_no=<?=$temp_no?>&has_island=true';
		else
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

	function js_goods_view(goods_no) {

		var frm = document.frm;
		
		frm.goods_no.value = goods_no;
		frm.mode.value = "S";
		frm.target = "blank";
		frm.method = "post";
		frm.action = "/manager/goods/goods_write.php";
		frm.submit();
		
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
				<h2>주문 등록 - 인터파크</h2>
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
								<!--
								<br><br>
								<a href="/_common/download_file.php?file_name=insert_order.xls&filename_rnm=insert_example.xls&str_path=manager/order/">받기</a>
								-->
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
					* 주문번호별 <?=totalCntTempOrderGoods($conn, $temp_no)?> 건 &nbsp;&nbsp;
					<? if ($insert_result) {?>
					* 등록건 <?=$row_cnt?> 건
					<? }?>
				</div>
				<div id="temp_scroll">
				<table cellpadding="0" cellspacing="0" class="rowstable01" style="width:2810px">
					<colgroup>
						<col width="35">
						<col width="100">
						<col width="120">
						<col width="720">
						<col width="120">
						<col width="120">
						<col width="120">
						<col width="120">
						<col width="120">
						<col width="120">
						<col width="120">
						<col width="80">
						<col width="500">
						<col width="400">
						
					</colgroup>
					<thead>
						<tr>
							<th>&nbsp;</th>
							<th>비고</th>
							<th>업체주문번호</th>
							<th>
								주문 상품<br>
								<table cellpadding="0" cellspacing="0" class="rowstable04" style="width:98%">
									<colgroup>
										<col width="10%">
										<col width="14%">
										<col width="*">
										<col width="10%">
										<col width="8%">
										<col width="8%">
										<col width="16%">
										<col width="4%">
									</colgroup>
									<thead>
										<tr>
											<th>상품번호</th>
											<th>상품고유번호</th>
											<th>상품명</th>
											<th>속성</th>
											<th>수량</th>
											<th>스티커번호</th>
											<th>스티커코드</th>
											<th class="end">스티커메세지</th>
										</tr>
									</thead>
								</table>
							</th>
							<th>업체명</th>
							<th>주문자</th>
							<th>연락처</th>
							<th>휴대전화번호</th>
							<th>수취인</th>
							<th>연락처</th>
							<th>휴대전화번호</th>
							<th>우편번호</th>
							<th>주소</th>
							<th class="end">주문자메모</th>
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
								$MEMO					= trim($arr_rs[$j]["MEMO"]);
								$DELIVERY				= trim($arr_rs[$j]["DELIVERY"]);
								$SA_DELIVERY			= trim($arr_rs[$j]["SA_DELIVERY"]);
								$CP_ORDER_NO			= trim($arr_rs[$j]["CP_ORDER_NO"]);
								$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
								$REG_DATE				= date("Y-m-d",strtotime($REG_DATE));

								// 데이터 유효성 검사
								$err_str = "정상";
								$warning_str = "";

								if ($CP_NO == "") {
									$err_str .=  "판매업체 누락,";
								} else {
									if (getCompayChk($conn, "판매", $s_adm_cp_type, $CP_NO) == "") {
										$err_str .=  "판매업체 오류,";
									}
								}

								

								$arr_rs_temp_goods = selectTempOrderGoods($conn, $temp_no, $ORDER_NO);
								if (sizeof($arr_rs_temp_goods) > 0) {
									for ($k = 0 ; $k < sizeof($arr_rs_temp_goods); $k++) {

										$GOODS_NO			= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_NO"]);
										$CP_ORDER_GOODS		= SetStringFromDB($arr_rs_temp_goods[$k]["CP_ORDER_GOODS"]);
										$GOODS_MART_CODE	= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_MART_CODE"]);
										$GOODS_NAME			= $arr_rs_temp_goods[$k]["GOODS_NAME"];
										$GOODS_SUB_NAME		= $arr_rs_temp_goods[$k]["GOODS_SUB_NAME"];
										$GOODS_PRICE		= trim($arr_rs_temp_goods[$k]["GOODS_PRICE"]);
										$QTY				= trim($arr_rs_temp_goods[$k]["QTY"]);
										$OPT_STICKER_NO		= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_OPTION_01"]);
										$OPT_STICKER_CODE	= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_OPTION_02"]);
										$OPT_STICKER_MSG	= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_OPTION_03"]);

										// 상품명으로 검색해서 $GOODS_NO 구하기

										if ($CP_ORDER_GOODS == "") {
											$err_str .=  "상품고유번호 누락,";
										}
										
										if ($GOODS_NO == "등록요망") {

											$GOODS_NAME = SetStringToDB($GOODS_NAME);
											$GOODS_OPTION_NM = SetStringToDB($GOODS_OPTION_NM);

											$GOODS_NO = tryGoodNoFromMartData($conn, $GOODS_NAME, $GOODS_SUB_NAME, $GOODS_MART_CODE);
										}

										if ($GOODS_NO == "등록요망" || $GOODS_NO == "복수상품존재") {
											$err_str .=  "상품번호 누락,";
										}
										else
										{
											updateTempOrderGoodsNo($conn, $ORDER_NO, $ORDER_SEQ, $GOODS_NO, $temp_no);
											$arr_rs_temp_goods[$k]["GOODS_NO"] = $GOODS_NO;
										}

										// 스티커코드으로 검색해서 $OPT_STICKER_NO 구하기
										if ($OPT_STICKER_NO == "등록요망") {
											$OPT_STICKER_NO = tryGoodNoByGoodsName($conn, $OPT_STICKER_CODE);
										}

										if ($OPT_STICKER_NO == "등록요망" || $OPT_STICKER_NO == "복수스티커존재") {
											$err_str .=  "스티커번호 오류,";
										}
										else
										{
											updateTempOrderStickerNo($conn, $ORDER_NO, $ORDER_SEQ, $OPT_STICKER_NO, $temp_no);
											$arr_rs_temp_goods[$k]["GOODS_OPTION_01"] = $OPT_STICKER_NO;
										}

										
										if (chkCompanyOrderNo($conn, $CP_ORDER_NO, $GOODS_NO) > 0) {
											$warning_str = "마트 주문번호 존재 ";
											//$err_str .= "마트 주문번호 존재, ";
										}
										

										if ($QTY == "") {
											$err_str .=  "수량 누락,";
										} else {
											if ($QTY  < "1") {
												//$err_str .=  "수량 오류,";
											}
										}
										
										if ($R_NAME == "") {
											$err_str .=  "수령인 누락,";
										}

										if ($R_HPHONE == "") {
											$err_str .=  "휴대전화번호 누락,";
										}

										if ($R_ADDR1 == "") {
											$err_str .=  "주소 누락,";
										}
									}
								}

								if ($err_str <> "정상") {
									$err_str = str_replace("정상","",$err_str);
									$err_str = substr($err_str, 0, (strlen($err_str) -1));
									$err_str = str_replace(",","<div class='sp5'></div>",$err_str);
									$err_str = "<font color='red'>".$err_str."</font>";
								}

								if($warning_str <> ""){ 
									$warning_str = "<font color='blue'>".$warning_str."</font>";
								}
					?>
						<tr>
							<td class="filedown">
								<input type="checkbox" name="chk[]" value="<?=$ORDER_NO?>">
							</td>
							<td class="modeual_nm" style="padding:8px 0 3px 0">
								<a href="javascript:js_view('<?= $rn ?>','<?= $temp_no ?>','<?= $ORDER_NO ?>');"><?=$err_str?></a>
								<? if ($err_str == "정상") {?>
								<input type="hidden" name="ok[]" value="<?=$ORDER_NO?>">
								<? } ?><br/>
								<?= $warning_str?>
							</td>
							<td><?= $CP_ORDER_NO?></td>
							<td>

								<table cellpadding="0" cellspacing="0" class="rowstable04"  style="width:98%">
									<colgroup>
										<col width="10%">
										<col width="14%">
										<col width="*">
										<col width="10%">
										<col width="8%">
										<col width="8%">
										<col width="16%">
										<col width="4%">
									</colgroup>
								<?
									if (sizeof($arr_rs_temp_goods) > 0) {
										for ($k = 0 ; $k < sizeof($arr_rs_temp_goods); $k++) {

											$GOODS_NO			= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_NO"]);
											$CP_ORDER_GOODS		= SetStringFromDB($arr_rs_temp_goods[$k]["CP_ORDER_GOODS"]);
											$GOODS_MART_CODE	= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_MART_CODE"]);
											$GOODS_NAME			= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_NAME"]);
											$GOODS_SUB_NAME		= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_SUB_NAME"]);
											$GOODS_PRICE		= trim($arr_rs_temp_goods[$k]["GOODS_PRICE"]);
											$QTY				= trim($arr_rs_temp_goods[$k]["QTY"]);
											$OPT_STICKER_NO		= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_OPTION_01"]);
											$OPT_STICKER_CODE	= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_OPTION_02"]);
											$OPT_STICKER_MSG	= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_OPTION_03"]);
											?>
											<tr>
												<td><?= $GOODS_NO?></td>
												<td><?= $CP_ORDER_GOODS?></td>
												<td class="modeual_nm"><?= $GOODS_NAME?></td>
												<td class="modeual_nm"><?= $GOODS_SUB_NAME?></td>
												<td><?= number_format($QTY)?></td>
												<td><?=$OPT_STICKER_NO?></td>
												<td><?=$OPT_STICKER_CODE?></td>
												<td><?=$OPT_STICKER_MSG?></td>
											</tr>
											<?
										}
									}
								?>
								</table>
							</td>
							<td class="modeual_nm"><?=getCompanyName($conn, $CP_NO)?></a></td>
							<td><?= $O_NAME?></td>
							<td><?= $O_PHONE?></td>
							<td><?= $O_HPHONE?></td>
							<td><?= $R_NAME?></td>
							<td><?= $R_PHONE?></td>
							<td><?= $R_HPHONE?></td>
							<td><?= $R_ZIPCODE?></td>
							<td class="modeual_nm"><?= $R_ADDR1?></td>
							<td class="modeual_nm"><?= $MEMO?></td>
							
						</tr>
					<?			
										$warning_str = "";
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
					<!--<input type="button" name="aa" value=" 상품등록요망 리스트 " class="btntxt" onclick="js_unregistered_goods_excel();">&nbsp;&nbsp;&nbsp;&nbsp; 
					-->
					<input type="button" name="aa2" value=" 미등록자료 엑셀받기 " class="btntxt" onclick="js_excel();">&nbsp;&nbsp;&nbsp;&nbsp;
					<!--
					<input type="button" name="bb" value=" 정상자료 등록 " class="btntxt" onclick="js_register();">&nbsp;&nbsp;&nbsp;&nbsp;
					-->
					<input type="button" name="cc" value=" 선택자료 삭제 " class="btntxt" onclick="js_delete();">
				</div>

			</div>
			<!--
			<?
				// 현재 주문 상품 수량 리스트
				$order_goods_list = listTempOrderCnt($conn, $temp_no, $has_island);
			?>
			<div class="text_frame">* 현재 주문 상품 수량 리스트 &nbsp; <a href="javascript:js_temp_goods_excel();"><img src="../images/common/btn/btn_excel.gif" alt="주문 상품 수량 리스트"></a>
			<div class="float_right"><label><input type="checkbox" onclick="js_chk_island()" name="has_island" <?=($has_island ? "checked='checked'" : "")?> value="true"/> 제주도 및 도서산간 포함</label></div>
			</div>
			<table cellpadding="0" cellspacing="0" class="rowstable">

				<colgroup>
					<col width="120px" />
					<col width="120px" />
					<col width="120px" />
					<col width="*" />
					<col width="120px" />
					<col width="120px" />
					<col width="120px" />
				</colgroup>
				<thead>
					<tr>
						<th>상품종류</th>
						<th>상품코드</th>
						<th>낱개바코드</th>
						<th>상품명</th>
						<th>재고</th>
						<th>박스입수</th>
						<th class="end">주문수량*구성상품수량 (주문총합)</th>
					</tr>
				</thead>
				<tbody>
				<?
					if (sizeof($order_goods_list) > 0) {
						
						for ($j = 0 ; $j < sizeof($order_goods_list); $j++) {
							$GOODS_NO				= trim($order_goods_list[$j]["GOODS_NO"]);
							$CATE_NAME				= trim($order_goods_list[$j]["CATE_NAME"]);
							$GOODS_CODE				= trim($order_goods_list[$j]["GOODS_CODE"]);
							$KANCODE				= trim($order_goods_list[$j]["KANCODE"]);
							$GOODS_NAME				= SetStringFromDB($order_goods_list[$j]["GOODS_NAME"]);
							$DELIVERY_CNT_IN_BOX	= trim($order_goods_list[$j]["DELIVERY_CNT_IN_BOX"]);
							$CNT					= trim($order_goods_list[$j]["CNT"]);
							$STOCK_CNT				= trim($order_goods_list[$j]["STOCK_CNT"]);
							$BSTOCK_CNT				= trim($order_goods_list[$j]["BSTOCK_CNT"]);
				?>
					<tr>
						<td height="24px"><?=$CATE_NAME?></td>
						<td><?=$GOODS_CODE?></td>
						<td><?=$KANCODE?></td>
						<td class="pname" style="text-align:left;padding-left:5px;"><?=$GOODS_NAME?></td>
						<td style="text-align:right;padding-right:5px;"><?=number_format($STOCK_CNT)?></td>
						<td><?=$DELIVERY_CNT_IN_BOX?></td>
						<td style="text-align:right;padding-right:5px;"><?=number_format($CNT)?></td>
					</tr>
				<?
						}
					}else {
				?>
					<tr>
						<td colspan="7" height="30">데이터가 없습니다</td>
					</tr>
				<?
					}
				?>
				</tbody>
			</table>
			<br/>
			<span>(제주, 울릉등 도서산간지역은 합산에서 제외됩니다.)</span>
			-->
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