<?session_start();?>
<?
# =============================================================================
# File Name    : pop_menu_list.php
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");


#====================================================================
# common_header Check Session
#====================================================================
	require "../../_common/common_header.php"; 

#==============================================================================
# Confirm right
#==============================================================================

	$sPageRight_		= "Y";
	$sPageRight_R		= "Y";
	$sPageRight_I		= "Y";
	$sPageRight_U		= "Y";
	$sPageRight_D		= "Y";
	$sPageRight_F		= "Y";


#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/menu/menu.php";
	require "../../_classes/biz/admin/admin.php";


#====================================================================
# Request Parameter
#====================================================================

	$group_no = trim($group_no);


	$arr_rs = selectAdminGroup($conn, $group_no);

	$rs_group_name = trim($arr_rs[0]["GROUP_NAME"]); 

	$arr_rs_right = listAdminGroupMenuRight($conn, $group_no);
	
	$con_use_tf		= "Y";
	$con_del_tf		= "N";
	$search_field	= "";
	$search_str		= "";
	
	$nExist = "0";

	$arr_rs = listAdminMenu($conn, $con_use_tf, $con_del_tf, $search_field, $search_str);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; chaset=<?=$g_chrset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->

<script LANGUAGE="JavaScript">
<!--
	function Sendit() {
		var frm = document.frm; 

		var total = frm["menu_cd[]"].length;

		for(var i=0; i< total; i++) {
			if(frm["chk_read[]"][i].checked == true) {
				frm["read_chk[]"][i].value="Y";
			} else {
				frm["read_chk[]"][i].value="N";
			}
		}

		for(var i=0; i< total; i++) {
			if(frm["chk_reg[]"][i].checked == true) {
				frm["reg_chk[]"][i].value="Y";
			} else {
				frm["reg_chk[]"][i].value="N";
			}
		}

		for(var i=0; i< total; i++) {
			if(frm["chk_upd[]"][i].checked == true) {
				frm["upd_chk[]"][i].value="Y";
			} else {
				frm["upd_chk[]"][i].value="N";
			}
		}

		for(var i=0; i< total; i++) {
			if(frm["chk_del[]"][i].checked == true) {
				frm["del_chk[]"][i].value="Y";
			} else {
				frm["del_chk[]"][i].value="N";
			}
		}

		for(var i=0; i< total; i++) {
			if(frm["chk_file[]"][i].checked == true) {
				frm["file_chk[]"][i].value="Y";
			} else {
				frm["file_chk[]"][i].value="N";
			}
		}
		
		frm.submit();
	}


	function setFlag(menu_cd, cnt, kind) {

		var frm = document.frm; 
		var total = frm["menu_cd[]"].length;
				
		// 조회
		if (kind == "R") {
			
			if (frm["chk_read[]"][cnt].checked == true) {
			
				for(var i=0; i< total; i++) {
				
					// 대분류
					if (menu_cd.length == 2) { 
												
						if (frm["menu_cd[]"][i].value.substring(0,2) == menu_cd) {
							frm["read_chk[]"][i].value = "Y";	
							frm["chk_read[]"][i].checked = true;	
						}	
					}

					// 중분류
					if (menu_cd.length == 4) { 							
						if (frm["menu_cd[]"][i].value == menu_cd.substring(0,2)) {
							frm["read_chk[]"][i].value = "Y";	
							frm["chk_read[]"][i].checked = true;	
						}	

						if (frm["menu_cd[]"][i].value.substring(0,4) == menu_cd) {
							frm["read_chk[]"][i].value = "Y";	
							frm["chk_read[]"][i].checked = true;	
						}	
					}
					
					// 소분류
					if (menu_cd.length == 6) { 							
						if (frm["menu_cd[]"][i].value == menu_cd.substring(0,2)) {
							frm["read_chk[]"][i].value = "Y";	
							frm["chk_read[]"][i].checked = true;	
						}	

						if (frm["menu_cd[]"][i].value == menu_cd.substring(0,4)) {
							frm["read_chk[]"][i].value = "Y";	
							frm["chk_read[]"][i].checked = true;	
						}	

						if (frm["menu_cd[]"][i].value.substring(0,6) == menu_cd) {
							frm["read_chk[]"][i].value = "Y";	
							frm["chk_read[]"][i].checked = true;	
						}	
					}


				} 
			} else {
			
				for(var i=0; i< total; i++) {
				
					// 대분류
					if (menu_cd.length == 2) { 
												
						if (frm["menu_cd[]"][i].value.substring(0,2) == menu_cd) {
							frm["read_chk[]"][i].value = "N";	
							frm["chk_read[]"][i].checked = false;	
						}	
					}

					// 중분류
					if (menu_cd.length == 4) { 												
						if (frm["menu_cd[]"][i].value.substring(0,4) == menu_cd) {
							frm["read_chk[]"][i].value = "N";	
							frm["chk_read[]"][i].checked = false;	
						}	
					}				

				} 			
			}
		}			
		
		// 등록
		if (kind == "I") {
			
			if (frm["chk_reg[]"][cnt].checked == true) {
			
				for(var i=0; i< total; i++) {
				
					// 대분류
					if (menu_cd.length == 2) { 
												
						if (frm["menu_cd[]"][i].value.substring(0,2) == menu_cd) {
							frm["reg_chk[]"][i].value = "Y";	
							frm["chk_reg[]"][i].checked = true;	
						}	
					
					}

					// 중분류
					if (menu_cd.length == 4) { 							
						if (frm["menu_cd[]"][i].value == menu_cd.substring(0,2)) {
							frm["reg_chk[]"][i].value = "Y";	
							frm["chk_reg[]"][i].checked = true;	
						}	

						if (frm["menu_cd[]"][i].value.substring(0,4) == menu_cd) {
							frm["reg_chk[]"][i].value = "Y";	
							frm["chk_reg[]"][i].checked = true;	
						}	
					}
					
					// 소분류
					if (menu_cd.length == 6) { 							
						if (frm["menu_cd[]"][i].value == menu_cd.substring(0,2)) {
							frm["reg_chk[]"][i].value = "Y";	
							frm["chk_reg[]"][i].checked = true;	
						}	

						if (frm["menu_cd[]"][i].value == menu_cd.substring(0,4)) {
							frm["reg_chk[]"][i].value = "Y";	
							frm["chk_reg[]"][i].checked = true;	
						}	

						if (frm["menu_cd[]"][i].value.substring(0,6) == menu_cd) {
							frm["reg_chk[]"][i].value = "Y";	
							frm["chk_reg[]"][i].checked = true;	
						}	
					}


				} 
			} else {
			
				for(var i=0; i< total; i++) {
				
					
					// 대분류
					if (menu_cd.length == 2) { 
												
						if (frm["menu_cd[]"][i].value.substring(0,2) == menu_cd) {
							frm["reg_chk[]"][i].value = "N";	
							frm["chk_reg[]"][i].checked = false;	
						}	
					}

					// 중분류
					if (menu_cd.length == 4) { 												
						if (frm["menu_cd[]"][i].value.substring(0,4) == menu_cd) {
							frm["reg_chk[]"][i].value = "N";	
							frm["chk_reg[]"][i].checked = false;	
						}	
					}				

				} 			
			}
		}			


		// 수정
		if (kind == "U") {
			
			if (frm["chk_upd[]"][cnt].checked == true) {
			
				for(var i=0; i< total; i++) {
				
					// 대분류
					if (menu_cd.length == 2) { 
												
						if (frm["menu_cd[]"][i].value.substring(0,2) == menu_cd) {
							frm["upd_chk[]"][i].value = "Y";	
							frm["chk_upd[]"][i].checked = true;	
						}	
					}

					// 중분류
					if (menu_cd.length == 4) { 							
						if (frm["menu_cd[]"][i].value == menu_cd.substring(0,2)) {
							frm["upd_chk[]"][i].value = "Y";	
							frm["chk_upd[]"][i].checked = true;	
						}	

						if (frm["menu_cd[]"][i].value.substring(0,4) == menu_cd) {
							frm["upd_chk[]"][i].value = "Y";	
							frm["chk_upd[]"][i].checked = true;	
						}	
					}
					
					// 소분류
					if (menu_cd.length == 6) { 							
						if (frm["menu_cd[]"][i].value == menu_cd.substring(0,2)) {
							frm["upd_chk[]"][i].value = "Y";	
							frm["chk_upd[]"][i].checked = true;	
						}	

						if (frm["menu_cd[]"][i].value == menu_cd.substring(0,4)) {
							frm["upd_chk[]"][i].value = "Y";	
							frm["chk_upd[]"][i].checked = true;	
						}	

						if (frm["menu_cd[]"][i].value.substring(0,6) == menu_cd) {
							frm["upd_chk[]"][i].value = "Y";	
							frm["chk_upd[]"][i].checked = true;	
						}	
					}


				} 
			} else {
			
				for(var i=0; i< total; i++) {
				
					
					// 대분류
					if (menu_cd.length == 2) { 
												
						if (frm["menu_cd[]"][i].value.substring(0,2) == menu_cd) {
							frm["upd_chk[]"][i].value = "N";	
							frm["chk_upd[]"][i].checked = false;	
						}	
					}

					// 중분류
					if (menu_cd.length == 4) { 												
						if (frm["menu_cd[]"][i].value.substring(0,4) == menu_cd) {
							frm["upd_chk[]"][i].value = "N";	
							frm["chk_upd[]"][i].checked = false;	
						}	
					}				

				} 			
			}
		}			


		// 조회
		if (kind == "D") {
			
			if (frm["chk_del[]"][cnt].checked == true) {
			
				for(var i=0; i< total; i++) {
				
					// 대분류
					if (menu_cd.length == 2) { 
												
						if (frm["menu_cd[]"][i].value.substring(0,2) == menu_cd) {
							frm["del_chk[]"][i].value = "Y";	
							frm["chk_del[]"][i].checked = true;	
						}	
					}

					// 중분류
					if (menu_cd.length == 4) { 							
						if (frm["menu_cd[]"][i].value == menu_cd.substring(0,2)) {
							frm["del_chk[]"][i].value = "Y";	
							frm["chk_del[]"][i].checked = true;	
						}	

						if (frm["menu_cd[]"][i].value.substring(0,4) == menu_cd) {
							frm["del_chk[]"][i].value = "Y";	
							frm["chk_del[]"][i].checked = true;	
						}	
					}
					
					// 소분류
					if (menu_cd.length == 6) { 							
						if (frm["menu_cd[]"][i].value == menu_cd.substring(0,2)) {
							frm["del_chk[]"][i].value = "Y";	
							frm["chk_del[]"][i].checked = true;	
						}	

						if (frm["menu_cd[]"][i].value == menu_cd.substring(0,4)) {
							frm["del_chk[]"][i].value = "Y";	
							frm["chk_del[]"][i].checked = true;	
						}	

						if (frm["menu_cd[]"][i].value.substring(0,6) == menu_cd) {
							frm["del_chk[]"][i].value = "Y";	
							frm["chk_del[]"][i].checked = true;	
						}	
					}


				} 
			} else {
			
				for(var i=0; i< total; i++) {
				
					
					// 대분류
					if (menu_cd.length == 2) { 
												
						if (frm["menu_cd[]"][i].value.substring(0,2) == menu_cd) {
							frm["del_chk[]"][i].value = "N";	
							frm["chk_del[]"][i].checked = false;	
						}	
					}

					// 중분류
					if (menu_cd.length == 4) { 												
						if (frm["menu_cd[]"][i].value.substring(0,4) == menu_cd) {
							frm["del_chk[]"][i].value = "N";	
							frm["chk_del[]"][i].checked = false;	
						}	
					}				

				} 			
			}
		}			

		// 조회
		if (kind == "F") {
			
			if (frm["chk_file[]"][cnt].checked == true) {
			
				for(var i=0; i< total; i++) {
				
					// 대분류
					if (menu_cd.length == 2) { 
												
						if (frm["menu_cd[]"][i].value.substring(0,2) == menu_cd) {
							frm["file_chk[]"][i].value = "Y";	
							frm["chk_file[]"][i].checked = true;	
						}	
					}

					// 중분류
					if (menu_cd.length == 4) { 							
						if (frm["menu_cd[]"][i].value == menu_cd.substring(0,2)) {
							frm["file_chk[]"][i].value = "Y";	
							frm["chk_file[]"][i].checked = true;	
						}	

						if (frm["menu_cd[]"][i].value.substring(0,4) == menu_cd) {
							frm["file_chk[]"][i].value = "Y";	
							frm["chk_file[]"][i].checked = true;	
						}	
					}
					
					// 소분류
					if (menu_cd.length == 6) { 							
						if (frm["menu_cd[]"][i].value == menu_cd.substring(0,2)) {
							frm["file_chk[]"][i].value = "Y";	
							frm["chk_file[]"][i].checked = true;	
						}	

						if (frm["menu_cd[]"][i].value == menu_cd.substring(0,4)) {
							frm["file_chk[]"][i].value = "Y";	
							frm["chk_file[]"][i].checked = true;	
						}	

						if (frm["menu_cd[]"][i].value.substring(0,6) == menu_cd) {
							frm["file_chk[]"][i].value = "Y";	
							frm["chk_file[]"][i].checked = true;	
						}	
					}


				} 
			} else {
			
				for(var i=0; i< total; i++) {
				
					
					// 대분류
					if (menu_cd.length == 2) { 
												
						if (frm["menu_cd[]"][i].value.substring(0,2) == menu_cd) {
							frm["file_chk[]"][i].value = "N";	
							frm["chk_file[]"][i].checked = false;	
						}	
					}

					// 중분류
					if (menu_cd.length == 4) { 												
						if (frm["menu_cd[]"][i].value.substring(0,4) == menu_cd) {
							frm["file_chk[]"][i].value = "N";	
							frm["chk_file[]"][i].checked = false;	
						}	
					}				

				} 			
			}
		}			

		//alert(menu_cd);
		//alert(kind);
	}

//-->
</script>
</head>
<body id="popup_code">

<form name="frm" action="admingroup_right_dml.php" method="post">
<input type="hidden" name="group_no" value="<?=$group_no?>">	

<div id="popupwrap_code">
	<h1>관리자 메뉴 등록</h1>
	<div id="postsch">
		<h2>* 관리자 그룹별 권한을 지정하는 화면 입니다.</h2>
		<div class="addr_inp">

		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td>
					<table id='t' cellpadding="0" class="rowstable" cellspacing="0" border="0" width="100%">
						<colgroup>
							<col width="50%">
							<col width="10%">
							<col width="10%">
							<col width="10%">
							<col width="10%">
							<col width="10%">
						</colgroup>
						<thead>
							<tr>
								<th scope="col">메뉴명</th>
								<th scope="col">조회</th>
								<th scope="col">등록</th>
								<th scope="col">수정</th>
								<th scope="col">삭제</th>
								<th class="end" scope="col">파일</th>
							</tr>
						</thead>
						<tbody>
						<?
							
							if (sizeof($arr_rs) > 0) {
						
								for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
									//MENU_NO, MENU_CD, MENU_NAME, MENU_URL, MENU_FLAG, MENU_SEQ01, MENU_SEQ02, MENU_SEQ03, MENU_RIGHT
							
									$MENU_NO				= trim($arr_rs[$j]["MENU_NO"]);
									$MENU_CD				= trim($arr_rs[$j]["MENU_CD"]);
									$MENU_NAME			= trim($arr_rs[$j]["MENU_NAME"]);
									$MENU_URL				= trim($arr_rs[$j]["MENU_URL"]);
									$MENU_FLAG			= trim($arr_rs[$j]["MENU_FLAG"]);
									$MENU_SEQ01			= trim($arr_rs[$j]["MENU_SEQ01"]);
									$MENU_SEQ02			= trim($arr_rs[$j]["MENU_SEQ02"]);
									$MENU_SEQ03			= trim($arr_rs[$j]["MENU_SEQ03"]);
									$MENU_RIGHT			= trim($arr_rs[$j]["MENU_RIGHT"]);
									$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
									$DEL_TF					= trim($arr_rs[$j]["DEL_TF"]);
									$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);

									$REG_DATE = date("Y-m-d",strtotime($REG_DATE));

									if (strlen($MENU_CD) == 2) {
										$menu_str = "<font color='blue'>⊙ ".$MENU_NAME."</font>";
									} else {
										for ($menuspace = 0 ; $menuspace < strlen($MENU_CD) ;$menuspace++) {
											$menu_str = $menu_str ."&nbsp;";
										}

										if (strlen($MENU_CD) == 4) {
											$menu_str = $menu_str ."┗ <font color='navy'>".$MENU_NAME."</font>";
										} else if (strlen($MENU_CD) == 6) {
											$menu_str = $menu_str ."&nbsp;&nbsp;┗ <font color='gray'>".$MENU_NAME."</font>";
										}
									}

									//echo $MENU_CD;
						?>
							<tr align="center" height="25" bgcolor="#FFFFFF">
								<td class="modeual_nm">
									<?=$menu_str?>
									<input type="hidden" name="menu_right[]" value="<?=$MENU_RIGHT?>">
									<input type="hidden" name="menu_cd[]" value="<?=$MENU_CD?>">
									<input type="hidden" name="menu_url[]" value="<?=$MENU_URL?>">
								</td>				
						<?
									if (sizeof($arr_rs_right) > 0) {
		
										for ($jk = 0 ; $jk < sizeof($arr_rs_right); $jk++) {

											$SUB_MENU_CD	= trim($arr_rs_right[$jk]["MENU_CD"]);
											$READ_FLAG		= trim($arr_rs_right[$jk]["READ_FLAG"]);
											$REG_FLAG			= trim($arr_rs_right[$jk]["REG_FLAG"]);
											$UPD_FLAG			= trim($arr_rs_right[$jk]["UPD_FLAG"]);
											$DEL_FLAG			= trim($arr_rs_right[$jk]["DEL_FLAG"]);
											$FILE_FLAG		= trim($arr_rs_right[$jk]["FILE_FLAG"]);
											
											//echo $SUB_MENU_CD."---".$MENU_CD."<br>";

											if ($MENU_CD == trim($SUB_MENU_CD)) { 
						
												$nExist = "1";

												if (trim($READ_FLAG) == "Y") {
						?>
								<td>
									<input type="checkbox" name="chk_read[]" value="Y" checked onClick="setFlag('<?=$MENU_CD?>','<?=$j?>','R');">
									<input type="hidden" name="read_chk[]" value="">
								</td>
						<?						
												} else { 
						?>
								<td>
									<input type="checkbox" name="chk_read[]" value="Y" onClick="setFlag('<?=$MENU_CD?>','<?=$j?>','R');">
									<input type="hidden" name="read_chk[]" value="">
								</td>
						<?
												}

												if (trim($REG_FLAG) == "Y") {
						?>
								<td>
									<input type="checkbox" name="chk_reg[]" value="Y" checked onClick="setFlag('<?=$MENU_CD?>','<?=$j?>','I');">
									<input type="hidden" name="reg_chk[]" value="">
								</td>
						<?						
												} else { 
						?>
								<td>
									<input type="checkbox" name="chk_reg[]" value="Y" onClick="setFlag('<?=$MENU_CD?>','<?=$j?>','I');">
									<input type="hidden" name="reg_chk[]" value="">
								</td>
						<?
												}

												if (trim($UPD_FLAG) == "Y") {
						?>
								<td>
									<input type="checkbox" name="chk_upd[]" value="Y" checked onClick="setFlag('<?=$MENU_CD?>','<?=$j?>','U');">
									<input type="hidden" name="upd_chk[]" value="">
								</td>
						<?						
												} else { 
						?>
								<td>
									<input type="checkbox" name="chk_upd[]" value="Y" onClick="setFlag('<?=$MENU_CD?>','<?=$j?>','U');">
									<input type="hidden" name="upd_chk[]" value="">
								</td>
						<?
												}

												if (trim($DEL_FLAG) == "Y") {
						?>
								<td>
									<input type="checkbox" name="chk_del[]" value="Y" checked onClick="setFlag('<?=$MENU_CD?>','<?=$j?>','D');">
									<input type="hidden" name="del_chk[]" value="">
								</td>
						<?						
												} else { 
						?>
								<td>
									<input type="checkbox" name="chk_del[]" value="Y" onClick="setFlag('<?=$MENU_CD?>','<?=$j?>','D');">
									<input type="hidden" name="del_chk[]" value="">
								</td>
						<?
												}

												if (trim($FILE_FLAG) == "Y") {
						?>
								<td>
									<input type="checkbox" name="chk_file[]" value="Y" checked onClick="setFlag('<?=$MENU_CD?>','<?=$j?>','F');">
									<input type="hidden" name="file_chk[]" value="">
								</td>
						<?						
												} else { 
						?>
								<td>
									<input type="checkbox" name="chk_file[]" value="Y" onClick="setFlag('<?=$MENU_CD?>','<?=$j?>','F');">
									<input type="hidden" name="file_chk[]" value="">
								</td>
						<?
						
												}
											}
										}
				
										if ($nExist == "0")  {
						?>

								<td>
									<input type="checkbox" name="chk_read[]" value="Y" onClick="setFlag('<?=$MENU_CD?>','<?=$j?>','R');">
									<input type="hidden" name="read_chk[]" value="">
								</td>
								<td>
									<input type="checkbox" name="chk_reg[]" value="Y" onClick="setFlag('<?=$MENU_CD?>','<?=$j?>','I');">
									<input type="hidden" name="reg_chk[]" value="">
								</td>
								<td>
									<input type="checkbox" name="chk_upd[]" value="Y" onClick="setFlag('<?=$MENU_CD?>','<?=$j?>','U');">
									<input type="hidden" name="upd_chk[]" value="">
								</td>
								<td>
									<input type="checkbox" name="chk_del[]" value="Y" onClick="setFlag('<?=$MENU_CD?>','<?=$j?>','D');">
									<input type="hidden" name="del_chk[]" value="">
								</td>
								<td>
									<input type="checkbox" name="chk_file[]" value="Y" onClick="setFlag('<?=$MENU_CD?>','<?=$j?>','F');">
									<input type="hidden" name="file_chk[]" value="">
								</td>
						<?
				
										}
				
										$nExist = "0";
				
									} else {
						?>
								<td>
									<input type="checkbox" name="chk_read[]" value="Y" onClick="setFlag('<?=$MENU_CD?>','<?=$j?>','R');">
									<input type="hidden" name="read_chk[]" value="">
								</td>
								<td>
									<input type="checkbox" name="chk_reg[]" value="Y" onClick="setFlag('<?=$MENU_CD?>','<?=$j?>','I');">
									<input type="hidden" name="reg_chk[]" value="">
								</td>
								<td>
									<input type="checkbox" name="chk_upd[]" value="Y" onClick="setFlag('<?=$MENU_CD?>','<?=$j?>','U');">
									<input type="hidden" name="upd_chk[]" value="">
								</td>
								<td>
									<input type="checkbox" name="chk_del[]" value="Y" onClick="setFlag('<?=$MENU_CD?>','<?=$j?>','D');">
									<input type="hidden" name="del_chk[]" value="">
								</td>
								<td>
									<input type="checkbox" name="chk_file[]" value="Y" onClick="setFlag('<?=$MENU_CD?>','<?=$j?>','F');">
									<input type="hidden" name="file_chk[]" value="">
								</td>
						<?
									}
						?>
							</tr>
						<?
										$menu_str = "";
									}
								} else {
						?>
							<tr align="center" bgcolor="#FFFFFF">
								<td height="25" colspan="7">등록 메뉴가 없습니다.<!--등록 메뉴가 없습니다.--></td>
							</tr>
						<?
								}
						?>
						</tbody>
					</table>
				</td>
			</tr>
		</table>
		</div>
		<div class="btn">
			<? if (($sPageRight_I == "Y") && ($sPageRight_U == "Y") && ($sPageRight_D == "Y")) { ?>
				<a href="javascript:Sendit();"><img src="../images/admin/btn_confirm.gif" alt="등록" /></a>
			<? } ?>
		</div>
	<br />
	</div>
	<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
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