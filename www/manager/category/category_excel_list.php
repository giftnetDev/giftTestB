<?session_start();?>
<?
# =============================================================================
# File Name    : category_list.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @C&C Corp. All Rights Reserved.
# =============================================================================

$file_name="카테고리-".date("Ymd").".xls";
header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
header( "Content-Disposition: attachment; filename=$file_name" );
header( "Content-Description: orion@giringrim.com" );

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "GD003"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/category/category.php";

#====================================================================
# Request Parameter
#====================================================================

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
	$del_tf = "N";
#============================================================
# Page process
#============================================================

#===============================================================
# Get Search list count
#===============================================================

	$arr_rs = listCategory($conn, $con_cate, $use_tf, $del_tf, $search_field, $search_str);

	#echo sizeof($arr_rs);
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
</head>
<body>
<h3>카테고리 리스트</h3>
<!-- List -->
<table border="1">
	<tr>
		<th>카테고리명</th>
		<th>카테고리코드</th>
		<th>카테고리설명</th>
	</tr>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							//category_NO, category_CD, category_NAME, category_URL, category_FLAG, category_SEQ01, category_SEQ02, category_SEQ03, category_RIGHT
							
							$CATE_NO				= trim($arr_rs[$j]["CATE_NO"]);
							$CATE_CD				= trim($arr_rs[$j]["CATE_CD"]);
							$CATE_NAME			= trim($arr_rs[$j]["CATE_NAME"]);
							$CATE_MEMO			= trim($arr_rs[$j]["CATE_MEMU"]);
							$CATE_FLAG			= trim($arr_rs[$j]["CATE_FLAG"]);
							$CATE_SEQ01			= trim($arr_rs[$j]["CATE_SEQ01"]);
							$CATE_SEQ02			= trim($arr_rs[$j]["CATE_SEQ02"]);
							$CATE_SEQ03			= trim($arr_rs[$j]["CATE_SEQ03"]);
							$CATE_SEQ04			= trim($arr_rs[$j]["CATE_SEQ04"]);
							$CATE_CODE			= trim($arr_rs[$j]["CATE_CODE"]);
							$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
							$DEL_TF					= trim($arr_rs[$j]["DEL_TF"]);
							$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);

							$REG_DATE = date("Y-m-d",strtotime($REG_DATE));

							if (strlen($CATE_CD) == 2) {
								$cate_str = "<font color='blue'>⊙ ".$CATE_NAME."</font>";
							} else {
								for ($menuspace = 0 ; $menuspace < strlen($CATE_CD) ;$menuspace++) {
									$cate_str = $cate_str ."&nbsp;";
								}

								if (strlen($CATE_CD) == 4) {
									$cate_str = $cate_str ."┗ <font color='navy'>".$CATE_NAME."</font>";
								} else if (strlen($CATE_CD) == 6) {
									$cate_str = $cate_str ."&nbsp;&nbsp;┗ <font color='gray'>".$CATE_NAME."</font>";
								} else if (strlen($CATE_CD) == 8) {
									$cate_str = $cate_str ."&nbsp;&nbsp;&nbsp;┗ <font color='gray'>".$CATE_NAME."</font>";
								}
							}

				?>
	<tr>
		<td><?=$cate_str?></td>
		<td style="mso-number-format:\@"><?=$CATE_CD ?></td>
		<td><?=$CATE_MEMU?></td>
	</tr>
				<?			
							$cate_str = "";
						}
					} else { 
				?> 
	<tr>
		<td height="50" align="center" colspan="9">등록된 내용이 없습니다</td>
	</tr>
				<? 
					}
				?>

</table>
			<!-- //List -->
			
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>
