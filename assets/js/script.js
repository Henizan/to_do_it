document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    
    // Si nous sommes sur la page calendrier
    if (calendarEl) {
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: 'api/read_tasks.php',
            editable: true, // Permet le Drag & Drop et le redimensionnement
            
            // Quand on déplace une tâche
            eventDrop: function(info) {
                updateTaskDates(info.event);
            },
            
            // Quand on allonge/raccourcit la durée d'une tâche
            eventResize: function(info) {
                updateTaskDates(info.event);
            },

            // Quand on clique sur une tâche
            eventClick: function(info) {
                // Remplir la modale avec les infos de l'événement
                document.getElementById('edit-id_task').value = info.event.id;
                document.getElementById('edit-title').value = info.event.title;
                document.getElementById('edit-description').value = info.event.extendedProps.description || '';
                document.getElementById('edit-start').value = formatLocal(info.event.start);
                
                if (info.event.end) {
                    document.getElementById('edit-end').value = formatLocal(info.event.end);
                } else {
                    document.getElementById('edit-end').value = '';
                }

                // Récupérer la couleur (via borderColor ou backgroundColor)
                let color = info.event.backgroundColor || '#FFEB3B';
                document.getElementById('edit-color').value = color;

                // Afficher la modale
                document.getElementById('edit-modal').style.display = 'flex';
                
                // Garder une référence à l'événement cliqué pour pouvoir faire remove() plus tard
                window.currentEditingEvent = info.event;
            }
        });
        calendar.render();

        /* ==================== A J O U T E R ==================== */
        var addButton = document.getElementById('add-event-button');
        if (addButton) {
            addButton.addEventListener('click', function() {
                var form = document.getElementById('event-form');
                
                // Petite validation côté client
                if (!document.getElementById('event-title').value || !document.getElementById('event-start').value) {
                    alert("Veuillez remplir le titre et la date de début.");
                    return;
                }

                fetch('api/create_task.php', {
                    method: 'POST',
                    body: new FormData(form)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        calendar.refetchEvents();
                        form.reset();
                    } else {
                        alert(data.error || "Erreur lors de l'ajout.");
                    }
                })
                .catch(error => {
                    console.error('Erreur AJAX:', error);
                    alert("Erreur de connexion avec le serveur.");
                });
            });
        }

        /* ==================== M O D I F I E R ==================== */
        // Fermer la modale
        var closeModalBtn = document.getElementById('close-modal-btn');
        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', function() {
                document.getElementById('edit-modal').style.display = 'none';
            });
        }

        // Sauvegarder les modifications
        var saveEditBtn = document.getElementById('save-edit-button');
        if (saveEditBtn) {
            saveEditBtn.addEventListener('click', function() {
                var form = document.getElementById('edit-form');
                
                if (!document.getElementById('edit-title').value || !document.getElementById('edit-start').value) {
                    alert("Le titre et la date de début sont obligatoires.");
                    return;
                }

                fetch('api/update_task.php', {
                    method: 'POST',
                    body: new FormData(form) // Envoi form-data
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('edit-modal').style.display = 'none';
                        calendar.refetchEvents();
                    } else {
                        alert(data.error || "Erreur lors de la modification.");
                    }
                })
                .catch(error => console.error('Erreur AJAX:', error));
            });
        }

        // Supprimer depuis la modale
        var deleteEditBtn = document.getElementById('delete-event-button');
        if (deleteEditBtn) {
            deleteEditBtn.addEventListener('click', function() {
                if (confirm("Voulez-vous vraiment supprimer cette tâche ?")) {
                    var taskId = document.getElementById('edit-id_task').value;
                    deleteTask(taskId, function() {
                        if (window.currentEditingEvent) {
                            window.currentEditingEvent.remove();
                        }
                        document.getElementById('edit-modal').style.display = 'none';
                    });
                }
            });
        }
    }
});

// Formate la date JavaScript en format SQL (YYYY-MM-DD HH:MM:SS) en respectant le fuseau horaire local
function formatLocal(date) {
    let local = new Date(date.getTime() - (date.getTimezoneOffset() * 60000));
    return local.toISOString().slice(0, 19).replace('T', ' ');
}

// Fonction utilitaire pour mettre à jour les dates via l'API
function updateTaskDates(event) {
    let data = {
        id_task: event.id,
        start_date: formatLocal(event.start)
    };
    
    if (event.end) {
        data.end_date = formatLocal(event.end);
    }

    fetch('api/update_task.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert(data.error || "Erreur lors de la modification de la date.");
        }
    })
    .catch(error => console.error('Erreur:', error));
}

// Fonction utilitaire pour supprimer une tâche via l'API
function deleteTask(taskId, onSuccess) {
    fetch('api/delete_task.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_task: taskId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            onSuccess();
        } else {
            alert(data.error || "Erreur lors de la suppression.");
        }
    })
    .catch(error => console.error('Erreur:', error));
}
