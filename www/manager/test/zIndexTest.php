<?

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="EUC-KR">
    <meta http-equiv="Content-Type" content="text/html; charset=EUC-KR"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
        /** */
        div{
            width:100px;
            height:100px;
        }
        div.lowDiv{
            background-color: yellow;
            z-index: 1;
            /* position:relative; */
        }
        div.midDiv{
            background-color: yellowgreen;
            z-index: 2;
            position:absolute; top:30px; left:30px;
            
        }
        div.highDiv{
            background-color: green;
            z-index: 3;
            position:absolute; top:100px; left:100px;
        }
    </style>
</head>
<body>
    <div class="lowDiv">
        test
        
    </div>
    <div class="midDiv">
        test1
    </div>
    <div class="highDiv">
        test2
    </div>
    
</body>
</html>