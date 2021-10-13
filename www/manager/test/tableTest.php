<?

?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=EUC-KR"/>
        <script type="text/javascript" src="../js/common.js"></script>
        <script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
        <script>
            var gMax=12;

            function js_alterTableValue(){
                alert('js_alterTableValue() 실행');
                var txtNum=$("#txtNum").val();
                var txtContent=$("#txtContent").val();
                if(trim(txtNum)=="" || trim(txtContent)==""){
                    alert('공란이 있습니다');
                    return;
                }
                var tdId=txtNum+"td";
                alert(tdId);
                document.getElementById(tdId).innerText=txtContent;


            }
            function js_createTable(location, tableId, row, col)
            {
                var table="<table border='1' id='"+tableId+"' name='"+tableId+"'>";
                for(var i=0;i<row;i++)
                {
                    table+="<tr>";
                    for(var j=0;j<col;j++)
                    {
                        table+="<td id='"+Number(i*4+j)+"td'>"+Number(i*4+j)+"</td>";
                    }
                    table+="</tr>";
                }
                $(location).html(table);
            }

        </script>
    </head>
    <body>
        <div value="A">
            <input type="button" id="btnCreateTable" onclick="js_createTable('#lblTable','table1',3,4)" value="표 만들기"/>
            <br/>
            <label for="lblNum">&nbsp;&nbsp;선택할 TD : <input type="text" id="txtNum" name="txtNum"/></label>
            <br/>
            <label for="lblContent">입력할 내용 : <input type="text" id="txtContent" name="txtContent"/></label>
            <br/>
            <input type="button" id="btnAlterContent" value="내용 바꾸기" onclick="js_alterTableValue()"/>

        </div>
        <div>
            <label id="lblTable"></label>
        </div>

    </body>
</html>
