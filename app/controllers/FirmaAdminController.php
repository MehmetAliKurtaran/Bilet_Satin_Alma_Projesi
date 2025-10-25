<?php
// /bilet-satin-alma/app/controllers/FirmaAdminController.php

class FirmaAdminController {
    private $db;
    private $company_id;

    public function __construct() {
        auth_guard(['Firma Admin']);
        $this->db = (new Database())->getConnection();
        $this->company_id = $_SESSION['company_id'];
        
        if (empty($this->company_id)) {
            set_message('danger', 'Hesabınız bir firmaya atanmamış. Lütfen sistem yöneticisi ile iletişime geçin.');
            redirect('logout');
        }
    }

    // --- SEFER YÖNETİMİ ---
    public function manageTrips() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_trip') {
            verify_csrf_token($_POST['csrf_token']);
            $stmt = $this->db->prepare("
                INSERT INTO Trips (company_id, departure_city, arrival_city, departure_time, arrival_time, price, seat_count)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $params = [
                $this->company_id,
                $_POST['departure_city'],
                $_POST['arrival_city'],
                $_POST['departure_time'],
                $_POST['arrival_time'],
                $_POST['price'],
                $_POST['seat_count']
            ];
            
            try {
                $stmt->execute($params);
                set_message('success', 'Yeni sefer başarıyla eklendi.');
            } catch (Exception $e) {
                set_message('danger', 'Sefer eklenirken hata: ' . $e->getMessage());
            }
            redirect('firma/trips');
        }
        
        if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
            $stmt = $this->db->prepare("DELETE FROM Trips WHERE id = ? AND company_id = ?");
            $stmt->execute([$_GET['id'], $this->company_id]);
            
            if ($stmt->rowCount() > 0) {
                set_message('success', 'Sefer başarıyla silindi.');
            } else {
                set_message('danger', 'Sefer silinemedi (size ait değil).');
            }
            redirect('firma/trips');
        }
        
        $stmt = $this->db->prepare("SELECT * FROM Trips WHERE company_id = ? ORDER BY departure_time DESC");
        $stmt->execute([$this->company_id]);
        $trips = $stmt->fetchAll();
        
        load_view('firma/manage_trips', ['trips' => $trips]);
    }
    
    public function editTrip($id) {
        $stmt = $this->db->prepare("SELECT * FROM Trips WHERE id = ? AND company_id = ?");
        $stmt->execute([$id, $this->company_id]);
        $trip = $stmt->fetch();
        if (!$trip) {
            set_message('danger', 'Sefer bulunamadı veya size ait değil.');
            redirect('firma/trips');
        }
        
        load_view('firma/edit_trip', ['trip' => $trip]);
    }
    
    public function updateTrip($id) {
        verify_csrf_token($_POST['csrf_token']);
        $stmt = $this->db->prepare("
            UPDATE Trips SET
            departure_city = ?, 
            arrival_city = ?, 
            departure_time = ?, 
            arrival_time = ?, 
            price = ?, 
            seat_count = ?
            WHERE id = ? AND company_id = ?
        ");
        
        $params = [
            $_POST['departure_city'],
            $_POST['arrival_city'],
            $_POST['departure_time'],
            $_POST['arrival_time'],
            $_POST['price'],
            $_POST['seat_count'],
            $id,
            $this->company_id
        ];
        
        $stmt->execute($params);
        set_message('success', 'Sefer başarıyla güncellendi.');
        redirect('firma/trips');
    }
    
    // --- KUPON YÖNETİMİ (FİRMA) ---
    public function manageCoupons() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_coupon') {
            verify_csrf_token($_POST['csrf_token']);
            $stmt = $this->db->prepare("INSERT INTO Coupons (code, discount_rate, usage_limit, expiration_date, company_id) VALUES (?, ?, ?, ?, ?)");
            try {
                $stmt->execute([
                    $_POST['code'],
                    $_POST['discount_rate'],
                    $_POST['usage_limit'],
                    $_POST['expiration_date'],
                    $this->company_id // Sadece kendi firmasına
                ]);
                set_message('success', 'Firmaya özel kupon eklendi.');
            } catch (PDOException $e) {
                set_message('danger', 'Kupon kodu benzersiz olmalı.');
            }
            redirect('firma/coupons');
        }
        
        if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
            $stmt = $this->db->prepare("DELETE FROM Coupons WHERE id = ? AND company_id = ?");
            $stmt->execute([$_GET['id'], $this->company_id]);
            set_message('success', 'Firma kuponu silindi.');
            redirect('firma/coupons');
        }

        $stmt = $this->db->prepare("SELECT * FROM Coupons WHERE company_id = ? ORDER BY expiration_date DESC");
        $stmt->execute([$this->company_id]);
        $coupons = $stmt->fetchAll();
        
        load_view('firma/manage_coupons', ['coupons' => $coupons]);
    }
}