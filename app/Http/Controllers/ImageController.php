<?php

namespace App\Http\Controllers;

use App\Models\Room;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\AttachFile;

class ImageController extends Controller
{
    private $authUser = "Unknow";
    public function __construct()
	{

    }

    public function index(Request $request)
    {
        $items = AttachFile::where(function($query) use($request){
            if($request->has('room_id')){
                $query->whereRoomId($request->room_id);
            }

            if($request->has('other_service_id')){
                $query->whereOtherServiceId($request->other_service_id);
            }

            if($request->has('location_id')){
                $query->whereLocationId($request->location_id);
            }

            if($request->has('reserv_bill')){
                $query->where('reserv_bill',$request->reserv_bill);
            }
        })->get();

        return response()->json($items,200);
    }

    public function store(Request $request)
    {
        if($request->has('not_del_ids')){
            AttachFile::where(function($query) use($request){
                if($request->has('room_id')){
                    $query->whereRoomId($request->room_id);
                }
    
                if($request->has('other_service_id')){
                    $query->whereOtherServiceId($request->other_service_id);
                }
    
                if($request->has('location_id')){
                    $query->whereLocationId($request->location_id);
                }

                if($request->has('reserv_bill')){
                    $query->where('reserv_bill',$request->reserv_bill);
                }
            })
            ->whereNotIn('file_id',explode(",",$request->not_del_ids))
            ->delete();
        }
        
        $files = $request->file('images');
        
        for($i = 0 ; $i < collect($request->allFiles())->count() ;$i++){
            $file = $request->file("image_{$i}");
            Storage::disk('images')->put($file->getClientOriginalName(),file_get_contents($file->getRealPath()));
            
            $path = Storage::disk('images')->path($file->getClientOriginalName());

            $item = new AttachFile;

            $item->file_name = $file->getClientOriginalName();
            $item->file_path = "/images/".$file->getClientOriginalName();
            $item->created_by = $this->authUser;
            
            if($request->has('room_id')){
                $item->room_id = $request->room_id;
            }

            if($request->has('reserv_bill')){
                $item->reserv_bill = $request->reserv_bill;
            }

            if($request->has('other_service_id')){
                $item->other_service_id = $request->other_service_id;
            }

            if($request->has('location_id')){
                $item->location_id = $request->location_id;
            }

            $item->save();
        }
        
        return response()->json(["message" => "Successful"],200);
    }

}