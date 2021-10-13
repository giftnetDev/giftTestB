<?	
	require "../_common/home_pre_setting.php";  
	require "../_classes/biz/board/catalog_pop.php";
	header("Cache-Control: no-cache");
?>
<?
	// require "../_classes/com/db/DBUtil.php";
	$conn = db_connection("w");

    $mem_no=$_SESSION['C_MEM_NO'];

	$CNT   = pop_Sel_catalog_cnt($conn);	
	
?>
<?	
	function getRecentGoods($db, $search_field, $search_str, $arr_options, $order_field, $order_str, $nPage, $nRowCount, $total_cnt){
		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		// $query_r = "set @rownum = ".$logical_num ."; ";
		// mysql_query($query_r,$db);

		$query = "SELECT 		@rownum:= @rownum - 1  as rn, GOODS_NO, GOODS_CATE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, CATE_01, CATE_02, CATE_03, CATE_04, DELIVERY_CNT_IN_BOX,
								PRICE, BUY_PRICE, SALE_PRICE, EXTRA_PRICE, STOCK_CNT, TAX_TF, IMG_URL, FILE_NM_100, FILE_RNM_100, FILE_PATH_100, FILE_SIZE_100, FILE_EXT_100, 
								FILE_NM_150, FILE_RNM_150, FILE_PATH_150, FILE_SIZE_150, FILE_EXT_150, CONTENTS, CONCEAL_PRICE_TF,
								READ_CNT, DISP_SEQ, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE,
								(SELECT CP_NM FROM TBL_COMPANY WHERE TBL_COMPANY.CP_NO = TBL_GOODS.CATE_03 ) AS CP_NAME,
								DELIVERY_CNT_IN_BOX, STOCK_TF, MSTOCK_CNT, TSTOCK_CNT, FSTOCK_CNT, BSTOCK_CNT, STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, SALE_SUSU, LABOR_PRICE, OTHER_PRICE, NEXT_SALE_PRICE, WRAP_WIDTH, WRAP_LENGTH, WRAP_MEMO, RESTOCK_DATE
				    FROM 		TBL_GOODS 
				   WHERE 		USE_TF = 'Y' 
				     AND 		DEL_TF = 'N' 
				 	 AND 		GOODS_CODE LIKE '%-2%' 
			 		 AND 		GOODS_CATE NOT LIKE '01%' 
					 AND 		CATE_04 = '판매중'
					 AND 		REG_DATE <= curdate() - interval 3 day 
					 AND 		SALE_PRICE > 0
					 AND 		CATE_03 <> 4638
					 AND 		EXPOSURE_TF='Y'
					 AND (
							FILE_RNM_100 <>  ''
						 OR FILE_RNM_150 <>  ''
						  )
					 ";
					 // 매입 (인터넷 매입 - 4638) 제외

		$code_cate = $arr_options["code_cate"];
		if($code_cate != "") { 
			if($code_cate % 100 == 0)
				$query .= " AND GOODS_CODE LIKE  '".substr($code_cate, 0, 1)."%' ";
			else
				$query .= " AND GOODS_CODE LIKE  '".$code_cate."-%' ";
		}

		$start_price = $arr_options["start_price"];
		if ($start_price <> "") {
			$query .= " AND SALE_PRICE >= '".$start_price."' ";
		}

		$end_price   = $arr_options["end_price"];
		if ($end_price <> "") {
			$query .= " AND SALE_PRICE <= '".$end_price."' ";
		}

		if ($search_str <> "") {
			if ($search_field == "ALL") {
				if(is_numeric($search_str)) 
					$query .= " AND (GOODS_NAME like '%".$search_str."%' OR GOODS_SUB_NAME like '%".$search_str."%' OR GOODS_NO = ".$search_str." OR GOODS_CODE LIKE '%".$search_str."%' OR MEMO LIKE '%".$search_str."%') ";
				else
					$query .= " AND (GOODS_NAME like '%".$search_str."%' OR GOODS_SUB_NAME like '%".$search_str."%' OR GOODS_CODE LIKE '%".$search_str."%' OR MEMO LIKE '%".$search_str."%') ";

			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}
		if ($order_field == "") { 
			$order_field = " ORDER BY REG_DATE";
		} else { 
			$order_field = " ORDER BY ".$order_field;
		}

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= $order_field." ".$order_str." limit ".$offset.", ".$nRowCount;

        // echo $query."<br/><br/>";
		// exit;

		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;


	}//end of function
?>
<?// PROCESS_START
	$arr_rs=pop_Sel_catalog($conn);
	$filePath=trim($arr_rs[0]["FILEPATH"]);

	$arr_goods=getRecentGoods($conn, $search_field_X, $search_str, $arr_options_X, $order_field_X, $order_str_X, 1,100,1000 );
	$cntGoods=sizeof($arr_goods);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<?
	require "./Mheader.php";
	require "./Msearch.php";
?>
<script>

function js_close_popup()
{
	$("#popup_catalog").css("display","none");
}

function js_add_goods_view()
{
	var totalCntGoods=Number($("input[name='totalGoodsCnt']").val());

	var cnt=Number($("input[name='goodsCnt']").val());
	var from=cnt*12;                                                //더보기 클릭 시 12개만 나오도록..
	var nextCnt=(cnt+1)*12;

	if(totalCntGoods<=nextCnt){
		nextCnt=totalCntGoods;
		$(".product_list_more").css("display","none");
	}
	else{
		$("input[name='goodsCnt']").val(cnt+1);
	}
	for(i=from; i<nextCnt; i++){
		$("#dv_product_list_cell_"+i).css("display","block");
	}

}

function js_notice_pop() 
{				
	if($("input[name=CNT]").val() != 0)		//카다로그 팝업 데이터가 있어야만 팝업 나오도록..
	{
		$("#popup_catalog").show();
	}
}
</script>
	
	
</head>
<body>
	<div class="wrap">


		<!-- <div id="dv_search" style="width:100%; height:50px; background-color:#FFFFFF; opacity:0.8;margin-top:-27px; text-align:center; position:fixed; z-index:10; border:solid #DDDDDD 2px;">
			<input type="text" id="search_str">
			<button type="button"></button>
		 <input type="button"> -->
			<!-- <input type="button" id="search_button"/> -->
		<!--</div> -->
		<!-- <input type="text"> -->
		<div class="product_list">
		<?
			if($cntGoods>0){
                if($cntGoods>11){        //더보기 클릭 시 12개만 나오도록..
                    $extendflag='Y';
                }
                else{
                    $extendflag='N';
                }
				for($i=0; $i<$cntGoods; $i++){
					$CUR_GOODS_NO		=	$arr_goods[$i]["GOODS_NO"];
					$CUR_GOODS_CODE		=	$arr_goods[$i]["GOODS_CODE"];
					$CUR_GOODS_NAME		=	$arr_goods[$i]["GOODS_NAME"];
					$CUR_SALE_PRiCE		=	$arr_goods[$i]["SALE_PRICE"];

					$CUR_FILE_NM_100	=	$arr_goods[$i]["FILE_NM_100"];
					$CUR_IMG_URL		=	$arr_goods[$i]["IMG_URL"];
					$CUR_FILE_PATH_150	=	$arr_goods[$i]["FILE_PATH_150"];
					$CUR_FILE_RNM_150	=	$arr_goods[$i]["FILE_RNM_150"];

					$concel_tf			=   $arr_goods[$i]["CONCEAL_PRICE_TF"];

					$CUR_IMG_URL		=	getGoodsImage($CUR_FILE_NM_100, $CUR_IMG_URL, $CUR_FILE_PATH_150, $CUR_FILE_RNM_150,150,150);


				?>
					<div class="product_list_cell" id="dv_product_list_cell_<?=$i?>">
						<dl style="cursor: pointer;" onclick="location.href='Mgoods_info.php?goods_no=<?=$CUR_GOODS_NO?>'">
						<div class="img" style="background:url('<?=$CUR_IMG_URL?>') no-repeat;background-size:auto 100%; background-position:center center"></div>
						<b>
							<span><?=$CUR_GOODS_CODE?></span>
							<?=$CUR_GOODS_NAME?>
						</b>
						<?
                            if($mem_no <> "")
                            {	
                            ?>
                                <i><i><?=number_format($CUR_SALE_PRiCE)?></i> 원</i>
                            <?
                            }
                            else
                            {	
                                if($concel_tf != "Y")
                                {   
                            ?>      <i><i><?=number_format($CUR_SALE_PRiCE)?></i> 원</i>
                            <?	}
                                else
                                {   
                                ?>
                                    <i><i>가격문의</i></i>
                            <?
                                }
                            } 
                        ?>
						</dl>	
					</div>
					<script>
						if(Number('<?=$i?>')>11){        //더보기 클릭 시 12개만 나오도록..
							$("#dv_product_list_cell_<?=$i?>").css("display","none");
						}
					</script>
				<?
				}//end of for(cntGoods)
				$goodsCnt=1;
			}//end of if(cntGoods>0)
		?>

		
		</div><!--product_list-->
		<?
			if($extendflag=='Y'){
			?>
				<div class="product_list_more"><button type="button" onclick="js_add_goods_view();">더보기</button></div>
			<?
			}
		?>
<?
	require "Mfooter.php";
?>

	</div>


	<div class="popup_frame" id="popup_catalog">
		<div class="dark_wall"></div>
		<div class="popup_body">
			<div class="popup_area_right">
				<span class="popup_btn" id="btn_no_day">N</span>
				<span class="popup_btn" onclick="js_close_popup()">X</span>
			</div>
			<!-- <div class="popup_area_center" style="background:url('<?=$filePath?>')no-repeat;background-size:auto 100%;background-position:center center; height:100%;"> -->
			<?
				if($filePath<>""){
				?>
					<div class="popup_area_center">
						<img src="<?=$filePath?>" style="width: 100%; height:100%; object-fit: fill;">
					</div>
				<?
				}
			?>
		</div>


	</div>
	<input type="hidden" name="goodsCnt" value="<?=$goodsCnt?>">
	<input type="hidden" name="extendflag" value='<?=$extendflag?>'>
	<input type="hidden" name="CNT" value="<?=$CNT?>" />



<!------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
	

<!------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
</body>
<script>
	$(document).ready(function(){

		$("#btn_no_day").click(function(){
			$("#popup_catalog").remove();
			//쿠키 추가
			$.cookie('chk_popup_catalog', '<?=date("Y-m-d",strtotime("0 day"))?>'); 
		});

	});
	if($.cookie('chk_popup_catalog') != '<?=date("Y-m-d",strtotime("0 day"))?>') 
	{
		//$("#popup_catalog").show();
		$("#popup_catalog").hide();
		js_notice_pop();
    }
	else
	{
		$("#popup_catalog").remove();
	}
</script>


</html>
