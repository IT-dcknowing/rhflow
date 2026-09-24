import React, { useState } from 'react';
import { PayrollResult } from '../types/payroll';
import { formatCurrency, COUNTRIES } from '../data/countries';
import { ChevronDown, ChevronUp } from 'lucide-react';

interface MinimalPayrollResultProps {
  result: PayrollResult;
  countryCode?: 'CI';
}

export const MinimalPayrollResult: React.FC<MinimalPayrollResultProps> = ({
  result,
}) => {
  const [showDetails, setShowDetails] = useState<boolean>(false);
  const country = COUNTRIES['CI'];

  return (
    <div className="space-y-8">
      {/* Résumé Principal */}
      <div className="space-y-6">
        {/* Net à Payer (Mis en avant) */}
        <div className="rounded-lg border border-neutral-900 bg-neutral-950 p-6 text-white">
          <div className="text-xs uppercase tracking-wider text-neutral-400 font-medium">
            Net à Payer
          </div>
          <div className="mt-2 text-3xl sm:text-4xl font-semibold tracking-tight">
            {formatCurrency(result.netSalaryToPay)}
          </div>
          <div className="mt-1 text-xs text-neutral-400">
            Montant net perçu par le salarié ({result.netRatio.toFixed(1)}% du salaire brut)
          </div>
        </div>

        {/* Lignes principales épurées */}
        <div className="divide-y divide-neutral-100 border-y border-neutral-200 text-sm">
          <div className="flex items-center justify-between py-3.5">
            <span className="text-neutral-600">Salaire Brut Total</span>
            <span className="font-medium text-neutral-950">
              {formatCurrency(result.totalGrossSalary)}
            </span>
          </div>

          <div className="flex items-center justify-between py-3.5">
            <span className="text-neutral-600">Total Cotisations Salariales</span>
            <span className="font-medium text-neutral-950">
              {formatCurrency(result.totalEmployeeContributions)}
            </span>
          </div>

          <div className="flex items-center justify-between py-3.5">
            <span className="text-neutral-600">Impôt ({country.taxName})</span>
            <span className="font-medium text-neutral-950">
              {formatCurrency(result.totalIncomeTax)}
            </span>
          </div>

          <div className="flex items-center justify-between py-3.5">
            <span className="text-neutral-900 font-medium">Coût Total Employeur</span>
            <span className="font-semibold text-neutral-950">
              {formatCurrency(result.totalEmployerCost)}
            </span>
          </div>
        </div>
      </div>

      {/* Détail repliable */}
      <div className="pt-2">
        <button
          type="button"
          onClick={() => setShowDetails(!showDetails)}
          className="flex w-full items-center justify-between rounded-md border border-neutral-200 bg-white px-4 py-2.5 text-sm font-medium text-neutral-900 hover:bg-neutral-50 transition-colors cursor-pointer"
        >
          <span>{showDetails ? 'Masquer le détail du calcul' : 'Afficher le détail du calcul'}</span>
          {showDetails ? (
            <ChevronUp className="h-4 w-4 text-neutral-500" />
          ) : (
            <ChevronDown className="h-4 w-4 text-neutral-500" />
          )}
        </button>

        {showDetails && (
          <div className="mt-4 space-y-6 pt-2 text-xs">
            {/* 0. Composition du Salaire Brut */}
            <div className="space-y-2">
              <h4 className="font-semibold text-neutral-900 text-xs tracking-tight uppercase">
                Composition du Salaire Brut
              </h4>
              <div className="rounded-md border border-neutral-200 bg-white divide-y divide-neutral-100">
                <div className="flex justify-between p-2.5">
                  <span className="text-neutral-500">Salaire de base</span>
                  <span className="font-mono font-medium text-neutral-900">
                    {formatCurrency(result.baseSalary)}
                  </span>
                </div>
                {result.overSalary > 0 && (
                  <div className="flex justify-between p-2.5">
                    <span className="text-neutral-500">Sursalaire</span>
                    <span className="font-mono font-medium text-neutral-900">
                      {formatCurrency(result.overSalary)}
                    </span>
                  </div>
                )}
                {result.seniorityBonus > 0 && (
                  <div className="flex justify-between p-2.5">
                    <span className="text-neutral-500">Prime d'ancienneté</span>
                    <span className="font-mono font-medium text-neutral-900">
                      {formatCurrency(result.seniorityBonus)}
                    </span>
                  </div>
                )}
                {result.transportAllowance > 0 && (
                  <div className="flex justify-between p-2.5">
                    <div>
                      <span className="text-neutral-500">Indemnité de transport</span>
                      <span className="block text-[10px] text-neutral-400">
                        Exonéré : {formatCurrency(result.transportExempt)} | Imposable : {formatCurrency(result.transportTaxable)}
                      </span>
                    </div>
                    <span className="font-mono font-medium text-neutral-900">
                      {formatCurrency(result.transportAllowance)}
                    </span>
                  </div>
                )}
                {result.otherTaxableBonuses > 0 && (
                  <div className="flex justify-between p-2.5">
                    <span className="text-neutral-500">Autres primes imposables</span>
                    <span className="font-mono font-medium text-neutral-900">
                      {formatCurrency(result.otherTaxableBonuses)}
                    </span>
                  </div>
                )}
                {result.benefitsInKind > 0 && (
                  <div className="flex justify-between p-2.5">
                    <span className="text-neutral-500">Avantages en nature</span>
                    <span className="font-mono font-medium text-neutral-900">
                      {formatCurrency(result.benefitsInKind)}
                    </span>
                  </div>
                )}
                {result.overtimePay > 0 && (
                  <div className="flex justify-between p-2.5">
                    <span className="text-neutral-500">Heures supplémentaires</span>
                    <span className="font-mono font-medium text-neutral-900">
                      {formatCurrency(result.overtimePay)}
                    </span>
                  </div>
                )}
                <div className="flex justify-between p-2.5 bg-neutral-50 font-medium">
                  <span className="text-neutral-900">Total Salaire Brut</span>
                  <span className="font-mono font-semibold text-neutral-950">
                    {formatCurrency(result.totalGrossSalary)}
                  </span>
                </div>
              </div>
            </div>

            {/* 1. Base imposable ITS */}
            <div className="space-y-2">
              <h4 className="font-semibold text-neutral-900 text-xs tracking-tight uppercase">
                Base imposable & Déductions ITS (Réforme 2024)
              </h4>
              <div className="rounded-md border border-neutral-200 bg-white divide-y divide-neutral-100">
                <div className="flex justify-between p-3">
                  <div>
                    <span className="text-neutral-500">Salaire brut imposable</span>
                    <span className="block text-[10px] text-neutral-400">
                      Brut total déduction faite du transport exonéré (max 30 000 FCFA)
                    </span>
                  </div>
                  <span className="font-mono font-medium text-neutral-900">
                    {formatCurrency(result.grossTaxableSalary)}
                  </span>
                </div>
                <div className="flex justify-between p-3">
                  <div>
                    <span className="text-neutral-500">Cotisations sociales déductibles</span>
                    <span className="block text-[10px] text-neutral-400">
                      CNPS Retraite (6.3%) + CMU salariale
                    </span>
                  </div>
                  <span className="font-mono font-medium text-neutral-900">
                    -{formatCurrency(result.totalEmployeeContributions)}
                  </span>
                </div>
                <div className="flex justify-between p-3 bg-neutral-50">
                  <div>
                    <span className="font-medium text-neutral-900">Revenu Net Imposable (Base ITS)</span>
                    <span className="block text-[10px] text-neutral-500">
                      Art. 115 CGI ivoirien réformé
                    </span>
                  </div>
                  <span className="font-mono font-semibold text-neutral-950">
                    {formatCurrency(result.netTaxableBase)}
                  </span>
                </div>
              </div>
            </div>

            {/* 2. Impôt brut ITS et réductions familiales RICF */}
            <div className="space-y-2">
              <h4 className="font-semibold text-neutral-900 text-xs tracking-tight uppercase">
                Barème ITS 2024 & Réduction RICF
              </h4>
              <div className="rounded-md border border-neutral-200 bg-white divide-y divide-neutral-100">
                {result.taxBrackets.length > 0 && (
                  <div className="p-3 space-y-1.5">
                    <div className="text-neutral-500 font-medium">Tranches progressives CGI art. 119 bis :</div>
                    {result.taxBrackets.map((bracket, i) => (
                      <div key={i} className="flex justify-between text-neutral-600 font-mono text-[11px]">
                        <span>
                          {bracket.rate}% sur {formatCurrency(bracket.taxableAmount)}
                        </span>
                        <span className="text-neutral-900 font-medium">
                          {formatCurrency(bracket.taxAmount)}
                        </span>
                      </div>
                    ))}
                  </div>
                )}
                <div className="flex justify-between p-3">
                  <div>
                    <span className="text-neutral-500">Réduction d'impôt RICF</span>
                    <span className="block text-[10px] text-neutral-400">
                      5 500 FCFA par demi-part au-delà de 1 part ({result.partsCount ?? 1} part{(result.partsCount ?? 1) > 1 ? 's' : ''})
                    </span>
                  </div>
                  <span className="font-mono text-neutral-900">
                    {result.familyDeductionRateOrAmount > 0
                      ? `-${formatCurrency(result.familyDeductionRateOrAmount)}`
                      : '0 FCFA'}
                  </span>
                </div>
                <div className="flex justify-between p-3 bg-neutral-50">
                  <span className="font-medium text-neutral-900">Impôt ITS net à retenir</span>
                  <span className="font-mono font-semibold text-neutral-950">
                    {formatCurrency(result.totalIncomeTax)}
                  </span>
                </div>
              </div>
            </div>

            {/* 3. Détail des cotisations sociales */}
            <div className="space-y-2">
              <h4 className="font-semibold text-neutral-900 text-xs tracking-tight uppercase">
                Détail des charges sociales et patronales (CNPS & CMU & Fiscalité)
              </h4>
              <div className="overflow-x-auto rounded-md border border-neutral-200 bg-white">
                <table className="w-full text-left">
                  <thead className="border-b border-neutral-200 bg-neutral-50 text-[11px] font-medium text-neutral-500">
                    <tr>
                      <th className="p-2.5">Cotisation</th>
                      <th className="p-2.5">Organisme</th>
                      <th className="p-2.5 text-right">Part Salariale</th>
                      <th className="p-2.5 text-right">Part Patronale</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-neutral-100 font-mono text-[11px]">
                    {result.employeeContributions.map((c) => (
                      <tr key={c.id}>
                        <td className="p-2.5 font-sans">
                          <div className="font-medium text-neutral-900">{c.name}</div>
                          {c.notes && (
                            <span className="inline-block mt-0.5 rounded border border-neutral-200 bg-neutral-100 px-1.5 py-0.5 text-[10px] text-neutral-600 font-sans">
                              {c.notes}
                            </span>
                          )}
                        </td>
                        <td className="p-2.5 text-neutral-500">{c.organization}</td>
                        <td className="p-2.5 text-right text-neutral-900">
                          {formatCurrency(c.amountEmployee)}
                        </td>
                        <td className="p-2.5 text-right text-neutral-900">
                          {formatCurrency(c.amountEmployer)}
                        </td>
                      </tr>
                    ))}
                    {result.employerContributions
                      .filter((ec) => !result.employeeContributions.some((sc) => sc.id === ec.id))
                      .map((ec) => (
                        <tr key={ec.id}>
                          <td className="p-2.5 font-sans">
                            <div className="font-medium text-neutral-900">{ec.name}</div>
                            {ec.notes && (
                              <span className="inline-block mt-0.5 rounded border border-neutral-200 bg-neutral-100 px-1.5 py-0.5 text-[10px] text-neutral-600 font-sans">
                                {ec.notes}
                              </span>
                            )}
                          </td>
                          <td className="p-2.5 text-neutral-500">{ec.organization}</td>
                          <td className="p-2.5 text-right text-neutral-400">-</td>
                          <td className="p-2.5 text-right text-neutral-900">
                            {formatCurrency(ec.amountEmployer)}
                          </td>
                        </tr>
                      ))}
                  </tbody>
                  <tfoot className="border-t border-neutral-200 bg-neutral-50 font-sans font-semibold text-neutral-950">
                    <tr>
                      <td colSpan={2} className="p-2.5">Total</td>
                      <td className="p-2.5 text-right font-mono">
                        {formatCurrency(result.totalEmployeeContributions)}
                      </td>
                      <td className="p-2.5 text-right font-mono">
                        {formatCurrency(result.totalEmployerContributions)}
                      </td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};
