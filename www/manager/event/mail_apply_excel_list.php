<?session_start();?>
<?
# =============================================================================
# File Name    : apply_list.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @MONEUAL Corp. All Rights Reserved.
# =============================================================================

$file_name="당첨자-".date("Ymd").".xls";
header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
header( "Content-Disposition: attachment; filename=$file_name" );
header( "Content-Description: orion@giringrim.com" );


#====================================================================
# common_header Check Session
#====================================================================
	include "../../_common/common_header.php"; 

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");
	
#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/moneual/event/event.php";
	require "../../_classes/moneual/mailevent/mailevent.php";

#====================================================================
# Request Parameter
#====================================================================

	$mm_top	 = "6";
	$mm_sub	 = "2";

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$con_event_no			= trim($con_event_no);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
	$del_tf = "N";
#============================================================
# Page process
#============================================================
	
	$nPage = 1;
	$nPageSize = 10000;

#===============================================================
# Get Search list count
#===============================================================
	$this_date = date("Ymd",strtotime("0 day"));

	$arr_rs = listMailEvent($conn, $con_event_no, $con_use_tf, $del_tf, $search_field, $search_str, $nPage, $nPageSize);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
</head>

<body>

<h3>추천 이벤트응모자</h3>
<!-- List -->
<table class="tbl_list" border="1">
	<tr>
		<th>No.</th>
		<th>이벤트명</th>
		<th>아이디</th>
		<th>이름</th>
		<th>휴대전화번호</th>
		<th>추천 E-MAIL</th>
		<th>응모일자</th>
	</tr>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$rn							= trim($arr_rs[$j]["rn"]);
							$EMAIL_EVENT_NO	= trim($arr_rs[$j]["EMAIL_EVENT_NO"]);
							$EVENT_NO				= trim($arr_rs[$j]["EVENT_NO"]);
							$EVENT_NM				= trim($arr_rs[$j]["EVENT_NM"]);
							$M_ID						= trim($arr_rs[$j]["M_ID"]);
							$M_NO						= trim($arr_rs[$j]["M_NO"]);
							$M_NM						= trim($arr_rs[$j]["M_NM"]);
							$HPHONE					= trim($arr_rs[$j]["HPHONE"]);
							$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);
							
							$REG_DATE = date("Y-m-d",strtotime($REG_DATE));

				?>
	<tr>
		<td><?= $rn ?></td>
		<td><?=$EVENT_NM?></td>
		<td><?=$M_ID?></td>
		<td><?=$M_NM?></td>
		<td><?=$HPHONE?></td>
		<td>
			<?
				$arr_rs_email = selectMailSendEvent($conn, $EMAIL_EVENT_NO, $g_site_no);
				
				if (sizeof($arr_rs_email) > 0) {
					for ($h = 0 ; $h < sizeof($arr_rs_email); $h++) {
						$RS_M_NM	= trim($arr_rs_email[$h]["M_NM"]);
						$RS_EMAIL	= trim($arr_rs_email[$h]["EMAIL"]);
						
						$arr_rs_mem = selectMemberAsEmail($conn, $RS_EMAIL);
						
						$x = 0; 
						$m_no	= trim($arr_rs_mem[$x]["m_no"]);
						
						if ($m_no == "") {
							echo $RS_M_NM." [".$RS_EMAIL."]<br>";
						} else {
					?>
					<font color="orange"><?=$RS_M_NM?> [<?=$RS_EMAIL?>]</font><br>;
					<?
						}
					}
				}
			?>		
		</td>
		<td><?=$REG_DATE ?></td>
	</tr>

				<?			
						}
					} else { 
				?> 
	<tr>
		<td height="50" align="center" colspan="7">등록된 내용이 없습니다</td>
	</tr>
				<? 
					}
				?>

</table>
			<!-- //List -->
			
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>
