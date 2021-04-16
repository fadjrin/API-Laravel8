<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Exception;

class AuthController extends Controller
{
	public function login(Request $request)
	{
		$validator = $this->validateLogin($request); 

		if (!$validator->fails()) {
	        if (!auth()->attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
	            return response()->json([
	                'status'  => false,
	                'msg' => 'Gagal login.',
	                'data' => []
	            ])->setStatusCode(400);		
	        }

            return response()->json([
                'status'  => true,
                'msg' => 'Sukses login',
                'data' => [
                	'user' => auth()->user(),
                	'access_token' => auth()->user()->createToken('authToken')->accessToken
                ]
            ]);	
		}else{
            $messages = $validator->errors()->all('<li>:message</li>');
            return response()->json([
                'status'  => false,
                'msg' => '<ul>'.implode('', $messages).'</ul>',
                'data' => []
            ])->setStatusCode(400);									
		} 			
	}

	public function register(Request $request)
	{
		$validator = $this->validateRegister($request); 

		if (!$validator->fails()) {
			try{
				$user = User::create([
					'email' => $request->input('email'),
					'password' => Hash::make($request->input('password')),
					'name' => $request->input('name'),
				]);

	            return response()->json([
	                'status'  => true,
	                'msg' => 'Sukses pendaftaran user',
	                'data' => [
	                	'user' => $user,
	                	'access_token' => $user->createToken('authToken')->accessToken
	                ]
	            ])->setStatusCode(201);					
			} catch(Exception $e) {
	            return response()->json([
	                'status'  => false,
	                'msg' => 'Gagal pendaftaran user',
	                'data' => []
	            ])->setStatusCode(400);												
			}
		}else{
            $messages = $validator->errors()->all('<li>:message</li>');
            return response()->json([
                'status'  => false,
                'msg' => '<ul>'.implode('', $messages).'</ul>',
                'data' => []
            ])->setStatusCode(400);												
		}
	}

	protected function validateLogin($request)
	{
        $required['email'] = 'email|required|max:256';   
        $required['password'] = 'required|min:6'; 

		$message['email.required'] = 'Email wajib diinput';
        $message['email.email'] = 'Email wajib sesuai format email';
        $message['email.max'] = 'Email maksimal 256 karakter';

        $message['password.required'] = 'Password wajib diinput';
        $message['password.min'] = 'Password minimal 6 karakter';              

        return Validator::make($request->all(), $required, $message);		
	}

	protected function validateRegister($request)
	{
		$required['name'] = 'required|max:256';   
        $required['email'] = 'email|required|unique:users|max:256';   
        $required['password'] = 'required|min:6'; 

        $message['name.required'] = 'Name wajib diinput';
        $message['name.max'] = 'Name maksimal 256 karakter';        

        $message['email.required'] = 'Email wajib diinput';
        $message['email.email'] = 'Email wajib sesuai format email';
        $message['email.unique'] = 'Email sudah terdaftar';
        $message['email.max'] = 'Email maksimal 256 karakter';

        $message['password.required'] = 'Password wajib diinput';
        $message['password.min'] = 'Password minimal 6 karakter';              

        return Validator::make($request->all(), $required, $message);		
	}
}
