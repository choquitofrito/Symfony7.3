# Solution au problème de corruption des fichiers de XAMPP (MySql ne démarre pas)

- Renommer C:\xampp\mysql\data (C:\xampp\mysql\dataOld)
- Créer un nouveau dossier C:\xampp\mysql\data
- Copier le contenu de C:\xampp\mysql\backup dans C:\xampp\mysql\data
- Copier les dossiers C:\xampp\mysql\dataOld\phpmyadmin et C:\xampp\mysql\dataOld\mysql dans C:\xampp\mysql\data
- Démarrer le serveur