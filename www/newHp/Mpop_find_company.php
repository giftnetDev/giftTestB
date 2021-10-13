<!DOCTYPE html>
<html lang="ko">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>기존 거래처 매칭</title>

	<script src="js/jquery.min.js"></script>
	
	<script src="js/all.js"></script>
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/gh/moonspam/NanumSquare@1.0/nanumsquare.css">
	<link rel="stylesheet" href="css/stylemb.css" type='text/css'>

    <style>
        ul{
            padding:0px;
        }

        li{
            padding-left:20px;
        }
        
        li:hover{
            background-color:#f0f0f0;
            cursor: pointer;
        }
        hr{
            margin-bottom: 0px;
        }
    </style>
    <script>
        function pickCompany(cp_info)
        {
            var cpinfo = cp_info.split('|');
            
            /*alert(cp_info);
            alert(cpinfo[0]);
            alert(cpinfo[1]);
            alert(cpinfo[2]);
            alert(cpinfo[3]);*/

            parent.document.getElementById("cp_no").value = cpinfo[0];

            parent.document.getElementById("companyname").value = cpinfo[1];
            parent.document.getElementById("biz_num1").value = cpinfo[2].substring(0,3);
            parent.document.getElementById("biz_num2").value = cpinfo[2].substring(4,6);
            parent.document.getElementById("biz_num3").value = cpinfo[2].substring(7,12);
            parent.document.getElementById("ceoname").value = cpinfo[3];

            parent.js_popClose();

            parent.document.getElementById("company_popup").style.display = "none";
        }

        function iframeOnload()
        {

            //event binding
            $("#search_btn").bind("click",function(){
                
                var search_type = $("#search_type").val();
                var search_str = $("#search_str").val();
                
                //ajax
                $.ajax({
					url: '/manager/ajax_processing.php',
					dataType: 'json',
					type: 'post',
					data : {
						"mode":"SELECT_COMPANY_NO",
						"search_type":search_type,
						"search_str":search_str
					},
					success: function(response) {
                        //리스트 출력
						if(response.length > 0){
                            cp_list = "";
							for(i=0;i<response.length;i++){
                                cp_no = response[i]["cp_no"];
                                cp_nm = response[i]["cp_nm"];
                                cp_nm2 = response[i]["cp_nm2"];
                                biz_no = response[i]["biz_no"];
                                ceo_nm = response[i]["ceo_nm"];

                                cp_list += "<li onClick='pickCompany(this.id);' id='"+cp_no+"|"+cp_nm+"|"+biz_no+"|"+ceo_nm+"' style='border-bottom:1px solid #E0E0E0; text-align:left;'><dl><dt><span>"+cp_nm+"&nbsp;</span></dt><dt><span>"+cp_nm2+"&nbsp;</span></dt><dt><span>"+biz_no+"&nbsp;</span></dt></dl></li>"
                            }
                            $("#company_list").html(cp_list);
                        } else {
                            cp_list = "<li>표시할 결과가 없습니다.</li>";
                            $("#company_list").html(cp_list);
                        }
					},//success
					error : function (jqXHR, textStatus, errorThrown) {
						alert('ERRORS: ' + textStatus);
					}//error
				});//ajax
            });


            $(document).keydown(function (key) {
                //엔터
                if(key.keyCode == 13){
                    var search_type = $("#search_type").val();
                    var search_str = $("#search_str").val();
                    //"-" 제거
                    search_str = search_str.replace("-", "");

                    //ajax
                    $.ajax({
                        url: '/manager/ajax_processing.php',
                        dataType: 'json',
                        type: 'post',
                        data : {
                            "mode":"SELECT_COMPANY_NO",
                            "search_type":search_type,
                            "search_str":search_str
                        },
                        success: function(response) {
                            //리스트 출력
                            if(response.length > 0){
                                cp_list = "";
                                for(i=0;i<response.length;i++){
                                    cp_no = response[i]["cp_no"];
                                    cp_nm = response[i]["cp_nm"];
                                    cp_nm2 = response[i]["cp_nm2"];
                                    biz_no = response[i]["biz_no"];
                                    ceo_nm = response[i]["ceo_nm"];

                                    cp_list += "<li onClick='pickCompany(this.id);' id='"+cp_no+"|"+cp_nm+"|"+biz_no+"|"+ceo_nm+"' style='border-bottom:1px solid #E0E0E0; text-align:left;'><dl><dt><span>"+cp_nm+"&nbsp;</span></dt><dt><span>"+cp_nm2+"&nbsp;</span></dt><dt><span>"+biz_no+"&nbsp;</span></dt></dl></li>"
                                }
                                $("#company_list").html(cp_list);
                            } else {
                                cp_list = "<li>표시할 결과가 없습니다.</li>";
                                $("#company_list").html(cp_list);
                            }
                        },//success
                        error : function (jqXHR, textStatus, errorThrown) {
                            alert('ERRORS: ' + textStatus);
                        }//error
                    });//ajax
                }
            });

            if (parent.document.getElementById("biz_num1").value != "" && parent.document.getElementById("biz_num2").value != "" && parent.document.getElementById("biz_num3").value != "")
            {
                $("#search_type").val("1");
                $("#search_str").val(parent.document.getElementById("biz_num1").value+parent.document.getElementById("biz_num2").value+parent.document.getElementById("biz_num3").value);
                $("#search_btn").trigger("click");
            }
            else
            {
                $("#search_type").val("1");
                $("#search_str").val("");
                $("#company_list").html("");
            }
        }
    </script>
</head>
<body class="wrap_cp_body">
    <div class="wrap_cp">
        <div>
            <b>거래처 검색</b>
            <select id="search_type">
                <option value="1">사업자등록번호</option>
                <option value="2">업체명</option>
            </select>
            <input id="search_str" name="search_str" type="text" value="" style="margin-bottom: 0%;"> 
            <div class="trightidpw">
                <a href="#" id="search_btn" class="cartingidpw">검색</a>
            </div>

            <hr>
            <div class="overflow-y" style="margin-top: 10px;">
                <ul id = "company_list" style="list-style:none;">
                </ul>
            </div>

        </div>
    </div>    
</body>
</html>