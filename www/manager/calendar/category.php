<?
  if($_SERVER["HTTP_REFERER"]==str_replace($_SERVER["HTTP_HOST"]."/","",$_SERVER["HTTP_REFERER"])) { echo "Bad Request!"; exit; }
  $pseq = $_POST[seq];
  $pcname = trim($_POST[cname]);
  $pctext = trim($_POST[ctext]);
  $pccolor = $_POST[ccolor];
  if(!str_replace(" ","",$pcname)) {
    echo "<script> alert('Ķ�������� �Է��ϼ���.'); </script>";
    exit;
  }

  $Connect = mysql_connect("localhost", "giftace", "giftnet6818") or die("MySQL Server�� ������ �� �����ϴ�.");
  mysql_select_db("giftace", $Connect);

  if($_POST["mde"]=="del") {
    $sql = "DELETE FROM TBL_CALENDAR WHERE SEQ='".$pseq."' AND CNAME='".$pcname."';";
  }
  else {
    if(!$pseq) {
      $sql = "INSERT INTO TBL_CALENDAR(CNAME,CTEXT,CCOLOR) VALUES ('".$pcname."','".$pctext."','".$pccolor."');";
    }
    else {
      $sql = "update TBL_CALENDAR set CNAME='".$pcname."',CTEXT='".$pctext."',CCOLOR='".$pccolor."' where SEQ='".$pseq."';";
    }
  }
  mysql_query($sql,$Connect);

  $sql = "SELECT SEQ,CNAME,CTEXT,CCOLOR FROM TBL_CALENDAR ORDER BY SEQ;";
  $qry = mysql_query($sql,$Connect);
  while($rs = mysql_fetch_array($qry)) {
    $clist .= "  parent.cateList('".$rs["SEQ"]."','".$rs["CNAME"]."','".$rs["CTEXT"]."','".$rs["CCOLOR"]."');".chr(13);
    $lclist .= "  parent.leftcateList('".$rs["CNAME"]."','".$rs["CCOLOR"]."');".chr(13);
    if(!$dclist) {
      $dcname = $rs["CNAME"];
      $dcolor = $rs["CCOLOR"];
    }
    $dclist .= "  parent.diarycateList('".$rs["SEQ"]."','".$rs["CNAME"]."','".$rs["CCOLOR"]."');".chr(13);
  } mysql_free_result($qry);

  mysql_close($Connect);
?>

<script>
  //Ķ���� ���� ���� Ķ���� ��Ͽ� �߰�
  var tbl = parent.document.getElementById("cateListTable");
  for(i=tbl.rows.length-1; i>0; i--) { tbl.deleteRow(i); }
<?=$clist?>

  //���� �޴��� Ķ���� ��Ͽ� �߰�
  var tbl = parent.document.getElementById("leftcateListTable");
  for(i=tbl.rows.length-1; i>=0; i--) { tbl.deleteRow(i); }
<?=$lclist?>

  //����������� ī�װ� ��Ͽ� �߰�
  var tbl = parent.document.getElementById("calendarcateDiv").childNodes[0];
  for(i=tbl.rows.length-1; i>=0; i--) { tbl.deleteRow(i); }
<?=$dclist?>

  //����������� ī�װ� �⺻�� ���ڿ� �� ���� ����
  parent.document.getElementById("cateTr").bgColor = "<?=$dcolor?>";
  parent.document.getElementById("cateTr").cells[0].innerText = "<?=$dcname?>";

  //Ķ���� �� �ݱ�
  parent.cCclose();
</script>