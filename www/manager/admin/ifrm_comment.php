<?session_start();?>
<?
# =============================================================================
# File Name    : ifrm_comment.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.11.16
# Modify Date  : 
#	Copyright : Copyright @아름지기 Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# common_header Check Session
#====================================================================
//	require "../../_common/common_header.php"; 

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
	require "../../_classes/biz/board/new_board.php";

#====================================================================
# Request Parameter
#====================================================================

	#List Parameter
	$parent_bb_code	= trim($parent_bb_code);
	$parent_bb_no		= trim($parent_bb_no);
	
	// parent 게시판 정보 입력
	$cate_01	= $parent_bb_code;
	$cate_02	= $parent_bb_no;

	$con_cate_01 = $parent_bb_code;
	$con_cate_02 = $parent_bb_no;

	//$bb_code		= "REPLY";
	$s_adm_no	= "WEB";


	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
	$con_use_tf = "Y";
	$del_tf = "N";
	

	if ($mode == "IR") {

		$title			= SetStringToDB($title);
		$writer_nm	= SetStringToDB($writer_nm);
		$contents		= SetStringToDB($contents);
		

		$result =  insertBoard($conn, $bb_code, $cate_01, $cate_02, $cate_03, $cate_04, $writer_nm, $writer_pw, $email, $homepage, $title, $ref_ip, $recomm, $contents, $file_nm, $file_rnm, $file_path, $file_size, $file_ext, $keyword, $comment_tf, $use_tf, $s_adm_no);
		
		if ($result) {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_front_title?></title>
<script type="text/javascript">
	document.location = "ifrm_comment.php?parent_bb_code=<?=$parent_bb_code?>&parent_bb_no=<?=$parent_bb_no?>";
</script>
</head>
</html>
<?
			exit;
		}
	}

	if ($mode == "UR") {

		$title			= SetStringToDB($title);
		$writer_nm	= SetStringToDB($writer_nm);
		$contents		= SetStringToDB($contents);

		$result = updateBoard($conn, $cate_01, $cate_02, $cate_03, $cate_04, $writer_nm, $writer_pw, $email, $homepage, $title, $ref_ip, $recomm, $contents, $file_nm, $file_rnm, $file_path, $file_size, $file_ext, $keyword, $comment_tf, $use_tf, $s_adm_no, $bb_code, $bb_no);
		
		if ($result) {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_front_title?></title>
<script type="text/javascript">
	document.location = "ifrm_comment.php?parent_bb_code=<?=$parent_bb_code?>&parent_bb_no=<?=$parent_bb_no?>";
</script>
</head>
</html>
<?
			exit;
		}
	}


	if ($mode == "DR") {
		
		//$bb_code = "REPLY";

		$result = deleteBoard($conn, $s_adm_no, $bb_code, $bb_no);
		if ($result) {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_front_title?></title>
<script type="text/javascript">
	document.location = "ifrm_comment.php?parent_bb_code=<?=$parent_bb_code?>&parent_bb_no=<?=$parent_bb_no?>";
</script>
</head>
</html>
<?
			exit;
		}
	}

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
		$nPageSize = 5;
	}

	$nPageBlock	= 10;

	$nListCnt =totalCntBoard($conn, $bb_code, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $keyword, $reply_state, $con_use_tf, $del_tf, $search_field, $search_str);

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listBoard($conn, $bb_code, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $keyword, $reply_state, $con_use_tf, $del_tf, $search_field, $search_str, $nPage, $nPageSize);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_front_title?></title>
<script type="text/javascript">

	function init(){ 
		var doc = document.getElementById("infodoc"); 
		doc.style.top=0; 
		doc.style.left=0; 
		if(doc.offsetHeight == 0){ 
		} else { 
			pageheight = doc.offsetHeight; 
			pagewidth = "670"; 

			parent.document.getElementById('ifr_comment').height = pageheight;
			//parent.frames["ifr_detail"].resizeTo(pagewidth,pageheight); 
		}
	}

	function js_reply_write() {
		var frm = document.frm;

		if ((frm.contents.value == "간략한 댓글을 올려주세요. 로그인 후 등록하실 수 있습니다.") || (frm.contents.value == "")) {
			alert("간략한 댓글을 올려주세요.");
			frm.contents.focus();
			return;
		}

		if (frm.bb_no.value == "") {
			frm.mode.value = "IR";
		} else {
			frm.mode.value = "UR";
		}
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_reply_modify(bb_no,idx) {

		var frm = document.frm;
		
		frm.bb_no.value = bb_no;
		
		if (frm.temp_contents.length == null) {
			frm.contents.value = frm.temp_contents.value;
		} else {
			frm.contents.value = frm.temp_contents[idx].value;
		}
		
		//frm.target = "";
		//frm.action = "<?=$_SERVER[PHP_SELF]?>";
		//frm.submit();

	}

	function js_reply_delete(bb_no) {

		var frm = document.frm;
		bDelOK = confirm('자료를 삭제 하시겠습니까?');
		
		if (bDelOK==true) {
			frm.mode.value = "DR";
			frm.bb_no.value = bb_no;
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}


</script>
<style type="text/css">

body,p,table,input,select { margin:0; padding:0; font-size:12px; font-family:돋움, Dotum; color:#737373;}

img	{ border:none }

.detail_etc	{ margin-bottom:10px }

.reple_box		{ height:90px; }
	.reple_box textarea	{ width:665px; height:65px; margin:10px 0 10px 10px; vertical-align:middle; background:#f4f4f4; border:solid 1px #DEDEDE; font-size:12px; font-family:돋움, Dotum; color:#737373; }
	.reple_box img	{ vertical-align:middle }

.tbl_reple		{ width:99%; border-collapse:collapse; }
	.tbl_reple th	{ text-align:left; font-weight:normal; color:#a2a2a2; padding:4px 10px 3px 10px; #padding:3px 10px 3px 10px; background:url(../images/common/bbs/line_dot.gif) repeat-x left bottom }
	.tbl_reple th strong	{ color:#666666 }
	.tbl_reple th.btn	{ text-align:right; }
	.tbl_reple td	{ padding:7px 0 5px 10px; #padding:14px 0 11px 10px; border-bottom:solid 1px #dddddd }

/*bbs page number navigation*/
#bbspgno { float: left; clear: both; width: 100%; font-family: verdana; font-size:10px; text-align:center; word-spacing:3px; padding-top: 10px; padding-bottom: 10px; }
#bbspgno strong.sel{ font-weight: bold; }
.bnk { padding-right:1px; margin:0px; border-right:0px; }
ul.bnk,ol.bnk {list-style:none; padding-right: 1px; margin: 0px;} 
ol li.bnk, ul li.bnk { display: inline; padding-right: 1px; margin: 0px; vertical-align:middle; }

/*link color style*/
a:link { text-decoration: none; color: #7f7f7f; }
a:visited { text-decoration: none; color: #7f7f7f; }
a:hover { text-decoration:underline; color:#7f7f7f; }

</style>
<body onload="init();" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<div id="infodoc" style="position:absolute;left:0;top:0;width:100%"> 
<br />

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="bb_no" value="">
<input type="hidden" name="bb_code" value="<?=$bb_code?>">
<input type="hidden" name="parent_bb_code" value="<?=$parent_bb_code?>">
<input type="hidden" name="parent_bb_no" value="<?=$parent_bb_no?>">
<input type="hidden" name="next_url" value="" />
<input type="hidden" name="mode" value="<?=$mode?>">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>">

<!-- 리플달기 -->
<div class="detail_etc">
	<!-- Write -->
		<div class="reple_box">
			<textarea name="contents" onFocus="if (this.value=='간략한 댓글을 올려주세요. 로그인 후 등록하실 수 있습니다.') this.value=''" onBlur="if (this.value == ''){this.value='간략한 댓글을 올려주세요. 로그인 후 등록하실 수 있습니다.'}">간략한 댓글을 올려주세요. 로그인 후 등록하실 수 있습니다.</textarea>
			<a href="javascript:js_reply_write();"><img src="/kor/images/common/bbs/bbtn_confirm.gif" alt="확인"></a>
			<input type="hidden" name="writer_nm" value="<?=$s_adm_name?>">
			<input type="hidden" name="cate_03" value="<?=$s_adm_id?>">
			<input type="hidden" name="cate_04" value="<?=$s_mem_no?>">
			<input type="hidden" name="use_tf" value="Y">
		</div>
	<!-- //Write -->
			
	<!-- Read -->
	<table class="tbl_reple">
		<colgroup>
			<col><col width="90">
		</colgroup>
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
					$WRITER_NM			= SetStringFromDB($arr_rs[$j]["WRITER_NM"]);
					$TITLE					= SetStringFromDB($arr_rs[$j]["TITLE"]);
					$CONTENTS				= SetStringFromDB($arr_rs[$j]["CONTENTS"]);
					$HIT_CNT				= trim($arr_rs[$j]["HIT_CNT"]);
					$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
					$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);
					
					$REG_DATE = date("Y-m-d",strtotime($REG_DATE));
		
		?>
		<tr>
			<th><font color="navy"><?=$CATE_03?></font> <?=$REG_DATE?></th>
			<th class="btn">
		<? //if ($s_mem_no == $CATE_04) { ?>
				<a href="javascript:js_reply_modify('<?=$BB_NO?>','<?=$j?>');"><img src="/kor/images/common/bbs/sbtn_modify.gif" alt="수정"></a>
				<a href="javascript:js_reply_delete('<?=$BB_NO?>');"><img src="/kor/images/common/bbs/sbtn_delete.gif" alt="삭제"></a>
		<? //} ?>
			</th>
		</tr>
		<tr>
			<td colspan="2">
		<?=nl2br($CONTENTS)?>
				<input type="hidden" name="temp_contents" value="<?=$CONTENTS?>">
			</td>
		</tr>
		<?			
				}
			} else { 
		?> 
		<tr>
			<td height="50" align="center" colspan="8">등록된 내용이 없습니다. </td>
		</tr>
		<? 
			}
		?>
	</table>
	<!-- //Read -->
	<!-- Paging -->
	<!-- --------------------- 페이지 처리 화면 START -------------------------->
	<?
	# ==========================================================================
	#  페이징 처리
	# ==========================================================================
		if (sizeof($arr_rs) > 0) {
		#$search_field		= trim($search_field);
		#$search_str			= trim($search_str);
		$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&parent_bb_no=".$parent_bb_no;

	?>
		<?= Front_Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
	<?
		}
	?>
	<!-- --------------------- 페이지 처리 화면 END -------------------------->
	<!-- //Paging -->
</div>
</div>
</form>
</body>
</html>
<!-- //리플달기 -->
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>
