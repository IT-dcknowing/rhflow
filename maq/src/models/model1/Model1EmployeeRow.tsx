import React from 'react';
import { Employee, ComputedEmployeePay } from '../../data/employeeTypes';
import { User, AlertTriangle, CheckCircle2 } from 'lucide-react';

interface Model1EmployeeRowProps {
  employee: Employee;
  computedPay: ComputedEmployeePay;
  isSelected: boolean;
  isExpertMode: boolean;
  onSelect: (checked: boolean) => void;
  onClick: () => void;
}

export const Model1EmployeeRow: React.FC<Model1EmployeeRowProps> = ({
  employee,
  computedPay,
  isSelected,
  isExpertMode,
  onSelect,
  onClick,
}) => {
  const isAnomaly = employee.status === 'anomaly';
  const isPending = employee.status === 'pending';

  return (
    <tr
      onClick={onClick}
      className={`cursor-pointer transition ${
        isAnomaly
          ? 'bg-[#FAFAFA] border-l-4 border-l-[#253e87]'
          : isPending
          ? 'hover:bg-[#F0F0EE] border-l-4 border-l-gray-300'
          : 'hover:bg-[#F0F0EE]'
      }`}
    >
      {/* Checkbox sélection en masse */}
      <td
        className="p-4 border-r border-[#E8E8E6] text-center"
        onClick={(e) => e.stopPropagation()}
      >
        <input
          type="checkbox"
          checked={isSelected}
          onChange={(e) => onSelect(e.target.checked)}
          className="emp-checkbox w-4 h-4 rounded border-[#E8E8E6] text-[#253e87] focus:ring-[#253e87] cursor-pointer"
        />
      </td>

      {/* Salarié (15px, font-weight 600 pour les noms) */}
      <td className="p-4 border-r border-[#E8E8E6] text-[15px] flex items-center justify-between text-[#1F1F1E]">
        <div className="flex flex-col">
          <span className="flex items-center gap-2.5 font-semibold">
            <User className="w-4 h-4 text-[#253e87] shrink-0" />
            <span>{employee.name}</span>
          </span>
          {employee.seniority && (
            <span className="text-[12px] text-gray-500 font-normal pl-6">
              Anc. {employee.seniority}
            </span>
          )}
        </div>
        {isAnomaly && (
          <span
            className="text-[12px] font-semibold px-3 py-1 rounded-full bg-[#253e87] text-white flex items-center gap-1 shadow-xs"
            title={employee.alert || 'Anomalie détectée'}
          >
            ⚠️ Anomalie
          </span>
        )}
        {isPending && (
          <span
            className="text-[12px] font-semibold px-3 py-1 rounded-full bg-gray-100 text-gray-700 border border-gray-200 flex items-center gap-1 shadow-2xs"
            title="En attente de saisie"
          >
            ⚪ En attente
          </span>
        )}
      </td>

      {/* Département (14-15px) */}
      <td className="p-4 border-r border-[#E8E8E6] text-[#1F1F1E] text-[14px] md:text-[15px] font-normal">
        {employee.dept}
      </td>

      {/* Jours (15px) */}
      <td
        className={`p-4 border-r border-[#E8E8E6] text-center font-mono text-[15px] ${
          employee.days < 30 ? 'text-[#253e87] font-semibold' : 'text-[#1F1F1E]'
        }`}
      >
        {employee.days}j
      </td>

      {/* Base (15-16px Montserrat font-weight 600) */}
      <td className="p-4 border-r border-[#E8E8E6] text-right font-montserrat text-[15px] font-semibold text-[#1F1F1E]">
        {employee.base.toLocaleString('fr-FR')}
      </td>

      {/* Primes (15-16px Montserrat font-weight 600) */}
      <td className="p-4 border-r border-[#E8E8E6] text-right font-montserrat bg-[#FAFAFA]/50 text-[15px] font-semibold text-[#1F1F1E]">
        {computedPay.totalGains.toLocaleString('fr-FR')}
      </td>

      {/* Colonnes Mode Expert */}
      {isExpertMode && (
        <>
          <td className="p-4 border-r border-[#E8E8E6] text-right font-montserrat text-neutral-700 text-[15px] font-medium">
            –{computedPay.totalCotis.toLocaleString('fr-FR')}
          </td>
          <td className="p-4 border-r border-[#E8E8E6] text-right font-montserrat text-neutral-700 text-[15px] font-medium">
            –{computedPay.itsNet.toLocaleString('fr-FR')}
          </td>
        </>
      )}

      {/* Net à payer (15-16px font-weight 600) */}
      <td className="p-4 text-right text-[15px] md:text-[16px] font-semibold font-montserrat">
        <div className="flex items-center justify-end gap-2">
          <span className={isAnomaly ? 'text-[#253e87]' : 'text-[#1F1F1E]'}>
            {computedPay.net.toLocaleString('fr-FR')} FCFA
          </span>
          {isAnomaly ? (
            <span title="Anomalie à vérifier">
              <AlertTriangle className="w-4 h-4 text-[#253e87] shrink-0" />
            </span>
          ) : isPending ? (
            <span
              className="w-4 h-4 rounded-full border-2 border-gray-400 bg-white inline-block shrink-0"
              title="⚪ En attente de saisie"
            />
          ) : (
            <span title="✓ Conforme">
              <CheckCircle2 className="w-4 h-4 text-[#253e87] shrink-0" />
            </span>
          )}
        </div>
      </td>
    </tr>
  );
};
