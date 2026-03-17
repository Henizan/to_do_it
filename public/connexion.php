<?php
require '../includes/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['email'], $_POST['password'])) {
        header("Location: connexion.html?error=" . urlencode("Erreur : Veuillez remplir tous les champs."));
        exit;
    }
    
    $email= htmlspecialchars($_POST['email']);
    $password= $_POST['password'];

    try {
        $stmt =$pdo->prepare(("SELECT * FROM utilisateur WHERE email = ?"));
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['user_name'] = $user['name'];
            header('Location: tab_bord.php');
            exit;
        } else {
            header("Location: connexion.html?error=" . urlencode("Email ou mot de passe incorrect."));
            exit;
        }
    } catch (PDOException $e) {
        header("Location: connexion.html?error=" . urlencode("Erreur SQL : " . $e->getMessage()));
        exit;
    }
}
?>