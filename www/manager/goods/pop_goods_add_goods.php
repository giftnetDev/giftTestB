<? session_start();?>
<?
    error_reporting(-1);
    ini_set('display_erros','On');

    require "../../_classes/com/db/DBUtil.php";
    $conn=db_connection("w");
?>
<?
    $tdNum=$_GET['tdNum'];
    $catalogNo=$_GET['catalogNo'];
    $pageNo=$_GET['pageNo'];
    $pageHeight=$_GET['pageHeight'];
    $pageWidth=$_GET['pageWidth'];
    $pageIdx=$_GET['pageIdx'];

    function pgagListGoods($db){
        $query="SELECT GOODS_NO, GOODS_CODE, GOODS_NAME, CATE_04, PRICE, IMG_URL, FILE_RNM_150, FILE_PATH_150
                FROM TBL_GOODS";
        
        $result=mysql_query($query, $db);
        $cnt=mysql_num_rows($result);
        $record=array();
        if($cnt>0){
            for($i=0;$i<$cnt;$i++){
                $record[$i]=mysql_fetch_assoc($result);
            }
        }
        return $record;
    }
    function pgagMakeGoodsCategorySelectBox(){
        
        $selectBox="";
        $selectBox.="<SELECT name='sltGoodsCategory' id='sltGoodsCategory'>";
        $selectBox.="<option value=''>검색 조건</option>";
        $selectBox.="<option value='opGoodsNo'>상품번호</option>";
        $selectBox.="<option value='opGoodsCode'>상품코드</option>";
        $selectBox.="<option value='opGoodsName'>상품명</option>";
        $selectBox.="</SELECT>";

        echo $selectBox;
    }

?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset ?>" />
        <title><?= $g_title ?></title>
        <script type="text/javascript" src="../js/common.js"></script>
        <script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>

        <script type="text/javascript">

            var g_goodDatas;

            function js_make_size_selectbox(sltName,optTitle, optRange,location){
                // alert('width is '+optRange);
                var stlHtml="<SELECT name='"+sltName+"' id='"+sltName+"'> <option value=''>"+optTitle+"</option>"
                var cnt=optRange-0;
                for(i=1;i<=cnt;i++){
                    stlHtml+="<option value='"+i+"'>"+i+"</option>"
                    // alert('cnt is '+cnt);
                }
                stlHtml+="</SELECT>";
                //alert(stlHtml);
                $(location).html(stlHtml);
            }
            function js_select_goods(idx){
                var catalogNo="<?=$catalogNo?>";
                var pageNo="<?=$pageNo?>";
                var tdNum="<?=$tdNum?>";
                var pageIdx="<?=$pageIdx?>";
                
                // return ;
                //부모 페이지에서 함수 하나 끌어다 와야 한다.
                var goodsWidth=$("#sltGoodWidth option:selected").val()-0;
                var goodsHeight=$("#sltGoodHeight option:selected").val()-0;
                alert(goodsWidth+" , "+goodsHeight);
                if(goodsWidth<1 || goodsHeight<1){
                    alert('상품의 너비와 높이를 1이상으로 설정해 주세요');
                    return;
                }
                //alert('test');
                window.opener.js_add_goods_to_database(catalogNo, pageIdx, tdNum, goodsWidth, goodsHeight, g_goodDatas[idx]);
                self.close();
            }
            function js_view_goods_list(dataList){
                var table="<table border='1'>";
                var cnt=dataList.length;
                var imgUrl;
                for(i=0;i<cnt;i++){
                    imgUrl=""+dataList[i]['FILE_PATH_150']+dataList[i]['FILE_RNM_150'];
                    table+="<tr><td> <a herf='#' onclick='js_select_goods("+i+");return false;'> <table><tr><td> "
                    +dataList[i]['GOODS_NO']+"</td><td><img src='"+imgUrl+"' width=100 height=100></td><td>"
                    +dataList[i]['GOODS_NAME']+"</td></tr></table></a></td></tr>";
                }
                table+="</table>";
                $("#dvTable").html(table);
            }
            function js_search(){
                var sltGoodsCategory=$("#sltGoodsCategory option:checked").val();
                var gc=trim(sltGoodsCategory);
                if(gc==""){
                    alert('검색조건을 선택해 주세오');
                    return ;
                }
                var content=$("#txtGoodContent").val();
                content=trim(content);
                if(content==""){
                    alert('검색 정보가 없습니다');
                    return;
                }
                $.ajax({
                    url:'/manager/ajax/ajax_catalog.php',
                    dataType:'json',
                    type:'POST',
                    data:{
                        'mode':"SEARCH_GOODS_INFO",
                        'sltGoodsCategory':gc,
                        'content':content
                    },
                    success:function(data){
                        console.log('SUCCESS');
                        console.log(data);
                        console.log('data length is '+data.length);
                        g_goodDatas=data;
                        js_view_goods_list(data);
                    },
                    error:function(jqXHR,textStatus,errorThrown){
                        console.log('ERROR');
                        console.log('textStatus is '+textStatus+', and errorThrown is '+errorThrown);
                    }
                });

            }
        </script>
        <script>
            $(document).ready(function(){
                $('#sltGoodWidth option:checked').val(1);
                $('#sltGoodHeight option:checked').val(1);
            });
        </script>

    </haed>
    <body>
        <div>
            <?=pgagMakeGoodsCategorySelectBox()?>

            <input type="text" name="txtGoodContent" id="txtGoodContent" />
            <input type="button" name="btnSearch" id="btnSearch" onclick="js_search()" value="검색"/>
            <label id="lblGoodWidth"></label>
            <label id="lblGoodHeight"></label>
            <script>
                js_make_size_selectbox("sltGoodWidth","너비",<?=$pageWidth?>,"#lblGoodWidth");
                js_make_size_selectbox("sltGoodHeight","높이",<?=$pageHeight?>,"#lblGoodHeight");
            </script>

            
        </div>
        <div id="dvTable">
        </div>
    </body>    
</html>