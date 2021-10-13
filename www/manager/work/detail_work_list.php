<?session_start();?>
<?
# =============================================================================
# File Name    : work_list.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2015-09-18
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

	$con_order_type = "";


	$menu_right = "WO004"; // 메뉴마다 셋팅 해 주어야 합니다


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
	require "../../_classes/com/util/ImgUtil.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/work/work.php";
	require "../../_classes/biz/stock/stock.php";
	
	if ($s_adm_cp_type == "구매" || $s_adm_cp_type == "판매공급" ) { 
		$cp_type2 = $s_adm_com_code;
	}

	if ($s_adm_cp_type == "판매") { 
		$cp_type = $s_adm_com_code;
	}

	if ($mode == "W") {

		$row_cnt		= count($chk_flag);
		$work_line	= trim($work_line);

		for($i=0; $i <= ($row_cnt - 1) ; $i++) {
			$result_up = updateWorksLine($conn, $chk_flag[$i], $work_line);
		}

	}

	if ($mode == "T") {

		$row_cnt = count($chk_flag);

		for($i=0; $i <= ($row_cnt - 1) ; $i++) {
			$result_upy = updateWorksFlagY($conn, $chk_flag[$i], null, $s_adm_no);
		}

	}

#====================================================================
# Request Parameter
#====================================================================

	if ($work_date == "") {
		$work_date = date("Y-m-d",strtotime("0 day"));
	} else {
		$work_date = trim($work_date);
	}


	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$con_cate_01 = trim($con_cate_01);
	$con_cate_02 = trim($con_cate_02);
	$con_cate_03 = trim($con_cate_03);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);

	$work_type_1		= trim($work_type_1);
	$work_type_2		= trim($work_type_2);
	$work_type_3		= trim($work_type_3);
	$work_type_4		= trim($work_type_4);

	$lineA		= trim($lineA);
	$lineB		= trim($lineB);
	$lineC		= trim($lineC);

	$submit_flag	= trim($submit_flag);
	/*
	echo "submit_flag : ".$submit_flag."<br>";

	echo "line A : ".$lineA."<br>";
	echo "line B : ".$lineB."<br>";
	echo "line C : ".$lineC."<br>";

	echo "work_type_1 : ".$work_type_1."<br>";
	echo "work_type_2 : ".$work_type_2."<br>";
	echo "work_type_3 : ".$work_type_3."<br>";
	echo "work_type_4 : ".$work_type_4."<br>";
	*/

	if ($submit_flag == "") {

		$work_type_1		= "Y";
		$work_type_2		= "Y";
		$work_type_3		= "Y";
		$work_type_4		= "Y";
		$work_type_5		= "Y";

		$lineA		= "Y";
		$lineB		= "Y";
		$lineC		= "Y";

	}

	$arr_work_type = $work_type_1."|".$work_type_2."|".$work_type_3."|".$work_type_4."|".$work_type_5;
	$arr_work_line = $lineA."|".$lineB."|".$lineC;

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
		$nPageSize = 2000;
	}

	$nPageBlock	= 10;
	
#	echo $start_date;
#	echo $end_date;

#===============================================================
# Get Search list count
#===============================================================

	/*
	$nListCnt =totalCntWorkDetailList($conn, $order_type, $work_date, $arr_work_type, $arr_work_line, "Y", "N", $search_field, $search_str);
	#echo $nListCnt;

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}
	*/

	$arr_rs = listWorkDetailList($conn, $order_type, $work_date, $arr_work_type, $arr_work_line, "Y", "N", $search_field, $search_str, $nPage, $nPageSize);

	
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

	function js_view(reserve_no) {

		var frm = document.frm;
		
		var url = "/manager/order/order_read.php?reserve_no="+reserve_no;

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

	function js_sub_goods_popup() {
		
		var url = "popup_work_goods.php?work_date=<?=$work_date?>";

		NewWindow(url,'popup_work_goods','820','700','YES');

	}

	function js_order_popup(order_goods_no) {

		var url = "popup_work.php?work_date=<?=$work_date?>&order_goods_no="+order_goods_no;

		NewWindow(url,'popup_work_goods','1600','1200','YES');

	}

	function js_work_line(line) {
		
		var frm = document.frm;
		
		frm.work_line.value = line;
		frm.mode.value = "W";
		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_work_finish() {

		var frm = document.frm;
		frm.mode.value = "T";
		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	function js_all_check() {
		var frm = document.frm;
		
		if (frm['chk_flag[]'] != null) {
			
			if (frm['chk_flag[]'].length != null) {

				if (frm.all_chk.checked == true) {
					for (i = 0; i < frm['chk_flag[]'].length; i++) {
						frm['chk_flag[]'][i].checked = true;
					}
				} else {
					for (i = 0; i < frm['chk_flag[]'].length; i++) {
						frm['chk_flag[]'][i].checked = false;
					}
				}
			} else {
			
				if (frm.all_chk.checked == true) {
					frm['chk_flag[]'].checked = true;
				} else {
					frm['chk_flag[]'].checked = false;
				}
			}
		}
	}

	function js_outcase(goods_no) {
		var url = "popup_outcase.php?goods_no="+goods_no;
		NewWindow(url,'popup_outcase','1024','400','YES');
	}

	function js_opt_memo_view(order_goods_no) {
		var url = "popup_opt_memo.php?order_goods_no="+order_goods_no;
		NewWindow(url,'popup_opt_memo','820','700','YES');
	}

</script>
</head>

<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="reserve_no" value="">
<input type="hidden" name="use_tf" value="">
<input type="hidden" name="submit_flag" value="Y">
<input type="hidden" name="mode" value="">
<input type="hidden" name="work_line" value="">
<input type="hidden" name="con_order_type" value="<?=$con_order_type?>">
<input type="hidden" name="nPage" value="<?=$nPage?>">
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
				<h2>공정별 작업 리스트 (<?=$work_date?>)</h2>

				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="10%" />
					<col width="37%" />
					<col width="10%" />
					<col width="37%" />
					<col width="6%" />
				</colgroup>
				<thead>
					<tr>
						<th>작업일</th>
						<td>
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="work_date" value="<?=$work_date?>" maxlength="10"/>
						</td>
						<th>작업구분</th>
						<td colspan="2">
							<label for="check99"><input type="checkbox" name="work_type_1" value="Y" id="check99" <? if ($work_type_1 == "Y") echo "checked"; ?>> 인박스</label>&nbsp;
							<label for="check98"><input type="checkbox" name="work_type_2" value="Y" id="check98" <? if ($work_type_2 == "Y") echo "checked"; ?>> 포장지</label>&nbsp;
							<label for="check97"><input type="checkbox" name="work_type_3" value="Y" id="check97" <? if ($work_type_3 == "Y") echo "checked"; ?>> 스티커</label>&nbsp;
							<label for="check96"><input type="checkbox" name="work_type_4" value="Y" id="check96" <? if ($work_type_4 == "Y") echo "checked"; ?>> 아웃박스</label>&nbsp;
							<label for="check95"><input type="checkbox" name="work_type_5" value="Y" id="check95" <? if ($work_type_5 == "Y") echo "checked"; ?>> 아웃박스 스티커</label>&nbsp;
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>작업라인</th>
						<td colspan="4">
						<label for="check100"><input type="checkbox" name="lineA" value="Y" id="check100" <? if ($lineA == "Y") echo "checked"; ?>> 작업 A</label>&nbsp;
						<label for="check101"><input type="checkbox" name="lineB" value="Y" id="check101" <? if ($lineB == "Y") echo "checked"; ?>> 작업 B</label>&nbsp;
						<label for="check102"><input type="checkbox" name="lineC" value="Y" id="check102" <? if ($lineC == "Y") echo "checked"; ?>> 작업 C</label>&nbsp;
						<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>

						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="button" name="aa" value=" 선택한 A 라인 작업으로 등록 " class="btntxt" onclick="js_work_line('A');">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="button" name="aa" value=" 선택한 B 라인 작업으로 등록 " class="btntxt" onclick="js_work_line('B');">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="button" name="aa" value=" 선택한 C 라인 작업으로 등록 " class="btntxt" onclick="js_work_line('C');">
						</td>
					</tr>
				</tbody>
			</table>

			<div class="btnright02">
				
				
				<input type="button" name="aa" value=" 선택한 작업 완료 " class="btntxt" onclick="js_work_finish();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="button" name="aa" value=" 금일 작업 자재 조회 " class="btntxt" onclick="js_sub_goods_popup();">&nbsp;&nbsp;&nbsp;
				
			</div>

			<b>총 <?=$nListCnt?> 건</b>
			<!--
			<br>
			<br>*  작업일이 오늘 날짜인 경우와 오늘 이전 날짜 중 작업 완료 아닌 건을 노출
			<br>*  각 주문별 작업 공정에 따라 구분
			-->
			<table cellpadding="0" cellspacing="0" class="rowstable" border="0">

				<colgroup>
					<col width="3%" />
					<col width="6%" />
					<!--<col width="6%" />-->
					<col width="23%" />
					<col width="23%" />
					<col width="6%" />
					<col width="18%" />
					<col width="6%"/>
					<col width="15%" />
				</colgroup>
				<thead>
					<tr>
						<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
						<th>작업라인</th>
						<!--<th>작업순번</th>-->
						<th>주문상품</th>
						<th>작업구분</th>
						<th>주문수량</th>
						<th>주문번호/업체</th>
						<th>영업담당</th>
						<th class="end">작업메모</th>
					</tr>
				</thead>

				<tbody>
				<?
					$nCnt = 0;
					
					$work_title		= "";
					$work_img			= "";
					$work_name		= "";

					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$RESERVE_NO						= trim($arr_rs[$j]["RESERVE_NO"]);
							$ORDER_GOODS_NO				= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
							$ORDER_DATE						= trim($arr_rs[$j]["ORDER_DATE"]);
							$OPT_OUTSTOCK_DATE		= trim($arr_rs[$j]["OPT_OUTSTOCK_DATE"]);
							$CP_NO								= trim($arr_rs[$j]["CP_NO"]);
							$O_MEM_NM							= trim($arr_rs[$j]["O_MEM_NM"]);
							$GOODS_NAME						= trim($arr_rs[$j]["GOODS_NAME"]);
							$GOODS_NO							= trim($arr_rs[$j]["GOODS_NO"]);
							$OPT_MANAGER_NO				= trim($arr_rs[$j]["OPT_MANAGER_NO"]);
							$OPT_MEMO							= trim($arr_rs[$j]["OPT_MEMO"]);
							$WORK_ORDER						= trim($arr_rs[$j]["WORK_ORDER"]);
							$WORK_DATE						= trim($arr_rs[$j]["WORK_DATE"]);
							$BULK_TF							= trim($arr_rs[$j]["BULK_TF"]);
							//$GOODS_IMG						= trim($arr_rs[$j]["GOODS_IMG"]);
							//$GOODS_PATH_IMG				= trim($arr_rs[$j]["GOODS_PATH_IMG"]);
							$QTY									= trim($arr_rs[$j]["QTY"]);
							$OPT_STICKER_NO				= trim($arr_rs[$j]["OPT_STICKER_NO"]);
							$OPT_OUTBOX_TF				= trim($arr_rs[$j]["OPT_OUTBOX_TF"]);
							$OPT_WRAP_NO					= trim($arr_rs[$j]["OPT_WRAP_NO"]);
							$OPT_PRINT_MSG				= trim($arr_rs[$j]["OPT_PRINT_MSG"]);
							$OPT_STICKER_MSG			= trim($arr_rs[$j]["OPT_STICKER_MSG"]);
							$WORK_NO							= trim($arr_rs[$j]["WORK_NO"]);
							$WORK_LINE						= trim($arr_rs[$j]["WORK_LINE"]);
							$WORK_TYPE						= trim($arr_rs[$j]["WORK_TYPE"]);
							$WORK_FLAG						= trim($arr_rs[$j]["WORK_FLAG"]);

							$refund_able_qty = getRefundAbleQty($conn, $RESERVE_NO, $ORDER_GOODS_NO);
							
							//전체취소건은 제외
							if($refund_able_qty == 0) 
								continue;

							$GOODS_IMG = getImage($conn, $GOODS_NO, "170", "170");

							if (trim($WORK_TYPE) == "INCASE") {
								$work_title	= "인박스";
								$arr_incase = getOrderGoodsSub($conn, $GOODS_NO, "INCASE");
								$work_name	= $arr_incase[0]["GOODS_NAME"];
								$work_img	= getImage($conn, $arr_incase[0]["GOODS_NO"], "", "");
							}

							if (trim($WORK_TYPE) == "WRAP") {
								$work_title	= "포장지";
								$work_img	= getImage($conn, $OPT_WRAP_NO, "", "");
							}

							if (trim($WORK_TYPE) == "STICKER") {
								$work_title	= "스티커";
								$work_img	= getImage($conn, $OPT_STICKER_NO, "", "");
							}

							if (trim($WORK_TYPE) == "OUTCASE") {
								$work_title	= "아웃박스";
								$arr_outcase = getOrderGoodsSub($conn, $GOODS_NO, "OUTCASE");
								$work_img	 = ""; //getImage($conn, $arr_outcase[0]["GOODS_NO"], "", "");
							}

							if (trim($WORK_TYPE) == "OUTSTICKER") {
								$work_title	= "아웃박스스티커";
								$work_img			= "";
							}

						?>
						<tr height="25" <? if (($j % 2) == 1) {?> style="background:#EFEFEF" <? } ?>>
							<td><input type="checkbox" name="chk_flag[]" value="<?=$WORK_NO?>"></td> 
							<td class="sort"><span><b><?=$WORK_LINE?><b></span></td>

							<td style="text-align:center;padding:5px 5px 5px 5px">
								<a href="javascript:js_order_popup('<?=$ORDER_GOODS_NO?>');"><?=$GOODS_NAME?><br>
								<img src="<?=$GOODS_IMG?>" width="170" height="170">
								</a>
								<?
									// 구성품 정보 가지고 오기 
									$arr_goods_sub =selectGoodsSub($conn, $GOODS_NO);
								?>
								<div style="width:100%;text-align:left; padding:10px 5px 5px 10px">
								<?
									if (sizeof($arr_goods_sub) > 0) {
										for ($jk = 0 ; $jk < sizeof($arr_goods_sub); $jk++) {
											$sub_goods_name			= trim($arr_goods_sub[$jk]["GOODS_NAME"]);
											$sub_goods_cnt			= trim($arr_goods_sub[$jk]["GOODS_CNT"]);
											echo $sub_goods_name."&nbsp;&nbsp;<font color='red'>(<b>".$sub_goods_cnt."</b>)</font><br>";
										}
									}
								?>
								</div>
							</td>

							<td style="text-align:center;padding:5px 5px 5px 5px">
								<b><font color="red"><?=$work_title	?></font></b><br>
								<? if ($work_img) { ?>
								<?	if (trim($WORK_TYPE) == "STICKER") { ?>
								<img src="<?=$work_img?>" width="250">
								<?	} else if (trim($WORK_TYPE) == "OUTCASE") {?>
								<img src="<?=$work_img?>" width="170"><br><br>
								<b><a href="javascript:js_outcase('<?=$GOODS_NO?>');">상세보기</a></b>
								<?	} else { ?>
								<img src="<?=$work_img?>" width="170">
								<?	}?>
								<? } else { ?>
								<?	if (trim($WORK_TYPE) == "OUTCASE") {?>
								<b><font color="#AFAFAF">이미지 미등록</font></b>
								<?	} else { ?>
								<b><font color="navy">있음</font></b>
								<? } ?>
								<? } ?>
								
								<? if (trim($WORK_TYPE) == "INCASE") { ?>
									<br><br><b><?=$work_name?></b>
								<? } ?>
								

								<? if (trim($WORK_TYPE) == "STICKER") { ?>
									<br><br><b><?=$OPT_STICKER_MSG?></b>
								<? } ?>

							</td>
							<td><b><?=number_format($QTY)?></b></td>
							<td style="text-align:center">
								<a href="javascript:js_view('<?=$RESERVE_NO?>');"><?=$RESERVE_NO?></a><br>
								<?= getCompanyName($conn, $CP_NO);?>
							</td>
							<td style="text-align:center"><?=getAdminName($conn,$OPT_MANAGER_NO);?></td>
							<td class="modeual_nm" style="padding:5px 2px 2px 5px;"><a href="javascript:js_opt_memo_view('<?=$ORDER_GOODS_NO?>');"><?=$OPT_MEMO?></a></td>
						</tr>
						<?
						} 

					}else{
						?>
						<tr class="order">
							<td height="50" align="center" colspan="16">데이터가 없습니다. </td>
						</tr>
					<?
						}
					?>
				</tbody>
			</table>

				<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">
					<input type="button" name="aa" value=" 선택한 작업 완료 " class="btntxt" onclick="js_work_finish();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="button" name="aa" value=" 금일 작업 자재 조회 " class="btntxt" onclick="js_sub_goods_popup();">&nbsp;&nbsp;&nbsp;
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
							$strParam = $strParam."&sel_order_state=".$sel_order_state."&cp_type=".$cp_type."&cp_type2=".$cp_type2."&sel_pay_type=".$sel_pay_type."&con_order_type=".$con_order_type;
							$strParam = $strParam."&order_field=".$order_field."&order_str=".$order_str;

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