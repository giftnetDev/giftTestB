<?session_start();?>
<?
# =============================================================================
# File Name    : st_order_list.php
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
	$menu_right = "SG004"; // 메뉴마다 셋팅 해 주어야 합니다

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
	
	//echo $s_adm_cp_type;
	//echo $s_adm_com_code;

	if ($s_adm_cp_type == "구매") { 
		$cp_type2 = $s_adm_com_code;
	}

	if ($start_date == "") {
		$start_date = date("Y-m-d",strtotime("-1 month"));;
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));;
	} else {
		$end_date = trim($end_date);
	}

	if ($confirm_ymd == "") {
		$confirm_ymd = date("Y-m-d",strtotime("0 month"));;
	} else {
		$confirm_ymd = trim($confirm_ymd);
	}

	//echo $s_adm_cp_type;
	//echo $s_adm_com_code;

	if ($mode == "U") {

		$row_cnt = count($chk_no);
		//echo $row_cnt;

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$temp_order_goods_no					= $chk_no[$k];
			
			//echo $temp_order_goods_no;

			//if ($temp_delivery_cp <> "")
			//echo $temp_order_goods_no."<br>";
			//echo $confirm_ymd."<br>";
			//echo $confirm_tf."<br>";

			$result = updateConfirmStateStOrder($conn, $temp_order_goods_no, $confirm_ymd, $confirm_tf, $s_adm_no);
		
		}
	}
#====================================================================
# Request Parameter
#====================================================================

	if ($order_field == "")
		$order_field = "PAY_DATE";

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$con_cate_01 = trim($con_cate_01);
	$con_cate_02 = trim($con_cate_02);
	$con_cate_03 = trim($con_cate_03);

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

#===============================================================
# Get Search list count
#===============================================================

	$nListCnt =totalCntStConfirmOrder($conn, $start_date, $end_date, $sel_confirm_tf, $cp_type2, $con_use_tf, $del_tf, $search_field, $search_str);
	
	#echo $nListCnt;

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listStConfirmOrder($conn, $start_date, $end_date, $sel_confirm_tf, $cp_type2, $con_use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize);


	$arr_rs_all = listAllStConfirmOrder($conn, $start_date, $end_date, $sel_confirm_tf, $cp_type2, $con_use_tf, $del_tf, $search_field, $search_str);


?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/calendar.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script language="javascript">


	// 조회 버튼 클릭 시 
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

function js_toggle() {

	var frm = document.frm;
	var chk_cnt = 0;

	if (frm('chk_reserve_no[]') == null) {
		alert("선택할 주문이 없습니다.");
		return;
	}

	if (frm('chk_reserve_no[]').length != null) {
		
		for (i = 0 ; i < frm('chk_reserve_no[]').length; i++) {
			if (frm('chk_reserve_no[]')[i].checked == true) {
				chk_cnt = 1;
			}
		}
	
	} else {
		if (frm('chk_reserve_no[]').checked == true) chk_cnt = 1;
	}
	
	if (chk_cnt == 0) {
		alert("상태 변경할 주문을 선택해 주세요");
		return;
	}

	bDelOK = confirm('주문 상태를 변경 하시겠습니까?');
		
	if (bDelOK==true) {

		frm.mode.value = "T";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}
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

	function js_confirm() {

		var frm = document.frm;
		
		frm.mode.value = "U";
		frm.confirm_tf.value = "Y";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	function js_cancel_confirm() {

		var frm = document.frm;
		
		frm.mode.value = "U";
		frm.confirm_tf.value = "N";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

</script>
</head>

<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="rn" value="">
<input type="hidden" name="reserve_no" value="">
<input type="hidden" name="confirm_tf" value="">
<input type="hidden" name="use_tf" value="">
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

				<h2>입고 정산 상세</h2>
				<div class="btnright">
					&nbsp;
				</div>
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
						<th>결제일 :</th>
						<td>
							<input type="text" class="txt" style="width: 75px;" name="start_date" value="<?=$start_date?>" maxlength="10" readonly="1" />
							<a href="javascript:show_calendar('document.frm.start_date', document.frm.start_date.value);" onFocus="blur();"><!--
						--><img src="/manager/images/bu/ic_calendar.gif" alt="" /></a>  ~ 

							<input type="text" class="txt" style="width: 75px;" name="end_date" value="<?=$end_date?>" maxlength="10" readonly="1" />
							<a href="javascript:show_calendar('document.frm.end_date', document.frm.end_date.value);" onFocus="blur();"><!--
						--><img src="/manager/images/bu/ic_calendar.gif" alt="" /></a>
						</td>
						<th>정산상태 :</th>
						<td colspan="2">
							<?= makeSelectBox($conn,"CONFIRM_TF","sel_confirm_tf","125","선택","",$sel_confirm_tf)?>
						</td>
					</tr>
				</thead>
				<tbody>
					<? if ($s_adm_cp_type == "운영") { ?>
					<tr>
						<th>공급업체 :</th>
						<td colspan="4">
							<?= makeCompanySelectBoxAsCpNo($conn, '구매', $cp_type2);?>
						</td>					
					</tr>
					<? } else { ?>
					<input type="hidden" name="cp_type" value = "">
					<input type="hidden" name="sel_pay_type" value = "">
					<? } ?>
					<tr>
						<th>정렬 :</th>
						<td>
							<select name="order_field" style="width:84px;">
								<option value="PAY_DATE" <? if ($order_field == "PAY_DATE") echo "selected"; ?> >결제일</option>
								<option value="ORDER_DATE" <? if ($order_field == "ORDER_DATE") echo "selected"; ?> >입고일시</option>
								<option value="REG_DATE" <? if ($order_field == "REG_DATE") echo "selected"; ?> >등록일</option>
							</select>&nbsp;&nbsp;
							<input type='radio' class="" name='order_str' value='DESC' <? if (($order_str == "DESC") || ($order_str == "")) echo " checked"; ?> > 오름차순 &nbsp;
							<input type='radio' name='order_str' value='ASC' <? if ($order_str == "ASC") echo " checked"; ?>> 내림차순
						</td>

						<th>검색조건 :</th>
						<td>
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
								<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >통합검색</option>
								<option value="GOODS_NAME" <? if ($search_field == "GOODS_NAME") echo "selected"; ?> >상품명</option>
							</select>&nbsp;

							<input type="text" value="<?=$search_str?>" name="search_str" class="txt" />
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						</td>
						<td align="right">
							<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="sp20"></div>
			<b>총 <?=$nListCnt?> 건</b>
			<table cellpadding="0" cellspacing="0" class="rowstable" border="0">
				<colgroup>
					<col width="2%" />
					<col width="8%" />
					<col width="13%" />
					<col width="24%" />
					<col width="10%"/>
					<col width="6%" />
					<col width="5%" />
					<col width="8%" />
					<col width="8%" />
					<col width="8%" />
				</colgroup>
				<thead>
					<tr>
						<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
						<th>결제일</th>
						<th>업체명</th>
						<th>상품명</th>
						<th>옵션</th>
						<th>매입가</th>
						<th>수량</th>
						<th>합계</th>
						<th>입고등록일</th>
						<th>정산여부</th>
						<th class="end">정산일</th>
					</tr>
				</thead>
				<tbody>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$rn									= trim($arr_rs[$j]["rn"]);
							$ORDER_GOODS_NO			= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
							$BUY_CP_NO					= trim($arr_rs[$j]["BUY_CP_NO"]);
							$GOODS_NAME					= SetStringFromDB(trim($arr_rs[$j]["GOODS_NAME"]));
							$BUY_PRICE					= trim($arr_rs[$j]["BUY_PRICE"]);
							$QTY								= trim($arr_rs[$j]["QTY"]);
							$PAY_DATE						= trim($arr_rs[$j]["PAY_DATE"]);
							$CONFIRM_TF					= trim($arr_rs[$j]["CONFIRM_TF"]);
							$CONFIRM_DATE				= trim($arr_rs[$j]["CONFIRM_DATE"]);

							$ORDER_DATE					= trim($arr_rs[$j]["ORDER_DATE"]);
							$REG_DATE						= trim($arr_rs[$j]["REG_DATE"]);

							if (($CONFIRM_TF == "N") || ($CONFIRM_TF == "") ) {
								$CONFIRM_DATE		= "";
								$str_confirm = "<font color = 'gray'>미정산</font>";
							} else {
								$CONFIRM_DATE		= date("Y-m-d H:i",strtotime($CONFIRM_DATE));
								$str_confirm = "<font color = 'navy'>정산</font>";
							}

							$GOODS_OPTION_01		= trim($arr_rs[$j]["GOODS_OPTION_01"]);
							$GOODS_OPTION_02		= trim($arr_rs[$j]["GOODS_OPTION_02"]);
							$GOODS_OPTION_03		= trim($arr_rs[$j]["GOODS_OPTION_03"]);
							$GOODS_OPTION_04		= trim($arr_rs[$j]["GOODS_OPTION_04"]);
							$GOODS_OPTION_NM_01	= trim($arr_rs[$j]["GOODS_OPTION_NM_01"]);
							$GOODS_OPTION_NM_02	= trim($arr_rs[$j]["GOODS_OPTION_NM_02"]);
							$GOODS_OPTION_NM_03	= trim($arr_rs[$j]["GOODS_OPTION_NM_03"]);
							$GOODS_OPTION_NM_04	= trim($arr_rs[$j]["GOODS_OPTION_NM_04"]);

							$option_str = "";

							if ($GOODS_OPTION_NM_01 <> "") {
								$option_str .= $GOODS_OPTION_NM_01." : ".$GOODS_OPTION_01."&nbsp;";
							}

							if ($GOODS_OPTION_NM_02 <> "") {
								$option_str .= $GOODS_OPTION_NM_02." : ".$GOODS_OPTION_02."&nbsp;";
							}

							if ($GOODS_OPTION_NM_03 <> "") {
								$option_str .= $GOODS_OPTION_NM_03." : ".$GOODS_OPTION_03."&nbsp;";
							}

							if ($GOODS_OPTION_NM_04 <> "") {
								$option_str .= $GOODS_OPTION_NM_04." : ".$GOODS_OPTION_04."&nbsp;";
							}

							
							$ORDER_DATE		= date("Y-m-d",strtotime($ORDER_DATE));
							$REG_DATE			= date("Y-m-d",strtotime($REG_DATE));
							
				?>
					<tr height="37">
						<td class="order"><input type="checkbox" name="chk_no[]" value="<?=$ORDER_GOODS_NO?>"></td>
						<td ><?=$PAY_DATE?></a></td>
						<td class="modeual_nm"><?= getCompanyName($conn, $BUY_CP_NO);?></td>
						<td class="modeual_nm"><?= $GOODS_NAME?></td>
						<td class="modeual_nm"><?= $option_str?></td>
						<td class="price"><?=number_format($BUY_PRICE)?></td>
						<td class="price"><?=number_format($QTY)?></td>
						<td class="price"><?=number_format($BUY_PRICE * $QTY)?></td>
						<td><?=$REG_DATE?></td>
						<td><?=$str_confirm?></td>
						<td><?=$CONFIRM_DATE?></td>
					</tr>
					<?
									
						}



						if (sizeof($arr_rs_all) > 0) {
							for ($j = 0 ; $j < sizeof($arr_rs_all); $j++) {
								$ALL_BUY_PRICE		= trim($arr_rs_all[$j]["ALL_BUY_PRICE"]);
								$ALL_QTY					= trim($arr_rs_all[$j]["ALL_QTY"]);
							}
						}
					?>

					<!-- 합계 -->
						<tr class="goods_end">
							<td colspan="11">
								&nbsp;
							</td>
						</tr>
						<tr class="goods_end" height="35">
							<td class="filedown" colspan="2">합 계</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td class="modeual_nm" colspan="1" ></td>
							<td class="price"><?=number_format($ALL_QTY)?></td>
							<td class="price"><?=number_format($ALL_BUY_PRICE)?></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>

					<?

					}else{
						?>
						<tr class="order">
							<td height="50" align="center" colspan="11">데이터가 없습니다. </td>
						</tr>
					<?
						}
					?>
				</tbody>
			</table>

			<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">
<? if ($sPageRight_U == "Y") {?>
				정산일 :
				<input type="text" class="txt" style="width: 75px;" name="confirm_ymd" value="<?=$confirm_ymd?>" maxlength="10" readonly="1" />
				<a href="javascript:show_calendar('document.frm.confirm_ymd', document.frm.confirm_ymd.value);" onFocus="blur();"><!--
				--><img src="/manager/images/bu/ic_calendar.gif" alt="" /></a>&nbsp;&nbsp;
	<input type="button" name="aa" value=" 정산처리 " class="btntxt" onclick="js_confirm();"> 
	<input type="button" name="aa" value=" 정산취소 " class="btntxt" onclick="js_cancel_confirm();"> 
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
							//$sel_order_state, $cp_type, $cp_type2, $sel_pay_type, $con_use_tf,
							$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&start_date=".$start_date."&end_date=".$end_date;
							$strParam = $strParam."&sel_order_state=".$sel_order_state."&cp_type=".$cp_type."&cp_type2=".$cp_type2."&sel_pay_type=".$sel_pay_type;
							$strParam = $strParam."&order_field=".$order_field."&order_str=".$order_str."&sel_confirm_tf=".$sel_confirm_tf;

					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- 페이지 처리 화면 END -------------------------->
				<br />

				<div class="sp10"></div>
		<!-- // E: mwidthwrap -->
			</div>
			<!-- // E: mwidthwrap -->

		</td>
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