<?php
require "db.php";

$feedFile = __DIR__."/feed.txt";
if(!file_exists($feedFile)){
  file_put_contents($feedFile,"0");
}

if(isset($_GET['feed'])){
  // trigger servo ESP32
  file_put_contents($feedFile,"1");

  // log pakan
  $conn->query("
    INSERT INTO log_pakan(mode,jumlah_putaran,keterangan)
    VALUES('MANUAL',1,'Pakan manual via web')
  ");

  // log aktuator
  $conn->query("
    INSERT INTO log_aktuator(aktuator,aksi,sebab,sumber)
    VALUES('SERVO_PAKAN','PUTAR','Manual feed','WEB')
  ");

  // NOTIFIKASI
  $conn->query("
    INSERT INTO notifikasi(jenis,pesan,sumber)
    VALUES('INFO','Pakan manual diberikan','WEB')
  ");

  echo "1";
  exit;
}

if(isset($_GET['reset'])){
  file_put_contents($feedFile,"0");
  echo "0";
  exit;
}

echo trim(file_get_contents($feedFile));
