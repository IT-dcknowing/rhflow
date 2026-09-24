import React, { useState, useMemo } from 'react';
import { Employee, ComputedEmployeePay, Period } from '../../data/employeeTypes';
import { Model1EmployeeRow } from './Model1EmployeeRow';
import { Model1Drawer } from './Model1Drawer';
import { PayslipModal } from '../../components/PayslipModal';
import { MassProcessingToolbar } from '../../components/MassProcessingToolbar';
import {
  Search,
  SlidersHorizontal,
  RotateCcw,
  CheckCircle2,
  Building2,
  Lock,
  Unlock,
} from 'lucide-react';

interface Model1ViewProps {
  employees: Employee[];
  computedMap: Map<number, ComputedEmployeePay>;
  currentPeriod: Period;
  isReadOnly: boolean;
  onUpdateEmployee: (updated: Employee) => void;
  onUpdateEmployees: (updated: Employee[]) => void;
  onResetData: () => void;
  onOpenValidationModal: () => void;
  onUnlockPeriod: () => void;
  onShowToast: (msg: string) => void;
}

export const Model1View: React.FC<Model1ViewProps> = ({
  employees,
  computedMap,
  currentPeriod,
  isReadOnly,
  onUpdateEmployee,
  onUpdateEmployees,
  onResetData,
  onOpenValidationModal,
  onUnlockPeriod,
  onShowToast,
}) => {
  // Filters & State
  const [searchTerm, setSearchTerm] = useState<string>('');
  const [filterStatus, setFilterStatus] = useState<'all' | 'anomaly' | 'ok' | 'pending'>('all');
  const [isExpertMode, setIsExpertMode] = useState<boolean>(false);
  const [selectedIds, setSelectedIds] = useState<number[]>([]);

  // Drawer & Payslip Modal State
  const [activeEmployeeId, setActiveEmployeeId] = useState<number | null>(null);
  const [payslipEmployeeId, setPayslipEmployeeId] = useState<number | null>(null);

  const totalAnomalies = employees.filter((e) => e.status === 'anomaly').length;
  const totalPending = employees.filter((e) => e.status === 'pending').length;
  const totalOk = employees.filter((e) => e.status === 'ok').length;

  // Filtered employees
  const filteredEmployees = useMemo(() => {
    const term = searchTerm.trim().toLowerCase();
    return employees.filter((emp) => {
      const matchSearch =
        term === '' ||
        emp.name.toLowerCase().includes(term) ||
        emp.dept.toLowerCase().includes(term) ||
        emp.matricule.toLowerCase().includes(term);

      const matchFilter =
        filterStatus === 'all' ||
        (filterStatus === 'anomaly' && emp.status === 'anomaly') ||
        (filterStatus === 'ok' && emp.status === 'ok') ||
        (filterStatus === 'pending' && emp.status === 'pending');

      return matchSearch && matchFilter;
    });
  }, [employees, searchTerm, filterStatus]);

  // Grand totals
  const totals = useMemo(() => {
    let days = 0;
    let base = 0;
    let primes = 0;
    let cotis = 0;
    let impot = 0;
    let net = 0;

    for (const emp of filteredEmployees) {
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
  }, [filteredEmployees, computedMap]);

  // Bulk actions
  const handleSelectAll = (checked: boolean) => {
    if (checked) {
      setSelectedIds(filteredEmployees.map((e) => e.id));
    } else {
      setSelectedIds([]);
    }
  };

  const handleRowSelect = (id: number, checked: boolean) => {
    if (checked) {
      setSelectedIds((prev) => [...prev, id]);
    } else {
      setSelectedIds((prev) => prev.filter((item) => item !== id));
    }
  };

  const activeEmployee = employees.find((e) => e.id === activeEmployeeId) || null;
  const activeComputedPay = activeEmployee ? computedMap.get(activeEmployee.id) || null : null;

  const payslipEmployee = employees.find((e) => e.id === payslipEmployeeId) || null;
  const payslipComputedPay = payslipEmployee ? computedMap.get(payslipEmployee.id) || null : null;

  return (
    <div className="flex-1 flex flex-col w-full relative">
      {/* Barre de traitement de masse */}
      <MassProcessingToolbar
        selectedIds={selectedIds}
        employees={employees}
        isReadOnly={isReadOnly}
        onClearSelection={() => setSelectedIds([])}
        onUpdateEmployees={onUpdateEmployees}
        onShowToast={onShowToast}
      />

      {/* Barre d'outils : Recherche, Filtre anomalies, Mode Expert, Réinitialiser */}
      <div className="flex flex-col md:flex-row justify-between items-stretch md:items-center gap-4 mb-4">
        <div className="flex items-center gap-3 flex-1 max-w-xl">
          <div className="relative flex-1 flex items-center">
            <Search className="w-4 h-4 text-[#6B6B6B] absolute left-3.5 pointer-events-none" />
            <input
              id="search-input"
              type="text"
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              placeholder="Rechercher par nom, matricule, département..."
              className="w-full border border-[#E8E8E6] rounded-xl pl-10 pr-4 py-2.5 text-[14px] md:text-[15px] font-normal focus:outline-none focus:border-[#253e87] focus:ring-1 focus:ring-[#253e87] bg-[#FAFAFA] transition text-[#1F1F1E]"
            />
          </div>
          <select
            id="filter-status"
            value={filterStatus}
            onChange={(e) => setFilterStatus(e.target.value as 'all' | 'anomaly' | 'ok' | 'pending')}
            className="border border-[#E8E8E6] rounded-xl px-4 py-2.5 text-[13px] md:text-[14px] font-medium bg-white text-[#1F1F1E] focus:outline-none focus:border-[#253e87] cursor-pointer"
          >
            <option value="all">Filtre : Tous ({employees.length})</option>
            <option value="anomaly">Avec Anomalies ({totalAnomalies})</option>
            <option value="ok">Sans Anomalie ({totalOk})</option>
            <option value="pending">En attente ({totalPending})</option>
          </select>
        </div>

        <div className="flex items-center gap-3 text-[13px] md:text-[14px] font-semibold">
          <button
            id="expert-mode-btn"
            type="button"
            onClick={() => {
              setIsExpertMode(!isExpertMode);
              onShowToast(!isExpertMode ? 'Mode Expert activé' : 'Mode Expert désactivé');
            }}
            className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-[#E8E8E6] bg-[#F4F4F5] hover:bg-[#F0F0EE] text-[#1F1F1E] transition cursor-pointer"
          >
            <SlidersHorizontal className="w-4 h-4 text-[#253e87]" />
            <span>
              Mode Expert :{' '}
              <span id="expert-status" className={isExpertMode ? 'underline font-semibold text-[#253e87]' : ''}>
                {isExpertMode ? 'ON' : 'OFF'}
              </span>
            </span>
          </button>

          <button
            type="button"
            onClick={() => {
              setSelectedIds([]);
              setSearchTerm('');
              setFilterStatus('all');
              onResetData();
            }}
            className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-[#E8E8E6] bg-[#F4F4F5] hover:bg-[#F0F0EE] text-[#1F1F1E] transition cursor-pointer"
            title="Réinitialiser les données"
          >
            <RotateCcw className="w-4 h-4 text-[#6B6B6B]" />
            <span>Réinitialiser</span>
          </button>
        </div>
      </div>

      {/* Tableau des salariés (Vue Masse) */}
      <div className="border border-[#E8E8E6] rounded-2xl overflow-x-auto flex-1 bg-white shadow-sm mb-6">
        <table className="w-full text-left border-collapse text-[15px]">
          <thead>
            <tr className="border-b border-[#E8E8E6] bg-[#FAFAFA] text-[13px] md:text-[14px] text-[#1F1F1E] font-semibold tracking-[0.5px]">
              <th className="p-4 border-r border-[#E8E8E6] w-12 text-center">
                <input
                  type="checkbox"
                  id="select-all-checkbox"
                  checked={
                    filteredEmployees.length > 0 &&
                    filteredEmployees.every((e) => selectedIds.includes(e.id))
                  }
                  onChange={(e) => handleSelectAll(e.target.checked)}
                  className="w-4 h-4 rounded border-[#E8E8E6] text-[#253e87] focus:ring-[#253e87] cursor-pointer"
                />
              </th>
              <th className="p-4 border-r border-[#E8E8E6] w-60">SALARIÉ</th>
              <th className="p-4 border-r border-[#E8E8E6] w-36">DÉPARTEMENT</th>
              <th className="p-4 border-r border-[#E8E8E6] text-center w-28">JOURS</th>
              <th className="p-4 border-r border-[#E8E8E6] text-right w-40">BASE (FCFA)</th>
              <th className="p-4 border-r border-[#E8E8E6] text-right w-44">PRIMES (FCFA)</th>
              {isExpertMode && (
                <>
                  <th className="p-4 border-r border-[#E8E8E6] text-right w-36 expert-col text-neutral-600">
                    COTIS. (CI)
                  </th>
                  <th className="p-4 border-r border-[#E8E8E6] text-right w-36 expert-col text-neutral-600">
                    IMPÔT (ITS)
                  </th>
                </>
              )}
              <th className="p-4 text-right w-48">NET À PAYER</th>
            </tr>
          </thead>
          <tbody id="payroll-tbody" className="divide-y divide-[#E8E8E6] text-[15px] text-[#1F1F1E]">
            {filteredEmployees.length === 0 ? (
              <tr>
                <td colSpan={isExpertMode ? 9 : 7} className="p-8 text-center text-[#1F1F1E] font-medium">
                  Aucun salarié ne correspond à votre recherche.
                </td>
              </tr>
            ) : (
              filteredEmployees.map((emp) => {
                const calc = computedMap.get(emp.id)!;
                return (
                  <Model1EmployeeRow
                    key={emp.id}
                    employee={emp}
                    computedPay={calc}
                    isSelected={selectedIds.includes(emp.id)}
                    isExpertMode={isExpertMode}
                    onSelect={(checked) => handleRowSelect(emp.id, checked)}
                    onClick={() => setActiveEmployeeId(emp.id)}
                  />
                );
              })
            )}
          </tbody>
          <tfoot>
            <tr className="border-t-2 border-[#253e87] bg-[#FAFAFA] text-[14px] font-semibold text-[#1F1F1E]">
              <td className="p-4 border-r border-[#E8E8E6] text-center">-</td>
              <td className="p-4 border-r border-[#E8E8E6]">
                TOTAUX (<span id="total-count">{filteredEmployees.length}</span> salariés)
              </td>
              <td className="p-4 border-r border-[#E8E8E6]">-</td>
              <td id="total-days" className="p-4 border-r border-[#E8E8E6] text-center font-montserrat text-[15px]">
                {totals.days.toLocaleString('fr-FR')}
              </td>
              <td id="total-base" className="p-4 border-r border-[#E8E8E6] text-right font-montserrat text-[15px]">
                {totals.base.toLocaleString('fr-FR')}
              </td>
              <td id="total-primes" className="p-4 border-r border-[#E8E8E6] text-right font-montserrat text-[15px]">
                {totals.primes.toLocaleString('fr-FR')}
              </td>
              {isExpertMode && (
                <>
                  <td id="total-cotis" className="p-4 border-r border-[#E8E8E6] text-right font-montserrat text-[#1F1F1E] text-[15px]">
                    –{totals.cotis.toLocaleString('fr-FR')}
                  </td>
                  <td id="total-impot" className="p-4 border-r border-[#E8E8E6] text-right font-montserrat text-[#1F1F1E] text-[15px]">
                    –{totals.impot.toLocaleString('fr-FR')}
                  </td>
                </>
              )}
              <td id="total-net" className="p-4 text-right text-[16px] font-semibold font-montserrat text-[#253e87]">
                {totals.net.toLocaleString('fr-FR')} FCFA
              </td>
            </tr>
          </tfoot>
        </table>
      </div>

      {/* Bannière d'audit & actions de clôture (Raccourci en une seule ligne demandée) */}
      <div className="flex flex-col sm:flex-row justify-between items-center gap-4 bg-[#FAFAFA] border border-[#E8E8E6] rounded-2xl p-5 shadow-xs">
        <div id="validation-status-text" className="text-[13px] md:text-[14px] font-normal text-[#1F1F1E] flex items-center gap-2">
          <Building2 className="w-4 h-4 text-[#253e87] shrink-0" />
          <span>
            <strong className="font-semibold text-[#253e87]">
              {totalAnomalies} anomalie{totalAnomalies > 1 ? 's' : ''} détectée{totalAnomalies > 1 ? 's' : ''}
            </strong>{' '}
            — Cliquez sur une ligne pour ouvrir le détail
          </span>
        </div>

        <div className="flex items-center gap-3">
          <button
            id="main-validation-btn"
            type="button"
            disabled={isReadOnly}
            onClick={onOpenValidationModal}
            className="py-3 px-6 text-[14px] tracking-wider uppercase shadow-md flex items-center gap-2.5 bg-[#253e87] text-white hover:bg-[#1c306d] font-semibold rounded-xl transition disabled:opacity-50 cursor-pointer"
          >
            <CheckCircle2 className="w-4 h-4" />
            <span id="main-validation-btn-text">
              {isReadOnly
                ? 'PÉRIODE VALIDÉE & VERROUILLÉE'
                : `VALIDER & GÉNÉRER LES BULLETINS (${(totals.net / 1000000).toFixed(2)}M FCFA)`}
            </span>
          </button>

          {currentPeriod.status === 'validée' && (
            <button
              id="cancel-validation-btn"
              type="button"
              onClick={onUnlockPeriod}
              className="py-3 px-4 uppercase text-[12px] text-red-600 border border-red-300 rounded-xl hover:bg-red-50 font-semibold transition cursor-pointer flex items-center gap-1.5"
            >
              <Unlock className="w-3.5 h-3.5" />
              <span>Déverrouiller</span>
            </button>
          )}
        </div>
      </div>

      {/* Tiroir chirurgical latéral */}
      <Model1Drawer
        employee={activeEmployee}
        computedPay={activeComputedPay}
        isOpen={Boolean(activeEmployeeId)}
        isReadOnly={isReadOnly}
        onClose={() => setActiveEmployeeId(null)}
        onSave={(updated) => {
          onUpdateEmployee(updated);
          setActiveEmployeeId(null);
        }}
        onOpenPayslip={(emp) => setPayslipEmployeeId(emp.id)}
        onShowToast={onShowToast}
      />

      {/* Bulletin officiel de paie modal */}
      <PayslipModal
        employee={payslipEmployee}
        computedPay={payslipComputedPay}
        period={currentPeriod}
        isOpen={Boolean(payslipEmployeeId)}
        onClose={() => setPayslipEmployeeId(null)}
        onShowToast={onShowToast}
      />
    </div>
  );
};
