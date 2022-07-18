<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>

            @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: normal;
            src: url("{{ public_path('fonts/THSarabunNew.ttf') }}") format('truetype');
            }

            @font-face {
                font-family: 'THSarabunNew';
                font-style: normal;
                font-weight: bold;
                src: url("{{ public_path('fonts/THSarabunNew Bold.ttf') }}") format('truetype');
            }

            @font-face {
                font-family: 'THSarabunNew';
                font-style: italic;
                font-weight: normal;
                src: url("{{ public_path('fonts/THSarabunNew Italic.ttf') }}") format('truetype');
            }

            @font-face {
                font-family: 'THSarabunNew';
                font-style: italic;
                font-weight: bold;
                src: url("{{ public_path('fonts/THSarabunNew BoldItalic.ttf') }}") format('truetype');
            }

            body{
                font-family: 'THSarabunNew';
                font-size: 20px;
                border-style: double;
            }

            .th-1{
                width: 80%;
                text-align: left;
                font-size: 22px;
                padding-left: 5px
            }

            .th-2{
                width: 20%;
                text-align: center;
                font-size: 22px;
                padding-left: 5px
            }

            .td-1{
                width: 80%;
                text-align: left;
                font-size: 18px;
                padding-left: 5px
            }

            .td-2{
                width: 20%;
                text-align: center;
                font-size: 18px;
                padding-left: 5px
            }
            

            .table{
                width: 600px;
                margin-left: auto;
                margin-right: auto;
            }

            table, th, td{
                border: 1px solid;
                border-collapse: collapse;
            }

        </style>
        <div style="text-align: center;">
            <h1>VP Rangsit Apmartment</h1>
            <h3 style="margin-bottom:5px;">ใบจอง</h3>
            <h4 style="margin-top:5px;">ห้อง {{$room_data->room_no}} ตึก {{$room_data->building_name}}</h4>
        </div>
    </head>
    <body>
        <table class="table">
            <tr>
                <th class="th-2" style="width:100px;">ชื่อ - นามสกุล</th>
                <th class="th-2" style="width:100px;">ค่ามัดจำ</th>
                <th class="th-2" style="width:100px;">วันที่จอง</th>
                <th class="th-2" style="width:100px;">วันที่เข้าอยู่</th>
            </tr>
            <tr>
                <td class="td-2">{{$room_data->user_full_name}}</td>
                <td class="td-2">{{$room_data->deposit_amount}}</td>
                <td class="td-2">{{$room_data->request_date}}</td>
                <td class="td-2">{{$room_data->reserv_stay_in_date}}</td>
            </tr>
        </table>
    </body>
</html>