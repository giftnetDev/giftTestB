
<!DOCTYPE HTML>
<HTML>
    <HEAD>
        <script>
            function add(){
                $.ajax({
                    url: "ajaxActivity.php",
                    dataType:"json"
                    type:"POST",
                    
                    data:{'num1' : num1, 'num2' : num2},
                    sucess:function(response){

                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR.responseText); 
                    }
                    

                });
            }    
        </script>
        <script>
            $(document).ready(function(){
                var num1=$('#num1').val();
                var num2=$('#num2').val();
                var datas={'num1':num1, 'num2':num2};
                var request=$.ajax({
                    url:"ajaxActivity.php",
                    method:"POST",
                    dataType:"json",
                    data:datas,
                });
                request.done(function(data){
                    console.log(data);
                    $('#addHtml').html(data);
                });
                request.fail(function(jqXHR, textStatus){
                    alert("Request failed: "+textStatus);
                });
            });
        </script>

    </HEAD>

    <BODY>
        <DIV>
            <FORM NAME="frmAdd", ID="frmAdd">
               <select id="num1">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="5">6</option>
                    <option value="5">7</option>
                    <option value="5">8</option>
                    <option value="5">9</option>
                </select>
                X
                <select id="num2">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="5">6</option>
                    <option value="5">7</option>
                    <option value="5">8</option>
                    <option value="5">9</option>
                </select>

                <input type="text" id="inputNum">
            </FORM>
        </DIV>
    </BODY>
</HTML>
