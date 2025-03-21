<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function login(Request $requset){

        //dd($requset->all());die();

        $user = User::where('email', $requset->email)->first();

        if($user){
            if(password_verify($requset->password, $user->password)){
                return response()->json([
                    'success' => 1,
                    'message' => 'Selamat datang '.$user->name,
                    'user' => $user
                ]);

            } 
            return $this->error('Password salah');
        }

        return $this->error('Email tidak terdaftar');
    }

    public function register(Request $requset){
        $validasi = Validator::make($requset->all(), [
            'name' => 'required',
            'email' => 'required|unique:users',
            'phone' => 'required|unique:users',
            'password' => 'required|min:6'
        ]);

        if($validasi->fails()){
            $val = $validasi->errors()->all();
            return $this->error($val[0]);
        }

        $user = User::create(array_merge($requset->all(), [
            'password' => bcrypt($requset->password)
        ]));

        if($user){
            return response()->json([
                'success' => 1,
                'message' => 'Selamat datang register berhasil',
                'user' => $user
            ]);
        }

        return $this->error('Registrasi gagal');

    }

    public function error($pesan){
        return response()->json([
            'success' => 0,
            'message' => $pesan
        ]);
    }
}
