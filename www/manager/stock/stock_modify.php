<?session_start();?>
<?
# =============================================================================
# File Name    : stock_modify.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2015-07-14
# Modify Date  : 
#	Copyright    : Copyright @C&C Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "SG007"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/company/company.php";
	require "../../_classes/biz/stock/stock.php";
#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);
	$goods_no = trim($goods_no);

	$total_qty = trim($total_qty);
	$fix_qty = trim($fix_qty);
	$org_qty = trim($org_qty);
	$fix_bqty = trim($fix_bqty);
	$org_bqty = trim($org_bqty);

	if ($mode == "U") {
		// 재고 정정 입니다.
		$result = fixGoodsStock($conn, $goods_no, $fix_qty, $org_qty, $fix_bqty, $org_bqty, $s_adm_no);
	}


	$arr_goods_stock = getGoodsStock($conn, $goods_no);

	$rs_goods_no			= trim($arr_goods_stock[0]["GOODS_NO"]);
	$rs_goods_name		= trim($arr_goods_stock[0]["GOODS_NAME"]);
	$rs_goods_code		= trim($arr_goods_stock[0]["GOODS_CODE"]);
	$S_IN_QTY					= trim($arr_goods_stock[0]["S_IN_QTY"]);
	$S_IN_BQTY				= trim($arr_goods_stock[0]["S_IN_BQTY"]);
	$S_OUT_QTY				= trim($arr_goods_stock[0]["S_OUT_QTY"]);
	$S_OUT_BQTY				= trim($arr_goods_stock[0]["S_OUT_BQTY"]);

	$CAL_QTY = $S_IN_QTY - $S_OUT_QTY;
	$CAL_BQTY = $S_IN_BQTY - $S_OUT_BQTY;
	
	$TOTAL_QTY = $CAL_QTY + $CAL_BQTY; 

#====================================================================
# DML Process
#====================================================================

	if ($result) {
		if ($mode == "U") {
?>	
<script language="javascript">
	alert('정상 처리 되었습니다.');
	opener.js_reload();
	self.close();
	//location.href =  "company_modify.php<?=$strParam?>&mode=S&temp_no=<?=$temp_no?>&cp_no=<?=$cp_no?>";
</script>
<?
		} 
		
		mysql_close($conn);
		exit;
	}	
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script>
	function js_save() {
		
		var frm = document.frm;
		
		if (frm.fix_qty.value.trim() == "") {
			alert("정정정상재고수량을 입력하세요.");
			return;
		}

		if (frm.fix_bqty.value.trim() == "") {
			alert("정정불량재고수량을 입력하세요.");
			return;
		}

		frm.mode.value = "U";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	function js_modify_qty() {
		var frm = document.frm;
		
		if ((parseInt(frm.fix_qty.value) > parseInt(frm.total_qty.value)) || (parseInt(frm.fix_qty.value) < 0)) {
			alert("입력값이 총재고 수량 보다 크거나 음수 일수 없습니다.");
			frm.fix_qty.value = frm.org_qty.value;
			frm.fix_bqty.value = frm.org_bqty.value;
		} else {
			frm.fix_bqty.value = frm.total_qty.value - frm.fix_qty.value;
		}
	}

	function js_modify_bqty() {
		var frm = document.frm;
		if ((parseInt(frm.fix_bqty.value) > parseInt(frm.total_qty.value)) || (parseInt(frm.fix_bqty.value) < 0)) {
			alert("입력값이 총재고 수량 보다 크거나 음수 일수 없습니다.");
			frm.fix_qty.value = frm.org_qty.value;
			frm.fix_bqty.value = frm.org_bqty.value;
		} else {
			frm.fix_qty.value = frm.total_qty.value - frm.fix_bqty.value;
		}
	}

</script>
</head>
<body id="popup_file">

<form name="frm" method="post" >
<input type="hidden" name="mode" value="" />
<input type="hidden" name="goods_no" value="<?= $goods_no?>">
<input type="hidden" name="total_qty" value="<?= $TOTAL_QTY?>">
<div id="popupwrap_file">
	<h1>재고 정정</h1>
	<div id="postsch">
		<h2>* 재고 수량을 정정 합니다.</h2>
		<div class="addr_inp">
			<table cellpadding="0" cellspacing="0" class="colstable02">
				<colgroup>
					<col width="15%" />
					<col width="35%" />
					<col width="15%" />
					<col width="35%" />
				</colgroup>
				<tr>
					<th>상품명</th>
					<td colspan="3" style="position:relative" class="line">
						<?=$rs_goods_name?>
					</td>
				</tr>
				<tr>
					<th>상품코드</th>
					<td colspan="3" style="position:relative" class="line">
						<?=$rs_goods_code?>
					</td>
				</tr>
				<tr>
					<th>총재고수량</th>
					<td colspan="3" style="position:relative" class="line">
						<b><?=number_format($TOTAL_QTY)?> 개</b>
					</td>
				</tr>
				<tr>
					<th>정상재고</th>
					<td style="position:relative" class="line">
						<?=number_format($CAL_QTY)?> 개
					</td>
					<th>정정정상재고수량</th>
					<td style="position:relative" class="line">
						<input type="text" value="<?=$CAL_QTY?>" name="fix_qty" onkeyup="return isNumber(this)" onChange="js_modify_qty();" >
						<input type="hidden" value="<?=$CAL_QTY?>" name="org_qty"> 개
					</td>
				</tr>
				<tr>
					<th>불량재고</th>
					<td style="position:relative" class="line">
						<?=number_format($CAL_BQTY)?> 개
					</td>
					<th>정정불량재고수량</th>
					<td style="position:relative" class="line">
						<input type="text" value="<?=$CAL_BQTY?>" name="fix_bqty" onkeyup="return isNumber(this)" onChange="js_modify_bqty();">
						<input type="hidden" value="<?=$CAL_BQTY?>" name="org_bqty"> 개
					</td>
				</tr>
			</table>
		</div>

		<div class="btn">
		<? if ($adm_no <> "" ) {?>
			<? if ($sPageRight_U == "Y") {?>
      <a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
			<? } ?>
		<? } else {?>
			<? if ($sPageRight_I == "Y") {?>
      <a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
			<? } ?>
		<? }?>
		</div>

	</div>
	<br />
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