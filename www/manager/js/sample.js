/**
* 익스플로러 7 미만에서 png 파일을 처리하기 위함
* css 에 png24 필요	: *.png24 {tmp:expression(setPng24(this)); }
* 사용법 : <img src="image.png" class="png24">
*/
function setPng24(obj)
{
    obj.width = obj.height = 1;
    obj.className = obj.className.replace(/\bpng24\b/i,'');
    obj.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+ obj.src +"',sizingMethod='image');"
    obj.src = "";
    return "";
}