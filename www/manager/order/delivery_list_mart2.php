<?
ini_set('memory_limit',-1);
session_start();
?>
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
	require "../../_common/common_header.php"; 

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/payment/payment.php";
	require "../../_classes/biz/stock/stock.php"; 
	require "../../_classes/biz/company/company.php"; 

	
#====================================================================
# Request Parameter
#====================================================================


#============================================================
# Page process
#============================================================

	$this_date = date("Y-m-d",strtotime("0 month"));
	$prev_date = date("Y-m-d",strtotime("0 month"));
	

	if($end_date == "")
		$end_date = $this_date;

	if($start_date == "")
		$start_date = $prev_date;

#===============================================================
# Get Search list count
#===============================================================

	if($cp_no <> '')
		$arr_rs = listOrderDeliveryForMart_LEVEL1_From_To($conn, $start_date, $end_date, '', $cp_no); //order_state = all

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
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script>
  $(function() {
    $( ".datepicker" ).datepicker({
      buttonImage: "/manager/images/calendar/cal.gif",
      buttonImageOnly: true,
      buttonText: "Select date",
	  showOn: "both",
	  dateFormat: "yy-mm-dd",
	  changeMonth: true,
      changeYear: true
    });

	$(".datepicker").keydown(function(){

		var value = $(this).val();

		if(value.length == 4 && value.lastIndexOf('-') == -1)
			$(this).val(value.substr(0, 4)+ "-" + value.substr(4)) ;

		if(value.length == 7 && value.lastIndexOf('-') == 4)
			$(this).val(value.substr(0, 8) + "-" + value.substr(8)) ;
	});
  });
</script>
<script language="javascript">


	// 조회 버튼 클릭 시 
	function js_search() {
		var frm = document.frm;
		
		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_view(rn, reserve_no) {

		var frm = document.frm;
		
		var url = "../order/order_read.php?reserve_no="+reserve_no;

		NewWindow(url, 'order_detail','860','600','YES');
		
	}

</script>
<style type="text/css">
/*#pop_table {z-index: 1; left: 80; overflow: auto; width: 500; height: 220}*/
#ex_scroll { z-index: 1; background-color:#f7f7f7; overflow: auto; width: 100%; height:155px; border:1px solid #d1d1d1;}
#temp_scroll { z-index: 1; overflow: auto; width: 95%; height:100%; border:1px solid #d1d1d1;}
table.rowstable03 > tbody > tr > td { 
	background:none; 
}
table.rowstable03 > tbody > tr:nth-child(even) {
    background-color:#f7f7f7;
}
</style>
</head>

<body id="admin">
<form name="frm" method="post" enctype="multipart/form-data" action="javascript:js_search();">
<input type="hidden" name="mode" value="">
<input type="hidden" name="total_cnt" value="<?=sizeof($arr_rs)?>">
<input type="hidden" name="order_goods_delivery_no" value="">

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

				<h2>마트 작업 & 출고 리스트</h2>

				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="15%" />
					<col width="30%" />
					<col width="15%" />
					<col width="30%" />
					<col width="10%" />
				</colgroup>
				<tbody>
					<tr>
						<th>주문회사</th>
						<td>
							<?
							    $arr_result = listCompanyBy($conn);
							?>
							<?=makeGenericSelectBox($conn, $arr_result, 'cp_no', '100', '선택', '', $cp_no, 'CP_NO', 'CP_NM')?>
						</td>
						<th>검색일</th>
						<td colspan="2">
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="start_date" value="<?=$start_date?>" maxlength="10"/> ~ 
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="end_date" value="<?=$end_date?>" maxlength="10"/>
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"></a>
						</td>
					</tr>
				</tbody>
			</table>
			
			<div style="width: 95%; text-align: right; margin: 10px 0 10px 0;">
				
			</div>
 
 			<table cellpadding="0" cellspacing="0" class="rowstable03" border="0" style="width:95%" >
				<colgroup>
					<col width="6%" />
					<col width="8%" />
					<col width="35%" />
					<col width="10%" />
					<col width="5%" />
					<col width="6%" />
					<col width="5%" />
					<col width="5%" />
					<col width="*" />
				</colgroup>
				<thead>
					<tr>
						<th>주문자명</th>
						<th>수령자명/주소</th>
						<th>상품명</th>
						<th>스티커명</th>
						<th>출고수</th>
						<th>주문상태</th>
						<th>작업가능</th>
						<th>재고부족</th>
						<th class="end">최종 처리일시</th>
					</tr>
				</thead>
			</table>
			<div id="temp_scroll" style="height:600px;">

				<table cellpadding="0" cellspacing="0" class="rowstable03" border="0" style="width:100%" >
					<colgroup>
						<col width="6%" />
						<col width="8%" />
						<col width="35%" />
						<col width="10%" />
						<col width="5%" />
						<col width="7%" />
						<col width="5%" />
						<col width="5%" />
						<col width="*" />
					</colgroup>
					<tbody>
					<?
					
						$delivery_paper_cnt = 0;

						if (sizeof($arr_rs) > 0) {
							for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
								
								$CP_NO		    = trim($arr_rs[$j]["CP_NO"]);
								$R_ADDR1      = trim($arr_rs[$j]["R_ADDR1"]);
								$R_MEM_NM    = trim($arr_rs[$j]["R_MEM_NM"]);
								$O_MEM_NM    = trim($arr_rs[$j]["O_MEM_NM"]);
								
								/*
								if(strpos($R_ADDR1,'제주시') !== false || strpos($R_ADDR1,'옹진군') !== false || strpos($R_ADDR1,'서귀포시') !== false || strpos($R_ADDR1,'울릉군') !== false)
								{
									$island_color = "style='background-color:yellow;'";
								}else
									$island_color = "";
								*/


					?>
						<tr <?=$island_color?>>
							<td><?=$O_MEM_NM?></td>
							<td><?=$R_MEM_NM?><br/><br/><?=$R_ADDR1?></td>
							
							<td colspan="7">
								<table cellpadding="0" cellspacing="0" class="innertable" border="0" >
								<colgroup>
									<col width="38%" />
									<col width="14%" />
									<col width="6%" />
									<col width="8%" />
									<col width="6%" />
									<col width="6%" />
									<col width="*" />
								</colgroup>
								<tbody>
								<?
									$R_ADDR1      = SetStringToDB(trim($arr_rs[$j]["R_ADDR1"]));
				
									$arr_rs2 = listOrderDeliveryForMart_LEVEL2_From_To($conn, $start_date, $end_date, $CP_NO, $O_MEM_NM, $R_MEM_NM, $R_ADDR1, ''); 
								
									if (sizeof($arr_rs2) > 0) {
										for ($k = 0 ; $k < sizeof($arr_rs2); $k++) {

											//OG.GOODS_OPTION_01, OG.OPT_STICKER_MSG, OG.OPT_WRAP_NO, OG.OPT_PRINT_MSG, OG.OPT_OUTBOX_TF,

											$ORDER_GOODS_NO		   = trim($arr_rs2[$k]["ORDER_GOODS_NO"]);		
											$CP_ORDER_GOODS		   = trim($arr_rs2[$k]["CP_ORDER_GOODS"]);	
											
											$GOODS_NO				= trim($arr_rs2[$k]["GOODS_NO"]);
											$GOODS_CODE				= trim($arr_rs2[$k]["GOODS_CODE"]);
											$GOODS_NAME				= trim($arr_rs2[$k]["GOODS_NAME"]);
											$QTY					= trim($arr_rs2[$k]["QTY"]);
											$GOODS_CNT		        = trim($arr_rs2[$k]["GOODS_CNT"]);
											$RESERVE_NO				= trim($arr_rs2[$k]["RESERVE_NO"]);
											$ORDER_STATE			= trim($arr_rs2[$k]["ORDER_STATE"]);											
											$DELIVERY_CNT_IN_BOX	= trim($arr_rs2[$k]["DELIVERY_CNT_IN_BOX"]);
											
											$GOODS_OPTION_01		    = trim($arr_rs2[$k]["GOODS_OPTION_01"]);
											$GOODS_OPTION_02		    = trim($arr_rs2[$k]["GOODS_OPTION_02"]);
											$GOODS_OPTION_03		    = trim($arr_rs2[$k]["GOODS_OPTION_03"]);

											$WORK_TF			    = trim($arr_rs2[$k]["WORK_TF"]);
											$WORK_DONE_ADM		    = trim($arr_rs2[$k]["WORK_DONE_ADM"]);
											$WORK_DONE_DATE		    = trim($arr_rs2[$k]["WORK_DONE_DATE"]);

											if($WORK_DONE_DATE <> '0000-00-00 00:00:00' && $WORK_DONE_DATE <> NULL)
												$WORK_DONE_DATE = "(".date("n월j일 H시i분",strtotime($WORK_DONE_DATE)).")";

											$GOODS_STATE           = trim($arr_rs2[$k]["CATE_04"]);
											if($GOODS_STATE != '판매중' && $GOODS_STATE != '재판매')
												$style_goods_state = 'style="background-color:red;"';
											else
												$style_goods_state = '';
											
											$fullBoxCnt = floor($QTY /  $DELIVERY_CNT_IN_BOX);
											
											if($ORDER_STATE == "6" || $ORDER_STATE == "7" || $ORDER_STATE == "8" || $ORDER_STATE == "9")
												$other_state = 'style="background-color:lightpink;"';
											else
												$other_state = '';

								?>
								<tr <?=$other_state?> style="border-bottom:1px dotted #f7f7f7;">
									<td <?=$style_goods_state?>>
										<a href="javascript:js_view('<?=$rn?>','<?=$RESERVE_NO?>');"><?=$GOODS_NAME?></a>
									</td>
									<td>
										<!--<a href="javascript:js_view('<?=$rn?>','<?=$RESERVE_NO?>');">-->
										<?=$GOODS_OPTION_02?>
										<!--</a>-->
									</td>
									<td title="구성수:<?=$GOODS_CNT?> / 주문수:<?=$QTY?>"><b><?=$GOODS_CNT * $QTY?></b></td>
									<td><?=getDcodeName($conn, 'ORDER_STATE', $ORDER_STATE)?></td>
									<td style="background-color:lightgreen;">
										<input type="radio" class="work_trigger" name="order_goods_<?=$ORDER_GOODS_NO?>" data-order_goods_no="<?=$ORDER_GOODS_NO?>" data-goods_no="<?=$GOODS_NO?>" data-qty="<?=$QTY?>" <? if($ORDER_STATE == "3") echo 'disabled="disabled"';?> <? if($WORK_TF == "Y") echo 'checked="checked"';?> value="Y"/>
									</td>
									<td style="background-color:LightCoral;">
										<input type="radio" class="work_trigger" name="order_goods_<?=$ORDER_GOODS_NO?>" data-order_goods_no="<?=$ORDER_GOODS_NO?>" data-qty="" data-goods_no="" <? if($ORDER_STATE == "3") echo 'disabled="disabled"';?> <? if($WORK_TF == "N") echo 'checked="checked"';?> value="N"/>
									</td>
									<td title="<?=getAdminName($conn, $WORK_DONE_ADM)?> <?=$WORK_DONE_DATE?>">
										<span style="<?if($WORK_TF == "Y") echo "color:green";?><?if($WORK_TF == "N") echo "color:red";?>"><?=$CP_ORDER_GOODS?></span>
										
									</td>
								</tr>
								
								<?		}
									}
								?>
								</tbody>
								</table>
							</td>
						</tr>

					<?	}
					}
					?>
						</tbody>
					</table>
				</div>
				<script>
					$(function(){
						$(".work_trigger").change(function(event){
							//alert($(this).data("order_goods_no") + " : " + $(this).val());

							event.stopPropagation();

							var order_goods_no = $(this).data("order_goods_no");
							var goods_no = $(this).data("goods_no"); 
							var qty = $(this).data("qty"); 
							var work_tf = $(this).val(); 
							

							(function() {
							  $.getJSON( "/manager/order/json_work.php", {
								mode: "UPDATE_WORK_STATE",
								order_goods_no: order_goods_no,
								work_tf: work_tf,
								goods_no : goods_no,
								qty : qty,
								work_done_adm : <?=$s_adm_no?>
							  })
								.done(function( data ) {
								  $.each( data, function( i, item ) {
									  if(item.RESULT == "0")
										  alert('연결오류 : 잠시후 다시 시도해주세요');
								  });
								});
							})();


						});

					});
				
				</script>
				<div class="sp10"></div>
				<div style="width: 95%; text-align: right; margin: 10px 0 10px 0;">
				</div>
			</div>
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