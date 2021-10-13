<?

	function insertEmail($db, $page_from, $cp_no, $name_from, $email_from, $name_to, $email_to, $title, $body, $attach_link, $reg_adm, $option) {

		$RESERVE_NO = $option["RESERVE_NO"];
		$ORDER_GOODS_NO = $option["ORDER_GOODS_NO"];

		$query="INSERT INTO TBL_EMAIL 
					(PAGE_FROM, CP_NO, NAME_FROM, EMAIL_FROM, NAME_TO, EMAIL_TO, TITLE, BODY, ATTACH_LINK, REG_DATE, REG_ADM";
		
		if($RESERVE_NO != "")
			$query.=" , RESERVE_NO  ";
		
		if($ORDER_GOODS_NO != "")
			$query.=" , ORDER_GOODS_NO  ";
		
		$query.="	) VALUES ('$page_from', '$cp_no', '$name_from', '$email_from', '$name_to', '$email_to', '$title', '$body', '$attach_link', now(), '$reg_adm' ";

		if($RESERVE_NO != "")
			$query.=" , '".$RESERVE_NO."'  ";
		
		if($ORDER_GOODS_NO != "")
			$query.=" , '".$ORDER_GOODS_NO."'  ";

		$query.=" ); ";
		
		//echo $query;
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return $true;
		}
	}

	function getEmailDate($db, $page_from, $cp_no) {


		$query=" SELECT REG_ADM, REG_DATE 
		           FROM TBL_EMAIL 
				  WHERE PAGE_FROM = '$page_from' AND CP_NO = '$cp_no'
			   ORDER BY REG_DATE DESC 
			      LIMIT 0, 1";
		
		//echo $query;
		//exit;

		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}



	function listEmailByReserveNo($db, $reserve_no) {


		$query=" SELECT PAGE_FROM, CP_NO, NAME_FROM, EMAIL_FROM, NAME_TO, EMAIL_TO, TITLE, BODY, ATTACH_LINK, REG_DATE, REG_ADM 
		           FROM TBL_EMAIL 
				  WHERE RESERVE_NO = '$reserve_no'
			   ORDER BY REG_DATE DESC ";
		
		//echo $query;
		//exit;

		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}
	

?>