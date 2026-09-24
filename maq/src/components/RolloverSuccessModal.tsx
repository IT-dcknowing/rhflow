import React from 'react';
import { motion, AnimatePresence } from 'motion/react';
import { CheckCircle2, ArrowRight, ShieldCheck, RefreshCw, Users } from 'lucide-react';
import { RolloverSummary } from '../logic/periodManager';

interface RolloverSuccessModalProps {
  isOpen: boolean;
  summary: RolloverSummary | null;
  onClose: () => void;
  onNavigateToProcessing: () => void;
}

export const RolloverSuccessModal: React.FC<RolloverSuccessModalProps> = ({
  isOpen,
  summary,
  onClose,
  onNavigateToProcessing,
}) => {
  if (!isOpen || !summary) return null;

  return (
    <AnimatePresence>
      <div
        className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
        role="dialog"
        aria-modal="true"
      >
        <motion.div
          initial={{ opacity: 0, scale: 0.92, y: 20 }}
          animate={{ opacity: 1, scale: 1, y: 0 }}
          exit={{ opacity: 0, scale: 0.95, y: 16 }}
          className="bg-white rounded-2xl shadow-2xl border border-[#E8E8E6] w-full max-w-lg overflow-hidden flex flex-col"
        >
          {/* BANDEAU HAUT */}
          <div className="bg-emerald-700 text-white p-6 flex flex-col items-center text-center">
            <div className="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center mb-3">
              <CheckCircle2 className="w-8 h-8 text-white" />
            </div>
            <h3 className="text-xl font-bold tracking-tight">
              {summary.targetPeriodName} initialisé avec succès !
            </h3>
            <p className="text-xs text-white/90 mt-1 max-w-sm">
              La paie du mois de {summary.previousPeriodName} a été clôturée et les données contractuelles ont été reportées.
            </p>
          </div>

          {/* DÉTAIL DU REPORT */}
          <div className="p-6 space-y-4">
            <div className="grid grid-cols-3 gap-3 text-center">
              <div className="p-3 rounded-xl bg-blue-50 border border-blue-100">
                <p className="text-[11px] font-bold text-blue-900 uppercase">Salariés</p>
                <p className="font-montserrat font-bold text-2xl text-[#1e3a8a] mt-0.5">
                  {summary.carriedOverCount}
                </p>
                <p className="text-[10px] text-blue-700">reportés à 100%</p>
              </div>

              <div className="p-3 rounded-xl bg-emerald-50 border border-emerald-100">
                <p className="text-[11px] font-bold text-emerald-900 uppercase">Échéances</p>
                <p className="font-montserrat font-bold text-2xl text-emerald-700 mt-0.5">
                  {summary.loansUpdatedCount} prêt
                </p>
                <p className="text-[10px] text-emerald-700">avancé (ex: 7/10)</p>
              </div>

              <div className="p-3 rounded-xl bg-purple-50 border border-purple-100">
                <p className="text-[11px] font-bold text-purple-900 uppercase">Ancienneté</p>
                <p className="font-montserrat font-bold text-2xl text-purple-700 mt-0.5">
                  +1 mois
                </p>
                <p className="text-[10px] text-purple-700">recalcul auto</p>
              </div>
            </div>

            {/* CONFIRMATION DES GARDE-FOUS */}
            <div className="rounded-xl border border-neutral-200 bg-neutral-50 p-3.5 space-y-2 text-xs">
              <p className="font-bold text-neutral-800 flex items-center gap-1.5">
                <ShieldCheck className="w-4 h-4 text-emerald-600" />
                <span>Garde-fous & Nettoyage mensuel validés :</span>
              </p>
              <ul className="text-[11px] space-y-1 text-neutral-600 list-disc list-inside">
                <li>Le mois de {summary.previousPeriodName} est désormais verrouillé (statut <strong>Payée</strong>).</li>
                <li>Jours travaillés réinitialisés à 30 jours pour l'ensemble des collaborateurs.</li>
                <li>Heures supplémentaires, absences et primes exceptionnelles remises à zéro.</li>
                <li>Plafond transport Abidjan (30 000 FCFA) et barème fiscal ITS vérifiés conformes.</li>
              </ul>
            </div>
          </div>

          {/* ACTIONS */}
          <div className="p-4 bg-neutral-50 border-t border-[#E8E8E6] flex items-center justify-end gap-3">
            <button
              type="button"
              onClick={onClose}
              className="px-4 py-2.5 rounded-xl border border-[#E8E8E6] bg-white text-[#1F1F1E] hover:bg-neutral-100 text-xs font-bold transition cursor-pointer"
            >
              Rester sur l'Accueil Paie
            </button>
            <button
              type="button"
              onClick={() => {
                onClose();
                onNavigateToProcessing();
              }}
              className="px-5 py-2.5 rounded-xl bg-[#1e3a8a] text-white hover:bg-[#1e3a8a]/90 text-xs font-bold transition flex items-center gap-2 cursor-pointer shadow-sm"
            >
              <span>Accéder au Traitement de paie</span>
              <ArrowRight className="w-4 h-4" />
            </button>
          </div>
        </motion.div>
      </div>
    </AnimatePresence>
  );
};
