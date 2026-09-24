import React, { useState } from 'react';
import { Employee, EmployeeElement, ComputedEmployeePay } from '../types/rhflow';
import { reverseCalculateNetToSurSalary } from '../utils/rhflowCalculator';
import {
  X,
  Plus,
  Clock,
  Coins,
  Calculator,
  FileText,
  AlertTriangle,
  Lock,
  ChevronDown,
} from 'lucide-react';

interface EmployeeDrawerProps {
  employee: Employee | null;
  computedPay: ComputedEmployeePay | null;
  isOpen: boolean;
  isReadOnly: boolean;
  onClose: () => void;
  onSave: (updatedEmployee: Employee) => void;
  onOpenPayslip: (employee: Employee) => void;
  onShowToast: (msg: string) => void;
}

export const EmployeeDrawer: React.FC<EmployeeDrawerProps> = ({
  employee,
  computedPay,
  isOpen,
  isReadOnly,
  onClose,
  onSave,
  onOpenPayslip,
  onShowToast,
}) => {
  if (!isOpen || !employee || !computedPay) return null;

  // Local draft state for drawer editing
  const [days, setDays] = useState<number>(employee.days);
  const [base, setBase] = useState<number>(employee.base);
  const [elements, setElements] = useState<EmployeeElement[]>([...employee.elements]);
  const [showTargetModal, setShowTargetModal] = useState<boolean>(false);
  const [targetNetInput, setTargetNetInput] = useState<string>('');

  // Keep local draft in sync if employee prop changes
  React.useEffect(() => {
    setDays(employee.days);
    setBase(employee.base);
    setElements([...employee.elements]);
  }, [employee]);

  const handleUpdateElementAmount = (index: number, newAmount: string) => {
    const parsed = Math.max(0, parseInt(newAmount, 10) || 0);
    const updated = [...elements];
    updated[index] = { ...updated[index], amount: parsed };
    setElements(updated);
  };

  const handleRemoveElement = (index: number) => {
    if (elements[index]?.locked) {
      onShowToast('Impossible de supprimer un prêt en cours avec échéancier.');
      return;
    }
    const updated = elements.filter((_, i) => i !== index);
    setElements(updated);
    onShowToast('Élément retiré');
  };

  const handleAddElementCatalog = (e: React.ChangeEvent<HTMLSelectElement>) => {
    const val = e.target.value;
    if (!val) return;

    let newElem: EmployeeElement | null = null;
    const uid = `el-${Date.now()}`;

    if (val === 'prime_transport') {
      newElem = {
        id: uid,
        type: 'transport',
        label: 'Prime de transport',
        amount: 30000,
        gain: true,
        imposable: false,
        social: true,
      };
    } else if (val === 'prime_perf') {
      newElem = {
        id: uid,
        type: 'performance',
        label: 'Prime de performance',
        amount: 45000,
        gain: true,
        imposable: true,
        social: true,
      };
    } else if (val === 'prime_logement') {
      newElem = {
        id: uid,
        type: 'logement',
        label: 'Prime de logement',
        amount: 25000,
        gain: true,
        imposable: true,
        social: false,
      };
    } else if (val === 'prime_exceptionnelle') {
      const amtStr = window.prompt('Montant de la prime exceptionnelle en FCFA :', '50000');
      const amt = parseInt(amtStr || '50000', 10) || 50000;
      newElem = {
        id: uid,
        type: 'gratification',
        label: 'Prime exceptionnelle',
        amount: amt,
        gain: true,
        imposable: true,
        social: true,
      };
    } else if (val === 'retenue_avance') {
      newElem = {
        id: uid,
        type: 'avance',
        label: 'Avance sur salaire',
        amount: 50000,
        gain: false,
        imposable: false,
        social: false,
      };
    } else if (val === 'retenue_divers') {
      const amtStr = window.prompt('Montant de la retenue diverse en FCFA :', '15000');
      const amt = parseInt(amtStr || '15000', 10) || 15000;
      newElem = {
        id: uid,
        type: 'retenue',
        label: 'Retenue diverse',
        amount: amt,
        gain: false,
        imposable: false,
        social: false,
      };
    } else if (val === 'hs_15' || val === 'hs_50' || val === 'hs_100') {
      const rateLabel = val === 'hs_15' ? '+15%' : val === 'hs_50' ? '+50%' : '+100%';
      const mult = val === 'hs_15' ? 1.15 : val === 'hs_50' ? 1.5 : 2.0;
      const hoursStr = window.prompt(`Nombre d'heures sup (${rateLabel}) :`, '8');
      const hours = parseFloat(hoursStr || '8') || 8;
      const hourlyRate = Math.round((base / 173.33) * mult);
      const totalHs = Math.round(hourlyRate * hours);
      newElem = {
        id: uid,
        type: 'heures_sup',
        label: `Heures Sup ${rateLabel} (${hours}h)`,
        amount: totalHs,
        gain: true,
        imposable: true,
        social: true,
      };
    } else if (val === 'pret_vehicule') {
      newElem = {
        id: uid,
        type: 'pret',
        label: 'Prêt Véhicule (Mois 3/6)',
        amount: 50000,
        gain: false,
        imposable: false,
        social: false,
        locked: true,
        schedule: '3/6',
      };
    } else if (val === 'conge_indemnite') {
      const amtStr = window.prompt("Montant de l'indemnité de congé en FCFA :", '40000');
      const amt = parseInt(amtStr || '40000', 10) || 40000;
      newElem = {
        id: uid,
        type: 'conge',
        label: 'Indemnité de Congé',
        amount: amt,
        gain: true,
        imposable: true,
        social: true,
      };
    } else if (val === 'avantage_logement') {
      newElem = {
        id: uid,
        type: 'avantage',
        label: 'Avantage en nature Logement',
        amount: 60000,
        gain: true,
        imposable: true,
        social: false,
      };
    } else if (val === 'remboursement_frais') {
      const amtStr = window.prompt('Montant net du remboursement de frais :', '25000');
      const amt = parseInt(amtStr || '25000', 10) || 25000;
      newElem = {
        id: uid,
        type: 'remboursement',
        label: 'Remboursement de frais professionnels',
        amount: amt,
        gain: true,
        imposable: false,
        social: false,
      };
    }

    if (newElem) {
      setElements((prev) => [...prev, newElem!]);
      onShowToast(`Ajouté : ${newElem.label}`);
    }
    e.target.value = '';
  };

  const handleSave = () => {
    const updatedEmployee: Employee = {
      ...employee,
      days,
      base,
      elements,
      // If employee reached 30 days and had attendance anomaly, resolve it
      status: days === 30 && employee.status === 'anomaly' && employee.alert?.includes('Pointeuse') ? 'ok' : employee.status,
      alert: days === 30 && employee.alert?.includes('Pointeuse') ? null : employee.alert,
    };
    onSave(updatedEmployee);
    onShowToast(`Modifications enregistrées pour ${employee.name}`);
  };

  const handleApplyTargetNet = (e: React.FormEvent) => {
    e.preventDefault();
    const targetVal = parseInt(targetNetInput.replace(/\D/g, ''), 10);
    if (!targetVal || isNaN(targetVal)) {
      onShowToast('Montant invalide');
      return;
    }

    const currentDraftEmployee: Employee = {
      ...employee,
      days,
      base,
      elements,
    };

    const neededSurSalary = reverseCalculateNetToSurSalary(currentDraftEmployee, targetVal);

    const existingSursalaireIdx = elements.findIndex((el) => el.type === 'sursalaire');
    let updatedElements = [...elements];
    if (existingSursalaireIdx >= 0) {
      updatedElements[existingSursalaireIdx] = {
        ...updatedElements[existingSursalaireIdx],
        amount: neededSurSalary,
      };
    } else {
      updatedElements.push({
        id: `el-sur-${Date.now()}`,
        type: 'sursalaire',
        label: "Sursalaire d'ajustement",
        amount: neededSurSalary,
        gain: true,
        imposable: true,
        social: true,
      });
    }

    setElements(updatedElements);
    setShowTargetModal(false);
    setTargetNetInput('');
    onShowToast(`🎯 Rétro-calcul appliqué : Sursalaire ajusté à ${neededSurSalary.toLocaleString('fr-FR')} FCFA`);
  };

  return (
    <>
      {/* Backdrop */}
      <div
        id="drawer-backdrop"
        className="fixed inset-0 bg-black/40 z-40 transition-opacity"
        onClick={onClose}
      />

      {/* Drawer */}
      <div
        id="slide-drawer"
        className="fixed top-0 right-0 bottom-0 w-full md:w-[50%] max-w-5xl bg-white border-l border-[#E8E8E6] shadow-2xl z-50 flex flex-col text-sm font-bold animate-in slide-in-from-right duration-200"
      >
        {/* Drawer Header */}
        <div className="p-6 border-b border-[#E8E8E6] flex justify-between items-center bg-[#FAFAFA]">
          <div>
            <h3 id="drawer-title" className="font-bold text-lg text-[#1F1F1E]">
              {employee.name} — Département {employee.dept}
            </h3>
            <p className="text-xs text-[#6B6B6B] mt-0.5">
              Matricule {employee.matricule} • Synchro Pointeuse OK
            </p>
          </div>
          <button
            id="close-drawer-btn"
            onClick={onClose}
            className="text-base font-bold p-2.5 hover:bg-[#E8E8E6] rounded-lg transition text-[#1F1F1E] cursor-pointer"
            aria-label="Fermer"
          >
            <X className="w-6 h-6" />
          </button>
        </div>

        {/* Drawer Content */}
        <div className="p-8 space-y-8 flex-1 overflow-y-auto" id="drawer-content">
          {/* Section 1 : PRÉSENCE & TEMPS */}
          <div className="space-y-3">
            <h4 className="text-sm uppercase font-bold text-[#1F1F1E] flex items-center justify-between border-b border-[#E8E8E6] pb-2">
              <span className="flex items-center gap-2">
                <Clock className="w-4 h-4 text-[#253e87]" />
                1. PRÉSENCE & TEMPS
              </span>
              <span className="text-xs text-[#6B6B6B] font-mono uppercase bg-neutral-100 px-2 py-0.5 rounded">
                Pointeuse biométrique
              </span>
            </h4>
            <div className="border border-[#E8E8E6] rounded-xl p-5 bg-[#FAFAFA] space-y-3 font-bold text-[#1F1F1E]">
              <div className="flex justify-between items-center">
                <span>Jours travaillés :</span>
                <div className="flex items-center gap-2">
                  <input
                    id="drawer-days-input"
                    type="number"
                    min="0"
                    max="30"
                    disabled={isReadOnly}
                    value={days}
                    onChange={(e) => setDays(Math.max(0, Math.min(30, parseInt(e.target.value, 10) || 0)))}
                    className="w-24 border border-[#E8E8E6] rounded-lg text-right px-3 py-2 bg-white font-bold text-[#1F1F1E] font-mono"
                  />
                  <span>jours</span>
                </div>
              </div>

              {days < 30 && (
                <div className="text-xs text-[#253e87] bg-blue-50 border border-blue-200 p-3 rounded-lg flex items-center gap-2">
                  <AlertTriangle className="w-4 h-4 text-[#253e87] shrink-0" />
                  <span>
                    Absence détectée par la pointeuse ({30 - days} jours d'absence). Prorata automatique appliqué sur le salaire de base et le transport.
                  </span>
                </div>
              )}

              <div>
                <button
                  type="button"
                  onClick={() => {
                    const hsAmt = window.prompt("Nombre d'heures sup à 15% :", '8');
                    if (hsAmt) {
                      const h = parseFloat(hsAmt) || 8;
                      const hourlyRate = Math.round((base / 173.33) * 1.15);
                      const totalHs = Math.round(hourlyRate * h);
                      setElements((prev) => [
                        ...prev,
                        {
                          id: `el-hs-${Date.now()}`,
                          type: 'heures_sup',
                          label: `Heures Sup +15% (${h}h)`,
                          amount: totalHs,
                          gain: true,
                          imposable: true,
                          social: true,
                        },
                      ]);
                      onShowToast(`Heures sup ajoutées (${totalHs.toLocaleString('fr-FR')} FCFA)`);
                    }
                  }}
                  disabled={isReadOnly}
                  className="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold rounded-lg border border-[#E8E8E6] bg-white hover:bg-neutral-100 text-[#1F1F1E] transition cursor-pointer"
                >
                  <Plus className="w-3.5 h-3.5 text-[#253e87]" />
                  + Ajouter Heures Sup
                </button>
              </div>
            </div>
          </div>

          {/* Section 2 : ÉLÉMENTS, PRIMES & RETENUES (Lignes Dynamiques) */}
          <div className="space-y-3">
            <h4 className="text-sm uppercase font-bold text-[#1F1F1E] flex items-center justify-between border-b border-[#E8E8E6] pb-2">
              <span className="flex items-center gap-2">
                <Coins className="w-4 h-4 text-[#253e87]" />
                2. ÉLÉMENTS, PRIMES & RETENUES
              </span>
              <span className="text-xs text-[#6B6B6B] font-mono uppercase bg-neutral-100 px-2 py-0.5 rounded">
                Lignes dynamiques
              </span>
            </h4>
            <div className="border border-[#E8E8E6] rounded-xl p-5 bg-white space-y-3 font-bold text-[#1F1F1E]">
              <div className="flex justify-between items-center py-2 border-b border-[#E8E8E6]">
                <span>Salaire de base contractuel :</span>
                <div className="flex items-center gap-2">
                  <input
                    id="drawer-base-input"
                    type="number"
                    step="1000"
                    disabled={isReadOnly}
                    value={base}
                    onChange={(e) => setBase(Math.max(0, parseInt(e.target.value, 10) || 0))}
                    className="w-36 border border-[#E8E8E6] rounded-lg text-right px-3 py-2 bg-[#FAFAFA] text-[#1F1F1E] font-mono font-bold"
                  />
                  <span className="text-xs text-[#6B6B6B]">FCFA</span>
                </div>
              </div>

              {/* Dynamic lines */}
              <div className="space-y-2 max-h-56 overflow-y-auto pr-1">
                {elements.map((el, index) => {
                  const badgeTaxSoc = `[IMPÔT ${el.imposable ? 'OUI' : 'NON'} · SOC ${el.social ? 'OUI' : 'NON'}]`;
                  return (
                    <div
                      key={el.id || index}
                      className="flex items-center justify-between py-2.5 border-b border-[#E8E8E6] text-xs font-bold gap-3 bg-[#FAFAFA] px-3 rounded-lg"
                    >
                      <div className="flex items-center gap-2 flex-1">
                        <span>• {el.label}</span>
                        <span className="text-[10px] text-[#6B6B6B] font-mono">
                          {badgeTaxSoc}
                        </span>
                      </div>
                      <div className="flex items-center gap-2">
                        <input
                          type="number"
                          value={el.amount}
                          disabled={isReadOnly}
                          onChange={(e) => handleUpdateElementAmount(index, e.target.value)}
                          className="w-28 text-right bg-white border border-[#E8E8E6] rounded px-2 py-1 font-mono text-[#1F1F1E]"
                        />
                        <span className="text-xs text-[#6B6B6B]">FCFA</span>
                        {el.locked ? (
                          <span
                            className="text-[#253e87] text-xs flex items-center gap-1"
                            title={`Prêt en cours — échéancier ${el.schedule}`}
                          >
                            <Lock className="w-3 h-3" />
                            {el.schedule}
                          </span>
                        ) : (
                          !isReadOnly && (
                            <button
                              type="button"
                              onClick={() => handleRemoveElement(index)}
                              className="text-red-500 hover:text-red-700 px-1 font-bold cursor-pointer transition"
                              title="Supprimer la ligne"
                            >
                              ✕
                            </button>
                          )
                        )}
                      </div>
                    </div>
                  );
                })}
              </div>

              {/* Add item catalog dropdown */}
              {!isReadOnly && (
                <div className="pt-2">
                  <div className="relative">
                    <select
                      id="add-element-select"
                      onChange={handleAddElementCatalog}
                      className="w-full appearance-none border border-[#253e87] bg-[#FAFAFA] text-[#1F1F1E] rounded-xl p-3 font-bold text-xs uppercase cursor-pointer pr-8"
                    >
                      <option value="">+ Ajouter un élément ▾</option>
                      <optgroup label="Prime / Indemnité">
                        <option value="prime_transport">Prime de transport (30 000 FCFA)</option>
                        <option value="prime_perf">Prime de performance (45 000 FCFA)</option>
                        <option value="prime_logement">Prime de logement (25 000 FCFA)</option>
                        <option value="prime_exceptionnelle">Prime exceptionnelle...</option>
                      </optgroup>
                      <optgroup label="Retenue / Prélèvement">
                        <option value="retenue_avance">Avance sur salaire (50 000 FCFA)</option>
                        <option value="retenue_divers">Retenue diverse...</option>
                      </optgroup>
                      <optgroup label="Heures Sup (Calcul auto)">
                        <option value="hs_15">Heures Sup +15%</option>
                        <option value="hs_50">Heures Sup +50%</option>
                        <option value="hs_100">Heures Sup +100%</option>
                      </optgroup>
                      <optgroup label="Prêt / Avancement">
                        <option value="pret_vehicule">Prêt Véhicule (Mois 3/6)</option>
                      </optgroup>
                      <optgroup label="Congé / Avantage / Remboursement">
                        <option value="conge_indemnite">Indemnité de Congé</option>
                        <option value="avantage_logement">Avantage Logement</option>
                        <option value="remboursement_frais">Remboursement de frais (Net direct)</option>
                      </optgroup>
                    </select>
                    <ChevronDown className="w-4 h-4 absolute right-3 top-3.5 text-[#253e87] pointer-events-none" />
                  </div>
                </div>
              )}
            </div>
          </div>

          {/* Section 3 : SIMULATION DU BULLETIN & CIBLE */}
          <div className="space-y-3">
            <h4 className="text-sm uppercase font-bold text-[#1F1F1E] flex items-center justify-between border-b border-[#E8E8E6] pb-2">
              <span className="flex items-center gap-2">
                <Calculator className="w-4 h-4 text-[#253e87]" />
                3. SIMULATION DU BULLETIN
              </span>
              <span className="text-xs text-[#6B6B6B] font-mono uppercase bg-neutral-100 px-2 py-0.5 rounded">
                Formule centralisée computePay
              </span>
            </h4>
            <div className="border border-[#E8E8E6] rounded-xl p-5 bg-[#FAFAFA] space-y-3 font-bold text-[#1F1F1E]">
              <div className="flex justify-between text-sm">
                <span>Brut Imposable :</span>
                <span id="sim-brut" className="font-mono text-neutral-900">
                  {computedPay.baseITS.toLocaleString('fr-FR')} FCFA
                </span>
              </div>
              <div className="flex justify-between text-sm">
                <span>Cotisations Sociales :</span>
                <span id="sim-cotis" className="font-mono text-neutral-900">
                  –{computedPay.totalCotis.toLocaleString('fr-FR')} FCFA
                </span>
              </div>
              <div className="flex justify-between text-sm">
                <span>Impôt sur le Salaire (ITS 2024) :</span>
                <span id="sim-impot" className="font-mono text-neutral-900">
                  –{computedPay.itsNet.toLocaleString('fr-FR')} FCFA
                </span>
              </div>
              {computedPay.totalRetenues > 0 && (
                <div className="flex justify-between text-sm text-red-700">
                  <span>Retenues & Prêts :</span>
                  <span className="font-mono">
                    –{computedPay.totalRetenues.toLocaleString('fr-FR')} FCFA
                  </span>
                </div>
              )}

              <div className="border-t border-[#253e87] pt-3 flex justify-between items-center">
                <div>
                  <p className="text-xs uppercase text-[#6B6B6B]">NET À PAYER</p>
                  <p id="sim-net" className="text-2xl font-bold text-[#253e87] font-mono">
                    {computedPay.net.toLocaleString('fr-FR')} FCFA
                  </p>
                </div>
                {!isReadOnly && (
                  <button
                    type="button"
                    onClick={() => setShowTargetModal(true)}
                    className="inline-flex items-center gap-1.5 py-2.5 px-4 bg-white border border-[#253e87] text-[#253e87] hover:bg-[#253e87] hover:text-white transition rounded-lg text-xs font-bold cursor-pointer"
                  >
                    <span>🎯 Cible Net</span>
                  </button>
                )}
              </div>
            </div>
          </div>

          {/* Bouton Voir le Bulletin Officiel PDF */}
          <div className="pt-2">
            <button
              type="button"
              onClick={() => onOpenPayslip(employee)}
              className="w-full border-2 border-[#253e87] bg-white text-[#253e87] py-3.5 rounded-xl font-bold hover:bg-[#253e87] hover:text-white transition flex items-center justify-center gap-2.5 shadow-xs uppercase cursor-pointer"
            >
              <FileText className="w-5 h-5" />
              <span>Voir le Bulletin Officiel PDF</span>
            </button>
          </div>
        </div>

        {/* Drawer Footer */}
        <div className="p-5 border-t border-[#E8E8E6] bg-[#FAFAFA] flex gap-4">
          {!isReadOnly && (
            <button
              id="drawer-save-btn"
              type="button"
              onClick={handleSave}
              className="flex-1 justify-center py-4 uppercase bg-[#253e87] text-white hover:bg-[#1c306d] font-bold rounded-lg transition cursor-pointer"
            >
              Enregistrer & Recalculer
            </button>
          )}
          <button
            type="button"
            onClick={onClose}
            className="py-4 uppercase px-8 bg-neutral-200 text-[#1F1F1E] hover:bg-neutral-300 font-bold rounded-lg transition cursor-pointer"
          >
            Fermer
          </button>
        </div>
      </div>

      {/* Rétro-calcul / Cible Net Modal */}
      {showTargetModal && (
        <div className="fixed inset-0 bg-black/50 z-60 flex items-center justify-center p-4">
          <div className="bg-white rounded-2xl max-w-md w-full p-6 space-y-5 border border-[#E8E8E6] shadow-2xl">
            <div className="flex justify-between items-center border-b border-[#E8E8E6] pb-3">
              <h4 className="font-bold text-base text-[#1F1F1E] flex items-center gap-2">
                <span>🎯 Rétro-calcul du Salaire Net</span>
              </h4>
              <button
                type="button"
                onClick={() => setShowTargetModal(false)}
                className="text-neutral-500 hover:text-neutral-800"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            <form onSubmit={handleApplyTargetNet} className="space-y-4">
              <p className="text-xs text-[#6B6B6B]">
                Saisissez le <strong>Net Cible</strong> souhaité pour {employee.name}.
                L'algorithme va rétro-calculer automatiquement le montant exact de sursalaire imposable et soumis aux cotisations nécessaire pour atteindre ce net.
              </p>

              <div>
                <label className="block text-xs font-bold uppercase text-[#1F1F1E] mb-1">
                  Net à payer cible (FCFA)
                </label>
                <input
                  type="text"
                  autoFocus
                  placeholder="Ex : 500 000"
                  value={targetNetInput}
                  onChange={(e) => setTargetNetInput(e.target.value)}
                  className="w-full border border-[#E8E8E6] rounded-xl px-4 py-2.5 font-mono text-base font-bold text-[#1F1F1E] focus:outline-none focus:border-[#253e87]"
                />
              </div>

              <div className="flex gap-3 pt-2">
                <button
                  type="submit"
                  className="flex-1 bg-[#253e87] text-white py-3 rounded-xl font-bold uppercase text-xs hover:bg-[#1c306d] transition cursor-pointer"
                >
                  Calculer le sursalaire
                </button>
                <button
                  type="button"
                  onClick={() => setShowTargetModal(false)}
                  className="px-5 bg-neutral-100 text-neutral-800 py-3 rounded-xl font-bold uppercase text-xs hover:bg-neutral-200 transition cursor-pointer"
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
