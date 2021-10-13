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
	$menu_right = "CF006"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/confirm/confirm.php";

#====================================================================
# Request Parameter
#====================================================================
	$mode			= trim($mode);
	$cl_no			= trim($cl_no);

	$strParam = "?start_date=".$start_date."&end_date=".$end_date."&cp_type=".$cp_type;
	
	$inout_date				= SetStringToDB($inout_date);
	$inout_type				= SetStringToDB($inout_type);
	$name					= SetStringToDB($name);
	$qty					= SetStringToDB($qty);
	$unit_price				= SetStringToDB($unit_price);
	$withdraw				= SetStringToDB($withdraw);
	$deposit				= SetStringToDB($deposit);
	$reserve_no				= SetStringToDB($reserve_no);
	$order_goods_no			= SetStringToDB($order_goods_no);
	$rgn_no					= SetStringToDB($rgn_no);
	
	$result	= false  ;

#====================================================================
# DML Process
#====================================================================
	if ($mode == "I") {

		$goods_no = null;

		$unit_price = str_replace(",", "", $unit_price);

		$result	= insertCompanyLedger($conn, $cp_no, $inout_date, $inout_type, $goods_no, $name, $qty, $unit_price, $to_cp_no, $surtax, $memo, $reserve_no, $order_goods_no, $rgn_no, $s_adm_no, null);

?>
<script type="text/javascript">
	
	var bOK = "";

	bOK = confirm('계속 등록 하시겠습니까?');

	if (bOK==true) {
		document.location = "company_ledger_write.php<?=$strParam?>";
	} else {
		document.location = "company_ledger_list.php<?=$strParam?>";
	}

</script>
<?
		mysql_close($conn);
		exit;
	}

	if ($mode == "U") {

		$result = updateCompanyLedger($conn, $cp_no, $inout_date, $inout_type, $name, $qty, $unit_price, $memo, $reserve_no, $order_goods_no, $rgn_no, $cl_no);


		if ($result) {

			if ($mode == "U") {
	?>	
	<script language="javascript">
		location.href =  "company_ledger_write.php<?=$strParam?>&mode=S&cl_no=<?=$cl_no?>";
	</script>
	<?
			} else {
	?>	
	<script language="javascript">
			alert('정상 처리 되었습니다.');
			location.href =  "company_ledger_list.php<?=$strParam?>";
	</script>
	<?
			}

			mysql_close($conn);
			exit;
		}	
	
	}

	if ($mode == "D") {
		$result = deleteCompanyLedger($conn, $cl_no, $s_adm_no);

	}

	if ($mode == "S") {

		$arr_rs = selectCompanyLedger($conn, $cl_no);

		$RS_CP_NO								= trim($arr_rs[0]["CP_NO"]); 
		$RS_INOUT_DATE							= SetStringFromDB($arr_rs[0]["INOUT_DATE"]); 
		$RS_INOUT_TYPE							= SetStringFromDB($arr_rs[0]["INOUT_TYPE"]); 
		$RS_NAME								= SetStringFromDB($arr_rs[0]["NAME"]); 
		$RS_QTY									= SetStringFromDB($arr_rs[0]["QTY"]); 
		$RS_UNIT_PRICE							= SetStringFromDB($arr_rs[0]["UNIT_PRICE"]); 
		$RS_WITHDRAW							= SetStringFromDB($arr_rs[0]["WITHDRAW"]); 
		$RS_DEPOSIT								= SetStringFromDB($arr_rs[0]["DEPOSIT"]); 
		
		$RS_RESERVE_NO							= SetStringFromDB($arr_rs[0]["RESERVE_NO"]); 
		$RS_ORDER_GOODS_NO						= SetStringFromDB($arr_rs[0]["ORDER_GOODS_NO"]); 
		$RS_RGN_NO								= SetStringFromDB($arr_rs[0]["RGN_NO"]); 

		$RS_MEMO								= SetStringFromDB($arr_rs[0]["MEMO"]); 

		$RS_INOUT_DATE			= date("Y-m-d",strtotime($RS_INOUT_DATE));
	} 

	if ($mode == "APPEND") {

		$arr_rs = selectCompanyLedger($conn, $cl_no);

		$RS_CP_NO								= trim($arr_rs[0]["CP_NO"]); 
		$RS_INOUT_DATE							= SetStringFromDB($arr_rs[0]["INOUT_DATE"]); 
		$RS_INOUT_TYPE							= SetStringFromDB($arr_rs[0]["INOUT_TYPE"]); 

		$RS_NAME								= ""; 
		$RS_QTY									= "";
		$RS_UNIT_PRICE							= "";
		$RS_WITHDRAW							= "";
		$RS_DEPOSIT								= "";
		
		$RS_RESERVE_NO							= SetStringFromDB($arr_rs[0]["RESERVE_NO"]); 
		$RS_ORDER_GOODS_NO						= SetStringFromDB($arr_rs[0]["ORDER_GOODS_NO"]); 
		$RS_RGN_NO								= SetStringFromDB($arr_rs[0]["RGN_NO"]); 

		$RS_INOUT_DATE			= date("Y-m-d",strtotime($RS_INOUT_DATE));
	} 

	$RS_INOUT_TYPE = getDcodeCode($conn, "COMPANY_LEDGER_TYPE", $RS_INOUT_TYPE);

	if(startsWith($RS_INOUT_TYPE, "D"))
		$RS_TOTAL_PRICE = $RS_DEPOSIT;
	else if(startsWith($RS_INOUT_TYPE, "W"))
		$RS_TOTAL_PRICE = $RS_WITHDRAW;


	if($cl_no == "") { 
		$RS_INOUT_DATE			= date("Y-m-d",strtotime("0 month"));
		$RS_CP_NO = $cp_type;
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
<script type="text/javascript" src="../jquery/jquery-ui-timepicker-addon.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui-timepicker-addon.css" type="text/css" />
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<script>
  $(function() {
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
		
		var frm = document.frm;
		var cl_no = frm.cl_no.value;

		/*
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
		*/
		
		if (cl_no == "") {
			frm.mode.value = "I";
		} else {
			if(frm.mode.value == "APPEND")  
				frm.mode.value = "I";
			else
				frm.mode.value = "U";
		}

		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	function js_list() {

		location.href = "company_ledger_list.php<?=$strParam?>";
	}

	function js_calculate_total_price() {
		
		var i_total_price		= 0;
		var i_qty				= 0;
		var i_unit_price		= 0;

		if ($("input[name=qty]").val() != "") i_qty = parseInt($("input[name=qty]").val().replace(",", ""));
		if ($("input[name=unit_price]").val() != "") i_unit_price = parseInt($("input[name=unit_price]").val().replace(",", ""));

		if(i_qty == "" || i_unit_price == "") return;

		i_total_price = i_qty * i_unit_price; 

		$(".total_price").html(numberFormat(i_total_price));

	}

</script>
</head>
<body id="admin">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="<? if($mode == "APPEND") { echo "APPEND"; } ?>">
<input type="hidden" name="cl_no" value="<?=$cl_no?>">
<input type="hidden" name="start_date" value="<?=$start_date?>">
<input type="hidden" name="end_date" value="<?=$end_date?>">
<input type="hidden" name="cp_type" value="<?=$cp_type?>">

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
				<h2>매출/매입 기장</h2>  
				<div class="sp5"></div>

				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="16%">
					<col width="34%">
					<col width="16%">
					<col width="34%">
				</colgroup>
					<tr>
						<th>입고업체</th>
						<td class="line">
							<?=getCompanyName($conn, $RS_CP_NO)?>
							<input type="hidden" name="cp_no" value="<?=$RS_CP_NO?>">
						</td>
						<th>잔액</th>
						<td class="line">
							<span class="get_balance" data-cp_no="<?=$RS_CP_NO?>">클릭해주세요</span>
							<script>
								$(function(){
									$(".get_balance").click(function(){
										var cp_no = $(this).data("cp_no");
										var clicked_obj = $(this);

										$.getJSON( "../confirm/json_company_ledger.php?cp_no=" + encodeURIComponent(cp_no), function(data) {
											if(data != undefined) { 
												if(data.length == 1) 
													clicked_obj.html(numberFormat(data[0].SUM_BALANCE) + " 원");
												else {
													alert(cp_no);
													clicked_obj.html("검색결과가 없습니다.");
												}
											}
											else
												alert(cp_no);
										});


									});

									$(".get_balance").click();

								});
								</script>
						</td>
					</tr>
				</table>
				<div class="sp10"></div>
				<?if($cl_no <> "") { ?>
				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="16%">
					<col width="34%">
					<col width="16%">
					<col width="34%">
				</colgroup>
					<tr>
						<th>기장구분</th>
						<td class="line">
							<?=getDcodeName($conn, "COMPANY_LEDGER_TYPE", $RS_INOUT_TYPE)?>
							<input type="hidden" name="inout_type" value="<?=$RS_INOUT_TYPE?>"/>
								
							<? // makeRadioBox($conn, "COMPANY_LEDGER_TYPE", "inout_type", $RS_INOUT_TYPE)?>
						</td>
						<th>매입/매출발생일</th>
						<td class="line">
							<input type="Text" name="inout_date" value="<?= $RS_INOUT_DATE?>" style="margin-right:3px;" class="txt datepicker">
						</td>
					</tr>
					<tr>
						<th>기장명</th>
						<td>
							<input type="Text" name="name" value="<?= $RS_NAME ?>" style="width:95%;" class="txt">
						</td>
						<th>금액</th>
						<td class="line">
							<span class="total_price"><?=$RS_TOTAL_PRICE?></span>
						</td>
					</tr>
					<tr>
						<th>수량</th>
						<td>
							<input type="Text" name="qty" value="<?= $RS_QTY ?>"  required onkeyup="return isNumber(this)" onChange="js_calculate_total_price()" class="txt calc">
						</td>
						<th>단가</th>
						<td>
							<input type="Text" name="unit_price" value="<?= $RS_UNIT_PRICE ?>"  required onkeyup="return isNumber(this)" onChange="js_calculate_total_price()" class="txt calc">
						</td>
					</tr>
					<tr>
						<th>주문번호</th>
						<td>
							주문  번호 : <input type="Text" name="reserve_no" value="<?= $RS_RESERVE_NO?>" class="txt"><br/><br/>
							주문상품번호 : <input type="Text" name="order_goods_no" value="<?= $RS_ORDER_GOODS_NO?>" class="txt"><br/><br/>
							발주상품번호 : <input type="Text" name="rgn_no" value="<?= $RS_RGN_NO?>" class="txt">
						</td>
						<th>비고</th>
						<td>
							<input type="Text" name="memo" value="<?= $RS_MEMO ?>" style="width:95%;" class="txt">
						</td>
					</tr>
				</table>
			<? } else { ?>
			
				<table cellpadding="0" cellspacing="0" class="colstable">
					<colgroup>
						<col width="16%">
						<col width="34%">
						<col width="16%">
						<col width="34%">
					</colgroup>
					<tr>
						<th>기장구분</th>
						<td class="line">
							<input type ="radio" name= "inout_type" value="DW02"><label> 입금 </label>
							<input type ="radio" name= "inout_type" value="WW04"><label> 지급 </label>
							<input type ="radio" name= "inout_type" value="RW05"> <label> 대체 </label>
							<input type="button" name="show_to_cp_no" value="대체선택" style="display:none;"/>
							<input type="hidden" name="to_cp_no" value=""/>

							<input type="hidden" name="qty" value="1"/>
							<!--<input type="hidden" name="name" value=""/>-->

							<script>
								$(function(){
									$("input[type=radio][name=inout_type]").change(function() {
										
										if($(this).val().startsWith("R")) { 
										
											$("input[name=show_to_cp_no]").show();

										} else { 
											$("input[name=name]").val("< " + $(this).next('label:first').html() + " >");
										}
									});
								});

								$("input[name=show_to_cp_no]").click(function(e){

									NewWindow("../company/pop_company_searched_list.php?search_str=&target_name=name&target_value=to_cp_no",'pop_company_searched_list','950','650','YES');

								});

								function js_selecting_company(target_name, cp_nm, target_value, cp_no) {
								
									$(function(){

										$("input[name="+target_name+"]").val(cp_nm);
										$("input[name="+target_value+"]").val(cp_no);

									});
								}

							</script>
						</td>
						<th>매입/매출발생일</th>
						<td class="line">
							<input type="Text" name="inout_date" value="<?= $RS_INOUT_DATE?>" style="margin-right:3px;" class="txt datepicker">
						</td>
					</tr>
					<tr>
						<th>기장명</th>
						<td colspan="3">
							<input type="Text" name="name" value="" style="width:95%;" class="txt">
						</td>
						
					</tr>
					<tr>
						<th>금액</th>
						<td class="line">
							<input type="Text" name="unit_price" value=""  required onkeyup="return isNumber(this)"  class="txt calc">
						</td>
						<th>비고</th>
						<td>
							<input type="Text" name="memo" value="" style="width:95%;" class="txt">
						</td>
					</tr>
				</table>
			<? } ?>
				
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

<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>