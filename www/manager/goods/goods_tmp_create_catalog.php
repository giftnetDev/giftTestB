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
    //     echo "�ش� Ŭ���� ����<br/>";
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
            //����ó��
        }
        else if($rs==1){
            //�ߺ�ó��
            echo"������ �ߺ��Դϴ�<br/>";
        }
        else{
            //DB ���� ó��
            echo"�����ͺ��̽� �����Դϴ�<br/>";
    
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
                alert('�Ѿ��, txtCatalogName is '+txtCatalogName.value);
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
                    <input type="button" name="" value="ī�޷α� ����2" onclick="javascript:add_catalog()" />
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
            <input type="button" name="btnViewCatalog" value="īŻ�α� ����" onclick="javascript:view_catalog(<?=$tmpCatalogSeq?>)" />
            </form>
        </div>




        <div>
                <!--īŻ�α� ���ý� �ش� īŻ�α׿� ���� ������ ������ ���� ���� īŻ�α��� MaxPage�� 0�̸� Page�����϶�� �߰� Maxpage�� 0���� ũ�� 1Page������ �����ش�.-->
        </div>

        <div>
                <from name="tmpForm", id="tmpForm" method="POST">
                    <input type=text name="texReserveNo" id="txtReserveNo">
                    <input type="button" name="btnReserveNo" id="btnReserveNo">
                    <input type="hidden" name="tmpMode" id="tmpMode" value="">
                </form>
                <!--īŻ�α� ���ý� �ش� īŻ�α׿� ���� ������ ������ ���� ���� īŻ�α��� MaxPage�� 0�̸� Page�����϶�� �߰� Maxpage�� 0���� ũ�� 1Page������ �����ش�.-->
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
            echo "<OPTION value=\"\">īŻ�α� ����</OPTION>";
            for($i=0;$i<$cnt;$i++){
                echo "<OPTION  value=\"".$i."\">".$record[$i]['CATALOG_NAME']."</OPTION>";    
            }
            echo"</SELECT>";
        }

    }
    function SetAllGoodsInfo(&$cPage, &$tmpCatalogSeq){

        //-----------------------(1)---cPage[catalog][page] ������ ��ü ����------------------------------
        $queryTop="SELECT PAGE_CNT, CATALOG_NO, CATALOG_NAME FROM TBL_CATALOG_TOP; ";
        $rsTop=mysql_query($queryTop);
        $cntTop=mysql_num_rows($rsTop);
        $recordTop= array();
        if($rsTop<>""){
            for($i=0;$i<$cntTop;$i++){
                $recordTop[$i]=mysql_fetch_assoc($rsTop);
                $tmpCatalogSeq[$i]=$recordTop[$i]['CATALOG_NO'];//�ӽ� �迭�� �̿��ؼ� catalog�� ������ �� �ֵ��� �Ѵ�.
                for($j=0;$j<$recordTop[$i]['PAGE_CNT'];$j++){
                    $cPage[$i][$j]= new CPage();

                }
            }
        }
        //----------------------------(1) ����------------------------------------------------------------

        //-----------------(2) cPage[catalog][page]�� �ش��ϴ� page�� ����� �����ϰ� �⺻������ ��Ī�� �ִ� �ڵ�-----------------
        $queryParent="SELECT PAGE_CATALOG, PAGE_NO, PAGE_SIZE_X, PAGE_SIZE_Y FROM TBL_CATALOG_PARENT; ";
        $rsParent=mysql_query($queryParent);
        $cntParent=mysql_num_rows($rsParent);
        $recordParent=array();
        if($rsParent<>""){
            for($i=0;$i<$cntParent;$i++){
                $recordParent[$i]=mysql_fetch_assoc($rsParent);
            }
            for($i=0;$i<$cntParent;$i++){
                //���ӵ� �迭�� �ҿ����� ���� �־���´�.
                $cPage[$tmpCatalogSeq[$i]][$recordParent[$i]['PAGE_NO']]->SetPageSize($recordParent[$i]['PAGE_SIZE_X'],$recordParent[$i]['PAGE_SIZE_Y']);
            }

        }
        //---------------------(2) ����------------------------------------------------------------------------------------------

    }

?>