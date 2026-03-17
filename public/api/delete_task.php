<?php
session_start();
require '../../includes/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Non authentifié']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if ($data && isset($data['id_task'])) {
    $id_task = $data['id_task'];
    $id_user = $_SESSION['user_id']; // Sécurité : on supprime seulement si la tâche appartient à ce user
    
    try {
        $stmt = $pdo->prepare("DELETE FROM task WHERE id_task = ? AND id_user = ?");
        $stmt->execute([$id_task, $id_user]);
        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Erreur serveur SQL : ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'ID de tâche manquant.']);
}
?>
