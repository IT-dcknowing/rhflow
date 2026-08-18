-- =====================================================================
-- Reprise : rattacher les comptes utilisateurs a leur entreprise
--
-- Contexte : SettingsController::storeUser() creait le User sans
-- company_id (seule la fiche Employee le recevait). Ces comptes se
-- connectent normalement mais toutes leurs pages restent vides, car
-- l'application filtre ses donnees sur auth()->user()->company_id.
--
-- Le code est corrige : les nouveaux comptes recoivent leur company_id.
-- Ce script repare uniquement les comptes deja crees.
--
-- Perimetre : les comptes dont users.company_id est NULL ET qui ont une
-- fiche employe portant un company_id. On ne touche jamais a un
-- rattachement deja renseigne.
--
-- NE CONCERNE PAS :
--   - les comptes type 'company' : leur lien passe par companies.user_id
--   - le compte super_admin : il n'appartient a aucune entreprise
--
-- A executer sur la base de PRODUCTION. Faire une sauvegarde avant.
-- =====================================================================


-- ---------------------------------------------------------------------
-- ETAPE 1 — Apercu : lister les comptes qui seront modifies.
-- A lancer seul d'abord, et verifier que le resultat correspond bien
-- a ce que vous attendez avant de passer a l'etape 2.
-- ---------------------------------------------------------------------
SELECT
    u.id            AS user_id,
    u.name          AS nom,
    u.email,
    u.type,
    u.company_id    AS company_id_actuel,   -- doit etre NULL
    e.company_id    AS company_id_a_poser,
    c.name          AS entreprise
FROM users u
INNER JOIN employees e ON e.user_id = u.id
LEFT  JOIN companies c ON c.id = e.company_id
WHERE u.company_id IS NULL
  AND e.company_id IS NOT NULL
ORDER BY e.company_id, u.id;


-- ---------------------------------------------------------------------
-- ETAPE 2 — Controle de securite : verifier qu'aucun compte n'a
-- plusieurs fiches employe. Le resultat DOIT etre vide.
-- Si des lignes remontent, ARRETEZ-VOUS : l'UPDATE choisirait une
-- entreprise au hasard parmi les fiches. Traitez ces cas a la main.
-- ---------------------------------------------------------------------
SELECT user_id, COUNT(*) AS nb_fiches
FROM employees
WHERE user_id IS NOT NULL
GROUP BY user_id
HAVING COUNT(*) > 1;


-- ---------------------------------------------------------------------
-- ETAPE 3 — La reprise.
-- Encadree par une transaction : rien n'est ecrit tant que le COMMIT
-- n'est pas lance. Si le nombre de lignes affectees ne correspond pas
-- a l'apercu de l'etape 1, lancez ROLLBACK au lieu de COMMIT.
-- ---------------------------------------------------------------------
START TRANSACTION;

UPDATE users u
INNER JOIN employees e ON e.user_id = u.id
SET u.company_id = e.company_id
WHERE u.company_id IS NULL
  AND e.company_id IS NOT NULL;

-- Verifiez ici le nombre de lignes affectees, puis :
--   COMMIT;     pour valider
--   ROLLBACK;   pour tout annuler

COMMIT;


-- ---------------------------------------------------------------------
-- ETAPE 4 — Verification apres reprise.
-- La premiere requete doit renvoyer 0.
-- La seconde ne doit lister que des comptes 'company' et 'super_admin',
-- qui n'ont legitimement pas de company_id.
-- ---------------------------------------------------------------------
SELECT COUNT(*) AS comptes_restant_a_rattacher
FROM users u
INNER JOIN employees e ON e.user_id = u.id
WHERE u.company_id IS NULL
  AND e.company_id IS NOT NULL;

SELECT type, COUNT(*) AS nb
FROM users
WHERE company_id IS NULL
GROUP BY type;
