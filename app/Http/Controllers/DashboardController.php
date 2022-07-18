<?php

namespace App\Http\Controllers;

use App\Models\ElectricityBill;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use PDF;
use Storage;

class DashboardController extends Controller
{
    private $authUser = "Unknow";
    public function __construct()
	{

    }

    public function dashboard_monthly_summary(Request $request)
    {
        $monthArr = [
            "",
            "มกราคม", 
            "กุมภาพันธ์", 
            "มีนาคม",
            "เมษายน",
            "พฤษภาคม",
            "มิถุนายน",
            "กรกฎาคม",
            "สิงหาคม",
            "กันยายน",
            "ตุลาคม",
            "พฤศจิกายน",
            "ธันวาคม"];

        $items = ElectricityBill::leftJoin('room','electricity_bill.room_id','=','room.room_id')
                            ->where(function($query) use($request) {
                                if($request->has('room_id')){
                                    $query->whereIn('room.room_id',$request->room_id);
                                }

                                if($request->has('month')){
                                    $query->where('electricity_bill.electricity_month',$request->month);
                                }

                                if($request->has('year')){
                                    $query->where('electricity_bill.electricity_year',$request->year);
                                }

                                if($request->has('building_name')){
                                    $query->whereIn('room.building_name',$request->building_name);
                                }
                            })->get();

        
        $sumPay = collect($items)->where('status_payment','=',1)->sum('total');
        $sumNotPay = collect($items)->where('status_payment','=',0)->sum('total');
        $sumAllPay = collect($items)->sum('total');

        $itemsThisYear = ElectricityBill::leftJoin('room','electricity_bill.room_id','=','room.room_id')
            ->where('electricity_bill.electricity_year',$request->year)->get();

        $dataColums = [];
        $itemsThisYear = collect($itemsThisYear)->groupBy('electricity_month')->values();
        foreach($itemsThisYear as $item){
            $dataColums[] = [
                "year" => $item[0]->electricity_year."-".$monthArr[$item[0]->electricity_month],
                'value' => collect($item)->where('status_payment','=',1)->sum('total'),
                'type' => 'ยอดชำระแล้วทั้งหมด'
            ];
            $dataColums[] = [
                "year" => $item[0]->electricity_year."-".$monthArr[$item[0]->electricity_month],
                'value' => collect($item)->where('status_payment','=',0)->sum('total'),
                'type' => 'ยอดค้างชำระทั้งหมด'
            ];
        }

        $data = [];
        $data[] = [
            "type" => 'ยอดค้างชำระทั้งหมด',
            'value' =>  $sumNotPay
        ];
        $data[] = [
            "type" => 'ยอดชำระแล้วทั้งหมด',
            'value' =>  $sumPay
        ];

        return response()->json(["data" => $data,'dataColums' => $dataColums, 'total' => $sumAllPay]);
    }

    public function dashboard_statistics_usage_room(Request $request)
    {
        $items = ElectricityBill::leftJoin('room','room.room_id','=','electricity_bill.room_id')
                ->where(function($query) use($request){

                    if($request->has('year')){
                        $query->where('electricity_bill.electricity_year', $request->electricity_year);
                    }

                    if($request->has('room_id')){
                        $query->where('electricity_bill.room_id',$request->room_id);
                    }

                })->get(['electricity_bill.*','room.room_no','room.building_name']);
        $monthArr = [
            "",
            "มกราคม", 
            "กุมภาพันธ์", 
            "มีนาคม",
            "เมษายน",
            "พฤษภาคม",
            "มิถุนายน",
            "กรกฎาคม",
            "สิงหาคม",
            "กันยายน",
            "ตุลาคม",
            "พฤศจิกายน",
            "ธันวาคม"];

        $items = collect($items)->groupBy('building_no','room_id')->values();

        $responseElectricity = [];
        $responseWater = [];
        foreach( $items as $itemRoom){
            $itemRoom = collect($itemRoom)->sortBy('electricity_year')->sortBy('electricity_month')->all();

            foreach($itemRoom as $item ){
                $responseElectricity[] = [
                    "year" => $item->electricity_year . "-" . $monthArr[$item->electricity_month],
                    "value" => $item->electricity_amount,
                    "category" => $item->room_no . " ตึก " . $item->building_name
                ];

                $responseWater[] = [
                    "year" => $item->electricity_year . "-" . $monthArr[$item->electricity_month],
                    "value" => $item->water_amount,
                    "category" => $item->room_no . " ตึก " . $item->building_name
                ];
            }

        }

        return response()->json(["data_electric" => $responseElectricity, "data_water" => $responseWater],200);
    }


}