<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-white mb-0"><?= $mainProduct['name'] ?></h4>
            <span class="text-muted small">Üretim Reçetesi (BOM)</span>
        </div>
        <a href="/production/index" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left"></i> Geri</a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-3 border-primary bg-primary bg-opacity-10 text-center py-4">
                <div class="card-body">
                    <i class="fa-solid fa-box-open fa-3x text-primary mb-2"></i>
                    <h3 class="fw-bold text-white"><?= $mainProduct['name'] ?></h3>
                    <span class="badge bg-primary">Hedef Ürün (1 Adet)</span>
                </div>
            </div>

            <div class="text-center my-2">
                <i class="fa-solid fa-arrow-down fa-2x text-muted opacity-50"></i>
            </div>

            <div class="card mb-4 border-warning bg-warning bg-opacity-10 text-center py-2">
                <div class="card-body p-2">
                    <h5 class="fw-bold text-warning m-0"><i class="fa-solid fa-gears"></i> MONTAJ / ÜRETİM HATTI</h5>
                </div>
            </div>

            <div class="text-center my-2">
                <i class="fa-solid fa-arrow-down fa-2x text-muted opacity-50"></i>
            </div>

            <h6 class="text-muted text-uppercase small fw-bold mb-3 ps-2 border-start border-4 border-warning">Gereken Bileşenler (Input)</h6>

            <div class="card border-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle mb-0">
                        <thead class="bg-secondary bg-opacity-25">
                            <tr>
                                <th>Bileşen</th>
                                <th class="text-center">Miktar</th>
                                <th class="text-center">Mevcut Stok</th>
                                <th class="text-end">Maliyet</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalCost = 0;
                            foreach ($recipe as $item):
                                $cost = $item['quantity'] * $item['buy_price'];
                                $totalCost += $cost;
                                $hasStock = $item['current_stock'] >= $item['quantity'];
                            ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-dark rounded p-2 me-3 text-warning"><i class="fa-solid fa-puzzle-piece"></i></div>
                                            <div>
                                                <div class="fw-bold"><?= $item['name'] ?></div>
                                                <small class="text-muted"><?= $item['code'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="fs-5 fw-bold text-white"><?= $item['quantity'] ?></span> <small class="text-muted"><?= $item['unit'] ?></small>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($hasStock): ?>
                                            <span class="badge bg-success"><i class="fa-solid fa-check"></i> <?= $item['current_stock'] ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><i class="fa-solid fa-xmark"></i> <?= $item['current_stock'] ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end text-muted"><?= number_format($cost, 2) ?> ₺</td>
                                    <td class="text-end">
                                        <a href="/production/delete_ingredient?id=<?= $item['id'] ?>&pid=<?= $mainProduct['id'] ?>" class="text-danger" onclick="return confirm('Sil?')"><i class="fa-solid fa-trash-can"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="border-top border-secondary">
                            <tr>
                                <td colspan="3" class="text-end text-muted small text-uppercase pt-3">Tahmini Birim Maliyet:</td>
                                <td class="text-end text-success fw-bold fs-4 pt-3"><?= number_format($totalCost, 2) ?> ₺</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow bg-dark">
                <div class="card-header bg-warning text-black fw-bold">
                    <i class="fa-solid fa-plus-circle me-1"></i> Bileşen Ekle
                </div>
                <div class="card-body">
                    <form action="/production/add_ingredient" method="POST">
                        <input type="hidden" name="product_id" value="<?= $mainProduct['id'] ?>">

                        <div class="mb-3">
                            <label class="form-label small text-muted">Ham Madde Seçin</label>
                            <select name="ingredient_id" class="form-select bg-black text-white border-secondary" required>
                                <option value="">Listeden Seçiniz...</option>
                                <?php foreach ($product as $p): ?>
                                    <?php if ($p['id'] != $mainProduct['id']): ?>
                                        <option value="<?= $p['id'] ?>">
                                            <?= $p['name'] ?> (Stok: <?= $p['current_stock'] ?>)
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small text-muted">Miktar (1 Üretim İçin)</label>
                            <input type="number" step="0.0001" name="quantity" class="form-control fw-bold fs-4 text-center bg-black text-white border-secondary" placeholder="0" required>
                        </div>

                        <button type="submit" class="btn btn-outline-warning w-100 fw-bold">LİSTEYE EKLE</button>
                    </form>
                </div>
            </div>

            <div class="alert alert-secondary mt-3 small">
                <i class="fa-solid fa-lightbulb me-1"></i>
                İpucu: Yarı mamulleri de reçeteye ekleyebilirsiniz. Sistem stoğu kontrol ederken yarı mamulleri de dikkate alır.
            </div>
        </div>
    </div>
</div>