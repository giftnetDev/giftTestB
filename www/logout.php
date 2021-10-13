<?session_start();?>
<?
# =============================================================================
# File Name    : logout.php
# =============================================================================


	$_SESSION['MEM_NO']				= "";
	$_SESSION['MEM_NM']				= "";
	$_SESSION['CP_CODE']			= "";
	$_SESSION['CP_NO']				= "";
	$_SESSION['CP_NM']				= "";

	session_destroy();


?>
<meta http-equiv='Refresh' content='0; URL=/'>