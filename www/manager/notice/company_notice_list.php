<?session_start();?>
<?
# =============================================================================
# File Name    : company_notice_list.php
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "BO004"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/board/board.php";

	$bb_code = "CNOTICE";

	if ($mode == "T") {
		updateBoardUseTF($conn, $use_tf, $s_adm_no, $bb_code, $bb_no);
	}

#====================================================================
# Request Parameter
#====================================================================

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$con_cate_01 = trim($con_cate_01);
	$con_cate_02 = trim($con_cate_02);
	$con_cate_03 = trim($con_cate_03);

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

	if ($s_adm_cp_type == "운영") { 
		$sel_cp_no = "";
	} else {
		$sel_cp_no = $s_adm_com_code;
	}

	if ($s_adm_cp_type == "운영") { 
	
		$nListCnt =totalCntBoard($conn, $bb_code, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $keyword, $reply_state, $con_use_tf, $del_tf, $search_field, $search_str);

		$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

		if ((int)($nTotalPage) < (int)($nPage)) {
			$nPage = $nTotalPage;
		}

		$arr_rs = listBoard($conn, $bb_code, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $keyword, $reply_state, $con_use_tf, $del_tf, $search_field, $search_str, $nPage, $nPageSize);

	} else {

		$nListCnt =totalCntBoardCompany($conn, $bb_code, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $sel_cp_no, $keyword, $reply_state, $con_use_tf, $del_tf, $search_field, $search_str);

		$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

		if ((int)($nTotalPage) < (int)($nPage)) {
			$nPage = $nTotalPage;
		}

		$arr_rs = listBoardCompany($conn, $bb_code, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $sel_cp_no, $keyword, $reply_state, $con_use_tf, $del_tf, $search_field, $search_str, $nPage, $nPageSize);
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script language="javascript" type="text/javascript" src="../js/common.js"></script>

<script language="javascript">

	function js_write() {
		document.location.href = "company_notice_write.php";
	}

	function js_view(rn, bb_code, bb_no) {

		var frm = document.frm;
		
		frm.bb_code.value = bb_code;
		frm.bb_no.value = bb_no;
		frm.mode.value = "S";
		frm.target = "";
		frm.method = "get";
		frm.action = "company_notice_read.php";
		frm.submit();
		
	}

	// 조회 버튼 클릭 시 
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

function js_toggle(bb_code, bb_no, use_tf) {
	var frm = document.frm;

	bDelOK = confirm('공개 여부를 변경 하시겠습니까?');
		
	if (bDelOK==true) {

		if (use_tf == "Y") {
			use_tf = "N";
		} else {
			use_tf = "Y";
		}

		frm.bb_code.value = bb_code;
		frm.bb_no.value = bb_no;
		frm.use_tf.value = use_tf;
		frm.mode.value = "T";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}
}

function js_con_cate_01 () {
	frm.nPage.value = "1";
	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();
}

function js_con_cate_02 () {
	frm.nPage.value = "1";
	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();
}

function js_con_cate_03 () {
	frm.nPage.value = "1";
	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();
}
</script>
</head>

<body id="admin" onresize="BodyMinSize();">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="rn" value="">
<input type="hidden" name="bb_no" value="">
<input type="hidden" name="bb_code" value="<?=$bb_code?>">
<input type="hidden" name="use_tf" value="">
<input type="hidden" name="seq_no" value="" />
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

        <h2>업체 공지사항</h2>
        <div class="btnright">
			<? if ($s_adm_cp_type == "운영") { ?>
			<a href="javascript:js_write();"><img src="../images/admin/btn_regist_02.gif" alt="등록" /></a>
			<? } else { ?>
				&nbsp;
			<? } ?>
		</div>
        <div class="category_choice">
			&nbsp;
		</div>     
        <table cellpadding="0" cellspacing="0" class="rowstable">
        <colgroup>
          <col width="5%" />
          <col width="75%" />
          <col width="15%" />
          <col width="10%" />
        </colgroup>
        <tr>
          <th>No.</th>
          <th>제목</th>
          <th>등록일</th>
          <th class="end">조회수</th>
        </tr>
							<?
								$nCnt = 0;
								
								if (sizeof($arr_rs) > 0) {
									
									for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
										
										$rn							= trim($arr_rs[$j]["rn"]);
										$BB_NO					= trim($arr_rs[$j]["BB_NO"]);
										$BB_CODE				= trim($arr_rs[$j]["BB_CODE"]);
										$CATE_01				= trim($arr_rs[$j]["CATE_01"]);
										$CATE_02				= trim($arr_rs[$j]["CATE_02"]);
										$CATE_03				= trim($arr_rs[$j]["CATE_03"]);
										$CATE_04				= trim($arr_rs[$j]["CATE_04"]);
										$WRITER_NM			= trim($arr_rs[$j]["WRITER_NM"]);
										$TITLE					= SetStringFromDB($arr_rs[$j]["TITLE"]);
										$HIT_CNT				= trim($arr_rs[$j]["HIT_CNT"]);
										$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
										$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);
										
										$REG_DATE = date("Y-m-d",strtotime($REG_DATE));
							
										if ($USE_TF == "Y") {
											$STR_USE_TF = "<font color='navy'>공개</font>";
										} else {
											$STR_USE_TF = "<font color='red'>비공개</font>";
										}
							?>
        <tr> 
          <td><?= $rn ?></td>
          <td class="modeual_nm"><a href="javascript:js_view('<?=$rn?>','<?=$BB_CODE?>','<?=$BB_NO?>');"><?=$TITLE?></a></td>
          <td><?= $REG_DATE ?></td>
          <td class="filedown">
						<?=$HIT_CNT?>
					</td>
        </tr>
							<?			
									}
								} else { 
							?> 
								<tr>
									<td height="50" align="center" colspan="8">데이터가 없습니다. </td>
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
						<option value="TITLE" <? if ($search_field == "TITLE") echo "selected"; ?> >제목</option>
						<option value="CONTENTS" <? if ($search_field == "CONTENTS") echo "selected"; ?> >내용</option>
						<option value="WRITER_NM" <? if ($search_field == "WRITER_NM") echo "selected"; ?> >작성자</option>
					</select>
					<input type="text" value="<?=$search_str?>" name="search_str" class="txt" />
					<a href="javascript:js_search();"><img src="../images/admin/btn_search.gif" class="sch" alt="Search" /></a>
				</div>      
			</div>
			<!-- // E: mwidthwrap -->

		</td>
	</tr>
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
