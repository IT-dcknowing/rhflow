import { CountryCode, CountryInfo, PayrollInputs } from '../types/payroll';

export const COUNTRIES: Record<CountryCode, CountryInfo> = {
  CI: {
    code: 'CI',
    name: "Côte d'Ivoire",
    flag: '🇨🇮',
    currency: 'FCFA',
    symbol: 'XOF',
    smig: 75000,
    taxName: 'ITS (Réforme 2024)',
    socialSecurityName: 'CNPS & CMU',
    exchangeRateToXOF: 1,
    description: 'ITS unifié 2024 (CGI art. 119 bis de 0% à 32%), RICF (5 500 F/demi-part), transport exonéré 30 000 FCFA, CNPS retraite 6.3% (plafond 3 375 000 FCFA) et CMU 500 FCFA.',
  },
};

export const DEFAULT_INPUTS: Record<CountryCode, PayrollInputs> = {
  CI: {
    country: 'CI',
    maritalStatus: 'single',
    dependents: 1,
    jobCategory: 'cadre',
    baseSalary: 500000,
    overSalary: 150000,
    seniorityMode: 'auto',
    seniorityYears: 3,
    seniorityBonusManual: 0,
    transportAllowance: 30000,
    benefitsInKind: 0,
    otherTaxableBonuses: 0,
    overtimePay: 0,
    cmuBeneficiariesCount: 1,
  },
};

export function formatCurrency(amount: number, _countryCode: CountryCode = 'CI'): string {
  const rounded = Math.round(amount);
  return `${new Intl.NumberFormat('fr-FR').format(rounded)} FCFA`;
}
