import React from 'react';
import { motion } from 'motion/react';
import {
  Users,
  Coins,
  AlertTriangle,
  ArrowRight,
  ShieldCheck,
  Calendar,
  RefreshCw,
  Lock,
  Unlock,
  CheckCircle2,
  Clock,
  FileCheck,
} from 'lucide-react';
import { Employee, ComputedEmployeePay, Period } from '../data/employeeTypes';
import { getPeriodStatusBadge } from '../logic/periodManager';

interface AccueilPaieViewProps {
  employees: Employee[];
  computedMap?: Map<number, ComputedEmployeePay>;
  currentPeriod: Period;
  previousPeriod?: Period | null;
  onNavigateToProcessing: () => void;
  onNavigateToTab?: (tabId: string) => void;
  onOpenRolloverModal?: () => void;
  onOpenValidationModal?: () => void;
  onUnlockPeriod?: () => void;
}

interface EmployeeRowData {
  id: number;
  name: string;
  dept: string;
  days: string;
  base: string;
  net: string;
  status: 'conforme' | 'anomalie' | 'attente';
  statusLabel: string;
}

// Données des 6 salariés réels conformément à la maquette
const EMPLOYEES_DISPLAY_DATA: EmployeeRowData[] = [
  {
    id: 1,
    name: 'Aka Koffi',
    dept: 'Technique',
    days: '30j',
    base: '450\u00A0000\u00A0FCFA',
    net: '420\u00A0635\u00A0FCFA',
    status: 'conforme',
    statusLabel: 'Conforme',
  },
  {
    id: 2,
    name: 'Bamba Aminata',
    dept: 'Marketing',
    days: '30j',
    base: '380\u00A0000\u00A0FCFA',
    net: '347\u00A0935\u00A0FCFA',
    status: 'conforme',
    statusLabel: 'Conforme',
  },
  {
    id: 3,
    name: 'Kouamé Yao',
    dept: 'Opérations',
    days: '22j',
    base: '330\u00A0000\u00A0FCFA',
    net: '221\u00A0434\u00A0FCFA',
    status: 'anomalie',
    statusLabel: 'Anomalie',
  },
  {
    id: 4,
    name: 'Diallo Ibrahim',
    dept: 'Commercial',
    days: '30j',
    base: '520\u00A0000\u00A0FCFA',
    net: '(à calculer)',
    status: 'attente',
    statusLabel: 'En attente',
  },
  {
    id: 5,
    name: 'Fatou Koné',
    dept: 'RH',
    days: '30j',
    base: '310\u00A0000\u00A0FCFA',
    net: '(à calculer)',
    status: 'attente',
    statusLabel: 'En attente',
  },
  {
    id: 6,
    name: 'Axel ROAD',
    dept: 'IT',
    days: '30j',
    base: '128\u00A0907\u00A0FCFA',
    net: '(à calculer)',
    status: 'attente',
    statusLabel: 'En attente',
  },
];

export const AccueilPaieView: React.FC<AccueilPaieViewProps> = ({
  currentPeriod,
  previousPeriod,
  onNavigateToProcessing,
  onNavigateToTab,
  onOpenRolloverModal,
  onOpenValidationModal,
  onUnlockPeriod,
}) => {
  const isPreviousClosed = previousPeriod ? (previousPeriod.status === 'payee' || previousPeriod.status === 'validée') : true;
  const isCurrentClosed = currentPeriod.status === 'payee' || currentPeriod.status === 'validée';
  const statusBadge = getPeriodStatusBadge(currentPeriod.status);

  return (
    <motion.div
      initial={{ opacity: 0, y: 8 }}
      animate={{ opacity: 1, y: 0 }}
      exit={{ opacity: 0, y: -8 }}
      transition={{ duration: 0.22 }}
      className="flex-1 flex flex-col gap-6"
    >
      {/* BARRE SUPÉRIEURE DE GESTION DE PÉRIODE & REPORT */}
      <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-white p-3.5 rounded-2xl border border-[#E8E8E6] shadow-2xs">
        {/* En haut à gauche : Bouton Reprendre la paie */}
        <div className="flex items-center gap-2 flex-wrap">
          {previousPeriod ? (
            <button
              type="button"
              id="btn-reprendre-paie-accueil"
              onClick={() => onOpenRolloverModal?.()}
              title={
                isPreviousClosed
                  ? `Report intelligent des données de ${previousPeriod.name} vers ${currentPeriod.name}`
                  : `Le mois de ${previousPeriod.name} doit d'abord être clôturé (statut Payée).`
              }
              className={`px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 cursor-pointer shadow-2xs ${
                isPreviousClosed
                  ? 'bg-[#1e3a8a] text-white hover:bg-[#1e3a8a]/90 hover:shadow-sm'
                  : 'bg-amber-100 text-amber-900 border border-amber-300 hover:bg-amber-200'
              }`}
            >
              <RefreshCw className="w-3.5 h-3.5" />
              <span>Reprendre la paie de {previousPeriod.name}</span>
              {!isPreviousClosed && (
                <span className="text-[10px] px-1.5 py-0.5 rounded bg-amber-200 text-amber-900 font-semibold">
                  ⚠️ Clôture requise
                </span>
              )}
            </button>
          ) : (
            <div className="text-xs text-[#888] font-medium px-2 flex items-center gap-1.5">
              <Calendar className="w-3.5 h-3.5 text-[#1e3a8a]" />
              <span>Premier mois de l'exercice</span>
            </div>
          )}

          {!isPreviousClosed && previousPeriod && (
            <span className="text-[11px] text-amber-800 hidden md:inline">
              Le mois de {previousPeriod.name} doit d'abord être clôturé (statut Payée).
            </span>
          )}
        </div>

        {/* En haut à droite : Statut du cycle de vie & Actions */}
        <div className="flex items-center gap-2.5 w-full sm:w-auto justify-between sm:justify-end">
          <div className="flex items-center gap-1.5 text-xs">
            <span className="text-[#6B6B6B] font-medium hidden lg:inline">Statut période :</span>
            <span
              className={`px-2.5 py-1 rounded-full text-xs font-bold border flex items-center gap-1.5 ${statusBadge.colorClass}`}
            >
              <span>{statusBadge.icon}</span>
              <span>{statusBadge.label}</span>
            </span>
          </div>

          {/* Action Clôturer ou Déverrouiller */}
          {currentPeriod.status === 'en_cours' && onOpenValidationModal && (
            <button
              type="button"
              onClick={onOpenValidationModal}
              className="px-3 py-1.5 rounded-lg border border-emerald-300 bg-emerald-50 text-emerald-800 hover:bg-emerald-100 font-bold text-xs transition flex items-center gap-1.5 cursor-pointer"
            >
              <FileCheck className="w-3.5 h-3.5 text-emerald-600" />
              <span>Clôturer et valider</span>
            </button>
          )}

          {isCurrentClosed && onUnlockPeriod && (
            <button
              type="button"
              onClick={onUnlockPeriod}
              className="px-3 py-1.5 rounded-lg border border-neutral-300 bg-neutral-100 text-neutral-800 hover:bg-neutral-200 font-bold text-xs transition flex items-center gap-1.5 cursor-pointer"
              title="Action administrateur : déverrouiller pour modification"
            >
              <Unlock className="w-3.5 h-3.5 text-neutral-600" />
              <span>Déverrouiller (Admin)</span>
            </button>
          )}
        </div>
      </div>

      {/* BANNIÈRE DE VERROUILLAGE SI PAYÉE */}
      {isCurrentClosed && (
        <div className="p-3.5 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-900 text-xs flex items-center justify-between gap-3 shadow-2xs">
          <div className="flex items-center gap-2">
            <Lock className="w-4 h-4 text-emerald-700 shrink-0" />
            <span>
              <strong>Période {currentPeriod.name} clôturée et payée (mode lecture seule).</strong> Les bulletins sont archivés et conformes.
            </span>
          </div>
          {onUnlockPeriod && (
            <button
              type="button"
              onClick={onUnlockPeriod}
              className="underline font-bold text-emerald-900 hover:text-emerald-950 shrink-0 cursor-pointer"
            >
              Déverrouiller
            </button>
          )}
        </div>
      )}

      {/* 1. HEADER NAVY ARRONDI (#1e3a8a) */}
      <header
        id="accueil-header"
        className="bg-[#1e3a8a] rounded-2xl p-6 text-white shadow-md flex flex-col md:flex-row justify-between items-start md:items-center gap-5"
      >
        <div className="space-y-2 max-w-3xl">
          {/* Badge période */}
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 text-white text-xs font-semibold backdrop-blur-xs">
            <Calendar className="w-3.5 h-3.5" />
            <span>Période active : {currentPeriod.name || 'JUIN 2026'}</span>
          </div>
          {/* Titre */}
          <h2 className="text-2xl font-bold tracking-tight text-white">
            Tableau de Bord — Accueil Paie
          </h2>
          {/* Sous-titre */}
          <p className="text-xs sm:text-sm text-white/85 leading-relaxed">
            Supervisez l'ensemble de vos 6 salariés, suivez l'impact budgétaire en temps réel avec le barème fiscal ivoirien ITS réformé et validez les salaires en toute conformité.
          </p>
        </div>

        {/* Bouton CTA à droite (fond blanc, texte navy) */}
        <button
          type="button"
          onClick={onNavigateToProcessing}
          className="px-5 py-3 rounded-xl bg-white text-[#1e3a8a] hover:bg-neutral-100 font-bold text-xs transition-all duration-150 flex items-center gap-2 shadow-sm shrink-0 cursor-pointer hover:shadow-md"
        >
          <span>Accéder au Traitement de paie</span>
          <ArrowRight className="w-4 h-4" />
        </button>
      </header>

      {/* 2. KPI CARDS (4 CARTES EN LIGNE) */}
      <section
        id="accueil-kpis"
        aria-label="Indicateurs clés de performance"
        className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4"
      >
        {/* KPI 1 : Effectif Total */}
        <div className="border border-[#E8E8E6] bg-white rounded-2xl p-5 shadow-xs flex items-center justify-between transition-all hover:shadow-sm">
          <div className="space-y-1">
            <p className="text-xs font-bold text-[#6B6B6B] uppercase tracking-wider">
              Effectif Total
            </p>
            <p className="font-montserrat font-bold text-[32px] text-[#1e3a8a] leading-none">
              6 salariés
            </p>
            <p className="text-xs text-[#6B6B6B] pt-0.5">
              1 en attente de variables
            </p>
          </div>
          <div className="w-12 h-12 rounded-2xl bg-[#EFF6FF] text-[#1e3a8a] flex items-center justify-center shrink-0">
            <Users className="w-6 h-6" />
          </div>
        </div>

        {/* KPI 2 : Masse Nette à Verser */}
        <div className="border border-[#E8E8E6] bg-white rounded-2xl p-5 shadow-xs flex items-center justify-between transition-all hover:shadow-sm">
          <div className="space-y-1">
            <p className="text-xs font-bold text-[#6B6B6B] uppercase tracking-wider">
              Masse Nette à Verser
            </p>
            <p className="font-montserrat font-bold text-[28px] text-[#1F1F1E] leading-none">
              (à calculer)
            </p>
            <p className="text-xs text-[#6B6B6B] pt-0.5">
              Net à payer global
            </p>
          </div>
          <div className="w-12 h-12 rounded-2xl bg-[#EFF6FF] text-[#1e3a8a] flex items-center justify-center shrink-0">
            <Coins className="w-6 h-6" />
          </div>
        </div>

        {/* KPI 3 : Cotisations & Impôts */}
        <div className="border border-[#E8E8E6] bg-white rounded-2xl p-5 shadow-xs flex items-center justify-between transition-all hover:shadow-sm">
          <div className="space-y-1">
            <p className="text-xs font-bold text-[#6B6B6B] uppercase tracking-wider">
              Cotisations & Impôts
            </p>
            <p className="font-montserrat font-bold text-[28px] text-[#1F1F1E] leading-none">
              (à calculer)
            </p>
            <p className="text-xs text-[#6B6B6B] pt-0.5">
              CNPS 6,3 % · CMU · ITS 2024
            </p>
          </div>
          <div className="w-12 h-12 rounded-2xl bg-[#EFF6FF] text-[#1e3a8a] flex items-center justify-center shrink-0">
            <ShieldCheck className="w-6 h-6" />
          </div>
        </div>

        {/* KPI 4 : Alertes & Anomalies */}
        <div className="border border-[#E8E8E6] bg-white rounded-2xl p-5 shadow-xs flex items-center justify-between transition-all hover:shadow-sm">
          <div className="space-y-1">
            <p className="text-xs font-bold text-[#6B6B6B] uppercase tracking-wider">
              Alertes & Anomalies
            </p>
            <p className="font-montserrat font-bold text-[32px] text-[#1e3a8a] leading-none">
              3 à contrôler
            </p>
            <p className="text-xs text-[#6B6B6B] pt-0.5">
              Pointages & variations
            </p>
          </div>
          <div className="w-12 h-12 rounded-2xl bg-[#EFF6FF] text-[#1e3a8a] flex items-center justify-center shrink-0">
            <AlertTriangle className="w-6 h-6 text-amber-600" />
          </div>
        </div>
      </section>

      {/* 3. SECTION « MODULES DE LA GESTION DE PAIE » */}
      <section
        id="accueil-modules"
        aria-label="Modules de la gestion de paie"
        className="border border-[#E8E8E6] bg-white rounded-2xl p-5 shadow-xs space-y-3"
      >
        <h3 className="text-xs font-bold uppercase tracking-wider text-[#6B6B6B]">
          MODULES DE LA GESTION DE PAIE
        </h3>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
          {/* Module 1 : Traitement de paie (Actif) */}
          <button
            type="button"
            onClick={onNavigateToProcessing}
            className="p-4 rounded-xl border border-[#1e3a8a]/35 bg-blue-50/50 hover:bg-blue-50/90 text-left transition flex flex-col justify-between cursor-pointer group shadow-2xs"
          >
            <div>
              <p className="text-xs font-bold text-[#1e3a8a] group-hover:underline">
                Traitement de paie
              </p>
              <p className="text-[11px] text-[#555] mt-1.5 leading-snug">
                Calcul des 6 bulletins & validation de la masse
              </p>
            </div>
            <span className="text-[11px] font-bold text-[#1e3a8a] mt-3 flex items-center gap-1">
              Ouvrir ›
            </span>
          </button>

          {/* Module 2 : Primes & Avantages (Actif) */}
          <button
            type="button"
            onClick={() => onNavigateToTab?.('primes')}
            className="p-4 rounded-xl border border-[#E8E8E6] bg-white hover:border-[#1e3a8a]/40 hover:bg-blue-50/30 text-left transition flex flex-col justify-between cursor-pointer group shadow-2xs"
          >
            <div>
              <p className="text-xs font-bold text-[#1F1F1E] group-hover:text-[#1e3a8a]">
                Primes & Avantages
              </p>
              <p className="text-[11px] text-[#6B6B6B] mt-1.5 leading-snug">
                Transport légal, rendement, panier
              </p>
            </div>
            <span className="text-[11px] font-bold text-[#1e3a8a] mt-3 flex items-center gap-1">
              Ouvrir ›
            </span>
          </button>

          {/* Module 3 : Prêts & Retenues (Actif) */}
          <button
            type="button"
            onClick={() => onNavigateToTab?.('prets')}
            className="p-4 rounded-xl border border-[#E8E8E6] bg-white hover:border-[#1e3a8a]/40 hover:bg-blue-50/30 text-left transition flex flex-col justify-between cursor-pointer group shadow-2xs"
          >
            <div>
              <p className="text-xs font-bold text-[#1F1F1E] group-hover:text-[#1e3a8a]">
                Prêts & Retenues
              </p>
              <p className="text-[11px] text-[#6B6B6B] mt-1.5 leading-snug">
                Avances et quotité cessible
              </p>
            </div>
            <span className="text-[11px] font-bold text-[#1e3a8a] mt-3 flex items-center gap-1">
              Ouvrir ›
            </span>
          </button>

          {/* Module 4 : Congés & Repos (Actif) */}
          <button
            type="button"
            onClick={() => onNavigateToTab?.('conges')}
            className="p-4 rounded-xl border border-[#E8E8E6] bg-white hover:border-[#1e3a8a]/40 hover:bg-blue-50/30 text-left transition flex flex-col justify-between cursor-pointer group shadow-2xs"
          >
            <div>
              <p className="text-xs font-bold text-[#1F1F1E] group-hover:text-[#1e3a8a]">
                Congés & Repos
              </p>
              <p className="text-[11px] text-[#6B6B6B] mt-1.5 leading-snug">
                2,2 jours/mois et ancienneté
              </p>
            </div>
            <span className="text-[11px] font-bold text-[#1e3a8a] mt-3 flex items-center gap-1">
              Ouvrir ›
            </span>
          </button>

          {/* Module 5 : Heures Supplémentaires (Actif) */}
          <button
            type="button"
            onClick={() => onNavigateToTab?.('heures_sup')}
            className="p-4 rounded-xl border border-[#E8E8E6] bg-white hover:border-[#1e3a8a]/40 hover:bg-blue-50/30 text-left transition flex flex-col justify-between cursor-pointer group shadow-2xs"
          >
            <div>
              <p className="text-xs font-bold text-[#1F1F1E] group-hover:text-[#1e3a8a]">
                Heures Supplémentaires
              </p>
              <p className="text-[11px] text-[#6B6B6B] mt-1.5 leading-snug">
                Majorations 15 %, 50 %, 75 %, 100 %
              </p>
            </div>
            <span className="text-[11px] font-bold text-[#1e3a8a] mt-3 flex items-center gap-1">
              Ouvrir ›
            </span>
          </button>
        </div>
      </section>

      {/* 4. TABLEAU « ÉTAT DE L'EFFECTIF POUR JUIN 2026 (6 SALARIÉS) » */}
      <section
        id="accueil-effectif-table"
        aria-label="État de l'effectif"
        className="border border-[#E8E8E6] bg-white rounded-2xl overflow-hidden shadow-xs"
      >
        <div className="p-4 border-b border-[#E8E8E6] bg-[#F9FAFB] flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
          <h3 className="text-xs font-bold text-[#1F1F1E]">
            État de l'effectif pour {currentPeriod.name || 'JUIN 2026'} (6 salariés)
          </h3>
          <button
            type="button"
            onClick={onNavigateToProcessing}
            className="text-xs font-bold text-[#1e3a8a] hover:underline cursor-pointer flex items-center gap-1"
          >
            <span>Voir tous les détails dans Traitement de paie →</span>
          </button>
        </div>

        <div className="overflow-x-auto">
          <table className="w-full text-left text-xs">
            <thead>
              <tr className="border-b border-[#E8E8E6] text-[#6B6B6B] font-semibold bg-[#F9FAFB]">
                <th className="py-3 px-4">SALARIÉ</th>
                <th className="py-3 px-4">DÉPARTEMENT</th>
                <th className="py-3 px-4 text-center">JOURS</th>
                <th className="py-3 px-4 text-right">BASE CONTRACTUELLE</th>
                <th className="py-3 px-4 text-right">NET ESTIMÉ</th>
                <th className="py-3 px-4 text-center">STATUT</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-[#E8E8E6]">
              {EMPLOYEES_DISPLAY_DATA.map((emp) => {
                return (
                  <tr
                    key={emp.id}
                    className="even:bg-[#FAFAFA]/70 hover:bg-blue-50/30 transition-colors"
                  >
                    {/* SALARIÉ */}
                    <td className="py-3.5 px-4 font-bold text-[#1F1F1E]">
                      {emp.name}
                    </td>

                    {/* DÉPARTEMENT */}
                    <td className="py-3.5 px-4 text-[#6B6B6B]">
                      {emp.dept}
                    </td>

                    {/* JOURS */}
                    <td className="py-3.5 px-4 text-center font-mono text-[#1F1F1E]">
                      {emp.days}
                    </td>

                    {/* BASE CONTRACTUELLE */}
                    <td className="py-3.5 px-4 text-right font-montserrat font-semibold text-[#1F1F1E]">
                      {emp.base}
                    </td>

                    {/* NET ESTIMÉ */}
                    <td className="py-3.5 px-4 text-right font-montserrat">
                      {emp.net === '(à calculer)' ? (
                        <span className="text-[#6B6B6B] italic font-medium">
                          (à calculer)
                        </span>
                      ) : (
                        <span className="font-bold text-[#1e3a8a]">
                          {emp.net}
                        </span>
                      )}
                    </td>

                    {/* STATUT */}
                    <td className="py-3.5 px-4 text-center">
                      {emp.status === 'conforme' && (
                        <span className="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                          <span className="w-2 h-2 rounded-full bg-emerald-500 shrink-0" />
                          <span>Conforme</span>
                        </span>
                      )}
                      {emp.status === 'anomalie' && (
                        <span className="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200">
                          <span className="w-2 h-2 rounded-full bg-amber-500 shrink-0" />
                          <span>Anomalie</span>
                        </span>
                      )}
                      {emp.status === 'attente' && (
                        <span className="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-0.5 rounded-full bg-neutral-100 text-neutral-600 border border-neutral-200">
                          <span className="w-2 h-2 rounded-full bg-neutral-400 shrink-0" />
                          <span>En attente</span>
                        </span>
                      )}
                    </td>
                  </tr>
                );
              })}
            </tbody>
          </table>
        </div>
      </section>
    </motion.div>
  );
};
