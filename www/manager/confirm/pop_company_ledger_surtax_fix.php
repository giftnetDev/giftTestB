<?session_start();?>
<?

#=========================================================================
# 발주서에서 매입 비용 추가 - 발주서에서 추가하는걸로 변경으로 사용안함 (2017-05-12)
#=========================================================================

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
	$mode	= trim($mode);
	
	$result	= false  ;

#====================================================================
# DML Process
#====================================================================

	if($mode == "RECALC") { 
		
		/*
		echo "calc_susu_rate : ".$calc_susu_rate."<br/>";
		for($i = 0; $i < sizeof($cl_no); $i ++) { 
			echo $cl_no[$i]."<br/>";
		}
		*/

		updateCompanyLedgerBySusuRate($conn, $cl_no, round($calc_susu_rate, 5), $s_adm_no);
?>
<script language="javascript">
		alert('적용되었습니다.');
		window.parent.opener.js_search();
</script>
<?
	}

	if($mode == "" && count($cl_no) == 0) {
?>
<script language="javascript">
		alert('선택된 기장이 없습니다. 체크박스로 변경하실 기장을 리스트에서 먼저 선택해주세요.');
		self.close();
</script>
<?
		exit;
	}

	$arr_rs = listCompanyLedgerByClNos($conn, $chk_no);
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
<script language="javascript">

	// 저장 버튼 클릭 시 
	function js_recalc_ledger(calc_susu_rate) {
		
		var frm = document.frm;

		frm.calc_susu_rate.value = calc_susu_rate;
		
		frm.mode.value = "RECALC";

		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	function js_all_check() {
		var frm = document.frm;
		
		if (frm['cl_no[]'] != null) {
			
			if (frm['cl_no[]'].length != null) {

				if (frm.all_chk.checked == true) {
					for (i = 0; i < frm['cl_no[]'].length; i++) {
						frm['cl_no[]'][i].checked = true;
					}
				} else {
					for (i = 0; i < frm['cl_no[]'].length; i++) {
						frm['cl_no[]'][i].checked = false;
					}
				}
			} else {
			
				if (frm.all_chk.checked == true) {
					frm['cl_no[]'].checked = true;
				} else {
					frm['cl_no[]'].checked = false;
				}
			}
		}
	}

</script>
</head>
<body id="popup_file">

<form name="frm" method="post" enctype="multipart/form-data">

	<input type="hidden" name="mode" value="" />
	<input type="hidden" name="calc_susu_rate" value=""/>
<?
	if($chk_no != null)
	{
		$postvalue = "";
		foreach ($chk_no as $cl_no) {
		  $postvalue .= '<input type="hidden" name="chk_no[]" value="' .$cl_no. '" />';
		}
		echo $postvalue;
	}
?>

<div id="popupwrap_file">
	<h1>수수료 재 계산</h1>
	<div id="postsch">
		<div class="addr_inp">
			<div class="sp10"></div>
			* 대입 (카드 처리)
			<table cellpadding="0" cellspacing="0" class="rowstable" border="0">
				<colgroup>
					<col width="3%" />
					<col width="10%" />
					<col width="5%" />
					<col width="*"/>
					<col width="5%" />
					<col width="10%" />
					<col width="10%" />
					<col width="10%" />
					<col width="10%" />
					<col width="10%" />
				</colgroup>
				<thead>
				<tr>
					<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
					<th>날짜</th>
					<th>구분</th>
					<th>상품명</th>
					<th>수량</th>
					<th>단가</th>
					<th>매출/지급액</th>
					<th>매입/입금액</th>
					<th>부가세</th>
					<th>수수율(%)</th>
				</tr>
				<?
					$SUM_QTY = 0;
					$SUM_UNIT_PRICE = 0;
					$SUM_WITHDRAW = 0;
					$SUM_DEPOSIT = 0;
					$SUM_SURTAX = 0;
					$arr_group_no = array();
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							//CL_NO, CP_NO, INOUT_DATE, INOUT_TYPE, NAME, QTY, UNIT_PRICE, WITHDRAW, DEPOSIT, RESERVE_NO

							$CL_NO						= trim($arr_rs[$j]["CL_NO"]);
							$INOUT_DATE					= trim($arr_rs[$j]["INOUT_DATE"]);
							$INOUT_TYPE					= trim($arr_rs[$j]["INOUT_TYPE"]);
							$GROUP_NO					= trim($arr_rs[$j]["GROUP_NO"]);
							$GOODS_NO					= trim($arr_rs[$j]["GOODS_NO"]);
							$NAME						= trim($arr_rs[$j]["NAME"]);
							$QTY						= trim($arr_rs[$j]["QTY"]);
							$UNIT_PRICE					= trim($arr_rs[$j]["UNIT_PRICE"]);
							$WITHDRAW					= trim($arr_rs[$j]["WITHDRAW"]);
							$DEPOSIT					= trim($arr_rs[$j]["DEPOSIT"]);
							$SURTAX						= trim($arr_rs[$j]["SURTAX"]);
							$CATE_01					= trim($arr_rs[$j]["CATE_01"]);
							$TAX_TF						= trim($arr_rs[$j]["TAX_TF"]);
							$MEMO						= trim($arr_rs[$j]["MEMO"]);
							$RESERVE_NO					= trim($arr_rs[$j]["RESERVE_NO"]);
							$ORDER_GOODS_NO				= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
							$RGN_NO						= trim($arr_rs[$j]["RGN_NO"]);
							$TO_CP_NO					= trim($arr_rs[$j]["TO_CP_NO"]);
							$INPUT_TYPE					= trim($arr_rs[$j]["INPUT_TYPE"]);

							$USE_TF						= trim($arr_rs[$j]["USE_TF"]);

							$TAX_CONFIRM_TF				= trim($arr_rs[$j]["TAX_CONFIRM_TF"]);
							$TAX_CONFIRM_DATE			= trim($arr_rs[$j]["TAX_CONFIRM_DATE"]);

							//$CF_CODE					= trim($arr_rs[$j]["CF_CODE"]);'

							$WITHDRAW = floor($WITHDRAW);
							$DEPOSIT = floor($DEPOSIT);
							$SURTAX = floor($SURTAX);


							$INOUT_DATE = date("Y-m-d",strtotime($INOUT_DATE));

							if($INOUT_TYPE != "대입") continue;

							if($USE_TF != "Y") { 
								$QTY = 0;
								$WITHDRAW = 0;
								$DEPOSIT = 0;
								$SURTAX = 0;
							}

							$SUM_QTY += $QTY;
							$SUM_UNIT_PRICE += $UNIT_PRICE;
							$SUM_WITHDRAW += $WITHDRAW;
							$SUM_DEPOSIT += $DEPOSIT;
							$SUM_SURTAX += $SURTAX;

							$susu_rate = $SURTAX * 100 / $UNIT_PRICE;

							array_push($arr_group_no, $GROUP_NO); 
							
							?>
				<tr height="30" class="<?if($USE_TF != "Y") echo "closed";?>">
					<td><input type="checkbox" name="cl_no[]" value="<?=$CL_NO?>"/></td>
					<td><?=$INOUT_DATE?></td>
					<td><?=$INOUT_TYPE?></td>
					<td class="modeual_nm">
						<?=$NAME?>
					</td>
					<td class="price">
						<? if($QTY != 0) {?>
						<?=number_format($QTY)?>
						<? } ?>
					</td>
					<td class="price"><?=number_format($UNIT_PRICE)?></td>
					<td class="price row_deposit"><?=number_format($DEPOSIT)?></td>
					<td class="price row_withdraw"><?=number_format($WITHDRAW)?></td>
					<td class="price row_surtax"><?=number_format($SURTAX)?></td>
					<td class="memo_trigger">
						<?=number_format($susu_rate, 5)?>
					</td>
				</tr>
				<? 
						}
					
						$sum_susu_rate = round($SUM_SURTAX * 100.0 / $SUM_UNIT_PRICE, 5);

				?>
				<tr height="30" style="background-color:#dfdfdf;">
					<td></td>
					<td></td>
					<td></td>
					<td class="modeual_nm">
						합계 : 
					</td>
					<td class="price">
						<?=$SUM_QTY?>
					</td>
					<td class="price"><?=number_format($SUM_UNIT_PRICE)?></td>
					<td class="price row_deposit"><?=number_format($SUM_DEPOSIT)?></td>
					<td class="price row_withdraw"><?=number_format($SUM_WITHDRAW)?></td>
					<td class="price row_surtax"><?=number_format($SUM_SURTAX)?></td>
					<td class="memo_trigger">
						<b><?=$sum_susu_rate?></b>
					</td>
				</tr>
				<?
					}
				?>
			</table>
			<div class="sp20"></div>
			* 대체 (입금 처리)
			<table cellpadding="0" cellspacing="0" class="rowstable" border="0">
				<colgroup>
					<col width="3%" />
					<col width="10%" />
					<col width="5%" />
					<col width="*"/>
					<col width="5%" />
					<col width="10%" />
					<col width="10%" />
					<col width="10%" />
					<col width="10%" />
					<col width="10%" />
				</colgroup>
				<thead>
				<tr>
					<th></th>
					<th>날짜</th>
					<th>구분</th>
					<th>상품명</th>
					<th>수량</th>
					<th>단가</th>
					<th>매출/지급액</th>
					<th>매입/입금액</th>
					<th>부가세</th>
					<th>비고</th>
				</tr>
				<?
					$has_row = false;
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							//CL_NO, CP_NO, INOUT_DATE, INOUT_TYPE, NAME, QTY, UNIT_PRICE, WITHDRAW, DEPOSIT, RESERVE_NO

							$CL_NO						= trim($arr_rs[$j]["CL_NO"]);
							$INOUT_DATE					= trim($arr_rs[$j]["INOUT_DATE"]);
							$INOUT_TYPE					= trim($arr_rs[$j]["INOUT_TYPE"]);
							$GOODS_NO					= trim($arr_rs[$j]["GOODS_NO"]);
							$NAME						= trim($arr_rs[$j]["NAME"]);
							$QTY						= trim($arr_rs[$j]["QTY"]);
							$UNIT_PRICE					= trim($arr_rs[$j]["UNIT_PRICE"]);
							$WITHDRAW					= trim($arr_rs[$j]["WITHDRAW"]);
							$DEPOSIT					= trim($arr_rs[$j]["DEPOSIT"]);
							$SURTAX						= trim($arr_rs[$j]["SURTAX"]);
							$CATE_01					= trim($arr_rs[$j]["CATE_01"]);
							$TAX_TF						= trim($arr_rs[$j]["TAX_TF"]);
							$MEMO						= trim($arr_rs[$j]["MEMO"]);
							$RESERVE_NO					= trim($arr_rs[$j]["RESERVE_NO"]);
							$ORDER_GOODS_NO				= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
							$RGN_NO						= trim($arr_rs[$j]["RGN_NO"]);
							$TO_CP_NO					= trim($arr_rs[$j]["TO_CP_NO"]);
							$INPUT_TYPE					= trim($arr_rs[$j]["INPUT_TYPE"]);

							$USE_TF						= trim($arr_rs[$j]["USE_TF"]);

							$TAX_CONFIRM_TF				= trim($arr_rs[$j]["TAX_CONFIRM_TF"]);
							$TAX_CONFIRM_DATE			= trim($arr_rs[$j]["TAX_CONFIRM_DATE"]);

							//$CF_CODE					= trim($arr_rs[$j]["CF_CODE"]);

							if($INOUT_TYPE != "대체") continue;

							$INOUT_DATE = date("Y-m-d",strtotime($INOUT_DATE));

							if($USE_TF == "Y") { 
								$BALANCE = $BALANCE + $DEPOSIT - $WITHDRAW;
								$has_row = true;
							} else { 
								$QTY = 0;
								$WITHDRAW = 0;
								$DEPOSIT = 0;
								$SURTAX = 0;
							}

							?>
				<tr height="30" class="<?if($USE_TF != "Y") echo "closed";?>">
					<td></td>
					<td><?=$INOUT_DATE?></td>
					<td><?=$INOUT_TYPE?></td>
					<td class="modeual_nm">
						<?=$NAME?>
					</td>
					<td class="price">
						<? if($QTY != 0) {?>
						<?=number_format($QTY)?>
						<? } ?>
					</td>
					<td class="price"><?=number_format($UNIT_PRICE)?></td>
					<td class="price row_deposit" ><?=number_format($DEPOSIT)?></td>
					<td class="price row_withdraw" ><?=number_format($WITHDRAW)?></td>
					<td class="price row_surtax" ><?=number_format($SURTAX)?></td>
					<td class="memo_trigger" >
						<?=$MEMO?>
					</td>
				</tr>
				<? 

						$calc_susu_rate = round(($SUM_UNIT_PRICE - $WITHDRAW) * 100.0 / $SUM_UNIT_PRICE, 5);

						
				?>	
				<tr height="30" style="background-color:#dfdfdf;">
					<td></td>
					<td></td>
					<td></td>
					<td class="modeual_nm">
						수수율 계산 : 
					</td>
					<td class="price">
					</td>
					<td class="price"><?=number_format($SUM_UNIT_PRICE)?></td>
					<td class="price row_deposit"></td>
					<td class="price row_withdraw"><?=number_format($WITHDRAW)?></td>
					<td class="price row_surtax"><?=number_format($SUM_UNIT_PRICE - $WITHDRAW)?></td>
					<td class="memo_trigger">
						<b><?=$calc_susu_rate?></b>
					</td>
				</tr>
				<?
						}
					}

				?>
			</table>
			<div class="sp10"></div>
			<div>
				<? if($has_row) { ?>
				<input type="button" onclick="javascript:js_recalc_ledger('<?=$calc_susu_rate?>')" value=" 선택한 기장을 <?=$calc_susu_rate?>% 로 재 계산 "/>
				<? } else { ?>
				<span style="color:red;">원장에서 대입에 대응하는 대체 기장도 선택해주세요. </span>
				<? } ?>
			</div>
			<!--
			<div class="btn">
				<? if ($sPageRight_I == "Y") {?>
					<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
				<? } ?>
			</div>
			-->

			<?
				$arr_rs = listCompanyLedgerByGroupNos($conn, $arr_group_no, $chk_no);
			?>
			<div class="sp20"></div>
			* 대입 (카드 계산에 대한 원장확인)
			<table cellpadding="0" cellspacing="0" class="rowstable" border="0">
				<colgroup>
					<col width="3%" />
					<col width="10%" />
					<col width="5%" />
					<col width="*"/>
					<col width="5%" />
					<col width="10%" />
					<col width="10%" />
					<col width="10%" />
					<col width="10%" />
					<col width="10%" />
				</colgroup>
				<thead>
				<tr>
					<th></th>
					<th>날짜</th>
					<th>구분</th>
					<th>상품명</th>
					<th>수량</th>
					<th>단가</th>
					<th>매출/지급액</th>
					<th>매입/입금액</th>
					<th>부가세</th>
					<th>비고</th>
				</tr>
				<?

					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							//CL_NO, CP_NO, INOUT_DATE, INOUT_TYPE, NAME, QTY, UNIT_PRICE, WITHDRAW, DEPOSIT, RESERVE_NO

							$CL_NO						= trim($arr_rs[$j]["CL_NO"]);
							$INOUT_DATE					= trim($arr_rs[$j]["INOUT_DATE"]);
							$INOUT_TYPE					= trim($arr_rs[$j]["INOUT_TYPE"]);
							$GOODS_NO					= trim($arr_rs[$j]["GOODS_NO"]);
							$NAME						= trim($arr_rs[$j]["NAME"]);
							$QTY						= trim($arr_rs[$j]["QTY"]);
							$UNIT_PRICE					= trim($arr_rs[$j]["UNIT_PRICE"]);
							$WITHDRAW					= trim($arr_rs[$j]["WITHDRAW"]);
							$DEPOSIT					= trim($arr_rs[$j]["DEPOSIT"]);
							$SURTAX						= trim($arr_rs[$j]["SURTAX"]);
							$CATE_01					= trim($arr_rs[$j]["CATE_01"]);
							$TAX_TF						= trim($arr_rs[$j]["TAX_TF"]);
							$MEMO						= trim($arr_rs[$j]["MEMO"]);
							$RESERVE_NO					= trim($arr_rs[$j]["RESERVE_NO"]);
							$ORDER_GOODS_NO				= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
							$RGN_NO						= trim($arr_rs[$j]["RGN_NO"]);
							$TO_CP_NO					= trim($arr_rs[$j]["TO_CP_NO"]);
							$INPUT_TYPE					= trim($arr_rs[$j]["INPUT_TYPE"]);

							$USE_TF						= trim($arr_rs[$j]["USE_TF"]);

							$TAX_CONFIRM_TF				= trim($arr_rs[$j]["TAX_CONFIRM_TF"]);
							$TAX_CONFIRM_DATE			= trim($arr_rs[$j]["TAX_CONFIRM_DATE"]);

							//$CF_CODE					= trim($arr_rs[$j]["CF_CODE"]);

							$INOUT_DATE = date("Y-m-d",strtotime($INOUT_DATE));

							if($USE_TF == "Y")
								$BALANCE = $BALANCE + $DEPOSIT - $WITHDRAW;
							else { 
								$QTY = 0;
								$WITHDRAW = 0;
								$DEPOSIT = 0;
								$SURTAX = 0;
							}

							?>
				<tr height="30" class="<?if($USE_TF != "Y") echo "closed";?>">
					<td></td>
					<td><?=$INOUT_DATE?></td>
					<td><?=$INOUT_TYPE?></td>
					<td class="modeual_nm">
						<?=$NAME?>
					</td>
					<td class="price">
						<? if($QTY != 0) {?>
						<?=number_format($QTY)?>
						<? } ?>
					</td>
					<td class="price"><?=number_format($UNIT_PRICE)?></td>
					<td class="price row_deposit" data-value="<?=$DEPOSIT?>"><?=number_format($DEPOSIT)?></td>
					<td class="price row_withdraw" data-value="<?=$WITHDRAW?>"><?=number_format($WITHDRAW)?></td>
					<td class="price row_surtax" data-value="<?=$SURTAX?>"><?=number_format($SURTAX)?></td>
					<td class="memo_trigger" data-cl_no="<?=$CL_NO?>">
						<?if($CATE_01 <> "") echo "[".$CATE_01."] "?>
						<?=$MEMO?>
					</td>
				</tr>
				<? 
						}
					}
				?>
			</table>
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