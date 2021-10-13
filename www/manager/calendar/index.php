<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>네이버 캘린더 만들기 - wiki's web programming (ver 0.17.0)</title>
<style>
	td, div, input, textarea, select, iframe {
		font-size:12; font-family:나눔고딕; color:#474747;
	}
	.sfont { font-family:tahoma; font-size:10; }
	.sgfont { font-family:tahoma; font-size:10; color:#bbbbbb; }
	.srfont { font-family:tahoma; font-size:10; color:#ff0000; }
	.srgfont { font-family:tahoma; font-size:10; color:#ff8989; }
	.schedulebar { position:absolute;padding-left:0px;word-break:break-all;height:17;line-height:1.4;overflow:hidden; }

</style>
<meta http-equiv="Content-Type" content="text/html;charset=euc-kr">
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" style="overflow-y:auto;">

<!-- 일정 등록폼 start -->
<div id="calendarFormDiv" style="width:100;height:100;position:absolute;display:none;width:100%;height:100%;z-index:50;">
<form name="calendarForm">
<input type="hidden" name="update_seq_no" value="">
<table width="500" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff" style="z-index:102;position:absolute;border:3px solid #cccccc;">
	<tr bgcolor="#eeeeee">
		<td height="30" style="padding-left:10;"><b><li>일정등록</li></b></td>
	</tr>
	<tr>
		<td>
			<table width="100%" border="0" cellspacing="1" cellpadding="5">
				<tr>
					<td width="80" align="center">일시</td>
					<td>
						<input type="text" name="sdate" style="width:100;border:1px solid #cccccc;text-align:center;cursor:hand;" onclick="FormDiaryView(this.name);" readonly> ~
						<input type="text" name="edate" style="width:100;border:1px solid #cccccc;text-align:center;cursor:hand;" onclick="FormDiaryView(this.name);" readonly>
						<!-- 일자 선택 DIV start -->
						<div id="calendardiaryDiv" style="position:absolute;display:none;background-color:#cccccc;margin-top:22;"></div>
						<!-- 일자 선택 DIV start -->
					</td>
				</tr>
				<tr>
					<td align="center">내용</td>
					<td>
						<input type="text" name="title" style="width:350;border:1px solid #cccccc;">
					</td>
				</tr>
				<tr>
					<td align="center">카테고리</td>
					<td>
						<table width="150" height="20" border="0" cellspacing="0" cellpadding="0" style="cursor:hand;" onclick="cateView();">
							<tr id="cateTr" bgcolor="#6e9cf2">
								<td id="test" style="padding-left:5;color:#ffffff;">개인일정</td>
								<td id="test" width="1%" style="padding-right:5;color:#ffffff;font-family:tahoma;font-size:8;">▼</td>
							</tr>
						</table>
						<input type="hidden" name="cate"><input type="hidden" name="sdt"><input type="hidden" name="edt">
						<!-- 카테고리 선택 DIV start -->
						<div id="calendarcateDiv" style="position:absolute;display:none;margin-top:2;">
						<table id="calendarcateDiv_table" width="150" border="0" cellspacing="0" cellpadding="0" style="cursor:hand;">
							
						</table>
						</div>
						<!-- 카테고리 선택 DIV end -->
					</td>
				</tr>
				<tr><td colspan="2" height="5"></td></tr>
				<tr><td colspan="2" bgcolor="#cccccc" height="1"></td></tr>
				<tr><td colspan="2" height="5"></td></tr>
					<td align="center">메모</td>
					<td>
						<textarea name="memo" rows="7" style="width:350;border:1px solid #cccccc;overflow-y:auto;"></textarea>
					</td>
				</tr>
				<tr><td colspan="2" height="5"></td></tr>
				<tr><td colspan="2" bgcolor="#cccccc" height="1"></td></tr>
				<tr><td colspan="2" height="5"></td></tr>
				<tr>
					<td colspan="2" align="center">
						<input type="button" value="저장하기" style="width:100;height:25;border:1px solid #aaaaaa;background-color:#cccccc;" onclick="diarySubmit();">
						<input type="button" value="취소" style="width:100;height:25;border:1px solid #cccccc;background-color:#eeeeee;" onclick="cFclose();">
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>

<div style="z-index:101;position:absolute;width:100%;height:100%;background-color:#cccccc;filter:alpha(opacity=50);opacity:0.5;" onclick="cFclose();"></div>

</div>
<!-- 일정 등록폼 end -->

<!-- 캘린더관리 start -->
<div id="calendarListDiv" style="width:100;height:100;position:absolute;display:none;width:100%;height:100%;z-index:50;">
		<form name="cateForm">
<table width="500" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff" style="z-index:102;position:absolute;border:3px solid #cccccc;">
	<tr bgcolor="#eeeeee">
		<td height="30" style="padding-left:10;"><b><li>캘린더 관리</li></b></td>
	</tr>
	<tr>
		<input type="hidden" name="seq" value="">
		<td>
			<table width="100%" border="0" cellspacing="1" cellpadding="5" style="margin-top:10;">
				<tr>
					<td width="80" align="center">캘린더명</td>
					<td>
						<input type="text" name="cname" maxlength="10" style="ime-mode:active;width:100%;border:1px solid #cccccc;background-color:#6e9cf2;color:#ffffff;" onclick="">
					</td>
					<td width="100" style="padding-right:20;" align="left">
						<!-- 색상 선택 DIV start -->
						<div id="colorDiv" style="position:absolute;display:none;margin-top:20;width:100px">
						<table id="colorDiv_table" width="100%" border="0" cellspacing="3" cellpadding="0" bgcolor="#ffffff" style="cursor:hand;border:1px solid #666666;">
							<tr height="20" align="center">
								<td bgcolor="#6e9cf2" width="20%" onclick="changecolor(this.cellIndex,this.bgColor);" style="border:1px solid #000000;font-family:tahoma;font-size:8;font-weight:bold;color:#ffffff;">√</td>
								<td bgcolor="#e6a11b" width="20%" onclick="changecolor(this.cellIndex,this.bgColor);" style="font-family:tahoma;font-size:8;font-weight:bold;color:#ffffff;"></td>
								<td bgcolor="#93cc4b" width="20%" onclick="changecolor(this.cellIndex,this.bgColor);" style="font-family:tahoma;font-size:8;font-weight:bold;color:#ffffff;"></td>
								<td bgcolor="#f3672a" width="20%" onclick="changecolor(this.cellIndex,this.bgColor);" style="font-family:tahoma;font-size:8;font-weight:bold;color:#ffffff;"></td>
								<td bgcolor="#a28ab5" width="20%" onclick="changecolor(this.cellIndex,this.bgColor);" style="font-family:tahoma;font-size:8;font-weight:bold;color:#ffffff;"></td>
							</tr>
						</table>
						</div>
						<!-- 색상 선택 DIV end -->
						<input type="button" value="색상변경" style="width:100%;height:19;border:1px solid #aaaaaa;background-color:#eeeeee;" onclick="document.getElementById('colorDiv').style.display='block';">
						<input type="hidden" name="ccolor" value="#6e9cf2">
					</td>
				</tr>
				<tr>
					<td align="center">설명</td>
					<td colspan="2" style="padding-right:20;">
						<input type="text" name="ctext" maxlength="50" style="ime-mode:active;width:100%;border:1px solid #cccccc;">
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center" style="padding-bottom:15;">
						<input type="button" value="저장하기" style="width:100;height:25;border:1px solid #aaaaaa;background-color:#cccccc;" onclick="cateSubmit();">
						<input type="button" value="닫기" style="width:100;height:25;border:1px solid #cccccc;background-color:#eeeeee;" onclick="cCclose();">
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor="#eeeeee">
		<td height="30" style="padding-left:10;"><b><li>캘린더목록</li></b></td>
	</tr>
	<tr>
		<td style="padding:10;">
			<table id="cateListTable" width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="#aaaaaa">
				<tr height="20" align="center" bgcolor="#dddddd">
					<td><b>캘린더</b></td>
					<td width="80"><b>관리</b></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>
<div style="z-index:101;position:absolute;width:100%;height:100%;background-color:#000000;filter:alpha(opacity=50);opacity:0.5"></div>
</div>
<!-- 캘린더관리 end -->

<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
	<tr height="50" bgcolor="#666666">
		<td style="color:#eeff9d;font-size:16;font-weight:bold;padding-left:10;">
			<!-- 헤더 영역 start -->
			네이버 캘린더 만들기 - wiki's web programming
			<!-- 헤더 영역 end -->
		</td>
	</tr>
	<tr align="center">
		<td>
			<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
				<tr align="center">
					<td width="190" valign="top" id="leftmenutd" style="display:block;">
						<!-- 메뉴 영역 start -->
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr align="center" height="10">
								<td>
									<!-- 메뉴영역 버튼 start -->
									<!--
									<input type="button" value="일정쓰기" style="width:87;height:30;background-color:#dddddd;border:1px solid #666666;" onfocus="this.blur();">
									<input type="button" value="기념일관리" style="width:87;height:30;background-color:#dddddd;border:1px solid #666666;" onfocus="this.blur();">
									-->
									<!-- 메뉴영역 버튼 end -->
								</td>
							</tr>
							<tr align="center">
                <td style="padding:7;">
                  <!-- 메뉴영역 달력 start -->
                  <div id="leftdiary"></div>
                  <!-- 메뉴영역 달력 end -->

                  <!-- 메뉴영역 캘린터목록 start -->
                  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top:20;">
                    <tr height="30">
                      <td width="50%"><b>캘린더목록</b></td>
                      <td align="right"><a href="javascript:;" style="color:#000000;" onclick="document.getElementById('calendarListDiv').style.display='block';cCcontrol();">+관리</a></td>
                    </tr>
                  </table>
                  <table id="leftcateListTable" width="100%" border="0" cellspacing="0" cellpadding="0">
                  </table>
                  <!-- 메뉴영역 캘린터목록 end -->
                </td>
              </tr>
            </table>
            <!-- 메뉴 영역 end -->
          </td>
          <td width="10" bgcolor="#eeeeee" style="font-family:tahoma;font-size:18;border-left:1px solid #cccccc;border-right:1px solid #cccccc;cursor:hand;"
            id="leftmenubartd" onclick="HideLeftMenu();" onmouseover="this.style.backgroundColor='#dddddd';" onmouseout="this.style.backgroundColor='';">◀</td>
          <td valign="top">
            <!-- 달력 영역 start -->
            <table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
              <tr align="center" valign="bottom" height="40">
                <td>
                  <!-- 달력 영역 메뉴 start -->
                  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #cccccc;">
                    <tr align="center" height="30">
											<!--
                      <td width="60" style="border-top:1px solid #cccccc;border-right:1px solid #cccccc;">일간</td>
                      <td width="60" style="border-top:1px solid #cccccc;border-right:1px solid #cccccc;">주간</td>
											<td width="60" style="border-top:1px solid #cccccc;border-right:1px solid #cccccc;font-weight:bold;" bgcolor="#eeeeee">월간</td>
                      -->
                      <td style="font-family:tahoma;font-size:18;font-weight:bold;">
                        <span style="color:#aaaaaa;cursor:hand;" onclick="moveMonth('-');">◀</span>
                        <span id="mainym"><?=date("Y")?>.<?=date("m")?></span>
                        <span style="color:#aaaaaa;cursor:hand;" onclick="moveMonth('+');">▶</span>
                      </td>
                      <td width="180" align="left">
												<!--
                        <input type="text" style="width:120;height:20;border:1px solid #aaaaaa;">
                        <input type="button" value="검색" onclick="" style="width:50;background-color:#dddddd;border:1px solid #666666;" onfocus="this.blur();">
												-->
                      </td>
                    </tr>
                  </table>
                  <!-- 달력 영역 메뉴 end -->
                </td>
              </tr>
              <tr>
                <td valign="top">
                  <!-- 달력 영역 달력 start -->
                  <div id="maindiary" style="height:100%">
									</div>
                  <!-- 달력 영역 달력 end -->
                </td>
              </tr>
              </tr>
            </table>
            <!-- 달력 영역 end -->
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr height="20" align="center" bgcolor="#dedede">
    <td style="border-top:1px solid #cccccc;">
      <!-- 풋터 영역 start -->
      <b>일정관리</b>
      <!-- 풋터 영역 end -->
    </td>
  </tr>
</table>
<iframe name="ProcessFrame" width="98%" height="200"></iframe>
<div id="delDiv" style="display:none;"></div>

<script language="javascript" type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script>

	/////좌측 메뉴 숨기기/보이기

	var update_no = "";

	$(document).ready(function () {
		

		$("#maindiary tr").find("td").hover(function () { 

			if (($(this).css('backgroundColor') == "rgb(255, 253, 200)") || ($(this).css('backgroundColor') == "#fffdc8")) {
				$(this).css('backgroundColor', '#ebe9b8');
			} else {
				$(this).css('backgroundColor', '#eeeeee');
			}
		}, function () { 

			if (($(this).css('backgroundColor') == "rgb(235, 233, 184)") || ($(this).css('backgroundColor') == "#ebe9b8")) {
				$(this).css('backgroundColor', "#fffdc8"); 
			} else {
				$(this).css('backgroundColor', "#ffffff"); 
			}
		});

		$("#maindiary tr").find("td").click(function () {

			$('#calendarFormDiv').show();
			cFcontrol();
			
			//alert(update_no);

			if (update_no == "") {
				calendarForm.sdate.value = $(this).val();
				calendarForm.edate.value = $(this).val();
				calendarForm.cate.value = document.getElementById("calendarcateDiv").childNodes[0].rows[0].cells[0].id.replace("cateseq","");
			}

		});

	});
	

	function js_view(didx) {

		update_no = didx;
		
		alert(update_no);
		
		calendarForm.update_seq_no.value = update_no;
		calendarForm.method = "post";
		calendarForm.action = "read.php";
		calendarForm.target = "ProcessFrame";
		calendarForm.submit();
		
		/*
		calendarForm.sdate.value = d_sdate;
		calendarForm.edate.value = d_edate;
		calendarForm.title.value = d_title;
		calendarForm.memo.value	 = d_memo;
	
		
		alert(d_color);
		alert(d_sdate);
		alert(d_edate);
		alert(d_title);
		alert(d_memo);
		*/

	}

	function HideLeftMenu() {
		if(document.getElementById("leftmenutd").style.display=="none") {
			document.getElementById("leftmenutd").style.display = "block";
			document.getElementById("leftmenubartd").innerText = "◀";
		}
		else {
			document.getElementById("leftmenutd").style.display = "none";
			document.getElementById("leftmenubartd").innerText = "▶";
		}

		setTimeout("diarybarInsert();",10);
	}

  /////좌측 메뉴 달력 만들기
  var lsy = "<?=date("Y")?>";
  var lsm = "<?=date("m")?>";

	function makeLeftDiary() {

		var strsm = lsm+"";
		if(strsm.length==1) { strsm = "0"+strsm; }
		var nfirstdate = new Date(lsy,(lsm-1),1);
		var nfirstweek = nfirstdate.getDay();
		var nlastdate = new Date(lsy,lsm,0);
		var nlastday = nlastdate.getDate();

		var dtmsg = "<tr height='40'><td colspan='2' style='font-family:tahoma;font-size:24;color:#aaaaaa;cursor:hand;' align='right' onclick=\"moveLeftMonth('-')\">◀</td>";
		dtmsg += "<td colspan='3' align='center'><div style='font-family:tahoma;font-size:10;font-weight:bold;'>"+lsy+"</div><div style='font-family:tahoma;font-size:24;font-weight:bold;'>"+lsm+"</div></td>";
		dtmsg += "<td colspan='2' style='font-family:tahoma;font-size:24;color:#aaaaaa;cursor:hand;' onclick=\"moveLeftMonth('+')\">▶</td></tr><tr bgcolor='#cccccc'><td colspan='7' height='1'></td></tr>";
		dtmsg += "<tr align='center' height='21'><td width='14%' style='color:#ff0000;'>일</td><td width='14%'>월</td><td width='14%'>화</td><td width='14%'>수</td><td width='14%'>목</td><td width='14%'>금</td><td width='14%' style='color:#0000ff;'>토</td></tr>";
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

		document.getElementById("leftdiary").innerHTML = "<table width='100%' height='100%'  border='0' cellspacing='0' cellpadding='0'>"+dtmsg+"<tr align='center' height='17' bgcolor='#eeeeee'>"+dmsg+"</tr><tr bgcolor='#cccccc'><td colspan='7' height='1'></td></tr></table>";
    
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

  /////좌측 메뉴 달력 월 이동
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
    //document.getElementById("leftdiary").childNodes[0].rows[0].cells[1].innerHTML = "<div style='font-family:tahoma;font-size:10;font-weight:bold;'>"+lsy+"</div><div style='font-family:tahoma;font-size:24;font-weight:bold;'>"+lsm+"</div>";
    makeLeftDiary();
  }

  /////메인 달력 만들기
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
		var dtmsg = "<tr align='center' height='25'><td width='14%' style='color:#ff0000;border-right:1px solid #cccccc;border-bottom:1px solid #666666;'><b>일</b></td>";
    dtmsg += "<td width='14%' style='border-right:1px solid #cccccc;border-bottom:1px solid #666666;'><b>월</b></td><td width='14%' style='border-right:1px solid #cccccc;border-bottom:1px solid #666666;'><b>화</b></td>";
    dtmsg += "<td width='14%' style='border-right:1px solid #cccccc;border-bottom:1px solid #666666;'><b>수</b></td><td width='14%' style='border-right:1px solid #cccccc;border-bottom:1px solid #666666;'><b>목</b></td>";
    dtmsg += "<td width='14%' style='border-right:1px solid #cccccc;border-bottom:1px solid #666666;'><b>금</b></td><td width='14%' style='border-bottom:1px solid #666666;'><b>토</b></td></tr>";
		var d = 0;
		var ntdsum = nlastday+nfirstweek;
		var dmsg = "";
		
		for(i=0; i<ntdsum; i++) {

      if(i<nfirstweek) {
        mtdcnt++;
        dmsg += "<td id='dtd"+mtdcnt+"' valign='top' ";
        dmsg += " style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;'>";
        if(i==0) dmsg += "<span style='padding-left:3;color:#ff8989;'></span><br><div id='daycnt"+mtdcnt+"'></div></td>";
        else dmsg += "<span style='padding-left:3;color:#bbbbbb;'></span><div id='daycnt"+mtdcnt+"'></div></td>";
      } else {
        d++;
        var tdfc = "";
        if(((i+1)%7)==1) { tdfc = "color:#ff0000;"; }
        mtdcnt++;
        var nday = d+"";
        if(nday.length<2) { nday = "0"+d; }
        dmsg += "<td id='dtd"+mtdcnt+"' value='"+ny+"-"+nm+"-"+nday+"' valign='top' ";
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
        dmsg += "<td id='dtd"+mtdcnt+"' value='"+nexty+"-"+nextm+"-"+nextmnday+"' valign='top' ";
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
		

		var day_cnt			= 1;
		var ym_day_cnt	= 1;

		for(i=1; i< document.getElementById("maindiary").childNodes[0].rows.length; i++) {
			for(j=0; j<7; j++) {

				if ($("#maindiary tr").eq(i).find("td").eq(j).val() == "") {
					if((day_cnt+"").length<2) {
						str_day_cnt = "0"+day_cnt;
					} else {
						str_day_cnt = day_cnt;
					}

					if (ym_day_cnt > nlastday) { 
						str_ny = nexty;
						str_nm = nextm;
					} else {
						str_ny = ny;
						str_nm = nm;
					}


					$("#maindiary tr").eq(i).find("td").eq(j).val(str_ny+"-"+str_nm+"-"+str_day_cnt);
					
					if (day_cnt == nlastday) day_cnt = 0;
					
					ym_day_cnt = ym_day_cnt + 1;
					day_cnt = day_cnt + 1;
				}
			}
		}

		for(i=1; i<document.getElementById("maindiary").childNodes[0].rows.length; i++) {
			if(document.getElementById("maindiary").childNodes[0].rows[i].cells[6]) {
				document.getElementById("maindiary").childNodes[0].rows[i].cells[6].style.borderRight = "0";
			}
		}

		if(ny=="<?=date("Y")?>" && nm=="<?=date("m")?>") {
			for(i=1; i< document.getElementById("maindiary").childNodes[0].rows.length; i++) {
				for(j=0; j<7; j++) {
					if ((document.getElementById("maindiary").childNodes[0].rows[i].cells[j].style.color=="#474747") || (document.getElementById("maindiary").childNodes[0].rows[i].cells[j].style.color=="rgb(71, 71, 71)")) {
						
						var temp_str = document.getElementById("maindiary").childNodes[0].rows[i].cells[j].innerText;
						
						temp_str = temp_str.replace(/^\s+|\s+$/g, '');

						if(temp_str=="<?=date("d")*1?>") {
							$("#maindiary tr").eq(i).find("td").eq(j).css("backgroundColor","#fffdc8");
						} else {
							$("#maindiary tr").eq(i).find("td").eq(j).css("backgroundColor","");
						}
					}
				}
			}
		}


	}

  makeDiary();

  /////메인 달력 월 이동
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

  /////페이지 로딩후 width를 재 조정해 주는 역할
  document.body.style.width = "100%";

	////일정등록폼 중앙 정렬
	function cFcontrol() {
		var t_left = (parseInt(document.body.offsetWidth)-parseInt($("#calendarFormDiv").find("table").width()))/2;
		var t_height = (parseInt(document.body.offsetHeight)-parseInt($("#calendarFormDiv").find("table").height()))/2;
		$("#calendarFormDiv").find("table").css("left",t_left+"px");
		$("#calendarFormDiv").find("table").css("top",t_height+"px");
	}
	cFcontrol();

	/////캘린더관리창 중앙 정렬
	function cCcontrol() {

		var t_left = (parseInt(document.body.offsetWidth)-parseInt($("#calendarListDiv").find("table").width()))/2;
		var t_height = (parseInt(document.body.offsetHeight)-parseInt($("#calendarListDiv").find("table").height()))/2;
		$("#calendarListDiv").find("table").css("left",t_left+"px");
		$("#calendarListDiv").find("table").css("top",t_height+"px");
	}
	cCcontrol();

	/////창의 크기변경시 일정등록폼 재정렬
	document.body.onresize = function() {
		setTimeout("cFcontrol();",100);
		setTimeout("cCcontrol();",100);
		setTimeout("diarybarInsert();",10);
	}

  /////메인 달력에서 onmousedown,onmouseup,onmouseover이벤트가 일어날 때 셀을 선택하게하거나 일정등록 폼을 보여줌
  var dragmd = "";
  var dragsv = "";
  var dragev = "";

  function cFdrag(m,v) {

		//alert(m);
		//alert(v);
		/*
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
		*/
		/*
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
		*/
	}

  /////일정등록폼 레이어 숨기기
  function cFclose() {
    document.getElementById('calendarFormDiv').style.display = "none";
    for(i=1; i<=mtdcnt; i++) {
      if(document.getElementById("dtd"+i).value=="<?=date("Y-m-d")?>") document.getElementById("dtd"+i).style.backgroundColor = "#fffdc8";
      else document.getElementById("dtd"+i).style.backgroundColor = "";
    }
  }

  /////일정등록 달력 보여주기

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

  /////일정등록 달력 만들기

  function MakeFormDiary(f) {
    var ddiv = document.getElementById("calendardiaryDiv");
    var strsm = fnm+"";
    if(strsm.length==1) { strsm = "0"+strsm; }
    var nfirstdate = new Date(fny,(fnm-1),1);
    var nfirstweek = nfirstdate.getDay();
    var nlastdate = new Date(fny,fnm,0);
    var nlastday = nlastdate.getDate();
    var cdtmsg = "<tr bgcolor='#dddddd' height='25'><td colspan='1' style='font-family:tahoma;color:#aaaaaa;cursor:hand;' align='right' onclick=\"moveFormMonth('"+f+"','-')\">◀</td>";
    cdtmsg += "<td colspan='5' align='center'><div id='formym' style='font-family:tahoma;font-weight:bold;'>"+fny+"년 "+fnm+"월</div></td>";
    cdtmsg += "<td colspan='1' style='font-family:tahoma;color:#aaaaaa;cursor:hand;' onclick=\"moveFormMonth('"+f+"','+')\">▶</td></tr>";
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
    ddiv.innerHTML = "<table width='150' border='0' cellspacing='0' cellpadding='0' style='border:2px solid #aaaaaa;'>"+cdtmsg+"<tr align='center' height='17' bgcolor='#eeeeee'>"+cdmsg+"</tr><tr bgcolor='#cccccc'><td colspan='7' height='1'></td></tr><tr bgcolor='#cccccc'><td colspan='7' align='center' style='font-size:11;font-family:tahoma;cursor:hand;' bgcolor='#dddddd' height='18' onclick=\"selectFormDate('"+f+"','<?=date("Y-m-d");?>');\">오늘 : <?=date("Y-m-d");?></td></tr></table>";
  }

  /////일정등록 달력 월 이동
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
    document.getElementById("formym").innerHTML = fny+"년 "+fnm+"월";
    MakeFormDiary(f);
  }

  /////일정등록 일자 선택
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

  /////일정등록 카테고리 보이기/숨기기
  function cateView() {
    if(document.getElementById("calendarcateDiv").style.display=="none") {
      document.getElementById("calendarcateDiv").style.display = "block";
    }
    else {
      document.getElementById("calendarcateDiv").style.display = "none";
    }
  }

  /////일정등록 카테고리선택
  function cateChange(v,c,s) {
    calendarForm.cate.value = s;
    document.getElementById("cateTr").cells[0].innerText = v;
    document.getElementById("cateTr").bgColor = c;
    cateView();
  }

  /////캘린더관리창 레이어 숨기기
  function cCclose() {
    cateForm.reset();
    changecolor('0','#6e9cf2');
    document.getElementById('calendarListDiv').style.display = "none";
  }

  /////캘린더관리 색상 변경
  function changecolor(n,c) {
    
		var cTbl = document.getElementById("colorDiv_table");
    
		for(i=0; i<cTbl.rows[0].cells.length; i++) {
      cTbl.rows[0].cells[i].style.border = "0px";
      cTbl.rows[0].cells[i].innerText = "";
    }

    cTbl.rows[0].cells[n].style.border = "1px solid #000000";
    cTbl.rows[0].cells[n].innerText = "√";
    cateForm.cname.style.backgroundColor = c;
    cateForm.ccolor.value = c;
    document.getElementById("colorDiv").style.display = "none";
  }

  /////캘린더 저장
  function cateSubmit() {
    if(!cateForm.cname.value.replace(/ /gi,"")) {
      alert("캘린더명을 입력하세요.");
      cateForm.cname.focus();
      return false;
    }
    cateForm.method = "post";
    cateForm.action = "category.php";
    cateForm.target = "ProcessFrame";
    cateForm.submit();
  }

  /////캘린더 관리 폼의 캘린더 목록에 추가
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
    td1.innerHTML = "<a href='javascript:;' style='color:#000000;' onclick=\"cateModify('"+s+"','"+n+"','"+t+"','"+c+"');\">수정</a> | <a href='javascript:;' style='color:#000000;' onclick=\"cateDelete('"+s+"','"+n+"');\">삭제</a>";
	}

  /////좌측 메뉴의 캘린더 목록에 추가
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
    ltd2.innerText = "▼";
  }

  /////일정등록폼의 카테고리 목록에 추가
  function diarycateList(s,n,c) {

    var dtbl = document.getElementById("calendarcateDiv_table");
    var dtr = dtbl.insertRow();
    dtr.style.height = "20";
    dtr.bgColor = c;
    dtr.onclick = function() { cateChange(this.cells[0].innerText,this.bgColor,this.cells[0].id.replace("cateseq","")); }
    dtr.onmouseover = function() { this.cells[1].innerText="√"; }
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

  /////카테고리 수정하기
  function cateModify(s,n,t,c) {
    cateForm.seq.value = s;
    cateForm.cname.value = n;
    cateForm.cname.style.backgroundColor = c;
    cateForm.ctext.value = t;
    cateForm.ccolor.value = c;
  }

  /////카테고리 삭제하기
  function cateDelete(s,n) {
    if(confirm("정말 삭제하시겠습니까?")) {
      document.getElementById("delDiv").innerHTML = "<form name='deleteForm'><input type='hidden' name='seq' value='"+s+"'><input type='hidden' name='cname' value='"+n+"'><input type='hidden' name='mde' value='del'></form>";
      deleteForm.method = "post";
      deleteForm.action = "category.php";
      deleteForm.target = "ProcessFrame";
      deleteForm.submit();
      document.getElementById("delDiv").innerHTML = "";
    }
  }

  /////일정 저장
  function diarySubmit() {

		if(!calendarForm.title.value.replace(/ /gi,"")) {
			alert("일정내용을 입력하세요.");
			calendarForm.title.focus();
			return false;
		}
		
		calendarForm.sdt.value = document.getElementById("maindiary").childNodes[0].rows[1].cells[0].value;
		calendarForm.edt.value = document.getElementById("maindiary").childNodes[0].rows[document.getElementById("maindiary").childNodes[0].rows.length-1].cells[6].value;

    calendarForm.method = "post";
    calendarForm.action = "diary.php";
    calendarForm.target = "ProcessFrame";
    calendarForm.submit();

		alert("0n Submit");
  }

  diarylist = new Array();

  /////일정 표시 하기
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
          var barmsg = "<div class='schedulebar' onclick=\"js_view("+dlary[0]+");\" style='position:absolute;z-index:1;background-Color:"+dlary[1]+";color:#ffffff;margin-top:"+(maxdaybarcnt*19+3)+";";
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
          var barmsg = "<div class='schedulebar' onclick=\"js_view("+dlary[0]+");\" style='position:absolute;z-index:1;background-Color:"+dlary[1]+";color:#ffffff;margin-top:"+(maxdaybarcnt*19+3)+";";
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
		
		cFclose();

  }

	/////index.php의 로딩이 완료된 후 ProcessFrame에서 load.php를 로딩
	function dataLoad() {
		var nowsdt = document.getElementById("maindiary").childNodes[0].rows[1].cells[0].value;
		var nowedt = document.getElementById("maindiary").childNodes[0].rows[document.getElementById("maindiary").childNodes[0].rows.length-1].cells[6].value;
		ProcessFrame.location.href = "load.php?sdt="+nowsdt+"&edt="+nowedt;
	}

	dataLoad();

	if(typeof String.prototype.trim !== 'function') {
		String.prototype.trim = function() {
			return this.replace(/^\s+|\s+$/g, ''); 
		}
	}

</script>
</body>
</html>


<!--
<table width='100%' height='100%' border='0' cellspacing='0' cellpadding='0' onselectstart='return false'>
<tr align='center' height='25'>
	<td width='14%' style='color:#ff0000;border-right:1px solid #cccccc;border-bottom:1px solid #666666;'><b>일</b></td>
	<td width='14%' style='border-right:1px solid #cccccc;border-bottom:1px solid #666666;'><b>월</b></td>
	<td width='14%' style='border-right:1px solid #cccccc;border-bottom:1px solid #666666;'><b>화</b></td>
	<td width='14%' style='border-right:1px solid #cccccc;border-bottom:1px solid #666666;'><b>수</b></td>
	<td width='14%' style='border-right:1px solid #cccccc;border-bottom:1px solid #666666;'><b>목</b></td>
	<td width='14%' style='border-right:1px solid #cccccc;border-bottom:1px solid #666666;'><b>금</b></td>
	<td width='14%' style='border-bottom:1px solid #666666;'><b>토</b></td>
</tr>
<tr>
	<td id='dtd1' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;'><span style='padding-left:3;color:#ff8989;'></span><br><div id='daycnt1'></div></td>
	<td id='dtd2' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;'><span style='padding-left:3;color:#bbbbbb;'></span><div id='daycnt2'></div></td>
	<td id='dtd3' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;'><span style='padding-left:3;color:#bbbbbb;'></span><div id='daycnt3'></div></td>
	<td id='dtd4' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;'><span style='padding-left:3;color:#bbbbbb;'></span><div id='daycnt4'></div></td>
	<td id='dtd5' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;'><span style='padding-left:3;color:#bbbbbb;'></span><div id='daycnt5'></div></td>
	<td id='dtd6' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;'><span style='padding-left:3;color:#bbbbbb;'></span><div id='daycnt6'></div></td>
	<td id='dtd7' value='2015-08-01' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;'>1</span><div id='daycnt7'></div></td>
</tr>
<tr>
	<td id='dtd8' value='2015-08-02' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;color:#ff0000;'>2</span><div id='daycnt8'></div></td>
	<td id='dtd9' value='2015-08-03' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;'>3</span><div id='daycnt9'></div></td>
	<td id='dtd10' value='2015-08-04' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;'>4</span><div id='daycnt10'></div></td>
	<td id='dtd11' value='2015-08-05' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;'>5</span><div id='daycnt11'></div></td>
	<td id='dtd12' value='2015-08-06' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;'>6</span><div id='daycnt12'></div></td>
	<td id='dtd13' value='2015-08-07' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;'>7</span><div id='daycnt13'></div></td>
	<td id='dtd14' value='2015-08-08' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;'>8</span><div id='daycnt14'></div></td>
</tr>
<tr>
	<td id='dtd15' value='2015-08-09' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;color:#ff0000;'>9</span><div id='daycnt15'></div></td>
	<td id='dtd16' value='2015-08-10' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;'>10</span><div id='daycnt16'></div></td>
	<td id='dtd17' value='2015-08-11' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;'>11</span><div id='daycnt17'></div></td>
	<td id='dtd18' value='2015-08-12' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;'>12</span><div id='daycnt18'></div></td>
	<td id='dtd19' value='2015-08-13' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;'>13</span><div id='daycnt19'></div></td>
	<td id='dtd20' value='2015-08-14' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;'>14</span><div id='daycnt20'></div></td>
	<td id='dtd21' value='2015-08-15' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;'>15</span><div id='daycnt21'></div></td>
</tr>
<tr>
	<td id='dtd22' value='2015-08-16' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;color:#ff0000;'>16</span><div id='daycnt22'></div></td>
	<td id='dtd23' value='2015-08-17' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;'>17</span><div id='daycnt23'></div></td>
	<td id='dtd24' value='2015-08-18' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;'>18</span><div id='daycnt24'></div></td>
	<td id='dtd25' value='2015-08-19' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;'>19</span><div id='daycnt25'></div></td>
	<td id='dtd26' value='2015-08-20' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;'>20</span><div id='daycnt26'></div></td>
	<td id='dtd27' value='2015-08-21' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;'>21</span><div id='daycnt27'></div></td>
	<td id='dtd28' value='2015-08-22' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;'>22</span><div id='daycnt28'></div></td>
</tr>
<tr>
	<td id='dtd29' value='2015-08-23' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;color:#ff0000;'>23</span><div id='daycnt29'></div></td>
	<td id='dtd30' value='2015-08-24' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;'>24</span><div id='daycnt30'></div></td>
	<td id='dtd31' value='2015-08-25' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;'>25</span><div id='daycnt31'></div></td>
	<td id='dtd32' value='2015-08-26' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;'>26</span><div id='daycnt32'></div></td>
	<td id='dtd33' value='2015-08-27' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;'>27</span><div id='daycnt33'></div></td>
	<td id='dtd34' value='2015-08-28' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;'>28</span><div id='daycnt34'></div></td>
	<td id='dtd35' value='2015-08-29' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;'>29</span><div id='daycnt35'></div></td>
</tr>
<tr>
	<td id='dtd36' value='2015-08-30' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;color:#ff0000;'>30</span><div id='daycnt36'></div></td>
	<td id='dtd37' value='2015-08-31' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;color:#474747;'><span style='padding-left:3;'>31</span><div id='daycnt37'></div></td>
	<td id='dtd38' value='2015-09-01' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;'><span style='padding-left:3;color:#bbbbbb;'>1</span><div id='daycnt38'></div></td>
	<td id='dtd39' value='2015-09-02' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;'><span style='padding-left:3;color:#bbbbbb;'>2</span><div id='daycnt39'></div></td>
	<td id='dtd40' value='2015-09-03' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;'><span style='padding-left:3;color:#bbbbbb;'>3</span><div id='daycnt40'></div></td>
	<td id='dtd41' value='2015-09-04' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;'><span style='padding-left:3;color:#bbbbbb;'>4</span><div id='daycnt41'></div></td>
	<td id='dtd42' value='2015-09-05' valign='top' onmousedown="cFdrag('s',this.id.replace(/dtd/,''));" onmouseup="cFdrag('e',this.id.replace(/dtd/,''));" onmouseover="cFdrag('o',this.id.replace(/dtd/,''));" style='padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;'><span style='padding-left:3;color:#bbbbbb;'>5</span><div id='daycnt42'></div></td>
</tr>
</table>
-->