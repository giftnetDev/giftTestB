<?php

/* �ڵ����Թ��� ����*/
	session_start();
	header('Content-Type: image/gif');

	$captcha = '';

	/*����*/
	$patten = '123456789QWEERTYUIOPASZDFGHJKLZMXNCBVqpwoeirutyalskdjfhgzmxncbv'; //���� ����
	for($i = 0, $len = strlen($patten) -1; $i < 6; $i++){ //6���� ���� ����
		$captcha .= $patten[rand(0, $len)];
	}

	$_SESSION['capt'] = $captcha;
	
	$img = imagecreatetruecolor(60, 20); //ũ��
	imagefilledrectangle($img, 0,0,100,100,0xc80000); // ����
	imagestring($img, 5, 3, 3, $captcha, 0xffffff); //���� ����, ���ڻ���
	imageline($img,0,rand() % 20,100,rand() % 20, 0x001458); //�� ���� 
	imagegif($img);
	imagedestroy($img);
?>