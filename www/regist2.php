<?php
session_start();

	?>

<!DOCTYPE html>

<html>
<head>
	<title>�ڵ����Թ������� ����</title>
<script type="text/javascript">
	/* ���� ���ΰ�ħ */
	function refresh_captcha(){
		document.getElementById("capt_img").src="captcha.php?waste="+Math.random(); 
//capt_img id�� �ҷ��� �������� �������� ������
	}

function ConfirmCode()
{
    var frm = document.frm;
    alert(1);
    alert(frm.capt_no.value);
    alert(frm.captchaNm.value);    
}

</script>
</head>
<body>
<form method="post" action="join_ok.php">
		<h2>�ڵ����Թ������� �Է�</h2>
        <img src="captcha.php" alt="captcha" title="captcha" id="capt_img"/>
        <input type="text" name="captchaNm" />
        <input type="submit" vlaue="Ȯ��" />
    </form>
	<button onclick="refresh_captcha();">���ΰ�ħ</button>
	
</body>
</html>