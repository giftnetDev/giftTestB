<?session_start();?>
<?
# =============================================================================
# File Name    : admingroup_list.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @아름지기 Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "AD003"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/admin/admin.php";

#====================================================================
# Request Parameter
#====================================================================

	$mm_subtree	 = "8";
	
	$result = false;

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	

	if ($mode == "I") {
		$result = insertAdminGroup($conn, $group_name, $use_tf, $s_adm_no);
		$nPage = "1";
	}

	if ($mode == "U") {
		$result = updateAdminGroup($conn, $group_name, $use_tf, $s_adm_no, $group_no);
	}

	if ($mode == "D") {
		$result = deleteAdminGroup($conn, $s_adm_no, $group_no);
		$nPage = "1";
	}

	if ($result) {
?>	
<script language="javascript">
		location.href =  '<?=$_SERVER[PHP_SELF]?>?nPage=<?=$nPage?>&nPageSize=<?=$nPageSize?>';
</script>
<?
		exit;
	}

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

	$nListCnt =totalCntAdminGroup($conn, $use_tf, $del_tf, $search_field, $search_str);

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	#$del_tf = "Y";

	$arr_rs = listAdminGroup($conn, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nPageSize);

	#echo sizeof($arr_rs);
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script language="javascript" type="text/javascript" src="../js/common.js"></script>

<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" >


	function js_write() {

		var frm = document.frm; 
		
		if (frm.group_no.value == "") {
		  frm.mode.value = "I";
		} else {
			frm.mode.value = "U";
		}

		if (frm.group_name.value == "") {
		  	alert("관리자 그룹명을 입력 하십시오."); //관리자 그룹명을 입력 하십시오.
			frm.group_name.focus();
			return;
		}

		frm.target = "";
		frm.method = "post";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}


	function js_view(group_no, group_name) {

		var frm = document.frm; 
		frm.group_no.value = group_no;
		frm.group_name.value = group_name;
		frm.text_mode.value = '[수정 모드]'; //수정 모드
		document.getElementById("btn_save").src = "../images/admin/btn_modify.gif";
		frm.mode.value = "U";
	}
	
	function js_cancel() {

		var frm = document.frm; 
		frm.group_no.value = "";
		frm.group_name.value = "";
		frm.text_mode.value = '[등록 모드]'; //수정 모드
		document.getElementById("btn_save").src = "../images/admin/btn_regist_02.gif";
		frm.mode.value = "I";
	}

	// 조회 버튼 클릭 시 
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_delete(group_no) {

		var frm = document.frm;

		bDelOK = confirm('자료를 삭제 하시겠습니까?\n해당 그룹에 속한 관리자도 같이 삭제 됩니다.');
		if (bDelOK==true) {
			frm.mode.value = "D";
			frm.group_no.value = group_no;
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

</script>

<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="rn" value="">
<input type="hidden" name="group_no" value="">
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
	include_once('../../_common/editor/func_editor.php');

?>
		</td>
		<td class="contentarea">

			
			<!-- S: mwidthwrap -->
			<div id="mwidthwrap">
				<h2>관리자 그룹 관리</h2>  


        <table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="15%" />
					<col width="85%" />
				</colgroup>
				<thead>
					<tr>
						<th>관리자 그룹명</th>
						<td>
							<input type="text" class="txt" style="width:75%" name="group_name" required/>
							<input type="text" class="txt" name="text_mode" style="width:75px" style="border:0px; soild #FFF" value="[ 등록모드 ]" readonly="">
						</td>
					</tr>
				</thead>
			</table>

			<div class="btnright">
				<? if ($sPageRight_I == "Y") {?>
				<a href="javascript:js_write();"><img id="btn_save" src="../images/admin/btn_regist_02.gif" alt="저장" /></a>
				<a href="javascript:js_cancel();"><img src="../images/admin/btn_cancel.gif" alt="취소" /></a>
				<? } ?>
			</div>
		</div>
		<div class="sp20"></div>


				<table cellpadding="0" cellspacing="0" class="rowstable">
				<colgroup>
					<col width="10%" />
					<col width="35%" />
					<col width="40%" />
					<col width="15%" />
				</colgroup>
				<thead>
					<tr>
						<th>번호</th>
						<th>그룹명</th>
						<th>메뉴설정</th>
						<th class="end">삭제</th>
					</tr>
				</thead>
				<tbody>

				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							#GROUP_NO, GROUP_NAME, GROUP_FLAG, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE

							$rn							= trim($arr_rs[$j]["rn"]);
							$GROUP_NO				= trim($arr_rs[$j]["GROUP_NO"]);
							$GROUP_NAME			= trim($arr_rs[$j]["GROUP_NAME"]);
							$GROUP_FLAG			= trim($arr_rs[$j]["GROUP_FLAG"]);
							$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
							$DEL_TF					= trim($arr_rs[$j]["DEL_TF"]);
							$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);

							$REG_DATE = date("Y-m-d",strtotime($REG_DATE));
				
				?>
					<tr>
						<td><?=$rn?></td>
						<td class="pname">
							<a href="javascript:js_view('<?=$GROUP_NO?>','<?=$GROUP_NAME?>');"><?=$GROUP_NAME?></a>
						</td>
						<td>
						<? if ($sPageRight_U == "Y") { ?>
							<a href="javascript:NewWindow('pop_menu_list.php?group_no=<?=$GROUP_NO?>','pop_menu_list','620','650','YES')">[ + ]</a>
						<? } ?>
						</td>
						<td class="filedown">
						<? if ($sPageRight_D == "Y") { ?>									 
							<a href="javascript:js_delete('<?=trim($GROUP_NO)?>');"><img src="../images/admin/btn_delete_s.gif" alt="삭제"></a>
						<? } ?>
						</td>
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
				</tbody>
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