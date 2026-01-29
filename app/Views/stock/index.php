<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-white"><i class="fa-solid fa-boxes-stacked me-2 text-warning"></i> Stok Yönetimi</h2>
        <p class="text-muted">Stok kartları, envanter durumu ve hızlı hareket girişleri.</p>
    </div>
    <a href="/stock/create_page" class="btn btn-warning btn-lg fw-bold shadow">
        <i class="fa-solid fa-plus"></i> YENİ STOK KARTI
    </a>
</div>

<div class="card border-0 shadow bg-dark border-top border-warning border-3">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle datatable w-100">
                <thead>
                    <tr>
                        <th>Kod / Tip</th>
                        <th>Ürün Adı</th>
                        <th>Fiyatlar (A/S)</th>
                        <th>Mevcut Stok</th>
                        <th class="text-end" style="min-width: 160px;">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): foreach ($products as $p):
                            // Kritik Stok Hesabı
                            $isCritical = false;
                            if (isset($p['min_stock_alert']) && $p['current_stock'] <= $p['min_stock_alert']) {
                                $isCritical = true;
                            }
                    ?>
                            <tr>
                                <td>
                                    <div class="badge bg-secondary mb-1"><?= $p['code'] ?></div>
                                    <div>
                                        <?php if ($p['product_type'] == 'raw'): ?>
                                            <span class="badge bg-info text-dark" style="font-size: 0.7rem;">Ham Madde</span>
                                        <?php elseif ($p['product_type'] == 'semi'): ?>
                                            <span class="badge bg-primary bg-opacity-75" style="font-size: 0.7rem;">Yarı Mamul</span>
                                        <?php else: ?>
                                            <span class="badge bg-success bg-opacity-75" style="font-size: 0.7rem;">Tam Mamul</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold fs-5 text-white"><?= $p['name'] ?></div>
                                    <?php if ($isCritical): ?>
                                        <small class="text-danger fw-bold"><i class="fa-solid fa-triangle-exclamation"></i> Kritik Seviye! (Min: <?= (float)$p['min_stock_alert'] ?>)</small>
                                    <?php else: ?>
                                        <small class="text-muted"><?= $p['category'] ?? '' ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="small text-muted">Alış: <span class="text-white"><?= number_format($p['buy_price'], 2) ?> ₺</span></div>
                                    <div class="small text-muted">Satış: <span class="text-success fw-bold"><?= number_format($p['sell_price'], 2) ?> ₺</span></div>
                                </td>
                                <td>
                                    <h4 class="m-0 fw-bold <?= $p['current_stock'] > 0 ? 'text-white' : 'text-danger' ?>">
                                        <?= (float)$p['current_stock'] ?> <small class="fs-6 text-muted"><?= $p['unit'] ?></small>
                                    </h4>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-warning me-1 fw-bold"
                                        onclick="openActionModal(<?= $p['id'] ?>, '<?= addslashes($p['name']) ?>', <?= $p['buy_price'] ?>, <?= $p['sell_price'] ?>)">
                                        <i class="fa-solid fa-right-left"></i> İşlem
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="showHistory(<?= $p['id'] ?>)">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                    </button>
                                </td>
                            </tr>
                    <?php endforeach;
                    endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="actionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white">Stok Hareketi Ekle</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="/stock/movement" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="product_id" id="actPid">

                    <h5 class="text-center text-warning fw-bold mb-4" id="actName">...</h5>

                    <div class="mb-3">
                        <label class="small text-muted">İşlem Türü</label>
                        <select name="process_type" id="procType" class="form-select text-white fw-bold bg-black border-secondary" onchange="updateModalColors()">
                            <optgroup label="Giriş İşlemleri (+)">
                                <option value="purchase">Alış Faturası (Stok Artar)</option>
                                <option value="return_in">Müşteri İadesi (Stok Artar)</option>
                                <option value="count_plus">Sayım Fazlası (Stok Artar)</option>
                            </optgroup>
                            <optgroup label="Çıkış İşlemleri (-)">
                                <option value="sale" selected>Satış Faturası (Stok Azalır)</option>
                                <option value="return_out">Tedarikçi İadesi (Stok Azalır)</option>
                                <option value="count_minus">Sayım Eksiği / Zayi (Stok Azalır)</option>
                            </optgroup>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted">Cari Hesap (Kimden/Kime?)</label>
                        <select name="contact_id" id="actContact" class="form-select bg-dark text-white border-secondary">
                            <option value="">Yükleniyor...</option>
                        </select>
                        <small class="text-muted" style="font-size: 0.7rem;">Alış seçerseniz tedarikçiler, satış seçerseniz müşteriler listelenir.</small>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="small text-muted">Miktar</label>
                            <input type="number" step="0.0001" name="quantity" id="actQty" class="form-control fw-bold fs-4 text-center bg-dark text-white border-secondary" placeholder="0" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="small text-muted">Birim Fiyat (₺)</label>
                            <input type="number" step="0.01" name="price" id="actPrice" class="form-control text-center bg-dark text-white border-secondary" placeholder="0.00">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted">Vade Tarihi (Ödeme Ne Zaman?)</label>
                        <input type="date" name="maturity_date" class="form-control bg-dark text-white border-secondary" value="<?= date('Y-m-d') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted">Belge No (Fatura/İrsaliye)</label>
                        <input type="text" name="document_no" id="actDoc" class="form-control bg-dark text-white border-secondary">
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted">Açıklama</label>
                        <input type="text" name="description" class="form-control bg-dark text-white border-secondary">
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="submit" class="btn btn-primary w-100 fw-bold" id="actBtn">İŞLEMİ ONAYLA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="historyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white">Hareket Geçmişi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <table class="table table-dark table-striped mb-0 small align-middle">
                    <thead>
                        <tr>
                            <th>Tarih</th>
                            <th>İşlem</th>
                            <th>Belge No</th>
                            <th>Miktar</th>
                            <th>Fiyat</th>
                            <th>Toplam</th>
                        </tr>
                    </thead>
                    <tbody id="historyBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // Global değişkenler
    let currentPid = 0;
    let defaultBuyPrice = 0;
    let defaultSellPrice = 0;
    let allContacts = []; // Tüm cariler hafızada

    // Sayfa Yüklendiğinde Carileri Çek
    document.addEventListener('DOMContentLoaded', function() {
        fetch('/stock/get_contacts')
            .then(res => res.json())
            .then(data => {
                allContacts = data;
            });
    });

    // 1. İşlem Modalını Açan Fonksiyon
    function openActionModal(id, name, buy, sell) {
        currentPid = id;
        defaultBuyPrice = parseFloat(buy);
        defaultSellPrice = parseFloat(sell);

        // Form alanlarını sıfırla
        document.getElementById('actPid').value = id;
        document.getElementById('actName').innerText = name;
        document.getElementById('actQty').value = '';
        document.getElementById('actDoc').value = '';

        // Varsayılan işlem tipini Satış yap ve renkleri/listeyi güncelle
        document.getElementById('procType').value = 'sale';
        updateModalColors();

        new bootstrap.Modal(document.getElementById('actionModal')).show();
    }

    // 2. İşlem Tipi Değişince (Renk ve Cari Filtreleme)
    function updateModalColors() {
        const type = document.getElementById('procType').value;
        const btn = document.getElementById('actBtn');
        const priceInput = document.getElementById('actPrice');
        const select = document.getElementById('actContact');

        // RENK VE FİYAT AYARI
        // Giriş İşlemleri (Yeşil)
        if (['purchase', 'return_in', 'count_plus'].includes(type)) {
            btn.className = 'btn btn-success w-100 fw-bold';
            btn.innerHTML = '<i class="fa-solid fa-arrow-down"></i> GİRİŞ YAP (STOK ARTAR)';
            priceInput.value = defaultBuyPrice.toFixed(2);
        }
        // Çıkış İşlemleri (Kırmızı)
        else {
            btn.className = 'btn btn-danger w-100 fw-bold';
            btn.innerHTML = '<i class="fa-solid fa-arrow-up"></i> ÇIKIŞ YAP (STOK AZALIR)';
            priceInput.value = defaultSellPrice.toFixed(2);
        }

        // CARİ LİSTESİ FİLTRELEME
        // Satış -> Müşteri, Alış -> Tedarikçi
        select.innerHTML = '<option value="">Cari Seçiniz (Opsiyonel)...</option>';

        let targetType = 'customer'; // Varsayılan: Müşteri

        if (['purchase', 'return_out'].includes(type)) {
            targetType = 'supplier'; // Alış veya İade Çıkış ise Tedarikçi
        }

        allContacts.forEach(c => {
            if (c.type === targetType) {
                const label = c.type === 'customer' ? '(Müşteri)' : '(Tedarikçi)';
                select.innerHTML += `<option value="${c.id}">${c.title} ${label}</option>`;
            }
        });

        // Eğer satış seçiliyse özel fiyat kontrolünü tetikle
        if (type === 'sale') checkSpecialPrice();
    }

    // 3. Cari Seçilince Akıllı Fiyat Kontrolü
    document.getElementById('actContact').addEventListener('change', checkSpecialPrice);

    function checkSpecialPrice() {
        const contactId = document.getElementById('actContact').value;
        const type = document.getElementById('procType').value;
        const priceInput = document.getElementById('actPrice');

        // Sadece "Satış" işleminde özel fiyat bakarız
        if (type === 'sale') {
            if (!contactId) {
                priceInput.value = defaultSellPrice.toFixed(2);
                return;
            }

            const formData = new FormData();
            formData.append('product_id', currentPid);
            formData.append('contact_id', contactId);

            fetch('/stock/get_price', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.text())
                .then(price => {
                    let finalPrice = parseFloat(price);
                    priceInput.value = finalPrice.toFixed(2);

                    if (finalPrice !== defaultSellPrice) {
                        priceInput.style.backgroundColor = '#ffc107';
                        priceInput.style.color = '#000';
                        setTimeout(() => {
                            priceInput.style.backgroundColor = '';
                            priceInput.style.color = '';
                        }, 800);
                    }
                });
        }
    }

    // 4. Geçmiş Görüntüleme
    function showHistory(id) {
        const tbody = document.getElementById('historyBody');
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">Yükleniyor...</td></tr>';
        new bootstrap.Modal(document.getElementById('historyModal')).show();

        fetch('/stock/history?id=' + id)
            .then(res => res.json())
            .then(data => {
                tbody.innerHTML = '';
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Kayıt yok.</td></tr>';
                    return;
                }
                const types = {
                    'purchase': 'Alış Faturası',
                    'sale': 'Satış Faturası',
                    'return_in': 'İade Alım',
                    'return_out': 'İade Çıkış',
                    'count_plus': 'Sayım Fazlası',
                    'count_minus': 'Sayım Eksiği',
                    'production_in': 'Üretim Girişi',
                    'production_out': 'Üretim Çıkışı'
                };

                data.forEach(row => {
                    const color = row.is_entry == 1 ? 'text-success' : 'text-danger';
                    const icon = row.is_entry == 1 ? '+' : '-';
                    tbody.innerHTML += `<tr>
                        <td>${new Date(row.created_at).toLocaleString('tr-TR')}</td>
                        <td>${types[row.process_type] || row.process_type}</td>
                        <td>${row.document_no || '-'}</td>
                        <td class="${color} fw-bold">${icon}${row.quantity}</td>
                        <td>${row.price} ₺</td>
                        <td>${(row.quantity * row.price).toFixed(2)} ₺</td>
                    </tr>`;
                });
            });
    }
</script>