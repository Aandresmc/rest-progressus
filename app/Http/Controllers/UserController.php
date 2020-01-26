<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 
use Illuminate\Support\Facades\Auth; 
use Validator;
class UserController extends Controller 
{
public $successStatus = 200;
/** 
* login api 
* 
* @return \Illuminate\Http\Response 
*/ 
public function login(){ 

    $messages = [
        'email.required'  => 'El correo electronico es requerido',
        'password.required'  => 'La contraseña es requerida',
     ];

    $rules = [ 
        'email' => 'required|email',
        'password' => 'required', 
    ];
    $validator = Validator::make(['email' => request('email'), 'password' => request('password')],$rules, $messages);

    if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 

    $user = Auth::user(); 
    $success['token'] =  $user->createToken('MyApp')-> accessToken; 

    return response()->json(['success' => $success], $this-> successStatus); 

    } 
    else{ 
    
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }
        else {
            return response()->json(['error'=> 'Email y/o Contraseña invalida'], 401);
        }
    } 
 }
/** 
* Register api 
* 
* @return \Illuminate\Http\Response 
*/ 
public function register(Request $request) 
{ 
    $messages = [
        'name.required' => 'El nombre es requerido',
        'email.required'  => 'El correo electronico es requerido',
        'password.required'  => 'La contraseña es requerida',
        'c_password.required'  => 'La contraseña de confirmacion es requerida',
        'c_password.regex' => 'La contraseña es insegura',
        'password.regex' => 'La contraseña es insegura',
        'c_password.same' => 'Las contraseñas no coinciden',
     ];

    $rules = [ 
        'name' => 'required', 
        'email' => 'required|email', 
        'password' => 'required|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/', 
        'c_password' => 'required|same:password|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/', 
    ];

    $validator = Validator::make($request->all(),$rules, $messages);
    
    if ($validator->fails()) return response()->json(['error'=>$validator->errors()], 401);            
    
    $input = $request->all(); 
    $input['password'] = bcrypt($input['password']); 
    $user = User::create($input); 
    $success['token'] =  $user->createToken('MyApp')-> accessToken; 
    $success['name'] =  $user->name;
    return response()->json(['success'=>$success], $this-> successStatus); 
}
/** 
* users api 
* 
* @return \Illuminate\Http\Response 
*/ 
public function users() 
{ 
    $users = User::all();
    return response()->json(['success' => $users], $this-> successStatus); 
    } 
}