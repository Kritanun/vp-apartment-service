<?php

namespace App\Http\Controllers;

use App\Models\Room;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use PDF;
use Storage;

class RoomController extends Controller
{
    private $authUser = "Unknow";
    public function __construct()
	{

    }

    public function index(Request $request)
    {
        $authUser = $request->user();

        $items = Room::where(function($query) use($request, $authUser) {

                    if($request->has('room_id')){
                        $query->whereIn('room_id',$request->room_id);
                    }

                    if($authUser->is_admin == 0){
                        $query->where('user_id',$authUser->user_id)
                            ->where('effective_date','<=',date('Y-m-d'));
                    }

                    if($request->has('building_name')){
                        $query->whereIn('building_name',$request->building_name);
                    }

                    if($request->has('room_type')){
                        $query->whereIn('room_type',$request->room_type);
                    }

                    if($request->has('rental_balance')){
                        $query->whereIn('rental_balance',$request->rental_balance);
                    }

                })->where('is_active',1)
                ->orderBy('building_name')
                ->orderBy('room_no')
                ->paginate($request->perPage, ['*'], 'page', $request->page);

        return response()->json($items,200);
    }

    public function show(Request $request, $id)
    {
        $item = Room::find($id);

        return response()->json($item);
    }

    public function store(Request $request)
    {
        $item = new Room;
        $item->fill($request->all());
        $item->created_by = $this->authUser;
        $item->updated_by = $this->authUser;

        $item->save();

        return response()->json($item,201);
    }

    public function update(Request $request, $id)
    {
        
        $item = Room::find($id);

        if(empty($item)){
            return response()->json(["message" => "Data not found"],404);
        }

        $item->fill($request->all());
        $item->updated_by = $this->authUser;

        $item->save();

        return response()->json($item,200);
    }

    public function destroy(Request $request, $id)
    {
        $item = Room::find($id);

        if(empty($item)){
            return response()->json(["message" => "Data not found"],404);
        }

        $item->is_active = 0;
        $item->save();

        return response()->json(["message" => "Successful"],200);
    }

    public function get_room_summary(Request $request)
    {
        $items = Room::where('is_active',1)->get();

        $response = [
            "all_room" => collect($items)->count(),
            "room_unavailable" => collect($items)->where('room_status','!=','ว่าง')->count(),
            "room_available" => collect($items)->where('room_status','=','ว่าง')->count()
        ];

        return response()->json($response,200);

    }

    public function export(Request $request)
    {
        if(! $request->has('room_id')){
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

        $roomData = Room::leftJoin('user','user.user_id','=','room.user_id')
                    ->find($request->room_id);

        $pdf = PDF::loadView('reserv_bill',
        [
        'room_data'=> $roomData
        ]);

        Storage::put("public/pdf/reserv_bill_{$request->room_id}.pdf", $pdf->output());
        $fileName = "reserv_bill_{$request->room_id}.pdf";
        $pathFile = public_path('storage\pdf\\'.$fileName);

        $headers = [
            'Content-Type' => 'application/pdf',
        ];
        return response()->download($pathFile, $fileName, $headers)->deleteFileAfterSend(true);

    }

    public function update_outstanding_balance(Request $request, $id)
    {
        $item = Room::find($id);

        if(empty($item)){
            return response()->json(["message" => "Data not found"],404);
        }

        $item->outstanding_balance = $request->outstanding_balance;
        $item->updated_by = $this->authUser;

        $item->save();

        return response()->json($item,200);
    }
}