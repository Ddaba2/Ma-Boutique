-- ============================================
-- Script de création de la base de données FasoDocs
-- ============================================

-- Créer la base de données si elle n'existe pas
CREATE DATABASE IF NOT EXISTS fasodocs_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

-- Utiliser la base de données
USE fasodocs_db;

-- Message de confirmation
SELECT 'Base de données fasodocs_db créée avec succès!' AS Message;

-- Afficher les bases de données disponibles
SHOW DATABASES;
