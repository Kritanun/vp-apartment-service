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
            <h3 style="margin-bottom:5px;">ใบแจ้งการค้างชำระ</h3>
            <h4 style="margin-top:5px;">ห้อง {{$room_data->room_no}} ตึก {{$room_data->building_name}}</h4>
        </div>
    </head>
    <body>
        <table class="table">
            <tr>
                <th class="th-2" style="width:100px;">วันที่</th>
                <th class="th-2" style="width:100px;">ค่าเช่า</th>
                <th class="th-2" style="width:100px;">ค่าน้ำ</th>
                <th class="th-2" style="width:100px;">ค่าไฟฟ้า</th>
                <th class="th-2" style="width:100px;">ค่าขยะ</th>
                <th class="th-2" style="width:100px;">รวม</th>
            </tr>
            @foreach($overdue_bill_data as $key => $itemBill)
                <tr>
                    <td class="td-2">{{$itemBill['month']}} {{$itemBill['year']}}</td>
                    <td class="td-2">{{$room_data->rental_balance}}</td>
                    <td class="td-2">{{$itemBill['water_amount']}}</td>
                    <td class="td-2">{{$itemBill['electricity_amount']}}</td>
                    <td class="td-2">{{$itemBill['trash_amount']}}</td>
                    <td class="td-2">{{$itemBill['total']}}</td>
                </tr>
            @endforeach
            <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align: right;padding-right: 5px;">รวม</td>
                    <td class="td-2">{{$overdue_balance}}</td>
                </tr>
        </table>
        <div style="margin-left:30px">
            <h3>ธ.กสิกร 062-8365660 นาย กฤตนันท์ ตัณฑวิบูลย์วงศ์</h3>
        </div>
        <div class="page-break"></div>
    </body>
</html>