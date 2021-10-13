<?session_start();?>
<?
# =============================================================================
# File Name    : order_list.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @C&C Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "OD004"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/payment/payment.php";

	if ($mode == "U") {

		$row_cnt = count($chk_pay_no);
		//echo $row_cnt;

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_pay_no			= $chk_pay_no[$k];
			
			$arr_pay_no			= explode("|", $str_pay_no);
			
			$tmp_pay_no			= trim($arr_pay_no[0]);
			$tmp_reserve_no = trim($arr_pay_no[1]);
			$tmp_pay_reason = trim($arr_pay_no[2]);

			//echo $tmp_pay_no."<br>";
			//echo $tmp_reserve_no."<br>";
			//echo $tmp_pay_reason."<br>";

			#echo $tmp_pay_no;

			$result = updatePaymentState($conn, $tmp_pay_no, $tmp_pay_reason, $tmp_reserve_no, $pay_state, $s_adm_no);
		
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

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
	$del_tf = "N";
	$sel_pay_reason = "물품구매";

	if ($sel_pay_type == "") 
		$sel_pay_type = "BANK";

	if ($sel_order_state == "") 
		$sel_order_state = "0";

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

	$condition = "";
#===============================================================
# Get Search list count
#===============================================================

	$nListCnt =totalCntPayment($conn, $start_date, $end_date, $sel_pay_type, $cp_type, $sel_pay_state, $sel_account_bank, $reserve_no, $mem_no, $sel_pay_reason, $cash_receipt_state, $con_use_tf, $del_tf, $search_field, $search_str, $condition);
	
	#echo $nListCnt;

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listPayment($conn, $start_date, $end_date, $sel_pay_type, $cp_type, $sel_pay_state, $sel_account_bank, $reserve_no, $mem_no, $sel_pay_reason, $cash_receipt_state, $con_use_tf, $del_tf, $order_field, $order_str, $search_field, $search_str, $condition, $nPage, $nPageSize);

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
<script language="javascript">

	function js_write() {
		document.location.href = "payment_write.php";
	}

	function js_view(rn, reserve_no) {

		var frm = document.frm;
		
		var url = "order_read.php?reserve_no="+reserve_no;

		NewWindow(url, 'order_detail','860','600','YES');
		
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

	function js_toggle(pay_state) {
		var frm = document.frm;

		bDelOK = confirm('입금 상태를 변경 하시겠습니까?');
		
		if (bDelOK==true) {
			
			frm.pay_state.value = pay_state;
			frm.mode.value = "T";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_all_check() {
		var frm = document.frm;
		
		if (frm['chk_pay_no[]'] != null) {
			
			if (frm['chk_pay_no[]'].length != null) {

				if (frm.all_chk.checked == true) {
					for (i = 0; i < frm['chk_pay_no[]'].length; i++) {
						frm['chk_pay_no[]'][i].checked = true;
					}
				} else {
					for (i = 0; i < frm['chk_pay_no[]'].length; i++) {
						frm['chk_pay_no[]'][i].checked = false;
					}
				}
			} else {
			
				if (frm.all_chk.checked == true) {
					frm['chk_pay_no[]'].checked = true;
				} else {
					frm['chk_pay_no[]'].checked = false;
				}
			}
		}
	}

	function js_excel() {

		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.target = "";
		frm.action = "<?=str_replace("list","excel_list",$_SERVER[PHP_SELF])?>";
		frm.submit();

	}

	function js_pay(state) {

		var frm = document.frm;
		
		frm.pay_state.value = state;
		frm.mode.value = "U";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	
	}

</script>
</head>

<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="rn" value="">
<input type="hidden" name="pay_no" value="">
<input type="hidden" name="use_tf" value="">
<input type="hidden" name="pay_state" value="" />
<input type="hidden" name="mode" value="">
<input type="hidden" name="nPage" value="<?=$nPage?>">
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

				<h2>입금 관리</h2>
				<div class="btnright"><!--<a href="javascript:js_write();"><img src="../images/admin/btn_regist_02.gif" alt="등록" /></a>--></div>
				<div class="category_choice"><!--<select style="width: 100px;"><option>제품선택</option></select>-->&nbsp;</div>

				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="120" />
					<col width="*" />
					<col width="120" />
					<col width="*" />
					<col width="120" />
				</colgroup>
				<thead>
					<tr>
						<th>주문일 :</th>
						<td>
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="start_date" value="<?=$start_date?>" maxlength="10"/>
							 ~ 
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="end_date" value="<?=$end_date?>" maxlength="10"/>
						</td>
						<th>입금상태 :</th>
						<td>
							<?= makeSelectBox($conn,"PAY_STATE","sel_pay_state","125","선택","",$sel_pay_state)?>
						</td>
						<th>판매업체 :</th>
						<td>
							<input type="text" class="seller" style="width:155px" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'판매',$cp_type)?>" />
							<script>
							$(function() {
						     var cache = {};
								$( ".seller" ).autocomplete({
									source: function( request, response ) {
										var term = request.term;
										if ( term in cache ) {
											response(cache[term]);
											return;
										}
						 
										$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('판매'), request, function( data, status, xhr ) {
											cache[term] = data;
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
							<input type="hidden" name="cp_type" value="<?=$cp_type?>">
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>결재방식 :</th>
						<td>
							<?= makeSelectBox($conn,"PAY_TYPE","sel_pay_type","125","선택","",$sel_pay_type)?>
						</td>
						<th>입금은행 :</th>
						<td colspan="3">
							<?= makeSelectBox($conn,"ACCOUNT_BANK","sel_account_bank","225","선택","",$sel_account_bank)?>
						</td>
					</tr>
					<tr>
						<th>정렬 :</th>
						<td>
							<select name="order_field" style="width:84px;">
								<option value="ORDER_DATE" <? if ($order_field == "ORDER_DATE") echo "selected"; ?> >주문일시</option>
								<option value="REG_DATE" <? if ($order_field == "REG_DATE") echo "selected"; ?> >등록일</option>
								<option value="CMS_DEPOSITOR" <? if ($order_field == "CMS_DEPOSITOR") echo "selected"; ?> >입금인</option>
								<option value="O_MEM_NM" <? if ($order_field == "O_MEM_NM") echo "selected"; ?> >주문인</option>
								<option value="TOTAL_PRICE" <? if ($order_field == "TOTAL_PRICE") echo "selected"; ?> >총주문액</option>
								<option value="BANK_AMOUNT" <? if ($order_field == "BANK_AMOUNT") echo "selected"; ?> >입금액</option>
								<option value="PAID_DATE" <? if ($order_field == "PAID_DATE") echo "selected"; ?> >입금일</option>
							</select>&nbsp;&nbsp;
							<input type='radio' class="" name='order_str' value='DESC' <? if (($order_str == "DESC") || ($order_str == "")) echo " checked"; ?> > 오름차순 &nbsp;
							<input type='radio' name='order_str' value='ASC' <? if ($order_str == "ASC") echo " checked"; ?>> 내림차순
						</td>

						<th>검색조건 :</th>
						<td colspan="2">
							<select name="nPageSize" style="width:84px;">
								<option value="20" <? if ($nPageSize == "20") echo "selected"; ?> >20개씩</option>
								<option value="50" <? if ($nPageSize == "50") echo "selected"; ?> >50개씩</option>
								<option value="100" <? if ($nPageSize == "100") echo "selected"; ?> >100개씩</option>
								<option value="200" <? if ($nPageSize == "200") echo "selected"; ?> >200개씩</option>
								<option value="300" <? if ($nPageSize == "300") echo "selected"; ?> >300개씩</option>
								<option value="400" <? if ($nPageSize == "400") echo "selected"; ?> >400개씩</option>
								<option value="500" <? if ($nPageSize == "500") echo "selected"; ?> >500개씩</option>
							</select>&nbsp;
							<select name="search_field" style="width:84px;">
								<option value="RESERVE_NO" <? if ($search_field == "RESERVE_NO") echo "selected"; ?> >주문번호</option>
								<option value="CMS_DEPOSITOR" <? if ($search_field == "CMS_DEPOSITOR") echo "selected"; ?> >입금인</option>
								<option value="O_MEM_NM" <? if ($search_field == "O_MEM_NM") echo "selected"; ?> >주문인</option>
							</select>&nbsp;

							<input type="text" value="<?=$search_str?>" name="search_str" class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();"/>
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						</td>
						<td align="right">
							<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="sp20"></div>
			총 <?=$nListCnt?> 건
			<table cellpadding="0" cellspacing="0" class="rowstable" border="0">
				<colgroup>
					<col width="3%" />
					<col width="8%" />
					<col width="19%" />
					<col width="6%" />
					<col width="9%" />
					<col width="8%" />
					<col width="14%" />
					<col width="6%" />
					<col width="7%" />
					<col width="10%" />
					<col width="10%" />
				</colgroup>
				<thead>
					<tr>
						<th rowspan="2"><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
						<th rowspan="2">주문번호</th>
						<th>판매업체</th>
						<th>입금인</th>
						<th>전화번호</th>
						<th>총주문액</th>
						<th rowspan="2">은행</th>
						<th rowspan="2">상태</th>
						<th rowspan="2">결재방법</th>
						<th rowspan="2">주문일</th>
						<th rowspan="2" class="end">입금일</th>
					</tr>
					<tr>
						<th>상품명</th>
						<th>주문인</th>
						<th>연락처</th>
						<th>입금액</th>

					</tr>
				</thead>
				<tbody>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							$rn								= trim($arr_rs[$j]["rn"]);
							$RESERVE_NO				= trim($arr_rs[$j]["RESERVE_NO"]);
							$PAY_NO						= trim($arr_rs[$j]["PAY_NO"]);
							$PAY_REASON				= trim($arr_rs[$j]["PAY_REASON"]);
							$CP_NO						= trim($arr_rs[$j]["CP_NO"]);
							$TOTAL_PRICE			= trim($arr_rs[$j]["TOTAL_PRICE"]);
							$O_MEM_NM					= trim($arr_rs[$j]["O_MEM_NM"]);
							$O_PHONE					= trim($arr_rs[$j]["O_PHONE"]);
							$O_HPHONE					= trim($arr_rs[$j]["O_HPHONE"]);
							
							$PAY_EXT					= trim($arr_rs[$j]["PAY_EXT"]);
							$PAY_TYPE					= trim($arr_rs[$j]["PAY_TYPE"]);
							$PAY_STATE				= trim($arr_rs[$j]["PAY_STATE"]);
							$MEM_NM						= trim($arr_rs[$j]["MEM_NM"]);
							$NM								= trim($arr_rs[$j]["NM"]);
							$CMS_AMOUNT				= trim($arr_rs[$j]["CMS_AMOUNT"]);
							$CMS_PAY_BANK			= trim($arr_rs[$j]["CMS_PAY_BANK"]);
							$CMS_PAY_ACCOUNT	= trim($arr_rs[$j]["CMS_PAY_ACCOUNT"]);
							$CMS_DEPOSITOR		= trim($arr_rs[$j]["CMS_DEPOSITOR"]);
							$BANK_AMOUNT			= trim($arr_rs[$j]["BANK_AMOUNT"]);
							$BANK_PAY_ACCOUNT	= trim($arr_rs[$j]["BANK_PAY_ACCOUNT"]);
							$BANK_PAY_DATE		= trim($arr_rs[$j]["BANK_PAY_DATE"]);

							$BANK_AMOUNT			= trim($arr_rs[$j]["BANK_AMOUNT"]);
							$CARD_AMOUNT			= trim($arr_rs[$j]["CARD_AMOUNT"]);
							$PGBANK_AMOUNT		= trim($arr_rs[$j]["PGBANK_AMOUNT"]);
							$RESERVE_NO				= trim($arr_rs[$j]["RESERVE_NO"]);
							$ORDER_DATE				= trim($arr_rs[$j]["ORDER_DATE"]);
							$REQ_DATE					= trim($arr_rs[$j]["REQ_DATE"]);
							$REG_DATE					= trim($arr_rs[$j]["REG_DATE"]);
							$PAID_DATE				= trim($arr_rs[$j]["PAID_DATE"]);
							$CANCEL_DATE			= trim($arr_rs[$j]["CANCEL_DATE"]);
							
							if ($MEM_TYPE == "") {
								$str_mem_type = "비회원";
								$MEM_NM = $NM;
							} else {
								$str_mem_type = getDcodeName($conn, "MEM_TYPE", $MEM_TYPE);
							}

							$amount = "0";
							
							$amount = ($CMS_AMOUNT + $BANK_AMOUNT + $CARD_AMOUNT + $PGBANK_AMOUNT);

							$REG_DATE			= date("Y-m-d",strtotime($REG_DATE));

							$ORDER_DATE		= date("Y-m-d H:i:s",strtotime($ORDER_DATE));

							if (($REQ_DATE == "") || ($REQ_DATE == "0000-00-00 00:00:00")) { 
								$REQ_DATE		= "&nbsp;";
							} else {
								$REQ_DATE		= date("Y-m-d",strtotime($REQ_DATE));
							}

							if (($PAID_DATE == "") || ($PAID_DATE == "0000-00-00 00:00:00")) { 
								$PAID_DATE		= "&nbsp;";
							} else {
								$PAID_DATE		= date("Y-m-d",strtotime($PAID_DATE));
							}

							if (($CANCEL_DATE == "") || ($CANCEL_DATE == "0000-00-00 00:00:00")) { 
								$CANCEL_DATE		= "&nbsp;";
							} else {
								$CANCEL_DATE	= date("Y-m-d",strtotime($CANCEL_DATE));
							}
				
							if ($USE_TF == "Y") {
								$STR_USE_TF = "<font color='navy'>공개</font>";
							} else {
								$STR_USE_TF = "<font color='red'>비공개</font>";
							}

							
							$str_pay_account = "ACCOUNT_BANK";

							$arr_goods = listManagerOrderGoods($conn, $RESERVE_NO, $MEM_NO, "Y", "N");
							
							if (sizeof($arr_goods) > 0) {
								for ($h = 0 ; $h < sizeof($arr_goods); $h++) {
									$GOODS_NAME					= SetStringFromDB($arr_goods[$h]["GOODS_NAME"]);
									if ($h == 0) {
										$str_gooods_name = $GOODS_NAME;
									} else {
										$str_gooods_name = $GOODS_NAME."외 ".$h."건";
									}
								}
							}

							
						?>
						<tr>
							<td rowspan="2">
								<input type="checkbox" name="chk_pay_no[]" value="<?=$PAY_NO?>|<?=$RESERVE_NO?>|<?=$PAY_REASON?>">
								<!--
								<input type="hidden" name="chk_reserve_no[]" value="">
								<input type="hidden" name="chk_pay_reason[]" value="">
								-->
							</td>
							<td  rowspan="2" class="order"><a href="javascript:js_view('<?=$rn?>','<?=$RESERVE_NO?>');"><?=$RESERVE_NO?></a></td>
							<td class="modeual_nm" height="35"><?= getCompanyName($conn, $CP_NO);?></td>
							<td class="filedown"><?=$CMS_DEPOSITOR?></td>
							<td><?=$O_PHONE?></td>
							<td class="price"><?=number_format($TOTAL_PRICE)?></td>
							<td rowspan="2">
							<?
								if ($PAY_EXT <> "") {
									echo $PAY_EXT;
								} else {
									echo getDcodeName($conn, $str_pay_account, $BANK_PAY_ACCOUNT);
								}
							?>
							</td>
							<td rowspan="2"><?=getDcodeName($conn, "PAY_STATE", $PAY_STATE);?></td>
							<td rowspan="2"><?=getDcodeName($conn, "PAY_TYPE", $PAY_TYPE);?></td>
							<td rowspan="2"><?=$ORDER_DATE?></td>
							<td rowspan="2"><?=$PAID_DATE?></td>
							<!--
							<td class="lpd10"><?=$TITLE?></a></td>
							<td><a href="javascript:js_toggle('<?=$BB_CODE?>','<?=$BB_NO?>','<?=$USE_TF?>');"><?= $STR_USE_TF ?></a></td>
							<td><?= $REG_DATE ?></td>
							-->
						</tr>
						<tr>
							<td class="modeual_nm" height="35"><?=$str_gooods_name?></td>
							<td class="filedown"><?=$O_MEM_NM?></td>
							<td><?=$O_HPHONE?></td>
							<td class="price"><?=number_format($amount)?></td>
						</tr>
						<?
						}
					}else{
						?>
						<tr>
							<td height="50" align="center" colspan="12">데이터가 없습니다. </td>
						</tr>
					<?
						}
					?>
				</tbody>
			</table>

			<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">
<? if ($sPageRight_U == "Y") {?>
	<input type="button" name="aa" value=" 입금처리로 " class="btntxt" onclick="js_pay('1');"> 
	<input type="button" name="aa" value=" 입금전으로 " class="btntxt" onclick="js_pay('0');">
<? } ?>
			</div>
			
				<!-- --------------------- 페이지 처리 화면 START -------------------------->
				<?
					# ==========================================================================
					#  페이징 처리
					# ==========================================================================
					if (sizeof($arr_rs) > 0) {
						#$search_field		= trim($search_field);
						#$search_str			= trim($search_str);
						$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&start_date=".$start_date."&end_date=".$end_date;
						$strParam = $strParam."&sel_order_state=".$sel_order_state."&cp_type=".$cp_type."&sel_pay_type=".$sel_pay_type."&order_field=".$order_field."&order_str=".$order_str;
						$strParam = $strParam."&sel_pay_state=".$sel_pay_state;

				?>
				<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
				<?
					}
				?>
				<!-- --------------------- 페이지 처리 화면 END -------------------------->
		<!-- // E: mwidthwrap -->
			</div>
			<!-- // E: mwidthwrap -->

		</td>
	</tr>
	<tr>
		<td colspan="2" height="70"><div class="copyright"><img src="../images/admin/copyright.gif" alt="" /></div></td>
	</tr>
	</table>

</div>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>