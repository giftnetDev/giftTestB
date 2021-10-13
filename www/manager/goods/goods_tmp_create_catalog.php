<?
   	error_reporting(-1);
    ini_set('display_errors', 'On');
class CPage{
    private $width;
    private $height;
    private $chk;
    public function __construct(){
        $this->chk= array();
    }
    // public function __destruct()
    // {
    //     echo "해당 클래스 삭제<br/>";
    // }

    public function DeletePage(){
        $this->__destruct();
    }
    public function GetPageSize(){
        $size=array($this->width, $this->height);
        return $size;
    }
    public function SetPageSize($width, $height){
        $this->width=$width;
        $this->height=$height;
        for($y=0;$y<$height;$y++){
            for($x=0;$x<$width;$x++){
                $this->chk[$y][$x]=0;
            }
        }
    }
}

?>

<?
    require "../../_classes/com/db/DBUtil.php";
    $tmpCatalogSeq=array();
    $conn=db_connection("w");
    $mode="NORMAL";
    echo $mode." mode<br/>";
    if($mode=="INPUT"){
        echo $mode."<br/>";
        echo "Catalog Name is : ".$txtCatalogName."<br/>";
        $rs=CreateCatalog($txtCatalogName);
        if($rs==0){
            echo"DB SUCCESS<br/>";
            //정상처리
        }
        else if($rs==1){
            //중복처리
            echo"데이터 중복입니다<br/>";
        }
        else{
            //DB 오류 처리
            echo"데이터베이스 오류입니다<br/>";
    
        }
        $mode="NORMAL";
    
    }

?>

<!DOCTYPE html>
<HTML>
    <HEAD>
        <script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
        <script>
            function add_catalog(){
                var frm=document.getElementById('frmAddCatalog');
                var txtCatalogName=frm.txtCatalogName;

                var allData={"txtCatalogName":txtCatalogName.value, "mode":"ADD_CATALOG"};
                alert('넘어옴, txtCatalogName is '+txtCatalogName.value);
                $.ajax({
                    url: '/manager/ajax_processing.php',
                    dataType: 'json',
                    type : 'POST',
                    data : allData,

                   success:function(data){
                        console.log(data);
                   },
                   error: function(jqXHR,textStatus,errorThrown){
                        console.log(jqXHR.responseText);
                    }
                });   
            }   

            function view_catalog(catalog){
                var array=catalog;
                alert(array);
                var datas={'mode':"VIEW_CATALOG",'catalogNo':$('#sltCatalog option:checked').val()};
                $.ajax({
                    url:'/manager/ajax_processing.php',
                    dataType:'json',
                    type:'POST',
                    data:datas,
                    success:function(data){
                        console.log(data);
                    },
                    error:function(jqXHR, textStatus, errorThrown){
                        console.log(jqXHR.responseText);
                    }
                });    
            }
        </script>
    </HEAD>
    
    <BODY>
        <div name="dvAddCatalog", id="dvAddCatalog">

               <form name="frmAddCatalog" id="frmAddCatalog">
                    <input type="text" name="txtCatalogName" id="txtCatalogName" placeholder ="Catalog Name">
                    <input type="hidden" name="mode" value="">
                    <input type="button" name="" value="카달로그 생성2" onclick="javascript:add_catalog()" />
                </form>


        </div>
        <div>
            <?SetAllGoodsInfo($cPage,$tmpCatalogSeq);?>
            <?
                $catalogCnt=sizeof($tmpCatalogSeq);
                echo "-----catalogCnt is : ".$catalogCnt."-------<br/>";
                for($i=0;$i<$catalogCnt;$i++){
                    echo $i."is ".$tmpCatalogSeq[$i]."<br/>";
                }

            ?>
        </div>
        <div>
            <form name="sltCatalog" id="sltCatalog" method="POST">
            <?CreateCatalogSelectBox();?>
            <input type="button" name="btnViewCatalog" value="카탈로그 보기" onclick="javascript:view_catalog(<?=$tmpCatalogSeq?>)" />
            </form>
        </div>




        <div>
                <!--카탈로그 선택시 해당 카탈로그에 대한 정보가 나오는 공간 만약 카탈로그의 MaxPage가 0이면 Page생성하라고 뜨고 Maxpage가 0보다 크면 1Page정보를 보여준다.-->
        </div>

        <div>
                <from name="tmpForm", id="tmpForm" method="POST">
                    <input type=text name="texReserveNo" id="txtReserveNo">
                    <input type="button" name="btnReserveNo" id="btnReserveNo">
                    <input type="hidden" name="tmpMode" id="tmpMode" value="">
                </form>
                <!--카탈로그 선택시 해당 카탈로그에 대한 정보가 나오는 공간 만약 카탈로그의 MaxPage가 0이면 Page생성하라고 뜨고 Maxpage가 0보다 크면 1Page정보를 보여준다.-->
        </div>
    </BODY>
</HTML>

<?

    function CreateCatalog($txtCatalogName){
        $query="SELECT CATALOG_NAME FROM TBL_CATALOG_TOP WHERE CATALOG_NAME= '".$txtCatalogName."' ; ";
        echo $query."<br>";
        $result=mysql_query($query);
        $cnt=mysql_num_rows($result);
        if($cnt>0) return 1;
        else{
            $query2="INSERT INTO TBL_CATALOG_TOP(CATALOG_NAME) VALUES('".$txtCatalogName."'); ";
            $result=mysql_query($query2);
            if(!$result){
                return -1;
            }
            else{
                return  0;
            }
        }
    }

    function CreateCatalogSelectBox(){
        $query="SELECT CATALOG_NO, CATALOG_NAME, PAGE_CNT FROM TBL_CATALOG_TOP; ";
        $result=mysql_query($query);
        echo "result of Query is : ".$result."<br/>";

        $cnt=mysql_num_rows($result);

        $record=array();

        if($result<>""){
            for($i=0;$i<$cnt;$i++){
                $record[$i]=mysql_fetch_assoc($result);
            }
            echo "<SELECT name=\"sltCatalog\" id=\"sltCatalog\">";
            echo "<OPTION value=\"\">카탈로그 선택</OPTION>";
            for($i=0;$i<$cnt;$i++){
                echo "<OPTION  value=\"".$i."\">".$record[$i]['CATALOG_NAME']."</OPTION>";    
            }
            echo"</SELECT>";
        }

    }
    function SetAllGoodsInfo(&$cPage, &$tmpCatalogSeq){

        //-----------------------(1)---cPage[catalog][page] 형태의 객체 생성------------------------------
        $queryTop="SELECT PAGE_CNT, CATALOG_NO, CATALOG_NAME FROM TBL_CATALOG_TOP; ";
        $rsTop=mysql_query($queryTop);
        $cntTop=mysql_num_rows($rsTop);
        $recordTop= array();
        if($rsTop<>""){
            for($i=0;$i<$cntTop;$i++){
                $recordTop[$i]=mysql_fetch_assoc($rsTop);
                $tmpCatalogSeq[$i]=$recordTop[$i]['CATALOG_NO'];//임시 배열을 이용해서 catalog에 접근할 수 있도록 한다.
                for($j=0;$j<$recordTop[$i]['PAGE_CNT'];$j++){
                    $cPage[$i][$j]= new CPage();

                }
            }
        }
        //----------------------------(1) 종료------------------------------------------------------------

        //-----------------(2) cPage[catalog][page]에 해당하는 page의 사이즈를 구성하고 기본정보를 매칭해 주는 코드-----------------
        $queryParent="SELECT PAGE_CATALOG, PAGE_NO, PAGE_SIZE_X, PAGE_SIZE_Y FROM TBL_CATALOG_PARENT; ";
        $rsParent=mysql_query($queryParent);
        $cntParent=mysql_num_rows($rsParent);
        $recordParent=array();
        if($rsParent<>""){
            for($i=0;$i<$cntParent;$i++){
                $recordParent[$i]=mysql_fetch_assoc($rsParent);
            }
            for($i=0;$i<$cntParent;$i++){
                //연속된 배열에 불연속인 값을 넣어놓는다.
                $cPage[$tmpCatalogSeq[$i]][$recordParent[$i]['PAGE_NO']]->SetPageSize($recordParent[$i]['PAGE_SIZE_X'],$recordParent[$i]['PAGE_SIZE_Y']);
            }

        }
        //---------------------(2) 종료------------------------------------------------------------------------------------------

    }

?>