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
	require "../../_common/common_header.php"; 

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/util/ImgUtil.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/work/work.php";
	
	if ($s_adm_cp_type == "구매" || $s_adm_cp_type == "판매공급" ) { 
		$cp_type2 = $s_adm_com_code;
	}

	if ($s_adm_cp_type == "판매") { 
		$cp_type = $s_adm_com_code;
	}

	$work_type_1		= trim($work_type_1);
	$work_type_2		= trim($work_type_2);
	$work_type_3		= trim($work_type_3);
	$work_type_4		= trim($work_type_4);
	$work_type_5		= trim($work_type_5);

	$lineA		= trim($lineA);
	$lineB		= trim($lineB);
	$lineC		= trim($lineC);

	$submit_flag	= trim($submit_flag);

	if ($submit_flag == "") {

		$work_type_1		= "Y";
		$work_type_2		= "Y";
		$work_type_3		= "Y";
		$work_type_4		= "Y";
		$work_type_5		= "Y";

		$lineA		= "Y";
		$lineB		= "Y";
		$lineC		= "Y";
	}

	$arr_work_type = $work_type_1."|".$work_type_2."|".$work_type_3."|".$work_type_4."|".$work_type_5;
	$arr_work_line = $lineA."|".$lineB."|".$lineC;


#====================================================================
# Request Parameter
#====================================================================

	if ($work_date == "") {
		$work_date = date("Y-m-d",strtotime("0 day"));
	} else {
		$work_date = trim($work_date);
	}


	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$con_cate_01 = trim($con_cate_01);
	$con_cate_02 = trim($con_cate_02);
	$con_cate_03 = trim($con_cate_03);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
	$del_tf = "N";
#============================================================
# Page process
#============================================================

	if ($nPage <> "") {
		$nPage = (int)($nPage);
	} else {
		$nPage = 1;
	}

	if ($nPageSize <> "") {
		$nPageSize = (int)($nPageSize);
	} else {
		$nPageSize = 2000;
	}

	$nPageBlock	= 10;
	
#	echo $start_date;
#	echo $end_date;

#===============================================================
# Get Search list count
#===============================================================

	$arr_rs = listWorkList($conn, $order_type, $work_date, $arr_work_type, $arr_work_line, "Y", "N", $search_field, $search_str, $nPage, $nPageSize);

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

<style>
	table.rowstable {width:100%} 
	table.rowstable th {font-size: 18px; line-height: 26px; font-weight:bold; padding: 5px;}
	table.rowstable td {font-size: 18px; line-height: 26px; font-weight:bold; padding: 5px;}
</style>
</head>

<body>

<form name="frm" method="post">

	<?
		$nCnt = 0;
		
		if (sizeof($arr_rs) > 0) {
			for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

				$RESERVE_NO						= trim($arr_rs[$j]["RESERVE_NO"]);
				$ORDER_GOODS_NO				= trim($arr_rs[$j]["ORDER_GOODS_NO"]);

				if($ORDER_GOODS_NO != $order_goods_no)
					continue;

				$ORDER_DATE						= trim($arr_rs[$j]["ORDER_DATE"]);
				$OPT_OUTSTOCK_DATE		 = trim($arr_rs[$j]["OPT_OUTSTOCK_DATE"]);
				$CP_NO								 = trim($arr_rs[$j]["CP_NO"]);
				$O_MEM_NM						  = trim($arr_rs[$j]["O_MEM_NM"]);
				$R_MEM_NM						  = trim($arr_rs[$j]["R_MEM_NM"]);
				$CATE_01						   = trim($arr_rs[$j]["CATE_01"]);
				$GOODS_NAME						= trim($arr_rs[$j]["GOODS_NAME"]);
				$GOODS_NO					   	  = trim($arr_rs[$j]["GOODS_NO"]);
				$OPT_MANAGER_NO				= trim($arr_rs[$j]["OPT_MANAGER_NO"]);
				$OPT_MEMO						  = trim($arr_rs[$j]["OPT_MEMO"]);
				$WORK_ORDER						= trim($arr_rs[$j]["WORK_ORDER"]);
				$WORK_DATE						 = trim($arr_rs[$j]["WORK_DATE"]);
				$BULK_TF						   = trim($arr_rs[$j]["BULK_TF"]);
				$refund_able_qty = getRefundAbleQty($conn, $RESERVE_NO, $ORDER_GOODS_NO);
			
				//전체취소건은 제외
				if($refund_able_qty == 0) 
					continue;

				$OPT_STICKER_NO				= trim($arr_rs[$j]["OPT_STICKER_NO"]);
				$OPT_OUTBOX_TF				 = trim($arr_rs[$j]["OPT_OUTBOX_TF"]);
				$OPT_WRAP_NO				   = trim($arr_rs[$j]["OPT_WRAP_NO"]);
				$OPT_PRINT_MSG				 = trim($arr_rs[$j]["OPT_PRINT_MSG"]);
				$OPT_STICKER_MSG		   = trim($arr_rs[$j]["OPT_STICKER_MSG"]);
				$WORK_LINE						 = trim($arr_rs[$j]["WORK_LINE"]);
				$WORK_QTY					 	  = trim($arr_rs[$j]["WORK_QTY"]);
				$WORK_SEQ					 	  = trim($arr_rs[$j]["WORK_SEQ"]);
				$DELIVERY_CNT_IN_BOX   = trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);
				$WORK_MSG						  = trim($arr_rs[$j]["WORK_MSG"]);
				

				if($CATE_01 <> "")
					$GOODS_NAME = $CATE_01.")".$GOODS_NAME;

				$GOODS_IMG = getImage($conn, $GOODS_NO, "280", "280");

				// 구성품 정보 가지고 오기 
				$arr_goods_sub = selectGoodsSub($conn, $GOODS_NO);

	?>
	<table cellpadding="0" cellspacing="0" class="rowstable" border="0">
		<colgroup>
			<col width="8%" />
			<col width="22%" />
			<col width="20%" />
			<col width="8%" />
			<col width="*" />
			<col width="10%"/>
		</colgroup>
		<thead>
			<tr>
				<th>주문일자</th>
				<th>주문업체</th>
				<th>주문자</th>
				<th>수령자</th>
				<th>상품명</th>
				<th class="end">영업담당</th>
			</tr>
		</thead>

		<tbody>
				<tr>
					<td style="">
						<?= date("n월j일",strtotime($WORK_DATE))?>
					</td>
					<td style="text-align:center"><?= getCompanyName($conn, $CP_NO) ?></td>
					<td class="modeual_nm"><?= $O_MEM_NM?></td>
					<td class="modeual_nm"><?= $R_MEM_NM?></td>
					<td class="modeual_nm"><a href="javascript:js_view('<?=$RESERVE_NO?>');"><?=$GOODS_NAME?></a></td>
					<td style="text-align:center"><?=getAdminName($conn,$OPT_MANAGER_NO);?></td>
				</tr>
				<tr style="background:#EFEFEF">
					<td>
						<b><?=$WORK_SEQ?> 번</b> 
					</td> 
					<td colspan="2">
						<img src="<?=$GOODS_IMG?>">
					</td>
					<td colspan="2" style="text-align:left; font-size:19px; line-height:26px; font-weight:bold;">
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
					<td class="price" style="font-size:22px; line-height:28px; font-weight:bold; text-align:center;"><b><?=number_format($refund_able_qty - $WORK_QTY)?> 개</td>
				</tr>
			
		</tbody>
	</table>
	<div class="sp10"></div>
	<table style="width:100%" cellpadding="0" cellspacing="0" border="0">
		<tr height="25">
			<td width="20%" style="text-align:center; font-weight:bold; font-size:24px;">인박스</td>
			<td width="20%" style="text-align:center; font-weight:bold; font-size:24px;">포장지</td>
			<td width="20%" style="text-align:center; font-weight:bold; font-size:24px;">스티커</td>
			<td width="20%" style="text-align:center; font-weight:bold; font-size:24px;">아웃박스</td>
			<td width="20%" style="text-align:center; font-weight:bold; font-size:24px;">아웃박스스티커</td>
		</tr>
		<tr>
			<?
				$arr_work = selectWork($conn, $order_goods_no);
				
				//초기화
				$case_work_no			= "";
				$case_img				= "";
				$case_flag				= "";
				$wrap_work_no			= "";
				$wrap_img				= "";
				$wrap_flag				= "";
				$sticker_work_no		= "";
				$sticker_img			= "";
				$sticker_flag			= "";
				$out_work_no			= "";
				$out_img				= "";
				$out_flag				= "";

				if (sizeof($arr_work) > 0) {
					for ($k = 0 ; $k < sizeof($arr_work); $k++) {
						
						$RS_WORK_NO			= trim($arr_work[$k]["WORK_NO"]);
						$RS_WORK_TYPE		= trim($arr_work[$k]["WORK_TYPE"]);
						$RS_WORK_FLAG		= trim($arr_work[$k]["WORK_FLAG"]);
						$RS_CONFIRM_ADM		= trim($arr_work[$k]["CONFIRM_ADM"]);

						if (trim($RS_WORK_TYPE) == "INCASE") {
							$arr_incase = getOrderGoodsSub($conn, $GOODS_NO, "INCASE");
							$case_work_no	= $RS_WORK_NO;
							$case_name		= $arr_incase[0]["GOODS_NAME"];
							$case_img		= getImage($conn, $arr_incase[0]["GOODS_NO"], "", "");
							$case_flag		= $RS_WORK_FLAG;
						}

						if (trim($RS_WORK_TYPE) == "WRAP") {
							$wrap_work_no	= $RS_WORK_NO;
							$wrap_img		= getImage($conn, $OPT_WRAP_NO, "", "");
							$wrap_flag		= $RS_WORK_FLAG;
						}

						if (trim($RS_WORK_TYPE) == "STICKER") {
							$sticker_work_no	= $RS_WORK_NO;
							$sticker_img		= getImage($conn, $OPT_STICKER_NO, "", "");
							$sticker_flag		= $RS_WORK_FLAG;
						}

						if (trim($RS_WORK_TYPE) == "OUTCASE") {
							$arr_outcase = getOrderGoodsSub($conn, $GOODS_NO, "OUTCASE");
							$out_work_no	= $RS_WORK_NO;
							$out_img		= ""; //getImage($conn, $arr_outcase[0]["GOODS_NO"], "", "");
							$out_flag		= $RS_WORK_FLAG;
						}

						if (trim($RS_WORK_TYPE) == "OUTSTICKER") {
							$out_sticker_work_no	= $RS_WORK_NO;
							$out_sticker_flag		= $RS_WORK_FLAG;
						}
					}
				}
			?>
			<td style="text-align: center;">
				<? if ($case_work_no) { ?>
				<img src="<?=$case_img?>" style="max-width:200px; max-height:200px;"><br><br>
				<b><?=$case_name?></b>
				<? } else { ?>
				<font style="font-size:30px;">없음</font>
				<? } ?>
			</td>
			<td style="text-align: center;">
				<? if ($wrap_work_no) {?>
				<img src="<?=$wrap_img?>" style="max-width:200px; max-height:200px;">
				<? } else { ?>
				<font style="font-size:30px;">없음</font>
				<? } ?>
			</td>
			<td style="text-align: center;">
				<? if ($sticker_work_no) { ?>
				<img src="<?=$sticker_img?>" style="max-width:200px; max-height:200px;">
				<? } else { ?>
				<font style="font-size:30px;">없음</font>
				<? } ?><br/>
				<font style="color:blue;"><?=$OPT_STICKER_MSG?></font>
			</td>

			<td style="text-align: center;">
				<? if ($out_work_no) { ?>
				<?	if ($out_img) {?>
				<img src="<?=$out_img?>" style="max-width:200px; max-height:200px;"><br>
				<?	} else { ?>
				<font style="font-size:30px;">이미지 미등록</font>
				<?	} ?>
				<? } else { ?>
				<font style="font-size:30px;">없음</font>
				<? } ?>
				<br/>
				<font style="color:red; font-weight:bold;">박스입수:<?=$DELIVERY_CNT_IN_BOX?>개</font>
			</td>

			<td style="text-align: center;">
				<? if ($out_sticker_work_no) { ?>
				<b><font style="font-size:30px;">있음</font></b>
				<? } else { ?>
				<font style="font-size:30px;">없음</font>
				<? } ?>
			</td>
		</tr>
		<tr style="background:#EFEFEF" height="40">
			<th style="font-size: 28px; line-height:36px; font-weight:bold;">작업메모</th>
			<td style="font-size: 28px; line-height:36px; font-weight:bold;" colspan="4"><?=$OPT_MEMO?></td>
		</tr>
	</table>
	<?
			} 

		}
	?>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>