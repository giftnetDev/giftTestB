<?
	require "../_common/home_pre_setting.php";

	require "../_classes/biz/member/member.php";

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
<meta http-equiv='Refresh' content='0; URL=/newHp/Nindex.php'>

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
	require "header.php";
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

		frm.mode.value = "S";
		frm.method = "post";
		
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function findIdpwPop() 
	{
		var frm = document.frm;

		//NewWindow("pop_search_idpw.php", 'pop_find_idpw','420','400','YES');
		NewWindow("pop_find_idpw.php", 'pop_find_idpw','430','420','YES');
	}

</script>
</head>
<body>
<div class="wrap">	
<?
	require "top.php";
?>

<!-- 로그인 -->

		<div class="detail_page">
			<div class="detail_page_inner">
				
				<div class="cart_info">
					<h2>
						Login / Join
					</h2><br>
					<form name="frm">
					<input type="hidden" name="mode">
				
					<div class="fl_50 border-right">
						<div class="tcenter" style="margin-top:40px;">
							<input type="text" placeholder="아이디" class="id" name="iid" id="iid"><br>						
							<input type="password" placeholder="패스워드" class="pw" name="pwd" id="pwd" onkeydown = "if(event.keyCode==13) js_login();" ><br>	
							<div class="tcenter_for_check"><input type="checkbox" name="iid_saved" id="iid_saved"> <label for="iid_saved" style="cursor:pointer;">아이디 저장</label></div>												
							<a href="javascript:js_login();" class="joomoon login_run">로그인</a>
						</div>
						<div class="tcenter_02" style="margin-top:-20px;">
							<a href="javascript:findIdpwPop();">아이디 / 비밀번호 찾기</a>
						</div>
					</form>	
				</div>							
					<div class="fl_50">
						<div class="tcenter_02">
							<br><br><br><br>아직 회원이 아니시라면, 회원가입 후 다양한<br>혜택을 만나보세요<br><br>
						</div>
						<div class="tcenter" style="margin-top:20px;">
							<a href="register.php" class="carting join_run">회원가입</a>
						</div>
					</div>
				
				<div class="bgc_01 elements" style="top:-70px;left:90%;"></div>
				<div class="bgc_02 elements" style="top:-60px;left:70%;"></div>
		
			</div>
		</div>
<!-- // 로그인 -->


<?
	require "footer.php";
?>

<script src="js/jquery.mousewheel.js"></script>
<script type="text/javascript">
	if($.cookie("cookie_id") != undefined) { 
		document.frm.iid.value = $.cookie("cookie_id");
		$("input[name=iid_saved]").prop("checked", true);
	}

	$(function() {
		  $(".wrapper").mousewheel(function(event, delta) {
		  this.scrollLeft -= (delta * 120);
		  event.preventDefault();
		  });
		})
	
</script>
</div>
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