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

#====================================================================
# Request Parameter
#====================================================================

	$mm_top	 = "6";
	$mm_sub	 = "2";

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$con_event_no			= trim($con_event_no);
	$con_member_type	= trim($con_member_type);
	$con_pick_tf			= trim($con_pick_tf);

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

	$arr_rs = listEventApply($conn, $con_event_no, $con_event_type, $con_member_type, $con_pick_tf, $con_use_tf, $del_tf, $search_field, $search_str, $nPage, $nPageSize);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
</head>

<body>
<h3>이벤트응모자</h3>
<!-- List -->
<table border="1">
	<tr>
		<th>No.</th>
		<th>이벤트명</th>
		<th>아이디</th>
		<th>이름</th>
		<th>주소</th>
		<th>연락처</th>
		<th>E-MAIL</th>
		<th>응모일자</th>
		<th>당첨여부</th>
	</tr>
	
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$rn							= trim($arr_rs[$j]["rn"]);
							$APPLY_NO				= trim($arr_rs[$j]["APPLY_NO"]);
							$EVENT_NO				= trim($arr_rs[$j]["EVENT_NO"]);
							$EVENT_NM				= trim($arr_rs[$j]["EVENT_NM"]);
							$MEMBER_NO			= trim($arr_rs[$j]["MEMBER_NO"]);
							$MEMBER_NM			= trim($arr_rs[$j]["MEMBER_NM"]);
							$MEMBER_ID			= trim($arr_rs[$j]["MEMBER_ID"]);
							$ZIPCODE				= trim($arr_rs[$j]["ZIPCODE"]);
							$ADDR01					= trim($arr_rs[$j]["ADDR01"]);
							$ADDR02					= trim($arr_rs[$j]["ADDR02"]);
							$PHONE01				= trim($arr_rs[$j]["PHONE01"]);
							$PHONE02				= trim($arr_rs[$j]["PHONE02"]);
							$PHONE03				= trim($arr_rs[$j]["PHONE03"]);
							$EMAIL					= trim($arr_rs[$j]["EMAIL"]);
							$PICK_TF				= trim($arr_rs[$j]["PICK_TF"]);
							$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);
							
							$REG_DATE = date("Y-m-d",strtotime($REG_DATE));

							if ($PICK_TF == "Y") {
								$STR_PICK_TF = "<strong class=\"fc_red\">당첨</strong>";
							} else {
								$STR_PICK_TF = "<strong>미당첨</strong>";
							}
							
				?>
	<tr>
		<td><?= $rn ?></td>
		<td><?=$EVENT_NM?></td>
		<td><?=$MEMBER_ID?></td>
		<td><?=$MEMBER_NM?></td>
		<td>[<?=$ZIPCODE?>] <?=$ADDR01?> <?=$ADDR02?></td>
		<td><?=$PHONE01?>-<?=$PHONE02?>-<?=$PHONE03?></td>
		<td><?=$EMAIL?></td>
		<td><?=$REG_DATE ?></td>
		<td align="center"><?= $STR_PICK_TF ?></td>
	</tr>

				<?			
						}
					} else { 
				?> 
	<tr>
		<td height="50" align="center" colspan="9">등록된 내용이 없습니다</td>
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
