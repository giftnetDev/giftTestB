<?php 
session_start();
	if($_SESSION['capt'] != $_POST['captcha'])
		{
			echo "<script>alert('1�ڵ����Թ��������� �ùٸ��� �ʽ��ϴ�.');</script>";
		}else{
			echo "<script>alert('2�ڵ����Թ��������� ��Ȯ�ϰ� �Է��ϼ̽��ϴ�.');</script>";
		}
	?>
<meta http-equiv="refresh" content="0 url=/">