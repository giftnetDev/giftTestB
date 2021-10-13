<?
    require "../../_classes/com/db/DBUtil.php";
    require "../../_classes/biz/order/order.php";

    $conn = db_connection("w");
    CreateSalesRevenue($conn,82210,1020,100,2000,"U");
?>