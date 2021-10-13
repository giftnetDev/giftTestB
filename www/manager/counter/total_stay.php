<?session_start();?>
<?
# =============================================================================
# File Name    : admin_list.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @아름지기 Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$site_code = trim($site_code); // 메뉴마다 셋팅 해 주어야 합니다
	
	if ($site_code == "hy") {
		$menu_right = "S0006"; // 메뉴마다 셋팅 해 주어야 합니다
		$menu_name = "함양 한옥";
	}

	if ($site_code == "arum") {
		$menu_right = "S0007"; // 메뉴마다 셋팅 해 주어야 합니다
		$menu_name = "아름재단";
	}

	if ($site_code == "comp") {
		$menu_right = "S0008"; // 메뉴마다 셋팅 해 주어야 합니다
		$menu_name = "공모전";
	}

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

#====================================================================
# common_header Check Session
#====================================================================
	require "../../_common/common_header.php"; 


#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";

	function Get_TotalDays($year,$month) { 

		$day = 1; 

		while(checkdate($month,$day,$year)) { 
			$day ++ ; 
		} 
		$day--; 

		return $day; 
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_chrset?>" />
<title><?=$g_title?></title>
<link rel="STYLESHEET" type="text/css" href="../css/bbs.css" />
<link rel="STYLESHEET" type="text/css" href="../css/layout.css" />
<STYLE type='text/css'>
/*
A:link {COLOR: #000000; TEXT-DECORATION: none}
A:visited {COLOR: #000000; TEXT-DECORATION: none}
A:hover {COLOR: #980000; TEXT-DECORATION: underline}
*/
TD {FONT-SIZE: 9pt}
.h {FONT-SIZE: 9pt; LINE-HEIGHT: 120%}
.h2 {FONT-SIZE: 9pt; LINE-HEIGHT: 180%}
.s {FONT-SIZE: 8pt}
.l {FONT-SIZE: 11pt}
.text {  line-height: 125%}
</STYLE>

<script type="text/javascript" src="../js/common.js"></script>

</head>

<body id="bg">
<div id="wrap">

<?
	#====================================================================
	# common top_area
	#====================================================================

	require "../../_common/top_area.php";

	#====================================================================
	# common left_area
	#====================================================================

	require "../../_common/left_area.php";
?>

	<div id="contents">
		<p><a href="/">홈</a> &gt; 사이트 관리</p>
		
		<div id="tit01">
			<h2><?=$menu_name?> 접속 통계 조회 </h2>
		</div>

		<div id="bbsList">

<?


# 현재 시간을 년월일로 가져온다
# 입력 받는 년월이 없을 경우 ( 첫 클릭시에 이번달의 통계를 보여주는 것) 오늘 날짜를 기준으로 한다
# 시간 별 통계나 요일별 통계 추가 필요

if(!$year) {
	$year = date("Y",time());
} 

if(!$month) {
	$month = date("m",time());
}

# Get_TotalDays 함수로 이번달의 총 일자를 구한다
# 몇일 까지 있는지를 검사한후 쿼리해서 결과 값을 가져온다

$total_days = Get_TotalDays($year,$month) ;

# 이달의 최고 카운터를 가져온다 
# group by day 와 max desc 로 가져온다

$max_result = mysql_query("SELECT DAY,COUNT(*) MAX FROM TBL_TOTAL_COUNTER WHERE SITE_CODE = '$site_code' AND YEAR='$year' AND MONTH='$month' GROUP BY DAY ORDER BY MAX DESC,DAY DESC LIMIT 1");
echo mysql_error();
$max_row = mysql_fetch_array($max_result);


# 이달의 최소 카운터를 가져온다 

$min_result = mysql_query("SELECT DAY,COUNT(*) MAX FROM TBL_TOTAL_COUNTER WHERE SITE_CODE = '$site_code' AND YEAR='$year' AND MONTH='$month' GROUP BY DAY ORDER BY MAX,DAY DESC LIMIT 1");
$min_row = mysql_fetch_array($min_result);


# $month 의 총 카운터 숫자를 가져온다


$result = mysql_query("SELECT COUNT(*) FROM TBL_TOTAL_COUNTER WHERE SITE_CODE = '$site_code' AND YEAR='$year' AND MONTH='$month'");
$total = mysql_result($result,0,0);

?>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td align='center'>
<?
	include "./menu.inc";
?>
		</td>
	<tr>
	</tr>
		</td>
			<table border="0" cellpadding="2" cellspacing="2" width="100%">
				<tr>
					<td class="lpd10">
						<b>총 방문 이용자 수 통계</b> (<?=$year?> 년 <?=$month?>월의 접속 통계)
					</td>
				</tr>
				<tr>
					<td class="lpd10">
						<font color='#666699'>총 접속자 수:</font>  <?=$total?> 명 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<font color='#666699'>최고 :</font>  
						<img src='/kor/counter/img/red.gif' width='10' height='10'> <?=$max_row[1]?>명 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<font color='#666699'>최소 :</font> 
						<img src='/kor/counter/img/yellow.gif' width='10' height='10'> <?=$min_row[1]?>명
					</td>
				</tr>
			</table>
<?

if ($max_row[1] == 0) {
	$max_row[1] = 1;
}

# 해당 날짜의 카운터를 가져온다
?>
			<table width='100%' cellpadding='0' cellspacing='0' border='0' bordercolorlight='#666666' bordercolordark='#FFFFFF' bgcolor='#FFFFFF' bordercolor='#FFFFFF'>
				<tr> 
					<td width='50%' height='158' valign='top'> 
						<table border=0 cellpadding=2 cellspacing=2 width=100%>
							<tr>
								<td align=center bgcolor='#F6F6F6' width='30'>일자</td>
								<td>접속자 수</td>
							</tr> 
<?
for($i=1;$i<=15;$i++) {
	$i_len = strlen($i);
	$zero = 0;
	if($i_len == 1) {
		$i = $zero.$i;
	}
	$r = mysql_query("SELECT COUNT(*) FROM TBL_TOTAL_COUNTER WHERE SITE_CODE = '$site_code' AND YEAR='$year' AND MONTH='$month' AND DAY='$i'");
	$q = mysql_result($r,0,0);
	
	# 이미지 바의 크기를 구한다
	# (자기값 / 가장 큰 값)*최고길이로 구하면 된다
	
	$max_width = 200;
	$img_width=($q/$max_row[1])*$max_width;
?>
							<tr>
								<td bgcolor='#F6F6F6'>
									<?=$i?> 일
								</td>
								<td class="lpd10" >
<?
	if($i==$max_row[0]) {
		echo "
			<img src='/kor/counter/img/red.gif' width='$img_width' height='10'>
			";
		} elseif($i==$min_row[0]) {
			echo "
			<img src='/kor/counter/img/yellow.gif' width='$img_width' height='10'>
			";
		} else {
			echo "
			<img src='/kor/counter/img/sur.gif' width='$img_width' height='10'>
			";
		}
			if($i==$max_row[0]) {
				echo "
					<b><font color='red'>($q 명)</font></b>
				";
			} else {
				echo "
					($q 명)
				";
			}
?>
								</td>
							</tr>
<?
		}
?>
						</table>
					</td>
					<td width='50%' height='158' valign='top'> 
						<table border=0 cellpadding=2 cellspacing=2 width=100%>
							<tr>
								<td align=center bgcolor='#F6F6F6' width='30'>일자</td>
								<td>접속자 수</td>
							</tr>
<?
for($i=16;$i<=$total_days;$i++) {
	
	$i_len = strlen($i);
	$zero = 0;
	if($i_len == 1) {
		$i = $zero.$i;
	}
	$r = mysql_query("SELECT COUNT(*) FROM TBL_TOTAL_COUNTER WHERE SITE_CODE = '$site_code' AND YEAR='$year' AND MONTH='$month' AND DAY='$i'");
	$q = mysql_result($r,0,0);
	
	# 이미지 바의 크기를 구한다
	# (자기값 / 가장 큰 값)*최고길이로 구하면 된다
	
	$max_width = 200;
	$img_width=($q/$max_row[1])*$max_width;
?>
							<tr>
								<td align=center bgcolor='#F6F6F6' width='30'>
									<?=$i?> 일
								</td>
								<td class="lpd10" >
<?
	if($i==$max_row[0]) {
		echo "
		<img src='/kor/counter/img/red.gif' width='$img_width' height='10'>
		";
	} elseif($i==$min_row[0]) {
		echo "
		<img src='/kor/counter/img/yellow.gif' width='$img_width' height='10'>
		";
	} else {
		echo "
		<img src='/kor/counter/img/sur.gif' width='$img_width' height='10'>
		";
	}
		if($i==$max_row[0]) {
			echo "
				<b><font color='red'>($q 명)</font></b>
			";
		} else {
			echo "
			($q 명)
			";
		}
?>
								</td>
							</tr>
<?
}
?>
						</table>
					</td>
				</tr>
			</table>

			<table width="100%">
				<tr bgcolor='#F6F6F6'>
					<td align=center colspan=2>
						<a href="<?=$PHP_SELF?>?site_code=<?=$site_code?>&year=<?=($year-1)?>&month=12"><font color='red'>[<?=($year-1)?> 년 12 월]</font></a>&nbsp;&nbsp;&nbsp;
<?
	# 월별 링크를 출력한다
	
	for($i=01;$i<=12;$i++) {
		# $i 의 길이를 측정해서 1 이라면 앞자리에 0 을 붙여 준다
		
		$i_len = strlen($i);
		$zero = 0;
		if($i_len == 1) {
			$i = $zero.$i;
		}
		# $i의 달을 쿼리해서 카운터가 있으면 링크를 출력하고 없다면 링크 출력을 하지 않눈다
				
		$q = "SELECT * FROM TBL_TOTAL_COUNTER WHERE SITE_CODE = '$site_code' AND YEAR=$year AND MONTH=$i";
		$month_result = mysql_query($q);
		$row = mysql_fetch_array($month_result);	
		
		if ($row) {
				echo "
					<a href='$PHP_SELF?site_code=$site_code&year=$year&month=$i'><font color='red'>$i 월</font></a>&nbsp;
				";		
			} else {
				echo "
					$i 월&nbsp;
				";	
			}
	}
?>
						&nbsp;&nbsp;&nbsp;<a href="<?=$PHP_SELF?>?site_code=<?=$site_code?>&year=<?=($year+1)?>&month=01"><font color="red">[<?=($year+1)?> 년 01 월]</font></a>&nbsp;
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<br>
<br>
		</div>
	</div>
	<div id="site_info">Copyright &copy; 2009 (재)아름지기 All Rights Reserved.</div>

</div>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>