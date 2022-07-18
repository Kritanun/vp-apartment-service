<?php

namespace App\Http\Controllers;

use App\Models\FeedBack;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use PDF;
use Storage;
use Illuminate\Support\Facades\Auth;

class FeedBackController extends Controller
{
    private $authUser = "Unknow";
    public function __construct()
	{

    }

    public function index(Request $request)
    {
        $authUser = $request->user();

        $items = FeedBack::where(function($query) use($authUser,$request){
                    if($authUser->is_admin == 0){
                        $query->whereCreatedBy($authUser->user_id);
                    }
                    if($request->has('is_read')){
                        if($request->is_read == 1 || $request->is_read == 0){
                            $query->whereIsRead($request->is_read);
                        }
                    }
                })
                ->orderBy('created_at','DESC')
                ->paginate($request->perPage, ['*'], 'page', $request->page);

        return response()->json($items,200);
    }

    public function show(Request $request, $id)
    {
        $item = FeedBack::find($id);

        return response()->json($item);
    }

    public function store(Request $request)
    {
        $item = new FeedBack;
        $item->fill($request->all());

        $authUser = $request->user();
        $item->created_by = $authUser->user_id;
        $item->updated_by = $authUser->user_id;

        $item->save();

        return response()->json($item,201);
    }

    public function update(Request $request, $id)
    {
        
        $item = FeedBack::find($id);

        if(empty($item)){
            return response()->json(["message" => "Data not found"],404);
        }

        $authUser = $request->user();

        $item->fill($request->all());
        // $item->created_by = $authUser->user_id;
        $item->updated_by = $authUser->user_id;

        $item->save();

        return response()->json($item,201);
    }

    public function destroy(Request $request, $id)
    {
        $item = FeedBack::find($id);

        if(empty($item)){
            return response()->json(["message" => "Data not found"],404);
        }

        $item->delete();

        return response()->json(["message" => "Successful"],200);
    }
    
}