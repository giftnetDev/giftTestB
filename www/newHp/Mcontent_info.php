<?
	require "../_common/home_pre_setting.php";
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<?
	require "Mheader.php";

	function getCateCode($db)
    {
        $query = "SELECT CONCAT(DCODE_NM, '|', SUBSTRING(DCODE_EXT,-4,4)) AS CADALOG
                    FROM TBL_CODE_DETAIL
                   WHERE 1 =1
                     AND PCODE = 'HOME_BANNER'
                     AND DCODE = 'NEW_BANNER_TITLE'
                        ";

        $result = mysql_query($query,$db);
        $rows   = mysql_fetch_array($result);
        
        return $rows[0];
    }
?>
</head>
<body topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
<div class="wrap">
<?
	$bb_no = $no;
	$bb_code = "HOMEPAGE";
	$arr_rs = selectBoard($conn, $bb_code, $bb_no);
	

	$rs_bb_no					= trim($arr_rs[0]["BB_NO"]); 
	$rs_bb_code					= trim($arr_rs[0]["BB_CODE"]); 
	$rs_title					= SetStringFromDB($arr_rs[0]["TITLE"]); 
	//$rs_contents				= SetStringFromDB($arr_rs[0]["CONTENTS"]);
	$rs_contents				= trim($arr_rs[0]["CONTENTS"]);
	$rs_file_nm					= trim($arr_rs[0]["FILE_NM"]); 
	$rs_file_rnm				= trim($arr_rs[0]["FILE_RNM"]); 
	$rs_file_size				= trim($arr_rs[0]["FILE_SIZE"]); 
	$rs_file_etc				= trim($arr_rs[0]["FILE_ETC"]); 
	$rs_cate_01					= trim($arr_rs[0]["CATE_01"]); 
	$rs_cate_02					= trim($arr_rs[0]["CATE_02"]); 
	$rs_cate_03					= trim($arr_rs[0]["CATE_03"]); 
	$rs_cate_04					= trim($arr_rs[0]["CATE_04"]); 
	$rs_keyword					= trim($arr_rs[0]["KEYWORD"]); 
	$rs_use_tf					= trim($arr_rs[0]["USE_TF"]); 
	$rs_del_tf					= trim($arr_rs[0]["DEL_TF"]); 
	$rs_reg_adm					= trim($arr_rs[0]["REG_ADM"]); 

	$content = $rs_contents;

	$cadalog = getCateCode($conn);

	$arr = explode('|', $cadalog);

	$cateNm = $arr[0];
	$cateCd = $arr[1];

?>
<!-- °íÁ¤ ÄÁÅÙÃ÷ -->
	<div class="detail_page" style="padding-left: 20px; padding-right: 20px;">		
		<h4><?=$rs_title?> 
			<span><?=$rs_cate_01?></span>
		</h4>
		<div class = "lcenter" style="word-break: normal; text-align: left;">
			<img src="img/company_mobile.png" alt="" style="margin:0px auto;display:block">
			<br>
			<div class="contents_c">
			<?	if($bb_no != "4")	
			{
				echo $content;
			} 
			else 
			{?>
				<a href="Mmenu_list.php?cate=<?=$cateCd?>"> <?=$cateNm?> </a>
			<?}
			?>	
			</div>
		</div>
	</div>				
<!-- // °íÁ¤ ÄÁÅÙÃ÷ -->
</div>
<?
	require "Mfooter.php";
?>
</body>
</html>

 