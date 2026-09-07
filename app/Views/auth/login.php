<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PTK PT Lonsum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background: #000c30; 
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #ffffff;
            border: 1px solid #e3e6f0;
            border-radius: 16px;
            box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.03);
            width: 100%;
            max-width: 400px;
            padding: 2.5rem 2rem;
        }
        .form-control {
            border: 1px solid #d1d3e2;
            padding: 0.6rem 1rem;
            font-size: 14px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .form-control:focus {
            color: #495057;
            background-color: #fff;
            border-color: #198754;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.15);
        }
        .btn-success {
            background-color: #198754 !important;
            border-color: #198754 !important;
            font-weight: 700;
            padding: 0.6rem 1rem;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s ease;
        }
        .btn-success:hover {
            background-color: #146c43 !important;
            border-color: #146c43 !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(25, 135, 84, 0.2);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="text-center mb-4">
            <div class="bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center rounded-circle mb-2" style="width: 56px; height: 56px; border: 1px solid rgba(25, 135, 84, 0.1);">
                <span class="fs-4">🌿</span>
            </div>
            <h4 class="fw-bold text-dark mb-1" style="letter-spacing: -0.3px;">PT LONSUM</h4>
            
        </div>
        
        <?php if (session()->getFlashdata('gagal')) : ?>
            <div class="alert alert-danger p-2-5 text-center small border-0 rounded-3 mb-3" style="background-color: #fde8e8; color: #9b1c1c;">
                🔒 <?= session()->getFlashdata('gagal') ?>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('auth/proses_login') ?>" method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autocomplete="off">
            </div>
            
            <div class="mb-4">
                <label class="form-label small fw-bold text-secondary">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
            </div>
            
            <button type="submit" class="btn btn-success w-100 shadow-sm">
                Masuk Ke Sistem
            </button>
        </form>
        
        <div class="text-center mt-4 pt-2">
            <p class="text-muted mb-0" style="font-size: 10.5px;">&copy; <?= date('Y') ?> PT PP London Sumatra Tbk.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>