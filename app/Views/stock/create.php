<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none text-muted">Panel</a></li>
                <li class="breadcrumb-item"><a href="/stock/index" class="text-decoration-none text-muted">Stok</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">Yeni Kart</li>
            </ol>
        </nav>
        <h2 class="fw-bold text-white">Yeni Stok Kartı Tanımla</h2>
    </div>
    <a href="/stock/index" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left"></i> Listeye Dön
    </a>
</div>

<form action="/stock/save" method="POST">
    <div class="row g-4">

        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header bg-transparent border-secondary py-3">
                    <h5 class="m-0 text-primary"><i class="fa-solid fa-box-open me-2"></i> Genel Ürün Bilgileri</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Stok Kodu <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control fw-bold" placeholder="Örn: ELK-001" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Barkod (EAN-13)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary"><i class="fa-solid fa-barcode"></i></span>
                                <input type="text" name="barcode" class="form-control" placeholder="Barkod okutunuz...">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small">Ürün Adı <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control fs-5" placeholder="Örn: Logitech MX Master 3 Mouse" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Ürün Tipi <span class="text-danger">*</span></label>
                            <select name="product_type" class="form-select fw-bold text-white bg-dark">
                                <option value="product">Tam Mamul (Satılacak Ürün)</option>
                                <option value="raw">Ham Madde (Satın Alınan)</option>
                                <option value="semi">Yarı Mamul (Üretimde Kullanılan)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Marka</label>
                            <input type="text" name="brand" class="form-control" placeholder="Örn: Logitech">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small">Ürün Açıklaması</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Ürün hakkında teknik detaylar..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">

            <div class="card mb-4">
                <div class="card-header bg-transparent border-secondary py-3">
                    <h5 class="m-0 text-success"><i class="fa-solid fa-tags me-2"></i> Fiyatlandırma</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small">Alış Fiyatı (Maliyet)</label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="buy" class="form-control" placeholder="0.00">
                            <span class="input-group-text bg-dark border-secondary">₺</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small">Satış Fiyatı</label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="sell" class="form-control fw-bold text-success" placeholder="0.00">
                            <span class="input-group-text bg-dark border-secondary">₺</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small">KDV Oranı</label>
                        <select name="vat" class="form-select">
                            <option value="20" selected>%20</option>
                            <option value="10">%10</option>
                            <option value="1">%1</option>
                            <option value="0">%0 (Muaf)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-transparent border-secondary py-3">
                    <h5 class="m-0 text-warning"><i class="fa-solid fa-gears me-2"></i> Stok Ayarları</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small">Ana Birim</label>
                        <select name="unit" class="form-select">
                            <option value="Adet">Adet</option>
                            <option value="Kg">Kilogram (kg)</option>
                            <option value="Mt">Metre (m)</option>
                            <option value="Lt">Litre (lt)</option>
                            <option value="Koli">Koli</option>
                            <option value="Saat">Saat (Hizmet)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small">Kritik Stok Uyarısı</label>
                        <input type="number" name="min" class="form-control" value="10">
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Stok bu seviyenin altına düştüğünde sistem uyarı verir.</small>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold fs-5 shadow-lg">
                    <i class="fa-solid fa-save me-2"></i> KARTI KAYDET
                </button>
            </div>
        </div>
    </div>
</form>