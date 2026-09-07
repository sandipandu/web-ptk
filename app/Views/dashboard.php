<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Monitoring PTK - PT Lonsum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8f9fc;
        }

        /* Override Style Sidebar agar Selaras Korporat */
        .sidebar {
            background-color: #1a202c !important; /* Navy Gelap Premium */
            min-height: 100vh;
        }
        .sidebar .brand-title {
            color: #22c55e !important; /* Hijau Emerald Segar */
            font-weight: 800;
            letter-spacing: 0.5px;
        }
        .sidebar .nav-link.active {
            background-color: #16a34a !important; /* Hijau Lonsum Aktif */
            color: #ffffff !important;
            border-radius: 8px;
        }
        .sidebar .nav-link:not(.active) {
            color: #94a3b8 !important;
        }
        .sidebar .nav-link:not(.active):hover {
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
        }

        /* WARNA KARTU STATISTIK MINIMALIS & FORMAL */
        .card-stat-purple { background-color: #f3e8ff !important; border: 1px solid #e9d5ff !important; }
        .card-stat-purple .stat-title { color: #6b21a8 !important; }
        .card-stat-purple .stat-number { color: #4c1d95 !important; }
        .card-stat-purple .stat-icon { color: #a855f7 !important; opacity: 0.4; }

        .card-stat-blue { background-color: #e0f2fe !important; border: 1px solid #bae6fd !important; }
        .card-stat-blue .stat-title { color: #0369a1 !important; }
        .card-stat-blue .stat-number { color: #0c4a6e !important; }
        .card-stat-blue .stat-icon { color: #0284c7 !important; opacity: 0.4; }

        .card-stat-orange { background-color: #ffedd5 !important; border: 1px solid #fed7aa !important; }
        .card-stat-orange .stat-title { color: #c2410c !important; }
        .card-stat-orange .stat-number { color: #7c2d12 !important; }
        .card-stat-orange .stat-icon { color: #ea580c !important; opacity: 0.4; }

        .card-stat-green { background-color: #dcfce7 !important; border: 1px solid #bbf7d0 !important; }
        .card-stat-green .stat-title { color: #15803d !important; }
        .card-stat-green .stat-number { color: #14532d !important; }
        .card-stat-green .stat-icon { color: #16a34a !important; opacity: 0.4; }

        .p-custom-2-5 { padding: 0.65rem !important; }
        .py-custom-2-5 { padding-top: 0.65rem !important; padding-bottom: 0.65rem !important; }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            
            <?= view('layouts/sidebar') ?>

            <?php 
                // ==========================================================================
                // PROSES PENGOLAHAN DATA OTOMATIS & AUTO-SORTING (3 KATEGORI)
                // ==========================================================================
                $kebun_terpenuhi = [];
                $kebun_proses = [];
                $kebun_belum = [];

                if (!empty($matriks_kebun)) {
                    foreach ($matriks_kebun as $nama_kebun => $daftar_job) {
                        $total_quota = 0;
                        $total_realisasi = 0;

                        foreach ($daftar_job as $job) { 
                            $total_quota += (int)$job['quota']; 
                            $total_realisasi += (int)($job['real'] ?? 0);
                        }

                        $kurang = $total_quota - $total_realisasi;
                        
                        $data_kebun = [
                            'nama'        => $nama_kebun,
                            'total_quota' => $total_quota,
                            'total_real'  => $total_realisasi,
                            'kurang'      => $kurang > 0 ? $kurang : 0
                        ];

                        // Logika Penentuan Status Pemenuhan Kebun Otomatis (3 Kondisi)
                        if ($total_realisasi >= $total_quota && $total_quota > 0) {
                            $kebun_terpenuhi[] = $data_kebun;
                        } elseif ($total_realisasi > 0 && $total_realisasi < $total_quota) {
                            $kebun_proses[] = $data_kebun;
                        } elseif ($total_quota > 0) {
                            $kebun_belum[] = $data_kebun;
                        }
                    }
                }

                // Sorting Otomatis
                // Yang proses dan belum diurutkan dari KURANG TERBANYAK (Minus terbesar)
                usort($kebun_proses, function($a, $b) {
                    return $b['kurang'] <=> $a['kurang']; 
                });
                usort($kebun_belum, function($a, $b) {
                    return $b['kurang'] <=> $a['kurang']; 
                });

                // Yang terpenuhi diurutkan dari Realisasi terbanyak
                usort($kebun_terpenuhi, function($a, $b) {
                    return $b['total_real'] <=> $a['total_real']; 
                });

                // Hitung jumlah otomatis untuk badge
                $count_terpenuhi = count($kebun_terpenuhi);
                $count_proses = count($kebun_proses);
                $count_belum = count($kebun_belum);

                // Kamus Mapping Nama Panjang Kebun Master
                $nama_panjang_kebun = [
                    'RIE' => 'Riam Indah Estate', 'BTE' => 'Budi Tirta Estate', 'SDE' => 'Suka Damai Estate',
                    'KBE' => 'Ketapat Bening Estate', 'SKE' => 'Sei Kepayang Estate', 'GHE' => 'Gunung Hijau Estate',
                    'BRE' => 'Bangun Harjo Estate', 'BHE' => 'Bukit Hijau Estate', 'AME-B' => 'Slemana POM',
                    'AME-D' => 'Turangie POM', 'BCE' => 'Batu Cemerlang Estate', 'BCS' => 'Block Control System',
                    'BEE' => 'Belani Elok Estate', 'BEPOM' => 'Belani Elok POM', 'BPE' => 'Bebah Permata',
                    'GBE' => 'Gunung Bais Estate', 'KCE' => 'Kencana Sari Estate'
                ];
                $get_nama_panjang = function($db_name) use ($nama_panjang_kebun) {
                    $db_name_upper = strtoupper(trim($db_name));
                    foreach ($nama_panjang_kebun as $short => $long) {
                        if (str_contains($db_name_upper, $short)) {
                            return $long;
                        }
                    }
                    return $db_name;
                };
            ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pb-5">       
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show shadow-sm mt-3" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="alert bg-white border border-start border-success border-4 shadow-sm p-4 mt-4 mb-4 rounded-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="fs-4">📊</span>
                            <h2 class="fw-bold text-dark mb-0" style="letter-spacing: -0.5px; font-size: 24px;">Dashboard Kontrol PTK</h2>
                        </div>
                        <p class="text-muted small mb-2">Statistik & Ringkasan Analitis PTK</p>
                        <span class="badge bg-light text-secondary border p-custom-2-5 rounded fw-semibold small" style="font-size: 11px;">
                            📅 <?= date('l, d F Y') ?>
                        </span>
                    </div>

                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <a href="<?= site_url('ptk/export/excel') ?>" class="btn btn-outline-secondary fw-bold shadow-sm rounded-3 btn-sm px-3 py-2">
                            📊 Export ke Excel
                        </a>
                        <button type="button" class="btn btn-outline-primary fw-bold shadow-sm rounded-3 btn-sm px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalImport">
                            📥 Import Excel
                        </button>
                        <a href="<?= site_url('ptk/tambah') ?>" class="btn btn-success fw-bold shadow-sm rounded-3 btn-sm px-4 py-2">
                            ➕ Input PTK Baru
                        </a>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card card-stat-purple border-0 shadow-sm rounded-3">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="stat-title text-uppercase fw-bold small">Total Wilayah Kebun</small>
                                        <h2 class="stat-number fw-bold mb-0 mt-1"><?= !empty($matriks_kebun) ? count($matriks_kebun) : 0 ?> <span class="fs-6 fw-normal opacity-70">Estates</span></h2>
                                    </div>
                                    <div class="stat-icon fs-1">🏞️</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-stat-blue border-0 shadow-sm rounded-3">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="stat-title text-uppercase fw-bold small">Total Job Diajukan</small>
                                        <h2 class="stat-number fw-bold mb-0 mt-1"><?= $total_ptk ?? 0 ?> <span class="fs-6 fw-normal opacity-70">Positions</span></h2>
                                    </div>
                                    <div class="stat-icon fs-1">💼</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-stat-orange border-0 shadow-sm rounded-3">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="stat-title text-uppercase fw-bold small">Total Process Masuk</small>
                                        <h2 class="stat-number fw-bold mb-0 mt-1"><?= $stats['total_proses'] ?? 0 ?> <span class="fs-6 fw-normal opacity-70">Persons</span></h2>
                                    </div>
                                    <div class="stat-icon fs-1">⏳</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-stat-green border-0 shadow-sm rounded-3">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="stat-title text-uppercase fw-bold small">Total Realisasi</small>
                                        <h2 class="stat-number fw-bold mb-0 mt-1"><?= $stats['total_realisasi'] ?? 0 ?> <span class="fs-6 fw-normal opacity-70">Realized</span></h2>
                                    </div>
                                    <div class="stat-icon fs-1">✅</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mt-2">
                    <div class="col-12">
                        
                        <div class="d-flex justify-content-center mb-3">
                            <button class="btn btn-outline-success fw-bold px-4 py-2 shadow-sm rounded-pill d-flex align-items-center gap-2" 
                                    type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#collapseTabelStatus" 
                                    aria-expanded="false" 
                                    aria-controls="collapseTabelStatus">
                                <i class="bi bi-list-check"></i> Lihat Daftar Status Kebun
                            </button>
                        </div>

                        <div class="collapse" id="collapseTabelStatus">
                            <div class="row g-4 pt-2">
                                
                                <div class="col-xl-4 col-lg-12">
                                    <div class="card border-0 shadow-sm rounded-3 p-4 bg-white h-100 border-top border-success border-4">
                                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                            <h6 class="fw-bold text-success mb-0" style="font-size: 14px;">✅ Terpenuhi</h6>
                                            <span class="badge bg-success text-white rounded-pill fw-bold" style="font-size: 11px;"><?= $count_terpenuhi ?> Kebun</span>
                                        </div>
                                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                            <table class="table table-hover align-middle mb-0" style="font-size: 12.5px;">
                                                <thead class="table-light text-muted fw-bold" style="font-size: 11px; position: sticky; top: 0; z-index: 1;">
                                                    <tr>
                                                        <th scope="col" class="py-2 px-3">WILAYAH KEBUN</th>
                                                        <th scope="col" class="py-2 text-end">KUOTA</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($kebun_terpenuhi)): ?>
                                                        <?php foreach ($kebun_terpenuhi as $info): ?>
                                                            <tr>
                                                                <td class="px-3">
                                                                    <span class="fw-bold text-dark">Kebun <?= esc($info['nama']) ?></span>
                                                                    <span class="text-muted small d-block" style="font-size: 11px;"><?= esc($get_nama_panjang($info['nama'])) ?></span>
                                                                </td>
                                                                <td class="text-end fw-bold text-success"><?= $info['total_quota'] ?> org</td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr><td colspan="2" class="text-center text-muted py-3">Belum ada kebun yang terpenuhi 100%.</td></tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-4 col-lg-12">
                                    <div class="card border-0 shadow-sm rounded-3 p-4 bg-white h-100 border-top border-warning border-4">
                                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                            <h6 class="fw-bold text-warning mb-0" style="font-size: 14px;">⏳ Sedang Proses</h6>
                                            <span class="badge bg-warning text-dark rounded-pill fw-bold" style="font-size: 11px;"><?= $count_proses ?> Kebun</span>
                                        </div>
                                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                            <table class="table table-hover align-middle mb-0" style="font-size: 12.5px;">
                                                <thead class="table-light text-muted fw-bold" style="font-size: 11px; position: sticky; top: 0; z-index: 1;">
                                                    <tr>
                                                        <th scope="col" class="py-2 px-3">WILAYAH KEBUN</th>
                                                        <th scope="col" class="py-2 text-center">PROGRESS</th>
                                                        <th scope="col" class="py-2 text-end text-warning">KURANG</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($kebun_proses)): ?>
                                                        <?php foreach ($kebun_proses as $info): ?>
                                                            <tr>
                                                                <td class="px-3">
                                                                    <span class="fw-bold text-dark">Kebun <?= esc($info['nama']) ?></span>
                                                                    <span class="text-muted small d-block" style="font-size: 11px;"><?= esc($get_nama_panjang($info['nama'])) ?></span>
                                                                </td>
                                                                <td class="text-center fw-semibold text-dark">
                                                                    <?= $info['total_real'] ?> <span class="text-muted">/ <?= $info['total_quota'] ?></span>
                                                                </td>
                                                                <td class="text-end fw-bold text-warning">-<?= $info['kurang'] ?> org</td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr><td colspan="3" class="text-center text-muted py-3">Tidak ada data kebun yang sedang diproses.</td></tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-4 col-lg-12">
                                    <div class="card border-0 shadow-sm rounded-3 p-4 bg-white h-100 border-top border-danger border-4">
                                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                            <h6 class="fw-bold text-danger mb-0" style="font-size: 14px;">⚠️ Belum Diproses</h6>
                                            <span class="badge bg-danger text-white rounded-pill fw-bold" style="font-size: 11px;"><?= $count_belum ?> Kebun</span>
                                        </div>
                                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                            <table class="table table-hover align-middle mb-0" style="font-size: 12.5px;">
                                                <thead class="table-light text-muted fw-bold" style="font-size: 11px; position: sticky; top: 0; z-index: 1;">
                                                    <tr>
                                                        <th scope="col" class="py-2 px-3">WILAYAH KEBUN</th>
                                                        <th scope="col" class="py-2 text-end text-danger">KURANG (KUOTA)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($kebun_belum)): ?>
                                                        <?php foreach ($kebun_belum as $info): ?>
                                                            <tr>
                                                                <td class="px-3">
                                                                    <span class="fw-bold text-dark">Kebun <?= esc($info['nama']) ?></span>
                                                                    <span class="text-muted small d-block" style="font-size: 11px;"><?= esc($get_nama_panjang($info['nama'])) ?></span>
                                                                </td>
                                                                <td class="text-end fw-bold text-danger">-<?= $info['kurang'] ?> org</td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr><td colspan="2" class="text-center text-muted py-3">Tidak ada kebun yang belum diproses.</td></tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

            </main>
        </div>
    </div>

    <div class="modal fade" id="modalImport" tabindex="-1" aria-labelledby="modalImportLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-3 border-0 shadow">
                <div class="modal-header bg-success text-white py-3"> 
                    <h5 class="modal-title fw-bold" id="modalImportLabel">📥 Import Data PTK dari Excel</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= site_url('ptk/import') ?>" method="POST" enctype="multipart/form-data">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="file_excel" class="form-label fw-semibold text-secondary">Pilih File Master Excel (.xls, .xlsx)</label>
                            <input class="form-control form-control-lg border-2" type="file" id="file_excel" name="file_excel" accept=".xls,.xlsx" required>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top-0 py-3">
                        <button type="button" class="btn btn-outline-secondary fw-semibold px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success fw-bold px-4 shadow-sm">Mulai Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>