<?session_start();?>
<?
#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/goods/goods.php";

	function selectGoodsRequestWarehose($db, $goods_no) { 

		$query = "SELECT COUNT(*)
					FROM TBL_GOODS_REQUEST_WAREHOUSE  
				   WHERE REQ_DATE = CURDATE() AND GOODS_NO = '$goods_no' AND DEL_TF = 'N'";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}
?>

<!DOCTYPE html>
<html lang="ko-kr">
  <head>
    <meta charset="<?=$g_charset?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

	<title><?=$g_title?>-창고재고/발주체크</title>

    <!-- Bootstrap -->
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

	<style>
		.container {position:relative;}
		.more_info, .glyphicon-minus, .alert{display:none;} 
		.basic_info, .request, .requested {cursor:pointer;}
		.alert-fixed {
			position:fixed; 
			top: 0px; 
			left: 0px; 
			width: 100%;
			z-index:9999; 
			border-radius:0px
		}
	</style>
  </head>
  <body>
	<form name="frm" method="post">
		<div class="container">

			<div class="form-group">
				<label for="kancode">자재코드/상품명/바코드 중 입력</label>
				<input type="text" id="barcode" name="kancode" class="form-control" placeholder="스캔 또는 입력" autofocus />
			</div>

			<table class="table">
			<colgroup>
				<col width="15%" />
				<col width="*" />
				<col width="10%" />
				<col width="10%" />
				<col width="10%" />
			</colgroup>
			<thead>
				<th>코드</th>
				<th>상품명</th>
				<th>입수</th>
				<th>재고</th>
				<th>발주</th>
			</thead>
			<?
				if($kancode <> "") { 

					$con_cate = "01";
					$start_date = ""; 
					$end_date = "";
					$start_price = ""; 
					$end_price = "";
					$con_cate_01 = ""; 
					$con_cate_02 = ""; 
					$con_cate_03 = "";
					$con_cate_04 = ""; 
					$con_delivery_profit = ""; 
					$con_delivery_fee = ""; 
					$con_tax_tf = ""; 
					$con_use_tf = "Y"; 
					$del_tf = "N";
					$search_field = "ALL";
					$search_str = $kancode;
					$order_field = "";
					$order_str = "";
					$nPage = "1";
					$nPageSize = "10000";
					$exclude_category = "";

					$arr_rs = listGoods($conn, $con_cate, $start_date, $end_date, $start_price, $end_price, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $con_delivery_profit, $con_delivery_fee, $con_tax_tf, $con_use_tf, $del_tf, $search_field, $search_str, $exclude_category, $order_field, $order_str, $nPage, $nPageSize);

					if(sizeof($arr_rs) > 0) {
						for($i = 0; $i < sizeof($arr_rs); $i ++) { 
							$rs_goods_no			= $arr_rs[$i]["GOODS_NO"];
							$rs_goods_code			= $arr_rs[$i]["GOODS_CODE"];
							$rs_kancode				= $arr_rs[$i]["KANCODE"];
							$rs_goods_name			= $arr_rs[$i]["GOODS_NAME"];
							$rs_goods_sub_name		= $arr_rs[$i]["GOODS_SUB_NAME"];
							$rs_stock_cnt			= $arr_rs[$i]["STOCK_CNT"];
							$rs_fstock_cnt			= $arr_rs[$i]["FSTOCK_CNT"];
							$rs_delivery_cnt_in_box = $arr_rs[$i]["DELIVERY_CNT_IN_BOX"];
							$rs_memo				= $arr_rs[$i]["MEMO"];

							$cnt = selectGoodsRequestWarehose($conn, $rs_goods_no);

							$chk_request = listRequestedGoodsRequest($conn, $rs_kancode);
							$chk_request_stock_cnt = listRequestedGoodsRequestStockCnt($conn, $rs_kancode, $rs_goods_code);

			?>
			<tr class="basic_info" data-goods_no="<?=$rs_goods_no?>">
				<td>
					<span class="glyphicon glyphicon-minus basic_info" data-goods_no="<?=$rs_goods_no?>" aria-hidden="true"></span>
					<span class="glyphicon glyphicon-plus basic_info" data-goods_no="<?=$rs_goods_no?>" aria-hidden="true"></span>
					<?=$rs_goods_code?>
				</td>
				<td>
					<?=$rs_goods_name." ".$rs_goods_sub_name?>
				</td>
				<td>
					<?=$rs_delivery_cnt_in_box?>
				</td>
				<td>
					<?=$rs_stock_cnt?>
				</td>
				<td>
					<?=$rs_fstock_cnt?>
				</td>
			</tr>
			<tr class="more_info" data-goods_no="<?=$rs_goods_no?>">
				<td></td>
				<td colspan="2"><?if($chk_request <> "") echo "발주수량: ".$chk_request."<br/>"; ?>
								<?if($chk_request_stock_cnt <> "") echo "재고수량: ".$chk_request_stock_cnt."<br/>"; ?>
								<?if($rs_memo <> "") echo "비고: ".$rs_memo; ?>
				</td>
				<td colspan="2">
					<? if($rs_fstock_cnt == 0) { ?>
						<a href="#" class="request" <? if($cnt > 0) echo "style='display:none;'"; ?> data-goods_no="<?=$rs_goods_no?>"><span class="glyphicon glyphicon-download-alt" aria-hidden="true">발주요청</span></a>
						<a href="#" class="requested" <? if($cnt <= 0) echo "style='display:none;'"; ?> data-goods_no="<?=$rs_goods_no?>"><span class="glyphicon glyphicon-ok" aria-hidden="true">요청됨</span></a>
					<? } ?>
				</td>
			</tr>
			<?
						}
					}
				}
			?>
		</table>

		<div class="alert alert-success alert-fixed" role="alert">
		  <strong>발주 요청이 완료되었습니다.</strong>
		</div>

		<div class="alert alert-info alert-fixed" role="alert">
		  <strong>발주 요청이 취소되었습니다.</strong>
		</div>

		<div class="alert alert-danger alert-fixed" role="alert">
		  <strong>처리중 에러가 발생했습니다.</strong>
		</div>

		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

		<!-- Latest compiled and minified JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

		<script>
			$(function(){
				$("input[name=kancode]").keydown(function(event){
					if(event.keyCode == 13)
					{
						var frm = document.frm;

						frm.target = "";
						frm.action = "<?=$_SERVER[PHP_SELF]?>";
						frm.submit();
					}
				});

				$(".basic_info").click(function(){
					var goods_no = $(this).data("goods_no");

					$("tr.more_info[data-goods_no="+goods_no+"]").toggle();
					$(".glyphicon-minus[data-goods_no="+goods_no+"]").toggle();
					$(".glyphicon-plus[data-goods_no="+goods_no+"]").toggle();

				});
				
				/*
				$('.basic_info').touchstart(function(){
					var goods_no = $(this).data("goods_no");

					$("tr.more_info[data-goods_no="+goods_no+"]").toggle();
					$(".glyphicon-minus[data-goods_no="+goods_no+"]").toggle();
					$(".glyphicon-plus[data-goods_no="+goods_no+"]").toggle();

				});
				*/

				$(".request").click(function(e){
					
					e.preventDefault();

					var goods_no = $(this).data("goods_no");

					$.ajax({
						  url: "json_goods_request.php",
						  dataType: 'json',
						  async: true,
						  data: {goods_no: goods_no, mode: 'insert'},
						  success: function(data) {
							$.each( data, function( i, item ) {
								
								if(item.RESULT == "1")
								{
									var goods_no = item.GOODS_NO; 
									$(".request[data-goods_no="+goods_no+"]").hide();
									$(".requested[data-goods_no="+goods_no+"]").show();

									$(".alert-success").show();
								}else
								{
									$(".alert-danger").show();
								}

							  });
						  }
						});

					setTimeout(function () {
							$(".alert").hide();
						}, 2000);

				});

				$(".requested").click(function(e){

					e.preventDefault();

					var goods_no = $(this).data("goods_no");

					//cancel request
					$.ajax({
						  url: "json_goods_request.php",
						  dataType: 'json',
						  async: true,
						  data: {goods_no: goods_no, mode: 'cancel'},
						  success: function(data) {
							$.each( data, function( i, item ) {
								
								if(item.RESULT == "1")
								{
									var goods_no = item.GOODS_NO;
									$(".request[data-goods_no="+goods_no+"]").show();
									$(".requested[data-goods_no="+goods_no+"]").hide();

									$(".alert-info").show();
								}else
								{
									$(".alert-danger").show();
								}

							  });
						  }
						});
					
					

					setTimeout(function () {
							$(".alert").hide();
						}, 2000);

				});

			});
		
		</script>

		<script>
		  $(document).ready(function() {
			if (!("autofocus" in document.createElement("input"))) {
			  $("#barcode").focus();
			}
		  });
		</script>
		</div>
	</form>
  </body>
</html>

<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>