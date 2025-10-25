<?php
// /bilet-satin-alma/database/init_db.php
// Veritabanı tablolarını ve örnek verileri yükler.

require_once '../app/config/Database.php';

try {
    $db = (new Database())->getConnection();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Veritabanı bağlantısı başarılı.<br>";

    // --- 1. TABLOLARI OLUŞTUR ---
    $sql = "
        PRAGMA foreign_keys = ON;

        CREATE TABLE IF NOT EXISTS Companies (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS Users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            fullname TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT 'User',
            company_id INTEGER NULL,          
            balance REAL NOT NULL DEFAULT 1000.0,
            FOREIGN KEY (company_id) REFERENCES Companies(id) ON DELETE SET NULL
        );

        CREATE TABLE IF NOT EXISTS Trips (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            company_id INTEGER NOT NULL,
            departure_city TEXT NOT NULL,
            arrival_city TEXT NOT NULL,
            departure_time DATETIME NOT NULL,
            arrival_time DATETIME NOT NULL,
            price REAL NOT NULL,
            seat_count INTEGER NOT NULL DEFAULT 40,
            FOREIGN KEY (company_id) REFERENCES Companies(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS Bookings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            trip_id INTEGER NOT NULL,
            seat_number INTEGER NOT NULL,
            status TEXT NOT NULL DEFAULT 'ACTIVE',
            price_paid REAL NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES Users(id),
            FOREIGN KEY (trip_id) REFERENCES Trips(id),
            UNIQUE(trip_id, seat_number)
        );

        CREATE TABLE IF NOT EXISTS Coupons (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            code TEXT NOT NULL UNIQUE,
            discount_rate REAL NOT NULL,
            usage_limit INTEGER NOT NULL DEFAULT 1,
            usage_count INTEGER NOT NULL DEFAULT 0,
            expiration_date DATE NOT NULL,
            company_id INTEGER NULL,
            FOREIGN KEY (company_id) REFERENCES Companies(id) ON DELETE CASCADE
        );
    ";
    $db->exec($sql);
    echo "Tüm tablolar başarıyla oluşturuldu.<br>";
    echo "<hr>";

    // --- 2. ÖRNEK FİRMALARI EKLE ---
    $db->exec("INSERT OR IGNORE INTO Companies (name) VALUES ('Metro Turizm');");
    $db->exec("INSERT OR IGNORE INTO Companies (name) VALUES ('Kamil Koç');");
    echo "Örnek firmalar (Metro Turizm, Kamil Koç) eklendi.<br>";
    
    $metroId = $db->query("SELECT id FROM Companies WHERE name = 'Metro Turizm'")->fetchColumn();
    $kamilId = $db->query("SELECT id FROM Companies WHERE name = 'Kamil Koç'")->fetchColumn();

    // --- 3. ÖRNEK KULLANICILARI EKLE ---

    // Admin Kullanıcı
    $adminEmail = 'admin@biletsistemi.com';
    $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT OR IGNORE INTO Users (fullname, email, password, role, balance) VALUES (?, ?, ?, 'Admin', 9999)");
    $stmt->execute(['Admin Kullanıcı', $adminEmail, $adminPass]);
    echo "Varsayılan Admin kullanıcısı (email: admin@biletsistemi.com, şifre: admin123) oluşturuldu.<br>";

    // Firma Admin Kullanıcı (Metro Turizm'e bağlı)
    if ($metroId) {
        $firmaAdminEmail = 'firma@metro.com';
        $firmaAdminPass = password_hash('firma123', PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT OR IGNORE INTO Users (fullname, email, password, role, company_id) VALUES (?, ?, ?, 'Firma Admin', ?)");
        $stmt->execute(['Metro Firma Admin', $firmaAdminEmail, $firmaAdminPass, $metroId]);
        echo "Örnek Firma Admin kullanıcısı (email: firma@metro.com, şifre: firma123) oluşturuldu ve Metro Turizm'e atandı.<br>";
    }

    // Normal User (Yolcu)
    $userEmail = 'yolcu@deneme.com';
    $userPass = password_hash('yolcu123', PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT OR IGNORE INTO Users (fullname, email, password, role, balance) VALUES (?, ?, ?, 'User', 1500.0)");
    $stmt->execute(['Ahmet Yolcu', $userEmail, $userPass]);
    echo "Örnek Yolcu kullanıcısı (email: yolcu@deneme.com, şifre: yolcu123) 1500 TL bakiye ile oluşturuldu.<br>";
    echo "<hr>";

    // --- 4. ÖRNEK SEFERLERİ EKLE ---
    // Sefer tarihleri dinamik olarak ayarlanır.
    
    // Sefer 1 (Metro Turizm)
    if ($metroId) {
        $stmt = $db->prepare("INSERT OR IGNORE INTO Trips (company_id, departure_city, arrival_city, departure_time, arrival_time, price, seat_count) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$metroId, 'İstanbul', 'Ankara', date('Y-m-d H:i:s', strtotime('+1 day 09:00:00')), date('Y-m-d H:i:s', strtotime('+1 day 15:00:00')), 550.0, 40]);
    }
    
    // Sefer 2 (Kamil Koç)
    if ($kamilId) {
        $stmt = $db->prepare("INSERT OR IGNORE INTO Trips (company_id, departure_city, arrival_city, departure_time, arrival_time, price, seat_count) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$kamilId, 'Ankara', 'İzmir', date('Y-m-d H:i:s', strtotime('+1 day 11:30:00')), date('Y-m-d H:i:s', strtotime('+1 day 18:00:00')), 620.0, 40]);
    }

    // Sefer 3 (Metro Turizm)
    if ($metroId) {
        $stmt = $db->prepare("INSERT OR IGNORE INTO Trips (company_id, departure_city, arrival_city, departure_time, arrival_time, price, seat_count) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$metroId, 'İstanbul', 'İzmir', date('Y-m-d H:i:s', strtotime('+2 day 14:00:00')), date('Y-m-d H:i:s', strtotime('+2 day 21:00:00')), 600.0, 40]);
    }

    echo "Örnek 3 adet sefer başarıyla eklendi.<br>";
    echo "<hr>";

} catch (PDOException $e) {
    echo "Veritabanı hatası: " . $e->getMessage();
}