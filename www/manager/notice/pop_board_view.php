<?session_start();?>
<?
# =============================================================================
# File Name    : pop_board_view.php
# 게시판 팝업 읽기
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "BO002"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/board/board.php";
#====================================================================
# DML Process
#====================================================================
	


	$result_view = viewChkBoard($conn,$bb_code, $bb_no);
	$arr_rs = selectBoard($conn, $bb_code, $bb_no);
	

	$rs_bb_no						= trim($arr_rs[0]["BB_NO"]); 
	$rs_bb_code					= trim($arr_rs[0]["BB_CODE"]); 
	$rs_title					= $arr_rs[0]["TITLE"]; 
	$rs_contents				= $arr_rs[0]["CONTENTS"]; 
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

	//$content  = html_entity_decode($rs_contents);
	$content  = $rs_contents;

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->

<script type="text/javascript">


</script>

<style>
	.addr_inp img {max-width:95%;}
</style>
</head>
<body id="popup_file">

<form name="frm" method="post">
<input type="hidden" name="cp_no" value="<?=$cp_no?>">
<input type="hidden" name="mode" value="">
<div id="popupwrap_file">
	<h1><?=$rs_title?></h1>
	<div id="postsch_code">
		
		<div class="addr_inp">
			<?=$content?>
		</div>
		<div class="sp20"></div>
	</div>
	<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
</div>
</form>
</body>
</html>
<?
#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);
?>