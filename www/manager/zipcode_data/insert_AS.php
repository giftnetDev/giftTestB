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


	if( !($File=fopen("AS.txt", "r"))) { echo('파일이 없습니다.');}
	// 데이타 체크 
	$cnt = 0;

	while(!feof($File)) {

		$Data=fgets($File, 255);
		
		echo $Data."<br>";

//		$store_url // 지역코드
//		$store_nm  // 센터명
//		$addr01		 //주소 
//		$phone01	// 전화번호
//		$phone02	// 전화번호
//		$phone03	// 전화번호
		
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
		
//		echo $arr_zip[0]."<br>";    //우편번호
//		echo $arr_zip[1]."<br>";    //일련번호
//		echo $arr_zip[2]."<br>";    //시도
		/*
		echo $arr_zip[3]."<br>";    //시군구
		echo $arr_zip[4]."<br>";    //읍면동
		echo $arr_zip[5]."<br>";    //리
		echo $arr_zip[6]."<br>";    //도서
		echo $arr_zip[7]."<br>";    //번지
		echo $arr_zip[8]."<br>";    //아파트/건물명
		echo $arr_zip[9]."<br>";    //변경일
		echo $arr_zip[10]."<br>";    //주소
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
		//if(@mysql_query($Data, $db_conn)) { $cnt++;}   //디비입력
	}
	
	fclose($File);

#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>
