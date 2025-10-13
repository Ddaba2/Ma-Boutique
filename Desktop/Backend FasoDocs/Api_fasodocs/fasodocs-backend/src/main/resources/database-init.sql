-- Script SQL pour initialiser la base de données FasoDocs
-- Date: 13 Octobre 2025

-- Création de la base de données
CREATE DATABASE IF NOT EXISTS fasodocs_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE fasodocs_db;

-- Note: Les tables seront créées automatiquement par Hibernate
-- Ce script contient des données d'exemple à insérer après le premier démarrage

-- ============================================
-- DONNÉES D'EXEMPLE (À exécuter après le premier démarrage)
-- ============================================

-- Insertion des catégories
INSERT INTO categories (titre, description, icone_url, date_creation) VALUES
('État Civil', 'Procédures liées à l\'état civil (naissance, mariage, décès, etc.)', 'https://example.com/icons/etat-civil.png', NOW()),
('Documents d\'Identité', 'Cartes d\'identité, passeports, permis de conduire', 'https://example.com/icons/identite.png', NOW()),
('Éducation', 'Inscriptions scolaires, bourses, équivalences de diplômes', 'https://example.com/icons/education.png', NOW()),
('Santé', 'Carte CMSS, certificats médicaux, vaccinations', 'https://example.com/icons/sante.png', NOW()),
('Emploi', 'Recherche d\'emploi, contrats de travail, retraite', 'https://example.com/icons/emploi.png', NOW()),
('Justice', 'Casier judiciaire, légalisations, attestations', 'https://example.com/icons/justice.png', NOW()),
('Foncier', 'Titres fonciers, permis de construire, lotissements', 'https://example.com/icons/foncier.png', NOW()),
('Entreprises', 'Création d\'entreprise, patentes, licences commerciales', 'https://example.com/icons/entreprises.png', NOW());

-- Insertion de procédures d'exemple
INSERT INTO procedures (nom, titre, url_vers_formulaire, cout, delai, description, date_creation, date_modification, categorie_id) VALUES
('CARTE_NINA', 'Carte d\'Identité Nationale NINA', 'https://anpe.gov.ml/formulaires/nina', 5000, '15 jours', 'Demande de carte d\'identité nationale biométrique NINA', NOW(), NOW(), 2),
('EXTRAIT_NAISSANCE', 'Extrait d\'acte de naissance', NULL, 1000, '3 jours', 'Obtention d\'un extrait d\'acte de naissance', NOW(), NOW(), 1),
('PASSEPORT', 'Passeport biométrique', 'https://diplomatie.gov.ml/passeport', 75000, '30 jours', 'Demande de passeport biométrique malien', NOW(), NOW(), 2),
('CARTE_CMSS', 'Carte d\'Assurance Maladie CMSS', NULL, 2500, '7 jours', 'Obtention de la carte CMSS pour l\'assurance maladie', NOW(), NOW(), 4),
('PERMIS_CONDUIRE', 'Permis de conduire', 'https://transports.gov.ml/permis', 25000, '45 jours', 'Demande de permis de conduire (catégories A, B, C, D)', NOW(), NOW(), 2),
('CASIER_JUDICIAIRE', 'Bulletin de casier judiciaire', NULL, 500, '2 jours', 'Demande de bulletin de casier judiciaire', NOW(), NOW(), 6),
('CERTIFICAT_MARIAGE', 'Certificat de mariage', NULL, 5000, '5 jours', 'Obtention d\'un certificat de mariage', NOW(), NOW(), 1);

-- Insertion des étapes pour la procédure CARTE_NINA
INSERT INTO procedure_etapes (procedure_id, etape) VALUES
(1, '1. Se rendre au centre d\'enrôlement le plus proche'),
(1, '2. Présenter les documents requis (acte de naissance, certificat de résidence)'),
(1, '3. Effectuer l\'enrôlement biométrique (photo, empreintes digitales)'),
(1, '4. Payer les frais d\'établissement (5000 FCFA)'),
(1, '5. Retirer la carte après 15 jours au centre d\'enrôlement');

-- Insertion des documents requis pour CARTE_NINA
INSERT INTO documents_requis (description, est_obligatoire, modele_url, procedure_id) VALUES
('Acte de naissance ou jugement supplétif', TRUE, NULL, 1),
('Certificat de résidence ou attestation d\'hébergement', TRUE, NULL, 1),
('Deux photos d\'identité récentes', TRUE, NULL, 1),
('Ancien document d\'identité (si renouvellement)', FALSE, NULL, 1);

-- Insertion des documents requis pour EXTRAIT_NAISSANCE
INSERT INTO documents_requis (description, est_obligatoire, modele_url, procedure_id) VALUES
('Demande manuscrite adressée au maire', TRUE, 'https://example.com/formulaires/demande-extrait.pdf', 2),
('Pièce d\'identité du demandeur ou du parent', TRUE, NULL, 2),
('Frais d\'établissement (1000 FCFA)', TRUE, NULL, 2);

-- Insertion des lieux de traitement
INSERT INTO lieux_traitement (nom, adresse, horaires, coordonnees_gps, telephone, email) VALUES
('Mairie de Bamako - Commune I', 'Avenue Cheick Zayed, Bamako', 'Lundi-Vendredi: 8h-15h', '12.6392,-8.0029', '+223 20 22 45 67', 'mairie.commune1@bamako.ml'),
('Mairie de Bamako - Commune II', 'Hippodrome, Bamako', 'Lundi-Vendredi: 8h-15h', '12.6500,-8.0100', '+223 20 29 34 56', 'mairie.commune2@bamako.ml'),
('Direction Nationale de la Population', 'Quartier du Fleuve, Bamako', 'Lundi-Vendredi: 8h-16h', '12.6456,-8.0012', '+223 20 22 78 90', 'dnpop@gov.ml'),
('Centre d\'Enrôlement NINA - Bamako Centre', 'ACI 2000, Bamako', 'Lundi-Samedi: 8h-17h', '12.6550,-7.9900', '+223 76 12 34 56', 'nina.bamako@anpe.ml'),
('Tribunal de Première Instance', 'Cité Administrative, Bamako', 'Lundi-Vendredi: 8h-15h', '12.6423,-8.0056', '+223 20 22 89 01', 'tribunal.bamako@justice.ml');

-- Association procédures-lieux
INSERT INTO procedure_lieux (procedure_id, lieu_id) VALUES
(1, 4), -- CARTE_NINA -> Centre d'Enrôlement
(2, 1), -- EXTRAIT_NAISSANCE -> Mairie Commune I
(2, 2), -- EXTRAIT_NAISSANCE -> Mairie Commune II
(3, 3), -- PASSEPORT -> Direction Nationale de la Population
(6, 5), -- CASIER_JUDICIAIRE -> Tribunal
(7, 1), -- CERTIFICAT_MARIAGE -> Mairie Commune I
(7, 2); -- CERTIFICAT_MARIAGE -> Mairie Commune II

-- Insertion des traductions en Bambara (exemples)
INSERT INTO traductions (langue, contenu_traduit, audio_url, date_creation, procedure_id) VALUES
('bm', 'NINA karti ye jateminɛ karti ye min bɛ kɛ biometrique fɛ. A bɛ fanga di mɔgɔ ma ko a ye Mali jamana den ye.', 'https://example.com/audio/nina-bm.mp3', NOW(), 1),
('bm', 'Denmisenw cogoya ye sɛbɛn ye min bɛ di mɔgɔ ma ko a bangebagaw be Mali.', 'https://example.com/audio/extrait-bm.mp3', NOW(), 2);

-- Insertion de paramètres système
INSERT INTO parametres (contenu, valeur, description, date_creation, date_modification) VALUES
('VERSION_APP', '1.0.0', 'Version actuelle de l\'application', NOW(), NOW()),
('EMAIL_SUPPORT', 'support@fasodocs.ml', 'Email de support technique', NOW(), NOW()),
('TELEPHONE_SUPPORT', '+223 20 XX XX XX', 'Téléphone du support', NOW(), NOW()),
('LANGUES_DISPONIBLES', 'fr,bm', 'Langues disponibles dans l\'application', NOW(), NOW());

-- ============================================
-- NOTES IMPORTANTES
-- ============================================
-- 1. Les rôles (ROLE_CITOYEN, ROLE_ADMIN) sont créés automatiquement au démarrage
-- 2. Créez un compte admin via l'API puis modifiez son rôle en base :
--    UPDATE citoyen_roles SET role_id = 2 WHERE citoyen_id = [ID_ADMIN];
-- 3. Configurez Firebase pour les notifications push
-- 4. Ajoutez plus de procédures selon les besoins du Mali

-- ============================================
-- REQUÊTES UTILES
-- ============================================

-- Compter le nombre de procédures par catégorie
-- SELECT c.titre, COUNT(p.id) as nombre_procedures 
-- FROM categories c 
-- LEFT JOIN procedures p ON c.id = p.categorie_id 
-- GROUP BY c.id;

-- Voir les procédures avec leurs documents requis
-- SELECT p.nom, dr.description, dr.est_obligatoire 
-- FROM procedures p 
-- JOIN documents_requis dr ON p.id = dr.procedure_id;

-- Voir les notifications non lues d'un citoyen
-- SELECT * FROM notifications 
-- WHERE citoyen_id = [ID] AND est_lue = FALSE 
-- ORDER BY date_envoi DESC;
