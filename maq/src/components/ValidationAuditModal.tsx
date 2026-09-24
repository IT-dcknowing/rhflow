import React, { useState } from 'react';
import { Period } from '../types/rhflow';
import { X, Lock, CheckCircle2 } from 'lucide-react';

interface ValidationAuditModalProps {
  isOpen: boolean;
  period: Period;
  totalNet: number;
  anomaliesCount: number;
  not30DaysCount: number;
  totalEmployeesCount: number;
  onClose: () => void;
  onConfirm: () => void;
}

export const ValidationAuditModal: React.FC<ValidationAuditModalProps> = ({
  isOpen,
  period,
  totalNet,
  anomaliesCount,
  not30DaysCount,
  totalEmployeesCount,
  onClose,
  onConfirm,
}) => {
  if (!isOpen) return null;

  const [check1, setCheck1] = useState<boolean>(true);
  const [check2, setCheck2] = useState<boolean>(true);
  const [check3, setCheck3] = useState<boolean>(true);

  return (
    <div
      id="validation-modal"
      className="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4"
    >
      <div className="bg-white border border-[#E8E8E6] rounded-2xl max-w-lg w-full p-8 space-y-6 shadow-2xl text-sm font-bold relative">
        <div className="flex justify-between items-center border-b border-[#E8E8E6] pb-4">
          <h3 className="font-bold text-base uppercase text-[#1F1F1E] flex items-center gap-2.5">
            <Lock className="w-5 h-5 text-[#253e87]" />
            Clôture & Audit de Paie
          </h3>
          <button
            onClick={onClose}
            className="text-base font-bold text-[#1F1F1E] hover:text-neutral-500 cursor-pointer"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        <div id="validation-modal-body" className="space-y-4 text-[#1F1F1E]">
          <div className="border border-[#E8E8E6] rounded-xl p-4 bg-[#FAFAFA] space-y-2.5 font-bold text-[#1F1F1E]">
            <p className="font-bold">Période : {period.name}</p>
            <p>
              Effectif total : <strong>{totalEmployeesCount} salariés</strong>
            </p>
            <p>
              Masse nette totale :{' '}
              <strong className="font-montserrat text-[#253e87]">
                {totalNet.toLocaleString('fr-FR')} FCFA
              </strong>
            </p>
          </div>

          <div className="space-y-2.5 pt-2 text-xs">
            <p className="font-bold uppercase text-[#6B6B6B]">Checklist d'audit obligatoire :</p>

            <label className="flex items-center gap-2.5 cursor-pointer">
              <input
                type="checkbox"
                checked={check1}
                onChange={(e) => setCheck1(e.target.checked)}
                className="w-4 h-4 rounded border-[#E8E8E6] text-[#253e87] focus:ring-[#253e87]"
              />
              <span>
                Aucune anomalie critique non résolue ({anomaliesCount} détectée{anomaliesCount > 1 ? 's' : ''})
              </span>
            </label>

            <label className="flex items-center gap-2.5 cursor-pointer">
              <input
                type="checkbox"
                checked={check2}
                onChange={(e) => setCheck2(e.target.checked)}
                className="w-4 h-4 rounded border-[#E8E8E6] text-[#253e87] focus:ring-[#253e87]"
              />
              <span>
                Salariés à jours ajustés ({not30DaysCount} avec &lt; 30j)
              </span>
            </label>

            <label className="flex items-center gap-2.5 cursor-pointer">
              <input
                type="checkbox"
                checked={check3}
                onChange={(e) => setCheck3(e.target.checked)}
                className="w-4 h-4 rounded border-[#E8E8E6] text-[#253e87] focus:ring-[#253e87]"
              />
              <span>Cohérence globale de la masse salariale validée</span>
            </label>
          </div>

          <p className="text-xs text-[#6B6B6B] pt-2">
            En confirmant, le mois passera au statut <strong>Validée</strong>, les bulletins seront verrouillés et le virement bancaire sera généré.
          </p>
        </div>

        <div className="border-t border-[#E8E8E6] pt-4 flex gap-3">
          <button
            id="confirm-validation-btn"
            type="button"
            onClick={onConfirm}
            className="flex-1 justify-center py-3.5 uppercase tracking-wider bg-[#253e87] text-white hover:bg-[#1c306d] font-bold rounded-lg transition cursor-pointer flex items-center gap-2"
          >
            <CheckCircle2 className="w-4 h-4" />
            <span>Confirmer le virement & clôturer</span>
          </button>
          <button
            type="button"
            onClick={onClose}
            className="py-3.5 uppercase px-5 bg-neutral-200 text-[#1F1F1E] hover:bg-neutral-300 font-bold rounded-lg transition cursor-pointer"
          >
            Annuler
          </button>
        </div>
      </div>
    </div>
  );
};
