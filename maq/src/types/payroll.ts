export type CountryCode = 'CI';

export type MaritalStatus = 'single' | 'married' | 'divorced' | 'widowed';
export type JobCategory = 'cadre' | 'non_cadre';

export interface CountryInfo {
  code: CountryCode;
  name: string;
  flag: string;
  currency: string;
  symbol: string;
  smig: number;
  taxName: string;
  socialSecurityName: string;
  exchangeRateToXOF: number;
  description: string;
}

export interface PayrollInputs {
  country: CountryCode;
  maritalStatus: MaritalStatus;
  dependents: number; // Nombre d'enfants à charge
  jobCategory: JobCategory;
  
  // Salary components
  baseSalary: number;
  overSalary: number; // Sursalaire
  seniorityMode: 'auto' | 'manual';
  seniorityYears: number; // Années pour calcul auto
  seniorityBonusManual: number; // Montant saisi manuel
  transportAllowance: number; // Prime de transport
  benefitsInKind: number; // Avantages en nature (logement, voiture, etc.)
  otherTaxableBonuses: number; // Autres primes imposables
  overtimePay: number; // Heures supplémentaires

  // Côte d'Ivoire
  cmuBeneficiariesCount?: number; // Nombre d'assurés CMU (500 FCFA/pers)
}

export interface ContributionLine {
  id: string;
  name: string;
  organization: string;
  rateEmployee: number;
  amountEmployee: number;
  rateEmployer: number;
  amountEmployer: number;
  ceilingApplied?: number;
  isExempt?: boolean;
  notes?: string;
}

export interface TaxBracketConfig {
  min: number;
  max: number | null;
  rate: number;
}

export type Bracket = TaxBracketConfig;

export interface TaxBracketDetail {
  min: number;
  max: number | null;
  rate: number;
  taxableAmount: number;
  taxAmount: number;
}

export interface CountryPayrollConfig {
  country: CountryCode;
  name: string;
  currency: string;
  socialContributions: {
    employeeRates: Array<{ id: string; name: string; rate: number; ceiling?: number; notes?: string }>;
    employerRates: Array<{ id: string; name: string; rate: number; ceiling?: number; notes?: string }>;
  };
  taxBrackets: Bracket[];
  familyRelief: {
    system: 'ricf' | 'parts_discount' | 'percentage' | 'fixed_allowance' | 'none';
    description: string;
    maxPartsOrCharges?: number;
  };
  specialRules: {
    transportExemptionCap?: number;
    professionalAbatementRate?: number;
    professionalAbatementCap?: number;
    benefitsInKindTaxableRate?: number;
    tripleControl?: boolean;
    criticalNotes?: string[];
  };
}

export interface PayrollResult {
  // Standardized developer outputs (Required as per specification)
  gross: number;
  netPay: number; // NET À PAYER
  employerCost: number; // COÛT TOTAL EMPLOYEUR
  currency: string;
  employeeDeductions: {
    social: number;
    tax: number;
    other: number;
  };
  breakdown: {
    gross: number;
    taxableBase: number;
    socialBase: number;
    abatement: number;
    familyRelief: number;
  };

  // Gross components
  baseSalary: number;
  overSalary: number;
  seniorityBonus: number;
  transportAllowance: number;
  transportExempt: number;
  transportTaxable: number;
  benefitsInKind: number;
  otherTaxableBonuses: number;
  overtimePay: number;
  
  totalGrossSalary: number; // Salaire brut total
  grossTaxableSalary: number; // Salaire brut imposable
  grossSocialSalary: number; // Assiette des cotisations sociales

  // Employee deductions
  employeeContributions: ContributionLine[];
  totalEmployeeContributions: number; // Total cotisations salariales

  // Tax computation
  professionalAbatement: number; // Abattement frais professionnels
  netTaxableBase: number; // Base imposable à l'impôt sur le revenu
  familyDeductionRateOrAmount: number; // Réduction charges de famille
  partsCount?: number; // Nombre de parts fiscales (ex: CI, etc.)
  taxBrackets: TaxBracketDetail[];
  totalIncomeTax: number; // Total impôt (ITS, IUTS, PAYE, RTS, etc.)
  otherEmployeeTaxes: number; // ex: CMU forfaitaire, etc.

  // Net payable
  netBeforeTax: number;
  netSalaryToPay: number; // NET À PAYER (très mis en avant)

  // Employer costs
  employerContributions: ContributionLine[];
  totalEmployerContributions: number;
  totalEmployerCost: number; // COÛT TOTAL EMPLOYEUR (Brut + Cotisations patronales)
  
  // Ratios & analytics
  netRatio: number; // Net / Brut %
  employeeDeductionsRatio: number; // (Cotisations + Impôts) / Brut %
  employerChargesRatio: number; // Charges patronales / Brut %
}

export interface SavedSimulation {
  id: string;
  name: string;
  date: string;
  country: CountryCode;
  inputs: PayrollInputs;
  result: PayrollResult;
}
