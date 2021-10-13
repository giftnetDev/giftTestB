<?session_start();?>
<?
# =============================================================================
# File Name    : out_write.php
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "SG006"; // 메뉴마다 셋팅 해 주어야 합니다

	if ($pay_date == "") {
		$pay_date = date("Y-m-d",strtotime("0 month"));
	} else {
		$pay_date = trim($pay_date);
	}

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
	require "../../_classes/biz/stock/stock.php";

#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);
	
	//echo $pb_nm; 
	//echo $mode;
	
	$stock_no					= SetStringToDB($stock_no);
	$stock_type					= SetStringToDB($stock_type);
	$stock_code					= SetStringToDB($stock_code);
	$cp_type					= SetStringToDB($cp_type);
	$out_cp_no					= SetStringToDB($out_cp_no);
	$goods_no					= SetStringToDB($goods_no);
	$goods_code					= SetStringToDB($goods_code);
	$in_loc						= SetStringToDB($in_loc);
	$in_loc_ext					= SetStringToDB($in_loc_ext);
	$qty						= SetStringToDB($qty);
	$buy_price					= SetStringToDB($buy_price);
	$out_date					= SetStringToDB($out_date);
	$pay_date					= SetStringToDB($pay_date);
	
	$result	= false  ;

#====================================================================
# DML Process
#====================================================================
	if ($mode == "I") {
		
		$stock_type = "OUT";
		
		if (left($stock_code,1) == "N") {
			$out_qty		= $qty;
			$out_bqty	= 0;
			$out_tqty	= 0;
		} if (left($stock_code,1) == "B") {
			$out_qty		= 0;
			$out_bqty	= $qty;
			$out_tqty	= 0;
		} if (left($stock_code,1) == "T") {
			$out_qty		= 0;
			$out_bqty	= 0;
			$out_tqty	= $qty;
		}
	
		//echo $qty;
		//echo $in_qty;

		//exit;

		$out_cp_no = $cp_type;
		$out_price = $buy_price;
		$close_tf = "N";

		$result = insertStock($conn, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $order_goods_no, $rgn_no, $close_tf, $s_adm_no,$memo);
?>
<script type="text/javascript">
	
	var bDelOK = "";

	bDelOK = confirm('계속 등록 하시겠습니까?');

	if (bDelOK==true) {
		document.location = "out_write.php";
	} else {
		document.location = "out_list.php";
	}

</script>
<?
		mysql_close($conn);
		exit;
	}

	if ($mode == "S") {

		$arr_rs = selectStock($conn, $stock_no);

		$RS_STOCK_NO							= trim($arr_rs[0]["STOCK_NO"]); 
		$RS_STOCK_TYPE						= SetStringFromDB($arr_rs[0]["STOCK_TYPE"]); 
		$RS_STOCK_CODE						= SetStringFromDB($arr_rs[0]["STOCK_CODE"]); 
		$RS_IN_CP_NO							= SetStringFromDB($arr_rs[0]["IN_CP_NO"]); 
		$RS_OUT_CP_NO							= SetStringFromDB($arr_rs[0]["OUT_CP_NO"]); 
		$RS_GOODS_NO							= SetStringFromDB($arr_rs[0]["GOODS_NO"]); 
		$RS_GOODS_CODE						= SetStringFromDB($arr_rs[0]["GOODS_CODE"]); 
		$RS_IN_LOC								= SetStringFromDB($arr_rs[0]["IN_LOC"]); 
		$RS_IN_LOC_EXT						= SetStringFromDB($arr_rs[0]["IN_LOC_EXT"]); 
		$RS_IN_QTY								= SetStringFromDB($arr_rs[0]["IN_QTY"]); 
		$RS_IN_BQTY								= SetStringFromDB($arr_rs[0]["IN_BQTY"]); 
		$RS_IN_FQTY								= SetStringFromDB($arr_rs[0]["IN_FQTY"]); 
		$RS_OUT_QTY								= SetStringFromDB($arr_rs[0]["OUT_QTY"]); 
		$RS_OUT_BQTY							= SetStringFromDB($arr_rs[0]["OUT_BQTY"]); 
		$RS_OUT_TQTY							= SetStringFromDB($arr_rs[0]["OUT_TQTY"]); 
		$RS_IN_PRICE							= SetStringFromDB($arr_rs[0]["IN_PRICE"]); 
		$RS_OUT_PRICE							= SetStringFromDB($arr_rs[0]["OUT_PRICE"]); 
		$RS_IN_DATE								= SetStringFromDB($arr_rs[0]["IN_DATE"]); 
		$RS_OUT_DATE							= SetStringFromDB($arr_rs[0]["OUT_DATE"]); 
		$RS_PAY_DATE							= SetStringFromDB($arr_rs[0]["PAY_DATE"]); 
		$RS_CLOSE_TF							= SetStringFromDB($arr_rs[0]["CLOSE_TF"]); 
		$RS_REG_DATE							= SetStringFromDB($arr_rs[0]["REG_DATE"]); 
		$RS_MEMO							= SetStringFromDB($arr_rs[0]["MEMO"]); 
		$RS_GOODS_NAME						= SetStringFromDB($arr_rs[0]["GOODS_NAME"]); 
		$RS_GOODS_CODE						= SetStringFromDB($arr_rs[0]["GOODS_CODE"]); 
		$RS_RESERVE_NO						= SetStringFromDB($arr_rs[0]["RESERVE_NO"]); 

		$RS_OUT_DATE		= date("Y-m-d",strtotime($RS_OUT_DATE));
		$RS_PAY_DATE		= date("Y-m-d",strtotime($RS_PAY_DATE));

		//echo $RS_OUT_DATE;

		if (left($RS_STOCK_CODE,1) == "N") {
			$QTY = $RS_OUT_QTY;
		} else if (left($RS_STOCK_CODE,1) == "B") {
			$QTY = $RS_OUT_BQTY;
		} else if (left($RS_STOCK_CODE,1) == "T") {
			$QTY = $RS_OUT_TQTY;
		}

	} else {
		$RS_OUT_DATE = $pay_date;
		$RS_PAY_DATE = $pay_date;
	}

	if ($mode == "U") {

		$close_tf = "N";
		$stock_type = "OUT";

		if (left($stock_code,1) == "N") {
			$out_qty		= $qty;
			$out_bqty	= 0;
			$out_tqty	= 0;
		} if (left($stock_code,1) == "B") {
			$out_qty		= 0;
			$out_bqty	= $qty;
			$out_tqty	= 0;
		} if (left($stock_code,1) == "T") {
			$out_qty		= 0;
			$out_bqty	= 0;
			$out_tqty	= $qty;
		}

		$out_cp_no = $cp_type;
		$out_price = $buy_price;

		$result = updateStock($conn, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $in_price, $out_price, $reserve_no, $in_date, $out_date, $pay_date, $close_tf, $s_adm_no, $memo, $stock_no);

	}

	if ($mode == "D") {
		$result =  deleteStock($conn, $stock_no, $s_adm_no);
	}

	$strParam = $strParam."?nPage=".$nPage."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&start_date=".$start_date."&end_date=".$end_date;
	$strParam = $strParam."&con_stock_code=".$con_stock_code."&cp_type=".$cp_type."&cp_type2=".$cp_type2;
	$strParam = $strParam."&order_field=".$order_field."&order_str=".$order_str;
		
	if ($result) {
	
		if ($mode == "U") {
?>	
<script language="javascript">
	location.href =  "out_write.php<?=$strParam?>&mode=S&stock_no=<?=$stock_no?>";
</script>
<?
		} else {
?>	
<script language="javascript">
		alert('정상 처리 되었습니다.');
		location.href =  "out_list.php<?=$strParam?>";
</script>
<?
		}
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
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
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
<script>
	$(function() {
		$('.autocomplete_off').attr('autocomplete', 'off');
	});
</script>
<script language="javascript">

function js_delete() {

	var frm = document.frm;

	bDelOK = confirm('자료를 삭제 하시겠습니까?');
	
	if (bDelOK==true) {
		frm.mode.value = "D";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

}


	// 저장 버튼 클릭 시 
	function js_save() {
		
		var stock_no = "<?= $stock_no ?>";
		var frm = document.frm;


		if (isNull(frm.goods_no.value)) {
			alert('상품을 선택해주세요.');
			frm.goods_no.focus();
			return ;		
		}

		if (isNull(frm.stock_code.value)) {
			alert('출고구분을 선택해주세요.');
			frm.stock_code.focus();
			return ;		
		}

		if (isNull(frm.cp_type.value)) {
			alert('출고업체를 선택해주세요.');
			frm.cp_type.focus();
			return ;		
		}

		if (isNull(frm.qty.value)) {
			alert('수량을 입력해주세요.');
			frm.qty.focus();
			return ;		
		}

		if (isNull(frm.buy_price.value)) {
			alert('매입가를 입력해주세요.');
			frm.buy_price.focus();
			return ;		
		}

		if (isNull(frm.in_loc.value)) {
			alert('사유를 선택해주세요.');
			frm.in_loc.focus();
			return ;		
		}
		

		if (isNull(frm.out_date.value)) {
			alert('출고일을 입력해주세요.');
			frm.out_date.focus();
			return;
		}
/*
		if (isNull(frm.pay_date.value)) {
			alert('결제일을 입력해주세요.');
			frm.pay_date.focus();
			return ;		
		}
*/
		if (stock_no == "") {
			frm.mode.value = "I";
		} else {
			frm.mode.value = "U";
		}

		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

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
				frm.action = "/manager/goods/search_goods.php";
				frm.target = "ifr_hidden";
				frm.submit();
			} else {
				hide('suggest');
			}
		}
		setTimeout("sendKeyword();", 100);
	}

	function searchKeyword() {
		var frm = document.frm;
		var keyword = document.frm.search_name.value;
		frm.keyword.value = keyword;
		frm.action = "/manager/goods/search_goods.php";
		frm.target = "ifr_hidden";
		frm.submit();
	}

	function displayResult(str) {

//		if (httpRequest.readyState == 4) {
//			if (httpRequest.status == 200) {
				
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
				
				html += "<table width='100%' border='0'><tr><td style='padding:0px 5px 0px 5px' width='55px'><img src='"+arr_keywordList[2]+"' width='50' height='50' border='0'></td><td><a href=\"javascript:js_select('"+
				arr_keywordList[0]+"','"+arr_keywordList[1]+"')\">"+
				arr_keywordList[1]+"</a></td><td width='105px'>판매가 : "+arr_keywordList[3]+"</td></tr></table>";
		
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

		frm.goods_name.value					= arr_keywordValues[0];
		frm.goods_no.value						= arr_keywordValues[1];
		frm.goods_buy_price.value			= arr_keywordValues[2];
		//frm.goods_sale_price.value		= arr_keywordValues[3];

		loopSendKeyword = false;
		checkFirst = false;
		hide('suggest');

		//document.getElementById('goods_detail').src = "order_goods_detail.php?goods_no="+frm.goods_no.value+"&cp_no=<?=$cp_no?>&mode=S";

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

	function js_list() {
		//var frm = document.frm;

		//frm.target = "";
		//frm.method = "post";
		//frm.action = "out_list.php";
		//frm.submit();

		location.href =  "out_list.php<?=$strParam?>";
	}

</script>
</head>
<body id="admin">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="keyword" value="">
<input type="hidden" name="goods_name" value="<?=$RS_GOODS_NAME?>">
<input type="hidden" name="goods_no" value="<?=$RS_GOODS_NO?>">
<input type="hidden" name="stock_no" value="<?= $RS_STOCK_NO?>">
<input type="hidden" name="search_field" value="<?= $search_field ?>">
<input type="hidden" name="search_str" value="<?= $search_str ?>">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>">
<input type="hidden" name="goods_type" value="unit">

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
				<h2>출고 등록</h2>  
				<div class="sp5"></div>

				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="16%">
					<col width="34%">
					<col width="16%">
					<col width="34%">
				</colgroup>
				<thead>
					<tr>
						<th>상품검색</th>
						<td colspan="5" style="position:relative" class="line">
							<div id="suggest" style="position:absolute; left:15px; top:25px; width:95%; height:81px; z-index:100; visibility: hidden; width:95%; ">
								<div id="suggestList" style="position:relative; border:solid 1px #cec6ba; background:#FFFFFF; width:95%;"></div>
							</div>
							<input type="text" class="txt" style="width:95%; ime-mode:Active;" autocomplete="off" name="search_name" required value="<?=$RS_GOODS_NAME?>" onKeyDown="startSuggest();" />
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>출고구분</th>
						<td style="position:relative" class="line">
							<?= makeSelectBox($conn, 'OUT_ST','stock_code',"125", "선택", "", $RS_STOCK_CODE);?>
						</td>
						<th>출고업체</th>
						<td style="position:relative" class="line">
							<input type="text" class="autocomplete_off" style="width:90%" placeholder="업체(명/코드) 입력후 엔터를 눌러주세요" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'',$RS_OUT_CP_NO)?>" />
							<input type="hidden" name="cp_type" value="<?=$RS_OUT_CP_NO?>">

							<script>
								$(function(){

									$("input[name=txt_cp_type]").keydown(function(e){

										if(e.keyCode==13) { 

											var keyword = $(this).val();
											
											if(keyword == "") { 
												$("input[name=cp_type]").val('');
												js_search();
											} else { 
												$.getJSON( "../company/json_company_list.php?term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
													if(data.length == 1) { 
														
														js_selecting_company("txt_cp_type", data[0].label, "cp_type", data[0].id);

													} else if(data.length > 1){ 
														NewWindow("../company/pop_company_searched_list.php?search_str="+keyword + "&target_name=txt_cp_type&target_value=cp_type",'pop_company_searched_list','950','650','YES');

													} else 
														alert("검색결과가 없습니다.");
												});
											}
										}

									});
									
									$("input[name=txt_cp_type]").keyup(function(e){
										var keyword = $(this).val();

										if(keyword == "") { 
											$("input[name=cp_type]").val('');
										}
									});

								});

							</script>
							<!--
							<input type="text" class="autocomplete_off" style="width:160px" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'판매',$RS_OUT_CP_NO)?>" placeholder="업체명/코드입력후 선택"/>
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
										$("input[name=cp_type]").val(ui.item.id);
									}
								}).bind( "blur", function( event ) {
									var cp_no = $("input[name=cp_type]").val();
									if(cp_no != '') {
										$.getJSON( "../company/json_company_list.php?cp_no=" + cp_no, function(data) {
											if(data[0].CP_NO == 'undefined') {
												$("input[name=cp_type]").val('');
											} else {
												if(data[0].COMPANY != $(".seller").val())
												{

													$(".seller").val();
													$("input[name=cp_type]").val('');
												}
											}
										});
									} 
								});
							});
							</script>
							<input type="hidden" name="cp_type" value="<?=$RS_OUT_CP_NO?>">
							-->
							<script>
								function js_selecting_company(target_name, cp_nm, target_value, cp_no) {
									
									$(function(){

										$("input[name="+target_name+"]").val(cp_nm);
										$("input[name="+target_value+"]").val(cp_no);

									});

								}
							</script>
						</td>
					</tr>
					<tr>
						<th>출고수량</th>
						<td>
							<input type="Text" name="qty" value="<?= $QTY ?>" style="width:70px;" required onkeyup="return isNumber(this)" class="txt">
						</td>
						<th>출고단가</th>
						<td>
							<input type="Text" name="buy_price" value="<?= $RS_OUT_PRICE?>" style="width:120px;" itemname="가격" required onkeyup="return isNumber(this)" class="txt">
						</td>
					</tr>
					<tr>
						<th>출고 사유</th>
						<td>
							<?= makeSelectBox($conn, 'LOC','in_loc',"125", "선택", "", $RS_IN_LOC);?>
						</td>
						<th>사유 상세</th>
						<td>
							<input type="Text" name="in_loc_ext" value="<?= $RS_IN_LOC_EXT?>" style="width:120px;" itemname="사유 상세" class="txt">
						</td>
					</tr>
					<tr>
						<th>출고일</th>
						<td>
							<input type="Text" name="out_date" value="<?= $RS_OUT_DATE?>" style="width:100px; margin-right:3px;" itemname="출고일" required class="txt datepicker">
						</td>
						<th>주문번호</th>
						<td>
							<input type="Text" name="reserve_no" value="<?= $RS_RESERVE_NO?>" style="width:100px; margin-right:3px;" itemname="주문번호" class="txt">
						</td>
					</tr>
					<tr>
						<th>메모</th>
						<td colspan="3">
							<textarea name="memo" style="width:98%; height:50px" class="txt"><?= $RS_MEMO?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
				
				<div class="btnright">

				<? if ($stock_no <> "" ) {?>
					<? if ($sPageRight_U == "Y") {?>
          <a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
					<? } ?>
				<? } else {?>
					<? if ($sPageRight_I == "Y") {?>
          <a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
					<? } ?>
				<? }?>

          <a href="javascript:js_list();"><img src="../images/admin/btn_list.gif" alt="목록" /></a>
				<? if ($stock_no <> "") {?>
					<? if ($sPageRight_D == "Y") {?>
          <a href="javascript:js_delete();"><img src="../images/admin/btn_delete.gif" alt="삭제" /></a>
					<? } ?>
				<? } ?>

        </div>      
      </div>
      <!-- // E: mwidthwrap -->

    </td>
  </tr>
  </table>
</div>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>
<script type="text/javascript">
<?
	if ($rs_goods_no == "") {
		if ($rs_goods_name <> "") {
?>
	searchKeyword();
<?
		}
	}
?>
</script>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>