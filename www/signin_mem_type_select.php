<?
	require "_common/home_pre_setting.php";
	
	require "_classes/biz/member/member.php";


?>
<!DOCTYPE html>
<html lang="ko">
<head>
<?
	require "_common/v2_header.php";
	
?>
<script>
	$(document).ready(function(){
		//약관동의
		$(".btn_go_normal").click(function(){
				var frm = document.frm;
				//일반회원 가입 페이지 주소
				frm.action="/signin.php";
				frm.submit();
		});

		$(".btn_go_supplier").click(function(){
				var frm = document.frm;
				//납품업체 가입 페이지 주소
				frm.action="/supplier_signin.php";
				frm.submit();
		});
	});
</script>
</head>
<body>
<?
	require "_common/v2_top.php";
?>
<!-- 회원가입 -->
<div class="container members signin">
    <h5 class="title">회원가입</h5>
    <div class="contents">
        <form name="frm" class="form-horizontal in-signin" method="post">
			<input type="hidden" name="mode" value="">
            <ul class="nav nav-pills navbar-right">
                <li class="active">사용자 유형</li>
                <li>약관동의</li>
                <li>정보입력</li>
                <li>가입완료</li>
            </ul>
            <div class="form-group">
                물건의 구매를 원하시면 "일반회원"을 물건의 납품을 원하시면 "납품업체"를 선택해주세요.
            </div>
			<div class="btns text-center" role="group">
                <button type="button" class="btn btn-default btn_go_normal active">일반회원</button>
                <button type="button" class="btn btn-default btn_go_supplier active">납품업체</button>
            </div>
        </form>
    </div>
</div>
<!-- // 회원가입 -->

<?
	require "_common/v2_footer.php";
?>

</body>
</html>

