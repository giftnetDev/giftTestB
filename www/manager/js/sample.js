/**
* �ͽ��÷η� 7 �̸����� png ������ ó���ϱ� ����
* css �� png24 �ʿ�	: *.png24 {tmp:expression(setPng24(this)); }
* ���� : <img src="image.png" class="png24">
*/
function setPng24(obj)
{
    obj.width = obj.height = 1;
    obj.className = obj.className.replace(/\bpng24\b/i,'');
    obj.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+ obj.src +"',sizingMethod='image');"
    obj.src = "";
    return "";
}