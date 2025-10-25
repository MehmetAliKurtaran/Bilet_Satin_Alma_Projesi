<h3><i class="bi bi-signpost-split"></i> Sefer Yönetimi (Firma Admin) [cite: 26]</h3>
<hr>
<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm">
            [cite_start]<div class="card-header">Yeni Sefer Ekle [cite: 28]</div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>firma/trips" method="POST">
                    <input type="hidden" name="action" value="add_trip">
                    <div class="mb-2">
                        <label class="form-label">Kalkış Yeri</label>
                        <input type="text" name="departure_city" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Varış Yeri</label>
                        <input type="text" name="arrival_city" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Kalkış Zamanı</label>
                        <input type="datetime-local" name="departure_time" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Varış Zamanı</label>
                        <input type="datetime-local" name="arrival_time" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-2">
                             <label class="form-label">Fiyat (TL)</label>
                            <input type="number" step="0.01" name="price" class="form-control" required>
                        </div>
                         <div class="col-6 mb-2">
                             <label class="form-label">Koltuk Sayısı</label>
                            <input type="number" name="seat_count" class="form-control" value="40" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Seferi Ekle</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
         <div class="card shadow-sm">
             <div class="card-header">Firmanızın Seferleri</div>
             <div class="card-body">
                 <div class="list-group">
                    <?php if (empty($trips)): ?>
                        <p class="text-muted">Henüz sefer eklememişsiniz.</p>
                    <?php else: ?>
                        <?php foreach ($trips as $trip): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?php echo e($trip['departure_city']); ?> &rarr; <?php echo e($trip['arrival_city']); ?></strong>
                                    <br>
                                    <small><?php echo date('d.m.Y H:i', strtotime($trip['departure_time'])); ?> | <?php echo e($trip['price']); ?> TL</small>
                                </div>
                                <a href="<?php echo BASE_URL . 'firma/trips?action=delete&id=' . $trip['id']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Bu seferi silmek istediğinizden emin misiniz?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                 </div>
             </div>
         </div>
    </div>
</div>