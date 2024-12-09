<?php
require '../includes/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $surname = htmlspecialchars($_POST['surname']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            header("Location: inscription.html?error=Cet email est déjà utilisé.");
            exit;
        }

        
        $stmt = $pdo->prepare("INSERT INTO utilisateur (name, surname, email, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $surname, $email, $password]);

       
        header("Location: connexion.html");
        exit;
    } catch (PDOException $e) {
        header("Location: inscription.html?error=Erreur SQL : " . urlencode($e->getMessage()));
        exit;
    }
}
?>
