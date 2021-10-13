
<?php
# =============================================================================
# File Name    : goods_proposal.php
# Modlue       : 
# Writer       : Sungwook Min
# Create Date  : 2015.09.14
# Modify Date  : 
#	Copyright  : Copyright @C&C Corp. All Rights Reserved.
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
	require "../_classes/com/etc/etc.php";
	require "../_classes/biz/goods/goods.php";
  

#====================================================================
# Request Parameter
#====================================================================
	
	$decripted_key = explode('|', base64_decode(urldecode($key)));

	$goods_proposal_no		= trim($decripted_key[0]);
	$goods_no		= trim($decripted_key[1]);


#====================================================================
# DML Process
#====================================================================

	if($goods_proposal_no != '')
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
	}

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
<link rel="stylesheet" href="jquery/jquery-ui.min.css" type="text/css" />

</head>
<body id="popup_file" style="margin:10px auto 50px;">

<form name="frm" method="post">
	<h1 style="text-align:center; margin: 20px auto;">상품 제안서</h1>
	<div id="postsch">
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
						<th>상품명(브랜드포함)</th>
						<td colspan="3" class="line">
							<?=$rs_title?>
						</td>
					</tr>
					<tr>
						<th>상품 이미지 URL</th>
						<td colspan="3" class="line">
							<?
								if ($rs_image_url <> "") {
							?>
							<img src="<?=$rs_image_url?>" alt="<?=$rs_image_url?>" alt="이미지"><br>
							<?
								}
							?>
						</td>
					</tr>
					<tr>
						<th>구성(세부내역)</th>
						<td colspan="3" class="line">
							<?=$rs_component?>
						</td>
					</tr>
					<tr>
						<th>용도 및 특징</th>
						<td colspan="3" class="line">
							<?=str_replace("\n", "<br/>", $rs_description)?>
						</td>
					</tr>
					<tr>
						<th>제안가(VAT포함)</th>
						<td colspan="3" class="line">
							<?=number_format($rs_proposal_price)?> 원
						</td>
					</tr>

					<tr>
						<th>소비자가</th>
						<td class="line">
							<?=number_format($rs_retail_price)?> 원
						</td>
						<th>박스입수</th>
						<td class="line">
							<?=$rs_delivery_cnt_in_box?> 개
						</td>
					</tr>
					<tr>
						<th>제조원</th>
						<td  class="line">
							<?=$rs_manufacturer?>
						</td>

						<th>원산지</th>
						<td class="line">
							<?=$rs_origin?>
						</td>
					</tr>

				</tbody>
			</table>
				
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