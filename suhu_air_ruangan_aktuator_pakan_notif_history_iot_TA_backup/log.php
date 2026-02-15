<?php
require "db.php";

$jenis = $_POST['jenis'] ?? 'INFO';
$pesan = $_POST['pesan'] ?? '';

if ($pesan == '') {
  http_response_code(400);
  exit;
}

if (!$conn->query("
  INSERT INTO notifikasi(jenis,pesan,sumber)
  VALUES('$jenis','$pesan','SISTEM')
")) {
  echo $conn->error;
  exit;
}

echo "OK";
