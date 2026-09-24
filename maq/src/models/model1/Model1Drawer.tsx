import React, { useState } from 'react';
import { Employee, EmployeeElement, ComputedEmployeePay } from '../../data/employeeTypes';
import { reverseCalculateNetToSurSalary } from '../../logic/payrollCalculator';
import { FormattedAmountInput } from '../../components/FormattedAmountInput';
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

interface Model1DrawerProps {
  employee: Employee | null;
  computedPay: ComputedEmployeePay | null;
  isOpen: boolean;
  isReadOnly: boolean;
  onClose: () => void;
  onSave: (updatedEmployee: Employee) => void;
  onOpenPayslip: (employee: Employee) => void;
  onShowToast: (msg: string) => void;
}

export const Model1Drawer: React.FC<Model1DrawerProps> = ({
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
  const [targetNetInput, setTargetNetInput] = useState<number>(500000);

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
    const targetVal = targetNetInput;
    if (!targetVal || isNaN(targetVal) || targetVal <= 0) {
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
        id: `sursalaire-${Date.now()}`,
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
    onShowToast(`Sursalaire calculé (+${neededSurSalary.toLocaleString('fr-FR')} F) pour un Net de ${targetVal.toLocaleString('fr-FR')} F`);
  };

  return (
    <>
      {/* Backdrop */}
      <div
        className="fixed inset-0 bg-black/30 backdrop-blur-xs z-40 transition-opacity"
        onClick={onClose}
      />

      {/* Drawer Container (50% desktop width) */}
      <div
        id="employee-drawer"
        className="fixed inset-y-0 right-0 w-full md:w-1/2 bg-white border-l border-[#E8E8E6] shadow-2xl z-50 flex flex-col justify-between font-sans text-sm font-bold text-[#1F1F1E] animate-in slide-in-from-right duration-200"
      >
        {/* Header */}
        <div className="p-6 border-b border-[#E8E8E6] flex justify-between items-start bg-white">
          <div className="space-y-1">
            <div className="flex items-center gap-3">
              <span id="drawer-matricule" className="text-xs bg-[#253e87]/10 text-[#253e87] px-2.5 py-1 rounded font-mono font-bold">
                {employee.matricule}
              </span>
              <h2 id="drawer-name" className="text-lg font-bold text-[#1F1F1E]">
                {employee.name}
              </h2>
            </div>
            <p id="drawer-dept" className="text-xs text-[#6B6B6B]">
              Département : <span className="text-[#1F1F1E] font-bold">{employee.dept}</span> •{' '}
              {employee.maritalStatus === 'married' ? 'Marié(e)' : 'Célibataire'} ({employee.parts ?? 1} part{(employee.parts ?? 1) > 1 ? 's' : ''})
              {employee.seniority && (
                <> • Ancienneté : <span className="text-[#1F1F1E] font-bold">{employee.seniority}</span> (seuil 2 ans non atteint : 0 F de prime)</>
              )}
            </p>
          </div>

          <button
            id="close-drawer"
            type="button"
            onClick={onClose}
            className="p-2 rounded-lg hover:bg-[#F0F0EE] text-[#6B6B6B] hover:text-[#1F1F1E] transition cursor-pointer"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Content Scrollable */}
        <div className="p-6 overflow-y-auto flex-1 space-y-6">
          {/* Anomaly banner if any */}
          {employee.status === 'anomaly' && employee.alert && (
            <div className="bg-[#253e87]/10 border border-[#253e87]/30 rounded-xl p-4 flex items-start gap-3 text-xs font-bold text-[#253e87]">
              <AlertTriangle className="w-4 h-4 shrink-0 text-[#253e87] mt-0.5" />
              <div>
                <strong className="block text-xs uppercase tracking-wide">Anomalie détectée</strong>
                <p className="font-normal text-xs text-[#1F1F1E] mt-0.5">{employee.alert}</p>
              </div>
            </div>
          )}

          {/* Section 1 : Présence & Temps */}
          <section className="space-y-3">
            <h3 className="text-xs uppercase tracking-wider text-[#6B6B6B] font-bold flex items-center gap-2">
              <Clock className="w-4 h-4 text-[#253e87]" />
              <span>Section 1 — Présence & Temps (Pointeuse)</span>
            </h3>

            <div className="border border-[#E8E8E6] rounded-xl p-4 bg-[#FAFAFA] space-y-3">
              <div className="flex items-center justify-between">
                <div>
                  <label htmlFor="drawer-days-input" className="block text-xs text-[#6B6B6B] font-bold">
                    Jours travaillés (base 30j calendaires)
                  </label>
                  <span className="text-[11px] text-[#6B6B6B]">
                    {days < 30 ? (
                      <span className="text-[#253e87] font-bold">
                        Prorata appliqué : {days}/30 jours ({30 - days}j d'absence)
                      </span>
                    ) : (
                      'Mois complet (30/30j)'
                    )}
                  </span>
                </div>
                <div className="flex items-center gap-2">
                  <input
                    id="drawer-days-input"
                    type="number"
                    min="0"
                    max="30"
                    disabled={isReadOnly}
                    value={days}
                    onChange={(e) => setDays(Math.max(0, Math.min(30, parseInt(e.target.value, 10) || 0)))}
                    className="w-20 text-center border border-[#E8E8E6] rounded-lg py-1.5 px-2 font-mono font-bold text-sm bg-white text-[#1F1F1E] focus:outline-none focus:border-[#253e87]"
                  />
                  <span className="text-xs font-bold text-[#6B6B6B]">jours</span>
                </div>
              </div>

              {days < 30 && (
                <div className="pt-2 border-t border-[#E8E8E6] flex justify-between text-xs font-bold">
                  <span className="text-[#6B6B6B]">Salaire de base proratisé :</span>
                  <span className="font-mono text-[#253e87]">
                    {Math.round((base * days) / 30).toLocaleString('fr-FR')} FCFA
                  </span>
                </div>
              )}
            </div>
          </section>

          {/* Section 2 : Éléments, Primes & Retenues */}
          <section className="space-y-3">
            <div className="flex justify-between items-center">
              <h3 className="text-xs uppercase tracking-wider text-[#6B6B6B] font-bold flex items-center gap-2">
                <Coins className="w-4 h-4 text-[#253e87]" />
                <span>Section 2 — Éléments, Primes & Retenues</span>
              </h3>
            </div>

            {/* Salaire de base contractuel */}
            <div className="border border-[#E8E8E6] rounded-xl p-4 bg-[#FAFAFA] flex items-center justify-between">
              <div>
                <p className="text-xs font-bold text-[#1F1F1E]">Salaire de Base Contractuel</p>
                <span className="text-[10px] text-[#6B6B6B]">Élément obligatoire soumis à cotisation</span>
              </div>
              <div className="flex items-center gap-2">
                <FormattedAmountInput
                  disabled={isReadOnly}
                  value={base}
                  onChange={(val) => setBase(val)}
                  currencySuffix="FCFA"
                  className="w-36 text-right border border-[#E8E8E6] rounded-lg py-1.5 px-3 font-montserrat font-bold text-sm bg-white text-[#1F1F1E] focus:outline-none focus:border-[#253e87]"
                />
              </div>
            </div>

            {/* Liste des éléments dynamiques */}
            <div className="space-y-2">
              {elements.map((el, idx) => (
                <div
                  key={el.id}
                  className="border border-[#E8E8E6] rounded-xl p-3 bg-white flex items-center justify-between gap-3 text-xs"
                >
                  <div className="flex-1 space-y-1">
                    <div className="flex items-center gap-2">
                      <span className="font-bold text-[#1F1F1E]">{el.label}</span>
                      {el.locked && (
                        <span className="text-[10px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 flex items-center gap-1 font-mono">
                          <Lock className="w-2.5 h-2.5" /> {el.schedule}
                        </span>
                      )}
                    </div>
                    <div className="flex items-center gap-1.5 text-[10px] text-[#6B6B6B]">
                      <span className={`px-1.5 py-0.2 rounded font-bold ${el.imposable ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700'}`}>
                        {el.imposable ? 'Impôt OUI' : 'Impôt NON'}
                      </span>
                      <span>•</span>
                      <span className={`px-1.5 py-0.2 rounded font-bold ${el.social ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700'}`}>
                        {el.social ? 'Soc OUI' : 'Soc NON'}
                      </span>
                      {el.type === 'transport' && (
                        <span className="text-[#253e87] font-bold">
                          (Exonéré max 30 000 F)
                        </span>
                      )}
                    </div>
                  </div>

                  <div className="flex items-center gap-2">
                    <span className={`font-bold ${el.gain ? 'text-emerald-700' : 'text-red-700'}`}>
                      {el.gain ? '+' : '–'}
                    </span>
                    <FormattedAmountInput
                      disabled={isReadOnly || el.locked}
                      value={el.amount}
                      onChange={(newAmt) => {
                        const updated = [...elements];
                        updated[idx] = { ...updated[idx], amount: newAmt };
                        setElements(updated);
                      }}
                      currencySuffix="F"
                      className="w-28 text-right border border-[#E8E8E6] rounded-lg py-1 px-2.5 font-montserrat font-bold text-xs bg-[#FAFAFA] text-[#1F1F1E] focus:outline-none focus:border-[#253e87] disabled:bg-gray-100"
                    />

                    {!el.locked && !isReadOnly && (
                      <button
                        type="button"
                        onClick={() => handleRemoveElement(idx)}
                        className="p-1 text-[#6B6B6B] hover:text-red-600 transition cursor-pointer"
                        title="Supprimer cet élément"
                      >
                        <X className="w-4 h-4" />
                      </button>
                    )}
                  </div>
                </div>
              ))}
            </div>

            {/* Menu catalogue d'accessoires de paie */}
            {!isReadOnly && (
              <div className="relative pt-1">
                <select
                  id="catalog-select"
                  onChange={handleAddElementCatalog}
                  defaultValue=""
                  className="w-full border border-dashed border-[#253e87] text-[#253e87] bg-[#FAFAFA] hover:bg-blue-50/50 rounded-xl py-2.5 px-4 text-xs font-bold focus:outline-none cursor-pointer appearance-none transition"
                >
                  <option value="" disabled>
                    ＋ Ajouter une prime, retenue ou accessoire du catalogue...
                  </option>
                  <optgroup label="Primes & Indemnités Courantes">
                    <option value="prime_transport">Prime de transport (30 000 FCFA)</option>
                    <option value="prime_perf">Prime de performance (45 000 FCFA)</option>
                    <option value="prime_logement">Prime de logement (25 000 FCFA)</option>
                    <option value="prime_exceptionnelle">Prime exceptionnelle (saisie libre)</option>
                    <option value="conge_indemnite">Indemnité de Congé payé</option>
                  </optgroup>
                  <optgroup label="Heures Supplémentaires">
                    <option value="hs_15">Heures Sup Majorées +15% (de jour)</option>
                    <option value="hs_50">Heures Sup Majorées +50% (de nuit)</option>
                    <option value="hs_100">Heures Sup Majorées +100% (dimanche/férié)</option>
                  </optgroup>
                  <optgroup label="Avantages & Frais">
                    <option value="avantage_logement">Avantage en nature Logement</option>
                    <option value="remboursement_frais">Remboursement de frais professionnels (non imposable)</option>
                  </optgroup>
                  <optgroup label="Retenues & Avances">
                    <option value="retenue_avance">Avance sur salaire (50 000 FCFA)</option>
                    <option value="pret_vehicule">Prêt Véhicule (Mois 3/6 - 50 000 FCFA)</option>
                    <option value="retenue_divers">Autre retenue sur salaire</option>
                  </optgroup>
                </select>
                <div className="absolute right-4 top-4 pointer-events-none text-[#253e87]">
                  <ChevronDown className="w-4 h-4" />
                </div>
              </div>
            )}
          </section>

          {/* Section 3 : Simulation du Bulletin & Rétro-calcul */}
          <section className="space-y-3">
            <div className="flex justify-between items-center">
              <h3 className="text-xs uppercase tracking-wider text-[#6B6B6B] font-bold flex items-center gap-2">
                <Calculator className="w-4 h-4 text-[#253e87]" />
                <span>Section 3 — Simulation Bulletin & Rétro-calcul 🎯</span>
              </h3>

              <button
                type="button"
                onClick={() => onOpenPayslip(employee)}
                className="text-xs text-[#253e87] hover:underline font-bold flex items-center gap-1 cursor-pointer"
              >
                <FileText className="w-3.5 h-3.5" />
                <span>Voir Bulletin Officiel</span>
              </button>
            </div>

            {/* Décomposition financière */}
            <div className="border border-[#E8E8E6] rounded-xl p-4 bg-[#FAFAFA] space-y-2 text-xs">
              <div className="flex justify-between text-neutral-700">
                <span>Brut Imposable</span>
                <span className="font-montserrat font-bold text-[#1F1F1E]">
                  {computedPay.brut.toLocaleString('fr-FR')} FCFA
                </span>
              </div>
              <div className="flex justify-between text-neutral-700">
                <span>Cotisations Sociales (CNPS 6,3% + CMU)</span>
                <span className="font-montserrat text-neutral-800">
                  –{computedPay.totalCotis.toLocaleString('fr-FR')} FCFA
                </span>
              </div>
              <div className="flex justify-between text-neutral-700">
                <span>Impôt sur Salaires (ITS 2024 après RICF)</span>
                <span className="font-montserrat text-neutral-800">
                  –{computedPay.itsNet.toLocaleString('fr-FR')} FCFA
                </span>
              </div>
              {computedPay.totalRetenues > 0 && (
                <div className="flex justify-between text-neutral-700">
                  <span>Prêts & Retenues</span>
                  <span className="font-montserrat text-neutral-800">
                    –{computedPay.totalRetenues.toLocaleString('fr-FR')} FCFA
                  </span>
                </div>
              )}

              <div className="border-t border-[#E8E8E6] pt-2 flex justify-between items-center font-bold text-sm">
                <span className="text-[#253e87]">Net à payer simulé :</span>
                <span className="font-montserrat text-base text-[#253e87]">
                  {computedPay.net.toLocaleString('fr-FR')} FCFA
                </span>
              </div>
            </div>

            {/* Rétro-calcul Net cible 🎯 */}
            {!isReadOnly && (
              <div className="border border-[#253e87]/20 rounded-xl p-4 bg-[#253e87]/5 space-y-2">
                <div className="flex justify-between items-center">
                  <div>
                    <h4 className="text-xs font-bold text-[#253e87] flex items-center gap-1.5">
                      <span>Rétro-calcul automatique (Cible Net)</span>
                    </h4>
                    <p className="text-[11px] text-[#6B6B6B]">
                      Ajuste instantanément le sursalaire imposable pour garantir un salaire net précis.
                    </p>
                  </div>
                  <button
                    type="button"
                    onClick={() => setShowTargetModal(true)}
                    className="px-3 py-1.5 bg-[#253e87] text-white hover:bg-[#1c306d] text-xs font-bold rounded-lg transition shadow-xs cursor-pointer"
                  >
                    Définir Net Cible...
                  </button>
                </div>
              </div>
            )}
          </section>
        </div>

        {/* Footer Drawer */}
        <div className="p-6 border-t border-[#E8E8E6] flex gap-3 bg-white">
          <button
            id="drawer-save-btn"
            type="button"
            disabled={isReadOnly}
            onClick={handleSave}
            className="flex-1 py-3 uppercase tracking-wider bg-[#253e87] text-white hover:bg-[#1c306d] font-bold rounded-lg transition disabled:opacity-50 cursor-pointer shadow-xs"
          >
            Enregistrer & Recalculer
          </button>
          <button
            type="button"
            onClick={onClose}
            className="py-3 px-6 uppercase bg-neutral-200 text-[#1F1F1E] hover:bg-neutral-300 font-bold rounded-lg transition cursor-pointer"
          >
            Fermer
          </button>
        </div>
      </div>

      {/* Target Net Modal */}
      {showTargetModal && (
        <div className="fixed inset-0 bg-black/40 z-60 flex items-center justify-center p-4">
          <div className="bg-white border border-[#E8E8E6] rounded-2xl max-w-sm w-full p-6 space-y-4 shadow-2xl text-sm font-bold">
            <div className="flex justify-between items-center border-b border-[#E8E8E6] pb-3">
              <h3 className="font-bold text-sm text-[#253e87] uppercase flex items-center gap-2">
                <Calculator className="w-4 h-4" />
                Rétro-calcul — Cible Net
              </h3>
              <button
                type="button"
                onClick={() => setShowTargetModal(false)}
                className="text-[#6B6B6B] hover:text-[#1F1F1E] p-1"
              >
                <X className="w-4 h-4" />
              </button>
            </div>

            <form onSubmit={handleApplyTargetNet} className="space-y-4">
              <div>
                <label className="block text-xs text-[#6B6B6B] mb-1 font-bold">
                  Salaire Net à Payer Souhaité (FCFA) :
                </label>
                <FormattedAmountInput
                  value={targetNetInput}
                  onChange={setTargetNetInput}
                  currencySuffix="FCFA"
                  className="w-full border border-[#E8E8E6] rounded-xl px-4 py-2.5 text-base font-montserrat font-bold focus:outline-none focus:border-[#253e87] text-[#1F1F1E] bg-[#FAFAFA]"
                />
              </div>

              <p className="text-[11px] text-[#6B6B6B] leading-relaxed">
                Le système déduira automatiquement les cotisations CNPS (6,3%), CMU (500 F) et le barème progressif ITS 2024 pour générer le sursalaire exact.
              </p>

              <div className="flex gap-2 pt-2">
                <button
                  type="submit"
                  className="flex-1 py-2.5 bg-[#253e87] text-white hover:bg-[#1c306d] font-bold rounded-lg text-xs uppercase cursor-pointer"
                >
                  Calculer & Appliquer
                </button>
                <button
                  type="button"
                  onClick={() => setShowTargetModal(false)}
                  className="py-2.5 px-4 bg-neutral-200 text-[#1F1F1E] hover:bg-neutral-300 font-bold rounded-lg text-xs uppercase cursor-pointer"
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
