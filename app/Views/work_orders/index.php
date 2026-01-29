<style>
    /* TIMELINE CSS - LOGLAR İÇİN */
    .timeline {
        position: relative;
        padding-left: 20px;
        border-left: 2px solid #444;
        margin-left: 10px;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -26px;
        top: 0;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #ff6600;
        border: 2px solid #1e1e1e;
    }

    .timeline-date {
        font-size: 0.75rem;
        color: #888;
        margin-bottom: 3px;
    }

    .timeline-content {
        background: #2b2b2b;
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #444;
    }

    .timeline-content h6 {
        margin: 0;
        font-weight: bold;
        color: #fff;
    }

    .timeline-content p {
        margin: 5px 0 0;
        font-size: 0.9rem;
        color: #ccc;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-white"><i class="fa-solid fa-clipboard-list me-2 text-warning"></i> İş Emirleri Takibi</h2>
        <p class="text-muted">Üretim planlama, operasyon süreçleri ve aktivite logları.</p>
    </div>
    <button class="btn btn-warning fw-bold shadow" data-bs-toggle="modal" data-bs-target="#newOrderModal">
        <i class="fa-solid fa-plus"></i> YENİ İŞ EMRİ OLUŞTUR
    </button>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card p-3 border-start border-4 border-secondary bg-dark shadow-sm">
            <h6 class="text-muted small text-uppercase">Bekleyen</h6>
            <h3 class="text-white fw-bold">
                <?= count(array_filter($orders, fn($o) => $o['status'] == 'planned')) ?>
            </h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 border-start border-4 border-primary bg-dark shadow-sm">
            <h6 class="text-muted small text-uppercase">Üretimde</h6>
            <h3 class="text-primary fw-bold">
                <?= count(array_filter($orders, fn($o) => $o['status'] == 'started')) ?>
            </h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 border-start border-4 border-success bg-dark shadow-sm">
            <h6 class="text-muted small text-uppercase">Tamamlanan (Toplam)</h6>
            <h3 class="text-success fw-bold">
                <?= count(array_filter($orders, fn($o) => $o['status'] == 'completed')) ?>
            </h3>
        </div>
    </div>
</div>

<div class="card border-0 shadow bg-dark">
    <div class="card-body">
        <table class="table table-dark table-hover align-middle datatable w-100">
            <thead>
                <tr>
                    <th>Emir Kodu</th>
                    <th>Ürün</th>
                    <th>Öncelik</th>
                    <th>Takvim</th>
                    <th>Durum</th>
                    <th class="text-end">Aksiyonlar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td>
                            <span class="badge bg-secondary font-monospace"><?= $o['order_code'] ?></span>
                            <div class="small text-muted mt-1" style="font-size:0.7rem;">
                                <?= date('d.m.Y H:i', strtotime($o['created_at'])) ?>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold fs-5 text-white"><?= $o['product_name'] ?></div>
                            <div class="text-warning fw-bold"><?= $o['quantity'] ?> <small><?= $o['unit'] ?></small></div>
                        </td>
                        <td>
                            <?php
                            $prioColors = ['low' => 'secondary', 'normal' => 'info', 'high' => 'warning', 'urgent' => 'danger'];
                            $prioNames = ['low' => 'Düşük', 'normal' => 'Normal', 'high' => 'Yüksek', 'urgent' => 'ACİL'];
                            ?>
                            <span class="badge bg-<?= $prioColors[$o['priority']] ?> text-dark"><?= $prioNames[$o['priority']] ?></span>
                        </td>
                        <td>
                            <div class="small text-muted"><i class="fa-regular fa-calendar me-1"></i> Baş: <?= date('d.m.Y', strtotime($o['planned_start_date'])) ?></div>
                            <div class="small text-muted"><i class="fa-solid fa-flag-checkered me-1"></i> Bit: <?= date('d.m.Y', strtotime($o['planned_end_date'])) ?></div>
                        </td>
                        <td>
                            <?php if ($o['status'] == 'planned'): ?>
                                <span class="badge bg-secondary text-white p-2 border border-secondary">PLANLANDI</span>
                            <?php elseif ($o['status'] == 'started'): ?>
                                <div class="progress" style="height: 20px; width: 100px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 60%">Üretiliyor</div>
                                </div>
                            <?php elseif ($o['status'] == 'completed'): ?>
                                <span class="badge bg-success p-2"><i class="fa-solid fa-check-circle"></i> BİTTİ</span>
                            <?php else: ?>
                                <span class="badge bg-danger">İPTAL</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">

                            <button class="btn btn-sm btn-outline-light me-1" onclick="showLogs(<?= $o['id'] ?>, '<?= $o['order_code'] ?>')">
                                <i class="fa-solid fa-history"></i> Log
                            </button>

                            <?php if ($o['status'] == 'planned'): ?>
                                <a href="/workorder/change_status?id=<?= $o['id'] ?>&status=started" class="btn btn-sm btn-outline-primary fw-bold">
                                    <i class="fa-solid fa-play"></i> BAŞLAT
                                </a>
                                <a href="/workorder/change_status?id=<?= $o['id'] ?>&status=cancelled" class="btn btn-sm btn-outline-danger" onclick="return confirm('İptal edilecek?')">
                                    <i class="fa-solid fa-ban"></i>
                                </a>

                            <?php elseif ($o['status'] == 'started'): ?>
                                <a href="/workorder/change_status?id=<?= $o['id'] ?>&status=completed" class="btn btn-sm btn-success fw-bold shadow" onclick="return confirm('Üretim tamamlanacak ve stoklara işlenecek. Onaylıyor musunuz?')">
                                    <i class="fa-solid fa-check"></i> BİTİR
                                </a>

                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="newOrderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-warning"><i class="fa-solid fa-file-signature me-2"></i> Yeni İş Emri</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="/workorder/create" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="small text-muted">Üretilecek Mamul</label>
                        <select name="product_id" class="form-select bg-black text-white fw-bold border-secondary" required>
                            <?php foreach ($producibles as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= $p['name'] ?> (Stok: <?= $p['current_stock'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="small text-muted">Hedef Miktar</label>
                            <input type="number" step="0.01" name="quantity" class="form-control fw-bold fs-4 text-center bg-black text-warning border-secondary" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted">Öncelik</label>
                            <select name="priority" class="form-select bg-black text-white border-secondary">
                                <option value="low">Düşük</option>
                                <option value="normal" selected>Normal</option>
                                <option value="high">Yüksek</option>
                                <option value="urgent">ACİL!</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="small text-muted">Planlanan Başlama</label>
                            <input type="date" name="planned_start_date" class="form-control bg-dark text-white border-secondary" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted">Planlanan Bitiş</label>
                            <input type="date" name="planned_end_date" class="form-control bg-dark text-white border-secondary" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted">Notlar / Açıklama</label>
                        <textarea name="notes" class="form-control bg-dark text-white border-secondary" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="submit" class="btn btn-warning w-100 fw-bold">EMRİ OLUŞTUR</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="logModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white">Süreç Geçmişi: <span id="logOrderCode" class="text-warning font-monospace"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="timeline" id="timelineContent">
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showLogs(id, code) {
        document.getElementById('logOrderCode').innerText = code;
        const timeline = document.getElementById('timelineContent');
        timeline.innerHTML = '<div class="text-center text-muted">Yükleniyor...</div>';

        new bootstrap.Modal(document.getElementById('logModal')).show();

        fetch('/workorder/get_logs?id=' + id)
            .then(res => res.json())
            .then(data => {
                timeline.innerHTML = '';
                if (data.length === 0) {
                    timeline.innerHTML = '<div class="text-center text-muted">Kayıt bulunamadı.</div>';
                    return;
                }

                data.forEach(log => {
                    // Log tipine göre renk ve başlık belirle
                    let color = '#888';
                    let title = 'İşlem';
                    if (log.action === 'created') {
                        color = '#0d6efd';
                        title = 'Oluşturuldu';
                    }
                    if (log.action === 'started') {
                        color = '#ffc107';
                        title = 'Başlatıldı';
                    }
                    if (log.action === 'completed') {
                        color = '#198754';
                        title = 'Tamamlandı';
                    }
                    if (log.action === 'cancelled') {
                        color = '#dc3545';
                        title = 'İptal Edildi';
                    }

                    const date = new Date(log.created_at).toLocaleString('tr-TR');

                    timeline.innerHTML += `
                        <div class="timeline-item">
                            <style>.timeline-item::before { background: ${color} !important; }</style>
                            <div class="timeline-date">${date} - ${log.user_name}</div>
                            <div class="timeline-content" style="border-left: 3px solid ${color}">
                                <h6 style="color:${color}">${title}</h6>
                                <p>${log.description}</p>
                            </div>
                        </div>
                    `;
                });
            });
    }
</script>