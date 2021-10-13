<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>일정관리</title>
<style>
  td, div, input, textarea, select, iframe {
    font-size:12; font-family:나눔고딕; color:#474747;
  }
  .sfont { font-family:tahoma; font-size:10; }
  .sgfont { font-family:tahoma; font-size:10; color:#bbbbbb; }
  .srfont { font-family:tahoma; font-size:10; color:#ff0000; }
  .srgfont { font-family:tahoma; font-size:10; color:#ff8989; }
</style>
<meta http-equiv="Content-Type" content="text/html;charset=euc-kr">
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" style="overflow-y:auto;">

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
              <tr align="center" height="50">
                <td>
                  <!-- 메뉴영역 버튼 start -->
                  <input type="button" value="일정쓰기" style="width:87;height:30;background-color:#dddddd;border:1px solid #666666;" onfocus="this.blur();">
                  <input type="button" value="기념일관리" style="width:87;height:30;background-color:#dddddd;border:1px solid #666666;" onfocus="this.blur();">
                  <!-- 메뉴영역 버튼 end -->
                </td>
              </tr>
              <tr align="center">
                <td style="padding:7;">
                  <!-- 메뉴영역 달력 start -->
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr height="40">
                      <td colspan="2" style="font-family:tahoma;font-size:24;color:#aaaaaa;" align="right">◀</td>
                      <td colspan="3" align="center">
                        <div style="font-family:tahoma;font-size:10;font-weight:bold;">2009</div>
                        <div style="font-family:tahoma;font-size:24;font-weight:bold;">12</div>
                      </td>
                      <td colspan="2" style="font-family:tahoma;font-size:24;color:#aaaaaa;">▶</td>
                    </tr>
                    <tr bgcolor="#cccccc"><td colspan="7" height="1"></td></tr>
                    <tr align="center" height="21">
                      <td width="14%" style="color:#ff0000;">일</td>
                      <td width="14%">월</td>
                      <td width="14%">화</td>
                      <td width="14%">수</td>
                      <td width="14%">목</td>
                      <td width="14%">금</td>
                      <td width="14%" style="color:#0000ff;">토</td>
                    </tr>
                    <tr bgcolor="#cccccc"><td colspan="7" height="1"></td></tr>
                    <tr align="center" height="17" bgcolor="#eeeeee">
                      <td class="srgfont">29</td><td class="sgfont">30</td><td class="sfont">1</td><td class="sfont">2</td>
                      <td class="sfont">3</td><td class="sfont">4</td><td class="sfont">5</td>
                    </tr>
                    <tr align="center" height="17" bgcolor="#eeeeee">
                      <td class="srfont">6</td><td class="sfont">7</td><td class="sfont">8</td><td class="sfont">9</td>
                      <td class="sfont">10</td><td class="sfont">11</td><td class="sfont">12</td>
                    </tr>
                    <tr align="center" height="17" bgcolor="#eeeeee">
                      <td class="srfont">13</td><td class="sfont">14</td><td class="sfont">15</td><td class="sfont">16</td>
                      <td class="sfont">17</td><td class="sfont">18</td><td class="sfont">19</td>
                    </tr>
                    <tr align="center" height="17" bgcolor="#eeeeee">
                      <td class="srfont">20</td><td class="sfont">21</td><td class="sfont">22</td><td class="sfont">23</td>
                      <td class="sfont">24</td><td class="srfont">25</td><td class="sfont" bgcolor="#6e9cf2" style="color:#ffffff;">26</td>
                    </tr>
                    <tr align="center" height="17" bgcolor="#eeeeee">
                      <td class="srfont">27</td><td class="sfont">28</td><td class="sfont">29</td><td class="sfont">30</td>
                      <td class="sfont">31</td><td class="srgfont">1</td><td class="sgfont">2</td>
                    </tr>
                    <tr align="center" height="17">
                      <td class="srgfont">3</td><td class="sgfont">4</td><td class="sgfont">5</td><td class="sgfont">6</td>
                      <td class="sgfont">7</td><td class="sgfont">8</td><td class="sgfont">9</td>
                    </tr>
                    <tr bgcolor="#cccccc"><td colspan="7" height="1"></td></tr>
                  </table>
                  <!-- 메뉴영역 달력 end -->

                  <!-- 메뉴영역 캘린터목록 start -->
                  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top:20;">
                    <tr height="30">
                      <td width="50%"><b>캘린더목록</b></td>
                      <td align="right">+만들기 | 설정</td>
                    </tr>
                  </table>
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="1%"><input type="checkbox" checked></td>
                      <td bgcolor="#6e9cf2" style="padding-left:5;color:#ffffff;">개인일정</td>
                      <td width="1%" bgcolor="#6e9cf2" style="padding-right:5;color:#ffffff;font-family:tahoma;font-size:8;">▼</td>
                    </tr>
                    <tr><td height="3" colspan="3"></td></tr>
                    <tr>
                      <td><input type="checkbox" checked></td>
                      <td bgcolor="#e6a11b" style="padding-left:5;color:#ffffff;">회사일정</td>
                      <td bgcolor="#e6a11b" style="padding-right:5;color:#ffffff;font-family:tahoma;font-size:8;">▼</td>
                    </tr>
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
                      <td width="60" style="border-top:1px solid #cccccc;border-right:1px solid #cccccc;">일간</td>
                      <td width="60" style="border-top:1px solid #cccccc;border-right:1px solid #cccccc;">주간</td>
                      <td width="60" style="border-top:1px solid #cccccc;border-right:1px solid #cccccc;font-weight:bold;" bgcolor="#eeeeee">월간</td>
                      <td style="font-family:tahoma;font-size:18;font-weight:bold;">
                        <span style="color:#aaaaaa;">◀</span>
                        2009.12
                        <span style="color:#aaaaaa;">▶</span>
                      </td>
                      <td width="180" align="left">
                        <input type="text" style="width:120;height:20;border:1px solid #aaaaaa;">
                        <input type="button" value="검색" onclick="" style="width:50;background-color:#dddddd;border:1px solid #666666;" onfocus="this.blur();">
                      </td>
                    </tr>
                  </table>
                  <!-- 달력 영역 메뉴 end -->
                </td>
              </tr>
              <tr>
                <td valign="top">
                  <!-- 달력 영역 달력 start -->
                  <table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr align="center" height="25">
                      <td width="14%" style="color:#ff0000;border-right:1px solid #cccccc;border-bottom:1px solid #666666;"><b>일</b></td>
                      <td width="14%" style="border-right:1px solid #cccccc;border-bottom:1px solid #666666;"><b>월</b></td>
                      <td width="14%" style="border-right:1px solid #cccccc;border-bottom:1px solid #666666;"><b>화</b></td>
                      <td width="14%" style="border-right:1px solid #cccccc;border-bottom:1px solid #666666;"><b>수</b></td>
                      <td width="14%" style="border-right:1px solid #cccccc;border-bottom:1px solid #666666;"><b>목</b></td>
                      <td width="14%" style="border-right:1px solid #cccccc;border-bottom:1px solid #666666;"><b>금</b></td>
                      <td width="14%" style="border-bottom:1px solid #666666;"><b>토</b></td>
                    </tr>
                    <tr>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;">
                        <span style="padding-left:3;color:#ff8989;">29</span>
                      </td>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;">
                        <span style="padding-left:3;color:#bbbbbb;">30</span>
                      </td>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;">
                        <span style="padding-left:3;">1</span>
                      </td>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;">
                        <span style="padding-left:3;">2</span>
                      </td>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;">
                        <span style="padding-left:3;">3</span>
                      </td>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;">
                        <span style="padding-left:3;">4</span>
                      </td>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;">
                        <span style="padding-left:3;">5</span>
                      </td>
                    </tr>
                    <tr>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;">
                        <span style="padding-left:3;color:#ff0000;">6</span>
                      </td>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;">
                        <span style="padding-left:3;">7</span>
                      </td>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;">
                        <span style="padding-left:3;">8</span>
                      </td>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;">
                        <span style="padding-left:3;">9</span>
                      </td>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;">
                        <span style="padding-left:3;">10</span>
                      </td>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;">
                        <span style="padding-left:3;">11</span>
                      </td>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;">
                        <span style="padding-left:3;">12</span>
                      </td>
                    </tr>
                    <tr>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;">
                        <span style="padding-left:3;color:#ff0000;">13</span>
                      </td>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;">
                        <span style="padding-left:3;">14</span>
                      </td>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;">
                        <span style="padding-left:3;">15</span>
                      </td>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;">
                        <span style="padding-left:3;">16</span>
                      </td>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;">
                        <span style="padding-left:3;">17</span>
                      </td>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;">
                        <span style="padding-left:3;">18</span>
                      </td>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;">
                        <span style="padding-left:3;">19</span>
                      </td>
                    </tr>
                    <tr>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;">
                        <span style="padding-left:3;color:#ff0000;">20</span>
                      </td>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;">
                        <span style="padding-left:3;">21</span>
                      </td>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;">
                        <span style="padding-left:3;">22</span>
                      </td>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;">
                        <span style="padding-left:3;">23</span>
                      </td>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;">
                        <span style="padding-left:3;">24</span>
                      </td>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;">
                        <span style="padding-left:3;color:#ff0000;">25</span>
                      </td>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;" bgcolor="#fffdc8">
                        <span style="padding-left:3;">26</span>
                      </td>
                    </tr>
                    <tr>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;">
                        <span style="padding-left:3;color:#ff0000;">27</span>
                      </td>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;">
                        <span style="padding-left:3;">28</span>
                      </td>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;">
                        <span style="padding-left:3;">29</span>
                      </td>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;">
                        <span style="padding-left:3;">30</span>
                      </td>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;">
                        <span style="padding-left:3;">31</span>
                      </td>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;border-right:1px solid #cccccc;">
                        <span style="padding-left:3;color:#ff8989;">1</span>
                      </td>
                      <td valign="top" style="padding-top:3;border-top:1px solid #cccccc;">
                        <span style="padding-left:3;color:#bbbbbb;">2</span>
                      </td>
                    </tr>
                  </table>
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
      <b>copyright wiki. (http://wiki.pe.kr/)</b>
      <!-- 풋터 영역 end -->
    </td>
  </tr>
</table>

<script>
  function HideLeftMenu() {
    if(document.getElementById("leftmenutd").style.display=="none") {
      document.getElementById("leftmenutd").style.display = "block";
      document.getElementById("leftmenubartd").innerText = "◀";
    }
    else {
      document.getElementById("leftmenutd").style.display = "none";
      document.getElementById("leftmenubartd").innerText = "▶";
    }
  }
</script>

</body>
</html>