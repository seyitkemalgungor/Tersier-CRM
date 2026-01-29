<!DOCTYPE html>
<html lang="tr" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?? 'Tersier ERP' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <style>
        :root {
            --primary: #ff6600;
            --dark-bg: #121212;
            --panel-bg: #1e1e1e;
            --border: #333;
        }

        body {
            background-color: var(--dark-bg);
            font-family: 'Segoe UI', sans-serif;
            overflow-x: hidden;
        }

        .sidebar {
            height: 100vh;
            width: 260px;
            position: fixed;
            top: 0;
            left: 0;
            background: var(--panel-bg);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 25px;
            text-align: center;
            border-bottom: 1px solid var(--border);
        }

        .brand {
            color: var(--primary);
            font-weight: 900;
            letter-spacing: 2px;
            font-size: 1.6rem;
            text-decoration: none;
        }

        .menu-label {
            padding: 15px 25px 5px 25px;
            font-size: 0.75rem;
            color: #666;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .nav-link {
            color: #bbb;
            padding: 10px 25px;
            display: flex;
            align-items: center;
            text-decoration: none;
            border-left: 4px solid transparent;
            transition: 0.2s;
            font-size: 0.95rem;
        }

        .nav-link:hover,
        .nav-link.active {
            background: #252525;
            color: var(--primary);
            border-left-color: var(--primary);
        }

        .nav-link i {
            width: 30px;
            font-size: 1.1em;
            text-align: center;
            margin-right: 5px;
        }

        .main-content {
            margin-left: 260px;
            padding: 30px;
        }

        .card {
            background: var(--panel-bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Form & Table Styles */
        .form-control,
        .form-select {
            background: #2b2b2b;
            border: 1px solid #444;
            color: #fff;
        }

        .form-control:focus {
            background: #333;
            border-color: var(--primary);
            color: #fff;
            box-shadow: none;
        }

        .table-dark {
            --bs-table-bg: transparent;
        }

        /* SweetAlert Custom */
        div:where(.swal2-container) div:where(.swal2-popup) {
            background: #1e1e1e !important;
            color: #fff;
            border: 1px solid #444;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <a href="/dashboard" class="brand"><i class="fa-solid fa-cube"></i> TERSIER</a>
        </div>

        <div class="overflow-auto" style="flex:1;">
            <div class="menu-label">Genel Bakış</div>
            <a href="/dashboard" class="nav-link <?= ($_SERVER['REQUEST_URI'] == '/dashboard') ? 'active' : '' ?>">
                <i class="fa-solid fa-gauge-high"></i> Dashboard
            </a>

            <div class="menu-label">Stok & Depo</div>
            <a href="/stock/index" class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/stock') !== false) ? 'active' : '' ?>">
                <i class="fa-solid fa-boxes-stacked"></i> Stok Kartları
            </a>
            <a href="/stock/index" class="nav-link">
                <i class="fa-solid fa-right-left"></i> Hareketler
            </a>

            <div class="menu-label text-warning">Üretim (MRP)</div>
            <a href="/production/index" class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/production') !== false) ? 'active' : '' ?>">
                <i class="fa-solid fa-industry"></i> Üretim Merkezi
            </a>
            <a href="/workorder/index" class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/workorder') !== false) ? 'active' : '' ?>">
                <i class="fa-solid fa-clipboard-list"></i> İş Emirleri
            </a>

            <div class="menu-label text-success">FİNANS</div>
            <a href="/finance/index" class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/finance') !== false) ? 'active' : '' ?>">
                <i class="fa-solid fa-coins"></i> Kasa & Vade Takibi
            </a>
            <a href="#" class="nav-link disabled" style="opacity:0.5">
                <i class="fa-solid fa-clipboard-list"></i> İş Emirleri
            </a>
            <a href="/contact/index" class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/contact') !== false) ? 'active' : '' ?>">
                <i class="fa-solid fa-users"></i> Cari Hesaplar
            </a>
        </div>

        <div class="p-3 border-top border-secondary bg-dark bg-opacity-50">
            <div class="d-flex align-items-center justify-content-between">
                <div style="line-height:1.2;">
                    <small class="text-muted" style="font-size:0.7rem;">GİRİŞ YAPAN</small><br>
                    <span class="fw-bold text-white"><?= $_SESSION['company_name'] ?></span>
                </div>
                <a href="/auth/logout" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-power-off"></i></a>
            </div>
        </div>
    </div>

    <div class="main-content">
        <?= $content ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('.datatable').DataTable({
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/tr.json"
                },
                order: [
                    [0, "desc"]
                ]
            });
        });
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) Swal.fire({
            icon: 'success',
            title: 'İşlem Başarılı',
            showConfirmButton: false,
            timer: 1500
        });
        if (urlParams.has('error')) Swal.fire({
            icon: 'error',
            title: 'Hata',
            text: urlParams.get('error')
        });
    </script>
</body>

</html>