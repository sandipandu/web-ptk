<?php

namespace App\Models;

use CodeIgniter\Model;

class HrisRatioModel extends Model
{
    protected $table            = 'hris_ratios';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['kebun', 'jenis_rasio', 'nilai_rasio', 'keterangan'];
    protected $useTimestamps    = false;
}