<?
	if($_SERVER["HTTP_REFERER"]==str_replace($_SERVER["HTTP_HOST"]."/","",$_SERVER["HTTP_REFERER"])) { echo "Bad Request!"; exit; }
	$pseq			= $_POST[seq];
	$pcate		= $_POST[cate];
	$psdate		= $_POST[sdate];
	$pedate		= $_POST[edate];
	$ptitle		= trim($_POST[title]);
	$pmemo		= trim($_POST[memo]);
	$psdt			= $_POST[sdt];
	$pedt			= $_POST[edt];

	if(!str_replace(" ","",$ptitle)) {
		echo "<script> alert('내용을 입력하세요.'); </script>";
		exit;
	}

	$Connect = mysql_connect("localhost", "giftace", "giftnet6818") or die("MySQL Server에 연결할 수 없습니다.");
	mysql_select_db("giftace", $Connect);

	$sql = "insert into TBL_DIARY(DCATE,SDATE,EDATE,DTITLE,DMEMO) values('".$pcate."','".$psdate."','".$pedate."','".$ptitle."','".$pmemo."');";
	mysql_query($sql,$Connect);

	$sql = "select A.SEQ,B.CCOLOR,";
	$sql .= "(case when A.SDATE<'".$psdt."' then '".$psdt."' else A.SDATE end) RSDATE,";
	$sql .= "(case when A.EDATE>'".$pedt."' then '".$pedt."' else A.EDATE end) REDATE,";
	$sql .= "A.SDATE,A.EDATE,A.DTITLE,A.DMEMO from TBL_DIARY A left join TBL_CALENDAR B on A.DCATE=B.SEQ where A.SDATE<='".$pedt."' and A.EDATE>='".$psdt."' order by A.SDATE,A.SEQ;";
	$qry = mysql_query($sql,$Connect);
	$i = 0;

	while($rs = mysql_fetch_array($qry)) {
		$list .= "  parent.diarylist[".$i."] = '".$rs["SEQ"]."|".$rs["CCOLOR"]."|".$rs["RSDATE"]."|".$rs["REDATE"]."|".$rs["SDATE"]."|".$rs["EDATE"]."|".str_replace("|","&#124;",str_replace("'","&#39;",$rs["DTITLE"]))."|".str_replace("|","&#124;",str_replace("'","&#39;",str_replace(chr(13).chr(10),"<br>",$rs["DMEMO"])))."';".chr(13);
		$i++;
	} mysql_free_result($qry);

	mysql_close($Connect);
?>
<?=$psdt?>
<br>
<?=$pedt?>
<script>
	parent.diarylist = new Array();
<?=$list?>
	parent.diarybarInsert();
</script>