<?php

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/com/util/ImgUtil.php";


	function deleteRefundState($db, $refund_no, $del_adm) {

		$query=" UPDATE TBL_REFUND 
					SET 
						DEL_TF				= 'Y',
						DEL_ADM				=	'$del_adm',
						DEL_DATE			=	now()
				  WHERE REFUND_NO			= '$refund_no' ";

		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateRefundState($db, $refund_no, $up_adm) {

		$query=" UPDATE TBL_REFUND 
					SET 
						REFUND_STATE		= '1',
						UP_ADM				=	'$up_adm',
						UP_DATE				=	now()
				  WHERE REFUND_NO			= '$refund_no' AND REFUND_STATE = '0' ";

		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}
		
	if($mode == "DELETE_REFUND")
	{
		$isSuccess = deleteRefundState($conn, $refund_no, $s_adm_no);

		$results = "[{\"RESULT\":\"".$isSuccess."\"}]";
		echo $results;
	}

	if($mode == "RECEIVE_REFUND")
	{
		$isSuccess = updateRefundState($conn, $refund_no, $s_adm_no);

		$results = "[{\"RESULT\":\"".$isSuccess."\"}]";
		echo $results;
	}





?>

