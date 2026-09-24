import React, { useState, useMemo } from 'react';
import { Employee, ComputedEmployeePay, Period, EmployeeElement } from '../../data/employeeTypes';
import { PayslipModal } from '../../components/PayslipModal';
import { MassProcessingToolbar } from '../../components/MassProcessingToolbar';
import { FormattedAmountInput } from '../../components/FormattedAmountInput';
import {
  Clock,
  Coins,
  Calculator,
  ShieldAlert,
  CheckCircle2,
  Lock,
  FileText,
  AlertTriangle,
  ChevronDown,
  ChevronRight,
  RotateCcw,
  Sparkles,
} from 'lucide-react';

interface Model3ViewProps {
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

const STEPS_META = [
  { id: 1, name: 'Présence', icon: Clock },
  { id: 2, name: 'Variables', icon: Coins },
  { id: 3, name: 'Calcul', icon: Calculator },
  { id: 4, name: 'Contrôle', icon: ShieldAlert },
  { id: 5, name: 'Validation', icon: CheckCircle2 },
];

export const Model3View: React.FC<Model3ViewProps> = ({
  employees,
  computedMap,
  currentPeriod,
  isReadOnly,
  onUpdateEmployee,
  onUpdateEmployees,
  onOpenValidationModal,
  onUnlockPeriod,
  onShowToast,
}) => {
  // Stepper state (1 to 5)
  const [currentStep, setCurrentStep] = useState<number>(1);

  // Selection for bulk mass processing
  const [selectedIds, setSelectedIds] = useState<number[]>([]);

  // Step 2: Treated variables tracking
  const [treatedVarIds, setTreatedVarIds] = useState<number[]>(() => {
    // Default: employees without anomaly are pre-treated
    return employees.filter((e) => e.status === 'ok' && e.days === 30).map((e) => e.id);
  });

  // Step 2 sub-editor active employee
  const [editingVarEmpId, setEditingVarEmpId] = useState<number | null>(null);

  // Step 3: Run calculation state
  const [isCalculating, setIsCalculating] = useState<boolean>(false);
  const [hasCalculated, setHasCalculated] = useState<boolean>(true);

  // Step 4: Examined / PDF modal employee
  const [payslipModalEmpId, setPayslipModalEmpId] = useState<number | null>(null);

  // Step 1: Justifications notes state
  const [motifs, setMotifs] = useState<Record<number, string>>({});

  // Totals
  const totals = useMemo(() => {
    let base = 0;
    let primes = 0;
    let cotis = 0;
    let impot = 0;
    let net = 0;
    let brut = 0;

    for (const emp of employees) {
      const c = computedMap.get(emp.id);
      if (c) {
        base += c.proratedBase;
        primes += c.totalGains;
        cotis += c.totalCotis;
        impot += c.itsNet;
        net += c.net;
        brut += c.brut;
      }
    }
    return { base, primes, cotis, impot, net, brut };
  }, [employees, computedMap]);

  // Anomalies list
  const anomalyEmployees = useMemo(() => {
    return employees.filter((e) => e.days < 30 || e.status === 'anomaly');
  }, [employees]);

  const normalEmployees = useMemo(() => {
    return employees.filter((e) => e.days === 30 && e.status !== 'anomaly');
  }, [employees]);

  // Handle step navigation
  const goToStep = (stepId: number) => {
    if (stepId <= currentStep || stepId === currentStep + 1) {
      setCurrentStep(stepId);
    }
  };

  // Step 1 actions
  const handleUpdateEmpDays = (id: number, val: string) => {
    const emp = employees.find((e) => e.id === id);
    if (!emp || isReadOnly) return;
    const days = Math.max(0, Math.min(30, parseInt(val, 10) || 0));
    const isFull = days === 30;
    const updated: Employee = {
      ...emp,
      days,
      status: isFull && emp.alert?.includes('Pointeuse') ? 'ok' : emp.status,
      alert: isFull && emp.alert?.includes('Pointeuse') ? null : emp.alert,
    };
    onUpdateEmployee(updated);
  };

  const handleValidateStep1 = () => {
    const invalid = employees.some((e) => isNaN(e.days) || e.days < 0 || e.days > 30);
    if (invalid) {
      onShowToast('Veuillez vérifier que tous les jours sont renseignés entre 0 et 30.');
      return;
    }
    onShowToast('✓ Étape 1 (Présence) validée avec succès.');
    setCurrentStep(2);
  };

  // Step 2 actions
  const handleRolloverVariables = () => {
    setTreatedVarIds(employees.map((e) => e.id));
    onShowToast(`✓ Primes et éléments variables stables reconduits pour les ${employees.length} salariés.`);
  };

  const handleSaveVarEditor = (updatedEmp: Employee) => {
    onUpdateEmployee(updatedEmp);
    setTreatedVarIds((prev) => (prev.includes(updatedEmp.id) ? prev : [...prev, updatedEmp.id]));
    setEditingVarEmpId(null);
    onShowToast(`Variables enregistrées pour ${updatedEmp.name}`);
  };

  const handleAcceptVarAsIs = (id: number) => {
    setTreatedVarIds((prev) => (prev.includes(id) ? prev : [...prev, id]));
    setEditingVarEmpId(null);
    const emp = employees.find((e) => e.id === id);
    onShowToast(`Salarié ${emp?.name} accepté sans modification.`);
  };

  const handleValidateStep2 = () => {
    setTreatedVarIds(employees.map((e) => e.id));
    onShowToast('✓ Étape 2 (Variables) validée.');
    setCurrentStep(3);
  };

  // Step 3 actions
  const handleRunCalculation = () => {
    setIsCalculating(true);
    setTimeout(() => {
      setIsCalculating(false);
      setHasCalculated(true);
      onShowToast(`✓ Calcul du moteur CI 2026 terminé pour les ${employees.length} bulletins.`);
    }, 1000);
  };

  // Step 4 actions
  const handleAcceptDeviation = (id: number) => {
    const emp = employees.find((e) => e.id === id);
    if (emp) {
      onUpdateEmployee({
        ...emp,
        status: 'ok',
        alert: null,
      });
      onShowToast(`Écart accepté et consigné pour ${emp.name}`);
    }
  };

  const editingEmp = employees.find((e) => e.id === editingVarEmpId) || null;
  const payslipEmp = employees.find((e) => e.id === payslipModalEmpId) || null;
  const payslipCalc = payslipEmp ? computedMap.get(payslipEmp.id) || null : null;

  return (
    <div className="flex-1 flex flex-col bg-white border border-[#E8E8E6] rounded-2xl overflow-hidden shadow-xs">
      {/* 1. STEPPER NAVIGATION (STICKY) */}
      <nav
        id="tunnel-stepper"
        className="border-b border-[#E8E8E6] bg-[#FAFAFA] px-6 py-3.5 sticky top-[73px] z-30 shadow-2xs"
      >
        <div className="max-w-6xl mx-auto flex items-center justify-between overflow-x-auto text-xs font-bold gap-3">
          <div className="flex items-center gap-2 md:gap-5 w-full justify-between">
            {STEPS_META.map((step) => {
              const isCurrent = step.id === currentStep;
              const isCompleted = step.id < currentStep;
              const isClickable = step.id <= currentStep;
              const Icon = step.icon;

              return (
                <div
                  key={step.id}
                  onClick={() => isClickable && goToStep(step.id)}
                  className={`flex items-center gap-2 transition ${
                    isClickable ? 'cursor-pointer hover:opacity-80' : 'opacity-40 cursor-not-allowed'
                  } ${isCurrent ? 'text-[#253e87] font-bold' : 'text-[#6B6B6B]'}`}
                >
                  <span
                    className={`w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-mono font-bold ${
                      isCompleted
                        ? 'bg-emerald-600 text-white'
                        : isCurrent
                        ? 'bg-[#253e87] text-white shadow-xs'
                        : 'bg-gray-200 text-gray-700'
                    }`}
                  >
                    {isCompleted ? '✓' : step.id}
                  </span>
                  <span className="hidden sm:inline">
                    {step.id} — {step.name}
                  </span>
                  <span className="sm:hidden">{step.name}</span>
                  {step.id < 5 && <span className="text-[#D4D4D8] ml-1">▸</span>}
                </div>
              );
            })}
          </div>
        </div>
      </nav>

      {/* 2. CONTENU DE L'ÉTAPE COURANTE */}
      <main className="flex-1 max-w-6xl mx-auto w-full p-6" id="tunnel-main-content">
        {/* ================= ÉTAPE 1 : PRÉSENCE ================= */}
        {currentStep === 1 && (
          <div className="space-y-6 animate-in fade-in duration-150">
            <div>
              <h2 className="text-lg font-bold text-[#1F1F1E]">
                Étape 1 — Contrôle des Présences & Pointeuse
              </h2>
              <p className="text-xs text-[#6B6B6B] font-bold">
                Vérifiez et corrigez les anomalies de pointage avant de passer aux variables. Le prorata de salaire et de transport sur base 30 jours s'ajuste automatiquement.
              </p>
            </div>

            <div className="space-y-4">
              <div className="font-bold text-xs uppercase tracking-wider text-[#6B6B6B] flex items-center gap-2">
                <AlertTriangle className="w-4 h-4 text-[#253e87]" />
                <span>Salariés avec présence ou anomalie à contrôler ({anomalyEmployees.length})</span>
              </div>

              <div className="space-y-3">
                {anomalyEmployees.map((emp) => (
                  <div
                    key={emp.id}
                    className="border border-[#E8E8E6] rounded-xl p-5 bg-[#FAFAFA] space-y-3 shadow-2xs"
                  >
                    <div className="flex justify-between items-start">
                      <div>
                        <h4 className="font-bold text-sm text-[#1F1F1E]">{emp.name}</h4>
                        <p className="text-xs text-[#6B6B6B] font-bold">
                          Département {emp.dept} • Matricule {emp.matricule} • Base contractuelle :{' '}
                          <span className="font-mono text-[#1F1F1E]">{emp.base.toLocaleString('fr-FR')} FCFA</span>
                        </p>
                      </div>
                      <span className="text-xs font-mono font-bold px-2.5 py-1 rounded bg-[#253e87] text-white flex items-center gap-1 shadow-2xs">
                        <AlertTriangle className="w-3 h-3" />
                        {emp.alert || 'Présence < 30j'}
                      </span>
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                      <div>
                        <label className="block text-xs font-bold mb-1 text-[#1F1F1E]">
                          Jours travaillés (pré-rempli pointeuse)
                        </label>
                        <div className="flex items-center gap-2">
                          <input
                            type="number"
                            min="0"
                            max="30"
                            disabled={isReadOnly}
                            value={emp.days}
                            onChange={(e) => handleUpdateEmpDays(emp.id, e.target.value)}
                            className="w-24 border border-[#E8E8E6] rounded-lg px-3 py-2 bg-white font-mono text-sm font-bold text-[#1F1F1E] focus:outline-none focus:border-[#253e87] text-center"
                          />
                          <span className="text-xs font-bold text-[#6B6B6B]">jours sur 30</span>
                          {emp.days < 30 && (
                            <span className="text-xs text-[#253e87] font-bold">
                              ({30 - emp.days}j d'absence)
                            </span>
                          )}
                        </div>
                      </div>

                      <div>
                        <label className="block text-xs font-bold mb-1 text-[#1F1F1E]">
                          Justification / Motif RH (tracé pour audit)
                        </label>
                        <input
                          type="text"
                          disabled={isReadOnly}
                          value={motifs[emp.id] || ''}
                          onChange={(e) => setMotifs({ ...motifs, [emp.id]: e.target.value })}
                          placeholder="Ex: Absence justifiée / Certificat médical"
                          className="w-full border border-[#E8E8E6] rounded-lg px-3 py-2 bg-white text-xs font-bold text-[#1F1F1E] focus:outline-none focus:border-[#253e87]"
                        />
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            {/* Accordéon salariés conformes */}
            <div className="border border-[#E8E8E6] rounded-xl overflow-hidden bg-white">
              <details className="group">
                <summary className="p-4 bg-[#FAFAFA] font-bold text-xs cursor-pointer flex justify-between items-center text-[#1F1F1E]">
                  <span>{normalEmployees.length} salariés à 30 jours — conformes</span>
                  <span className="font-mono text-xs text-[#6B6B6B]">▼ Dérouler</span>
                </summary>
                <div className="p-4 border-t border-[#E8E8E6] grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs font-mono font-bold text-[#6B6B6B]">
                  {normalEmployees.map((e) => (
                    <div key={e.id}>• {e.name} (30j)</div>
                  ))}
                </div>
              </details>
            </div>

            <div className="flex justify-end pt-4">
              <button
                type="button"
                onClick={handleValidateStep1}
                className="py-3 px-6 bg-[#253e87] text-white hover:bg-[#1c306d] text-xs font-bold rounded-lg transition cursor-pointer shadow-xs uppercase tracking-wider"
              >
                Valider l'étape Présence ▸
              </button>
            </div>
          </div>
        )}

        {/* ================= ÉTAPE 2 : VARIABLES ================= */}
        {currentStep === 2 && (
          <div className="space-y-6 animate-in fade-in duration-150">
            {editingEmp ? (
              /* SOUS-ÉDITEUR MODAL / INLINE D'UN SALARIÉ */
              <div className="space-y-6 max-w-2xl mx-auto border border-[#E8E8E6] rounded-2xl p-6 bg-white shadow-xl">
                <div className="flex justify-between items-center border-b border-[#E8E8E6] pb-3">
                  <h3 className="font-bold text-sm uppercase text-[#253e87] flex items-center gap-2">
                    <Coins className="w-4 h-4" />
                    Traitement des variables — {editingEmp.name}
                  </h3>
                  <button
                    type="button"
                    onClick={() => setEditingVarEmpId(null)}
                    className="text-xs font-bold text-[#6B6B6B] hover:text-[#1F1F1E] cursor-pointer"
                  >
                    ✕ Fermer
                  </button>
                </div>

                <div className="space-y-4">
                  <div>
                    <label className="block text-xs font-bold text-[#1F1F1E] mb-1">Salaire de Base</label>
                    <FormattedAmountInput
                      disabled={isReadOnly}
                      value={editingEmp.base}
                      onChange={(val) => {
                        onUpdateEmployee({ ...editingEmp, base: val });
                      }}
                      currencySuffix="FCFA"
                      className="w-full border border-[#E8E8E6] rounded-lg p-2.5 font-montserrat text-sm font-bold text-[#1F1F1E]"
                    />
                  </div>

                  <div className="space-y-2">
                    <label className="block text-xs font-bold text-[#1F1F1E]">Éléments de paie actuels</label>
                    <div className="border border-[#E8E8E6] rounded-xl p-3 bg-[#FAFAFA] space-y-2">
                      {editingEmp.elements.map((el, idx) => (
                        <div
                          key={el.id}
                          className="flex items-center justify-between py-2 border-b border-[#E8E8E6] text-xs font-montserrat font-bold gap-3"
                        >
                          <span className="text-[#1F1F1E] font-sans font-bold">• {el.label}</span>
                          <div className="flex items-center gap-2">
                            <FormattedAmountInput
                              disabled={isReadOnly || el.locked}
                              value={el.amount}
                              onChange={(amt) => {
                                const updatedEls = [...editingEmp.elements];
                                updatedEls[idx] = { ...updatedEls[idx], amount: amt };
                                onUpdateEmployee({ ...editingEmp, elements: updatedEls });
                              }}
                              currencySuffix="FCFA"
                              className="w-32 text-right border border-[#E8E8E6] rounded px-2 py-1 font-montserrat text-xs bg-white text-[#1F1F1E] font-bold"
                            />
                            {!el.locked && !isReadOnly && (
                              <button
                                type="button"
                                onClick={() => {
                                  const updatedEls = editingEmp.elements.filter((_, i) => i !== idx);
                                  onUpdateEmployee({ ...editingEmp, elements: updatedEls });
                                }}
                                className="text-xs text-red-600 font-mono px-1 cursor-pointer"
                              >
                                ✕
                              </button>
                            )}
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>

                  {editingEmp.elements.some((e) => e.type === 'pret') && (
                    <div className="border border-[#253e87]/20 rounded-xl p-3 bg-blue-50/40 text-xs font-bold space-y-1 text-[#253e87]">
                      <p>🔗 Échéancier Prêt Véhicule : Mois 3 / 6 (Actif)</p>
                      <p className="text-[11px] text-[#6B6B6B]">
                        Prélèvement mensuel verrouillé de 50 000 FCFA conformément au contrat.
                      </p>
                    </div>
                  )}
                </div>

                <div className="flex justify-end gap-3 pt-4 border-t border-[#E8E8E6]">
                  <button
                    type="button"
                    onClick={() => handleAcceptVarAsIs(editingEmp.id)}
                    className="px-4 py-2 border border-[#E8E8E6] rounded-lg text-xs font-bold hover:bg-[#F4F4F5] text-[#1F1F1E] cursor-pointer"
                  >
                    Accepter tel quel
                  </button>
                  <button
                    type="button"
                    onClick={() => handleSaveVarEditor(editingEmp)}
                    className="px-5 py-2 bg-[#253e87] text-white hover:bg-[#1c306d] text-xs font-bold rounded-lg cursor-pointer"
                  >
                    Enregistrer & Traiter
                  </button>
                </div>
              </div>
            ) : (
              /* LISTE PRINCIPALE DE L'ÉTAPE 2 */
              <>
                <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                  <div>
                    <h2 className="text-lg font-bold text-[#1F1F1E]">
                      Étape 2 — Saisie des Éléments Variables
                    </h2>
                    <p className="text-xs text-[#6B6B6B] font-bold">
                      Traitement des primes, prêts et variations mensuelles avant calcul global.
                    </p>
                  </div>
                  <button
                    type="button"
                    onClick={handleRolloverVariables}
                    disabled={isReadOnly}
                    className="px-4 py-2 border border-[#E8E8E6] bg-white hover:bg-[#F4F4F5] text-[#1F1F1E] text-xs font-bold rounded-lg transition cursor-pointer shadow-2xs"
                  >
                    ▸ Reporter les éléments stables du mois précédent
                  </button>
                </div>

                <div className="bg-[#FAFAFA] border border-[#E8E8E6] rounded-xl p-4 flex justify-between items-center text-xs font-bold">
                  <span className="text-[#1F1F1E]">Progression du traitement</span>
                  <span className="font-mono text-[#253e87]">
                    {treatedVarIds.length}/{employees.length} salariés traités
                  </span>
                </div>

                <div className="space-y-3">
                  {employees
                    .filter((e) => !treatedVarIds.includes(e.id) || e.status === 'anomaly')
                    .map((emp) => (
                      <div
                        key={emp.id}
                        className="border border-[#E8E8E6] rounded-xl p-5 bg-[#FAFAFA] space-y-3 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 shadow-2xs"
                      >
                        <div className="space-y-1">
                          <div className="flex items-center gap-2">
                            <h4 className="font-bold text-sm text-[#1F1F1E]">{emp.name}</h4>
                            <span className="text-xs font-bold px-2 py-0.5 rounded bg-white border border-[#E8E8E6] text-[#1F1F1E]">
                              {emp.dept}
                            </span>
                            {treatedVarIds.includes(emp.id) && (
                              <span className="text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">
                                ✓ Traité
                              </span>
                            )}
                          </div>
                          <p className="text-xs text-[#6B6B6B] font-bold">
                            Raison : {emp.alert || (emp.status === 'pending' ? 'Salarié en attente de saisie des variables' : 'Élément variable ou prime à vérifier')}
                            {emp.seniority && ` • Ancienneté : ${emp.seniority}`}
                          </p>
                        </div>
                        <button
                          type="button"
                          onClick={() => setEditingVarEmpId(emp.id)}
                          className="px-4 py-2 bg-[#253e87] text-white hover:bg-[#1c306d] text-xs font-bold rounded-lg transition cursor-pointer"
                        >
                          Traiter ▸
                        </button>
                      </div>
                    ))}
                </div>

                <div className="flex justify-between pt-4">
                  <button
                    type="button"
                    onClick={() => setCurrentStep(1)}
                    className="px-4 py-2 border border-[#E8E8E6] rounded-lg text-xs font-bold hover:bg-[#F4F4F5] text-[#1F1F1E] cursor-pointer"
                  >
                    ◂ Retour
                  </button>
                  <button
                    type="button"
                    onClick={handleValidateStep2}
                    className="py-3 px-6 bg-[#253e87] text-white hover:bg-[#1c306d] text-xs font-bold rounded-lg transition cursor-pointer shadow-xs uppercase tracking-wider"
                  >
                    Valider l'étape Variables ▸
                  </button>
                </div>
              </>
            )}
          </div>
        )}

        {/* ================= ÉTAPE 3 : CALCUL ================= */}
        {currentStep === 3 && (
          <div className="space-y-6 animate-in fade-in duration-150">
            {isCalculating ? (
              <div className="max-w-md mx-auto text-center space-y-4 py-20">
                <p className="text-sm font-bold font-mono text-[#253e87] animate-pulse">
                  Calcul des {employees.length} bulletins en cours (Moteur CI 2026)…
                </p>
                <div className="w-full bg-[#E8E8E6] h-2 rounded-full overflow-hidden">
                  <div className="bg-[#253e87] h-full w-full animate-pulse"></div>
                </div>
              </div>
            ) : !hasCalculated ? (
              <div className="max-w-xl mx-auto text-center space-y-6 py-12">
                <div className="space-y-2">
                  <h2 className="text-xl font-bold text-[#1F1F1E]">Étape 3 — Calcul de la Paie</h2>
                  <p className="text-xs text-[#6B6B6B] font-bold">
                    Lancement du moteur de paie unifié (Barème ITS 2024, CNPS 6,3%, CMU, RICF) pour l'ensemble de l'effectif.
                  </p>
                </div>
                <button
                  type="button"
                  onClick={handleRunCalculation}
                  className="py-4 px-8 bg-[#253e87] text-white hover:bg-[#1c306d] font-bold text-sm rounded-xl cursor-pointer shadow-lg transition uppercase tracking-wider"
                >
                  Calculer la paie maintenant ⚡
                </button>
              </div>
            ) : (
              /* GRAND TABLEAU DE MASSE CONSOLIDÉ */
              <>
                <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                  <div>
                    <h2 className="text-lg font-bold text-[#1F1F1E]">
                      Étape 3 — Tableau de Masse Salariale Consolidé
                    </h2>
                    <p className="text-xs text-[#6B6B6B] font-bold">
                      Vue d'ensemble consolidée des {employees.length} bulletins calculés avec le barème officiel CI.
                    </p>
                  </div>
                  <button
                    type="button"
                    onClick={() => setCurrentStep(4)}
                    className="py-2.5 px-5 bg-[#253e87] text-white hover:bg-[#1c306d] text-xs font-bold rounded-lg transition cursor-pointer shadow-xs uppercase tracking-wider"
                  >
                    Passer au contrôle ▸
                  </button>
                </div>

                {/* Traitement de masse Step 3 */}
                <MassProcessingToolbar
                  selectedIds={selectedIds}
                  employees={employees}
                  isReadOnly={isReadOnly}
                  onClearSelection={() => setSelectedIds([])}
                  onUpdateEmployees={onUpdateEmployees}
                  onShowToast={onShowToast}
                />

                <div className="border border-[#E8E8E6] rounded-xl overflow-x-auto bg-white shadow-xs">
                  <table className="w-full text-left text-[15px]">
                    <thead>
                      <tr className="border-b border-[#E8E8E6] bg-[#FAFAFA] text-[#1F1F1E] uppercase text-[13px] md:text-[14px] font-semibold tracking-[0.5px]">
                        <th className="p-3.5 w-10 text-center">
                          <input
                            type="checkbox"
                            checked={employees.length > 0 && employees.every((e) => selectedIds.includes(e.id))}
                            onChange={(e) => {
                              if (e.target.checked) {
                                setSelectedIds(employees.map((emp) => emp.id));
                              } else {
                                setSelectedIds([]);
                              }
                            }}
                            className="rounded border-[#E8E8E6] text-[#253e87] focus:ring-[#253e87] cursor-pointer"
                          />
                        </th>
                        <th className="p-3.5">SALARIÉ</th>
                        <th className="p-3.5">DÉPARTEMENT</th>
                        <th className="p-3.5 text-center">JOURS</th>
                        <th className="p-3.5 text-right">BASE (PRORATA)</th>
                        <th className="p-3.5 text-right">PRIMES</th>
                        <th className="p-3.5 text-right">COTIS. (CI)</th>
                        <th className="p-3.5 text-right">IMPÔT (ITS)</th>
                        <th className="p-3.5 text-right font-semibold">NET (FCFA)</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-[#E8E8E6] text-[#1F1F1E] text-[15px]">
                      {employees.map((emp) => {
                        const calc = computedMap.get(emp.id)!;
                        const hasAnomaly = emp.status === 'anomaly';
                        const isSelected = selectedIds.includes(emp.id);
                        return (
                          <tr key={emp.id} className={`hover:bg-[#FAFAFA] transition ${isSelected ? 'bg-blue-50/40' : ''}`}>
                            <td className="p-3.5 text-center" onClick={(e) => e.stopPropagation()}>
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
                            <td className="p-3.5 font-semibold flex items-center gap-2 text-[15px]">
                              {hasAnomaly && <span className="w-2.5 h-2.5 rounded-full bg-[#253e87] shrink-0"></span>}
                              <span>{emp.name}</span>
                            </td>
                            <td className="p-3.5 text-[#1F1F1E] text-[14px] md:text-[15px]">{emp.dept}</td>
                            <td className="p-3.5 text-center font-mono text-[15px]">{emp.days}j</td>
                            <td className="p-3.5 text-right font-montserrat text-[15px] font-semibold">
                              {calc.proratedBase.toLocaleString('fr-FR')}
                            </td>
                            <td className="p-3.5 text-right font-montserrat text-[15px] font-semibold">
                              {calc.totalGains.toLocaleString('fr-FR')}
                            </td>
                            <td className="p-3.5 text-right font-montserrat text-[#6B6B6B] text-[15px]">
                              –{calc.totalCotis.toLocaleString('fr-FR')}
                            </td>
                            <td className="p-3.5 text-right font-montserrat text-[#6B6B6B] text-[15px]">
                              –{calc.itsNet.toLocaleString('fr-FR')}
                            </td>
                            <td className="p-3.5 text-right font-montserrat font-semibold text-[#253e87] text-[15px]">
                              {calc.net.toLocaleString('fr-FR')}
                            </td>
                          </tr>
                        );
                      })}
                    </tbody>
                    <tfoot>
                      <tr className="bg-[#FAFAFA] font-semibold border-t-2 border-[#253e87] text-[#1F1F1E] text-[14px]">
                        <td className="p-3.5 text-center font-mono text-[12px] text-[#6B6B6B]">
                          {selectedIds.length > 0 ? `${selectedIds.length} coché${selectedIds.length > 1 ? 's' : ''}` : '—'}
                        </td>
                        <td className="p-3.5" colSpan={3}>
                          TOTAUX ({employees.length} salariés)
                        </td>
                        <td className="p-3.5 text-right font-montserrat text-[15px] font-semibold">{totals.base.toLocaleString('fr-FR')}</td>
                        <td className="p-3.5 text-right font-montserrat text-[15px] font-semibold">{totals.primes.toLocaleString('fr-FR')}</td>
                        <td className="p-3.5 text-right font-montserrat text-[15px]">–{totals.cotis.toLocaleString('fr-FR')}</td>
                        <td className="p-3.5 text-right font-montserrat text-[15px]">–{totals.impot.toLocaleString('fr-FR')}</td>
                        <td className="p-3.5 text-right font-montserrat text-[16px] font-semibold text-[#253e87]">
                          {totals.net.toLocaleString('fr-FR')} FCFA
                        </td>
                      </tr>
                    </tfoot>
                  </table>
                </div>

                <div className="flex justify-between pt-4">
                  <button
                    type="button"
                    onClick={() => setCurrentStep(2)}
                    className="px-4 py-2 border border-[#E8E8E6] rounded-lg text-xs font-bold hover:bg-[#F4F4F5] text-[#1F1F1E] cursor-pointer"
                  >
                    ◂ Retour
                  </button>
                  <button
                    type="button"
                    onClick={() => setCurrentStep(4)}
                    className="py-3 px-6 bg-[#253e87] text-white hover:bg-[#1c306d] text-xs font-bold rounded-lg transition cursor-pointer shadow-xs uppercase tracking-wider"
                  >
                    Passer au contrôle ▸
                  </button>
                </div>
              </>
            )}
          </div>
        )}

        {/* ================= ÉTAPE 4 : CONTRÔLE ================= */}
        {currentStep === 4 && (
          <div className="space-y-6 animate-in fade-in duration-150">
            <div>
              <h2 className="text-lg font-bold text-[#1F1F1E]">
                Étape 4 — Contrôle des Écarts Suspects
              </h2>
              <p className="text-xs text-[#6B6B6B] font-bold">
                Vérification des anomalies de pointeuse, primes inhabituelles et variations nettes.
              </p>
            </div>

            <div className="space-y-3">
              {anomalyEmployees.length > 0 ? (
                anomalyEmployees.map((emp) => {
                  const calc = computedMap.get(emp.id)!;
                  return (
                    <div
                      key={emp.id}
                      className="border border-[#E8E8E6] rounded-xl p-4 bg-[#FAFAFA] flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shadow-2xs"
                    >
                      <div>
                        <h4 className="font-bold text-xs uppercase text-[#1F1F1E]">
                          {emp.name} ({emp.dept})
                        </h4>
                        <p className="text-xs text-[#6B6B6B] mt-0.5 font-bold">
                          {emp.alert || `Absence détectée (${emp.days}/30j)`} — Net actuel :{' '}
                          <span className="font-mono text-[#253e87]">
                            {calc.net.toLocaleString('fr-FR')} FCFA
                          </span>
                        </p>
                      </div>
                      <div className="flex items-center gap-2">
                        <button
                          type="button"
                          onClick={() => handleAcceptDeviation(emp.id)}
                          className="px-3.5 py-1.5 border border-[#E8E8E6] bg-white rounded-lg text-xs font-bold hover:bg-[#F4F4F5] text-[#1F1F1E] cursor-pointer"
                        >
                          Accepter
                        </button>
                        <button
                          type="button"
                          onClick={() => setPayslipModalEmpId(emp.id)}
                          className="px-3.5 py-1.5 bg-[#253e87] text-white hover:bg-[#1c306d] rounded-lg text-xs font-bold cursor-pointer"
                        >
                          Examiner le bulletin
                        </button>
                      </div>
                    </div>
                  );
                })
              ) : (
                <div className="border border-emerald-200 bg-emerald-50 rounded-xl p-6 text-center text-xs font-bold text-emerald-800">
                  ✓ Aucun écart suspect non traité. Tous les salariés sont en conformité.
                </div>
              )}
            </div>

            <div className="flex justify-between pt-4">
              <button
                type="button"
                onClick={() => setCurrentStep(3)}
                className="px-4 py-2 border border-[#E8E8E6] rounded-lg text-xs font-bold hover:bg-[#F4F4F5] text-[#1F1F1E] cursor-pointer"
              >
                ◂ Retour
              </button>
              <button
                type="button"
                onClick={() => setCurrentStep(5)}
                className="py-3 px-6 bg-[#253e87] text-white hover:bg-[#1c306d] text-xs font-bold rounded-lg transition cursor-pointer shadow-xs uppercase tracking-wider"
              >
                Valider l'étape Contrôle ▸
              </button>
            </div>
          </div>
        )}

        {/* ================= ÉTAPE 5 : VALIDATION ================= */}
        {currentStep === 5 && (
          <div className="space-y-6 max-w-2xl mx-auto animate-in fade-in duration-150">
            <div>
              <h2 className="text-lg font-bold text-[#1F1F1E]">
                Étape 5 — Validation & Clôture de Paie
              </h2>
              <p className="text-xs text-[#6B6B6B] font-bold">
                Récapitulatif final et verrouillage pour la période de {currentPeriod.name}.
              </p>
            </div>

            <div className="border border-[#E8E8E6] rounded-2xl p-6 bg-[#FAFAFA] space-y-3 text-xs font-montserrat font-bold shadow-xs">
              <div className="flex justify-between text-[#1F1F1E]">
                <span>Période :</span> <span className="font-bold">{currentPeriod.name}</span>
              </div>
              <div className="flex justify-between text-[#1F1F1E]">
                <span>Effectif total :</span> <span className="font-bold">{employees.length} salariés</span>
              </div>
              <div className="flex justify-between text-[#1F1F1E]">
                <span>Masse Brute Imposable :</span>{' '}
                <span className="font-bold">{totals.brut.toLocaleString('fr-FR')} FCFA</span>
              </div>
              <div className="flex justify-between text-[#1F1F1E]">
                <span>Cotisations Sociales (CNPS + CMU) :</span>{' '}
                <span className="font-bold">–{totals.cotis.toLocaleString('fr-FR')} FCFA</span>
              </div>
              <div className="flex justify-between text-[#1F1F1E]">
                <span>Impôt Salariés (ITS 2024 unifié) :</span>{' '}
                <span className="font-bold">–{totals.impot.toLocaleString('fr-FR')} FCFA</span>
              </div>
              <div className="flex justify-between border-t border-[#E8E8E6] pt-3 text-sm text-[#253e87]">
                <span>Masse Nette à Virer :</span>{' '}
                <span className="font-bold text-base">{totals.net.toLocaleString('fr-FR')} FCFA</span>
              </div>
            </div>

            <div className="border border-[#E8E8E6] rounded-xl p-4 space-y-2 text-xs font-bold bg-white">
              <p className="font-bold uppercase text-[#6B6B6B]">Checklist finale</p>
              <div className="flex items-center gap-2 text-[#1F1F1E]">
                <input type="checkbox" checked disabled className="rounded text-[#253e87]" />
                <span>Tous les pointages validés</span>
              </div>
              <div className="flex items-center gap-2 text-[#1F1F1E]">
                <input type="checkbox" checked disabled className="rounded text-[#253e87]" />
                <span>Écarts suspects examinés et tracés</span>
              </div>
              <div className="flex items-center gap-2 text-[#1F1F1E]">
                <input type="checkbox" checked disabled className="rounded text-[#253e87]" />
                <span>Conformité barème ITS 2024 & charges sociales CI</span>
              </div>
            </div>

            <div className="flex flex-col sm:flex-row gap-3 pt-2">
              {!isReadOnly ? (
                <button
                  type="button"
                  onClick={onOpenValidationModal}
                  className="py-3.5 px-6 bg-[#253e87] text-white hover:bg-[#1c306d] text-xs font-bold rounded-xl flex-1 transition cursor-pointer shadow-md uppercase tracking-wider flex items-center justify-center gap-2"
                >
                  <Lock className="w-4 h-4" />
                  <span>Confirmer le paiement et clôturer 🔒</span>
                </button>
              ) : (
                <button
                  type="button"
                  onClick={onUnlockPeriod}
                  className="py-3.5 px-6 border border-red-300 text-red-700 bg-red-50 hover:bg-red-100 text-xs font-bold rounded-xl flex-1 transition cursor-pointer"
                >
                  Annuler la validation (Déverrouiller)
                </button>
              )}
              <button
                type="button"
                onClick={() => setPayslipModalEmpId(employees[0]?.id || 1)}
                className="py-3.5 px-6 border border-[#E8E8E6] bg-white hover:bg-[#F4F4F5] text-xs font-bold rounded-xl text-[#1F1F1E] cursor-pointer flex items-center justify-center gap-2"
              >
                <FileText className="w-4 h-4 text-[#253e87]" />
                <span>Générer les bulletins PDF 📄</span>
              </button>
            </div>
          </div>
        )}
      </main>

      {/* MODALE BULLETIN OFFICIEL EXAMINÉ */}
      {payslipModalEmpId && payslipEmp && payslipCalc && (
        <PayslipModal
          employee={payslipEmp}
          computedPay={payslipCalc}
          period={currentPeriod}
          isOpen={Boolean(payslipModalEmpId)}
          onClose={() => setPayslipModalEmpId(null)}
          onShowToast={onShowToast}
        />
      )}
    </div>
  );
};
