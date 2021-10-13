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
	$menu_right = "CS002"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

#====================================================================
# common_header Check Session
#====================================================================
	include "../../_common/common_header.php"; 
	
#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/company/company.php";
	require "../../_classes/biz/counsel/counsel.php";
	
#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);


	if ($counsel_start_date == "") {
		$counsel_start_date = date("Y-m-d",strtotime("-12 month"));
	} else {
		$counsel_start_date = trim($counsel_start_date);
	}
	
	if ($counsel_end_date == "") {
		$counsel_end_date = date("Y-m-d",strtotime("0 month"));
	} else {
		$counsel_end_date = trim($counsel_end_date);
	}
	
	if ($reg_start_date == "") {
		$reg_start_date = date("Y-m-d",strtotime("-12 month"));
	} else {
		$reg_start_date = trim($reg_start_date);
	}
	
	if ($reg_end_date == "") {
		$reg_end_date = date("Y-m-d",strtotime("0 month"));
	} else {
		$reg_end_date = trim($reg_end_date);
	}
	
	$counsel_type = trim($counsel_type);//상담유형
	
	$cp_type = trim($cp_type);
	
	$search_option = trim($search_option);

	$search_str = trim($search_str);

	$page_size = trim($page_size);

	$page_no = trim($page_no);

	$sort_option = trim($sort_option);

	$sort_by = trim($sort_by);


#====================================================================
# DML Process
#====================================================================


function listCounsel2($db, $counsel_start_date, $counsel_end_date, $reg_start_date, $reg_end_date, $counsel_type, $cp_no, $search_option, $search_str, $sort_option, $sort_by){
	$query =    "SELECT A.*, B.CP_NM, B.CP_NM2, C.ADM_NAME
						FROM TBL_COUNSEL A JOIN TBL_COMPANY B ON A.CP_NO = B.CP_NO
							JOIN TBL_ADMIN_INFO C ON A.COUNSEL_ADM_NO = C.ADM_NO
						WHERE A.DEL_TF = 'N'
							AND B.DEL_TF = 'N'
							AND C.DEL_TF = 'N' 
	";

	if($counsel_start_date != "" && $counsel_end_date != ""){
		$query .=" AND A.COUNSEL_DATE BETWEEN '$counsel_start_date 00:00:00' AND '$counsel_end_date 23:59:59' ";
	}

	if($reg_start_date != "" && $reg_end_date != "" ){
		$query .=" AND A.REG_DATE BETWEEN '$reg_start_date 00:00:00' AND '$reg_end_date 23:59:59' ";
	}

	if($counsel_type != "" ){
		$query .= " AND A.COUNSEL_TYPE = '$counsel_type' ";
	}

	if($cp_no != "" ){
		$query .= " AND A.CP_NO = '$cp_no' ";
	}

	if($search_str != ""){
		if($search_option == "MANAGER_NM"){
			$query .= " AND A.MANAGER_NM LIKE '%$search_str%' ";
		} else if($search_option == "ASK"){
			$query .= " AND A.ASK LIKE '%$search_str%' ";
		} else if($search_option == "ANSWER"){
			$query .= " AND A.ANSWER LIKE '%$search_str%' ";
		} else if($search_option == "COUNSEL_ADM_NM"){
			$query .= " AND C.ADM_NAME LIKE '%$search_str%' ";
		}
	}

	if($sort_option == "REG_DATE"){
		$query .=" ORDER BY REG_DATE ";
	} else {
		$query .=" ORDER BY COUNSEL_DATE ";
	}
	
	if($sort_by == "ASC"){
		$query .=" ASC ";
	} else {
		$query .=" DESC ";
	}
	
	// if($page_size != "" && $page_no != "" ){
	// 	$offset1 = ($page_no - 1) * $page_size;
	// 	$offset2 = ($page_no ) * $page_size;
	// 	$query .= " LIMIT $offset1, $offset2 ";
	// }

	 //echo $query;

	$result = mysql_query($query,$db);
	$record = array();
	//echo "<script>console.log('test');</script>";
	if ($result <> "") {
		for($i=0;$i < mysql_num_rows($result);$i++) {
			$record[$i] = sql_result_array($result,$i);
		}
	}
	return $record;
	//test

}

$arr_rs = listCounsel2($conn, $counsel_start_date, $counsel_end_date, $reg_start_date, $reg_end_date, $counsel_type, $cp_type, $search_option, $search_str, $sort_option, $sort_by);

?>
<!DOCTYPE html>
<html xml:lang="ko" lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charshet?>" />
<title><?=$g_title?></title>
<meta content="기프트넷 관리자" name="keywords" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />

<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
<link rel="icon" type="image/x-icon" href="/" />
<link type="text/css" rel="stylesheet" href="../css/reset.css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script>
<script src="../../lib/js/jquery-1.11.2.min.js" charset="euc-kr"></script>
<script src="../../lib/js/jquery_ui.js" charset="euc-kr"></script>
<script type="text/javascript" src="../jquery/jquery.datetimepicker.full.js"></script> -->
<script>
	function js_search() {
		var frm = document.frm;
		frm.target = "";
		frm.method = "get";
		frm.action = "<?=$_SERVER["PHP_SELF"]?>";
		frm.submit();
	}
    $(document).ready(function(){
        $( ".txt_date" ).datepicker({
            showOn: "button",
            buttonImage: "/manager/images/calendar/cal.gif",
            buttonImageOnly: true,
            buttonText: "Select date",
            showOn: "both",
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            changeYear: true,
            beforeShow: function() {
                setTimeout(function(){
                    $('.ui-datepicker').css('z-index', 99999999999999);
                }, 0);
            }
        });//datepicker

        $("#save_btn").click(function(){
            var temp_cp_type = $("#cp_type").val();
            var temp_manager_nm = $("#manager_nm").val();
            var temp_counsel_date = $("#counsel_date").val();
            var temp_counsel_adm_no = $("#counsel_adm_no").val();
            var temp_counsel_type = $("#counsel_type").val();
            var temp_ask = $("#ask").val();
            var temp_answer = $("#answer").val();

            $.ajax({
                url: '/manager/ajax_processing.php',
                dataType: 'text',
                type: 'post',
                data : {
                    "mode":"INSERT_COUNSEL",
                    "cp_type":temp_cp_type,
                    "manager_nm":temp_manager_nm,
                    "counsel_date":temp_counsel_date,
                    "counsel_adm_no":temp_counsel_adm_no,
                    "counsel_type":temp_counsel_type,
                    "ask":temp_ask,
                    "answer":temp_answer,
                    "s_adm_no":"<?=$s_adm_no?>"
                },
                success: function(response) {
                    if(response.length > 0){
                        alert("success");
                    } else {
                        alert("fail");
                    }
                },
                error : function (jqXHR, textStatus, errorThrown) {
                    alert('ERRORS: ' + textStatus);
                }
            });//ajax
        });
		$("#btnSearch").click(function(){
			//alert('aaa');
			js_search();
		});

        $(".item").click(function(){
            //선택된 제안 상품의 제안번호
            var seq_no = $(this).parents("tr").children("td:first").children("input").val();

            //이동할 주소
            var url = "https://www.giftnet.co.kr/manager/counsel/counsel_write.php?seq_no="+seq_no;

            //이동
            window.open(url, "counsel_write");
        });

		$("#insertBtn").click(function(){
			//이동할 주소
			var url = "https://www.giftnet.co.kr/manager/counsel/counsel_write.php";

			//이동
			window.open(url, "counsel_write");
		});
		
		$("#deleteBtn").click(function(){
			var con_test = confirm("정말로 삭제하시겠습니까?");
			if(con_test == true){
				var selectedRows = $(".tbody > tr > td > input:checked");
				var datas = Array();
				for(i=0;i<selectedRows.length;i++){
					var temp_seq_no = selectedRows.eq(i).val();
					datas.push({"seq_no":temp_seq_no});
				}//for

				$.ajax({
					url: '/manager/ajax_processing.php',
					dataType: 'json',
					type: 'post',
					data : {
						"mode":"DELETE_COUNSEL",
						"seq_no":datas,
						"s_adm_no":"<?=$s_adm_no?>"
					},
					success: function(response) {
						if(response.length > 0){
							var update_fail_cnt = response[0]["update_fail_cnt"];
							var update_cnt = response[0]["update_cnt"];
							if(update_fail_cnt != 0){
								for(i=0;i<response[0]["data"].length;i++){
									var temp_seq_no = response[0]["data"][i]["seq_no"];
									var temp_update_result = response[0]["data"][i]["update_result"];
									if(temp_update_result == "fail"){
										$("#chk_"+temp_seq_no).parents("td").parents("tr").eq(0).css("background-color","#ffb0b0");
									} else {
										$("#chk_"+temp_seq_no).parents("td").parents("tr").eq(0).remove();
									}
								}
								alert("삭제 : " + update_cnt + "건, 실패 : "+update_fail_cnt+"건\n실패한 항목은 배경이 붉은 색으로 표시됩니다.\n실패 항목을 확인하세요.");
							} else {
								alert("삭제되었습니다. \n"+update_cnt+"건");
								for(i=0;i<response[0]["data"].length;i++){
									var temp_seq_no = response[0]["data"][i]["seq_no"];
									var temp_update_result = response[0]["data"][i]["update_result"];
									$("#chk_"+temp_seq_no).parents("td").parents("tr").eq(0).remove();
								}
							}
						} else {
							alert("결과를 회신받지 못했습니다. 삭제에 실패했습니다.");
						}
					},
					error : function (jqXHR, textStatus, errorThrown) {
						alert('ERRORS: ' + textStatus);
					}
				});//ajax
			}//if
		});//deleteBtn
		
    });//ready

	$(function(){
		$("input[name=search_str]").keydown(function(e){
			if(e.keyCode==13){
				js_search();
			}
		});
	});

</script>
<style>
	.item:hover{
		cursor:pointer;
	}
	.hover-color:hover{
			background-color: #f0f0f0;
	}

</style>
</head>
<body id="wrap">

	<form name="frm" method="post" enctype="multipart/form-data">
	<input type="hidden" name="mode" value="">
	<input type="hidden" name="cp_no" value="<?= $cp_no?>">

	<? require "../../_common/top_area3.php"; ?>

	<div class="midarea">
		<div class="leftarea">
			<?	
				require "../../_common/left_area3.php";
				include_once('../../_common/editor/func_editor.php'); 
			?>
		</div><!--"leftarea-->
		<div class="contentsarea">

			<div class="menu-counsel">
				<h2><strong>상담리스트</strong></h2>



				<div class="rev-header">

					<div class="datesch">
						<span class="datebox"><input type="text" id="counsel_start_date" name="counsel_start_date" class="txt_date" placeholder="상담일 선택" value="<?=$counsel_start_date?>"/></span><em>~</em>
						<span class="datebox"><input type="text" id="counsel_end_date"   name="counsel_end_date"   class="txt_date" placeholder="상담일 선택" value="<?=$counsel_end_date?>"/></span>	
					</div><!--class="datesch"-->

					<div class="btn-listview">

						<span class="optionbox">
							<?= makeSelectBox1($conn,"COUNSEL_TYPE","counsel_type","상담유형","",$counsel_type)?>
						</span>
						
						<span class="optionbox">
							<?= makeSelectBox1($conn,"COUNSEL_SEARCH_OPTIO","search_option","검색조건","",$search_option)?>
						</span>
						<p class="schbox">
							<span class="inpbox">
								<input type="text" name="search_str" class="txt" placeholder="검색어" />
							</span>
							<button type="button" id="btnSearch" title="검색">검색</button>
						</p>

						
					</div><!--class="btn-listview"-->

				</div><!--class="rev-header-->

				<div class="boardlist large-padding">
					총 <?=number_format(sizeof($arr_rs))?> 건
					<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table">
						<colgroup>
							<col width="2%">
							<col width="6%">
							<col width="6%">
							<col width="9%">
							<col width="9%">
							<col width="25%">
							<col width="25%">
							<col width="6%">
							<col width="6%">
							<col width="6%">
						</colgroup>
						<thead>
							<tr>
								<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
								<th>상담일</th>
								<th>상담유형</th>
								<th>업체명</th>
								<th>담당자</th>
								<th>문의(상담)</th>
								<th>답변(조치)</th>
								<th>상담자</th>
								<th>등록일</th>
								<th class="end">등록자</th>
							</tr>
						</thead>
						<tbody class="tbody">
						<?
							if (sizeof($arr_rs) > 0) {
								for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
									$SEQ_NO = trim($arr_rs[$j]["SEQ_NO"]);
									$REG_ADM = trim(getAdminName($conn, $arr_rs[$j]["REG_ADM"]));
									$COUNSEL_DATE = trim($arr_rs[$j]["COUNSEL_DATE"]);
									$COUNSEL_TYPE = trim($arr_rs[$j]["COUNSEL_TYPE"]);
									$REG_DATE = trim($arr_rs[$j]["REG_DATE"]);
									$CP_NO = trim($arr_rs[$j]["CP_NO"]);
									$MANAGER_NM = trim(SetStringFromDB($arr_rs[$j]["MANAGER_NM"]));
									$ASK = trim(SetStringFromDB($arr_rs[$j]["ASK"]));
									$ANSWER = trim(SetStringFromDB($arr_rs[$j]["ANSWER"]));
									$ADM_NAME = trim(SetStringFromDB($arr_rs[$j]["ADM_NAME"]));
									$CP_NM = trim(SetStringFromDB($arr_rs[$j]["CP_NM"]));
									$CP_NM2 = trim(SetStringFromDB($arr_rs[$j]["CP_NM2"]));
						?>
							<tr height="35" class="hover-color">
								<td><input type="checkbox" name="chk_no[]" id="chk_<?=$SEQ_NO?>" value="<?=$SEQ_NO?>"></td>
								<td class="item"><?=$COUNSEL_DATE?></td>
								<td class="item"><?=$COUNSEL_TYPE?></td>
								<td class="item"><?=$CP_NM?> <?=$CP_NM2?></td>
								<td class="item"><?=$MANAGER_NM?></td>
								<td class="item"><?=$ASK?></td>
								<td class="item"><?=$ANSWER?></td>
								<td class="item"><?=$ADM_NAME?></td>
								<td class="item"><?=$REG_DATE?></td>
								<td class="item"><?=$REG_ADM?></td>
							</tr>
						
						<?
								}//for
							} else {
						?>
							<tr>
								<td colspan="14">데이터가 없습니다.</td>
							</tr>
						<?
							}
						?>
						</tbody>
					</table>

				</div>
			</div><!--class="menu-counsel"-->
			<div class="menu-counsel">
				<div id="rev-header" >
					<div classs="datesch">
				<!-- style="text-align:right;padding-right:65px;" -->
						<div class="btn-listview">
							<input id="insertBtn" type="button" value="등록" style="width:100px;height:20px;"/>&nbsp;&nbsp;
							<input id="deleteBtn" type="button" value="삭제" style="width:100px;height:20px;"/>
						</div>
					</div>
				</div>
			</div>
			
		</div><!--class="contentsarea-->
	</div> <!--class="midarea"-->



			<div class="sp20"></div>
			




                <!-- contents end-->
            </div><!-- mwidthwrap -->
        </td><!-- contentarea -->
  </tr>
  </table>
</div>
<iframe src="about:blank" name="ifr_hidden" id="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>

<script src="../../lib/js/jquery.easing.1.3.js" charset="euc-kr"></script>
<script src="../../lib/js/modernizr-2.8.3-respond-1.4.2.min.js" charset="euc-kr"></script>
<script src="../../lib/js/slick.js" charset="euc-kr"></script>
<script src="../../lib/js/common_ui.js" charset="euc-kr"></script>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>