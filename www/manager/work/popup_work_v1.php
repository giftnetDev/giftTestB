<?session_start();?>
<?
# =============================================================================
# File Name    : popup_work_goods.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2015-11-04
# Modify Date  : 
#	Copyright : Copyright @giftnet Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "WO004"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/work/work.php";
	require "../../_classes/biz/order/order.php";

#====================================================================
# DML Process
#====================================================================
	
	$arr_rs = selectWorkDetail($conn, $order_goods_no);
	
	$j = 0;

	$RESERVE_NO						= trim($arr_rs[$j]["RESERVE_NO"]);
	$ORDER_GOODS_NO				= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
	$ORDER_DATE						= trim($arr_rs[$j]["ORDER_DATE"]);
	$OPT_OUTSTOCK_DATE		= trim($arr_rs[$j]["OPT_OUTSTOCK_DATE"]);
	$CP_NO								= trim($arr_rs[$j]["CP_NO"]);
	$O_MEM_NM							= trim($arr_rs[$j]["O_MEM_NM"]);
	$GOODS_NAME						= trim($arr_rs[$j]["GOODS_NAME"]);
	$GOODS_NO							= trim($arr_rs[$j]["GOODS_NO"]);
	$OPT_MANAGER_NO				= trim($arr_rs[$j]["OPT_MANAGER_NO"]);
	$OPT_MEMO							= trim($arr_rs[$j]["OPT_MEMO"]);
	$WORK_ORDER						= trim($arr_rs[$j]["WORK_ORDER"]);
	$WORK_DATE						= trim($arr_rs[$j]["WORK_DATE"]);
	$BULK_TF							= trim($arr_rs[$j]["BULK_TF"]);
	$GOODS_IMG						= trim($arr_rs[$j]["GOODS_IMG"]);
	$GOODS_PATH_IMG				= trim($arr_rs[$j]["GOODS_PATH_IMG"]);
	$QTY									= trim($arr_rs[$j]["QTY"]);
	$OPT_STICKER_NO				= trim($arr_rs[$j]["OPT_STICKER_NO"]);
	$OPT_OUTBOX_TF				= trim($arr_rs[$j]["OPT_OUTBOX_TF"]);
	$OPT_WRAP_NO					= trim($arr_rs[$j]["OPT_WRAP_NO"]);
	$OPT_PRINT_MSG				= trim($arr_rs[$j]["OPT_PRINT_MSG"]);
	$OPT_STICKER_MSG			= trim($arr_rs[$j]["OPT_STICKER_MSG"]);
	$WORK_NO							= trim($arr_rs[$j]["WORK_NO"]);
	$WORK_LINE						= trim($arr_rs[$j]["WORK_LINE"]);
	$WORK_TYPE						= trim($arr_rs[$j]["WORK_TYPE"]);
	$WORK_FLAG						= trim($arr_rs[$j]["WORK_FLAG"]);
	$WORK_QTY					 	= trim($arr_rs[$j]["WORK_QTY"]);

	$refund_able_qty = getRefundAbleQty($conn, $RESERVE_NO, $ORDER_GOODS_NO);

	//if ($GOODS_PATH_IMG <> "" ) {
	//	$GOODS_PATH_IMG = str_replace("/upload_data/goods_image/", "", $GOODS_PATH_IMG);
	//}

	$GOODS_IMG = getImage($conn, $GOODS_NO, "170", "170"); //$GOODS_PATH_IMG.$GOODS_IMG;

	$arr_work = selectWork($conn, $ORDER_GOODS_NO);
	//초기화
	$case_work_no			= "";
	$case_flag				= "";
	$wrap_work_no			= "";
	$wrap_flag				= "";
	$sticker_work_no		= "";
	$sticker_flag			= "";
	$out_work_no			= "";
	$out_flag				= "";

	if (sizeof($arr_work) > 0) {
		for ($k = 0 ; $k < sizeof($arr_work); $k++) {
			
			$RS_WORK_NO			= trim($arr_work[$k]["WORK_NO"]);
			$RS_WORK_TYPE		= trim($arr_work[$k]["WORK_TYPE"]);
			$RS_WORK_FLAG		= trim($arr_work[$k]["WORK_FLAG"]);
			$RS_CONFIRM_ADM	= trim($arr_work[$k]["CONFIRM_ADM"]);

			//echo $RS_WORK_TYPE;
			
			if (trim($RS_WORK_TYPE) == "INCASE") {
				$case_work_no	= $RS_WORK_NO;
				$case_flag		= $RS_WORK_FLAG;
			}

			if (trim($RS_WORK_TYPE) == "WRAP") {
				$wrap_work_no	= $RS_WORK_NO;
				$wrap_flag		= $RS_WORK_FLAG;
			}

			if (trim($RS_WORK_TYPE) == "STICKER") {
				$sticker_work_no	= $RS_WORK_NO;
				$sticker_flag		= $RS_WORK_FLAG;
			}

			if (trim($RS_WORK_TYPE) == "OUTCASE") {
				$out_work_no	= $RS_WORK_NO;
				$out_flag		= $RS_WORK_FLAG;
			}
		}
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->

<script type="text/javascript">


</script>
<style>
	#popupwrap_work h1 {width:100%;}
	body#popup_file {width:100%;} 
	#popupwrap_work {width:100%;} 
	.sub_title_row > td {text-align:center; font-weight:bold;}
	.img_row > td {text-align:center; height:200px; vertical-align:middle;}
</style>

</head>
<body id="popup_file">

<form name="frm" method="post" enctype="multipart/form-data">

<div id="popupwrap_work">
	<h1><?=$GOODS_NAME?></h1>
	<br>
	<div id="postsch_code">
		<table cellpadding="0" cellspacing="0" width="98%">
			<colgroup>
			<col width="33%" />
			<col width="33%" />
			<col width="34%" />
			</colgroup>
			<?
				$arr_incase = getOrderGoodsSub($conn, $GOODS_NO, "INCASE");
				$arr_outcase = getOrderGoodsSub($conn, $GOODS_NO, "OUTCASE");

				$incase_img  = getImage($conn, $arr_incase[0]["GOODS_NO"], "", "");
				$sticker_img = getImage($conn, $OPT_STICKER_NO, "", "");
				$wrap_img	 = getImage($conn, $OPT_WRAP_NO, "", "");
				$outcase_img = getImage($conn, $arr_outcase[0]["GOODS_NO"], "", "");
			?>
			<tr class="sub_title_row">
				<td>[상품이미지]</td>
				<td>[인박스]</td>
				<td>[스티커]</td>
			</tr>
			<tr class="img_row">
				<td>
					<img src="<?=$GOODS_IMG?>" width="300" >
				</td>
				<td>
					<? if ($case_work_no) { ?>
					<?  if ($incase_img) { ?>
					<img src="<?=$incase_img?>" width="300">
					<?  } else { ?>
					이미지 미등록
					<?  } ?>
					<? } else { ?>
						없음
					<? } ?>
				</td>
				<td>
					<? if ($sticker_work_no) { ?>
					<? if ($sticker_img) { ?>
					<img src="<?=$sticker_img?>" width="300">
					<? } else { ?>
					이미지 미등록
					<? } ?>
					<? } else { ?>
						없음
					<? } ?>
					<br><br><b><?=$OPT_STICKER_MSG?></b>
				</td>
			</tr>
			<tr>
				<td colspan="3" style="height:20px"></td>
			</tr>
			<tr class="sub_title_row">
				<td>[포장지]</td>
				<td>[아웃박스]</td>
				<td>[주문수량/작업수량]</td>
			</tr>
			<tr class="img_row">
				<td>
					<? if ($wrap_work_no) { ?>
					<? if ($wrap_img) { ?>
					<img src="<?=$wrap_img?>" width="300">
					<? } else { ?>
					이미지 미등록
					<? } ?>
					<? } else { ?>
						없음
					<? } ?>
				</td>
				<td>
					<? if ($out_work_no) { ?>
					<? if ($outcase_img) { ?>
					<img src="<?=$outcase_img?>" width="300">
					<? } else { ?>
					이미지 미등록
					<? } ?>
					<? } else { ?>
						없음
					<? } ?>
				</td>
				<td>
					<span style="font-size:50px;">수량 <?=number_format($refund_able_qty - $WORK_QTY)?> 개</span>
				</td>
			</tr>
			<tr>
				<td colspan="3" style="height:20px"></td>
			</tr>
			<tr class="sub_title_row">
				<td>[구성품]</td>
				<td></td>
				<td></td>
			</tr>
			<tr class="img_row">
				<td>
					<?
						// 구성품 정보 가지고 오기 
						$arr_goods_sub =selectGoodsSub($conn, $GOODS_NO);
					?>
					<div style="width:100%;text-align:left; padding:10px 5px 5px 10px">
					<?
						if (sizeof($arr_goods_sub) > 0) {
							for ($jk = 0 ; $jk < sizeof($arr_goods_sub); $jk++) {
								$sub_goods_name			= trim($arr_goods_sub[$jk]["GOODS_NAME"]);
								$sub_goods_cnt			= trim($arr_goods_sub[$jk]["GOODS_CNT"]);
								echo $sub_goods_name."&nbsp;&nbsp;<font color='red'>(<b>".$sub_goods_cnt."</b>)</font><br>";
							}
						}
					?>
				</td>
				<td>
					
				</td>
				<td>
					
				</td>
			</tr>

		</table>
	</div>
	<div class="sp20"></div>
</form>
</body>
</html>
<?
#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);
?>