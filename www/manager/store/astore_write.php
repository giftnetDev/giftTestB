<?session_start();?>
<?
# =============================================================================
# File Name    : astore_write.php
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
	require "../../_classes/moneual/store/store.php";

	$store_type = "A";
	$mm_subtree	 = "5";
#====================================================================
# DML Process
#====================================================================

	if ($mode == "I") {

#====================================================================
	$savedir1 = $g_physical_path."upload_data/store";
#====================================================================

		$store_nm			= SetStringToDB($store_nm);
		$addr01				= SetStringToDB($addr01);
		$addr02				= SetStringToDB($addr02);
		$store_hour		= SetStringToDB($store_hour);
		$contents			= SetStringToDB($contents);

		$file_nm					= upload($_FILES[file_nm], $savedir1, 10 , array('gif', 'jpeg', 'jpg','png'));
		$file_rnm					= $_FILES[file_nm][name];

		$file_size = $_FILES[file_nm]['size'];
		$file_ext  = end(explode('.', $_FILES[file_nm]['name']));
		
		$zipcode = $zipcode01.$zipcode02;

		$result =  insertStore($conn, $g_site_no, $store_type, $store_cate, $store_nm, $store_url, $zipcode, $addr01, $addr02, $phone01, $phone02, $phone03, $store_hour, $contents, $file_nm, $file_rnm, $file_path, $file_size, $file_ext, $use_tf, $s_adm_no);

	}

	if ($mode == "U") {


#====================================================================
		$savedir1 = $g_physical_path."upload_data/store";
#====================================================================
		# file업로드
		switch ($flag01) {
			case "insert" :

				$file_nm					= upload($_FILES[file_nm], $savedir1, 10 , array('gif', 'jpeg', 'jpg','png'));
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

				$file_nm					= upload($_FILES[file_nm], $savedir1, 10 , array('gif', 'jpeg', 'jpg','png'));
				$file_rnm					= $_FILES[file_nm][name];

				$file_size = $_FILES[file_nm]['size'];
				$file_ext  = end(explode('.', $_FILES[file_nm]['name']));

			break;
		}

		$zipcode = $zipcode01.$zipcode02;

		$result = updateStore($conn, $g_site_no, $store_type, $store_cate, $store_nm, $store_url, $zipcode, $addr01, $addr02, $phone01, $phone02, $phone03, $store_hour, $contents, $file_nm, $file_rnm, $file_path, $file_size, $file_ext, $use_tf, $s_adm_no, $store_no);
	}


	if ($mode == "T") {

		updateStoreUseTF($conn, $use_tf, $s_adm_no, $store_no);

	}

	if ($mode == "D") {
		
		
	//	$row_cnt = count($chk);
		
	//	for ($k = 0; $k < $row_cnt; $k++) {
		
	//		$tmp_banner_no = $chk[$k];

			$result = deleteStore($conn, $s_adm_no, $store_no);
		
//		}
	}

	if ($mode == "S") {

		$arr_rs = selectStore($conn, $store_no);
		
		$rs_store_no			= trim($arr_rs[0]["STORE_NO"]);
		$rs_store_cate		= trim($arr_rs[0]["STORE_CATE"]);
		$rs_store_nm			= SetStringFromDB($arr_rs[0]["STORE_NM"]);
		$rs_store_url			= trim($arr_rs[0]["STORE_URL"]);
		$rs_zipcode				= trim($arr_rs[0]["ZIPCODE"]);
		$rs_addr01				= SetStringFromDB($arr_rs[0]["ADDR01"]);
		$rs_addr02				= SetStringFromDB($arr_rs[0]["ADDR02"]);
		$rs_phone01				= trim($arr_rs[0]["PHONE01"]);
		$rs_phone02				= trim($arr_rs[0]["PHONE02"]);
		$rs_phone03				= trim($arr_rs[0]["PHONE03"]);
		$rs_store_hour		= SetStringFromDB($arr_rs[0]["STORE_HOUR"]);
		$rs_contents			= SetStringFromDB($arr_rs[0]["CONTENTS"]);
		$rs_file_nm				= trim($arr_rs[0]["FILE_NM"]);
		$rs_file_rnm			= trim($arr_rs[0]["FILE_RNM"]);
		$rs_use_tf				= trim($arr_rs[0]["USE_TF"]); 
		$rs_del_tf				= trim($arr_rs[0]["DEL_TF"]); 
		$re_reg_date			= trim($arr_rs[0]["REG_DATE"]);


	}

	if ($result) {
		$strParam = $strParam."?nPage=".$nPage."&nPageSize=".$nPageSize."&con_store_cate=".$con_store_cate."&search_field=".$search_field."&search_str=".$search_str;
?>	
<script language="javascript">
		alert('정상 처리 되었습니다.');
		document.location.href = "astore_list.php<?=$strParam?>";
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
	
	frm.store_nm.value = "";
	frm.contents.value = "";
	frm.addr01.value = "";
	frm.addr02.value = "";

	frm.method = "get";
	frm.action = "astore_list.php";
	frm.submit();
}


function js_save() {

	var frm = document.frm;
	var store_no = "<?= $store_no ?>";

	
	if (frm.store_cate.value == "") {
		alert("지역을 선택해 주십시오.");
		frm.store_cate.focus();
		return ;		
	}

	frm.store_nm.value = frm.store_nm.value.trim();
	
	if (isNull(frm.store_nm.value)) {
		alert('지점명을 입력해주세요.');
		frm.store_nm.focus();
		return ;		
	}

	if (isNull(frm.zipcode01.value)) {
		alert('주소를 입력해주세요.');
		js_post();
		return ;		
	}

	if (isNull(frm.zipcode02.value)) {
		alert('주소를 입력해주세요.');
		js_post();
		return ;		
	}

	if (isNull(frm.addr01.value)) {
		alert('주소를 입력해주세요.');
		js_post();
		return ;		
	}

	if (isNull(frm.addr02.value)) {
		alert('상세 주소를 입력해주세요.');
		frm.addr02.focus();
		return ;		
	}

	if (isNull(frm.phone01.value)) {
		alert('전화번호를 입력해주세요.');
		frm.phone01.focus();
		return ;		
	}

	if (isNull(frm.phone02.value)) {
		alert('전화번호를 입력해주세요.');
		frm.phone02.focus();
		return ;		
	}

	if (isNull(frm.phone03.value)) {
		alert('전화번호를 입력해주세요.');
		frm.phone03.focus();
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

	if (isNull(store_no)) {
		frm.mode.value = "I";
	} else {
		frm.mode.value = "U";
		frm.store_no.value = frm.store_no.value;
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


function file_change(file) { 
	document.getElementById("file_name").value = file; 
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
}


//우편번호 찾기
function js_post() {
	var url = "/_common/common_post.php?flag=div";
	NewWindow(url, '우편번호찾기', '390', '370', 'NO');
}


</script>

</head>
<body id="admin" onresize="BodyMinSize();">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="" />
<input type="hidden" name="seq_no" value="" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="con_store_cate" value="<?=$con_store_cate?>" />
<input type="hidden" name="store_no" value="<?=$store_no?>" />
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
        <h2>대리점 관리</h2>  
        <table cellpadding="0" cellspacing="0" class="colstable">
        <colgroup>
          <col width="120" />
          <col width="*" />
        </colgroup>
        <tr> 
          <th>상점명</th>
          <td><input type="text" class="txt" name="store_nm" value="<?=$rs_store_nm?>" style="width: 80%;" /></td>
        </tr>
        <tr>
          <th>지역 선택</th>
          <td>
						<?= makeSelectBox($conn,"AREA","store_cate","125","지역선택","",$rs_store_cate)?>&nbsp;
					</td>
        </tr>
        <tr> 
          <th rowspan="3">주소</th>
          <td>
						<input type="text" class="txt" name="zipcode01" value="<?=substr($rs_zipcode,0,3)?>" maxlength="3" readonly="1" style="width: 30px;" /> -
						<input type="text" class="txt" name="zipcode02" value="<?=substr($rs_zipcode,-3)?>" maxlength="3" readonly="1" style="width: 30px;" />
						<a href="javascript:js_post();"><img src="../images/admin/btn_filesch.gif" alt="찾기" class="btn_sch"></a><br />
					</td>
				</tr>
				<tr>
					<td>
						<input type="text" class="txt" name="addr01" value="<?=$rs_addr01?>" readonly="1" style="width: 80%;" /><br />
					</td>
				</tr>
				<tr>
					<td>
						<input type="text" class="txt" name="addr02" value="<?=$rs_addr02?>" style="width: 80%;" />
					</td>
					</td>
        </tr>

        <tr> 
          <th>전화번호</th>
          <td>
						<input type="text" class="txt" name="phone01" value="<?=$rs_phone01?>" style="width: 30px;" maxlength="10" onkeyup="return isNumber(this)" /> -
						<input type="text" class="txt" name="phone02" value="<?=$rs_phone02?>" style="width: 30px;" maxlength="4" onkeyup="return isNumber(this)"/> -
						<input type="text" class="txt" name="phone03" value="<?=$rs_phone03?>" style="width: 30px;" maxlength="4" onkeyup="return isNumber(this)"/>
					</td>
        </tr>

				<tr> 
          <th>기타설명</th>
          <td class="contentswrite"><textarea style="width: 80%;" name="contents"><?=$rs_contents?></textarea></td>
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