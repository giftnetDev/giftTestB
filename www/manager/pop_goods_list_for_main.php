
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
	require "../_classes/biz/goods/goods.php";
  
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

#====================================================================
# DML Process
#====================================================================
	$con_cate = "17,14";
	$start_date = date("Y-m-d",strtotime("-15 day"));
	$order_field = "REG_DATE";
	$order_str = "DESC";
	$con_cate_04 = "판매중";
	$con_use_tf = "Y";
	$del_tf = "N";

	$nListCnt = totalCntGoods($conn, $con_cate, $start_date, $end_date, $start_price, $end_price, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $con_tax_tf, $con_use_tf, $del_tf, $search_field, $search_str, $arr_options, $order_field, $order_str);

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listGoods($conn, $con_cate, $start_date, $end_date, $start_price, $end_price, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $con_tax_tf, $con_use_tf, $del_tf, $search_field, $search_str, $arr_options, $order_field, $order_str, $nPage, $nPageSize);

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
<script type="text/javascript">
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
	<input type="hidden" name="goods_no" value="">
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
						<col width="100" />
					</colgroup>
					<tr>
						<th>이미지</th>
						<th>상품코드</th>
						<th>상품명</th>
						<th>판매가</th>
						<th>등록일</th>
					</tr>
				</table>
				<div class="temp_scroll">
					<table cellpadding="0" cellspacing="0" class="rowstable">
						<colgroup>
							<col width="140" />
							<col width="120" />
							<col width="*" />
							<col width="100" />
							<col width="100" />
						</colgroup>
						<?
							
							if (sizeof($arr_rs) > 0) {
			
								for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

									$GOODS_NO				= trim($arr_rs[$j]["GOODS_NO"]);
									$GOODS_CODE				= trim($arr_rs[$j]["GOODS_CODE"]);
									$GOODS_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
									$STOCK_CNT				= trim($arr_rs[$j]["STOCK_CNT"]);
									$BSTOCK_CNT				= trim($arr_rs[$j]["BSTOCK_CNT"]);
									$IMG_URL				= trim($arr_rs[$j]["IMG_URL"]);
									$FILE_NM				= trim($arr_rs[$j]["FILE_NM_100"]);
									$FILE_RNM				= trim($arr_rs[$j]["FILE_RNM_100"]);
									$FILE_PATH				= trim($arr_rs[$j]["FILE_PATH_100"]);
									$FILE_SIZE				= trim($arr_rs[$j]["FILE_SIZE_100"]);
									$FILE_EXT				= trim($arr_rs[$j]["FILE_EXT_100"]);
									$FILE_NM_150			= trim($arr_rs[$j]["FILE_NM_150"]);
									$FILE_RNM_150			= trim($arr_rs[$j]["FILE_RNM_150"]);
									$FILE_PATH_150			= trim($arr_rs[$j]["FILE_PATH_150"]);
									$FILE_SIZE_150			= trim($arr_rs[$j]["FILE_SIZE_150"]);
									$FILE_EXT_150			= trim($arr_rs[$j]["FILE_EXT_150"]);
									$SALE_PRICE				= trim($arr_rs[$j]["SALE_PRICE"]);
									$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);


									$REG_DATE = date("Y-m-d",strtotime($REG_DATE));
									
									// 이미지가 저장 되어 있을 경우
									$img_url	= getGoodsImage($FILE_NM, $IMG_URL, $FILE_PATH_150, $FILE_RNM_150, "135", "135");


						?>
						<tr height="30">
							<td style="padding: 1px 1px 1px 1px; width:137px; height:137px;">
								<img src="<?=$img_url?>" title="클릭하시면 새 창에 원본 이미지가 열립니다." data-thumbnail="<?=$img_url?>" class="goods_thumb" style="max-width:135px; max-height:135px;">
							</td>
							<td><a href="javascript:js_view('<?= $GOODS_NO ?>');"><?=$GOODS_CODE?></a></td>
							<td class="modeual_nm"><a href="javascript:js_view('<?= $GOODS_NO ?>');"><?=$GOODS_NAME?></a></td>
							<td><?=number_format($SALE_PRICE)?>원</td>
							<td><?=$REG_DATE?></td>
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
			<div style="text-align:right; background-color:#ddd; padding:2px;">
				<label><input type="checkbox" class="popup_stop" value="Y"/>오늘 그만 보기</label>	
			</div>
		</div>

	</div>
</form>
<script>
	$(function(){
		var win;
		$(".goods_thumb").click(function() {
			
			var origin_img = $(this).prop("src").replace("simg/s_50_50_","");
			
			win = window.open(origin_img, 'win');
			window.setTimeout('check()',1000);

		});

		$(".popup_stop").change(function(){
			if($(this).is(":checked")) { 
				$.cookie('chk_latest_goods', '<?=date("Y-m-d",strtotime("0 day"))?>'); 
				self.close();
			}
		});

	});
</script>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================
 
	mysql_close($conn);
	
?>