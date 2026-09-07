<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data PTK - PT Lonsum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Mengunci warna dasar background aplikasi agar lebih clean */
        body {
            background-color: #f8fafc;
        }
        /* Penyelarasan style input focus dengan aksen hangat (warning/amber) */
        .form-control:focus, .form-select:focus {
            border-color: #f59e0b !important;
            box-shadow: 0 0 0 0.25rem rgba(245, 158, 11, 0.15) !important;
        }
        /* Card styling */
        .custom-card {
            border-radius: 12px !important;
            border: 1px solid rgba(0,0,0,0.05) !important;
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
                        <h1 class="h3 fw-bold text-dark mb-1">Edit Data PTK</h1>
                        <p class="text-muted small mb-0">Perbarui informasi pengajuan kuota permintaan tenaga kerja kebun yang sudah terdaftar.</p>
                    </div>
                </div>

                <div class="card custom-card shadow-sm mb-5">
                    <div class="card-header bg-warning text-dark py-3 d-flex align-items-center" style="background-color: #f59e0b !important; color: #1e293b !important; border-top-left-radius: 11px; border-top-right-radius: 11px;">
                        <h6 class="mb-0 fw-bold">✏️ Edit Data Pengajuan Kuota Kebun</h6>
                    </div>
                    <div class="card-body p-4 bg-white" style="border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                        
                        <form action="<?= site_url('ptk/update/'.$ptk['id']) ?>" method="POST">
                            <?= csrf_field() ?>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-dark">No. PTK Kebun</label>
                                    <input type="text" name="nomor_dokumen" class="form-control py-2" style="border-radius: 8px;" value="<?= esc($ptk['nomor_dokumen']) ?>" placeholder="Contoh: 396/KBE/HRD/X/2025" required autocomplete="off">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-dark">Estate / Mill Kebun</label>
                                    <input type="text" name="kebun" id="kebun" class="form-control py-2" style="border-radius: 8px;" list="list-kebun" value="<?= esc($ptk['kebun']) ?>" placeholder="Pilih atau ketik kebun baru..." required autocomplete="off" onchange="daftarkanOpsiBaru(this, 'list-kebun')">
                                    
                                    <datalist id="list-kebun">
                                        <?php if(isset($list_kebun_db) && !empty($list_kebun_db)): ?>
                                            <?php foreach($list_kebun_db as $k): ?>
                                                <option value="<?= esc($k['kebun']) ?>"></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </datalist>
                                </div>
                            </div>

                            <hr class="my-4" style="border-color: rgba(0,0,0,0.08);">

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold mb-0" style="color: #d97706; font-size: 16px;">💼 Detail Posisi Jabatan & Quota Kebutuhan</h5>
                            </div>

                            <datalist id="list-jabatan">
                                <?php if(isset($list_jabatan_db) && !empty($list_jabatan_db)): ?>
                                    <?php foreach($list_jabatan_db as $j): ?>
                                        <option value="<?= esc($j['posisi_diminta']) ?>"></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </datalist>

                            <div class="row border rounded-3 p-3 mb-3 align-items-end" style="background-color: #f8fafc; border-color: rgba(0,0,0,0.06) !important;">
                                <div class="col-md-4 mb-2 mb-md-0">
                                    <label class="form-label small fw-bold text-dark">Job (Jabatan + Kode)</label>
                                    <input type="text" name="posisi_diminta" class="form-control" style="border-radius: 8px;" list="list-jabatan" value="<?= esc($ptk['posisi_diminta']) ?>" placeholder="Pilih atau ketik jabatan..." required autocomplete="off" onchange="daftarkanOpsiBaru(this, 'list-jabatan')">
                                </div>
                                
                                <div class="col-md-2 mb-2 mb-md-0">
                                    <label class="form-label small fw-bold text-primary">Jumlah Kebutuhan</label>
                                    <input type="number" name="jumlah_kebutuhan" id="inputPermintaan" class="form-control" style="border-radius: 8px;" value="<?= esc($ptk['jumlah_kebutuhan']) ?>" min="1" placeholder="Contoh: 20" required oninput="kunciMaksimal()">
                                </div>
                                
                                <div class="col-md-2 mb-2 mb-md-0">
                                    <label class="form-label small fw-bold text-warning">Proses (Seleksi)</label>
                                    <input type="number" name="proses_masuk" id="inputProses" class="form-control" style="border-radius: 8px;" value="<?= esc($ptk['proses_masuk']) ?>" min="0" required oninput="kunciMaksimal()">
                                </div>
                                
                                <div class="col-md-2 mb-2 mb-md-0">
                                    <label class="form-label small fw-bold text-success">Terpenuhi (Real)</label>
                                    <input type="number" name="realisasi" id="inputRealisasi" class="form-control" style="border-radius: 8px;" value="<?= esc($ptk['realisasi']) ?>" min="0" required oninput="kunciMaksimal()">
                                </div>

                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-secondary fw-bold w-100" style="border-radius: 8px;" disabled>🔒 Kunci Baris</button>
                                </div>
                            </div>

                            <div class="text-end mt-4 border-top pt-3">
                                <a href="<?= site_url('ptk') ?>" class="btn btn-light px-4 me-2 fw-bold" style="border-radius: 8px; border: 1px solid #cbd5e1;">Batal</a>
                                <button type="submit" class="btn btn-warning px-5 fw-bold shadow-sm py-2 text-dark" style="border-radius: 8px; background-color: #f59e0b; border-color: #f59e0b;">
                                    💾 Update Perubahan
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Menangani pendaftaran otomatis item baru jika data sementara di luar list
        function daftarkanOpsiBaru(inputElement, datalistId) {
            const val = inputElement.value.trim();
            if (val === "") return;

            const datalist = document.getElementById(datalistId);
            const options = datalist.options;
            
            let sudahAda = false;
            for (let i = 0; i < options.length; i++) {
                if (options[i].value.toLowerCase() === val.toLowerCase()) {
                    sudahAda = true;
                    break;
                }
            }

            if (!sudahAda) {
                const newOption = document.createElement('option');
                newOption.value = val;
                datalist.appendChild(newOption);
            }
        }

        // Fungsi Validasi & Kunci Maksimal Input Proses dan Realisasi
        function kunciMaksimal() {
            const inputPermintaan = document.getElementById('inputPermintaan');
            const inputProses = document.getElementById('inputProses');
            const inputRealisasi = document.getElementById('inputRealisasi');

            if (!inputPermintaan || !inputProses || !inputRealisasi) return;

            let maxVal = parseInt(inputPermintaan.value) || 0;
            let prosesVal = parseInt(inputProses.value) || 0;
            let realVal = parseInt(inputRealisasi.value) || 0;

            // Update atribut max secara dinamis
            inputProses.setAttribute('max', maxVal);
            inputRealisasi.setAttribute('max', maxVal);

            // Cek dan potong nilai jika melebihi permintaan
            if (prosesVal > maxVal) inputProses.value = maxVal;
            if (realVal > maxVal) inputRealisasi.value = maxVal;
        }

        // Jalankan sinkronisasi saat pertama kali load halaman
        document.addEventListener("DOMContentLoaded", function() {
            const kebunInput = document.getElementById('kebun');
            if(kebunInput) daftarkanOpsiBaru(kebunInput, 'list-kebun');
            
            const jabatanInput = document.querySelector('input[name="posisi_diminta"]');
            if(jabatanInput) daftarkanOpsiBaru(jabatanInput, 'list-jabatan');

            // Jalankan kunciMaksimal sekali di awal untuk update atribut 'max'
            kunciMaksimal();
        });
    </script>
</body>
</html>