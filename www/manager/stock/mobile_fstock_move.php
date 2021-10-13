<?session_start();?>
<?
#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "SG022"; // 메뉴마다 셋팅 해 주어야 합니다

#====================================================================
# common_header Check Session
#====================================================================
	require "../../_common/common_header.php"; 

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/stock/stock.php";

	if($page_mode == "")
		$page_mode = "F";

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

?>

<!DOCTYPE html>
<html lang="ko-kr">
  <head>
    <meta charset="<?=$g_charset?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta name="format-detection" content="telephone=no" />

	<title><?=$g_title?>-가입고전환</title>

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
		<input type="hidden" name="page_mode" value="<?=$page_mode?>" />
		<div class="container">
			<ul class="nav nav-pills">
			  <li role="presentation" class="active"><a href="/manager/stock/mobile_fstock_move.php">가입고전환</a></li>
			  <li role="presentation"><a href="/manager/stock/mobile_stock_info.php">재고현황</a></li>
			  <li role="presentation"><a href="/manager/">PC버전</a></li>
			</ul>
			<br/>
			<div class="form-group">
				<label for="kancode">자재코드/상품명/바코드 중 입력</label>
				<input type="text" id="barcode" name="kancode" class="form-control" value="<?=$kancode?>" placeholder="스캔 또는 입력" autofocus />
			</div>

			<ul class="nav nav-tabs">
			  <li role="presentation" <? if ($page_mode == "F") echo "class='active'"; ?>><a href="<?=$_SERVER['PHP_SELF']?>?page_mode=F&kancode=<?=$kancode?>">가입고</a></li>
			  <li role="presentation" <? if ($page_mode == "N") echo "class='active'"; ?>><a href="<?=$_SERVER['PHP_SELF']?>?page_mode=N&kancode=<?=$kancode?>">정상입고</a></li>
			  <li role="presentation" <? if ($page_mode == "B") echo "class='active'"; ?>><a href="<?=$_SERVER['PHP_SELF']?>?page_mode=B&kancode=<?=$kancode?>">불량입고</a></li>
			  <li role="presentation" <? if ($page_mode == "FO") echo "class='active'"; ?>><a href="<?=$_SERVER['PHP_SELF']?>?page_mode=FO&kancode=<?=$kancode?>">발주반품출고</a></li>
			</ul>

			<table class="table">
			<colgroup>
				<col width="10%" />
				<col width="*" />
				<col width="10%" />
				<col width="50%" />
			</colgroup>
			<thead>
				<th></th>
				<th>상품명</th>
				<th>수량</th>
				<th>사유상세</th>
			</thead>
			<?
				$start_date = ""; 
				$end_date = "";
				$con_stock_type = "IN";

				if($kancode == "")
					$start_date = date("Y-m-d",strtotime("-6 month"));

				if($page_mode == "F" || $page_mode == "FO")
					$con_stock_code = "FST02";
				else if($page_mode == "N")  
					$con_stock_code = "NST01";
				else if($page_mode == "B")  
					$con_stock_code = "BST03";
				
				$sel_cp_type2 = "";
				$con_out_cp_no = "";
				$sel_loc = "";
				$del_tf = "N";

				$search_field = "FSTOCK_ALL";
				$search_str = $kancode;
				$order_field = "REG_DATE";
				$order_str = "DESC";
				$nPage = "1";
				$nPageSize = "10000";
				$exclude_category = "";
				$nListCnt = "10000";

				$arr_rs = listStock($conn, $start_date, $end_date, $con_stock_type, $con_stock_code, $sel_cp_type2, $con_out_cp_no, $sel_loc, $filter, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize, $nListCnt);

				if(sizeof($arr_rs) > 0) {
					for($i = 0; $i < sizeof($arr_rs); $i ++) { 

						$IN_DATE						= trim($arr_rs[$i]["IN_DATE"]);
						$STOCK_NO						= trim($arr_rs[$i]["STOCK_NO"]);
						$GOODS_NAME						= SetStringFromDB(trim($arr_rs[$i]["GOODS_NAME"]));
						$GOODS_SUB_NAME					= SetStringFromDB(trim($arr_rs[$i]["GOODS_SUB_NAME"]));
						$GOODS_CODE						= SetStringFromDB(trim($arr_rs[$i]["GOODS_CODE"]));
						$IN_PRICE						= trim($arr_rs[$i]["IN_PRICE"]);
						$IN_QTY							= trim($arr_rs[$i]["IN_QTY"]);
						$IN_BQTY						= trim($arr_rs[$i]["IN_BQTY"]);
						$IN_FQTY						= trim($arr_rs[$i]["IN_FQTY"]);
						$PAY_DATE						= trim($arr_rs[$i]["PAY_DATE"]);
						$IN_CP_NO						= trim($arr_rs[$i]["IN_CP_NO"]);
						$STOCK_CODE						= trim($arr_rs[$i]["STOCK_CODE"]);
						$IN_LOC							= trim($arr_rs[$i]["IN_LOC"]);
						$IN_LOC_EXT						= trim($arr_rs[$i]["IN_LOC_EXT"]);
						$RESERVE_NO						= trim($arr_rs[$i]["RESERVE_NO"]);
						$MEMO							= trim($arr_rs[$i]["MEMO"]);
						$REG_DATE						= trim($arr_rs[$i]["REG_DATE"]);
						$IN_DATE						= trim($arr_rs[$i]["IN_DATE"]);
						$PREV_STOCK_NO					= trim($arr_rs[$i]["PREV_STOCK_NO"]);
						$GOODS_CATE						= trim($arr_rs[$i]["GOODS_CATE"]);
						$BB_NO							= trim($arr_rs[$i]["BB_NO"]);

						if($page_mode == "F") { 
							if($IN_FQTY < 0) continue;
						} else if($page_mode == "FO") { 
							if($IN_FQTY > 0) continue;
						}
						
						$REG_DATE			= date("Y-m-d H:i",strtotime($REG_DATE));
						$IN_DATE			= date("Y-m-d H:i",strtotime($IN_DATE));


			?>
			<tr class="basic_info" data-stock_no="<?=$STOCK_NO?>">
				<td>
					<span class="glyphicon glyphicon-minus basic_info" data-stock_no="<?=$STOCK_NO?>" aria-hidden="true"></span>
					<span class="glyphicon glyphicon-plus basic_info" data-stock_no="<?=$STOCK_NO?>" aria-hidden="true"></span>
					<?=$GOODS_CODE?>
				</td>
				<td>
					<?
						if($BB_NO > 0) { 
							echo "<span style='color:red;'>[반품]</span> ";
						}
					?>
					<?=$GOODS_NAME." ".$GOODS_SUB_NAME?>
					
				</td>
				<td>
					<?
						if($page_mode == "F" || $page_mode == "FO") 
							echo $IN_FQTY;
						else if($page_mode == "N") 
							echo $IN_QTY;
						else if($page_mode == "B") 
							echo $IN_BQTY;
					
					?>
					<?
						if(startsWith($GOODS_CATE, "14")) { 
							echo "<span style='color:red; font-size:12px;'> set</span>";
						}
					?>
				</td>
				<td>
					<?=$MEMO?>
				</td>
			</tr>
			<tr class="more_info" data-stock_no="<?=$STOCK_NO?>">
				<td><?=$IN_DATE?></td>
				<td colspan="2"></td>
				<td style="text-align:right;">
					<?=$IN_LOC_EXT?>
					<?
						if($PREV_STOCK_NO <> "") { 
							if($IN_LOC_EXT != "가입고전환-세트해체" && $sPageRight_I == "Y") { 
					?>
						<input type="button" class="undo" value="되돌리기" data-qty="<? if($page_mode == "F") 
							echo $IN_FQTY;
						else if($page_mode == "N") 
							echo $IN_QTY;
						else if($page_mode == "B") 
							echo $IN_BQTY;?>" data-stock_no="<?=$STOCK_NO?>" data-prev_stock_no="<?=$PREV_STOCK_NO?>" />
					<?	
							} else { 
					?>
						<span style='color:red;'>(되돌림불가)</span>
					<?		} 
						} 
					?>
				</td>
			</tr>
			<tr class="more_info" data-stock_no="<?=$STOCK_NO?>">
				<? if($page_mode == "F" || $page_mode == "FO") { ?>
				<td>
					<? if($sPageRight_D == "Y") { ?><input type="button" class="delete" name="bb" style="margin-top:10px;" value="삭제" data-stock_no="<?=$STOCK_NO?>" />
					<? } ?>
				</td>
				<td><input type="text" name="arr_memo" class="input_memo" value="<?=$MEMO?>" style="width:90%;"/></td>
				<td colspan="2" style="text-align:right;">

					<? if($IN_FQTY <> 0 && $sPageRight_I == "Y") { ?>
					<div style="display:inline-block;">정상 : <input type="number" class="input_qty" name="arr_input_qty" value="<?=$IN_FQTY?>" style="width:90px;" pattern="[0-9]*"/></div>
					<div style="display:inline-block;">불량 : <input type="number" class="input_bqty" name="arr_input_bqty" value="0" style="width:90px;" pattern="[0-9]*"/></div>

						<? if($page_mode == "F") { ?>
						<input type="button" class="submit" name="bb" style="margin-left:200px; margin-top:10px;" value="입고" data-stock_no="<?=$STOCK_NO?>" />
						<? } ?>

						<? if($page_mode == "FO") {?>
						<input type="button" class="submit" name="bb" style="margin-left:200px; margin-top:10px;" value="출고" data-stock_no="<?=$STOCK_NO?>" />
						<? } ?>

					<? } ?>
					
				</td>
				
				<? } ?>
			</tr>
			<?
					}
				}
			?>
		</table>

		<div class="alert alert-success alert-fixed" role="alert">
		  <strong>처리가 완료되었습니다.</strong>
		</div>

		<div class="alert alert-info alert-fixed" role="alert">
		  <strong>처리가 취소되었습니다.</strong>
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
				$("input[name=kancode]").click(function() {
					
					if($(this).val() == '<?=$kancode?>')
						$(this).val('');

				});

				$("input[name=kancode]").keydown(function(event){
					if(event.keyCode == 13)
					{
						var frm = document.frm;

						frm.target = "";
						frm.action = "<?=$_SERVER['PHP_SELF']?>";
						frm.submit();
					}
				});

				$(".basic_info").click(function(){
					var stock_no = $(this).data("stock_no");

					$("tr.more_info[data-stock_no="+stock_no+"]").toggle();
					$(".glyphicon-minus[data-stock_no="+stock_no+"]").toggle();
					$(".glyphicon-plus[data-stock_no="+stock_no+"]").toggle();

				});

				$(".submit").click(function(e){
					
					e.preventDefault();

					var stock_no = $(this).data("stock_no");

					var input_qty = $(this).closest("td").find(".input_qty").val();
					var input_bqty =  $(this).closest("td").find(".input_bqty").val();
					var input_memo =  $(this).closest("tr").find(".input_memo").val();

					$.ajax({
						  url: "json_goods_request.php",
						  dataType: 'json',
						  async: true,
						  data: {stock_no: stock_no, 
								input_qty: input_qty, 
								input_bqty: input_bqty, 
								memo: input_memo, 
								del_adm: <?=$s_adm_no?>,  
								mode: 'FSTOCK_MOVE'
							},
						  success: function(data) {
							$.each( data, function( i, item ) {
								
								$(".alert-success").show();
								
								if(item.RESULT == "1")
								{
									var stock_no = item.STOCK_NO; 
									$(".submit[data-stock_no="+stock_no+"]").closest("td").hide();

									$(".alert-success").show();
								}else
								{
									$(".alert-danger").show();
								}
								

							  });
						  },
						  fail: function() { 
							$(".alert-danger").show();
						  }
						});

					setTimeout(function () {
							$(".alert").hide();
						}, 2000);

				});

				$(".delete").click(function(e){
					
					e.preventDefault();

					var stock_no = $(this).data("stock_no");

					$.ajax({
						  url: "json_goods_request.php",
						  dataType: 'json',
						  async: true,
						  data: {stock_no: stock_no, mode: 'FSTOCK_DELETE', del_adm : <?=$s_adm_no?>},
						  success: function(data) {
							$.each( data, function( i, item ) {
								
								$(".alert-success").show();
								
								if(item.RESULT == "1")
								{
									var stock_no = item.STOCK_NO; 
									$(".table").find("tr[data-stock_no="+stock_no+"]").hide();

									$(".alert-success").show();
								}else
								{
									$(".alert-danger").show();
								}
								

							  });
						  },
						  fail: function() { 
							$(".alert-danger").show();
						  }
						});

					setTimeout(function () {
							$(".alert").hide();
						}, 2000);

				});

				$(".undo").click(function(e){
					alert('testttt');
					
					e.preventDefault();

					var stock_no		= $(this).data("stock_no");
					var prev_stock_no	= $(this).data("prev_stock_no");
					var qty				= $(this).data("qty");
					var this_elem		= $(this);
					this_elem.hide();
					alert('qty: '+qty);
					// return;

					$.ajax({
						  url: "json_goods_request.php",
						  dataType: 'json',
						  async: true,
						  data: {stock_no: stock_no, prev_stock_no: prev_stock_no, qty: qty, del_adm : <?=$s_adm_no?>, mode: 'NBSTOCK_UNDO'},
						  success: function(data) {
							$.each( data, function( i, item ) {
								
								$(".alert-success").show();
								
								
								if(item.RESULT != "1")
								{
									$(".alert-danger").show();
								}
								

							  });
						  },
						  fail: function() { 
							$(".alert-danger").show();
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