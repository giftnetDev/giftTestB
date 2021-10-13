<?
	require "_common/home_pre_setting.php";

	require "_classes/biz/member/member.php";

	$iid			= trim($_POST['iid']);
	$pwd			= trim(MD5($_POST['pwd']));

	if($mode == "S") { 
		//echo $iid."<br/>";
		//echo $pwd."<br/>";

		$arr_rs = chkMember($conn, $iid);

		//MEM_NO, MEM_NM, MEM_PW, EMAIL, PHONE, HPHONE, CP_NO

		if(sizeof($arr_rs) > 0) { 
			$rs_mem_no				= trim($arr_rs[0]["MEM_NO"]); 
			$rs_mem_type			= trim($arr_rs[0]["MEM_TYPE"]); 
			$rs_mem_nm				= trim($arr_rs[0]["MEM_NM"]); 
			$rs_mem_pw				= trim($arr_rs[0]["MEM_PW"]); 
			$rs_email				= trim($arr_rs[0]["EMAIL"]); 
			$rs_phone				= trim($arr_rs[0]["PHONE"]);
			$rs_hphone				= trim($arr_rs[0]["HPHONE"]);
			$rs_cp_no				= trim($arr_rs[0]["CP_NO"]);
			$rs_use_tf				= trim($arr_rs[0]["USE_TF"]); 
			$rs_del_tf				= trim($arr_rs[0]["DEL_TF"]); 
		}

		$result = "";

		if (sizeof($arr_rs) <= 0 || $rs_use_tf == "N") 
		{
			$result = "1";
			$str_result = "해당 아이디가 없습니다. 다시 확인 부탁 드립니다.";
		} else {

			if ($rs_mem_pw == $pwd) {
				$result = "0";
				$str_result = "";
			} else {
				$result = "2";
				$str_result = "회원 정보가 일치 하지 않습니다. 다시 확인 부탁 드립니다.";
			}
		}

		// result 0 : 승인 , 1 : 아이디 없음, 2 : 비밀번호 틀림
		if ($result == "0") {
			
			$_SESSION['C_MEM_NO']				= $rs_mem_no;
			$_SESSION['C_MEM_NM']				= $rs_mem_nm;
			$_SESSION['C_MEM_ID']				= $iid;
			$_SESSION['C_CP_NO']				= $rs_cp_no;
			$_SESSION['C_CP_NM']				= getCompanyNameWithNoCode($conn, $rs_cp_no);
			$_SESSION['C_MEM_TYPE']			= $rs_mem_type;
			insertUserLog($conn, "MO", $rs_mem_nm, $_SERVER['REMOTE_ADDR']);
			
?>
<meta http-equiv='Refresh' content='0; URL=/'>

<?
			exit;
		} else { 
			insertUserLog($conn, 'MX', $iid, $_SERVER['REMOTE_ADDR']);
		}
	}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<?
	require "_common/v2_header.php";
?>

<script language="javascript" type="text/javascript">
	function js_login() {

		var frm = document.frm;

		if (frm.iid.value == "") {
			alert("아이디를 입력해 주세요.");
			frm.iid.focus();
			return false;
		} else {
			if($("input[name=iid_saved]").is(":checked"))
				$.cookie("cookie_id", frm.iid.value);
			else
				$.removeCookie("cookie_id");
		}

		if (frm.pwd.value == "") {
			alert("패스워드를 입력해 주세요.");
			frm.pwd.focus();
			return false;
		}
				
		frm.action="<?=$_SERVER[PHP_SELF]?>";

		return true;
	}

	function findIdpwPop() 
	{
		var frm = document.frm;

		NewWindow("pop_search_idpw.php", 'search_company','420','400','YES');
	}

</script>
</head>
<body>
<?
	require "_common/v2_top.php";
?>
<!-- 로그인 -->
<div class="container members login">
    <h5 class="title">로그인/회원가입</h5>
    <div class="contents">
        <form class="form-horizontal in-login" name="frm" method="post" onsubmit="return js_login();">
		<input type="hidden" name="mode" value="S">
          <div class="form-group">
            <label class="control-label col-sm-3" for="iid">아이디</label>
            <div class="col-sm-10 col-lg-offset-0 col-lg-9">
              <input type="id" class="form-control" name="iid" id="iid" placeholder=""><label><span><input type="checkbox" name="iid_saved"> 아이디 저장</label></span>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-3" for="pwd">비밀번호</label>
            <div class="col-sm-10 col-lg-9"> 
              <input type="password" class="form-control" name="pwd" id="pwd" placeholder="">
            </div>
          </div>
          <div class="form-group"> 
            <div class="col-sm-offset-2 col-sm-10 col-lg-9 col-lg-offset-3">
              <button type="submit" class="btn btn-default">로그인</button>
            </div>
          </div>
          <p class="find"><a href="javascript:findIdpwPop();">아이디/비밀번호 찾기</a></p>
        </form>
        
        <div class="in-signin">
            <p>아직 회원이 아니시라면, 회원가입 후 다양한 혜택을 만나보세요.</p>
            <!--<a href="signin_mem_type_select.php"><button type="button" class="btn btn-default" >회원가입</button></a>-->
			<a href="regist.php"><button type="button" class="btn btn-default" >회원가입</button></a>
        </div>                
    </div>
</div>
<!-- // 로그인 -->


<?
	require "_common/v2_footer.php";
?>

<script type="text/javascript">
	if($.cookie("cookie_id") != undefined) { 
		document.frm.iid.value = $.cookie("cookie_id");
		$("input[name=iid_saved]").prop("checked", true);
	}

	
</script>
</body>
</html>


<?
	if ($result != "0" && $str_result != "") {
?>		
<script language="javascript">
	alert('<?= $str_result ?>');
</script>
<?
	}
?>