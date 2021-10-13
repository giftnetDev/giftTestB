<?session_start();?>
<?
# =============================================================================
# File Name    : mail_apply_list.php
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
	require "../../_classes/moneual/event/event.php";
	require "../../_classes/moneual/mailevent/mailevent.php";

//	if ($mode == "T") {
//		updateEventPickTF($conn, $pick_tf, $s_adm_no, $apply_no);
//	}

#====================================================================
# Request Parameter
#====================================================================

	$mm_subtree	 = "4";

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$con_event_no			= trim($con_event_no);

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

	$nListCnt =totalCntMailEvent($conn, $con_event_no, $con_use_tf, $del_tf, $search_field, $search_str);

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listMailEvent($conn, $con_event_no, $con_use_tf, $del_tf, $search_field, $search_str, $nPage, $nPageSize);


?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; chaset=<?=$g_chrset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script language="javascript" type="text/javascript" src="../js/common.js"></script>

<script language="javascript">

	function js_write() {
		document.location.href = "mail_apply_write.php";
	}

	function js_view(rn, mem_no) {

		var frm = document.frm;
		
		frm.m_no.value = mem_no;
		frm.mode.value = "S";
		frm.target = "_new";
		frm.method = "get";
		frm.action = "/manager/member/member_write.php";
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

function js_toggle(apply_no, pick_tf) {
	var frm = document.frm;

	bDelOK = confirm('담첨 여부를 변경 하시겠습니까?');
		
	if (bDelOK==true) {

		if (pick_tf == "Y") {
			pick_tf = "N";
		} else {
			pick_tf = "Y";
		}

		frm.apply_no.value = apply_no;
		frm.pick_tf.value = pick_tf;
		frm.mode.value = "T";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}
}

function js_con_event_no () {
	frm.nPage.value = "1";
	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();
}

function js_con_memeber_type () {
	frm.nPage.value = "1";
	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();
}

function js_con_pick_tf () {
	frm.nPage.value = "1";
	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();
}

function js_excel() {

	frm.nPage.value = "1";
	frm.target = "";
	frm.action = "mail_apply_excel_list.php";
	frm.submit();

}

</script>
</head>

<body id="admin" onresize="BodyMinSize();">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="rn" value="">
<input type="hidden" name="m_no" value="">
<input type="hidden" name="pick_tf" value="">
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

        <h2>이벤트 친구 추천 관리</h2>
        <div class="btnright"><a href="javascript:js_excel();"><img src="../images/admin/icon_file.gif"></a></div>
        <div class="category_choice">
					<br />
					<?= makeEventSelectBoxOnChange($conn, "con_event_no","355","이벤트명 선택","",$con_event_no)?>&nbsp;
				</div>     
        <table cellpadding="0" cellspacing="0" class="rowstable">
        <colgroup>
          <col width="5%" />
          <col width="7%" />
          <col width="7%" />
          <col width="10%" />
          <col width="20%" />
          <col/>
          <col width="10%" />
        </colgroup>
        <tr>
          <th>No.</th>
          <th>아이디</th>
          <th>성명</th>
          <th>연락처</th>
          <th>이벤트명</th>
					<th>추천 Email</th>
          <th class="end">응모일자</th>
        </tr>
							<?
								$nCnt = 0;
								
								if (sizeof($arr_rs) > 0) {
									
									for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
										
										$rn							= trim($arr_rs[$j]["rn"]);
										$EMAIL_EVENT_NO	= trim($arr_rs[$j]["EMAIL_EVENT_NO"]);
										$EVENT_NO				= trim($arr_rs[$j]["EVENT_NO"]);
										$EVENT_NM				= trim($arr_rs[$j]["EVENT_NM"]);
										$M_ID						= trim($arr_rs[$j]["M_ID"]);
										$M_NO						= trim($arr_rs[$j]["M_NO"]);
										$M_NM						= trim($arr_rs[$j]["M_NM"]);
										$HPHONE					= trim($arr_rs[$j]["HPHONE"]);
										$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);
										
										$REG_DATE = date("Y-m-d",strtotime($REG_DATE));
							
										if ($PICK_TF == "Y") {
											$STR_PICK_TF = "<font color='navy'>당첨</font>";
										} else {
											$STR_PICK_TF = "<font color='red'>미당첨</font>";
										}
										
							?>
        <tr> 
          <td class="filedown"><?= $rn ?></td>
          <td class="pname"><?=$M_ID?></td>
          <td class="filedown"><a href="javascript:js_view('<?=$rn?>','<?=$M_NO?>');"><?=$M_NM?></a></td>
          <td class="pname"><?=$HPHONE?></td>
          <td class="modeual_nm"><?=$EVENT_NM?></td>
          <td class="modeual_nm">
						<?
							$arr_rs_email = selectMailSendEvent($conn, $EMAIL_EVENT_NO, $g_site_no);
							
							if (sizeof($arr_rs_email) > 0) {
								for ($h = 0 ; $h < sizeof($arr_rs_email); $h++) {
									$RS_M_NM	= trim($arr_rs_email[$h]["M_NM"]);
									$RS_EMAIL	= trim($arr_rs_email[$h]["EMAIL"]);
									
									$arr_rs_mem = selectMemberAsEmail($conn, $RS_EMAIL);
									
									$x = 0; 
									$m_no	= trim($arr_rs_mem[$x]["m_no"]);
									
									if ($m_no == "") {
										echo $RS_M_NM." [".$RS_EMAIL."]&nbsp;";
									} else {
								?>
								<a href="javascript:js_view('<?=$rn?>','<?=$m_no?>');"><font color="orange"><?=$RS_M_NM?> [<?=$RS_EMAIL?>]</font></a>&nbsp;
								<?
									}
								}
							}
						?>
					</td>
          <td><?= $REG_DATE ?></td>
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
							$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&con_event_no=".$con_event_no."&con_member_type=".$con_member_type."&con_pick_tf=".$con_pick_tf;

					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- 페이지 처리 화면 END -------------------------->
				<br />
				<div class="bottom_search">
					<select name="search_field" style="width:84px;">
						<option value="A.MEMBER_NM" <? if ($search_field == "A.MEMBER_NM") echo "selected"; ?> >성명</option>
					</select>
					<input type="text" value="<?=$search_str?>" name="search_str" class="txt" />
					<a href="javascript:js_search();"><img src="../images/admin/btn_search.gif" class="sch" alt="Search" /></a>
				</div>      
			</div>
			<!-- // E: mwidthwrap -->

		</td>
	</tr>
	<tr>
		<td colspan="2" height="70"><div class="copyright"><img src="../images/admin/copyright.gif" alt="" /></div></td>
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
