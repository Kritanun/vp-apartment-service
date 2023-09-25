<?php

namespace App\Http\Controllers;

use App\Models\ElectricityBill;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;

use App\Models\Room;
use App\Models\User;

class FilterController extends Controller
{
    private $authUser = "Unknow";
    public function __construct()
	{

    }

    public function getAllRoom(Request $request)
    {
        $authUser = $request->user();

        $items = Room::where('is_active',1)
                ->where(function($query) use($authUser){
                    if($authUser->is_admin == 0){
                        $query->whereUserId($authUser->user_id);
                    }
                })
                // ->where('effective_date','<=',date('Y-m-d'))
                ->orderBy('room_no')
                ->get(['room_id','room_no','room_status','building_name']);

        return response()->json($items,200);

    }

    public function getAllBuilding(Request $request)
    {
        $authUser = $request->user();
        $items = Room::where('is_active',1)
                ->where(function($query) use($authUser){
                    if($authUser->is_admin == 0){
                        $query->whereUserId($authUser->user_id);
                    }
                })
                ->groupBy('building_name')
                ->get(['building_name']);

        return response()->json($items,200);
    }

    public function getRoomtype(Request $request)
    {
        $authUser = $request->user();
        $items = Room::where('is_active',1)
                ->where(function($query) use($authUser){
                    if($authUser->is_admin == 0){
                        $query->whereUserId($authUser->user_id);
                    }
                })
                ->groupBy('room_type')
                ->get(['room_type']);

        return response()->json($items,200);
    }

    public function getRentalRoomBalance(Request $request)
    {
        $authUser = $request->user();
        $items = Room::where('is_active',1)
                ->where(function($query) use($authUser){
                    if($authUser->is_admin == 0){
                        $query->whereUserId($authUser->user_id);
                    }
                })
                ->groupBy('rental_balance')
                ->get(['rental_balance']);

        return response()->json($items,200);
    }

    public function getUserName(Request $request)
    {
        $authUser = $request->user();
        $items = User::where(function($query) use($authUser){
            if($authUser->is_admin == 0){
                $query->whereUserId($authUser->user_id);
            }
        })
        ->get();

        return response()->json($items,200);
    }

    public function getStatus(Request $request)
    {
        $authUser = $request->user();
        $items = Room::where('is_active',1)
                    ->where(function($query) use($authUser){
                        if($authUser->is_admin == 0){
                            $query->whereUserId($authUser->user_id);
                        }
                    })
                    ->whereNotIn('room_status',['สำเร็จ','ว่าง'])
                    ->groupBy('room_status')
                    ->get(['room_status']);

        return response()->json($items,200);
    }

    public function getAllRoomAvailable(Request $request)
    {
        $items = Room::where('is_active',1)
                // ->where('effective_date','<=',date('Y-m-d'))
                ->orderBy('room_no')
                ->get(['room_id','room_no','room_status','building_name']);

        return response()->json($items,200);
    }
}