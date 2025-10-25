<?php
// /bilet-satin-alma/app/controllers/AccountController.php
// DEPRECATED HATASI DÜZELTİLDİ: utf8_decode() yerine iconv() kullanıldı.
// FONT HATASI DÜZELTİLDİ: 'helvetica' yerine 'Arial' kullanıldı.

class AccountController {
    private $db;

    public function __construct() {
        auth_guard(['User']);
        $this->db = (new Database())->getConnection();
    }

    public function myTickets() {
        $user_id = $_SESSION['user_id'];
        
        $stmt = $this->db->prepare("
            SELECT B.*, T.departure_city, T.arrival_city, T.departure_time, C.name as company_name, U.fullname, U.email
            FROM Bookings B
            JOIN Trips T ON B.trip_id = T.id
            JOIN Companies C ON T.company_id = C.id
            JOIN Users U ON B.user_id = U.id
            WHERE B.user_id = ?
            ORDER BY T.departure_time DESC
        ");
        $stmt->execute([$user_id]);
        $tickets = $stmt->fetchAll();

        load_view('my_tickets', ['tickets' => $tickets]);
    }

    public function cancelTicket($booking_id) {
        $user_id = $_SESSION['user_id'];

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                SELECT B.price_paid, T.departure_time 
                FROM Bookings B
                JOIN Trips T ON B.trip_id = T.id
                WHERE B.id = ? AND B.user_id = ? AND B.status = 'ACTIVE'
            ");
            $stmt->execute([$booking_id, $user_id]);
            $booking = $stmt->fetch();

            if (!$booking) {
                set_message('danger', 'İptal edilecek bilet bulunamadı veya bu bilet size ait değil.');
                $this->db->rollBack();
                redirect('account/tickets');
            }

            $departure_time = new DateTime($booking['departure_time']);
            $now = new DateTime();
            $interval = $now->diff($departure_time);
            
            if ($now > $departure_time || ($interval->h < 1 && $interval->days == 0 && $interval->invert == 0)) {
                set_message('danger', 'Kalkış saatine 1 saatten az kaldığı için bilet iptal edilemez.');
                $this->db->rollBack();
                redirect('account/tickets');
            }

            $stmt = $this->db->prepare("UPDATE Bookings SET status = 'CANCELLED' WHERE id = ?");
            $stmt->execute([$booking_id]);

            $price_to_refund = $booking['price_paid'];
            $stmt = $this->db->prepare("UPDATE Users SET balance = balance + ? WHERE id = ?");
            $stmt->execute([$price_to_refund, $user_id]);

            $this->db->commit();
            set_message('success', 'Biletiniz başarıyla iptal edildi. ' . e($price_to_refund) . ' TL hesabınıza iade edildi.');

        } catch (Exception $e) {
            $this->db->rollBack();
            set_message('danger', 'İptal sırasında bir hata oluştu: ' . $e->getMessage());
        }
        
        redirect('account/tickets');
    }
    
    // PDF çıktısı için FPDF'nin anlayacağı formata (ISO-8859-1) dönüştüren yardımcı fonksiyon
    private function pdf_convert($string) {
        // utf8_decode() yerine modern iconv() kullanılıyor
        return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $string);
    }

    public function downloadTicket($booking_id) {
        $user_id = $_SESSION['user_id'];
        
        $stmt = $this->db->prepare("
            SELECT B.*, T.departure_city, T.arrival_city, T.departure_time, C.name as company_name, U.fullname
            FROM Bookings B
            JOIN Trips T ON B.trip_id = T.id
            JOIN Companies C ON T.company_id = C.id
            JOIN Users U ON B.user_id = U.id
            WHERE B.id = ? AND B.user_id = ?
        ");
        $stmt->execute([$booking_id, $user_id]);
        $ticket = $stmt->fetch();

        if (!$ticket) {
            set_message('danger', 'Bilet bulunamadı.');
            redirect('account/tickets');
        }

        $pdf = new FPDF();
        $pdf->AddPage();
        
        $pdf->SetFont('Arial', 'B', 16); // 'helvetica' yerine 'Arial'
        $pdf->Cell(0, 10, $this->pdf_convert('OTOBÜS BİLETİ'), 0, 1, 'C');
        $pdf->Ln(10);
        
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(40, 10, $this->pdf_convert('Yolcu Adı:'));
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, $this->pdf_convert($ticket['fullname'])); // HATA BURADAYDI (110)
        $pdf->Ln();
        
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(40, 10, 'Firma:');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, $this->pdf_convert($ticket['company_name'])); // HATA BURADAYDI (116)
        $pdf->Ln();

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(40, 10, 'Güzergah:');
        $pdf->SetFont('Arial', '', 12);
        $guzergah = $ticket['departure_city'] . ' -> ' . $ticket['arrival_city'];
        $pdf->Cell(0, 10, $this->pdf_convert($guzergah)); // HATA BURADAYDI (122)
        $pdf->Ln();

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(40, 10, $this->pdf_convert('Kalkış:'));
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, date('d.m.Y H:i', strtotime($ticket['departure_time'])));
        $pdf->Ln();

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(40, 10, $this->pdf_convert('Koltuk No:'));
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, $ticket['seat_number']);
        $pdf->Ln();
        
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(0, 10, $this->pdf_convert('İyi yolculuklar dileriz.'), 0, 0, 'C');

        $pdf->Output('D', 'bilet-' . $ticket['id'] . '.pdf');
    }
}