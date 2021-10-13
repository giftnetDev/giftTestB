<?session_start();?>
<?
# =============================================================================
# File Name    : sale_confirm_order_detail_list.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2011.01.13
# Modify Date  : 
#	Copyright : Copyright @orion. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "CF004"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/order/order.php";

#====================================================================
# DML Process
#====================================================================

#====================================================================
# Request Parameter
#====================================================================

	$use_tf = "Y";
	$del_tf = "N";
#============================================================
# Page process
#============================================================

#===============================================================
# Get Search list count
#===============================================================
	//echo $p_confirm_ymd;
	//echo $p_buy_cp_no;

	$arr_rs = listSaleConfirmCpOrderList($conn, $p_confirm_ymd, $p_cp_no, $use_tf, $del_tf, $search_field, $search_str);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/calendar.js"></script>
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->

<script type="text/javascript">
	// 조회 버튼 클릭 시 
	function js_search() {
		var frm = document.frm;
		
		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_excel() {

		var frm = document.frm;
		
		frm.target = "";
		frm.action = "<?=str_replace("list","excel_list",$_SERVER[PHP_SELF])?>";
		frm.submit();

	}

</script>

</head>
<body id="popup_stock">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="" />
<input type="hidden" name="mode" value="MULTI" />
<input type="hidden" name="goods_no" value="" />
<input type="hidden" name="idx" value="" />
<input type="hidden" name="p_confirm_ymd" value="<?=$p_confirm_ymd?>" />
<input type="hidden" name="p_cp_no" value="<?=$p_cp_no?>" />


<div id="popupwrap_stock">
	<h1>판매 업체 정산 상세 조회</h1>
	<div id="postsch_code">
		<h2>* 판매 업체 정산 항목을 조회 합니다.</h2>
		<table cellpadding="0" cellspacing="0" width="100%" height="586" border="0">
			<tr>
				<td valign="top">
					<table cellpadding="0" cellspacing="0" class="colstable" style="width:98%">
					<colgroup>
						<col width="120" />
						<col width="*" />
						<col width="120" />
					</colgroup>
					<tbody>
						<tr>
							<th>검색조건 :</th>
							<td>
								<select name="search_field" style="width:84px;">
									<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >통합검색</option>
									<option value="G.RESERVE_NO" <? if ($search_field == "G.RESERVE_NO") echo "selected"; ?> >주문번호</option>
									<option value="O.O_MEM_NM" <? if ($search_field == "O.O_MEM_NM") echo "selected"; ?> >주문자명</option>
									<option value="O.R_MEM_NM" <? if ($search_field == "O.R_MEM_NM") echo "selected"; ?> >수령자명</option>
									<option value="G.GOODS_NAME" <? if ($search_field == "G.GOODS_NAME") echo "selected"; ?> >상품명</option>
								</select>&nbsp;
								<input type="text" value="<?=$search_str?>" name="search_str" style="height: 16px; border: 1px solid #c0bfbf;" />
								<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
							</td>
							<td align="right">
								<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>
							</td>
						</tr>
					</tbody>
					</table>
					<div class="sp20"></div>
					<b>총 <?=sizeof($arr_rs)?> 건</b>

					<table cellpadding="0" cellspacing="0" class="rowstable" style="width:98%">

					<colgroup>
						<col width="10%" />
						<col width="7%" />
						<col width="7%" />
						<col width="20%" />
						<col width="6%" />
						<col width="6%" />
						<col width="7%" />
						<col width="6%" />
						<col width="9%" /><!-- 합계-->
						<col width="6%" /><!-- 3자물류-->
						<col width="8%" /><!-- 주문상태-->
						<col width="8%" /><!-- 완료일시-->
					</colgroup>
					<thead>

						<tr>
							<th>주문번호</th>
							<th>주문자명</th>
							<th>수령자명</th>
							<th>상품명</th>
							<th>판매가</th>
							<th>배송비</th>
							<th>추가배송비</th>
							<th>수량</th>
							<th>합계</th>
							<th>3자물류</th>
							<th>주문상태</th>
							<th class="end">완료일시</th>
						</tr>
					</thead>
				<?
					$nCnt = 0;

					$SUM_PRICE = 0;
					$TOT_SUM_PRICE = 0;
					$TOT_QTY	= 0;
					
					if (sizeof($arr_rs) > 0) {
						
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$RESERVE_NO					= trim($arr_rs[$j]["RESERVE_NO"]);
							$O_MEM_NM						= trim($arr_rs[$j]["O_MEM_NM"]);
							$R_MEM_NM						= trim($arr_rs[$j]["R_MEM_NM"]);
							
							$GOODS_NAME					= trim($arr_rs[$j]["GOODS_NAME"]);
							$GOODS_OPTION_NAME	= trim($arr_rs[$j]["GOODS_OPTION_NAME"]);
							
							$SALE_PRICE					= trim($arr_rs[$j]["SALE_PRICE"]);
							$BUY_PRICE					= trim($arr_rs[$j]["BUY_PRICE"]);
							$EXTRA_PRICE				= trim($arr_rs[$j]["EXTRA_PRICE"]);
							$DELIVERY_PRICE			= trim($arr_rs[$j]["DELIVERY_PRICE"]);
							$QTY								= trim($arr_rs[$j]["QTY"]);
							$SA_DELIVERY_PRICE	= trim($arr_rs[$j]["SA_DELIVERY_PRICE"]);
							$ORDER_STATE				= trim($arr_rs[$j]["ORDER_STATE"]);
							$FINISH_DATE				= trim($arr_rs[$j]["FINISH_DATE"]);

							$FINISH_DATE = date("Y-m-d",strtotime($FINISH_DATE));

							//$SUM_PRICE = ($SALE_PRICE * $QTY) + ($EXTRA_PRICE * $QTY);
							$SUM_PRICE = ($SALE_PRICE * $QTY);
							$TOT_SUM_PRICE = $TOT_SUM_PRICE + $SUM_PRICE;
							$TOT_QTY = $TOT_QTY + $QTY;
							$TOT_SA_DELIVERY_PRICE = $TOT_SA_DELIVERY_PRICE + $SA_DELIVERY_PRICE;
				
				?>
						<tr height="23">
							<td><?=$RESERVE_NO?></td>
							<td class="pname"><?=$O_MEM_NM?></td>
							<td class="pname"><?=$R_MEM_NM?></td>
							<td class="modeual_nm"><?=$GOODS_NAME?></td>
							<td class="price"><?=number_format($SALE_PRICE)?></td>
							<td class="price"><?=number_format($EXTRA_PRICE)?></td>
							<td class="price"><?=number_format($DELIVERY_PRICE)?></td>
							<td class="price"><?=number_format($QTY)?></td>
							<td class="price"><?=number_format($SUM_PRICE)?></td>
							<td class="price"><?=number_format($SA_DELIVERY_PRICE)?></td>
							<td class="pname"><?=getDcodeName($conn, "ORDER_STATE", $ORDER_STATE);?></td>
							<td class="filedown"><?= $FINISH_DATE ?></td>
						</tr>
				<?
						}
					}
				?>
						<tr height="23">
							<td>합계</td>
							<td colspan="6">&nbsp;</td>
							<td class="price"><?=number_format($TOT_QTY)?></td>
							<td class="price"><?=number_format($TOT_SUM_PRICE)?></td>
							<td class="price"><?=number_format($TOT_SA_DELIVERY_PRICE)?></td>
							<td class="pname">&nbsp;</td>
							<td class="filedown">&nbsp;</td>
						</tr>
					</table>
				<div class="sp20"></div>
			</td>
		</tr>
		</table>
	</div>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
	<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
</div>
</form>
</body>
</html>
<script type="text/javascript" src="../js/wrest.js"></script>
<?
#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);
?>