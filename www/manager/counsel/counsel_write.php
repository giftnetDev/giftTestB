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
	$menu_right = "CS001"; // 메뉴마다 셋팅 해 주어야 합니다

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

    if($counsel_date == ""){
        $counsel_date = date("Y-m-d",strtotime("0 month"));
    } else {
        $counsel_date = trim($counsel_date);
    }

    if($seq_no == ""){
        $seq_no = trim($seq_no);
    }

    
#====================================================================
# DML Process
#====================================================================
function getCounsel($db, $seq_no){
    $query =    "SELECT *
                        FROM TBL_COUNSEL
                        WHERE SEQ_NO = '$seq_no'
                            AND DEL_TF = 'N'
    ";

    // echo $query;

	$result = mysql_query($query,$db);
	$record = array();

	if ($result <> "") {
		for($i=0;$i < mysql_num_rows($result);$i++) {
			$record[$i] = sql_result_array($result,$i);
		}
	}
	return $record;
}
if($seq_no != ""){
    $arr_rs = getCounsel($conn,$seq_no);

    $cp_type = $arr_rs[0]["CP_NO"];
    $manager_nm = $arr_rs[0]["MANAGER_NM"];
    $counsel_adm_no = $arr_rs[0]["COUNSEL_ADM_NO"];
    $counsel_date = $arr_rs[0]["COUNSEL_DATE"];
    $counsel_type = $arr_rs[0]["COUNSEL_TYPE"];
	$ask = $arr_rs[0]["ASK"];
	$answer = $arr_rs[0]["ANSWER"];
	
	//echo "<script>alert('질문은".$ask."');</script>";
	//echo "대답은 $answer<br>";

}
?>

<!DOCTYPE html>
<html xml:lang="ko" lang="ko">
<head>
<title>기프트넷 관리자</title>
<meta content="기프트넷 관리자" name="keywords" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
<link rel="icon" type="image/x-icon" href="/" />
<link type="text/css" rel="stylesheet" href="../css/reset.css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->
<script src="../../lib/js/jquery-1.11.2.min.js" charset="euc-kr"></script>
<script src="../../lib/js/jquery_ui.js" charset="euc-kr"></script>
<script type="text/javascript" src="../jquery/jquery.datetimepicker.full.js"></script>

<script>

	$(document).ready(function(){
		// $('#datepicker').datetimepicker({
		// 	format:'Y-m-d H:i:s',
		// });
		$("#btn_save").click(function(){
			var temp_cp_type=$("#cp_type").val();	//CP_NO
			var temp_manager_nm=$("#manager_nm").val();//업체 담당자
			var temp_counsel_date =$("#counsel_date").val();//상담일
			var temp_counsel_adm_no=$("select[name=counsel_adm_no]").val();//상담자
			var temp_counsel_type=$("select[name=counsel_type]").val();//상담유형

			// alert(temp_counsel_adm_no);
			var temp_ask=$("#ask").val();						//질문
			var temp_answer=$("#answer").val();					//답변
			var temp_seq_no ='<?=$seq_no?>';

			// alert(temp_seq_no);
			// alert("<?=$s_adm_no?>");
			if(temp_cp_type==""){
				alert("업체가 입력되지 않았습니다");
				return;
			}
			if(temp_manager_nm==""){
				if(!confirm("업체 담당자가 등록되지 않았습니다. 계속하시겠습니까"))return;
			}
			if(temp_ask==""){
				if(!confirm("질문이 등록되지 않았습니다. 계속하시겠습니까?")) return;
			}
			if(temp_answer==""){
				if(!confirm("답변이 등록되지 않았습니다. 계속하시겠습니까?"))return;
			}
			$.ajax({
                url: '/manager/ajax_processing.php',
                dataType: 'text',
                type: 'post',
                data : {
                    "mode":"INSERT_COUNSEL",
                    "cp_type":temp_cp_type,
                    "manager_nm":temp_manager_nm,
                    "counsel_date":temp_counsel_date,
                    "counsel_adm_no":temp_counsel_adm_no,//상담자
                    "counsel_type":temp_counsel_type,
                    "ask":temp_ask,
                    "answer":temp_answer,
                    //"s_adm_no":temp_counsel_adm_no,
					"s_adm_no":"<?=$s_adm_no?>", //등록자
                    "seq_no":temp_seq_no
                },
                success: function(response) {
                    if(response.length > 0){
                        if(response == "counsel not exist"){
                            alert("상담이 존재하지 않습니다.\n수정에 실패하였습니다.");
                        } else if(response == "update"){
                            alert("수정되었습니다.");
                        } else if(response == "update fail"){
                            alert("수정에 실패하였습니다.");
                        } else if(response == "insert"){
                            alert("등록되었습니다.");
                        } else if(response == "isnert fail"){
                            alert("등록에 실패하였습니다.");
                        }
                    } else {
                        alert("실패하였습니다.");
                    }
					location.href='counsel_list.php';
                },
                error : function (jqXHR, textStatus, errorThrown) {
                    alert('ERRORS: ' + textStatus);
                }
            });//ajax
		});

	});
</script>
<script>
	function js_selecting_company(targetName, cpName, targetValue, cpNo){
		$(function(){
			$("input[name="+targetName+"]").val(cpName);
			$("input[name="+targetValue+"]").val(cpNo);
		})
	}

	function js_search(){
		var frm=document.frm;
		frm.target="";
		frm.method="get";
		frm.action="<?=$_SERVER["PHP_SELF"]?>";
		frm.submit();
		
	}

</script>
</head>

<body>

<div id="wrap">
	<!-- S: toparea -->
    <? require "../../_common/top_area3.php"; ?>
	<!-- //E: toparea -->

	<!-- S: midarea -->
	<div class="midarea">

		<div class="leftarea">
            <? require "../../_common/left_area3.php"; ?>
        </div>

	
		<div class="contentsarea">
			<div class="menu-modify">
				<h2><strong>상담등록</strong></h2>
				<div class="member-joinbox">
					<div class="join-contents step2">
						<fieldset>
							<legend>정보입력</legend>
							<ul class="form-insert">
								<li>
									<label>업체</label>
									<div>
										<p class="inpbox">
											<input type="text" class="txt txt-orange" name="txt_cp_type" id="txt_cp_type" title="업체" value="<?=getCompanyAutocompleteTextBox($conn,'',$cp_type)?>" placeholder="업체(명/코드) 입력후 엔터를 눌러주세요" />
											<input type="hidden" id="cp_type" name="cp_type" value="<?=$cp_type?>"/>
											<script>
												$(function(){
													$("input[name=txt_cp_type]").keydown(function(e){
														if(e.keyCode==13){
															var keyword=$(this).val();
															//alert('keyword is '+keyword);
															if(keyword==""){
																$("input[name=cp_type]").val('');
															}
															else{
																//alert('keyword is '+keyword);
																$.getJSON( "../company/json_company_list.php?term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
																if(data.length == 1) { 
																	js_selecting_company("txt_cp_type", data[0].label, "cp_type", data[0].id);

																} else if(data.length > 1){ 
																	NewWindow("../company/pop_company_searched_list.php?search_str="+keyword + "&target_name=txt_cp_type&target_value=cp_type",'pop_company_searched_list','950','650','YES');

																} else 
																	alert("검색결과가 없습니다.");
																});
															}
														}
													});

													$("input[name=txt_cp_type]").keyup(function(e){
														var keyword=$(this).val();
														if(keyword == ""){
															$("input[name=cp_type]").val('');
															//alert("아무것도 없습니다");
														}
													})
												});
											</script>
											
										</p>

									</div>
								</li>
								<li>
									<label>업체담당자</label>
									<div>
										<p class="inpbox"><input type="text" id="manager_nm" class="txt" title="업체담당자" value="<?=$manager_nm?>" /></p>
									</div>
								</li>
								<li>
									<label>상담일</label>
									<div class="rev-header">
                                        <div class="datesch">
                                            <span class="datebox"><input type="text" class="txt_date" id="counsel_date" placeholder="상담일 선택" value="<?=$counsel_date?>"/></span>
                                        </div>
                                    </div>
								</li>
								<li>
									<label>상담자</label>
									<div>
										<span class="optionbox">
											<!-- <select title="상담자 선택"><option>홍길동</option><option>홍길동</option><option>홍길동</option><option>홍길동</option></select> -->
											<?
											//기존 상담 내역이면 기존 상담자를 표시하고, 아니면 현재 관리자를 표시한다.
												if($counsel_adm_no != ""){
													echo makeSelectBox1($conn,"COUNSEL_ADM", "counsel_adm_no","선택","",$counsel_adm_no);
												} else {
													echo makeSelectBox1($conn,"COUNSEL_ADM", "counsel_adm_no","선택","",$s_adm_no);
												}
											?>
										</span>
									</div>
								</li>
								<li>
									<label>상담유형</label>
									<div>
										<span class="optionbox">
											<!-- <select title="상담유형 선택"><option>유형</option><option class="selected">유형</option><option>유형</option><option>유형</option></select> -->
											<? echo makeSelectBox1($conn,"COUNSEL_TYPE","counsel_type","선택","",$counsel_type);?>
										</span>
									</div>
								</li>
								<li>
									<label>질문</label>
									<div>
										<textarea id="ask" style="width:100%;height:150px"><?=$ask?></textarea>
									</div>
								</li>
								<li>
									<label>답변</label>
									<div>
										<textarea id="answer" style="width:100%;height:150px"><?=$answer?></textarea>
									</div>
								</li>
							</ul>
							<span class="btncenter">
								<button type="button" class="btn-navy btn-large" id="btn_save">등록</button>
								<button type="button" class="btn-white btn-large" onclick="location.href='counsel_list.php'">취소</button>
							</span>
						</fieldset>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- //E: midarea -->

</div>


</body>
</html>

<script src="../../lib/js/jquery.easing.1.3.js" charset="euc-kr"></script>
<script src="../../lib/js/modernizr-2.8.3-respond-1.4.2.min.js" charset="euc-kr"></script>
<script src="../../lib/js/slick.js" charset="euc-kr"></script>
<script src="../../lib/js/common_ui.js" charset="euc-kr"></script>