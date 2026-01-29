<h2 class="mb-4 text-white fw-bold">Genel Bakış</h2>

<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card stat-card p-4 h-100" style="border-left-color: #0d6efd;">
            <div class="d-flex justify-content-between">
                <div>
                    <h5 class="text-muted small text-uppercase">Toplam Ürün</h5>
                    <h2 class="text-white fw-bold"><?= $stats['total_products'] ?? 0 ?></h2>
                </div>
                <div class="fs-1 text-primary opacity-50"><i class="fa-solid fa-boxes-stacked"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card p-4 h-100" style="border-left-color: #198754;">
            <div class="d-flex justify-content-between">
                <div>
                    <h5 class="text-muted small text-uppercase">Toplam Stok Değeri</h5>
                    <h2 class="text-success fw-bold"><?= number_format((float)$stats['total_value'], 2) ?> ₺</h2>
                </div>
                <div class="fs-1 text-success opacity-50"><i class="fa-solid fa-sack-dollar"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card p-4 h-100" style="border-left-color: #dc3545;">
            <div class="d-flex justify-content-between">
                <div>
                    <h5 class="text-muted small text-uppercase">Kritik Seviyedeki Ürünler</h5>
                    <h2 class="text-danger fw-bold"><?= $stats['critical_count'] ?? 0 ?></h2>
                </div>
                <div class="fs-1 text-danger opacity-50"><i class="fa-solid fa-triangle-exclamation"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-transparent border-secondary py-3">
        <h5 class="m-0 text-white"><i class="fa-solid fa-clock-rotate-left me-2"></i> Son Stok Hareketleri</h5>
    </div>
    <div class="card-body p-0">
        <table class="table table-dark table-hover mb-0">
            <thead>
                <tr>
                    <th>Tarih</th>
                    <th>Ürün</th>
                    <th>İşlem</th>
                    <th>Miktar</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($stats['recent_moves'])): ?>
                    <?php foreach ($stats['recent_moves'] as $move): ?>
                        <tr>
                            <td><?= date('d.m.Y H:i', strtotime($move['created_at'])) ?></td>
                            <td><?= $move['product_name'] ?></td>
                            <td>
                                <?php
                                $badges = [
                                    'purchase' => '<span class="badge bg-success">Alış Faturası</span>',
                                    'sale' => '<span class="badge bg-primary">Satış Faturası</span>',
                                    'return_in' => '<span class="badge bg-info text-dark">İade Alım</span>',
                                    'return_out' => '<span class="badge bg-warning text-dark">İade Çıkış</span>'
                                ];
                                echo $badges[$move['process_type']] ?? '<span class="badge bg-secondary">İşlem</span>';
                                ?>
                            </td>
                            <td>
                                <?php if ($move['is_entry']): ?>
                                    <span class="text-success fw-bold">+<?= $move['quantity'] ?></span>
                                <?php else: ?>
                                    <span class="text-danger fw-bold">-<?= $move['quantity'] ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center py-3 text-muted">Henüz hareket yok.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>