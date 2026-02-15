<?php
require "../config/db.php";

$img = $_FILES['image'] ?? null;
if(!$img) exit("NO FILE");

$name = time().".jpg";
$path = "../uploads/cam/".$name;
move_uploaded_file($img['tmp_name'],$path);

$size = filesize($path)/1024;

$conn->query("
INSERT INTO gambar(nama_file,path,ukuran_kb,sumber)
VALUES('$name','$path','$size','ESP32-CAM')
");

echo "OK";
