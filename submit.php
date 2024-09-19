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

$response = ['success' => false];

if (!empty($_FILES['fontFile'])) {
  $uploadResponse = $fontUploadService->handleFileUpload($_FILES['fontFile']);
  $response = array_merge($response, $uploadResponse);
  echo json_encode($response);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'delete' && $_POST['status'] === 'deleteFont') {

  $fontId = intval($_POST['id']);

  if ($fontId > 0) {

    $font = $database->showFont($fontId);

    if ($font) {
      $filePath = $font['file_path'];
      if ($database->deleteFont($fontId)) {
        if (file_exists($filePath)) {
          if (unlink($filePath)) {
            echo json_encode(['success' => true, 'message' => 'Font deleted successfully']);
          }
        }
      }
    } else {
      echo json_encode(['success' => false, 'message' => 'Font not found']);
    }
  }
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] === 'createFontGroup' || $_POST['action'] === 'editFontGroup')) {

  $title = $_POST['title'];
  $fontIds = $_POST['font_id'] ?? [];
  $fontTitles = $_POST['font_name'] ?? [];
  $groupId = $_POST['font_group_id'] ?? null;

  if (empty($title)) {
    echo json_encode(['success' => false, 'message' => 'Title is required.']);
    exit;
  }

  if (count($fontIds) < 2) {
    echo json_encode(['success' => false, 'message' => 'You must select at least two fonts.']);
    exit;
  }

  if (count($fontIds) !== count($fontTitles)) {
    echo json_encode(['success' => false, 'message' => 'Some fields may missing!']);
    exit;
  }
  if ($_POST['action'] === 'createFontGroup') {
    if ($database->createFontGroup($title, $fontIds, $fontTitles)) {
      echo json_encode(['success' => true, 'message' => 'Font group created successfully!']);
    }
  } else {
    if ($database->updateFontGroup($groupId, $title, $fontIds, $fontTitles)) {
      echo json_encode(['success' => true, 'message' => 'Font group updated successfully!']);
    }
  }


  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'deleteGroup' && isset($_POST['group_id'])) {
  $groupId = intval($_POST['group_id']);

  if ($database->deleteFontGroup($groupId)) {
    echo json_encode(['success' => true, 'message' => 'Successfully delete the group.']);
  } else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete the group.']);
  }

  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['group_id']) && $_GET['action'] === 'getGroup') {
  $groupId = intval($_GET['group_id']);
  $group = $database->getFontGroupById($groupId);
  if ($group) {
    echo json_encode(['success' => true, 'data' => $group]);
  } else {
    echo json_encode(['success' => false, 'message' => 'Group not found']);
  }
  exit;
}

$data = [];
$fonts = $database->getAllFonts();
$fontGroups = $database->fetchFontGroups();
$data['fonts'] = $fonts;
$data['fontGroups'] = $fontGroups;
$response = ['success' => true, 'data' => $data];

echo json_encode($response);
exit;
