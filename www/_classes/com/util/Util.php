<?

	function redirectTohttps() { 
		if($_SERVER['HTTPS']!="on") { 
			$redirect = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			header("Location:$redirect"); 
		} 
	}

	function getContentImages($contents){  //�̹��� �±� ����
		$contents = stripslashes($contents); 
		
		$pattern = "'src=[\"|\'](.*?)[\"|\']'si"; 
		preg_match_all($pattern, $contents, $match); 
		return $match[1][0]; 
	} 

	function sendMail($EMAIL, $NAME, $SUBJECT, $CONTENT, $mailto){ // ���� ������

		$admin_email = $EMAIL;
		$admin_name  = $NAME;
	//	$mcontent=file_get_contents("idpwcheck_mail.html");
	//	$contents=str_replace("###password###",$CONTENT,$mcontent); 

		$header = "Return-Path:".$admin_email." \n";
		$header .= "MIME-Version: 1.0 \n";
		$header .= "Content-Type: text/html; charset=euc-kr \n";
		$header .= "X-Mailer: PHP \n";
		$header .= "Content-Transfer-Encoding: 8bit \n";
		$header .= "From:  ����Ʈ��<".$admin_email."> \n";
		$header .= "Bcc: ����Ʈ��<".$admin_email."> \n";
		$subject  = "=?EUC-KR?B?".base64_encode($SUBJECT)."?=\n";
		$contents = $CONTENT;

		$message = $contents;
//		$message = base64_encode($contents);
		flush();
		@mail($mailto, $subject, $message, $header);
	}

	// ���� ������ - ÷������ ���� - �ܱ�����(gmail)�� �̻������ ����, ���̹����Ͽ��� �Ⱥ���
	function sendMail2($EMAIL, $NAME, $SUBJECT, $mail_body, $mailto, $path, $filename) {  

		$admin_email = $EMAIL;
		$admin_name  = $NAME;

		$file = $path . "/" . $filename;
		$file_size = filesize($file);
		$handle = fopen($file, "r");
		$content = fread($handle, $file_size);
		fclose($handle);
		$content = chunk_split(base64_encode($content));

		// a random hash will be necessary to send mixed content
		$separator = md5(time());

		// carriage return type (we use a PHP end of line constant)
		$eol = "\n";

		// main header (multipart mandatory)
		$headers = "Return-Path:".$admin_email." ". $eol;
		$headers .= "MIME-Version: 1.0 ". $eol;
		$headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"" . $eol;
		$headers .= "X-Mailer: PHP ". $eol;
		$headers .= "Content-Transfer-Encoding: 8bit ". $eol;
		$headers .= "From:  ����Ʈ��<".$admin_email."> ". $eol;
		$headers .= "Bcc: ����Ʈ��<".$admin_email."> ". $eol;
		$subject  = "=?EUC-KR?B?".base64_encode($SUBJECT)."?=". $eol;

		// message
		$nmessage .= "--" . $separator . $eol;
		$nmessage .= "Content-Type: text/plain; charset=\"euc-kr\"" . $eol;
		$nmessage .= "Content-Transfer-Encoding: base64" . $eol;
		
		$nmessage .= chunk_split(base64_encode($mail_body)) . $eol;

		// attachment
		$nmessage .= "--" . $separator . $eol;
		$nmessage .= "Content-Type: application/octet-stream; name=\"" . $filename . "\"" . $eol;
		$nmessage .= "Content-Transfer-Encoding: base64" . $eol;
		$nmessage .= "Content-Disposition: attachment" . $eol;
		$nmessage .= $content . $eol;
		$nmessage .= "--" . $separator . "--";

		//SEND Mail
		if (mail($mailto, $subject, $nmessage, $headers)) {
			return true; 
		} else {
			return false;
		}
	}


	function mailer($from, $from_email, $to, $to_email, $subject, $content, $path, $filename) {
		 error_reporting(E_ALL ^ E_WARNING); 

		$content = nl2br($content);

		$error_msg = "";
	
		$mail = new PHPMailer(true);
		//$mail->IsSendmail();

		try {

			$mail->CharSet    = "euc-kr";
			//$mail->Encoding   = "base64";
			//$mail->CharSet    = "euckr";
			$to_email.=",giftneta@naver.com";
			$to_email = str_replace(",", ";", $to_email);

			//���� �߼� �����ϵ��� ���� 2016-06-30
			foreach (explode(";", $to_email) as $splited_to_email) {
				$mail->AddAddress($splited_to_email, $splited_to_email);
			}

			$mail->SetFrom($from_email, $from);
			$mail->AddReplyTo($from_email, $from);
			$mail->Subject = $subject;

			$mail->AddBCC($from_email);

			//$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
			$mail->MsgHTML($content);
			if($filename != "") $mail->AddAttachment($path."/".$filename, $filename);
		
			$mail->Send();
			
		} catch (phpmailerException $e) {

			echo $e->errorMessage();
			$error_msg .= "���� ���� �޼��� :".$e->errorMessage();
			//die;
			//continue;
		} catch (Exception $e) {
			echo $e->errorMessage();
			$error_msg .= "���� ���� �޼��� :".$e->getMessage();
			//die;
			//continue;
		}
		
		return $error_msg;
	}

/*
	function sendMailReservationConfirm($EMAIL, $NAME, $SUBJECT, $CONTENT, $mailto){ //��й�ȣã�� ����

		if ($EMAIL == "") {
			$EMAIL = "arum@arumjigi.org";
		}

		$admin_email = $EMAIL;
		$admin_name  = $NAME;

		$mcontent=file_get_contents("mail.html");

		//echo $NAME;

		$contents=str_replace("###name###",$NAME, $mcontent); 
		$contents=str_replace("###SUBJECT###",$SUBJECT,$contents); 
		$contents=str_replace("###CONTENT###",$CONTENT,$contents); 

		$header = "Return-Path:".$admin_email."\r\n";
		$header .= "MIME-Version: 1.0\r\n";
		$header .= "Content-Type: text/html; charset=euc-kr\r\n";
		$header .= "X-Mailer: PHP\r\n";
		$header .= "Content-Transfer-Encoding: 8bit\r\n";
		$header .= "From: (��) �Ƹ����� �Ծ��ѿ�<".$admin_email.">\r\n";
		$header .= "Reply-To: (��) �Ƹ����� �Ծ��ѿ�<".$admin_email.">\r\n";
		$subject  = "=?EUC-KR?B?".base64_encode($SUBJECT)."?=\n";
	//	$contents = $CONTENT;

		$message = $contents;
//		$message = base64_encode($contents);
		flush();
		@mail($mailto, $subject, $message, $header);
	}

	function sendMailReceipt($EMAIL, $TRANNO, $MEMNO, $TRANDATE, $GOODNAME, $SUPPLYAMTTOTAL, $TAXAMTTOTAL, $AMTTOTAL, $ADDR, $BIZ_NO, $BIZ_OW, $mailto){ //��й�ȣã�� ����
		
		if ($EMAIL == "") {
			$EMAIL = "arum@arumjigi.org";
		}
		
		$admin_email = $EMAIL;
		$admin_name  = $NAME;

		$mcontent=file_get_contents("receipt_mail.html");

		//echo $NAME;
		$SUBJECT = "(��) �Ƹ����⿡�� �Ʒ��� ���� ���ݿ������� ���� �Ͽ����ϴ�.";

		$contents=str_replace("###TRANNO###",$TRANNO, $mcontent); 
		$contents=str_replace("###MEMNO###",$MEMNO,$contents); 
		$contents=str_replace("###TRANDATE###",$TRANDATE,$contents); 
		$contents=str_replace("###GOODNAME###",$GOODNAME,$contents); 
		$contents=str_replace("###SUPPLYAMTTOTAL###",$SUPPLYAMTTOTAL,$contents); 
		$contents=str_replace("###TAXAMTTOTAL###",$TAXAMTTOTAL,$contents); 
		$contents=str_replace("###AMTTOTAL###",$AMTTOTAL,$contents); 
		$contents=str_replace("###ADDR###",$ADDR,$contents); 
		$contents=str_replace("###BIZ_OW###",$BIZ_OW,$contents); 
		$contents=str_replace("###BIZ_NO###",$BIZ_NO,$contents); 

		$header = "Return-Path:".$admin_email."\r\n";
		$header .= "MIME-Version: 1.0\r\n";
		$header .= "Content-Type: text/html; charset=euc-kr\r\n";
		$header .= "X-Mailer: PHP\r\n";
		$header .= "Content-Transfer-Encoding: 8bit\r\n";
		$header .= "From: ".$BIZ_OW."<".$admin_email.">\r\n";
		$header .= "Reply-To: ".$BIZ_OW."<".$admin_email.">\r\n";
		$subject  = "=?EUC-KR?B?".base64_encode($SUBJECT)."?=\n";
	//	$contents = $CONTENT;

		$message = $contents;
//		$message = base64_encode($contents);
		flush();
		@mail($mailto, $subject, $message, $header);
	}


	function new_member_sendMail($EMAIL, $NAME, $SUBJECT, $CONTENT, $mailto){//ȸ���������� ����

		$admin_email = $EMAIL;
		$admin_name  = $NAME;
		$mcontent=file_get_contents("mail01.html");
		$contents=str_replace("###name###",$NAME,$mcontent); 
		$contents=str_replace("###mem_id###",$CONTENT,$contents); 

		$header = "Return-Path:".$admin_email."\r\n";
		$header .= "MIME-Version: 1.0\r\n";
		$header .= "Content-Type: text/html; charset=euc-kr\r\n";
		$header .= "X-Mailer: PHP\r\n";
		$header .= "Content-Transfer-Encoding: 8bit\r\n";
		$header .= "From: (��) �Ƹ�����<".$admin_email.">\r\n";
		$header .= "Reply-To: (��) �Ƹ�����<".$admin_email.">\r\n";
		$subject  = "=?EUC-KR?B?".base64_encode($SUBJECT)."?=\n";
	//	$contents = $CONTENT;

		$message = $contents;
//		$message = base64_encode($contents);
		flush();
		@mail($mailto, $subject, $message, $header);
	}

*/

Function PageList($URL,$nPage,$TPage,$PBlock,$Ext) {

	$str = "";

	if ($TPage > 1) {

		$SPage = (int)(($nPage - 1) / $PBlock) * $PBlock + 1;
		$EPage = $SPage + $PBlock - 1;

		if ($TPage < $EPage) {
			$EPage = $TPage;
		}

		if ($nPage > 1) {
			$str = "<a href='".$URL."?nPage=".($nPage - 1).$Ext."'>PREV</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
		} else {
			$str = "PREV&nbsp;&nbsp;|&nbsp;&nbsp;";
		}

				
		$Cnt = 1;  # ���ڷ� �νĽ�Ŵ ���� ������ ����ü �ǰ� ����	
		for ($Cnt = $SPage; $Cnt <= $EPage ; $Cnt++) {
			if ($Cnt == (int)($nPage)) {
					$str = $str . "<b>" . $Cnt . "</b>&nbsp;&nbsp;|&nbsp;&nbsp;";
			} else {
					$str = $str . "<a href='".$URL."?nPage=".$Cnt.$Ext."'>" . $Cnt . "</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
			}
		}

		if ($nPage <> (int)($TPage)) {
			$str = $str . "<a href='".$URL."?nPage=".($nPage + 1).$Ext."'>NEXT</a>";
		} else {
			$str = $str . "NEXT";
		}
	
	} else {

		$str = "<b>1</b>";
	}

	return $str;


#	$str = "";
	
#	//echo "nPage->" .$nPage ."<br>";
#	//echo "PBlock->" .$PBlock ."<br>";

#	if ($TPage > 1) {

#		$SPage = ((int)(($nPage-1) / $PBlock)) * $PBlock + 1;
#		
#		echo "��� --> ".((int)(($nPage) / $PBlock)) * $PBlock ."<br>";
#		echo "SPage-->".$SPage."<br>";
#
#		$EPage = $SPage + $PBlock - 1;
#
#		if ($TPage < $EPage) {
#			$EPage = $TPage;
#		}
#
#		if ($SPage > 1) {
#			$str = "<a href='".$URL."?nPage=". ($SPage-1) . $Ext ."'>PREV</a>&nbsp;&nbsp;|&nbsp;";
#		} else {
#			$str = "PREV&nbsp;&nbsp;|&nbsp;";
#		}
#
#
#		$Cnt = 1;  # ���ڷ� �νĽ�Ŵ ���� ������ ����ü �ǰ� ����	
#		
#		for ($Cnt = $SPage; $Cnt < $EPage ; $Cnt++) {
#			if ($Cnt == (int)($nPage)) {
#					$str = $str . "<b>" . $Cnt . "</b>&nbsp;";
#			
#			} else {
#			
#					$str = $str . "<a href='". $URL . "?nPage=". $Cnt . $Ext ."'>" . $Cnt . "</a>&nbsp;";
#			
#			} 
#		} 
#			
#
#		if ($EPage < $TPage) {
#			$str = $str . "|&nbsp;&nbsp;<a href='" . $URL . "?nPage=" . ($EPage + 1) . $Ext . "'>NEXT</a>";
#		} else {
#			$str = $str . "|&nbsp;&nbsp;NEXT";
#		}
#	
#	} else {
#		$str = "<b>1</b>";
#	}

#	return $str;
}

function date_diff2($date1, $date2) 
{ 
    list($startYear, $startMonth, $startDay) = explode("-", substr($date1, 0, 10)); 
    list($endYear, $endMonth, $endDay) = explode("-", substr($date2, 0, 10)); 

    if($startYear < 1970 || $startMonth < 1 || $startMonth > 12){ 
        echo "<script type='text/javascript' language='javascript'>alert('�ùٸ� ��¥������ �ƴմϴ�.');</script>"; 
        return false; 
    } 
    if($endYear < 1970 || $endMonth < 1 || $endMonth > 12){ 
        echo "<script type='text/javascript' language='javascript'>alert('�ùٸ� ��¥������ �ƴմϴ�.');</script>"; 
        return false; 
    } 

    $startTimestamp = mktime(0, 0, 0, $startMonth, $startDay, $startYear); 
    $endTimestamp = mktime(0, 0, 0, $endMonth, $endDay, $endYear); 
         
    if($startTimestamp != $endTimestamp){ 
        $diffTimestamp = ($startTimestamp > $endTimestamp ? ($startTimestamp-$endTimestamp) : ($endTimestamp-$startTimestamp)); 
        return $diffDate = round($diffTimestamp / 86400);        // �Ϸ�� 60*60*24 �� 
    } 
    else 
        return $diffDate = 0; 
}

Function PageListAsForm($script_name,$nPage,$TPage,$PBlock, $nPageSize) {

	$str = "";

	if ($TPage > 1) {

		$SPage = ((int)(($nPage - 1) / $PBlock)) * $PBlock + 1;
		$EPage = $SPage + $PBlock - 1;

		if ($TPage < $EPage) {
			$EPage = $TPage;
		}

		if ($nPage > 1) {
			$str = "<a href='javascript:". $script_name ."(". ($nPage - 1) . ");'>PREV</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
		} else {
			$str = "PREV&nbsp;&nbsp;|&nbsp;&nbsp;";
		}

				
		$Cnt = 1;  # ���ڷ� �νĽ�Ŵ ���� ������ ����ü �ǰ� ����	
		for ($Cnt = $SPage; $Cnt <= $EPage ; $Cnt++) {
			if ($Cnt == (int)($nPage)) {
					$str = $str . "<b>" . $Cnt . "</b>&nbsp;&nbsp;|&nbsp;&nbsp;";
			} else {
					$str = $str . "<a href='javascript:". $script_name . "(". $Cnt . ");'>" . $Cnt . "</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
			}
		}

		if ($nPage <> (int)($TPage)) {
			$str = $str . "<a href='javascript:" . $script_name . "(". ($nPage + 1) . ");'>NEXT</a>";
		} else {
			$str = $str . "NEXT";
		}
	
	} else {

		$str = "<b>1</b>";
	}

	return $str;
}


/**  
 * ���� ���ε�   
 *  
 * @param array $filearray  // ���� �迭 $_FILES['file']   
 * @param string $targetdir  
 * @param integer $max_size  
 * @param array $allowext  
 * @return boolean (FALSE) or string (uploaded filename)  
 *   
 * ����  
 *   
 * upload('���Ϲ迭', '���ε� ���丮', '�뷮MB����', '���Ȯ����');  
 * upload($_FILE['filename'], '/home/userdir/public_html/data/board/', 1, array('gif', 'exe', 'jpeg', 'jpg'));  
 *   
 * 1. ��Ȯ�� Ȯ���� ó���� ���ؼ��� ��������� �м��ؾ� �մϴ�.   
 * 2. ��Ȯ�� ���ε带 ���� ������ ������ �����ڵ带 ó���ϴ°͵� �ʿ��մϴ�.   
 */  
  

function upload($filearray, $targetdir, $max_size = 1 /* MByte */, $allowext) {
	$max_size = $max_size * 1024 * 1024 * 1;    // ����Ʈ�� ����Ѵ�. 1MB = 1024KB = 1048576Byte   
	

	//echo $targetdir;
	if (!file_exists($targetdir)) { 
		//echo "targetdir --> ".$targetdir."<br>";
		mkdir($targetdir, 0777);
		//exec("mkdir -p ".$targetdir);                # ���丮 �����
	}


	if($filearray['size'] > $max_size){
		// echo "������ �ʰ�<br>";

		return fasle;
	} 
	else {
		// echo "������ ����<br>";
		//try {   // Ư���� ��츦 ���� ����ó��    
			/**  
			 * �̺κ��� ���ϸ��� ������ �κ��� ���Ͻ�ŵ�ϴ�.   
			 *   
			 * ���� ��� test.jpeg �̸� jpeg�� �����ɴϴ�.  
			 */  
			$file_ext = end(explode('.', $filearray['name']));   
			// echo "file_ext : ".$file_ext."<br>";

			

			$file_real_name = str_replace(".".$file_ext,"",$filearray['name']);
			/** �Լ� end  
			 * end �� �迭�� ������ ���Ҹ� ����Ű�� �� �� ������ ���Ҹ� ���� ��ŵ�ϴ�.   
			 * array('a', 'b', 'c' , 'd') �̸� 'd'�� ���Ͻ�ŵ�ϴ�.  
			 */  
			   
			/** �Լ� explode   
			 * explode �� ������ ���ڿ� �� �ڵ忡���� "." �� �̿��ؼ� �迭�� �����ϴ�.   
			 * ���ڿ� a.b.c.d �� explode �� ��ģ�Ŀ���  
			 * array('a', 'b', 'c','d') �� �˴ϴ�.   
			 */  
			   
			/**  
			 * in_array �� �迭�� �ش� ���� �ִ��� ã���ϴ�.   
			 * array(ã������, �迭)   
			 * ã���� true ��ã���� false�� ����մϴ�.  
			 */  


			// echo "inner_01<br>";
			if (in_array(strtolower($file_ext), $allowext)) { // Ȯ���ڸ� �˻��Ѵ�. 
				// echo "inner_02<br>";
				// echo "====================succeess======================";  
			
				//���� _ �� ��ġ 
				//$temp_file_name = str_replace(" ","_",$filearray['name']);

				//�ѱ� ���ϸ� ó���� ���� �ӽ� ���ϸ��� ��¥�� ����
				$writeday = date("Ymd_His",strtotime("0 day"));
				$temp_file_name = $writeday.".".$file_ext;

				$file_name = get_filename_check($targetdir, $temp_file_name);

				//$file_name = $file_real_name."-".mktime() . '.' . $file_ext;    
				// �ߺ��� ������ ���ε� �ɼ� �����Ƿ� time�Լ��� �̿��� unixtime���� �����̸��� ������ְ�   
				// �� �� ���� Ȯ���ڸ� �ٿ��ݴϴ�. ��Ȯ���� �� ������δ� ���Ͼ��ε带 ��Ȯ�� �ߺ��� üũ�ߴٰ� �Ҽ� �����ϴ�.   
				
				$path = $targetdir . '/' . $file_name;   
				// ���� ���� ��θ� ����� �ݴϴ�. �Լ� ����ÿ� �Է¹��� ��θ� �̿��ؼ� ����� �ݴϴ�.   
				// echo "path : ".$path."<br>"; 
// 
				// echo "inner_03<br>";

				if(move_uploaded_file($filearray['tmp_name'], $path))    
				{   
					// ���������� ���ε� �ߴٸ� ���ε�� ���ϸ��� �������ϴ�   
					// �̺κп� DB�� ���� ������ �־��ֽðų� ���ϸ��� �����ϴ� �κ��� �־��ֽø� �˴ϴ�.    
					// �Ǵ� ���ϵ� ���ϸ����� ó�� �Ͻø� �˴ϴ�.    
					return $file_name;
				}
				else return false;   
				// ���� ���� ��쿡�� false�� ����մϴ�.   
			
			}
			else{ //return false;
				// echo "inner_ELSE<br>";
				// echo "====================error=================";
				if($file_ext!=""){
				?>
				<SCRIPT LANGUAGE="JavaScript">
				<!--
					alert('����Ҽ� ���� Ȯ���� �Դϴ�.');
					history.back();
				//-->
				</SCRIPT>
				<?
					die;
				}
			}
		}
	
		//catch (Exception $e) {
		//	throw new Exception('���� ���ε忡 ���� �Ͽ����ϴ�.');
		//}
	//}
}


function multiupload($filearray, $cnt, $targetdir, $max_size = 20 /* MByte */, $allowext) {
	$max_size = $max_size * 1024 * 1024;    // ����Ʈ�� ����Ѵ�. 1MB = 1024KB = 1048576Byte   

	if (!file_exists($targetdir)) { 
		//echo "targetdir --> ".$targetdir."<br>";
		mkdir($targetdir, 0777);
		//exec("mkdir -p ".$targetdir);                # ���丮 �����
	}

	if($filearray['size'][$cnt] > $max_size) return false;
	else {
		//try {   // Ư���� ��츦 ���� ����ó��    
			/**  
			 * �̺κ��� ���ϸ��� ������ �κ��� ���Ͻ�ŵ�ϴ�.   
			 *   
			 * ���� ��� test.jpeg �̸� jpeg�� �����ɴϴ�.  
			 */  
			$file_ext = end(explode('.', $filearray['name'][$cnt]));   

			$file_real_name = str_replace(".".$file_ext,"",$filearray['name'][$cnt]);
			/** �Լ� end  
			 * end �� �迭�� ������ ���Ҹ� ����Ű�� �� �� ������ ���Ҹ� ���� ��ŵ�ϴ�.   
			 * array('a', 'b', 'c' , 'd') �̸� 'd'�� ���Ͻ�ŵ�ϴ�.  
			 */  
			   
			/** �Լ� explode   
			 * explode �� ������ ���ڿ� �� �ڵ忡���� "." �� �̿��ؼ� �迭�� �����ϴ�.   
			 * ���ڿ� a.b.c.d �� explode �� ��ģ�Ŀ���  
			 * array('a', 'b', 'c','d') �� �˴ϴ�.   
			 */  
			   
			/**  
			 * in_array �� �迭�� �ش� ���� �ִ��� ã���ϴ�.   
			 * array(ã������, �迭)   
			 * ã���� true ��ã���� false�� ����մϴ�.  
			 */  

			if(in_array(strtolower($file_ext), $allowext)) { // Ȯ���ڸ� �˻��Ѵ�.   
			
				//���� _ �� ��ġ 
				$temp_file_name = str_replace(" ","_",$filearray['name'][$cnt]);

				//�ѱ� ���ϸ� ó���� ���� �ӽ� ���ϸ��� ��¥�� ����
				$writeday = date("Ymd",strtotime("0 day"));
				$temp_file_name = $writeday.".".$file_ext;

				$file_name = get_filename_check($targetdir, $temp_file_name);

				//$file_name = $file_real_name."-".mktime() . '.' . $file_ext;    
				// �ߺ��� ������ ���ε� �ɼ� �����Ƿ� time�Լ��� �̿��� unixtime���� �����̸��� ������ְ�   
				// �� �� ���� Ȯ���ڸ� �ٿ��ݴϴ�. ��Ȯ���� �� ������δ� ���Ͼ��ε带 ��Ȯ�� �ߺ��� üũ�ߴٰ� �Ҽ� �����ϴ�.   
				
				$path = $targetdir . '/' . $file_name;   
				// ���� ���� ��θ� ����� �ݴϴ�. �Լ� ����ÿ� �Է¹��� ��θ� �̿��ؼ� ����� �ݴϴ�.    
		
				if(move_uploaded_file($filearray['tmp_name'][$cnt], $path))    
				{   
					// ���������� ���ε� �ߴٸ� ���ε�� ���ϸ��� �������ϴ�   
					// �̺κп� DB�� ���� ������ �־��ֽðų� ���ϸ��� �����ϴ� �κ��� �־��ֽø� �˴ϴ�.    
					// �Ǵ� ���ϵ� ���ϸ����� ó�� �Ͻø� �˴ϴ�.    
					return $file_name;
				}
				else return false;   
				// ���� ���� ��쿡�� false�� ����մϴ�.   
			}
			else return false;
		}
	
		//catch (Exception $e) {
		//	throw new Exception('���� ���ε忡 ���� �Ͽ����ϴ�.');
		//}
	//}
}


//���� �ߺ� üũ
function get_filename_check($filepath, $filename) { 

	if (!preg_match("'/$'", $filepath)) $filepath .= '/'; 
	if (is_file($filepath . $filename)) { 

		preg_match("'^([^.]+)(\[([0-9]+)\])(\.[^.]+)$'", $filename, $match); 

		if (empty($match)) { 

			$filename = preg_replace("`^([^.]+)(\.[^.]+)$`", "\\1[1]\\2", $filename); 
		} 
		else{ 

			$filename = $match[1] . '[' . ($match[3] + 1) . ']' . $match[4]; 
		} 

		return get_filename_check($filepath, $filename); 
	} 
	else { 

		return $filename; 
	} 
} 


/*		

		<div id="bbspgno">
			<ul class="bnk">
				<li class="bnk"><a href="#"><img src="../images/common/bbs/bu_prev01.gif" alt="����" /></a></li>
				<li class="bnk"><strong class="sel">1</strong></li>
				<li class="bnk"><img src="../images/common/bbs/ver_bar.gif" alt="" /></li>
				<li class="bnk"><a href="#">2</a></li>
				<li class="bnk"><img src="../images/common/bbs/ver_bar.gif" alt="" /></li>
				<li class="bnk"><a href="#">3</a></li>
				<li class="bnk"><img src="../images/common/bbs/ver_bar.gif" alt="" /></li>
				<li class="bnk"><a href="#">4</a></li>
				<li class="bnk"><img src="../images/common/bbs/ver_bar.gif" alt="" /></li>
				<li class="bnk"><a href="#">5</a></li>
				<li class="bnk"><img src="../images/common/bbs/ver_bar.gif" alt="" /></li>
				<li class="bnk"><a href="#">6</a></li>
				<li class="bnk"><img src="../images/common/bbs/ver_bar.gif" alt="" /></li>
				<li class="bnk"><a href="#">7</a></li>
				<li class="bnk"><img src="../images/common/bbs/ver_bar.gif" alt="" /></li>
				<li class="bnk"><a href="#">8</a></li>
				<li class="bnk"><img src="../images/common/bbs/ver_bar.gif" alt="" /></li>
				<li class="bnk"><a href="#">9</a></li>
				<li class="bnk"><a href="#"><img src="../images/common/bbs/bu_next01.gif" alt="����" /></a></li>
			</ul>
		</div>*/

// ������ ǥ��
// ������ ǥ��
function Image_PageList ($URL, $nPage, $TPage, $PBlock, $Ext) {

	$str = "";

	$SPage = (int)(($nPage - 1) / $PBlock) * $PBlock + 1;
	$EPage = $SPage + $PBlock - 1;

	if ($TPage > 1 ) {

		$intTemp = (int)(($nPage - 1) / $PBlock) * $PBlock + 1;
		$intLoop = 1;

		if ($TPage < $EPage) {
			$EPage = $TPage;
		}
	
		$str = "<div class=\"paging\">\n";
		
		# ���� ���
		if ($intTemp == 1) {
			$str .= "<span class=\"arr\"><a href=".$URL."?nPage=1&".$Ext."><img src=\"/manager/images/admin/pag_first_bu.gif\" alt=\"�� ó��\"></a></span>\n";
			$str .= "<span class=\"arr\"><a href=\"#\"><img src=\"/manager/images/admin/pag_first.gif\" alt=\"����".$PBlock."��\"></a></span>\n";
		} else {
			$str .= "<span class=\"arr\"><a href=".$URL."?nPage=1&".$Ext."><img src=\"/manager/images/admin/pag_first_bu.gif\" alt=\"�� ó��\"></a></span>\n";
			$str .= "<span class=\"arr\"><a href=".$URL."?nPage=".($intTemp - $PBlock)."&".$Ext.">";
			$str .= "<img src=\"/manager/images/admin/pag_first.gif\" alt=\"����".$PBlock."��\">";
			$str .= "</a></span>\n";
		}
		
		# ���� ������
		if ($nPage == 1) {
			$str .= "<span class=\"arr\"><a href=\"#\"><img src=\"/manager/images/admin/pag_prev.gif\" alt=\"��������\" /></a></span>\n";
		} else {
			$str .= "<span class=\"arr\"><a href=".$URL."?nPage=".($nPage - 1)."&".$Ext.">";
			$str .= "<img src=\"/manager/images/admin/pag_prev.gif\" alt=\"��������\" />";
			$str .= "</a></span> ";
		}
		

		# ������

		$Cnt = 1;  # ���ڷ� �νĽ�Ŵ ���� ������ ����ü �ǰ� ����	
		for ($Cnt = $SPage; $Cnt <= $EPage ; $Cnt++) {
			if ($Cnt == (int)($nPage)) {
				$str .= "<span><a href=\"".$URL."?nPage=".$Cnt."&".$Ext."\" class=\"selected\">" .$Cnt. "</a></span>\n";
			} else {
				$str .= "<span><a href=\"".$URL."?nPage=".$Cnt."&".$Ext."\" >" .$Cnt. "</a></span>\n";
			}
			$intTemp++;
		}
	
		# ���� ������
		if ($nPage >= $TPage) {
			$str .= "<span class=\"arr\"><a href=\"#\"><img src=\"/manager/images/admin/pag_next.gif\" alt=\"��������\" /></a></span>\n";
		} else {
			$str .= "<span class=\"arr\"><a href=".$URL."?nPage=" .($nPage + 1). "&".$Ext.">";
			$str .= "<img src=\"/manager/images/admin/pag_next.gif\" alt=\"��������\" />";
			$str .= "</a></span>\n";
		}
		
		# ���� ���
		if ($intTemp > $TPage) {
			$str .= "<span class=\"arr\"><a href=\"#\"><img src=\"/manager/images/admin/pag_final.gif\" alt=\"����".$PBlock."��\"></a></span>\n";
			$str .= "<span class=\"arr\"><a href=".$URL."?nPage=".$TPage."&".$Ext."><img src=\"/manager/images/admin/pag_first_bu.gif\" alt=\"�� ������\"></a></span>\n";
		} else {
			$str .= "<span class=\"arr\"><a href=".$URL."?nPage=" .$intTemp. "&".$Ext.">";
			$str .= "<img src=\"/manager/images/admin/pag_final.gif\" alt=\"����".$PBlock."��\">";
			$str .= "</a></span>\n";
			$str .= "<span class=\"arr\"><a href=".$URL."?nPage=".$TPage."&".$Ext."><img src=\"/manager/images/admin/pag_first_bu.gif\" alt=\"�� ������\"></a></span>\n";
		}
		
		$str .= "</div>";
		
		
	}
	return $str;
}

// ������ ǥ��


/*
		<div id="bbspgno">
			<ul class="bnk">
				<li class="bnk"><a href="#" onFocus="blur();"><img src="../images/bbs/prev02.gif" alt="ó��" /></a></li>
				<li class="bnk"><a href="#" onFocus="blur();"><img src="../images/bbs/prev01.gif" alt="����" /></a></li>
				<li class="bnk"><strong class="sel">1</strong></li>
				<li class="bnk"><a href="#" onFocus="blur();">2</a></li>
				<li class="bnk"><a href="#" onFocus="blur();">3</a></li>
				<li class="bnk"><a href="#" onFocus="blur();">4</a></li>
				<li class="bnk"><a href="#" onFocus="blur();">5</a></li>
				<li class="bnk"><a href="#" onFocus="blur();">6</a></li>
				<li class="bnk"><a href="#" onFocus="blur();">7</a></li>
				<li class="bnk"><a href="#" onFocus="blur();">8</a></li>
				<li class="bnk"><a href="#" onFocus="blur();">9</a></li>
				<li class="bnk"><a href="#" onFocus="blur();"><img src="../images/bbs/next01.gif" alt="����" /></a></li>
				<li class="bnk"><a href="#"  onFocus="blur();"><img src="../images/bbs/next02.gif" alt="�ǳ�" /></a></li>
			</ul>
		</div>

		<div id="bbspgno">
			<ul class="bnk">
				<li class="bnk"><a href="#"><img src="../images/common/bbs/bu_prev02.gif" alt="�Ǿ�" /></a></li>
				<li class="bnk"><a href="#"><img src="../images/common/bbs/bu_prev01.gif" alt="����" /></a></li>
				<li class="bnk"><strong class="sel">1</strong></li>
				<li class="bnk"><a href="#">2</a></li>
				<li class="bnk"><a href="#">3</a></li>
				<li class="bnk"><a href="#">4</a></li>
				<li class="bnk"><a href="#">5</a></li>
				<li class="bnk"><a href="#">6</a></li>
				<li class="bnk"><a href="#">7</a></li>
				<li class="bnk"><a href="#">8</a></li>
				<li class="bnk"><a href="#">9</a></li>
				<li class="bnk"><a href="#"><img src="../images/common/bbs/bu_next01.gif" alt="����" /></a></li>
				<li class="bnk"><a href="#"><img src="../images/common/bbs/bu_next02.gif" alt="�ǵ�" /></a></li>
			</ul>
		</div>
*/


// ���θ� ��ǰ ����Ʈ�� ����¡
function Front_Image_PageList ($URL, $nPage, $TPage, $PBlock, $Ext) {
	
	/*
	echo $nPage."<br>";
	echo $TPage."<br>";
	echo $PBlock."<br>";
	*/

	$str = "";

	$SPage = (int)(($nPage - 1) / $PBlock) * $PBlock + 1;
	$EPage = $SPage + $PBlock - 1;

	if ($TPage > 1 ) {

		$intTemp = (int)(($nPage - 1) / $PBlock) * $PBlock + 1;
		$intLoop = 1;

		if ($TPage < $EPage) {
			$EPage = $TPage;
		}
	
		$str  = "<div id=\"paging\">\n";
		$str .= "<ul>\n";
		
		# ���� ���
		if ($intTemp == 1) {
			//$str .= "<li><a href=".$URL."?nPage=1&".$Ext."><img src=\"../images/bbs/prev2.gif\" alt=\"�� ó��\"></a></li>\n";
			$str .= "<li><a href=\"#\"><img src=\"../images/bbs/prev2.gif\" alt=\"����".$PBlock."��\"></a></span>\n";
		} else {
			//$str .= "<li><a href=".$URL."?nPage=1&".$Ext."><img src=\"../images/bbs/prev2.gif\" alt=\"�� ó��\"></a></li>\n";
			$str .= "<li><a href=".$URL."?nPage=".($intTemp - $PBlock)."&".$Ext.">";
			$str .= "<img src=\"../images/bbs/prev2.gif\" alt=\"����".$PBlock."��\">";
			$str .= "</a></li>\n";
		}
		
		# ���� ������
		if ($nPage == 1) {
			$str .= "<li><a href=\"#\"><img src=\"../images/bbs/prev.gif\" alt=\"��������\" /></a></li>\n";
		} else {
			$str .= "<li><a href=".$URL."?nPage=".($nPage - 1)."&".$Ext.">";
			$str .= "<img src=\"../images/bbs/prev.gif\" alt=\"��������\" />";
			$str .= "</a></li> ";
		}
		

		# ������

		$Cnt = 1;  # ���ڷ� �νĽ�Ŵ ���� ������ ����ü �ǰ� ����	
		for ($Cnt = $SPage; $Cnt <= $EPage ; $Cnt++) {
			if ($Cnt == (int)($nPage)) {
				$str .= "<li><strong class='sel'>" .$Cnt. "</strong></li>\n";
			} else {
				$str .= "<li><a href=\"".$URL."?nPage=".$Cnt."&".$Ext."\" >" .$Cnt. "</a></li>\n";
			}
			$intTemp++;
		}
	
		# ���� ������
		if ($nPage >= $TPage) {
			$str .= "<li><a href=\"#\"><img src=\"../images/bbs/next.gif\" alt=\"��������\" /></a></li>\n";
		} else {
			$str .= "<li><a href=".$URL."?nPage=" .($nPage + 1). "&".$Ext.">";
			$str .= "<img src=\"../images/bbs/next.gif\" alt=\"��������\" />";
			$str .= "</a></li>\n";
		}
		
		# ���� ���
		if ($intTemp > $TPage) {
			$str .= "<li><a href=\"#\"><img src=\"../images/bbs/next2.gif\" alt=\"����".$PBlock."��\"></a></li>\n";
			//$str .= "<li><a href=".$URL."?nPage=".$TPage."&".$Ext."><img src=\"../images/admin/pag_first_bu.gif\" alt=\"�� ������\"></a></li>\n";
		} else {
			$str .= "<li><a href=".$URL."?nPage=" .$intTemp. "&".$Ext.">";
			$str .= "<img src=\"../images/bbs/next2.gif\" alt=\"����".$PBlock."��\">";
			$str .= "</a></li>\n";
			//$str .= "<li><a href=".$URL."?nPage=".$TPage."&".$Ext."><img src=\"../images/admin/pag_first_bu.gif\" alt=\"�� ������\"></a></li>\n";
		}
		$str .= "</ul>";
		$str .= "</div>";
		
		
	}
	return $str;
}

//���ڿ� �ڸ���
function TextCut($str,$start,$len,$suffix = "...") {
	$lenth=$len - $start;   
	
	if (strlen($str)>$lenth) {  //���� �ڸ��� �ȴٸ� ǥ�� 
		$ok=1;
	}
 
	$str = trim($str); 
	$backcnt= 0; // ����ù���ڿ��� �ڷΰ� byte �� (space�� ��/���ڰ� ���ö� ����) 
	$cntcheck =0; 
	
	if ($start>0 ) { 
		if(ord($str[$start]) >= 128) { // ù ���۱��ڰ� �ѱ��̸� 
			for ($i=$start;$i>0;$i--) { 
				if (ord($str[$i]) >= 128) { 
					$backcnt++; 
				} else { 
					break; 
				} 
			}
			
			$start= ($backcnt%2) ? $start : $start-1; //ù��¥�� ������, ������ = (���� byte -1byte) 

			if (($backcnt%2)==1) { 
				$cntcheck = 0; //���� ���� ù���� ��©�� 
			} else { 
				$cntcheck = 1; //���� ���� ù���� ©�� 
			} 

		}
	}

	$backcnt2= 0; // ���������ڿ��� �ڷΰ� byte �� (space�� ��/���ڰ� ���ö� ����) 
	
	for ($i=($len-1);$i>=0;$i--) { 
		if (ord($str[$i+$start]) >= 128) { 
			$backcnt2++; 
		} else { 
			break; 
		} 
	} 

	if (($backcnt2%2)==1) { 
		$cntcheck2 = 1; //���� ������ ���� ©�� 
	} else { 
		$cntcheck2 = 0; //���� ������ ���� ��©�� 
	} 

	(int)$cnt=$len-abs($backcnt2%2); //�ڸ� ���ڿ� ���� (byte) 
	if(($cntcheck+$cntcheck2)==2) $cnt+=2; //$cntcheck�� ©����, $cntcheck2�� ©���� ��� 
	$cutstr = substr($str,$start,$cnt); 
	if ($ok){$cutstr .= $suffix;}  ///�߶��� ��쿡�� ���� ... ���� 
	return $cutstr; 
}


// DB�� �Է� �ϱ�
function SetStringToDB($str) {
	
	$temp_str = "";
	
	$temp_str = trim($str);
	$temp_str = addslashes($temp_str);

	return $temp_str; 
}

// DB���� ������
function SetStringFromDB($str) {
	
	$temp_str = "";
	
	$temp_str = trim($str);
	$temp_str = stripslashes($temp_str);
	//�⺻������ htmlentitle�� ����ϰ� ����ȭ �Ҷ��� ���� �ҵ� 2017-05-04
	$temp_str = str_replace("\"","&quot;", $temp_str);

	return $temp_str; 
}

// Ȯ���� ���ϱ� 
function file_ext($str) {
	$ext1 = strrev($str);
	$ext2 = explode(".",$ext1);
	return strrev($ext2[0]);

}

function byteConvert($bytes) {
	$s = array('B', 'Kb', 'MB', 'GB', 'TB', 'PB'); 
	$e = floor(log($bytes)/log(1024)); 
	return sprintf('%.2f '.$s[$e], ($bytes/pow(1024, floor($e)))); 
} 

function cleanQuery($string) {
	if(get_magic_quotes_gpc()) { // prevents duplicate backslashes
		
		$string = stripslashes($string);
	}

	if (phpversion() >= '4.3.0') {
		$string = mysql_real_escape_string($string);
	}	else {
		$string = mysql_escape_string($string);
	}
	return $string;
}


function getFileIcon($str) {
	
	$string = "";
	
	$str = strtolower($str); 
	
	//echo $str;

	switch ($str) {

		case 'ai' ;
			$string = "/kor/images/icon/icon_ai.gif";
			break;

		case 'doc' ;
			$string = "/kor/images/icon/icon_doc.gif";
			break;

		case 'docx' ;
			$string = "/kor/images/icon/icon_doc.gif";
			break;

		case 'txt' ;
			$string = "/kor/images/icon/icon_document.gif";
			break;

		case 'exe' ;
			$string = "/kor/images/icon/icon_exe.gif";
			break;

		case 'xls' ;
			$string = "/kor/images/icon/icon_exel.gif";
			break;

		case 'xlsx' ;
			$string = "/kor/images/icon/icon_exel.gif";
			break;

		case 'fla' ;
			$string = "/kor/images/icon/icon_fla.gif";
			break;

		case 'gif' ;
			$string = "/kor/images/icon/icon_gif.gif";
			break;

		case 'zip'; 'rar' ; 'gz' ; 'tgz' ;
			$string = "/kor/images/icon/icon_gz.gif";
			break;

		case 'htm' ; 'html' ;
			$string = "/kor/images/icon/icon_htm.gif";
			break;

		case 'hwp' ;
			$string = "/kor/images/icon/icon_hwp.gif";
			break;

		case 'jpg' ;
			$string = "/kor/images/icon/icon_jpg.gif";
			break;

		case 'mp3' ;
			$string = "/kor/images/icon/icon_mp3.gif";
			break;

		case 'pdf' ;
			$string = "/kor/images/icon/icon_pdf.gif";
			break;

		case 'ppt' ; 'pptx' ;
			$string = "/kor/images/icon/icon_ppt.gif";
			break;

		case 'wmv' ;
			$string = "/kor/images/icon/icon_wm.gif";
			break;

		case 'qm' ;
			$string = "/kor/images/icon/icon_qm.gif";
			break;

		default ; 
			$string = "/kor/images/icon/icon_disk.gif";
			break;
		
	}
	
	if ($str == "") $string = "/kor/images/icon/blank.gif";

	return $string;

}

function go($str) {
	
	$string = "";
	
	$string .= "<html>";
	$string .= "<script type='text/javascript'>";
	$string .= "document.frm.submit();";
	$string .= "</script>";
	$string .= "<body onload='init();'>";
	$string .= "<form name='frm' action='".$str."' target='_parent'>";
	$string .= "</form>";
	$string .= "</body>";
	$string .= "</html>";

	return $string;
}

function chkDate($str, $format) {

	if ($format == "YYYY-MM-DD") {

		//$yyyy = date("Y",strtotime($str));
		//$mm = date("m",strtotime($str));
		//$dd = date("d",strtotime($str));
		
		$yyyy = left($str, 4);
		$mm = substr($str, 5,2);
		$dd = right($str,2);
		/*
		echo $yyyy;
		echo $mm;
		echo $dd;
		*/

		return checkdate($mm , $dd, $yyyy);

	} else {
		return false;
	}

}

function get_text($str, $html=0)
{
    /* 3.22 ���� (HTML üũ �ٹٲ޽� ��� ��������)
    $source[] = "/  /";
    $target[] = " &nbsp;";
    */

    // 3.31
    // TEXT ����� ��� &amp; &nbsp; ���� �ڵ带 �������� ����� �ֱ� ����
    if ($html == 0) {
        $str = html_symbol($str);
    }

    $source[] = "/</";
    $target[] = "&lt;";
    $source[] = "/>/";
    $target[] = "&gt;";
    //$source[] = "/\"/";
    //$target[] = "&#034;";
    $source[] = "/\'/";
    $target[] = "&#039;";
    //$source[] = "/}/"; $target[] = "&#125;";
    if ($html) {
        $source[] = "/\n/";
        $target[] = "<br/>";
    }

    return preg_replace($source, $target, $str);
}

function html_symbol($str)
{
	return preg_replace("/\&([a-z0-9]{1,20}|\#[0-9]{0,3});/i", "&#038;\\1;", $str);
}

// ���Ǻ��� ����
function set_session($session_name, $value)
{
	//session_register($session_name);
	// PHP ������ ���̸� ���ֱ� ���� ���
	$$session_name = $_SESSION["$session_name"] = $value;
}


// ���Ǻ����� ����
function get_session($session_name)
{
	return $_SESSION[$session_name];
}

function right($value, $count){
	$value = substr($value, (strlen($value) - $count), strlen($value));
	return $value;
}

function left($string, $count){
	return substr($string, 0, $count);
}

function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}
function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}

function base64url_encode($str) {
    return strtr(base64_encode($str), '+/', '-_');
}
function base64url_decode($base64url) {
    return base64_decode(strtr($base64url, '-_', '+/'));
}


function downloadFile($url, $path)
{
	// echo "$path<br>";
	// exit;
	$newfname = $path;
	$file = fopen ($url, 'rb');
	if ($file) {
		$newf = fopen ($newfname, 'wb');
		if ($newf) {
			while(!feof($file)) {
				fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
			}
		}
	}
	if ($file) {
		fclose($file);
	}
	if ($newf) {
		fclose($newf);
	}
}

function br2nl( $input ) {
    $input = preg_replace('#<br[/\s]*>#si', "", $input);
	$input = trim($input);

	return $input;
}

function br2nl4Excel( $input ) {
    $input = preg_replace('#<br[/\s]*>#si', "\n", $input);
	$input = trim($input);

	return $input;
}


function getSafeNumberFormatted($value) {

	if($value <> "")
		return number_format($value);
	else 
		return "0";

}

function getRowcount($text, $width=55) {
    $rc = 0;
    $line = explode("\n", $text);
    foreach($line as $source) {
        $rc += intval((strlen($source) / $width) +1);
    }
    return $rc;
}

function ceiling($value, $precision = 0) {
    return ceil($value * pow(10, $precision)) / pow(10, $precision);
}


//EXCEL - NUMBERSTRING() �Լ� ����
//���� : http://www.tipssoft.com/bulletin/board.php?bo_table=update&wr_id=998
function NUMBERSTRING($parm_money)
{
	//�޸�����
	$parm_money = str_replace(",", "", $parm_money);

	// ���� �ݾ��� ������ ǥ���ϴ� ���ڵ��� ���� ���̺��� �����Ѵ�.
	$p_unit_table = array("õ", "��", "��", "��", "õ", "��", "��", "��", "õ", "��", "��", "��", "õ", "��", "��", "��");

	// �ƶ��� ����ǥ���� ���ڵ��� ���� ���̺��� �����Ѵ�.
	$p_number_table = array("��", "��", "��", "��", "��", "��", "ĥ", "��", "��");

	// �Է¹��� ���ڸ� ���� �ݾ����� �ٲٱ� ���� ������ �����Ѵ�.

	$temp_data = $parm_money;
	$result_data = "";

	// ��ȯ�� ���ڿ� temp_data�� ���̸� ���Ѵ�.
	$cnt = strlen($temp_data);

	// ���ڿ��� ���̸�ŭ �ݺ����� �����Ѵ�.
	for($i = 0; $i < $cnt; $i++){

		// ���ڰ� '0' �� �ƴ� ���
		if($temp_data[$i] != '0'){
			// result_data ������ �ڿ� �˸��� ���� ���ڸ� �߰��Ѵ�.
			// ���� temp_data[i] �� 8�� ����Ǿ� �ִٸ� 
			// temp_data[i] �� 1�� �� 7�� p_number_table�� �ε����� �Ǿ�
			// p_number_table[7] �� ���� "��"�� result_data�� �߰� �ȴ�.
			// p_number_table�� �ε��� - 1 �� ���ڿ� �ش��ϴ� ���ڰ� ����Ǿ� �ִ�.
			$result_data .= $p_number_table[$temp_data[$i] - '0' - 1];
			
			// result_data ������ �ڿ� �˸��� �ݾ� ���� ���ڸ� �߰��Ѵ�.
			// ���� �Է¹��� ���� 206000 �̶��, count = 6
			// 2 : p_unit_table[16 - 6 + 0]  => ��
			// 0 : �ؿ� else ���� ����ȴ�.
			// 6 : p_unit_table[16 - 6 + 2]  => õ
			// 000 : �ؿ� else ���� ����ȴ�.
			$result_data .= $p_unit_table[16 - $cnt + $i];

		// ���ڰ� '0' �� ���
		} else {
			// ���� �Է¹��� ���� 206000 �̶��, count = 6
			// 2 : ���� if������ ����Ǿ��⶧���� ������� ����.   => ��
			// 0 : (16 - 6 + 1 + 1) % 4 �� 0�̹Ƿ� 
			//     result_data ������ �ڿ� �˸��� ���� ���ڸ� �߰��Ѵ�.
			//     p_unit_table[16 - 6 + 1]                        => ��
			// 6 : ���� if������ ����Ǿ��⶧���� ������� ����.   => õ
			// 0 : (16 - 6 + 3 + 1) % 4 �� 1�̹Ƿ� ���� ������ �������� �ʴ´�.
			// 0 : (16 - 6 + 4 + 1) % 4 �� 1�̹Ƿ� ���� ������ �������� �ʴ´�.
			// 0 : (16 - 6 + 5 + 1) % 4 �� 0�̹Ƿ�
			//     result_data ������ �ڿ� �˸��� ���� ���ڸ� �߰��Ѵ�.
			//     p_unit_table[16 - 6 + 5]                        => ��
			if(!((16 - $cnt + $i + 1) % 4)) 
				$result_data .= $p_unit_table[16 - $cnt + $i];
		}
	}
	return $result_data;
}

//���� �迭�˻� - DB ȣ�Ⱚ���� ���� ã����
function in_array_r($needle, $haystack, $strict = false) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
            return true;
        }
    }

    return false;
}


function cleanUTF($string) {
   $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.

   return preg_replace('/[^\x{1100}-\x{11FF}\x{3130}-\x{318F}\x{AC00}-\x{D7AF}\w]/u','',$string); // Removes special chars.
}


function get_tax($amount, $method) {
 switch ($method) {
  case 'A' :                                  // �ΰ�������
   $supply = $amount;                         // ���ް�
   $tax = 0;                                 // �ΰ���
  break;
  case 'B' :                                  // �ΰ�������
   $supply = $amount;                         // ���ް�
   $tax = $supply * 0.1;                        // �ΰ���
  break;
  case 'C' :                                  // �ΰ�������
   $supply = $amount / 1.1;
   $tax = $amount - $supply;
  break;
  case 'G' :                                  // �ΰ������� - ����Ʈ��
   $supply = round($amount / 1.1);
   $tax = $amount - $supply;
  break;
 }
 $supply = round($supply);                      // ������ �հ�ݾ׿��� �ݿø� �� ��
 $tax = round($tax);
 return array($supply, $tax);
}

function getNameFromNumber($num) {
    $numeric = ($num - 1) % 26;
    $letter = chr(65 + $numeric);
    $num2 = intval(($num - 1) / 26);
    if ($num2 > 0) {
        return getNameFromNumber($num2) . $letter;
    } else {
        return $letter;
    }
}

function get_domain() {
	// output: /myproject/index.php
	$currentPath = $_SERVER['PHP_SELF']; 

	// output: Array ( [dirname] => /myproject [basename] => index.php [extension] => php [filename] => index ) 
	$pathInfo = pathinfo($currentPath); 

	// output: localhost
	$hostName = $_SERVER['HTTP_HOST']; 

	// output: http://
	$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';

	// return: http://localhost/myproject/
	return $protocol.'://'.$hostName;
}

function isWeekend($date) {
    return (date('N', strtotime($date)) >= 6);
}
?>
