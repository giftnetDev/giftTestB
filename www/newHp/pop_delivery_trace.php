
<?
	require "../_common/home_pre_setting.php";

#====================================================================
# Request Parameter
#====================================================================

	$delivery_cp				= trim($delivery_cp);
	$delivery_no				= trim($delivery_no);

#===============================================================
# Get Search list count
#===============================================================

	$trace = getDeliveryUrl($conn, $delivery_cp);
	$trace = $trace.$delivery_no;

?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr;" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>배송추적</title>

<script src="../js/jquery.js"></script>
<script type="text/javascript" src="../manager/jquery/jquery.cookie.js"></script>
<script>
		$(document).ready(function(){
			
			window.open('<?=$trace?>', "", "width=800, height=700, top=50, left=0");
			self.close();
		});			
</script>
</head>
<body>
</body>
</html>