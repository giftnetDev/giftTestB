<?session_start();?>
<?
# =============================================================================
# File Name    : event_write.php
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

#====================================================================
	$savedir1 = $g_physical_path."upload_data/event";
#====================================================================

		$event_nm		= SetStringToDB($event_nm);
		$contents		= SetStringToDB($contents);
		
		$file_nm					= upload($_FILES[file_nm], $savedir1, 10 , array('gif', 'jpeg', 'jpg','png','zip','exe','ini','dll'));
		$file_rnm					= $_FILES[file_nm][name];

		$file_size = $_FILES[file_nm]['size'];
		$file_ext  = end(explode('.', $_FILES[file_nm]['name']));

		$file_nm2					= upload($_FILES[file_nm2], $savedir1, 10 , array('gif', 'jpeg', 'jpg','png','zip','exe','ini','dll'));
		$file_rnm2					= $_FILES[file_nm2][name];

		$file_size2 = $_FILES[file_nm2]['size'];
		$file_ext2  = end(explode('.', $_FILES[file_nm2]['name']));

		$result =  insertEvent($conn, $g_site_no, $event_type, $event_nm, $event_from, $event_to, $event_result, $file_nm, $file_rnm, $file_path, $file_size, $file_ext, $file_nm2, $file_rnm2, $file_path2, $file_size2, $file_ext2, $event_state, $use_tf, $s_adm_no);

	}

	if ($mode == "U") {

#====================================================================
		$savedir1 = $g_physical_path."upload_data/event";
#====================================================================
		# file업로드
		switch ($flag01) {
			case "insert" :

				$file_nm					= upload($_FILES[file_nm], $savedir1, 10 , array('gif', 'jpeg', 'jpg','png','zip','exe','ini','dll'));
				$file_rnm					= $_FILES[file_nm][name];

				$file_size = $_FILES[file_nm]['size'];
				$file_ext  = end(explode('.', $_FILES[file_nm]['name']));

			break;
			case "keep" :

				$file_nm		= $old_file_nm;
				$file_rnm		= $old_file_rnm;

				$file_size	= $old_file_size;
				$file_ext		= $old_file_ext;

			break;
			case "delete" :

				$file_nm	= "";
				$file_rnm	= "";

				$file_size = "";
				$file_ext  = "";

			break;
			case "update" :

				$file_nm					= upload($_FILES[file_nm], $savedir1, 10 , array('gif', 'jpeg', 'jpg','png','zip','exe','ini','dll'));
				$file_rnm					= $_FILES[file_nm][name];

				$file_size = $_FILES[file_nm]['size'];
				$file_ext  = end(explode('.', $_FILES[file_nm]['name']));

			break;
		}

		switch ($flag02) {
			case "insert" :

				$file_nm2					= upload($_FILES[file_nm2], $savedir1, 10 , array('gif', 'jpeg', 'jpg','png','zip','exe','ini','dll'));
				$file_rnm2					= $_FILES[file_nm2][name];

				$file_size2 = $_FILES[file_nm2]['size'];
				$file_ext2  = end(explode('.', $_FILES[file_nm2]['name']));

			break;
			case "keep" :

				$file_nm2		= $old_file_nm2;
				$file_rnm2		= $old_file_rnm2;

				$file_size2	= $old_file_size2;
				$file_ext2		= $old_file_ext2;

			break;
			case "delete" :

				$file_nm2	= "";
				$file_rnm2	= "";

				$file_size2 = "";
				$file_ext2  = "";

			break;
			case "update" :

				$file_nm2					= upload($_FILES[file_nm2], $savedir1, 10 , array('gif', 'jpeg', 'jpg','png','zip','exe','ini','dll'));
				$file_rnm2					= $_FILES[file_nm2][name];

				$file_size2 = $_FILES[file_nm2]['size'];
				$file_ext2  = end(explode('.', $_FILES[file_nm2]['name']));

			break;
		}

		$result = updateEvent($conn, $g_site_no, $event_type, $event_nm, $event_from, $event_to, $event_result, $file_nm, $file_rnm, $file_path, $file_size, $file_ext, $file_nm2, $file_rnm2, $file_path2, $file_size2, $file_ext2, $event_state, $use_tf, $s_adm_no, $event_no);
	}


	if ($mode == "T") {

		updateEventUseTF($conn, $use_tf, $s_adm_no, $event_no);

	}

	if ($mode == "D") {
		
		
	//	$row_cnt = count($chk);
		
	//	for ($k = 0; $k < $row_cnt; $k++) {
		
	//		$tmp_banner_no = $chk[$k];

			$result = deleteEvent($conn, $s_adm_no, $event_no);
		
//		}
	}

	if ($mode == "S") {

		$arr_rs = selectEvent($conn, $event_no);
		

		$rs_event_no				= trim($arr_rs[0]["EVENT_NO"]); 
		$rs_event_type			= trim($arr_rs[0]["EVENT_TYPE"]); 
		$rs_event_nm				= SetStringFromDB($arr_rs[0]["EVENT_NM"]); 
		$rs_event_from			= trim($arr_rs[0]["EVENT_FROM"]); 
		$rs_event_to				= trim($arr_rs[0]["EVENT_TO"]); 
		$rs_event_result		= trim($arr_rs[0]["EVENT_RESULT"]); 
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

	}

	if ($rs_event_from == "") {
		$rs_event_from = date("Y-m-d",strtotime("0 day"));
	}

	if ($rs_event_to == "") {
		$rs_event_to = date("Y-m-d",strtotime("0 day"));
	}

	if ($rs_event_result == "") {
		$rs_event_result = date("Y-m-d",strtotime("0 day"));
	}

	if ($result) {
		$strParam = $strParam."?nPage=".$nPage."&nPageSize=".$nPageSize."&con_event_state=".$con_event_state."&con_use_tf=".$con_use_tf."&search_field=".$search_field."&search_str=".$search_str;
?>	
<script language="javascript">
		alert('정상 처리 되었습니다.');
		document.location.href = "event_list.php<?=$strParam?>";
</script>
<?
		exit;
	}	
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script language="javascript" type="text/javascript" src="../js/common.js"></script>
<script language="javascript" type="text/javascript" src="../js/calendar.js"></script>

<script language="javascript" type="text/javascript">


function js_list() {
	var frm = document.frm;
		
	frm.method = "get";
	frm.action = "event_list.php";
	frm.submit();
}


function js_save() {

	var frm = document.frm;
	var event_no = "<?= $event_no ?>";
	
	frm.event_nm.value = frm.event_nm.value.trim();
	
	if (isNull(frm.event_nm.value)) {
		alert('이벤트명을 입력해주세요.');
		frm.event_nm.focus();
		return ;		
	}

	if (document.frm.rd_use_tf == null) {
		//alert(document.frm.rd_use_tf);
	} else {
		if (frm.rd_use_tf[0].checked == true) {
			frm.use_tf.value = "Y";
		} else {
			frm.use_tf.value = "N";
		}
	}

	if (document.frm.rd_event_type == null) {
		//alert(document.frm.rd_use_tf);
	} else {
		if (frm.rd_event_type[0].checked == true) {
			frm.event_type.value = "C";
		} else if (frm.rd_event_type[1].checked == true) {
			frm.event_type.value = "A";
		} else {
			frm.event_type.value = "M";
		}
	}

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


function file_change(file) { 
	document.getElementById("file_name").value = file; 
}

function file_change2(file) { 
	document.getElementById("file_name2").value = file; 
}

function js_delete() {

	var frm = document.frm;

	bDelOK = confirm('자료를 삭제 하시겠습니까?');
	
	if (bDelOK==true) {
		frm.mode.value = "D";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

}

function js_fileView(obj,idx) {
	
	var frm = document.frm;
	
	if (idx == 01) {
		if (obj.selectedIndex == 2) {
			document.getElementById("file_change").style.display = "inline";
		} else {
			document.getElementById("file_change").style.display = "none";
		}
	}

	if (idx == 02) {
		if (obj.selectedIndex == 2) {
			document.getElementById("file_change2").style.display = "inline";
		} else {
			document.getElementById("file_change2").style.display = "none";
		}
	}

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
?>


		</td>
		<td class="contentarea">

      
      <!-- S: mwidthwrap -->
      <div id="mwidthwrap">
        <h2>이벤트 관리</h2>  
        <table cellpadding="0" cellspacing="0" class="colstable">
        <colgroup>
          <col width="120" />
          <col width="*" />
        </colgroup>
 				<tr> <!-- 가장 마지막에 오는 tr 엘리먼트에 end 클래스 붙여주세요 -->
					<th>공개 여부</th>
					<td class="choices">
						<input type="radio" class="radio" name="rd_use_tf" value="Y" <? if (($rs_use_tf =="Y") || ($rs_use_tf =="")) echo "checked"; ?>> 공개 <span style="width:20px;"></span>
						<input type="radio" class="radio" name="rd_use_tf" value="N" <? if ($rs_use_tf =="N") echo "checked"; ?>> 비공개
						<input type="hidden" name="use_tf" value="<?= $rs_use_tf ?>"> 
					</td>
				</tr>

				<tr>
          <th>이벤트 명</th>
          <td>
						<input type="text" class="txt" name="event_nm" value="<?=$rs_event_nm?>" style="width: 95%;" />
					</td>
				</tr>

				<tr>
          <th>배너</th>
          <td>

						<?
							if (strlen($rs_file_nm) > 3) {
						?>
							<img src="/upload_data/event/<?=$rs_file_nm?>" alt="<?=$rs_file_rnm?>"><br /><br />
							&nbsp;&nbsp;
							<select name="flag01" style="width:70px;" onchange="javascript:js_fileView(this,'01')">
								<option value="keep">유지</option>
								<option value="delete">삭제</option>
								<option value="update">수정</option>
							</select>
							
							<input type="hidden" name="old_file_nm" value="<?= $rs_file_nm?>">
							<input type="hidden" name="old_file_rnm" value="<?= $rs_file_rnm?>">
							<input type="hidden" name="old_file_size" value="<?= $rs_file_size?>">
							<input type="hidden" name="old_file_ext" value="<?= $rs_file_ext?>">

							<div id="file_change" style="display:none;">
							<input type="text" id="file_name" disabled="disabled" class="txt" style="width: 60%;"/> 
							<span id="file_box"> 
								<img src="../images/admin/btn_filesch.gif" class="btn_sch" alt="찾아보기" />
								<span id="file_box2"> 
									<input type="file" id="file" name="file_nm" onchange="file_change(this.value)" /> 
								</span> 
							</span>
							</div>

						<?
							} else {	
						?>
						<input type="text" id="file_name" disabled="disabled" class="txt" style="width: 80%;"/> 
						<span id="file_box"> 
							<img src="../images/admin/btn_filesch.gif" class="btn_sch" alt="찾아보기" />
							<span id="file_box2"> 
								<input type="file" id="file" name="file_nm" onchange="file_change(this.value)" /> 
							</span> 
						</span>
							<input type="hidden" name="old_file_nm" value="">
							<input type="hidden" name="old_file_rnm" value="">
							<input type="hidden" name="old_file_size" value="">
							<input type="hidden" name="old_file_ext" value="">
							<input TYPE="hidden" name="flag01" value="insert">

						<?
							}	
						?>

					</td>
        </tr>

				<tr>
          <th>기간</th>
          <td>
						<input type="text" class="txt" name="event_from" value="<?=$rs_event_from?>" style="width: 65px;" readonly="1">
						<a href="javascript:show_calendar('document.frm.event_from', document.frm.event_from.value);"><img src="../images/calendar/cal.gif" width="16" height="16" border="0" alt="Click Here to Pick up the timestamp"></a>
						~
						<input type="text" class="txt" name="event_to" value="<?=$rs_event_to?>" style="width: 65px;" readonly="1">
						<a href="javascript:show_calendar('document.frm.event_to', document.frm.event_to.value);"><img src="../images/calendar/cal.gif" width="16" height="16" border="0" alt="Click Here to Pick up the timestamp"></a>
					</td>
				</tr>

				<tr>
          <th>결과 발표일</th>
          <td>
						<input type="text" class="txt" name="event_result" value="<?=$rs_event_result?>" style="width: 65px;" readonly="1">
						<a href="javascript:show_calendar('document.frm.event_result', document.frm.event_result.value);"><img src="../images/calendar/cal.gif" width="16" height="16" border="0" alt="Click Here to Pick up the timestamp"></a>
					</td>
				</tr>

        <tr>
          <th>유형</th>
					<td class="choices">
						<input type="radio" class="radio" name="rd_event_type" value="C" <? if (($rs_event_type =="C") || ($rs_event_type =="")) echo "checked"; ?>> 단순 응모형 <span style="width:20px;"></span>
						<input type="radio" class="radio" name="rd_event_type" value="A" <? if ($rs_event_type =="A") echo "checked"; ?>> 답변 입력형
						<input type="radio" class="radio" name="rd_event_type" value="M" <? if ($rs_event_type =="M") echo "checked"; ?>> 추천 메일형
						<input type="hidden" name="event_type" value="<?= $rs_event_type ?>"> 
					</td>
        </tr>

				<tr class="end">
          <th>내용</th>
          <td>

						<?
							if (strlen($rs_file_nm2) > 3) {
						?>
							<img src="/upload_data/event/<?=$rs_file_nm2?>" alt="<?=$rs_file_rnm2?>"><br /><br />
							&nbsp;&nbsp;
							<select name="flag02" style="width:70px;" onchange="javascript:js_fileView(this,'02')">
								<option value="keep">유지</option>
								<option value="delete">삭제</option>
								<option value="update">수정</option>
							</select>
							
							<input type="hidden" name="old_file_nm2" value="<?= $rs_file_nm2?>">
							<input type="hidden" name="old_file_rnm2" value="<?= $rs_file_rnm2?>">
							<input type="hidden" name="old_file_size2" value="<?= $rs_file_size2?>">
							<input type="hidden" name="old_file_ext2" value="<?= $rs_file_ext2?>">

							<div id="file_change2" style="display:none;">
							<input type="text" id="file_name2" disabled="disabled" class="txt" style="width: 60%;"/> 
							<span id="file_box_ex"> 
								<img src="../images/admin/btn_filesch.gif" class="btn_sch" alt="찾아보기" />
								<span id="file_box_ex2"> 
									<input type="file" id="file_ex" name="file_nm2" onchange="file_change2(this.value)" /> 
								</span> 
							</span>
							</div>

						<?
							} else {	
						?>

						<input type="text" id="file_name2" disabled="disabled" class="txt" style="width: 80%;"/> 
						<span id="file_box_ex"> 
							<img src="../images/admin/btn_filesch.gif" class="btn_sch" alt="찾아보기" />
							<span id="file_box_ex2"> 
								<input type="file" id="file_ex" name="file_nm2" onchange="file_change2(this.value)" /> 
							</span> 
						</span>
							<input type="hidden" name="old_file_nm2" value="">
							<input type="hidden" name="old_file_rnm2" value="">
							<input type="hidden" name="old_file_size2" value="">
							<input type="hidden" name="old_file_ext2" value="">
							<input TYPE="hidden" name="flag02" value="insert">

						<?
							}	
						?>

					</td>
        </tr>

        </table>
        <div class="btnright">
          <a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
          <a href="javascript:js_list();"><img src="../images/admin/btn_list.gif" alt="목록" /></a>
					<? if ($event_no <> "") {?>
          <a href="javascript:js_delete();"><img src="../images/admin/btn_delete.gif" alt="삭제" /></a>
					<? } ?>
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