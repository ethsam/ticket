# Projet Ticket

## 📋 Table des matières

- [Installation des dépendances](#installation-des-dépendances)
- [Configuration Symfony](#configuration-symfony)

---

## 🔧 Installation des dépendances

### Dépendances PHP requises

Installer les bundles nécessaires avec Composer :

```bash
# ORM et base de données
composer require symfony/orm-pack

# Développement
composer require --dev symfony/maker-bundle

# Templates et vues
composer require symfony/twig-bundle

# Administration
composer require easycorp/easyadmin-bundle

# Gestion des uploads
composer require vich/uploader-bundle

# Pagination
composer require knplabs/knp-paginator-bundle

# Gestionnaire de fichiers
composer require artgris/filemanager-bundle

# Traitement d'images
composer require liip/imagine-bundle
```

---

## ⚙️ Configuration Symfony

### Commandes essentielles

#### Base de données

```bash
# Créer la base de données
php bin/console doctrine:database:create

# Appliquer les migrations
symfony console doctrine:migrations:migrate
```

#### Développement

```bash
# Générer une nouvelle entité
php bin/console make:entity
```

```bash
# Données de base
php bin/console initialize
```

---

## 📝 Notes

- Assurez-vous que toutes les dépendances sont installées avant d'exécuter les commandes

---

## 👨‍💻 Développé par

**Samuel Ethève** - Expert en développement web et mobile
- Fondateur de ScaleInsight
- Associé chez Viceversa
- 17+ années d'expérience dans le digital
