import { PayrollInputs, PayrollResult, CountryCode } from '../types/payroll';
import { COUNTRIES } from '../data/countries';
import { COUNTRY_PAYROLL_CONFIGS } from '../data/countryConfigs';
import { calculateCoteIvoire } from './countries/coteIvoire';

export {
  COUNTRY_PAYROLL_CONFIGS,
  calculateCoteIvoire,
};

/**
 * Calculateur central de paie - Côte d'Ivoire
 * Exécute la logique fiscale et sociale ivoirienne (ITS 2024, CNPS, CMU)
 * et retourne un objet standardisé et immuable.
 */
export function calculatePayroll(countryOrInputs: string | PayrollInputs, optionalInputs?: PayrollInputs): PayrollResult {
  let inputs: PayrollInputs;
  if (typeof countryOrInputs === 'string') {
    if (!optionalInputs) {
      throw new Error('inputs is required when country is passed as the first argument');
    }
    inputs = { ...optionalInputs, country: 'CI' };
  } else {
    inputs = { ...countryOrInputs, country: 'CI' };
  }

  const rawResult = calculateCoteIvoire(inputs);
  const currency = COUNTRIES['CI'].currency;

  const standardizedResult: PayrollResult = {
    ...rawResult,
    gross: rawResult.totalGrossSalary,
    netPay: rawResult.netSalaryToPay,
    employerCost: rawResult.totalEmployerCost,
    currency,
    employeeDeductions: {
      social: rawResult.totalEmployeeContributions,
      tax: rawResult.totalIncomeTax,
      other: rawResult.otherEmployeeTaxes || 0,
    },
    breakdown: {
      gross: rawResult.totalGrossSalary,
      taxableBase: rawResult.grossTaxableSalary,
      socialBase: rawResult.grossSocialSalary,
      abatement: rawResult.professionalAbatement,
      familyRelief: rawResult.familyDeductionRateOrAmount,
    },
  };

  return standardizedResult;
}

/**
 * Surcharge optionnelle acceptant (country, inputs)
 */
export function calculatePayrollForCountry(
  _country: CountryCode,
  inputs: PayrollInputs
): PayrollResult {
  return calculatePayroll(inputs);
}
