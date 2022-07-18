<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;

class UserController extends Controller
{
    private $authUser = "Unknow";
    public function __construct()
	{

    }

    public function index(Request $request)
    {
        $authUser = $request->user();

        $items = User::leftJoin('room','room.user_id','=','user.user_id')
            ->where(function($query) use($request, $authUser){
                if($request->has('room_id')){
                    $query->whereIn('room.room_id',$request->room_id);
                }

                if($authUser->is_admin == 0){
                    $query->where('user.user_id',$authUser->user_id);
                }

                if($request->has('user_id')){
                    $query->whereIn('user.user_id',$request->user_id);
                }
            })
            ->whereNotNull('user.user_id')
            ->paginate($request->perPage, ['*'], 'page', $request->page);

        return response()->json($items,200);
    }

    public function show(Request $request, $id)
    {
        $item = User::find($id);

        return response()->json($item);
    }

    public function store(Request $request)
    {
        $item = new User;
        $item->fill($request->all());
        $item->created_by = $this->authUser;
        $item->updated_by = $this->authUser;

        $item->save();

        return response()->json($item,201);
    }

    public function update(Request $request, $id)
    {
        $item = User::find($id);

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
        $item = User::find($id);

        if(empty($item)){
            return response()->json(["message" => "Data not found"],404);
        }

        $item->delete();

        return response()->json(["message" => "Successful"],200);
    }
}