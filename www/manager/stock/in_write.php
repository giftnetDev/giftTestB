<?session_start();?>
<?
# =============================================================================
# File Name    : in_write.php
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "SG003"; // 메뉴마다 셋팅 해 주어야 합니다

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
	$mode			= trim($mode);
	$stock_no	= trim($stock_no);
	
	//echo $pb_nm; 
	//echo $mode;
	
	$stock_no				= SetStringToDB($stock_no);
	$stock_type				= SetStringToDB($stock_type);
	$stock_code				= SetStringToDB($stock_code);
	$cp_type				= SetStringToDB($cp_type);
	$out_cp_no				= SetStringToDB($out_cp_no);
	$goods_no				= SetStringToDB($goods_no);
	$goods_code				= SetStringToDB($goods_code);
	$in_loc					= SetStringToDB($in_loc);
	$in_loc_ext				= SetStringToDB($in_loc_ext);
	$qty					= SetStringToDB($qty);
	$buy_price				= SetStringToDB($buy_price);
	$in_date				= SetStringToDB($in_date);
	$pay_date				= SetStringToDB($pay_date);
	
	$result	= false  ;


#====================================================================
# DML Process
#====================================================================
	if ($mode == "I") {
		
		$stock_type = "IN";
		
		if (left($stock_code,1) == "N") {
			$in_qty		= $qty;
			$in_bqty	= 0;
			$in_fqty	= 0;
		} if (left($stock_code,1) == "B") {
			$in_qty		= 0;
			$in_bqty	= $qty;
			$in_fqty	= 0;
		} if (left($stock_code,1) == "F") {
			$in_qty		= 0;
			$in_bqty	= 0;
			$in_fqty	= $qty;
		}
	
		//echo $qty;
		//echo $in_qty;

		//exit;

		$in_cp_no = $cp_type;
		$in_price = $buy_price;
		$close_tf = "N";

		echo "rgn_no : $rgn_no<br>";

		$result = insertStock($conn, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $order_goods_no, $rgn_no, $close_tf, $s_adm_no, $memo);

		//기 발주한 곳의 수량이 변경된다면 발주서의 입고수량도 변경
		if($rgn_no != 0 && $rgn_no != null)
			updateGoodsRequestGoodsQty($conn, $rgn_no);

?>
<script type="text/javascript">
	
	var bDelOK = "";

	bDelOK = confirm('계속 등록 하시겠습니까?');

	if (bDelOK==true) {
		document.location = "in_write.php";
	} else {
		document.location = "in_list.php";
	}

</script>
<?
		mysql_close($conn);
		exit;
	}

	if ($mode == "S") {

		$arr_rs = selectStock($conn, $stock_no);

		$RS_STOCK_NO							= trim($arr_rs[0]["STOCK_NO"]); 
		$RS_STOCK_TYPE							= SetStringFromDB($arr_rs[0]["STOCK_TYPE"]); 
		$RS_STOCK_CODE							= SetStringFromDB($arr_rs[0]["STOCK_CODE"]); 
		$RS_IN_CP_NO							= SetStringFromDB($arr_rs[0]["IN_CP_NO"]); 
		$RS_OUT_CP_NO							= SetStringFromDB($arr_rs[0]["OUT_CP_NO"]); 
		$RS_GOODS_NO							= SetStringFromDB($arr_rs[0]["GOODS_NO"]); 
		$RS_IN_LOC								= SetStringFromDB($arr_rs[0]["IN_LOC"]); 
		$RS_IN_LOC_EXT							= SetStringFromDB($arr_rs[0]["IN_LOC_EXT"]); 
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
		$RS_GOODS_NAME							= SetStringFromDB($arr_rs[0]["GOODS_NAME"]); 
		$RS_GOODS_CODE							= SetStringFromDB($arr_rs[0]["GOODS_CODE"]); 
		$RS_RESERVE_NO							= SetStringFromDB($arr_rs[0]["RESERVE_NO"]); 
		$RS_MEMO								= SetStringFromDB($arr_rs[0]["MEMO"]); 

		$RS_IN_DATE			= date("Y-m-d",strtotime($RS_IN_DATE));
		$RS_PAY_DATE		= date("Y-m-d",strtotime($RS_PAY_DATE));

		if (left($RS_STOCK_CODE,1) == "N") {
			$QTY = $RS_IN_QTY;
		} else if (left($RS_STOCK_CODE,1) == "B") {
			$QTY = $RS_IN_BQTY;
		} else if (left($RS_STOCK_CODE,1) == "F") {
			$QTY = $RS_IN_FQTY;
		}
	} else {
		$RS_IN_DATE	 = $pay_date;
		$RS_PAY_DATE = $pay_date;
	}

	if ($mode == "U") {
		
		$close_tf = "N";
		$stock_type = "IN";

		if (left($stock_code,1) == "N") {
			$in_qty		= $qty;
			$in_bqty	= 0;
			$in_fqty	= 0;
		} if (left($stock_code,1) == "B") {
			$in_qty		= 0;
			$in_bqty	= $qty;
			$in_fqty	= 0;
		} if (left($stock_code,1) == "F") {
			$in_qty		= 0;
			$in_bqty	= 0;
			$in_fqty	= $qty;
		}

		$in_cp_no = $cp_type;
		$in_price = $buy_price;



		$result = updateStock($conn, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $in_price, $out_price, $reserve_no, $in_date, $out_date, $pay_date, $close_tf, $s_adm_no, $memo, $stock_no);

		//기 발주한 곳의 수량이 변경된다면 발주서의 입고수량도 변경
		if($rgn_no != 0 && $rgn_no != null)
			updateGoodsRequestGoodsQty($conn, $rgn_no);
	
	}

	if ($mode == "D") {
		//$result = deleteStOrder($conn,$order_no);
		$result = deleteStock($conn, $stock_no, $s_adm_no);

	}

	
	$strParam = $strParam."?nPage=".$nPage."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&sel_cp_type2=".$sel_cp_type2;
	$strParam = $strParam."&start_date=".$start_date."&end_date=".$end_date."&con_stock_type=".$con_stock_type."&con_stock_code=".$con_stock_code;
	$strParam = $strParam."&sel_loc=".$sel_loc;

	if ($result) {
		
		if ($mode == "U") {
?>	
<script language="javascript">
	location.href =  "in_write.php<?=$strParam?>&mode=S&stock_no=<?=$stock_no?>";
</script>
<?
		} else {
?>	
<script language="javascript">
		alert('정상 처리 되었습니다.');
		location.href =  "in_list.php<?=$strParam?>";
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
			alert('입고구분을 선택해주세요.');
			frm.stock_code.focus();
			return ;		
		}

		if (isNull(frm.cp_type.value)) {
			alert('입고업체를 선택해주세요.');
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
		

		if (isNull(frm.in_date.value)) {
			alert('입고일을 입력해주세요.');
			frm.in_date.focus();
			return;
		}

		if (isNull(frm.pay_date.value)) {
			alert('결제일을 입력해주세요.');
			frm.pay_date.focus();
			return ;		
		}
		
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

		//alert(arr_keywordValues[0]); // 상품명
		//alert(arr_keywordValues[1]); // 상품번호
		//alert(arr_keywordValues[2]); // 공급가
		//alert(arr_keywordValues[3]); // 판매가
		//alert(arr_keywordValues[4]); // 상품코드 (경박)
		//alert(arr_keywordValues[5]); // 업체번호
		//alert(arr_keywordValues[6]); // 업체이름 [업체코드 (경박)]
		frm.goods_name.value					= arr_keywordValues[0];
		frm.goods_no.value						= arr_keywordValues[1];
		frm.buy_price.value						= arr_keywordValues[2];
		frm.cp_type.value						= arr_keywordValues[5];
		frm.txt_cp_type.value					= arr_keywordValues[6];

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
		//frm.method = "get";
		//frm.action = "in_list.php<?=$strParam?>";
		//frm.submit();

		location.href = "in_list.php<?=$strParam?>";
	}

</script>
</head>
<body id="admin">

<form name="frm" method="post" enctype="multipart/form-data">
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
<input type="hidden" name="sel_cp_type2" value="<?=$sel_cp_type2?>">
<input type="hidden" name="con_stock_code" value="<?=$con_stock_code?>">
<input type="hidden" name="sel_loc" value="<?=$sel_loc?>">

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
						<th>입고구분</th>
						<td style="position:relative" class="line">
							<?= makeSelectBox($conn, 'IN_ST','stock_code',"125", "선택", "", $RS_STOCK_CODE);?>
						</td>
						<th>입고업체</th>
						<td style="position:relative" class="line">
							<input type="text" class="supplier" style="width:160px" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'구매',$cp_type2)?>" placeholder="업체명/코드입력후 선택" />
							<script>
							$(function() {
						     var cache = {};
								$( ".supplier" ).autocomplete({
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
										$(".supplier").val(ui.item.value);
										$("input[name=cp_type]").val(ui.item.id);
									}
								}).bind( "blur", function( event ) {
									var cp_no = $("input[name=cp_type]").val();
									if(cp_no != '') {
										$.getJSON( "../company/json_company_list.php?cp_no=" + cp_no, function(data) {
											if(data[0].CP_NO == 'undefined') {
												$("input[name=cp_type]").val('');
											} else {
												if(data[0].COMPANY != $(".supplier").val())
												{

													$(".supplier").val();
													$("input[name=cp_type]").val('');
												}
											}
										});
									} 
								});
							});
							</script>
							<input type="hidden" name="cp_type" value="<?=$RS_IN_CP_NO?>">
						</td>
					</tr>
					<tr>
						<th>입고수량</th>
						<td>
							<input type="Text" name="qty" value="<?= $QTY ?>" style="width:70px;" required onkeyup="return isNumber(this)" class="txt">
						</td>
						<th>매입단가</th>
						<td>
							<input type="Text" name="buy_price" value="<?= $RS_IN_PRICE?>" style="width:120px;" itemname="가격" required onkeyup="return isNumber(this)" class="txt">
						</td>
					</tr>
					<tr>
						<th>입고 사유</th>
						<td>
							<?= makeSelectBox($conn, 'LOC','in_loc',"125", "선택", "", $RS_IN_LOC);?>
						</td>
						<th>사유 상세</th>
						<td>
							<input type="Text" name="in_loc_ext" value="<?= $RS_IN_LOC_EXT?>" style="width:120px;" itemname="사유 상세" class="txt">
						</td>
					</tr>
					<tr>
						<th>주문번호</th>
						<td colspan="3">
							<input type="Text" name="reserve_no" value="<?= $RS_RESERVE_NO?>" style="width:120px;" itemname="주문번호" class="txt">
						</td>
					</tr>
					<tr>
						<th>입고일</th>
						<td>
							<input type="Text" name="in_date" value="<?= $RS_IN_DATE?>" style="width:100px; margin-right:3px;" itemname="입고일" required class="txt datepicker">
						</td>
						<th>결제일</th>
						<td>
							<input type="Text" name="pay_date" value="<?= $RS_PAY_DATE?>" style="width:100px; margin-right:3px;" itemname="결제일" required class="txt datepicker">
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