<?php
require "db.php";
header("Content-Type: application/json");

$data=[];

$q = $conn->query("
  SELECT 
    created_at AS waktu,
    suhu_ruangan,
    suhu_air,
    ntu,
    kualitas_air
  FROM sensor_data
  ORDER BY created_at DESC
  LIMIT 50
");

while($r=$q->fetch_assoc()){
  $data[]=[
    "waktu"=>$r['waktu'],
    "ruangan"=>$r['suhu_ruangan'],
    "air"=>$r['suhu_air'],
    "ntu"=>$r['ntu'],
    "kualitas"=>$r['kualitas_air']
  ];
}

echo json_encode($data);
