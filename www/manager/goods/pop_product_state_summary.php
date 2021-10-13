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
	$menu_right = "GD007"; // 메뉴마다 셋팅 해 주어야 합니다

#====================================================================
# common_header Check Session
#====================================================================
	require "../../_common/common_header.php"; 

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/util/ImgUtil.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/goods/goods.php";
    require "../../_classes/biz/company/company.php";
    
#===============================================================
# custom function
#===============================================================
function selectProductState($db){
    $query ="SELECT
                C.CP_NM
				,C.CP_CODE
				,C.CP_NO
                ,COUNT(*) AS GOODS_QTY
                ,SUM(IF(P.DISPLAY = 'Y',1,0)) AS DP_ON
                ,SUM(IF(P.DISPLAY = 'N' or P.DISPLAY = '',1,0)) AS DP_OFF
            FROM
                TBL_GOODS G
                JOIN TBL_GOODS_PRICE P ON G.GOODS_NO = P.GOODS_NO
                JOIN TBL_COMPANY C ON P.CP_NO = C.CP_NO
            GROUP BY
                C.CP_NM
    ";
    //echo $query;
    $result = mysql_query($query,$db);
    $record = array();
    if ($result <> "") {
        for($i=0;$i < mysql_num_rows($result);$i++) {
            $record[$i] = sql_result_array($result,$i);
        }
    }
    return $record;
}
function sellingCompanyCnt($db){
    $query ="SELECT
                COUNT(*) AS CNT
            FROM
                TBL_GOODS G
                JOIN TBL_GOODS_PRICE P ON G.GOODS_NO = P.GOODS_NO
                JOIN TBL_COMPANY C ON P.CP_NO = C.CP_NO
            GROUP BY
                C.CP_NM
    ";
    //echo $query;
    $result = mysql_query($query,$db);
    $record = array();
    if ($result <> "") {
        for($i=0;$i < mysql_num_rows($result);$i++) {
            $record[$i] = sql_result_array($result,$i);
        }
    }
    return $record[0]["CNT"];
}
#===============================================================
# Get Search list count
#===============================================================
	$arr_rs = selectProductState($conn);
	$cnt = sellingCompanyCnt($conn);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
    <title>기프트넷</title>
    <link rel="stylesheet" href="../css/admin.css" type="text/css" />
    <script type="text/javascript" src="../js/common.js"></script>
    <script type="text/javascript" src="../js/goods_common.js"></script>
    <script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
	<script language="javascript">
		function js_view(cp_no,dp_tf){
			var url = "/manager/goods/goods_price_list.php?cp_no="+cp_no+"&chk_display="+dp_tf+"&nPageSize=20";
			window.open(url,'_blank');
		}
	</script>
</head>
<body id="popup_file">
<form name="frm">
    <div id="popupwrap_file">
        <h1>업체별 판매상품 현황</h1>
        <div id="postsch_code">
            <h2>* 해당 업체를 누르시면 상세 내역을 보실 수 있습니다.</h2>
            <div class="addr_inp">
				<div>총 <?=sellingCompanyCnt($conn)?>개 업체</div>
                <table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table">
					<colgroup>
						<col width="10%">
						<col width="*%">
						<col width="15%">
						<col width="15%">
						<col width="15%">
					</colgroup>
					<thead>
						<tr>
							<th>업체코드</th>
							<th>판매업체명</th>
							<th>등록 상품 수량</th>
							<th>전시 수량</th>
							<th>비전시 수량</th>
						</tr>
					</thead>
					<tbody>
						<?
						$TOTAL_GOODS_QTY		= 0;
						$TOTAL_DISPLAY_QTY		= 0;
						$TOTAL_NOT_DISPLAY_QTY	= 0;
						if (sizeof($arr_rs) > 0) {
							for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
								$COMPANY_CODE		= $arr_rs[$j]["CP_CODE"];
								$COMPANY_NAME		= $arr_rs[$j]["CP_NM"];
								$COMPANY_NO			= $arr_rs[$j]["CP_NO"];
								$GOODS_QTY			= $arr_rs[$j]["GOODS_QTY"];
								$DISPLAY_QTY		= $arr_rs[$j]["DP_ON"];
								$NOT_DISPLAY_QTY	= $arr_rs[$j]["DP_OFF"];

								$TOTAL_GOODS_QTY		+= $GOODS_QTY;
								$TOTAL_DISPLAY_QTY		+= $DISPLAY_QTY;
								$TOTAL_NOT_DISPLAY_QTY	+= $NOT_DISPLAY_QTY;
								?>
								<tr height="30">
									<td><a href="javascript:js_view('<?=$COMPANY_NO?>','');"><?=$COMPANY_CODE?></a></td>
									<td><a href="javascript:js_view('<?=$COMPANY_NO?>','');"><?=$COMPANY_NAME?></a></td>
									<td><a href="javascript:js_view('<?=$COMPANY_NO?>','');"><?=$GOODS_QTY?></a></td>
									<td><a href="javascript:js_view('<?=$COMPANY_NO?>','Y');"><?=$DISPLAY_QTY?></a></td>
									<td><a href="javascript:js_view('<?=$COMPANY_NO?>','N');"><?=$NOT_DISPLAY_QTY?></a></td>
								</tr>
								<?
							}
							?>
							<tr height="30">
								<td><b>합계 : </b></td>
								<td></td>
								<td><b><?=$TOTAL_GOODS_QTY?></b></td>
								<td><b><?=$TOTAL_DISPLAY_QTY?></b></td>
								<td><b><?=$TOTAL_NOT_DISPLAY_QTY?></b></td>
							</tr>
							<?
						} else {
							?>
							<tr>
								<td colspan = '5' align="center" height="50">데이터가 없습니다. </td>
							</tr>
							<?
						}
						?>
					</tbody>
				</table>
            </div>
        <div class="sp15"></div>
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