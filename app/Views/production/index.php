<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h2 class="fw-bold text-white"><i class="fa-solid fa-industry me-2 text-warning"></i> Üretim Operasyon Merkezi</h2>
        <p class="text-muted">Reçete bazlı üretim planlama ve stok simülasyonu.</p>
    </div>
    <a href="/stock/create_page" class="btn btn-outline-warning">
        <i class="fa-solid fa-plus"></i> Yeni Mamul Tanımla
    </a>
</div>

<div class="row g-4">
    <?php foreach ($producibles as $p): ?>
        <?php
        // Basit Hesaplama (Controller'dan gelmesi daha iyi olurdu ama görsel için burada yapıyoruz)
        $db = (new Database())->getConnection();
        $prodModel = new Production($db);
        $recipe = $prodModel->getRecipe($p['id']);

        $maxProduction = 999999;
        $hasRecipe = count($recipe) > 0;
        $missingItem = null; // Eksik olan ilk malzeme

        if ($hasRecipe) {
            foreach ($recipe as $item) {
                if ($item['quantity'] > 0) {
                    $canMake = floor($item['current_stock'] / $item['quantity']);
                    if ($canMake < $maxProduction) {
                        $maxProduction = $canMake;
                        if ($canMake == 0) $missingItem = $item['name'];
                    }
                }
            }
        } else {
            $maxProduction = 0;
        }

        // Progress Bar Rengi ve Doluluk Hesabı (Görsel show için 100 üzerinden normalize et)
        // Varsayalım ki hedef stok 50 olsun (Görsel amaçlı)
        $target = 50;
        $percent = ($maxProduction > 100) ? 100 : $maxProduction;
        $barColor = ($maxProduction > 0) ? 'bg-success' : 'bg-danger';
        if ($maxProduction > 0 && $maxProduction < 10) $barColor = 'bg-warning';
        ?>

        <div class="col-md-6 col-xl-4">
            <div class="card h-100 border-0 shadow-lg position-relative overflow-hidden">
                <div class="position-absolute top-0 start-0 bottom-0 bg-gradient <?= $hasRecipe ? ($maxProduction > 0 ? 'bg-success' : 'bg-danger') : 'bg-secondary' ?>" style="width: 6px;"></div>

                <div class="card-body ps-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="fw-bold text-white mb-1"><?= $p['name'] ?></h5>
                            <span class="badge bg-dark border border-secondary text-muted"><?= $p['code'] ?></span>
                            <span class="badge <?= ($p['product_type'] == 'semi') ? 'bg-info text-dark' : 'bg-primary' ?> ms-1">
                                <?= ($p['product_type'] == 'semi') ? 'Yarı Mamul' : 'Tam Mamul' ?>
                            </span>
                        </div>

                        <div class="dropdown">
                            <button class="btn btn-sm btn-dark" data-bs-toggle="dropdown"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item" href="/production/recipe/<?= $p['id'] ?>"><i class="fa-solid fa-diagram-project me-2"></i> Reçeteyi Düzenle</a></li>
                                <li><a class="dropdown-item text-danger" href="#">Pasife Al</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Üretim Kapasitesi</span>
                            <span class="fw-bold text-white"><?= $hasRecipe ? $maxProduction . ' Adet' : 'Reçete Yok' ?></span>
                        </div>
                        <div class="progress bg-dark border border-secondary" style="height: 10px;">
                            <div class="progress-bar <?= $barColor ?> progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $hasRecipe ? $percent : 0 ?>%"></div>
                        </div>
                        <?php if ($hasRecipe && $maxProduction == 0): ?>
                            <small class="text-danger mt-1 d-block"><i class="fa-solid fa-triangle-exclamation"></i> Eksik: <?= $missingItem ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="/production/builder/<?= $p['id'] ?>" class="btn btn-outline-info btn-sm flex-fill fw-bold">
                            <i class="fa-solid fa-sitemap"></i> AĞAÇ OLUŞTUR
                        </a>

                        <?php if ($hasRecipe && $maxProduction > 0): ?>
                            <button class="btn btn-warning btn-sm flex-fill fw-bold" onclick="openProduceModal(<?= $p['id'] ?>, '<?= $p['name'] ?>', <?= $maxProduction ?>)">
                                <i class="fa-solid fa-gears"></i> ÜRETİM YAP
                            </button>
                        <?php else: ?>
                            <button class="btn btn-secondary btn-sm flex-fill" disabled>
                                <i class="fa-solid fa-ban"></i> Üretilemez
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (empty($producibles)): ?>
        <div class="col-12 text-center py-5">
            <i class="fa-solid fa-box-open fa-3x text-muted mb-3"></i>
            <p class="text-muted">Henüz üretilebilir (Mamul/Yarı Mamul) ürün tanımlanmamış.</p>
            <a href="/stock/create_page" class="btn btn-primary">Tanımla</a>
        </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="produceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark border-warning">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-warning fw-bold"><i class="fa-solid fa-bolt me-2"></i> Üretim Emri</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="/production/produce" method="POST">
                <div class="modal-body text-center">
                    <input type="hidden" name="product_id" id="prodId">

                    <div class="mb-4">
                        <span class="badge bg-warning text-dark mb-2">Hedef Ürün</span>
                        <h3 class="text-white fw-bold" id="prodName">...</h3>
                    </div>

                    <div class="row justify-content-center mb-4">
                        <div class="col-6">
                            <label class="small text-muted mb-2">Üretilecek Miktar</label>
                            <div class="input-group input-group-lg">
                                <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('prodQty').stepDown()"><i class="fa-solid fa-minus"></i></button>
                                <input type="number" name="quantity" id="prodQty" class="form-control text-center bg-dark text-white fw-bold border-secondary" value="1" min="1">
                                <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('prodQty').stepUp()"><i class="fa-solid fa-plus"></i></button>
                            </div>
                            <small class="text-success mt-1 d-block">Maksimum: <span id="maxQtyDisplay"></span> Adet</small>
                        </div>
                    </div>

                    <div class="alert alert-secondary bg-opacity-25 border-0 small text-start">
                        <i class="fa-solid fa-info-circle me-1"></i>
                        Bu işlem sonucunda reçetedeki ham maddeler stoktan otomatik düşülecek ve mamul stoğa eklenecektir.
                    </div>
                </div>
                <div class="modal-footer border-secondary justify-content-center">
                    <button type="submit" class="btn btn-warning fw-bold px-5 py-2 w-100">ONAYLA VE ÜRET</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openProduceModal(id, name, max) {
        document.getElementById('prodId').value = id;
        document.getElementById('prodName').innerText = name;
        document.getElementById('maxQtyDisplay').innerText = max;
        document.getElementById('prodQty').max = max;
        document.getElementById('prodQty').value = 1;
        new bootstrap.Modal(document.getElementById('produceModal')).show();
    }
</script>