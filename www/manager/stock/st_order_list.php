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
	$menu_right = "SG003"; // �޴����� ���� �� �־�� �մϴ�

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

	if ($s_adm_cp_type == "����") { 
		$cp_type2 = $s_adm_com_code;
	}

	//echo $s_adm_cp_type;
	//echo $s_adm_com_code;

	if ($mode == "T") {
		
		#echo $chg_order_state;
		
		$reserve_no_cnt = count($chk_reserve_no);

		for($i=0; $i <= ($reserve_no_cnt - 1) ; $i++) {
			$result = updateStOrderState($conn, $chk_reserve_no[$i], $chg_order_state);
		}

		//updateBoardUseTF($conn, $use_tf, $s_adm_no, $bb_code, $bb_no);
	}


	if ($mode == "D") {
		$row_cnt = count($chk_no);

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_order_goods_no = $chk_no[$k];
			
			$result = deleteStOrder($conn, $str_order_goods_no, $s_adm_no);
		
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

	if ($order_field == "")
		$order_field = "REG_DATE";

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

	$nListCnt =totalCntStOrder($conn, $start_date, $end_date, $sel_order_state, $cp_type2, $con_use_tf, $del_tf, $search_field, $search_str);
	
	#echo $nListCnt;

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listStOrder($conn, $start_date, $end_date, $sel_order_state, $cp_type2, $con_use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize);


	$arr_rs_all = listAllStOrder($conn, $start_date, $end_date, $sel_order_state, $cp_type2, $con_use_tf, $del_tf, $search_field, $search_str);


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

	function js_write() {

		var frm = document.frm;
		
		frm.target = "";
		frm.method = "get";
		frm.action = "st_order_write.php";
		frm.submit();
		
	}

	function js_view(rn, order_goods_no) {

		var frm = document.frm;
		
		frm.order_goods_no.value = order_goods_no;

		frm.mode.value = "S";
		frm.target = "";
		frm.method = "get";
		frm.action = "st_order_write.php";
		frm.submit();
	}

	// ��ȸ ��ư Ŭ�� �� 
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
		alert("������ �ֹ��� �����ϴ�.");
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
		alert("���� ������ �ֹ��� ������ �ּ���");
		return;
	}

	bDelOK = confirm('�ֹ� ���¸� ���� �Ͻðڽ��ϱ�?');
		
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

		bDelOK = confirm('������ �����Ͻðڽ��ϱ�?');
		
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

</script>
</head>

<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="rn" value="">
<input type="hidden" name="order_goods_no" value="">
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

				<h2>�԰� ����</h2>
				<div class="btnright">
					<? if ($s_adm_cp_type == "�") { ?>
					<a href="javascript:js_write();"><img src="../images/admin/btn_regist_02.gif" alt="���" /></a>
					<? } else { ?>
					&nbsp;
					<? } ?>
				</div>
				<div class="category_choice"><!--<select style="width: 100px;"><option>��ǰ����</option></select>-->&nbsp;</div>

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
						<th>�԰��� :</th>
						<td colspan="4">
							<input type="text" class="txt" style="width: 75px;" name="start_date" value="<?=$start_date?>" maxlength="10" readonly="1" />
							<a href="javascript:show_calendar('document.frm.start_date', document.frm.start_date.value);" onFocus="blur();"><!--
						--><img src="/manager/images/bu/ic_calendar.gif" alt="" /></a>  ~ 

							<input type="text" class="txt" style="width: 75px;" name="end_date" value="<?=$end_date?>" maxlength="10" readonly="1" />
							<a href="javascript:show_calendar('document.frm.end_date', document.frm.end_date.value);" onFocus="blur();"><!--
						--><img src="/manager/images/bu/ic_calendar.gif" alt="" /></a>
						</td>
						<!--
						<th>�԰���� :</th>
						<td colspan="2">
							<?= makeSelectBox($conn,"ORDER_STATE","sel_order_state","125","����","",$sel_order_state)?>
						</td>
						-->
					</tr>
				</thead>
				<tbody>
					<? if ($s_adm_cp_type == "�") { ?>
					<tr>
						<th>���޾�ü :</th>
						<td colspan="4">
							<?= makeCompanySelectBoxAsCpNo($conn, '����', $cp_type2);?>
						</td>					
					</tr>
					<? } else { ?>
					<input type="hidden" name="cp_type" value = "">
					<input type="hidden" name="sel_pay_type" value = "">
					<? } ?>
					<tr>
						<th>���� :</th>
						<td>
							<select name="order_field" style="width:84px;">
								<option value="ORDER_DATE" <? if ($order_field == "ORDER_DATE") echo "selected"; ?> >�԰��Ͻ�</option>
								<option value="PAY_DATE" <? if ($order_field == "PAY_DATE") echo "selected"; ?> >������</option>
								<option value="REG_DATE" <? if ($order_field == "REG_DATE") echo "selected"; ?> >�����</option>
							</select>&nbsp;&nbsp;
							<input type='radio' class="" name='order_str' value='DESC' <? if (($order_str == "DESC") || ($order_str == "")) echo " checked"; ?> > �������� &nbsp;
							<input type='radio' name='order_str' value='ASC' <? if ($order_str == "ASC") echo " checked"; ?>> ��������
						</td>

						<th>�˻����� :</th>
						<td>
							<select name="nPageSize" style="width:84px;">
								<option value="20" <? if ($nPageSize == "20") echo "selected"; ?> >20����</option>
								<option value="50" <? if ($nPageSize == "50") echo "selected"; ?> >50����</option>
								<option value="100" <? if ($nPageSize == "100") echo "selected"; ?> >100����</option>
								<option value="200" <? if ($nPageSize == "200") echo "selected"; ?> >200����</option>
								<option value="300" <? if ($nPageSize == "300") echo "selected"; ?> >300����</option>
								<option value="400" <? if ($nPageSize == "400") echo "selected"; ?> >400����</option>
								<option value="500" <? if ($nPageSize == "500") echo "selected"; ?> >500����</option>
							</select>&nbsp;
							<select name="search_field" style="width:84px;">
								<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >���հ˻�</option>
								<option value="GOODS_NAME" <? if ($search_field == "GOODS_NAME") echo "selected"; ?> >��ǰ��</option>
							</select>&nbsp;

							<input type="text" value="<?=$search_str?>" name="search_str" class="txt" />
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						</td>
						<td align="right">
							<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="���� ����Ʈ" /></a>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="sp20"></div>
			<b>�� <?=$nListCnt?> ��</b>
			<table cellpadding="0" cellspacing="0" class="rowstable" border="0">
				<colgroup>
					<col width="2%" />
					<col width="8%" />
					<col width="15%" />
					<col width="26%" />
					<col width="12%"/>
					<col width="7%" />
					<col width="6%" />
					<col width="8%" />
					<col width="8%" />
					<col width="8%" />
				</colgroup>
				<thead>
					<tr>
						<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
						<th>�԰���</th>
						<th>��ü��</th>
						<th>��ǰ��</th>
						<th>�ɼ�</th>
						<th>���԰�</th>
						<th>����</th>
						<th>�հ�</th>
						<th>�����</th>
						<th class="end">������</th>
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

							$ORDER_DATE						= trim($arr_rs[$j]["ORDER_DATE"]);
							$REG_DATE						= trim($arr_rs[$j]["REG_DATE"]);
							
							$ORDER_DATE		= date("Y-m-d",strtotime($ORDER_DATE));
							$REG_DATE			= date("Y-m-d",strtotime($REG_DATE));
							
				?>
					<tr height="37">
						<td class="order"><input type="checkbox" name="chk_no[]" value="<?=$ORDER_GOODS_NO?>"></td>
						<td ><?=$ORDER_DATE?></a></td>
						<td class="modeual_nm"><?= getCompanyName($conn, $BUY_CP_NO);?></td>
						<td class="modeual_nm"><a href="javascript:js_view('<?=$rn?>','<?=$ORDER_GOODS_NO?>');"><?= $GOODS_NAME?></a></td>
						<td class="modeual_nm"><?= $option_str?></td>
						<td class="price"><?=number_format($BUY_PRICE)?></td>
						<td class="price"><?=number_format($QTY)?></td>
						<td class="price"><?=number_format($BUY_PRICE * $QTY)?></td>
						<td><?=$REG_DATE?></td>
						<td><?=$PAY_DATE?></td>
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

					<!-- �հ� -->
						<tr class="goods_end">
							<td colspan="10">
								&nbsp;
							</td>
						</tr>
						<tr class="goods_end" height="35">
							<td class="filedown" colspan="2">�� ��</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td class="modeual_nm" colspan="1" ></td>
							<td class="price"><?=number_format($ALL_QTY)?></td>
							<td class="price"><?=number_format($ALL_BUY_PRICE)?></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>

					<?

					}else{
						?>
						<tr class="order">
							<td height="50" align="center" colspan="9">�����Ͱ� �����ϴ�. </td>
						</tr>
					<?
						}
					?>
				</tbody>
			</table>

				<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">
				<? if (($sPageRight_D == "Y") && ($s_adm_cp_type == "�")) {?>
					<input type="button" name="aa" value=" ������ �԰� ���� " class="btntxt" onclick="js_delete();"> 
				<? } ?>
				</div>

					<!-- --------------------- ������ ó�� ȭ�� START -------------------------->
					<?
						# ==========================================================================
						#  ����¡ ó��
						# ==========================================================================
						if (sizeof($arr_rs) > 0) {
							#$search_field		= trim($search_field);
							#$search_str			= trim($search_str);
							//$sel_order_state, $cp_type, $cp_type2, $sel_pay_type, $con_use_tf,
							$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&start_date=".$start_date."&end_date=".$end_date;
							$strParam = $strParam."&sel_order_state=".$sel_order_state."&cp_type=".$cp_type."&cp_type2=".$cp_type2."&sel_pay_type=".$sel_pay_type;
							$strParam = $strParam."&order_field=".$order_field."&order_str=".$order_str;

					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- ������ ó�� ȭ�� END -------------------------->
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