<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-white"><i class="fa-solid fa-address-book me-2 text-primary"></i> Cari Hesap Yönetimi</h2>
        <p class="text-muted">Müşteri ve Tedarikçi veritabanı, bakiye durumları.</p>
    </div>
    <button class="btn btn-primary fw-bold btn-lg shadow" data-bs-toggle="modal" data-bs-target="#newContactModal">
        <i class="fa-solid fa-user-plus"></i> YENİ CARİ OLUŞTUR
    </button>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card p-4 border-start border-4 border-success bg-dark shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase small mb-2">Toplam Alacak (Müşteriler)</h6>
                    <h2 class="text-success fw-bold m-0"><?= number_format($receivable ?? 0, 2) ?> ₺</h2>
                </div>
                <div class="bg-success bg-opacity-10 p-3 rounded">
                    <i class="fa-solid fa-wallet fa-2x text-success"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-4 border-start border-4 border-danger bg-dark shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase small mb-2">Toplam Borç (Tedarikçiler)</h6>
                    <h2 class="text-danger fw-bold m-0"><?= number_format($payable ?? 0, 2) ?> ₺</h2>
                </div>
                <div class="bg-danger bg-opacity-10 p-3 rounded">
                    <i class="fa-solid fa-file-invoice-dollar fa-2x text-danger"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow bg-dark">
    <div class="card-header bg-dark border-secondary">
        <ul class="nav nav-tabs card-header-tabs" id="contactTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active text-white fw-bold" id="customer-tab" data-bs-toggle="tab" data-bs-target="#customers" type="button">
                    <i class="fa-solid fa-users text-success me-2"></i> Müşteriler
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link text-white fw-bold" id="supplier-tab" data-bs-toggle="tab" data-bs-target="#suppliers" type="button">
                    <i class="fa-solid fa-truck-field text-danger me-2"></i> Tedarikçiler
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body p-0">
        <div class="tab-content" id="contactTabContent">

            <div class="tab-pane fade show active" id="customers">
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle datatable w-100 mb-0">
                        <thead>
                            <tr>
                                <th>Müşteri Ünvanı</th>
                                <th>İletişim</th>
                                <th>Bakiye Durumu</th>
                                <th class="text-end">İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($contacts)): foreach ($contacts as $c): if ($c['type'] != 'customer') continue; ?>
                                    <tr>
                                        <td>
                                            <a href="/contact/detail?id=<?= $c['id'] ?>" class="text-white fw-bold text-decoration-none fs-5 hover-warning">
                                                <?= $c['title'] ?> <i class="fa-solid fa-arrow-up-right-from-square small ms-1 text-muted"></i>
                                            </a>
                                            <div class="small text-muted"><i class="fa-solid fa-location-dot me-1"></i> <?= $c['city'] ?? '-' ?></div>
                                        </td>
                                        <td>
                                            <div class="small text-white"><i class="fa-solid fa-phone me-1 text-warning"></i> <?= $c['phone'] ?? '-' ?></div>
                                            <div class="small text-muted"><?= $c['email'] ?? '-' ?></div>
                                        </td>
                                        <td>
                                            <?php if ($c['balance'] > 0): ?>
                                                <span class="text-success fw-bold fs-5">+<?= number_format($c['balance'], 2) ?> ₺</span>
                                                <div class="small text-muted" style="font-size:0.7rem;">ALACAKLIYIZ</div>
                                            <?php elseif ($c['balance'] < 0): ?>
                                                <span class="text-danger fw-bold fs-5"><?= number_format($c['balance'], 2) ?> ₺</span>
                                                <div class="small text-muted" style="font-size:0.7rem;">ALACAKLI</div>
                                            <?php else: ?>
                                                <span class="text-muted fw-bold">0.00 ₺</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <a href="/contact/detail?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-info me-1"><i class="fa-solid fa-eye"></i> Detay</a>
                                            <a href="/contact/delete?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Silmek istediğine emin misin?')"><i class="fa-solid fa-trash"></i></a>
                                        </td>
                                    </tr>
                            <?php endforeach;
                            endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="suppliers">
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle datatable w-100 mb-0">
                        <thead>
                            <tr>
                                <th>Tedarikçi Ünvanı</th>
                                <th>İletişim</th>
                                <th>Bakiye Durumu</th>
                                <th class="text-end">İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($contacts)): foreach ($contacts as $c): if ($c['type'] != 'supplier') continue; ?>
                                    <tr>
                                        <td>
                                            <a href="/contact/detail?id=<?= $c['id'] ?>" class="text-white fw-bold text-decoration-none fs-5 hover-warning">
                                                <?= $c['title'] ?> <i class="fa-solid fa-arrow-up-right-from-square small ms-1 text-muted"></i>
                                            </a>
                                            <div class="small text-muted"><i class="fa-solid fa-truck me-1"></i> Tedarikçi</div>
                                        </td>
                                        <td>
                                            <div class="small text-white"><i class="fa-solid fa-phone me-1 text-warning"></i> <?= $c['phone'] ?? '-' ?></div>
                                        </td>
                                        <td>
                                            <?php if ($c['balance'] < 0): ?>
                                                <span class="text-danger fw-bold fs-5"><?= number_format(abs($c['balance']), 2) ?> ₺</span>
                                                <div class="small text-muted" style="font-size:0.7rem;">BORÇLUYUZ</div>
                                            <?php elseif ($c['balance'] > 0): ?>
                                                <span class="text-success fw-bold fs-5"><?= number_format($c['balance'], 2) ?> ₺</span>
                                                <div class="small text-muted" style="font-size:0.7rem;">ALACAKLIYIZ (Avans)</div>
                                            <?php else: ?>
                                                <span class="text-muted fw-bold">0.00 ₺</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <a href="/contact/detail?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-info me-1"><i class="fa-solid fa-eye"></i> Detay</a>
                                            <a href="/contact/delete?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Silmek istediğine emin misin?')"><i class="fa-solid fa-trash"></i></a>
                                        </td>
                                    </tr>
                            <?php endforeach;
                            endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="newContactModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-primary fw-bold"><i class="fa-solid fa-address-card me-2"></i> Yeni Cari Kartı</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="/contact/save" method="POST">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="small text-muted">Cari Tipi <span class="text-danger">*</span></label>
                            <select name="type" class="form-select bg-black text-white fw-bold border-secondary">
                                <option value="customer">Müşteri (Alıcı)</option>
                                <option value="supplier">Tedarikçi (Satıcı)</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="small text-muted">Firma Ünvanı / Ad Soyad <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control fw-bold" required>
                        </div>
                        <div class="col-md-6"><label class="small text-muted">Vergi Dairesi</label><input type="text" name="tax_office" class="form-control"></div>
                        <div class="col-md-6"><label class="small text-muted">Vergi No</label><input type="text" name="tax_id" class="form-control"></div>
                        <div class="col-md-6"><label class="small text-muted">Telefon</label><input type="text" name="phone" class="form-control"></div>
                        <div class="col-md-6"><label class="small text-muted">E-Posta</label><input type="email" name="email" class="form-control"></div>
                        <div class="col-12"><label class="small text-muted">Adres</label><textarea name="address" class="form-control" rows="2"></textarea></div>
                        <div class="col-md-6"><label class="small text-muted">Risk Limiti</label><input type="number" name="risk_limit" class="form-control" placeholder="0.00"></div>
                        <div class="col-md-6"><label class="small text-muted">IBAN</label><input type="text" name="iban" class="form-control"></div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">KAYDET</button>
                </div>
            </form>
        </div>
    </div>
</div>