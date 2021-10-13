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
	$menu_right = "OD018"; // 메뉴마다 셋팅 해 주어야 합니다

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

#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);

#====================================================================
# DML Process
#====================================================================
	if ($mode == "FR") {
	
	#====================================================================
		$savedir1 = $g_physical_path."upload_data/temp_delivery_return";
	#====================================================================

		$file_nm	= upload($_FILES[file_nm], $savedir1, 10000 , array('xls'));

		require_once '../../_excel_reader/Excel/reader.php';
		$data = new Spreadsheet_Excel_Reader();
		$data->setOutputEncoding('euc-kr');
		$data->read("../../upload_data/temp_delivery_return/".$file_nm);
		$temp_no = $file_nm;

		error_reporting(E_ALL ^ E_NOTICE);

		for($i = 2 ; $i <= $data->sheets[0]['numRows']; $i++) {
			
			$A	= SetStringToDB(trim($data->sheets[0]['cells'][$i][1]));
			$B  = SetStringToDB(trim($data->sheets[0]['cells'][$i][2]));
			$C  = SetStringToDB(trim($data->sheets[0]['cells'][$i][3]));
			$D	= SetStringToDB(trim($data->sheets[0]['cells'][$i][4]));
			$E	= SetStringToDB(trim($data->sheets[0]['cells'][$i][5]));
			$F  = SetStringToDB(trim($data->sheets[0]['cells'][$i][6]));
			$G	= SetStringToDB(trim($data->sheets[0]['cells'][$i][7]));
			$H	= SetStringToDB(trim($data->sheets[0]['cells'][$i][8]));
			$I	= SetStringToDB(trim($data->sheets[0]['cells'][$i][9]));
			$J	= SetStringToDB(trim($data->sheets[0]['cells'][$i][10]));
			$K	= SetStringToDB(trim($data->sheets[0]['cells'][$i][11]));
			$L	= SetStringToDB(trim($data->sheets[0]['cells'][$i][12]));
			$M	= SetStringToDB(trim($data->sheets[0]['cells'][$i][13]));
			$N	= SetStringToDB(trim($data->sheets[0]['cells'][$i][14]));
			$O	= SetStringToDB(trim($data->sheets[0]['cells'][$i][15]));
			$P	= SetStringToDB(trim($data->sheets[0]['cells'][$i][16]));
			$Q	= SetStringToDB(trim($data->sheets[0]['cells'][$i][17]));
			$R	= SetStringToDB(trim($data->sheets[0]['cells'][$i][18]));
			$S	= SetStringToDB(trim($data->sheets[0]['cells'][$i][19]));
			$T	= SetStringToDB(trim($data->sheets[0]['cells'][$i][20]));
			$U	= SetStringToDB(trim($data->sheets[0]['cells'][$i][21]));
			$V	= SetStringToDB(trim($data->sheets[0]['cells'][$i][22]));
			$W	= SetStringToDB(trim($data->sheets[0]['cells'][$i][23]));
			$X	= SetStringToDB(trim($data->sheets[0]['cells'][$i][24]));
			$Y	= SetStringToDB(trim($data->sheets[0]['cells'][$i][25]));
			$Z	= SetStringToDB(trim($data->sheets[0]['cells'][$i][26]));
			$AA	= SetStringToDB(trim($data->sheets[0]['cells'][$i][27]));
			$AB	= SetStringToDB(trim($data->sheets[0]['cells'][$i][28]));
			$AC	= SetStringToDB(trim($data->sheets[0]['cells'][$i][29]));
			$AD	= SetStringToDB(trim($data->sheets[0]['cells'][$i][30]));
			$AE	= SetStringToDB(trim($data->sheets[0]['cells'][$i][31]));
			$AF	= SetStringToDB(trim($data->sheets[0]['cells'][$i][32]));
			$AG	= SetStringToDB(trim($data->sheets[0]['cells'][$i][33]));
			$AH	= SetStringToDB(trim($data->sheets[0]['cells'][$i][34]));
			$AI	= SetStringToDB(trim($data->sheets[0]['cells'][$i][35]));
			$AJ	= SetStringToDB(trim($data->sheets[0]['cells'][$i][36]));
			$AK	= SetStringToDB(trim($data->sheets[0]['cells'][$i][37]));

			if($AJ == "" && $AK == "") { 
			
				$DELIVERY_CP = "";
				$DELIVERY_NO = "";

				$arr_return = listOrderGoodsDeliveryReturn($conn, $B, $C);
				if(sizeof($arr_return) > 0) { 

					$DELIVERY_CP = $arr_return[0]["DELIVERY_CP"];
					$DELIVERY_NO = $arr_return[0]["DELIVERY_NO"];
				} else { 
					$DELIVERY_CP = "";
					$DELIVERY_NO = "";
				}
				$AJ = $DELIVERY_CP;
				$AK = $DELIVERY_NO;
			}
			

			insertTempOrderGoodsDeliveryReturn_Interpark($conn, $temp_no, $A, $B, $C, $D, $E, $F, $G, $H, $I, $J, $K, $L, $M, $N, $O, $P, $Q, $R, $S, $T, $U, $V, $W, $X, $Y, $Z, $AA, $AB, $AC, $AD, $AE, $AF, $AG, $AI, $AJ, $AK);

		}

		unset($data);
		//echo 'order_write_file.php?mode=L&temp_no='.$file_nm.'&this_date='.$this_date;
?>	
<script language="javascript">
		location.href =  '<?=$_SERVER[PHP_SELF]?>?mode=L&temp_no=<?=$file_nm?>';
</script>
<?
		exit;
	}

	if ($mode == "L") {
		$arr_rs = listTempOrderGoodsDeliveryReturn_Interpark($conn, $temp_no);
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
#ex_scroll { z-index: 1; background-color:#f7f7f7; overflow-x: visible; overflow-y: hidden; width: 100%; height:95px; border:1px solid #d1d1d1;}
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
		
		AllowAttach(frm.file_nm);

		if (isNull(file_rname)) {
			frm.mode.value = "FR";
		} else {
			frm.mode.value = "I";
		}

		frm.method = "post";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
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

	function js_excel() {
		
		var frm = document.frm;

		frm.target = "";
		
		frm.action = "order_delivery_paper_excel.php?temp_no=<?=$temp_no?>";
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
				<h2>주문서 송장반환</h2>
				<table cellpadding="0" cellspacing="0" class="colstable">

					<colgroup>
						<col width="12%">
						<col width="38%">
						<col width="12%">
						<col width="38%">
					</colgroup>
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
			</div>

			<div class="text_frame">* 송장 완료파일 &nbsp; <a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="송장 완료파일"></a>
			</div>
			<table cellpadding="0" cellspacing="0" class="rowstable" width="100%">

				<!--
				<colgroup>
					<col width="10%" />
					<col width="10%" />
					<col width="10%" />
					<col width="*" />
					<col width="10%" />
					<col width="10%" />
					<col width="10%" />
					<col width="10%" />
					<col width="10%" />
				</colgroup>
				
				<thead>
					<tr>
						<th>쇼핑몰코드</th><th>주문번호</th><th>상품코드</th><th>판매자상품코드</th><th>주문자ID</th><th>주문자</th><th>주문자전화번호</th><th>주문자핸드폰</th><th>수령인</th><th>전화번호</th><th>핸드폰</th><th>결제일</th><th>주문일</th><th>주문상태</th><th>카테고리명</th><th>상품명</th><th>옵션</th><th>수량</th><th>판매가격</th><th>옵션가격</th><th>총판매가격</th><th>배송비</th><th>면과세</th><th>주소</th><th>주문시요구사항</th><th>회원등급별할인금액합계</th><th>쿠폰할인금액합계</th><th>결제방식</th><th>사용포인트</th><th>일반결재금액</th><th>회원그룹</th><th>송장등록일</th><th>취소완료일</th><th>반품완료일</th><th>고객사</th><th>택배사</th><th class="end">송장번호</th>
					</tr>
				</thead>
				-->
				<tbody>
				<?
					if (sizeof($arr_rs) > 0) {
						
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							$A	= SetStringFromDB($arr_rs[$j]["A"]);
							$B	= SetStringFromDB($arr_rs[$j]["B"]);
							$C	= SetStringFromDB($arr_rs[$j]["C"]);
							$D	= SetStringFromDB($arr_rs[$j]["D"]);
							$E	= SetStringFromDB($arr_rs[$j]["E"]);
							$F	= SetStringFromDB($arr_rs[$j]["F"]);
							$G	= SetStringFromDB($arr_rs[$j]["G"]);
							$H	= SetStringFromDB($arr_rs[$j]["H"]);
							$I	= SetStringFromDB($arr_rs[$j]["I"]);
							$J	= SetStringFromDB($arr_rs[$j]["J"]);
							$K	= SetStringFromDB($arr_rs[$j]["K"]);
							$L	= SetStringFromDB($arr_rs[$j]["L"]);
							$M	= SetStringFromDB($arr_rs[$j]["M"]);
							$N	= SetStringFromDB($arr_rs[$j]["N"]);
							$O	= SetStringFromDB($arr_rs[$j]["O"]);
							$P	= SetStringFromDB($arr_rs[$j]["P"]);
							$Q	= SetStringFromDB($arr_rs[$j]["Q"]);
							$R	= SetStringFromDB($arr_rs[$j]["R"]);
							$S	= SetStringFromDB($arr_rs[$j]["S"]);
							$T	= SetStringFromDB($arr_rs[$j]["T"]);
							$U	= SetStringFromDB($arr_rs[$j]["U"]);
							$V	= SetStringFromDB($arr_rs[$j]["V"]);
							$W	= SetStringFromDB($arr_rs[$j]["W"]);
							$X	= SetStringFromDB($arr_rs[$j]["X"]);
							$Y	= SetStringFromDB($arr_rs[$j]["Y"]);
							$Z	= SetStringFromDB($arr_rs[$j]["Z"]);
							$AA	= SetStringFromDB($arr_rs[$j]["AA"]);
							$AB	= SetStringFromDB($arr_rs[$j]["AB"]);
							$AC	= SetStringFromDB($arr_rs[$j]["AC"]);
							$AD	= SetStringFromDB($arr_rs[$j]["AD"]);
							$AE	= SetStringFromDB($arr_rs[$j]["AE"]);
							$AF	= SetStringFromDB($arr_rs[$j]["AF"]);
							$AG	= SetStringFromDB($arr_rs[$j]["AG"]);
							$AH	= SetStringFromDB($arr_rs[$j]["AH"]);
							$AI	= SetStringFromDB($arr_rs[$j]["AI"]);
							$AJ	= SetStringFromDB($arr_rs[$j]["AJ"]);
							$AK	= SetStringFromDB($arr_rs[$j]["AK"]);
				?>
					<tr>
						<td><?=$A?></td>
						<td><?=$B?></td>
						<td><?=$C?></td>
						<td><?=$D?></td>
						<td><?=$E?></td>
						<td><?=$F?></td>
						<td><?=$G?></td>
						<td><?=$H?></td>
						<td><?=$I?></td>
						<td><?=$J?></td>
						<td><?=$K?></td>
						<td><?=$L?></td>
						<td><?=$M?></td>
						<td><?=$N?></td>
						<td><?=$O?></td>
						<td><?=$P?></td>
						<td><?=$Q?></td>
						<td><?=$R?></td>
						<td><?=$S?></td>
						<td><?=$T?></td>
						<td><?=$U?></td>
						<td><?=$V?></td>
						<td><?=$W?></td>
						<td><?=$X?></td>
						<td><?=$Y?></td>
						<td><?=$Z?></td>
						<td><?=$AA?></td>
						<td><?=$AB?></td>
						<td><?=$AC?></td>
						<td><?=$AD?></td>
						<td><?=$AE?></td>
						<td><?=$AF?></td>
						<td><?=$AG?></td>
						<td><?=$AH?></td>
						<td><?=$AI?></td>
						<td><?=$AJ?></td>
						<td><?=$AK?></td>
					</tr>
				<?
						}
					}else {
				?>
					<tr>
						<td colspan="37" height="30">데이터가 없습니다</td>
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