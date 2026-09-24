import React, { useState } from 'react';
import { Employee } from '../data/employeeTypes';
import { reverseCalculateNetToSurSalary } from '../logic/payrollCalculator';
import { FormattedAmountInput } from './FormattedAmountInput';
import {
  Gift,
  CalendarDays,
  Calculator,
  Download,
  Trash2,
  X,
  Sparkles,
} from 'lucide-react';

interface MassProcessingToolbarProps {
  selectedIds: number[];
  employees: Employee[];
  isReadOnly: boolean;
  onClearSelection: () => void;
  onUpdateEmployees: (updated: Employee[]) => void;
  onShowToast: (msg: string) => void;
}

export const MassProcessingToolbar: React.FC<MassProcessingToolbarProps> = ({
  selectedIds,
  employees,
  isReadOnly,
  onClearSelection,
  onUpdateEmployees,
  onShowToast,
}) => {
  // Modal states for bulk operations
  const [modalType, setModalType] = useState<'prime' | 'days' | 'targetNet' | null>(null);

  // Form states
  const [primeName, setPrimeName] = useState<string>('Prime de performance');
  const [primeAmount, setPrimeAmount] = useState<number>(30000);
  const [primeType, setPrimeType] = useState<string>('performance');
  const [daysAmount, setDaysAmount] = useState<number>(30);
  const [targetNetAmount, setTargetNetAmount] = useState<number>(500000);

  if (selectedIds.length === 0) return null;

  const count = selectedIds.length;

  // 1. Bulk Apply Prime
  const handleConfirmPrime = (e: React.FormEvent) => {
    e.preventDefault();
    if (primeAmount <= 0) {
      onShowToast('Veuillez saisir un montant valide.');
      return;
    }

    const updated = employees.map((emp) => {
      if (selectedIds.includes(emp.id)) {
        return {
          ...emp,
          elements: [
            ...emp.elements,
            {
              id: `bulk-${Date.now()}-${emp.id}`,
              type: primeType,
              label: primeName,
              amount: primeAmount,
              gain: true,
              imposable: primeType !== 'transport',
              social: true,
            },
          ],
        };
      }
      return emp;
    });

    onUpdateEmployees(updated);
    onShowToast(`🎁 Prime "${primeName}" (+${primeAmount.toLocaleString('fr-FR')} F) appliquée à ${count} salarié${count > 1 ? 's' : ''}.`);
    setModalType(null);
    onClearSelection();
  };

  // 2. Bulk Modify Days
  const handleConfirmDays = (e: React.FormEvent) => {
    e.preventDefault();
    if (daysAmount < 1 || daysAmount > 30) {
      onShowToast('Nombre de jours doit être entre 1 et 30.');
      return;
    }

    const updated = employees.map((emp) => {
      if (selectedIds.includes(emp.id)) {
        const isFull = daysAmount === 30;
        return {
          ...emp,
          days: daysAmount,
          status: (isFull && emp.alert?.includes('Pointeuse') ? 'ok' : emp.status) as 'ok' | 'anomaly',
          alert: isFull && emp.alert?.includes('Pointeuse') ? null : emp.alert,
        };
      }
      return emp;
    });

    onUpdateEmployees(updated);
    onShowToast(`📅 Jours de travail modifiés à ${daysAmount}j pour ${count} salarié${count > 1 ? 's' : ''}.`);
    setModalType(null);
    onClearSelection();
  };

  // 3. Bulk Target Net (Rétro-calcul en masse)
  const handleConfirmTargetNet = (e: React.FormEvent) => {
    e.preventDefault();
    if (targetNetAmount <= 0) {
      onShowToast('Veuillez saisir un salaire net valide.');
      return;
    }

    const updated = employees.map((emp) => {
      if (selectedIds.includes(emp.id)) {
        const neededSurSalary = reverseCalculateNetToSurSalary(emp, targetNetAmount);
        const existingIdx = emp.elements.findIndex((el) => el.type === 'sursalaire');
        let newElements = [...emp.elements];
        if (existingIdx >= 0) {
          newElements[existingIdx] = {
            ...newElements[existingIdx],
            amount: neededSurSalary,
          };
        } else {
          newElements.push({
            id: `sursalaire-${Date.now()}-${emp.id}`,
            type: 'sursalaire',
            label: "Sursalaire d'ajustement",
            amount: neededSurSalary,
            gain: true,
            imposable: true,
            social: true,
          });
        }
        return {
          ...emp,
          elements: newElements,
        };
      }
      return emp;
    });

    onUpdateEmployees(updated);
    onShowToast(`🎯 Net cible de ${targetNetAmount.toLocaleString('fr-FR')} F appliqué avec rétro-calcul sur ${count} salarié${count > 1 ? 's' : ''}.`);
    setModalType(null);
    onClearSelection();
  };

  // 4. Bulk Recalculate (force state refresh)
  const handleBulkRecalculate = () => {
    onUpdateEmployees([...employees]);
    onShowToast(`⚡ Recalcul complet effectué sur la sélection (${count} salarié${count > 1 ? 's' : ''}).`);
  };

  // 5. Bulk Export CSV
  const handleBulkExport = () => {
    const selectedEmployees = employees.filter((e) => selectedIds.includes(e.id));
    let csv = 'Matricule;Nom;Departement;Jours;Base_FCFA;Statut\n';
    for (const emp of selectedEmployees) {
      csv += `${emp.matricule};"${emp.name}";${emp.dept};${emp.days};${emp.base};${emp.status}\n`;
    }
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `export-salaries-selection-${Date.now()}.csv`;
    link.click();
    URL.revokeObjectURL(url);
    onShowToast(`📥 Export CSV généré pour ${count} salarié${count > 1 ? 's' : ''}.`);
  };

  // 6. Bulk Delete (Suppression / Retrait de la sélection)
  const handleBulkDelete = () => {
    if (window.confirm(`Confirmez-vous le retrait de ${count} salarié${count > 1 ? 's' : ''} de la période active ?`)) {
      const remaining = employees.filter((e) => !selectedIds.includes(e.id));
      if (remaining.length === 0) {
        onShowToast('Impossible de vider complètement la paie (au moins 1 salarié requis).');
        return;
      }
      onUpdateEmployees(remaining);
      onClearSelection();
      onShowToast(`🗑️ ${count} salarié${count > 1 ? 's' : ''} retiré${count > 1 ? 's' : ''}.`);
    }
  };

  return (
    <>
      <div
        id="bulk-toolbar"
        className="bg-[#253e87] text-white px-5 py-3.5 rounded-xl mb-4 flex flex-col lg:flex-row justify-between items-center shadow-lg gap-3 font-montserrat text-xs uppercase tracking-wider animate-in fade-in duration-150"
      >
        <div className="flex items-center gap-3">
          <span id="bulk-count" className="bg-white/20 px-3 py-1 rounded-md font-bold text-sm">
            {count} sélectionné{count > 1 ? 's' : ''}
          </span>
          <span className="font-semibold text-white/90 hidden sm:inline">Actions de masse :</span>
        </div>

        <div className="flex items-center gap-2 flex-wrap justify-end">
          {/* 1. Appliquer une prime */}
          <button
            type="button"
            disabled={isReadOnly}
            onClick={() => setModalType('prime')}
            className="bg-white text-[#253e87] hover:bg-neutral-100 px-3 py-2 rounded-lg font-bold flex items-center gap-1.5 transition cursor-pointer shadow-xs disabled:opacity-50"
            title="Ajouter une prime ou indemnité à tous les salariés sélectionnés"
          >
            <Gift className="w-3.5 h-3.5 text-[#253e87]" />
            <span>🎁 Prime...</span>
          </button>

          {/* 2. Modifier les jours */}
          <button
            type="button"
            disabled={isReadOnly}
            onClick={() => setModalType('days')}
            className="bg-white text-[#253e87] hover:bg-neutral-100 px-3 py-2 rounded-lg font-bold flex items-center gap-1.5 transition cursor-pointer shadow-xs disabled:opacity-50"
            title="Ajuster les jours travaillés (prorata automatique)"
          >
            <CalendarDays className="w-3.5 h-3.5 text-[#253e87]" />
            <span>📅 Jours...</span>
          </button>

          {/* 3. Définir un Net Cible */}
          <button
            type="button"
            disabled={isReadOnly}
            onClick={() => setModalType('targetNet')}
            className="bg-white text-[#253e87] hover:bg-neutral-100 px-3 py-2 rounded-lg font-bold flex items-center gap-1.5 transition cursor-pointer shadow-xs disabled:opacity-50"
            title="Appliquer un salaire Net cible via le rétro-calcul automatique"
          >
            <Calculator className="w-3.5 h-3.5 text-[#253e87]" />
            <span>🎯 Net Cible...</span>
          </button>

          {/* 4. Recalculer en masse */}
          <button
            type="button"
            onClick={handleBulkRecalculate}
            className="bg-white/15 text-white hover:bg-white/25 px-3 py-2 rounded-lg font-bold flex items-center gap-1.5 transition cursor-pointer"
            title="Forcer le recalcul immédiat de tous les bulletins de la sélection"
          >
            <Sparkles className="w-3.5 h-3.5" />
            <span className="hidden sm:inline">Recalculer</span>
          </button>

          {/* 5. Exporter la sélection */}
          <button
            type="button"
            onClick={handleBulkExport}
            className="bg-white/15 text-white hover:bg-white/25 px-3 py-2 rounded-lg font-bold flex items-center gap-1.5 transition cursor-pointer"
            title="Exporter la sélection en CSV"
          >
            <Download className="w-3.5 h-3.5" />
            <span className="hidden sm:inline">Export</span>
          </button>

          {/* 6. Supprimer de la période */}
          {!isReadOnly && (
            <button
              type="button"
              onClick={handleBulkDelete}
              className="bg-red-500/80 hover:bg-red-600 text-white px-3 py-2 rounded-lg font-bold flex items-center gap-1.5 transition cursor-pointer"
              title="Retirer les salariés sélectionnés de la période"
            >
              <Trash2 className="w-3.5 h-3.5" />
              <span className="hidden sm:inline">Supprimer</span>
            </button>
          )}

          {/* 7. Désélectionner */}
          <button
            type="button"
            onClick={onClearSelection}
            className="bg-white/10 hover:bg-white/20 text-white px-2.5 py-2 rounded-lg font-bold flex items-center gap-1 transition cursor-pointer"
            title="Tout désélectionner"
          >
            <X className="w-3.5 h-3.5" />
          </button>
        </div>
      </div>

      {/* Modal 1: Prime en masse */}
      {modalType === 'prime' && (
        <div className="fixed inset-0 bg-black/40 z-60 flex items-center justify-center p-4">
          <div className="bg-white border border-[#E8E8E6] rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl font-montserrat">
            <div className="flex justify-between items-center border-b border-[#E8E8E6] pb-3">
              <h3 className="font-bold text-sm text-[#253e87] uppercase flex items-center gap-2">
                <Gift className="w-4 h-4" />
                Appliquer une prime ({count} salarié{count > 1 ? 's' : ''})
              </h3>
              <button
                type="button"
                onClick={() => setModalType(null)}
                className="text-[#6B6B6B] hover:text-[#1F1F1E] p-1 cursor-pointer"
              >
                <X className="w-4 h-4" />
              </button>
            </div>

            <form onSubmit={handleConfirmPrime} className="space-y-4 text-xs font-semibold">
              <div>
                <label className="block text-[#6B6B6B] mb-1">Type de prime / Élément :</label>
                <select
                  value={primeType}
                  onChange={(e) => {
                    setPrimeType(e.target.value);
                    if (e.target.value === 'performance') setPrimeName('Prime de performance');
                    else if (e.target.value === 'logement') setPrimeName('Prime de logement');
                    else if (e.target.value === 'transport') setPrimeName('Prime de transport');
                    else if (e.target.value === 'gratification') setPrimeName('Prime exceptionnelle');
                  }}
                  className="w-full border border-[#E8E8E6] rounded-lg p-2.5 bg-[#FAFAFA] text-[#1F1F1E] font-semibold focus:outline-none focus:border-[#253e87]"
                >
                  <option value="performance">Prime de performance</option>
                  <option value="logement">Prime de logement</option>
                  <option value="gratification">Prime exceptionnelle / Gratification</option>
                  <option value="transport">Prime de transport (exonérée max 30k)</option>
                </select>
              </div>

              <div>
                <label className="block text-[#6B6B6B] mb-1">Intitulé personnalisé :</label>
                <input
                  type="text"
                  value={primeName}
                  onChange={(e) => setPrimeName(e.target.value)}
                  className="w-full border border-[#E8E8E6] rounded-lg p-2.5 bg-[#FAFAFA] text-[#1F1F1E] font-semibold focus:outline-none focus:border-[#253e87]"
                />
              </div>

              <div>
                <label className="block text-[#6B6B6B] mb-1">Montant à attribuer (FCFA) :</label>
                <FormattedAmountInput
                  value={primeAmount}
                  onChange={setPrimeAmount}
                  currencySuffix="FCFA"
                  className="w-full border border-[#E8E8E6] rounded-lg p-2.5 bg-[#FAFAFA] text-[#1F1F1E] text-sm font-bold focus:outline-none focus:border-[#253e87]"
                />
              </div>

              <div className="flex gap-2 pt-2">
                <button
                  type="submit"
                  className="flex-1 py-2.5 bg-[#253e87] text-white hover:bg-[#1c306d] font-bold rounded-lg uppercase cursor-pointer"
                >
                  Appliquer la prime
                </button>
                <button
                  type="button"
                  onClick={() => setModalType(null)}
                  className="py-2.5 px-4 bg-neutral-200 text-[#1F1F1E] hover:bg-neutral-300 font-bold rounded-lg uppercase cursor-pointer"
                >
                  Annuler
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* Modal 2: Jours en masse */}
      {modalType === 'days' && (
        <div className="fixed inset-0 bg-black/40 z-60 flex items-center justify-center p-4">
          <div className="bg-white border border-[#E8E8E6] rounded-2xl max-w-sm w-full p-6 space-y-4 shadow-2xl font-montserrat">
            <div className="flex justify-between items-center border-b border-[#E8E8E6] pb-3">
              <h3 className="font-bold text-sm text-[#253e87] uppercase flex items-center gap-2">
                <CalendarDays className="w-4 h-4" />
                Modifier jours ({count} salarié{count > 1 ? 's' : ''})
              </h3>
              <button
                type="button"
                onClick={() => setModalType(null)}
                className="text-[#6B6B6B] hover:text-[#1F1F1E] p-1 cursor-pointer"
              >
                <X className="w-4 h-4" />
              </button>
            </div>

            <form onSubmit={handleConfirmDays} className="space-y-4 text-xs font-semibold">
              <div>
                <label className="block text-[#6B6B6B] mb-1">Jours travaillés (base calendaire 30j) :</label>
                <div className="flex items-center gap-2">
                  <input
                    type="number"
                    min="1"
                    max="30"
                    autoFocus
                    value={daysAmount}
                    onChange={(e) => setDaysAmount(Math.max(1, Math.min(30, parseInt(e.target.value, 10) || 0)))}
                    className="w-full border border-[#E8E8E6] rounded-lg p-2.5 bg-[#FAFAFA] text-center font-bold text-base text-[#1F1F1E] focus:outline-none focus:border-[#253e87]"
                  />
                  <span className="text-[#6B6B6B]">jours</span>
                </div>
              </div>

              <p className="text-[11px] text-[#6B6B6B] leading-relaxed">
                Le salaire de base et la prime de transport seront automatiquement proratisés selon la formule légale (ex: 22j/30j).
              </p>

              <div className="flex gap-2 pt-2">
                <button
                  type="submit"
                  className="flex-1 py-2.5 bg-[#253e87] text-white hover:bg-[#1c306d] font-bold rounded-lg uppercase cursor-pointer"
                >
                  Valider
                </button>
                <button
                  type="button"
                  onClick={() => setModalType(null)}
                  className="py-2.5 px-4 bg-neutral-200 text-[#1F1F1E] hover:bg-neutral-300 font-bold rounded-lg uppercase cursor-pointer"
                >
                  Annuler
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* Modal 3: Net Cible en masse */}
      {modalType === 'targetNet' && (
        <div className="fixed inset-0 bg-black/40 z-60 flex items-center justify-center p-4">
          <div className="bg-white border border-[#E8E8E6] rounded-2xl max-w-sm w-full p-6 space-y-4 shadow-2xl font-montserrat">
            <div className="flex justify-between items-center border-b border-[#E8E8E6] pb-3">
              <h3 className="font-bold text-sm text-[#253e87] uppercase flex items-center gap-2">
                <Calculator className="w-4 h-4" />
                Rétro-calcul Net Cible en masse
              </h3>
              <button
                type="button"
                onClick={() => setModalType(null)}
                className="text-[#6B6B6B] hover:text-[#1F1F1E] p-1 cursor-pointer"
              >
                <X className="w-4 h-4" />
              </button>
            </div>

            <form onSubmit={handleConfirmTargetNet} className="space-y-4 text-xs font-semibold">
              <div>
                <label className="block text-[#6B6B6B] mb-1">
                  Salaire Net à Payer souhaité pour les {count} salarié{count > 1 ? 's' : ''} :
                </label>
                <FormattedAmountInput
                  value={targetNetAmount}
                  onChange={setTargetNetAmount}
                  currencySuffix="FCFA"
                  className="w-full border border-[#E8E8E6] rounded-lg p-2.5 bg-[#FAFAFA] text-base text-[#1F1F1E] font-bold focus:outline-none focus:border-[#253e87]"
                />
              </div>

              <p className="text-[11px] text-[#6B6B6B] leading-relaxed">
                Le système recalculera individuellement le sursalaire imposable de chaque salarié sélectionné pour garantir exactement ce montant net (en tenant compte de leurs parts fiscales respectives et du barème ITS 2024).
              </p>

              <div className="flex gap-2 pt-2">
                <button
                  type="submit"
                  className="flex-1 py-2.5 bg-[#253e87] text-white hover:bg-[#1c306d] font-bold rounded-lg uppercase cursor-pointer"
                >
                  Calculer & Appliquer
                </button>
                <button
                  type="button"
                  onClick={() => setModalType(null)}
                  className="py-2.5 px-4 bg-neutral-200 text-[#1F1F1E] hover:bg-neutral-300 font-bold rounded-lg uppercase cursor-pointer"
                >
                  Annuler
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </>
  );
};
