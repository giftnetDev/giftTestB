<?
    require "../../_classes/com/db/DBUtil.php";

    $conn=db_connection("w");
    $catalogs=array();
?>
<?
    class CCatalog{
        public $catalogNo;
        public $pageCnt;
        
        public function __construct($catalogNo, $pageCnt){
            $this->catalogNo=$catalogNo;
            $this->pageCnt=$pageCnt;
        }
        // public function __destruct()
        // {
            
        // }
        public function getPageCnt(){
            return $this->pageCnt;
        }
        public function getCatalogNo(){
            return $this->catalogNo;
        }
    }

?>
<?
    //"ClassTest.php" Inner Functions
    function ictLoadCatalog($db){
        $query="SELECT CATALOG_NO, PAGE_CNT FROM TBL_CATALOG_TOP ; ";
        $result =mysql_query($query, $db);
        $cnt=mysql_num_rows($result);
        
        $record=array();
        if($cnt>0){
            for($i=0;$i<$cnt;$i++){
                $record[$i]=mysql_fetch_assoc($result);
            }
            return $record;
        }
        return "";
    }
?>
<?
    //"ClassTest.php" Functions
    function ctInitPage($db, &$catalogs){
        $rs_arr=ictLoadCatalog($db);
        if($rs_arr<>""){
            $cnt=count($rs_arr);
            for($i=0;$i<$cnt;$i++){
                $catalogs[$i]=new CCatalog($rs_arr[$i]['CATALOG_NO'], $rs_arr[$i]['PAGE_CNT']);
            }
            echo"Init Complete<br/>";
        }

    }
    function ctGetClassInfo($catalogNo,&$catalogs){
         $a=$catalogs[$catalogNo]->getPageCnt();
         echo "<script>alert('".$a."');</script>";
         return 0;
    }

?>
<?
    ctInitPage($conn,$catalog);
?>


<!DOCTYPE html>
<HTML>
    <HEAD>
        <meta http-equiv="Content-Type" content="text/html; charset=euc-kr"/>
        <script src="../jquery/jquery-1.11.3.min.js"></script>
        <script>
            var catalog;
            function func(){
                var a;
                //alert('function 실행');
                catalog= $('#txtPage').val();
                <?ctGetClassInfo(3,$catalogs);?>
            }
        </script>

    </HEAD>

    <BODY>
        <div>
            <input type="text" name="txtPage" id="txtPage"/>
            <input type="button" name="btnViewPageInfo" id="btnPage" value="해당 페이지 정보 확인" onclick="func()"/>
        </div>
    </BODY>

</HTML>



