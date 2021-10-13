<!DOCTYPE html>
<head>
    <title>기존 거래처 매칭</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <script src="../js/jquery.js"></script>
    <style>
        ul{
            padding:0px;
        }

        li{
            padding-left:40px;
            padding-top:10px;
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

            opener.document.getElementById("cp_no").value = cpinfo[0];

            opener.document.getElementById("companyname").value = cpinfo[1];
            opener.document.getElementById("biz_num1").value = cpinfo[2].substring(0,3);
            opener.document.getElementById("biz_num2").value = cpinfo[2].substring(4,6);
            opener.document.getElementById("biz_num3").value = cpinfo[2].substring(7,12);
            opener.document.getElementById("ceoname").value = cpinfo[3];

            window.opener.js_popClose();
            self.close();
        }

        $(document).ready(function(){

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

                                cp_list += "<li onClick='pickCompany(this.id);' id='"+cp_no+"|"+cp_nm+"|"+biz_no+"|"+ceo_nm+"' style='border-bottom:1px solid #E0E0E0;'><dl><dt>"+cp_nm+"&nbsp;</dt><dt>"+cp_nm2+"&nbsp;</dt><dd>"+biz_no+"&nbsp;</dd></dl></li>"
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

                                    cp_list += "<li onClick='pickCompany(this.id);' id='"+cp_no+"|"+cp_nm+"|"+biz_no+"|"+ceo_nm+"' style='border-bottom:1px solid #E0E0E0;'><dl><dt>"+cp_nm+"&nbsp;</dt><dt>"+cp_nm2+"&nbsp;</dt><dd>"+biz_no+"&nbsp;</dd></dl></li>"
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

            if (opener.document.getElementById("biz_num1").value != "" && opener.document.getElementById("biz_num2").value != "" && opener.document.getElementById("biz_num3").value != "")
            {
                $("#search_str").val(opener.document.getElementById("biz_num1").value+opener.document.getElementById("biz_num2").value+opener.document.getElementById("biz_num3").value);
                $("#search_btn").trigger("click");
            }

        });
    </script>
</head>
<body>
    <br>    
    <div class="form-group">
        <div class="col-sm-10 col-lg-offset-0 col-lg-9">
            <select class="form-control" style="width:150px;display:inline;" id="search_type">
                <option value="1">사업자등록번호</option>
                <option value="2">업체명</option>
            </select>
            <input class="form-control" style="width:150px;display:inline;" id="search_str" name="search_str" type="text" value=""> 
            <input type="button" id="search_btn" style="width:100px;margin-bottom:3px;display:inline;" class="btn-sm btn btn-default" value="검색">
        </div>
    </div>
    <hr>
    <div>
        <ul id = "company_list" style="list-style:none;">
        </ul>
    </div>
</body>
</html>