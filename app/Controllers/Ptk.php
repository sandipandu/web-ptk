<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PtkModel;
// Panggil library PhpSpreadsheet untuk fitur Export & Import
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Ptk extends BaseController
{
    protected $ptkModel;

    public function __construct()
    {
        $this->ptkModel = new PtkModel();
    }

    /**
     * Pengecekan Login Internal Keamanan Controller
     * Catatan: Sangat direkomendasikan menggunakan Filter CodeIgniter 4 untuk skala produksi.
     */
    protected function cekLogin()
    {
        if (!session()->get('sudah_login')) {
            // Menggunakan response redirect bawaan CodeIgniter 4 yang aman bagi session
            response()->redirect(site_url('auth/login'))->send();
            exit();
        }
    }

    // =========================================================================
    // UPDATE FIX: Menyelaraskan hitungan ajuan dan Sorting A-Z
    // =========================================================================
    public function index()
    {
        $this->cekLogin();

        // Menggunakan GROUP BY kebun dan posisi_diminta serta membersihkan spasi liar dengan TRIM()
        // SUM digunakan agar kalkulasi kuota, proses, dan realisasi pada halaman depan tetap akurat terakumulasi
        $semua_data = $this->ptkModel->select('
                TRIM(kebun) as kebun, 
                TRIM(posisi_diminta) as posisi_diminta, 
                MAX(id) as id, 
                MAX(nomor_dokumen) as nomor_dokumen,
                SUM(jumlah_kebutuhan) as jumlah_kebutuhan, 
                SUM(proses_masuk) as proses_masuk, 
                SUM(realisasi) as realisasi
            ')
            ->groupBy('TRIM(kebun), TRIM(posisi_diminta)')
            ->findAll();
            
        $matriks = [];
        
        if ($semua_data) {
            foreach ($semua_data as $row) {
                // Kelompokkan berdasarkan kebun, jika kosong beri nama 'Tanpa Estate'
                $kebun = !empty($row['kebun']) ? $row['kebun'] : 'Tanpa Estate';
                
                $quota  = (int)($row['jumlah_kebutuhan'] ?? 0);
                $proses = (int)($row['proses_masuk'] ?? 0);
                $real   = (int)($row['realisasi'] ?? 0);
                
                // Menata data ringkas per kebun untuk halaman awal index grid
                $matriks[$kebun][] = [
                    'id'              => $row['id'], 
                    'no_ptk'          => $row['nomor_dokumen'] ?? '-',
                    'posisi'          => $row['posisi_diminta'] ?? 'Tanpa Nama Jabatan',
                    'quota'           => $quota,
                    'proses'          => $proses,
                    'real'            => $real,
                    'belum_terpenuhi' => ($quota - $real)
                ];
            }
        }

        // KUNCI PERBAIKAN: Mengurutkan array matriks berdasarkan nama kebun (Key) dari A-Z
        ksort($matriks);

        $data['matriks_kebun'] = $matriks;
        return view('ptk/index', $data);
    }

    // =========================================================================
    // UPDATE: Langsung menampilkan Akumulasi Total per Jenis Jabatan di Kebun
    // =========================================================================
    public function detail($nama_kebun)
    {
        $this->cekLogin();

        // Decode nama kebun dari URL (menghindari masalah spasi dan tanda kurung)
        $nama_kebun_decode = trim(urldecode($nama_kebun));
        
        // Query langsung mengelompokkan (GROUP BY) posisi_diminta dan menjumlahkan totalnya
        $data_kebun = $this->ptkModel->select('
                TRIM(posisi_diminta) as posisi_diminta, 
                SUM(jumlah_kebutuhan) as total_quota, 
                SUM(proses_masuk) as total_proses, 
                SUM(realisasi) as total_realisasi
            ')
            ->where('TRIM(kebun)', $nama_kebun_decode)
            ->groupBy('TRIM(posisi_diminta)')
            ->findAll();
        
        // Proteksi jika nama kebun tidak ditemukan di database
        if (empty($data_kebun)) {
            return redirect()->to(site_url('ptk'))->with('error', 'Data rincian untuk kebun tersebut tidak ditemukan!');
        }

        $daftar_job = [];
        foreach ($data_kebun as $row) {
            $quota  = (int)$row['total_quota'];
            $proses = (int)$row['total_proses'];
            $real   = (int)$row['total_realisasi'];

            $daftar_job[] = [
                'posisi'          => $row['posisi_diminta'] ?? 'Tanpa Nama Jabatan',
                'quota'           => $quota,
                'proses'          => $proses,
                'real'            => $real,
                'belum_terpenuhi' => ($quota - $real)
            ];
        }

        $data['nama_kebun'] = $nama_kebun_decode;
        $data['daftar_job'] = $daftar_job;

        return view('ptk/detail', $data);
    }

    // =========================================================================
    // BARU / AJAX SYNC: Mengambil data mentah rincian per nomor dokumen/PTK
    // FIX SINKRONISASI JABATAN: Menambahkan key alias ganda agar tidak undefined di Modal
    // =========================================================================
    public function get_rincian_ajax()
    {
        $this->cekLogin();

        $kebun = $this->request->getGet('kebun');
        $posisi = $this->request->getGet('posisi');

        // Ambil data detail tanpa GROUP BY untuk di-render ke dalam Pop-up Modal
        $detail_berkas = $this->ptkModel->where([
            'TRIM(kebun)'          => trim($kebun),
            'TRIM(posisi_diminta)' => trim($posisi)
        ])->findAll();

        $data_output = [];
        if (!empty($detail_berkas)) {
            foreach ($detail_berkas as $berkas) {
                $quota  = (int)($berkas['jumlah_kebutuhan'] ?? 0);
                $proses = (int)($berkas['proses_masuk'] ?? 0);
                $real   = (int)($berkas['realisasi'] ?? 0);

                $data_output[] = [
                    'id'              => $berkas['id'],
                    'no_ptk'          => $berkas['nomor_dokumen'] ?? '-',
                    'no_berkas'       => $berkas['nomor_dokumen'] ?? '-', // Alias sinkronisasi view lama
                    'nomor_dokumen'   => $berkas['nomor_dokumen'] ?? '-', // Pasangan deteksi JS key DB
                    
                    // SINKRONISASI JABATAN: Menyediakan alias variasi nama agar JavaScript pembaca objek tidak "undefined"
                    'posisi'          => $berkas['posisi_diminta'] ?? 'Tanpa Nama Jabatan',
                    'posisi_diminta'  => $berkas['posisi_diminta'] ?? 'Tanpa Nama Jabatan', // Pasangan deteksi JS key DB
                    'job'             => $berkas['posisi_diminta'] ?? 'Tanpa Nama Jabatan', // Cadangan properti 'job'
                    
                    'quota'           => $quota,
                    'jumlah_kebutuhan'=> $quota, // Pasangan deteksi JS key DB
                    'proses'          => $proses,
                    'proses_masuk'    => $proses, // Pasangan deteksi JS key DB
                    'real'            => $real,
                    'realisasi'       => $real, // Pasangan deteksi JS key DB
                    'belum_terpenuhi' => ($quota - $real)
                ];
            }
        }

        // Return dalam bentuk JSON agar bisa dibaca Fetch API JavaScript pada View
        return $this->response->setJSON($data_output);
    }

    // =========================================================================
    // FIX UPDATE_AJAX: Menangani pembaruan data real-time baris berkas dari Modal
    // =========================================================================
    public function update_ajax()
    {
        $this->cekLogin();

        // Ambil data kiriman POST dari Fetch API/AJAX
        $id     = $this->request->getPost('id');
        $quota  = $this->request->getPost('quota');
        $proses = $this->request->getPost('proses');
        $real   = $this->request->getPost('real');

        // Validasi input dasar
        if (empty($id) || $quota === null || $proses === null || $real === null) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data input tidak lengkap atau tidak valid.'
            ]);
        }

        // Cari tahu apakah data tersebut eksis di database sebelum di-update
        $cekData = $this->ptkModel->find($id);
        if (!$cekData) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal memperbarui! Data baris berkas tidak ditemukan.'
            ]);
        }

        // Susun struktur update yang disinkronkan dengan nama field database asli
        $dataUpdate = [
            'jumlah_kebutuhan' => (int)$quota,
            'proses_masuk'     => (int)$proses,
            'realisasi'        => (int)$real
        ];

        // Jalankan perintah pembaruan database
        if ($this->ptkModel->update($id, $dataUpdate)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data berkas PTK berhasil diperbarui!'
            ]);
        } else {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan internal saat memperbarui database.'
            ]);
        }
    }

    // =========================================================================
    // FITUR: Menampilkan halaman rincian dokumen detail per item jabatan
    // =========================================================================
    public function rincian_job($nama_kebun_encoded, $nama_job_encoded)
    {
        $this->cekLogin();

        $nama_kebun = trim(urldecode($nama_kebun_encoded));
        // Mengenangkan string Base64 aman ke format nama jabatan asli
        $nama_job   = trim(base64_decode($nama_job_encoded));

        // Cari data berkas peminta yang kebun dan posisinya sama
        $detail_berkas = $this->ptkModel->where([
            'TRIM(kebun)'          => $nama_kebun,
            'TRIM(posisi_diminta)' => $nama_job
        ])->findAll();

        $data_output = [];
        if (!empty($detail_berkas)) {
            foreach ($detail_berkas as $berkas) {
                $quota  = (int)($berkas['jumlah_kebutuhan'] ?? 0);
                $proses = (int)($berkas['proses_masuk'] ?? 0);
                $real   = (int)($berkas['realisasi'] ?? 0);

                $data_output[] = [
                    'id'              => $berkas['id'],
                    'no_berkas'       => $berkas['nomor_dokumen'] ?? '-',
                    'quota'           => $quota,
                    'proses'          => $proses,
                    'real'            => $real,
                    'belum_terpenuhi' => ($quota - $real)
                ];
            }
        }

        return view('ptk/rincian_job', [
            'nama_kebun'    => $nama_kebun,
            'nama_job'      => $nama_job,
            'detail_berkas' => $data_output
        ]);
    }

    // =========================================================================
    // UPDATE FIX: Menghapus data massal kebun aman dari gangguan karakter khusus
    // =========================================================================
    public function delete_kebun($nama_kebun = null)
    {
        $this->cekLogin();

        // 1. Cek pertama: Apakah nama kebun dikirim via Query String (?kebun=...)
        if ($nama_kebun === null) {
            $nama_kebun = $this->request->getGet('kebun');
        }

        // 2. Cek kedua: Jika tidak kosong, bersihkan datanya
        if (!empty($nama_kebun)) {
            $nama_kebun_decode = trim(urldecode($nama_kebun));
        } else {
            // 3. Cadangan Terakhir: Tangkap string URI mentah pasca teks 'delete_kebun/'
            $uri = $this->request->getUri();
            $path = $uri->getPath();
            if (preg_match('/delete_kebun\/(.+)/', $path, $matches)) {
                $nama_kebun_decode = trim(urldecode($matches[1]));
            } else {
                return redirect()->to(site_url('ptk'))->with('error', 'Gagal menghapus! Parameter nama kebun tidak terdeteksi.');
            }
        }

        // Verifikasi apakah data kebun tersebut memang eksis di database
        $cek_data = $this->ptkModel->where('TRIM(kebun)', $nama_kebun_decode)->findAll();

        if (empty($cek_data)) {
            return redirect()->to(site_url('ptk'))->with('error', 'Gagal menghapus! Data kebun "' . $nama_kebun_decode . '" tidak ditemukan di database.');
        }

        // Jalankan perintah hapus massal untuk semua baris yang field 'kebun'-nya cocok
        $this->ptkModel->where('TRIM(kebun)', $nama_kebun_decode)->delete();

        return redirect()->to(site_url('ptk'))->with('sukses', 'Seluruh data pengajuan untuk Kebun "' . $nama_kebun_decode . '" berhasil dihapus dari sistem!');
    }

    // =========================================================================
    // UPDATE: Menghapus seluruh pengajuan berdasarkan kombinasi Kebun & Jabatan tertentu
    // =========================================================================
    public function delete_by_job($nama_kebun_encoded, $nama_job_encoded)
    {
        $this->cekLogin();

        $nama_kebun = trim(urldecode($nama_kebun_encoded));
        $nama_job   = trim(base64_decode($nama_job_encoded));

        // Jalankan perintah hapus massal untuk baris jabatan yang spesifik di kebun ini
        $this->ptkModel->where([
            'TRIM(kebun)'          => $nama_kebun,
            'TRIM(posisi_diminta)' => $nama_job
        ])->delete();

        return redirect()->to(site_url('ptk/detail/'.urlencode($nama_kebun)))->with('sukses', 'Semua data pengajuan untuk jabatan ' . $nama_job . ' sukses dibersihkan!');
    }

    // =========================================================================
    // UPDATE: Menampilkan halaman form tambah PTK baru dengan deteksi kebun otomatis
    // =========================================================================
    public function tambah($nama_kebun = null)
    {
        $this->cekLogin();

        // Tangkap parameter nama kebun jika ada (dari halaman detail), lalu kirim ke view
        $data['kebun_otomatis'] = $nama_kebun ? trim(urldecode($nama_kebun)) : null;

        // --- MENGAMBIL DAFTAR KEBUN DAN JABATAN DARI DATABASE UNTUK DROPDOWN DATALIST ---
        $data['list_kebun_db'] = $this->ptkModel->select('kebun')->distinct()->where('kebun !=', '')->where('kebun IS NOT NULL')->findAll();
        $data['list_jabatan_db'] = $this->ptkModel->select('posisi_diminta')->distinct()->where('posisi_diminta !=', '')->where('posisi_diminta IS NOT NULL')->findAll();

        return view('ptk/tambah', $data);
    }

    // FITUR UTAMA: Menyimpan banyak data pengajuan posisi PTK sekaligus (Multi-Row)
    public function simpan()
    {
        $this->cekLogin();

        $nomor_dokumen     = $this->request->getPost('nomor_dokumen');
        $kebun             = trim($this->request->getPost('kebun'));

        $daftar_posisi    = $this->request->getPost('posisi_diminta');
        $daftar_quota     = $this->request->getPost('jumlah_kebutuhan');
        $daftar_proses    = $this->request->getPost('proses_masuk');
        $daftar_realisasi = $this->request->getPost('realisasi');

        if (is_array($daftar_posisi)) {
            foreach ($daftar_posisi as $index => $posisi) {
                if (!empty($posisi)) {
                    $this->ptkModel->save([
                        'nomor_dokumen'      => $nomor_dokumen,
                        'kebun'              => $kebun,
                        'departemen'         => '', 
                        'alasan_permintaan'  => '', 
                        'posisi_diminta'     => trim($posisi),
                        'jumlah_kebutuhan'   => (int)($daftar_quota[$index] ?? 0),
                        'proses_masuk'       => (int)($daftar_proses[$index] ?? 0),
                        'realisasi'          => (int)($daftar_realisasi[$index] ?? 0),
                        'status_persetujuan' => 'Pending' 
                    ]);
                }
            }
        }

        return redirect()->to(site_url('ptk'))->with('sukses', 'Semua Posisi Job PTK Berhasil Ditambahkan Sekaligus!');
    }

    // Menampilkan halaman form edit berdasarkan ID data yang dipilih
    public function edit($id)
    {
        $this->cekLogin();

        $data['ptk'] = $this->ptkModel->find($id);
        
        if (empty($data['ptk'])) {
            return redirect()->to(site_url('ptk'))->with('error', 'Data pengajuan tidak ditemukan!');
        }
        
        // --- MENGAMBIL DAFTAR KEBUN DAN JABATAN DARI DATABASE UNTUK DROPDOWN DATALIST ---
        $data['list_kebun_db'] = $this->ptkModel->select('kebun')->distinct()->where('kebun !=', '')->where('kebun IS NOT NULL')->findAll();
        $data['list_jabatan_db'] = $this->ptkModel->select('posisi_diminta')->distinct()->where('posisi_diminta !=', '')->where('posisi_diminta IS NOT NULL')->findAll();
        
        return view('ptk/edit', $data);
    }

    // Memperbarui data ke database setelah form edit disubmit
    public function update($id)
    {
        $this->cekLogin();

        if (!$this->ptkModel->find($id)) {
            return redirect()->to(site_url('ptk'))->with('error', 'Data tidak ditemukan!');
        }

        $kebun_lama = trim($this->request->getPost('kebun'));

        $this->ptkModel->update($id, [
            'nomor_dokumen'     => $this->request->getPost('nomor_dokumen'),
            'kebun'             => $kebun_lama,
            'posisi_diminta'    => trim($this->request->getPost('posisi_diminta')),
            'jumlah_kebutuhan'  => (int)$this->request->getPost('jumlah_kebutuhan'),
            'proses_masuk'      => (int)$this->request->getPost('proses_masuk'),
            'realisasi'         => (int)$this->request->getPost('realisasi'),
            'departemen'        => '', 
            'alasan_permintaan' => '', 
        ]);

        return redirect()->to(site_url('ptk/detail/'.urlencode($kebun_lama)))->with('sukses', 'Data PTK Berhasil Diperbarui!');
    }

    // Menghapus baris data posisi jabatan tertentu (Tunggal via detail)
    public function delete($id)
    {
        $this->cekLogin();

        $data = $this->ptkModel->find($id);
        if (!$data) {
            return redirect()->to(site_url('ptk'))->with('error', 'Data gagal dihapus karena tidak ditemukan!');
        }

        $this->ptkModel->delete($id);
        return redirect()->to(site_url('ptk/detail/'.urlencode($data['kebun'])))->with('sukses', 'Data PTK Berhasil Dihapus!');
    }

    // =========================================================================
    // FITUR EXPORT EXCEL DATA MENTAH (BACKUP)
    // =========================================================================
    public function exportExcel()
    {
        $this->cekLogin();

        $semua_ptk = $this->ptkModel->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Atur Header Template
        $sheet->setCellValue('A1', 'NO');
        $sheet->setCellValue('B1', 'NO PTK / DOKUMEN');
        $sheet->setCellValue('C1', 'ESTATE / MILL (KEBUN)');
        $sheet->setCellValue('D1', 'POSISI / JOB');
        $sheet->setCellValue('E1', 'QUOTA (PERMINTAAN)');
        $sheet->setCellValue('F1', 'PROSES (SELEKSI)');
        $sheet->setCellValue('G1', 'REALISASI (TERPENUHI)');

        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        $column = 2; 
        $no = 1;

        foreach ($semua_ptk as $ptk) {
            $sheet->setCellValue('A' . $column, $no++);
            $sheet->setCellValue('B' . $column, $ptk['nomor_dokumen'] ?? '-');
            $sheet->setCellValue('C' . $column, $ptk['kebun'] ?? 'Tanpa Estate');
            $sheet->setCellValue('D' . $column, $ptk['posisi_diminta'] ?? '-');
            $sheet->setCellValue('E' . $column, (int)($ptk['jumlah_kebutuhan'] ?? 0));
            $sheet->setCellValue('F' . $column, (int)($ptk['proses_masuk'] ?? 0));
            $sheet->setCellValue('G' . $column, (int)($ptk['realisasi'] ?? 0));
            
            $column++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Backup_Data_PTK_Lonsum_' . date('Y-m-d_H-i') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    // =========================================================================
    // FITUR IMPORT EXCEL ANTI-BENTROK ID (RESTORE)
    // =========================================================================
    public function importExcel()
    {
        $this->cekLogin();

        $fileExcel = $this->request->getFile('file_excel');

        if (!$fileExcel || !$fileExcel->isValid()) {
            return redirect()->to(site_url('ptk'))->with('error', 'File tidak valid atau tidak ditemukan!');
        }

        $ekstensi = $fileExcel->getClientExtension();
        $file_terbaca = ['xls', 'xlsx'];
        if (!in_array($ekstensi, $file_terbaca)) {
            return redirect()->to(site_url('ptk'))->with('error', 'Format file harus .xls atau .xlsx!');
        }

        $spreadsheet = IOFactory::load($fileExcel->getTempName());
        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        $jumlah_sukses = 0;

        foreach ($sheetData as $index => $row) {
            if ($index == 0) continue; // Skip baris pertama (Header)

            // Validasi data penting: Nomor Dokumen (B) & Kebun (C) tidak boleh kosong
            if (empty($row[1]) || empty($row[2])) {
                continue;
            }

            // Gunakan insert() tanpa menyertakan ID lama agar DB membuat susunan ID Auto-Increment baru yang bersih
            $this->ptkModel->insert([
                'nomor_dokumen'      => $row[1], // Kolom B
                'kebun'              => trim($row[2]), // Kolom C
                'posisi_diminta'     => trim($row[3]), // Kolom D
                'jumlah_kebutuhan'   => (int)($row[4] ?? 0), // Kolom E
                'proses_masuk'       => (int)($row[5] ?? 0), // Kolom F
                'realisasi'          => (int)($row[6] ?? 0), // Kolom G
                'departemen'         => '', 
                'alasan_permintaan'  => '', 
                'status_persetujuan' => 'Pending'
            ]);

            $jumlah_sukses++;
        }

        if ($jumlah_sukses > 0) {
            return redirect()->to(site_url('ptk'))->with('sukses', $jumlah_sukses . ' Data Berhasil Dipulihkan ke Sistem via Excel!');
        } else {
            return redirect()->to(site_url('ptk'))->with('error', 'Tidak ada data valid yang berhasil di-import.');
        }
    }
}