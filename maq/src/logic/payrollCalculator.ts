import { Employee, ComputedEmployeePay } from '../data/employeeTypes';
import { calculateRawITS } from './itsBracket';
import { calculateSocialContributions } from './cnpsRates';
import { determineFiscalParts, calculateRICF } from './ricf';

/**
 * Moteur de calcul officiel pour la Côte d'Ivoire (Réforme 2024-2026)
 * - Prorata sur 30 jours calendaires
 * - Prime de transport exonérée jusqu'à 30 000 FCFA
 * - Assiette CNPS plafonnée à 3 375 000 FCFA pour la retraite
 * - CMU : forfait mensuel non proratisé (500 FCFA salarial, 500 FCFA patronal)
 * - Barème ITS unifié (0%, 16%, 21%, 24%, 28%, 32%)
 * - Réduction pour charges de famille (RICF) : 5 500 FCFA par demi-part au-delà de 1 part
 * - Charges patronales : Retraite 7.7%, PF 5.75% (plaf 70k), AT 3% (plaf 70k), Maternité 0.75% (plaf 70k), CE 2.8%, FDFP 1.28% effectif
 */
export function computeEmployeePay(emp: Employee): ComputedEmployeePay {
  const days = Math.max(0, Math.min(30, emp.days));
  const proratedBase = Math.round((emp.base * days) / 30);

  let totalTransportOriginal = 0;
  let otherGains = 0;
  let totalRetenues = 0;

  for (const el of emp.elements) {
    if (el.gain) {
      if (el.type === 'transport') {
        totalTransportOriginal += el.amount;
      } else {
        otherGains += el.amount;
      }
    } else {
      totalRetenues += el.amount;
    }
  }

  // Prorata transport sur 30 jours
  const proratedTransport = days < 30 ? Math.round((totalTransportOriginal * days) / 30) : totalTransportOriginal;
  const totalGains = proratedTransport + otherGains;

  // 1. Salaire Brut
  const brut = proratedBase + totalGains;

  // 2. Cotisations sociales (CNPS et CMU salariales + patronales)
  const social = calculateSocialContributions(brut, proratedTransport);

  // 3. Base ITS = Assiette CNPS (Côte d'Ivoire CGI 2024)
  const baseITS = social.assietteCNPS;
  const rawITS = calculateRawITS(baseITS);

  // 4. Parts fiscales & RICF
  const parts = determineFiscalParts(emp.maritalStatus, emp.dependents, emp.parts);
  const ricf = calculateRICF(parts);
  const itsNet = Math.max(0, rawITS - ricf);

  // 5. Net à payer
  const net = brut - social.cnpsSalariale - social.cmu - itsNet - totalRetenues;

  // 6. Coût total employeur
  const coutTotalEmployeur = brut + social.totalChargesPatronales;

  return {
    proratedBase,
    proratedTransport,
    otherGains,
    totalGains,
    totalRetenues,
    brut,
    transportExempt: social.transportExempt,
    assietteCNPS: social.assietteCNPS,
    cnpsSalariale: social.cnpsSalariale,
    cmu: social.cmu,
    baseITS,
    rawITS,
    ricf,
    itsNet,
    totalCotis: social.totalCotisSalariales,
    net,
    cnpsRetraitePatronale: social.cnpsRetraitePatronale,
    cnpsPFPatronale: social.cnpsPFPatronale,
    cnpsATPatronale: social.cnpsATPatronale,
    cnpsMatPatronale: social.cnpsMatPatronale,
    cmuPatronale: social.cmuPatronale,
    cePatronale: social.cePatronale,
    fdfpPatronale: social.fdfpPatronale,
    totalChargesPatronales: social.totalChargesPatronales,
    coutTotalEmployeur,
  };
}

/**
 * Rétro-calcul : Détermine le sursalaire ou gain additionnel requis pour atteindre
 * exactement un Net Cible pour un salarié donné.
 */
export function reverseCalculateNetToSurSalary(
  emp: Employee,
  targetNet: number
): number {
  if (targetNet <= 0) return 0;

  // Binary search sur le gain nécessaire
  let low = 0;
  let high = Math.max(10000000, targetNet * 3);
  let bestSurSalary = 0;
  let bestDiff = Infinity;

  // Clone pour tester sans sursalaire préexistant
  const testEmp: Employee = {
    ...emp,
    elements: emp.elements.filter((e) => e.type !== 'sursalaire'),
  };

  for (let iter = 0; iter < 45; iter++) {
    const mid = Math.round((low + high) / 2);
    const candidateElements = [
      ...testEmp.elements,
      {
        id: 'sursalaire-retro',
        type: 'sursalaire',
        label: "Sursalaire d'ajustement",
        amount: mid,
        gain: true,
        imposable: true,
        social: true,
      },
    ];

    const res = computeEmployeePay({ ...testEmp, elements: candidateElements });
    const diff = res.net - targetNet;

    if (Math.abs(diff) < bestDiff) {
      bestDiff = Math.abs(diff);
      bestSurSalary = mid;
    }

    if (diff === 0) {
      break;
    } else if (diff < 0) {
      low = mid + 1;
    } else {
      high = mid - 1;
    }
  }

  return bestSurSalary;
}
