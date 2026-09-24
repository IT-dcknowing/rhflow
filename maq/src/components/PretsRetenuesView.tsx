import React, { useState } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import {
  HandCoins,
  AlertTriangle,
  ArrowLeft,
  Calculator,
  CheckCircle2,
  Calendar,
  X,
  CreditCard,
  Edit2,
  PauseCircle,
  PlayCircle,
} from 'lucide-react';
import { Period } from '../data/employeeTypes';

interface PretsRetenuesViewProps {
  currentPeriod: Period;
  onNavigateHome: () => void;
  onNavigateProcessing: () => void;
}

interface PretRow {
  id: number;
  salarie: string;
  intitule: string;
  capital: string;
  echeances: string;
  mensualite: string;
  statut: 'applique' | 'suspendu' | 'insuffisance';
}

interface RetenueRow {
  id: number;
  salarie: string;
  sbi: string;
  sbs: string;
  cnps: string;
  cmu: string;
  autres: string;
  statut: 'applique' | 'suspendu';
}

export const PretsRetenuesView: React.FC<PretsRetenuesViewProps> = ({
  currentPeriod,
  onNavigateHome,
  onNavigateProcessing,
}) => {
  // Tableau 1 - Prêts & échéanciers (avec actions interactives)
  const [prets, setPrets] = useState<PretRow[]>([
    {
      id: 4,
      salarie: 'Diallo Ibrahim',
      intitule: 'Prêt scolaire',
      capital: '450\u00A0000',
      echeances: '6 / 10',
      mensualite: '45\u00A0000',
      statut: 'applique',
    },
    {
      id: 1,
      salarie: 'Aka Koffi',
      intitule: '–',
      capital: '–',
      echeances: '–',
      mensualite: '–',
      statut: 'applique',
    },
    {
      id: 2,
      salarie: 'Bamba Aminata',
      intitule: '–',
      capital: '–',
      echeances: '–',
      mensualite: '–',
      statut: 'applique',
    },
    {
      id: 3,
      salarie: 'Kouamé Yao',
      intitule: '–',
      capital: '–',
      echeances: '–',
      mensualite: '–',
      statut: 'applique',
    },
    {
      id: 5,
      salarie: 'Fatou Koné',
      intitule: '–',
      capital: '–',
      echeances: '–',
      mensualite: '–',
      statut: 'applique',
    },
    {
      id: 6,
      salarie: 'Axel ROAD',
      intitule: '–',
      capital: '–',
      echeances: '–',
      mensualite: '–',
      statut: 'applique',
    },
  ]);

  // Tableau 2 - Retenues salariales (les 6 salariés réels)
  const [retenues] = useState<RetenueRow[]>([
    {
      id: 1,
      salarie: 'Aka Koffi',
      sbi: '505\u00A0000',
      sbs: '505\u00A0000',
      cnps: '31\u00A0815',
      cmu: '500',
      autres: '0',
      statut: 'applique',
    },
    {
      id: 2,
      salarie: 'Bamba Aminata',
      sbi: '410\u00A0000',
      sbs: '410\u00A0000',
      cnps: '25\u00A0830',
      cmu: '500',
      autres: '0',
      statut: 'applique',
    },
    {
      id: 3,
      salarie: 'Kouamé Yao',
      sbi: '360\u00A0000',
      sbs: '360\u00A0000',
      cnps: '22\u00A0680',
      cmu: '500',
      autres: '0',
      statut: 'applique',
    },
    {
      id: 4,
      salarie: 'Diallo Ibrahim',
      sbi: '550\u00A0000',
      sbs: '550\u00A0000',
      cnps: '34\u00A0650',
      cmu: '500',
      autres: '45\u00A0000',
      statut: 'applique',
    },
    {
      id: 5,
      salarie: 'Fatou Koné',
      sbi: '340\u00A0000',
      sbs: '340\u00A0000',
      cnps: '21\u00A0420',
      cmu: '500',
      autres: '0',
      statut: 'applique',
    },
    {
      id: 6,
      salarie: 'Axel ROAD',
      sbi: '128\u00A0907',
      sbs: '128\u00A0907',
      cnps: '8\u00A0121',
      cmu: '500',
      autres: '0',
      statut: 'applique',
    },
  ]);

  // Modal d'édition d'échéancier
  const [editingPret, setEditingPret] = useState<PretRow | null>(null);
  const [editMensualite, setEditMensualite] = useState('45 000');
  const [editEcheances, setEditEcheances] = useState('6 / 10');
  const [toastMsg, setToastMsg] = useState<string | null>(null);

  const handleToggleSuspend = (id: number) => {
    setPrets(
      prets.map((p) => {
        if (p.id === id) {
          const nextStatut = p.statut === 'suspendu' ? 'applique' : 'suspendu';
          setToastMsg(
            nextStatut === 'suspendu'
              ? `Prêt de ${p.salarie} suspendu pour la période ${currentPeriod.name || 'JUIN 2026'}.`
              : `Prêt de ${p.salarie} réactivé.`
          );
          setTimeout(() => setToastMsg(null), 3000);
          return { ...p, statut: nextStatut };
        }
        return p;
      })
    );
  };

  const handleSaveEcheancier = (e: React.FormEvent) => {
    e.preventDefault();
    if (!editingPret) return;
    setPrets(
      prets.map((p) =>
        p.id === editingPret.id
          ? { ...p, mensualite: editMensualite.replace(/\s/g, '\u00A0'), echeances: editEcheances }
          : p
      )
    );
    setToastMsg(`Échéancier mis à jour pour ${editingPret.salarie}.`);
    setTimeout(() => setToastMsg(null), 3000);
    setEditingPret(null);
  };

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

      {/* BANDEAU D'ALERTE QUOTITÉ CESSIBLE */}
      <div className="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-start gap-3 text-amber-900 shadow-2xs">
        <AlertTriangle className="w-5 h-5 text-amber-600 shrink-0 mt-0.5" />
        <div className="text-xs space-y-1">
          <p className="font-bold">
            Attention : retenues supérieures à 1/3 du salaire net pour Diallo Ibrahim
          </p>
          <p className="text-amber-800/90 leading-relaxed">
            Conformément au Code du Travail ivoirien (Art. L.34.1), le total cumulé des retenues (prêts, saisies-arrêts et acomptes) ne peut excéder la quotité cessible légale du salaire net. Veuillez vérifier la solvabilité de l'échéance.
          </p>
        </div>
      </div>

      {/* 1. KPI CARDS (HAUT DE PAGE) */}
      <section
        id="prets-kpis"
        aria-label="Indicateurs clés prêts et retenues"
        className="grid grid-cols-1 sm:grid-cols-3 gap-4"
      >
        <div className="border border-[#E8E8E6] bg-white rounded-2xl p-5 shadow-xs flex items-center justify-between">
          <div>
            <p className="text-xs font-bold text-[#6B6B6B] uppercase tracking-wider">
              Prêts actifs en cours
            </p>
            <p className="font-montserrat font-bold text-[32px] text-[#1e3a8a] leading-tight">
              1
            </p>
            <p className="text-xs text-[#6B6B6B]">Salarié éligible : Diallo Ibrahim</p>
          </div>
          <div className="w-12 h-12 rounded-2xl bg-[#EFF6FF] text-[#1e3a8a] flex items-center justify-center shrink-0">
            <HandCoins className="w-6 h-6" />
          </div>
        </div>

        <div className="border border-[#E8E8E6] bg-white rounded-2xl p-5 shadow-xs flex items-center justify-between">
          <div>
            <p className="text-xs font-bold text-[#6B6B6B] uppercase tracking-wider">
              Retenues appliquées ce mois
            </p>
            <p className="font-montserrat font-bold text-[32px] text-[#1e3a8a] leading-tight">
              2
            </p>
            <p className="text-xs text-[#6B6B6B]">CNPS, CMU & prélèvement prêt</p>
          </div>
          <div className="w-12 h-12 rounded-2xl bg-[#EFF6FF] text-[#1e3a8a] flex items-center justify-center shrink-0">
            <CreditCard className="w-6 h-6" />
          </div>
        </div>

        <div className="border border-[#E8E8E6] bg-white rounded-2xl p-5 shadow-xs flex items-center justify-between">
          <div>
            <p className="text-xs font-bold text-[#6B6B6B] uppercase tracking-wider">
              Total mensualités prélevées
            </p>
            <p className="font-montserrat font-bold text-[28px] text-[#1F1F1E] leading-tight">
              45 000 F
            </p>
            <p className="text-xs text-[#6B6B6B]">Prêt scolaire échéance 6/10</p>
          </div>
          <div className="w-12 h-12 rounded-2xl bg-[#EFF6FF] text-[#1e3a8a] flex items-center justify-center shrink-0">
            <Calendar className="w-6 h-6" />
          </div>
        </div>
      </section>

      {/* TABLEAU 1 – PRÊTS & ÉCHÉANCIERS */}
      <section
        id="section-prets-echeanciers"
        className="bg-white border border-[#E8E8E6] rounded-2xl shadow-xs overflow-hidden"
      >
        <div className="p-4 border-b border-[#E8E8E6] bg-[#F9FAFB] flex items-center justify-between">
          <h3 className="text-xs font-bold text-[#1F1F1E] uppercase tracking-wider">
            Tableau 1 – Prêts & échéanciers
          </h3>
          <span className="text-[11px] text-[#6B6B6B]">
            Période active : {currentPeriod.name || 'JUIN 2026'}
          </span>
        </div>

        <div className="overflow-x-auto">
          <table className="w-full text-left text-xs">
            <thead>
              <tr className="border-b border-[#E8E8E6] text-[#6B6B6B] font-semibold bg-[#F9FAFB]">
                <th className="py-3 px-4">SALARIÉ</th>
                <th className="py-3 px-4">INTITULÉ</th>
                <th className="py-3 px-4 text-right">CAPITAL (FCFA)</th>
                <th className="py-3 px-4 text-center">ÉCHÉANCES RESTANTES</th>
                <th className="py-3 px-4 text-right">MENSUALITÉ (FCFA)</th>
                <th className="py-3 px-4 text-center">STATUT</th>
                <th className="py-3 px-4 text-right">ACTIONS</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-[#E8E8E6]">
              {prets.map((item) => (
                <tr key={item.id} className="hover:bg-blue-50/20 transition-colors">
                  {/* SALARIÉ */}
                  <td className="py-3.5 px-4 font-bold text-[#1F1F1E]">
                    {item.salarie}
                  </td>

                  {/* INTITULÉ */}
                  <td className="py-3.5 px-4 text-[#1F1F1E]">
                    {item.intitule}
                  </td>

                  {/* CAPITAL */}
                  <td className="py-3.5 px-4 text-right font-montserrat font-medium text-[#1F1F1E]">
                    {item.capital !== '–' ? `${item.capital}\u00A0FCFA` : '–'}
                  </td>

                  {/* ÉCHÉANCES RESTANTES */}
                  <td className="py-3.5 px-4 text-center font-mono font-medium">
                    {item.echeances}
                  </td>

                  {/* MENSUALITÉ */}
                  <td className="py-3.5 px-4 text-right font-montserrat font-bold text-[#1e3a8a]">
                    {item.mensualite !== '–' ? `${item.mensualite}\u00A0FCFA` : '–'}
                  </td>

                  {/* STATUT */}
                  <td className="py-3.5 px-4 text-center">
                    {item.capital !== '–' ? (
                      item.statut === 'applique' ? (
                        <span className="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                          <span className="w-1.5 h-1.5 rounded-full bg-emerald-500" />
                          <span>Appliqué</span>
                        </span>
                      ) : item.statut === 'suspendu' ? (
                        <span className="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200">
                          <span className="w-1.5 h-1.5 rounded-full bg-amber-500" />
                          <span>Suspendu</span>
                        </span>
                      ) : (
                        <span className="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-800 border border-rose-200">
                          <span className="w-1.5 h-1.5 rounded-full bg-rose-500" />
                          <span>Insuffisance de Net</span>
                        </span>
                      )
                    ) : (
                      <span className="text-[#888] font-mono">–</span>
                    )}
                  </td>

                  {/* ACTIONS */}
                  <td className="py-3.5 px-4 text-right">
                    {item.capital !== '–' ? (
                      <div className="flex items-center justify-end gap-1.5">
                        <button
                          type="button"
                          onClick={() => {
                            setEditingPret(item);
                            setEditMensualite(item.mensualite.replace(/\u00A0/g, ' '));
                            setEditEcheances(item.echeances);
                          }}
                          className="px-2.5 py-1 rounded-lg border border-[#D0D0CE] bg-white hover:bg-neutral-50 text-[#1F1F1E] font-medium text-[11px] flex items-center gap-1 cursor-pointer transition shadow-2xs"
                        >
                          <Edit2 className="w-3 h-3 text-[#1e3a8a]" />
                          <span>Modifier</span>
                        </button>
                        <button
                          type="button"
                          onClick={() => handleToggleSuspend(item.id)}
                          className={`px-2.5 py-1 rounded-lg font-medium text-[11px] flex items-center gap-1 cursor-pointer transition shadow-2xs ${
                            item.statut === 'suspendu'
                              ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200'
                              : 'bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200'
                          }`}
                        >
                          {item.statut === 'suspendu' ? (
                            <>
                              <PlayCircle className="w-3 h-3" />
                              <span>Activer</span>
                            </>
                          ) : (
                            <>
                              <PauseCircle className="w-3 h-3" />
                              <span>Suspendre</span>
                            </>
                          )}
                        </button>
                      </div>
                    ) : (
                      <span className="text-[#888] font-mono">–</span>
                    )}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </section>

      {/* TABLEAU 2 – RETENUES SALARIALES */}
      <section
        id="section-retenues-salariales"
        className="bg-white border border-[#E8E8E6] rounded-2xl shadow-xs overflow-hidden"
      >
        <div className="p-4 border-b border-[#E8E8E6] bg-[#F9FAFB] flex items-center justify-between">
          <h3 className="text-xs font-bold text-[#1F1F1E] uppercase tracking-wider">
            Tableau 2 – Retenues salariales
          </h3>
          <span className="text-[11px] text-[#6B6B6B]">
            Cotisations obligatoires CNPS (6,3 %), CMU (500 F) & autres prélèvements
          </span>
        </div>

        <div className="overflow-x-auto">
          <table className="w-full text-left text-xs">
            <thead>
              <tr className="border-b border-[#E8E8E6] text-[#6B6B6B] font-semibold bg-[#F9FAFB]">
                <th className="py-3 px-4">SALARIÉ</th>
                <th className="py-3 px-4 text-right">SBI (FCFA)</th>
                <th className="py-3 px-4 text-right">SBS (FCFA)</th>
                <th className="py-3 px-4 text-right">CNPS (6,3 %)</th>
                <th className="py-3 px-4 text-right">CMU (FCFA)</th>
                <th className="py-3 px-4 text-right">AUTRES RETENUES</th>
                <th className="py-3 px-4 text-center">STATUT</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-[#E8E8E6]">
              {retenues.map((row) => (
                <tr key={row.id} className="hover:bg-blue-50/20 transition-colors">
                  {/* SALARIÉ */}
                  <td className="py-3.5 px-4 font-bold text-[#1F1F1E]">
                    {row.salarie}
                  </td>

                  {/* SBI */}
                  <td className="py-3.5 px-4 text-right font-montserrat font-medium text-[#1F1F1E]">
                    {row.sbi}
                  </td>

                  {/* SBS */}
                  <td className="py-3.5 px-4 text-right font-montserrat font-medium text-[#1F1F1E]">
                    {row.sbs}
                  </td>

                  {/* CNPS */}
                  <td className="py-3.5 px-4 text-right font-montserrat font-semibold text-blue-800">
                    {row.cnps}
                  </td>

                  {/* CMU */}
                  <td className="py-3.5 px-4 text-right font-montserrat font-medium text-[#1F1F1E]">
                    {row.cmu}
                  </td>

                  {/* AUTRES RETENUES */}
                  <td className="py-3.5 px-4 text-right font-montserrat font-bold text-amber-700">
                    {row.autres}
                  </td>

                  {/* STATUT */}
                  <td className="py-3.5 px-4 text-center">
                    <span className="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                      <span className="w-1.5 h-1.5 rounded-full bg-emerald-500" />
                      <span>Appliqué</span>
                    </span>
                  </td>
                </tr>
              ))}
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

      {/* MODALE – MODIFIER L'ÉCHÉANCIER */}
      <AnimatePresence>
        {editingPret && (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <motion.div
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              exit={{ opacity: 0, scale: 0.95 }}
              className="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-[#E8E8E6]"
            >
              <div className="bg-[#1e3a8a] text-white px-6 py-4 flex items-center justify-between">
                <div className="flex items-center gap-2">
                  <Edit2 className="w-5 h-5" />
                  <h3 className="font-bold text-sm">
                    Modifier l'échéancier — {editingPret.salarie}
                  </h3>
                </div>
                <button
                  type="button"
                  onClick={() => setEditingPret(null)}
                  className="p-1.5 rounded-lg text-white/80 hover:text-white hover:bg-white/10 transition cursor-pointer"
                >
                  <X className="w-5 h-5" />
                </button>
              </div>

              <form onSubmit={handleSaveEcheancier} className="p-6 space-y-4 text-xs">
                <div>
                  <label className="block font-bold text-[#1F1F1E] mb-1">
                    Intitulé du prêt
                  </label>
                  <input
                    type="text"
                    value={editingPret.intitule}
                    disabled
                    className="w-full px-3 py-2 rounded-xl border border-[#D0D0CE] bg-neutral-100 text-[#555] font-medium"
                  />
                </div>

                <div>
                  <label className="block font-bold text-[#1F1F1E] mb-1">
                    Échéances restantes (format actuel / total)
                  </label>
                  <input
                    type="text"
                    value={editEcheances}
                    onChange={(e) => setEditEcheances(e.target.value)}
                    className="w-full px-3 py-2 rounded-xl border border-[#D0D0CE] bg-white font-mono focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/40"
                    placeholder="ex: 6 / 10"
                    required
                  />
                </div>

                <div>
                  <label className="block font-bold text-[#1F1F1E] mb-1">
                    Mensualité à prélever (FCFA)
                  </label>
                  <input
                    type="text"
                    value={editMensualite}
                    onChange={(e) => setEditMensualite(e.target.value)}
                    className="w-full px-3 py-2 rounded-xl border border-[#D0D0CE] bg-white font-montserrat font-bold focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/40"
                    placeholder="ex: 45 000"
                    required
                  />
                </div>

                <div className="pt-3 border-t border-[#E8E8E6] flex items-center justify-end gap-2.5">
                  <button
                    type="button"
                    onClick={() => setEditingPret(null)}
                    className="px-4 py-2 rounded-xl border border-[#D0D0CE] text-[#1F1F1E] hover:bg-neutral-100 font-semibold cursor-pointer"
                  >
                    Annuler
                  </button>
                  <button
                    type="submit"
                    className="px-4 py-2 rounded-xl bg-[#1e3a8a] text-white hover:bg-[#162a63] font-bold cursor-pointer shadow-xs"
                  >
                    Enregistrer
                  </button>
                </div>
              </form>
            </motion.div>
          </div>
        )}
      </AnimatePresence>
    </motion.div>
  );
};
