<?session_start();?>
<?
# =============================================================================
# File Name    : pcode_list.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @C&C Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "SY002"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/syscode/syscode.php";

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

	if ($nPage <> "") {
		$nPage = (int)($nPage);
	} else {
		$nPage = 1;
	}

	if ($nPageSize <> "") {
		$nPageSize = (int)($nPageSize);
	} else {
		$nPageSize = 10;
	}

	$nPageBlock	= 10;

#===============================================================
# Get Search list count
#===============================================================

	$nListCnt =totalCntPcode($conn, $g_site_no, $use_tf, $del_tf, $search_field, $search_str);

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	#$del_tf = "Y";

	$arr_rs = listPcode($conn, $g_site_no, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nPageSize);

	#echo sizeof($arr_rs);
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; chaset=<?=$g_chrset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script language="javascript" type="text/javascript" src="../js/common.js"></script>

<script language="javascript" type="text/javascript" >


	function js_write() {
		var url = "pcode_write_popup.php";
		NewWindow(url, '대분류등록', '560', '313', 'NO');
	}

	function js_view(rn, seq) {

		var url = "pcode_write_popup.php?mode=S&pcode_no="+seq;
		NewWindow(url, '대분류조회', '560', '313', 'NO');
	}
	
	function js_view_dcode(rn, seq) {

		var url = "dcode_list_popup.php?mode=R&pcode_no="+seq;
		NewWindow(url, '세부분류조회', '560', '650', 'NO');
	}
	
	// 조회 버튼 클릭 시 
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

</script>

</head>
<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="rn" value="">
<input type="hidden" name="pcode_no" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>">

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

				<h2>코드 관리</h2>
				<div class="btnright"><a href="javascript:js_write();"><img src="../images/admin/btn_regist_02.gif" alt="등록" /></a></div>
				<div class="category_choice"><!--<select style="width: 100px;"><option>제품선택</option></select>-->&nbsp;</div>
				<table cellpadding="0" cellspacing="0" class="rowstable">
				<colgroup>
					<col width="5%">
					<col width="30%">
					<col width="30%">
					<col width="35%">
				</colgroup>
				<tr>
					<th>NO.</th>
					<th>코드</th>
					<th>코드명</th>
					<th class="end">메뉴</th>
				</tr>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							#rn, PCODE_NO, PCODE, PCODE_NM, PCODE_MEMO, PCODE_SEQ_NO, USE_TF, DEL_TF, 
							#REG_ADM, REG_DATE, UP_ADM, UP_DATED, DEL_ADM, DEL_DATE

							$rn							= trim($arr_rs[$j]["rn"]);
							$PCODE_NO				= trim($arr_rs[$j]["PCODE_NO"]);
							$PCODE					= trim($arr_rs[$j]["PCODE"]);
							$PCODE_NM				= trim($arr_rs[$j]["PCODE_NM"]);
							$PCODE_MEMO			= trim($arr_rs[$j]["PCODE_MEMO"]);
							$PCODE_SEQ_NO		= trim($arr_rs[$j]["PCODE_SEQ_NO"]);
							$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
							$DEL_TF					= trim($arr_rs[$j]["DEL_TF"]);
							$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);

							$REG_DATE = date("Y-m-d",strtotime($REG_DATE));
				
				?>

				<tr> 
					<td><?=$rn?></td>
					<td class="pname"><a href="javascript:js_view('<?= $rn ?>','<?= $PCODE_NO ?>');"><?= $PCODE ?></a></td>
					<td><?= $PCODE_NM ?></td>
					<td class="filedown"><a href="javascript:js_view_dcode('<?= $rn ?>','<?= $PCODE_NO ?>');">[세부분류코드]</a></td>
				</tr>
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
			</table>

					<!-- --------------------- 페이지 처리 화면 START -------------------------->
					<?
						# ==========================================================================
						#  페이징 처리
						# ==========================================================================
						if (sizeof($arr_rs) > 0) {
							#$search_field		= trim($search_field);
							#$search_str			= trim($search_str);
							$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str;

					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- 페이지 처리 화면 END -------------------------->
				<br />
				<div class="bottom_search">
					<select name="search_field" style="width:84px;">
						<option value="PCODE" <? if ($search_field == "PCODE") echo "selected"; ?> >코드</option>
						<option value="PCODE_NM" <? if ($search_field == "PCODE_NM") echo "selected"; ?> >코드명</option>
					</select>
					<input type="text" value="<?=$search_str?>" name="search_str" class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();"/>
					<a href="javascript:js_search();"><img src="../images/admin/btn_search.gif" class="sch" alt="Search" /></a>
				</div>      
			</div>
			<!-- // E: mwidthwrap -->

		</td>
	</tr>
	<!--
	<tr>
		<td colspan="2" height="70"><div class="copyright"><img src="../images/admin/copyright.gif" alt="" /></div></td>
	</tr>
	-->
	</table>
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