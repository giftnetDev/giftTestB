<?

    $catalogNo=$_GET['catalogNo'];
    $pageIdx=$_GET['pageIdx'];
    $pageNo=$_GET['pageNo'];
    $catalogPageCnt=$_GET['catalogPageCnt'];

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
            function js_make_number_select_box(sltName,optTitle, optRange,Location){
                var sltHtml="<SELECT name'"+sltName+"' id='"+sltName+"'> <option value=''>"+optTitle+"</option>";
                var cnt=optRange-0;
                for(i=1;i<=cnt;i++){
                    sltHtml+="<option value='"+i+"'>"+i+"</option>";
                }
                sltHtml+="</SELECT>";
                $(location).html(sltHtml);
            }
            function js_change_page_location(catalogPageCnt, pageNo){
                var opt=$('input:radio[name="rdLocation"]:checked').val();

                var location=-1;
                switch(opt){
                    case 'first':
                        location=0;
                    break;
                    case 'rear':
                        location = $('#sltPage option:checked').val()-0;
                    break;

                    case 'last':
                        location=catalogPageCnt-1;
                    break;
                }

                $.ajax({
                    url:'/manager/ajax/ajax_catalog.php',
                    dataType:'text',
                    type:'POST',
                    data:{
                        'mode':"CHANGE_PAGE_LOCATION",
                        'catalogPageCnt':catalogPageCnt,
                        'catalogNo':catalogNo,
                        'pageIdx':pageIdx,
                        'location':location
                    },
                    success:function(data){
                        window.opener.location.reload();
                        self.close();
                    },
                    error:function(jqXHR, textStatus, errorThrown){
                        console.log("FAIL, and textStatus : "+textyStatus+" erroThrown is "+errorThrown);

                    }
                });
            }

        </script>
    </head>
    <body>
        <h2 sytle="margin:0;">현재 페이지 위치 바꾸는 창</h2>
        <div>
            <label>
                <input type='radio' name='rdLocation' value='first'/>맨 앞
            </label>
            <label>
                <span id='spnPage'></span>
                <input type='radio' name='rdLocation' value='rear'/>뒤    
                <script>
                    js_make_number_select_box("sltPage","패이지",'<?=$catalogPageCnt?>',"#spnPage");
                </script>

            </label>
            <label>
                <input type='radio' name='rdLocation' value='last' checked='checked'/>맨 뒤</label>
            </label>
            <br/>
            <input type='button' name='btnChangeLocation' id='btnChangeLocation' value='위치 변경' onclick="js_change_page_location('<?=$catalogPageCnt?>','<?=$page?>');"/>
    </body>

</html>