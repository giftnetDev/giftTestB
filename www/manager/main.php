<?session_start();?>
<?
# =============================================================================
# File Name    : main.php
# =============================================================================

#====================================================================
# common_header Check Session
#====================================================================
//	include "$_SERVER[DOCUMENT_ROOT]/common/common_header.php"; 

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");
	
#=====================================================================
# common function, login_function
#=====================================================================
	require "../_common/config.php";
	require "../_classes/com/util/Util.php";
	require "../_classes/com/etc/etc.php";
	require "../_classes/biz/admin/admin.php";
	require "../_classes/biz/goods/goods.php";


	if($mode == "EXCEPT_FROM_CATEGORY") { 
		$row_cnt = count($chk_no);

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_goods_no = $chk_no[$k];
			$result = deleteGoodsCategoryBatch($conn, $str_goods_no, $goods_cate);
		}

?>	
<script language="javascript">
		alert('정상 처리 되었습니다.');
</script>
<?

	}

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="css/admin.css" type="text/css" />
<script language="javascript" type="text/javascript" src="js/common.js"></script>
<script type="text/javascript" src="/manager/jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="/manager/jquery/jquery.floatThead.min.js"></script>
<script type="text/javascript" src="/manager/jquery/jquery.cookie.js"></script>
<style type="text/css">
	* {margin:0; padding:0}
	body {width:100%; height:100%; position:relative}
	img {vertical-align:top}

#list_wrap {float:left; top:100px; padding:10px}
.list dl {float:left; margin-bottom:15px;}
.list dl dt {padding-bottom:10px}
.list dl dt .goods_title {font-weight:bold; font-size:1.3em;}
.list dl dt > a {float:right;}
.list dl dd {line-height:1.4em}
.list dl ul {padding-left:10px; padding-right:5px}
.list dl ul li {padding:5px}

table.rowstable {width:100%;} 
.temp_scroll_title {width: 800px; } 
.temp_scroll { z-index: 1; background-color:#f7f7f7; overflow: auto; width: 800px; height:400px; border:1px solid #d1d1d1;}
.temp_scroll > ul {width:1000px;}
</style>
<script>
	function js_notice_popup() {

		var frm = document.frm;
		
		var url = "pop_goods_list_for_main.php";

		NewWindow(url, 'pop_goods_list_for_main','860','600','YES');
		
	}

	$(function(){
		$('table.fixed_header_table').floatThead({
			getSizingRow: function($table){ // this is only called when using IE
				return $table.find('tbody tr:not(.grouping):visible:first>*');
			}, position: 'fixed'
		});
	});
</script>
<script type="text/javascript">
	function js_view(goods_no) {

		
		var frm = document.frm;
		
		frm.goods_no.value = goods_no;
		frm.mode.value = "S";
		frm.target = "_blank";
		frm.method = "get";
		frm.action = "/manager/goods/goods_write.php";
		frm.submit();

		//var url = "/manager/goods/goods_write.php?mode=S&goods_no=" + goods_no;
		//NewWindow(url, 'pop_goods_list_for_main','860','600','YES');
		
	}

	function js_update_category(goods_cate) { 
		
		var frm = document.frm;
		
		frm.goods_cate.value = goods_cate;

		bDelOK = confirm('선택한 상품을 현재 카테고리에서 제외 하시겠습니까?');
		
		if (bDelOK==true) {
			
			frm.mode.value = "EXCEPT_FROM_CATEGORY";
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

</script>
</head>
<body id="admin">

<form name="frm" method="post">
<input type="hidden" name="goods_no" value="">
<input type="hidden" name="goods_cate" value="">
<input type="hidden" name="mode" value="">
<div id="adminwrap">

<?
	#====================================================================
	# common top_area
	#====================================================================

	require "../_common/top_area.php";
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

	require "../_common/left_area.php";
?>
		</td>
		<td class="contentarea">

			<!-- S: mwidthwrap -->
			<div id="mwidthwrap">
				<h2> 관리자 홈</h2>
				<? if ($s_adm_cp_type == "운영") { ?>
				<div id="list_wrap">
					
					<div class="list">
						<dl>
							<?
								$goods_cate1_cate = getDcodeName($conn, 'MANAGER_SETUP', '상품리스트1');
								$goods_cate1_title = getDcodeExtByCode($conn, 'MANAGER_SETUP', '상품리스트1');
								$cnt_empty = 0;
							?>
							<dt><span class="goods_title"><?=$goods_cate1_title?></span>
								<a href="/manager/goods/goods_list.php?con_cate=<?=$goods_cate1_cate?>" style="text-decoration:underline;">상품관리에서 보기</a>
							</dt>
						
							<dd class="temp_scroll_title">
								<table cellpadding="0" cellspacing="0" class="rowstable">
								    <colgroup>
										<col width="60" />
										<col width="350" />
										<col width="40" />
										<col width="40" />
									</colgroup>
									<tr>
										<th>상품코드</th>
										<th>상품명</th>
										<th>정상재고</th>
										<th>불량재고</th>
									</tr>
								</table>
								<div class="temp_scroll">
									<table cellpadding="0" cellspacing="0" class="rowstable">
										<colgroup>
											<col width="60" />
											<col width="350" />
											<col width="40" />
											<col width="40" />
										</colgroup>
										<?
											
											$arr_delayed = listGoods($conn, $goods_cate1_cate, '', '', '', '', '', '', '', '', '', 'Y', 'N', '', '', '', 'STOCK_CNT', 'DESC', 1, 10000);


											if (sizeof($arr_delayed) > 0) {
							
												for ($j = 0 ; $j < sizeof($arr_delayed); $j++) {
													$GOODS_NO				= trim($arr_delayed[$j]["GOODS_NO"]);
													$GOODS_CODE				= trim($arr_delayed[$j]["GOODS_CODE"]);
													$GOODS_NAME				= SetStringFromDB($arr_delayed[$j]["GOODS_NAME"]);
													$STOCK_CNT				= trim($arr_delayed[$j]["STOCK_CNT"]);
													$BSTOCK_CNT				= trim($arr_delayed[$j]["BSTOCK_CNT"]);

													if($STOCK_CNT == "0" && $BSTOCK_CNT == "0") { 
														$cnt_empty ++;
														continue;
													}

											/*
											$arr_delayed = listSearchGoodsCategory($conn, $goods_cate1_cate, 'Y', '', '');

											if(sizeof($arr_delayed) > 0) { 
												for($i = 0; $i < sizeof($arr_delayed); $i++) {

													$GOODS_CODE  = $arr_delayed[$i]["GOODS_CODE"];
													$GOODS_NAME  = $arr_delayed[$i]["GOODS_NAME"];
											*/
										?>
										<tr height="30">
											<td><a href="javascript:js_view('<?= $GOODS_NO ?>');"><?=$GOODS_CODE?></a></td>
											<td class="modeual_nm"><a href="javascript:js_view('<?= $GOODS_NO ?>');"><?=$GOODS_NAME?></a></td>
											<td><?=number_format($STOCK_CNT)?></td>
											<td><?=number_format($BSTOCK_CNT)?></td>
										</tr>
										<?
												}
											}
										?>
									</table>
								</div>
							</dd>
							<? if($cnt_empty > 0) { ?>
							<div class="sp20"></div>
							<dt>
								<span class="goods_title">부진 재고 소진 완료 리스트</span>
								
							</dt>
							<dd class="temp_scroll_title">
								<table cellpadding="0" cellspacing="0" class="rowstable">
									<colgroup>
										<col width="5" />
										<col width="60" />
										<col width="350" />
										<col width="40" />
										<col width="40" />
									</colgroup>
									<tr>
										<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
										<th>상품코드</th>
										<th>상품명</th>
										<th>정상재고</th>
										<th>불량재고</th>
									</tr>
									<?
										
										$arr_delayed = listGoods($conn, $goods_cate1_cate, '', '', '', '', '', '', '', '', '', 'Y', 'N', '', '', '', '', '', 1, 10000);


										if (sizeof($arr_delayed) > 0) {
						
											for ($j = 0 ; $j < sizeof($arr_delayed); $j++) {
												$GOODS_NO				= trim($arr_delayed[$j]["GOODS_NO"]);
												$GOODS_CODE				= trim($arr_delayed[$j]["GOODS_CODE"]);
												$GOODS_NAME				= SetStringFromDB($arr_delayed[$j]["GOODS_NAME"]);
												$STOCK_CNT				= trim($arr_delayed[$j]["STOCK_CNT"]);
												$BSTOCK_CNT				= trim($arr_delayed[$j]["BSTOCK_CNT"]);

												if($STOCK_CNT == "0" && $BSTOCK_CNT == "0") { 

									?>
									<tr height="30">
										<td><input type="checkbox" name="chk_no[]" class="chk" value="<?=$GOODS_NO?>"></td>
										<td><a href="javascript:js_view('<?= $GOODS_NO ?>');"><?=$GOODS_CODE?></a></td>
										<td class="modeual_nm"><a href="javascript:js_view('<?= $GOODS_NO ?>');"><?=$GOODS_NAME?></a></td>
										<td><?=number_format($STOCK_CNT)?></td>
										<td><?=number_format($BSTOCK_CNT)?></td>
									</tr>
									<?
												}
											}
										}
									?>
								</table>
								<div style="text-align:right; margin-top:5px;">
									<input type="button" name="btn_except_category" value=" 선택한 상품 카테고리에서 제외 " onclick="javascript:js_update_category('<?=$goods_cate1_cate?>')"/>
								</div>
							</dd>
							<? } ?>
						</dl>

					</div><!--//list-->
				</div><!--//list_wrap-->
				<? } ?>
				
			</div>
		</td>
	</tr>
	</table>
</div>
</form>
<? if ($s_adm_cp_type == "운영") { ?>
<script>
	if($.cookie('chk_latest_goods') != '<?=date("Y-m-d",strtotime("0 day"))?>') {
			js_notice_popup();
	}
</script>
<? } ?>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>