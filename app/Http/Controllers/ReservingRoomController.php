<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\MailController;

class ReservingRoomController extends Controller
{
    private $authUser = "Unknow";
    public function __construct()
	{

    }

    public function index(Request $request)
    {
        $roomChange = collect(Room::whereNotNull('change_room_id')->where('is_active',1)->get(['change_room_id']))
                    ->pluck('change_room_id')->values()->toArray();
        
        $items = Room::where('room_status','=','ว่าง')
                    ->whereNotIn('room_id',$roomChange)
                    ->where('is_active',1)
                    // ->where('effective_date','<=',date('Y-m-d'))
                    ->where(function($query) use($request) {

                    if($request->has('room_id')){
                        $query->whereIn('room_id',$request->room_id);
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
                })->paginate($request->perPage, ['*'], 'page', $request->page);

        return response()->json($items,200);
    }

    public function index_request_leave_change_room(Request $request)
    {
        $authUser = $request->user();

        $items = Room::leftJoin('user','user.user_id','=','room.user_id')
                    ->where('room_status','!=','ว่าง')
                    ->where(function($query) use($request, $authUser) {

                        if($authUser->is_admin == 0){
                            $query->where('room.user_id',$authUser->user_id);
                        }

                        if($request->has('room_id')){
                            $query->whereIn('room_id',$request->room_id);
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

                        if($request->has('room_status') && $request->room_status != 'ทั้งหมด'){
                            $query->where('room_status',$request->room_status);
                        }
                    })->paginate($request->perPage, ['*'], 'page', $request->page);

        return response()->json($items,200);
    }

    public function index_all_requrst_room(Request $request)
    {
        $items = Room::leftJoin('user','user.user_id','=','room.user_id')
                    ->leftJoin('room as r2','r2.room_id','=','room.change_room_id')
                    ->where('room.room_status','!=','ว่าง')
                    ->where('room.room_status','!=','สำเร็จ')
                    ->where(function($query) use($request) {

                        if($request->has('room_id')){
                            $query->whereIn('room.room_id',$request->room_id);
                        }

                        if($request->has('building_name')){
                            $query->whereIn('room.building_name',$request->building_name);
                        }

                        if($request->has('room_type')){
                            $query->whereIn('room.room_type',$request->room_type);
                        }

                        if($request->has('rental_balance')){
                            $query->whereIn('room.rental_balance',$request->rental_balance);
                        }
                    })->paginate(
                        $request->perPage, 
                        ['room.*','user.*',"r2.room_no as change_room_name","r2.building_name as change_building_name"], 
                        'page', 
                        $request->page);

        return response()->json($items,200);
    }

    public function reserv_room(Request $request, $id)
    {
        $item = Room::find($id);

        // $file = $request->file('reserving_image');

        // $extension = $file->getClientOriginalExtension();

        // $fileName = "room_{$id}.{$extension}";

        // $path = 'uploads/reserv_room/';

        // $file->move($path, $filename);

        // $laravel_save_path = url('/').'/'.$path.$filename;
        $item->request_date = $request->request_date;
        $item->reserv_stay_in_date = $request->reserv_stay_in_date;
        $item->room_status = 'จอง';
        $item->user_id = $request->user_id;
        $item->updated_by = $this->authUser;
        // $item->reserving_image = $laravel_save_path;
        $item->save();

        $mailData = Room::leftJoin('user','user.user_id','=','room.user_id')
                        ->where('room.room_id',$id)
                        ->first();

        $mail = new MailController();
        $userAdmins = \App\Models\User::whereIsAdmin(1)->get();

        foreach($userAdmins as $user){
            if($user->email != null){
                $mail->send_mail_reserv($user->email,$mailData);
            }
        }

        return response()->json(["message" => "Successful"],200);
    }

    public function cancel_reserv_room(Request $request, $id)
    {
        $item = Room::find($id);

        $item->room_status = 'ว่าง';
        $item->user_id = null;
        $item->updated_by = $this->authUser;
        $item->save();

        return response()->json(["message" => "Successful"],200);
    }

    public function approve_reserv_room(Request $request, $id)
    {
        $item = Room::find($id);

        if($item->room_status == 'จอง'){
            $item->room_status = 'สำเร็จ';
        }elseif($item->room_status == 'ขอย้ายออก'){
            $item->room_status = 'ว่าง';
            $item->leave_date = null;
            $item->user_id = null;
        }elseif($item->room_status == 'ขอย้ายห้อง'){
            $change_item = Room::find($item->change_room_id);
            $change_item->room_status = 'สำเร็จ';
            $change_item->user_id = $item->user_id;
            $change_item->updated_by = $this->authUser;
            $change_item->save();

            $item->room_status = 'ว่าง';
            $item->user_id = null;
        }

        $item->request_date = null;
        $item->leave_date = null;
        $item->change_room_id = null;
        $item->reserv_stay_in_date = null;
        if( $request->effective_date != null ){
            $item->effective_date = $request->effective_date;
        }else{
            $item->effective_date = date('Y-m-d');
        }
        
        // $item->user_id = null;
        $item->updated_by = $this->authUser;
        $item->save();

        return response()->json(["message" => "Successful"],200);
    }

    public function reject_reserv_room(Request $request, $id)
    {
        $item = Room::find($id);

        if($item->room_status == 'จอง'){
            $item->room_status = 'ว่าง';
            $item->user_id = null;
        }elseif($item->room_status == 'ขอย้ายออก'){
            $item->room_status = 'สำเร็จ';
        }elseif($item->room_status == 'ขอย้ายห้อง'){
            $item->room_status = 'สำเร็จ';
        }

        $item->updated_by = $this->authUser;
        $item->save();

        return response()->json(["message" => "Successful"],200);
    }

    public function leave_room(Request $request, $id)
    {
        $item = Room::find($id);

        $item->room_status = 'ขอย้ายออก';
        $item->request_date = $request->request_date;
        // $item->user_id = null;
        $item->updated_by = $this->authUser;
        $item->leave_date = $request->leave_date;
        $item->save();

        $mailData = Room::leftJoin('user','user.user_id','=','room.user_id')
                        ->where('room.room_id',$id)
                        ->first();

        $mail = new MailController();

        $userAdmins = \App\Models\User::whereIsAdmin(1)->get();
        foreach($userAdmins as $user){
            if($user->email != null)
                $mail->send_mail_leave($user->email,$mailData);
        }

        return response()->json(["message" => "Successful"],200);
    }

    public function change_room(Request $request, $id)
    {
        $item = Room::find($id);

        $item->room_status = 'ขอย้ายห้อง';
        $item->request_date = $request->request_date;
        $item->reserv_stay_in_date = $request->reserv_stay_in_date;
        // $item->user_id = null;
        $item->change_room_id = $request->change_room_id;
        $item->updated_by = $this->authUser;
        $item->save();

        return response()->json(["message" => "Successful"],200);
    }
   
}