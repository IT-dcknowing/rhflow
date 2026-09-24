import { PayrollInputs, PayrollResult, ContributionLine, TaxBracketDetail } from '../../types/payroll';
import { standardizePayrollResult } from './standardizer';

export function calculateCoteIvoire(inputs: PayrollInputs): PayrollResult {
  const {
    baseSalary,
    overSalary,
    seniorityYears,
    transportAllowance,
    benefitsInKind,
    otherTaxableBonuses,
    overtimePay,
    maritalStatus,
    dependents,
  } = inputs;

  // 1. Prime d'ancienneté conventionnelle
  const seniorityRate = seniorityYears >= 2 ? Math.min(25, 2 + (seniorityYears - 2)) : 0;
  const seniorityBonus = Math.round((baseSalary * seniorityRate) / 100);

  // 2. Brut total
  const totalGrossSalary =
    baseSalary +
    overSalary +
    seniorityBonus +
    transportAllowance +
    benefitsInKind +
    otherTaxableBonuses +
    overtimePay;

  // 3. Exonération transport (plafond 30 000 FCFA selon arrêté interministériel)
  const transportCap = 30000;
  const transportExempt = Math.min(transportAllowance, transportCap);
  const transportTaxable = Math.max(0, transportAllowance - transportExempt);

  // Assiette CNPS = Brut - Transport (si exonéré)
  const cnpsBaseSalary = Math.max(0, totalGrossSalary - transportExempt);
  const cnpsCeiling = 3375000; // Plafond 2024-2026 : 3 375 000 FCFA (45 × SMIG de 75 000)
  const cnpsRetraiteBase = Math.min(cnpsBaseSalary, cnpsCeiling);

  // Cotisations CNPS
  const cnpsRetraiteEmployee = Math.round(cnpsRetraiteBase * 0.063); // 6.3%
  const cnpsRetraiteEmployer = Math.round(cnpsRetraiteBase * 0.077); // 7.7%

  // Prestations familiales (Plafond 70 000 FCFA, 5.75% patronal)
  const pfBase = Math.min(cnpsBaseSalary, 70000);
  const pfEmployer = Math.round(pfBase * 0.0575);

  // Accidents du travail / Maladies professionnelles (Plafond 70 000 FCFA, 3% patronal)
  const atEmployer = Math.round(pfBase * 0.03);

  // Maternité (Plafond 70 000 FCFA, 0.75% patronal)
  const matEmployer = Math.round(pfBase * 0.0075);

  // CMU (500 FCFA salarial + 500 FCFA patronal par bénéficiaire)
  const cmuCount = inputs.cmuBeneficiariesCount && inputs.cmuBeneficiariesCount > 0 ? inputs.cmuBeneficiariesCount : 1;
  const cmuEmployee = 500 * cmuCount;
  const cmuEmployer = 500 * cmuCount;

  const employeeContributions: ContributionLine[] = [
    {
      id: 'ci-cnps-retraite',
      name: 'CNPS Retraite',
      organization: 'CNPS',
      rateEmployee: 6.3,
      amountEmployee: cnpsRetraiteEmployee,
      rateEmployer: 7.7,
      amountEmployer: cnpsRetraiteEmployer,
      ceilingApplied: cnpsCeiling,
      notes: cnpsBaseSalary > cnpsCeiling ? 'Plafond 3 375 000 FCFA atteint' : undefined,
    },
    {
      id: 'ci-cmu',
      name: 'CMU (Assurance Maladie)',
      organization: 'CNAM',
      rateEmployee: 0,
      amountEmployee: cmuEmployee,
      rateEmployer: 0,
      amountEmployer: cmuEmployer,
      notes: '500 FCFA salarial + 500 FCFA patronal',
    },
  ];

  // Brut Imposable (Brut soumis aux impôts employeur CE et FDFP)
  const brutImposable = Math.max(0, totalGrossSalary - transportExempt);

  // Revenu Net Imposable à l'ITS (CGI art. 115 réformé 2024+) :
  // Brut imposable - cotisations sociales salariales obligatoires (CNPS retraite 6.3% + CMU)
  const baseITS = Math.max(0, brutImposable - cnpsRetraiteEmployee - cmuEmployee);

  // Barème progressif ITS Côte d'Ivoire (Réforme 2024+ - CGI art. 119 bis)
  const itsBrackets = [
    { min: 0, max: 75000, rate: 0 },
    { min: 75000, max: 240000, rate: 0.16 },
    { min: 240000, max: 800000, rate: 0.21 },
    { min: 800000, max: 2400000, rate: 0.24 },
    { min: 2400000, max: 8000000, rate: 0.28 },
    { min: 8000000, max: null, rate: 0.32 },
  ];

  let rawITS = 0;
  const taxBrackets: TaxBracketDetail[] = [];

  for (const b of itsBrackets) {
    let taxableInBracket = 0;
    if (baseITS > b.min) {
      if (b.max === null) {
        taxableInBracket = baseITS - b.min;
      } else {
        taxableInBracket = Math.min(baseITS, b.max) - b.min;
      }
    }
    const tax = Math.round(taxableInBracket * b.rate);
    rawITS += tax;
    if (taxableInBracket > 0 || b.min === 0) {
      taxBrackets.push({
        min: b.min,
        max: b.max,
        rate: Number((b.rate * 100).toFixed(0)),
        taxableAmount: taxableInBracket,
        taxAmount: tax,
      });
    }
  }

  // Calcul des parts et RICF :
  // Célibataire : 1 part (2 demi-parts), Marié : 2 parts (4 demi-parts)
  // + 0.5 part (1 demi-part) par enfant à charge, max 5 parts
  let parts = maritalStatus === 'married' ? 2 : 1;
  parts += dependents * 0.5;
  parts = Math.min(5, Math.max(1, parts));

  // RICF = 5 500 FCFA × (nombre de demi-parts au-delà de 1)
  const totalHalfParts = parts * 2;
  const halfPartsBeyondOne = Math.max(0, totalHalfParts - 2);
  const ricfAmount = halfPartsBeyondOne * 5500;

  const totalIncomeTax = Math.max(0, rawITS - ricfAmount);

  // Charges patronales additionnelles :
  // Contribution Employeur (CE) : 2.8% sur base imposable brute
  const ceEmployer = Math.round(brutImposable * 0.028);

  // FDFP : 1.6% après abattement de 20% (soit 1.6% × 80% = 1.28% effectif sur base imposable brute)
  const fdfpEmployer = Math.round(brutImposable * 0.016 * 0.80);

  const employerContributions: ContributionLine[] = [
    {
      id: 'ci-cnps-retraite',
      name: 'CNPS Retraite',
      organization: 'CNPS',
      rateEmployee: 6.3,
      amountEmployee: cnpsRetraiteEmployee,
      rateEmployer: 7.7,
      amountEmployer: cnpsRetraiteEmployer,
      ceilingApplied: cnpsCeiling,
      notes: cnpsBaseSalary > cnpsCeiling ? 'Plafond 3 375 000 FCFA atteint' : undefined,
    },
    {
      id: 'ci-cmu',
      name: 'CMU (Assurance Maladie)',
      organization: 'CNAM',
      rateEmployee: 0,
      amountEmployee: cmuEmployee,
      rateEmployer: 0,
      amountEmployer: cmuEmployer,
      notes: '500 FCFA salarial + 500 FCFA patronal',
    },
    {
      id: 'ci-cnps-pf',
      name: 'Prestations Familiales',
      organization: 'CNPS',
      rateEmployee: 0,
      amountEmployee: 0,
      rateEmployer: 5.75,
      amountEmployer: pfEmployer,
      ceilingApplied: 70000,
      notes: cnpsBaseSalary > 70000 ? 'Plafond 70 000 FCFA atteint' : undefined,
    },
    {
      id: 'ci-cnps-at',
      name: 'Accidents du Travail / MP',
      organization: 'CNPS',
      rateEmployee: 0,
      amountEmployee: 0,
      rateEmployer: 3.0,
      amountEmployer: atEmployer,
      ceilingApplied: 70000,
      notes: cnpsBaseSalary > 70000 ? 'Plafond 70 000 FCFA atteint' : undefined,
    },
    {
      id: 'ci-cnps-mat',
      name: 'Assurance Maternité',
      organization: 'CNPS',
      rateEmployee: 0,
      amountEmployee: 0,
      rateEmployer: 0.75,
      amountEmployer: matEmployer,
      ceilingApplied: 70000,
      notes: cnpsBaseSalary > 70000 ? 'Plafond 70 000 FCFA atteint' : undefined,
    },
    {
      id: 'ci-ce',
      name: 'Contribution Employeur (CE)',
      organization: 'Direction Générale des Impôts',
      rateEmployee: 0,
      amountEmployee: 0,
      rateEmployer: 2.8,
      amountEmployer: ceEmployer,
      notes: '2.8% local sur base imposable',
    },
    {
      id: 'ci-fdfp',
      name: 'FDFP (Taxe Apprentissage & Formation)',
      organization: 'FDFP / Trésor',
      rateEmployee: 0,
      amountEmployee: 0,
      rateEmployer: 1.28,
      amountEmployer: fdfpEmployer,
      notes: '1.6% après abattement de 20% (1.28% effectif)',
    },
  ];

  const totalEmployeeContributions = cnpsRetraiteEmployee + cmuEmployee;
  const totalEmployerContributions =
    cnpsRetraiteEmployer +
    pfEmployer +
    atEmployer +
    matEmployer +
    cmuEmployer +
    ceEmployer +
    fdfpEmployer;

  // Net = Brut - CNPS Salarial - CMU Salarial - Impôt Net
  const netSalaryToPay = Math.round(totalGrossSalary - totalEmployeeContributions - totalIncomeTax);
  const totalEmployerCost = Math.round(totalGrossSalary + totalEmployerContributions);

  return standardizePayrollResult({
    baseSalary,
    overSalary,
    seniorityBonus,
    transportAllowance,
    transportExempt,
    transportTaxable,
    benefitsInKind,
    otherTaxableBonuses,
    overtimePay,
    totalGrossSalary,
    grossTaxableSalary: brutImposable,
    grossSocialSalary: cnpsBaseSalary,
    employeeContributions,
    totalEmployeeContributions,
    professionalAbatement: 0,
    netTaxableBase: baseITS,
    familyDeductionRateOrAmount: ricfAmount,
    partsCount: parts,
    taxBrackets,
    totalIncomeTax,
    otherEmployeeTaxes: 0,
    netBeforeTax: totalGrossSalary - totalEmployeeContributions,
    netSalaryToPay,
    employerContributions,
    totalEmployerContributions,
    totalEmployerCost,
    netRatio: totalGrossSalary > 0 ? (netSalaryToPay / totalGrossSalary) * 100 : 0,
    employeeDeductionsRatio:
      totalGrossSalary > 0
        ? ((totalEmployeeContributions + totalIncomeTax) / totalGrossSalary) * 100
        : 0,
    employerChargesRatio:
      totalGrossSalary > 0 ? (totalEmployerContributions / totalGrossSalary) * 100 : 0,
  }, 'FCFA');
}
