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
	$menu_right = "SG024"; // 메뉴마다 셋팅 해 주어야 합니다

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
	$delivery_no	= trim($delivery_no);
	$barcode	= trim($barcode);
	
	
	//echo $pb_nm; 
	//echo $mode;
	
	$stock_no				= SetStringToDB($stock_no);
	$stock_type				= SetStringToDB($stock_type);
	$stock_code				= SetStringToDB($stock_code);
	$cp_type				= SetStringToDB($cp_type);
	$out_cp_no				= SetStringToDB($out_cp_no);
	$goods_no				= SetStringToDB($goods_no);
	$goods_code				= SetStringToDB($goods_code);
	$in_loc					= SetStringToDB($in_loc);
	$in_loc_ext				= SetStringToDB($in_loc_ext);
	$qty					= SetStringToDB($qty);
	$buy_price				= SetStringToDB($buy_price);
	$out_date				= SetStringToDB($out_date);
	$pay_date				= SetStringToDB($pay_date);
	
	$result	= false  ;

#====================================================================
# DML Process
#====================================================================

//echo $delivery_no; 
//echo $mode;

	$is_correct = 'N';
	
	if ($mode == "R") {
		//echo "PREV_DELIVERY_NO : ".$prev_delivery_no."<br/>";
		$result_reset = resetOrderGoodsScan($conn, $prev_delivery_no);

	}

	if ($mode == "U") {
		if($delivery_no <> "" && $barcode <> "") {

			if (!(in_array($barcode, $rs_kancode) || in_array($barcode, $rs_kancode_box)) ) { 
		
				$msg = "주문받은 상품이 아닙니다.";
		
			} else {

				for($i= 0; $i < sizeof($rs_goods_no); $i++) {

					$temp_goods_no			  = $rs_goods_no[$i];
					$temp_goods_code		  = $rs_goods_code[$i];
					$temp_kancode			  = $rs_kancode[$i];
					$temp_kancode_box		  = $rs_kancode_box[$i];
					$temp_reserve_no		  = $rs_reserve_no[$i];
					$temp_cp_no				  = $rs_cp_no[$i];
					$temp_delivery_cnt_in_box = $rs_delivery_cnt_in_box[$i];
					$temp_delivery_goods_seq  = $rs_delivery_goods_seq[$i];
					$temp_completed			  = $rs_completed[$i];

					if($temp_kancode == $barcode && $temp_completed == "N") {

						//echo "낱개 매치! ";
						$qty = 1;
						updateOrderGoodsByDeliveryNo($conn, $delivery_no, $temp_delivery_goods_seq, $barcode, $qty);

						insertOrderGoodsScanHistory($conn, $delivery_no, $temp_delivery_goods_seq, $barcode, $qty, $s_adm_no);

						$is_correct = 'Y'; 

						break;

					} else if ($temp_kancode_box == $barcode && $temp_completed == "N") {

						//echo "박스 매치! ";
						$qty = $temp_delivery_cnt_in_box;
						updateOrderGoodsByDeliveryNo($conn, $delivery_no, $temp_delivery_goods_seq, $barcode, $qty);

						insertOrderGoodsScanHistory($conn, $delivery_no, $temp_delivery_goods_seq, $barcode, $qty, $s_adm_no);

						$is_correct = 'Y';
						break;
					}
				}
			}
		} else {
			//alert(" ");
		}

	}

	if ($mode == "C") {

		undoOrderGoodsScanHistory($conn, $delivery_no, $s_adm_no);

	}


	if ($delivery_no != "") {
		$arr_rs = listOrderGoodsByDeliveryNo($conn, $delivery_no);

		//$arr_order_rs = selectOrderDelivery($conn, "", $delivery_no);
		$arr_order_rs = selectOrderDeliveryPaper($conn, "", $delivery_no);

		$rs_delivery_seq	        = trim($arr_order_rs[0]["DELIVERY_SEQ"]); 
		$rs_delivery_no 		    = trim($arr_order_rs[0]["DELIVERY_NO"]);
		$rs_delivery_cp				= trim($arr_order_rs[0]["DELIVERY_CP"]);
		$rs_order_nm		        = trim($arr_order_rs[0]["ORDER_NM"]); 
		$rs_order_phone		        = trim($arr_order_rs[0]["ORDER_PHONE"]);
		$rs_order_hphone		    = trim($arr_order_rs[0]["ORDER_HPHONE"]);
		$rs_receiver_nm		        = trim($arr_order_rs[0]["RECEIVER_NM"]); 
		$rs_receiver_phone		    = trim($arr_order_rs[0]["RECEIVER_PHONE"]);
		$rs_receiver_hphone		    = trim($arr_order_rs[0]["RECEIVER_HPHONE"]);
		$rs_receiver_addr			= trim($arr_order_rs[0]["RECEIVER_ADDR"]); 
		$rs_goods_delivery_name	    = trim($arr_order_rs[0]["GOODS_DELIVERY_NAME"]); 
		$rs_memo				    = trim($arr_order_rs[0]["MEMO"]); 
		$rs_goods_delivery_name		= trim($arr_order_rs[0]["GOODS_DELIVERY_NAME"]); 
		$rs_delivery_profit_code	= trim($arr_order_rs[0]["DELIVERY_PROFIT_CODE"]); 
		$rs_delivery_fee_code		= trim($arr_order_rs[0]["DELIVERY_FEE_CODE"]); 
		$rs_delivery_claim_code		= trim($arr_order_rs[0]["DELIVERY_CLAIM_CODE"]); 
		$rs_delivery_date           = trim($arr_order_rs[0]["DELIVERY_DATE"]); 
		$rs_outstock_tf             = trim($arr_order_rs[0]["OUTSTOCK_TF"]); 

	}

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
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script>
  $(function() {
    $( ".datepicker" ).datepicker({
      showOn: "button",
      buttonImage: "/manager/images/calendar/cal.gif",
      buttonImageOnly: true,
      buttonText: "Select date",
	  showOn: "both",
	  dateFormat: "yy-mm-dd",
	  changeMonth: true,
      changeYear: true
    });
  });

  	function js_clear() {
		location.href = "<?=$_SERVER[PHP_SELF]?>";
	}

	function js_cancel() {
		var frm = document.frm;
		
		frm.mode.value = "C";
		frm.target = "";
		frm.delivery_no.value = frm.prev_delivery_no.value;
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_reset() {
		var frm = document.frm;
		
		frm.mode.value = "R";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}
</script>

</head>
<body id="admin">

<? if($delivery_no <> "" && $barcode <> "" && $is_correct == 'N' && $barcode != 'CANCEL' && $barcode != 'RESET' && $barcode != 'CLEAR') { ?>
	<embed src='http://www.giftace.or.kr/manager/sound/wrong1.wav' hidden='true' autostart='true' loop='false' />
<? } ?>

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="">
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
	include_once('../../_common/editor/func_editor.php');

?>

		</td>
		<td class="contentarea">

			<!-- S: mwidthwrap -->
			<div id="mwidthwrap">

				<div class="title_with_submenu">
					<h2 class="title">출고 등록 - 작업장</h2>  
					<!--
					<div class="right_menu">	
						<b>입고등록으로</b>
						<span name="kancode_toInStock"></span>
						<script>
							$(function(){
								$("span[name=kancode_toInStock]").barcode("INSTOCK", "code128", {output:'bmp', barHeight:30});  
							});
						</script>
						 INSTOCK
					</div>
					-->
					<div class="sp5"></div>
				</div>

				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="16%">
					<col width="34%">
					<col width="16%">
					<col width="34%">
				</colgroup>
				<tr>
					<th>송장번호</th>
					<td class="line">
						<input type="Text" name="delivery_no" value="<?=$delivery_no?>" style="ime-mode:disabled;"  class="txt">
					</td>
					<th>입력 바코드</th>
					<td class="line">
						<input type="Text" name="barcode" value="" style="ime-mode:disabled;" class="txt">
					</td>
				</tr>
				<tr>
					<th>화면 초기화</th>
					<td>	
						<span name="kancode_clear"></span>
						<script>
							$(function(){
								$("span[name=kancode_clear]").barcode("CLEAR", "code128", {output:'bmp', barHeight:30});  
							});
						</script>
						 CLEAR
						 <input type="button" name="bb" onclick="javascript:js_clear();"  value="초기화"/> 
					</td>
					<th>이전 스캔 취소</th>
					<td>
						<span name="kancode_cancel"></span>
						<script>
							$(function(){
								$("span[name=kancode_cancel]").barcode("CANCEL", "code128", {output:'bmp', barHeight:30});  
							});
						</script>
						 CANCEL
						 <input type="button" name="bb" onclick="javascript:js_cancel();"  value="방금전 입력취소"/>
					</td>
				</tr>
				<tr>
					<th>송장 취소</th>
					<td>	
						<span name="kancode_reset"></span>
						<script>
							$(function(){
								$("span[name=kancode_reset]").barcode("RESET", "code128", {output:'bmp', barHeight:30});  
							});
						</script>
						 RESET
						 <input type="button" name="bb" onclick="javascript:js_reset();"  value="송장입력취소"/>
					</td>
					<td colspan="2">
						<div class="reset_confirm">
							<span style="">이 송장에 관해 스캔한 내용이 전부 취소됩니다 진행하시겠습니까? </span>
							<span name="kancode_yes"></span>
								<script>
									$(function(){
										$("span[name=kancode_yes]").barcode("YES", "code128", {output:'bmp', barHeight:30});  
									});
								</script>
								 YES
						</div>
					</td>
				</tr>
				</table>
				<script>
					$(function(){

						$("input[name=delivery_no]").keydown(function(event){
							if(event.keyCode == 13)
							{
								if($(this).val() == "CLEAR")
								{
									<?
										$delivery_no = "";
									?>
									location.href = "<?=$_SERVER[PHP_SELF]?>";
					
								} else if($(this).val() == "INSTOCK")
								{
									location.href = "/manager/stock/in_write_barcode.php";
																
								} else if($(this).val() == "CANCEL")
								{
									<?
										//$delivery_no = "";
									?>
									//$("input[name=delivery_no]").val('').focus();

									var frm = document.frm;

									frm.mode.value = "C";
									frm.target = "";
									frm.delivery_no.value = frm.prev_delivery_no.value;
									frm.action = "<?=$_SERVER[PHP_SELF]?>";
									frm.submit();
								
								
								} else if($(this).val() == "RESET")
								{
									$(".reset_confirm").show();
									$("input[name=delivery_no]").val('').focus();

								} else if($(this).val() == "YES")
								{
									var frm = document.frm;

									frm.mode.value = "R";
									frm.target = "";
									frm.action = "<?=$_SERVER[PHP_SELF]?>";
									frm.submit();
								
								} else {

									var frm = document.frm;

									//alert($("input[name=delivery_no]").val());
									frm.prev_delivery_no.value = $("input[name=delivery_no]").val();
									frm.target = "";
									frm.action = "<?=$_SERVER[PHP_SELF]?>";
									frm.submit();
								}
							}
						});

						if($("input[name=delivery_no]").val() != "")
							$("input[name=barcode]").focus();
						else
							$("input[name=delivery_no]").focus();

						$("input[name=barcode]").keydown(function(event){
							if(event.keyCode == 13)
							{

								if($(this).val() == "CLEAR")
								{
									<?
										$delivery_no = "";
									?>
									location.href = "<?=$_SERVER[PHP_SELF]?>";
								
								} else if($(this).val() == "CANCEL")
								{
									var frm = document.frm;

									frm.mode.value = "C";
									frm.target = "";
									frm.action = "<?=$_SERVER[PHP_SELF]?>";
									frm.submit();
								
								} else if($(this).val() == "RESET")
								{
									$(".reset_confirm").show();
									$("input[name=delivery_no]").val('').focus();

								} else if($(this).val() == "YES")
								{
									var frm = document.frm;

									frm.mode.value = "R";
									frm.target = "";
									frm.action = "<?=$_SERVER[PHP_SELF]?>";
									frm.submit();

								} else {

									var frm = document.frm;

									frm.mode.value = "U";
									frm.target = "";
									frm.action = "<?=$_SERVER[PHP_SELF]?>";
									frm.submit();
								}
							}
						});

						
					});
				</script>
				<div class="sp10"></div>
				<table cellpadding="0" cellspacing="0" width="100%" class="colstable02">

					<colgroup>
						<col width="15%" />
						<col width="35%" />
						<col width="15%" />
						<col width="35%" />
					</colgroup>
						<tr>
							<th>출고번호</th>
							<td class="line">
								<?=$rs_delivery_seq?>
							</td>
							<th>택배사/송장번호</th>
							<td class="line">
								<?=$rs_delivery_cp?> <?=$rs_delivery_no?>
							</td>
						</tr>
						<tr>
							<th>송장내용</th>
							<td class="line" colspan="3">
								<?=$rs_goods_delivery_name?>
							</td>
						</tr>
						<tr>
							<th>주문자명</th>
							<td class="line">
								<?=$rs_order_nm?>
							</td>
							<th>수령자명</th>
							<td class="line">
								<?=$rs_receiver_nm?>
							</td>
						</tr>						
						<tr>
							<th>주소</th>
							<td class="line" colspan="3">
								<?=$rs_receiver_addr?>
							</td>
						</tr>
					</table>
				<div class="sp10"></div>
				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="10%">
					<col width="10%">
					<col width="*">
					<col width="10%">
					<col width="10%">
					<col width="10%">
					<col width="15%">
					<col width="15%">
				</colgroup>
				<thead>
					<th>상품이미지</th>
					<th>상품코드</th>
					<th>상품명</th>
					<th>스티커</th>
					<th>총 수량</th>
					<th>스캔수량</th>
					<th>낱개바코드</th>
					<th>박스바코드</th>
				</thead>
				<?
					$chkComplete = 0;
					if (sizeof($arr_rs) > 0) {
				
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
					
						$GOODS_NO				= trim($arr_rs[$j]["GOODS_NO"]);
						$GOODS_CATE				= trim($arr_rs[$j]["GOODS_CATE"]);
						$GOODS_CODE				= trim($arr_rs[$j]["GOODS_CODE"]);
						$KANCODE				= trim($arr_rs[$j]["KANCODE"]);
						$KANCODE_BOX			= trim($arr_rs[$j]["KANCODE_BOX"]);
						$GOODS_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
						$GOODS_SUB_NAME			= SetStringFromDB($arr_rs[$j]["GOODS_SUB_NAME"]);
						$IMG_URL				= trim($arr_rs[$j]["IMG_URL"]);
						$FILE_NM				= trim($arr_rs[$j]["FILE_NM_100"]);
						$FILE_RNM_150			= trim($arr_rs[$j]["FILE_RNM_150"]);
						$FILE_PATH_150			= trim($arr_rs[$j]["FILE_PATH_150"]);
						$STICKER_NO				= trim($arr_rs[$j]["STICKER_NO"]);

						$RESERVE_NO				= trim($arr_rs[$j]["RESERVE_NO"]);
						$CP_NO					= trim($arr_rs[$j]["CP_NO"]);

						$GOODS_TOTAL			= trim($arr_rs[$j]["GOODS_TOTAL"]);
						$SCAN_CNT				= trim($arr_rs[$j]["SCAN_CNT"]);

						$DELIVERY_CNT_IN_BOX	= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);
						$DELIVERY_GOODS_SEQ     = trim($arr_rs[$j]["DELIVERY_GOODS_SEQ"]);

						$img_url	= getGoodsImage($FILE_NM, $IMG_URL, $FILE_PATH_150, $FILE_RNM_150, "50", "50");

						$sticker_img		= getImage($conn, $STICKER_NO, "", "");
						$sticker_name		= getGoodsName($conn, $STICKER_NO);

						if($GOODS_TOTAL == $SCAN_CNT)
						{
							$chkComplete ++;
							$str_full_class = "green";
						}
						else if($GOODS_TOTAL < $SCAN_CNT)
						{
							$chkComplete ++;
							$str_full_class = "red";
						}
						else 
						{
							$chkComplete --;
							$str_full_class = "";
						}
				
				?>
				<tr class="<?=$str_full_class?>">
					<td><img src="<?=$img_url?>" width="50" height="50"></a><?=$DELIVERY_GOODS_SEQ?>
						<input type="hidden" name="rs_goods_no[]" value="<?=$GOODS_NO?>" />

						<input type="hidden" name="rs_reserve_no[]" value="<?=$RESERVE_NO?>" />
						<input type="hidden" name="rs_cp_no[]" value="<?=$CP_NO?>" />
						<input type="hidden" name="rs_delivery_cnt_in_box[]" value="<?=$DELIVERY_CNT_IN_BOX?>" />
						<input type="hidden" name="rs_delivery_goods_seq[]" value="<?=$DELIVERY_GOODS_SEQ?>" />
						<input type="hidden" name="rs_goods_code[]" value="<?=$GOODS_CODE?>" />
						<input type="hidden" name="rs_kancode[]" value="<?=$KANCODE?>" />
						<input type="hidden" name="rs_kancode_box[]" value="<?=$KANCODE_BOX?>" />
						<input type="hidden" name="rs_completed[]" value="<?=$GOODS_TOTAL == $SCAN_CNT ? "Y" : "N"?>" />
					</td>
					<td class="line modeual_nm"><?=$GOODS_CODE?></td>
					<td class="line modeual_nm"><?=$GOODS_NAME?></td>
					<td class="line modeual_nm">
												<? if ($STICKER_NO) { ?>
												<img src="<?=$sticker_img?>" style="max-width:80px; max-height:80px;"><br/>
												<font><?=$sticker_name?></font>
												<? } else { ?>
												<font color="#AFAFAF">없음</font>
												<? } ?>
					</td>
					<td class="line" align="center"><?=$GOODS_TOTAL?></td>
					<td class="line" align="center"><?=$SCAN_CNT?></td>
					<td class="line" align="center">
						<?=$KANCODE?>
						<!--
						<?  if($GOODS_TOTAL > $SCAN_CNT) { ?>
						<span name="kancode_<?=$GOODS_CODE?>"></span>
						<script>
							$(function(){
								$("span[name=kancode_<?=$GOODS_CODE?>]").barcode("<?=$KANCODE?>", "code128", {output:'bmp', barHeight:30});  
							});
						</script>
						
						<br/><?=$KANCODE?> (1 개)
						<? } else if($GOODS_TOTAL == $SCAN_CNT) { ?>
									완료
						<? } else { ?>
									초과 (<?= $SCAN_CNT - $GOODS_TOTAL?> 개 초과)
						<? } ?>
						-->
					</td>
					<td class="line" align="center">
						<?=$KANCODE_BOX?>
						<!--
						<?  if($GOODS_TOTAL > $SCAN_CNT && $GOODS_TOTAL >= $DELIVERY_CNT_IN_BOX) { ?>
							<span name="kancode_box_<?=$GOODS_CODE?>"></span>
							<script>
								$(function(){
									$("span[name=kancode_box_<?=$GOODS_CODE?>]").barcode("<?=$KANCODE_BOX?>", "code128", {output:'bmp', barHeight:30});  
								});
							</script>
							
							<br/><?=$KANCODE_BOX?> (<?=$DELIVERY_CNT_IN_BOX?> 개)
						<? } ?>
						-->
					</td>
				</tr>
				<?
						}
					} else {
						//echo sizeof($arr_order_rs)." ".$delivery_no;
						if (sizeof($arr_order_rs) == 0 && $delivery_no <> "") { 
				?>
				<!-- 검색결과 없음 -->
				<tr><td colspan="7" height="35" align="center"><b>(<?=$delivery_no?>) 유효한 송장인지 확인바랍니다.</b></td></tr>
				<script>
					$(function(){
						$("input[name=delivery_no]").val('').focus();
					});
				</script>
				<?		$delivery_no = "";
						}
					} 
				?>

				<!-- 전체 완료-->
				<? if($chkComplete == sizeof($arr_rs) && $chkComplete != 0) { ?>
				<tr><td colspan="7" height="35" align="center"><b>모두 완료되었습니다</b></td></tr>
				<script>
					$(function(){
						$("input[name=delivery_no]").val('').focus();
					});
				</script>
				<embed src='http://www.giftace.or.kr/manager/sound/completed.wav' hidden='true' autostart='true' loop='false' />
				<?  
					} 
				?>

				<!-- 송장 취소 -->
				<? if($result_reset) { ?>
				<tr><td colspan="7" height="35" align="center"><b>송장이 취소 되었습니다</b></td></tr>
				<script>
					$(function(){
						$("input[name=delivery_no]").val('').focus();
					});
				</script>
				<?  
					} 
				?>
				
				</table>
					
			   
			 </div>
			<!-- // E: mwidthwrap -->
	    </td>
  </tr>
  </table>
</div>


<? if($is_correct == 'Y' && $chkComplete != sizeof($arr_rs)) { ?>
	<embed src='http://www.giftace.or.kr/manager/sound/Bing-sound.mp3' hidden='true' autostart='true' loop='false' />
<? } ?>

<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>