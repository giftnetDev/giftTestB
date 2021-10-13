<? session_start(); ?>
<?

#====================================================================
# DB Include, DB Connection
#====================================================================
require "../../_classes/com/db/DBUtil.php";

$conn = db_connection("w");

#====================================================================
# common_header Check Session
#====================================================================
//require "../../_common/common_header.php"; 

#=====================================================================
# common function, login_function
#=====================================================================
// require "../../_common/config.php";
// require "../../_classes/com/util/Util.php";
// require "../../_classes/com/util/ImgUtil.php";
// require "../../_classes/com/etc/etc.php";
// require "../../_classes/biz/goods/goods.php";
// require "../../_classes/biz/stock/stock.php";
// require "../../_classes/biz/company/company.php";
// require "../../_classes/biz/work/work.php";
// require "../../_classes/biz/admin/admin.php";
// require "../../_classes/biz/order/order.php";

    function selectCompanyByFilter($db, $range, $index){
        $query="SELECT  CP_NO, CP_NM, CP_ADDR, CP_PHONE, MANAGER_NM, CP_ZIP
                FROM    TBL_COMPANY
                WHERE   CP_CATE LIKE '3005%'
                AND     DEL_TF='N'
                AND     USE_TF='Y'
                AND     CATALOG_SEND_TF='Y'
                ";

        if($range=='L'){
            $query.=" AND SALE_ADM_NO NOT IN('60','56','64','7')";

            switch($index){
                case 1:
                {//서울
                    $query.=" AND  CP_PHONE LIKE '02%' ";
                    break;
                }
                case 2:
                {//경기
                    $query.=" AND  CP_PHONE LIKE '031%' ";
                    break;
                }
                case 3:
                {//인천
                    $query.=" AND  CP_PHONE LIKE '032%' ";
                    break;
                }
                case 4:
                {//충북
                    $query.=" AND  CP_PHONE LIKE '043%' ";
                    break;
                }
                case 5:
                {//충남,대전,세종
                    $query.=" AND  (CP_PHONE LIKE '041%' OR CP_PHONE LIKE '042%' OR CP_PHONE LIKE '044%') ";
                    break;
                }
                case 6:
                {//전북
                    $query.=" AND  CP_PHONE LIKE '063%'  ";
                    break;
                }
                case 7:
                {//전남, 광주
                    $query.=" AND  (CP_PHONE LIKE '061%' OR CP_PHONE LIKE '062%') ";
                    break;
                }
                case 8:
                {//경북 대구
                    $query.=" AND  (CP_PHONE LIKE '053%' OR CP_PHONE LIKE '054%') ";
                    break;
                }
                case 9:
                {//경남, 울산, 부산
                    $query.=" AND  (CP_PHONE LIKE '055%' OR CP_PHONE LIKE '052%' OR CP_PHONE LIKE '051%') ";
                    break;
                }
                case 10:
                {
                    $query.=" AND CP_PHONE LIKE '064%' ";
                    break;
                }
                default:{
                    break;
                }
            }
        }
        else if($range=='S'){
            switch($index){
                case 60:
                {
                    $query.="AND SALE_ADM_NO= '60' ";
                    break;   
                }
                case 56:
                {
                    $query.="AND SALE_ADM_NO= '56' ";
                    break;   
                }
                case 64:
                {
                    $query.="AND SALE_ADM_NO= '64' ";
                    break;   
                }
                case 7:
                {
                    $query.="AND SALE_ADM_NO= '7' ";
                    break;   
                }
            }

        }
        else{
            exit;
        }

        // echo $query."<br>";
        // exit;

        $result=mysql_query($query, $db);

        if(!$result){
            echo "<script>alert();</script>";
            exit;
        }

        $cnt=mysql_num_rows($result);
        $record=array();
        if($cnt>0){
            for($i=0; $i<$cnt; $i++){
                $record[$i]=mysql_fetch_assoc($result);
            }
            
        }
        return $record;

    }//end of function 




#====================================================================
# Request Parameter
#====================================================================

    // print_r($_POST);
    // // exit;

    // echo "<br><br><br>";

    require_once "../../_PHPExcel/Classes/PHPExcel.php";


    $objConditional = new PHPExcel_Style_Conditional();
    
    // $objConditional->setConditionType()

    
    $objPHPExcel = new PHPExcel();

    $sheet1=$objPHPExcel->setActiveSheetIndex(0);
    $sheet2=$objPHPExcel->createSheet(1);
    $sheet1->setTitle("TEST1");
    $sheet2->setTitle("TEST2");


    $fileName   =   $_POST['filename'];
    $password   =   $_POST['password'];
    $ranage     =   $_POST['range'];
    $sel_index_sales=$_POST['sel_index_sales'];
    $sel_index_local=$_POST['sel_index_local'];

    if($range=='L'){
        $index=$sel_index_local;
    }
    else{
        $index=$sel_index_sales;
    }

    $sheet1->setCellValue("A1", iconv("EUC-KR", "UTF-8", "CP_NO"));
    $sheet1->setCellValue("B1", iconv("EUC-KR", "UTF-8", "CP_NM"));
    $sheet1->setCellValue("C1", iconv("EUC-KR", "UTF-8", "CP_ADDR"));
    $sheet1->setCellValue("D1", iconv("EUC-KR", "UTF-8", "CP_PHONE"));
    $sheet1->setCellValue("E1", iconv("EUC-KR", "UTF-8", "MANAGER_NM"));
    $sheet1->setCellValue("F1", iconv("EUC-KR", "UTF-8", "CP_ZIP"));
    $sheet1->setCellValue("G1", iconv("EUC-KR", "UTF-8", "MEMO"));

    $sheet2->setCellValue("A1", iconv("EUC-KR", "UTF-8", "CP_NO"));
    $sheet2->setCellValue("B1", iconv("EUC-KR", "UTF-8", "CP_NM"));
    $sheet2->setCellValue("C1", iconv("EUC-KR", "UTF-8", "CP_ADDR"));
    $sheet2->setCellValue("D1", iconv("EUC-KR", "UTF-8", "CP_PHONE"));
    $sheet2->setCellValue("E1", iconv("EUC-KR", "UTF-8", "MANAGER_NM"));
    $sheet2->setCellValue("F1", iconv("EUC-KR", "UTF-8", "CP_ZIP"));
    $sheet2->setCellValue("G1", iconv("EUC-KR", "UTF-8", "MEMO"));


    $arr_rs=selectCompanyByFilter($conn, $range, $index);


    $cnt_arr=sizeof($arr_rs);

    if($cnt_arr>0){
        for($i = 0; $i < $cnt_arr; $i++){
            $CP_NO          =       $arr_rs[$i]["CP_NO"];
            $CP_NM          =       $arr_rs[$i]["CP_NM"];
            $CP_ADDR        =       $arr_rs[$i]["CP_ADDR"];
            $CP_PHONE       =       $arr_rs[$i]["CP_PHONE"];
            $MANAGER_NM     =       $arr_rs[$i]["MANAGER_NM"];
            $CP_ZIP         =       $arr_rs[$i]["CP_ZIP"];


            $sheet1->setCellValue("A".($i+2), iconv("EUC-KR", "UTF-8", $CP_NO));
            $sheet1->setCellValue("B".($i+2), iconv("EUC-KR", "UTF-8", $CP_NM));
            $sheet1->setCellValue("C".($i+2), iconv("EUC-KR", "UTF-8", $CP_ADDR));
            $sheet1->setCellValue("D".($i+2), iconv("EUC-KR", "UTF-8", $CP_PHONE));
            $sheet1->setCellValue("E".($i+2), iconv("EUC-KR", "UTF-8", $MANAGER_NM));
            $sheet1->setCellValue("F".($i+2), iconv("EUC-KR", "UTF-8", $CP_ZIP));

            $sheet2->setCellValue("A".($i+2), iconv("EUC-KR", "UTF-8", $CP_NO));
            $sheet2->setCellValue("B".($i+2), iconv("EUC-KR", "UTF-8", $CP_NM));
            $sheet2->setCellValue("C".($i+2), iconv("EUC-KR", "UTF-8", $CP_ADDR));
            $sheet2->setCellValue("D".($i+2), iconv("EUC-KR", "UTF-8", $CP_PHONE));
            $sheet2->setCellValue("E".($i+2), iconv("EUC-KR", "UTF-8", $MANAGER_NM));
            $sheet2->setCellValue("F".($i+2), iconv("EUC-KR", "UTF-8", $CP_ZIP));




            // $sheetIndex->setCellValue("A5", iconv("EUC-KR", "UTF-8", "No."));

        }//end of for($i<$cnt_arr)
    }//end of if($cnt_arr>0);

    $objConditional->setConditionType(PHPExcel_Style_Conditional::CONDITION_CONTAINSTEXT)
                    ->setOperatorType(PHPExcel_Style_Conditional::OPERATOR_CONTAINSTEXT)
                    ->addCondition("Bla bla");

    $objConditional->getStyle()->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getEndColor()->setARGB(PHPExcel_Style_Color::COLOR_GREEN);

    $conditionalStyles=$sheet1->getStyle('A2')->getConditionalStyles();
    array_push($conditionalStyles, $objConditional);   
    $sheet1->getStyle('A2:F200')->setConditionalStyles($conditionalStyles);




// 1행
// $regDate=substr($REQ_DATE,0,10);
// $title ="대상엘티디 발주서 양식(".$regDate.")";
// $sheetIndex->mergeCells("A1:I3");



// // Rename sheet
// $objPHPExcel->setActiveSheetIndex(0)->setTitle(iconv("EUC-KR", "UTF-8", "발주서"));

// // Set active sheet index to the first sheet, so Excel opens this as the first sheet
// $objPHPExcel->setActiveSheetIndex(0);

//$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(100);

// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
// $filename = "발주서-" . date("Ymd");

// Redirect output to a client’s web browser (Excel5)

    header('Content-Type: application/vnd.ms-excel');
    header("Content-Disposition: attachment;filename=" . $fileName . ".xls");
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');

    mysql_close($conn);
    exit;
?>