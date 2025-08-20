<?php
$host = "localhost";
$dbname = "crud_productos";
$username = "root";
$password = "usbw";

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die(json_encode([
    "status" => "error",
    "message" => "Error de conexión: " . $e->getMessage()
  ]));
}