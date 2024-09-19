<?php

class Database
{
    private $pdo;

    public function __construct($host, $dbname, $user, $password)
    {
        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function insertFont($fontName, $filePath)
    {
        $stmt = $this->pdo->prepare("INSERT INTO fonts (font_name, file_path) VALUES (:font_name, :file_path)");
        $stmt->bindParam(':font_name', $fontName);
        $stmt->bindParam(':file_path', $filePath);
        return $stmt->execute();
    }

    public function getAllFonts()
    {
        $query = "SELECT * FROM fonts";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteFont($fontId)
    {
        $query = $this->pdo->prepare("DELETE FROM fonts WHERE id = :id");
        $query->bindParam(':id', $fontId, PDO::PARAM_INT);
        return $query->execute();
    }

    public function showFont($fontId)
    {
        $query = $this->pdo->prepare("SELECT file_path FROM fonts WHERE id = :id");
        $query->bindParam(':id', $fontId, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }
}
