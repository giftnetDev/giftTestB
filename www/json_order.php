<?php

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#=====================================================================
# common function, login_function
#=====================================================================
	require "_classes/biz/cart/cart.php";

	if($mode == "DELETE_CART")
	{
		$isSuccess = deleteCart($conn, $cart_no);

		$results = "[{\"RESULT\":\"".$isSuccess."\"}]";
		echo $results;
	}




?>

