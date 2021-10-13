<?session_start();?>
<?
# =============================================================================
# File Name    : suggest_read.php
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

	$bb_code = "KSU";
	$mm_subtree	 = "4";
#====================================================================
# DML Process
#====================================================================

	if ($mode == "I") {

#====================================================================
	$savedir1 = $g_physical_path."upload_data/board";
#====================================================================

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
		$rs_keyword					= trim($arr_rs[0]["KEYWORD"]); 
		$rs_use_tf					= trim($arr_rs[0]["USE_TF"]); 
		$rs_del_tf					= trim($arr_rs[0]["DEL_TF"]); 
		$rs_reg_date				= trim($arr_rs[0]["REG_DATE"]); 
		
		$rs_reg_date				= date("Y-m-d",strtotime($rs_reg_date));
		
		$del_tf = "N";
		
		# ������
		$arr_rs = selectPreBoard($conn, $bb_code, $bb_no, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $keyword, $reply_state, $con_use_tf, $del_tf, $search_field, $search_str);
	
		$rs_pre_bb_no					= trim($arr_rs[0]["BB_NO"]);
		$rs_pre_title					= SetStringFromDB($arr_rs[0]["TITLE"]);


		# ������
		$arr_rs = selectPostBoard($conn, $bb_code, $bb_no, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $keyword, $reply_state, $con_use_tf, $del_tf, $search_field, $search_str);
	
		$rs_post_bb_no					= trim($arr_rs[0]["BB_NO"]);
		$rs_post_title					= SetStringFromDB($arr_rs[0]["TITLE"]);

	}

	if ($result) {
		$strParam = $strParam."?nPage=".$nPage."&nPageSize=".$nPageSize."&con_cate_01=".$con_cate_01."&con_cate_02=".$con_cate_02."&con_cate_03=".$con_cate_03."&search_field=".$search_field."&search_str=".$search_str;
?>	
<script language="javascript">
		alert('���� ó�� �Ǿ����ϴ�.');
		document.location.href = "suggest_list.php<?=$strParam?>";
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
		
	frm.method = "get";
	frm.action = "suggest_list.php";
	frm.submit();
}


function js_save() {

	var frm = document.frm;
	var bb_no = "<?= $bb_no ?>";
	
	frm.title.value = frm.title.value.trim();
	
	if (isNull(frm.title.value)) {
		alert('������ �Է����ּ���.');
		frm.title.focus();
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
//		alert("���� �Ͻ� �ڷᰡ �����ϴ�.");
//	} else {

		bDelOK = confirm('�ڷḦ ���� �Ͻðڽ��ϱ�?');
		
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
?>


		</td>
		<td class="contentarea">

      
      <!-- S: mwidthwrap -->
      <div id="mwidthwrap">
        <h2>������ ����</h2>  

        <table cellpadding="0" cellspacing="0" class="colstable">
        <colgroup>
          <col width="120" />
          <col width="*" />
          <col width="120" />
          <col width="*" />
        </colgroup>
        <tr>
          <th>����</th>
          <td colspan="3">
						<?=getDcodeName($conn,"SUG",$rs_cate_01)?>
					</td>
        </tr>
        <tr>
          <th>����</th>
          <td colspan="3">
						<?=$rs_title?>
					</td>
        </tr>
        <tr>
          <th>����</th>
          <td><?=$rs_writer_nm?></td>
          <th>�̸���</th>
          <td><?=$rs_email?></td>
        </tr>
        <tr>
          <th>����ó</th>
          <td><?=$rs_homepage?></td>
          <th>�����</th>
          <td><?=$rs_reg_date?></td>
        </tr>
        <tr class="end"> <!-- ���� �������� ���� tr ������Ʈ�� end Ŭ���� �ٿ��ּ��� -->
          <td colspan="4" class="contentsview">
						<?=nl2br($rs_contents)?>
					</td>
        </tr>

				</table>

        <div class="btnright">
          <!--<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="Ȯ��" /></a>-->
          <a href="javascript:js_list();"><img src="../images/admin/btn_list.gif" alt="���" /></a>
					<? if ($bb_no <> "") {?>
          <a href="javascript:js_delete();"><img src="../images/admin/btn_delete.gif" alt="����" /></a>
					<? } ?>
        </div>

        <table cellpadding="0" cellspacing="0" class="pntable">
        <colgroup>
          <col width="120" />
          <col width="*" />
        </colgroup>


				<? if ($rs_post_bb_no <> "") { ?>
        <tr>
          <th>������ <img src="../images/arr_prev.gif" alt="������" /></th>
          <td colspan="3"><a href="#" onClick="js_view('<?= ($RN - 1) ?>','<?= $rs_post_bb_no ?>');"><?= $rs_post_title?></a> </td>
        </tr>
				<?	} else {?>
        <tr>
          <th>������ <img src="../images/arr_prev.gif" alt="������" /></th>
          <td colspan="3">�������� �����ϴ�. </td>
        </tr>
				<?	} ?>

				<? if ($rs_pre_bb_no <> "") { ?>
        <tr>
          <th>������ <img src="../images/arr_next.gif" alt="������" /></th>
          <td colspan="3"><a href="#" onClick="js_view('<?= ($RN + 1) ?>','<?= $rs_pre_bb_no ?>');"><?= $rs_pre_title?></a> </td>
        </tr>
				<?	} else {?>
				<tr class="next">
					<th>������ <img src="../images/arr_next.gif" alt="������" /></th>
					<td colspan="3">�������� �����ϴ�.</td>
				</tr>
				<?	} ?>


        </table>

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