<h3><i class="bi bi-person-plus"></i> Yeni Firma Admin Kullanıcısı Oluştur</h3>
<hr>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>admin/create-firma-admin" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-3">
                        <label for="fullname" class="form-label">Ad Soyad</label>
                        <input type="text" class="form-control" id="fullname" name="fullname" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-posta Adresi</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Geçici Şifre</label>
                        <input type="password" class="form-control" id="password" name="password" minlength="6" required>
                    </div>
                    <div class="mb-3">
                        <label for="company_id" class="form-label">Atanacak Firma</label>
                        <select class="form-select" id="company_id" name="company_id" required>
                            <option value="">Lütfen bir firma seçin...</option>
                            <?php foreach($companies as $company): ?>
                                <option value="<?php echo $company['id']; ?>"><?php echo e($company['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Firma Admin Oluştur</button>
                </form>
            </div>
        </div>
    </div>
</div>