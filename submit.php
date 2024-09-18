<?php
header('Content-Type: application/json');

require 'Database.php';
require 'FontUploadService.php';

$host = 'localhost';
$dbname = 'font_groups_maker';
$user = 'root';
$password = 'mysql';

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Database connection failed: " . $e->getMessage());
}

$database = new Database($host, $dbname, $user, $password);
$fontUploadService = new FontUploadService($database);

if (!empty($_FILES['fontFile'])) {
  $response = $fontUploadService->handleFileUpload($_FILES['fontFile']);
  echo json_encode($response);
}

