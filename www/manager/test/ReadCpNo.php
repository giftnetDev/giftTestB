<?
    require "../../_classes/com/db/DBUtil.php";
    require "../../_classes/com/util/Util.php";
    $conn = db_connection("w");

    if($mode=="FR"){
        require_once "../../_PHPExcel/Classes/PHPExcel.php";

        $objPHPExcel=new PHPExcel();

        $allData=array();

        $filePath=$_SERVER[DOCUMENT_ROOT]."/_tmpExcel/";

        // echo $filePath."<br>";
        // exit;

        $fileName=upload($_FILES[excelFile],$filePath, 10000, array('xls'));

        $fileName=$filePath.$fileName;

        // echo $fileName."<br>";
        //exit;

        $cpNos="";

        try{
            //업로드한 PHP파일을 읽어온다.
            $objPHPExcel=PHPExcel_IOFactory::load($fileName);

            //업로드한 Excel파일의 총 Sheet수를 파악한다.
            $sheetCount=$objPHPExcel->getSheetCount();
            echo $sheetCount;

            for($sheet=0;$sheet<$sheetCount;$sheet++){ ////각 sheet에 있는 내용들을 읽어온다.
                
                //해당 $sheet를 Active한 상태로 만든다.
                $objPHPExcel->setActiveSheetIndex($sheet);
                $activeSheet=$objPHPExcel->getActiveSheet();

                $lastRow=$activeSheet->getHighestRow();     //마지막 행을 가져옴
                $lastCol=$activeSheet->getHighestColumn();  //마지막 열을 가져옴
                echo "<br>";
                echo "$lastRow<br>$lastCol<br>";

                for($row = 2; $row<=$lastRow;$row++){
                    $cpNo=$activeSheet->getCell("A".$row);

                    if($row<$lastRow) $cpNos= $cpNos.$cpNo.", ";
                    else $cpNos=$cpNos.$cpNo." ";
                }

            }
            // echo "<br><br>";

            // echo $cpNos."<br>";
            // exit;
            // require_once "../../_PHPExcel/Classes/PHPExcel/IOFactory.php";

            // $downloadExcelObj=new PHPExcel();
            // $downloadExcelObj->getProperties()->setTitle("Tmp");

            // $currSheet=$downloadExcelObj->getActiveSheet();
            // $currSheet->setTitle("1");



            $rsCpInf=dbProcess($conn, $cpNos);



        }
        catch(exception $e){
            echo $e;
        }
    }
    
?>
<?
    function dbProcess($db, $str){
        $query="SELECT CP_NM, CP_PHONE, CP_ADDR, MANAGER_NM, PHONE, HPHONE, CP_ZIP
        FROM  `TBL_COMPANY` 
        WHERE (CP_NM LIKE  '%농협%'
        OR CP_NM LIKE  '%농업협동%'
        OR CP_NM LIKE  '%축협%'
        OR CP_NM LIKE  '%축산%'
        OR CP_NM LIKE  '%생명%'
        OR CP_NM LIKE  '%손해%'
        OR CP_NM LIKE  '%NH%')
        AND CP_NO NOT IN( ".$str." ) ; ";

        echo $query;
        exit;

        $result = mysql_query($query, $db);
        $record= array();
        if($result){
            $cnt=mysql_num_rows($result);
            for($i=0;$i<$cnt;$i++){
                $record[$i]=mysql_fetch_assoc($result);
            }
        }



    }



?>

<html>
    <head>
        <title></title>
    </head>
    <body>
        <form name="frm" enctype="multipart/form-data" method="post">
            <input type="hidden" name="mode" value=""/>
            <table>
                <tr>
                    <th style="background-color:#DCDCDC">파일</th>
                    <td><input type="file" name="excelFile"/></td>
                </tr>
                <tr>
                    <th style="background-color:#DCDCDC">등록</th>
                    <td style="text-align:center;"><input type="button" value="업로드" onclick="js_read();"/></td>
                </tr>
            </table>
        </form>

    </body>
</html>

<script>
    function js_read(){
        var frm=document.frm;

        frm.mode.value="FR";
        frm.action="<?=$_SERVER[PHP_SELF]?>";
        frm.submit();


    }
</script>