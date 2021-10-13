<?
    class CPolygon{
        public $name;
        public $degree;
        public $slide;
        public function __construct($name, $degree, $slide)
        {
            $this->name=$name;
            $this->degree=$degree;
            $this->slide=$slide;
        }
    }
?>
<?
    require "../../_classes/com/db/DBUtil.php";
    $conn=db_connection("w");
?>
<?
    $polygons=array();
    for($i=3;$i<7;$i++){
        $angle=(180*($i-2))/$i;
        $name="".$i."각형";
        $slide=$i;
        $polygons[$i-3]=new CPolygon($name,$angle,$slide);
    }
    $query="SELECT * FROM TBL_CATALOG_TOP ; ";
    $result=mysql_query($query,$conn);
    $cnt=mysql_num_rows($result);
    $record=array();
    if($cnt>0){
        for($i=0;$i<$cnt;$i++){
            $record[$i]=mysql_fetch_assoc($result);
        }
    }
    print_r($record);
    echo"<br/><br/><br/>";

    $arrR=json_encode($record);
    print_r($arrR);
    // $arrRs=array();
    // for($i=0;$i<$cnt;$i++){
    //     $arrTmp=$record[$i];
    //     $arrRs[$i]=get_object_vars()
    // }
    

    // print_r($polygons);
    // echo"<br/><br/>";
    // var_dump($polygons);
    // echo"<br/><br/>get_object_vars()후에 나오는 형태<br/>";

    // $arrPoly1=$polygons[0];
    // print_r($arrPoly1);
    // echo"<br/>";
    // var_dump($arrPoly1);
    // echo"<br/><br/><br/>";
    // echo"get_object_vars()후에 나오는 형태<br/>";
    // $tArrPoly=get_object_vars($arrPoly1);
    // print_r($tArrPoly);
    // echo"<br/>";
    // var_dump($tArrPoly);
    // echo"<br/>";

    // $arrPoly=get_object_vars($polygons[0]);
    // print_r($arrPoly);

?>