<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Paket;

class PaketController extends Controller
{
    public function index(){
        //dd($requset->all());die();
        $paket = Paket::all();
        return response()->json([
            'success' => 1,
            'message' => 'Get Paket berhasil',
            'pakets' => $paket
        ]);
    }
}
