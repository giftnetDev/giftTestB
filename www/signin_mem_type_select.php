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
		//�������
		$(".btn_go_normal").click(function(){
				var frm = document.frm;
				//�Ϲ�ȸ�� ���� ������ �ּ�
				frm.action="/signin.php";
				frm.submit();
		});

		$(".btn_go_supplier").click(function(){
				var frm = document.frm;
				//��ǰ��ü ���� ������ �ּ�
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
<!-- ȸ������ -->
<div class="container members signin">
    <h5 class="title">ȸ������</h5>
    <div class="contents">
        <form name="frm" class="form-horizontal in-signin" method="post">
			<input type="hidden" name="mode" value="">
            <ul class="nav nav-pills navbar-right">
                <li class="active">����� ����</li>
                <li>�������</li>
                <li>�����Է�</li>
                <li>���ԿϷ�</li>
            </ul>
            <div class="form-group">
                ������ ���Ÿ� ���Ͻø� "�Ϲ�ȸ��"�� ������ ��ǰ�� ���Ͻø� "��ǰ��ü"�� �������ּ���.
            </div>
			<div class="btns text-center" role="group">
                <button type="button" class="btn btn-default btn_go_normal active">�Ϲ�ȸ��</button>
                <button type="button" class="btn btn-default btn_go_supplier active">��ǰ��ü</button>
            </div>
        </form>
    </div>
</div>
<!-- // ȸ������ -->

<?
	require "_common/v2_footer.php";
?>

</body>
</html>

