<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan PTK Baru - PT Lonsum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
        }
        .form-control:focus, .form-select:focus {
            border-color: #16a34a !important;
            box-shadow: 0 0 0 0.25rem rgba(22, 163, 74, 0.15) !important;
        }
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
                        <h1 class="h3 fw-bold text-dark mb-1">Form Permintaan Tenaga Kerja Baru</h1>
                        <p class="text-muted small mb-0">Halaman pengisian berkas kuota kebutuhan personil.</p>
                    </div>
                </div>

                <div class="card custom-card shadow-sm mb-4 bg-white border-start border-primary border-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-2">
                            <h6 class="fw-bold text-primary mb-0" style="letter-spacing: 0.3px;">⚡ Scan Dokumen PTK Otomatis</h6>
                        </div>
                        <p class="text-muted small mb-3">Punya file scan atau foto form PTK? Upload di sini agar sistem mengekstrak.</p>
                        
                        <div class="row align-items-center">
                            <div class="col-md-7 mb-2 mb-md-0">
                                <input type="file" id="scan_file" accept=".jpg, .jpeg, .png, .pdf, image/*, application/pdf" class="form-control" style="border-radius: 8px;">
                            </div>
                            <div class="col-md-5">
                                <button type="button" id="btn-scan" class="btn btn-primary fw-bold w-100 shadow-sm py-2" style="border-radius: 8px; background-color: #0254d8; border-color: #0254d8;">
                                    <span id="scan-text">🤖 Upload</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card custom-card shadow-sm mb-5">
                    <div class="card-header bg-success text-white py-3 d-flex align-items-center" style="background-color: #16a34a !important; border-top-left-radius: 11px; border-top-right-radius: 11px;">
                        <h6 class="mb-0 fw-bold">📝 Input Data Pengajuan Tenaga Kerja</h6>
                    </div>
                    <div class="card-body p-4 bg-white" style="border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                        
                        <form action="<?= site_url('ptk/simpan') ?>" method="POST">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-dark">No. PTK Kebun</label>
                                    <input type="text" name="nomor_dokumen" id="nomor_dokumen" class="form-control py-2" style="border-radius: 8px;" placeholder="Contoh: 396/KBE/HRD/X/2025" required autocomplete="off">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-dark">Estate / Mill Kebun</label>
                                    <input type="text" name="kebun" id="kebun" class="form-control py-2" style="border-radius: 8px;" list="list-kebun" placeholder="Pilih atau ketik kebun baru..." value="<?= esc($_GET['kebun'] ?? $kebun_otomatis ?? '') ?>" required autocomplete="off" onchange="daftarkanOpsiBaru(this, 'list-kebun')">
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
                                <h5 class="fw-bold mb-0" style="color: #16a34a; font-size: 16px;">💼 Daftar Posisi Jabatan & Quota Kebutuhan</h5>
                                <button type="button" id="btn-tambah-job" class="btn btn-primary fw-bold btn-sm shadow-sm px-3 py-1.5" style="border-radius: 6px; background-color: #0254d8; border-color: #0254d8;">
                                    ➕ Tambah Baris Jabatan
                                </button>
                            </div>

                            <datalist id="list-jabatan">
                                <?php if(isset($list_jabatan_db) && !empty($list_jabatan_db)): ?>
                                    <?php foreach($list_jabatan_db as $j): ?>
                                        <option value="<?= esc($j['posisi_diminta']) ?>"></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </datalist>

                            <div id="container-job">
                                <div class="row row-job border rounded-3 p-3 mb-3 align-items-end" style="background-color: #f8fafc; border-color: rgba(0,0,0,0.06) !important;">
                                    <div class="col-md-4 mb-2 mb-md-0">
                                        <label class="form-label small fw-bold text-dark">Job (Jabatan + Kode)</label>
                                        <input type="text" name="posisi_diminta[]" class="form-control posisi-job-input" style="border-radius: 8px;" list="list-jabatan" placeholder="Pilih atau ketik jabatan baru..." required autocomplete="off" onchange="daftarkanOpsiBaru(this, 'list-jabatan')">
                                    </div>
                                    <div class="col-md-2 mb-2 mb-md-0">
                                        <label class="form-label small fw-bold text-primary">Jumlah Kebutuhan</label>
                                        <input type="number" name="jumlah_kebutuhan[]" class="form-control input-permintaan" style="border-radius: 8px;" min="0" value="0" required oninput="kunciSemuaBaris()">
                                    </div>
                                    <div class="col-md-2 mb-2 mb-md-0">
                                        <label class="form-label small fw-bold text-warning">Proses (Seleksi)</label>
                                        <input type="number" name="proses_masuk[]" class="form-control input-proses" style="border-radius: 8px;" min="0" value="0" required oninput="kunciSemuaBaris()">
                                    </div>
                                    <div class="col-md-2 mb-2 mb-md-0">
                                        <label class="form-label small fw-bold text-success">Terpenuhi (Real)</label>
                                        <input type="number" name="realisasi[]" class="form-control input-realisasi" style="border-radius: 8px;" min="0" value="0" required oninput="kunciSemuaBaris()">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-danger btn-hapus-row fw-bold w-100" style="border-radius: 8px;" disabled>🗑️ Hapus</button>
                                    </div>
                                </div>
                            </div>

                            <div class="text-end mt-4 border-top pt-3">
                                <button type="reset" class="btn btn-light px-4 me-2 fw-bold" style="border-radius: 8px; border: 1px solid #cbd5e1;">Reset Form</button>
                                <button type="submit" class="btn btn-success px-5 fw-bold shadow-sm py-2" style="border-radius: 8px; background-color: #16a34a; border-color: #16a34a;">
                                    💾 Simpan & Daftarkan Semua PTK
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
        // --- FITUR AUTOMATIS: MEMUAT DATA YANG PERNAH DIKETIK DARI LOCALSTORAGE ---
        document.addEventListener("DOMContentLoaded", function() {
            loadSavedOptions('list-kebun', 'custom_kebun');
            loadSavedOptions('list-jabatan', 'custom_jabatan');
        });

        function loadSavedOptions(datalistId, storageKey) {
            const datalist = document.getElementById(datalistId);
            const savedItems = JSON.parse(localStorage.getItem(storageKey)) || [];
            savedItems.forEach(item => {
                if (!cekApakahSudahAda(datalist, item)) {
                    const newOption = document.createElement('option');
                    newOption.value = item;
                    datalist.appendChild(newOption);
                }
            });
        }

        function cekApakahSudahAda(datalistElement, value) {
            const options = datalistElement.options;
            for (let i = 0; i < options.length; i++) {
                if (options[i].value.toLowerCase() === value.toLowerCase()) {
                    return true;
                }
            }
            return false;
        }

        // --- LOGIKA UTAMA: MENYIMPAN INPUT DATA BARU SECARA PERMANEN DI LOCALSTORAGE ---
        function daftarkanOpsiBaru(inputElement, datalistId) {
            const val = inputElement.value.trim();
            if (val === "") return;

            const datalist = document.getElementById(datalistId);
            
            if (!cekApakahSudahAda(datalist, val)) {
                const newOption = document.createElement('option');
                newOption.value = val;
                datalist.appendChild(newOption);
                
                const storageKey = (datalistId === 'list-kebun') ? 'custom_kebun' : 'custom_jabatan';
                const savedItems = JSON.parse(localStorage.getItem(storageKey)) || [];
                savedItems.push(val);
                localStorage.setItem(storageKey, JSON.stringify(savedItems));
            }
        }

        // --- FITUR BARU: FUNGSI KUNCI MAKSIMAL UNTUK SEMUA BARIS (BAIK LAMA MAUPUN BARU) ---
        function kunciSemuaBaris() {
            let rows = document.getElementsByClassName('row-job');
            
            for (let i = 0; i < rows.length; i++) {
                let inPermintaan = rows[i].querySelector('.input-permintaan');
                let inProses = rows[i].querySelector('.input-proses');
                let inRealisasi = rows[i].querySelector('.input-realisasi');

                if (inPermintaan && inProses && inRealisasi) {
                    let maxQ = parseInt(inPermintaan.value) || 0;
                    let valP = parseInt(inProses.value) || 0;
                    let valR = parseInt(inRealisasi.value) || 0;

                    inProses.setAttribute('max', maxQ);
                    inRealisasi.setAttribute('max', maxQ);

                    if (valP > maxQ) inProses.value = maxQ;
                    if (valR > maxQ) inRealisasi.value = maxQ;
                }
            }
        }

        // --- 1. SCRIPT TAMBAH BARIS JABATAN ---
        document.getElementById('btn-tambah-job').addEventListener('click', function() {
            let container = document.getElementById('container-job');
            let rows = container.getElementsByClassName('row-job');
            
            const inputKebunUtama = document.getElementById('kebun');
            if (inputKebunUtama) daftarkanOpsiBaru(inputKebunUtama, 'list-kebun');

            for (let i = 0; i < rows.length; i++) {
                let inputLama = rows[i].querySelector('input[name="posisi_diminta[]"]');
                if (inputLama && inputLama.value.trim() !== "") {
                    daftarkanOpsiBaru(inputLama, 'list-jabatan');
                }
            }
            
            // Jalankan kloning baris baru
            let cloneRow = rows[0].cloneNode(true);
            
            let inputJob = cloneRow.querySelector('input[name="posisi_diminta[]"]');
            inputJob.value = "";
            inputJob.addEventListener('change', function() {
                daftarkanOpsiBaru(this, 'list-jabatan');
            });

            // Re-assign event listener agar baris baru juga punya kemampuan gembok otomatis
            let inputPermintaan = cloneRow.querySelector('.input-permintaan');
            let inputProses = cloneRow.querySelector('.input-proses');
            let inputRealisasi = cloneRow.querySelector('.input-realisasi');
            
            inputPermintaan.value = "0";
            inputProses.value = "0";
            inputRealisasi.value = "0";

            inputPermintaan.addEventListener('input', kunciSemuaBaris);
            inputProses.addEventListener('input', kunciSemuaBaris);
            inputRealisasi.addEventListener('input', kunciSemuaBaris);
            
            let btnHapus = cloneRow.querySelector('.btn-hapus-row');
            btnHapus.disabled = false;
            btnHapus.className = "btn btn-danger btn-hapus-row fw-bold w-100";
            
            btnHapus.addEventListener('click', function() {
                cloneRow.remove();
            });
            
            container.appendChild(cloneRow);
        });

        // --- 2. SCRIPT AJAX INTEGRASI KE BACKEND AI ---
        document.getElementById('btn-scan').addEventListener('click', async function() {
            const fileInput = document.getElementById('scan_file');
            const file = fileInput.files[0];
            
            if (!file) {
                alert('Silakan pilih file Gambar atau PDF hasil scan form PTK terlebih dahulu!');
                return;
            }

            const formData = new FormData();
            formData.append('file', file);

            const btnScan = document.getElementById('btn-scan');
            const btnText = document.getElementById('scan-text');
            btnText.innerText = "⏳ AI Sedang Memproses Dokumen...";
            btnScan.disabled = true;

            try {
                const response = await fetch('http://127.0.0.1:5000/api/v1/scan-ptk', {
                    method: 'POST',
                    body: formData
                });

                const resData = await response.json();
                console.log("Respon Mentah dari Python AI:", resData);

                if (response.ok && resData.status === 'success') {
                    
                    let detectedNoPTK = resData.data.no_ptk || resData.data.nomor_ptk || resData.data.nomor_dokumen || "";
                    if (detectedNoPTK) {
                        document.getElementById('nomor_dokumen').value = detectedNoPTK;
                    }

                    let detectedKebun = resData.data.kebun || resData.data.estate || "";
                    let targetTeks = detectedKebun.toUpperCase().replace("KEBUN", "").trim();

                    if (targetTeks.includes("MANAGER") || targetTeks === "") {
                        if (detectedNoPTK) {
                            let parts = detectedNoPTK.split('/');
                            if (parts.length > 1) {
                                targetTeks = parts[1].trim().toUpperCase();
                            }
                        }
                    }

                    const inputKebun = document.getElementById('kebun');
                    const datalistKebun = document.getElementById('list-kebun');
                    
                    if (inputKebun && targetTeks !== "") {
                        let cocok = false;
                        for (let i = 0; i < datalistKebun.options.length; i++) {
                            let optVal = datalistKebun.options[i].value.toUpperCase().trim();
                            if (optVal.includes(targetTeks) || targetTeks.includes(optVal.split(' ')[0])) {
                                inputKebun.value = datalistKebun.options[i].value;
                                cocok = true;
                                break;
                            }
                        }
                        
                        if (!cocok) {
                            inputKebun.value = detectedKebun; 
                            daftarkanOpsiBaru(inputKebun, 'list-kebun');
                        }
                    }

                    let detectedJabatan = resData.data.jabatan || "";
                    let targetJabatan = detectedJabatan.toUpperCase().trim();

                    if (targetJabatan === "") {
                        let fileName = file.name.toUpperCase();
                        if (fileName.includes("SECURITY")) {
                            targetJabatan = "SECURITY";
                        } else if (fileName.includes("HARVESTER") || fileName.includes("PEMANEN")) {
                            targetJabatan = "HARVESTERS";
                        }
                    }

                    const firstRowJobInput = document.querySelector('#container-job .row-job .posisi-job-input');
                    const firstRowQtyInput = document.querySelector('#container-job .row-job input[name="jumlah_kebutuhan[]"]');
                    const datalistJabatan = document.getElementById('list-jabatan');

                    if (targetJabatan !== "" && firstRowJobInput) {
                        let cocokJob = false;
                        for (let j = 0; j < datalistJabatan.options.length; j++) {
                            let jobVal = datalistJabatan.options[j].value.toUpperCase();
                            if (jobVal.includes(targetJabatan)) {
                                firstRowJobInput.value = datalistJabatan.options[j].value;
                                cocokJob = true;
                                break;
                            }
                        }
                        if (!cocokJob) {
                            firstRowJobInput.value = detectedJabatan;
                            daftarkanOpsiBaru(firstRowJobInput, 'list-jabatan');
                        }
                    }

                    let detectedQty = resData.data.qty || resData.data.jumlah || resData.data.quota || "";
                    if (detectedQty && firstRowQtyInput) {
                        firstRowQtyInput.value = detectedQty;
                        kunciSemuaBaris(); // Update gembok setelah AI mengisi kuota
                    }
                    
                    alert('⚡ AI Sukses! Data nomor dokumen, kebun, jabatan, dan kuota kebutuhan berhasil diisi otomatis.');
                } else {
                    alert('Gagal membaca dokumen: ' + (resData.message || 'Format teks tidak terstruktur dengan baik.'));
                }
            } catch (error) {
                console.error(error);
                alert('Gagal terhubung ke Server AI Python! Pastikan terminal VS Code kamu masih menyala.');
            } finally {
                btnText.innerText = "🤖 Jalankan Ekstraksi AI";
                btnScan.disabled = false;
            }
        });
    </script>
</body>
</html>