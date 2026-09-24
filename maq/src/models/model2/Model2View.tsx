import React, { useState, useEffect, useMemo } from 'react';
import { Employee, ComputedEmployeePay, Period, EmployeeElement } from '../../data/employeeTypes';
import { reverseCalculateNetToSurSalary } from '../../logic/payrollCalculator';
import { PayslipModal } from '../../components/PayslipModal';
import { MassProcessingToolbar } from '../../components/MassProcessingToolbar';
import { FormattedAmountInput } from '../../components/FormattedAmountInput';
import {
  Search,
  CheckCircle2,
  AlertTriangle,
  FileText,
  Table as TableIcon,
  ChevronLeft,
  ChevronRight,
  Calculator,
  Lock,
  X,
  Plus,
  Building2,
} from 'lucide-react';

interface Model2ViewProps {
  employees: Employee[];
  computedMap: Map<number, ComputedEmployeePay>;
  currentPeriod: Period;
  isReadOnly: boolean;
  onUpdateEmployee: (updated: Employee) => void;
  onUpdateEmployees: (updated: Employee[]) => void;
  onOpenValidationModal: () => void;
  onUnlockPeriod: () => void;
  onShowToast: (msg: string) => void;
}

export const Model2View: React.FC<Model2ViewProps> = ({
  employees,
  computedMap,
  currentPeriod,
  isReadOnly,
  onUpdateEmployee,
  onUpdateEmployees,
  onOpenValidationModal,
  onShowToast,
}) => {
  // Active employee for the live bulletin
  const [activeEmployeeId, setActiveEmployeeId] = useState<number>(() => {
    // Default to first employee or first anomaly
    const firstAnomaly = employees.find((e) => e.status === 'anomaly');
    return firstAnomaly ? firstAnomaly.id : (employees[0]?.id ?? 1);
  });

  // Mass selection IDs
  const [selectedIds, setSelectedIds] = useState<number[]>([]);

  // Display mode: split view (bulletin vivant) or mass view (fullscreen table)
  const [isMassViewOpen, setIsMassViewOpen] = useState<boolean>(false);

  // Search & filter
  const [sidebarSearch, setSidebarSearch] = useState<string>('');
  const [massSearch, setMassSearch] = useState<string>('');
  const [massFilterStatus, setMassFilterStatus] = useState<'all' | 'anomaly' | 'ok' | 'pending'>('all');

  // Modals
  const [isLoanModalOpen, setIsLoanModalOpen] = useState<boolean>(false);
  const [isTargetModalOpen, setIsTargetModalOpen] = useState<boolean>(false);
  const [targetNetInput, setTargetNetInput] = useState<number>(500000);
  const [isPdfModalOpen, setIsPdfModalOpen] = useState<boolean>(false);

  // Micro flash animation for calculations
  const [calcFlash, setCalcFlash] = useState<boolean>(false);

  const triggerCalcFlash = () => {
    setCalcFlash(true);
    setTimeout(() => setCalcFlash(false), 800);
  };

  // Active employee object & calculations
  const activeEmployee = employees.find((e) => e.id === activeEmployeeId) || employees[0];
  const activeCalc = activeEmployee ? computedMap.get(activeEmployee.id) : undefined;

  // Sorted employees list: Anomalies first, then alphabetical
  const sortedEmployees = useMemo(() => {
    return [...employees].sort((a, b) => {
      if (a.status === 'anomaly' && b.status !== 'anomaly') return -1;
      if (b.status === 'anomaly' && a.status !== 'anomaly') return 1;
      return a.name.localeCompare(b.name);
    });
  }, [employees]);

  // Filtered for sidebar
  const sidebarFiltered = useMemo(() => {
    const term = sidebarSearch.trim().toLowerCase();
    if (!term) return sortedEmployees;
    return sortedEmployees.filter(
      (emp) =>
        emp.name.toLowerCase().includes(term) ||
        emp.dept.toLowerCase().includes(term) ||
        emp.matricule.toLowerCase().includes(term)
    );
  }, [sortedEmployees, sidebarSearch]);

  // Filtered for mass view
  const massFiltered = useMemo(() => {
    const term = massSearch.trim().toLowerCase();
    return employees.filter((emp) => {
      const matchSearch =
        term === '' ||
        emp.name.toLowerCase().includes(term) ||
        emp.dept.toLowerCase().includes(term) ||
        emp.matricule.toLowerCase().includes(term);

      const matchStatus =
        massFilterStatus === 'all' ||
        (massFilterStatus === 'anomaly' && emp.status === 'anomaly') ||
        (massFilterStatus === 'ok' && emp.status === 'ok') ||
        (massFilterStatus === 'pending' && emp.status === 'pending');

      return matchSearch && matchStatus;
    });
  }, [employees, massSearch, massFilterStatus]);

  // Mass view totals
  const massTotals = useMemo(() => {
    let days = 0;
    let base = 0;
    let primes = 0;
    let cotis = 0;
    let impot = 0;
    let net = 0;

    for (const emp of massFiltered) {
      const c = computedMap.get(emp.id);
      if (c) {
        days += emp.days;
        base += emp.base;
        primes += c.totalGains;
        cotis += c.totalCotis;
        impot += c.itsNet;
        net += c.net;
      }
    }
    return { days, base, primes, cotis, impot, net };
  }, [massFiltered, computedMap]);

  // Keyboard navigation (ArrowUp / ArrowDown)
  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if (isMassViewOpen || isTargetModalOpen || isLoanModalOpen || isPdfModalOpen) return;
      const tag = (document.activeElement?.tagName || '').toUpperCase();
      if (tag === 'INPUT' || tag === 'SELECT' || tag === 'TEXTAREA') return;

      if (e.key === 'ArrowUp') {
        e.preventDefault();
        navigateEmployee(-1);
      } else if (e.key === 'ArrowDown') {
        e.preventDefault();
        navigateEmployee(1);
      }
    };

    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [sortedEmployees, activeEmployeeId, isMassViewOpen, isTargetModalOpen, isLoanModalOpen, isPdfModalOpen]);

  const navigateEmployee = (direction: number) => {
    const currentIdx = sortedEmployees.findIndex((e) => e.id === activeEmployeeId);
    const newIdx = currentIdx + direction;
    if (newIdx >= 0 && newIdx < sortedEmployees.length) {
      setActiveEmployeeId(sortedEmployees[newIdx].id);
      triggerCalcFlash();
    }
  };

  // Direct editing of active employee's days
  const handleUpdateDays = (val: number | string) => {
    if (!activeEmployee || isReadOnly) return;
    const parsedDays = typeof val === 'number' ? val : Math.max(0, Math.min(30, parseInt(val, 10) || 0));
    const isFull = parsedDays === 30;
    const updated: Employee = {
      ...activeEmployee,
      days: parsedDays,
      status: isFull && activeEmployee.alert?.includes('Pointeuse') ? 'ok' : activeEmployee.status,
      alert: isFull && activeEmployee.alert?.includes('Pointeuse') ? null : activeEmployee.alert,
    };
    onUpdateEmployee(updated);
    triggerCalcFlash();
    onShowToast(`Jours mis à jour (${parsedDays}j) pour ${activeEmployee.name}`);
  };

  // Direct editing of active employee's base salary
  const handleUpdateBase = (val: number | string) => {
    if (!activeEmployee || isReadOnly) return;
    const parsedBase = typeof val === 'number' ? val : Math.max(0, parseInt(val, 10) || 0);
    const updated: Employee = {
      ...activeEmployee,
      base: parsedBase,
    };
    onUpdateEmployee(updated);
    triggerCalcFlash();
    onShowToast(`Salaire de base mis à jour pour ${activeEmployee.name}`);
  };

  // Direct editing of an element amount
  const handleUpdateElementAmount = (index: number, val: number | string) => {
    if (!activeEmployee || isReadOnly) return;
    const parsedAmt = typeof val === 'number' ? val : Math.max(0, parseInt(val, 10) || 0);
    const updatedElements = [...activeEmployee.elements];
    if (updatedElements[index]) {
      updatedElements[index] = {
        ...updatedElements[index],
        amount: parsedAmt,
      };
      onUpdateEmployee({
        ...activeEmployee,
        elements: updatedElements,
      });
      triggerCalcFlash();
      onShowToast(`Montant mis à jour : ${parsedAmt.toLocaleString('fr-FR')} FCFA`);
    }
  };

  // Remove element from active employee
  const handleRemoveElement = (index: number) => {
    if (!activeEmployee || isReadOnly) return;
    const el = activeEmployee.elements[index];
    if (el?.locked) {
      onShowToast('Impossible de supprimer un prêt en cours avec échéancier.');
      return;
    }
    const updatedElements = activeEmployee.elements.filter((_, i) => i !== index);
    onUpdateEmployee({
      ...activeEmployee,
      elements: updatedElements,
    });
    triggerCalcFlash();
    onShowToast(`Élément retiré du bulletin de ${activeEmployee.name}`);
  };

  // Add element from catalog
  const handleAddFromCatalog = (e: React.ChangeEvent<HTMLSelectElement>) => {
    const val = e.target.value;
    if (!val || !activeEmployee || isReadOnly) return;

    let newEl: EmployeeElement | null = null;
    const uid = `el-${Date.now()}`;

    if (val === 'transport') {
      newEl = {
        id: uid,
        type: 'transport',
        label: 'Prime de transport',
        amount: 30000,
        gain: true,
        imposable: false,
        social: true,
      };
    } else if (val === 'sursalaire') {
      newEl = {
        id: uid,
        type: 'sursalaire',
        label: 'Sursalaire',
        amount: 50000,
        gain: true,
        imposable: true,
        social: true,
      };
    } else if (val === 'logement') {
      newEl = {
        id: uid,
        type: 'logement',
        label: 'Prime de logement',
        amount: 40000,
        gain: true,
        imposable: true,
        social: false,
      };
    } else if (val === 'representation') {
      newEl = {
        id: uid,
        type: 'representation',
        label: 'Prime de représentation',
        amount: 35000,
        gain: true,
        imposable: true,
        social: true,
      };
    } else if (val === 'performance') {
      newEl = {
        id: uid,
        type: 'performance',
        label: 'Prime de performance',
        amount: 45000,
        gain: true,
        imposable: true,
        social: true,
      };
    } else if (val === 'gratification') {
      newEl = {
        id: uid,
        type: 'gratification',
        label: 'Gratification exceptionnelle',
        amount: 60000,
        gain: true,
        imposable: true,
        social: true,
      };
    } else if (val.startsWith('av_')) {
      const typeLabel = val === 'av_vehicule' ? 'Véhicule' : val === 'av_logement' ? 'Logement' : 'Téléphone';
      newEl = {
        id: uid,
        type: val,
        label: `Avantage en nature ${typeLabel}`,
        amount: 30000,
        gain: true,
        imposable: true,
        social: false,
      };
    } else if (val === 'avance') {
      newEl = {
        id: uid,
        type: 'avance',
        label: 'Avance sur salaire',
        amount: 50000,
        gain: false,
        imposable: false,
        social: false,
      };
    } else if (val === 'retenue') {
      newEl = {
        id: uid,
        type: 'retenue',
        label: 'Retenue diverse',
        amount: 15000,
        gain: false,
        imposable: false,
        social: false,
      };
    } else if (val === 'cantine') {
      newEl = {
        id: uid,
        type: 'cantine',
        label: 'Participation cantine',
        amount: 10000,
        gain: false,
        imposable: false,
        social: false,
      };
    }

    if (newEl) {
      onUpdateEmployee({
        ...activeEmployee,
        elements: [...activeEmployee.elements, newEl],
      });
      triggerCalcFlash();
      onShowToast(`Ajouté : ${newEl.label}`);
    }
    e.target.value = '';
  };

  // Target Net Calculation
  const handleConfirmTargetNet = () => {
    if (!activeEmployee || isReadOnly) return;
    const targetVal = typeof targetNetInput === 'number' ? targetNetInput : parseInt(String(targetNetInput).replace(/\D/g, ''), 10);
    if (!targetVal || isNaN(targetVal)) {
      onShowToast('Montant invalide');
      return;
    }

    const neededSurSalary = reverseCalculateNetToSurSalary(activeEmployee, targetVal);
    const existingIdx = activeEmployee.elements.findIndex((e) => e.type === 'sursalaire');
    let updatedElements = [...activeEmployee.elements];

    if (existingIdx >= 0) {
      updatedElements[existingIdx] = {
        ...updatedElements[existingIdx],
        amount: neededSurSalary,
      };
    } else {
      updatedElements.push({
        id: `sursalaire-${Date.now()}`,
        type: 'sursalaire',
        label: 'Ajustement cible (Sursalaire)',
        amount: neededSurSalary,
        gain: true,
        imposable: true,
        social: true,
      });
    }

    onUpdateEmployee({
      ...activeEmployee,
      elements: updatedElements,
    });

    setIsTargetModalOpen(false);
    triggerCalcFlash();
    onShowToast(`🎯 Rétro-calcul réussi : Net cible ajusté à ${targetVal.toLocaleString('fr-FR')} FCFA`);
  };

  return (
    <div className="flex-1 flex flex-col bg-white border border-[#E8E8E6] rounded-2xl overflow-hidden relative shadow-xs">
      {/* SOUS-BARRE D'OUTILS MODÈLE 2 */}
      <div className="border-b border-[#E8E8E6] bg-[#FAFAFA] px-6 py-2.5 flex items-center justify-between text-xs font-bold">
        <div className="flex items-center gap-3">
          <span className="text-[#253e87] font-bold uppercase tracking-wider flex items-center gap-1.5">
            <FileText className="w-4 h-4" />
            <span>Modèle 2 — Le Bulletin Vivant</span>
          </span>
          <span className="text-[#6B6B6B] hidden sm:inline">
            • Le bulletin lui-même est l'éditeur • Raccourcis clavier (Flèches ↑ / ↓)
          </span>
        </div>

        <div className="flex items-center gap-3">
          {/* Toggle Vue Masse / Vue Bulletin */}
          <button
            id="view-mode-btn"
            type="button"
            onClick={() => setIsMassViewOpen(!isMassViewOpen)}
            className="px-3 py-1.5 rounded-lg border border-[#E8E8E6] bg-white text-[#1F1F1E] hover:bg-[#F4F4F5] transition flex items-center gap-1.5 cursor-pointer font-bold shadow-2xs"
          >
            {isMassViewOpen ? (
              <>
                <FileText className="w-3.5 h-3.5 text-[#253e87]" />
                <span>Retour au Bulletin Vivant</span>
              </>
            ) : (
              <>
                <TableIcon className="w-3.5 h-3.5 text-[#253e87]" />
                <span>Basculer en Vue Masse</span>
              </>
            )}
          </button>

          {/* Badge Calculs à jour avec animation */}
          <div
            className={`px-3 py-1.5 rounded-lg border border-[#E8E8E6] bg-white font-mono text-xs font-bold transition flex items-center gap-1.5 ${
              calcFlash ? 'border-[#253e87] text-[#253e87] bg-blue-50/50 scale-105' : 'text-[#6B6B6B]'
            }`}
          >
            <span className={`w-2 h-2 rounded-full ${calcFlash ? 'bg-[#253e87] animate-ping' : 'bg-emerald-600'}`} />
            <span>Calculs CI à jour</span>
          </div>
        </div>
      </div>

      {/* 1. VUE MASSE (TABLEAU PLEIN ÉCRAN SI ACTIVÉE) */}
      {isMassViewOpen ? (
        <div id="mass-view-container" className="flex-1 bg-white p-6 overflow-y-auto space-y-4">
          <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
              <h2 className="text-lg font-bold tracking-tight text-[#1F1F1E]">
                Vue Masse — Paie Globale ({currentPeriod.name})
              </h2>
              <p className="text-xs text-[#6B6B6B] font-bold">
                Synthèse expert • {employees.length} salariés • Moteur CI 2026 unifié en temps réel
              </p>
            </div>
            <div className="flex items-center gap-3">
              <div className="relative">
                <Search className="w-4 h-4 absolute left-3 top-2.5 text-[#6B6B6B]" />
                <input
                  type="text"
                  value={massSearch}
                  onChange={(e) => setMassSearch(e.target.value)}
                  placeholder="Filtrer par nom, département..."
                  className="border border-[#E8E8E6] rounded-lg pl-9 pr-4 py-2 text-xs bg-[#FAFAFA] focus:outline-none focus:border-[#253e87] w-64 text-[#1F1F1E] font-bold"
                />
              </div>
              <select
                value={massFilterStatus}
                onChange={(e) => setMassFilterStatus(e.target.value as 'all' | 'anomaly' | 'ok' | 'pending')}
                className="border border-[#E8E8E6] rounded-lg px-3 py-2 text-xs bg-white focus:outline-none text-[#1F1F1E] font-bold cursor-pointer"
              >
                <option value="all">Tous les statuts ({employees.length})</option>
                <option value="anomaly">Anomalies uniquement ({employees.filter((e) => e.status === 'anomaly').length})</option>
                <option value="ok">Sans anomalie ({employees.filter((e) => e.status === 'ok').length})</option>
                <option value="pending">En attente ({employees.filter((e) => e.status === 'pending').length})</option>
              </select>
            </div>
          </div>

          {/* Barre d'outils de traitement de masse */}
          <MassProcessingToolbar
            selectedIds={selectedIds}
            employees={employees}
            isReadOnly={isReadOnly}
            onClearSelection={() => setSelectedIds([])}
            onUpdateEmployees={onUpdateEmployees}
            onShowToast={onShowToast}
          />

          <div className="border border-[#E8E8E6] rounded-xl overflow-x-auto bg-white shadow-xs">
            <table className="w-full text-left border-collapse text-[15px]">
              <thead>
                <tr className="border-b border-[#E8E8E6] bg-[#FAFAFA] text-[#1F1F1E] uppercase text-[13px] md:text-[14px] font-semibold tracking-[0.5px]">
                  <th className="p-3.5 border-r border-[#E8E8E6] w-10 text-center">
                    <input
                      type="checkbox"
                      checked={massFiltered.length > 0 && massFiltered.every((e) => selectedIds.includes(e.id))}
                      onChange={(e) => {
                        if (e.target.checked) {
                          setSelectedIds(Array.from(new Set([...selectedIds, ...massFiltered.map((emp) => emp.id)])));
                        } else {
                          const idsToRemove = new Set(massFiltered.map((emp) => emp.id));
                          setSelectedIds(selectedIds.filter((id) => !idsToRemove.has(id)));
                        }
                      }}
                      className="rounded border-[#E8E8E6] text-[#253e87] focus:ring-[#253e87] cursor-pointer"
                    />
                  </th>
                  <th className="p-3.5 border-r border-[#E8E8E6]">SALARIÉ</th>
                  <th className="p-3.5 border-r border-[#E8E8E6]">DÉPARTEMENT</th>
                  <th className="p-3.5 border-r border-[#E8E8E6] text-center">JOURS</th>
                  <th className="p-3.5 border-r border-[#E8E8E6] text-right">BASE (FCFA)</th>
                  <th className="p-3.5 border-r border-[#E8E8E6] text-right">PRIMES & GAINS</th>
                  <th className="p-3.5 border-r border-[#E8E8E6] text-right">COTIS. (CI)</th>
                  <th className="p-3.5 border-r border-[#E8E8E6] text-right">IMPÔT (ITS)</th>
                  <th className="p-3.5 text-right font-semibold">NET À PAYER</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-[#E8E8E6] text-[#1F1F1E] text-[15px]">
                {massFiltered.map((emp) => {
                  const calc = computedMap.get(emp.id)!;
                  const isAnomaly = emp.status === 'anomaly';
                  const isSelected = selectedIds.includes(emp.id);
                  return (
                    <tr
                      key={emp.id}
                      className={`hover:bg-[#F4F4F5] transition ${
                        isSelected ? 'bg-blue-50/40' : isAnomaly ? 'border-l-4 border-l-[#253e87] bg-blue-50/20' : ''
                      }`}
                    >
                      <td className="p-3.5 border-r border-[#E8E8E6] text-center" onClick={(e) => e.stopPropagation()}>
                        <input
                          type="checkbox"
                          checked={isSelected}
                          onChange={(e) => {
                            if (e.target.checked) {
                              setSelectedIds((prev) => [...prev, emp.id]);
                            } else {
                              setSelectedIds((prev) => prev.filter((id) => id !== emp.id));
                            }
                          }}
                          className="rounded border-[#E8E8E6] text-[#253e87] focus:ring-[#253e87] cursor-pointer"
                        />
                      </td>
                      <td
                        className="p-3.5 border-r border-[#E8E8E6] font-semibold flex items-center gap-2 cursor-pointer text-[15px]"
                        onClick={() => {
                          setActiveEmployeeId(emp.id);
                          setIsMassViewOpen(false);
                          triggerCalcFlash();
                        }}
                      >
                        {isAnomaly && <AlertTriangle className="w-3.5 h-3.5 text-[#253e87] shrink-0" />}
                        <span>{emp.name}</span>
                      </td>
                      <td className="p-3.5 border-r border-[#E8E8E6] text-[#1F1F1E] text-[14px] md:text-[15px]">{emp.dept}</td>
                      <td className="p-3.5 border-r border-[#E8E8E6] text-center font-mono text-[15px]">{emp.days}j</td>
                      <td className="p-3.5 border-r border-[#E8E8E6] text-right font-montserrat text-[15px] font-semibold">
                        {emp.base.toLocaleString('fr-FR')}
                      </td>
                      <td className="p-3.5 border-r border-[#E8E8E6] text-right font-montserrat text-[15px] font-semibold">
                        {calc.totalGains.toLocaleString('fr-FR')}
                      </td>
                      <td className="p-3.5 border-r border-[#E8E8E6] text-right font-montserrat text-[#6B6B6B] text-[15px]">
                        –{calc.totalCotis.toLocaleString('fr-FR')}
                      </td>
                      <td className="p-3.5 border-r border-[#E8E8E6] text-right font-montserrat text-[#6B6B6B] text-[15px]">
                        –{calc.itsNet.toLocaleString('fr-FR')}
                      </td>
                      <td className="p-3.5 text-right font-montserrat font-semibold text-[#253e87] text-[15px]">
                        {calc.net.toLocaleString('fr-FR')} FCFA
                      </td>
                    </tr>
                  );
                })}
              </tbody>
              <tfoot>
                <tr className="border-t-2 border-[#253e87] bg-[#FAFAFA] font-semibold text-[#1F1F1E] text-[14px]">
                  <td className="p-3.5 border-r border-[#E8E8E6] text-center font-mono text-[12px] text-[#6B6B6B]">
                    {selectedIds.length > 0 ? `${selectedIds.length} coché${selectedIds.length > 1 ? 's' : ''}` : '—'}
                  </td>
                  <td className="p-3.5 border-r border-[#E8E8E6]">TOTAUX ({massFiltered.length})</td>
                  <td className="p-3.5 border-r border-[#E8E8E6]">—</td>
                  <td className="p-3.5 border-r border-[#E8E8E6] text-center font-mono text-[15px]">
                    {massTotals.days.toLocaleString('fr-FR')}
                  </td>
                  <td className="p-3.5 border-r border-[#E8E8E6] text-right font-montserrat text-[15px] font-semibold">
                    {massTotals.base.toLocaleString('fr-FR')}
                  </td>
                  <td className="p-3.5 border-r border-[#E8E8E6] text-right font-montserrat text-[15px] font-semibold">
                    {massTotals.primes.toLocaleString('fr-FR')}
                  </td>
                  <td className="p-3.5 border-r border-[#E8E8E6] text-right font-montserrat text-[15px]">
                    –{massTotals.cotis.toLocaleString('fr-FR')}
                  </td>
                  <td className="p-3.5 border-r border-[#E8E8E6] text-right font-montserrat text-[15px]">
                    –{massTotals.impot.toLocaleString('fr-FR')}
                  </td>
                  <td className="p-3.5 text-right font-montserrat text-[16px] font-semibold text-[#253e87]">
                    {massTotals.net.toLocaleString('fr-FR')} FCFA
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>

          <div className="flex justify-end pt-2">
            <button
              type="button"
              onClick={() => setIsMassViewOpen(false)}
              className="px-5 py-2.5 bg-[#253e87] text-white hover:bg-[#1c306d] text-xs font-bold rounded-lg transition cursor-pointer"
            >
              Retour au Bulletin Vivant
            </button>
          </div>
        </div>
      ) : (
        /* 2. VUE SPLIT (BULLETIN VIVANT STANDARD) */
        <div className="flex-1 flex overflow-hidden">
          {/* GAUCHE : LISTE COMPACTE TYPE BOÎTE MAIL (w-80) */}
          <aside className="w-80 border-r border-[#E8E8E6] flex flex-col bg-[#FAFAFA] shrink-0">
            <div className="p-3.5 border-b border-[#E8E8E6] space-y-2.5">
              <div className="flex items-center justify-between">
                <span className="text-xs font-bold uppercase tracking-wider text-[#6B6B6B]">
                  Employés (<span id="emp-count">{employees.length}</span>)
                </span>
                <span className="text-[10px] bg-[#E8E8E6] px-2 py-0.5 rounded font-mono font-bold text-[#1F1F1E]">
                  Tri : Anomalies 1er
                </span>
              </div>
              <div className="relative">
                <Search className="w-3.5 h-3.5 absolute left-2.5 top-2 text-[#6B6B6B]" />
                <input
                  type="text"
                  value={sidebarSearch}
                  onChange={(e) => setSidebarSearch(e.target.value)}
                  placeholder="Rechercher..."
                  className="w-full border border-[#E8E8E6] rounded-lg pl-8 pr-3 py-1.5 text-xs bg-white focus:outline-none focus:border-[#253e87] text-[#1F1F1E] font-bold"
                />
              </div>
            </div>

            <div id="sidebar-list" className="flex-1 overflow-y-auto divide-y divide-[#E8E8E6]">
              {sidebarFiltered.map((emp) => {
                const calc = computedMap.get(emp.id)!;
                const isActive = emp.id === activeEmployeeId;
                const isAnomaly = emp.status === 'anomaly';

                return (
                  <div
                    key={emp.id}
                    onClick={() => {
                      setActiveEmployeeId(emp.id);
                      triggerCalcFlash();
                    }}
                    className={`p-3.5 cursor-pointer transition flex items-center justify-between ${
                      isActive ? 'bg-[#E4E4E7]' : 'hover:bg-[#F4F4F5]'
                    } ${isAnomaly ? 'border-l-4 border-l-[#253e87]' : ''}`}
                  >
                    <div className="space-y-0.5 overflow-hidden">
                      <div className="flex items-center gap-1.5 font-bold text-xs text-[#1F1F1E]">
                        {isAnomaly && <AlertTriangle className="w-3 h-3 text-[#253e87] shrink-0" />}
                        {emp.status === 'pending' && (
                          <span
                            className="w-2.5 h-2.5 rounded-full border border-gray-400 bg-white inline-block shrink-0"
                            title="⚪ En attente de saisie"
                          />
                        )}
                        <span className="truncate">{emp.name}</span>
                      </div>
                      <p className="text-[11px] text-[#6B6B6B] truncate font-bold">
                        {emp.dept} • {emp.days}j{emp.seniority ? ` • Anc. ${emp.seniority}` : ''}
                      </p>
                    </div>
                    <div className="text-right font-montserrat text-xs font-bold text-[#1F1F1E]">
                      {calc.net.toLocaleString('fr-FR')}
                    </div>
                  </div>
                );
              })}
            </div>
          </aside>

          {/* CENTRE : LE BULLETIN DE PAIE ÉDITABLE EN DIRECT */}
          <section className="flex-1 overflow-y-auto bg-white flex flex-col items-center p-6 md:p-10">
            {activeEmployee && activeCalc && (
              <div className="max-w-2xl w-full space-y-6 pb-20">
                {/* ENTÊTE BULLETIN */}
                <div className="border border-[#E8E8E6] rounded-xl p-6 bg-[#FAFAFA] space-y-4 shadow-2xs">
                  <div className="flex justify-between items-start">
                    <div>
                      <h3 id="bulletin-name" className="text-lg font-bold text-[#1F1F1E]">
                        {activeEmployee.name}
                      </h3>
                      <p id="bulletin-meta" className="text-xs text-[#6B6B6B] mt-0.5 font-bold">
                        {activeEmployee.dept} — Matricule {activeEmployee.matricule} •{' '}
                        {activeEmployee.maritalStatus === 'married' ? 'Marié(e)' : 'Célibataire'} (
                        {activeEmployee.parts ?? 1} part{(activeEmployee.parts ?? 1) > 1 ? 's' : ''})
                        {activeEmployee.seniority && (
                          <> • Ancienneté : <span className="text-[#1F1F1E] font-bold">{activeEmployee.seniority}</span> (seuil 2 ans non atteint : pas de prime)</>
                        )}
                      </p>
                    </div>
                    {activeEmployee.status === 'anomaly' && activeEmployee.alert && (
                      <div
                        id="bulletin-alert-badge"
                        className="text-xs bg-[#253e87] text-white px-2.5 py-1 rounded-md font-mono font-bold flex items-center gap-1"
                      >
                        <AlertTriangle className="w-3.5 h-3.5" />
                        <span>{activeEmployee.alert}</span>
                      </div>
                    )}
                    {activeEmployee.status === 'pending' && (
                      <div
                        id="bulletin-pending-badge"
                        className="text-xs bg-gray-100 border border-gray-300 text-gray-700 px-2.5 py-1 rounded-md font-bold flex items-center gap-1"
                      >
                        <span>⚪ En attente de saisie des variables</span>
                      </div>
                    )}
                  </div>
                </div>

                {/* SURFACE D'ÉDITION DU BULLETIN (LE BULLETIN EST L'ÉDITEUR) */}
                <div className="border border-[#E8E8E6] rounded-2xl p-6 bg-white space-y-5 shadow-xs relative">
                  <div className="flex justify-between items-center border-b border-[#E8E8E6] pb-3 text-xs uppercase font-mono text-[#6B6B6B] font-bold">
                    <span>Éléments de paie & calculs</span>
                    <span>Montant (FCFA)</span>
                  </div>

                  <div id="bulletin-lines" className="space-y-3">
                    {/* Ligne Salaire de Base & Jours */}
                    <div className="flex items-center justify-between py-2.5 border-b border-[#E8E8E6] text-xs font-montserrat font-bold">
                      <div className="flex items-center gap-2 text-[#1F1F1E]">
                        <span>Salaire de base</span>
                        <div className="flex items-center gap-1 bg-[#FAFAFA] border border-[#E8E8E6] rounded px-1.5 py-0.5">
                          <input
                            type="number"
                            min="0"
                            max="30"
                            disabled={isReadOnly}
                            value={activeEmployee.days}
                            onChange={(e) => handleUpdateDays(e.target.value)}
                            className="w-8 text-center bg-transparent font-montserrat focus:outline-none text-[#1F1F1E] font-bold"
                          />
                          <span className="text-[#6B6B6B]">j</span>
                        </div>
                        {activeEmployee.days < 30 && (
                          <span className="text-[10px] text-[#253e87] font-bold">
                            (Prorata: {activeCalc.proratedBase.toLocaleString('fr-FR')} F)
                          </span>
                        )}
                      </div>
                      <div className="flex items-center gap-2">
                        <FormattedAmountInput
                          disabled={isReadOnly}
                          value={activeEmployee.base}
                          onChange={(newBase) => handleUpdateBase(newBase)}
                          currencySuffix="F"
                          className="w-32 text-right bg-[#FAFAFA] border border-[#E8E8E6] rounded px-2 py-1 font-montserrat focus:outline-none focus:border-[#253e87] text-[#1F1F1E] font-bold"
                        />
                      </div>
                    </div>

                    {/* Lignes Primes, Retenues & Prêts */}
                    {activeEmployee.elements.map((el, index) => (
                      <div
                        key={el.id}
                        className="flex items-center justify-between py-2.5 border-b border-[#E8E8E6] text-xs font-montserrat font-bold group"
                      >
                        <div className="flex items-center gap-2 text-[#1F1F1E]">
                          <span>{el.label}</span>
                          <span
                            className={`text-[10px] px-1.5 py-0.5 rounded font-bold ${
                              el.gain ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'
                            }`}
                          >
                            [{el.gain ? 'GAIN' : 'RETENUE'}]
                          </span>
                          {el.type === 'transport' && (
                            <span className="text-[10px] text-[#253e87] font-bold">(Exonéré 30k)</span>
                          )}
                        </div>
                        <div className="flex items-center gap-2">
                          <FormattedAmountInput
                            disabled={isReadOnly || el.locked}
                            value={el.amount}
                            onChange={(newAmt) => handleUpdateElementAmount(index, newAmt)}
                            currencySuffix="F"
                            className="w-28 text-right bg-[#FAFAFA] border border-[#E8E8E6] rounded px-2 py-1 font-montserrat focus:outline-none focus:border-[#253e87] text-[#1F1F1E] font-bold disabled:bg-gray-100"
                          />
                          {el.locked ? (
                            <button
                              type="button"
                              onClick={() => setIsLoanModalOpen(true)}
                              className="text-[#253e87] font-bold hover:underline cursor-pointer text-xs"
                              title="Échéancier prêt"
                            >
                              🔗 {el.schedule}
                            </button>
                          ) : (
                            !isReadOnly && (
                              <button
                                type="button"
                                onClick={() => handleRemoveElement(index)}
                                className="text-[#6B6B6B] hover:text-red-600 px-1 font-bold cursor-pointer"
                                title="Supprimer"
                              >
                                ✕
                              </button>
                            )
                          )}
                        </div>
                      </div>
                    ))}
                  </div>

                  {/* AJOUT D'UN ÉLÉMENT (CATALOGUE DÉROULANT GROUPÉ) */}
                  {!isReadOnly && (
                    <div id="add-element-container" className="pt-3 border-t border-[#E8E8E6]">
                      <select
                        id="catalog-select"
                        onChange={handleAddFromCatalog}
                        defaultValue=""
                        className="w-full border border-[#E8E8E6] rounded-xl p-3 text-xs font-bold bg-[#FAFAFA] text-[#1F1F1E] focus:outline-none focus:border-[#253e87] cursor-pointer"
                      >
                        <option value="" disabled>
                          + Insérer un élément ▾
                        </option>
                        <optgroup label="Primes & Indemnités">
                          <option value="transport">Prime de transport (30 000 FCFA)</option>
                          <option value="sursalaire">Sursalaire (50 000 FCFA)</option>
                          <option value="logement">Prime de logement (40 000 FCFA)</option>
                          <option value="representation">Prime de représentation (35 000 FCFA)</option>
                          <option value="performance">Prime de performance (45 000 FCFA)</option>
                          <option value="gratification">Gratification (60 000 FCFA)</option>
                        </optgroup>
                        <optgroup label="Avantages en Nature">
                          <option value="av_vehicule">Avantage Véhicule (30 000 FCFA)</option>
                          <option value="av_logement">Avantage Logement (30 000 FCFA)</option>
                          <option value="av_telephone">Avantage Téléphone (30 000 FCFA)</option>
                        </optgroup>
                        <optgroup label="Retenues & Prélèvements">
                          <option value="avance">Avance sur salaire (50 000 FCFA)</option>
                          <option value="retenue">Retenue diverse (15 000 FCFA)</option>
                          <option value="cantine">Participation Cantine (10 000 FCFA)</option>
                        </optgroup>
                      </select>
                    </div>
                  )}

                  {/* TOTAUX & CALCULS LÉGAUX CÔTE D'IVOIRE */}
                  <div className="border-t border-[#E8E8E6] pt-4 space-y-2 text-xs font-montserrat font-bold">
                    <div className="flex justify-between text-[#6B6B6B]">
                      <span className="font-sans">Cotisations sociales (CNPS 6,3% + CMU)</span>
                      <span id="calc-cotis" className="text-[#1F1F1E] font-montserrat">
                        –{activeCalc.totalCotis.toLocaleString('fr-FR')} FCFA
                      </span>
                    </div>
                    <div className="flex justify-between text-[#6B6B6B]">
                      <span className="font-sans">Impôt sur le salaire (ITS 2024 après RICF)</span>
                      <span id="calc-impot" className="text-[#1F1F1E] font-montserrat">
                        –{activeCalc.itsNet.toLocaleString('fr-FR')} FCFA
                      </span>
                    </div>
                    {activeCalc.totalRetenues > 0 && (
                      <div className="flex justify-between text-[#6B6B6B]">
                        <span className="font-sans">Prêts & Retenues</span>
                        <span className="text-[#1F1F1E] font-montserrat">
                          –{activeCalc.totalRetenues.toLocaleString('fr-FR')} FCFA
                        </span>
                      </div>
                    )}
                  </div>

                  {/* BANDEAU NET À PAYER DU BULLETIN */}
                  <div className="border-t-2 border-[#253e87] pt-4 flex justify-between items-center bg-[#FAFAFA] -mx-6 -mb-6 p-6 rounded-b-2xl">
                    <div>
                      <span className="text-xs uppercase tracking-wider text-[#6B6B6B] font-bold">
                        NET À PAYER
                      </span>
                      <div className="text-2xl font-bold font-montserrat tracking-tight text-[#253e87] flex items-center gap-2">
                        <span id="calc-net">{activeCalc.net.toLocaleString('fr-FR')}</span>{' '}
                        <span className="text-sm font-normal text-[#1F1F1E]">FCFA</span>
                      </div>
                    </div>
                    {!isReadOnly && (
                      <button
                        type="button"
                        onClick={() => {
                          setTargetNetInput(activeCalc.net);
                          setIsTargetModalOpen(true);
                        }}
                        className="px-4 py-2.5 rounded-lg border border-[#E8E8E6] bg-white text-[#1F1F1E] hover:bg-[#F4F4F5] text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shadow-2xs"
                        title="Rétro-calcul cible net"
                      >
                        <Calculator className="w-3.5 h-3.5 text-[#253e87]" />
                        <span>🎯 Cible Net</span>
                      </button>
                    )}
                  </div>
                </div>

                {/* NAVIGATION & ACTIONS DU BULLETIN */}
                <div className="flex flex-col sm:flex-row justify-between items-center gap-3 pt-2">
                  <div className="flex items-center gap-2 w-full sm:w-auto">
                    <button
                      type="button"
                      onClick={() => navigateEmployee(-1)}
                      className="px-3 py-2 rounded-lg border border-[#E8E8E6] bg-white hover:bg-[#F4F4F5] text-xs font-bold text-[#1F1F1E] flex items-center gap-1 cursor-pointer"
                    >
                      <ChevronLeft className="w-4 h-4" />
                      <span>Préc.</span>
                    </button>
                    <button
                      type="button"
                      onClick={() => setIsPdfModalOpen(true)}
                      className="px-4 py-2 rounded-lg border border-[#E8E8E6] bg-white hover:bg-[#F4F4F5] text-xs font-bold text-[#1F1F1E] flex items-center gap-1.5 cursor-pointer"
                    >
                      <FileText className="w-3.5 h-3.5 text-[#253e87]" />
                      <span>Générer le PDF de ce salarié</span>
                    </button>
                    <button
                      type="button"
                      onClick={() => navigateEmployee(1)}
                      className="px-3 py-2 rounded-lg border border-[#E8E8E6] bg-white hover:bg-[#F4F4F5] text-xs font-bold text-[#1F1F1E] flex items-center gap-1 cursor-pointer"
                    >
                      <span>Suiv.</span>
                      <ChevronRight className="w-4 h-4" />
                    </button>
                  </div>

                  <button
                    type="button"
                    disabled={isReadOnly}
                    onClick={onOpenValidationModal}
                    className="py-3 px-6 bg-[#253e87] text-white hover:bg-[#1c306d] text-xs font-bold rounded-lg transition disabled:opacity-50 cursor-pointer shadow-xs w-full sm:w-auto"
                  >
                    {isReadOnly ? 'PÉRIODE VERROUILLÉE' : 'VALIDER LE MOIS ET GÉNÉRER TOUS LES BULLETINS'}
                  </button>
                </div>
              </div>
            )}
          </section>
        </div>
      )}

      {/* MODALE ÉCHÉANCIER PRÊT 🔗 */}
      {isLoanModalOpen && (
        <div className="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
          <div className="bg-white border border-[#E8E8E6] rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
            <div className="flex justify-between items-center border-b border-[#E8E8E6] pb-3">
              <h4 className="font-bold text-sm uppercase text-[#1F1F1E]">Échéancier Prêt Véhicule</h4>
              <button
                type="button"
                onClick={() => setIsLoanModalOpen(false)}
                className="font-bold text-sm text-[#6B6B6B] hover:text-[#1F1F1E] p-1"
              >
                ✕
              </button>
            </div>
            <div className="space-y-2 text-xs font-mono">
              <p className="text-[#6B6B6B] font-bold">Remboursement en cours : 50 000 FCFA / mois</p>
              <div className="border border-[#E8E8E6] rounded-lg divide-y divide-[#E8E8E6]">
                {[1, 2, 3, 4, 5, 6].map((m) => {
                  const isPaidOrCurrent = m <= 3;
                  return (
                    <div key={m} className="p-2.5 flex justify-between items-center text-xs">
                      <span className="font-bold text-[#1F1F1E]">Mois {m}/6 — {currentPeriod.name}</span>
                      <span className={isPaidOrCurrent ? 'font-bold text-[#253e87]' : 'text-[#6B6B6B]'}>
                        {isPaidOrCurrent ? '50 000 FCFA (Échu / En cours)' : '50 000 FCFA (À venir)'}
                      </span>
                    </div>
                  );
                })}
              </div>
            </div>
            <div className="flex justify-end pt-2">
              <button
                type="button"
                onClick={() => setIsLoanModalOpen(false)}
                className="px-4 py-2 bg-[#253e87] text-white hover:bg-[#1c306d] text-xs font-bold rounded-lg cursor-pointer"
              >
                Fermer
              </button>
            </div>
          </div>
        </div>
      )}

      {/* MODALE CIBLE NET 🎯 */}
      {isTargetModalOpen && (
        <div className="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
          <div className="bg-white border border-[#E8E8E6] rounded-2xl max-w-sm w-full p-6 space-y-4 shadow-2xl text-xs font-mono font-bold">
            <div className="flex justify-between items-center border-b border-[#E8E8E6] pb-2 text-sm text-[#253e87]">
              <span>🎯 Rétro-calcul Cible Net</span>
              <button
                type="button"
                onClick={() => setIsTargetModalOpen(false)}
                className="text-[#6B6B6B] hover:text-[#1F1F1E] p-1"
              >
                ✕
              </button>
            </div>
            <div className="space-y-3">
              <p className="text-[#6B6B6B]">Entrez le salaire Net à payer souhaité (en FCFA) :</p>
              <FormattedAmountInput
                value={targetNetInput}
                onChange={(val) => setTargetNetInput(val)}
                autoFocus
                currencySuffix="FCFA"
                className="w-full border border-[#E8E8E6] rounded-lg p-2.5 font-montserrat text-sm bg-[#FAFAFA] text-[#1F1F1E] focus:outline-none focus:border-[#253e87]"
              />
              <p className="text-[11px] text-[#6B6B6B] font-normal leading-relaxed">
                Le moteur calculera exactement le sursalaire à ajouter en tenant compte de la CNPS (6,3%), de la CMU (500 F) et des tranches de l'ITS 2024.
              </p>
            </div>
            <div className="flex justify-end gap-2 pt-2">
              <button
                type="button"
                onClick={() => setIsTargetModalOpen(false)}
                className="px-3 py-2 border border-[#E8E8E6] rounded-lg text-xs font-bold hover:bg-[#F4F4F5] text-[#1F1F1E] cursor-pointer"
              >
                Annuler
              </button>
              <button
                type="button"
                onClick={handleConfirmTargetNet}
                className="px-4 py-2 bg-[#253e87] text-white hover:bg-[#1c306d] text-xs font-bold rounded-lg cursor-pointer"
              >
                Appliquer
              </button>
            </div>
          </div>
        </div>
      )}

      {/* MODALE PDF OFFICIEL */}
      {isPdfModalOpen && activeEmployee && activeCalc && (
        <PayslipModal
          employee={activeEmployee}
          computedPay={activeCalc}
          period={currentPeriod}
          isOpen={isPdfModalOpen}
          onClose={() => setIsPdfModalOpen(false)}
          onShowToast={onShowToast}
        />
      )}
    </div>
  );
};
