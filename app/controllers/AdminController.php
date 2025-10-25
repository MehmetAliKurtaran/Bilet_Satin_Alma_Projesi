<?php
// /bilet-satin-alma/app/controllers/AdminController.php

class AdminController {
    private $db;

    public function __construct() {
        auth_guard(['Admin']);
        $this->db = (new Database())->getConnection();
    }

    // --- FİRMA YÖNETİMİ ---
    public function manageFirms() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_firm') {
            verify_csrf_token($_POST['csrf_token']);
            $name = $_POST['name'];
            if (!empty($name)) {
                try {
                    $stmt = $this->db->prepare("INSERT INTO Companies (name) VALUES (?)");
                    $stmt->execute([$name]);
                    set_message('success', 'Yeni firma başarıyla eklendi: ' . e($name));
                } catch (PDOException $e) {
                    set_message('danger', 'Bu firma adı zaten mevcut.');
                }
                redirect('admin/firms');
            }
        }
        
        if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
            try {
                $stmt = $this->db->prepare("DELETE FROM Companies WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                set_message('success', 'Firma başarıyla silindi.');
            } catch (PDOException $e) {
                 set_message('danger', 'Firma silinirken hata oluştu: ' . $e->getMessage());
            }
            redirect('admin/firms');
        }

        $firms = $this->db->query("SELECT * FROM Companies ORDER BY name")->fetchAll();
        load_view('admin/manage_firms', ['firms' => $firms]);
    }
    
    public function editFirm($id) {
        $stmt = $this->db->prepare("SELECT * FROM Companies WHERE id = ?");
        $stmt->execute([$id]);
        $firm = $stmt->fetch();
        if (!$firm) {
            set_message('danger', 'Firma bulunamadı.');
            redirect('admin/firms');
        }
        
        load_view('admin/edit_firm', ['firm' => $firm]);
    }
    
    public function updateFirm($id) {
        verify_csrf_token($_POST['csrf_token']);
        $name = $_POST['name'];
        
        $stmt = $this->db->prepare("UPDATE Companies SET name = ? WHERE id = ?");
        $stmt->execute([$name, $id]);
        set_message('success', 'Firma adı başarıyla güncellendi.');
        redirect('admin/firms');
    }

    // --- FİRMA ADMİN YÖNETİMİ ---
    public function createFirmaAdmin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf_token($_POST['csrf_token']);
            $fullname = $_POST['fullname'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $company_id = $_POST['company_id'];
            
            $stmt = $this->db->prepare("SELECT id FROM Users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                set_message('danger', 'Bu e-posta adresi zaten kayıtlı.');
                redirect('admin/create-firma-admin');
            }
            
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("INSERT INTO Users (fullname, email, password, role, company_id, balance) VALUES (?, ?, ?, 'Firma Admin', ?, 0)");
            
            if ($stmt->execute([$fullname, $email, $hashed_password, $company_id])) {
                set_message('success', 'Firma Admin kullanıcısı başarıyla oluşturuldu.');
                redirect('admin/firms');
            } else {
                set_message('danger', 'Kullanıcı oluşturulurken bir hata oluştu.');
                redirect('admin/create-firma-admin');
            }
        }
        
        $companies = $this->db->query("SELECT id, name FROM Companies ORDER BY name")->fetchAll();
        load_view('admin/create_firma_admin', ['companies' => $companies]);
    }
    
    // --- KUPON YÖNETİMİ (GENEL) ---
    public function manageCoupons() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_coupon') {
            verify_csrf_token($_POST['csrf_token']);
            $stmt = $this->db->prepare("INSERT INTO Coupons (code, discount_rate, usage_limit, expiration_date, company_id) VALUES (?, ?, ?, ?, NULL)");
            try {
                $stmt->execute([
                    $_POST['code'],
                    $_POST['discount_rate'],
                    $_POST['usage_limit'],
                    $_POST['expiration_date']
                ]);
                set_message('success', 'Genel kupon eklendi.');
            } catch (PDOException $e) {
                set_message('danger', 'Kupon kodu benzersiz olmalı.');
            }
            redirect('admin/coupons');
        }
        
        if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
            $stmt = $this->db->prepare("DELETE FROM Coupons WHERE id = ? AND company_id IS NULL");
            $stmt->execute([$_GET['id']]);
            set_message('success', 'Genel kupon silindi.');
            redirect('admin/coupons');
        }

        $coupons = $this->db->query("SELECT * FROM Coupons WHERE company_id IS NULL ORDER BY expiration_date DESC")->fetchAll();
        load_view('admin/manage_coupons', ['coupons' => $coupons]);
    }
}