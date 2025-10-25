<?php
// /bilet-satin-alma/app/config/init.php
// FPDF (PDF) YÜKLEYİCİSİ DÜZELTİLDİ

// --- GÜVENLİK AYARLARI ---
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

session_start();

// --- CSRF TOKEN OLUŞTURMA ---
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Temel ayarlar
define('BASE_URL', '/');
require_once __DIR__ . '/Database.php';

// Controller'ları ve Kütüphaneleri otomatik yükle
spl_autoload_register(function ($class_name) {
    // Controller'ları yükle
    $controller_file = __DIR__ . '/../controllers/' . $class_name . '.php';
    if (file_exists($controller_file)) {
        require_once $controller_file;
    }
    
    // Kütüphaneleri (FPDF) yükle
    // DÜZELTME: Sınıf adı 'FPDF' olsa bile 'fpdf.php' dosyasını bulması için strtolower() eklendi.
    $lib_file = __DIR__ . '/../lib/' . strtolower($class_name) . '.php';
    if (file_exists($lib_file)) {
        require_once $lib_file;
    }
});

// Güvenli HTML çıktısı (XSS Önlemi)
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Yönlendirme
function redirect($url) {
    header('Location: ' . BASE_URL . ltrim($url, '/'));
    exit;
}

// Flash Mesaj
function set_message($type, $message) {
    $_SESSION['messages'][] = ['type' => $type, 'message' => $message];
}

// --- GÜVENLİK FONKSİYONLARI ---

// CSRF Token Kontrolü (Tüm POST işlemleri için)
function verify_csrf_token($token) {
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        set_message('danger', 'Güvenlik hatası: Geçersiz form isteği.');
        redirect($_SERVER['HTTP_REFERER'] ?? '');
    }
}

// Yetki Kontrolü
function auth_guard($roles = []) {
    if (!isset($_SESSION['user_id'])) {
        set_message('danger', 'Bu sayfayı görmek için giriş yapmalısınız.');
        redirect('login');
    }
    
    if (!empty($roles) && !in_array($_SESSION['role'], $roles)) {
        set_message('danger', 'Bu işlem için yetkiniz bulunmamaktadır.');
        redirect('');
    }
}

// Güvenli Oturum Yenileme (Session Fixation Önlemi)
function regenerate_session() {
    session_regenerate_id(true);
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}