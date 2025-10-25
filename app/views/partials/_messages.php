<?php
// /bilet-satin-alma/app/views/partials/_messages.php

if (!empty($_SESSION['messages'])) {
    foreach ($_SESSION['messages'] as $msg) {
        echo '<div class="alert alert-' . e($msg['type']) . ' alert-dismissible fade show" role="alert">';
        echo e($msg['message']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
    }
    // Mesajları gösterdikten sonra temizle
    unset($_SESSION['messages']);
}