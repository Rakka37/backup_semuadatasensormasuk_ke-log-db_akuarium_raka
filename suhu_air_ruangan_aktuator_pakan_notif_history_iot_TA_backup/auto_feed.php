<?php
require "db.php";

// log pakan otomatis
$conn->query("
  INSERT INTO log_pakan(mode,jumlah_putaran,keterangan)
  VALUES('OTOMATIS',1,'Pakan otomatis sesuai jadwal')
");

// log aktuator
$conn->query("
  INSERT INTO log_aktuator(aktuator,aksi,sebab,sumber)
  VALUES('SERVO_PAKAN','PUTAR','Jadwal otomatis','ESP32')
");

// NOTIFIKASI
$conn->query("
  INSERT INTO notifikasi(jenis,pesan,sumber)
  VALUES('INFO','Pakan otomatis diberikan sesuai jadwal','ESP32')
");

echo "OK";
