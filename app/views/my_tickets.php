<h3><i class="bi bi-ticket-detailed"></i> Biletlerim</h3>
<hr>

<div class="list-group">
    <?php if (empty($tickets)): ?>
        <p class="text-center text-muted">Hiç bilet satın almamışsınız.</p>
    <?php else: ?>
        <?php foreach ($tickets as $ticket): ?>
            <div class="list-group-item shadow-sm mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-1"><?php echo e($ticket['departure_city']); ?> &rarr; <?php echo e($ticket['arrival_city']); ?></h5>
                        <p class="mb-1">
                            <strong>Firma:</strong> <?php echo e($ticket['company_name']); ?><br>
                            <strong>Kalkış:</strong> <?php echo date('d.m.Y H:i', strtotime($ticket['departure_time'])); ?><br>
                            <strong>Koltuk No:</strong> <span class="badge bg-dark fs-6"><?php echo e($ticket['seat_number']); ?></span>
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p>
                            <strong>Durum:</strong> 
                            <?php if ($ticket['status'] == 'ACTIVE'): ?>
                                <span class="badge bg-success">AKTİF</span>
                            <?php else: ?>
                                <span class="badge bg-danger">İPTAL EDİLMİŞ</span>
                            <?php endif; ?>
                        </p>
                        <p><strong>Ödenen:</strong> <?php echo e(number_format($ticket['price_paid'], 2)); ?> TL</p>
                        
                        <?php if ($ticket['status'] == 'ACTIVE'): ?>
                            <a href="<?php echo BASE_URL . 'account/tickets/cancel/' . $ticket['id']; ?>" 
                               class="btn btn-warning btn-sm"
                               [cite_start]onclick="return confirm('Bu bileti iptal etmek istediğinizden emin misiniz? (Son 1 saat kuralı [cite: 23] geçerlidir)');">
                               <i class="bi bi-x-circle"></i> İptal Et
                            </a>
                            
                            <a href="<?php echo BASE_URL . 'account/tickets/pdf/' . $ticket['id']; ?>" class="btn btn-info btn-sm">
                                <i class="bi bi-file-pdf"></i> PDF İndir
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>