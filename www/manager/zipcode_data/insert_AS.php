<?
#====================================================================
# DB Include, DB Connection
#====================================================================
	include "../../_classes/com/db/DBUtil.php";

	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/moneual/store/store.php";

	$conn = db_connection("w");


	$query ="DELETE FROM TBL_STORE WHERE STORE_TYPE = 'AS' AND store_nm = ''; ";

//	$query ="DELETE FROM TBL_STORE WHERE STORE_TYPE = 'AS' ";

	$result = mysql_query($query);

	// Check result
	// This shows the actual query sent to MySQL, and the error. Useful for debugging.

//	if (!$result) {
//		$message  = 'Invalid query: ' . mysql_error() . "\n";
//		$message .= 'Whole query: ' . $query;
//		die($message);
//	}


	if( !($File=fopen("AS.txt", "r"))) { echo('������ �����ϴ�.');}
	// ����Ÿ üũ 
	$cnt = 0;

	while(!feof($File)) {

		$Data=fgets($File, 255);
		
		echo $Data."<br>";

//		$store_url // �����ڵ�
//		$store_nm  // ���͸�
//		$addr01		 //�ּ� 
//		$phone01	// ��ȭ��ȣ
//		$phone02	// ��ȭ��ȣ
//		$phone03	// ��ȭ��ȣ
		
		$arr_data = explode("^",$Data);
		
		#echo $arr_data[2]."<br>";
		
		$store_type = "AS";
		$store_cate	= "S01";
		$use_tf			= "Y";

		$store_url = $arr_data[0];
		$store_nm	 = $arr_data[2];

		$arr_p_zip_addr = explode(" ",$arr_data[3]);
	
		$p_zip_addr01 = "";
		$p_zip_addr02 = "";
	
	
		for ($g = 0 ; $g < sizeof($arr_p_zip_addr) ; $g++) {
			if ($g < 3) {
				$p_zip_addr01 = $p_zip_addr01." ".$arr_p_zip_addr[$g];
			} else {
				$p_zip_addr02 = $p_zip_addr02." ".$arr_p_zip_addr[$g];
			}
		}

		$addr01 = $p_zip_addr01;
		$addr02 = $p_zip_addr02;

		$arr_phone = explode("-",$arr_data[4]);


		$phone01 = $arr_phone[0];
		$phone02 = $arr_phone[1];
		$phone03 = $arr_phone[2];


//		$result =  insertStore($conn, "1", $store_type, $store_cate, $store_nm, $store_url, $zipcode, $addr01, $addr02, $phone01, $phone02, $phone03, $store_hour, $contents, $file_nm, $file_rnm, $file_path, $file_size, $file_ext, $use_tf, $s_adm_no);

		//echo $addr02."<br>";


//		$arr_zip = explode(chr(9),$Data);
		
//		echo $arr_zip[0]."<br>";    //�����ȣ
//		echo $arr_zip[1]."<br>";    //�Ϸù�ȣ
//		echo $arr_zip[2]."<br>";    //�õ�
		/*
		echo $arr_zip[3]."<br>";    //�ñ���
		echo $arr_zip[4]."<br>";    //���鵿
		echo $arr_zip[5]."<br>";    //��
		echo $arr_zip[6]."<br>";    //����
		echo $arr_zip[7]."<br>";    //����
		echo $arr_zip[8]."<br>";    //����Ʈ/�ǹ���
		echo $arr_zip[9]."<br>";    //������
		echo $arr_zip[10]."<br>";    //�ּ�
		*/

//		$bunji = trim($arr_zip[7].$arr_zip[8]);
	
//		$bunji = str_replace("'","''",$bunji);
		
//		$full_addr = str_replace("'","''",$arr_zip[10]);
		
//		if ($arr_zip[0] <> "") {

//			$query ="INSERT INTO TBL_ZIPCODE (POST_NO, SIDO, SIGUNGU, DONG, RI, DOSE, BUNJI, FULL_ADDR) VALUES 
//																			 ('$arr_zip[0]', '$arr_zip[2]','$arr_zip[3]','$arr_zip[4]','$arr_zip[5]','$arr_zip[6]','$bunji','$full_addr'); ";

//			$result = mysql_query($query);
//		}
		// Check result
		// This shows the actual query sent to MySQL, and the error. Useful for debugging.

//		if (!$result) {
//			$message  = 'Invalid query: ' . mysql_error() . "\n";
//			$message .= 'Whole query: ' . $query;
//			die($message);
//		}

		$cnt++;
		//if(@mysql_query($Data, $db_conn)) { $cnt++;}   //����Է�
	}
	
	fclose($File);

#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>
