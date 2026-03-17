<?php
session_start();
require '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.html');
    exit;
}

$id_user = $_SESSION['user_id'];
$message = '';
$error = '';

// Traitement du formulaire de mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $surname = trim($_POST['surname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($name) || empty($surname) || empty($email)) {
        $error = "Les champs Nom, Prénom et Email sont obligatoires.";
    } else {
        try {
            if (!empty($password)) {
                // Mise à jour avec nouveau mot de passe
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE utilisateur SET name = ?, surname = ?, email = ?, password = ? WHERE id_user = ?");
                $stmt->execute([$name, $surname, $email, $hashed_password, $id_user]);
            } else {
                // Mise à jour sans toucher au mot de passe
                $stmt = $pdo->prepare("UPDATE utilisateur SET name = ?, surname = ?, email = ? WHERE id_user = ?");
                $stmt->execute([$name, $surname, $email, $id_user]);
            }
            
            // Mise à jour de la session si le nom a changé
            $_SESSION['user_name'] = $name;
            $message = "Vos informations ont été mises à jour avec succès.";
        } catch (PDOException $e) {
            // Gestion de l'erreur d'email en doublon
            if ($e->getCode() == 23505) { 
                $error = "Cet email est déjà utilisé par un autre compte.";
            } else {
                $error = "Erreur lors de la mise à jour : " . $e->getMessage();
            }
        }
    }
}

// Récupération des données actuelles pour pré-remplir le formulaire
try {
    $stmt = $pdo->prepare("SELECT name, surname, email FROM utilisateur WHERE id_user = ?");
    $stmt->execute([$id_user]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur de récupération des données.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://use.typekit.net/aga5ynr.css">
    <title>To Do It - Paramètres</title>
    <style>
        .settings-content {
            margin-left: 10vw; /* Laisse la place à la sidebar (environ 11vw au total) */
            display: flex;
            justify-content: center;
            align-items: center; /* Centrage vertical */
            min-height: 80vh; /* Prend toute la hauteur de l'écran ou presque */
        }
        .msg-success { color: #81C784; background: rgba(129,199,132,0.1); padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center; }
        .msg-error { color: #ff4757; background: rgba(255,71,87,0.1); padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center; }
    </style>
</head>

<body>
    
    <header>
        <div class="main-header">
            <div class="main-title">
                <h1>To Do It</h1>
            </div>
            <p class="main-text"> Modifiez vos informations personnelles ici.</p>
        </div>
    </header>

    <div class="logo">
        <a href="index.html"><img src="../assets/images/logo_todoit.png" alt="To Do It logo"></a>
    </div>
    <nav class="sidebar">
        <ul>
            <li><a href="tab_bord.php">Tableau de bord</a></li>
            <li><a href="calendrier.php">Calendrier</a></li>
            <li><a href="setting.php">Paramètres</a></li>
            <li><a href="index.html">Se déconnecter</a></li>
        </ul>
    </nav>

    <main class="settings-content">
        <div class="loginbox" style="margin: 0; width: 30vw; max-width: 500px; padding: 30px;">
            <h3>Préférences du compte</h3>
            
            <?php if ($message): ?>
                <div class="msg-success"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="msg-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="setting.php">
                <div class="form-box">
                    <label for="name">Prénom</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
                </div>
                <div class="form-box">
                    <label for="surname">Nom</label>
                    <input type="text" id="surname" name="surname" value="<?= htmlspecialchars($user['surname'] ?? '') ?>" required>
                </div>
                <div class="form-box">
                    <label for="email">Adresse Émail</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                </div>
                <div class="form-box">
                    <label for="password">Nouveau mot de passe</label>
                    <input type="password" id="password" name="password" placeholder="Laisser vide pour ne pas changer">
                </div>
                
                <button type="submit" class="btn">Mettre à jour</button>
            </form>
        </div>
    </main>
</body>
</html>