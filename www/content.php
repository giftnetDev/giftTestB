<?
	require "_common/home_pre_setting.php";
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<?
	require "_common/v2_header.php";
?>
</head>
<body>
<?
	require "_common/v2_top.php";
?>
<?
	$bb_no = $no;
	$bb_code = "HOMEPAGE";
	$arr_rs = selectBoard($conn, $bb_code, $bb_no);
	

	$rs_bb_no						= trim($arr_rs[0]["BB_NO"]); 
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

?>
<!-- °íÁ¤ ÄÁÅÙÃ÷ -->
<div class="container page-content">
    <h5><?=$rs_title?> <span><?=$rs_cate_01?></span></h5>
    <div class="contents">
        <?=$content?>
    </div>
</div>
<!-- // °íÁ¤ ÄÁÅÙÃ÷ -->


<?
	require "_common/v2_footer.php";
?>
</body>
</html>

