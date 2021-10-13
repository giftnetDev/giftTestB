<?
	require "_common/home_pre_setting.php";
	// require "./_classes/com/db/DBUtil.php";
	// require "_classes/com/etc/etc.php";

	// $conn=db_connection("w");
?>
<?	
	require "_classes/biz/board/catalog_pop.php";

    $CNT     = pop_Sel_catalog_cnt($conn);
?>
<!DOCTYPE html>
<script>
	function js_notice_pop() 
	{				
		//alert($("input[name=CNT]").val());
		if($("input[name=CNT]").val() != 0)		//카다로그 팝업 데이터가 있어야만 팝업 나오도록..
		{
			window.open("pop_catalog_notice.php","pop_catalog_notice","status=no ,location=no, directoryies=no, resizable=no, scrollbars=no, titlebar=no, width=360, height=958, top=0 left=0");
		}
	}
</script>
<html lang="ko">
<head>
	<!--<meta charset="euc-kr">
	<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width">
	<meta name="description" content="기프트넷" />
	<title>기프트넷</title>
	<link rel="icon" type="image/x-icon" herf="/" />
	<script type="text/javascript" src="newDesign/js/jquery-1.11.2.min.js"></script>
	<script type="text/javascript" src="newDesign/js/jquery_ui.js"></script>
	<script type="text/javascript" src="newDesign/js/jquery.easing.1.3.js"></script>
	<script type="text/javascript" src="newDesign/js/slick.js"></script>
	<script type="text/javascript" src="newDesign/js/common_ui.js"></script>
	<link type="text/css" rel="stylesheet" href="newDesign/css/reset.css" />-->

<meta name="google-site-verification" content="Ukdz4SbMNGExyZir317AFjOMQ94UI5MbfCp4vcH6wIM" />
	<?
		require "_common/v2_header.php";
	?>
</head>
<body id="">
	<div id="wrap">
		<div class="header">

	
			<?
				require "_common/v2_top.php";
			?>
		</div>
		<!-- class='header' -->
	
<!-- Main Best -->
		<div class="container">
			<div class="maincontents">
				<?
					//require "_main_best.php";
				?>
<!-- // Main Best -->
<!-- 브랜드관 -->
<!--

	
				<?
					//require "_main_special.php";
				?>
				<?
					//require "_main_recommand.php";
				?>
    
</div>
-->
<!-- // 브랜드관 -->
<!-- 최근 등록 상품 -->
				<?
					require "_main_latest.php";
				?>
<!-- // 최근 등록 상품 -->
				<?
					// require "_common/v2_footer.php";
				?>
			</div><!--class="maincontents"-->
		</div><!--class="container-->
	</div><!--class="wrap"-->

<script src="../js/jquery.js"></script>
<script type="text/javascript" src="../manager/jquery/jquery.cookie.js"></script>
<script>
			$(document).ready(function(){
				
				if($.cookie("notice_pop") != '<?=date("Y-m-d",strtotime("0 day"))?>') 
				{	
					js_notice_pop();
				}
			});			
</script>

</body>
</html>
