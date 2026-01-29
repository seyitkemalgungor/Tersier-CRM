<!DOCTYPE html>
<html lang="tr" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <title>Kayıt Ol | Tersier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #121212;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }

        .auth-box {
            width: 100%;
            max-width: 400px;
            background: #1e1e1e;
            padding: 40px;
            border-radius: 15px;
            border: 1px solid #333;
            box-shadow: 0 0 50px rgba(0, 0, 0, 0.5);
        }

        .btn-primary {
            background: #ff6600;
            border: none;
            color: #000;
            font-weight: bold;
        }

        .btn-primary:hover {
            background: #e65c00;
        }

        .form-control {
            background: #2b2b2b;
            border-color: #444;
            color: #fff;
        }

        .form-control:focus {
            background: #333;
            border-color: #ff6600;
            color: #fff;
            box-shadow: none;
        }

        a {
            color: #ff6600;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="auth-box">
        <h2 class="text-center mb-4" style="color:#ff6600; font-weight:800; letter-spacing:2px;">TERSIER</h2>
        <p class="text-center text-muted mb-4">Yeni Firma Kaydı</p>

        <?php if (!empty($message)) echo $message; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="text-muted small">Firma Ünvanı</label>
                <input type="text" name="company_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="text-muted small">Vergi No / TCKN</label>
                <input type="text" name="tax_id" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="text-muted small">E-Posta</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-4">
                <label class="text-muted small">Şifre</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2">HESAP OLUŞTUR</button>
        </form>
        <div class="text-center mt-3">
            <small class="text-muted">Zaten üye misin? <a href="/auth/login">Giriş Yap</a></small>
        </div>
    </div>
</body>

</html>