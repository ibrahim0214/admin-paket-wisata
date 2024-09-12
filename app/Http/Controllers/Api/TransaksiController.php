<?php

namespace App\Http\Controllers\Api;

use App\Transaksi;
use App\TransaksiDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TransaksiController extends Controller
{
    public function store(Request $request){

        $validasi = Validator::make($request->all(), [
            'user_id' => 'required',
            'total_item' => 'required',
            'total_harga' => 'required',
            'name' => 'required',
            'phone' => 'required',
        ]);

        if ($validasi->fails()){
            $val = $validasi->errors()->all();
            return $this->error($val[0]);
        }

        $kode_payment = "INV/PYM/".now()->format('Y-m-d')."/".rand(100,999);
        $kode_trx = "INV/PYM/".now()->format('Y-m-d')."/".rand(100,999);
        $kode_unik = rand(100,999);
        $status = "MENUNGGU";

        $dataTransaksi = array_merge($request->all(), [
            'kode_payment' => $kode_payment,
            'kode_trx' => $kode_trx,
            'kode_unik' => $kode_unik,
            'status' => $status,
        ]);

        \DB::beginTransaction();
        $transaksi = Transaksi::create($dataTransaksi);
        foreach ($request -> pakets as $paket){
            $detail = [
                'transaksi_id' => $transaksi->id,
                'produk_id' => $paket['id'],
                'total_item' => $paket['total_item'],
                'catatan' => $paket['catatan'],
                'total_harga' => $paket['total_harga'],

            ];
            $transaksiDetail = TransaksiDetail::create($detail);
        }

        if(!empty($transaksi) && !empty($transaksiDetail)){
            \DB::commit();
            return response()->json([
                'success' => 1,
                'message' => 'Transaksi Berhasil',
                'transaksi' => collect($transaksi)
            ]);
        } else {
            \DB::rollback();
            $this->error('Transaksi Gagal');
        }
    }

    public function history($id){

        $transaksis = Transaksi::with(['user','details'])->whereHas('user', function($query) use ($id){
            $query->whereId($id);
        })->get();

        foreach($transaksis as $transaksi){
            $details = $transaksi->details;

            foreach ($details as $detail){
                $detail->paket;
            }
        }

        if(!empty($transaksis)){
            return response()->json([
                'success' => 1,
                'message' => 'Transaksi Berhasil',
                'transaksis' => collect($transaksis )
            ]);
        } else {
            $this->error('Transaksi Gagal');
        }

    }

    public function error($pesan){
        return response()->json([
            'success' => 0,
            'message' => $pesan
        ]);   

    } 
}
