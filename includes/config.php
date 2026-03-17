<?php
// Configuration pour PostgreSQL via Docker
// On utilise getenv pour récupérer les variables d'environnement (définies dans docker-compose.yml / .env)
$host = getenv('DB_HOST') ?: 'db';
$dbname = getenv('DB_NAME') ?: 'to_do_it';
$username = getenv('DB_USER') ?: 'todo_user';
$password = getenv('DB_PASSWORD') ?: 'todo_password';

try {
    // Utilisation du pilote pgsql au lieu de mysql
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}
?>