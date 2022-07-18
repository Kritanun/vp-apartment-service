<?php

namespace App\Http\Controllers;

use App\Models\OtherService;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;

class OtherServiceController extends Controller
{
    private $authUser = "Unknow";
    public function __construct()
	{

    }

    public function index(Request $request)
    {
        $items = OtherService::leftJoin('attach_file','other_service.other_service_id','=','attach_file.other_service_id')
                    ->orderBy('other_service.created_at','desc')
                    ->get([
                        'other_service.*',
                        'attach_file.file_id',
                        'attach_file.file_path',
                        'attach_file.file_name'
                    ]);

        return response()->json($items,200);
    }

    public function show(Request $request, $id)
    {
        $item = OtherService::find($id);

        return response()->json($item);
    }

    public function store(Request $request)
    {
        $item = new OtherService;
        $item->fill($request->all());
        $item->created_by = $this->authUser;
        $item->updated_by = $this->authUser;

        $item->save();

        return response()->json($item,201);
    }

    public function update(Request $request, $id)
    {
        $item = OtherService::find($id);

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
        $item = OtherService::find($id);

        if(empty($item)){
            return response()->json(["message" => "Data not found"],404);
        }

        $item->delete();

        return response()->json(["message" => "Successful"],200);
    }
}