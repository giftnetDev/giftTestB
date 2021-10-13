<?
error_reporting(-1);
ini_set('display_errors', 'On');

class CSheet{
    private $width;
    private $height;
    private $chk;
    private $pageIdx;

    function __construct($pageIdx, $w, $h){
        $this->chk=array();
        $this->width=$w;
        $this->height=$h;
        $this->pageIdx=$pageIdx;
        for($i=0;$i<$this->height;$i++){
            for($j=0;$j<$this->width;$j++){
                $this->chk[$i][$j]=false;
            }
        }
    }
    public function OccupyingSlot($x, $y){
        $this->chk[$y][$x]=true;
    }
    public function GetSlotState($x,$y){
        return $this->chk[$y][$x];
    }
    public function ShowSize(){

    }
    public function GetPageIdx(){
        return $this->pageIdx;
    }
    public function GetPageWidth(){
        return $this->width;
    }
    public function GetPageHeight(){
        return $this->height;
    }
}

?>
<?
    require "../../_classes/com/db/DBUtil.php";
    $conn=db_connection("w");

    //sql chk변수
    $cntD=0;
     //이 페이지에서 공통으로 사용할 변수들
    $arrAPB=array('X','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R');
    $fileName="";//catalogName
    $catalogSize=-1;
    $pages=array();
    $goods=array();
    $pageInforms=array();
    $mergeRow=11;

    // $mode="PRINT_TO_EXCEL";
    //$fileName="2001년 상반기";

?>
<?  // 여기에 mode는 하나밖에 없지만 의미상 이 php 실행 시작지점을 명시적으로 표시하기 위해 이렇게 썼다.
    //if($mode=="PRINT_TO_EXCEL"){

        $catalogNo=$_GET['hdnCatalogNo'];
        $queryT="SELECT CATALOG_NAME, PAGE_CNT FROM TBL_CATALOG_TOP WHERE CATALOG_NO = ".$catalogNo." ; ";
        $resultT=mysql_query($queryT, $conn);

        if($resultT){
            $recordT=mysql_fetch_assoc($resultT);
            $fileName=$recordT['CATALOG_NAME'];
            $catalogSize=$recordT['PAGE_CNT'];
            $cntD+=1;//첫번째 DB 접근 chk
        }
        else{
            //echo "---ERROR NO DATA----<br/>";
        }

        
        require "../../_PHPExcel/Classes/PHPExcel.php";
        require "../../_PHPExcel/Classes/PHPExcel/IOFactory.php";
    
        $objPHPExcel=new PHPExcel();
        // $objMDrawing=new PHPExcel_Worksheet_MemoryDrawing();
        $objPHPExcel->getProperties()->setTitle("Tmp");

        //엑셀의 Sheet이름 정하기
        $currSheet=$objPHPExcel->getActiveSheet();
        $currSheet->setTitle("1");
        if($catalogSize>1){
            for($i=2;$i<=$catalogSize;$i++){
                $objWorkSheet=$objPHPExcel->CreateSheet($i);
                $objWorkSheet->setTitle($i);
            }
        }
        
        //각 페이지별로 사이즈 잡아놓기
        $queryP="SELECT PAGE_IDX, PAGE_NO, PAGE_SIZE_X, PAGE_SIZE_Y FROM TBL_CATALOG_PARENT WHERE PAGE_CATALOG = ".$catalogNo." ORDER BY PAGE_NO ASC ; ";
        $resultP=mysql_query($queryP, $conn);
        $cntPage=mysql_num_rows($resultP);
        if($cntPage>0){
            for($i=0;$i<$cntPage;$i++){
                $pages[$i]=mysql_fetch_assoc($resultP);
                $pageInforms[$i]= new CSheet($pages[$i]['PAGE_IDX'], $pages[$i]['PAGE_SIZE_X'], $pages[$i]['PAGE_SIZE_Y']);
                for($c=1;$c<=$pages[$i]['PAGE_SIZE_X'];$c++){
                    $objPHPExcel->setActiveSheetIndex($i)->getColumnDimension($arrAPB[$c*2-1])->setWidth(40);
                }
            }
            $cntD+=2; //두번째 DB 접근 chk
        }

        $queryC="SELECT * FROM TBL_CATALOG_CHILD WHERE CATALOG_NO = ".$catalogNo." ; ";
        $resultC=mysql_query($queryC, $conn);
        $cntC=mysql_num_rows($resultC);

        //echo "cnt is ".$cntC."<br/>";
        
        if($cntC>0){
            for($i=0;$i<$cntC;$i++){
                $goods[$i]=mysql_fetch_assoc($resultC);
                for($j=0;$j<$catalogSize;$j++){
                    if($goods[$i]['PAGE_IDX']==$pageInforms[$j]->GetPageIdx()){
                        //j번째 page의 i번째아이탬 pos에다가 i번째 아이탬을 배치하라.
                        $goodPosX   =$goods[$i]['POS_X']+1;//엑셀에서는 1부터, 프로그램에서는 0부터 시작한다.
                        $goodPosY   =$goods[$i]['POS_Y']+1;//엑셀에서는 1부터, 프로그램에서는 0부터 시작한다.
                        $goodWidth  =$goods[$i]['SIZE_X'];
                        $goodHeight =$goods[$i]['SIZE_Y'];
                        $currSheet=$objPHPExcel->setActiveSheetIndex($j);
                        AssignGoods($currSheet,$goodPosX,$goodPosY,$goodWidth,$goodHeight,$arrAPB,$mergeRow);
                        InsertImage($currSheet,$goodPosX,$goodPosY,$goods[$i]['FILE_PATH'].$goods[$i]['FILE_RNM'],$arrAPB,$mergeRow);
                        InsertGoodInform($currSheet, $goodPosX, $goodPosY, $goodWidth, $goodHeight, $goods[$i], $arrAPB, $mergeRow);
                        for($y=$goodPosY;$y<$goodPosY+$goodHeight;$y++){
                            for($x=$goodPosX;$x<$goodPosX+$goodWidth;$x++){
                                $pageInforms[$j]->OccupyingSlot($x-1,$y-1);
                            }
                        }
                        break;
                    }
                }//for($j<catalogSize)  
            }//for($i<cnt)
        }//if($cnt>0)


        

        for($i=0;$i<$catalogSize;$i++){
            for($y=0;$y<$pageInforms[$i]->GetPageHeight();$y++){
                for($x=0;$x<$pageInforms[$i]->GetPageWidth();$x++){
                    if($pageInforms[$i]->GetSlotState($x,$y)==false){
                        $currSheet=$objPHPExcel->setActiveSheetIndex($i);
                        CreateSlot($currSheet,$x+1,$y+1,$arrAPB,$mergeRow);
                    }
                }
            }
        }

        $objPHPExcel->setActiveSheetIndex(0);

        //echo "catalogNo is : ".$catalogNo."<br/>";

        header( "Content-type: application/vnd.ms-excel; charset=utf-8"); 
        header("Content-Disposition: attachment;filename=".$fileName.".xls");
        header('Cache-Control: max-age=0');
     
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');

        mysql_close($conn);
        exit;
    //}

    function AssignGoods($currSheet, $goodPosX, $goodPosY, $goodWidth, $goodHeight, $arrAPB, $mergeRow){
        $xi=$arrAPB[2*$goodPosX-1];
        $yi=$mergeRow*$goodPosY-($mergeRow-1);
        $xf=$arrAPB[2*($goodPosX+$goodWidth-1)-1];
        $xf1=$arrAPB[2*($goodPosX+$goodWidth-1)];
        $yf=$mergeRow*($goodPosY+$goodHeight-1);




        $currSheet->mergeCells($xi.$yi.":".$xf.$yf);
        $currSheet->getStyle($xi.$yi.":".$xf1.$yf)->getBorders()->getOutline()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
        if($goodHeight>1){
            $xi2=$arrAPB[2*($goodPosX+$goodWidth-1)];
            $xf2=$xi2;
            $yi2=$goodPosY*($mergeRow)-($mergeRow-1);
            $yf2=($goodPosY+$goodHeight-2)*($mergeRow);
            $currSheet->mergeCells($xi2.$yi2.":".$xf2.$yf2);
        } 
    }
    function CreateSlot($currSheet, $x, $y, $arrAPB,$mergeRow){
        $colStart=$arrAPB[$x*2-1];
        $colEnd=$colStart;
        $colEnd1=$arrAPB[$x*2];
        $rowStart=$y*$mergeRow-($mergeRow-1);
        $rowEnd=$y*$mergeRow;
 
        $currSheet->mergeCells($colStart.$rowStart.":".$colEnd.$rowEnd);
        $currSheet->getStyle($colStart.$rowStart.":".$colEnd1.$rowEnd)->getBorders()->getOutline()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
    }
    function InsertImage($sheet, $x, $y,$fileName,$arrAPB, $mergeRow){
        
        $x=(int)$x;
        $y=(int)$y;
        

        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $imgUrl=$_SERVER["DOCUMENT_ROOT"]."";
        $imgUrl.=$fileName;
        $objDrawing->setPath($imgUrl);

        $row=$mergeRow*$y-($mergeRow-1);
        $col=$arrAPB[2*$x-1];
        $objDrawing->setCoordinates($col.$row);
        $objDrawing->setOffsetX(2);
        $objDrawing->setOffsetY(2);
        $objDrawing->setWidthAndHeight(190,190);

        $objDrawing->setWorksheet($sheet); //이미지를 파일에 저장-> 어느 Sheet에 저장할 것인지 정한다. 
    }
    function InsertGoodInform($currSheet, $posX, $posY ,$width, $height, $objGood, $arrAPB,$mergeRow){
        //Excel 기준 posX, posY로 입력받았다.
        $row=($posY+$height-1)*$mergeRow;
        $col=$arrAPB[($posX+$width-1)*2];
        if($objGood['MULTIPLE_TF']=='Y'){
            $currSheet->getStyle($col.($row-10))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFFFFF55");
        }
        $currSheet->setCellValue($col.($row-10),iconv("EUC-KR","UTF-8",$objGood['GOODS_NAME']));
        $currSheet->setCellValue($col.($row-9),iconv("EUC-KR","UTF-8",$objGood['GOODS_CODE']));
        $currSheet->getStyle($col.($row-8))->getNumberFormat()->setFormatCode("_(\"\\\"* #,##0_);_(\"\\\"* \(#,##0\);_(\"\\\"* \"-\"??_);_(@_)");
        $currSheet->setCellValue($col.($row-8),iconv("EUC-KR","UTF-8",$objGood['PRICE']));
        $currSheet->setCellValue($col.($row-7),iconv("EUC-KR","UTF-8","박스입수 : ".$objGood['DELIVERY_CNT_IN_BOX']."개"));
        $currSheet->setCellValue($col.($row-6),iconv("EUC-KR","UTF-8",$objGood['GOODS_DSC1']));
        $currSheet->setCellValue($col.($row-5),iconv("EUC-KR","UTF-8",$objGood['GOODS_DSC2']));
        $currSheet->setCellValue($col.($row-4),iconv("EUC-KR","UTF-8",$objGood['GOODS_DSC3']));
        $currSheet->setCellValue($col.($row-3),iconv("EUC-KR","UTF-8",$objGood['GOODS_DSC4']));
        $currSheet->setCellValue($col.($row-2),iconv("EUC-KR","UTF-8",$objGood['GOODS_DSC5']));
        $currSheet->setCellValue($col.($row-1),iconv("EUC-KR","UTF-8",$objGood['GOODS_DSC6']));
        $currSheet->setCellValue($col.($row+0),iconv("EUC-KR","UTF-8",$objGood['GOODS_DSC7']));

    }
?>