/***** Blur *******************************************************************************************/
// �ڹٽ�ũ��Ʈ���� ����ϴ� �������� ����
var g4_path      = "..";
var g4_bbs       = "bbs";
var g4_bbs_img   = "img";
var g4_url       = "/manager";
var g4_charset   = "euc-kr";
var g4_cookie_domain = "";
var g4_is_gecko  = navigator.userAgent.toLowerCase().indexOf("gecko") != -1;
var g4_is_ie     = navigator.userAgent.toLowerCase().indexOf("msie") != -1;


//var myAnchors=document.all.tags("A");
//function allblur() {
//	for (i=0;i<myAnchors.length;i++) {
//		myAnchors[i].onfocus=new Function("blur()");
//	}
//}

//allblur();

function bluring(){
	if(event.srcElement.tagName=="A"||event.srcElement.tagName=="IMG") document.body.focus();
}
document.onfocusin=bluring;

/***** Img RollOver ****************************************************************************************/

function imageOver(imgEl) {
	imgEl.src = imgEl.src.replace(".gif", "_on.gif");
}
function imageOut(imgEl) {
	imgEl.src = imgEl.src.replace("_on.gif", ".gif");
}

/***** Min Width ****************************************************************************************/

function BodyMinSize() {
	var mwid = document.getElementById("mwidthwrap").style;
	if(document.body.clientWidth <= 1024) {
		mwid.width = "1024px";
	} else {
		mwid.width = "100%";
	}
}

/**
* �ͽ��÷η� 7 �̸����� png ������ ó���ϱ� ����
* css �� png24 �ʿ�	: *.png24 {tmp:expression(setPng24(this)); }
* ���� : <img src="image.png" class="png24">
*/
function setPng24(obj)
{
	if (navigator.userAgent.toLowerCase().indexOf("msie 7") < 0) {
		obj.width = obj.height = 1;
		obj.className = obj.className.replace(/\bpng24\b/i,'');
		obj.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+ obj.src +"',sizingMethod='image');"
		obj.src = "";
		return "";
	}
}


String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g,"");   
}

String.prototype.ltrim = function() {   
	return this.replace(/^\s+/,"");      
}   
      
String.prototype.rtrim = function() {   
	return this.replace(/\s+$/,"");      
} 

String.prototype.replaceall = function(_findValue, _replaceValue) {
 return this.replace(new RegExp(_findValue,"g"), _replaceValue);
};

function replaceAll(str, searchStr, replaceStr) {

    return str.split(searchStr).join(replaceStr);
}

function fc_chk_byte(aro_name,ari_max)
{

	var ls_str     = aro_name.value; // �̺�Ʈ�� �Ͼ ��Ʈ���� value ��
	var li_str_len = ls_str.length;  // ��ü����

	// �����ʱ�ȭ
	var li_max      = ari_max; // ������ ���ڼ� ũ��
	var i           = 0;  // for���� ���
	var li_byte     = 0;  // �ѱ��ϰ��� 2 �׹ܿ��� 1�� ����
	var li_len      = 0;  // substring�ϱ� ���ؼ� ���
	var ls_one_char = ""; // �ѱ��ھ� �˻��Ѵ�
	var ls_str2     = ""; // ���ڼ��� �ʰ��ϸ� �����Ҽ� ������������ �����ش�.

	for(i=0; i< li_str_len; i++)
	{
		// �ѱ�������
		ls_one_char = ls_str.charAt(i);

		// �ѱ��̸� 2�� ���Ѵ�.
		if (escape(ls_one_char).length > 4)
		{
			li_byte = li_byte+2;
		}
      // �׿��� ���� 1�� ���Ѵ�.
		else
    {
			li_byte++;
		}

		// ��ü ũ�Ⱑ li_max�� ����������
		if(li_byte <= li_max)
		{
			li_len = i + 1;
		}
	}
   
	// ��ü���̸� �ʰ��ϸ�
	if(li_byte > li_max)
	{
		alert( li_max + " ���ڸ� �ʰ� �Է��Ҽ� �����ϴ�. \n �ʰ��� ������ �ڵ����� ���� �˴ϴ�. ");
		ls_str2 = ls_str.substr(0, li_len);
		aro_name.value = ls_str2;
 
	}
	aro_name.focus();   
}


function isNull(str) {
	str = str.replace(/\s/g, "");
	return ((str == null || str == "" || str == "<undefined>" || str == "undefined") ? true:false);
}

function NewWindow(mypage, myname, w, h, scroll) {
	var winl = (screen.width - w) / 2;
	var wint = (screen.height - h) / 2;
	winprops = 'height='+h+',width='+w+',top='+wint+',left='+winl+',scrollbars='+scroll+',noresize';
	win = window.open(mypage, myname, winprops);

	if(win==null)
		alert("�˾��� ���ܵǾ����ϴ�!"); 
	else 
		win.focus();

	return win;

	//if (parseInt(navigator.appVersion) >= 4) { win.window.focus(); }
}

function NewDownloadWindow(path, params, method) {
    method = method || "post"; // Set method to post by default if not specified.

    // The rest of this code assumes you are not using a library.
    // It can be made less wordy if you use one.
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);

    for(var key in params) {
        if(params.hasOwnProperty(key)) {
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", params[key]);

            form.appendChild(hiddenField);
         }
    }

    document.body.appendChild(form);
    form.submit();
}

function containsCharsOnly(input,chars) {
	
	for (var inx = 0; inx < input.value.length; inx++) {

		if (chars.indexOf(input.value.charAt(inx)) == -1) {
			alert(chars + "�� �Է� �����մϴ�..");
			
			input.value = input.value.substring(0,input.value.length -1);
			
			input.focus();
			return false;
		}
	}
	return true;	
}

// ��ȭ��ȣ �˻� 
//
function isPhoneNumber(input) {
	var chars = "1234567890-";
	//2018-04-24 ��������� ���ϼ� ������ �־� ���� 
	//var chars = "1234567890-~() ";
	return containsCharsOnly(input,chars);
}

function isNumber(input) {
	var chars = "1234567890,";
	return containsCharsOnly(input,chars);
}

function isFloat(input) {
	var chars = "1234567890.";
	return containsCharsOnly(input,chars);
}

function isMathNumber(input) {
	var chars = "-1234567890";
	return containsCharsOnly(input,chars);
}

function isScaleNumber(input) {
	var chars = "1234567890X";
	return containsCharsOnly(input,chars);
}

function _ID(obj){return document.getElementById(obj)}

function openLayer(obj,mode) {
	obj = _ID(obj);
	if (mode) obj.style.display = mode;
	else obj.style.display = (obj.style.display!="none") ? "none" : "block";
}

/*** ���̾� �˾�â ���� ***/
function popupLayer(s,w,h)
{
	if (!w) w = 600;
	if (!h) h = 400;

	var pixelBorder = 3;
	var titleHeight = 12;
	w += pixelBorder * 2;
	h += pixelBorder * 2 + titleHeight;

	var bodyW = document.body.clientWidth;
	var bodyH = document.body.clientHeight;

	var posX = (bodyW - w) / 2;
	var posY = (bodyH - h) / 2;

	hiddenSelectBox('hidden');

	/*** ��׶��� ���̾� ***/
	var obj = document.createElement("div");
	with (obj.style){
		position = "absolute";
		left = 0;
		top = 0;
		width = "100%";
		height = document.body.scrollHeight;
		backgroundColor = "#000000";
		filter = "Alpha(Opacity=80)";
		opacity = "0.5";
	}
	obj.id = "objPopupLayerBg";
	document.body.appendChild(obj);

	/*** ���������� ���̾� ***/
	var obj = document.createElement("div");
	with (obj.style){
		position = "absolute";
		left = posX + document.body.scrollLeft;
		top = posY + document.body.scrollTop;
		width = w;
		height = h;
		backgroundColor = "#ffffff";
		border = "3px solid #000000";
	}
	obj.id = "objPopupLayer";
	document.body.appendChild(obj);

	/*** Ÿ��Ʋ�� ���̾� ***/
	var bottom = document.createElement("div");
	with (bottom.style){
		position = "absolute";
		width = w - pixelBorder * 2;
		height = titleHeight;
		left = 0;
		top = h - titleHeight - pixelBorder * 3;
		padding = "4px 0 0 0";
		textAlign = "center";
		backgroundColor = "#000000";
		color = "#ffffff";
		font = "bold 8pt tahoma; letter-spacing:0px";
		
	}
	bottom.innerHTML = "<a href='javascript:closeLayer()' class='white'>X close</a>";
	obj.appendChild(bottom);

	/*** ���������� ***/
	var ifrm = document.createElement("iframe");
	with (ifrm.style){
		width = w - 6;
		height = h - pixelBorder * 2 - titleHeight - 3;
		//border = "3 solid #000000";
	}
	ifrm.frameBorder = 0;
	ifrm.src = s;
	//ifrm.className = "scroll";
	obj.appendChild(ifrm);
}
function closeLayer()
{
	hiddenSelectBox('visible');
	_ID('objPopupLayer').parentNode.removeChild( _ID('objPopupLayer') );
	_ID('objPopupLayerBg').parentNode.removeChild( _ID('objPopupLayerBg') );
}
function hiddenSelectBox(mode)
{
	var obj = document.getElementsByTagName('select');
	for (i=0;i<obj.length;i++){
		obj[i].style.visibility = mode;
	}
}

function clear_select(obj){
	sel_len = obj.length;
	for(i = sel_len ; i > 0; i--) {
		obj.options[i] = null;
	}
	return ;
}

    // �̹����� ũ�⿡ ���� ��â�� ũ�Ⱑ ����˴ϴ�.
    // zzzz�Բ��� �˷��ּ̽��ϴ�. 2005/04/12
    function image_window(img)
    {
        var w = img.tmp_width; 
        var h = img.tmp_height; 
        var winl = (screen.width-w)/2; 
        var wint = (screen.height-h)/3; 

        if (w >= screen.width) { 
            winl = 0; 
            h = (parseInt)(w * (h / w)); 
        } 

        if (h >= screen.height) { 
            wint = 0; 
            w = (parseInt)(h * (w / h)); 
        } 

        var js_url = "<script language='JavaScript1.2'> \n"; 
            js_url += "<!-- \n"; 
            js_url += "var ie=document.all; \n"; 
            js_url += "var nn6=document.getElementById&&!document.all; \n"; 
            js_url += "var isdrag=false; \n"; 
            js_url += "var x,y; \n"; 
            js_url += "var dobj; \n"; 
            js_url += "function movemouse(e) \n"; 
            js_url += "{ \n"; 
            js_url += "  if (isdrag) \n"; 
            js_url += "  { \n"; 
            js_url += "    dobj.style.left = nn6 ? tx + e.clientX - x : tx + event.clientX - x; \n"; 
            js_url += "    dobj.style.top  = nn6 ? ty + e.clientY - y : ty + event.clientY - y; \n"; 
            js_url += "    return false; \n"; 
            js_url += "  } \n"; 
            js_url += "} \n"; 
            js_url += "function selectmouse(e) \n"; 
            js_url += "{ \n"; 
            js_url += "  var fobj      = nn6 ? e.target : event.srcElement; \n"; 
            js_url += "  var topelement = nn6 ? 'HTML' : 'BODY'; \n"; 
            js_url += "  while (fobj.tagName != topelement && fobj.className != 'dragme') \n"; 
            js_url += "  { \n"; 
            js_url += "    fobj = nn6 ? fobj.parentNode : fobj.parentElement; \n"; 
            js_url += "  } \n"; 
            js_url += "  if (fobj.className=='dragme') \n"; 
            js_url += "  { \n"; 
            js_url += "    isdrag = true; \n"; 
            js_url += "    dobj = fobj; \n"; 
            js_url += "    tx = parseInt(dobj.style.left+0); \n"; 
            js_url += "    ty = parseInt(dobj.style.top+0); \n"; 
            js_url += "    x = nn6 ? e.clientX : event.clientX; \n"; 
            js_url += "    y = nn6 ? e.clientY : event.clientY; \n"; 
            js_url += "    document.onmousemove=movemouse; \n"; 
            js_url += "    return false; \n"; 
            js_url += "  } \n"; 
            js_url += "} \n"; 
            js_url += "document.onmousedown=selectmouse; \n"; 
            js_url += "document.onmouseup=new Function('isdrag=false'); \n"; 
            js_url += "//--> \n"; 
            js_url += "</"+"script> \n"; 

        var settings;

        if (    // �̹����� ũ�⿡ ���� ��â�� ũ�Ⱑ ����˴ϴ�.
    // zzzz�Բ��� �˷��ּ̽��ϴ�. 2005/04/12
    function image_window(img)
    {
        var w = img.tmp_width; 
        var h = img.tmp_height; 
        var winl = (screen.width-w)/2; 
        var wint = (screen.height-h)/3; 

        if (w >= screen.width) { 
            winl = 0; 
            h = (parseInt)(w * (h / w)); 
        } 

        if (h >= screen.height) { 
            wint = 0; 
            w = (parseInt)(h * (w / h)); 
        } 

        var js_url = "<script language='JavaScript1.2'> \n"; 
            js_url += "<!-- \n"; 
            js_url += "var ie=document.all; \n"; 
            js_url += "var nn6=document.getElementById&&!document.all; \n"; 
            js_url += "var isdrag=false; \n"; 
            js_url += "var x,y; \n"; 
            js_url += "var dobj; \n"; 
            js_url += "function movemouse(e) \n"; 
            js_url += "{ \n"; 
            js_url += "  if (isdrag) \n"; 
            js_url += "  { \n"; 
            js_url += "    dobj.style.left = nn6 ? tx + e.clientX - x : tx + event.clientX - x; \n"; 
            js_url += "    dobj.style.top  = nn6 ? ty + e.clientY - y : ty + event.clientY - y; \n"; 
            js_url += "    return false; \n"; 
            js_url += "  } \n"; 
            js_url += "} \n"; 
            js_url += "function selectmouse(e) \n"; 
            js_url += "{ \n"; 
            js_url += "  var fobj      = nn6 ? e.target : event.srcElement; \n"; 
            js_url += "  var topelement = nn6 ? 'HTML' : 'BODY'; \n"; 
            js_url += "  while (fobj.tagName != topelement && fobj.className != 'dragme') \n"; 
            js_url += "  { \n"; 
            js_url += "    fobj = nn6 ? fobj.parentNode : fobj.parentElement; \n"; 
            js_url += "  } \n"; 
            js_url += "  if (fobj.className=='dragme') \n"; 
            js_url += "  { \n"; 
            js_url += "    isdrag = true; \n"; 
            js_url += "    dobj = fobj; \n"; 
            js_url += "    tx = parseInt(dobj.style.left+0); \n"; 
            js_url += "    ty = parseInt(dobj.style.top+0); \n"; 
            js_url += "    x = nn6 ? e.clientX : event.clientX; \n"; 
            js_url += "    y = nn6 ? e.clientY : event.clientY; \n"; 
            js_url += "    document.onmousemove=movemouse; \n"; 
            js_url += "    return false; \n"; 
            js_url += "  } \n"; 
            js_url += "} \n"; 
            js_url += "document.onmousedown=selectmouse; \n"; 
            js_url += "document.onmouseup=new Function('isdrag=false'); \n"; 
            js_url += "//--> \n"; 
            js_url += "</"+"script> \n"; 

        var settings;

        if (g4_is_gecko) {
            settings  ='width='+(w+10)+','; 
            settings +='height='+(h+10)+','; 
        } else {
            settings  ='width='+w+','; 
            settings +='height='+h+','; 
        }
        settings +='top='+wint+','; 
        settings +='left='+winl+','; 
        settings +='scrollbars=no,'; 
        settings +='resizable=yes,'; 
        settings +='status=no'; 


        win=window.open("","image_window",settings); 
        win.document.open(); 
        win.document.write ("<html><head> \n<meta http-equiv='imagetoolbar' CONTENT='no'> \n<meta http-equiv='content-type' content='text/html; charset="+g4_charset+"'>\n"); 
        var size = "�̹��� ������ : "+w+" x "+h;
        win.document.write ("<title>"+size+"</title> \n"); 
        if(w >= screen.width || h >= screen.height) { 
            win.document.write (js_url); 
            var click = "ondblclick='window.close();' style='cursor:move' title=' "+size+" \n\n �̹��� ����� ȭ�麸�� Ů�ϴ�. \n ���� ��ư�� Ŭ���� �� ���콺�� �������� ������. \n\n ���� Ŭ���ϸ� ������. '"; 
        } 
        else 
            var click = "onclick='window.close();' style='cursor:pointer' title=' "+size+" \n\n Ŭ���ϸ� ������. '"; 
        win.document.write ("<style>.dragme{position:relative;}</style> \n"); 
        win.document.write ("</head> \n\n"); 
        win.document.write ("<body leftmargin=0 topmargin=0 bgcolor=#dddddd style='cursor:arrow;'> \n"); 
        win.document.write ("<table width=100% height=100% cellpadding=0 cellspacing=0><tr><td align=center valign=middle><img src='"+img.src+"' width='"+w+"' height='"+h+"' border=0 class='dragme' "+click+"></td></tr></table>");
        win.document.write ("</body></html>"); 
        win.document.close(); 

        if(parseInt(navigator.appVersion) >= 4){win.window.focus();} 
    }) {
            settings  ='width='+(w+10)+','; 
            settings +='height='+(h+10)+','; 
        } else {
            settings  ='width='+w+','; 
            settings +='height='+h+','; 
        }
        settings +='top='+wint+','; 
        settings +='left='+winl+','; 
        settings +='scrollbars=no,'; 
        settings +='resizable=yes,'; 
        settings +='status=no'; 


        win=window.open("","image_window",settings); 
        win.document.open(); 
        win.document.write ("<html><head> \n<meta http-equiv='imagetoolbar' CONTENT='no'> \n<meta http-equiv='content-type' content='text/html; charset="+g4_charset+"'>\n"); 
        var size = "�̹��� ������ : "+w+" x "+h;
        win.document.write ("<title>"+size+"</title> \n"); 
        if(w >= screen.width || h >= screen.height) { 
            win.document.write (js_url); 
            var click = "ondblclick='window.close();' style='cursor:move' title=' "+size+" \n\n �̹��� ����� ȭ�麸�� Ů�ϴ�. \n ���� ��ư�� Ŭ���� �� ���콺�� �������� ������. \n\n ���� Ŭ���ϸ� ������. '"; 
        } 
        else 
            var click = "onclick='window.close();' style='cursor:pointer' title=' "+size+" \n\n Ŭ���ϸ� ������. '"; 
        win.document.write ("<style>.dragme{position:relative;}</style> \n"); 
        win.document.write ("</head> \n\n"); 
        win.document.write ("<body leftmargin=0 topmargin=0 bgcolor=#dddddd style='cursor:arrow;'> \n"); 
        win.document.write ("<table width=100% height=100% cellpadding=0 cellspacing=0><tr><td align=center valign=middle><img src='"+img.src+"' width='"+w+"' height='"+h+"' border=0 class='dragme' "+click+"></td></tr></table>");
        win.document.write ("</body></html>"); 
        win.document.close(); 

        if(parseInt(navigator.appVersion) >= 4){win.window.focus();} 
    }

	
	function numberFormat(val) { 

		//return val.toLocaleString('ko-KR', {minimumFractionDigits: 2});
		val = (''+val).replaceall(",", "");
		return (Math.round(val)).toLocaleString('ko-KR');
	}
	
	/*
	function numberFormat(val) {
		
		val = (''+val).replaceall(",", "");
		val = Math.round(val);

		is_minus = false;
		if(val < 0) {
			is_minus = true;
			val *= -1;
		}
		return (is_minus ? "-" : "") + numberFormat_loop(val);
	}

	function numberFormat_loop(val) {
		val = ''+val; // val�� string���� ��������ȯ
		if(val.length<=3) return val;
 
		var os = val.length%3;
		if(os==0) os=3;
		return val.substring(0,os)+','+numberFormat_loop(val.substring(os));
	}
	*/

	//  Checks that string starts with the specific string
	if (typeof String.prototype.startsWith != 'function') {
		String.prototype.startsWith = function (str) {
			return this.slice(0, str.length) == str;
		};
	}

	//  Checks that string ends with the specific string...
	if (typeof String.prototype.endsWith != 'function') {
		String.prototype.endsWith = function (str) {
			return this.slice(-str.length) == str;
		};
	}


	function eraseSpace(val) {
		var space = /\s+/g;
		return val.replace(space,"");
	}

	function isEmpty(val) {
		return (val == null || eraseSpace(val) == "");
	}

	function lTrim(val) {
		var space = /^\s*/;
		return val.replace(space,"");
	}

	function rTrim(val) {
		var space = /\s*$/;
		return val.replace(space,"");
	}

	function trim(val) {
		return rTrim(lTrim(val));
	}

	function isAlphanumberic(val) {
	var isStr = /^([a-zA-Z0-9]+)$/;   
	return isStr.test(val);
	}

	function isLeapYear(iYear) {
		return !(((iYear % 4) == 0) && ((iYear % 100) != 0) || ((iYear % 400) == 0));
	}

	function getDaysInMonth(iYear, iMonth) {
		var tmpByte = 0;

		if (iMonth == 1 || iMonth == 3 || iMonth == 5 || iMonth == 7 || iMonth == 8 || iMonth == 10 || iMonth == 12) {
			tmpByte = 31;
		} else if (iMonth == 4 || iMonth == 6 || iMonth == 9 || iMonth == 11) {
			tmpByte = 30;
		} else if (iMonth == 2) {
			if (isLeapYear(iYear)) {
				tmpByte = 28;
			} else {
				tmpByte = 29;
			}
		}

		return tmpByte;
	}

	function addZero(n) {
		return n < 10 ? "0" + n : n;
	}

	
	function checkStaEndDt(staObj, endObj) {

		var regDate = /^([1|2]\d{3})[\-\/\.]?(0[1-9]|1[012])[\-\/\.]?(0[1-9]|[12][0-9]|3[0-1])$/;

		var d = new Date();
		var staDt;
		var endDt;

		var toDay =
			d.getFullYear() + '-' +
			addZero(d.getMonth() + 1, 2) + '-' +
			addZero(d.getDate(), 2);

		if (!isEmpty(eraseSpace(staObj.val()))) {
			if (staObj.val().match(regDate) == null) {
				alert("��¥�� ���Ŀ� ���� �ʽ��ϴ�.");
				staObj.val("");
				return false;
			} 
			else {
				if (parseInt(RegExp.$3, 10) < 0 || parseInt(RegExp.$3, 10) > getDaysInMonth(parseInt(RegExp.$1, 10), parseInt(RegExp.$2, 10))) {
					alert("��¥�� ���Ŀ� ���� �ʽ��ϴ�.");
					staObj.val("");
					return false;
				} else {
					staDt = RegExp.$1 + "-" + RegExp.$2 + "-" + RegExp.$3;

					//if (staDt > toDay) {
					//   open_message('Alter',  'admin.common.date.todayEarly', "");
					//    return false;
					//}
				}
			}
		}

		if (!isEmpty(eraseSpace(endObj.val()))) {
			if (endObj.val().match(regDate) == null) {
				alert("��¥�� ���Ŀ� ���� �ʽ��ϴ�.");
				endObj.val("");
				return false;
			} else {
				if (parseInt(RegExp.$3, 10) < 0 || parseInt(RegExp.$3, 10) > getDaysInMonth(parseInt(RegExp.$1, 10), parseInt(RegExp.$2, 10))) {
					alert("��¥�� ���Ŀ� ���� �ʽ��ϴ�.");
					endObj.val("");
					return false;
				} else {
					endDt = RegExp.$1 + "-" + RegExp.$2 + "-" + RegExp.$3;
				}
			}
		}

		//if (!isEmpty(eraseSpace(staObj.val())) && !isEmpty(eraseSpace(endObj.val()))) {
			//if (staDt > endDt) {
			//    open_message('Alter', 'admin.common.date.todayEarly', "");
			//    return false;
			//}
		//}

		staObj.val(staDt);
		endObj.val(endDt);
		return true;

	}

	function checkDt(staObj) {

		var regDate = /^([1|2]\d{3})[\-\/\.]?(0[1-9]|1[012])[\-\/\.]?(0[1-9]|[12][0-9]|3[0-1])$/;

		var d = new Date();
		var staDt;

		var toDay =
			d.getFullYear() + '-' +
			addZero(d.getMonth() + 1, 2) + '-' +
			addZero(d.getDate(), 2);

		if (!isEmpty(eraseSpace(staObj.val()))) {
			if (staObj.val().match(regDate) == null) {
				alert("��¥�� ���Ŀ� ���� �ʽ��ϴ�.");
				staObj.val("");
				return false;
			} 
			else {
				if (parseInt(RegExp.$3, 10) < 0 || parseInt(RegExp.$3, 10) > getDaysInMonth(parseInt(RegExp.$1, 10), parseInt(RegExp.$2, 10))) {
					alert("��¥�� ���Ŀ� ���� �ʽ��ϴ�.");
					staObj.val("");
					return false;
				} else {
					staDt = RegExp.$1 + "-" + RegExp.$2 + "-" + RegExp.$3;

					//if (staDt > toDay) {
					//   open_message('Alter',  'admin.common.date.todayEarly', "");
					//    return false;
					//}
				}
			}
		}

		//if (!isEmpty(eraseSpace(staObj.val())) && !isEmpty(eraseSpace(endObj.val()))) {
			//if (staDt > endDt) {
			//    open_message('Alter', 'admin.common.date.todayEarly', "");
			//    return false;
			//}
		//}

		staObj.val(staDt);
		return true;

	}

String.prototype.contains = function(it) { return this.indexOf(it) != -1; };

// Closure
(function() {
  /**
   * Decimal adjustment of a number.
   *
   * @param {String}  type  The type of adjustment.
   * @param {Number}  value The number.
   * @param {Integer} exp   The exponent (the 10 logarithm of the adjustment base).
   * @returns {Number} The adjusted value.
   */
  function decimalAdjust(type, value, exp) {
    // If the exp is undefined or zero...
    if (typeof exp === 'undefined' || +exp === 0) {
      return Math[type](value);
    }
    value = +value;
    exp = +exp;
    // If the value is not a number or the exp is not an integer...
    if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0)) {
      return NaN;
    }
    // Shift
    value = value.toString().split('e');
    value = Math[type](+(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp)));
    // Shift back
    value = value.toString().split('e');
    return +(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp));
  }

  // Decimal round
  if (!Math.round10) {
    Math.round10 = function(value, exp) {
      return decimalAdjust('round', value, exp);
    };
  }
  // Decimal floor
  if (!Math.floor10) {
    Math.floor10 = function(value, exp) {
      return decimalAdjust('floor', value, exp);
    };
  }
  // Decimal ceil
  if (!Math.ceil10) {
    Math.ceil10 = function(value, exp) {
      return decimalAdjust('ceil', value, exp);
    };
  }
})();

function br2nl(str) {
    return str.replace(/<br\s*\/?>/mg,"\n");
}

function nl2br (str, is_xhtml) {   
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';    
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag);
}

function disabledEventPropagation(event)
{
   if (event.stopPropagation){
       event.stopPropagation();
   }
   else if(window.event){
      window.event.cancelBubble=true;
   }
}

/**** ��¥��� ****/
function formatDate(date) {
    var mymonth = date.getMonth() + 1;
    var myweekday = date.getDate();
    return (date.getFullYear() + "-" + ((mymonth < 10) ? "0" : "") + mymonth + "-" + ((myweekday < 10) ? "0" : "") + myweekday);
}

// ����
function SetToday(begin, end) {
    var obj1 = document.getElementById(begin);
    var obj2 = document.getElementById(end);
    var mydate = new Date();
    mydate.setDate(mydate.getDate());
    obj1.value = formatDate(mydate);
    if (obj2 != null) {
        obj2.value = obj1.value;
    }
}

// ����
function SetYesterday(begin, end) {
    var obj1 = document.getElementById(begin);
    var obj2 = document.getElementById(end);
    var mydate = new Date();
    mydate.setDate(mydate.getDate() - 1);
    obj1.value = formatDate(mydate);
    if (obj2 != null) {
        obj2.value = obj1.value;
    }
}
// ������
function SetPrevWeek(begin, end) {
    var obj1 = document.getElementById(begin);
    var obj2 = document.getElementById(end);
    var now = new Date();
	now.setDate(now.getDate() - 7);
    var nowDayOfWeek = now.getDay();
    var nowDay = now.getDate();
    var nowMonth = now.getMonth();
    var nowYear = now.getFullYear();
    nowYear += (nowYear < 2000) ? 1900 : 0;
    var weekStartDate = new Date(nowYear, nowMonth, nowDay - nowDayOfWeek);
    var weekEndDate = new Date(nowYear, nowMonth, nowDay + (6 - nowDayOfWeek));
    obj1.value = formatDate(weekStartDate);
    obj2.value = formatDate(weekEndDate);
}
// �̹���
function SetWeek(begin, end) {
    var obj1 = document.getElementById(begin);
    var obj2 = document.getElementById(end);
    var now = new Date();
    var nowDayOfWeek = now.getDay();
    var nowDay = now.getDate();
    var nowMonth = now.getMonth();
    var nowYear = now.getFullYear();
    nowYear += (nowYear < 2000) ? 1900 : 0;
    var weekStartDate = new Date(nowYear, nowMonth, nowDay - nowDayOfWeek);
    var weekEndDate = new Date(nowYear, nowMonth, nowDay + (6 - nowDayOfWeek));
    obj1.value = formatDate(weekStartDate);
    obj2.value = formatDate(weekEndDate);
}
// 7����
function Set7Days(begin, end) {
    var obj1 = document.getElementById(begin);
    var obj2 = document.getElementById(end);
    var mydate = new Date();
    mydate.setDate(mydate.getDate() - 7);
    obj1.value = formatDate(mydate);
    obj1.focus();
    obj2.value = formatDate(new Date());
    obj2.focus();
}
// 30����
function Set30Days(begin, end) {
    var obj1 = document.getElementById(begin);
    var obj2 = document.getElementById(end);
    var mydate = new Date();
    mydate.setDate(mydate.getDate() - 30);
    obj1.value = formatDate(mydate);
    obj1.focus();
    obj2.value = formatDate(new Date());
    obj2.focus();
}
// 90����
function Set90Days(begin, end) {
    var obj1 = document.getElementById(begin);
    var obj2 = document.getElementById(end);
    var mydate = new Date();
    mydate.setDate(mydate.getDate() - 90);
    obj1.value = formatDate(mydate);
    obj1.focus();
    obj2.value = formatDate(new Date());
    obj2.focus();
}
// �̹���
function SetCurrentMonthDays(begin, end) {
    var obj1 = document.getElementById(begin);
    var obj2 = document.getElementById(end);
    var d2, d22;
    d2 = new Date();
    d22 = new Date(d2.getFullYear(), d2.getMonth());
    var d3, d33;
    d3 = new Date();
    d33 = new Date(d3.getFullYear(), d3.getMonth() + 1, "");
    obj1.value = formatDate(d22);
    obj1.focus();
    obj2.value = formatDate(d33);
    obj2.focus();
}
// ������
function SetPrevMonthDays(begin, end) {
    var obj1 = document.getElementById(begin);
    var obj2 = document.getElementById(end);
    var d2, d22;
    d2 = new Date();
    d22 = new Date(d2.getFullYear(), d2.getMonth() -1);
    var d3, d33;
    d3 = new Date();
    d33 = new Date(d3.getFullYear(), d3.getMonth(), "");
    obj1.value = formatDate(d22);
    obj1.focus();
    obj2.value = formatDate(d33);
    obj2.focus();
}
// ����
function SetCurrentYearDays(begin, end) {
    var obj1 = document.getElementById(begin);
    var obj2 = document.getElementById(end);
    var d2, d22;
    d2 = new Date();
    d22 = new Date(d2.getFullYear() ,"0","1");
    var d3, d33;
    d3 = new Date();
    d33 = new Date(d3.getFullYear() + 1,"", "");
    obj1.value = formatDate(d22);
    obj1.focus();
    obj2.value = formatDate(d33);
    obj2.focus();
}
// ����
function SetPrevYearDays(begin, end) {
    var obj1 = document.getElementById(begin);
    var obj2 = document.getElementById(end);
    var d2, d22;
    d2 = new Date();
    d22 = new Date(d2.getFullYear() - 1 ,"0","1");
    var d3, d33;
    d3 = new Date();
    d33 = new Date(d3.getFullYear(),"", "");
    obj1.value = formatDate(d22);
    obj1.focus();
    obj2.value = formatDate(d33);
    obj2.focus();
}


var Base64 = {
// private property
_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

// public method for encoding
encode : function (input) {
    var output = "";
    var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
    var i = 0;

    input = Base64._utf8_encode(input);

    while (i < input.length) {

        chr1 = input.charCodeAt(i++);
        chr2 = input.charCodeAt(i++);
        chr3 = input.charCodeAt(i++);

        enc1 = chr1 >> 2;
        enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
        enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
        enc4 = chr3 & 63;

        if (isNaN(chr2)) {
            enc3 = enc4 = 64;
        } else if (isNaN(chr3)) {
            enc4 = 64;
        }

        output = output +
        Base64._keyStr.charAt(enc1) + Base64._keyStr.charAt(enc2) +
        Base64._keyStr.charAt(enc3) + Base64._keyStr.charAt(enc4);

    }

    return output;
},

// public method for decoding
decode : function (input) {
    var output = "";
    var chr1, chr2, chr3;
    var enc1, enc2, enc3, enc4;
    var i = 0;

    input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

    while (i < input.length) {

        enc1 = Base64._keyStr.indexOf(input.charAt(i++));
        enc2 = Base64._keyStr.indexOf(input.charAt(i++));
        enc3 = Base64._keyStr.indexOf(input.charAt(i++));
        enc4 = Base64._keyStr.indexOf(input.charAt(i++));

        chr1 = (enc1 << 2) | (enc2 >> 4);
        chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
        chr3 = ((enc3 & 3) << 6) | enc4;

        output = output + String.fromCharCode(chr1);

        if (enc3 != 64) {
            output = output + String.fromCharCode(chr2);
        }
        if (enc4 != 64) {
            output = output + String.fromCharCode(chr3);
        }

    }

    output = Base64._utf8_decode(output);

    return output;

},

// private method for UTF-8 encoding
_utf8_encode : function (string) {
    string = string.replace(/\r\n/g,"\n");
    var utftext = "";

    for (var n = 0; n < string.length; n++) {

        var c = string.charCodeAt(n);

        if (c < 128) {
            utftext += String.fromCharCode(c);
        }
        else if((c > 127) && (c < 2048)) {
            utftext += String.fromCharCode((c >> 6) | 192);
            utftext += String.fromCharCode((c & 63) | 128);
        }
        else {
            utftext += String.fromCharCode((c >> 12) | 224);
            utftext += String.fromCharCode(((c >> 6) & 63) | 128);
            utftext += String.fromCharCode((c & 63) | 128);
        }

    }

    return utftext;
},

// private method for UTF-8 decoding
_utf8_decode : function (utftext) {
    var string = "";
    var i = 0;
    var c = c1 = c2 = 0;

    while ( i < utftext.length ) {

        c = utftext.charCodeAt(i);

        if (c < 128) {
            string += String.fromCharCode(c);
            i++;
        }
        else if((c > 191) && (c < 224)) {
            c2 = utftext.charCodeAt(i+1);
            string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
            i += 2;
        }
        else {
            c2 = utftext.charCodeAt(i+1);
            c3 = utftext.charCodeAt(i+2);
            string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
            i += 3;
        }

    }
    return string;
}

}