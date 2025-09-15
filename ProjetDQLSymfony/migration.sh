#!/bin/bash

# # permet de commenter du code dans le terminal
# Supprimer les versions antérieurs de migration pour éviter les erreurs
# (Commande du système d'exploitation et non de symfony)
echo "yes" | rm -rf migrations
mkdir -p migrations

# Pour créer la base de donnée cf env. pour le nom de la BD
# Supprime l'ancienne BD
symfony console doctrine:database:drop --force --no-interaction

# Crée nouvelle BD
symfony console doctrine:database:create --no-interaction

# Migration
symfony console make:migration --no-interaction
symfony console doctrine:migration:migrate --no-interaction

# supprime et recrée les données dans la DB
symfony console doctrine:fixtures:load --no-interaction

# ajoute données dans la DB après donc risque doublons
# symfony console doctrine:fixtures:load --append
