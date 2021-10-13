<?
	if($_SERVER["HTTP_REFERER"]==str_replace($_SERVER["HTTP_HOST"]."/","",$_SERVER["HTTP_REFERER"])) { echo "Bad Request!"; exit; }

	$Connect = mysql_connect("localhost", "giftace", "giftnet6818") or die("MySQL Server에 연결할 수 없습니다.");

	mysql_select_db("giftace",$Connect);

	$update_seq_no = trim($update_seq_no);

	$sql = "SELECT B.SEQ, B.DCATE, B.SDATE, ,B.EDATE, B.DTITLE, B.DMEMO, A.CCOLOR
						FROM TBL_CALENDAR A, TBL_DIARY B 
					 WHERE B.SEQ = '$update_seq_no' ";

	$qry = mysql_query($sql,$Connect);

	$rows   = mysql_fetch_array($qry);
	$seq		= $rows[0];
	$dcate  = $rows[1];
	$sdate  = $rows[2];
	$edate  = $rows[3];
	$dtitle	= $rows[4];
	$dmemo  = $rows[5];
	$ccolor	= $rows[6];

	mysql_close($Connect);
?>
<?=$sql?><br>
<script>

</script>