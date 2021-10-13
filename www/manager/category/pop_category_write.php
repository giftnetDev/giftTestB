<?session_start();?>
<?
# =============================================================================
# File Name    : category_list.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @C&C Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$memu_right = "GD003"; // 메뉴마다 셋팅 해 주어야 합니다

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


	#sPageRight_R 해당 메뉴 읽기 권한
	#sPageRight_I 해당 메뉴 입력 권한
	#sPageRight_U 해당 메뉴 수정 권한
	#sPageRight_D 해당 메뉴 삭제 권한
	#sPageRight_  해당 메뉴 코드 메뉴 관리 에서 지정된 코드 값을 사용한다.

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/category/category.php";

#====================================================================
# Request Parameter
#====================================================================

$m_level = Trim($m_level);
$m_seq01 = Trim($m_seq01);
$m_seq02 = Trim($m_seq02);
$m_seq03 = Trim($m_seq03);

#====================================================================
# Declare variables
#====================================================================

#====================================================================
# Request Parameter
#====================================================================

$cate_no			= trim($cate_no);
$m_level			= trim($m_level);
$m_seq01			= trim($m_seq01);
$m_seq02			= trim($m_seq02);
$m_seq03			= trim($m_seq03);
$cate_name		= trim($cate_name);
$cate_memo		= trim($cate_memo);
$cate_yn			= trim($cate_yn);
$cate_cd			= trim($cate_cd);
$in_cate_code = trim($in_cate_code);

//echo $m_level;

$result = false;
#====================================================================
# DML Process
#====================================================================
	
#====================================================================
	$savedir1 = $g_physical_path."upload_data/category";
#====================================================================
	
	if ($mode == "I") {
		

		$cate_img					= upload($_FILES[cate_img], $savedir1, 100 , array('gif', 'jpeg', 'jpg','png'));
		$cate_img_over		= upload($_FILES[cate_img_over], $savedir1, 100 , array('gif', 'jpeg', 'jpg','png'));
		//$file_rnm					= $_FILES[cate_img][name];

		$result = insertCategory($conn, $m_level, $m_seq01, $m_seq02, $m_seq03, $cate_name, $cate_memo, $cate_flag, $in_cate_code, $cate_img, $cate_img_over, $use_tf, $s_adm_no);

	}


	if ($mode == "S") {

		$arr_rs = selectCategory($conn, $cate_no);

		//category_NO, category_NAME, category_URL, category_FLAG, category_CD, category_RIGHT,category_IMG,category_IMG_OVER

		$rs_cate_no				= trim($arr_rs[0]["CATE_NO"]); 
		$rs_cate_name			= trim($arr_rs[0]["CATE_NAME"]); 
		$rs_cate_memo			= trim($arr_rs[0]["CATE_MEMO"]); 
		$rs_cate_flag			= trim($arr_rs[0]["CATE_FLAG"]); 
		$rs_cate_cd				= trim($arr_rs[0]["CATE_CD"]); 
		$rs_cate_code			= trim($arr_rs[0]["CATE_CODE"]); 
		$rs_cate_img			= trim($arr_rs[0]["CATE_IMG"]); 
		$rs_cate_img_over	= trim($arr_rs[0]["CATE_IMG_OVER"]); 
		$rs_use_tf				= trim($arr_rs[0]["USE_TF"]); 
		$rs_del_tf				= trim($arr_rs[0]["DEL_TF"]); 

	}

	if ($mode == "U") {
		
		switch ($flag01) {
			case "insert" :
				$cate_img					= upload($_FILES[cate_img], $savedir1, 100 , array('gif', 'jpeg', 'jpg','png'));
			break;
			case "keep" :
				$cate_img			= $old_cate_img;
			break;
			case "delete" :
				$cate_img			= "";
			break;
			case "update" :
				$cate_img					= upload($_FILES[cate_img], $savedir1, 100 , array('gif', 'jpeg', 'jpg','png'));
			break;
		}

		switch ($flag02) {
			case "insert" :
				$cate_img_over		= upload($_FILES[cate_img_over], $savedir1, 100 , array('gif', 'jpeg', 'jpg','png'));
			break;
			case "keep" :
				$cate_img_over		= $old_cate_img_over;
			break;
			case "delete" :
				$cate_img_over		= "";
			break;
			case "update" :
				$cate_img_over		= upload($_FILES[cate_img_over], $savedir1, 100 , array('gif', 'jpeg', 'jpg','png'));
			break;
		}

		$result = updateCategory($conn, $cate_name, $cate_memo, $cate_flag, $in_cate_code, $cate_img, $cate_img_over, $use_tf, $s_adm_no, $cate_no);

	}

	if ($mode == "D") {
		$result = deleteCategory($conn, $s_adm_no, $cate_no);
	}

	if ($rs_cate_cd <> "") {

		if (strlen($m_level) == 2) {
			$level_str = "대표분류 메뉴";
		} else if (strlen($m_level) == 4) {
			$level_str = "대분류 메뉴";
		} else if (strlen($m_level) == 6) {
			$level_str = "중분류 메뉴";
		} else if (strlen($m_level) == 8) {
			$level_str = "소분류 메뉴";
		}

	
	} else {

		if (strlen($m_level) == 0) {
			$level_str = "대표분류 메뉴";
		} else if (strlen($m_level) == 2) {
			$level_str = "대분류 메뉴";
		} else if (strlen($m_level) == 4) {
			$level_str = "중분류 메뉴";
		} else if (strlen($m_level) == 6) {
			$level_str = "소분류 메뉴";
		}	
	}


#=================================================================
# Get Result set from stored procedure
#=================================================================
	if ($result) {
?>	
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<script language="javascript">
		//alert('정상 처리 되었습니다.');
		opener.js_search();
		self.close();
</script>
<?
		exit;
	}	
?>
<!-- Top Menu 시작 -->
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->

<script language="javascript">
	
	function js_save() {
		
		var cate_no = "<?= $cate_no ?>";
		var frm = document.frm;

		if (frm.cate_name.value == "") {
			alert("카테고리명을 입력하세요.");
			frm.cate_name.focus();
			return;
		}


		if (isNull(cate_no)) {
			frm.mode.value = "I";
		} else {
			frm.mode.value = "U";
		}
		
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	// Ajax
	function sendKeyword() {

		if (frm.old_cate_code.value != frm.in_cate_code.value)	{

			var keyword = document.frm.in_cate_code.value;

			//alert(keyword);
						
			if (keyword != '') {
				var params = "keyword="+encodeURIComponent(keyword);
			
				//alert(params);
				sendRequest("category_dup_check.php", params, displayResult, 'POST');
			}
			//setTimeout("sendKeyword();", 100);
		} else {
			js_save();
		}
	}

	function displayResult() {
		
		if (httpRequest.readyState == 4) {
			if (httpRequest.status == 200) {
				
				var resultText = httpRequest.responseText;
				
				var result = resultText;
				
				//alert(result);
				
				//return;
				if (result == "1") {
					alert("사용중인 코드 입니다.");
					return;
				} else {
					js_save();
				}
			} else {
				alert("에러 발생: "+httpRequest.status);
			}
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
	

	function js_delete() {
		var frm = document.frm;

		bDelOK = confirm('해당 메뉴를 삭제 하시겠습니까?\n\n해당 메뉴에 하위 메뉴도 모두 삭제 됩니다.');
		
		if (bDelOK==true) {
			frm.mode.value = "D";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}


</script>
</head>
<body id="popup_code">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="">
<input type="hidden" name="cate_no" value="<?=$cate_no?>">
<input type="hidden" name="m_level" value="<?=$m_level?>">
<input type="hidden" name="m_seq01" value="<?=$m_seq01?>">
<input type="hidden" name="m_seq02" value="<?=$m_seq02?>">
<input type="hidden" name="m_seq03" value="<?=$m_seq03?>">

<div id="popupwrap_code">
	<h1>카테고리 등록</h1>
	<div id="postsch_code">
		<h2>* 상품관리에서 사용할 카테고리를 등록하는 화면 입니다.</h2>
		<div class="addr_inp">

		<table cellpadding="0" cellspacing="0" width="95%">
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0" border="0" width="100%" class="colstable">
		
						<colgroup>
							<col width="20%">
							<col width="80%">
						</colgroup>
						<tr>
							<th>카테고리분류</th>
							<td>
								<?=$level_str?>
							</td>
						</tr>
						<tr>
							<th>카테고리명</th>
							<td>
								<input type="text" name="cate_name" value="<?= $rs_cate_name ?>" style="width:90%;" class="txt" />
							</td>
						</tr>
						<tr>
							<th>카테고리설명</th>
							<td colspan="3">
								<input type="text" name="cate_memo" value="<?= $rs_cate_memo ?>" style="width:90%;" class="txt" />
							</td>
						</tr>
						<tr>
							<th>사용자코드</th>
							<td colspan="3">
								<input type="text" name="in_cate_code" value="<?= $rs_cate_code ?>" style="width:30%;" class="txt" />
								<input type="hidden" name="old_cate_code" value="<?= $rs_cate_code ?>">
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		</div>
		
		<div class="btn">
				<? if ($cate_no <> "" ) {?>
					<? if ($sPageRight_U == "Y") {?>
			<a href="javascript:sendKeyword();"><img src="../images/admin/btn_regist_02.gif" alt="등록" /></a>
					<? } ?>
				<? } else {?>
					<? if ($sPageRight_I == "Y") {?>
			<a href="javascript:sendKeyword();"><img src="../images/admin/btn_regist_02.gif" alt="등록" /></a>
					<? } ?>
				<? }?>
				<? if ($cate_no <> "") {?>
					<? if ($sPageRight_D == "Y") {?>
			<a href="javascript:js_delete();"><img src="../images/admin/btn_delete.gif" alt="삭제" /></a>
					<? } ?>
				<? }?>

		</div>

	</div>
	<br />
	<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
</div>
<input type="hidden" name="cate_flag" value="Y">
<input type="hidden" name="use_tf" value="Y">
</form>
</body>
</html>
<?
#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);
?>
