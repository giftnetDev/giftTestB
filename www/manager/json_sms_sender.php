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
	/******************** �������� ********************/
	$sms_url = "http://sslsms.cafe24.com/sms_sender.php"; // ���ۿ�û URL
	// $sms_url = "https://sslsms.cafe24.com/sms_sender.php"; // HTTPS ���ۿ�û URL
	$sms['user_id'] = base64_encode("giftnetsms"); //SMS ���̵�.
	$sms['secure'] = base64_encode("eb6791964ffa7465263e9b35cc71b691") ;//����Ű
	$sms['msg'] = base64_encode(stripslashes($msg));
	$sms['subject'] = base64_encode($title); //����
	$sms['rphone'] = base64_encode($rphone); //�����ڹ�ȣ
	$sms['sphone1'] = base64_encode($recver1);  // $_POST['sphone1']);
	$sms['sphone2'] = base64_encode($recver2); // $_POST['sphone2']);
	$sms['sphone3'] = base64_encode($recver3; // $_POST['sphone3']);
	$sms['rdate'] = base64_encode($rdate);	//��) 20080930
	$sms['rtime'] = base64_encode($rtime);	//��) 173000
	$sms['mode'] = base64_encode("1"); // base64 ���� �ݵ�� ��尪�� 1�� �ּž� �մϴ�.
	$sms['returnurl'] = base64_encode($returnurl);//	�޽��� ���� �� �̵��� ������	( http:// �� ���̼ž� �մϴ�. )
	$sms['testflag'] = base64_encode($testflag);//�׽�Ʈ�� ��� : Y	�׽�Ʈ�� �ƴ� ��� �Է����� ������.	���� sms�� ������ ������ �ܼ��� ������ �׽�Ʈ�� ���� �뵵
	$sms['destination'] = base64_encode($destination);//�޽����� �޴� ��� �̸��� �ְ� ���� �� �̿�destination ���� "�޴�����ȣ|�̸�" �� ���� '|'���ڷ� �����ؼ� �Է��Ͻð�,	msg���� ��{name}�� �̶�� ������ �Է� �� �����Ͻø� �˴ϴ�.
	$returnurl = "";	//�޽��� ���� �� �̵��� ������	( http:// �� ���̼ž� �մϴ�. )
	$sms['repeatFlag'] = base64_encode($repeatFlag);	//�ݺ� ������ ���ϴ� ��� : Y	�ݺ� ������ ������ �ʴ� ��� �Է����� ������.
	$sms['repeatNum'] = base64_encode($repeatNum);		//1~10ȸ ����.
	$sms['repeatTime'] = base64_encode($repeatTime);	//15�� �̻���� ����.
	$sms['smsType'] = base64_encode($smsType); // LMS�ϰ�� L

	$nointeractive = ""; //����� ��� : 1, ������ ��ȭ����(alert)�� ����

	$host_info = explode("/", $sms_url);
	$host = $host_info[2];
	$path = $host_info[3];

	srand((double)microtime()*1000000);
	$boundary = "---------------------".substr(md5(rand(0,32000)),0,10);
	
	//print_r($sms);
	//echo $returnurl;

	// ��� ����
	$header = "POST /".$path ." HTTP/1.0\r\n";
	$header .= "Host: ".$host."\r\n";
	$header .= "Content-type: multipart/form-data, boundary=".$boundary."\r\n";

	// ���� ����
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
		$Result= $rMsg[0]; //�߼۰��
		$Count= $rMsg[1]; //�ܿ��Ǽ�
		
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

