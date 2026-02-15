<?php
require "db.php";
header("Content-Type: application/json");

// Ambil data sensor terakhir
$q = $conn->query("
  SELECT suhu_ruangan, suhu_air, ntu
  FROM sensor_data
  ORDER BY id DESC
  LIMIT 1
");

if($q->num_rows == 0){
  echo json_encode([
    "ruangan"=>"--",
    "air"=>"--",
    "ntu"=>"--"
  ]);
  exit;
}

$d = $q->fetch_assoc();

echo json_encode([
  "ruangan" => round($d['suhu_ruangan'],1),
  "air"     => round($d['suhu_air'],1),
  "ntu"     => round($d['ntu'],1)
]);
