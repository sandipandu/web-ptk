<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PtkModel;

class Dashboard extends BaseController
{
    protected $ptkModel;

    public function __construct()
    {
        // Satpam Keamanan Session
        if (!session()->get('sudah_login')) {
            $response = service('response');
            $response->redirect(site_url('auth/login'))->send();
            exit;
        }

        $this->ptkModel = new PtkModel();
    }

    // Menampilkan halaman dashboard utama
    public function index()
    {
        // 1. Data dasar (Count & Sum) untuk render Card atas
        $data['total_ptk']     = $this->ptkModel->countAllResults();
        $data['total_pending'] = $this->ptkModel->where('status_persetujuan', 'Pending')->countAllResults();
        
        $data['stats'] = $this->ptkModel->selectSum('jumlah_kebutuhan', 'total_quota')
                                        ->selectSum('proses_masuk', 'total_proses')
                                        ->selectSum('realisasi', 'total_realisasi')
                                        ->first();

        // Ambil total wilayah kebun yang unik secara langsung untuk card atas
        $totalKebun = $this->ptkModel->select('COUNT(DISTINCT(kebun)) as total')->first();
        $data['total_estates'] = $totalKebun['total'] ?? 0;

        // 2. Ambil semua data untuk Matriks Kebun
        // Logika pengelompokan (Terpenuhi/Belum) & Sorting sekarang sepenuhnya ditangani secara otomatis oleh View
        $semua_data = $this->ptkModel->findAll();
        $matriks = [];
        
        if ($semua_data) {
            foreach ($semua_data as $row) {
                $kebun = !empty($row['kebun']) ? strtoupper(trim($row['kebun'])) : 'TANPA ESTATE';
                
                // Cukup kirimkan Quota dan Realisasi ke View
                $matriks[$kebun][] = [
                    'quota' => (int)($row['jumlah_kebutuhan'] ?? 0),
                    'real'  => (int)($row['realisasi'] ?? 0),
                ];
            }
        }

        $data['matriks_kebun'] = $matriks;

        return view('dashboard', $data);
    }
}