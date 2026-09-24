/**
 * Définition des types et interfaces pour la paie RH Flow
 */

export interface EmployeeElement {
  id: string;
  type: string;
  label: string;
  amount: number;
  gain: boolean; // true = gain / prime, false = retenue / prêt
  imposable: boolean;
  social: boolean;
  locked?: boolean;
  schedule?: string;
}

export type EmployeeStatus = 'ok' | 'anomaly' | 'pending';

export interface Employee {
  id: number;
  matricule: string;
  name: string;
  dept: string;
  days: number;
  base: number;
  elements: EmployeeElement[];
  status: EmployeeStatus;
  alert: string | null;
  maritalStatus?: 'single' | 'married';
  dependents?: number;
  parts?: number;
  seniority?: string;
  seniorityYears?: number;
  seniorityMonths?: number;
}

export interface ComputedEmployeePay {
  proratedBase: number;
  proratedTransport: number;
  otherGains: number;
  totalGains: number;
  totalRetenues: number;
  brut: number;
  transportExempt: number;
  assietteCNPS: number;
  cnpsSalariale: number;
  cmu: number;
  baseITS: number;
  rawITS: number;
  ricf: number;
  itsNet: number;
  totalCotis: number;
  net: number;
  // Employer
  cnpsRetraitePatronale: number;
  cnpsPFPatronale: number;
  cnpsATPatronale: number;
  cnpsMatPatronale: number;
  cmuPatronale: number;
  cePatronale: number;
  fdfpPatronale: number;
  totalChargesPatronales: number;
  coutTotalEmployeur: number;
}

export type PeriodStatus = 'payee' | 'validée' | 'en_cours' | 'brouillon';

export interface Period {
  name: string;
  status: PeriodStatus;
  dateOuverture?: string;
  dateCloture?: string;
  isLocked?: boolean;
}

export type ViewModelId = 'model1' | 'model2' | 'model3';
