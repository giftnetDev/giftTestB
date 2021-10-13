<?
    require "../../_classes/com/db/DBUtil.php";
    require "../../_classes/com/etc/etc.php";
	$arr_rs_menu = getListAdminGroupMenu($conn, $s_adm_group_no);
?>
<script language="javascript" type="text/javascript">

	//var layerStatus = 1;  
	
	function subtreeview(chk, cntFlag) {

		//alert(cntFlag);
		
		for (i = 0; i < <?=sizeof($arr_rs_menu)?> ; i++) {

			if (cntFlag != i ) {
				if(document.getElementById('subtreelist'+i) != null) {
					document.getElementById('subtreelist'+i).style.display = "none";
				}
			}
		}
		
		//alert(document.getElementById('subtreelist'+cntFlag).style.display);
		if (document.getElementById('subtreelist'+cntFlag).style.display == "" ) {
			document.getElementById('subtreelist'+cntFlag).style.display = "none";  
		} else {
			document.getElementById('subtreelist'+cntFlag).style.display = "";  
		}
	}

</script>
			<div class="sp10"></div>
			<ul id="leftmenutree">

<?
	if (sizeof($arr_rs_menu) > 0) {
		for ($m = 0 ; $m < sizeof($arr_rs_menu); $m++) {
			
			$M_MENU_CD		= trim($arr_rs_menu[$m]["MENU_CD"]);
			$M_MENU_NAME	= trim($arr_rs_menu[$m]["MENU_NAME"]);
			$M_MENU_URL		= trim($arr_rs_menu[$m]["MENU_URL"]);
			
			if (strlen($M_MENU_CD) == "2") {

				if ($m <> 0) {
?>
					</ul>
				</li>
<?
				}

				// 메뉴 활성화 부분
				//echo $sPageMenu_CD;

				if ($M_MENU_CD == substr($sPageMenu_CD,0,2)) {
					$str_display_ = "";
				} else {
					$str_display_ = "none";
				}
?>
				
				<li class="subtree">
					<a href="#" onclick="subtreeview(this, <?=$m?>); return false" style="cursor: pointer;"><?=$M_MENU_NAME?></a>
					
					<ul id="subtreelist<?=$m?>" style="display: <?= $str_display_ ?>">
<?
			}
			if (strlen($M_MENU_CD) == "4") {
?>						
						<li><a href="<?=$M_MENU_URL?>"><?=$M_MENU_NAME?></a></li>
<?
			}
		}
	}
?>
		</ul>
<br>
<br>
<!--<iframe src="/_common/keep_session.php" name="keep_session" frameborder="no" width="140" height="1" marginwidth="0" marginheight="0" border=0></iframe>-->

<?
    //2018-12-05 세션유지용
	//출처 1 : http://php.net/manual/en/book.session.php
	//출처 2 : https://blogs.oracle.com/oswald/php:-sessiongcmaxlifetime-vs-sessioncookielifetime
	if( !isset($_SESSION['last_access']) || (time() - $_SESSION['last_access']) > 60 ) 
	  $_SESSION['last_access'] = time(); 
?>