<?php
require_once 'db.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
  if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
  } else {
    $stmt = $pdo->query("SELECT * FROM productos WHERE estado=1");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
  }
  exit;
}

if ($method === 'POST') {
  $accion = $_GET['accion'] ?? '';

  $nombre = $_POST['nombre'] ?? '';
  $precio = isset($_POST['precio']) ? floatval($_POST['precio']) : 0;
  $stock  = isset($_POST['stock']) ? intval($_POST['stock']) : 0;

  if ($precio < 0 || $stock < 0) {
    echo json_encode(["status" => "error", "message" => "No se permiten valores negativos"]);
    exit;
  }

  $imagen = '';
  if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $nombreImg = basename($_FILES['imagen']['name']);
    $ruta = 'uploads/' . uniqid() . '_' . $nombreImg;
    move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);
    $imagen = $ruta;
  }

  if ($accion === 'crear') {
    $stmt = $pdo->prepare("INSERT INTO productos (nombre, precio, stock, imagen) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nombre, $precio, $stock, $imagen]);
    echo json_encode(["status" => "ok", "id" => $pdo->lastInsertId()]);
    exit;
  }

  if ($accion === 'editar') {
    $id = $_POST['id'] ?? '';
    $stmt = $pdo->prepare("UPDATE productos SET nombre=?, precio=?, stock=? WHERE id=?");
    $stmt->execute([$nombre, $precio, $stock, $id]);
    echo json_encode(["status" => "ok"]);
    exit;
  }
}

if ($method === 'DELETE') {
  $data = json_decode(file_get_contents("php://input"), true);
  $stmt = $pdo->prepare("UPDATE productos SET estado=0 WHERE id=?");
  $stmt->execute([$data['id']]);
  echo json_encode(["status" => "ok"]);
  exit;
}

http_response_code(405);
echo json_encode(["status" => "error", "message" => "Método no permitido"]);