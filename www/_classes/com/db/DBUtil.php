<?
	error_reporting(E_ALL & ~E_NOTICE);
	ini_set("display_errors", 1);


	function db_connection($usr_type) {
		
		if ($usr_type == "w") {

			$host_addr  =  "localhost";				// DB ���� IP �ּ�
			$dbname     =  "gfttestb";								// My-Sql DB ���� 
			$db_id      =  "gfttestb";						// My-Sql DB ID
			$db_passwd  =  "giftnet6818";						// My-Sql DB ��й�ȣ  //minew00!

			// FTP ��� zz1210zz
				
		} else if ($usr_type == "r") {

			$host_addr  =  "localhost";				// DB ���� IP �ּ�
			$dbname     =  "gfttestb";								// My-Sql DB ���� 
			$db_id      =  "gfttestb";						// My-Sql DB ID
			$db_passwd  =  "giftnet6818";						// My-Sql DB ��й�ȣ  //minew00!

		}

		$link = mysql_connect($host_addr, $db_id, $db_passwd);
		
		if (!$link) {
			die('������ ���̽��� ���ῡ ���� �Ͽ����ϴ�. :' . mysql_error());
		}

		$db_selected = mysql_select_db($dbname, $link);
		
		if (!$db_selected) {
			die ('�ش� ������ ���̽��� ã�� �� �����ϴ�. : ' . mysql_error());
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