import { PayrollResult } from '../../types/payroll';

export type RawPayrollFields = Omit<
  PayrollResult,
  'gross' | 'netPay' | 'employerCost' | 'currency' | 'employeeDeductions' | 'breakdown'
>;

export function standardizePayrollResult(
  raw: RawPayrollFields,
  currency: string = 'FCFA'
): PayrollResult {
  return {
    ...raw,
    gross: raw.totalGrossSalary,
    netPay: raw.netSalaryToPay,
    employerCost: raw.totalEmployerCost,
    currency,
    employeeDeductions: {
      social: raw.totalEmployeeContributions,
      tax: raw.totalIncomeTax,
      other: raw.otherEmployeeTaxes || 0,
    },
    breakdown: {
      gross: raw.totalGrossSalary,
      taxableBase: raw.grossTaxableSalary,
      socialBase: raw.grossSocialSalary,
      abatement: raw.professionalAbatement,
      familyRelief: raw.familyDeductionRateOrAmount,
    },
  };
}
