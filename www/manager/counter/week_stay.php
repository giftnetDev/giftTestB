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

	$sDate = $year."-".$month."-01";
	
	# Get_TotalDays �Լ��� �̹����� �� ���ڸ� ���Ѵ�
	# ���� ���� �ִ����� �˻����� �����ؼ� ��� ���� �����´�

	$total_days = Get_TotalDays($year,$month) ;

	# �̴��� �ְ� ī���͸� �����´� 
	# group by day �� max desc �� �����´�

	$max_result = mysql_query("SELECT WEEK,COUNT(*) MAX FROM TBL_TOTAL_COUNTER WHERE SITE_CODE = '$site_code' AND YEAR='$year' AND MONTH='$month' GROUP BY WEEK ORDER BY MAX DESC,DAY DESC LIMIT 1");
	echo mysql_error();
	$max_row = mysql_fetch_array($max_result);


# �̴��� �ּ� ī���͸� �����´� 

	$min_result = mysql_query("SELECT WEEK,COUNT(*) MAX FROM TBL_TOTAL_COUNTER WHERE SITE_CODE = '$site_code' AND YEAR='$year' AND MONTH='$month' GROUP BY WEEK ORDER BY MAX,DAY DESC LIMIT 1");
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
						<b>���Ϻ� �̿��� �� ���</b> (<?=$year?> �� <?=$month?>���� ���� ���)
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
		</td>
	<tr>
	</tr>
		</td>
			<table width='100%' cellpadding='0' cellspacing='0' border='0' bordercolorlight='#666666' bordercolordark='#FFFFFF' bgcolor='#FFFFFF' bordercolor='#FFFFFF'>
				<tr> 
					<td width='90%' height='158' valign='top'> 
						<table border=0 cellpadding=2 cellspacing=2 width=100%>
							<tr>
								<td align=center bgcolor='#F6F6F6' width='50'>
									����
								</td>
								<td>
									���� ��
								</td>
							</tr>
<?
	for($i=0;$i<=6;$i++) {
		
		#echo "select count(*) from tb_total_counter where year='$year' and month='$month' and week='$i'";
	
		$r = mysql_query("SELECT COUNT(*) FROM TBL_TOTAL_COUNTER WHERE SITE_CODE = '$site_code' AND YEAR='$year' AND MONTH='$month' AND WEEK='$i'");
		$q = mysql_result($r,0,0);
	
		# �̹��� ���� ũ�⸦ ���Ѵ�
		# (�ڱⰪ / ���� ū ��)*�ְ���̷� ���ϸ� �ȴ�
	
		$max_width = 490;
		$img_width=($q/$max_row[1])*$max_width;

?>
							<tr>
								<td bgcolor='#F6F6F6'>
<?
			if ($i == 0) {
				echo "�Ͽ���";			
			} else if ($i == 1) {
				echo "������";			
			} else if ($i == 2) {
				echo "ȭ����";			
			} else if ($i == 3) {
				echo "������";			
			} else if ($i == 4) {
				echo "�����";			
			} else if ($i == 5) {
				echo "�ݿ���";			
			} else if ($i == 6) {
				echo "�����";			
			}
?>				
								</td>
								<td class="lpd10">

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
					<b><font color='red'>($q ȸ)</font></b>
				";
			} else {
				echo "
					($q ȸ)
				";
			}
	echo "
								</td>
							</tr>
	 ";
}

?>

						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<TABLE width='100%' cellpadding='0' cellspacing='0' border='0' bordercolorlight='#666666' bordercolordark='#FFFFFF' bgcolor='#FFFFFF' bordercolor='#FFFFFF'>
				<tr>
					<td>
						<table border='0' cellpadding=2 cellspacing=2 width=100% bordercolorlight='#666666' bordercolordark='#FFFFFF' bgcolor='#FFFFFF' bordercolor='#FFFFFF'>
							<tr align=center bgcolor='#F6F6F6'>
								<td align=center bgcolor='#F6F6F6' width='50'>
									�� ��
								</td>
								<td >�Ͽ���</td>
								<td >������</td>
								<td >ȭ����</td>
								<td >������</td>
								<td >�����</td>
								<td >�ݿ���</td>
								<td >�����</td>
								<td >�հ�</td>
							</tr>
<?
		$we = date("w", strtotime($sDate));	

		if ($we == 0) {

			$start_day = 0;
			$end_day = 6;

		} else if ($we == 1) {

			$start_day = -1;
			$end_day = 5;

		} else if ($we == 2) {

			$start_day = -2;
			$end_day = 4;

		} else if ($we == 3) {
			
			$start_day = -3;
			$end_day = 3;

		} else if ($we == 4) {

			$start_day = -4;
			$end_day = 2;

		} else if ($we == 5) {

			$start_day = -5;
			$end_day = 2;

		} else if ($we == 6) {

			$start_day = -6;
			$end_day = 1;

		}

		# ������ ������ ��¥ ���ϱ�

		if ($month != "01") {
			$pre_month = $month - 1;
			$pre_year = $year;
		} else {
			
			$pre_month = "12";	
			$pre_year = $year - 1;
		}
		
		$pre_total_days = Get_TotalDays($pre_year,$pre_month);
		
		$pre_day = $pre_total_days - ($we - 1) ;


		# ������ ù�� ���ϱ�

		if ($month != "12") {
			$next_month = $month + 1;
			$next_year = $year;
		} else {
			
			$next_month = "01";	
			$next_year = $year + 1;
		}

		$next_sDate = $next_year."-".$next_month."-01";

		$next_we = date("w", strtotime($next_sDate));	

//		$query ="SELECT COUNT(*) CNT FROM TBL_TOTAL_COUNTER 
//				WHERE REGDATE >= date_add('$sDate', interval $start_day day) 
//				AND REGDATE <= date_add('$sDate',interval $end_day day) ";
	
//		$result = mysql_query($query);

		$firstDay = 0;

		$first_week = 1;

		#echo $total_days ;
		#echo $next_we;

		$week_total = 0;
		$month_total = 0;
		$sun_total = 0;
		$mon_total = 0;
		$tus_total = 0;
		$wed_total = 0;
		$thu_total = 0;
		$fri_total = 0;
		$sat_total = 0;

?>
		<tr align=right>
			<td align=center bgcolor='#F6F6F6' width='60'>
				<?echo $first_week++?> ��
			</td>

<?
		for ($i = 0 ; $i <= $total_days+($we-1) ; $i++) {
			if (($i == 7) || ($i == 14) || ($i == 21) || ($i == 28) || ($i == 35)) {
?>
			<td>
				<table width="100%">
					<tr>
						<td>					
							&nbsp;&nbsp;&nbsp;<br>
						</td>
					</tr>
					<tr>
						<td align='right'>
							<b><font color=blue><?echo number_format($week_total)?></font></b>
						</td>
					</tr>
				</table>			
			</td>		
		</tr>
		<tr align=right>
			<td align=center bgcolor='#F6F6F6' width='60'>
				<?echo $first_week++?> ��
			</td>
<?
				$week_total = 0;
			}
?>
			<td>
<?
			if ($i == $we) {			
				$firstDay = 1;
			}
					
					
			if ($firstDay > 0) {
?>
				<table width="100%">
					<tr>
						<td>					
							<font color='green'><?echo $firstDay?></font>
						</td>
					</tr>
					<tr>
						<td align='right'>
<?

				if (strlen($firstDay) == 1) {
					$day_ = "0".$firstDay; 				
				} else {
					$day_ = $firstDay;
				}
				
				$r = mysql_query("SELECT COUNT(*) FROM TBL_TOTAL_COUNTER WHERE SITE_CODE = '$site_code' AND YEAR='$year' AND MONTH='$month' AND DAY='$day_'");
				$q = mysql_result($r,0,0);
						
				echo "<b>".number_format($q)."</b>";
				$week_total = $week_total + $q; 
				$month_total = $month_total + $q; 

				if (($i == 0) || ($i == 7) || ($i == 14) || ($i == 21) || ($i == 28) || ($i == 35)) {
					$sun_total = $sun_total + $q;
				}
				if (($i == 1) || ($i == 8) || ($i == 15) || ($i == 22) || ($i == 29) || ($i == 36)) {
					$mon_total = $mon_total + $q;
				}
				if (($i == 2) || ($i == 9) || ($i == 16) || ($i == 23) || ($i == 30) || ($i == 37)) {
					$tus_total = $tus_total + $q;
				}
				if (($i == 3) || ($i == 10) || ($i == 17) || ($i == 24) || ($i == 31) || ($i == 38)) {
					$wed_total = $wed_total + $q;
				}
				if (($i == 4) || ($i == 11) || ($i == 18) || ($i == 25) || ($i == 32) || ($i == 39)) {
					$thu_total = $thu_total + $q;
				}
				if (($i == 5) || ($i == 12) || ($i == 19) || ($i == 26) || ($i == 33) || ($i == 40)) {
					$fri_total = $fri_total + $q;
				}
				if (($i == 6) || ($i == 13) || ($i == 20) || ($i == 27) || ($i == 34) || ($i == 41)) {
					$sat_total = $sat_total + $q;
				}


?>
						</td>
					</tr>
				</table>
<?						 
				$firstDay++;
			} else {

?>

				<table width="100%">
					<tr>
						<td>					
							<font color='silver'><?echo $pre_day++?></font>
						</td>
					</tr>
					<tr>
						<td align='right'>
<?
				if (strlen($pre_month) == 1) {
					$pre_month_ = "0".$pre_month; 				
				} else {
					$pre_month_ = $pre_month;
				}


				$r = mysql_query("SELECT COUNT(*) FROM TBL_TOTAL_COUNTER WHERE SITE_CODE = '$site_code' AND YEAR='$pre_year' AND MONTH='$pre_month_' AND DAY='$pre_day++'");
				$q = mysql_result($r,0,0);
						
				echo "<b>".number_format($q)."</b>";
				$week_total = $week_total + $q; 
				$month_total = $month_total + $q; 

				if (($i == 0) || ($i == 7) || ($i == 14) || ($i == 21) || ($i == 28) || ($i == 35)) {
					$sun_total = $sun_total + $q;
				}
				if (($i == 1) || ($i == 8) || ($i == 15) || ($i == 22) || ($i == 29) || ($i == 36)) {
					$mon_total = $mon_total + $q;
				}
				if (($i == 2) || ($i == 9) || ($i == 16) || ($i == 23) || ($i == 30) || ($i == 37)) {
					$tus_total = $tus_total + $q;
				}
				if (($i == 3) || ($i == 10) || ($i == 17) || ($i == 24) || ($i == 31) || ($i == 38)) {
					$wed_total = $wed_total + $q;
				}
				if (($i == 4) || ($i == 11) || ($i == 18) || ($i == 25) || ($i == 32) || ($i == 39)) {
					$thu_total = $thu_total + $q;
				}
				if (($i == 5) || ($i == 12) || ($i == 19) || ($i == 26) || ($i == 33) || ($i == 40)) {
					$fri_total = $fri_total + $q;
				}
				if (($i == 6) || ($i == 13) || ($i == 20) || ($i == 27) || ($i == 34) || ($i == 41)) {
					$sat_total = $sat_total + $q;
				}

?>
						</td>
					</tr>
				</table>
<?											
			}

			if ($firstDay-1 == $total_days) {
				if($next_we	!= 0) {
					for ($j = 1; $j < (8 - $next_we); $j++) {
?>
				<td>
					<table width="100%">
						<tr>
							<td>					
								<font color='silver'><?echo $j?></font>
							</td>
						</tr>
						<tr>
							<td align='right'>
<?

						if (strlen($j) == 1) {
							$j_ = "0".$j; 				
						} else {
							$j_ = $j;
						}

						if (strlen($next_month) == 1) {
							$next_month_ = "0".$next_month; 				
						} else {
							$next_month_ = $next_month;
						}

						$r = mysql_query("SELECT COUNT(*) FROM TBL_TOTAL_COUNTER WHERE SITE_CODE = '$site_code' AND YEAR='$next_year' AND MONTH='$next_month_' AND DAY='$j_'");
						$q = mysql_result($r,0,0);
						
						echo "<b>".number_format($q)."</b>";
						$week_total = $week_total + $q; 
						$month_total = $month_total + $q; 

						if (($i == 0) || ($i == 7) || ($i == 14) || ($i == 21) || ($i == 28) || ($i == 35)) {
							$sun_total = $sun_total + $q;
						}
						if (($i == 1) || ($i == 8) || ($i == 15) || ($i == 22) || ($i == 29) || ($i == 36)) {
							$mon_total = $mon_total + $q;
						}
						if (($i == 2) || ($i == 9) || ($i == 16) || ($i == 23) || ($i == 30) || ($i == 37)) {
							$tus_total = $tus_total + $q;
						}
						if (($i == 3) || ($i == 10) || ($i == 17) || ($i == 24) || ($i == 31) || ($i == 38)) {
							$wed_total = $wed_total + $q;
						}
						if (($i == 4) || ($i == 11) || ($i == 18) || ($i == 25) || ($i == 32) || ($i == 39)) {
							$thu_total = $thu_total + $q;
						}
						if (($i == 5) || ($i == 12) || ($i == 19) || ($i == 26) || ($i == 33) || ($i == 40)) {
							$fri_total = $fri_total + $q;
						}
						if (($i == 6) || ($i == 13) || ($i == 20) || ($i == 27) || ($i == 34) || ($i == 41)) {
							$sat_total = $sat_total + $q;
						}
                		
?>
							</td>
						</tr>
					</table>
				</td>
<?											
								
											
					}
				}		
			}
?>
			</td>
<?
			}
?>

			<td>
				<table width="100%">
				<tr>
					<td>					
						&nbsp;&nbsp;&nbsp;<br>
					</td>
				</tr>
				<tr>
					<td align='right'>
						<b><font color=blue><?echo number_format($week_total)?></font></b>
					</td>
				</tr>
				</table>			
			</td>		
		</tr>
		<tr>
			<td align=center bgcolor='#F6F6F6' width='60' height='35'>
				�� ��<br>
			</td>
			<td>
				<table width='100%'>
				<tr>
					<td>					
						&nbsp;&nbsp;&nbsp;<br>
					</td>
				</tr>
				<tr>
					<td align='right'>
						<b><font color=blue><?echo number_format($sun_total)?></font></b>
					</td>
				</tr>
				</table>
			</td>
			<td>
				<table width='100%'>
				<tr>
					<td>					
						&nbsp;&nbsp;&nbsp;<br>
					</td>
				</tr>
				<tr>
					<td align='right'>
						<b><font color=blue><?echo number_format($mon_total)?></font></b>
					</td>
				</tr>
				</table>
			</td>
			<td>
				<table width='100%'>
				<tr>
					<td>					
						&nbsp;&nbsp;&nbsp;<br>
					</td>
				</tr>
				<tr>
					<td align='right'>
						<b><font color=blue><?echo number_format($tus_total)?></font></b>
					</td>
				</tr>
				</table>
			</td>
			<td>
				<table width='100%'>
				<tr>
					<td>					
						&nbsp;&nbsp;&nbsp;<br>
					</td>
				</tr>
				<tr>
					<td align='right'>
						<b><font color=blue><?echo number_format($wed_total)?></font></b>
					</td>
				</tr>
				</table>
			</td>
			<td>
				<table width='100%'>
				<tr>
					<td>					
						&nbsp;&nbsp;&nbsp;<br>
					</td>
				</tr>
				<tr>
					<td align='right'>
						<b><font color=blue><?echo number_format($thu_total)?></font></b>
					</td>
				</tr>
				</table>
			</td>
			<td>
				<table width='100%'>
				<tr>
					<td>					
						&nbsp;&nbsp;&nbsp;<br>
					</td>
				</tr>
				<tr>
					<td align='right'>
						<b><font color=blue><?echo number_format($fri_total)?></font></b>
					</td>
				</tr>
				</table>
			</td>
			<td>
				<table width='100%'>
				<tr>
					<td>					
						&nbsp;&nbsp;&nbsp;<br>
					</td>
				</tr>
				<tr>
					<td align='right'>
						<b><font color=blue><?echo number_format($sat_total)?></font></b>
					</td>
				</tr>
				</table>
			</td>
			<td>
				<table width='100%'>
				<tr>
					<td>					
						&nbsp;&nbsp;&nbsp;<br>
					</td>
				</tr>
				<tr>
					<td align='right'>
						<b><font color=#FF5555><?echo number_format($month_total)?></font></b>
					</td>
				</tr>
				</table>
			</td>
		</tr>
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