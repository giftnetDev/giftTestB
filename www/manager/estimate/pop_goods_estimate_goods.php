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
	$menu_right = "GD007"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/company/company.php";
	require "../../_classes/biz/estimate/estimate.php";

#====================================================================
# Request Parameter
#====================================================================

	$gp_no = trim($gp_no);

#===============================================================
# Get Search list count
#===============================================================

	$arr_rs = selectGoodsEstimateGoods($conn, $gpg_no);

	$GOODS_NO				= $arr_rs[0]["GOODS_NO"];
	$GOODS_NAME				= $arr_rs[0]["GOODS_NAME"];
	$SIZE					= $arr_rs[0]["SIZE"];
	$COMPONENT				= $arr_rs[0]["COMPONENT"];
	$DESCRIPTION			= $arr_rs[0]["DESCRIPTION"];
	$ESTIMATE_PRICE			= $arr_rs[0]["ESTIMATE_PRICE"];
	$RETAIL_PRICE			= $arr_rs[0]["RETAIL_PRICE"];
	$DELIVERY_CNT_IN_BOX	= $arr_rs[0]["DELIVERY_CNT_IN_BOX"];
	$MANUFACTURER			= $arr_rs[0]["MANUFACTURER"];
	$ORIGIN					= $arr_rs[0]["ORIGIN"];

	if($RETAIL_PRICE <> "") 
		$RETAIL_PRICE = number_format($RETAIL_PRICE)." 원";
	
	if($ESTIMATE_PRICE <> "") 
		$ESTIMATE_PRICE = number_format($ESTIMATE_PRICE)." 원";

	$img_url	= getImage($conn, $GOODS_NO, "", "");

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
  
</head>
<style>
	input[type=text], textarea, input[type=button] {display:none; width:90%;} 
	.request_date {width:95%; text-align:right;}
	.row {position:relative;}
	.extra_button {display:none; position:absolute; }
	.minus {color: red; cursor:pointer;}
</style>
<script>
	$(function(){
		$("td, th").on("click", function(){
			var source = $(this).find("span");
			var text_box = $(this).find("input[type=text], textarea");
			var button = $(this).find("input[type=button]");

			if(text_box.is(":hidden")) {
				source.hide();
				text_box.val(br2nl(source.html())).show().focus();
				button.show();
			}

		});

		$("input[type=text]").on("keydown", function(){
			$(this).parent().find("span").html($(this).val());

			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13'){

				var gpg_no = $(this).closest("table").data("gpg_no");
				var column = $(this).closest("th").data("column");
				var value = $(this).val();

				(function() {
				  $.getJSON( "/manager/estimate/json_goods_estimate.php", {
					mode: "UPDATE_ESTIMATE_GOODS",
					gpg_no: gpg_no,
					column: column,
					value : value
				  })
					.done(function( data ) {

					  $("span").show();
					  $("input[type=text]").hide();

					  $.each( data, function( i, item ) {
						  if(item.RESULT == "0")
							  alert('연결오류 : 잠시후 다시 시도해주세요');
					  });
					});
				})();

			}
		});

		$("input[type=button]").on("click", function(){

			var nl2br_val = nl2br($(this).parent().find("textarea").val());
			
			$(this).parent().find("span").html(nl2br_val);

			var gpg_no = $(this).closest("table").data("gpg_no");
			var column = $(this).closest("th").data("column");

			(function() {
			  $.getJSON( "/manager/estimate/json_goods_estimate.php", {
				mode: "UPDATE_ESTIMATE_GOODS",
				gpg_no: gpg_no,
				column: column,
				value : nl2br_val
			  })
				.done(function( data ) {

				  $("span").show();
				  $("textarea,input[type=button]").hide();

				  $.each( data, function( i, item ) {
					  if(item.RESULT == "0")
						  alert('연결오류 : 잠시후 다시 시도해주세요');
				  });
				});
			})();

		});

		$("input[type=text]").on("blur", function(){
			$("span").show();
			$("input[type=text], textarea, input[type=button]").hide();
		});

		$(".row, .extra_button").hover(
		  function() { //IN
			$( this ).find(".extra_button").show();
		  }, function() { //OUT
			$( this ).find(".extra_button").hide();
		  }
		);

		$(".minus").on("click", function(){
			$(this).closest(".row").remove();
		});

	});
</script>
<style>
body#popup_delivery_confirmation {width:1000px;}
</style>
<body id="popup_delivery_confirmation">

<div id="popup_delivery_confirmation">
	<div id="postsch_code">
		<div class="addr_inp">

<form name="frm" method="post">
<input type="hidden" name="reserve_no" value="<?=$reserve_no?>">
<input type="hidden" name="mode" value="<?=$mode?>">

			<div class="sp10"></div>
			<table cellpadding="0" cellspacing="0" width="100%" class="colstable03">
				<tr>
					<th class="h1">상 품 소 개 서</th>
				</tr>
			</table>
			<div class="sp10"></div>
			
			<table cellpadding="0" cellspacing="0" width="100%" class="colstable03" data-gpg_no="<?=$gpg_no?>">
				<colgroup>
					<col width="15%" />
					<col width="35%" />
					<col width="15%" />
					<col width="35%" />
				</colgroup>
				<tr>
					<th>상품명(브랜드포함)</th>
					<th class="line" colspan="3" data-column="GOODS_NAME">
						<span><?=$GOODS_NAME?></span>
						<input type="text" value="" placeholder="수정 내용 작성 후 꼭 엔터키를 눌러주세요"/>
					</th>
					
				</tr>
				<tr>
					<th colspan="4">
						<img src="<?=$img_url?>"/>
					</th>
				</tr>
				<tr>
					<th>상품규격( cm )</th>
					<th class="line" colspan="3" data-column="SIZE">
						<span><?=$SIZE?></span>
						<input type="text" value="" placeholder="수정 내용 작성 후 꼭 엔터키를 눌러주세요"/>
					</th>
				</tr>
				<tr>
					<th>구성(세부내역)</th>
					<th class="line" colspan="3" data-column="COMPONENT">
						<span><?=$COMPONENT?></span>
						<input type="text" value="" placeholder="수정 내용 작성 후 꼭 엔터키를 눌러주세요"/>
					</th>
				</tr>
				<tr height="80">
					<th>용도 및 특징</th>
					<th class="line" colspan="3" data-column="DESCRIPTION" style="text-align:left; padding-left:10px;">
						<span><?=nl2br($DESCRIPTION)?></span>
						<textarea rows="5" cols="100"></textarea>
						<input type="button" value="수정" />
					</th>
				</tr>
				<? if($RETAIL_PRICE == $ESTIMATE_PRICE) { ?>
				<tr>
					<th>제안가(VAT포함)</th>
					<th class="line" data-column="ESTIMATE_PRICE">
						<span><?=$ESTIMATE_PRICE?></span>
						<input type="text" value="" placeholder="수정 내용 작성 후 꼭 엔터키를 눌러주세요"/>
					</th>
					<th>박스입수</th>
					<th class="line" data-column="DELIVERY_CNT_IN_BOX">
						<span><?=$DELIVERY_CNT_IN_BOX?></span>
						<input type="text" value="" placeholder="수정 내용 작성 후 꼭 엔터키를 눌러주세요"/>
					</th>
				</tr>
				<? } else { ?>
				<tr>
					<th>제안가(VAT포함)</th>
					<th class="line" colspan="3" data-column="ESTIMATE_PRICE">
						<span><?=$ESTIMATE_PRICE?></span>
						<input type="text" value="" placeholder="수정 내용 작성 후 꼭 엔터키를 눌러주세요"/>
					</th>
				</tr>
				<tr>
					<th>기프트넷단가</th>
					<th class="line" data-column="RETAIL_PRICE">
						<span><?=$RETAIL_PRICE?></span>
						<input type="text" value="" placeholder="수정 내용 작성 후 꼭 엔터키를 눌러주세요"/>
					</th>
					<th>박스입수</th>
					<th class="line" data-column="DELIVERY_CNT_IN_BOX">
						<span><?=$DELIVERY_CNT_IN_BOX?></span>
						<input type="text" value="" placeholder="수정 내용 작성 후 꼭 엔터키를 눌러주세요"/>
					</th>
				</tr>
				<? } ?>
				
				<tr>
					<th>제조원</th>
					<th class="line" data-column="MANUFACTURER">
						<span><?=$MANUFACTURER?></span>
						<input type="text" value="" placeholder="수정 내용 작성 후 꼭 엔터키를 눌러주세요"/>
					</th>
					<th>원산지</th>
					<th class="line" data-column="ORIGIN">
						<span><?=$ORIGIN?></span>
						<input type="text" value="" placeholder="수정 내용 작성 후 꼭 엔터키를 눌러주세요"/>
					</th>
				</tr>
			</table>
			<div class="sp20"></div>
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