<?session_start();?>
<?
# =============================================================================
# File Name    : result_write.php
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

	$mm_subtree	 = "4";
#====================================================================
# DML Process
#====================================================================

	if ($mode == "I") {

		$result			= SetStringToDB($result);
		$result			=  insertEventResult($conn, $contents, $s_adm_no, $event_no);

	}

	if ($mode == "U") {

		$result			= SetStringToDB($result);
		$result			=  insertEventResult($conn, $contents, $s_adm_no, $event_no);

	}

	if ($mode == "S") {

		$arr_rs = selectEvent($conn, $event_no);
		

		$rs_event_no				= trim($arr_rs[0]["EVENT_NO"]); 
		$rs_event_type			= trim($arr_rs[0]["EVENT_TYPE"]); 
		$rs_event_nm				= SetStringFromDB($arr_rs[0]["EVENT_NM"]); 
		$rs_event_from			= trim($arr_rs[0]["EVENT_FROM"]); 
		$rs_event_to				= trim($arr_rs[0]["EVENT_TO"]); 
		$rs_event_result		= trim($arr_rs[0]["EVENT_RESULT"]); 
		$rs_result					= trim($arr_rs[0]["RESULT"]); 
		$rs_file_nm					= trim($arr_rs[0]["FILE_NM"]); 
		$rs_file_rnm				= trim($arr_rs[0]["FILE_RNM"]); 
		$rs_file_size				= trim($arr_rs[0]["FILE_SIZE"]); 
		$rs_file_etc				= trim($arr_rs[0]["FILE_ETC"]); 
		$rs_file_nm2				= trim($arr_rs[0]["FILE_NM2"]); 
		$rs_file_rnm2				= trim($arr_rs[0]["FILE_RNM2"]); 
		$rs_file_size2			= trim($arr_rs[0]["FILE_SIZE2"]); 
		$rs_file_etc2				= trim($arr_rs[0]["FILE_ETC2"]); 
		$rs_event_state			= trim($arr_rs[0]["EVENT_STATE"]); 
		$rs_use_tf					= trim($arr_rs[0]["USE_TF"]); 
		$rs_del_tf					= trim($arr_rs[0]["DEL_TF"]); 
		
		//echo $rs_result;
		$content  = $rs_result;

	}


	if ($result) {
		$strParam = $strParam."?nPage=".$nPage."&nPageSize=".$nPageSize."&event_no=".$event_no."&con_event_state=".$con_event_state."&con_use_tf=".$con_use_tf."&search_field=".$search_field."&search_str=".$search_str."&mode=S";
?>	
<script language="javascript">
	document.location.href = "result_write.php<?=$strParam?>";
</script>
<?
		exit;
	}	
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; chaset=<?=$g_chrset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script language="javascript" type="text/javascript" src="../js/common.js"></script>
<script language="javascript" type="text/javascript" src="../js/calendar.js"></script>

<script language="javascript" type="text/javascript">


function js_list() {
	var frm = document.frm;
		
	frm.method = "get";
	frm.action = "result_list.php";
	frm.submit();
}


function js_save() {

	var frm = document.frm;
	var event_no = "<?= $event_no ?>";

	frm.contents.value = SubmitHTML();
	
	if (isNull(event_no)) {
		frm.mode.value = "I";
	} else {
		frm.mode.value = "U";
		frm.event_no.value = frm.event_no.value;
	}

	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();

}

</script>

</head>
<body id="admin" onresize="BodyMinSize();">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="" />
<input type="hidden" name="seq_no" value="" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="event_no" value="<?=$event_no?>" />
<input type="hidden" name="nPage" value="<?=$nPage?>" />
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>" />

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
        <h2>이벤트 당첨결과 관리</h2>  
        <table cellpadding="0" cellspacing="0" class="colstable02">
        <colgroup>
          <col width="120" />
          <col width="*" />
        </colgroup>

				<tr>
          <th>이벤트 명</th>
          <td class="line">
						<?=$rs_event_nm?>
					</td>
				</tr>
				<tr>
          <th>기간</th>
          <td class="line">
						<?=$rs_event_from?> ~ <?=$rs_event_to?>
					</td>
				</tr>

				<tr>
          <th>결과 발표일</th>
          <td class="line">
						<?=$rs_event_result?>
					</td>
				</tr>
				<tr class="end">
          <th>당첨 결과 내용</th>
          <td class="contentswrite">
						<input type="hidden" name="contents" value="">
						<?= myEditor(1,'../../_common/editor','frm','content','100%','300');?>
					</td>
        </tr>

        </table>
        <div class="btnright">
          <a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
          <a href="javascript:js_list();"><img src="../images/admin/btn_list.gif" alt="목록" /></a>
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
<iframe src="" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>
<?
#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);
?>