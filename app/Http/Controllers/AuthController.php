<?php

namespace App\Http\Controllers;

use App\Models\Drivers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function __construct()
    {
        //
    }

    public function loginDriver(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->all()], 401
            );
        }


        $email = $request->input('email');
        $password = $request->input('password');
        $fcmToken = $request->input('fcm_token');

        $driver = Drivers::where('email', $email)->first();

        if($driver) {
            if(Hash::check($password, $driver->password)){
                $token = Str::random(32);
                if($fcmToken) {
                    $driver->update([
                        'api_token' => $token,
                        'fcm_token' => $fcmToken
                    ]);
                }else {
                    $driver->update([
                        'api_token' => $token,
                        'fcm_token' => $driver->fcm_token
                    ]);
                }
                return response()->json([
                    'success' => true,
                    'message' => "Login Success!",
                    'data' => [
                        'api_token' => $driver->api_token,
                        'fcm_token' => $driver->fcm_token
                    ]
                    
                ],201);
            }else {
                return response()->json([
                    'success' => false,
                    'message' => "Login Failed! Wrong Password",
                    'data' => null
                ],401);
            }
        }else {
            return response()->json([
                'success' => false,
                'message' => "Login Failed! Email not registered!",
                'data' => null
            ],404);
        }
    }

    public function signUpDriver(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'email' => 'required|unique:drivers',
            'password' => 'required',
            'no_hp' => 'required|unique:drivers',
            'no_kendaraan' => 'required|unique:drivers',
            'foto_stnk' => 'required',
            'foto_ktp' => 'required',
            'foto_formal' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->all()], 401
            );
        }

            if($request->hasFile('foto_stnk') && $request->hasFile('foto_ktp') && $request->hasFile('foto_formal')) {
                $foto_stnk = Str::random(32);
                $foto_ktp = Str::random(32);
                $foto_formal = Str::random(32);
                $destinationPath = storage_path('/storage/app/public/imageupload');
                $request->file('foto_stnk')->move($destinationPath, $foto_stnk.'.'.$request->file('foto_stnk')->getClientOriginalExtension());
                $request->file('foto_ktp')->move($destinationPath, $foto_ktp.'.'.$request->file('foto_ktp')->getClientOriginalExtension());
                $request->file('foto_formal')->move($destinationPath, $foto_formal.'.'.$request->file('foto_formal')->getClientOriginalExtension());


                $data = Drivers::create([
                    'nama' => $request->input('nama'),
                    'email' => $request->input('email'),
                    'password' => Hash::make($request->input('password')),
                    'no_hp' => $request->input('no_hp'),
                    'no_kendaraan' => $request->input('no_kendaraan'),
                    'foto_stnk' => $foto_stnk.'.'.$request->file('foto_stnk')->getClientOriginalExtension(),
                    'foto_ktp' => $foto_ktp.'.'.$request->file('foto_ktp')->getClientOriginalExtension(),
                    'foto_formal' => $foto_formal.'.'.$request->file('foto_formal')->getClientOriginalExtension(),
                    'rating' => 0,0,
                    'status' => 0

                ]);

                if($data) {
                    return response()->json([
                        'success' => true,
                        'message' => "Register is Successfully",
                        'data' => $data
                    ], 201);
                }else {
                    return response()->json([
                        'success' => false,
                        'message' => "Register is not Successfully",
                        'data' => ''
                    ], 400);
                }
            }else {
                return response()->json([
                    'success' => false,
                    'message' => "Register is not Successfully",
                    'data' => ''
                ], 400);
            }
    }
}