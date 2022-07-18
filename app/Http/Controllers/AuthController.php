<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use PDF;
use Storage;
use ZipArchive;
use Validator;
use Hash;
use Auth;

class AuthController extends Controller
{
    private $authUser = "Unknow";
    public function __construct()
	{

    }

    public function login(Request $request)
    {

        $attr = $request->validate([
            'username' => 'required|string|',
            'password' => 'required|string|'
        ]);

        if (!Auth::attempt($attr)) {
            return response()->json(["message"=>'Credentials not match'], 401);
        }

        return response()->json([
            'token' => auth()->user()->createToken('auth_token')->plainTextToken
        ],200);

    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(["message" => "Token Delete"],200);
    }

    public function register(Request $request)
    {
        
        $validate = User::where('username',$request->username)->count();
       
        if($validate != 0){
            return response()->json(["message"=>"User used to be"],400);
        }

        $item = new User;
        $item->fill($request->all());
        $item->created_by = $this->authUser;
        $item->updated_by = $this->authUser;
        $item->password = bcrypt($request->password);

        $item->save();

        return response()->json($item,201);
    }

    public function forget_password(Request $request)
    {
        
        $item = User::where('username',$request->user_name)
                ->where('tel_number',$request->tel_number)
                ->first();

        if(!$item){
            return response()->json(["message"=>"Can't Reset Password"],400);
        }

        $item->fill($request->all());
        $item->updated_by = $this->authUser;
        $item->password = bcrypt($request->password);

        $item->save();

        return response()->json($item,200);
    }
    
}