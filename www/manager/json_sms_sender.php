<?session_start();?>
<?

#====================================================================
# DB Include, DB Connection
#====================================================================
require "../../_classes/com/db/DBUtil.php";

$conn = db_connection("w");

#=====================================================================
# common function, login_function
#=====================================================================
require "../../_common/config.php";

function insertSMS($db, $sms_code, $phone1, $phone2, $phone3, $smsType, $title, $msg, $recver1, $recver2, $recver3, $Result, $Count, $reg_adm)
{
	$query 	= " SELECT IFNULL(MAX(SMS_NO),0) AS SMS_NO FROM T_SMS_SENDER WHERE SMS_TYPE = 'HP02' ";
	$result = mysql_query($query,$db);
	$rows   = mysql_fetch_array($result);

	if ($rows[0] <> 0) 
	{
		$SMS_NO = $rows[0] + 1;
	}
	else
	{
		$SMS_NO = 1;
	}

	$qry=	"
				INSERT INTO T_SMS_SENDER(
										  SMS_NO
										, SMS_CODE
										, SENDER_NO1
										, SENDER_NO2
										, SENDER_NO3
										, SMS_TYPE
										, TITLE
										, MESSAGE
										, RECVER_NO1
										, RECVER_NO2
										, RECVER_NO3
										, SMS_RESULT
										, SMS_REMAINING
										, REG_ADM
										, REG_DATE
										)
								values (
										  '$SMS_NO'
										, '$sms_code'
										, '$phone1'
										, '$phone2'
										, '$phone3'
										, '$smsType'
										, '$title'
										, '$msg'
										, '$recver1'
										, '$recver2'
										, '$recver3'
										, '$Result'
										, '$Count'
										, '$reg_adm'
										, now()
										)
			";
	
	$result=mysql_query($qry,$db);
	echo $query;
	// exit;
	if($result<>"") return 1;

	else return 0;	
}

if($mode=="HOMEPAGE_IDPW_FIND")
{
	/******************** 인증정보 ********************/
	$sms_url = "http://sslsms.cafe24.com/sms_sender.php"; // 전송요청 URL
	// $sms_url = "https://sslsms.cafe24.com/sms_sender.php"; // HTTPS 전송요청 URL
	$sms['user_id'] = base64_encode("giftnetsms"); //SMS 아이디.
	$sms['secure'] = base64_encode("eb6791964ffa7465263e9b35cc71b691") ;//인증키
	$sms['msg'] = base64_encode(stripslashes($msg));
	$sms['subject'] = base64_encode($title); //제목
	$sms['rphone'] = base64_encode($rphone); //수신자번호
	$sms['sphone1'] = base64_encode($recver1);  // $_POST['sphone1']);
	$sms['sphone2'] = base64_encode($recver2); // $_POST['sphone2']);
	$sms['sphone3'] = base64_encode($recver3; // $_POST['sphone3']);
	$sms['rdate'] = base64_encode($rdate);	//예) 20080930
	$sms['rtime'] = base64_encode($rtime);	//예) 173000
	$sms['mode'] = base64_encode("1"); // base64 사용시 반드시 모드값을 1로 주셔야 합니다.
	$sms['returnurl'] = base64_encode($returnurl);//	메시지 전송 후 이동할 페이지	( http:// 를 붙이셔야 합니다. )
	$sms['testflag'] = base64_encode($testflag);//테스트일 경우 : Y	테스트가 아닐 경우 입력하지 마세요.	실제 sms는 보내지 않으나 단순한 페이지 테스트를 위한 용도
	$sms['destination'] = base64_encode($destination);//메시지에 받는 사람 이름을 넣고 싶을 때 이용destination 값을 "휴대폰번호|이름" 과 같이 '|'문자로 구분해서 입력하시고,	msg값에 “{name}” 이라는 문구를 입력 후 전송하시면 됩니다.
	$returnurl = "";	//메시지 전송 후 이동할 페이지	( http:// 를 붙이셔야 합니다. )
	$sms['repeatFlag'] = base64_encode($repeatFlag);	//반복 설정을 원하는 경우 : Y	반복 설정을 원하지 않는 경우 입력하지 마세요.
	$sms['repeatNum'] = base64_encode($repeatNum);		//1~10회 가능.
	$sms['repeatTime'] = base64_encode($repeatTime);	//15분 이상부터 가능.
	$sms['smsType'] = base64_encode($smsType); // LMS일경우 L

	$nointeractive = ""; //사용할 경우 : 1, 성공시 대화상자(alert)를 생략

	$host_info = explode("/", $sms_url);
	$host = $host_info[2];
	$path = $host_info[3];

	srand((double)microtime()*1000000);
	$boundary = "---------------------".substr(md5(rand(0,32000)),0,10);
	
	//print_r($sms);
	//echo $returnurl;

	// 헤더 생성
	$header = "POST /".$path ." HTTP/1.0\r\n";
	$header .= "Host: ".$host."\r\n";
	$header .= "Content-type: multipart/form-data, boundary=".$boundary."\r\n";

	// 본문 생성
	foreach($sms AS $index => $value){
		$data .="--$boundary\r\n";
		$data .= "Content-Disposition: form-data; name=\"".$index."\"\r\n";
		$data .= "\r\n".$value."\r\n";
		$data .="--$boundary\r\n";
	}
	$header .= "Content-length: " . strlen($data) . "\r\n\r\n";

	$fp = fsockopen($host, 80);

	if ($fp) {
		fputs($fp, $header.$data);
		$rsp = '';
		while(!feof($fp)) {
			$rsp .= fgets($fp,8192);
		}
		fclose($fp);
		$msg = explode("\r\n\r\n",trim($rsp));
		$rMsg = explode(",", $msg[1]);
		$Result= $rMsg[0]; //발송결과
		$Count= $rMsg[1]; //잔여건수
		
		if(insertSMS($conn, $sms_code, $phone1, $phone2, $phone3, $smsType, $title, $msg, $recver1, $recver2, $recver3, $Result, $Count, $reg_adm) == 1 )
		{
			//if($Result=="success" || $Result=="reserved")
			if($Result =="success")
			{
				$result = "Y";

				echo "[{\"RESULT\":\"".$result."\",\"RETURN_VLAUE\":\"".$Result."\"}]";
			}
			else
			{
				$result = "N";
				echo "[{\"RESULT\":\"".$result."\",\"RETURN_VLAUE\":\"".$Result."\"}]";
			}
		}
		else 
		{
			echo 0;
		}
}

?>

