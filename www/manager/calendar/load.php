<?
	if($_SERVER["HTTP_REFERER"]==str_replace($_SERVER["HTTP_HOST"]."/","",$_SERVER["HTTP_REFERER"])) { echo "Bad Request!"; exit; }

	$Connect = mysql_connect("localhost", "giftace", "giftnet6818") or die("MySQL Server에 연결할 수 없습니다.");

	mysql_select_db("giftace",$Connect);

	$sql = "SELECT SEQ,CNAME,CTEXT,CCOLOR FROM TBL_CALENDAR ORDER BY SEQ;";

	$qry = mysql_query($sql,$Connect);
	
	while($rs = mysql_fetch_array($qry)) {
		$clist .= "  parent.cateList('".$rs["SEQ"]."','".$rs["CNAME"]."','".$rs["CTEXT"]."','".$rs["CCOLOR"]."');".chr(13);
		$lclist .= "  parent.leftcateList('".$rs["CNAME"]."','".$rs["CCOLOR"]."');".chr(13);
		if(!$dclist) {
			$dcname = $rs["cname"];
			$dcolor = $rs["ccolor"];
		}
		$dclist .= "  parent.diarycateList('".$rs["SEQ"]."','".$rs["CNAME"]."','".$rs["CCOLOR"]."');".chr(13);
	} mysql_free_result($qry);

	$sql = "select A.SEQ,B.CCOLOR,";
	$sql .= "(case when A.SDATE<'".$sdt."' then '".$sdt."' else A.SDATE end) RSDATE,";
	$sql .= "(case when A.EDATE>'".$edt."' then '".$edt."' else A.EDATE end) REDATE,";
	$sql .= "A.SDATE,A.EDATE,A.DTITLE,A.DMEMO from TBL_DIARY A left join TBL_CALENDAR B on A.DCATE=B.SEQ where A.SDATE<='".$edt."' and A.EDATE>='".$sdt."' order by A.SDATE,A.SEQ;";
	$qry = mysql_query($sql,$Connect);
	$i = 0;

	while($rs = mysql_fetch_array($qry)) {
		$list .= "  parent.diarylist[".$i."] = '".$rs["SEQ"]."|".$rs["CCOLOR"]."|".$rs["RSDATE"]."|".$rs["REDATE"]."|".$rs["SDATE"]."|".$rs["EDATE"]."|".str_replace("|","&#124;",str_replace("'","&#39;",$rs["DTITLE"]))."|".str_replace("|","&#124;",str_replace("'","&#39;",str_replace(chr(13).chr(10),"<br>",$rs["DMEMO"])))."';".chr(13);
		$i++;
	} mysql_free_result($qry);


	mysql_close($Connect);
?>
<?=$sql?><br>
<script>
  //캘린더 관리 폼의 캘린더 목록에 추가
  var tbl = parent.document.getElementById("cateListTable");
  for(i=tbl.rows.length-1; i>0; i--) { tbl.deleteRow(i); }
<?=$clist?>
  parent.cCcontrol();

  //좌측 메뉴의 캘린더 목록에 추가
  var tbl = parent.document.getElementById("leftcateListTable");
  for(i=tbl.rows.length-1; i>=0; i--) { tbl.deleteRow(i); }
<?=$lclist?>

  //일정등록폼의 카테고리 목록에 추가
  var tbl = parent.document.getElementById("calendarcateDiv_table");
  for(i=tbl.rows.length-1; i>=0; i--) { tbl.deleteRow(i); }
<?=$dclist?>
	//일정등록폼의 카테고리 기본값 문자열 및 색상 설정
	parent.document.getElementById("cateTr").bgColor = "<?=$dcolor?>";
	parent.document.getElementById("cateTr").cells[0].innerText = "<?=$dcname?>";
	parent.diarylist = new Array();
<?=$list?>
	parent.diarybarInsert();
</script>