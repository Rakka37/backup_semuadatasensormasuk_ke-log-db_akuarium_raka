<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "akuarium_raka";

$conn = new mysqli($host,$user,$pass,$db);
if($conn->connect_error){
  die("DB ERROR");
}
?>
