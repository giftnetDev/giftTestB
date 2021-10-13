<?session_start();?>
<?

#=========================================================================
# 월별 미수 내역 조회
#=========================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "CF012"; // 메뉴마다 셋팅 해 주어야 합니다

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
# DML Process
#====================================================================

	$cp_no				= trim($cp_no);
	$cp_nm				= trim($cp_nm);
	$collect			= trim($collect);
	//$start_date			= trim($start_date);

	if ($mode == "U") {

		$prev_0 = trim(str_replace(",", "", $prev_0));
		$prev_1 = trim(str_replace(",", "", $prev_1));
		$prev_2 = trim(str_replace(",", "", $prev_2));
		$prev_3 = trim(str_replace(",", "", $prev_3));
		$result = updateAccountReceivableReport($conn, $cp_no, $memo, $prev_0, $prev_1, $prev_2, $prev_3, $except_tf, $s_adm_no);

		//$result = updateAccountReceivableReport($conn, $cp_no, $memo, $prev_1, $prev_2, $prev_3, $except_tf, $s_adm_no);
		mysql_close($conn);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<script>
	alert('수정 되었습니다');
	opener.js_search();
	self.close();
</script>
</head>
</html>
<?
		exit;
	}


	

	$arr_rs_report = selectAccountReceivableReport($conn, $cp_no);
		
	if(sizeof($arr_rs_report) > 0) { 
		$MEMO		= trim($arr_rs_report[0]["MEMO"]);
		$PREV_0		= trim($arr_rs_report[0]["PREV_0"]);
		$PREV_1		= trim($arr_rs_report[0]["PREV_1"]);
		$PREV_2		= trim($arr_rs_report[0]["PREV_2"]);
		$PREV_3		= trim($arr_rs_report[0]["PREV_3"]);
		$PREV_4		= trim($arr_rs_report[0]["PREV_4"]);
		$EXCEPT_TF	= trim($arr_rs_report[0]["EXCEPT_TF"]);
		$UP_ADM		= trim($arr_rs_report[0]["UP_ADM"]);
		$UP_DATE	= trim($arr_rs_report[0]["UP_DATE"]);
	}

	if($PREV_4 == "")
		$PREV_4 = "...";

	if($UP_ADM == "")
		$UP_ADM = "...";
		
	if($UP_DATE == "")
		$UP_DATE = "";
	else
		$UP_DATE = date("Y-m-d H:i:s",strtotime($UP_DATE));

	$arr_rs = listPrevBalanceByMonth($conn, $cp_no, $UP_DATE);

?>


<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->
<script type="text/javascript" src="../../_common/SE2.1.1.8141/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script type="text/javascript">

	function js_save() {
		var frm = document.frm;

		frm.mode.value = "U";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

</script>

</head>
<body id="popup_file">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="cp_no" value="<?=$cp_no?>">
<input type="hidden" name="mode" value="">
<div id="popupwrap_file">
	<h1>월별 이전 미수 (<?=$cp_nm?>)</h1>
	<div class="btn_right">
		최종 수정자 : <?=getAdminName($conn, $UP_ADM)?>, 최종 수정일 : <?=$UP_DATE?>
	</div>
	<div id="postsch">
		<div class="addr_inp">
			
			<table cellpadding="0" cellspacing="0" class="rowstable" border="0">
				<!--
				<colgroup>
					<col width="9%" />
					<col width="8%" />
					<col width="8%" />
					<col width="8%" />
				</colgroup>
				-->
				<thead>
				<tr>
					<th>년월</th>
					<th>매출(+)</th>
					<th>추가(+)</th>
					<th>입금(-)</th>
					<th class="end">이월잔액</th>
				</tr>
				<?
					$PREV_BALANCE = 0;
					$TOTAL_SUM_DEPOSIT = 0;
					$TOTAL_SUM_APPEND = 0;
					$TOTAL_SUM_WITHDRAW = 0;
					$TOTAL_SUM_DEPOSIT_START = 0;
					$TOTAL_SUM_WITHDRAW_START = 0;


					if (sizeof($arr_rs) > 0) {
						
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							$chk_prev_1 = false;
							$chk_prev_2 = false;
							$chk_prev_3 = false;
							
							$GROUP_MONTH					= trim($arr_rs[$j]["GROUP_MONTH"]);
							$SUM_DEPOSIT					= trim($arr_rs[$j]["SUM_DEPOSIT"]);
							$SUM_APPEND						= trim($arr_rs[$j]["SUM_APPEND"]);
							$SUM_WITHDRAW					= trim($arr_rs[$j]["SUM_WITHDRAW"]);
							$SUM_DEPOSIT_START				= trim($arr_rs[$j]["SUM_DEPOSIT_START"]);
							$SUM_WITHDRAW_START				= trim($arr_rs[$j]["SUM_WITHDRAW_START"]);

							$SUM_LATER_WITHDRAW				= trim($arr_rs[$j]["SUM_LATER_WITHDRAW"]);
							

							if($GROUP_MONTH != date("Y-m",strtotime("0 month"))) { 

								if($SUM_DEPOSIT_START <> 0)
								$SUM_DEPOSIT = $SUM_DEPOSIT_START;

								if($SUM_WITHDRAW_START <> 0)
									$SUM_WITHDRAW = $SUM_WITHDRAW_START;

								
								$TOTAL_SUM_DEPOSIT	+= $SUM_DEPOSIT;
								$TOTAL_SUM_APPEND	+= $SUM_APPEND;
								$TOTAL_SUM_WITHDRAW += $SUM_WITHDRAW;

								$chk_prev_1 = (date("Y-m",strtotime("-1 month")) == $GROUP_MONTH);
								$chk_prev_2 = (date("Y-m",strtotime("-2 month")) == $GROUP_MONTH);
								$chk_prev_3 = (date("Y-m",strtotime("-3 month")) == $GROUP_MONTH);
				?>
				<?
					if($j == 0 && ($SUM_DEPOSIT_START <> 0 || $SUM_WITHDRAW_START <> 0)) { 
				?>
				<tr height="30">
					<td>실사이전</td>
					<td><?=number_format($SUM_DEPOSIT)?></td>
					<td></td>
					<td><?=number_format($SUM_WITHDRAW)?></td>
					<td><?=0?></td>
				</tr>
				<?
						$PREV_BALANCE = $SUM_DEPOSIT - $SUM_WITHDRAW;
						continue;
					}

				?>
				<tr height="30">
					<td><?=$GROUP_MONTH?></td>
					<td>
						
						<a class="<? if($chk_prev_1) { ?>prev_1<? } ?><? if($chk_prev_2) { ?>prev_2<? } ?><? if($chk_prev_3) { ?>prev_3<? } ?>"
						 data-value="<?=floor($SUM_DEPOSIT)?>" <? if($chk_prev_1 || $chk_prev_2 || $chk_prev_3) { ?>style="text-decoration:underline;"<? } ?>>
						<?=number_format($SUM_DEPOSIT)?></a>
					</td>
					<td><?=number_format($SUM_APPEND)?></td>
					<td>
						<?=number_format($SUM_WITHDRAW)?>
					</td>
					<td>
						
						<a class="<? if($chk_prev_1) { ?>prev_1<? } ?><? if($chk_prev_2) { ?>prev_2<? } ?><? if($chk_prev_3) { ?>prev_3<? } ?>"
						 data-value="<?=floor($PREV_BALANCE + $SUM_DEPOSIT - $SUM_WITHDRAW)?>" <? if($chk_prev_1 || $chk_prev_2 || $chk_prev_3) { ?>style="text-decoration:underline;"<? } ?>>
						<?=number_format($PREV_BALANCE + $SUM_DEPOSIT - $SUM_WITHDRAW)?>
						</a>
					</td>
				</tr>
				<?
							$PREV_BALANCE = $PREV_BALANCE + $SUM_DEPOSIT - $SUM_WITHDRAW;
						
						} else { 

				?>

				<tr height="50">
					<td><?=$GROUP_MONTH?></td>
					<td colspan="4" style="text-align:left;">
						<? if($UP_DATE <> "") { ?>
							<? if($SUM_LATER_WITHDRAW > 0) { ?>
								이번달 입금액 <?=number_format($SUM_WITHDRAW)?> 중
								최종확인(<b><?= date("n월j일",strtotime($UP_DATE))?></b>) 이후 입금액 <b><?=number_format($SUM_LATER_WITHDRAW)?></b> 
								<input type="button" onclick="js_calc_withdraw('');" value="순차적 차감">
							<? } else { ?>
								이번달 입금액 <?=number_format($SUM_WITHDRAW)?> 이전 보고시 확인완료
							<? } ?>

						<? } else { ?>
							이번달 입금액 <?=number_format($SUM_WITHDRAW)?>
						<? } ?>

						
					</td>
				</tr>

				<?
						}


						}
					} else { 
				?>
				<tr height="50">
					<td colspan="5">데이터가 없습니다.</td>
				</tr>
				<?  }  ?>
				<tr height="15">
					<td colspan="5">&nbsp;</td>
				</tr>
				
				<tr height="30">
					<td><b>월 합계 : </b></td>
					<td><?=number_format($TOTAL_SUM_DEPOSIT)?></td>
					<td><?=number_format($TOTAL_SUM_APPEND)?></td>
					<td><?=number_format($TOTAL_SUM_WITHDRAW)?></td>
					<td><b>이월 잔액</b><br/></br><b><?=number_format($TOTAL_SUM_DEPOSIT + $TOTAL_SUM_APPEND - $TOTAL_SUM_WITHDRAW + $TOTAL_SUM_DEPOSIT_START - $TOTAL_SUM_WITHDRAW_START)?></b></td>
				</tr>
				
				
			</table>
			<div class="sp20"></div>
			<h2>* 이전 월 미수금 설정입니다.</h2>
			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="colstable">
				<colgroup>
					<col width="20%">
					<col width="80%">
				</colgroup>
				<tr>
					<th><?=date("n월",strtotime("0 month"))?></th>
					<td>
						<input type="text" name="prev_0" value="<?=$PREV_0?>"/> 원
					</td>
				</tr>
				<tr>
					<th><?=date("n월",strtotime("-1 month"))?></th>
					<td>
						<input type="text" name="prev_1" value="<?=$PREV_1?>"/> 원 <? if($SUM_LATER_WITHDRAW > 0 && $UP_DATE <> "") { ?><input type="button" onclick="js_calc_withdraw('prev_1');" value="차감"/><? } ?>
					</td>
				</tr>
				<tr>
					<th><?=date("n월",strtotime("-2 month"))?></th>
					<td>
						<input type="text" name="prev_2" value="<?=$PREV_2?>"/> 원 <? if($SUM_LATER_WITHDRAW > 0 && $UP_DATE <> "") { ?><input type="button" onclick="js_calc_withdraw('prev_2');" value="차감"/><? } ?>
					</td>
				</tr>
				<tr>
					<th><?=date("n월",strtotime("-3 month"))?></th>
					<td>
						<input type="text" name="prev_3" value="<?=$PREV_3?>"/> 원 <? if($SUM_LATER_WITHDRAW > 0 && $UP_DATE <> "") { ?><input type="button" onclick="js_calc_withdraw('prev_3');" value="차감"/><? } ?>
					</td> 
				</tr>
				<? if($UP_DATE <> "") { ?>
					<? if($SUM_LATER_WITHDRAW > 0) { ?>
				<tr>
					<th>입금 잔여</th>
					<td>
						<span name="sum_later_withdraw"><?=number_format($SUM_LATER_WITHDRAW)?></span>
					</td> 
				</tr>
				<?  } 
				} else { 
				?>
				<tr height="40">
					<td colspan="2">
						<b>이전 미수 처리 기록이 없는 최초 입력입니다. 현재까지의 입금액 까지 차감해서 이전월 미수 입력해주세요. 이후 자동계산됩니다.</b>
					</td> 
				</tr>
				<? } ?>
			</table>
			<script type="text/javascript">
				function js_calc_withdraw(target_name) { 
					var int_sum_later_withdraw = $("[name=sum_later_withdraw]").html().replaceall(",","");
					var int_prev_1 = $("[name=prev_1]").val().replaceall(",","");
					var int_prev_2 = $("[name=prev_2]").val().replaceall(",","");
					var int_prev_3 = $("[name=prev_3]").val().replaceall(",","");

					if(target_name == "") { 
						if(int_prev_3 > 0 && int_sum_later_withdraw > 0){ 
							
							if(int_sum_later_withdraw - int_prev_3 >= 0)
								$("[name=prev_3]").val(0);
							else
								$("[name=prev_3]").val(int_prev_3 - int_sum_later_withdraw);
							int_sum_later_withdraw = int_sum_later_withdraw - int_prev_3;
						}
						if(int_prev_2 > 0 && int_sum_later_withdraw > 0){ 
							
							if(int_sum_later_withdraw - int_prev_2 >= 0)
								$("[name=prev_2]").val(0);
							else
								$("[name=prev_2]").val(int_prev_2 - int_sum_later_withdraw);
							int_sum_later_withdraw = int_sum_later_withdraw - int_prev_2;
						}
						if(int_prev_1 > 0 && int_sum_later_withdraw > 0){ 
							
							if(int_sum_later_withdraw - int_prev_1 >= 0)
								$("[name=prev_1]").val(0);
							else
								$("[name=prev_1]").val(int_prev_1 - int_sum_later_withdraw);
							int_sum_later_withdraw = int_sum_later_withdraw - int_prev_1;
						}
						
					} else {
						
						if(target_name == "prev_1") { 
							if(int_prev_1 > 0 && int_sum_later_withdraw > 0){ 
							
								if(int_sum_later_withdraw - int_prev_1 >= 0)
									$("[name=prev_1]").val(0);
								else
									$("[name=prev_1]").val(int_prev_1 - int_sum_later_withdraw);
								int_sum_later_withdraw = int_sum_later_withdraw - int_prev_1;
							}
						} else if(target_name == "prev_2") { 
							if(int_prev_2 > 0 && int_sum_later_withdraw > 0){ 
							
								if(int_sum_later_withdraw - int_prev_2 >= 0)
									$("[name=prev_2]").val(0);
								else
									$("[name=prev_2]").val(int_prev_2 - int_sum_later_withdraw);
								int_sum_later_withdraw = int_sum_later_withdraw - int_prev_2;
							}

						} else if(target_name == "prev_3") { 
							if(int_prev_3 > 0 && int_sum_later_withdraw > 0){ 
							
								if(int_sum_later_withdraw - int_prev_3 >= 0)
									$("[name=prev_3]").val(0);
								else
									$("[name=prev_3]").val(int_prev_3 - int_sum_later_withdraw);
								int_sum_later_withdraw = int_sum_later_withdraw - int_prev_3;
							}

						}
					}

					$("[name=sum_later_withdraw]").html(numberFormat(Math.max(0, int_sum_later_withdraw)));
					
				}
			</script>

			<h2>* 미수 메모 입니다. 등록을 클릭하면 해당 내용이 저장 됩니다.</h2>
			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="colstable">
				<colgroup>
					<col width="20%">
					<col width="80%">
				</colgroup>
					<tr>
						<td colspan="2" class="lpd20 rpd20 right" style="border:none;">
							<textarea name="memo" class="txt" style="width:100%" rows="5"><?=$MEMO ?></textarea>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<label style="color:red;"><input type="checkbox" name="except_tf" value="Y" <?if($EXCEPT_TF == "Y") echo "checked";?>/> 미수금 계산 제외</label>
						</td>
					</tr>
			</table>
			<br/>
			
			<div class="btn">
				<a href="javascript:js_save();"><img src="../images/admin/btn_regist_02.gif" alt="등록" /></a>
			</div>

			<script type="text/javascript">
				$(function(){
					$(".prev_1").click(function(){
						$("[name=prev_1]").val($(this).data("value"));
					});

					$(".prev_2").click(function(){
						$("[name=prev_2]").val($(this).data("value"));
					});

					$(".prev_3").click(function(){
						$("[name=prev_3]").val($(this).data("value"));
					});
				});
			</script>
		</div>
	</div>
	<br />
	<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
</div>
<iframe src="" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>

</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>