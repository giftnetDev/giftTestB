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
	$menu_right = "OD015"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

#====================================================================
# common_header Check Session
#====================================================================
//	include "../../_common/common_header.php"; 

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/stock/stock.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/admin/admin.php";
#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);

#====================================================================
# DML Process
#====================================================================

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
<style> 
	body#popup_file {width:100%;}
	#popupwrap_file {width:100%;}
	input[type=button] {width:95%; height:50px; margin:10px;}
</style>
</head>
<body id="popup_file" onload="document.getElementsByName('search_txt')[0].focus();">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="" />
<div id="popupwrap_file">
	<h1>재고체크</h1>
	<div id="postsch">
		<div class="addr_inp">
			<table cellpadding="0" cellspacing="0" class="colstable02">
				<colgroup>
					<col width="20%" />
					<col width="40%" />
					<col width="40%" />
				</colgroup>
				<tr>
					<th>낱개,박스바코드/자재코드</th>
					<td colspan="2" class="line">
						<input type="text" class="txt" style="width:80%;" name="search_txt" />
					</td>
				</tr>
			</table>
			<div class="sp10"></div>
			<table cellpadding="0" cellspacing="0" class="colstable02">
				<colgroup>
					<col width="10%" />
					<col width="*" />
					<col width="20%" />
					<col width="20%" />
				</colgroup>
				<tr>
					<th>자재코드</th>
					<th>상품명</th>
					<th>정상재고</th>
					<th>불량재고</th>
				</tr>
				<?
					if($mode == "S") { 
					$arr_goods = chkGoodsByKeyword($conn, $search_txt);
						if(sizeof($arr_goods) > 0) {
							for($i = 0; $i < sizeof($arr_goods); $i ++) { 
								$rs_goods_no = $arr_goods[$i]["GOODS_NO"];
								$rs_goods_code = $arr_goods[$i]["GOODS_CODE"];
								$rs_goods_name = $arr_goods[$i]["GOODS_NAME"];
								$rs_stock_cnt = $arr_goods[$i]["STOCK_CNT"];
								$rs_bstock_cnt = $arr_goods[$i]["BSTOCK_CNT"];
				?>
				<tr>
					<td>
						<?=$rs_goods_code?>
					</td>
					<td>
						<?=$rs_goods_name?>
					</td>
					<td>
						<?=$rs_stock_cnt?>
					</td>
					<td>
						<?=$rs_bstock_cnt?>
					</td>
				</tr>
				<?
							}
						}
					}
				?>
			</table>
			<div class="sp10"></div>
			<table cellpadding="0" cellspacing="0" class="colstable02">
				<colgroup>
					<col width="33%" />
					<col width="33%" />
					<col width="*" />
				</colgroup>
				<tr>
					<td>
						<input type="button" class="pad num"  name="num_1" value="1"/>
					</td>
					<td>
						<input type="button" class="pad num"  name="num_2" value="2"/>
					</td>
					<td>
						<input type="button" class="pad num"  name="num_3" value="3"/>
					</td>
				</tr>
				<tr>
					<td>
						<input type="button" class="pad num"  name="num_4" value="4"/>
					</td>
					<td>
						<input type="button" class="pad num"  name="num_5" value="5"/>
					</td>
					<td>
						<input type="button" class="pad num"  name="num_6" value="6"/>
					</td>
				</tr>
				<tr>
					<td>
						<input type="button" class="pad num"  name="num_7" value="7"/>
					</td>
					<td>
						<input type="button" class="pad num"  name="num_8" value="8"/>
					</td>
					<td>
						<input type="button" class="pad num"  name="num_9" value="9"/>
					</td>
				</tr>
				<tr>
					<td>
						<input type="button" class="num"  name="num_Clear" value="Clear"/>
					</td>
					<td>
						<input type="button" class="pad num"  name="num_0" value="0"/>
					</td>
					<td>
						<input type="button" class="num"  name="num_Enter" value="Enter"/>
						<script>
							$(function(){

								$("input[name=search_txt]").keydown(function(event){
									if(event.keyCode == 13)
									{
										var frm = document.frm;

										frm.mode.value = "S";
										frm.target = "";
										frm.action = "<?=$_SERVER[PHP_SELF]?>";
										frm.submit();
									}
								});

								var hasHit = 0;
								$(".pad.num").click(function(){
									if(hasHit == 0)
										$("input[name=search_txt]").val('');

									$("input[name=search_txt]").val($("input[name=search_txt]").val() + $(this).val());
									hasHit++;
								});

								$("input[name=num_Clear]").click(function(event){
										
									$("input[name=search_txt]").val('');
								});

								$("input[name=num_Enter]").click(function(event){
										
									var frm = document.frm;

									frm.mode.value = "S";
									frm.target = "";
									frm.action = "<?=$_SERVER[PHP_SELF]?>";
									frm.submit();
								});
							});
						</script>
					</td>
				</tr>
			</table>     
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