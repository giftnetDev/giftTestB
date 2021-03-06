<?session_start();?>
<?

   /******************** 인증정보 ********************/
	$sms_url = "http://sslsms.cafe24.com/sms_sender.php"; // 전송요청 URL
	// $sms_url = "https://sslsms.cafe24.com/sms_sender.php"; // HTTPS 전송요청 URL
	$sms['user_id'] = base64_encode("giftnetsms"); //SMS 아이디.
	$sms['secure'] = base64_encode("eb6791964ffa7465263e9b35cc71b691") ;//인증키
	$sms['msg'] = base64_encode(stripslashes($_POST['msg']));
	if($_POST['smsType'] == 'L') {
		$sms['subject'] = base64_encode($_POST['subject']); //제목
	}

	$sms['rphone'] = base64_encode($_POST['rphone']);
	$sms['sphone1'] = base64_encode("070");  // $_POST['sphone1']);
	$sms['sphone2'] = base64_encode("8896"); // $_POST['sphone2']);
	$sms['sphone3'] = base64_encode("0627"); // $_POST['sphone3']);
	$sms['rdate'] = base64_encode($_POST['rdate']);
	$sms['rtime'] = base64_encode($_POST['rtime']);
	$sms['mode'] = base64_encode("1"); // base64 사용시 반드시 모드값을 1로 주셔야 합니다.
	$sms['returnurl'] = base64_encode($_POST['returnurl']);
	$sms['testflag'] = base64_encode($_POST['testflag']);
	$sms['destination'] = base64_encode($_POST['destination']);
	$returnurl = $_POST['returnurl'];
	$sms['repeatFlag'] = base64_encode($_POST['repeatFlag']);
	$sms['repeatNum'] = base64_encode($_POST['repeatNum']);
	$sms['repeatTime'] = base64_encode($_POST['repeatTime']);
	$sms['smsType'] = base64_encode($_POST['smsType']); // LMS일경우 L

	$nointeractive = $_POST['nointeractive']; //사용할 경우 : 1, 성공시 대화상자(alert)를 생략

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

		//발송결과 알림
		if($Result=="success" || $Result=="reserved") {

			require "../_classes/com/db/DBUtil.php";

			$conn = db_connection("w");

			require "../_common/config.php";
			require "../_classes/com/util/Util.php";
			require "../_classes/com/etc/etc.php";
			require "../_classes/biz/order/order.php";
			require "../_classes/biz/member/member.php";
			require "../_classes/biz/board/board.php";

			$arr_order_rs = selectOrder($conn, $reserve_no);
			$rs_o_mem_nm	= trim($arr_order_rs[0]["O_MEM_NM"]); 
			$rs_r_mem_nm	= trim($arr_order_rs[0]["R_MEM_NM"]); 

			if ($this_date == "") 
				$this_date = date("Y-m-d",strtotime("0 month"));

			if ($this_h == "") 
				$this_h = date("H",strtotime("0 month"));

			if ($this_i == "") 
				$this_i = date("i",strtotime("0 month"));

			if ($this_s == "") 
				$this_s = date("s",strtotime("0 month"));


			$temp_date = $this_date." ".$this_h.":".$this_i.":".$this_s;

			$use_tf = "Y";
			// 클레임 등록
			$bb_code		= "CLAIM";
			$writer_nm	    = $s_adm_nm;
			$writer_pw	    = $s_adm_no;
			$cate_01		= $reserve_no;
			$cate_02		= 'CX005';
			$cate_03		= 0;
			$cate_04		= '99';
			$title			= $goods_name;
			$contents		= " 발송번호 : ".$rphone." 발송내용 : ".$_POST['msg']." ".$s_adm_nm." (".$temp_date.")";
			$email			= $rs_o_mem_nm;
			$homepage		= $rs_r_mem_nm;
			$keyword		= $buy_cp_no;
			$file_size	= 0;

			$contents = $contents." ".$s_adm_nm." (".$temp_date.")";

			//echo $bb_code." / ".$cate_01." / ".$cate_02." / ".$cate_03." / ".$cate_04." / ".$writer_nm." / ".$writer_pw." / ".$email." / ".$homepage." / ".$title." / ".$ref_ip." / ".$recomm." / ".$contents." / ".$file_nm." / ".$file_rnm." / ".$file_path." / ".$file_size." / ".$file_ext." / ".$keyword." / ".$comment_tf." / ".$use_tf." / ".$s_adm_no;
			//exit;
			
			$result = insertBoard($conn, $bb_code, $cate_01, $cate_02, $cate_03, $cate_04, $writer_nm, $writer_pw, $email, $homepage, $title, $ref_ip, $recomm, $contents, $file_nm, $file_rnm, $file_path, $file_size, $file_ext, $keyword, $comment_tf, $use_tf, $s_adm_no);

			if($Result=="success") { 
				$alert = "성공";
				$alert .= " 잔여건수는 ".$Count."건 입니다."; 
			}

			if($Result=="reserved") {
				$alert = "성공적으로 예약되었습니다.";
				$alert .= " 잔여건수는 ".$Count."건 입니다.";
			}

		}
		else if($Result=="3205") {
			$alert = "잘못된 번호형식입니다.";
		}

		else if($Result=="0044") {
			$alert = "스팸문자는발송되지 않습니다.";
		}

		else {
			$alert = "[Error]".$Result;
		}
	}
	else {
		$alert = "Connection Failed";
	}

	if($nointeractive=="1" && ($Result!="success" && $Result!="Test Success!" && $Result!="reserved") ) {
		echo "<script>alert('".$alert ."');</script>";
	}
	else if($nointeractive!="1") {
		echo "<script>alert('".$alert ."');</script>";
	}
	//echo "<script>location.href='".$returnurl."';</script>";
	echo "<script>
			if(/MSIE/.test(navigator.userAgent)){
			 if(navigator.appVersion.indexOf('MSIE 7.0')>=0){
			  window.open('about:blank','_self').close();
			 }
			 else{
			  window.opener=self;
			  self.close();
			 }
			}
	      </script>";

function insertClaimForSMS()
{
	
}

function GoodsOptionConfirmUp($db, $reg_adm, $goods_no)
{
	$query="UPDATE TBL_GOODS
			   SET OPTION_CF = 'Y'
				 , OPTION_ADM = '$reg_adm'
				 , OPTION_DATE = NOW()
			 WHERE GOODS_NO = '$goods_no'
			 ";

	$result=mysql_query($query,$db);
	//echo $query;
	// exit;
	if($result<>"") return 1;

	else return 0;	
}

if($mode=="GOODS_OPTION_SAVE")
{
	/******************** 인증정보 ********************/
	$sms_url = "http://sslsms.cafe24.com/sms_sender.php"; // 전송요청 URL
	// $sms_url = "https://sslsms.cafe24.com/sms_sender.php"; // HTTPS 전송요청 URL
	$sms['user_id'] = base64_encode("giftnetsms"); //SMS 아이디.
	$sms['secure'] = base64_encode("eb6791964ffa7465263e9b35cc71b691") ;//인증키
	$sms['msg'] = base64_encode(stripslashes($_POST['msg']));
	if($_POST['smsType'] == 'L') {
		$sms['subject'] = base64_encode($_POST['subject']); //제목
	}

	$sms['rphone'] = base64_encode($_POST['rphone']);
	$sms['sphone1'] = base64_encode("070");  // $_POST['sphone1']);
	$sms['sphone2'] = base64_encode("8896"); // $_POST['sphone2']);
	$sms['sphone3'] = base64_encode("0627"); // $_POST['sphone3']);
	$sms['rdate'] = base64_encode($_POST['rdate']);	//예) 20080930
	$sms['rtime'] = base64_encode($_POST['rtime']);	//예) 173000
	$sms['mode'] = base64_encode("1"); // base64 사용시 반드시 모드값을 1로 주셔야 합니다.
	$sms['returnurl'] = base64_encode($_POST['returnurl']);//	메시지 전송 후 이동할 페이지	( http:// 를 붙이셔야 합니다. )
	$sms['testflag'] = base64_encode($_POST['testflag']);//테스트일 경우 : Y	테스트가 아닐 경우 입력하지 마세요.	실제 sms는 보내지 않으나 단순한 페이지 테스트를 위한 용도
	$sms['destination'] = base64_encode($_POST['destination']);//메시지에 받는 사람 이름을 넣고 싶을 때 이용destination 값을 "휴대폰번호|이름" 과 같이 '|'문자로 구분해서 입력하시고,	msg값에 “{name}” 이라는 문구를 입력 후 전송하시면 됩니다.
	$returnurl = $_POST['returnurl'];	//메시지 전송 후 이동할 페이지	( http:// 를 붙이셔야 합니다. )
	$sms['repeatFlag'] = base64_encode($_POST['repeatFlag']);	//반복 설정을 원하는 경우 : Y	반복 설정을 원하지 않는 경우 입력하지 마세요.
	$sms['repeatNum'] = base64_encode($_POST['repeatNum']);		//1~10회 가능.
	$sms['repeatTime'] = base64_encode($_POST['repeatTime']);	//15분 이상부터 가능.
	$sms['smsType'] = base64_encode($_POST['smsType']); // LMS일경우 L

	$nointeractive = $_POST['nointeractive']; //사용할 경우 : 1, 성공시 대화상자(alert)를 생략

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

		//발송결과 알림
		if($Result=="success" || $Result=="reserved") {

			require "../_classes/com/db/DBUtil.php";

			$conn = db_connection("w");

			require "../_common/config.php";
			require "../_classes/com/util/Util.php";
			require "../_classes/com/etc/etc.php";
			require "../_classes/biz/order/order.php";
			require "../_classes/biz/member/member.php";
			require "../_classes/biz/board/board.php";

			$arr_order_rs = selectOrder($conn, $reserve_no);
			$rs_o_mem_nm	= trim($arr_order_rs[0]["O_MEM_NM"]); 
			$rs_r_mem_nm	= trim($arr_order_rs[0]["R_MEM_NM"]); 

			if ($this_date == "") 
				$this_date = date("Y-m-d",strtotime("0 month"));

			if ($this_h == "") 
				$this_h = date("H",strtotime("0 month"));

			if ($this_i == "") 
				$this_i = date("i",strtotime("0 month"));

			if ($this_s == "") 
				$this_s = date("s",strtotime("0 month"));


			$temp_date = $this_date." ".$this_h.":".$this_i.":".$this_s;

			$use_tf = "Y";
			// 클레임 등록
			$bb_code		= "CLAIM";
			$writer_nm	    = $s_adm_nm;
			$writer_pw	    = $s_adm_no;
			$cate_01		= $reserve_no;
			$cate_02		= 'CX005';
			$cate_03		= 0;
			$cate_04		= '99';
			$title			= $goods_name;
			$contents		= " 발송번호 : ".$rphone." 발송내용 : ".$_POST['msg']." ".$s_adm_nm." (".$temp_date.")";
			$email			= $rs_o_mem_nm;
			$homepage		= $rs_r_mem_nm;
			$keyword		= $buy_cp_no;
			$file_size	= 0;

			$contents = $contents." ".$s_adm_nm." (".$temp_date.")";

			//echo $bb_code." / ".$cate_01." / ".$cate_02." / ".$cate_03." / ".$cate_04." / ".$writer_nm." / ".$writer_pw." / ".$email." / ".$homepage." / ".$title." / ".$ref_ip." / ".$recomm." / ".$contents." / ".$file_nm." / ".$file_rnm." / ".$file_path." / ".$file_size." / ".$file_ext." / ".$keyword." / ".$comment_tf." / ".$use_tf." / ".$s_adm_no;
			//exit;
			
			$result = insertBoard($conn, $bb_code, $cate_01, $cate_02, $cate_03, $cate_04, $writer_nm, $writer_pw, $email, $homepage, $title, $ref_ip, $recomm, $contents, $file_nm, $file_rnm, $file_path, $file_size, $file_ext, $keyword, $comment_tf, $use_tf, $s_adm_no);

			if($Result=="success") { 
				$alert = "성공";
				$alert .= " 잔여건수는 ".$Count."건 입니다."; 
			}

			if($Result=="reserved") {
				$alert = "성공적으로 예약되었습니다.";
				$alert .= " 잔여건수는 ".$Count."건 입니다.";
			}

		}
		else if($Result=="3205") {
			$alert = "잘못된 번호형식입니다.";
		}

		else if($Result=="0044") {
			$alert = "스팸문자는발송되지 않습니다.";
		}

		else {
			$alert = "[Error]".$Result;
		}
	}
	else {
		$alert = "Connection Failed";
	}

	if($nointeractive=="1" && ($Result!="success" && $Result!="Test Success!" && $Result!="reserved") ) {
		echo "<script>alert('".$alert ."');</script>";
	}
	else if($nointeractive!="1") {
		echo "<script>alert('".$alert ."');</script>";
	}
}

?>

