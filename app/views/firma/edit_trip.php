<h3><i class="bi bi-pencil"></i> Sefer Düzenle (Firma Admin)</h3>
<hr>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header">Sefer Bilgilerini Güncelle</div>
            <div class="card-body">
                <form action="<?php echo BASE_URL . 'firma/trips/update/' . $trip['id']; ?>" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-2">
                        <label class="form-label">Kalkış Yeri</label>
                        <input type="text" name="departure_city" class="form-control" value="<?php echo e($trip['departure_city']); ?>" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Varış Yeri</label>
                        <input type="text" name="arrival_city" class="form-control" value="<?php echo e($trip['arrival_city']); ?>" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Kalkış Zamanı</label>
                        <input type="datetime-local" name="departure_time" class="form-control" value="<?php echo e(date('Y-m-d\TH:i', strtotime($trip['departure_time']))); ?>" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Varış Zamanı</label>
                        <input type="datetime-local" name="arrival_time" class="form-control" value="<?php echo e(date('Y-m-d\TH:i', strtotime($trip['arrival_time']))); ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-2">
                             <label class="form-label">Fiyat (TL)</label>
                            <input type="number" step="0.01" name="price" class="form-control" value="<?php echo e($trip['price']); ?>" required>
                        </div>
                         <div class="col-6 mb-2">
                             <label class="form-label">Koltuk Sayısı</label>
                            <input type="number" name="seat_count" class="form-control" value="<?php echo e($trip['seat_count']); ?>" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Seferi Güncelle</button>
                </form>
            </div>
        </div>
    </div>
</div>