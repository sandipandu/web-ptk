<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\HrisRatioModel;
use App\Models\PtkModel;

class HrisRatio extends BaseController
{
    protected $ratioModel;
    protected $ptkModel;

    public function __construct()
    {
        // Pengecekan session login agar aman
        if (!session()->get('sudah_login')) {
            return redirect()->to('auth/login')->send();
        }
        $this->ratioModel = new HrisRatioModel();
        $this->ptkModel = new PtkModel();
    }

    // Menampilkan halaman daftar rasio dan form input
    public function index()
    {
        $data['ratios'] = $this->ratioModel->findAll();
        
        // Mengambil list kebun unik dari data PTK (Urut Abjad) untuk pilihan dropdown
        $data['daftar_kebun'] = $this->ptkModel->distinct()->select('kebun')->orderBy('kebun', 'ASC')->findAll();

        // Mengarahkan ke file view hrisratio/index.php sesuai struktur
        return view('hrisratio/index', $data);
    }

    // Proses simpan data rasio baru / update
    public function store()
    {
        // Validasi disesuaikan dengan field yang dikirim dari form input view
        $rules = [
            'kebun'        => 'required',
            'job_position' => 'required',
            'ratio'        => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Semua kolom master rasio wajib diisi dengan benar!');
        }

        // Menyimpan data master rasio
        $this->ratioModel->save([
            'kebun'        => strtoupper(trim($this->request->getPost('kebun'))),
            'jenis_rasio'  => $this->request->getPost('job_position'), // Menyimpan info Jabatan
            'nilai_rasio'  => $this->request->getPost('ratio'),        // Menyimpan Nilai Rasio Standar
            'keterangan'   => 'Di-input via Master Rasio System',
        ]);

        return redirect()->to(site_url('hrisratio'))->with('success', 'Data Rasio HRIS berhasil disimpan!');
    }

    // Fitur Otomatis: Sinkronisasi Kalkulasi Rasio ke File Monitoring PTK
    public function sync($kebun)
    {
        $kebunClean = strtoupper(trim(rawurldecode($kebun)));
        
        // 1. Ambil semua rasio aktif untuk kebun yang dipilih
        $rasioData = $this->ratioModel->where('kebun', $kebunClean)->first();
        if (!$rasioData) {
            return redirect()->back()->with('error', 'Rasio HRIS untuk kebun ' . $kebunClean . ' belum diMaster / di-input!');
        }

        // 2. Ambil data pengajuan PTK yang berjalan di kebun tersebut
        $dataPtk = $this->ptkModel->where('kebun', $kebunClean)->findAll();

        if (empty($dataPtk)) {
            return redirect()->back()->with('error', 'Tidak ditemukan data monitoring PTK untuk kebun ' . $kebunClean);
        }

        foreach ($dataPtk as $ptk) {
            // Asumsi default variabel pengali luas lahan / target volume, ganti sesuai field riil Anda jika ada
            $variabelPengali = (int)($ptk['target_volume'] ?? 100); 
            
            // Hitung rekomendasi kuota berdasarkan Nilai Rasio HRIS
            $rekomendasiKuota = ceil($variabelPengali * $rasioData['nilai_rasio']);

            // Update otomatis jumlah kebutuhan kuota di database monitoring PTK
            $this->ptkModel->update($ptk['id'], [
                'jumlah_kebutuhan' => $rekomendasiKuota
            ]);
        }

        return redirect()->to(site_url('hrisratio'))->with('success', "Berhasil sinkronisasi & update monitoring kebun $kebunClean berdasarkan Rasio HRIS!");
    }
}