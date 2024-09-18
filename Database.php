<?php

class Database {
    private $pdo;

    public function __construct($host, $dbname, $user, $password) {
        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function insertFont($fontName, $filePath) {
        $stmt = $this->pdo->prepare("INSERT INTO fonts (font_name, file_path) VALUES (:font_name, :file_path)");
        $stmt->bindParam(':font_name', $fontName);
        $stmt->bindParam(':file_path', $filePath);
        return $stmt->execute();
    }
}
