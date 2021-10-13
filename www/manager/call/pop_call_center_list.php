
<?php
# =============================================================================
# =============================================================================

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
	require "../_classes/com/util/ImgUtil.php";
	require "../_classes/com/etc/etc.php";
	require "../_classes/biz/board/board.php";
  
#====================================================================
# Request Parameter
#====================================================================


	if ($start_date == "") {
		//$start_date = date("Y-m-d",strtotime("-12 month"));
		$start_date = "2010-07-24";
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));
	} else {
		$end_date = trim($end_date);
	}

	$day_0 = date("Y-m-d",strtotime("0 month"));
	$day_1 = date("Y-m-d",strtotime("-1 day"));
	$day_7 = date("Y-m-d",strtotime("-7 day"));
	$day_31 = date("Y-m-d",strtotime("-1 month"));

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

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
		$nPageSize = 10;
	}

	$nPageBlock	= 10;

#===============================================================
# Get Search list count
#===============================================================

	$nListCnt =totalCntBoard($conn, $bb_code, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $keyword, $reply_state, $con_use_tf, $del_tf, $search_field, $search_str);

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listBoard($conn, $bb_code, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $keyword, $reply_state, $con_use_tf, $del_tf, $search_field, $search_str, $nPage, $nPageSize);


?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="css/admin.css" type="text/css" />
<script type="text/javascript" src="js/common.js"></script>
<script type="text/javascript" src="jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="jquery/jquery.cookie.js"></script>
<link rel="stylesheet" href="jquery/jquery-ui.min.css" type="text/css" />
<script>
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
</script>
</head>
<body id="popup_file" style="margin:10px auto 50px;">

<form name="frm" method="post">
	<input type="hidden" name="mode" value="">
	<h1 style="text-align:center; margin: 20px auto;">신규 상품 리스트</h1>
	<div id="postsch">
		<div class="addr_inp">
			<div style="width:95%;">
				<div style="float:left;">총 <?=number_format($nListCnt)?> 건 - <?=$nPage?> / <?=$nTotalPage?> 페이지</div>
				<div style="float:right;">최근 15일 신규등록분</div>
			</div>
			<dd class="temp_scroll_title">
				<table cellpadding="0" cellspacing="0" class="rowstable">
					<colgroup>
						<col width="140" />
						<col width="120" />
						<col width="*" />
						<col width="100" />
					</colgroup>
					<tr>
						<th>날짜</th>
						<th>시간</th>
						<th>문의내용</th>
						<th>등록자</th>
					</tr>
						<?
							
							if (sizeof($arr_rs) > 0) {
			
								for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

									$rn							= trim($arr_rs[$j]["rn"]);
									$BB_NO					= trim($arr_rs[$j]["BB_NO"]);
									$BB_CODE				= trim($arr_rs[$j]["BB_CODE"]);
									$CATE_01				= trim($arr_rs[$j]["CATE_01"]);
									$CATE_02				= trim($arr_rs[$j]["CATE_02"]);
									$CATE_03				= trim($arr_rs[$j]["CATE_03"]);
									$CATE_04				= trim($arr_rs[$j]["CATE_04"]);
									$WRITER_NM				= trim($arr_rs[$j]["WRITER_NM"]);
									$WRITER_PW				= trim($arr_rs[$j]["WRITER_PW"]);
									$TITLE					= SetStringFromDB($arr_rs[$j]["TITLE"]);
									$CONTENTS				= SetStringFromDB($arr_rs[$j]["CONTENTS"]);
									$HIT_CNT				= trim($arr_rs[$j]["HIT_CNT"]);
									$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
									$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);
									$REG_ADM				= trim($arr_rs[$j]["REG_ADM"]);
									
									$RS_DATE = date("Y-m-d",strtotime($REG_DATE));
									$RS_TIME = date("H:i",strtotime($REG_DATE));


						?>
						<tr height="30">
							<td><a href="javascript:js_view('<?=$rn?>','<?=$BB_CODE?>','<?=$BB_NO?>');"><?=$rn?></a></td>
							<td><?= $RS_DATE ?></td>
							
							<td><?= $RS_TIME ?></td>
							<td class="modeual_nm">
								<?
									if($CATE_04 <> "") 
										echo getCompanyName($conn,$CATE_04);
									else
										echo $CATE_03;
								?>
							</td>
							<td class="modeual_nm">
								<?
									if($WRITER_PW <> "") 
										echo getAdminName($conn,$WRITER_PW);
									else
										echo $WRITER_NM;
								?>
							</td>
							<td class="modeual_nm">
								<a href="javascript:js_view('<?=$rn?>','<?=$BB_CODE?>','<?=$BB_NO?>');">
								<?
									$CONTENTS = nl2br($CONTENTS);
									echo $CONTENTS;
								?>
								</a>
							</td>
						</tr>
						<?
								}
							}
						?>
					</table>
					<div class="sp30"></div>
				</div>
				<!-- --------------------- 페이지 처리 화면 START -------------------------->
				<?
					# ==========================================================================
					#  페이징 처리
					# ==========================================================================
					if (sizeof($arr_rs) > 0) {
						#$search_field		= trim($search_field);
						#$search_str			= trim($search_str);

						$strParam = "";
						$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str;
						$strParam = $strParam."&start_date=".$start_date."&end_date=".$end_date."&start_price=".$start_price."&end_price=".$end_price."&con_cate=".$con_cate;
						$strParam = $strParam."&con_cate_01=".$con_cate_01."&con_cate_02=".$con_cate_02."&con_cate_03=".$con_cate_03."&con_cate_04=".$con_cate_04."&con_tax_tf=".$con_tax_tf."&chk_vendor=".$chk_vendor."&vendor_calc=".$vendor_calc."&view_type=".$view_type."&exclude_category=".$exclude_category."&chk_next_sale_price=".$chk_next_sale_price;

				?>
				<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
				<?
					}
				?>
				<!-- --------------------- 페이지 처리 화면 END -------------------------->
				
			</dd>
		</div>

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