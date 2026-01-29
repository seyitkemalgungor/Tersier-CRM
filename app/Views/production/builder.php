<!DOCTYPE html>
<style>
    /* AĞAÇ GÖRÜNÜMÜ İÇİN CSS (Organizasyon Şeması Gibi) */
    .tree ul {
        padding-top: 20px;
        position: relative;
        transition: all 0.5s;
        -webkit-transition: all 0.5s;
        -moz-transition: all 0.5s;
        display: flex;
        justify-content: center;
    }

    .tree li {
        float: left;
        text-align: center;
        list-style-type: none;
        position: relative;
        padding: 20px 5px 0 5px;
        transition: all 0.5s;
    }

    /* Çizgiler */
    .tree li::before,
    .tree li::after {
        content: '';
        position: absolute;
        top: 0;
        right: 50%;
        border-top: 2px solid #555;
        width: 50%;
        height: 20px;
    }

    .tree li::after {
        right: auto;
        left: 50%;
        border-left: 2px solid #555;
    }

    .tree li:only-child::after,
    .tree li:only-child::before {
        display: none;
    }

    .tree li:only-child {
        padding-top: 0;
    }

    .tree li:first-child::before,
    .tree li:last-child::after {
        border: 0 none;
    }

    .tree li:last-child::before {
        border-right: 2px solid #555;
        border-radius: 0 5px 0 0;
    }

    .tree li:first-child::after {
        border-radius: 5px 0 0 0;
    }

    .tree ul ul::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        border-left: 2px solid #555;
        width: 0;
        height: 20px;
    }

    /* KART TASARIMI */
    .node-card {
        border: 2px solid #444;
        padding: 10px;
        text-decoration: none;
        color: #fff;
        background-color: #1e1e1e;
        display: inline-block;
        border-radius: 5px;
        transition: all 0.5s;
        min-width: 150px;
        position: relative;
        cursor: grab;
    }

    .node-card:hover {
        border-color: #ff6600;
        background: #252525;
    }

    .node-card .qty-badge {
        position: absolute;
        top: -10px;
        right: -10px;
        background: #ff6600;
        color: white;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.8rem;
    }

    .node-card .del-btn {
        position: absolute;
        bottom: -10px;
        right: 50%;
        transform: translateX(50%);
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        cursor: pointer;
    }

    .node-card:hover .del-btn {
        display: flex;
    }

    /* SOL MENÜ (Malzeme Listesi) */
    .material-list {
        max-height: 80vh;
        overflow-y: auto;
        background: #111;
        border: 1px solid #333;
        border-radius: 8px;
        padding: 10px;
    }

    .material-item {
        padding: 10px;
        margin-bottom: 5px;
        background: #222;
        border: 1px solid #333;
        border-radius: 4px;
        cursor: grab;
        display: flex;
        justify-content: space-between;
    }

    .material-item:hover {
        background: #333;
        border-color: #555;
    }

    /* SÜRÜKLEME EFEKTLERİ */
    .sortable-ghost {
        opacity: 0.4;
        background: #ff6600;
    }

    .drop-zone {
        min-height: 100px;
        border: 2px dashed #444;
        border-radius: 10px;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 20px;
    }

    .drop-active {
        border-color: #28a745;
        background: rgba(40, 167, 69, 0.1);
    }
</style>

<div class="row h-100">
    <div class="col-md-3">
        <h5 class="text-white mb-3"><i class="fa-solid fa-boxes-stacked me-2"></i> Malzemeler</h5>
        <div class="input-group mb-2">
            <input type="text" id="searchBox" class="form-control bg-dark text-white border-secondary" placeholder="Malzeme ara...">
        </div>

        <div id="materialSource" class="material-list">
            <?php foreach ($materials as $m): ?>
                <div class="material-item" data-id="<?= $m['id'] ?>" data-name="<?= $m['name'] ?>" data-unit="<?= $m['unit'] ?>">
                    <div>
                        <strong class="d-block text-white small"><?= $m['name'] ?></strong>
                        <span class="badge bg-secondary" style="font-size:0.6rem;"><?= $m['code'] ?></span>
                    </div>
                    <div class="text-muted small"><?= $m['current_stock'] ?> <?= $m['unit'] ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="alert alert-info mt-2 small">
            <i class="fa-solid fa-hand-pointer"></i> Listeden ürünü tutup sağdaki ağaca sürükleyin.
        </div>
    </div>

    <div class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-white fw-bold">
                <?= $mainProduct['name'] ?> <span class="text-muted fs-6">Üretim Ağacı</span>
            </h4>
            <a href="/production/index" class="btn btn-outline-light btn-sm">Geri Dön</a>
        </div>

        <div class="card bg-dark border-secondary h-100" style="min-height: 600px; overflow: auto;">
            <div class="card-body d-flex justify-content-center">

                <div class="tree">
                    <ul>
                        <li>
                            <div class="node-card border-warning" style="min-width: 200px;">
                                <i class="fa-solid fa-star text-warning mb-2"></i><br>
                                <strong class="fs-5"><?= $mainProduct['name'] ?></strong><br>
                                <span class="text-muted small">Hedef Ürün (1 <?= $mainProduct['unit'] ?>)</span>
                            </div>

                            <ul id="treeDropZone" class="drop-zone-container">
                                <?php if (empty($recipe)): ?>
                                    <div class="text-muted small mt-3 empty-placeholder">Malzemeleri buraya sürükleyin...</div>
                                <?php else: ?>
                                    <?php foreach ($recipe as $item): ?>
                                        <li class="tree-item" data-id="<?= $item['ingredient_id'] ?>">
                                            <div class="node-card">
                                                <div class="qty-badge"><?= floatval($item['quantity']) ?></div>
                                                <i class="fa-solid fa-cube text-info mb-1"></i><br>
                                                <strong><?= $item['name'] ?></strong><br>
                                                <small class="text-muted"><?= $item['code'] ?></small>
                                                <button class="del-btn" onclick="removeNode(this, <?= $item['ingredient_id'] ?>)">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    const mainProductId = <?= $mainProduct['id'] ?>;

    // 1. SOL TARAF (KAYNAK)
    new Sortable(document.getElementById('materialSource'), {
        group: {
            name: 'shared',
            pull: 'clone', // Kopyası gider, aslı kalır
            put: false // Buraya geri bırakılamaz
        },
        sort: false, // Liste içinde sıralama kapalı
        animation: 150
    });

    // 2. SAĞ TARAF (HEDEF - AĞAÇ)
    const treeZone = document.getElementById('treeDropZone');

    new Sortable(treeZone, {
        group: 'shared',
        animation: 150,
        ghostClass: 'sortable-ghost',

        onAdd: function(evt) {
            const item = evt.item;
            const ingredientId = item.getAttribute('data-id');
            const name = item.getAttribute('data-name');
            const unit = item.getAttribute('data-unit');

            // Sürüklenen elemanı hemen gizle (Biz kendi güzel HTML'imizi koyacağız)
            item.style.display = 'none';

            // POPUP AÇ: Miktar Sor
            Swal.fire({
                title: name,
                text: `1 adet ${'<?= $mainProduct['name'] ?>'} üretmek için kaç ${unit} ${name} gerekir?`,
                input: 'number',
                inputAttributes: {
                    step: '0.01',
                    min: '0'
                },
                showCancelButton: true,
                confirmButtonText: 'Ekle',
                cancelButtonText: 'İptal',
                background: '#1e1e1e',
                color: '#fff'
            }).then((result) => {
                if (result.isConfirmed && result.value > 0) {
                    // AJAX İLE KAYDET
                    saveIngredient(ingredientId, result.value, item);
                } else {
                    // İptal edilirse sürüklenen hayaleti sil
                    item.remove();
                }
            });
        }
    });

    // KAYDETME FONKSİYONU
    function saveIngredient(ingredientId, qty, ghostItem) {
        fetch('/production/ajax_add_ingredient', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: mainProductId,
                    ingredient_id: ingredientId,
                    quantity: qty
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    // Hayalet öğeyi sil, yerine güzel ağaç dalını ekle
                    ghostItem.remove();
                    addVisualNode(ingredientId, ghostItem.getAttribute('data-name'), qty, ghostItem.getAttribute('data-name')); // Basitçe ismi kullan

                    // Eğer "boş" yazısı varsa sil
                    const placeholder = document.querySelector('.empty-placeholder');
                    if (placeholder) placeholder.remove();

                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        background: '#198754',
                        color: '#fff'
                    });
                    Toast.fire({
                        icon: 'success',
                        title: 'Reçeteye eklendi'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata',
                        text: 'Zaten ekli olabilir.',
                        background: '#1e1e1e',
                        color: '#fff'
                    });
                    ghostItem.remove();
                }
            });
    }

    // AĞACA GÖRSEL DÜĞÜM EKLEME (Sayfa yenilenmeden)
    function addVisualNode(id, name, qty, code) {
        const li = document.createElement('li');
        li.className = 'tree-item';
        li.setAttribute('data-id', id);

        li.innerHTML = `
            <div class="node-card">
                <div class="qty-badge">${qty}</div>
                <i class="fa-solid fa-cube text-info mb-1"></i><br>
                <strong>${name}</strong><br>
                <button class="del-btn" onclick="removeNode(this, ${id})"><i class="fa-solid fa-xmark"></i></button>
            </div>
        `;
        treeDropZone.appendChild(li);
    }

    // SİLME FONKSİYONU
    function removeNode(btn, ingredientId) {
        Swal.fire({
            title: 'Silinsin mi?',
            text: "Bu malzeme reçeteden çıkarılacak.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Evet, Sil',
            background: '#1e1e1e',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                // AJAX SILME
                fetch('/production/ajax_delete_ingredient', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: mainProductId,
                            ingredient_id: ingredientId
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // HTML'den kaldır (<li> elementini bulup sil)
                            btn.closest('li').remove();
                        }
                    });
            }
        });
    }

    // ARAMA KUTUSU
    document.getElementById('searchBox').addEventListener('keyup', function() {
        const val = this.value.toLowerCase();
        const items = document.querySelectorAll('.material-item');
        items.forEach(item => {
            const text = item.innerText.toLowerCase();
            item.style.display = text.includes(val) ? 'flex' : 'none';
        });
    });
</script>