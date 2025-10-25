<h3><i class="bi bi-pencil"></i> Firma Düzenle (Admin)</h3>
<hr>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="<?php echo BASE_URL . 'admin/firms/update/' . $firm['id']; ?>" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label">Firma Adı</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo e($firm['name']); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Güncelle</button>
                </form>
            </div>
        </div>
    </div>
</div>