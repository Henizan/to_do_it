<?php
session_start();
require '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.html');
    exit;
}

$id_user = $_SESSION['user_id'];
$tasks = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM task WHERE id_user = ? ORDER BY start_date ASC");
    $stmt->execute([$id_user]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des tâches.";
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
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.typekit.net/aga5ynr.css">
    <title>To Do It - Tableau de bord</title>
    <!-- Inclusion du script global (pour la suppression) -->
    <script src="../assets/js/script.js"></script>
    <style>
        body, html {
            overflow-x: hidden;
            height: 100%;
        }
        .dashboard-content { 
            margin-left: 260px; /* Aligné après la sidebar */
            height: 100vh;
            position: relative; /* Conteneur pour nos post-its en absolu */
        }
        
        /* Design du Post-it */
        .post-it { 
            position: absolute;
            width: 250px;
            min-height: 250px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 2px 4px 6px rgba(0,0,0,0.15);
            /* Petit effet de rotation aléatoire pour le réalisme (sera géré par JS ou inline) */
            cursor: grab;
            transition: box-shadow 0.2s, transform 0.2s;
            font-family: 'Caveat', cursive; /* Police manuscrite pour effet réaliste */
            font-size: 1.2em;
            color: #333;
            /* L'épingle ou le bout de scotch */
            border-top: 15px solid rgba(0,0,0,0.05);
        }
        .post-it:active {
            cursor: grabbing;
            box-shadow: 4px 8px 12px rgba(0,0,0,0.25);
            transform: scale(1.02) rotate(0deg) !important;
            z-index: 1000 !important;
        }

        .post-it h3 { margin: 0 0 10px 0; font-size: 1.5em; border-bottom: 2px dashed rgba(0,0,0,0.1); padding-bottom: 5px; }
        .post-it p { margin: 0 0 15px 0; flex-grow: 1; }
        .post-it .dates { font-size: 0.85em; opacity: 0.8; margin-bottom: 10px;}
        
        .post-it-actions { display: flex; justify-content: space-between; align-items: center; }
        
        .delete-btn { 
            background: transparent; 
            color: #ff4757; 
            border: 1px solid #ff4757; 
            padding: 3px 8px; 
            border-radius: 4px; 
            cursor: pointer; 
            font-family: 'Caveat', cursive;
            font-size: 1em;
            transition: 0.2s;
        }
        .delete-btn:hover { background: #ff4757; color: white;}

        /* Palette de couleur */
        .color-picker {
            display: flex;
            gap: 5px;
        }
        .color-dot {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            cursor: pointer;
            border: 1px solid rgba(0,0,0,0.1);
        }
    </style>
</head>

<body>
    
    <header>
        <div class="main-header">
            <div class="main-title">
                <h1>To Do It</h1>
            </div>
            <p class="main-text"> Bonjour <?= htmlspecialchars($_SESSION['user_name']) ?>, disposez vos post-its librement !</p>
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

    <main class="dashboard-content" id="dashboard-board">
        
        <?php if (isset($error)): ?>
            <p style="color: red; position: fixed;"><?= $error ?></p>
        <?php endif; ?>

        <?php if (empty($tasks)): ?>
            <div style="background: rgba(255,255,255,0.7); display: inline-block; padding: 20px; border-radius: 8px; margin: 20px;">
                <p>Vous n'avez pas de Post-it. Rendez-vous dans le <strong>Calendrier</strong> pour en créer un !</p>
            </div>
        <?php else: ?>
            <?php foreach ($tasks as $task): 
                // Valeurs par défaut pour prévenir les bugs graphiques
                $x = !empty($task['pos_x']) ? (int)$task['pos_x'] : rand(280, 800);
                $y = !empty($task['pos_y']) ? (int)$task['pos_y'] : rand(100, 400);
                $color = !empty($task['color']) ? htmlspecialchars($task['color']) : '#FFEB3B';
                $rotation = rand(-3, 3); // Légère rotation pour le réalisme
            ?>
                <div class="post-it" 
                     id="task-<?= $task['id_task'] ?>" 
                     data-id="<?= $task['id_task'] ?>"
                     style="left: <?= $x ?>px; top: <?= $y ?>px; background-color: <?= $color ?>; transform: rotate(<?= $rotation ?>deg);">
                    
                    <div>
                        <h3><?= htmlspecialchars($task['title']) ?></h3>
                        <p><?= nl2br(htmlspecialchars($task['description'])) ?></p>
                        <div class="dates">
                            🕒 <?= date('d/m/Y H:i', strtotime($task['start_date'])) ?>
                            <?php if ($task['end_date']): ?>
                                <br>⏳ <?= date('d/m/Y H:i', strtotime($task['end_date'])) ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="post-it-actions">
                        <div class="color-picker">
                            <!-- Couleurs pastel au choix -->
                            <div class="color-dot" style="background-color: #FFEB3B;" onclick="changeColor(<?= $task['id_task'] ?>, '#FFEB3B')"></div>
                            <div class="color-dot" style="background-color: #FFCDD2;" onclick="changeColor(<?= $task['id_task'] ?>, '#FFCDD2')"></div>
                            <div class="color-dot" style="background-color: #C8E6C9;" onclick="changeColor(<?= $task['id_task'] ?>, '#C8E6C9')"></div>
                            <div class="color-dot" style="background-color: #BBDEFB;" onclick="changeColor(<?= $task['id_task'] ?>, '#BBDEFB')"></div>
                        </div>
                        <button class="delete-btn" onclick="deleteTaskDashboard(<?= $task['id_task'] ?>)">X</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

    <script>
        /* ======================== *
         *   LOGIQUE DRAG & DROP    *
         * ======================== */
        let draggedItem = null;
        let startX, startY;
        let zIndexCounter = 10;

        document.querySelectorAll('.post-it').forEach(postit => {
            postit.addEventListener('mousedown', function(e) {
                // Si on clique sur un bouton de la palette ou de suppression, on ne drag pas
                if(e.target.tagName.toLowerCase() === 'button' || e.target.classList.contains('color-dot')) {
                    return;
                }
                
                draggedItem = this;
                this.style.zIndex = ++zIndexCounter;
                
                // Calcul de la distance du pointeur par rapport au bord supérieur gauche du post-it
                startX = e.pageX - this.offsetLeft;
                startY = e.pageY - this.offsetTop;
            });
        });

        document.addEventListener('mousemove', function(e) {
            if (!draggedItem) return;
            e.preventDefault();

            // Nouvelle position en fonction du décalage initial
            let newX = e.pageX - startX;
            let newY = e.pageY - startY;

            // Applique
            draggedItem.style.left = newX + 'px';
            draggedItem.style.top = newY + 'px';
        });

        document.addEventListener('mouseup', function() {
            if (draggedItem) {
                // Sauvegarde via API Fetch
                let taskId = draggedItem.dataset.id;
                let finalX = parseInt(draggedItem.style.left, 10);
                let finalY = parseInt(draggedItem.style.top, 10);

                fetch('api/update_position.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_task: taskId, pos_x: finalX, pos_y: finalY })
                }).catch(err => console.error("Erreur de sauvegarde position:", err));

                draggedItem = null;
            }
        });

        /* ======================== *
         *   LOGIQUE INTERACTIONS   *
         * ======================== */
        function deleteTaskDashboard(taskId) {
            if (confirm("Voulez-vous vraiment jeter ce post-it ?")) {
                deleteTask(taskId, function() {
                    let item = document.getElementById('task-' + taskId);
                    if (item) {
                        item.style.transform = 'scale(0)';
                        setTimeout(() => item.remove(), 300);
                    }
                });
            }
        }

        function changeColor(taskId, hexColor) {
            let item = document.getElementById('task-' + taskId);
            
            // Met à jour visuellement tout de suite pour la fluidité
            if (item) {
                item.style.backgroundColor = hexColor;
            }

            // Sauvegarde en base de données
            fetch('api/update_color.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_task: taskId, color: hexColor })
            }).then(r => r.json())
              .then(data => {
                  if (!data.success) {
                      alert("Erreur lors de la sauvegarde de la couleur.");
                  }
              });
        }
    </script>
</body>
</html>