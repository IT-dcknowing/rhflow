import { CountryCode, CountryPayrollConfig } from '../types/payroll';

export const COUNTRY_PAYROLL_CONFIGS: Record<CountryCode, CountryPayrollConfig> = {
  CI: {
    country: 'CI',
    name: 'Côte d’Ivoire',
    currency: 'FCFA',
    socialContributions: {
      employeeRates: [
        { id: 'ci-cnps-retraite', name: 'CNPS Retraite', rate: 6.3, ceiling: 3375000, notes: 'Plafond légal 2024-2026' },
        { id: 'ci-cmu', name: 'CMU Salarié', rate: 0, notes: '500 FCFA forfaitaire par bénéficiaire' },
      ],
      employerRates: [
        { id: 'ci-cnps-retraite-emp', name: 'CNPS Retraite Employeur', rate: 7.7, ceiling: 3375000 },
        { id: 'ci-cnps-pf', name: 'Prestations Familiales', rate: 5.75, ceiling: 70000 },
        { id: 'ci-cnps-at', name: 'Accidents du Travail / MP', rate: 3.0, ceiling: 70000 },
        { id: 'ci-cnps-mat', name: 'Assurance Maternité', rate: 0.75, ceiling: 70000 },
        { id: 'ci-cmu-emp', name: 'CMU Employeur', rate: 0, notes: '500 FCFA forfaitaire par bénéficiaire' },
        { id: 'ci-ce', name: 'Contribution Employeur (CE)', rate: 2.8, notes: '2.8% personnel local / 12% expatrié' },
        { id: 'ci-fdfp', name: 'FDFP (Taxe d’apprentissage + Formation)', rate: 1.6, notes: '1.6% après abattement 20%' },
      ],
    },
    taxBrackets: [
      { min: 0, max: 75000, rate: 0 },
      { min: 75000, max: 240000, rate: 0.16 },
      { min: 240000, max: 800000, rate: 0.21 },
      { min: 800000, max: 2400000, rate: 0.24 },
      { min: 2400000, max: 8000000, rate: 0.28 },
      { min: 8000000, max: null, rate: 0.32 },
    ],
    familyRelief: {
      system: 'ricf',
      description: '5 500 FCFA par demi-part au-delà de 1 part (max 5 parts)',
      maxPartsOrCharges: 5,
    },
    specialRules: {
      transportExemptionCap: 30000,
      criticalNotes: [
        'Exonération de transport légale : 30 000 FCFA (arrêté interministériel)',
        'Barème progressif ITS unifié 2024+ (CGI art. 119 bis : 0% à 32%)',
        'Plafond CNPS Retraite actualisé à 3 375 000 FCFA (45 × SMIG)',
        'FDFP : 1.6% après abattement de 20% (soit 1.28% effectif sur base imposable)',
      ],
    },
  },
};
