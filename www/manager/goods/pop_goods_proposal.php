<?session_start();?>
<?
# =============================================================================
# File Name    : pop_goods_proposal.php
# Modlue       : 
# Writer       : Sungwook Min
# Create Date  : 2015.09.03
# Modify Date  : 
#	Copyright  : Copyright @C&C Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "GD004"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/goods/goods.php";
#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);
	
	$goods_proposal_no		= trim($goods_proposal_no);
	$goods_no		= trim($goods_no);


#====================================================================
# DML Process
#====================================================================

	if ($mode == "S") {

		if($goods_proposal_no <> '')
		{
			$arr_rs = selectGoodsProposal($conn, $goods_proposal_no);

			$rs_cp_no			    = trim($arr_rs[0]["CP_NO"]); 
			$rs_title			    = trim($arr_rs[0]["TITLE"]); 
			$rs_image_url			= trim($arr_rs[0]["IMAGE_URL"]); 
			$rs_component			= trim($arr_rs[0]["COMPONENT"]); 
			$rs_description			= trim($arr_rs[0]["DESCRIPTION"]); 
			$rs_proposal_price		= trim($arr_rs[0]["PROPOSAL_PRICE"]); 
			$rs_retail_price		= trim($arr_rs[0]["RETAIL_PRICE"]); 
			$rs_delivery_cnt_in_box	= trim($arr_rs[0]["DELIVERY_CNT_IN_BOX"]); 
			$rs_manufacturer		= trim($arr_rs[0]["MANUFACTURER"]); 
			$rs_origin				= trim($arr_rs[0]["ORIGIN"]); 
			$rs_use_tf				= trim($arr_rs[0]["USE_TF"]); 
		}else
		{
			if($goods_no <> '')
			{
				$arr_rs_goods = selectGoods($conn, $goods_no);

				//GOODS_CATE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, CATE_01, CATE_02, CATE_03, CATE_04, 
				//					PRICE, BUY_PRICE, SALE_PRICE, EXTRA_PRICE, STOCK_CNT, FSTOCK_CNT, BSTOCK_CNT, MSTOCK_CNT, 
				//					TAX_TF, IMG_URL, FILE_NM_100, FILE_RNM_100, FILE_PATH_100, FILE_SIZE_100, FILE_EXT_100, 
				//					FILE_NM_150, FILE_RNM_150, FILE_PATH_150, FILE_SIZE_150, FILE_EXT_150, CONTENTS, MEMO, 
				//					DELIVERY_CNT_IN_BOX, STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, SALE_SUSU

				$GOODS_NAME			    = trim($arr_rs_goods[0]["GOODS_NAME"]); 
				$GOODS_SUB_NAME			= trim($arr_rs_goods[0]["GOODS_SUB_NAME"]); 
				$IMG_URL			    = trim($arr_rs_goods[0]["IMG_URL"]); 
				$FILE_RNM_150			= trim($arr_rs_goods[0]["FILE_RNM_150"]); 
				$FILE_PATH_150			= trim($arr_rs_goods[0]["FILE_PATH_150"]); 
				$PRICE		            = trim($arr_rs_goods[0]["PRICE"]); 
				$DELIVERY_CNT_IN_BOX	= trim($arr_rs_goods[0]["DELIVERY_CNT_IN_BOX"]); 
				$CATE_02		        = trim($arr_rs_goods[0]["CATE_02"]); 

				$rs_title = $GOODS_NAME." ".$GOODS_SUB_NAME;
				$rs_image_url = ($IMG_URL != '' ? $IMG_URL : $FILE_PATH_150.$FILE_RNM_150);
				$rs_delivery_cnt_in_box = $DELIVERY_CNT_IN_BOX;
				$rs_manufacturer = $CATE_02;
				$rs_retail_price = $PRICE;


			}
		}
		
	}

	if ($mode == "I") {
			$cp_no			    = trim($cp_no); 
			$title			    = trim(SetStringToDB($title)); 
			$image_url			= trim(SetStringToDB($image_url)); 
			$component			= trim(SetStringToDB($component)); 
			$description		= trim(SetStringToDB($description)); 
			$proposal_price		= trim($proposal_price); 
			$retail_price		= trim($retail_price); 
			$delivery_cnt_in_box= trim($delivery_cnt_in_box); 
			$manufacturer		= trim(SetStringToDB($manufacturer)); 
			$origin				= trim(SetStringToDB($origin)); 
			$use_tf				= trim($use_tf); 

			$result = insertGoodsProposal($conn, $cp_no, $title, $image_url, $component, $description, $proposal_price, $retail_price, $delivery_cnt_in_box, $manufacturer, $origin, $use_tf, $goods_no, $goods_proposal_no, $s_adm_no);

	}

	if ($mode == "U") {
			$cp_no			    = trim($cp_no); 
			$title			    = trim(SetStringToDB($title)); 
			$image_url			= trim(SetStringToDB($image_url)); 
			$component			= trim(SetStringToDB($component)); 
			$description		= trim(SetStringToDB($description)); 
			$proposal_price		= trim($proposal_price); 
			$retail_price		= trim($retail_price); 
			$delivery_cnt_in_box= trim($delivery_cnt_in_box); 
			$manufacturer		= trim(SetStringToDB($manufacturer)); 
			$origin				= trim(SetStringToDB($origin)); 
			$use_tf				= trim($use_tf);

			$result = updateGoodsProposal($conn, $cp_no, $title, $image_url, $component, $description, $proposal_price, $retail_price, $delivery_cnt_in_box, $manufacturer, $origin, $use_tf, $goods_no, $goods_proposal_no, $s_adm_no);
	}

	if ($result) {
		$strParam = $strParam."?nPage=".$nPage."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&con_cp_type=".$con_cp_type;
		
		if ($mode == "I" || $mode == "U") {
?>	
<script language="javascript">
	alert('정상 처리 되었습니다.');
	opener.js_reload();
	self.close();
</script>
<?
		} else {
?>	
<script language="javascript">
		alert('정상 처리 되었습니다.');
		opener.location.href =  "goods_list.php<?=$strParam?>";
</script>
<?
		}
		exit;
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
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script language="javascript">

	// 저장 버튼 클릭 시 
	function js_save() {
		
		var frm = document.frm;
		var goods_proposal_no = "<?= $goods_proposal_no ?>";
		
		if (document.frm.rd_use_tf == null) {
			//alert(document.frm.rd_use_tf);
		} else {
			if (frm.rd_use_tf[0].checked == true) {
				frm.use_tf.value = "Y";
			} else {
				frm.use_tf.value = "N";
			}
		}

		if (isNull(goods_proposal_no)) {
			frm.mode.value = "I";
		} else {
			frm.mode.value = "U";
			frm.goods_proposal_no.value = frm.goods_proposal_no.value;
		}

		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}


</script>
</head>
<body id="popup_file">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="goods_no" value="<?= $goods_no ?>" />
<input type="hidden" name="goods_proposal_no" value="<?= $goods_proposal_no ?>" />

<div id="popupwrap_file">
	<h1>상품 제안서 등록/수정</h1>
	<div id="postsch">
		<h2>* 제안서 정보를 등록/수정 합니다.</h2>
		<div class="addr_inp">

			<table cellpadding="0" cellspacing="0" class="colstable02">

				<colgroup>
					<col width="15%" />
					<col width="35%" />
					<col width="15%" />
					<col width="35%" />
				</colgroup>
				<tbody>
				<tr>
						<th>제안사</th>
						<td class="line" colspan="3">
							<? if ($s_adm_cp_type == "운영") { ?>
							<input type="text" class="supplyer" style="width:210px" name="txt_cp_no" value="<?=getCompanyAutocompleteTextBox($conn,'판매',$rs_cp_no)?>" />
							<script>
							$(function() {
						     var cache = {};
								$( ".supplyer" ).autocomplete({
									source: function( request, response ) {
										var term = request.term;
										if ( term in cache ) {
											response( cache[term] );
											return;
										}
						 
										$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('판매'), request, function( data, status, xhr ) {
											cache[term] = data;
											response(data);
										});
									},
									minLength: 2,
									select: function( event, ui ) {
										$(".supplyer").val(ui.item.value);
										$("input[name=cp_no]").val(ui.item.id);
									}
								}).bind( "blur", function( event ) {
									var cp_no = $("input[name=cp_no]").val();
									if(cp_no != '') {
										$.getJSON( "../company/json_company_list.php?cp_no=" + cp_no, function(data) {
											if(data[0].CP_NO == 'undefined') {
												$("input[name=cp_no]").val('');
											} else {
												if(data[0].COMPANY != $(".supplyer").val())
												{

													$(".supplyer").val();
													$("input[name=cp_no]").val('');
												}
											}
										});
									} 
								});
							});
							</script>
							<input type="hidden" name="cp_no" value="<?=$rs_cp_no?>" />
							<? }?>
							
						</td>
						
					</tr>
					<tr>
						<th>상품명(브랜드포함)</th>
						<td colspan="3" class="line">
							<input type="text" class="txt" style="width:95%" name="title" required value="<?=$rs_title?>" />
						</td>
					</tr>
					<tr>
						<th>상품 이미지 URL</th>
						<td colspan="3" class="line">
							<?
								if ($rs_image_url <> "") {
							?>
							<img src="<?=$rs_image_url?>" alt="<?=$rs_image_url?>" width="100" alt="이미지"><br>
							<?
								}
							?>
							<input type="text" class="txt" style="width:75%" name="image_url" value="<?=$rs_image_url?>" />
						</td>
					</tr>
					<tr>
						<th>구성(세부내역)</th>
						<td colspan="3" class="line">
							<input type="text" class="txt" style="width:95%" name="component" required value="<?=$rs_component?>" />
						</td>
					</tr>
					<tr>
						<th>용도 및 특징</th>
						<td colspan="3" class="line">
							<textarea name="description" style="padding-left:0px;width:100%;height:100px;"><?=$rs_description?></textarea>
						</td>
					</tr>
					<tr>
						<th>제안가(VAT포함)</th>
						<td colspan="3" class="line">
							<input type="text" class="txt" style="width:95%" name="proposal_price" required value="<?=$rs_proposal_price?>" />
						</td>
					</tr>

					<tr>
						<th>소비자가</th>
						<td class="line">
							<input type="text" class="txt" style="width:90px" name="retail_price" value="<?=$rs_retail_price?>" onkeyup="return isNumber(this)" /> 원
						</td>
						<th>박스입수</th>
						<td class="line">
							<input type="text" class="txt" style="width:210px" name="delivery_cnt_in_box" value="<?=$rs_delivery_cnt_in_box?>" onkeyup="return isNumber(this)"/> 개
						</td>
					</tr>
					<tr>
						<th>제조원</th>
						<td  class="line">
							<input type="text" class="txt" style="width:90px" name="manufacturer" value="<?=$rs_manufacturer?>"  /> 
						</td>

						<th>원산지</th>
						<td class="line">
							<input type="text" class="txt" style="width:90px" name="origin" value="<?=$rs_origin?>"  /> 
						</td>
					</tr>
					<tr>
						<th>사용여부</th>
						<td colspan="3" class="line">
							<input type="radio" class="radio" name="rd_use_tf" value="Y" <? if (($rs_use_tf =="Y") || ($rs_use_tf =="")) echo "checked"; ?>> 사용함 <span style="width:20px;"></span>
							<input type="radio" class="radio" name="rd_use_tf" value="N" <? if ($rs_use_tf =="N") echo "checked"; ?>> 사용안함
							<input type="hidden" name="use_tf" value="<?= $rs_use_tf ?>"> 
						</td>
					</tr>
				</tbody>
			</table>
				
		</div>
		<div class="btn">
		<? if ($adm_no <> "" ) {?>
			<? if ($sPageRight_U == "Y") {?>
      <a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
			<? } ?>
		<? } else {?>
			<? if ($sPageRight_I == "Y") {?>
      <a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
			<? } ?>
		<? }?>
		</div>

	</div>
	<br />
	<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
</div>
<script type="text/javascript" src="../js/wrest.js"></script>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>