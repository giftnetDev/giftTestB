<?

	function redirectTohttps() { 
		if($_SERVER['HTTPS']!="on") { 
			$redirect = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			header("Location:$redirect"); 
		} 
	}

	function getContentImages($contents){  //이미지 태그 축출
		$contents = stripslashes($contents); 
		
		$pattern = "'src=[\"|\'](.*?)[\"|\']'si"; 
		preg_match_all($pattern, $contents, $match); 
		return $match[1][0]; 
	} 

	function sendMail($EMAIL, $NAME, $SUBJECT, $CONTENT, $mailto){ // 메일 보내기

		$admin_email = $EMAIL;
		$admin_name  = $NAME;
	//	$mcontent=file_get_contents("idpwcheck_mail.html");
	//	$contents=str_replace("###password###",$CONTENT,$mcontent); 

		$header = "Return-Path:".$admin_email." \n";
		$header .= "MIME-Version: 1.0 \n";
		$header .= "Content-Type: text/html; charset=euc-kr \n";
		$header .= "X-Mailer: PHP \n";
		$header .= "Content-Transfer-Encoding: 8bit \n";
		$header .= "From:  기프트넷<".$admin_email."> \n";
		$header .= "Bcc: 기프트넷<".$admin_email."> \n";
		$subject  = "=?EUC-KR?B?".base64_encode($SUBJECT)."?=\n";
		$contents = $CONTENT;

		$message = $contents;
//		$message = base64_encode($contents);
		flush();
		@mail($mailto, $subject, $message, $header);
	}

	// 메일 보내기 - 첨부파일 포함 - 외국메일(gmail)은 이상없으나 다음, 네이버메일에선 안보임
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
		$headers .= "From:  기프트넷<".$admin_email."> ". $eol;
		$headers .= "Bcc: 기프트넷<".$admin_email."> ". $eol;
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

			//다중 발송 가능하도록 수정 2016-06-30
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
			$error_msg .= "메일 에러 메세지 :".$e->errorMessage();
			//die;
			//continue;
		} catch (Exception $e) {
			echo $e->errorMessage();
			$error_msg .= "메일 에러 메세지 :".$e->getMessage();
			//die;
			//continue;
		}
		
		return $error_msg;
	}

/*
	function sendMailReservationConfirm($EMAIL, $NAME, $SUBJECT, $CONTENT, $mailto){ //비밀번호찾기 메일

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
		$header .= "From: (재) 아름지기 함양한옥<".$admin_email.">\r\n";
		$header .= "Reply-To: (재) 아름지기 함양한옥<".$admin_email.">\r\n";
		$subject  = "=?EUC-KR?B?".base64_encode($SUBJECT)."?=\n";
	//	$contents = $CONTENT;

		$message = $contents;
//		$message = base64_encode($contents);
		flush();
		@mail($mailto, $subject, $message, $header);
	}

	function sendMailReceipt($EMAIL, $TRANNO, $MEMNO, $TRANDATE, $GOODNAME, $SUPPLYAMTTOTAL, $TAXAMTTOTAL, $AMTTOTAL, $ADDR, $BIZ_NO, $BIZ_OW, $mailto){ //비밀번호찾기 메일
		
		if ($EMAIL == "") {
			$EMAIL = "arum@arumjigi.org";
		}
		
		$admin_email = $EMAIL;
		$admin_name  = $NAME;

		$mcontent=file_get_contents("receipt_mail.html");

		//echo $NAME;
		$SUBJECT = "(재) 아름지기에서 아래와 같이 현금영수증을 발행 하였습니다.";

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


	function new_member_sendMail($EMAIL, $NAME, $SUBJECT, $CONTENT, $mailto){//회원가입축하 메일

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
		$header .= "From: (재) 아름지기<".$admin_email.">\r\n";
		$header .= "Reply-To: (재) 아름지기<".$admin_email.">\r\n";
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

				
		$Cnt = 1;  # 숫자로 인식시킴 현재 페이지 볼드체 되게 수정	
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
#		echo "계산 --> ".((int)(($nPage) / $PBlock)) * $PBlock ."<br>";
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
#		$Cnt = 1;  # 숫자로 인식시킴 현재 페이지 볼드체 되게 수정	
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
        echo "<script type='text/javascript' language='javascript'>alert('올바른 날짜형식이 아닙니다.');</script>"; 
        return false; 
    } 
    if($endYear < 1970 || $endMonth < 1 || $endMonth > 12){ 
        echo "<script type='text/javascript' language='javascript'>alert('올바른 날짜형식이 아닙니다.');</script>"; 
        return false; 
    } 

    $startTimestamp = mktime(0, 0, 0, $startMonth, $startDay, $startYear); 
    $endTimestamp = mktime(0, 0, 0, $endMonth, $endDay, $endYear); 
         
    if($startTimestamp != $endTimestamp){ 
        $diffTimestamp = ($startTimestamp > $endTimestamp ? ($startTimestamp-$endTimestamp) : ($endTimestamp-$startTimestamp)); 
        return $diffDate = round($diffTimestamp / 86400);        // 하루는 60*60*24 초 
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

				
		$Cnt = 1;  # 숫자로 인식시킴 현재 페이지 볼드체 되게 수정	
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
 * 파일 업로드   
 *  
 * @param array $filearray  // 파일 배열 $_FILES['file']   
 * @param string $targetdir  
 * @param integer $max_size  
 * @param array $allowext  
 * @return boolean (FALSE) or string (uploaded filename)  
 *   
 * 사용법  
 *   
 * upload('파일배열', '업로드 디렉토리', '용량MB단위', '허용확장자');  
 * upload($_FILE['filename'], '/home/userdir/public_html/data/board/', 1, array('gif', 'exe', 'jpeg', 'jpg'));  
 *   
 * 1. 정확한 확장자 처리를 위해서는 파일헤더를 분석해야 합니다.   
 * 2. 정확히 업로드를 위해 저장전 파일의 오류코드를 처리하는것도 필요합니다.   
 */  
  

function upload($filearray, $targetdir, $max_size = 1 /* MByte */, $allowext) {
	$max_size = $max_size * 1024 * 1024 * 1;    // 바이트로 계산한다. 1MB = 1024KB = 1048576Byte   
	

	//echo $targetdir;
	if (!file_exists($targetdir)) { 
		//echo "targetdir --> ".$targetdir."<br>";
		mkdir($targetdir, 0777);
		//exec("mkdir -p ".$targetdir);                # 디렉토리 만들기
	}


	if($filearray['size'] > $max_size){
		// echo "사이즈 초과<br>";

		return fasle;
	} 
	else {
		// echo "사이즈 적정<br>";
		//try {   // 특별한 경우를 위해 예외처리    
			/**  
			 * 이부분은 파일명의 마지막 부분을 리턴시킵니다.   
			 *   
			 * 예를 들어 test.jpeg 이면 jpeg를 가져옵니다.  
			 */  
			$file_ext = end(explode('.', $filearray['name']));   
			// echo "file_ext : ".$file_ext."<br>";

			

			$file_real_name = str_replace(".".$file_ext,"",$filearray['name']);
			/** 함수 end  
			 * end 는 배열의 마지막 원소를 가리키게 한 후 마지막 원소를 리턴 시킵니다.   
			 * array('a', 'b', 'c' , 'd') 이면 'd'를 리턴시킵니다.  
			 */  
			   
			/** 함수 explode   
			 * explode 는 정해진 문자열 위 코드에서는 "." 를 이용해서 배열로 나눕니다.   
			 * 문자열 a.b.c.d 는 explode 를 거친후에는  
			 * array('a', 'b', 'c','d') 가 됩니다.   
			 */  
			   
			/**  
			 * in_array 는 배열에 해당 값이 있는지 찾습니다.   
			 * array(찾을원소, 배열)   
			 * 찾으면 true 못찾으면 false를 출력합니다.  
			 */  


			// echo "inner_01<br>";
			if (in_array(strtolower($file_ext), $allowext)) { // 확장자를 검사한다. 
				// echo "inner_02<br>";
				// echo "====================succeess======================";  
			
				//공백 _ 로 대치 
				//$temp_file_name = str_replace(" ","_",$filearray['name']);

				//한글 파일명 처리를 위해 임시 파일명을 날짜로 만듦
				$writeday = date("Ymd_His",strtotime("0 day"));
				$temp_file_name = $writeday.".".$file_ext;

				$file_name = get_filename_check($targetdir, $temp_file_name);

				//$file_name = $file_real_name."-".mktime() . '.' . $file_ext;    
				// 중복된 파일이 업로드 될수 있으므로 time함수를 이용해 unixtime으로 파일이름을 만들어주고   
				// 그 후 파일 확장자를 붙여줍니다. 정확히는 이 방식으로는 파일업로드를 정확히 중복을 체크했다고 할수 없습니다.   
				
				$path = $targetdir . '/' . $file_name;   
				// 파일 저장 경로를 만들어 줍니다. 함수 실행시에 입력받은 경로를 이용해서 만들어 줍니다.   
				// echo "path : ".$path."<br>"; 
// 
				// echo "inner_03<br>";

				if(move_uploaded_file($filearray['tmp_name'], $path))    
				{   
					// 정상적으로 업로드 했다면 업로드된 파일명을 내보냅니다   
					// 이부분에 DB에 저장 구문을 넣어주시거나 파일명을 저장하는 부분을 넣어주시면 됩니다.    
					// 또는 리턴된 파일명으로 처리 하시면 됩니다.    
					return $file_name;
				}
				else return false;   
				// 실패 했을 경우에는 false를 출력합니다.   
			
			}
			else{ //return false;
				// echo "inner_ELSE<br>";
				// echo "====================error=================";
				if($file_ext!=""){
				?>
				<SCRIPT LANGUAGE="JavaScript">
				<!--
					alert('등록할수 없는 확장자 입니다.');
					history.back();
				//-->
				</SCRIPT>
				<?
					die;
				}
			}
		}
	
		//catch (Exception $e) {
		//	throw new Exception('파일 업로드에 실패 하였습니다.');
		//}
	//}
}


function multiupload($filearray, $cnt, $targetdir, $max_size = 20 /* MByte */, $allowext) {
	$max_size = $max_size * 1024 * 1024;    // 바이트로 계산한다. 1MB = 1024KB = 1048576Byte   

	if (!file_exists($targetdir)) { 
		//echo "targetdir --> ".$targetdir."<br>";
		mkdir($targetdir, 0777);
		//exec("mkdir -p ".$targetdir);                # 디렉토리 만들기
	}

	if($filearray['size'][$cnt] > $max_size) return false;
	else {
		//try {   // 특별한 경우를 위해 예외처리    
			/**  
			 * 이부분은 파일명의 마지막 부분을 리턴시킵니다.   
			 *   
			 * 예를 들어 test.jpeg 이면 jpeg를 가져옵니다.  
			 */  
			$file_ext = end(explode('.', $filearray['name'][$cnt]));   

			$file_real_name = str_replace(".".$file_ext,"",$filearray['name'][$cnt]);
			/** 함수 end  
			 * end 는 배열의 마지막 원소를 가리키게 한 후 마지막 원소를 리턴 시킵니다.   
			 * array('a', 'b', 'c' , 'd') 이면 'd'를 리턴시킵니다.  
			 */  
			   
			/** 함수 explode   
			 * explode 는 정해진 문자열 위 코드에서는 "." 를 이용해서 배열로 나눕니다.   
			 * 문자열 a.b.c.d 는 explode 를 거친후에는  
			 * array('a', 'b', 'c','d') 가 됩니다.   
			 */  
			   
			/**  
			 * in_array 는 배열에 해당 값이 있는지 찾습니다.   
			 * array(찾을원소, 배열)   
			 * 찾으면 true 못찾으면 false를 출력합니다.  
			 */  

			if(in_array(strtolower($file_ext), $allowext)) { // 확장자를 검사한다.   
			
				//공백 _ 로 대치 
				$temp_file_name = str_replace(" ","_",$filearray['name'][$cnt]);

				//한글 파일명 처리를 위해 임시 파일명을 날짜로 만듦
				$writeday = date("Ymd",strtotime("0 day"));
				$temp_file_name = $writeday.".".$file_ext;

				$file_name = get_filename_check($targetdir, $temp_file_name);

				//$file_name = $file_real_name."-".mktime() . '.' . $file_ext;    
				// 중복된 파일이 업로드 될수 있으므로 time함수를 이용해 unixtime으로 파일이름을 만들어주고   
				// 그 후 파일 확장자를 붙여줍니다. 정확히는 이 방식으로는 파일업로드를 정확히 중복을 체크했다고 할수 없습니다.   
				
				$path = $targetdir . '/' . $file_name;   
				// 파일 저장 경로를 만들어 줍니다. 함수 실행시에 입력받은 경로를 이용해서 만들어 줍니다.    
		
				if(move_uploaded_file($filearray['tmp_name'][$cnt], $path))    
				{   
					// 정상적으로 업로드 했다면 업로드된 파일명을 내보냅니다   
					// 이부분에 DB에 저장 구문을 넣어주시거나 파일명을 저장하는 부분을 넣어주시면 됩니다.    
					// 또는 리턴된 파일명으로 처리 하시면 됩니다.    
					return $file_name;
				}
				else return false;   
				// 실패 했을 경우에는 false를 출력합니다.   
			}
			else return false;
		}
	
		//catch (Exception $e) {
		//	throw new Exception('파일 업로드에 실패 하였습니다.');
		//}
	//}
}


//파일 중복 체크
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
				<li class="bnk"><a href="#"><img src="../images/common/bbs/bu_prev01.gif" alt="이전" /></a></li>
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
				<li class="bnk"><a href="#"><img src="../images/common/bbs/bu_next01.gif" alt="다음" /></a></li>
			</ul>
		</div>*/

// 페이지 표시
// 페이지 표시
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
		
		# 이전 블록
		if ($intTemp == 1) {
			$str .= "<span class=\"arr\"><a href=".$URL."?nPage=1&".$Ext."><img src=\"/manager/images/admin/pag_first_bu.gif\" alt=\"맨 처음\"></a></span>\n";
			$str .= "<span class=\"arr\"><a href=\"#\"><img src=\"/manager/images/admin/pag_first.gif\" alt=\"이전".$PBlock."개\"></a></span>\n";
		} else {
			$str .= "<span class=\"arr\"><a href=".$URL."?nPage=1&".$Ext."><img src=\"/manager/images/admin/pag_first_bu.gif\" alt=\"맨 처음\"></a></span>\n";
			$str .= "<span class=\"arr\"><a href=".$URL."?nPage=".($intTemp - $PBlock)."&".$Ext.">";
			$str .= "<img src=\"/manager/images/admin/pag_first.gif\" alt=\"이전".$PBlock."개\">";
			$str .= "</a></span>\n";
		}
		
		# 이전 페이지
		if ($nPage == 1) {
			$str .= "<span class=\"arr\"><a href=\"#\"><img src=\"/manager/images/admin/pag_prev.gif\" alt=\"이전으로\" /></a></span>\n";
		} else {
			$str .= "<span class=\"arr\"><a href=".$URL."?nPage=".($nPage - 1)."&".$Ext.">";
			$str .= "<img src=\"/manager/images/admin/pag_prev.gif\" alt=\"이전으로\" />";
			$str .= "</a></span> ";
		}
		

		# 페이지

		$Cnt = 1;  # 숫자로 인식시킴 현재 페이지 볼드체 되게 수정	
		for ($Cnt = $SPage; $Cnt <= $EPage ; $Cnt++) {
			if ($Cnt == (int)($nPage)) {
				$str .= "<span><a href=\"".$URL."?nPage=".$Cnt."&".$Ext."\" class=\"selected\">" .$Cnt. "</a></span>\n";
			} else {
				$str .= "<span><a href=\"".$URL."?nPage=".$Cnt."&".$Ext."\" >" .$Cnt. "</a></span>\n";
			}
			$intTemp++;
		}
	
		# 다음 페이지
		if ($nPage >= $TPage) {
			$str .= "<span class=\"arr\"><a href=\"#\"><img src=\"/manager/images/admin/pag_next.gif\" alt=\"다음으로\" /></a></span>\n";
		} else {
			$str .= "<span class=\"arr\"><a href=".$URL."?nPage=" .($nPage + 1). "&".$Ext.">";
			$str .= "<img src=\"/manager/images/admin/pag_next.gif\" alt=\"다음으로\" />";
			$str .= "</a></span>\n";
		}
		
		# 다음 블록
		if ($intTemp > $TPage) {
			$str .= "<span class=\"arr\"><a href=\"#\"><img src=\"/manager/images/admin/pag_final.gif\" alt=\"다음".$PBlock."개\"></a></span>\n";
			$str .= "<span class=\"arr\"><a href=".$URL."?nPage=".$TPage."&".$Ext."><img src=\"/manager/images/admin/pag_first_bu.gif\" alt=\"맨 마지막\"></a></span>\n";
		} else {
			$str .= "<span class=\"arr\"><a href=".$URL."?nPage=" .$intTemp. "&".$Ext.">";
			$str .= "<img src=\"/manager/images/admin/pag_final.gif\" alt=\"다음".$PBlock."개\">";
			$str .= "</a></span>\n";
			$str .= "<span class=\"arr\"><a href=".$URL."?nPage=".$TPage."&".$Ext."><img src=\"/manager/images/admin/pag_first_bu.gif\" alt=\"맨 마지막\"></a></span>\n";
		}
		
		$str .= "</div>";
		
		
	}
	return $str;
}

// 페이지 표시


/*
		<div id="bbspgno">
			<ul class="bnk">
				<li class="bnk"><a href="#" onFocus="blur();"><img src="../images/bbs/prev02.gif" alt="처음" /></a></li>
				<li class="bnk"><a href="#" onFocus="blur();"><img src="../images/bbs/prev01.gif" alt="이전" /></a></li>
				<li class="bnk"><strong class="sel">1</strong></li>
				<li class="bnk"><a href="#" onFocus="blur();">2</a></li>
				<li class="bnk"><a href="#" onFocus="blur();">3</a></li>
				<li class="bnk"><a href="#" onFocus="blur();">4</a></li>
				<li class="bnk"><a href="#" onFocus="blur();">5</a></li>
				<li class="bnk"><a href="#" onFocus="blur();">6</a></li>
				<li class="bnk"><a href="#" onFocus="blur();">7</a></li>
				<li class="bnk"><a href="#" onFocus="blur();">8</a></li>
				<li class="bnk"><a href="#" onFocus="blur();">9</a></li>
				<li class="bnk"><a href="#" onFocus="blur();"><img src="../images/bbs/next01.gif" alt="다음" /></a></li>
				<li class="bnk"><a href="#"  onFocus="blur();"><img src="../images/bbs/next02.gif" alt="맨끝" /></a></li>
			</ul>
		</div>

		<div id="bbspgno">
			<ul class="bnk">
				<li class="bnk"><a href="#"><img src="../images/common/bbs/bu_prev02.gif" alt="맨앞" /></a></li>
				<li class="bnk"><a href="#"><img src="../images/common/bbs/bu_prev01.gif" alt="이전" /></a></li>
				<li class="bnk"><strong class="sel">1</strong></li>
				<li class="bnk"><a href="#">2</a></li>
				<li class="bnk"><a href="#">3</a></li>
				<li class="bnk"><a href="#">4</a></li>
				<li class="bnk"><a href="#">5</a></li>
				<li class="bnk"><a href="#">6</a></li>
				<li class="bnk"><a href="#">7</a></li>
				<li class="bnk"><a href="#">8</a></li>
				<li class="bnk"><a href="#">9</a></li>
				<li class="bnk"><a href="#"><img src="../images/common/bbs/bu_next01.gif" alt="다음" /></a></li>
				<li class="bnk"><a href="#"><img src="../images/common/bbs/bu_next02.gif" alt="맨뒤" /></a></li>
			</ul>
		</div>
*/


// 쇼핑몰 상품 리스트용 페이징
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
		
		# 이전 블록
		if ($intTemp == 1) {
			//$str .= "<li><a href=".$URL."?nPage=1&".$Ext."><img src=\"../images/bbs/prev2.gif\" alt=\"맨 처음\"></a></li>\n";
			$str .= "<li><a href=\"#\"><img src=\"../images/bbs/prev2.gif\" alt=\"이전".$PBlock."개\"></a></span>\n";
		} else {
			//$str .= "<li><a href=".$URL."?nPage=1&".$Ext."><img src=\"../images/bbs/prev2.gif\" alt=\"맨 처음\"></a></li>\n";
			$str .= "<li><a href=".$URL."?nPage=".($intTemp - $PBlock)."&".$Ext.">";
			$str .= "<img src=\"../images/bbs/prev2.gif\" alt=\"이전".$PBlock."개\">";
			$str .= "</a></li>\n";
		}
		
		# 이전 페이지
		if ($nPage == 1) {
			$str .= "<li><a href=\"#\"><img src=\"../images/bbs/prev.gif\" alt=\"이전으로\" /></a></li>\n";
		} else {
			$str .= "<li><a href=".$URL."?nPage=".($nPage - 1)."&".$Ext.">";
			$str .= "<img src=\"../images/bbs/prev.gif\" alt=\"이전으로\" />";
			$str .= "</a></li> ";
		}
		

		# 페이지

		$Cnt = 1;  # 숫자로 인식시킴 현재 페이지 볼드체 되게 수정	
		for ($Cnt = $SPage; $Cnt <= $EPage ; $Cnt++) {
			if ($Cnt == (int)($nPage)) {
				$str .= "<li><strong class='sel'>" .$Cnt. "</strong></li>\n";
			} else {
				$str .= "<li><a href=\"".$URL."?nPage=".$Cnt."&".$Ext."\" >" .$Cnt. "</a></li>\n";
			}
			$intTemp++;
		}
	
		# 다음 페이지
		if ($nPage >= $TPage) {
			$str .= "<li><a href=\"#\"><img src=\"../images/bbs/next.gif\" alt=\"다음으로\" /></a></li>\n";
		} else {
			$str .= "<li><a href=".$URL."?nPage=" .($nPage + 1). "&".$Ext.">";
			$str .= "<img src=\"../images/bbs/next.gif\" alt=\"다음으로\" />";
			$str .= "</a></li>\n";
		}
		
		# 다음 블록
		if ($intTemp > $TPage) {
			$str .= "<li><a href=\"#\"><img src=\"../images/bbs/next2.gif\" alt=\"다음".$PBlock."개\"></a></li>\n";
			//$str .= "<li><a href=".$URL."?nPage=".$TPage."&".$Ext."><img src=\"../images/admin/pag_first_bu.gif\" alt=\"맨 마지막\"></a></li>\n";
		} else {
			$str .= "<li><a href=".$URL."?nPage=" .$intTemp. "&".$Ext.">";
			$str .= "<img src=\"../images/bbs/next2.gif\" alt=\"다음".$PBlock."개\">";
			$str .= "</a></li>\n";
			//$str .= "<li><a href=".$URL."?nPage=".$TPage."&".$Ext."><img src=\"../images/admin/pag_first_bu.gif\" alt=\"맨 마지막\"></a></li>\n";
		}
		$str .= "</ul>";
		$str .= "</div>";
		
		
	}
	return $str;
}

//문자열 자르기
function TextCut($str,$start,$len,$suffix = "...") {
	$lenth=$len - $start;   
	
	if (strlen($str)>$lenth) {  //만일 자르게 된다면 표시 
		$ok=1;
	}
 
	$str = trim($str); 
	$backcnt= 0; // 시작첫글자에서 뒤로간 byte 수 (space나 영/숫자가 나올때 까지) 
	$cntcheck =0; 
	
	if ($start>0 ) { 
		if(ord($str[$start]) >= 128) { // 첫 시작글자가 한글이면 
			for ($i=$start;$i>0;$i--) { 
				if (ord($str[$i]) >= 128) { 
					$backcnt++; 
				} else { 
					break; 
				} 
			}
			
			$start= ($backcnt%2) ? $start : $start-1; //첫글짜가 깨지면, 시작점 = (시작 byte -1byte) 

			if (($backcnt%2)==1) { 
				$cntcheck = 0; //문장 시작 첫글자 안짤림 
			} else { 
				$cntcheck = 1; //문장 시작 첫글자 짤림 
			} 

		}
	}

	$backcnt2= 0; // 마지막글자에서 뒤로간 byte 수 (space나 영/숫자가 나올때 까지) 
	
	for ($i=($len-1);$i>=0;$i--) { 
		if (ord($str[$i+$start]) >= 128) { 
			$backcnt2++; 
		} else { 
			break; 
		} 
	} 

	if (($backcnt2%2)==1) { 
		$cntcheck2 = 1; //문장 마지막 글자 짤림 
	} else { 
		$cntcheck2 = 0; //문장 마지막 글자 안짤림 
	} 

	(int)$cnt=$len-abs($backcnt2%2); //자를 문자열 길이 (byte) 
	if(($cntcheck+$cntcheck2)==2) $cnt+=2; //$cntcheck가 짤리고, $cntcheck2가 짤리는 경우 
	$cutstr = substr($str,$start,$cnt); 
	if ($ok){$cutstr .= $suffix;}  ///잘랐을 경우에만 끝에 ... 붙임 
	return $cutstr; 
}


// DB에 입력 하기
function SetStringToDB($str) {
	
	$temp_str = "";
	
	$temp_str = trim($str);
	$temp_str = addslashes($temp_str);

	return $temp_str; 
}

// DB에서 빼오기
function SetStringFromDB($str) {
	
	$temp_str = "";
	
	$temp_str = trim($str);
	$temp_str = stripslashes($temp_str);
	//기본적으로 htmlentitle를 사용하고 엑셀화 할때는 뺴야 할듯 2017-05-04
	$temp_str = str_replace("\"","&quot;", $temp_str);

	return $temp_str; 
}

// 확장자 구하기 
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
    /* 3.22 막음 (HTML 체크 줄바꿈시 출력 오류때문)
    $source[] = "/  /";
    $target[] = " &nbsp;";
    */

    // 3.31
    // TEXT 출력일 경우 &amp; &nbsp; 등의 코드를 정상으로 출력해 주기 위함
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

// 세션변수 생성
function set_session($session_name, $value)
{
	//session_register($session_name);
	// PHP 버전별 차이를 없애기 위한 방법
	$$session_name = $_SESSION["$session_name"] = $value;
}


// 세션변수값 얻음
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


//EXCEL - NUMBERSTRING() 함수 구현
//참조 : http://www.tipssoft.com/bulletin/board.php?bo_table=update&wr_id=998
function NUMBERSTRING($parm_money)
{
	//콤마제거
	$parm_money = str_replace(",", "", $parm_money);

	// 문자 금액의 단위를 표현하는 문자들을 담은 테이블을 생성한다.
	$p_unit_table = array("천", "백", "십", "조", "천", "백", "십", "억", "천", "백", "십", "만", "천", "백", "십", "원");

	// 아라비안 숫자표기의 문자들을 담은 테이블을 생성한다.
	$p_number_table = array("일", "이", "삼", "사", "오", "육", "칠", "팔", "구");

	// 입력받은 숫자를 문자 금액으로 바꾸기 위한 변수를 선언한다.

	$temp_data = $parm_money;
	$result_data = "";

	// 변환된 문자열 temp_data의 길이를 구한다.
	$cnt = strlen($temp_data);

	// 문자열의 길이만큼 반복문을 수행한다.
	for($i = 0; $i < $cnt; $i++){

		// 문자가 '0' 이 아닐 경우
		if($temp_data[$i] != '0'){
			// result_data 변수의 뒤에 알맞은 숫자 문자를 추가한다.
			// 만약 temp_data[i] 에 8이 저장되어 있다면 
			// temp_data[i] 에 1을 뺀 7이 p_number_table의 인덱스가 되어
			// p_number_table[7] 의 값인 "팔"이 result_data에 추가 된다.
			// p_number_table은 인덱스 - 1 에 숫자에 해당하는 문자가 저장되어 있다.
			$result_data .= $p_number_table[$temp_data[$i] - '0' - 1];
			
			// result_data 변수의 뒤에 알맞은 금액 단위 문자를 추가한다.
			// 만약 입력받은 값이 206000 이라면, count = 6
			// 2 : p_unit_table[16 - 6 + 0]  => 십
			// 0 : 밑에 else 문이 수행된다.
			// 6 : p_unit_table[16 - 6 + 2]  => 천
			// 000 : 밑에 else 문이 수행된다.
			$result_data .= $p_unit_table[16 - $cnt + $i];

		// 문자가 '0' 일 경우
		} else {
			// 만약 입력받은 값이 206000 이라면, count = 6
			// 2 : 위의 if문에서 수행되었기때문에 수행되지 않음.   => 십
			// 0 : (16 - 6 + 1 + 1) % 4 가 0이므로 
			//     result_data 변수의 뒤에 알맞은 숫자 문자를 추가한다.
			//     p_unit_table[16 - 6 + 1]                        => 만
			// 6 : 위의 if문에서 수행되었기때문에 수행되지 않음.   => 천
			// 0 : (16 - 6 + 3 + 1) % 4 가 1이므로 다음 문장을 수행하지 않는다.
			// 0 : (16 - 6 + 4 + 1) % 4 가 1이므로 다음 문장을 수행하지 않는다.
			// 0 : (16 - 6 + 5 + 1) % 4 가 0이므로
			//     result_data 변수의 뒤에 알맞은 숫자 문자를 추가한다.
			//     p_unit_table[16 - 6 + 5]                        => 원
			if(!((16 - $cnt + $i + 1) % 4)) 
				$result_data .= $p_unit_table[16 - $cnt + $i];
		}
	}
	return $result_data;
}

//다중 배열검색 - DB 호출값에서 정보 찾을때
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
  case 'A' :                                  // 부가세없음
   $supply = $amount;                         // 공급가
   $tax = 0;                                 // 부가세
  break;
  case 'B' :                                  // 부가세별도
   $supply = $amount;                         // 공급가
   $tax = $supply * 0.1;                        // 부가세
  break;
  case 'C' :                                  // 부가세포함
   $supply = $amount / 1.1;
   $tax = $amount - $supply;
  break;
  case 'G' :                                  // 부가세포함 - 기프트넷
   $supply = round($amount / 1.1);
   $tax = $amount - $supply;
  break;
 }
 $supply = round($supply);                      // 마지막 합계금액에서 반올림 해 줌
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
