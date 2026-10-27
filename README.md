# 🏢 ERP Laminas

> Application ERP de gestion d'entreprise développée avec le framework **Laminas MVC** (PHP) et **laminas-db**.

![PHP](https://img.shields.io/badge/PHP-8.1%20|%208.2%20|%208.3-777BB4?logo=php&logoColor=white)
![Laminas](https://img.shields.io/badge/Laminas-MVC%203.7-2B6CB0)
![Docker](https://img.shields.io/badge/Docker-ready-2496ED?logo=docker&logoColor=white)
![License](https://img.shields.io/badge/License-BSD--3--Clause-green)

---

## 📌 Table des matières

1. [Présentation](#présentation)
2. [Écosystème du projet](#écosystème-du-projet)
3. [Fonctionnalités](#fonctionnalités)
4. [Stack technique](#stack-technique)
5. [Architecture du projet](#architecture-du-projet)
6. [Prérequis](#prérequis)
7. [Installation](#installation)
8. [Configuration](#configuration)
9. [Lancement](#lancement)
10. [Docker](#docker)
11. [Tests et qualité du code](#tests-et-qualité-du-code)
12. [Déploiement](#déploiement)
13. [Contribuer](#contribuer)
14. [Licence](#licence)
15. [Auteur](#auteur)

---

## Présentation

**ERP Laminas** est le socle applicatif d'un ERP web. Il s'appuie sur l'architecture **MVC modulaire** de Laminas, la couche d'accès aux données **laminas-db** et la gestion de sessions **laminas-session** pour authentifier et suivre les utilisateurs.

> ✏️ **À compléter :** décrivez en 2-3 phrases le public cible (PME, secteur d'activité…) et les processus métier couverts.

---

## Écosystème du projet

Ce dépôt fait partie d'un ensemble de trois projets liés :

| Dépôt | Rôle |
|---|---|
| **erp-laminas** *(ce dépôt)* | ERP de base : socle Laminas MVC, accès base de données, sessions |
| [`erp-ai-laminas`](https://github.com/kammounimen/erp-ai-laminas) | Variante du frontend avec le module `Invoice` et l'intégration IA (JWT, client HTTP Guzzle) |
| [`ERP-AI`](https://github.com/kammounimen/ERP-AI) | Backend Python/FastAPI : extraction de factures et recherche intelligente avec Mistral AI |

```
 erp-laminas  ──(évolue vers)──►  erp-ai-laminas  ──HTTP/JSON──►  ERP-AI (FastAPI + Mistral)
 (ERP de base)                    (ERP + module IA)                 (extraction, recherche)
```

> ✏️ Vérifiez que cette description correspond bien à la relation entre vos dépôts, et ajustez-la si besoin.

---

## Fonctionnalités

- 🔐 **Authentification et sessions** (`laminas-session`)
- 🗄️ **Accès aux données** via `laminas-db`
- 🧩 **Architecture modulaire** (module `Application`, extensible avec de nouveaux modules métier)
- 🐳 **Environnement Docker** prêt à l'emploi
- ✅ **Outils qualité** : PHPUnit, Psalm, PHP_CodeSniffer

> ✏️ **À compléter :** listez les modules fonctionnels réellement implémentés (clients, fournisseurs, produits, stock, facturation, tableau de bord…).

---

## Stack technique

| Couche | Technologie |
|---|---|
| Langage | PHP 8.1 / 8.2 / 8.3 |
| Framework | Laminas MVC `^3.7` |
| Base de données | `laminas/laminas-db` `^2.22` |
| Sessions | `laminas/laminas-session` `^2.27` |
| Conteneurisation | Docker, Docker Compose |
| Tests | PHPUnit `^10.4`, `laminas-test` |
| Analyse statique | Psalm `^5.13` |
| Style de code | PHP_CodeSniffer |

---

## Architecture du projet

```
erp-laminas/
├── bin/                    # Scripts utilitaires (cache, etc.)
├── config/                 # Configuration de l'application
│   └── autoload/           # Configuration locale (non versionnée)
├── data/cache/             # Cache applicatif
├── module/
│   └── Application/        # Module principal
│       ├── config/         # Routes, services, vues
│       ├── src/            # Contrôleurs, modèles…
│       ├── view/           # Templates
│       └── test/           # Tests unitaires
├── public/                 # Point d'entrée web (index.php) et assets
├── Dockerfile
├── docker-compose.yml
├── composer.json
├── phpcs.xml               # Règles de style
├── phpunit.xml.dist        # Configuration des tests
└── psalm.xml               # Analyse statique
```

> ✏️ Adaptez l'arborescence de `module/Application` à votre structure réelle (contrôleurs, formulaires, tables…).

---

## Prérequis

- **PHP** 8.1, 8.2 ou 8.3, avec l'extension de votre SGBD (ex. `pdo_mysql`)
- **[Composer](https://getcomposer.org/)**
- Un serveur de base de données (> ✏️ à préciser : MySQL, MariaDB, PostgreSQL…)
- **Docker** et **Docker Compose** *(optionnel)*

---

## Installation

```bash
# 1. Cloner le dépôt (branche master)
git clone https://github.com/kammounimen/erp-laminas.git
cd erp-laminas

# 2. Installer les dépendances
composer install

# 3. Activer le mode développement
composer development-enable
```

---

## Configuration

Créez votre configuration locale dans `config/autoload/` (ces fichiers ne doivent pas être versionnés) :

```php
<?php
// config/autoload/local.php
return [
    'db' => [
        'driver'   => 'Pdo_Mysql',
        'database' => 'erp_client_db',
        'hostname' => 'localhost',
        'username' => 'root',
        'password' => 'votre_mot_de_passe',
        'charset'  => 'utf8mb4',
    ],
];
```

> ✏️ Adaptez le nom de la base et le driver à votre environnement, et documentez ici le script SQL d'initialisation si vous en avez un.

⚠️ Ne commitez jamais de mots de passe ou de clés secrètes.

Après toute modification de la configuration :

```bash
composer clear-config-cache
```

---

## Lancement

```bash
composer serve
# équivalent à : php -S 0.0.0.0:8080 -t public
```

Ouvrez ensuite **http://localhost:8080**.

> Le serveur intégré de PHP est réservé au développement.

### Commandes Composer utiles

| Commande | Description |
|---|---|
| `composer serve` | Lance le serveur de développement |
| `composer development-enable` | Active le mode développement |
| `composer development-disable` | Désactive le mode développement |
| `composer development-status` | Affiche l'état du mode développement |
| `composer clear-config-cache` | Vide le cache de configuration |
| `composer test` | Lance les tests PHPUnit |
| `composer static-analysis` | Lance Psalm |
| `composer cs-check` / `cs-fix` | Vérifie / corrige le style de code |

---

## Docker

```bash
docker-compose up -d --build
```

L'application est alors disponible sur **http://localhost:8080**.

Exécuter une commande dans le conteneur :

```bash
docker-compose run laminas composer install
```

Pour ajouter une extension PHP (ex. `pdo_pgsql`), décommentez les lignes correspondantes dans le `Dockerfile`, puis relancez `docker-compose up -d --build`.

---

## Tests et qualité du code

```bash
composer test              # tests unitaires (PHPUnit)
composer static-analysis   # analyse statique (Psalm)
composer cs-check          # vérification du style
composer cs-fix            # correction automatique du style
```

Pour adapter la configuration de PHPUnit, copiez `phpunit.xml.dist` en `phpunit.xml` (ignoré par Git).

---

## Déploiement

### Apache

```apache
<VirtualHost *:80>
    ServerName erp.example.com
    DocumentRoot /chemin/vers/erp-laminas/public
    <Directory /chemin/vers/erp-laminas/public>
        DirectoryIndex index.php
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Nginx

```nginx
server {
    listen       80;
    server_name  erp.example.com;
    root         /chemin/vers/erp-laminas/public;

    location / {
        index index.php;
        try_files $uri $uri/ @php;
    }

    location @php {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_param  SCRIPT_FILENAME /chemin/vers/erp-laminas/public/index.php;
        include fastcgi_params;
    }
}
```

En production : désactivez le mode développement (`composer development-disable`), servez l'application derrière **HTTPS** avec Apache ou Nginx + PHP-FPM.

---

## Contribuer

1. Forkez le projet
2. Créez une branche : `git checkout -b feature/ma-fonctionnalite`
3. Committez : `git commit -m "Ajout de ma fonctionnalité"`
4. Poussez : `git push origin feature/ma-fonctionnalite`
5. Ouvrez une Pull Request

Avant toute PR : `composer cs-check`, `composer static-analysis` et `composer test`.

---

## Licence

Distribué sous licence **BSD-3-Clause**. Voir [LICENSE.md](LICENSE.md) et [COPYRIGHT.md](COPYRIGHT.md).

---

## Auteur

**kammounimen** — [github.com/kammounimen](https://github.com/kammounimen)
