<div class="mainContent">
    <form name="frm" entype="multipart/form-data">
        <div></div><!--네비게이션-->
        <div>
            <h2 class="title">카달로그 콜리스트</h2>
            <hr class="lineTitle" noshade size='1'>
        </div><!--타이틀-->
        <div class="dvDashboard">
            <table class="dashboardTable">
                <colgroup>
                    <col width="24%">
                    <col width="36%">
                    <col width="16%">
                    <col width="24%">
                </colgroup>
                <tr>
                    <th>파일 이름</th>
                    <td><input type="text" name="filename"></td>
                    <th>비밀번호</th>
                    <td><input type="text" name="password"></td>
                </tr>
                <tr>
                    <th>범주</th>
                    <td>
                        <input type="radio" name="range" value="L" checked="checked;">지역
                        <input type="radio" name="range" value="S">영업부원
                    </td>
                    <th>목록</th>
                    <td>
                        <select name="sel_index_local">
                            <option value="0">선택</option>
                            <option value="1">서울</option>
                            <option value="2">경기</option>
                            <option value="3">인천</option>
                            <option value="4">충북</option>
                            <option value="5">충남</option>
                            <option value="6">전북</option>
                            <option value="7">전남</option>
                            <option value="8">경북</option>
                            <option value="9">경남</option>
                            <option value="10">제주</option>
                        </select>
                        <select name="sel_index_sales" style="display:none;">
                            <option value="0">선택</option>
                            <option value="60">양진현</option>
                            <option value="56">최인호</option>
                            <option value="64">배기진</option>
                            <option value="7">황건수</option>
                        </select>
                    </td>
                </tr>
            </table><!--dashboardTable-->
        </div><!--class="dvDashboard-->
        <div class="space20px"></div>
        <div class="space20px"></div>

        <div class="dvButtonCenter">

            <input type="button" class="btnGreen" value="엑셀로 받기" onclick="js_excel()">
        </div>
    </form>
</div><!--class="mainContent-->