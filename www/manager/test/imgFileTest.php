<?
    require "../../_classes/com/db/DBUtil.php";
    require "../../_classes/com/util/Util.php";
    $conn = db_connection("w");
?>
<html>
    <head>
        <script>
            function js_read(){
                var frm=document.frm;
                frm.mode.value="FR";
                frm.action="<?=$_SERVER[PHP_SELF]?>";
                frm.submit();
            }
        </script>
    </head>
    <body>
        <form name="frm" entype="multipart/form-data" method="POST">
            <input type="file" name="file_nm_100" size="20">
            <img src="" width="100">

            <input type="hidden" name="mode" value="<?=$mode?>">
        </form>

    </body>
</html>