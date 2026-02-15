<?php
require "db.php";
header("Content-Type: application/json");

$data=[];

$q=$conn->query("
  SELECT created_at AS waktu, aktuator, aksi, sebab
  FROM log_aktuator
  ORDER BY created_at DESC
  LIMIT 50
");

while($r=$q->fetch_assoc()){
  $data[]=$r;
}

echo json_encode($data);
