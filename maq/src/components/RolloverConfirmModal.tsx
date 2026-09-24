import React, { useState } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import {
  RefreshCw,
  CheckCircle2,
  XCircle,
  AlertTriangle,
  ShieldCheck,
  Calendar,
  Lock,
  ArrowRight,
  X,
  Info,
  ChevronRight,
} from 'lucide-react';
import { Period } from '../data/employeeTypes';
import { RolloverSummary } from '../logic/periodManager';

interface RolloverConfirmModalProps {
  isOpen: boolean;
  previousPeriod: Period | null;
  targetPeriod: Period;
  onClose: () => void;
  onConfirmRollover: (forceAdmin?: boolean) => void;
}

export const RolloverConfirmModal: React.FC<RolloverConfirmModalProps> = ({
  isOpen,
  previousPeriod,
  targetPeriod,
  onClose,
  onConfirmRollover,
}) => {
  const [isForceAdmin, setIsForceAdmin] = useState(false);
  const [isProcessing, setIsProcessing] = useState(false);

  if (!isOpen || !previousPeriod) return null;

  const isPreviousClosed = previousPeriod.status === 'payee' || previousPeriod.status === 'validée';
  const canProceed = isPreviousClosed || isForceAdmin;

  const handleExecute = () => {
    setIsProcessing(true);
    setTimeout(() => {
      onConfirmRollover(isForceAdmin);
      setIsProcessing(false);
    }, 600);
  };

  return (
    <AnimatePresence>
      <div
        id="rollover-modal-backdrop"
        className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
        role="dialog"
        aria-modal="true"
        aria-labelledby="rollover-modal-title"
      >
        <motion.div
          initial={{ opacity: 0, scale: 0.95, y: 16 }}
          animate={{ opacity: 1, scale: 1, y: 0 }}
          exit={{ opacity: 0, scale: 0.95, y: 16 }}
          transition={{ duration: 0.2 }}
          className="bg-white rounded-2xl shadow-2xl border border-[#E8E8E6] w-full max-w-xl overflow-hidden flex flex-col max-h-[90vh]"
        >
          {/* HEADER MODALE */}
          <div className="bg-[#1e3a8a] text-white p-5 flex items-start justify-between">
            <div className="flex items-center gap-3">
              <div className="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center text-white shrink-0">
                <RefreshCw className={`w-5 h-5 ${isProcessing ? 'animate-spin' : ''}`} />
              </div>
              <div>
                <h3 id="rollover-modal-title" className="text-lg font-bold tracking-tight">
                  Reprendre la paie de {previousPeriod.name} ?
                </h3>
                <p className="text-xs text-white/80 mt-0.5">
                  Report intelligent vers la période cible <span className="font-bold text-white">{targetPeriod.name}</span>
                </p>
              </div>
            </div>

            <button
              type="button"
              onClick={onClose}
              className="text-white/70 hover:text-white p-1 rounded-lg hover:bg-white/10 transition cursor-pointer"
              aria-label="Fermer"
            >
              <X className="w-5 h-5" />
            </button>
          </div>

          {/* CONTENU PRINCIPAL */}
          <div className="p-6 space-y-5 overflow-y-auto">
            {/* GARDE-FOU BLOQUANT SI NON CLÔTURÉ */}
            {!isPreviousClosed && (
              <div className="p-4 rounded-xl border border-amber-300 bg-amber-50 text-amber-900 flex items-start gap-3">
                <AlertTriangle className="w-5 h-5 text-amber-600 shrink-0 mt-0.5" />
                <div className="space-y-2 text-xs flex-1">
                  <p className="font-bold text-sm text-amber-950">
                    Garde-fou actif : période précédente non clôturée
                  </p>
                  <p className="leading-relaxed">
                    Le mois de <strong>{previousPeriod.name}</strong> doit d'abord être clôturé (statut <strong>Payée</strong>) pour garantir l'intégrité comptable et éviter les doublons.
                  </p>
                  <div className="pt-1 flex items-center gap-2">
                    <label className="inline-flex items-center gap-2 cursor-pointer text-xs font-semibold text-amber-900">
                      <input
                        type="checkbox"
                        checked={isForceAdmin}
                        onChange={(e) => setIsForceAdmin(e.target.checked)}
                        className="rounded border-amber-400 text-[#1e3a8a] focus:ring-[#1e3a8a] w-4 h-4 cursor-pointer"
                      />
                      <span>Autoriser le forçage administrateur pour cette simulation</span>
                    </label>
                  </div>
                </div>
              </div>
            )}

            {/* GRILLE COMPARATIVE DU REPORT INTELLIGENT */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              {/* CE QUI EST REPORTÉ */}
              <div className="rounded-xl border border-emerald-200 bg-emerald-50/50 p-4 space-y-2.5">
                <div className="flex items-center gap-2 text-emerald-800 font-bold text-xs uppercase tracking-wider">
                  <CheckCircle2 className="w-4 h-4 text-emerald-600" />
                  <span>Éléments reportés</span>
                </div>
                <ul className="text-xs space-y-2 text-emerald-950">
                  <li className="flex items-center gap-2">
                    <span className="text-emerald-600 font-bold">✅</span>
                    <span><strong>Salaires de base</strong> (contrat)</span>
                  </li>
                  <li className="flex items-center gap-2">
                    <span className="text-emerald-600 font-bold">✅</span>
                    <span><strong>Sursalaires</strong> (structure stable)</span>
                  </li>
                  <li className="flex items-center gap-2">
                    <span className="text-emerald-600 font-bold">✅</span>
                    <span><strong>Primes de transport</strong> (plafond vérifié)</span>
                  </li>
                  <li className="flex items-center gap-2">
                    <span className="text-emerald-600 font-bold">✅</span>
                    <span><strong>Avantages en nature</strong> (logement, etc.)</span>
                  </li>
                  <li className="flex items-center gap-2">
                    <span className="text-emerald-600 font-bold">✅</span>
                    <span><strong>Prêts en cours</strong> (échéances décrémentées)</span>
                  </li>
                  <li className="flex items-center gap-2">
                    <span className="text-emerald-600 font-bold">✅</span>
                    <span><strong>Parts & situation familiale</strong></span>
                  </li>
                </ul>
              </div>

              {/* CE QUI EST RÉINITIALISÉ */}
              <div className="rounded-xl border border-rose-200 bg-rose-50/50 p-4 space-y-2.5">
                <div className="flex items-center gap-2 text-rose-800 font-bold text-xs uppercase tracking-wider">
                  <XCircle className="w-4 h-4 text-rose-600" />
                  <span>Éléments réinitialisés</span>
                </div>
                <ul className="text-xs space-y-2 text-rose-950">
                  <li className="flex items-center gap-2">
                    <span className="text-rose-600 font-bold">❌</span>
                    <span><strong>Jours travaillés</strong> (remis à 30j)</span>
                  </li>
                  <li className="flex items-center gap-2">
                    <span className="text-rose-600 font-bold">❌</span>
                    <span><strong>Heures supplémentaires</strong> (à zéro)</span>
                  </li>
                  <li className="flex items-center gap-2">
                    <span className="text-rose-600 font-bold">❌</span>
                    <span><strong>Congés & absences</strong> (à zéro)</span>
                  </li>
                  <li className="flex items-center gap-2">
                    <span className="text-rose-600 font-bold">❌</span>
                    <span><strong>Primes exceptionnelles</strong> (à zéro)</span>
                  </li>
                  <li className="flex items-center gap-2">
                    <span className="text-rose-600 font-bold">❌</span>
                    <span><strong>Retenues ponctuelles & avances</strong></span>
                  </li>
                  <li className="flex items-center gap-2">
                    <span className="text-rose-600 font-bold">❌</span>
                    <span><strong>Alertes de pointage</strong> résolues</span>
                  </li>
                </ul>
              </div>
            </div>

            {/* GARDE-FOUS ET CONTRÔLES FISCAUX AUTOMATIQUES */}
            <div className="rounded-xl border border-[#E8E8E6] bg-[#FAFAFA] p-3.5 space-y-2 text-xs">
              <p className="font-bold text-[#1F1F1E] flex items-center gap-1.5">
                <ShieldCheck className="w-4 h-4 text-[#1e3a8a]" />
                <span>Garde-fous & Barèmes légaux appliqués</span>
              </p>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px] text-[#555]">
                <div className="flex items-center gap-1.5">
                  <span className="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                  <span><strong>Prime d'ancienneté</strong> : recalculée (+1 mois)</span>
                </div>
                <div className="flex items-center gap-1.5">
                  <span className="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                  <span><strong>Plafond transport Abidjan</strong> : vérifié à 30 000 F</span>
                </div>
                <div className="flex items-center gap-1.5">
                  <span className="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                  <span><strong>Barème ITS 2024</strong> : barème réformé conforme</span>
                </div>
                <div className="flex items-center gap-1.5">
                  <span className="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                  <span><strong>Prêt Diallo Ibrahim</strong> : échéance 7 / 10 reportée</span>
                </div>
              </div>
            </div>
          </div>

          {/* FOOTER ACTIONS */}
          <div className="bg-[#FAFAFA] border-t border-[#E8E8E6] px-6 py-4 flex items-center justify-between gap-3">
            <button
              type="button"
              onClick={onClose}
              disabled={isProcessing}
              className="px-4 py-2.5 rounded-xl border border-[#E8E8E6] bg-white text-[#1F1F1E] hover:bg-neutral-100 text-xs font-bold transition cursor-pointer"
            >
              Annuler
            </button>

            <button
              type="button"
              onClick={handleExecute}
              disabled={!canProceed || isProcessing}
              className={`px-5 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 shadow-sm ${
                canProceed && !isProcessing
                  ? 'bg-[#1e3a8a] text-white hover:bg-[#1e3a8a]/90 cursor-pointer'
                  : 'bg-neutral-300 text-neutral-500 cursor-not-allowed'
              }`}
            >
              <span>{isProcessing ? 'Report en cours...' : 'Reprendre →'}</span>
              {!isProcessing && <ArrowRight className="w-4 h-4" />}
            </button>
          </div>
        </motion.div>
      </div>
    </AnimatePresence>
  );
};
