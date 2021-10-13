<?session_start();?>
<?
    require "../../_classes/com/db/DBUtil.php";
    $conn= db_connection("w");

    #Confirm right
    $menu_right="GD015";

    #Common_header Check Session
    include "../../_common/common_header.php";

    #Common Function, Login_Function
    require "../../_common/config.php";
    require "../../_classes/com/util/Util.php";
    require "../../_classes/com/etc/etc.php";
    require "../../_classes/biz/goods/goods.php";

    #Request Parameter

    $catalogNo=$_GET['catalogNo'];
    $pageNo=$_GET['pageNo'];


    #DML Process
?>


<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?= $g_charset ?>" />
        <title><?= $g_title ?></title>
        <link rel="stylesheet" href="../css/admin.css" type="text/css" />
        <script type="text/javascript" src="../js/common.js"></script>
        <script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
        <script>
            //JavaScript Functions

            function js_make_number_select_box(sltName,optTitle, optRange,location){
                var stlHtml="<SELECT name='"+sltName+"' id='"+sltName+"'> <option value=''>"+optTitle+"</option>";
                var cnt=optRange-0;
                for(i=1;i<=cnt;i++){
                    stlHtml+="<option value='"+i+"'>"+i+"</option>";
                }
                stlHtml+="</SELECT>";
                $(location).html(stlHtml);
            }
            function js_create_page(catalogNo,pageNo){
                var width=$('#sltWidth option:checked').val()-0;
                var height=$('#sltHeight option:checked').val()-0;
                var page=$('#sltPage option:checked').val()-0;
                var loc=$('input:radio[name="rdLocation"]:checked').val();
                if(width<=0 || height<=0){
                    alert('�ʺ� Ȥ�� ������ ũ�Ⱑ ���õ��� �ʾҽ��ϴ�');
                    return ;
                }
                if(width>5 || height>6){
                    alert('�߸��� �Է� ���Դϴ�');
                    return ;
                }

                switch(loc){
                    case 'first':
                        page=0;
                    break;
                    case 'rear':
                        if(page<=0){
                            alert('�������� ���� ��ġ�� �������� �ʾҽ��ϴ�');
                            return;
                        }    
                    break;
                    case 'last':
                        page=pageNo;
                    break;
                }
                //alert(width+', '+height);

                if(confirm(catalogNo+','+pageNo)){
                    alert('page is '+page);
                    window.opener.js_add_page_to_database(catalogNo,pageNo,width,height,page);
                    self.close();
                }
                else{
                    return;
                }
                
            }
        $(document).ready(function(){
                // $('#sltWidth option:checked').val(1);
                // $('#sltHeight option:checked').val(1);
        });
        </script>
    </head>
    <body>
        <h2 style="margin:0;">���̺� �����ϴ� â</h2>
        <div>
            <label id="lblWidth"></label>
            <label id="lblHeight"></label>
            <script>
                js_make_number_select_box("sltWidth","�ʺ�",5,"#lblWidth");
                js_make_number_select_box("sltHeight","����",6,"#lblHeight");
            </script>
            <br/>
            <label><input type='radio' name='rdLocation' value='first'/> �� ��</label>
            <label>
            <span id='spnPage'></span>
                <script>
                    js_make_number_select_box("sltPage","������",<?=$pageNo?>,"#spnPage");
                </script>
                <input type='radio' name='rdLocation' value='rear'/> ��</label>
            <label><input type='radio' name='rdLocation' value='last' checked='checked' />�� ��</label>
            <br/>
            <input type="button" name="btnCreatePage" id="btnCreatePage" value="������ ����" onclick="js_create_page(<?=$catalogNo?>,<?=$pageNo?>)"/>

        </div>
    </body>
</html>