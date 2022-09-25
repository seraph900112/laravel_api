<?php

use App\Models\User;
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
    return 123457489484896;
});



Route::get('/login', function (Request $request) {
    if (auth()->attempt(['name' => $request->name, 'password' => $request->password])) {
        // Authentication passed...
        $user = auth()->user();
        //return ['origin' => $user->api_token, "request"  => $request->token];
        if(! $request->token == $user->api_token ){
            $user->api_token =  $user->createToken('app')->plainTextToken;
        }
        $user->save();
        return $user;
    }
    return response()->json([
        'error' => 'Unauthenticated user',
        'code' => 401,
    ], 401);
});

Route::post('/tokens/create', function (Request $request) {
    $name = $request->string('name');
    $user = User::where('name',$name)->first();
    $token = $user->createToken('app');
    return ['token' => $token->plainTextToken];
});
