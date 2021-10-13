<?session_start();?>
<?
# =============================================================================
# File Name    : admin_list.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @�Ƹ����� Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$site_code = trim($site_code); // �޴����� ���� �� �־�� �մϴ�
	
	if ($site_code == "hy") {
		$menu_right = "S0006"; // �޴����� ���� �� �־�� �մϴ�
		$menu_name = "�Ծ� �ѿ�";
	}

	if ($site_code == "arum") {
		$menu_right = "S0007"; // �޴����� ���� �� �־�� �մϴ�
		$menu_name = "�Ƹ����";
	}

	if ($site_code == "comp") {
		$menu_right = "S0008"; // �޴����� ���� �� �־�� �մϴ�
		$menu_name = "������";
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
		<p><a href="/">Ȩ</a> &gt; ����Ʈ ����</p>
		
		<div id="tit01">
			<h2><?=$menu_name?> ���� ��� ��ȸ </h2>
		</div>

		<div id="bbsList">

<?


# ���� �ð��� ����Ϸ� �����´�
# �Է� �޴� ����� ���� ��� ( ù Ŭ���ÿ� �̹����� ��踦 �����ִ� ��) ���� ��¥�� �������� �Ѵ�
# �ð� �� ��質 ���Ϻ� ��� �߰� �ʿ�

if(!$year) {
	$year = date("Y",time());
} 

if(!$month) {
	$month = date("m",time());
}

# Get_TotalDays �Լ��� �̹����� �� ���ڸ� ���Ѵ�
# ���� ���� �ִ����� �˻����� �����ؼ� ��� ���� �����´�

$total_days = Get_TotalDays($year,$month) ;

# �̴��� �ְ� ī���͸� �����´� 
# group by day �� max desc �� �����´�

$max_result = mysql_query("SELECT DAY,COUNT(*) MAX FROM TBL_TOTAL_COUNTER WHERE SITE_CODE = '$site_code' AND YEAR='$year' AND MONTH='$month' GROUP BY DAY ORDER BY MAX DESC,DAY DESC LIMIT 1");
echo mysql_error();
$max_row = mysql_fetch_array($max_result);


# �̴��� �ּ� ī���͸� �����´� 

$min_result = mysql_query("SELECT DAY,COUNT(*) MAX FROM TBL_TOTAL_COUNTER WHERE SITE_CODE = '$site_code' AND YEAR='$year' AND MONTH='$month' GROUP BY DAY ORDER BY MAX,DAY DESC LIMIT 1");
$min_row = mysql_fetch_array($min_result);


# $month �� �� ī���� ���ڸ� �����´�


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
						<b>�� �湮 �̿��� �� ���</b> (<?=$year?> �� <?=$month?>���� ���� ���)
					</td>
				</tr>
				<tr>
					<td class="lpd10">
						<font color='#666699'>�� ������ ��:</font>  <?=$total?> �� &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<font color='#666699'>�ְ� :</font>  
						<img src='/kor/counter/img/red.gif' width='10' height='10'> <?=$max_row[1]?>�� &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<font color='#666699'>�ּ� :</font> 
						<img src='/kor/counter/img/yellow.gif' width='10' height='10'> <?=$min_row[1]?>��
					</td>
				</tr>
			</table>
<?

if ($max_row[1] == 0) {
	$max_row[1] = 1;
}

# �ش� ��¥�� ī���͸� �����´�
?>
			<table width='100%' cellpadding='0' cellspacing='0' border='0' bordercolorlight='#666666' bordercolordark='#FFFFFF' bgcolor='#FFFFFF' bordercolor='#FFFFFF'>
				<tr> 
					<td width='50%' height='158' valign='top'> 
						<table border=0 cellpadding=2 cellspacing=2 width=100%>
							<tr>
								<td align=center bgcolor='#F6F6F6' width='30'>����</td>
								<td>������ ��</td>
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
	
	# �̹��� ���� ũ�⸦ ���Ѵ�
	# (�ڱⰪ / ���� ū ��)*�ְ���̷� ���ϸ� �ȴ�
	
	$max_width = 200;
	$img_width=($q/$max_row[1])*$max_width;
?>
							<tr>
								<td bgcolor='#F6F6F6'>
									<?=$i?> ��
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
					<b><font color='red'>($q ��)</font></b>
				";
			} else {
				echo "
					($q ��)
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
								<td align=center bgcolor='#F6F6F6' width='30'>����</td>
								<td>������ ��</td>
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
	
	# �̹��� ���� ũ�⸦ ���Ѵ�
	# (�ڱⰪ / ���� ū ��)*�ְ���̷� ���ϸ� �ȴ�
	
	$max_width = 200;
	$img_width=($q/$max_row[1])*$max_width;
?>
							<tr>
								<td align=center bgcolor='#F6F6F6' width='30'>
									<?=$i?> ��
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
				<b><font color='red'>($q ��)</font></b>
			";
		} else {
			echo "
			($q ��)
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
						<a href="<?=$PHP_SELF?>?site_code=<?=$site_code?>&year=<?=($year-1)?>&month=12"><font color='red'>[<?=($year-1)?> �� 12 ��]</font></a>&nbsp;&nbsp;&nbsp;
<?
	# ���� ��ũ�� ����Ѵ�
	
	for($i=01;$i<=12;$i++) {
		# $i �� ���̸� �����ؼ� 1 �̶�� ���ڸ��� 0 �� �ٿ� �ش�
		
		$i_len = strlen($i);
		$zero = 0;
		if($i_len == 1) {
			$i = $zero.$i;
		}
		# $i�� ���� �����ؼ� ī���Ͱ� ������ ��ũ�� ����ϰ� ���ٸ� ��ũ ����� ���� �ʴ���
				
		$q = "SELECT * FROM TBL_TOTAL_COUNTER WHERE SITE_CODE = '$site_code' AND YEAR=$year AND MONTH=$i";
		$month_result = mysql_query($q);
		$row = mysql_fetch_array($month_result);	
		
		if ($row) {
				echo "
					<a href='$PHP_SELF?site_code=$site_code&year=$year&month=$i'><font color='red'>$i ��</font></a>&nbsp;
				";		
			} else {
				echo "
					$i ��&nbsp;
				";	
			}
	}
?>
						&nbsp;&nbsp;&nbsp;<a href="<?=$PHP_SELF?>?site_code=<?=$site_code?>&year=<?=($year+1)?>&month=01"><font color="red">[<?=($year+1)?> �� 01 ��]</font></a>&nbsp;
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
	<div id="site_info">Copyright &copy; 2009 (��)�Ƹ����� All Rights Reserved.</div>

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