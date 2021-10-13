<?session_start()?>
<?
    require "../../_classes/com/db/DBUtil.php";

    $conn=db_connection("w");

	$curUrl	=	trim($_POST['curUrl']);
	$mode	=	trim($_POST['mode']);




	// echo "curUrl : ".$curUrl."<br>";
	// echo "mode : ".$mode."<br>";
	// exit;


?>
<?
	function chkMember($db, $mem_id) {

		$query = "SELECT MEM_NO, MEM_TYPE, MEM_NM, MEM_PW, EMAIL, PHONE, HPHONE, CP_NO, USE_TF, DEL_TF
					FROM TBL_MEMBER 
				   WHERE MEM_ID = '$mem_id' 
				   		AND DEL_TF = 'N' ";

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
	function getCompanyNameWithNoCode($db, $cp_no) {

		if($cp_no <= 0) return "&nbsp;";

		if (is_numeric($cp_no)) {

			$query = "SELECT CONCAT(CP_NM, ' ', CP_NM2) FROM TBL_COMPANY WHERE CP_NO = '$cp_no' ";
		
			//echo $query;

			$result = mysql_query($query,$db);
			$rows   = mysql_fetch_array($result);

			if ($result <> "") {
				$tmp_str  = $rows[0];
			} else {
				$tmp_str  = "&nbsp;";
			}
		} else {
			$tmp_str  = "&nbsp;";
		}

		return $tmp_str;

	}
	function insertUserLog($db, $user_type, $log_id, $log_ip) {
		
		$query="INSERT INTO TBL_USER_LOG (USER_TYPE, LOG_ID, LOG_IP, LOGIN_DATE) 
															 values ('$user_type', '$log_id', '$log_ip', now()); ";
		
		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


?>
<?
	if($mode == "LOGIN") { 
		$iid            = trim($_POST['iid']);
        $pwd			= trim(MD5($_POST['pwd']));
        $curUrl         = trim($_POST['curUrl']);
        // exit;

		$arr_rs = chkMember($conn, $iid);

		//MEM_NO, MEM_NM, MEM_PW, EMAIL, PHONE, HPHONE, CP_NO

		if(sizeof($arr_rs) > 0) { 
			$rs_mem_no				= trim($arr_rs[0]["MEM_NO"]); 
			$rs_mem_type			= trim($arr_rs[0]["MEM_TYPE"]); 
			$rs_mem_nm				= trim($arr_rs[0]["MEM_NM"]); 
			$rs_mem_pw				= trim($arr_rs[0]["MEM_PW"]); 
			$rs_email				= trim($arr_rs[0]["EMAIL"]); 
			$rs_phone				= trim($arr_rs[0]["PHONE"]);
			$rs_hphone				= trim($arr_rs[0]["HPHONE"]);
			$rs_cp_no				= trim($arr_rs[0]["CP_NO"]);
			$rs_use_tf				= trim($arr_rs[0]["USE_TF"]); 
			$rs_del_tf				= trim($arr_rs[0]["DEL_TF"]); 
		}

		$result = "";

		if (sizeof($arr_rs) <= 0 || $rs_use_tf == "N") 
		{
			$result = "1";
			$str_result = "해당 아이디가 없습니다. 다시 확인 부탁 드립니다.";
		} else {

			if ($rs_mem_pw == $pwd) {
				$result = "0";
				$str_result = "";
			} else {
				$result = "2";
				$str_result = "회원 정보가 일치 하지 않습니다. 다시 확인 부탁 드립니다.";
			}
		}
        // echo "str_result".$str_result."<br>";
        // exit;

		// result 0 : 승인 , 1 : 아이디 없음, 2 : 비밀번호 틀림

        // echo "result : ".$result."<br><br>";
		if ($result == "0") {
			
			$_SESSION['C_MEM_NO']				= $rs_mem_no;
			$_SESSION['C_MEM_NM']				= $rs_mem_nm;
			$_SESSION['C_MEM_ID']				= $iid;
			$_SESSION['C_CP_NO']				= $rs_cp_no;
			$_SESSION['C_CP_NM']				= getCompanyNameWithNoCode($conn, $rs_cp_no);
			$_SESSION['C_MEM_TYPE']			= $rs_mem_type;
			insertUserLog($conn, "MO", $rs_mem_nm, $_SERVER['REMOTE_ADDR']);
        }

        // print_r($_SESSION);
        // exit;
    }
			
?>
<meta http-equiv='Refresh' content='0; URL=/<?=$curUrl?>'>