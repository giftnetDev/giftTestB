<?session_start();?>
<?

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "SP009"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

#====================================================================
# common_header Check Session
#====================================================================
	require "../../_common/common_header.php"; 

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/member/member.php";
	require "../../_classes/biz/payment/payment.php";
	require "../../_classes/biz/board/board.php";

	$mode	= trim($mode);
	
	if ($this_date == "") 
		$this_date = date("Y-m-d",strtotime("0 month"));

	if ($mode == "FR") {
		
		#====================================================================
			$savedir1 = $g_physical_path."upload_data/temp_order";
		#====================================================================

		$file_nm	= upload($_FILES[file_nm], $savedir1, 10000 , array('xls','xlsx'));
		error_reporting(E_ALL ^ E_NOTICE);

		require_once "../../_PHPExcel/Classes/PHPExcel.php";
		$objPHPExcel = new PHPExcel();
		require_once "../../_PHPExcel/Classes/PHPExcel/IOFactory.php";
		$filename = '../../upload_data/temp_order/'.$file_nm; 

		$temp_no = $file_nm;
		
		try {
			
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

				$A      = iconv("UTF-8","EUC-KR", $objWorksheet->getCell('A'.$i)->getValue());
				$B      = iconv("UTF-8","EUC-KR", $objWorksheet->getCell('B'.$i)->getValue());
				$C      = iconv("UTF-8","EUC-KR", $objWorksheet->getCell('C'.$i)->getValue());
				$D      = iconv("UTF-8","EUC-KR", $objWorksheet->getCell('D'.$i)->getValue());
				$E		= iconv("UTF-8","EUC-KR", $objWorksheet->getCell('E'.$i)->getValue());
				$F  	= iconv("UTF-8","EUC-KR", $objWorksheet->getCell('F'.$i)->getValue());
				$G		= iconv("UTF-8","EUC-KR", $objWorksheet->getCell('G'.$i)->getValue());
				$H		= iconv("UTF-8","EUC-KR", $objWorksheet->getCell('H'.$i)->getValue());
				$I		= iconv("UTF-8","EUC-KR", $objWorksheet->getCell('I'.$i)->getValue());
				$J		= iconv("UTF-8","EUC-KR", $objWorksheet->getCell('J'.$i)->getValue());
				$K		= iconv("UTF-8","EUC-KR", $objWorksheet->getCell('K'.$i)->getValue());
				$L		= iconv("UTF-8","EUC-KR", $objWorksheet->getCell('L'.$i)->getValue());
				$M		= iconv("UTF-8","EUC-KR", $objWorksheet->getCell('M'.$i)->getValue());
				$N		= iconv("UTF-8","EUC-KR", $objWorksheet->getCell('N'.$i)->getValue());
				$O		= iconv("UTF-8","EUC-KR", $objWorksheet->getCell('O'.$i)->getValue());
				$P		= iconv("UTF-8","EUC-KR", $objWorksheet->getCell('P'.$i)->getValue());
				$Q		= iconv("UTF-8","EUC-KR", $objWorksheet->getCell('Q'.$i)->getValue());
				$R		= iconv("UTF-8","EUC-KR", $objWorksheet->getCell('R'.$i)->getValue());
				$S		= iconv("UTF-8","EUC-KR", $objWorksheet->getCell('S'.$i)->getValue());
				$T		= iconv("UTF-8","EUC-KR", $objWorksheet->getCell('T'.$i)->getValue());
				$U		= iconv("UTF-8","EUC-KR", $objWorksheet->getCell('U'.$i)->getValue());
				$V		= iconv("UTF-8","EUC-KR", $objWorksheet->getCell('V'.$i)->getValue());
				$W		= iconv("UTF-8","EUC-KR", $objWorksheet->getCell('W'.$i)->getValue());
				$X		= iconv("UTF-8","EUC-KR", $objWorksheet->getCell('X'.$i)->getValue());
				$Y		= iconv("UTF-8","EUC-KR", $objWorksheet->getCell('Y'.$i)->getValue());
				$Z		= iconv("UTF-8","EUC-KR", $objWorksheet->getCell('Z'.$i)->getValue());
				$AA		= iconv("UTF-8","EUC-KR", $objWorksheet->getCell('AA'.$i)->getValue());
				$AB		= iconv("UTF-8","EUC-KR", $objWorksheet->getCell('AB'.$i)->getValue());
				$AC		= iconv("UTF-8","EUC-KR", $objWorksheet->getCell('AC'.$i)->getValue());
				$AD		= iconv("UTF-8","EUC-KR", $objWorksheet->getCell('AD'.$i)->getValue());

				insertTempOrderEzwell($conn, $temp_no, $A, $B, $C, $D, $E, $F, $G, $H, $I, $J, $K, $L, $M, $N, $O, $P, $Q, $R, $S, $T, $U, $V, $W, $X, $Y, $Z, $AA, $AB, $AC, $AD, $s_adm_no);

				// 코드 숫자 패턴과 인식 XXX-XXXXXX 2017-04-17
				preg_match('/\\d{3}-\\d{6}/', $objWorksheet->getCell('I'.$i)->getValue(), $matches);

				if (!empty($matches)) {
					$goods_code = $matches[0]; 
				} else
					$goods_code = "";

				/*
				if (strpos($I, "/") > 0) { 

					$goods_code = substr($I, strpos($I, "/") + 1, 15);
					$goods_code = trim($goods_code);

					//if(!(strlen($goods_code) == 10 && strpos($goods_code, "-") > 0))
					//	$goods_code = "";
				}
				*/
				
				//2017-03-14 처리자 요청에 따라서 주문일을 처리일로 처리하도록 조치
				//$order_date = $G;
				$order_date = $this_date;
				$cp_order_no = $B;
				$con_cp_no = "8321"; //이지웰 복지몰
				$goods_name = $I;
				$sale_price = str_replace(",", "", $M);
				$qty = $Q;
				$o_mem_nm = $D;
				$o_phone = $F;
				$o_hphone = $E; 
				$r_mem_nm = $U."님";
				$r_phone = $W;
				$r_hphone = $V;
				$zipcode = $X;
				$r_addr1 = $Y;
				$order_memo = $Z;
	
				
				$opt_wrap_code = ""; 
				$opt_sticker_code = "";
				$opt_sticker_msg = "";
				$opt_print_msg = "";
				$opt_outbox_tf = "";
				$opt_manager_nm = "양진현"; 
				$opt_outstock_date = date("Y-m-d",strtotime("1 day")); //기본값으로 금일 + 1
				$con_delivery_type = "택배"; //이지웰 일반적으로 택배사용
				$delivery_price = "";
				$work_memo = "";
				$delivery_cp = "롯데택배"; //이지웰 일반적으로 롯데택배사용
				$sender_nm = "이지웰"; 
				$sender_phone = "031-527-6812";
				$delivery_cnt_in_box = $Q;

				insertTempOrderMROConversion($conn, $temp_no, $order_date, $cp_order_no, $con_cp_no, $goods_code, $goods_name, $sale_price, $qty, $o_mem_nm, $o_phone, $o_hphone, $r_mem_nm, $r_phone, $r_hphone, $zipcode, $r_addr1, $order_memo, $opt_wrap_code, $opt_sticker_code, $opt_sticker_msg, $opt_print_msg, $opt_outbox_tf, $opt_manager_nm, $opt_outstock_date, $con_delivery_type, $delivery_price, $work_memo, $delivery_cp, $sender_nm, $sender_phone, $delivery_cnt_in_box);

			}

		} catch (exception $e) {
			echo '엑셀파일을 읽는도중 오류가 발생하였습니다.';
		}
		
	}	

	$arr_rs = listTempOrderMROConversion($conn, $temp_no);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script type="text/javascript" src="../jquery/jquery-ui-timepicker-addon.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui-timepicker-addon.css" type="text/css" />
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
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
<script>
// 저장 버튼 클릭 시 
	function js_save() {
		
		var frm = document.frm;

		if (isNull(frm.file_nm.value)) {
			alert('파일을 선택해 주세요.');
			frm.file_nm.focus();
			return ;		
		}
		
		//AllowAttach(frm.file_nm);

		frm.mode.value = "FR";

		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
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

	function js_temp_goods_excel() {
		
		var frm = document.frm;

		frm.target = "";
		frm.action = "pop_order_MRO_conversion_excel_list.php";
		frm.submit();

	}

</script>
<style>
	table.rowstable01 tr.first_tr td { background-color: #ffffff; }
	table.rowstable01 tr.second_tr td { background-color: #f5f5f5;}
	table.rowstable01 tr.end td { border-bottom:2px solid black;}
</style>
</head>

<body id="popup_order_wide">

<div id="popupwrap_order_wide">
	<h1>이지웰 주문 - 통합엑셀로 변환</h1>
	<div id="postsch_code">

		<div class="addr_inp">
		

<form name="frm" method="post" enctype="multipart/form-data">

			<input type="hidden" name="mode" value="">
			<input type="hidden" name="temp_no" value="<?=$temp_no?>">

			<div class="sp10"></div>
			<table cellpadding="0" cellspacing="0" width="100%" class="colstable02">
				<colgroup>
					<col width="10%">
					<col width="35%">
					<col width="10%">
					<col width="35%">
					<col width="10%">
				</colgroup>
				<tr>
					<th>파일</th>
					<td class="line"><input type="file" name="file_nm" style="width:60%;" class="txt"></td>
					<th>주문일</th>
					<td class="line">
						<input type="text" class="txt datepicker" style="width: 150px; margin-right:3px;" name="this_date" value="<?=$this_date?>" maxlength="10"/>
					</td>
					<td class="line">
					<? if ($file_nm <> "" ) {?>
						<? if ($sPageRight_U == "Y") {?>
						<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
						<? } ?>
					<? } else {?>
						<? if ($sPageRight_I == "Y") {?>
						<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
						<? } ?>
					<? }?>
					</td>
				</tr>
			</table>
			<div class="sp15"></div>
			<?
				if(cntTempOrderMROConversion($conn, $temp_no) > 0) {
			
			?>
			
			<table cellpadding="0" cellspacing="0" width="100%" class="colstable02">
				<colgroup>
					<col width="10%">
					<col width="*">
				</colgroup>
				<tr>
					<th>변환된 파일 다운로드</th>
					<td class="line"><a href="javascript:js_temp_goods_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a></td>
				</tr>
			</table>
			<? } ?>
			<div class="sp15"></div>

			<table cellpadding="0" cellspacing="0" class="rowstable01">
				<colgroup>
					<col width="100">
					<col width="100">
					<col width="100">
					<col width="100">
					<col width="100">
					<col width="100">
					<col width="100">
					<col width="100">
					<col width="100">
					<col width="100">
					<col width="100">
					<col width="100">
					<col width="100">
					<col width="100">
				</colgroup>
				<thead>
					<tr>
						<th>판매업체주문번호</th>
						<th>매출업체</th>
						<th>주문자</th>
						<th>주문자연락처</th>
						<th>주문자휴대전화번호</th>
						<th>수취인</th>
						<th>수취인연락처</th>
						<th>수취인휴대전화번호</th>
						<th>우편번호</th>
						<th>주소</th>
						<th colspan="4">주문자메모</th>
					</tr>
					<tr>
						<th>상품코드</th>
						<th>상품명</th>
						<th>판매가</th>
						<th>주문수량</th>
						<th>포장지코드</th>
						<th>스티커코드</th>
						<th>스티커메세지</th>
						<th>인쇄메세지</th>
						<th>아웃박스스티커유무</th>
						<th>영업담당자</th>
						<th>출고예정일</th>
						<th>배송방식</th>
						<th>배송비</th>
						<th>작업메모</th>
					</tr>
				</thead>
				<tbody>
				<?
					if (sizeof($arr_rs) > 0) {
						
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
						
							$CP_ORDER_NO			= trim($arr_rs[$j]["CP_ORDER_NO"]);
							$CP_NO					= trim($arr_rs[$j]["CP_NO"]);
							$GOODS_CODE				= SetStringFromDB($arr_rs[$j]["GOODS_CODE"]);
							$GOODS_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
							$SALE_PRICE				= SetStringFromDB($arr_rs[$j]["SALE_PRICE"]);
							$QTY					= SetStringFromDB($arr_rs[$j]["QTY"]);
							$O_MEM_NM				= SetStringFromDB($arr_rs[$j]["O_MEM_NM"]);
							$O_PHONE				= SetStringFromDB($arr_rs[$j]["O_PHONE"]);
							$O_HPHONE				= SetStringFromDB($arr_rs[$j]["O_HPHONE"]);
							$R_MEM_NM				= SetStringFromDB($arr_rs[$j]["R_MEM_NM"]);
							$R_PHONE				= SetStringFromDB($arr_rs[$j]["R_PHONE"]);
							$R_HPHONE				= SetStringFromDB($arr_rs[$j]["R_HPHONE"]);
							$ZIPCODE				= SetStringFromDB($arr_rs[$j]["ZIPCODE"]);
							$R_ADDR1				= SetStringFromDB($arr_rs[$j]["R_ADDR1"]);
							$MEMO					= SetStringFromDB($arr_rs[$j]["MEMO"]);
							$OPT_WRAP_CODE			= SetStringFromDB($arr_rs[$j]["OPT_WRAP_CODE"]);
							$OPT_STICKER_CODE		= SetStringFromDB($arr_rs[$j]["OPT_STICKER_CODE"]);
							$OPT_STICKER_MSG		= SetStringFromDB($arr_rs[$j]["OPT_STICKER_MSG"]);
							$OPT_PRINT_MSG			= SetStringFromDB($arr_rs[$j]["OPT_PRINT_MSG"]);
							$OPT_OUTBOX_TF			= SetStringFromDB($arr_rs[$j]["OPT_OUTBOX_TF"]);
							$OPT_MANAGER_NM			= SetStringFromDB($arr_rs[$j]["OPT_MANAGER_NM"]);
							$OPT_OUTSTOCK_DATE		= SetStringFromDB($arr_rs[$j]["OPT_OUTSTOCK_DATE"]);
							$DELIVERY_TYPE			= SetStringFromDB($arr_rs[$j]["DELIVERY_TYPE"]);
							$DELIVERY_PRICE			= SetStringFromDB($arr_rs[$j]["DELIVERY_PRICE"]);
							$WORK_MEMO				= SetStringFromDB($arr_rs[$j]["WORK_MEMO"]);
					?>

					<tr class="first_tr">
						<td><?=$CP_ORDER_NO?></td>
						<td><?=$CP_NO?></td>
						<td><?=$O_MEM_NM?></td>
						<td><?=$O_PHONE?></td>
						<td><?=$O_HPHONE?></td>
						<td><?=$R_MEM_NM?></td>
						<td><?=$R_PHONE?></td>
						<td><?=$R_HPHONE?></td>
						<td><?=$ZIPCODE?></td>
						<td><?=$R_ADDR1?></td>
						<td colspan="4"><?=$MEMO?></td>
					</tr>
					<tr class="second_tr end">
						<td><?=$GOODS_CODE?></td>
						<td><?=$GOODS_NAME?></td>
						<td><?=$SALE_PRICE?></td>
						<td><?=$QTY?></td>
						<td><?=$OPT_WRAP_CODE?></td>
						<td><?=$OPT_STICKER_CODE?></td>
						<td><?=$OPT_STICKER_MSG?></td>
						<td><?=$OPT_PRINT_MSG?></td>
						<td><?=$OPT_OUTBOX_TF?></td>
						<td><?=$OPT_MANAGER_NM?></td>
						<td><?=$OPT_OUTSTOCK_DATE?></td>
						<td><?=$DELIVERY_TYPE?></td>
						<td><?=$DELIVERY_PRICE?></td>
						<td><?=$WORK_MEMO?></td>
					</tr>
				<?
						}
					}
				?>
				</tbody>
			</table>
</form>

		</div>
	</div>
	<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
</div>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>