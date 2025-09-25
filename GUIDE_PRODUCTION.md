# 🎯 Guide de démarrage ParoCompta - Production

## 🚀 Application nettoyée et prête !

Votre application ParoCompta a été complètement nettoyée et est maintenant prête pour une utilisation réelle avec la comptabilité de votre paroisse.

## 🔑 Connexion Administrateur

**Compte administrateur :**
- **Email :** `admin@paro.com`
- **Mot de passe :** `password`

## 📋 Étapes pour commencer

### 1. Première connexion
1. Allez sur : http://127.0.0.1:8000/login
2. Connectez-vous avec les identifiants admin ci-dessus
3. Vous arriverez sur un tableau de bord vide (normal !)

### 2. Créer votre première paroisse
1. Cliquez sur **"Paroisses"** dans le menu
2. Cliquez sur **"Nouvelle paroisse"**
3. Remplissez les informations :
   - **Nom :** Le nom de votre paroisse
   - **Suivi des paiements :** Activez si vous voulez suivre les rémunérations
   - **Montant hebdomadaire :** Ex: 1000 F par semaine
   - **Dossier de stockage :** Ex: `C:\ParoCompta\Documents\MaParoisse`

### 3. Créer un utilisateur saisissant
1. Allez dans **"Utilisateurs"** (si le menu existe) ou créez-en un manuellement
2. Assignez-le à votre paroisse
3. Il pourra alors saisir les fiches comptables

### 4. Commencer la saisie
1. Allez dans **"Fiches comptables"**
2. Cliquez sur **"Nouvelle fiche"**
3. Choisissez la catégorie appropriée :
   - **Autres Recettes**
   - **Quêtes Paroisse**
   - **Quêtes Stations**
   - **Autres Quêtes**

## 🛠️ Commandes utiles

### Nettoyer complètement la base de données
```bash
php artisan parocompta:clean --force
```

### Réinitialiser complètement l'application
```bash
php artisan parocompta:reset --force
```

### Créer uniquement le compte admin
```bash
php artisan db:seed --class=ProductionSeeder
```

## 📁 Structure des fichiers générés

Les fichiers Word seront sauvegardés selon cette structure :
```
VotreDossier/
├── NomParoisse/
│   ├── Trimestre1/
│   │   ├── AutresRecettes/
│   │   ├── QuetesParoisse/
│   │   ├── QuetesStations/
│   │   └── AutresQuetes/
│   ├── Trimestre2/
│   └── ...
```

## 🔧 Configuration recommandée

### Dossier de stockage
- **Windows :** `C:\ParoCompta\Documents\VotreParoisse`
- **Permissions :** Assurez-vous que le serveur web peut écrire dans ce dossier

### Sauvegarde
- Sauvegardez régulièrement votre base de données SQLite : `database/database.sqlite`
- Sauvegardez vos fichiers générés dans le dossier de stockage

## 🆘 En cas de problème

### Réinitialiser complètement
Si vous voulez repartir de zéro :
```bash
php artisan parocompta:reset --force
```

### Tester un dossier de stockage
En tant qu'admin, allez sur : http://127.0.0.1:8000/admin/storage-test

### Vérifier les permissions
Utilisez l'outil de test de stockage pour diagnostiquer les problèmes de permissions.

## 📊 Fonctionnalités disponibles

✅ **Gestion des paroisses**
✅ **Saisie des fiches comptables** (4 catégories)
✅ **Génération automatique de fichiers Word**
✅ **Suivi des paiements** des saisissants
✅ **Statistiques détaillées**
✅ **Stockage local sur PC**
✅ **Interface moderne et intuitive**

## 🎉 Prêt à commencer !

Votre application ParoCompta est maintenant configurée pour une utilisation réelle. Commencez par créer votre première paroisse et bonne comptabilité ! 📈

---

**Développé avec ❤️ pour la gestion comptable des paroisses catholiques**
