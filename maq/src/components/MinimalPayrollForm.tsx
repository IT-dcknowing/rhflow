import React from 'react';
import { PayrollInputs } from '../types/payroll';
import { COUNTRIES } from '../data/countries';

interface MinimalPayrollFormProps {
  inputs: PayrollInputs;
  onChange: (newInputs: PayrollInputs) => void;
  onSubmit: () => void;
}

export const MinimalPayrollForm: React.FC<MinimalPayrollFormProps> = ({
  inputs,
  onChange,
  onSubmit,
}) => {
  const currentCountry = COUNTRIES['CI'];

  const handleNumericChange = (field: keyof PayrollInputs, value: string) => {
    const parsed = value === '' ? 0 : Math.max(0, parseInt(value, 10) || 0);
    onChange({
      ...inputs,
      [field]: parsed,
    });
  };

  // Calcul auto prime ancienneté conventionnelle CI
  const seniorityRate = inputs.seniorityYears >= 2 ? Math.min(25, 2 + (inputs.seniorityYears - 2)) : 0;
  const calculatedSeniorityBonus = Math.round((inputs.baseSalary * seniorityRate) / 100);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    onSubmit();
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-6">
      {/* Situation familiale & Enfants à charge */}
      <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div className="space-y-2">
          <label htmlFor="marital-status" className="block text-sm font-medium text-neutral-900">
            Situation familiale
          </label>
          <select
            id="marital-status"
            value={inputs.maritalStatus}
            onChange={(e) =>
              onChange({
                ...inputs,
                maritalStatus: e.target.value as 'single' | 'married',
              })
            }
            className="w-full rounded-md border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-950 transition-colors"
          >
            <option value="single">Célibataire (1 part)</option>
            <option value="married">Marié(e) (2 parts)</option>
          </select>
        </div>

        <div className="space-y-2">
          <label htmlFor="dependents-input" className="block text-sm font-medium text-neutral-900">
            Enfants à charge
          </label>
          <input
            id="dependents-input"
            type="number"
            min="0"
            max="12"
            value={inputs.dependents === 0 ? '' : inputs.dependents}
            placeholder="0"
            onChange={(e) => handleNumericChange('dependents', e.target.value)}
            className="w-full rounded-md border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 placeholder:text-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-950 transition-colors"
          />
          <p className="text-[11px] text-neutral-400">+0,5 part par enfant (max 5 parts pour le RICF)</p>
        </div>
      </div>

      {/* Nombre de bénéficiaires CMU */}
      <div className="space-y-2">
        <div className="flex items-center justify-between">
          <label htmlFor="cmu-beneficiaries" className="block text-sm font-medium text-neutral-900">
            Nombre d'assurés CMU
          </label>
          <span className="text-xs text-neutral-500 font-mono">
            500 FCFA / assuré
          </span>
        </div>
        <input
          id="cmu-beneficiaries"
          type="number"
          min="1"
          max="20"
          value={inputs.cmuBeneficiariesCount ?? 1}
          placeholder="1"
          onChange={(e) => handleNumericChange('cmuBeneficiariesCount', e.target.value)}
          className="w-full rounded-md border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 placeholder:text-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-950 transition-colors"
        />
        <p className="text-[11px] text-neutral-400">
          Couverture Maladie Universelle (CMU) : cotisation forfaitaire de 500 FCFA part salariale + 500 FCFA part patronale par assuré.
        </p>
      </div>

      {/* Salaire de base */}
      <div className="space-y-2">
        <div className="flex items-center justify-between">
          <label htmlFor="base-salary" className="block text-sm font-medium text-neutral-900">
            Salaire de base
          </label>
          <span className="text-xs text-neutral-400">
            {currentCountry.currency}
          </span>
        </div>
        <input
          id="base-salary"
          type="number"
          min="0"
          step="1000"
          value={inputs.baseSalary === 0 ? '' : inputs.baseSalary}
          placeholder="0"
          onChange={(e) => handleNumericChange('baseSalary', e.target.value)}
          className="w-full rounded-md border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 placeholder:text-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-950 transition-colors"
        />
      </div>

      {/* Sursalaire */}
      <div className="space-y-2">
        <div className="flex items-center justify-between">
          <label htmlFor="over-salary" className="block text-sm font-medium text-neutral-900">
            Sursalaire
          </label>
          <span className="text-xs text-neutral-400">
            {currentCountry.currency}
          </span>
        </div>
        <input
          id="over-salary"
          type="number"
          min="0"
          step="1000"
          value={inputs.overSalary === 0 ? '' : inputs.overSalary}
          placeholder="0"
          onChange={(e) => handleNumericChange('overSalary', e.target.value)}
          className="w-full rounded-md border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 placeholder:text-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-950 transition-colors"
        />
      </div>

      {/* Prime d’ancienneté (années + calcul auto) */}
      <div className="space-y-2">
        <div className="flex items-center justify-between">
          <label htmlFor="seniority-years" className="block text-sm font-medium text-neutral-900">
            Prime d’ancienneté (calcul auto)
          </label>
          <span className="text-xs text-neutral-500 font-mono">
            {inputs.seniorityYears >= 2 ? `+${seniorityRate}% (${calculatedSeniorityBonus.toLocaleString('fr-FR')} ${currentCountry.currency})` : '0%'}
          </span>
        </div>
        <div className="relative">
          <input
            id="seniority-years"
            type="number"
            min="0"
            max="40"
            value={inputs.seniorityYears === 0 ? '' : inputs.seniorityYears}
            placeholder="0"
            onChange={(e) => handleNumericChange('seniorityYears', e.target.value)}
            className="w-full rounded-md border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 placeholder:text-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-950 transition-colors"
          />
          <span className="absolute right-3 top-2 text-xs text-neutral-400 pointer-events-none">
            années
          </span>
        </div>
        <p className="text-xs text-neutral-400">
          Convention collective interprofessionnelle ivoirienne : 2% dès 2 ans de service, puis +1% par année supplémentaire (plafonné à 25%).
        </p>
      </div>

      {/* Indemnité de transport */}
      <div className="space-y-2">
        <div className="flex items-center justify-between">
          <label htmlFor="transport-allowance" className="block text-sm font-medium text-neutral-900">
            Indemnité de transport
          </label>
          <span className="text-xs text-neutral-400">
            {currentCountry.currency}
          </span>
        </div>
        <input
          id="transport-allowance"
          type="number"
          min="0"
          step="1000"
          value={inputs.transportAllowance === 0 ? '' : inputs.transportAllowance}
          placeholder="0"
          onChange={(e) => handleNumericChange('transportAllowance', e.target.value)}
          className="w-full rounded-md border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 placeholder:text-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-950 transition-colors"
        />
        <p className="text-[11px] text-neutral-500">
          Exonération fiscale (ITS) et sociale (CNPS) jusqu'à 30 000 FCFA par mois (arrêté interministériel ivoirien).
        </p>
      </div>

      {/* Autres primes imposables */}
      <div className="space-y-2">
        <div className="flex items-center justify-between">
          <label htmlFor="other-bonuses" className="block text-sm font-medium text-neutral-900">
            Autres primes imposables
          </label>
          <span className="text-xs text-neutral-400">
            {currentCountry.currency}
          </span>
        </div>
        <input
          id="other-bonuses"
          type="number"
          min="0"
          step="1000"
          value={inputs.otherTaxableBonuses === 0 ? '' : inputs.otherTaxableBonuses}
          placeholder="0"
          onChange={(e) => handleNumericChange('otherTaxableBonuses', e.target.value)}
          className="w-full rounded-md border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 placeholder:text-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-950 transition-colors"
        />
      </div>

      {/* Avantages en nature */}
      <div className="space-y-2">
        <div className="flex items-center justify-between">
          <label htmlFor="benefits-in-kind" className="block text-sm font-medium text-neutral-900">
            Avantages en nature
          </label>
          <span className="text-xs text-neutral-400">
            {currentCountry.currency}
          </span>
        </div>
        <input
          id="benefits-in-kind"
          type="number"
          min="0"
          step="1000"
          value={inputs.benefitsInKind === 0 ? '' : inputs.benefitsInKind}
          placeholder="0"
          onChange={(e) => handleNumericChange('benefitsInKind', e.target.value)}
          className="w-full rounded-md border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 placeholder:text-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-950 transition-colors"
        />
      </div>

      {/* Bouton principal noir : « Calculer » */}
      <div className="pt-2">
        <button
          id="calculate-button"
          type="submit"
          className="w-full rounded-md bg-neutral-950 px-4 py-2.5 text-sm font-medium text-white hover:bg-neutral-800 active:bg-neutral-900 focus:outline-none focus:ring-2 focus:ring-neutral-950 focus:ring-offset-2 transition-colors cursor-pointer"
        >
          Calculer
        </button>
      </div>
    </form>
  );
};
