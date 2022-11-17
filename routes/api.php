<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['cors:api'])->group( function (){

    Route::get('/mail', 'MailController@test_mail');

    Route::get('/me', function(Request $request){
        
        return response()->json(["user" => $request->user()],200);
    })->middleware('auth:sanctum');

    Route::post('/login', 'AuthController@login');
    Route::post('/register', 'AuthController@register');

    Route::post('/logout', function(Request $request){
        $request->user()->tokens()->delete();
        return response()->json(["message" => "Token Delete"],200);
    })->middleware('auth:sanctum');

    Route::post('/forget_password', 'AuthController@forget_password');

    // Electricity Bill
    Route::get('/electricity_bill', 'ElectricityBillController@index')->middleware('auth:sanctum');
    Route::get('/electricity_bill/{id}', 'ElectricityBillController@show');
    Route::post('/electricity_bill', 'ElectricityBillController@store');
    Route::patch('/electricity_bill/{id}', 'ElectricityBillController@update');
    Route::delete('/electricity_bill/{id}', 'ElectricityBillController@destroy');
    Route::patch('/approve_payment/{id}', 'ElectricityBillController@approve_payment');

    // Overdue Electricity Bill
    Route::get('/electricity_bill_overdue', 'ElectricityBillOverdueController@index')->middleware('auth:sanctum');
    Route::patch('/approve_payment_overdue/{id}', 'ElectricityBillOverdueController@approve_payment_overdue');
    
    // Other Service
    Route::get('/other_service', 'OtherServiceController@index');
    Route::get('/other_service/{id}', 'OtherServiceController@show');
    Route::post('/other_service', 'OtherServiceController@store');
    Route::patch('/other_service/{id}', 'OtherServiceController@update');
    Route::delete('/other_service/{id}', 'OtherServiceController@destroy');

    // Nearby Location
    Route::get('/nearby_location', 'NearbyLocationController@index');
    Route::get('/nearby_location/{id}', 'NearbyLocationController@show');
    Route::post('/nearby_location', 'NearbyLocationController@store');
    Route::patch('/nearby_location/{id}', 'NearbyLocationController@update');
    Route::delete('/nearby_location/{id}', 'NearbyLocationController@destroy');

    // Room
    Route::get('/room', 'RoomController@index')->middleware('auth:sanctum');
    Route::get('/room/summary', 'RoomController@get_room_summary');
    Route::get('/room/{id}', 'RoomController@show');
    Route::post('/room', 'RoomController@store');
    Route::patch('/room/{id}', 'RoomController@update');
    Route::delete('/room/{id}', 'RoomController@destroy');
    Route::patch('/room_outstanding_balance/{id}', 'RoomController@update_outstanding_balance');

    // User
    Route::get('/user', 'UserController@index')->middleware('auth:sanctum');
    Route::get('/user/{id}', 'UserController@show');
    Route::post('/user', 'UserController@store');
    Route::patch('/user/{id}', 'UserController@update');
    Route::delete('/user/{id}', 'UserController@destroy');

    // Reserving Room
    Route::get('/reserving_room', 'ReservingRoomController@index');
    Route::get('/all_requrst_room', 'ReservingRoomController@index_all_requrst_room');
    Route::get('/leave_change_room', 'ReservingRoomController@index_request_leave_change_room')->middleware('auth:sanctum');
    Route::patch('/reserving_room/reserv/{id}', 'ReservingRoomController@reserv_room');
    Route::patch('/reserving_room/cancel_reserv/{id}', 'ReservingRoomController@cancel_reserv_room');
    Route::patch('/reserving_room/approve/{id}', 'ReservingRoomController@approve_reserv_room');
    Route::patch('/reserving_room/reject/{id}', 'ReservingRoomController@reject_reserv_room');
    Route::patch('/reserving_room/leave/{id}', 'ReservingRoomController@leave_room');
    Route::patch('/reserving_room/change_room/{id}', 'ReservingRoomController@change_room');

    // Dropdown
    Route::get('/dropdown/room', 'FilterController@getAllRoom');
    Route::get('/dropdown/building', 'FilterController@getAllBuilding');
    Route::get('/dropdown/room_type', 'FilterController@getRoomtype');
    Route::get('/dropdown/balance_room', 'FilterController@getRentalRoomBalance');
    Route::get('/dropdown/user', 'FilterController@getUserName');
    Route::get('/dropdown/room_status', 'FilterController@getStatus');

    // Images
    Route::get('/upload_images', 'ImageController@index');
    Route::post('/upload_images', 'ImageController@store');

    // Export
    Route::post('/export/monthly_bill', 'ElectricityBillController@export');
    Route::post('/export/overdue_monthly_bill', 'ElectricityBillOverdueController@export');
    Route::post('/export/reserv_bill', 'RoomController@export');

    // Dashboard
    Route::get('/dashboard_monthly_summary', 'DashboardController@dashboard_monthly_summary');
    Route::get('/dashboard_statistics_usage_room', 'DashboardController@dashboard_statistics_usage_room');

    // Feed Back
    Route::get('/feed_back', 'FeedBackController@index')->middleware('auth:sanctum');
    Route::get('/feed_back/{id}', 'FeedBackController@show');
    Route::post('/feed_back', 'FeedBackController@store')->middleware('auth:sanctum');
    Route::patch('/feed_back/{id}', 'FeedBackController@update');
    Route::delete('/feed_back/{id}', 'FeedBackController@destroy');
});
