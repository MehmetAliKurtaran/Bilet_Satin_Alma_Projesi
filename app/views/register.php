<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0">Kayıt Ol (Yolcu Hesabı)</h4>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>register" method="POST">
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
                        <label for="password" class="form-label">Şifre</label>
                        <input type="password" class="form-control" id="password" name="password" minlength="6" required>
                    </div>
                     <div class="mb-3">
                        <label for="password_confirm" class="form-label">Şifre (Tekrar)</label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Kayıt Ol</button>
                </form>
            </div>
             <div class="card-footer text-center">
                Zaten hesabınız var mı? <a href="<?php echo BASE_URL; ?>login">Giriş Yapın</a>
            </div>
        </div>
    </div>
</div>