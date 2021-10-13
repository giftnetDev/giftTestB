<?session_start();?>
<?
# =============================================================================
# File Name    : popup_write.php
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

	$mm_subtree	 = "0";
#====================================================================
# DML Process
#====================================================================

	if ($mode == "I") {

#====================================================================
	$savedir1 = $g_physical_path."upload_data/popup";
#====================================================================

		$popup_nm		= SetStringToDB($popup_nm);
		$contents		= SetStringToDB($contents);
		
		$file_nm					= upload($_FILES[file_nm], $savedir1, 10 , array('gif', 'jpeg', 'jpg','png','zip','exe','ini','dll'));
		$file_rnm					= $_FILES[file_nm][name];

		$file_size = $_FILES[file_nm]['size'];
		$file_ext  = end(explode('.', $_FILES[file_nm]['name']));

		$result =  insertPopup($conn, $g_site_no, $popup_type, $popup_nm, $popup_from, $popup_to, $contents, $popup_width, $popup_hieght, $popup_top, $popup_left, $scroll_tf, $cook_tf, $image_tf, $file_nm, $file_rnm, $file_path, $file_size, $file_ext, $hit_cnt, $url, $use_tf, $s_adm_no);

	}

	if ($mode == "U") {

#====================================================================
		$savedir1 = $g_physical_path."upload_data/popup";
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


		$result = updatePopup($conn, $g_site_no, $popup_type, $popup_nm, $popup_from, $popup_to, $contents, $popup_width, $popup_hieght, $popup_top, $popup_left, $scroll_tf, $cook_tf, $image_tf, $file_nm, $file_rnm, $file_path, $file_size, $file_ext, $url, $use_tf, $s_adm_no, $popup_no);
	
	}


	if ($mode == "T") {

		updatePopupUseTF($conn, $g_site_no, $use_tf, $s_adm_no, $popup_no);

	}

	if ($mode == "D") {
		
		
	//	$row_cnt = count($chk);
		
	//	for ($k = 0; $k < $row_cnt; $k++) {
		
	//		$tmp_banner_no = $chk[$k];

			$result = deletePopup($conn, $g_site_no, $s_adm_no, $popup_no);
		
//		}
	}

	if ($mode == "S") {

		$arr_rs = selectPopup($conn, $g_site_no, $popup_no);
		

		$rs_popup_no				= trim($arr_rs[0]["POPUP_NO"]); 
		$rs_popup_type			= trim($arr_rs[0]["POPUP_TYPE"]); 
		$rs_popup_nm				= SetStringFromDB($arr_rs[0]["POPUP_NM"]); 
		$rs_popup_from			= trim($arr_rs[0]["POPUP_FROM"]); 
		$rs_popup_to				= trim($arr_rs[0]["POPUP_TO"]); 
		$rs_contents				= SetStringFromDB($arr_rs[0]["CONTENTS"]); 
		$rs_popup_width			= trim($arr_rs[0]["POPUP_WIDTH"]); 
		$rs_popup_hieght		= trim($arr_rs[0]["POPUP_HIEGHT"]); 
		$rs_popup_top				= trim($arr_rs[0]["POPUP_TOP"]); 
		$rs_popup_left			= trim($arr_rs[0]["POPUP_LEFT"]); 
		$rs_scroll_tf				= trim($arr_rs[0]["SCROLL_TF"]); 
		$rs_cookie_tf				= trim($arr_rs[0]["SCROLL_TF"]); 
		$rs_image_tf				= trim($arr_rs[0]["IMAGE_TF"]); 
		$rs_file_nm					= trim($arr_rs[0]["FILE_NM"]); 
		$rs_file_rnm				= trim($arr_rs[0]["FILE_RNM"]); 
		$rs_file_size				= trim($arr_rs[0]["FILE_SIZE"]); 
		$rs_file_etc				= trim($arr_rs[0]["FILE_ETC"]); 
		$rs_url							= trim($arr_rs[0]["URL"]); 
		$rs_use_tf					= trim($arr_rs[0]["USE_TF"]); 
		$rs_del_tf					= trim($arr_rs[0]["DEL_TF"]); 

		$content = $rs_contents;

		//echo $rs_use_tf;

	}

	if ($rs_popup_from == "") {
		$rs_popup_from = date("Y-m-d",strtotime("0 day"));
	}

	if ($rs_popup_to == "") {
		$rs_popup_to = date("Y-m-d",strtotime("0 day"));
	}

	if ($result) {
		$strParam = $strParam."?nPage=".$nPage."&nPageSize=".$nPageSize."&con_use_tf=".$con_use_tf."&search_field=".$search_field."&search_str=".$search_str;
?>	
<script language="javascript">
		alert('정상 처리 되었습니다.');
		document.location.href = "popup_list.php<?=$strParam?>";
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
	frm.action = "popup_list.php";
	frm.submit();
}


function js_save() {

	var frm = document.frm;
	var popup_no = "<?= $popup_no ?>";
	
	frm.popup_nm.value = frm.popup_nm.value.trim();
	frm.popup_width.value = frm.popup_width.value.trim();
	frm.popup_hieght.value = frm.popup_hieght.value.trim();
	frm.popup_top.value = frm.popup_top.value.trim();
	frm.popup_left.value = frm.popup_left.value.trim();
	
	if (isNull(frm.popup_nm.value)) {
		alert('팝업명을 입력해주세요.');
		frm.popup_nm.focus();
		return ;		
	}

	if (isNull(frm.popup_width.value)) {
		alert('가로크기를 입력해주세요.');
		frm.popup_width.focus();
		return ;		
	}

	if (isNull(frm.popup_hieght.value)) {
		alert('세로크기를 입력해주세요.');
		frm.popup_hieght.focus();
		return ;		
	}

	if (isNull(frm.popup_top.value)) {
		alert('상단여백을 입력해주세요.');
		frm.popup_top.focus();
		return ;		
	}

	if (isNull(frm.popup_left.value)) {
		alert('죄측여백을 입력해주세요.');
		frm.popup_left.focus();
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

	if (document.frm.rd_scroll_tf == null) {
		//alert(document.frm.rd_use_tf);
	} else {
		if (frm.rd_scroll_tf[0].checked == true) {
			frm.scroll_tf.value = "Y";
		} else {
			frm.scroll_tf.value = "N";
		}
	}

	if (document.frm.rd_cook_tf == null) {
		//alert(document.frm.rd_use_tf);
	} else {
		if (frm.rd_cook_tf[0].checked == true) {
			frm.cook_tf.value = "Y";
		} else {
			frm.cook_tf.value = "N";
		}
	}

	if (document.frm.rd_image_tf == null) {
		//alert(document.frm.rd_use_tf);
	} else {
		if (frm.rd_image_tf[0].checked == true) {
			frm.image_tf.value = "Y";
		} else {
			frm.image_tf.value = "N";
		}
	}

	if (isNull(popup_no)) {
		frm.mode.value = "I";
	} else {
		frm.mode.value = "U";
		frm.popup_no.value = frm.popup_no.value;
	}

	//alert(frm.use_tf.value);

	frm.contents.value = SubmitHTML();

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
<input type="hidden" name="popup_no" value="<?=$popup_no?>" />
<input type="hidden" name="nPage" value="<?=$nPage?>" />
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>" />

<div id="adminwrap">
  
<?
	#====================================================================
	# common top_area
	#====================================================================

	require "../../_common/top_area.php";

	include_once('../../_common/editor/func_editor.php');
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
				<table cellpadding="0" cellspacing="0" class="colstable02">
        <colgroup>
          <col width="120" />
          <col width="*" />
        </colgroup>
 				<tr> <!-- 가장 마지막에 오는 tr 엘리먼트에 end 클래스 붙여주세요 -->
					<th>공개 여부</th>
					<td class="choices line">
						<input type="radio" class="radio" name="rd_use_tf" value="Y" <? if (($rs_use_tf =="Y") || ($rs_use_tf =="")) echo "checked"; ?>> 공개 <span style="width:20px;"></span>
						<input type="radio" class="radio" name="rd_use_tf" value="N" <? if ($rs_use_tf =="N") echo "checked"; ?>> 비공개
						<input type="hidden" name="use_tf" value="<?= $rs_use_tf ?>"> 
					</td>
				</tr>

        <tr>
          <th>제목</th>
          <td class="line"><input type="text" class="txt" name="popup_nm" value="<?=$rs_popup_nm?>" style="width: 88%;" /></td>
        </tr>
        <tr>
          <th>기간</th>
          <td class="line">
						<input type="text" class="txt" name="popup_from" value="<?=$rs_popup_from?>" style="width: 65px;" readonly="1">
						<a href="javascript:show_calendar('document.frm.popup_from', document.frm.popup_from.value);"><img src="../images/calendar/cal.gif" width="16" height="16" border="0" alt="Click Here to Pick up the timestamp"></a>
						~
						<input type="text" class="txt" name="popup_to" value="<?=$rs_popup_to?>" style="width: 65px;" readonly="1">
						<a href="javascript:show_calendar('document.frm.popup_to', document.frm.popup_to.value);"><img src="../images/calendar/cal.gif" width="16" height="16" border="0" alt="Click Here to Pick up the timestamp"></a>
					</td>
        </tr>
        <tr>
          <th>팝업창 크기</th>
          <td class="line">
						가로: <input type="text" class="txt" style="width: 50px;" name="popup_width" value="<?=$rs_popup_width?>" onkeyup="return isNumber(this)" /> 
						세로: <input type="text" class="txt" style="width: 50px;" name="popup_hieght" value="<?=$rs_popup_hieght?>" onkeyup="return isNumber(this)" />
					</td>
        </tr>
        <tr>
          <th>팝업창 위치</th>
          <td class="line">
						Top: <input type="text" class="txt" style="width: 50px;" name="popup_top" value="<?=$rs_popup_top?>" onkeyup="return isNumber(this)" /> 
						Left <input type="text" class="txt" style="width: 50px;" name="popup_left" value="<?=$rs_popup_left?>" onkeyup="return isNumber(this)" />
					</td>
        </tr>
        <tr>
          <th>스크롤 여부</th>
          <td class="choices line">
						<input type="radio" class="radio" name="rd_scroll_tf" value="Y" <? if (($rs_scroll_tf =="Y") || ($rs_scroll_tf =="")) echo "checked"; ?>> 사용 <span style="width:20px;"></span>
						<input type="radio" class="radio" name="rd_scroll_tf" value="N" <? if ($rs_scroll_tf =="N") echo "checked"; ?>> 사용안함
						<input type="hidden" name="scroll_tf" value="<?= $rs_scroll_tf ?>"> 
					</td>
        </tr>
        <tr>
          <th>재표시 여부</th>
          <td class="choices line">
						<input type="radio" class="radio" name="rd_cook_tf" value="Y" <? if (($rs_cookie_tf =="Y") || ($rs_cookie_tf =="")) echo "checked"; ?>> 사용 <span style="width:20px;"></span>
						<input type="radio" class="radio" name="rd_cook_tf" value="N" <? if ($rs_cookie_tf =="N") echo "checked"; ?>> 사용안함
						<input type="hidden" name="cook_tf" value="<?= $rs_cookie_tf ?>"> 
					</td>
        </tr>
        <tr>
          <th>이미지 여부</th>
          <td class="choices line">
						<input type="radio" class="radio" name="rd_image_tf" value="Y" <? if (($rs_image_tf =="Y") || ($rs_image_tf =="")) echo "checked"; ?>> 사용 <span style="width:20px;"></span>
						<input type="radio" class="radio" name="rd_image_tf" value="N" <? if ($rs_image_tf =="N") echo "checked"; ?>> 사용안함
						<input type="hidden" name="image_tf" value="<?= $rs_image_tf ?>"> 
					</td>
        </tr>
        <tr>
          <th>이동 페이지 링크</th>
          <td class="line"><input type="text" class="txt" name="url" value="<?=$rs_url?>" style="width: 88%;" /></td>
        </tr>

        <tr> 
          <th>파일첨부</th>
          <td class="line">
						이미지 일 경우 입력 
						
						<?
							if (strlen($rs_file_nm) > 3) {
						?>
							<img src="/upload_data/popup/<?=$rs_file_nm?>" alt="<?=$rs_file_rnm?>"><br /><br />
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
        <tr class="end"> <!-- 가장 마지막에 오는 tr 엘리먼트에 end 클래스 붙여주세요 -->
          <th>내 용</th>
          <td class="contentswrite">
						<input type="hidden" name="contents" value="">
						<?= myEditor(1,'../../_common/editor','frm','content','100%','300');?>
					</td>
        </tr>
        </table>

        <div class="btnright">
          <a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
          <a href="javascript:js_list();"><img src="../images/admin/btn_list.gif" alt="목록" /></a>
					<? if ($popup_no <> "") {?>
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