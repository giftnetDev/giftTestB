<?
function MobileCheck() {
    // 모바일 기종(배열 순서 중요, 대소문자 구분 안함)
    $ary_m = array("iPhone","iPod","IPad","Android","Blackberry","SymbianOS|SCH-M\d+","Opera Mini","Windows CE","Nokia","Sony","Samsung","LGTelecom","SKT","Mobile","Phone");
  
    for($i=0; $i<count($ary_m); $i++){
        if(preg_match("/$ary_m[$i]/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return $ary_m[$i];
            break;
        }
    }
    return "PC";
}
?>

<?
$chk_m = MobileCheck();
  
$chk_value = "";
  
if($chk_m == "PC"){
  
    $chk_value = "pc";
  
} else {
  
   $chk_value = "mobile";
  
}
//echo"===============2323=============.$chk_value";
?>

<!DOCTYPE html>
<html lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr;" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>기프트넷</title>
</head>
<frameset rows="*" border="0" >
<!--<frame src="../newHp/Nindex.php" scrolling="no" marginwidth="0" marginheight="0" noresize>-->

<? 
    if($chk_value == "pc")
    {
?>        
    <frame src="newHp/Nindex.php" scrolling="no" marginwidth="0" marginheight="0" noresize>
<?  }
    else
    {
?>
    <!-- <frame src="./newHp/Mindex.php"> -->
    <frame src="newHp/Mindex.php">
<?
    }
?>

</frameset>

</html>