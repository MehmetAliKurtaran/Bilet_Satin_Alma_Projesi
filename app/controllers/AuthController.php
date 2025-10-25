<?php
// /bilet-satin-alma/app/controllers/AuthController.php

class AuthController {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function showLogin() {
        load_view('login');
    }

    public function handleLogin() {
        verify_csrf_token($_POST['csrf_token']); // CSRF Koruması
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $this->db->prepare("SELECT * FROM Users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            regenerate_session(); // GÜVENLİK: Oturumu yeniden oluştur
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['company_id'] = $user['company_id'];
            
            set_message('success', 'Başarıyla giriş yaptınız. Hoş geldiniz, ' . e($user['fullname']));
            redirect('');
        } else {
            set_message('danger', 'Geçersiz e-posta veya şifre.');
            redirect('login');
        }
    }

    public function showRegister() {
        load_view('register');
    }

    public function handleRegister() {
        verify_csrf_token($_POST['csrf_token']); // CSRF Koruması
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $password_confirm = $_POST['password_confirm'];

        if ($password !== $password_confirm) {
            set_message('danger', 'Şifreler uyuşmuyor.');
            redirect('register');
        }

        $stmt = $this->db->prepare("SELECT id FROM Users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            set_message('danger', 'Bu e-posta adresi zaten kayıtlı.');
            redirect('register');
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO Users (fullname, email, password, role, balance) VALUES (?, ?, ?, 'User', 1000.0)");
        
        if ($stmt->execute([$fullname, $email, $hashed_password])) {
            set_message('success', 'Hesabınız başarıyla oluşturuldu. Lütfen giriş yapın.');
            redirect('login');
        } else {
            set_message('danger', 'Kayıt sırasında bir hata oluştu.');
            redirect('register');
        }
    }

    public function logout() {
        session_destroy();
        redirect('');
    }
}