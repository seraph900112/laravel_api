<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Chat;
use App\Models\Post;
use App\Models\post_picture;
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
        if($user->api_token != $request->token){
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

Route::post('/chat', function(Request $request){
    $chat = new Chat();
    $chat->sender_id = $request->sender_id;
    $chat->receive_id = $request->receive_id;
    $chat->content = $request->content;
    $chat->save();
    $chatId = $chat->id;
    $data = DB::select('select * from chat_ where id = ?', [$chatId]);
    event(new \App\Events\Chat($data[0]));
    return 'success!';
    
});

Route::middleware('auth:sanctum')->post('/addpost', function(Request $request){
    $post = new Post();
    $post->posterId = $request->posterId;
    $post->hasText = $request->hasText;
    $post->hasPhoto = $request->hasPhoto;
    $post->text = $request->text;
    $post->save();
    if($request->hasPhoto == '1'){
        $photos = json_decode($request->photo);

        foreach($photos as $pic){
            $post_picture = new post_picture();
            $post_picture->postId = $post->id;
            $post_picture->picture = $pic;
            $post_picture->save();
        }
    }
    return 'success!';
});

Route::middleware('auth:sanctum')->get('/getchat',function(Request $request){
    return DB::table('chat_')
        ->where('receive_id', $request->id)
        ->orderby('created_at','desc')
        ->leftJoin('users','users.id', '=', 'chat_.sender_id')
        ->select('chat_.*', 'users.name','users.id')
        ->get()
        ->unique('sender_id');

});

Route::get('/getpost', function(Request $request){

   return DB::table('post')
   ->leftJoin('post_picture', 'post.id', '=', 'post_picture.postId')
   ->select('post.*', 'post_picture.picture')
   ->orderBy('post.id','asc')
   ->get();

});

Route::middleware('auth:sanctum')->get('getanchat', function(Request $request){
    return DB::table('chat_')
    ->where('sender_id', $request->sender_id)
    ->where('receive_id', $request->receive_id)
    ->orderby('created_at','desc')
    ->get();
});