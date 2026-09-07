<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Rincian PTK Kebun - PT Lonsum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        /* ==========================================================================
           STYLE KHUSUS SAAT CETAK / PRINT (MEDIA PRINT)
           ========================================================================== */
        @media print {
            .btn, .btn-group, .pt-3, .mb-2, .d-print-none, [view="layouts/sidebar"], .sidebar, .modal {
                display: none !important;
            }

            .row, .container-fluid {
                display: block !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            main, .col-md-9, .col-lg-10 {
                width: 100% !important;
                max-width: 100% !important;
                flex: 0 0 100% !important;
                margin-left: 0 !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
                padding-top: 0 !important;
                position: relative !important;
                left: 0 !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }

            .table-dark {
                background-color: #ffffff !important;
                color: #000000 !important;
                border-bottom: 2px solid #000000 !important;
            }
            
            .table-primary, .table-warning, .table-success, .table-danger, .table-light, .bg-light, .bg-white {
                background-color: #ffffff !important;
                color: #000000 !important;
            }

            .badge, .badge-print-total {
                background-color: transparent !important;
                color: #000000 !important;
                padding: 0 !important;
                font-size: 1rem !important;
                font-weight: bold !important;
                border-radius: 0 !important;
            }
            
            .table-bordered th, .table-bordered td, .table-bordered tfoot tr td {
                border: 1px solid #000000 !important;
                color: #000000 !important;
            }
            
            * {
                -webkit-print-color-adjust: economy !important;
                print-color-adjust: economy !important;
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            
            <?= view('layouts/sidebar') ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">

                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                    <div>
                        <h1 class="h2 fw-bold text-dark mb-1">Rincian PTK: Kebun <?= esc($nama_kebun) ?></h1>
                        <p class="text-muted mb-0">Manajemen akumulasi kuota kebutuhan tenaga kerja per jenis jabatan</p>
                    </div>
                    <div class="btn-group shadow-sm d-print-none">
                       
                        <a href="<?= site_url('ptk/tambah/' . urlencode($nama_kebun)) ?>" class="btn btn-success fw-bold">➕ Tambah Posisi</a>
                    </div>
                </div>

                <?php 
                // Inisialisasi variabel hitungan akumulasi total bawah
                $total_quota = 0;
                $total_proses = 0;
                $total_real = 0;
                $total_belum_terpenuhi = 0;

                if (!empty($daftar_job)) {
                    foreach ($daftar_job as $job) {
                        $total_quota += $job['quota'];
                        $total_proses += $job['proses'];
                        $total_real += $job['real'];
                        $total_belum_terpenuhi += $job['belum_terpenuhi'];
                    }
                }
                ?>

                <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-5">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="mb-0 fw-bold text-secondary text-uppercase fs-6">📊 Akumulasi Total per Jenis Jabatan</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered text-center align-middle mb-0">
                                <thead class="table-dark small text-uppercase">
                                    <tr>
                                        <th class="text-start ps-4 py-3">Nama Jabatan / Job</th>
                                        <th class="table-primary text-dark" style="width: 14%;">Total Permintaan</th>
                                        <th class="table-warning text-dark" style="width: 14%;">Total Proses</th>
                                        <th class="table-success text-dark" style="width: 14%;">Total Realisasi</th>
                                        <th class="table-danger text-dark" style="width: 16%;">Total Sisa Kuota</th>
                                        <th style="width: 18%;" class="d-print-none">Aksi Operasional</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($daftar_job)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted bg-white">
                                                Tidak ada posisi jabatan aktif untuk kebun ini.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($daftar_job as $job): ?>
                                            <tr>
                                                <td class="fw-bold text-secondary text-start ps-4">
                                                    <?= esc($job['posisi']) ?>
                                                </td>
                                                <td class="fw-bold text-primary fs-5">
                                                    <?= $job['quota'] ?>
                                                </td>
                                                <td class="fw-bold text-warning fs-5">
                                                    <?= $job['proses'] ?>
                                                </td>
                                                <td class="fw-bold text-success fs-5">
                                                    <?= $job['real'] ?>
                                                </td>
                                                <td class="fw-bold fs-6">
                                                    <?php if ($job['belum_terpenuhi'] <= 0): ?>
                                                        <span class="text-success">✔️ Terpenuhi</span>
                                                    <?php else: ?>
                                                        <span class="text-danger">⚠️ Sisa: <?= $job['belum_terpenuhi'] ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="d-print-none">
                                                    <div class="btn-group shadow-sm" role="group">
                                                        <button type="button" 
                                                                class="btn btn-info text-white btn-sm px-2 fw-bold btn-detail-ptk" 
                                                                data-kebun="<?= esc($nama_kebun) ?>" 
                                                                data-posisi="<?= esc($job['posisi']) ?>"
                                                                title="Lihat Rincian Berkas Dokumen">
                                                            🔍 Detail
                                                        </button>
                                                        <a href="<?= site_url('ptk/delete_by_job/'.urlencode($nama_kebun).'/'.base64_encode($job['posisi'])) ?>" class="btn btn-danger btn-sm px-2" onclick="return confirm('Apakah Anda yakin ingin menghapus seluruh berkas pengajuan posisi <?= esc($job['posisi']); ?> di Kebun ini?')" title="Hapus Semua Data Jabatan Ini">
                                                            🗑️ Hapus
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>

                                <?php if (!empty($daftar_job)): ?>
                                <tfoot class="table-light fw-bold text-dark border-top-2">
                                    <tr style="height: 50px;">
                                        <td class="text-start ps-4 text-uppercase text-dark-500 fs-6">TOTAL KESELURUHAN :</td>
                                        <td class="text-primary fs-5 fw-extrabold"><?= $total_quota ?></td>
                                        <td class="text-warning fs-5 fw-extrabold"><?= $total_proses ?></td>
                                        <td class="text-success fs-5 fw-extrabold"><?= $total_real ?></td>
                                        <td class="fw-bold fs-6">
                                            <?php if ($total_belum_terpenuhi <= 0): ?>
                                                <span class="text-success">✔️ Terpenuhi</span>
                                            <?php else: ?>
                                                <span class="text-danger">⚠️ Sisa: <?= $total_belum_terpenuhi ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="d-print-none"></td>
                                    </tr>
                                </tfoot>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- MODAL DETAIL & EDIT INLINE -->
    <div class="modal fade" id="modalDetailPTK" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold" id="modalDetailLabel">📋 Detail Rincian Berkas No PTK</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered text-center align-middle mb-0" id="tableRincianKonten">
                            <thead class="table-secondary small text-uppercase fw-bold">
                                <tr>
                                    <th class="py-3">No. PTK / Dokumen</th>
                                    <th>Jabatan / Posisi</th>
                                    <th style="width: 12%;">Permintaan</th>
                                    <th style="width: 12%;">Proses</th>
                                    <th style="width: 12%;">Realisasi</th>
                                    <th style="width: 15%;">Status Sisa</th>
                                    <th style="width: 15%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="loadingArea">
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- FOOTER MODAL DENGAN TOMBOL SIMPAN SEMUA -->
                <div class="modal-footer bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-success fw-bold d-none shadow-sm" id="btnSimpanSemua" onclick="simpanSemuaPerubahan()">
                        💾 Simpan Semua Perubahan
                    </button>
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Inisialisasi Instance Modal Bootstrap
        const bootstrapModalDetail = new bootstrap.Modal(document.getElementById('modalDetailPTK'));
        
        let currentKebun = '';
        let currentPosisi = '';

        // 1. EVENT TOMBOL DETAIL UTAMA DIKLIK
        document.querySelectorAll('.btn-detail-ptk').forEach(button => {
            button.addEventListener('click', function() {
                currentKebun = this.getAttribute('data-kebun');
                currentPosisi = this.getAttribute('data-posisi');
                muatDataRincian(currentKebun, currentPosisi);
            });
        });

        // FUNGSI UTAMA UNTUK AMBIL & RENDER DATA RINCIAN BERKAS KE MODAL
        function muatDataRincian(kebun, posisi) {
            const loadingArea = document.getElementById('loadingArea');
            
            // Reset state tombol simpan global setiap kali modal baru dibuka
            document.getElementById('btnSimpanSemua').classList.add('d-none');
            
            loadingArea.innerHTML = `
                <tr>
                    <td colspan="7" class="py-4 text-muted text-center">
                        <div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>
                        Sedang memuat data rincian berkas nomor PTK...
                    </td>
                </tr>`;
            
            bootstrapModalDetail.show();

            fetch(`<?= site_url('ptk/get_rincian_ajax') ?>?kebun=${encodeURIComponent(kebun)}&posisi=${encodeURIComponent(posisi)}`)
                .then(response => response.json())
                .then(data => {
                    loadingArea.innerHTML = '';
                    
                    if (data.length === 0) {
                        loadingArea.innerHTML = `<tr><td colspan="7" class="py-4 text-muted">Tidak ada rincian data ditemukan.</td></tr>`;
                        return;
                    }

                    data.forEach(item => {
                        let sisaKebutuhan = parseInt(item.jumlah_kebutuhan || 0) - parseInt(item.realisasi || 0);
                        let statusBadge = sisaKebutuhan <= 0 
                            ? `<span class="badge bg-success py-2 px-3 rounded-pill">✔️ Terpenuhi</span>`
                            : `<span class="badge bg-danger py-2 px-3 rounded-pill">⚠️ Sisa: ${sisaKebutuhan}</span>`;

                        loadingArea.innerHTML += `
                            <tr id="row-modal-${item.id}">
                                <td class="fw-bold text-dark text-start ps-3">${item.nomor_dokumen || '-'}</td>
                                <td class="text-start ps-3">${item.posisi_dimintu || item.posisi}</td>
                                <td class="fw-bold text-primary data-quota">${item.jumlah_kebutuhan}</td>
                                <td class="fw-bold text-warning data-proses">${item.proses_masuk}</td>
                                <td class="fw-bold text-success data-real">${item.realisasi}</td>
                                <td class="data-status">${statusBadge}</td>
                                <td>
                                    <div class="btn-group shadow-sm aksi-area">
                                        <button type="button" class="btn btn-warning btn-sm text-dark fw-bold" onclick="aktifkanEditInline(${item.id})">✏️ Edit</button>
                                        <a href="<?= site_url('ptk/delete/') ?>${item.id}" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus berkas nomor PTK ini?')" title="Hapus Berkas">🗑️ Hapus</a>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    loadingArea.innerHTML = `<tr><td colspan="7" class="py-4 text-danger fw-bold">Gagal mengambil data. Coba lagi nanti.</td></tr>`;
                });
        }

        // 2. FUNGSI UNTUK MENGUBAH BARIS DATA MENJADI INPUT FORM (INLINE EDIT)
        function aktifkanEditInline(id) {
            const tr = document.getElementById(`row-modal-${id}`);
            
            // Tandai bahwa baris ini sedang di-edit
            tr.setAttribute('data-editing', 'true');
            
            const quotaTd = tr.querySelector('.data-quota');
            const prosesTd = tr.querySelector('.data-proses');
            const realTd = tr.querySelector('.data-real');
            const aksiTd = tr.querySelector('.aksi-area');

            const quotaVal = quotaTd.innerText.trim();
            const prosesVal = prosesTd.innerText.trim();
            const realVal = realTd.innerText.trim();

            // Tambahkan event handler onkeyup dan onchange untuk validasi dinamis
            quotaTd.innerHTML = `<input type="number" class="form-control form-control-sm text-center input-quota fw-bold text-primary" value="${quotaVal}" min="1" onkeyup="kunciMaksimal(${id})" onchange="kunciMaksimal(${id})" style="width: 85px; margin: 0 auto;">`;
            prosesTd.innerHTML = `<input type="number" class="form-control form-control-sm text-center input-proses fw-bold text-warning" value="${prosesVal}" min="0" max="${quotaVal}" onkeyup="kunciMaksimal(${id})" onchange="kunciMaksimal(${id})" style="width: 85px; margin: 0 auto;">`;
            realTd.innerHTML = `<input type="number" class="form-control form-control-sm text-center input-real fw-bold text-success" value="${realVal}" min="0" max="${quotaVal}" onkeyup="kunciMaksimal(${id})" onchange="kunciMaksimal(${id})" style="width: 85px; margin: 0 auto;">`;

            // Ubah tombol aksi hanya menjadi Batal
            aksiTd.innerHTML = `
                <button type="button" class="btn btn-secondary btn-sm fw-bold" onclick="batalEditInline(${id}, ${quotaVal}, ${prosesVal}, ${realVal})" title="Batal Edit baris ini">❌ Batal</button>
            `;

            // Tampilkan tombol Simpan Semua di footer
            document.getElementById('btnSimpanSemua').classList.remove('d-none');
        }

        // 3. FUNGSI UNTUK MENGUNCI INPUT AGAR PROSES/REALISASI TIDAK MELEBIHI PERMINTAAN
        function kunciMaksimal(id) {
            const tr = document.getElementById(`row-modal-${id}`);
            if (!tr) return;

            const inQuota = tr.querySelector('.input-quota');
            const inProses = tr.querySelector('.input-proses');
            const inReal = tr.querySelector('.input-real');

            let maxQ = parseInt(inQuota.value) || 0;
            let valP = parseInt(inProses.value) || 0;
            let valR = parseInt(inReal.value) || 0;

            // Update atribut max secara dinamis
            inProses.setAttribute('max', maxQ);
            inReal.setAttribute('max', maxQ);

            // Jika input diketik manual melebih batas, langsung potong jadi angka maksimal (permintaan)
            if (valP > maxQ) inProses.value = maxQ;
            if (valR > maxQ) inReal.value = maxQ;
        }

        // 4. FUNGSI UNTUK MEMBATALKAN OPERASI EDIT INLINE
        function batalEditInline(id, q, p, r) {
            const tr = document.getElementById(`row-modal-${id}`);
            
            // Hapus tanda edit pada baris ini
            tr.removeAttribute('data-editing');
            
            const sisa = q - r;
            
            tr.querySelector('.data-quota').innerText = q;
            tr.querySelector('.data-proses').innerText = p;
            tr.querySelector('.data-real').innerText = r;
            
            const statusBadge = sisa <= 0 
                ? `<span class="badge bg-success py-2 px-3 rounded-pill">✔️ Terpenuhi</span>`
                : `<span class="badge bg-danger py-2 px-3 rounded-pill">⚠️ Sisa: ${sisa}</span>`;
            tr.querySelector('.data-status').innerHTML = statusBadge;

            tr.querySelector('.aksi-area').innerHTML = `
                <button type="button" class="btn btn-warning btn-sm text-dark fw-bold" onclick="aktifkanEditInline(${id})">✏️ Edit</button>
                <a href="<?= site_url('ptk/delete/') ?>${id}" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus berkas nomor PTK ini?')" title="Hapus Berkas">🗑️ Hapus</a>
            `;

            // Cek apakah masih ada baris lain yang sedang di-edit. Jika tidak ada, sembunyikan tombol Simpan Semua
            if (document.querySelectorAll('tr[data-editing="true"]').length === 0) {
                document.getElementById('btnSimpanSemua').classList.add('d-none');
            }
        }

        // 5. FUNGSI BULK SAVE: KIRIM SEMUA PERUBAHAN SEKALIGUS MENGGUNAKAN PROMISE.ALL
        function simpanSemuaPerubahan() {
            const editingRows = document.querySelectorAll('tr[data-editing="true"]');
            if (editingRows.length === 0) return;

            let promises = [];
            let isAdaYangKosong = false;
            let isLebihDariPermintaan = false;

            // Kumpulkan semua data dari setiap baris yang sedang diedit
            editingRows.forEach(tr => {
                const id = tr.id.replace('row-modal-', '');
                const quotaNew = tr.querySelector('.input-quota').value;
                const prosesNew = tr.querySelector('.input-proses').value;
                const realNew = tr.querySelector('.input-real').value;

                if (quotaNew === '' || prosesNew === '' || realNew === '') {
                    isAdaYangKosong = true;
                }

                // Validasi Double Check sebelum dikirim
                let q = parseInt(quotaNew) || 0;
                let p = parseInt(prosesNew) || 0;
                let r = parseInt(realNew) || 0;

                if (p > q || r > q) {
                    isLebihDariPermintaan = true;
                }

                const formData = new FormData();
                formData.append('id', id);
                formData.append('quota', quotaNew);
                formData.append('proses', prosesNew);
                formData.append('real', realNew);

                // Buat request AJAX per baris tanpa menunggu satu-satu (dikumpulkan ke dalam array Promise)
                const request = fetch(`<?= site_url('ptk/update_ajax') ?>`, {
                    method: 'POST',
                    body: formData
                }).then(response => response.json());

                promises.push(request);
            });

            if (isAdaYangKosong) {
                alert('Peringatan: Semua parameter angka harus diisi!');
                return;
            }

            if (isLebihDariPermintaan) {
                alert('Peringatan: Jumlah Proses dan Realisasi TIDAK BOLEH melebihi jumlah Permintaan!');
                return;
            }

            // Ubah state tombol menjadi loading
            const btnSimpan = document.getElementById('btnSimpanSemua');
            const originalText = btnSimpan.innerHTML;
            btnSimpan.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan Data...`;
            btnSimpan.disabled = true;

            // Eksekusi semua request secara bersamaan (Parallel / Bulk)
            Promise.all(promises)
                .then(results => {
                    // Cek jika ada request yang gagal
                    const failed = results.filter(res => res.status !== 'success');
                    if(failed.length > 0) {
                        alert('Peringatan: Beberapa data gagal disimpan. Halaman akan dimuat ulang untuk sinkronisasi.');
                    }
                    
                    // Selesai -> Tutup modal & Refresh halaman agar hitungan total akurat
                    bootstrapModalDetail.hide();
                    location.reload();
                })
                .catch(error => {
                    console.error('Error saat Bulk Update:', error);
                    alert('Terjadi error koneksi ke server saat menyimpan data.');
                    
                    // Kembalikan tombol seperti semula jika error koneksi
                    btnSimpan.innerHTML = originalText;
                    btnSimpan.disabled = false;
                });
        }
    </script>
</body>
</html>