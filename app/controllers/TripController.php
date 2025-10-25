<?php
// /bilet-satin-alma/app/controllers/TripController.php

class TripController {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function show($id) {
        $stmt = $this->db->prepare("
            SELECT T.*, C.name as company_name 
            FROM Trips T
            JOIN Companies C ON T.company_id = C.id
            WHERE T.id = ?
        ");
        $stmt->execute([$id]);
        $trip = $stmt->fetch();

        if (!$trip) {
            set_message('danger', 'Sefer bulunamadı.');
            redirect('');
        }

        $stmt = $this->db->prepare("SELECT seat_number FROM Bookings WHERE trip_id = ? AND status = 'ACTIVE'");
        $stmt->execute([$id]);
        $booked_seats = $stmt->fetchAll(PDO::FETCH_COLUMN);

        load_view('trip_detail', ['trip' => $trip, 'booked_seats' => $booked_seats]);
    }

    public function buy($id) {
        auth_guard(['User']);
        verify_csrf_token($_POST['csrf_token']); // CSRF Koruması

        $seat_number = $_POST['seat_number'] ?? null;
        $coupon_code = $_POST['coupon_code'] ?? null;
        $user_id = $_SESSION['user_id'];

        if (!$seat_number) {
            set_message('danger', 'Lütfen bir koltuk seçin.');
            redirect('trip/' . $id);
        }

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("SELECT price, company_id FROM Trips WHERE id = ?");
            $stmt->execute([$id]);
            $trip = $stmt->fetch();
            $price = $trip['price'];
            $company_id = $trip['company_id'];
            $final_price = $price;

            // --- Kupon Kontrolü ---
            if (!empty($coupon_code)) {
                $stmt = $this->db->prepare("
                    SELECT * FROM Coupons 
                    WHERE code = ? 
                    AND expiration_date >= date('now') 
                    AND usage_count < usage_limit
                    AND (company_id IS NULL OR company_id = ?)
                ");
                $stmt->execute([$coupon_code, $company_id]);
                $coupon = $stmt->fetch();

                if ($coupon) {
                    $discount_amount = $price * $coupon['discount_rate'];
                    $final_price = $price - $discount_amount;
                    
                    // Kupon kullanım sayısını artır
                    $stmt = $this->db->prepare("UPDATE Coupons SET usage_count = usage_count + 1 WHERE id = ?");
                    $stmt->execute([$coupon['id']]);
                    
                    set_message('info', 'Kupon uygulandı! İndirim Tutarı: ' . e(number_format($discount_amount, 2)) . ' TL');
                } else {
                    set_message('warning', 'Geçersiz veya süresi dolmuş kupon kodu.');
                }
            }

            $stmt = $this->db->prepare("SELECT balance FROM Users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user_balance = $stmt->fetchColumn();

            if ($user_balance < $final_price) {
                set_message('danger', 'Yetersiz bakiye. Gereken: ' . e(number_format($final_price, 2)) . ' TL');
                $this->db->rollBack();
                redirect('trip/' . $id);
            }

            $stmt = $this->db->prepare("SELECT id FROM Bookings WHERE trip_id = ? AND seat_number = ? AND status = 'ACTIVE'");
            $stmt->execute([$id, $seat_number]);
            if ($stmt->fetch()) {
                set_message('danger', 'Seçtiğiniz koltuk (No: ' . e($seat_number) . ') başkası tarafından alındı.');
                $this->db->rollBack();
                redirect('trip/' . $id);
            }
            
            $new_balance = $user_balance - $final_price;
            $stmt = $this->db->prepare("UPDATE Users SET balance = ? WHERE id = ?");
            $stmt->execute([$new_balance, $user_id]);

            $stmt = $this->db->prepare("INSERT INTO Bookings (user_id, trip_id, seat_number, price_paid) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, $id, $seat_number, $final_price]);

            $this->db->commit();
            set_message('success', 'Biletiniz başarıyla satın alındı (Koltuk No: ' . e($seat_number) . '). Ödenen Tutar: ' . e(number_format($final_price, 2)) . ' TL');
            redirect('account/tickets');

        } catch (Exception $e) {
            $this->db->rollBack();
            set_message('danger', 'Bilet alımı sırasında bir hata oluştu: ' . $e->getMessage());
            redirect('trip/' . $id);
        }
    }
}