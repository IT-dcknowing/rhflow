# RH FLOW — Documentation technique et fonctionnelle

**Application :** RHFLOW — Gestion des ressources humaines et de la paie
**Marché cible :** Côte d'Ivoire / Afrique de l'Ouest (barèmes ITS, CNPS, CMU ivoiriens)
**Type :** Application web SaaS multi-entreprises
**Date du document :** 3 août 2026

---

## Table des matières

1. [Présentation générale](#1-présentation-générale)
2. [Architecture technique](#2-architecture-technique)
3. [Modèle de données](#3-modèle-de-données)
4. [Sécurité, rôles et permissions](#4-sécurité-rôles-et-permissions)
5. [Modules fonctionnels](#5-modules-fonctionnels)
6. [Le moteur de paie](#6-le-moteur-de-paie)
7. [Modèle SaaS : plans, modules, abonnements](#7-modèle-saas--plans-modules-abonnements)
8. [Traitements asynchrones](#8-traitements-asynchrones)
9. [Intégrations externes](#9-intégrations-externes)
10. [Déploiement et exploitation](#10-déploiement-et-exploitation)
11. [État des lieux qualité et dette technique](#11-état-des-lieux-qualité-et-dette-technique)
12. [Annexes](#12-annexes)

---

## 1. Présentation générale

### 1.1 Objet

RH FLOW est une plateforme SaaS de gestion des ressources humaines et de la paie, conçue pour la réglementation ivoirienne. Elle couvre le cycle complet :

- gestion administrative du personnel (dossiers, contrats, documents) ;
- suivi du temps (pointage, absences, heures supplémentaires, congés) ;
- calcul et édition de la paie (bulletins, livre de paie) ;
- déclarations sociales et fiscales (CNPS, CMU, ITS, DISA) ;
- gestion des prêts, avantages en nature, ruptures de contrat ;
- vie de l'entreprise (annonces, réunions, événements, promotions).

### 1.2 Caractéristiques structurantes

| Caractéristique | Valeur |
|---|---|
| Architecture | Multi-tenant par `company_id`, modulaire |
| Langue | Français (`APP_LOCALE=fr`) |
| Devise | XOF — FCFA |
| Fuseau horaire | Africa/Abidjan |
| Nombre de modules métier | 20 |
| Nombre de tables | 209 |
| Nombre de modèles Eloquent | 156 (dont ~120 grilles salariales sectorielles) |

### 1.3 Périmètre réglementaire couvert

- **ITS** (Impôt sur Traitements et Salaires) — barème progressif à 6 tranches, versions mensuelle et journalière ;
- **RICF** — Réduction pour Impôt Charges de Famille, selon le nombre de parts ;
- **CNPS** — retraite (part salariale 6,3 %, part patronale 7,7 %), prestations familiales, accidents du travail à taux variable ;
- **CMU** — Couverture Maladie Universelle, 500 FCFA par personne couverte avec plafonnement ;
- **Plafonds d'exonération** — transport localisé (Abidjan 30 000, Bouaké 24 000, autres 20 000 — Art. 116 al.1 CGI), frais de restauration (30 000) ;
- **Grilles salariales conventionnelles** par secteur d'activité et catégorie professionnelle.

---

## 2. Architecture technique

### 2.1 Pile logicielle

| Couche | Technologie | Version |
|---|---|---|
| Langage | PHP | ^8.2 |
| Framework | Laravel | 12.43.1 |
| Modularisation | nwidart/laravel-modules | ^12.0 |
| Permissions | spatie/laravel-permission | — |
| Génération PDF | barryvdh/laravel-dompdf | ^3.1 |
| Export Excel | maatwebsite/excel | ^3.1.55 |
| Base de données | MySQL | — |
| Build front | Vite | ^7.0.7 |
| CSS | Bootstrap 5.3.8 + Tailwind 4 | — |
| Aide contextuelle | driver.js | ^1.4.0 |

### 2.2 Organisation du code

```
rhflow/
├── app/
│   ├── Models/              156 modèles (dont ~120 grilles sectorielles)
│   ├── Services/            ImpotsService, SalaryService, ModuleService,
│   │                        NotificationService, TimeTrackingService
│   ├── Jobs/                GenerateBulletinsJob
│   ├── Http/
│   │   ├── Controllers/     CompanyController, ApiAppController, …
│   │   └── Middleware/      SuperAdmin, MaintenanceMode, VerifyHubToken
│   ├── Helpers/             modules.php
│   └── Support/             helpers.php
├── Modules/                 20 modules métier (voir §5)
├── routes/
│   ├── web.php              point d'entrée, agrège les autres
│   ├── auth.php             authentification
│   ├── user.php             espace utilisateur
│   ├── super-admin.php      back-office éditeur
│   ├── api.php              API
│   └── console.php          commandes
├── database/
│   ├── migrations/          30 migrations socle
│   └── seeders/             10 seeders
└── resources/views/layouts/ app, auth, doc, error, super-admin
```

### 2.3 Structure d'un module

Chaque module suit la convention `nwidart/laravel-modules` :

```
Modules/<Nom>/
├── Http/Controllers/
├── Models/
├── Providers/
├── database/migrations/
├── resources/views/
├── routes/
│   ├── web.php
│   └── api.php
└── module.json
```

L'activation des modules est pilotée par `modules_statuses.json` (niveau technique) **et** par la table `pack_modules` (niveau commercial, voir §7).

### 2.4 Multi-tenant

L'isolation des données repose sur la colonne `company_id`, présente sur toutes les tables métier. Le filtrage est appliqué **explicitement dans chaque requête** :

```php
->where("company_id", Auth::user()->company_id)
```

> **Point d'attention :** il n'existe pas de *global scope* Eloquent centralisant cette règle. L'isolation dépend donc de la rigueur de chaque requête. Voir §11.

### 2.5 Middleware

| Alias | Classe | Rôle |
|---|---|---|
| `super.admin` | `App\Http\Middleware\SuperAdmin` | Réserve l'accès au back-office éditeur |
| `maintenance` | `App\Http\Middleware\MaintenanceMode` | Bascule l'application en mode maintenance |
| `verify.hub.token` | `App\Http\Middleware\VerifyHubToken` | Vérifie un jeton d'appel externe |

La quasi-totalité des routes métier est protégée par `['auth', 'maintenance']` et préfixée par `company`.

### 2.6 Gestion des erreurs

`bootstrap/app.php` personnalise le rendu des exceptions :

- requêtes JSON → réponse JSON normalisée `{message, status, code}` ;
- requêtes web → vues `errors.404` et `errors.500` dédiées, **y compris en mode debug** ;
- les exceptions d'authentification et de validation sont explicitement exclues pour préserver leurs redirections natives ;
- les erreurs de code ≥ 500 sont journalisées en niveau `critical`.

### 2.7 Configuration d'infrastructure

| Paramètre | Valeur |
|---|---|
| `QUEUE_CONNECTION` | `database` |
| `CACHE_STORE` | `database` |
| `SESSION_DRIVER` | `file` |
| `SESSION_LIFETIME` | 120 minutes |
| `FILESYSTEM_DISK` | `local` |
| `MAIL_MAILER` | SMTP (`mail.dc-knowing.com`, port 465, SSL) |
| `LOG_CHANNEL` | `stack` → `single` |

---

## 3. Modèle de données

### 3.1 Domaine « Entreprise et référentiel »

| Table | Contenu |
|---|---|
| `companies` | Entreprise cliente : identité, abonnement, plafonds, paramètres de pointage, signature et cachet électroniques, taux accident du travail, préfixe matricule |
| `branches` | Succursales (portent la ville, utilisée pour le plafond transport) |
| `departments`, `designations` | Départements et postes |
| `sectors` | Secteurs d'activité (déterminent la grille salariale) |
| `job_categories` | Catégories professionnelles |
| `countries`, `marital_statuses` | Référentiels |
| `work_locations`, `payment_types` | Lieux de travail, moyens de paiement |
| `settings`, `company_documents` | Paramétrage et documents d'entreprise |

### 3.2 Domaine « Personnel »

| Table | Contenu |
|---|---|
| `employees` | Fiche salarié — voir détail ci-dessous |
| `familys` | Composition familiale |
| `employee_documents` | Pièces justificatives |
| `employee_cmus` | Personnes couvertes par la CMU |
| `demandes` | Demandes du salarié |
| `contracts`, `contract_types`, `contract_avenants`, `contract_attechments`, `contract_comments` | Gestion contractuelle |

**Colonnes structurantes de `employees` :**

- *Identité* : `name`, `dob`, `gender`, `nationality`, `martalstatu_id`, `enfant`, `parts`, `cmu`
- *Rattachement* : `company_id`, `branch_id`, `department_id`, `designation_id`, `secteur_id`, `categorie`, `sous_categorie`, `statut_emp`
- *Contrat* : `start_date`, `end_date`, `company_doj`, `contrat`, `is_active`
- *Rémunération* : `salary` (mensuel contractuel), `salary_horaire`, `salary_type`, `branch_location` (salaire catégoriel proratisé), **`tax_payer_id` (nombre de jours travaillés)**
- *Social/fiscal* : `num_cnps`, `num_secu_soc`, `charge_its`, `charge_cnps`, `charge_cmu`, `charge_expat`
- *Paiement* : `paytype`, coordonnées bancaires, `orange_money`, `mtn_money`, `moov_money`, `wave_money`

> **Piège de nommage :** la colonne `tax_payer_id` ne contient **pas** un identifiant fiscal mais le **nombre de jours travaillés** dans le mois. Ce nom trompeur est une source d'erreurs récurrente.

### 3.3 Domaine « Paie »

| Table | Contenu |
|---|---|
| `paie_exercices` | Exercice de paie (année) |
| `paie_periodes` | Période de paie : dates de début/fin, date de paiement, statut, type |
| `allowances` | Gains : `montant` (base 100 %), `amount` (proratisé), `jours_work`, `trait_fisc`, `trait_cnps`, `type_amount`, `code` |
| `allowance_options` | Modes de calcul des gains |
| `retenues`, `type_retenues` | Retenues : `base`, `taux`, `amount`, `patronale`, `salariale`, `ordre`, `code` |
| `pay_slips` | **Bulletin figé** — instantané de la paie (voir ci-dessous) |
| `set_salaries` | Paramétrage salarial |
| `avantages` | Avantages en nature (`amount` barème, `amount_reel`) |

**`pay_slips` — table d'instantané.** Une fois le bulletin généré, les montants sont **figés** : `basic_salary`, `salary_brut`, `net_imposable`, `net_sociale`, `net_payble`, `total_retenue`, `total_patronale`, `nbre_jour`, ainsi que les gains et retenues sérialisés (`allowances`, `retenues`) et les données d'identité recopiées (`nom_etp`, `num_cnps_emp`, `categories_emp`, `parts_emp`, …).

> **Conséquence majeure :** les écrans de déclarations et les PDF de bulletins lisent `pay_slips` et **ne recalculent rien**. Toute correction du moteur de calcul est sans effet sur les bulletins déjà générés. Voir §6.6.

### 3.4 Domaine « Temps et absences »

| Table | Contenu |
|---|---|
| `time_sheets` | Pointages |
| `overtimes` | Heures supplémentaires |
| `leaves`, `leave_types` | Congés |
| `pointeuses` | Terminaux de pointage |

### 3.5 Domaine « Prêts »

`loans`, `loan_types`, `loan_options`, `loan_payments` — les échéances alimentent les retenues de paie (code 500).

### 3.6 Domaine « Ruptures »

`ruptures`, `rupture_types` — calcul des droits de fin de contrat, décompte final, certificat de travail.

### 3.7 Domaine « Vie d'entreprise »

`announcements`, `announcement_employees`, `meetings`, `meeting_employees`, `events`, `event_types`, `event_employees`, `event_participants`, `awards`, `award_types`, `promotions`, `transfers`.

### 3.8 Domaine « SaaS »

`plans`, `modules`, `pack_modules`, `orders`, `coupons`, `users`, `teams`, `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`, `notifications`, `contacts`, `temp_tokens`.

### 3.9 Grilles salariales sectorielles

Environ **120 tables** `secteur_*` encodent les minima conventionnels par secteur et catégorie : bâtiment, commerce, banque, assurances, industrie (agroalimentaire, bois, textile, sucre, thon, polyvalent), pétrole (production et distribution), transport (routier, aérien, fonds), pêche (côtière, large), maritime, hôtellerie, tourisme, agriculture, élevage, forestier, dockers, nettoyage, sécurité, maison, santé.

Chaque table suit le schéma `secteur_<secteur>_<catégorie>` (ex. `secteur_commerce_cadres`, `secteur_peches_larges_matlots`).

> **Observation :** ce découpage en une table et un modèle par couple secteur/catégorie génère une forte redondance structurelle. Une table unique `grilles_salariales` avec des colonnes discriminantes couvrirait le même besoin. Voir §11.

---

## 4. Sécurité, rôles et permissions

### 4.1 Rôles

Six rôles définis dans `database/seeders/PermissionSeeder.php` :

| Rôle | Portée |
|---|---|
| `super-admin` | Back-office éditeur — toutes permissions |
| `company` | Administrateur de l'entreprise cliente |
| `hr` | Gestion RH : dossiers, pointage, validation des congés, recrutement |
| `paie` | Gestion de la paie et des impôts |
| `fiscal` | Déclarations fiscales et sociales |
| `employee` | Espace salarié : profil, pointage, demande de congé, bulletins |

### 4.2 Permissions

Nomenclature `<domaine>.<objet>.<action>` :

- `super.admin.*` — accès, tableau de bord, entreprises, packs, utilisateurs, rapports, paramètres, support
- `company.*` — accès, tableau de bord, employés, pointage, congés, paie, rapports, paramètres
- `hr.*` — accès, tableau de bord, consultation/édition employés, pointage, validation congés, recrutement, rapports
- `fiscal.*` — accès, calculs, déclarations, rapports, spécificités Afrique de l'Ouest
- `employee.*` — accès, tableau de bord, profil, pointage, demande de congé, consultation des bulletins

> **Anomalie relevée :** le rôle `paie` reçoit les permissions `paie.access`, `paie.dashboard`, `paie.salaries.manage`, `paie.payroll.generate`, `paie.taxes.manage`, `paie.reports.generate` — **or aucune de ces permissions n'est créée** dans le seeder. L'exécution de `PermissionSeeder` lèvera une exception `PermissionDoesNotExist` à cette étape. Voir §11.

### 4.3 Protection applicative

- authentification Laravel standard (`routes/auth.php`), `BCRYPT_ROUNDS=12` ;
- protection CSRF active, jeton exposé via `<meta name="csrf-token">` dans tous les layouts ;
- back-office isolé derrière le middleware `super.admin` ;
- mode maintenance contrôlable par middleware.

---

## 5. Modules fonctionnels

Volumétrie mesurée par module :

| Module | Contrôleurs | Modèles | Migrations | Vues | Routes |
|---|---:|---:|---:|---:|---:|
| PaieSalaries | 1 | 4 | 3 | 46 | 75 |
| Settings | 2 | 3 | 4 | 21 | 74 |
| Declarations | 2 | 0 | 0 | 17 | 40 |
| Employees | 3 | 5 | 5 | 23 | 39 |
| Time | 4 | 3 | 2 | 10 | 30 |
| Evenements | 9 | 10 | 6 | 26 | 28 |
| Contracts | 1 | 4 | 5 | 9 | 24 |
| Ruptures | 1 | 2 | 2 | 11 | 23 |
| Loans | 2 | 3 | 3 | 6 | 18 |
| Leaves | 1 | 2 | 1 | 7 | 16 |
| LandingPage | 2 | 0 | 0 | 8 | 13 |
| User | 2 | 1 | 0 | 1 | 10 |
| Impot | 1 | 0 | 0 | 1 | 9 |
| Simulator | 1 | 0 | 0 | 1 | 9 |
| Pointeuses | 1 | 1 | 1 | 6 | 8 |
| NatureAvantage | 1 | 1 | 1 | 5 | 4 |
| Chatbot | 1 | 0 | 0 | 1 | 0 |

### 5.1 Employees — Dossier du personnel

Gestion complète de la fiche salarié : identité, situation familiale, affectation, rémunération, coordonnées bancaires et mobile money, documents, CMU, composition familiale, demandes.

Fonctions notables : import Excel en masse (`EmployeesImport`), génération de matricule à partir du préfixe entreprise, aperçu et téléchargement des bulletins du salarié.

### 5.2 Contracts — Gestion contractuelle

Types de contrat, contrats, **avenants**, pièces jointes, commentaires. Statuts de validation (`accept`). Génération et téléchargement de documents.

Le contrat conditionne l'inclusion du salarié dans la génération des bulletins : seuls les salariés dont un contrat couvre la période sont retenus.

### 5.3 PaieSalaries — Cœur du système

Le module le plus important (75 routes). Détaillé au §6.

Sous-ensembles :

- **Exercices et périodes** — création, duplication d'éléments d'une période à l'autre, recopie des primes, génération des bulletins, validation du paiement ;
- **Gains (`allowance`)** — création, options de calcul, valeurs par défaut ;
- **Retenues** — application individuelle ou en masse, activation/désactivation, remboursements ;
- **Calcul des paies** (`paiesalaries/calcule`) — écran principal, saisie des jours travaillés, aperçu du bulletin ;
- **Livre de paie** — états mensuels, annuels, individuels.

### 5.4 Declarations — Déclarations sociales et fiscales

- **Livre de paie** : mensuel, annuel, individuel ;
- **Déclarations** : mensuelle, annuelle ;
- **Cotisations** : états CNPS, CMU ;
- **Formats de télédéclaration** : `get_decla_efi`, `get_decla_edi`, `get_decla_cnps`, `get_decla_cmu`, `get_disa` (DISA) ;
- **Génération PDF en masse** des bulletins via file d'attente (voir §8).

### 5.5 Time — Suivi du temps

- **Absences** : saisie, pièces justificatives, circuit de validation ;
- **Heures supplémentaires** : saisie, marquage payé/non payé, export ;
- **Pointage par QR code** : scan, entrée/sortie, portail de pointage autonome, rapports, export.

Service dédié : `App\Services\TimeTrackingService`.

### 5.6 Pointeuses — Terminaux physiques

Déclaration et gestion des pointeuses. Un répertoire `pointeuse_json/` à la racine reçoit les échanges de données.

### 5.7 Leaves — Congés

Types de congés, demandes, calendrier, attestations, calcul des droits. Interaction avec la paie via le champ `jours_leave` des gains.

### 5.8 Loans — Prêts au personnel

Types et options de prêt, échéanciers, paiements, prêts actifs, rapports. Service `LoanCalculatorService`. Les échéances alimentent la retenue de code 500.

### 5.9 NatureAvantage — Avantages en nature

Double valorisation : `amount` (barème fiscal) et `amount_reel` (valeur réelle). Les deux entrent différemment dans le brut, l'assiette imposable et le net.

### 5.10 Ruptures — Fin de contrat

Types de rupture, calcul des droits (`calculerDroitsRupture`), décompte final et relevé, exports PDF et Word.

### 5.11 Evenements — Vie de l'entreprise

Le module le plus riche en contrôleurs (9) : annonces, réunions, événements, récompenses, promotions, mutations, avec gestion des participants.

### 5.12 Settings — Paramétrage

74 routes. Types de congés, types de prêts, lieux de travail, documents d'entreprise, référentiels, paramètres généraux.

### 5.13 Impot et Simulator

Modules de calcul et de simulation exposant chacun 9 routes, sans persistance propre.

### 5.14 Chatbot — Assistant conversationnel

Assistant IA capable d'agir sur les données via un jeu d'outils (`GlobalAgentTool`) : audit d'entreprise, recrutement, génération de bulletins, consultation des congés et événements.

| Composant | Rôle |
|---|---|
| `AnthropicService` | Fournisseur Claude (modèle par défaut `claude-3-5-sonnet-20241022`, surchargeable par `ANTHROPIC_MODEL`) |
| `GeminiService` | Fournisseur Google Gemini |
| `PayrollChatbot` | Orchestration conversationnelle |
| `GlobalAgentTool` | Outils exposés à l'IA, avec accès en écriture à la base |

Le fournisseur actif est choisi par `CHATBOT_PROVIDER`. Un mode démonstration existe (`CHATBOT_DEMO`).

> **Point de vigilance :** `GlobalAgentTool` crée des enregistrements de paie (`PaySlip`) et des employés. Toute évolution du moteur de paie doit être répercutée dans ce service, qui constitue un troisième point de génération des bulletins à côté du contrôleur et du job.

### 5.15 LandingPage — Site vitrine et souscription

Pages publiques, formulaire de contact, tunnel de souscription et paiement via `GeniusPayService`.

### 5.16 User — Espace utilisateur

Profil, préférences, gestion du compte.

---

## 6. Le moteur de paie

### 6.1 Cycle fonctionnel

```
1. Créer un EXERCICE (année)
        ↓
2. Créer une PÉRIODE (mois) — dates début/fin, date de paiement
        ↓
3. Alimenter les ÉLÉMENTS de la période
   • recopie des primes de la période précédente
   • gains (allowances) : salaire, sursalaire, primes, indemnités
   • retenues : prêts, autres retenues, remboursements
   • avantages en nature
        ↓
4. Saisir les JOURS TRAVAILLÉS par salarié (écran « Calcul des paies »)
   → déclenche la proratisation des gains et le recalcul des charges
        ↓
5. Contrôler via l'APERÇU du bulletin
        ↓
6. GÉNÉRER LES BULLETINS  → écriture figée dans pay_slips
        ↓
7. VALIDER LE PAIEMENT    → clôture de la période
        ↓
8. DÉCLARATIONS           → livre de paie, CNPS, CMU, ITS, DISA
```

### 6.2 Chaîne de calcul

Toutes les méthodes vivent dans `Modules/Employees/Models/Employee.php`.

| Méthode | Rôle |
|---|---|
| `get_jours_work($periodeId)` | **Source unique du nombre de jours.** Priorité à `allowance.jours_work` de la période, repli sur `tax_payer_id`. Normalise 28, 29 et 31 → 30 |
| `get_Salary_base($periodeId)` | Salaire catégoriel proratisé → alimente `pay_slips.basic_salary` |
| `get_brut_salary($periodeId)` | Brut = base proratisée + gains proratisés + avantages réels |
| `get_salary_imposable($periodeId)` | Assiette ITS : brut − exonérations − abattement 10 % + avantages au barème |
| `get_salary_social($periodeId)` | Assiette CNPS |
| `get_retenue($periodeId)` | Total des retenues salariales |
| `get_patronale($periodeId)` | Total des charges patronales |
| `get_net_salary($periodeId)` | Net = brut + remboursements − (retenues + avantages réels + cantine + autres + prêts) |

Le calcul et l'enregistrement des retenues fiscales et sociales sont réalisés par `PaieSalariesController::update()`, déclenché à la saisie des jours travaillés.

### 6.3 Règle de proratisation

Base fixe de **30 jours**, quel que soit le nombre de jours calendaires du mois :

```
montant proratisé = (montant mensuel ÷ 30) × jours travaillés
```

S'applique au salaire catégoriel et à tous les gains de type « montant mensuel » (`type_amount = 1`).

**Cas particulier :** 28, 29 et 31 jours sont normalisés à 30 par `get_jours_work()` et donc **payés comme un mois complet**. Comportement volontaire (mois de février, mois de 31 jours), mais qui implique qu'un salarié ayant 1 ou 2 jours d'absence est payé au mois plein.

### 6.4 Calcul de l'ITS

L'impôt **ne se proratise pas linéairement** : le barème est progressif. La méthode réglementaire est :

1. reconstituer le salaire imposable sur un mois complet ;
2. appliquer le barème mensuel ;
3. proratiser l'impôt au nombre de jours.

Le système obtient un résultat strictement équivalent par un **barème journalier** (tranches mensuelles divisées par 30) appliqué au salaire journalier, puis multiplié par les jours travaillés.

```
salaire journalier = salaire imposable proratisé ÷ jours travaillés
impôt              = barème_journalier(salaire journalier) × jours travaillés
```

Barèmes complets en annexe §12.1 et §12.2.

### 6.5 Composition du net

```
  Salaire brut
+ Remboursements                      (code 601)
− Impôts nets                         (code 403)
− Cotisation retraite CNPS salarié    (code 301)
− Couverture Maladie Universelle      (code 302)
− Avantages en nature (valeur réelle)
− Frais de restauration               (code 131)
− Autres retenues                     (code 501)
− Prêts                               (code 500)
= NET À PAYER
```

### 6.6 Trois points de génération des bulletins

| Point d'entrée | Fichier |
|---|---|
| Écran « Générer les bulletins » | `Modules/PaieSalaries/Http/Controllers/PaieSalariesController.php` |
| Traitement asynchrone | `app/Jobs/GenerateBulletinsJob.php` |
| Assistant IA | `Modules/Chatbot/Services/GlobalAgentTool.php` |

Les trois écrivent dans `pay_slips`. **Toute évolution du calcul doit être répercutée aux trois endroits.**

> **Règle d'exploitation critique :** la génération ne traite que les salariés **sans bulletin** pour la période :
> ```php
> $missing_payslips = $payslip_employee->diff($validatePaysilp);
> ```
> Relancer la génération sur une période qui possède déjà des bulletins **ne les réécrit pas**. Pour appliquer une correction, il faut **supprimer** les bulletins concernés puis les régénérer.

### 6.7 Correctifs appliqués le 3 août 2026

Une campagne de correction a porté sur la proratisation sur jours travaillés. Neuf défauts corrigés dans sept fichiers.

| N° | Défaut | Fichier |
|---|---|---|
| 1 | Colonne « Salaire de base » affichant le montant mensuel plein | `payslip/calcul.blade.php` |
| 2 | Champ « S. catégoriel y compris les jours travaillés » figé | `payslip/modals/days.blade.php` |
| 3 | `periode_id` absent de la requête AJAX → primes de toutes les périodes modifiées | `payslip/modals/days.blade.php` |
| 4 | Aperçu du bulletin : condition dont les deux branches étaient identiques | `payslip/modals/preview.blade.php` |
| 5 | Nombre de jours affiché lu sur `tax_payer_id` au lieu de `get_jours_work()` | `payslip/modals/preview.blade.php` |
| 6 | `get_Salary_base()` : condition dont les deux branches étaient identiques | `Employees/Models/Employee.php` |
| 7 | `nbre_jour` non renseigné à la génération par l'assistant IA | `Chatbot/Services/GlobalAgentTool.php` |
| 8 | Charges calculées **avant** l'enregistrement des nouveaux jours | `PaieSalariesController.php` |
| 9 | **Salaire journalier divisé par 30 au lieu des jours travaillés** | `PaieSalariesController.php` |

Le défaut n° 9 avait un impact financier direct : sur un cas réel à 15 jours, l'impôt calculé était de 130 663 FCFA au lieu de 288 880 FCFA, soit **158 217 FCFA non prélevés** pour un salarié sur un mois. Le net affiché était de 1 159 212 FCFA au lieu de 1 000 995 FCFA.

**Validation :** conformité vérifiée sur les 31 valeurs de jours travaillés (1 à 31), zéro écart avec la méthode de reconstitution mensuelle.

**Action requise :** les retenues et bulletins antérieurs restent calculés avec l'ancien code. Procédure : ressaisir les jours travaillés dans le modal, enregistrer, puis supprimer et régénérer le bulletin.

---

## 7. Modèle SaaS : plans, modules, abonnements

### 7.1 Plans commercialisés

| Plan | Prix (FCFA/mois) | Salariés maximum |
|---|---:|---:|
| Gratuit | 0 | 5 |
| Basic | 5 000 | 10 |
| Pro | 10 000 | 25 |
| Basic Edge | 20 000 | 50 |
| Pro Edge | 35 000 | 100 |
| Pro Max | 50 000 | 150 |
| Pro Master | 50 000 | 1 000 |
| Pro Day | 50 000 | 5 000 |

> **Observation commerciale :** trois plans (Pro Max, Pro Master, Pro Day) partagent le même prix de 50 000 FCFA pour des plafonds de 150, 1 000 et 5 000 salariés. À confirmer.

### 7.2 Modules commercialisables

Treize modules activables par plan via `pack_modules` :

`employee` · `contract` · `time` · `leave` · `declaration` · `event` · `rupture` · `impot` · `salary` · `loans` · `natureavantage` · `pointuse` · `user`

L'activation est vérifiée par `App\Services\ModuleService`, exposé aux vues par les helpers de `app/Helpers/modules.php` et `app/Support/helpers.php`.

### 7.3 Contrôle d'abonnement

La table `companies` porte `plan_id`, `subscription_status`, `subscription_start_date`, `subscription_end_date`, `max_employees`, `max_storage_gb`, `current_storage_used`.

La génération des bulletins vérifie le quota et refuse le traitement au-delà du plafond, avec invitation au réabonnement.

### 7.4 Back-office éditeur

`routes/super-admin.php` — gestion des entreprises clientes (création, suspension, activation, suppression, abonnement, renouvellement, historique de connexion, exports XLSX et PDF), des utilisateurs, des secteurs, des packs et modules.

---

## 8. Traitements asynchrones

`QUEUE_CONNECTION=database` — tables `jobs`, `job_batches`, `failed_jobs`.

### 8.1 `GenerateBulletinsJob`

Génération des bulletins d'une période en arrière-plan.

### 8.2 `GenerateBulkBulletinsPDF`

Génération d'un PDF consolidé de tous les bulletins d'une période.

Caractéristiques :

- **dispatché en mode synchrone** (`dispatchSync`) depuis `BulletinQueueController`, pour contourner les problèmes de worker — `set_time_limit(600)` et `memory_limit` porté à 512 Mo ;
- progression suivie en cache, interrogeable par `declarations/resume/progress` ;
- rendu via `declarations::pdf.bulk_bulletins_wrapper`, PDF produit par DomPDF (A4 portrait), stocké sur le disque public sous `bulletins/{filename}.pdf` ;
- trois tentatives, délai maximal d'une heure.

Le document `QUEUE_TEST.md` documente les procédures de test associées.

---

## 9. Intégrations externes

| Service | Usage | Configuration |
|---|---|---|
| **Anthropic (Claude)** | Assistant conversationnel | `ANTHROPIC_API_KEY`, `ANTHROPIC_MODEL`, `ANTHROPIC_BASE_URL` |
| **Google Gemini** | Assistant conversationnel (alternative) | `GEMINI_API_KEY` |
| **GeniusPay** | Encaissement des abonnements | `https://pay.genius.ci/api/v1/merchant` — création de paiement, vérification de signature de webhook |
| **Wave** | Paiement mobile | `WAVE_ENVIRONMENT` (sandbox / production) |
| **SMTP** | Notifications par courriel | `mail.dc-knowing.com:465` SSL |

Les moyens de paiement mobile ivoiriens (Orange Money, MTN, Moov, Wave) sont stockés au niveau du salarié pour le versement des salaires.

---

## 10. Déploiement et exploitation

### 10.1 Environnements

| Environnement | URL |
|---|---|
| Développement | `http://127.0.0.1:8000` |
| Production | `https://rhflow.dc-knowing.com` |

Chemin serveur de production : `/home/cp2255957p00/public_html/rhflow`.

### 10.2 Installation

```bash
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

### 10.3 Mise à jour applicative

```bash
php artisan optimize:clear     # purge config, cache, vues, routes, événements
```

À exécuter systématiquement après déploiement — les vues Blade compilées et le cache de configuration masquent sinon les modifications.

### 10.4 Traitement des files d'attente

```bash
php artisan queue:work
```

Note : la génération PDF en masse étant dispatchée en synchrone, elle ne dépend pas d'un worker actif.

### 10.5 Points de vigilance en exploitation

- **`APP_ENV=local` et `APP_DEBUG=true` sont positionnés alors que `APP_URL` pointe sur la production.** À corriger avant toute exposition publique : `APP_ENV=production`, `APP_DEBUG=false`.
- `SESSION_DRIVER=file` — en cas de répartition de charge multi-serveurs, basculer sur `database` ou `redis`.
- `SESSION_SECURE_COOKIE=false` alors que le site est servi en HTTPS — à passer à `true`.
- Les erreurs de type « CSRF token mismatch » proviennent généralement d'une session expirée (`SESSION_LIFETIME=120`), d'un `SESSION_DOMAIN` inadapté ou de droits d'écriture insuffisants sur `storage/framework/sessions`.

---

## 11. État des lieux qualité et dette technique

Constats factuels relevés lors de l'analyse, classés par criticité.

### 11.1 Critiques

| Constat | Détail |
|---|---|
| **Absence de tests automatisés** | `tests/` ne contient que `ExampleTest` (unitaire et fonctionnel). Aucun test ne couvre le moteur de paie, alors qu'il produit des montants légalement opposables |
| **`APP_DEBUG=true` avec URL de production** | Expose les traces d'exception et la configuration |
| **Trois implémentations parallèles de la génération de bulletins** | Contrôleur, job et service IA — toute divergence produit des bulletins incohérents selon le point d'entrée |
| **Bulletins figés non régénérables** | Une correction du calcul n'atteint jamais les bulletins existants sans suppression manuelle préalable |

### 11.2 Importantes

| Constat | Détail |
|---|---|
| **Seeder de permissions incohérent** | Le rôle `paie` reçoit six permissions jamais créées → exception à l'exécution |
| **`ImpotsService` inutilisé et porteur du même défaut** | `app/Services/ImpotsService.php` n'est référencé nulle part et contient la division par 30 corrigée ailleurs (`$base = $modeJournalier ? ($brut / 30) : $brut;`). S'il est un jour branché, il réintroduira le défaut n° 9 |
| **Isolation multi-tenant non centralisée** | Aucun global scope sur `company_id` ; chaque requête doit filtrer explicitement |
| **`PaieSalariesController` monolithique** | Environ 3 900 lignes, 75 routes sur un seul contrôleur |
| **Barèmes fiscaux codés en dur** | Tranches ITS, RICF et plafonds répétés en clair à plusieurs endroits (contrôleur, modèle, service). Un changement réglementaire impose une modification multi-fichiers |
| **Colonne `tax_payer_id` mal nommée** | Contient le nombre de jours travaillés, pas un identifiant fiscal |

### 11.3 Modérées

| Constat | Détail |
|---|---|
| **~120 tables de grilles sectorielles** | Une table et un modèle par couple secteur/catégorie ; une table unique paramétrée suffirait |
| **Plafonds mensuels non proratisés** | Transport exonéré (30 000 / 24 000 / 20 000), frais de restauration (30 000) et bases patronales (75 000 en dur) sont appliqués en entier sur un mois partiel — écart favorable au salarié |
| **Divergence sur statuts particuliers** | `get_brut_salary()` force le salaire de base à 0 pour les statuts Stagiaire, Apprenti et Prestataire lorsque les jours diffèrent de 30, alors que le bulletin affiche le montant proratisé |
| **Archives et scripts de test à la racine** | `app.zip`, `LandingPage.zip`, `Settings.zip`, `Simulator.zip`, `Time.zip`, `check_*.php`, `find_*.php`, `test_ai*.php`, `temp_show_complete.blade.php` |
| **Écart d'arrondi RICF** | Branche mensuelle 22 000 FCFA pour 3 parts contre 733 × 30 = 21 990 en journalier — 10 FCFA |
| **Fichiers de vues volumineux** | `preview.blade.php` ≈ 800 lignes, `employee_salary.blade.php` et `details_mois.blade.php` plusieurs milliers |

### 11.4 Recommandations prioritaires

1. **Couvrir le moteur de paie par des tests automatisés** — jeux de cas sur 1 à 31 jours, comparaison au barème de référence. C'est le meilleur retour sur investissement compte tenu de la nature légale des montants produits.
2. **Extraire le calcul de paie dans un service unique**, consommé par les trois points de génération.
3. **Centraliser les barèmes** dans un fichier de configuration daté et versionné, pour absorber les évolutions réglementaires sans modification de code.
4. **Ajouter une commande de recalcul** des bulletins d'une période, avec option de préservation des périodes validées.
5. **Corriger `APP_ENV` / `APP_DEBUG` / `SESSION_SECURE_COOKIE`** en production.
6. **Supprimer ou corriger `ImpotsService`** pour éviter la réintroduction du défaut.

---

## 12. Annexes

### 12.1 Barème ITS mensuel

| Tranche (FCFA) | Taux |
|---|---:|
| 0 – 75 000 | 0 % |
| 75 001 – 240 000 | 16 % |
| 240 001 – 800 000 | 21 % |
| 800 001 – 2 400 000 | 24 % |
| 2 400 001 – 8 000 000 | 28 % |
| au-delà de 8 000 000 | 32 % |

### 12.2 Barème ITS journalier

Tranches mensuelles divisées par 30.

| Tranche (FCFA) | Taux |
|---|---:|
| 0 – 2 500 | 0 % |
| 2 501 – 8 000 | 16 % |
| 8 001 – 26 667 | 21 % |
| 26 668 – 80 000 | 24 % |
| 80 001 – 266 667 | 28 % |
| au-delà de 266 667 | 32 % |

### 12.3 Réduction pour charges de famille (RICF)

| Parts | Mensuel | Journalier |
|---:|---:|---:|
| 1 | 0 | 0 |
| 1,5 | 5 500 | 183 |
| 2 | 11 000 | 367 |
| 2,5 | 16 500 | 550 |
| 3 | 22 000 | 733 |
| 3,5 | 27 500 | 917 |
| 4 | 33 000 | 1 100 |
| 4,5 | 38 500 | 1 283 |
| 5 | 44 000 | 1 467 |

### 12.4 Taux de cotisation

| Cotisation | Part salariale | Part patronale |
|---|---:|---:|
| Retraite CNPS | 6,3 % | 7,7 % |
| Prestations familiales | — | 5,75 % (base 75 000) |
| Accident du travail | — | 2 % à 5 % selon l'entreprise (`companies.accident_taux`) |
| CMU | 500 FCFA par personne, plafonné à 3 000 au-delà de 6 personnes | 3 000 FCFA |

### 12.5 Codes de rubriques

| Code | Libellé |
|---|---|
| 100 | Salaire de base |
| 101 | Sursalaire |
| 111 | Prime de transport |
| 120 | Prime de responsabilité |
| 126 | Rubrique de base complémentaire |
| 131 | Frais de restauration / cantine |
| 150 | Avantages en nature et en argent |
| 301 | Cotisation retraite CNPS (salarié) |
| 302 | Couverture Maladie Universelle |
| 305–308 | Cotisations complémentaires |
| 401 | Impôts bruts avant RICF |
| 402 | Réduction pour charges de famille |
| 403 | Impôts nets |
| 409–412 | Charges patronales |
| 500 | Prêt |
| 501 | Autre retenue |
| 601 | Remboursement |

### 12.6 Plafonds d'exonération

| Plafond | Valeur | Base légale |
|---|---:|---|
| Transport — Abidjan | 30 000 | Art. 116 al.1 CGI |
| Transport — Bouaké | 24 000 | Art. 116 al.1 CGI |
| Transport — autres localités | 20 000 | Art. 116 al.1 CGI |
| Frais de restauration | 30 000 | — |
| Abattement forfaitaire | 10 % du brut hors rubriques 111 et 126 | — |
| SMIG de référence | 75 000 | — |
| Horaire mensuel de référence | 173,33 heures | — |

### 12.7 Documents complémentaires du dépôt

| Fichier | Contenu |
|---|---|
| `README.md` | Présentation et installation |
| `GUIDE_UTILISATEUR_RH_FLOW.md` | Guide utilisateur |
| `ARCHITECTURE_ET_FONCTIONNEMENT_PAIE_RHFLOW.md` | Architecture du module de paie |
| `RAPPORT_CONFORMITE_PAIE_2024.md` | Rapport de conformité réglementaire |
| `CHATBOT_README.md` | Assistant conversationnel |
| `QUEUE_TEST.md` | Procédures de test des files d'attente |

### 12.8 Glossaire

| Terme | Signification |
|---|---|
| **ITS** | Impôt sur Traitements et Salaires |
| **IGR** | Impôt Général sur le Revenu |
| **RICF** | Réduction pour Impôt Charges de Famille |
| **CNPS** | Caisse Nationale de Prévoyance Sociale |
| **CMU** | Couverture Maladie Universelle |
| **DISA** | Déclaration Individuelle des Salaires Annuels |
| **SBI** | Salaire Brut Imposable — assiette de l'ITS |
| **SBS** | Salaire Brut Social — assiette CNPS |
| **Sursalaire** | Complément au salaire catégoriel conventionnel |
| **Salaire catégoriel** | Minimum conventionnel du secteur et de la catégorie |
| **Parts** | Quotient familial déterminant la RICF |
| **Exercice** | Année de paie |
| **Période** | Mois de paie |
| **Avenant** | Modification contractuelle |
| **Rupture** | Fin de contrat, tous motifs confondus |
