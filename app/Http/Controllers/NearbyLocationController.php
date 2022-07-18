<?php

namespace App\Http\Controllers;

use App\Models\NearbyLocation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;

class NearbyLocationController extends Controller
{
    private $authUser = "Unknow";
    public function __construct()
	{

    }

    public function index(Request $request)
    {
        $items = NearbyLocation::leftJoin('attach_file','nearby_location.location_id','=','attach_file.location_id')
                    ->orderBy('nearby_location.created_at','desc')
                    ->get([
                        'nearby_location.*',
                        'attach_file.file_id',
                        'attach_file.file_path',
                        'attach_file.file_name'
                    ]);

        return response()->json($items,200);
    }

    public function show(Request $request, $id)
    {
        $item = NearbyLocation::find($id);

        return response()->json($item);
    }

    public function store(Request $request)
    {
        $item = new NearbyLocation;
        $item->fill($request->all());
        $item->created_by = $this->authUser;
        $item->updated_by = $this->authUser;

        $item->save();

        return response()->json($item,201);
    }

    public function update(Request $request, $id)
    {
        $item = NearbyLocation::find($id);

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
        $item = NearbyLocation::find($id);

        if(empty($item)){
            return response()->json(["message" => "Data not found"],404);
        }

        $item->delete();

        return response()->json(["message" => "Successful"],200);
    }
}