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

/******************** �������� ********************/
$sms_url = "http://sslsms.cafe24.com/sms_sender.php"; // ���ۿ�û URL
// $sms_url = "https://sslsms.cafe24.com/sms_sender.php"; // HTTPS ���ۿ�û URL
$sms['user_id'] = base64_encode("giftnetsms"); //SMS ���̵�.
$sms['secure'] = base64_encode("eb6791964ffa7465263e9b35cc71b691") ;//����Ű
$sms['msg'] = base64_encode(stripslashes($msg));
$sms['subject'] = base64_encode($title); //���� (smsŸ���� L�ϰ�쿡�� ������ ǥ�õ�. S�� ��� ǥ�þȵ�.)
$sms['rphone'] = base64_encode($rphone); //�����ڹ�ȣ
$sms['sphone1'] = base64_encode($recver1);  // $_POST['sphone1']);
$sms['sphone2'] = base64_encode($recver2); // $_POST['sphone2']);
$sms['sphone3'] = base64_encode($recver3); // $_POST['sphone3']);
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

// echo 1;
// exit;

// print_r($sms);
// echo $returnurl;

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
}

function SelectMemberCk($db, $phone1, $phone2, $phone3, $custNm)
{
	$query 	= " SELECT MEM_NO
					 , MEM_ID
					 , MEM_NM
					 , HPHONE
					 , REPLACE( HPHONE, '-', '' ) AS ENTERED_HP
					 , LPAD( CAST( RAND( ) *1000000 AS SIGNED ) +1, 6, '0' ) AS CERTIFICATION_NO
				  FROM TBL_MEMBER
				 WHERE MEM_NM = '$custNm'
				   AND REPLACE( HPHONE, '-', '' ) = CONCAT('$phone1', '$phone2', '$phone3')
		";

	$result = mysql_query($query,$db);
	$record = array();
	//echo $query;
	if ($result <> "") {
		for($i=0;$i < mysql_num_rows($result);$i++) {
			$record[$i] = sql_result_array($result,$i);
		}
	}
	return $record;
}


function insertSMS($db, $sms_code, $phone1, $phone2, $phone3, $smsType, $custNm, $title, $msg, $recver1, $recver2, $recver3, $Result, $Count, $CER_NO)
{
	$query 	= " SELECT IFNULL(MAX(SMS_NO),0) AS SMS_NO FROM T_SMS_SENDER";
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
										, MEM_NM
										, TITLE
										, MESSAGE
										, RECVER_NO1
										, RECVER_NO2
										, RECVER_NO3
										, CERTIFICATION
										, SMS_RESULT
										, SMS_REMAINING
										, REG_DATE
										)
								values (
										  '$SMS_NO'
										, '$sms_code'
										, '$phone1'
										, '$phone2'
										, '$phone3'
										, '$smsType'
										, '$custNm'
										, '$title'
										, '$msg'
										, '$recver1'
										, '$recver2'
										, '$recver3'
										, '$CER_NO'
										, '$Result'
										, '$Count'
										, now()
										)
			";
	
	$result=mysql_query($qry,$db);
	//echo $query;
	//echo $qry;
	// exit;
	if($result<>"") return 1;

	else return 0;	
}

function SelectSmsCerCk($db, $phone1, $phone2, $phone3, $custNm, $sms_code)
{
	$query 	= "		SELECT A.SMS_NO
						 , A.CERTIFICATION
						 , B.MEM_NO
						 , B.MEM_ID
					  FROM T_SMS_SENDER A, TBL_MEMBER B
					 WHERE A.MEM_NM = B.MEM_NM
					   AND CONCAT( A.SENDER_NO1, A.SENDER_NO2, A.SENDER_NO3 ) = REPLACE( B.HPHONE, '-', '' )
					   AND A.MEM_NM = '$custNm'
					   AND A.SENDER_NO1 = '$phone1'
					   AND A.SENDER_NO2 = '$phone2'
					   AND A.SENDER_NO3 = '$phone3'
					   AND A.SMS_CODE = '$sms_code'
					 ORDER BY A.SMS_NO DESC
					 LIMIT 1
		";

	$result = mysql_query($query,$db);
	$record = array();
	//echo $query;
	if ($result <> "") {
		for($i=0;$i < mysql_num_rows($result);$i++) {
			$record[$i] = sql_result_array($result,$i);
		}
	}
	return $record;
}

function Update_PASSWORD($db, $memNo,$custId,$custNpw)
{
	$query="UPDATE TBL_MEMBER 
			   SET MEM_PW = MD5('$custNpw')
				 , UP_DATE = NOW() 
				 , UP_ADM = '$custId'
			WHERE MEM_NO = '$memNo'
			  AND MEM_ID = '$custId'
			";

	$result=mysql_query($query,$db);
	//echo $query;
	// exit;
	if($result<>"") return 1;

	else return 0;	
}

function SelectCertification($db)
{
	$query 	= " SELECT LPAD( CAST( RAND( ) *1000000 AS SIGNED ) +1, 6, '0' ) AS CERTIFICATION_NO
		";

	$result = mysql_query($query,$db);
	$record = array();
	//echo $query;
	if ($result <> "") {
		for($i=0;$i < mysql_num_rows($result);$i++) {
			$record[$i] = sql_result_array($result,$i);
		}
	}
	return $record;
}

function SelSmsCerCk($db, $phone1, $phone2, $phone3, $custNm, $sms_code)
{
	$query 	= "		SELECT A.SMS_NO
						 , A.CERTIFICATION
						 , A.MEM_NM
					  FROM T_SMS_SENDER A
					 WHERE A.MEM_NM = '$custNm'
					   AND A.SENDER_NO1 = '$phone1'
					   AND A.SENDER_NO2 = '$phone2'
					   AND A.SENDER_NO3 = '$phone3'
					   AND A.SMS_CODE = '$sms_code'
					 ORDER BY A.SMS_NO DESC
					 LIMIT 1
		";

	$result = mysql_query($query,$db);
	$record = array();
	//echo $query;
	if ($result <> "") {
		for($i=0;$i < mysql_num_rows($result);$i++) {
			$record[$i] = sql_result_array($result,$i);
		}
	}
	return $record;
}

//-----------------------------------------------------------------------------------------------------------------------------------//

if($mode=="HOMEPAGE_CERTIFICATION")
{
	$NcustNm 	= iconv("utf8","euckr",$custNm);

	$arr = SelectMemberCk($conn, $phone1, $phone2, $phone3, $NcustNm);

	if(sizeof($arr) > 0) 
	{
		$CER_NO = $arr[0]["CERTIFICATION_NO"];

		$result = "Y";
		echo "[{\"RESULT\":\"".$result."\",\"CER_NO\":\"".$CER_NO."\"}]";
	}
	else
	{
		$result = "N";
		echo "[{\"RESULT\":\"".$result."\"}]";
	}
}

if($mode=="HOMEPAGE_SMS_INSERT")
{
	$NcustNm 	= iconv("utf8","euckr",$custNm);
	$Ntitle 	= iconv("utf8","euckr",$title);

	$Nmessage 	= iconv("utf8","euckr",$message);
			
	if(insertSMS($conn, $sms_code, $phone1, $phone2, $phone3, $smsType, $NcustNm, $Ntitle, $Nmessage, $recver1, $recver2, $recver3, $Result, $Count, $cer_no) == 1 )
	{
		if($Result=="success" || $Result=="reserved" || $Result=="Test Success!")
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
		$result = "N";
		$Result = "error2";
		echo "[{\"RESULT\":\"".$result."\",\"RETURN_VLAUE\":\"".$Result."\"}]";
	}	
	
}

if($mode=="HOMEPAGE_IDPW_CERTIFICATION")
{
	$NcustNm 	= iconv("utf8","euckr",$custNm);

	$arr = SelectSmsCerCk($conn, $phone1, $phone2, $phone3, $NcustNm, $sms_code);

	$CERTIFICATION = $arr[0]["CERTIFICATION"];

	if($cerNo == $CERTIFICATION)
	{
		$MEM_NO = $arr[0]["MEM_NO"];
		$MEM_ID = $arr[0]["MEM_ID"];

		$result = "Y";
		echo "[{\"RESULT\":\"".$result."\",\"MEM_NO\":\"".$MEM_NO."\",\"MEM_ID\":\"".$MEM_ID."\"}]";
	}
	else
	{
		$result = "N";
		echo "[{\"RESULT\":\"".$result."\"}]";
	}
}

if($mode=="HOMEPAGE_IDPW_UPDATE")
{
	if(Update_PASSWORD($conn,$memNo,$custId,$custNpw) == 1 )
	{
		$result = "Y";
		echo "[{\"RESULT\":\"".$result."\"}]";
	}
	else 
	{
		$result = "N";
		echo "[{\"RESULT\":\"".$result."\"}]";
	}
}

if($mode=="HOMEPAGE_CERTIFICATION_SEL")
{
	$arr = SelectCertification($conn);

	if(sizeof($arr) > 0) 
	{
		$CER_NO = $arr[0]["CERTIFICATION_NO"];

		$result = "Y";
		echo "[{\"RESULT\":\"".$result."\",\"CER_NO\":\"".$CER_NO."\"}]";
	}
	else
	{
		$result = "N";
		echo "[{\"RESULT\":\"".$result."\"}]";
	}
}

if($mode=="HOMEPAGE_CERTIFICATION_CONFIRM")
{
	$NcustNm 	= iconv("utf8","euckr",$custNm);

	$arr = SelSmsCerCk($conn, $phone1, $phone2, $phone3, $NcustNm, $sms_code);

	$CERTIFICATION = $arr[0]["CERTIFICATION"];

	if($cerNo == $CERTIFICATION)
	{
		$result = "Y";
		echo "[{\"RESULT\":\"".$result."\"}]";
	}
	else
	{
		$result = "N";
		echo "[{\"RESULT\":\"".$result."\"}]";
	}
}

?>

