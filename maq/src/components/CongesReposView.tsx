import React, { useState } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import {
  CalendarDays,
  Plus,
  ArrowLeft,
  Calculator,
  CheckCircle2,
  AlertCircle,
  FileText,
  X,
  Clock,
  Check,
} from 'lucide-react';
import { Period } from '../data/employeeTypes';

interface CongesReposViewProps {
  currentPeriod: Period;
  onNavigateHome: () => void;
  onNavigateProcessing: () => void;
}

interface CongeRow {
  id: number;
  salarie: string;
  dept: string;
  typeAbsence: string;
  debut: string;
  fin: string;
  jours: number;
  impactPaie: string;
  justificatif: 'fourni' | 'manquant' | 'aucun';
  statut: 'valide' | 'attente' | 'aucun';
}

interface SoldeRow {
  id: number;
  salarie: string;
  anciennete: string;
  soldeAcquis: string;
  consomme: string;
  soldeRestant: string;
}

export const CongesReposView: React.FC<CongesReposViewProps> = ({
  currentPeriod,
  onNavigateHome,
  onNavigateProcessing,
}) => {
  // Tableau 1 - Registre des congés (les 6 salariés)
  const [conges, setConges] = useState<CongeRow[]>([
    {
      id: 1,
      salarie: 'Aka Koffi',
      dept: 'Technique',
      typeAbsence: '–',
      debut: '–',
      fin: '–',
      jours: 0,
      impactPaie: '–',
      justificatif: 'aucun',
      statut: 'aucun',
    },
    {
      id: 3,
      salarie: 'Kouamé Yao',
      dept: 'Opérations',
      typeAbsence: 'Absence injustifiée',
      debut: '05/09',
      fin: '12/09',
      jours: 8,
      impactPaie: 'Déduction',
      justificatif: 'manquant',
      statut: 'attente',
    },
    {
      id: 2,
      salarie: 'Bamba Aminata',
      dept: 'Marketing',
      typeAbsence: '–',
      debut: '–',
      fin: '–',
      jours: 0,
      impactPaie: '–',
      justificatif: 'aucun',
      statut: 'aucun',
    },
    {
      id: 4,
      salarie: 'Diallo Ibrahim',
      dept: 'Commercial',
      typeAbsence: 'Congé payé annuel',
      debut: '15/06',
      fin: '25/06',
      jours: 10,
      impactPaie: 'Indemnisé',
      justificatif: 'fourni',
      statut: 'valide',
    },
    {
      id: 5,
      salarie: 'Fatou Koné',
      dept: 'RH',
      typeAbsence: '–',
      debut: '–',
      fin: '–',
      jours: 0,
      impactPaie: '–',
      justificatif: 'aucun',
      statut: 'aucun',
    },
    {
      id: 6,
      salarie: 'Axel ROAD',
      dept: 'IT',
      typeAbsence: '–',
      debut: '–',
      fin: '–',
      jours: 0,
      impactPaie: '–',
      justificatif: 'aucun',
      statut: 'aucun',
    },
  ]);

  // Tableau 2 - Suivi des soldes (les 6 salariés avec formule 2,2 j/mois)
  const [soldes] = useState<SoldeRow[]>([
    {
      id: 1,
      salarie: 'Aka Koffi',
      anciennete: '3 ans',
      soldeAcquis: '18,2 j',
      consomme: '0 j',
      soldeRestant: '18,2 j',
    },
    {
      id: 2,
      salarie: 'Bamba Aminata',
      anciennete: '4 ans',
      soldeAcquis: '24,2 j',
      consomme: '0 j',
      soldeRestant: '24,2 j',
    },
    {
      id: 3,
      salarie: 'Kouamé Yao',
      anciennete: '2 ans',
      soldeAcquis: '14,0 j',
      consomme: '0 j',
      soldeRestant: '14,0 j',
    },
    {
      id: 4,
      salarie: 'Diallo Ibrahim',
      anciennete: '5 ans',
      soldeAcquis: '30,5 j',
      consomme: '10 j',
      soldeRestant: '20,5 j',
    },
    {
      id: 5,
      salarie: 'Fatou Koné',
      anciennete: '1 an',
      soldeAcquis: '8,8 j',
      consomme: '0 j',
      soldeRestant: '8,8 j',
    },
    {
      id: 6,
      salarie: 'Axel ROAD',
      anciennete: '6 mois',
      soldeAcquis: '4,4 j',
      consomme: '0 j',
      soldeRestant: '4,4 j',
    },
  ]);

  // Modale Saisir un congé / repos
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [selectedEmp, setSelectedEmp] = useState('Aka Koffi');
  const [selectedType, setSelectedType] = useState('Congé payé annuel');
  const [debutDate, setDebutDate] = useState('2026-06-10');
  const [finDate, setFinDate] = useState('2026-06-15');
  const [nbJours, setNbJours] = useState('5');
  const [justifState, setJustifState] = useState<'fourni' | 'manquant'>('fourni');
  const [toastMsg, setToastMsg] = useState<string | null>(null);

  const handleSubmitModal = (e: React.FormEvent) => {
    e.preventDefault();
    const updated = conges.map((c) => {
      if (c.salarie === selectedEmp) {
        return {
          ...c,
          typeAbsence: selectedType,
          debut: debutDate.substring(5).replace('-', '/'),
          fin: finDate.substring(5).replace('-', '/'),
          jours: parseInt(nbJours, 10) || 1,
          impactPaie: selectedType === 'Absence injustifiée' ? 'Déduction' : 'Indemnisé',
          justificatif: justifState,
          statut: 'attente' as const,
        };
      }
      return c;
    });
    setConges(updated);
    setIsModalOpen(false);
    setToastMsg(`Demande de congé enregistrée pour ${selectedEmp}.`);
    setTimeout(() => setToastMsg(null), 3000);
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

      {/* BARRE SUPÉRIEURE AVEC BOUTON PRINCIPAL */}
      <div className="bg-white border border-[#E8E8E6] rounded-2xl p-4 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <div className="flex items-center gap-2">
          <div className="w-9 h-9 rounded-xl bg-blue-50 text-[#1e3a8a] flex items-center justify-center shrink-0">
            <CalendarDays className="w-5 h-5" />
          </div>
          <div>
            <h2 className="text-sm font-bold text-[#1F1F1E]">
              Gestion des Congés & Repos — {currentPeriod.name || 'JUIN 2026'}
            </h2>
            <p className="text-xs text-[#6B6B6B]">
              Acquisition légale de 2,2 jours ouvrables par mois et suivi des absences
            </p>
          </div>
        </div>

        {/* Bouton principal : + Saisir un congé / repos */}
        <button
          type="button"
          onClick={() => setIsModalOpen(true)}
          className="px-4 py-2 rounded-xl bg-[#1e3a8a] text-white hover:bg-[#162a63] text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer shadow-sm"
        >
          <Plus className="w-4 h-4" />
          <span>+ Saisir un congé / repos</span>
        </button>
      </div>

      {/* KPI CARDS (3 CARTES) */}
      <section
        id="conges-kpis"
        aria-label="Indicateurs congés et repos"
        className="grid grid-cols-1 sm:grid-cols-3 gap-4"
      >
        <div className="border border-[#E8E8E6] bg-white rounded-2xl p-5 shadow-xs flex items-center justify-between">
          <div>
            <p className="text-xs font-bold text-[#6B6B6B] uppercase tracking-wider">
              Salariés en congé ce mois-ci
            </p>
            <p className="font-montserrat font-bold text-[32px] text-[#1e3a8a] leading-tight">
              1
            </p>
            <p className="text-xs text-[#6B6B6B]">Diallo Ibrahim (10 jours)</p>
          </div>
          <div className="w-12 h-12 rounded-2xl bg-[#EFF6FF] text-[#1e3a8a] flex items-center justify-center shrink-0">
            <CalendarDays className="w-6 h-6" />
          </div>
        </div>

        <div className="border border-[#E8E8E6] bg-white rounded-2xl p-5 shadow-xs flex items-center justify-between">
          <div>
            <p className="text-xs font-bold text-[#6B6B6B] uppercase tracking-wider">
              Demandes en attente
            </p>
            <p className="font-montserrat font-bold text-[32px] text-amber-600 leading-tight">
              2
            </p>
            <p className="text-xs text-[#6B6B6B]">À valider avant clôture paie</p>
          </div>
          <div className="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
            <Clock className="w-6 h-6" />
          </div>
        </div>

        <div className="border border-[#E8E8E6] bg-white rounded-2xl p-5 shadow-xs flex items-center justify-between">
          <div>
            <p className="text-xs font-bold text-[#6B6B6B] uppercase tracking-wider">
              Jours injustifiés à déduire
            </p>
            <p className="font-montserrat font-bold text-[32px] text-[#1F1F1E] leading-tight">
              0
            </p>
            <p className="text-xs text-[#6B6B6B]">Impact brut sous contrôle</p>
          </div>
          <div className="w-12 h-12 rounded-2xl bg-[#EFF6FF] text-[#1e3a8a] flex items-center justify-center shrink-0">
            <CheckCircle2 className="w-6 h-6 text-emerald-600" />
          </div>
        </div>
      </section>

      {/* TABLEAU 1 – REGISTRE DES CONGÉS */}
      <section
        id="section-registre-conges"
        className="bg-white border border-[#E8E8E6] rounded-2xl shadow-xs overflow-hidden"
      >
        <div className="p-4 border-b border-[#E8E8E6] bg-[#F9FAFB] flex items-center justify-between">
          <h3 className="text-xs font-bold text-[#1F1F1E] uppercase tracking-wider">
            Tableau 1 – Registre des congés & absences
          </h3>
          <span className="text-[11px] text-[#6B6B6B]">
            6 salariés réels de l'entreprise
          </span>
        </div>

        <div className="overflow-x-auto">
          <table className="w-full text-left text-xs">
            <thead>
              <tr className="border-b border-[#E8E8E6] text-[#6B6B6B] font-semibold bg-[#F9FAFB]">
                <th className="py-3 px-4">SALARIÉ</th>
                <th className="py-3 px-4">DÉPARTEMENT</th>
                <th className="py-3 px-4">TYPE D'ABSENCE</th>
                <th className="py-3 px-4 text-center">DÉBUT</th>
                <th className="py-3 px-4 text-center">FIN</th>
                <th className="py-3 px-4 text-center">JOURS</th>
                <th className="py-3 px-4 text-center">IMPACT PAIE</th>
                <th className="py-3 px-4 text-center">JUSTIFICATIF</th>
                <th className="py-3 px-4 text-center">STATUT</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-[#E8E8E6]">
              {conges.map((row) => (
                <tr key={row.id} className="hover:bg-blue-50/20 transition-colors">
                  {/* SALARIÉ */}
                  <td className="py-3.5 px-4 font-bold text-[#1F1F1E]">
                    {row.salarie}
                  </td>

                  {/* DÉPARTEMENT */}
                  <td className="py-3.5 px-4 text-[#6B6B6B]">
                    {row.dept}
                  </td>

                  {/* TYPE D'ABSENCE */}
                  <td className="py-3.5 px-4 text-[#1F1F1E] font-medium">
                    {row.typeAbsence}
                  </td>

                  {/* DÉBUT */}
                  <td className="py-3.5 px-4 text-center font-mono text-[#555]">
                    {row.debut}
                  </td>

                  {/* FIN */}
                  <td className="py-3.5 px-4 text-center font-mono text-[#555]">
                    {row.fin}
                  </td>

                  {/* JOURS */}
                  <td className="py-3.5 px-4 text-center font-bold text-[#1e3a8a]">
                    {row.jours > 0 ? `${row.jours} j` : '0'}
                  </td>

                  {/* IMPACT PAIE */}
                  <td className="py-3.5 px-4 text-center">
                    {row.impactPaie === 'Déduction' && (
                      <span className="font-semibold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200">
                        Déduction
                      </span>
                    )}
                    {row.impactPaie === 'Indemnisé' && (
                      <span className="font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                        Indemnisé
                      </span>
                    )}
                    {row.impactPaie === '–' && <span className="text-[#888] font-mono">–</span>}
                  </td>

                  {/* JUSTIFICATIF */}
                  <td className="py-3.5 px-4 text-center">
                    {row.justificatif === 'fourni' && (
                      <span className="text-emerald-700 font-semibold inline-flex items-center gap-1">
                        <span>✅ Fourni</span>
                      </span>
                    )}
                    {row.justificatif === 'manquant' && (
                      <span className="text-rose-700 font-semibold inline-flex items-center gap-1">
                        <span>❌ Manquant</span>
                      </span>
                    )}
                    {row.justificatif === 'aucun' && (
                      <span className="text-[#888] font-mono">–</span>
                    )}
                  </td>

                  {/* STATUT */}
                  <td className="py-3.5 px-4 text-center">
                    {row.statut === 'valide' && (
                      <span className="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span className="w-1.5 h-1.5 rounded-full bg-emerald-500" />
                        <span>Validé</span>
                      </span>
                    )}
                    {row.statut === 'attente' && (
                      <span className="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200">
                        <span className="w-1.5 h-1.5 rounded-full bg-amber-500" />
                        <span>En attente</span>
                      </span>
                    )}
                    {row.statut === 'aucun' && (
                      <span className="text-[#888] font-mono">–</span>
                    )}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </section>

      {/* TABLEAU 2 – SUIVI DES SOLDES (Calcul : 2,2 jours / mois) */}
      <section
        id="section-suivi-soldes"
        className="bg-white border border-[#E8E8E6] rounded-2xl shadow-xs overflow-hidden"
      >
        <div className="p-4 border-b border-[#E8E8E6] bg-[#F9FAFB] flex items-center justify-between">
          <div>
            <h3 className="text-xs font-bold text-[#1F1F1E] uppercase tracking-wider">
              Tableau 2 – Suivi des soldes de congés payés
            </h3>
            <p className="text-[11px] text-[#6B6B6B] mt-0.5">
              Règle légale CI : <strong>2,2 jours / mois</strong> de travail effectif + majorations d'ancienneté
            </p>
          </div>
          <span className="text-xs font-bold text-[#1e3a8a] bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-200">
            Formule : 2,2 j/mois
          </span>
        </div>

        <div className="overflow-x-auto">
          <table className="w-full text-left text-xs">
            <thead>
              <tr className="border-b border-[#E8E8E6] text-[#6B6B6B] font-semibold bg-[#F9FAFB]">
                <th className="py-3 px-4">SALARIÉ</th>
                <th className="py-3 px-4">ANCIENNETÉ</th>
                <th className="py-3 px-4 text-center">SOLDE ACQUIS</th>
                <th className="py-3 px-4 text-center">CONSOMMÉ</th>
                <th className="py-3 px-4 text-center">SOLDE RESTANT</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-[#E8E8E6]">
              {soldes.map((s) => (
                <tr key={s.id} className="hover:bg-blue-50/20 transition-colors">
                  {/* SALARIÉ */}
                  <td className="py-3.5 px-4 font-bold text-[#1F1F1E]">
                    {s.salarie}
                  </td>

                  {/* ANCIENNETÉ */}
                  <td className="py-3.5 px-4 text-[#6B6B6B] font-medium">
                    {s.anciennete}
                  </td>

                  {/* SOLDE ACQUIS */}
                  <td className="py-3.5 px-4 text-center font-montserrat font-semibold text-[#1F1F1E]">
                    {s.soldeAcquis}
                  </td>

                  {/* CONSOMMÉ */}
                  <td className="py-3.5 px-4 text-center font-montserrat font-medium text-rose-700">
                    {s.consomme}
                  </td>

                  {/* SOLDE RESTANT */}
                  <td className="py-3.5 px-4 text-center font-montserrat font-bold text-[#1e3a8a] text-sm">
                    {s.soldeRestant}
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

      {/* MODALE – SAISIR UN CONGÉ / REPOS */}
      <AnimatePresence>
        {isModalOpen && (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <motion.div
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              exit={{ opacity: 0, scale: 0.95 }}
              className="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-[#E8E8E6]"
            >
              <div className="bg-[#1e3a8a] text-white px-6 py-4 flex items-center justify-between">
                <div className="flex items-center gap-2">
                  <CalendarDays className="w-5 h-5" />
                  <h3 className="font-bold text-sm">Saisir un congé / absence</h3>
                </div>
                <button
                  type="button"
                  onClick={() => setIsModalOpen(false)}
                  className="p-1.5 rounded-lg text-white/80 hover:text-white hover:bg-white/10 transition cursor-pointer"
                >
                  <X className="w-5 h-5" />
                </button>
              </div>

              <form onSubmit={handleSubmitModal} className="p-6 space-y-4 text-xs">
                <div>
                  <label className="block font-bold text-[#1F1F1E] mb-1">
                    Salarié concerné
                  </label>
                  <select
                    value={selectedEmp}
                    onChange={(e) => setSelectedEmp(e.target.value)}
                    className="w-full px-3 py-2 rounded-xl border border-[#D0D0CE] bg-white font-medium focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/40"
                  >
                    <option value="Aka Koffi">Aka Koffi (Technique)</option>
                    <option value="Bamba Aminata">Bamba Aminata (Marketing)</option>
                    <option value="Kouamé Yao">Kouamé Yao (Opérations)</option>
                    <option value="Diallo Ibrahim">Diallo Ibrahim (Commercial)</option>
                    <option value="Fatou Koné">Fatou Koné (RH)</option>
                    <option value="Axel ROAD">Axel ROAD (IT)</option>
                  </select>
                </div>

                <div>
                  <label className="block font-bold text-[#1F1F1E] mb-1">
                    Type d'absence
                  </label>
                  <select
                    value={selectedType}
                    onChange={(e) => setSelectedType(e.target.value)}
                    className="w-full px-3 py-2 rounded-xl border border-[#D0D0CE] bg-white font-medium focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/40"
                  >
                    <option value="Congé payé annuel">Congé payé annuel (2,2 j/mois)</option>
                    <option value="Absence injustifiée">Absence injustifiée (déduction)</option>
                    <option value="Événement familial">Événement familial légal</option>
                    <option value="Maladie ordinaire">Maladie ordinaire (certificat médical)</option>
                    <option value="Repos récupérateur">Repos récupérateur</option>
                  </select>
                </div>

                <div className="grid grid-cols-2 gap-3">
                  <div>
                    <label className="block font-bold text-[#1F1F1E] mb-1">Date début</label>
                    <input
                      type="date"
                      value={debutDate}
                      onChange={(e) => setDebutDate(e.target.value)}
                      className="w-full px-3 py-2 rounded-xl border border-[#D0D0CE] bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/40"
                      required
                    />
                  </div>
                  <div>
                    <label className="block font-bold text-[#1F1F1E] mb-1">Date fin</label>
                    <input
                      type="date"
                      value={finDate}
                      onChange={(e) => setFinDate(e.target.value)}
                      className="w-full px-3 py-2 rounded-xl border border-[#D0D0CE] bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/40"
                      required
                    />
                  </div>
                </div>

                <div className="grid grid-cols-2 gap-3">
                  <div>
                    <label className="block font-bold text-[#1F1F1E] mb-1">
                      Nombre de jours ouvrables
                    </label>
                    <input
                      type="number"
                      min="1"
                      max="30"
                      value={nbJours}
                      onChange={(e) => setNbJours(e.target.value)}
                      className="w-full px-3 py-2 rounded-xl border border-[#D0D0CE] bg-white font-bold font-montserrat focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/40"
                      required
                    />
                  </div>
                  <div>
                    <label className="block font-bold text-[#1F1F1E] mb-1">
                      Pièce justificative
                    </label>
                    <select
                      value={justifState}
                      onChange={(e) => setJustifState(e.target.value as 'fourni' | 'manquant')}
                      className="w-full px-3 py-2 rounded-xl border border-[#D0D0CE] bg-white font-medium focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/40"
                    >
                      <option value="fourni">✅ Fourni</option>
                      <option value="manquant">❌ Manquant</option>
                    </select>
                  </div>
                </div>

                <div className="pt-3 border-t border-[#E8E8E6] flex items-center justify-end gap-2.5">
                  <button
                    type="button"
                    onClick={() => setIsModalOpen(false)}
                    className="px-4 py-2 rounded-xl border border-[#D0D0CE] text-[#1F1F1E] hover:bg-neutral-100 font-semibold cursor-pointer"
                  >
                    Annuler
                  </button>
                  <button
                    type="submit"
                    className="px-4 py-2 rounded-xl bg-[#1e3a8a] text-white hover:bg-[#162a63] font-bold cursor-pointer shadow-xs"
                  >
                    Valider la saisie
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
