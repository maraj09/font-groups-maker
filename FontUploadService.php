<?php

use FontLib\Font;

require_once 'vendor/autoload.php';

class FontUploadService
{
  private $database;
  private $uploadDir = 'uploads/';

  public function __construct(Database $database)
  {
    $this->database = $database;
  }

  public function handleFileUpload($file)
  {
    if (!file_exists($this->uploadDir)) {
      mkdir($this->uploadDir, 0777, true);
    }
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = uniqid() . '.' . $fileExtension;
    $filePath = $this->uploadDir . $fileName;
    move_uploaded_file($file['tmp_name'], $filePath);

    $fontName = $this->extractFontName($filePath);

    if ($this->database->insertFont($fontName, $filePath)) {
      return ['status' => true, 'message' => 'File uploaded and font information stored successfully.'];
    } else {
      return ['status' => 'error', 'message' => 'Failed to save font information in the database.'];
    }
  }

  public function extractFontName($filePath)
  {
    $font = Font::load($filePath);
    $font->parse();
    $fontName = $font->getFontName();

    return $fontName ?: 'Unknown Font';
  }
}
