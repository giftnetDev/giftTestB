<?session_start();?>
<?
# =============================================================================
# File Name    : order_list.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @C&C Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "CF002"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/company/company.php";
	
	$cp_no			= trim($cp_type);

	//echo $cp_no;

	$arr_rs = selectCompany($conn, $cp_no);

	$rs_ad_type	= trim($arr_rs[0]["AD_TYPE"]); 
	
	$this_date = date("Y-m-d",strtotime("0 month"));
	$this_yyyy = date("Y",strtotime("0 month"));
	$this_mm = date("m",strtotime("0 month"));

	$this_w = date("w",strtotime("0 day"));
	
	$this_d = date("d",strtotime("0 day"));
	
	$pre_yyyy = date("Y",strtotime("-1 month"));
	$pre_mm = date("m",strtotime("-1 month"));
	
	$this_end_date = mktime(0,0,0,date(m)+1,1,date(Y))-1; //이번달의 마지막날
	$this_end_date = date('Y-m-d',$this_end_date);

	$pre_end_date = mktime(0,0,0,date(m),1,date(Y))-1; //이번달의 마지막날
	$pre_end_date = date('Y-m-d',$pre_end_date);

	//echo $pre_end_date;

	if ($rs_ad_type == "현금결제") {
		$s_date = $this_date;
		$e_date = $this_date;
	}

	if ($rs_ad_type == "주마감") {
		if ($this_w == 1) {
			$e_date = date("Y-m-d",strtotime("-1 day"));
			$s_date = date("Y-m-d",strtotime("-7 day"));
		}

		if ($this_w == 2) {
			$e_date = date("Y-m-d",strtotime("-2 day"));
			$s_date = date("Y-m-d",strtotime("-8 day"));
		}

		if ($this_w == 3) {
			$e_date = date("Y-m-d",strtotime("-3 day"));
			$s_date = date("Y-m-d",strtotime("-9 day"));
		}

		if ($this_w == 4) {
			$e_date = date("Y-m-d",strtotime("-4 day"));
			$s_date = date("Y-m-d",strtotime("-10 day"));
		}

		if ($this_w == 5) {
			$e_date = date("Y-m-d",strtotime("-5 day"));
			$s_date = date("Y-m-d",strtotime("-11 day"));
		}

		if ($this_w == 6) {
			$e_date = date("Y-m-d",strtotime("-6 day"));
			$s_date = date("Y-m-d",strtotime("-12 day"));
		}

		if ($this_w == 0) {
			$e_date = date("Y-m-d",strtotime("0 day"));
			$s_date = date("Y-m-d",strtotime("-6 day"));
		}

	}

	if ($rs_ad_type == "15일마감") {

		if ($this_d < 15) {
			$s_date = $pre_yyyy."-".$pre_mm."-15";
			$e_date = $pre_end_date;
		} else {
			$s_date = $this_yyyy."-".$this_mm."-01";
			$e_date = $this_yyyy."-".$this_mm."-15";
		}

	}

	if ($rs_ad_type == "당월말일") {
		$s_date = $this_yyyy."-".$this_mm."-01";
		$e_date = $this_end_date;
	}

	if ($rs_ad_type == "익월20일") {
		$s_date = $pre_yyyy."-".$pre_mm."-01";
		$e_date = $pre_end_date;
	}

	if ($rs_ad_type == "익월25일") {
		$s_date = $pre_yyyy."-".$pre_mm."-01";
		$e_date = $pre_end_date;
	}

	if ($rs_ad_type == "익월말일") {
		$s_date = $pre_yyyy."-".$pre_mm."-01";
		$e_date = $pre_end_date;
	}
	
	$str_ad_type = getDcodeName($conn, "AD_TYPE", $rs_ad_type);
?>
<script type="text/javascript">
	parent.js_setDate('<?=$str_ad_type?>','<?=$s_date?>','<?=$e_date?>');
</script>
<?
	echo $s_date;
	echo $e_date;


#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>