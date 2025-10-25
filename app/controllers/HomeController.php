<?php
// /bilet-satin-alma/app/controllers/HomeController.php

class HomeController {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function index() {
        $departure_city = $_GET['departure_city'] ?? null;
        $arrival_city = $_GET['arrival_city'] ?? null;
        $trips = [];
        $params = [];

        $sql = "SELECT T.*, C.name as company_name 
                FROM Trips T
                JOIN Companies C ON T.company_id = C.id
                WHERE T.departure_time > datetime('now', '+1 hour')"; 

        if ($departure_city && $arrival_city) {
            $sql .= " AND T.departure_city LIKE ? AND T.arrival_city LIKE ?";
            $params[] = '%' . $departure_city . '%';
            $params[] = '%' . $arrival_city . '%';
        }
        
        $sql .= " ORDER BY T.departure_time ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $trips = $stmt->fetchAll();

        load_view('home', ['trips' => $trips]);
    }
}