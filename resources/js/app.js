import './bootstrap';
import Alpine from 'alpinejs';
import axios from 'axios';

// Import Bootstrap JS
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

window.Alpine = Alpine;
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;

// Gestion des formulaires dynamiques
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation des formulaires dynamiques
    initDynamicForms();
    
    // Initialisation du dashboard
    initDashboard();
    
    // Initialisation des tableaux de données
    initDataTables();
});

// Fonction pour initialiser les formulaires dynamiques
function initDynamicForms() {
    const forms = document.querySelectorAll('.dynamic-form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const url = form.getAttribute('action');
            const method = form.getAttribute('method').toUpperCase();
            
            // Conversion des données en JSON pour les formulaires avec data-json="true"
            if (form.dataset.json === 'true') {
                const jsonData = {};
                for (const [key, value] of formData.entries()) {
                    jsonData[key] = value;
                }
                
                axios({
                    method: method,
                    url: url,
                    data: {
                        ...Object.fromEntries(formData),
                        data_json: JSON.stringify(jsonData)
                    },
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    showNotification('Succès', response.data.message || 'Opération réussie', 'success');
                    
                    // Redirection si spécifiée
                    if (form.dataset.redirect) {
                        window.location.href = form.dataset.redirect;
                    }
                    
                    // Téléchargement si disponible
                    if (response.data.file_url) {
                        window.open(response.data.file_url, '_blank');
                    }
                })
                .catch(error => {
                    showNotification('Erreur', error.response?.data?.message || 'Une erreur est survenue', 'error');
                    console.error('Erreur:', error);
                });
            } else {
                // Soumission standard du formulaire
                axios({
                    method: method,
                    url: url,
                    data: formData,
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    showNotification('Succès', response.data.message || 'Opération réussie', 'success');
                    
                    // Redirection si spécifiée
                    if (form.dataset.redirect) {
                        window.location.href = form.dataset.redirect;
                    }
                })
                .catch(error => {
                    showNotification('Erreur', error.response?.data?.message || 'Une erreur est survenue', 'error');
                    console.error('Erreur:', error);
                });
            }
        });
    });
}

// Fonction pour initialiser le dashboard
function initDashboard() {
    const dashboardStats = document.getElementById('dashboard-stats');
    if (!dashboardStats) return;
    
    // Charger les statistiques
    axios.get('/api/payments/stats')
        .then(response => {
            const stats = response.data;
            
            // Mettre à jour les statistiques
            document.getElementById('total-payments').textContent = stats.total_payments + ' €';
            document.getElementById('parishes-count').textContent = stats.parishes_count;
            document.getElementById('average-per-parish').textContent = stats.average_per_parish.toFixed(2) + ' €';
            
            // Initialiser le graphique si Chart.js est disponible
            if (window.Chart) {
                initCharts(stats);
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement des statistiques:', error);
        });
}

// Fonction pour initialiser les tableaux de données
function initDataTables() {
    const tables = document.querySelectorAll('.data-table');
    
    tables.forEach(table => {
        const endpoint = table.dataset.endpoint;
        if (!endpoint) return;
        
        // Charger les données
        axios.get(endpoint)
            .then(response => {
                const data = response.data.data || response.data;
                
                // Vider le tableau
                const tbody = table.querySelector('tbody');
                tbody.innerHTML = '';
                
                // Remplir le tableau avec les données
                data.forEach(item => {
                    const row = document.createElement('tr');
                    
                    // Créer les cellules en fonction des colonnes définies
                    const columns = JSON.parse(table.dataset.columns || '[]');
                    columns.forEach(column => {
                        const cell = document.createElement('td');
                        cell.className = 'px-4 py-2 border';
                        
                        // Gérer les valeurs imbriquées (ex: "parish.name")
                        if (column.field.includes('.')) {
                            const parts = column.field.split('.');
                            let value = item;
                            for (const part of parts) {
                                value = value?.[part];
                            }
                            cell.textContent = value || '';
                        } else {
                            cell.textContent = item[column.field] || '';
                        }
                        
                        row.appendChild(cell);
                    });
                    
                    // Ajouter les actions
                    if (table.dataset.actions === 'true') {
                        const actionsCell = document.createElement('td');
                        actionsCell.className = 'px-4 py-2 border';
                        
                        // Bouton Voir
                        const viewBtn = document.createElement('button');
                        viewBtn.className = 'bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded mr-2';
                        viewBtn.textContent = 'Voir';
                        viewBtn.addEventListener('click', () => {
                            window.location.href = `/${table.dataset.resource}/${item.id}`;
                        });
                        actionsCell.appendChild(viewBtn);
                        
                        // Bouton Modifier
                        const editBtn = document.createElement('button');
                        editBtn.className = 'bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 rounded mr-2';
                        editBtn.textContent = 'Modifier';
                        editBtn.addEventListener('click', () => {
                            window.location.href = `/${table.dataset.resource}/${item.id}/edit`;
                        });
                        actionsCell.appendChild(editBtn);
                        
                        // Bouton Supprimer
                        const deleteBtn = document.createElement('button');
                        deleteBtn.className = 'bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded';
                        deleteBtn.textContent = 'Supprimer';
                        deleteBtn.addEventListener('click', () => {
                            if (confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
                                axios.delete(`/api/${table.dataset.resource}/${item.id}`)
                                    .then(() => {
                                        showNotification('Succès', 'Élément supprimé avec succès', 'success');
                                        // Recharger le tableau
                                        initDataTables();
                                    })
                                    .catch(error => {
                                        showNotification('Erreur', error.response?.data?.message || 'Une erreur est survenue', 'error');
                                    });
                            }
                        });
                        actionsCell.appendChild(deleteBtn);
                        
                        row.appendChild(actionsCell);
                    }
                    
                    tbody.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Erreur lors du chargement des données:', error);
            });
    });
}

// Fonction pour afficher une notification
function showNotification(title, message, type = 'info') {
    // Créer l'élément de notification
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        'bg-blue-500'
    } text-white`;
    
    // Ajouter le contenu
    notification.innerHTML = `
        <div class="font-bold">${title}</div>
        <div>${message}</div>
    `;
    
    // Ajouter au DOM
    document.body.appendChild(notification);
    
    // Supprimer après 5 secondes
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

Alpine.start();
