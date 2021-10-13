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
	$menu_right = "OD015"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";


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
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/payment/payment.php";

/*
	echo "start_date : ".$start_date."<br/>";
	echo "end_date : ".$end_date."<br/>";
	echo "cp_no : ".$cp_no."<br/>";

	echo "start_name : ".iconv("UTF-8","EUC-KR",$start_name)."<br/>";
	echo "end_name : ".iconv("UTF-8","EUC-KR",$end_name)."<br/>";
*/

	$file_name="창고 작업 준비 품목별 리스트-".date("Y-m-d_His",strtotime("0 month")).".xls";
	header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
	header( "Content-Disposition: attachment; filename=$file_name" );

	$start_name = iconv("UTF-8","EUC-KR", $start_name);
	$end_name = iconv("UTF-8","EUC-KR", $end_name);

	$arr_rs = listOrderDeliveryForMart_LEVEL1_From_To($conn, $start_date, $end_date, '', $cp_no); //order_state = all

	$i = 0;

	$is_checked = false;
	
	if($start_name == "")
		$is_checked = true;

	if (sizeof($arr_rs) > 0) {
		for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
			$CP_NO		 = trim($arr_rs[$j]["CP_NO"]);
			$R_ADDR1     = trim($arr_rs[$j]["R_ADDR1"]);
			$R_MEM_NM    = trim($arr_rs[$j]["R_MEM_NM"]);
			$O_MEM_NM    = trim($arr_rs[$j]["O_MEM_NM"]);

			//echo $CP_NO." | ".$R_ADDR1." | ".$R_MEM_NM." | ".$O_MEM_NM." | "."<br/>"; 

			if($is_checked == false && $O_MEM_NM == $start_name) 
				$is_checked = true;

			//echo ($O_MEM_NM."||".$start_name)."<br/>";

			if($is_checked) { 
				$R_ADDR1      = SetStringToDB(trim($arr_rs[$j]["R_ADDR1"]));

				//echo $CP_NO." | ".$R_ADDR1." | ".$R_MEM_NM." | ".$O_MEM_NM."<br/>"; 
				
				$arr_rs2 = listOrderDeliveryForMart_LEVEL2_From_To($conn, $start_date, $end_date, $CP_NO, $O_MEM_NM, $R_MEM_NM, $R_ADDR1, '2'); 

				if (sizeof($arr_rs2) > 0) {
					for ($k = 0 ; $k < sizeof($arr_rs2); $k++) {

						$current_qty = getRefundAbleQty($conn, $arr_rs2[$k]["RESERVE_NO"], $arr_rs2[$k]["ORDER_GOODS_NO"]);
						if($arr_rs2[$k]["GOODS_NO"] == $arr_rs2[$k+1]["GOODS_NO"])
						{
							//echo "current".$current_qty."<br/>";
							$next_qty = getRefundAbleQty($conn, $arr_rs2[$k+1]["RESERVE_NO"], $arr_rs2[$k+1]["ORDER_GOODS_NO"]);
							//echo "next".$next_qty."<br/>";

							if(strpos($arr_rs2[$k]["ORDER_GOODS_NO"], ',') !== false)  
								$current_total_qty = intval($arr_rs2[$k]["GOODS_CNT"]) * intval($arr_rs2[$k]["QTY"]);
							else
								$current_total_qty = intval($arr_rs2[$k]["GOODS_CNT"]) * intval($current_qty);
							
							$next_total_qty = intval($arr_rs2[$k+1]["GOODS_CNT"]) * intval($next_qty);

							$arr_rs2[$k+1]["GOODS_CNT"]	= $next_total_qty  + $current_total_qty;

							//echo "current_total_qty".$current_total_qty."<br/>";
							//echo "next_total_qty".$next_total_qty."<br/>";
							//echo "total: ".(intval($next_total_qty) + intval($current_total_qty))."<br/>";
							
							$arr_rs2[$k+1]["QTY"]	= 1;
							$arr_rs2[$k+1]["ORDER_GOODS_NO"] .= ",".$arr_rs2[$k]["ORDER_GOODS_NO"];
							$arr_rs2[$k+1]["QTY_CHANGED"] = "Y"; 
						} else {

							//echo "입력:".$arr_rs2[$k]["GOODS_CNT"]."//".$arr_rs2[$k]["QTY"]."<br/>";

							$arr_rs3[$i]["GOODS_NO"]            = trim($arr_rs2[$k]["GOODS_NO"]);
							$arr_rs3[$i]["GOODS_NAME"]		    = trim($arr_rs2[$k]["GOODS_NAME"]);
							$arr_rs3[$i]["GOODS_SUB_NAME"]		= trim($arr_rs2[$k]["GOODS_SUB_NAME"]);
							
							if($arr_rs2[$k]["QTY_CHANGED"] == "Y") 
								$arr_rs3[$i]["QTY"]		            = trim($arr_rs2[$k]["QTY"]);
							else 
								$arr_rs3[$i]["QTY"]		            = $current_qty;

							$arr_rs3[$i]["ORDER_GOODS_NO"]      = trim($arr_rs2[$k]["ORDER_GOODS_NO"]);
							$arr_rs3[$i]["GOODS_CNT"]           = trim($arr_rs2[$k]["GOODS_CNT"]);
							
							$arr_rs3[$i]["SUM"] = $arr_rs3[$i]["GOODS_CNT"] * $arr_rs3[$i]["QTY"];

							$i++;

						}
					}

				}
			}
				
			if($O_MEM_NM == $end_name)
				break;
		}
	}

	$arr = array();
	if(sizeof($arr_rs3) > 0) {
		for($i = 0; $i < sizeof($arr_rs3); $i++) {

			$arr[$i]["GOODS_NO"]		= $arr_rs3[$i]["GOODS_NO"];
			$arr[$i]["GOODS_NAME"]		= $arr_rs3[$i]["GOODS_NAME"];
			$arr[$i]["GOODS_SUB_NAME"]	= $arr_rs3[$i]["GOODS_SUB_NAME"];
			$arr[$i]["SUM"]				= $arr_rs3[$i]["SUM"];

			//echo $arr[$i]["GOODS_NAME"].$arr[$i]["GOODS_SUB_NAME"]."|".$arr[$i]["SUM"]."<br/>";	
		}
	}

	foreach ($arr as $key => $row) {
		$GOODS_NO[$key]  = $row['GOODS_NO'];
	}

	if(sizeof($arr) > 0)
		array_multisort($GOODS_NO, SORT_ASC, $arr);


	$i = 0;
	for ($k = 0 ; $k < sizeof($arr); $k++) {
		if($arr[$k]["GOODS_NO"] == $arr[$k+1]["GOODS_NO"])
		{
			$arr[$k+1]["SUM"]	= $arr[$k+1]["SUM"]  + $arr[$k]["SUM"];
		} else {

			$arr_rs4[$i]["GOODS_NO"]            = trim($arr[$k]["GOODS_NO"]);
			$arr_rs4[$i]["GOODS_NAME"]		    = trim($arr[$k]["GOODS_NAME"]);
			$arr_rs4[$i]["GOODS_SUB_NAME"]		= trim($arr[$k]["GOODS_SUB_NAME"]);
			$arr_rs4[$i]["SUM"]					= trim($arr[$k]["SUM"]);

			$i++;

		}
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<style>td { mso-number-format:\@; font-size: 16px; line-height: 24px; } </style> 
<title><?=$g_title?></title>
</head>

<body>
<font size=3><b><?=$file_name?></b></font> 
<br>
<br>
<font size=3><b>상품 준비 수량</b></font> 
<br>
<TABLE border=1>
	<thead>
		<tr>
			<td><font size=2><b>상품명</b></td>
			<td><font size=2><b>수량</b></td>
		</tr>
	</thead>
	<tbody>

	<?
		if (sizeof($arr_rs4) > 0) {
			for ($j = 0 ; $j < sizeof($arr_rs4); $j++) {
				
				$GOODS_NAME		     = trim($arr_rs4[$j]["GOODS_NAME"]);
				$GOODS_SUB_NAME	     = trim($arr_rs4[$j]["GOODS_SUB_NAME"]);
				$SUM				 = trim($arr_rs4[$j]["SUM"]);

	?>
			<tr>
				<td bgColor='#FFFFFF' align='left'><?=$GOODS_NAME?> <?=$GOODS_SUB_NAME?></td>
				<td bgColor='#FFFFFF' align='right'><b><?=$SUM?></b></td>
			</tr>
			
	<?
			}
		 }else {
	?>
			<tr class="order">
				<td height="50" align="center" colspan="2">데이터가 없습니다. </td>
			</tr>
	<?
		 }
	?>
	</tbody>
	
</table>


</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>