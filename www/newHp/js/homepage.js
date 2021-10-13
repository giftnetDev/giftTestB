function js_move_page(page){
    location.href=page;
}
function js_delete_from_wishList(goodsNo, memberNo){
    if(memberNo==""){
        return;
    }
    $.ajax({
        url:'/ajax/ajax_process.php',
        dataType:"JSON",
        type:'POST',
        async:true,
        data:{
            'mode':"EXCLUDE_GOODS_FROM_WISHLIST",
            'goodsNo':goodsNo,
            'memberNo':memberNo,
        },
        success:function(data){
            // alert(data);
            $("#like_"+goodsNo).remove();    
            $("#likeSymbol_"+goodsNo).removeClass("like_on");
            cntLike=Number(data);           
            // cntLike--;
            $(".like_cnt").html(cntLike);
            js_like_region_process(cntLike);
            if(cntLike>0){  
                $("#imgLike").attr("src","./img/like_c_on.png");
            }
            else{
                $("#imgLike").attr("src","./img/like_c.png");
            }
            
        },
        error:function(jqXHR, textStatus, errorThrown){

        }
    });
}
function js_add_goods_to_wishList(goodsNo, imgUrl, memberNo, cpNo, obj){
    if(memberNo==""){
        $("#login_popup").css("display","block");
        alert('로그인 후 이용해 주세요!!');
        return;
    }
    var cntLike=0;
    if(obj.className=="like"){
        obj.classList.add('like_on');
        // alert('추가1');

        cntLike=Number($(".like_cnt").html()); 
        $.ajax({
            url:'/ajax/ajax_process.php',
            dataType:"JSON",
            type:'POST',
            async:true,
            data:{
                'mode':"ADD_GOODS_TO_WISHLIST",
                'goodsNo':goodsNo,
                'memberNo':memberNo,
                'cpNo':cpNo,
                'imgUrl':imgUrl
            },
            success:function(data){
                // alert(data);
                cntLike=Number($(".like_cnt").html());    
                cntLike++;
                $(".like_cnt").html(cntLike);
                js_add_wishList_div(data, memberNo)

                js_like_region_process(cntLike);
                if(cntLike>0){  
                    $("#imgLike").attr("src","./img/like_c_on.png");
                }
                else{
                    $("#imgLike").attr("src","./img/like_c.png");
                }
                

            },
            error:function(jqXHR, textStatus, errorThrown){
                alert('실패..');
             }
        });
    }//end of if(like)
    else{
        obj.classList.remove('like_on');
        // alert('삭제1');

        $.ajax({
            url:'/ajax/ajax_process.php',
            dataType:"JSON",
            type:'POST',
            async:true,
            data:{
                'mode':"EXCLUDE_GOODS_FROM_WISHLIST",
                'goodsNo':goodsNo,
                'memberNo':memberNo,
            },
            success:function(data){
                // alert('삭제되었습니다!');
                // cntLike=Number($(".like_cnt").html());               
                cntLike=data;
                alert('삭제되었습니다');
                $(".like_cnt").html(cntLike);
                // cntLike=Number($(".like_cnt").html()); 
                $('#like_'+goodsNo).remove();

                js_like_region_process(cntLike);
                if(cntLike>0){  
                    $("#imgLike").attr("src","./img/like_c_on.png");
                }
                else{
                    $("#imgLike").attr("src","./img/like_c.png");
                }
            },
            error:function(jqXHR, textStatus, errorThrown){
            }
        });

    }
    // alert(cntLike);

}//end of function
function js_like_region_process(num){
    if(num>4){
        $(".like_region").css("overflow","hidden");
        $(".like_region").css("overflow-y","auto");
        $(".like_region").css('height',"452px");
        $(".like_region").css('width',"130px");
        $(".like_region").css("left","0px");
        $(".like_item").css("right","5px");

        // $(".like_region").css("overflow","auto");
    }
    else{
        $(".like_region").css("overflow","");
        $(".like_region").css("overflow-y","");
        $(".like_region").css('height',"");
        $(".like_region").css('width',"");
        $(".like_region").css("left","10px");
        $(".like_item").css("right","20px");

    }

}


function js_add_wishList_div(data, memberNo){
    var newDiv= "<div class='like_item' id='like_"+data['GOODS_NO']+"' onmouseover='js_view_item_description("+data['GOODS_NO']+")' onmouseout='js_hide_item_description("+data['GOODS_NO']+")'>";
        newDiv+="<div><div class='like_close' ";
        newDiv+="onclick='js_delete_from_wishList("+data['GOODS_NO']+","+memberNo+")'>X</div><a href='#'>";
        newDiv+="<img src='"+data['IMG_URL']+"' width='100px'>";
        newDiv+="</a></div>";
        newDiv+="<div class='like_item_description' id='description_"+data['GOODS_NO']+"' >";
        newDiv+=data['GOODS_CODE']+"<br>"+data['GOODS_NAME']+"<br>"+data['SALE_PRICE']+"원";
        newDiv+="</div>"//class='like_item_description
        newDiv+="</div>";//class='like_item'
    $('.like_region').append(newDiv);
}
function js_view_item_description(goodsNo){
    // alert('test');
    $('#description_'+goodsNo).css('display','block');
    $('.like_region').css("width","370px");
    // $('.like_region').css("height","500px");
    $('.like_item').css("right","-240px");
}
function js_hide_item_description(goodsNo){
    // alert('test11');
    $('#description_'+goodsNo).css('display','none');
    $('.like_region').css("width","130px");
    // $('.like_region').css("height","400px");
    $('.like_item').css("right","0px");
    
}
