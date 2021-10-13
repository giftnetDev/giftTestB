<?session_start();?>
<?
#====================================================================
# DB Include, DB Connection
#====================================================================
require "../../_classes/com/db/DBUtil.php";

$conn = db_connection("w");

$menu_right = "CP006"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

#====================================================================
# common_header Check Session
#====================================================================
	require "../../_common/common_header.php"; 


#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/util/ImgUtil.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/goods/goods.php";
    require "../../_classes/biz/order/order.php";
    require "../../_classes/dataStructure/LinkedList.php";

    // print_r($_SESSION);


?>
<?//FUNCTIONS ZONE


?>
<?//PROCESS ZONE

?>

<!DOCTYPE HTML>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>">
        <title><?=$g_title?></title>
        <link rel="stylesheet" href="../css/newStyle/newERPStyle.css">
        <script type="text/javascript" src="../js/common.js"></script>
        <script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>

        <script>
            
            //JS_FUNCTION ZONE
            
            function js_excel(){
                // alert('test');
                if($("input[name='filename']").val()==""){
                    alert('파일이름을 입력해 주세요');
                    $("input[name='filename']").focus();
                    return;
                }
                // if($("input[name='upassword']").val()==""){
                    
                // }
                var frm=document.frm;
                frm.target="";
                frm.action="./company_catalog_excel.php";
                frm.method="POST";
                frm.submit();
            } 
            

        </script>
        
    </head>
    <body>
        <table id="wholeFrame" width="100%">
            <thead>
                <colgroup>
                    <col width="200">
                    <col width="*">
                </colgroup>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2" id='tdTopArea'> <? require "../../_common/top_area4.php"; ?> </td>
                </tr>
                <tr>
                    <td id="tdLeftArea"> <? require "../../_common/left_area_new.php"; ?> </td>
                    <td id="tdContent">
                        <? require "./c_company_catalog_list.php"; ?>
                    </td><!--id="tdContent-->
                </tr>
            </tbody>
        </table><!--id="wholeFrame-->
    </body>
    <script>
        $(document).ready(function(){

            $("input:radio[name='range']").change(function(){
                if($(this).val()=="S"){
                    $("select[name='sel_index_local']").css('display','none');
                    $("select[name='sel_index_sales']").css('display','block');
                }
                else{
                    $("select[name='sel_index_local']").css('display','block');
                    $("select[name='sel_index_sales']").css('display','none');

                }
            });

        });
    </script>
</html>