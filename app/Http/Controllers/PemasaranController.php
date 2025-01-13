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
        $data = Pemasaran::select('id','cabang_pkss','jbtn_pemasar','nama_pemasar','user_nik as nik','dt_period_pipeline','nama_client','tk_pria_client','tk_wanita_client','kab_kota_client','alamat_client','job_client','bid_usaha_client','kepemilikan_client','riwayat_usaha_client','kantor_pusat_client','uker_client','info_negatif_client','riwayat_kerjasama_client','pengambilan_keputusan_client','name_pic_client','phone_client','jabatan_pic_client','fokus_bisnis_client','potensi_bisnis_client','vendor_exist_client','akhir_kerjasama_client','farming_client','jalur_kerjasama_client','media_penawaran_client','dt_perkiraan_kerjasama','legalitas_perusahaan','f_aanwijzing','f_penawaran_tender','penawaran_tender','prakiraan_nilai','komite','top','note_1','note_2','note_3','note_4','note_5','note_check','img1','img2','img3','img4','img5','approval','checked','submitted','approval_pembina','status','reason','reason_kanpus','reason_pembina','dispo_kanpus','dispo_cabang','nik_pembina')->where('user_nik','=',$nik)->where('status','=','WARM')->get();
        
        return response()->json($data, 200);
    }

    public function storeRencanaPemasaran(Request $request){
        // Validate the request
        $validator = Validator::make($request->all(), [
            'pipeline_form_id'  => 'required|integer',
            'nik'         => 'required',
            'nama_client'      => 'required',
            'dt_realisasi_kunjungan'     => 'required',
            'lokasi_kunjungan'           => 'required',
        ]);
        //  $pemasaran = Pemasaran::findOrFail($validator['id']);
        //  dd($pemasaran);
        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $dt_realisasi_kunjungan = Carbon::parse($request->dt_realisasi_kunjungan)->format('Y-m-d H:i:s');

         // Update the stat flag
        if($request->phone_client != null){
            PemasaranActivity::create([
                'pipeline_form_id' => $request->pipeline_form_id,
                'nik' => $request->nik,
                'nama_client' => $request->nama_client,
                'phone_client' => $request->phone_client,
                'dt_kunjungan' => $dt_realisasi_kunjungan,
                'lokasi_kunjungan' => $request->lokasi_kunjungan,
                'stat_perencanaan' => 'Y'
            ]);
        }else{
            PemasaranActivity::create([
                'pipeline_form_id' => $request->pipeline_form_id,
                'nik' => $request->nik,
                'nama_client' => $request->nama_client,
                'dt_kunjungan' => $dt_realisasi_kunjungan,
                'lokasi_kunjungan' => $request->lokasi_kunjungan,
                'stat_perencanaan' => 'Y'
            ]);
        }
 
        $pemasaran = PemasaranActivity::where('pipeline_form_id','=',$request->pipeline_form_id)->where('stat_perencanaan','=','Y')->first();
        
        return response()->json([
            'message' => 'Berhasil membuat Rencana Kunjungan',
            'data' => $pemasaran,
            'code' => 200
        ]);
    }

    public function getRencanaPemasaran($nik){
        $data = PemasaranActivity::where('nik','=',$nik)->where('stat_perencanaan','=','Y')->where('stat_kunjungan','=', NULL)->get();
        return response()->json($data, 200);
    }


    public function storeKunjunganPemasaran(Request $request){
        // Validate the request
        $validated = $request->validate([
            'pipeline_form_id'           => 'required|integer',
            'rencana_id'                 => 'required|integer',
            'img'                        => 'required|image|mimes:jpeg,png,jpg,gif|',
            'dt_realisasi_kunjungan'     => 'required',
            'geo_loc'                    => 'required',
            'alamat'                    => 'required',
        ]);

         // Find the Pemasaran by ID
        $pemasaran = Pemasaran::findOrFail($validated['pipeline_form_id']);

        $dt_realisasi_kunjungan = Carbon::parse($request->dt_realisasi_kunjungan)->format('Y-m-d H:i:s');

        if($request->todo != NULL){
            // Update the stat flag
            PemasaranActivity::where('id', '=', $request->rencana_id)->update([
                'dt_realisasi_kunjungan' => $dt_realisasi_kunjungan,
                'img' => request()->file('img')->getClientOriginalName(),
                'stat_kunjungan' => 'Y',
                'stat_kunjungan' => 'Y',
                'geo_loc' => $request->geo_loc,
                'alamat' => $request->alamat,
                'todo' => $request->todo
            ]);
        }else{
            // Update the stat flag
            PemasaranActivity::where('id', '=', $request->rencana_id)->update([
                'dt_realisasi_kunjungan' => $dt_realisasi_kunjungan,
                'img' => request()->file('img')->getClientOriginalName(),
                'stat_kunjungan' => 'Y',
                'stat_kunjungan' => 'Y',
                'geo_loc' => $request->geo_loc,
                'alamat' => $request->alamat
            ]);
        }

        $pemasaran = PemasaranActivity::where('id','=',$request->rencana_id)->get();
        
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

        $count_of_pemasaran = PemasaranActivity::where('nik','=',$nik)->where('stat_perencanaan','=','Y')->where('stat_kunjungan','=','Y')->whereBetween('dt_realisasi_kunjungan', [request('dtfrom'), request('dtto')])->count();

        $pemasaran_counts['pemasaran'] = $count_of_pemasaran;

        array_push($results, [
            'pemasaran' => $count_of_pemasaran
        ]);

        return response()->json([
            'data'=> $results,
            'code' => 200,
        ]);
    }

    public function getRiwayatPemasaran($nik){
        $data = PemasaranActivity::where('nik','=',$nik)->where('stat_perencanaan','=','Y')->where('stat_kunjungan','=','Y')->get();
        return response()->json($data, 200);
    }
}
