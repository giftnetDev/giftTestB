<?session_start();?>
<?
# =============================================================================
# File Name    : qna_read.php
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
	$menu_right = "C0004"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

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
	require "../../_classes/biz/board/board.php";

	$bb_code = "QNA";
#====================================================================
# DML Process
#====================================================================

	if ($mode == "I") {

		$reply		= SetStringToDB($reply);
		$result = updateQnaAnswer($conn, $reply, $s_adm_no, 'Y', $bb_code, $bb_no); 

	}

	if ($mode == "U") {

		$reply		= SetStringToDB($reply);
		$result = updateQnaAnswer($conn, $reply, $s_adm_no, 'Y', $bb_code, $bb_no); 

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
		$rs_writer_nm				= trim($arr_rs[0]["WRITER_NM"]); 
		$rs_email						= trim($arr_rs[0]["EMAIL"]); 
		$rs_homepage				= trim($arr_rs[0]["HOMEPAGE"]); 
		$rs_title						= SetStringFromDB($arr_rs[0]["TITLE"]); 
		$rs_contents				= SetStringFromDB($arr_rs[0]["CONTENTS"]); 
		$rs_file_nm					= trim($arr_rs[0]["FILE_NM"]); 
		$rs_file_rnm				= trim($arr_rs[0]["FILE_RNM"]); 
		$rs_file_size				= trim($arr_rs[0]["FILE_SIZE"]); 
		$rs_file_etc				= trim($arr_rs[0]["FILE_ETC"]); 
		$rs_cate_01					= trim($arr_rs[0]["CATE_01"]); 
		$rs_cate_02					= trim($arr_rs[0]["CATE_02"]); 
		$rs_cate_03					= trim($arr_rs[0]["CATE_03"]); 
		$rs_cate_04					= trim($arr_rs[0]["CATE_04"]); 
		$rs_reply						= SetStringFromDB($arr_rs[0]["REPLY"]); 
		$rs_reply_adm				= trim($arr_rs[0]["REPLY_ADM"]); 
		$rs_reply_date			= trim($arr_rs[0]["REPLY_DATE"]); 
		$rs_reply_state			= trim($arr_rs[0]["REPLY_STATE"]); 
		$rs_keyword					= trim($arr_rs[0]["KEYWORD"]); 
		$rs_use_tf					= trim($arr_rs[0]["USE_TF"]); 
		$rs_del_tf					= trim($arr_rs[0]["DEL_TF"]); 
		$rs_reg_date				= trim($arr_rs[0]["REG_DATE"]); 
		$rs_reply_state			= trim($arr_rs[0]["REPLY_STATE"]);
		
		$rs_reg_date				= date("Y-m-d",strtotime($rs_reg_date));

		if ($rs_reply_state == "Y") {
			$str_reply_state = "<font color='navy'>답변완료</font>";
		} else {
			$str_reply_state = "<font color='red'>미답변</font>";
		}
		
		$del_tf = "N";
		
		# 이전글
		$arr_rs = selectPreBoard($conn, $bb_code, $bb_no, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $keyword, $reply_state, $con_use_tf, $del_tf, $search_field, $search_str);
	
		$rs_pre_bb_no					= trim($arr_rs[0]["BB_NO"]);
		$rs_pre_title					= SetStringFromDB($arr_rs[0]["TITLE"]);


		# 다음글
		$arr_rs = selectPostBoard($conn, $bb_code, $bb_no, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $keyword, $reply_state, $con_use_tf, $del_tf, $search_field, $search_str);
	
		$rs_post_bb_no					= trim($arr_rs[0]["BB_NO"]);
		$rs_post_title					= SetStringFromDB($arr_rs[0]["TITLE"]);

	}

	if ($result) {
		$strParam = $strParam."?nPage=".$nPage."&nPageSize=".$nPageSize."&con_cate_01=".$con_cate_01."&con_cate_02=".$con_cate_02."&con_cate_03=".$con_cate_03."&search_field=".$search_field."&search_str=".$search_str;
?>	
<script language="javascript">
		alert('정상 처리 되었습니다.');
		document.location.href = "qna_list.php<?=$strParam?>";
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

<script language="javascript" type="text/javascript">


function js_list() {
	var frm = document.frm;

	frm.reply.value = "";

	frm.method = "get";
	frm.action = "qna_list.php";
	frm.submit();
}


function js_save() {

	var frm = document.frm;
	var reply_state = "<?= $rs_reply_state ?>";
	
	frm.reply.value = frm.reply.value.trim();
	
	if (isNull(frm.reply.value)) {
		alert('답변을 입력해주세요.');
		frm.reply.focus();
		return ;		
	}

	if (reply_state == "N") {
		frm.mode.value = "I";
	} else {
		frm.mode.value = "U";
		frm.bb_no.value = frm.bb_no.value;
	}

	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();

}

function js_view(rn, bb_no) {

	var frm = document.frm;
		
	frm.bb_no.value = bb_no;
	frm.mode.value = "S";
	frm.target = "";
	frm.method = "get";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();
		
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

</script>

</head>

<body id="bg">
<div id="wrap">

<form name="frm" method="post">
<input type="hidden" name="rn" value="" />
<input type="hidden" name="seq_no" value="" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="bb_no" value="<?=$bb_no?>" />
<input type="hidden" name="bb_code" value="<?=$bb_code?>" />
<input type="hidden" name="nPage" value="<?=$nPage?>" />
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>" />
<input type="hidden" name="search_field" value="<?=$search_field?>">
<input type="hidden" name="search_str" value="<?=$search_str?>">

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
			<h2>Q&A 관리</h2>
		</div>

		<div id="bbsWrite">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">

				<colgroup>
					<col width="20%" />
					<col width="*" />
					<col width="20%" />
					<col width="*" />
				</colgroup>
				<thead>
					<tr>
						<td class="lpd20">질문 구분</td>
						<td colspan="3">
							<?=getDcodeName($conn,"QNA",$rs_cate_01)?>
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="lpd20">제목</td>
						<td colspan="3">
							<?=$rs_title?>
						</td>
					</tr>
					<tr>
						<td class="lpd20">성명</td>
						<td><?=$rs_writer_nm?></td>
						<td class="lpd20">이메일</td>
						<td><?=$rs_email?></td>
					</tr>
					<tr>
						<td class="lpd20">등록일</td>
						<td><?=$rs_reg_date?></td>
						<td class="lpd20">처리결과</td>
						<td><?=$str_reply_state?></td>
					</tr>
					<tr> <!-- 가장 마지막에 오는 tr 엘리먼트에 end 클래스 붙여주세요 -->
						<td colspan="4" class="subject" style="padding: 20px 20px 20px 20px">
							<?=nl2br($rs_contents)?>
						</td>
					</tr>
				</tbody>
			</table>

			<table cellpadding="0" cellspacing="0" border="0" width="100%">

				<colgroup>
					<col width="120" />
					<col width="*" />
				</colgroup>
				<thead>
				
					<tr>
						<td class="lpd20">제목</td>
						<td>
							답변 : <?=$rs_title?>
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="lpd20">답변자 이메일</td>
						<td>
							<?=$s_adm_email?>
						</td>
					</tr>
					<tr> 
						<td class="lpd20">답변</td>
						<td class="subject">
							<textarea class="box01" cols="100" rows="13" name="reply"><?=$rs_reply?></textarea>
						</td>
					</tr>

				</tbody>
			</table>

			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<colgroup>
					<col width="20%" />
					<col width="80%" />
				</colgroup>
				<thead>
				<? if ($rs_post_bb_no <> "") { ?>
				<tr>
					<td class="lpd20">이전글 <!--<img src="../images/arr_prev.gif" alt="이전글" />--></td>
					<td class="lpd20"><a href="#" onClick="js_view('<?= ($RN - 1) ?>','<?= $rs_post_bb_no ?>');"><?= $rs_post_title?></a> </td>
				</tr>
				<?	} else {?>
				<tr>
					<td class="lpd20">이전글 <!--<img src="../images/arr_prev.gif" alt="이전글" />--></td>
					<td class="lpd20">이전글이 없습니다. </td>
				</tr>
				<?	} ?>
				</thead>
				<tbody>
				<? if ($rs_pre_bb_no <> "") { ?>
				<tr>
					<td class="lpd20">다음글 <!--<img src="../images/arr_next.gif" alt="다음글" />--></td>
					<td class="lpd20"><a href="#" onClick="js_view('<?= ($RN + 1) ?>','<?= $rs_pre_bb_no ?>');"><?= $rs_pre_title?></a> </td>
				</tr>
				<?	} else {?>
				<tr class="next">
					<td class="lpd20">다음글 <!--<img src="../images/arr_next.gif" alt="다음글" />--></td>
					<td class="lpd20">다음글이 없습니다.</td>
				</tr>
				<?	} ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="2"></td>
					</tr>
				</tfoot>
			</table>


			<span class="btn_list">
				<? if ($sPageRight_U == "Y") {?>
				<a href="javascript:js_save();"><img src="../images/common/btn/btn_save.gif" alt="확인" /></a>
				<? } ?>
				<a href="javascript:js_list();"><img src="../images/common/btn/btn_list.gif" alt="목록" /></a>
				<? if ($bb_no <> "") {?>
				<?	if ($sPageRight_D == "Y") {?>
				<a href="javascript:js_delete();"><img src="../images/common/btn/btn_delete.gif" alt="삭제" /></a>
				<?	 } ?>
				<? } ?>

			</span>
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