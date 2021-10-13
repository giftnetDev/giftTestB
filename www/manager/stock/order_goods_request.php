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
	$menu_right = "SG020"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/stock/stock.php";


	if ($mode == "I") {
		
		//echo $goods_no."<br/>";
		//echo $req_cnt."<br/>";

		if($goods_no <> "" && $req_cnt <> "")
			InsertRequestGoods($conn, $goods_no, $req_cnt, $s_adm_no);

	}

	$day_0 = date("Y-m-d",strtotime("0 month"));

#===============================================================
# Get Search list count
#===============================================================

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script type="text/javascript" src="../jquery/jquery-ui-timepicker-addon.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui-timepicker-addon.css" type="text/css" />
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<script type="text/javascript" src="../jquery/jquery.floatThead.min.js"></script>
<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script>
<script>
	$(function(){
		$('table.fixed_header_table').floatThead({
			position: 'fixed'
		});
	});
</script>
<script language="javascript">
	
	function js_save(goods_no) {

		var frm = document.frm;
		
		frm.mode.value = "I";
		frm.goods_no.value = goods_no;
		frm.req_cnt.value = $("input[name='arr_req_cnt[]'][data-goods_no="+goods_no+"]").val();
		frm.target = "";
		frm.method = "post";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
		
	}
</script>
</head>
<body id="admin">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="goods_no" value="">
<input type="hidden" name="req_cnt" value="">

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
	include_once('../../_common/editor/func_editor.php');

?>
<style>
	table.rowstable04 { border-top: none; }
	table.rowstable04 > th { padding: 9px 0 8px 0; font-weight: normal; color: #86a4b2; border-top: 1px solid #d2dfe5; background: #ebf3f6 url('../images/admin/bg_bar_01.gif') right center no-repeat; }
	table.rowstable04 > th.end { background: #ebf3f6; }
	table.rowstable04 td { color: #555555; text-align: center; vertical-align: middle; background: none; }

</style>
		</td>
		<td class="contentarea">

			<!-- S: mwidthwrap -->
			<div id="mwidthwrap">
				<h2>주문 발주 관리</h2>
				
			<form name="frm_stock" method="post">
			<input type="hidden" name="s_adm_no" value="<?=$s_adm_no?>"/>
			<!-- // E: mwidthwrap -->

			<?
				// 부족 재고 리스트
				$sub_stock_list = listTempFakeOrderStock2($conn);
			?>
			* 재고 부족 자재 리스트
			<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table">

				<colgroup>
					<col width="2%" />
					<col width="8%" />
					<col width="*" />
					<col width="12%" />
					<col width="12%" />
					<col width="6%" />
					<col width="6%" />
					<col width="6%" />
					<col width="6%" />
					<col width="6%" />
					<col width="6%" />
					<col width="6%" />
					<col width="7%" />
					<col width="6%" />
				</colgroup>
				<thead>
					<tr>
						<th colspan="3"></th>
						<th colspan="2">동일바코드</th>
						<th colspan="8">상품정보</th>
					</tr>
					<tr>
						<th></th>
						<th>자재코드</th>
						<th>자재명</th>

						<th>발주수량</th>
						<th>재고수량</th>

						<th>금일<br/>주문수량</th>
						<th>창고<br/>발주요청</th>
						<th>가재고</th>
						<th>정상재고</th>
						<th>총<br/>가용재고</th>
						<th>최소재고</th>
						<th>박스입수</th>
						<th>발주요청<br/>박스수량</th>
						<th class="end"> </th>
					</tr>
				</thead>
				<tbody>
				<?
					$nCnt = 0;
					
					if (sizeof($sub_stock_list) > 0) {
						
						for ($j = 0 ; $j < sizeof($sub_stock_list); $j++) {

							//echo $j."<br/>";
							
							$GOODS_NO				= trim($sub_stock_list[$j]["GOODS_NO"]);
							$GOODS_CODE				= trim($sub_stock_list[$j]["GOODS_CODE"]);
							$GOODS_NAME				= SetStringFromDB($sub_stock_list[$j]["GOODS_NAME"]);
							$TOTAL_ORDER_QTY		= trim($sub_stock_list[$j]["TOTAL_ORDER_QTY"]);
							$F_STOCK_CNT			= trim($sub_stock_list[$j]["F_STOCK_CNT"]);
							$N_STOCK_CNT			= trim($sub_stock_list[$j]["N_STOCK_CNT"]);
							$TOTAL_STOCK_CNT		= trim($sub_stock_list[$j]["TOTAL_STOCK_CNT"]);
							$GOODS_CNT_IN_BOX		= trim($sub_stock_list[$j]["GOODS_CNT_IN_BOX"]);
							$MSTOCK_CNT				= trim($sub_stock_list[$j]["MSTOCK_CNT"]);
							$KANCODE				= trim($sub_stock_list[$j]["KANCODE"]);
							$WAREHOUSE_REQUEST_CNT	= trim($sub_stock_list[$j]["WAREHOUSE_REQUEST_CNT"]);
							
							
							$chk_qty = $TOTAL_ORDER_QTY;

							$chk_request = listRequestedGoodsRequest($conn, $KANCODE);
							$chk_request_stock_cnt = listRequestedGoodsRequestStockCnt($conn, $KANCODE, $GOODS_CODE);

							$req_box_cnt = ceil(($MSTOCK_CNT - ($TOTAL_STOCK_CNT - $chk_qty < 0 ? 0 : $TOTAL_STOCK_CNT - $chk_qty)) / $GOODS_CNT_IN_BOX); 
								
							
							$is_req_today = totalCntRequestGoods($conn, $day_0, $day_0, 'N', 'GOODS_NO', $GOODS_NO);

							
				?>
					<tr <?if($UNSEND_GOODS_TOTAL > 0) echo "style='background-color:#EFEFEF;'"; ?>>
						<td>
						</td>
						<td><?=$GOODS_CODE?></td>
						<td class="pname" style="text-align:left;padding-left:5px;"><?=$GOODS_NAME?></td>
						<td class="pname" style="text-align:left;padding-left:5px;"><?=$chk_request?></td>
						<td class="pname" style="text-align:left;padding-left:5px;"><?=$chk_request_stock_cnt?></td>
						<td style="text-align:right;padding-right:5px;"><?=number_format($TOTAL_ORDER_QTY)?></td>
						<td style="text-align:right;padding-right:5px;"><?=number_format($WAREHOUSE_REQUEST_CNT)?></td>
						<td style="text-align:right;padding-right:5px;"><?=number_format($F_STOCK_CNT)?></td>
						<td style="text-align:right;padding-right:5px;"><?=number_format($N_STOCK_CNT)?></td>
						<td style="text-align:right;padding-right:5px; font-weight:bold;"><?=number_format($TOTAL_STOCK_CNT)?></td>
						<td class="filedown" style="text-align:right;padding-right:5px;"><?=number_format($MSTOCK_CNT)?></td>
						<td class="filedown" style="text-align:right;padding-right:5px;"><?=number_format($GOODS_CNT_IN_BOX)?></td>
						<td>
							<!--
							<input type="hidden" name="req_goods_no[]" value="<?=$GOODS_NO?>">
							<input type="hidden" name="req_goods_code[]" value="<?=$GOODS_CODE?>">
							<input type="hidden" name="req_goods_nm[]" value="<?=$GOODS_NAME?>">
							<input type="hidden" name="req_goods_order[]" value="<?=$TOTAL_ORDER_QTY?>">
							<input type="hidden" name="req_unsend_goods_total[]" value="<?=$UNSEND_GOODS_TOTAL?>">
							<input type="hidden" name="req_f_stock[]" value="<?=$F_STOCK_CNT?>">
							<input type="hidden" name="req_n_stock[]" value="<?=$N_STOCK_CNT?>">
							<input type="hidden" name="req_a_stock[]" value="<?=$TOTAL_STOCK_CNT?>">
							<input type="hidden" name="req_goods_cnt_in_box[]" value="<?=$GOODS_CNT_IN_BOX?>">
							-->
							<input type="text" name="arr_req_cnt[]" data-goods_no="<?=$GOODS_NO?>" style="width:50px;text-align:right" value="<?=$req_box_cnt?>">
						</td>
						<td>
							<? if($is_req_today <= 0) { ?>
							<input type="button" name="bb" value="발주추가" class="btntxt" onclick="js_save('<?=$GOODS_NO?>');"/>
							<? } ?>
						</td>
					</tr>
				<?
						}
					}
				?>
				</tbody>
			</table>
			<!--
			<div class="btnrighttxt">
				발주서 받을 이메일 주소 : <input type="text" name="recive_email" value="gift@giftnet.co.kr">&nbsp;&nbsp;
				<input type="button" name="aa" value=" 발주 메일 보내기 " class="btntxt" onclick="js_send_mail();">&nbsp;&nbsp;&nbsp;&nbsp; 
			</div>
			-->

<script>
	
	function js_send_mail() {
		
		var frm = document.frm_stock;
		
		if (frm.recive_email.value == "") {
			alert('발주서 받을 이메일 주소를 입력해 주십시오.');
			return;
		}

		frm.target = "_blank";
		frm.action = "../order/send_req_stock.php";
		frm.submit();

	}

</script>

			<br>
			<br>

    </td>
  </tr>
  </table>
</form>


    </td>
  </tr>
  </table>




</div>
<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>