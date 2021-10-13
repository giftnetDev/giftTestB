<?session_start();?>
<?
//header("Pragma;no-cache");
//header("Cache-Control;no-cache,must-revalidate");

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "GD021"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/goods/goods_option.php";	

#====================================================================
# Request Parameter
#====================================================================
	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);

	$option_yn			= trim($option_yn);
	$option_confirm_yn	= trim($option_confirm_yn);
	
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
		$nPageSize = 20;
	}

	$nPageBlock	= 10;

#===============================================================
# Get Search list count
#===============================================================

	$arr_options = array("exclude_category" => $con_exclude_category, "vendor_calc" => $txt_vendor_calc);

	$nListCnt =totalCntGoodsOption($conn, $con_cate, $search_field, $search_str, $arr_options, $order_field, $order_str, $nPage, $nRowCount,$total_cnt, $option_yn, $option_confirm_yn);

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = OptionlistGoods($conn, $con_cate, $search_field, $search_str, $arr_options, $order_field, $order_str, $nPage, $nPageSize,$nListCnt, $option_yn, $option_confirm_yn);
		
	$strParam = $strParam."&nPageSize=".$nPageSize."&con_cate=".$con_cate."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str."&option_yn=".$option_yn."&option_confirm_yn=".$option_confirm_yn;

	
	
?>
	
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<script type="text/javascript" src="../jquery/jquery.floatThead.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<style>
	.wrong {background-color:#ffcdcf;} 
</style>
  <script type="text/javascript" >

	function js_view(rn, goods_no) 
	{

		var frm = document.frm;
		
		frm.goods_no.value = goods_no;
		frm.mode.value = "S";
		frm.target = "";
		frm.method = "get";
		frm.action = "goods_option_detail.php";
		frm.submit();
		
	}


	// 조회 버튼 클릭 시 
	function js_search() {
		
		var frm = document.frm;

		frm.nPage.value = "1";

		frm.con_cate.value = "";

		if (frm.gd_cate_01 != null) {
			if (frm.gd_cate_01.value != "") {
				frm.con_cate.value = frm.gd_cate_01.value;
			}
		}

		if (frm.gd_cate_02 != null) {
			if (frm.gd_cate_02.value != "") {
				frm.con_cate.value = frm.gd_cate_02.value;
			}
		}

		if (frm.gd_cate_03 != null) {
			if (frm.gd_cate_03.value != "") {
				frm.con_cate.value = frm.gd_cate_03.value;
			}
		}

		if (frm.gd_cate_04 != null) {
			if (frm.gd_cate_04.value != "") {
				frm.con_cate.value = frm.gd_cate_04.value;
			}
		}

		frm.target = "";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_reload() {
		location.reload();
	}

	(function($){
		$.fn.extend({
			center: function () {
				return this.each(function() {
					var top = ($(window).height() - $(this).find("img").outerHeight()) / 2 + $(window).scrollTop();
					var left = ($(window).width() - $(this).find("img").outerWidth()) / 2;

					if($(this).find("img").outerHeight() == 0 || $(this).find("img").outerWidth() == 0)
						$(this).css({position:'absolute', margin:0, top: (100 + $(window).scrollTop()) +'px', left: 400 +'px'});
					else
						$(this).css({position:'absolute', margin:0, top: (top > 0 ? top : 0)+'px', left: (left > 0 ? left : 0)+'px'});
				});
			}
		}); 
	})(jQuery);

	$(function(){
	
		var img_frame = $("<div style='background-color: #EFEFEF; border: 1px solid #DEDEDE; padding:5px 5px 5px 5px; z-index:9999;'></div>");
		$(".goods_thumb").hover(function(){

			var origin_img = $(this).prop("src").replace("simg/s_50_50_","");
			
			img_frame.show().append($("<img src='"+origin_img+"?v="+(new Date()).getTime()+"' style='max-height:800px; max-width:600px;'/>"));

			$(this).after(img_frame);

			img_frame.center();

		}, function(){

			img_frame.empty().hide();

		});

		var win;
		$(".goods_thumb").click(function() {
			
			var origin_img = $(this).prop("src").replace("simg/s_50_50_","");
			
			win = window.open(origin_img, 'win');
			window.setTimeout('check()',1000);

		});

		function check() {
			if(win.document.readyState =='complete'){
				win.document.execCommand("SaveAs");
				win.close();
			} else { 
				window.setTimeout('check();',1000);
			}
		}

		$(window).scroll(function() {
		   img_frame.empty().hide();
		});

	});
</script>

</head>

<body id="admin">
<form name="frm" method="post" onSubmit="js_search(); return true;">
<input type="hidden" name="depth" value="">
<input type="hidden" name="goods_no" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="nPage" value="<?=$nPage?>">

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

				<h2>상품 옵션 관리</h2>
				<div class="btnright">

				</div>
				<div class="category_choice">&nbsp;</div>

				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="10%" />
					<col width="*" />
					<col width="10%" />
					<col width="*" />
				</colgroup>
				<tbody>
					<tr>
						<th>카테고리</th>
						<td colspan="3">
							<?= makeCategorySelectBoxOnChange($conn, $con_cate, $con_exclude_category);?>
							<input type="hidden" name="con_cate" value="<?=$con_cate?>">
							<input type="hidden" name="con_exclude_category" value="<?=$con_exclude_category?>"/>
							<span class="exception">
							<?
								if($con_exclude_category <> "") { 
									$max_index = 0;
									while($max_index <= strlen($con_exclude_category)) {
												
										if($max_index > 2)
											echo " > ";
										echo getCategoryNameOnly($conn, left($con_exclude_category, $max_index));

										$max_index += 2;

									}
								}
							?>
							</span>
						</td>
					</tr>
									
					<tr>
						<th>검색조건</th>
						<td>
							<select name="nPageSize" style="width:84px;">
								<option value="20" <? if ($nPageSize == "20") echo "selected"; ?> >20개씩</option>
								<option value="50" <? if ($nPageSize == "50") echo "selected"; ?> >50개씩</option>
								<option value="100" <? if ($nPageSize == "100") echo "selected"; ?> >100개씩</option>
								<option value="300" <? if ($nPageSize == "300") echo "selected"; ?> >300개씩</option>
								<option value="500" <? if ($nPageSize == "500") echo "selected"; ?> >500개씩</option>
							</select>&nbsp;
							<select name="search_field" style="width:84px;">
								<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >통합검색</option>
								<option value="GOODS_NAME" <? if ($search_field == "GOODS_NAME") echo "selected"; ?> >상품명</option>
								<option value="GOODS_NO" <? if ($search_field == "GOODS_NO") echo "selected"; ?> >상품번호</option>
								<option value="GOODS_CODE" <? if ($search_field == "GOODS_CODE") echo "selected"; ?> >상품코드</option>
							</select>&nbsp;

							</select>&nbsp;

							<input type="text" value="<?=$search_str?>" name="search_str" size="15" class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();" />
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						</td>
						<th>정렬</th>
						<td>
							<select name="order_field" style="width:84px;">
								<option value="REG_DATE" <? if ($order_field == "") echo "selected"; ?> >등록일</option>
								<option value="GOODS_NAME" <? if ($order_field == "GOODS_NAME") echo "selected"; ?> >상품명</option>
								<option value="GOODS_NO" <? if ($order_field == "GOODS_NO") echo "selected"; ?> >상품번호</option>
								<option value="GOODS_CODE" <? if ($order_field == "GOODS_CODE") echo "selected"; ?> >상품코드</option>
							</select>&nbsp;&nbsp;
							<input type='radio' name='order_str' value='ASC' <? if ($order_str == "ASC") echo " checked"; ?>> 오름차순 &nbsp;
							<input type='radio' class="" name='order_str' value='DESC' <? if (($order_str == "DESC") || ($order_str == "")) echo " checked"; ?> > 내림차순 
						</td>
					</tr>

					<tr>
						<th>옵션유무</th>
						<td>
							<select name="option_yn" style="width:84px;">
									<option value="ALL" <? if ($option_yn == "ALL") echo "selected"; ?> >선택</option>
									<option value="Y" <? if ($option_yn == "Y") echo "selected"; ?> >Y</option>
									<option value="N" <? if ($option_yn == "N") echo "selected"; ?> >N</option>
							</select>&nbsp;&nbsp;
						</td>
						<th>옵션완료유무</th>
						<td>
							<select name="option_confirm_yn" style="width:84px;">
									<option value="ALL" <? if ($option_confirm_yn == "ALL") echo "selected"; ?> >선택</option>
									<option value="Y" <? if ($option_confirm_yn == "Y") echo "selected"; ?> >Y</option>
									<option value="N" <? if ($option_confirm_yn == "N") echo "selected"; ?> >N</option>
							</select>&nbsp;&nbsp;
						</td>
					</tr>
				</tbody>
			</table>
			<div class="sp20"></div>
	
				총 <?=number_format($nListCnt)?> 건
				<div class="clear"></div>
				<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table">
					

					<colgroup>
						<col width="7%" />
						<col width="5%" />
						<col width="16%" />
						<col width="*"/>
						<col width="10%" />
						<col width="10%" />
						<col width="15%" />
					</colgroup>
					<thead>
						<tr>
							<th>상품번호</th>
							<th>이미지</th>
							<th>상품코드</th>
							<th>상품명</th>
							<th>옵션유무</th>
							<th>옵션완료유무</th>
							<th>옵션완료시간</th>
						</tr>
					</thead>
					

					<tbody>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							$rn								= trim($arr_rs[$j]["rn"]);
							$GOODS_NO					= trim($arr_rs[$j]["GOODS_NO"]);
							$GOODS_CODE				= trim($arr_rs[$j]["GOODS_CODE"]);
							$GOODS_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
							$GOODS_SUB_NAME			= SetStringFromDB($arr_rs[$j]["GOODS_SUB_NAME"]);
							$IMG_URL					= trim($arr_rs[$j]["IMG_URL"]);
							$FILE_NM					= trim($arr_rs[$j]["FILE_NM_100"]);
							$FILE_RNM					= trim($arr_rs[$j]["FILE_RNM_100"]);
							$FILE_PATH				= trim($arr_rs[$j]["FILE_PATH_100"]);
							$FILE_SIZE				= trim($arr_rs[$j]["FILE_SIZE_100"]);
							$FILE_EXT				= trim($arr_rs[$j]["FILE_EXT_100"]);
							$FILE_NM_150			= trim($arr_rs[$j]["FILE_NM_150"]);
							$FILE_RNM_150			= trim($arr_rs[$j]["FILE_RNM_150"]);
							$FILE_PATH_150			= trim($arr_rs[$j]["FILE_PATH_150"]);
							$FILE_SIZE_150			= trim($arr_rs[$j]["FILE_SIZE_150"]);
							$FILE_EXT_150			= trim($arr_rs[$j]["FILE_EXT_150"]);
							$FILE_EXT_150			= trim($arr_rs[$j]["FILE_EXT_150"]);

							$OPTION_YN				= trim($arr_rs[$j]["OPTION_YN"]);

							$OPTION_CF				= trim($arr_rs[$j]["OPTION_CF"]);
							$OPTION_ADM				= trim($arr_rs[$j]["OPTION_ADM"]);
							$OPTION_DATE			= trim($arr_rs[$j]["OPTION_DATE"]);

							// 이미지가 저장 되어 있을 경우
							$img_url	= getGoodsImage($FILE_NM, $IMG_URL, $FILE_PATH_150, $FILE_RNM_150, "50", "50");
							$convertedGoodsCode = str_replace("-", "_", $GOODS_CODE);
							$detailUrl	= $_SERVER[DOCUMENT_ROOT]."/upload_data/goods_image/detail/".$convertedGoodsCode.".jpg";
				
				?>

						<? if($view_type == "price" || $view_type == "") { ?>
						<tr class="<?=$str_use_style?> <?=$str_wrong_style?>" >
							<td>
								<?=$GOODS_NO?>
							</td>
							<td style="padding: 1px 1px 1px 1px">
								<img src="<?=$img_url?>" title="클릭하시면 새 창에 원본 이미지가 열립니다." data-thumbnail="<?=$img_url?>" class="goods_thumb" width="50px" height="50px">
							</td>
							<td><a href="javascript:js_view('<?= $rn ?>','<?= $GOODS_NO ?>');"><?=$GOODS_CODE?></td>
							<td class="modeual_nm"><a href="javascript:js_view('<?= $rn ?>','<?= $GOODS_NO ?>');"><?=$STR_TAX_TF?> <?= $GOODS_NAME ?> <?= $GOODS_SUB_NAME ?></a></td>
							<td><?=$OPTION_YN?></td><!--옵션유뮤상태-->
							<td><?=$OPTION_CF?></td><!--옵션완료유무-->
							<td><?=$OPTION_DATE?></td><!--옵션완료유무-->
						</tr>
						<? } ?>
				<?			
						}
					} else { 
				?> 
						<tr>
							<td align="center" height="50" colspan="7">데이터가 없습니다. </td>
						</tr>
				<? 
					}
				?>
					</tbody>
				</table>
				
				<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">
					
				</div>
				<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">

				</div>

				
					<!-- --------------------- 페이지 처리 화면 START -------------------------->
					<?
						# ==========================================================================
						#  페이징 처리
						# ==========================================================================
						if (sizeof($arr_rs) > 0) {
							#$search_field		= trim($search_field);
							#$search_str			= trim($search_str);

							#$search_str			= trim($search_str);

							$strParam = "";
							$strParam = $strParam."&nPageSize=".$nPageSize."&con_cate=".$con_cate."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str."&option_yn=".$option_yn."&option_confirm_yn=".$option_confirm_yn;


							
					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- 페이지 처리 화면 END -------------------------->
			</div>
			<div class="sp50"></div>

			<!-- // E: mwidthwrap -->

		</td>
	</tr>
	</table>
	<a style="display:scroll;position:fixed;bottom:10px;right:10px;" href="#">▲ 위로</a>
</div>
<iframe src="about:blank" name="ifr_hidden" id="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>