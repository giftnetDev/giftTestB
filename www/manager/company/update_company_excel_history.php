<?
     error_reporting(-1);
     ini_set('display_errors', 'On');

     require "../../_classes/com/db/DBUtil.php";
 
     $conn=db_connection("w");

     require_once "../../_PHPExcel/Classes/PHPExcel.php";

     $objPHPExcel=new PHPExcel();
     require_once "../../_PHPExcel/Classes/PHPExcel/IOFactory.php";

     $filePath="../../_tmpExcel/";
     $fileNM="test2.xls";

     $fileName=$filePath.$fileNM;
     //echo "file Name is ".$fileName."<br/>";

     try{
          $objReader=PHPExcel_IOFactory::createReaderForFile($fileName);
          $objReader->setReadDataOnly(true);
          $objExcel=$objReader->load($fileName);

          $objExcel->setActiveSheetIndex(0);
          $objWorksheet=$objExcel->getActiveSheet();

          $objPHPExcel->getProperties()->setTitle('Temp');

          $currSheet=$objPHPExcel->getActiveSheet(); //현재 사용할 sheet로 currSheet을 사용할 것인데 이것은 objPHPExcel->getActivesheet();에서 사용하겠다

          //$objWorksheet  -> readExcelObj
          //$currSheet     -> writeExcelObj

          ?>
          <table>
          <?
          $cp_nos="";
          for($i=2;$i<=41 ; $i++){
               $cpNo          =$objWorksheet->getCell('A'.$i)->getValue();
               $cpName        = iconv("utf8","euckr",$objWorksheet->getCell('B'.$i)->getValue());
               $cpRprstPhone  =$objWorksheet->getCell('C'.$i)->getValue();
               $cpAddr        = iconv("utf8","euckr",$objWorksheet->getCell('D'.$i)->getValue());
               $cpManager     = iconv("utf8","euckr",$objWorksheet->getCell('E'.$i)->getValue());
               $cpPhone       =$objWorksheet->getCell('F'.$i)->getValue();
               $cpCPhone      =$objWorksheet->getCell('G'.$i)->getValue();
               $cpZip         =$objWorksheet->getCell('H'.$i)->getValue();
               $cp_nos .= "'".$cpNo."',";
               // echo "<tr>
               //                <th>cpNo</th><td>$cpNo</td>
               //                <th>cpName</th><td>$cpName</td>
               //                <th>cpRprstPhone</th><td>$cpRprstPhone</td>
               //                <th>cpAddr</th><td>$cpAddr</td>
               //                <th>cpManager</th><td>$cpManager</td>
               //                <th>cpPhone</th><td>$cpPhone</td>
               //                <th>cpCPhone</th><td>$cpCPhone</td>
               //                <th>cpZip</th><td>$cpZip</td>
               //           </tr>";

               //UpdateCompanyInfo($conn, $cpNo, $cpName, $cpRprstPhone, $cpAddr, $cpManager, $cpPhone, $cpCPhone, $cpZip);
               // GetCompanyNm2($conn,$cpNo, $i);
          }
          $cp_nos = rtrim($cp_nos,",");
          // echo "cp_nos : ".$cp_nos;
          $query ="
               select * from TBL_COMPANY where cp_no in ($cp_nos)
          ";

		//echo $query;
		$result = mysql_query($query,$conn);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}

          echo count($record);
          // for($i=0;$i < count($record);$i++) {
          //      $CP_NO = $record[$i]["CP_NO"];
          //      $CP_NM = $record[$i]["CP_NM "];
          //      $CP_NM2 = $record[$i]["CP_NM2"];
          //      $CP_ADDR = $record[$i]["CP_ADDR"];
          //      $MANAGER_NM = $record[$i]["MANAGER_NM"];
          //      $PHONE = $record[$i]["PHONE"];
          //      $HPHONE = $record[$i]["HPHONE"];
          // }
          ?>
          </table>
          <style>
               th, td{
                    border:solid 1px #000000;
               }
          </style>
          <?
     }
     catch(exception $e){
          echo "엑셀파일을 읽는 도중 오류가 발생하였습니다.";
     }


     
?>
<?
     function UpdateCompanyInfo($db, $cpNo, $cpName, $cpRprstPhone, $cpAddr, $cpManager, $cpPhone, $cpCPhone, $cpZip){
          $query="UPDATE TBL_COMPANY SET CP_NM = '".$cpName."', CP_PHONE ='".$cpRprstPhone."', CP_ADDR = '".$cpAddr."', MANAGER_NM = '".$cpManager."', PHONE = '".$cpPhone."', HPHONE = '".$cpCPhone."', CP_ZIP = '".$cpZip."' 
          WHERE CP_NO = ".$cpNo." AND USE_TF = 'Y' AND DEL_TF = 'N' ; ";
          $result=mysql_query($query, $db);
          if($result <> ""){

          }
     }
     function GetCompanyNm2($db, $cpNo, $i){
          $query="SELECT CP_NM2 FROM TBL_COMPANY WHERE CP_NO = ".$cpNo." AND CP_NM2 <> \"\" AND USE_TF ='Y' AND DEL_TF = 'N' ; ";
          $result=mysql_query($query, $db);
          
          $cnt=mysql_num_rows($result);
          $record = array();
          if($cnt>0){
               $cpNm2=$record[0]['CP_NM2'];
               echo $cpNo."<br/>";

          }
     }
?>
<!-- <!DOCTYPE HTML>
<html>
     <head>

     </head>
     <body>
          <form>
               <label for="ex_file">업로드</label>
               <input type='file' name='flCpExcel' id='flCpExcel'/>

          </form>
     </body>
</html> -->
