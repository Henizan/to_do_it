<?php
require '../includes/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST')
    if (!isset($_POST['email'], $_POST['password'])) {
        die("Erreur : Veuillez remplir tous les champs.");
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
        echo "Connexion réussie !";
        header('Location: tab_bord.html');
        exit;
    } else {
        echo "Email ou mdp incorrect.";
    }
} catch (PDOException $e) {
    die("Erreur SQL : " . $e->getMessage());
}
?>