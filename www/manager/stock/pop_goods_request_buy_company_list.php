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
	$menu_right = "SG017"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/stock/stock.php";

#====================================================================
# Request Parameter
#====================================================================

	//echo  "&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str."&start_date=".$start_date."&end_date=".$end_date."&con_cp_type=".$con_cp_type."&con_delivery_tf=".$con_delivery_tf."&con_to_here=".$con_to_here."&con_cancel_tf=".$con_cancel_tf."&con_confirm_tf=".$con_confirm_tf."&con_changed_tf=".$con_changed_tf."&con_wrap_tf=".$con_wrap_tf."&con_sticker_tf=".$con_sticker_tf."<br/>";

	$arr_rs = listGoodsRequestDistinctBuyCP2($conn, $start_date, $end_date, $cp_type, $filter, $search_field, $search_str, $order_field, $order_str);


#===============================================================
# Get Search list count
#===============================================================


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title>기프트넷</title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>

<script type="text/javascript">

	function js_link_to_goods_request(cp_no) {

		var frm = document.frm;
		
		window.open("/manager/stock/goods_request_list.php?con_cp_type=" + cp_no + "&start_date=<?=$start_date?>&end_date=<?=$end_date?>" ,'_blank');
		
	}

	function js_link_to_company_ledger(cp_no, max_inout_date) {

		var frm = document.frm;
		
		window.open("/manager/confirm/company_ledger_list.php?cp_type=" + cp_no + "&start_date=<?=$start_date?>&end_date="+max_inout_date+"&show_type=2" ,'_blank');
		
	}
</script>
<style type="text/css">
#postsch_code {padding-right: 30px;}
#temp_scroll { z-index: 1;  overflow: auto; width: 100%; height:550px; border:1px solid #d1d1d1;}
</style>
</head>

<body>

<div>
	<div id="postsch_code">
		<div class="addr_inp">

<form name="frm" method="post">
	<input type="hidden" name="start_date" value="<?=$start_date?>"/>
	<input type="hidden" name="end_date" value="<?=$end_date?>"/>
			<h2>매입업체별 계산서확인</h2>
			<div class="sp10"></div>
			<div style="width:95%;">
				<div style="float:left;">총 <?=sizeof($arr_rs)?> 업체..</div>
				<div style="float:right;">조회 기간 : <?=$start_date?> ~ <?=$end_date?></div>
			</div>
			
			<table cellpadding="0" cellspacing="0" style="width:100%;" class="rowstable">
				<colgroup>
					<col width="7%" />
					<col width="*" />
					<col width="11%" />
					<col width="11%" />
					<col width="11%" />
					<col width="9%" />
					<col width="9%" />
					<col width="9%" />
					<col width="9%" />
				</colgroup>
				<tr>
					<th rowspan="2">
						보기
					</th>
					<th rowspan="2">
						매입업체
					</th>
					<th colspan="3">
						발주 
					</th>
					<th colspan="4">
						원장 
					</th>
				</tr>
				<tr>
					<th>
						총 발주
					</th>
					<th>
						취소
					</th>
					<th>
						유효
					</th>
					
					<th>
						총 기장
					</th>
					<th>
						총 미발행
					</th>
					<th>
						이월(<?=date("n",strtotime("0 month"))?>월)<br/>미발행 기장
					</th>
					<th>
						계산서 미발행
					</th>
					
				</tr>
			</table>
			<div id="temp_scroll">
				<table cellpadding="0" cellspacing="0" style="width:100%;" class="rowstable">
					<colgroup>
						<col width="7%" />
						<col width="*" />
						<col width="11%" />
						<col width="11%" />
						<col width="11%" />
						<col width="9%" />
						<col width="9%" />
						<col width="9%" />
						<col width="9%" />
					</colgroup>

					<?

					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							$BUY_CP_NM				= trim($arr_rs[$j]["BUY_CP_NM"]);
							$BUY_CP_NO				= trim($arr_rs[$j]["BUY_CP_NO"]);
							$TOTAL_GRG				= trim($arr_rs[$j]["TOTAL_GRG"]);
							$CANCEL_GRG				= trim($arr_rs[$j]["CANCEL_GRG"]);
							$TOTAL_CL				= trim($arr_rs[$j]["TOTAL_CL"]);
							$NOT_CONFIRMED_CL		= trim($arr_rs[$j]["NOT_CONFIRMED_CL"]);
							$MAX_INOUT_DATE			= trim($arr_rs[$j]["MAX_INOUT_DATE"]);
							$SAME_MONTH_CNT			= trim($arr_rs[$j]["SAME_MONTH_CNT"]);

							if($NOT_CONFIRMED_CL - $SAME_MONTH_CNT > 0)
								$str_warning = " style='background-color:#ffcdcf; color:red;' ";
							else
								$str_warning = "";

					?>
					<tr height="35">
						<td>
							<?if($NOT_CONFIRMED_CL - $SAME_MONTH_CNT > 0) { ?>
							<a href="javascript:js_link_to_goods_request('<?=$BUY_CP_NO?>');" style="text-decoration:underline;">발주</a>
							<a href="javascript:js_link_to_company_ledger('<?=$BUY_CP_NO?>', '<?=$MAX_INOUT_DATE?>')" style="text-decoration:underline;">원장</a>
							<? } ?>
						</td>
						<td class="modeual_nm">
							<?=$BUY_CP_NM?>
						</td>
						<td>
							<?=$TOTAL_GRG?>
						</td>
						<td>
							<span style="color:red;"><?=$CANCEL_GRG?></span>
						</td>
						<td>
							<span style="color:blue;"><?=$TOTAL_GRG - $CANCEL_GRG?></span>
						</td>
						<td>
							<?=$TOTAL_CL?>
						</td>
						<td>
							<?=$NOT_CONFIRMED_CL?>
						</td>
						<td>
							<?=$SAME_MONTH_CNT?>
						</td>
						<td <?=$str_warning?>>
							<?=$NOT_CONFIRMED_CL - $SAME_MONTH_CNT?>
						</td>
					</tr>
					<?
						}
					} else { 
					?>
					<tr height="50">
						<td colspan="8">
							기간안에 검색된 데이터가 없습니다.
						</td>
					</tr>
					<? 
					} 
					?>
				</table>
			</div>
	</div>
</div>
<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</div>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>