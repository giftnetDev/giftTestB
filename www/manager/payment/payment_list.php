<?session_start();?>
<?
# =============================================================================
# File Name    : payment_list.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @아름지기 Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");


#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "I0001"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/payment/payment.php";

	if ($mode == "T") {
		//updateBoardUseTF($conn, $use_tf, $s_adm_no, $bb_code, $bb_no);
	}

#====================================================================
# Request Parameter
#====================================================================

	$mm_subtree	 = "3";

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
		$nPageSize = 10;
	}

	$nPageBlock	= 10;
	
#	echo $start_date;
#	echo $end_date;
#echo $sel_pay_reason;
	$condition = "";
#===============================================================
# Get Search list count
#===============================================================

	$nListCnt =totalCntPayment($conn, $start_date, $end_date, $sel_pay_type, $sel_pay_state, $reserve_no, $mem_no, trim($sel_pay_reason), $cash_receipt_state, $con_use_tf, $del_tf, $search_field, $search_str, $condition);
	
	#echo $nListCnt;

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listPayment($conn, $start_date, $end_date, $sel_pay_type, $sel_pay_state, $reserve_no, $mem_no, trim($sel_pay_reason), $cash_receipt_state, $use_tf, $del_tf, $search_field, $search_str, $condition, $nPage, $nPageSize);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="STYLESHEET" type="text/css" href="../css/bbs.css" />
<link rel="STYLESHEET" type="text/css" href="../css/layout.css" />

<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/calendar.js"></script>
<script language="javascript">

	function js_write() {
		document.location.href = "payment_write.php";
	}

	function js_view(rn, pay_no) {

		var frm = document.frm;
		
		frm.pay_no.value = pay_no;
		frm.mode.value = "S";
		frm.target = "";
		frm.method = "get";
		frm.action = "payment_write.php";
		frm.submit();
		
	}

	// 조회 버튼 클릭 시 
	function js_search() {
		var frm = document.frm;

		frm.nPage.value = "1";
		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

function js_toggle(pay_no, use_tf) {
	var frm = document.frm;

	bDelOK = confirm('공개 여부를 변경 하시겠습니까?');
		
	if (bDelOK==true) {

		if (use_tf == "Y") {
			use_tf = "N";
		} else {
			use_tf = "Y";
		}

		frm.pay_no.value = pay_no;
		frm.use_tf.value = use_tf;
		frm.mode.value = "T";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}
}

function js_con_cate_01 () {
	frm.nPage.value = "1";
	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();
}

function js_con_cate_02 () {
	frm.nPage.value = "1";
	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();
}

function js_con_cate_03 () {
	frm.nPage.value = "1";
	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();
}
</script>
</head>

<body id="bg">
<div id="wrap">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="rn" value="">
<input type="hidden" name="pay_no" value="">
<input type="hidden" name="use_tf" value="">
<input type="hidden" name="seq_no" value="" />
<input type="hidden" name="mode" value="">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>">
<?
	#====================================================================
	# common top_area
	#====================================================================

	require "../../_common/top_area.php";

	#====================================================================
	# common left_area
	#====================================================================

	require "../../_common/left_area.php";
?>

	<div id="contents">
		<p><a href="/">홈</a> &gt; 입금 관리</p>
		
		<div id="tit01">
			<h2> 입금조회</h2>
		</div>

		<div id="bbsWrite">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<colgroup>
					<col width="120" />
					<col width="*" />
					<col width="120" />
					<col width="*" />
				</colgroup>
				<thead>
					<tr>
						<td class="lpd20">조회기간 :</td>
						<td>
							<input type="text" class="box01" style="width: 75px;" name="start_date" value="<?=$start_date?>" maxlength="10" readonly="1" />
							<a href="javascript:show_calendar('document.frm.start_date', document.frm.start_date.value);" onFocus="blur();"><!--
						--><img src="../images/bu/ic_calendar.gif" alt="" /></a>  ~ 

							<input type="text" class="box01" style="width: 75px;" name="end_date" value="<?=$end_date?>" maxlength="10" readonly="1" />
							<a href="javascript:show_calendar('document.frm.end_date', document.frm.end_date.value);" onFocus="blur();"><!--
						--><img src="../images/bu/ic_calendar.gif" alt="" /></a>
						</td>
						<td class="lpd20">입금사유 :</td>
						<td>
							<?= makePayReasonSelectBox($conn,"sel_pay_reason","125","선택","",$sel_pay_reason)?>
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="lpd20">입금방식 :</td>
						<td>
							<?= makeSelectBox($conn,"PAY_TYPE","sel_pay_type","125","선택","",$sel_pay_type)?>
						</td>
						<td class="lpd20">입금상태 :</td>
						<td>
							<?= makeSelectBox($conn,"PAY_STATE","sel_pay_state","125","선택","",$sel_pay_state)?>
						</td>
					</tr>
					<tr>
						<td class="lpd20">&nbsp</td>
						<td>
							&nbsp
						</td>
						<td class="lpd20">검색조건 :</td>
						<td>
							<select name="search_field" style="width:84px;">
								<option value="MEM_NM" <? if ($search_field == "MEM_NM") echo "selected"; ?> >회원명</option>
								<option value="RESERVE_NO" <? if ($search_field == "RESERVE_NO") echo "selected"; ?> >예약번호</option>
							</select>&nbsp;

							<input type="text" value="<?=$search_str?>" name="search_str" class="box01" />
							<a href="javascript:js_search();"><img src="../images/common/btn/btn_go01.gif" alt="go" /></a>
						</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="10"></td>
					</tr>
				</tfoot>
			</table>
		</div>

		<div id="bbsList">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<colgroup>
					<col width="5%" />
					<col width="8%" />
					<col width="10%" />
					<col width="10%" />
					<col width="13%" />
					<col width="10%" />
					<col width="10%" />
					<col width="10%" />
					<col width="10%" />
					<col width="10%" />
				</colgroup>
				<thead>
					<tr>
						<th>번호</th>
						<th>회원구분</th>
						<th>회원명</th>
						<th>입금사유</th>
						<th>입금방식</th>
						<th>금액</th>
						<th>상태</th>
						<th>신청일</th>
						<th>입금일</th>
						<th>최소일</th>
					</tr>
				</thead>
				<tbody>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							$rn							= trim($arr_rs[$j]["rn"]);
							$PAY_NO					= trim($arr_rs[$j]["PAY_NO"]);
							$PAY_REASON			= trim($arr_rs[$j]["PAY_REASON"]);
							$MEM_TYPE				= trim($arr_rs[$j]["MEM_TYPE"]);
							
							$PAY_EXT				= trim($arr_rs[$j]["PAY_EXT"]);
							$PAY_TYPE				= trim($arr_rs[$j]["PAY_TYPE"]);
							$PAY_STATE			= trim($arr_rs[$j]["PAY_STATE"]);
							$MEM_NM					= trim($arr_rs[$j]["MEM_NM"]);
							$NM							= trim($arr_rs[$j]["NM"]);
							$CMS_AMOUNT			= trim($arr_rs[$j]["CMS_AMOUNT"]);
							$BANK_AMOUNT		= trim($arr_rs[$j]["BANK_AMOUNT"]);
							$CARD_AMOUNT		= trim($arr_rs[$j]["CARD_AMOUNT"]);
							$PGBANK_AMOUNT	= trim($arr_rs[$j]["PGBANK_AMOUNT"]);
							$RESERVE_NO			= trim($arr_rs[$j]["RESERVE_NO"]);
							$REQ_DATE				= trim($arr_rs[$j]["REQ_DATE"]);
							$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);
							$PAID_DATE			= trim($arr_rs[$j]["PAID_DATE"]);
							$CANCEL_DATE		= trim($arr_rs[$j]["CANCEL_DATE"]);
							
							if ($MEM_TYPE == "") {
								$str_mem_type = "비회원";
								$MEM_NM = $NM;
							} else {
								$str_mem_type = getDcodeName($conn, "MEM_TYPE", $MEM_TYPE);
							}
							
							$amount = "0";
							
							$amount = ($CMS_AMOUNT + $BANK_AMOUNT + $CARD_AMOUNT + $PGBANK_AMOUNT);

							$REG_DATE			= date("Y-m-d",strtotime($REG_DATE));

							if (($REQ_DATE == "") || ($REQ_DATE == "0000-00-00 00:00:00")) { 
								$REQ_DATE		= "&nbsp;";
							} else {
								$REQ_DATE		= date("Y-m-d",strtotime($REQ_DATE));
							}

							if ($PAID_DATE == "") { 
								$PAID_DATE		= "&nbsp;";
							} else {
								$PAID_DATE		= date("Y-m-d",strtotime($PAID_DATE));
							}

							if ($CANCEL_DATE == "") { 
								$CANCEL_DATE		= "&nbsp;";
							} else {
								$CANCEL_DATE	= date("Y-m-d",strtotime($CANCEL_DATE));
							}

				
							if ($USE_TF == "Y") {
								$STR_USE_TF = "<font color='navy'>공개</font>";
							} else {
								$STR_USE_TF = "<font color='red'>비공개</font>";
							}
						?>
						<tr>
							<td class="font"><?= $rn ?></td>
							<td><?=$str_mem_type?></td>
							<td><a href="javascript:js_view('<?=$rn?>','<?=$PAY_NO?>');"><?=$MEM_NM?></a></td>
							<td><a href="javascript:js_view('<?=$rn?>','<?=$PAY_NO?>');"><?=$PAY_REASON?></a></td>
							<td>
							<?
								if ($PAY_EXT <> "") {
									echo $PAY_EXT;
								} else {
									echo getDcodeName($conn, "PAY_TYPE",$PAY_TYPE);
								}
							?>
							</td>
							<td class="rpd10"><?=number_format($amount)?> 원</td>
							<td><?=getDcodeName($conn, "PAY_STATE", $PAY_STATE);?></td>
							<td><?=$REQ_DATE?></td>
							<td><?=$PAID_DATE?></td>
							<td><?=$CANCEL_DATE?></td>
							<!--
							<td class="lpd10"><?=$TITLE?></a></td>
							<td><a href="javascript:js_toggle('<?=$BB_CODE?>','<?=$BB_NO?>','<?=$USE_TF?>');"><?= $STR_USE_TF ?></a></td>
							<td><?= $REG_DATE ?></td>
							-->
						</tr>
						<?
						}
					}else{
						?>
						<tr>
							<td height="50" align="center" colspan="10">데이터가 없습니다. </td>
						</tr>
					<?
						}
					?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="10"></td>
					</tr>
				</tfoot>
			</table>
			<!--
			<span class="btn_write">
				<? if ($sPageRight_I == "Y") {?>
				<a href="javascript:js_write();"><img src="../images/common/btn/btn_app.gif" alt="등록" /></a>
				<? } ?>
			</span>
			-->
		</div>
				<!-- --------------------- 페이지 처리 화면 START -------------------------->
				<?
					# ==========================================================================
					#  페이징 처리
					# ==========================================================================
					if (sizeof($arr_rs) > 0) {
						#$search_field		= trim($search_field);
						#$search_str			= trim($search_str);
						$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&start_date=".$start_date."&end_date=".$end_date."&sel_pay_reason=".$sel_pay_reason."&sel_pay_type=".$sel_pay_type."&sel_pay_state=".$sel_pay_state;

				?>
				<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
				<?
					}
				?>
				<!-- --------------------- 페이지 처리 화면 END -------------------------->
		<!-- // E: mwidthwrap -->
	</div>

	<div id="site_info">Copyright &copy; 2009 (재)아름지기 All Rights Reserved.</div>

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