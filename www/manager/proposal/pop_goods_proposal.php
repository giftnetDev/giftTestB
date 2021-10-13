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
	require "../../_classes/biz/proposal/proposal.php";

#====================================================================
# Request Parameter
#====================================================================

	$gp_no = trim($gp_no);
	$print_type = trim($print_type);
	$discount_percent = trim($discount_percent);

#===============================================================
# Get Search list count
#===============================================================

	$arr_rs = selectGoodsProposalByGpNo($conn, $gp_no);

	$GROUP_NO		= $arr_rs[0]["GROUP_NO"];
	$CP_NO			= $arr_rs[0]["CP_NO"];
	$GOODS_CATE		= $arr_rs[0]["GOODS_CATE"];
	$CP_NM			= getCompanyNameWithNoCode($conn, $CP_NO);
	$MEMO			= $arr_rs[0]["MEMO"];

	$arr_rs_goods = listGoodsProposalGoods($conn, $gp_no, 'N');

	$IS_NOT_SAME_PRICE = false;
	if (sizeof($arr_rs_goods) > 0) {
		for ($j = 0 ; $j < sizeof($arr_rs_goods); $j++) {
			$RETAIL_PRICE				= trim($arr_rs_goods[$j]["RETAIL_PRICE"]);
			$PROPOSAL_PRICE				= trim($arr_rs_goods[$j]["PROPOSAL_PRICE"]);

			if($RETAIL_PRICE != $PROPOSAL_PRICE) { 
				$IS_NOT_SAME_PRICE = true;
				break;
			}

		}
	}

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
	input[type=text] {display:none; width:90%;} 
	.request_date {width:95%; text-align:right;}
	.row {position:relative;}
	.extra_button {display:none; position:absolute; }
	.minus {color: red; cursor:pointer;}
</style>
<script>
	$(function(){
		$("td, th").on("click", function(){
			var source = $(this).find("span");
			var text_box = $(this).find("input[type=text]");
			source.hide();
			text_box.val(source.html()).show().focus();

		});

		$("input[type=text]").on("keydown", function(){
			$(this).parent().find("span").html($(this).val());

			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13'){

				var gpg_no = $(this).closest("tr").data("gpg_no");
				var column = $(this).closest("th").data("column");
				var value = $(this).val();

				(function() {
				  $.getJSON( "/manager/proposal/json_goods_proposal.php", {
					mode: "UPDATE_PROPOSAL_GOODS",
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

		$("input[type=text]").on("blur", function(){
			$("span").show();
			$("input[type=text]").hide();
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

<?
	//카테고리 추가로 인한 출력
	if($GOODS_CATE <> "") { 
		$CATEGORY_NAME = getCategoryNameOnly($conn, $GOODS_CATE);
?>
			<div class="sp10"></div>
			<table cellpadding="0" cellspacing="0" width="100%" class="colstable03">
				<tr>
					<th class="h1">
						제 안 서	( <?=$CATEGORY_NAME?> )
					</th>
				</tr>
			</table>
			<div class="sp10"></div>

			<table cellpadding="0" cellspacing="0" width="100%" class="colstable03">
				<colgroup>
					<col width="4%" />
					<col width="18%" />
					<col width="7%" />
					<col width="*" />
					<col width="7%" />
					<? if($IS_NOT_SAME_PRICE) { ?>
					<col width="14%" />
					<? } ?>
					<col width="14%" />
					<col width="14%" />
				</colgroup>
				<tr>
					<th>
						번호
					</th>
					<th>
						카테고리
					</th>
					<th>
						페이지
					</th>
					<th>
						상품명
					</th>
					<th>
						박스단위
					</th>
					<? if($IS_NOT_SAME_PRICE) { ?>
					<th>
						기프트넷단가
					</th>
					<? } ?>
					<th>
						제안가
					</th>
					<th>
						이미지
					</th>
				</tr>
				<?

				if (sizeof($arr_rs_goods) > 0) {


					//상품수량이 적을시 공백으로 줄 수 있는 적정한 수량이 13줄
					for ($j = 0 ; $j < (sizeof($arr_rs_goods) > 13 ? sizeof($arr_rs_goods) : 13); $j++) {

						$GPG_NO						= trim($arr_rs_goods[$j]["GPG_NO"]);
						$GOODS_NO					= trim($arr_rs_goods[$j]["GOODS_NO"]);
						$GOODS_CODE					= trim($arr_rs_goods[$j]["GOODS_CODE"]);
						$GOODS_NAME					= trim($arr_rs_goods[$j]["GOODS_NAME"]);
						$RETAIL_PRICE				= trim($arr_rs_goods[$j]["RETAIL_PRICE"]);
						$PROPOSAL_PRICE				= trim($arr_rs_goods[$j]["PROPOSAL_PRICE"]);
						$DELIVERY_CNT_IN_BOX		= trim($arr_rs_goods[$j]["DELIVERY_CNT_IN_BOX"]);
						$COMPONENT					= trim($arr_rs_goods[$j]["COMPONENT"]);

						$GOODS_CATE					= trim($arr_rs_goods[$j]["GOODS_CATE"]);
						$PAGE						= trim($arr_rs_goods[$j]["PAGE"]);

						$CATEGORY_NAME = getCategoryNameOnly($conn, $GOODS_CATE);
	
						if($print_type == "DETAIL_MIDDLE_SIZE"){
							//단가할인율 적용
							$PROPOSAL_PRICE = $RETAIL_PRICE * (100 - $discount_percent) / 100.0;
							//끝자리 하나 가져옴
							$units = $PROPOSAL_PRICE - ((int)($PROPOSAL_PRICE / 10) * 10);
							//7이상이면 +10 7미만이면 1의자리 버림
							if($units >= 7)
								$PROPOSAL_PRICE = ($PROPOSAL_PRICE - $units) + 10;
							else
								$PROPOSAL_PRICE -= $units;
						}

						if($RETAIL_PRICE <> "") 
							$RETAIL_PRICE = number_format($RETAIL_PRICE)." 원";
						
						if($PROPOSAL_PRICE <> "") 
							$PROPOSAL_PRICE = number_format($PROPOSAL_PRICE)." 원";
						
						$img_url	= getImage($conn, $GOODS_NO, "100", "100");

					if($GPG_NO != '') {
				?>
				<tr class="row" data-gpg_no="<?=$GPG_NO?>">
					<th><?=$j+1?></th>
					<th>
						<span><?=$CATEGORY_NAME?></span> 
					</th>
					<th data-column="PAGE">
						<span><?=$PAGE?></span> 
						<input type="text" value=""/>
					</th>
					<th data-column="GOODS_NAME">
						<span><?=$GOODS_NAME?></span> <?="[".$GOODS_CODE."]"?>
						<input type="text" value=""/>
					</th>
					<th data-column="DELIVERY_CNT_IN_BOX">
						<span><?=$DELIVERY_CNT_IN_BOX?></span>
						<input type="text" value=""/>
					</th>
					<? if($IS_NOT_SAME_PRICE) { ?>
					<th data-column="RETAIL_PRICE">
						<span><?=$RETAIL_PRICE?></span>
						<input type="text" value=""/>
					</th>
					<? } ?>
					<th data-column="PROPOSAL_PRICE">
						<span><?=$PROPOSAL_PRICE?></span>
						<input type="text" value=""/>
					</th>
					<th>
						<img src="<?=$img_url?>" style="max-width:100px; max-height:100px;"/>
					</th>
					
				</tr>
				<? } else { ?>
				<tr class="row">
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<? if($IS_NOT_SAME_PRICE) { ?>
					<th></th>
					<? } ?>
					<th></th>
					<th></th>
				</tr>
				<? } ?>
				<?
						
					}
				}
				
				?>
			</table>
			<div class="sp10"></div>

			<?=$MEMO?>
			<div class="sp20"></div>


<? } else { ?>
		<div class="sp10"></div>
			<table cellpadding="0" cellspacing="0" width="100%" class="colstable03">
				<tr>
					<th class="h1">
						제 안 서
					</th>
				</tr>
			</table>
			<div class="sp10"></div>

			<table cellpadding="0" cellspacing="0" width="100%" class="colstable03">
				<colgroup>
					<col width="4%" />
					<col width="*" />
					<col width="25%" />
					<col width="7%" />
					<? if($IS_NOT_SAME_PRICE) { ?>
					<col width="14%" />
					<? } ?>
					<col width="14%" />
					<col width="14%" />
				</colgroup>
				<tr>
					<th>
						번호
					</th>
					<th>
						상품명
					</th>
					<th>
						구성
					</th>
					<th>
						박스단위
					</th>
					<? if($IS_NOT_SAME_PRICE) { ?>
					<th>
						기프트넷단가
					</th>
					<? } ?>
					<th>
						제안가
					</th>
					<th>
						이미지
					</th>
				</tr>
				<?

				if (sizeof($arr_rs_goods) > 0) {
					//상품수량이 적을시 공백으로 줄 수 있는 적정한 수량이 13줄
					for ($j = 0 ; $j < (sizeof($arr_rs_goods) > 13 ? sizeof($arr_rs_goods) : 13); $j++) {

						$GPG_NO						= trim($arr_rs_goods[$j]["GPG_NO"]);
						$GOODS_NO					= trim($arr_rs_goods[$j]["GOODS_NO"]);
						$GOODS_CODE					= trim($arr_rs_goods[$j]["GOODS_CODE"]);
						$GOODS_NAME					= trim($arr_rs_goods[$j]["GOODS_NAME"]);
						$RETAIL_PRICE				= trim($arr_rs_goods[$j]["RETAIL_PRICE"]);
						$PROPOSAL_PRICE				= trim($arr_rs_goods[$j]["PROPOSAL_PRICE"]);
						$DELIVERY_CNT_IN_BOX		= trim($arr_rs_goods[$j]["DELIVERY_CNT_IN_BOX"]);
						$COMPONENT					= trim($arr_rs_goods[$j]["COMPONENT"]);

						if($print_type == "DETAIL_MIDDLE_SIZE"){
							//단가할인율 적용
							$PROPOSAL_PRICE = $RETAIL_PRICE * (100 - $discount_percent) / 100.0;
							//끝자리 하나 가져옴
							$units = $PROPOSAL_PRICE - ((int)($PROPOSAL_PRICE / 10) * 10);
							//7이상이면 +10 7미만이면 1의자리 버림
							if($units >= 7)
								$PROPOSAL_PRICE = ($PROPOSAL_PRICE - $units) + 10;
							else
								$PROPOSAL_PRICE -= $units;
						}

						if($RETAIL_PRICE <> "") 
							$RETAIL_PRICE = number_format($RETAIL_PRICE)." 원";
						
						if($PROPOSAL_PRICE <> "") 
							$PROPOSAL_PRICE = number_format($PROPOSAL_PRICE)." 원";

						$img_url	= getImage($conn, $GOODS_NO, "100", "100");

					if($GPG_NO != '') {
				?>
				<tr class="row" data-gpg_no="<?=$GPG_NO?>">
					<th><?=$j+1?></th>
					<th data-column="GOODS_NAME">
						<span><?=$GOODS_NAME?></span> <?="[".$GOODS_CODE."]"?>
						<input type="text" value=""/>
					</th>
					<th data-column="COMPONENT">
						<span><?=$COMPONENT?></span>
						<input type="text" value=""/>
					</th>
					<th data-column="DELIVERY_CNT_IN_BOX">
						<span><?=$DELIVERY_CNT_IN_BOX?></span>
						<input type="text" value=""/>
					</th>
					<?if($IS_NOT_SAME_PRICE) { ?>
					<th data-column="RETAIL_PRICE">
						<span><?=$RETAIL_PRICE?></span>
						<input type="text" value=""/>
					</th>
					<? } ?>
					<th data-column="PROPOSAL_PRICE">
						<span><?=$PROPOSAL_PRICE?></span>
						<input type="text" value=""/>
					</th>
					<th>
						<img src="<?=$img_url?>" style="max-width:100px; max-height:100px;"/>
					</th>
					
				</tr>
				<? } else { ?>
				<tr class="row">
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<? if($IS_NOT_SAME_PRICE) { ?>
					<th></th>
					<? } ?>
					<th></th>
					<th></th>
				</tr>
				<? } ?>
				<?
						
					}
				}
				
				?>
			</table>
			<div class="sp10"></div>

			<?=$MEMO?>
			<div class="sp20"></div>

<? } ?>

			
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