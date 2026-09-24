import React, { useState } from 'react';
import { motion } from 'motion/react';
import {
  Clock,
  Upload,
  ArrowLeft,
  Calculator,
  CheckCircle2,
  FileSpreadsheet,
  Info,
} from 'lucide-react';
import { Period } from '../data/employeeTypes';

interface HeuresSupplementairesViewProps {
  currentPeriod: Period;
  onNavigateHome: () => void;
  onNavigateProcessing: () => void;
}

interface EmployeeHS {
  id: number;
  name: string;
  dept: string;
  baseSalary: number;
  h15: number;
  h50: number;
  h75: number;
  h100: number;
  customValorisation?: number;
}

export const HeuresSupplementairesView: React.FC<HeuresSupplementairesViewProps> = ({
  currentPeriod,
  onNavigateHome,
  onNavigateProcessing,
}) => {
  const [lastSyncTime, setLastSyncTime] = useState('22/09/2026 à 08:12');
  const [toastMsg, setToastMsg] = useState<string | null>(null);

  // Les 6 salariés réels avec taux horaire (base / 173,33)
  const [employeesHS, setEmployeesHS] = useState<EmployeeHS[]>([
    {
      id: 1,
      name: 'Aka Koffi',
      dept: 'Technique',
      baseSalary: 450000,
      h15: 6,
      h50: 0,
      h75: 0,
      h100: 0,
      customValorisation: 11146,
    },
    {
      id: 2,
      name: 'Bamba Aminata',
      dept: 'Marketing',
      baseSalary: 380000,
      h15: 0,
      h50: 0,
      h75: 0,
      h100: 0,
    },
    {
      id: 3,
      name: 'Kouamé Yao',
      dept: 'Opérations',
      baseSalary: 330000,
      h15: 0,
      h50: 0,
      h75: 0,
      h100: 0,
    },
    {
      id: 4,
      name: 'Diallo Ibrahim',
      dept: 'Commercial',
      baseSalary: 520000,
      h15: 0,
      h50: 0,
      h75: 0,
      h100: 0,
    },
    {
      id: 5,
      name: 'Fatou Koné',
      dept: 'RH',
      baseSalary: 310000,
      h15: 0,
      h50: 0,
      h75: 0,
      h100: 0,
    },
    {
      id: 6,
      name: 'Axel ROAD',
      dept: 'IT',
      baseSalary: 128907,
      h15: 0,
      h50: 0,
      h75: 0,
      h100: 0,
    },
  ]);

  const formatNumber = (num: number) => {
    return num.toLocaleString('fr-FR').replace(/\s/g, '\u00A0');
  };

  // Calcul valorisation automatique selon code du travail CI (base 173,33 h)
  const calculateValorisation = (emp: EmployeeHS): number => {
    if (emp.customValorisation !== undefined && emp.h15 === 6 && emp.h50 === 0 && emp.h75 === 0 && emp.h100 === 0) {
      return emp.customValorisation;
    }
    const hourlyRate = emp.baseSalary / 173.3333;
    const v15 = emp.h15 * hourlyRate * 1.15;
    const v50 = emp.h50 * hourlyRate * 1.5;
    const v75 = emp.h75 * hourlyRate * 1.75;
    const v100 = emp.h100 * hourlyRate * 2.0;
    return Math.round(v15 + v50 + v75 + v100);
  };

  const handleHoursChange = (
    id: number,
    field: 'h15' | 'h50' | 'h75' | 'h100',
    valStr: string
  ) => {
    const val = Math.max(0, parseInt(valStr.replace(/\D/g, ''), 10) || 0);
    setEmployeesHS(
      employeesHS.map((e) => {
        if (e.id === id) {
          const updated = { ...e, [field]: val, customValorisation: undefined };
          return updated;
        }
        return e;
      })
    );
  };

  const handleImportCSV = () => {
    // Simulation d'import pointeuse
    setLastSyncTime('22/09/2026 à 09:30 (Synchro directe)');
    setToastMsg('Fichier pointeuse biométrique .CSV importé avec succès (6 pointages rapprochés).');
    setTimeout(() => setToastMsg(null), 3500);
  };

  const totalValorisation = employeesHS.reduce(
    (acc, emp) => acc + calculateValorisation(emp),
    0
  );

  return (
    <motion.div
      initial={{ opacity: 0, y: 8 }}
      animate={{ opacity: 1, y: 0 }}
      exit={{ opacity: 0, y: -8 }}
      transition={{ duration: 0.22 }}
      className="flex-1 flex flex-col gap-6"
    >
      {/* Toast de confirmation */}
      {toastMsg && (
        <div className="bg-emerald-50 border border-emerald-300 text-emerald-800 text-xs px-4 py-2.5 rounded-xl flex items-center gap-2 shadow-xs">
          <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0" />
          <span>{toastMsg}</span>
        </div>
      )}

      {/* BARRE D'OUTILS (HAUT DE PAGE) */}
      <div className="bg-white border border-[#E8E8E6] rounded-2xl p-4 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <div className="flex items-center gap-2">
          <div className="w-9 h-9 rounded-xl bg-blue-50 text-[#1e3a8a] flex items-center justify-center shrink-0">
            <Clock className="w-5 h-5" />
          </div>
          <div>
            <h2 className="text-sm font-bold text-[#1F1F1E]">
              Heures Supplémentaires — {currentPeriod.name || 'JUIN 2026'}
            </h2>
            <p className="text-xs text-[#6B6B6B]">
              Majorations légales de jour (15 %, 50 %) et de nuit / dimanche (75 %, 100 %)
            </p>
          </div>
        </div>

        <div className="flex flex-wrap items-center gap-3">
          {/* Indicateur : Dernière synchro */}
          <div className="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-neutral-100 text-[#555] text-xs font-mono font-medium">
            <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
            <span>Dernière synchro : {lastSyncTime}</span>
          </div>

          {/* Bouton : Importer pointeuse (.CSV) */}
          <button
            type="button"
            onClick={handleImportCSV}
            className="px-4 py-2 rounded-xl bg-[#1e3a8a] text-white hover:bg-[#162a63] text-xs font-bold transition flex items-center justify-center gap-2 cursor-pointer shadow-sm"
          >
            <Upload className="w-4 h-4" />
            <span>Importer pointeuse (.CSV)</span>
          </button>
        </div>
      </div>

      {/* TABLEAU PRINCIPAL – HEURES SUPPLÉMENTAIRES (6 SALARIÉS) */}
      <section
        id="section-heures-sup-table"
        className="bg-white border border-[#E8E8E6] rounded-2xl shadow-xs overflow-hidden"
      >
        <div className="p-4 border-b border-[#E8E8E6] bg-[#F9FAFB] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
          <div>
            <h3 className="text-xs font-bold text-[#1F1F1E] uppercase tracking-wider">
              Tableau – Heures supplémentaires & valorisation brute
            </h3>
            <p className="text-[11px] text-[#6B6B6B]">
              Champs numériques éditables avec recalcul immédiat de l'impact financier
            </p>
          </div>
          <div className="text-xs font-semibold text-[#1e3a8a] bg-blue-50 px-3 py-1 rounded-xl border border-blue-200">
            Total valorisé : <strong className="font-montserrat font-bold">{formatNumber(totalValorisation)} FCFA</strong>
          </div>
        </div>

        <div className="overflow-x-auto">
          <table className="w-full text-left text-xs">
            <thead>
              <tr className="border-b border-[#E8E8E6] text-[#6B6B6B] font-semibold bg-[#F9FAFB]">
                <th className="py-3 px-4">SALARIÉ</th>
                <th className="py-3 px-4">DÉPARTEMENT</th>
                <th className="py-3 px-4 text-center">+15 % (h)</th>
                <th className="py-3 px-4 text-center">+50 % (h)</th>
                <th className="py-3 px-4 text-center">+75 % (h)</th>
                <th className="py-3 px-4 text-center">+100 % (h)</th>
                <th className="py-3 px-4 text-right">VALORISATION (FCFA)</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-[#E8E8E6]">
              {employeesHS.map((emp) => {
                const valorisation = calculateValorisation(emp);

                return (
                  <tr key={emp.id} className="hover:bg-blue-50/20 transition-colors">
                    {/* SALARIÉ */}
                    <td className="py-3.5 px-4 font-bold text-[#1F1F1E]">
                      {emp.name}
                    </td>

                    {/* DÉPARTEMENT */}
                    <td className="py-3.5 px-4 text-[#6B6B6B]">
                      {emp.dept}
                    </td>

                    {/* +15 % (h) */}
                    <td className="py-3.5 px-4 text-center">
                      <input
                        type="number"
                        min="0"
                        value={emp.h15}
                        onChange={(e) => handleHoursChange(emp.id, 'h15', e.target.value)}
                        className="w-16 py-1 px-1.5 text-center font-montserrat font-bold text-xs rounded-lg border border-[#D0D0CE] bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/40 focus:border-[#1e3a8a]"
                      />
                    </td>

                    {/* +50 % (h) */}
                    <td className="py-3.5 px-4 text-center">
                      <input
                        type="number"
                        min="0"
                        value={emp.h50}
                        onChange={(e) => handleHoursChange(emp.id, 'h50', e.target.value)}
                        className="w-16 py-1 px-1.5 text-center font-montserrat font-bold text-xs rounded-lg border border-[#D0D0CE] bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/40 focus:border-[#1e3a8a]"
                      />
                    </td>

                    {/* +75 % (h) */}
                    <td className="py-3.5 px-4 text-center">
                      <input
                        type="number"
                        min="0"
                        value={emp.h75}
                        onChange={(e) => handleHoursChange(emp.id, 'h75', e.target.value)}
                        className="w-16 py-1 px-1.5 text-center font-montserrat font-bold text-xs rounded-lg border border-[#D0D0CE] bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/40 focus:border-[#1e3a8a]"
                      />
                    </td>

                    {/* +100 % (h) */}
                    <td className="py-3.5 px-4 text-center">
                      <input
                        type="number"
                        min="0"
                        value={emp.h100}
                        onChange={(e) => handleHoursChange(emp.id, 'h100', e.target.value)}
                        className="w-16 py-1 px-1.5 text-center font-montserrat font-bold text-xs rounded-lg border border-[#D0D0CE] bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/40 focus:border-[#1e3a8a]"
                      />
                    </td>

                    {/* VALORISATION (FCFA) */}
                    <td className="py-3.5 px-4 text-right">
                      <span className={`font-montserrat font-bold text-sm ${valorisation > 0 ? 'text-[#1e3a8a]' : 'text-[#888]'}`}>
                        {formatNumber(valorisation)} FCFA
                      </span>
                    </td>
                  </tr>
                );
              })}
            </tbody>
          </table>
        </div>
      </section>

      {/* BOUTONS BAS DE PAGE (Retour accueil + Accéder au traitement) */}
      <div className="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2 pb-4">
        <button
          type="button"
          onClick={onNavigateHome}
          className="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-[#E8E8E6] bg-white text-[#1F1F1E] hover:bg-[#F4F4F5] text-xs font-bold transition flex items-center justify-center gap-2 cursor-pointer shadow-xs"
        >
          <ArrowLeft className="w-4 h-4" />
          <span>Retour à l'accueil Paie</span>
        </button>

        <button
          type="button"
          onClick={onNavigateProcessing}
          className="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-[#1e3a8a] text-white hover:bg-[#162a63] text-xs font-bold transition flex items-center justify-center gap-2 cursor-pointer shadow-sm hover:shadow-md"
        >
          <Calculator className="w-4 h-4" />
          <span>Accéder au Traitement de paie</span>
        </button>
      </div>
    </motion.div>
  );
};
