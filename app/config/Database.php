<?php
// /bilet-satin-alma/app/config/Database.php

class Database {
    private $dbPath = __DIR__ . '/../../database/bus_ticket.sqlite'; // Veritabanı dosyasının yolu
    private $connection = null;

    public function getConnection() {
        if ($this->connection == null) {
            try {
                $this->connection = new PDO("sqlite:" . $this->dbPath);
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $this->connection->exec('PRAGMA foreign_keys = ON;');
            } catch (PDOException $e) {
                die("Bağlantı hatası: " . $e->getMessage());
            }
        }
        return $this->connection;
    }
}