<?php
session_start();
require '../../includes/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Non authentifié']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if ($data && isset($data['id_task'], $data['color'])) {
    $id_task = $data['id_task'];
    $color = $data['color'];
    $id_user = $_SESSION['user_id'];

    // Vérification basique anti-injection CSS (on n'accepte que des codes hex)
    if (!preg_match('/^#[a-f0-9]{6}$/i', $color)) {
         echo json_encode(['success' => false, 'error' => 'Couleur invalide.']);
         exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE task SET color = ? WHERE id_task = ? AND id_user = ?");
        $stmt->execute([$color, $id_task, $id_user]);
        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Erreur serveur SQL : ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Données invalides.']);
}
?>
