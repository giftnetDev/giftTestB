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
    //This Page Code
    ctInitPage($conn,$catalogs);

?>