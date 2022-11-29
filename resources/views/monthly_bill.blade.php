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
                width: 500px;
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
            <h3 style="margin-bottom:5px;">ใบแจ้งหนี้</h3>
            <h4 style="margin-top:5px;">ใบแจ้งหนี้ ห้อง {{$room_data->room_no}} ตึก {{$room_data->building_name}} ประจำเดือน {{$month}} {{$year}}</h4>
        </div>
    </head>
    <body>
        <table class="table">
            <tr>
                <th class="th-1">รายการ</th>
                <th class="th-2">จำนวน</th>
            </tr>
            <tr>
                <td class="td-1">ค่าเช่า</td>
                <td class="td-2">{{$room_data->rental_balance}}</td>
            </tr>
            <tr>
                <td class="td-1">หน่วยที่ใช้น้ำ</td>
                <td class="td-2">{{$bill_data->water_start_unit}} - {{$bill_data->water_end_unit}}</td>
            </tr>
            <tr>
                <td class="td-1">ค่าน้ำ</td>
                <td class="td-2">{{$bill_data->water_amount}}</td>
            </tr>
            <tr>
                <td class="td-1">หน่วยที่ใช้ไฟฟ้า</td>
                <td class="td-2">{{$bill_data->electricity_start_unit}} - {{$bill_data->electricity_end_unit}}</td>
            </tr>
            <tr>
                <td class="td-1">ค่าไฟฟ้า</td>
                <td class="td-2">{{$bill_data->electricity_amount}}</td>
            </tr>
            <tr>
                <td class="td-1">ค่าขยะ</td>
                <td class="td-2">{{$bill_data->trash_amount}}</td>
            </tr>

            <tr>
                <td class="td-1">ค่าค้างชำระ</td>
                <td class="td-2">{{$room_data->outstanding_balance}}</td>
            </tr>

            <tr>
                <td class="td-1">ส่วนลด</td>
                <td class="td-2">{{$bill_data->discount}}</td>
            </tr>
            
            <tr>
                <td class="td-1"><b><u>รวม</u></b></td>
                <td class="td-2"><b><u>
                    {{$bill_data->total + $room_data->outstanding_balance}}
                </u></b></td>
            </tr>
        </table>
        <div style="margin-left:100px">
            <h3>ธ.กสิกร 062-8365660 นาย กฤตนันท์ ตัณฑวิบูลย์วงศ์</h3>
        </div>
        <div class="page-break"></div>
    </body>
</html>