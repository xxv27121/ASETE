<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/repos/MaterialRepo.php';
require_once __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = (int)($_POST['material_id'] ?? 0);
$repo = new MaterialRepo();
$material = $repo->findById($id);

if (!$material) {
    http_response_code(404);
    exit('Material no encontrado');
}

$stmt = db()->prepare(
    'UPDATE prestamos
     SET fecha_devolucion = CURRENT_TIMESTAMP
     WHERE material_id = ? AND usuario_id = ?
     AND fecha_devolucion IS NULL'
);
$stmt->execute([$id, currentUser()['id']]);

if ($stmt->rowCount() > 0) {
    $repo->setDisponible($id, true);
}

header('Location: material.php?id=' . $id);
exit;