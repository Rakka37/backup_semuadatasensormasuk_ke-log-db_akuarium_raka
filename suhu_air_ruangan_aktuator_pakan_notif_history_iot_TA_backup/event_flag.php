<?php
require "db.php";
header("Content-Type: application/json");

// ambil event terbaru (auto feed / notif)
$q = $conn->query("
  SELECT MAX(created_at) AS last_event
  FROM notifikasi
");

$r = $q->fetch_assoc();
echo json_encode([
  "last_event" => $r['last_event']
]);
