<?session_start();?>
<?
// Àý´ë °áÄÚ À­ºÎºÐ Ä­¶ç¿ìÁö¸¶¼¼¿ä
header("Content-Type: text/plain; charset=euc-kr"); 

#====================================================================
# common_header Check Session
#====================================================================
//	require "../../_common/common_header.php"; 

#====================================================================
# DB Include, DB Connection
#====================================================================
	include "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("r");

#=====================================================================
# common function, login_function
#=====================================================================
	include "../../_classes/com/util/Util.php";
	include "../../_classes/biz/admin/admin.php";

#====================================================================
# Request Parameter
#====================================================================

	if (trim($keyword) !="") {
		
		$result = dupAdmin($conn, $keyword);

		print($result);
	}

?>