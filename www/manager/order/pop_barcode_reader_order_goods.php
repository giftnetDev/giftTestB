<?session_start();?>
<?
# =============================================================================
# File Name    : pop_barcode_reader.php
# Modlue       : 
# Writer       : Sungwook Min
# Create Date  : 2015-11-05
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
	include "../../_common/common_header.php"; 

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/stock/stock.php";
	require "../../_classes/biz/admin/admin.php";
#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);

	if ($order_date == "") 
	$order_date = date("Y-m-d H:i:s",strtotime("0 month"));

	if($mode == "I")
	{
	  	insertScannedGoods($conn, $order_date, $cp_type, $search_text, $s_adm_no);
	}

#====================================================================
# DML Process
#====================================================================

	$arr = selectScannedGoods($conn, $order_date, "");

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
<script type="text/javascript" src="../jquery/jquery-ui-timepicker-addon.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui-timepicker-addon.css" type="text/css" />
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<script>
  $(function() {
	 $('.datepicker').datetimepicker({
	   	  dateFormat: "yy-mm-dd", 
		  timeFormat: "HH:mm:ss",
		  buttonImage: "/manager/images/calendar/cal.gif",
          buttonImageOnly: true,
          buttonText: "Select date",
     	  showOn: "both",
	      changeMonth: true,
	      changeYear: true
     });
  });
</script>
</head>
<body id="popup_file" onload="document.getElementsByName('search_text')[0].focus();">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="" />
<div id="popupwrap_file">
	<h1>바코드입력</h1>
	<div id="postsch">
		<h2>* 바코드 입력합니다.</h2>
		<div class="addr_inp">
			<table cellpadding="0" cellspacing="0" class="colstable02">
				<colgroup>
					<col width="20%" />
					<col width="35%" />
					<col width="15%" />
					<col width="*%" />
				</colgroup>
				<tr>
					<th>주문일</th>
					<td class="line">
						<input type="text" class="txt datepicker" style="width: 150px; margin-right:3px;" name="order_date" value="<?=$order_date?>" maxlength="10"/>
					</td>
					<th>판매업체</th>
					<td class="line">
						<?=makeCompanySelectBoxWithNameWithoutCP($conn, 'cp_type', '운영', $cp_type)?>
					</td>
				</tr>
				<tr>
					<th>칸코드</th>
					<td class="line">
						<input type="text" class="txt" style="width:80%;" name="search_text" value="" />

						<script>
							$(function(){
								$("input[name=search_text]").keydown(function(event){
									if(event.keyCode == 13)
									{
										var frm = document.frm;

										frm.mode.value = "I";
										frm.target = "";
										frm.action = "<?=$_SERVER[PHP_SELF]?>";
										frm.submit();
									}
								});
							});
						</script>



					</td>
					<td colspan="2" class="line"></td>
				</tr>
			</table>
			<div class="btn">
			  <a href="javascript:window.close();"><img src="../images/admin/btn_cancel.gif" alt="취소" /></a>
			</div>      
		</div>

		<h2>* 체크된 리스트. </h2>
		<div class="sp10"></div>
		총 스캔 수량 : <span class="total_num"><?=sizeof($arr)?></span>
		<div class="addr_inp">
			<table cellpadding="0" cellspacing="0" class="colstable02">
				<colgroup>
					<col width="10%" />
					<col width="*" />
					<col width="10%" />
					<col width="15%" />
					<col width="10%" />
					<col width="10%" />
				</colgroup>
				<thead>
					<tr>
						<th>순번</th>
						<th>주문시간</th>
						<th>처리업체</th>
						<th>칸코드</th>
						<th>등록자</th>
						<th class="end">등록일시</th>
					</tr>
				</thead>
				<tbody>
					<?
						if(sizeof($arr) > 0) {
							for($i = 0; $i < sizeof($arr); $i ++) { 
								$rs_order_date = $arr[$i]["ORDER_DATE"];
								$rs_cp_no	   = $arr[$i]["CP_NO"];
								$rs_kancode	   = $arr[$i]["KANCODE"];
								$rs_reg_adm	   = $arr[$i]["REG_ADM"];
								$rs_reg_date   = $arr[$i]["REG_DATE"];

								$rs_admin			= selectAdmin($conn, $rs_reg_adm);
								$rs_adm_name	= SetStringFromDB($rs_admin[0]["ADM_NAME"]);

								$rs_reg_date = date("Y-m-d",strtotime($rs_reg_date));
					?>
						<tr>
							<td><?= $i + 1 ?></td>
							<td><?=$rs_order_date?></td>
							<td><?= getCompanyName($conn, $rs_cp_no);?></td>
							<td><?=$rs_kancode?></td>
							<td><?=$rs_adm_name?></td>
							<td><?=$rs_reg_date?></td>
						</tr>

					<? } 
					} else { ?>

					<tr>
						<td colspan="6">데이터가 없습니다</td>
					</tr>

					<? } ?>

				</tbody>
				
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