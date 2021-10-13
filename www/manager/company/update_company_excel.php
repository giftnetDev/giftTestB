<?
     error_reporting(-1);
     ini_set('display_errors', 'On');

     require "../../_classes/com/db/DBUtil.php";
 
     $conn=db_connection("w");

     require_once "../../_PHPExcel/Classes/PHPExcel.php";

     $objPHPExcel=new PHPExcel();
     require_once "../../_PHPExcel/Classes/PHPExcel/IOFactory.php";

     $filePath="../../_tmpExcel/";
     $fileNM="test.xls";

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


          for($i=2;$i<=3496 ; $i++){
               $cpNo          =$objWorksheet->getCell('A'.$i)->getValue();
               $cpName        =$objWorksheet->getCell('B'.$i)->getValue();
               $cpRprstPhone  =$objWorksheet->getCell('C'.$i)->getValue();
               $cpAddr        =$objWorksheet->getCell('D'.$i)->getValue();
               $cpManager     =$objWorksheet->getCell('E'.$i)->getValue();
               $cpPhone       =$objWorksheet->getCell('F'.$i)->getValue();
               $cpCPhone      =$objWorksheet->getCell('G'.$i)->getValue();
               $cpZip         =$objWorksheet->getCell('H'.$i)->getValue();

               //UpdateCompanyInfo($conn, $cpNo, $cpName, $cpRprstPhone, $cpAddr, $cpManager, $cpPhone, $cpCPhone, $cpZip);
               GetCompanyNm2($conn,$cpNo, $i);
          }
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
