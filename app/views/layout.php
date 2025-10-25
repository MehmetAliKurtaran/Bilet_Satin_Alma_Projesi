<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token']; ?>">
    <title>Bilet Satın Alma Platformu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>"><i class="bi bi-bus-front"></i> Bilet Platformu</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>">Ana Sayfa</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        
                        <?php if ($_SESSION['role'] == 'Admin'): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Admin Panel</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>admin/firms">Firma Yönetimi</a></li>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>admin/create-firma-admin">Firma Admin Oluştur</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>admin/coupons">Genel Kuponlar</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                        
                        <?php if ($_SESSION['role'] == 'Firma Admin'): ?>
                             <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Firma Panel</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>firma/trips">Sefer Yönetimi</a></li>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>firma/coupons">Firma Kuponları</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <?php if ($_SESSION['role'] == 'User'): ?>
                            <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>account/tickets">Biletlerim</a></li>
                        <?php endif; ?>

                        <li class="nav-item">
                             <a class="nav-link" href="<?php echo BASE_URL; ?>logout">
                                <i class="bi bi-box-arrow-right"></i> Çıkış Yap (<?php echo e($_SESSION['fullname']); ?>)
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>login">Giriş Yap</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>register">Kayıt Ol</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php require_once __DIR__ . '/partials/_messages.php'; ?>
        <?php
        if (isset($content_view) && file_exists($content_view)) {
            require $content_view;
        } else {
            echo "<div class='alert alert-danger'>View dosyası yüklenemedi: $content_view</div>";
        }
        ?>
    </div>

    <footer class="text-center mt-5 p-3 bg-light border-top">
        <p class="mb-0"><?php echo date('Y'); ?> Bilet Satın Alma Platformu</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>