<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>��������</title>
<style>
	td, div, input, textarea, select, iframe {
		font-size:12; font-family:��������; color:#474747;
	}
	.sfont { font-family:tahoma; font-size:10; }
	.sgfont { font-family:tahoma; font-size:10; color:#bbbbbb; }
	.srfont { font-family:tahoma; font-size:10; color:#ff0000; }
	.srgfont { font-family:tahoma; font-size:10; color:#ff8989; }
	.schedulebar { position:absolute;padding-left:5;word-break:break-all;height:17;line-height:1.4;overflow:hidden; }
</style>
<meta http-equiv="Content-Type" content="text/html;charset=euc-kr">
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" style="overflow-y:auto;">

<!-- ���� ����� start -->
<div id="calendarFormDiv" style="width:100;height:100;position:absolute;display:none;width:100%;height:100%;z-index:50;">
<table width="500" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff" style="z-index:102;position:absolute;border:3px solid #cccccc;">
  <tr bgcolor="#eeeeee">
    <td height="30" style="padding-left:10;"><b><li>�������</li></b></td>
  </tr>
  <tr><form name="calendarForm">
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="5">
        <tr>
          <td width="80" align="center">�Ͻ�</td>
          <td>
            <input type="text" name="sdate" style="width:100;border:1px solid #cccccc;text-align:center;cursor:hand;" onclick="FormDiaryView(this.name);" readonly> ~
            <input type="text" name="edate" style="width:100;border:1px solid #cccccc;text-align:center;cursor:hand;" onclick="FormDiaryView(this.name);" readonly>
            <!-- ���� ���� DIV start -->
            <div id="calendardiaryDiv" style="position:absolute;display:none;background-color:#cccccc;margin-top:22;"></div>
            <!-- ���� ���� DIV start -->
          </td>
        </tr>
        <tr>
          <td align="center">����</td>
          <td>
            <input type="text" name="title" style="width:350;border:1px solid #cccccc;">
          </td>
        </tr>
        <tr>
          <td align="center">ī�װ���</td>
          <td>
            <table width="150" height="20" border="0" cellspacing="0" cellpadding="0" style="cursor:hand;" onclick="cateView();">
              <tr id="cateTr" bgcolor="#6e9cf2">
                <td id="test" style="padding-left:5;color:#ffffff;">��������</td>
                <td id="test" width="1%" style="padding-right:5;color:#ffffff;font-family:tahoma;font-size:8;">��</td>
              </tr>
            </table>
            <input type="hidden" name="cate"><input type="hidden" name="sdt"><input type="hidden" name="edt">
            <!-- ī�װ��� ���� DIV start -->
            <div id="calendarcateDiv" style="position:absolute;display:none;margin-top:2;">
            <table width="150" border="0" cellspacing="0" cellpadding="0" style="cursor:hand;">
            </table>
            </div>
            <!-- ī�װ��� ���� DIV end -->
          </td>
        </tr>
        <tr><td colspan="2" height="5"></td></tr>
        <tr><td colspan="2" bgcolor="#cccccc" height="1"></td></tr>
        <tr><td colspan="2" height="5"></td></tr>
          <td align="center">�޸�</td>
          <td>
            <textarea name="memo" rows="7" style="width:350;border:1px solid #cccccc;overflow-y:auto;"></textarea>
          </td>
        </tr>
        <tr><td colspan="2" height="5"></td></tr>
        <tr><td colspan="2" bgcolor="#cccccc" height="1"></td></tr>
        <tr><td colspan="2" height="5"></td></tr>
        <tr>
          <td colspan="2" align="center">
            <input type="button" value="�����ϱ�" style="width:100;height:25;border:1px solid #aaaaaa;background-color:#cccccc;" onclick="diarySubmit();">
            <input type="button" value="���" style="width:100;height:25;border:1px solid #cccccc;background-color:#eeeeee;" onclick="cFclose();">
          </td>
        </tr>
      </table>
    </td></form>
  </tr>
</table>
<div style="z-index:101;position:absolute;width:100%;height:100%;background-color:#cccccc;filter:alpha(opacity=50);" onclick="cFclose();"></div>
</div>
<!-- ���� ����� end -->

<!-- Ķ�������� start -->
<div id="calendarListDiv" style="width:100;height:100;position:absolute;display:none;width:100%;height:100%;z-index:50;">
<table width="500" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff" style="z-index:102;position:absolute;border:3px solid #cccccc;">
  <tr bgcolor="#eeeeee">
    <td height="30" style="padding-left:10;"><b><li>Ķ���� ����</li></b></td>
  </tr>
  <tr><form name="cateForm">
    <input type="hidden" name="seq" value="">
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="5" style="margin-top:10;">
        <tr>
          <td width="80" align="center">Ķ������</td>
          <td>
            <input type="text" name="cname" maxlength="10" style="ime-mode:active;width:100%;border:1px solid #cccccc;background-color:#6e9cf2;color:#ffffff;" onclick="">
          </td>
          <td width="100" style="padding-right:20;" align="left">
            <!-- ���� ���� DIV start -->
            <div id="colorDiv" style="position:absolute;display:none;margin-top:20;">
            <table width="100%" border="0" cellspacing="3" cellpadding="0" bgcolor="#ffffff" style="cursor:hand;border:1px solid #666666;">
              <tr height="20" align="center">
                <td bgcolor="#6e9cf2" width="20%" onclick="changecolor(this.cellIndex,this.bgColor);" style="border:1px solid #000000;font-family:tahoma;font-size:8;font-weight:bold;color:#ffffff;">��</td>
                <td bgcolor="#e6a11b" width="20%" onclick="changecolor(this.cellIndex,this.bgColor);" style="font-family:tahoma;font-size:8;font-weight:bold;color:#ffffff;"></td>
                <td bgcolor="#93cc4b" width="20%" onclick="changecolor(this.cellIndex,this.bgColor);" style="font-family:tahoma;font-size:8;font-weight:bold;color:#ffffff;"></td>
                <td bgcolor="#f3672a" width="20%" onclick="changecolor(this.cellIndex,this.bgColor);" style="font-family:tahoma;font-size:8;font-weight:bold;color:#ffffff;"></td>
                <td bgcolor="#a28ab5" width="20%" onclick="changecolor(this.cellIndex,this.bgColor);" style="font-family:tahoma;font-size:8;font-weight:bold;color:#ffffff;"></td>
              </tr>
            </table>
            </div>
            <!-- ���� ���� DIV end -->
            <input type="button" value="���󺯰�" style="width:100%;height:19;border:1px solid #aaaaaa;background-color:#eeeeee;" onclick="document.getElementById('colorDiv').style.display='block';">
            <input type="hidden" name="ccolor" value="#6e9cf2">
          </td>
        </tr>
        <tr>
          <td align="center">����</td>
          <td colspan="2" style="padding-right:20;">
            <input type="text" name="ctext" maxlength="50" style="ime-mode:active;width:100%;border:1px solid #cccccc;">
          </td>
        </tr>
        <tr>
          <td colspan="3" align="center" style="padding-bottom:15;">
            <input type="button" value="�����ϱ�" style="width:100;height:25;border:1px solid #aaaaaa;background-color:#cccccc;" onclick="cateSubmit();">
            <input type="button" value="�ݱ�" style="width:100;height:25;border:1px solid #cccccc;background-color:#eeeeee;" onclick="cCclose();">
          </td>
        </tr>
      </table>
    </td>
  </tr></form>
  <tr bgcolor="#eeeeee">
    <td height="30" style="padding-left:10;"><b><li>Ķ�������</li></b></td>
  </tr>
  <tr>
    <td style="padding:10;">
      <table id="cateListTable" width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="#aaaaaa">
        <tr height="20" align="center" bgcolor="#dddddd">
          <td><b>Ķ����</b></td>
          <td width="80"><b>����</b></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<div style="z-index:101;position:absolute;width:100%;height:100%;background-color:#000000;filter:alpha(opacity=30);"></div>
</div>
<!-- Ķ�������� end -->

<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
  <tr height="50" bgcolor="#666666">
    <td style="color:#eeff9d;font-size:16;font-weight:bold;padding-left:10;">
      <!-- ��� ���� start -->
      ���̹� Ķ���� ����� - wiki's web programming
      <!-- ��� ���� end -->
    </td>
  </tr>
  <tr align="center">
    <td>
      <table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
        <tr align="center">
          <td width="190" valign="top" id="leftmenutd" style="display:block;">
            <!-- �޴� ���� start -->
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr align="center" height="50">
                <td>
                  <!-- �޴����� ��ư start -->
                  <input type="button" value="��������" style="width:87;height:30;background-color:#dddddd;border:1px solid #666666;" onfocus="this.blur();">
                  <input type="button" value="����ϰ���" style="width:87;height:30;background-color:#dddddd;border:1px solid #666666;" onfocus="this.blur();">
                  <!-- �޴����� ��ư end -->
                </td>
              </tr>
              <tr align="center">
                <td style="padding:7;">
                  <!-- �޴����� �޷� start -->
                  <div id="leftdiary"></div>
                  <!-- �޴����� �޷� end -->

                  <!-- �޴����� Ķ���͸�� start -->
                  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top:20;">
                    <tr height="30">
                      <td width="50%"><b>Ķ�������</b></td>
                      <td align="right"><a href="javascript:;" style="color:#000000;" onclick="document.getElementById('calendarListDiv').style.display='block';cCcontrol();">+����</a></td>
                    </tr>
                  </table>
                  <table id="leftcateListTable" width="100%" border="0" cellspacing="0" cellpadding="0">
                  </table>
                  <!-- �޴����� Ķ���͸�� end -->
                </td>
              </tr>
            </table>
            <!-- �޴� ���� end -->
          </td>
          <td width="10" bgcolor="#eeeeee" style="font-family:tahoma;font-size:18;border-left:1px solid #cccccc;border-right:1px solid #cccccc;cursor:hand;"
            id="leftmenubartd" onclick="HideLeftMenu();" onmouseover="this.style.backgroundColor='#dddddd';" onmouseout="this.style.backgroundColor='';">��</td>
          <td valign="top">
            <!-- �޷� ���� start -->
            <table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
              <tr align="center" valign="bottom" height="40">
                <td>
                  <!-- �޷� ���� �޴� start -->
                  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #cccccc;">
                    <tr align="center" height="30">
                      <td width="60" style="border-top:1px solid #cccccc;border-right:1px solid #cccccc;">�ϰ�</td>
                      <td width="60" style="border-top:1px solid #cccccc;border-right:1px solid #cccccc;">�ְ�</td>
                      <td width="60" style="border-top:1px solid #cccccc;border-right:1px solid #cccccc;font-weight:bold;" bgcolor="#eeeeee">����</td>
                      <td style="font-family:tahoma;font-size:18;font-weight:bold;">
                        <span style="color:#aaaaaa;cursor:hand;" onclick="moveMonth('-');">��</span>
                        <span id="mainym"><?=date("Y")?>.<?=date("m")?></span>
                        <span style="color:#aaaaaa;cursor:hand;" onclick="moveMonth('+');">��</span>
                      </td>
                      <td width="180" align="left">
                        <input type="text" style="width:120;height:20;border:1px solid #aaaaaa;">
                        <input type="button" value="�˻�" onclick="" style="width:50;background-color:#dddddd;border:1px solid #666666;" onfocus="this.blur();">
                      </td>
                    </tr>
                  </table>
                  <!-- �޷� ���� �޴� end -->
                </td>
              </tr>
              <tr>
                <td valign="top">
                  <!-- �޷� ���� �޷� start -->
                  <div id="maindiary"></div>
                  <!-- �޷� ���� �޷� end -->
                </td>
              </tr>
              </tr>
            </table>
            <!-- �޷� ���� end -->
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr height="20" align="center" bgcolor="#dedede">
    <td style="border-top:1px solid #cccccc;">
      <!-- ǲ�� ���� start -->
      <b>copyright wiki. (http://wiki.kr/)</b>
      <!-- ǲ�� ���� end -->
    </td>
  </tr>
</table>

<iframe name="ProcessFrame" width="100%" height="200"></iframe>
<div id="delDiv" style="display:none;"></div>

<script>
  /////���� �޴� �����/���̱�
  function HideLeftMenu() {
    if(document.getElementById("leftmenutd").style.display=="none") {
      document.getElementById("leftmenutd").style.display = "block";
      document.getElementById("leftmenubartd").innerText = "��";
    }
    else {
      document.getElementById("leftmenutd").style.display = "none";
      document.getElementById("leftmenubartd").innerText = "��";
    }
    setTimeout("diarybarInsert();",10);
  }

  /////���� �޴� �޷� �����
  var lsy = "<?=date("Y")?>";
  var lsm = "<?=date("m")?>";
  function makeLeftDiary() {
    var strsm = lsm+"";
    if(strsm.length==1) { strsm = "0"+strsm; }
    var nfirstdate = new Date(lsy,(lsm-1),1);
    var nfirstweek = nfirstdate.getDay();
    var nlastdate = new Date(lsy,lsm,0);
    var nlastday = nlastdate.getDate();
    var dtmsg = "<tr height='40'><td colspan='2' style='font-family:tahoma;font-size:24;color:#aaaaaa;cursor:hand;' align='right' onclick=\"moveLeftMonth('-')\">��</td>";
    dtmsg += "<td colspan='3' align='center'><div style='font-family:tahoma;font-size:10;font-weight:bold;'>"+lsy+"</div><div style='font-family:tahoma;font-size:24;font-weight:bold;'>"+lsm+"</div></td>";
    dtmsg += "<td colspan='2' style='font-family:tahoma;font-size:24;color:#aaaaaa;cursor:hand;' onclick=\"moveLeftMonth('+')\">��</td></tr><tr bgcolor='#cccccc'><td colspan='7' height='1'></td></tr>";
    dtmsg += "<tr align='center' height='21'><td width='14%' style='color:#ff0000;'>��</td><td width='14%'>��</td><td width='14%'>ȭ</td><td width='14%'>��</td><td width='14%'>��</td><td width='14%'>��</td><td width='14%' style='color:#0000ff;'>��</td></tr>";
    dtmsg += "<tr bgcolor='#cccccc'><td colspan='7' height='1'></td></tr>";
    var d = 0;
    var ntdsum = nlastday+nfirstweek;
    var dmsg = "";
    for(i=0; i<ntdsum; i++) {
      if(i<nfirstweek) {
        if(i==0) dmsg += "<td class='srgfont' style='cursor:hand;'></td>";
        else dmsg += "<td class='sgfont' style='cursor:hand;'></td>";
      }
      else {
        d++;
        var tdfc = "sfont";
        if(((i+1)%7)==1) { tdfc = "srfont"; }
        dmsg += "<td class='"+tdfc+"' style='cursor:hand;background-color:#eeeeee;'>"+d+"</td>";
      }
      if(i<ntdsum-1 && ((i+1)%7)==0) { dmsg += "</tr><tr align='center' height='17' bgcolor='#eeeeee'>"; }
    }
    i = 0;
    if(7-(ntdsum%7)>0 && (ntdsum%7)>0) {
      for(i=0; i<(7-(ntdsum%7)); i++) {
        tdfc = "sgfont";
        if(i==0 && (ntdsum%7)==0) { tdfc = "srgfont"; }
        dmsg += "<td class='"+tdfc+"' style='cursor:hand;'>"+(i+1)+"</td>";
      }
    }
    dmsg += "<tr align='center' height='17'><td class='srgfont' style='cursor:hand;'>"+(i+1)+"</td>";
    for(j=1; j<7; j++) { dmsg += "<td class='sgfont' style='cursor:hand;'>"+(i+j+1)+"</td>"; }
    document.getElementById("leftdiary").innerHTML = "<table width='100%' border='0' cellspacing='0' cellpadding='0'>"+dtmsg+"<tr align='center' height='17' bgcolor='#eeeeee'>"+dmsg+"</tr><tr bgcolor='#cccccc'><td colspan='7' height='1'></td></tr></table>";
    var ltm = lsm-1;
    var lty = lsy;
    if(ltm<1) { lty = lsy-1; ltm = 12; }
    var tlastdate = new Date(lty,ltm,0);
    var tlastday = tlastdate.getDate();
    var btcnt = -1;
    for(i=0; i<7; i++) {
      if(!document.getElementById("leftdiary").childNodes[0].rows[4].cells[i].innerText) btcnt++;
    }
    var tfirstday = tlastday - btcnt;
    for(i=0; i<=btcnt; i++) {
      document.getElementById("leftdiary").childNodes[0].rows[4].cells[i].innerText = tfirstday+i;
    }
    if(lsy=="<?=date("Y")?>" && lsm=="<?=date("m")?>") {
      for(i=4; i<document.getElementById("leftdiary").childNodes[0].rows.length-1; i++) {
        for(j=0; j<7; j++) {
          if(document.getElementById("leftdiary").childNodes[0].rows[i].cells[j].style.backgroundColor=="#eeeeee") {
            if(document.getElementById("leftdiary").childNodes[0].rows[i].cells[j].innerText=="<?=date("d")*1?>") {
              document.getElementById("leftdiary").childNodes[0].rows[i].cells[j].style.backgroundColor = "#6e9cf2";
              document.getElementById("leftdiary").childNodes[0].rows[i].cells[j].style.color = "#ffffff";
            }
            else {
              document.getElementById("leftdiary").childNodes[0].rows[i].cells[j].style.backgroundColor = "#eeeeee";
              document.getElementById("leftdiary").childNodes[0].rows[i].cells[j].style.color = "";
            }
          }
        }
      }
    }
  }
  makeLeftDiary();

  /////���� �޴� �޷� �� �̵�
  function moveLeftMonth(v) {
    if(v=="+") {
      lsm++;
      if(lsm>12) { lsy++; lsm = 1; }
    }
    else {
      lsm--;
      if(lsm<1) { lsy--; lsm = 12; }
    }
    if(lsm<10) { lsm = "0"+lsm; }
    document.getElementById("leftdiary").childNodes[0].rows[0].cells[1].innerHTML = "<div style='font-family:tahoma;font-size:10;font-weight:bold;'>"+lsy+"</div><div style='font-family:tahoma;font-size:24;font-weight:bold;'>"+lsm+"</div>";
    makeLeftDiary();
  }

  /////���� �޷� �����
  var ny = "<?=date("Y")?>";
  var nm = "<?=date("m")?>";
  var mtdcnt = 0;
  function makeDiary() {
    mtdcnt = 0;
    var strsm = nm+"";
    if(strsm.length==1) { strsm = "0"+strsm; }
    var nfirstdate = new Date(ny,(nm-1),1);
    var nfirstweek = nfirstdate.getDay();
    var nlastdate = new Date(ny,nm,0);
    var nlastday = nlastdate.getDate();
    var dtmsg = "<tr align='center' height='25'><td width='14%' style='color:#ff0000;border-right:1px solid #cccccc;border-bottom:1px solid #666666;'><b>��</b></td>";
    dtmsg += "<td width='14%' style='border-right:1px solid #cccccc;border-bottom:1px solid #666666;'><b>��</b></td><td width='14%' style='border-right:1px solid #cccccc;border-bottom:1px solid #666666;'><b>ȭ</b></td>";
    dtmsg += "<td width='14%' style='border-right:1px solid #cccccc;border-bottom:1px solid #666666;'><b>��</b></td><td width='14%' style='border-right:1px solid #cccccc;border-bottom:1px solid #666666;'><b>��</b></td>";
    dtmsg += "<td width='14%' style='border-right:1px solid #cccccc;border-bottom:1px solid #666666;'><b>��</b></td><td width='14%' style='border-bottom:1px solid #666666;'><b>��</b></td></tr>";
    var d = 0;
    var ntdsum = nlastday+nfirstweek;
    var dmsg = "";
    for(i=0; i<ntdsum; i++) {
      if(i<nfirstweek) {
        mtdcnt++;
        dmsg += "<td id='dtd"+mtdcnt+"' value='' valign='top' onmousedown=\"cFdrag('s',this.id.replace(/dtd/,''));\" onmouseup=\"cFdrag('e',this.id.replace(/dtd/,''));\" onmouseover=\"cFdrag('o',this.id.replace(/dtd/,''));\"";
        dmsg += " style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;'>";
        if(i==0) dmsg += "<span style='padding-left:3;color:#ff8989;'></span><br><div id='daycnt"+mtdcnt+"'></div></td>";
        else dmsg += "<span style='padding-left:3;color:#bbbbbb;'></span><div id='daycnt"+mtdcnt+"'></div></td>";
      }
      else {
        d++;
        var tdfc = "";
        if(((i+1)%7)==1) { tdfc = "color:#ff0000;"; }
        mtdcnt++;
        var nday = d+"";
        if(nday.length<2) { nday = "0"+d; }
        dmsg += "<td id='dtd"+mtdcnt+"' value='"+ny+"-"+nm+"-"+nday+"' valign='top' onmousedown=\"cFdrag('s',this.id.replace(/dtd/,''));\" onmouseup=\"cFdrag('e',this.id.replace(/dtd/,''));\" onmouseover=\"cFdrag('o',this.id.replace(/dtd/,''));\"";
        dmsg += " style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;"+tdfc+"'>"+d+"</span><div id='daycnt"+mtdcnt+"'></div></td>";
      }
      if(i<ntdsum-1 && ((i+1)%7)==0) { dmsg += "</tr><tr>"; }
    }
    i = 0;
    var nexty = ny;
    var nextm = (nm*1) + 1;
    if(nextm>12) { nextm=1; nexty++; }
    if((nextm+"").length==1) nextm = "0"+nextm;
    if(7-(ntdsum%7)>0 && (ntdsum%7)>0) {
      for(i=0; i<(7-(ntdsum%7)); i++) {
        tdfc = "color:#bbbbbb;";
        if(i==0 && (ntdsum%7)==0) { tdfc = "color:#ff8989;"; }
        mtdcnt++;
        var nextmnday = (i+1)+"";
        if(nextmnday.length<2) { nextmnday = "0"+(i+1); }
        dmsg += "<td id='dtd"+mtdcnt+"' value='"+nexty+"-"+nextm+"-"+nextmnday+"' valign='top' onmousedown=\"cFdrag('s',this.id.replace(/dtd/,''));\" onmouseup=\"cFdrag('e',this.id.replace(/dtd/,''));\" onmouseover=\"cFdrag('o',this.id.replace(/dtd/,''));\"";
        dmsg += " style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;'><span style='padding-left:3;"+tdfc+"'>"+(i+1)+"</span><div id='daycnt"+mtdcnt+"'></div></td>";
      }
    }
    document.getElementById("maindiary").innerHTML = "<table width='100%' height='100%' border='0' cellspacing='0' cellpadding='0' onselectstart='return false'>"+dtmsg+"<tr>"+dmsg+"</tr></table>";
    var ltm = nm-1;
    var lty = ny;
    if(ltm<1) { lty = ny-1; ltm = 12; }
    if((ltm+"").length<2) ltm = "0"+ltm;
    var tlastdate = new Date(lty,ltm,0);
    var tlastday = tlastdate.getDate();
    var btcnt = -1;
    for(i=0; i<7; i++) {
      if(!document.getElementById("maindiary").childNodes[0].rows[1].cells[i].childNodes[0].innerText) btcnt++;
    }
    var tfirstday = tlastday - btcnt;
    for(i=0; i<=btcnt; i++) {
      document.getElementById("maindiary").childNodes[0].rows[1].cells[i].childNodes[0].innerText = tfirstday+i;
      document.getElementById("maindiary").childNodes[0].rows[1].cells[i].value = lty+"-"+ltm+"-"+(tfirstday+i);
    }
    for(i=1; i<document.getElementById("maindiary").childNodes[0].rows.length; i++) {
      if(document.getElementById("maindiary").childNodes[0].rows[i].cells[6]) {
        document.getElementById("maindiary").childNodes[0].rows[i].cells[6].style.borderRight = "0";
      }
    }
    if(ny=="<?=date("Y")?>" && nm=="<?=date("m")?>") {
      for(i=1; i<document.getElementById("maindiary").childNodes[0].rows.length; i++) {
        for(j=0; j<7; j++) {
          if(document.getElementById("maindiary").childNodes[0].rows[i].cells[j].style.color=="#474747") {
            if(document.getElementById("maindiary").childNodes[0].rows[i].cells[j].innerText=="<?=date("d")*1?>") {
              document.getElementById("maindiary").childNodes[0].rows[i].cells[j].style.backgroundColor = "#fffdc8";
            }
            else {
              document.getElementById("maindiary").childNodes[0].rows[i].cells[j].style.backgroundColor = "";
            }
          }
        }
      }
    }
  }
  makeDiary();

  /////���� �޷� �� �̵�
  function moveMonth(v) {
    if(v=="+") {
      nm++;
      if(nm>12) { ny++; nm = 1; }
    }
    else {
      nm--;
      if(nm<1) { ny--; nm = 12; }
    }
    if(nm<10) { nm = "0"+nm; }
    lsy = ny;
    lsm = nm;
    document.getElementById("mainym").innerHTML = ny+"."+nm;
    makeDiary();
    makeLeftDiary();
    setTimeout("dataLoad();",100);
  }

  /////������ �ε��� width�� �� ������ �ִ� ����
  document.body.style.width = "100%";

  /////��������� �߾� ����
  function cFcontrol() {
    document.getElementById('calendarFormDiv').childNodes[0].style.left = (parseInt(document.body.offsetWidth)-parseInt(document.getElementById('calendarFormDiv').childNodes[0].offsetWidth))/2;
    document.getElementById('calendarFormDiv').childNodes[0].style.top = (parseInt(document.body.offsetHeight)-parseInt(document.getElementById('calendarFormDiv').childNodes[0].offsetHeight))/2;
  }
  cFcontrol();

  /////Ķ��������â �߾� ����
  function cCcontrol() {
    document.getElementById('calendarListDiv').childNodes[0].style.left = (parseInt(document.body.offsetWidth)-parseInt(document.getElementById('calendarListDiv').childNodes[0].offsetWidth))/2;
    document.getElementById('calendarListDiv').childNodes[0].style.top = (parseInt(document.body.offsetHeight)-parseInt(document.getElementById('calendarListDiv').childNodes[0].offsetHeight))/2;
  }
  cCcontrol();

  /////â�� ũ�⺯��� ��������� ������
  document.body.onresize = function() {
    setTimeout("cFcontrol();",100);
    setTimeout("cCcontrol();",100);
    setTimeout("diarybarInsert();",10);
  }

  /////���� �޷¿��� onmousedown,onmouseup,onmouseover�̺�Ʈ�� �Ͼ �� ���� �����ϰ��ϰų� ������� ���� ������
  var dragmd = "";
  var dragsv = "";
  var dragev = "";
  function cFdrag(m,v) {
    if(m=="s") {
      dragmd = m;
      dragsv = v;
      dragev = "";
    }
    if(dragmd=="s") {
      var zsv = dragsv;
      var zev = v;
      if(parseInt(zsv)>parseInt(v)) {
        var ts = dragsv;
        zsv = v;
        zev = ts;
      }
      for(i=1; i<=mtdcnt; i++) {
        if(i>=parseInt(zsv) && i<=parseInt(zev)) {
          if(document.getElementById("dtd"+i).value=="<?=date("Y-m-d")?>") { document.getElementById("dtd"+i).style.backgroundColor = "#ebe9b8"; }
          else { document.getElementById("dtd"+i).style.backgroundColor = "#eeeeee"; }
        } else {
          if(document.getElementById("dtd"+i).value=="<?=date("Y-m-d")?>") { document.getElementById("dtd"+i).style.backgroundColor = "#fffdc8"; }
          else { document.getElementById("dtd"+i).style.backgroundColor = ""; }
        }
      }
    }
    if(m=="e") {
      document.getElementById('calendarFormDiv').style.display = "block";
      cFcontrol();
      calendarForm.sdate.value = document.getElementById("dtd"+zsv).value;
      calendarForm.edate.value = document.getElementById("dtd"+zev).value;
      calendarForm.cate.value = document.getElementById("calendarcateDiv").childNodes[0].rows[0].cells[0].id.replace("cateseq","");
      dragmd = "";
      dragsv = "";
      dragev = "";
    }
  }

  /////��������� ���̾� �����
  function cFclose() {
    document.getElementById('calendarFormDiv').style.display = "none";
    for(i=1; i<=mtdcnt; i++) {
      if(document.getElementById("dtd"+i).value=="<?=date("Y-m-d")?>") document.getElementById("dtd"+i).style.backgroundColor = "#fffdc8";
      else document.getElementById("dtd"+i).style.backgroundColor = "";
    }
  }

  /////������� �޷� �����ֱ�
  var fny = ny;
  var fnm = nm;
  function FormDiaryView(f) {
    var dleft = eval("calendarForm."+f).parentNode.parentNode.cells[0].offsetWidth + eval("calendarForm."+f).offsetLeft + 2;
    document.getElementById("calendardiaryDiv").style.left = dleft;
    if(eval("calendarForm."+f).value) {
      fny = eval("calendarForm."+f).value.substr(0,4);
      fnm = eval("calendarForm."+f).value.substr(5,2);
    }
    else {
      fny = ny;
      fnm = nm;
    }
    MakeFormDiary(f);
    document.getElementById("calendardiaryDiv").style.display = "block";
  }

  /////������� �޷� �����
  function MakeFormDiary(f) {
    var ddiv = document.getElementById("calendardiaryDiv");
    var strsm = fnm+"";
    if(strsm.length==1) { strsm = "0"+strsm; }
    var nfirstdate = new Date(fny,(fnm-1),1);
    var nfirstweek = nfirstdate.getDay();
    var nlastdate = new Date(fny,fnm,0);
    var nlastday = nlastdate.getDate();
    var cdtmsg = "<tr bgcolor='#dddddd' height='25'><td colspan='1' style='font-family:tahoma;color:#aaaaaa;cursor:hand;' align='right' onclick=\"moveFormMonth('"+f+"','-')\">��</td>";
    cdtmsg += "<td colspan='5' align='center'><div id='formym' style='font-family:tahoma;font-weight:bold;'>"+fny+"�� "+fnm+"��</div></td>";
    cdtmsg += "<td colspan='1' style='font-family:tahoma;color:#aaaaaa;cursor:hand;' onclick=\"moveFormMonth('"+f+"','+')\">��</td></tr>";
    var d = 0;
    var ntdsum = nlastday+nfirstweek;
    var cdmsg = "";
    for(i=0; i<ntdsum; i++) {
      if(i<nfirstweek) { cdmsg += "<td></td>"; }
      else {
        d++;
        var tdfc = "sfont";
        if(((i+1)%7)==1) { tdfc = "srfont"; }
        var fday = d+"";
        if(fday.length<2) { fday = "0"+d; }
        cdmsg += "<td class='"+tdfc+"' onclick=\"selectFormDate('"+f+"','"+fny+"-"+fnm+"-"+fday+"')\" style='cursor:hand;background-color:#eeeeee;'>"+d+"</td>";
      }
      if(i<ntdsum-1 && ((i+1)%7)==0) { cdmsg += "</tr><tr align='center' height='17' bgcolor='#eeeeee'>"; }
    }
    if(7-(ntdsum%7)>0 && (ntdsum%7)>0) {
      for(i=0; i<(7-(ntdsum%7)); i++) { cdmsg += "<td></td>"; }
    }
    ddiv.innerHTML = "<table width='150' border='0' cellspacing='0' cellpadding='0' style='border:2px solid #aaaaaa;'>"+cdtmsg+"<tr align='center' height='17' bgcolor='#eeeeee'>"+cdmsg+"</tr><tr bgcolor='#cccccc'><td colspan='7' height='1'></td></tr><tr bgcolor='#cccccc'><td colspan='7' align='center' style='font-size:11;font-family:tahoma;cursor:hand;' bgcolor='#dddddd' height='18' onclick=\"selectFormDate('"+f+"','<?=date("Y-m-d");?>');\">���� : <?=date("Y-m-d");?></td></tr></table>";
  }

  /////������� �޷� �� �̵�
  function moveFormMonth(f,v) {
    if(v=="+") {
      fnm++;
      if(fnm>12) { fny++; fnm = 1; }
    }
    else {
      fnm--;
      if(fnm<1) { fny--; fnm = 12; }
    }
    if(fnm<10) { fnm = "0"+fnm; }
    document.getElementById("formym").innerHTML = fny+"�� "+fnm+"��";
    MakeFormDiary(f);
  }

  /////������� ���� ����
  function selectFormDate(f,v) {
    eval("calendarForm."+f).value = v;
    if(f=="sdate" && calendarForm.edate.value) {
      if(parseInt(v.replace(/-/gi,""))>parseInt(calendarForm.edate.value.replace(/-/gi,""))) { calendarForm.edate.value = v; }
    }
    if(f=="edate" && calendarForm.sdate.value) {
      if(parseInt(v.replace(/-/gi,""))<parseInt(calendarForm.sdate.value.replace(/-/gi,""))) { calendarForm.sdate.value = v; }
    }
    document.getElementById("calendardiaryDiv").style.display = "none";
  }

  /////������� ī�װ��� ���̱�/�����
  function cateView() {
    if(document.getElementById("calendarcateDiv").style.display=="none") {
      document.getElementById("calendarcateDiv").style.display = "block";
    }
    else {
      document.getElementById("calendarcateDiv").style.display = "none";
    }
  }

  /////������� ī�װ�������
  function cateChange(v,c,s) {
    calendarForm.cate.value = s;
    document.getElementById("cateTr").cells[0].innerText = v;
    document.getElementById("cateTr").bgColor = c;
    cateView();
  }

  /////Ķ��������â ���̾� �����
  function cCclose() {
    cateForm.reset();
    changecolor('0','#6e9cf2');
    document.getElementById('calendarListDiv').style.display = "none";
  }

  /////Ķ�������� ���� ����
  function changecolor(n,c) {
    var cTbl = document.getElementById("colorDiv").childNodes[0];
    for(i=0; i<cTbl.rows[0].cells.length; i++) {
      cTbl.rows[0].cells[i].style.border = "0px";
      cTbl.rows[0].cells[i].innerText = "";
    }
    cTbl.rows[0].cells[n].style.border = "1px solid #000000";
    cTbl.rows[0].cells[n].innerText = "��";
    cateForm.cname.style.backgroundColor = c;
    cateForm.ccolor.value = c;
    document.getElementById("colorDiv").style.display = "none";
  }

  /////Ķ���� ����
  function cateSubmit() {
    if(!cateForm.cname.value.replace(/ /gi,"")) {
      alert("Ķ�������� �Է��ϼ���.");
      cateForm.cname.focus();
      return false;
    }
    cateForm.method = "post";
    cateForm.action = "category.php";
    cateForm.target = "ProcessFrame";
    cateForm.submit();
  }

  /////Ķ���� ���� ���� Ķ���� ��Ͽ� �߰�
  function cateList(s,n,t,c) { 
    var tbl = document.getElementById("cateListTable");
    var tr = tbl.insertRow();
    tr.height = "20";
    tr.bgColor = "#ffffff";
    var td0 = tr.insertCell();
    td0.style.padding = "5";
    td0.innerHTML = "<div style='width:100%;background-color:"+c+";height:18;padding:1;padding-left:5;font-weight:bold;'>"+n+"</div>"+t;
    var td1 = tr.insertCell();
    td1.align = "center";
    td1.innerHTML = "<a href='javascript:;' style='color:#000000;' onclick=\"cateModify('"+s+"','"+n+"','"+t+"','"+c+"');\">����</a> | <a href='javascript:;' style='color:#000000;' onclick=\"cateDelete('"+s+"','"+n+"');\">����</a>";
  }

  /////���� �޴��� Ķ���� ��Ͽ� �߰�
  function leftcateList(n,c) {
    var ltbl = document.getElementById("leftcateListTable");
    var ztr = ltbl.insertRow();
    var ztd = ztr.insertCell();
    ztd.height = "3";
    ztd.colspan = "3";
    var ltr = ltbl.insertRow();
    var ltd0 = ltr.insertCell();
    ltd0.style.width = "1%";
    ltd0.innerHTML = "<input type='checkbox' checked>";
    var ltd1 = ltr.insertCell();
    ltd1.style.width = "98%";
    ltd1.bgColor = c;
    ltd1.style.paddingLeft = "5";
    ltd1.style.color = "#ffffff";
    ltd1.innerText = n;
    var ltd2 = ltr.insertCell();
    ltd2.style.width = "1%";
    ltd2.bgColor = c;
    ltd2.style.paddingRight = "5";
    ltd2.style.color = "#ffffff";
    ltd2.style.fontFamily = "tahoma";
    ltd2.style.fontSize = "8";
    ltd2.innerText = "��";
  }

  /////����������� ī�װ��� ��Ͽ� �߰�
  function diarycateList(s,n,c) {
    var dtbl = document.getElementById("calendarcateDiv").childNodes[0];
    var dtr = dtbl.insertRow();
    dtr.style.height = "20";
    dtr.bgColor = c;
    dtr.onclick = function() { cateChange(this.cells[0].innerText,this.bgColor,this.cells[0].id.replace("cateseq","")); }
    dtr.onmouseover = function() { this.cells[1].innerText="��"; }
    dtr.onmouseout = function() { this.cells[1].innerText=""; }
    var dtd0 = dtr.insertCell();
    dtd0.style.paddingLeft = "5";
    dtd0.style.color = "#ffffff";
    dtd0.id = "cateseq"+s;
    dtd0.innerText = n;
    var dtd1 = dtr.insertCell();
    dtd1.style.width = "1%";
    dtd1.style.paddingRight = "5";
    dtd1.style.color = "#ffffff";
    dtd1.style.fontFamily = "tahoma";
    dtd1.style.fontSize = "8";
    dtd1.style.fontWeight = "bold";
  }

  /////ī�װ��� �����ϱ�
  function cateModify(s,n,t,c) {
    cateForm.seq.value = s;
    cateForm.cname.value = n;
    cateForm.cname.style.backgroundColor = c;
    cateForm.ctext.value = t;
    cateForm.ccolor.value = c;
  }

  /////ī�װ��� �����ϱ�
  function cateDelete(s,n) {
    if(confirm("���� �����Ͻðڽ��ϱ�?")) {
      document.getElementById("delDiv").innerHTML = "<form name='deleteForm'><input type='hidden' name='seq' value='"+s+"'><input type='hidden' name='cname' value='"+n+"'><input type='hidden' name='mde' value='del'></form>";
      deleteForm.method = "post";
      deleteForm.action = "category.php";
      deleteForm.target = "ProcessFrame";
      deleteForm.submit();
      document.getElementById("delDiv").innerHTML = "";
    }
  }

  /////���� ����
  function diarySubmit() {
    if(!calendarForm.title.value.replace(/ /gi,"")) {
      alert("���������� �Է��ϼ���.");
      calendarForm.title.focus();
      return false;
    }
    calendarForm.sdt.value = document.getElementById("maindiary").childNodes[0].rows[1].cells[0].value;
    calendarForm.edt.value = document.getElementById("maindiary").childNodes[0].rows[document.getElementById("maindiary").childNodes[0].rows.length-1].cells[6].value;
    calendarForm.method = "post";
    calendarForm.action = "diary.php";
    calendarForm.target = "ProcessFrame";
    calendarForm.submit();
  }

  diarylist = new Array();

  /////���� ǥ�� �ϱ�
  function diarybarInsert() {
    var maxdaycnt = parseInt(document.getElementById("maindiary").childNodes[0].rows[document.getElementById("maindiary").childNodes[0].rows.length-1].cells[6].id.replace("dtd",""));
    var daybarcnt = new Array();
    for(i=1; i<=maxdaycnt; i++) {
      daybarcnt[i] = 0;
      document.getElementById("daycnt"+i).innerHTML = "";
    }
    for(d=0; d<diarylist.length; d++) {
      var dlary = diarylist[d].split("|");
      var scn = 0;
      for(i=1; i<=maxdaycnt; i++) {
        if(document.getElementById("dtd"+i).value==dlary[2]) { var stacell = document.getElementById("dtd"+i).id.replace("dtd",""); }
        if(document.getElementById("dtd"+i).value==dlary[3]) { var endcell = document.getElementById("dtd"+i).id.replace("dtd",""); }
      }
      for(i=parseInt(stacell); i<=parseInt(endcell); i++) {
        if(i==parseInt(stacell)) {
          var maxdaybarcnt = 0;
          for(j=i; j<=i+(7-(i%7)); j++) {
            if(j<=parseInt(endcell)) {
              if(daybarcnt[j]>maxdaybarcnt) { maxdaybarcnt = daybarcnt[j]; }
            }
          }
          var barmsg = "<div class='schedulebar' onclick=\"\" style='position:absolute;z-index:1;background-Color:"+dlary[1]+";color:#ffffff;margin-top:"+(maxdaybarcnt*19+3)+";";
          scn = i;
          var firstcellwidth = document.getElementById("dtd"+i).offsetLeft;
        }
        if(i>parseInt(stacell) && i<parseInt(endcell) && scn>0 && (i % 7)==1) {
          var cellwidth = document.getElementById("dtd7").offsetLeft + document.getElementById("dtd7").offsetWidth - firstcellwidth;
          firstcellwidth = 0;
          barmsg += "width:"+cellwidth+";' title='"+dlary[6]+"\n("+dlary[4]+"~"+dlary[5]+")'>"+dlary[6]+"</div>";
          document.getElementById("daycnt"+scn).innerHTML += barmsg;
          var maxdaybarcnt = 0;
          for(j=i; j<=i+(7-(i%7)); j++) {
            if(j<=parseInt(endcell)) {
              if(daybarcnt[j]>maxdaybarcnt) { maxdaybarcnt = daybarcnt[j]; }
            }
          }
          var barmsg = "<div class='schedulebar' onclick=\"\" style='position:absolute;z-index:1;background-Color:"+dlary[1]+";color:#ffffff;margin-top:"+(maxdaybarcnt*19+3)+";";
          scn = i;
        }
        if(i==parseInt(endcell)) {
          var cellwidth = document.getElementById("dtd"+i).offsetLeft + document.getElementById("dtd"+i).offsetWidth - firstcellwidth;
          barmsg += "width:"+cellwidth+";' title='"+dlary[6]+"\n("+dlary[4]+"~"+dlary[5]+")'>"+dlary[6]+"</div>";
          document.getElementById("daycnt"+scn).innerHTML += barmsg;
          firstcellwidth = 0;
          scn = 0;
        }
        daybarcnt[i]++;
      }
    }
  }

  /////index.php�� �ε��� �Ϸ�� �� ProcessFrame���� load.php�� �ε�
  function dataLoad() {
    var nowsdt = document.getElementById("maindiary").childNodes[0].rows[1].cells[0].value;
    var nowedt = document.getElementById("maindiary").childNodes[0].rows[document.getElementById("maindiary").childNodes[0].rows.length-1].cells[6].value;
    ProcessFrame.location.href = "load.php?sdt="+nowsdt+"&edt="+nowedt;
  }
  dataLoad();
</script>

</body>
</html>