# ARCHITECTURE ET FONCTIONNEMENT DU SYSTÈME DE PAIE RH FLOW

Ce document présente une vue d'ensemble du fonctionnement technique et opérationnel du système de paie intégré à la plateforme RH Flow.

---

## 1. ARCHITECTURE MODULAIRE
Le système de paie de RH Flow n'est pas un bloc isolé, mais le résultat de l'interaction de plusieurs modules spécialisés :

*   **Module Employees** : Gère le "Core" (Salaire de base, catégorie professionnelle, situation matrimoniale et nombre de parts).
*   **Module PaieSalaries** : Cerveau du système. Il gère les périodes de paie, les retenues et la génération finale des bulletins.
*   **Module Time & Attendance** : Alimente la paie en données variables (Heures supplémentaires, absences, jours travaillés).
*   **Module Loans (Prêts)** : Gère les échéanciers et déduit automatiquement les mensualités lors du calcul du net.
*   **Module NatureAvantage** : Traite les avantages en nature (Logement, Véhicule) et leur impact fiscal/social.

---

## 2. LE CYCLE DE CALCUL (ENGINE)
Le moteur de calcul (`SalaryService`) suit une logique séquentielle rigoureuse pour garantir l'exactitude :

1.  **Établissement du Brut** : 
    `Salaire de Base + Primes + Indemnités + Avantages en Nature = Salaire Brut Total`
2.  **Calcul de l'Assiette Fiscale** : 
    Application des exonérations légales (ex: transport < 30k) et abattements forfaitaires (10%) pour obtenir le **Brut Imposable**.
3.  **Détermination du Social** :
    Application du taux CNPS (6,3%) sur le salaire social plafonné à 3 375 000 FCFA.
4.  **Calcul de l'Impôt (IRS)** :
    Application du barème progressif (0% à 32%) sur le brut imposable, puis application de la réduction pour charges de famille (10% à 45%).
5.  **Détermination du Net à Payer** :
    `Brut Total - Retenues (Impôts + CNPS + CMU + Prêts + Acomptes) = Net à Payer`

---

## 3. WORKFLOW OPÉRATIONNEL (GUIDE UTILISATEUR)
RH Flow propose un parcours guidé en 3 étapes clés pour sécuriser la paie :

### Étape 1 : Préparation & Variables (Mode Cockpit)
L'utilisateur saisit les éléments variables du mois (absences, primes exceptionnelles) via une interface centralisée appelée "Cockpit", évitant ainsi de naviguer employé par employé.

### Étape 2 : Calcul de la Période
L'utilisateur lance le traitement global. Le système vérifie la cohérence des contrats et génère les résultats pour l'ensemble des effectifs en quelques secondes.

### Étape 3 : Contrôle & Clôture
*   Génération du **Livre de Paie** (Vue d'ensemble).
*   Génération des **Bulletins de Paie** individuels.
*   Validation finale qui "verrouille" la période pour éviter toute modification ultérieure.

---

## 4. SÉCURITÉ ET CONFORMITÉ
*   **Historisation** : Chaque bulletin est stocké de manière immuable. Même si le salaire d'un employé change le mois suivant, le bulletin passé reste intact.
*   **Mises à jour Légales** : Comme démontré lors de l'audit du 04 Mai 2026, le moteur de calcul est mis à jour pour refléter les réformes fiscales nationales (IRS 2024 en Côte d'Ivoire).

---

## 5. CAPACITÉS DE REPORTING
Le système génère automatiquement les états suivants :
- **Bulletins de Paie** (PDF formaté).
- **Livre de Paie Mensuel** (Tableau de bord financier).
- **États de virements** (Fichiers pour transmission bancaire).
- **Déclarations sociales & fiscales** (Préparation des données pour la CNPS et la DGI).

---
*Document conçu pour la documentation technique de RH Flow - Mai 2026*
