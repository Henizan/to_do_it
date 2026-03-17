<?php
session_start();
require '../../includes/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Non authentifié']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if ($data && isset($data['id_task'], $data['pos_x'], $data['pos_y'])) {
    $id_task = $data['id_task'];
    $pos_x = (int)$data['pos_x'];
    $pos_y = (int)$data['pos_y'];
    $id_user = $_SESSION['user_id'];

    try {
        $stmt = $pdo->prepare("UPDATE task SET pos_x = ?, pos_y = ? WHERE id_task = ? AND id_user = ?");
        $stmt->execute([$pos_x, $pos_y, $id_task, $id_user]);
        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Erreur serveur SQL : ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Données invalides.']);
}
?>
