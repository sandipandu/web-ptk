<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tabel PTK - PT Lonsum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
        }
        
        /* STYLE DASAR KARTU KEBUN */
        .kebun-card {
            transition: all 0.25s ease-in-out;
            cursor: pointer;
            border: 1px solid rgba(0,0,0,0.06) !important;
            text-decoration: none;
            border-radius: 12px !important;
        }
        .kebun-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08) !important;
        }

        /* VARIAN WARNA KARTU BERDASARKAN STATUS */
        
        /* 1. Status Proses (Sebagian Terpenuhi) */
        .kebun-proses {
            background-color: #ffffff !important;
        }
        .kebun-proses:hover {
            border-color: #f59e0b !important; /* Warna Border Orange saat dihover (Proses) */
            box-shadow: 0 12px 24px rgba(245, 158, 11, 0.12) !important;
        }

        /* 2. Status Terpenuhi (100% Selesai) */
        .kebun-terpenuhi {
            background-color: #f0fdf4 !important; /* Hijau Pastel Halus */
            border-color: #bbf7d0 !important;
        }
        .kebun-terpenuhi:hover {
            border-color: #16a34a !important; /* Warna Border Hijau saat dihover (Terpenuhi) */
            box-shadow: 0 12px 24px rgba(22, 163, 74, 0.15) !important;
        }

        /* 3. Status Belum Proses (Realisasi 0) */
        .kebun-belum {
            background-color: #fef2f2 !important; /* Merah Pastel Halus */
            border-color: #fecaca !important;
        }
        .kebun-belum:hover {
            border-color: #dc2626 !important; /* Warna Border Merah saat dihover (Belum Proses) */
            box-shadow: 0 12px 24px rgba(220, 38, 38, 0.15) !important;
        }

        /* STYLE TOMBOL DELETE KEBUN */
        .btn-delete-kebun {
            position: relative;
            z-index: 5;
            padding: 0.35rem 0.6rem;
            font-size: 0.9rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            background-color: #f1f5f9;
            color: #64748b;
        }
        .btn-delete-kebun:hover {
            background-color: #fee2e2 !important;
            color: #ef4444 !important;
        }
        .btn-delete-kebun.disabled {
            pointer-events: none;
            opacity: 0.6;
        }
        
        /* SEARCH & MODAL */
        .search-input:focus {
            border-color: #16a34a !important;
            box-shadow: 0 0 0 0.25rem rgba(22, 163, 74, 0.15) !important;
        }
        
        .modal-lonsum .modal-header {
            background-color: #16a34a !important;
            color: #ffffff !important;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            padding: 1rem 1.5rem;
        }
        .modal-lonsum .modal-title {
            font-size: 1.15rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .modal-lonsum .btn-close {
            filter: invert(1) grayscale(1) brightness(2);
            opacity: 0.8;
        }
        .modal-lonsum .btn-close:hover {
            opacity: 1;
        }
        .modal-lonsum .catatan-struktur {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 0.75rem 1rem;
        }
        .modal-lonsum .btn-mulai-upload {
            background-color: #16a34a !important;
            border-color: #16a34a !important;
            color: #ffffff !important;
        }
        .modal-lonsum .btn-mulai-upload:hover {
            background-color: #15803d !important;
            border-color: #15803d !important;
        }
        .modal-lonsum .btn-batal-modal {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            color: #475569;
        }
        .modal-lonsum .btn-batal-modal:hover {
            background-color: #f8fafc;
            color: #334155;
        }

        /* STYLE PAGINATION CUSTOM */
        .pagination .page-link {
            color: #475569;
            border-color: #e2e8f0;
            padding: 0.6rem 1rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .pagination .page-item.active .page-link {
            background-color: #16a34a !important;
            border-color: #16a34a !important;
            color: white !important;
            box-shadow: 0 4px 10px rgba(22, 163, 74, 0.15);
        }
        .pagination .page-link:hover:not(.active) {
            background-color: #f1f5f9;
            color: #16a34a;
        }
        .pagination .page-item.disabled .page-link {
            background-color: #f8fafc;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0 min-vh-100">
            
            <?= view('layouts/sidebar') ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-5 pt-4">
                
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-3 mb-4 border-bottom">
                    <div>
                        <h1 class="h3 fw-bold text-dark mb-1">Daftar Permintaan Tenaga Kerja (PTK)</h1>
                        <p class="text-muted small mb-0">Manajemen berkas pengajuan dan kebutuhan personil di setiap wilayah kebun.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="<?= site_url('ptk/exportExcel') ?>" class="btn btn-outline-primary fw-bold shadow-sm px-3 py-2" style="border-radius: 8px;">
                            <i class="bi bi-download me-1"></i> Export Excel
                        </a>
                        <button type="button" class="btn btn-outline-success fw-bold shadow-sm px-3 py-2" style="border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#modalImportExcel">
                            <i class="bi bi-upload me-1"></i> Import Excel
                        </button>
                        <a href="<?= site_url('ptk/tambah') ?>" class="btn btn-success fw-bold shadow-sm px-4 py-2" style="border-radius: 8px; background-color: #16a34a; border-color: #16a34a;">
                            <i class="bi bi-plus-lg me-1"></i> Input PTK Baru
                        </a>
                    </div>
                </div>

                <?php if (session()->getFlashdata('sukses')) : ?>
                    <div class="alert alert-success border-0 shadow-sm mb-4 p-3 rounded-3" style="background-color: #dcfce7; color: #15803d; font-weight: 500;">
                        <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('sukses') ?>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger border-0 shadow-sm mb-4 p-3 rounded-3" style="background-color: #fee2e2; color: #b91c1c; font-weight: 500;">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <div class="card border-0 shadow-sm rounded-3 p-3 mb-4 bg-white">
                    <div class="row align-items-center">
                        <div class="col-md-6 col-lg-4">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 8px 0 0 8px;">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" id="searchKebun" class="form-control search-input border-start-0 bg-light" placeholder="Cari nama kebun atau nomor PTK..." style="border-radius: 0 8px 8px 0;" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-8 text-md-end mt-2 mt-md-0">
                            <span class="text-muted small fw-medium" id="searchCounter">
                                Menampilkan <?= empty($matriks_kebun) ? 0 : count($matriks_kebun) ?> Estate Area
                            </span>
                        </div>
                    </div>
                </div>

                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-2 row-cols-lg-3 g-4 mb-4" id="kebunGridContainer">
                    
                    <?php if (empty($matriks_kebun)): ?>
                        <div class="col-12 w-100 text-center py-5 text-muted bg-white rounded-3 shadow-sm border" id="emptyPlaceholder">
                            <i class="bi bi-folder-x display-4 d-block mb-3 text-secondary"></i>
                            <p class="fs-5 mb-0 fw-medium">Belum ada data pengajuan kebun yang terekam di database.</p>
                        </div>
                    <?php else: ?>
                        
                        <div class="col-12 w-100 text-center py-5 text-muted bg-white rounded-3 shadow-sm border d-none" id="emptySearchPlaceholder">
                            <i class="bi bi-search display-4 d-block mb-3 text-secondary"></i>
                            <p class="fs-5 mb-0 fw-medium">Data kebun atau nomor dokumen yang Anda cari tidak ditemukan.</p>
                        </div>

                        <?php foreach ($matriks_kebun as $nama_kebun => $daftar_job): ?>
                            <?php 
                                $nomor_tampil = '-';
                                $total_kuota = 0;
                                $total_realisasi = 0;

                                if (!empty($daftar_job)) {
                                    $first_job = $daftar_job[0];
                                    $nomor_tampil = $first_job['no_ptk'] ?? $first_job['nomor_ptk'] ?? $first_job['nomor_dokumen'] ?? $first_job['no_dokumen'] ?? '-';
                                    
                                    // Hitung akumulasi kuota dan realisasi untuk menentukan status kartu
                                    foreach ($daftar_job as $job) {
                                        $total_kuota += (int)($job['jumlah_kebutuhan'] ?? $job['quota'] ?? 0);
                                        $total_realisasi += (int)($job['realisasi'] ?? $job['real'] ?? 0);
                                    }
                                }

                                $nama_kebun_clean = trim($nama_kebun);
                                
                                // LOGIKA 3 STATUS: Terpenuhi, Belum Proses, Proses
                                $isTerpenuhi = ($total_realisasi >= $total_kuota && $total_kuota > 0);
                                $isBelumMulai = ($total_realisasi == 0 && $total_kuota > 0);
                                
                                if ($isTerpenuhi) {
                                    $cardClass = 'kebun-terpenuhi';
                                    $badgeColor = '#16a34a'; // Hijau
                                    $badgeBg = '#dcfce7';
                                    $badgeText = '✅ Terpenuhi';
                                } elseif ($isBelumMulai) {
                                    $cardClass = 'kebun-belum';
                                    $badgeColor = '#dc2626'; // Merah
                                    $badgeBg = '#fee2e2';
                                    $badgeText = '⚠️ Belum Proses';
                                } else {
                                    $cardClass = 'kebun-proses';
                                    $badgeColor = '#ea580c'; // Orange
                                    $badgeBg = '#ffedd5';
                                    $badgeText = '⏳ Proses';
                                }
                            ?>
                            
                            <div class="col kebun-item-card" data-kebun="<?= strtolower(esc($nama_kebun_clean)) ?>" data-dokumen="<?= strtolower(esc(trim($nomor_tampil))) ?>">
                                <div onclick="window.location.href='<?= site_url('ptk/detail/'.urlencode($nama_kebun_clean)) ?>'" class="card h-100 kebun-card <?= $cardClass ?> shadow-sm">
                                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                                        <div>
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <span class="badge px-2.5 py-1.5 small shadow-sm" style="background-color: <?= $badgeBg ?>; color: <?= $badgeColor ?>; font-weight: 700; border-radius: 6px;">
                                                    <?= $badgeText ?>
                                                </span>
                                                
                                                <a href="<?= site_url('ptk/delete_kebun/'.urlencode($nama_kebun_clean)) ?>" 
                                                   class="btn btn-delete-kebun border-0 shadow-sm"
                                                   onclick="event.stopPropagation(); if(this.classList.contains('disabled')) return false; if(confirm('Peringatan Operasional! Menghapus kebun ini akan menghapus semua (<?= count($daftar_job) ?>) posisi job di dalamnya. Anda yakin?')) { this.classList.add('disabled'); return true; } return false;"
                                                   title="Hapus Seluruh Data Kebun <?= esc($nama_kebun_clean) ?>">
                                                    <i class="bi bi-trash3-fill"></i>
                                                </a>
                                            </div>
                                            
                                            <h4 class="fw-bold text-dark mb-0">Kebun <?= esc($nama_kebun_clean) ?></h4>
                                        </div>
                                        
                                        <div class="text-end mt-4 border-top pt-3 d-flex justify-content-between align-items-center">
                                            <span class="badge rounded-pill font-monospace" style="font-size: 0.75rem; background-color: rgba(0,0,0,0.05); color: #475569; padding: 0.5rem 0.75rem;">
                                                <i class="bi bi-briefcase me-1"></i> <?= count($daftar_job) ?> Ajuan
                                            </span>
                                            <span class="fw-bold small" style="font-size: 0.85rem; color: <?= $badgeColor ?>;">
                                                Lihat Detail <i class="bi bi-arrow-right ms-1"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </div>

                <?php if (!empty($matriks_kebun)): ?>
                    <div class="d-flex justify-content-center mb-5 mt-4">
                        <nav aria-label="Navigasi Halaman Kebun">
                            <ul class="pagination shadow-sm" id="paginationControl" style="border-radius: 8px; overflow: hidden;">
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>

            </main>

        </div>
    </div>

    <div class="modal fade modal-lonsum" id="modalImportExcel" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalImportExcelLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <form action="<?= site_url('ptk/importExcel') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="modalImportExcelLabel">
                            <i class="bi bi-file-earmark-excel me-2"></i> Import Data PTK dari Excel
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 pt-4 pb-3">
                        <div class="mb-3">
                            <label for="file_excel" class="form-label small fw-medium text-secondary mb-2">Pilih File Master Excel (.xls, .xlsx)</label>
                            <input class="form-control" type="file" id="file_excel" name="file_excel" accept=".xlsx, .xls" required style="border-radius: 6px; padding: 0.5rem 0.75rem;">
                        </div>

                        <div class="catatan-struktur mb-0 mt-3">
                            <div class="small text-secondary fw-bold mb-1">
                                <i class="bi bi-info-circle me-1 text-success"></i> Catatan Struktur Kolom:
                            </div>
                            <div class="text-muted" style="font-size: 0.8rem; line-height: 1.4;">
                                Pastikan susunan data berkas sesuai dengan berkas master template: Nama Kebun, Posisi/Jabatan, Quota, Proses, dan Realisasi.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pb-4 px-4 pt-2 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-batal-modal fw-medium px-4 py-2" data-bs-dismiss="modal" style="border-radius: 6px; font-size: 0.9rem;">Batal</button>
                        <button type="submit" class="btn btn-mulai-upload fw-bold px-4 py-2" style="border-radius: 6px; font-size: 0.9rem;">Mulai Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchKebun');
            const cards = Array.from(document.querySelectorAll('.kebun-item-card'));
            const paginationControl = document.getElementById('paginationControl');
            const emptySearchPlaceholder = document.getElementById('emptySearchPlaceholder');
            const searchCounter = document.getElementById('searchCounter');
            
            // KONFIGURASI JUMLAH DATA PER HALAMAN
            const itemsPerPage = 6; 
            let currentPage = 1;
            let filteredCards = [...cards]; 

            // Fungsi Inti Merender Tampilan Card per Halaman
            function displayPage(page, isSearching = false) {
                currentPage = page;
                const startIndex = (page - 1) * itemsPerPage;
                const endIndex = startIndex + itemsPerPage;

                // Sembunyikan semua card terlebih dahulu
                cards.forEach(card => card.classList.add('d-none'));

                // Tampilkan hanya card yang masuk dalam rentang halaman saat ini
                const activeItems = filteredCards.slice(startIndex, endIndex);
                activeItems.forEach(card => card.classList.remove('d-none'));

                // Perbarui Tombol Angka Navigasi
                renderPaginationControls();
                
                // Kembalikan scroll ke atas HANYA jika bukan dipicu dari pengetikan kolom pencarian
                if(!isSearching) {
                     window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }

            // Fungsi Membuat Struktur HTML Tombol Pagination
            function renderPaginationControls() {
                if (!paginationControl) return;
                paginationControl.innerHTML = '';

                const totalPages = Math.ceil(filteredCards.length / itemsPerPage);
                if (totalPages <= 1) return; 

                // 1. Tombol Previous
                const prevLi = document.createElement('li');
                prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
                prevLi.innerHTML = `<a class="page-link" href="#" aria-label="Previous">«</a>`;
                if (currentPage !== 1) {
                    prevLi.addEventListener('click', function(e) {
                        e.preventDefault();
                        displayPage(currentPage - 1);
                    });
                }
                paginationControl.appendChild(prevLi);

                // 2. Tombol Angka Halaman
                for (let i = 1; i <= totalPages; i++) {
                    const li = document.createElement('li');
                    li.className = `page-item ${currentPage === i ? 'active' : ''}`;
                    li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                    li.addEventListener('click', function(e) {
                        e.preventDefault();
                        displayPage(i);
                    });
                    paginationControl.appendChild(li);
                }

                // 3. Tombol Next
                const nextLi = document.createElement('li');
                nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
                nextLi.innerHTML = `<a class="page-link" href="#" aria-label="Next">»</a>`;
                if (currentPage !== totalPages) {
                    nextLi.addEventListener('click', function(e) {
                        e.preventDefault();
                        displayPage(currentPage + 1);
                    });
                }
                paginationControl.appendChild(nextLi);
            }

            // Fungsi Filter Pencarian Kebun Terintegrasi dengan Pagination
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const keyword = e.target.value.toLowerCase().trim();
                    
                    // Filter data berdasarkan keyword input
                    filteredCards = cards.filter(function(card) {
                        const namaKebun = card.getAttribute('data-kebun') || '';
                        const noDokumen = card.getAttribute('data-dokumen') || '';
                        return namaKebun.includes(keyword) || noDokumen.includes(keyword);
                    });
                    
                    // Handle tampilan placeholder kosong
                    if (filteredCards.length === 0 && cards.length > 0) {
                        if(emptySearchPlaceholder) emptySearchPlaceholder.classList.remove('d-none');
                    } else if (emptySearchPlaceholder) {
                        emptySearchPlaceholder.classList.add('d-none');
                    }
                    
                    if (searchCounter) {
                        searchCounter.textContent = `Menampilkan ${filteredCards.length} Estate Area`;
                    }

                    // Reset kembali ke halaman 1 dengan flag searching = true (supaya tidak scroll otomatis)
                    displayPage(1, true);
                });
            }

            // Jalankan fungsi inisialisasi awal saat halaman pertama kali dibuka
            displayPage(1);
        });
    </script>
</body>
</html>