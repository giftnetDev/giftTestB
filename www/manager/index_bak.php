<?session_start();?>
<?

#====================================================================
# common_header Check Session
#====================================================================
//	include "$_SERVER[DOCUMENT_ROOT]/common/common_header.php"; 

// www redirect
if(substr($_SERVER[HTTP_HOST],0,4) != "www.")
{ 
	if(strpos($_SERVER[HTTP_HOST], 'cafe24.com') === false)
	{
		header('Location: http://www.'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
		exit;
	}
} 



#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../_classes/com/db/DBUtil.php";

	if ($_SESSION['s_adm_no'] <> "") {

		$next_url = "./main.php";

?>
<meta http-equiv='Refresh' content='0; URL=<?=$next_url?>'>
<!--
<script language="javascript">
		location.href =  '/index.php';
</script>	
-->
<?
			exit;
	}
	
	$conn = db_connection("w");
	
#=====================================================================
# common function, login_function
#=====================================================================
	require "../_common/config.php";
	require "../_classes/com/util/Util.php";
	require "../_classes/com/etc/etc.php";
	require "../_classes/biz/admin/admin.php";


#====================================================================
# Request Parameter
#====================================================================
	$adm_id			= trim($_POST['adm_id']);
	$passwd			= trim($_POST['adm_pw']);
	$mode				= trim($_POST['mode']);

	if ($mode == "S" && $adm_id != "" && $passwd != "") {

		$is_remote = false;
		//if(startsWith($_SERVER['REMOTE_ADDR'], "112.161"))
		if(startsWith($_SERVER['REMOTE_ADDR'], "221.162")||startsWith($_SERVER['REMOTE_ADDR'], "112.161"))
			$is_remote = true;

		$arr_rs = confirmAdmin($conn, $adm_id);

		$rs_adm_no				= trim($arr_rs[0]["ADM_NO"]); 
		$rs_adm_id				= trim($arr_rs[0]["ADM_ID"]); 
		$rs_passwd				= trim($arr_rs[0]["PASSWD"]); 
		$rs_adm_name			= trim($arr_rs[0]["ADM_NAME"]); 
		$rs_adm_email			= trim($arr_rs[0]["ADM_EMAIL"]); 
		$rs_group_no			= trim($arr_rs[0]["GROUP_NO"]); 
		$rs_com_code			= trim($arr_rs[0]["COM_CODE"]); 
		$rs_cp_type				= trim($arr_rs[0]["CP_TYPE"]); 
		$rs_md_tf				= trim($arr_rs[0]["MD_TF"]); 
		
		//echo $rs_cp_type;
		//echo $passwd."<br>";
		//echo $rs_passwd;

		$result = "";

		if ($rs_adm_no == "") {
			$result = "1";
			$str_result = "해당 아이디가 없습니다.";
		} else {

			if ($rs_passwd == $passwd) {

				if($rs_adm_id == "jjoccoba77" || $rs_adm_id == "jh.yang" || $rs_adm_id == "nambbo" || $rs_adm_id == "system" || $rs_adm_id == "gong" || $rs_adm_id == "ks.whang" || $rs_adm_id == "nims" || $rs_adm_id == "cih0705" || $rs_adm_id == "bkj0121") { 
					
					$result = "0";
					$str_result = "";

				} else { 

					if(!$is_remote) { 
						$result = "3";
						$str_result = "알수없는 지역에서의 접근입니다.";

					} else { 
						$result = "0";
						$str_result = "";
					}
				}
			} else {
				$result = "2";
				$str_result = "회원 정보가 일치 하지 않습니다. 다시 확인 부탁 드립니다.";
			}
		}
		
		// result 0 : 승인 , 1 : 아이디 없음, 2 : 비밀번호 틀림
		if ($result == "0") {
			
			//session_register('s_adm_no','s_pb_no','s_adm_id','s_adm_nm','s_adm_email','s_adm_type');
			$_SESSION['s_adm_no']				= $rs_adm_no;
			$_SESSION['s_adm_id']				= $rs_adm_id;
			$_SESSION['s_adm_nm']				= $rs_adm_name;
			$_SESSION['s_adm_email']		= $rs_adm_email;
			$_SESSION['s_adm_group_no']	= $rs_group_no;
			$_SESSION['s_adm_com_code']	= $rs_com_code;
			$_SESSION['s_adm_cp_type']	= $rs_cp_type;
			$_SESSION['s_adm_md_tf']	= $rs_md_tf;

			insertUserLog($conn, 'AO', $rs_adm_id, $_SERVER['REMOTE_ADDR']);
			
			$next_url = "./main.php";		
?>
<meta http-equiv='Refresh' content='0; URL=<?=$next_url?>'>
<?
			exit;
		} else {
			insertUserLog($conn, 'AX', $adm_id, $_SERVER['REMOTE_ADDR']);

?>
<meta http-equiv='Refresh' content='0; URL=<?=$_SERVER[PHP_SELF]?>'>
<?

			
		}
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_chrset?>" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
<link rel="icon" type="image/x-icon" href="/" />
<title><?=$g_title?></title>
<link type="text/css" rel="stylesheet" href="../manager/css/reset.css" />
<script language="javascript" type="text/javascript" src="./js/common.js"></script>

<script language="javascript" type="text/javascript">
	function js_login() {

		var frm = document.frm;

		if (frm.adm_id.value == "") {
			alert("관리자의 아이디를 입력해 주세요.");
			frm.adm_id.focus();
			return;
		}

		if (frm.adm_pw.value == "") {
			alert("관리자의 패스워드를 입력해 주세요.");
			frm.adm_pw.focus();
			return;
		}
				
		if (frm.adm_id.value != "" && frm.adm_pw.value != "") {
			frm.action="<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}
</script>
</head>

<body id="admin" style="background: none;">
<div id="loginwrap">
<form name="frm" method="post">
<input type="hidden" name="mode" value="S">
	<div class="sp30"></div>
	<p>
		<b>기프트넷 (B)동 SCM 사이트 입니다.</b><br /><br />
		<br /><br />
	</p>
	<div class="sp60"></div>
	<dl>
		<dt><img src="./images/admin/tit_adminpage.gif" alt="Administrator page 관리자님께서는 아이디와 비밀번호를 입력해 주시기 바랍니다." /></dt>
		<dd><img src="./images/admin/hd_id.gif" class="hd" alt="ID" /> <input type="text" name="adm_id" value="<?= $adm_id?>" maxlength="20" class="txt" style="ime-mode:inactive;"/></dd>
		<dd><img src="./images/admin/hd_pw.gif" class="hd" alt="PW" /> <input type="password" name="adm_pw" value="" class="txt" style="ime-mode:inactive;" onmouseup="return false;" onkeydown = "if(event.keyCode==13) js_login();"/></dd>
		<dd class="btn_login"><input type="image" onclick="js_login();" src="./images/admin/btn_login.gif" alt="LOGIN"></dd>
		<dd class="webmag"><strong>기프트넷</strong> 관리를 위한 페이지입니다!!!</dd>
	</dl>  
	<div class="copyright_login"><?=$_SERVER['REMOTE_ADDR']?></div>
</div>
</form>
</body>
</html>
<?

#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>

<?
	if ($result != "0" && $result != "") {
?>		
<script language="javascript">
	alert('<?= $str_result ?>');
</script>
<?
	}
?>
