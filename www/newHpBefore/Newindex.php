<?
	require "_common/home_pre_setting.php";
	require "_classes/biz/board/catalog_pop.php";

	// print_r($_SESSION);

    $CNT     = pop_Sel_catalog_cnt($conn);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
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
<?
	require "header.php";
?>
</head>
<body>

<div class="wrap">

<?//<!-- Top -->
	// print_r($_SESSION);
	require "top.php";
?>
<?//<!-- sub -->
	require "main.php";
?>
</div>

<?//<!-- footer-->
	require "footer.php";
?>
	<form name="frm">
		<input type="hidden" name="mode">
		<input type="hidden" name="curUrl" value="Newindex.php">
		<div id="login_popup">
			<div class="dark_wall"></div>
			<div class="login_pop">
				<div class="login_pop_x">X</div>
				<div class="cart_info">
					<br>
					<h2>
						Login / Join
					</h2>
					<div class="tcenter" style="margin-top:40px;">
						<input type="text" placeholder="아이디" class="id" name="iid"><br>
						<input type="password" placeholder="패스워드" class="pw" name="pwd"><br>
						<div class="tcenter_for_check"><input type="checkbox" id="id_save"> <label for="id_save" style="cursor:pointer;">아이디 저장</label></div>
						<a href="javascript:js_login();" class="joomoon login_run">로그인</a>
				</div>
				<div class="tcenter_02" style="margin-top:-40px;">
					<a href="#">아이디 / 비밀번호 찾기</a>
				</div>
							
			</div><!--login_pop-->
		</div>
	
	</form>

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
	<script src="js/jquery.mousewheel.js"></script> 
        <script>
            $(function() {
            $(".wrapper").mousewheel(function(event, delta) {
            this.scrollLeft -= (delta * 120);
            event.preventDefault();
            });
            })

            
        </script>
        <script type="text/javascript">
            document.addEventListener("mousemove", parallax);
            function parallax(e) {
            
                this.querySelectorAll('.elements').forEach(Layer => {
                const speed = Layer.getAttribute('data-speed');
                const x = (window.innerWidth - e.pageX*speed)/100;
                Layer.style.transform = 'translateX(${x}px)';
            })
            }
            
        </script>



</body>
</html>
