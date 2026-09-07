<?php

namespace App\Models;

use CodeIgniter\Model;

class PtkModel extends Model
{
    protected $table            = 'permintaan_tk';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    
    // Kolom tabel yang boleh diisi oleh form
    protected $allowedFields    = [
        'nomor_dokumen', 
        'departemen', 
        'kebun',              // SEKARANG SUDAH ADA
        'posisi_diminta', 
        'jumlah_kebutuhan', 
        'proses_masuk',       // SEKARANG SUDAH ADA
        'realisasi',          // SEKARANG SUDAH ADA
        'alasan_permintaan', 
        'status_persetujuan'
    ];
}