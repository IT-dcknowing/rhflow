# Architecture Multi-Modèles — RH Flow (Côte d'Ivoire)

Cette architecture permet de basculer instantanément entre **trois modèles de vue** pour la gestion de la paie RH Flow en Côte d'Ivoire, tout en conservant strictement :
1. **La même logique de calcul** (Côte d'Ivoire 2026, barème ITS 2024, CNPS 6,3% plafonnée, forfait CMU, réduction pour charges de famille RICF).
2. **Le même jeu de données partagé** (47 salariés dont les 12 salariés de référence avec leurs primes, jours travaillés et anomalies).
3. **La même charte graphique** (Bleu Navy `#253e87` / `#0a1f44`).

---

## 🎨 Les 3 Modèles Implémentés

### Modèle 1 : « La Vue Hybride — Masse & Chirurgie »
- **Principe** : Grille tabulaire consolidée de l'ensemble des salariés avec détection visuelle des anomalies.
- **Tiroir latéral (50% largeur)** : Clic sur une ligne pour ouvrir la vue chirurgicale avec :
  - Section 1 : Contrôle de la présence et du prorata 30 jours calendaires.
  - Section 2 : Primes, éléments de paie, catalogue déroulant d'accessoires.
  - Section 3 : Simulation du bulletin et module de rétro-calcul cible net.
- **Actions en masse** : Checkboxes multi-lignes pour appliquer des primes ou modifier les jours en lot.
- **Mode Expert** : Affichage des colonnes détaillées cotisations et impôts.

### Modèle 2 : « Le Bulletin Vivant »
- **Principe** : L'éditeur est le bulletin de paie lui-même.
- **Disposition Split-view** :
  - **Panneau latéral gauche (320px)** : Liste compacte façon boîte mail, triée avec anomalies en priorité, recherche instantanée et affichage du net.
  - **Zone centrale** : Bulletin interactif où chaque ligne (base, primes, retenues, jours) est directement éditable avec icône ✎.
  - **Actions intégrées** : Catalogue d'insertion d'éléments, rétro-calcul cible net avec modale dédiée, visualisation de l'échéancier prêt véhicule 🔗.
  - **Bascule Vue Masse** : Bouton pour afficher la vue globale en tableau plein écran avec filtres et totaux.
  - **Navigation clavier** : Raccourcis Flèches Haut (↑) et Bas (↓) pour naviguer entre les salariés.

### Modèle 3 : « Le Tunnel — Paie en 5 Étapes »
- **Principe** : Workflow guidé pas à pas pour sécuriser la clôture mensuelle.
- **Stepper horizontal sticky** :
  1. **Étape 1 — Présence** : Contrôle des écarts pointeuse (< 30j), saisie des motifs/justificatifs RH et liste des salariés conformes.
  2. **Étape 2 — Variables** : Traitement ciblé des primes et prêts, report des variables stables du mois précédent en 1 clic.
  3. **Étape 3 — Calcul** : Animation de calcul par le moteur CI et grand tableau de masse salariale consolidé.
  4. **Étape 4 — Contrôle** : Analyse des variations nettes suspectes et anomalies avec boutons *Accepter* et *Examiner*.
  5. **Étape 5 — Validation** : Récapitulatif masse brute / masse nette, checklist d'audit et verrouillage officiel.

---

## 📁 Organisation du Code

```
src/
├── data/                       # Données & contrats partagés
│   ├── employeeTypes.ts        # Interfaces TypeScript (Employee, ComputedEmployeePay, Period)
│   └── employees.ts            # Données réelles initiales (47 salariés dont les 12 de référence)
│
├── logic/                      # Moteur de calcul officiel Côte d'Ivoire
│   ├── itsBracket.ts           # Barème progressif ITS 2024 (tranches 0% à 32%)
│   ├── cnpsRates.ts            # Taux et plafonds CNPS & forfait CMU
│   ├── ricf.ts                 # Réduction d'Impôt pour Charges de Famille (parts fiscales)
│   └── payrollCalculator.ts    # Calculateur unifié (brut, cotisations, net, rétro-calcul)
│
├── styles/                     # Charte graphique partagée
│   └── navy-theme.css          # Variables CSS et utilitaires bleu navy
│
├── models/                     # Modèles d'affichage
│   ├── model1/                 # Modèle 1 : Vue Hybride
│   │   ├── Model1View.tsx
│   │   ├── Model1EmployeeRow.tsx
│   │   └── Model1Drawer.tsx
│   ├── model2/                 # Modèle 2 : Bulletin Vivant
│   │   └── Model2View.tsx
│   └── model3/                 # Modèle 3 : Le Tunnel
│       └── Model3View.tsx
│
└── components/
    ├── ModelSelector.tsx       # Sélecteur [ Modèle 1 ] [ Modèle 2 ] [ Modèle 3 ] [ + ]
    ├── PayslipModal.tsx        # Bulletin de paie officiel imprimable
    └── ValidationAuditModal.tsx # Modal d'audit et de clôture de période
```
