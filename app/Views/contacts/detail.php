<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="/contact/index" class="text-decoration-none text-muted">Cariler</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">Kart Detayı</li>
            </ol>
        </nav>
        <h2 class="fw-bold text-white mb-0">
            <?= $contact['title'] ?>
            <?php if ($contact['type'] == 'customer'): ?>
                <span class="badge bg-success fs-6 align-middle ms-2">Müşteri</span>
            <?php else: ?>
                <span class="badge bg-danger fs-6 align-middle ms-2">Tedarikçi</span>
            <?php endif; ?>
        </h2>
    </div>

    <div class="text-end">
        <h3 class="m-0 <?= $contact['balance'] >= 0 ? 'text-success' : 'text-danger' ?>">
            <?= number_format(abs($contact['balance']), 2) ?> ₺
        </h3>
        <small class="text-muted"><?= $contact['balance'] >= 0 ? 'ALACAKLIYIZ' : 'BORÇLUYUZ' ?></small>
    </div>
</div>

<ul class="nav nav-tabs border-secondary mb-4" id="myTab" role="tablist">
    <li class="nav-item">
        <button class="nav-link active text-muted" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button">
            <i class="fa-solid fa-circle-info me-2"></i> Genel Bilgiler
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link text-muted" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button">
            <i class="fa-solid fa-list-check me-2"></i> Hesap Ekstresi (Hareketler)
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link text-muted" id="prices-tab" data-bs-toggle="tab" data-bs-target="#prices" type="button">
            <i class="fa-solid fa-tags me-2 text-warning"></i> Özel Fiyatlar
        </button>
    </li>
</ul>

<div class="tab-content" id="myTabContent">

    <div class="tab-pane fade show active" id="info">
        <div class="row g-4">
            <div class="col-md-8">
                <div class="card h-100">
                    <div class="card-header bg-transparent border-secondary fw-bold text-white">İletişim & Fatura Bilgileri</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="small text-muted">Telefon</label>
                                <div class="fs-5"><?= $contact['phone'] ?? '-' ?></div>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted">E-Posta</label>
                                <div class="fs-5"><?= $contact['email'] ?? '-' ?></div>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted">Vergi Dairesi</label>
                                <div class="fs-5"><?= $contact['tax_office'] ?? '-' ?></div>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted">Vergi No / TCKN</label>
                                <div class="fs-5"><?= $contact['tax_id'] ?? '-' ?></div>
                            </div>
                            <div class="col-12">
                                <label class="small text-muted">Adres</label>
                                <div class="fs-5"><?= $contact['address'] ?? '-' ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-warning bg-warning bg-opacity-10">
                    <div class="card-header bg-transparent border-warning text-warning fw-bold">Finansal Ayarlar</div>
                    <div class="card-body">
                        <label class="small text-muted">Risk Limiti</label>
                        <h4 class="text-white"><?= number_format($contact['risk_limit'] ?? 0, 2) ?> ₺</h4>
                        <hr class="border-secondary">
                        <label class="small text-muted">Vade Günü</label>
                        <h4 class="text-white"><?= $contact['payment_term'] ?? 0 ?> Gün</h4>
                        <hr class="border-secondary">
                        <label class="small text-muted">IBAN</label>
                        <div class="text-white small font-monospace"><?= $contact['iban'] ?? 'TR--' ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="activity">
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-dark table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Tarih</th>
                            <th>İşlem</th>
                            <th>Ürün</th>
                            <th class="text-center">Miktar</th>
                            <th class="text-end">Tutar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movements as $m): ?>
                            <tr>
                                <td><?= date('d.m.Y', strtotime($m['created_at'])) ?></td>
                                <td><?= $m['document_no'] ?? '-' ?></td>
                                <td><?= $m['product_name'] ?></td>
                                <td class="text-center">
                                    <?php if ($m['is_entry']): ?>
                                        <span class="text-success"><i class="fa-solid fa-arrow-down"></i> <?= $m['quantity'] ?></span>
                                    <?php else: ?>
                                        <span class="text-danger"><i class="fa-solid fa-arrow-up"></i> <?= $m['quantity'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end fw-bold">
                                    <?= number_format($m['quantity'] * $m['price'], 2) ?> ₺
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($movements)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Hareket kaydı bulunamadı.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="prices">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-black fw-bold">
                        <i class="fa-solid fa-tag me-1"></i> Yeni Fiyat Tanımla
                    </div>
                    <div class="card-body">
                        <form action="/contact/save_price" method="POST">
                            <input type="hidden" name="contact_id" value="<?= $contact['id'] ?>">

                            <div class="mb-3">
                                <label class="small text-muted">Ürün Seçin</label>
                                <select name="product_id" id="prodSelect" class="form-select bg-dark text-white" required onchange="updateListPrice()">
                                    <option value="" data-price="0">Seçiniz...</option>
                                    <?php foreach ($products as $p): ?>
                                        <option value="<?= $p['id'] ?>" data-price="<?= $p['sell_price'] ?>">
                                            <?= $p['name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-info" id="listPriceDisplay">Liste Fiyatı: 0.00 ₺</small>
                            </div>

                            <div class="mb-4">
                                <label class="small text-muted">Bu Müşteriye Özel Fiyat</label>
                                <input type="number" step="0.01" name="special_price" class="form-control fw-bold fs-4 text-warning bg-dark" placeholder="0.00" required>
                            </div>

                            <button type="submit" class="btn btn-warning w-100 fw-bold">FİYATI KAYDET</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card h-100">
                    <div class="card-header bg-transparent border-secondary text-white">Tanımlı Özel Fiyatlar</div>
                    <div class="card-body p-0">
                        <table class="table table-dark table-hover mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Ürün Adı</th>
                                    <th>Liste Fiyatı</th>
                                    <th>Özel Fiyat</th>
                                    <th>Fark</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($specialPrices as $sp):
                                    $diff = $sp['list_price'] - $sp['special_price'];
                                    $percent = ($sp['list_price'] > 0) ? round(($diff / $sp['list_price']) * 100) : 0;
                                ?>
                                    <tr>
                                        <td>
                                            <span class="fw-bold"><?= $sp['product_name'] ?></span><br>
                                            <small class="text-muted"><?= $sp['product_code'] ?></small>
                                        </td>
                                        <td class="text-muted text-decoration-line-through"><?= number_format($sp['list_price'], 2) ?> ₺</td>
                                        <td class="text-warning fw-bold fs-5"><?= number_format($sp['special_price'], 2) ?> ₺</td>
                                        <td>
                                            <?php if ($diff > 0): ?>
                                                <span class="badge bg-success">▼ %<?= $percent ?> İndirim</span>
                                            <?php elseif ($diff < 0): ?>
                                                <span class="badge bg-danger">▲ %<?= abs($percent) ?> Zam</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Aynı</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <a href="/contact/delete_price?id=<?= $sp['id'] ?>&cid=<?= $contact['id'] ?>" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($specialPrices)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Özel fiyat tanımlanmamış.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Ürün seçince liste fiyatını gösteren script
    function updateListPrice() {
        const select = document.getElementById('prodSelect');
        const price = select.options[select.selectedIndex].getAttribute('data-price');
        document.getElementById('listPriceDisplay').innerText = 'Liste Fiyatı: ' + parseFloat(price).toFixed(2) + ' ₺';
    }

    // URL'de tab varsa o tabı aç (Sayfa yenilenince doğru sekmede kalsın)
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');
    if (tab) {
        const tabEl = document.querySelector(`#${tab}-tab`);
        if (tabEl) {
            new bootstrap.Tab(tabEl).show();
        }
    }
</script>