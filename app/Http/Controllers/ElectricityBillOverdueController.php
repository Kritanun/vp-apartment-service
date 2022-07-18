<?php

namespace App\Http\Controllers;

use App\Models\ElectricityBill;
use App\Models\Room;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use PDF;
use Storage;
use ZipArchive;

class ElectricityBillOverdueController extends Controller
{
    private $authUser = "Unknow";
    public function __construct()
	{

    }

    public function index(Request $request)
    {
        $thisYear = date('Y');
        $thisMonth = date('m');

        $authUser = $request->user();

        $items = ElectricityBill::leftJoin('room','electricity_bill.room_id','=','room.room_id')
                    ->leftJoin('user','user.user_id','=','room.user_id')
                    ->where('electricity_bill.status_payment','=',0)
                    ->where('room_status','สำเร็จ')
                    ->where(function($query) use($request, $authUser) {
                        if($request->has('room_id')){
                            $query->whereIn('room.room_id',$request->room_id);
                        }

                        if($authUser->is_admin == 0){
                            $query->where('room.user_id',$authUser->user_id);
                        }

                        if($request->has('user_id')){
                            $query->where('room.user_id',$request->user_id);
                        }

                        if($request->has('building_name')){
                            $query->whereIn('room.building_name',$request->building_name);
                        }
                    })
                    ->get([
                        'electricity_bill.*',
                        'room.room_no',
                        'room.building_name',
                        'room.outstanding_balance',
                        'user.user_full_name'
                    ]);

        $items = collect($items)->groupBy('room_id')->values();
        $response = [];
        foreach($items as $itemGroup){
            
            $response[] = [
                    "room_id" => $itemGroup[0]->room_id,
                    "room_no" => $itemGroup[0]->room_no,
                    "tenant_name" => $itemGroup[0]->user_full_name,
                    "month_overdue" => count($itemGroup),
                    "overdue_balance"=> 
                        collect($itemGroup)->sum('total') + $itemGroup[0]->outstanding_balance,
                    "building" => $itemGroup[0]->building_name
                ];
        }

        $resultPaginate = $this->customs_paginate($request, $response);

        return response()->json($resultPaginate,200);
    }

    public function approve_payment_overdue(Request $request, $id)
    {
        $item = ElectricityBill::where('room_id',$id)->update([
            'status_payment' => 1
        ]);


        return response()->json(["message" => "Successful"],200);
    }

    public function export(Request $request)
    {
        if(! $request->has('export_ids')){
           response()->json(["message" => "required data"],500);
        }

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

        foreach($request->export_ids as $roomId){

            $roomData = Room::find($roomId);

            $billData = ElectricityBill::leftJoin('room','electricity_bill.room_id','=','room.room_id')
                            ->where('electricity_bill.room_id',$roomId)
                            ->where('electricity_bill.status_payment','=',0)
                            ->where('room_status','สำเร็จ')
                            ->orderBy('electricity_year')
                            ->orderBy('electricity_month')
                            ->get();

            $tranformData = [];
            
            foreach($billData as $item){
                $tranformData[] = [
                    "month" => $monthArr[$item->electricity_month],
                    "year" => intval($item->electricity_year) + 543,
                    "total" => $item->total,
                    "trash_amount" => $item->trash_amount,
                    "electricity_amount" => $item->electricity_amount,
                    "water_amount" => $item->water_amount,
                ];
            }

            $overdueBalance = collect($billData)->sum('total');


            $pdf = PDF::loadView('overdue_monthly_bill',['overdue_bill_data' => $tranformData,'room_data'=> $roomData,'overdue_balance' => $overdueBalance]);

            Storage::put("public/overdue_pdf/overdue_monthly_bill_{$roomId}.pdf", $pdf->output());
        }

        $zip = new ZipArchive;
        $dateNow = date('Y_m_d');
        $fileName = "overdue_monthly_bill_{$dateNow}.zip";
        if ($zip->open(public_path($fileName), ZipArchive::CREATE) === TRUE)
        {
            $files = \File::files(storage_path('app/public/overdue_pdf'));
            foreach ($files as $key => $value) {
                $relativeNameInZipFile = basename($value);
                $zip->addFile($value, $relativeNameInZipFile);
            }
            $zip->close();
        }

        // ลบไฟล์ที่ทำการ zip แล้ว
        $this->deleteDir(storage_path('app/public/overdue_pdf'));

        $headers = [
            'Content-Type' => 'application/zip',
        ];
        return response()->download(public_path($fileName), $fileName, $headers)->deleteFileAfterSend(true);

    }

    public function deleteDir($dir) 
	{ 
        $files = array_diff(scandir($dir), array('.', '..')); 

        foreach ($files as $file) { 
            (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file"); 
        }

        return rmdir($dir); 
	}
}