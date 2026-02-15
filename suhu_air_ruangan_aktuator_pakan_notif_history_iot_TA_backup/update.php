<?php
require "db.php";

/* =====================================================
   MODE STREAM NOTIFIKASI (UNTUK DASHBOARD)
   ===================================================== */
if (isset($_GET['stream'])) {

  header("Content-Type: application/json");

  $lastId = intval($_GET['last_id'] ?? 0);

  $q = $conn->query("
    SELECT id, pesan
    FROM notifikasi
    WHERE id > $lastId
    ORDER BY id ASC
    LIMIT 20
  ");

  $data = [];
  while ($r = $q->fetch_assoc()) {
    $data[] = $r;
  }

  echo json_encode($data);
  exit;
}

/* =====================================================
   MODE UPDATE SENSOR (UNTUK ESP32)
   ===================================================== */

$ruangan = floatval($_GET['ruangan'] ?? 0);
$air     = floatval($_GET['air'] ?? 0);
$ntu     = floatval($_GET['ntu'] ?? 0);

/* ===== SIMPAN DATA SENSOR ===== */
$conn->query("
  INSERT INTO sensor_data (suhu_ruangan, suhu_air, ntu)
  VALUES ($ruangan, $air, $ntu)
");

/* =====================================================
   MODE ALARM TERUS (SUHU & KERUH BISA BARENGAN)
   ===================================================== */

// 🔥 SUHU PANAS
if ($air >= 30) {
  $conn->query("
    INSERT INTO notifikasi (jenis, pesan, sumber)
    VALUES (
      'WARNING',
      CONCAT('Suhu air tinggi (', $air, ' °C)'),
      'SENSOR'
    )
  ");
}

// ❄️ SUHU DINGIN
if ($air < 25) {
  $conn->query("
    INSERT INTO notifikasi (jenis, pesan, sumber)
    VALUES (
      'WARNING',
      CONCAT('Suhu air rendah (', $air, ' °C)'),
      'SENSOR'
    )
  ");
}

// 🧪 AIR KERUH
if ($ntu > 50) {
  $conn->query("
    INSERT INTO notifikasi (jenis, pesan, sumber)
    VALUES (
      'ALERT',
      CONCAT('Air keruh (', $ntu, ' NTU)'),
      'SENSOR'
    )
  ");
}

echo "OK";
