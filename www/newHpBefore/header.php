
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr;" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>기프트넷</title>

<script src="js/babel.min.js"></script>
<script src="js/jquery.min.js"></script>
<script src="js/jquery_ui.js"></script>
<script src="js/homepage.js"></script>

<script src="js/all.js"></script>
<!-- <script src="js/homepage.js"></script> -->

<link rel="stylesheet" href="css/stylenew.css" type='text/css'>
<link rel="stylesheet" href="css/relatedPop.css" type="text/css">
<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/earlyaccess/nanumgothic.css">
<link rel="stylesheet" href="css/jquery_ui.css" type="text/css">
<script>
		function js_login(){

			
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
					
            frm.method="POST";
			frm.action="./manager/login/loginAction.php";
            frm.mode.value="LOGIN";
            frm.submit();

			return true;		
		
		}
	</script>
<style>
    #login_popup{
	    display:none;
    }
</style>
<!--<link href="css/style_v2.css?v=4" rel="stylesheet">
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">-->
