<?php
try {
    $pdo = new PDO(dsn: "mysql:host=localhost;dbname=to_do_it", username: "root", password: "");
    echo "Connexion réussie";
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
?>
