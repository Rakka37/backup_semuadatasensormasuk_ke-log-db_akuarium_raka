<?php
require "config/db.php";
header("Content-Type: application/json");

$data = [];

// === NOTIFIKASI + PAKAN ===
$q = $conn->query("
  SELECT 'NOTIFIKASI' AS jenis, pesan, created_at AS waktu
  FROM notifikasi

  UNION ALL

  SELECT 'PAKAN', CONCAT(mode,' - ',keterangan), created_at
  FROM log_pakan

  ORDER BY waktu DESC
  LIMIT 30
");

while($r = $q->fetch_assoc()){
  $data[] = [
    "jenis" => $r['jenis'],
    "pesan" => $r['pesan'],
    "waktu" => $r['waktu']
  ];
}

echo json_encode($data);
