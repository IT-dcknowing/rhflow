import React from 'react';
import { motion } from 'motion/react';
import {
  Home,
  Calculator,
  Gift,
  HandCoins,
  CalendarDays,
  Clock,
  ArrowUpDown,
} from 'lucide-react';

export type SidebarTabId =
  | 'accueil'
  | 'traitement'
  | 'primes'
  | 'prets'
  | 'conges'
  | 'heures_sup';

export interface NavigationSidebarProps {
  activeTab: SidebarTabId;
  onSelectTab: (tab: SidebarTabId) => void;
  useRecommendedOrder?: boolean;
  onToggleOrder?: () => void;
  showIcons?: boolean;
  userName?: string;
  userRole?: string;
  currentPeriodName?: string;
}

interface SidebarItemConfig {
  id: SidebarTabId;
  label: string;
  icon: React.ComponentType<{ className?: string }>;
}

export const CAPTURE_ORDER_ITEMS: SidebarItemConfig[] = [
  { id: 'accueil', label: 'Accueil Paie', icon: Home },
  { id: 'traitement', label: 'Traitement de paie', icon: Calculator },
  { id: 'primes', label: 'Primes & Avantages', icon: Gift },
  { id: 'prets', label: 'Prêts & Retenues', icon: HandCoins },
  { id: 'conges', label: 'Congés & Repos', icon: CalendarDays },
  { id: 'heures_sup', label: 'Heures Supplémentaires', icon: Clock },
];

export const RECOMMENDED_UX_ORDER_ITEMS: SidebarItemConfig[] = [
  { id: 'accueil', label: 'Accueil Paie', icon: Home },
  { id: 'primes', label: 'Primes & Avantages', icon: Gift },
  { id: 'prets', label: 'Prêts & Retenues', icon: HandCoins },
  { id: 'conges', label: 'Congés & Repos', icon: CalendarDays },
  { id: 'heures_sup', label: 'Heures Supplémentaires', icon: Clock },
  { id: 'traitement', label: 'Traitement de paie', icon: Calculator },
];

export const NavigationSidebar: React.FC<NavigationSidebarProps> = ({
  activeTab,
  onSelectTab,
  useRecommendedOrder = false,
  onToggleOrder,
  showIcons = false,
  userName = 'Fatou Koné',
  userRole = 'Responsable Paie CI',
  currentPeriodName = 'Juin 2026',
}) => {
  const items = useRecommendedOrder ? RECOMMENDED_UX_ORDER_ITEMS : CAPTURE_ORDER_ITEMS;

  // Calcul des initiales pour l'avatar
  const initials = userName
    .split(' ')
    .map((n) => n[0])
    .filter(Boolean)
    .slice(0, 2)
    .join('')
    .toUpperCase() || 'RH';

  return (
    <aside
      id="main-navigation-sidebar"
      className="w-[260px] shrink-0 self-start select-none"
      aria-label="Navigation principale"
    >
      {/* Conteneur Navy arrondi avec largeur adaptée pour laisser respirer les libellés agrandis */}
      <nav
        className="bg-[#1c357d] rounded-[28px] p-4 shadow-xl shadow-blue-950/20 flex flex-col relative overflow-hidden"
      >
        {/* 1. BLOC EN-TÊTE UTILISATEUR (Nom + Rôle + Avatar avec initiales) */}
        <div
          id="sidebar-user-header"
          className="flex items-center gap-3 px-3 py-2.5 mb-3 rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xs"
        >
          {/* Avatar avec initiales et voyant en ligne */}
          <div className="relative shrink-0 w-9 h-9 rounded-full bg-white text-[#1c357d] font-black text-xs flex items-center justify-center shadow-xs">
            <span>{initials}</span>
            <span
              className="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-400 border-2 border-[#1c357d] rounded-full"
              title="Connecté"
            />
          </div>
          {/* Identité utilisateur (lisibilité améliorée ≥ 12px) */}
          <div className="flex flex-col min-w-0">
            <span className="text-[13px] font-bold text-white truncate leading-tight">
              {userName}
            </span>
            <span className="text-[12px] font-medium text-white/80 truncate leading-tight">
              {userRole}
            </span>
          </div>
        </div>

        {/* 2. LISTE DES 6 ITEMS DE NAVIGATION (15-16 px, font-weight 500 inactif / 600 actif) */}
        <div className="flex flex-col gap-2">
          {items.map((item) => {
            const isActive = activeTab === item.id;
            const Icon = item.icon;

            return (
              <button
                key={item.id}
                id={`nav-item-${item.id}`}
                type="button"
                onClick={() => onSelectTab(item.id)}
                className={`relative w-full py-3 px-3.5 rounded-2xl text-[15px] text-center transition-all duration-150 cursor-pointer flex items-center justify-center ${
                  isActive
                    ? 'text-[#13265C] font-semibold shadow-md shadow-blue-950/25'
                    : 'text-white/85 font-medium hover:text-white hover:bg-white/10'
                }`}
              >
                {/* Indicateur actif blanc avec barre verticale contrastée à gauche */}
                {isActive && (
                  <motion.div
                    layoutId="sidebarActivePill"
                    className="absolute inset-0 bg-white rounded-2xl shadow-md ring-1 ring-black/5 z-0 overflow-hidden flex items-center"
                    transition={{
                      type: 'spring',
                      stiffness: 400,
                      damping: 32,
                      duration: 0.2,
                    }}
                  >
                    {/* Barre verticale colorée accentuée à gauche */}
                    <span className="w-1.5 h-6 bg-[#1c357d] rounded-r-full absolute left-0" />
                  </motion.div>
                )}

                {/* Contenu textuel */}
                <span className="relative z-10 flex items-center justify-center gap-2 whitespace-nowrap">
                  {showIcons && (
                    <Icon className={`w-4 h-4 shrink-0 ${isActive ? 'text-[#1c357d]' : 'text-white/80'}`} />
                  )}
                  <span>{item.label}</span>
                </span>
              </button>
            );
          })}
        </div>

        {/* 3. SÉPARATEUR VISUEL NET */}
        <div className="my-3.5 border-t border-white/20" />

        {/* 4. RAPPEL DE LA PÉRIODE ACTIVE (CONSERVÉ) */}
        <div className="px-2 py-1 flex items-center justify-between text-[12px] text-white/80 font-medium">
          <span className="text-white/70">Période :</span>
          <span className="font-bold text-white bg-white/15 px-2.5 py-0.5 rounded-md border border-white/10 uppercase">
            {currentPeriodName}
          </span>
        </div>

        {/* 5. BASCULE ORDRE CAPTURE / ORDRE UX (CONSERVÉ SOUS LE SÉPARATEUR) */}
        {onToggleOrder && (
          <div className="pt-2.5 mt-2 border-t border-white/15 flex items-center justify-between px-2 text-[12px] text-white/70">
            <span className="truncate font-medium">
              {useRecommendedOrder ? 'Ordre UX recommandé' : 'Ordre capture'}
            </span>
            <button
              type="button"
              onClick={onToggleOrder}
              title="Basculer entre l'ordre de la capture et l'ordre UX recommandé"
              className="text-white hover:text-white flex items-center gap-1 cursor-pointer py-1 px-1.5 rounded-md hover:bg-white/10 transition font-semibold"
            >
              <span>Changer</span>
              <ArrowUpDown className="w-3 h-3" />
            </button>
          </div>
        )}
      </nav>
    </aside>
  );
};

