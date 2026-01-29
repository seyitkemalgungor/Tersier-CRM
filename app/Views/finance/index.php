<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-white"><i class="fa-solid fa-scale-balanced me-2 text-warning"></i> Finans Paneli</h2>
        <p class="text-muted">Kasa durumu, alacak/verecek takibi ve işlem geçmişi.</p>
    </div>
    <button class="btn btn-warning fw-bold btn-lg shadow" onclick="openTransactionModal('collection')">
        <i class="fa-solid fa-plus-minus me-2"></i> HIZLI İŞLEM
    </button>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card p-4 bg-dark border-start border-4 border-success shadow h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted small text-uppercase mb-2">Bugünkü Tahsilat</h6>
                    <h2 class="text-success fw-bold m-0">+<?= number_format($daily['in'] ?? 0, 2) ?> ₺</h2>
                </div>
                <div class="bg-success bg-opacity-10 p-3 rounded">
                    <i class="fa-solid fa-arrow-down fa-2x text-success"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-4 bg-dark border-start border-4 border-danger shadow h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted small text-uppercase mb-2">Bugünkü Ödeme</h6>
                    <h2 class="text-danger fw-bold m-0">-<?= number_format($daily['out'] ?? 0, 2) ?> ₺</h2>
                </div>
                <div class="bg-danger bg-opacity-10 p-3 rounded">
                    <i class="fa-solid fa-arrow-up fa-2x text-danger"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <?php $net = ($daily['in'] ?? 0) - ($daily['out'] ?? 0); ?>
        <div class="card p-4 bg-dark border-start border-4 <?= $net >= 0 ? 'border-primary' : 'border-warning' ?> shadow h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted small text-uppercase mb-2">Günlük Net Akış</h6>
                    <h2 class="text-white fw-bold m-0"><?= number_format($net, 2) ?> ₺</h2>
                </div>
                <div class="bg-primary bg-opacity-10 p-3 rounded">
                    <i class="fa-solid fa-vault fa-2x text-primary"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow bg-dark h-100">
            <div class="card-header bg-dark border-secondary p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title text-white m-0"><i class="fa-solid fa-list-ul me-2"></i> Finansal Hareketler</h5>
                </div>

                <form action="" method="GET" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <small class="text-muted">Başlangıç</small>
                        <input type="date" name="start_date" class="form-control form-control-sm bg-black text-white border-secondary" value="<?= $filters['start_date'] ?? date('Y-m-01') ?>">
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Bitiş</small>
                        <input type="date" name="end_date" class="form-control form-control-sm bg-black text-white border-secondary" value="<?= $filters['end_date'] ?? date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Cari Hesap</small>
                        <select name="contact_id" class="form-select form-select-sm bg-black text-white border-secondary">
                            <option value="">Tümü</option>
                            <?php if (!empty($contacts)): foreach ($contacts as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= (isset($filters['contact_id']) && $filters['contact_id'] == $c['id']) ? 'selected' : '' ?>><?= $c['title'] ?></option>
                            <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-1">
                        <div class="w-100">
                            <small class="text-muted">İşlem</small>
                            <select name="type" class="form-select form-select-sm bg-black text-white border-secondary">
                                <option value="">Tümü</option>
                                <option value="collection" <?= (isset($filters['type']) && $filters['type'] == 'collection') ? 'selected' : '' ?>>Tahsilat</option>
                                <option value="payment" <?= (isset($filters['type']) && $filters['type'] == 'payment') ? 'selected' : '' ?>>Ödeme</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary mb-0 align-self-end"><i class="fa-solid fa-search"></i></button>
                    </div>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-striped table-hover mb-0 small align-middle">
                        <thead>
                            <tr>
                                <th>Tarih</th>
                                <th>Cari Hesap</th>
                                <th>İşlem / Yöntem</th>
                                <th>Açıklama</th>
                                <th class="text-end">Tutar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($transactions)): ?>
                                <?php foreach ($transactions as $t): ?>
                                    <tr>
                                        <td><?= date('d.m.Y', strtotime($t['date'])) ?></td>
                                        <td>
                                            <a href="/contact/detail?id=<?= $t['contact_id'] ?>" class="text-warning text-decoration-none fw-bold">
                                                <?= $t['contact_name'] ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php if ($t['source'] == 'invoice'): ?>
                                                <?php if ($t['type'] == 'purchase'): ?>
                                                    <span class="badge bg-secondary border border-secondary text-white">Alış Faturası</span>
                                                <?php elseif ($t['type'] == 'sale'): ?>
                                                    <span class="badge bg-secondary border border-warning text-warning">Satış Faturası</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><?= $t['type'] ?></span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?php if ($t['type'] == 'collection'): ?>
                                                    <span class="badge bg-success bg-opacity-75">Tahsilat</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger bg-opacity-75">Ödeme</span>
                                                <?php endif; ?>
                                                <span class="text-muted ms-1"><?= $t['method'] ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-muted"><?= $t['description'] ?></td>
                                        <td class="text-end fw-bold fs-6">
                                            <?php
                                            // Renklendirme Mantığı:
                                            // + (Yeşil): Satış, Tahsilat
                                            // - (Kırmızı): Alış, Ödeme
                                            $isPositive = false;
                                            if ($t['source'] == 'invoice' && $t['type'] == 'sale') $isPositive = true;
                                            if ($t['source'] == 'payment' && $t['type'] == 'collection') $isPositive = true;

                                            if ($isPositive):
                                            ?>
                                                <span class="text-success">+<?= number_format($t['amount'], 2) ?> ₺</span>
                                            <?php else: ?>
                                                <span class="text-danger">-<?= number_format($t['amount'], 2) ?> ₺</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">Kriterlere uygun kayıt bulunamadı.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">

        <div class="card border-0 shadow bg-dark mb-4">
            <div class="card-header bg-success text-white fw-bold d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-hand-holding-dollar me-2"></i> ALACAKLARIMIZ</span>
                <span class="badge bg-white text-success"><?= count($receivables ?? []) ?></span>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush bg-dark" style="max-height: 250px; overflow-y: auto;">
                    <?php if (!empty($receivables)): ?>
                        <?php foreach ($receivables as $r): ?>
                            <li class="list-group-item bg-dark text-white d-flex justify-content-between align-items-center border-secondary">
                                <div class="overflow-hidden me-2">
                                    <div class="fw-bold text-white text-truncate"><?= $r['title'] ?></div>
                                    <small class="text-muted"><i class="fa-solid fa-phone me-1"></i> <?= $r['phone'] ?></small>
                                </div>
                                <div class="text-end" style="min-width: 100px;">
                                    <div class="fw-bold text-success fs-5"><?= number_format($r['balance'], 2) ?> ₺</div>
                                    <button class="btn btn-sm btn-outline-success py-0 mt-1 w-100"
                                        onclick="openTransactionModal('collection', <?= $r['id'] ?>, '<?= addslashes($r['title']) ?>', <?= $r['balance'] ?>)">
                                        Tahsil Et
                                    </button>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item bg-dark text-center py-4 text-muted border-secondary">Tahsil edilecek alacak yok.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="card border-0 shadow bg-dark">
            <div class="card-header bg-danger text-white fw-bold d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-file-invoice-dollar me-2"></i> ÖDEYECEKLERİMİZ</span>
                <span class="badge bg-white text-danger"><?= count($payables ?? []) ?></span>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush bg-dark" style="max-height: 250px; overflow-y: auto;">
                    <?php if (!empty($payables)): ?>
                        <?php foreach ($payables as $p): ?>
                            <li class="list-group-item bg-dark text-white d-flex justify-content-between align-items-center border-secondary">
                                <div class="overflow-hidden me-2">
                                    <div class="fw-bold text-white text-truncate"><?= $p['title'] ?></div>
                                    <small class="text-muted">Borçlu</small>
                                </div>
                                <div class="text-end" style="min-width: 100px;">
                                    <div class="fw-bold text-danger fs-5"><?= number_format(abs($p['balance']), 2) ?> ₺</div>
                                    <button class="btn btn-sm btn-outline-danger py-0 mt-1 w-100"
                                        onclick="openTransactionModal('payment', <?= $p['id'] ?>, '<?= addslashes($p['title']) ?>', <?= abs($p['balance']) ?>)">
                                        Ödeme Yap
                                    </button>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item bg-dark text-center py-4 text-muted border-secondary">Ödenecek borç yok.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="transactionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white">Finans İşlemi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="/finance/save_transaction" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="small text-muted">İşlem Türü</label>
                        <select name="type" id="transType" class="form-select bg-black text-white fw-bold border-secondary" onchange="toggleTypeColor()">
                            <option value="collection">TAHSİLAT (Kasa Giriş)</option>
                            <option value="payment">ÖDEME (Kasa Çıkış)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted">Cari Hesap</label>
                        <select name="contact_id" id="contactSelect" class="form-select bg-dark text-white border-secondary" required>
                            <option value="">Seçiniz...</option>
                            <?php if (!empty($contacts)): foreach ($contacts as $c): ?>
                                    <?php
                                    // Bakiye gösterimi: Pozitifse (A), Negatifse (B)
                                    $balText = ($c['balance'] >= 0) ? number_format($c['balance'], 2) . ' (A)' : number_format(abs($c['balance']), 2) . ' (B)';
                                    ?>
                                    <option value="<?= $c['id'] ?>"><?= $c['title'] ?> [<?= $balText ?>]</option>
                            <?php endforeach;
                            endif; ?>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="small text-muted">Tutar (₺)</label>
                            <input type="number" step="0.01" name="amount" id="amountInput" class="form-control fw-bold fs-4 text-center bg-black text-success border-secondary" required>
                        </div>
                        <div class="col-6">
                            <label class="small text-muted">Tarih</label>
                            <input type="date" name="payment_date" class="form-control bg-dark text-white border-secondary" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted">Ödeme Yöntemi</label>
                        <select name="payment_method" class="form-select bg-dark text-white border-secondary">
                            <option value="bank">Banka / Havale</option>
                            <option value="cash">Nakit Kasa</option>
                            <option value="check">Çek</option>
                            <option value="card">Kredi Kartı</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted">Açıklama</label>
                        <input type="text" name="description" class="form-control bg-dark text-white border-secondary" placeholder="Örn: Fatura ödemesi...">
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="submit" class="btn btn-success w-100 fw-bold" id="submitBtn">KAYDET</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // İşlem Tipine Göre Renkleri ve Metinleri Değiştir
    function toggleTypeColor() {
        const type = document.getElementById('transType').value;
        const input = document.getElementById('amountInput');
        const btn = document.getElementById('submitBtn');

        if (type === 'collection') {
            input.classList.remove('text-danger');
            input.classList.add('text-success');
            btn.classList.remove('btn-danger');
            btn.classList.add('btn-success');
            btn.innerText = 'TAHSİLATI KAYDET';
        } else {
            input.classList.remove('text-success');
            input.classList.add('text-danger');
            btn.classList.remove('btn-success');
            btn.classList.add('btn-danger');
            btn.innerText = 'ÖDEMEYİ KAYDET';
        }
    }

    // Modal açma yardımcısı
    function openTransactionModal(defaultType, id = null, name = null, amount = null) {
        document.getElementById('transType').value = defaultType;
        toggleTypeColor();

        // Eğer listeden tıklandıysa cariyi ve tutarı otomatik seç
        if (id) {
            document.getElementById('contactSelect').value = id;
            if (amount) document.getElementById('amountInput').value = parseFloat(amount).toFixed(2);
        } else {
            // Butondan tıklandıysa temizle
            document.getElementById('contactSelect').value = "";
            document.getElementById('amountInput').value = "";
        }

        new bootstrap.Modal(document.getElementById('transactionModal')).show();
    }
</script>