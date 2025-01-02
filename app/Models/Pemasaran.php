<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemasaran extends Model
{
    use HasFactory;

    protected $table = 'pipeline_forms';

    protected $primaryKey = 'id';

    protected $fillable = [
        'cabang_pkss',
        'jbtn_pemasar',
        'nama_pemasar',
        'user_nik',
        'dt_period_pipeline',
        // -------------- FORM 2 ------------------
        'nama_client',
        'tk_pria_client',
        'tk_wanita_client',
        'kab_kota_client',
        'alamat_client',
        'job_client',
        'bid_usaha_client',
        'kepemilikan_client',
        'riwayat_usaha_client',
        'kantor_pusat_client',
        'uker_client',
        'info_negatif_client',
        'riwayat_kerjasama_client',
        'pengambilan_keputusan_client',
        'name_pic_client',
        'phone_client',
        'jabatan_pic_client',
        'fokus_bisnis_client',
        'potensi_bisnis_client',
        'vendor_exist_client',
        'akhir_kerjasama_client',
        'farming_client',
        'jalur_kerjasama_client',
        'media_penawaran_client',
        // -------------- FORM 3 ------------------
        'dt_perkiraan_kerjasama',
        'legalitas_perusahaan',
        'f_aanwijzing',
        'f_penawaran_tender',
        'penawaran_tender',
        'prakiraan_nilai',
        'komite',
        'top',
        'note_1',
        'note_2',
        'note_3',
        'note_4',
        'note_5',
        'note_check',
        'img1',
        'img2',
        'img3',
        'img4',
        'img5',
        // ----------- HIDDEN STATUS -------------
        'approval',
        'checked',
        'submitted',
        'approval_pembina',
        'status',
        'reason',
        'reason_kanpus',
        'reason_pembina',
        'dispo_kanpus',
        'dispo_cabang',
        // ----------- ETC -------------
        'nik_pembina',
    ];
}
