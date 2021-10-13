<?
    //Set Up
    require "../../_classes/com/util/Util.php";
    require "../../_classes/com/db/DBUtil.php";
    $conn=db_connection("w");
    $mode=$_POST['mode'];
    // $catalogs=array();
    // $pages="";
    error_reporting(-1);
    ini_set('display_errors', 'On');

?>
<?

    class CPage{
        public $width;
        public $height;
        public $pageIdx;
        public function __construct($pageIdx,$width,$height){
            $this->width=$width;
            $this->height=$height;
            $this->pageIdx=$pageIdx;
        }
    }
?>
<?
    // ajax_catalog Inner Functions
    function iacIsRedundancyCatalogName($db,$catalogNameTr){
        $query="SELECT CATALOG_NAME FROM TBL_CATALOG_TOP WHERE CATALOG_NAME = '".$catalogNameTr."'; ";
        $result=mysql_query($query, $db);
        // print_r($result);
        if($result<>""){
            $cnt=mysql_num_rows($result);
            if($cnt>0){ 
                return 1;
            }
            else{
                return 0;// ���ٴ� �ǹ̷� �ߺ� �ƴ�
            } 
        }
        
    }
?>
<?
    //ajax_catalog Functions

    function acCreateNewCatalog($db, $catalogName){
        $catalogName=iconv("utf-8","euc-kr",$catalogName);

        if(iacIsRedundancyCatalogName($db,$catalogName)==1){
            //echo"<script>console.log('DB Redundancy');</script>";
            return 1;
        }
        $query="INSERT INTO TBL_CATALOG_TOP(CATALOG_NAME) VALUES('".$catalogName."'); ";
        $result=mysql_query($query,$db);
        echo $query;
        if($result<>""){
            return 0;
        }
        else{
            return -1;
        } 
        
    }
    function acGetCatalogPagesWidthNo($db, $catalogNo){
        $query="SELECT PAGE_CNT FROM TBL_CATALOG_TOP WHERE CATALOG_NO =".$catalogNo. " ; ";
        $result=mysql_query($query, $db);
        $cnt=mysql_num_rows($result);
        $record=array();
        if($cnt>0){
            $record[0]=mysql_fetch_assoc($result);
            return $record[0]['PAGE_CNT'];
        }
        else{
            return -1;
        }    
    }

    function acAddPage($db,$catalogNo, $pageNo,$pageWidth, $pageHeight, $location){
        //echo "location : $location<br>";
        //TBL_CATALOG_TOP�� PAGE_CNT�� 1 ������Ų��.
        $query="UPDATE TBL_CATALOG_TOP SET PAGE_CNT = ".$pageNo."+1 WHERE CATALOG_NO =".$catalogNo."; ";
        $result=mysql_query($query,$db);
        //���� �ش�īŻ�α׿� ������������ �ڿ� �߰��Ǵ� ���� �ƴ� � ������ �տ� �߰��ȴٸ� �� �� ��������� �������� �ش�īŻ�α׿� ���ϴ� � ������ �̻��� PAGE�� PAGE_NO�� 1�� ���������ش�.
        $queryU="UPDATE TBL_CATALOG_PARENT SET PAGE_NO= PAGE_NO + 1 WHERE PAGE_CATALOG = ".$catalogNo." AND PAGE_NO >= ".$location." ; ";
        mysql_query($queryU,$db);

        if($result<>""){
            $query2="INSERT INTO TBL_CATALOG_PARENT(PAGE_CATALOG, PAGE_NO, PAGE_SIZE_X, PAGE_SIZE_Y) VALUES(".$catalogNo." , ".$location." , ".$pageWidth." , ".$pageHeight." );";
            $result2=mysql_query($query2,$db);
            if($result2<>""){
                return 0;//���� ���
            }
            else{
                $queryE="UPDATE TBL_CATALOG_TOP SET PAGE_CNT =PAGE_CNT-1 WHERE CATALOG_NO=".$catalogNo."' ";
                mysql_query($queryE,$db);
                return -1;//�� ��°���� ����(1���� ����)
            }
        }
        else{
            return -2;//ù ��°���� ����(2�� ����)
        }
    }

    function acInsertGoodsToPage($db, $catalogNo, $pageNo, $goodsNo, $row, $col){
        $queryS="SELECT GOODS_NO, GOODS_CODE, GOODS_NAME, CATE_04, FILE_PATH_150, FILE_RNM_150, SALE_PRICE FROM TBL_GOODS WHERE GOODS_NO = ".$goodsNo." ; ";
        $resultS = mysql_query($queryS, $db);
        $cntS=mysql_num_rows($resultS);
        $recordS=array();
        
        if($cntS>0){
            $record[0]=mysql_fetch_assoc($resultS);
        }

        $query= "INSERT INTO TBL_CATALOG_CHILD(GOODS_NO, CATALOG_NO, PAGE_NO, POS_X, POS_Y, SIZE_X, SIZE_Y, ";
    }

?>
<?
    if($mode=="DELETE_PAGE"){
        $catalogNo=$_POST['catalogNo'];
        $pageNo=$_POST['pageNo'];
        $pageIdx=$_POST['pageIdx'];
        $catalogSize=$_POST['catalogSize'];

        $cnt=0;

        $queryChild="DELETE FROM TBL_CATALOG_CHILD WHERE PAGE_IDX = ".$pageIdx." ; ";
        $resultChild=mysql_query($queryChild, $conn);
        if($resultChild) $cnt+=1;

        $queryParent="DELETE FROM TBL_CATALOG_PARENT WHERE PAGE_IDX = ".$pageIdx." ; ";
        $resultParent=mysql_query($queryParent,$conn);
        if($resultParent) $cnt+=2;

        $queryParent2="UPDATE TBL_CATALOG_PARENT SET PAGE_NO = PAGE_NO-1 
                        WHERE PAGE_CATALOG = ".$catalogNo." AND PAGE_NO BETWEEN ".($pageNo+1)." AND ".($catalogSize-1)." ; ";
        $resultParent2=mysql_query($queryParent2,$conn);
        if($resultParent2) $cnt+=4;

        $queryTop = "UPDATE TBL_CATALOG_TOP SET PAGE_CNT = PAGE_CNT-1 WHERE CATALOG_NO = ".$catalogNo." ; ";
        $resultTop=mysql_query($queryTop,$conn);
        if($resultTop) $cnt+=8;

        echo $cnt;
    }

    if($mode=="DELETE_GOODS"){
        $goodIdx=$_POST['goodIdx'];
        $query="DELETE FROM TBL_CATALOG_CHILD WHERE GOODS_IDX =".$goodIdx." ; ";
        $result=mysql_query($query, $conn);
        if($result){
            echo 1;
        }
        else{
            echo 0;
        }
    }
    if($mode=="UPDATE_GOODS_INFO"){
        $goodIdx =  $_POST['goodIdx'];
        $goodName = $_POST['goodName'];
        $goodCode = $_POST['goodCode'];
        $price =    $_POST['price'];
        $dsc1 =     $_POST['dsc1'];
        $dsc2 =     $_POST['dsc2'];
        $dsc3 =     $_POST['dsc3'];
        $dsc4 =     $_POST['dsc4'];
        $dsc5 =     $_POST['dsc5'];
        $dsc6 =     $_POST['dsc6'];
        $dsc7 =     $_POST['dsc7']; 
        $multipleTF=$_POST['multipleTF'];  
        $saleState= $_POST['saleState'];
        $deliveryCntInBox=$_POST['deliveryCntInBox'];
        
        $goodNameC=SetStringToDB(iconv("utf8","euckr",$goodName));
        $dsc1C    =SetStringToDB(iconv("utf8","euckr",$dsc1));
        $dsc2C    =SetStringToDB(iconv("utf8","euckr",$dsc2));
        $dsc3C    =SetStringToDB(iconv("utf8","euckr",$dsc3));
        $dsc4C    =SetStringToDB(iconv("utf8","euckr",$dsc4));
        $dsc5C    =SetStringToDB(iconv("utf8","euckr",$dsc5));
        $dsc6C    =SetStringToDB(iconv("utf8","euckr",$dsc6));
        $dsc7C    =SetStringToDB(iconv("utf8","euckr",$dsc7));
        $saleState=SetStringToDB(iconv("utf8","euckr",$saleState));

        //echo iconv("euckr","utf8",$saleState);

        $query = "UPDATE TBL_CATALOG_CHILD SET GOODS_NAME = '".$goodNameC."', GOODS_CODE = '".$goodCode."', PRICE = ".$price.",
        GOODS_DSC1 = '".$dsc1C."', GOODS_DSC2 = '".$dsc2C."', GOODS_DSC3 = '".$dsc3C."', GOODS_DSC4 = '".$dsc4C."', GOODS_DSC5 = '".$dsc5C."'
        , GOODS_DSC6 = '".$dsc6C."', GOODS_DSC7 = '".$dsc7C."', MULTIPLE_TF = '".$multipleTF."', SALE_STATE = '".$saleState."', DELIVERY_CNT_IN_BOX = ".$deliveryCntInBox."
        WHERE GOODS_IDX = ".$goodIdx." ; ";

        $result=mysql_query($query,$conn);
        if($result){
            return 1;
        }
        else{
            return 0;
        }
    }
    if($mode=="ADD_GOODS_TO_DATABASE"){
    
        $catalogNo=$_POST['catalogNo'];
        $pageIdx=$_POST['pageIdx'];
        $goodsNo=$_POST['goodsNo'];
        $tdNum=$_POST['tdNum'];
        $pageHeight=$_POST['pageHeight'];
        $pageWidth=$_POST['pageWidth'];
        $goodHeight=$_POST['goodHeight'];
        $goodWidth=$_POST['goodWidth'];
        
        $goodCode=$_POST['goodCode'];
        $price=$_POST['price'];
        $filePath=$_POST['filePath'];
        $fileName=$_POST['fileName'];
        $goodName=$_POST['goodName'];
        $saleState=$_POST['saleState'];
        $deliveryCntInBox=$_POST['deliveryCntInBox'];

        $tdNum=str_replace("td","",$tdNum);
        $td=(int)$tdNum;

        $goodPosX=$td%$pageWidth;
        $goodPosY=(int)($td/$pageWidth);
         $goodNameC=SetStringToDB(iconv("utf8","euckr",$goodName));
        $saleStateC=SetStringToDB(iconv("utf8","euckr",$saleState));
        
        
        $query="INSERT INTO TBL_CATALOG_CHILD(
                        GOODS_NO,   CATALOG_NO, PAGE_IDX,    POS_X,      POS_Y, 
                        SIZE_X,     SIZE_Y,     GOODS_CODE, GOODS_NAME, FILE_PATH,
                        FILE_RNM,   PRICE,      DELIVERY_CNT_IN_BOX,    SALE_STATE, USE_TF)  
                    VALUES(
                       ".$goodsNo.", ".$catalogNo.", ".$pageIdx.", ".$goodPosX.", ".$goodPosY.",
                       ".$goodWidth.", ".$goodHeight.", '".$goodCode."', '".$goodNameC."', '".$filePath."', 
                       '".$fileName."', ".$price.", ".$deliveryCntInBox.", '".$saleStateC."', 'Y' ) ; ";

        //echo $query."<br>";
        $result=mysql_query($query, $conn);
        if($result){
            $query2="SELECT * FROM TBL_CATALOG_CHILD WHERE CATALOG_NO = ".$catalogNo." AND PAGE_IDX = ".$pageIdx." AND POS_X = ".$goodPosX." AND POS_Y = ".$goodPosY." ; ";
            $result2=mysql_query($query2, $conn);
            if($result2){
                $record=mysql_fetch_assoc($result2); 
                $record['GOODS_NAME']=urlencode(iconv("euckr","utf8",$record['GOODS_NAME']));
                $record['SALE_STATE']=urlencode(iconv("euckr","utf8",$record['SALE_STATE']));
                $record['GOODS_DSC1']=urlencode(iconv("euckr","utf8",$record['GOODS_DSC1']));
                $record['GOODS_DSC2']=urlencode(iconv("euckr","utf8",$record['GOODS_DSC2']));
                $record['GOODS_DSC3']=urlencode(iconv("euckr","utf8",$record['GOODS_DSC3']));
                $record['GOODS_DSC4']=urlencode(iconv("euckr","utf8",$record['GOODS_DSC4']));
                $record['GOODS_DSC5']=urlencode(iconv("euckr","utf8",$record['GOODS_DSC5']));
                $record['GOODS_DSC6']=urlencode(iconv("euckr","utf8",$record['GOODS_DSC6']));
                $record['GOODS_DSC7']=urlencode(iconv("euckr","utf8",$record['GOODS_DSC7']));

                $arrJson=json_encode($record);
                $rets=urldecode($arrJson);

                echo $rets;
            }
            else{
                echo 0;
            }

        }
        else{
            // echo "  $goodsNo<br>
            //         $catalogNo<br>
            //         $pageIdx<br>
            //         $goodPosX<br>
            //         $goodPosY<br>
            //         $goodWidth<br>
            //         $goodHeight<br>
            //         $goodCode<br>
            //         $goodNameC<br>
            //         $filePath<br>
            //         $fileName<br>
            //         $price<br>
            //         $deliveryCntInBox<br>
            //         $saleStateC<br>";
            echo -1;
        }

    }

    if($mode=="DELETE_CATALOG"){
        $cnt=0;
        $catalogNo=$_POST['catalogNo'];
        $queryT = "DELETE FROM TBL_CATALOG_TOP WHERE CATALOG_NO = ".$catalogNo." ; ";
        $resultT=mysql_query($queryT,$conn);
        if($resultT) $cnt+=1;

        $queryP = "DELETE FROM TBL_CATALOG_PARENT WHERE PAGE_CATALOG = ".$catalogNo." ; ";
        $resultP=mysql_query($queryP,$conn);
        if($resultP) $cnt+=2;

        $queryC = "DELETE FROM TBL_CATALOG_CHILD WHERE CATALOG_NO = ".$catalogNo." ; ";
        $resultC=mysql_query($queryC,$conn);
        if($resultC) $cnt+=4;

        echo $cnt;
    }


    if($mode=="SEARCH_GOODS_INFO"){
        $sltGoodsCategory=$_POST['sltGoodsCategory'];
        $content=$_POST['content'];
            $query="SELECT GOODS_NO, GOODS_CODE, GOODS_NAME, CATE_04, FILE_PATH_150, FILE_RNM_150, SALE_PRICE, DELIVERY_CNT_IN_BOX FROM TBL_GOODS ";
        if($sltGoodsCategory=="opGoodsNo"){
            //GoodsNo�� �˻�
            $query.=" WHERE GOODS_NO=".$content." ";
        }
        else if($sltGoodsCategory=="opGoodsCode"){
            //GoodsCode�� �˻�
            $query.=" WHERE GOODS_CODE LIKE '%".$content."%' ";

        }
        else if($sltGoodsCategory=="opGoodsName"){
            //GoodsName���� �˻�
            $content=iconv("utf8","euckr",$content);
            $query.=" WHERE GOODS_NAME LIKE '%".$content."%' ";
        }
        else{
            //echo 0;
            return;
        }
        $query.=" AND USE_TF='Y' AND DEL_TF='N' ; ";


        $result=mysql_query($query, $conn);
        $cnt=mysql_num_rows($result);
        $record=array();

        if($cnt>0){
            for($i=0;$i<$cnt;$i++){
                $record[$i]=mysql_fetch_assoc($result);
                $record[$i]['GOODS_NAME']=urlencode(iconv("euckr","utf8",$record[$i]['GOODS_NAME']));
                $record[$i]['CATE_04']=urlencode(iconv("euckr","utf8",$record[$i]['CATE_04']));
                    //$record[$i]['GOODS_NAME']=urlencode(iconv("euckr","utf8",$record[$i]['GOODS_NAME']));    
            }
        }
        
        $arrJson=json_encode($record);
        $rets=urldecode($arrJson);
        echo $rets;
    }

    if($mode=="VIEW_PAGE"){
        $catalogNo=$_POST['catalogNo'];
        $pageNo=$_POST['pageNo'];

        $query="SELECT PAGE_IDX, PAGE_SIZE_X, PAGE_SIZE_Y FROM TBL_CATALOG_PARENT WHERE PAGE_CATALOG = ".$catalogNo." AND PAGE_NO = ".$pageNo." ; ";
        $result=mysql_query($query,$conn);
        $cnt=mysql_num_rows($result);
        $record=array();
        $jsonPage="";
        if($cnt>0){
            $record[0]=mysql_fetch_assoc($result);
            $objPage=new CPage($record[0]['PAGE_IDX'], $record[0]['PAGE_SIZE_X'], $record[0]['PAGE_SIZE_Y']);
            $arrPage=get_object_vars($objPage);
            $jsonPage=json_encode($arrPage);
            echo $jsonPage;
        }
    }
    if($mode=="GET_GOODS_INFO"){

        $pageIdx=$_POST['pageIdx'];

        $query="SELECT * FROM TBL_CATALOG_CHILD 
                WHERE PAGE_IDX = ".$pageIdx." ; ";

            $result=mysql_query($query,$conn);
            $cnt=mysql_num_rows($result);
            $record=array();

            if($cnt>0){
                for($i=0;$i<$cnt;$i++){
                    $record[$i]=mysql_fetch_assoc($result);
                    $record[$i]['GOODS_NAME']=urlencode(iconv("euckr","utf8",$record[$i]['GOODS_NAME']));
                    $record[$i]['SALE_STATE']=urlencode(iconv("euckr","utf8",$record[$i]['SALE_STATE']));
                    $record[$i]['GOODS_DSC1']=urlencode(iconv("euckr","utf8",$record[$i]['GOODS_DSC1']));
                    $record[$i]['GOODS_DSC2']=urlencode(iconv("euckr","utf8",$record[$i]['GOODS_DSC2']));
                    $record[$i]['GOODS_DSC3']=urlencode(iconv("euckr","utf8",$record[$i]['GOODS_DSC3']));
                    $record[$i]['GOODS_DSC4']=urlencode(iconv("euckr","utf8",$record[$i]['GOODS_DSC4']));
                    $record[$i]['GOODS_DSC5']=urlencode(iconv("euckr","utf8",$record[$i]['GOODS_DSC5']));
                    $record[$i]['GOODS_DSC6']=urlencode(iconv("euckr","utf8",$record[$i]['GOODS_DSC6']));
                    $record[$i]['GOODS_DSC7']=urlencode(iconv("euckr","utf8",$record[$i]['GOODS_DSC7']));
                }
            }
            $arrJson=json_encode($record);
            $rets=urldecode($arrJson);
            echo $rets;
        //������ �ٽ� �����ؾ� �Ѵ�.

    }

    if($mode=="ADD_PAGE"){
        $catalogNo=$_POST['catalogNo'];
        $pageNo=$_POST['pageNo'];
        $pageWidth=$_POST['pageWidth'];
        $pageHeight=$_POST['pageHeight'];
        $location=$_POST['location'];
        $rs=acAddPage($conn,$catalogNo, $pageNo,$pageWidth, $pageHeight,$location);
        //echo "first location is $location<br>";
        if($rs>=0){
            $query="SELECT PAGE_IDX FROM TBL_CATALOG_PARENT WHERE PAGE_CATALOG = ".$catalogNo." AND PAGE_NO = ".$location." ; ";
            $result=mysql_query($query,$conn);
            $record=mysql_fetch_assoc($result);
            $pageIdx=$record['PAGE_IDX'];

            echo $pageIdx; //acAddPage()�� ���������� �����
        }
        else{
            echo -1;//acAddPage()�� ������������ �����
        }
        //�̰� success�� ���� ��� ������ table�� ��� �ٲ��ش�.
    }

    if($mode=="VIEW_CATALOG"){
        $catalogNo = $_POST['catalogNo'];
        $pageCnt=acGetCatalogPagesWidthNo($conn, $catalogNo);

        if($pageCnt==-1){//DB ����
            echo -1;// DB error �޽����� ����Ѵ�.
        }
        else if($pageCnt==0){//�������� �ϳ��� ���������� ���� ���
            echo 0;//�������� �����϶�� �޽����� �Բ� ������ ������ư�� �����ش�.
        }
        else{//�������� 1���̻� �����ϴ� ���
            echo $pageCnt;//0�������� �����ִ� ������ ������ش�.
        }

    }
    if($mode=="ADD_CATALOG"){
        $catalogName=$_POST['catalogName'];
        $rs=acCreateNewCatalog($conn, $catalogName);
        
        if($rs==0) echo iconv("euckr","utf8",$catalogName);//$catalog������ ������ success�� �� ���� "0"�̷� ������ ������ success�� �� �� ����??
        //if($rs==0) echo $catalogName;
        else if($rs==-1) echo "-1";
        else echo "1";
    }

?>