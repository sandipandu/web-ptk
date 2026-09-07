<?php

namespace App\Controllers;

class Auth extends BaseController
{
    protected $db;

    public function __construct()
    {
        // Panggil query builder bawaan CI4 untuk cek ke database langsung
        $this->db = \Config\Database::connect();
    }

    // Tampilan Halaman Login
    public function login()
    {
        // SELARAS: Jika sudah login, otomatis lempar ke halaman DASHBOARD utama
        if (session()->get('sudah_login')) {
            return redirect()->to(site_url('dashboard'));
        }
        return view('auth/login');
    }

    // Proses Pengecekan Login
    public function proses_login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Cari user berdasarkan username dan password yang di-MD5
        $user = $this->db->table('users')->getWhere([
            'username' => $username,
            'password' => md5($password)
        ])->getRowArray();

        if ($user) {
            // Jika cocok, buat session tanda masuk
            session()->set([
                'sudah_login'  => true,
                'username'     => $user['username'],
                'nama_lengkap' => $user['nama_lengkap'],
                'role'         => $user['role']
            ]);
            
            // SELARAS: Begitu sukses login langsung masuk ke halaman DASHBOARD utama
            return redirect()->to(site_url('dashboard'));
        } else {
            // Jika salah, kembalikan ke login via site_url dengan pesan error
            return redirect()->to(site_url('auth/login'))->with('gagal', 'Username atau Password salah!');
        }
    }

    // Fungsi Logout
    public function logout()
    {
        session()->destroy();
        
        // Gunakan site_url saat logout agar kembali ke form login dengan aman
        return redirect()->to(site_url('auth/login'));
    }
}