<?session_start();?>
<?
header("Pragma;no-cache");
header("Cache-Control;no-cache,must-revalidate");

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
	include "../../_common/common_header.php"; 

	
#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/util/ImgUtil.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/goods/goods_option.php";
	require "../../_classes/biz/admin/admin.php";


?>

<?

	if ($mode == "S") {

		$arr_rs = selectGoods($conn, $goods_no);

		$rs_goods_no			= trim($arr_rs[0]["GOODS_NO"]); 
		$rs_goods_cate			= trim($arr_rs[0]["GOODS_CATE"]); 
		$rs_goods_code			= trim($arr_rs[0]["GOODS_CODE"]); 
		$rs_goods_name			= SetStringFromDB($arr_rs[0]["GOODS_NAME"]); 
		$rs_goods_sub_name	    = SetStringFromDB($arr_rs[0]["GOODS_SUB_NAME"]); 
		$rs_cate_01				= trim($arr_rs[0]["CATE_01"]); 
		$rs_cate_02				= trim($arr_rs[0]["CATE_02"]); 
		$rs_cate_03				= trim($arr_rs[0]["CATE_03"]); 
		$rs_cate_04				= trim($arr_rs[0]["CATE_04"]);
		$rs_restock_date		= trim($arr_rs[0]["RESTOCK_DATE"]); 
		$rs_price				= trim($arr_rs[0]["PRICE"]); 
		$rs_buy_price			= trim($arr_rs[0]["BUY_PRICE"]); 
		$rs_sale_price			= trim($arr_rs[0]["SALE_PRICE"]); 
		$rs_extra_price			= trim($arr_rs[0]["EXTRA_PRICE"]); 
		$rs_stock_cnt			= trim($arr_rs[0]["STOCK_CNT"]); 
		$rs_mstock_cnt          = trim($arr_rs[0]["MSTOCK_CNT"]);
		$rs_tax_tf				= trim($arr_rs[0]["TAX_TF"]); 
		$rs_img_url				= trim($arr_rs[0]["IMG_URL"]); 
		$rs_file_nm_100			= trim($arr_rs[0]["FILE_NM_100"]); 
		$rs_file_rnm_100		= trim($arr_rs[0]["FILE_RNM_100"]); 
		$rs_file_path_100		= trim($arr_rs[0]["FILE_PATH_100"]); 
		$rs_file_size_100		= trim($arr_rs[0]["FILE_SIZE_100"]); 
		$rs_file_ext_100		= trim($arr_rs[0]["FILE_EXT_100"]); 
		$rs_file_nm_150			= trim($arr_rs[0]["FILE_NM_150"]); 
		$rs_file_rnm_150		= trim($arr_rs[0]["FILE_RNM_150"]); 
		$rs_file_path_150		= trim($arr_rs[0]["FILE_PATH_150"]); 
		$rs_file_size_150		= trim($arr_rs[0]["FILE_SIZE_150"]); 
		$rs_file_ext_150		= trim($arr_rs[0]["FILE_EXT_150"]); 
		$rs_contents			= trim($arr_rs[0]["CONTENTS"]); 
		$rs_memo				= trim($arr_rs[0]["MEMO"]); 
		$rs_delivery_cnt_in_box	= trim($arr_rs[0]["DELIVERY_CNT_IN_BOX"]); 
		$rs_read_cnt			= trim($arr_rs[0]["READ_CNT"]); 
		$rs_disp_seq			= trim($arr_rs[0]["DISP_SEQ"]); 
		$rs_use_tf				= trim($arr_rs[0]["USE_TF"]); 
		$rs_del_tf				= trim($arr_rs[0]["DEL_TF"]); 
		$contents			    = trim($arr_rs[0]["CONTENTS"]); 
		$rs_stock_tf		    = trim($arr_rs[0]["STOCK_TF"]); 
		
		/* 2015 9월 08일 추가*/
		$rs_sticker_price		= trim($arr_rs[0]["STICKER_PRICE"]); 
		$rs_print_price			= trim($arr_rs[0]["PRINT_PRICE"]); 
		$rs_delivery_price		= trim($arr_rs[0]["DELIVERY_PRICE"]); 
		$rs_sale_susu			= trim($arr_rs[0]["SALE_SUSU"]); 

		/* 2016 2월 18일 추가*/
		$rs_labor_price			= trim($arr_rs[0]["LABOR_PRICE"]); 
		$rs_other_price			= trim($arr_rs[0]["OTHER_PRICE"]); 

		/* 2016 10월 10일 추가*/
		$rs_next_sale_price		= trim($arr_rs[0]["NEXT_SALE_PRICE"]); 
		$rs_reg_adm				= trim($arr_rs[0]["REG_ADM"]); 
		$rs_reg_date			= trim($arr_rs[0]["REG_DATE"]); 
		$rs_exposure_tf			= trim($arr_rs[0]["EXPOSURE_TF"]);

		$CURRENT_REASEON = trim($arr_rs[0]["REASON"]); 
		
		if ($rs_tax_tf == "비과세") {
			$STR_TAX_TF = "<font color='orange'>비과세</font>";
		} else {
			$STR_TAX_TF = "<font color='navy'>과세</font>";
		}
		
		$img_url	= getGoodsImage($rs_file_nm_100, $rs_img_url, $rs_file_path_150, $rs_file_rnm_150, "250", "250");

		if($tab_index == "")
		$tab_index = 0;

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
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->
<script type="text/javascript" src="../../_common/SE2.1.1.8141/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<script type="text/javascript" src="../jquery/jquery.cookie.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<style>
	.wordbreak{
		word-wrap:break-word;
	}
</style>
<script>
	$(function() {
		$("#tabs").tabs({
		  active : <?=$tab_index?>
		});
	});

</script>
<script type="text/javascript">

	function js_all_check(optionTp) 
	{
		var frm = document.frm;
		var chkOption;
		var all_chk;		

		if(optionTp == "W")
		{
			chkOption = frm['chk_noW[]'] ;
			all_chk = frm.all_chkW;
		}
		
		if(optionTp == "S")
		{
			chkOption = frm['chk_noS[]'] ;
			all_chk = frm.all_chkS;
		}
		
		if (chkOption != null) {

			if (chkOption.length != null) {
				if (all_chk.checked == true) {					
					for (i = 0; i < chkOption.length; i++) {
						chkOption[i].checked = true;
					}
				} else {
					for (i = 0; i < chkOption.length; i++) {
						chkOption[i].checked = false;
					}
				}
			} else {
			
				if (all_chk.checked == true) {
					chkOption.checked = true;
				} else {
					chkOption.checked = false;
				}
			}
		}
	}

	function optionSelsave(optionTp) 
	{
		var frm = document.frm;
		var optionNo;
		
		if(optionTp == "W")
		{
			if(frm.wrap_no.value == "") 
			{
				alert('포장지를 선택 해 주세요.');
				return;
			}
			else
			{
				optionNo = frm.wrap_no.value;
			}
		}
		
		if(optionTp == "S")
		{
			if(frm.stiker_no.value == "") 
			{
				alert('스티커를 선택 해 주세요.');
				return;
			}
			else
			{
				optionNo = frm.stiker_no.value;
			}
		}

		$.ajax({
			url: "json_goods_option.php",
			dataType: 'json',
			type: 'post',
			async: true,
			data: {
					  mode: "GOODS_OPTION_SAVE"					
					, reg_adm: <?=$s_adm_no?>
					, goods_no: frm.goods_no.value
					, goods_cd: frm.goods_cd.value
					, optionTp: optionTp
					, optionNo: optionNo
				},
				success: function(data) 
				{
					//alert("성공");
					js_search(optionTp);
				}	,
				error: function(jqXHR, textStatus, errorThrown)
				{
					alert('통신 실패');
					return;
				}
		});
	}

	function js_selDel(optionTp) 
	{ 
		var frm = document.frm;

		if(optionTp == "W")
		{

			var selected_cnt = $("input[name='chk_noW[]']:checked").length;

			if(selected_cnt == 0) 
			{
				alert('선택된 데이터가 없습니다');
				return;
			}

			if (!confirm("선택한 데이터를 삭제 하시겠습니까?")) return;	

			var option_no= new Array();

			$("input[name='chk_noW[]']:checked").each(function(){
				option_no.push($(this).val());
			});
		}

		if(optionTp == "S")
		{

			var selected_cnt = $("input[name='chk_noS[]']:checked").length;

			if(selected_cnt == 0) 
			{
				alert('선택된 데이터가 없습니다');
				return;
			}

			if (!confirm("선택한 데이터를 삭제 하시겠습니까?")) return;	

			var option_no= new Array();

			$("input[name='chk_noS[]']:checked").each(function(){
				option_no.push($(this).val());
			});
		}

		$.ajax({
			url: "json_goods_option.php",
			dataType: 'json',
			type: 'post',
			async: true,
			data: {
					  mode: "GOODS_OPTION_DEL"					
					, option_no: option_no
				},
				success: function(data) 
				{
					alert("선택 된 데이터가 삭제 되었습니다.");
					js_search(optionTp);
				}	,
				error: function(jqXHR, textStatus, errorThrown)
				{
					alert('통신 실패');
					return;
				}
		});
	}

	function js_reload() 
	{
		location.reload();
	}
	
	function tabIndex(tabNo) 
	{
		alert(tabNo);
		if(tabNo == "S")
		{
			frm.tab_index.value = 1;	
		}
		else
		{
			frm.tab_index.value = 0;	
		}
	}

	function js_search(optionTp) {
		var frm = document.frm;
		
		if(optionTp == "S")
		{
			frm.tab_index.value = 1;	
		}
		else
		{
			frm.tab_index.value = 0;	
		}
		
		frm.mode.value = "S";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_list() 
	{
		document.location.href = "goods_option_list.php";
	}	

	(function($){
		$.fn.extend({
			center: function () {
				return this.each(function() {
					var top = ($(window).height() - $(this).find("img").outerHeight()) / 2 + $(window).scrollTop();
					var left = ($(window).width() - $(this).find("img").outerWidth()) / 2;

					if($(this).find("img").outerHeight() == 0 || $(this).find("img").outerWidth() == 0)
						$(this).css({position:'absolute', margin:0, top: (50 + $(window).scrollTop()) +'px', left: 200 +'px'});
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
			
			img_frame.show().append($("<img src='"+origin_img+"?v="+(new Date()).getTime()+"' style='max-height:400px; max-width:300px;'/>"));

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

<style>
	.row_deleted {background-color:#dfdfdf; }
	.row_deleted > td{color:#fff !important;}
	.row_deleted > td > a{color:#fff !important;}
</style>
</head>
<body id="admin">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="goods_no" value="<?=$goods_no?>" />
<input type="hidden" name="goods_cd" value="<?=$rs_goods_code?>" />
<input type="hidden" name="tab_index" value="<?=$tab_index?>" />
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
			
			<h2 style="margin:0;">상품 옵션 관리</h2>				
			<div class="btnright" style="margin:0 0 5px 0;">
				<a href="javascript:js_list();"><img src="../images/admin/btn_list.gif" alt="목록"></a>
			</div>

			<table cellpadding="0" cellspacing="0" class="colstable02">
				<colgroup>
					<col width="15%" />
					<col width="15%" />
					<col width="15%" />
					<col width="55%" />
				</colgroup>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       
				<tbody>
					<tr>
						<td align="center" style="padding: 5px 5px 5px 5px" class="line" colspan="2" rowspan="6">
							<img src="<?=$img_url?>" border="0" width="250" height="250">
						</td>
						<th>상품명</th>
						<td colspan="3" class="line">
							<?=$rs_goods_name?>
						</td>
					</tr>
					<tr>
						<th>상품코드</th>
						<td class="line">
							<?=$rs_goods_code?>
						</td>
					</tr>
					<tr>
						<th>공급업체</th>
						<td class="line">
							<?= getCompanyName($conn, $rs_cate_03);?>
						</td>
					</tr>					
					<tr>
						<th>과세여부</th>
						<td class="line">
							<?=$STR_TAX_TF?>
						</td>
					</tr>
					<tr>
						<th>박스입수</th>
						<td class="line">
							<?=$rs_delivery_cnt_in_box?>
						</td>
					</tr>
					<tr>
						<th>판매가</th>
						<td class="line">
							<?=number_format($rs_sale_price) ?> 원
							<input type="hidden" name="origin_sale_price" value="<?=$origin_sale_price?>"/>
						</td>
					</tr>			

				</table>

				<div class="sp20"></div>

				<div id="tabs" style="width:95%; margin:10px 0;">
					<ul>
						<li><a href="#tabs-1">포장지</a></li> 
						<li><a href="#tabs-2">스티커</a></li> 

					</ul>
					
					<div id="tabs-1">
					<table cellpadding="0" cellspacing="0" class="colstable">
					<colgroup>
						<col width="120" />
						<col width="*" />
						<col width="120" />
						<col width="*" />
					</colgroup>
					<tbody>


					<tr>
						<th>포장지</th>
						<td class="line">
						
							<?
								$ar_wrap_all = array();

								$arr_wrap_all = listGoodsOptionSel($conn, '010204', $goods_no, "W");
									
									foreach($arr_wrap_all as $item) { 
										$ar_wrap_all[] = array("GOODS_NO" => $item["GOODS_NO"],  "GOODS_NAME2" => iconv("EUC-KR", "UTF-8", $item["GOODS_NAME"]), "IMG_URL" => getImage($conn, $item["GOODS_NO"], "250", "250"));
									}

									echo makeGoodsSelectBoxWithDataImage($conn, $arr_wrap_all, "wrap_no", "150", "선택없음", "", "", "GOODS_NO", "GOODS_NAME");

							?>
								
								<script>
								$(function(){

									$("select[name=wrap_no]").change(function(){
										var image_url = $(this).find(':selected').attr('data-image');
										$("img[name=sample_img]").attr("src", image_url);
									});

								});
								</script>
							<input type="button" name="optionSelect" value="선  택" onclick="optionSelsave('W');" />
						</td>
						<td rowspan="2" colspan="2" style="text-align:center;"><img name="sample_img" src="/manager/images/no_img.gif" style="max-height:200px; max-width:200px;"/></td>
					</tr>
					
					</tbody>
					</table>
					<br>
					* 포장지 상품 리스트
					<div style="display:inline-block; width: 95%; text-align: right; margin: 0 0 10px 0;">
						<input type="button" value="선택삭제" onclick="js_selDel('W')">
					</div>	
					<table cellpadding="0" cellspacing="0" class="rowstable">
						<colgroup>
							<col width="3%" />
							<col width="4%"/>
							<col width="5%" />
							<col width="10%" />
							<col width="*"/>
							<col width="15%"/>
							<col width="10%"/>
							<col width="15%"/>
						</colgroup>
						<tr>							
							<th><input type="checkbox" name="all_chkW" onClick="js_all_check('W');"></th>
							<th>No.</th>
							<th>이미지</th>
							<th>상품코드</th>
							<th>상품명</th>
							<th>회사카테고리</th>
							<th>등록자</th>
							<th>등록시간</th>
						</tr>
						<?
							$arr_rs = selectTGoodsOption($conn, $goods_no, "W");
							
							if(sizeof($arr_rs) > 0) 
							{
								for($i = 0; $i < sizeof($arr_rs); $i++)
								{
									$RN					= trim($arr_rs[$i]["RN"]);
									$OPTION_NO 			= trim($arr_rs[$i]["OPTION_NO"]);
									$GOODS_NO 			= trim($arr_rs[$i]["GOODS_NO"]);
									$GOODS_CODE 		= trim($arr_rs[$i]["GOODS_CODE"]);
									$OPTION_TYPE 		= trim($arr_rs[$i]["OPTION_TYPE"]);
									$OPTION_GOODS_NO 	= trim($arr_rs[$i]["OPTION_GOODS_NO"]);
									$OPTION_GOODS_NAME 	= SetStringFromDB($arr_rs[$i]["OPTION_GOODS_NAME"]);
									$CP_CATE 			= trim($arr_rs[$i]["CP_CATE"]);
									$CATE_NAME 			= trim($arr_rs[$i]["CATE_NAME"]);
									$REG_ADM 			= trim($arr_rs[$i]["REG_ADM"]);
									$REG_DATE 			= trim($arr_rs[$i]["REG_DATE"]);
									$FILE_NM	  		= trim($arr_rs[$i]["FILE_NM_100"]);
									$IMG_URL  			= trim($arr_rs[$i]["IMG_URL"]);
									$FILE_PATH_150  	= trim($arr_rs[$i]["FILE_PATH_150"]);
									$FILE_RNM_150  		= trim($arr_rs[$i]["FILE_RNM_150"]);

									// 이미지가 저장 되어 있을 경우
									$img_url	= getGoodsImage($FILE_NM, $IMG_URL, $FILE_PATH_150, $FILE_RNM_150, "50", "50");

						?>
								<tr height="30">
									<td><input type="checkbox" name="chk_noW[]" class="chk" value="<?=$OPTION_NO?>"></td>
									<td><?=$RN?> </td>
									<td style="padding: 1px 1px 1px 1px">
										<img src="<?=$img_url?>" title="클릭하시면 새 창에 원본 이미지가 열립니다." data-thumbnail="<?=$img_url?>" class="goods_thumb" width="50px" height="50px">
									</td>
									<td><?=$GOODS_CODE?> </td>
									<td class="modeual_nm"><?=$OPTION_GOODS_NAME?> </td>
									<td class="modeual_nm"><?=$CATE_NAME?> </td>
									<td><?=getAdminName($conn, $REG_ADM)?> </td>
									<td><?=$REG_DATE?> </td>
									
								</tr>
						<?
								}
							} 
							else 
							{
						?>
							<tr>
								<td height="30" colspan="8">포장지 상품이 없습니다.</td>
							</tr>
						<?
							}
						?>
					</table>
				</div>

					
				<div id="tabs-2">
					<table cellpadding="0" cellspacing="0" class="colstable">
					<colgroup>
						<col width="120" />
						<col width="*" />
						<col width="120" />
						<col width="*" />
					</colgroup>
					<tbody>


					<tr>
						<th>스티커</th>
						<td class="line">
						
							<?
								$ar_wrap_all = array();

								$arr_wrap_all = listGoodsOptionSel($conn, '0103', $goods_no, "S");
									
									foreach($arr_wrap_all as $item) { 
										$ar_wrap_all[] = array("GOODS_NO" => $item["GOODS_NO"],  "GOODS_NAME2" => iconv("EUC-KR", "UTF-8", $item["GOODS_NAME"]), "IMG_URL" => getImage($conn, $item["GOODS_NO"], "250", "250"));
									}

									echo makeGoodsSelectBoxWithDataImage($conn, $arr_wrap_all, "stiker_no", "150", "선택없음", "", "", "GOODS_NO", "GOODS_NAME");

							?>
								
								<script>
								$(function(){

									$("select[name=stiker_no]").change(function(){
										var image_url = $(this).find(':selected').attr('data-image');
										$("img[name=sample_img]").attr("src", image_url);
									});

								});
								</script>
							<input type="button" name="optionSelect" value="선  택" onclick="optionSelsave('S');" />
						</td>
						<td rowspan="2" colspan="2" style="text-align:center;"><img name="sample_img" src="/manager/images/no_img.gif" style="max-height:200px; max-width:200px;"/></td>
					</tr>
					
					</tbody>
					</table>
					<br>
					* 스티커 상품 리스트
					<div style="display:inline-block; width: 95%; text-align: right; margin: 0 0 10px 0;">
						<input type="button" value="선택삭제" onclick="js_selDel('S')">
					</div>	
					<table cellpadding="0" cellspacing="0" class="rowstable">
						<colgroup>
							<col width="3%" />
							<col width="4%"/>
							<col width="5%" />
							<col width="10%" />
							<col width="*"/>
							<col width="15%"/>
							<col width="10%"/>
							<col width="15%"/>
						</colgroup>
						<tr>							
							<th><input type="checkbox" name="all_chkS" onClick="js_all_check('S');"></th>
							<th>No.</th>
							<th>이미지</th>
							<th>상품코드</th>
							<th>상품명</th>
							<th>회사카테고리</th>
							<th>등록자</th>
							<th>등록시간</th>
						</tr>
						<?
							$arr_rs = selectTGoodsOption($conn, $goods_no, "S");
							
							if(sizeof($arr_rs) > 0) 
							{
								for($i = 0; $i < sizeof($arr_rs); $i++)
								{
									$RN					= trim($arr_rs[$i]["RN"]);
									$OPTION_NO 			= trim($arr_rs[$i]["OPTION_NO"]);
									$GOODS_NO 			= trim($arr_rs[$i]["GOODS_NO"]);
									$GOODS_CODE 		= trim($arr_rs[$i]["GOODS_CODE"]);
									$OPTION_TYPE 		= trim($arr_rs[$i]["OPTION_TYPE"]);
									$OPTION_GOODS_NO 	= trim($arr_rs[$i]["OPTION_GOODS_NO"]);
									$OPTION_GOODS_NAME 	= SetStringFromDB($arr_rs[$i]["OPTION_GOODS_NAME"]);
									$CP_CATE 			= trim($arr_rs[$i]["CP_CATE"]);
									$CATE_NAME 			= trim($arr_rs[$i]["CATE_NAME"]);
									$REG_ADM 			= trim($arr_rs[$i]["REG_ADM"]);
									$REG_DATE 			= trim($arr_rs[$i]["REG_DATE"]);
									$FILE_NM	  		= trim($arr_rs[$i]["FILE_NM_100"]);
									$IMG_URL  			= trim($arr_rs[$i]["IMG_URL"]);
									$FILE_PATH_150  	= trim($arr_rs[$i]["FILE_PATH_150"]);
									$FILE_RNM_150  		= trim($arr_rs[$i]["FILE_RNM_150"]);

									// 이미지가 저장 되어 있을 경우
									$img_url	= getGoodsImage($FILE_NM, $IMG_URL, $FILE_PATH_150, $FILE_RNM_150, "50", "50");

						?>
								<tr height="30">
									<td><input type="checkbox" name="chk_noS[]" class="chk" value="<?=$OPTION_NO?>"></td>
									<td><?=$RN?> </td>
									<td style="padding: 1px 1px 1px 1px">
										<img src="<?=$img_url?>" title="클릭하시면 새 창에 원본 이미지가 열립니다." data-thumbnail="<?=$img_url?>" class="goods_thumb" width="50px" height="50px">
									</td>
									<td><?=$GOODS_CODE?> </td>
									<td class="modeual_nm"><?=$OPTION_GOODS_NAME?> </td>
									<td class="modeual_nm"><?=$CATE_NAME?> </td>
									<td><?=getAdminName($conn, $REG_ADM)?> </td>
									<td><?=$REG_DATE?> </td>
									
								</tr>
						<?
								}
							} 
							else 
							{
						?>
							<tr>
								<td height="30" colspan="8">스티커 상품이 없습니다.</td>
							</tr>
						<?
							}
						?>
					</table>
				</div>		
			</div>
				
    </td>
  </tr>
  </table>
</div>

<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>
<?
#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);
?>