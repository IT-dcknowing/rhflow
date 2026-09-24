import { computeEmployeePay, reverseCalculateNetToSurSalary } from '../src/utils/rhflowCalculator';
import { Employee } from '../src/types/rhflow';

function assert(condition: boolean, message: string) {
  if (!condition) {
    console.error(`❌ ÉCHEC : ${message}`);
    process.exit(1);
  } else {
    console.log(`✅ SUCCÈS : ${message}`);
  }
}

function approx(val1: number, val2: number, delta = 2): boolean {
  return Math.abs(val1 - val2) <= delta;
}

console.log('================================================================');
console.log('🧪 VALIDATION OFFICIELLE DES 3 CAS TEST RH FLOW — CÔTE D\'IVOIRE');
console.log('================================================================\n');

// -------------------------------------------------------------
// CAS TEST 1 — Aka Koffi (sans anomalie)
// Brut: 535 000 F, Assiette CNPS: 505 000 F, CNPS: 31 815 F,
// CMU: 500 F, ITS: 82 050 F, Net: 420 635 F
// -------------------------------------------------------------
console.log('--- CAS TEST 1 : Aka Koffi (30j, Célibataire 1 part, Base 450k, Transport 30k, Perf 55k) ---');
const akaKoffi: Employee = {
  id: 1,
  matricule: '#RH-312',
  name: 'Aka Koffi',
  dept: 'Technique',
  days: 30,
  base: 450000,
  maritalStatus: 'single',
  dependents: 0,
  parts: 1,
  elements: [
    { id: '1', type: 'transport', label: 'Prime de transport', amount: 30000, gain: true, imposable: false, social: true },
    { id: '2', type: 'performance', label: 'Prime de performance', amount: 55000, gain: true, imposable: true, social: true },
  ],
  status: 'ok',
  alert: null,
};

const res1 = computeEmployeePay(akaKoffi);
assert(res1.brut === 535000, `Cas 1 Brut: attendu 535 000 F, obtenu ${res1.brut} F`);
assert(res1.assietteCNPS === 505000, `Cas 1 Assiette CNPS: attendu 505 000 F, obtenu ${res1.assietteCNPS} F`);
assert(res1.cnpsSalariale === 31815, `Cas 1 CNPS salariale: attendu 31 815 F, obtenu ${res1.cnpsSalariale} F`);
assert(res1.cmu === 500, `Cas 1 CMU: attendu 500 F, obtenu ${res1.cmu} F`);
assert(res1.itsNet === 82050, `Cas 1 ITS net: attendu 82 050 F, obtenu ${res1.itsNet} F`);
assert(res1.net === 420635, `Cas 1 Net à payer: attendu 420 635 F, obtenu ${res1.net} F`);

// -------------------------------------------------------------
// CAS TEST 2 — Kouamé Yao (avec prorata 22j)
// Base proratisée: 242 000 F, Transport proratisé: 22 000 F,
// Brut: 264 000 F, CNPS: 15 246 F, CMU: 500 F, ITS: 26 820 F, Net: 221 434 F
// -------------------------------------------------------------
console.log('\n--- CAS TEST 2 : Kouamé Yao (22j, Base 330k, Transport 30k proratisé, sans prêt pour validation formule brute) ---');
const kouameYaoValidation: Employee = {
  id: 3,
  matricule: '#RH-144',
  name: 'Kouamé Yao',
  dept: 'Opérations',
  days: 22,
  base: 330000,
  maritalStatus: 'single',
  dependents: 0,
  parts: 1,
  elements: [
    { id: '1', type: 'transport', label: 'Prime de transport', amount: 30000, gain: true, imposable: false, social: true },
  ],
  status: 'anomaly',
  alert: 'Absence du 12 au 18 juin',
};

const res2 = computeEmployeePay(kouameYaoValidation);
assert(res2.proratedBase === 242000, `Cas 2 Base proratisée 22j: attendu 242 000 F, obtenu ${res2.proratedBase} F`);
assert(res2.proratedTransport === 22000, `Cas 2 Transport proratisé 22j: attendu 22 000 F, obtenu ${res2.proratedTransport} F`);
assert(res2.brut === 264000, `Cas 2 Brut: attendu 264 000 F, obtenu ${res2.brut} F`);
assert(res2.cnpsSalariale === 15246, `Cas 2 CNPS salariale: attendu 15 246 F, obtenu ${res2.cnpsSalariale} F`);
assert(res2.cmu === 500, `Cas 2 CMU: attendu 500 F, obtenu ${res2.cmu} F`);
assert(res2.itsNet === 26820, `Cas 2 ITS net: attendu 26 820 F, obtenu ${res2.itsNet} F`);
assert(res2.net === 221434, `Cas 2 Net à payer: attendu 221 434 F, obtenu ${res2.net} F`);

// -------------------------------------------------------------
// CAS TEST 3 — N'Guessan Koffi Armand (salaire élevé)
// Brut: 1 898 128 F, CNPS: 117 692 F, CMU: 500 F, ITS: 378 351 F, Net: 1 401 585 F
// -------------------------------------------------------------
console.log('\n--- CAS TEST 3 : N\'Guessan Koffi Armand (30j, Marié 2 enfants = 3 parts, Brut 1 898 128 F) ---');
const nguessanArmand: Employee = {
  id: 99,
  matricule: '#RH-999',
  name: "N'Guessan Koffi Armand",
  dept: 'Direction',
  days: 30,
  base: 1868128,
  maritalStatus: 'married',
  dependents: 2,
  parts: 3, // Marié (2 parts) + 2 enfants (1 part) = 3 parts -> RICF = 4 demi-parts * 5500 = 22 000 F
  elements: [
    { id: '1', type: 'transport', label: 'Prime de transport', amount: 30000, gain: true, imposable: false, social: true },
  ],
  status: 'ok',
  alert: null,
};

const res3 = computeEmployeePay(nguessanArmand);
assert(res3.brut === 1898128, `Cas 3 Brut: attendu 1 898 128 F, obtenu ${res3.brut} F`);
assert(res3.assietteCNPS === 1868128, `Cas 3 Assiette CNPS: attendu 1 868 128 F, obtenu ${res3.assietteCNPS} F`);
assert(approx(res3.cnpsSalariale, 117692), `Cas 3 CNPS salariale: attendu 117 692 F, obtenu ${res3.cnpsSalariale} F`);
assert(res3.cmu === 500, `Cas 3 CMU: attendu 500 F, obtenu ${res3.cmu} F`);
assert(approx(res3.itsNet, 378351), `Cas 3 ITS net: attendu 378 351 F, obtenu ${res3.itsNet} F`);
assert(approx(res3.net, 1401585), `Cas 3 Net à payer: attendu 1 401 585 F, obtenu ${res3.net} F`);

// -------------------------------------------------------------
// TEST RÉTRO-CALCUL
// -------------------------------------------------------------
console.log('\n--- TEST DU RÉTRO-CALCUL SURSALAIRE POUR NET CIBLE ---');
// Cas A : Aka Koffi (Base 450k + Perf 55k -> Net actuel 420 635 F) pour atteindre 500 000 F Net
const targetNet = 500000;
const neededSurSalaryAka = reverseCalculateNetToSurSalary(akaKoffi, targetNet);
const akaWithSur: Employee = {
  ...akaKoffi,
  elements: [
    ...akaKoffi.elements,
    { id: 's', type: 'sursalaire', label: 'Sursalaire', amount: neededSurSalaryAka, gain: true, imposable: true, social: true },
  ],
};
const resRetroAka = computeEmployeePay(akaWithSur);
assert(approx(resRetroAka.net, targetNet, 5), `Rétro-calcul Aka Koffi (base 450k + perf 55k): Net cible ${targetNet} F atteint (obtenu: ${resRetroAka.net} F avec sursalaire ${neededSurSalaryAka} F)`);

// Cas B : Salarié standard (Base 330k, Transport 30k) pour atteindre 500 000 F Net
const standardEmp: Employee = {
  id: 101,
  matricule: '#TEST-330',
  name: 'Salarié Base 330k',
  dept: 'Test',
  days: 30,
  base: 330000,
  maritalStatus: 'single',
  dependents: 0,
  parts: 1,
  elements: [
    { id: 'tr', type: 'transport', label: 'Prime de transport', amount: 30000, gain: true, imposable: false, social: true },
  ],
  status: 'ok',
  alert: null,
};
const neededSurSalary330 = reverseCalculateNetToSurSalary(standardEmp, targetNet);
assert(approx(neededSurSalary330, 284168, 5), `Rétro-calcul Base 330k: Sursalaire attendu ~284 168 F, obtenu ${neededSurSalary330} F`);

const standardWithSur: Employee = {
  ...standardEmp,
  elements: [
    ...standardEmp.elements,
    { id: 's', type: 'sursalaire', label: 'Sursalaire', amount: neededSurSalary330, gain: true, imposable: true, social: true },
  ],
};
const resRetro330 = computeEmployeePay(standardWithSur);
assert(approx(resRetro330.net, targetNet, 5), `Rétro-calcul Base 330k: Net cible ${targetNet} F atteint (obtenu: ${resRetro330.net} F, brut: ${resRetro330.brut} F)`);

console.log('\n================================================================');
console.log('🎉 TOUS LES 3 CAS TEST OFFICIELS SONT VALIDÉS À 100% !');
console.log('================================================================');
