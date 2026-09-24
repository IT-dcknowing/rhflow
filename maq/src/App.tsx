import React, { useState, useMemo } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import { Employee, Period, ViewModelId } from './data/employeeTypes';
import { INITIAL_EMPLOYEES, INITIAL_PERIODS } from './data/employees';
import { computeEmployeePay } from './logic/payrollCalculator';
import { ModelSelector } from './components/ModelSelector';
import { Model1View } from './models/model1/Model1View';
import { Model2View } from './models/model2/Model2View';
import { Model3View } from './models/model3/Model3View';
import { NavigationSidebar, SidebarTabId } from './components/NavigationSidebar';
import { AccueilPaieView } from './components/AccueilPaieView';
import { PrimesAvantagesView } from './components/PrimesAvantagesView';
import { PretsRetenuesView } from './components/PretsRetenuesView';
import { CongesReposView } from './components/CongesReposView';
import { HeuresSupplementairesView } from './components/HeuresSupplementairesView';
import { ValidationAuditModal } from './components/ValidationAuditModal';
import { RolloverConfirmModal } from './components/RolloverConfirmModal';
import { RolloverSuccessModal } from './components/RolloverSuccessModal';
import {
  executePayrollRollover,
  getPreviousPeriodInfo,
  getNextPeriodInfo,
  getPeriodStatusBadge,
  RolloverSummary,
} from './logic/periodManager';
import {
  Building2,
  Calendar,
  ChevronDown,
  CheckCircle2,
  ArrowLeft,
  RefreshCw,
  Lock,
  Unlock,
} from 'lucide-react';

const TAB_LABELS: Record<SidebarTabId, string> = {
  accueil: 'Accueil Paie',
  traitement: 'Traitement de paie',
  primes: 'Primes & Avantages',
  prets: 'Prêts & Retenues',
  conges: 'Congés & Repos',
  heures_sup: 'Heures Supplémentaires',
};

export default function App() {
  // Active View Model state (Modèle 1 par défaut)
  const [activeModel, setActiveModel] = useState<ViewModelId>('model1');

  // Sidebar navigation state with localStorage persistence
  const [activeSidebarTab, setActiveSidebarTab] = useState<SidebarTabId>(() => {
    if (typeof window !== 'undefined') {
      const saved = localStorage.getItem('rhflow_active_sidebar_tab') as SidebarTabId;
      if (
        saved &&
        ['accueil', 'traitement', 'primes', 'prets', 'conges', 'heures_sup'].includes(saved)
      ) {
        return saved;
      }
    }
    return 'traitement'; // Ordre et affichage conforme à la capture par défaut
  });

  // Ordre des items : Capture (par défaut) vs Recommandé UX
  const [useRecommendedOrder, setUseRecommendedOrder] = useState<boolean>(() => {
    if (typeof window !== 'undefined') {
      return localStorage.getItem('rhflow_sidebar_order_ux') === 'true';
    }
    return false;
  });

  const handleSelectSidebarTab = (tab: SidebarTabId) => {
    setActiveSidebarTab(tab);
    if (typeof window !== 'undefined') {
      localStorage.setItem('rhflow_active_sidebar_tab', tab);
    }
  };

  const handleToggleOrder = () => {
    setUseRecommendedOrder((prev) => {
      const next = !prev;
      if (typeof window !== 'undefined') {
        localStorage.setItem('rhflow_sidebar_order_ux', String(next));
      }
      showToast(next ? 'Ordre UX recommandé activé' : 'Ordre de la capture activé');
      return next;
    });
  };

  // Periods state
  const [exercise, setExercise] = useState<string>('2026');
  const [periodsData, setPeriodsData] = useState<Record<string, Period[]>>(INITIAL_PERIODS);
  const [periodIndex, setPeriodIndex] = useState<number>(5); // JUIN 2026
  const [isPeriodMenuOpen, setIsPeriodMenuOpen] = useState<boolean>(false);

  // Rollover modal and lifecycle state
  const [isRolloverModalOpen, setIsRolloverModalOpen] = useState<boolean>(false);
  const [isRolloverSuccessModalOpen, setIsRolloverSuccessModalOpen] = useState<boolean>(false);
  const [rolloverSummary, setRolloverSummary] = useState<RolloverSummary | null>(null);

  // Employees data state per period
  const [employeesByPeriod, setEmployeesByPeriod] = useState<Record<string, Employee[]>>(() => ({
    '2026-5': INITIAL_EMPLOYEES, // Juin 2026
    '2026-4': INITIAL_EMPLOYEES.map((e) => ({
      ...e,
      days: 30,
      status: 'ok',
      alert: null,
    })), // Mai 2026
  }));

  const currentPeriodKey = `${exercise}-${periodIndex}`;
  const [employees, setEmployees] = useState<Employee[]>(INITIAL_EMPLOYEES);

  // Modals state
  const [isValidatingModalOpen, setIsValidatingModalOpen] = useState<boolean>(false);

  // Toast notification state
  const [toastMessage, setToastMessage] = useState<string | null>(null);

  const showToast = (msg: string) => {
    setToastMessage(msg);
    setTimeout(() => {
      setToastMessage(null);
    }, 3500);
  };

  const currentPeriod = periodsData[exercise]?.[periodIndex] || {
    name: 'JUIN 2026',
    status: 'en_cours',
  };
  const isReadOnly = currentPeriod.status === 'validée' || currentPeriod.status === 'payee';

  // Calcul de la période précédente
  const previousPeriodInfo = useMemo(() => {
    return getPreviousPeriodInfo(periodsData[exercise] || [], periodIndex, exercise);
  }, [periodsData, exercise, periodIndex]);
  const previousPeriod = previousPeriodInfo.period;
  const isPreviousClosed = previousPeriod ? (previousPeriod.status === 'payee' || previousPeriod.status === 'validée') : true;

  // Changement de période avec conservation des données du mois
  const handleSelectPeriod = (idx: number) => {
    setPeriodIndex(idx);
    setIsPeriodMenuOpen(false);
    const key = `${exercise}-${idx}`;
    if (employeesByPeriod[key]) {
      setEmployees(employeesByPeriod[key]);
    } else {
      setEmployees(INITIAL_EMPLOYEES);
    }
    const targetP = periodsData[exercise]?.[idx];
    if (targetP) {
      showToast(`Période active : ${targetP.name}`);
    }
  };

  // Compute pay for all employees using shared CI 2026 calculation engine
  const computedMap = useMemo(() => {
    const map = new Map<number, ReturnType<typeof computeEmployeePay>>();
    for (const emp of employees) {
      map.set(emp.id, computeEmployeePay(emp));
    }
    return map;
  }, [employees]);

  // Overall totals for validation modal
  const grandTotalNet = useMemo(() => {
    let sum = 0;
    for (const emp of employees) {
      const c = computedMap.get(emp.id);
      if (c) sum += c.net;
    }
    return sum;
  }, [employees, computedMap]);

  const totalAnomalies = employees.filter((e) => e.status === 'anomaly').length;
  const not30DaysCount = employees.filter((e) => e.days < 30).length;

  const handleUpdateEmployee = (updatedEmployee: Employee) => {
    setEmployees((prev) => {
      const next = prev.map((e) => (e.id === updatedEmployee.id ? updatedEmployee : e));
      setEmployeesByPeriod((dict) => ({ ...dict, [currentPeriodKey]: next }));
      return next;
    });
  };

  const handleUpdateEmployees = (updatedList: Employee[]) => {
    setEmployees(updatedList);
    setEmployeesByPeriod((dict) => ({ ...dict, [currentPeriodKey]: updatedList }));
  };

  const handleResetData = () => {
    setEmployees(INITIAL_EMPLOYEES);
    setEmployeesByPeriod((dict) => ({ ...dict, [currentPeriodKey]: INITIAL_EMPLOYEES }));
    showToast('Données réinitialisées au jeu initial (6 salariés).');
  };

  /**
   * MOTEUR DE REPORT INTELLIGENT
   * Déclenche la reprise du mois précédent avec gardes-fous
   */
  const handleConfirmRollover = (forceAdmin?: boolean) => {
    if (!previousPeriod) {
      showToast('Aucune période précédente disponible pour le report.');
      return;
    }

    const prevKey = `${exercise}-${periodIndex - 1}`;
    const sourceEmployees = employeesByPeriod[prevKey] || INITIAL_EMPLOYEES;

    const result = executePayrollRollover(
      sourceEmployees,
      previousPeriod,
      currentPeriod,
      forceAdmin ?? false
    );

    if (!result.success) {
      showToast(`⚠️ ${result.error || 'Erreur lors du report'}`);
      return;
    }

    // Mise à jour des salariés
    setEmployees(result.newEmployees);
    setEmployeesByPeriod((prev) => ({
      ...prev,
      [currentPeriodKey]: result.newEmployees,
    }));

    // Mise à jour des cycles de vie des périodes
    setPeriodsData((prev) => {
      const updated = { ...prev };
      const list = [...(updated[exercise] || [])];

      // Règle 1 : Un seul mois « En cours » à la fois
      // Règle 2 : Le mois précédent passe en « Payée »
      const nextList = list.map((p, i) => {
        if (i === periodIndex) {
          return {
            ...p,
            status: 'en_cours' as const,
            dateOuverture: new Date().toLocaleDateString('fr-FR'),
          };
        }
        if (i === periodIndex - 1) {
          return {
            ...p,
            status: 'payee' as const,
            dateCloture: new Date().toLocaleDateString('fr-FR'),
          };
        }
        if (p.status === 'en_cours') {
          return { ...p, status: 'payee' as const };
        }
        return p;
      });

      updated[exercise] = nextList;
      return updated;
    });

    setRolloverSummary(result.summary);
    setIsRolloverModalOpen(false);
    setIsRolloverSuccessModalOpen(true);
    showToast(`📋 ${result.summary.carriedOverCount} salariés reportés avec succès pour ${currentPeriod.name} !`);
  };

  /**
   * CLÔTURE ET VALIDATION DU MOIS COURANT
   * Passe le mois en « Payée » et verrouille
   */
  const handleFinalValidationConfirm = () => {
    setPeriodsData((prev) => {
      const updated = { ...prev };
      const list = [...(updated[exercise] || [])];
      list[periodIndex] = {
        ...list[periodIndex],
        status: 'payee',
        dateCloture: new Date().toLocaleDateString('fr-FR'),
      };
      updated[exercise] = list;
      return updated;
    });
    setIsValidatingModalOpen(false);
    showToast(`Succès ! La période ${currentPeriod.name} est clôturée, payée et verrouillée.`);
  };

  /**
   * DÉVERROUILLAGE ADMINISTRATEUR D'UNE PÉRIODE PAYÉE
   */
  const handleUnlockPeriod = () => {
    setPeriodsData((prev) => {
      const updated = { ...prev };
      const list = [...(updated[exercise] || [])];
      list[periodIndex] = {
        ...list[periodIndex],
        status: 'en_cours',
      };
      updated[exercise] = list;
      return updated;
    });
    showToast(`La période ${currentPeriod.name} a été déverrouillée par l'administrateur (mode édition).`);
  };

  return (
    <div className="min-h-screen flex flex-col bg-white text-[#1F1F1E] font-sans antialiased selection:bg-[#E8E8E6] selection:text-[#253e87]">
      {/* 1. SÉLECTEUR DE MODÈLES EN HAUT DE PAGE */}
      <ModelSelector
        activeModel={activeModel}
        onSelectModel={(model) => {
          setActiveModel(model);
          showToast(
            `Affichage basculé sur le ${
              model === 'model1'
                ? 'Modèle 1 (Vue Hybride)'
                : model === 'model2'
                ? 'Modèle 2 (Bulletin Vivant)'
                : 'Modèle 3 (Le Tunnel)'
            }`
          );
        }}
        onAddNewModel={() => {
          showToast('Pour ajouter un Modèle supplémentaire, créez le composant dans /src/models/ et ajoutez son onglet.');
        }}
      />

      {/* 2. EN-TÊTE PRINCIPAL (Uniformisé avec charte RH Flow) */}
      <header className="border-b border-[#E8E8E6] px-6 py-3.5 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white sticky top-0 z-30 shadow-xs">
        <div className="flex items-center gap-3">
          <div className="bg-[#1c357d] text-white px-3.5 py-2 font-bold tracking-widest uppercase rounded-lg shadow-xs flex items-center gap-2 text-[14px]">
            <Building2 className="w-4 h-4" />
            RH FLOW
          </div>
          <div>
            <h1 className="font-bold text-[22px] md:text-2xl tracking-tight text-[#1F1F1E] leading-tight">
              {activeModel === 'model1' && 'La Vue Hybride — Masse & Chirurgie'}
              {activeModel === 'model2' && 'Modèle 2 — Le Bulletin Vivant'}
              {activeModel === 'model3' && 'Modèle 3 — Le Tunnel en 5 Étapes'}
            </h1>
            <p className="text-[13px] md:text-sm font-normal text-[#6B6B6B] mt-0.5">
              Zéro Jargon · Édition Multi-lignes · Rétro-calcul 🎯
            </p>
          </div>
        </div>

        <div className="flex items-center gap-3 flex-wrap">
          {/* Reprendre le mois précédent */}
          {previousPeriod && (
            <button
              type="button"
              id="btn-reprendre-paie-header"
              onClick={() => setIsRolloverModalOpen(true)}
              className={`inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border text-[13px] md:text-[14px] font-semibold transition cursor-pointer shadow-2xs ${
                isPreviousClosed
                  ? 'border-[#E8E8E6] bg-[#F4F4F5] hover:bg-[#F0F0EE] text-[#1e3a8a]'
                  : 'border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-900'
              }`}
              title={
                isPreviousClosed
                  ? `Report intelligent des éléments stables de ${previousPeriod.name}`
                  : `Le mois de ${previousPeriod.name} doit d'abord être clôturé (statut Payée).`
              }
            >
              <RefreshCw className="w-4 h-4 text-[#1e3a8a]" />
              <span className="hidden sm:inline">Reprendre la paie de {previousPeriod.name}</span>
              <span className="sm:hidden">Reprendre</span>
              {!isPreviousClosed && (
                <span className="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0" title="Clôture requise" />
              )}
            </button>
          )}

          {/* Sélecteur d'exercice & période */}
          <div className="relative">
            <button
              type="button"
              id="btn-period-selector-dropdown"
              onClick={() => setIsPeriodMenuOpen(!isPeriodMenuOpen)}
              className="border border-[#E8E8E6] rounded-xl px-3.5 py-2 bg-[#FAFAFA] text-[#1F1F1E] text-[13px] md:text-[14px] font-semibold flex items-center gap-2 hover:bg-[#F0F0EE] transition cursor-pointer"
            >
              <span id="breadcrumb-exercise">EXERCICE {exercise}</span>
              <span className="text-[#6B6B6B]">▸</span>
              <span id="breadcrumb-month" className="text-[#1c357d]">
                {currentPeriod.name}
              </span>
              <ChevronDown className="w-4 h-4 text-[#6B6B6B]" />
            </button>

            {isPeriodMenuOpen && (
              <div className="absolute right-0 mt-2 w-72 bg-white border border-[#E8E8E6] rounded-xl shadow-xl z-50 p-2 space-y-1">
                <div className="px-3 py-2 text-[12px] uppercase font-bold text-[#6B6B6B] border-b border-[#E8E8E6] flex justify-between items-center">
                  <span>Périodes d'exercice</span>
                  <select
                    value={exercise}
                    onChange={(e) => {
                      setExercise(e.target.value);
                      setPeriodIndex(0);
                    }}
                    className="text-[12px] border border-[#E8E8E6] rounded p-1 bg-[#FAFAFA]"
                  >
                    <option value="2026">2026</option>
                    <option value="2025">2025</option>
                  </select>
                </div>
                <div className="space-y-1 pt-1 max-h-64 overflow-y-auto">
                  {periodsData[exercise]?.map((p, idx) => {
                    const badgeInfo = getPeriodStatusBadge(p.status);
                    const isSelected = idx === periodIndex;

                    return (
                      <button
                        key={p.name}
                        type="button"
                        onClick={() => handleSelectPeriod(idx)}
                        className={`w-full text-left px-3 py-2.5 rounded-lg text-[13px] font-semibold flex justify-between items-center hover:bg-[#F0F0EE] transition cursor-pointer ${
                          isSelected ? 'bg-[#F0F0EE] text-[#1c357d]' : 'text-[#1F1F1E]'
                        }`}
                      >
                        <div className="flex items-center gap-2">
                          {(p.status === 'payee' || p.status === 'validée') && (
                            <Lock className="w-3.5 h-3.5 text-emerald-600" />
                          )}
                          <span>{p.name}</span>
                        </div>
                        <span className={`text-[12px] px-2.5 py-0.5 rounded font-semibold border ${badgeInfo.colorClass}`}>
                          {badgeInfo.icon} {badgeInfo.label}
                        </span>
                      </button>
                    );
                  })}
                </div>
              </div>
            )}
          </div>

          {/* Badge de statut du cycle de vie de la période active */}
          {(() => {
            const currentStatusBadge = getPeriodStatusBadge(currentPeriod.status);
            return (
              <div
                id="header-status-badge"
                className={`text-[12px] md:text-[13px] font-semibold px-3.5 py-1.5 rounded-full flex items-center gap-1.5 border shadow-2xs ${currentStatusBadge.colorClass}`}
              >
                <span>{currentStatusBadge.icon}</span>
                <span className="uppercase">{currentStatusBadge.label}</span>
              </div>
            );
          })()}

          {/* Bouton Calculs à jour */}
          <div className="hidden lg:flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-800 text-[12px] font-semibold">
            <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600" />
            <span>Calculs à jour</span>
          </div>
        </div>
      </header>

      {/* 3. FIL D'ARIANE (Gestion de la paie > Item) + FLÈCHE RETOUR (Exactement comme sur la capture) */}
      <div className="px-6 pt-4 pb-2 max-w-[1700px] w-full mx-auto">
        <div className="flex items-center gap-2 text-[13px] text-[#6B6B6B]">
          <span>Gestion de la paie</span>
          <span>&gt;</span>
          <span className="font-semibold text-[#1F1F1E]">
            {TAB_LABELS[activeSidebarTab]}
          </span>
        </div>
        <div className="pt-2">
          <button
            type="button"
            onClick={() => handleSelectSidebarTab(activeSidebarTab === 'accueil' ? 'traitement' : 'accueil')}
            className="inline-flex items-center text-[#1c357d] hover:text-[#162a63] transition p-1 -ml-1 rounded hover:bg-neutral-100 cursor-pointer"
            title="Retour à l'accueil"
          >
            <ArrowLeft className="w-5 h-5 stroke-[2.5]" />
          </button>
        </div>
      </div>

      {/* 4. DISPOSITION PRINCIPALE : SIDEBAR NAVY À GAUCHE + CONTENU À DROITE (Sur tous les modèles 1, 2, 3, 4) */}
      <div className="flex-1 flex flex-col md:flex-row items-start gap-6 px-6 pb-6 w-full max-w-[1700px] mx-auto">
        {/* Sidebar Navy commune */}
        <NavigationSidebar
          activeTab={activeSidebarTab}
          onSelectTab={handleSelectSidebarTab}
          useRecommendedOrder={useRecommendedOrder}
          onToggleOrder={handleToggleOrder}
          currentPeriodName={currentPeriod.name}
          userName="Fatou Koné"
          userRole="Responsable Paie CI"
        />

        {/* Zone de contenu principale avec transition fluide fade-in */}
        <main className="flex-1 min-w-0 w-full">
          <AnimatePresence mode="wait">
            <motion.div
              key={activeSidebarTab + (activeSidebarTab === 'traitement' ? activeModel : '')}
              initial={{ opacity: 0, y: 4 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: -4 }}
              transition={{ duration: 0.22, ease: 'easeInOut' }}
              className="w-full"
            >
              {/* Accueil Paie */}
              {activeSidebarTab === 'accueil' && (
                <AccueilPaieView
                  employees={employees}
                  computedMap={computedMap}
                  currentPeriod={currentPeriod}
                  previousPeriod={previousPeriod}
                  onNavigateToProcessing={() => {
                    handleSelectSidebarTab('traitement');
                  }}
                  onNavigateToTab={(tab) => handleSelectSidebarTab(tab as SidebarTabId)}
                  onOpenRolloverModal={() => setIsRolloverModalOpen(true)}
                  onOpenValidationModal={() => setIsValidatingModalOpen(true)}
                  onUnlockPeriod={handleUnlockPeriod}
                />
              )}

              {/* Traitement de paie (Bascule instantanée sur le modèle actif 1, 2 ou 3) */}
              {activeSidebarTab === 'traitement' && (
                <>
                  {activeModel === 'model1' && (
                    <Model1View
                      employees={employees}
                      computedMap={computedMap}
                      currentPeriod={currentPeriod}
                      isReadOnly={isReadOnly}
                      onUpdateEmployee={handleUpdateEmployee}
                      onUpdateEmployees={handleUpdateEmployees}
                      onResetData={handleResetData}
                      onOpenValidationModal={() => setIsValidatingModalOpen(true)}
                      onUnlockPeriod={handleUnlockPeriod}
                      onShowToast={showToast}
                    />
                  )}

                  {activeModel === 'model2' && (
                    <Model2View
                      employees={employees}
                      computedMap={computedMap}
                      currentPeriod={currentPeriod}
                      isReadOnly={isReadOnly}
                      onUpdateEmployee={handleUpdateEmployee}
                      onUpdateEmployees={handleUpdateEmployees}
                      onOpenValidationModal={() => setIsValidatingModalOpen(true)}
                      onUnlockPeriod={handleUnlockPeriod}
                      onShowToast={showToast}
                    />
                  )}

                  {activeModel === 'model3' && (
                    <Model3View
                      employees={employees}
                      computedMap={computedMap}
                      currentPeriod={currentPeriod}
                      isReadOnly={isReadOnly}
                      onUpdateEmployee={handleUpdateEmployee}
                      onUpdateEmployees={handleUpdateEmployees}
                      onOpenValidationModal={() => setIsValidatingModalOpen(true)}
                      onUnlockPeriod={handleUnlockPeriod}
                      onShowToast={showToast}
                    />
                  )}
                </>
              )}

              {/* Page 1 : Primes & Avantages */}
              {activeSidebarTab === 'primes' && (
                <PrimesAvantagesView
                  currentPeriod={currentPeriod}
                  onNavigateHome={() => handleSelectSidebarTab('accueil')}
                  onNavigateProcessing={() => {
                    handleSelectSidebarTab('traitement');
                  }}
                />
              )}

              {/* Page 2 : Prêts & Retenues */}
              {activeSidebarTab === 'prets' && (
                <PretsRetenuesView
                  currentPeriod={currentPeriod}
                  onNavigateHome={() => handleSelectSidebarTab('accueil')}
                  onNavigateProcessing={() => {
                    handleSelectSidebarTab('traitement');
                  }}
                />
              )}

              {/* Page 3 : Congés & Repos */}
              {activeSidebarTab === 'conges' && (
                <CongesReposView
                  currentPeriod={currentPeriod}
                  onNavigateHome={() => handleSelectSidebarTab('accueil')}
                  onNavigateProcessing={() => {
                    handleSelectSidebarTab('traitement');
                  }}
                />
              )}

              {/* Page 4 : Heures Supplémentaires */}
              {activeSidebarTab === 'heures_sup' && (
                <HeuresSupplementairesView
                  currentPeriod={currentPeriod}
                  onNavigateHome={() => handleSelectSidebarTab('accueil')}
                  onNavigateProcessing={() => {
                    handleSelectSidebarTab('traitement');
                  }}
                />
              )}
            </motion.div>
          </AnimatePresence>
        </main>
      </div>

      {/* 4. MODAL CLÔTURE & AUDIT (Partagé) */}
      <ValidationAuditModal
        isOpen={isValidatingModalOpen}
        period={currentPeriod}
        totalNet={grandTotalNet}
        anomaliesCount={totalAnomalies}
        not30DaysCount={not30DaysCount}
        totalEmployeesCount={employees.length}
        onClose={() => setIsValidatingModalOpen(false)}
        onConfirm={handleFinalValidationConfirm}
      />

      {/* MODALE REPRENDRE LA PAIE (CONFIRMATION & GARDE-FOUS) */}
      <RolloverConfirmModal
        isOpen={isRolloverModalOpen}
        previousPeriod={previousPeriod}
        targetPeriod={currentPeriod}
        onClose={() => setIsRolloverModalOpen(false)}
        onConfirmRollover={handleConfirmRollover}
      />

      {/* MODALE DE CONFIRMATION DE TRANSITION (SUCCÈS) */}
      <RolloverSuccessModal
        isOpen={isRolloverSuccessModalOpen}
        summary={rolloverSummary}
        onClose={() => setIsRolloverSuccessModalOpen(false)}
        onNavigateToProcessing={() => {
          setIsRolloverSuccessModalOpen(false);
          handleSelectSidebarTab('traitement');
        }}
      />

      {/* 5. TOAST NOTIFICATION */}
      {toastMessage && (
        <div
          id="toast-notification"
          className="fixed bottom-6 right-6 bg-[#253e87] text-white px-5 py-3 rounded-xl shadow-2xl z-50 text-xs font-bold flex items-center gap-2 border border-white/20 animate-in fade-in slide-in-from-bottom duration-200"
        >
          <CheckCircle2 className="w-4 h-4 text-emerald-400" />
          <span>{toastMessage}</span>
        </div>
      )}
    </div>
  );
}
