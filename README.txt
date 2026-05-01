================================================================================
                    ENERGY CALCULATOR - TP SIA 2026 M1-IL
                    Calcul de déperditions thermiques d'un bâtiment
================================================================================

AUTEURS:
--------
DOUHA DOUAA
GALLEZ NADA

DATE: Mai 2026
DÉMONSTRATION: 4 Mai 2026

================================================================================
LOGICIELS ET VERSIONS UTILISÉS:
================================================================================

ENVIRONNEMENT DE DÉVELOPPEMENT ET EXÉCUTION:
--------------------------------------------
- Système d'exploitation: Windows 10/11
- Serveur Web: WAMP Server 3.2.7 
  * Apache HTTP Server 2.4.x
  * PHP 8.0.x (ou supérieur)
  * MySQL 8.0.x (ou supérieur)
  
NAVIGATEUR WEB:
---------------
- Microsoft Edge (Recommandé)
- Compatible aussi avec: Google Chrome, Firefox, Safari

BIBLIOTHÈQUES EXTERNES (CDN - Aucune installation requise):
-----------------------------------------------------------
- Chart.js 3.x (Graphiques interactifs)
- Font Awesome 6.0.0 (Icônes)

================================================================================
PROCÉDURE D'INSTALLATION PAS À PAS:
================================================================================

ÉTAPE 1: Installation de WAMP Server
-------------------------------------
1. Téléchargez WAMP Server depuis: https://www.wampserver.com/
2. Lancez l'installateur et suivez les instructions par défaut.
3. Vérifiez que l'icône WAMP dans la barre des tâches est VERTE 🟢.

ÉTAPE 2: Déploiement des fichiers
----------------------------------
1. Extrayez l'archive NOM_PRENOM.zip
2. Copiez le dossier "tp_batiment" dans le répertoire www de WAMP:
   C:\wamp64\www\tp_batiment\

ÉTAPE 3: Configuration de la Base de Données (IMPORTANT)
---------------------------------------------------------
1. Ouvrez le fichier: config.php
2. Vérifiez les lignes suivantes (par défaut pour WAMP/XAMPP) :

   define('DB_HOST', 'localhost');     // Hôte MySQL
   define('DB_USER', 'root');          // Utilisateur MySQL
   define('DB_PASS', '');              // Mot de passe (VIDE par défaut sous WAMP)
   define('DB_NAME', 'tp_batiment');   // Nom de la base

   ⚠️ Si vous avez mis un mot de passe root dans MySQL, modifiez DB_PASS.

3. LANCEZ L'INSTALLATION AUTOMATIQUE :
   - Ouvrez votre navigateur (Edge/Chrome)
   - Allez à l'adresse : http://localhost/tp_batiment/install.php
   - Le script va créer la base de données, les tables et insérer les données de démo.
   - Attendez le message "✅ Installation terminée avec succès !".

ÉTAPE 4: Connexion à l'application
-----------------------------------
1. Allez à : http://localhost/tp_batiment/login.php
2. Utilisez l'un des comptes suivants créés automatiquement :

   👑 COMPTE ADMINISTRATEUR:
   Identifiant: admin
   Mot de passe: admin123
   
   👤 COMPTE UTILISATEUR STANDARD:
   Identifiant: user
   Mot de passe: user123

================================================================================
ACCÈS AUX RÉSULTATS DES CALCULS:
================================================================================

1. FORMULAIRE DE CALCUL (index.php)
------------------------------------
- Saisissez le nom du bâtiment et sa surface.
- Choisissez les matériaux (Mur, Plancher, Ouvrant, Toiture) via les listes déroulantes.
- Entrez les surfaces correspondantes.
- Cliquez sur "Calculer".

2. PAGE DE RÉSULTATS (result.php)
----------------------------------
- Affiche la Classe Énergétique (A à G) avec code couleur.
- Affiche la consommation en kWh/m²/an.
- Montre une barre de progression visuelle.

3. HISTORIQUE (historique.php)
------------------------------
- Tableau de tous vos calculs précédents.
- Graphique comparatif des consommations.
- Possibilité de rechercher ou supprimer une entrée.

================================================================================
COPIES D'ÉCRAN DE L'APPLICATION:
================================================================================

[INSÉREZ VOS CAPTURES D'ÉCRAN ICI]
*Astuce : Faites des captures d'écran de :
1. La page de Login
2. Le formulaire de calcul rempli
3. La page de résultat (ex: Classe B verte)
4. La page historique avec le graphique
Collez-les directement ci-dessous ou dans un dossier "screenshots" joint.*

================================================================================
FORMULES DE CALCUL UTILISÉES:
================================================================================

Déperdition Totale (W/°C) = Σ (Surface × Coefficient K)
Consommation (kWh/m²/an) = (Déperdition × DJU × 24h × 0.8) / (Rendement × Surface)

Paramètres physiques utilisés :
- DJU (Degrés-Jours Unifiés) : 2000
- Rendement Chauffage : 90%
- Apports Gratuits : 20%

Classification (kWh/m²/an) :
A (<70), B (71-110), C (111-180), D (181-250), E (251-330), F (331-420), G (>420)

================================================================================
FIN DU DOCUMENT
================================================================================