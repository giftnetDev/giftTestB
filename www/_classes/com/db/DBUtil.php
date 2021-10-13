<?
	error_reporting(E_ALL & ~E_NOTICE);
	ini_set("display_errors", 1);


	function db_connection($usr_type) {
		
		if ($usr_type == "w") {

			$host_addr  =  "localhost";				// DB 접속 IP 주소
			$dbname     =  "gfttestb";								// My-Sql DB 선택 
			$db_id      =  "gfttestb";						// My-Sql DB ID
			$db_passwd  =  "giftnet6818";						// My-Sql DB 비밀번호  //minew00!

			// FTP 비번 zz1210zz
				
		} else if ($usr_type == "r") {

			$host_addr  =  "localhost";				// DB 접속 IP 주소
			$dbname     =  "gfttestb";								// My-Sql DB 선택 
			$db_id      =  "gfttestb";						// My-Sql DB ID
			$db_passwd  =  "giftnet6818";						// My-Sql DB 비밀번호  //minew00!

		}

		$link = mysql_connect($host_addr, $db_id, $db_passwd);
		
		if (!$link) {
			die('데이터 베이스에 연결에 실패 하였습니다. :' . mysql_error());
		}

		$db_selected = mysql_select_db($dbname, $link);
		
		if (!$db_selected) {
			die ('해당 데이터 베이스를 찾을 수 없습니다. : ' . mysql_error());
		}

		return 	$link;

	}

	function sql_result_array($handle,$row) {
		$count = mysql_num_fields($handle);
		for($i=0;$i<$count;$i++){
			$fieldName = mysql_field_name($handle,$i);
			$ret[$fieldName] = mysql_result($handle,$row,$i);
			//echo $fieldName . "=" . $ret[$fieldName] . "<BR>";
		}
		return $ret;
	}

?>