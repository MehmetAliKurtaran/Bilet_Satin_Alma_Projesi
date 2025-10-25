<h3><i class="bi bi-building"></i> Firma Yönetimi (Admin)</h3>
<hr>
<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header">Yeni Firma Ekle</div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>admin/firms" method="POST">
                    <input type="hidden" name="action" value="add_firm">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label">Firma Adı</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Ekle</button>
                </form>
            </div>
        </div>
         <a href="<?php echo BASE_URL; ?>admin/create-firma-admin" class="btn btn-primary mt-3 w-100">
            <i class="bi bi-person-plus"></i> Yeni Firma Admini Oluştur
        </a>
    </div>
    
    <div class="col-md-8">
         <div class="card shadow-sm">
             <div class="card-header">Mevcut Firmalar</div>
             <div class="card-body">
                <ul class="list-group">
                    <?php foreach ($firms as $firm): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?php echo e($firm['name']); ?>
                            <div>
                                <a href="<?php echo BASE_URL . 'admin/firms/edit/' . $firm['id']; ?>" class="btn btn-primary btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?php echo BASE_URL . 'admin/firms?action=delete&id=' . $firm['id']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Bu firmayı silmek istediğinizden emin misiniz?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
             </div>
         </div>
    </div>
</div>