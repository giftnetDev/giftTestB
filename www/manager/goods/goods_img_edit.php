<?
    require "../../_classes/com/db/DBUtil.php";
    require "../../_classes/com/util/Util.php";


    require "../../_common/config.php";

    $conn=db_connection("w");
?>
<?
    class GoodsImageInfo{
        private $goodsNo;
        private $filePath;
        private $fileName;
        private $fileRName;
        private $fileEXT;
        private $fileSize;

    }
?>
<?
//------------------FUNCTIONS-----------------------

    function getGoodsImageInfo($db, $goods_no){
        $query="SELECT  GOODS_CODE, GOODS_NAME, FILE_NM_100, FILE_RNM_100, FILE_SIZE_100, FILE_EXT_100, 
                        FILE_NM_150, FILE_RNM_150, FILE_PATH_150, FILE_SIZE_150, FILE_EXT_150, CONTENTS, IMG_URL
                FROM TBL_GOODS
                WHERE GOODS_NO = ".$goods_no."
                ";
        $result=mysql_query($query, $db);

        $rows=mysql_fetch_assoc($result);

        return $rows;

    }
    function getGoodsDetailImageInfo($db, $goods_no){

    }
    function setGoodsImageInfo($db, $goodsNo, $fileRealName, $filePath, $fileName, $fileEXT, $fileSize){
        $query="UPDATE TBL_GOODS 
                SET FILE_NM_100     =   '".$fileName."', 
                    FILE_RNM_100    =   '".$fileRealName."', 
                    FILE_SIZE_100   =   '".$fileSize."', 
                    FILE_EXT_100    =   '".$fileEXT."' 
                WHERE GOODS_NO      =   ".$goodsNo."    
                ";
        
        echo $query."<br>";

        $result=mysql_query($query, $db);
    }
        

                    

?>
<?
//-----------------------valuable-----------------------------------------------
$savedir1 = $g_physical_path."upload_data/goods";
//상품 이미지 경로
// //upload_data/goods/
//상품상세 경로
//	$file_path = $_SERVER[DOCUMENT_ROOT]."/upload_data/goods_image/detail/".str_replace("-","_",$rs_goods_code).".jpg";
//#############################################################################
//-----------------------Page Process------------------------------------------
//#############################################################################-  
    // print_r($_POST);

    // echo "goods_no : $goods_no<br>";

    print_r($_POST);
    echo "<br><br><br>";


    if($mode=="SAVE_IMG_FILE"){
        switch ($flag01) {
        
            case "insert" :
                echo "insert<br>";
                $FILE_NM_100		= upload($_FILES[file_nm_100], $savedir1, 10 , array('gif', 'jpeg', 'jpg','png'));
                $FILE_RNM_100		= $_FILES[file_nm_100][name];
    
                $FILE_SIZE_100	= $_FILES[file_nm_100]['size'];
                $FILE_EXT_100		= end(explode('.', $_FILES[file_nm_100]['name']));
    
            break;
            case "keep" :
    
                $FILE_NM_100		= $old_file_nm_100;
                $FILE_RNM_100		= $old_file_rnm_100;
    
                $FILE_SIZE_100	= $old_file_size_100;
                $FILE_EXT_100		= $old_file_ext_100;
    
            break;
            case "delete" :
    
                $FILE_NM_100		= "";
                $FILE_RNM_100		= "";
    
                $FILE_SIZE_100	= "";
                $FILE_EXT_100		= "";
    
            break;
            case "update" :
    
                $FILE_NM_100		= upload($_FILES[file_nm_100], $savedir1, 10 , array('gif', 'jpeg', 'jpg','png'));
                $FILE_RNM_100		= $_FILES[file_nm_100][name];
    
                $FILE_SIZE_100 = $_FILES[file_nm_100]['size'];
                $FILE_EXT_100  = end(explode('.', $_FILES[file_nm_100]['name']));
    
            break;

        }//end of switch($flag01)


        setGoodsImageInfo($conn, $goods_no, $FILE_RNM_100, "", $FILE_NM_100, $FILE_EXT_100, $FILE_SIZE_100);

        
    }//end of mode = "SAVE_IMG_FILE"



    $arr_goods=getGoodsImageInfo($conn, $goods_no);

    $GOODS_NAME     =       $arr_goods["GOODS_NAME"];
    $GOODS_CODE     =       $arr_goods["GOODS_CODE"];
    $FILE_NM_100    =       $arr_goods["FILE_NM_100"];
    $FILE_RNM_100   =       $arr_goods["FILE_RNM_100"];
    $FILE_SIZE_100  =       $arr_goods["FILE_SIZE_100"];
    $FILE_EXT_100   =       $arr_goods["FILE_EXT_100"];
    $FILE_NM_150    =       $arr_goods["FILE_NM_150"];
    $FILE_RNM_150   =       $arr_goods["FILE_RNM_150"];
    $FILE_PATH_150  =       $arr_goods["FILE_PATH_150"];
    $FILE_SIZE_150  =       $arr_goods["FILE_SIZE_150"];
    $FILE_EXT_150   =       $arr_goods["FILE_EXT_150"];
    $CONTENTS       =       $arr_goods["CONTENTS"];
    $IMG_URL        =       $arr_goods["IMG_URL"];

?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
        <title><?=$g_title?></title>
        <!-- <link rel="stylesheet" href="../css/admin.css" type="text/css" /> -->
        <script type="text/javascript" src="../js/common.js"></script>
        <script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
        <script type="text/javascript" src="../js/goods_common.js"></script>
        <script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->
        <script type="text/javascript" src="../../_common/SE2.1.1.8141/js/HuskyEZCreator.js" charset="utf-8"></script>
        <script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
        <script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
        <script type="text/javascript" src="../jquery/jquery.cookie.js"></script>
        <!-- <link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" /> -->

        <script>
            function js_save(){
                var frm=document.frm;
                frm.mode.value="SAVE_IMG_FILE";
                frm.method="POST";
                frm.target="";
                frm.action="<?=$_SERVER[PHP_SELF]?>";

                frm.submit();
            }
            function js_list(){
                document.location.href="goods_list_limited.php";
            }
            function js_fileView(obj){
                var res=$('select[name=flag01]').val();
                if(res=='update'){
                    $('input[name=file_nm_100]').css("visibility","visible");
                }
                else{
                    $('input[name=file_nm_100]').css("visibility","hidden");
                }
            }
        </script>

    </head>
    <body>
        <form name="frm">
            <!-- hidden 변수-->
            <input type="hidden" name="goods_no" value="<?=$goods_no?>">
            <input type="hidden" name="mode" value="<?=$mode?>">
            <input type="hidden" name="hFileNm100" value="<?=$FILE_NM_100?>">
            <input type="hidden" name="old_file_nm_100" value="<?=$FILE_NM_100?>">
            <input type="hidden" name="old_file_rnm_100" value="<?=$FILE_RNM_100?>">
            <input type="hidden" name="old_file_size_100" value="<?=$FILE_SIZE_100?>">
            <input type="hidden" name="old_file_ext_100" value="<?=$FILE_EXT_100?>">

            <table>
                <colgroup>
                    <col width="20%" />
                    <col width="20%" />
                    <col width="20%" />
                    <col width="20%" />
                    <col width="20%" />

                </colgroup>
                <tr><!--상품명-->
                    <td>상품명</td>
                    <td colspan="4"><?=$GOODS_NAME?></td>
                </tr>
                
                <tr><!--상품코드-->
                    <td>상품코드</td>
                    <td colspan="4"><?=$GOODS_CODE?></td>
                </tr>
                
                <tr>
                <!--상품 URL이미지-->
                    <td>상품 이미지 URL</td>
                    <td colspan="2"><input type="text" name="img_url" value="<?=$IMG_URL?>"></td>
                    <td></td>
                    <td><img src="" alt=""></td>
                </tr>

                <tr>
                <!--이미지 경로-->
                    <td>상품 이미지 경로</td>
                    <td colspan="2"><input type="text" name="file_path_150" style="width:300px" value="<?=$FILE_PATH_150?>"></td>
                    <td><input type="text" name="file_rnm_150" value="<?=$FILE_RNM_150?>"></td>
                    <td><img src="" alt=""></td>
                </tr>

                <tr>
                <!--상품 이미지-->

                    <td>상품 이미지</td>
                    <td colspan="2"><input type="file" name="file_nm_100" ></td>
                    <td>
                    <?
                        if($FILE_NM_100 != ""){
                        ?>
                            <select name="flag01" style="width:70px" onchange="javascript:js_fileView(this)">
                                <option value="keep">유지</option>
                                <option value="delete">삭제</option>
                                <option value="update">수정</option>
                            </select>
                        <?
                        }
                        else{
                        ?>
                            <input type="hidden" name="flag01" value="insert">
                        <?
                        }
                    ?>                  
                    </td>
                    <td>
                    <?
                        if($FILE_NM_100 != ""){
                        ?>
                            <img class="" src="/upload_data/goods/<?=$FILE_NM_100?>" alt="<?=$FILE_RNM_100?>" width="100" alt="이미지">
                            <script>

                            </script>
                        <?
                        }
                    ?>
                    </td>
                </tr>

<!-- 
                <tr>
                    <td>상품 이미지 상세1</td>
                    <td colspan="2"><input type="file" name="ex_file1[]"></td>
                    <td></td>
                    <td><img src="" alt=""></td>
                </tr>

                <tr>
                    <td>상품 이미지 상세2</td>
                    <td colspan="2"><input type="file" name="ex_file1[]"></td>
                    <td></td>
                    <td><img src="" alt=""></td>
                </tr>

                <tr>
                    <td>상품 이미지 상세3</td>
                    <td colspan="2"><input type="file" name="ex_file1[]"></td>
                    <td></td>
                    <td><img src="" alt=""></td>
                </tr> -->


            </table>

            <div style="float:right; padding-right: 25%;">
                    <input type="button" value="저장" onclick="js_save();">
                    &nbsp; &nbsp;
                    <input type="button" value="목록" onclick="js_list()">
                    
            </div>
        </form>
    </body>
    <script>
        $(document).ready(function(){
            var test=$('input[name=hFileNm100]').val();
            alert(test);
            if($('input[name=hFileNm100]').val()!=""){
                $('input[name=file_nm_100]').css("visibility","hidden");
            }
            else{
                $('input[name=file_nm_100]').css("visibility","visible");
            }
        });
    </script>

</html>