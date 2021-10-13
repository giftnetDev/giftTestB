<?
	require "_common/home_pre_setting.php";
	
	require "_classes/biz/board/catalog_pop.php";

    $arr_rs     = pop_Sel_catalog($conn);
    $FILEPATH	= trim($arr_rs[0]["FILEPATH"]);
?>

    
<!DOCTYPE html>
<head>
    <title>카달로그</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <script src="../js/jquery.js"></script>
	<script type="text/javascript" src="/manager/jquery/jquery.cookie.js"></script>
    <style>
        ul{
            padding:0px;
        }

        li{
            padding-left:40px;
            padding-top:10px;
        }
        
        li:hover{
            background-color:#f0f0f0;
            cursor: pointer;
        }
        hr{
            margin-bottom: 0px;
        }
    </style>
<script>
	$(function(){
		
		$(".popup_stop").change(function(){
			if($(this).is(":checked")) { 
				$.cookie('notice_pop', '<?=date("Y-m-d",strtotime("0 day"))?>'); 
				self.close();
			}
		});

	});
</script>    
        
</head>
<body>
    <br>
    <div style="overflow-y:auto;">
        <ul id = "company_list" style="list-style:none;">
        <img name="catalog_img" src="<?=$FILEPATH?>" style="max-height:900px; max-width:400px;"/>
        </ul>
    </div>
	<div style="text-align:right; background-color:#ddd; padding:0px;">
		<label><input type="checkbox" class="popup_stop" value="Y"/>&nbsp;오늘 그만 보기&nbsp;&nbsp;</label>	
	</div>
</body>
</html>