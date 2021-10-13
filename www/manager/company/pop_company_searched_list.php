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
	$menu_right = "CP002"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/company/company.php";


#====================================================================
# Request Parameter
#====================================================================

	

	
#===============================================================
# Get Search list count
#===============================================================

	$nPage = 1;
	$nPageSize = 1000;
	$search_field = "CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE,CEO_NM,BIZ_NO";

	$filter = array('con_is_mall' => $con_is_mall);

	$use_tf = 'Y';
	$del_tf = 'N';

	$arr_rs = listCompany($conn, $con_cate, $con_cp_type, $con_ad_type, $date_start, $date_end, $min_dc_rate, $max_dc_rate, $sel_sale_adm_no, $filter, $use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />

<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery.floatThead.min.js"></script>
<script>
	$(function(){
		$('table.fixed_header_table').floatThead({
			position: 'fixed'
		});
	});
</script>
<script language="javascript">
	
	function js_search()
	{
		var frm = document.frm;

		frm.mode.value = "S";
		frm.target = "";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

</script>
<style>
	.top_group td {border-top: 2px solid black;  }
	.bottom_group td {border-top: 1px dotted black; }
	table.rowstable td {background: none;}
	table.rowstable {border-bottom: 2px solid black;} 
	.btnright {text-align:right; padding-right:50px; margin: 4px 0;}
</style>
</head>

<body id="popup_order_wide">

<div id="popupwrap_order_wide">
	<h1>거래처 조회</h1>
	<div id="postsch_code">

		<div class="addr_inp">

<form name="frm" method="post">
<input type="hidden" name="mode" value="">
<input type="hidden" name="keyword" value="">
<input type="hidden" name="target_name" value="<?=$target_name?>">
<input type="hidden" name="target_value" value="<?=$target_value?>">

	<div class="btnright">
		<?= makeSelectBox($conn,"CP_TYPE","con_cp_type","125","전체","",$con_cp_type)?> <input type="text" value="<?=$search_str?>" name="search_str" size="20" class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();" />
		<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
	</div>
	<table cellpadding="0" cellspacing="0" class="rowstable01 fixed_header_table">
		<colgroup>
			<col width="5%">
			<col width="*">
			<col width="10%">
			<col width="12%">
			<col width="10%">
			<col width="7%">
			<col width="6%">
			<col width="7%">
			<col width="7%">
			<col width="10%">
		</colgroup>
		<thead>
			<tr>
				<th>관리<br/>코드</th>
				<th>업체명 - 지점명</th>
				<th>담당자명</th>
				<th>연락처</th>
				<th>팩스</th>
				<th>업체구분</th>
				<th>밴더할인</th>
				<th>등록일</th>
				<th>영업담당자</th>
				<th class="end">잔액</th>
			</tr>
		</thead>
		<tbody>
		<?
			$nCnt = 0;
			
			if (sizeof($arr_rs) > 0) {
				
				for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
													
					$rn							= trim($arr_rs[$j]["rn"]);
					$CP_NO					= trim($arr_rs[$j]["CP_NO"]);
					$CP_CODE				= trim($arr_rs[$j]["CP_CODE"]);
					$CP_NM					= SetStringFromDB($arr_rs[$j]["CP_NM"]);
					$CP_NM2					= SetStringFromDB($arr_rs[$j]["CP_NM2"]);
					$CEO_NM					= SetStringFromDB($arr_rs[$j]["CEO_NM"]);
					$CP_TYPE				= SetStringFromDB($arr_rs[$j]["CP_TYPE"]);
					$AD_TYPE				= SetStringFromDB($arr_rs[$j]["AD_TYPE"]);
					$SALE_ADM_NO		    = SetStringFromDB($arr_rs[$j]["SALE_ADM_NO"]);
					$MANAGER_NM			    = SetStringFromDB($arr_rs[$j]["MANAGER_NM"]);
					$CP_PHONE				= SetStringFromDB($arr_rs[$j]["CP_PHONE"]);
					$CP_FAX					= SetStringFromDB($arr_rs[$j]["CP_FAX"]);
					$PHONE					= SetStringFromDB($arr_rs[$j]["PHONE"]);
					$DC_RATE				= SetStringFromDB($arr_rs[$j]["DC_RATE"]);
					
					$CONTRACT_START	= trim($arr_rs[$j]["CONTRACT_START"]);
					$CONTRACT_END		= trim($arr_rs[$j]["CONTRACT_END"]);
					$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);

					$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
					$DEL_TF					= trim($arr_rs[$j]["DEL_TF"]);
					
					$CONTRACT_START = date("Y-m-d",strtotime($CONTRACT_START));
					$CONTRACT_END		= date("Y-m-d",strtotime($CONTRACT_END));
					$REG_DATE				= date("Y-m-d",strtotime($REG_DATE));

					$SALE_ADM_NM = getAdminName($conn, $SALE_ADM_NO); 
					
					if($USE_TF == "N")
						$str_use_style = "unused";
					else
						$str_use_style = "";

		?>
			<tr class="<?=$str_use_style ?>" height="40">
				<td class="modeual_nm"><a href="javascript:parent.opener.js_selecting_company('<?=$target_name?>', '<?= $CP_NM ?> <?= $CP_NM2 ?> [<?=$CP_CODE?>]', '<?=$target_value?>', '<?= $CP_NO ?>', {'DC_RATE':'<?=$DC_RATE?>', 'CP_TYPE':'<?=$CP_TYPE?>'}); window.close();">[<?=$CP_CODE?>]</a></td>
				<td class="modeual_nm"><a href="javascript:parent.opener.js_selecting_company('<?=$target_name?>', '<?= $CP_NM ?> <?= $CP_NM2 ?> [<?=$CP_CODE?>]', '<?=$target_value?>', '<?= $CP_NO ?>', {'DC_RATE':'<?=$DC_RATE?>', 'CP_TYPE':'<?=$CP_TYPE?>'}); window.close();"><?= $CP_NM ?> <?= $CP_NM2 ?></a></td>
				<td><?= $MANAGER_NM ?></td>
				<td><?= $CP_PHONE ?></td>
				<td><?= $CP_FAX ?></td>
				<td><?= getDcodeName($conn, "CP_TYPE", $CP_TYPE);?></td>
				<!--<td><?= getDcodeName($conn, "AD_TYPE", $AD_TYPE);?></td>-->
				<td><?= ($DC_RATE != "0" ? $DC_RATE."%" : "") ?></td>
				<td class="filedown"><?= $REG_DATE ?></td>
				<td><?= $SALE_ADM_NM ?></td>
				<td><span class="get_balance" data-cp_no="<?= $CP_NO ?>">클릭하세요</span>
				</td>
			</tr>
		<?			
						}
					} else { 
				?> 
					<tr>
						<td align="center" height="50"  colspan="10">데이터가 없습니다. </td>
					</tr>
				<? 
					}
				?>
		</tbody>
	</table>
	<script>
	$(function(){
		$(".get_balance").click(function(){
			var cp_no = $(this).data("cp_no");
			var clicked_obj = $(this);

			$.getJSON( "../confirm/json_company_ledger.php?cp_no=" + encodeURIComponent(cp_no), function(data) {
				if(data != undefined) { 
					if(data.length == 1) 
						clicked_obj.html(numberFormat(data[0].SUM_BALANCE) + " 원");
					else {
						clicked_obj.html("검색결과가 없습니다.");
					}
				}
			});


		});

	});
	</script>

<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
<div class="bot_close" style="width:auto;"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
</div>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>