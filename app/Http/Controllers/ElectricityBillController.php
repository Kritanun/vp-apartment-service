<?php

namespace App\Http\Controllers;

use App\Models\ElectricityBill;
use App\Models\Room;
use App\Http\Controllers\MailController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use PDF;
use Storage;
use ZipArchive;

class ElectricityBillController extends Controller
{
    private $authUser = "Unknow";
    public function __construct()
	{

    }

    public function index(Request $request)
    {
        $authUser = $request->user();

        $items = ElectricityBill::leftJoin('room','electricity_bill.room_id','=','room.room_id')
                            ->where(function($query) use($request, $authUser) {
                                if($request->has('room_id')){
                                    $query->whereIn('room.room_id',$request->room_id);
                                }

                                if($authUser->is_admin == 0){
                                    $query->where('room.user_id',$authUser->user_id);
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
                            })
                            ->paginate($request->perPage, ['*'], 'page', $request->page);

        return response()->json($items,200);
    }

    public function show(Request $request, $id)
    {
        $item = ElectricityBill::find($id);

        return response()->json($item);
    }

    public function store(Request $request)
    {
        $room = Room::find($request->room_id);
        $item = new ElectricityBill;
        $item->fill($request->all());
        $item->total = $room->rental_balance + $request->electricity_amount + $request->trash_amount + $request->water_amount;
        $item->created_by = $this->authUser;
        $item->updated_by = $this->authUser;

        $item->save();

        $mail = new MailController();

        $user = \App\Models\User::find($room);

        if($user && $user->email != null){
            $mail->send_mail_bill($user->email,$this->export_by_id($item->electricity_bill_id));
        }

        return response()->json($item,201);
    }

    public function update(Request $request, $id)
    {
        $item = ElectricityBill::find($id);

        if(empty($item)){
            return response()->json(["message" => "Data not found"],404);
        }

        $item->fill($request->all());
        $item->created_by = $this->authUser;
        $item->updated_by = $this->authUser;

        $item->save();

        return response()->json($item,201);
    }

    public function destroy(Request $request, $id)
    {
        $item = ElectricityBill::find($id);

        if(empty($item)){
            return response()->json(["message" => "Data not found"],404);
        }

        $item->delete();

        return response()->json(["message" => "Successful"],200);
    }

    public function approve_payment(Request $request, $id)
    {
        $item = ElectricityBill::where('electricity_bill_id',$id)->first();
        $item->status_payment = 1;
        $item->save();

        $room = Room::where('room_id',$item->room_id)->update(['outstanding_balance' => null]);


        return response()->json(["message" => "Successful"],200);
    }

    public function export_by_id($id,)
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

        $billData = ElectricityBill::find($id);

        $roomData = Room::find($billData->room_id);

        if(Date('m') != $billData->electricity_month || Date('Y') != $billData->electricity_year){
            $roomData->outstanding_balance = 0;
        }

        $pdf = PDF::loadView('monthly_bill',
            [
                'bill_data' => $billData,
                'room_data'=> $roomData,
                'month'=> $monthArr[$billData->electricity_month],
                "year" => intval($billData->electricity_year) + 543
            ]);

        Storage::disk('public')->put("/monthly_bill_{$billData->room_id}.pdf", $pdf->output());

        // $file = storage_path("app/public/pdf/monthly_bill_{$billData->room_id}.pdf");

        return "monthly_bill_{$billData->room_id}.pdf";

    }

    public function export(Request $request)
    {
        if(! $request->has('export_ids') && ! $request->has('electricity_month') && ! $request->has('electricity_year')){
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

            $billData = ElectricityBill::whereRoomId($roomId)
                            ->where('electricity_month', $request->electricity_month)
                            ->where('electricity_year', $request->electricity_year)
                            ->first();

            $roomData = Room::find($roomId);
            if(Date('m') != $billData->electricity_month || Date('Y') != $billData->electricity_year){
                $roomData->outstanding_balance = 0;
            }

            $pdf = PDF::loadView('monthly_bill',
            [
                'bill_data' => $billData,
                'room_data'=> $roomData,
                'month'=> $monthArr[$request->electricity_month],
                "year" => intval($request->electricity_year) + 543
            ]);

            Storage::put("public/pdf/monthly_bill_{$roomId}.pdf", $pdf->output());
        }

        $zip = new ZipArchive;
        $dateNow = date('Y_m_d');
        $fileName = "monthly_bill_{$dateNow}.zip";
        if ($zip->open(public_path($fileName), ZipArchive::CREATE) === TRUE)
        {
            $files = \File::files(storage_path('app/public/pdf'));
            foreach ($files as $key => $value) {
                $relativeNameInZipFile = basename($value);
                $zip->addFile($value, $relativeNameInZipFile);
            }
            $zip->close();
        }

        // ลบไฟล์ที่ทำการ zip แล้ว
        $this->deleteDir(storage_path('app/public/pdf'));

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