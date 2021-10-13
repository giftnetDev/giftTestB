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
	$menu_right = "SG018"; // �޴����� ���� �� �־�� �մϴ�

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
	require "../../_classes/com/util/ImgUtil.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/stock/stock.php";
	require "../../_classes/biz/order/order.php";

#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);

	if($mode == "INSERT") { 
		$arr_rs = listOrderGoodsByDeliveryNo($conn, $delivery_no);
		$chk_result = chkDeliveryCalculator($conn, $delivery_no);

if(!$chk_result) { 
?>	
<script language="javascript">
		alert('�̹� ���� �����̰ų� �ý��� �����Դϴ�.');
</script>
<?

} else {
			for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
				$GOODS_CODE				= trim($arr_rs[$j]["GOODS_CODE"]);
				$GOODS_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
				$GOODS_TOTAL			= trim($arr_rs[$j]["GOODS_TOTAL"]);

				//echo $delivery_no." // ".$GOODS_CODE." // ".$GOODS_NAME." // ".$GOODS_TOTAL."<br/>";
				$result = insertDeliveryCalculator($conn, $delivery_no, $GOODS_CODE, $GOODS_NAME, $GOODS_TOTAL);


			}

		}

	}

	if($mode == "RESET") { 
		resetDeliveryCalculator($conn);

	}

	if($mode == "CANCEL") {
		cancelDeliveryCalculator($db, $delivery_no);
	}

#====================================================================
# DML Process
#====================================================================
	

	if ($delivery_no != "") {
		$arr_rs = listOrderGoodsByDeliveryNo($conn, $delivery_no);
	}

	$arr_sum = listDeliveryCalculator($conn);

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
<script type="text/javascript" src="../jquery/jquery-barcode.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script>

	// ��ȸ ��ư Ŭ�� �� 
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_reset() {
		var frm = document.frm;
		
		frm.mode.value = "RESET";
		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_excel() {

		var frm = document.frm;
		
		frm.mode.value = "";
		frm.nPage.value = "1";
		frm.target = "";
		frm.action = "<?=str_replace("calculator.","calculator_excel.",$_SERVER[PHP_SELF])?>";
		frm.submit();
	}
</script>

</head>
<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="mode" value="">
<input type="hidden" name="nPage" value="">
<input type="hidden" name="prev_delivery_no" value="<?=$prev_delivery_no?>">
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

				<h2>���� ����</h2>

				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="120" />
					<col width="*" />
					<col width="120" />
					<col width="*" />
					<col width="120" />
				</colgroup>
				<tbody>
					<tr>

						<th>�����ȣ :</th>
						<td>
							<input type="Text" name="delivery_no" value="" style="ime-mode:disabled;"  class="txt">
						</td>
						<th>ȭ�� �ʱ�ȭ</th>
						<td >	
							<span name="kancode_clear"></span>
							<script>
								$(function(){
									$("span[name=kancode_clear]").barcode("RESET", "code128", {output:'bmp', barHeight:30});  
								});
							</script>
							 RESET
							 <input type="button" name="bb" onclick="javascript:js_reset();"  value="����"/>
						</td>
						<td align="right">
							<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="���� ����Ʈ" /></a>
						</td>
					</tr>
					<tr>
						<!--<th>���� ��ĵ ���</th>
						<td colspan="2">
							<span name="kancode_cancel"></span>
							<script>
								$(function(){
									$("span[name=kancode_cancel]").barcode("CANCEL", "code128", {output:'bmp', barHeight:30});  
								});
							</script>
							 CANCEL
						</td>-->
					</tr>
				</tbody>
				</table>
				<script>
					$(function(){

						$("input[name=delivery_no]").keydown(function(event){
							if(event.keyCode == 13)
							{
								if($(this).val() == "RESET")
								{
									var frm = document.frm;

									frm.mode.value = "RESET";
									frm.target = "";
									frm.action = "<?=$_SERVER[PHP_SELF]?>";
									frm.submit();
								
								
								} else if($(this).val() == "CANCEL")
								{
									var frm = document.frm;

									frm.mode.value = "RESET";
									frm.target = "";
									frm.action = "<?=$_SERVER[PHP_SELF]?>";
									frm.submit();

								} else {

									var frm = document.frm;

									//alert($("input[name=delivery_no]").val());
									frm.mode.value = "INSERT";
									frm.target = "";
									frm.action = "<?=$_SERVER[PHP_SELF]?>";
									frm.submit();
								}
							}
						});

						$("input[name=delivery_no]").focus();


					});
				</script>
				<div class="sp10"></div>
				<h3>���� ��ǰ ����</h3>
				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="25%">
					<col width="*">
					<col width="25%">
				</colgroup>
				<thead>
					<th>��ǰ�ڵ�</th>
					<th>��ǰ��</th>
					<th>�� ����</th>
				</thead>
				<?
					if (sizeof($arr_rs) > 0) {
				
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
					
						$GOODS_CODE				= trim($arr_rs[$j]["GOODS_CODE"]);
						$GOODS_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
						$GOODS_SUB_NAME			= SetStringFromDB($arr_rs[$j]["GOODS_SUB_NAME"]);
						$GOODS_TOTAL			= trim($arr_rs[$j]["GOODS_TOTAL"]);
				
				?>
				<tr>
					<td class="line"><?=$GOODS_CODE?></td>
					<td class="line"><?=$GOODS_NAME?></td>
					<td class="line"><?=$GOODS_TOTAL?></td>
				</tr>
				<?
						}
					} else {

						if ($delivery_no <> "") { 
				?>
				<!-- �˻���� ���� -->
				<tr><td colspan="3" height="35" align="center"><b>(<?=$delivery_no?>) �߰������̰ų� �̵�� �����Դϴ�.</b></td></tr>
				<script>
					$(function(){
						$("input[name=delivery_no]").val('').focus();
					});
				</script>
				<?		$delivery_no = "";
						}
					} 
				?>

				<!-- ���� ��� -->
				<? if($result_reset) { ?>
				<tr><td colspan="7" height="35" align="center"><b>������ ��� �Ǿ����ϴ�</b></td></tr>
				<script>
					$(function(){
						$("input[name=delivery_no]").val('').focus();
					});
				</script>
				<?  
					} 
				?>
				
				</table>
				<div class="sp10"></div>
				<h3>��������� ��ǰ ���� �ջ�</h3>
				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="25%">
					<col width="*">
					<col width="25%">
				</colgroup>
				<thead>
					<th>��ǰ�ڵ�</th>
					<th>��ǰ��</th>
					<th>�� ����</th>
				</thead>
				<?
					if (sizeof($arr_sum) > 0) {
				
						for ($j = 0 ; $j < sizeof($arr_sum); $j++) {
					
						$GOODS_CODE				= trim($arr_sum[$j]["GOODS_CODE"]);
						$GOODS_NAME				= SetStringFromDB($arr_sum[$j]["GOODS_NAME"]);
						$SUM_GOODS_TOTAL			= trim($arr_sum[$j]["SUM_GOODS_TOTAL"]);
				
				?>
				<tr>
					<td class="line"><?=$GOODS_CODE?></td>
					<td class="line"><?=$GOODS_NAME?></td>
					<td class="line"><?=$SUM_GOODS_TOTAL?></td>
				</tr>
				<?
					}
				} else{
					?>
					<tr class="order">
						<td height="50" align="center" colspan="3">�����Ͱ� �����ϴ�. </td>
					</tr>
				<?
					}
				?>
				</table>

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