<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// --- 1. RUTE SISTEM AUTENTIKASI (LOGIN & LOGOUT) ---
$routes->get('/', 'Auth::login'); 
$routes->get('auth/login', 'Auth::login');
$routes->post('auth/proses_login', 'Auth::proses_login');
$routes->get('auth/logout', 'Auth::logout');


// --- 2. RUTE DASHBOARD UTAMA ---
$routes->get('dashboard', 'Dashboard::index');


// --- 3. RUTE OPERASIONAL PTK (GROUPING SYSTEM) ---
$routes->group('ptk', function($routes) {
    
    // Halaman Utama / Index Grid Kebun
    $routes->get('/', 'Ptk::index');          
    
    // =========================================================================
    // FIX UPDATE: RUTE EXPORT & IMPORT EXCEL (Ditaruh atas agar tidak bentrok/404)
    // =========================================================================
    $routes->post('importExcel', 'Ptk::importExcel'); // Menangani POST form importExcel utama Anda
    $routes->post('import', 'Ptk::importExcel');      // Cadangan jika ada form lama yang mengarah ke ptk/import
    
    $routes->get('exportExcel', 'Ptk::exportExcel');  // Menerima request ptk/exportExcel sesuai tombol HTML
    $routes->get('export/excel', 'Ptk::exportExcel'); // Cadangan jika ada view lain menggunakan ptk/export/excel

    // =========================================================================
    // BARU & FIX AJAX: Ambil Detail Berkas & Update Data via Modal Pop-up
    // =========================================================================
    $routes->get('get_rincian_ajax', 'Ptk::get_rincian_ajax');
    $routes->post('update_ajax', 'Ptk::update_ajax'); // <--- FIX ERROR POST 404 DISINI

    // Proses Simpan Data Form Multi-Row
    $routes->post('simpan', 'Ptk::simpan');   

    // Form Tambah tanpa Parameter (Akses Langsung)
    $routes->get('tambah', 'Ptk::tambah');          

    // =========================================================================
    // RUTE DINAMIS PARAMETER (Menggunakan Placeholder (:any) dan (:num))
    // =========================================================================
    
    // Form Tambah Mendukung Deteksi Parameter Nama Kebun (Contoh: ptk/tambah/GBE)
    $routes->get('tambah/(:any)', 'Ptk::tambah/$1'); 
    
    // Rute untuk Halaman Rincian Penuh Kebun
    $routes->get('detail/(:any)', 'Ptk::detail/$1');
    
    // Rute untuk Detail Berkas Dokumen per Job (Aman dari Error 400)
    $routes->get('rincian_job/(:any)/(:any)', 'Ptk::rincian_job/$1/$2');
    
    // Rute untuk Fitur Hapus Massal per Kebun dari Halaman Index Grid
    $routes->get('delete_kebun/(:any)', 'Ptk::delete_kebun/$1');
    
    // Rute untuk Fitur Hapus Massal per Job di Kebun Tertentu
    $routes->get('delete_by_job/(:any)/(:any)', 'Ptk::delete_by_job/$1/$2');
    
    // Rute untuk Edit, Update, dan Hapus Tunggal Berkas Berdasarkan ID (Angka)
    $routes->get('edit/(:num)', 'HrisRatio::edit/$1'); // Ditangani oleh Controller Rasio jika terkait rasio    
    $routes->get('edit/(:num)', 'Ptk::edit/$1');    
    $routes->post('update/(:num)', 'Ptk::update/$1'); 
    $routes->get('delete/(:num)', 'Ptk::delete/$1');  
});


// --- 4. BARU: RUTE INTEGRASI RASIO HRIS KE MONITORING ---
$routes->group('hrisratio', function($routes) {
    // Halaman utama input rasio & tabel daftar rasio kebun
    $routes->get('/', 'HrisRatio::index');
    
    // Proses simpan data rasio baru dari form input
    $routes->post('store', 'HrisRatio::store');
    
    // Proses eksekusi sinkronisasi otomatis kalkulasi rasio ke data monitoring kebun
    $routes->get('sync/(:any)', 'HrisRatio::sync/$1');
});