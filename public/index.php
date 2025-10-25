<?php
// /bilet-satin-alma/public/index.php
// TAMAMLANMIŞ VE GÜVENLİKLİ SÜRÜM (Yazım Hatası Düzeltildi)

require_once __DIR__ . '/../app/config/init.php';

function load_view($view_name, $data = []) {
    extract($data); 
    $content_view = __DIR__ . '/../app/views/' . $view_name . '.php';
    require __DIR__ . '/../app/views/layout.php';
}

$request_uri = $_SERVER['REQUEST_URI'];
$request_path = parse_url($request_uri, PHP_URL_PATH);
$route = rtrim($request_path, '/');
if ($route === '') { $route = '/'; }
$method = $_SERVER['REQUEST_METHOD'];

switch (true) {

    // ==== GÜVENLİK UYARISI: KURULUMDAN SONRA BU SATIRI SİLİN! ====
    case ($method == 'GET' && $route == BASE_URL . 'setup-database'):
        echo "Veritabanı kurulumu başlıyor...<br>";
        require_once __DIR__ . '/../database/init_db.php';
        echo "<br>Veritabanı kurulumu tamamlandı. Lütfen bu rotayı 'public/index.php' dosyasından SİLİN.";
        break;
    // ==============================================================

    case ($method == 'GET' && $route == BASE_URL):
        (new HomeController())->index();
        break;

    // --- Authentication ---
    case ($method == 'GET' && $route == BASE_URL . 'login'): (new AuthController())->showLogin(); break;
    case ($method == 'POST' && $route == BASE_URL . 'login'): (new AuthController())->handleLogin(); break;
    case ($method == 'GET' && $route == BASE_URL . 'register'): (new AuthController())->showRegister(); break;
    case ($method == 'POST' && $route == BASE_URL . 'register'): (new AuthController())->handleRegister(); break;
    case ($method == 'GET' && $route == BASE_URL . 'logout'): (new AuthController())->logout(); break;

    // --- Sefer Detay ve Bilet Alma ---
    case ($method == 'GET' && preg_match('#^' . BASE_URL . 'trip/(\d+)$#', $route, $matches)):
        (new TripController())->show($matches[1]); break;
    case ($method == 'POST' && preg_match('#^' . BASE_URL . 'trip/buy/(\d+)$#', $route, $matches)):
        (new TripController())->buy($matches[1]); break;

    // --- User (Yolcu) Paneli ---
    case ($method == 'GET' && $route == BASE_URL . 'account/tickets'):
        (new AccountController())->myTickets(); break;
    case ($method == 'GET' && preg_match('#^' . BASE_URL . 'account/tickets/cancel/(\d+)$#', $route, $matches)):
        (new AccountController())->cancelTicket($matches[1]); break;
    case ($method == 'GET' && preg_match('#^' . BASE_URL . 'account/tickets/pdf/(\d+)$#', $route, $matches)):
        (new AccountController())->downloadTicket($matches[1]); break;
        
    // --- Admin Paneli ---
    case ($method == 'GET' && $route == BASE_URL . 'admin/firms'): (new AdminController())->manageFirms(); break;
    case ($method == 'POST' && $route == BASE_URL . 'admin/firms'): (new AdminController())->manageFirms(); break;
    case ($method == 'GET' && preg_match('#^' . BASE_URL . 'admin/firms/edit/(\d+)$#', $route, $matches)):
        (new AdminController())->editFirm($matches[1]); break;
    case ($method == 'POST' && preg_match('#^' . BASE_URL . 'admin/firms/update/(\d+)$#', $route, $matches)):
        (new AdminController())->updateFirm($matches[1]); break;
    
    case ($method == 'GET' && $route == BASE_URL . 'admin/create-firma-admin'): (new AdminController())->createFirmaAdmin(); break;
    case ($method == 'POST' && $route == BASE_URL . 'admin/create-firma-admin'): (new AdminController())->createFirmaAdmin(); break;

    case ($method == 'GET' && $route == BASE_URL . 'admin/coupons'): (new AdminController())->manageCoupons(); break;
    case ($method == 'POST' && $route == BASE_URL . 'admin/coupons'): (new AdminController())->manageCoupons(); break;

    // --- Firma Admin Paneli ---
    case ($method == 'GET' && $route == BASE_URL . 'firma/trips'): (new FirmaAdminController())->manageTrips(); break;
    case ($method == 'POST' && $route == BASE_URL . 'firma/trips'): (new FirmaAdminController())->manageTrips(); break;
    case ($method == 'GET' && preg_match('#^' . BASE_URL . 'firma/trips/edit/(\d+)$#', $route, $matches)):
        (new FirmaAdminController())->editTrip($matches[1]); break;
    case ($method == 'POST' && preg_match('#^' . BASE_URL . 'firma/trips/update/(\d+)$#', $route, $matches)):
        (new FirmaAdminController())->updateTrip($matches[1]); break;

    case ($method == 'GET' && $route == BASE_URL . 'firma/coupons'): (new FirmaAdminController())->manageCoupons(); break;
    case ($method == 'POST' && $route == BASE_URL . 'firma/coupons'): (new FirmaAdminController())->manageCoupons(); break;
        
    // 404
    default:
        http_response_code(404);
        echo "404 - Sayfa Bulunamadı. (Route: $route)";
        break;
}