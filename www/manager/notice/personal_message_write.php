<?session_start();?>
<?

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "BO005"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

#====================================================================
# common_header Check Session
#====================================================================
	require "../../_common/common_header.php"; 
	
#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/board/board.php";

	$bb_code = "PMESSAGE";

#====================================================================
# DML Process
#====================================================================

	if ($mode == "I") {

#====================================================================
	$savedir1 = $g_physical_path."upload_data/message";
#====================================================================

		$title		= SetStringToDB($title);
		$contents = SetStringToDB($contents);
		
		$file_nm					= upload($_FILES[file_nm], $savedir1, 300 , array('gif', 'jpeg', 'jpg','png','zip','exe','ini','dll','pdf','xls','xlsx'));
		$file_rnm					= $_FILES[file_nm][name];

		$file_size = $_FILES[file_nm]['size'];
		$file_ext  = end(explode('.', $_FILES[file_nm]['name']));


		$new_bb_no =  insertBoard($conn, $bb_code, $cate_01, $cate_02, $cate_03, $cate_04, $writer_nm, $writer_pw, $email, $homepage, $title, $ref_ip, $recomm, $contents, $file_nm, $file_rnm, $file_path, $file_size, $file_ext, $keyword, $comment_tf, $use_tf, $s_adm_no);
		
		$result = deleteBoardMessage($conn, $bb_code, $new_bb_no);

		$arr_adm_no = explode(",",$send_data);

		if (sizeof($arr_adm_no) > 0) {

			for ($m = 0; $m < sizeof($arr_adm_no); $m++) {
				$result = insertBoardMessage($conn, $bb_code, $new_bb_no, $arr_adm_no[$m]);
			}
		}

	}

	if ($mode == "U") {

#====================================================================
		$savedir1 = $g_physical_path."upload_data/message";
#====================================================================
		# file업로드
		switch ($flag01) {
			case "insert" :

				$file_nm					= upload($_FILES[file_nm], $savedir1, 300 , array('gif', 'jpeg', 'jpg','png','zip','exe','ini','dll','pdf','xls','xlsx'));
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

				$file_nm					= upload($_FILES[file_nm], $savedir1, 300 , array('gif', 'jpeg', 'jpg','png','zip','exe','ini','dll','pdf','xls','xlsx'));
				$file_rnm					= $_FILES[file_nm][name];

				$file_size = $_FILES[file_nm]['size'];
				$file_ext  = end(explode('.', $_FILES[file_nm]['name']));

			break;
		}


		$result = updateBoard($conn, $cate_01, $cate_02, $cate_03, $cate_04, $writer_nm, $writer_pw, $email, $homepage, $title, $ref_ip, $recomm, $contents, $file_nm, $file_rnm, $file_path, $file_size, $file_ext, $keyword, $comment_tf, $use_tf, $s_adm_no, $bb_code, $bb_no);

		$result = deleteBoardMessage($conn, $bb_code, $bb_no);

		$arr_adm_no = explode(",",$send_data);

		if (sizeof($arr_adm_no) > 0) {

			for ($m = 0; $m < sizeof($arr_adm_no); $m++) {
				$result = insertBoardMessage($conn, $bb_code, $bb_no, $arr_adm_no[$m]);
			}
		}
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
		$rs_file_etc				= trim($arr_rs[0]["FILE_ETC"]); 
		$rs_cate_01					= trim($arr_rs[0]["CATE_01"]); 
		$rs_cate_02					= trim($arr_rs[0]["CATE_02"]); 
		$rs_cate_03					= trim($arr_rs[0]["CATE_03"]); 
		$rs_cate_04					= trim($arr_rs[0]["CATE_04"]); 
		$rs_keyword					= trim($arr_rs[0]["KEYWORD"]); 
		$rs_use_tf					= trim($arr_rs[0]["USE_TF"]); 
		$rs_del_tf					= trim($arr_rs[0]["DEL_TF"]); 
		$rs_reg_adm					= trim($arr_rs[0]["REG_ADM"]); 

		$content  = $rs_contents;
	}

	if ($result) {
		$strParam = $strParam."?nPage=".$nPage."&nPageSize=".$nPageSize."&con_cate_01=".$con_cate_01."&con_cate_02=".$con_cate_02."&con_cate_03=".$con_cate_03."&search_field=".$search_field."&search_str=".$search_str;
?>	
<script language="javascript">
		alert('정상 처리 되었습니다.');
		document.location.href = "personal_message_list.php<?=$strParam?>";
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
<script type="text/javascript" src="../../_common/SE2.1.1.8141/js/HuskyEZCreator.js" charset="utf-8"></script>
<script language="javascript" type="text/javascript">


function js_list() {

	var frm = document.frm;
		
	frm.contents.value = "";
	frm.method = "post";
	frm.action = "personal_message_list.php";
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

	var send_data = ""; 
		
	for(var i = 0; i < frm.sel_right.length; i++) {
		frm.sel_right.options[i].selected = true; 
		if (send_data == "") {
			send_data = frm.sel_right.options[i].value;	
		} else {
			send_data = send_data+","+frm.sel_right.options[i].value;	
		}
	}	

	frm.send_data.value = send_data;

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

	oEditors[0].exec("UPDATE_CONTENTS_FIELD", []);   // 에디터의 내용이 textarea에 적용된다.
	frm.method = "post";
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
}

function LeftToRight() {
	var objSel, objSel
	var frm = document.frm;
	
	objLeft = frm.sel_left;
	objRight = frm.sel_right;
		
	for(var i = 0; i < objLeft.length; i++)
		if(objLeft.options[i].selected)
			objRight.options[objRight.length] = new Option(objLeft.options[i].text,  objLeft.options[i].value);

	for(var i = 0; i < objRight.length; i++) {
		for(var j = 0; j < objLeft.length; j++) {
			if(objLeft.options[j].selected) {
				objLeft.options[j] = null;
				break;
			}
		}
	}
}

function RightToLeft() {
	var objSel, objSel
	var frm = document.frm;

	objLeft = frm.sel_left;
	objRight = frm.sel_right;
		
	for(var i = 0; i < objRight.length; i++)
		if(objRight.options[i].selected)
			objLeft.options[objLeft.length] = new Option(objRight.options[i].text,  objRight.options[i].value);

	for(var i = 0; i < objLeft.length; i++) {
		for(var j = 0; j < objRight.length; j++) {
			if(objRight.options[j].selected) {
				objRight.options[j] = null;
				break;
			}	
		}
	} 
}

function js_select_message() {
	
	var frm = document.frm;
	var type = "";

	objLeft = frm.sel_left;
	objRight = frm.sel_right;

	clear_select(objLeft);
	
	for (i=0; i < frm.chk_sale.length; i++) {
		if (frm.chk_sale[i].checked == true) {
			type = frm.chk_sale[i].value;
		}
	}

	if (type == "전체") {	
		for (k = 0; k < arr_message_no.length; k++) {
			
			objLeft.options[objLeft.length] = new Option(arr_message_name[k],  arr_message_no[k]);
		}
	}

	if (type == "기프트넷") {	
		for (k = 0; k < arr_message_no.length; k++) {
			if (arr_message_type[k].indexOf("1") > -1) {
				objLeft.options[objLeft.length] = new Option(arr_message_name[k],  arr_message_no[k]);
			}
		}
	}
	


}

function clear_select(obj){
	sel_len = obj.length;
	for(i = sel_len ; i >= 0; i--) {
		obj.options[i] = null;
	}
	return ;
}

<?= makeMessageScriptArray($conn, "arr_message"); ?>

</script>

</head>
<body id="admin" onresize="BodyMinSize();">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="" />
<input type="hidden" name="seq_no" value="" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="bb_no" value="<?=$bb_no?>" />
<input type="hidden" name="bb_code" value="<?=$bb_code?>" />
<input type="hidden" name="nPage" value="<?=$nPage?>" />
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>" />
<input type="hidden" name="kind" value="" />
<input type="hidden" name="chk_flag" value="" />
<input type="hidden" name="send_data" value="" />

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
        <h2>메세지 전송</h2>  
        <table cellpadding="0" cellspacing="0" class="colstable02">
        <colgroup>
          <col width="120" />
          <col width="*" />
        </colgroup>
        <tr>
          <th>제목</th>
          <td class="line">
						<input type="text" class="txt" name="title" value="<?=$rs_title?>" style="width: 95%;" />
					</td>
        </tr>
          <th>첨부</th>
          <td class="line">

						<?
							if (strlen($rs_file_nm) > 3) {
						?>
							<a href="/_common/download_file.php?menu=board&bb_code=<?= $rs_bb_code ?>&bb_no=<?= $rs_bb_no ?>&field=file_nm"><?= $rs_file_rnm ?></a>
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
          <th>내용</th>
          <td class="contentswrite line">
						<span class="fl" style="padding-left:0px;width:900px;height:500px;"><textarea name="contents" id="contents"  style="padding-left:0px;width:890px;height:400px;"><?=$rs_contents?></textarea></span>
					</td>
        </tr>

				<tr class="end">
					<th>수신자</th>
					<td style="padding: 10px 10px 10px 15px;">
						<table border="0">
							<tr>
								<td width="230" style="padding: 10px 10px 10px 0px">
									
									<input type="radio" value="전체" name="chk_sale" onClick="js_select_message();" checked> 전체 
									<input type="radio" value="기프트넷" name="chk_sale" onClick="js_select_message();"> 기프트넷 <br/>
									
									<br><br>

									* 사용자 리스트 <br>
									<select name="sel_left" size="10" style="width:230px" multiple>
										<?
											$arr_message_no = listMessageBoard($conn, $rs_bb_no, $rs_bb_code, "NO");
											
											if (sizeof($arr_message_no) > 0) {
				
												for ($j = 0 ; $j < sizeof($arr_message_no); $j++) {
													$ADM_NO					= trim($arr_message_no[$j]["ADM_NO"]);
													$ADM_NAME				= trim($arr_message_no[$j]["ADM_NAME"]);
										?>
										<option value="<?=$ADM_NO?>"><?=$ADM_NAME?></option>
										<?
												}
											}
										?>
									</select>
								</td>
								<td style="padding: 10px 10px 10px 10px" align="center">
									&nbsp;
									<br><br>
									<input type=button name=LR value=' &gt; ' onClick="javascript:LeftToRight()" class="txt" style="width:30px">
									<br><br>
									<input type=button name=RL value=' &lt; '  onClick="javascript:RightToLeft()" class="txt" style="width:30px">
								</td>
								<td width="230" style="padding: 10px 10px 10px 10px">
									&nbsp;
									<br><br>
									* 적용 사용자 리스트 <br>
									<select name="sel_right" size="10" style="width:230px" multiple>
										<?
											$arr_message_yes = listMessageBoard($conn, $rs_bb_code, $rs_bb_no, "YES");
											
											if (sizeof($arr_message_yes) > 0) {
				
												for ($j = 0 ; $j < sizeof($arr_message_yes); $j++) {
													$ADM_NO					= trim($arr_message_yes[$j]["ADM_NO"]);
													$ADM_NAME				= trim($arr_message_yes[$j]["ADM_NAME"]);
										?>
										<option value="<?=$ADM_NO?>"><?=$ADM_NAME?></option>
										<?
												}
											}
										?>
									</select>
								</td>
							</table>
					</td>
				</tr>
				<!--
				<tr> 
          <th>키워드</th>
          <td class="line">
						<input type="text" class="txt" name="keyword" value="<?=$rs_keyword?>" style="width: 80%;" />
						&nbsp;<b>키워드는 ‘,’로 구분합니다.</b>
					</td>
        </tr>
				-->
        </table>
        <div class="btnright">
					<? if ($s_adm_no == $rs_reg_adm || $sPageRight_D == "Y") { ?>
          <a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
					<?	if ($bb_no <> "") {?>
          <a href="javascript:js_delete();"><img src="../images/admin/btn_delete.gif" alt="삭제" /></a>
					<?	} ?>
					<? } ?>
          <a href="javascript:js_list();"><img src="../images/admin/btn_list.gif" alt="목록" /></a>
        </div>      
      </div>
      <!-- // E: mwidthwrap -->
    </td>
  </tr>
  </table>
</div>
<iframe src="" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
<SCRIPT LANGUAGE="JavaScript">

var oEditors = [];
	nhn.husky.EZCreator.createInIFrame({
	oAppRef: oEditors,
	elPlaceHolder: "contents",
	sSkinURI: "../../_common/SE2.1.1.8141/SmartEditor2Skin.html",
	htParams : {
		bUseToolbar : true, 
		fOnBeforeUnload : function(){ 
			// alert('야') 
		},
		fOnAppLoad : function(){ 
		// 이 부분에서 FOCUS를 실행해주면 됩니다. 
		this.oApp.exec("EVENT_EDITING_AREA_KEYDOWN", []); 
		this.oApp.setIR(""); 
		//oEditors.getById["ir1"].exec("SET_IR", [""]); 
		}
	}, 
	fCreator: "createSEditor2"
});

//-->
</SCRIPT>
</body>
</html>
<?
#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);
?>