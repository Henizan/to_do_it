<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.html');
    exit;
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
    <title>To Do It - Calendrier</title>
    <script src="
https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js
"></script>
    <script src="../assets/js/script.js"></script>
</head>


<body>


    <header>
        <div class="main-header">
            <div class="main-title">
                <h1>To Do It</h1>
            </div>
            <p class="main-text"> Organisez vos journées et accomplissez vos tâches facilement .</p>
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
    <div class="calendarpagebox">
        <div class="calendarbox">
            <div id='calendar' class="calendar"></div>
        </div>

        <div class="addeventbox">
            <h3>Ajouter un évévenement / une tâche</h3>
            <form id="event-form">
                <div class="form-box">
                    <label for="event-title">Titre de la tâche</label>
                    <input type="text" id="event-title" name="event-title" placeholder="Titre de la tâche" required>
                </div>
                <div class="form-box">
                    <label for="event-description">Description de la tâche</label>
                    <input id="event-description" name="event-description" placeholder="Description de la tâche">
                </div>
                <div class="form-box">
                    <label for="event-start">Date et heure du début de la tâche</label>
                    <input type="datetime-local" id="event-start" name="event-start" required>
                </div>
                <div class="form-box">
                    <label for="event-end">Date et heure de la fin de la tâche</label>
                    <input type="datetime-local" id="event-end" name="event-end">
                </div>
                <div class="form-box">
                    <label for="event-color">Couleur du Post-it</label>
                    <select id="event-color" name="event-color" style="padding: 10px; border-radius: 5px; border: 1px solid #ccc; width: 100%; margin-top: 5px;">
                        <option value="#FFEB3B" style="background-color: #FFEB3B;">Jaune (Par défaut)</option>
                        <option value="#FFCDD2" style="background-color: #FFCDD2;">Rose Pastel</option>
                        <option value="#C8E6C9" style="background-color: #C8E6C9;">Vert Pastel</option>
                        <option value="#BBDEFB" style="background-color: #BBDEFB;">Bleu Pastel</option>
                    </select>
                </div>
                <button type="button" id="add-event-button" class="add-event-btn">ajouter</button>
            </form>
        </div>
        
        <!-- MODALE D'ÉDITION DE TÂCHE (Masquée par défaut) -->
        <div id="edit-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
            <div class="addeventbox" style="background: white; padding: 20px; border-radius: 8px; width: 400px; max-width: 90%; position: relative;">
                <button type="button" id="close-modal-btn" style="position: absolute; top: 10px; right: 10px; background: transparent; border: none; font-size: 1.5em; cursor: pointer;">&times;</button>
                <h3>Modifier la tâche</h3>
                <form id="edit-form">
                    <input type="hidden" id="edit-id_task" name="edit-id_task">
                    <div class="form-box">
                        <label for="edit-title">Titre</label>
                        <input type="text" id="edit-title" name="edit-title" required>
                    </div>
                    <div class="form-box">
                        <label for="edit-description">Description</label>
                        <input id="edit-description" name="edit-description">
                    </div>
                    <div class="form-box">
                        <label for="edit-start">Début</label>
                        <input type="datetime-local" id="edit-start" name="edit-start" required>
                    </div>
                    <div class="form-box">
                        <label for="edit-end">Fin</label>
                        <input type="datetime-local" id="edit-end" name="edit-end">
                    </div>
                    <div class="form-box">
                        <label for="edit-color">Couleur</label>
                        <select id="edit-color" name="edit-color" style="padding: 10px; border-radius: 5px; border: 1px solid #ccc; width: 100%; margin-top: 5px;">
                            <option value="#FFEB3B" style="background-color: #FFEB3B;">Jaune (Par défaut)</option>
                            <option value="#FFCDD2" style="background-color: #FFCDD2;">Rose Pastel</option>
                            <option value="#C8E6C9" style="background-color: #C8E6C9;">Vert Pastel</option>
                            <option value="#BBDEFB" style="background-color: #BBDEFB;">Bleu Pastel</option>
                        </select>
                    </div>
                    <div style="display: flex; gap: 10px; margin-top: 20px;">
                        <button type="button" id="save-edit-button" class="add-event-btn" style="flex: 1;">Enregistrer</button>
                        <button type="button" id="delete-event-button" class="add-event-btn" style="flex: 1; background-color: #ff4757;">Supprimer</button>
                    </div>
                </form>
            </div>
        </div>


    </div>
</body>