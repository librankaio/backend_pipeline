<?php

namespace App\Http\Controllers;

use App\Models\Pemasaran;
use App\Models\PemasaranActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PemasaranController extends Controller
{
    public function getPemasaran($nik){
        // dd($nik);
        $data = Pemasaran::where('user_nik','=',$nik)->where('status','=','WARM')->get();
        return response()->json($data, 200);
    }

    public function storeRencanaPemasaran(Request $request){
        // dd($request->all());
        // Validate the request
        $validator = Validator::make($request->all(), [
            'pipeline_form_id' => 'required|integer',
             'user_nik'         => 'required',
             'dt_kunjungan'     => 'required',
             'lokasi'           => 'required',
        ]);
         
        //  $pemasaran = Pemasaran::findOrFail($validator['id']);
        //  dd($pemasaran);
        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
         $dt_kunjungan = Carbon::parse($request->dt_kunjungan)->format('Y-m-d H:i:s');

         // Update the stat flag
         PemasaranActivity::create([
             'pipeline_form_id' => $request->pipeline_form_id,
             'user_nik' => $request->user_nik,
             'dt_kunjungan' => $dt_kunjungan,
             'lokasi' => $request->lokasi,
             'stat_rencana' => 'Y'
        ]);
 
        $pemasaran = PemasaranActivity::where('pipeline_form_id','=',$request->pipeline_form_id)->where('stat_rencana','=','Y')->first();
        
        return response()->json([
            'message' => 'Berhasil membuat Rencana Kunjungan',
            'data' => $pemasaran,
            'code' => 200
        ]);
    }

    public function getRencanaPemasaran($nik){
        $data = PemasaranActivity::where('user_nik','=',$nik)->where('stat_rencana','=','Y')->get();
        return response()->json($data, 200);
    }


    public function storeKunjunganPemasaran(Request $request){
        // Validate the request
         $validated = $request->validate([
             'pipeline_form_id' => 'required|integer',
             'user_nik'         => 'required',
             'dt_realisasi'     => 'required',
             'img'              => 'required|image|mimes:jpeg,png,jpg,gif|',
         ]);

         // Find the Pemasaran by ID
         $pemasaran = Pemasaran::findOrFail($validated['id']);

         $dt_realisasi = Carbon::parse($request->dt_realisasi)->format('Y-m-d H:i:s');

         // Update the stat flag
         PemasaranActivity::create([
             'pipeline_form_id' => $request->pipeline_form_id,
             'user_nik' => $request->user_nik,
             'dt_realisasi' => $dt_realisasi,
             'img' => request()->file('img')->getClientOriginalName(),
             'stat_kunjungan' => 'Y'
        ]);
 
        $pemasaran = PemasaranActivity::where('pipeline_form_id','=',$request->pipeline_form_id);
        
        $originalname_img_upload = request('img')->getClientOriginalName();
        $path_upload = "img_kunjungan/$request->pipeline_form_id/";
        request()->file('img')->storeAs($path_upload, $originalname_img_upload);

        return response()->json([
            'message' => 'Berhasil membuat Rencana Kunjungan',
            'data' => $pemasaran,
            'code' => 200
        ]);
     }


    public function getStatistik($nik){
        // dd(request()->all());
        $results = [];

        $count_of_pembinaan = Pemasaran::where('nik','=',$nik)->where('stat_kunjungan','=','Y')->where('stat_perencanaan','=','Y')->where('jenis_kunjungan','=','Pembinaan')->whereBetween('dt_realisasi_kunjungan', [request('dtfrom'), request('dtto')])->count();
        $count_of_penagihan = Pemasaran::where('nik','=',$nik)->where('stat_kunjungan','=','Y')->where('stat_perencanaan','=','Y')->where('jenis_kunjungan','=','Penagihan')->whereBetween('dt_realisasi_kunjungan', [request('dtfrom'), request('dtto')])->count();

        $pembinaan_counts['pembinaan'] = $count_of_pembinaan;
        $penagihan_counts['penagihan'] = $count_of_penagihan;

        array_push($results, [
            'pembinaan' => $count_of_pembinaan, 
            'penagihan' => $count_of_penagihan
        ]);

        // dd($results);

        return response()->json([
            'data'=> $results,
            'code' => 200,
        ]);
    }
}
