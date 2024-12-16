# Documentation du Projet Formation

## Contact
- **Nom:** Hamdi Abdallah
- **Email:** hamdi.abdallah@polytechnicien.tn

## Configuration requise
- PHP 8.0 ou supérieur
- Composer
- Symfony CLI
- MySQL/MariaDB
- Git

## Installation du projet

### 1. Cloner le projet depuis GitHub
```bash
# Cloner le dépôt
git clone https://github.com/Hamdiabdall/Project3-Sumfony.git

# Accéder au répertoire du projet
cd Project3-Sumfony
```

### 2. Installation des dépendances
```bash
# Installer les dépendances PHP avec Composer
composer install
```

### 3. Configuration de l'environnement

1. Copiez le fichier `.env` en `.env.local`:
```bash
cp .env .env.local
```

2. Modifiez le fichier `.env.local` pour configurer votre base de données:
```
DATABASE_URL="mysql://votre_utilisateur:votre_mot_de_passe@127.0.0.1:3306/nom_de_votre_base"
```

### 4. Création de la base de données
```bash
# Créer la base de données
php bin/console doctrine:database:create

# Exécuter les migrations
php bin/console doctrine:migrations:migrate
```

### 5. Configuration du dossier d'upload
```bash
# Créer le dossier pour les images des formations
mkdir public/uploads/formations
```

### 6. Démarrer le serveur
```bash
# Démarrer le serveur Symfony
symfony server:start
```

## Utilisation

Une fois le serveur démarré, vous pouvez accéder à l'application à l'adresse:
`http://localhost:8000`

### Fonctionnalités principales

1. **Gestion des formations**
   - Liste des formations: `/formation/`
   - Ajouter une formation: `/formation/nouvelle`
   - Modifier une formation: `/formation/{id}/modifier`
   - Voir une formation: `/formation/{id}/voir`
   - Supprimer une formation: `/formation/{id}/supprimer`

### Structure des données

Une formation contient:
- Titre
- Prix
- Durée
- Date de début
- Image (optionnelle)

## Maintenance

### Mise à jour du projet
```bash
# Mettre à jour les dépendances
composer update

# Mettre à jour la base de données
php bin/console doctrine:migrations:migrate
```

### Résolution des problèmes courants

1. **Problèmes de permissions**
```bash
# Donner les droits d'écriture au dossier var
chmod -R 777 var/
```

2. **Vider le cache**
```bash
php bin/console cache:clear
```

## Support

Pour toute question ou problème, vous pouvez:
- Ouvrir une issue sur GitHub
- Contacter le développeur: hamdi.abdallah@polytechnicien.tn

---

© 2024 Hamdi Abdallah. Tous droits réservés.
