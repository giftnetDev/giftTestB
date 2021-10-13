<?session_start();?>
<?
#====================================================================
# DB Include, DB Connection
#====================================================================
require "_classes/com/db/DBUtil.php";

$conn = db_connection("w");

#=====================================================================
# common function, login_function
#=====================================================================
require "_common/config.php";

function SelectMemberIDck($db, $custID)
{
	$query 	= " SELECT MEM_NO
					 , MEM_ID
					 , MEM_NM
				  FROM TBL_MEMBER
				 WHERE MEM_ID = '$custID'
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


function insertHpMemeber($db, $custTp, $custID, $custPwd, $custNm, $custEmail, $custTelPhone, $custPhone, $custZipcode, $custAddr, $bizNo1, $bizNo2, $bizNo3, $companyNm, $ceoNm, $cpNo)
{
	$query ="SELECT IFNULL(MAX(MEM_NO),0) + 1 AS MAX_NO FROM TBL_MEMBER ";
	$result = mysql_query($query,$db);
	$rows   = mysql_fetch_array($result);
	
	$new_mem_no = $rows[0];

	$qry=	"
				INSERT INTO TBL_MEMBER(
										  MEM_NO
										, MEM_TYPE
										, MEM_ID
										, MEM_PW
										, MEM_NM
										, CP_NM
										, CEO_NM
										, BIZ_NUM1
										, BIZ_NUM2
										, BIZ_NUM3
										, EMAIL
										, ZIPCODE
										, ADDR1
										, PHONE
										, HPHONE
										, CP_NO
										, REG_ADM
										, REG_DATE
										)
								values (
										  '$new_mem_no'
										, '$custTp'
										, '$custID'
										, MD5('$custPwd')
										, '$custNm'
										, '$companyNm'
										, '$ceoNm'
										, '$bizNo1'
										, '$bizNo2'
										, '$bizNo3'
										, '$custEmail'
										, '$custZipcode'
										, '$custAddr'
										, '$custTelPhone'
										, '$custPhone'
										, '$cpNo'
										, '$custID'
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

function SelDcodeExt($db, $pcode, $em_title, $em_body) 
{
	$query = "SELECT DCODE_EXT
				FROM TBL_CODE_DETAIL
			   WHERE PCODE = '$pcode'
				 AND DCODE IN('$em_title', '$em_body')
				 ";	
	//echo $query;	
	$result = mysql_query($query,$db);
	$record = array();

	if ($result <> "") {
		for($i=0;$i < mysql_num_rows($result);$i++) {
			$record[$i] = sql_result_array($result,$i);
		}
	}
	return $record;
}

function mailer_hp($from, $from_email, $to, $to_email, $subject, $content, $path, $filename) {
	// error_reporting(E_ALL ^ E_WARNING); 

	$content = nl2br($content);

	$error_msg = "";

	$mail = new PHPMailer(true);
	//$mail->IsSendmail();

	try {
		$mail->CharSet    = "utf-8";

		$mail->AddAddress($to_email, $to);

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

function SelectMyinfo($db, $mem_no, $mem_type)
{
	$query 	=" SELECT MEM_NO
					, MEM_TYPE
					, MEM_ID
					, MEM_NM
					, CP_NM
					, CEO_NM
					, BIZ_NUM1
					, BIZ_NUM2
					, BIZ_NUM3
					, EMAIL
					, SUBSTRING_INDEX(SUBSTRING_INDEX(EMAIL, '@', 1), '@', -1) EMAIL1
					, SUBSTRING_INDEX(SUBSTRING_INDEX(EMAIL, '@', 2), '@', -1) EMAIL2
					, ZIPCODE
					, ADDR1
					, PHONE
					, SUBSTRING_INDEX(SUBSTRING_INDEX(PHONE, '-', 1), '-', -1) PHONE1
					, SUBSTRING_INDEX(SUBSTRING_INDEX(PHONE, '-', 2), '-', -1) PHONE2
					, SUBSTRING_INDEX(SUBSTRING_INDEX(PHONE, '-', 3), '-', -1) PHONE3
					, HPHONE
					, SUBSTRING_INDEX(SUBSTRING_INDEX(HPHONE, '-', 1), '-', -1) HPHONE1
					, SUBSTRING_INDEX(SUBSTRING_INDEX(HPHONE, '-', 2), '-', -1) HPHONE2
					, SUBSTRING_INDEX(SUBSTRING_INDEX(HPHONE, '-', 3), '-', -1) HPHONE3
					, CP_NO
				 FROM TBL_MEMBER
				WHERE MEM_NO = '$mem_no'
				  AND MEM_TYPE = '$mem_type'
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

function Update_CustomerInfo($db, $custNo, $custTp, $custID, $custPwd, $custNm, $custEmail, $custTelPhone, $custPhone, $custZipcode, $custAddr, $bizNo1, $bizNo2, $bizNo3, $companyNm, $ceoNm, $cpNo)
{
	$query="UPDATE TBL_MEMBER 
			   SET MEM_PW 	= MD5('$custPwd')
				 , MEM_NM	= '$custNm'
				 , CP_NM	= '$companyNm'
				 , CEO_NM	= '$ceoNm'
				 , BIZ_NUM1	= '$bizNo1'
				 , BIZ_NUM2	= '$bizNo2'
				 , BIZ_NUM3	= '$bizNo3'
				 , EMAIL	= '$custEmail'
				 , ZIPCODE	= '$custZipcode'
				 , ADDR1	= '$custAddr'
				 , PHONE	= '$custTelPhone'
				 , HPHONE	= '$custPhone'
				 , CP_NO	= '$cpNo'
				 , UP_DATE  = NOW() 
				 , UP_ADM 	= '$custID'
			WHERE MEM_NO 	= '$custNo'
			  AND MEM_ID 	= '$custID'
			  AND MEM_TYPE 	= '$custTp'
			";

	$result=mysql_query($query,$db);
	//echo $query;
	// exit;
	if($result<>"") return 1;

	else return 0;	
}

function SelecBizNo($db, $bizNo1, $bizNo2, $bizNo3)
{
	$query 	=" SELECT CP_NO
				 FROM TBL_COMPANY
				WHERE BIZ_NO =  CONCAT( '$bizNo1', '-', '$bizNo2', '-', '$bizNo3' )
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

function SelectMemberPwck($db, $custNo, $custID, $custPwd)
{
	$query 	= " SELECT MEM_NO
					 , MEM_ID
					 , MEM_NM
				  FROM TBL_MEMBER
				 WHERE MEM_NO = '$custNo'
				   AND MEM_ID = '$custID'
				   AND MEM_PW = MD5( '$custPwd' )

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

function Update_Member_UseYn($db, $custNo, $custID)
{
	$query="UPDATE TBL_MEMBER 
			   SET DEL_TF 	= 'Y'
				 , DEL_DATE = NOW() 
				 , DEL_ADM 	= '$custID'
			WHERE MEM_NO 	= '$custNo'
			  AND MEM_ID 	= '$custID'
			";

	$result=mysql_query($query,$db);
	//echo $query;
	// exit;
	if($result<>"") return 1;

	else return 0;	
}

//-----------------------------------------------------------------------------------------------------------------------------------//

if($mode=="HOMEPAGE_CUSTOMER_CK")
{
	$arr = SelectMemberIDck($conn, $custID);

	if(sizeof($arr) > 0) 
	{
		$result = "N";
		echo "[{\"RESULT\":\"".$result."\"}]";
	}
	else
	{
		$result = "Y";
		echo "[{\"RESULT\":\"".$result."\"}]";
	}
}

if($mode=="HOMEPAGE_CUSTOMER_IN")
{			
	$NcustNm 		= iconv("utf8","euckr",$custNm);
	$NcustAddr 		= iconv("utf8","euckr",$custAddr);
	$NcompanyNm 	= iconv("utf8","euckr",$companyNm);
	$NceoNm 		= iconv("utf8","euckr",$ceoNm);
	$NgiftNm 		= iconv("euckr","utf8","기프트넷");

	if($bizNo1 != "" && $bizNo2 != "" && $bizNo3 != "")
	{
		$arr = SelecBizNo($conn, $bizNo1, $bizNo2, $bizNo3);

		if(sizeof($arr) > 0) 
		{
			$cpNo 	= $arr[0]["CP_NO"];
		}
	}

	if(insertHpMemeber($conn, $custTp, $custID, $custPwd, $NcustNm, $custEmail, $custTelPhone, $custPhone, $custZipcode, $NcustAddr, $bizNo1, $bizNo2, $bizNo3, $NcompanyNm, $NceoNm, $cpNo) == 1 )
	{
		$from_email = "gift@giftnet.co.kr";	//gift@giftnet.co.kr 기프트넷이메일
		//$to_email = $custEmail		//고객이메일
		$email_info = SelDcodeExt($conn, "HOME_INFO", "HP_WELCOM_EMAIL", "HP_WELCOM_EMAIL_BODY");

		$email_title 		= iconv("euckr","utf8",$email_info[0]["DCODE_EXT"]);
		$email_body 		= iconv("euckr","utf8",$email_info[1]["DCODE_EXT"]);

		$email_body = str_replace("[MEM_ID]", iconv("euckr","utf8",$NcustNm), $email_body);

		$email_body = str_replace("[BR]", "<br>", $email_body);

		include('_PHPMailer/class.phpmailer.php');

		$result_msg = mailer_hp($NgiftNm, $from_email, $custEmail, $custEmail, $email_title, $email_body, '', '');
		
		$result = "Y";

		echo "[{\"RESULT\":\"".$result."\"}]";
	}
	else 
	{
		$result = "N";
		echo "[{\"RESULT\":\"".$result."\"}]";
	}
}

if($mode=="HOMEPAGE_MYINFO_SEL")
{
	$arr = SelectMyinfo($conn, $mem_no, $mem_type);

	if(sizeof($arr) > 0) 
	{
		$MEM_ID 	= $arr[0]["MEM_ID"];
		$MEM_NM 	= iconv("euckr","utf8", $arr[0]["MEM_NM"]);
		$CP_NM 		= iconv("euckr","utf8", $arr[0]["CP_NM"]);
		$CEO_NM 	= iconv("euckr","utf8", $arr[0]["CEO_NM"]);
		$BIZ_NUM1 	= $arr[0]["BIZ_NUM1"];
		$BIZ_NUM2 	= $arr[0]["BIZ_NUM2"];
		$BIZ_NUM3 	= $arr[0]["BIZ_NUM3"];
		$EMAIL1 	= $arr[0]["EMAIL1"];
		$EMAIL2 	= $arr[0]["EMAIL2"];
		$ZIPCODE 	= $arr[0]["ZIPCODE"];
		$ADDR1 		= iconv("euckr","utf8", $arr[0]["ADDR1"]);
		$PHONE1 	= $arr[0]["PHONE1"];
		$PHONE2 	= $arr[0]["PHONE2"];
		$PHONE3 	= $arr[0]["PHONE3"];
		$HPHONE1 	= $arr[0]["HPHONE1"];
		$HPHONE2 	= $arr[0]["HPHONE2"];
		$HPHONE3 	= $arr[0]["HPHONE3"];
		$CP_NO 		= $arr[0]["CP_NO"];

		$result = "Y";
		echo "[{
			 \"RESULT\":\"".$result."\"
			,\"MEM_ID\":\"".$MEM_ID."\"
			,\"MEM_NM\":\"".$MEM_NM."\"
			,\"CP_NM\":\"".$CP_NM."\"
			,\"CEO_NM\":\"".$CEO_NM."\"
			,\"BIZ_NUM1\":\"".$BIZ_NUM1."\"
			,\"BIZ_NUM2\":\"".$BIZ_NUM2."\"
			,\"BIZ_NUM3\":\"".$BIZ_NUM3."\"
			,\"EMAIL1\":\"".$EMAIL1."\"
			,\"EMAIL2\":\"".$EMAIL2."\"
			,\"ZIPCODE\":\"".$ZIPCODE."\"
			,\"ADDR1\":\"".$ADDR1."\"
			,\"PHONE1\":\"".$PHONE1."\"
			,\"PHONE2\":\"".$PHONE2."\"
			,\"PHONE3\":\"".$PHONE3."\"
			,\"HPHONE1\":\"".$HPHONE1."\"
			,\"HPHONE2\":\"".$HPHONE2."\"
			,\"HPHONE3\":\"".$HPHONE3."\"
			,\"CP_NO\":\"".$CP_NO."\"
			}]";
	}
	else
	{
		$result = "N";
		echo "[{\"RESULT\":\"".$result."\"}]";
	}
}

if($mode=="HOMEPAGE_CUSTOMER_UP")
{
	$NcustNm 		= iconv("utf8","euckr",$custNm);
	$NcustAddr 		= iconv("utf8","euckr",$custAddr);
	$NcompanyNm 	= iconv("utf8","euckr",$companyNm);
	$NceoNm 		= iconv("utf8","euckr",$ceoNm);

	if(Update_CustomerInfo($conn, $custNo, $custTp, $custID, $custPwd, $NcustNm, $custEmail, $custTelPhone, $custPhone, $custZipcode, $NcustAddr, $bizNo1, $bizNo2, $bizNo3, $NcompanyNm, $NceoNm, $cpNo) == 1 )
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

if($mode=="HOMEPAGE_MEMBER_PWCK")
{
	$arr = SelectMemberPwck($conn, $custNo, $custID, $custPwd);

	if(sizeof($arr) > 0) 
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

if($mode=="HOMEPAGE_MEMBER_DEL")
{
	if(Update_Member_UseYn($conn, $custNo, $custID) == 1 )
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