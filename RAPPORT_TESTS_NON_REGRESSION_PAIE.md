# RAPPORT DE TESTS DE NON-RÉGRESSION — MOTEUR DE PAIE
**Périmètre : proratisation, prime d'ancienneté, écritures de la paie du mois**
**Exécuté le 24 septembre 2026 · branche `update/designPaie` · commit de référence `408dfa1`**

---

## 1. OBJET

Ce rapport rend compte des tests écrits et exécutés pour couvrir les modifications
récentes du moteur de paie :

- la proratisation du salaire de base et des primes selon les jours travaillés,
  en particulier le cas de **29 jours** signalé en recette ;
- l'ouverture de la **prime d'ancienneté à deux ans** et son application automatique ;
- les points d'entrée d'écriture ajoutés à l'écran « Paie du mois » : traitement
  en masse et enregistrement du détail d'un salarié.

Les tests ne remplacent pas une recette fonctionnelle. Ils figent le comportement
attendu du calcul afin qu'une modification ultérieure ne le change pas sans qu'on
le voie.

---

## 2. DISPOSITIF

| Élément | Valeur |
| :--- | :--- |
| Fichier de tests | `tests/Feature/PaieProratisationTest.php` |
| Configuration | `phpunit.paie.xml` |
| Commande | `php artisan test -c phpunit.paie.xml --filter=PaieProratisationTest` |
| Base | base de développement MySQL (`rhss`) |
| Isolation | une transaction par test, annulée en `tearDown()` |

### Pourquoi une configuration séparée

La configuration par défaut (`phpunit.xml`) force `DB_CONNECTION=sqlite` et
`DB_DATABASE=:memory:`. Le schéma du projet ne s'y installe pas, et plusieurs
requêtes de l'application utilisent du SQL propre à MySQL — `FIELD()` sur la
page d'accueil, colonnes `enum` sur les périodes. Les tests de paie doivent donc
tourner sur la vraie base.

`phpunit.paie.xml` est une copie de `phpunit.xml` **sans** ces deux lignes de
forçage. Aucun autre réglage ne change.

### Isolation des écritures

Ces tests écrivent réellement : ils modifient des salaires, des jours travaillés,
des primes, et suppriment une rubrique pour vérifier qu'elle se recrée. Chaque
test ouvre une transaction sur la connexion MySQL en `setUp()` et l'annule en
`tearDown()`.

La transaction est ouverte **à la main** et non par le trait
`DatabaseTransactions` : ce dernier s'exécute avant `setUp()` et tentait d'ouvrir
une transaction sur MySQL même lorsque la suite tourne sur SQLite, ce qui faisait
échouer les tests au lieu de les ignorer.

Un quatorzième test vérifie le dispositif lui-même : il échoue si la transaction
n'est plus ouverte, c'est-à-dire si les autres tests se mettaient à écrire
durablement.

---

## 3. RÉSULTATS

### 3.1 Suite de paie

```
Tests: 14 passed (44 assertions)
Duration: 10.75s
```

| # | Test | Ce qu'il garantit |
| :-- | :--- | :--- |
| 1 | `les_jours_travailles_ne_sont_plus_ramenes_a_30_a_29_jours` | 29 jours saisis restent 29 |
| 2 | `un_mois_de_28_ou_31_jours_calendaires_vaut_un_mois_complet` | février et mois longs = mois plein |
| 3 | `les_jours_sont_bornes_a_30` | aucune saisie ne dépasse 30 jours de paie |
| 4 | `le_salaire_de_base_est_proratise_a_29_jours` | base = salaire × 29/30 |
| 5 | `la_prime_danciennete_souvre_a_deux_ans_et_pas_avant` | barème Art. 55 CCI |
| 6 | `la_prime_danciennete_est_posee_et_proratisee` | moitié du droit à 15 jours |
| 7 | `la_rubrique_danciennete_est_creee_si_elle_manque` | code 104 créé, exo 0 % / Soumis |
| 8 | `le_traitement_en_masse_refuse_plus_de_30_jours` | réponse 422 |
| 9 | `le_traitement_en_masse_ignore_les_salaries_dune_autre_entreprise` | cloisonnement |
| 10 | `le_traitement_en_masse_applique_les_jours_a_toute_la_selection` | application groupée |
| 11 | `une_periode_verrouillee_refuse_toute_ecriture` | bulletins générés = figés |
| 12 | `la_recherche_de_salaries_est_cloisonnee_par_entreprise` | cloisonnement, plafond 20 |
| 13 | `enregistrer_un_salarie_ecrit_jours_base_et_primes` | montant plein + prorata |
| 14 | `aucune_ecriture_ne_survit_a_la_transaction` | garde-fou du dispositif |

### 3.2 Barème d'ancienneté vérifié (test 5)

| Ancienneté à la fin de période | Taux attendu | Résultat |
| :--- | :--- | :--- |
| 23 mois | 0 % | conforme |
| 24 mois | 2 % | conforme |
| 35 mois | 2 % | conforme |
| 36 mois | 3 % | conforme |
| 60 mois | 5 % | conforme |
| 600 mois | 25 % (plafond) | conforme |

Le seuil est bien **24 mois révolus**, et non 25 comme auparavant. Le 25ᵉ mois
n'est plus une zone morte.

### 3.3 Suite par défaut — comparaison avant / après

| | Avant | Après |
| :--- | :--- | :--- |
| `php artisan test` | 1 échec, 1 succès | 1 échec, 1 succès, **14 ignorés** |

**Aucune régression introduite.** L'échec est antérieur à ces travaux :
`tests/Feature/ExampleTest.php` appelle `/` sur un SQLite dépourvu du schéma, et
la requête des plans utilise `FIELD()`, non supporté par SQLite. Les 14 tests de
paie s'ignorent d'eux-mêmes hors MySQL plutôt que de teindre la suite en rouge.

---

## 4. DÉFAUT DÉCOUVERT PAR LES TESTS

Le test 3 a échoué à sa première exécution.

`Employee::get_jours_work()` ne bornait pas la valeur haute : **45 jours saisis
renvoyaient 45**, ce qui donnait une base à 150 % du salaire au lieu d'un prorata.
L'interface borne la saisie à 30, mais rien ne protégeait le modèle d'une écriture
directe, d'un import ou d'un appel d'API.

Correction appliquée : borne à 30 dans `get_jours_work()`, sur les deux chemins —
la prime de période et le repli sur `tax_payer_id`.

**Portée sur les données existantes : nulle.** Vérification en base au moment du
correctif :

```
allowances.jours_work  > 30 : 0 ligne
employees.tax_payer_id > 30 : 0 ligne
```

C'est un garde-fou, pas une reprise de données.

---

## 5. INTÉGRITÉ DE LA BASE APRÈS EXÉCUTION

Contrôles effectués après la dernière série de tests :

| Indicateur | Attendu | Constaté |
| :--- | :--- | :--- |
| Lignes `allowances` actives | 2 407 | 2 407 |
| Rubriques `allowance_options` | 290 | 290 |
| Rubriques code 104 | 14 | 14 |
| Salariés à 120 000 (valeur de test) | 0 | 0 |
| Transactions restées ouvertes | 0 | 0 |

Trois pages ont été rappelées après coup pour confirmer que l'application répond
toujours : paie du mois, packs d'abonnement et ruptures — **HTTP 200** sur les trois.

---

## 6. CE QUI N'EST PAS COUVERT

Ces limites sont à connaître avant de s'appuyer sur ce rapport.

- **Aucun test de navigateur.** Le rendu HTML et les points d'entrée serveur sont
  vérifiés ; l'ouverture du tiroir, les clics et la saisie au clavier ne le sont pas.
- **Les périodes verrouillées ne sont testées qu'en refus d'écriture.** La lecture
  des bulletins figés dans la grille n'a pas de test dédié.
- **Le cycle de paiement n'est pas couvert** : génération des bulletins, validation
  du paiement, clôture de période.
- **Les tests s'appuient sur les données de développement.** Ils se déclarent
  ignorés si aucune période ouverte ou aucun salarié actif n'est disponible, plutôt
  que d'échouer. Sur une base vide, ils ne prouvent rien.
- **Le barème ITS, les cotisations CNPS et CMU** ne sont pas couverts ici. Ils
  relèvent du `RAPPORT_CONFORMITE_PAIE_2024.md`.

---

## 7. POINTS OUVERTS SANS RAPPORT AVEC CES TESTS

Relevés au cours des travaux, non corrigés, sans test associé :

1. **`Employee::get_patronale()`** exclut les codes 307 et 308. La colonne
   `pay_slips.total_patronale` est donc sous-évaluée de la retraite et de la CMU
   employeur, et alimente le tableau de bord, l'API mobile et le chatbot.
2. **Le gestionnaire d'exceptions** renvoie toute erreur de validation JSON en 500
   avec la clé de traduction brute, faute de `getStatusCode()` sur
   `ValidationException`.
3. **`genererBulletins()` ne recalcule jamais un bulletin existant.** Une période
   dont les bulletins sont générés ne peut plus être retraitée.
4. **Les 207 bulletins déjà émis** conservent leur taux FPC d'origine après
   l'alignement à 1,2 %. Aucune reprise n'a été lancée.
