<?
#====================================================================
# DB Include, DB Connection
#====================================================================
	include "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");


	$query ="DELETE FROM TBL_ZIPCODE; ";

	$result = mysql_query($query);

	// Check result
	// This shows the actual query sent to MySQL, and the error. Useful for debugging.

	if (!$result) {
		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $query;
		die($message);
	}


	if( !($File=fopen("20150515_euc_kr.txt", "r"))) { echo('������ �����ϴ�.');}
	// ����Ÿ üũ 
	$cnt = 0;

	while(!feof($File)) {
	//while(!feof($File) && ($cnt < 100)) {

		$Data=fgets($File, 255);
	
		$arr_zip = explode(chr(9),$Data);
		
		echo $arr_zip[0]."<br>";    //�����ȣ
		echo $arr_zip[1]."<br>";    //�Ϸù�ȣ
		echo $arr_zip[2]."<br>";    //�õ�
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

		$bunji = trim($arr_zip[7].$arr_zip[8]);
	
		$bunji = str_replace("'","''",$bunji);
		
		$full_addr = str_replace("'","''",$arr_zip[10]);
		
		if ($arr_zip[0] <> "") {

			$query ="INSERT INTO TBL_ZIPCODE (POST_NO, SIDO, SIGUNGU, DONG, RI, DOSE, BUNJI, FULL_ADDR) VALUES 
																			 ('$arr_zip[0]', '$arr_zip[2]','$arr_zip[3]','$arr_zip[4]','$arr_zip[5]','$arr_zip[6]','$bunji','$full_addr'); ";

			$result = mysql_query($query);
		}
		// Check result
		// This shows the actual query sent to MySQL, and the error. Useful for debugging.

		if (!$result) {
			$message  = 'Invalid query: ' . mysql_error() . "\n";
			$message .= 'Whole query: ' . $query;
			die($message);
		}

	$cnt++;
	//if(@mysql_query($Data, $db_conn)) { $cnt++;}   //����Է�
}
fclose($File);

#====================================================================
# DB Close
#====================================================================

#	mysql_close($conn);
?>
