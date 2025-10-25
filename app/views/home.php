<div class="p-5 mb-4 bg-light rounded-3">
    <div class="container-fluid py-5">
        <h1 class="display-5 fw-bold">Güvenli ve Hızlı Biletin Adresi</h1>
        <p class="col-md-8 fs-4">Gitmek istediğiniz yeri seçin, yolculuğunuz başlasın.</p>
    </div>
</div>

<div class="card mb-4 shadow-sm">
    <div class="card-header bg-dark text-white">
        <h4 class="mb-0"><i class="bi bi-search"></i> Sefer Arama</h4>
    </div>
    <div class="card-body">
        <form action="<?php echo BASE_URL; ?>" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="departure_city" class="form-label fw-bold">Kalkış Noktası</label>
                    <input type="text" id="departure_city" name="departure_city" class="form-control" placeholder="Örn: İstanbul" value="<?php echo e($_GET['departure_city'] ?? ''); ?>">
                </div>
                <div class="col-md-5">
                    <label for="arrival_city" class="form-label fw-bold">Varış Noktası</label>
                    <input type="text" id="arrival_city" name="arrival_city" class="form-control" placeholder="Örn: Ankara" value="<?php echo e($_GET['arrival_city'] ?? ''); ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Sefer Bul</button>
                </div>
            </div>
        </form>
    </div>
</div>

<h3>Arama Sonuçları</h3>
<hr>
<div class="list-group">
    <?php if (empty($trips)): ?>
        <p class="text-center text-muted">Arama kriterlerinize uygun sefer bulunamadı.</p>
    <?php else: ?>
        <?php foreach ($trips as $trip): ?>
            <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center shadow-sm mb-2">
                <div>
                    <h5 class="mb-1">
                        <i class="bi bi-geo-alt-fill"></i> <?php echo e($trip['departure_city']); ?> &rarr; <?php echo e($trip['arrival_city']); ?>
                    </h5>
                    <p class="mb-1">
                        <small class="text-muted">
                            <i class="bi bi-building"></i> <?php echo e($trip['company_name']); ?> | 
                            <i class="bi bi-calendar-event"></i> <?php echo date('d.m.Y H:i', strtotime($trip['departure_time'])); ?>
                        </small>
                    </p>
                </div>
                <div class="text-end">
                    <h4 class="text-success mb-2"><?php echo e(number_format($trip['price'], 2)); ?> TL</h4>
                    <a href="<?php echo BASE_URL . 'trip/' . $trip['id']; ?>" class="btn btn-danger">Koltuk Seç</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>