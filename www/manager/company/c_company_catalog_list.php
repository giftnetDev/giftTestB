<div class="mainContent">
    <form name="frm" entype="multipart/form-data">
        <div></div><!--�׺���̼�-->
        <div>
            <h2 class="title">ī�޷α� �ݸ���Ʈ</h2>
            <hr class="lineTitle" noshade size='1'>
        </div><!--Ÿ��Ʋ-->
        <div class="dvDashboard">
            <table class="dashboardTable">
                <colgroup>
                    <col width="24%">
                    <col width="36%">
                    <col width="16%">
                    <col width="24%">
                </colgroup>
                <tr>
                    <th>���� �̸�</th>
                    <td><input type="text" name="filename"></td>
                    <th>��й�ȣ</th>
                    <td><input type="text" name="password"></td>
                </tr>
                <tr>
                    <th>����</th>
                    <td>
                        <input type="radio" name="range" value="L" checked="checked;">����
                        <input type="radio" name="range" value="S">�����ο�
                    </td>
                    <th>���</th>
                    <td>
                        <select name="sel_index_local">
                            <option value="0">����</option>
                            <option value="1">����</option>
                            <option value="2">���</option>
                            <option value="3">��õ</option>
                            <option value="4">���</option>
                            <option value="5">�泲</option>
                            <option value="6">����</option>
                            <option value="7">����</option>
                            <option value="8">���</option>
                            <option value="9">�泲</option>
                            <option value="10">����</option>
                        </select>
                        <select name="sel_index_sales" style="display:none;">
                            <option value="0">����</option>
                            <option value="60">������</option>
                            <option value="56">����ȣ</option>
                            <option value="64">�����</option>
                            <option value="7">Ȳ�Ǽ�</option>
                        </select>
                    </td>
                </tr>
            </table><!--dashboardTable-->
        </div><!--class="dvDashboard-->
        <div class="space20px"></div>
        <div class="space20px"></div>

        <div class="dvButtonCenter">

            <input type="button" class="btnGreen" value="������ �ޱ�" onclick="js_excel()">
        </div>
    </form>
</div><!--class="mainContent-->