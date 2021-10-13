
    <footer>
		<? 
			$arr_hp_f_c2 = listDcode($conn, "HOME_FOOTER2_CONTENT", 'Y', 'N', '', '', 1, 100);
			for($q = 0; $q < sizeof($arr_hp_f_c2); $q++) 
			{ 
				$footer_id = $arr_hp_f_c2[$q]["DCODE"];
				$footer_name = $arr_hp_f_c2[$q]["DCODE_NM"];
				$footer_url = $arr_hp_f_c2[$q]["DCODE_EXT"];

				if($footer_url <> "") 
				{ ?>
					<a href="<?=$footer_url?>"><?=$footer_name?></a>
		<?		} 
				else 
				{ ?>
					<a href="content_info.php?no=<?=$footer_id?>"><?=$footer_name?></a>
		<?		}
			}
		
		?>
        <b>
            (주)기프트넷 | 경기도 남양주시 진건읍 배양리 98번지 (주)기프트넷 ㅣ 대표이사 양진현 ㅣ 사업자등록번호 132-81-58846 ㅣ 통신판매업고신고 2005-경기남양주-0238<br>
            본 페이지와 상품이미지 저작권은 (주)기프트넷에 있습니다. 상품내용 및 이미지의 무단복제를 금합니다.<br>
			Copyright &copy; 2016 giftnet ALL Rights Reserved.
        </b>
        <div class="more"></div>
        <div class="infopop">
            <b>고객센터</b><br>
            <span>TEL : 031-527-6812</span>
            <span>평일 10시 ~ 17시 </span>
			<span>(점심 12시 ~ 13시)</span>
            <span>FAX   :  031-527-6858</span>

            <div class="div_line"></div><br>

            <b>입금계좌</b><br>
            농협은행<br>
            <span>351-1137-6964-13</span>
            <span>예금주 기프트넷</span>
			
            <div class="div_line"></div><br>

            <b>문의</b><br>gift@giftnet.co.kr

            <div class="cl"></div>
        </div>
    </footer>
<!-- // Banner -->
	<div id="catalog_banner" style="display:none;" class="banner hidden-xs">
		<div class="title">
			<style>
				#close_catalog:hover{
					cursor:pointer;
				}
				#hide_catalog{
					cursor:pointer;
				}
			</style>
			<a href="<?=getDcodeExtByCode($conn, "HOME_BANNER", "NEW_BANNER_TITLE" )?>" class="banner_title">
			<span><?=getDcodeName($conn, "HOME_BANNER", "BANNER_TITLE" )?><span></a>&nbsp;&nbsp;
						
			<span title="닫기" style="color:#337ab7;cursor:pointer;float:right;margin-right:0px;" id="close_catalog"><img src="img/x.png" alt=""></span>
			<span title="하루 동안 숨기기" style="color:#337ab7;cursor:pointer;float:right;" id="hide_catalog"><img src="img/icon_01.png" alt=""></span>	
			<span title="축소" class="btn_toggle_size_small" style="color:#337ab7;cursor:pointer;float:right;margin-right:0px;" id="minus_catalogue"><img src="img/icon_02_minus.png" alt=""></span>			
		</div>
		<div class="banner_body">
			<a href="<?=getDcodeExtByCode($conn, "HOME_BANNER", "NEW_BANNER_TITLE" )?>"><img src="<?=getDcodeExtByCode($conn, "HOME_BANNER", "BANNER_BODY" )?>"></a>
		</div>
	</div>

	<div id="catalog_banner_small" class="banner hidden-xs smaller"  style="display:none;">
		<div class="title" style="width:150px;">
			<style>
				#close_catalog:hover{
					cursor:pointer;
				}
				#hide_catalog{
					cursor:pointer;
				}
			</style>
			
			<span title="닫기" style="color:#337ab7;cursor:pointer;float:right;margin-right:0px;" id="close_catalog_small"><img src="img/x.png" alt=""></span>
			<span title="하루 동안 숨기기" style="color:#337ab7;cursor:pointer;float:right;" id="hide_catalog_small"><img src="img/icon_01.png" alt=""></span>	
			<span title="확장" class="btn_toggle_size" style="color:#337ab7;cursor:pointer;float:right;margin-right:0px;" id="plus_catalogue"><img src="img/icon_02.png" alt=""></span>
		</div>
		<div class="banner_body">
			<a href="<?=getDcodeExtByCode($conn, "HOME_BANNER", "NEW_BANNER_TITLE" )?>"><img src="<?=getDcodeExtByCode($conn, "HOME_BANNER", "BANNER_BODY" )?>"></a>
		</div>		
	</div>


<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
<!--<script src="js/jquery-1.11.3.min.js"></script> -->
<!--<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>-->

<!-- Include all compiled plugins (below), or include individual files as needed --> 
<!--<script src="js/bootstrap.js"></script>-->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script type="text/javascript" src="/js/jquery.cookie.js"></script>

<script src="js/common.js"></script>

<style>
	.infopop.shrink {display:inline;}
	.infopop.shrink_none {display:none;}
</style>

<script type="text/javascript">

	$(function(){

		if($.cookie("is_shrink") == "Y") 
		{ 
			$(".infopop").removeClass("shrink");	
			$(".infopop").addClass("shrink_none");	
			$(".banner").css("right",0);
		}
		else
		{
			$(".infopop").removeClass("shrink_none");	
			$(".infopop").addClass("shrink");	
		}

		$(".more").click(function(e)
		{		
			$.cookie("is_shrink", "N");
		});

		$(".cl").click(function(e)
		{			
			$.cookie("is_shrink", "Y");
		});


		if($.cookie("mg_size") == "Y") 
		{ 
			$("#catalog_banner").css("display","none");
			$("#catalog_banner_small").css("display","block");
		}
		else
		{
			$("#catalog_banner").css("display","block");
			$("#catalog_banner_small").css("display","none");
		}

		$(".btn_toggle_size").click(function(e)
		{
			$.cookie("mg_size", "N");			
		});

		$(".btn_toggle_size_small").click(function(e)
		{
			$.cookie("mg_size", "Y");			
		});

	});

	$(document).ready(function(){
		$("#close_catalog").click(function(){
			$("#catalog_banner").remove();
		});

		$("#close_catalog_small").click(function(){
			$("#catalog_banner_small").remove();
		});

		$("#hide_catalog").click(function(){
			$("#catalog_banner").remove();
			//쿠키 추가
			$.cookie('chk_latest_banner', '<?=date("Y-m-d",strtotime("0 day"))?>'); 
		});

		$("#hide_catalog_small").click(function(){
			$("#catalog_banner_small").remove();
			//쿠키 추가
			$.cookie('chk_latest_banner_small', '<?=date("Y-m-d",strtotime("0 day"))?>'); 
		});
	});

	//하루 동안 안 보기 날짜 지났으면 배너 띄움
	if($.cookie('chk_latest_banner') != '<?=date("Y-m-d",strtotime("0 day"))?>') 
	{
		$("#catalog_banner").show();
    }
	else
	{
		$("#catalog_banner").remove();
	}

	//하루 동안 안 보기 날짜 지났으면 배너 띄움
	if($.cookie('chk_latest_banner_small') != '<?=date("Y-m-d",strtotime("0 day"))?>') 
	{
		$("#catalog_banner_small").show();
    }
	else
	{
		$("#catalog_banner_small").remove();
	}

</script>