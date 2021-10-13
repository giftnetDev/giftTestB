<?session_start();?>
<?
# =============================================================================
# File Name    : popup_list.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @MONEUAL Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# common_header Check Session
#====================================================================
	include "../../_common/common_header.php"; 

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");
	
#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/moneual/popup/popup.php";


	if ($mode == "T") {
		updatePopupUseTF($conn, $g_site_no, $use_tf, $s_adm_no, $event_no);
	}

#====================================================================
# Request Parameter
#====================================================================

	$mm_subtree	 = "0";

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
	$this_date = date("Ymd",strtotime("0 day"));

	$nListCnt =totalCntPopup($conn, $g_site_no, $popup_type, $popup_from, $popup_to, $con_use_tf, $del_tf, $search_field, $search_str);

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listPopup($conn, $g_site_no, $popup_type, $popup_from, $popup_to, $con_use_tf, $del_tf, $search_field, $search_str, $nPage, $nPageSize);


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
		document.location.href = "popup_write.php";
	}

	function js_view(rn, seq) {

		var frm = document.frm;
		
		frm.popup_no.value = seq;
		frm.mode.value = "S";
		frm.target = "";
		frm.method = "get";
		frm.action = "popup_write.php";
		frm.submit();
		
	}

	// 조회 버튼 클릭 시 
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

function js_toggle(popup_no, use_tf) {
	var frm = document.frm;

	bDelOK = confirm('공개 여부를 변경 하시겠습니까?');
		
	if (bDelOK==true) {

		if (use_tf == "Y") {
			use_tf = "N";
		} else {
			use_tf = "Y";
		}

		frm.popup_no.value = popup_no;
		frm.use_tf.value = use_tf;
		frm.mode.value = "T";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}
}

</script>
</head>

<body id="admin" onresize="BodyMinSize();">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="rn" value="">
<input type="hidden" name="popup_no" value="">
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
        <h2>팝업관리</h2>

        <table cellpadding="0" cellspacing="0" class="rowstable">
        <colgroup>
          <col width="5%" />
          <col width="*%" />
          <col width="15%" />
          <col width="15%" />
          <col width="15%" />
        </colgroup>
        <tr>
          <th>No.</th>
          <th>제목</th>
          <th>게시 시작일</th>
          <th>게시 종료일</th>
          <th class="end">사용여부</th>
        </tr>
							<?
								$nCnt = 0;
								
								if (sizeof($arr_rs) > 0) {
									
									for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
										
										$rn							= trim($arr_rs[$j]["rn"]);
										$POPUP_NO				= trim($arr_rs[$j]["POPUP_NO"]);
										$POPUP_NM				= trim($arr_rs[$j]["POPUP_NM"]);
										$POPUP_FROM			= trim($arr_rs[$j]["POPUP_FROM"]);
										$POPUP_TO				= trim($arr_rs[$j]["POPUP_TO"]);
										$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
										$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);
										
										$REG_DATE = date("Y-m-d",strtotime($REG_DATE));
							
										if ($USE_TF == "Y") {
											$STR_USE_TF = "<font color='navy'>사용</font>";
										} else {
											$STR_USE_TF = "<font color='red'>사용안함</font>";
										}
										
										$from_date = str_replace("-","",$POPUP_FROM);
										$to_date = str_replace("-","",$POPUP_TO);
										
										if (($from_date <= $this_date) && ($to_date >= $this_date)) {
											$str_state = "진행중";
										} else if ($to_date < $this_date) {
											$str_state = "진행완료";
										} else {
											$str_state = "진행예정";
										}
							?>


        <tr> 
          <td class="filedown"><?=$rn?></td>
          <td class="modeual_nm"><a href="javascript:js_view('<?=$rn?>','<?=$POPUP_NO?>');"><?=$POPUP_NM?></a></td>
          <td><?=$POPUP_FROM?></td>
          <td><?=$POPUP_TO?></td>
          <td><?=$STR_USE_TF?></td>
        </tr>

							<?			
									}
								} else { 
							?> 
								<tr>
									<td height="50" align="center" colspan="5">자료가 없습니다. </td>
								</tr>
							<? 
								}
							?>
<!--
        <tr class="end"> 
          <td class="tit">팝업관리 제목영역제목영역제목영역제목영역</td>
          <td>2009-08-08</td>
          <td>2009-08-08</td>
          <td>사용</td>
        </tr>
-->
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

				
				<div class="btnright">
          <a href="javascript:js_write();"><img src="../images/btn_regist_02.gif" alt="등록" /></a>
        </div> 
       </div>
       <!-- // E: mwidthwrap -->

    </td>
  </tr>
  <tr>
    <td colspan="2" height="70"><div class="copyright"><img src="../images/copyright.gif" alt="" /></div></td>
  </tr>
  </table>  
</div>

</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>
