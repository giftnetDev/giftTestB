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
            //���ε��� PHP������ �о�´�.
            $objPHPExcel=PHPExcel_IOFactory::load($fileName);

            //���ε��� Excel������ �� Sheet���� �ľ��Ѵ�.
            $sheetCount=$objPHPExcel->getSheetCount();
            echo $sheetCount;

            for($sheet=0;$sheet<$sheetCount;$sheet++){ ////�� sheet�� �ִ� ������� �о�´�.
                
                //�ش� $sheet�� Active�� ���·� �����.
                $objPHPExcel->setActiveSheetIndex($sheet);
                $activeSheet=$objPHPExcel->getActiveSheet();

                $lastRow=$activeSheet->getHighestRow();     //������ ���� ������
                $lastCol=$activeSheet->getHighestColumn();  //������ ���� ������
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
        WHERE (CP_NM LIKE  '%����%'
        OR CP_NM LIKE  '%�������%'
        OR CP_NM LIKE  '%����%'
        OR CP_NM LIKE  '%���%'
        OR CP_NM LIKE  '%����%'
        OR CP_NM LIKE  '%����%'
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
                    <th style="background-color:#DCDCDC">����</th>
                    <td><input type="file" name="excelFile"/></td>
                </tr>
                <tr>
                    <th style="background-color:#DCDCDC">���</th>
                    <td style="text-align:center;"><input type="button" value="���ε�" onclick="js_read();"/></td>
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