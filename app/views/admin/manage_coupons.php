<h3><i class="bi bi-ticket-perc"></i> Genel Kupon Yönetimi (Admin)</h3>
<p>Burada oluşturulan kuponlar tüm firmalarda geçerlidir.</p>
<hr>
<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header">Yeni Genel Kupon Ekle</div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>admin/coupons" method="POST">
                    <input type="hidden" name="action" value="add_coupon">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-2">
                        <label class="form-label">Kupon Kodu</label>
                        <input type="text" name="code" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">İndirim Oranı (Örn: 0.10 = %10)</label>
                        <input type="number" step="0.01" min="0.01" max="1.00" name="discount_rate" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Kullanım Limiti</label>
                        <input type="number" min="1" name="usage_limit" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Son Kullanma Tarihi</label>
                        <input type="date" name="expiration_date" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Kupon Ekle</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
         <div class="card shadow-sm">
             <div class="card-header">Mevcut Genel Kuponlar</div>
             <div class="card-body">
                <div class="list-group">
                    <?php if (empty($coupons)): ?>
                        <p class="text-muted">Genel kupon yok.</p>
                    <?php else: ?>
                        <?php foreach ($coupons as $coupon): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="text-primary fs-5"><?php echo e($coupon['code']); ?></strong><br>
                                    <small>
                                        %<?php echo e($coupon['discount_rate'] * 100); ?> İndirim | 
                                        SKT: <?php echo e(date('d.m.Y', strtotime($coupon['expiration_date']))); ?> |
                                        Kullanım: <?php echo e($coupon['usage_count']); ?>/<?php echo e($coupon['usage_limit']); ?>
                                    </small>
                                </div>
                                <a href="<?php echo BASE_URL . 'admin/coupons?action=delete&id=' . $coupon['id']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Bu kuponu silmek istediğinizden emin misiniz?');">
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