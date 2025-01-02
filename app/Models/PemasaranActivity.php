<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemasaranActivity extends Model
{
    use HasFactory;

    protected $table = 'pemasaran_activities';

    protected $primaryKey = 'id';

    protected $foreignKey = 'pipeline_form_id';

    protected $fillable = [
        'pipeline_form_id',
        'img',
        'dt_kunjungan',
        'dt_realisasi',
        'lokasi',
        'stat_rencana',
        'stat_kunjungan'
    ];
}