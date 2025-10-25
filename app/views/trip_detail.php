<style>
    .seat { width: 50px; height: 50px; margin: 5px; border-radius: 5px; cursor: pointer; }
    .seat.booked { background-color: #dc3545 !important; cursor: not-allowed; }
    input[name="seat_number"] { display: none; }
    input[name="seat_number"]:checked + label { background-color: #198754 !important; color: white; }
</style>

<h3>Sefer Detayları ve Koltuk Seçimi</h3>
<hr>
<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title"><?php echo e($trip['departure_city']); ?> &rarr; <?php echo e($trip['arrival_city']); ?></h5>
        <p class="card-text">
            <strong>Firma:</strong> <?php echo e($trip['company_name']); ?><br>
            <strong>Kalkış:</strong> <?php echo date('d.m.Y H:i', strtotime($trip['departure_time'])); ?><br>
            <strong>Fiyat:</strong> <span class="text-success fw-bold"><?php echo e(number_format($trip['price'], 2)); ?> TL</span>
        </p>
    </div>
</div>

<form action="<?php echo BASE_URL . 'trip/buy/' . $trip['id']; ?>" method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <div class="card">
        <div class="card-header">Koltuk Seçimi</div>
        <div class="card-body">
            <p>Lütfen koltuğunuzu seçin. (Kırmızılar dolu koltuklardır)</p>
            <div class="d-flex flex-wrap justify-content-center">
                <?php for ($i = 1; $i <= $trip['seat_count']; $i++): ?>
                    <?php $is_booked = in_array($i, $booked_seats); ?>
                    
                    <input type="radio" name="seat_number" id="seat-<?php echo $i; ?>" value="<?php echo $i; ?>" <?php if ($is_booked) echo 'disabled'; ?> required>
                    <label 
                        for="seat-<?php echo $i; ?>" 
                        class="btn btn-outline-secondary seat d-flex align-items-center justify-content-center <?php if ($is_booked) echo 'booked'; ?>"
                        title="<?php if ($is_booked) echo 'Dolu'; else echo 'Koltuk ' . $i; ?>">
                        <?php echo $i; ?>
                    </label>
                <?php endfor; ?>
            </div>
        </div>
        <div class="card-footer">
            <div class="row align-items-end">
                <div class="col-md-6">
                    <label for="coupon_code" class="form-label">İndirim Kuponu</label>
                    <input type="text" name="coupon_code" id="coupon_code" class="form-control" placeholder="Varsa kupon kodunuzu girin">
                </div>
                <div class="col-md-6 text-end">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <button type="submit" class="btn btn-success btn-lg">Satın Al</button>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>login" class="btn btn-warning btn-lg">Bilet Almak İçin Lütfen Giriş Yapın</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</form>