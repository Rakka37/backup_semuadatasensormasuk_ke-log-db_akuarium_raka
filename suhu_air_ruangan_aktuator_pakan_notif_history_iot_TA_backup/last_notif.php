<?php
require "db.php";
header("Content-Type: application/json");

$q = $conn->query("
  SELECT id,pesan
  FROM notifikasi
  ORDER BY id DESC
  LIMIT 1
");

if($q->num_rows===0){
  echo json_encode(null);
  exit;
}

echo json_encode($q->fetch_assoc());
