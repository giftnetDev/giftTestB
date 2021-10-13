<?php

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/goods/goods.php";

	if($mode == "UPDATE_GOODS_WRAP")
	{
		$result = updateGoodsWrapInfo($conn, $wrap_width, $wrap_length, iconv('utf-8', 'euc-kr', $wrap_memo), $goods_no);

		$results = "[{\"RESULT\":\"".$result."\"}]";

		echo $results;

	}
		

?>

