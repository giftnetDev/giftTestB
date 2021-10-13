<? session_start(); ?>
<?
//header("Pragma;no-cache");
//header("Cache-Control;no-cache,must-revalidate");
error_reporting(-1);
ini_set('display_errors', 'On');

#====================================================================
# DB Include, DB Connection
#====================================================================
require "../../_classes/com/db/DBUtil.php";

$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
$menu_right = "GD015"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

#====================================================================
# common_header Check Session
#====================================================================
require "../../_common/common_header.php";  //이걸 require시키지 않으면 세션 없이도 접근할 수 있다. 이걸 꼭 해줘야 한다.


#=====================================================================
# common function, login_function
#=====================================================================
require "../../_common/config.php";
require "../../_classes/com/util/Util.php";
require "../../_classes/com/util/ImgUtil.php";
require "../../_classes/com/etc/etc.php";
require "../../_classes/biz/goods/goods.php";
// require "../ajax/ajax_catalog.php";
?>
<?
    //class define
?>
<?
    //Page Fields
    $catalogCnt=0;
    $mode=$_POST['mode'];
    $catalogName=$_POST['catalogName'];
    
?>
<?
    //"good_catalog" Inner Functionss
?>
<?
    //"good_catalog" Functions
    function gcCreateCatalogSelectBox($db){
        $query="SELECT CATALOG_NO, CATALOG_NAME FROM TBL_CATALOG_TOP ;";
        
        $result=mysql_query($query, $db);
        $cnt=mysql_num_rows($result);
        $record=array();

        if($cnt>0){
            for($i=0;$i<$cnt;$i++){
                $record[$i]=mysql_fetch_assoc($result);
                echo"<OPTION value='".$record[$i]['CATALOG_NO']."'>".$record[$i]['CATALOG_NAME']."</OPTION>";
            }
        }
        else{
            echo"<script>alert('".$cnt."');</script>";
        }
        echo"</SELECT>";
        return $cnt;
    }
    function gcAddCatalog($db, $catalogName){
        $query="SELECT CATALOG_NAME FROM TBL_CATALOG_TOP WHERE CATALOG_NAME='".$catalogName."'; ";
        $result=mysql_query($query,$db);
        $cnt=mysql_num_rows($result);
        if($cnt>0){
            return 1;
        }
        else{
            $query2="INSERT INTO TBL_CATALOG_TOP(CATALOG_NAME) VALUES('".$catalogName."'); ";
            $result2=mysql_query($query2, $db);
            if($result2<>""){
                return 0;
            }
            else{
                return -1;
            }
        }
    }
?>
<?
    //THis Page Code
    echo"<script>console.log('mode is ".$mode."');</script>";
    echo"<script>console.log('mode is ".$catalogName."');</script>";

    if($mode=="ADD_CATALOG"){
        //echo"<script>alert('3-3');</script>";
        $catalogName=$_POST['txtCatalog'];
        if($catalogName==""){
            echo "catalogName is NULL<br/>";
            $mode="NORMAL";
            return ;
        }
        $res=gcAddCatalog($conn,$catalogName);
        if($res==-1){
            echo"<script>alert('DB Insert Error');</script>";
            echo "DB Insert Error<br/>";
        }
        else if($res==0){
            //echo"<script>alert('DB Insert Success');</script>";
            echo "DB Insert Success<br/>";
        }
        else{
            echo"<script>alert('DB Redundancy');</script>";
            echo "DB Redundancy<br/>";
        }
        $mode="NORMAL";
        ?>
            <script>document.location="goods_catalog.php?mode=<?=$mode?>"</script>
        <?
        exit;

    }
?>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= $g_charset ?>" />
    <title><?= $g_title ?></title>
    <link rel="stylesheet" href="../css/admin.css" type="text/css" />
    <script type="text/javascript" src="../js/common.js"></script>
    <script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
    
    <script type="text/javascript">
        // js Global valuable in goods_catalog
        var g_isExistenceLayer=0;
        var g_catalogNo=-1;
        var g_catalogPageCnt=-1;
        var g_pageNo=-1;
        var g_pageIdx=-1;
        var g_pageHeight=0;
        var g_pageWidth=0;
        var g_chkArr;
        var g_goods= new Array();
        function js_delete_catalog_from_database(catalogNo){
            $.ajax({
                url:'/manager/ajax/ajax_catalog.php',
                dataType:'text',
                type:'POST',
                data:{
                    'mode':"DELETE_CATALOG",
                    'catalogNo':catalogNo
                },
                success:function(data){
                    $('#sltCatalog option[value='+catalogNo+']').remove();
                    $("#sltCatalog option[value='0']").prop('selected',true);

                    $("#tblPagination").remove();
                    // $("#tblGoods").remove();
                    $("#dvGrid").html("");
                    $("#dvPagination").html("");
                    g_catalogNo=-1;
                    g_pageNo=-1;
                    // $("#dvGrid").html(noPageAlert);
                    //js_no_page_alert();

                    //카달로그 삭제가 완료된 후 표시되지 말아야 할 버튼 4가지
                    $("#btnAddPage2").attr('disabled', true);
                    $("#btnAddPage2").hide();
                    $("#btnDeletePage").attr('disabled', true);
                    $("#btnDeletePage").hide();
                    $("#btnPrintExcel").attr('disabled',true);
                    $("#btnPrintExcel").hide();
                    $("#btnDeleteCatalog").attr('disabled',true);
                    $("#btnDeleteCatalog").hide();

                    alert('삭제 완료');



                },
                error:function(jqXHR, textStatus, errorThrown){

                }

            });
        }
        function js_delete_catalog(catalogNo){
            var catalogName = $('#sltCatalog option:checked').text();
            if(confirm("\""+catalogName+"\" 카탈로그를 삭제하시겠습니까")){
                js_delete_catalog_from_database(catalogNo);
            }
        }
        function js_print_to_excel(catalogNo){
            var url="goods_create_catalog_excel.php";
            // var win=NewWindow(url,"goods_create_catalog_excel",500,500,'yes');
            //win.self.close();
            var frm=document.getElementById("frmExcel");
            frm.hdnCatalogNo.value=catalogNo;
            //alert("catalogNo is "+ frm.hdnCatalogNo.value);
            //$('#hdnCatalogNo').val(catalogNo);
            frm.target="";
            frm.method="GET";
            frm.action=url;
            frm.submit();
        }
        function js_delete_page(catalogNo,pageNo){
            if(confirm(Number(pageNo+1)+"번쩨 페이지를 삭제히시겠습니까?")){
                js_delete_page_from_database(catalogNo,pageNo);
                alert(Number(pageNo+1)+"번쩨 페이지 삭제 완료");
            }         
        }

        function js_delete_page_from_database(catalogNo,pageNo){
            pageNo-=0;
            //alert(catalogNo+','+pageNo+','+g_pageIdx+','+g_catalogPageCnt);
            $.ajax({
                url:'/manager/ajax/ajax_catalog.php',
                dataType:'text',
                type:'POST',
                data:{
                    'mode':"DELETE_PAGE",
                    'catalogNo':catalogNo,
                    'pageNo':pageNo,
                    'pageIdx':g_pageIdx,
                    'catalogSize':g_catalogPageCnt
                },
                success:function(data){
                    
                    if(g_catalogPageCnt==pageNo+1){
                        pageNo--;
                    }
                    g_catalogPageCnt--;
                    if(g_catalogPageCnt==0){
                        js_no_page_alert();
                    }
                    else{
                        js_view_page(catalogNo,pageNo);
                    }
                },
                error:function(jqXHR,textStatus,errorThrown){

                }
            });
        }
        function js_delete_goods(idx){
            var good_idx=g_goods[idx]['GOODS_IDX'];
            $.ajax({
                url:'/manager/ajax/ajax_catalog.php',
                dataType:'text',
                type:'POST',
                data:{
                    'mode':"DELETE_GOODS",
                    'goodIdx':good_idx
                },
                success:function(data){
                    js_splitTable(g_goods[idx]['CATALOG_NO'],g_goods[idx]['PAGE_NO'],g_pageWidth,g_pageHeight,
                    g_goods[idx]['POS_X'],g_goods[idx]['POS_Y'],g_goods[idx]['SIZE_X'],g_goods[idx]['SIZE_Y']);
                    delete g_goods[idx];
                },
                error:function(jqXHR,textStatus,errorThrown){

                }
            });
        }
        function js_get_good_info(idx){
            return g_goods[idx];
        }
        function js_update_good_info(data,idx){
            g_goods[idx]['GOODS_NAME']=data['GOODS_NAME'];
            g_goods[idx]['GOODS_CODE']=data['GOODS_CODE'];
            g_goods[idx]['PRICE']=data['PRICE'];
            for(var i=1; i<=7;i++){
                g_goods[idx]['GOODS_DSC'+i]=data['GOODS_DSC'+i];
            }
            $.ajax({
                url:'/manager/ajax/ajax_catalog.php',
                dataType:'text',
                type:'POST',
                data:{
                    'mode':"UPDATE_GOODS_INFO",
                    'goodIdx':data['GOODS_IDX'],
                    'goodName':data['GOODS_NAME'],
                    'goodCode':data['GOODS_CODE'],
                    'price':data['PRICE'],
                    'dsc1':data['GOODS_DSC1'],
                    'dsc2':data['GOODS_DSC2'],
                    'dsc3':data['GOODS_DSC3'],
                    'dsc4':data['GOODS_DSC4'],
                    'dsc5':data['GOODS_DSC5'],
                    'dsc6':data['GOODS_DSC6'],
                    'dsc7':data['GOODS_DSC7'],
                    'multipleTF':data['MULTIPLE_TF'],
                    'saleState':data['SALE_STATE'],
                    'deliveryCntInBox':data['DELIVERY_CNT_IN_BOX']
                    //판매상태에 대하여 다루기는 해야한다.
                },
                success:function(data){
                    console.log("SUCCESS");
                    console.log("data is");
                    console.log(data);
                },
                error:function(jqXHR,textState,errorThrown){
                    console.log("ERROR");
                    console.log("textStatus is "+textState);
                    console.log("errorThrown is "+errorThrown);
                }
            });
        }
        function js_pos_to_td(posX, posY, pageWidth, pageHeight){
            posX-=0;
            posY-=0;
            pageWidth-=0;
            pageHeight-=0;
            var idx=posY*pageWidth+posX;
            return "td"+idx;
        }
        function js_splitTable(catalogNo,pageNo,pageWidth,pageHeight,posX,posY,width,height){
            //td값을 먼저 계산한다
            pageWidth-=0;
            pageHeight-=0;
            posX-=0;
            posY-=0;
            width-=0;
            height-=0;
            var obj=null;
            for(var i=posY; i<posY+height;i++){
                for(var j=posX; j<posX+width;j++){
                    var tdNum=js_pos_to_td(j,i,pageWidth,pageHeight);
                    var num=tdNum.replace("td","");
                    var tdId="#"+tdNum;
                    if(i==posY && j==posX){
                        $(tdId).attr("colspan",1);
                        $(tdId).attr("rowspan",1);
                        $(tdId).attr("width",150);
                        $(tdId).attr("height",150);
                        
                        obj=document.getElementById(tdNum);
                        obj.innerHTML="<a href='#' id='href"+num+"' onclick='js_add_goods("+catalogNo+","+pageNo+",\""+tdNum+"\"); return false;'>추가하기</a>";

                    }
                    else{
                        $(tdId).show();
                    }
                    g_chkArr[i][j]=0;
                }
            }

        }
        function js_mergeTable(pageWidth, pageHeight, tdNum, posX, posY, width, height){
            // alert('merge table');
            pageWidth-=0;
            pageHeight-=0;
            posX-=0;
            posY-=0;
            width-=0;
            height-=0;

            for(var i=posY;i<posY+height;i++){
                for(var j=posX;j<posX+width;j++){
                    if(i==posY && j==posX) continue;
                    var idx=i*pageWidth+j;
                    console.log(idx);
                    //$("#td"+idx).remove();
                    $("#td"+idx).hide();
                }
            }
            $("#"+tdNum).attr("colspan",width);
            $("#"+tdNum).attr("rowspan",height);
            $("#"+tdNum).attr("width",150*width);
            $("#"+tdNum).attr("height",150*height);
        }
        function js_occupy_chk_arr(posX, posY, width, height){
            posX-=0;
            posY-=0;
            width-=0;
            height-=0;
            for(var i=posY;i<posY+height;i++){
                for(var j=posX;j<posX+width;j++){
                    g_chkArr[i][j]=1;
                }
            }
        }
        function js_open_goods_edit_window(idx){
            //alert(g_goods[idx]['GOODS_NO']);
            var url="pop_goods_catalog_goods_info.php?idx="+idx;
            var wndObj=NewWindow(url,url.replace('.php',''),650,800,'yes');
        }
        function js_view_goods_info_layer(idx){
            var dvLayerId="dvLayer"+idx;
            var obj=document.getElementById(dvLayerId);
            obj.innerHTML="<table><tr><td>"
            +g_goods[idx]['GOODS_NAME']+"</td></tr>"
            +"<tr><td>"+g_goods[idx]['GOODS_CODE']+"</td></tr>"
            +"<tr><td>"+g_goods[idx]['PRICE']+"</td></tr>"
            +"<tr><td>"+g_goods[idx]['SALE_STATE']+"</td></tr>"
            +"<tr><td>"+g_goods[idx]['GOODS_DSC1']+"</td></tr>"
            +"<tr><td>"+g_goods[idx]['GOODS_DSC2']+"</td></tr>"
            +"<tr><td>"+g_goods[idx]['GOODS_DSC3']+"</td></tr>"
            +"<tr><td>"+g_goods[idx]['GOODS_DSC4']+"</td></tr>"
            +"<tr><td>"+g_goods[idx]['GOODS_DSC5']+"</td></tr>"
            +"<tr><td>"+g_goods[idx]['GOODS_DSC6']+"</td></tr>"
            +"<tr><td>"+g_goods[idx]['GOODS_DSC7']+"</td></tr>"
            +"</table>";
            
            $("#aLayer"+idx).hover(
                function(){$("#"+dvLayerId).slideDown(5)},
                function(){$("#"+dvLayerId).slideUp(5)}
            );

        }
        function js_view_goods_info(tdNum,imgUrl,idx){
            var obj=document.getElementById(tdNum);
            //var a=100;
            if(idx=="undefined"){
                idx=0;

            }

            obj.innerHTML="<a href='#' onclick='js_open_goods_edit_window("+idx+");' onmouseover='js_view_goods_info_layer("+idx+")'id='aLayer"+idx+"' ><img src='"+imgUrl+"' width='100' height='100'><div id='dvLayer"+idx+"'></div></a>";

            
        }
        function js_arrange_goods_in_table(goodsList,listLength){
            // console.log('listLength is '+listLength);
            for(var i=0; i<listLength; i++){
                console.log('current i is '+i);
                var posX=goodsList[i]['POS_X']-0;
                var posY=goodsList[i]['POS_Y']-0;
                var width=goodsList[i]['SIZE_X']-0;
                var height=goodsList[i]['SIZE_Y']-0;
                var imgUrl=goodsList[i]['FILE_PATH']+goodsList[i]['FILE_RNM'];
                var tdIdx=js_pos_to_td(posX,posY, g_pageWidth, g_pageHeight);
                js_mergeTable(g_pageWidth, g_pageHeight, tdIdx, posX, posY, width, height);
                js_occupy_chk_arr(posX, posY, width, height);
                js_view_goods_info(tdIdx,imgUrl,i);
            }
            for(var i=0;i<listLength;i++){

            }
        }
        function js_is_able_to_insert_goods(catlogNo, pageNo, tdNum, goodWidth, goodHeight){
            // alert('g_pageWidth is '+g_pageWidth+', g_pageHeight is '+g_pageHeight);
            var tableNum=tdNum.replace('td','');
            goodWidth-=0;
            goodHeight-=0;
            tableNum-=0;
            var goodPosX=tableNum%g_pageWidth;
            var goodPosY=parseInt(tableNum/g_pageWidth);
            if(goodPosX+goodWidth>g_pageWidth || goodPosY+goodHeight>g_pageHeight){
                return false;
            } 
            //------------------------------
            // alert('goods pos X is '+goodPosX+', goods pos y is '+goodPosY+', goodWidth is '+goodWidth+', goodHeight is '+goodHeight);
            for(i=goodPosY;i<goodPosY+goodHeight;i++){
                for(j=goodPosX;j<goodPosX+goodWidth;j++){
                    if(g_chkArr[i][j]>0){
                        return false;
                    }
                }
            }
            js_occupy_chk_arr(goodPosX,goodPosY,goodWidth,goodHeight);
            return true;
        }
        function js_add_goods_to_database(catalogNo, pageIdx, tdNum, goodWidth, goodHeight, goodData){
             //alert(catalogNo+', '+pageNo+', '+tdNum+', '+goodWidth+', '+goodHeight);
             //console.log(goodData);
             //alert();
            var res=js_is_able_to_insert_goods(catalogNo, pageIdx, tdNum, goodWidth, goodHeight);
            // alert('result of function is '+res);
            if(res==true)
            {
                //alert(goodData['GOODS_CODE']);
                var pageHeight=g_pageHeight;
                var pageWidth=g_pageWidth;
                // return ;
                $.ajax({
                    url:'/manager/ajax/ajax_catalog.php',
                    dataType:'json',
                    type:'POST',
                    data:{
                        'mode':"ADD_GOODS_TO_DATABASE",
                        'catalogNo':catalogNo,
                        'pageIdx':pageIdx,
                        'goodsNo':goodData['GOODS_NO'],
                        'tdNum':tdNum,
                        'pageHeight':pageHeight,
                        'pageWidth':pageWidth,
                        'goodHeight':goodHeight,
                        'goodWidth':goodWidth,
                        'goodCode':goodData['GOODS_CODE'],
                        'price':goodData['SALE_PRICE'],
                        'filePath':goodData['FILE_PATH_150'],
                        'fileName':goodData['FILE_RNM_150'],
                        'goodName':goodData['GOODS_NAME'],
                        'saleState':goodData['CATE_04'],
                        'deliveryCntInBox':goodData['DELIVERY_CNT_IN_BOX']
                    },
                    success:function(data){
                        if(data=="0" || data=="-1"){
                            alert('insert fail');
                            
                        }
                        else{
                            //alert('카탈로그 생성 후 첫 아이탬 삽입');
                            console.log(data);
                            js_mergeTable(pageWidth,pageHeight,tdNum,data['POS_X'],data['POS_Y'],goodWidth,goodHeight);
                            js_occupy_chk_arr(data['POS_X'],data['POS_Y'],goodWidth,goodHeight);
                            js_view_goods_info(tdNum,goodData['FILE_PATH_150']+goodData['FILE_RNM_150'],g_goods.length);
                            //추가될 TD value에 들어갈 값을 미리 세팅해놓는다.
                            var tdIndex=g_goods.length;                            
                            g_goods.push(data);
                            //여기다가 해당 TD의 value정보를 바꾼다.
                            

                        }
                    },
                    error:function(jqXHR,textStatus,errorThrown){
                        console.log('ERROR!!!');
                        console.log('state : '+textStatus+', errorThrown : '+errorThrown);
                    }
                });
            }
            else{
                alert('현재 자리에 현재 크기로 배치할 수 없습니다');
            }
            
        }
        function js_load_goods(pageIdx){
            
            $.ajax({
                //해당 카탈로그, 해당 페이지에 포함되어있는 Goods를 불러오는 ajax
                url:'/manager/ajax/ajax_catalog.php',
                dataType:'json',
                type:'POST',
                data:{
                    'mode':"GET_GOODS_INFO",
                    'pageIdx':pageIdx
                },
                success:function(data){
                    console.log('Goods Data SUCCESS');
                    console.log(data);
                    g_goods=data;
                    // console.log(g_goods);
                    var len=data.length;
                    if(len>0){
                        js_arrange_goods_in_table(data,len); //해당 goods정보로 table변경 및 g배열 값 채우기
                    }
                },
                error:function(jqXHR,textStatus,errorThrown){
                    console.log('ERROR');
                }
            })

        }
        function js_view_page(catalogNo, pageNo){
            var table;
            //표에 대한 정보(catalogNo, pageNo, pageWidth, pageHeight)를 활용해서 표를 생성
            $.ajax({
                url:'/manager/ajax/ajax_catalog.php',
                dataType:'json',
                type:'POST',
                data:{
                    'mode':"VIEW_PAGE",
                    'catalogNo':catalogNo,
                    'pageNo':pageNo
                },
                success:function(data){
                    //alert("표 보여주기 AJAX성공");
                    console.log(data);
                    //2. width,height 설정
                    g_pageWidth=data['width'];
                    g_pageHeight=data['height'];
                    g_pageIdx=data['pageIdx'];
                    //1. 페이지 정보에 따라 table 뼈대 그리기 및 g배열 0으로 초기화
                    
                    js_create_table(catalogNo, pageNo,g_pageHeight,g_pageWidth,"#dvGrid");
                    //3. 해당 catalog, page에 할당된 goods 가져오기
                    js_load_goods(g_pageIdx);
                    // $('#dvControl').append(btnAddPage);
                    console.log('total page is '+g_catalogPageCnt);
                    js_view_pagination(g_catalogPageCnt,pageNo)//페이지네이션 기능 만들기
                    $('#btnAddPage2').show();
                    $('#btnAddPage2').attr('disabled',false);
                    $('#btnDeletePage').show();
                    $('#btnDeletePage').attr('disabled',false);
                    $('#btnPrintExcel').show();
                    $('#btnPrintExcel').attr('disabled',false);
                    $('#btnDeleteCatalog').show();
                    $('#btnDeleteCatalog').attr('disabled',false);

                },
                error:function(jqXHR,textStatus,errorThrown){
                    //alert("실패");
                    console.log("FAIL, and textStatus : "+textStatus+", errorThrown is "+errorThrown);
                }
            });
        }
        function js_add_goods(catalogNo, pageNo, tdNum){
            // alert(catalogNo+', '+pageNo);
            console.log(tdNum);
            var url="pop_goods_add_goods.php?catalogNo="+catalogNo+"&pageNo="+pageNo+"&tdNum="+tdNum+"&pageHeight="+g_pageHeight+"&pageWidth="+g_pageWidth+"&pageIdx="+g_pageIdx;
            NewWindow(url,'pop_goods_add_goods','700','400','Yes');
        }
        function js_fill_goods_in_area(tdNum,imgUrl,goodNo){
            document.getElementById(tdNum).innerText=imgUrl;
            //$("#"+tdNum).val()
        }
        function js_create_table(catalogNo, pageNo, row, col, location){
            //alert('row is '+row+', '+'col is '+col);    
            var table="<table id='tblGoods' border='1' style=' margin:auto; text-align:center;'>";
            var num=0;

            g_chkArr=new Array(row);
            for(i=0;i<row;i++){
                g_chkArr[i]=new Array(col);
                for(j=0;j<col;j++){
                    g_chkArr[i][j]=0;
                }
            }

            for(i=0;i<row;i++){
                table+="<tr>"
                for(j=0;j<col;j++){
                    table+="<td id='td"+num+"' name='-1' width='150' height='150'><a href='#' id='href"+num+"' onclick='js_add_goods("+catalogNo+","+pageNo+",\"td"+num+"\"); return false;'>추가하기</a></td>";
                    num++;
                }
                table+="</tr>";
            }
            table+="</table>";
            $(location).html(table);
        
        }
        function js_add_catalog(){
            // alert('3-1');
            var frm=document.getElementById("frmMode");
            var catalogName=$("#txtCatalog").val();
            if(trim(catalogName)==""){
                alert('카테로리 이름을 입력하시오');
                return;
            }
             alert('카탈로그 이름 : '+catalogName);
            var mode=document.getElementById("mode");
            mode.value="ADD_CATALOG";
            frm.method="POST";
            frm.action="<?=$_SERVER[PHP_SELF]?>";
            frm.submit();

        }
        function js_no_page_alert(){
            var noPageAlert="<p><br/><h2>현재 카탈로그에 할당되어있는 페이지가 없습니다.<br/> 페이지를 생성해 주세요.</h2></p>";
            noPageAlert+="<input type='button' name='btnAddPage' id='btnAddPage' value='페이지 추가' onclick='js_add_page("+g_catalogNo+",0)'/>";
            $("#dvGrid").html(noPageAlert);
            $("#btnAddPage2").attr('disabled', true);
            $("#btnAddPage2").hide();
            $("#btnDeletePage").attr('disabled', true);
            $("#btnDeletePage").hide();
            $("#btnPrintExcel").attr('disabled',true);
            $("#btnPrintExcel").hide();
            $("#dvPagination").html("");

            //페이지가 하나도 없는 카탈로그라도 지울 수 있도록
            $("#btnDeleteCatalog").attr('disabled',false);
            $("#btnDeleteCatalog").show();
            
            if(g_catalogNo<0){
                //g_catalogNo가 할당되어 있지 않은 경우만
                $("#btnDeleteCatalog").attr('disabled',true);
                $("#btnDeleteCatalog").hide();
            }


            
        }

        function js_add_page(catalogNo, pageNo){
            var url="pop_goods_making_catalog_page.php?catalogNo="+catalogNo+"&pageNo="+pageNo;
            NewWindow(url,'pop_goods_making_catalog_page','830','600','Yes');
            
        }

        function js_move_page(idx){
            console.log(idx);
            //여기서 idx에 해당되는 page가 보여야 한다
            if(idx==g_pageNo+1) return;
            js_view_page(g_catalogNo,idx-1);// idx-1 인 이유는 DB상에서 0page부터 시작하기 때문이다.
            g_pageNo=idx-1;
        }
        function js_move_side_page(dir){
            dir-=0;
            if(dir==1){ //좌측 한 칸
                if(g_pageNo==0) return;
                js_view_page(g_catalogNo,g_pageNo-1);
                g_pageNo--;
            }
            else if(dir==2){ //우측 한 칸
                if(g_pageNo==g_catalogPageCnt-1) return ;
                js_view_page(g_catalogNo,g_pageNo+1);
                g_pageNo++;
            }
            else if(dir==3){ //좌측 10칸
                if(g_pageNo==0) return;
                if(g_pageNo-10<0){
                    js_view_page(g_catalogNo,0);
                    g_pageNo=0;
                }
                else{
                    js_view_page(g_catalogNo,g_pageNo-10);
                    g_pageNo-=10;
                }
            }
            else if(dir==4){ //우측 10칸
                if(g_pageNo==g_catalogPageCnt-1) return ;
                if(g_pageNo+10>g_catalogPageCnt){
                    js_view_page(g_catalogNo,g_catalogPageCnt-1);
                    g_pageNo=g_catalogPageCnt-1;
                }
                else{
                    js_view_page(g_catalogNo,g_pageNo+10);
                    g_pageNo+=10;
                }
            }
            else if(dir==5){ //좌측 끝
                if(g_pageNo==0) return;
                js_view_page(g_catalogNo,0);
                g_pageNo=0;
            }
            else if(dir==6){ //우측 끝
                if(g_pageNo==g_catalogPageCnt-1) return ;
                js_view_page(g_catalogNo,g_catalogPageCnt-1);
                g_pageNo=g_catalogPageCnt-1;
            }
        }
        function js_view_pagination(totalPage,currPage){
            console.log('curr page is '+currPage);
            currPage+=1;
            var sp=parseInt(currPage/10)*10+1;
            if(currPage>0 && currPage%10==0) sp-=10;
            // var ep=sp+9<totalPage ? sp+9:totalPage; 
            if(sp+9<totalPage){
                ep=sp+9;
            }
            else{
                ep=totalPage;
            }
            var pageStr="<div class='paging'>";
            pageStr+="<span><a href='#' onclick='js_move_side_page(5); return false;'><img src='/manager/images/admin/pag_first_bu.gif'></a></span>";
            pageStr+="<span><a href='#' onclick='js_move_side_page(3); return false;'><img src='/manager/images/admin/pag_first.gif'></a></span>";
            pageStr+="<span><a href='#' onclick='js_move_side_page(1); return false;'><img src='/manager/images/admin/pag_prev.gif'></a></span>";
            for(var i=sp;i<=ep;i++){
                if(i==currPage)pageStr+="<span><a href='#' onclick='js_move_page("+i+"); return false;'class='selected'>"+i+"</a></span>";
                else pageStr+="<span><a href='#' onclick='js_move_page("+i+"); return false;'>"+i+"</a></span>";
            }
            pageStr+="<span><a href='#' onclick='js_move_side_page(2); return false;'><img src='/manager/images/admin/pag_next.gif'></a></span>";
            pageStr+="<span><a href='#' onclick='js_move_side_page(4); return false;'><img src='/manager/images/admin/pag_final.gif'></a></span>";
            pageStr+="<span><a href='#' onclick='js_move_side_page(6); return false;'><img src='/manager/images/admin/pag_first_bu.gif'></a></span>";
            pageStr+="</div>";

           $('#dvPagination').html(pageStr);
        }

        function js_view_catalog(){
            var catalogNo=$('#sltCatalog option:checked').val();

            $.ajax({
                url: '/manager/ajax/ajax_catalog.php',
                dataType:'text',
                type:'POST',
                data:{
                    'mode': "VIEW_CATALOG", 
                    'catalogNo':catalogNo 
                },
                success:function(data){
                    data=data-0;
                    g_catalogPageCnt=data;
                    g_catalogNo=catalogNo;
                    g_pageNo=0;
                    if(data==0){
                        //해당 카탈로그의 Page가 하나도 없을 때
                        js_no_page_alert();
                    }
                    else if(data>0){
 
                        //해당 카탈로그의 Page가 1개 이상 있을 때
                        // alert('Page Existence');
                        console.log('page Existence and total page is '+data);
                        js_view_page(g_catalogNo,0);
                        

                    }
                },
                error:function(jqXHR, textStatus, errorThrown){
                    console.log("ERROR");
                    console.log("textStatus : "+textStatus+", errorThrown : "+errorThrown);
                }
            });
            g_catalogNo=catalogNo;
        }
        function js_add_page_to_database(catalogNo, pageNo,width, height,location){
            var table="";
            alert("!!!! location is "+location);
            $.ajax({
                url:'/manager/ajax/ajax_catalog.php',
                dataType:'text',
                type:'POST',
                data:{
                    'mode':"ADD_PAGE",
                    'catalogNo':catalogNo,
                    'pageNo':pageNo,
                    'pageWidth':width,
                    'pageHeight':height,
                    'location':location
                },
                success:function(data){
                    //alert('SUCCESS : ' +data);
                    data=Number(data);
                    if(data>-1){//DB 생성 및 Update가 제대로 된 경우
                        
                        //표 만드는 함수가 들어가야 한다.
                        js_create_table(catalogNo,location,height,width,"#dvGrid");
                        g_catalogPageCnt++;
                        g_pageNo=location;
                        g_pageIdx=data;
                        g_pageWidth=width;
                        g_pageHeight=height;

                        js_view_pagination(g_catalogPageCnt,location);
                        //console.log('첫 페이지 생성 후 g_catalog_pageCnt is '+g_catalogPageCnt);
                        $('#btnAddPage2').show();
                        $('#btnAddPage2').attr('disabled',false);
                        $('#btnDeletePage').show();
                        $('#btnDeletePage').attr('disabled',false);
                        $('#btnPrintExcel').show();
                        $('#btnPrintExcel').attr('disabled',false);
                        $('#btnDeleteCatalog').show();
                        $('#btnDeleteCatalog').attr('disabled',false);

                    }
                    else{//문제 발생시
                        alert('data is '+data);
                        js_no_page_alert();
                    }
                    
                },
                error:function(jqXHR,textStatus,errorThrown){
                    console.log("ERROR");
                    console.log("textStatus : "+textStatus+", errorThrown : "+errorThrown);
                }
            });
        }
        function js_change_page_location(){
            var url="pop_catalog_change_page_location.php?catalogNo="+g_catalogNo+"&pageIdx="+g_pageIdx+"&catalogPageCnt="+g_catalogPageCnt;
            NewWindow(url,'pop_catalog_change_page_location','830','600','Yes'); 
        }
    </script>
    <script>
        $(document).ready(function(){
            $(document).on('keypress',function(e) {
                if(e.which == 13) {
                    //delete g_goods[1];
                    // g_goods.push({"CATALOG_NO": "5","FILE_PATH": "/upload_data/goods_image/0101/","FILE_RNM": "301-008065.jpg","GOODS_CODE": "301-008065","GOODS_DSC1": "","GOODS_DSC2": "","GOODS_DSC3": "","GOODS_DSC4": "","GOODS_DSC5": "","GOODS_DSC6": "","GOODS_DSC7": "","GOODS_DSC8": "","GOODS_DSC9": "","GOODS_IDX": "18","GOODS_NAME": "슈가버블 주방세제 용기 300g","GOODS_NO": "3797","MULTIPLE_TF": "F","PAGE_NO": "0","POS_X": "1","POS_Y": "1","PRICE": "0","SALE_STATE": "판매중","SIZE_X": "1","SIZE_Y": "1","USE_TF": "Y"});
                    //console.log(g_goods);
                }
                if(e.which == 32) {
                    //console.log(g_goods);
                    console.log('------top of doc-----');
                    console.log('g_catalog_pageCnt is '+g_catalogPageCnt);
                    console.log('g_pageNo is '+g_pageNo);
                    console.log('----bottom of doc----');
                    // console.log('g_pageIdx is '+g_pageIdx);
                    // console.log(g_goods);
                    // console.log(g_chkArr);
                    // console.log(g_goods.length);
                    // console.log('catalogNo is : '+g_catalogNo);
                }

            });
        });
    </script>
</head>

<body>
    <div id="adminwrap">

        <?
        #====================================================================
        # common top_area
        #====================================================================

        require "../../_common/top_area.php";
        ?>

        <table width="100%" cellpadding="0" cellspacing="0">
            <colgroup>
                <col width="180" />
                <col width="*" />
            </colgroup>
            <tr>
                <td class="leftarea">
                    <?
                    #====================================================================
                    # common left_area
                    #====================================================================

                    require "../../_common/left_area.php";
                    ?>
                </td>
                <td class="contentarea">

                    <h2 style="margin:0;">카탈로그 관리</h2>

                    <div id=dvHeadTable>
                        <table cellpadding="0" cellspacing="0" class="colstable">
                            <colgroup>
                                <col width="15%" />
                                <col width="24%" />
                                <col width="12%" />
                                <col width="15%" />
                                <col width="*" />
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>
                                        카탈로그 선택
                                    </th>
                                    <td>
                                        <SELECT name="sltCatalog" id="sltCatalog"><OPTION value="0">카탈로그 선택</OPTION>";
                                        <?$catalogCnt=gcCreateCatalogSelectBox($conn);?>
                                        &nbsp;
                                        
                                    </td>
                                    <td>
                                        <input type="button" name="btnViewCatalog" id="btnViewCatalog" value="카탈로그 보기" onclick="js_view_catalog()"/>
                                       
                                    </td>
                                    <th>
                                        엑셀로 인쇄
                                    </th>
                                    <td>
                                        <form name="frmExcel" id="frmExcel">
                                            <input type="button" name="btnPrintExcel" id="btnPrintExcel" value="엑셀로 인쇄" onclick="javascript:js_print_to_excel(g_catalogNo);" disabled hidden />
                                            <input type="hidden" id="hdnCatalogNo" name="hdnCatalogNo" value="">
                                        </form>
                                        
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        카달로그 추가
                                    </th>
                                    <td>
                                        <form name="frmMode" id="frmMode">
                                            <input type="text" name="txtCatalog" id="txtCatalog" value="" placeholder="카탈로그 이름" />
                                            <input type="hidden" name="mode" id="mode" value="">
                                        </form>
                                        
                                    </td>
                                    <td>                    
                                            <input type="button" name="btnAddCatalog" id="btnAddCatalog" value="카탈로그 추가" onclick="js_add_catalog()"/>

                                    </td>
                                    <th>
                                        페이지 추가
                                    </th>
                                    <td>
                                        <input type="button" name="btnAddPage2" id="btnAddPage2" value="페이지 추가" onclick="javascript:js_add_page(g_catalogNo,g_catalogPageCnt);" disabled hidden/>
                                    </td>
                                </tr>
                            </thead>
                        </table>
                    </div>                     

                    <div name="dvGrid" id="dvGrid"style="background-color:#f0f0f0; overflow:auto; height: 700px; text-align:center; width:95%;">
                    </div>
                    
                    <div name="dvPagination" id="dvPagination" style="background-color:#ffffff; height:30px; text-align:center; width:95%;">
                    </div> 
                    <div class="btnright" style="margin:0 0 5px 0; height:20px;">
                            <input type="button" name="btnChangePageLocation" id="btnChangePageLocation" value="현재 패이지 위치변경" onclick="" disabled />
                            
                            <input type="button" name="btnDeletePage" id="btnDeletePage" value="현재 패이지 삭제" onclick="javascript:js_delete_page(g_catalogNo,g_pageNo);" disabled hidden/>
                            &nbsp;
                            <input type="button" name="btnDeleteCatalog" id="btnDeleteCatalog" value="카탈로그 삭제" onclick="javascript:js_delete_catalog(g_catalogNo)" disabled hidden />

                    </div>

                </td>
            </tr>
        </table>
    </div>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================s
mysql_close($conn);
?>