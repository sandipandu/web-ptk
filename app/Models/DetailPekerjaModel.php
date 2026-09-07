<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailPekerjaModel extends Model
{
    protected $table            = 'detail_pekerja';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['ptk_id', 'nama_pekerja', 'nik', 'status_proses', 'keterangan'];
}