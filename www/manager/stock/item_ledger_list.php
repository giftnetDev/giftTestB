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
	$menu_right = "SG019"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/stock/stock.php";
	

	if ($mode == "D") {
		$row_cnt = count($chk_no);

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_stock_no = $chk_no[$k];
			
			$result = deleteStock($conn, $str_stock_no, $s_adm_no);
		
		}
		
	}

#====================================================================
# Request Parameter
#====================================================================

	$mm_subtree	 = "3";

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

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	//$con_stock_code = "";
	//$con_stock_code = trim($con_stock_code);
	$cp_type2 = trim($cp_type2);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
	$del_tf = "N";
#============================================================
# Page process
#============================================================

	if ($nPage <> "") {
		$nPage = (int)($nPage);
	} else {
		$nPage = 1;
	}

	if ($nPageSize <> "") {
		$nPageSize = (int)($nPageSize);
	} else {
		$nPageSize = 20;
	}

	$nPageBlock	= 10;
	
#	echo $start_date;
#	echo $end_date;

	//두 상태 다 확인
	//$con_close_tf = "N";

#===============================================================
# Get Search list count
#===============================================================

	if($print_type <> "") { 
		//$order_field = " CASE WHEN A.STOCK_TYPE =  'IN' THEN A.IN_DATE ELSE A.OUT_DATE END "; 
		$order_str = " ASC ";
	}
		

	$nListCnt = totalCntStockInOut($conn, $search_date_type, $start_date, $end_date, $con_stock_type, $print_type, $cp_type2, $con_out_cp_no, $sel_loc, $con_close_tf, $del_tf, $search_field, $search_str);
	
	#echo $nListCnt;

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listStockInOut($conn, $search_date_type, $start_date, $end_date, $con_stock_type, $print_type, $cp_type2, $con_out_cp_no, $sel_loc, $con_close_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize, $nListCnt);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<script type="text/javascript" src="../jquery/jquery.floatThead.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script>
  $(function() {
    $( ".datepicker" ).datepicker({
      buttonImage: "/manager/images/calendar/cal.gif",
      buttonImageOnly: true,
      buttonText: "Select date",
	  showOn: "both",
	  dateFormat: "yy-mm-dd",
	  changeMonth: true,
      changeYear: true,
	  beforeShow: function() {
        setTimeout(function(){
            $('.ui-datepicker').css('z-index', 99999999999999);
        }, 0);
	  }
    });

	$(".datepicker").keydown(function(){

		var value = $(this).val();

		if(value.length == 4 && value.lastIndexOf('-') == -1)
			$(this).val(value.substr(0, 4)+ "-" + value.substr(4)) ;

		if(value.length == 7 && value.lastIndexOf('-') == 4)
			$(this).val(value.substr(0, 8) + "-" + value.substr(8)) ;
	});

	$(".datepicker").blur(function(){
		if($(this).val().length > 8)
			checkStaEndDt($("input[name=start_date]"), $("input[name=end_date]"));
	});
  });
</script>
<script>
	$(function(){
		$('table.fixed_header_table').floatThead({
			position: 'fixed'
		});
	});
</script>
<script type="text/javascript">

	// tag 관련 레이어가 다 로드 되기전인지 판별하기 위해 필요
	var tag_flag = "0";

	var checkFirst = false;
	var lastKeyword = '';
	var loopSendKeyword = false;
	
	function startSuggest() {

		if ((event.keyCode == 8) || (event.keyCode == 46)) {
			checkFirst = false;
			loopSendKeyword = false;
		}

		if (checkFirst == false) {
			setTimeout("sendKeyword();", 100);
			loopSendKeyword = true;
		}
		checkFirst = true;
	}

	function sendKeyword() {
		
		var frm = document.frm;

		if (loopSendKeyword == false) return;

		var keyword = document.frm.search_name.value;
		
		if (keyword == '') {
			
			lastKeyword = '';
		
			hide('suggest');

		} else if (keyword != lastKeyword) {

			lastKeyword = keyword;
				
			if (keyword != '') {
				frm.keyword.value = keyword;
				frm.exclude_category.value = "14"; 
				frm.goods_type.value = "unit";
				frm.action = "/manager/goods/search_goods.php";
				frm.target = "ifr_hidden";
				frm.submit();
				
			} else {
				hide('suggest');
			}
		}
		setTimeout("sendKeyword();", 100);
	}

	function displayResult(str) {

		var resultText = str;
		
		var result = resultText.split('|');

		var count = parseInt(result[0]);

		var keywordList = null;
		var arr_keywordList = null;

		if (count > 0) {
					
			keywordList = result[1].split('^');
			
			var html = '';
			
			for (var i = 0 ; i < keywordList.length ; i++) {
						
				arr_keywordList = keywordList[i].split('');

				// arr_keywordList[7]; 매입가
				// arr_keywordList[4]; 재고
				// arr_keywordList[8]; 판매상황
				
				html += "<table width='100%' border='0'><tr><td style='padding:0px 5px 0px 5px' width='55px'><img src='"+arr_keywordList[2]+"' width='50' height='50' border='0'></td>";
				html += "<td>" +"<a href=\"javascript:js_select('"+ arr_keywordList[0]+"','"+arr_keywordList[1]+"')\">"+arr_keywordList[1]+"</a>" + "</td>";
				html += "<td width='55px'>"+arr_keywordList[8]+"</td>";
				html += "<td width='105px'>정상 : "+arr_keywordList[4]+"</td><td width='105px'>불량 : "+arr_keywordList[5]+"</td></tr></table>";
		
			}

			var listView = document.getElementById('suggestList');
			listView.innerHTML = html;
					
			suggest.style.visibility  ="visible"; 
		} else {
			suggest.style.visibility  ="hidden"; 
		}
	}

	function js_select(selectedKey,selectedKeyword) {

		var frm = document.frm;

		frm.search_name.value = selectedKeyword;

		arr_keywordValues = selectedKey.split('');

		frm.search_field.value					= "GOODS_CODE";
		frm.search_str.value					= arr_keywordValues[4];

		js_search();
		
		loopSendKeyword = false;
		checkFirst = false;
		hide('suggest');

	}

	function show(elementId) {
		var element = document.getElementById(elementId);
		
		if (element) {
			element.style.visibility  ="visible"; 
			//element.style.display = '';
		}
	}

	function hide(elementId) {
		var element = document.getElementById(elementId);
		if (element) {
			element.style.visibility  ="hidden"; 
			//element.style.display = 'none';
		}
	}

<?
	$day_0 = date("Y-m-d",strtotime("0 month"));
	$day_1 = date("Y-m-d",strtotime("-1 day"));
	$day_7 = date("Y-m-d",strtotime("-7 day"));
	$day_31 = date("Y-m-d",strtotime("-1 month"));
	$day_180 = date("Y-m-d",strtotime("-6 month"));
	$day_365 = date("Y-m-d",strtotime("-12 month"));
	$day_1095 = date("Y-m-d",strtotime("-36 month"));
?>
	var day_0 = "<?=$day_0?>";
	var day_1 = "<?=$day_1?>";
	var day_7 = "<?=$day_7?>";
	var day_31 = "<?=$day_31?>";
	var day_180 = "<?=$day_180?>";
	var day_365 = "<?=$day_365?>";
	var day_1095 = "<?=$day_1095?>";

	function js_search_date(iday) {

		var frm = document.frm;
		
		if (iday == 0) {
			frm.start_date.value = day_0;
			frm.end_date.value = day_0;
		}

		if (iday == 1) {
			frm.start_date.value = day_1;
			frm.end_date.value = day_0;
		}

		if (iday == 7) {
			frm.start_date.value = day_7;
			frm.end_date.value = day_0;
		}

		if (iday == 31) {
			frm.start_date.value = day_31;
			frm.end_date.value = day_0;
		}

		if (iday == 180) {
			frm.start_date.value = day_180;
			frm.end_date.value = day_0;
		}

		if (iday == 365) {
			frm.start_date.value = day_365;
			frm.end_date.value = day_0;
		}

		if (iday == 1095) {
			frm.start_date.value = day_1095;
			frm.end_date.value = day_0;
		}

		frm.nPage.value = "1";
		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	// 조회 버튼 클릭 시 
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_all_check() {
		var frm = document.frm;
		
		if (frm['chk_no[]'] != null) {
			
			if (frm['chk_no[]'].length != null) {

				if (frm.all_chk.checked == true) {
					for (i = 0; i < frm['chk_no[]'].length; i++) {
						frm['chk_no[]'][i].checked = true;
					}
				} else {
					for (i = 0; i < frm['chk_no[]'].length; i++) {
						frm['chk_no[]'][i].checked = false;
					}
				}
			} else {
			
				if (frm.all_chk.checked == true) {
					frm['chk_no[]'].checked = true;
				} else {
					frm['chk_no[]'].checked = false;
				}
			}
		}
	}

	function js_delete() {
		var frm = document.frm;

		bDelOK = confirm('정말로 삭제하시겠습니까?');
		
		if (bDelOK==true) {
			
			frm.mode.value = "D";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}
	

	function js_excel() {

		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.target = "";
		frm.action = "<?=str_replace("list","excel_list",$_SERVER[PHP_SELF])?>";
		frm.submit();

	}

	function js_reload() {
		location.reload();
	}

	function js_view_order(rn, reserve_no) {

		var frm = document.frm;
		
		var url = "/manager/order/order_read.php?reserve_no="+reserve_no;

		NewWindow(url, 'order_detail','860','600','YES');
		
	}

	function js_view_goods_request(req_no) {

		/*
		var frm = document.frm;

		frm.req_no.value = req_no;
		frm.target = "";
		frm.method = "get";
		frm.action = "goods_request_write.php";
		frm.submit();
		*/
		var url = "pop_goods_request.php?req_no=" + req_no;

		NewWindow(url, 'pop_goods_request','1024','600','YES');
		
	}

	function js_stock_memo_view(stock_no) {

		var url = "pop_stock_memo.php?stock_no="+stock_no;
		NewWindow(url,'pop_stock_memo','820','700','YES');

	}

	function js_stock_memo_view(stock_no) {

		var url = "pop_stock_memo.php?stock_no="+stock_no;
		NewWindow(url,'pop_stock_memo','820','700','YES');

	}
</script>

</head>

<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="stock_no" value="">
<input type="hidden" name="use_tf" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<input type="hidden" name="keyword" value="">
<input type="hidden" name="exclude_category" value="">
<input type="hidden" name="goods_type" value="">
<!--<input type="hidden" name="nPageSize" value="<?=$nPageSize?>">-->
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
?>
		</td>
		<td class="contentarea">

			<!-- S: mwidthwrap -->
			<div id="mwidthwrap">

				<h2>입/출고 관리</h2>
				
				<table cellpadding="0" cellspacing="0" class="colstable">
					<colgroup>
						<col width="100" />
						<col width="*" />
						<col width="100" />
						<col width="*" />
						<col width="120" />
					</colgroup>
					<thead>
						<tr>
							<th>
								<select name="search_date_type">
									<option value="inout_date" <? if ($search_date_type == "inout_date" || $search_date_type == "") echo "selected" ?>>입/출고일</option>
									<!--<option value="reg_date" <? if ($search_date_type == "reg_date") echo "selected" ?>>등록일</option>-->
								</select>
							</th>
							<td colspan="3">
								<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="start_date" value="<?=$start_date?>" maxlength="10" readonly="1" /> ~
								<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="end_date" value="<?=$end_date?>" maxlength="10" readonly="1" />&nbsp;<a href="javascript:js_search_date('0');"><input type="button" class="btntxt" value="당일"></a>
							<a href="javascript:js_search_date('1');"><input type="button" class="btntxt" value="전일"></a>
							<a href="javascript:js_search_date('7');"><input type="button" class="btntxt" value="7일"></a>
							<a href="javascript:js_search_date('31');"><input type="button" class="btntxt" value="1개월"></a> <a href="javascript:js_search_date('180');"><input type="button" class="btntxt" value="6개월"></a> <a href="javascript:js_search_date('365');"><input type="button" class="btntxt" value="1년"></a> <a href="javascript:js_search_date('1095');"><input type="button" class="btntxt" value="3년"></a>
							</td>
							<td align="right">
								<select name="print_type">
									<option value="" <? if($print_type == "") echo "selected='selected'";?>>전체</option>
									<option value="F" <? if($print_type == "F") echo "selected='selected'";?>>가재고</option>
									<option value="N" <? if($print_type == "N") echo "selected='selected'";?>>정상재고</option>
									<option value="B" <? if($print_type == "B") echo "selected='selected'";?>>불량재고</option>
								</select>
							</td>
						</tr>
						<tr>
							<th>상품검색</th>
							<td colspan="3" style="position:relative" class="line">
								<div id="suggest" style="position:absolute; left:15px; top:25px; width:95%; height:81px; z-index:10001; visibility: hidden; width:95%; ">
									<div id="suggestList" style=" height:600px; overflow-y:auto; position:relative; border:solid 1px #cec6ba; background:#FFFFFF; width:95%; z-index:0; "></div>
								</div>
								<input type="text" class="txt search_name" style="width:95%; ime-mode:Active;" autocomplete="off" name="search_name" value="" onkeyup="startSuggest();" onFocus="this.value='';" placeholder="검색하실 상품을 입력 후 잠시 기다려주세요." />
							</td>
							<td align="right">
								
							</td>
							
							
						</tr>
						<tr>
							<th>검색조건</th>
							<td colspan="3">
								<select name="nPageSize" style="width:84px;">
									<option value="20" <? if ($nPageSize == "20") echo "selected"; ?> >20개씩</option>
									<option value="50" <? if ($nPageSize == "50") echo "selected"; ?> >50개씩</option>
									<option value="100" <? if ($nPageSize == "100") echo "selected"; ?> >100개씩</option>
									<option value="200" <? if ($nPageSize == "200") echo "selected"; ?> >200개씩</option>
									<option value="300" <? if ($nPageSize == "300") echo "selected"; ?> >300개씩</option>
									<option value="400" <? if ($nPageSize == "400") echo "selected"; ?> >400개씩</option>
									<option value="500" <? if ($nPageSize == "500") echo "selected"; ?> >500개씩</option>
									<option value="100000" <? if ($nPageSize == "100000") echo "selected"; ?> >기록전체(주의)</option>
								</select>&nbsp;
								<select name="search_field" style="width:84px;">
									<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >통합검색</option>
									<option value="GOODS_CODE" <? if ($search_field == "GOODS_CODE") echo "selected"; ?> >상품코드</option>
									<option value="GOODS_NAME" <? if ($search_field == "GOODS_NAME") echo "selected"; ?> >상품명</option>
									<option value="GOODS_NO" <? if ($search_field == "GOODS_NO") echo "selected"; ?> >상품번호</option>
									<option value="RESERVE_NO" <? if ($search_field == "RESERVE_NO") echo "selected"; ?> >주문번호</option>
									<option value="IN_LOC_EXT" <? if ($search_field == "IN_LOC_EXT") echo "selected"; ?> >*사유상세</option>
									<option value="MEMO" <? if ($search_field == "MEMO") echo "selected"; ?> >*메모</option>
								</select>&nbsp;

								<input type="text" value="<?=$search_str?>" name="search_str" class="txt"  onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();"  />
								<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
							</td>
							<td align="right">
								<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>
							</td>
							
							
						</tr>
					</thead>
				</table>
				<div class="sp20"></div>
				<b>총 <?=$nListCnt?> 건</b>

				<? if($print_type == "F" || $print_type == "N" || $print_type == "B") { ?>
				<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table" border="0">
					<colgroup>
						<col width="2%" />
						<col width="8%" />
						<col width="6%" />
						<col width="*" />
						<col width="7%" />
						<col width="7%" />
						<col width="7%" />
						<col width="12%" />
						<col width="5%" />
						<col width="8%" />
						<col width="5%" />
						<col width="8%" />
						<col width="7%" />
					</colgroup> 
					<thead>
						<tr>
							<th rowspan="2"><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
							<th rowspan="2">입/출고일</th>
							<th rowspan="2">재고구분</th>
							<th rowspan="2">상품명</th>
							<th colspan="3">재고</th>
							<th rowspan="2">업체명</th>
							<th rowspan="2">사유</th>
							<th rowspan="2">사유상세</th>
							<th rowspan="2">메모</th>
							<th rowspan="2">등록일</th>
							<th class="end" rowspan="2">주문번호</th>
						</tr>
						<tr>
							<th>입고</th>
							<th>출고</th>
							<th>합계</th>
						</tr>
					</thead>
					<tbody>
					<?
						
						$ALL_IN_QTY = 0;
						$ALL_OUT_QTY = 0;
						$ALL_TOTAL_QTY = 0;

						if (sizeof($arr_rs) > 0) {
							for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

								$STR_DATE = "";
								$STR_COMPANY = "";
								$STR_STOCK_CODE = "";
								
								$rn								= trim($arr_rs[$j]["rn"]);
								$IN_DATE						= trim($arr_rs[$j]["IN_DATE"]);
								$OUT_DATE						= trim($arr_rs[$j]["OUT_DATE"]);
								$STOCK_NO						= trim($arr_rs[$j]["STOCK_NO"]);
								$STOCK_CODE						= trim($arr_rs[$j]["STOCK_CODE"]);
								$STOCK_TYPE						= trim($arr_rs[$j]["STOCK_TYPE"]);
								$GOODS_NAME						= SetStringFromDB(trim($arr_rs[$j]["GOODS_NAME"]));
								$GOODS_CODE						= SetStringFromDB(trim($arr_rs[$j]["GOODS_CODE"]));
								$IN_PRICE						= trim($arr_rs[$j]["IN_PRICE"]);
								$OUT_PRICE						= trim($arr_rs[$j]["OUT_PRICE"]);
								$IN_NQTY						= trim($arr_rs[$j]["IN_QTY"]);
								$IN_BQTY						= trim($arr_rs[$j]["IN_BQTY"]);
								$IN_FQTY						= trim($arr_rs[$j]["IN_FQTY"]);
								$OUT_NQTY						= trim($arr_rs[$j]["OUT_QTY"]);
								$OUT_BQTY						= trim($arr_rs[$j]["OUT_BQTY"]);
								$OUT_TQTY						= trim($arr_rs[$j]["OUT_TQTY"]);
								$PAY_DATE						= trim($arr_rs[$j]["PAY_DATE"]);
								$IN_CP_NO						= trim($arr_rs[$j]["IN_CP_NO"]);
								$OUT_CP_NO						= trim($arr_rs[$j]["OUT_CP_NO"]);
								
								$IN_LOC							= trim($arr_rs[$j]["IN_LOC"]);
								$IN_LOC_EXT						= trim($arr_rs[$j]["IN_LOC_EXT"]);
								$RESERVE_NO						= trim($arr_rs[$j]["RESERVE_NO"]);
								$ORDER_GOODS_NO					= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
								$RGN_NO							= trim($arr_rs[$j]["RGN_NO"]);
								$MEMO							= trim($arr_rs[$j]["MEMO"]);
								
								$CLOSE_TF						= trim($arr_rs[$j]["CLOSE_TF"]);
								$REG_DATE						= trim($arr_rs[$j]["REG_DATE"]);
								
								$IN_DATE			= date("Y-m-d",strtotime($IN_DATE));
								$OUT_DATE			= date("Y-m-d",strtotime($OUT_DATE));
								$REG_DATE			= date("Y-m-d H:i:s",strtotime($REG_DATE));

								//if ($PAY_DATE) $PAY_DATE = date("Y-m-d",strtotime($PAY_DATE));


								if($CLOSE_TF == "Y")
									$closed_style_tr = "class='closed'";
								else
									$closed_style_tr = "";

								if(trim($STOCK_TYPE) == "IN") { 
									$STR_DATE = $IN_DATE;
									$STR_COMPANY = getCompanyNameWithNoCode($conn, $IN_CP_NO);
									$STR_STOCK_CODE = getDcodeName($conn, "IN_ST", $STOCK_CODE);

									if(startsWith($STOCK_CODE, "F")) 
										$IN_QTY =  $IN_FQTY;
									else if(startsWith($STOCK_CODE, "N")) 
										$IN_QTY =  $IN_NQTY;
									else if(startsWith($STOCK_CODE, "B")) 
										$IN_QTY =  $IN_BQTY;

								} else {
									$STR_DATE = $OUT_DATE;
									$STR_COMPANY = getCompanyNameWithNoCode($conn, $OUT_CP_NO);
									$STR_STOCK_CODE = getDcodeName($conn, "OUT_ST", $STOCK_CODE);
	
									if(startsWith($STOCK_CODE, "F")) 
										$OUT_QTY =  $OUT_FQTY;
									else if(startsWith($STOCK_CODE, "N")) 
										$OUT_QTY =  $OUT_NQTY;
									else if(startsWith($STOCK_CODE, "B")) 
										$OUT_QTY =  $OUT_BQTY;
								}

								if($CLOSE_TF != "Y") { 
									$ALL_IN_QTY += $IN_QTY;
									$ALL_OUT_QTY += $OUT_QTY;
								}

						if($j == 0) { 
							if($nPage == 1) { 
								$arr_total = listSumStockInOut($conn, $search_date_type, $start_date, $end_date, $con_stock_type, $print_type, $cp_type2, $con_out_cp_no, $sel_loc, $con_close_tf, $del_tf, $search_field, $search_str, $nPage, $nPageSize, $nListCnt, $option);

								
							} else { 
								$option = array('BASE_DATE' => $STR_DATE, 'STOCK_NO'=> $STOCK_NO);
								$arr_total = listSumStockInOut($conn, $search_date_type, $start_date, $end_date, $con_stock_type, $print_type, $cp_type2, $con_out_cp_no, $sel_loc, $con_close_tf, $del_tf, $search_field, $search_str, $nPage, $nPageSize, $nListCnt, $option);
							}

							if (sizeof($arr_total) > 0 && ($print_type == "F" || $print_type == "N" || $print_type == "B")) {
								for ($o = 0 ; $o < sizeof($arr_total); $o++) {
									$SUM_PREV_QTY = $arr_total[$o]["SUM_PREV_QTY"];

								
						?>
						<tr height="37">
							<td class="order"></td>
							<td><?=$STR_DATE?></td>
							<td></td>
							<td class="modeual_nm"> <이전재고> </td>
							<td class="price"></td>
							<td class="price"></td>
							<td class="price"><?=number_format($SUM_PREV_QTY)?></td>
							<td class="modeual_nm"></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<?
								}
							}
						}

								if($CLOSE_TF != "Y") { 
									$SUM_PREV_QTY += ($IN_QTY - $OUT_QTY);
								}

						?>

						
						<tr height="37" <?=$closed_style_tr ?>>
							<td class="order"><input type="checkbox" name="chk_no[]" value="<?=$STOCK_NO?>"></td>
							<td><?=$STR_DATE?></td>
							<td><?=$STR_STOCK_CODE ?></td>
							<td class="modeual_nm">[<?=$GOODS_CODE?>]<br/> <?= $GOODS_NAME?></td>
							<td class="price"><?= number_format($IN_QTY)?></td>
							<td class="price" <?=($CLOSE_TF == "Y" ? "" : "style='color:red;'") ?>><?=number_format($OUT_QTY)?></td>
							<td class="price"><?=number_format($SUM_PREV_QTY)?></td>
							<td class="modeual_nm"><?=$STR_COMPANY?></td>
							<td><?= getDcodeName($conn, "LOC", $IN_LOC)?></td>
							<td><?=$IN_LOC_EXT?></td>
							<td onclick="javascript:js_stock_memo_view('<?=$STOCK_NO?>');"><?=$MEMO?></td>
							<td><?=$REG_DATE?></td>
							<td><?=getLinkScriptForOrderView($conn, $RESERVE_NO, $ORDER_GOODS_NO, $RGN_NO)?></td>
						</tr>
						<?
								$IN_QTY = 0;
								$OUT_QTY = 0;

							}

						?>
					</table>

			
			

					<!-- --------------------- 페이지 처리 화면 START -------------------------->
					<?
						# ==========================================================================
						#  페이징 처리
						# ==========================================================================
						if (sizeof($arr_rs) > 0) {
							#$search_field		= trim($search_field);
							#$search_str			= trim($search_str);
							//$sel_order_state, $cp_type, $cp_type2, $sel_pay_type, $con_use_tf,
							$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&start_date=".$start_date."&end_date=".$end_date;
							$strParam = $strParam."&search_date_type=".$search_date_type."&print_type=".$print_type;
							$strParam = $strParam."&order_field=".$order_field."&order_str=".$order_str;

					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- 페이지 처리 화면 END -------------------------->
					<br />
			

					<table cellpadding="0" cellspacing="0" class="rowstable" style="border:none;" border="0">
						<colgroup>
							<col width="2%" />
							<col width="8%" />
							<col width="6%" />
							<col width="*" />
							<col width="7%" />
							<col width="7%" />
							<col width="7%" />
							<col width="12%" />
							<col width="5%" />
							<col width="8%" />
							<col width="5%" />
							<col width="8%" />
							<col width="7%" />
						</colgroup> 
						<!-- 합계 -->
							<tr class="goods_end">
								<td colspan="13">
									&nbsp;
								</td>
							</tr>
							<?
								$arr_sum = totalStockInOut($conn, $search_date_type, $start_date, $end_date, $print_type, $search_field, $search_str);
								
								for ($k = 0 ; $k < sizeof($arr_sum); $k++) {
									$GOODS_NAME						= SetStringFromDB(trim($arr_sum[$k]["GOODS_NAME"]));
									$GOODS_CODE						= SetStringFromDB(trim($arr_sum[$k]["GOODS_CODE"]));

									$SUM_IN_QTY						= trim($arr_sum[$k]["SUM_IN_QTY"]);
									$SUM_IN_BQTY					= trim($arr_sum[$k]["SUM_IN_BQTY"]);
									$SUM_IN_FQTY					= trim($arr_sum[$k]["SUM_IN_FQTY"]);
									$SUM_OUT_QTY					= trim($arr_sum[$k]["SUM_OUT_QTY"]);
									$SUM_OUT_BQTY					= trim($arr_sum[$k]["SUM_OUT_BQTY"]);
									
									$SUM_TOTAL_IN_QTY				= trim($arr_sum[$k]["SUM_TOTAL_IN_QTY"]);
									$SUM_TOTAL_OUT_QTY				= trim($arr_sum[$k]["SUM_TOTAL_OUT_QTY"]);

									//주문번호로 검색할때는 재고가 아니라 합계라고 표기하고 가용재고는 없앰
									$is_total_show = $search_field != "RESERVE_NO";

							?>
							<tr class="goods_end" height="35">
								<td class="filedown" colspan="2">해당 기간 합계</td>
								<td>&nbsp;</td>
								<td class="modeual_nm">[<?=$GOODS_CODE?>] <?= $GOODS_NAME?></td>
								<td class="price"><b><?=number_format($SUM_TOTAL_IN_QTY)?></b></td>
								<td class="price"><b style="color:red;"><?=number_format($SUM_TOTAL_OUT_QTY)?></b></td>
								<td class="price">&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<? if($print_type == "N" && $is_total_show) { ?>
								<td><b>재고 합</b></td>
								<td class="price"><b style="color:blue;"><?=number_format($SUM_IN_QTY - $SUM_OUT_QTY)?></b></td>
								<? } else { ?>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<? } ?>
								<? if($print_type == "B" && $is_total_show) { ?>
								<td><b>재고 합</b></td>
								<td class="price"><b style="color:blue;"><?=number_format($SUM_IN_BQTY - $SUM_OUT_BQTY)?></b></td>
								<? } else { ?>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<? } ?>
							</tr>
							<?
								}
							?>
							<tr class="goods_end">
								<td colspan="13">
									&nbsp;
								</td>
							</tr>
							<?
								$arr_sum = totalStockInOut($conn, '', '', '', $print_type, $search_field, $search_str);
								
								for ($k = 0 ; $k < sizeof($arr_sum); $k++) {
									$GOODS_NAME						= SetStringFromDB(trim($arr_sum[$k]["GOODS_NAME"]));
									$GOODS_CODE						= SetStringFromDB(trim($arr_sum[$k]["GOODS_CODE"]));

									$SUM_IN_QTY						= trim($arr_sum[$k]["SUM_IN_QTY"]);
									$SUM_IN_BQTY					= trim($arr_sum[$k]["SUM_IN_BQTY"]);
									$SUM_IN_FQTY					= trim($arr_sum[$k]["SUM_IN_FQTY"]);
									$SUM_OUT_QTY					= trim($arr_sum[$k]["SUM_OUT_QTY"]);
									$SUM_OUT_BQTY					= trim($arr_sum[$k]["SUM_OUT_BQTY"]);

									$SUM_TOTAL_IN_QTY				= trim($arr_sum[$k]["SUM_TOTAL_IN_QTY"]);
									$SUM_TOTAL_OUT_QTY				= trim($arr_sum[$k]["SUM_TOTAL_OUT_QTY"]);
									
									//주문번호로 검색할때는 재고가 아니라 합계라고 표기하고 가용재고는 없앰
									$is_total_show = $search_field != "RESERVE_NO";
							?>
							<tr class="goods_end" height="35">
								<td class="filedown" colspan="2">전체 기간 합계</td>
								<td>&nbsp;</td>
								<td class="modeual_nm">[<?=$GOODS_CODE?>] <?= $GOODS_NAME?></td>
								<td class="price"><b><?=number_format($SUM_TOTAL_IN_QTY)?></b></td>
								<td class="price"><b style="color:red;"><?=number_format($SUM_TOTAL_OUT_QTY)?></b></td>
								<td class="price">&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<? if($print_type == "N" && $is_total_show) { ?>
								<td><b>재고 합</b></td>
								<td class="price"><b style="color:blue;"><?=number_format($SUM_IN_QTY - $SUM_OUT_QTY)?></b></td>
								<? } else { ?>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<? } ?>
								<? if($print_type == "B" && $is_total_show) { ?>
								<td><b>재고 합</b></td>
								<td class="price"><b style="color:blue;"><?=number_format($SUM_IN_BQTY - $SUM_OUT_BQTY)?></b></td>
								<? } else { ?>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<? } ?>
							</tr>
							<?
								}
							?>
						<?

						}else{
							
						?>
							<tr class="order">
								<td height="50" align="center" colspan="13">데이터가 없습니다. </td>
							</tr>
						<?
							}
						?>
					</tbody>
				</table>

				<? } else { ?>

				<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table" border="0">
					<colgroup>
						<col width="2%" />
						<col width="8%" />
						<col width="6%" />
						<col width="*" />
						<col width="5%" />
						<col width="5%" />
						<col width="5%" />
						<col width="5%" />
						<col width="5%" />
						<col width="7%" />
						<col width="5%" />
						<col width="8%" />
						<col width="5%" />
						<col width="8%" />
						<col width="7%" />
					</colgroup> 
					<thead>
						<tr>
							<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
							<th>입/출고일</th>
							<th>재고구분</th>
							<th>상품명</th>
							<th>가입고</th>
							<th>정상입고</th>
							<th>정상출고</th>
							<th>불량입고</th>
							<th>불량출고</th>
							<th>업체명</th>
							<th>사유</th>
							<th>사유상세</th>
							<th>메모</th>
							<th>등록일</th>
							<th class="end">주문번호</th>
						</tr>
						<tr>
							
						</tr>
					</thead>
					<tbody>
					<?
						$nCnt = 0;
						
						$ALL_IN_QTY = 0;
						$ALL_OUT_QTY = 0;
						$ALL_IN_BQTY = 0;
						$ALL_OUT_BQTY = 0;
						$ALL_IN_FQTY = 0;
						$ALL_OUT_TQTY = 0;

						if (sizeof($arr_rs) > 0) {
							for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

								$STR_DATE = "";
								$STR_COMPANY = "";
								$STR_STOCK_CODE = "";
								
								$rn								= trim($arr_rs[$j]["rn"]);
								$IN_DATE						= trim($arr_rs[$j]["IN_DATE"]);
								$OUT_DATE						= trim($arr_rs[$j]["OUT_DATE"]);
								$STOCK_NO						= trim($arr_rs[$j]["STOCK_NO"]);
								$STOCK_CODE						= trim($arr_rs[$j]["STOCK_CODE"]);
								$STOCK_TYPE						= trim($arr_rs[$j]["STOCK_TYPE"]);
								$GOODS_NAME						= SetStringFromDB(trim($arr_rs[$j]["GOODS_NAME"]));
								$GOODS_CODE						= SetStringFromDB(trim($arr_rs[$j]["GOODS_CODE"]));
								$IN_PRICE						= trim($arr_rs[$j]["IN_PRICE"]);
								$OUT_PRICE						= trim($arr_rs[$j]["OUT_PRICE"]);
								$IN_QTY							= trim($arr_rs[$j]["IN_QTY"]);
								$IN_BQTY						= trim($arr_rs[$j]["IN_BQTY"]);
								$IN_FQTY						= trim($arr_rs[$j]["IN_FQTY"]);
								$OUT_QTY						= trim($arr_rs[$j]["OUT_QTY"]);
								$OUT_BQTY						= trim($arr_rs[$j]["OUT_BQTY"]);
								$OUT_TQTY						= trim($arr_rs[$j]["OUT_TQTY"]);
								$PAY_DATE						= trim($arr_rs[$j]["PAY_DATE"]);
								$IN_CP_NO						= trim($arr_rs[$j]["IN_CP_NO"]);
								$OUT_CP_NO						= trim($arr_rs[$j]["OUT_CP_NO"]);
								
								$IN_LOC							= trim($arr_rs[$j]["IN_LOC"]);
								$IN_LOC_EXT						= trim($arr_rs[$j]["IN_LOC_EXT"]);
								$RESERVE_NO						= trim($arr_rs[$j]["RESERVE_NO"]);
								$ORDER_GOODS_NO					= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
								$RGN_NO							= trim($arr_rs[$j]["RGN_NO"]);
								$MEMO							= trim($arr_rs[$j]["MEMO"]);
								
								$CLOSE_TF						= trim($arr_rs[$j]["CLOSE_TF"]);
								$REG_DATE						= trim($arr_rs[$j]["REG_DATE"]);
								
								$IN_DATE			= date("Y-m-d",strtotime($IN_DATE));
								$OUT_DATE			= date("Y-m-d",strtotime($OUT_DATE));
								$REG_DATE			= date("Y-m-d H:i:s",strtotime($REG_DATE));

								//if ($PAY_DATE) $PAY_DATE = date("Y-m-d",strtotime($PAY_DATE));

								if($CLOSE_TF == "Y")
									$closed_style_tr = "class='closed'";
								else
									$closed_style_tr = "";

								if(trim($STOCK_TYPE) == "IN") { 
									$STR_DATE = $IN_DATE;
									$STR_COMPANY = getCompanyNameWithNoCode($conn, $IN_CP_NO);
									$STR_STOCK_CODE = getDcodeName($conn, "IN_ST", $STOCK_CODE);
								} else {
									$STR_DATE = $OUT_DATE;
									$STR_COMPANY = getCompanyNameWithNoCode($conn, $OUT_CP_NO);
									$STR_STOCK_CODE = getDcodeName($conn, "OUT_ST", $STOCK_CODE);
								}

								if($STR_STOCK_CODE == "선출고") continue;

								if($CLOSE_TF != "Y") { 
									$ALL_IN_QTY = $ALL_IN_QTY + $IN_QTY;
									$ALL_OUT_QTY = $ALL_OUT_QTY + $OUT_QTY;
									$ALL_IN_BQTY = $ALL_IN_BQTY + $IN_BQTY;
									$ALL_OUT_BQTY = $ALL_OUT_BQTY + $OUT_BQTY;
									$ALL_IN_FQTY = $ALL_IN_FQTY + $IN_FQTY;
									//$ALL_OUT_TQTY = $ALL_OUT_TQTY + $OUT_TQTY;
								}

								$STR_TITLE = "부분합 : 가입고 ".$ALL_IN_FQTY.", 정상입고 ".$ALL_IN_QTY.", 선출고 -, 정상출고 ".$ALL_OUT_QTY.", 불량입고 ".$ALL_IN_BQTY.", 불량출고 ".$ALL_OUT_BQTY." ";
					?>
						<tr height="37" <?=$closed_style_tr ?>  title="<?=$STR_TITLE?>">
							<td class="order"><input type="checkbox" name="chk_no[]" value="<?=$STOCK_NO?>"></td>
							<td><?=$STR_DATE?></td>
							<td><?=$STR_STOCK_CODE ?></td>
							<td class="modeual_nm">[<?=$GOODS_CODE?>]<br/> <?= $GOODS_NAME?></td>
							<td class="price"><?=(startsWith($STOCK_CODE, 'FST') ? number_format($IN_FQTY) : "")?></td>
							<td class="price"><?=(startsWith($STOCK_CODE, 'NST') ? number_format($IN_QTY) : "")?></td>
							<td class="price" <?=($CLOSE_TF == "Y" ? "" : "style='color:red;'") ?>><?=(startsWith($STOCK_CODE, 'NOUT') ? number_format($OUT_QTY) : "")?></td>
							<td class="price"><?=(startsWith($STOCK_CODE, 'BST') ? number_format($IN_BQTY) : "")?></td>
							<td class="price" <?=($CLOSE_TF == "Y" ? "" : "style='color:red;'") ?>><?=(startsWith($STOCK_CODE, 'BOUT') ? number_format($OUT_BQTY) : "")?></td>
							<td class="modeual_nm"><?=$STR_COMPANY?></td>
							<td><?= getDcodeName($conn, "LOC", $IN_LOC)?></td>
							<td><?=$IN_LOC_EXT?></td>
							<td onclick="javascript:js_stock_memo_view('<?=$STOCK_NO?>');"><?=$MEMO?></td>
							<td><?=$REG_DATE?></td>
							<td><?=getLinkScriptForOrderView($conn, $RESERVE_NO, $ORDER_GOODS_NO, $RGN_NO)?></td>
						</tr>
						<?

							}

						?>
					</table>

			
			

					<!-- --------------------- 페이지 처리 화면 START -------------------------->
					<?
						# ==========================================================================
						#  페이징 처리
						# ==========================================================================
						if (sizeof($arr_rs) > 0) {
							#$search_field		= trim($search_field);
							#$search_str			= trim($search_str);
							//$sel_order_state, $cp_type, $cp_type2, $sel_pay_type, $con_use_tf,
							$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&start_date=".$start_date."&end_date=".$end_date;
							$strParam = $strParam."&search_date_type=".$search_date_type;
							$strParam = $strParam."&order_field=".$order_field."&order_str=".$order_str;

					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- 페이지 처리 화면 END -------------------------->
					<br />
			

					<table cellpadding="0" cellspacing="0" class="rowstable" style="border:none;" border="0">
					<colgroup>
						<col width="2%" />
						<col width="8%" />
						<col width="6%" />
						<col width="*" />
						<col width="5%" />
						<col width="5%" />
						<col width="5%" />
						<col width="5%" />
						<col width="5%" />
						<col width="7%" />
						<col width="5%" />
						<col width="8%" />
						<col width="5%" />
						<col width="8%" />
						<col width="7%" />
					</colgroup> 
						<!-- 합계 -->
							<tr class="goods_end">
								<td colspan="15">
									&nbsp;
								</td>
							</tr>
							<tr class="goods_end" height="35">
								<td class="filedown" colspan="2">페이지 합계</td>
								<td>&nbsp;</td>
								<td class="modeual_nm"></td>
								<td class="price"><b><?=number_format($ALL_IN_FQTY)?></b></td>
								<td class="price"><b><?=number_format($ALL_IN_QTY)?></b></td>
								<td class="price"><b style="color:red;"><?=number_format($ALL_OUT_QTY)?></b></td>
								<td class="price"><b><?=number_format($ALL_IN_BQTY)?></b></td>
								<td class="price"><b style="color:red;"><?=number_format($ALL_OUT_BQTY)?></b></td>
								<td colspan="6">&nbsp;</td>
							</tr>
							<tr class="goods_end">
								<td colspan="15">
									&nbsp;
								</td>
							</tr>
							<?
								$arr_sum = totalStockInOut($conn, $search_date_type, $start_date, $end_date, '', $search_field, $search_str);
								
								for ($k = 0 ; $k < sizeof($arr_sum); $k++) {
									$GOODS_NAME						= SetStringFromDB(trim($arr_sum[$k]["GOODS_NAME"]));
									$GOODS_CODE						= SetStringFromDB(trim($arr_sum[$k]["GOODS_CODE"]));
									$SUM_IN_QTY						= trim($arr_sum[$k]["SUM_IN_QTY"]);
									$SUM_IN_BQTY					= trim($arr_sum[$k]["SUM_IN_BQTY"]);
									$SUM_IN_FQTY					= trim($arr_sum[$k]["SUM_IN_FQTY"]);
									$SUM_OUT_QTY					= trim($arr_sum[$k]["SUM_OUT_QTY"]);
									$SUM_OUT_BQTY					= trim($arr_sum[$k]["SUM_OUT_BQTY"]);
									//$SUM_OUT_TQTY					= trim($arr_sum[$k]["SUM_OUT_TQTY"]);

								//주문번호로 검색할때는 재고가 아니라 합계라고 표기하고 가용재고는 없앰
								$is_total_show = $search_field != "RESERVE_NO";
							?>
							<tr class="goods_end" height="35">
								<td class="filedown" colspan="2">해당 기간 합계</td>
								<td>&nbsp;</td>
								<td class="modeual_nm">[<?=$GOODS_CODE?>] <?= $GOODS_NAME?></td>
								<td class="price"><b><?=number_format($SUM_IN_FQTY)?></b></td>
								<td class="price"><b><?=number_format($SUM_IN_QTY)?></b></td>
								<td class="price"><b style="color:red;"><?=number_format($SUM_OUT_QTY)?></b></td>
								<td class="price"><b><?=number_format($SUM_IN_BQTY)?></b></td>
								<td class="price"><b style="color:red;"><?=number_format($SUM_OUT_BQTY)?></b></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<? if($is_total_show) { ?>
								<td><b>정상재고</b></td>
								<td class="price"><b style="color:blue;"><?=number_format($SUM_IN_QTY - $SUM_OUT_QTY)?></b></td>
								<td><b>불량재고</b></td>
								<td class="price"><b style="color:blue;"><?=number_format($SUM_IN_BQTY - $SUM_OUT_BQTY)?></b></td>
								<? } else { ?>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<? } ?>
							</tr>
							
							<?
								}
							?>
							<tr class="goods_end">
								<td colspan="15">
									&nbsp;
								</td>
							</tr>
							<?
								$arr_sum = totalStockInOut($conn, '', '', '', '', $search_field, $search_str);
								
								for ($k = 0 ; $k < sizeof($arr_sum); $k++) {
									$GOODS_NAME						= SetStringFromDB(trim($arr_sum[$k]["GOODS_NAME"]));
									$GOODS_CODE						= SetStringFromDB(trim($arr_sum[$k]["GOODS_CODE"]));
									$SUM_IN_QTY						= trim($arr_sum[$k]["SUM_IN_QTY"]);
									$SUM_IN_BQTY					= trim($arr_sum[$k]["SUM_IN_BQTY"]);
									$SUM_IN_FQTY					= trim($arr_sum[$k]["SUM_IN_FQTY"]);
									$SUM_OUT_QTY					= trim($arr_sum[$k]["SUM_OUT_QTY"]);
									$SUM_OUT_BQTY					= trim($arr_sum[$k]["SUM_OUT_BQTY"]);

								//주문번호로 검색할때는 재고가 아니라 합계라고 표기하고 가용재고는 없앰
								$is_total_show = $search_field != "RESERVE_NO";
							?>
							<tr class="goods_end" height="35">
								<td class="filedown" colspan="2">전체 기간 합계</td>
								<td>&nbsp;</td>
								<td class="modeual_nm">[<?=$GOODS_CODE?>] <?= $GOODS_NAME?></td>
								<td class="price"><b><?=number_format($SUM_IN_FQTY)?></b></td>
								<td class="price"><b><?=number_format($SUM_IN_QTY)?></b></td>
								<td class="price"><b style="color:red;"><?=number_format($SUM_OUT_QTY)?></b></td>
								<td class="price"><b><?=number_format($SUM_IN_BQTY)?></b></td>
								<td class="price"><b style="color:red;"><?=number_format($SUM_OUT_BQTY)?></b></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<? if($is_total_show) { ?>
								<td><b>정상재고</b></td>
								<td class="price"><b style="color:blue;"><?=number_format($SUM_IN_QTY - $SUM_OUT_QTY)?></b></td>
								<td><b>불량재고</b></td>
								<td class="price"><b style="color:blue;"><?=number_format($SUM_IN_BQTY - $SUM_OUT_BQTY)?></b></td>
								<? } else { ?>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<? } ?>
							</tr>
							
							<?
								}
							?>
						<?

						}else{
							
						?>
							<tr class="order">
								<td height="50" align="center" colspan="15">데이터가 없습니다. </td>
							</tr>
						<?
							}
						?>
					</tbody>
				</table>


				<? } ?>

				<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">
				<? if (($sPageRight_D == "Y") && ($s_adm_cp_type == "운영")) {?>
					<input type="button" name="aa" value=" 선택한 입/출고 삭제 " class="btntxt" onclick="js_delete();"> 
				<? } ?>
				</div>

				

				<div class="sp10"></div>
		<!-- // E: mwidthwrap -->
			</div>
			<!-- // E: mwidthwrap -->

		</td>
	</tr>
	</table>
</div>
<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>