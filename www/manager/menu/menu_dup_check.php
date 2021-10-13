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
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("r");

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_classes/com/util/Util.php";
	require "../../_classes/biz/menu/menu.php";

#====================================================================
# Request Parameter
#====================================================================

	if (trim($keyword) !="") {
		
		$result = dupMenuRight($conn, $keyword);

		print($result);
	}

?>