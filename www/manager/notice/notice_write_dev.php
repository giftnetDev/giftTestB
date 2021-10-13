<?session_start();?>
<?
# =============================================================================
# File Name    : download_write.php
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
	require "../../_classes/biz/board/new_board.php";

	$bb_code = "BBS";

	$mm_subtree	 = "3";
#====================================================================
# DML Process
#====================================================================

	if ($mode == "I") {

#====================================================================
	$savedir1 = $g_physical_path."upload_data/board";
#====================================================================

		$title		= SetStringToDB($title);
		$contents = SetStringToDB($contents);
		
		$file_nm					= upload($_FILES[file_nm], $savedir1, 1000 , array('gif', 'jpeg', 'jpg','png','zip','hwp','ppt','pptx','doc','docx','xls','xlsx','asf','wmv','avi','mp4','mpg','mpeg','pdf'));

		$file_nm2					= upload($_FILES[file_nm2], $savedir1, 1000 , array('gif', 'jpeg', 'jpg','png','zip','hwp','ppt','pptx','doc','docx','xls','xlsx','asf','wmv','avi','mp4','mpg','mpeg','pdf'));

		$file_rnm					= $_FILES[file_nm][name];

		$file_rnm2					= $_FILES[file_nm2][name];

		$file_size = $_FILES[file_nm]['size'];

		$file_size2 = $_FILES[file_nm2]['size'];

		$file_ext  = end(explode('.', $_FILES[file_nm][name]));
		$file_ext2  = end(explode('.', $_FILES[file_nm2][name]));

		//$banner_real_img = str_replace(".".$file_ext,"",$_FILES[banner_img]['name']).".".$file_ext;
		

		$result =  insertBoard($conn, $bb_code, $file_nm2, $file_rnm2, $file_size2, $file_ext2, $writer_nm, $writer_pw, $email, $homepage, $title, $ref_ip, $recomm, $contents, $file_nm, $file_rnm, $file_path, $file_size, $file_ext, $keyword, $comment_tf,  $use_tf, $s_adm_no);
	}

	if ($mode == "U") {

#====================================================================
		$savedir1 = $g_physical_path."upload_data/board";
#====================================================================
		# file업로드
		switch ($flag01) {
			case "insert" :

				$file_nm					= upload($_FILES[file_nm], $savedir1, 1000 , array('gif', 'jpeg', 'jpg','png','zip','hwp','ppt','pptx','doc','docx','xls','xlsx','asf','wmv','avi','mp4','mpg','mpeg','pdf'));
				$file_rnm					= $_FILES[file_nm][name];

				$file_size = $_FILES[file_nm]['size'];
				$file_ext  = end(explode('.', $_FILES[file_nm][name]));

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

				$file_nm					= upload($_FILES[file_nm], $savedir1, 1000 , array('gif', 'jpeg', 'jpg','png','zip','hwp','ppt','pptx','doc','docx','xls','xlsx','asf','wmv','avi','mp4','mpg','mpeg','pdf'));
				$file_rnm					= $_FILES[file_nm][name];
				$file_size = $_FILES[file_nm]['size'];
				$file_ext  = end(explode('.', $_FILES[file_nm][name]));

			break;
		}
		
		switch ($flag02) {
			case "insert" :

				$file_nm2					= upload($_FILES[file_nm2], $savedir1, 1000 , array('gif', 'jpeg', 'jpg','png','zip','hwp','ppt','pptx','doc','docx','xls','xlsx','asf','wmv','avi','mp4','mpg','mpeg','pdf'));
				$file_rnm2					= $_FILES[file_nm2][name];
				$file_size2 = $_FILES[file_nm2]['size'];
				$file_ext2  = end(explode('.', $_FILES[file_nm2][name]));

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

				$file_nm2					= upload($_FILES[file_nm2], $savedir1, 1000 , array('gif', 'jpeg', 'jpg','png','zip','hwp','ppt','pptx','doc','docx','xls','xlsx','asf','wmv','avi','mp4','mpg','mpeg','pdf'));
				$file_rnm2					= $_FILES[file_nm2][name];
				$file_size2 = $_FILES[file_nm2]['size'];
				$file_ext2  = end(explode('.', $_FILES[file_nm2][name]));

			break;
		}

		$result = updateBoard($conn, $file_nm2, $file_rnm2, $file_size2, $file_ext2, $writer_nm, $writer_pw, $email, $homepage, $title, $ref_ip, $recomm, $contents, $file_nm, $file_rnm, $file_path, $file_size, $file_ext, $keyword, $comment_tf, $use_tf, $s_adm_no, $bb_code, $bb_no);
	}


	if ($mode == "T") {

		updateBannerUseTF($conn, $use_tf, $s_adm_no, $bb_code, $bb_no);

	}

	if ($mode == "D") {
		
		
	//	$row_cnt = count($chk);
		
	//	for ($k = 0; $k < $row_cnt; $k++) {
		
	//		$tmp_banner_no = $chk[$k];

			$result = deleteBoard($conn, $s_adm_no, $bb_code, $bb_no);
		
//		}
	}

	if ($mode == "S") {

		$arr_rs = selectBoard($conn, $bb_code, $bb_no);
		

		$rs_bb_no						= trim($arr_rs[0]["BB_NO"]); 
		$rs_bb_code					= trim($arr_rs[0]["BB_CODE"]); 
		$rs_title						= SetStringFromDB($arr_rs[0]["TITLE"]); 
		$rs_contents				= SetStringFromDB($arr_rs[0]["CONTENTS"]); 
		$rs_file_nm					= trim($arr_rs[0]["FILE_NM"]); 
		$rs_file_rnm				= trim($arr_rs[0]["FILE_RNM"]); 
		$rs_file_size				= trim($arr_rs[0]["FILE_SIZE"]); 
		$rs_file_etc				= trim($arr_rs[0]["FILE_EXT"]); 
		$rs_file_nm2				= trim($arr_rs[0]["CATE_01"]); 
		$rs_file_rnm2				= trim($arr_rs[0]["CATE_02"]); 
		$rs_file_size2			= trim($arr_rs[0]["CATE_03"]); 
		$rs_file_etc2				= trim($arr_rs[0]["CATE_04"]); 
		$rs_keyword					= trim($arr_rs[0]["KEYWORD"]); 
		$rs_comment_tf			= trim($arr_rs[0]["COMMENT_TF"]); 
		$rs_use_tf					= trim($arr_rs[0]["USE_TF"]); 
		$rs_del_tf					= trim($arr_rs[0]["DEL_TF"]); 

	}

	if ($result) {
		$strParam = $strParam."?nPage=".$nPage."&nPageSize=".$nPageSize."&con_cate_01=".$con_cate_01."&con_cate_02=".$con_cate_02."&con_cate_03=".$con_cate_03."&search_field=".$search_field."&search_str=".$search_str;
?>	
<script language="javascript">
		alert('정상 처리 되었습니다.');
		document.location.href = "notice_list.php<?=$strParam?>";
</script>
<?
		exit;
	}	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="STYLESHEET" type="text/css" href="../css/bbs.css" />
<link rel="STYLESHEET" type="text/css" href="../css/layout.css" />

<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/calendar.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->

<script language="javascript" type="text/javascript">


function js_list() {
	var frm = document.frm;
		
	frm.keyword.value = "";
	frm.title.value = "";
	frm.contents.value = "";

	frm.method = "get";
	frm.action = "notice_list.php";
	frm.submit();
}


function js_save() {

	var frm = document.frm;
	var bb_no = "<?= $bb_no ?>";
	
	frm.title.value = frm.title.value.trim();
	
	if (isNull(frm.title.value)) {
		alert('제목을 입력해주세요.');
		frm.title.focus();
		return ;		
	}

	/*if (frm.cate_01.value == "") {
		alert('제품군을 선택해주세요.');
		frm.cate_01.focus();
		return ;		
	}

	if (frm.cate_02.value == "") {
		alert('제품을 선택해주세요.');
		frm.cate_02.focus();
		return ;		
	}

	if (frm.cate_03.value == "") {
		alert('OS를 선택해주세요.');
		frm.cate_03.focus();
		return ;		
	}*/

	if (document.frm.rd_use_tf == null) {
		//alert(document.frm.rd_use_tf);
	} else {
		if (frm.rd_use_tf[0].checked == true) {
			frm.use_tf.value = "Y";
		} else {
			frm.use_tf.value = "N";
		}
	}

	if (document.frm.rd_comment_tf == null) {
		//alert(document.frm.rd_use_tf);
	} else {
		if (frm.rd_comment_tf[0].checked == true) {
			frm.comment_tf.value = "Y";
		} else {
			frm.comment_tf.value = "N";
		}
	}

	if (isNull(bb_no)) {
		frm.mode.value = "I";
	} else {
		frm.mode.value = "U";
		frm.bb_no.value = frm.bb_no.value;
	}

	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();

}

function js_view(rn, seq) {

	var frm = document.frm;
		
	frm.seq_no.value = seq;
	frm.mode.value = "S";
	frm.target = "";
	frm.method = "get";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();
		
}


function file_change(file) { 
	document.getElementById("file_name").value = file; 
}


function js_delete() {

	var frm = document.frm;
//	var chk_cnt = 0;

//	check=document.getElementsByName("chk[]");
	
//	for (i=0;i<check.length;i++) {
//		if(check.item(i).checked==true) {
//			chk_cnt++;
//		}
//	}
	
//	if (chk_cnt == 0) {
//		alert("선택 하신 자료가 없습니다.");
//	} else {

		bDelOK = confirm('자료를 삭제 하시겠습니까?');
		
		if (bDelOK==true) {
			frm.mode.value = "D";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}

//	}
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

function js_cate_01 () {

	var frm = document.frm;
	var obj = "cate_02";
	obj = eval("frm."+obj);
	clear_select(obj);

	frm.target = "ifr_hidden";
	frm.action = "../../_common/get_next_value.php";
	frm.submit();
}

function clear_select(obj){
	sel_len = obj.length;
	for(i = sel_len ; i > 0; i--) {
		obj.options[i] = null;
	}
	return ;
}

function add_select(value, text, index){

	var obj = eval("document.frm.cate_02");
		
	if (obj != null) {
		obj.options[index] = new Option(text, value);
	}
}

</script>

</head>



<body id="bg" onresize="BodyMinSize();">
<div id="wrap">
<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="" />
<input type="hidden" name="seq_no" value="" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="bb_no" value="<?=$bb_no?>" />
<input type="hidden" name="bb_code" value="<?=$bb_code?>" />
<input type="hidden" name="nPage" value="<?=$nPage?>" />
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>" />
<input type="hidden" name="writer_nm" value="<?=$s_adm_name?>" />
<div id="adminwrap">

<?
	#====================================================================
	# common top_area
	#====================================================================

	require "../../_common/top_area.php";

	#====================================================================
	# common left_area
	#====================================================================

	require "../../_common/left_area.php";
?>

	<div id="contents">
		<p><a href="/">홈</a> &gt; 공모전 게시판 관리</p>

		<div id="tit01">
			<h2>공지사항</h2>
		</div>
		
		<div id="bbsWrite">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<colgroup>
					<col width="120" />
					<col width="*" />
				</colgroup>
				<thead>
					<tr>
						<td class="lpd20">제목</td>
						<td colspan="2"><input type="text" class="box01" name="title" value="<?=$rs_title?>" style="width: 60%;" /></td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="lpd20">첨부1</td>
						<td colspan="2">
						<?
							if (strlen($rs_file_nm) > 3) {
								$sql="select count(*) from TBL_DOWNLOAD_CNT where bb_code='$bb_code' and bb_no='$bb_no' and file_name ='$rs_file_nm'";
								$result = mysql_query($sql);
								$rows   = mysql_fetch_array($result);
								$total  = $rows[0];
						?>
							<a href="/_common/download_file.php?menu=board&bb_code=<?= $rs_bb_code ?>&bb_no=<?= $rs_bb_no ?>&field=file_nm"><?= $rs_file_rnm ?></a> (<?=$total?>)
							&nbsp;&nbsp;
							<select name="flag01" style="width:70px;" onchange="javascript:js_fileView(this,'01')">
								<option value="keep">유지</option>
								<option value="delete">삭제</option>
								<option value="update">수정</option>
							</select>
							
							<input type="hidden" name="old_file_nm" value="<?= $rs_file_nm?>">
							<input type="hidden" name="old_file_rnm" value="<?= $rs_file_rnm?>">
							<input type="hidden" name="old_file_size" value="<?= $rs_file_size?>">
							<input type="hidden" name="old_file_ext" value="<?= $rs_file_etc?>">

							<div id="file_change" style="display:none;">
								<input type="file" id="file" name="file_nm" class="box01" /> 
							</div>

						<?
							} else {
						?>
							<input type="file" id="file" name="file_nm" class="box01" /> 
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
						<td class="lpd20">첨부2</td>
						<td colspan="2">
						<?
							if (strlen($rs_file_nm2) > 3) {
								$sql="select count(*) from TBL_DOWNLOAD_CNT where bb_code='$bb_code' and bb_no='$bb_no' and file_name ='$rs_file_nm2'";
								$result = mysql_query($sql);
								$rows   = mysql_fetch_array($result);
								$total2  = $rows[0];
						?>
							<a href="/_common/download_file.php?menu=board&bb_code=<?= $rs_bb_code ?>&bb_no=<?= $rs_bb_no ?>&field=CATE_02"><?= $rs_file_rnm2 ?></a> (<?=$total2?>)
							&nbsp;&nbsp;
							<select name="flag02" style="width:70px;" onchange="javascript:js_fileView(this,'02')">
								<option value="keep">유지</option>
								<option value="delete">삭제</option>
								<option value="update">수정</option>
							</select>
							
							<input type="hidden" name="old_file_nm2" value="<?= $rs_file_nm2?>">
							<input type="hidden" name="old_file_rnm2" value="<?= $rs_file_rnm2?>">
							<input type="hidden" name="old_file_size2" value="<?= $rs_file_size2?>">
							<input type="hidden" name="old_file_ext2" value="<?= $rs_file_etc2?>">

							<div id="file_change2" style="display:none;">
								<input type="file" id="file2" name="file_nm2" class="box01" /> 
							</div>

						<?
							} else {
						?>
							<input type="file" id="file2" name="file_nm2" class="box01" /> 
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
					<tr>
						<td class="lpd20">내용</td>
						<td colspan="2" class="subject"><textarea class="box01" cols="100" rows="15" name="contents"><?=$rs_contents?></textarea></td>
					</tr>
					<tr>
						<td class="lpd20">키워드</td>
						<td colspan="2"><input type="text" class="box01" name="keyword" value="<?=$rs_keyword?>" style="width: 60%;" />
						&nbsp;<b>키워드는 ‘,’로 구분합니다.</b></td>
					</tr>
					<tr>
						<td class="lpd20">댓글 사용 여부</td>
						<td colspan="2">
							<input type="radio" class="radio" name="rd_comment_tf" value="Y" <? if ($rs_comment_tf =="Y") echo "checked"; ?>> 사용 <span style="width:20px;"></span>
							<input type="radio" class="radio" name="rd_comment_tf" value="N" <? if (($rs_comment_tf =="N") || ($rs_comment_tf =="")) echo "checked"; ?>> 미사용
							<input type="hidden" name="comment_tf" value="<?= $rs_comment_tf ?>"> 
						</td>
					</tr>
					<tr>
						<td class="lpd20">공개 여부</td>
						<td colspan="2">
							<input type="radio" class="radio" name="rd_use_tf" value="Y" <? if (($rs_use_tf =="Y") || ($rs_use_tf =="")) echo "checked"; ?>> 공개 <span style="width:20px;"></span>
							<input type="radio" class="radio" name="rd_use_tf" value="N" <? if ($rs_use_tf =="N") echo "checked"; ?>> 비공개
							<input type="hidden" name="use_tf" value="<?= $rs_use_tf ?>"> 
						</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="10"></td>
					</tr>
				</tfoot>
			</table>
			<span class="btn_list">
				<a href="javascript:js_save();"><img src="../images/common/btn/btn_save.gif" alt="확인" /></a> 
				 <a href="javascript:js_list();"><img src="../images/common/btn/btn_list.gif" alt="목록" /></a>
				
				<a href="javascript:js_delete();"><img src="../images/common/btn/btn_delete.gif" alt="삭제" /></a>
				
			</span>
			<? if ($rs_comment_tf == "Y") { ?>
			<iframe src="ifrm_comment.php?parent_bb_code=<?=$rs_bb_code?>&parent_bb_no=<?=$rs_bb_no?>" name="ifrmhidden" id="ifr_comment" frameborder="no" width="100%" height="250" marginwidth="0" marginheight="0" border="0"></iframe>
			<? } ?>

		</div>
	</div>
	<div id="site_info">Copyright &copy; 2009 (재)아름지기 All Rights Reserved.</div>

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