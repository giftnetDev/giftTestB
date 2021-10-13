<?session_start();?>
<?
# =============================================================================
# File Name    : logout.php
# =============================================================================


	$_SESSION['s_adm_no']				= "";
	$_SESSION['s_adm_id']				= "";
	$_SESSION['s_adm_nm']				= "";
	$_SESSION['s_adm_email']			= "";
	$_SESSION['s_adm_group_no']			= "";
	$_SESSION['s_adm_com_code']			= "";
	$_SESSION['s_adm_cp_type']			= "";
	$_SESSION['s_adm_md_tf']			= "";

	session_destroy();


?>
<meta http-equiv='Refresh' content='0; URL=/manager/'>
