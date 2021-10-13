<?session_start();?>
<?
# =============================================================================
# File Name    :	faq_write.php
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
	require "../../_classes/moneual/board/board.php";

	$bb_code = "KFAQ";
	$mm_subtree	 = "3";
#====================================================================
# DML Process
#====================================================================

	if ($mode == "I") {

		$title		= SetStringToDB($title);
		$contents = SetStringToDB($contents);
		
		$result =  insertBoard($conn, $bb_code, $cate_01, $cate_02, $cate_03, $cate_04, $writer_nm, $writer_pw, $email, $homepage, $title, $ref_ip, $recomm, $contents, $file_nm, $file_rnm, $file_path, $file_size, $file_ext, $keyword, $use_tf, $s_adm_no);

	}

	if ($mode == "U") {

		$result = updateBoard($conn, $cate_01, $cate_02, $cate_03, $cate_04, $writer_nm, $writer_pw, $email, $homepage, $title, $ref_ip, $recomm, $contents, $file_nm, $file_rnm, $file_path, $file_size, $file_ext, $keyword, $use_tf, $s_adm_no, $bb_code, $bb_no);
	}


	if ($mode == "T") {

		updateBannerUseTF($conn, $use_tf, $s_adm_no, $bb_code, $bb_no);

	}

	if ($mode == "D") {
		
			$result = deleteBoard($conn, $s_adm_no, $bb_code, $bb_no);
		
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
		$rs_file_etc				= trim($arr_rs[0]["FILE_ETC"]); 
		$rs_cate_01					= trim($arr_rs[0]["CATE_01"]); 
		$rs_cate_02					= trim($arr_rs[0]["CATE_02"]); 
		$rs_cate_03					= trim($arr_rs[0]["CATE_03"]); 
		$rs_cate_04					= trim($arr_rs[0]["CATE_04"]); 
		$rs_keyword					= trim($arr_rs[0]["KEYWORD"]); 
		$rs_use_tf					= trim($arr_rs[0]["USE_TF"]); 
		$rs_del_tf					= trim($arr_rs[0]["DEL_TF"]); 

		$content  = $rs_contents;

	}

	if ($result) {
		$strParam = $strParam."?nPage=".$nPage."&nPageSize=".$nPageSize."&con_cate_01=".$con_cate_01."&con_cate_02=".$con_cate_02."&con_cate_03=".$con_cate_03."&search_field=".$search_field."&search_str=".$search_str;
?>	
<script language="javascript">
		alert('정상 처리 되었습니다.');
		document.location.href = "faq_list.php<?=$strParam?>";
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

<script language="javascript" type="text/javascript">


function js_list() {
	var frm = document.frm;

	frm.contents.value = "";
	frm.keyword.value = "";

	frm.method = "get";
	frm.action = "faq_list.php";
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

	if (frm.cate_01.value == "") {
		alert('카테고리를 선택해주세요.');
		frm.cate_01.focus();
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

	if (isNull(bb_no)) {
		frm.mode.value = "I";
	} else {
		frm.mode.value = "U";
		frm.bb_no.value = frm.bb_no.value;
	}

	frm.contents.value = SubmitHTML();

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


function js_cate_01 () {

	var frm = document.frm;
	var obj = "cate_02";
	obj = eval("frm."+obj);
	clear_select(obj);

	frm.target = "ifr_hidden";
	frm.action = "../../_common/get_next_pcode.php";
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
<body id="admin" onresize="BodyMinSize();">

<form name="frm" method="post">
<input type="hidden" name="rn" value="" />
<input type="hidden" name="seq_no" value="" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="bb_no" value="<?=$bb_no?>" />
<input type="hidden" name="bb_code" value="<?=$bb_code?>" />
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
        <h2>FAQ 관리</h2>  
        <table cellpadding="0" cellspacing="0" class="colstable02">
        <colgroup>
          <col width="120" />
          <col width="*" />
        </colgroup>
        <tr>
          <th>질문</th>
          <td class="line">
						<input type="text" class="txt" name="title" value="<?=$rs_title?>" style="width: 95%;" />
					</td>
        </tr>
        <tr>
          <th>카테고리</th>
          <td class="line">

					<?= makeSelectBoxOnChange($conn,"FAQ","cate_01","155","카테고리선택","",$rs_cate_01)?>&nbsp;
					<? if ($rs_cate_01 == "") $rs_cate_01 = "!";?>
					<?= makeSelectBox($conn,$rs_cate_01,"cate_02","155","하위카테고리선택","",$rs_cate_02)?>&nbsp;
					
					</td>
        </tr>
        <tr> 
          <th>답변</th>
          <td class="contentswrite line">
						<input type="hidden" name="contents" value="">
						<?= myEditor(1,'../../_common/editor','frm','content','100%','300');?>
						<!--<textarea style="width: 93.2%;" name="contents"><?=$rs_contents?></textarea>-->
					</td>
        </tr>
				<tr> 
          <th>키워드</th>
          <td class="line">
						<input type="text" class="txt" name="keyword" value="<?=$rs_keyword?>" style="width: 80%;" />
						&nbsp;<b>키워드는 ‘,’로 구분합니다.</b>
					</td>
        </tr>
				<tr class="end"> <!-- 가장 마지막에 오는 tr 엘리먼트에 end 클래스 붙여주세요 -->
					<th>공개 여부</th>
					<td class="choices">
						<input type="radio" class="radio" name="rd_use_tf" value="Y" <? if (($rs_use_tf =="Y") || ($rs_use_tf =="")) echo "checked"; ?>> 공개 <span style="width:20px;"></span>
						<input type="radio" class="radio" name="rd_use_tf" value="N" <? if ($rs_use_tf =="N") echo "checked"; ?>> 비공개
						<input type="hidden" name="use_tf" value="<?= $rs_use_tf ?>"> 
					</td>
				</tr>
        </table>
        <div class="btnright">
          <a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
          <a href="javascript:js_list();"><img src="../images/admin/btn_list.gif" alt="목록" /></a>
					<? if ($bb_no <> "") {?>
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