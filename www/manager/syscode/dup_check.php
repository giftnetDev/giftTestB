<?session_start();?>
<?
// ���� ���� ���κ� ĭ�����������
header("Content-Type: text/plain; charset=euc-kr"); 

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "SY002"; // �޴����� ���� �� �־�� �մϴ�

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";
	
#====================================================================
# common_header
#====================================================================
	require "../../_common/common_header.php"; 

#=====================================================================
# common function, login_function
#=====================================================================
	include "../../_classes/com/util/Util.php";
	include "../../_classes/biz/syscode/syscode.php";

#====================================================================
# Request Parameter
#====================================================================

	if (trim($keyword) !="") {
		
		$result = dupPcode($conn, $keyword, $type);

		print($result);
	}

?>